<?php

class Database {
    public $connection;

    /**
     * Constructor for Database class
     * @param array $config
     */
    public function __construct($config) {
        $data_source_name = "mysql:host={$config['host']};port={$config['port']};dbname={$config['db_name']}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->connection = new PDO($data_source_name, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: {$e->getMessage()}");
        }
    }
}