<?php declare(strict_types=1); $title='Pengumuman'; $edit=null;
if(isset($_GET['edit'])){ $s=$db->prepare("SELECT * FROM announcements WHERE id=?"); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/announcements')); exit; }
$act=$_POST['act']??'save';
if($act==='bulk_delete'){ $ids=array_filter(array_map('intval',(array)($_POST['ids']??[]))); if(!$ids){ Session::flash('err','Pilih minimal 1 data.'); } else { $ph=implode(',',array_fill(0,count($ids),'?')); $db->prepare("DELETE FROM announcements WHERE id IN ($ph)")->execute(array_values($ids)); Auth::log($db,'delete','announcements','Hapus bulk pengumuman'); Session::flash('ok',count($ids).' data dihapus.'); } }
elseif($act==='delete'){ $db->prepare("DELETE FROM announcements WHERE id=?")->execute([(int)$_POST['id']]); Auth::log($db,'delete','announcements','Hapus pengumuman'); Session::flash('ok','Dihapus.'); }
else{ $t=trim($_POST['title']??''); if($t===''){ Session::flash('err','Judul wajib.'); } else {
$att=$_POST['old_att']??null; if(!empty($_FILES['att']['name']??'')){ $n=Security::safeName($_FILES['att']['name']); move_uploaded_file($_FILES['att']['tmp_name'],ROOT.'/assets/uploads/'.$n); $att='assets/uploads/'.$n; }
if(!empty($_POST['id'])) $db->prepare("UPDATE announcements SET title=?,content=?,attachment=?,status=?,published_at=? WHERE id=?")->execute([$t,$_POST['content']??'',$att,$_POST['status']??'published',$_POST['published_at']?:date('Y-m-d H:i:s'),(int)$_POST['id']]);
else $db->prepare("INSERT INTO announcements(title,content,attachment,status,published_at,author_id) VALUES(?,?,?,?,?,?)")->execute([$t,$_POST['content']??'',$att,$_POST['status']??'published',$_POST['published_at']?:date('Y-m-d H:i:s'),$_SESSION['user']['id']]);
Auth::log($db,'save','announcements',"Simpan $t"); Session::flash('ok','Disimpan.'); } }
header('Location: '.Helper::url('admin/announcements')); exit; }
$rows=$db->query("SELECT * FROM announcements ORDER BY id DESC")->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-bullhorn text-emerald-600 mr-1"></i>Pengumuman</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($rows) ?> pengumuman</span>
<button id="btnAdd" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Pengumuman</button>
</div>
<form method="post" id="bulkForm"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b text-sm"><span id="selCount" class="text-slate-500">0 dipilih</span><button type="button" id="btnBulkAnn" class="ml-auto bg-red-600 hover:bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[640px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-8"><input type="checkbox" id="checkAllAnn"></th><th class="p-3 w-10">No</th><th class="p-3">Judul</th><th class="p-3">Status</th><th class="p-3">Tanggal</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$rows): ?><tr><td colspan="6" class="p-10 text-center text-slate-500"><i class="fa fa-bullhorn text-3xl block mb-2"></i>Belum ada pengumuman. Klik Tambah Pengumuman.</td></tr><?php endif; ?>
<?php $no=1; foreach($rows as $r): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= $r['id'] ?>" class="rowcheck"></td><td class="p-3 text-slate-500"><?= $no++ ?></td><td class="p-3 font-semibold"><?= Helper::e($r['title']) ?><span class="block text-[11px] font-normal text-slate-400"><?= Helper::e(Helper::excerpt($r['content']??'',80)) ?></span></td>
<td class="p-3"><span class="text-xs font-bold px-2 py-0.5 rounded-full <?= ($r['status']??'')==='published'?'bg-emerald-100 text-emerald-700':'bg-amber-100 text-amber-700' ?>"><?= Helper::e($r['status']) ?></span></td>
<td class="p-3 text-xs text-slate-500 whitespace-nowrap"><?= Helper::e($r['published_at']??'') ?></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$r['id'],'title'=>$r['title'],'content'=>$r['content']??'','attachment'=>$r['attachment']??'','status'=>$r['status'],'published_at'=>$r['published_at']??'']),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>

