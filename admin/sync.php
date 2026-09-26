<?php declare(strict_types=1); $title='Sinkron SIMAD';

// Kunci setting endpoint SIMAD (domain bisa ganti tanpa bongkar backend)
const SIMAD_KEYS = ['simad_base_url','simad_api_key','simad_ep_guru','simad_ep_siswa','simad_ep_ekskul','simad_timeout'];

function simad_settings(PDO $db): array {
  $s=[];
  try { foreach($db->query("SELECT `key`,`value` FROM settings WHERE `key` LIKE 'simad\_%'") as $r) $s[$r['key']]=$r['value']; } catch(Throwable) {}
  return $s + ['simad_base_url'=>'http://simad.test','simad_api_key'=>'SIS_CENTRAL_HUB_SECRET_2026','simad_ep_guru'=>'/api/v1/teachers','simad_ep_siswa'=>'/api/v1/students','simad_ep_ekskul'=>'/api/v1/extracurriculars','simad_timeout'=>'20'];
}
function simad_save(PDO $db, array $in): void {
  $st=$db->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)");
  foreach(SIMAD_KEYS as $k) if(array_key_exists($k,$in)) $st->execute([$k,trim((string)$in[$k])]);
}
function simad_build_url(string $base, string $ep): string {
  $ep=trim($ep); if($ep==='') return '';
  $ep=(string)preg_replace('/\.php(?=[?#\/]|$)/i','',$ep);
  if(preg_match('~^https?://~i',$ep)) return $ep;
  return rtrim(trim($base),'/').'/'.ltrim($ep,'/');
}
function simad_final_url(string $base, string $ep, string $key): string {
  $url=simad_build_url($base,$ep); if($url==='') return '';
  $key=trim($key);
  if($key!==''&&stripos($url,'api_key=')===false) $url.=(str_contains($url,'?')?'&':'?').'api_key='.urlencode($key);
  return $url;
}
function simad_fetch(string $url, string $key, int $timeout): array {
  if(!preg_match('~^https?://[^\\s/$.?#].[^\\s]*$~i',$url)) return ['ok'=>false,'error'=>'URL tidak valid: '.$url];
  $ch=curl_init($url);
  $hdr=['Accept: application/json'];
  if($key!==''){ $hdr[]='Authorization: Bearer '.$key; $hdr[]='X-API-KEY: '.$key; }
  curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>max(5,$timeout),CURLOPT_CONNECTTIMEOUT=>10,CURLOPT_HTTPHEADER=>$hdr,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_MAXREDIRS=>3]);
  $body=curl_exec($ch); $err=curl_error($ch); $code=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch);
  if($body===false||$err!=='') return ['ok'=>false,'error'=>'cURL: '.$err,'code'=>$code];
  if($code<200||$code>=300) return ['ok'=>false,'error'=>'HTTP '.$code.': '.mb_substr((string)$body,0,200),'code'=>$code];
  $j=json_decode((string)$body,true);
  if(!is_array($j)) return ['ok'=>false,'error'=>'Respon bukan JSON valid','code'=>$code];
  return ['ok'=>true,'json'=>$j,'code'=>$code];
}
function simad_rows(mixed $j): array {
  if(!is_array($j)) return [];
  if(array_is_list($j)) return $j;
  foreach(['data','results','rows','items','payload'] as $k) if(isset($j[$k])&&is_array($j[$k])) return array_is_list($j[$k])?$j[$k]:simad_rows($j[$k]);
  foreach(['guru','teachers','siswa','students','ekskul','ekstrakurikuler','extracurriculars'] as $k) if(isset($j[$k])&&is_array($j[$k])) return array_is_list($j[$k])?$j[$k]:simad_rows($j[$k]);
  if(isset($j['data'])&&is_array($j['data'])) return simad_rows($j['data']);
  return [];
}
function simad_pick(array $r, array $keys, string $def=''): string {
  $low=[]; foreach($r as $k=>$v) $low[mb_strtolower((string)$k)]=$v;
  foreach($keys as $k){ if(!isset($low[$k])) continue; $v=$low[$k]; if(is_array($v)||is_object($v)) continue; if(trim((string)$v)!=='') return trim((string)$v); }
  return $def;
}
function simad_recount_kelas(PDO $db): void {
  try{
    $db->exec("UPDATE student_classes c SET n_l=(SELECT COUNT(*) FROM students s WHERE s.class_id=c.id AND s.gender='L'), n_p=(SELECT COUNT(*) FROM students s WHERE s.class_id=c.id AND s.gender='P')");
  }catch(Throwable){}
}
function simad_mark(PDO $db, string $key, int $added, int $updated, int $skipped): void {
  $db->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")
    ->execute([$key,date('d-m-Y H:i')." | +$added baru, $updated diperbarui".($skipped?", $skipped dilewati":'')]);
}
function simad_refresh_stats(PDO $db): array {
  try{ $nSiswa=(int)$db->query("SELECT COUNT(*) FROM students")->fetchColumn(); }catch(Throwable){ $nSiswa=0; }
  try{ $nGuru=(int)$db->query("SELECT COUNT(*) FROM teachers WHERE is_active=1")->fetchColumn(); }catch(Throwable){ $nGuru=0; }
  try{ $nEkskul=(int)$db->query("SELECT COUNT(*) FROM extracurriculars WHERE is_active=1")->fetchColumn(); }catch(Throwable){ $nEkskul=0; }
  try{ $nRombel=(int)$db->query("SELECT COUNT(*) FROM student_classes")->fetchColumn(); }catch(Throwable){ $nRombel=0; }
  try{
    $rows=$db->query("SELECT id,name FROM statistics")->fetchAll();
    $up=$db->prepare("UPDATE statistics SET value=? WHERE id=?");
    foreach($rows as $r){
      $n=mb_strtolower((string)($r['name']??''));
      $v=null;
      if(str_contains($n,'siswa')||str_contains($n,'peserta didik')||str_contains($n,'murid')||str_contains($n,'santri')||str_contains($n,'pelajar')) $v=$nSiswa;
      elseif(str_contains($n,'guru')||str_contains($n,'tendik')||str_contains($n,'pendidik')||str_contains($n,'pengajar')) $v=$nGuru;
      elseif(str_contains($n,'ekstra')||str_contains($n,'ekskul')) $v=$nEkskul;
      elseif(str_contains($n,'rombel')||str_contains($n,'rombongan')||$n==='kelas') $v=$nRombel;
      if($v!==null) $up->execute([$v,(int)$r['id']]);
    }
  }catch(Throwable){}
  try{
    $has=$db->query("SELECT COUNT(*) FROM school_profile")->fetchColumn();
    if($has) $db->prepare("UPDATE school_profile SET total_students=?,total_teachers=?,total_extracurricular=?")->execute([$nSiswa,$nGuru,$nEkskul]);
  }catch(Throwable){}
  return [$nSiswa,$nGuru,$nEkskul,$nRombel];
}

