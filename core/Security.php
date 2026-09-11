<?php
declare(strict_types=1);
final class Security {
    public static function csrfToken(): string {
        if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }
    public static function csrfField(): string {
        return '<input type="hidden" name="csrf" value="' . htmlspecialchars(self::csrfToken(), ENT_QUOTES) . '">';
    }
    public static function verifyCsrf(?string $t): bool {
        return is_string($t) && hash_equals($_SESSION['csrf'] ?? '', $t);
    }
    /**
     * Anti brute-force dengan lockout eksponensial.
     * Maksimal 5 percobaan gagal dalam jendela lockout.
     * Setiap kelipatan 5 gagal, durasi lockout naik: 1, 5, 15, 60 menit (maks).
     * Pembatasan diterapkan per IP dan per username secara terpisah.
     */
    public static function loginAllowed(PDO $db, string $ip, string $user): bool {
        self::cleanupAttempts($db);
        $maxFailures = 5;
        $steps = [1, 5, 15, 60]; // menit

        foreach ([$ip, $user] as $key) {
            $recentFailures = self::countRecentFailures($db, $key, $steps);
            $level = (int) floor($recentFailures / $maxFailures);
            if ($level === 0) continue;
            $lockMinutes = $steps[min($level - 1, count($steps) - 1)];
            if (self::hasFailureWithin($db, $key, $lockMinutes)) {
                return false;
            }
        }
        return true;
    }

    private static function countRecentFailures(PDO $db, string $key, array $steps): int {
        $maxWindow = max($steps);
        $s = $db->prepare("SELECT COUNT(*) FROM login_attempts WHERE (ip=? OR username=?) AND attempted_at > NOW() - INTERVAL ? MINUTE");
        $s->execute([$key, $key, $maxWindow]);
        return (int) $s->fetchColumn();
    }

    private static function hasFailureWithin(PDO $db, string $key, int $minutes): bool {
        $s = $db->prepare("SELECT 1 FROM login_attempts WHERE (ip=? OR username=?) AND attempted_at > NOW() - INTERVAL ? MINUTE LIMIT 1");
        $s->execute([$key, $key, $minutes]);
        return (bool) $s->fetch();
    }

    private static function cleanupAttempts(PDO $db): void {
        try { $db->exec("DELETE FROM login_attempts WHERE attempted_at < NOW() - INTERVAL 24 HOUR"); } catch (Throwable) {}
    }

    public static function remainingLockoutSeconds(PDO $db, string $ip, string $user): int {
        $maxFailures = 5;
        $steps = [1, 5, 15, 60];
        $max = 0;
        foreach ([$ip, $user] as $key) {
            $recentFailures = self::countRecentFailures($db, $key, $steps);
            $level = (int) floor($recentFailures / $maxFailures);
            if ($level === 0) continue;
            $lockMinutes = $steps[min($level - 1, count($steps) - 1)];
            $s = $db->prepare("SELECT MAX(attempted_at) FROM login_attempts WHERE (ip=? OR username=?) AND attempted_at > NOW() - INTERVAL ? MINUTE");
            $s->execute([$key, $key, $lockMinutes]);
            $latest = $s->fetchColumn();
            if ($latest) {
                $elapsed = time() - strtotime((string)$latest);
                $remaining = ($lockMinutes * 60) - $elapsed;
                if ($remaining > $max) $max = $remaining;
            }
        }
        return $max;
    }

    public static function logAttempt(PDO $db, string $ip, string $user): void {
        $s = $db->prepare("INSERT INTO login_attempts(ip,username,attempted_at) VALUES(?,?,NOW())");
        $s->execute([$ip, $user]);
    }

