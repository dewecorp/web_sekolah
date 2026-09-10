<?php declare(strict_types=1); Auth::requireRole(['administrator','editor']); $title='Section Builder';
$types = ['hero'=>['Hero','fa-panorama'],'carousel'=>['Carousel','fa-clone'],'carousel-berita'=>['Carousel Berita','fa-clone'],'kategori-berita'=>['Kategori Berita','fa-tags'],'countdown'=>['Countdown Timer','fa-clock'],'image'=>['Image','fa-image'],'video'=>['Video','fa-video'],'audio'=>['Audio','fa-music'],'html'=>['Custom HTML','fa-code'],'sambutan'=>['Sambutan','fa-user-tie'],'statistik'=>['Statistik','fa-chart-simple'],'berita'=>['Berita','fa-newspaper'],'agenda'=>['Agenda','fa-calendar-days'],'pengumuman'=>['Pengumuman','fa-bullhorn'],'galeri'=>['Galeri','fa-images'],'guru'=>['Guru','fa-chalkboard-user'],'prestasi'=>['Prestasi','fa-trophy'],'ekskul'=>['Ekskul','fa-futbol'],'cta'=>['CTA','fa-bullhorn']];
$styles = ['default'=>'Default','card'=>'Card','minimal'=>'Minimal','gradient'=>'Grad Emerald','sunset'=>'Grad Sunset','ocean'=>'Grad Ocean','dark'=>'Dark','glass'=>'Glass','bordered'=>'Bordered','outline'=>'Outline','glow'=>'Glow','band'=>'Band','soft'=>'Soft','emerald-soft'=>'Emerald Soft','shadow'=>'Shadow'];
$stylePrev = ['default'=>'#f1f5f9','card'=>'#ffffff','minimal'=>'#f8fafc','gradient'=>'linear-gradient(90deg,#059669,#14b8a6)','sunset'=>'linear-gradient(90deg,#f97316,#f43f5e)','ocean'=>'linear-gradient(90deg,#0284c7,#4f46e5)','dark'=>'#0f172a','glass'=>'linear-gradient(90deg,#e2e8f080,#f8fafc80)','bordered'=>'#ecfdf5','outline'=>'#ecfdf5','glow'=>'#ffffff','band'=>'#059669','soft'=>'#f8fafc','emerald-soft'=>'#ecfdf5','shadow'=>'#ffffff'];
$bgs = ['white'=>'Putih','slate'=>'Slate','emerald-soft'=>'Emerald muda','emerald'=>'Emerald','dark'=>'Gelap','gradient-emerald'=>'Grad Emerald','gradient-indigo'=>'Grad Indigo','gradient-sunset'=>'Grad Sunset','gradient-ocean'=>'Grad Ocean','transparent'=>'Transparan'];
$bgPrev = ['white'=>'#ffffff','slate'=>'#f1f5f9','emerald-soft'=>'#d1fae5','emerald'=>'#047857','dark'=>'#0f172a','gradient-emerald'=>'linear-gradient(90deg,#059669,#14b8a6)','gradient-indigo'=>'linear-gradient(90deg,#4f46e5,#8b5cf6)','gradient-sunset'=>'linear-gradient(90deg,#f97316,#f43f5e)','gradient-ocean'=>'linear-gradient(90deg,#0284c7,#4f46e5)','transparent'=>'repeating-conic-gradient(#e5e7eb 0 25%,#fff 0 50%) 0 0/16px 16px'];
$effects = ['none'=>'Tanpa efek','fade-up'=>'Fade Up','fade-down'=>'Fade Down','fade-left'=>'Fade Kiri','fade-right'=>'Fade Kanan','zoom-in'=>'Zoom In','zoom-out'=>'Zoom Out','slide-left'=>'Slide Kiri','slide-right'=>'Slide Kanan','flip'=>'Flip','bounce-in'=>'Bounce In','rotate-in'=>'Rotate In','light-left'=>'Light Speed Kiri','light-right'=>'Light Speed Kanan','back-in'=>'Back In','roll-in'=>'Roll In','elastic-in'=>'Elastic In','swing-in'=>'Swing In','blur-in'=>'Blur Reveal','pop-in'=>'Pop In','skew-in'=>'Skew In'];
$effectPrev = ['none'=>'—','fade-up'=>'↑','fade-down'=>'↓','fade-left'=>'←','fade-right'=>'→','zoom-in'=>'⊕','zoom-out'=>'⊖','slide-left'=>'⇤','slide-right'=>'⇥','flip'=>'⇄','bounce-in'=>'⤴','rotate-in'=>'⟳'];
$effectPrev += ['light-left'=>'<<','light-right'=>'>>','back-in'=>'<)','roll-in'=>'@','elastic-in'=>'()','swing-in'=>'~','blur-in'=>'O','pop-in'=>'+','skew-in'=>'/'];
$grids = ['cards-2'=>'Kartu 2 Kolom','cards-3'=>'Kartu 3 Kolom','cards-4'=>'Kartu 4 Kolom','featured'=>'Sorotan + List','magazine'=>'Magazine Grid','masonry'=>'Masonry Cards','horizontal'=>'Kartu Horizontal','timeline'=>'Timeline Berita','list'=>'List Horizontal','overlay'=>'Overlay Gambar','minimal'=>'Minimal Teks'];
$edit = null;
if (isset($_GET['edit'])) { $s = $db->prepare("SELECT * FROM homepage_sections WHERE id=?"); $s->execute([(int)$_GET['edit']]); $edit = $s->fetch(); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!Security::verifyCsrf($_POST['csrf'] ?? null)) {
    if (($_POST['ajax'] ?? '') === '1') { header('Content-Type: application/json'); echo json_encode(['ok'=>false,'msg'=>'CSRF tidak valid']); exit; }
    Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/sections')); exit;
  }
  $act = $_POST['act'] ?? 'save';
  if ($act === 'video_title') {
    header('Content-Type: application/json');
    $u = trim((string)($_POST['url'] ?? ''));
    $emb = $u;
    if (preg_match('~(?:youtube\.com/(?:watch\?.*v=|shorts/|embed/)|youtu\.be/)([\w-]{6,})~', $u, $mm)) $emb = 'https://www.youtube.com/embed/' . $mm[1];
    elseif (preg_match('~dailymotion\.com/video/([\w]+)~', $u, $mm)) $emb = 'https://www.dailymotion.com/embed/video/' . $mm[1];
    elseif (preg_match('~dai\.ly/([\w]+)~', $u, $mm)) $emb = 'https://www.dailymotion.com/embed/video/' . $mm[1];
    elseif (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $u, $mm)) $emb = 'https://player.vimeo.com/video/' . $mm[1];
    $cands = [];
    if (preg_match('~youtube\.com/embed/([\w-]{6,})~', $emb, $mm)) { $cands[] = 'https://www.youtube.com/oembed?url=' . urlencode('https://www.youtube.com/watch?v=' . $mm[1]) . '&format=json'; $cands[] = 'https://noembed.com/embed?url=' . urlencode($u); }
    elseif (preg_match('~dailymotion\.com/embed/video/([\w]+)~', $emb, $mm)) { $cands[] = 'https://www.dailymotion.com/services/oembed?url=' . urlencode('https://www.dailymotion.com/video/' . $mm[1]) . '&format=json'; $cands[] = 'https://noembed.com/embed?url=' . urlencode($u); }
    elseif (preg_match('~player\.vimeo\.com/video/(\d+)~', $emb, $mm)) { $cands[] = 'https://vimeo.com/api/oembed.json?url=' . urlencode('https://vimeo.com/' . $mm[1]); $cands[] = 'https://noembed.com/embed?url=' . urlencode($u); }
    else $cands[] = 'https://noembed.com/embed?url=' . urlencode($u);
    $t = '';
    foreach ($cands as $api) {
      $ctx = stream_context_create(['http' => ['timeout' => 6, 'header' => "User-Agent: SchoolCMS/1.0\r\n"]]);
      $json = @file_get_contents($api, false, $ctx);
      if (!$json) continue;
      $d = json_decode($json, true);
      if (is_array($d) && !empty($d['title'])) { $t = trim((string)$d['title']); break; }
    }
    echo json_encode($t !== '' ? ['ok' => true, 'title' => $t] : ['ok' => false, 'msg' => 'Judul tidak ditemukan (video privat / dibatasi / tanpa oEmbed)']);
    exit;
  }
  if ($act === 'reorder') {    $ids = is_array($_POST['order'] ?? null) ? $_POST['order'] : explode(',', (string)($_POST['order'] ?? ''));
    $i = 1; foreach ($ids as $id) { if (!ctype_digit((string)$id)) continue; $db->prepare("UPDATE homepage_sections SET sort_order=? WHERE id=?")->execute([$i++, (int)$id]); }
    Auth::log($db,'update','sections','Reorder via drag-drop');
    if (($_POST['ajax'] ?? '') === '1') { header('Content-Type: application/json'); echo json_encode(['ok'=>true]); exit; }
    Session::flash('ok','Urutan disimpan.'); header('Location: '.Helper::url('admin/sections')); exit;
  }
  if ($act === 'quick_add') {
    $t = $_POST['type'] ?? 'custom'; if (!isset($types[$t])) $t = 'custom';
    $qp=in_array($_POST['sec_page']??'home',['home','berita'],true)?$_POST['sec_page']:'home';
    $key = $qp.'-'.$t.'-'.time();
    $pos = (int)($_POST['position'] ?? 0);
    if ($pos > 0) { $db->prepare("UPDATE homepage_sections SET sort_order=sort_order+1 WHERE page=? AND sort_order>=?")->execute([$qp,$pos]); $mx = $pos; }
    else { $st=$db->prepare("SELECT COALESCE(MAX(sort_order),0)+1 FROM homepage_sections WHERE page=?"); $st->execute([$qp]); $mx=(int)$st->fetchColumn(); }
    $db->prepare("INSERT INTO homepage_sections(section_key,page,type,title,subtitle,style,bg,padding,align,items_limit,sort_order,is_active,effect,grid) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
      ->execute([$key,$qp,$t,$types[$t][0].' Baru','', 'card','white','lg','left',3,$mx,1,'fade-up','cards-3']);
    $nid = (int)$db->lastInsertId();
    Auth::log($db,'create','sections',"Quick add $t");
    if (($_POST['ajax'] ?? '') === '1') { header('Content-Type: application/json'); echo json_encode(['ok'=>true,'id'=>$nid]); exit; }
    Session::flash('ok',$types[$t][0].' ditambah. Silakan edit.');
    header('Location: '.Helper::url('admin/sections?edit=').$nid); exit;
  }
  if ($act === 'duplicate') {
    $s = $db->prepare("SELECT * FROM homepage_sections WHERE id=?"); $s->execute([(int)$_POST['id']]); $r = $s->fetch();
    if ($r) {
      $nk = $r['section_key'].'-copy-'.time();
      $db->prepare("INSERT INTO homepage_sections(section_key,type,title,subtitle,content,image,btn_text,btn_url,btn_target,btn2_text,btn2_url,btn2_target,buttons_json,style,bg,padding,align,items_limit,sort_order,is_active,effect,grid) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute([$nk,$r['type'],$r['title'].' (Copy)',$r['subtitle'],$r['content'],$r['image'],$r['btn_text'],$r['btn_url'],$r['btn_target']??'_self',$r['btn2_text'],$r['btn2_url'],$r['btn2_target']??'_self',$r['buttons_json']??null,$r['style'],$r['bg'],$r['padding'],$r['align'],$r['items_limit'],(int)$r['sort_order']+1,(int)$r['is_active'],$r['effect']??'fade-up',$r['grid']??'cards-3']);
      $nid = (int)$db->lastInsertId();
      try {
        $ss = $db->prepare("SELECT * FROM section_slides WHERE section_id=? ORDER BY sort_order,id"); $ss->execute([(int)$r['id']]);
        $ins = $db->prepare("INSERT INTO section_slides(section_id,heading,subheading,image,cta_text,cta_url,cta2_text,cta2_url,sort_order,is_active) VALUES(?,?,?,?,?,?,?,?,?,?)");
        foreach ($ss->fetchAll() as $sl) $ins->execute([$nid, $sl['heading'], $sl['subheading'], $sl['image'], $sl['cta_text'], $sl['cta_url'], $sl['cta2_text'], $sl['cta2_url'], $sl['sort_order'], $sl['is_active']]);
      } catch (Throwable) {}
      Session::flash('ok','Section diduplikat (termasuk slide).');
    }
    header('Location: '.Helper::url('admin/sections')); exit;
  }
  if ($act === 'delete') { $db->prepare("DELETE FROM homepage_sections WHERE id=?")->execute([(int)$_POST['id']]); Auth::log($db,'delete','sections','Hapus section'); Session::flash('ok','Section dihapus.'); }
  elseif ($act === 'toggle') { $db->prepare("UPDATE homepage_sections SET is_active=1-is_active WHERE id=?")->execute([(int)$_POST['id']]); Session::flash('ok','Status diubah.'); }
  elseif ($act === 'slide_del') {
    $sid = (int)($_POST['section_id'] ?? 0);
    $s = $db->prepare("SELECT * FROM section_slides WHERE id=?"); $s->execute([(int)$_POST['slide_id']]); $sl = $s->fetch();
    if ($sl) {
      if (!empty($sl['subheading']) && !preg_match('~^https?://~i', (string)$sl['subheading'])) @unlink(ROOT . '/assets/uploads/' . basename((string)$sl['subheading']));
      if (!empty($sl['image']) && !preg_match('~^https?://~i', (string)$sl['image']) && is_file(ROOT . '/assets/uploads/' . basename((string)$sl['image']))) @unlink(ROOT . '/assets/uploads/' . basename((string)$sl['image']));
      $db->prepare("DELETE FROM section_slides WHERE id=?")->execute([$sl['id']]); Auth::log($db, 'delete', 'sections', 'Hapus video/slide'); Session::flash('ok', 'Video dihapus.');
    }
    header('Location: ' . Helper::url('admin/sections?edit=' . $sid)); exit;
  } elseif ($act === 'slide_save') {
    $sid = (int)($_POST['section_id'] ?? $edit['id'] ?? 0);
    if ($sid <= 0) { Session::flash('err', 'Simpan section dulu.'); header('Location: ' . Helper::url('admin/sections')); exit; }
    $st = $db->prepare("SELECT type FROM homepage_sections WHERE id=?"); $st->execute([$sid]); $stype = $st->fetchColumn();
    if ($stype !== 'carousel') { Session::flash('err', 'Slide hanya untuk widget Carousel. Hero cukup 1 gambar di tab Konten.'); header('Location: ' . Helper::url('admin/sections?edit=' . $sid)); exit; }
    // Kumpulkan semua file upload (dukung multi)
    $upFiles = [];
    if (!empty($_FILES['slide_img']['name'])) {
      if (is_array($_FILES['slide_img']['name'])) {
        $n = count($_FILES['slide_img']['name']);
        for ($i = 0; $i < $n; $i++) {
          if (empty($_FILES['slide_img']['name'][$i]) || ($_FILES['slide_img']['error'][$i] ?? 4) !== 0) continue;
          $upFiles[] = ['name' => $_FILES['slide_img']['name'][$i], 'type' => $_FILES['slide_img']['type'][$i] ?? '', 'tmp_name' => $_FILES['slide_img']['tmp_name'][$i], 'error' => $_FILES['slide_img']['error'][$i], 'size' => $_FILES['slide_img']['size'][$i]];
        }
      } elseif (($_FILES['slide_img']['error'] ?? 4) === 0) $upFiles[] = $_FILES['slide_img'];
    }
    foreach ($upFiles as $uf) {
      $e = Security::validImage($uf, $APP);
      if ($e) { Session::flash('err', $e . ' (' . $uf['name'] . ')'); header('Location: ' . Helper::url('admin/sections?edit=' . $sid)); exit; }
    }
    if (!empty($_POST['slide_id'])) {
      // Edit satu slide (pakai file pertama bila ada)
      $img = $_POST['old_slide_img'] ?? null;
      if ($upFiles) { $n = Security::safeName($upFiles[0]['name']); move_uploaded_file($upFiles[0]['tmp_name'], ROOT . '/assets/uploads/' . $n); $img = $n; }
      $db->prepare("UPDATE section_slides SET heading=?,subheading=?,image=COALESCE(?,image),cta_text=?,cta_url=?,cta2_text=?,cta2_url=?,sort_order=?,is_active=? WHERE id=? AND section_id=?")->execute([$_POST['slide_heading'] ?? '', $_POST['slide_sub'] ?? '', $img, $_POST['slide_cta'] ?? '', $_POST['slide_cta_url'] ?? '', $_POST['slide_cta2'] ?? '', $_POST['slide_cta2_url'] ?? '', (int)($_POST['slide_order'] ?? 0), !empty($_POST['slide_active']) ? 1 : 0, (int)$_POST['slide_id'], $sid]);
      Auth::log($db, 'update', 'sections', "Ubah slide #$sid"); Session::flash('ok', 'Slide diubah.');
    } else {
      if (!$upFiles) { Session::flash('err', 'Gambar slide wajib (bisa pilih banyak sekaligus).'); header('Location: ' . Helper::url('admin/sections?edit=' . $sid)); exit; }
      $mx = (int)$db->query("SELECT COALESCE(MAX(sort_order),0) FROM section_slides WHERE section_id=$sid")->fetchColumn();
      $n = 0;
      foreach ($upFiles as $k => $uf) {
        $fn = Security::safeName($uf['name']); move_uploaded_file($uf['tmp_name'], ROOT . '/assets/uploads/' . $fn);
        $hd = $_POST['slide_heading'] ?? '';
        if (count($upFiles) > 1 && $hd !== '') $hd .= ' (' . ($k + 1) . ')';
        $db->prepare("INSERT INTO section_slides(section_id,heading,subheading,image,cta_text,cta_url,cta2_text,cta2_url,sort_order,is_active) VALUES(?,?,?,?,?,?,?,?,?,1)")->execute([$sid, $hd, $_POST['slide_sub'] ?? '', $fn, $_POST['slide_cta'] ?? '', $_POST['slide_cta_url'] ?? '', $_POST['slide_cta2'] ?? '', $_POST['slide_cta2_url'] ?? '', ++$mx]);
        $n++;
      }
      Auth::log($db, 'create', 'sections', "Tambah $n slide #$sid"); Session::flash('ok', "$n slide ditambah.");
    }
    header('Location: ' . Helper::url('admin/sections?edit=' . $sid)); exit;
  } elseif ($act === 'slide_del') {
    $sid = (int)($_POST['section_id'] ?? 0);
    $s = $db->prepare("SELECT * FROM section_slides WHERE id=?"); $s->execute([(int)$_POST['slide_id']]); $sl = $s->fetch();
    if ($sl) { $db->prepare("DELETE FROM section_slides WHERE id=?")->execute([$sl['id']]); Auth::log($db, 'delete', 'sections', 'Hapus slide'); Session::flash('ok', 'Slide dihapus.'); }
    header('Location: ' . Helper::url('admin/sections?edit=' . $sid)); exit;
  } elseif ($act === 'slide_toggle') {
    $sid = (int)($_POST['section_id'] ?? 0);
    $db->prepare("UPDATE section_slides SET is_active=1-is_active WHERE id=?")->execute([(int)$_POST['slide_id']]);
    Session::flash('ok', 'Status slide diubah.');
    if (($_POST['ajax'] ?? '') === '1') { header('Content-Type: application/json'); echo json_encode(['ok' => true]); exit; }
    header('Location: ' . Helper::url('admin/sections?edit=' . $sid)); exit;
  }
  else {
    $key = Security::slug(trim($_POST['section_key'] ?? $_POST['title'] ?? 'section'));
    if ($key === '') { Session::flash('err','Key/judul wajib.'); header('Location: '.Helper::url('admin/sections')); exit; }
    $stype = $_POST['type'] ?? 'custom';
    $img = $_POST['old_image'] ?? ($edit['image'] ?? null);
    if ($stype === 'video') $img = null;
    elseif (!empty($_FILES['image']['name'] ?? '')) {
      $e = Security::validImage($_FILES['image'], $APP);
      if ($e) { Session::flash('err',$e); header('Location: '.Helper::url('admin/sections')); exit; }
      $n = Security::safeName($_FILES['image']['name']); move_uploaded_file($_FILES['image']['tmp_name'], ROOT.'/assets/uploads/'.$n); $img = $n;
    }
    if (!empty($_POST['clear_image'])) $img = null;
    // Tombol dinamis: btns_text[]/btns_url[] + btns_blank[] (index yg tab baru)
    $bTexts = (array)($_POST['btns_text'] ?? []);
    $bUrls = (array)($_POST['btns_url'] ?? []);
    $bBlanks = array_map('strval', (array)($_POST['btns_blank'] ?? []));
    $buttons = [];
    $nBtn = max(count($bTexts), count($bUrls));
    for ($bi = 0; $bi < $nBtn; $bi++) {
      $bt = trim((string)($bTexts[$bi] ?? '')); $bu = trim((string)($bUrls[$bi] ?? ''));
      if ($bt === '' && $bu === '') continue;
      $buttons[] = ['text' => $bt, 'url' => $bu, 'target' => in_array((string)$bi, $bBlanks, true) ? '_blank' : '_self'];
      if (count($buttons) >= 5) break;
    }
    $buttonsJson = $buttons ? json_encode($buttons) : null;
    if(in_array($stype,['berita','kategori-berita','carousel-berita'],true)){
      $nc=(string)($_POST['news_cat']??'');
      $car=['limit'=>max(1,min(10,(int)($_POST['car_limit']??5))),'delay'=>max(1000,min(20000,(int)($_POST['car_delay']??5000))),'mh'=>max(150,min(420,(int)($_POST['car_mh']??200))),'dh'=>max(180,min(520,(int)($_POST['car_dh']??270))),'auto'=>!empty($_POST['car_auto'])?1:0,'dots'=>!empty($_POST['car_dots'])?1:0];
      $bjM=['news_cat'=>$nc,'car'=>$car]; if(!empty($buttonsJson)){ $jb2=json_decode($buttonsJson,true); if(is_array($jb2))$bjM=array_merge($jb2,$bjM); } $buttonsJson=json_encode($bjM);
    }
     $b1 = $buttons[0] ?? ['text' => '', 'url' => '', 'target' => '_self'];
    $b2 = $buttons[1] ?? ['text' => '', 'url' => '', 'target' => '_self'];
     if(($_POST['type']??'')==='countdown'){
      $cdT=trim((string)($_POST['cd_target']??'')); if($cdT!=='')$_POST['content']=str_replace('T',' ',$cdT);
      $cdBg=trim((string)($_POST['old_cd_bg']??''));
      if(!empty($_FILES['cd_bg']['name']??'')){ $e=Security::validImage($_FILES['cd_bg'],$APP); if($e){ Session::flash('err',$e); header('Location: '.Helper::url('admin/sections')); exit; } $n=Security::safeName($_FILES['cd_bg']['name']); move_uploaded_file($_FILES['cd_bg']['tmp_name'],ROOT.'/assets/uploads/'.$n); if($cdBg!==''&&$cdBg!==$n)@unlink(ROOT.'/assets/uploads/'.basename($cdBg)); $cdBg=$n; }
      if(!empty($_POST['cd_bg_clear'])){ if($cdBg!=='')@unlink(ROOT.'/assets/uploads/'.basename($cdBg)); $cdBg=''; }
      $bjC=['cd_msg'=>trim((string)($_POST['cd_msg']??'Acara telah dimulai!')),'cd_hide'=>!empty($_POST['cd_hide_zero']),'cd_info'=>trim((string)($_POST['cd_info']??'')),'cd_bg'=>$cdBg]; if(!empty($buttonsJson)){ $jb2=json_decode($buttonsJson,true); if(is_array($jb2)) $bjC=array_merge($jb2,$bjC); } $buttonsJson=json_encode($bjC);
      $img=$_POST['cd_style']??'glass';
    }
    if(($_POST['type']??'')==='cta'){
      $ctaBg=trim((string)($_POST['old_cta_bg']??''));
      if(!empty($_FILES['cta_bg']['name']??'')){ $e=Security::validImage($_FILES['cta_bg'],$APP); if($e){ Session::flash('err',$e); header('Location: '.Helper::url('admin/sections')); exit; } $n=Security::safeName($_FILES['cta_bg']['name']); move_uploaded_file($_FILES['cta_bg']['tmp_name'],ROOT.'/assets/uploads/'.$n); if($ctaBg!==''&&$ctaBg!==$n)@unlink(ROOT.'/assets/uploads/'.basename($ctaBg)); $ctaBg=$n; }
      if(!empty($_POST['cta_bg_clear'])){ if($ctaBg!=='')@unlink(ROOT.'/assets/uploads/'.basename($ctaBg)); $ctaBg=''; }
      $ctaStyle=in_array($_POST['cta_style']??'gradient',['gradient','dark','light','image','glass'],true)?$_POST['cta_style']:'gradient';
      $ctaBtn=in_array($_POST['cta_btn']??'white',['white','emerald','dark','outline','glass'],true)?$_POST['cta_btn']:'white';
      $ctaHover=in_array($_POST['cta_btn_hover']??'auto',['auto','darken','lift','glow','zoom'],true)?$_POST['cta_btn_hover']:'auto';
      $ctaAlign=in_array($_POST['cta_align']??'center',['left','center'],true)?$_POST['cta_align']:'center';
      $bjT=['cta_bg'=>$ctaBg,'cta_style'=>$ctaStyle,'cta_btn'=>$ctaBtn,'cta_btn_hover'=>$ctaHover,'cta_align'=>$ctaAlign]; if(!empty($buttonsJson)){ $jb2=json_decode($buttonsJson,true); if(is_array($jb2))$bjT=array_merge($jb2,$bjT); } $buttonsJson=json_encode($bjT);
    }
    $imgFx=$_POST['img_fx'] ?? 'none';
    $imgSizeOpts=array_keys(['thumb'=>1,'sm'=>1,'md'=>1,'lg'=>1,'full'=>1,'orig'=>1,'lead'=>1,'wide'=>1,'banner'=>1,'ads'=>1,'sq'=>1,'port'=>1,'story'=>1,'custom'=>1]);
    $imgSize=in_array($_POST['img_size']??'full',$imgSizeOpts,true)?$_POST['img_size']:'full';
    $imgCw=max(0,min(2400,(int)($_POST['img_cw']??0))); $imgCh=max(0,min(1600,(int)($_POST['img_ch']??0)));
    if(($_POST['type']??'')==='galeri'){
      $imgFx=in_array($_POST['gal_anim']??'none',['marquee','marquee-alt'],true)?$_POST['gal_anim']:'none';
    }
    $imgHover=$_POST['img_hover'] ?? 'none';
    if(($_POST['type']??'')==='galeri'){
      $imgHover=!empty($_POST['gal_bubble'])?'bubble':'none';
      $gSp=in_array($_POST['gal_speed']??'normal',['slow','normal','fast'],true)?$_POST['gal_speed']:'normal';
      $gPh=in_array($_POST['gal_photo']??'md',['sm','md','lg'],true)?$_POST['gal_photo']:'md';
      $imgSize=$gSp.'|'.$gPh;
    }
    if(($_POST['type']??'')==='sambutan'){
      $wpos=in_array($_POST['wpos']??'left',['left','right','top'],true)?$_POST['wpos']:'left';
      $wphoto=in_array($_POST['wphoto']??'bulat',['kotak','rounded','bulat','lingkaran','kartu','bingkai'],true)?$_POST['wphoto']:'bulat';
      $wtext=in_array($_POST['wtext']??'default',['default','card','quote','center'],true)?$_POST['wtext']:'default';
      $_POST['grid']=$wpos; $imgSize=$wphoto;
      $bjW=['wtext'=>$wtext]; if(!empty($buttonsJson)){ $jb2=json_decode($buttonsJson,true); if(is_array($jb2))$bjW=array_merge($jb2,$bjW); } $buttonsJson=json_encode($bjW);
    }
    $secPage=in_array($_POST['sec_page']??'home',['home','berita'],true)?$_POST['sec_page']:'home';
    $d = [$_POST['type'] ?? 'custom', $_POST['title'] ?? '', $_POST['subtitle'] ?? '', $_POST['content'] ?? '', $img, $b1['text'], $b1['url'], $b1['target'], $b2['text'], $b2['url'], $b2['target'], $buttonsJson, $_POST['style'] ?? 'default', $_POST['bg'] ?? 'white', $_POST['padding'] ?? 'lg', $_POST['align'] ?? 'left', (int)($_POST['items_limit'] ?? 3), (int)($_POST['sort_order'] ?? 0), !empty($_POST['is_active']) ? 1 : 0, $_POST['effect'] ?? 'fade-up', $_POST['grid'] ?? 'cards-3', $imgFx, $imgSize, $imgCw?:null, $imgCh?:null, $imgHover, $secPage];
    try {
      if (!empty($_POST['id'])) { $db->prepare("UPDATE homepage_sections SET type=?,title=?,subtitle=?,content=?,image=?,btn_text=?,btn_url=?,btn_target=?,btn2_text=?,btn2_url=?,btn2_target=?,buttons_json=?,style=?,bg=?,padding=?,align=?,items_limit=?,sort_order=?,is_active=?,effect=?,grid=?,img_fx=?,img_size=?,img_cw=?,img_ch=?,img_hover=?,page=? WHERE id=?")->execute([...$d, (int)$_POST['id']]); Auth::log($db,'update','sections',"Ubah $key"); $secId=(int)$_POST['id']; }
      else { $db->prepare("INSERT INTO homepage_sections(section_key,page,type,title,subtitle,content,image,btn_text,btn_url,btn_target,btn2_text,btn2_url,btn2_target,buttons_json,style,bg,padding,align,items_limit,sort_order,is_active,effect,grid,img_fx,img_size,img_cw,img_ch,img_hover) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute([$key, ...$d]); $secId=(int)$db->lastInsertId(); Auth::log($db,'create','sections',"Tambah $key"); }
      if ($stype === 'video' && $secId > 0) {
        $normUrl = function(string $u): string {
          $u = trim($u);
          if ($u === '') return '';
          if (preg_match('~(?:youtube\.com/(?:watch\?.*v=|shorts/|embed/)|youtu\.be/)([\w-]{6,})~', $u, $m)) return 'https://www.youtube.com/embed/' . $m[1];
          if (preg_match('~dailymotion\.com/video/([\w]+)~', $u, $m)) return 'https://www.dailymotion.com/embed/video/' . $m[1];
          if (preg_match('~dai\.ly/([\w]+)~', $u, $m)) return 'https://www.dailymotion.com/embed/video/' . $m[1];
          if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $u, $m)) return 'https://player.vimeo.com/video/' . $m[1];
          return $u;
        };
        $fetchTitle = function(string $embedUrl, string $origUrl): string {
          $cands = [];
          if (preg_match('~youtube\.com/embed/([\w-]{6,})~', $embedUrl, $m)) { $cands[] = 'https://www.youtube.com/oembed?url=' . urlencode('https://www.youtube.com/watch?v=' . $m[1]) . '&format=json'; $cands[] = 'https://noembed.com/embed?url=' . urlencode($origUrl); }
          elseif (preg_match('~dailymotion\.com/embed/video/([\w]+)~', $embedUrl, $m)) { $cands[] = 'https://www.dailymotion.com/services/oembed?url=' . urlencode('https://www.dailymotion.com/video/' . $m[1]) . '&format=json'; $cands[] = 'https://noembed.com/embed?url=' . urlencode($origUrl); }
          elseif (preg_match('~player\.vimeo\.com/video/(\d+)~', $embedUrl, $m)) { $cands[] = 'https://vimeo.com/api/oembed.json?url=' . urlencode('https://vimeo.com/' . $m[1]); $cands[] = 'https://noembed.com/embed?url=' . urlencode($origUrl); }
          else $cands[] = 'https://noembed.com/embed?url=' . urlencode($origUrl !== '' ? $origUrl : $embedUrl);
          foreach ($cands as $api) {
            $ctx = stream_context_create(['http' => ['timeout' => 6, 'header' => "User-Agent: SchoolCMS/1.0\r\n"]]);
            $json = @file_get_contents($api, false, $ctx);
            if (!$json) continue;
            $dd = json_decode($json, true);
            if (is_array($dd) && !empty($dd['title'])) return trim((string)$dd['title']);
          }
          return '';
        };
        $ids = (array)($_POST['vids_id'] ?? []);
        $titles = (array)($_POST['vids_title'] ?? []);
        $urls = (array)($_POST['vids_url'] ?? []);
        $fetched = (array)($_POST['vids_fetched'] ?? []);
        $files = [];
        if (!empty($_FILES['vids_file']['name']) && is_array($_FILES['vids_file']['name'])) {
          $n = count($_FILES['vids_file']['name']);
          for ($i = 0; $i < $n; $i++) {
            if (empty($_FILES['vids_file']['name'][$i]) || (($_FILES['vids_file']['error'][$i] ?? 4) !== 0)) { $files[$i] = null; continue; }
            $files[$i] = ['name' => $_FILES['vids_file']['name'][$i], 'tmp_name' => $_FILES['vids_file']['tmp_name'][$i], 'error' => $_FILES['vids_file']['error'][$i], 'size' => $_FILES['vids_file']['size'][$i]];
          }
        }
        $keep = [];
        $nRow = max(count($ids), count($titles), count($urls));
        $mx = (int)$db->query("SELECT COALESCE(MAX(sort_order),0) FROM section_slides WHERE section_id=$secId")->fetchColumn();
        for ($i = 0; $i < $nRow; $i++) {
          $vid = (int)($ids[$i] ?? 0);
          $old = null;
          if ($vid > 0) { $os = $db->prepare("SELECT * FROM section_slides WHERE id=? AND section_id=?"); $os->execute([$vid, $secId]); $old = $os->fetch(); if (!$old) continue; }
          $fileUrl = $old['subheading'] ?? '';
          if (!empty($files[$i])) {
            $e = Security::validVideo($files[$i], $APP);
            if ($e) { Session::flash('err', $e); header('Location: ' . Helper::url('admin/sections?edit=' . $secId)); exit; }
            $fn = Security::safeName($files[$i]['name']); move_uploaded_file($files[$i]['tmp_name'], ROOT . '/assets/uploads/' . $fn);
            if ($old && !empty($old['subheading']) && !preg_match('~^https?://~i', (string)$old['subheading'])) @unlink(ROOT . '/assets/uploads/' . basename((string)$old['subheading']));
            $fileUrl = $fn;
          }
          if (!empty($_POST['vids_clear'][$i])) { if ($old && !empty($old['subheading']) && !preg_match('~^https?://~i', (string)$old['subheading'])) @unlink(ROOT . '/assets/uploads/' . basename((string)$old['subheading'])); $fileUrl = ''; }
          $rawUrl = trim((string)($urls[$i] ?? ''));
          $linkUrl = $normUrl($rawUrl);
          if ($fileUrl === '' && ($linkUrl === '' || !preg_match('~^https?://~i', $linkUrl))) continue;
          $hd = trim((string)($titles[$i] ?? ''));
          if ($hd === '') $hd = trim((string)($fetched[$i] ?? ''));
          if ($hd === '' && $linkUrl !== '') $hd = $fetchTitle($linkUrl, $rawUrl);
          if ($hd === '' && $fileUrl !== '') $hd = pathinfo($fileUrl, PATHINFO_FILENAME);
          if ($vid > 0) { $db->prepare("UPDATE section_slides SET heading=?,image=?,subheading=? WHERE id=? AND section_id=?")->execute([$hd, $linkUrl, $fileUrl, $vid, $secId]); $keep[] = $vid; }
          else { $db->prepare("INSERT INTO section_slides(section_id,heading,subheading,image,sort_order,is_active) VALUES(?,?,?,?,?,1)")->execute([$secId, $hd, $fileUrl, $linkUrl, ++$mx]); $keep[] = (int)$db->lastInsertId(); }
        }
        if ($keep) { $ph = implode(',', array_fill(0, count($keep), '?')); $db->prepare("DELETE FROM section_slides WHERE section_id=? AND id NOT IN ($ph)")->execute([$secId, ...$keep]); }
        else $db->prepare("DELETE FROM section_slides WHERE section_id=?")->execute([$secId]);
      }
      Session::flash('ok','Section disimpan.');
    } catch (Throwable $e) { Session::flash('err','Gagal: key sudah dipakai.'); }
  }
  $backTab=in_array($_POST['sec_page']??'home',['home','berita'],true)?$_POST['sec_page']:'home'; header('Location: '.Helper::url('admin/sections?tab='.$backTab)); exit;
}
if(!isset($secTab))$secTab='home'; $secTab=in_array($_GET['tab']??$secTab,['home','berita'],true)?$_GET['tab']??$secTab:'home';
if(!empty($edit['page']))$secTab=$edit['page'];
$st=$db->prepare("SELECT s.*,(SELECT COUNT(*) FROM section_slides WHERE section_id=s.id) slide_cnt FROM homepage_sections s WHERE s.page=? ORDER BY s.sort_order,s.id"); $st->execute([$secTab]); $rows=$st->fetchAll();
$pageLinks = $db->query("SELECT title,slug FROM pages WHERE status='published' AND deleted_at IS NULL ORDER BY title LIMIT 100")->fetchAll();
// Tombol dinamis inspector: dari buttons_json, fallback ke btn_text/btn2 lama
$editButtons = [];
if (!empty($edit['buttons_json'])) { $jb = json_decode($edit['buttons_json'], true); if (is_array($jb)) foreach ($jb as $b) { if (!empty($b['text']) || !empty($b['url'])) $editButtons[] = ['text' => $b['text'] ?? '', 'url' => $b['url'] ?? '', 'target' => ($b['target'] ?? '_self') === '_blank' ? '_blank' : '_self']; } }
if (!$editButtons) {
  if (!empty($edit['btn_text']) || !empty($edit['btn_url'])) $editButtons[] = ['text' => $edit['btn_text'], 'url' => $edit['btn_url'], 'target' => $edit['btn_target'] ?? '_self'];
  if (!empty($edit['btn2_text']) || !empty($edit['btn2_url'])) $editButtons[] = ['text' => $edit['btn2_text'], 'url' => $edit['btn2_url'], 'target' => $edit['btn2_target'] ?? '_self'];
}
if (!$editButtons) $editButtons[] = ['text' => '', 'url' => '', 'target' => '_self']; // default 1 tombol kosong
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-1">
<h1 class="text-xl font-extrabold"><i class="fa fa-pen-ruler text-emerald-600 mr-1"></i>Section Builder</h1>
<div class="flex gap-1 bg-slate-100 rounded-lg p-1 text-xs font-bold"><a href="?tab=home" class="px-3 py-1.5 rounded-md <?= $secTab==='home'?'bg-white shadow':'text-slate-500' ?>"><i class="fa fa-home mr-1"></i>Landing</a><a href="?tab=berita" class="px-3 py-1.5 rounded-md <?= $secTab==='berita'?'bg-white shadow':'text-slate-500' ?>"><i class="fa fa-newspaper mr-1"></i>Berita</a></div>
<a href="<?= Helper::url($secTab==='berita'?'berita':'') ?>" target="_blank" rel="noopener noreferrer" class="ml-auto text-sm px-3 py-1.5 border rounded-lg bg-white"><i class="fa fa-eye mr-1"></i>Live Preview</a>
</div>
<p class="text-xs text-slate-500 mb-3">Drag widget ke kanvas (atau klik) • drag kartu untuk urutkan • klik kartu untuk edit • hero upload gambar • carousel auto-slide.</p>
<div class="bg-white rounded-2xl border p-3 mb-3">
<p class="text-xs font-bold uppercase text-slate-400 mb-2"><i class="fa fa-plus mr-1"></i>Widget — drag ke kanvas / klik</p>
<div class="grid grid-cols-4 sm:grid-cols-6 gap-2" id="palette">
<?php foreach($types as $k=>$v): ?>
<form method="post" class="contents pal-form" data-type="<?= $k ?>"><?= Security::csrfField() ?><input type="hidden" name="act" value="quick_add"><input type="hidden" name="type" value="<?= $k ?>"><input type="hidden" name="sec_page" value="<?= $secTab ?>">
<button draggable="true" data-ptype="<?= $k ?>" class="pal-btn border rounded-xl p-2.5 text-center hover:border-emerald-500 hover:bg-emerald-50 transition cursor-grab active:cursor-grabbing w-full"><i class="fa <?= $v[1] ?> text-lg text-emerald-600 pointer-events-none"></i><span class="block text-[11px] font-semibold mt-1 pointer-events-none"><?= $v[0] ?></span></button>
</form><?php endforeach; ?>
</div></div>
<div class="grid lg:grid-cols-5 gap-3 items-start">
<div class="lg:col-span-3 bg-white rounded-2xl border p-3">
<p class="text-xs font-bold uppercase text-slate-400 mb-2"><i class="fa fa-grip-vertical mr-1"></i>Kanvas — drop di sini (<?= count($rows) ?>)</p>
<div id="canvas" class="grid gap-2 min-h-[120px] rounded-xl border-2 border-dashed border-transparent p-1 transition">
<?php if(!$rows): ?><p class="text-sm text-slate-500 text-center py-8" id="emptyCanvas">Kanvas kosong. Drag widget ke sini.</p><?php endif; ?>
<?php foreach($rows as $r): ?>
<div draggable="true" data-id="<?= $r['id'] ?>" data-edit="?tab=<?= $secTab ?>&edit=<?= $r['id'] ?>" class="sec-card border rounded-xl p-2.5 flex items-center gap-2.5 bg-slate-50 hover:border-emerald-400 cursor-pointer transition <?= $r['is_active']?'':'opacity-60' ?>">
<span class="text-slate-300 cursor-grab px-1 drag-handle"><i class="fa fa-grip-vertical"></i></span>
<span class="w-9 h-9 rounded-lg grid place-items-center shrink-0 text-white" style="background:<?= Helper::e($bgPrev[$r['bg']] ?? '#059669') ?>;<?= in_array($r['bg'],['white','slate','transparent','emerald-soft'],true)?'color:#059669;border:1px solid #a7f3d0;':'' ?>"><i class="fa <?= $types[$r['type']][1] ?? 'fa-cube' ?>"></i></span>
<span class="min-w-0 flex-1"><b class="text-sm block truncate"><?= Helper::e($r['title'] ?: $types[$r['type']][0] ?? $r['type']) ?></b>
<span class="text-[11px] text-slate-500 font-mono"><?= Helper::e($r['section_key']) ?> • <?= Helper::e($r['style']) ?>/<?= Helper::e($r['bg']) ?> • #<?= (int)$r['sort_order'] ?></span>
<span class="block h-1.5 rounded-full mt-1" style="background:<?= Helper::e($stylePrev[$r['style']] ?? '#e2e8f0') ?>"></span></span>
<span class="flex gap-1 shrink-0">
<form method="post"><input type="hidden" name="csrf" value="<?= Security::csrfToken() ?>"><input type="hidden" name="act" value="duplicate"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white hover:text-sky-600" title="Duplikat"><i class="fa fa-copy text-xs"></i></button></form>
<form method="post"><input type="hidden" name="csrf" value="<?= Security::csrfToken() ?>"><input type="hidden" name="act" value="toggle"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white <?= $r['is_active']?'text-emerald-600':'text-slate-400' ?>" title="On/Off"><i class="fa <?= $r['is_active']?'fa-eye':'fa-eye-slash' ?> text-xs"></i></button></form>
<form method="post" data-confirm><?= Security::csrfField() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="w-8 h-8 border rounded-lg grid place-items-center bg-white text-red-600" title="Hapus"><i class="fa fa-trash text-xs"></i></button></form>
</span></div>
<?php endforeach; ?>
</div></div>
<?php
$editSlides = [];
if (!empty($edit['id'])) { $ss = $db->prepare("SELECT * FROM section_slides WHERE section_id=? ORDER BY sort_order,id"); $ss->execute([(int)$edit['id']]); $editSlides = $ss->fetchAll(); }
$isCarousel = ($edit['type'] ?? '') === 'carousel';
$isVideo = ($edit['type'] ?? '') === 'video';
$useSlides = $isCarousel;
?>
<div class="lg:col-span-2 bg-white rounded-2xl border p-4 lg:sticky lg:top-16">
<h2 class="font-bold text-sm mb-2"><i class="fa fa-sliders mr-1 text-emerald-600"></i><?= $edit ? 'Inspector: '.Helper::e($edit['section_key']) : 'Inspector' ?></h2>
<?php $tabCols=3; ?>
<div class="grid grid-cols-<?= $tabCols ?> gap-1 mb-3 bg-slate-100 rounded-lg p-1 text-xs font-bold" data-tabs>
<button type="button" data-tab="konten" class="tab-btn px-2 py-1.5 rounded-md bg-white shadow">Konten</button>
<?php if($isCarousel): ?><button type="button" data-tab="slide" class="tab-btn px-2 py-1.5 rounded-md text-slate-500">Slide<?= $editSlides ? ' (' . count($editSlides) . ')' : '' ?></button><?php endif; ?>
<button type="button" data-tab="gaya" class="tab-btn px-2 py-1.5 rounded-md text-slate-500">Gaya</button>
<button type="button" data-tab="lanjut" class="tab-btn px-2 py-1.5 rounded-md text-slate-500">Lanjut</button>
</div>
<form method="post" enctype="multipart/form-data" data-loading class="grid gap-2 text-sm" id="inspector"><?= Security::csrfField() ?>
<input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>"><input type="hidden" name="old_image" value="<?= Helper::e($edit['image'] ?? '') ?>"><input type="hidden" name="sec_page" value="<?= Helper::e($edit['page'] ?? ($secTab ?? 'home')) ?>">
<?php
$curType = $edit['type'] ?? '';
// Field per fungsi section (tak semua section butuh semua field)
$showTitle = !in_array($curType, [], true);
$showSubtitle = in_array($curType, ['hero', 'carousel', 'carousel-berita', 'kategori-berita', 'countdown', 'image', 'video', 'audio', 'berita', 'galeri', 'guru', 'prestasi', 'ekskul', 'cta', 'custom', 'html', 'agenda', 'pengumuman', 'sambutan', 'statistik', ''], true);
$showContent = in_array($curType, ['custom', 'countdown', 'audio', 'html', 'cta', ''], true);
$showImage = in_array($curType, ['hero', 'image', 'custom', 'html', ''], true); // hero/image 1 gambar; carousel via tab Slide; video via field khusus
$showBtns = in_array($curType, ['hero', 'image', 'cta', 'custom', 'html', ''], true); // default 1 tombol + tambah
$showVideo = ($curType === 'video');
$showLimit = in_array($curType, ['carousel', 'berita', 'carousel-berita', 'kategori-berita', 'galeri', 'guru', 'prestasi', 'ekskul', 'agenda', 'pengumuman', ''], true);
$showGrid = in_array($curType, ['berita','kategori-berita','ekskul','prestasi','guru','galeri'], true) || $curType === '';
$showSlide = ($curType === 'carousel');
$limitLabel = ['carousel' => 'Jumlah slide', 'berita' => 'Jumlah berita', 'carousel-berita' => 'Jumlah berita (default)', 'kategori-berita' => 'Berita per kategori', 'galeri' => 'Jumlah foto', 'guru' => 'Jumlah guru', 'prestasi' => 'Jumlah prestasi', 'ekskul' => 'Jumlah ekskul', 'agenda' => 'Jumlah agenda', 'pengumuman' => 'Jumlah pengumuman'];
?>
<div data-pane="konten" class="grid gap-2">
<input type="hidden" name="type" value="<?= Helper::e($edit['type'] ?? ($_GET['new_type'] ?? 'custom')) ?>">
<label class="grid gap-1" data-f="title">Judul<input name="title" value="<?= Helper::e($edit['title'] ?? '') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1" data-f="subtitle"<?= $showSubtitle ? '' : ' style="display:none"' ?>>Subjudul<input name="subtitle" value="<?= Helper::e($edit['subtitle'] ?? '') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1" data-f="content"<?= $showContent ? '' : ' style="display:none"' ?>>Konten / HTML<textarea name="content" rows="3" class="border rounded-lg p-2 font-mono text-xs"><?= Helper::e($edit['content'] ?? '') ?></textarea></label>
<div class="border rounded-xl p-2.5 bg-slate-50" data-f="image"<?= $showImage ? '' : ' style="display:none"' ?>><p class="text-xs font-bold mb-1.5"><i class="fa fa-image mr-1 text-emerald-600"></i>Gambar <span class="font-normal text-slate-400">(1 gambar)</span></p>
<?php $secPrev=Helper::cover($edit['image'] ?? '', ($edit['type'] ?? 'section').'-'.($edit['section_key'] ?? ($edit['id'] ?? '')), 1200, 630); $secHas=!empty($edit['image']); ?><img id="secImgPrev" src="<?= Helper::e($secPrev) ?>" alt="" class="h-36 w-full object-cover rounded-lg border mb-1.5"><?php if($secHas): ?><label class="text-xs flex gap-1.5 items-center"><input type="checkbox" name="clear_image" value="1"> Hapus gambar (kembali ke bawaan)</label><?php else: ?><p class="text-[11px] text-slate-400 mb-1.5"><i class="fa fa-circle-info mr-1"></i>Gambar bawaan otomatis — upload untuk ganti.</p><?php endif; ?>
<input type="file" name="image" id="secImgInp" accept="image/*" class="border rounded-lg p-2 w-full bg-white text-xs"></div>
<?php $cdOldBg=''; if(($edit['type']??'')==='countdown'){ $bjBg=json_decode((string)($edit['buttons_json']??''),true); if(is_array($bjBg)&&!empty($bjBg['cd_bg'])) $cdOldBg=$bjBg['cd_bg']; } ?>
<div class="border rounded-xl p-2.5 bg-violet-50" data-f="cd_bg"<?= ($curType==='countdown')?'':' style="display:none"' ?>><p class="text-xs font-bold mb-1.5"><i class="fa fa-image mr-1 text-violet-600"></i>Background Timer</p>
<?php if($cdOldBg!==''): ?><img id="cdBgPrev" src="<?= Helper::upload($cdOldBg) ?>" alt="" class="h-24 w-full object-cover rounded-lg border mb-1.5"><label class="text-xs flex gap-1.5 items-center"><input type="checkbox" name="cd_bg_clear" value="1"> Hapus background</label><?php else: ?><img id="cdBgPrev" class="hidden h-24 w-full object-cover rounded-lg border mb-1.5" alt="Preview"><?php endif; ?>
<input type="hidden" name="old_cd_bg" value="<?= Helper::e($cdOldBg) ?>">
<input type="file" name="cd_bg" id="cdBgInp" accept="image/*" class="border rounded-lg p-2 w-full bg-white text-xs"></div>
<?php $ctaOldBg=''; $ctaStyle='gradient'; $ctaBtn='white'; $ctaHover='auto'; $ctaAlign='center'; if(($edit['type']??'')==='cta'||$curType==='cta'){ try{ $bjT=json_decode((string)($edit['buttons_json']??''),true); if(is_array($bjT)){ if(!empty($bjT['cta_bg']))$ctaOldBg=$bjT['cta_bg']; if(!empty($bjT['cta_style']))$ctaStyle=$bjT['cta_style']; if(!empty($bjT['cta_btn']))$ctaBtn=$bjT['cta_btn']; if(!empty($bjT['cta_btn_hover']))$ctaHover=$bjT['cta_btn_hover']; if(!empty($bjT['cta_align']))$ctaAlign=$bjT['cta_align']; } }catch(Throwable){} if(!in_array($ctaStyle,['gradient','dark','light','image','glass'],true))$ctaStyle='gradient'; if(!in_array($ctaBtn,['white','emerald','dark','outline','glass'],true))$ctaBtn='white'; if(!in_array($ctaHover,['auto','darken','lift','glow','zoom'],true))$ctaHover='auto'; if(!in_array($ctaAlign,['left','center'],true))$ctaAlign='center'; } ?>
<div class="border rounded-xl p-2.5 bg-emerald-50 grid gap-1.5" data-f="cta_bg"<?= $curType==='cta'?'':' style="display:none"' ?>>
<p class="text-xs font-bold"><i class="fa fa-bullhorn mr-1 text-emerald-600"></i>Background CTA</p>
<?php if($ctaOldBg!==''): ?><img id="ctaBgPrev" src="<?= Helper::upload($ctaOldBg) ?>" alt="" class="h-24 w-full object-cover rounded-lg border mb-1"><label class="text-xs flex gap-1.5 items-center"><input type="checkbox" name="cta_bg_clear" value="1"> Hapus background</label><?php else: ?><img id="ctaBgPrev" class="hidden h-24 w-full object-cover rounded-lg border mb-1" alt="Preview"><?php endif; ?>
<input type="hidden" name="old_cta_bg" value="<?= Helper::e($ctaOldBg) ?>">
<input type="file" name="cta_bg" id="ctaBgInp" accept="image/*" class="border rounded-lg p-2 w-full bg-white text-xs">
<label class="grid gap-0.5 text-xs">Style background<select name="cta_style" class="border rounded-lg p-1.5 bg-white"><option value="gradient" <?= $ctaStyle==='gradient'?'selected':'' ?>>Gradasi Emerald</option><option value="dark" <?= $ctaStyle==='dark'?'selected':'' ?>>Gelap</option><option value="light" <?= $ctaStyle==='light'?'selected':'' ?>>Terang</option><option value="image" <?= $ctaStyle==='image'?'selected':'' ?>>Gambar (wajib upload)</option><option value="glass" <?= $ctaStyle==='glass'?'selected':'' ?>>Kaca</option></select></label>
<label class="grid gap-0.5 text-xs">Style tombol<select name="cta_btn" class="border rounded-lg p-1.5 bg-white"><option value="white" <?= $ctaBtn==='white'?'selected':'' ?>>Putih solid</option><option value="emerald" <?= $ctaBtn==='emerald'?'selected':'' ?>>Emerald solid</option><option value="dark" <?= $ctaBtn==='dark'?'selected':'' ?>>Gelap solid</option><option value="outline" <?= $ctaBtn==='outline'?'selected':'' ?>>Outline putih</option><option value="glass" <?= $ctaBtn==='glass'?'selected':'' ?>>Kaca</option></select></label>
<label class="grid gap-0.5 text-xs">Efek hover tombol<select name="cta_btn_hover" class="border rounded-lg p-1.5 bg-white"><option value="auto" <?= $ctaHover==='auto'?'selected':'' ?>>Otomatis (ikut style)</option><option value="darken" <?= $ctaHover==='darken'?'selected':'' ?>>Gelapkan</option><option value="lift" <?= $ctaHover==='lift'?'selected':'' ?>>Naik + bayangan</option><option value="glow" <?= $ctaHover==='glow'?'selected':'' ?>>Bersinar</option><option value="zoom" <?= $ctaHover==='zoom'?'selected':'' ?>>Membesar</option></select></label>
<label class="grid gap-0.5 text-xs">Posisi konten<select name="cta_align" class="border rounded-lg p-1.5 bg-white"><option value="center" <?= $ctaAlign==='center'?'selected':'' ?>>Tengah</option><option value="left" <?= $ctaAlign==='left'?'selected':'' ?>>Kiri</option></select></label>
</div>
<?php if($showVideo): $vids=$editSlides; ?>
<div class="border rounded-xl p-2.5 bg-slate-50 grid gap-1.5">
<p class="text-xs font-bold"><i class="fa fa-video mr-1 text-emerald-600"></i>Daftar Video (<?= count($vids) ?>) <span class="font-normal text-slate-400">— upload / tautan, edit judul, hapus per baris</span></p>
<div id="vidList" class="grid gap-1.5">
<?php foreach($vids as $vi=>$vv): $vvFile=trim((string)($vv['subheading']??'')); $vvHasFile=$vvFile!==''&&!preg_match('~^https?://~i',$vvFile); $vvLink=trim((string)($vv['image']??'')); if(!preg_match('~^https?://~i',$vvLink))$vvLink=''; ?>
<div class="flex items-center gap-2 border rounded-xl p-1.5 bg-white text-xs" data-vidrow>
<input type="hidden" name="vids_id[]" value="<?= (int)$vv['id'] ?>">
<span class="w-9 h-9 rounded-lg bg-slate-900 text-white grid place-items-center shrink-0"><i class="fa fa-play text-[10px]"></i></span>
<span class="flex-1 min-w-0 grid gap-1">
<input name="vids_title[]" value="<?= Helper::e($vv['heading']??'') ?>" placeholder="Judul video (auto bila kosong)" class="border rounded-lg p-1.5">
<span class="flex gap-1.5"><input type="file" name="vids_file[]" accept="video/mp4,video/webm,video/ogg" class="border rounded-lg p-1.5 bg-white flex-1 min-w-0 text-[11px]" title="Upload MP4/WebM"><input name="vids_url[]" value="<?= Helper::e($vvLink) ?>" placeholder="ATAU tautan YouTube/Dailymotion/Vimeo/MP4" class="border rounded-lg p-1.5 bg-white font-mono flex-1 min-w-0"></span>
<?php if($vvHasFile): ?><span class="text-emerald-600 font-mono text-[11px] truncate"><?= Helper::e(basename($vvFile)) ?> <label class="font-normal"><input type="checkbox" name="vids_clear[]" value="<?= $vi ?>"> hapus file</label></span><?php endif; ?>
<input type="hidden" name="vids_fetched[]" value="">
</span>
<span class="flex flex-col gap-1 shrink-0">
<button type="button" class="vidFetch w-7 h-7 border rounded-lg bg-white grid place-items-center hover:text-emerald-600" title="Ambil judul otomatis"><i class="fa fa-wand-magic-sparkles text-[10px]"></i></button>
<button type="button" class="vidDel w-7 h-7 border rounded-lg bg-white grid place-items-center text-red-600" title="Hapus video ini"><i class="fa fa-trash text-[10px]"></i></button>
</span>
</div>
<?php endforeach; ?>
</div>
<button type="button" id="vidAdd" class="text-[11px] font-bold border rounded-lg px-2 py-1.5 hover:border-emerald-400"><i class="fa fa-plus mr-1"></i>Tambah Video</button>
</div>
<?php endif; ?>
<div class="grid gap-1.5" data-f="btns"<?= $showBtns ? '' : ' style="display:none"' ?>>
<div class="flex items-center gap-2"><p class="text-xs font-bold"><i class="fa fa-hand-pointer mr-1 text-emerald-600"></i>Tombol</p><button type="button" id="btnAddMore" class="ml-auto text-[11px] font-bold border rounded-lg px-2 py-1 hover:border-emerald-400"><i class="fa fa-plus mr-1"></i>Tambah Tombol</button></div>
<div id="btnList" class="grid gap-1.5">
<?php foreach($editButtons as $bi=>$b): ?>
<div class="border rounded-xl p-2 bg-slate-50 grid gap-1.5" data-btnrow>
<p class="text-[11px] font-bold text-slate-500">Tombol <?= $bi+1 ?><?php if($bi>0): ?> <button type="button" class="btnDel text-red-600 font-bold ml-1">hapus</button><?php endif; ?></p>
<input name="btns_text[]" value="<?= Helper::e($b['text']) ?>" placeholder="Teks tombol" class="border rounded-lg p-2 bg-white">
<div class="grid grid-cols-[1fr_auto] gap-1.5">
<select class="btn-page border rounded-lg p-2 bg-white text-xs"><option value="">— Pilih laman —</option><?php foreach($pageLinks as $pl): ?><option value="/<?= Helper::e($pl['slug']) ?>"><?= Helper::e($pl['title']) ?></option><?php endforeach; ?></select>
<label class="flex gap-1 items-center text-[11px] whitespace-nowrap"><input type="checkbox" name="btns_blank[]" value="<?= $bi ?>" <?= $b['target']==='_blank'?'checked':'' ?>> Tab baru</label>
</div>
<input name="btns_url[]" value="<?= Helper::e($b['url']) ?>" placeholder="/profil atau https://..." class="border rounded-lg p-2 bg-white font-mono text-xs">
</div>
<?php endforeach; ?>
</div></div>
<label class="grid gap-1" data-f="limit"<?= $showLimit ? '' : ' style="display:none"' ?>><span id="limitLabel"><?= Helper::e($limitLabel[$curType] ?? 'Limit item') ?></span><input type="number" name="items_limit" min="1" max="12" value="<?= (int)($edit['items_limit'] ?? 3) ?>" class="border rounded-lg p-2"></label>
<?php $showNewsSrc=in_array($curType,['berita','kategori-berita','carousel-berita'],true); $newsCatSel=''; try{ $bjN=json_decode((string)($edit['buttons_json']??''),true); if(is_array($bjN)&&!empty($bjN['news_cat']))$newsCatSel=(string)$bjN['news_cat']; }catch(Throwable){} $newsCats=[]; try{ $newsCats=$db->query("SELECT id,name FROM categories ORDER BY name")->fetchAll(); }catch(Throwable){} ?>
<div class="border rounded-xl p-2.5 bg-sky-50 grid gap-1.5" data-f="news_src"<?= $showNewsSrc?'':' style="display:none"' ?>>
<p class="text-xs font-bold"><i class="fa fa-newspaper mr-1 text-sky-600"></i>Sumber Berita</p>
<label class="grid gap-0.5 text-xs">Ambil dari kategori<select name="news_cat" class="border rounded-lg p-1.5 bg-white"><option value="">Semua kategori</option><?php foreach($newsCats as $nc): ?><option value="<?= (int)$nc['id'] ?>" <?= $newsCatSel===(string)$nc['id']?'selected':'' ?>><?= Helper::e($nc['name']) ?></option><?php endforeach; ?></select></label>
<?php if($curType==='carousel-berita'): $carSet=[]; try{ $bjC=json_decode((string)($edit['buttons_json']??''),true); if(is_array($bjC)&&!empty($bjC['car']))$carSet=$bjC['car']; }catch(Throwable){} ?>
<p class="text-xs font-bold mt-1"><i class="fa fa-sliders mr-1 text-sky-600"></i>Kontrol Carousel</p>
<div class="grid grid-cols-2 gap-1.5">
<label class="grid gap-0.5 text-xs">Jumlah tampil<input type="number" name="car_limit" min="1" max="10" value="<?= (int)($carSet['limit']??5) ?>" class="border rounded-lg p-1.5 bg-white"></label>
<label class="grid gap-0.5 text-xs">Delay (ms)<input type="number" name="car_delay" min="1000" max="20000" step="500" value="<?= (int)($carSet['delay']??5000) ?>" class="border rounded-lg p-1.5 bg-white"></label>
<label class="grid gap-0.5 text-xs">Tinggi mobile<input type="number" name="car_mh" min="150" max="420" value="<?= (int)($carSet['mh']??200) ?>" class="border rounded-lg p-1.5 bg-white"></label>
<label class="grid gap-0.5 text-xs">Tinggi desktop<input type="number" name="car_dh" min="180" max="520" value="<?= (int)($carSet['dh']??270) ?>" class="border rounded-lg p-1.5 bg-white"></label>
<label class="flex gap-1.5 items-center text-xs mt-4"><input type="checkbox" name="car_auto" value="1" <?= !empty($carSet['auto']??1)?'checked':'' ?>> Autoplay</label>
<label class="flex gap-1.5 items-center text-xs mt-4"><input type="checkbox" name="car_dots" value="1" <?= !empty($carSet['dots']??1)?'checked':'' ?>> Titik navigasi</label>
</div>
<?php endif; ?>
</div>
</div>
<div data-pane="gaya" class="hidden grid gap-2">
<div data-f="gridpick"<?= $showGrid ? '' : ' style="display:none"' ?>>
<p class="text-xs font-bold uppercase text-slate-400">Gaya Grid <span class="font-normal normal-case text-slate-400">(Berita, Kategori Berita, Ekskul, Prestasi, Guru, Galeri)</span></p>
<div class="grid grid-cols-2 gap-1.5" data-gridpick>
<?php $gridPrev=['cards-2'=>'▦▦','cards-3'=>'▦▦▦','cards-4'=>'▦▦▦▦','featured'=>'▦▤','magazine'=>'▦▦','masonry'=>'▦','horizontal'=>'▤','timeline'=>'⋮','list'=>'☰','overlay'=>'▣','minimal'=>'―']; $gridOpts=$grids; foreach($gridOpts as $k=>$l): ?><button type="button" data-grid="<?= $k ?>" class="gridpick border rounded-lg p-1.5 text-center <?= ($edit['grid'] ?? 'cards-3') === $k ? 'ring-2 ring-emerald-500 border-emerald-500' : '' ?>"><span class="block text-xl leading-none"><?= $gridPrev[$k] ?? '▦' ?></span><span class="text-[10px] leading-tight block mt-1"><?= $l ?></span></button><?php endforeach; ?>
</div><input type="hidden" name="grid" value="<?= Helper::e($edit['grid'] ?? 'cards-3') ?>">
</div>
 <?php $showGal=in_array($curType,['galeri'],true); $galAnim=in_array($edit['img_fx']??'none',['marquee','marquee-alt'],true)?$edit['img_fx']:'none'; $galBubble=($edit['img_hover']??'none')==='bubble'; $galSpRaw=$edit['img_size']??'normal'; $galSpeed=strtok($galSpRaw,'|')?:'normal'; if(!in_array($galSpeed,['slow','normal','fast'],true))$galSpeed='normal'; $galPh=explode('|',$galSpRaw)[1]??'md'; if(!in_array($galPh,['sm','md','lg'],true))$galPh='md'; ?>
<div data-f="galanim"<?= $showGal?'':' style="display:none"' ?>>
<p class="text-xs font-bold uppercase text-slate-400">Animasi Galeri <span class="font-normal normal-case text-slate-400">(Berjalan & Gelembung)</span></p>
<div class="grid grid-cols-3 gap-1.5">
<?php foreach(['none'=>'Statis','marquee'=>'Melingkar','marquee-alt'=>'Bolak-balik'] as $k=>$l): ?><button type="button" data-galanim="<?= $k ?>" class="galanimpick border rounded-lg p-1.5 text-center <?= $galAnim===$k?'ring-2 ring-emerald-500 border-emerald-500':'' ?>"><span class="text-[10px] leading-tight block"><?= $l ?></span></button><?php endforeach; ?>
</div><input type="hidden" name="gal_anim" value="<?= Helper::e($galAnim) ?>">
<div class="grid grid-cols-2 gap-1.5 mt-1.5">
<label class="flex gap-1.5 items-center text-xs mt-5"><input type="checkbox" name="gal_bubble" value="1" <?= $galBubble?'checked':'' ?>> Glow foto tengah</label>
<label class="grid gap-0.5 text-xs">Kecepatan<select name="gal_speed" class="border rounded-lg p-1.5 bg-white"><option value="slow" <?= $galSpeed==='slow'?'selected':'' ?>>Lambat</option><option value="normal" <?= $galSpeed==='normal'?'selected':'' ?>>Normal</option><option value="fast" <?= $galSpeed==='fast'?'selected':'' ?>>Cepat</option></select></label>
</div>
<div class="grid grid-cols-3 gap-1.5 mt-1.5">
<?php foreach(['sm'=>'Kecil (h-32)','md'=>'Sedang (h-40)','lg'=>'Besar (h-56)'] as $k=>$l): ?><button type="button" data-galphoto="<?= $k ?>" class="galphotopick border rounded-lg p-1.5 text-center <?= $galPh===$k?'ring-2 ring-emerald-500 border-emerald-500':'' ?>"><span class="text-[10px] leading-tight block"><?= $l ?></span></button><?php endforeach; ?>
</div><input type="hidden" name="gal_photo" value="<?= Helper::e($galPh) ?>">
</div>
<?php $showWelcome=($curType==='sambutan'); $wPos=in_array($edit['grid']??'left',['left','right','top'],true)?$edit['grid']:'left'; $rawWPhoto=trim((string)($edit['img_size']??'rounded')); if($rawWPhoto===''||$rawWPhoto==='full')$rawWPhoto='rounded'; $wPhoto=in_array($rawWPhoto,['kotak','rounded','bulat','lingkaran','kartu','bingkai'],true)?$rawWPhoto:'rounded'; $wText='default'; try{ $bjW=json_decode((string)($edit['buttons_json']??''),true); if(is_array($bjW)&&!empty($bjW['wtext']))$wText=$bjW['wtext']; }catch(Throwable){} if(!in_array($wText,['default','card','quote','center'],true))$wText='default'; ?>
<div data-f="welcome"<?= $showWelcome?'':' style="display:none"' ?> class="grid gap-2 border rounded-xl p-2.5 bg-emerald-50/60">
<p class="text-xs font-bold"><i class="fa fa-user-tie mr-1 text-emerald-600"></i>Gaya Sambutan</p>
<p class="text-[11px] font-bold uppercase text-slate-400">Posisi Gambar</p>
<div class="grid grid-cols-3 gap-1.5">
<?php foreach(['left'=>'Kiri','right'=>'Kanan','top'=>'Atas'] as $k=>$l): ?><button type="button" data-wpos="<?= $k ?>" class="wpospick border rounded-lg p-1.5 text-center bg-white <?= $wPos===$k?'ring-2 ring-emerald-500 border-emerald-500':'' ?>"><span class="text-[10px] leading-tight block font-bold"><?= $l ?></span></button><?php endforeach; ?>
</div><input type="hidden" name="wpos" value="<?= Helper::e($wPos) ?>">
<p class="text-[11px] font-bold uppercase text-slate-400">Style Foto</p>
<div class="grid grid-cols-3 gap-1.5">
<?php foreach(['kotak'=>'Kotak','rounded'=>'Rounded','bulat'=>'Bulat','lingkaran'=>'Lingkaran','kartu'=>'Kartu','bingkai'=>'Bingkai'] as $k=>$l): ?><button type="button" data-wphoto="<?= $k ?>" class="wphotopick border rounded-lg p-1.5 text-center bg-white <?= $wPhoto===$k?'ring-2 ring-emerald-500 border-emerald-500':'' ?>"><span class="text-[10px] leading-tight block font-bold"><?= $l ?></span></button><?php endforeach; ?>
</div><input type="hidden" name="wphoto" value="<?= Helper::e($wPhoto) ?>">
<p class="text-[11px] font-bold uppercase text-slate-400">Style Teks</p>
<div class="grid grid-cols-2 gap-1.5">
<?php foreach(['default'=>'Default','card'=>'Kartu','quote'=>'Quote','center'=>'Tengah'] as $k=>$l): ?><button type="button" data-wtext="<?= $k ?>" class="wtextpick border rounded-lg p-1.5 text-center bg-white <?= $wText===$k?'ring-2 ring-emerald-500 border-emerald-500':'' ?>"><span class="text-[10px] leading-tight block font-bold"><?= $l ?></span></button><?php endforeach; ?>
</div><input type="hidden" name="wtext" value="<?= Helper::e($wText) ?>">
</div>
<?php $showImgFx=in_array($curType,['hero','image','sambutan'],true); $showImgSize=($curType==='image'); ?>
<div data-f="imgfx"<?= $showImgFx?'':' style="display:none"' ?>>
<p class="text-xs font-bold uppercase text-slate-400">Style Gambar <span class="font-normal normal-case text-slate-400">(Hero, Image & Sambutan)</span></p>
<div class="grid grid-cols-3 gap-1.5" data-imgfxpick>
<?php $imgFxOpts=['none'=>'Statis','kenburns'=>'Ken Burns','kenburns-rev'=>'Ken Burns Balik','zoom-slow'=>'Zoom Lambat','pan-left'=>'Geser Kiri','pan-right'=>'Geser Kanan','fade-zoom'=>'Fade Zoom','float'=>'Melayang']; $imgFxIcon=['none'=>'—','kenburns'=>'◎','kenburns-rev'=>'◉','zoom-slow'=>'⊕','pan-left'=>'←','pan-right'=>'→','fade-zoom'=>'◍','float'=>'〜']; foreach($imgFxOpts as $k=>$l): ?><button type="button" data-imgfx="<?= $k ?>" class="imgfxpick border rounded-lg p-1.5 text-center <?= ($edit['img_fx'] ?? 'none') === $k ? 'ring-2 ring-emerald-500 border-emerald-500' : '' ?>"><span class="block text-lg leading-none"><?= $imgFxIcon[$k] ?></span><span class="text-[10px] leading-tight block mt-1"><?= $l ?></span></button><?php endforeach; ?>
</div><input type="hidden" name="img_fx" value="<?= Helper::e($edit['img_fx'] ?? 'none') ?>">
</div>
<div data-f="imgsize"<?= $showImgSize?'':' style="display:none"' ?>>
<p class="text-xs font-bold uppercase text-slate-400">Ukuran Gambar <span class="font-normal normal-case text-slate-400">(Image)</span></p>
<div class="grid grid-cols-3 gap-1.5" data-imgsizepick>
<?php $imgSizeOpts=['thumb'=>'Thumbnail 320×200','sm'=>'Kecil 640×360','md'=>'Sedang 960×540','lg'=>'Besar 1280×720','full'=>'Penuh 1600×900','lead'=>'Leaderboard 728×90','wide'=>'Wide 1200×300','banner'=>'Banner 1400×400','ads'=>'Iklan 720×90','sq'=>'Kotak 800×800','port'=>'Potrait 800×1200','story'=>'Story 1080×1920','orig'=>'Asli','custom'=>'Custom']; foreach($imgSizeOpts as $k=>$l): ?><button type="button" data-imgsize="<?= $k ?>" class="imgsizepick border rounded-lg p-1.5 text-center <?= ($edit['img_size'] ?? 'full') === $k ? 'ring-2 ring-emerald-500 border-emerald-500' : '' ?>"><span class="text-[10px] leading-tight block"><?= $l ?></span></button><?php endforeach; ?>
</div><input type="hidden" name="img_size" value="<?= Helper::e($edit['img_size'] ?? 'full') ?>">
<div class="grid grid-cols-2 gap-1.5 mt-1.5" id="imgCustomBox"<?= ($edit['img_size'] ?? 'full')==='custom'?'':' style="display:none"' ?>>
<label class="grid gap-0.5 text-xs">Lebar px<input type="number" name="img_cw" value="<?= (int)($edit['img_cw'] ?? 0) ?>" min="0" max="2400" placeholder="cth 1200" class="border rounded-lg p-1.5 bg-white"></label>
<label class="grid gap-0.5 text-xs">Tinggi px<input type="number" name="img_ch" value="<?= (int)($edit['img_ch'] ?? 0) ?>" min="0" max="1600" placeholder="cth 600" class="border rounded-lg p-1.5 bg-white"></label>
</div>
</div>
<?php $showSamHov=($curType==='sambutan'); ?>
<div data-f="imghover"<?= ($showImgSize||$showSamHov)?'':' style="display:none"' ?>>
<p class="text-xs font-bold uppercase text-slate-400">Efek Hover <span class="font-normal normal-case text-slate-400">(<?= $showSamHov?'Sambutan':'Image' ?>)</span></p>
<div class="grid grid-cols-3 gap-1.5" data-imghoverpick>
<?php $imgHovOpts=['none'=>'Tanpa','zoom'=>'Zoom','zoom-rotate'=>'Zoom Putar','bright'=>'Cerah','dark'=>'Gelap','gray'=>'Hitam-Putih','blur'=>'Blur','slide-up'=>'Geser Naik']; foreach($imgHovOpts as $k=>$l): ?><button type="button" data-imghover="<?= $k ?>" class="imghoverpick border rounded-lg p-1.5 text-center <?= ($edit['img_hover'] ?? 'none') === $k ? 'ring-2 ring-emerald-500 border-emerald-500' : '' ?>"><span class="text-[10px] leading-tight block"><?= $l ?></span></button><?php endforeach; ?>
</div><input type="hidden" name="img_hover" value="<?= Helper::e($edit['img_hover'] ?? 'none') ?>">
</div>
<p class="text-xs font-bold uppercase text-slate-400">Animasi Masuk (saat scroll)</p>
<div class="grid grid-cols-3 gap-1.5">
<?php foreach($effects as $k=>$l): ?><button type="button" data-fx="<?= $k ?>" class="fxpick border rounded-lg p-1.5 text-center <?= ($edit['effect'] ?? 'fade-up') === $k ? 'ring-2 ring-emerald-500 border-emerald-500' : '' ?>"><span class="block text-lg leading-none"><?= $effectPrev[$k] ?></span><span class="text-[10px] leading-tight block mt-1"><?= $l ?></span></button><?php endforeach; ?>
</div><input type="hidden" name="effect" value="<?= Helper::e($edit['effect'] ?? 'fade-up') ?>">
<p class="text-xs font-bold uppercase text-slate-400">Style</p>
<div class="grid grid-cols-3 gap-1.5" data-pick="style">
<?php foreach($styles as $k=>$l): ?><button type="button" data-val="<?= $k ?>" class="pick border rounded-lg p-1.5 text-center <?= ($edit['style'] ?? 'default') === $k ? 'ring-2 ring-emerald-500 border-emerald-500' : '' ?>"><span class="block h-8 rounded-md mb-1 border" style="background:<?= $stylePrev[$k] ?>"></span><span class="text-[10px] leading-tight block"><?= $l ?></span></button><?php endforeach; ?>
</div><input type="hidden" name="style" value="<?= Helper::e($edit['style'] ?? 'default') ?>">
<p class="text-xs font-bold uppercase text-slate-400">Background</p>
<div class="grid grid-cols-3 gap-1.5" data-pick="bg">
<?php foreach($bgs as $k=>$l): ?><button type="button" data-val="<?= $k ?>" class="pick border rounded-lg p-1.5 text-center <?= ($edit['bg'] ?? 'white') === $k ? 'ring-2 ring-emerald-500 border-emerald-500' : '' ?>"><span class="block h-8 rounded-md border mb-1" style="background:<?= $bgPrev[$k] ?>"></span><span class="text-[10px] leading-tight block"><?= $l ?></span></button><?php endforeach; ?>
</div><input type="hidden" name="bg" value="<?= Helper::e($edit['bg'] ?? 'white') ?>">
<div class="grid grid-cols-2 gap-2">
<label class="grid gap-1 text-xs">Padding<select name="padding" class="border rounded-lg p-2"><?php foreach(['sm'=>'Kecil','md'=>'Sedang','lg'=>'Besar','xl'=>'Ekstra'] as $k=>$l): ?><option value="<?= $k ?>" <?= ($edit['padding'] ?? 'lg') === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select></label>
<label class="grid gap-1 text-xs">Align<select name="align" class="border rounded-lg p-2"><?php foreach(['left'=>'Kiri','center'=>'Tengah','right'=>'Kanan'] as $k=>$l): ?><option value="<?= $k ?>" <?= ($edit['align'] ?? 'left') === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select></label>
</div></div>
<?php $cdTarget='';$cdEndMsg='Acara telah dimulai!';$cdHideZero=0;$cdStyle='glass';$cdInfo='';if(($edit['type']??'')==='countdown'){ $rawC=trim((string)($edit['content']??'')); $bj=json_decode((string)($edit['buttons_json']??''),true); if(is_array($bj)&&!empty($bj['cd_msg'])) $cdEndMsg=$bj['cd_msg']; if(is_array($bj)&&isset($bj['cd_hide'])) $cdHideZero=$bj['cd_hide']?'1':'0'; if(is_array($bj)&&!empty($bj['cd_info'])) $cdInfo=$bj['cd_info']; if(!empty($edit['image'])&&in_array($edit['image'],['glass','dark','light','pill'],true)) $cdStyle=$edit['image']; if($rawC!==''&&preg_match('/^\d{4}-\d{2}-\d{2}(?:[ T]\d{2}:\d{2}(?::\d{2})?)?$/',$rawC)) $cdTarget=str_replace(' ','T',substr($rawC,0,16)); } ?>
<div data-pane="lanjut" class="hidden grid gap-2">
<div data-f="cd"<?= ($curType==='countdown')?'':' style="display:none"' ?> class="grid gap-2 border rounded-xl p-2.5 bg-violet-50">
<p class="text-xs font-bold"><i class="fa fa-clock mr-1 text-violet-600"></i>Countdown</p>
<label class="grid gap-1 text-xs">Waktu target<input type="datetime-local" name="cd_target" value="<?= Helper::e($cdTarget) ?>" class="border rounded-lg p-2 bg-white"></label>
<label class="grid gap-1 text-xs">Teks info (badge)<input name="cd_info" value="<?= Helper::e($cdInfo) ?>" placeholder="cth: PPDB 2026" class="border rounded-lg p-2 bg-white"></label>
<label class="grid gap-1 text-xs">Pesan saat selesai<input name="cd_msg" value="<?= Helper::e($cdEndMsg) ?>" placeholder="Acara telah dimulai!" class="border rounded-lg p-2 bg-white"></label>
<label class="flex gap-2 items-center text-xs"><input type="checkbox" name="cd_hide_zero" value="1" <?= $cdHideZero==='1'||$cdHideZero===1?'checked':'' ?>> Sembunyikan angka nol</label>
<label class="grid gap-1 text-xs">Gaya countdown<select name="cd_style" class="border rounded-lg p-2 bg-white"><option value="glass" <?= $cdStyle==='glass'?'selected':'' ?>>Kaca — blur + progress</option><option value="dark" <?= $cdStyle==='dark'?'selected':'' ?>>Gelap</option><option value="light" <?= $cdStyle==='light'?'selected':'' ?>>Terang</option><option value="pill" <?= $cdStyle==='pill'?'selected':'' ?>>Pill — kapsul besar</option></select></label>
</div>
<div class="grid grid-cols-2 gap-2">
<label class="grid gap-1 text-xs">Key<input name="section_key" value="<?= Helper::e($edit['section_key'] ?? '') ?>" placeholder="hero-2" class="border rounded-lg p-2 font-mono"></label>
<label class="grid gap-1 text-xs">Urutan<input type="number" name="sort_order" value="<?= (int)($edit['sort_order'] ?? (count($rows)+1)) ?>" class="border rounded-lg p-2"></label>
</div>
<label class="flex gap-2 items-center text-sm"><input type="checkbox" name="is_active" value="1" <?= !isset($edit) || $edit['is_active'] ? 'checked' : '' ?> class="w-4 h-4"> Aktif</label>
<?php if($edit): ?><p class="text-xs text-slate-500 font-mono">ID #<?= (int)$edit['id'] ?> • <?= Helper::e($edit['type']) ?></p><?php endif; ?>
</div>
<div class="flex gap-2 pt-2 sticky bottom-0 bg-white" id="globalSave"><button class="flex-1 bg-emerald-600 text-white rounded-lg p-2 font-bold">Simpan</button>
<?php if($edit): ?><a href="<?= Helper::url('admin/sections') ?>" class="border rounded-lg px-4 py-2">Batal</a><?php endif; ?></div>
<div class="hidden" id="saveBar"></div></form>
<div data-pane="slide" class="hidden mt-2">
<?php if (empty($edit)): ?>
<p class="text-xs text-slate-500 bg-slate-50 border rounded-lg p-3">Simpan section dulu, lalu tambah item di sini. Berlaku untuk widget <b>Carousel</b>.</p>
<?php elseif (!$useSlides): ?>
<p class="text-xs text-slate-500 bg-slate-50 border rounded-lg p-3">Tab ini khusus widget <b>Carousel</b> (daftar gambar). Video diatur langsung di tab Konten.</p>
<?php else: ?>
<p class="text-[11px] text-slate-500 mb-2"><i class="fa fa-clone mr-1"></i>Slide section ini (<?= count($editSlides) ?>). Upload beberapa gambar sekaligus.</p>
<div class="grid gap-1.5 mb-2">
<?php foreach ($editSlides as $sl): ?>
<div class="flex items-center gap-2 border rounded-xl p-1.5 bg-slate-50 text-xs">
<?php if ($sl['image']): ?><img src="<?= Helper::upload($sl['image']) ?>" class="w-12 h-9 rounded-lg object-cover border" loading="lazy"><?php else: ?><span class="w-12 h-9 rounded-lg bg-slate-200 grid place-items-center text-slate-400"><i class="fa fa-image"></i></span><?php endif; ?>
<span class="flex-1 min-w-0"><b class="block truncate"><?= Helper::e($sl['heading'] ?: '(tanpa judul)') ?></b><span class="text-slate-400">#<?= (int)$sl['sort_order'] ?> <?= $sl['is_active'] ? '' : '• off' ?></span></span>
<button type="button" class="sl-edit w-7 h-7 border rounded-lg bg-white grid place-items-center hover:text-emerald-600" title="Edit" data-slide='<?= htmlspecialchars(json_encode($sl), ENT_QUOTES) ?>'><i class="fa fa-pen text-[10px]"></i></button>
<form method="post" class="inline"><input type="hidden" name="csrf" value="<?= Security::csrfToken() ?>"><input type="hidden" name="act" value="slide_toggle"><input type="hidden" name="section_id" value="<?= $edit['id'] ?>"><input type="hidden" name="slide_id" value="<?= $sl['id'] ?>"><button class="w-7 h-7 border rounded-lg bg-white grid place-items-center <?= $sl['is_active'] ? 'text-emerald-600' : 'text-slate-400' ?>" title="On/Off"><i class="fa fa-power-off text-[10px]"></i></button></form>
<form method="post" data-confirm class="inline"><?= Security::csrfField() ?><input type="hidden" name="act" value="slide_del"><input type="hidden" name="section_id" value="<?= $edit['id'] ?>"><input type="hidden" name="slide_id" value="<?= $sl['id'] ?>"><button class="w-7 h-7 border rounded-lg bg-white grid place-items-center text-red-600" title="Hapus"><i class="fa fa-trash text-[10px]"></i></button></form>
</div>
<?php endforeach; ?>
<?php if (!$editSlides): ?><p class="text-xs text-slate-400 text-center py-2">Belum ada slide. Tambah di bawah.</p><?php endif; ?>
</div>
<form method="post" enctype="multipart/form-data" data-loading class="grid gap-1.5 border rounded-xl p-2.5 bg-slate-50"><?= Security::csrfField() ?>
<input type="hidden" name="act" value="slide_save"><input type="hidden" name="section_id" value="<?= $edit['id'] ?>"><input type="hidden" name="slide_id" id="sl_id" value="0"><input type="hidden" name="old_slide_img" id="sl_old" value="">
<p class="text-xs font-bold" id="slTitle">Tambah Slide</p>
<label class="grid gap-0.5 text-xs">Judul<input name="slide_heading" id="sl_h" class="border rounded-lg p-1.5 bg-white"></label>
<label class="grid gap-0.5 text-xs">Subjudul<input name="slide_sub" id="sl_s" class="border rounded-lg p-1.5 bg-white"></label>
<label class="grid gap-0.5 text-xs">Gambar <span class="text-slate-400">(bisa multi-upload sekaligus)</span><input type="file" name="slide_img[]" id="sl_img" accept="image/*" multiple class="border rounded-lg p-1.5 bg-white"></label>
<img id="sl_prev" class="hidden h-20 w-full object-cover rounded-lg border">
<div id="sl_prev_multi" class="hidden grid-cols-4 gap-1.5"></div>
<div class="grid grid-cols-2 gap-1.5"><input name="slide_cta" id="sl_c1" placeholder="Tombol 1" class="border rounded-lg p-1.5 bg-white text-xs"><input name="slide_cta_url" id="sl_u1" placeholder="/profil" class="border rounded-lg p-1.5 bg-white text-xs"></div>
<div class="grid grid-cols-2 gap-1.5"><input name="slide_cta2" id="sl_c2" placeholder="Tombol 2" class="border rounded-lg p-1.5 bg-white text-xs"><input name="slide_cta2_url" id="sl_u2" placeholder="/berita" class="border rounded-lg p-1.5 bg-white text-xs"></div>
<div class="grid grid-cols-2 gap-1.5">
<label class="grid gap-0.5 text-xs">Urutan<input type="number" name="slide_order" id="sl_o" value="0" class="border rounded-lg p-1.5 bg-white"></label>
<label class="flex gap-1.5 items-center text-xs mt-5"><input type="checkbox" name="slide_active" id="sl_a" value="1" checked> Aktif</label>
</div>
<div class="flex gap-1.5"><button class="flex-1 bg-emerald-600 text-white rounded-lg py-1.5 text-xs font-bold" id="slSave">Tambah Slide</button><button type="button" id="slReset" class="border rounded-lg px-3 text-xs">Reset</button></div>
</form>
<?php endif; ?>
</div></div></div>
<script>
const TYPEHINT={hero:'Hero: 1 gambar + judul + subjudul + tombol. Tanpa slide, tanpa limit item.',carousel:'Carousel: multi-gambar lewat tab Slide. Tanpa tombol section, input tombol disembunyikan.',countdown:'Countdown: judul + subjudul + target waktu di Konten (cth: 2026-12-31 23:59).',image:'Image: 1 gambar + judul + subjudul overlay + tombol.',video:'Video: tempel link YouTube biasa (watch?v= / youtu.be / shorts) ATAU embed. Auto jadi embed. Tinggi proporsional max 420px.',audio:'Audio: URL file MP3 di Konten + judul. Auto render audio player.',html:'Custom HTML: tulis HTML bebas di Konten + tombol (tambah bila perlu).',sambutan:'Sambutan: otomatis dari Profil. Atur posisi gambar + style foto + style teks di tab Gaya.',statistik:'Statistik: otomatis dari Sekolah > Statistik. Cukup judul section.',berita:'Berita: judul + limit + gaya grid. Tanpa tombol section.',agenda:'Agenda: judul + limit agenda mendatang.',pengumuman:'Pengumuman: judul + limit info terbaru.',galeri:'Galeri: judul + limit foto.',guru:'Guru: judul + limit guru aktif.',prestasi:'Prestasi: judul + limit.',ekskul:'Ekskul: judul + limit.',cta:'CTA: judul + subjudul + tombol (tambah bila perlu). Tanpa limit item.',custom:'Custom: konten HTML + gambar + tombol.'};
const TYPELIMIT={carousel:'Jumlah slide',berita:'Jumlah berita','carousel-berita':'Jumlah berita (default)','kategori-berita':'Berita per kategori',galeri:'Jumlah foto',guru:'Jumlah guru',prestasi:'Jumlah prestasi',ekskul:'Jumlah ekskul',agenda:'Jumlah agenda',pengumuman:'Jumlah pengumuman'};
function applyType(){
  // Tipe terkunci (hidden input) — field sudah dirender server per fungsi. Jangan paksa tampil.
  if(!document.getElementById('f_type'))return;
  const t=document.getElementById('f_type')?.value||'';
  const show=n=>document.querySelectorAll('[data-f="'+n+'"]').forEach(e=>e.style.display='');
  const hide=n=>document.querySelectorAll('[data-f="'+n+'"]').forEach(e=>e.style.display='none');
  // Konten: field mengikuti fungsi tipe. Gaya/Lanjut SELALU tampil penuh.
  // Tipe terkunci via hidden input — tab Slide/Video sudah dirender server sesuai tipe.
  ['subtitle','content','image','btns','limit'].forEach(show);
  if(t==='hero'){hide('content');hide('limit')}
  if(t==='carousel'){hide('content');hide('image');hide('btns')}
  if(t==='countdown'){hide('image');hide('btns');hide('limit')}
  if(t==='image'){hide('content');hide('limit')}
  if(t==='video'){hide('image');hide('btns');hide('limit')}
  if(t==='audio'){hide('image');hide('btns');hide('limit')}
  if(t==='html'){hide('image');hide('limit')}
  if(['sambutan','statistik'].includes(t)){hide('subtitle');hide('content');hide('image');hide('btns');hide('limit')}
  if(['berita','carousel-berita','kategori-berita','galeri','guru','prestasi','ekskul','agenda','pengumuman'].includes(t)){hide('content');hide('image');hide('btns')}
  if(t==='cta'){hide('content');hide('image');hide('limit')}
  if(t==='custom'){hide('limit')}
  const ll=document.getElementById('limitLabel');if(ll)ll.textContent=TYPELIMIT[t]||'Limit item';
  const hint=document.getElementById('typeHint');if(hint)hint.textContent=TYPEHINT[t]||'';
}
document.getElementById('f_type')?.addEventListener('change',applyType);
applyType();
// tambah tombol dinamis (maks 5)
document.getElementById('btnAddMore')?.addEventListener('click',()=>{
  const list=document.getElementById('btnList');
  const n=list.querySelectorAll('[data-btnrow]').length;
  if(n>=5){Swal.fire('Maksimal','Maksimal 5 tombol.','warning');return}
  const div=document.createElement('div');
  div.className='border rounded-xl p-2 bg-slate-50 grid gap-1.5';div.setAttribute('data-btnrow','');
  div.innerHTML='<p class="text-xs font-bold">Tombol '+(n+1)+' <button type="button" class="btnDel text-red-600 font-bold ml-1">hapus</button></p>'
  +'<input name="btns_text[]" placeholder="Teks tombol" class="border rounded-lg p-2 bg-white">'
  +'<div class="grid grid-cols-[1fr_auto] gap-1.5"><select class="btn-page border rounded-lg p-2 bg-white text-xs"><option value="">— Pilih laman —</option><?php foreach($pageLinks as $pl): ?><option value="/<?= Helper::e($pl['slug']) ?>"><?= Helper::e($pl['title']) ?></option><?php endforeach; ?></select>'
  +'<label class="flex gap-1 items-center text-[11px] whitespace-nowrap"><input type="checkbox" data-blank value="1"> Tab baru</label></div>'
  +'<input name="btns_url[]" placeholder="/profil atau https://..." class="border rounded-lg p-2 bg-white font-mono text-xs">';
  list.appendChild(div);wireBtnRow(div);
});
function wireBtnRow(row){
  const sel=row.querySelector('.btn-page'),url=row.querySelector('input[name="btns_url[]"]'),chk=row.querySelector('[data-blank]');
  sel?.addEventListener('change',()=>{if(sel.value)url.value=sel.value});
  chk?.addEventListener('change',()=>{chk.name=chk.checked?'btns_blank[]':'_x';chk.value=row.dataset.idx||[...document.querySelectorAll('#btnList [data-btnrow]')].indexOf(row)});
  row.querySelector('.btnDel')?.addEventListener('click',()=>{row.remove();reindexBtns()});
}
function reindexBtns(){
  document.querySelectorAll('#btnList [data-btnrow]').forEach((row,i)=>{
    row.querySelector('p').firstChild.textContent='Tombol '+(i+1)+' ';
    const chk=row.querySelector('[data-blank],input[name="btns_blank[]"],input[name="_x"]');
    if(chk){chk.value=i;if(chk.checked)chk.name='btns_blank[]'}
  });
}
document.querySelectorAll('#btnList [data-btnrow]').forEach(wireBtnRow);
reindexBtns();
document.querySelectorAll('[data-tabs] .tab-btn').forEach(b=>b.addEventListener('click',()=>{
  document.querySelectorAll('[data-tabs] .tab-btn').forEach(x=>{x.classList.remove('bg-white','shadow');x.classList.add('text-slate-500')});
  b.classList.add('bg-white','shadow');b.classList.remove('text-slate-500');
  document.querySelectorAll('[data-pane]').forEach(p=>p.classList.toggle('hidden',p.dataset.pane!==b.dataset.tab));
  // form slide punya tombol sendiri; sembunyikan Simpan global saat tab Slide
  document.getElementById('globalSave')?.classList.toggle('hidden',b.dataset.tab==='slide');
}));
document.querySelectorAll('[data-pick]').forEach(w=>{
  w.querySelectorAll('.pick').forEach(b=>b.addEventListener('click',()=>{
    w.querySelectorAll('.pick').forEach(x=>x.classList.remove('ring-2','ring-emerald-500','border-emerald-500'));
    b.classList.add('ring-2','ring-emerald-500','border-emerald-500');
    const hid=w.parentElement.querySelector('input[name="'+w.dataset.pick+'"]'); if(hid)hid.value=b.dataset.val;
  }));
});
document.querySelectorAll('.fxpick').forEach(b=>b.addEventListener('click',()=>{
  document.querySelectorAll('.fxpick').forEach(x=>x.classList.remove('ring-2','ring-emerald-500','border-emerald-500'));
  b.classList.add('ring-2','ring-emerald-500','border-emerald-500');
  const hid=document.querySelector('input[name="effect"]'); if(hid)hid.value=b.dataset.fx;
}));
document.querySelectorAll('.btn-page').forEach(s=>s.addEventListener('change',()=>{
  if(!s.value)return;
  const t=document.getElementById(s.dataset.url);if(t)t.value=s.value;
}));
document.querySelectorAll('.gridpick').forEach(b=>b.addEventListener('click',()=>{
  document.querySelectorAll('.gridpick').forEach(x=>x.classList.remove('ring-2','ring-emerald-500','border-emerald-500'));
  b.classList.add('ring-2','ring-emerald-500','border-emerald-500');
  const hid=document.querySelector('input[name="grid"]'); if(hid)hid.value=b.dataset.grid;
}));
document.querySelectorAll('.imgfxpick').forEach(b=>b.addEventListener('click',()=>{
  document.querySelectorAll('.imgfxpick').forEach(x=>x.classList.remove('ring-2','ring-emerald-500','border-emerald-500'));
  b.classList.add('ring-2','ring-emerald-500','border-emerald-500');
  const hid=document.querySelector('input[name="img_fx"]'); if(hid)hid.value=b.dataset.imgfx;
}));
document.querySelectorAll('.imgsizepick').forEach(b=>b.addEventListener('click',()=>{
  document.querySelectorAll('.imgsizepick').forEach(x=>x.classList.remove('ring-2','ring-emerald-500','border-emerald-500'));
  b.classList.add('ring-2','ring-emerald-500','border-emerald-500');
  const hid=document.querySelector('input[name="img_size"]'); if(hid)hid.value=b.dataset.imgsize;
  const box=document.getElementById('imgCustomBox'); if(box)box.style.display=b.dataset.imgsize==='custom'?'':'none';
}));
document.getElementById('secImgInp')?.addEventListener('change',e=>{const f=e.target.files?.[0];const p=document.getElementById('secImgPrev');if(f&&p){p.src=URL.createObjectURL(f);p.classList.remove('hidden')}});
document.getElementById('cdBgInp')?.addEventListener('change',e=>{const f=e.target.files?.[0];const p=document.getElementById('cdBgPrev');if(f&&p){p.src=URL.createObjectURL(f);p.classList.remove('hidden')}});
document.getElementById('ctaBgInp')?.addEventListener('change',e=>{const f=e.target.files?.[0];const p=document.getElementById('ctaBgPrev');if(f&&p){p.src=URL.createObjectURL(f);p.classList.remove('hidden')}});
document.getElementById('sl_img')?.addEventListener('change',e=>{
  const files=[...e.target.files||[]];const single=document.getElementById('sl_prev'),multi=document.getElementById('sl_prev_multi');
  if(single)single.classList.add('hidden');
  if(multi){multi.innerHTML='';multi.classList.add('hidden')}
  if(files.length===1){single.src=URL.createObjectURL(files[0]);single.classList.remove('hidden')}
  else if(files.length>1){multi.classList.remove('hidden');multi.classList.add('grid');files.forEach(f=>{const im=document.createElement('img');im.src=URL.createObjectURL(f);im.className='h-16 w-full object-cover rounded-lg border';multi.appendChild(im)})}
});
document.querySelectorAll('.galanimpick').forEach(b=>b.addEventListener('click',()=>{
  document.querySelectorAll('.galanimpick').forEach(x=>x.classList.remove('ring-2','ring-emerald-500','border-emerald-500'));
  b.classList.add('ring-2','ring-emerald-500','border-emerald-500');
  const hid=document.querySelector('input[name="gal_anim"]'); if(hid)hid.value=b.dataset.galanim;
}));
document.querySelectorAll('.galphotopick').forEach(b=>b.addEventListener('click',()=>{
  document.querySelectorAll('.galphotopick').forEach(x=>x.classList.remove('ring-2','ring-emerald-500','border-emerald-500'));
  b.classList.add('ring-2','ring-emerald-500','border-emerald-500');
  const hid=document.querySelector('input[name="gal_photo"]'); if(hid)hid.value=b.dataset.galphoto;
}));
document.querySelectorAll('.imghoverpick').forEach(b=>b.addEventListener('click',()=>{
  document.querySelectorAll('.imghoverpick').forEach(x=>x.classList.remove('ring-2','ring-emerald-500','border-emerald-500'));
  b.classList.add('ring-2','ring-emerald-500','border-emerald-500');
  const hid=document.querySelector('input[name="img_hover"]'); if(hid)hid.value=b.dataset.imghover;
}));
document.querySelectorAll('.wpospick').forEach(b=>b.addEventListener('click',()=>{
  document.querySelectorAll('.wpospick').forEach(x=>x.classList.remove('ring-2','ring-emerald-500','border-emerald-500'));
  b.classList.add('ring-2','ring-emerald-500','border-emerald-500');
  const hid=document.querySelector('input[name="wpos"]'); if(hid)hid.value=b.dataset.wpos;
}));
document.querySelectorAll('.wphotopick').forEach(b=>b.addEventListener('click',()=>{
  document.querySelectorAll('.wphotopick').forEach(x=>x.classList.remove('ring-2','ring-emerald-500','border-emerald-500'));
  b.classList.add('ring-2','ring-emerald-500','border-emerald-500');
  const hid=document.querySelector('input[name="wphoto"]'); if(hid)hid.value=b.dataset.wphoto;
}));
document.querySelectorAll('.wtextpick').forEach(b=>b.addEventListener('click',()=>{
  document.querySelectorAll('.wtextpick').forEach(x=>x.classList.remove('ring-2','ring-emerald-500','border-emerald-500'));
  b.classList.add('ring-2','ring-emerald-500','border-emerald-500');
  const hid=document.querySelector('input[name="wtext"]'); if(hid)hid.value=b.dataset.wtext;
}));
document.getElementById('vidAdd')?.addEventListener('click',()=>{
  const list=document.getElementById('vidList');
  const div=document.createElement('div');
  div.className='flex items-center gap-2 border rounded-xl p-1.5 bg-white text-xs';div.setAttribute('data-vidrow','');
  div.innerHTML='<input type="hidden" name="vids_id[]" value="0">'
  +'<span class="w-9 h-9 rounded-lg bg-slate-900 text-white grid place-items-center shrink-0"><i class="fa fa-play text-[10px]"></i></span>'
  +'<span class="flex-1 min-w-0 grid gap-1"><span class="flex gap-1.5"><input name="vids_title[]" placeholder="Judul video (auto bila kosong)" class="border rounded-lg p-1.5 flex-1 min-w-0"><input type="hidden" name="vids_fetched[]"><button type="button" class="vidFetch border rounded-lg px-2 text-xs font-bold hover:border-emerald-400 whitespace-nowrap" title="Ambil judul otomatis"><i class="fa fa-wand-magic-sparkles"></i></button></span>'
  +'<span class="flex gap-1.5"><input type="file" name="vids_file[]" accept="video/mp4,video/webm,video/ogg" class="border rounded-lg p-1.5 bg-white flex-1 min-w-0 text-[11px]" title="Upload MP4/WebM"><input name="vids_url[]" placeholder="ATAU tautan YouTube/Dailymotion/Vimeo/MP4" class="border rounded-lg p-1.5 bg-white font-mono flex-1 min-w-0"></span></span>'
  +'<button type="button" class="vidDel w-7 h-7 border rounded-lg bg-white grid place-items-center text-red-600 shrink-0" title="Hapus video ini"><i class="fa fa-trash text-[10px]"></i></button>';
  list.appendChild(div);wireVidRow(div);
});
function wireVidRow(row){
  row.querySelector('.vidDel')?.addEventListener('click',()=>{
    const idIn=row.querySelector('input[name="vids_id[]"]');
    if(idIn&&parseInt(idIn.value||'0',10)>0){
      Swal.fire({title:'Hapus video ini?',text:'Baris hilang + file ikut terhapus saat Simpan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya, Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)row.remove()});
    }else row.remove();
  });
  row.querySelector('.vidFetch')?.addEventListener('click',async()=>{
    const urlIn=row.querySelector('input[name="vids_url[]"]'),tIn=row.querySelector('input[name="vids_title[]"]'),fIn=row.querySelector('input[name="vids_fetched[]"]'),btn=row.querySelector('.vidFetch');
    const url=(urlIn?.value||'').trim();
    if(!url){Swal.fire('Isi dulu','Tempel tautan video dulu.','warning');return}
    btn.disabled=true;const old=btn.innerHTML;btn.innerHTML='<i class="fa fa-spinner fa-spin"></i>';
    try{
      const r=await fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'csrf=<?= Security::csrfToken() ?>&ajax=1&act=video_title&url='+encodeURIComponent(url)});
      const j=await r.json();
      if(j.ok&&j.title){tIn.value=j.title;if(fIn)fIn.value=j.title;Swal.fire({icon:'success',title:'Judul diambil',text:j.title,timer:1800,showConfirmButton:false})}
      else Swal.fire('Gagal',j.msg||'Judul tidak ditemukan','error');
    }catch(_){Swal.fire('Gagal','Tidak bisa ambil judul','error')}
    btn.disabled=false;btn.innerHTML=old;
  });
}
document.querySelectorAll('#vidList [data-vidrow]').forEach(wireVidRow);
function resetSlide(){
  document.getElementById('sl_id').value=0;
  const oldEl=document.getElementById('sl_old');if(oldEl)oldEl.value='';
  ['sl_h','sl_s','sl_c1','sl_u1','sl_c2','sl_u2'].forEach(id=>{const el=document.getElementById(id);if(el)el.value=''});
  document.getElementById('sl_o').value=0;document.getElementById('sl_a').checked=true;
  const fi=document.getElementById('sl_img');if(fi)fi.value='';
  const pv=document.getElementById('sl_prev');if(pv)pv.classList.add('hidden');
  document.getElementById('slTitle').textContent='Tambah Slide';
  document.getElementById('slSave').textContent='Tambah Slide';
}
document.querySelectorAll('.sl-edit').forEach(b=>b.addEventListener('click',()=>{
  const d=JSON.parse(b.dataset.slide);
  document.getElementById('sl_id').value=d.id;
  const oldEl=document.getElementById('sl_old');if(oldEl)oldEl.value=d.image||'';
  document.getElementById('sl_h').value=d.heading||'';
  const sEl=document.getElementById('sl_s');if(sEl)sEl.value=d.subheading||'';
  document.getElementById('sl_c1').value=d.cta_text||'';
  document.getElementById('sl_u1').value=d.cta_url||'';
  document.getElementById('sl_c2').value=d.cta2_text||'';
  document.getElementById('sl_u2').value=d.cta2_url||'';
  document.getElementById('sl_o').value=d.sort_order||0;
  document.getElementById('sl_a').checked=(d.is_active==1);
  const pv=document.getElementById('sl_prev');
  if(pv){
    if(d.image){pv.src='<?= Helper::url('assets/uploads/') ?>/'+d.image;pv.classList.remove('hidden')}else pv.classList.add('hidden');
  }
  document.getElementById('slTitle').textContent='Edit Slide #'+d.id;
  document.getElementById('slSave').textContent='Simpan Perubahan';
  document.querySelector('[data-tab="slide"]')?.click();
}));
document.getElementById('slReset')?.addEventListener('click',resetSlide);
document.getElementById('sl_img')?.addEventListener('change',e=>{
  const f=e.target.files[0];if(!f)return;
  const pv=document.getElementById('sl_prev');pv.src=URL.createObjectURL(f);pv.classList.remove('hidden');
});
const cv=document.getElementById('canvas');let dragCard=null,dragType=null;
document.querySelectorAll('.pal-btn').forEach(b=>{
  b.addEventListener('dragstart',e=>{dragType=b.dataset.ptype;dragCard=null;e.dataTransfer.setData('text/sec-type',dragType);e.dataTransfer.effectAllowed='copy'});
  b.addEventListener('dragend',()=>{dragType=null;cv?.classList.remove('border-emerald-400','bg-emerald-50/50')});
  b.addEventListener('click',()=>{/* biarkan submit form: tambah di akhir lalu buka inspector */});
});
cv?.querySelectorAll('.sec-card').forEach(c=>{
  c.addEventListener('dragstart',e=>{dragCard=c;dragType=null;e.dataTransfer.setData('text/sec-id',c.dataset.id);e.dataTransfer.effectAllowed='move';setTimeout(()=>c.classList.add('opacity-40'),0)});
  c.addEventListener('dragend',()=>{c.classList.remove('opacity-40');dragCard=null;cv.classList.remove('border-emerald-400','bg-emerald-50/50');if(!dragType)saveOrder()});
  c.addEventListener('dragover',e=>{e.preventDefault();if((dragCard&&dragCard!==c)||dragType){const r=c.getBoundingClientRect();(e.clientY<r.top+r.height/2?c.before(dragCard||dropHint()):c.after(dragCard||dropHint()))}});
  c.addEventListener('click',e=>{if(e.target.closest('a,button,form'))return;location.href=c.dataset.edit});
});
cv?.addEventListener('dragover',e=>{e.preventDefault();cv.classList.add('border-emerald-400','bg-emerald-50/50')});
cv?.addEventListener('dragleave',e=>{if(e.target===cv)cv.classList.remove('border-emerald-400','bg-emerald-50/50')});
cv?.addEventListener('drop',e=>{
  e.preventDefault();cv.classList.remove('border-emerald-400','bg-emerald-50/50');
  document.getElementById('dropHint')?.remove();
  const t=e.dataTransfer.getData('text/sec-type')||dragType;
  if(t){const cards=[...cv.querySelectorAll('.sec-card')];let pos=cards.length+1;for(let i=0;i<cards.length;i++){const r=cards[i].getBoundingClientRect();if(e.clientY<r.top+r.height/2){pos=i+1;break}}
    fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'csrf=<?= Security::csrfToken() ?>&ajax=1&act=quick_add&type='+encodeURIComponent(t)+'&position='+pos})
    .then(r=>r.json()).then(j=>{if(j.ok)location.href='?edit='+j.id;else Swal.fire('Gagal',j.msg||'Tambah gagal','error')}).catch(()=>Swal.fire('Gagal','Tambah gagal','error'));
  } else saveOrder();
});
function dropHint(){let h=document.getElementById('dropHint');if(!h){h=document.createElement('div');h.id='dropHint';h.className='h-2 rounded-full bg-emerald-400'}return h}
function saveOrder(){
  const order=[...cv.querySelectorAll('.sec-card')].map(c=>c.dataset.id);
  fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'csrf=<?= Security::csrfToken() ?>&ajax=1&act=reorder&order='+encodeURIComponent(order.join(','))})
  .then(r=>r.json()).then(j=>{if(j.ok){Swal.fire({icon:'success',title:'Urutan disimpan',timer:1200,showConfirmButton:false})}else{Swal.fire('Gagal',j.msg||'Reorder gagal','error')}})
  .catch(()=>Swal.fire('Gagal','Reorder gagal','error'));
}
</script>
<?php require ROOT.'/templates/admin/footer.php'; ?>




