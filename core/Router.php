<?php
declare(strict_types=1);
final class Router {
 public static function uri(): string {
  $u = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
  $b = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
  if ($b !== '' && str_starts_with($u, $b)) $u = substr($u, strlen($b));
  return '/' . trim($u ?: '/', '/');
 }
 public static function dispatch(): void {
  $DB = Database::conn();
  $APP = require ROOT . '/config/app.php';
  $db = $DB;
  $uri = self::uri();
  if (str_starts_with($uri, '/admin')) { require ROOT . '/admin/index.php'; return; }
  if (str_starts_with($uri, '/api')) { require ROOT . '/api/index.php'; return; }
  require ROOT . '/pages/router.php';
 }
}

