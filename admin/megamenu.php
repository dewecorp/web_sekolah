<?php declare(strict_types=1); $title='Mega Menu';
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/megamenu')); exit; }
$act=$_POST['act']??'';
if($act==='delete'){ $db->prepare("DELETE FROM mega_menus WHERE id=?")->execute([(int)$_POST['id']]); Auth::log($db,'delete','megamenu','Hapus mega menu'); Session::flash('ok','Dihapus.'); }
else{ $tt=trim($_POST['title']??''); if($tt===''){ Session::flash('err','Judul wajib.'); } else {
$cols=[]; foreach(($_POST['col_title']??[]) as $i=>$ct){ if(trim($ct)==='') continue; $links=[]; foreach(explode("\n",$_POST['col_links'][$i]??'') as $ln){ $ln=trim($ln); if($ln===''||!str_contains($ln,'|')) continue; [$l,$u]=explode('|',$ln,2); $links[]= ['label'=>trim($l),'url'=>trim($u)]; } $cols[]=['title'=>$ct,'links'=>$links]; }
if(!empty($_POST['id'])) $db->prepare("UPDATE mega_menus SET title=?,menu_item_id=?,columns_json=?,is_active=? WHERE id=?")->execute([$tt,$_POST['menu_item_id']?:null,json_encode($cols),1,(int)$_POST['id']]);
else $db->prepare("INSERT INTO mega_menus(title,menu_item_id,columns_json,is_active) VALUES(?,?,?,1)")->execute([$tt,$_POST['menu_item_id']?:null,json_encode($cols)]);
Auth::log($db,'save','megamenu',"Simpan $tt"); Session::flash('ok','Mega menu disimpan.'); } }
header('Location: '.Helper::url('admin/megamenu')); exit; }
$rows=$db->query("SELECT m.*, mi.label FROM mega_menus m LEFT JOIN menu_items mi ON mi.id=m.menu_item_id ORDER BY sort_order")->fetchAll();
$items=$db->query("SELECT id,label FROM menu_items WHERE parent_id IS NULL ORDER BY label")->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-layer-group text-emerald-600 mr-1"></i>Mega Menu</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($rows) ?> mega</span>
<button id="btnAdd" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Mega Menu</button>
</div>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[520px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3">Judul</th><th class="p-3">Kait Menu</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$rows): ?><tr><td colspan="3" class="p-10 text-center text-slate-500">Belum ada mega menu.</td></tr><?php endif; ?>
<?php foreach($rows as $r): $cc=json_decode($r['columns_json']??'[]',true)?:[]; ?>
<tr class="border-t hover:bg-slate-50"><td class="p-3 font-semibold"><?= Helper::e($r['title']) ?><span class="block text-[11px] font-normal text-slate-400"><?= count($cc) ?> kolom</span></td>
<td class="p-3 text-xs text-slate-500"><?= Helper::e($r['label']??'-') ?></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$r['id'],'title'=>$r['title'],'menu_item_id'=>$r['menu_item_id'],'cols'=>$cc]),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<?= Helper::iconBtns([Helper::delBtn((int)$r['id'])]) ?></span></td></tr><?php endforeach; ?></table></div></div>
<p class="text-xs text-slate-500 mt-2">Links per baris format: Label|/url</p>

<div id="megaModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl my-4">
<div class="flex items-center gap-2 px-5 py-3.5 border-b"><h2 class="font-extrabold" id="modalTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Mega Menu</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" data-loading class="p-5 grid gap-2.5 text-sm"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="f_id" value="0">
<label class="grid gap-1 font-semibold">Judul<input name="title" id="f_title" required placeholder="AKADEMIK" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Kaitkan ke Menu<select name="menu_item_id" id="f_menu" class="border rounded-lg p-2 font-normal"><option value="">-- Kaitkan ke menu --</option><?php foreach($items as $i): ?><option value="<?= $i['id'] ?>"><?= Helper::e($i['label']) ?></option><?php endforeach; ?></select></label>
<?php for($k=0;$k<3;$k++): ?><div class="border rounded-lg p-2"><input name="col_title[]" id="f_ct<?= $k ?>" placeholder="Judul kolom <?= $k+1 ?>" class="border rounded p-1.5 w-full mb-1"><textarea name="col_links[]" id="f_cl<?= $k ?>" rows="3" placeholder="Kurikulum|/kurikulum" class="border rounded p-1.5 w-full font-mono text-xs"></textarea></div><?php endfor; ?>
<div class="flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>
<script>
const modal=document.getElementById('megaModal');
function openModal(d){
  document.getElementById('modalTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Mega Menu':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Mega Menu');
  document.getElementById('f_id').value=d?.id||0;
  document.getElementById('f_title').value=d?.title||'';
  document.getElementById('f_menu').value=d?.menu_item_id||'';
  for(let k=0;k<3;k++){
    const c=(d?.cols||[])[k]||{};
    document.getElementById('f_ct'+k).value=c.title||'';
    document.getElementById('f_cl'+k).value=(c.links||[]).map(l=>l.label+'|'+l.url).join("\n");
  }
  modal.classList.remove('hidden');document.body.style.overflow='hidden';
}
function closeModal(){modal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAdd').addEventListener('click',()=>openModal(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openModal(JSON.parse(b.dataset.row))));
modal.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>

