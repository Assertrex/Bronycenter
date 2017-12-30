<?php

/**
* Handles database connection and provides simple CRUD functions for PDO
* operations
*
* @since Release 0.1.0
*/

namespace AssertrexPHP;

use PDO;
use PDOException;

class Database
{
    /**
     * Singleton instance of a current class
     *
     * @since Release 0.1.0
     */
    private static $instance = null;

    /**
     * Place for instance of a config class
     *
     * @since Release 0.1.0
     */
    private $config = null;

    /**
     * Place for instance of a flash class
     *
     * @since Release 0.1.0
     */
    private $flash = null;

    /**
     * Place for object of a database PDO connection
     *
     * @since Release 0.1.0
     */
    private $connection = null;

    /**
     * Create a database connection on class initialization
     *
     * @since Release 0.1.0
     */
    public function __construct()
    {
        $this->flash = Flash::getInstance();
        $this->config = Config::getInstance();

        // Get database configuration
        $credentials = $this->config->getSection('database');

        // Try to create a database connection with PDO
        try {
            $this->connection = new PDO(
                $credentials['driver'] . ':host=' .
                    $credentials['hostname'] . ';dbname=' .
                    $credentials['database'] . ';charset=' .
                    $credentials['charset'],
                $credentials['username'],
                $credentials['password'],
                [
                    PDO::MYSQL_ATTR_FOUND_ROWS => true,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );
        } catch (PDOException $e) {
            // Display error message on exception
            echo 'Connection with database couldn\'t be established!';

            // Stop execution of the rest of code
            die();
        }
    }

    /**
     * Check if instance of current class is existing and create and/or return it
     *
     * @since Release 0.1.0
     * @var boolean Set as true to reset class instance
     * @return object Instance of a current class
     */
     public static function getInstance($reset = false) {
         if (!self::$instance || $reset === true) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Perform an INSERT statement
     *
     * @since 0.1.0
     * @var string $columns Comma-separated (with spaces) list of used columns
     * @var string $table Name of used table
     * @var string $extend Additional part of statement (after values)
     * @var array $values Values for positional placeholders
     * @return string ID of last inserted row
     */
    public function create($columns, $table, $extend, $values)
    {
        // Store columns as question marks
		$placeholders = implode(', ', array_fill(0, count(explode(', ', $columns)), '?'));

        // Prepare and execute SQL INSERT statement
		$statement = $this->connection->prepare("INSERT INTO $table ($columns) VALUES ($placeholders) $extend");
		$statement->execute($values);

        // Return ID of a last inserted row
		return $this->connection->lastInsertId();
    }

    /**
     * Perform a SELECT statement
     *
     * @since 0.1.0
     * @var string $columns Comma-separated (with spaces) list of used columns
     * @var string $table Name of used table
     * @var string $extend Additional part of statement (after table name)
     * @var array $values Values for positional placeholders
     * @var boolean $multiple Values for positional placeholders
     * @return array Selected row/rows containing selected columns values
     */
    public function read($columns, $table, $extend, $values, $multiple = true)
    {
        // Prepare and execute SQL SELECT statement
        $statement = $this->connection->prepare("SELECT $columns FROM $table $extend");
        $statement->execute($values);

        // If requested, return only one selected row
        if ($multiple === false)
            return $statement->fetch();

        // Return all selected rows by default
        return $statement->fetchAll();
    }

    /**
     * Perform an UPDATE statement
     *
     * @since 0.1.0
     * @var string $columns Comma-separated (with spaces) list of used columns
     * @var string $table Name of used table
     * @var string $extend Additional part of statement (after columns)
     * @var array $values Values for positional placeholders
     * @return integer Amount of updated rows
     */
    public function update($columns, $table, $extend, $values)
    {
        // Store columns to contain values as question marks
        $columns = explode(', ', $columns);
        $columns = implode(' = ?, ', $columns);

        // Add question mark value on last item too
        $columns = $columns . ' = ?';

        // Prepare and execute SQL UPDATE statement
		$statement = $this->connection->prepare("UPDATE $table SET $columns $extend");
		$statement->execute($values);

        // Return amount of updated rows
		return $statement->rowCount();
    }

    /**
     * Perform a DELETE statement
     *
     * @since 0.1.0
     * @var string $table Name of used table
     * @var string $extend Additional part of statement (after table name)
     * @var array $values Values for positional placeholders
     * @return integer Amount of deleted rows
     */
    public function delete($table, $extend, $values)
    {
        // Prepare and execute SQL DELETE statement
		$statement = $this->connection->prepare("DELETE FROM $table $extend");
		$statement->execute($values);

        // Return number of deleted rows
		return $statement->rowCount();
    }
}
