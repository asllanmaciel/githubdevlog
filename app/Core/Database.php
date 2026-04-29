<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $dsn = Env::get('DB_DSN');
        $user = Env::get('DB_USER');
        $pass = Env::get('DB_PASS');
        $name = Env::get('DB_NAME', 'github_devlog_ai');

        if ($dsn === null || trim($dsn) === '') {
            $defaultSqlite = __DIR__ . '/../../database/devlog.sqlite';
            $dsn = "sqlite:$defaultSqlite";
        }

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            if (str_starts_with($dsn, 'sqlite:')) {
                self::$connection = new PDO($dsn, null, null, $options);
            } else {
                self::$connection = new PDO($dsn, $user ?: null, $pass ?: null, $options);
            }
        } catch (PDOException $e) {
            throw new \RuntimeException('Falha na conexão com banco de dados: ' . $e->getMessage());
        }

        self::migrate();
        return self::$connection;
    }

    private static function migrate(): void
    {
        $schemaPath = __DIR__ . '/../../database/schema.sql';
        $sql = is_file($schemaPath) ? trim((string) file_get_contents($schemaPath)) : '';
        if ($sql === '') {
            return;
        }

        $pdo = self::$connection;
        if (!$pdo) {
            return;
        }

        $statements = preg_split('/;\s*$/m', $sql);
        foreach ($statements as $statement) {
            $stmt = trim($statement);
            if ($stmt === '') {
                continue;
            }
            $pdo->exec($stmt);
        }
    }

    public static function getConnection(): PDO
    {
        return self::connect();
    }
}
