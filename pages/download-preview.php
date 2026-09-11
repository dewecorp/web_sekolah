<?php
declare(strict_types=1);
$f = trim($_GET['file'] ?? '');
$path = ROOT . '/assets/uploads/' . $f;
if ($f === '' || !preg_match('~^[a-zA-Z0-9._-]+$~', $f) || !is_file($path)) { http_response_code(404); echo 'Dokumen tidak ditemukan.'; exit; }
$ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
if ($ext !== 'pdf') { header('Location: ' . Helper::url('assets/uploads/' . $f)); exit; }
$niceName = trim($_GET['name'] ?? '');
if ($niceName === '') $niceName = pathinfo($f, PATHINFO_FILENAME);
$base64 = base64_encode(file_get_contents($path));
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= Helper::e($niceName) ?></title><script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script><style>
*{box-sizing:border-box}
html,body{margin:0;padding:0;font-family:system-ui,sans-serif;background:#525659}
#bar{display:flex;align-items:center;gap:8px;padding:10px 16px;background:#323639;color:#fff;position:sticky;top:0;z-index:10}
#bar b{font-size:14px;flex:1;overflow:hidden;white-space:nowrap;text-overflow:ellipsis}
#bar button,#bar a{background:#059669;color:#fff;border:0;border-radius:6px;padding:8px 12px;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;white-space:nowrap}
#bar button:hover,#bar a:hover{background:#047857}
#bar .ghost{background:#4b5563}
#bar .ghost:hover{background:#374151}
#pages{display:flex;flex-direction:column;align-items:center;padding:20px 10px 40px;gap:16px}
canvas.page{background:#fff;box-shadow:0 2px 10px rgba(0,0,0,.45);max-width:820px;width:100%;height:auto}
#load{color:#fff;font-size:14px;padding:40px}
@page{size:A4;margin:10mm}
@media print{
  html,body{background:#fff}
  #bar,#load{display:none!important}
  #pages{display:block;padding:0;gap:0}
  canvas.page{box-shadow:none;margin:0 auto;max-width:100%;width:100%;page-break-after:always;break-inside:avoid}
  canvas.page:last-child{page-break-after:auto}
}
</style></head>
<body>
<div id="bar"><b><?= Helper::e($niceName) ?></b><button id="btnZoomOut" class="ghost" title="Perkecil">A-</button><button id="btnZoomIn" class="ghost" title="Perbesar">A+</button><button id="btnPrint"><i class="fa fa-print"></i> Cetak</button><a id="btnDownload" href="#"><i class="fa fa-download"></i> Unduh</a></div>
<div id="pages"><div id="load">Memuat dokumen...</div></div>
<script>
const docName=<?= json_encode($niceName, JSON_UNESCAPED_UNICODE) ?>;
document.title=docName;
const pdfBase64='<?= $base64 ?>';
pdfjsLib.GlobalWorkerOptions.workerSrc='https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
let pdfDoc=null; const curScale=2;
function b64toBlob(b64,ct){
  const bin=atob(b64);const arr=new Uint8Array(bin.length);
  for(let i=0;i<bin.length;i++)arr[i]=bin.charCodeAt(i);
  return new Blob([arr],{type:ct});
}
const pdfBlob=b64toBlob(pdfBase64,'application/pdf');
const pdfUrl=URL.createObjectURL(pdfBlob);
const dl=document.getElementById('btnDownload');
dl.href=pdfUrl; dl.download=docName+'.pdf';
async function renderAll(){
  const box=document.getElementById('pages');
  box.innerHTML='';
  for(let i=1;i<=pdfDoc.numPages;i++){
    const page=await pdfDoc.getPage(i);
    const vp=page.getViewport({scale:curScale});
    const cv=document.createElement('canvas');cv.className='page';
    cv.width=Math.floor(vp.width);cv.height=Math.floor(vp.height);
    box.appendChild(cv);
    await page.render({canvasContext:cv.getContext('2d'),viewport:vp}).promise;
  }
}
let zoomLevel=1;
function applyZoom(){ document.getElementById('pages').style.transform='scale('+zoomLevel+')'; document.getElementById('pages').style.transformOrigin='top center'; }
document.getElementById('btnZoomIn').addEventListener('click',()=>{ zoomLevel=Math.min(2,zoomLevel+0.15); applyZoom(); });
document.getElementById('btnZoomOut').addEventListener('click',()=>{ zoomLevel=Math.max(0.5,zoomLevel-0.15); applyZoom(); });
document.getElementById('btnPrint').addEventListener('click',()=>{ document.title=docName; window.print(); });
window.addEventListener('beforeprint',()=>{ document.title=docName; });
(async()=>{
  const data=await pdfBlob.arrayBuffer();
  pdfDoc=await pdfjsLib.getDocument({data}).promise;
  await renderAll();
})();
</script>
</body></html>

