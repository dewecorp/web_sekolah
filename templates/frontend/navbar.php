<?php
$top_addr = Database::setting('address','Jl. Pendidikan No.123');
$top_phone = Database::setting('phone','(021) 1234567');
$top_email = Database::setting('email','info@sekolah.sch.id');
$school = Database::setting('school_name','SMK Nusantara');
$tagline = Database::setting('tagline','Unggul, Berkarakter');
$logo = Database::setting('logo','');
try {
$db = Database::conn();
$mm = $db->query("SELECT * FROM menus WHERE location='primary' LIMIT 1")->fetch();
$items = [];
if ($mm) {
  $st = $db->prepare("SELECT * FROM menu_items WHERE menu_id=? AND is_active=1 ORDER BY sort_order");
  $st->execute([$mm['id']]); $items = $st->fetchAll();
}
$mega = $db->query("SELECT m.*, mi.label, mi.url FROM mega_menus m LEFT JOIN menu_items mi ON mi.id=m.menu_item_id WHERE m.is_active=1 ORDER BY m.sort_order")->fetchAll();
} catch (Throwable) { $items=[]; $mega=[]; }
function menuTree($items,$parent=null){ $o=[]; foreach($items as $i){ if(($i['parent_id']??null)==$parent) $o[]=$i; } return $o; }
function menuLink($u,$forceTarget=null){ [$href,$t]=Helper::menuUrl($u); $t=$forceTarget?:$t; return 'href="'.Helper::e($href).'"'.($t==='_blank'?' target="_blank" rel="noopener noreferrer"':''); }
?>
<div class="bg-emerald-700 text-white text-xs hidden md:block"><div class="max-w-7xl mx-auto px-4 py-1.5 flex justify-between items-center gap-3">
<div class="flex gap-4 min-w-0"><span class="shrink-0"><i class="fa fa-calendar-day mr-1"></i><span data-clock-date><?= Helper::tgl(date('Y-m-d')) ?></span> • <span data-clock-time class="font-mono font-bold"><?= date('H:i:s') ?></span> WIB</span><span class="truncate"><i class="fa fa-location-dot mr-1"></i><?= Helper::e($top_addr) ?></span><span class="hidden lg:inline"><i class="fa fa-phone mr-1"></i><?= Helper::e($top_phone) ?></span><span class="hidden xl:inline"><i class="fa fa-envelope mr-1"></i><?= Helper::e($top_email) ?></span></div>
<div class="flex gap-3"><a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a><a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a><a href="#" aria-label="Youtube"><i class="fab fa-youtube"></i></a></div>
</div></div>
<header id="mainNav" class="sticky top-0 z-50 bg-white/90 dark:bg-slate-900/90 backdrop-blur border-b-0 transition">
<div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
<a href="<?= Helper::url() ?>" class="flex items-center gap-2.5"><span class="w-11 h-11 rounded-xl bg-emerald-600 text-white grid place-items-center font-bold overflow-hidden shadow-sm"><?php if($logo): ?><img src="<?= Helper::upload($logo) ?>" alt="Logo <?= Helper::e($school) ?>" class="w-full h-full object-contain bg-white p-1"><?php else: ?><?= Helper::e(mb_substr($school,0,1)) ?><?php endif; ?></span><span><span class="block font-extrabold leading-tight"><?= Helper::e($school) ?></span><span class="block text-xs text-slate-500"><?= Helper::e($tagline) ?></span></span></a>
<nav class="hidden lg:flex items-center gap-1 text-sm font-medium" id="deskMenu">
<?php foreach(menuTree($items) as $m): $ch=menuTree($items,$m['id']); $hasMega=false;
if(($m['kind']??'link')==='mega'&&!empty($m['mega_id'])){ foreach($mega as $g){ if((int)$g['id']===(int)$m['mega_id']) $hasMega=$g; } }
if(!$hasMega){ foreach($mega as $g){ if(($g['label']??'')===$m['label']) $hasMega=$g; } } ?>
<?php if($hasMega): $cols=json_decode($hasMega['columns_json']??'[]',true)?:[]; ?>
<div class="relative group"><button class="px-3 py-2 rounded-lg hover:bg-emerald-50"><?= Helper::e($m['label']) ?> <i class="fa fa-chevron-down text-[10px]"></i></button>
<div class="absolute left-1/2 -translate-x-1/2 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition"><div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border p-6 grid grid-cols-3 gap-6 w-[640px] max-w-[90vw]">
<?php foreach($cols as $c): ?><div><p class="font-bold text-xs uppercase text-emerald-700 mb-2"><?= Helper::e($c['title']??'') ?></p><?php foreach($c['links']??[] as $l): ?><a href="<?= Helper::e($l['url']??'#') ?>" class="block py-1 text-slate-600 dark:text-slate-300 hover:text-emerald-600"><?= Helper::e($l['label']??'') ?></a><?php endforeach; ?></div><?php endforeach; ?>
</div></div></div>
<?php elseif($ch): ?><div class="relative group"><a <?= menuLink($m['url'],$m['target']??null) ?> class="px-3 py-2 rounded-lg hover:bg-emerald-50 inline-block"><?= Helper::e($m['label']) ?> <i class="fa fa-chevron-down text-[10px]"></i></a>
<div class="absolute top-full left-0 pt-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition"><div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl border py-2 w-52"><?php foreach($ch as $c): ?><a <?= menuLink($c['url'],$c['target']??null) ?> class="block px-4 py-2 hover:bg-emerald-50"><?= Helper::e($c['label']) ?></a><?php endforeach; ?></div></div></div>
<?php else: ?><a <?= menuLink($m['url'],$m['target']??null) ?> class="px-3 py-2 rounded-lg hover:bg-emerald-50"><?= Helper::e($m['label']) ?></a><?php endif; ?><?php endforeach; ?>
</nav>
<div class="flex items-center gap-2"><button id="darkBtn" class="w-9 h-9 rounded-lg border grid place-items-center" aria-label="Tema">🌙</button><button id="mobBtn" class="lg:hidden w-9 h-9 rounded-lg border grid place-items-center"><i class="fa fa-bars"></i></button></div>
</div>
<div id="mobMenu" class="lg:hidden hidden border-t max-h-[70vh] overflow-auto"><div class="p-4 grid gap-1 text-sm">
<?php foreach(menuTree($items) as $m): $ch=menuTree($items,$m['id']); ?>
<?php if($ch): ?><details class="border rounded-lg"><summary class="p-2 cursor-pointer"><?= Helper::e($m['label']) ?></summary><div class="pl-4 pb-2"><?php foreach($ch as $c): ?><a <?= menuLink($c['url'],$c['target']??null) ?> class="block p-2"><?= Helper::e($c['label']) ?></a><?php endforeach; ?></div></details>
<?php else: ?><a <?= menuLink($m['url'],$m['target']??null) ?> class="p-2 rounded hover:bg-slate-100"><?= Helper::e($m['label']) ?></a><?php endif; ?><?php endforeach; ?>
</div></div>
</header>
