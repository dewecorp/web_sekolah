<?php
declare(strict_types=1);
$title='Halaman'; $edit=null;
if(isset($_GET['edit'])){ $s=$db->prepare("SELECT * FROM pages WHERE id=? AND deleted_at IS NULL"); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/pages')); exit; }
  $act=$_POST['act']??'save'; $t=trim($_POST['title']??'');
  if($act==='bulk_delete'){ $ids=array_filter(array_map('intval',(array)($_POST['ids']??[]))); if(!$ids){ Session::flash('err','Pilih minimal 1 data.'); } else { $ph=implode(',',array_fill(0,count($ids),'?')); $db->prepare("UPDATE pages SET deleted_at=NOW() WHERE id IN ($ph)")->execute(array_values($ids)); Auth::log($db,'delete','pages','Hapus bulk halaman'); Session::flash('ok',count($ids).' halaman dihapus.'); } }
  elseif($act==='delete'){ $db->prepare("UPDATE pages SET deleted_at=NOW() WHERE id=?")->execute([(int)$_POST['id']]); Auth::log($db,'delete','pages','Hapus halaman'); Session::flash('ok','Halaman dihapus.'); }
  elseif($t===''){ Session::flash('err','Judul wajib.'); }
  else{
    $slug=Security::slug($_POST['slug']??$t);
    if($slug==='')$slug='halaman-'.time();
    $img=$_POST['old_img']??null;
    if(!empty($_POST['clear_img']))$img=null;
    if(!empty($_FILES['img']['name']??'')){ $e=Security::validImage($_FILES['img'],$APP); if($e){ Session::flash('err',$e); header('Location: '.Helper::url('admin/pages')); exit; } $n=Security::safeName($_FILES['img']['name']); move_uploaded_file($_FILES['img']['tmp_name'],ROOT.'/assets/uploads/'.$n); $img=$n; }
    if(!empty($_POST['id'])){ $db->prepare("UPDATE pages SET title=?,slug=?,content=?,featured_image=?,status=?,seo_title=?,seo_description=? WHERE id=?")->execute([$t,$slug,$_POST['content']??'',$img,$_POST['status']??'draft',$_POST['seo_title']??'',$_POST['seo_description']??'',(int)$_POST['id']]); Auth::log($db,'update','pages',"Ubah $t"); }
    else{ try{ $db->prepare("INSERT INTO pages(title,slug,content,featured_image,status,seo_title,seo_description,author_id) VALUES(?,?,?,?,?,?,?,?)")->execute([$t,$slug,$_POST['content']??'',$img,$_POST['status']??'draft',$_POST['seo_title']??'',$_POST['seo_description']??'',$_SESSION['user']['id']]); Auth::log($db,'create','pages',"Tambah $t"); }catch(Throwable $e){ Session::flash('err','Slug sudah dipakai.'); header('Location: '.Helper::url('admin/pages')); exit; } }
    Session::flash('ok','Halaman disimpan.');
  }
  header('Location: '.Helper::url('admin/pages')); exit;
}
$q=trim($_GET['q']??'');
if($q){ $st=$db->prepare("SELECT * FROM pages WHERE deleted_at IS NULL AND (title LIKE ? OR slug LIKE ?) ORDER BY id DESC"); $st->execute(["%$q%","%$q%"]); $rows=$st->fetchAll(); }
else $rows=$db->query("SELECT * FROM pages WHERE deleted_at IS NULL ORDER BY id DESC")->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-file-lines text-emerald-600 mr-1"></i>Halaman</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($rows) ?> halaman</span>
<form class="ml-2 hidden sm:flex gap-1"><input name="q" value="<?= Helper::e($q) ?>" placeholder="Cari judul/slug..." class="border rounded-lg px-3 py-1.5 text-sm w-52"><button class="bg-slate-800 text-white px-3 rounded-lg text-sm"><i class="fa fa-search"></i></button></form>
<button id="btnAdd" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Halaman</button>
</div>
<form method="post" id="bulkForm"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b text-sm"><span id="selCount" class="text-slate-500">0 dipilih</span><button type="button" id="btnBulk" class="ml-auto bg-red-600 hover:bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[640px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-8"><input type="checkbox" id="checkAll"></th><th class="p-3 w-10">No</th><th class="p-3">Judul</th><th class="p-3">Slug / SEO</th><th class="p-3">Status</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$rows): ?><tr><td colspan="6" class="p-10 text-center text-slate-500"><i class="fa fa-file-circle-xmark text-3xl block mb-2"></i>Belum ada halaman. Klik Tambah Halaman.</td></tr><?php endif; ?>
<?php $no=1; foreach($rows as $r): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= $r['id'] ?>" class="rowcheck"></td><td class="p-3 text-slate-500"><?= $no++ ?></td><td class="p-3 font-semibold"><?= Helper::e($r['title']) ?><span class="block text-[11px] font-normal text-slate-400"><?= Helper::e(Helper::excerpt($r['content']??'',80)) ?></span></td>
<td class="p-3 text-xs text-slate-500 font-mono">/<?= Helper::e($r['slug']) ?><?= $r['seo_title']?'<span class="block text-emerald-600">SEO ✓</span>':'' ?></td>
<td class="p-3"><span class="text-xs font-bold px-2 py-0.5 rounded-full <?= $r['status']==='published'?'bg-emerald-100 text-emerald-700':'bg-amber-100 text-amber-700' ?>"><?= $r['status'] ?></span></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$r['id'],'title'=>$r['title'],'slug'=>$r['slug'],'content'=>$r['content']??'','img'=>$r['featured_image']??'','status'=>$r['status'],'seo_title'=>$r['seo_title']??'','seo_description'=>$r['seo_description']??'']),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<a href="<?= Helper::url($r['slug']) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-sky-600" title="Lihat"><i class="fa fa-eye text-xs"></i></a>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>

