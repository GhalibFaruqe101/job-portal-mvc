<?php
// Recruiter Module: User Model
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
     * Create a new recruiter user + blank recruiter profile (transactional)
     */
    public function createUser($name, $email, $phone, $password, $agency_name) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'recruiter';

        $this->conn->begin_transaction();

        try {
            // Insert into users table
            $stmt = $this->conn->prepare(
                "INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssss", $name, $email, $phone, $password_hash, $role);

            if (!$stmt->execute()) {
                throw new Exception("User insert failed");
            }

            $user_id = $this->conn->insert_id;

            // Create a blank recruiter profile
            $stmt2 = $this->conn->prepare(
                "INSERT INTO recruiter_profiles (user_id, agency_name) VALUES (?, ?)"
            );
            $stmt2->bind_param("is", $user_id, $agency_name);

            if (!$stmt2->execute()) {
                throw new Exception("Profile insert failed");
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("createUser transaction failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify a password against stored hash
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
?>
