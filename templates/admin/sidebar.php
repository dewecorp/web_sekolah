<aside id="sidebar" class="hidden md:flex w-60 shrink-0 bg-gradient-to-b from-emerald-800 via-emerald-700 to-emerald-800 text-emerald-50 text-sm flex-col shadow-lg" style="margin-top:0!important">
<?php $role = $_SESSION['user']['role'] ?? 'author'; $u = Auth::user(); $cur = Router::uri(); ?>
<?php $link = function($p, $ic, $lb) use ($cur) { $t = trim($p, '/'); $a = $t === 'admin' ? $cur === '/admin' : str_starts_with($cur, '/' . $t); return '<a href="' . Helper::url($p) . '" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition ' . ($a ? 'bg-white text-emerald-800 font-bold shadow' : 'text-emerald-50/90 hover:bg-white/15 hover:text-white') . '"><i class="fa ' . $ic . ' w-4 text-center"></i>' . $lb . '</a>'; }; ?>
<nav id="sideNav" class="grid gap-1 p-3 flex-1 overflow-y-auto overscroll-contain content-start">
<?= $link('admin', 'fa-gauge', 'Dashboard') ?>
<p class="px-3 mt-3 text-[11px] font-bold uppercase tracking-wide text-emerald-200/70">Konten</p>
<?= $link('admin/pages', 'fa-file-lines', 'Halaman') ?>
<?= $link('admin/posts', 'fa-newspaper', 'Berita') ?>
<?php if($role!=='author'): ?>
<?= $link('admin/categories', 'fa-tags', 'Kategori') ?>
<?php endif; ?>
<?= $link('admin/media', 'fa-photo-film', 'Media') ?>
<?= $link('admin/gallery', 'fa-images', 'Galeri') ?>
<?php if($role!=='author'): ?>
<?= $link('admin/announcements', 'fa-bullhorn', 'Pengumuman') ?>
<?= $link('admin/agenda', 'fa-calendar-days', 'Agenda') ?>
<p class="px-3 mt-3 text-[11px] font-bold uppercase tracking-wide text-emerald-200/70">Sekolah</p>
<?= $link('admin/teachers', 'fa-chalkboard-user', 'Guru & Staff') ?>
<?= $link('admin/students', 'fa-user-graduate', 'Data Siswa') ?>
<?= $link('admin/sarana', 'fa-building-columns', 'Sarana') ?>
<?= $link('admin/sarana-pembelajaran', 'fa-chalkboard', 'Sarana Belajar') ?>
 <?= $link('admin/ekskul', 'fa-futbol', 'Ekstrakurikuler') ?>
<?= $link('admin/prestasi', 'fa-trophy', 'Prestasi') ?>
<?= $link('admin/structure', 'fa-sitemap', 'Struktur') ?>
<?= $link('admin/curriculum', 'fa-book-open', 'Kurikulum') ?>
<?= $link('admin/vision', 'fa-bullseye', 'Visi, Misi & Tujuan') ?>
<?= $link('admin/statistics', 'fa-chart-simple', 'Statistik') ?>
<p class="px-3 mt-3 text-[11px] font-bold uppercase tracking-wide text-emerald-200/70">Tampilan</p>
<?= $link('admin/menus', 'fa-list-ul', 'Menu') ?>
<?= $link('admin/megamenu', 'fa-layer-group', 'Mega Menu') ?>
<?= $link('admin/widgets', 'fa-puzzle-piece', 'Widget Sidebar') ?>
<?= $link('admin/footer', 'fa-shoe-prints', 'Footer') ?>
<?= $link('admin/sections', 'fa-table-columns', 'Section & Slider') ?>
<?= $link('admin/themes', 'fa-palette', 'Tema & Warna') ?>
<?= $link('admin/appearance', 'fa-display', 'Pengaturan Tampilan') ?>
<?php endif; ?>
<?php if($role==='administrator'): ?>
<p class="px-3 mt-3 text-[11px] font-bold uppercase tracking-wide text-emerald-200/70">Pengaturan</p>
<?= $link('admin/settings', 'fa-school', 'Identitas') ?>
<?= $link('admin/seo', 'fa-magnifying-glass', 'SEO') ?>
<?= $link('admin/users', 'fa-users', 'User') ?>
<?= $link('admin/logs', 'fa-database', 'Backup & Restore') ?>
<?php endif; ?>
</nav>
<div class="p-3 border-t border-white/15 bg-emerald-900/40">
<div class="flex items-center gap-2 px-1 mb-2 min-w-0"><span class="w-8 h-8 rounded-full bg-white text-emerald-800 grid place-items-center shrink-0 font-bold"><?= Helper::e(mb_substr($u['name'] ?? 'A', 0, 1)) ?></span><span class="min-w-0 flex-1"><b class="text-xs block truncate text-white"><?= Helper::e($u['name'] ?? '') ?></b><span class="text-[11px] text-emerald-200/70"><?= Helper::e($u['role'] ?? '') ?></span></span></div>
<form method="post" action="<?= Helper::url('admin/logout') ?>" data-confirm-logout><?= Security::csrfField() ?><button class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-red-600 hover:bg-red-500 text-white text-sm font-bold"><i class="fa fa-right-from-bracket"></i>Logout</button></form>
</div>
</aside>
<script>
(function(){
  var nav=document.getElementById('sideNav'),side=document.getElementById('sidebar'),top=document.getElementById('adminTopbar');
  // kunci rapat: ikuti tinggi navbar aktual, tanpa celah putih
  function fit(){if(!side||!top)return;var h=Math.round(top.getBoundingClientRect().height);document.documentElement.style.setProperty('--adminbar',h+'px');side.style.top=h+'px';side.style.height='calc(100vh - '+h+'px)';document.body.style.paddingTop=h+'px'}
  fit();window.addEventListener('resize',fit);setTimeout(fit,300);
  if(!nav)return;
  // pulihkan posisi scroll terakhir agar menu bawah tidak lompat ke atas
  try{var s=sessionStorage.getItem('sideScroll');if(s!==null)nav.scrollTop=parseInt(s,10)||0}catch(e){}
  nav.addEventListener('scroll',function(){try{sessionStorage.setItem('sideScroll',nav.scrollTop)}catch(e){}},{passive:true});
  // pastikan menu aktif terlihat di dalam nav SAJA (tanpa geser halaman)
  var a=nav.querySelector('a.bg-white');
  if(a){var r=a.getBoundingClientRect(),nr=nav.getBoundingClientRect();if(r.top<nr.top||r.bottom>nr.bottom){try{nav.scrollTop+=r.top-nr.top-(nr.height-r.height)/2}catch(e){}}}
})();
</script>
