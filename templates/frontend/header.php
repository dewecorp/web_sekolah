<?php $uri = Router::uri(); $seoMeta = [];
try { $seoMeta = Database::conn()->query("SELECT * FROM seo_settings LIMIT 1")->fetch() ?: []; } catch (Throwable) {}
$schoolName = Database::setting('school_name','Sekolah');
$metaTitle = $metaTitle ?? Database::setting('meta_title', Database::setting('homepage_title',$schoolName.' - Website Resmi Sekolah'));
$metaDesc = $metaDesc ?? Database::setting('meta_description','Website resmi '.$schoolName.'. Informasi profil, berita, agenda, galeri, prestasi, guru, kurikulum, dan kontak sekolah.');
$metaKeys = Database::setting('meta_keywords', mb_strtolower($schoolName).', sekolah, pendidikan, profil sekolah, berita sekolah, agenda sekolah, galeri, prestasi, guru, kurikulum, kontak');
$ogImg = Database::setting('og_image', $seoMeta['og_image'] ?? '');
if($ogImg===''||$ogImg===null){ $ogImg = Database::setting('logo',''); }
$canonSet = Database::setting('canonical_url', $seoMeta['canonical_url'] ?? '');
$canonUrl = $canonSet!=='' ? $canonSet : Helper::url(ltrim($uri,'/'));
$robotsContent = Database::setting('robots_content','');
$verifyGoogle = Database::setting('google_verification','');
$verifyBing = Database::setting('bing_verification','');
$verifyYandex = Database::setting('yandex_verification','');
$analyticsId = Database::setting('ga_measurement_id','');
$siteTheme = Database::setting('site_theme','elegant');
if (!in_array($siteTheme,['elegant','classic','vibrant','editorial','minimal'],true)) $siteTheme='elegant';
$themePrimary = Database::setting('theme_primary','#059669');
$themeAccent = Database::setting('theme_accent','#f59e0b');
if (!preg_match('/^#[0-9a-fA-F]{6}$/',$themePrimary)) $themePrimary='#059669';
if (!preg_match('/^#[0-9a-fA-F]{6}$/',$themeAccent)) $themeAccent='#f59e0b';
$themeRadius = Database::setting('theme_radius','soft');
if (!in_array($themeRadius,['soft','square','round'],true)) $themeRadius='soft';
?>
<!DOCTYPE html><html lang="id" class="scroll-smooth"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= Helper::e($metaTitle) ?></title>
<meta name="description" content="<?= Helper::e($metaDesc) ?>">
<meta name="keywords" content="<?= Helper::e($metaKeys) ?>">
<meta name="author" content="<?= Helper::e($schoolName) ?>">
<meta name="publisher" content="<?= Helper::e($schoolName) ?>">
<meta name="robots" content="<?= Helper::e($robotsContent!==''?$robotsContent:'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1') ?>">
<meta name="googlebot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="bingbot" content="index, follow">
<meta name="language" content="Indonesian">
<meta name="revisit-after" content="1 days">
<meta name="rating" content="general">
<meta name="distribution" content="global">
<meta name="theme-color" content="<?= $themePrimary ?>">
<link rel="canonical" href="<?= Helper::e($canonUrl) ?>">
<meta property="og:locale" content="id_ID">
<meta property="og:site_name" content="<?= Helper::e($schoolName) ?>">
<meta property="og:title" content="<?= Helper::e($metaTitle) ?>">
<meta property="og:description" content="<?= Helper::e($metaDesc) ?>">
<meta property="og:url" content="<?= Helper::e($canonUrl) ?>">
<meta property="og:type" content="website">
<?php if($ogImg): ?><meta property="og:image" content="<?= Helper::e(str_starts_with($ogImg,'http')?$ogImg:Helper::upload($ogImg)) ?>"><meta property="og:image:alt" content="<?= Helper::e($metaTitle) ?>"><?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= Helper::e($metaTitle) ?>">
<meta name="twitter:description" content="<?= Helper::e($metaDesc) ?>">
<?php if($ogImg): ?><meta name="twitter:image" content="<?= Helper::e(str_starts_with($ogImg,'http')?$ogImg:Helper::upload($ogImg)) ?>"><?php endif; ?>
<?php if($verifyGoogle!==''): ?><meta name="google-site-verification" content="<?= Helper::e($verifyGoogle) ?>"><?php endif; ?>
<?php if($verifyBing!==''): ?><meta name="msvalidate.01" content="<?= Helper::e($verifyBing) ?>"><?php endif; ?>
<?php if($verifyYandex!==''): ?><meta name="yandex-verification" content="<?= Helper::e($verifyYandex) ?>"><?php endif; ?>
<script type="application/ld+json">{"@context":"https://schema.org","@type":"WebSite","name":<?= json_encode($schoolName,JSON_UNESCAPED_UNICODE) ?>,"url":<?= json_encode(Helper::url(),JSON_UNESCAPED_UNICODE) ?>,"inLanguage":"id-ID","publisher":{"@type":"Organization","name":<?= json_encode($schoolName,JSON_UNESCAPED_UNICODE) ?>,"url":<?= json_encode(Helper::url(),JSON_UNESCAPED_UNICODE) ?>}}</script>
<?php if(!empty($schemaNews)): ?><script type="application/ld+json"><?= json_encode($schemaNews,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?></script><?php endif; ?>
<?php if($analyticsId!==''): ?><script async src="https://www.googletagmanager.com/gtag/js?id=<?= Helper::e($analyticsId) ?>"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','<?= Helper::e($analyticsId) ?>')</script><?php endif; ?>
<?php $favLogo=Database::setting('logo',''); if($favLogo!==''): ?><link rel="icon" href="<?= Helper::e(Helper::upload($favLogo)) ?>"><?php else: ?><link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%23059669'/%3E%3Ctext x='32' y='44' font-size='34' text-anchor='middle' fill='white' font-family='sans-serif' font-weight='bold'%3ES%3C/text%3E%3C/svg%3E"><?php endif; ?>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={darkMode:'class',theme:{extend:{fontFamily:{sans:['"Plus Jakarta Sans"','system-ui','sans-serif']}}}}</script>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>document.documentElement.classList.add('js')</script>
<link rel="stylesheet" href="<?= Helper::asset('css/style.css') ?>">
<style>
:root{--school-primary:<?= $themePrimary ?>;--school-accent:<?= $themeAccent ?>;--school-radius:<?= $themeRadius==='square'?'8px':($themeRadius==='round'?'28px':'16px') ?>}
[class~="bg-emerald-500"],[class~="bg-emerald-600"]{background-color:var(--school-primary)!important}
[class~="bg-emerald-700"],[class~="bg-emerald-800"]{background-color:color-mix(in srgb,var(--school-primary),#071b18 24%)!important}
[class~="text-emerald-500"],[class~="text-emerald-600"],[class~="text-emerald-700"]{color:var(--school-primary)!important}
[class~="border-emerald-200"],[class~="border-emerald-300"],[class~="border-emerald-400"],[class~="border-emerald-500"],[class~="border-emerald-600"]{border-color:color-mix(in srgb,var(--school-primary),white 45%)!important}
[class~="bg-emerald-50"],[class~="bg-emerald-100"]{background-color:color-mix(in srgb,var(--school-primary),white 88%)!important}
[class~="bg-amber-400"],[class~="bg-amber-500"],[class~="bg-amber-600"]{background-color:var(--school-accent)!important}[class~="text-amber-400"],[class~="text-amber-500"],[class~="text-amber-600"],[class~="text-amber-700"]{color:var(--school-accent)!important}
[class*="from-emerald-"]{--tw-gradient-from:var(--school-primary) var(--tw-gradient-from-position)!important;--tw-gradient-to:color-mix(in srgb,var(--school-primary),transparent 100%) var(--tw-gradient-to-position)!important}
[class*="to-teal-"]{--tw-gradient-to:color-mix(in srgb,var(--school-primary),#0ea5e9 30%) var(--tw-gradient-to-position)!important}
::selection{background:var(--school-primary);color:white}a,button{transition-color:.2s ease,background-color .2s ease,border-color .2s ease,transform .2s ease}
.site-shell [class*="rounded-2xl"],.site-shell [class*="rounded-xl"]{border-radius:var(--school-radius)}
.theme-elegant{background-image:radial-gradient(circle at 8% 8%,color-mix(in srgb,var(--school-primary),transparent 92%),transparent 28%),linear-gradient(180deg,#f8fafc,#fff 38%,#f8fafc)}
.theme-elegant #mainNav{box-shadow:0 10px 35px rgba(15,23,42,.06)}
.theme-classic{font-family:Georgia,'Times New Roman',serif;letter-spacing:0}.theme-classic h1,.theme-classic h2,.theme-classic h3,.theme-classic h4{font-family:Georgia,'Times New Roman',serif}.theme-classic #mainNav{border-bottom:3px solid var(--school-accent)}
.theme-vibrant{background-image:radial-gradient(circle at 0 10%,color-mix(in srgb,var(--school-accent),transparent 86%),transparent 24%),radial-gradient(circle at 100% 30%,color-mix(in srgb,var(--school-primary),transparent 88%),transparent 28%)}
.theme-vibrant article,.theme-vibrant main [class*="rounded-2xl"]{box-shadow:0 12px 35px rgba(15,23,42,.08)}
.theme-editorial{background:#fffaf5}.theme-editorial h1,.theme-editorial h2,.theme-editorial h3{font-family:Georgia,'Times New Roman',serif;letter-spacing:-.025em}.theme-editorial #mainNav{border-bottom:4px solid var(--school-primary)}.theme-editorial article{border-top:3px solid var(--school-accent)}
.theme-minimal{background:#fff}.theme-minimal #mainNav{box-shadow:none;background:#fff!important}.theme-minimal main [class*="shadow"]{box-shadow:none!important}.theme-minimal article,.theme-minimal main [class*="rounded-2xl"]{border-color:#e2e8f0}
</style>
<script>if(localStorage.theme==='dark')document.documentElement.classList.add('dark')</script>
<noscript><style>#loader{display:none!important}</style></noscript>
</head>
<body class="site-shell theme-<?= Helper::e($siteTheme) ?> radius-<?= Helper::e($themeRadius) ?> font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 tracking-tight flex flex-col min-h-screen">
<div id="loader" class="fixed inset-0 z-[99] bg-white dark:bg-slate-950 grid place-items-center"><div class="w-10 h-10 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div></div>
<?php require ROOT.'/templates/frontend/navbar.php'; ?>
<main class="min-h-[60vh] flex-1 w-full flex flex-col">
