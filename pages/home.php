<?php
$profile = $db->query("SELECT * FROM school_profile LIMIT 1")->fetch() ?: [];
$postsAll = $db->query("SELECT p.*, c.name cat FROM posts p LEFT JOIN categories c ON c.id=p.category_id WHERE p.status='published' AND p.deleted_at IS NULL ORDER BY published_at DESC LIMIT 12")->fetchAll();
$agendas = $db->query("SELECT * FROM agenda WHERE status='published' AND event_date>=CURDATE() ORDER BY event_date LIMIT 6")->fetchAll();
$ann = $db->query("SELECT * FROM announcements WHERE status='published' ORDER BY published_at DESC LIMIT 6")->fetchAll();
$galImgs = [];
try { foreach($db->query("SELECT gi.*, g.title gtitle FROM gallery_images gi JOIN galleries g ON g.id=gi.gallery_id WHERE g.status='published' ORDER BY gi.id DESC LIMIT 12") as $r) $galImgs[]=$r; } catch (Throwable) {}
$teachers = $db->query("SELECT * FROM teachers WHERE is_active=1 ORDER BY sort_order LIMIT 8")->fetchAll();
$prestasi = $db->query("SELECT * FROM achievements WHERE is_active=1 ORDER BY id DESC LIMIT 6")->fetchAll();
$ekskul = $db->query("SELECT * FROM extracurriculars WHERE is_active=1 ORDER BY sort_order LIMIT 6")->fetchAll();
$secs = [];
try { foreach($db->query("SELECT * FROM homepage_sections WHERE is_active=1 ORDER BY sort_order,id") as $r) $secs[]=$r; } catch (Throwable) {}
// Slide per-section (hero/carousel). Fallback: tabel sliders lama bila masih ada.
$secSlides = [];
try {
  $ids = array_column($secs, 'id');
  if ($ids) {
    $in = implode(',', array_map('intval', $ids));
    foreach ($db->query("SELECT * FROM section_slides WHERE section_id IN ($in) AND is_active=1 ORDER BY section_id,sort_order,id") as $sl) $secSlides[$sl['section_id']][] = $sl;
  }
  $legacy = [];
  try { $legacy = $db->query("SELECT * FROM sliders WHERE is_active=1 ORDER BY sort_order LIMIT 12")->fetchAll(); } catch (Throwable) {}
} catch (Throwable) { $legacy = []; }
if (!$secs) $secs = [['id'=>0,'section_key'=>'hero','type'=>'hero','title'=>'Selamat Datang','subtitle'=>Database::setting('tagline',''),'style'=>'default','bg'=>'dark','padding'=>'xl','align'=>'left','items_limit'=>5,'image'=>'','content'=>'','btn_text'=>'Jelajahi Sekolah','btn_url'=>Helper::url('profil'),'btn2_text'=>'Lihat Berita','btn2_url'=>Helper::url('berita')]];
$metaTitle = Database::setting('homepage_title','Sekolah');
require ROOT.'/templates/frontend/header.php';

$padMap = ['sm'=>'py-6','md'=>'py-10','lg'=>'py-14','xl'=>'py-20'];
$bgMap = ['white'=>'bg-white dark:bg-slate-800','slate'=>'bg-slate-100 dark:bg-slate-900','emerald-soft'=>'bg-emerald-50 dark:bg-emerald-950','emerald'=>'bg-emerald-700 text-white','dark'=>'bg-slate-900 text-white','gradient-emerald'=>'bg-gradient-to-r from-emerald-600 to-teal-600 text-white','gradient-indigo'=>'bg-gradient-to-r from-indigo-600 to-violet-600 text-white','gradient-sunset'=>'bg-gradient-to-r from-orange-500 to-rose-500 text-white','gradient-ocean'=>'bg-gradient-to-r from-sky-600 to-indigo-600 text-white','transparent'=>''];
$boxMap = ['default'=>'','card'=>'rounded-2xl border shadow-sm p-6 md:p-8 bg-white dark:bg-slate-800','minimal'=>'border-t-4 border-emerald-500 pt-8','gradient'=>'rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-8 shadow-lg','sunset'=>'rounded-2xl bg-gradient-to-r from-orange-500 to-rose-500 text-white p-8 shadow-lg','ocean'=>'rounded-2xl bg-gradient-to-r from-sky-600 to-indigo-600 text-white p-8 shadow-lg','dark'=>'rounded-2xl bg-slate-900 text-white p-8 shadow-lg','glass'=>'rounded-2xl bg-white/70 dark:bg-slate-800/70 backdrop-blur border p-8 shadow','bordered'=>'rounded-2xl border-2 border-emerald-300 p-8','outline'=>'rounded-2xl border-2 border-dashed border-slate-300 p-8','glow'=>'rounded-2xl p-8 bg-white dark:bg-slate-800 border border-emerald-200 shadow-[0_0_40px_rgba(16,185,129,.35)]','band'=>'border-y-4 border-emerald-600 py-8','soft'=>'rounded-2xl bg-slate-50 dark:bg-slate-800 p-8','emerald-soft'=>'rounded-2xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-100 p-8','shadow'=>'rounded-2xl shadow-xl p-8 bg-white dark:bg-slate-800'];
$al = fn($a) => $a==='center' ? 'text-center mx-auto' : ($a==='right' ? 'text-right ml-auto' : 'text-left');
$btn = function($t,$u,$ghost=false,$target='_self') {
  if(!$t)return '';
  [$href,$auto]=Helper::menuUrl($u?:'#');
  $tgt=$target==='_blank'||$auto==='_blank'?' target="_blank" rel="noopener noreferrer"':'';
  return '<a href="'.Helper::e($href).'"'.$tgt.' class="'.($ghost?'border border-current px-5 py-2.5 rounded-xl hover:bg-white/10':'bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-emerald-500').' inline-block mt-2 mr-2">'.Helper::e($t).'</a>';
};
$head = fn($s) => (($s['title']??'')||($s['subtitle']??'')) ? '<div class="section-heading mb-6 '.$al($s['align']??'left').' max-w-2xl '.(($s['align']??'')==='center'?'mx-auto':'').'">'.($s['title']?'<h2 class="text-2xl md:text-3xl font-extrabold">'.Helper::e($s['title']).'</h2>':'').($s['subtitle']?'<p class="mt-2 opacity-80">'.Helper::e($s['subtitle']).'</p>':'').'</div>' : '';
$secBtns = function($s) use ($btn) {
  $list = [];
  if (!empty($s['buttons_json'])) { $jb = json_decode($s['buttons_json'], true); if (is_array($jb)) foreach ($jb as $b) { if (!empty($b['text']) || !empty($b['url'])) $list[] = $b; } }
  if (!$list) {
    if (!empty($s['btn_text']) || !empty($s['btn_url'])) $list[] = ['text' => $s['btn_text'], 'url' => $s['btn_url'], 'target' => $s['btn_target'] ?? '_self'];
    if (!empty($s['btn2_text']) || !empty($s['btn2_url'])) $list[] = ['text' => $s['btn2_text'], 'url' => $s['btn2_url'], 'target' => $s['btn2_target'] ?? '_self'];
  }
  $h = '';
  foreach ($list as $i => $b) $h .= $btn($b['text'] ?? '', $b['url'] ?? '', $i > 0, $b['target'] ?? '_self');
  return $h;
};

