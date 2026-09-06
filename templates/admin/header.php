<?php $u = Auth::user(); ?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= Helper::e($title ?? 'Admin') ?> - SchoolCMS</title>
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
#sideNav::-webkit-scrollbar-thumb{background:#10b981;border-radius:99px}</style></head>
<body class="bg-slate-100 text-slate-800">
<?php require ROOT.'/templates/admin/navbar.php'; ?>
<div class="flex items-start">
<?php require ROOT.'/templates/admin/sidebar.php'; ?>
<main class="flex-1 p-4 md:p-6 w-full min-w-0">
