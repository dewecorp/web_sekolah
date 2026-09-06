<?php declare(strict_types=1); Auth::requireRole(['administrator','editor']); $title='Menu Manager';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!Security::verifyCsrf($_POST['csrf']??null)){
    if(($_POST['ajax']??'')==='1'){ header('Content-Type: application/json'); echo json_encode(['ok'=>false,'msg'=>'CSRF tidak valid']); exit; }
    Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/menus')); exit;
  }
  $act=$_POST['act']??'';
  $ajax=($_POST['ajax']??'')==='1';
  $done=null;
  if($act==='menu'){ $nm=trim($_POST['name']??'Menu'); $loc=$_POST['location']??'primary'; $db->prepare("INSERT INTO menus(name,location) VALUES(?,?)")->execute([$nm,$loc]); Auth::log($db,'create','menus',"Menu $nm"); $done='Menu dibuat.'; }
  elseif($act==='menu_rename'){ $db->prepare("UPDATE menus SET name=?,location=? WHERE id=?")->execute([trim($_POST['name']??'Menu'),$_POST['location']??'primary',(int)$_POST['menu_id']]); Auth::log($db,'update','menus','Ubah menu'); $done='Menu diubah.'; }
  elseif($act==='menu_delete'){ $db->prepare("DELETE FROM menus WHERE id=?")->execute([(int)$_POST['menu_id']]); Auth::log($db,'delete','menus','Hapus menu'); Session::flash('ok','Menu dihapus.'); header('Location: '.Helper::url('admin/menus')); exit; }
  elseif($act==='item'){
    $mid=(int)$_POST['menu_id']; $lb=trim($_POST['label']??''); $url=trim($_POST['url']??'#'); $tgt=!empty($_POST['new_tab'])?'_blank':'_self';
    $kind=in_array($_POST['kind']??'link',['link','mega'],true)?$_POST['kind']:'link';
    $megaId=$kind==='mega'?(int)($_POST['mega_id']??0):null;
    if($kind==='mega'&&!$megaId){ Session::flash('err','Pilih konten mega menu.'); header('Location: '.Helper::url('admin/menus?menu='.$mid)); exit; }
    if($lb===''||$mid<=0){
      if($ajax){ header('Content-Type: application/json'); echo json_encode(['ok'=>false,'msg'=>'Label wajib diisi.']); exit; }
      Session::flash('err','Menu + label wajib.'); header('Location: '.Helper::url('admin/menus')); exit;
    }
    $mx=(int)$db->query("SELECT COALESCE(MAX(sort_order),0)+1 FROM menu_items WHERE menu_id=$mid")->fetchColumn();
    $db->prepare("INSERT INTO menu_items(menu_id,parent_id,label,url,target,sort_order,is_active,kind,mega_id) VALUES(?,?,?, ?,?,?,1,?,?)")->execute([$mid,($_POST['parent_id']??null)?:null,$lb,$url===''?'#':$url,$tgt,$mx,$kind,$megaId?:null]);
    Auth::log($db,'create','menus',"Item $lb ($kind)"); $done='Item ditambah.';
  }
  elseif($act==='batch_pages'){
    $mid=(int)$_POST['menu_id'];
    $slugs=array_values(array_unique(array_filter(array_map('trim',(array)($_POST['slugs']??[])))));
    if($mid<=0||!$slugs){ Session::flash('err','Pilih minimal 1 laman.'); header('Location: '.Helper::url('admin/menus?menu='.$mid)); exit; }
    $in=implode(',',array_fill(0,count($slugs),'?'));
    $st=$db->prepare("SELECT title,slug FROM pages WHERE slug IN ($in) AND deleted_at IS NULL");
    $st->execute($slugs); $found=$st->fetchAll();
    if(!$found){ Session::flash('err','Laman tidak ditemukan.'); header('Location: '.Helper::url('admin/menus?menu='.$mid)); exit; }
    $added=0;$skipped=0;
    foreach($found as $p){
      $url='/'.$p['slug'];
      $ex=$db->prepare("SELECT id FROM menu_items WHERE menu_id=? AND url=? LIMIT 1");
      $ex->execute([$mid,$url]);
      if($ex->fetch()){ $skipped++; continue; }
      $mx=(int)$db->query("SELECT COALESCE(MAX(sort_order),0)+1 FROM menu_items WHERE menu_id=$mid")->fetchColumn();
      $db->prepare("INSERT INTO menu_items(menu_id,parent_id,label,url,target,sort_order,is_active) VALUES(?,?,?, ?,?,?,1)")->execute([$mid,null,$p['title'],$url,'_self',$mx]);
      $added++;
    }
    Auth::log($db,'create','menus',"Tambah $added laman ke menu");
    if($added)Session::flash('ok',"$added laman masuk struktur menu.".($skipped?" $skipped sudah ada, dilewati.":''));
    else Session::flash('err','Tidak ada laman ditambah (mungkin semuanya sudah ada di menu).');
  }
  elseif($act==='item_update'){
    $s=$db->prepare("SELECT * FROM menu_items WHERE id=?"); $s->execute([(int)$_POST['id']]); $old=$s->fetch();
    if(!$old){ Session::flash('err','Item tidak ada.'); header('Location: '.Helper::url('admin/menus')); exit; }
    $lb=trim($_POST['label']??$old['label']); $url=trim($_POST['url']??$old['url']); $tgt=!empty($_POST['new_tab'])?'_blank':'_self';
    $kind=in_array($_POST['kind']??($old['kind']??'link'),['link','mega'],true)?$_POST['kind']:$old['kind'];
    $megaId=$kind==='mega'?(int)($_POST['mega_id']??$old['mega_id']??0):null;
    if($kind==='mega'&&!$megaId){ Session::flash('err','Pilih konten mega menu.'); header('Location: '.Helper::url('admin/menus?menu='.(int)$_POST['menu_id'])); exit; }
    $db->prepare("UPDATE menu_items SET label=?,url=?,target=?,is_active=?,kind=?,mega_id=? WHERE id=?")->execute([$lb,$url===''?'#':$url,$tgt,!empty($_POST['is_active'])?1:0,$kind,$megaId?:null,(int)$_POST['id']]);
    Auth::log($db,'update','menus',"Ubah $lb ($kind)"); $done='Item diubah.';
  }
  elseif($act==='del_item'){ $db->prepare("DELETE FROM menu_items WHERE id=?")->execute([(int)$_POST['id']]); Auth::log($db,'delete','menus','Hapus item'); $done='Item dihapus.'; }
  elseif($act==='toggle'){ $db->prepare("UPDATE menu_items SET is_active=1-is_active WHERE id=?")->execute([(int)$_POST['id']]); $done='Status diubah.'; }
  elseif($act==='reorder'){
    $tree=json_decode($_POST['tree']??'[]',true)?:[];
    $fix=function($nodes,$parent,$mid) use (&$fix,$db){
      $o=1; foreach($nodes as $n){
        $id=(int)($n['id']??0); if($id<=0)continue;
        $db->prepare("UPDATE menu_items SET parent_id=?,sort_order=?,menu_id=? WHERE id=?")->execute([$parent,$o++,$mid,$id]);
        if(!empty($n['children']))$fix($n['children'],$id,$mid);
      }
    };
    $fix($tree,null,(int)$_POST['menu_id']);
    Auth::log($db,'update','menus','Reorder drag-drop');
    if($ajax){ header('Content-Type: application/json'); echo json_encode(['ok'=>true]); exit; }
    $done='Urutan disimpan.';
  }
  if($ajax){ header('Content-Type: application/json'); echo json_encode(['ok'=>true]); exit; }
  if($done)Session::flash('ok',$done);
  $back='admin/menus'; if(!empty($_POST['menu_id']))$back.='?menu='.(int)$_POST['menu_id'];
  header('Location: '.Helper::url($back)); exit;
}
$menus=$db->query("SELECT m.*,(SELECT COUNT(*) FROM menu_items WHERE menu_id=m.id) cnt FROM menus m ORDER BY id")->fetchAll();
$mid=(int)($_GET['menu']??($menus[0]['id']??0));
$cur=null; foreach($menus as $m)if($m['id']==$mid)$cur=$m;
$items=[]; if($mid){ $s=$db->prepare("SELECT * FROM menu_items WHERE menu_id=? ORDER BY sort_order"); $s->execute([$mid]); $items=$s->fetchAll(); }
$pages=$db->query("SELECT title,slug,status FROM pages WHERE deleted_at IS NULL ORDER BY title LIMIT 100")->fetchAll();
$megas=$db->query("SELECT id,title FROM mega_menus WHERE is_active=1 ORDER BY title LIMIT 50")->fetchAll();
$build=function($items,$parent=null) use (&$build){
  $o=[]; foreach($items as $i){ if(($i['parent_id']??null)==$parent){ $i['children']=$build($items,$i['id']); $o[]=$i; } } return $o;
};
$tree=$build($items);
// Render nested: <ul class=mkids><li class=mnode data-id>card + editor + <ul class=mkids>anak</ul></li></ul>
$megaOpts=function($sel) use ($megas){
  $h='<option value="">-- Pilih konten mega --</option>';
  foreach($megas as $g)$h.='<option value="'.$g['id'].'"'.((int)($sel??0)==(int)$g['id']?' selected':'').'>'.Helper::e($g['title']).'</option>';
  return $h;
};
$node=function($n,$depth=0) use (&$node,$megaOpts,$megas){
  $isMega=($n['kind']??'link')==='mega';
  echo '<li class="mnode" data-id="'.$n['id'].'" data-depth="'.$depth.'">';
  echo '<div class="mcard flex items-center gap-2 px-2.5 py-2 bg-white border rounded-xl cursor-move hover:border-emerald-400">';
  echo '<span class="m-drag-handle text-slate-300 cursor-grab" title="Seret untuk memindahkan"><i class="fa fa-grip-vertical text-xs pointer-events-none"></i></span>';
  echo '<span class="font-semibold text-sm flex-1 truncate">'.Helper::e($n['label']).'</span>';
  echo $isMega?'<span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-violet-100 text-violet-700"><i class="fa fa-layer-group mr-0.5"></i>MEGA</span>':'<span class="text-[10px] font-mono text-slate-400 truncate max-w-[140px] hidden sm:block">'.Helper::e($n['url']).'</span>';
  echo '<span class="text-[10px] px-1.5 py-0.5 rounded '.($n['target']==='_blank'?'bg-sky-100 text-sky-700':'bg-slate-100 text-slate-500').'">'.($n['target']==='_blank'?'tab baru':'tab sama').'</span>';
  echo $n['is_active']?'':'<span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-200 text-slate-500">off</span>';
  echo '<button type="button" class="m-up text-[11px] w-8 h-8 grid place-items-center border rounded-lg bg-white hover:text-emerald-600 hover:border-emerald-400" title="Geser ke atas" aria-label="Geser menu ke atas"><i class="fa fa-chevron-up"></i></button>';
  echo '<button type="button" class="m-down text-[11px] w-8 h-8 grid place-items-center border rounded-lg bg-white hover:text-emerald-600 hover:border-emerald-400" title="Geser ke bawah" aria-label="Geser menu ke bawah"><i class="fa fa-chevron-down"></i></button>';
  echo '<button type="button" class="m-edit text-[11px] font-bold w-8 h-8 grid place-items-center border rounded-lg bg-white hover:text-emerald-600 hover:border-emerald-400" title="Edit menu" aria-label="Edit menu"><i class="fa fa-pen"></i></button>';
  echo '<button type="button" class="m-del text-[11px] font-bold w-8 h-8 grid place-items-center border rounded-lg bg-white text-red-600 hover:border-red-400" title="Hapus menu" aria-label="Hapus menu"><i class="fa fa-trash"></i></button>';
  echo '</div>';
  echo '<div class="m-body hidden border border-t-0 p-2.5 grid gap-1.5 text-xs bg-slate-50 rounded-b-xl mb-1.5">';
  echo '<form method="post" class="grid gap-1.5">'.Security::csrfField().'<input type="hidden" name="act" value="item_update"><input type="hidden" name="id" value="'.$n['id'].'"><input type="hidden" name="menu_id" value="'.$n['menu_id'].'">';
  echo '<label class="grid gap-0.5">Label navigasi<input name="label" value="'.Helper::e($n['label']).'" class="border rounded-lg p-1.5"></label>';
  echo '<label class="grid gap-0.5">Jenis Menu<select name="kind" class="border rounded-lg p-1.5 m-kind"><option value="link"'.($isMega?'':' selected').'>Tautan (link biasa)</option><option value="mega"'.($isMega?' selected':'').'>Mega Menu</option></select></label>';
  echo '<label class="grid gap-0.5 m-urlwrap"'.($isMega?' style="display:none"':'').'>URL <span class="text-slate-400">relatif (/profil) atau penuh (https://...)</span><input name="url" value="'.Helper::e($n['url']).'" class="border rounded-lg p-1.5 font-mono"></label>';
  echo '<label class="grid gap-0.5 m-megawrap"'.($isMega?'':' style="display:none"').'>Konten Mega Menu <a href="'.Helper::url('admin/megamenu').'" target="_blank" rel="noopener noreferrer" class="text-emerald-600">kelola →</a><select name="mega_id" class="border rounded-lg p-1.5">'.$megaOpts($n['mega_id']??null).'</select></label>';
  echo '<label class="flex gap-1.5 items-center"><input type="checkbox" name="new_tab" value="1" '.($n['target']==='_blank'?'checked':'').'> Buka tab baru</label>';
  echo '<label class="flex gap-1.5 items-center"><input type="checkbox" name="is_active" value="1" '.($n['is_active']?'checked':'').'> Aktif</label>';
  echo '<span class="flex gap-1.5"><button class="flex-1 bg-emerald-600 text-white rounded-lg py-1.5 font-bold"><i class="fa fa-floppy-disk mr-1"></i>Simpan Perubahan</button>';
  echo '<button type="button" class="m-cancel px-2.5 border rounded-lg bg-white text-slate-500" title="Batal"><i class="fa fa-xmark"></i></button>';
  echo '<button type="button" class="m-out px-2.5 border rounded-lg bg-white text-amber-600" data-id="'.$n['id'].'" title="Keluarkan jadi menu induk"><i class="fa fa-arrow-right-from-bracket"></i></button>';
  echo '<button type="button" class="m-tg px-2.5 border rounded-lg bg-white" data-id="'.$n['id'].'" title="Aktif/Nonaktif"><i class="fa fa-power-off"></i></button></span></form></div>';
  echo '<ul class="mkids grid gap-1.5 mt-1.5 ml-5 sm:ml-8 pl-3 border-l-2 border-dashed border-slate-300 min-h-[6px]">';
  foreach($n['children']??[] as $c)$node($c,$depth+1);
  echo '</ul></li>';
};
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-1">
<h1 class="text-xl font-extrabold"><i class="fa fa-list-ul text-emerald-600 mr-1"></i>Menu Manager</h1>
<a href="<?= Helper::url() ?>" target="_blank" rel="noopener noreferrer" class="ml-auto text-sm px-3 py-1.5 border rounded-lg bg-white"><i class="fa fa-eye mr-1"></i>Lihat Situs</a>
</div>
<p class="text-xs text-slate-500 mb-3">Seret kartu: atas/bawah = urutan • <b>tengah kartu = jadikan anak menu</b> • tombol Edit/Hapus jelas di tiap item.</p>
<div class="bg-white rounded-2xl border p-3 mb-3 flex flex-wrap items-center gap-2 text-sm">
<span class="text-xs font-bold uppercase text-slate-400">Menu:</span>
<?php foreach($menus as $m): ?><a href="?menu=<?= $m['id'] ?>" class="px-3 py-1.5 rounded-lg border <?= $mid==$m['id']?'bg-emerald-600 text-white border-emerald-600 font-bold':'hover:border-emerald-400' ?>"><?= Helper::e($m['name']) ?> <span class="text-[10px] opacity-70">(<?= $m['cnt'] ?>)</span></a><?php endforeach; ?>
<form method="post" class="flex gap-1 ml-auto"><?= Security::csrfField() ?><input type="hidden" name="act" value="menu"><input name="name" required placeholder="Menu baru..." class="border rounded-lg px-2.5 py-1.5 text-sm w-36"><input type="hidden" name="location" value="primary"><button class="bg-slate-800 text-white px-3 rounded-lg text-sm" title="Buat menu"><i class="fa fa-plus"></i></button></form>
</div>
<?php if(!$mid): ?><p class="text-sm text-slate-500">Buat menu dulu.</p><?php else: ?>
<div class="grid lg:grid-cols-5 gap-3 items-start">
<div class="lg:col-span-2 grid gap-3">
<div class="bg-white rounded-2xl border p-3 text-sm">
<p class="text-xs font-bold uppercase text-slate-400 mb-2"><i class="fa fa-plus mr-1"></i>Tambah dari Laman</p>
<?php if(!$pages): ?><p class="text-xs text-slate-400">Belum ada laman. Buat dulu di menu Halaman.</p><?php else: ?>
<form method="post" data-loading><?= Security::csrfField() ?><input type="hidden" name="act" value="batch_pages"><input type="hidden" name="menu_id" value="<?= $mid ?>">
<div class="max-h-44 overflow-y-auto border rounded-lg divide-y mb-2"><?php foreach($pages as $p): ?><label class="flex gap-2 items-center px-2.5 py-1.5 text-xs hover:bg-emerald-50 cursor-pointer"><input type="checkbox" name="slugs[]" value="<?= Helper::e($p['slug']) ?>"> <span class="flex-1 truncate"><?= Helper::e($p['title']) ?></span> <span class="text-slate-400 font-mono">/<?= Helper::e($p['slug']) ?></span> <?= ($p['status']??'')==='published'?'':'<span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-700">draft</span>' ?></label><?php endforeach; ?></div>
<button class="w-full bg-emerald-600 text-white rounded-lg py-1.5 font-bold text-xs">+ Tambah ke Menu</button></form><?php endif; ?>
</div>
<div class="bg-white rounded-2xl border p-3 text-sm">
<p class="text-xs font-bold uppercase text-slate-400 mb-2"><i class="fa fa-link mr-1"></i>Tautan Khusus</p>
<form method="post" data-loading class="grid gap-1.5"><?= Security::csrfField() ?><input type="hidden" name="act" value="item"><input type="hidden" name="menu_id" value="<?= $mid ?>">
<input name="label" required placeholder="Label (mis. PPDB / Akademik)" class="border rounded-lg p-2">
<label class="grid gap-0.5 text-xs font-bold">Jenis Menu<select name="kind" id="addKind" class="border rounded-lg p-2 font-normal"><option value="link">Tautan (link biasa)</option><option value="mega">Mega Menu</option></select></label>
<span id="addUrlWrap" class="grid gap-1.5"><input name="url" placeholder="/pendaftaran atau https://sekolah-lain.sch.id" class="border rounded-lg p-2 font-mono text-xs">
<label class="flex gap-1.5 items-center text-xs"><input type="checkbox" name="new_tab" value="1"> Buka tab baru (web luar)</label></span>
<span id="addMegaWrap" class="grid gap-1.5" style="display:none"><select name="mega_id" class="border rounded-lg p-2 text-xs"><?php foreach($megas as $g): ?><option value="<?= $g['id'] ?>"><?= Helper::e($g['title']) ?></option><?php endforeach; ?></select><a href="<?= Helper::url('admin/megamenu') ?>" target="_blank" rel="noopener noreferrer" class="text-xs text-emerald-600">Kelola konten mega →</a><?php if(!$megas): ?><span class="text-xs text-amber-600">Belum ada mega. Buat dulu di Mega Menu.</span><?php endif; ?></span>
<button class="bg-emerald-600 text-white rounded-lg py-1.5 font-bold">+ Tambah ke Menu</button></form>
<div class="grid grid-cols-3 gap-1 mt-2 text-[11px]">
<?php foreach([['Beranda','/'],['Berita','/berita'],['Galeri','/galeri'],['Guru','/guru'],['Agenda','/agenda'],['Kontak','/kontak']] as $q): ?>
<form method="post"><?= Security::csrfField() ?><input type="hidden" name="act" value="item"><input type="hidden" name="menu_id" value="<?= $mid ?>"><input type="hidden" name="label" value="<?= $q[0] ?>"><input type="hidden" name="url" value="<?= $q[1] ?>"><button class="border rounded-lg px-1 py-1 w-full hover:border-emerald-400">+ <?= $q[0] ?></button></form>
<?php endforeach; ?></div></div>
</div>
<div class="lg:col-span-3 grid gap-3">
<div class="bg-white rounded-2xl border p-3">
<p class="text-xs font-bold uppercase text-slate-400 mb-2"><i class="fa fa-gear mr-1"></i>Kelola Menu Ini</p>
<form method="post" data-loading class="flex flex-wrap gap-1.5"><?= Security::csrfField() ?><input type="hidden" name="act" value="menu_rename"><input type="hidden" name="menu_id" value="<?= $mid ?>">
<input name="name" value="<?= Helper::e($cur['name']??'') ?>" class="border rounded-lg p-1.5 text-sm flex-1 min-w-[140px]">
<select name="location" class="border rounded-lg p-1.5 text-sm"><option value="primary" <?= ($cur['location']??'')==='primary'?'selected':'' ?>>primary</option><option value="footer" <?= ($cur['location']??'')==='footer'?'selected':'' ?>>footer</option></select>
<button class="bg-emerald-600 text-white px-3 rounded-lg text-sm font-bold"><i class="fa fa-pen mr-1"></i>Edit Menu</button></form>
<form method="post" data-confirm class="mt-1.5"><?= Security::csrfField() ?><input type="hidden" name="act" value="menu_delete"><input type="hidden" name="menu_id" value="<?= $mid ?>"><button class="text-xs font-bold text-red-600 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50"><i class="fa fa-trash mr-1"></i>Hapus Menu Ini</button></form>
</div>
<div class="bg-white rounded-2xl border p-3">
<div class="flex flex-wrap items-center gap-2 mb-2">
<p class="text-xs font-bold uppercase text-slate-400"><i class="fa fa-sitemap mr-1"></i>Struktur: <?= Helper::e($cur['name']??'') ?> (<?= count($items) ?>)</p>
<span class="ml-auto flex gap-1">
<button id="btnExpand" class="text-[11px] border rounded-lg px-2 py-1">Buka semua</button>
<button id="btnSaveOrder" class="text-[11px] bg-emerald-600 text-white rounded-lg px-3 py-1 font-bold"><i class="fa fa-floppy-disk mr-1"></i>Simpan Urutan</button>
</span></div>
<?php if(!$items): ?><p class="text-sm text-slate-500 text-center py-8">Menu kosong. Tambah dari panel kiri.</p><?php else: ?>
  <div class="text-[11px] text-slate-500 bg-emerald-50 border border-emerald-200 rounded-lg p-2 mb-2"><i class="fa fa-hand-pointer mr-1 text-emerald-600"></i><b>Cara pakai:</b> seret ke <b>atas/bawah</b> untuk urutan • seret ke <b>tengah kartu lain</b> (kartu menyala hijau) untuk jadikan <b>anak menu</b> • seret ke <b>area kosong bawah / tepi kiri kartu</b> untuk keluarkan jadi <b>induk</b> • atau klik tombol <i class="fa fa-arrow-right-from-bracket text-amber-600"></i> <b>Keluar</b> di editor item.</div>
  <div id="rootDrop" class="hidden text-center text-xs font-bold text-emerald-700 border-2 border-dashed border-emerald-400 bg-emerald-50 rounded-xl p-3 mb-2"><i class="fa fa-arrow-up-from-bracket mr-1"></i>Lepaskan di sini untuk jadikan MENU INDUK</div>
