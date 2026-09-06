<?php
declare(strict_types=1);
require __DIR__ . '/config/database.php'; // load .env + return config array dibuang, ENV terisi
require __DIR__ . '/config/constants.php';
foreach (['Database','Session','Security','Validator','Helper','Auth','Router'] as $__c) require_once __DIR__ . "/core/{$__c}.php";
$APP = require __DIR__ . '/config/app.php';
Session::start();
$__uri = Router::uri();
if (str_starts_with($__uri, '/install')) { require __DIR__ . '/install/index.php'; exit; }
try { $DB = Database::conn(); } catch (Throwable $e) { header('Location: '.BASE_URL.'/install'); exit; }
try { Auth::tryRemember($DB); } catch (Throwable) {}
Router::dispatch();
