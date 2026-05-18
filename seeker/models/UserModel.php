<?php
// Seeker Module: User Model
class UserModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Find a user by email address
     */
    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Create a new seeker user
     */
    public function createUser($name, $email, $phone, $password) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'seeker';
        $stmt = $this->conn->prepare(
            "INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssss", $name, $email, $phone, $password_hash, $role);
        return $stmt->execute();
    }

    /**
     * Verify a password against stored hash
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
?>
