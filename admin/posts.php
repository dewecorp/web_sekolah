<?php
declare(strict_types=1);
$title='Berita'; $q=trim($_GET['q']??''); $fcat=trim($_GET['cat']??''); $fdate=trim($_GET['date']??''); $fmonth=trim($_GET['month']??''); $fyear=trim($_GET['year']??''); $page=max(1,(int)($_GET['page']??1)); $per=10; $off=($page-1)*$per;
$edit=null; if(isset($_GET['edit'])){ $s=$db->prepare("SELECT * FROM posts WHERE id=? AND deleted_at IS NULL"); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/posts')); exit; }
  $act=$_POST['act']??'save';
  $isAuthorPost = ($_SESSION['user']['role'] ?? '') === 'author'; $uidPost = (int)$_SESSION['user']['id'];
  if ($isAuthorPost && in_array($act,['delete','toggle'],true)) { $own=$db->prepare("SELECT id FROM posts WHERE id=? AND author_id=?"); $own->execute([(int)$_POST['id'],$uidPost]); if(!$own->fetch()){ Session::flash('err','Akses ditolak.'); header('Location: '.Helper::url('admin/posts')); exit; } }
  if($act==='bulk_delete'){ $ids=array_filter(array_map('intval',(array)($_POST['ids']??[]))); if(!$ids){ Session::flash('err','Pilih minimal 1 data.'); } else { $ph=implode(',',array_fill(0,count($ids),'?')); if($isAuthorPost){ $st=$db->prepare("UPDATE posts SET deleted_at=NOW() WHERE id IN ($ph) AND author_id=?"); $st->execute(array_merge(array_values($ids),[$uidPost])); } else { $st=$db->prepare("UPDATE posts SET deleted_at=NOW() WHERE id IN ($ph)"); $st->execute(array_values($ids)); } Auth::log($db,'delete','posts','Hapus bulk berita'); Session::flash('ok',$st->rowCount().' berita dihapus.'); } }
  elseif($act==='delete'){ $db->prepare("UPDATE posts SET deleted_at=NOW() WHERE id=?")->execute([(int)$_POST['id']]); Auth::log($db,'delete','posts','Hapus berita'); Session::flash('ok','Berita dihapus.'); }
  elseif($act==='toggle'){ $db->prepare("UPDATE posts SET status=CASE WHEN status='published' THEN 'draft' ELSE 'published' END WHERE id=?")->execute([(int)$_POST['id']]); Auth::log($db,'update','posts','Ubah status berita'); Session::flash('ok','Status diubah.'); }
  else{
    $t=trim($_POST['title']??''); if($t===''){ Session::flash('err','Judul wajib.'); header('Location: '.Helper::url('admin/posts')); exit; }
    $slug=Security::slug($_POST['slug']??$t); if($slug==='')$slug='berita-'.time();
    $img=$_POST['old_img']??null;
    if(!empty($_POST['clear_img']))$img=null;
    if(!empty($_FILES['img']['name']??'')){ $e=Security::validImage($_FILES['img'],$APP); if($e){ Session::flash('err',$e); header('Location: '.Helper::url('admin/posts')); exit; } $n=Security::safeName($_FILES['img']['name']); move_uploaded_file($_FILES['img']['tmp_name'],ROOT.'/assets/uploads/'.$n); $img=$n; }
    $d=['title'=>$t,'slug'=>$slug,'cat'=>$_POST['category_id']?:null,'ex'=>$_POST['excerpt']??'','ct'=>$_POST['content']??'','img'=>$img,'st'=>$_POST['status']??'draft','pub'=>$_POST['published_at']?:date('Y-m-d H:i:s'),'au'=>$_SESSION['user']['id']];
    try{
      if(!empty($_POST['id'])){ $db->prepare("UPDATE posts SET title=?,slug=?,category_id=?,excerpt=?,content=?,featured_image=?,status=?,published_at=? WHERE id=?")->execute([$d['title'],$d['slug'],$d['cat'],$d['ex'],$d['ct'],$d['img'],$d['st'],$d['pub'],(int)$_POST['id']]); Auth::log($db,'update','posts',"Ubah $t"); }
      else{ $db->prepare("INSERT INTO posts(title,slug,category_id,excerpt,content,featured_image,status,author_id,published_at) VALUES(?,?,?,?,?,?,?,?,?)")->execute([$d['title'],$d['slug'],$d['cat'],$d['ex'],$d['ct'],$d['img'],$d['st'],$d['au'],$d['pub']]); Auth::log($db,'create','posts',"Tambah $t"); }
      Session::flash('ok','Berita disimpan.');
    }catch(Throwable $e){ Session::flash('err','Slug sudah dipakai.'); header('Location: '.Helper::url('admin/posts')); exit; }
  }
  header('Location: '.Helper::url('admin/posts')); exit;
}
$cats=$db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
try{ $pyears=$db->query("SELECT DISTINCT YEAR(COALESCE(published_at,created_at)) y FROM posts WHERE deleted_at IS NULL ORDER BY y DESC")->fetchAll(PDO::FETCH_COLUMN); }catch(Throwable){ $pyears=[]; }
$isAuthor = ($_SESSION['user']['role'] ?? '') === 'author'; $uid = (int)$_SESSION['user']['id'];
if ($isAuthor && $edit && (int)($edit['author_id'] ?? 0) !== $uid) $edit = null;
$w="WHERE p.deleted_at IS NULL"; $pr=[];
if($q){$w.=" AND p.title LIKE ?";$pr[]="%$q%";}
if($fcat!==''){if($fcat==='0'){$w.=" AND p.category_id IS NULL";}else{$w.=" AND p.category_id=?";$pr[]=(int)$fcat;}}
if(preg_match('/^\d{4}-\d{2}-\d{2}$/',$fdate)){$w.=" AND DATE(COALESCE(p.published_at,p.created_at))=?";$pr[]=$fdate;}
if(preg_match('/^\d{4}-\d{2}$/',$fmonth)){$w.=" AND DATE_FORMAT(COALESCE(p.published_at,p.created_at),'%Y-%m')=?";$pr[]=$fmonth;}
if(preg_match('/^\d{4}$/',$fyear)){$w.=" AND YEAR(COALESCE(p.published_at,p.created_at))=?";$pr[]=$fyear;}
if ($isAuthor) { $w .= " AND p.author_id=$uid"; }
$st=$db->prepare("SELECT COUNT(*) FROM posts p $w"); $st->execute($pr); $total=(int)$st->fetchColumn();
$st=$db->prepare("SELECT p.*,c.name cat FROM posts p LEFT JOIN categories c ON c.id=p.category_id $w ORDER BY p.id DESC LIMIT $per OFFSET $off"); $st->execute($pr); $rows=$st->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-newspaper text-emerald-600 mr-1"></i>Berita</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= $total ?> berita</span>
<form class="ml-2 hidden sm:flex gap-1"><input name="q" value="<?= Helper::e($q) ?>" placeholder="Cari judul..." class="border rounded-lg px-3 py-1.5 text-sm w-52"><button type="submit" title="Cari" aria-label="Cari" class="bg-slate-800 text-white px-3 rounded-lg text-sm"><i class="fa fa-search text-xs"></i></button></form>
<button id="btnAdd" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Berita</button>
</div>
<form id="postFilter" class="grid sm:grid-cols-2 lg:grid-cols-6 gap-2 mb-4 text-sm"><input name="q" id="pq" value="<?= Helper::e($q) ?>" placeholder="Cari judul..." class="border rounded-lg px-3 py-1.5"><select name="cat" id="pcat" class="border rounded-lg px-3 py-1.5"><option value="">Semua kategori</option><option value="0" <?= $fcat==='0'?'selected':'' ?>>Tanpa kategori</option><?php foreach($cats as $c): ?><option value="<?= $c['id'] ?>" <?= (string)$fcat===(string)$c['id']?'selected':'' ?>><?= Helper::e($c['name']) ?></option><?php endforeach; ?></select><input type="date" name="date" id="pdate" value="<?= Helper::e($fdate) ?>" title="Filter tanggal" class="border rounded-lg px-3 py-1.5"><input type="month" name="month" id="pmonth" value="<?= Helper::e($fmonth) ?>" title="Filter bulan" class="border rounded-lg px-3 py-1.5"><select name="year" id="pyear" class="border rounded-lg px-3 py-1.5"><option value="">Semua tahun</option><?php foreach($pyears as $y): ?><option value="<?= $y ?>" <?= (string)$fyear===(string)$y?'selected':'' ?>><?= $y ?></option><?php endforeach; ?></select><a href="<?= Helper::url('admin/posts') ?>" class="px-3 py-1.5 border rounded-lg text-center" title="Reset">Reset</a></form>
<script>
(function(){
  const f=document.getElementById('postFilter');if(!f)return;
  let t=null;
  f.querySelector('#pq')?.addEventListener('input',()=>{clearTimeout(t);t=setTimeout(()=>f.submit(),500)});
  ['pcat','pdate','pmonth','pyear'].forEach(id=>f.querySelector('#'+id)?.addEventListener('change',()=>f.submit()));
})();
</script>
<form method="post" id="bulkForm"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b text-sm"><span id="selCount" class="text-slate-500">0 dipilih</span><button type="button" id="btnBulk" class="ml-auto bg-red-600 hover:bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[640px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-8"><input type="checkbox" id="checkAllPosts"></th><th class="p-3 w-10">No</th><th class="p-3">Judul</th><th class="p-3">Kategori</th><th class="p-3">Status</th><th class="p-3">Tanggal</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$rows): ?><tr><td colspan="7" class="p-10 text-center text-slate-500"><i class="fa fa-newspaper text-3xl block mb-2"></i>Belum ada berita. Klik Tambah Berita.</td></tr><?php endif; ?>
<?php $no=$off+1; foreach($rows as $r): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= $r['id'] ?>" class="rowcheck"></td><td class="p-3 text-slate-500"><?= $no++ ?></td><td class="p-3 font-semibold"><?= Helper::e($r['title']) ?><span class="block text-[11px] font-normal text-slate-400"><?= Helper::e($r['cat']??'Tanpa kategori') ?> • /berita/<?= Helper::e($r['slug']) ?></span></td>
<td class="p-3"><span class="inline-block text-xs font-semibold px-2 py-1 rounded-full bg-sky-50 text-sky-700 whitespace-nowrap"><?= Helper::e($r['cat']??'Tanpa kategori') ?></span></td>
<td class="p-3"><span class="text-xs font-bold px-2 py-0.5 rounded-full <?= $r['status']==='published'?'bg-emerald-100 text-emerald-700':'bg-amber-100 text-amber-700' ?>"><?= $r['status'] ?></span></td>
<td class="p-3 text-xs text-slate-500"><?= Helper::e(Helper::tgl($r['published_at']??$r['created_at'])) ?></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$r['id'],'title'=>$r['title'],'slug'=>$r['slug'],'category_id'=>$r['category_id'],'excerpt'=>$r['excerpt']??'','content'=>$r['content']??'','img'=>$r['featured_image']??'','status'=>$r['status'],'published_at'=>isset($r['published_at'])?date('Y-m-d\TH:i',strtotime($r['published_at'])):date('Y-m-d\TH:i')]),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<a href="<?= Helper::url('berita/'.$r['slug']) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-sky-600" title="Lihat"><i class="fa fa-eye text-xs"></i></a>
<form method="post" class="inline"><input type="hidden" name="csrf" value="<?= Security::csrfToken() ?>"><input type="hidden" name="act" value="toggle"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-amber-600" title="Publish/Draft"><i class="fa fa-arrows-rotate text-xs"></i></button></form>
<form method="post" data-confirm class="inline"><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>
<div class="mt-3 text-sm"><?= Helper::paginate($total,$per,$page,Helper::url('admin/posts')) ?></div>