function simad_sync_guru(PDO $db, array $rows): array {
  $added=0;$updated=0;$skipped=0;
  $findNip=$db->prepare("SELECT id FROM teachers WHERE nip=? LIMIT 1");
  $findName=$db->prepare("SELECT id FROM teachers WHERE name=? LIMIT 1");
  $ins=$db->prepare("INSERT INTO teachers(name,nip,position,type,education,subject,description,is_active,sort_order) VALUES(?,?,?,?,?,?,?,1,0)");
  $upd=$db->prepare("UPDATE teachers SET name=?,position=?,type=?,education=?,subject=?,description=? WHERE id=?");
  foreach($rows as $r){
    if(!is_array($r)){ $skipped++; continue; }
    $nm=simad_pick($r,['nama_guru','name','nama','nama_lengkap','fullname','full_name']);
    if($nm===''){ $skipped++; continue; }
    $nip=simad_pick($r,['nuptk','nip','nik','no_induk','nomor_induk','kode_guru']);
    $pos=simad_pick($r,['jabatan','position','posisi']);
    if($pos===''&&simad_pick($r,['kelas_wali'])!=='') $pos='Wali Kelas '.simad_pick($r,['kelas_wali']);
    if($pos==='') $pos='Guru';
    $pl=mb_strtolower($pos);
    $type=(str_contains($pl,'tata usaha')||str_contains($pl,'tu ')||str_contains($pl,'operator')||str_contains($pl,'staff')||str_contains($pl,'tendik')||str_contains($pl,'penjaga')||str_contains($pl,'administrasi'))?'tendik':'guru';
    $subj='';
    foreach(['mengajar_list','mengajar','mapel','subject','bidang'] as $mk){
      if(!isset($r[$mk])){ foreach($r as $k=>$v){ if(mb_strtolower((string)$k)===mb_strtolower($mk)){ $r[$mk]=$v; break; } } }
      if(!isset($r[$mk])) continue;
      $v=$r[$mk];
      if(is_array($v)){ $v=array_filter(array_map(fn($x)=>is_array($x)?($x['nama']??$x['name']??''):trim((string)$x),$v)); if($v) $subj=implode(', ',$v); }
      elseif(trim((string)$v)!==''&&trim((string)$v)!=='[]'){ $t=trim((string)$v,'[]" '); if($t!=='') $subj='Kelas '.str_replace('","',', ',$t); }
      if($subj!=='') break;
    }
    if($subj===''&&simad_pick($r,['kelas_wali'])!=='') $subj='Wali Kelas '.simad_pick($r,['kelas_wali']);
    $edu=simad_pick($r,['pendidikan','education','ijazah']);
    $desc=simad_pick($r,['description','deskripsi','bio','profil']);
    if($desc===''&&simad_pick($r,['wali_kelas','kelas_wali'])!=='') $desc='Wali kelas '.simad_pick($r,['wali_kelas','kelas_wali']);
    $id=null;
    if($nip!==''&&$nip!=='-'){ $findNip->execute([$nip]); $id=$findNip->fetchColumn()?:null; }
    if(!$id&&($nip===''||$nip==='-')&&$nm!==''){ $findName->execute([$nm]); $id=$findName->fetchColumn()?:null; }
    if($id){ $upd->execute([$nm,$pos,$type,$edu,$subj,$desc,(int)$id]); $updated++; }
    else{ try{ $ins->execute([$nm,$nip!==''?$nip:null,$pos,$type,$edu,$subj,$desc]); $added++; }catch(Throwable){ $skipped++; } }
  }
  return [$added,$updated,$skipped];
}

