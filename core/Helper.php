<?php
declare(strict_types=1);
final class Helper {
    public static function e(?string $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
    public static function setting(string $k, string $d = ''): string { return Database::setting($k, $d); }
    public static function url(string $p = ''): string { return rtrim(BASE_URL, '/') . '/' . ltrim($p, '/'); }
    // URL menu: dukung absolut (https://web lain), relatif (/profil), # dan mailto/tel
    public static function menuUrl(string $u): array {
        $u = trim($u);
        if ($u === '' || $u === '#') return ['#', '_self'];
        $low = strtolower($u);
        if (str_starts_with($low, 'http://') || str_starts_with($low, 'https://')) return [$u, '_blank'];
        if (str_starts_with($low, 'mailto:') || str_starts_with($low, 'tel:') || str_starts_with($u, '#')) return [$u, '_self'];
        return [self::url(ltrim($u, '/')), '_self'];
    }
    public static function asset(string $p): string {
        $rel = 'assets/' . ltrim($p, '/');
        $v = '';
        $full = ROOT . '/' . $rel;
        if (is_file($full)) $v = '?v=' . filemtime($full);
        return self::url($rel) . $v;
    }
    public static function upload(string $p): string { return self::url('assets/uploads/' . ltrim($p, '/')); }
    // Gambar dummy profesional (Unsplash). Stabil via images.unsplash.com photo ID.
    // Kunci agar tak random: seed = id/slug -> foto konsisten per konten.
    public static function dummy(string $seed = '', int $w = 1200, int $h = 700): string {
        $pool = [
            'photo-1523050854058-8df90110c9f1', // kampus wisuda
            'photo-1509062522246-3755977927d7', // kelas belajar
            'photo-1427504494785-3a9ca7044f45', // sekolah anak
            'photo-1577896851231-70ef18881754', // siswa kelas
            'photo-1580582932707-520aed937b7b', // ruang kelas kosong
            'photo-1524178232363-1fb2b075b655', // kuliah
            'photo-1503676260728-1c00da094a0b', // edukasi buku
            'photo-1541339907198-e08756dedf3f', // gedung kampus
            'photo-1571260899304-425eee4c7efc', // wisuda topi
            'photo-1594608661623-aa0bd3a69d98', // lab komputer
        ];
        $i = abs(crc32((string)$seed)) % count($pool);
        return 'https://images.unsplash.com/' . $pool[$i] . '?auto=format&fit=crop&w=' . $w . '&h=' . $h . '&q=70';
    }
    // URL gambar konten: upload asli bila ada, dummy bila kosong
    public static function cover(?string $file, string $seed = '', int $w = 1200, int $h = 700): string {
        $file = trim((string)$file);
        if ($file !== '' && !str_starts_with($file, 'http')) {
            if (is_file(ROOT . '/assets/uploads/' . ltrim($file, '/'))) return self::upload($file);
        } elseif ($file !== '') return $file;
        return self::dummy($seed ?: 'sekolah', $w, $h);
    }
    public static function excerpt(string $html, int $len = 140): string {
        $t = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
        return mb_strlen($t) > $len ? mb_substr($t, 0, $len) . '...' : $t;
    }
    public static function tgl(string $d): string {
        $b = ['Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'Mei','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Agu','Sep'=>'Sep','Oct'=>'Okt','Nov'=>'Nov','Dec'=>'Des'];
        return strtr(date('d M Y', strtotime($d)), $b);
    }
    public static function pageDate(string $table = '', string $dateCol = 'updated_at'): string {
        if ($table !== '') {
            try {
                $pdo = Database::conn();
                $cols = $pdo->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_COLUMN);
                $col = in_array($dateCol, $cols, true) ? $dateCol : (in_array('updated_at', $cols, true) ? 'updated_at' : (in_array('created_at', $cols, true) ? 'created_at' : ''));
                if ($col !== '') {
                    $v = $pdo->query("SELECT `$col` FROM `$table` ORDER BY `$col` DESC LIMIT 1")->fetchColumn();
                    if ($v) return self::tgl((string)$v);
                }
            } catch (Throwable) {}
        }
        try {
            $v = Database::conn()->query("SELECT `updated_at` FROM settings ORDER BY `updated_at` DESC LIMIT 1")->fetchColumn();
            if ($v) return self::tgl((string)$v);
        } catch (Throwable) {}
        return self::tgl(date('Y-m-d'));
    }
    // Time ago Asia/Jakarta: baru saja, X mnt/jam/hr lalu
    public static function ago(?string $dt): string {
        if (!$dt) return '-';
        try { $t = new DateTime($dt, new DateTimeZone('Asia/Jakarta')); } catch (Throwable) { return $dt; }
        $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $s = $now->getTimestamp() - $t->getTimestamp();
        if ($s < 0) $s = 0;
        if ($s < 60) return 'baru saja';
        if ($s < 3600) return floor($s / 60) . ' mnt lalu';
        if ($s < 86400) return floor($s / 3600) . ' jam lalu';
        if ($s < 86400 * 7) return floor($s / 86400) . ' hr lalu';
        return self::tgl($dt);
    }
    // Icon-only action buttons (konsisten semua modul)
    public static function iconBtns(array $btns): string {
        $h = '<span class="inline-flex items-center gap-1">';
        foreach ($btns as $b) {
            $t = $b['tip'] ?? '';
            if (($b['kind'] ?? '') === 'link') $h .= '<a href="' . $b['href'] . '" title="' . self::e($t) . '" aria-label="' . self::e($t) . '" class="w-8 h-8 inline-flex items-center justify-center rounded-lg border bg-white text-slate-600 hover:text-emerald-600 hover:border-emerald-300 transition"><i class="fa ' . $b['icon'] . ' text-xs"></i></a>';
            else $h .= '<form method="post"' . (!empty($b['confirm']) ? ' data-confirm' : '') . ' class="inline"><input type="hidden" name="csrf" value="' . Security::csrfToken() . '">' . ($b['extra'] ?? '') . '<button title="' . self::e($t) . '" aria-label="' . self::e($t) . '" class="w-8 h-8 inline-flex items-center justify-center rounded-lg border bg-white ' . ($b['cls'] ?? 'text-slate-600 hover:text-emerald-600 hover:border-emerald-300') . ' transition"><i class="fa ' . $b['icon'] . ' text-xs"></i></button></form>';
        }
        return $h . '</span>';
    }
    // Ikon edit/hapus standar
    public static function editBtn(int $id): array { return ['kind'=>'link','href'=>'?edit='.$id,'icon'=>'fa-pen','tip'=>'Edit']; }
    public static function delBtn(int $id, string $act='delete'): array { return ['icon'=>'fa-trash','tip'=>'Hapus','confirm'=>true,'cls'=>'text-red-600 hover:text-red-700 hover:border-red-300','extra'=>'<input type="hidden" name="act" value="'.$act.'"><input type="hidden" name="id" value="'.$id.'">']; }
    public static function paginate(int $total, int $per, int $page, string $base): string {
        $pages = max(1, (int)ceil($total / $per));
        if ($pages < 2) return '';
        $h = '<nav class="flex gap-1 justify-center mt-6">';
        for ($i = 1; $i <= $pages; $i++) {
            $a = $i === $page ? 'bg-emerald-600 text-white' : 'bg-white dark:bg-slate-800 hover:bg-emerald-50';
            $h .= '<a href="' . $base . '?page=' . $i . '" class="px-3 py-1.5 rounded-lg border text-sm ' . $a . '">' . $i . '</a>';
        }
        return $h . '</nav>';
    }
}
