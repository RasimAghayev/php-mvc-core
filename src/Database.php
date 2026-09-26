<?php

declare(strict_types=1);

namespace RasimAghayev\PhpMvcCore;

use PDO;
use PDOException;

class Database
{
    private string $host;
    private string $user;
    private string $pass;
    private string $dbname;
    private string $charset;
    private mixed $dbh = null;
    private ?string $error = null;
    private mixed $stmt = null;

    public function __construct(
        string $host = DB_HOST,
        string $user = DB_USER,
        string $pass = DB_PASS,
        string $dbname = DB_NAME,
        string $charset = CHARSET ?? 'utf8mb4'
    ) {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbname = $dbname;
        $this->charset = $charset;
        $this->connect();
    }

    private function connect(): void
    {
        try {
            $opt = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => true,
            ];
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=' . $this->charset;
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $opt);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            throw $e;
        }
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function query(string $query): void
    {
        $this->stmt = $this->dbh->prepare($query);
    }

    public function bind(string $param, mixed $value, ?int $type = null): void
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute(): bool
    {
        return $this->stmt->execute();
    }

    public function singleOBJ(): array
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function singleASS(): ?array
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    public function lastInsertId(): string
    {
        return $this->dbh->lastInsertId();
    }

    public function insertRecord(string $table, array $fields, array $values, bool $return = false): mixed
    {
        $newFields = '(`' . implode('`, `', $fields) . '`)';
        $newValues = "('" . implode("', '", array_map(fn($v) => addslashes((string)$v), $values)) . "')";
        $this->query("INSERT INTO {$table} {$newFields} VALUES {$newValues}");
        if ($this->execute()) {
            return $return ? $this->lastInsertId() : true;
        }
        return false;
    }
}
