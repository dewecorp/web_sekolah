<?php
declare(strict_types=1);
return [
    'name' => $_ENV['APP_NAME'] ?? 'Sekolah CMS',
    'url' => $_ENV['APP_URL'] ?? 'http://localhost/web_sekolah',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'timezone' => 'Asia/Jakarta',
    'upload_max_mb' => 5,
    'allowed_image_mimes' => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'],
    'blocked_ext' => ['php','phtml','php5','phar','exe','sh','js','html'],
];
