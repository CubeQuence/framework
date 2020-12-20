<?php

namespace CQ\DB;

// TODO: encrypt all values except ID
use CQ\Crypto\Symmetric;

class EncryptedDB extends DB
{
    /**
     * Select data from database.
     *
     * @param string $table
     * @param array  $columns
     * @param array  $where
     *
     * @return array|null
     */
    public static function select($table, $columns, $where)
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
    public static function get($table, $columns, $where)
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
    public static function create($table, $data)
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
    public static function update($table, $data, $where)
    {
        $data = array_merge($data, ['updated_at' => date('Y-m-d H:i:s')]);
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
    public static function delete($table, $where)
    {
        return $GLOBALS['cq_database']->delete($table, ['AND' => $where]);
    }

    /**
     * Determine whether the target data existed.
     *
     * @param string $table
     * @param array  $where
     *
     * @return array|null
     */
    public static function has($table, $where)
    {
        return $GLOBALS['cq_database']->has($table, $where);
    }

    /**
     * Counts the number of rows.
     *
     * @param string $table
     * @param array  $where
     *
     * @return int|null
     */
    public static function count($table, $where)
    {
        return $GLOBALS['cq_database']->count($table, $where);
    }
}
