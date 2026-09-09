<?php declare(strict_types=1); $title='Data Siswa';
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/students')); exit; }
$act=$_POST['act']??'save';
if($act==='meta'){ foreach(['siswa_title','siswa_desc','siswa_show','siswa_cols'] as $k){ if(!array_key_exists($k,$_POST['s']??[])) continue; $v=trim((string)$_POST['s'][$k]); if($k==='siswa_show') $v=$v==='1'?'1':'0'; $db->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")->execute([$k,$v]); } Auth::log($db,'update','siswa','Ubah pengaturan'); Session::flash('ok','Pengaturan siswa disimpan.'); }
elseif($act==='class_save'){ $cn=trim($_POST['class_name']??''); $nl=max(0,(int)($_POST['n_l']??0)); $np=max(0,(int)($_POST['n_p']??0)); if($cn===''){ Session::flash('err','Nama kelas wajib.'); } else { $so=(int)($_POST['sort_order']??0); if(!empty($_POST['id'])){ $cid=(int)$_POST['id']; $db->prepare("UPDATE student_classes SET name=?,n_l=?,n_p=?,sort_order=? WHERE id=?")->execute([$cn,$nl,$np,$so,$cid]); Auth::log($db,'update','siswa',"Ubah kelas $cn: $nl putra, $np putri, total ".($nl+$np)); } else { try{ $db->prepare("INSERT INTO student_classes(name,n_l,n_p,sort_order) VALUES(?,?,?,?)")->execute([$cn,$nl,$np,$so]); }catch(Throwable){ Session::flash('err','Kelas sudah ada.'); header('Location: '.Helper::url('admin/students')); exit; } Auth::log($db,'create','siswa',"Tambah kelas $cn: $nl putra, $np putri, total ".($nl+$np)); } Session::flash('ok','Kelas disimpan.'); } }
elseif($act==='class_del'){ $cid=(int)$_POST['id']; $cn=$db->prepare("SELECT name FROM student_classes WHERE id=?"); $cn->execute([$cid]); $cname=$cn->fetchColumn()?:'kelas#'.$cid; $db->prepare("DELETE FROM student_classes WHERE id=?")->execute([$cid]); Auth::log($db,'delete','siswa',"Hapus kelas $cname"); Session::flash('ok','Kelas dihapus (siswa jadi tanpa kelas).'); }
elseif($act==='bulk_delete'){ $ids=array_filter(array_map('intval',(array)($_POST['ids']??[]))); if(!$ids){ Session::flash('err','Pilih minimal 1 data.'); } else { $ph=implode(',',array_fill(0,count($ids),'?')); $st=$db->prepare("SELECT name FROM student_classes WHERE id IN ($ph)"); $st->execute(array_values($ids)); $names=$st->fetchAll(PDO::FETCH_COLUMN); $db->prepare("DELETE FROM student_classes WHERE id IN ($ph)")->execute(array_values($ids)); Auth::log($db,'delete','siswa','Hapus kelas: '.implode(', ',array_slice($names,0,3)).(count($names)>3?' (+'.(count($names)-3).' lain)':'')); Session::flash('ok',count($ids).' kelas dihapus.'); } }
elseif($act==='delete'){ $cid=(int)$_POST['id']; $cn=$db->prepare("SELECT name FROM student_classes WHERE id=?"); $cn->execute([$cid]); $cname=$cn->fetchColumn()?:'kelas#'.$cid; $db->prepare("DELETE FROM student_classes WHERE id=?")->execute([$cid]); Auth::log($db,'delete','siswa',"Hapus kelas $cname"); Session::flash('ok','Dihapus.'); }
header('Location: '.Helper::url('admin/students')); exit; }
$sets=[]; foreach($db->query("SELECT `key`,`value` FROM settings WHERE `key` IN ('siswa_title','siswa_desc','siswa_show','siswa_cols')") as $r) $sets[$r['key']]=$r['value'];
$classes=$db->query("SELECT * FROM student_classes ORDER BY sort_order,id")->fetchAll();
$rows=$db->query("SELECT s.*,c.name cname FROM students s LEFT JOIN student_classes c ON c.id=s.class_id ORDER BY c.sort_order,c.id,s.sort_order,s.id")->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-user-graduate text-emerald-600 mr-1"></i>Data Siswa</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= array_sum(array_column($classes,'n_l'))+array_sum(array_column($classes,'n_p')) ?> siswa</span>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($classes) ?> kelas</span>
<a href="<?= Helper::url('siswa') ?>" target="_blank" rel="noopener noreferrer" class="text-sm px-3 py-1.5 border rounded-lg bg-white"><i class="fa fa-eye mr-1"></i>Lihat Public</a>
<button id="btnClass" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Kelas</button>
</div>
<form method="post" data-loading class="bg-white rounded-2xl border p-4 grid md:grid-cols-4 gap-2 text-sm mb-3"><?= Security::csrfField() ?>
<input type="hidden" name="act" value="meta">
<label class="grid gap-1">Judul public<input name="s[siswa_title]" value="<?= Helper::e($sets['siswa_title']??'Data Siswa') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Tampil di public<select name="s[siswa_show]" class="border rounded-lg p-2"><option value="1" <?= ($sets['siswa_show']??'1')==='1'?'selected':'' ?>>Tampilkan</option><option value="0" <?= ($sets['siswa_show']??'1')==='0'?'selected':'' ?>>Sembunyikan (404)</option></select></label>
<label class="grid gap-1">Deskripsi singkat<input name="s[siswa_desc]" value="<?= Helper::e($sets['siswa_desc']??'') ?>" placeholder="Ringkasan singkat" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Kolom<select name="s[siswa_cols]" class="border rounded-lg p-2"><option value="2" <?= ($sets['siswa_cols']??'4')==='2'?'selected':'' ?>>2 kolom</option><option value="3" <?= ($sets['siswa_cols']??'4')==='3'?'selected':'' ?>>3 kolom</option><option value="4" <?= ($sets['siswa_cols']??'4')==='4'?'selected':'' ?>>4 kolom</option></select></label>
<button class="md:col-span-4 bg-slate-800 hover:bg-slate-700 text-white rounded-xl py-2 font-bold text-sm"><i class="fa fa-floppy-disk mr-1"></i>Simpan Pengaturan</button>
</form>
<form method="post" id="bulkForm"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="px-4 py-3 font-bold border-b">Daftar Kelas</div>
<div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b text-sm"><span id="selCount" class="text-slate-500">0 dipilih</span><button type="button" id="btnBulk" class="ml-auto bg-red-600 hover:bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[640px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-8"><input type="checkbox" id="checkAll"></th><th class="p-3 w-10">No</th><th class="p-3">Nama Kelas</th><th class="p-3 text-center">Putra</th><th class="p-3 text-center">Putri</th><th class="p-3 text-center">Total Siswa</th><th class="p-3 text-right">Aksi</th></tr>
<?php $no=1; foreach($classes as $c): $nt=(int)$c['n_l']+(int)$c['n_p']; ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= $c['id'] ?>" class="rowcheck"></td><td class="p-3 text-slate-500"><?= $no++ ?></td>
<td class="p-3 font-extrabold"><?= Helper::e($c['name']) ?></td>
<td class="p-3 text-center font-extrabold text-sky-700"><?= (int)$c['n_l'] ?></td><td class="p-3 text-center font-extrabold text-pink-700"><?= (int)$c['n_p'] ?></td><td class="p-3 text-center font-extrabold"><?= $nt ?></td>
<td class="p-3"><span class="flex gap-1 justify-end"><button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-class='<?= htmlspecialchars(json_encode(['id'=>$c['id'],'class_name'=>$c['name'],'n_l'=>(int)$c['n_l'],'n_p'=>(int)$c['n_p'],'sort_order'=>$c['sort_order']]),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button><form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form></span></td></tr>
<?php endforeach; ?>
<?php if(!$classes): ?><tr><td colspan="7" class="p-10 text-center text-slate-500"><i class="fa fa-user-graduate text-3xl block mb-2"></i>Belum ada kelas. Klik Tambah Kelas.</td></tr><?php endif; ?>
</table></div></div>
<div class="bg-slate-50 border rounded-2xl px-4 py-2.5 mt-3 text-xs text-slate-500">Slug public: <code class="font-mono bg-white px-1.5 py-0.5 rounded border font-bold text-emerald-700">/siswa</code></div>
<div id="clsModal" class="hidden fixed inset-0 z-50 overflow-y-auto"><div class="fixed inset-0 bg-slate-900/60" data-close></div><div class="relative min-h-full flex items-start justify-center p-3 sm:p-6"><div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl my-4">
<div class="flex items-center gap-2 px-4 py-3 border-b bg-white"><h2 class="font-extrabold text-sm" id="clsTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Kelas</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" data-loading class="p-4 grid gap-2.5 text-sm bg-white"><?= Security::csrfField() ?>
<input type="hidden" name="act" value="class_save"><input type="hidden" name="id" id="c_id" value="0">
<label class="grid gap-1 font-semibold">Nama kelas<input name="class_name" id="c_name" required placeholder="cth: X IPA 1" class="border rounded-lg p-2 font-normal"></label>
<div class="grid grid-cols-2 gap-2">
<label class="grid gap-1 font-semibold">Jumlah putra<input type="number" name="n_l" id="c_nl" min="0" value="0" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Jumlah putri<input type="number" name="n_p" id="c_np" min="0" value="0" class="border rounded-lg p-2 font-normal"></label>
</div>
<label class="grid gap-1 font-semibold">Urutan<input type="number" name="sort_order" id="c_sort" value="0" class="border rounded-lg p-2 font-normal"></label>
<div class="flex justify-center"><button class="bg-slate-800 hover:bg-slate-700 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>
<script>
(function(){const ca=document.getElementById('checkAll'),sc=document.getElementById('selCount'),bb=document.getElementById('btnBulk'),bf=document.getElementById('bulkForm');if(!ca||!bb||!bf)return;const up=()=>{sc.textContent=document.querySelectorAll('.rowcheck:checked').length+' dipilih'};ca.addEventListener('change',()=>{document.querySelectorAll('.rowcheck').forEach(c=>c.checked=ca.checked);up()});document.addEventListener('change',e=>{if(e.target.classList&&e.target.classList.contains('rowcheck'))up()});bb.addEventListener('click',()=>{const n=document.querySelectorAll('.rowcheck:checked').length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 data.','warning');return}Swal.fire({title:'Hapus '+n+' data?',text:'Tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)bf.submit()})});})();
</script>
<script>
const clsModal=document.getElementById('clsModal');
function openCls(d){
  document.getElementById('clsTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Kelas':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Kelas');
  document.getElementById('c_id').value=d?.id||0;document.getElementById('c_name').value=d?.class_name||'';document.getElementById('c_nl').value=d?.n_l??0;document.getElementById('c_np').value=d?.n_p??0;document.getElementById('c_sort').value=d?.sort_order??0;
  clsModal.classList.remove('hidden');document.body.style.overflow='hidden';
}
function closeAll(){clsModal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnClass').addEventListener('click',()=>openCls(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openCls(JSON.parse(b.dataset.class))));
document.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',closeAll));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeAll()});
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>
