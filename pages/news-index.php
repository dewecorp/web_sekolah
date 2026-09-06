<?php
$metaTitle='Indeks Berita - '.Database::setting('school_name','Sekolah');
require ROOT.'/templates/frontend/header.php';
try{
  $years=$db->query("SELECT YEAR(COALESCE(p.published_at,p.created_at)) yr, COUNT(*) total, COALESCE(SUM(p.views),0) views FROM posts p WHERE p.status='published' AND p.deleted_at IS NULL GROUP BY yr ORDER BY yr DESC")->fetchAll();
}catch(Throwable){ $years=[]; }
$byYear=[];
try{
  $st=$db->prepare("SELECT p.*,c.name cat FROM posts p LEFT JOIN categories c ON c.id=p.category_id WHERE p.status='published' AND p.deleted_at IS NULL AND YEAR(COALESCE(p.published_at,p.created_at))=? ORDER BY p.published_at DESC, p.id DESC LIMIT 100");
  foreach($years as $y){ $st->execute([(int)$y['yr']]); $byYear[$y['yr']]=$st->fetchAll(); }
}catch(Throwable){}
$totalAll=array_sum(array_map(fn($y)=>(int)$y['total'],$years));
$topYear=$years[0]??null;
?>
<div class="w-full px-4 md:px-8 py-10">
<?php
$heroBadge='<i class="fa fa-layer-group text-amber-300"></i>Indeks Berita';
$heroTitle='Indeks Berita';
$heroDesc='';
$heroCrumb='<a href="'.Helper::url().'" class="hover:text-white">Beranda</a> / <a href="'.Helper::url('berita').'" class="hover:text-white">Berita</a> / Indeks';
$heroTheme='sky';
$heroStats=[['icon'=>'fa-newspaper','label'=>$totalAll.' berita','solid'=>true],['icon'=>'fa-calendar-days','label'=>count($years).' tahun','solid'=>false],['icon'=>'fa-fire','label'=>$topYear?('Teraktif '.$topYear['yr'].' ('.$topYear['total'].')'):'Belum ada data','solid'=>false]];
require ROOT.'/templates/frontend/page-hero.php'; ?>
<?php if(!$years): ?>
<div class="bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-2xl p-12 text-center mt-4 text-slate-500 reveal">Belum ada berita terbit.</div>
<?php else: ?>
<div class="flex flex-wrap gap-2 mt-4 reveal">
<?php foreach($years as $y): ?><a href="#th-<?= $y['yr'] ?>" class="inline-flex items-center gap-2 border dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 rounded-xl text-sm font-bold hover:border-sky-400 hover:text-sky-600"><i class="fa fa-calendar text-sky-500"></i><?= $y['yr'] ?><span class="text-[11px] px-2 py-0.5 rounded-full bg-sky-100 dark:bg-sky-900 text-sky-700 dark:text-sky-200"><?= $y['total'] ?></span></a><?php endforeach; ?>
<a href="<?= Helper::url('berita') ?>" class="ml-auto inline-flex items-center gap-2 text-sm font-bold text-emerald-600">Kembali ke Berita <i class="fa fa-arrow-right text-xs"></i></a>
</div>
<?php foreach($years as $y): $yr=$y['yr']; $rows=$byYear[$yr]??[]; ?>
<section id="th-<?= $yr ?>" class="mt-8 scroll-mt-28 reveal">
<div class="relative overflow-hidden rounded-2xl bg-slate-950 text-white p-5 md:p-6 flex flex-col md:flex-row md:items-center gap-4">
<span class="absolute -right-10 -top-12 w-40 h-40 rounded-full border-[16px] border-white/10"></span>
<div class="flex items-center gap-4 min-w-0">
<span class="text-4xl md:text-5xl font-extrabold tracking-tight"><?= $yr ?></span>
<span class="min-w-0"><span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full bg-sky-500 text-white"><i class="fa fa-newspaper"></i><?= $y['total'] ?> berita</span>
<span class="block text-xs text-slate-300 mt-1.5"><i class="fa fa-eye mr-1"></i><?= number_format((int)$y['views']) ?> dibaca</span></span>
</div>
<div class="relative md:ml-auto flex gap-2 text-xs">
<input placeholder="Cari di <?= $yr ?>..." data-filter-year="<?= $yr ?>" class="bg-white/10 border border-white/20 rounded-xl px-3 py-2 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-400 w-full md:w-56">
</div>
</div>
<div class="divide-y divide-slate-200 dark:divide-slate-700 border-y dark:border-slate-700 mt-4 bg-white dark:bg-slate-800 rounded-2xl border dark:border-slate-700 overflow-hidden">
<?php foreach($rows as $p): ?>
<a data-year="<?= $yr ?>" data-t="<?= Helper::e(strtolower($p['title'].' '.($p['cat']??''))) ?>" href="<?= Helper::url('berita/'.$p['slug']) ?>" class="nitem flex items-baseline gap-3 px-4 py-3.5 group hover:bg-slate-50 dark:hover:bg-slate-700/40 transition">
<span class="text-xs font-mono text-slate-400 shrink-0 w-28"><?= Helper::tgl($p['published_at']??$p['created_at']) ?></span>
<span class="font-bold flex-1 min-w-0 truncate group-hover:text-sky-600 transition-colors"><?= Helper::e($p['title']) ?></span>
<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 shrink-0 hidden sm:block"><?= Helper::e($p['cat']??'Berita') ?></span>
<span class="text-sky-600 font-bold group-hover:translate-x-1 transition-transform shrink-0">→</span></a>
<?php endforeach; ?>
</div>
</section>
<?php endforeach; ?>
<?php endif; ?>
</div>
<script>
(function(){
  document.querySelectorAll('[data-filter-year]').forEach(q=>{
    q.addEventListener('input',()=>{
      const yr=q.dataset.filterYear, s=(q.value||'').toLowerCase();
      document.querySelectorAll('.nitem[data-year="'+yr+'"]').forEach(el=>{
        el.style.display=!s||el.dataset.t.includes(s)?'':'none';
      });
    });
  });
})();
</script>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