function simad_sync_siswa(PDO $db, array $rows): array {
  $added=0;$updated=0;$skipped=0;
  $cols=[]; try{ $cols=$db->query("SHOW COLUMNS FROM students")->fetchAll(PDO::FETCH_COLUMN); }catch(Throwable){}
  $hasLevel=in_array('class_level',$cols,true); $hasMajor=in_array('major',$cols,true);
  $findClass=$db->prepare("SELECT id FROM student_classes WHERE name=? LIMIT 1");
  $mkClass=$db->prepare("INSERT INTO student_classes(name,n_l,n_p,sort_order) VALUES(?,0,0,0)");
  $findNis=$db->prepare("SELECT id FROM students WHERE nis IS NOT NULL AND nis<>'' AND nis=? LIMIT 1");
  $findNisn=$db->prepare("SELECT id FROM students WHERE nisn IS NOT NULL AND nisn<>'' AND nisn=? LIMIT 1");
  $findName=$db->prepare("SELECT id FROM students WHERE name=? LIMIT 1");
  foreach($rows as $r){
    if(!is_array($r)){ $skipped++; continue; }
    $nm=simad_pick($r,['nama_siswa','name','nama','nama_lengkap','fullname']);
    if($nm===''){ $skipped++; continue; }
    $nis=simad_pick($r,['nis','no_induk','nomor_induk','id_siswa']);
    $nisn=simad_pick($r,['nisn']);
    $g=mb_strtoupper(mb_substr(simad_pick($r,['gender','jk','jenis_kelamin','kelamin','sex']),0,1));
    $gender=in_array($g,['L','P'],true)?$g:null;
    if($gender===null){ $gl=mb_strtolower(simad_pick($r,['gender','jk','jenis_kelamin','kelamin'])); $gender=str_contains($gl,'perempuan')||str_contains($gl,'wanita')||$gl==='p'?'P':(str_contains($gl,'laki')||$gl==='l'?'L':null); }
    $cls=simad_pick($r,['class','kelas','class_name','nama_kelas','rombel','rombel_nama']);
    $cid=null;
    if($cls!==''){ $findClass->execute([$cls]); $cid=$findClass->fetchColumn()?:null;
      if(!$cid){ try{ $mkClass->execute([$cls]); $cid=(int)$db->lastInsertId(); }catch(Throwable){ $cid=null; } } }
    $id=null;
    if($nisn!==''){ $findNisn->execute([$nisn]); $id=$findNisn->fetchColumn()?:null; }
    if(!$id&&$nis!==''){ $findNis->execute([$nis]); $id=$findNis->fetchColumn()?:null; }
    if(!$id&&$nis===''&&$nisn===''&&$nm!==''){ $findName->execute([$nm]); $id=$findName->fetchColumn()?:null; }
    try{
      if($id){
        $s=$db->prepare("UPDATE students SET name=?,nis=?,nisn=?,class_id=?,gender=? WHERE id=?");
        $s->execute([$nm,$nis!==''?$nis:null,$nisn!==''?$nisn:null,$cid,$gender,(int)$id]);
        if($hasLevel||$hasMajor){
          $lv=simad_pick($r,['class_level','jenjang','tingkat']); $mj=simad_pick($r,['major','jurusan','program']);
          if($hasLevel&&$lv!=='') $db->prepare("UPDATE students SET class_level=? WHERE id=?")->execute([$lv,(int)$id]);
          if($hasMajor&&$mj!=='') $db->prepare("UPDATE students SET major=? WHERE id=?")->execute([$mj,(int)$id]);
        }
        $updated++;
      } else {
        $s=$db->prepare("INSERT INTO students(name,nis,nisn,class_id,gender,is_active,sort_order) VALUES(?,?,?,?,?,1,0)");
        $s->execute([$nm,$nis!==''?$nis:null,$nisn!==''?$nisn:null,$cid,$gender]);
        $nid=(int)$db->lastInsertId();
        if($hasLevel||$hasMajor){
          $lv=simad_pick($r,['class_level','jenjang','tingkat']); $mj=simad_pick($r,['major','jurusan','program']);
          if($hasLevel&&$lv!=='') $db->prepare("UPDATE students SET class_level=? WHERE id=?")->execute([$lv,$nid]);
          if($hasMajor&&$mj!=='') $db->prepare("UPDATE students SET major=? WHERE id=?")->execute([$mj,$nid]);
        }
        $added++;
      }
    }catch(Throwable){ $skipped++; }
  }
  simad_recount_kelas($db);
  return [$added,$updated,$skipped];
}