<ul id="menuTree" class="mkids grid gap-1.5 min-h-[80px]">
<?php foreach($tree as $n)$node($n); ?>
</ul><?php endif; ?>
</div></div></div>
<?php endif; ?>
<style>
.mkids:empty{border:2px dashed #e2e8f0;border-radius:.75rem}
.mkids .mkids:empty::after{content:'Seret ke sini untuk jadikan anak menu';display:block;font-size:11px;color:#94a3b8;text-align:center;padding:6px}
#menuTree.dragover{outline:2px dashed #10b981;outline-offset:4px;border-radius:.75rem}
.mnode.drop-parent>.mcard{border-color:#10b981!important;box-shadow:0 0 0 3px #a7f3d0;background:#ecfdf5!important}
.mnode.drop-parent>.mcard::after{content:'Lepaskan: jadikan anak menu';font-size:10px;font-weight:700;color:#059669;margin-left:auto}
.mnode{list-style:none}
#dropHint{height:8px;border-radius:99px;background:#10b981;margin:4px 0 4px 24px}
.m-drag-handle{touch-action:none;user-select:none;padding:.35rem}
.m-drag-ghost{position:fixed;z-index:9999;pointer-events:none;opacity:.92;box-shadow:0 18px 45px rgba(15,23,42,.22);transform:rotate(1deg);max-width:min(520px,80vw)}
</style>
<script>
const CSRF='<?= Security::csrfToken() ?>',MID=<?= (int)$mid ?>;
// toggle Jenis di form tambah: link <-> mega
document.getElementById('addKind')?.addEventListener('change',e=>{
  const mega=e.target.value==='mega';
  document.getElementById('addUrlWrap').style.display=mega?'none':'';
  document.getElementById('addMegaWrap').style.display=mega?'':'none';
});
// toggle Jenis di tiap editor item
document.querySelectorAll('.m-kind').forEach(s=>s.addEventListener('change',()=>{
  const mega=s.value==='mega',box=s.closest('form');
  box.querySelector('.m-urlwrap').style.display=mega?'none':'';
  box.querySelector('.m-megawrap').style.display=mega?'':'none';
}));
// buka/tutup editor + tombol Edit & Hapus yang jelas
document.querySelectorAll('.mnode').forEach(li=>{
  const card=li.querySelector(':scope > .mcard'), body=li.querySelector(':scope > .m-body');
  card?.querySelector('.m-edit')?.addEventListener('click',e=>{e.stopPropagation();body.classList.toggle('hidden')});
  card?.addEventListener('click',e=>{if(e.target.closest('button'))return;body.classList.toggle('hidden')});
  body?.querySelector('.m-cancel')?.addEventListener('click',()=>body.classList.add('hidden'));
  li.querySelector('.m-del')?.addEventListener('click',()=>{
    Swal.fire({title:'Hapus "'+card.querySelector('.font-semibold').textContent.trim()+'"?',text:'Submenu di dalamnya ikut terhapus.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya, Hapus',cancelButtonText:'Batal'}).then(r=>{if(r.isConfirmed){fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'csrf='+CSRF+'&act=del_item&id='+li.dataset.id}).then(()=>location.reload())}});
  });
});
document.getElementById('btnExpand')?.addEventListener('click',e=>{const h=[...document.querySelectorAll('.m-body')].some(x=>x.classList.contains('hidden'));document.querySelectorAll('.m-body').forEach(x=>x.classList.toggle('hidden',!h));e.target.textContent=h?'Tutup semua':'Buka semua'});
// Geser satu posisi tanpa drag, tetap di level induk yang sama.
function updateMoveButtons(){
  document.querySelectorAll('.mnode').forEach(li=>{
    const siblings=[...li.parentElement.children].filter(x=>x.classList.contains('mnode'));
    const pos=siblings.indexOf(li),up=li.querySelector(':scope > .mcard .m-up'),down=li.querySelector(':scope > .mcard .m-down');
    if(up){up.disabled=pos<=0;up.classList.toggle('opacity-30',pos<=0);up.classList.toggle('cursor-not-allowed',pos<=0)}
    if(down){down.disabled=pos<0||pos===siblings.length-1;down.classList.toggle('opacity-30',down.disabled);down.classList.toggle('cursor-not-allowed',down.disabled)}
  });
}
document.querySelectorAll('.m-up,.m-down').forEach(button=>button.addEventListener('click',e=>{
  e.stopPropagation();const li=button.closest('.mnode');if(!li)return;
  const siblings=[...li.parentElement.children].filter(x=>x.classList.contains('mnode')),pos=siblings.indexOf(li);
  if(button.classList.contains('m-up')&&pos>0)siblings[pos-1].before(li);
  if(button.classList.contains('m-down')&&pos>=0&&pos<siblings.length-1)siblings[pos+1].after(li);
  updateMoveButtons();autoSave();
}));
updateMoveButtons();
// toggle aktif
document.querySelectorAll('.m-tg').forEach(b=>b.addEventListener('click',()=>{fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'csrf='+CSRF+'&act=toggle&id='+b.dataset.id}).then(()=>location.reload())}));
// tombol Keluar manual: keluarkan anak jadi induk (tanpa drag)
document.querySelectorAll('.m-out').forEach(b=>b.addEventListener('click',()=>{
  const li=b.closest('li.mnode');const root=document.getElementById('menuTree');if(!li||!root)return;
  root.appendChild(li);syncDepthAll();updateMoveButtons();autoSave();
  Swal.fire({icon:'success',title:'Sudah jadi menu induk',timer:1100,showConfirmButton:false});
}));
/* === DRAG-DROP MENU ===
const tree=document.getElementById('menuTree'),rootDrop=document.getElementById('rootDrop');
let drag=null,hint=null,dropAction=null;
function ensureHint(){
  if(!hint){hint=document.createElement('li');hint.id='dropHint';hint.setAttribute('aria-hidden','true')}
  return hint;
}
function clearDropState(){
  hint?.remove();
  document.querySelectorAll('.mnode.drop-parent').forEach(x=>x.classList.remove('drop-parent'));
  rootDrop?.classList.remove('bg-emerald-100');dropAction=null;
}
function syncDepthAll(){
  if(!tree)return;
  function walk(el,d){el.dataset.depth=d;el.querySelectorAll(':scope > ul.mkids > li.mnode').forEach(ch=>walk(ch,d+1))}
  tree.querySelectorAll(':scope > li.mnode').forEach(li=>walk(li,0));
}
function finishDrop(){
  if(!drag||!dropAction)return;
  const moving=drag,action=dropAction;
  if(action.type==='child')action.parent.querySelector(':scope > ul.mkids').appendChild(moving);
  if(action.type==='line')action.hint.replaceWith(moving);
  if(action.type==='root')tree.appendChild(moving);
  moving.classList.remove('opacity-40');rootDrop?.classList.add('hidden');
  clearDropState();syncDepthAll();drag=null;autoSave();
}
if(tree){
  tree.querySelectorAll('li.mnode').forEach(li=>{
    const card=li.querySelector(':scope > .mcard');
    li.addEventListener('dragstart',e=>{
      e.stopPropagation();drag=li;clearDropState();
      e.dataTransfer.effectAllowed='move';e.dataTransfer.setData('text/plain',li.dataset.id);
      rootDrop?.classList.remove('hidden');requestAnimationFrame(()=>li.classList.add('opacity-40'));
    });
    li.addEventListener('dragend',e=>{
      e.stopPropagation();li.classList.remove('opacity-40');rootDrop?.classList.add('hidden');
      clearDropState();drag=null;
    });
    card?.addEventListener('dragover',e=>{
      if(!drag||drag===li||drag.contains(li))return;
      e.preventDefault();e.stopPropagation();e.dataTransfer.dropEffect='move';clearDropState();
      const rect=card.getBoundingClientRect(),position=(e.clientY-rect.top)/rect.height;
      if(position>.25&&position<.75){
        li.classList.add('drop-parent');dropAction={type:'child',parent:li};
      }else{
        const h=ensureHint();position<=.5?li.before(h):li.after(h);
        dropAction={type:'line',hint:h};
      }
    });
    card?.addEventListener('drop',e=>{e.preventDefault();e.stopPropagation();finishDrop()});
  });
  tree.querySelectorAll('ul.mkids').forEach(ul=>{
    ul.addEventListener('dragover',e=>{
      if(!drag||e.target!==ul)return;
      e.preventDefault();e.stopPropagation();clearDropState();
      const h=ensureHint();ul.appendChild(h);dropAction={type:'line',hint:h};
    });
    ul.addEventListener('drop',e=>{if(e.target===ul){e.preventDefault();e.stopPropagation();finishDrop()}});
  });
  tree.addEventListener('dragover',e=>{
    if(!drag||e.target!==tree)return;
    e.preventDefault();e.stopPropagation();clearDropState();
    const h=ensureHint();tree.appendChild(h);dropAction={type:'line',hint:h};
  });
  tree.addEventListener('drop',e=>{if(e.target===tree){e.preventDefault();e.stopPropagation();finishDrop()}});
  rootDrop?.addEventListener('dragover',e=>{
    if(!drag)return;e.preventDefault();e.stopPropagation();clearDropState();
    rootDrop.classList.add('bg-emerald-100');dropAction={type:'root'};
  });
  rootDrop?.addEventListener('drop',e=>{e.preventDefault();e.stopPropagation();finishDrop()});
}*/
// Pointer drag avoids nested HTML5 drag events bubbling through parent <li> nodes.
const tree=document.getElementById('menuTree'),rootDrop=document.getElementById('rootDrop');
function syncDepthAll(){if(!tree)return;function walk(el,d){el.dataset.depth=d;el.querySelectorAll(':scope > ul.mkids > li.mnode').forEach(ch=>walk(ch,d+1))}tree.querySelectorAll(':scope > li.mnode').forEach(li=>walk(li,0))}
(()=>{if(!tree)return;let active=null,ghost=null,placeholder=null;
const nodes=[...tree.querySelectorAll('.mnode')];
const clear=()=>{placeholder?.remove();placeholder=null;ghost?.remove();ghost=null;nodes.forEach(n=>n.classList.remove('opacity-40','drop-parent'));rootDrop?.classList.add('hidden')};
const end=()=>{if(!active)return;const n=active.node,origin=active.origin;let moved=!!active.moved;if(placeholder?.parentElement){placeholder.replaceWith(n);moved=true}clear();active=null;syncDepthAll();updateMoveButtons();if(moved||n.parentElement!==origin)autoSave()};
nodes.forEach(n=>n.querySelector('.m-drag-handle')?.addEventListener('pointerdown',e=>{e.preventDefault();e.stopPropagation();active={node:n,origin:n.parentElement,moved:false};n.classList.add('opacity-40');ghost=n.querySelector(':scope > .mcard').cloneNode(true);ghost.classList.add('m-drag-ghost');document.body.appendChild(ghost);rootDrop?.classList.remove('hidden');move(e);window.addEventListener('pointermove',move);window.addEventListener('pointerup',up,{once:true});}));
function move(e){if(!active)return;ghost.style.left=(e.clientX+12)+'px';ghost.style.top=(e.clientY+12)+'px';nodes.forEach(n=>n.classList.remove('drop-parent'));const el=document.elementFromPoint(e.clientX,e.clientY)?.closest('.mnode');if(!el||el===active.node||active.node.contains(el)){placeholder?.remove();placeholder=null;return}const card=el.querySelector(':scope > .mcard'),r=card.getBoundingClientRect(),f=(e.clientY-r.top)/r.height;placeholder?.remove();placeholder=document.createElement('li');placeholder.id='dropHint';if(f>.28&&f<.72){el.classList.add('drop-parent');el.querySelector(':scope > ul.mkids').appendChild(placeholder)}else{f<.5?el.before(placeholder):el.after(placeholder)}}
function up(e){window.removeEventListener('pointermove',move);const toRoot=rootDrop?.contains(document.elementFromPoint(e.clientX,e.clientY));if(toRoot){placeholder?.remove();placeholder=null;tree.appendChild(active.node);active.moved=true}end()}
})();
let saveT=null;
function autoSave(){clearTimeout(saveT);saveT=setTimeout(()=>saveOrder(true),600)}
function kidsToTree(ul){
  const out=[];
  ul.querySelectorAll(':scope > li.mnode').forEach(li=>{out.push({id:li.dataset.id,children:kidsToTree(li.querySelector(':scope > ul.mkids'))})});
  return out;
}
function saveOrder(silent){
  if(!tree)return Promise.resolve(false);
  return fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'csrf='+CSRF+'&ajax=1&act=reorder&menu_id='+MID+'&tree='+encodeURIComponent(JSON.stringify(kidsToTree(tree)))})
  .then(r=>r.json()).then(j=>{if(j.ok&&silent===false)Swal.fire({icon:'success',title:'Urutan tersimpan',timer:1100,showConfirmButton:false});return j.ok}).catch(()=>false);
}
document.getElementById('btnSaveOrder')?.addEventListener('click',()=>saveOrder(false));
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>
