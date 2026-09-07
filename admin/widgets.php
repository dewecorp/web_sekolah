<?php declare(strict_types=1); $title='Widget & Footer';
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/widgets')); exit; }
$act=$_POST['act']??'save';
if($act==='delete'){ $db->prepare("DELETE FROM widgets WHERE id=?")->execute([(int)$_POST['id']]); Session::flash('ok','Dihapus.'); }
else{ $tt=$_POST['title']??''; if(!empty($_POST['id'])) $db->prepare("UPDATE widgets SET area=?,type=?,title=?,content=?,sort_order=?,is_active=? WHERE id=?")->execute([$_POST['area']??'sidebar',$_POST['type']??'html',$tt,$_POST['content']??'',(int)($_POST['sort_order']??0),(int)($_POST['is_active']??1),(int)$_POST['id']]); else $db->prepare("INSERT INTO widgets(area,type,title,content,sort_order,is_active) VALUES(?,?,?,?,?,?)")->execute([$_POST['area']??'sidebar',$_POST['type']??'html',$tt,$_POST['content']??'',(int)($_POST['sort_order']??0),(int)($_POST['is_active']??1)]); Session::flash('ok','Disimpan.'); }
header('Location: '.Helper::url('admin/widgets')); exit; }
$rows=$db->query("SELECT * FROM widgets ORDER BY area,sort_order")->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-puzzle-piece text-emerald-600 mr-1"></i>Widget & Footer</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($rows) ?> widget</span>
<button id="btnAdd" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Widget</button>
</div>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[640px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3">Judul / Tipe</th><th class="p-3">Area</th><th class="p-3">Urut</th><th class="p-3">Status</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$rows): ?><tr><td colspan="5" class="p-10 text-center text-slate-500"><i class="fa fa-puzzle-piece text-3xl block mb-2"></i>Belum ada widget. Klik Tambah Widget.</td></tr><?php endif; ?>
<?php foreach($rows as $r): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3 font-semibold"><?= Helper::e($r['title']?:$r['type']) ?><span class="block text-[11px] font-normal text-slate-400 font-mono"><?= Helper::e($r['type']) ?></span></td>
<td class="p-3 text-xs text-slate-500"><?= Helper::e($r['area']) ?></td>
<td class="p-3 text-xs"><?= (int)$r['sort_order'] ?></td>
<td class="p-3"><span class="text-xs font-bold px-2 py-0.5 rounded-full <?= $r['is_active']?'bg-emerald-100 text-emerald-700':'bg-slate-100 text-slate-500' ?>"><?= $r['is_active']?'Aktif':'Nonaktif' ?></span></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$r['id'],'area'=>$r['area'],'type'=>$r['type'],'title'=>$r['title']??'','content'=>$r['content']??'','sort_order'=>(int)$r['sort_order'],'is_active'=>(int)$r['is_active']]),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>

<div id="widgetModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl my-4">
<div class="flex items-center gap-2 px-5 py-3.5 border-b bg-white"><h2 class="font-extrabold" id="modalTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Widget</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" data-loading class="p-5 grid gap-3 text-sm bg-white"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="f_id" value="0">
<label class="grid gap-1 font-semibold">Area<select name="area" id="f_area" class="border rounded-lg p-2 font-normal"><option value="sidebar">sidebar</option><option value="footer">footer</option><option value="homepage">homepage</option></select></label>
<label class="grid gap-1 font-semibold">Tipe<select name="type" id="f_type" class="border rounded-lg p-2 font-normal"><option value="html">Custom HTML</option><option value="posts">Latest Posts</option><option value="agenda">Agenda</option><option value="announcements">Announcements</option><option value="contact">Contact</option><option value="social">Social Media</option></select></label>
<label class="grid gap-1 font-semibold">Judul<input name="title" id="f_title" placeholder="Judul" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Konten<textarea name="content" id="f_content" rows="4" placeholder="Konten/HTML" class="border rounded-lg p-2 font-normal"></textarea></label>
<label class="grid gap-1 font-semibold">Urutan<input type="number" name="sort_order" id="f_sort" value="0" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Status<select name="is_active" id="f_active" class="border rounded-lg p-2 font-normal"><option value="1">Aktif</option><option value="0">Nonaktif</option></select></label>
<div class="flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<script>
const modal=document.getElementById('widgetModal');
function openModal(d){
  document.getElementById('modalTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Widget':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Widget');
  document.getElementById('f_id').value=d?.id||0;
  document.getElementById('f_area').value=d?.area||'sidebar';
  document.getElementById('f_type').value=d?.type||'html';
  document.getElementById('f_title').value=d?.title||'';
  document.getElementById('f_content').value=d?.content||'';
  document.getElementById('f_sort').value=d?.sort_order??0;
  document.getElementById('f_active').value=String(d?.is_active??1);
  modal.classList.remove('hidden');document.body.style.overflow='hidden';
}
function closeModal(){modal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAdd').addEventListener('click',()=>openModal(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openModal(JSON.parse(b.dataset.row))));
modal.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>



