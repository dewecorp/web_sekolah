<?php
// Partial hero modern reusable. Variabel: $heroBadge, $heroTitle, $heroDesc, $heroCrumb, $heroTheme, $heroStats ([icon,label,value]), $heroActions (html)
$heroBadge = $heroBadge ?? '';
$heroTitle = $heroTitle ?? ($metaTitle ?? 'Halaman');
$heroDesc = $heroDesc ?? '';
$heroCrumb = $heroCrumb ?? '';
$heroTheme = $heroTheme ?? 'emerald';
$heroStats = $heroStats ?? [];
$heroActions = $heroActions ?? '';
$heroAlign = $heroAlign ?? Database::setting('hero_align','center'); // left, center, right
$themes = [
  'emerald'=>'from-emerald-700 via-emerald-600 to-teal-500',
  'violet'=>'from-violet-700 via-indigo-600 to-sky-500',
  'sky'=>'from-slate-950 via-sky-900 to-indigo-900',
  'amber'=>'from-amber-600 via-orange-500 to-rose-500',
  'teal'=>'from-teal-700 via-emerald-600 to-lime-500',
];
$g = $themes[$heroTheme] ?? $themes['emerald'];
$alignClass = match($heroAlign) {
  'left' => 'text-left',
  'right' => 'text-right',
  default => 'text-center'
};
$justifyClass = match($heroAlign) {
  'left' => 'justify-start',
  'right' => 'justify-end',
  default => 'center'
};
?>
<div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br <?= $g ?> text-white p-7 md:p-12 shadow-xl reveal">
<span class="absolute -right-16 -top-20 w-64 h-64 rounded-full border-[28px] border-white/10"></span>
<span class="absolute -left-20 -bottom-24 w-72 h-72 rounded-full border-[36px] border-white/10"></span>
<div class="relative max-w-3xl mx-auto <?= $alignClass ?>">
<?php if($heroCrumb): ?><nav class="text-xs text-white/70 mb-3 <?= $alignClass ?>"><?= $heroCrumb ?></nav><?php endif; ?>
<?php if($heroBadge): ?><span class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/20 px-3 py-1 text-[11px] font-bold uppercase tracking-[.16em]"><?= $heroBadge ?></span><?php endif; ?>
<h1 class="text-3xl md:text-5xl font-extrabold leading-tight mt-4 <?= $alignClass ?>"><?= Helper::e($heroTitle) ?></h1>
<?php if($heroDesc): ?><p class="text-white/80 text-sm md:text-base max-w-2xl mt-4 leading-relaxed <?= $alignClass ?>"><?= $heroDesc ?></p><?php endif; ?>
<?php if($heroStats||$heroActions): ?><div class="mt-5 flex flex-wrap gap-2 text-sm <?= $justifyClass ?>"><?= $heroActions ?><?php foreach($heroStats as $st): ?><span class="inline-flex items-center gap-2 <?= ($st['solid']??false)?'bg-white text-slate-900':'border border-white/40' ?> px-4 py-2 rounded-xl font-bold"><i class="fa <?= Helper::e($st['icon']??'fa-circle') ?> <?= ($st['solid']??false)?'text-emerald-600':'' ?>"></i><?= Helper::e($st['label']??'') ?></span><?php endforeach; ?></div><?php endif; ?>
</div>
</div>
