<?php declare(strict_types=1); $title='Guru & Staff'; $edit=null;
if(isset($_GET['edit'])){ $s=$db->prepare("SELECT * FROM teachers WHERE id=?"); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/teachers')); exit; }
$act=$_POST['act']??'save'; $nm=trim($_POST['name']??'');
if($act==='import_chunk'){
  header('Content-Type: application/json');
  $rows=json_decode($_POST['rows_json']??'[]',true);
  if(!is_array($rows)||!$rows){ echo json_encode(['ok'=>false,'msg'=>'Data kosong']); exit; }
  $added=0;$skipped=0;
  $ins=$db->prepare("INSERT INTO teachers(name,nip,position,type,photo,education,subject,description,is_active,sort_order) VALUES(?,?,?,?,?,?,?,?,?,?)");
  foreach(array_slice($rows,0,200) as $r){
    $n=trim((string)($r['name']??''));
    if($n===''){ $skipped++; continue; }
    $type=strtolower(trim((string)($r['type']??'guru')));
    if(!in_array($type,['guru','tendik'],true)) $type='guru';
    $ins->execute([$n,trim((string)($r['nip']??'')),trim((string)($r['position']??'Guru'))?:'Guru',$type,null,trim((string)($r['education']??'')),trim((string)($r['subject']??'')),trim((string)($r['description']??'')),isset($r['is_active'])?(int)$r['is_active']:1,(int)($r['sort_order']??0)]);
    $added++;
  }
  Auth::log($db,'import','teachers',"Impor chunk $added guru");
  echo json_encode(['ok'=>true,'added'=>$added,'skipped'=>$skipped]); exit;
}
if($act==='import'){
  $rows=json_decode($_POST['rows_json']??'[]',true);
  if(!is_array($rows)||!$rows){ Session::flash('err','File kosong / format tidak dikenali.'); header('Location: '.Helper::url('admin/teachers')); exit; }
  $added=0;$skipped=0;
  $ins=$db->prepare("INSERT INTO teachers(name,nip,position,type,photo,education,subject,description,is_active,sort_order) VALUES(?,?,?,?,?,?,?,?,?,?)");
  foreach(array_slice($rows,0,500) as $r){
    $n=trim((string)($r['name']??''));
    if($n===''){ $skipped++; continue; }
    $type=strtolower(trim((string)($r['type']??'guru')));
    if(!in_array($type,['guru','tendik'],true)) $type='guru';
    $ins->execute([$n,trim((string)($r['nip']??'')),trim((string)($r['position']??'Guru'))?:'Guru',$type,null,trim((string)($r['education']??'')),trim((string)($r['subject']??'')),trim((string)($r['description']??'')),isset($r['is_active'])?(int)$r['is_active']:1,(int)($r['sort_order']??0)]);
    $added++;
  }
  Auth::log($db,'import','teachers',"Impor $added guru"); Session::flash('ok',"$added guru diimpor.".($skipped?" $skipped baris kosong dilewati.":''));
  header('Location: '.Helper::url('admin/teachers')); exit;
}
if($act==='bulk_delete'){ $ids=array_filter(array_map('intval',(array)($_POST['ids']??[]))); if(!$ids){ Session::flash('err','Pilih minimal 1 data.'); } else { $ph=implode(',',array_fill(0,count($ids),'?')); $db->prepare("DELETE FROM teachers WHERE id IN ($ph)")->execute(array_values($ids)); Auth::log($db,'delete','teachers','Hapus bulk guru'); Session::flash('ok',count($ids).' data dihapus.'); } }
elseif($act==='delete'){ $db->prepare("DELETE FROM teachers WHERE id=?")->execute([(int)$_POST['id']]); Auth::log($db,'delete','teachers','Hapus guru'); Session::flash('ok','Data dihapus.'); }
elseif($nm===''){ Session::flash('err','Nama wajib.'); }
else{ $ph=$_POST['old_photo']??null; if(!empty($_FILES['photo']['name']??'')){ $e=Security::validImage($_FILES['photo'],$APP); if($e){ Session::flash('err',$e); header('Location: '.Helper::url('admin/teachers')); exit; } $n=Security::safeName($_FILES['photo']['name']); move_uploaded_file($_FILES['photo']['tmp_name'],ROOT.'/assets/uploads/'.$n); $ph=$n; }
if(!empty($_POST['id'])) $db->prepare("UPDATE teachers SET name=?,nip=?,position=?,type=?,photo=?,education=?,subject=?,description=?,is_active=?,sort_order=? WHERE id=?")->execute([$nm,$_POST['nip']??'',$_POST['position']??'Guru',$_POST['type']??'guru',$ph,$_POST['education']??'',$_POST['subject']??'',$_POST['description']??'',(int)($_POST['is_active']??1),(int)($_POST['sort_order']??0),(int)$_POST['id']]);
else $db->prepare("INSERT INTO teachers(name,nip,position,type,photo,education,subject,description,is_active,sort_order) VALUES(?,?,?,?,?,?,?,?,?,?)")->execute([$nm,$_POST['nip']??'',$_POST['position']??'Guru',$_POST['type']??'guru',$ph,$_POST['education']??'',$_POST['subject']??'',$_POST['description']??'',(int)($_POST['is_active']??1),(int)($_POST['sort_order']??0)]);
Auth::log($db,'save','teachers',"Simpan $nm"); Session::flash('ok','Data disimpan.'); }
header('Location: '.Helper::url('admin/teachers')); exit; }
$rows=$db->query("SELECT * FROM teachers ORDER BY sort_order,id DESC")->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-chalkboard-user text-emerald-600 mr-1"></i>Guru & Staff</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($rows) ?> orang</span>
<button id="btnAdd" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah Guru/Staff</button>
<button id="btnImport" class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-file-excel mr-1"></i>Impor Excel</button>
</div>
<form method="post" id="bulkForm"><?= Security::csrfField() ?><input type="hidden" name="act" value="bulk_delete"></form>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b text-sm"><span id="selCount" class="text-slate-500">0 dipilih</span><button type="button" id="btnBulkTeacher" class="ml-auto bg-red-600 hover:bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="fa fa-trash mr-1"></i>Hapus Terpilih</button></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[800px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-8"><input type="checkbox" id="checkAllTeacher"></th><th class="p-3 w-10">No</th><th class="p-3">Foto</th><th class="p-3">Nama</th><th class="p-3">NUPTK</th><th class="p-3">Jabatan</th><th class="p-3">Status</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$rows): ?><tr><td colspan="8" class="p-10 text-center text-slate-500"><i class="fa fa-chalkboard-user text-3xl block mb-2"></i>Belum ada data. Klik Tambah Guru/Staff.</td></tr><?php endif; ?>
<?php $no=1; foreach($rows as $r): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= $r['id'] ?>" class="rowcheck"></td><td class="p-3 text-slate-500"><?= $no++ ?></td><td class="p-3"><?php if(!empty($r['photo'])): ?><img src="<?= Helper::upload($r['photo']) ?>" alt="" class="w-10 h-10 rounded-full object-cover border"><?php else: ?><span class="w-10 h-10 rounded-full bg-slate-100 border grid place-items-center text-slate-400"><i class="fa fa-user text-sm"></i></span><?php endif; ?></td>
<td class="p-3 font-semibold"><?= Helper::e($r['name']) ?><span class="block text-xs font-normal text-slate-500"><?= Helper::e($r['subject']??'') ?></span></td>
<td class="p-3 font-mono text-xs"><?= Helper::e($r['nip']??'') ?></td>
<td class="p-3"><?= Helper::e($r['position']) ?><span class="block text-[11px] text-slate-400"><?= Helper::e($r['type']??'') ?></span></td>
<td class="p-3"><span class="text-xs font-bold px-2 py-0.5 rounded-full <?= $r['is_active']?'bg-emerald-100 text-emerald-700':'bg-slate-200 text-slate-600' ?>"><?= $r['is_active']?'Aktif':'Nonaktif' ?></span></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$r['id'],'name'=>$r['name'],'nip'=>$r['nip']??'','position'=>$r['position'],'type'=>$r['type'],'photo'=>$r['photo']??'','education'=>$r['education']??'','subject'=>$r['subject']??'','description'=>$r['description']??'','is_active'=>(int)$r['is_active'],'sort_order'=>(int)$r['sort_order']]),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>

