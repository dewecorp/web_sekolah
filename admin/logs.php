<?php declare(strict_types=1); Auth::requireRole(['administrator']); $title='Backup & Restore';
$dir = ROOT . '/database/backups';
if (!is_dir($dir)) @mkdir($dir, 0755, true);
$dbName = $DB->query("SELECT DATABASE()")->fetchColumn();

function backupList(string $dir): array {
  $o = [];
  foreach (glob($dir . '/backup-*.sql') ?: [] as $f) {
    $o[] = ['name' => basename($f), 'time' => filemtime($f), 'size' => filesize($f)];
  }
  usort($o, fn($a, $b) => $b['time'] <=> $a['time']);
  return $o;
}
function dumpDb(PDO $db, ?string &$err = null): ?string {
  try {
    $out = "-- SchoolCMS Backup " . date('c') . "\nSET FOREIGN_KEY_CHECKS=0;\n";
    $tables = [];
    foreach ($db->query("SHOW TABLES") as $r) $tables[] = array_values($r)[0];
    foreach ($tables as $t) {
      $c = $db->query("SHOW CREATE TABLE `$t`")->fetch();
      $out .= "\nDROP TABLE IF EXISTS `$t`;\n" . $c['Create Table'] . ";\n";
      foreach ($db->query("SELECT * FROM `$t`") as $row) {
        $vals = array_map(fn($v) => $v === null ? 'NULL' : $db->quote((string)$v), $row);
        $out .= "INSERT INTO `$t` (`" . implode('`,`', array_keys($row)) . "`) VALUES (" . implode(',', $vals) . ");\n";
      }
    }
    $out .= "\nSET FOREIGN_KEY_CHECKS=1;\n";
    return $out;
  } catch (Throwable $e) { $err = $e->getMessage(); return null; }
}

