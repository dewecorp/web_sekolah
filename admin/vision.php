<?php declare(strict_types=1); Auth::requireRole(['administrator','editor']); $title='Visi, Misi & Tujuan';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/vision')); exit; }
  foreach(['vm_title','vm_show','vision_content','mission_content','goals_content'] as $k){
    if(!array_key_exists($k,$_POST['s']??[])) continue;
    $v=trim((string)$_POST['s'][$k]);
    if($k==='vm_show') $v=$v==='1'?'1':'0';
    $db->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")->execute([$k,$v]);
  }
  Auth::log($db,'update','vision','Ubah visi misi tujuan'); Session::flash('ok','Visi, misi & tujuan disimpan.');
  header('Location: '.Helper::url('admin/vision')); exit;
}
$sets=[]; foreach($db->query("SELECT `key`,`value` FROM settings") as $r) $sets[$r['key']]=$r['value'];
if(empty($sets['vision_content'])||empty($sets['mission_content'])){
  try{
    $old=$db->query("SELECT vision,mission FROM school_profile LIMIT 1")->fetch()?:[];
    if(empty($sets['vision_content'])&&!empty($old['vision'])) $sets['vision_content']=$old['vision'];
    if(empty($sets['mission_content'])&&!empty($old['mission'])) $sets['mission_content']=$old['mission'];
  }catch(Throwable){}
}
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-bullseye text-emerald-600 mr-1"></i>Visi, Misi & Tujuan</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold">public: /visi-misi</span>
<a href="<?= Helper::url('visi-misi') ?>" target="_blank" rel="noopener noreferrer" class="ml-auto text-sm px-3 py-1.5 border rounded-lg bg-white"><i class="fa fa-eye mr-1"></i>Lihat Public</a>
</div>
<form method="post" data-loading id="vmForm" class="grid gap-3 text-sm items-start"><?= Security::csrfField() ?>
<div class="bg-white rounded-2xl border p-4 grid md:grid-cols-2 gap-2">
<label class="grid gap-1">Judul public<input name="s[vm_title]" value="<?= Helper::e($sets['vm_title']??'Visi, Misi & Tujuan') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Tampil di public<select name="s[vm_show]" class="border rounded-lg p-2"><option value="1" <?= ($sets['vm_show']??'1')==='1'?'selected':'' ?>>Tampilkan</option><option value="0" <?= ($sets['vm_show']??'1')==='0'?'selected':'' ?>>Sembunyikan (404)</option></select></label>
</div>
<div class="bg-white rounded-2xl border p-4 grid gap-2">
<h2 class="font-bold"><i class="fa fa-eye text-emerald-600 mr-1"></i>Visi</h2>
<label class="grid gap-1">Isi visi<textarea name="s[vision_content]" id="visionContent" rows="6"><?= Helper::e($sets['vision_content']??'') ?></textarea><span class="editorWarn hidden text-xs font-normal text-red-600">Editor gagal dimuat (CDN diblokir). Textarea biasa tetap bisa disimpan.</span></label>
</div>
<div class="bg-white rounded-2xl border p-4 grid gap-2">
<h2 class="font-bold"><i class="fa fa-list-check text-emerald-600 mr-1"></i>Misi</h2>
<label class="grid gap-1">Isi misi (satu poin per baris / list)<textarea name="s[mission_content]" id="missionContent" rows="8"><?= Helper::e($sets['mission_content']??'') ?></textarea><span class="editorWarn hidden text-xs font-normal text-red-600">Editor gagal dimuat (CDN diblokir). Textarea biasa tetap bisa disimpan.</span></label>
</div>
<div class="bg-white rounded-2xl border p-4 grid gap-2">
<h2 class="font-bold"><i class="fa fa-flag text-emerald-600 mr-1"></i>Tujuan</h2>
<label class="grid gap-1">Isi tujuan<textarea name="s[goals_content]" id="goalsContent" rows="8"><?= Helper::e($sets['goals_content']??'') ?></textarea><span class="editorWarn hidden text-xs font-normal text-red-600">Editor gagal dimuat (CDN diblokir). Textarea biasa tetap bisa disimpan.</span></label>
</div>
<div class="flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2.5 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button></div>
</form>
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mt-3 text-sm"><b><i class="fa fa-list-ul text-amber-600 mr-1"></i>Menampilkan di menu public:</b> buka <a href="<?= Helper::url('admin/menus') ?>" class="text-emerald-700 font-bold">Menu Manager → Tautan Khusus</a> tambah URL <code class="font-mono bg-white px-1 rounded">/visi-misi</code>.</div>
<script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.1/tinymce.min.js"></script>
<script>
let vmEditors=[];
(function(){
  if(!window.tinymce||!window.RichEditorCreate){ document.querySelectorAll('.editorWarn').forEach(x=>x.classList.remove('hidden')); return; }
  ['visionContent','missionContent','goalsContent'].forEach(id=>{
    const el=document.getElementById(id); if(!el)return;
    window.RichEditorCreate(el,{height:400}).then(e=>{vmEditors.push(e)}).catch(()=>{ el.closest('label')?.querySelector('.editorWarn')?.classList.remove('hidden') });
  });
})();
document.getElementById('vmForm').addEventListener('submit',()=>{
  const ids=['visionContent','missionContent','goalsContent'];
  vmEditors.forEach(e=>{ try{ const el=e.sourceElement; if(el&&ids.includes(el.id)) el.value=e.getData() }catch(_){} });
});
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>




