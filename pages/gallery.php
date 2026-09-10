<?php
$gals=$db->query("SELECT * FROM galleries WHERE status='published' ORDER BY created_at DESC")->fetchAll();
$imgsByGal=[];
try { foreach($db->query("SELECT * FROM gallery_images ORDER BY id DESC") as $im){ $imgsByGal[$im['gallery_id']][]=$im; } } catch (Throwable) {}
$metaTitle='Galeri - '.Database::setting('school_name','Sekolah');
require ROOT.'/templates/frontend/header.php';
$totalGal=count($gals); $totalImg=0; foreach($imgsByGal as $v) $totalImg+=count($v); ?>
<div class="w-full px-4 md:px-8 py-10">
<?php
$heroBadge='<i class="fa fa-images text-amber-300"></i>Galeri Sekolah';
$heroTitle='Galeri';
$heroDesc='';
$heroCrumb='<a href="'.Helper::url().'" class="hover:text-white">Beranda</a> / Galeri';
$heroTheme='violet';
$heroStats=[['icon'=>'fa-calendar-day','label'=>Helper::pageDate('galleries'),'solid'=>true],['icon'=>'fa-images','label'=>$totalGal.' album','solid'=>false],['icon'=>'fa-camera','label'=>$totalImg.' foto','solid'=>false]];
require ROOT.'/templates/frontend/page-hero.php'; ?>
<?php if(!$gals): ?><div class="bg-white border rounded-2xl p-10 text-center mt-4 text-slate-500">Belum ada galeri</div><?php else: ?>
<?php foreach($gals as $g): $ims=$imgsByGal[$g['id']]??[]; ?>
<h2 class="font-bold mt-8 mb-3 reveal"><?= Helper::e($g['title']) ?> <span class="text-xs font-normal text-slate-400">(<?= count($ims) ?> foto)</span></h2>
<?php if(!$ims): ?><p class="text-sm text-slate-500">Belum ada foto pada album ini.</p><?php else: ?>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3"><?php foreach($ims as $im): ?>
<img src="<?= Helper::url($im['filepath']) ?>" alt="<?= Helper::e($im['caption']??$g['title']) ?>" data-lightbox loading="lazy" class="h-44 w-full object-cover rounded-xl border cursor-zoom-in card-hover reveal">
<?php endforeach; ?></div><?php endif; ?><?php endforeach; ?><?php endif; ?></div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
