<?php declare(strict_types=1); $title='SEO';
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/seo')); exit; }
foreach(['meta_title','meta_description','meta_keywords','og_image','canonical_url','robots_content','google_verification','bing_verification','yandex_verification','ga_measurement_id','robots_txt'] as $k){ if(!array_key_exists($k,$_POST['s']??[])) continue; $db->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")->execute([$k,(string)$_POST['s'][$k]]); }
Auth::log($db,'update','seo','Ubah pengaturan SEO'); Session::flash('ok','Pengaturan SEO disimpan.');
header('Location: '.Helper::url('admin/seo')); exit; }
$sets=[]; foreach($db->query("SELECT `key`,`value` FROM settings") as $r) $sets[$r['key']]=$r['value'];
try{ $seo=$db->query("SELECT * FROM seo_settings LIMIT 1")->fetch()?:[]; }catch(Throwable){ $seo=[]; }
require ROOT.'/templates/admin/header.php'; ?>
<h1 class="text-xl font-extrabold mb-4"><i class="fa fa-magnifying-glass text-emerald-600 mr-1"></i>SEO</h1>
<form method="post" data-loading class="grid lg:grid-cols-2 gap-3 text-sm items-start"><?= Security::csrfField() ?>
<div class="bg-white rounded-2xl border p-4 grid gap-2">
<h2 class="font-bold"><i class="fa fa-tags text-emerald-600 mr-1"></i>Meta Dasar</h2>
<?php foreach(['meta_title'=>'Meta Title','meta_description'=>'Meta Description','meta_keywords'=>'Keywords (pisah koma)','og_image'=>'OG Image (URL / path upload)','canonical_url'=>'Canonical URL (kosong = otomatis)'] as $k=>$l): ?>
<label class="grid gap-1"><?= $l ?><input name="s[<?= $k ?>]" value="<?= Helper::e($sets[$k]??$seo[$k]??'') ?>" class="border rounded-lg p-2"></label><?php endforeach; ?>
<p class="text-xs text-slate-400">Dipakai di &lt;title&gt;, meta description/keywords, dan Open Graph semua halaman public. Kosong = pakai default (nama sekolah & deskripsi umum).</p>
</div>
<div class="grid gap-3">
<div class="bg-white rounded-2xl border p-4 grid gap-2">
<h2 class="font-bold"><i class="fa fa-shield-halved text-emerald-600 mr-1"></i>Verifikasi Search Engine</h2>
<?php foreach(['google_verification'=>'Google Search Console','bing_verification'=>'Bing Webmaster','yandex_verification'=>'Yandex Webmaster'] as $k=>$l): ?>
<label class="grid gap-1"><?= $l ?><input name="s[<?= $k ?>]" value="<?= Helper::e($sets[$k]??'') ?>" placeholder="kode verifikasi saja" class="border rounded-lg p-2 font-mono text-xs"></label><?php endforeach; ?>
<label class="grid gap-1">Google Analytics (Measurement ID)<input name="s[ga_measurement_id]" value="<?= Helper::e($sets['ga_measurement_id']??'') ?>" placeholder="G-XXXXXXX" class="border rounded-lg p-2 font-mono text-xs"></label>
</div>
<div class="bg-white rounded-2xl border p-4 grid gap-2">
<h2 class="font-bold"><i class="fa fa-robot text-emerald-600 mr-1"></i>Robots & Sitemap</h2>
<label class="grid gap-1">Meta robots<input name="s[robots_content]" value="<?= Helper::e($sets['robots_content']??'') ?>" placeholder="index, follow (kosong = default SEO)" class="border rounded-lg p-2 font-mono text-xs"></label>
<label class="grid gap-1">Isi robots.txt kustom<textarea name="s[robots_txt]" rows="5" placeholder="Kosong = otomatis (allow /, sitemap). Tulis manual bila perlu." class="border rounded-lg p-2 font-mono text-xs"><?= Helper::e($sets['robots_txt']??'') ?></textarea></label>
<p class="text-xs text-slate-400">Sitemap: <a href="<?= Helper::url('sitemap.xml') ?>" target="_blank" class="text-emerald-700 font-bold">/sitemap.xml</a> • Robots: <a href="<?= Helper::url('robots.txt') ?>" target="_blank" class="text-emerald-700 font-bold">/robots.txt</a></p>
</div>
</div>
<div class="lg:col-span-2 flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2.5 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button></div>
</form>
<?php require ROOT.'/templates/admin/footer.php'; ?>
