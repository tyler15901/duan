<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'candidate') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
$msg = '';

if ($job_id > 0) {
    // Kiểm tra đã ứng tuyển chưa
    $stmt = $pdo->prepare("SELECT id FROM applications WHERE user_id = ? AND job_id = ?");
    $stmt->execute([$user_id, $job_id]);
    if ($stmt->fetch()) {
        $msg = "Bạn đã ứng tuyển JD này rồi!";
    } else {
        // Kiểm tra tồn tại JD
        $jd_stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
        $jd_stmt->execute([$job_id]);
        if ($jd_stmt->fetch()) {
            // Thêm ứng tuyển
            $insert = $pdo->prepare("INSERT INTO applications (user_id, job_id) VALUES (?, ?)");
            $insert->execute([$user_id, $job_id]);
            $msg = "Ứng tuyển thành công!";
        } else {
            $msg = "Không tìm thấy JD.";
        }
    }
} else {
    $msg = "Dữ liệu không hợp lệ.";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Ứng tuyển JD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
    height: 100%;
}

body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.main-content {
    flex: 1 0 auto;
}

footer {
    flex-shrink: 0;
}

    </style>
</head>
<body>
    <div class="container" style="max-width:500px;margin:80px auto;">
        <div class="card p-4">
            <h4 class="mb-3">Ứng tuyển việc làm</h4>
            <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
            <a href="job_search.php" class="btn btn-primary">Quay lại tìm việc</a>
        </div>
    </div>
</body>
</html>
