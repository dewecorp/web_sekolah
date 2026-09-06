<?php $metaTitle=$pg["seo_title"]?:$pg["title"]; require ROOT."/templates/frontend/header.php";
$plain=trim(preg_replace('/\s+/', ' ', strip_tags($pg["content"]??''))); $words=$plain!==''?str_word_count($plain):0; $mins=max(1,(int)ceil($words/200)); ?><div class="w-full px-4 md:px-8 py-10"><?php
$heroBadge='<i class="fa fa-file-lines text-amber-300"></i>Halaman';
$heroTitle=$pg["title"];
$heroDesc=!empty($pg["seo_description"])?nl2br(Helper::e($pg["seo_description"])):'';
$heroCrumb='<a href="'.Helper::url().'" class="hover:text-white">Beranda</a> / '.Helper::e($pg["title"]);
$heroTheme='teal';
$heroStats=[['icon'=>'fa-clock','label'=>'± '.$mins.' mnt baca','solid'=>true],['icon'=>'fa-calendar-day','label'=>Helper::tgl($pg['updated_at']??$pg['created_at']??'now'),'solid'=>false]];
require ROOT."/templates/frontend/page-hero.php"; ?><article class="mt-4 bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-2xl p-6 md:p-8 text-slate-700 dark:text-slate-200 text-justify leading-relaxed reveal"><?= $pg["content"] ?></article></div><?php require ROOT."/templates/frontend/footer.php"; ?>
