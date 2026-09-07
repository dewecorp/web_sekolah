<?php declare(strict_types=1); Auth::requireRole(['administrator','editor']); $title='Struktur Organisasi';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/structure')); exit; }
  $act=$_POST['act']??'save';
  if($act==='meta'){
    foreach(['struktur_title','struktur_desc','struktur_show'] as $k){
      if(!array_key_exists($k,$_POST['s']??[])) continue;
      $v=trim((string)$_POST['s'][$k]);
      if($k==='struktur_show') $v=$v==='1'?'1':'0';
      $db->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")->execute([$k,$v]);
    }
    if(!empty($_FILES['org_chart']['name']??'')){
      $e=Security::validImage($_FILES['org_chart'],$APP);
      if($e){ Session::flash('err',$e); header('Location: '.Helper::url('admin/structure')); exit; }
      $n=Security::safeName($_FILES['org_chart']['name']);
      move_uploaded_file($_FILES['org_chart']['tmp_name'],ROOT.'/assets/uploads/'.$n);
      $db->prepare("UPDATE school_profile SET org_chart=?")->execute([$n]);
    }
    if(!empty($_POST['clear_chart'])){ try{ $db->prepare("UPDATE school_profile SET org_chart=NULL")->execute(); }catch(Throwable){} }
    Auth::log($db,'update','structure','Ubah meta struktur'); Session::flash('ok','Pengaturan struktur disimpan.');
  } else {
    $tid=(int)($_POST['teacher_id']??0); $pos=trim($_POST['position']??''); $ord=(int)($_POST['sort_order']??0);
    if($act==='bulk_delete'){ $ids=array_filter(array_map('intval',(array)($_POST['ids']??[]))); if(!$ids){ Session::flash('err','Pilih minimal 1 data.'); } else { $ph=implode(',',array_fill(0,count($ids),'?')); $db->prepare("DELETE FROM structures WHERE id IN ($ph)")->execute(array_values($ids)); Auth::log($db,'delete','structure','Hapus bulk struktur'); Session::flash('ok',count($ids).' data dihapus.'); } }
    elseif($act==='delete'){ $db->prepare("DELETE FROM structures WHERE id=?")->execute([(int)$_POST['id']]); Session::flash('ok','Dihapus.'); }
    elseif($tid<=0){ Session::flash('err','Nama guru wajib dipilih.'); }
    else{
      $nm=$db->prepare("SELECT name FROM teachers WHERE id=?"); $nm->execute([$tid]); $tname=$nm->fetchColumn()?:'guru#'.$tid;
      if($pos===''){ $pp=$db->prepare("SELECT position FROM teachers WHERE id=?"); $pp->execute([$tid]); $pos=(string)($pp->fetchColumn()?:''); }
      if(!empty($_POST['id'])) $db->prepare("UPDATE structures SET teacher_id=?,position=?,sort_order=? WHERE id=?")->execute([$tid,$pos,$ord,(int)$_POST['id']]);
      else $db->prepare("INSERT INTO structures(teacher_id,position,sort_order) VALUES(?,?,?)")->execute([$tid,$pos,$ord]);
      Auth::log($db,'save','structure',"Simpan $tname - $pos"); Session::flash('ok','Disimpan.');
    }
  }
  header('Location: '.Helper::url('admin/structure')); exit;
}
$sets=[]; foreach($db->query("SELECT `key`,`value` FROM settings") as $r) $sets[$r['key']]=$r['value'];
$rows=$db->query("SELECT s.*,t.name tname,t.photo tphoto FROM structures s LEFT JOIN teachers t ON t.id=s.teacher_id ORDER BY s.sort_order,s.id")->fetchAll();
$teachers=$db->query("SELECT id,name,position FROM teachers ORDER BY name")->fetchAll();
try{ $chart=$db->query("SELECT org_chart FROM school_profile LIMIT 1")->fetchColumn(); }catch(Throwable){ $chart=null; }
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-sitemap text-emerald-600 mr-1"></i>Struktur Organisasi</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($rows) ?> jabatan</span>
<a href="<?= Helper::url('struktur-organisasi') ?>" target="_blank" rel="noopener noreferrer" class="text-sm px-3 py-1.5 border rounded-lg bg-white"><i class="fa fa-eye mr-1"></i>Lihat Public</a>
<button id="btnAdd" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Jabatan</button>
</div>
<form method="post" data-loading enctype="multipart/form-data" class="bg-white rounded-2xl border p-4 grid md:grid-cols-4 gap-2 text-sm mb-3"><?= Security::csrfField() ?>
<input type="hidden" name="act" value="meta">
<label class="grid gap-1 md:col-span-2">Judul public<input name="s[struktur_title]" value="<?= Helper::e($sets['struktur_title']??'Struktur Organisasi') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Tampil di public<select name="s[struktur_show]" class="border rounded-lg p-2"><option value="1" <?= ($sets['struktur_show']??'1')==='1'?'selected':'' ?>>Tampilkan</option><option value="0" <?= ($sets['struktur_show']??'1')==='0'?'selected':'' ?>>Sembunyikan (404)</option></select></label>
<label class="grid gap-1">Deskripsi singkat<textarea name="s[struktur_desc]" rows="1" class="border rounded-lg p-2"><?= Helper::e($sets['struktur_desc']??'') ?></textarea></label>
<div class="grid gap-1 border rounded-xl p-2.5 bg-slate-50 md:col-span-2">
<span class="text-xs font-bold"><i class="fa fa-image mr-1 text-emerald-600"></i>Bagan Struktur (gambar)</span>
<?php if(!empty($chart)): ?><img src="<?= Helper::upload($chart) ?>" alt="Bagan" class="h-24 w-full object-contain rounded-lg border bg-white"><?php endif; ?>
<input type="file" name="org_chart" accept="image/*" class="border rounded-lg p-1.5 w-full bg-white text-xs">
<?php if(!empty($chart)): ?><label class="text-xs flex gap-1.5 items-center"><input type="checkbox" name="clear_chart" value="1"> Hapus bagan</label><?php endif; ?>
</div>
<button class="md:col-span-4 bg-slate-800 hover:bg-slate-700 text-white rounded-xl py-2 font-bold text-sm"><i class="fa fa-floppy-disk mr-1"></i>Simpan Pengaturan</button>
</form>
<form method="post" id="bulkForm"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b text-sm"><span id="selCount" class="text-slate-500">0 dipilih</span><button type="button" id="btnBulk" class="ml-auto bg-red-600 hover:bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[640px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-8"><input type="checkbox" id="checkAll"></th><th class="p-3 w-10">No</th><th class="p-3">Nama Guru</th><th class="p-3">Jabatan</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$rows): ?><tr><td colspan="5" class="p-10 text-center text-slate-500"><i class="fa fa-sitemap text-3xl block mb-2"></i>Belum ada struktur. Klik Tambah Jabatan.</td></tr><?php endif; ?>
<?php $no=1; foreach($rows as $r): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= $r['id'] ?>" class="rowcheck"></td><td class="p-3 text-slate-500"><?= $no++ ?></td>
<td class="p-3 font-semibold"><span class="flex items-center gap-2"><?php if(!empty($r['tphoto'])): ?><img src="<?= Helper::upload($r['tphoto']) ?>" alt="" class="w-8 h-8 rounded-full object-cover border"><?php else: ?><span class="w-8 h-8 rounded-full bg-slate-100 border grid place-items-center text-slate-400"><i class="fa fa-user text-xs"></i></span><?php endif; ?><?= Helper::e($r['tname']??'(guru dihapus)') ?></span></td>
<td class="p-3"><?= Helper::e($r['position']) ?></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$r['id'],'teacher_id'=>$r['teacher_id'],'position'=>$r['position'],'sort_order'=>$r['sort_order']]),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>
<div id="strModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl my-4">
<div class="flex items-center gap-2 px-5 py-3.5 border-b bg-white"><h2 class="font-extrabold" id="modalTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Jabatan</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" data-loading class="p-5 grid gap-3 text-sm bg-white"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="f_id" value="0">
<label class="grid gap-1 font-semibold">Nama Guru (dari Guru & Staff)<select name="teacher_id" id="f_teacher" required class="border rounded-lg p-2 font-normal"><option value="">-- Pilih guru --</option><?php foreach($teachers as $t): ?><option value="<?= $t['id'] ?>" data-pos="<?= Helper::e($t['position']??'') ?>"><?= Helper::e($t['name']) ?> — <?= Helper::e($t['position']??'') ?></option><?php endforeach; ?></select><?php if(!$teachers): ?><span class="text-xs font-normal text-amber-600">Belum ada guru. Tambah dulu di Guru & Staff.</span><?php endif; ?></label>
<label class="grid gap-1 font-semibold">Jabatan<input name="position" id="f_pos" placeholder="Otomatis dari data guru bila kosong" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Urutan<input type="number" name="sort_order" id="f_sort" value="0" class="border rounded-lg p-2 font-normal"></label>
<div class="flex justify-center md:col-span-2"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>
<script>
(function(){const ca=document.getElementById('checkAll'),sc=document.getElementById('selCount'),bb=document.getElementById('btnBulk'),bf=document.getElementById('bulkForm');if(!ca||!bb||!bf)return;const up=()=>{sc.textContent=document.querySelectorAll('.rowcheck:checked').length+' dipilih'};ca.addEventListener('change',()=>{document.querySelectorAll('.rowcheck').forEach(c=>c.checked=ca.checked);up()});document.addEventListener('change',e=>{if(e.target.classList&&e.target.classList.contains('rowcheck'))up()});bb.addEventListener('click',()=>{const n=document.querySelectorAll('.rowcheck:checked').length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 data.','warning');return}Swal.fire({title:'Hapus '+n+' data?',text:'Tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)bf.submit()})});})();
</script>
<script>
const modal=document.getElementById('strModal');
function openModal(d){
  document.getElementById('modalTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Jabatan':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Jabatan');
  document.getElementById('f_id').value=d?.id||0;
  document.getElementById('f_teacher').value=d?.teacher_id||'';
  document.getElementById('f_pos').value=d?.position||'';
  document.getElementById('f_sort').value=d?.sort_order??0;
  modal.classList.remove('hidden');document.body.style.overflow='hidden';
}
function closeModal(){modal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAdd').addEventListener('click',()=>openModal(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openModal(JSON.parse(b.dataset.row))));
modal.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
document.getElementById('f_teacher').addEventListener('change',e=>{const o=e.target.selectedOptions[0];const p=document.getElementById('f_pos');if(o&&o.dataset.pos&&!p.value)p.value=o.dataset.pos});
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>



