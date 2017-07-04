<?php

/**
 * Class used for interaction with databases
 *
 * @since 0.1.0
 */
class Database
{
    /**
     * Object of system class
     *
     * @since 0.1.0
     * @var object
     */
    private $system = null;

    /**
     * Database connection
     *
     * @since 0.1.0
     * @var object
     */
    private $connection = null;

    /**
     * Connect into database while creating an object
     *
     * @since 0.1.0
     * @var object $o_system Object of system class
     */
    public function __construct($o_system)
    {
        $this->system = $o_system;

        // Store database credentials in a variable
        $credentials = $this->system->getSettings('database');

        // Create a connection via PDO
        try {
            $this->connection = new PDO(
                $credentials['driver'] . ':host=' . $credentials['hostname'] . ';dbname=' . $credentials['database'] . ';charset=' . $credentials['charset'],
                $credentials['username'], $credentials['password'], [
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );
        } catch (PDOException $e) {
            // Display PDO stack trace only on testing server
            if ($this->system->testing) {
                echo 'Connection failed: ' . $e->getMessage();
            } else {
                echo 'Couldn\'t connect into database.';
            }
        }
    }

    /**
     * Perform an INSERT statement
     *
     * @since 0.1.0
     * @var string $columns Comma-separated (with spaces) list of used columns
     * @var string $table Name of used table
     * @var string $extend Additional part of statement
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

        // Return ID of last inserted row
		return $this->connection->lastInsertId();
    }

    /**
     * Perform an SELECT statement
     *
     * @since 0.1.0
     * @var string $columns Comma-separated (with spaces) list of used columns
     * @var string $table Name of used table
     * @var string $extend Additional part of statement
     * @var array $values Values for positional placeholders
     * @return array Selected rows containing columns values
     */
    public function read($columns, $table, $extend, $values)
    {
        // Prepare and execute SQL SELECT statement
        $statement = $this->connection->prepare("SELECT $columns FROM $table $extend");
        $statement->execute($values);

        // Return array of all selected rows
        return $statement->fetchAll();
    }

    /**
     * Perform an UPDATE statement
     *
     * @since 0.1.0
     * @var string $table Name of used table
     * @var string $columns Comma-separated (with spaces) list of used columns
     * @var string $extend Additional part of statement
     * @var array $values Values for positional placeholders
     * @return int Number of updated rows
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

        // Return number of updated rows
		return $statement->rowCount();
    }

    /**
     * Perform an DELETE statement
     *
     * @since 0.1.0
     * @var string $table Name of used table
     * @var string $extend Additional part of statement
     * @var array $values Values for positional placeholders
     * @return int Number of deleted rows
     */
    public function delete($table, $extend, $values)
    {
        // Prepare and execute SQL DELETE statement
		$statement = $this->connection->prepare("DELETE FROM $table $extend");
		$statement->execute($values);

        // Return number of deleted rows // FIXME Does not return true
		return $statement->rowCount();
    }
}
