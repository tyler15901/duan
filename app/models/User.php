<?php
require_once __DIR__ . '/../../includes/db.php';

class User extends Model {
    public function findByEmail($email) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    public function create($email, $password, $name, $role) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO users (email, password, name, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$email, $password, $name, $role]);
    }
}