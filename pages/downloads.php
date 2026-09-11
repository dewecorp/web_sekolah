<?php
declare(strict_types=1);
$metaTitle = 'Media Unduh - ' . Database::setting('school_name','Sekolah');
$q = trim($_GET['q'] ?? '');
try { $db->exec("CREATE TABLE IF NOT EXISTS downloads (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,title VARCHAR(255) NOT NULL,doc_type ENUM('file','link') NOT NULL DEFAULT 'file',filename VARCHAR(255) DEFAULT NULL,file_url VARCHAR(500) DEFAULT NULL,mime VARCHAR(100) DEFAULT NULL,extension VARCHAR(20) DEFAULT NULL,size_bytes INT UNSIGNED DEFAULT 0,download_count INT UNSIGNED DEFAULT 0,is_active TINYINT(1) NOT NULL DEFAULT 1,uploaded_by INT UNSIGNED DEFAULT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,INDEX idx_active (is_active),INDEX idx_type (doc_type)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"); } catch (Throwable) {}
$w = ['is_active=1']; $pr = [];
if ($q !== '') { $w[] = 'title LIKE ?'; $pr[] = "%$q%"; }
$st = $db->prepare("SELECT * FROM downloads WHERE " . implode(' AND ', $w) . " ORDER BY created_at DESC"); $st->execute($pr); $rows = $st->fetchAll();
function dlSize(int $b): string { if($b<1024) return $b.' B'; if($b<1024*1024) return round($b/1024,1).' KB'; return round($b/(1024*1024),1).' MB'; }
require ROOT.'/templates/frontend/header.php'; ?>
<div class="w-full px-4 md:px-8 py-10">
<?php
$heroBadge='<i class="fa fa-file-arrow-down text-amber-300"></i>Unduhan';
$heroTitle='Media Unduh';
$heroCrumb='<a href="'.Helper::url().'" class="hover:text-white">Beranda</a> / Media Unduh';
$heroTheme='emerald';
$heroStats=[['icon'=>'fa-file-lines','label'=>count($rows).' dokumen','solid'=>true]];
require ROOT.'/templates/frontend/page-hero.php'; ?>
<div class="mt-6 bg-white dark:bg-slate-800 border rounded-2xl p-5 md:p-6">
<form method="get" class="flex gap-2 mb-4 max-w-md"><input name="q" value="<?= Helper::e($q) ?>" placeholder="Cari dokumen..." class="border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-sm flex-1 bg-white dark:bg-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition placeholder:text-slate-400"><button class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 rounded-xl text-sm font-bold transition"><i class="fa fa-search mr-1"></i>Cari</button><a href="<?= Helper::url('media-unduh') ?>" class="px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-sm hover:border-emerald-300 transition">Reset</a></form>
<div class="overflow-x-auto">
<table class="w-full text-sm text-left">
<thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="p-3">No</th><th class="p-3">Nama Dokumen</th><th class="p-3">Ukuran</th><th class="p-3">Tanggal</th><th class="p-3">Aksi</th></tr></thead>
<tbody class="divide-y"><?php if(!$rows): ?><tr><td colspan="5" class="p-8 text-center text-slate-500">Belum ada dokumen unduhan.</td></tr><?php else: $no=0; foreach($rows as $r): $no++; ?>
<tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
<td class="p-3"><?= $no ?></td>
<?php $dlIcon=($r['doc_type']==='link')?'fa-link text-sky-600':(match($r['extension']??''){'pdf'=>'fa-file-pdf text-red-600','doc'=>'fa-file-word text-blue-600','docx'=>'fa-file-word text-blue-600','xls'=>'fa-file-excel text-green-600','xlsx'=>'fa-file-excel text-green-600','ppt'=>'fa-file-powerpoint text-orange-600','pptx'=>'fa-file-powerpoint text-orange-600','zip'=>'fa-file-zipper text-amber-600','rar'=>'fa-file-zipper text-amber-600',default=>'fa-file text-slate-500'}); ?>
<td class="p-3"><div class="flex items-center gap-2"><i class="fa <?= $dlIcon ?> text-lg"></i><div><p class="font-bold"><?= Helper::e($r['title']) ?></p><p class="text-xs text-slate-400"><?= Helper::e($r['extension'] ?: ($r['doc_type']==='link' ? 'URL' : 'file')) ?></p></div></div></td>
<td class="p-3 whitespace-nowrap"><?= $r['doc_type']==='file' ? dlSize((int)$r['size_bytes']) : '-' ?></td>
<td class="p-3 whitespace-nowrap"><?= Helper::tgl($r['created_at']) ?></td>
<td class="p-3"><?php
$rawUrl = $r['doc_type']==='file' ? Helper::url('assets/uploads/'.ltrim($r['filename'],'/')) : Helper::e($r['file_url']);
$ext = strtolower($r['extension'] ?? '');
$isPdf = ($r['doc_type']==='file' && $ext==='pdf');
$btnText = $r['doc_type']==='link' ? 'Buka' : ($isPdf ? 'Lihat / Cetak' : 'Unduh');
$btnIcon = $r['doc_type']==='link' ? 'fa-external-link-alt' : ($isPdf ? 'fa-print' : 'fa-download');
$btnUrl = $isPdf ? Helper::url('media-unduh/preview?file=' . urlencode($r['filename']) . '&name=' . urlencode($r['title'])) : $rawUrl;
?><a href="<?= $btnUrl ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-xs font-bold"><i class="fa <?= $btnIcon ?>"></i><?= $btnText ?></a></td>
</tr>
<?php endforeach; endif; ?></tbody>
</table>
</div>
</div>
</div>
<?php require ROOT.'/templates/frontend/footer.php'; ?>
