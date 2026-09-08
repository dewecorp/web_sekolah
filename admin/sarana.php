<?php declare(strict_types=1); $title='Sarana & Infrastruktur';
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/sarana')); exit; }
$act=$_POST['act']??'save';
if($act==='meta'){ foreach(['sarana_title','sarana_desc','sarana_show'] as $k){ if(!array_key_exists($k,$_POST['s']??[])) continue; $v=trim((string)$_POST['s'][$k]); if($k==='sarana_show') $v=$v==='1'?'1':'0'; $db->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")->execute([$k,$v]); } Session::flash('ok','Pengaturan disimpan.'); }
elseif($act==='bulk_delete'){ $ids=array_filter(array_map('intval',(array)($_POST['ids']??[]))); if(!$ids){ Session::flash('err','Pilih minimal 1 data.'); } else { $ph=implode(',',array_fill(0,count($ids),'?')); $db->prepare("DELETE FROM facilities WHERE id IN ($ph)")->execute(array_values($ids)); Session::flash('ok',count($ids).' data dihapus.'); } }
elseif($act==='delete'){ $db->prepare("DELETE FROM facilities WHERE id=?")->execute([(int)$_POST['id']]); Session::flash('ok','Dihapus.'); }
else{ $nm=trim($_POST['name']??''); if($nm===''){ Session::flash('err','Nama sarana wajib.'); } else {
$qty=trim((string)($_POST['qty']??'')); $unit=trim((string)($_POST['unit']??'')); $cond=in_array($_POST['cond']??'baik',['baik','rusak'],true)?$_POST['cond']:'baik'; $so=(int)($_POST['sort_order']??0);
if(!empty($_POST['id'])) $db->prepare("UPDATE facilities SET name=?,qty=?,unit=?,cond=?,sort_order=? WHERE id=?")->execute([$nm,$qty,$unit,$cond,$so,(int)$_POST['id']]); else $db->prepare("INSERT INTO facilities(name,qty,unit,cond,sort_order) VALUES(?,?,?,?,?)")->execute([$nm,$qty,$unit,$cond,$so]);
Session::flash('ok','Disimpan.'); } }
header('Location: '.Helper::url('admin/sarana')); exit; }
$sets=[]; foreach($db->query("SELECT `key`,`value` FROM settings WHERE `key` IN ('sarana_title','sarana_desc','sarana_show')") as $r) $sets[$r['key']]=$r['value'];
$rows=$db->query("SELECT * FROM facilities ORDER BY sort_order,id")->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-building-columns text-emerald-600 mr-1"></i>Sarana & Infrastruktur</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($rows) ?> sarana</span>
<a href="<?= Helper::url('sarana') ?>" target="_blank" rel="noopener noreferrer" class="text-sm px-3 py-1.5 border rounded-lg bg-white"><i class="fa fa-eye mr-1"></i>Lihat Public</a>
<button id="btnAdd" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Sarana</button>
</div>
<form method="post" data-loading class="bg-white rounded-2xl border p-4 grid md:grid-cols-4 gap-2 text-sm mb-3"><?= Security::csrfField() ?>
<input type="hidden" name="act" value="meta">
<label class="grid gap-1">Judul public<input name="s[sarana_title]" value="<?= Helper::e($sets['sarana_title']??'Sarana & Infrastruktur') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Tampil di public<select name="s[sarana_show]" class="border rounded-lg p-2"><option value="1" <?= ($sets['sarana_show']??'1')==='1'?'selected':'' ?>>Tampilkan</option><option value="0" <?= ($sets['sarana_show']??'1')==='0'?'selected':'' ?>>Sembunyikan (404)</option></select></label>
<label class="grid gap-1 md:col-span-2">Deskripsi singkat<input name="s[sarana_desc]" value="<?= Helper::e($sets['sarana_desc']??'') ?>" placeholder="Ringkasan singkat" class="border rounded-lg p-2"></label>
<button class="md:col-span-4 bg-slate-800 hover:bg-slate-700 text-white rounded-xl py-2 font-bold text-sm"><i class="fa fa-floppy-disk mr-1"></i>Simpan Pengaturan</button>
</form>
<form method="post" id="bulkForm"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="px-4 py-3 font-bold border-b">Daftar Sarana</div>
<div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b text-sm"><span id="selCount" class="text-slate-500">0 dipilih</span><button type="button" id="btnBulk" class="ml-auto bg-red-600 hover:bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[680px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-8"><input type="checkbox" id="checkAll"></th><th class="p-3 w-10">No</th><th class="p-3">Nama Sarana</th><th class="p-3 text-center">Luas / Jumlah</th><th class="p-3 text-center">Satuan</th><th class="p-3 text-center">Kondisi</th><th class="p-3 text-right">Aksi</th></tr>
<?php $no=1; foreach($rows as $r): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= $r['id'] ?>" class="rowcheck"></td><td class="p-3 text-slate-500"><?= $no++ ?></td>
<td class="p-3 font-extrabold"><?= Helper::e($r['name']) ?></td>
<td class="p-3 text-center font-bold"><?= Helper::e($r['qty']??'-') ?></td>
<td class="p-3 text-center"><?= Helper::e($r['unit']??'-') ?></td>
<td class="p-3 text-center"><span class="text-xs font-bold px-2.5 py-1 rounded-full <?= ($r['cond']??'baik')==='baik'?'bg-emerald-100 text-emerald-700':'bg-red-100 text-red-700' ?>"><i class="fa <?= ($r['cond']??'baik')==='baik'?'fa-circle-check':'fa-triangle-exclamation' ?> mr-1"></i><?= ($r['cond']??'baik')==='baik'?'Baik':'Rusak' ?></span></td>
<td class="p-3"><span class="flex gap-1 justify-end"><button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$r['id'],'name'=>$r['name'],'qty'=>$r['qty']??'','unit'=>$r['unit']??'','cond'=>$r['cond']??'baik','sort_order'=>$r['sort_order']]),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button><form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form></span></td></tr>
<?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="7" class="p-10 text-center text-slate-500"><i class="fa fa-building-columns text-3xl block mb-2"></i>Belum ada sarana. Klik Tambah Sarana.</td></tr><?php endif; ?>
</table></div></div>
<div class="bg-slate-50 border rounded-2xl px-4 py-2.5 mt-3 text-xs text-slate-500">Slug public: <code class="font-mono bg-white px-1.5 py-0.5 rounded border font-bold text-emerald-700">/sarana</code></div>
<div id="sarModal" class="hidden fixed inset-0 z-50 overflow-y-auto"><div class="fixed inset-0 bg-slate-900/60" data-close></div><div class="relative min-h-full flex items-start justify-center p-3 sm:p-6"><div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl my-4">
<div class="flex items-center gap-2 px-4 py-3 border-b bg-white"><h2 class="font-extrabold text-sm" id="sarTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Sarana</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" data-loading class="p-4 grid gap-2.5 text-sm bg-white"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="f_id" value="0">
<label class="grid gap-1 font-semibold">Nama sarana<input name="name" id="f_name" required placeholder="cth: Ruang Kelas" class="border rounded-lg p-2 font-normal"></label>
<div class="grid grid-cols-2 gap-2">
<label class="grid gap-1 font-semibold">Luas / Jumlah<input name="qty" id="f_qty" placeholder="cth: 120 / 32" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Satuan<input name="unit" id="f_unit" placeholder="cth: m2 / unit / ruang" class="border rounded-lg p-2 font-normal"></label>
</div>
<div class="grid grid-cols-2 gap-2">
<label class="grid gap-1 font-semibold">Kondisi<select name="cond" id="f_cond" class="border rounded-lg p-2 font-normal"><option value="baik">Baik</option><option value="rusak">Rusak</option></select></label>
<label class="grid gap-1 font-semibold">Urutan<input type="number" name="sort_order" id="f_sort" value="0" class="border rounded-lg p-2 font-normal"></label>
</div>
<div class="flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>
<script>
(function(){const ca=document.getElementById('checkAll'),sc=document.getElementById('selCount'),bb=document.getElementById('btnBulk'),bf=document.getElementById('bulkForm');if(!ca||!bb||!bf)return;const up=()=>{sc.textContent=document.querySelectorAll('.rowcheck:checked').length+' dipilih'};ca.addEventListener('change',()=>{document.querySelectorAll('.rowcheck').forEach(c=>c.checked=ca.checked);up()});document.addEventListener('change',e=>{if(e.target.classList&&e.target.classList.contains('rowcheck'))up()});bb.addEventListener('click',()=>{const n=document.querySelectorAll('.rowcheck:checked').length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 data.','warning');return}Swal.fire({title:'Hapus '+n+' data?',text:'Tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)bf.submit()})});})();
</script>
<script>
const modal=document.getElementById('sarModal');
const syncSel=(id,val)=>{const sel=document.getElementById(id);if(!sel)return;sel.value=val??'';sel.dispatchEvent(new Event('change',{bubbles:true}));if(typeof sel._csync==='function')sel._csync();if(typeof sel._cpaint==='function')sel._cpaint();if(window.__refreshSelects&&typeof window.__refreshSelects[id]==='function')window.__refreshSelects[id]()};
function openModal(d){
  document.getElementById('sarTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Sarana':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Sarana');
  document.getElementById('f_id').value=d?.id||0;document.getElementById('f_name').value=d?.name||'';document.getElementById('f_qty').value=d?.qty||'';document.getElementById('f_unit').value=d?.unit||'';document.getElementById('f_sort').value=d?.sort_order??0;
  modal.classList.remove('hidden');document.body.style.overflow='hidden';
  syncSel('f_cond',d?.cond||'baik');
}
function closeModal(){modal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAdd').addEventListener('click',()=>openModal(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openModal(JSON.parse(b.dataset.row))));
modal.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>
