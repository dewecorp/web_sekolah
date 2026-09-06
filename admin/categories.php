<?php
declare(strict_types=1);
$title='Kategori'; $q=trim($_GET['q']??''); $edit=null;
try{$db->exec("ALTER TABLE categories ADD COLUMN grid_style VARCHAR(30) NOT NULL DEFAULT 'cards-3' AFTER description");}catch(Throwable){}
if(isset($_GET['edit'])){ $s=$db->prepare("SELECT * FROM categories WHERE id=?"); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/categories')); exit; }
  $act=$_POST['act']??'save';
  if($act==='bulk_delete'){ $ids=array_filter(array_map('intval',(array)($_POST['ids']??[]))); if(!$ids){ Session::flash('err','Pilih minimal 1 data.'); } else { $ph=implode(',',array_fill(0,count($ids),'?')); $db->prepare("DELETE FROM categories WHERE id IN ($ph)")->execute(array_values($ids)); Auth::log($db,'delete','categories','Hapus bulk kategori'); Session::flash('ok',count($ids).' kategori dihapus.'); } }
  elseif($act==='delete'){ $db->prepare("DELETE FROM categories WHERE id=?")->execute([(int)$_POST['id']]); Auth::log($db,'delete','categories','Hapus kategori'); Session::flash('ok','Kategori dihapus.'); }
  else{
    $name=trim($_POST['name']??''); $slug=Security::slug($_POST['slug']??$name);
    if($name===''){ Session::flash('err','Nama wajib diisi.'); }
    else{
      $grid=in_array($_POST['grid_style']??'', ['cards-2','cards-3','cards-4','magazine','masonry','horizontal','timeline','overlay','minimal'],true)?$_POST['grid_style']:'cards-3';
      if(!empty($_POST['id'])){ $db->prepare("UPDATE categories SET name=?,slug=?,description=?,grid_style=? WHERE id=?")->execute([$name,$slug,$_POST['description']??'',$grid,(int)$_POST['id']]); Auth::log($db,'update','categories',"Ubah kategori $name"); }
      else{ $db->prepare("INSERT INTO categories(name,slug,description,grid_style) VALUES(?,?,?,?)")->execute([$name,$slug,$_POST['description']??'',$grid]); Auth::log($db,'create','categories',"Tambah kategori $name"); }
      Session::flash('ok','Kategori disimpan.');
    }
  }
  header('Location: '.Helper::url('admin/categories')); exit;
}
$where=$q?"WHERE name LIKE ?":""; $p= $q?["%$q%"]:[];
$st=$db->prepare("SELECT * FROM categories $where ORDER BY id DESC"); $st->execute($p); $rows=$st->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-tags text-emerald-600 mr-1"></i>Kategori</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($rows) ?> kategori</span>
<form class="ml-2 hidden sm:flex gap-1"><input name="q" value="<?= Helper::e($q) ?>" placeholder="Cari nama..." class="border rounded-lg px-3 py-1.5 text-sm w-52"><button class="bg-slate-800 text-white px-3 rounded-lg text-sm"><i class="fa fa-search"></i></button></form>
<button id="btnAdd" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Kategori</button>
</div>
<form method="post" id="bulkForm"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b text-sm"><span id="selCount" class="text-slate-500">0 dipilih</span><button type="button" id="btnBulk" class="ml-auto bg-red-600 hover:bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[520px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-8"><input type="checkbox" id="checkAll"></th><th class="p-3 w-10">No</th><th class="p-3">Nama</th><th class="p-3">Slug</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$rows): ?><tr><td colspan="5" class="p-10 text-center text-slate-500"><i class="fa fa-tags text-3xl block mb-2"></i>Belum ada kategori. Klik Tambah Kategori.</td></tr><?php endif; ?>
<?php $no=1; foreach($rows as $r): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= $r['id'] ?>" class="rowcheck"></td><td class="p-3 text-slate-500"><?= $no++ ?></td><td class="p-3 font-semibold"><?= Helper::e($r['name']) ?><?php if(!empty($r['description'])): ?><span class="block text-[11px] font-normal text-slate-400"><?= Helper::e(Helper::excerpt($r['description']??'',80)) ?></span><?php endif; ?></td>
<td class="p-3 text-xs text-slate-500 font-mono">/<?= Helper::e($r['slug']) ?></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$r['id'],'name'=>$r['name'],'slug'=>$r['slug'],'description'=>$r['description']??'','grid_style'=>$r['grid_style']??'cards-3']),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>

