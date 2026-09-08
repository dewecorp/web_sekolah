<?php
$all = $db->query("SELECT * FROM announcements WHERE status='published' ORDER BY published_at DESC LIMIT 100")->fetchAll();
$latest = $all[0] ?? null;
$withFile = count(array_filter($all, fn($x) => !empty($x['attachment'])));
$metaTitle = 'Pengumuman - ' . Database::setting('school_name', 'Sekolah');
require ROOT . '/templates/frontend/header.php'; ?>
<div class="relative overflow-hidden bg-slate-950 text-white">
<div class="absolute -top-24 right-0 w-96 h-96 bg-sky-500/25 rounded-full blur-3xl"></div>
<div class="absolute bottom-0 -left-24 w-[26rem] h-[26rem] bg-indigo-500/20 rounded-full blur-3xl"></div>
<div class="absolute inset-0 opacity-[0.15]" style="background-image:linear-gradient(#fff1 1px,transparent 1px),linear-gradient(90deg,#fff1 1px,transparent 1px);background-size:44px 44px;mask-image:radial-gradient(ellipse 80% 70% at 50% 30%,#000 60%,transparent 100%)"></div>
<div class="relative max-w-7xl mx-auto px-4 pt-12 pb-10">
<span class="inline-flex items-center gap-1.5 text-[11px] font-bold bg-white/10 border border-white/15 rounded-full px-3 py-1"><a href="<?= Helper::url() ?>" class="hover:text-white text-sky-200">Beranda</a><span class="opacity-50">/</span>Pengumuman</span>
<h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mt-3">Pengu<span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-300 to-indigo-200">muman</span></h1>
<p class="text-slate-300 mt-2 max-w-2xl">Informasi resmi sekolah. Terbaru di atas, lengkap dengan tanggal dan lampiran.</p>
<div class="grid grid-cols-3 max-w-lg gap-2.5 mt-6">
<div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur p-3 text-center"><p class="text-2xl font-extrabold"><?= count($all) ?></p><p class="text-[11px] text-sky-200">Total</p></div>
<div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur p-3 text-center"><p class="text-2xl font-extrabold"><?= $withFile ?></p><p class="text-[11px] text-sky-200">Lampiran</p></div>
<div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur p-3 text-center"><p class="text-2xl font-extrabold"><?= $latest ? '1' : '0' ?></p><p class="text-[11px] text-sky-200">Terbaru</p></div>
</div></div>
<?php if ($latest): $ldt = $latest['published_at'] ?? $latest['created_at']; $lfile = !empty($latest['attachment']); ?>
<div class="relative max-w-7xl mx-auto px-4 pb-10 -mt-2">
<div class="rounded-3xl p-[1px] bg-gradient-to-r from-sky-400 via-indigo-300 to-sky-400 reveal">
<div class="rounded-3xl bg-slate-900/95 backdrop-blur p-5 md:p-6 flex flex-col md:flex-row gap-4">
<span class="w-12 h-12 rounded-2xl bg-gradient-to-b from-amber-400 to-orange-500 grid place-items-center text-xl shrink-0 shadow-lg">📢</span>
<div class="flex-1 min-w-0">
<p class="text-[11px] font-extrabold text-amber-300 tracking-widest uppercase">Sorotan • <?= Helper::e(Helper::ago($ldt)) ?></p>
<h2 class="text-xl md:text-2xl font-extrabold mt-1"><?= Helper::e($latest['title']) ?></h2>
<p class="text-sm text-slate-300 mt-1.5 line-clamp-2"><?= Helper::e(Helper::excerpt($latest['content'], 180)) ?></p>
<div class="flex flex-wrap gap-2 mt-3">
<button data-goto="<?= md5($latest['title']) ?>" class="text-xs font-bold bg-white text-slate-900 px-3.5 py-2 rounded-xl hover:bg-sky-100">Baca Selengkapnya</button>
<?php if ($lfile): $href = str_starts_with($latest['attachment'], 'http') ? $latest['attachment'] : Helper::url(ltrim($latest['attachment'], '/')); ?>
<a href="<?= Helper::e($href) ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-bold border border-white/25 px-3.5 py-2 rounded-xl hover:bg-white/10"><i class="fa fa-download mr-1"></i>Unduh Lampiran</a>
<?php endif; ?>
</div></div></div></div></div>
<?php endif; ?>
</div>
<div class="max-w-7xl mx-auto px-4 py-8">
<div class="relative mb-5 reveal"><i class="fa fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i><input id="q" placeholder="Cari pengumuman..." class="w-full border dark:border-slate-700 rounded-2xl pl-10 pr-3 py-2.5 text-sm bg-white dark:bg-slate-800 shadow-sm focus:ring-2 focus:ring-sky-500 focus:outline-none"></div>
<?php if (!$all): ?>
<div class="bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-3xl p-14 text-center reveal"><span class="w-16 h-16 rounded-3xl bg-sky-100 dark:bg-sky-900 grid place-items-center mx-auto text-3xl">📢</span><p class="font-extrabold text-lg mt-4">Belum ada pengumuman</p><p class="text-sm text-slate-500">Pengumuman baru tampil di sini setelah admin publish.</p></div>
<?php else: ?>
<div class="relative pl-9" id="plist">
<div class="absolute left-[13px] top-2 bottom-2 w-0.5 bg-gradient-to-b from-sky-400 via-indigo-400 to-emerald-400 rounded-full"></div>
<?php foreach ($all as $i => $x): $dt = $x['published_at'] ?? $x['created_at']; $hasFile = !empty($x['attachment']); ?>
<article id="p-<?= md5($x['title']) ?>" data-t="<?= Helper::e(strtolower($x['title'] . ' ' . strip_tags($x['content']))) ?>" class="pitem relative pb-4 last:pb-0 reveal scroll-mt-32">
<span class="absolute -left-9 top-0 w-7 h-7 rounded-full <?= $i === 0 ? 'bg-gradient-to-b from-amber-400 to-orange-500' : 'bg-gradient-to-b from-sky-500 to-indigo-600' ?> text-white grid place-items-center ring-4 ring-slate-50 dark:ring-slate-950 text-[11px] shadow"><i class="fa <?= $i === 0 ? 'fa-star' : 'fa-bullhorn' ?>"></i></span>
<div class="bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-3xl p-4 sm:p-5 hover:shadow-xl hover:-translate-y-0.5 hover:border-sky-300 dark:hover:border-sky-700 transition-all">
<div class="flex flex-wrap items-center gap-1.5">
<h2 class="font-extrabold text-[15px] flex-1 min-w-[160px]"><?= Helper::e($x['title']) ?></h2>
<?php if ($i === 0): ?><span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-400 text-slate-900">TERBARU</span><?php endif; ?>
<?php if ($hasFile): ?><span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-sky-100 dark:bg-sky-900 text-sky-700 dark:text-sky-200"><i class="fa fa-paperclip mr-0.5"></i>Lampiran</span><?php endif; ?>
</div>
<p class="text-[11px] text-slate-400 mt-1.5 flex flex-wrap gap-x-3"><span><i class="fa fa-clock mr-1"></i><?= Helper::e(Helper::ago($dt)) ?></span><span><i class="fa fa-calendar-day mr-1"></i><?= Helper::e(Helper::tgl($dt)) ?></span></p>
<div class="ann-content text-sm text-slate-600 dark:text-slate-300 mt-2 leading-relaxed text-justify"><?= $x['content'] ?></div>
<?php if ($hasFile): $href = str_starts_with($x['attachment'], 'http') ? $x['attachment'] : Helper::url(ltrim($x['attachment'], '/')); ?>
<a href="<?= Helper::e($href) ?>" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-white bg-sky-600 hover:bg-sky-500 px-3.5 py-2 rounded-xl shadow"><i class="fa fa-download"></i>Unduh Lampiran</a>
<?php endif; ?>
</div></article>
<?php endforeach; ?>
</div>
<p id="empty" class="hidden text-center py-12"><span class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 grid place-items-center mx-auto text-2xl">🔍</span><span class="block font-bold mt-3">Tidak cocok.</span><span class="text-sm text-slate-500">Ubah kata kunci.</span></p>
<?php endif; ?></div>
<script>
(function(){
  var q=document.getElementById('q'),items=[...document.querySelectorAll('.pitem')],empty=document.getElementById('empty');
  q?.addEventListener('input',function(){
    var s=(q.value||'').toLowerCase(),n=0;
    items.forEach(function(el){var ok=!s||el.dataset.t.includes(s);el.style.display=ok?'':'none';if(ok)n++});
    if(empty)empty.classList.toggle('hidden',n>0);
  });
  document.querySelectorAll('[data-goto]').forEach(function(b){b.addEventListener('click',function(){
    var t=document.getElementById('p-'+b.dataset.goto);if(t){t.scrollIntoView({behavior:'smooth',block:'center'});t.classList.add('ring-2','ring-amber-400','rounded-3xl');setTimeout(function(){t.classList.remove('ring-2','ring-amber-400','rounded-3xl')},2000)}
  })});
})();
</script>
<?php require ROOT . '/templates/frontend/footer.php'; ?>
