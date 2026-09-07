<?php declare(strict_types=1); $isFooterPage=rtrim(Router::uri(),'/')==='/admin/footer'; $fixedArea=$isFooterPage?'footer':null; $redirectAdmin=$isFooterPage?'admin/footer':'admin/widgets'; $title=$isFooterPage?'Footer':'Widget Sidebar';
try{$db->exec("ALTER TABLE widgets ADD COLUMN animation VARCHAR(30) NOT NULL DEFAULT 'zoom'");}catch(Throwable){}
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url($redirectAdmin)); exit; }
$act=$_POST['act']??'save';
if($act==='delete'){ $db->prepare("DELETE FROM widgets WHERE id=?")->execute([(int)$_POST['id']]); Session::flash('ok','Dihapus.'); }
elseif($act==='move'){
  $id=(int)($_POST['id']??0);$dir=$_POST['dir']??'';$cur=$db->prepare("SELECT id,area,sort_order FROM widgets WHERE id=?");$cur->execute([$id]);$current=$cur->fetch();
  if($current&&in_array($dir,['up','down'],true)){$items=$db->prepare("SELECT id FROM widgets WHERE area=? ORDER BY sort_order,id");$items->execute([$current['area']]);$ids=array_map('intval',array_column($items->fetchAll(),'id'));$pos=array_search($id,$ids,true);$other=$dir==='up'?$pos-1:$pos+1;if($pos!==false&&isset($ids[$other])){[$ids[$pos],$ids[$other]]=[$ids[$other],$ids[$pos]];$db->beginTransaction();$setOrder=$db->prepare("UPDATE widgets SET sort_order=? WHERE id=?");foreach($ids as $i=>$widgetId)$setOrder->execute([$i+1,$widgetId]);$db->commit();Session::flash('ok','Urutan widget diperbarui.');}}
  header('Location: '.Helper::url($redirectAdmin));exit;
}
else{ $tt=trim((string)($_POST['title']??'')); $type=$_POST['type']??'html'; $content=(string)($_POST['content']??'');
  if($type==='image')$content=trim(strip_tags(html_entity_decode($content,ENT_QUOTES|ENT_HTML5,'UTF-8')));
  if($type==='image'&&!empty($_FILES['widget_image']['name']??'')){ $e=Security::validImage($_FILES['widget_image'],$APP); if($e){Session::flash('err',$e);header('Location: '.Helper::url($redirectAdmin));exit;} $n=Security::safeName($_FILES['widget_image']['name']); move_uploaded_file($_FILES['widget_image']['tmp_name'],ROOT.'/assets/uploads/'.$n); $fi=new finfo(FILEINFO_MIME_TYPE); $mime=$fi->file(ROOT.'/assets/uploads/'.$n); $db->prepare("INSERT INTO media(filename,filepath,mime,extension,size_bytes,uploaded_by) VALUES(?,?,?,?,?,?)")->execute([$n,'assets/uploads/'.$n,$mime,strtolower(pathinfo($n,PATHINFO_EXTENSION)),filesize(ROOT.'/assets/uploads/'.$n),$_SESSION['user']['id']]); $content=$n; }
  if($type==='image'&&trim($content)===''){Session::flash('err','Pilih foto untuk widget gambar.');header('Location: '.Helper::url($redirectAdmin));exit;}
  $animation=in_array($_POST['animation']??'zoom',['none','zoom','lift','grayscale','blur'],true)?$_POST['animation']:'zoom';
  $area=$fixedArea??($_POST['area']??'sidebar');
  if(!empty($_POST['id'])) $db->prepare("UPDATE widgets SET area=?,type=?,title=?,content=?,animation=?,sort_order=?,is_active=? WHERE id=?")->execute([$area,$type,$tt,$content,$animation,(int)($_POST['sort_order']??0),(int)($_POST['is_active']??1),(int)$_POST['id']]); else $db->prepare("INSERT INTO widgets(area,type,title,content,animation,sort_order,is_active) VALUES(?,?,?,?,?,?,?)")->execute([$area,$type,$tt,$content,$animation,(int)($_POST['sort_order']??0),(int)($_POST['is_active']??1)]); Session::flash('ok','Disimpan.'); }
