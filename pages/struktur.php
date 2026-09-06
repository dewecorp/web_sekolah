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
<div class="relative max-w-3xl">
<nav class="text-xs text-white/70 mb-3"><a href="<?= Helper::url() ?>" class="hover:text-white">Beranda</a> / Struktur</nav>
<span class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/20 px-3 py-1 text-[11px] font-bold uppercase tracking-[.16em]"><i class="fa fa-sitemap text-amber-300"></i>Organisasi Sekolah</span>
<h1 class="text-3xl md:text-5xl font-extrabold leading-tight mt-4"><?= Helper::e($title) ?></h1>
<?php if($desc): ?><p class="text-white/80 text-sm md:text-base max-w-2xl mt-4 text-justify leading-relaxed"><?= nl2br(Helper::e($desc)) ?></p><?php endif; ?>
<div class="mt-5 flex flex-wrap gap-2 text-sm">
<span class="inline-flex items-center gap-2 bg-white text-slate-900 px-4 py-2 rounded-xl font-bold"><i class="fa fa-users text-violet-600"></i><?= $total ?> pejabat</span>
<span class="inline-flex items-center gap-2 border border-white/40 px-4 py-2 rounded-xl font-bold"><i class="fa fa-image"></i><?= $foto ?> berfoto</span>
<?php if(!empty($prof['org_chart'])): ?><a href="#bagan" class="inline-flex items-center gap-2 border border-white/40 px-4 py-2 rounded-xl font-bold hover:bg-white/10"><i class="fa fa-diagram-project"></i>Lihat bagan</a><?php endif; ?>
</div>
</div>
</div>
<div class="bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-2xl overflow-hidden reveal mt-4">
<div class="p-4 md:p-5 border-b dark:border-slate-700 flex justify-end">
<div class="relative w-full sm:w-64"><i class="fa fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i><input id="qStr" placeholder="Cari nama / jabatan..." class="w-full border dark:border-slate-700 rounded-xl pl-10 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-900 focus:ring-2 focus:ring-violet-500 focus:outline-none"></div>
</div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[560px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50 dark:bg-slate-700/50"><th class="p-3 w-12">No</th><th class="p-3">Nama</th><th class="p-3">Jabatan</th><th class="p-3 hidden md:table-cell">Mapel</th></tr>
<?php if(!$srows): ?><tr><td colspan="4" class="p-12 text-center text-slate-500"><span class="w-14 h-14 rounded-2xl bg-violet-100 dark:bg-violet-900 grid place-items-center mx-auto text-2xl">🏛️</span><p class="font-bold mt-3">Belum ada data struktur</p><p class="text-xs">Admin dapat menambah via Sekolah &gt; Struktur.</p></td></tr><?php endif; ?>
<?php $no=1; foreach($srows as $r): ?>
<tr class="srow border-t dark:border-slate-700 hover:bg-violet-50/60 dark:hover:bg-slate-700/40 transition" data-t="<?= Helper::e(strtolower(($r['tname']??'').' '.($r['position']??'').' '.($r['tsubject']??''))) ?>">
<td class="p-3 text-slate-400 font-bold"><?= $no++ ?></td>
<td class="p-3 font-semibold"><span class="flex items-center gap-2.5"><?php if(!empty($r['tphoto'])): ?><img src="<?= Helper::upload($r['tphoto']) ?>" alt="<?= Helper::e($r['tname']??'') ?>" data-lightbox loading="lazy" class="w-9 h-9 rounded-full object-cover border cursor-zoom-in"><?php else: ?><span class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-700 border dark:border-slate-600 grid place-items-center text-slate-400"><i class="fa fa-user text-xs"></i></span><?php endif; ?><span class="min-w-0"><span class="block truncate"><?= Helper::e($r['tname']??'-') ?></span><?php if(!empty($r['tedu'])): ?><span class="block text-[11px] font-normal text-slate-400 truncate"><?= Helper::e($r['tedu']) ?></span><?php endif; ?></span></span></td>
<td class="p-3"><span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full bg-violet-100 dark:bg-violet-900 text-violet-700 dark:text-violet-200"><i class="fa fa-id-badge"></i><?= Helper::e($r['position']) ?></span></td>
<td class="p-3 text-xs text-slate-500 hidden md:table-cell"><?= Helper::e($r['tsubject']??'-') ?></td></tr><?php endforeach; ?></table></div>
<p id="strEmpty" class="hidden text-center text-sm text-slate-500 py-8">Tidak cocok. Ubah kata kunci.</p>
</div>
<?php if(!empty($prof['org_chart'])): ?><img id="bagan" src="<?= Helper::upload($prof['org_chart']) ?>" alt="Bagan struktur organisasi" data-lightbox loading="lazy" class="reveal scroll-mt-28 mt-4 rounded-2xl cursor-zoom-in w-full object-contain"><?php endif; ?>
</div>
<script>
(function(){var q=document.getElementById('qStr'),rows=[...document.querySelectorAll('.srow')],em=document.getElementById('strEmpty');q?.addEventListener('input',()=>{var s=(q.value||'').toLowerCase(),n=0;rows.forEach(r=>{var ok=!s||r.dataset.t.includes(s);r.style.display=ok?'':'none';if(ok)n++});if(em)em.classList.toggle('hidden',n>0)})})();
</script>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
