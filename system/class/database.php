<?php

/**
 * Class used for interaction with databases.
 *
 * @copyright 2017 BronyCenter
 * @author Assertrex <norbert.gotowczyc@gmail.com>
 * @since 0.1.0
 */
class Database
{
    /**
     * Object of a system class.
     *
     * @since 0.1.0
     * @var null|object
     */
    private $system = null;

    /**
     * Object of connection with database.
     *
     * @since 0.1.0
     * @var null|object
     */
    private $connection = null;

    /**
     * Try to connect into database.
     *
     * @since 0.1.0
     * @var object $o_system Object of a system class.
     */
    public function __construct($o_system)
    {
        // Store required class object in a property.
        $this->system = $o_system;

        // Store database credentials in a variable.
        $credentials = $this->system->getSettings('database');

        // Try to create a database connection with PDO.
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
            // Display PDO errors only on a development server.
            if ($this->system->development) {
                echo '[PDO Exception in database.php]: ' . $e->getMessage() . '<br />';
            } else {
                echo '[Database error]: System couldn\'t connect into database.';
            }
        }
    }

    /**
     * Perform an INSERT statement.
     *
     * @since 0.1.0
     * @var string $columns Comma-separated (with spaces) list of used columns.
     * @var string $table Name of used table.
     * @var string $extend Additional part of statement (after values).
     * @var array $values Values for positional placeholders.
     * @return string ID of last inserted row.
     */
    public function create($columns, $table, $extend, $values)
    {
        // Store columns as question marks.
		$placeholders = implode(', ', array_fill(0, count(explode(', ', $columns)), '?'));

        // Prepare and execute SQL INSERT statement.
		$statement = $this->connection->prepare("INSERT INTO $table ($columns) VALUES ($placeholders) $extend");
		$statement->execute($values);

        // Return ID of a last inserted row.
		return $this->connection->lastInsertId();
    }

    /**
     * Perform a SELECT statement.
     *
     * @since 0.1.0
     * @var string $columns Comma-separated (with spaces) list of used columns.
     * @var string $table Name of used table.
     * @var string $extend Additional part of statement (after table name).
     * @var array $values Values for positional placeholders.
     * @var boolean $multiple Values for positional placeholders.
     * @return array Selected row/rows containing selected columns values.
     */
    public function read($columns, $table, $extend, $values, $multiple = true)
    {
        // Prepare and execute SQL SELECT statement.
        $statement = $this->connection->prepare("SELECT $columns FROM $table $extend");
        $statement->execute($values);

        // Return array of all selected rows.
        if ($multiple === true) {
            return $statement->fetchAll();
        }

        // Return array of a single row if requested.
        return $statement->fetch();
    }

    /**
     * Perform an UPDATE statement.
     *
     * @since 0.1.0
     * @var string $columns Comma-separated (with spaces) list of used columns.
     * @var string $table Name of used table.
     * @var string $extend Additional part of statement (after columns).
     * @var array $values Values for positional placeholders.
     * @return integer Amount of updated rows.
     */
    public function update($columns, $table, $extend, $values)
    {
        // Store columns to contain values as question marks.
        $columns = explode(', ', $columns);
        $columns = implode(' = ?, ', $columns);

        // Add question mark value on last item too.
        $columns = $columns . ' = ?';

        // Prepare and execute SQL UPDATE statement.
		$statement = $this->connection->prepare("UPDATE $table SET $columns $extend");
		$statement->execute($values);

        // Return amount of updated rows.
		return $statement->rowCount();
    }

    /**
     * Perform a DELETE statement.
     *
     * @since 0.1.0
     * @var string $table Name of used table.
     * @var string $extend Additional part of statement (after table name).
     * @var array $values Values for positional placeholders.
     * @return integer Amount of deleted rows.
     */
    public function delete($table, $extend, $values)
    {
        // Prepare and execute SQL DELETE statement.
		$statement = $this->connection->prepare("DELETE FROM $table $extend");
		$statement->execute($values);

        // Return number of deleted rows.
        // FIXME Probably does not return a value.
		return $statement->rowCount();
    }
}