foreach ($secs as $s):
$type=$s['type']??'custom'; $lim=max(1,min(12,(int)($s['items_limit']??3))); $pad=$padMap[$s['padding']??'lg']??'py-14';
$fx=$s['effect']??'fade-up'; $fxCls=$fx==='none'?'fx-none':'fx fx-'.$fx;

if ($type==='hero'):
  // Hero = 1 gambar statis dari field image section. Slide/carousel tidak berlaku di sini.
  $t = $s['title'] ?: 'Selamat Datang'; $st = $s['subtitle'] ?? '';
  $heroImg = Helper::cover($s['image'] ?? '', 'hero-' . ($s['section_key'] ?? $t), 1600, 900);
?>
<section class="mobile-center-section relative overflow-hidden bg-slate-900 text-white <?= $fxCls ?> fx-hero min-h-[78vh] md:min-h-[92vh] flex items-center">
<img src="<?= Helper::e($heroImg) ?>" alt="<?= Helper::e($t) ?>" fetchpriority="high" class="fx-zoomimg absolute inset-0 w-full h-full object-cover" loading="eager">
<div class="absolute inset-0 bg-slate-900/55"></div>
<div class="absolute inset-0 bg-gradient-to-r from-emerald-950/90 via-emerald-900/50 to-slate-900/40"></div>
<div class="relative max-w-7xl mx-auto px-4 py-24 md:py-32 w-full <?= $al($s['align']??'left') ?>">
<h1 data-fx-item style="--fx-d:.05s" class="text-4xl md:text-6xl font-extrabold max-w-3xl leading-tight <?= ($s['align']??'')==='center'?'mx-auto':'' ?>"><?= Helper::e($t) ?></h1>
<p data-fx-item style="--fx-d:.2s" class="mt-4 text-lg text-slate-200 max-w-2xl <?= ($s['align']??'')==='center'?'mx-auto':'' ?>"><?= Helper::e($st) ?></p>
<div data-fx-item style="--fx-d:.35s" class="mt-8"><?= $secBtns($s) ?></div>
</div></section>
<?php continue; endif;

if ($type==='carousel'):
  $myItems = $secSlides[$s['id'] ?? 0] ?? [];
  if (!$myItems && !empty($legacy)) { $myItems = array_map(fn($l) => ['heading' => $l['heading'] ?? '', 'subheading' => $l['subheading'] ?? '', 'image' => $l['image'] ?? '', 'cta_text' => $l['cta_text'] ?? '', 'cta_url' => $l['cta_url'] ?? '', 'cta2_text' => $l['cta2_text'] ?? '', 'cta2_url' => $l['cta2_url'] ?? ''], $legacy); }
  if (!$myItems) $myItems = [['heading' => $s['title'] ?: 'Selamat Datang', 'subheading' => $s['subtitle'] ?? '', 'image' => $s['image'] ?? '', 'cta_text' => $s['btn_text'] ?? '', 'cta_url' => $s['btn_url'] ?? '']];
  $items = array_slice($myItems, 0, $lim);
