<?php
if((Database::setting('vm_show','1')==='0')){ http_response_code(404); require ROOT.'/templates/error/404.php'; exit; }
$title=Database::setting('vm_title','Visi, Misi & Tujuan');
$vision=Database::setting('vision_content',''); $mission=Database::setting('mission_content',''); $goals=Database::setting('goals_content','');
$metaTitle=$title.' - '.Database::setting('school_name','Sekolah'); require ROOT.'/templates/frontend/header.php';
$mCount=max(substr_count(strtolower($mission),'<li'),count(array_filter(array_map('trim',explode("\n",strip_tags($mission))))));
$gCount=max(substr_count(strtolower($goals),'<li'),count(array_filter(array_map('trim',explode("\n",strip_tags($goals))))));
?>
<div class="w-full px-4 md:px-8 py-10">
<?php
$heroBadge='<i class="fa fa-bullseye text-amber-300"></i>Visi Misi Tujuan';
$heroTitle=$title;
$heroDesc='';
$heroCrumb='<a href="'.Helper::url().'" class="hover:text-white">Beranda</a> / Visi Misi';
$heroTheme='emerald';
$heroStats=[['icon'=>'fa-eye','label'=>'Visi','solid'=>true],['icon'=>'fa-list-check','label'=>$mCount.' misi','solid'=>false],['icon'=>'fa-flag','label'=>$gCount.' tujuan','solid'=>false]];
require ROOT.'/templates/frontend/page-hero.php'; ?>
<div class="grid lg:grid-cols-3 gap-4 mt-4 items-start">
<article class="bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-2xl p-6 md:p-8 reveal">
<h2 class="font-extrabold flex items-center gap-2"><span class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-200 grid place-items-center"><i class="fa fa-eye text-sm"></i></span>Visi</h2>
<div class="mt-3 text-sm text-justify leading-relaxed text-slate-600 dark:text-slate-300"><?= $vision!==''?$vision:'<p class="text-slate-400">Belum diisi. Kelola via Sekolah &gt; Visi, Misi & Tujuan.</p>' ?></div>
</article>
<article class="bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-2xl p-6 md:p-8 reveal">
<h2 class="font-extrabold flex items-center gap-2"><span class="w-9 h-9 rounded-xl bg-sky-100 dark:bg-sky-900 text-sky-700 dark:text-sky-200 grid place-items-center"><i class="fa fa-list-check text-sm"></i></span>Misi</h2>
<div class="mt-3 text-sm text-justify leading-relaxed text-slate-600 dark:text-slate-300"><?= $mission!==''?$mission:'<p class="text-slate-400">Belum diisi.</p>' ?></div>
</article>
<article class="bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-2xl p-6 md:p-8 reveal">
<h2 class="font-extrabold flex items-center gap-2"><span class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-200 grid place-items-center"><i class="fa fa-flag text-sm"></i></span>Tujuan</h2>
<div class="mt-3 text-sm text-justify leading-relaxed text-slate-600 dark:text-slate-300"><?= $goals!==''?$goals:'<p class="text-slate-400">Belum diisi.</p>' ?></div>
</article>
</div>
</div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
