<?php
declare(strict_types=1);
Auth::requireRole(['administrator','editor']);
$title='Media Unduh';
$q=trim($_GET['q']??'');
$ftype=trim($_GET['type']??''); if($ftype!=='' && !in_array($ftype,['file','link'],true))$ftype='';
try { $db->exec("CREATE TABLE IF NOT EXISTS downloads (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,title VARCHAR(255) NOT NULL,doc_type ENUM('file','link') NOT NULL DEFAULT 'file',filename VARCHAR(255) DEFAULT NULL,file_url VARCHAR(500) DEFAULT NULL,mime VARCHAR(100) DEFAULT NULL,extension VARCHAR(20) DEFAULT NULL,size_bytes INT UNSIGNED DEFAULT 0,download_count INT UNSIGNED DEFAULT 0,is_active TINYINT(1) NOT NULL DEFAULT 1,uploaded_by INT UNSIGNED DEFAULT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,INDEX idx_active (is_active),INDEX idx_type (doc_type)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"); } catch (Throwable) {}
$act=$_POST['act']??'';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/downloads')); exit; }
  if($act==='delete'){
    $s=$db->prepare("SELECT * FROM downloads WHERE id=?"); $s->execute([(int)$_POST['id']]); $m=$s->fetch();
    if($m){ if($m['doc_type']==='file' && !empty($m['filename'])) @unlink(ROOT.'/assets/uploads/'.$m['filename']); $db->prepare("DELETE FROM downloads WHERE id=?")->execute([$m['id']]); Auth::log($db,'delete','downloads','Hapus '.$m['title']); Session::flash('ok','Dokumen dihapus.'); }
    header('Location: '.Helper::url('admin/downloads')); exit;
  }
  if($act==='toggle'){
    $db->prepare("UPDATE downloads SET is_active=1-is_active WHERE id=?")->execute([(int)$_POST['id']]);
    Session::flash('ok','Status diubah.'); header('Location: '.Helper::url('admin/downloads')); exit;
  }
  if($act==='bulk_delete'){
    $ids=array_values(array_unique(array_filter(array_map('intval',(array)($_POST['ids']??[])))));
    if($ids){
      $in=implode(',',array_fill(0,count($ids),'?'));
      $st=$db->prepare("SELECT * FROM downloads WHERE id IN ($in)"); $st->execute($ids); $found=$st->fetchAll();
      foreach($found as $m){ if($m['doc_type']==='file' && !empty($m['filename'])) @unlink(ROOT.'/assets/uploads/'.$m['filename']); $db->prepare("DELETE FROM downloads WHERE id=?")->execute([$m['id']]); }
      Auth::log($db,'delete','downloads','Hapus massal '.count($found).' dokumen'); Session::flash('ok',count($found).' dokumen dihapus.');
    }
    header('Location: '.Helper::url('admin/downloads')); exit;
  }
  if($act==='save'){
    $id=(int)($_POST['id']??0);
    $type=in_array($_POST['doc_type']??'file',['file','link'],true)?$_POST['doc_type']:'file';
    $title=trim($_POST['title']??''); if($title===''){ Session::flash('err','Nama dokumen wajib diisi.'); header('Location: '.Helper::url('admin/downloads'.($id?'?edit='.$id:''))); exit; }
    $filename=''; $fileUrl=''; $mime=''; $ext=''; $size=0;
    if($type==='link'){
      $fileUrl=trim($_POST['file_url']??''); if($fileUrl===''){ Session::flash('err','Tautan dokumen wajib diisi.'); header('Location: '.Helper::url('admin/downloads'.($id?'?edit='.$id:''))); exit; }
      $ext=strtolower(pathinfo(parse_url($fileUrl,PHP_URL_PATH)?:'',PATHINFO_EXTENSION));
      $mime='application/octet-stream';
    } else {
      if(!empty($_FILES['file']['name']??'')){
        $f=$_FILES['file']; $ext=strtolower(pathinfo((string)$f['name'],PATHINFO_EXTENSION));
        if(!in_array($ext,['pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar'],true)){ Session::flash('err','Format file tidak diizinkan.'); header('Location: '.Helper::url('admin/downloads'.($id?'?edit='.$id:''))); exit; }
        if(($f['size']??0)>20*1024*1024){ Session::flash('err','File maksimal 20MB.'); header('Location: '.Helper::url('admin/downloads'.($id?'?edit='.$id:''))); exit; }
        if(($f['error']??4)!==0){ Session::flash('err','Upload gagal.'); header('Location: '.Helper::url('admin/downloads'.($id?'?edit='.$id:''))); exit; }
        $orig=pathinfo((string)$f['name'],PATHINFO_FILENAME);
        $base=preg_replace('/[^\p{L}\p{N}\s._-]+/u','',$orig);
        $base=trim(preg_replace('/\s+/',' ',$base));
        if($base==='')$base='dokumen';
        $base=mb_substr($base,0,80);
        $rand=bin2hex(random_bytes(4));
        $n=$base.'-'.$rand.'.'.$ext;
        while(file_exists(ROOT.'/assets/uploads/'.$n)){$rand=bin2hex(random_bytes(6));$n=$base.'-'.$rand.'.'.$ext;}
        move_uploaded_file($f['tmp_name'],ROOT.'/assets/uploads/'.$n);
        $fi=new finfo(FILEINFO_MIME_TYPE); $mime=$fi->file(ROOT.'/assets/uploads/'.$n) ?: 'application/octet-stream';
        $filename=$n; $size=filesize(ROOT.'/assets/uploads/'.$n);
      } elseif($id){
        $old=$db->prepare("SELECT filename,file_url,extension,mime,size_bytes FROM downloads WHERE id=?")->execute([$id]); $old=$old->fetch();
        if($old){ $filename=$old['filename']; $fileUrl=$old['file_url']; $ext=$old['extension']; $mime=$old['mime']; $size=(int)$old['size_bytes']; }
      } else { Session::flash('err','File wajib diupload.'); header('Location: '.Helper::url('admin/downloads')); exit; }
    }
    if($id){
      if($type==='file' && $filename!=='' && $fileUrl!=='') $fileUrl='';
      if($type==='link' && $filename!==''){ @unlink(ROOT.'/assets/uploads/'.$filename); $filename=''; }
      $db->prepare("UPDATE downloads SET title=?,doc_type=?,filename=?,file_url=?,mime=?,extension=?,size_bytes=? WHERE id=?")->execute([$title,$type,$filename?:null,$fileUrl?:null,$mime,$ext,$size,$id]);
      Auth::log($db,'update','downloads','Ubah '.$title); Session::flash('ok','Dokumen diubah.');
    } else {
      $db->prepare("INSERT INTO downloads(title,doc_type,filename,file_url,mime,extension,size_bytes,uploaded_by) VALUES(?,?,?,?,?,?,?,?)")->execute([$title,$type,$filename?:null,$fileUrl?:null,$mime,$ext,$size,$_SESSION['user']['id']??null]);
      Auth::log($db,'create','downloads','Tambah '.$title); Session::flash('ok','Dokumen ditambah.');
    }
    header('Location: '.Helper::url('admin/downloads')); exit;
  }
}
$w=[]; $pr=[];
if($q){ $w[]="title LIKE ?"; $pr[]="%$q%"; }
if($ftype){ $w[]="doc_type=?"; $pr[]=$ftype; }
$where=$w?('WHERE '.implode(' AND ',$w)):'';
$st=$db->prepare("SELECT * FROM downloads $where ORDER BY created_at DESC"); $st->execute($pr); $rows=$st->fetchAll();
function fmtSize(int $b):string{ if($b<1024)return $b.' B'; if($b<1024*1024)return round($b/1024,1).' KB'; return round($b/(1024*1024),1).' MB'; }
require ROOT.'/templates/admin/header.php'; ?>
<h1 class="text-xl font-extrabold mb-4"><i class="fa fa-file-arrow-down text-emerald-600 mr-1"></i>Media Unduh</h1>
<section class="bg-white rounded-2xl border p-4 grid gap-3">
<div class="flex flex-wrap items-center gap-2">
<form method="get" id="filterForm" class="flex gap-2 flex-1"><input name="q" id="filterQ" value="<?= Helper::e($q) ?>" placeholder="Cari dokumen..." class="border rounded-lg p-2 text-sm flex-1"><select name="type" id="filterType" class="border rounded-lg p-2 text-sm"><option value="">Semua</option><option value="file" <?= $ftype==='file'?'selected':'' ?>>File</option><option value="link" <?= $ftype==='link'?'selected':'' ?>>Tautan</option></select><a href="<?= Helper::url('admin/downloads') ?>" class="px-3 py-2 border rounded-lg text-sm">Reset</a></form>
<button type="button" id="btnAddDoc" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-sm font-bold"><i class="fa fa-plus mr-1"></i>Tambah Dokumen</button>
</div>
<form method="post" id="bulkForm" class="hidden"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="overflow-x-auto">
<table class="w-full text-sm text-left">
<thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="p-3 w-10"><input type="checkbox" id="checkAll"></th><th class="p-3">No</th><th class="p-3">Nama Dokumen</th><th class="p-3">Ukuran</th><th class="p-3">Tanggal</th><th class="p-3">Aksi</th></tr></thead>
<tbody class="divide-y"><?php if(!$rows): ?><tr><td colspan="6" class="p-6 text-center text-slate-500">Belum ada dokumen.</td></tr><?php else: $no=0; foreach($rows as $r): $no++; ?>
<tr class="hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= (int)$r['id'] ?>" class="rowcheck"></td>
<td class="p-3"><?= $no ?></td>
<?php $dlIcon=($r['doc_type']==='link')?'fa-link text-sky-600':(match($r['extension']??''){'pdf'=>'fa-file-pdf text-red-600','doc'=>'fa-file-word text-blue-600','docx'=>'fa-file-word text-blue-600','xls'=>'fa-file-excel text-green-600','xlsx'=>'fa-file-excel text-green-600','ppt'=>'fa-file-powerpoint text-orange-600','pptx'=>'fa-file-powerpoint text-orange-600','zip'=>'fa-file-zipper text-amber-600','rar'=>'fa-file-zipper text-amber-600',default=>'fa-file text-slate-500'}); ?>
<td class="p-3"><div class="flex items-center gap-2"><i class="fa <?= $dlIcon ?> text-lg"></i><div><p class="font-bold"><?= Helper::e($r['title']) ?></p><p class="text-xs text-slate-400"><?= Helper::e($r['extension']?:($r['doc_type']==='link'?'URL':'file')) ?> • <?= (int)$r['download_count'] ?> unduhan</p></div></div></td>
<td class="p-3 whitespace-nowrap"><?= $r['doc_type']==='file'?fmtSize((int)$r['size_bytes']):'-' ?></td>
<td class="p-3 whitespace-nowrap"><?= Helper::tgl($r['created_at']) ?></td>
<td class="p-3"><div class="flex items-center gap-1">
<?php
$targetUrl = $r['doc_type']==='file' ? Helper::upload($r['filename']) : Helper::e($r['file_url']);
$ext = strtolower($r['extension'] ?? '');
$isPdf = ($r['doc_type']==='file' && $ext==='pdf');
$previewUrl = $isPdf ? Helper::url('media-unduh/preview?file=' . urlencode($r['filename']) . '&name=' . urlencode($r['title'])) : $targetUrl;
?>
<a href="<?= $previewUrl ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 inline-flex items-center justify-center rounded-lg border bg-white text-slate-600 hover:text-emerald-600" title="Lihat / Unduh"><i class="fa fa-eye text-xs"></i></a>
<button type="button" class="w-8 h-8 inline-flex items-center justify-center rounded-lg border bg-white text-slate-600 hover:text-emerald-600 btnEditDoc" title="Edit" data-item="<?= Helper::e(json_encode(['id'=>$r['id'],'title'=>$r['title'],'doc_type'=>$r['doc_type'],'file_url'=>$r['file_url'],'filename'=>$r['filename']],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) ?>"><i class="fa fa-pen text-xs"></i></button>
<form method="post" class="inline"><?= Security::csrfField() ?><input type="hidden" name="act" value="toggle"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="w-8 h-8 inline-flex items-center justify-center rounded-lg border bg-white <?= $r['is_active']?'text-emerald-600':'text-slate-400' ?>" title="On/Off"><i class="fa <?= $r['is_active']?'fa-eye':'fa-eye-slash' ?> text-xs"></i></button></form>
<form method="post" class="inline" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="w-8 h-8 inline-flex items-center justify-center rounded-lg border bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</div></td>
</tr>
<?php endforeach; endif; ?></tbody>
</table>
</div>
<div class="flex items-center gap-2 text-sm"><span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700"><i class="fa fa-file-lines mr-1"></i><?= count($rows) ?> dokumen</span><span id="selCount" class="text-xs text-slate-400">0 dipilih</span><button type="button" id="btnBulk" class="ml-auto text-xs font-bold text-red-600 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<p class="text-xs text-slate-400"><i class="fa fa-circle-info mr-1"></i>Halaman publik dapat diakses di <code class="bg-slate-100 px-1 rounded">/media-unduh</code></p>
</section>
<form method="post" enctype="multipart/form-data" id="docModalForm" class="hidden"><?= Security::csrfField() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" id="modalDocId" value="0"></form>
<script>
const DOC_ICON={file:'fa-file-pdf text-red-600',link:'fa-link text-sky-600'};
function docModalHtml(item){
  item=item||{id:0,title:'',doc_type:'file',file_url:'',filename:''};
  const isFile=item.doc_type==='file';
  return `<div class="grid gap-3 text-left text-sm" id="docModalBody">
    <input type="hidden" id="swalDocId" value="${item.id}">
    <input type="hidden" id="swalDocType" value="${item.doc_type||'file'}">
    <label class="grid gap-1 font-semibold">Nama dokumen<input id="swalTitle" value="${item.title.replace(/"/g,'&quot;')}" placeholder="Pedoman Akademik 2026" class="border rounded-lg p-2 font-normal"></label>
    <div class="grid gap-1">
      <span class="font-semibold">Jenis dokumen</span>
      <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl">
        <button type="button" data-dt="file" class="doc-type-btn flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition ${isFile?'bg-white text-emerald-700 shadow':'text-slate-500 hover:text-slate-700'}"><i class="fa fa-file-arrow-up"></i>Upload File</button>
        <button type="button" data-dt="link" class="doc-type-btn flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition ${!isFile?'bg-white text-emerald-700 shadow':'text-slate-500 hover:text-slate-700'}"><i class="fa fa-link"></i>Tautan</button>
      </div>
    </div>
    <div id="swalFileBox" class="grid gap-1" style="${!isFile?'display:none':''}">
      <label class="grid gap-1 font-semibold">File <span class="font-normal text-slate-400">(PDF/DOC/XLS/PPT/ZIP/RAR, max 20MB)</span><input type="file" id="swalFile" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar" class="border rounded-lg p-2 font-normal bg-white"></label>
      ${item.filename?'<p class="text-xs text-slate-500">File saat ini: <a href="<?= Helper::url('assets/uploads') ?>'+item.filename+'" target="_blank" class="text-emerald-700 font-bold hover:underline">'+item.filename+'</a></p>':''}
    </div>
    <div id="swalLinkBox" class="grid gap-1" style="${isFile?'display:none':''}">
      <label class="grid gap-1 font-semibold">URL tautan<input type="url" id="swalFileUrl" value="${(item.file_url||'').replace(/"/g,'&quot;')}" placeholder="https://drive.google.com/..." class="border rounded-lg p-2 font-normal"></label>
    </div>
  </div>`;
}
function bindDocType(){
  document.querySelectorAll('#docModalBody .doc-type-btn').forEach(b=>b.addEventListener('click',()=>{
    const v=b.dataset.dt; document.getElementById('swalDocType').value=v;
    document.querySelectorAll('#docModalBody .doc-type-btn').forEach(x=>{
      const active=x.dataset.dt===v;
      x.className='doc-type-btn flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition '+(active?'bg-white text-emerald-700 shadow':'text-slate-500 hover:text-slate-700');
    });
    document.getElementById('swalFileBox').style.display=v==='file'?'':'none';
    document.getElementById('swalLinkBox').style.display=v==='link'?'':'none';
  }));
}
function saveDoc(){
  const id=parseInt(document.getElementById('swalDocId').value||'0',10);
  const title=document.getElementById('swalTitle').value.trim();
  const docType=document.getElementById('swalDocType').value;
  const fileUrl=document.getElementById('swalFileUrl').value.trim();
  const fileInput=document.getElementById('swalFile');
  if(!title){Swal.showValidationMessage('Nama dokumen wajib diisi');return false;}
  if(docType==='link'&&!fileUrl){Swal.showValidationMessage('Tautan wajib diisi');return false;}
  if(docType==='file'&&!id&&!fileInput.files.length){Swal.showValidationMessage('File wajib diupload');return false;}
  const fd=new FormData(document.getElementById('docModalForm'));
  fd.set('id',id); fd.set('title',title); fd.set('doc_type',docType); fd.set('file_url',fileUrl);
  if(fileInput&&fileInput.files.length)fd.append('file',fileInput.files[0]);
  fetch('<?= Helper::url('admin/downloads') ?>',{method:'POST',body:fd}).then(r=>{if(r.ok)location.reload();else Swal.fire('Gagal','Simpan gagal.','error');}).catch(()=>Swal.fire('Gagal','Koneksi gagal.','error'));
  return false;
}
(function(){
  const f=document.getElementById('filterForm');if(!f)return;
  let t=null;
  f.querySelector('#filterQ')?.addEventListener('input',()=>{clearTimeout(t);t=setTimeout(()=>f.submit(),500)});
  f.querySelector('#filterType')?.addEventListener('change',()=>f.submit());
})();
document.getElementById('btnAddDoc')?.addEventListener('click',()=>{
  Swal.fire({title:'Tambah Dokumen',html:docModalHtml(),showCancelButton:true,confirmButtonText:'Tambah',cancelButtonText:'Batal',confirmButtonColor:'#059669',didOpen:bindDocType,preConfirm:saveDoc});
});
document.querySelectorAll('.btnEditDoc').forEach(b=>b.addEventListener('click',()=>{
  const item=JSON.parse(b.dataset.item);
  Swal.fire({title:'Edit Dokumen',html:docModalHtml(item),showCancelButton:true,confirmButtonText:'Simpan',cancelButtonText:'Batal',confirmButtonColor:'#059669',didOpen:bindDocType,preConfirm:saveDoc});
}));
(function(){
  const all=document.getElementById('checkAll'),rows=[...document.querySelectorAll('.rowcheck')],cnt=document.getElementById('selCount'),btn=document.getElementById('btnBulk'),form=document.getElementById('bulkForm');
  if(!rows.length)return;
  const upd=()=>{const n=rows.filter(r=>r.checked).length;cnt.textContent=n+' dipilih';if(all)all.checked=rows.length>0&&n===rows.length};
  all?.addEventListener('change',()=>{rows.forEach(r=>r.checked=all.checked);upd()});
  rows.forEach(r=>r.addEventListener('change',upd));upd();
  btn?.addEventListener('click',()=>{const n=rows.filter(r=>r.checked).length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 dokumen.','warning');return} Swal.fire({title:'Hapus '+n+' dokumen?',text:'Data tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya, Hapus',cancelButtonText:'Batal'}).then(r=>{if(r.isConfirmed)form.submit()})});
})();
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>
