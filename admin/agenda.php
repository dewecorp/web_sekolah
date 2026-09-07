<?php declare(strict_types=1); $title='Agenda'; $edit=null;
if(isset($_GET['edit'])){ $s=$db->prepare("SELECT * FROM agenda WHERE id=?"); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/agenda')); exit; }
$act=$_POST['act']??'save';
if($act==='bulk_delete'){ $ids=array_filter(array_map('intval',(array)($_POST['ids']??[]))); if(!$ids){ Session::flash('err','Pilih minimal 1 data.'); } else { $ph=implode(',',array_fill(0,count($ids),'?')); $db->prepare("DELETE FROM agenda WHERE id IN ($ph)")->execute(array_values($ids)); Auth::log($db,'delete','agenda','Hapus bulk agenda'); Session::flash('ok',count($ids).' data dihapus.'); } }
elseif($act==='delete'){ $db->prepare("DELETE FROM agenda WHERE id=?")->execute([(int)$_POST['id']]); Auth::log($db,'delete','agenda','Hapus agenda'); Session::flash('ok','Dihapus.'); }
else{ $t=trim($_POST['title']??''); if($t===''){ Session::flash('err','Judul wajib.'); } else {
$sd=$_POST['event_date']??date('Y-m-d'); $ed=trim($_POST['end_date']??''); if($ed===''||$ed<$sd)$ed=$sd;
if(!empty($_POST['id'])) $db->prepare("UPDATE agenda SET title=?,event_date=?,end_date=?,start_time=?,end_time=?,location=?,description=?,status=? WHERE id=?")->execute([$t,$sd,$ed,$_POST['start_time']??'',$_POST['end_time']??'',$_POST['location']??'',$_POST['description']??'',$_POST['status']??'published',(int)$_POST['id']]);
else $db->prepare("INSERT INTO agenda(title,event_date,end_date,start_time,end_time,location,description,status) VALUES(?,?,?,?,?,?,?,?)")->execute([$t,$sd,$ed,$_POST['start_time']??'',$_POST['end_time']??'',$_POST['location']??'',$_POST['description']??'',$_POST['status']??'published']);
Auth::log($db,'save','agenda',"Simpan $t"); Session::flash('ok','Disimpan.'); } }
header('Location: '.Helper::url('admin/agenda')); exit; }
$rows=$db->query("SELECT * FROM agenda ORDER BY event_date DESC LIMIT 100")->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-calendar-days text-emerald-600 mr-1"></i>Agenda</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($rows) ?> agenda</span>
<button id="btnAdd" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Agenda</button>
</div>
<form method="post" id="bulkForm"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b text-sm"><span id="selCount" class="text-slate-500">0 dipilih</span><button type="button" id="btnBulkAgenda" class="ml-auto bg-red-600 hover:bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[640px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-8"><input type="checkbox" id="checkAllAgenda"></th><th class="p-3 w-10">No</th><th class="p-3">Judul</th><th class="p-3">Tanggal</th><th class="p-3">Lokasi</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$rows): ?><tr><td colspan="6" class="p-10 text-center text-slate-500"><i class="fa fa-calendar-xmark text-3xl block mb-2"></i>Belum ada agenda. Klik Tambah Agenda.</td></tr><?php endif; ?>
<?php $no=1; foreach($rows as $r): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= $r['id'] ?>" class="rowcheck"></td><td class="p-3 text-slate-500"><?= $no++ ?></td><td class="p-3 font-semibold"><?= Helper::e($r['title']) ?><span class="block text-[11px] font-normal text-slate-400"><?= Helper::e(Helper::excerpt($r['description']??'',80)) ?></span></td>
<?php $fmtD=fn($d)=>$d?date('d-m-Y',strtotime($d)):''; ?>
<td class="p-3 text-xs whitespace-nowrap"><?= Helper::e($fmtD($r['event_date'])) ?><?= ($r['end_date']&&$r['end_date']!==$r['event_date'])?' – '.Helper::e($fmtD($r['end_date'])):'' ?><?= $r['start_time']?' <span class="text-slate-400">'.Helper::e(substr((string)$r['start_time'],0,5)).($r['end_time']?'-'.Helper::e(substr((string)$r['end_time'],0,5)):'').'</span>':'' ?></td>
<td class="p-3 text-xs text-slate-500"><?= Helper::e($r['location']??'') ?></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$r['id'],'title'=>$r['title'],'event_date'=>$r['event_date'],'end_date'=>$r['end_date']??$r['event_date'],'start_time'=>substr((string)($r['start_time']??''),0,5),'end_time'=>substr((string)($r['end_time']??''),0,5),'location'=>$r['location']??'','description'=>$r['description']??'','status'=>$r['status']]),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>