<div id="annModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl my-4 overflow-hidden">
<div class="flex items-center gap-2 px-5 py-3.5 border-b bg-white"><h2 class="font-extrabold" id="modalTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Pengumuman</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" enctype="multipart/form-data" data-loading class="p-5 grid gap-3 text-sm bg-white"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="f_id" value="0"><input type="hidden" name="old_att" id="f_old" value="">
<label class="grid gap-1 font-semibold">Judul<input name="title" id="f_title" required placeholder="Judul pengumuman" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Isi<textarea name="content" id="f_content" rows="5" placeholder="Isi pengumuman..." class="border rounded-lg p-2 font-normal"></textarea></label>
<div class="border rounded-xl p-3 bg-slate-50"><p class="text-xs font-bold mb-1.5"><i class="fa fa-paperclip mr-1 text-emerald-600"></i>Lampiran</p>
<a id="f_att_link" href="#" target="_blank" rel="noopener noreferrer" class="hidden text-xs text-sky-600 underline break-all mb-1.5 block">Lihat file lama</a>
<input type="file" name="att" id="f_att" class="border rounded-lg p-2 w-full bg-white text-xs"></div>
<div class="grid grid-cols-2 gap-3">
<label class="grid gap-1 font-semibold">Status<select name="status" id="f_status" class="border rounded-lg p-2 font-normal"><option value="published">published</option><option value="draft">draft</option></select></label>
<label class="grid gap-1 font-semibold">Tanggal<input type="datetime-local" name="published_at" id="f_date" class="border rounded-lg p-2 font-normal"></label>
</div>
<div class="flex gap-2 bg-white pt-2 pb-1"><button class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl p-2.5 font-bold"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<script>
(function(){const ca=document.getElementById('checkAllAnn'),sc=document.getElementById('selCount'),bb=document.getElementById('btnBulkAnn'),bf=document.getElementById('bulkForm');if(!ca||!bb||!bf)return;const up=()=>{sc.textContent=document.querySelectorAll('.rowcheck:checked').length+' dipilih'};ca.addEventListener('change',()=>{document.querySelectorAll('.rowcheck').forEach(c=>c.checked=ca.checked);up()});document.addEventListener('change',e=>{if(e.target.classList&&e.target.classList.contains('rowcheck'))up()});bb.addEventListener('click',()=>{const n=document.querySelectorAll('.rowcheck:checked').length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 data.','warning');return}Swal.fire({title:'Hapus '+n+' data?',text:'Tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)bf.submit()})});})();
</script>
<script>
const modal=document.getElementById('annModal');
function openModal(d){
  document.getElementById('modalTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Pengumuman':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Pengumuman');
  document.getElementById('f_id').value=d?.id||0;
  document.getElementById('f_title').value=d?.title||'';
  document.getElementById('f_content').value=d?.content||'';
  document.getElementById('f_old').value=d?.attachment||'';
  document.getElementById('f_status').value=d?.status||'published';
  document.getElementById('f_date').value=(d?.published_at||'').replace(' ','T').slice(0,16);
  const lk=document.getElementById('f_att_link');
  if(d?.attachment){lk.href='<?= Helper::url('') ?>/'+d.attachment.replace(/^\//,'');lk.textContent=d.attachment;lk.classList.remove('hidden')}else{lk.classList.add('hidden')}
  modal.classList.remove('hidden');document.body.style.overflow='hidden';
}
function closeModal(){modal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAdd').addEventListener('click',()=>openModal(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openModal(JSON.parse(b.dataset.row))));
modal.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
<?php if($edit): ?>openModal(<?= json_encode(['id'=>$edit['id'],'title'=>$edit['title'],'content'=>$edit['content']??'','attachment'=>$edit['attachment']??'','status'=>$edit['status'],'published_at'=>$edit['published_at']??'']) ?>);<?php endif; ?>
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>
