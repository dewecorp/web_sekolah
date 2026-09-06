<?php declare(strict_types=1); $title='Identitas Sekolah';
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/settings')); exit; }
foreach(($_POST['s']??[]) as $k=>$v){ $k=preg_replace('/[^a-z_]/','',$k); if(in_array($k,['meta_title','meta_description','meta_keywords','og_image','canonical_url'],true)) continue; $db->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")->execute([$k,(string)$v]); }
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
$logo = $saveUpload('logo'); $pp = $saveUpload('principal_photo');
if ($upErr !== '') { Session::flash('err', $upErr); header('Location: '.Helper::url('admin/settings')); exit; }
if (is_string($logo)) { $db->prepare("INSERT INTO settings(`key`,`value`) VALUES('logo',?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")->execute([$logo]); }
if($p){ $db->prepare("UPDATE school_profile SET principal_name=?,principal_title=?,principal_greeting=?,principal_photo=COALESCE(?,principal_photo) WHERE id=?")->execute([$_POST['principal_name']??'',$_POST['principal_title']??'',$_POST['principal_greeting']??'',$pp,$p['id']]); }
Auth::log($db,'update','settings','Ubah identitas sekolah'); Session::flash('ok','Identitas sekolah disimpan.');
header('Location: '.Helper::url('admin/settings')); exit; }
$sets=[]; foreach($db->query("SELECT * FROM settings") as $r) $sets[$r['key']]=$r['value'];
$prof=$db->query("SELECT * FROM school_profile LIMIT 1")->fetch()?:[];
require ROOT.'/templates/admin/header.php'; ?>
<h1 class="text-xl font-extrabold mb-4"><i class="fa fa-school text-emerald-600 mr-1"></i>Identitas Sekolah</h1>
<form method="post" enctype="multipart/form-data" data-loading id="identityForm" class="grid lg:grid-cols-2 gap-3 text-sm items-start"><?= Security::csrfField() ?>
<div class="grid gap-3">
<div class="bg-white rounded-2xl border p-4 grid gap-2"><h2 class="font-bold"><i class="fa fa-school text-emerald-600 mr-1"></i>Identitas Sekolah</h2>
<?php foreach(['school_name'=>'Nama Sekolah','tagline'=>'Tagline','address'=>'Alamat','phone'=>'Telepon','email'=>'Email','footer_text'=>'Footer Text','homepage_title'=>'Homepage Title'] as $k=>$l): ?>
<label class="grid gap-1"><?= $l ?><input name="s[<?= $k ?>]" value="<?= Helper::e($sets[$k]??'') ?>" class="border rounded-lg p-2"></label><?php endforeach; ?>
<label class="grid gap-1">Hero Alignment<select name="s[hero_align]" class="border rounded-lg p-2"><option value="left" <?= ($sets['hero_align']??'center')==='left'?'selected':'' ?>>Kiri</option><option value="center" <?= ($sets['hero_align']??'center')==='center'?'selected':'' ?>>Tengah</option><option value="right" <?= ($sets['hero_align']??'center')==='right'?'selected':'' ?>>Kanan</option></select><span class="text-xs font-normal text-slate-400">Rata kiri/tengah/kanan hero & semua elemen</span></label>
<label class="grid gap-1">Logo<input type="file" name="logo" accept="image/*" class="border rounded-lg p-2"></label></div>
<div class="bg-white rounded-2xl border p-4 grid gap-2"><h2 class="font-bold"><i class="fa fa-share-nodes text-emerald-600 mr-1"></i>Media Sosial</h2>
<?php foreach(['facebook'=>'Facebook','instagram'=>'Instagram','youtube'=>'YouTube','tiktok'=>'TikTok'] as $k=>$l): ?>
<label class="grid gap-1"><?= $l ?><input name="s[<?= $k ?>]" value="<?= Helper::e($sets[$k]??'') ?>" placeholder="https://..." class="border rounded-lg p-2"></label><?php endforeach; ?></div>
</div>
<div class="grid gap-3">
<div class="bg-white rounded-2xl border p-4 grid gap-2"><h2 class="font-bold">Profil</h2>
<label class="grid gap-1">Kepala Sekolah<input name="principal_name" value="<?= Helper::e($prof['principal_name']??'') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Jabatan<input name="principal_title" value="<?= Helper::e($prof['principal_title']??'') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Sambutan<textarea name="principal_greeting" id="principalGreeting" rows="6"><?= Helper::e($prof['principal_greeting']??'') ?></textarea><span id="editorWarn" class="hidden text-xs font-normal text-red-600">Editor gagal dimuat (CDN diblokir). Textarea biasa tetap bisa disimpan.</span></label>
<label class="grid gap-1">Foto Kepala Sekolah<input type="file" name="principal_photo" accept="image/*" class="border rounded-lg p-2"></label></div>
<div class="bg-white rounded-2xl border p-4 grid gap-2"><h2 class="font-bold"><i class="fa fa-clock text-emerald-600 mr-1"></i>Jam Layanan Kontak</h2>
<label class="grid gap-1">Hari<input name="s[service_days]" value="<?= Helper::e($sets['service_days']??'Senin - Jumat') ?>" placeholder="Senin - Jumat" class="border rounded-lg p-2"></label>
<div class="grid grid-cols-2 gap-2">
<label class="grid gap-1">Jam Buka<input type="time" name="s[service_open]" value="<?= Helper::e($sets['service_open']??'07:00') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Jam Tutup<input type="time" name="s[service_close]" value="<?= Helper::e($sets['service_close']??'15:30') ?>" class="border rounded-lg p-2"></label></div></div>
</div>
<div class="lg:col-span-2 flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-10 py-2.5 font-bold w-full sm:w-auto sm:min-w-[220px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan Identitas</button></div></form>
<script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@41.4.2/build/ckeditor.js"></script>
<script>
let greetEditor=null;
(function(){
  const el=document.getElementById('principalGreeting'),warn=document.getElementById('editorWarn');
  if(!el)return;
  if(!window.ClassicEditor){ warn&&warn.classList.remove('hidden'); return; }
  ClassicEditor.create(el).then(e=>{greetEditor=e}).catch(()=>warn&&warn.classList.remove('hidden'));
})();
document.getElementById('identityForm').addEventListener('submit',()=>{ if(greetEditor){ try{document.getElementById('principalGreeting').value=greetEditor.getData()}catch(_){} } });
</script>
<style>.ck-editor__editable{min-height:220px}</style>
<?php require ROOT.'/templates/admin/footer.php'; ?>
