<?php $rows=$db->query("SELECT * FROM achievements WHERE is_active=1 ORDER BY id DESC")->fetchAll(); $metaTitle='Prestasi - '.Database::setting('school_name','Sekolah'); require ROOT.'/templates/frontend/header.php'; ?>
<div class="bg-gradient-to-r from-amber-600 via-orange-500 to-rose-500 text-white">
<div class="max-w-7xl mx-auto px-4 py-12 reveal">
<nav class="text-xs text-amber-100 mb-2"><a href="<?= Helper::url() ?>" class="hover:text-white">Beranda</a> / Prestasi</nav>
<h1 class="text-3xl md:text-4xl font-extrabold">Prestasi Sekolah</h1>
<p class="text-amber-50 mt-1 max-w-2xl">Kebanggaan siswa dan sekolah di berbagai ajang.</p>
<div class="mt-4 text-sm"><span class="bg-white/15 px-3 py-1.5 rounded-full"><b><?= count($rows) ?></b> prestasi</span></div>
</div></div>
<div class="max-w-7xl mx-auto px-4 py-8">
<?php if(!$rows): ?><div class="bg-white dark:bg-slate-800 border rounded-3xl p-12 text-center text-slate-500 reveal"><span class="w-14 h-14 rounded-2xl bg-amber-100 grid place-items-center mx-auto text-2xl">🏆</span><p class="font-extrabold text-lg mt-3">Belum ada prestasi</p></div><?php else: ?>
<div class="grid md:grid-cols-3 gap-4"><?php foreach($rows as $r): ?>
<article class="rounded-3xl overflow-hidden border dark:border-slate-700 bg-white dark:bg-slate-800 card-hover reveal">
<div class="bg-gradient-to-r from-amber-400 to-orange-500 p-4 flex items-center gap-3">
<span class="w-11 h-11 rounded-xl bg-white/25 grid place-items-center text-white text-xl shrink-0"><i class="fa fa-trophy"></i></span>
<span class="flex gap-1.5 flex-wrap"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/25 text-white"><?= Helper::e($r['level']??'Sekolah') ?></span><span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-900/30 text-white"><?= Helper::e($r['year']??date('Y')) ?></span></span>
</div>
<div class="p-4"><h2 class="font-extrabold leading-snug"><?= Helper::e($r['title']) ?></h2>
<?php if(!empty($r['description'])): ?><p class="text-sm text-slate-600 dark:text-slate-300 mt-1.5 leading-relaxed text-justify"><?= nl2br(Helper::e($r['description'])) ?></p><?php endif; ?></div>
</article>
<?php endforeach; ?></div><?php endif; ?></div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