function simad_sync_ekskul(PDO $db, array $rows): array {
  $added=0;$updated=0;$skipped=0;
  $find=$db->prepare("SELECT id FROM extracurriculars WHERE name=? LIMIT 1");
  $ins=$db->prepare("INSERT INTO extracurriculars(name,description,coach,`day`,`time`,schedule,is_active,sort_order) VALUES(?,?,?,?,?,?,1,0)");
  $upd=$db->prepare("UPDATE extracurriculars SET description=?,coach=?,`day`=?,`time`=?,schedule=? WHERE id=?");
  $days=['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Ahad'];
  foreach($rows as $r){
    if(!is_array($r)){ $skipped++; continue; }
    $nm=simad_pick($r,['nama_ekstrakurikuler','name','nama','nama_ekskul','ekstrakurikuler']);
    if($nm===''){ $skipped++; continue; }
    $day=simad_pick($r,['hari','day']); $day=$day==='Ahad'?'Minggu':$day; $day=in_array($day,$days,true)?($day==='Ahad'?'Minggu':$day):null;
    $time=simad_pick($r,['waktu','time','jam','pukul']); if($time!=='') $time=mb_substr($time,0,5).':00';
    if(!preg_match('/^\d{2}:\d{2}(:\d{2})?$/',$time)) $time='';
    $coach='';
    if(isset($r['pembina'])&&is_array($r['pembina'])){ $cn=array_filter(array_map(fn($x)=>is_array($x)?($x['nama_pembina']??$x['nama']??$x['name']??''):trim((string)$x),$r['pembina'])); if($cn) $coach=implode(', ',$cn); }
    if($coach==='') $coach=simad_pick($r,['coach','pelatih','guru']);
    $desc=simad_pick($r,['description','deskripsi','keterangan']);
    $cnt=isset($r['jumlah_anggota'])?(int)$r['jumlah_anggota']:(isset($r['anggota'])&&is_array($r['anggota'])?count($r['anggota']):0);
    if($desc===''&&$cnt>0) $desc=$cnt.' anggota';
    $sched=simad_pick($r,['schedule','jadwal','lokasi','tempat','ruang']);
    if($sched===''&&$day!==null) $sched=$day.($time!==''?" jam ".mb_substr($time,0,5):'');
    if(isset($r['waktu_selesai'])&&trim((string)$r['waktu_selesai'])!==''&&$time!=='') $sched=($sched!==''?$sched.' ':($day??'')).'('.mb_substr($time,0,5).' - '.mb_substr(trim((string)$r['waktu_selesai']),0,5).')';
    $find->execute([$nm]); $id=$find->fetchColumn()?:null;
    try{
      if($id){ $upd->execute([$desc,$coach,$day,$time!==''?$time:null,$sched,(int)$id]); $updated++; }
      else{ $ins->execute([$nm,$desc,$coach,$day,$time!==''?$time:null,$sched]); $added++; }
    }catch(Throwable){ $skipped++; }
  }
  return [$added,$updated,$skipped];
}

