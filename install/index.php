<?php
declare(strict_types=1);
// Bootstrap mandiri: /install bisa diakses langsung (bypass index.php) karena .htaccess skip direktori exist
if (!defined('ROOT')) {
    require dirname(__DIR__) . '/config/database.php';
    require dirname(__DIR__) . '/config/constants.php';
    foreach (['Database','Session','Security','Validator','Helper','Auth','Router'] as $__c) require_once dirname(__DIR__) . "/core/{$__c}.php";
    $APP = require dirname(__DIR__) . '/config/app.php';
    Session::start();
}
// Installer 6 langkah: config -> test -> tables -> admin -> site -> finish
$step = (int)($_GET['s'] ?? 1);
$lock = ROOT . '/install/lock.php';
if (is_file($lock)) { echo 'Installer terkunci. Hapus install/lock.php untuk instal ulang.'; exit; }
$err = ''; $msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 2) {
    $env = "DB_HOST=" . ($_POST['host'] ?? '127.0.0.1') . "\nDB_NAME=" . ($_POST['name'] ?? 'school_cms') . "\nDB_USER=" . ($_POST['user'] ?? 'root') . "\nDB_PASS=" . ($_POST['pass'] ?? '') . "\nAPP_URL=" . ($_POST['url'] ?? BASE_URL) . "\nAPP_NAME=\"" . ($_POST['app'] ?? 'Sekolah CMS') . "\"\nAPP_ENV=production\n";
    file_put_contents(ROOT . '/.env', $env);
    foreach (explode("\n", $env) as $l) { if (!str_contains($l, '=')) continue; [$k,$v] = explode('=', $l, 2); $_ENV[trim($k)] = trim($v, " \t\"'"); }
    try { Database::conn(); header('Location: ' . BASE_URL . '/install?s=3'); exit; }
    catch (Throwable $e) { $err = 'Koneksi gagal: ' . $e->getMessage(); }
}
if ($step === 3) {
    try {
        $db = Database::conn();
        $db->exec(file_get_contents(ROOT . '/database/schema.sql'));
        $db->exec(file_get_contents(ROOT . '/database/seed.sql'));
        header('Location: ' . BASE_URL . '/install?s=4'); exit;
    } catch (Throwable $e) { $err = 'Import gagal: ' . $e->getMessage(); }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 4) {
    try {
        $db = Database::conn();
        $h = password_hash($_POST['password'] ?? 'admin123', PASSWORD_DEFAULT);
        $db->prepare("DELETE FROM users WHERE username='admin'")->execute();
        $db->prepare("INSERT INTO users(role_id,name,username,email,password,is_active) VALUES(1,?,?,?, ?,1)")
            ->execute([$_POST['name'] ?? 'Administrator', $_POST['username'] ?? 'admin', $_POST['email'] ?? 'admin@sekolah.sch.id', $h]);
        if (!empty($_POST['site'])) { foreach ($_POST['site'] as $k => $v) { $db->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")->execute([$k, $v]); } }
        file_put_contents($lock, "<?php // installed " . date('c'));
        header('Location: ' . BASE_URL . '/install?s=6'); exit;
    } catch (Throwable $e) { $err = $e->getMessage(); }
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<title>Installer - Langkah <?= $step ?></title></head>
<body class="bg-slate-100 min-h-screen grid place-items-center p-4">
<div class="bg-white rounded-2xl shadow p-8 w-full max-w-lg">
<h1 class="text-xl font-bold mb-1">Installer SchoolCMS</h1><p class="text-sm text-slate-500 mb-4">Langkah <?= $step ?> dari 6</p>
<?php if ($err): ?><script>Swal.fire('Terjadi Kesalahan','<?= addslashes($err) ?>','error')</script><p class="text-red-600 text-sm mb-2"><?= Helper::e($err) ?></p><?php endif; ?>
<?php if ($step === 1): ?><a href="<?= BASE_URL ?>/install?s=2" class="bg-emerald-600 text-white px-4 py-2 rounded-lg">Mulai</a>
<?php elseif ($step === 2): ?><form method="post" class="grid gap-2"><input name="host" value="127.0.0.1" class="border rounded p-2" placeholder="DB_HOST"><input name="name" value="school_cms" class="border rounded p-2" placeholder="DB_NAME"><input name="user" value="root" class="border rounded p-2" placeholder="DB_USER"><input name="pass" class="border rounded p-2" placeholder="DB_PASS"><input name="url" value="<?= Helper::e(BASE_URL) ?>" class="border rounded p-2"><input name="app" value="SMK Nusantara" class="border rounded p-2"><button class="bg-emerald-600 text-white rounded p-2">Test & Simpan</button></form>
<?php elseif ($step === 4): ?><form method="post" class="grid gap-2"><input name="name" value="Administrator" class="border rounded p-2"><input name="username" value="admin" class="border rounded p-2"><input name="email" value="admin@sekolah.sch.id" class="border rounded p-2"><input name="password" type="password" value="admin123" class="border rounded p-2"><input name="site[school_name]" value="SMK Nusantara" class="border rounded p-2"><button class="bg-emerald-600 text-white rounded p-2">Buat Admin & Selesai</button></form>
<?php else: ?><p class="text-sm">Tabel terbuat. Lanjut buat admin.</p><a href="<?= BASE_URL ?>/install?s=4" class="bg-emerald-600 text-white px-4 py-2 rounded-lg">Lanjut</a><?php endif; ?>
<?php if ($step === 6): ?><script>Swal.fire('Berhasil!','Instalasi selesai.','success')</script><p>Hapus folder /install. <a class="text-emerald-600" href="<?= BASE_URL ?>/">Beranda</a> | <a class="text-emerald-600" href="<?= BASE_URL ?>/admin/login">Login Admin</a></p><?php endif; ?>
</div></body></html>
