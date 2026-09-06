<?php declare(strict_types=1); $title='SEO';
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/seo')); exit; }
foreach(['meta_title','meta_description','meta_keywords','og_image','canonical_url'] as $k){ if(!array_key_exists($k,$_POST['s']??[])) continue; $db->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")->execute([$k,(string)$_POST['s'][$k]]); }
Auth::log($db,'update','seo','Ubah pengaturan SEO'); Session::flash('ok','Pengaturan SEO disimpan.');
header('Location: '.Helper::url('admin/seo')); exit; }
$sets=[]; foreach($db->query("SELECT `key`,`value` FROM settings") as $r) $sets[$r['key']]=$r['value'];
try{ $seo=$db->query("SELECT * FROM seo_settings LIMIT 1")->fetch()?:[]; }catch(Throwable){ $seo=[]; }
require ROOT.'/templates/admin/header.php'; ?>
<h1 class="text-xl font-extrabold mb-4"><i class="fa fa-magnifying-glass text-emerald-600 mr-1"></i>SEO</h1>
<form method="post" data-loading class="bg-white rounded-2xl border p-4 grid gap-2 text-sm max-w-2xl"><?= Security::csrfField() ?>
<?php foreach(['meta_title'=>'Meta Title','meta_description'=>'Meta Description','meta_keywords'=>'Keywords (pisah koma)','og_image'=>'OG Image (URL / path upload)','canonical_url'=>'Canonical URL'] as $k=>$l): ?>
<label class="grid gap-1"><?= $l ?><input name="s[<?= $k ?>]" value="<?= Helper::e($sets[$k]??$seo[$k]??'') ?>" class="border rounded-lg p-2"></label><?php endforeach; ?>
<p class="text-xs text-slate-400">Dipakai di &lt;title&gt;, meta description/keywords, dan Open Graph semua halaman public. Kosong = pakai default (nama sekolah & deskripsi umum).</p>
<button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl py-2.5 font-bold"><i class="fa fa-floppy-disk mr-1"></i>Simpan SEO</button>
</form>
<?php require ROOT.'/templates/admin/footer.php'; ?>
