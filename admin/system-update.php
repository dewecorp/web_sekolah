<?php declare(strict_types=1); Auth::requireRole(['administrator']);
header('Content-Type: application/json');
$out = function(array $d){ echo json_encode($d, JSON_UNESCAPED_UNICODE); exit; };
if ($_SERVER['REQUEST_METHOD'] !== 'POST') $out(['ok'=>false,'msg'=>'Metode tidak valid.']);
if (!Security::verifyCsrf($_POST['csrf'] ?? null)) $out(['ok'=>false,'msg'=>'CSRF tidak valid.']);
$action = $_POST['sub'] ?? 'status';
$run = function(string $cmd, int $timeout = 60): array {
  $des = [1=>['pipe','w'],2=>['pipe','w']];
  $p = @proc_open($cmd, $des, $pipes, ROOT);
  if (!is_resource($p)) return ['code'=>1,'out'=>'','err'=>'gagal jalan'];
  stream_set_blocking($pipes[1], false); stream_set_blocking($pipes[2], false);
  $o=''; $e=''; $t=time();
  while (true) {
    $s=proc_get_status($p);
    $o.=stream_get_contents($pipes[1]); $e.=stream_get_contents($pipes[2]);
    if (!$s['running']) { $code=(int)$s['exitcode']; break; }
    if (time()-$t>$timeout) { proc_terminate($p); $code=124; break; }
    usleep(200000);
  }
  fclose($pipes[1]); fclose($pipes[2]); proc_close($p);
  return ['code'=>$code??1,'out'=>trim($o),'err'=>trim($e)];
};
$git = 'git -C '.escapeshellarg(ROOT).' -c safe.directory='.escapeshellarg(ROOT);
if ($action==='status') {
  $lock = is_file(ROOT.'/storage/update.lock');
  $b=$run($git.' rev-parse --abbrev-ref HEAD 2>&1',10); $branch=trim($b['out']);
  $c=$run($git.' rev-parse --short HEAD 2>&1',10);
  $r=$run($git.' status --porcelain=v1 --untracked-files=no 2>&1',15);
  $out(['ok'=>true,'lock'=>$lock,'branch'=>$branch!==''?$branch:'-','commit'=>trim($c['out'])!==''?trim($c['out']):'-','dirty'=>trim($r['out'])!=='','dirtyList'=>trim($r['out'])]);
}
if ($action==='check') {
  $f=$run($git.' fetch origin --prune 2>&1',60);
  if ($f['code']!==0) $out(['ok'=>false,'msg'=>'Gagal ambil info terbaru.','detail'=>mb_substr($f['out'].' '.$f['err'],0,500)]);
  $b=$run($git.' rev-parse --abbrev-ref HEAD 2>&1',10); $branch=trim(preg_replace('/[^A-Za-z0-9_.\/-]/','',explode("\n",$b['out'])[0]??'main')) ?: 'main';
  if (!in_array($branch,['main','master'],true)) $out(['ok'=>false,'msg'=>'Cabang tidak didukung ('.$branch.').']);
  $l=$run($git.' rev-parse HEAD 2>&1',10); $rm=$run($git.' rev-parse origin/'.escapeshellarg($branch).' 2>&1',10);
  $local=trim(explode("\n",$l['out'])[0]??''); $remote=trim(explode("\n",$rm['out'])[0]??'');
  if ($local===''||$remote==='') $out(['ok'=>false,'msg'=>'Tidak bisa baca versi.']);
  if ($local===$remote) $out(['ok'=>true,'uptodate'=>true,'msg'=>'Sudah versi terbaru.','local'=>$local,'remote'=>$remote]);
  $n=$run($git.' rev-list --count HEAD..origin/'.escapeshellarg($branch).' 2>&1',15);
  $out(['ok'=>true,'uptodate'=>false,'behind'=>max(0,(int)trim($n['out'])),'local'=>$local,'remote'=>$remote]);
}
if ($action==='apply') {
  $lockF=ROOT.'/storage/update.lock';
  if (!is_dir(ROOT.'/storage')) @mkdir(ROOT.'/storage',0755,true);
  $lf=@fopen($lockF,'c+');
  if (!$lf || !flock($lf,LOCK_EX|LOCK_NB)) $out(['ok'=>false,'msg'=>'Update sedang berjalan. Tunggu selesai.']);
  try {
    $b=$run($git.' rev-parse --abbrev-ref HEAD 2>&1',10); $branch=trim(preg_replace('/[^A-Za-z0-9_.\/-]/','',explode("\n",$b['out'])[0]??'main')) ?: 'main';
    if (!in_array($branch,['main','master'],true)) $out(['ok'=>false,'msg'=>'Cabang tidak didukung ('.$branch.').']);
    $r=$run($git.' status --porcelain=v1 --untracked-files=no 2>&1',15);
    if (trim($r['out'])!=='') $out(['ok'=>false,'msg'=>'Ada perubahan lokal belum tersimpan. Batalkan dulu via akses server.','detail'=>mb_substr($r['out'],0,400)]);
    $f=$run($git.' fetch origin --prune 2>&1',90);
    if ($f['code']!==0) $out(['ok'=>false,'msg'=>'Gagal ambil data terbaru.','detail'=>mb_substr($f['out'].' '.$f['err'],0,400)]);
    $diff=$run($git.' diff --name-only HEAD..origin/'.escapeshellarg($branch).' 2>&1',20);
    $files=array_values(array_filter(array_map('trim',explode("\n",$diff['out']??''))));
    $guard=['.env','.env.','config/database.php','config/constants.php','assets/uploads/','database/backups/','database/backups','storage/','storage'];
    foreach ($files as $fl) { foreach ($guard as $g) { if ($fl===$g||str_starts_with($fl,rtrim($g,'/').'/')) $out(['ok'=>false,'msg'=>'Update menyentuh file sensitif ('.$g.'). Batal demi keamanan, data hosting aman.']); } }
    $bk='pre-update-'.date('Ymd-His');
    $bdir=ROOT.'/database/backups';
    if (is_dir($bdir)) {
      $tables=[]; try { $tables=$db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN); } catch (Throwable) {}
      if ($tables) {
        $sql="-- backup otomatis sebelum update $bk\n";
        foreach ($tables as $tb) {
          if (!preg_match('/^[A-Za-z0-9_]+$/',$tb)) continue;
          $cr=$db->query("SHOW CREATE TABLE `$tb`")->fetch(); $sql.="DROP TABLE IF EXISTS `$tb`;\n".($cr['Create Table']??'').";\n";
          $cnt=(int)$db->query("SELECT COUNT(*) FROM `$tb`")->fetchColumn();
          if ($cnt>0 && $cnt<=20000) { foreach ($db->query("SELECT * FROM `$tb`") as $row) { $vs=array_map(fn($v)=>$v===null?'NULL':$db->quote((string)$v),$row); $sql.="INSERT INTO `$tb` VALUES(".implode(',',$vs).");\n"; } }
        }
        @file_put_contents($bdir.'/'.$bk.'.sql',$sql);
      }
    }
    $m=$run($git.' merge --ff-only origin/'.escapeshellarg($branch).' 2>&1',120);
    if ($m['code']!==0) $out(['ok'=>false,'msg'=>'Gagal terapkan update (bukan fast-forward).','detail'=>mb_substr($m['out'].' '.$m['err'],0,500)]);
    $nc=$run($git.' rev-parse --short HEAD 2>&1',10);
    Auth::log($db,'update','sistem','Update sistem ke '.trim($nc['out']).($bk?', backup '.$bk:''));
    $out(['ok'=>true,'msg'=>'Update berhasil ke '.trim($nc['out']).'.','backup'=>$bk]);
  } finally { flock($lf,LOCK_UN); fclose($lf); @unlink($lockF); }
}
$out(['ok'=>false,'msg'=>'Aksi tidak dikenal.']);
