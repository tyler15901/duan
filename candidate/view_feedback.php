<?php
session_start();
require_once '../includes/db.php';

$user_id = $_SESSION['user_id'] ?? 0;
$cv_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("SELECT ai_feedback FROM cvs WHERE id = ? AND user_id = ?");
$stmt->execute([$cv_id, $user_id]);
$row = $stmt->fetch();

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đánh giá AI CV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding:24px; background:#f7fafc; font-family:'Montserrat', Arial,sans-serif;}
        .feedback-box { background:#fff; border-radius:12px; box-shadow:0 4px 18px #e1ecfb; padding:28px; }
    </style>
</head>
<body>
    <div class="container" style="max-width:640px;">
        <div class="feedback-box">
            <h5 class="mb-4 text-success"><i class="bi bi-robot"></i> Đánh giá AI CV</h5>
            <?php if ($row && !empty($row['ai_feedback'])): ?>
                <pre style="white-space: pre-line;"><?= htmlspecialchars($row['ai_feedback']) ?></pre>
            <?php else: ?>
                <div class="alert alert-warning">Chưa có đánh giá AI cho CV này.</div>
            <?php endif; ?>
            <a href="javascript:window.close()" class="btn btn-outline-secondary mt-3">Đóng</a>
        </div>
    </div>
</body>
</html>
