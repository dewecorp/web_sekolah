</main>
<?php $ft = Database::setting('footer_text','SMK Nusantara'); $pw = Database::setting('powered_by','Powered by SchoolCMS');
try { $fm = Database::conn()->query("SELECT * FROM menus WHERE location='footer' LIMIT 1")->fetch(); $fitems = [];
if ($fm) { $fs = Database::conn()->prepare("SELECT * FROM menu_items WHERE menu_id=? AND is_active=1 ORDER BY sort_order"); $fs->execute([$fm['id']]); $fitems = $fs->fetchAll(); } } catch (Throwable) { $fitems = []; }
if (!$fitems) $fitems = [['label'=>'Profil','url'=>'/profil'],['label'=>'Berita','url'=>'/berita'],['label'=>'Galeri','url'=>'/galeri'],['label'=>'Kontak','url'=>'/kontak']]; ?>
<footer class="bg-slate-900 text-slate-300 mt-16">
<div class="max-w-7xl mx-auto px-4 py-12 grid md:grid-cols-4 gap-8 text-sm">
<div><h4 class="font-bold text-white mb-2">Tentang</h4><p><?= Helper::e(Database::setting('school_name','SMK Nusantara')) ?> - <?= Helper::e(Database::setting('tagline','')) ?></p><p class="mt-2"><?= Helper::e(Database::setting('address','')) ?></p></div>
<div><h4 class="font-bold text-white mb-2">Navigasi</h4><?php foreach($fitems as $fi): [$fhref,$ftgt]=Helper::menuUrl($fi['url']); ?><a href="<?= Helper::e($fhref) ?>"<?= $ftgt==='_blank'?' target="_blank" rel="noopener noreferrer"':'' ?> class="block py-1"><?= Helper::e($fi['label']) ?></a><?php endforeach; ?></div>
<div><h4 class="font-bold text-white mb-2">Kontak</h4><p><?= Helper::e(Database::setting('phone','')) ?></p><p><?= Helper::e(Database::setting('email','')) ?></p></div>
<div><h4 class="font-bold text-white mb-2">Ikuti</h4><div class="flex gap-2"><a href="#" class="w-9 h-9 rounded bg-white/10 grid place-items-center"><i class="fab fa-facebook"></i></a><a href="#" class="w-9 h-9 rounded bg-white/10 grid place-items-center"><i class="fab fa-instagram"></i></a><a href="#" class="w-9 h-9 rounded bg-white/10 grid place-items-center"><i class="fab fa-youtube"></i></a></div></div>
</div>
<div class="border-t border-white/10"><div class="max-w-7xl mx-auto px-4 py-4 flex flex-col md:flex-row justify-between text-xs gap-2">
<span>&copy; <?= date('Y') ?> <?= Helper::e($ft) ?>. <?= Helper::e($pw) ?>.</span>
<a href="<?= Helper::url('admin/login') ?>" target="_blank" rel="noopener noreferrer" class="hover:text-white">Login Admin</a>
</div></div>
</footer>
<button id="toTop" aria-label="Kembali ke atas" class="fixed bottom-5 right-5 z-50 w-11 h-11 rounded-full bg-emerald-600 text-white shadow-lg grid place-items-center opacity-0 invisible translate-y-3 transition-all duration-300 hover:bg-emerald-500"><i class="fa fa-arrow-up"></i></button>
<script src="<?= Helper::asset('js/app.js') ?>"></script>
</body></html>
