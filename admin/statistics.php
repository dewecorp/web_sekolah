<?php declare(strict_types=1); Auth::requireRole(['administrator','editor']); $title='Statistik';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/statistics')); exit; }
  $act=$_POST['act']??'save';
  if($act==='bulk_delete'){ $ids=array_filter(array_map('intval',(array)($_POST['ids']??[]))); if(!$ids){ Session::flash('err','Pilih minimal 1 data.'); } else { $ph=implode(',',array_fill(0,count($ids),'?')); $db->prepare("DELETE FROM statistics WHERE id IN ($ph)")->execute(array_values($ids)); Auth::log($db,'delete','statistics','Hapus bulk statistik'); Session::flash('ok',count($ids).' data dihapus.'); } }
  elseif($act==='delete'){ $db->prepare("DELETE FROM statistics WHERE id=?")->execute([(int)$_POST['id']]); Session::flash('ok','Dihapus.'); }
  elseif($act==='toggle'){ $db->prepare("UPDATE statistics SET is_active=1-is_active WHERE id=?")->execute([(int)$_POST['id']]); Session::flash('ok','Status diubah.'); }
  else{
    $nm=trim($_POST['name']??'');
    if($nm===''){ Session::flash('err','Nama statistik wajib.'); }
    else{
      $val=(int)($_POST['value']??0); $suf=trim((string)($_POST['suffix']??'')); $icon=trim((string)($_POST['icon']??'fa-chart-simple'))?:'fa-chart-simple';
      $desc=trim((string)($_POST['description']??'')); $grad=trim((string)($_POST['gradient']??'from-emerald-500 to-teal-600'))?:'from-emerald-500 to-teal-600';
      $ord=(int)($_POST['sort_order']??0); $act2=(int)($_POST['is_active']??1);
      if(!empty($_POST['id'])) $db->prepare("UPDATE statistics SET name=?,value=?,suffix=?,icon=?,description=?,gradient=?,sort_order=?,is_active=? WHERE id=?")->execute([$nm,$val,$suf,$icon,$desc,$grad,$ord,$act2,(int)$_POST['id']]);
      else $db->prepare("INSERT INTO statistics(name,value,suffix,icon,description,gradient,sort_order,is_active) VALUES(?,?,?,?,?,?,?,?)")->execute([$nm,$val,$suf,$icon,$desc,$grad,$ord,$act2]);
      Auth::log($db,'save','statistics',"Simpan $nm"); Session::flash('ok','Disimpan.');
    }
  }
  header('Location: '.Helper::url('admin/statistics')); exit;
}
$rows=$db->query("SELECT * FROM statistics ORDER BY sort_order,id")->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-chart-simple text-emerald-600 mr-1"></i>Statistik</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($rows) ?> item</span>
<span class="text-[11px] bg-emerald-100 text-emerald-700 px-2.5 py-0.5 rounded-full font-bold">tampil di section Statistik home</span>
<button id="btnAdd" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Statistik</button>
</div>
<form method="post" id="bulkForm"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b text-sm"><span id="selCount" class="text-slate-500">0 dipilih</span><button type="button" id="btnBulk" class="ml-auto bg-red-600 hover:bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[640px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-8"><input type="checkbox" id="checkAll"></th><th class="p-3 w-10">No</th><th class="p-3">Nama Statistik</th><th class="p-3">Jumlah</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$rows): ?><tr><td colspan="5" class="p-10 text-center text-slate-500"><i class="fa fa-chart-simple text-3xl block mb-2"></i>Belum ada statistik. Klik Tambah Statistik.</td></tr><?php endif; ?>
<?php $no=1; foreach($rows as $r): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= $r['id'] ?>" class="rowcheck"></td><td class="p-3 text-slate-500"><?= $no++ ?></td>
<td class="p-3 font-semibold"><span class="flex items-center gap-2"><span class="w-8 h-8 rounded-lg bg-gradient-to-br <?= Helper::e($r['gradient']) ?> text-white grid place-items-center"><i class="fa <?= Helper::e($r['icon']) ?> text-xs"></i></span><span><?= Helper::e($r['name']) ?><span class="block text-[11px] font-normal text-slate-400"><?= Helper::e($r['description']??'') ?></span></span></span><?= $r['is_active']?'':' <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-200 text-slate-500">off</span>' ?></td>
<td class="p-3 font-extrabold text-lg"><?= number_format((int)$r['value']) ?><?= Helper::e($r['suffix']??'') ?></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$r['id'],'name'=>$r['name'],'value'=>(int)$r['value'],'suffix'=>$r['suffix']??'','icon'=>$r['icon']??'fa-chart-simple','description'=>$r['description']??'','gradient'=>$r['gradient']??'from-emerald-500 to-teal-600','sort_order'=>(int)$r['sort_order'],'is_active'=>(int)$r['is_active']]),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<form method="post" class="inline"><?= Security::csrfField() ?><input type="hidden" name="act" value="toggle"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-amber-600" title="Aktif/Nonaktif"><i class="fa fa-power-off text-xs"></i></button></form>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>
<div id="statModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full bg-white shadow-2xl my-4 mx-auto" style="max-width:620px;width:calc(100% - 2rem);overflow:visible;border-radius:1rem">
<div class="flex items-center gap-2 px-4 py-3 border-b bg-white" style="border-radius:1rem 1rem 0 0"><h2 class="font-extrabold text-sm" id="modalTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Statistik</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" data-loading class="p-3 grid gap-2 text-[13px] bg-white md:grid-cols-2" style="border-radius:0 0 1rem 1rem"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="f_id" value="0">
<label class="grid gap-1 font-semibold">Nama Statistik<input name="name" id="f_name" required placeholder="Siswa Aktif" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Deskripsi singkat<input name="description" id="f_desc" placeholder="Peserta didik tahun ini" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Jumlah<input type="number" name="value" id="f_val" value="0" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Akhiran (opsional)<input name="suffix" id="f_suf" placeholder="+, %, th" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Ikon (fa-...)<input name="icon" id="f_icon" value="fa-chart-simple" placeholder="fa-users" class="border rounded-lg p-2 font-normal font-mono text-xs"></label>
<label class="grid gap-1 font-semibold">Urutan<input type="number" name="sort_order" id="f_sort" value="0" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Gradasi warna<select name="gradient" id="f_grad" class="border rounded-lg p-2 font-normal">
<?php foreach(['from-emerald-500 to-teal-600'=>'Hijau','from-sky-500 to-indigo-600'=>'Biru','from-amber-500 to-orange-600'=>'Oranye','from-violet-500 to-fuchsia-600'=>'Ungu','from-rose-500 to-pink-600'=>'Merah muda','from-slate-600 to-slate-800'=>'Abu gelap'] as $gv=>$gl): ?><option value="<?= $gv ?>"><?= $gl ?></option><?php endforeach; ?>
</select></label>
<label class="grid gap-1 font-semibold">Status<select name="is_active" id="f_active" class="border rounded-lg p-2 font-normal"><option value="1">Aktif</option><option value="0">Nonaktif</option></select></label>
<div class="flex justify-center md:col-span-2"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>
<script>
(function(){const ca=document.getElementById('checkAll'),sc=document.getElementById('selCount'),bb=document.getElementById('btnBulk'),bf=document.getElementById('bulkForm');if(!ca||!bb||!bf)return;const up=()=>{sc.textContent=document.querySelectorAll('.rowcheck:checked').length+' dipilih'};ca.addEventListener('change',()=>{document.querySelectorAll('.rowcheck').forEach(c=>c.checked=ca.checked);up()});document.addEventListener('change',e=>{if(e.target.classList&&e.target.classList.contains('rowcheck'))up()});bb.addEventListener('click',()=>{const n=document.querySelectorAll('.rowcheck:checked').length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 data.','warning');return}Swal.fire({title:'Hapus '+n+' data?',text:'Tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)bf.submit()})});})();
</script>
<script>
const modal=document.getElementById('statModal');
function openModal(d){
  document.getElementById('modalTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Statistik':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Statistik');
  document.getElementById('f_id').value=d?.id||0;
  document.getElementById('f_name').value=d?.name||'';
  document.getElementById('f_val').value=d?.value??0;
  document.getElementById('f_suf').value=d?.suffix||'';
  document.getElementById('f_icon').value=d?.icon||'fa-chart-simple';
  document.getElementById('f_desc').value=d?.description||'';
  document.getElementById('f_grad').value=d?.gradient||'from-emerald-500 to-teal-600';
  document.getElementById('f_sort').value=d?.sort_order??0;
  document.getElementById('f_active').value=String(d?.is_active??1);
  if(document.getElementById('f_active')._cpaint)document.getElementById('f_active')._cpaint();
  if(document.getElementById('f_grad')._cpaint)document.getElementById('f_grad')._cpaint();
  modal.classList.remove('hidden');document.body.style.overflow='hidden';
}
function closeModal(){modal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAdd').addEventListener('click',()=>openModal(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openModal(JSON.parse(b.dataset.row))));
modal.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>