$sets=simad_settings($db);
$isJson=($_GET['ajax']??'')!==''||str_contains($_SERVER['HTTP_ACCEPT']??'','application/json');

if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!Security::verifyCsrf($_POST['csrf']??null)){ if($isJson){ header('Content-Type: application/json'); echo json_encode(['ok'=>false,'msg'=>'CSRF tidak valid']); exit; } Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/sync')); exit; }
  $act=$_POST['act']??'save';
  if($act==='save'){
    $in=[]; foreach(SIMAD_KEYS as $k) $in[$k]=$_POST['s'][$k]??$sets[$k]??'';
    $in['simad_timeout']=(string)max(5,min(120,(int)($in['simad_timeout']??20)));
    simad_save($db,$in);
    Auth::log($db,'update','sync','Ubah endpoint SIMAD');
    if($isJson){ header('Content-Type: application/json'); echo json_encode(['ok'=>true]); exit; }
    Session::flash('ok','Endpoint SIMAD disimpan. Domain ganti tinggal ubah base URL.');
    header('Location: '.Helper::url('admin/sync')); exit;
  }
  $type=$_POST['type']??'';
  $map=['guru'=>['simad_ep_guru','simad_last_guru'],'siswa'=>['simad_ep_siswa','simad_last_siswa'],'ekskul'=>['simad_ep_ekskul','simad_last_ekskul']];
  $doOne=function(string $t) use ($db,$sets,$map){
    [$epK,$lastK]=$map[$t];
    $cur=simad_settings($db);
    $url=simad_final_url($cur['simad_base_url']??'',$cur[$epK]??'',$cur['simad_api_key']??'');
    if($url==='') return ['ok'=>false,'msg'=>'Endpoint '.$t.' kosong. Isi dulu.'];
    $f=simad_fetch($url,$cur['simad_api_key']??'',(int)($cur['simad_timeout']??20));
    if(!$f['ok']) return ['ok'=>false,'msg'=>$f['error']??'Fetch gagal'];
    $rows=simad_rows($f['json']);
    if(!$rows) return ['ok'=>false,'msg'=>'Respon JSON tidak berisi array data.','code'=>$f['code']??0];
    [$a,$u,$s]=match($t){ 'guru'=>simad_sync_guru($db,$rows), 'siswa'=>simad_sync_siswa($db,$rows), default=>simad_sync_ekskul($db,$rows) };
    simad_mark($db,$lastK,$a,$u,$s);
    [$nSiswa,$nGuru,$nEkskul,$nRombel]=simad_refresh_stats($db);
    Auth::log($db,'sync','sync',"Sinkron $t: +$a baru, $u update | stat: $nSiswa siswa, $nGuru guru, $nEkskul ekskul, $nRombel rombel");
    return ['ok'=>true,'added'=>$a,'updated'=>$u,'skipped'=>$s,'total'=>count($rows),'stats'=>['siswa'=>$nSiswa,'guru'=>$nGuru,'ekskul'=>$nEkskul,'rombel'=>$nRombel]];
  };
  if($act==='refresh_stats'){
    header('Content-Type: application/json');
    [$nSiswa,$nGuru,$nEkskul,$nRombel]=simad_refresh_stats($db);
    Auth::log($db,'sync','sync',"Refresh statistik: $nSiswa siswa, $nGuru guru, $nEkskul ekskul, $nRombel rombel");
    echo json_encode(['ok'=>true,'stats'=>['siswa'=>$nSiswa,'guru'=>$nGuru,'ekskul'=>$nEkskul,'rombel'=>$nRombel]]); exit;
  }
  if($act==='test'){
    $cur=simad_settings($db);
    if(!isset($map[$type])){ header('Content-Type: application/json'); echo json_encode(['ok'=>false,'msg'=>'Tipe tidak dikenal']); exit; }
    $url=simad_final_url($cur['simad_base_url']??'',$cur[$map[$type][0]]??'',$cur['simad_api_key']??'');
    $f=simad_fetch($url,$cur['simad_api_key']??'',(int)($cur['simad_timeout']??20));
    header('Content-Type: application/json');
    if(!$f['ok']){ echo json_encode(['ok'=>false,'msg'=>$f['error']??'Gagal','url'=>$url]); exit; }
    $rows=simad_rows($f['json']);
    echo json_encode(['ok'=>true,'url'=>$url,'code'=>$f['code']??200,'count'=>count($rows),'sample'=>array_slice($rows,0,2)]); exit;
  }
  if(in_array($act,['sync_guru','sync_siswa','sync_ekskul','sync_all'],true)){
    header('Content-Type: application/json');
    $targets=$act==='sync_all'?['guru','siswa','ekskul']:[str_replace('sync_','',$act)];
    $out=[];
    foreach($targets as $t) $out[$t]=$doOne($t);
    $ok=!in_array(false,array_column($out,'ok'),true);
    echo json_encode(['ok'=>$ok,'result'=>$out]); exit;
  }
}

