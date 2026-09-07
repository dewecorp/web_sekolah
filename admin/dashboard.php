<?php
declare(strict_types=1);
$title = 'Dashboard';
$c = [];
foreach (['posts'=>"SELECT COUNT(*) FROM posts WHERE deleted_at IS NULL",'pages'=>"SELECT COUNT(*) FROM pages WHERE deleted_at IS NULL",'teachers'=>"SELECT COUNT(*) FROM teachers",'galleries'=>"SELECT COUNT(*) FROM galleries",'announcements'=>"SELECT COUNT(*) FROM announcements"] as $k=>$q) {
  try { $c[$k] = (int)$db->query($q)->fetchColumn(); } catch (Throwable) { $c[$k] = 0; }
}
// Retensi: aktivitas >24 jam hapus otomatis
try { $db->exec("DELETE FROM activity_logs WHERE created_at < NOW() - INTERVAL 24 HOUR"); } catch (Throwable) {}
try { $recent = $db->query("SELECT a.*, u.name uname FROM activity_logs a LEFT JOIN users u ON u.id=a.user_id ORDER BY a.created_at DESC LIMIT 50")->fetchAll(); } catch (Throwable) { $recent = []; }
try { $total24 = (int)$db->query("SELECT COUNT(*) FROM activity_logs")->fetchColumn(); } catch (Throwable) { $total24 = count($recent); }
try { $drafts = []; } catch (Throwable) { $drafts = []; }
try { $nDraft = 0; } catch (Throwable) { $nDraft = 0; }
try { $anns = $db->query("SELECT id,title,content,attachment,published_at,created_at FROM announcements WHERE status='published' ORDER BY published_at DESC, id DESC LIMIT 5")->fetchAll(); } catch (Throwable) { $anns = []; }
try { $nAnn = (int)$db->query("SELECT COUNT(*) FROM announcements WHERE status='published'")->fetchColumn(); } catch (Throwable) { $nAnn = count($anns); }
try { $nAnnFile = (int)$db->query("SELECT COUNT(*) FROM announcements WHERE status='published' AND attachment IS NOT NULL AND attachment<>''")->fetchColumn(); } catch (Throwable) { $nAnnFile = 0; }
try { $upcoming = $db->query("SELECT id,title,event_date,start_time,end_time,location,description FROM agenda WHERE event_date>=CURDATE() ORDER BY event_date LIMIT 5")->fetchAll(); } catch (Throwable) { $upcoming = []; }
try { $nAgenda = (int)$db->query("SELECT COUNT(*) FROM agenda WHERE event_date>=CURDATE()")->fetchColumn(); } catch (Throwable) { $nAgenda = count($upcoming); }
// Grafik berita 6 bulan terakhir
$labels = []; $dataPub = []; $dataDraft = [];
$blnId = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
for ($i = 5; $i >= 0; $i--) { $ts = strtotime("-$i month"); $m = date('Y-m', $ts); $labels[] = $blnId[(int)date('n', $ts)] . date(' y', $ts); }
try {
  $rows = $db->query("SELECT DATE_FORMAT(created_at,'%Y-%m') ym, status, COUNT(*) n FROM posts WHERE deleted_at IS NULL AND created_at >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 5 MONTH),'%Y-%m-01') GROUP BY ym, status")->fetchAll();
  $map = [];
  foreach ($rows as $r) $map[$r['ym']][$r['status']] = (int)$r['n'];
  for ($i = 5; $i >= 0; $i--) { $m = date('Y-m', strtotime("-$i month")); $dataPub[] = $map[$m]['published'] ?? 0; $dataDraft[] = $map[$m]['draft'] ?? 0; }
} catch (Throwable) { $dataPub = array_fill(0,6,0); $dataDraft = array_fill(0,6,0); }
$modIcon = ['posts'=>'fa-newspaper','pages'=>'fa-file-lines','categories'=>'fa-tags','media'=>'fa-photo-film','gallery'=>'fa-images','announcements'=>'fa-bullhorn','agenda'=>'fa-calendar-days','teachers'=>'fa-chalkboard-user','menus'=>'fa-list-ul','megamenu'=>'fa-layer-group','sliders'=>'fa-clone','widgets'=>'fa-puzzle-piece','sections'=>'fa-table-columns','settings'=>'fa-gear','users'=>'fa-users','logs'=>'fa-database','db'=>'fa-database','auth'=>'fa-key','extras'=>'fa-trophy'];
$modLabel = ['posts'=>'Berita','pages'=>'Halaman','categories'=>'Kategori','media'=>'Media','gallery'=>'Galeri','announcements'=>'Pengumuman','agenda'=>'Agenda','teachers'=>'Guru & Staff','menus'=>'Menu','megamenu'=>'Mega Menu','sliders'=>'Slider','widgets'=>'Widget','sections'=>'Section','settings'=>'Pengaturan','users'=>'User','logs'=>'Log','db'=>'Database','auth'=>'Autentikasi','extras'=>'Ekskul & Prestasi'];
$actLabel = ['create'=>'Menambah','update'=>'Mengubah','save'=>'Menyimpan','delete'=>'Menghapus','login'=>'Masuk','logout'=>'Keluar','backup'=>'Backup'];
$actColor = ['create'=>'bg-emerald-500','update'=>'bg-sky-500','save'=>'bg-sky-500','delete'=>'bg-red-500','login'=>'bg-emerald-500','logout'=>'bg-slate-400','backup'=>'bg-violet-500'];
$actBadge = ['create'=>'bg-emerald-100 text-emerald-700','update'=>'bg-sky-100 text-sky-700','save'=>'bg-sky-100 text-sky-700','delete'=>'bg-red-100 text-red-700','login'=>'bg-emerald-100 text-emerald-700','logout'=>'bg-slate-200 text-slate-600','backup'=>'bg-violet-100 text-violet-700'];
$totalAct = count($recent);
$uniqUser = $recent ? count(array_unique(array_map(fn($x)=>$x['uname']??'Sistem',$recent))) : 0;
$nAdd = 0; $nEdit = 0; $nDel = 0; $nLogin = 0;
foreach ($recent as $x) { $a = $x['action'] ?? ''; if ($a==='create') $nAdd++; elseif ($a==='update'||$a==='save') $nEdit++; elseif ($a==='delete') $nDel++; elseif ($a==='login') $nLogin++; }
require ROOT.'/templates/admin/header.php';
?>
<h1 class="text-xl font-extrabold mb-4">Dashboard</h1>
<div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
<?php foreach ([
  ['Berita',$c['posts'],'fa-newspaper','from-emerald-500 to-teal-600','admin/posts'],
  ['Halaman',$c['pages'],'fa-file-lines','from-sky-500 to-indigo-600','admin/pages'],
  ['Guru',$c['teachers'],'fa-chalkboard-user','from-amber-500 to-orange-600','admin/teachers'],
  ['Galeri',$c['galleries'],'fa-images','from-violet-500 to-purple-600','admin/gallery'],
  ['Pengumuman',$c['announcements'],'fa-bullhorn','from-rose-500 to-pink-600','admin/announcements'],
] as $s): ?>
<a href="<?= Helper::url($s[4]) ?>" class="bg-gradient-to-br <?= $s[3] ?> text-white rounded-2xl p-4 shadow hover:scale-[1.02] hover:shadow-lg transition">
<span class="w-9 h-9 rounded-xl bg-white/20 grid place-items-center"><i class="fa <?= $s[2] ?>"></i></span>
<p class="text-2xl font-extrabold mt-2"><?= $s[1] ?></p><p class="text-xs text-white/85"><?= $s[0] ?></p></a>
<?php endforeach; ?>
</div>
<div class="bg-white rounded-2xl border p-4 mt-3">
<div class="flex items-center justify-between mb-2"><h2 class="font-bold text-sm"><i class="fa fa-chart-column text-emerald-600 mr-1"></i>Grafik Berita (6 bulan)</h2><span class="text-[11px] text-slate-500">Published vs Draft</span></div>
<div class="h-64"><canvas id="newsChart"></canvas></div>
</div>
<div class="grid lg:grid-cols-2 gap-3 mt-3">
<div class="relative overflow-hidden rounded-2xl border p-4 bg-gradient-to-br from-rose-600 via-pink-600 to-fuchsia-600 text-white">
<span class="absolute -right-10 -top-12 w-44 h-44 rounded-full border-[18px] border-white/10"></span>
<span class="absolute -left-14 -bottom-16 w-52 h-52 rounded-full border-[22px] border-white/10"></span>
<div class="relative flex flex-wrap items-center gap-2 mb-2"><h2 class="font-bold text-sm"><i class="fa fa-bullhorn mr-1"></i>Pengumuman Terbaru</h2><span class="text-[11px] bg-white text-rose-700 px-2 py-0.5 rounded-full font-bold"><?= $nAnn ?> info</span><?php if($nAnnFile): ?><span class="text-[11px] bg-white/20 border border-white/30 px-2 py-0.5 rounded-full font-bold"><i class="fa fa-paperclip mr-1"></i><?= $nAnnFile ?> lampiran</span><?php endif; ?><a href="<?= Helper::url('admin/announcements') ?>" class="ml-auto text-[11px] bg-white/20 border border-white/30 px-2.5 py-1 rounded-lg font-bold hover:bg-white/30">Kelola →</a></div>
<?php if(!$anns): ?><div class="relative py-6 text-center"><span class="w-11 h-11 rounded-2xl bg-white/20 grid place-items-center mx-auto"><i class="fa fa-bell-slash"></i></span><p class="text-sm text-white/90 mt-2 font-semibold">Belum ada pengumuman</p></div>
<?php else: foreach($anns as $i=>$an): $dt=$an['published_at']??$an['created_at']; $hasFile=!empty($an['attachment']); ?>
<a href="<?= Helper::url('admin/announcements') ?>" class="relative flex gap-2.5 items-start bg-white text-slate-800 rounded-xl px-3 py-2.5 mb-2 hover:-translate-y-0.5 hover:shadow-lg transition">
<span class="w-9 h-9 rounded-xl text-white grid place-items-center shrink-0 <?= $i===0?'bg-gradient-to-b from-amber-400 to-orange-500':'bg-gradient-to-b from-rose-500 to-pink-600' ?>"><i class="fa <?= $i===0?'fa-star':'fa-bullhorn' ?> text-xs"></i></span>
<span class="min-w-0 flex-1"><span class="flex flex-wrap items-center gap-1.5"><b class="text-sm truncate flex-1 min-w-[120px]"><?= Helper::e($an['title']) ?></b><?php if($i===0): ?><span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">TERBARU</span><?php endif; ?><?php if($hasFile): ?><span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-sky-100 text-sky-700"><i class="fa fa-paperclip mr-0.5"></i>Lampiran</span><?php endif; ?></span>
<span class="text-[11px] text-slate-400 block mt-0.5"><i class="fa fa-clock mr-1"></i><?= Helper::e(Helper::ago($dt)) ?> • <?= Helper::e(Helper::tgl($dt)) ?></span>
<span class="text-xs text-slate-500 block truncate mt-0.5"><?= Helper::e(Helper::excerpt($an['content']??'',90)) ?></span></span>
<span class="self-center text-rose-500 font-bold text-sm shrink-0">→</span></a>
<?php endforeach; endif; ?></div>
<div class="bg-white rounded-2xl border p-4">
<div class="flex flex-wrap items-center gap-2 mb-2"><h2 class="font-bold text-sm"><i class="fa fa-calendar-days text-violet-500 mr-1"></i>Agenda Terdekat</h2><span class="text-[11px] bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full font-bold"><?= $nAgenda ?> agenda</span><a href="<?= Helper::url('admin/agenda') ?>" class="ml-auto text-[11px] text-emerald-600 font-bold">Kelola →</a></div>
<?php if(!$upcoming): ?><div class="py-6 text-center"><span class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-400 grid place-items-center mx-auto"><i class="fa fa-calendar-xmark"></i></span><p class="text-sm text-slate-500 mt-2 font-semibold">Belum ada agenda mendatang</p></div>
<?php else: foreach($upcoming as $a): $edEnd=(($a['end_date']??'')!=='')?$a['end_date']:$a['event_date']; $dd=(int)floor((strtotime($a['event_date'])-strtotime(date('Y-m-d')))/86400); $ddEnd=(int)floor((strtotime($edEnd)-strtotime(date('Y-m-d')))/86400); $when=$ddEnd<0?'Selesai':(($dd<=0&&$ddEnd>=0)?'Berlangsung':($dd===1?'Besok':($dd<=0?'Hari ini':"H-$dd"))); ?>
<div class="flex gap-2.5 items-start border border-slate-100 bg-slate-50 rounded-xl px-3 py-2.5 mb-2">
<span class="w-11 shrink-0 text-center bg-white border rounded-lg py-1"><b class="block text-base leading-none text-violet-600"><?= date('d',strtotime($a['event_date'])) ?></b><span class="text-[10px] text-slate-500 uppercase"><?= $blnId[(int)date('n',strtotime($a['event_date']))] ?></span></span>
<span class="min-w-0 flex-1"><b class="text-sm block truncate"><?= Helper::e($a['title']) ?></b>
<span class="text-[11px] text-slate-500 block mt-0.5"><i class="fa fa-calendar-day mr-1"></i><?= date('d-m-Y',strtotime($a['event_date'])) ?><?= ($edEnd!==$a['event_date'])?' – '.date('d-m-Y',strtotime($edEnd)):'' ?> • <i class="fa fa-location-dot mr-1"></i><?= Helper::e($a['location']?:'Lokasi TBD') ?><?php if(!empty($a['start_time'])): ?> • <i class="fa fa-clock mr-1"></i><?= Helper::e($a['start_time']) ?><?php endif; ?></span>
<?php if(!empty($a['description'])): ?><span class="text-xs text-slate-500 block truncate mt-0.5"><?= Helper::e($a['description']) ?></span><?php endif; ?></span>
<span class="text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0 <?= $dd===0?'bg-red-100 text-red-700':'bg-violet-100 text-violet-700' ?>"><?= $when ?></span></div>
<?php endforeach; endif; ?></div>
</div>
<div class="bg-white rounded-2xl border p-4 mt-3">
<div class="flex flex-wrap items-center gap-2 mb-3"><h2 class="font-bold text-sm"><i class="fa fa-clock-rotate-left text-sky-600 mr-1"></i>Aktivitas Terbaru</h2>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= $total24 ?> aktivitas 24 jam</span>
<?php if($recent): ?><span class="text-[11px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-bold">+<?= $nAdd ?> tambah</span><span class="text-[11px] bg-sky-100 text-sky-700 px-2 py-0.5 rounded-full font-bold">✎<?= $nEdit ?> ubah</span><span class="text-[11px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-bold">🗑<?= $nDel ?> hapus</span><span class="text-[11px] bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full font-bold"><?= $uniqUser ?> pengguna</span><?php endif; ?>
<span class="ml-auto text-[11px] text-slate-400"><i class="fa fa-trash-can mr-1"></i>Otomatis terhapus setelah 24 jam</span></div>
<?php if(!$recent): ?>
<div class="py-8 text-center"><span class="w-12 h-12 rounded-2xl bg-slate-100 grid place-items-center mx-auto text-slate-400"><i class="fa fa-mug-saucer text-xl"></i></span><p class="text-sm text-slate-500 mt-2 font-semibold">Belum ada aktivitas 24 jam terakhir</p><p class="text-xs text-slate-400">Setiap aksi admin tercatat di sini.</p></div>
<?php else: ?>
<div class="relative pl-10 max-h-[440px] overflow-y-auto pr-2 py-1">
<div class="absolute left-[15px] top-2 bottom-2 w-0.5 bg-gradient-to-b from-emerald-400 via-sky-400 to-violet-400 rounded-full"></div>
<?php $no=0; foreach($recent as $r): $no++; $dot = $actColor[$r['action']] ?? 'bg-slate-400'; $ic = $modIcon[$r['module']] ?? 'fa-circle-dot'; $ml = $modLabel[$r['module']] ?? $r['module']; $al = $actLabel[$r['action']] ?? $r['action']; $bg = $actBadge[$r['action']] ?? 'bg-slate-200 text-slate-600'; ?>
<div class="relative pb-4 last:pb-0">
<span class="absolute -left-10 top-0 w-8 h-8 rounded-full <?= $dot ?> text-white grid place-items-center ring-4 ring-white shadow"><i class="fa <?= $ic ?> text-xs"></i></span>
<div class="bg-slate-50 hover:bg-emerald-50/60 border border-slate-100 rounded-xl px-3 py-2 transition">
<p class="text-sm leading-snug"><span class="text-[11px] font-mono text-slate-400 mr-1">#<?= $no ?></span><b><?= Helper::e($r['uname']??'Sistem') ?></b> <span class="text-[11px] font-bold px-1.5 py-0.5 rounded-full <?= $bg ?>"><?= Helper::e($al) ?></span> <span class="text-slate-600"><?= Helper::e($r['description']?:$ml) ?></span></p>
<p class="text-[11px] text-slate-400 mt-1 flex flex-wrap gap-x-2"><span><i class="fa fa-clock mr-1"></i><?= Helper::e(Helper::ago($r['created_at'])) ?></span><span><i class="fa fa-folder-open mr-1"></i><?= Helper::e($ml) ?></span><?php if(!empty($r['ip'])): ?><span class="font-mono"><i class="fa fa-network-wired mr-1"></i><?= Helper::e($r['ip']) ?></span><?php endif; ?></p>
</div></div>
<?php endforeach; ?>
</div><?php endif; ?></div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('newsChart'),{type:'bar',
data:{labels:<?= json_encode($labels) ?>,datasets:[
{label:'Published',data:<?= json_encode($dataPub) ?>,backgroundColor:'#10b981',borderRadius:6},
{label:'Draft',data:<?= json_encode($dataDraft) ?>,backgroundColor:'#f59e0b',borderRadius:6}]},
options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true,ticks:{stepSize:1}}}}});
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>




