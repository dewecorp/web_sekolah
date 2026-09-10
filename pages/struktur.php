<?php
if((Database::setting('struktur_show','1')==='0')){ http_response_code(404); require ROOT.'/templates/error/404.php'; exit; }
try{ $srows=$db->query("SELECT s.*,t.name tname,t.photo tphoto,t.subject tsubject,t.education tedu FROM structures s LEFT JOIN teachers t ON t.id=s.teacher_id ORDER BY s.sort_order,s.id")->fetchAll(); }catch(Throwable){ $srows=[]; }
$prof=$db->query("SELECT org_chart FROM school_profile LIMIT 1")->fetch();
$title=Database::setting('struktur_title','Struktur Organisasi'); $desc=Database::setting('struktur_desc','');
$metaTitle=$title.' - '.Database::setting('school_name','Sekolah'); require ROOT.'/templates/frontend/header.php';
$total=count($srows); $foto=count(array_filter($srows,fn($x)=>!empty($x['tphoto'])));
?>
<div class="w-full px-4 md:px-8 py-10">
<div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-violet-700 via-indigo-600 to-sky-500 text-white p-7 md:p-12 shadow-xl reveal">
<span class="absolute -right-16 -top-20 w-64 h-64 rounded-full border-[28px] border-white/10"></span>
<span class="absolute -left-20 -bottom-24 w-72 h-72 rounded-full border-[36px] border-white/10"></span>
<?php $hal=Database::setting('hero_align','center'); $haC=$hal==='left'?'text-left':($hal==='right'?'text-right':'text-center'); $hjC=$hal==='left'?'justify-start':($hal==='right'?'justify-end':'justify-center'); $hmC=$hal==='left'?'mr-auto':($hal==='right'?'ml-auto':'mx-auto'); ?>
<div class="relative max-w-3xl <?= $hmC ?> <?= $haC ?>">
<nav class="text-xs text-white/70 mb-3 <?= $haC ?>"><a href="<?= Helper::url() ?>" class="hover:text-white">Beranda</a> / Struktur</nav>
<span class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/20 px-3 py-1 text-[11px] font-bold uppercase tracking-[.16em]"><i class="fa fa-sitemap text-amber-300"></i>Organisasi Sekolah</span>
<h1 class="text-3xl md:text-5xl font-extrabold leading-tight mt-4 <?= $haC ?>"><?= Helper::e($title) ?></h1>
<?php if($desc): ?><p class="text-white/80 text-sm md:text-base max-w-2xl mt-4 <?= $hmC ?> <?= $haC ?> leading-relaxed"><?= nl2br(Helper::e($desc)) ?></p><?php endif; ?>
<div class="mt-5 flex flex-wrap gap-2 text-sm <?= $hjC ?> <?= $haC ?>">
<span class="inline-flex items-center gap-2 bg-white text-slate-900 px-4 py-2 rounded-xl font-bold"><i class="fa fa-calendar-day text-violet-600"></i><?= Helper::pageDate('structures') ?></span>
<span class="inline-flex items-center gap-2 border border-white/40 px-4 py-2 rounded-xl font-bold"><i class="fa fa-users"></i><?= $total ?> pejabat</span>
<span class="inline-flex items-center gap-2 border border-white/40 px-4 py-2 rounded-xl font-bold"><i class="fa fa-image"></i><?= $foto ?> berfoto</span>
<?php if(!empty($prof['org_chart'])): ?><a href="#bagan" class="inline-flex items-center gap-2 border border-white/40 px-4 py-2 rounded-xl font-bold hover:bg-white/10"><i class="fa fa-diagram-project"></i>Lihat bagan</a><?php endif; ?>
</div>
</div>
</div>
<div class="mt-4 flex justify-end"><div class="relative w-full sm:w-72"><i class="fa fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i><input id="qStr" placeholder="Cari nama / jabatan..." class="w-full border dark:border-slate-700 rounded-xl pl-10 pr-3 py-2.5 text-sm bg-white dark:bg-slate-800 shadow-sm focus:ring-2 focus:ring-violet-500 focus:outline-none"></div></div>
<?php if(!$srows): ?><div class="mt-4 rounded-3xl border border-dashed p-12 text-center text-slate-500"><span class="w-14 h-14 rounded-2xl bg-violet-100 dark:bg-violet-900 grid place-items-center mx-auto text-2xl">🏛️</span><p class="font-bold mt-3">Belum ada data struktur</p><p class="text-xs">Admin dapat menambah via Sekolah &gt; Struktur.</p></div><?php else: ?>
<?php $strCols=Database::setting('struktur_cols','4'); $strGrid=$strCols==='2'?'sm:grid-cols-2':($strCols==='3'?'sm:grid-cols-2 lg:grid-cols-3':'sm:grid-cols-2 lg:grid-cols-4'); ?>
<div class="mt-4 grid <?= $strGrid ?> gap-4" id="strGrid">
<?php foreach($srows as $ri=>$r): $initials=implode('',array_map(fn($w)=>mb_strtoupper(mb_substr($w,0,1)),array_slice(preg_split('/\s+/',trim($r['tname']??'?')),0,2))); ?>
<article class="srow group relative rounded-3xl overflow-hidden border bg-white dark:bg-slate-800 dark:border-slate-700 card-hover reveal flex flex-col" data-t="<?= Helper::e(strtolower(($r['tname']??'').' '.($r['position']??'').' '.($r['tsubject']??''))) ?>">
<div class="bg-gradient-to-r from-violet-600 via-indigo-600 to-sky-500 p-4 flex items-center gap-3">
<?php if(!empty($r['tphoto'])): ?><img src="<?= Helper::upload($r['tphoto']) ?>" alt="<?= Helper::e($r['tname']??'') ?>" data-lightbox loading="lazy" class="w-14 h-14 rounded-2xl object-cover border-2 border-white/80 shadow cursor-zoom-in shrink-0"><?php else: ?><span class="w-14 h-14 rounded-2xl bg-white text-violet-700 grid place-items-center font-extrabold text-xl shrink-0 shadow"><?= Helper::e($initials) ?></span><?php endif; ?>
<div class="min-w-0 flex-1"><h2 class="font-extrabold text-white leading-tight truncate"><?= Helper::e($r['tname']??'-') ?></h2><span class="inline-flex items-center gap-1.5 text-xs font-extrabold tracking-wide px-3 py-1 rounded-full bg-white text-violet-700 shadow mt-2 ring-2 ring-white/60"><i class="fa fa-id-badge"></i><?= Helper::e($r['position']) ?></span></div>
<span class="hidden sm:grid w-8 h-8 rounded-full bg-white/15 place-items-center text-white/80 group-hover:bg-white group-hover:text-violet-600 transition"><i class="fa fa-arrow-right text-xs"></i></span>
</div>
<div class="p-4 flex-1 grid gap-1.5 text-sm">
<?php if(!empty($r['tedu'])): ?><p class="text-xs text-slate-500"><i class="fa fa-graduation-cap mr-1.5 text-violet-500"></i><?= Helper::e($r['tedu']) ?></p><?php endif; ?>
<?php if(!empty($r['tsubject'])): ?><p class="text-xs text-slate-500"><i class="fa fa-book mr-1.5 text-sky-500"></i><?= Helper::e($r['tsubject']) ?></p><?php endif; ?>
</div>
</article>
<?php endforeach; ?>
</div>
<p id="strEmpty" class="hidden text-center text-sm text-slate-500 py-8">Tidak cocok. Ubah kata kunci.</p>
<?php endif; ?>
<?php if(!empty($prof['org_chart'])): ?><img id="bagan" src="<?= Helper::upload($prof['org_chart']) ?>" alt="Bagan struktur organisasi" data-lightbox loading="lazy" class="reveal scroll-mt-28 mt-4 rounded-2xl cursor-zoom-in w-full object-contain"><?php endif; ?>
</div>
<script>
(function(){var q=document.getElementById('qStr'),rows=[...document.querySelectorAll('#strGrid .srow')],em=document.getElementById('strEmpty');q?.addEventListener('input',()=>{var s=(q.value||'').toLowerCase(),n=0;rows.forEach(r=>{var ok=!s||r.dataset.t.includes(s);r.style.display=ok?'':'none';if(ok)n++});if(em)em.classList.toggle('hidden',n>0)})})();
</script>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
