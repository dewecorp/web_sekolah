<?php
$st=$db->prepare("SELECT p.*,c.name cat,u.name author FROM posts p LEFT JOIN categories c ON c.id=p.category_id LEFT JOIN users u ON u.id=p.author_id WHERE p.slug=? AND p.status='published' LIMIT 1");
$st->execute([$slug??'']); $p=$st->fetch();
if(!$p){ http_response_code(404); require ROOT.'/templates/error/404.php'; exit; }
$db->prepare("UPDATE posts SET views=views+1 WHERE id=?")->execute([$p['id']]);
$metaTitle=$p['title']; $metaDesc=Helper::excerpt($p['excerpt']?:$p['content']);
require ROOT.'/templates/frontend/header.php'; ?>
<div class="max-w-3xl mx-auto px-4 py-10">
<nav class="text-xs text-slate-500 mb-3"><a href="<?= Helper::url() ?>">Beranda</a> / <a href="<?= Helper::url('berita') ?>">Berita</a> / <?= Helper::e($p['title']) ?></nav>
<span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded"><?= Helper::e($p['cat']??'Berita') ?></span>
<h1 class="text-3xl font-extrabold mt-2"><?= Helper::e($p['title']) ?></h1>
<p class="text-xs text-slate-500 mt-1"><?= Helper::e($p['author']??'') ?> • <?= Helper::tgl($p['published_at']??$p['created_at']) ?> • <?= (int)$p['views'] ?> dibaca</p>
<img src="<?= Helper::e(Helper::cover($p['featured_image']??'', 'berita-'.$p['slug'], 1200, 630)) ?>" alt="<?= Helper::e($p['title']) ?>" class="rounded-2xl mt-4 w-full aspect-[16/9] object-cover" loading="lazy">
<article class="prose max-w-none mt-4 text-slate-700 dark:text-slate-200"><?= $p['content'] ?></article>
</div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
