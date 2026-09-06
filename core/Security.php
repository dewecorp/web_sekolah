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
    // Rate limit sederhana: max 5 percobaan / 10 menit per IP
    public static function loginAllowed(PDO $db, string $ip): bool {
        $s = $db->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip=? AND attempted_at > NOW() - INTERVAL 10 MINUTE");
        $s->execute([$ip]);
        return ((int)$s->fetchColumn()) < 5;
    }
    public static function logAttempt(PDO $db, string $ip, string $user): void {
        $s = $db->prepare("INSERT INTO login_attempts(ip,username) VALUES(?,?)");
        $s->execute([$ip, $user]);
    }
    public static function clearAttempts(PDO $db, string $ip): void {
        $s = $db->prepare("DELETE FROM login_attempts WHERE ip=?");
        $s->execute([$ip]);
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
}