header('Location: '.Helper::url($redirectAdmin)); exit; }
$rows=$fixedArea?$db->prepare("SELECT * FROM widgets WHERE area=? ORDER BY sort_order,id"):null;
if($fixedArea){$rows->execute([$fixedArea]);$rows=$rows->fetchAll();}else{$rows=$db->query("SELECT * FROM widgets WHERE area<>'footer' ORDER BY area,sort_order,id")->fetchAll();}
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa <?= $isFooterPage?'fa-shoe-prints':'fa-puzzle-piece' ?> text-emerald-600 mr-1"></i><?= $isFooterPage?'Footer':'Widget Sidebar' ?></h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($rows) ?> widget</span>
<button id="btnAdd" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i><?= $isFooterPage?'Tambah Konten Footer':'Tambah Widget' ?></button>
</div>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[640px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3">Judul / Tipe</th><th class="p-3">Area</th><th class="p-3">Urut</th><th class="p-3">Status</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$rows): ?><tr><td colspan="5" class="p-10 text-center text-slate-500"><i class="fa <?= $isFooterPage?'fa-shoe-prints':'fa-puzzle-piece' ?> text-3xl block mb-2"></i><?= $isFooterPage?'Belum ada konten footer.':'Belum ada widget. Klik Tambah Widget.' ?></td></tr><?php endif; ?>
<?php foreach($rows as $r): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3 font-semibold"><?= Helper::e($r['title']?:$r['type']) ?><span class="block text-[11px] font-normal text-slate-400 font-mono"><?= Helper::e($r['type']) ?></span></td>
<td class="p-3 text-xs text-slate-500"><?= Helper::e($r['area']) ?></td>
<td class="p-3 text-xs"><?= (int)$r['sort_order'] ?></td>
<td class="p-3"><span class="text-xs font-bold px-2 py-0.5 rounded-full <?= $r['is_active']?'bg-emerald-100 text-emerald-700':'bg-slate-100 text-slate-500' ?>"><?= $r['is_active']?'Aktif':'Nonaktif' ?></span></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<form method="post" class="inline-flex gap-1"><?= Security::csrfField() ?><input type="hidden" name="act" value="move"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button name="dir" value="up" class="w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Naik"><i class="fa fa-arrow-up text-xs"></i></button><button name="dir" value="down" class="w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Turun"><i class="fa fa-arrow-down text-xs"></i></button></form>
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$r['id'],'area'=>$r['area'],'type'=>$r['type'],'title'=>$r['title']??'','content'=>$r['type']==='image'?trim(strip_tags(html_entity_decode((string)($r['content']??''),ENT_QUOTES|ENT_HTML5,'UTF-8'))):($r['content']??''),'animation'=>$r['animation']??'zoom','sort_order'=>(int)$r['sort_order'],'is_active'=>(int)$r['is_active']]),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>