<div id="postModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl my-4">
<div class="flex items-center gap-2 px-5 py-3.5 border-b bg-white"><h2 class="font-extrabold" id="modalTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Berita</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" enctype="multipart/form-data" data-loading class="p-5 grid gap-3 text-sm bg-white" id="postForm"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="f_id" value="0"><input type="hidden" name="old_img" id="f_old" value="">
<label class="grid gap-1 font-semibold">Judul<input name="title" id="f_title" required placeholder="Judul berita" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Slug <span class="font-normal text-slate-400 text-xs">otomatis dari judul</span><input name="slug" id="f_slug" placeholder="judul-berita" class="border rounded-lg p-2 font-normal font-mono text-xs"></label>
<label class="grid gap-1 font-semibold">Kategori<select name="category_id" id="f_cat" class="border rounded-lg p-2 font-normal"><option value="">- Tanpa kategori -</option><?php foreach($cats as $c): ?><option value="<?= $c['id'] ?>"><?= Helper::e($c['name']) ?></option><?php endforeach; ?></select></label>
<label class="grid gap-1 font-semibold">Tanggal Publish<input type="datetime-local" name="published_at" id="f_pub" value="<?= date('Y-m-d\TH:i') ?>" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Excerpt <span class="font-normal text-slate-400 text-xs">ringkasan kartu berita</span><textarea name="excerpt" id="f_ex" rows="2" placeholder="Ringkasan singkat..." class="border rounded-lg p-2 font-normal"></textarea></label>
<label class="grid gap-1 font-semibold">Konten<textarea name="content" id="postContent" rows="10"></textarea><span id="editorWarn" class="hidden text-xs font-normal text-red-600">Editor gagal dimuat (CDN diblokir). Textarea biasa tetap bisa disimpan.</span></label>
<div class="border rounded-xl p-3 bg-slate-50"><p class="text-xs font-bold mb-1.5"><i class="fa fa-image mr-1 text-emerald-600"></i>Featured Image</p>
<img id="f_prev" alt="" class="hidden h-28 w-full object-cover rounded-lg border mb-1.5">
<div class="flex gap-1.5 mb-1.5">
<button type="button" id="btnMedia" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold px-3 py-2 rounded-lg"><i class="fa fa-photo-film mr-1"></i>Pilih dari Media</button>
<label class="flex-1 bg-white border text-xs font-bold px-3 py-2 rounded-lg text-center cursor-pointer hover:border-emerald-400"><i class="fa fa-upload mr-1"></i>Upload Baru<input type="file" name="img" id="f_img" accept="image/*" class="hidden"></label>
</div>
<p class="text-[11px] text-slate-400 mb-1.5">Utamakan pilih dari Media. Upload dipakai bila gambar belum ada.</p>
<label class="text-xs flex gap-1.5 items-center mt-1.5" id="wrapClear" style="display:none"><input type="checkbox" name="clear_img" value="1"> Hapus gambar</label></div>
<label class="grid gap-1 font-semibold">Status<select name="status" id="f_status" class="border rounded-lg p-2 font-normal"><option value="draft">Draft</option><option value="published">Published</option></select>
<span class="text-xs font-normal text-slate-400">Published langsung tampil di /berita/slug</span></label>
<div class="flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<div id="mediaPicker" class="hidden fixed inset-0 z-[60] overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/70" data-close-picker></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl my-4">
<div class="flex items-center gap-2 px-5 py-3.5 border-b"><h2 class="font-extrabold"><i class="fa fa-photo-film text-emerald-600 mr-1"></i>Pilih Gambar</h2><button data-close-picker class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<div class="p-4">
<div class="grid grid-cols-2 gap-1 mb-3 bg-slate-100 rounded-lg p-1 text-xs font-bold">
<button type="button" data-mtab="lib" class="px-2 py-1.5 rounded-md bg-white shadow">Pustaka Media</button>
<button type="button" data-mtab="up" class="px-2 py-1.5 rounded-md text-slate-500">Unggah Baru</button>
</div>
<div id="paneLib">
<div class="flex gap-2 mb-2"><input id="msearch" placeholder="Cari gambar..." class="border rounded-lg p-2 text-sm w-full"><a href="<?= Helper::url('admin/media') ?>" target="_blank" rel="noopener noreferrer" class="text-xs border rounded-lg px-3 py-2 whitespace-nowrap">Kelola →</a></div>
<div id="mgrid" class="grid grid-cols-3 sm:grid-cols-4 gap-2 max-h-[50vh] overflow-y-auto border rounded-xl p-2 bg-slate-50"></div>
</div>
<div id="paneUp" class="hidden">
<div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center">
<p class="text-sm font-bold">Unggah gambar baru ke Media</p>
<p class="text-xs text-slate-400 mb-2">JPG/PNG/WebP/GIF max 5MB. Otomatis masuk pustaka.</p>
<input type="file" id="mfile" accept="image/*" class="border rounded-lg p-2 text-sm w-full bg-white">
<button type="button" id="mup" class="mt-2 bg-emerald-600 text-white text-sm font-bold px-4 py-2 rounded-xl">Upload</button>
</div></div>
<div class="flex items-center gap-2 px-5 py-3 border-t bg-slate-50">
<span id="mSelInfo" class="text-xs text-slate-500 flex-1 truncate">Belum ada gambar dipilih.</span>
<button type="button" data-close-picker class="border rounded-xl px-4 py-2 text-sm">Batal</button>
<button type="button" id="mInsert" disabled class="bg-emerald-600 disabled:opacity-40 text-white text-sm font-bold px-4 py-2 rounded-xl"><i class="fa fa-check mr-1"></i>Masukkan ke Berita</button>
</div>
</div></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.1/tinymce.min.js"></script>
<script>
const modal=document.getElementById('postModal');
const slugify=s=>(s||'').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
let postEditor=null,pendingData=null;
function syncSelect(id){
  const sel=document.getElementById(id);if(!sel)return;
  sel.dispatchEvent(new Event('change',{bubbles:true}));
  if(sel._cpaint)sel._cpaint();
  if(window.__refreshSelects&&window.__refreshSelects[id])window.__refreshSelects[id]();
}
function openModal(d){
  document.getElementById('modalTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Berita':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Berita');
  document.getElementById('f_id').value=d?.id||0;
  document.getElementById('f_title').value=d?.title||'';
  const s=document.getElementById('f_slug');s.value=d?.slug||'';delete s.dataset.touched;
  document.getElementById('f_cat').value=d?.category_id!=null?String(d.category_id):'';
  document.getElementById('f_ex').value=d?.excerpt||'';
  document.getElementById('f_status').value=d?.status||'draft';
  syncSelect('f_cat');syncSelect('f_status');
  document.getElementById('f_pub').value=d?.published_at||'<?= date('Y-m-d\TH:i') ?>';
  document.getElementById('f_old').value=d?.img||'';
  const pv=document.getElementById('f_prev');
  if(d?.img){pv.src='<?= Helper::url('assets/uploads/') ?>/'+d.img;pv.classList.remove('hidden');document.getElementById('wrapClear').style.display=''}else{pv.classList.add('hidden');document.getElementById('wrapClear').style.display='none'}
  if(postEditor)postEditor.setData(d?.content||'');else{document.getElementById('postContent').value=d?.content||'';pendingData=d?.content||''}
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
<?php if($edit): ?>openModal(<?= json_encode(['id'=>$edit['id'],'title'=>$edit['title'],'slug'=>$edit['slug'],'category_id'=>$edit['category_id'],'excerpt'=>$edit['excerpt']??'','content'=>$edit['content']??'','img'=>$edit['featured_image']??'','status'=>$edit['status'],'published_at'=>date('Y-m-d\TH:i',strtotime($edit['published_at']??'now'))]) ?>);<?php endif; ?>
function ensureEditor(){
  const warn=document.getElementById('editorWarn');
  if(postEditor)return;
  if(!window.tinymce||!window.RichEditorCreate){ if(warn)warn.classList.remove('hidden'); return; }
  const el=document.querySelector('#postContent');
  const done=e=>{postEditor=e;if(pendingData){try{e.setData(pendingData)}catch(_){}pendingData=null}};
  const fail=()=>{ const w=document.getElementById('editorWarn'); if(w)w.classList.remove('hidden') };
  try{ window.RichEditorCreate(el,{height:500}).then(done).catch(fail); }catch(_){ fail(); }}
document.getElementById('postForm').addEventListener('submit',()=>{ if(postEditor){ try{document.getElementById('postContent').value=postEditor.getData()}catch(_){} } });
// ---- Pemilih gambar ala WordPress: centang dulu, tombol Masukkan baru isi ----
const picker=document.getElementById('mediaPicker'),mgrid=document.getElementById('mgrid'),msearch=document.getElementById('msearch'),mup=document.getElementById('mup'),mfile=document.getElementById('mfile'),mInsert=document.getElementById('mInsert'),mSelInfo=document.getElementById('mSelInfo');
const MEDIA_CSRF='<?= Security::csrfToken() ?>',UP_BASE='<?= Helper::url('assets/uploads/') ?>';
let mSel=null; // {filename,url}
function paintSel(){
  if(mSel){mSelInfo.innerHTML='<b>'+mSel.filename+'</b> dipilih. Klik Masukkan ke Berita.';mInsert.disabled=false}
  else{mSelInfo.textContent='Belum ada gambar dipilih.';mInsert.disabled=true}
  mgrid.querySelectorAll('[data-fn]').forEach(b=>{
    const on=mSel&&b.dataset.fn===mSel.filename;
    b.classList.toggle('ring-2',on);b.classList.toggle('ring-emerald-500',on);b.classList.toggle('border-emerald-500',on);
    b.querySelector('.mcheck').classList.toggle('hidden',!on);
  });
}
function setFeatured(fn,url){
  document.getElementById('f_old').value=fn;
  document.getElementById('f_img').value='';
  const pv=document.getElementById('f_prev');
  pv.src=url;pv.classList.remove('hidden');
  document.getElementById('wrapClear').style.display='';
}
function loadMedia(q){
  mSel=null;paintSel();
  mgrid.innerHTML='<p class="text-sm text-slate-500 col-span-full text-center py-6">Memuat...</p>';
  fetch('<?= Helper::url('admin/media') ?>?ajax=1&act=list&qq='+encodeURIComponent(q||''),{headers:{'X-Requested-With':'fetch'}})
  .then(r=>r.json()).then(j=>{
    if(!j.ok){mgrid.innerHTML='<p class="text-sm text-red-600 col-span-full text-center">Gagal memuat.</p>';return}
    if(!j.items.length){mgrid.innerHTML='<p class="text-sm text-slate-500 col-span-full text-center py-6">Tidak ada gambar. Gunakan tab Unggah Baru.</p>';return}
    mgrid.innerHTML='';
    j.items.forEach(m=>{
      const b=document.createElement('button');
      b.type='button';b.dataset.fn=m.filename;b.dataset.url=m.url;
      b.className='relative border rounded-xl overflow-hidden hover:border-emerald-500 transition text-left';
      b.innerHTML='<span class="mcheck hidden absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-emerald-600 text-white grid place-items-center text-xs"><i class="fa fa-check"></i></span><img src="'+m.url+'" loading="lazy" class="h-24 w-full object-cover"><span class="block text-[10px] p-1 truncate">'+m.filename+'</span>';
      b.addEventListener('click',()=>{
        mSel=(mSel&&mSel.filename===m.filename)?null:{filename:m.filename,url:m.url};
        paintSel();
      });
      mgrid.appendChild(b);
    });
    paintSel();
  }).catch(()=>{mgrid.innerHTML='<p class="text-sm text-red-600 col-span-full text-center">Gagal memuat.</p>'});
}
function openPicker(){picker.classList.remove('hidden');mSel=null;loadMedia(msearch.value)}
function closePicker(){picker.classList.add('hidden')}
mInsert.addEventListener('click',()=>{
  if(!mSel){Swal.fire('Pilih dulu','Centang 1 gambar.','warning');return}
  setFeatured(mSel.filename,mSel.url);closePicker();
  Swal.fire({icon:'success',title:'Gambar dimasukkan',timer:1200,showConfirmButton:false});
});
document.getElementById('btnMedia').addEventListener('click',openPicker);
picker.querySelectorAll('[data-close-picker]').forEach(b=>b.addEventListener('click',closePicker));
document.querySelectorAll('#mediaPicker [data-mtab]').forEach(b=>b.addEventListener('click',()=>{
  document.querySelectorAll('#mediaPicker [data-mtab]').forEach(x=>{x.classList.remove('bg-white','shadow');x.classList.add('text-slate-500')});
  b.classList.add('bg-white','shadow');b.classList.remove('text-slate-500');
  document.getElementById('paneLib').classList.toggle('hidden',b.dataset.mtab!=='lib');
  document.getElementById('paneUp').classList.toggle('hidden',b.dataset.mtab!=='up');
}));
let mT=null;msearch.addEventListener('input',()=>{clearTimeout(mT);mT=setTimeout(()=>loadMedia(msearch.value),350)});
mup.addEventListener('click',()=>{
  const f=mfile.files[0];if(!f){Swal.fire('Pilih dulu','Pilih file gambar.','warning');return}
  const fd=new FormData();fd.append('csrf',MEDIA_CSRF);fd.append('ajax','1');fd.append('f',f);
  mup.disabled=true;mup.textContent='Mengunggah...';
  fetch('<?= Helper::url('admin/media') ?>',{method:'POST',body:fd}).then(r=>r.json()).then(j=>{
    mup.disabled=false;mup.textContent='Upload';
    if(!j.ok){Swal.fire('Gagal',j.msg||'Upload gagal','error');return}
    mfile.value='';
    // kembali ke pustaka, muat ulang, dan centang file baru (belum dimasukkan)
    document.querySelector('#mediaPicker [data-mtab="lib"]').click();
    loadMedia('');
    setTimeout(()=>{mSel={filename:j.filename,url:j.url};paintSel();
      mgrid.querySelectorAll('[data-fn]').forEach(b=>{
        const on=b.dataset.fn===j.filename;
        b.classList.toggle('ring-2',on);b.classList.toggle('ring-emerald-500',on);b.classList.toggle('border-emerald-500',on);
        b.querySelector('.mcheck').classList.toggle('hidden',!on);
      });
    },600);
    Swal.fire({icon:'success',title:'Terunggah, silakan centang lalu Masukkan',timer:1600,showConfirmButton:false});
  }).catch(()=>{mup.disabled=false;mup.textContent='Upload';Swal.fire('Gagal','Upload gagal','error')});
});
</script>
<script>
(function(){const ca=document.getElementById('checkAllPosts'),sc=document.getElementById('selCount'),bb=document.getElementById('btnBulk'),bf=document.getElementById('bulkForm');if(!ca||!bb||!bf)return;const up=()=>{sc.textContent=document.querySelectorAll('.rowcheck:checked').length+' dipilih'};ca.addEventListener('change',()=>{document.querySelectorAll('.rowcheck').forEach(c=>c.checked=ca.checked);up()});document.addEventListener('change',e=>{if(e.target.classList&&e.target.classList.contains('rowcheck'))up()});bb.addEventListener('click',()=>{const n=document.querySelectorAll('.rowcheck:checked').length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 data.','warning');return}Swal.fire({title:'Hapus '+n+' data?',text:'Tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)bf.submit()})});})();
</script>
<style>#postModal{z-index:50}</style>
<?php require ROOT.'/templates/admin/footer.php'; ?>