?>
<section class="relative overflow-hidden bg-slate-900 text-white <?= $fxCls ?> min-h-[78vh] md:min-h-[92vh] grid -mt-px" data-carousel>
<?php foreach($items as $i=>$sl): $slImg=Helper::cover($sl['image']??'', 'slide-'.($sl['heading']??$i), 1600, 900); ?>
<div data-slide class="col-start-1 row-start-1 flex items-center transition-opacity duration-700 ease-out <?= $i?'opacity-0 pointer-events-none':'opacity-100' ?>">
<img src="<?= Helper::e($slImg) ?>" alt="<?= Helper::e($sl['heading']) ?>" class="absolute inset-0 w-full h-full object-cover" loading="lazy">
<div class="absolute inset-0 bg-slate-900/55"></div>
<div class="absolute inset-0 bg-gradient-to-r from-emerald-900/90 via-emerald-900/40 to-slate-900/30"></div>
<div class="relative max-w-7xl mx-auto px-4 py-24 md:py-32 w-full text-left flex flex-col justify-center items-start"><h2 class="text-4xl md:text-6xl font-extrabold max-w-3xl leading-tight text-left ml-0 mr-auto"><?= Helper::e($sl['heading']) ?></h2><p class="mt-4 text-lg text-slate-200 max-w-2xl text-left ml-0 mr-auto"><?= Helper::e($sl['subheading']??'') ?></p>
<div class="mt-6"><?php if(!empty($sl['cta_text'])): ?><a href="<?= Helper::e($sl['cta_url']??'#') ?>" class="bg-emerald-600 px-5 py-2.5 rounded-xl font-semibold"><?= Helper::e($sl['cta_text']) ?></a><?php endif; ?></div></div>
</div>
<?php endforeach; ?>
<button data-prev aria-label="Sebelumnya" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 grid place-items-center"><i class="fa fa-chevron-left"></i></button>
<button data-next aria-label="Berikutnya" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 grid place-items-center"><i class="fa fa-chevron-right"></i></button>
<div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2"><?php foreach($items as $i=>$x): ?><button data-dot aria-label="Slide <?= $i+1 ?>" class="w-2.5 h-2.5 rounded-full bg-white/40 <?= $i?'':'bg-white' ?>"></button><?php endforeach; ?></div>
</section>
<?php continue; endif;

