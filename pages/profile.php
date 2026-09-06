<?php
declare(strict_types=1);
$profile = $db->query("SELECT * FROM school_profile LIMIT 1")->fetch() ?: [];
$metaTitle = 'Profil Sekolah - ' . Database::setting('school_name','Sekolah');
require ROOT.'/templates/frontend/header.php';
?>
<div class="max-w-7xl mx-auto px-4 py-10">
<nav class="text-xs text-slate-500 mb-4"><a href="<?= Helper::url() ?>">Beranda</a> / Profil</nav>
<h1 class="text-3xl font-extrabold reveal">Profil Sekolah</h1>
<div class="grid md:grid-cols-2 gap-6 mt-6">
<div class="bg-white dark:bg-slate-800 border rounded-2xl p-6 reveal"><h2 class="font-bold mb-2">Visi</h2><p class="text-sm text-slate-600 dark:text-slate-300 text-justify leading-relaxed"><?= nl2br(Helper::e($profile['vision']??'Belum diisi.')) ?></p></div>
<div class="bg-white dark:bg-slate-800 border rounded-2xl p-6 reveal"><h2 class="font-bold mb-2">Misi</h2><p class="text-sm text-slate-600 dark:text-slate-300 text-justify leading-relaxed"><?= nl2br(Helper::e($profile['mission']??'Belum diisi.')) ?></p></div>
</div>
<div class="bg-white dark:bg-slate-800 border rounded-2xl p-6 mt-6 reveal"><h2 class="font-bold mb-2">Sejarah</h2><p class="text-sm text-slate-600 dark:text-slate-300 text-justify leading-relaxed"><?= nl2br(Helper::e($profile['history']??'Belum diisi.')) ?></p></div>
</div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
