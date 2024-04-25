<?php

namespace Framework;

use PDO, PDOException, Exception;

class Database {
    public $connection;

    /**
     * Constructor for Database class
     * 
     * @param array $config
     */
    public function __construct($config) {
        $data_source_name = "mysql:host={$config['host']};port={$config['port']};dbname={$config['db_name']}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];

        try {
            $this->connection = new PDO($data_source_name, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: {$e->getMessage()}");
        }
    }

    /**
     * Query the database
     * 
     * @param string $query
     * @param array $params
     * 
     * @return PDOStatement
     * 
     * @throws PDOException
     */
    public function query($query, $params = []) {
        try {
            $statement = $this->connection->prepare($query);

            // Bind named parameters
            foreach($params as $param => $value) {
                $statement->bindValue(':' . $param, $value);
            }

            $statement->execute();
            return $statement;
        } catch (PDOException $e) {
            throw new Exception("Query failed to execute: {$e->getMessage()}");
        }
    }
}