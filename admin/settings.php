<?php declare(strict_types=1); $title='Pengaturan';
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/settings')); exit; }
foreach(($_POST['s']??[]) as $k=>$v){ $k=preg_replace('/[^a-z_]/','',$k); $db->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")->execute([$k,(string)$v]); }
$p=$db->query("SELECT * FROM school_profile LIMIT 1")->fetch();
$upErr='';
$saveUpload = function(string $field) use ($APP, &$upErr) {
  if (empty($_FILES[$field]['name'] ?? '')) return null;
  $e = Security::validImage($_FILES[$field], $APP);
  if ($e) { $upErr = $e; return false; }
  $n = Security::safeName($_FILES[$field]['name']);
  move_uploaded_file($_FILES[$field]['tmp_name'], ROOT.'/assets/uploads/'.$n);
  return $n;
};
$logo = $saveUpload('logo'); $pp = $saveUpload('principal_photo'); $oc = $saveUpload('org_chart');
if ($upErr !== '') { Session::flash('err', $upErr); header('Location: '.Helper::url('admin/settings')); exit; }
if (is_string($logo)) { $db->prepare("INSERT INTO settings(`key`,`value`) VALUES('logo',?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")->execute([$logo]); }
if($p){ $db->prepare("UPDATE school_profile SET principal_name=?,principal_title=?,principal_greeting=?,history=?,vision=?,mission=?,principal_photo=COALESCE(?,principal_photo),org_chart=COALESCE(?,org_chart),total_students=?,total_teachers=?,total_extracurricular=?,years_established=? WHERE id=?")->execute([$_POST['principal_name']??'',$_POST['principal_title']??'',$_POST['principal_greeting']??'',$_POST['history']??'',$_POST['vision']??'',$_POST['mission']??'',$pp,$oc,(int)($_POST['total_students']??0),(int)($_POST['total_teachers']??0),(int)($_POST['total_extracurricular']??0),(int)($_POST['years_established']??0),$p['id']]); }
Auth::log($db,'update','settings','Ubah pengaturan'); Session::flash('ok','Pengaturan disimpan.');
header('Location: '.Helper::url('admin/settings')); exit; }
$sets=[]; foreach($db->query("SELECT * FROM settings") as $r) $sets[$r['key']]=$r['value'];
$prof=$db->query("SELECT * FROM school_profile LIMIT 1")->fetch()?:[];
$seo=$db->query("SELECT * FROM seo_settings LIMIT 1")->fetch()?:[];
require ROOT.'/templates/admin/header.php'; ?>
<h1 class="text-xl font-extrabold mb-4">Identitas, Profil & SEO</h1>
<form method="post" enctype="multipart/form-data" data-loading class="grid lg:grid-cols-2 gap-3 text-sm"><?= Security::csrfField() ?>
<div class="bg-white rounded-2xl border p-4 grid gap-2"><h2 class="font-bold">Identitas Sekolah</h2>
<?php foreach(['school_name'=>'Nama Sekolah','tagline'=>'Tagline','address'=>'Alamat','phone'=>'Telepon','email'=>'Email','footer_text'=>'Footer Text','powered_by'=>'Powered By','homepage_title'=>'Homepage Title','facebook'=>'Facebook','instagram'=>'Instagram','youtube'=>'YouTube','tiktok'=>'TikTok'] as $k=>$l): ?>
<label class="grid gap-1"><?= $l ?><input name="s[<?= $k ?>]" value="<?= Helper::e($sets[$k]??'') ?>" class="border rounded-lg p-2"></label><?php endforeach; ?>
<label class="grid gap-1">Jam Layanan - Hari<input name="s[service_days]" value="<?= Helper::e($sets['service_days']??'Senin - Jumat') ?>" placeholder="Senin - Jumat" class="border rounded-lg p-2"></label>
<div class="grid grid-cols-2 gap-2">
<label class="grid gap-1">Jam Buka<input type="time" name="s[service_open]" value="<?= Helper::e($sets['service_open']??'07:00') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Jam Tutup<input type="time" name="s[service_close]" value="<?= Helper::e($sets['service_close']??'15:30') ?>" class="border rounded-lg p-2"></label></div>
<label class="grid gap-1">Logo<input type="file" name="logo" accept="image/*" class="border rounded-lg p-2"></label></div>
<div class="grid gap-3">
<div class="bg-white rounded-2xl border p-4 grid gap-2"><h2 class="font-bold">Profil & Statistik</h2>
<label class="grid gap-1">Kepala Sekolah<input name="principal_name" value="<?= Helper::e($prof['principal_name']??'') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Jabatan<input name="principal_title" value="<?= Helper::e($prof['principal_title']??'') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Sambutan<textarea name="principal_greeting" class="border rounded-lg p-2"><?= Helper::e($prof['principal_greeting']??'') ?></textarea></label>
<label class="grid gap-1">Foto Kepala Sekolah<input type="file" name="principal_photo" accept="image/*" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Bagan Struktur (gambar)<input type="file" name="org_chart" accept="image/*" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Visi<textarea name="vision" class="border rounded-lg p-2"><?= Helper::e($prof['vision']??'') ?></textarea></label>
<label class="grid gap-1">Misi<textarea name="mission" class="border rounded-lg p-2"><?= Helper::e($prof['mission']??'') ?></textarea></label>
<label class="grid gap-1">Sejarah<textarea name="history" class="border rounded-lg p-2"><?= Helper::e($prof['history']??'') ?></textarea></label>
<div class="grid grid-cols-2 gap-2">
<label class="grid gap-1">Siswa<input type="number" name="total_students" value="<?= (int)($prof['total_students']??0) ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Guru<input type="number" name="total_teachers" value="<?= (int)($prof['total_teachers']??0) ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Ekskul<input type="number" name="total_extracurricular" value="<?= (int)($prof['total_extracurricular']??0) ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Tahun Berdiri<input type="number" name="years_established" value="<?= (int)($prof['years_established']??0) ?>" class="border rounded-lg p-2"></label></div></div>
<div class="bg-white rounded-2xl border p-4 grid gap-2"><h2 class="font-bold">SEO Dasar</h2>
<?php foreach(['meta_title'=>'Meta Title','meta_description'=>'Meta Desc','meta_keywords'=>'Keywords','og_image'=>'OG Image','canonical_url'=>'Canonical'] as $k=>$l): ?>
<label class="grid gap-1"><?= $l ?><input name="s[<?= $k ?>]" value="<?= Helper::e($sets[$k]??$seo[$k]??'') ?>" class="border rounded-lg p-2"></label><?php endforeach; ?>
<button class="bg-emerald-600 text-white rounded-lg p-2 font-bold">Simpan Semua</button></div>
</div></form>
<?php require ROOT.'/templates/admin/footer.php'; ?>