$last=[]; foreach(['simad_last_guru'=>'guru','simad_last_siswa'=>'siswa','simad_last_ekskul'=>'ekskul'] as $k=>$t) $last[$t]=Database::setting($k,'Belum pernah sinkron');
$full=['guru'=>simad_final_url($sets['simad_base_url']??'',$sets['simad_ep_guru']??'',$sets['simad_api_key']??''),'siswa'=>simad_final_url($sets['simad_base_url']??'',$sets['simad_ep_siswa']??'',$sets['simad_api_key']??''),'ekskul'=>simad_final_url($sets['simad_base_url']??'',$sets['simad_ep_ekskul']??'',$sets['simad_api_key']??'')];
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-arrows-rotate text-emerald-600 mr-1"></i>Sinkron SIMAD</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold">guru • siswa • ekskul</span>
<?php $stPreview=[]; try{ foreach($db->query("SELECT name,value FROM statistics ORDER BY sort_order,id LIMIT 4") as $r) $stPreview[]=$r; }catch(Throwable){} ?>
<?php if($stPreview): ?><span class="text-[11px] bg-emerald-100 text-emerald-700 px-2.5 py-0.5 rounded-full font-bold">stat: <?= Helper::e(implode(' • ',array_map(fn($x)=>$x['name'].' '.$x['value'],$stPreview))) ?></span><?php endif; ?>
<button id="btnRefreshStats" class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-chart-simple mr-1"></i>Refresh Statistik</button>
<button id="btnSyncAll" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-xl shadow"><i class="fa fa-cloud-arrow-down mr-1"></i>Sinkron Semua</button>
</div>