<div id="pageModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl my-4">
<div class="flex items-center gap-2 px-5 py-3.5 border-b bg-white"><h2 class="font-extrabold" id="modalTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Halaman</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" enctype="multipart/form-data" data-loading class="p-5 grid gap-3 text-sm bg-white" id="pageForm"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="f_id" value="0"><input type="hidden" name="old_img" id="f_old" value="">
<label class="grid gap-1 font-semibold">Judul<input name="title" id="f_title" required placeholder="Tentang Kami" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Slug <span class="font-normal text-slate-400 text-xs">otomatis dari judul</span><input name="slug" id="f_slug" placeholder="tentang-kami" class="border rounded-lg p-2 font-normal font-mono text-xs"></label>
<label class="grid gap-1 font-semibold">Konten<textarea name="content" id="pageContent" rows="8"></textarea><span id="editorWarn" class="hidden text-xs font-normal text-red-600">Editor gagal dimuat (CDN diblokir). Textarea biasa tetap bisa disimpan.</span></label>
<div class="border rounded-xl p-3 bg-slate-50"><p class="text-xs font-bold mb-1.5"><i class="fa fa-image mr-1 text-emerald-600"></i>Featured Image</p>
<img id="f_prev" alt="" class="hidden h-28 w-full object-cover rounded-lg border mb-1.5">
<input type="file" name="img" id="f_img" accept="image/*" class="border rounded-lg p-2 w-full bg-white text-xs">
<label class="text-xs flex gap-1.5 items-center mt-1.5" id="wrapClear" style="display:none"><input type="checkbox" name="clear_img" value="1"> Hapus gambar</label></div>
<label class="grid gap-1 font-semibold">Status<select name="status" id="f_status" class="border rounded-lg p-2 font-normal"><option value="draft">Draft</option><option value="published">Published</option></select>
<span class="text-xs font-normal text-slate-400">Published langsung tampil di URL /slug</span></label>
<label class="grid gap-1 font-semibold">SEO Title<input name="seo_title" id="f_seot" placeholder="Tentang Kami - Sekolah" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">SEO Description<textarea name="seo_description" id="f_seod" rows="2" placeholder="Ringkasan untuk Google..." class="border rounded-lg p-2 font-normal"></textarea></label>
<div class="flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@41.4.2/build/ckeditor.js"></script>
<script>
(function(){const ca=document.getElementById('checkAll'),rows=()=>document.querySelectorAll('.rowcheck'),sc=document.getElementById('selCount'),bb=document.getElementById('btnBulk'),bf=document.getElementById('bulkForm');if(!ca||!bb||!bf)return;const up=()=>{const n=document.querySelectorAll('.rowcheck:checked').length;sc.textContent=n+' dipilih'};ca.addEventListener('change',()=>{rows().forEach(c=>c.checked=ca.checked);up()});document.addEventListener('change',e=>{if(e.target.classList&&e.target.classList.contains('rowcheck'))up()});bb.addEventListener('click',()=>{const n=document.querySelectorAll('.rowcheck:checked').length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 data.','warning');return}Swal.fire({title:'Hapus '+n+' data?',text:'Tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)bf.submit()})});})();
</script>
<script>
const modal=document.getElementById('pageModal');
const slugify=s=>(s||'').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
let pageEditor=null;
let pendingData=null;
function openModal(d){
  document.getElementById('modalTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Halaman':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Halaman');
  document.getElementById('f_id').value=d?.id||0;
  document.getElementById('f_title').value=d?.title||'';
  const s=document.getElementById('f_slug');s.value=d?.slug||'';delete s.dataset.touched;
  const st=document.getElementById('f_status');st.value=d?.status||'draft';
  st.dispatchEvent(new Event('change',{bubbles:true}));
  if(st._cpaint)st._cpaint(); else if(st._csync)st._csync(); else if(window.__refreshSelects&&window.__refreshSelects.f_status)window.__refreshSelects.f_status();
  document.getElementById('f_seot').value=d?.seo_title||'';
  document.getElementById('f_seod').value=d?.seo_description||'';
  document.getElementById('f_old').value=d?.img||'';
  const pv=document.getElementById('f_prev');
  if(d?.img){pv.src='<?= Helper::url('assets/uploads/') ?>/'+d.img;pv.classList.remove('hidden');document.getElementById('wrapClear').style.display=''}else{pv.classList.add('hidden');document.getElementById('wrapClear').style.display='none'}
  if(pageEditor)pageEditor.setData(d?.content||'');else{document.getElementById('pageContent').value=d?.content||'';pendingData=d?.content||''}
  modal.classList.remove('hidden');document.body.style.overflow='hidden';
  ensureEditor();
}
function closeModal(){modal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAdd').addEventListener('click',()=>openModal(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openModal(JSON.parse(b.dataset.row))));
modal.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
document.getElementById('f_title').addEventListener('input',e=>{const s=document.getElementById('f_slug');if(!s.dataset.touched)s.value=slugify(e.target.value)});
document.getElementById('f_slug').addEventListener('input',e=>e.target.dataset.touched='1');
document.getElementById('f_img').addEventListener('change',e=>{const f=e.target.files[0];if(!f)return;const pv=document.getElementById('f_prev');pv.src=URL.createObjectURL(f);pv.classList.remove('hidden')});
<?php if($edit): ?>openModal(<?= json_encode(['id'=>$edit['id'],'title'=>$edit['title'],'slug'=>$edit['slug'],'content'=>$edit['content']??'','img'=>$edit['featured_image']??'','status'=>$edit['status'],'seo_title'=>$edit['seo_title']??'','seo_description'=>$edit['seo_description']??'']) ?>);<?php endif; ?>
function ensureEditor(){
  const warn=document.getElementById('editorWarn');
  if(pageEditor)return;
  if(!window.ClassicEditor){ if(warn)warn.classList.remove('hidden'); return; }
  const el=document.querySelector('#pageContent');
  const done=e=>{pageEditor=e;if(pendingData){try{e.setData(pendingData)}catch(_){}pendingData=null}};
  const fail=()=>{ const w=document.getElementById('editorWarn'); if(w)w.classList.remove('hidden') };
  try{ window.CKCreate(el).then(done).catch(fail); }catch(_){ fail(); }}
document.getElementById('pageForm').addEventListener('submit',()=>{ if(pageEditor){ try{document.getElementById('pageContent').value=pageEditor.getData()}catch(_){} } });
</script>
<style>.ck-editor__editable{min-height:280px}.ck-content h1{font-size:1.6rem;font-weight:800}.ck-content h2{font-size:1.35rem;font-weight:800}.ck-content h3{font-size:1.15rem;font-weight:700}.ck-content table{width:100%}.ck-balloon-panel,.ck-dropdown__panel{z-index:9999!important}#pageModal{z-index:50}</style>
<?php require ROOT.'/templates/admin/footer.php'; ?>




