<?php
declare(strict_types=1);
$title='Media'; $q=trim($_GET['q']??''); $fdate=trim($_GET['date']??''); $fmonth=trim($_GET['month']??''); $fyear=trim($_GET['year']??'');
$view=in_array($_GET['view']??($_COOKIE['media_view']??'grid'),['grid','list'],true)?($_GET['view']??($_COOKIE['media_view']??'grid')):'grid';
if(isset($_GET['view'])) setcookie('media_view',$view,time()+30*86400,'/');
$thumb=in_array($_GET['thumb']??($_COOKIE['media_thumb']??'md'),['sm','md','lg'],true)?($_GET['thumb']??($_COOKIE['media_thumb']??'md')):'md';
if(isset($_GET['thumb'])) setcookie('media_thumb',$thumb,time()+30*86400,'/');
$thumbGrid=['sm'=>'grid-cols-3 md:grid-cols-6 lg:grid-cols-8','md'=>'grid-cols-2 md:grid-cols-5','lg'=>'grid-cols-1 sm:grid-cols-2 md:grid-cols-3'][$thumb];
$thumbH=['sm'=>'h-16','md'=>'h-28','lg'=>'h-44'][$thumb];
// AJAX untuk pemilih gambar (dipakai modal berita/laman): list + upload JSON
if(($_GET['ajax']??'')==='1'&&($_GET['act']??'')==='list'){
  header('Content-Type: application/json');
  $qq=trim($_GET['qq']??''); $w=[]; $pr=[];
  if($qq){$w[]="(filename LIKE ? OR mime LIKE ?)";$pr[]="%$qq%";$pr[]="%$qq%";}
  $w=$w?('WHERE '.implode(' AND ',$w)):'';
  $st=$db->prepare("SELECT id,filename,mime,size_bytes,created_at FROM media $w ORDER BY id DESC LIMIT 60"); $st->execute($pr);
  $rows=$st->fetchAll();
  foreach($rows as &$r)$r['url']=Helper::upload($r['filename']);
  echo json_encode(['ok'=>true,'items'=>$rows]); exit;
}
if($_SERVER['REQUEST_METHOD']==='POST'){
  $isAjax=($_POST['ajax']??'')==='1';
  $jerr=function($m){ header('Content-Type: application/json'); echo json_encode(['ok'=>false,'msg'=>$m]); exit; };
  if(!Security::verifyCsrf($_POST['csrf']??null)){ if($isAjax)$jerr('CSRF tidak valid.'); Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/media')); exit; }
  if(($_POST['act']??'')==='ckeditor'){
    $f=$_FILES['upload']??null;
    $jok=function($url){ header('Content-Type: application/json'); echo json_encode(['uploaded'=>1,'fileName'=>basename($url),'url'=>$url]); exit; };
    if(!$f||(($f['error']??4)!==0))$jerr('Upload gagal.');
    $e=Security::validImage($f,$APP);
    if($e)$jerr($e);
    $n=Security::safeName($f['name']); move_uploaded_file($f['tmp_name'],ROOT.'/assets/uploads/'.$n);
    $fi=new finfo(FILEINFO_MIME_TYPE);
    $mime=$fi->file(ROOT.'/assets/uploads/'.$n);
    $db->prepare("INSERT INTO media(filename,filepath,mime,extension,size_bytes,uploaded_by) VALUES(?,?,?,?,?,?)")->execute([$n,'assets/uploads/'.$n,$mime,strtolower(pathinfo($n,PATHINFO_EXTENSION)),filesize(ROOT.'/assets/uploads/'.$n),$_SESSION['user']['id']]);
    Auth::log($db,'create','media',"Upload CKEditor $n");
    $jok(Helper::upload($n));
  }
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
$synced=0;
try {
  $db->exec("CREATE TABLE IF NOT EXISTS downloads (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,title VARCHAR(255) NOT NULL,doc_type ENUM('file','link') NOT NULL DEFAULT 'file',filename VARCHAR(255) DEFAULT NULL,file_url VARCHAR(500) DEFAULT NULL,mime VARCHAR(100) DEFAULT NULL,extension VARCHAR(20) DEFAULT NULL,size_bytes INT UNSIGNED DEFAULT 0,download_count INT UNSIGNED DEFAULT 0,is_active TINYINT(1) NOT NULL DEFAULT 1,uploaded_by INT UNSIGNED DEFAULT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,INDEX idx_active (is_active),INDEX idx_type (doc_type)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  $have=$db->query("SELECT filename FROM media")->fetchAll(PDO::FETCH_COLUMN);
  $haveMap=[]; foreach($have as $h)$haveMap[$h]=true;
  $docs=$db->query("SELECT * FROM downloads WHERE doc_type='file' AND filename IS NOT NULL AND filename<>''")->fetchAll();
  foreach($docs as $d){
    if(isset($haveMap[$d['filename']])) continue;
    $f=ROOT.'/assets/uploads/'.basename($d['filename']);
    if(!is_file($f)) continue;
    $sz=(int)($d['size_bytes']?:filesize($f));
    $db->prepare("INSERT INTO media(filename,filepath,mime,extension,size_bytes,alt,uploaded_by) VALUES(?,?,?,?,?,?,?)")->execute([$d['filename'],'assets/uploads/'.$d['filename'],$d['mime']?:'application/octet-stream',$d['extension']?:strtolower(pathinfo($d['filename'],PATHINFO_EXTENSION)),$sz,$d['title'],$d['uploaded_by']]);
    $haveMap[$d['filename']]=true;
    $synced++;
  }
} catch (Throwable) {}
$w=[]; $pr=[];
if($q){$w[]="(filename LIKE ? OR mime LIKE ?)";$pr[]="%$q%";$pr[]="%$q%";}
if(preg_match('/^\d{4}-\d{2}-\d{2}$/',$fdate)){$w[]="DATE(created_at)=?";$pr[]=$fdate;}
if(preg_match('/^\d{4}-\d{2}$/',$fmonth)){$w[]="DATE_FORMAT(created_at,'%Y-%m')=?";$pr[]=$fmonth;}
if(preg_match('/^\d{4}$/',$fyear)){$w[]="YEAR(created_at)=?";$pr[]=$fyear;}
$w=$w?('WHERE '.implode(' AND ',$w)):'';
$st=$db->prepare("SELECT * FROM media $w ORDER BY id DESC LIMIT 200"); $st->execute($pr); $rows=$st->fetchAll();
try{ $totalMedia=(int)$db->query("SELECT COUNT(*) FROM media")->fetchColumn(); }catch(Throwable){ $totalMedia=count($rows); }
try{ $years=$db->query("SELECT DISTINCT YEAR(created_at) y FROM media ORDER BY y DESC")->fetchAll(PDO::FETCH_COLUMN); }catch(Throwable){ $years=[]; }
require ROOT.'/templates/admin/header.php'; ?>
<h1 class="text-xl font-extrabold mb-4">Media Library</h1>
<div class="bg-white rounded-2xl border p-4">
<form method="post" enctype="multipart/form-data" data-loading class="flex gap-2 mb-3 flex-wrap"><?= Security::csrfField() ?>
<input type="file" name="f" accept="image/*" required class="border rounded-lg p-2 text-sm"><button class="bg-emerald-600 text-white px-4 rounded-lg text-sm font-bold">Upload</button>
<a href="<?= Helper::url('admin/media') ?>" class="px-3 py-2 border rounded-lg text-sm">Reset</a></form>
<form id="mediaFilter" class="grid sm:grid-cols-2 lg:grid-cols-5 gap-2 mb-3"><input name="q" id="fq" value="<?= Helper::e($q) ?>" placeholder="Cari file..." class="border rounded-lg p-2 text-sm"><input type="date" name="date" id="fdate" value="<?= Helper::e($fdate) ?>" title="Filter tanggal" class="border rounded-lg p-2 text-sm"><input type="month" name="month" id="fmonth" value="<?= Helper::e($fmonth) ?>" title="Filter bulan" class="border rounded-lg p-2 text-sm"><select name="year" id="fyear" class="border rounded-lg p-2 text-sm"><option value="">Semua tahun</option><?php foreach($years as $y): ?><option value="<?= $y ?>" <?= (string)$fyear===(string)$y?'selected':'' ?>><?= $y ?></option><?php endforeach; ?></select><a href="<?= Helper::url('admin/media') ?>" class="px-3 py-2 border rounded-lg text-sm text-center" title="Reset">Reset</a></form>
<script>
(function(){
  const f=document.getElementById('mediaFilter');if(!f)return;
  let t=null;
  const go=()=>f.submit();
  f.querySelector('#fq')?.addEventListener('input',()=>{clearTimeout(t);t=setTimeout(go,500)});
  ['fdate','fmonth','fyear'].forEach(id=>f.querySelector('#'+id)?.addEventListener('change',go));
})();
</script>
<form method="post" id="bulkForm"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="flex items-center gap-2 mb-3 text-sm flex-wrap">
<label class="flex gap-1.5 items-center text-xs"><input type="checkbox" id="checkAll"> Pilih semua</label>
<span id="selCount" class="text-xs text-slate-400">0 dipilih</span>
<span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700"><i class="fa fa-photo-film mr-1"></i><?= $totalMedia ?> media</span>
<span class="inline-flex rounded-lg border overflow-hidden ml-1" title="Tampilan">
<?php $qs=http_build_query(array_filter(['q'=>$q,'date'=>$fdate,'month'=>$fmonth,'year'=>$fyear,'thumb'=>$thumb])); $qs=$qs?'&'.$qs:''; ?>
<a href="<?= Helper::url('admin/media?view=grid'.$qs) ?>" class="px-2 py-1.5 text-xs <?= $view==='grid'?'bg-emerald-600 text-white':'bg-white text-slate-500 hover:text-emerald-600' ?>" title="Thumbnail"><i class="fa fa-grip"></i></a>
<a href="<?= Helper::url('admin/media?view=list'.$qs) ?>" class="px-2 py-1.5 text-xs <?= $view==='list'?'bg-emerald-600 text-white':'bg-white text-slate-500 hover:text-emerald-600' ?>" title="List"><i class="fa fa-list"></i></a>
</span>
<?php if($view==='grid'): ?>
<span class="inline-flex rounded-lg border overflow-hidden" title="Ukuran thumbnail">
<?php $qs2=http_build_query(array_filter(['q'=>$q,'date'=>$fdate,'month'=>$fmonth,'year'=>$fyear,'view'=>$view])); $qs2=$qs2?'&'.$qs2:''; ?>
<a href="<?= Helper::url('admin/media?thumb=sm'.$qs2) ?>" class="px-2 py-1.5 text-xs <?= $thumb==='sm'?'bg-emerald-600 text-white':'bg-white text-slate-500 hover:text-emerald-600' ?>" title="Kecil">S</a>
<a href="<?= Helper::url('admin/media?thumb=md'.$qs2) ?>" class="px-2 py-1.5 text-xs <?= $thumb==='md'?'bg-emerald-600 text-white':'bg-white text-slate-500 hover:text-emerald-600' ?>" title="Medium">M</a>
<a href="<?= Helper::url('admin/media?thumb=lg'.$qs2) ?>" class="px-2 py-1.5 text-xs <?= $thumb==='lg'?'bg-emerald-600 text-white':'bg-white text-slate-500 hover:text-emerald-600' ?>" title="Besar">L</a>
</span>
<?php endif; ?>
<button type="button" id="btnBulk" class="ml-auto text-xs font-bold text-red-600 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button>
</div>
<?= $synced>0 ? '<div class="mb-3 text-xs font-bold px-3 py-2 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700"><i class="fa fa-check-circle mr-1"></i>'.$synced.' dokumen dari Media Unduh disinkronkan ke Media.</div>' : '' ?>
<?php $docIcon=['pdf'=>'fa-file-pdf text-red-600','doc'=>'fa-file-word text-blue-600','docx'=>'fa-file-word text-blue-600','xls'=>'fa-file-excel text-green-600','xlsx'=>'fa-file-excel text-green-600','ppt'=>'fa-file-powerpoint text-orange-600','pptx'=>'fa-file-powerpoint text-orange-600','zip'=>'fa-file-zipper text-amber-600','rar'=>'fa-file-zipper text-amber-600']; ?>
<?php if(!$rows): ?><p class="text-sm text-slate-500 text-center py-8">Belum ada media</p>
<?php elseif($view==='list'): ?>
<div class="overflow-x-auto"><table class="w-full text-sm text-left">
<thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="p-3 w-10"><input type="checkbox" id="checkAllList"></th><th class="p-3">No</th><th class="p-3">Pratinjau</th><th class="p-3">Nama</th><th class="p-3">Ukuran</th><th class="p-3">Tanggal</th><th class="p-3">Aksi</th></tr></thead>
<tbody class="divide-y"><?php $no=0; foreach($rows as $m): $no++; $mExt=strtolower($m['extension']??''); $isImg=str_starts_with($m['mime']??'','image/'); ?>
<tr class="hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= $m['id'] ?>" class="rowcheck"></td>
<td class="p-3"><?= $no ?></td>
<td class="p-3"><?php if($isImg): ?><img src="<?= Helper::upload($m['filename']) ?>" alt="" class="h-10 w-14 object-cover rounded-lg border" loading="lazy"><?php else: ?><span class="h-10 w-14 grid place-items-center rounded-lg border bg-slate-50"><i class="fa <?= $docIcon[$mExt]??'fa-file text-slate-400' ?> text-xl"></i></span><?php endif; ?></td>
<td class="p-3"><p class="font-bold"><?= Helper::e($m['alt']?:$m['filename']) ?></p><p class="text-xs text-slate-400"><?= Helper::e($m['mime']) ?> • .<?= Helper::e($mExt) ?></p></td>
<td class="p-3 whitespace-nowrap"><?= round($m['size_bytes']/1024) ?> KB</td>
<td class="p-3 whitespace-nowrap"><?= Helper::tgl($m['created_at']) ?></td>
<td class="p-3"><div class="flex items-center gap-1"><a href="<?= Helper::upload($m['filename']) ?>" target="_blank" class="w-8 h-8 inline-flex items-center justify-center rounded-lg border bg-white text-slate-600 hover:text-emerald-600" title="Lihat / Unduh"><i class="fa fa-eye text-xs"></i></a><?= Helper::iconBtns([Helper::delBtn((int)$m['id'])]) ?></div></td>
</tr>
<?php endforeach; ?></tbody></table></div>
<?php else: ?>
<div class="grid <?= $thumbGrid ?> gap-3"><?php $no=0; foreach($rows as $m): $no++; $mExt=strtolower($m['extension']??''); $isImg=str_starts_with($m['mime']??'','image/'); ?>
<div class="relative border rounded-xl overflow-hidden text-xs">
<span class="absolute top-1.5 left-1.5 w-6 h-6 rounded-lg bg-white shadow grid place-items-center text-[10px] font-bold text-slate-500"><?= $no ?></span>
<label class="absolute top-1.5 right-1.5 w-6 h-6 rounded-lg bg-white shadow grid place-items-center cursor-pointer"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= $m['id'] ?>" class="rowcheck"></label>
<?php if($isImg): ?><img src="<?= Helper::upload($m['filename']) ?>" alt="" class="<?= $thumbH ?> w-full object-cover" loading="lazy">
<?php else: ?><a href="<?= Helper::upload($m['filename']) ?>" target="_blank" class="<?= $thumbH ?> w-full grid place-items-center bg-slate-50 hover:bg-emerald-50 transition"><i class="fa <?= $docIcon[$mExt]??'fa-file text-slate-400' ?> text-4xl"></i></a><?php endif; ?>
<div class="p-2"><?php if($isImg): ?><p class="truncate font-semibold" title="<?= Helper::e($m['filename']) ?>"><?= Helper::e($m['alt']?:$m['filename']) ?></p><p class="text-slate-500"><?= Helper::e($m['mime']) ?> • <?= round($m['size_bytes']/1024) ?> KB</p><?php else: ?><p class="font-semibold leading-snug line-clamp-2 min-h-8" title="<?= Helper::e($m['alt']?:$m['filename']) ?>"><?= Helper::e($m['alt']?:$m['filename']) ?></p><?php endif; ?>
<div class="mt-1 flex items-center gap-1"><?php if(!$isImg): ?><a href="<?= Helper::upload($m['filename']) ?>" target="_blank" class="w-8 h-8 inline-flex items-center justify-center rounded-lg border bg-white text-slate-600 hover:text-emerald-600" title="Lihat / Unduh"><i class="fa fa-eye text-xs"></i></a><?php endif; ?><?= Helper::iconBtns([Helper::delBtn((int)$m['id'])]) ?></div></div></div>
<?php endforeach; ?></div><?php endif; ?></div>
<script>
(function(){
  const all=document.getElementById('checkAll'),allList=document.getElementById('checkAllList'),rows=[...document.querySelectorAll('.rowcheck')],cnt=document.getElementById('selCount'),btn=document.getElementById('btnBulk'),form=document.getElementById('bulkForm');
  if(!btn||!form)return;
  const upd=()=>{const n=rows.filter(r=>r.checked).length;cnt.textContent=n+' dipilih';if(all)all.checked=rows.length>0&&n===rows.length;if(allList)allList.checked=rows.length>0&&n===rows.length};
  all?.addEventListener('change',()=>{rows.forEach(r=>r.checked=all.checked);upd()});
  allList?.addEventListener('change',()=>{rows.forEach(r=>r.checked=allList.checked);upd()});
  rows.forEach(r=>r.addEventListener('change',upd));upd();
  btn.addEventListener('click',()=>{
    const n=rows.filter(r=>r.checked).length;
    if(!n){Swal.fire('Pilih dulu','Centang minimal 1 file.','warning');return}
    Swal.fire({title:'Hapus '+n+' file?',text:'File tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya, Hapus',cancelButtonText:'Batal'}).then(r=>{if(r.isConfirmed)form.submit()});
  });
})();
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>




