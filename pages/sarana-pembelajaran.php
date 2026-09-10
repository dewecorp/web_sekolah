<?php if((Database::setting('learn_show','1')==='0')){ http_response_code(404); require ROOT.'/templates/error/404.php'; exit; }
$rows=$db->query("SELECT * FROM learning_facilities ORDER BY sort_order,id")->fetchAll();
$sTitle=Database::setting('learn_title','Sarana Pembelajaran'); if(trim($sTitle)==='')$sTitle='Sarana Pembelajaran'; $sDesc=Database::setting('learn_desc','');
$totBaik=array_sum(array_column($rows,'good_qty')); $totSed=array_sum(array_column($rows,'mid_qty')); $totRus=array_sum(array_column($rows,'bad_qty')); $totAll=$totBaik+$totSed+$totRus;
$metaTitle=$sTitle.' - '.Database::setting('school_name','Sekolah'); require ROOT."/templates/frontend/header.php";
$heroBadge='<i class="fa fa-chalkboard text-amber-300"></i>'.Helper::e($sTitle);
$heroTitle=$sTitle; $heroDesc=$sDesc;
$heroCrumb='<a href="'.Helper::url().'" class="hover:text-white">Beranda</a> / Sarana Pembelajaran';
$heroTheme='amber';
$heroStats=[['icon'=>'fa-calendar-day','label'=>Helper::pageDate('learning_facilities'),'solid'=>true],['icon'=>'fa-boxes-stacked','label'=>count($rows).' jenis','solid'=>false],['icon'=>'fa-circle-check','label'=>$totBaik.' baik','solid'=>false],['icon'=>'fa-triangle-exclamation','label'=>$totRus.' rusak','solid'=>false]];
require ROOT."/templates/frontend/page-hero.php"; ?>
<div class="w-full px-4 md:px-8 py-10">
<?php if(!$rows): ?><div class="bg-white border rounded-3xl p-12 text-center mt-4 text-slate-500"><span class="w-14 h-14 rounded-2xl bg-amber-100 grid place-items-center mx-auto text-2xl">🧰</span><p class="font-extrabold text-lg mt-3">Belum ada data</p></div><?php else: ?>
<div class="grid md:grid-cols-4 gap-3 mt-4">
<div class="rounded-2xl border bg-emerald-50 p-4 flex items-center gap-3"><span class="w-10 h-10 rounded-xl bg-emerald-600 text-white grid place-items-center"><i class="fa fa-circle-check"></i></span><span><b class="block text-xl font-extrabold"><?= $totBaik ?></b><span class="text-xs text-slate-500">Baik</span></span></div>
<div class="rounded-2xl border bg-amber-50 p-4 flex items-center gap-3"><span class="w-10 h-10 rounded-xl bg-amber-500 text-white grid place-items-center"><i class="fa fa-wrench"></i></span><span><b class="block text-xl font-extrabold"><?= $totSed ?></b><span class="text-xs text-slate-500">Sedang</span></span></div>
<div class="rounded-2xl border bg-red-50 p-4 flex items-center gap-3"><span class="w-10 h-10 rounded-xl bg-red-600 text-white grid place-items-center"><i class="fa fa-triangle-exclamation"></i></span><span><b class="block text-xl font-extrabold"><?= $totRus ?></b><span class="text-xs text-slate-500">Rusak</span></span></div>
<div class="rounded-2xl border bg-slate-900 text-white p-4 flex items-center gap-3"><span class="w-10 h-10 rounded-xl bg-white/20 grid place-items-center"><i class="fa fa-boxes-stacked"></i></span><span><b class="block text-xl font-extrabold"><?= $totAll ?></b><span class="text-xs text-white/70">Total</span></span></div>
</div>
<div class="bg-white dark:bg-slate-800 border rounded-2xl overflow-hidden mt-4">
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[640px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50 dark:bg-slate-700/50"><th class="p-3 w-10">No</th><th class="p-3">Nama Barang</th><th class="p-3 text-center">Baik</th><th class="p-3 text-center">Sedang</th><th class="p-3 text-center">Rusak</th><th class="p-3 text-center">Jumlah</th></tr>
<?php $no=1; foreach($rows as $r): ?>
<tr class="border-t hover:bg-amber-50/60 dark:hover:bg-slate-700/40">
<td class="p-3 text-slate-400 font-bold"><?= $no++ ?></td>
<td class="p-3 font-extrabold"><?= Helper::e($r['name']) ?></td>
<td class="p-3 text-center font-bold text-emerald-700"><?= (int)$r['good_qty'] ?></td>
<td class="p-3 text-center font-bold text-amber-700"><?= (int)$r['mid_qty'] ?></td>
<td class="p-3 text-center font-bold text-red-700"><?= (int)$r['bad_qty'] ?></td>
<td class="p-3 text-center font-extrabold"><?= (int)$r['total'] ?></td>
</tr>
<?php endforeach; ?>
</table></div></div>
<?php endif; ?>
</div>
<?php require ROOT."/templates/frontend/footer.php"; ?>
