<?php
declare(strict_types=1);
$title='Kotak Masuk';
try{$db->exec("CREATE TABLE IF NOT EXISTS contact_messages(id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(150) NOT NULL,email VARCHAR(190) NOT NULL,subject VARCHAR(190) NOT NULL DEFAULT '',message MEDIUMTEXT NOT NULL,is_read TINYINT(1) NOT NULL DEFAULT 0,created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,KEY idx_read_created (is_read,created_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");}catch(Throwable){}
try{$db->exec("ALTER TABLE contact_messages ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0 AFTER message");}catch(Throwable){}
try{$db->exec("ALTER TABLE contact_messages ADD COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER is_read");}catch(Throwable){}
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/inbox')); exit; }
  $act=$_POST['act']??'';
  if($act==='read'){ $db->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([(int)$_POST['id']]); Session::flash('ok','Pesan ditandai dibaca.'); }
  elseif($act==='unread'){ $db->prepare("UPDATE contact_messages SET is_read=0 WHERE id=?")->execute([(int)$_POST['id']]); Session::flash('ok','Pesan ditandai belum dibaca.'); }
  elseif($act==='delete'){ $db->prepare("DELETE FROM contact_messages WHERE id=?")->execute([(int)$_POST['id']]); Session::flash('ok','Pesan dihapus.'); }
  elseif($act==='read_all'){ $db->exec("UPDATE contact_messages SET is_read=1 WHERE is_read=0"); Session::flash('ok','Semua pesan ditandai dibaca.'); }
  header('Location: '.Helper::url('admin/inbox')); exit;
}
$hl=(int)($_GET['hl']??0);
if($hl>0){ try{ $db->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([$hl]); }catch(Throwable){} }
$q=trim($_GET['q']??''); $f=($_GET['f']??'all')==='unread'?'unread':'all';
$where=[]; $p=[];
if($f==='unread'){ $where[]="is_read=0"; }
if($q!==''){ $where[]="(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)"; array_push($p,"%$q%","%$q%","%$q%","%$q%"); }
$sql="SELECT * FROM contact_messages".($where?' WHERE '.implode(' AND ',$where):'')." ORDER BY is_read ASC, id DESC";
$st=$db->prepare($sql); $st->execute($p); $rows=$st->fetchAll();
try{ $unread=(int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn(); }catch(Throwable){ $unread=0; }
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-inbox text-emerald-600 mr-1"></i>Kotak Masuk</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= count($rows) ?> pesan</span>
<?php if($unread>0): ?><span class="text-[11px] bg-emerald-600 text-white px-2.5 py-0.5 rounded-full font-bold"><?= $unread ?> belum dibaca</span><?php endif; ?>
<form class="ml-2 hidden sm:flex gap-1" method="get" action="<?= Helper::url('admin/inbox') ?>"><input type="hidden" name="f" value="<?= Helper::e($f) ?>"><input name="q" value="<?= Helper::e($q) ?>" placeholder="Cari nama/email/subjek..." class="border rounded-lg px-3 py-1.5 text-sm w-56"><button class="bg-slate-800 text-white px-3 rounded-lg text-sm"><i class="fa fa-search"></i></button></form>
<div class="ml-auto flex gap-1.5">
<a href="?f=all" class="text-xs font-bold px-3 py-2 rounded-xl border <?= $f==='all'?'bg-slate-800 text-white':'bg-white' ?>">Semua</a>
<a href="?f=unread" class="text-xs font-bold px-3 py-2 rounded-xl border <?= $f==='unread'?'bg-slate-800 text-white':'bg-white' ?>">Belum dibaca</a>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="read_all"><button class="text-xs font-bold px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white"><i class="fa fa-check-double mr-1"></i>Semua sudah dibaca</button></form>
</div>
</div>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[760px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-10">No</th><th class="p-3">Nama</th><th class="p-3">Email</th><th class="p-3">Subjek</th><th class="p-3">Isi Pesan</th><th class="p-3 w-28">Waktu</th><th class="p-3 text-right w-32">Aksi</th></tr>
<?php if(!$rows): ?><tr><td colspan="7" class="p-10 text-center text-slate-500"><i class="fa fa-inbox text-3xl block mb-2"></i>Belum ada pesan masuk.</td></tr><?php endif; ?>
<?php $no=1; foreach($rows as $r): $isNew=!(int)$r['is_read']; $isHl=$hl>0&&(int)$r['id']===$hl; ?>
<tr id="msg-<?= (int)$r['id'] ?>" class="border-t hover:bg-slate-50 <?= $isNew?'bg-emerald-50/40':'' ?> <?= $isHl?'ring-2 ring-emerald-400':'' ?>">
<td class="p-3 text-slate-500"><?= $no++ ?></td>
<td class="p-3 <?= $isNew?'font-extrabold':'font-semibold' ?>"><?= Helper::e($r['name']) ?><?php if($isNew): ?><span class="ml-1.5 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-600 text-white">Baru</span><?php endif; ?></td>
<td class="p-3 text-xs"><a href="mailto:<?= Helper::e($r['email']) ?>" class="text-emerald-700 hover:underline break-all <?= $isNew?'font-bold':'' ?>"><?= Helper::e($r['email']) ?></a></td>
<td class="p-3 <?= $isNew?'font-bold':'' ?>"><?= Helper::e($r['subject']!==''?$r['subject']:'(Tanpa subjek)') ?></td>
<td class="p-3 text-xs text-slate-600 max-w-[280px]"><span class="block line-clamp-3"><?= Helper::e(Helper::excerpt($r['message']??'',160)) ?></span><button type="button" class="msg-view text-emerald-700 font-bold mt-1 hover:underline" data-msg='<?= htmlspecialchars(json_encode(['id'=>$r['id'],'name'=>$r['name'],'email'=>$r['email'],'subject'=>$r['subject'],'message'=>$r['message'],'created'=>$r['created_at'],'is_read'=>(int)$r['is_read']]),ENT_QUOTES) ?>'><i class="fa fa-eye mr-1"></i>Lihat penuh</button></td>
<td class="p-3 text-xs text-slate-500 whitespace-nowrap"><?= Helper::e(Helper::tgl($r['created_at']??'now').' '.substr((string)($r['created_at']??''),11,5)) ?></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<?php if($isNew): ?><form method="post"><input type="hidden" name="csrf" value="<?= Security::csrfToken() ?>"><input type="hidden" name="act" value="read"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-emerald-600 transition hover:-translate-y-0.5 hover:shadow hover:border-emerald-300" title="Tandai dibaca"><i class="fa fa-envelope-open-text text-xs"></i></button></form>
<?php else: ?><form method="post"><input type="hidden" name="csrf" value="<?= Security::csrfToken() ?>"><input type="hidden" name="act" value="unread"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-slate-500 transition hover:-translate-y-0.5 hover:shadow hover:border-slate-300" title="Tandai belum dibaca"><i class="fa fa-envelope text-xs"></i></button></form><?php endif; ?>
<form method="post" data-confirm><input type="hidden" name="csrf" value="<?= Security::csrfToken() ?>"><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600 transition hover:-translate-y-0.5 hover:shadow hover:border-red-300" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>
<script>
document.querySelectorAll('.msg-view').forEach(b=>b.addEventListener('click',()=>{
  const d=JSON.parse(b.dataset.msg);
  const esc=s=>String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  const dt=esc(d.created||''); const subj=esc(d.subject||'(Tanpa subjek)');
  const chars=String(d.message||'').length; const words=String(d.message||'').trim()===''?0:String(d.message||'').trim().split(/\s+/).length;
  Swal.fire({
    title:'<span class="text-base font-extrabold">Detail Pesan</span>',
    html:'<div class="text-left">'
      +'<div class="flex items-center gap-3 rounded-2xl bg-slate-50 border p-3"><span class="w-11 h-11 rounded-full bg-emerald-600 text-white grid place-items-center font-extrabold text-lg shrink-0">'+esc((d.name||'?').trim().charAt(0).toUpperCase())+'</span><span class="min-w-0 flex-1"><b class="block truncate">'+esc(d.name)+'</b><span class="block text-xs text-slate-500 truncate">'+esc(d.email)+'</span><span class="mt-1 inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full '+(d.is_read?'bg-slate-200 text-slate-600':'bg-emerald-600 text-white')+'">'+(d.is_read?'Sudah dibaca':'Belum dibaca')+'</span></span></div>'
      +'<div class="mt-3 grid gap-1.5 text-sm"><div class="flex gap-2"><span class="w-16 shrink-0 text-slate-400 text-xs font-bold pt-0.5">Subjek</span><b class="flex-1">'+subj+'</b></div>'
      +'<div class="flex gap-2"><span class="w-16 shrink-0 text-slate-400 text-xs font-bold pt-0.5">Waktu</span><span class="flex-1 text-slate-600">'+dt+'</span></div>'
      +'<div class="flex gap-2"><span class="w-16 shrink-0 text-slate-400 text-xs font-bold pt-0.5">Info</span><span class="flex-1 text-xs text-slate-500">'+words+' kata • '+chars+' karakter</span></div></div>'
      +'<div class="mt-3 rounded-2xl border bg-white p-3 text-sm leading-relaxed whitespace-pre-wrap max-h-64 overflow-y-auto">'+esc(d.message)+'</div>'
      +'<div class="mt-3 flex flex-wrap gap-2"><button id="swCopyMsg" type="button" class="flex-1 text-xs font-bold px-3 py-2 rounded-xl border hover:bg-slate-50"><i class="fa fa-copy mr-1"></i>Salin Pesan</button></div>'
      +'</div>',
    showConfirmButton:false, showCloseButton:true, width:600,
    didOpen:()=>{ document.getElementById('swCopyMsg')?.addEventListener('click',()=>{ navigator.clipboard?.writeText(d.message||''); Swal.showValidationMessage('Pesan disalin!'); setTimeout(()=>Swal.resetValidationMessage(),1200); }); }
  });
}));
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>
