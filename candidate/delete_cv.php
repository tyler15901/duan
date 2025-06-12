<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cv_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($cv_id > 0) {
    // Lấy file_path
    $stmt = $pdo->prepare("SELECT file_path FROM cvs WHERE id = ? AND user_id = ?");
    $stmt->execute([$cv_id, $user_id]);
    $cv = $stmt->fetch();
    if ($cv) {
        if (file_exists($cv['file_path'])) unlink($cv['file_path']);
        // Xóa khỏi DB
        $del = $pdo->prepare("DELETE FROM cvs WHERE id = ? AND user_id = ?");
        $del->execute([$cv_id, $user_id]);
    }
}
header("Location: candidate_dashboard.php");
exit();
?>
