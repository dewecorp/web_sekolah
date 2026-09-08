<?php declare(strict_types=1); $title='Sarana Pembelajaran';
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/sarana-pembelajaran')); exit; }
$act=$_POST['act']??'save';
if($act==='meta'){ foreach(['learn_title','learn_desc','learn_show'] as $k){ if(!array_key_exists($k,$_POST['s']??[])) continue; $v=trim((string)$_POST['s'][$k]); if($k==='learn_show') $v=$v==='1'?'1':'0'; $db->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")->execute([$k,$v]); } Session::flash('ok','Pengaturan disimpan.'); }
elseif($act==='bulk_delete'){ $ids=array_filter(array_map('intval',(array)($_POST['ids']??[]))); if(!$ids){ Session::flash('err','Pilih minimal 1 data.'); } else { $ph=implode(',',array_fill(0,count($ids),'?')); $db->prepare("DELETE FROM learning_facilities WHERE id IN ($ph)")->execute(array_values($ids)); Session::flash('ok',count($ids).' data dihapus.'); } }
elseif($act==='delete'){ $db->prepare("DELETE FROM learning_facilities WHERE id=?")->execute([(int)$_POST['id']]); Session::flash('ok','Dihapus.'); }
else{ $nm=trim($_POST['name']??''); if($nm===''){ Session::flash('err','Nama barang wajib.'); } else {
$g=max(0,(int)($_POST['good_qty']??0)); $m=max(0,(int)($_POST['mid_qty']??0)); $b=max(0,(int)($_POST['bad_qty']??0)); $tot=$g+$m+$b; $so=(int)($_POST['sort_order']??0);
if(!empty($_POST['id'])) $db->prepare("UPDATE learning_facilities SET name=?,good_qty=?,mid_qty=?,bad_qty=?,total=?,sort_order=? WHERE id=?")->execute([$nm,$g,$m,$b,$tot,$so,(int)$_POST['id']]); else $db->prepare("INSERT INTO learning_facilities(name,good_qty,mid_qty,bad_qty,total,sort_order) VALUES(?,?,?,?,?,?)")->execute([$nm,$g,$m,$b,$tot,$so]);
Session::flash('ok','Disimpan.'); } }
header('Location: '.Helper::url('admin/sarana-pembelajaran')); exit; }
$sets=[]; foreach($db->query("SELECT `key`,`value` FROM settings WHERE `key` IN ('learn_title','learn_desc','learn_show')") as $r) $sets[$r['key']]=$r['value'];
$rows=$db->query("SELECT * FROM learning_facilities ORDER BY sort_order,id")->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-chalkboard text-emerald-600 mr-1"></i>Sarana Pembelajaran</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($rows) ?> barang</span>
<a href="<?= Helper::url('sarana-pembelajaran') ?>" target="_blank" rel="noopener noreferrer" class="text-sm px-3 py-1.5 border rounded-lg bg-white"><i class="fa fa-eye mr-1"></i>Lihat Public</a>
<button id="btnAdd" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Barang</button>
</div>
<form method="post" data-loading class="bg-white rounded-2xl border p-4 grid md:grid-cols-4 gap-2 text-sm mb-3"><?= Security::csrfField() ?>
<input type="hidden" name="act" value="meta">
<label class="grid gap-1">Judul public<input name="s[learn_title]" value="<?= Helper::e($sets['learn_title']??'Sarana Pembelajaran') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Tampil di public<select name="s[learn_show]" class="border rounded-lg p-2"><option value="1" <?= ($sets['learn_show']??'1')==='1'?'selected':'' ?>>Tampilkan</option><option value="0" <?= ($sets['learn_show']??'1')==='0'?'selected':'' ?>>Sembunyikan (404)</option></select></label>
<label class="grid gap-1 md:col-span-2">Deskripsi singkat<input name="s[learn_desc]" value="<?= Helper::e($sets['learn_desc']??'') ?>" placeholder="Ringkasan singkat" class="border rounded-lg p-2"></label>
<button class="md:col-span-4 bg-slate-800 hover:bg-slate-700 text-white rounded-xl py-2 font-bold text-sm"><i class="fa fa-floppy-disk mr-1"></i>Simpan Pengaturan</button>
</form>
<form method="post" id="bulkForm"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="px-4 py-3 font-bold border-b">Daftar Barang</div>
<div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b text-sm"><span id="selCount" class="text-slate-500">0 dipilih</span><button type="button" id="btnBulk" class="ml-auto bg-red-600 hover:bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[720px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-8"><input type="checkbox" id="checkAll"></th><th class="p-3 w-10">No</th><th class="p-3">Nama Barang</th><th class="p-3 text-center">Baik</th><th class="p-3 text-center">Sedang</th><th class="p-3 text-center">Rusak</th><th class="p-3 text-center">Jumlah</th><th class="p-3 text-right">Aksi</th></tr>
<?php $no=1; foreach($rows as $r): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= $r['id'] ?>" class="rowcheck"></td><td class="p-3 text-slate-500"><?= $no++ ?></td>
<td class="p-3 font-extrabold"><?= Helper::e($r['name']) ?></td>
<td class="p-3 text-center font-bold text-emerald-700"><?= (int)$r['good_qty'] ?></td>
<td class="p-3 text-center font-bold text-amber-700"><?= (int)$r['mid_qty'] ?></td>
<td class="p-3 text-center font-bold text-red-700"><?= (int)$r['bad_qty'] ?></td>
<td class="p-3 text-center font-extrabold"><?= (int)$r['total'] ?></td>
<td class="p-3"><span class="flex gap-1 justify-end"><button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$r['id'],'name'=>$r['name'],'good_qty'=>(int)$r['good_qty'],'mid_qty'=>(int)$r['mid_qty'],'bad_qty'=>(int)$r['bad_qty'],'sort_order'=>$r['sort_order']]),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button><form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form></span></td></tr>
<?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="8" class="p-10 text-center text-slate-500"><i class="fa fa-chalkboard text-3xl block mb-2"></i>Belum ada barang. Klik Tambah Barang.</td></tr><?php endif; ?>
</table></div></div>
<div class="bg-slate-50 border rounded-2xl px-4 py-2.5 mt-3 text-xs text-slate-500">Slug public: <code class="font-mono bg-white px-1.5 py-0.5 rounded border font-bold text-emerald-700">/sarana-pembelajaran</code></div>
<div id="learnModal" class="hidden fixed inset-0 z-50 overflow-y-auto"><div class="fixed inset-0 bg-slate-900/60" data-close></div><div class="relative min-h-full flex items-start justify-center p-3 sm:p-6"><div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl my-4">
<div class="flex items-center gap-2 px-4 py-3 border-b bg-white"><h2 class="font-extrabold text-sm" id="learnTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Barang</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" data-loading class="p-4 grid gap-2.5 text-sm bg-white"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="f_id" value="0">
<label class="grid gap-1 font-semibold">Nama barang<input name="name" id="f_name" required placeholder="cth: Meja Siswa" class="border rounded-lg p-2 font-normal"></label>
<div class="grid grid-cols-3 gap-2">
<label class="grid gap-1 font-semibold">Baik<input type="number" name="good_qty" id="f_good" min="0" value="0" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Sedang<input type="number" name="mid_qty" id="f_mid" min="0" value="0" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Rusak<input type="number" name="bad_qty" id="f_bad" min="0" value="0" class="border rounded-lg p-2 font-normal"></label>
</div>
<label class="grid gap-1 font-semibold">Urutan<input type="number" name="sort_order" id="f_sort" value="0" class="border rounded-lg p-2 font-normal"></label>
<div class="flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>
<script>
(function(){const ca=document.getElementById('checkAll'),sc=document.getElementById('selCount'),bb=document.getElementById('btnBulk'),bf=document.getElementById('bulkForm');if(!ca||!bb||!bf)return;const up=()=>{sc.textContent=document.querySelectorAll('.rowcheck:checked').length+' dipilih'};ca.addEventListener('change',()=>{document.querySelectorAll('.rowcheck').forEach(c=>c.checked=ca.checked);up()});document.addEventListener('change',e=>{if(e.target.classList&&e.target.classList.contains('rowcheck'))up()});bb.addEventListener('click',()=>{const n=document.querySelectorAll('.rowcheck:checked').length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 data.','warning');return}Swal.fire({title:'Hapus '+n+' data?',text:'Tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)bf.submit()})});})();
</script>
<script>
const modal=document.getElementById('learnModal');
function openModal(d){
  document.getElementById('learnTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Barang':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Barang');
  document.getElementById('f_id').value=d?.id||0;document.getElementById('f_name').value=d?.name||'';document.getElementById('f_good').value=d?.good_qty??0;document.getElementById('f_mid').value=d?.mid_qty??0;document.getElementById('f_bad').value=d?.bad_qty??0;document.getElementById('f_sort').value=d?.sort_order??0;
  modal.classList.remove('hidden');document.body.style.overflow='hidden';
}
function closeModal(){modal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAdd').addEventListener('click',()=>openModal(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openModal(JSON.parse(b.dataset.row))));
modal.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>