<div id="catModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl my-4 overflow-hidden">
<div class="flex items-center gap-2 px-5 py-3.5 border-b bg-white"><h2 class="font-extrabold" id="modalTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Kategori</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" data-loading class="p-5 grid gap-3 text-sm bg-white" id="catForm"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="f_id" value="0">
<label class="grid gap-1 font-semibold">Nama<input name="name" id="f_name" required placeholder="Berita" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Slug <span class="font-normal text-slate-400 text-xs">otomatis dari nama</span><input name="slug" id="f_slug" placeholder="berita" class="border rounded-lg p-2 font-normal font-mono text-xs"></label>
<label class="grid gap-1 font-semibold">Deskripsi<textarea name="description" id="f_desc" rows="3" placeholder="Keterangan kategori..." class="border rounded-lg p-2 font-normal"></textarea></label>
<label class="grid gap-1 font-semibold">Gaya grid berita<select name="grid_style" id="f_grid" class="border rounded-lg p-2 font-normal"><?php foreach(['cards-2'=>'Kartu 2 Kolom','cards-3'=>'Kartu 3 Kolom','cards-4'=>'Kartu 4 Kolom','magazine'=>'Magazine','masonry'=>'Masonry','horizontal'=>'Horizontal','timeline'=>'Timeline','overlay'=>'Overlay','minimal'=>'Minimal'] as $k=>$v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach; ?></select></label>
<div class="flex gap-2 bg-white pt-2 pb-1"><button class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl p-2.5 font-bold"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<script>
(function(){const ca=document.getElementById('checkAll'),sc=document.getElementById('selCount'),bb=document.getElementById('btnBulk'),bf=document.getElementById('bulkForm');if(!ca||!bb||!bf)return;const up=()=>{sc.textContent=document.querySelectorAll('.rowcheck:checked').length+' dipilih'};ca.addEventListener('change',()=>{document.querySelectorAll('.rowcheck').forEach(c=>c.checked=ca.checked);up()});document.addEventListener('change',e=>{if(e.target.classList&&e.target.classList.contains('rowcheck'))up()});bb.addEventListener('click',()=>{const n=document.querySelectorAll('.rowcheck:checked').length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 data.','warning');return}Swal.fire({title:'Hapus '+n+' data?',text:'Tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)bf.submit()})});})();
</script>
<script>
const modal=document.getElementById('catModal');
const slugify=s=>(s||'').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
function openModal(d){
  document.getElementById('modalTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Kategori':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Kategori');
  document.getElementById('f_id').value=d?.id||0;
  document.getElementById('f_name').value=d?.name||'';
  const s=document.getElementById('f_slug');s.value=d?.slug||'';delete s.dataset.touched;
  document.getElementById('f_desc').value=d?.description||'';
  document.getElementById('f_grid').value=d?.grid_style||'cards-3';
  modal.classList.remove('hidden');document.body.style.overflow='hidden';
}
function closeModal(){modal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAdd').addEventListener('click',()=>openModal(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openModal(JSON.parse(b.dataset.row))));
modal.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
document.getElementById('f_name').addEventListener('input',e=>{const s=document.getElementById('f_slug');if(!s.dataset.touched)s.value=slugify(e.target.value)});
document.getElementById('f_slug').addEventListener('input',e=>e.target.dataset.touched='1');
<?php if($edit): ?>openModal(<?= json_encode(['id'=>$edit['id'],'name'=>$edit['name'],'slug'=>$edit['slug'],'description'=>$edit['description']??'','grid_style'=>$edit['grid_style']??'cards-3']) ?>);<?php endif; ?>
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>
