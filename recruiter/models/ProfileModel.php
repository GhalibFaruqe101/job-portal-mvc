<?php
// Recruiter Module: Profile Model
class ProfileModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Get combined user + recruiter profile data
     */
    public function getProfile($user_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT u.id, u.name, u.email, u.phone, u.profile_pic,
                    rp.agency_name, rp.specialization, rp.description, rp.website
             FROM users u
             LEFT JOIN recruiter_profiles rp ON u.id = rp.user_id
             WHERE u.id = ?"
        );
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update users table fields
     */
    public function updateUser($user_id, $name, $phone)
    {
        $stmt = $this->conn->prepare(
            "UPDATE users SET name = ?, phone = ? WHERE id = ?"
        );
        $stmt->bind_param("ssi", $name, $phone, $user_id);
        return $stmt->execute();
    }

    /**
     * Update or insert recruiter_profiles row
     */
    public function updateRecruiterProfile($user_id, $agency_name, $specialization, $description, $website)
    {
        // Check if profile row exists
        $check = $this->conn->prepare("SELECT id FROM recruiter_profiles WHERE user_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $exists = $check->get_result()->fetch_assoc();

        if ($exists) {
            $stmt = $this->conn->prepare(
                "UPDATE recruiter_profiles 
                 SET agency_name = ?, specialization = ?, description = ?, website = ?
                 WHERE user_id = ?"
            );
            $stmt->bind_param("ssssi", $agency_name, $specialization, $description, $website, $user_id);
        } else {
            $stmt = $this->conn->prepare(
                "INSERT INTO recruiter_profiles (user_id, agency_name, specialization, description, website)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("issss", $user_id, $agency_name, $specialization, $description, $website);
        }
        return $stmt->execute();
    }

    /**
     * Update profile picture path
     */
    public function updateProfilePic($user_id, $pic_path)
    {
        $stmt = $this->conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
        $stmt->bind_param("si", $pic_path, $user_id);
        return $stmt->execute();
    }
}
?>