<?php
declare(strict_types=1);
$uri = Router::uri();
$db = $DB;
try {
if ($uri === '/' ) { require ROOT.'/pages/home.php'; exit; }
if ($uri === '/profil' || $uri === '/sejarah') { $_GET['slug']=$uri; require ROOT.'/pages/profile.php'; exit; }
if ($uri === '/visi-misi') { require ROOT.'/pages/vision.php'; exit; }
if ($uri === '/berita') { require ROOT.'/pages/news.php'; exit; }
if ($uri === '/indeks-berita' || $uri === '/berita/indeks') { require ROOT.'/pages/news-index.php'; exit; }
if (str_starts_with($uri, '/berita/')) { $slug = basename($uri); require ROOT.'/pages/news-detail.php'; exit; }
if ($uri === '/galeri') { require ROOT.'/pages/gallery.php'; exit; }
if ($uri === '/guru') { require ROOT.'/pages/teachers.php'; exit; }
if ($uri === '/prestasi') { require ROOT.'/pages/prestasi.php'; exit; }
if ($uri === '/ekstrakurikuler') { require ROOT.'/pages/ekskul.php'; exit; }
if ($uri === '/struktur-organisasi') { require ROOT.'/pages/struktur.php'; exit; }
if ($uri === '/kurikulum') { require ROOT.'/pages/kurikulum.php'; exit; }
if ($uri === '/agenda') { require ROOT.'/pages/agenda.php'; exit; }
if ($uri === '/pengumuman') { require ROOT.'/pages/announcements.php'; exit; }
if ($uri === '/kontak') { require ROOT.'/pages/contact.php'; exit; }
if ($uri === '/sitemap.xml') { require ROOT.'/pages/sitemap.php'; exit; }
if ($uri === '/robots.txt') { require ROOT.'/robots.php'; exit; }
// dynamic page by slug
$slug = trim($uri, '/');
$st = $db->prepare("SELECT * FROM pages WHERE slug=? AND status='published' AND deleted_at IS NULL LIMIT 1");
$st->execute([$slug]);
$pg = $st->fetch();
if ($pg) { require ROOT.'/pages/dynamic.php'; exit; }
http_response_code(404); require ROOT.'/templates/error/404.php'; exit;
} catch (Throwable $e) { http_response_code(500); require ROOT.'/templates/error/500.php'; exit; }