$bg=$bgMap[$s['bg']??'white']??''; $box=$boxMap[$s['style']??'default']??'';
$fx=$s['effect']??'fade-up'; $fxCls=$fx==='none'?'fx-none':'fx fx-'.$fx;
?>
<section class="mobile-center-section <?= $pad ?> <?= $bg ?>"><div class="max-w-7xl mx-auto px-4"><div class="<?= $box ?> <?= $fxCls ?>">
<?= $type==='cta' ? '' : $head($s) ?>
<?php if($type==='sambutan'): ?>
<div class="welcome-content grid md:grid-cols-[300px_1fr] gap-5 md:gap-6 items-center">
<div class="welcome-photo flex justify-center"><?php $pp=!empty($profile['principal_photo'])?Helper::upload($profile['principal_photo']):Helper::dummy('kepala-sekolah',600,700); ?><img src="<?= Helper::e($pp) ?>" alt="Kepala Sekolah" class="rounded-2xl shadow w-full max-w-[300px] aspect-[4/5] object-cover" loading="lazy"></div>
<div class="welcome-copy"><p class="font-bold text-sm uppercase text-emerald-600">Sambutan</p><h3 class="text-2xl font-bold"><?= Helper::e($profile['principal_name']??'-') ?></h3><p class="text-sm opacity-70"><?= Helper::e($profile['principal_title']??'Kepala Sekolah') ?></p><p class="welcome-message mt-2 opacity-90 text-justify"><?= nl2br(Helper::e($profile['principal_greeting']??'Selamat datang.')) ?></p></div>
</div>
<?php elseif($type==='statistik'): $st=(int)($profile['total_students']??0); $gr=(int)($profile['total_teachers']??0); $ek=(int)($profile['total_extracurricular']??0); $th=(int)($profile['years_established']??0); ?>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
<?php foreach([['Siswa Aktif',$st,'+','fa-users','from-emerald-500 to-teal-600','Peserta didik tahun ini'],['Guru & Tendik',$gr,'','fa-chalkboard-user','from-sky-500 to-indigo-600','Pendidik profesional'],['Ekstrakurikuler',$ek,'','fa-futbol','from-amber-500 to-orange-600','Minat & bakat'],['Tahun Berdiri',$th,'','fa-building-columns','from-violet-500 to-fuchsia-600','Pengalaman mendidik']] as $stt): ?>
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br <?= $stt[4] ?> text-white p-5 card-hover">
<span class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-white/15"></span><span class="absolute right-6 top-8 w-8 h-8 rounded-full bg-white/10"></span>
<span class="w-10 h-10 rounded-xl bg-white/20 grid place-items-center"><i class="fa <?= $stt[3] ?>"></i></span>
<p class="text-3xl font-extrabold mt-3" data-count="<?= $stt[1] ?>" data-suffix="<?= $stt[2] ?>">0</p>
<p class="font-bold text-sm"><?= $stt[0] ?></p><p class="text-[11px] text-white/75"><?= $stt[5] ?></p>
</div><?php endforeach; ?>
</div>
<?php elseif($type==='berita'): $rows=array_slice($postsAll,0,$lim); $grid=$s['grid']??'cards-3'; ?>
<?php if(!$rows): ?><p class="opacity-70 text-sm">Belum ada berita</p>
<?php elseif($grid==='featured'): $f=array_shift($rows); $fc=Helper::cover($f['featured_image']??'', 'berita-'.$f['slug'], 1000, 600); ?>
<div class="grid md:grid-cols-2 gap-4">
<a href="<?= Helper::url('berita/'.$f['slug']) ?>" class="relative rounded-3xl overflow-hidden min-h-[320px] flex items-end card-hover group">
<img src="<?= Helper::e($fc) ?>" alt="<?= Helper::e($f['title']) ?>" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy">
<span class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/30 to-transparent"></span>
<span class="relative p-5 block"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500 text-white"><?= Helper::e($f['cat']??'Berita') ?></span>
<span class="block font-extrabold text-white text-xl mt-2"><?= Helper::e($f['title']) ?></span>
<span class="block text-xs text-slate-300 mt-1"><?= Helper::tgl($f['published_at']??$f['created_at']) ?> • <?= Helper::e(Helper::excerpt($f['excerpt']?:$f['content'],90)) ?></span></span></a>
<div class="grid gap-3"><?php foreach($rows as $p): $cv=Helper::cover($p['featured_image']??'', 'berita-'.$p['slug'], 400, 260); ?>
<a href="<?= Helper::url('berita/'.$p['slug']) ?>" class="flex gap-3 rounded-2xl border overflow-hidden bg-white dark:bg-slate-800 card-hover p-2.5">
<img src="<?= Helper::e($cv) ?>" alt="<?= Helper::e($p['title']) ?>" class="w-28 h-20 rounded-xl object-cover shrink-0" loading="lazy">
<span class="min-w-0"><span class="block font-bold text-sm line-clamp-2"><?= Helper::e($p['title']) ?></span><span class="block text-[11px] opacity-60 mt-1"><?= Helper::e($f['cat']??$p['cat']??'Berita') ?> • <?= Helper::tgl($p['published_at']??$p['created_at']) ?></span></span></a>
<?php endforeach; ?></div></div>
<?php elseif($grid==='magazine'): $lead=array_shift($rows); $leadImg=Helper::cover($lead['featured_image']??'', 'berita-'.$lead['slug'], 1000, 700); ?>
<div class="grid lg:grid-cols-5 gap-4"><a href="<?= Helper::url('berita/'.$lead['slug']) ?>" class="lg:col-span-3 lg:row-span-2 relative min-h-[360px] rounded-3xl overflow-hidden group card-hover"><img src="<?= Helper::e($leadImg) ?>" alt="<?= Helper::e($lead['title']) ?>" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700"><span class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/25 to-transparent"></span><span class="absolute bottom-0 p-6 text-white"><span class="text-[10px] font-bold bg-emerald-600 px-2 py-1 rounded-full"><?= Helper::e($lead['cat']??'Berita') ?></span><strong class="block text-2xl md:text-3xl mt-3 leading-tight"><?= Helper::e($lead['title']) ?></strong><small class="block mt-2 text-slate-300"><?= Helper::tgl($lead['published_at']??$lead['created_at']) ?></small></span></a><div class="lg:col-span-2 grid gap-4"><?php foreach($rows as $p): $cv=Helper::cover($p['featured_image']??'', 'berita-'.$p['slug'],500,300); ?><a href="<?= Helper::url('berita/'.$p['slug']) ?>" class="flex gap-3 rounded-2xl border bg-white dark:bg-slate-800 p-2.5 card-hover"><img src="<?= Helper::e($cv) ?>" alt="<?= Helper::e($p['title']) ?>" class="w-28 h-24 rounded-xl object-cover"><span class="min-w-0"><small class="text-emerald-600 font-bold"><?= Helper::e($p['cat']??'Berita') ?></small><b class="block line-clamp-2 mt-1"><?= Helper::e($p['title']) ?></b><small class="text-slate-400"><?= Helper::tgl($p['published_at']??$p['created_at']) ?></small></span></a><?php endforeach; ?></div></div>
<?php elseif($grid==='masonry'): ?>
<div class="columns-1 sm:columns-2 lg:columns-3 gap-4"><?php foreach($rows as $i=>$p): $cv=Helper::cover($p['featured_image']??'', 'berita-'.$p['slug'],700,600); ?><article class="break-inside-avoid mb-4 rounded-2xl border overflow-hidden bg-white dark:bg-slate-800 card-hover"><img src="<?= Helper::e($cv) ?>" alt="<?= Helper::e($p['title']) ?>" class="w-full object-cover <?= $i%3===0?'h-64':($i%3===1?'h-44':'h-52') ?>"><div class="p-4"><small class="text-emerald-600 font-bold"><?= Helper::e($p['cat']??'Berita') ?></small><h3 class="font-bold mt-1"><?= Helper::e($p['title']) ?></h3><p class="text-xs text-slate-500 mt-2"><?= Helper::e(Helper::excerpt($p['excerpt']?:$p['content'],90)) ?></p></div></article><?php endforeach; ?></div>
<?php elseif($grid==='horizontal'): ?>
<div class="grid md:grid-cols-2 gap-4"><?php foreach($rows as $p): $cv=Helper::cover($p['featured_image']??'', 'berita-'.$p['slug'],500,400); ?><a href="<?= Helper::url('berita/'.$p['slug']) ?>" class="grid grid-cols-[120px_1fr] sm:grid-cols-[180px_1fr] rounded-2xl border overflow-hidden bg-white dark:bg-slate-800 card-hover"><img src="<?= Helper::e($cv) ?>" alt="<?= Helper::e($p['title']) ?>" class="w-full h-full min-h-36 object-cover"><span class="p-4"><small class="text-emerald-600 font-bold"><?= Helper::e($p['cat']??'Berita') ?></small><b class="block mt-1 line-clamp-2"><?= Helper::e($p['title']) ?></b><small class="block text-slate-400 mt-2"><?= Helper::tgl($p['published_at']??$p['created_at']) ?></small></span></a><?php endforeach; ?></div>
<?php elseif($grid==='timeline'): ?>
<div class="relative ml-3 md:ml-6 border-l-2 border-emerald-200 space-y-5"><?php foreach($rows as $p): ?><article class="relative pl-7"><span class="absolute -left-[9px] top-2 w-4 h-4 rounded-full bg-emerald-600 ring-4 ring-white dark:ring-slate-900"></span><div class="rounded-2xl border bg-white dark:bg-slate-800 p-4 card-hover"><small class="text-emerald-600 font-bold"><?= Helper::tgl($p['published_at']??$p['created_at']) ?> &bull; <?= Helper::e($p['cat']??'Berita') ?></small><h3 class="font-bold text-lg mt-1"><?= Helper::e($p['title']) ?></h3><p class="text-sm text-slate-500 mt-1"><?= Helper::e(Helper::excerpt($p['excerpt']?:$p['content'],120)) ?></p><a href="<?= Helper::url('berita/'.$p['slug']) ?>" class="inline-block text-emerald-600 text-sm font-bold mt-2">Baca selengkapnya &rarr;</a></div></article><?php endforeach; ?></div>
<?php elseif($grid==='list'): ?>
<div class="grid gap-2.5"><?php foreach($rows as $p): $cv=Helper::cover($p['featured_image']??'', 'berita-'.$p['slug'], 400, 260); ?>
<a href="<?= Helper::url('berita/'.$p['slug']) ?>" class="flex gap-3.5 items-center rounded-2xl border bg-white dark:bg-slate-800 p-3 card-hover">
<img src="<?= Helper::e($cv) ?>" alt="<?= Helper::e($p['title']) ?>" class="w-24 h-24 md:w-28 md:h-24 rounded-xl object-cover shrink-0" loading="lazy">
<span class="min-w-0 flex-1"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-200"><?= Helper::e($p['cat']??'Berita') ?></span>
<span class="block font-bold mt-1 line-clamp-2"><?= Helper::e($p['title']) ?></span>
<span class="block text-xs opacity-60 mt-0.5"><?= Helper::tgl($p['published_at']??$p['created_at']) ?> • <?= Helper::e(Helper::excerpt($p['excerpt']?:$p['content'],80)) ?></span></span>
<span class="text-emerald-600 font-bold text-sm shrink-0 hidden sm:block">→</span></a>
<?php endforeach; ?></div>
<?php elseif($grid==='overlay'): ?>
<div class="grid sm:grid-cols-2 <?= count($rows)>3?'lg:grid-cols-4':'md:grid-cols-3' ?> gap-4"><?php foreach($rows as $p): $cv=Helper::cover($p['featured_image']??'', 'berita-'.$p['slug'], 800, 600); ?>
<a href="<?= Helper::url('berita/'.$p['slug']) ?>" class="relative rounded-3xl overflow-hidden h-64 card-hover group">
<img src="<?= Helper::e($cv) ?>" alt="<?= Helper::e($p['title']) ?>" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy">
<span class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/20 to-transparent"></span>
<span class="absolute bottom-0 p-4 block"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/20 text-white backdrop-blur"><?= Helper::e($p['cat']??'Berita') ?></span>
<span class="block font-bold text-white mt-1.5 line-clamp-2"><?= Helper::e($p['title']) ?></span>
<span class="block text-[11px] text-slate-300 mt-1"><?= Helper::tgl($p['published_at']??$p['created_at']) ?></span></span></a>
<?php endforeach; ?></div>
<?php elseif($grid==='minimal'): ?>
<div class="divide-y divide-slate-200 dark:divide-slate-700 border-y"><?php foreach($rows as $p): ?>
<a href="<?= Helper::url('berita/'.$p['slug']) ?>" class="flex items-baseline gap-3 py-3.5 group">
<span class="text-xs font-mono text-slate-400 shrink-0"><?= Helper::tgl($p['published_at']??$p['created_at']) ?></span>
<span class="font-bold flex-1 group-hover:text-emerald-600 transition-colors"><?= Helper::e($p['title']) ?></span>
<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 shrink-0"><?= Helper::e($p['cat']??'Berita') ?></span>
<span class="text-emerald-600 font-bold group-hover:translate-x-1 transition-transform">→</span></a>
<?php endforeach; ?></div>
<?php elseif(in_array($grid,['cards-2','cards-3','cards-4'],true)): $cc=$grid==='cards-2'?'md:grid-cols-2':($grid==='cards-4'?'sm:grid-cols-2 lg:grid-cols-4':'md:grid-cols-3'); $hh=$grid==='cards-4'?'h-36':'h-44'; ?>
<div class="grid <?= $cc ?> gap-4"><?php foreach($rows as $p): $cv=Helper::cover($p['featured_image']??'', 'berita-'.$p['slug'], 800, 500); ?>
<article class="rounded-2xl border overflow-hidden card-hover bg-white dark:bg-slate-800"><img src="<?= Helper::e($cv) ?>" alt="<?= Helper::e($p['title']) ?>" class="<?= $hh ?> w-full object-cover" loading="lazy">
<div class="p-4"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-200"><?= Helper::e($p['cat']??'Berita') ?></span>
<h3 class="font-bold mt-2 line-clamp-2"><?= Helper::e($p['title']) ?></h3><p class="text-xs opacity-60 mt-1"><?= Helper::tgl($p['published_at']??$p['created_at']) ?></p><a href="<?= Helper::url('berita/'.$p['slug']) ?>" class="text-emerald-600 text-sm font-semibold mt-2 inline-block">Baca →</a></div></article>
<?php endforeach; ?></div>
<?php else: ?>
<div class="grid md:grid-cols-3 gap-4"><?php foreach($rows as $p): $cv=Helper::cover($p['featured_image']??'', 'berita-'.$p['slug'], 800, 500); ?>
<article class="rounded-2xl border overflow-hidden card-hover bg-white dark:bg-slate-800"><img src="<?= Helper::e($cv) ?>" alt="<?= Helper::e($p['title']) ?>" class="h-44 w-full object-cover" loading="lazy">
<div class="p-4"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-200"><?= Helper::e($p['cat']??'Berita') ?></span>
<h3 class="font-bold mt-2 line-clamp-2"><?= Helper::e($p['title']) ?></h3><p class="text-xs opacity-60 mt-1"><?= Helper::tgl($p['published_at']??$p['created_at']) ?></p><a href="<?= Helper::url('berita/'.$p['slug']) ?>" class="text-emerald-600 text-sm font-semibold mt-2 inline-block">Baca →</a></div></article>
<?php endforeach; ?></div>
<?php endif; ?>
<?php elseif($type==='agenda'): $rows=array_slice($agendas,0,$lim); $blnA=[1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des']; $today=date('Y-m-d'); ?>
<div class="flex flex-wrap items-center gap-2 mb-3"><h3 class="font-extrabold text-lg"><i class="fa fa-calendar-days text-violet-500 mr-1.5"></i><?= Helper::e($s['title']??'Agenda Terdekat') ?></h3><span class="text-[11px] bg-violet-100 dark:bg-violet-900 text-violet-700 dark:text-violet-200 px-2 py-0.5 rounded-full font-bold"><?= count($rows) ?> agenda</span><a href="<?= Helper::url('agenda') ?>" class="ml-auto text-xs text-emerald-600 font-bold">Semua →</a></div>
<?php if(!$rows): ?><p class="opacity-70 text-sm">Belum ada agenda</p><?php else: ?><div class="grid gap-2.5"><?php foreach($rows as $a): $dd=(int)floor((strtotime($a['event_date'])-strtotime($today))/86400); $when=$dd===0?'Hari ini':($dd===1?'Besok':"H-$dd"); ?>
<div class="flex gap-2.5 items-start border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-2xl px-3.5 py-3 card-hover">
<span class="w-12 shrink-0 text-center bg-slate-50 dark:bg-slate-700 border dark:border-slate-600 rounded-xl py-1.5"><b class="block text-lg leading-none text-violet-600"><?= date('d',strtotime($a['event_date'])) ?></b><span class="text-[10px] text-slate-500 uppercase"><?= $blnA[(int)date('n',strtotime($a['event_date']))] ?></span></span>
<span class="min-w-0 flex-1"><b class="text-sm block truncate"><?= Helper::e($a['title']) ?></b>
<span class="text-[11px] text-slate-500 block mt-0.5"><i class="fa fa-location-dot mr-1"></i><?= Helper::e($a['location']?:'Lokasi TBD') ?><?php if(!empty($a['start_time'])): ?> • <i class="fa fa-clock mr-1"></i><?= Helper::e($a['start_time']) ?><?php endif; ?></span>
<?php if(!empty($a['description'])): ?><span class="text-xs text-slate-500 block truncate mt-0.5"><?= Helper::e($a['description']) ?></span><?php endif; ?></span>
<span class="text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0 <?= $dd===0?'bg-red-100 text-red-700':'bg-violet-100 text-violet-700' ?>"><?= $when ?></span></div>
<?php endforeach; ?></div><?php endif; ?>
<?php elseif($type==='pengumuman'): $rows=array_slice($ann,0,$lim); ?>
<div class="flex flex-wrap items-center gap-2 mb-3"><h3 class="font-extrabold text-lg"><i class="fa fa-bullhorn text-rose-500 mr-1.5"></i><?= Helper::e($s['title']??'Pengumuman Terbaru') ?></h3><span class="text-[11px] bg-rose-100 dark:bg-rose-900 text-rose-700 dark:text-rose-200 px-2 py-0.5 rounded-full font-bold"><?= count($rows) ?> info</span><a href="<?= Helper::url('pengumuman') ?>" class="ml-auto text-xs text-emerald-600 font-bold">Semua →</a></div>
<?php if(!$rows): ?><p class="opacity-70 text-sm">Belum ada pengumuman</p><?php else: ?><div class="grid gap-2.5"><?php foreach($rows as $i=>$a): $hasFile=!empty($a['attachment']); ?>
<div class="announcement-card flex min-w-0 overflow-hidden gap-2 sm:gap-2.5 items-start border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-2xl px-3 sm:px-3.5 py-3 card-hover">
<span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl grid place-items-center shrink-0 text-white <?= $i===0?'bg-gradient-to-b from-amber-400 to-orange-500':'bg-gradient-to-b from-rose-500 to-pink-600' ?>"><i class="fa <?= $i===0?'fa-star':'fa-bullhorn' ?> text-sm"></i></span>
<span class="min-w-0 max-w-full flex-1 overflow-hidden"><span class="flex min-w-0 flex-wrap items-center gap-1.5"><b class="min-w-0 max-w-full text-sm break-words [overflow-wrap:anywhere]"><?= Helper::e($a['title']) ?></b><?php if($i===0): ?><span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">TERBARU</span><?php endif; ?><?php if($hasFile): ?><span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full bg-sky-100 text-sky-700"><i class="fa fa-paperclip mr-0.5"></i>Lampiran</span><?php endif; ?></span>
<span class="text-[11px] text-slate-400 block mt-0.5"><i class="fa fa-clock mr-1"></i><?= Helper::e(Helper::ago($a['published_at']??$a['created_at'])) ?> • <?= Helper::e(Helper::tgl($a['published_at']??$a['created_at'])) ?></span>
<span class="text-xs text-slate-500 block mt-1 line-clamp-2 break-words [overflow-wrap:anywhere]"><?= Helper::e(Helper::excerpt($a['content']??'',110)) ?></span></span></div>
<?php endforeach; ?></div><?php endif; ?>
<?php elseif($type==='galeri'): $rows=array_slice($galImgs,0,max(4,$lim)); ?>
<?php if(!$rows): ?><div class="grid grid-cols-2 md:grid-cols-4 gap-3"><?php for($gi=0;$gi<4;$gi++): ?><img src="<?= Helper::e(Helper::dummy('galeri-'.$gi.'-'.($s['section_key']??''),600,450)) ?>" alt="Galeri sekolah" loading="lazy" class="h-40 w-full object-cover rounded-xl border card-hover"><?php endfor; ?></div><?php else: ?><div class="grid grid-cols-2 md:grid-cols-4 gap-3"><?php foreach($rows as $g): ?><img src="<?= Helper::url($g['filepath']) ?>" alt="<?= Helper::e($g['caption']??$g['gtitle']) ?>" data-lightbox loading="lazy" class="h-40 w-full object-cover rounded-xl border cursor-zoom-in card-hover"><?php endforeach; ?></div><?php endif; ?>
<?php elseif($type==='guru'): $rows=array_slice($teachers,0,$lim); ?>
<?php if(!$rows): ?><div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4"><?php for($gi=0;$gi<4;$gi++): ?><div class="text-center border rounded-2xl p-4 bg-white dark:bg-slate-800"><img src="<?= Helper::e(Helper::dummy('guru-'.$gi,300,300)) ?>" alt="Guru" loading="lazy" class="w-20 h-20 mx-auto rounded-full object-cover"><p class="font-bold mt-2">Nama Guru</p><p class="text-xs text-emerald-700">Guru</p></div><?php endfor; ?></div><?php else: ?><div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4"><?php foreach($rows as $g): ?><div class="text-center border rounded-2xl p-4 bg-white dark:bg-slate-800"><div class="w-20 h-20 mx-auto rounded-full bg-emerald-100 grid place-items-center text-3xl overflow-hidden"><?php if($g['photo']): ?><img src="<?= Helper::upload($g['photo']) ?>" alt="<?= Helper::e($g['name']) ?>" class="w-full h-full object-cover" loading="lazy"><?php else: ?><img src="<?= Helper::e(Helper::dummy('guru-'.$g['id'],300,300)) ?>" alt="<?= Helper::e($g['name']) ?>" class="w-full h-full object-cover" loading="lazy"><?php endif; ?></div><p class="font-bold mt-2"><?= Helper::e($g['name']) ?></p><p class="text-xs text-emerald-700"><?= Helper::e($g['position']) ?></p></div><?php endforeach; ?></div><?php endif; ?>
<?php elseif($type==='prestasi'): $rows=array_slice($prestasi,0,$lim); ?>
<div class="flex flex-wrap items-center gap-2 mb-3"><h3 class="font-extrabold text-lg"><i class="fa fa-trophy text-amber-500 mr-1.5"></i><?= Helper::e($s['title']??'Prestasi') ?></h3><span class="text-[11px] bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-200 px-2 py-0.5 rounded-full font-bold"><?= count($rows) ?> prestasi</span><a href="<?= Helper::url('prestasi') ?>" class="ml-auto text-xs text-emerald-600 font-bold">Semua →</a></div>
<?php if(!$rows): ?><div class="text-center border border-dashed rounded-2xl p-8"><span class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900 grid place-items-center mx-auto text-xl">🏆</span><p class="font-bold mt-2 text-sm">Belum ada prestasi</p><p class="text-xs opacity-60">Prestasi siswa tampil di sini setelah ditambah admin.</p></div><?php else: ?><div class="grid md:grid-cols-3 gap-4"><?php foreach($rows as $r): ?>
<article class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 card-hover flex flex-col">
<div class="bg-gradient-to-r from-amber-400 to-orange-500 p-4 flex items-center gap-3">
<span class="w-11 h-11 rounded-xl bg-white/25 grid place-items-center text-white text-xl shrink-0"><i class="fa fa-trophy"></i></span>
<span class="flex gap-1.5 flex-wrap"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/25 text-white"><?= Helper::e($r['level']??'Sekolah') ?></span><span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-900/30 text-white"><?= Helper::e($r['year']??date('Y')) ?></span></span>
</div>
<div class="p-4 flex-1 flex flex-col"><h3 class="font-bold leading-snug"><?= Helper::e($r['title']) ?></h3>
<?php if(!empty($r['description'])): ?><p class="text-xs text-slate-500 mt-1.5 line-clamp-3"><?= Helper::e(Helper::excerpt($r['description'],120)) ?></p><?php endif; ?>
<p class="text-[11px] text-slate-400 mt-auto pt-2"><i class="fa fa-medal mr-1 text-amber-500"></i>Prestasi <?= Helper::e($r['level']??'') ?></p></div></article>
<?php endforeach; ?></div><?php endif; ?>
<?php elseif($type==='ekskul'): $rows=array_slice($ekskul,0,$lim); $ekIcon=['fa-futbol','fa-campground','fa-palette','fa-music','fa-robot','fa-book-quran']; ?>
<div class="flex flex-wrap items-center gap-2 mb-3"><h3 class="font-extrabold text-lg"><i class="fa fa-futbol text-emerald-500 mr-1.5"></i><?= Helper::e($s['title']??'Ekstrakurikuler') ?></h3><span class="text-[11px] bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-200 px-2 py-0.5 rounded-full font-bold"><?= count($rows) ?> kegiatan</span><a href="<?= Helper::url('ekstrakurikuler') ?>" class="ml-auto text-xs text-emerald-600 font-bold">Semua →</a></div>
<?php if(!$rows): ?><div class="text-center border border-dashed rounded-2xl p-8"><span class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900 grid place-items-center mx-auto text-xl">⚽</span><p class="font-bold mt-2 text-sm">Belum ada ekstrakurikuler</p><p class="text-xs opacity-60">Kegiatan ekskul tampil di sini setelah ditambah admin.</p></div><?php else: ?><div class="grid md:grid-cols-3 gap-4"><?php foreach($rows as $i=>$r): $ic=$ekIcon[$i%count($ekIcon)]; ?>
<article class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 card-hover">
<div class="flex items-center gap-3">
<span class="w-12 h-12 rounded-2xl bg-gradient-to-b from-emerald-500 to-teal-600 text-white grid place-items-center text-xl shrink-0"><i class="fa <?= $ic ?>"></i></span>
<div class="min-w-0"><h3 class="font-bold leading-snug truncate"><?= Helper::e($r['name']) ?></h3>
<?php if(!empty($r['coach'])): ?><p class="text-[11px] text-slate-500"><i class="fa fa-user-tie mr-1 text-emerald-500"></i><?= Helper::e($r['coach']) ?></p><?php endif; ?></div>
</div>
<?php if(!empty($r['description'])): ?><p class="text-xs text-slate-500 mt-3 line-clamp-3"><?= Helper::e(Helper::excerpt($r['description'],110)) ?></p><?php endif; ?>
<div class="flex flex-wrap gap-1.5 mt-3">
<?php if(!empty($r['coach'])): ?><span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700"><i class="fa fa-user mr-0.5"></i><?= Helper::e(mb_substr($r['coach'],0,18)) ?></span><?php endif; ?>
<?php if(!empty($r['schedule'])): ?><span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-200"><i class="fa fa-clock mr-0.5"></i><?= Helper::e($r['schedule']) ?></span><?php endif; ?>
</div></article>
<?php endforeach; ?></div><?php endif; ?>
<?php elseif($type==='cta'): ?>
<div class="cta-panel relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-500 text-white p-7 md:p-12 shadow-xl"><span class="absolute -right-16 -top-20 w-64 h-64 rounded-full border-[28px] border-white/10"></span><span class="absolute -left-20 -bottom-24 w-72 h-72 rounded-full border-[36px] border-white/10"></span><div class="relative max-w-3xl mx-auto text-center"><span class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/20 px-3 py-1 text-[11px] font-bold uppercase tracking-[.16em]"><i class="fa fa-sparkles text-amber-300"></i><?= Helper::e($s['subtitle']??'Bersama membangun masa depan') ?></span><h3 class="text-3xl md:text-5xl font-extrabold leading-tight mt-4"><?= Helper::e($s['title']??'Mari Bergabung Bersama Kami') ?></h3><p class="text-white/80 text-sm md:text-base max-w-2xl mx-auto mt-4 leading-relaxed"><?= Helper::e($s['content']??'Temukan lingkungan belajar yang aman, inspiratif, dan berorientasi pada masa depan.') ?></p><div class="mt-7 flex flex-wrap justify-center gap-3"><?= $secBtns($s) ?: $btn('Hubungi Kami',Helper::url('kontak')) ?></div><div class="mt-6 flex flex-wrap justify-center gap-x-6 gap-y-2 text-xs text-white/70"><span><i class="fa fa-circle-check text-amber-300 mr-1"></i>Pembelajaran berkarakter</span><span><i class="fa fa-circle-check text-amber-300 mr-1"></i>Guru profesional</span><span><i class="fa fa-circle-check text-amber-300 mr-1"></i>Siap menghadapi masa depan</span></div></div></div>
<?php else: ?>
<?php if(!empty($s['image'])): ?><img src="<?= Helper::upload($s['image']) ?>" alt="<?= Helper::e($s['title']??'') ?>" class="rounded-2xl mb-4 w-full max-h-96 object-cover" loading="lazy"><?php endif; ?>
<?php if(!empty($s['content'])): ?><div class="prose max-w-none"><?= $s['content'] ?></div><?php endif; ?>
<?php $cb=$secBtns($s); if($cb): ?><div class="mt-3"><?= $cb ?></div><?php endif; ?>
<?php endif; ?>
</div></div></section>
<?php endforeach; ?>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