<form method="post" data-loading class="bg-white rounded-2xl border p-4 grid md:grid-cols-2 gap-3 text-sm mb-3"><?= Security::csrfField() ?>
<input type="hidden" name="act" value="save">
<div class="grid gap-2">
<h2 class="font-bold"><i class="fa fa-plug text-emerald-600 mr-1"></i>Koneksi SIMAD</h2>
<label class="grid gap-1 font-semibold">Base URL SIMAD<input name="s[simad_base_url]" value="<?= Helper::e($sets['simad_base_url']??'') ?>" placeholder="https://simad.sekolah.sch.id" class="border rounded-lg p-2 font-normal"></label>
<span class="text-xs text-slate-400">Domain ganti tinggal ubah ini. Endpoint di bawah boleh relatif.</span>
<label class="grid gap-1 font-semibold">API Key / Token<input name="s[simad_api_key]" value="<?= Helper::e($sets['simad_api_key']??'') ?>" placeholder="Bearer token SIMAD" class="border rounded-lg p-2 font-normal font-mono"></label>
<label class="grid gap-1 font-semibold">Timeout (detik)<input type="number" name="s[simad_timeout]" min="5" max="120" value="<?= Helper::e($sets['simad_timeout']??'20') ?>" class="border rounded-lg p-2 font-normal"></label>
</div>
<div class="grid gap-2">
<h2 class="font-bold"><i class="fa fa-link text-emerald-600 mr-1"></i>Endpoint Masuk</h2>
<?php foreach(['simad_ep_guru'=>'Endpoint Guru','simad_ep_siswa'=>'Endpoint Siswa','simad_ep_ekskul'=>'Endpoint Ekstrakurikuler'] as $k=>$l): ?>
<label class="grid gap-1 font-semibold"><?= $l ?><input name="s[<?= $k ?>]" value="<?= Helper::e($sets[$k]??'') ?>" placeholder="/api/..." class="border rounded-lg p-2 font-normal font-mono"></label>
<?php endforeach; ?>
<span class="text-xs text-slate-400">Bisa path relatif (<code class="font-mono">/api/guru</code>) atau URL penuh. Tersimpan di DB, tanpa edit kode.</span>
</div>
<div class="md:col-span-2"><button class="w-full bg-slate-800 hover:bg-slate-700 text-white rounded-xl py-2 font-bold text-sm"><i class="fa fa-floppy-disk mr-1"></i>Simpan Endpoint</button></div>
</form>

<div class="grid md:grid-cols-3 gap-3 text-sm">
<?php foreach(['guru'=>['Guru & Staff','fa-chalkboard-user','admin/teachers'],'siswa'=>['Data Siswa','fa-user-graduate','admin/students'],'ekskul'=>['Ekstrakurikuler','fa-futbol','admin/ekskul']] as $t=>$m): ?>
<div class="bg-white rounded-2xl border p-4 grid gap-2 content-start">
<h2 class="font-bold"><i class="fa <?= $m[1] ?> text-emerald-600 mr-1"></i><?= $m[0] ?></h2>
<code class="font-mono text-[11px] bg-slate-50 border rounded-lg p-2 break-all"><?= Helper::e($full[$t]!==''?$full[$t]:'(endpoint belum diisi)') ?></code>
<div class="text-xs text-slate-500">Terakhir: <?= Helper::e($last[$t]) ?></div>
<div class="flex gap-2">
<button data-test="<?= $t ?>" class="flex-1 border rounded-xl py-2 font-bold hover:bg-slate-50"><i class="fa fa-plug-circle-check mr-1"></i>Tes</button>
<button data-sync="<?= $t ?>" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl py-2 font-bold"><i class="fa fa-download mr-1"></i>Sinkron</button>
<a href="<?= Helper::url($m[2]) ?>" class="border rounded-xl py-2 px-3 font-bold hover:bg-slate-50" title="Lihat data"><i class="fa fa-eye"></i></a>
</div>
<div class="text-xs result" id="res-<?= $t ?>"></div>
</div>
<?php endforeach; ?>
</div>
<div class="bg-slate-50 border rounded-2xl px-4 py-2.5 mt-3 text-xs text-slate-500">Key dikirim sebagai <code class="font-mono bg-white px-1.5 py-0.5 rounded border">?api_key=</code> + header <code class="font-mono bg-white px-1.5 py-0.5 rounded border">Authorization: Bearer</code> / <code class="font-mono bg-white px-1.5 py-0.5 rounded border">X-API-KEY</code>. Mapping field fleksibel: nama/name, nip/nuptk, kelas/class/rombel, pembina/coach otomatis dipetakan. Data cocok by NIP/NIS/nama → update bila ada, tambah bila baru.</div>

