<?php declare(strict_types=1); $title='Ekskul & Prestasi';
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/extras')); exit; }
$act=$_POST['act']??''; $t=$_POST['kind']??'ekskul'; $t=in_array($t,['ekskul','prestasi'],true)?$t:'ekskul'; $tbl=$t==='prestasi'?'achievements':'extracurriculars';
if($act==='bulk_delete'){ $ids=array_filter(array_map('intval',(array)($_POST['ids']??[]))); if(!$ids){ Session::flash('err','Pilih minimal 1 data.'); } else { $ph=implode(',',array_fill(0,count($ids),'?')); $db->prepare("DELETE FROM $tbl WHERE id IN ($ph)")->execute(array_values($ids)); Auth::log($db,'delete',$t,'Hapus bulk '.$t); Session::flash('ok',count($ids).' data dihapus.'); } }
elseif($act==='delete'){ $db->prepare("DELETE FROM $tbl WHERE id=?")->execute([(int)$_POST['id']]); Session::flash('ok','Dihapus.'); }
else{ $nm=trim($_POST['name']??$_POST['title']??''); if($nm===''){ Session::flash('err','Nama/judul wajib.'); } else {
if($t==='prestasi'){ if(!empty($_POST['id'])) $db->prepare("UPDATE achievements SET title=?,description=?,`year`=?,`level`=? WHERE id=?")->execute([$nm,$_POST['description']??'',$_POST['year']??'',$_POST['level']??'',(int)$_POST['id']]); else $db->prepare("INSERT INTO achievements(title,description,`year`,`level`) VALUES(?,?,?,?)")->execute([$nm,$_POST['description']??'',$_POST['year']??'',$_POST['level']??'']); }
else{ if(!empty($_POST['id'])) $db->prepare("UPDATE extracurriculars SET name=?,description=?,coach=?,schedule=? WHERE id=?")->execute([$nm,$_POST['description']??'',$_POST['coach']??'',$_POST['schedule']??'',(int)$_POST['id']]); else $db->prepare("INSERT INTO extracurriculars(name,description,coach,schedule) VALUES(?,?,?,?)")->execute([$nm,$_POST['description']??'',$_POST['coach']??'',$_POST['schedule']??'']); }
Auth::log($db,'save',$t,"Simpan $nm"); Session::flash('ok','Disimpan.'); } }
header('Location: '.Helper::url('admin/extras')); exit; }
$eks=$db->query("SELECT * FROM extracurriculars ORDER BY id DESC")->fetchAll();
$pres=$db->query("SELECT * FROM achievements ORDER BY id DESC")->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-star text-emerald-600 mr-1"></i>Ekstrakurikuler & Prestasi</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($eks) ?> ekskul</span>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($pres) ?> prestasi</span>
<button id="btnAddEks" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Ekskul</button>
<button id="btnAddPres" class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-trophy mr-1"></i>Tambah Prestasi</button>
</div>
<form method="post" id="bulkEks"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"><input type="hidden" name="kind" value="ekskul"></form>
<form method="post" id="bulkPres"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"><input type="hidden" name="kind" value="prestasi"></form>
<div class="grid lg:grid-cols-2 gap-3 text-sm items-start">
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="px-4 py-3 font-bold border-b">Ekstrakurikuler</div>
<div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b text-sm"><span id="selCountEks" class="text-slate-500">0 dipilih</span><button type="button" id="btnBulkEks" class="ml-auto bg-red-600 hover:bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[420px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-8"><input type="checkbox" id="checkAllEks"></th><th class="p-3 w-10">No</th><th class="p-3">Nama</th><th class="p-3">Pembina / Jadwal</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$eks): ?><tr><td colspan="5" class="p-10 text-center text-slate-500"><i class="fa fa-futbol text-3xl block mb-2"></i>Belum ada ekskul. Klik Tambah Ekskul.</td></tr><?php endif; ?>
<?php $noEks=1; foreach($eks as $e): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkEks" name="ids[]" value="<?= $e['id'] ?>" class="rowcheck rowcheckEks"></td><td class="p-3 text-slate-500"><?= $noEks++ ?></td><td class="p-3 font-semibold"><?= Helper::e($e['name']) ?><span class="block text-[11px] font-normal text-slate-400"><?= Helper::e(Helper::excerpt($e['description']??'',80)) ?></span></td>
<td class="p-3 text-xs text-slate-500"><?= Helper::e($e['coach']??'') ?><span class="block"><?= Helper::e($e['schedule']??'') ?></span></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['kind'=>'ekskul','id'=>$e['id'],'name'=>$e['name'],'coach'=>$e['coach']??'','schedule'=>$e['schedule']??'','description'=>$e['description']??'']),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="kind" value="ekskul"><input type="hidden" name="id" value="<?= $e['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="px-4 py-3 font-bold border-b">Prestasi</div>
<div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b text-sm"><span id="selCountPres" class="text-slate-500">0 dipilih</span><button type="button" id="btnBulkPres" class="ml-auto bg-red-600 hover:bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[420px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-8"><input type="checkbox" id="checkAllPres"></th><th class="p-3 w-10">No</th><th class="p-3">Judul</th><th class="p-3">Tahun / Tingkat</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$pres): ?><tr><td colspan="5" class="p-10 text-center text-slate-500"><i class="fa fa-trophy text-3xl block mb-2"></i>Belum ada prestasi. Klik Tambah Prestasi.</td></tr><?php endif; ?>
<?php $noPres=1; foreach($pres as $e): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkPres" name="ids[]" value="<?= $e['id'] ?>" class="rowcheck rowcheckPres"></td><td class="p-3 text-slate-500"><?= $noPres++ ?></td><td class="p-3 font-semibold"><?= Helper::e($e['title']) ?><span class="block text-[11px] font-normal text-slate-400"><?= Helper::e(Helper::excerpt($e['description']??'',80)) ?></span></td>
<td class="p-3 text-xs text-slate-500"><?= Helper::e($e['year']??'') ?><span class="block"><?= Helper::e($e['level']??'') ?></span></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['kind'=>'prestasi','id'=>$e['id'],'title'=>$e['title'],'year'=>$e['year']??'','level'=>$e['level']??'','description'=>$e['description']??'']),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="kind" value="prestasi"><input type="hidden" name="id" value="<?= $e['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>
</div>

