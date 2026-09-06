<?php
declare(strict_types=1);
$profile = $db->query("SELECT * FROM school_profile LIMIT 1")->fetch() ?: [];
$metaTitle = 'Profil Sekolah - ' . Database::setting('school_name','Sekolah');
require ROOT.'/templates/frontend/header.php';
?>
<div class="w-full px-4 md:px-8 py-10">
<?php
$heroBadge='<i class="fa fa-school text-amber-300"></i>Profil Sekolah';
$heroTitle='Profil Sekolah';
$heroDesc=($tg=Database::setting('tagline',''))!==''?nl2br(Helper::e($tg)):'';
$heroCrumb='<a href="'.Helper::url().'" class="hover:text-white">Beranda</a> / Profil';
$heroTheme='emerald';
try{ $pstats=$db->query("SELECT * FROM statistics WHERE is_active=1 ORDER BY sort_order,id LIMIT 3")->fetchAll(); }catch(Throwable){ $pstats=[]; }
$heroStats=[]; foreach($pstats as $ps){ $heroStats[]=['icon'=>$ps['icon']??'fa-chart-simple','label'=>number_format((int)$ps['value']).($ps['suffix']??'').' '.($ps['name']??''),'solid'=>count($heroStats)===0]; }
require ROOT.'/templates/frontend/page-hero.php'; ?>
<div class="bg-white dark:bg-slate-800 border rounded-2xl p-6 mt-6 reveal min-w-0 max-w-full overflow-hidden"><h2 class="font-bold mb-2">Sejarah</h2><p class="text-sm text-slate-600 dark:text-slate-300 text-justify leading-relaxed break-words"><?= nl2br(Helper::e($profile['history']??'Belum diisi.')) ?></p></div>
<div class="bg-white dark:bg-slate-800 border rounded-2xl p-6 mt-6 reveal flex flex-col sm:flex-row sm:items-center gap-3 min-w-0 max-w-full overflow-hidden"><div class="min-w-0"><h2 class="font-bold">Visi, Misi & Tujuan</h2><p class="text-sm text-slate-500">Lihat halaman khusus visi, misi, dan tujuan sekolah.</p></div><a href="<?= Helper::url('visi-misi') ?>" class="sm:ml-auto shrink-0 inline-flex items-center gap-2 bg-emerald-600 text-white text-sm font-bold px-4 py-2.5 rounded-xl hover:bg-emerald-500">Lihat Visi Misi <i class="fa fa-arrow-right text-xs"></i></a></div>
</div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
