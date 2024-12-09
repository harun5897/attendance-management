<?php
class Database {
    private $host = "localhost";
    private $databaseName = "attendance";
    private $username = "root";
    private $password = "";
    private $conn = null;

    public function connect() {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO(
                    "mysql:host={$this->host};dbname={$this->databaseName}",
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo json_encode([
                    'success' => false,
                    'data' => null,
                    'message' => 'Database connection failed: ' . $e->getMessage()
                ]);
                exit();
            }
        }
        return $this->conn;
    }

    public function disconnect() {
        $this->conn = null;
    }
}
?>
