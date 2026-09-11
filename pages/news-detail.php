<?php
$st=$db->prepare("SELECT p.*,c.name cat,u.name author FROM posts p LEFT JOIN categories c ON c.id=p.category_id LEFT JOIN users u ON u.id=p.author_id WHERE p.slug=? AND p.status='published' LIMIT 1");
$st->execute([$slug??'']); $p=$st->fetch();
if(!$p){ http_response_code(404); require ROOT.'/templates/error/404.php'; exit; }
$db->prepare("UPDATE posts SET views=views+1 WHERE id=?")->execute([$p['id']]);
$metaTitle=$p['title']; $metaDesc=Helper::excerpt($p['excerpt']?:$p['content']);
$schemaNews=['@context'=>'https://schema.org','@type'=>'NewsArticle','headline'=>$p['title'],'description'=>$metaDesc,'image'=>[Helper::cover($p['featured_image']??'','berita-'.$p['slug'],1200,630)],'datePublished'=>$p['published_at']??$p['created_at'],'dateModified'=>$p['updated_at']??$p['published_at']??$p['created_at'],'author'=>[['@type'=>'Person','name'=>$p['author']??Database::setting('school_name','Redaksi')]],'publisher'=>['@type'=>'Organization','name'=>Database::setting('school_name','Sekolah'),'logo'=>['@type'=>'ImageObject','url'=>Helper::upload(Database::setting('logo',''))]],'mainEntityOfPage'=>['@type'=>'WebPage','@id'=>Helper::url('berita/'.$p['slug'])]];
try { $db->exec("CREATE TABLE IF NOT EXISTS post_comments (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,post_id INT UNSIGNED NOT NULL,parent_id INT UNSIGNED DEFAULT NULL,name VARCHAR(100) NOT NULL,email VARCHAR(190) NOT NULL,website VARCHAR(255) DEFAULT NULL,comment TEXT NOT NULL,status ENUM('pending','approved','spam','trash') NOT NULL DEFAULT 'pending',note VARCHAR(255) DEFAULT NULL,ip VARCHAR(45) DEFAULT NULL,user_agent VARCHAR(255) DEFAULT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,INDEX idx_post_status (post_id, status, created_at),INDEX idx_status (status)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"); } catch (Throwable) {}
$cOk=''; $cErr='';
$replyTo=null;
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['comment_send']??'')==='1'){
  $ip=$_SERVER['REMOTE_ADDR']??'127.0.0.1';
  $nm=trim((string)($_POST['cname']??'')); $em=trim((string)($_POST['cemail']??'')); $ws=trim((string)($_POST['cwebsite']??'')); $tx=trim((string)($_POST['ctext']??''));
  $hp=trim((string)($_POST['company']??''));
  $started=(int)($_POST['cstarted']??0);
  $parentId=(int)($_POST['parent_id']??0);
  if($parentId>0){ try{ $ps=$db->prepare("SELECT id,name FROM post_comments WHERE id=? AND post_id=? AND status='approved' LIMIT 1"); $ps->execute([$parentId,(int)$p['id']]); $replyTo=$ps->fetch()?:null; if(!$replyTo)$parentId=0; }catch(Throwable){ $parentId=0; } }
  if(!Security::verifyCsrf($_POST['csrf']??null)){ $cErr='Sesi kedaluwarsa. Muat ulang halaman.'; }
  elseif($hp!==''){ $cErr='Komentar ditolak.'; }
  elseif(mb_strlen($nm)<2||mb_strlen($nm)>100){ $cErr='Nama minimal 2 huruf.'; }
  elseif(!filter_var($em,FILTER_VALIDATE_EMAIL)||mb_strlen($em)>190){ $cErr='Email tidak valid.'; }
  elseif($ws!==''&&(mb_strlen($ws)>255||!preg_match('~^(https?://|www\.)~i',$ws))){ $cErr='Situs web tidak valid.'; }
  elseif(mb_strlen($tx)<3||mb_strlen($tx)>2000){ $cErr='Komentar 3-2000 karakter.'; }
  elseif($started>0&&(time()-$started<Security::COMMENT_MIN_SECONDS)){ $cErr='Terlalu cepat. Tulis komentar dengan wajar.'; }
  else{
    [$okRate,$rateMsg]=Security::commentRateAllowed($db,$ip);
    if(!$okRate){ $cErr=$rateMsg; }
    elseif(Security::isDuplicateComment($db,(int)$p['id'],$em,$tx)){ $cErr='Komentar duplikat terdeteksi.'; }
    else{
      [$spam,$spamMsg]=Security::isCommentSpam($tx,$nm,$em,$ws);
      if($spam){ try{ $db->prepare("INSERT INTO post_comments(post_id,parent_id,name,email,website,comment,status,note,ip,user_agent) VALUES(?,?,?,?,?,?,'spam',?,?,?)")->execute([(int)$p['id'],$parentId>0?$parentId:null,mb_substr($nm,0,100),$em,mb_substr($ws,0,255),mb_substr($tx,0,2000),$spamMsg,$ip,mb_substr($_SERVER['HTTP_USER_AGENT']??'',0,255)]); }catch(Throwable){} $cErr='Komentar ditolak: '.$spamMsg; }
      else{
        try{ $db->prepare("INSERT INTO post_comments(post_id,parent_id,name,email,website,comment,status,ip,user_agent) VALUES(?,?,?,?,?,?,'pending',?,?)")->execute([(int)$p['id'],$parentId>0?$parentId:null,mb_substr($nm,0,100),$em,mb_substr($ws,0,255),mb_substr($tx,0,2000),$ip,mb_substr($_SERVER['HTTP_USER_AGENT']??'',0,255)]); $cOk='Komentar terkirim dan menunggu moderasi. Terima kasih!'; $_POST=[]; }catch(Throwable $e){ $cErr='Gagal menyimpan komentar.'; }
      }
    }
  }
}
try{ $cmAll=$db->prepare("SELECT * FROM post_comments WHERE post_id=? AND status='approved' ORDER BY created_at ASC"); $cmAll->execute([(int)$p['id']]); $comments=$cmAll->fetchAll(); }catch(Throwable){ $comments=[]; }
try{ $cmCount=count($comments); }catch(Throwable){ $cmCount=0; }
$cmKids=[]; $cmTop=[];
foreach($comments as $cm){ $pid=(int)($cm['parent_id']??0); if($pid>0)$cmKids[$pid][]=$cm; else $cmTop[]=$cm; }
$renderComment=function(array $c) use (&$renderComment,$cmKids): string {
  $h='<div class="rounded-2xl border bg-slate-50 dark:bg-slate-900/40 p-4" id="comment-'.(int)$c['id'].'">';
  $h.='<div class="flex items-center gap-2.5"><span class="w-9 h-9 rounded-full bg-emerald-600 text-white grid place-items-center font-extrabold shrink-0">'.Helper::e(mb_strtoupper(mb_substr(trim($c['name']),0,1))).'</span><span class="min-w-0 flex-1"><b class="block text-sm truncate">'.Helper::e($c['name']).'</b><span class="block text-[11px] text-slate-400">'.Helper::tgl($c['created_at']).' • '.Helper::e(substr((string)$c['created_at'],11,5)).'</span></span><button type="button" class="reply-btn text-xs font-bold text-emerald-600 hover:underline shrink-0" data-id="'.(int)$c['id'].'" data-name="'.Helper::e($c['name']).'"><i class="fa fa-reply mr-1"></i>Balas</button></div>';
  $h.='<p class="mt-2 text-sm leading-relaxed whitespace-pre-wrap">'.Helper::e($c['comment']).'</p>';
  if(!empty($cmKids[(int)$c['id']])){
    $h.='<div class="mt-3 grid gap-2 pl-3 sm:pl-4 border-l-2 border-emerald-200">';
    foreach($cmKids[(int)$c['id']] as $kid) $h.=$renderComment($kid);
    $h.='</div>';
  }
  $h.='</div>';
  return $h;
};
require ROOT.'/templates/frontend/header.php'; ?>
<?php $shareUrl=Helper::url('berita/'.$p['slug']);$shareText=$p['title'].' - '.Database::setting('school_name','Sekolah');$shareHeading=Database::setting('share_heading','Bagikan berita ini');$shareDescription=Database::setting('share_description','Sebarkan informasi kepada keluarga dan teman.');
try{ $ls=$db->prepare("SELECT p.id,p.title,p.slug,p.featured_image,p.published_at,p.created_at,p.views,c.name cat FROM posts p LEFT JOIN categories c ON c.id=p.category_id WHERE p.status='published' AND p.deleted_at IS NULL AND p.id<>? ORDER BY p.published_at DESC, p.id DESC LIMIT 5"); $ls->execute([(int)$p['id']]); $latest=$ls->fetchAll(); }catch(Throwable){ $latest=[]; }
try{ $catId=$p['category_id']??null; $related=[]; if($catId){ $rl=$db->prepare("SELECT p.id,p.title,p.slug,p.featured_image,p.published_at,p.created_at,p.views,c.name cat FROM posts p LEFT JOIN categories c ON c.id=p.category_id WHERE p.status='published' AND p.deleted_at IS NULL AND p.id<>? AND p.category_id=? ORDER BY p.published_at DESC, p.id DESC LIMIT 4"); $rl->execute([(int)$p['id'],(int)$catId]); $related=$rl->fetchAll(); } if(count($related)<4){ $ex=array_merge([(int)$p['id']],array_map(fn($x)=>(int)($x['id']??0),$related)); $ph=implode(',',array_fill(0,count($ex),'?')); $rl2=$db->prepare("SELECT p.id,p.title,p.slug,p.featured_image,p.published_at,p.created_at,p.views,c.name cat FROM posts p LEFT JOIN categories c ON c.id=p.category_id WHERE p.status='published' AND p.deleted_at IS NULL AND p.id NOT IN ($ph) ORDER BY p.published_at DESC, p.id DESC LIMIT ".(4-count($related))); $rl2->execute($ex); $related=array_merge($related,$rl2->fetchAll()); } }catch(Throwable){ $related=[]; } ?>
<div class="w-full px-4 md:px-8 py-10">
<?php
$heroBadge='<i class="fa fa-newspaper text-white"></i>'.Helper::e($p['cat']??'Berita');
$heroTitle=$p['title'];
$heroDesc=!empty($p['excerpt'])?nl2br(Helper::e($p['excerpt'])):'';
$heroCrumb='<a href="'.Helper::url().'" class="hover:text-white">Beranda</a> / <a href="'.Helper::url('berita').'" class="hover:text-white">Berita</a> / '.Helper::e($p['cat']??'Berita');
$heroTheme='sky';
$heroStats=[['icon'=>'fa-user','label'=>$p['author']??'Redaksi','solid'=>true],['icon'=>'fa-calendar-day','label'=>Helper::tgl($p['published_at']??$p['created_at']),'solid'=>false],['icon'=>'fa-eye','label'=>(int)$p['views'].' dibaca','solid'=>false]];
require ROOT.'/templates/frontend/page-hero.php'; ?>
<div class="mt-4 grid lg:grid-cols-3 gap-4 items-start">
<article class="lg:col-span-2 bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-2xl p-6 md:p-8 reveal">
<img src="<?= Helper::e(Helper::cover($p['featured_image']??'', 'berita-'.$p['slug'], 1200, 630)) ?>" alt="<?= Helper::e($p['title']) ?>" class="rounded-2xl w-full aspect-[16/9] object-cover" loading="lazy">
<div class="prose max-w-none mt-4 text-slate-700 dark:text-slate-200 text-justify leading-relaxed news-body"><?= $p['content'] ?></div>
<style>.news-body a{color:#059669;font-weight:700;text-decoration:underline;text-decoration-thickness:2px;text-underline-offset:3px}.news-body a::after{content:"\f08e";font-family:"Font Awesome 6 Free";font-weight:900;font-size:.7em;margin-left:.35em;opacity:.7}.news-body a:hover{color:#047857;background:#ecfdf5;border-radius:.3rem}</style>
<section class="mt-8" id="komentar">
<h2 class="font-extrabold text-lg"><i class="fa fa-comments text-emerald-600 mr-1"></i><?= $cmCount ?> Komentar</h2>
<?php if($cmTop): ?><div class="mt-4 grid gap-3"><?php foreach($cmTop as $c) echo $renderComment($c); ?></div><?php else: ?><p class="mt-3 text-sm text-slate-500">Belum ada komentar. Jadilah yang pertama berkomentar.</p><?php endif; ?>
<form method="post" action="#komentar" class="mt-4 rounded-2xl border bg-slate-50 dark:bg-slate-900/40 p-4 md:p-5 grid gap-3" id="commentForm"><?= Security::csrfField() ?><input type="hidden" name="comment_send" value="1"><input type="hidden" name="cstarted" value="<?= time() ?>"><input type="hidden" name="parent_id" id="parentId" value="0">
<div><h3 class="font-extrabold" id="replyTitle">Tinggalkan komentar</h3><p class="text-xs text-slate-500">Komentar dimoderasi sebelum tampil. Email tidak dipublikasikan.</p><p id="replyInfo" class="hidden mt-2 text-xs font-bold px-3 py-2 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700">Membalas <span id="replyName"></span> <button type="button" id="replyCancel" class="ml-2 underline">Batal</button></p></div>
<?php if($cOk!==''): ?><div class="text-sm px-4 py-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 font-semibold"><i class="fa fa-check-circle mr-1"></i><?= Helper::e($cOk) ?></div><?php endif; ?>
<?php if($cErr!==''): ?><div class="text-sm px-4 py-2.5 rounded-xl bg-red-50 border border-red-200 text-red-700 font-semibold"><i class="fa fa-circle-exclamation mr-1"></i><?= Helper::e($cErr) ?></div><?php endif; ?>
<div class="grid sm:grid-cols-2 gap-3">
<label class="grid gap-1 text-sm font-semibold">Nama *<input name="cname" required maxlength="100" value="<?= Helper::e($_POST['cname']??'') ?>" placeholder="Nama Anda" class="border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 font-normal bg-white dark:bg-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition placeholder:text-slate-400"></label>
<label class="grid gap-1 text-sm font-semibold">Email *<input name="cemail" required type="email" maxlength="190" value="<?= Helper::e($_POST['cemail']??'') ?>" placeholder="nama@email.com" class="border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 font-normal bg-white dark:bg-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition placeholder:text-slate-400"></label>
</div>
<label class="grid gap-1 text-sm font-semibold">Situs web <span class="font-normal text-slate-400">(opsional)</span><input name="cwebsite" maxlength="255" value="<?= Helper::e($_POST['cwebsite']??'') ?>" placeholder="https://..." class="border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 font-normal bg-white dark:bg-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition placeholder:text-slate-400"></label>
<label class="grid gap-1 text-sm font-semibold">Komentar *<textarea name="ctext" required rows="4" maxlength="2000" placeholder="Tulis komentar yang sopan..." class="border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 font-normal bg-white dark:bg-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition placeholder:text-slate-400 resize-y"><?= Helper::e($_POST['ctext']??'') ?></textarea></label>
<input type="text" name="company" value="" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
<button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl py-3 font-bold transition hover:-translate-y-0.5 hover:shadow-xl"><i class="fa fa-paper-plane mr-1"></i>Kirim Komentar</button>
</form>
<script>
(function(){
  const pid=document.getElementById('parentId'),info=document.getElementById('replyInfo'),nm=document.getElementById('replyName'),title=document.getElementById('replyTitle');
  document.querySelectorAll('.reply-btn').forEach(b=>b.addEventListener('click',()=>{
    pid.value=b.dataset.id; nm.textContent=b.dataset.name; info.classList.remove('hidden');
    title.textContent='Balas komentar '+b.dataset.name;
    document.getElementById('commentForm').scrollIntoView({behavior:'smooth',block:'center'});
  }));
  document.getElementById('replyCancel')?.addEventListener('click',()=>{
    pid.value='0'; info.classList.add('hidden'); title.textContent='Tinggalkan komentar';
  });
})();
</script>
</section>
</article>
<aside class="grid gap-4 content-start">
<?php if($latest): ?><div class="rounded-2xl border bg-white dark:bg-slate-800 p-5 reveal"><div class="flex items-center gap-2 mb-3"><h2 class="font-extrabold"><i class="fa fa-clock-rotate-left text-sky-600 mr-1"></i>Berita Terbaru</h2><a href="<?= Helper::url('berita') ?>" class="ml-auto text-xs font-bold text-emerald-600">Semua →</a></div><div class="grid gap-2.5"><?php foreach($latest as $lp): $lcv=Helper::cover($lp['featured_image']??'','berita-'.$lp['slug'],400,260); ?><a href="<?= Helper::url('berita/'.$lp['slug']) ?>" class="flex gap-3 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden bg-slate-50 dark:bg-slate-900/40 p-2 card-hover group"><img src="<?= Helper::e($lcv) ?>" alt="<?= Helper::e($lp['title']) ?>" loading="lazy" class="w-24 h-20 rounded-xl object-cover shrink-0 group-hover:scale-105 transition duration-500"><span class="min-w-0 flex-1"><span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-600"><?= Helper::e($lp['cat']??'Berita') ?></span><span class="block font-bold text-sm leading-snug line-clamp-2 mt-0.5"><?= Helper::e($lp['title']) ?></span><span class="block text-[11px] text-slate-400 mt-1"><?= Helper::tgl($lp['published_at']??$lp['created_at']) ?> • <?= number_format((int)$lp['views']) ?> dibaca</span></span></a><?php endforeach; ?></div></div><?php endif; ?>
<div class="rounded-2xl border bg-white dark:bg-slate-800 p-5 reveal"><div><h2 class="font-extrabold text-lg"><?=Helper::e($shareHeading)?></h2><p class="text-sm text-slate-500"><?=Helper::e($shareDescription)?></p></div><div class="flex flex-wrap gap-2 mt-3">
<a href="https://wa.me/?text=<?= rawurlencode($shareText.' '.$shareUrl) ?>" target="_blank" rel="noopener" class="share-btn bg-green-500" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
<a href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode($shareUrl) ?>" target="_blank" rel="noopener" class="share-btn bg-blue-600" title="Facebook"><i class="fab fa-facebook-f"></i></a>
<a href="https://twitter.com/intent/tweet?text=<?= rawurlencode($shareText) ?>&url=<?= rawurlencode($shareUrl) ?>" target="_blank" rel="noopener" class="share-btn bg-slate-900" title="X / Twitter"><i class="fab fa-x-twitter"></i></a>
<a href="https://t.me/share/url?url=<?= rawurlencode($shareUrl) ?>&text=<?= rawurlencode($shareText) ?>" target="_blank" rel="noopener" class="share-btn bg-sky-500" title="Telegram"><i class="fab fa-telegram-plane"></i></a>
<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= rawurlencode($shareUrl) ?>" target="_blank" rel="noopener" class="share-btn bg-blue-700" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
<a href="mailto:?subject=<?= rawurlencode($shareText) ?>&body=<?= rawurlencode($shareUrl) ?>" class="share-btn bg-rose-500" title="Email"><i class="fa fa-envelope"></i></a>
<button type="button" class="share-btn bg-slate-500" data-copy-url="<?= Helper::e($shareUrl) ?>" title="Salin tautan"><i class="fa fa-link"></i></button>
</div></div>
<?php if($related): ?><div class="rounded-2xl border bg-white dark:bg-slate-800 p-5 reveal"><div class="flex items-center gap-2 mb-3"><h2 class="font-extrabold"><i class="fa fa-layer-group text-emerald-600 mr-1"></i>Berita Terkait</h2><a href="<?= Helper::url('berita') ?>" class="ml-auto text-xs font-bold text-emerald-600">Semua →</a></div><div class="grid gap-2.5"><?php foreach($related as $rp): $rcv=Helper::cover($rp['featured_image']??'','berita-'.$rp['slug'],400,260); ?><a href="<?= Helper::url('berita/'.$rp['slug']) ?>" class="flex gap-3 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden bg-slate-50 dark:bg-slate-900/40 p-2 card-hover group"><img src="<?= Helper::e($rcv) ?>" alt="<?= Helper::e($rp['title']) ?>" loading="lazy" class="w-24 h-20 rounded-xl object-cover shrink-0 group-hover:scale-105 transition duration-500"><span class="min-w-0 flex-1"><span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-600"><?= Helper::e($rp['cat']??'Berita') ?></span><span class="block font-bold text-sm leading-snug line-clamp-2 mt-0.5"><?= Helper::e($rp['title']) ?></span><span class="block text-[11px] text-slate-400 mt-1"><?= Helper::tgl($rp['published_at']??$rp['created_at']) ?> • <?= number_format((int)$rp['views']) ?> dibaca</span></span></a><?php endforeach; ?></div></div><?php endif; ?>
</aside>
</div>
<style>.share-btn{width:2.65rem;height:2.65rem;border-radius:.8rem;color:#fff;display:grid;place-items:center;transition:.2s}.share-btn:hover{transform:translateY(-3px);box-shadow:0 8px 18px #0f172a30}</style>
<script>document.querySelector('[data-copy-url]')?.addEventListener('click',async e=>{const b=e.currentTarget;try{await navigator.clipboard.writeText(b.dataset.copyUrl);const o=b.innerHTML;b.innerHTML='<i class="fa fa-check"></i>';setTimeout(()=>b.innerHTML=o,1200)}catch(_){prompt('Salin tautan:',b.dataset.copyUrl)}})</script>
<?php require ROOT.'/templates/frontend/footer.php'; ?>

