<?php
$per=9; $page=max(1,(int)($_GET['page']??1)); $off=($page-1)*$per;
$total=(int)$db->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn();
$st=$db->prepare("SELECT p.*,c.name cat FROM posts p LEFT JOIN categories c ON c.id=p.category_id WHERE p.status='published' ORDER BY published_at DESC LIMIT ? OFFSET ?");
$st->bindValue(1,$per,PDO::PARAM_INT); $st->bindValue(2,$off,PDO::PARAM_INT); $st->execute(); $posts=$st->fetchAll();
$metaTitle='Berita - '.Database::setting('school_name','Sekolah');
require ROOT.'/templates/frontend/header.php'; ?>
<div class="max-w-7xl mx-auto px-4 py-10"><h1 class="text-3xl font-extrabold reveal">Berita</h1>
<?php if(!$posts): ?><div class="bg-white border rounded-2xl p-10 text-center mt-6 text-slate-500">Belum ada berita</div><?php else: ?>
<div class="grid md:grid-cols-3 gap-4 mt-6"><?php foreach($posts as $p): $cv=Helper::cover($p['featured_image']??'', 'berita-'.$p['slug'], 800, 500); ?>
<article class="bg-white dark:bg-slate-800 rounded-2xl border overflow-hidden card-hover reveal">
<img src="<?= Helper::e($cv) ?>" alt="<?= Helper::e($p['title']) ?>" class="h-44 w-full object-cover" loading="lazy">
<div class="p-4"><h2 class="font-bold"><?= Helper::e($p['title']) ?></h2><p class="text-xs text-slate-500"><?= Helper::tgl($p['published_at']??$p['created_at']) ?></p><a href="<?= Helper::url('berita/'.$p['slug']) ?>" class="text-emerald-600 text-sm font-semibold">Baca →</a></div></article>
<?php endforeach; ?></div><?= Helper::paginate($total,$per,$page,Helper::url('berita')) ?><?php endif; ?></div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
