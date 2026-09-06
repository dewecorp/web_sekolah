<?php declare(strict_types=1); $title='Galeri';
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/gallery')); exit; }
$act=$_POST['act']??''; $t=trim($_POST['title']??'');
if($act==='del_album'){ $db->prepare("DELETE FROM galleries WHERE id=?")->execute([(int)$_POST['id']]); Auth::log($db,'delete','gallery','Hapus album'); Session::flash('ok','Album dihapus.'); }
elseif($act==='del_img'){ $s=$db->prepare("SELECT * FROM gallery_images WHERE id=?"); $s->execute([(int)$_POST['id']]); $im=$s->fetch(); if($im){ @unlink(ROOT.'/assets/uploads/'.basename($im['filepath'])); $db->prepare("DELETE FROM gallery_images WHERE id=?")->execute([$im['id']]); } Session::flash('ok','Foto dihapus.'); }
elseif($act==='album'){ if($t===''){ Session::flash('err','Judul wajib.'); } else { $slug=Security::slug($t); if(!empty($_POST['id'])) $db->prepare("UPDATE galleries SET title=?,slug=?,description=?,status=? WHERE id=?")->execute([$t,$slug,$_POST['description']??'',$_POST['status']??'published',(int)$_POST['id']]); else $db->prepare("INSERT INTO galleries(title,slug,description,status) VALUES(?,?,?,?)")->execute([$t,$slug,$_POST['description']??'',$_POST['status']??'published']); Auth::log($db,'create','gallery',"Album $t"); Session::flash('ok','Album disimpan.'); } }
elseif($act==='photo' && !empty($_FILES['f']['name']) && !empty($_POST['gallery_id'])){ $e=Security::validImage($_FILES['f'],$APP); if($e){ Session::flash('err',$e); header('Location: '.Helper::url('admin/gallery')); exit; } $n=Security::safeName($_FILES['f']['name']); move_uploaded_file($_FILES['f']['tmp_name'],ROOT.'/assets/uploads/'.$n); $db->prepare("INSERT INTO gallery_images(gallery_id,filepath,caption) VALUES(?,?,?)")->execute([(int)$_POST['gallery_id'],'assets/uploads/'.$n,$_POST['caption']??'']); Session::flash('ok','Foto ditambah.'); }
header('Location: '.Helper::url('admin/gallery'.(!empty($_POST['gallery_id'])?'?album='.(int)$_POST['gallery_id']:''))); exit; }
$albums=$db->query("SELECT g.*,(SELECT COUNT(*) FROM gallery_images WHERE gallery_id=g.id) cnt FROM galleries g ORDER BY id DESC")->fetchAll();
$sel=(int)($_GET['album']??($albums[0]['id']??0)); $imgs=[]; if($sel){ $s=$db->prepare("SELECT * FROM gallery_images WHERE gallery_id=? ORDER BY id DESC"); $s->execute([$sel]); $imgs=$s->fetchAll(); }
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-images text-emerald-600 mr-1"></i>Galeri</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($albums) ?> album</span>
<button id="btnAdd" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Album</button>
</div>
<div class="grid lg:grid-cols-2 gap-3 items-start">
<div class="bg-white rounded-2xl border p-4">
<p class="text-xs font-bold uppercase text-slate-400 mb-2">Album</p>
<?php if(!$albums): ?><p class="text-sm text-slate-500 text-center py-6">Belum ada album.</p><?php else: foreach($albums as $a): ?>
<div class="flex items-center gap-2 text-sm border-t py-2">
<a href="?album=<?= $a['id'] ?>" class="font-semibold flex-1 truncate <?= $sel==$a['id']?'text-emerald-600':'' ?>"><?= Helper::e($a['title']) ?> <span class="text-slate-400">(<?= $a['cnt'] ?>)</span></a>
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$a['id'],'title'=>$a['title'],'description'=>$a['description']??'','status'=>$a['status']]),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<?= Helper::iconBtns([Helper::delBtn((int)$a['id'],'del_album')]) ?></div>
<?php endforeach; endif; ?></div>
<div class="bg-white rounded-2xl border p-4">
<div class="flex items-center gap-2 mb-2"><h2 class="font-bold text-sm">Foto <?= $sel?"# $sel":'' ?></h2>
<?php if($sel): ?><button id="btnPhoto" class="ml-auto text-xs bg-emerald-600 text-white px-3 py-1.5 rounded-lg font-bold"><i class="fa fa-plus mr-1"></i>Upload Foto</button><?php endif; ?></div>
<div class="grid grid-cols-3 gap-2"><?php foreach($imgs as $im): ?><div class="border rounded-lg overflow-hidden text-xs"><img src="<?= Helper::url($im['filepath']) ?>" class="h-20 w-full object-cover" loading="lazy"><div class="p-1"><?= Helper::iconBtns([Helper::delBtn((int)$im['id'],'del_img')]) ?></div></div><?php endforeach; ?></div>
<?php if($sel&&!$imgs): ?><p class="text-sm text-slate-500 text-center py-4">Belum ada foto.</p><?php endif; ?></div></div>

