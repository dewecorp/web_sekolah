<?php declare(strict_types=1); Auth::requireRole(['administrator']); $title='User';
if($_SERVER['REQUEST_METHOD']==='POST'){ if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/users')); exit; }
$act=$_POST['act']??'save';
if($act==='delete'){ $db->prepare("UPDATE users SET deleted_at=NOW() WHERE id=? AND id<>?")->execute([(int)$_POST['id'],$_SESSION['user']['id']]); Session::flash('ok','User dinonaktifkan.'); }
else{ $nm=trim($_POST['name']??''); $un=trim($_POST['username']??''); $em=trim($_POST['email']??''); $rl=(int)($_POST['role_id']??3);
if($nm===''||$un===''||!Validator::email($em)){ Session::flash('err','Data tidak valid.'); } else {
$avatar=$_POST['old_avatar']??null;
if(!empty($_POST['clear_avatar'])){ if($avatar)@unlink(ROOT.'/assets/uploads/'.basename($avatar)); $avatar=null; }
if(!empty($_FILES['avatar']['name']??'')){ $e=Security::validImage($_FILES['avatar'],$APP); if($e){ Session::flash('err',$e); header('Location: '.Helper::url('admin/users')); exit; } $n=Security::safeName($_FILES['avatar']['name']); move_uploaded_file($_FILES['avatar']['tmp_name'],ROOT.'/assets/uploads/'.$n); if($avatar)@unlink(ROOT.'/assets/uploads/'.basename($avatar)); $avatar=$n; }
if(!empty($_POST['id'])){ $id=(int)$_POST['id']; if(!empty($_POST['password'])) $db->prepare("UPDATE users SET name=?,username=?,email=?,role_id=?,avatar=?,password=? WHERE id=?")->execute([$nm,$un,$em,$rl,$avatar,password_hash($_POST['password'],PASSWORD_DEFAULT),$id]); else $db->prepare("UPDATE users SET name=?,username=?,email=?,role_id=?,avatar=? WHERE id=?")->execute([$nm,$un,$em,$rl,$avatar,$id]); if(($_SESSION['user']['id']??0)==$id){ $_SESSION['user']['name']=$nm; $_SESSION['user']['username']=$un; $_SESSION['user']['avatar']=$avatar; } }
else{ $db->prepare("INSERT INTO users(role_id,name,username,email,password,avatar,is_active) VALUES(?,?,?,?,?,?,1)")->execute([$rl,$nm,$un,$em,password_hash($_POST['password']??'123456',PASSWORD_DEFAULT),$avatar]); }
Auth::log($db,'save','users',"Simpan $un"); Session::flash('ok','Disimpan.'); } }
header('Location: '.Helper::url('admin/users')); exit; }
$roles=$db->query("SELECT * FROM roles")->fetchAll();
$rows=$db->query("SELECT u.*,r.name role FROM users u JOIN roles r ON r.id=u.role_id WHERE u.deleted_at IS NULL ORDER BY u.id DESC")->fetchAll();
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-users text-emerald-600 mr-1"></i>User & Role</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($rows) ?> user</span>
<button id="btnAdd" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-plus mr-1"></i>Tambah User</button>
</div>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[560px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-10">No</th><th class="p-3">Nama</th><th class="p-3">Foto</th><th class="p-3">Role</th><th class="p-3 text-right">Aksi</th></tr>
<?php if(!$rows): ?><tr><td colspan="5" class="p-10 text-center text-slate-500"><i class="fa fa-users text-3xl block mb-2"></i>Belum ada user. Klik Tambah User.</td></tr><?php endif; ?>
<?php $no=1; foreach($rows as $r): $initial=mb_strtoupper(mb_substr(trim($r['name']??'?'),0,1)); ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3 text-slate-500"><?= $no++ ?></td>
<td class="p-3 font-semibold"><?= Helper::e($r['name']) ?><span class="block text-xs font-normal text-slate-500"><?= Helper::e($r['username']) ?> • <?= Helper::e($r['email']) ?></span></td>
<td class="p-3"><?php if(!empty($r['avatar'])): ?><img src="<?= Helper::upload($r['avatar']) ?>" alt="" class="w-10 h-10 rounded-full object-cover border"><?php else: ?><span class="w-10 h-10 rounded-full bg-emerald-600 text-white grid place-items-center font-extrabold"><?= Helper::e($initial) ?></span><?php endif; ?></td>
<td class="p-3"><?= Helper::e($r['role']) ?></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<button class="btn-edit w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-emerald-600" title="Edit" data-row='<?= htmlspecialchars(json_encode(['id'=>$r['id'],'name'=>$r['name'],'username'=>$r['username'],'email'=>$r['email'],'role_id'=>(int)$r['role_id'],'avatar'=>$r['avatar']??'']),ENT_QUOTES) ?>'><i class="fa fa-pen text-xs"></i></button>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Nonaktifkan"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>
<p class="text-xs text-slate-500 mt-2">Administrator: penuh. Editor: konten. Author: buat konten sendiri.</p>

<div id="userModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
<div class="fixed inset-0 bg-slate-900/60" data-close></div>
<div class="relative min-h-full flex items-start justify-center p-3 sm:p-6">
<div class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl my-4 overflow-hidden">
<div class="flex items-center gap-2 px-4 py-3 border-b bg-white"><h2 class="font-extrabold text-sm" id="modalTitle"><i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah User</h2><button data-close class="ml-auto w-8 h-8 rounded-lg border grid place-items-center hover:bg-slate-100"><i class="fa fa-xmark"></i></button></div>
<form method="post" enctype="multipart/form-data" data-loading class="p-4 grid gap-2.5 text-sm bg-white md:grid-cols-2"><?= Security::csrfField() ?>
<input type="hidden" name="id" id="f_id" value="0"><input type="hidden" name="old_avatar" id="f_old" value="">
<label class="grid gap-1 font-semibold">Nama<input name="name" id="f_name" required placeholder="Nama" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Username<input name="username" id="f_username" required placeholder="Username" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Email<input name="email" id="f_email" required type="email" placeholder="Email" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Password <span class="font-normal text-slate-400 text-xs" id="pwHint">wajib saat tambah</span><input name="password" id="f_password" type="password" required placeholder="Password" class="border rounded-lg p-2 font-normal"></label>
<label class="grid gap-1 font-semibold">Role<select name="role_id" id="f_role" class="border rounded-lg p-2 font-normal"><?php foreach($roles as $ro): ?><option value="<?= $ro['id'] ?>"><?= Helper::e($ro['name']) ?></option><?php endforeach; ?></select></label>
<div class="border rounded-xl p-2.5 bg-slate-50"><p class="text-xs font-bold mb-1.5"><i class="fa fa-image mr-1 text-emerald-600"></i>Foto (kosong = initial)</p>
<div class="flex items-center gap-2">
<span id="f_prev"><span class="w-10 h-10 rounded-full bg-emerald-600 text-white grid place-items-center font-extrabold">?</span></span>
<div class="grid gap-1 flex-1 min-w-0"><input type="file" name="avatar" id="f_avatar" accept="image/*" class="border rounded-lg p-1.5 w-full bg-white text-xs">
<label class="text-xs flex gap-1.5 items-center" id="wrapClear" style="display:none"><input type="checkbox" name="clear_avatar" value="1"> Hapus foto</label></div>
</div></div>
<div class="flex justify-center md:col-span-2"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button><button type="button" data-close class="ml-2 border rounded-xl px-5">Batal</button></div>
</form></div></div></div>

<script>
const modal=document.getElementById('userModal');
const pw=document.getElementById('f_password');
const pwHint=document.getElementById('pwHint');
const upBase='<?= Helper::url('assets/uploads/') ?>/';
function paintPrev(name,avatar){
  const box=document.getElementById('f_prev');
  const init=(name||'?').trim().charAt(0).toUpperCase()||'?';
  if(avatar){ box.innerHTML='<img src="'+upBase+avatar+'" alt="" class="w-10 h-10 rounded-full object-cover border">'; document.getElementById('wrapClear').style.display=''; }
  else{ box.innerHTML='<span class="w-10 h-10 rounded-full bg-emerald-600 text-white grid place-items-center font-extrabold">'+init+'</span>'; document.getElementById('wrapClear').style.display='none'; }
}
function openModal(d){
  document.getElementById('modalTitle').innerHTML=(d?'<i class="fa fa-pen text-emerald-600 mr-1"></i>Edit User':'<i class="fa fa-plus text-emerald-600 mr-1"></i>Tambah User');
  document.getElementById('f_id').value=d?.id||0;
  document.getElementById('f_name').value=d?.name||'';
  document.getElementById('f_username').value=d?.username||'';
  document.getElementById('f_email').value=d?.email||'';
  document.getElementById('f_role').value=d?.role_id||document.getElementById('f_role').options[0]?.value;
  document.getElementById('f_old').value=d?.avatar||'';
  document.getElementById('f_avatar').value='';
  paintPrev(d?.name||'',d?.avatar||'');
  pw.value='';
  if(d){pw.removeAttribute('required');pwHint.textContent='kosongkan jika tidak diubah'}else{pw.setAttribute('required','');pwHint.textContent='wajib saat tambah'}
  modal.classList.remove('hidden');document.body.style.overflow='hidden';
}
function closeModal(){modal.classList.add('hidden');document.body.style.overflow=''}
document.getElementById('btnAdd').addEventListener('click',()=>openModal(null));
document.getElementById('f_name').addEventListener('input',e=>{ if(!document.getElementById('f_old').value) paintPrev(e.target.value,''); });
document.getElementById('f_avatar').addEventListener('change',e=>{ const f=e.target.files[0]; if(!f)return; const box=document.getElementById('f_prev'); box.innerHTML='<img src="'+URL.createObjectURL(f)+'" alt="" class="w-12 h-12 rounded-full object-cover border">'; document.getElementById('wrapClear').style.display='none'; });
document.querySelectorAll('.btn-edit').forEach(b=>b.addEventListener('click',()=>openModal(JSON.parse(b.dataset.row))));
modal.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',closeModal));
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal()});
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>
