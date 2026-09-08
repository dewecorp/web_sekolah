<?php
if((Database::setting('kurikulum_show','1')==='0')){ http_response_code(404); require ROOT.'/templates/error/404.php'; exit; }
$title=Database::setting('kurikulum_title','Kurikulum');
if(trim($title)==='')$title='Kurikulum';
$content=trim((string)Database::setting('kurikulum_content',''));
if($content==='')$content='<p>Kurikulum Merdeka dengan penguatan karakter, literasi, numerasi, dan keterampilan vokasi.</p><ul class="list-disc ml-5 mt-3 grid gap-1"><li>Intrakurikuler</li><li>Projek Penguatan Profil Pelajar Pancasila</li><li>Ekstrakurikuler</li></ul>';
$compsRaw=trim((string)Database::setting('kurikulum_comps',"Intrakurikuler — pembelajaran tatap muka sesuai CP & TP.\nProjek P5 — penguatan profil pelajar Pancasila lintas mapel.\nEkstrakurikuler — minat, bakat, dan karakter di luar jam wajib."));
$compsHtml='';
if($compsRaw!==''&&(str_contains($compsRaw,'<li')||str_contains($compsRaw,'<p')||str_contains($compsRaw,'<ul')||str_contains($compsRaw,'<ol'))){
  $compsHtml=$compsRaw;
} else {
  $comps=array_values(array_filter(array_map('trim',explode("\n",$compsRaw))));
}
$metaTitle=$title.' - '.Database::setting('school_name','Sekolah'); require ROOT.'/templates/frontend/header.php';
$plain=trim(preg_replace('/\s+/', ' ', strip_tags($content)));
$words=$plain!==''?str_word_count($plain):0; $mins=max(1,(int)ceil($words/200));
$liCount=substr_count(strtolower($content),'<li');
?>
<div class="w-full max-w-full px-4 md:px-8 py-10">
<?php
$heroBadge='<i class="fa fa-book-open text-amber-300"></i>Kurikulum Sekolah';
$heroTitle=$title;
$heroDesc='';
$heroCrumb='<a href="'.Helper::url().'" class="hover:text-white">Beranda</a> / Kurikulum';
$heroTheme='emerald';
$heroActions='<a href="#isi" class="inline-flex items-center gap-2 bg-white text-slate-900 px-4 py-2 rounded-xl font-bold"><i class="fa fa-arrow-down text-emerald-600"></i>Baca isi</a>';
$heroStats=[['icon'=>'fa-list-check','label'=>$liCount.' poin','solid'=>false],['icon'=>'fa-clock','label'=>'± '.$mins.' mnt baca','solid'=>false],['icon'=>'fa-school','label'=>Database::setting('school_name','Sekolah'),'solid'=>false]];
require ROOT.'/templates/frontend/page-hero.php'; ?>
<div class="grid md:grid-cols-3 gap-3 mt-4">
<div class="rounded-2xl border bg-white dark:bg-slate-800 p-4 flex items-center gap-3 reveal"><span class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 grid place-items-center"><i class="fa fa-layer-group"></i></span><span><b class="block text-2xl font-extrabold"><?= $liCount ?></b><span class="text-xs text-slate-500">Komponen</span></span><span class="ml-auto text-[11px] font-bold px-2 py-1 rounded-full bg-emerald-100 text-emerald-700">Terstruktur</span></div>
<div class="rounded-2xl border bg-white dark:bg-slate-800 p-4 flex items-center gap-3 reveal"><span class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 grid place-items-center"><i class="fa fa-clock"></i></span><span><b class="block text-2xl font-extrabold"><?= $mins ?> mnt</b><span class="text-xs text-slate-500">Perkiraan baca</span></span></div>
<div class="rounded-2xl border bg-white dark:bg-slate-800 p-4 flex items-center gap-3 reveal"><span class="w-10 h-10 rounded-xl bg-violet-100 text-violet-700 grid place-items-center"><i class="fa fa-award"></i></span><span><b class="block text-sm font-bold leading-tight"><?= Helper::e(Database::setting('school_name','Sekolah')) ?></b><span class="text-xs text-slate-500">Kurikulum</span></span></div>
</div>
<div id="isi" class="mt-4 grid gap-4 max-w-full scroll-mt-28">
<div class="rounded-[1.7rem] bg-white dark:bg-slate-800 border dark:border-slate-700 p-6 md:p-8 reveal relative overflow-hidden">
<span class="absolute left-0 top-6 bottom-6 w-1 rounded-full bg-gradient-to-b from-emerald-500 to-cyan-500"></span>
<p class="text-[11px] font-bold uppercase tracking-[.16em] text-emerald-600"><i class="fa fa-quote-left mr-1"></i>Narasi Kurikulum</p>
<div class="kur-content mt-3 text-sm md:text-[15px] leading-relaxed text-slate-700 dark:text-slate-200"><?= $content ?></div>
</div>
<div class="rounded-[1.7rem] border dark:border-slate-700 bg-white dark:bg-slate-800 p-5 md:p-6 reveal">
<h2 class="font-extrabold flex items-center gap-2 pb-3 mb-2 border-b border-slate-100 dark:border-slate-700"><span class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 text-white grid place-items-center shadow"><i class="fa fa-layer-group text-sm"></i></span>Komponen utama <span class="ml-auto text-[11px] font-bold px-2 py-1 rounded-full bg-emerald-100 text-emerald-700"><?= count($comps ?? []) + ($compsHtml?substr_count(strtolower($compsHtml),'<li'):0) ?> poin</span></h2>
<?php if($compsHtml!==''): ?><div class="vm-list mt-4 text-sm leading-relaxed"><?= $compsHtml ?></div><?php elseif(!empty($comps)): ?><div class="vm-list mt-4"><ol><?php foreach($comps as $cp): $parts=preg_split('/^(.+?)\s+[—–-]\s+(.+)$/u', trim($cp), -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY); if(count($parts)>=2){$t0=trim($parts[0]);$t1=trim($parts[1]);}else{$t0=trim($cp);$t1='';} ?><li><span><b><?= Helper::e($t0) ?></b><?php if($t1!==''): ?><span class="block text-slate-500 mt-0.5"><?= Helper::e($t1) ?></span><?php endif; ?></span></li><?php endforeach; ?></ol></div><?php else: ?><p class="text-xs text-slate-400 mt-2">Belum diisi. Kelola via Sekolah &gt; Kurikulum.</p><?php endif; ?>

