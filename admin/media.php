<?php
declare(strict_types=1);
$title='Media'; $q=trim($_GET['q']??'');
// AJAX untuk pemilih gambar (dipakai modal berita/laman): list + upload JSON
if(($_GET['ajax']??'')==='1'&&($_GET['act']??'')==='list'){
  header('Content-Type: application/json');
  $qq=trim($_GET['qq']??''); $w=$qq?"WHERE filename LIKE ? OR mime LIKE ?":""; $pr=$qq?["%$qq%","%$qq%"]:[];
  $st=$db->prepare("SELECT id,filename,mime,size_bytes,created_at FROM media $w ORDER BY id DESC LIMIT 60"); $st->execute($pr);
  $rows=$st->fetchAll();
  foreach($rows as &$r)$r['url']=Helper::upload($r['filename']);
  echo json_encode(['ok'=>true,'items'=>$rows]); exit;
}
if($_SERVER['REQUEST_METHOD']==='POST'){
  $isAjax=($_POST['ajax']??'')==='1';
  $jerr=function($m){ header('Content-Type: application/json'); echo json_encode(['ok'=>false,'msg'=>$m]); exit; };
  if(!Security::verifyCsrf($_POST['csrf']??null)){ if($isAjax)$jerr('CSRF tidak valid.'); Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/media')); exit; }
  if(($_POST['act']??'')==='delete'){ $s=$db->prepare("SELECT * FROM media WHERE id=?"); $s->execute([(int)$_POST['id']]); $m=$s->fetch();
    if($m){ @unlink(ROOT.'/assets/uploads/'.$m['filename']); $db->prepare("DELETE FROM media WHERE id=?")->execute([$m['id']]); Auth::log($db,'delete','media','Hapus '.$m['filename']); }
    if($isAjax){ header('Content-Type: application/json'); echo json_encode(['ok'=>true]); exit; }
    Session::flash('ok','Media dihapus.');
  } elseif(($_POST['act']??'')==='bulk_delete'){
    $ids=array_values(array_unique(array_filter(array_map('intval',(array)($_POST['ids']??[])))));
    if(!$ids){ Session::flash('err','Pilih minimal 1 file.'); header('Location: '.Helper::url('admin/media')); exit; }
    $in=implode(',',array_fill(0,count($ids),'?'));
    $st=$db->prepare("SELECT * FROM media WHERE id IN ($in)"); $st->execute($ids); $found=$st->fetchAll();
    $n=0; foreach($found as $m){ @unlink(ROOT.'/assets/uploads/'.$m['filename']); $db->prepare("DELETE FROM media WHERE id=?")->execute([$m['id']]); $n++; }
    Auth::log($db,'delete','media',"Hapus massal $n media");
    Session::flash('ok',"$n file media dihapus.");
  } else {
    if(!empty($_FILES['f']['name'])){
      $e=Security::validImage($_FILES['f'],$APP);
      if($e){ if($isAjax)$jerr($e); Session::flash('err',$e); header('Location: '.Helper::url('admin/media')); exit; }
      $n=Security::safeName($_FILES['f']['name']); move_uploaded_file($_FILES['f']['tmp_name'],ROOT.'/assets/uploads/'.$n);
      $fi=new finfo(FILEINFO_MIME_TYPE);
      $mime=$fi->file(ROOT.'/assets/uploads/'.$n);
      $db->prepare("INSERT INTO media(filename,filepath,mime,extension,size_bytes,uploaded_by) VALUES(?,?,?,?,?,?)")->execute([$n,'assets/uploads/'.$n,$mime,strtolower(pathinfo($n,PATHINFO_EXTENSION)),filesize(ROOT.'/assets/uploads/'.$n),$_SESSION['user']['id']]);
      Auth::log($db,'create','media',"Upload $n");
      if($isAjax){ header('Content-Type: application/json'); echo json_encode(['ok'=>true,'filename'=>$n,'url'=>Helper::upload($n),'mime'=>$mime]); exit; }
      Session::flash('ok','Upload berhasil.');
    } elseif($isAjax)$jerr('Pilih file dulu.');
  }
  header('Location: '.Helper::url('admin/media')); exit;
}
$w=$q?"WHERE filename LIKE ? OR mime LIKE ?":""; $pr=$q?["%$q%","%$q%"]:[];
$st=$db->prepare("SELECT * FROM media $w ORDER BY id DESC LIMIT 60"); $st->execute($pr); $rows=$st->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<h1 class="text-xl font-extrabold mb-4">Media Library</h1>
<div class="bg-white rounded-2xl border p-4">
<form method="post" enctype="multipart/form-data" data-loading class="flex gap-2 mb-3 flex-wrap"><?= Security::csrfField() ?>
<input type="file" name="f" accept="image/*" required class="border rounded-lg p-2 text-sm"><button class="bg-emerald-600 text-white px-4 rounded-lg text-sm font-bold">Upload</button>
<a href="<?= Helper::url('admin/media') ?>" class="px-3 py-2 border rounded-lg text-sm">Reset</a></form>
<form class="flex gap-2 mb-3"><input name="q" value="<?= Helper::e($q) ?>" placeholder="Cari file..." class="border rounded-lg p-2 text-sm w-full"><button type="submit" title="Cari" aria-label="Cari" class="bg-slate-800 text-white px-3 rounded-lg text-sm"><i class="fa fa-search text-xs"></i></button></form>
<form method="post" id="bulkForm"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="flex items-center gap-2 mb-3 text-sm">
<label class="flex gap-1.5 items-center text-xs"><input type="checkbox" id="checkAll"> Pilih semua</label>
<span id="selCount" class="text-xs text-slate-400">0 dipilih</span>
<button type="button" id="btnBulk" class="ml-auto text-xs font-bold text-red-600 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button>
</div>
<?php if(!$rows): ?><p class="text-sm text-slate-500 text-center py-8">Belum ada media</p><?php else: ?>
<div class="grid grid-cols-2 md:grid-cols-5 gap-3"><?php $no=0; foreach($rows as $m): $no++; ?>
<div class="relative border rounded-xl overflow-hidden text-xs">
<span class="absolute top-1.5 left-1.5 w-6 h-6 rounded-lg bg-white shadow grid place-items-center text-[10px] font-bold text-slate-500"><?= $no ?></span>
<label class="absolute top-1.5 right-1.5 w-6 h-6 rounded-lg bg-white shadow grid place-items-center cursor-pointer"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= $m['id'] ?>" class="rowcheck"></label>
<img src="<?= Helper::upload($m['filename']) ?>" alt="" class="h-28 w-full object-cover" loading="lazy">
<div class="p-2"><p class="truncate font-semibold"><?= Helper::e($m['filename']) ?></p><p class="text-slate-500"><?= Helper::e($m['mime']) ?> • <?= round($m['size_bytes']/1024) ?> KB</p>
<div class="mt-1"><?= Helper::iconBtns([Helper::delBtn((int)$m['id'])]) ?></div></div></div>
<?php endforeach; ?></div><?php endif; ?></div>
<script>
(function(){
  const all=document.getElementById('checkAll'),rows=[...document.querySelectorAll('.rowcheck')],cnt=document.getElementById('selCount'),btn=document.getElementById('btnBulk'),form=document.getElementById('bulkForm');
  if(!btn||!form)return;
  const upd=()=>{const n=rows.filter(r=>r.checked).length;cnt.textContent=n+' dipilih';if(all)all.checked=rows.length>0&&n===rows.length};
  all?.addEventListener('change',()=>{rows.forEach(r=>r.checked=all.checked);upd()});
  rows.forEach(r=>r.addEventListener('change',upd));upd();
  btn.addEventListener('click',()=>{
    const n=rows.filter(r=>r.checked).length;
    if(!n){Swal.fire('Pilih dulu','Centang minimal 1 file.','warning');return}
    Swal.fire({title:'Hapus '+n+' file?',text:'File tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya, Hapus',cancelButtonText:'Batal'}).then(r=>{if(r.isConfirmed)form.submit()});
  });
})();
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>
