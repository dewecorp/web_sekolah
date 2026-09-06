<?php
$st=$db->prepare("SELECT p.*,c.name cat,u.name author FROM posts p LEFT JOIN categories c ON c.id=p.category_id LEFT JOIN users u ON u.id=p.author_id WHERE p.slug=? AND p.status='published' LIMIT 1");
$st->execute([$slug??'']); $p=$st->fetch();
if(!$p){ http_response_code(404); require ROOT.'/templates/error/404.php'; exit; }
$db->prepare("UPDATE posts SET views=views+1 WHERE id=?")->execute([$p['id']]);
$metaTitle=$p['title']; $metaDesc=Helper::excerpt($p['excerpt']?:$p['content']);
require ROOT.'/templates/frontend/header.php'; ?>
<?php $shareUrl=Helper::url('berita/'.$p['slug']);$shareText=$p['title'].' - '.Database::setting('school_name','Sekolah');$shareHeading=Database::setting('share_heading','Bagikan berita ini');$shareDescription=Database::setting('share_description','Sebarkan informasi kepada keluarga dan teman.'); ?>
<div class="w-full px-4 md:px-8 py-10">
<?php
$heroBadge='<i class="fa fa-newspaper text-amber-300"></i>'.Helper::e($p['cat']??'Berita');
$heroTitle=$p['title'];
$heroDesc=!empty($p['excerpt'])?nl2br(Helper::e($p['excerpt'])):'';
$heroCrumb='<a href="'.Helper::url().'" class="hover:text-white">Beranda</a> / <a href="'.Helper::url('berita').'" class="hover:text-white">Berita</a> / '.Helper::e($p['cat']??'Berita');
$heroTheme='sky';
$heroStats=[['icon'=>'fa-user','label'=>$p['author']??'Redaksi','solid'=>true],['icon'=>'fa-calendar-day','label'=>Helper::tgl($p['published_at']??$p['created_at']),'solid'=>false],['icon'=>'fa-eye','label'=>(int)$p['views'].' dibaca','solid'=>false]];
require ROOT.'/templates/frontend/page-hero.php'; ?>
<div class="mt-4 grid lg:grid-cols-3 gap-4 items-start">
<article class="lg:col-span-2 bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-2xl p-6 md:p-8 reveal">
<img src="<?= Helper::e(Helper::cover($p['featured_image']??'', 'berita-'.$p['slug'], 1200, 630)) ?>" alt="<?= Helper::e($p['title']) ?>" class="rounded-2xl w-full aspect-[16/9] object-cover" loading="lazy">
<div class="prose max-w-none mt-4 text-slate-700 dark:text-slate-200 text-justify leading-relaxed"><?= $p['content'] ?></div>
</article>
<aside class="grid gap-4"><div class="rounded-2xl border bg-white dark:bg-slate-800 p-5 reveal"><div><h2 class="font-extrabold text-lg"><?=Helper::e($shareHeading)?></h2><p class="text-sm text-slate-500"><?=Helper::e($shareDescription)?></p></div><div class="flex flex-wrap gap-2 mt-3">
<a href="https://wa.me/?text=<?= rawurlencode($shareText.' '.$shareUrl) ?>" target="_blank" rel="noopener" class="share-btn bg-green-500" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
<a href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode($shareUrl) ?>" target="_blank" rel="noopener" class="share-btn bg-blue-600" title="Facebook"><i class="fab fa-facebook-f"></i></a>
<a href="https://twitter.com/intent/tweet?text=<?= rawurlencode($shareText) ?>&url=<?= rawurlencode($shareUrl) ?>" target="_blank" rel="noopener" class="share-btn bg-slate-900" title="X / Twitter"><i class="fab fa-x-twitter"></i></a>
<a href="https://t.me/share/url?url=<?= rawurlencode($shareUrl) ?>&text=<?= rawurlencode($shareText) ?>" target="_blank" rel="noopener" class="share-btn bg-sky-500" title="Telegram"><i class="fab fa-telegram-plane"></i></a>
<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= rawurlencode($shareUrl) ?>" target="_blank" rel="noopener" class="share-btn bg-blue-700" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
<a href="mailto:?subject=<?= rawurlencode($shareText) ?>&body=<?= rawurlencode($shareUrl) ?>" class="share-btn bg-rose-500" title="Email"><i class="fa fa-envelope"></i></a>
<button type="button" class="share-btn bg-slate-500" data-copy-url="<?= Helper::e($shareUrl) ?>" title="Salin tautan"><i class="fa fa-link"></i></button>
</div></div></aside>
</div>
<style>.share-btn{width:2.65rem;height:2.65rem;border-radius:.8rem;color:#fff;display:grid;place-items:center;transition:.2s}.share-btn:hover{transform:translateY(-3px);box-shadow:0 8px 18px #0f172a30}</style>
<script>document.querySelector('[data-copy-url]')?.addEventListener('click',async e=>{try{await navigator.clipboard.writeText(e.currentTarget.dataset.copyUrl);Swal.fire({icon:'success',title:'Tautan disalin',timer:1000,showConfirmButton:false})}catch(_){prompt('Salin tautan:',e.currentTarget.dataset.copyUrl)}})</script>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
