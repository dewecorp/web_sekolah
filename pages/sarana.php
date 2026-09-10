<?php if((Database::setting('sarana_show','1')==='0')){ http_response_code(404); require ROOT.'/templates/error/404.php'; exit; }
$rows=$db->query("SELECT * FROM facilities ORDER BY sort_order,id")->fetchAll();
$sTitle=Database::setting('sarana_title','Sarana & Infrastruktur'); if(trim($sTitle)==='')$sTitle='Sarana & Infrastruktur'; $sDesc=Database::setting('sarana_desc','');
$baik=count(array_filter($rows,fn($x)=>($x['cond']??'baik')==='baik')); $rusak=count($rows)-$baik;
$metaTitle=$sTitle.' - '.Database::setting('school_name','Sekolah'); require ROOT."/templates/frontend/header.php";
$heroBadge='<i class="fa fa-building-columns text-amber-300"></i>'.Helper::e($sTitle);
$heroTitle=$sTitle; $heroDesc=$sDesc;
$heroCrumb='<a href="'.Helper::url().'" class="hover:text-white">Beranda</a> / Sarana';
$heroTheme='teal';
$heroStats=[['icon'=>'fa-calendar-day','label'=>Helper::pageDate('facilities'),'solid'=>true],['icon'=>'fa-building-columns','label'=>count($rows).' sarana','solid'=>false],['icon'=>'fa-circle-check','label'=>$baik.' baik','solid'=>false],['icon'=>'fa-triangle-exclamation','label'=>$rusak.' rusak','solid'=>false]];
require ROOT."/templates/frontend/page-hero.php"; ?>
<div class="w-full px-4 md:px-8 py-10">
<?php if(!$rows): ?><div class="bg-white border rounded-3xl p-12 text-center mt-4 text-slate-500"><span class="w-14 h-14 rounded-2xl bg-emerald-100 grid place-items-center mx-auto text-2xl">🏫</span><p class="font-extrabold text-lg mt-3">Belum ada data sarana</p></div><?php else: ?>
<div class="bg-white dark:bg-slate-800 border rounded-2xl overflow-hidden mt-4">
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[640px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50 dark:bg-slate-700/50"><th class="p-3 w-10">No</th><th class="p-3">Nama Sarana</th><th class="p-3 text-center">Luas / Jumlah</th><th class="p-3 text-center">Satuan</th><th class="p-3 text-center">Kondisi</th></tr>
<?php $no=1; foreach($rows as $r): ?>
<tr class="border-t hover:bg-emerald-50/60 dark:hover:bg-slate-700/40">
<td class="p-3 text-slate-400 font-bold"><?= $no++ ?></td>
<td class="p-3 font-extrabold"><?= Helper::e($r['name']) ?></td>
<td class="p-3 text-center font-bold"><?= Helper::e($r['qty']??'-') ?></td>
<td class="p-3 text-center"><?= Helper::e($r['unit']??'-') ?></td>
<td class="p-3 text-center"><span class="text-xs font-bold px-2.5 py-1 rounded-full <?= ($r['cond']??'baik')==='baik'?'bg-emerald-100 text-emerald-700':'bg-red-100 text-red-700' ?>"><?= ($r['cond']??'baik')==='baik'?'Baik':'Rusak' ?></span></td>
</tr>
<?php endforeach; ?>
</table></div></div>
<div class="grid md:grid-cols-3 gap-3 mt-4">
<div class="rounded-2xl border bg-white p-4 flex items-center gap-3"><span class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 grid place-items-center"><i class="fa fa-circle-check"></i></span><span><b class="block text-xl font-extrabold"><?= $baik ?></b><span class="text-xs text-slate-500">Kondisi Baik</span></span></div>
<div class="rounded-2xl border bg-white p-4 flex items-center gap-3"><span class="w-10 h-10 rounded-xl bg-red-100 text-red-700 grid place-items-center"><i class="fa fa-triangle-exclamation"></i></span><span><b class="block text-xl font-extrabold"><?= $rusak ?></b><span class="text-xs text-slate-500">Perlu Perbaikan</span></span></div>
<div class="rounded-2xl border bg-white p-4 flex items-center gap-3"><span class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 grid place-items-center"><i class="fa fa-building-columns"></i></span><span><b class="block text-xl font-extrabold"><?= count($rows) ?></b><span class="text-xs text-slate-500">Total Sarana</span></span></div>
</div>
<?php endif; ?>
</div>
<?php require ROOT."/templates/frontend/footer.php"; ?>
