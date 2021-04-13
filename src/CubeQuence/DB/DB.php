<?php

declare(strict_types=1);

namespace CQ\DB;

use CQ\Config\Config;
use Medoo\Medoo;

final class DB
{
    private static DB | null $instance = null;
    private static Medoo $client;

    /**
     * Connect to DB.
     */
    private function __construct()
    {
        self::$client = new Medoo([
            'database_type' => 'mysql',
            'server' => Config::get(
                key: 'database.host'
            ),
            'port' => Config::get(
                key: 'database.port'
            ),
            'database_name' => Config::get(
                key: 'database.database'
            ),
            'username' => Config::get(
                key: 'database.username'
            ),
            'password' => Config::get(
                key: 'database.password'
            ),
        ]);
    }

    /**
     * Get access to the Config singleton
     */
    private static function getInstance() : DB
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Select data from database.
     */
    public static function select(string $table, array $columns, array $where): array | null
    {
        $dbSingleton = self::getInstance();

        return $dbSingleton::$client->select($table, $columns, $where);
    }

    /**
     * Get only one record from table.
     */
    public static function get(string $table, array $columns, array | int $where): array | null
    {
        $dbSingleton = self::getInstance();

        return $dbSingleton::$client->get($table, $columns, $where);
    }

    /**
     * Insert new records in table.
     */
    public static function create(string $table, array $data): array
    {
        $dbSingleton = self::getInstance();

        $dbSingleton::$client->insert($table, $data);

        return $data;
    }

    /**
     * Modify data in table.
     */
    public static function update(string $table, array $data, array | int $where): array | null
    {
        $dbSingleton = self::getInstance();

        $data = array_merge(
            $data,
            ['updated_at' => date(format: 'Y-m-d H:i:s')]
        );

        $dbSingleton::$client->update($table, $data, $where);

        return $data;
    }

    /**
     * Delete data from table.
     */
    public static function delete(string $table, array $where): bool
    {
        $dbSingleton = self::getInstance();

        return (bool) $dbSingleton::$client->delete($table, ['AND' => $where]);
    }

    /**
     * Determine whether the target data existed.
     */
    public static function has(string $table, array $where): bool
    {
        $dbSingleton = self::getInstance();

        return $dbSingleton::$client->has($table, $where);
    }

    /**
     * Counts the number of rows.
     */
    public static function count(string $table, array $where): int | null
    {
        $dbSingleton = self::getInstance();

        return $dbSingleton::$client->count($table, $where);
    }
}
