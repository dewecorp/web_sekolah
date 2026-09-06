<?php
declare(strict_types=1);
define('ROOT', dirname(__DIR__));
define('BASE_URL', rtrim($_ENV['APP_URL'] ?? 'http://localhost/web_sekolah', '/'));
date_default_timezone_set('Asia/Jakarta');

