<?php
header('Content-Type: text/plain; charset=UTF-8');
$custom = '';
try{ $custom = Database::setting('robots_txt',''); }catch(Throwable){}
if(trim((string)$custom)!==''){ echo trim((string)$custom); return; }
echo "User-agent: *\nAllow: /\n";
echo "Disallow: /admin\nDisallow: /api\nDisallow: /install\n";
echo 'Sitemap: '.Helper::url('sitemap.xml')."\n";