<div id="widgetModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl my-4">
<div class="flex items-center gap-2 px-5 py-3.5 border-b bg-white"><h2 class="font-extrabold" id="modalTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Widget</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" enctype="multipart/form-data" data-loading id="widgetForm" class="p-5 grid gap-3 text-sm bg-white"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="f_id" value="0">
<?php if($isFooterPage): ?><input type="hidden" name="area" id="f_area" value="footer"><div class="rounded-lg bg-slate-50 border p-2 text-slate-600">Area: <b>Footer</b></div><?php else: ?><label class="grid gap-1 font-semibold">Area<select name="area" id="f_area" class="border rounded-lg p-2 font-normal"><option value="sidebar">Sidebar Berita</option><option value="homepage">Homepage</option></select></label><?php endif; ?>
<label class="grid gap-1 font-semibold">Tipe<select name="type" id="f_type" class="border rounded-lg p-2 font-normal"><option value="html">Custom HTML</option><option value="text">Teks</option><option value="image">Gambar</option><option value="about">Tentang Sekolah</option><option value="menu">Menu Navigasi</option><option value="categories">Kategori Berita</option><option value="links">Tautan Halaman</option><?php if(!$isFooterPage): ?><option value="posts">Latest Posts</option><option value="agenda">Agenda</option><option value="announcements">Announcements</option><?php endif; ?><option value="contact">Contact</option><option value="social">Social Media</option></select></label>
<label class="grid gap-1 font-semibold">Judul<input name="title" id="f_title" placeholder="Judul" class="border rounded-lg p-2 font-normal"></label>
<label id="contentField" class="grid gap-1 font-semibold"><span id="contentLabel">Konten</span><textarea name="content" id="f_content" rows="5" placeholder="Teks pengantar atau konten widget" class="border rounded-lg p-2 font-normal"></textarea><span id="contentHelp" class="text-xs font-normal text-slate-500">Bisa berisi teks terformat, tautan, atau HTML.</span></label>
<div id="linksField" class="hidden grid gap-1.5 font-semibold"><span class="text-xs">Tautan <span class="font-normal text-slate-400">per baris: Nama | /url</span></span>
<div id="linksList" class="grid gap-1.5"></div>
<button type="button" id="linksAdd" class="text-[11px] font-bold border rounded-lg px-2 py-1 hover:border-emerald-400"><i class="fa fa-plus mr-1"></i>Tambah Tautan</button></div>
<input type="hidden" name="content" id="f_links_data">
<label id="imageField" class="hidden grid gap-1 font-semibold"><span>Upload Foto</span><img id="f_image_preview" alt="Pratinjau widget" class="hidden w-full max-h-52 object-contain rounded-xl border bg-slate-50 p-1"><input type="file" name="widget_image" id="f_image" accept="image/*" class="border rounded-lg p-2 font-normal"><span class="text-xs font-normal text-slate-500">Foto akan disimpan ke Media dan ditampilkan di widget.</span></label>
<label id="animationField" class="hidden grid gap-1 font-semibold">Animasi saat hover<select name="animation" id="f_animation" class="border rounded-lg p-2 font-normal"><option value="zoom">Zoom halus</option><option value="lift">Naik sedikit</option><option value="grayscale">Warna muncul</option><option value="blur">Fokus gambar</option><option value="none">Tanpa animasi</option></select></label>
<label class="grid gap-1 font-semibold">Urutan<input type="number" name="sort_order" id="f_sort" value="0" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Status<select name="is_active" id="f_active" class="border rounded-lg p-2 font-normal"><option value="1">Aktif</option><option value="0">Nonaktif</option></select></label>
<div class="flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.1/tinymce.min.js"></script>
<script>
const modal=document.getElementById('widgetModal');
let widgetEditor=null,pendingWidgetContent='';
const editorTypes=['html','text','announcements'];
function syncWidgetSelect(id){const sel=document.getElementById(id);if(!sel)return;sel.dispatchEvent(new Event('change',{bubbles:true}));if(sel._cpaint)sel._cpaint();else if(sel._csync)sel._csync();else if(window.__refreshSelects&&window.__refreshSelects[id])window.__refreshSelects[id]()}
function ensureWidgetEditor(){if(widgetEditor||!editorTypes.includes(document.getElementById('f_type').value)||!window.RichEditorCreate)return;window.RichEditorCreate(document.getElementById('f_content'),{height:260,menubar:false,toolbar:['undo redo | blocks | bold italic underline | bullist numlist | link | alignleft aligncenter alignright | code']}).then(e=>{widgetEditor=e;e.setData(pendingWidgetContent);pendingWidgetContent=''}).catch(()=>{})}
function openModal(d){
  document.getElementById('modalTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Widget':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Widget');
  document.getElementById('f_id').value=d?.id||0;
  document.getElementById('f_area').value=d?.area||'sidebar';
  document.getElementById('f_type').value=d?.type||'html';
  document.getElementById('f_title').value=d?.title||'';
  pendingWidgetContent=d?.content||'';
  if(widgetEditor&&editorTypes.includes(d?.type||'html'))widgetEditor.setData(pendingWidgetContent);else document.getElementById('f_content').value=pendingWidgetContent;
  const preview=document.getElementById('f_image_preview'),content=d?.content||'';
  if((d?.type==='image')&&content){preview.src=/^(https?:)?\/\//i.test(content)||content.startsWith('/')?content:'<?= Helper::url('assets/uploads/') ?>/'+content;preview.classList.remove('hidden')}else{preview.removeAttribute('src');preview.classList.add('hidden')}
  if(d?.type==='links')loadLinks(d?.content||'');
  toggleImageField();
  document.getElementById('f_sort').value=d?.sort_order??0;
  document.getElementById('f_active').value=String(d?.is_active??1);
  document.getElementById('f_animation').value=d?.animation||'zoom';
  syncWidgetSelect('f_area');syncWidgetSelect('f_type');syncWidgetSelect('f_active');syncWidgetSelect('f_animation');
  modal.classList.remove('hidden');document.body.style.overflow='hidden';ensureWidgetEditor();
}
function closeModal(){modal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAdd').addEventListener('click',()=>openModal(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openModal(JSON.parse(b.dataset.row))));
modal.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
function toggleImageField(){const type=document.getElementById('f_type').value,image=type==='image',links=type==='links',hasContent=editorTypes.includes(type);document.getElementById('imageField').classList.toggle('hidden',!image);document.getElementById('animationField').classList.toggle('hidden',!image);document.getElementById('contentField').classList.toggle('hidden',!hasContent);document.getElementById('linksField').classList.toggle('hidden',!links);document.getElementById('contentLabel').textContent=type==='announcements'?'Pengantar Pengumuman':'Konten';document.getElementById('contentHelp').textContent=type==='announcements'?'Teks ini tampil di atas daftar otomatis pada widget.':'Bisa berisi teks terformat, tautan, atau HTML.';if(hasContent&&!modal.classList.contains('hidden'))ensureWidgetEditor()}
document.getElementById('f_type').addEventListener('change',toggleImageField);toggleImageField();
document.getElementById('f_image').addEventListener('change',e=>{const file=e.target.files[0],preview=document.getElementById('f_image_preview');if(!file)return;preview.src=URL.createObjectURL(file);preview.classList.remove('hidden')});
document.getElementById('widgetForm').addEventListener('submit',()=>{if(widgetEditor&&editorTypes.includes(document.getElementById('f_type').value)){try{document.getElementById('f_content').value=widgetEditor.getData()}catch(_){} }
  if(document.getElementById('f_type').value==='links'){document.getElementById('f_links_data').value=serializeLinks();}
});
function buildLinkRow(label='',url=''){const list=document.getElementById('linksList');const div=document.createElement('div');div.className='flex gap-1.5 items-center';div.innerHTML='<input name="link_label[]" placeholder="Nama" value="'+label.replace(/"/g,'&quot;')+'" class="border rounded-lg p-1.5 flex-1 min-w-0"><input name="link_url[]" value="'+url.replace(/"/g,'&quot;')+'" placeholder="/halaman atau https://..." class="border rounded-lg p-1.5 flex-1 min-w-0 font-mono text-xs"><button type="button" class="linkDel w-7 h-7 border rounded-lg grid place-items-center bg-white text-red-600 shrink-0" title="Hapus"><i class="fa fa-trash text-[10px]"></i></button>';list.appendChild(div);div.querySelector('.linkDel').addEventListener('click',()=>div.remove());}
function loadLinks(json){const list=document.getElementById('linksList');list.innerHTML='';let parsed=[];try{parsed=JSON.parse(json||'[]');}catch(_){if(json&&json.trim())parsed=json.split('\n').map(l=>{const p=l.split('|',2);return{label:p[0]||'',url:p[1]||''};});}
if(!parsed.length){buildLinkRow();return;}parsed.forEach(l=>buildLinkRow(l.label||'',l.url||''));}
function serializeLinks(){const rows=document.querySelectorAll('#linksList > div');const data=[];rows.forEach(r=>{const l=r.querySelector('input[name="link_label[]"]')?.value||'';const u=r.querySelector('input[name="link_url[]"]')?.value||'';if(l||u)data.push({label:l,url:u});});return JSON.stringify(data);}
document.getElementById('linksAdd')?.addEventListener('click',()=>buildLinkRow());
document.getElementById('widgetForm').addEventListener('submit',()=>{if(widgetEditor&&editorTypes.includes(document.getElementById('f_type').value)){try{document.getElementById('f_content').value=widgetEditor.getData()}catch(_){} } });
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>




