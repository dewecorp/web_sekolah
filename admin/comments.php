<?php
declare(strict_types=1);
if (!isset($db) || !$db instanceof PDO) $db = Database::conn();
Auth::requireRole(['administrator','editor']);
$title='Komentar';
try { $db->exec("CREATE TABLE IF NOT EXISTS post_comments (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,post_id INT UNSIGNED NOT NULL,parent_id INT UNSIGNED DEFAULT NULL,name VARCHAR(100) NOT NULL,email VARCHAR(190) NOT NULL,website VARCHAR(255) DEFAULT NULL,comment TEXT NOT NULL,status ENUM('pending','approved','spam','trash') NOT NULL DEFAULT 'pending',note VARCHAR(255) DEFAULT NULL,ip VARCHAR(45) DEFAULT NULL,user_agent VARCHAR(255) DEFAULT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,INDEX idx_post_status (post_id, status, created_at),INDEX idx_status (status)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"); } catch (Throwable) {}
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/comments')); exit; }
  $act=$_POST['act']??'';
  $ids=array_values(array_unique(array_filter(array_map('intval',(array)($_POST['ids']??(isset($_POST['id'])?[$_POST['id']]:[]))))));
  if($ids){
    $in=implode(',',array_fill(0,count($ids),'?'));
    if($act==='approve'){ $db->prepare("UPDATE post_comments SET status='approved',note=NULL WHERE id IN ($in)")->execute($ids); Session::flash('ok',count($ids).' komentar disetujui.'); }
    elseif($act==='spam'){ $db->prepare("UPDATE post_comments SET status='spam' WHERE id IN ($in)")->execute($ids); Session::flash('ok',count($ids).' komentar ditandai spam.'); }
    elseif($act==='trash'){ $db->prepare("UPDATE post_comments SET status='trash' WHERE id IN ($in)")->execute($ids); Session::flash('ok',count($ids).' komentar dipindah ke sampah.'); }
    elseif($act==='unspam'){ $db->prepare("UPDATE post_comments SET status='pending',note=NULL WHERE id IN ($in)")->execute($ids); Session::flash('ok',count($ids).' komentar dikembalikan ke antrean.'); }
    elseif($act==='restore'){ $db->prepare("UPDATE post_comments SET status='pending',note=NULL WHERE id IN ($in)")->execute($ids); Session::flash('ok',count($ids).' komentar dipulihkan.'); }
    elseif($act==='delete'){ $db->prepare("DELETE FROM post_comments WHERE id IN ($in)")->execute($ids); Session::flash('ok',count($ids).' komentar dihapus permanen.'); }
    elseif($act==='empty_trash'){ $db->prepare("DELETE FROM post_comments WHERE status IN ('trash','spam')")->execute(); Session::flash('ok','Sampah & spam dikosongkan.'); }
    Auth::log($db,$act,'comments','Aksi '.$act.' ('.count($ids).')');
  }
  header('Location: '.Helper::url('admin/comments')); exit;
}
$gf=$_GET['f']??'all'; $f=in_array($gf,['all','pending','approved','spam','trash'],true)?$gf:'all';
$q=trim($_GET['q']??'');
$w=[]; $p=[];
if($f!=='all'){ $w[]="c.status=?"; $p[]=$f; }
if($q!==''){ $w[]="(c.name LIKE ? OR c.email LIKE ? OR c.comment LIKE ? OR p.title LIKE ?)"; array_push($p,"%$q%","%$q%","%$q%","%$q%"); }
$sql="SELECT c.*,p.title post_title,p.slug post_slug FROM post_comments c LEFT JOIN posts p ON p.id=c.post_id".($w?' WHERE '.implode(' AND ',$w):'')." ORDER BY c.created_at DESC LIMIT 200";
$st=$db->prepare($sql); $st->execute($p); $rows=$st->fetchAll();
$cnt=[]; try { foreach($db->query("SELECT status,COUNT(*) n FROM post_comments GROUP BY status") as $r)$cnt[$r['status']]=(int)$r['n']; } catch (Throwable) {}
$total=array_sum($cnt);
$menus=['all'=>['Semua','fa-list'],'pending'=>['Menunggu','fa-clock'],'approved'=>['Disetujui','fa-check'],'spam'=>['Spam','fa-ban'],'trash'=>['Sampah','fa-trash']];
$tabs=''; foreach($menus as $k=>$v){ $n=$k==='all'?$total:($cnt[$k]??0); $tabs.='<a href="?f='.$k.'" class="text-xs font-bold px-3 py-2 rounded-xl border '.($f===$k?'bg-slate-800 text-white':'bg-white').'"><i class="fa '.$v[1].' mr-1"></i>'.$v[0].' ('.$n.')</a>'; }
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-comments text-emerald-600 mr-1"></i>Komentar</h1>
<form class="ml-2 hidden sm:flex gap-1" method="get" action="<?= Helper::url('admin/comments') ?>"><input type="hidden" name="f" value="<?= Helper::e($f) ?>"><input name="q" id="cq" value="<?= Helper::e($q) ?>" placeholder="Cari isi/pengirim/berita..." class="border rounded-lg px-3 py-1.5 text-sm w-64"><button class="bg-slate-800 text-white px-3 rounded-lg text-sm"><i class="fa fa-search"></i></button></form>
</div>
<div class="flex flex-wrap gap-1.5 mb-3"><?= $tabs ?></div>
<form method="post" id="bulkForm"><?= Security::csrfField() ?><input type="hidden" name="act" id="bulkAct" value="approve"></form>
<div class="mb-3 flex flex-wrap items-center gap-1.5 text-xs">
<label class="flex gap-1.5 items-center text-xs font-bold"><input type="checkbox" id="checkAll"> Pilih semua</label>
<span id="selCount" class="text-xs text-slate-400">0 dipilih</span>
<span class="ml-auto flex flex-wrap gap-1.5">
<button type="button" class="bulkBtn px-3 py-2 rounded-xl border bg-white font-bold hover:border-emerald-400" data-act="approve"><i class="fa fa-check text-emerald-600 mr-1"></i>Izinkan</button>
<button type="button" class="bulkBtn px-3 py-2 rounded-xl border bg-white font-bold hover:border-amber-400" data-act="spam"><i class="fa fa-ban text-amber-600 mr-1"></i>Spam</button>
<button type="button" class="bulkBtn px-3 py-2 rounded-xl border bg-white font-bold hover:border-red-400" data-act="trash"><i class="fa fa-trash text-red-600 mr-1"></i>Sampah</button>
<button type="button" class="bulkBtn px-3 py-2 rounded-xl border bg-white font-bold hover:border-emerald-400" data-act="restore"><i class="fa fa-rotate-left text-emerald-600 mr-1"></i>Pulihkan</button>
<button type="button" class="bulkBtn px-3 py-2 rounded-xl border bg-white font-bold text-red-600 hover:border-red-400" data-act="delete"><i class="fa fa-trash mr-1"></i>Hapus</button>
</span>
</div>
<div class="bg-white rounded-2xl border overflow-hidden">
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[820px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-10">No</th><th class="p-3">Isi Komentar</th><th class="p-3 w-44">Pengirim</th><th class="p-3 w-28">Tanggal</th><th class="p-3 text-right w-44">Aksi</th></tr>
<?php if(!$rows): ?><tr><td colspan="5" class="p-10 text-center text-slate-500"><i class="fa fa-comments text-3xl block mb-2"></i>Belum ada komentar.</td></tr><?php endif; ?>
<?php $no=1; $stBadge=['pending'=>'bg-amber-100 text-amber-700','approved'=>'bg-emerald-100 text-emerald-700','spam'=>'bg-red-100 text-red-700','trash'=>'bg-slate-200 text-slate-600']; foreach($rows as $r): ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3 text-slate-500"><input type="checkbox" form="bulkForm" name="ids[]" value="<?= (int)$r['id'] ?>" class="rowcheck"> <?= $no++ ?></td>
<td class="p-3"><span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full <?= $stBadge[$r['status']]??'bg-slate-100' ?>"><?= Helper::e($r['status']) ?></span>
<p class="mt-1 text-sm"><?= Helper::e(Helper::excerpt($r['comment']??'',180)) ?></p>
<p class="text-xs text-slate-400 mt-1">pada <a href="<?= Helper::url('berita/'.$r['post_slug']) ?>" target="_blank" class="text-emerald-700 hover:underline font-bold"><?= Helper::e(mb_strimwidth($r['post_title']??'(berita dihapus)',0,60,'...')) ?></a><?php if(!empty($r['note'])): ?> • <?= Helper::e($r['note']) ?><?php endif; ?></p>
<button type="button" class="c-view text-emerald-700 text-xs font-bold mt-1 hover:underline" data-c='<?= htmlspecialchars(json_encode($r,JSON_UNESCAPED_UNICODE),ENT_QUOTES) ?>'><i class="fa fa-eye mr-1"></i>Lihat penuh</button></td>
<td class="p-3 text-xs"><b class="block"><?= Helper::e($r['name']) ?></b><a href="mailto:<?= Helper::e($r['email']) ?>" class="text-emerald-700 hover:underline break-all"><?= Helper::e($r['email']) ?></a><?php if(!empty($r['website'])): ?><a href="<?= Helper::e($r['website']) ?>" target="_blank" class="block text-slate-400 hover:underline truncate"><?= Helper::e($r['website']) ?></a><?php endif; ?><span class="block text-slate-400 mt-0.5 font-mono"><?= Helper::e($r['ip']??'') ?></span></td>
<td class="p-3 text-xs text-slate-500 whitespace-nowrap"><?= Helper::e(Helper::tgl($r['created_at']??'now').' '.substr((string)($r['created_at']??''),11,5)) ?></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<?php if($r['status']!=='approved'): ?><form method="post"><input type="hidden" name="csrf" value="<?= Security::csrfToken() ?>"><input type="hidden" name="act" value="approve"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-emerald-600 hover:border-emerald-300" title="Izinkan"><i class="fa fa-check text-xs"></i></button></form><?php endif; ?>
<?php if($r['status']!=='spam'): ?><form method="post"><input type="hidden" name="csrf" value="<?= Security::csrfToken() ?>"><input type="hidden" name="act" value="spam"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-amber-600 hover:border-amber-300" title="Tolak / Spam"><i class="fa fa-ban text-xs"></i></button></form>
<?php else: ?><form method="post"><input type="hidden" name="csrf" value="<?= Security::csrfToken() ?>"><input type="hidden" name="act" value="unspam"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-emerald-600 hover:border-emerald-300" title="Bukan spam"><i class="fa fa-rotate-left text-xs"></i></button></form><?php endif; ?>
<?php if($r['status']!=='trash'): ?><form method="post" data-confirm><input type="hidden" name="csrf" value="<?= Security::csrfToken() ?>"><input type="hidden" name="act" value="trash"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-slate-500 hover:border-slate-300" title="Sampah"><i class="fa fa-trash text-xs"></i></button></form><?php endif; ?>
<form method="post" data-confirm><input type="hidden" name="csrf" value="<?= Security::csrfToken() ?>"><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600 hover:border-red-300" title="Hapus permanen"><i class="fa fa-trash-can text-xs"></i></button></form>
</span></td></tr><?php endforeach; ?></table></div></div>
<?php if($f==='trash'||$f==='spam'): ?><form method="post" data-confirm class="mt-3"><?= Security::csrfField() ?><input type="hidden" name="act" value="empty_trash"><button class="text-xs font-bold px-3 py-2 rounded-xl border bg-white text-red-600 hover:border-red-400"><i class="fa fa-trash-can mr-1"></i>Kosongkan sampah & spam</button></form><?php endif; ?>
<script>
document.querySelectorAll('.c-view').forEach(b=>b.addEventListener('click',()=>{
  const d=JSON.parse(b.dataset.c);
  const esc=s=>String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  Swal.fire({title:'<span class="text-base font-extrabold">Detail Komentar</span>',
    html:'<div class="text-left"><div class="flex items-center gap-3 rounded-2xl bg-slate-50 border p-3"><span class="w-11 h-11 rounded-full bg-emerald-600 text-white grid place-items-center font-extrabold text-lg shrink-0">'+esc((d.name||'?').trim().charAt(0).toUpperCase())+'</span><span class="min-w-0 flex-1"><b class="block truncate">'+esc(d.name)+'</b><span class="block text-xs text-slate-500 truncate">'+esc(d.email)+'</span><span class="block text-xs text-slate-400">'+esc(d.created_at||'')+'</span></span></div><div class="mt-3 rounded-2xl border bg-white p-3 text-sm leading-relaxed whitespace-pre-wrap max-h-64 overflow-y-auto">'+esc(d.comment)+'</div>'+(d.website?'<div class="mt-2 text-xs">Situs: <span class="text-emerald-700">'+esc(d.website)+'</span></div>':'')+(d.ip?'<div class="mt-1 text-xs text-slate-400 font-mono">IP: '+esc(d.ip)+'</div>':'')+'</div>',
    showConfirmButton:false,showCloseButton:true,width:600});
}));
(function(){
  const all=document.getElementById('checkAll'),rows=[...document.querySelectorAll('.rowcheck')],cnt=document.getElementById('selCount'),form=document.getElementById('bulkForm'),act=document.getElementById('bulkAct');
  const upd=()=>{const n=rows.filter(r=>r.checked).length;if(cnt)cnt.textContent=n+' dipilih';if(all)all.checked=rows.length>0&&n===rows.length};
  all?.addEventListener('change',()=>{rows.forEach(r=>r.checked=all.checked);upd()});
  rows.forEach(r=>r.addEventListener('change',upd));upd();
  document.querySelectorAll('.bulkBtn').forEach(b=>b.addEventListener('click',()=>{
    const n=rows.filter(r=>r.checked).length;
    if(!n){Swal.fire('Pilih dulu','Centang minimal 1 komentar.','warning');return}
    act.value=b.dataset.act; form.submit();
  }));
  const q=document.getElementById('cq'); let t=null;
  q?.addEventListener('input',()=>{clearTimeout(t);t=setTimeout(()=>q.form.submit(),500)});
})();
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>
