<?php if((Database::setting('siswa_show','1')==='0')){ http_response_code(404); require ROOT.'/templates/error/404.php'; exit; }
$classes=$db->query("SELECT * FROM student_classes ORDER BY sort_order,id")->fetchAll();
$totL=array_sum(array_column($classes,'n_l')); $totP=array_sum(array_column($classes,'n_p'));
$sTitle=Database::setting('siswa_title','Data Siswa'); if(trim($sTitle)==='')$sTitle='Data Siswa'; $sDesc=Database::setting('siswa_desc','');
$sCols=Database::setting('siswa_cols','4'); if(!in_array($sCols,['2','3','4'],true))$sCols='4'; $sGrid=$sCols==='2'?'md:grid-cols-2':($sCols==='3'?'md:grid-cols-3':'sm:grid-cols-2 lg:grid-cols-4');
$metaTitle=$sTitle.' - '.Database::setting('school_name','Sekolah'); require ROOT."/templates/frontend/header.php";
$heroBadge='<i class="fa fa-user-graduate text-amber-300"></i>'.Helper::e($sTitle);
$heroTitle=$sTitle; $heroDesc=$sDesc;
$heroCrumb='<a href="'.Helper::url().'" class="hover:text-white">Beranda</a> / Siswa';
$heroTheme='sky';
$heroStats=[['icon'=>'fa-calendar-day','label'=>Helper::pageDate('student_classes'),'solid'=>true],['icon'=>'fa-users','label'=>($totL+$totP).' siswa','solid'=>false],['icon'=>'fa-mars','label'=>$totL.' putra','solid'=>false],['icon'=>'fa-venus','label'=>$totP.' putri','solid'=>false]];
require ROOT."/templates/frontend/page-hero.php"; ?>
<div class="w-full px-4 md:px-8 py-10">
<div class="flex justify-end"><div class="relative w-full sm:w-72"><i class="fa fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i><input id="qStu" placeholder="Cari kelas..." class="w-full border rounded-xl pl-10 pr-3 py-2.5 text-sm bg-white shadow-sm focus:ring-2 focus:ring-sky-500 focus:outline-none"></div></div>
<?php if(!$classes): ?><div class="bg-white border rounded-3xl p-12 text-center mt-4 text-slate-500"><span class="w-14 h-14 rounded-2xl bg-sky-100 grid place-items-center mx-auto text-2xl">🎒</span><p class="font-extrabold text-lg mt-3">Belum ada data siswa</p></div><?php else: ?>
<div class="mt-4 grid <?= $sGrid ?> gap-4" id="stuGrid">
<?php foreach($classes as $c): $nt=(int)$c['n_l']+(int)$c['n_p']; ?>
<article class="srow rounded-3xl overflow-hidden border bg-white dark:bg-slate-800 card-hover reveal" data-t="<?= Helper::e(strtolower($c['name'])) ?>">
<div class="bg-gradient-to-r from-sky-600 via-indigo-600 to-violet-600 p-4 text-white">
<h2 class="font-extrabold text-lg"><i class="fa fa-users-rectangle mr-1.5"></i><?= Helper::e($c['name']) ?></h2>
<span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-white text-indigo-700 mt-2 inline-block"><?= $nt ?> siswa</span>
</div>
<div class="grid grid-cols-3 text-center divide-x divide-slate-100">
<div class="p-3"><p class="text-xl font-extrabold text-sky-700"><?= (int)$c['n_l'] ?></p><p class="text-[11px] text-slate-500">Putra</p></div>
<div class="p-3"><p class="text-xl font-extrabold text-pink-700"><?= (int)$c['n_p'] ?></p><p class="text-[11px] text-slate-500">Putri</p></div>
<div class="p-3"><p class="text-xl font-extrabold"><?= $nt ?></p><p class="text-[11px] text-slate-500">Total</p></div>
</div>
</article>
<?php endforeach; ?>
</div>
<p id="stuEmpty" class="hidden text-center text-sm text-slate-500 py-8">Tidak cocok. Ubah kata kunci.</p>
<?php endif; ?>
</div>
<script>(function(){var q=document.getElementById('qStu'),rows=[...document.querySelectorAll('#stuGrid .srow')],em=document.getElementById('stuEmpty');q?.addEventListener('input',()=>{var s=(q.value||'').toLowerCase(),n=0;rows.forEach(r=>{var ok=!s||r.dataset.t.includes(s);r.style.display=ok?'':'none';if(ok)n++});if(em)em.classList.toggle('hidden',n>0)})})();</script>
<?php require ROOT."/templates/frontend/footer.php"; ?>
