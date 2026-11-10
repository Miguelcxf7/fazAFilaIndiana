<?php
declare(strict_types=1);
/** Singleton: mantém uma única conexão PDO durante a requisição. */
final class Database {
    private static ?PDO $connection = null;
    private function __construct() {}
    public static function connection(): PDO {
        if (self::$connection === null) {
            $env = dirname(__DIR__, 2) . '/.env';
            if (!is_file($env)) throw new RuntimeException('Arquivo .env não encontrado. Copie .env.example para .env e configure o banco.');
            $vars = parse_ini_file($env, false, INI_SCANNER_RAW) ?: [];
            $connection = trim($vars['SRVDB'] ?? '');
            $user = trim($vars['DB_USER'] ?? '');
            $pass = (string)($vars['DB_PASS'] ?? '');
            // Aceita tanto o DSN PDO quanto a URL postgresql:// usada pelo projeto original.
            if (str_starts_with($connection, 'postgres://') || str_starts_with($connection, 'postgresql://')) {
                $url = parse_url($connection);
                if (!$url || empty($url['host'])) throw new RuntimeException('URL SRVDB inválida no .env.');
                $host = $url['host']; $port = $url['port'] ?? 5432; $dbname = ltrim($url['path'] ?? '', '/');
                $user = $user ?: urldecode($url['user'] ?? '');
                $pass = $pass !== '' ? $pass : urldecode($url['pass'] ?? '');
                $query = []; parse_str($url['query'] ?? '', $query);
                $ssl = $query['sslmode'] ?? 'require';
                $options = '';
                // Neon pode exigir que o endpoint seja informado nas opções do PostgreSQL.
                if (str_contains($host, '.neon.tech')) $options = ";options='endpoint=" . explode('.', $host)[0] . "'";
                $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode={$ssl}" . $options;
            } else {
                $dsn = $connection;
            }
            if (!$dsn || !$user) throw new RuntimeException('Configure SRVDB e DB_USER no arquivo .env.');
            self::$connection = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$connection;
    }
}
