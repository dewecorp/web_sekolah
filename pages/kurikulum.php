<?php
if((Database::setting('kurikulum_show','1')==='0')){ http_response_code(404); require ROOT.'/templates/error/404.php'; exit; }
$title=Database::setting('kurikulum_title','Kurikulum');
$content=Database::setting('kurikulum_content','<p>Kurikulum Merdeka dengan penguatan karakter, literasi, numerasi, dan keterampilan vokasi.</p><ul class="list-disc ml-5 mt-3 grid gap-1"><li>Intrakurikuler</li><li>Projek Penguatan Profil Pelajar Pancasila</li><li>Ekstrakurikuler</li></ul>');
$metaTitle=$title.' - '.Database::setting('school_name','Sekolah'); require ROOT.'/templates/frontend/header.php';
$plain=trim(preg_replace('/\s+/', ' ', strip_tags($content)));
$words=$plain!==''?str_word_count($plain):0; $mins=max(1,(int)ceil($words/200));
$liCount=substr_count(strtolower($content),'<li');
?>
<div class="w-full px-4 md:px-8 py-10">
<?php
$heroBadge='<i class="fa fa-book-open text-amber-300"></i>Kurikulum Sekolah';
$heroTitle=$title;
$heroDesc='';
$heroCrumb='<a href="'.Helper::url().'" class="hover:text-white">Beranda</a> / Kurikulum';
$heroTheme='emerald';
$heroActions='<a href="#isi" class="inline-flex items-center gap-2 bg-white text-slate-900 px-4 py-2 rounded-xl font-bold"><i class="fa fa-arrow-down text-emerald-600"></i>Baca isi</a>';
$heroStats=[['icon'=>'fa-list-check','label'=>$liCount.' poin','solid'=>false],['icon'=>'fa-clock','label'=>'± '.$mins.' mnt baca','solid'=>false],['icon'=>'fa-school','label'=>Database::setting('school_name','Sekolah'),'solid'=>false]];
require ROOT.'/templates/frontend/page-hero.php'; ?>
<div id="isi" class="grid lg:grid-cols-3 gap-4 mt-4 items-start scroll-mt-28">
<article class="lg:col-span-2 bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-2xl p-6 md:p-8 text-sm text-justify leading-relaxed reveal prose max-w-none dark:prose-invert"><?= $content ?></article>
<aside class="grid gap-4">
<div class="rounded-2xl border dark:border-slate-700 bg-white dark:bg-slate-800 p-5 reveal">
<h2 class="font-extrabold text-sm"><i class="fa fa-layer-group text-emerald-600 mr-1"></i>Komponen utama</h2>
<ul class="text-xs text-slate-500 mt-2 grid gap-1.5 leading-relaxed">
<li><b>1. Intrakurikuler</b> — pembelajaran tatap muka sesuai CP & TP.</li>
<li><b>2. Projek P5</b> — penguatan profil pelajar Pancasila lintas mapel.</li>
<li><b>3. Ekstrakurikuler</b> — minat, bakat, dan karakter di luar jam wajib.</li>
</ul>
<a href="<?= Helper::url('ekstrakurikuler') ?>" class="text-emerald-600 text-xs font-bold mt-3 inline-block">Lihat ekstrakurikuler &rarr;</a>
</div>
<div class="rounded-2xl border dark:border-slate-700 bg-white dark:bg-slate-800 p-5 reveal">
<h2 class="font-extrabold text-sm"><i class="fa fa-circle-question text-emerald-600 mr-1"></i>Butuh info lanjut?</h2>
<p class="text-xs text-slate-500 mt-2 leading-relaxed">Hubungi bagian kurikulum / TU pada jam layanan untuk jadwal, pembagian kelas, dan kegiatan projek.</p>
<a href="<?= Helper::url('kontak') ?>" class="mt-3 inline-flex items-center gap-2 bg-emerald-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl hover:bg-emerald-500"><i class="fa fa-headset"></i>Hubungi kami</a>
</div>
</aside>
</div>
</div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
