<?php declare(strict_types=1); $title='Prestasi';
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/prestasi')); exit; }
$act=$_POST['act']??'';
if($act==='bulk_delete'){ $ids=array_filter(array_map('intval',(array)($_POST['ids']??[]))); if(!$ids){ Session::flash('err','Pilih minimal 1 data.'); } else { $ph=implode(',',array_fill(0,count($ids),'?')); $db->prepare("DELETE FROM achievements WHERE id IN ($ph)")->execute(array_values($ids)); Auth::log($db,'delete','prestasi','Hapus bulk prestasi'); Session::flash('ok',count($ids).' data dihapus.'); } }
elseif($act==='delete'){ $db->prepare("DELETE FROM achievements WHERE id=?")->execute([(int)$_POST['id']]); Session::flash('ok','Dihapus.'); }
else{ $nm=trim($_POST['title']??''); if($nm===''){ Session::flash('err','Judul wajib.'); } else {
if(!empty($_POST['id'])) $db->prepare("UPDATE achievements SET title=?,description=?,`year`=?,`level`=? WHERE id=?")->execute([$nm,$_POST['description']??'',$_POST['year']??'',$_POST['level']??'',(int)$_POST['id']]); else $db->prepare("INSERT INTO achievements(title,description,`year`,`level`) VALUES(?,?,?,?)")->execute([$nm,$_POST['description']??'',$_POST['year']??'',$_POST['level']??'']);
Auth::log($db,'save','prestasi',"Simpan $nm"); Session::flash('ok','Disimpan.'); } }
header('Location: '.Helper::url('admin/prestasi')); exit; }
$pres=$db->query("SELECT * FROM achievements ORDER BY id DESC")->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-trophy text-emerald-600 mr-1"></i>Prestasi</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($pres) ?> prestasi</span>
<button id="btnAddPres" class="ml-auto bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-trophy mr-1"></i>Tambah Prestasi</button>
</div>
<form method="post" id="bulkPres"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="px-4 py-3 font-bold border-b">Daftar Prestasi</div>
<div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b text-sm"><span id="selCountPres" class="text-slate-500">0 dipilih</span><button type="button" id="btnBulkPres" class="ml-auto bg-red-600 hover:bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[420px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-8"><input type="checkbox" id="checkAllPres"></th><th class="p-3 w-10">No</th><th class="p-3">Judul</th><th class="p-3">Tahun</th><th class="p-3">Tingkat</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$pres): ?><tr><td colspan="6" class="p-10 text-center text-slate-500"><i class="fa fa-trophy text-3xl block mb-2"></i>Belum ada prestasi. Klik Tambah Prestasi.</td></tr><?php endif; ?>
<?php $noPres=1; foreach($pres as $e): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkPres" name="ids[]" value="<?= $e['id'] ?>" class="rowcheck rowcheckPres"></td><td class="p-3 text-slate-500"><?= $noPres++ ?></td><td class="p-3 font-semibold"><?= Helper::e($e['title']) ?><span class="block text-[11px] font-normal text-slate-400"><?= Helper::e(Helper::excerpt($e['description']??'',80)) ?></span></td>
<td class="p-3 text-xs text-slate-500"><?= Helper::e($e['year']??'') ?></td><td class="p-3 text-xs text-slate-500"><?= Helper::e($e['level']??'') ?></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$e['id'],'title'=>$e['title'],'year'=>$e['year']??'','level'=>$e['level']??'','description'=>$e['description']??'']),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $e['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>

<div id="presModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl my-4">
<div class="flex items-center gap-2 px-4 py-3 border-b bg-white"><h2 class="font-extrabold text-sm" id="presTitle"><i class="fa fa-trophy text-emerald-600 mr-1"></i>Tambah Prestasi</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" data-loading class="p-4 grid gap-2.5 text-sm bg-white"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="p_id" value="0">
<label class="grid gap-1 font-semibold">Judul<input name="title" id="p_title" required placeholder="Juara 1 OSN" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Tahun<input name="year" id="p_year" placeholder="2024" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Tingkat<select name="level" id="p_level" class="border rounded-lg p-2 font-normal"><option value="">- Pilih tingkat -</option><?php foreach(['Kecamatan','Kabupaten','Provinsi','Nasional'] as $lv): ?><option value="<?= $lv ?>"><?= $lv ?></option><?php endforeach; ?></select></label>
<label class="grid gap-1 font-semibold">Deskripsi<textarea name="description" id="p_desc" rows="3" placeholder="Deskripsi prestasi..." class="border rounded-lg p-2 font-normal"></textarea></label>
<div class="flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<script>
(function(){const ca=document.getElementById('checkAllPres'),sc=document.getElementById('selCountPres'),bb=document.getElementById('btnBulkPres'),bf=document.getElementById('bulkPres');if(!ca||!bb||!bf)return;const rows=()=>document.querySelectorAll('.rowcheckPres'),up=()=>{sc.textContent=document.querySelectorAll('.rowcheckPres:checked').length+' dipilih'};ca.addEventListener('change',()=>{rows().forEach(c=>c.checked=ca.checked);up()});document.addEventListener('change',e=>{if(e.target.classList&&e.target.classList.contains('rowcheckPres'))up()});bb.addEventListener('click',()=>{const n=document.querySelectorAll('.rowcheckPres:checked').length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 data.','warning');return}Swal.fire({title:'Hapus '+n+' data?',text:'Tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)bf.submit()})})})();
</script>
<script>
const presModal=document.getElementById('presModal');
function openPres(d){
  document.getElementById('presTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Prestasi':'<i class="fa fa-trophy text-emerald-600 mr-1"></i>Tambah Prestasi');
  document.getElementById('p_id').value=d?.id||0;
  document.getElementById('p_title').value=d?.title||'';
  document.getElementById('p_year').value=d?.year||'';
  document.getElementById('p_desc').value=d?.description||'';
  presModal.classList.remove('hidden');document.body.style.overflow='hidden';
  const lvSel=document.getElementById('p_level');
  if(lvSel){
    lvSel.value=(d?.level||'').trim();
    lvSel.dispatchEvent(new Event('change',{bubbles:true}));
    if(typeof lvSel._csync==='function')lvSel._csync();
    if(typeof lvSel._cpaint==='function')lvSel._cpaint();
    if(window.__refreshSelects&&typeof window.__refreshSelects['p_level']==='function')window.__refreshSelects['p_level']();
  }
}
function closeModal(){presModal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAddPres').addEventListener('click',()=>openPres(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openPres(JSON.parse(b.dataset.row))));
document.querySelectorAll('#presModal [data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>