// Buat backup tersimpan
if (isset($_GET['create'])) {
  $err = null; $sql = dumpDb($DB, $err);
  if ($sql === null) Session::flash('err', 'Backup gagal: ' . $err);
  else {
    $fn = 'backup-' . date('Ymd-His') . '.sql';
    file_put_contents($dir . '/' . $fn, $sql);
    Auth::log($DB, 'backup', 'db', 'Buat backup ' . $fn);
    Session::flash('ok', 'Backup ' . $fn . ' tersimpan.');
  }
  header('Location: ' . Helper::url('admin/logs')); exit;
}
// Unduh file backup
if (isset($_GET['dl'])) {
  $fn = basename((string)$_GET['dl']);
  $f = $dir . '/' . $fn;
  if (!preg_match('/^backup-[\d\-]+\.sql$/', $fn) || !is_file($f)) { http_response_code(404); echo 'File tidak ada.'; exit; }
  Auth::log($DB, 'backup', 'db', 'Unduh ' . $fn);
  header('Content-Type: application/sql'); header('Content-Disposition: attachment; filename=' . $fn); header('Content-Length: ' . filesize($f));
  readfile($f); exit;
}
// Hapus file backup
if (isset($_GET['del'])) {
  if (!Security::verifyCsrf($_GET['csrf'] ?? null)) Session::flash('err', 'CSRF tidak valid.');
  else {
    $fn = basename((string)$_GET['del']);
    $f = $dir . '/' . $fn;
    if (preg_match('/^backup-[\d\-]+\.sql$/', $fn) && is_file($f)) { @unlink($f); Auth::log($DB, 'delete', 'db', 'Hapus ' . $fn); Session::flash('ok', $fn . ' dihapus.'); }
    else Session::flash('err', 'File tidak ada.');
  }
  header('Location: ' . Helper::url('admin/logs')); exit;
}
// Restore: upload .sql lalu eksekusi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['act'] ?? '') === 'restore') {
  if (!Security::verifyCsrf($_POST['csrf'] ?? null)) { Session::flash('err', 'CSRF tidak valid.'); header('Location: ' . Helper::url('admin/logs')); exit; }
  if (empty($_FILES['f']['name'] ?? '') || ($_FILES['f']['error'] ?? 4) !== 0) { Session::flash('err', 'Pilih file .sql dulu.'); header('Location: ' . Helper::url('admin/logs')); exit; }
  $ext = strtolower(pathinfo($_FILES['f']['name'], PATHINFO_EXTENSION));
  $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['f']['tmp_name']);
  if ($ext !== 'sql' || $_FILES['f']['size'] > 50 * 1024 * 1024) { Session::flash('err', 'Hanya file .sql max 50MB.'); header('Location: ' . Helper::url('admin/logs')); exit; }
  // Auto-backup aman sebelum restore
  $err = null; $pre = dumpDb($DB, $err);
  if ($pre !== null) file_put_contents($dir . '/backup-pre-restore-' . date('Ymd-His') . '.sql', $pre);
  $sql = file_get_contents($_FILES['f']['tmp_name']);
  if (str_contains($sql, 'DROP DATABASE') || str_contains($sql, 'CREATE DATABASE')) { Session::flash('err', 'File ditolak (ada DROP/CREATE DATABASE).'); header('Location: ' . Helper::url('admin/logs')); exit; }
  try {
    $DB->exec("SET FOREIGN_KEY_CHECKS=0");
    // pecah statement sederhana (abaikan ; di dalam string via parsing manual)
    $stmts = []; $buf = ''; $inS = false; $inD = false;
    for ($i = 0, $L = strlen($sql); $i < $L; $i++) {
      $ch = $sql[$i];
      if ($ch === "'" && !$inD && ($i === 0 || $sql[$i - 1] !== '\\')) $inS = !$inS;
      elseif ($ch === '"' && !$inS && ($i === 0 || $sql[$i - 1] !== '\\')) $inD = !$inD;
      $buf .= $ch;
      if ($ch === ';' && !$inS && !$inD) { $stmts[] = $buf; $buf = ''; }
    }
    if (trim($buf) !== '') $stmts[] = $buf;
    $n = 0;
    foreach ($stmts as $q) {
      $q = trim($q);
      if ($q === '' || str_starts_with($q, '--')) continue;
      $DB->exec($q); $n++;
    }
    $DB->exec("SET FOREIGN_KEY_CHECKS=1");
    Auth::log($DB, 'update', 'db', 'Restore dari upload (' . $n . ' query)');
    Session::flash('ok', "Restore berhasil ($n query). Backup otomatis pre-restore tersimpan.");
  } catch (Throwable $e) {
    try { $DB->exec("SET FOREIGN_KEY_CHECKS=1"); } catch (Throwable) {}
    Session::flash('err', 'Restore gagal: ' . $e->getMessage());
  }
  header('Location: ' . Helper::url('admin/logs')); exit;
}
$files = backupList($dir);
$fmtSize = fn($b) => $b < 1024 ? $b . ' B' : ($b < 1048576 ? round($b / 1024, 1) . ' KB' : round($b / 1048576, 2) . ' MB');
require ROOT . '/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-database text-emerald-600 mr-1"></i>Backup & Restore</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold"><?= Helper::e((string)$dbName) ?></span>
</div>
<div class="grid md:grid-cols-2 gap-3">
<div class="bg-white rounded-2xl border p-5">
<span class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-600 grid place-items-center text-xl"><i class="fa fa-floppy-disk"></i></span>
<h2 class="font-extrabold mt-2">Backup Database</h2>
<p class="text-xs text-slate-500 mt-1">Simpan salinan <b>.sql</b> lengkap (struktur + data) ke server. File tercatat di tabel bawah dan bisa diunduh kapan saja.</p>
<div class="flex gap-2 mt-3">
<a href="?create=1" class="bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl"><i class="fa fa-plus mr-1"></i>Buat Backup Sekarang</a>
<a href="<?= Helper::url('admin/logs') ?>" class="border text-sm px-4 py-2 rounded-xl"><i class="fa fa-rotate mr-1"></i>Muat Ulang</a>
</div></div>
<div class="bg-white rounded-2xl border p-5">
<span class="w-11 h-11 rounded-2xl bg-sky-100 text-sky-600 grid place-items-center text-xl"><i class="fa fa-upload"></i></span>
<h2 class="font-extrabold mt-2">Restore Database</h2>
<p class="text-xs text-slate-500 mt-1">Upload file <b>.sql</b> hasil backup. Sistem otomatis menyimpan <b>pre-restore backup</b> dulu agar aman.</p>
<form method="post" enctype="multipart/form-data" data-loading class="flex gap-2 mt-3 flex-wrap"><?= Security::csrfField() ?>
<input type="hidden" name="act" value="restore">
<input type="file" name="f" accept=".sql" required class="border rounded-xl p-2 text-sm flex-1 min-w-[180px]">
<button class="bg-sky-600 hover:bg-sky-500 text-white text-sm font-bold px-4 py-2 rounded-xl"><i class="fa fa-upload mr-1"></i>Restore</button></form>
</div></div>
<div class="bg-white rounded-2xl border overflow-hidden mt-3">
<div class="px-4 py-3 border-b flex items-center gap-2"><h2 class="font-bold text-sm"><i class="fa fa-box-archive text-slate-500 mr-1"></i>File Backup (<?= count($files) ?>)</h2><span class="ml-auto text-[11px] text-slate-400">database/backups/</span></div>
<div class="overflow-x-auto"><table class="w-full text-sm min-w-[640px]">
<tr class="text-left text-slate-500 text-xs uppercase bg-slate-50"><th class="p-3 w-12">No</th><th class="p-3">Nama Backup</th><th class="p-3">Tanggal</th><th class="p-3">Ukuran</th><th class="p-3 text-right">Aksi</th></tr>
<?php if (!$files): ?><tr><td colspan="5" class="p-10 text-center text-slate-500"><i class="fa fa-box-open text-3xl block mb-2"></i>Belum ada backup. Klik Buat Backup Sekarang.</td></tr><?php endif; ?>
<?php $no = 0; foreach ($files as $f): $no++; ?>
<tr class="border-t hover:bg-slate-50">
<td class="p-3 text-slate-400 font-mono"><?= $no ?></td>
<td class="p-3 font-semibold font-mono text-xs"><?= Helper::e($f['name']) ?></td>
<td class="p-3 text-xs text-slate-500"><?= Helper::e(date('d M Y H:i', $f['time'])) ?> <span class="text-slate-400">(<?= Helper::e(Helper::ago(date('Y-m-d H:i:s', $f['time']))) ?>)</span></td>
<td class="p-3"><span class="text-xs bg-slate-100 px-2 py-0.5 rounded-full font-bold"><?= Helper::e($fmtSize($f['size'])) ?></span></td>
<td class="p-3"><span class="flex gap-1 justify-end">
<a href="?dl=<?= urlencode($f['name']) ?>" class="w-8 h-8 inline-flex items-center justify-center rounded-lg border bg-white text-emerald-600 hover:border-emerald-400" title="Unduh" aria-label="Unduh"><i class="fa fa-download text-xs"></i></a>
<a href="?del=<?= urlencode($f['name']) ?>&csrf=<?= Security::csrfToken() ?>" data-confirm-del class="w-8 h-8 inline-flex items-center justify-center rounded-lg border bg-white text-red-600 hover:border-red-400" title="Hapus" aria-label="Hapus"><i class="fa fa-trash text-xs"></i></a>
</span></td></tr><?php endforeach; ?></table></div></div>
<script>
document.querySelectorAll('[data-confirm-del]')?.forEach(a=>{a.addEventListener('click',e=>{e.preventDefault();Swal.fire({title:'Hapus backup?',text:'File tidak bisa dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya, Hapus',cancelButtonText:'Batal'}).then(r=>{if(r.isConfirmed)location.href=a.href})})});
</script>
<?php require ROOT . '/templates/admin/footer.php'; ?>

