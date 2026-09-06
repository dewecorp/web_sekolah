<?php $uri = Router::uri(); $seoMeta = [];
try { $seoMeta = Database::conn()->query("SELECT * FROM seo_settings LIMIT 1")->fetch() ?: []; } catch (Throwable) {}
$metaTitle = $metaTitle ?? Database::setting('meta_title', Database::setting('homepage_title','Sekolah CMS'));
$metaDesc = $metaDesc ?? Database::setting('meta_description','Website sekolah modern');
$metaKeys = Database::setting('meta_keywords','sekolah, pendidikan');
$ogImg = Database::setting('og_image', $seoMeta['og_image'] ?? '');
?>
<!DOCTYPE html><html lang="id" class="scroll-smooth"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= Helper::e($metaTitle) ?></title>
<meta name="description" content="<?= Helper::e($metaDesc) ?>">
<meta name="keywords" content="<?= Helper::e($metaKeys) ?>">
<link rel="canonical" href="<?= Helper::e(Helper::url(ltrim($uri,'/'))) ?>">
<meta property="og:title" content="<?= Helper::e($metaTitle) ?>">
<meta property="og:description" content="<?= Helper::e($metaDesc) ?>">
<meta property="og:type" content="website">
<?php if($ogImg): ?><meta property="og:image" content="<?= Helper::e(str_starts_with($ogImg,'http')?$ogImg:Helper::upload($ogImg)) ?>"><?php endif; ?>
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%23059669'/%3E%3Ctext x='32' y='44' font-size='34' text-anchor='middle' fill='white' font-family='sans-serif' font-weight='bold'%3ES%3C/text%3E%3C/svg%3E">
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={darkMode:'class',theme:{extend:{fontFamily:{sans:['"Plus Jakarta Sans"','system-ui','sans-serif']}}}}</script>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>document.documentElement.classList.add('js')</script>
<link rel="stylesheet" href="<?= Helper::asset('css/style.css') ?>">
<script>if(localStorage.theme==='dark')document.documentElement.classList.add('dark')</script>
<noscript><style>#loader{display:none!important}</style></noscript>
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 tracking-tight">
<div id="loader" class="fixed inset-0 z-[99] bg-white dark:bg-slate-950 grid place-items-center"><div class="w-10 h-10 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div></div>
<?php require ROOT.'/templates/frontend/navbar.php'; ?>
<main class="min-h-[60vh]">
