<?php $t=$db->query("SELECT * FROM teachers WHERE is_active=1 ORDER BY sort_order")->fetchAll(); $metaTitle='Guru & Staff - '.Database::setting('school_name','Sekolah'); require ROOT."/templates/frontend/header.php"; ?>
<div class="w-full px-4 md:px-8 py-10">
<?php
$heroBadge='<i class="fa fa-chalkboard-user text-amber-300"></i>Guru & Staff';
$heroTitle='Guru & Staff';
$heroDesc='';
$heroCrumb='<a href="'.Helper::url().'" class="hover:text-white">Beranda</a> / Guru';
$heroTheme='emerald';
$heroStats=[['icon'=>'fa-users','label'=>count($t).' orang','solid'=>true],['icon'=>'fa-user-tie','label'=>count(array_filter($t,fn($x)=>($x['type']??'')==='guru')).' guru','solid'=>false],['icon'=>'fa-briefcase','label'=>count(array_filter($t,fn($x)=>($x['type']??'')==='tendik')).' tendik','solid'=>false]];
require ROOT."/templates/frontend/page-hero.php"; ?>
<?php if(!$t): ?><div class="bg-white border rounded-2xl p-10 text-center mt-4 text-slate-500">Belum ada data guru</div><?php else: ?><div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4 mt-4"><?php foreach($t as $g): ?><div class="bg-white dark:bg-slate-800 border rounded-2xl overflow-hidden text-center p-4 card-hover reveal"><div class="w-20 h-20 mx-auto rounded-full bg-emerald-100 grid place-items-center overflow-hidden"><?php if(!empty($g["photo"])): ?><img src="<?= Helper::upload($g["photo"]) ?>" alt="<?= Helper::e($g["name"]) ?>" class="w-full h-full object-cover" loading="lazy"><?php else: ?><i class="fa fa-user text-2xl text-emerald-600"></i><?php endif; ?></div><p class="font-bold mt-2"><?= Helper::e($g["name"]) ?></p><p class="text-xs text-emerald-700"><?= Helper::e($g["position"]) ?></p><p class="text-xs text-slate-500"><?= Helper::e($g["subject"]??"") ?></p></div><?php endforeach; ?></div><?php endif; ?></div><?php require ROOT."/templates/frontend/footer.php"; ?>
