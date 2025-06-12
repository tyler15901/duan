<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/config.php';
require_once '../includes/employer_header.php';

// Chỉ cho nhà tuyển dụng đăng nhập mới truy cập
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['jd_file'])) {
    $file = $_FILES['jd_file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($file['error'] == 0 && $ext === 'pdf') {
        // Đảm bảo thư mục uploads/JD tồn tại
        $uploadDir = "../uploads/JD/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $target = $uploadDir . uniqid() . "_" . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $target)) {
            $job_title = isset($_POST['job_title']) ? trim($_POST['job_title']) : $file['name'];
            // Lưu thông tin vào DB
            $stmt = $pdo->prepare("INSERT INTO jobs (user_id, job_title, file_path, posted_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$_SESSION['user_id'], $job_title, $target]);
            $success = "Tải lên JD thành công!";
        } else {
            $error = "Lỗi khi lưu file!";
        }
    } else {
        $error = "Vui lòng chọn file PDF hợp lệ!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tải lên JD - Nhà tuyển dụng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; background: linear-gradient(135deg, #f7fafc 0%, #e2ecf8 100%); min-height: 100vh;}
        .main-content { max-width: 500px; margin: 38px auto 0 auto; background: #fff; border-radius: 18px; box-shadow: 0 4px 28px 0 rgba(0, 96, 237, 0.08); padding: 32px 36px 28px 36px;}
        h3 { color: #3276ef; font-weight: 700;}
        html, body {
    height: 100%;
    min-height: 100%;
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
    <div class="main-content">
        <h3 class="mb-3"><i class="bi bi-plus-square"></i> Tải lên JD (PDF)</h3>
        <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Tiêu đề tin tuyển dụng</label>
                <input type="text" name="job_title" class="form-control" placeholder="Nhập tiêu đề hoặc để mặc định tên file">
            </div>
            <div class="mb-3">
                <label class="form-label">Chọn file JD (PDF)</label>
                <input type="file" name="jd_file" accept=".pdf" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Tải lên</button>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
