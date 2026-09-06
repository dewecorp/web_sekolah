<?php http_response_code(404); require ROOT.'/templates/frontend/header.php'; ?>
<div class="max-w-xl mx-auto text-center py-20 px-4"><h1 class="text-6xl font-extrabold text-emerald-600">404</h1><p class="mt-2 font-bold">Halaman tidak ditemukan</p><a href="<?= Helper::url() ?>" class="mt-4 inline-block bg-emerald-600 text-white px-5 py-2 rounded-lg">Beranda</a></div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
