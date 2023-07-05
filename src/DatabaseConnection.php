<?php

class DatabaseConnection
{
    static $servername = "localhost";
    static $username = "root";
    static $password = "Batata.2021";
    static $dbname = "desafio";
    static $conn;

    public static function connect()
    {
        try {
            self::$conn = new PDO("mysql:host=" . self::$servername . ";dbname=" . self::$dbname, self::$username, self::$password);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Falha na conexão: " . $e->getMessage());
        }
    }

    public static function getConnection()
    {
        if (!self::$conn) {
            self::connect();
        }
        return self::$conn;
    }
}
