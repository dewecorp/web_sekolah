<?php
$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $_ENV[trim($k)] = trim($v, " \t\"'");
    }
}
$db = [
    'host'    => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'name'    => $_ENV['DB_NAME'] ?? 'school_cms',
    'user'    => $_ENV['DB_USER'] ?? 'root',
    'pass'    => $_ENV['DB_PASS'] ?? '',
    'charset' => 'utf8mb4',
];
$pdo = new PDO('mysql:host='.$db['host'].';dbname='.$db['name'].';charset='.$db['charset'], $db['user'], $db['pass']);
$stmt = $pdo->query('SELECT * FROM extracurriculars');
while($e = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Day: '" . $e['day'] . "' (len=" . strlen($e['day']) . ") HEX: " . bin2hex($e['day']) . PHP_EOL;
}