<div id="albumModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl my-4 overflow-hidden">
<div class="flex items-center gap-2 px-5 py-3.5 border-b"><h2 class="font-extrabold" id="modalTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Album</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" data-loading class="p-5 grid gap-2.5 text-sm"><?= Security::csrfField() ?>
<input type="hidden" name="act" value="album"><input type="hidden" name="id" id="f_id" value="0">
<label class="grid gap-1 font-semibold">Judul Album<input name="title" id="f_title" required class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Deskripsi<textarea name="description" id="f_desc" rows="3" class="border rounded-lg p-2 font-normal"></textarea></label>
<label class="grid gap-1 font-semibold">Status<select name="status" id="f_status" class="border rounded-lg p-2 font-normal"><option value="published">Published</option><option value="draft">Draft</option></select></label>
<div class="flex gap-2"><button class="flex-1 bg-emerald-600 text-white rounded-xl p-2.5 font-bold">Simpan</button><button type="button" data-close class="border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<div id="photoModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl my-4 overflow-hidden">
<div class="flex items-center gap-2 px-5 py-3.5 border-b"><h2 class="font-extrabold"><i class="fa fa-image text-emerald-600 mr-1"></i>Upload Foto</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" enctype="multipart/form-data" data-loading class="p-5 grid gap-2.5 text-sm"><?= Security::csrfField() ?>
<input type="hidden" name="act" value="photo"><input type="hidden" name="gallery_id" value="<?= $sel ?>">
<label class="grid gap-1 font-semibold">Foto<input type="file" name="f" accept="image/*" required class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Caption<input name="caption" placeholder="Keterangan foto" class="border rounded-lg p-2 font-normal"></label>
<div class="flex gap-2"><button class="flex-1 bg-emerald-600 text-white rounded-xl p-2.5 font-bold">Upload</button><button type="button" data-close class="border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<script>
const modal=document.getElementById('albumModal'),pmodal=document.getElementById('photoModal');
function openModal(d){
  document.getElementById('modalTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Album':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Album');
  document.getElementById('f_id').value=d?.id||0;
  document.getElementById('f_title').value=d?.title||'';
  document.getElementById('f_desc').value=d?.description||'';
  document.getElementById('f_status').value=d?.status||'published';
  modal.classList.remove('hidden');document.body.style.overflow='hidden';
}
function closeAll(){modal.classList.add('hidden');pmodal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAdd').addEventListener('click',()=>openModal(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openModal(JSON.parse(b.dataset.row))));
document.getElementById('btnPhoto')?.addEventListener('click',()=>{pmodal.classList.remove('hidden');document.body.style.overflow='hidden'});
document.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',closeAll));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeAll()});
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>
