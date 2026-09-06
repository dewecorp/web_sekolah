<?php $prof=$db->query("SELECT org_chart FROM school_profile LIMIT 1")->fetch(); $metaTitle='Struktur Organisasi - '.Database::setting('school_name','Sekolah'); require ROOT.'/templates/frontend/header.php'; ?>
<div class="max-w-4xl mx-auto px-4 py-10"><nav class="text-xs text-slate-500 mb-3"><a href="<?= Helper::url() ?>">Beranda</a> / Struktur</nav><h1 class="text-3xl font-extrabold reveal">Struktur Organisasi</h1>
<div class="bg-white dark:bg-slate-800 border rounded-2xl p-6 mt-6 text-center reveal">
<?php if(!empty($prof['org_chart'])): ?><img src="<?= Helper::upload($prof['org_chart']) ?>" alt="Struktur Organisasi" data-lightbox class="mx-auto rounded-xl cursor-zoom-in" loading="lazy"><?php else: ?><p class="text-sm text-slate-500">Bagan struktur belum diunggah. Kelola via Pengaturan.</p><?php endif; ?>
</div></div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
