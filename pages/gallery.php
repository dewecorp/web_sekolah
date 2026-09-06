<?php
$gals=$db->query("SELECT * FROM galleries WHERE status='published' ORDER BY created_at DESC")->fetchAll();
$imgsByGal=[];
try { foreach($db->query("SELECT * FROM gallery_images ORDER BY id DESC") as $im){ $imgsByGal[$im['gallery_id']][]=$im; } } catch (Throwable) {}
$metaTitle='Galeri - '.Database::setting('school_name','Sekolah');
require ROOT.'/templates/frontend/header.php'; ?>
<div class="max-w-7xl mx-auto px-4 py-10"><h1 class="text-3xl font-extrabold reveal">Galeri</h1>
<?php if(!$gals): ?><div class="bg-white border rounded-2xl p-10 text-center mt-6 text-slate-500">Belum ada galeri</div><?php else: ?>
<?php foreach($gals as $g): $ims=$imgsByGal[$g['id']]??[]; ?>
<h2 class="font-bold mt-8 mb-3 reveal"><?= Helper::e($g['title']) ?></h2>
<?php if(!$ims): ?><p class="text-sm text-slate-500">Belum ada foto pada album ini.</p><?php else: ?>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3"><?php foreach($ims as $im): ?>
<img src="<?= Helper::url($im['filepath']) ?>" alt="<?= Helper::e($im['caption']??$g['title']) ?>" data-lightbox loading="lazy" class="h-44 w-full object-cover rounded-xl border cursor-zoom-in card-hover reveal">
<?php endforeach; ?></div><?php endif; ?><?php endforeach; ?><?php endif; ?></div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
