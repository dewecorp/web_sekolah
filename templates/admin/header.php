<?php $u = Auth::user(); $brandName = Database::setting('school_name','SchoolCMS'); ?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
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
.ck.ck-editor,.ck-content{font-family:var(--font-body)!important}
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
.cselect-list{position:absolute;z-index:60;top:calc(100% + 6px);left:0;right:0;background:#fff;border:1px solid #e2e8f0;border-radius:.9rem;box-shadow:0 18px 45px rgba(15,23,42,.16);padding:.35rem;max-height:240px;overflow:auto}
.cselect-opt{display:flex;align-items:center;width:100%;text-align:left;padding:.5rem .7rem;border-radius:.6rem;font-size:.875rem;background:transparent;border:0}
.cselect-opt:hover{background:#ecfdf5}
main label.grid{gap:.3rem!important}
main label.grid input,main label.grid select,main label.grid textarea,main label.grid .ck-editor{margin-top:0!important}
form.grid{gap:.6rem!important;align-content:start;align-items:start}
form.grid>div{min-height:0;height:auto;align-content:start}
main table{min-width:0}
main .overflow-x-auto{max-width:100%;overflow-x:auto;overflow-y:visible}
html,body{height:100%;max-width:100%;overflow-x:clip}
@supports not (overflow:clip){html,body{overflow-x:hidden}}
body{min-height:100vh;display:flex;flex-direction:column}
body>div.flex{flex:1 0 auto;max-width:100%;min-width:0}
main{flex:1 0 auto;display:flex;flex-direction:column;min-width:0;max-width:100%;overflow-x:clip}
main>*{min-width:0;max-width:100%}
main input,main select,main textarea{max-width:100%;min-width:0}
.ck-editor,.ck-editor__editable,.ck-content{max-width:100%!important;min-width:0;overflow-wrap:anywhere}
main .grid>*,main .flex>*{min-width:0;max-width:100%}
main>footer{margin-top:auto;flex-shrink:0}
:root{--adminbar:57px}
#adminTopbar{position:fixed!important;top:0!important;left:0!important;right:0!important;z-index:50!important;background-color:#047857!important;color:#fff!important}
body{padding-top:var(--adminbar)!important}
#sidebar{position:fixed!important;top:var(--adminbar)!important;bottom:0!important;left:0!important;z-index:40!important;height:calc(100vh - var(--adminbar))!important;margin-top:0!important}
@media(max-width:767px){#sidebar:not(.open){display:none!important}}
@media(min-width:768px){body>div.flex{padding-left:15rem}}</style></head>
<body class="bg-slate-100 text-slate-800 flex flex-col min-h-screen">
<?php require ROOT.'/templates/admin/navbar.php'; ?>
<div class="flex items-stretch flex-1 w-full">
<?php require ROOT.'/templates/admin/sidebar.php'; ?>
<main class="flex-1 p-4 md:p-6 w-full min-w-0 flex flex-col">