<div id="teacherModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl my-4">
<div class="flex items-center gap-2 px-4 py-3 border-b bg-white"><h2 class="font-extrabold text-sm" id="modalTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Guru/Staff</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" enctype="multipart/form-data" data-loading class="p-4 grid gap-2.5 text-sm bg-white" id="teacherForm"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="f_id" value="0"><input type="hidden" name="old_photo" id="f_old" value="">
<label class="grid gap-1 font-semibold">Nama<input name="name" id="f_name" required placeholder="Nama lengkap" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">NUPTK<input name="nip" id="f_nip" placeholder="NUPTK" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Jabatan<input name="position" id="f_position" value="Guru" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Tipe<select name="type" id="f_type" class="border rounded-lg p-2 font-normal"><option value="guru">guru</option><option value="tendik">tendik</option></select></label>
<label class="grid gap-1 font-semibold">Mapel / Bidang<input name="subject" id="f_subject" placeholder="Matematika" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Pendidikan<input name="education" id="f_edu" placeholder="S1 Pendidikan" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Urutan<input type="number" name="sort_order" id="f_sort" value="0" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Status<select name="is_active" id="f_active" class="border rounded-lg p-2 font-normal"><option value="1">Aktif</option><option value="0">Nonaktif</option></select></label>
<div class="border rounded-xl p-3 bg-slate-50"><p class="text-xs font-bold mb-1.5"><i class="fa fa-image mr-1 text-emerald-600"></i>Foto</p>
<div class="flex items-start gap-3">
<img id="f_prev" alt="" class="hidden w-20 h-20 rounded-xl object-cover border">
<div class="grid gap-1 flex-1"><input type="file" name="photo" id="f_photo" accept="image/*" class="border rounded-lg p-2 w-full bg-white text-xs"></div>
</div></div>
<label class="grid gap-1 font-semibold">Deskripsi<textarea name="description" id="f_desc" rows="4" placeholder="Profil singkat..." class="border rounded-lg p-2 font-normal"></textarea></label>
<div class="flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close-import></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl my-4">
<div class="flex items-center gap-2 px-5 py-3.5 border-b bg-white"><h2 class="font-extrabold"><i class="fa fa-file-excel text-emerald-600 mr-1"></i>Impor Excel Guru</h2><button data-close-import class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" data-loading id="importForm" class="p-5 grid gap-3 text-sm bg-white" novalidate><?= Security::csrfField() ?>
<input type="hidden" name="act" value="import"><input type="hidden" name="rows_json" id="rows_json">
<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 flex flex-wrap items-center gap-2 text-xs">
<span class="flex-1 min-w-[180px]"><b>Langkah:</b> unduh template → isi → pilih file → impor. Kolom: name, nuptk, position, type, subject, education, description, is_active, sort_order.</span>
<button type="button" id="btnTemplate" class="bg-white border border-emerald-300 text-emerald-700 font-bold px-3 py-1.5 rounded-lg hover:bg-emerald-100"><i class="fa fa-download mr-1"></i>Template .xlsx</button>
</div>
<input type="file" id="importFile" accept=".xlsx,.xls" class="border rounded-lg p-2 w-full bg-white text-xs">
<div class="hidden" id="importProgWrap"><div class="flex justify-between text-xs text-slate-500"><span id="importInfo">Belum ada file.</span><span id="importPct">0%</span></div><div class="h-2.5 rounded-full bg-slate-100 overflow-hidden"><div id="importBar" class="h-full w-0 bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-300"></div></div></div>
<p id="importInfoFallback" class="text-xs text-slate-500">Belum ada file.</p>
<div class="flex gap-2"><button id="importSubmit" disabled class="flex-1 bg-emerald-600 disabled:opacity-40 text-white rounded-xl p-2.5 font-bold"><i class="fa fa-upload mr-1"></i>Impor</button><button type="button" data-close-import class="border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<script>
(function(){const ca=document.getElementById('checkAllTeacher'),sc=document.getElementById('selCount'),bb=document.getElementById('btnBulkTeacher'),bf=document.getElementById('bulkForm');if(!ca||!bb||!bf)return;const up=()=>{sc.textContent=document.querySelectorAll('.rowcheck:checked').length+' dipilih'};ca.addEventListener('change',()=>{document.querySelectorAll('.rowcheck').forEach(c=>c.checked=ca.checked);up()});document.addEventListener('change',e=>{if(e.target.classList&&e.target.classList.contains('rowcheck'))up()});bb.addEventListener('click',()=>{const n=document.querySelectorAll('.rowcheck:checked').length;if(!n){Swal.fire('Pilih dulu','Centang minimal 1 data.','warning');return}Swal.fire({title:'Hapus '+n+' data?',text:'Tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)bf.submit()})});})();
</script>
<script>
const modal=document.getElementById('teacherModal');
const upBase='<?= Helper::url('assets/uploads/') ?>/';
function openModal(d){
  document.getElementById('modalTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit Guru/Staff':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah Guru/Staff');
  document.getElementById('f_id').value=d?.id||0;
  document.getElementById('f_name').value=d?.name||'';
  document.getElementById('f_nip').value=d?.nip||'';
  document.getElementById('f_position').value=d?.position||'Guru';
  document.getElementById('f_type').value=d?.type||'guru';
  document.getElementById('f_sort').value=d?.sort_order??0;
  document.getElementById('f_subject').value=d?.subject||'';
  document.getElementById('f_edu').value=d?.education||'';
  document.getElementById('f_desc').value=d?.description||'';
  document.getElementById('f_active').value=String(d?.is_active??1);
  document.getElementById('f_old').value=d?.photo||'';
  document.getElementById('f_photo').value='';
  const pv=document.getElementById('f_prev');
  if(d?.photo){pv.src=upBase+d.photo;pv.classList.remove('hidden')}else{pv.src='';pv.classList.add('hidden')}
  modal.classList.remove('hidden');document.body.style.overflow='hidden';
}
function closeModal(){modal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAdd').addEventListener('click',()=>openModal(null));
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openModal(JSON.parse(b.dataset.row))));
modal.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
document.getElementById('f_photo').addEventListener('change',e=>{const f=e.target.files[0];if(!f)return;const pv=document.getElementById('f_prev');pv.src=URL.createObjectURL(f);pv.classList.remove('hidden')});
<?php if($edit): ?>openModal(<?= json_encode(['id'=>$edit['id'],'name'=>$edit['name'],'nip'=>$edit['nip']??'','position'=>$edit['position'],'type'=>$edit['type'],'photo'=>$edit['photo']??'','education'=>$edit['education']??'','subject'=>$edit['subject']??'','description'=>$edit['description']??'','is_active'=>(int)$edit['is_active'],'sort_order'=>(int)$edit['sort_order']]) ?>);<?php endif; ?>
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
(function(){
  const im=document.getElementById('importModal'),fin=document.getElementById('importFile'),rj=document.getElementById('rows_json'),sub=document.getElementById('importSubmit');
  const wrap=document.getElementById('importProgWrap'),bar=document.getElementById('importBar'),info=document.getElementById('importInfo'),pct=document.getElementById('importPct'),fallback=document.getElementById('importInfoFallback');
  if(!im)return;
  const CSRF='<?= Security::csrfToken() ?>';
  const setProg=(p,msg)=>{ if(wrap)wrap.classList.remove('hidden'); if(fallback)fallback.classList.add('hidden'); if(bar)bar.style.width=p+'%'; if(pct)pct.textContent=p+'%'; if(info&&msg!==undefined)info.textContent=msg; };
  const open=()=>{im.classList.remove('hidden');document.body.style.overflow='hidden'};
  const close=()=>{im.classList.add('hidden');document.body.style.overflow=''};
  document.getElementById('btnImport')?.addEventListener('click',open);
  im.querySelectorAll('[data-close-import]').forEach(b=>b.addEventListener('click',close));
  document.addEventListener('keydown',e=>{if(e.key==='Escape')close()});
  document.getElementById('btnTemplate')?.addEventListener('click',()=>{
    if(!window.XLSX){ Swal.fire('Gagal','Library Excel belum dimuat. Cek koneksi.','error'); return; }
    const head=['name','nuptk','position','type','subject','education','description','is_active','sort_order'];
    const ex=[['Nama Lengkap','123456','Guru','guru','Matematika','S1 Pendidikan','Profil singkat',1,0]];
    const ws=XLSX.utils.aoa_to_sheet([head,...ex]);
    ws['!cols']=[{wch:22},{wch:14},{wch:16},{wch:10},{wch:16},{wch:16},{wch:26},{wch:10},{wch:10}];
    const wb=XLSX.utils.book_new(); XLSX.utils.book_append_sheet(wb,ws,'Guru');
    XLSX.writeFile(wb,'template-guru.xlsx');
  });
  const norm=h=>String(h||'').toLowerCase().trim().replace(/[^a-z_]/g,'');
  const map={nama:'name',name:'name',nip:'nip',nuptk:'nip',jabatan:'position',position:'position',tipe:'type',type:'type',mapel:'subject',subject:'subject',bidang:'subject',pendidikan:'education',education:'education',deskripsi:'description',description:'description',status:'is_active',aktif:'is_active',is_active:'is_active',urutan:'sort_order',sort_order:'sort_order',sort:'sort_order'};
  let allRows=[];
  fin?.addEventListener('change',()=>{
    const f=fin.files[0]; if(!f)return;
    if(!window.XLSX){ setProg(0,'Library Excel gagal dimuat.'); return; }
    setProg(5,'Membaca '+f.name+'...'); sub.disabled=true;
    const rd=new FileReader();
    rd.onprogress=e=>{ if(e.lengthComputable) setProg(Math.max(5,Math.round(e.loaded/e.total*30)),'Membaca file '+Math.round(e.loaded/e.total*100)+'%...'); };
    rd.onload=e=>{
      try{
        setProg(35,'Parsing Excel...');
        const wb=XLSX.read(e.target.result,{type:'array'});
        const ws=wb.Sheets[wb.SheetNames[0]];
        const rows=XLSX.utils.sheet_to_json(ws,{defval:''});
        setProg(55,'Normalisasi '+rows.length+' baris...');
        allRows=rows.map(r=>{const o={}; Object.keys(r).forEach(k=>{const nk=map[norm(k)]||norm(k); o[nk]=r[k]}); if(o.is_active!==undefined){const v=String(o.is_active).toLowerCase(); o.is_active=(v==='0'||v==='nonaktif'||v==='tidak'||v==='no')?0:1} return o}).filter(o=>String(o.name||'').trim()!=='');
        setProg(70,allRows.length+' baris siap diimpor dari '+f.name);
        sub.disabled=!allRows.length;
      }catch(err){ setProg(0,'Gagal baca file: '+err.message); }
    };
    rd.onerror=()=>setProg(0,'Gagal membaca file.');
    rd.readAsArrayBuffer(f);
  });
  document.getElementById('importForm')?.addEventListener('submit',async e=>{
    e.preventDefault();
    if(!allRows.length){ Swal.fire('Kosong','Pilih file Excel dulu.','warning'); return; }
    const CH=100; let done=0,added=0,skipped=0;
    sub.disabled=true;
    for(let i=0;i<allRows.length;i+=CH){
      const chunk=allRows.slice(i,i+CH);
      setProg(70+Math.round(done/allRows.length*28),'Mengimpor '+(done+1)+' - '+Math.min(done+chunk.length,allRows.length)+' dari '+allRows.length+'...');
      try{
        const res=await fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'csrf='+encodeURIComponent(CSRF)+'&act=import_chunk&rows_json='+encodeURIComponent(JSON.stringify(chunk))});
        const j=await res.json();
        if(!j.ok)throw new Error(j.msg||'Impor gagal');
        added+=j.added||0; skipped+=j.skipped||0; done+=chunk.length;
      }catch(err){ Swal.fire('Gagal',err.message,'error'); sub.disabled=false; return; }
    }
    setProg(100,'Selesai: '+added+' diimpor, '+skipped+' dilewati.');
    await Swal.fire({icon:'success',title:'Impor selesai',text:added+' guru diimpor.'+(skipped?' '+skipped+' dilewati.':''),timer:1800,showConfirmButton:false});
    location.reload();
  });
})();
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>



