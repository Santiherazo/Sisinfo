<?php
class Database {
    private $host = '82.180.172.103';
    private $db_name = 'u358414249_sisinfo';
    private $username = 'u358414249_sisinfo';
    private $password = 'sK2!aD1~eM5(jZ3,';
    private $conn;

    public function __construct() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
    }

    public function beginTransaction() {
        $this->conn->beginTransaction();
    }

    public function commit() {
        $this->conn->commit();
    }

    public function rollBack() {
        $this->conn->rollBack();
    }

    public function prepare($query) {
        return $this->conn->prepare($query);
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
