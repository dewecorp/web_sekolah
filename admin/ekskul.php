<?php declare(strict_types=1); $title='Ekstrakurikuler';
$days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/ekskul')); exit; }
$act=$_POST['act']??'';
if($act==='meta'){ foreach(['ekskul_title','ekskul_desc','ekskul_show','ekskul_cols'] as $k){ if(!array_key_exists($k,$_POST['s']??[])) continue; $v=trim((string)$_POST['s'][$k]); if($k==='ekskul_show') $v=$v==='1'?'1':'0'; $db->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")->execute([$k,$v]); } Session::flash('ok','Pengaturan ekskul disimpan.'); header('Location: '.Helper::url('admin/ekskul')); exit; }
if($act==='bulk_delete'){ $ids=array_filter(array_map('intval',(array)($_POST['ids']??[]))); if(!$ids){ Session::flash('err','Pilih minimal 1 data.'); } else { $ph=implode(',',array_fill(0,count($ids),'?')); $db->prepare("DELETE FROM extracurriculars WHERE id IN ($ph)")->execute(array_values($ids)); Auth::log($db,'delete','ekskul','Hapus bulk ekskul'); Session::flash('ok',count($ids).' data dihapus.'); } }
elseif($act==='delete'){ $db->prepare("DELETE FROM extracurriculars WHERE id=?")->execute([(int)$_POST['id']]); Session::flash('ok','Dihapus.'); }
else{ $nm=trim($_POST['name']??''); if($nm===''){ Session::flash('err','Nama wajib.'); } else {
$day = in_array($_POST['day']??'', $days, true) ? $_POST['day'] : null;
$time = ($_POST['time']??'') !== '' ? $_POST['time'] : null;
if(!empty($_POST['id'])) $db->prepare("UPDATE extracurriculars SET name=?,description=?,coach=?,`day`=?,`time`=?,schedule=? WHERE id=?")->execute([$nm,$_POST['description']??'',$_POST['coach']??'',$day,$time,$_POST['schedule']??'',(int)$_POST['id']]); else $db->prepare("INSERT INTO extracurriculars(name,description,coach,`day`,`time`,schedule) VALUES(?,?,?,?,?,?)")->execute([$nm,$_POST['description']??'',$_POST['coach']??'',$day,$time,$_POST['schedule']??'']);
Auth::log($db,'save','ekskul',"Simpan $nm"); Session::flash('ok','Disimpan.'); } }
header('Location: '.Helper::url('admin/ekskul')); exit; }
$eks=$db->query("SELECT * FROM extracurriculars ORDER BY id DESC")->fetchAll();
$guruList=$db->query("SELECT name FROM teachers WHERE is_active=1 ORDER BY name")->fetchAll(PDO::FETCH_COLUMN) ?: [];
$guruList=array_values(array_unique(array_filter(array_map('trim',$guruList))));
$ekSets=[]; foreach($db->query("SELECT `key`,`value` FROM settings WHERE `key` IN ('ekskul_title','ekskul_desc','ekskul_show','ekskul_cols')") as $r) $ekSets[$r['key']]=$r['value'];
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-futbol text-emerald-600 mr-1"></i>Ekstrakurikuler</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($eks) ?> ekskul</span>
<a href="<?= Helper::url('ekstrakurikuler') ?>" target="_blank" rel="noopener noreferrer" class="text-sm px-3 py-1.5 border rounded-lg bg-white"><i class="fa fa-eye mr-1"></i>Lihat Public</a>
<button id="btnAddEks" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Ekskul</button>
</div>
<form method="post" data-loading class="bg-white rounded-2xl border p-4 grid md:grid-cols-4 gap-2 text-sm mb-3"><?= Security::csrfField() ?>
<input type="hidden" name="act" value="meta">
<label class="grid gap-1">Judul public<input name="s[ekskul_title]" value="<?= Helper::e($ekSets['ekskul_title']??'Ekstrakurikuler') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Tampil di public<select name="s[ekskul_show]" class="border rounded-lg p-2"><option value="1" <?= ($ekSets['ekskul_show']??'1')==='1'?'selected':'' ?>>Tampilkan</option><option value="0" <?= ($ekSets['ekskul_show']??'1')==='0'?'selected':'' ?>>Sembunyikan (404)</option></select></label>
<label class="grid gap-1">Deskripsi singkat<input name="s[ekskul_desc]" value="<?= Helper::e($ekSets['ekskul_desc']??'') ?>" placeholder="Ringkasan singkat" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Kolom<select name="s[ekskul_cols]" class="border rounded-lg p-2"><option value="2" <?= ($ekSets['ekskul_cols']??'3')==='2'?'selected':'' ?>>2 kolom</option><option value="3" <?= ($ekSets['ekskul_cols']??'3')==='3'?'selected':'' ?>>3 kolom</option><option value="4" <?= ($ekSets['ekskul_cols']??'3')==='4'?'selected':'' ?>>4 kolom</option></select></label>
<button class="md:col-span-4 bg-slate-800 hover:bg-slate-700 text-white rounded-xl py-2 font-bold text-sm"><i class="fa fa-floppy-disk mr-1"></i>Simpan Pengaturan</button>
</form>
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
<label class="grid gap-1 font-semibold">Pembina<select name="coach" id="e_coach" class="border rounded-lg p-2 font-normal swal2-select" style="width:100%"><option value="">- Pilih pembina -</option><?php foreach($guruList as $g): ?><option value="<?= Helper::e($g) ?>"><?= Helper::e($g) ?></option><?php endforeach; ?></select></label>
<label class="grid gap-1 font-semibold">Hari<select name="day" id="e_day" class="border rounded-lg p-2 font-normal"><option value="">- Pilih hari -</option><?php foreach($days as $d): ?><option value="<?= $d ?>"><?= $d ?></option><?php endforeach; ?></select></label>
<label class="grid gap-1 font-semibold">Jam<input type="time" name="time" id="e_time" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Keterangan tambahan / lokasi<input name="schedule" id="e_schedule" placeholder="Misal: Ruang 101" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Deskripsi<textarea name="description" id="e_desc" rows="3" placeholder="Deskripsi ekskul..." class="border rounded-lg p-2 font-normal"></textarea></label>
<div class="flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<script>
(function(){const ca=document.getElementById('checkAllEks'),sc=document.getElementById('selCountEks'),bb=document.getElementById('btnBulkEks'),bf=document.getElementById('bulkEks');if(!ca||!bb||!bf)return;const rows=()=>document.querySelectorAll('.rowcheckEks'),up=()=>{sc.textContent=document.querySelectorAll('.rowcheckEks:checked').length+' dipilih'};ca.addEventListener('change',()=>{rows().forEach(c=>c.checked=ca.checked);up()});document.addEventListener('change',e=>{if(e.target.classList&&e.target.classList.contains('rowcheckEks'))up()});bb.addEventListener('click',()=>{const n=document.querySelectorAll('.rowcheckEks:checked').length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 data.','warning');return}Swal.fire({title:'Hapus '+n+' data?',text:'Tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)bf.submit()})})})();
</script>
<script>
const eksModal=document.getElementById('eksModal');
function openEks(d){
  document.getElementById('eksTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Ekskul':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Ekskul');
  document.getElementById('e_id').value=d?.id||0;
  document.getElementById('e_name').value=d?.name||'';
  document.getElementById('e_schedule').value=d?.schedule||'';
  document.getElementById('e_desc').value=d?.description||'';
  let t=d?.time||''; if(t)t=t.substring(0,5);
  document.getElementById('e_time').value=t;
  eksModal.classList.remove('hidden');document.body.style.overflow='hidden';
  const coachVal=(d?.coach||'').trim();
  const coachSel=$('#e_coach');
  if(coachSel.find('option[value="'+coachVal+'"]').length===0&&coachVal!==''){
    coachSel.append(new Option(coachVal,coachVal,true,true));
  }
  coachSel.val(coachVal).trigger('change');
  const daySel=document.getElementById('e_day');
  if(daySel){
    daySel.value=(d?.day||'').trim();
    daySel.dispatchEvent(new Event('change',{bubbles:true}));
    if(typeof daySel._csync==='function')daySel._csync();
    if(typeof daySel._cpaint==='function')daySel._cpaint();
    if(window.__refreshSelects&&typeof window.__refreshSelects['e_day']==='function')window.__refreshSelects['e_day']();
  }
}
function closeModal(){eksModal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAddEks').addEventListener('click',()=>openEks(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openEks(JSON.parse(b.dataset.row))));
document.querySelectorAll('#eksModal [data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
</script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
#eksModal .select2-container .select2-selection--single{height:40px!important;border:1px solid #e2e8f0!important;border-radius:.65rem!important;box-shadow:0 1px 2px rgba(15,23,42,.06),0 4px 12px rgba(15,23,42,.04)!important;display:flex!important;align-items:center}
#eksModal .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:38px!important;padding-left:.75rem!important;padding-right:2rem!important;color:#0f172a!important;font-weight:400}
#eksModal .select2-container--default .select2-selection--single .select2-selection__placeholder{color:#94a3b8!important}
#eksModal .select2-container--default .select2-selection--single .select2-selection__arrow{height:38px!important;right:.5rem!important}
#eksModal .select2-container--default.select2-container--focus .select2-selection--single{border-color:#10b981!important;box-shadow:0 0 0 3px rgba(16,185,129,.18),0 4px 14px rgba(16,185,129,.12)!important;outline:none!important}
#eksModal .select2-container .select2-selection--single .select2-selection__clear{margin-right:1.4rem!important}
</style>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function(){
  $('#e_coach').select2({placeholder:'- Pilih pembina -',allowClear:true,width:'100%',dropdownParent:$('#eksModal')});
});
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>