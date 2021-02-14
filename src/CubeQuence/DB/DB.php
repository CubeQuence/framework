<?php

namespace CQ\DB;

use Medoo\Medoo;

use CQ\Config\Config;

class DB
{
    /**
     * Connect to DB.
     */
    public function __construct()
    {
        $GLOBALS['cq_database'] = new Medoo([
            'database_type' => 'mysql',
            'server' => Config::get(key: 'database.host'),
            'port' => Config::get(key: 'database.port'),
            'database_name' => Config::get(key: 'database.database'),
            'username' => Config::get(key: 'database.username'),
            'password' => Config::get(key: 'database.password'),
        ]);
    }

    /**
     * Select data from database.
     *
     * @param string $table
     * @param array  $columns
     * @param array  $where
     *
     * @return array|null
     */
    public static function select(string $table, array $columns, array $where) : array|null
    {
        return $GLOBALS['cq_database']->select($table, $columns, $where);
    }

    /**
     * Get only one record from table.
     *
     * @param string    $table
     * @param array     $columns
     * @param array|int $where
     *
     * @return array|null
     */
    public static function get(string $table, array $columns, array|int $where) : array|null
    {
        return $GLOBALS['cq_database']->get($table, $columns, $where);
    }

    /**
     * Insert new records in table.
     *
     * @param string $table
     * @param array  $data
     *
     * @return array
     */
    public static function create(string $table, array $data) : array
    {
        $GLOBALS['cq_database']->insert($table, $data);

        return $data;
    }

    /**
     * Modify data in table.
     *
     * @param string    $table
     * @param array     $data
     * @param array|int $where
     *
     * @return array|null
     */
    public static function update(string $table, array $data, array|int $where) : array|null
    {
        $data = array_merge($data, ['updated_at' => date(format: 'Y-m-d H:i:s')]);
        $GLOBALS['cq_database']->update($table, $data, $where);

        return $data;
    }

    /**
     * Delete data from table.
     *
     * @param string $table
     * @param array  $where
     *
     * @return array|null
     */
    public static function delete(string $table, array $where) : array|null
    {
        return $GLOBALS['cq_database']->delete($table, ['AND' => $where]);
    }

    /**
     * Determine whether the target data existed.
     *
     * @param string $table
     * @param array $where
     *
     * @return array|null
     */
    public static function has(string $table, array $where) : array|null
    {
        return $GLOBALS['cq_database']->has($table, $where);
    }

    /**
     * Counts the number of rows.
     *
     * @param string $table
     * @param array $where
     *
     * @return int|null
     */
    public static function count(string $table, array $where) : int|null
    {
        return $GLOBALS['cq_database']->count($table, $where);
    }
}