</div>
</div>
</div>
<style>
.kur-steps{counter-reset:kp;display:grid;gap:.85rem;margin:.2rem 0!important;padding:0!important;list-style:none!important}
.kur-steps li{counter-increment:kp;position:relative;background:#f8fafc;border:1px solid #e2e8f0;border-radius:1rem;padding:.9rem .95rem .9rem 3.4rem;transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease}
.kur-steps li:hover{transform:translateY(-3px);box-shadow:0 10px 26px rgba(15,23,42,.12);border-color:color-mix(in srgb,var(--school-primary,#059669),white 55%)}
.kur-steps li:hover::before{transform:scale(1.12) rotate(4deg)}
.kur-steps li::before{content:counter(kp,decimal-leading-zero);position:absolute;left:.8rem;top:.85rem;width:1.8rem;height:1.8rem;border-radius:.7rem;display:grid;place-items:center;font-size:.7rem;font-weight:800;color:#fff;background:linear-gradient(135deg,var(--school-primary,#059669),#0ea5e9);transition:transform .25s ease}
.kur-steps li b{display:block;font-size:.85rem}
.kur-steps li span{display:block;font-size:.75rem;color:#64748b;margin-top:.15rem}
.dark .kur-steps li{background:#1e293b;border-color:#334155}
.kur-content p{margin:.5rem 0}
.kur-content ul,.kur-content ol{margin:.6rem 0!important;padding-left:1.4rem!important;display:grid!important;gap:.35rem!important}
.kur-content ul{list-style:disc outside!important}
.kur-content ol{list-style:decimal outside!important}
.kur-content li{display:list-item!important}
.kur-content li::marker{color:var(--school-primary,#059669);font-weight:800}
</style>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