<script>
(function(){
  const CSRF='<?= Security::csrfToken() ?>';
  const post=async d=>{const f=new FormData();f.append('csrf',CSRF);Object.entries(d).forEach(([k,v])=>f.append(k,v));const r=await fetch('',{method:'POST',headers:{'Accept':'application/json'},body:f});return r.json()};
  const msg=(t,j)=>{const el=document.getElementById('res-'+t);if(!el)return;
    if(j.ok!==true&&(j.result===undefined)){el.innerHTML='<span class="text-red-600 font-bold">'+(j.msg||'Gagal')+'</span>';return}
    const r=j.result?j.result[t]:j;
    el.innerHTML=r&&r.ok?'<span class="text-emerald-700 font-bold">OK: +'+r.added+' baru, '+r.updated+' update'+(r.skipped?' ('+r.skipped+' lewati)':'')+'</span>':'<span class="text-red-600 font-bold">'+((r&&r.msg)||'Gagal')+'</span>'};
  document.querySelectorAll('[data-test]').forEach(b=>b.addEventListener('click',async()=>{
    const t=b.dataset.test,el=document.getElementById('res-'+t);el.textContent='Mengetes...';
    try{const j=await post({act:'test',type:t});el.innerHTML=j.ok?'<span class="text-emerald-700 font-bold">OK ('+j.code+'): '+j.count+' baris.</span>':'<span class="text-red-600 font-bold">'+(j.msg||'Gagal')+'</span>'}catch(e){el.innerHTML='<span class="text-red-600 font-bold">Gagal koneksi.</span>'}
  }));
  document.querySelectorAll('[data-sync]').forEach(b=>b.addEventListener('click',async()=>{
    const t=b.dataset.sync,el=document.getElementById('res-'+t);el.textContent='Menyinkron...';b.disabled=true;
    try{const j=await post({act:'sync_'+t});msg(t,j);if(j.ok)setTimeout(()=>location.reload(),1200)}catch(e){el.innerHTML='<span class="text-red-600 font-bold">Gagal koneksi.</span>'}b.disabled=false;
  }));
  document.getElementById('btnRefreshStats')?.addEventListener('click',async e=>{
    const b=e.currentTarget;b.disabled=true;const o=b.innerHTML;b.innerHTML='<i class="fa fa-spinner fa-spin mr-1"></i>Menghitung...';
    try{const j=await post({act:'refresh_stats'});if(j.ok){Swal.fire({icon:'success',title:'Statistik diperbarui',text:j.stats.siswa+' siswa, '+j.stats.guru+' guru, '+j.stats.ekskul+' ekskul, '+j.stats.rombel+' rombel.',timer:2000,showConfirmButton:false});setTimeout(()=>location.reload(),1200)}else Swal.fire('Gagal',j.msg||'Gagal','error')}
    catch(_){Swal.fire('Gagal','Koneksi gagal','error')}
    b.disabled=false;b.innerHTML=o;
  });
  document.getElementById('btnSyncAll')?.addEventListener('click',async e=>{
    const b=e.currentTarget;b.disabled=true;b.innerHTML='<i class="fa fa-spinner fa-spin mr-1"></i>Menyinkron...';
    try{const j=await post({act:'sync_all'});['guru','siswa','ekskul'].forEach(t=>msg(t,j));if(j.ok)setTimeout(()=>location.reload(),1500);else b.disabled=false}
    catch(_){b.disabled=false;b.innerHTML='<i class="fa fa-cloud-arrow-down mr-1"></i>Sinkron Semua'}
  });
})();
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>
