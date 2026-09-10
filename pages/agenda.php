<?php
$all = $db->query("SELECT * FROM agenda WHERE status='published' ORDER BY event_date ASC LIMIT 100")->fetchAll();
$today = date('Y-m-d');
$endOf = fn($x) => ($x['end_date']??'')!=='' ? $x['end_date'] : ($x['event_date']??'');
$up = array_values(array_filter($all, fn($x) => $endOf($x) >= $today));
$monthFull = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
$monthShort = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
$byMonth = [];
foreach ($all as $x) { $k = substr($x['event_date'], 0, 7); $byMonth[$k][] = $x; }
ksort($byMonth);
$next = $up[0] ?? null;
$monthCount = 0; foreach ($up as $x) if (substr($x['event_date'], 0, 7) === date('Y-m')) $monthCount++;
$metaTitle = 'Agenda - ' . Database::setting('school_name', 'Sekolah');
require ROOT . '/templates/frontend/header.php'; ?>
<div class="relative overflow-hidden bg-slate-950 text-white">
<div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-500/30 rounded-full blur-3xl"></div>
<div class="absolute top-10 right-0 w-[28rem] h-[28rem] bg-teal-400/20 rounded-full blur-3xl"></div>
<div class="absolute inset-0 opacity-[0.15]" style="background-image:linear-gradient(#fff1 1px,transparent 1px),linear-gradient(90deg,#fff1 1px,transparent 1px);background-size:44px 44px;mask-image:radial-gradient(ellipse 80% 70% at 50% 30%,#000 60%,transparent 100%)"></div>
<div class="relative max-w-7xl mx-auto px-4 pt-12 pb-10">
<span class="inline-flex items-center gap-1.5 text-[11px] font-bold bg-white/10 border border-white/15 rounded-full px-3 py-1"><a href="<?= Helper::url() ?>" class="hover:text-white text-emerald-200">Beranda</a><span class="opacity-50">/</span>Agenda</span>
<h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mt-3">Agenda <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-teal-200">Sekolah</span></h1>
<p class="text-slate-300 mt-2 max-w-2xl">Semua kegiatan terkurasi rapi. Jangan lewatkan momen penting sekolah.</p>
<div class="grid grid-cols-3 max-w-lg gap-2.5 mt-6">
<div class="rounded-2xl bg-white text-slate-900 border border-white/15 backdrop-blur p-3 text-center"><p class="text-sm font-extrabold"><i class="fa fa-calendar-day mr-1"></i><?= Helper::pageDate('agenda','event_date') ?></p><p class="text-[11px] opacity-70">Diperbarui</p></div>
<div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur p-3 text-center"><p class="text-2xl font-extrabold"><?= count($up) ?></p><p class="text-[11px] text-emerald-200">Mendatang</p></div>
<div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur p-3 text-center"><p class="text-2xl font-extrabold"><?= $monthCount ?></p><p class="text-[11px] text-emerald-200">Bulan ini</p></div>
<div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur p-3 text-center"><p class="text-2xl font-extrabold"><?= count($all) ?></p><p class="text-[11px] text-emerald-200">Total</p></div>
</div></div>
<?php if ($next): $nxd = $next['event_date']; ?>
<div class="relative max-w-7xl mx-auto px-4 pb-10 -mt-2">
<div class="rounded-3xl p-[1px] bg-gradient-to-r from-emerald-400 via-teal-300 to-emerald-400 reveal">
<div class="rounded-3xl bg-slate-900/95 backdrop-blur p-5 md:p-6 flex flex-col md:flex-row gap-5 items-start">
<div class="text-center bg-gradient-to-b from-emerald-500 to-teal-600 rounded-2xl px-5 py-4 min-w-[92px] shadow-lg shadow-emerald-900/50"><p class="text-3xl font-extrabold leading-none"><?= date('d', strtotime($nxd)) ?></p><p class="text-xs font-bold uppercase tracking-widest mt-1"><?= $monthShort[(int)date('n', strtotime($nxd))] ?> <?= date('Y', strtotime($nxd)) ?></p></div>
<div class="flex-1 min-w-0">
<p class="text-[11px] font-bold text-amber-300 tracking-widest uppercase"><span class="relative flex w-2 h-2 inline-block mr-1"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-amber-400"></span></span>Acara terdekat</p>
<h2 class="text-xl md:text-2xl font-extrabold mt-1"><?= Helper::e($next['title']) ?></h2>
<p class="text-sm text-slate-300 mt-1 flex flex-wrap gap-x-4 gap-y-1"><span><i class="fa fa-clock mr-1.5 text-emerald-300"></i><?= Helper::tgl($nxd) ?><?php if (!empty($next['start_time'])): ?> • <?= Helper::e($next['start_time']) ?><?php if (!empty($next['end_time'])): ?>–<?= Helper::e($next['end_time']) ?><?php endif; ?><?php endif; ?></span><?php if (!empty($next['location'])): ?><span><i class="fa fa-location-dot mr-1.5 text-emerald-300"></i><?= Helper::e($next['location']) ?></span><?php endif; ?></p>
<?php if (!empty($next['description'])): ?><p class="text-sm text-slate-400 mt-2 line-clamp-2"><?= Helper::e($next['description']) ?></p><?php endif; ?>
</div>
<div class="flex md:flex-col gap-2 items-center bg-white/5 border border-white/10 rounded-2xl p-3 text-center shrink-0">
<div class="flex gap-2" data-countdown="<?= Helper::e($nxd . ' ' . ($next['start_time'] ?: '00:00')) ?>">
<div><p class="text-xl font-extrabold bg-white/10 rounded-lg px-2 py-1" data-cd="d">0</p><p class="text-[10px] text-slate-400 mt-0.5">Hari</p></div>
<div><p class="text-xl font-extrabold bg-white/10 rounded-lg px-2 py-1" data-cd="h">0</p><p class="text-[10px] text-slate-400 mt-0.5">Jam</p></div>
<div><p class="text-xl font-extrabold bg-white/10 rounded-lg px-2 py-1" data-cd="m">0</p><p class="text-[10px] text-slate-400 mt-0.5">Mnt</p></div>
</div></div>
</div></div></div>
<?php endif; ?>
</div>
<div class="max-w-7xl mx-auto px-4 py-8">
<div class="sticky top-[68px] z-30 px-1 py-3 bg-slate-50/85 dark:bg-slate-950/85 backdrop-blur-lg rounded-2xl">
<div class="flex flex-col md:flex-row gap-2">
<div class="relative flex-1"><i class="fa fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i><input id="q" placeholder="Cari agenda, lokasi..." class="w-full border dark:border-slate-700 rounded-2xl pl-10 pr-3 py-2.5 text-sm bg-white dark:bg-slate-800 shadow-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
<div class="flex gap-1.5 text-sm font-bold">
<button data-f="all" class="fbtn px-4 py-2.5 rounded-2xl bg-slate-900 dark:bg-white dark:text-slate-900 text-white shadow">Semua</button>
<button data-f="up" class="fbtn px-4 py-2.5 rounded-2xl border dark:border-slate-700 bg-white dark:bg-slate-800">Mendatang</button>
<button data-f="past" class="fbtn px-4 py-2.5 rounded-2xl border dark:border-slate-700 bg-white dark:bg-slate-800">Selesai</button>
</div></div></div>
<?php if (!$all): ?>
<div class="bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-3xl p-14 text-center reveal"><span class="w-16 h-16 rounded-3xl bg-emerald-100 dark:bg-emerald-900 grid place-items-center mx-auto text-3xl">📅</span><p class="font-extrabold text-lg mt-4">Belum ada agenda</p><p class="text-sm text-slate-500">Agenda baru tampil di sini setelah admin publish.</p></div>
<?php else: foreach ($byMonth as $mk => $list): $ts = strtotime($mk . '-01'); ?>
<div class="mgroup" data-month="<?= Helper::e($mk) ?>">
<div class="flex items-center gap-3 mt-8 mb-3 reveal"><span class="text-sm font-extrabold bg-slate-900 dark:bg-white dark:text-slate-900 text-white px-3.5 py-1.5 rounded-full"><?= $monthFull[(int)date('n', $ts)] ?> <?= date('Y', $ts) ?></span><span class="text-xs text-slate-400 font-bold"><?= count($list) ?> kegiatan</span><span class="flex-1 h-px bg-slate-200 dark:bg-slate-700"></span></div>
<div class="grid gap-3">
<?php foreach ($list as $x): $ed = $x['event_date']; $edEnd = ($x['end_date']??'')!==''?$x['end_date']:$ed; $isUp = $edEnd >= $today; $dd = (int)floor((strtotime($ed) - strtotime($today)) / 86400); $ddEnd = (int)floor((strtotime($edEnd) - strtotime($today)) / 86400); $when = !$isUp ? 'Selesai' : ($dd <= 0 && $ddEnd >= 0 ? 'Berlangsung' : ($dd === 1 ? 'Besok' : "H-$dd")); ?>
<article data-kind="<?= $isUp ? 'up' : 'past' ?>" data-t="<?= Helper::e(strtolower($x['title'] . ' ' . ($x['location'] ?? ''))) ?>" class="aitem group bg-white dark:bg-slate-800 border dark:border-slate-700 rounded-3xl p-4 sm:p-5 flex gap-4 hover:shadow-xl hover:-translate-y-0.5 hover:border-emerald-300 dark:hover:border-emerald-700 transition-all reveal <?= $isUp ? '' : 'opacity-70 saturate-50' ?>">
<div class="text-center rounded-2xl px-3.5 py-3 h-fit min-w-[68px] <?= $isUp ? 'bg-gradient-to-b from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25' : 'bg-slate-100 dark:bg-slate-700 text-slate-400' ?>"><p class="text-2xl font-extrabold leading-none"><?= date('d', strtotime($ed)) ?></p><p class="text-[11px] font-bold uppercase mt-1"><?= $monthShort[(int)date('n', strtotime($ed))] ?></p></div>
<div class="min-w-0 flex-1">
<div class="flex flex-wrap items-center gap-2"><h2 class="font-extrabold text-[15px] group-hover:text-emerald-600 transition-colors"><?= Helper::e($x['title']) ?></h2><span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full <?= !$isUp ? 'bg-slate-200 dark:bg-slate-700 text-slate-500' : ($dd === 0 ? 'bg-red-500 text-white animate-pulse' : 'bg-violet-100 dark:bg-violet-900 text-violet-700 dark:text-violet-200') ?>"><?= $when ?></span></div>
<p class="text-xs text-slate-500 mt-1.5 flex flex-wrap gap-x-4 gap-y-1"><span><i class="fa fa-calendar-day mr-1.5 text-emerald-500"></i><?= Helper::tgl($ed) ?><?php if($edEnd!==$ed): ?> – <?= Helper::tgl($edEnd) ?><?php endif; ?></span><?php if (!empty($x['start_time'])): ?><span><i class="fa fa-clock mr-1.5 text-emerald-500"></i><?= Helper::e($x['start_time']) ?><?php if (!empty($x['end_time'])): ?>–<?= Helper::e($x['end_time']) ?><?php endif; ?></span><?php endif; ?><?php if (!empty($x['location'])): ?><span><i class="fa fa-location-dot mr-1.5 text-emerald-500"></i><?= Helper::e($x['location']) ?></span><?php endif; ?></p>
<?php if (!empty($x['description'])): ?><p class="text-sm text-slate-600 dark:text-slate-300 mt-2 leading-relaxed text-justify"><?= nl2br(Helper::e($x['description'])) ?></p><?php endif; ?>
</div></article>
<?php endforeach; ?>
</div></div>
<?php endforeach; ?>
<p id="empty" class="hidden text-center py-12"><span class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 grid place-items-center mx-auto text-2xl">🔍</span><span class="block font-bold mt-3">Tidak cocok.</span><span class="text-sm text-slate-500">Ubah kata kunci / filter.</span></p>
<?php endif; ?></div>
<script>
(function(){
  var q=document.getElementById('q'),items=[...document.querySelectorAll('.aitem')],empty=document.getElementById('empty'),mode='all';
  function run(){
    var s=(q.value||'').toLowerCase(),n=0,shownMonths={};
    items.forEach(function(el){
      var ok=(mode==='all'||el.dataset.kind===mode)&&(!s||el.dataset.t.includes(s));
      el.style.display=ok?'':'none';if(ok){n++;var g=el.closest('.mgroup');if(g)shownMonths[g.dataset.month]=1}
    });
    document.querySelectorAll('.mgroup').forEach(function(g){g.style.display=shownMonths[g.dataset.month]?'':'none'});
    if(empty)empty.classList.toggle('hidden',n>0);
  }
  q?.addEventListener('input',run);
  document.querySelectorAll('.fbtn').forEach(function(b){b.addEventListener('click',function(){
    mode=b.dataset.f;
    document.querySelectorAll('.fbtn').forEach(function(x){x.className='fbtn px-4 py-2.5 rounded-2xl border dark:border-slate-700 bg-white dark:bg-slate-800'});
    b.className='fbtn px-4 py-2.5 rounded-2xl bg-slate-900 dark:bg-white dark:text-slate-900 text-white shadow';run();
  })});
  document.querySelectorAll('[data-countdown]').forEach(function(box){
    var t=new Date(box.dataset.countdown.replace(' ','T')).getTime();
    function tick(){
      var d=t-Date.now();if(d<0)d=0;
      box.querySelector('[data-cd="d"]').textContent=Math.floor(d/864e5);
      box.querySelector('[data-cd="h"]').textContent=Math.floor(d/36e5)%24;
      box.querySelector('[data-cd="m"]').textContent=Math.floor(d/6e4)%60;
    }
    tick();setInterval(tick,30000);
  });
})();
</script>
<?php require ROOT . '/templates/frontend/footer.php'; ?>
