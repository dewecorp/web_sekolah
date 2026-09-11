<?php $u = Auth::user(); $brandName = Database::setting('school_name','SchoolCMS'); $adminFav = Database::setting('favicon','') ?: Database::setting('logo',''); ?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php if($adminFav!==''): ?><link rel="icon" href="<?= Helper::e(Helper::upload($adminFav)) ?>"><?php endif; ?>
<title><?= Helper::e($title ?? 'Admin') ?> - <?= Helper::e($brandName) ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--font-body:"Plus Jakarta Sans",system-ui,-apple-system,"Segoe UI",sans-serif}
html,body,input,select,textarea,button{font-family:var(--font-body)!important}
h1,h2,h3{font-family:var(--font-body)!important;letter-spacing:-.02em}
.font-mono,code,kbd,pre{font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace!important}
.swal2-popup{font-family:var(--font-body)!important}
.tox-tinymce,.tox .tox-edit-area__iframe{font-family:var(--font-body)!important}
#sideNav{scrollbar-width:thin;scrollbar-color:#10b981 #f1f5f9;overscroll-behavior:contain}
#sideNav::-webkit-scrollbar{width:6px}
#sideNav::-webkit-scrollbar-track{background:#f1f5f9}
#sideNav::-webkit-scrollbar-thumb{background:#10b981;border-radius:99px}
input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),select,textarea{border:1px solid #e2e8f0!important;border-color:#e2e8f0!important;background:#fff!important;box-shadow:0 1px 2px rgba(15,23,42,.06),0 4px 12px rgba(15,23,42,.04)!important;border-radius:.65rem!important;outline:none!important}
input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):focus,select:focus,textarea:focus,input:focus-visible,select:focus-visible,textarea:focus-visible{outline:none!important;border:1px solid #10b981!important;border-color:#10b981!important;box-shadow:0 0 0 3px rgba(16,185,129,.18),0 4px 14px rgba(16,185,129,.12)!important}
input[type="file"]{border:1px dashed #cbd5e1!important;background:#f8fafc!important;box-shadow:inset 0 1px 2px rgba(15,23,42,.04)!important;border-radius:.65rem!important}
select{appearance:none!important;-webkit-appearance:none!important;background-image:none!important}
.cselect{position:relative;min-width:0}
.cselect-btn{width:100%;display:flex;align-items:center;justify-content:space-between;gap:.5rem;background:#fff;border:1px solid #e2e8f0;border-radius:.65rem;padding:.5rem .75rem;box-shadow:0 1px 2px rgba(15,23,42,.06),0 4px 12px rgba(15,23,42,.04);font-size:.875rem;text-align:left}
.cselect.open .cselect-btn,.cselect-btn:focus{outline:none;border-color:#10b981;box-shadow:0 0 0 3px rgba(16,185,129,.18),0 4px 14px rgba(16,185,129,.12)}
.cselect-list{position:absolute;z-index:9999;top:calc(100% + 6px);left:0;right:0;background:#fff;border:1px solid #e2e8f0;border-radius:.9rem;box-shadow:0 18px 45px rgba(15,23,42,.16);padding:.35rem;max-height:240px;overflow:auto}
[id$="Modal"] .relative.w-full.bg-white{overflow:visible!important;border-radius:1rem!important}
[id$="Modal"] .relative.w-full.bg-white>div:first-child{border-radius:1rem 1rem 0 0!important}
[id$="Modal"] form{overflow:visible!important;border-radius:0 0 1rem 1rem!important;background:#fff}
.cselect-opt{display:flex;align-items:center;width:100%;text-align:left;padding:.5rem .7rem;border-radius:.6rem;font-size:.875rem;background:transparent;border:0}
.cselect-opt:hover{background:#ecfdf5}
main label.grid{gap:.3rem!important}
main label.grid input,main label.grid select,main label.grid textarea,main label.grid .tox-tinymce{margin-top:0!important}
form.grid{gap:.6rem!important;align-content:start;align-items:start}
form.grid>div{min-height:0;height:auto;align-content:start}
main table{min-width:0}
main .overflow-x-auto{max-width:100%;overflow-x:auto;overflow-y:visible}
html,body{height:100%;max-width:100%;overflow-x:clip}
@supports not (overflow:clip){html,body{overflow-x:hidden}}
body{min-height:100vh;display:flex;flex-direction:column}
body>div.flex{flex:1 0 auto;max-width:100%;min-width:0}
main{flex:1 0 auto;display:flex;flex-direction:column;min-width:0}
main>*{min-width:0;max-width:100%}
main input,main select,main textarea{max-width:100%;min-width:0}
.tox-tinymce{width:100%!important;max-width:100%!important;min-width:0;border-radius:.65rem!important}
.tox-tinymce-aux{z-index:10000!important}
.tox .tox-menubar{padding:2px 6px!important;font-size:11px!important}
.tox .tox-toolbar__group{padding:2px!important;gap:1px!important}
.tox .tox-toolbar__primary{padding:2px 3px!important}
.tox .tox-tbtn{width:34px!important;height:32px!important;margin:1px!important;border-radius:5px!important;color:#0f172a!important}
.tox .tox-tbtn svg,.tox .tox-icon svg{width:20px!important;height:20px!important;color:#0f172a!important;opacity:1!important}
.tox .tox-tbtn svg path,.tox .tox-icon svg path{fill:currentColor!important;stroke:none!important}
.tox .tox-tbtn__icon-wrap{width:24px!important;height:24px!important;display:inline-flex!important;align-items:center!important;justify-content:center!important;overflow:visible!important}
.tox .tox-tbtn svg,.tox .tox-icon svg{overflow:visible!important;flex:none!important}
.tox .tox-tbtn:hover{background:#e2e8f0!important}
.tox .tox-tbtn:hover,.tox .tox-tbtn:focus{color:#047857!important}
.tox .tox-tbtn:hover svg,.tox .tox-tbtn:focus svg{color:#047857!important}
.tox .tox-tbtn--enabled,.tox .tox-tbtn--enabled:hover{background:#d1fae5!important}
.tox .tox-tbtn--enabled,.tox .tox-tbtn--enabled svg{color:#047857!important}
.tox .tox-tbtn:disabled,.tox .tox-tbtn:disabled svg{color:#64748b!important;opacity:.85!important}
.tox .tox-tbtn--select{width:auto!important;min-width:62px!important;padding:0 5px!important;font-size:11px!important}
.tox .tox-tbtn__select-label{color:#0f172a!important;font-weight:600!important}
.tox .tox-statusbar{font-size:10px!important;padding:2px 6px!important}
main .grid>*,main .flex>*{min-width:0}
[id$="Modal"] .relative.w-full{max-width:min(560px,calc(100vw - 2rem))!important}
[id="pageModal"] .relative.w-full,[id="postModal"] .relative.w-full{max-width:min(720px,calc(100vw - 2rem))!important}
main>footer{margin-top:auto;flex-shrink:0}
:root{--adminbar:57px}
#adminTopbar{position:fixed!important;top:0!important;left:0!important;right:0!important;z-index:50!important;background-color:#047857!important;color:#fff!important}
body{padding-top:var(--adminbar)!important}
#sidebar{position:fixed!important;top:var(--adminbar)!important;bottom:0!important;left:0!important;z-index:40!important;height:calc(100vh - var(--adminbar))!important;margin-top:0!important}
@media(max-width:767px){#sidebar:not(.open){display:none!important}}
@media(min-width:768px){body>div.flex{padding-left:15rem}}</style></head>
<body class="bg-slate-100 text-slate-800 flex flex-col min-h-screen">
<script>
window.RichEditorCreate=function(el,options){
  if(!window.tinymce)return Promise.reject(new Error('Editor tidak tersedia'));
  const uploadUrl=<?= json_encode(Helper::url('admin/media'),JSON_UNESCAPED_SLASHES) ?>;
  const csrf=<?= json_encode(Security::csrfToken()) ?>;
  return window.tinymce.init(Object.assign({
    target:el,base_url:'https://cdn.jsdelivr.net/npm/tinymce@7.6.1',suffix:'.min',license_key:'gpl',height:460,menubar:'file edit view insert format tools table help',toolbar_mode:'wrap',
    plugins:'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount codesample directionality emoticons',
    toolbar:['undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor removeformat','alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image imageleft imagecenter imageright media table | blockquote hr charmap emoticons codesample | searchreplace visualblocks code preview fullscreen help'],
     branding:false,promotion:false,convert_urls:false,automatic_uploads:true,file_picker_types:'image',media_live_embeds:true,extended_valid_elements:'iframe[src|width|height|style|frameborder|allowfullscreen|loading|referrerpolicy|title|allow|scrolling],table[class|style|border|cellpadding|cellspacing|width],tr[class|style],td[class|style|colspan|rowspan|width|align|valign],th[class|style|colspan|rowspan|width|scope]',invalid_elements:'',sandbox_iframes:false,table_default_styles:{},table_default_attributes:{},table_class_list:[{title:'Default',value:''},{title:'Garis',value:'tbl-line'}],
    image_advtab:true,
    content_style:'body{font-family:"Plus Jakarta Sans",sans-serif;font-size:14px;line-height:1.65;padding:12px 16px}ul,ol{padding-left:2rem;margin:.75rem 0;list-style-position:outside}ul{list-style-type:disc}ol{list-style-type:decimal}img{max-width:100%;height:auto}figure.image{display:table;margin:1rem 0;clear:both}figure.image.align-center{margin-left:auto;margin-right:auto}figure.image.align-left{margin-left:0;margin-right:auto}figure.image.align-right{margin-left:auto;margin-right:0}figure.image img{display:block;max-width:100%;height:auto}table{width:100%;border-collapse:collapse}th,td{border:1px solid #cbd5e1;padding:6px}',
    setup:function(editor){
      const alignImage=position=>{
        let node=editor.selection.getNode();
        if(!node||node===editor.getBody())return;
        let figure=node.nodeName==='FIGURE'?node:editor.dom.getParent(node,'FIGURE');
        let img=node.nodeName==='IMG'?node:(figure?figure.querySelector('img'):editor.dom.getParent(node,'IMG'));
        if(!figure&&!img)return;
        const target=figure||img;
        ['align-left','align-center','align-right'].forEach(c=>editor.dom.removeClass(target,c));
        editor.dom.addClass(target,position==='center'?'align-center':position==='right'?'align-right':'align-left');
        const margins=position==='center'?['auto','auto']:position==='right'?['auto','0']:['0','auto'];
        editor.dom.setStyles(target,{display:figure?'table':'block',float:'none','margin-left':margins[0],'margin-right':margins[1]});
        if(img&&figure)editor.dom.setStyles(img,{display:'block',float:'none','max-width':'100%',height:'auto'});
        editor.nodeChanged();
      };
      editor.ui.registry.addButton('imageleft',{icon:'align-left',tooltip:'Gambar rata kiri',onAction:()=>alignImage('left')});
      editor.ui.registry.addButton('imagecenter',{icon:'align-center',tooltip:'Gambar rata tengah',onAction:()=>alignImage('center')});
      editor.ui.registry.addButton('imageright',{icon:'align-right',tooltip:'Gambar rata kanan',onAction:()=>alignImage('right')});
      editor.ui.registry.addContextToolbar('imagealignment',{predicate:node=>node&&(node.nodeName==='IMG'||node.nodeName==='FIGURE'),items:'imageleft imagecenter imageright | imageoptions',position:'node',scope:'node'});
    },
    images_upload_handler:function(blobInfo){return new Promise((resolve,reject)=>{const fd=new FormData();fd.append('csrf',csrf);fd.append('act','ckeditor');fd.append('upload',blobInfo.blob(),blobInfo.filename());fetch(uploadUrl,{method:'POST',body:fd}).then(r=>r.json()).then(j=>{if(j&&j.uploaded&&j.url)resolve(j.url);else reject(j&&(j.msg||j.message)||'Upload gagal')}).catch(()=>reject('Upload gagal'))})}
  },options||{})).then(editors=>{const editor=editors[0];editor.setData=value=>editor.setContent(value||'');editor.getData=()=>editor.getContent();editor.sourceElement=el;return editor});
};
</script>
<?php require ROOT.'/templates/admin/navbar.php'; ?>
<div class="flex items-stretch flex-1 w-full">
<?php require ROOT.'/templates/admin/sidebar.php'; ?>
<main class="flex-1 p-4 md:p-6 w-full min-w-0 flex flex-col">
