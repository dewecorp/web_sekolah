<?php
declare(strict_types=1);
// Bootstrap mandiri: /admin adalah folder asli, .htaccess skip rewrite (!-d),
// sehingga request /admin dieksekusi langsung tanpa lewat index.php utama.
if (!defined('ROOT')) {
    require dirname(__DIR__) . '/config/database.php';
    require dirname(__DIR__) . '/config/constants.php';
    foreach (['Database','Session','Security','Validator','Helper','Auth','Router'] as $__c) require_once dirname(__DIR__) . "/core/{$__c}.php";
    $APP = require dirname(__DIR__) . '/config/app.php';
    Session::start();
    try { $DB = Database::conn(); } catch (Throwable $e) { http_response_code(500); echo 'DB error. Cek .env / install.'; exit; }
    try { Auth::tryRemember($DB); } catch (Throwable) {}
}
if (!isset($APP)) $APP = require ROOT . '/config/app.php';
if (!isset($DB)) $DB = Database::conn();
$uri = Router::uri();
$db = $DB;

// login page
if ($uri === '/admin/login' || $uri === '/admin/login/') { require ROOT.'/admin/login.php'; exit; }
if ($uri === '/admin/logout') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!Security::verifyCsrf($_POST['csrf'] ?? null)) { Session::flash('err','CSRF tidak valid.'); header('Location: '.BASE_URL.'/admin'); exit; }
        Auth::logout($db);
    }
    header('Location: '.BASE_URL.'/admin/login'); exit;
}

// proteksi
Auth::requireLogin();
// Retensi global: log aktivitas kedaluwarsa 24 jam di semua halaman admin
try { $db->exec("DELETE FROM activity_logs WHERE created_at < NOW() - INTERVAL 24 HOUR"); } catch (Throwable) {}
// Notif pesan kontak hanya tampil 24 jam terakhir
try { $db->exec("CREATE TABLE IF NOT EXISTS contact_messages(id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(150) NOT NULL,email VARCHAR(190) NOT NULL,subject VARCHAR(190) NOT NULL DEFAULT '',message MEDIUMTEXT NOT NULL,is_read TINYINT(1) NOT NULL DEFAULT 0,created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,KEY idx_read_created (is_read,created_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"); } catch (Throwable) {}
$role = $_SESSION['user']['role'] ?? 'author';
$roleMap = [
  'administrator' => ['pages','posts','categories','media','downloads','gallery','comments','announcements','agenda','teachers','students','sarana','sarana-pembelajaran','ekskul','prestasi','structure','curriculum','vision','statistics','menus','megamenu','widgets','footer','sections','themes','appearance','settings','seo','users','logs','inbox'],
  'editor' => ['pages','posts','categories','media','downloads','gallery','comments','announcements','agenda','teachers','students','sarana','sarana-pembelajaran','ekskul','prestasi','structure','curriculum','vision','statistics','menus','megamenu','widgets','footer','sections','themes','appearance','inbox'],
  'author' => ['posts','pages','media','gallery'],
];
$allow = $roleMap[$role] ?? $roleMap['author'];
if ($uri === '/admin/system-update' || $uri === '/admin/system-update/') { require ROOT.'/admin/system-update.php'; exit; }
$path = trim(substr($uri, 6), '/'); // hapus /admin
if ($path === '') { require ROOT.'/admin/dashboard.php'; exit; }
$seg = explode('/', $path)[0];
if ($seg === 'sliders') { header('Location: ' . Helper::url('admin/sections')); exit; }
if ($seg === 'academic') { header('Location: ' . Helper::url('admin/structure')); exit; }
if (!in_array($seg, $allow, true)) { http_response_code(403); require ROOT.'/templates/error/403.php'; exit; }
require ROOT.'/admin/'.$seg.'.php';




