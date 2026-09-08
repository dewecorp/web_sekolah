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
<style>
.kur-content ul{list-style:disc!important;padding-left:1.4rem!important;margin:.6rem 0!important;display:grid;gap:.35rem}
.kur-content ol{list-style:decimal!important;padding-left:1.4rem!important;margin:.6rem 0!important;display:grid;gap:.35rem}
.kur-content li{display:list-item!important}
.kur-content li::marker{color:var(--school-primary,#059669)}
</style>
<div id="isi" class="grid lg:grid-cols-3 gap-4 mt-4 max-w-full items-start scroll-mt-28">
<article class="kur-content lg:col-span-2 bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-2xl p-6 md:p-8 text-sm text-justify leading-relaxed reveal prose max-w-none dark:prose-invert"><?= $content ?></article>
<aside class="grid gap-4 content-start">
<div class="rounded-2xl border dark:border-slate-700 bg-white dark:bg-slate-800 p-5 reveal">
<h2 class="font-extrabold text-sm"><i class="fa fa-layer-group text-emerald-600 mr-1"></i>Komponen utama</h2>
<?php if($compsHtml!==''): ?><div class="kur-content text-xs text-slate-500 mt-2 leading-relaxed prose max-w-none"><?= $compsHtml ?></div><?php elseif(!empty($comps)): ?><ul class="text-xs text-slate-500 mt-2 grid gap-1.5 leading-relaxed list-disc pl-5"><?php foreach($comps as $i=>$cp): $parts=preg_split('/^(.+?)\s+[—–-]\s+(.+)$/u', trim($cp), -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY); if(count($parts)>=2){$t0=trim($parts[0]);$t1=trim($parts[1]);}else{$t0=trim($cp);$t1='';} ?><li><b><?= $i+1 ?>. <?= Helper::e($t0) ?></b><?php if($t1!==''): ?> — <?= Helper::e($t1) ?><?php endif; ?></li><?php endforeach; ?></ul><?php else: ?><p class="text-xs text-slate-400 mt-2">Belum diisi. Kelola via Sekolah &gt; Kurikulum.</p><?php endif; ?>
<a href="<?= Helper::url('ekstrakurikuler') ?>" class="text-emerald-600 text-xs font-bold mt-3 inline-block">Lihat ekstrakurikuler &rarr;</a>
</div>
</aside>
</div>
</div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