<div id="agendaModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl my-4">
<div class="flex items-center gap-2 px-4 py-3 border-b bg-white"><h2 class="font-extrabold text-sm" id="modalTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Agenda</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" data-loading class="p-4 grid gap-2.5 text-sm bg-white"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="f_id" value="0">
<label class="grid gap-1 font-semibold">Judul<input name="title" id="f_title" required placeholder="Rapat wali murid" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Tanggal Mulai<input type="date" name="event_date" id="f_date" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Tanggal Selesai<input type="date" name="end_date" id="f_end_date" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Mulai<input type="time" name="start_time" id="f_start" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Selesai<input type="time" name="end_time" id="f_end" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Lokasi<input name="location" id="f_loc" placeholder="Aula sekolah" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Deskripsi<textarea name="description" id="f_desc" rows="4" placeholder="Detail agenda..." class="border rounded-lg p-2 font-normal"></textarea></label>
<label class="grid gap-1 font-semibold">Status<select name="status" id="f_status" class="border rounded-lg p-2 font-normal"><option value="published">published</option><option value="draft">draft</option></select></label>
<div class="flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<script>
(function(){const ca=document.getElementById('checkAllAgenda'),sc=document.getElementById('selCount'),bb=document.getElementById('btnBulkAgenda'),bf=document.getElementById('bulkForm');if(!ca||!bb||!bf)return;const up=()=>{sc.textContent=document.querySelectorAll('.rowcheck:checked').length+' dipilih'};ca.addEventListener('change',()=>{document.querySelectorAll('.rowcheck').forEach(c=>c.checked=ca.checked);up()});document.addEventListener('change',e=>{if(e.target.classList&&e.target.classList.contains('rowcheck'))up()});bb.addEventListener('click',()=>{const n=document.querySelectorAll('.rowcheck:checked').length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 data.','warning');return}Swal.fire({title:'Hapus '+n+' data?',text:'Tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)bf.submit()})});})();
</script>
<script>
const modal=document.getElementById('agendaModal');
function openModal(d){
  document.getElementById('modalTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Agenda':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Agenda');
  document.getElementById('f_id').value=d?.id||0;
  document.getElementById('f_title').value=d?.title||'';
  document.getElementById('f_date').value=d?.event_date||'<?= date('Y-m-d') ?>';
  document.getElementById('f_end_date').value=d?.end_date||d?.event_date||'<?= date('Y-m-d') ?>';
  document.getElementById('f_start').value=d?.start_time||'';
  document.getElementById('f_end').value=d?.end_time||'';
  document.getElementById('f_loc').value=d?.location||'';
  document.getElementById('f_desc').value=d?.description||'';
  document.getElementById('f_status').value=d?.status||'published';
  modal.classList.remove('hidden');document.body.style.overflow='hidden';
}
function closeModal(){modal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAdd').addEventListener('click',()=>openModal(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openModal(JSON.parse(b.dataset.row))));
modal.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
<?php if($edit): ?>openModal(<?= json_encode(['id'=>$edit['id'],'title'=>$edit['title'],'event_date'=>$edit['event_date'],'start_time'=>substr((string)($edit['start_time']??''),0,5),'end_time'=>substr((string)($edit['end_time']??''),0,5),'location'=>$edit['location']??'','description'=>$edit['description']??'','status'=>$edit['status']]) ?>);<?php endif; ?>
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>




