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
<div class="mt-4 rounded-[2rem] bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-600 text-white p-7 md:p-10 reveal relative overflow-hidden">
<span class="absolute -right-12 -top-14 w-64 h-64 rounded-full border-[28px] border-white/10"></span><span class="absolute -left-16 -bottom-16 w-56 h-56 rounded-full border-[28px] border-white/10"></span>
<span class="absolute inset-0 pointer-events-none opacity-15" style="background-image:linear-gradient(rgba(255,255,255,.14) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.14) 1px,transparent 1px);background-size:36px 36px;mask-image:radial-gradient(ellipse at center,black 40%,transparent 75%)"></span>
<div class="relative max-w-3xl">
<span class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/20 px-3 py-1 text-[11px] font-bold uppercase tracking-[.16em]"><i class="fa fa-quote-left text-amber-300"></i>Visi Sekolah</span>
<blockquote class="text-2xl md:text-3xl font-extrabold leading-tight mt-4 text-white"><?= $vision!==''?strip_tags($vision,'<br><strong><em>'):'Visi belum diisi — kelola via Sekolah > Visi, Misi & Tujuan.' ?></blockquote>
</div>
</div>
<div class="grid md:grid-cols-2 gap-4 mt-4 items-start">
<article class="bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-[1.7rem] p-6 md:p-7 reveal overflow-hidden">
<h2 class="font-extrabold flex items-center gap-2"><span class="w-9 h-9 rounded-xl bg-sky-600 text-white grid place-items-center shadow"><i class="fa fa-list-check text-sm"></i></span>Misi <span class="ml-auto text-[11px] font-bold px-2.5 py-1 rounded-full bg-sky-100 dark:bg-sky-900 text-sky-700 dark:text-sky-200"><?= $mCount ?> poin</span></h2>
<div class="vm-list mt-4 text-sm leading-relaxed text-slate-600 dark:text-slate-300"><?= $mission!==''?$mission:'<p class="text-slate-400">Belum diisi.</p>' ?></div>
</article>
<article class="bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-[1.7rem] p-6 md:p-7 reveal overflow-hidden">
<h2 class="font-extrabold flex items-center gap-2"><span class="w-9 h-9 rounded-xl bg-amber-500 text-white grid place-items-center shadow"><i class="fa fa-flag text-sm"></i></span>Tujuan <span class="ml-auto text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-200"><?= $gCount ?> poin</span></h2>
<div class="vm-list mt-4 text-sm leading-relaxed text-slate-600 dark:text-slate-300"><?= $goals!==''?$goals:'<p class="text-slate-400">Belum diisi.</p>' ?></div>
</article>
</div>
</div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