<div id="eksModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl my-4 overflow-hidden">
<div class="flex items-center gap-2 px-5 py-3.5 border-b bg-white"><h2 class="font-extrabold" id="eksTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Ekskul</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" data-loading class="p-5 grid gap-3 text-sm bg-white"><?= Security::csrfField() ?>
<input type="hidden" name="kind" value="ekskul"><input type="hidden" name="id" id="e_id" value="0">
<label class="grid gap-1 font-semibold">Nama<input name="name" id="e_name" required placeholder="Pramuka" class="border rounded-lg p-2 font-normal"></label>
<div class="grid md:grid-cols-2 gap-3">
<label class="grid gap-1 font-semibold">Pembina<input name="coach" id="e_coach" placeholder="Nama pembina" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Jadwal<input name="schedule" id="e_schedule" placeholder="Sabtu 08:00" class="border rounded-lg p-2 font-normal"></label>
</div>
<label class="grid gap-1 font-semibold">Deskripsi<textarea name="description" id="e_desc" rows="3" placeholder="Deskripsi ekskul..." class="border rounded-lg p-2 font-normal"></textarea></label>
<div class="flex justify-center md:col-span-2"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>
<div id="presModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl my-4 overflow-hidden">
<div class="flex items-center gap-2 px-5 py-3.5 border-b bg-white"><h2 class="font-extrabold" id="presTitle"><i class="fa fa-trophy text-emerald-600 mr-1"></i>Tambah Prestasi</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" data-loading class="p-5 grid gap-3 text-sm bg-white"><?= Security::csrfField() ?>
<input type="hidden" name="kind" value="prestasi"><input type="hidden" name="id" id="p_id" value="0">
<label class="grid gap-1 font-semibold">Judul<input name="title" id="p_title" required placeholder="Juara 1 OSN" class="border rounded-lg p-2 font-normal"></label>
<div class="grid md:grid-cols-2 gap-3">
<label class="grid gap-1 font-semibold">Tahun<input name="year" id="p_year" placeholder="2024" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Tingkat<input name="level" id="p_level" placeholder="Nasional" class="border rounded-lg p-2 font-normal"></label>
</div>
<label class="grid gap-1 font-semibold">Deskripsi<textarea name="description" id="p_desc" rows="3" placeholder="Deskripsi prestasi..." class="border rounded-lg p-2 font-normal"></textarea></label>
<div class="flex justify-center md:col-span-2"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<script>
(function(){function bulk(caId,cls,scId,bbId,bfId){const ca=document.getElementById(caId),sc=document.getElementById(scId),bb=document.getElementById(bbId),bf=document.getElementById(bfId);if(!ca||!bb||!bf)return;const rows=()=>document.querySelectorAll('.'+cls),up=()=>{sc.textContent=document.querySelectorAll('.'+cls+':checked').length+' dipilih'};ca.addEventListener('change',()=>{rows().forEach(c=>c.checked=ca.checked);up()});document.addEventListener('change',e=>{if(e.target.classList&&e.target.classList.contains(cls))up()});bb.addEventListener('click',()=>{const n=document.querySelectorAll('.'+cls+':checked').length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 data.','warning');return}Swal.fire({title:'Hapus '+n+' data?',text:'Tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)bf.submit()})})}bulk('checkAllEks','rowcheckEks','selCountEks','btnBulkEks','bulkEks');bulk('checkAllPres','rowcheckPres','selCountPres','btnBulkPres','bulkPres');})();
</script>
<script>
const eksModal=document.getElementById('eksModal');
const presModal=document.getElementById('presModal');
function openEks(d){
  document.getElementById('eksTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Ekskul':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Ekskul');
  document.getElementById('e_id').value=d?.id||0;
  document.getElementById('e_name').value=d?.name||'';
  document.getElementById('e_coach').value=d?.coach||'';
  document.getElementById('e_schedule').value=d?.schedule||'';
  document.getElementById('e_desc').value=d?.description||'';
  eksModal.classList.remove('hidden');document.body.style.overflow='hidden';
}
function openPres(d){
  document.getElementById('presTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Prestasi':'<i class="fa fa-trophy text-emerald-600 mr-1"></i>Tambah Prestasi');
  document.getElementById('p_id').value=d?.id||0;
  document.getElementById('p_title').value=d?.title||'';
  document.getElementById('p_year').value=d?.year||'';
  document.getElementById('p_level').value=d?.level||'';
  document.getElementById('p_desc').value=d?.description||'';
  presModal.classList.remove('hidden');document.body.style.overflow='hidden';
}
function openModal(d){
  if(d?.kind==='prestasi')openPres(d);else openEks(d);
}
function closeModal(){eksModal.classList.add('hidden');presModal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAddEks').addEventListener('click',()=>openEks(null));
document.getElementById('btnAddPres').addEventListener('click',()=>openPres(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openModal(JSON.parse(b.dataset.row))));
document.querySelectorAll('#eksModal [data-close],#presModal [data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>
