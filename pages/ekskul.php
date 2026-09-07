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
<div class="min-w-0"><h2 class="font-extrabold leading-snug"><?= Helper::e($r['name']) ?></h2></div>
</div>
<?php if(!empty($r['description'])): ?><p class="text-sm text-slate-600 dark:text-slate-300 mt-3 leading-relaxed text-justify"><?= nl2br(Helper::e($r['description'])) ?></p><?php endif; ?>
<div class="grid grid-cols-3 gap-2 mt-3 text-center">
<?php if(!empty($r['coach'])): ?><div class="rounded-xl bg-slate-100 dark:bg-slate-800 p-2.5"><div class="text-[10px] font-bold text-emerald-600 mb-0.5"><i class="fa fa-user-tie mr-1"></i>Pembina</div><div class="text-sm font-semibold"><?= Helper::e($r['coach']) ?></div></div><?php endif; ?>
<?php if(!empty($r['day'])): ?><div class="rounded-xl bg-amber-50 dark:bg-amber-900/30 p-2.5"><div class="text-[10px] font-bold text-amber-700 mb-0.5"><i class="fa fa-calendar-day mr-1"></i>Hari</div><div class="text-sm font-semibold"><?= Helper::e($r['day']) ?></div></div><?php endif; ?>
<?php if(!empty($r['time'])): ?><div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/30 p-2.5"><div class="text-[10px] font-bold text-emerald-700 mb-0.5"><i class="fa fa-clock mr-1"></i>Jam</div><div class="text-sm font-semibold"><?= date('H:i', strtotime($r['time'])) ?></div></div><?php endif; ?>
<?php if(!empty($r['schedule']) && empty($r['coach']) && empty($r['day']) && empty($r['time'])): ?><div class="col-span-3 rounded-xl bg-sky-50 dark:bg-sky-900/30 p-2.5"><div class="text-[10px] font-bold text-sky-700 mb-0.5"><i class="fa fa-location-dot mr-1"></i>Tempat</div><div class="text-sm font-semibold"><?= Helper::e($r['schedule']) ?></div></div><?php endif; ?>
</div>
<?php if(!empty($r['schedule']) && (!empty($r['coach']) || !empty($r['day']) || !empty($r['time']))): ?><div class="mt-2 rounded-xl bg-sky-50 dark:bg-sky-900/30 p-2.5 text-center"><div class="text-[10px] font-bold text-sky-700 mb-0.5"><i class="fa fa-location-dot mr-1"></i>Tempat</div><div class="text-sm font-semibold"><?= Helper::e($r['schedule']) ?></div></div><?php endif; ?>
</article>
<?php endforeach; ?></div><?php endif; ?></div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