    public static function clearAttempts(PDO $db, string $ip, ?string $user = null): void {
        if ($user === null) {
            $s = $db->prepare("DELETE FROM login_attempts WHERE ip=?");
            $s->execute([$ip]);
        } else {
            $s = $db->prepare("DELETE FROM login_attempts WHERE ip=? OR username=?");
            $s->execute([$ip, $user]);
        }
    }
    public static function slug(string $t): string {
        $t = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $t) ?: $t;
        $t = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $t));
        return trim($t, '-');
    }
    // Validasi upload image: MIME asli + ext + size + block executable
    public static function validImage(array $f, array $cfg): ?string {
        if (($f['error'] ?? 4) !== 0) return 'Upload gagal.';
        if ($f['size'] > $cfg['upload_max_mb'] * 1024 * 1024) return 'File terlalu besar.';
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $cfg['blocked_ext'], true)) return 'Tipe file dilarang.';
        $fi = new finfo(FILEINFO_MIME_TYPE);
        $mime = $fi->file($f['tmp_name']);
        if (!isset($cfg['allowed_image_mimes'][$mime])) return 'MIME tidak valid.';
        if ($cfg['allowed_image_mimes'][$mime] !== $ext && !($mime === 'image/jpeg' && in_array($ext, ['jpg','jpeg'], true))) return 'Ekstensi tidak cocok.';
        $img = @getimagesize($f['tmp_name']);
        if ($img === false) return 'Bukan gambar valid.';
        return null;
    }
    public static function safeName(string $orig): string {
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        return date('Ymd-His') . '-' . bin2hex(random_bytes(6)) . '.' . $ext;
    }
    public const COMMENT_MAX_LINKS = 2;
    public const COMMENT_MIN_SECONDS = 5;
    public const COMMENT_FLOOD_SECONDS = 30;
    public const COMMENT_MAX_PER_10MIN = 5;
    public static function commentRateAllowed(PDO $db, string $ip): array {
        try {
            $s = $db->prepare("SELECT COUNT(*) FROM post_comments WHERE ip=? AND created_at > NOW() - INTERVAL 10 MINUTE");
            $s->execute([$ip]);
            if ((int)$s->fetchColumn() >= self::COMMENT_MAX_PER_10MIN) return [false, 'Terlalu banyak komentar. Coba lagi dalam 10 menit.'];
            $s = $db->prepare("SELECT MAX(created_at) FROM post_comments WHERE ip=?");
            $s->execute([$ip]);
            $last = $s->fetchColumn();
            if ($last && (time() - strtotime((string)$last) < self::COMMENT_FLOOD_SECONDS)) return [false, 'Tunggu sebentar sebelum mengirim komentar lagi.'];
        } catch (Throwable) {}
        return [true, ''];
    }
    public static function isDuplicateComment(PDO $db, int $postId, string $email, string $comment): bool {
        try {
            $s = $db->prepare("SELECT 1 FROM post_comments WHERE post_id=? AND email=? AND comment=? AND created_at > NOW() - INTERVAL 10 MINUTE LIMIT 1");
            $s->execute([$postId, $email, $comment]);
            return (bool)$s->fetch();
        } catch (Throwable) { return false; }
    }
    public static function isCommentSpam(string $comment, string $name, string $email, string $website = ''): array {
        preg_match_all('~(https?://|www\.|\[url|<a\s+href)~i', $comment . ' ' . $website, $m);
        if (count($m[0]) > self::COMMENT_MAX_LINKS) return [true, 'Terlalu banyak tautan.'];
        $text = mb_strtolower($comment . ' ' . $name . ' ' . $website, 'UTF-8');
        $bad = ['viagra','cialis','casino','togel','slot gacor','maxwin','sbobet','poker online','judi online','porn','xxx','escort','pinjaman online','payday loan','obat kuat','backlink','jasa seo','seo murah','crypto giveaway','double your bitcoin','binary option','forex profit','guaranteed profit','weight loss','miracle cure'];
        foreach ($bad as $w) if ($w !== '' && str_contains($text, $w)) return [true, 'Terdeteksi kata spam.'];
        if (preg_match('~(https?://|www\.|<a|</a>)~i', $name)) return [true, 'Nama tidak valid.'];
        $plain = trim((string)preg_replace('~https?://\S+~', '', $comment));
        if (mb_strlen($plain) < 3) return [true, 'Komentar terlalu pendek.'];
        if (preg_match('/(.)\1{9,}/u', $comment)) return [true, 'Terdeteksi spam.'];
        if (preg_match('/[A-Z\W]*[A-Z]{15,}/', $comment)) return [true, 'Hindari huruf kapital berlebihan.'];
        return [false, ''];
    }
    // Validasi upload video: MP4/WebM/Ogg + size + block executable
    public static function validVideo(array $f, array $cfg): ?string {
        if (($f['error'] ?? 4) !== 0) return 'Upload gagal.';
        $maxMb = max(5, (int)($cfg['upload_video_max_mb'] ?? 100));
        if ($f['size'] > $maxMb * 1024 * 1024) return 'File terlalu besar (max ' . $maxMb . 'MB).';
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $cfg['blocked_ext'] ?? [], true)) return 'Tipe file dilarang.';
        if (!in_array($ext, ['mp4','webm','ogg','ogv','mov'], true)) return 'Format video harus MP4/WebM/OGG.';
        $fi = new finfo(FILEINFO_MIME_TYPE);
        $mime = (string)$fi->file($f['tmp_name']);
        if (!preg_match('~^(video/|application/octet-stream)~', $mime)) return 'MIME tidak valid (' . $mime . ').';
        return null;
    }
}
