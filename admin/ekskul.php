<?php declare(strict_types=1); $title='Ekstrakurikuler';
$days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/ekskul')); exit; }
$act=$_POST['act']??'';
if($act==='bulk_delete'){ $ids=array_filter(array_map('intval',(array)($_POST['ids']??[]))); if(!$ids){ Session::flash('err','Pilih minimal 1 data.'); } else { $ph=implode(',',array_fill(0,count($ids),'?')); $db->prepare("DELETE FROM extracurriculars WHERE id IN ($ph)")->execute(array_values($ids)); Auth::log($db,'delete','ekskul','Hapus bulk ekskul'); Session::flash('ok',count($ids).' data dihapus.'); } }
elseif($act==='delete'){ $db->prepare("DELETE FROM extracurriculars WHERE id=?")->execute([(int)$_POST['id']]); Session::flash('ok','Dihapus.'); }
else{ $nm=trim($_POST['name']??''); if($nm===''){ Session::flash('err','Nama wajib.'); } else {
$day = in_array($_POST['day']??'', $days, true) ? $_POST['day'] : null;
$time = ($_POST['time']??'') !== '' ? $_POST['time'] : null;
if(!empty($_POST['id'])) $db->prepare("UPDATE extracurriculars SET name=?,description=?,coach=?,`day`=?,`time`=?,schedule=? WHERE id=?")->execute([$nm,$_POST['description']??'',$_POST['coach']??'',$day,$time,$_POST['schedule']??'',(int)$_POST['id']]); else $db->prepare("INSERT INTO extracurriculars(name,description,coach,`day`,`time`,schedule) VALUES(?,?,?,?,?,?)")->execute([$nm,$_POST['description']??'',$_POST['coach']??'',$day,$time,$_POST['schedule']??'']);
Auth::log($db,'save','ekskul',"Simpan $nm"); Session::flash('ok','Disimpan.'); } }
header('Location: '.Helper::url('admin/ekskul')); exit; }
$eks=$db->query("SELECT * FROM extracurriculars ORDER BY id DESC")->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-futbol text-emerald-600 mr-1"></i>Ekstrakurikuler</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($eks) ?> ekskul</span>
<button id="btnAddEks" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Ekskul</button>
</div>
<form method="post" id="bulkEks"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="px-4 py-3 font-bold border-b">Daftar Ekstrakurikuler</div>
<div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b text-sm"><span id="selCountEks" class="text-slate-500">0 dipilih</span><button type="button" id="btnBulkEks" class="ml-auto bg-red-600 hover:bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[600px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-8"><input type="checkbox" id="checkAllEks"></th><th class="p-3 w-10">No</th><th class="p-3">Nama</th><th class="p-3">Pembina</th><th class="p-3">Hari</th><th class="p-3">Jam</th><th class="p-3">Tempat</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$eks): ?><tr><td colspan="8" class="p-10 text-center text-slate-500"><i class="fa fa-futbol text-3xl block mb-2"></i>Belum ada ekskul. Klik Tambah Ekskul.</td></tr><?php endif; ?>
<?php $noEks=1; foreach($eks as $e): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkEks" name="ids[]" value="<?= $e['id'] ?>" class="rowcheck rowcheckEks"></td><td class="p-3 text-slate-500"><?= $noEks++ ?></td><td class="p-3 font-semibold"><?= Helper::e($e['name']) ?><span class="block text-[11px] font-normal text-slate-400"><?= Helper::e(Helper::excerpt($e['description']??'',80)) ?></span></td>
<td class="p-3 text-sm"><?= Helper::e($e['coach']??'-') ?></td>
<td class="p-3 text-sm"><?= Helper::e($e['day']??'-') ?></td>
<td class="p-3 text-sm"><?= Helper::e($e['time'] ? date('H:i', strtotime($e['time'])) : '-') ?></td>
<td class="p-3 text-sm"><?= Helper::e($e['schedule']??'-') ?></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$e['id'],'name'=>$e['name'],'coach'=>$e['coach']??'','day'=>$e['day']??'','time'=>$e['time']??'','schedule'=>$e['schedule']??'','description'=>$e['description']??'']),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $e['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>

<div id="eksModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl my-4">
<div class="flex items-center gap-2 px-4 py-3 border-b bg-white"><h2 class="font-extrabold text-sm" id="eksTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Ekskul</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" data-loading class="p-4 grid gap-2.5 text-sm bg-white"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="e_id" value="0">
<label class="grid gap-1 font-semibold">Nama<input name="name" id="e_name" required placeholder="Pramuka" class="border rounded-lg p-2 font-normal"></label>
<div class="grid grid-cols-3 gap-2">
<label class="grid gap-1 font-semibold">Pembina<input name="coach" id="e_coach" placeholder="Nama pembina" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Hari<select name="day" id="e_day" class="border rounded-lg p-2 font-normal"><option value="">- Pilih hari -</option><?php foreach($days as $d): ?><option value="<?= $d ?>"><?= $d ?></option><?php endforeach; ?></select></label>
<label class="grid gap-1 font-semibold">Jam<input type="time" name="time" id="e_time" class="border rounded-lg p-2 font-normal"></label>
</div>
<label class="grid gap-1 font-semibold">Keterangan jadwal tambahan<input name="schedule" id="e_schedule" placeholder="Misal: Ruang 101" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Deskripsi<textarea name="description" id="e_desc" rows="3" placeholder="Deskripsi ekskul..." class="border rounded-lg p-2 font-normal"></textarea></label>
<div class="flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<script>
(function(){const ca=document.getElementById('checkAllEks'),sc=document.getElementById('selCountEks'),bb=document.getElementById('btnBulkEks'),bf=document.getElementById('bulkEks');if(!ca||!bb||!bf)return;const rows=()=>document.querySelectorAll('.rowcheckEks'),up=()=>{sc.textContent=document.querySelectorAll('.rowcheckEks:checked').length+' dipilih'};ca.addEventListener('change',()=>{rows().forEach(c=>c.checked=ca.checked);up()});document.addEventListener('change',e=>{if(e.target.classList&&e.target.classList.contains('rowcheckEks'))up()});bb.addEventListener('click',()=>{const n=document.querySelectorAll('.rowcheckEks:checked').length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 data.','warning');return}Swal.fire({title:'Hapus '+n+' data?',text:'Tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)bf.submit()})})})();
</script>
<script>
const eksModal=document.getElementById('eksModal');
function openEks(d){
  console.log('openEks called with:', d);
  document.getElementById('eksTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Ekskul':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Ekskul');
  document.getElementById('e_id').value=d?.id||0;
  document.getElementById('e_name').value=d?.name||'';
  document.getElementById('e_coach').value=d?.coach||'';
  document.getElementById('e_schedule').value=d?.schedule||'';
  document.getElementById('e_desc').value=d?.description||'';
  // format time for input[type="time"]
  let t=d?.time||''; if(t)t=t.substring(0,5);
  document.getElementById('e_time').value=t;
  eksModal.classList.remove('hidden');document.body.style.overflow='hidden';
  // set day AFTER modal is visible (fixes select value not sticking in hidden container)
  const dayVal=(d?.day||'').trim();
  console.log('Setting day to:', dayVal);
  const daySel=document.getElementById('e_day');
  console.log('Day select element:', daySel);
  console.log('Day select options:', daySel ? [...daySel.options].map(o=>o.value) : 'none');
  if(daySel && dayVal){
    daySel.value=dayVal;
    console.log('After setting, daySel.value =', daySel.value);
  }
}
function closeModal(){eksModal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAddEks').addEventListener('click',()=>openEks(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openEks(JSON.parse(b.dataset.row))));
document.querySelectorAll('#eksModal [data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>