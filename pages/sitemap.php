<?php
header("Content-Type: text/xml; charset=UTF-8");
$base = Helper::url();
$now = date('Y-m-d');
$urls = [];
$urls[] = ['loc'=>$base,'lastmod'=>$now,'priority'=>'1.0'];
$static = ['profil','visi-misi','struktur-organisasi','kurikulum','berita','indeks-berita','galeri','guru','siswa','sarana','sarana-pembelajaran','prestasi','ekstrakurikuler','agenda','pengumuman','kontak'];
foreach($static as $s) $urls[] = ['loc'=>Helper::url($s),'lastmod'=>$now,'priority'=>'0.8'];
try{ foreach($db->query("SELECT slug,updated_at FROM posts WHERE status='published' AND deleted_at IS NULL ORDER BY updated_at DESC, id DESC") as $r) $urls[]=['loc'=>Helper::url('berita/'.$r['slug']),'lastmod'=>substr((string)($r['updated_at']??$now),0,10),'priority'=>'0.7']; }catch(Throwable){}
try{ foreach($db->query("SELECT slug FROM pages WHERE status='published' AND deleted_at IS NULL ORDER BY updated_at DESC") as $r) $urls[]=['loc'=>Helper::url($r['slug']),'lastmod'=>$now,'priority'=>'0.6']; }catch(Throwable){}
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; ?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><?php foreach($urls as $u): ?><url><loc><?= Helper::e($u['loc']) ?></loc><lastmod><?= Helper::e($u['lastmod']) ?></lastmod><changefreq>weekly</changefreq><priority><?= $u['priority'] ?></priority></url><?php endforeach; ?></urlset>

