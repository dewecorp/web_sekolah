<?php
declare(strict_types=1);
final class Validator {
    public static function required(array $d, array $fields): array {
        $e = [];
        foreach ($fields as $f) if (trim((string)($d[$f] ?? '')) === '') $e[$f] = 'Wajib diisi.';
        return $e;
    }
    public static function email(string $v): bool { return filter_var($v, FILTER_VALIDATE_EMAIL) !== false; }
    public static function len(string $v, int $min, int $max): bool { $l = mb_strlen($v); return $l >= $min && $l <= $max; }
}
