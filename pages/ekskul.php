<?php $rows=$db->query("SELECT * FROM extracurriculars WHERE is_active=1 ORDER BY sort_order")->fetchAll(); $metaTitle='Ekstrakurikuler - '.Database::setting('school_name','Sekolah'); require ROOT.'/templates/frontend/header.php'; ?>
<div class="w-full px-4 md:px-8 py-10">
<?php
$heroBadge='<i class="fa fa-futbol text-amber-300"></i>Ekstrakurikuler';
$heroTitle='Ekstrakurikuler';
$heroDesc='';
$heroCrumb='<a href="'.Helper::url().'" class="hover:text-white">Beranda</a> / Ekstrakurikuler';
$heroTheme='teal';
$heroStats=[['icon'=>'fa-futbol','label'=>count($rows).' kegiatan aktif','solid'=>true]];
require ROOT.'/templates/frontend/page-hero.php'; ?>
<div class="mt-4">
<?php $ekIcon=['fa-futbol','fa-campground','fa-palette','fa-music','fa-robot','fa-book-quran']; ?>
<?php if(!$rows): ?><div class="bg-white dark:bg-slate-800 border rounded-3xl p-12 text-center text-slate-500 reveal"><span class="w-14 h-14 rounded-2xl bg-emerald-100 grid place-items-center mx-auto text-2xl">⚽</span><p class="font-extrabold text-lg mt-3">Belum ada ekstrakurikuler</p></div><?php else: ?>
<div class="grid md:grid-cols-3 gap-4"><?php foreach($rows as $i=>$r): $ic=$ekIcon[$i%count($ekIcon)]; ?>
<article class="bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-3xl p-5 card-hover reveal">
<div class="flex items-center gap-3">
<span class="w-12 h-12 rounded-2xl bg-gradient-to-b from-emerald-500 to-teal-600 text-white grid place-items-center text-xl shrink-0"><i class="fa <?= $ic ?>"></i></span>
<div class="min-w-0"><h2 class="font-extrabold leading-snug"><?= Helper::e($r['name']) ?></h2>
<?php if(!empty($r['coach'])): ?><p class="text-[11px] text-slate-500"><i class="fa fa-user-tie mr-1 text-emerald-500"></i><?= Helper::e($r['coach']) ?></p><?php endif; ?></div>
</div>
<?php if(!empty($r['description'])): ?><p class="text-sm text-slate-600 dark:text-slate-300 mt-3 leading-relaxed text-justify"><?= nl2br(Helper::e($r['description'])) ?></p><?php endif; ?>
<div class="flex flex-wrap gap-1.5 mt-3">
<?php if(!empty($r['coach'])): ?><span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700"><i class="fa fa-user mr-0.5"></i><?= Helper::e($r['coach']) ?></span><?php endif; ?>
<?php if(!empty($r['schedule'])): ?><span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-200"><i class="fa fa-clock mr-0.5"></i><?= Helper::e($r['schedule']) ?></span><?php endif; ?>
</div></article>
<?php endforeach; ?></div><?php endif; ?></div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
