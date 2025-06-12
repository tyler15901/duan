<?php
session_start();
require_once '../includes/db.php';
$user_id = $_SESSION['user_id'] ?? 0;
$fullname = $_SESSION['fullname'] ?? 'Ứng viên';

// Truy vấn CV của ứng viên
$stmt = $pdo->prepare("SELECT id, cv_name AS name, file_path, uploaded_at AS date FROM cvs WHERE user_id = ? ORDER BY uploaded_at DESC");
$stmt->execute([$user_id]);
$list_cv = $stmt->fetchAll();


// Truy vấn thông báo kết quả ứng tuyển
$stmt = $pdo->prepare("SELECT a.id, j.job_title, a.status, a.applied_at
                       FROM applications a
                       JOIN jobs j ON a.job_id = j.id
                       WHERE a.user_id = ? AND a.status IN ('accepted','rejected') AND a.notified = 0
                       ORDER BY a.applied_at DESC");
$stmt->execute([$user_id]);
$results = $stmt->fetchAll();


if ($results) {
    foreach ($results as $row) {
        if ($row['status'] === 'accepted') {
            echo '<div class="alert alert-success mb-2">Bạn đã được <b>CHẤP NHẬN</b> vào vị trí: <b>' . htmlspecialchars($row['job_title']) . '</b>!</div>';
        } elseif ($row['status'] === 'rejected') {
            echo '<div class="alert alert-danger mb-2">Bạn đã bị <b>TỪ CHỐI</b> ở vị trí: <b>' . htmlspecialchars($row['job_title']) . '</b>.</div>';
        }
        // Đánh dấu đã thông báo
        $update = $pdo->prepare("UPDATE applications SET notified = 1 WHERE id = ?");
        $update->execute([$row['id']]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_cv']) && isset($_FILES['cv_file'])) {
    $file = $_FILES['cv_file'];
    if ($file['error'] == 0 && strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) === 'pdf') {
        $targetDir = "../uploads/CV/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = uniqid() . "_" . basename($file['name']);
        $target = $targetDir . $fileName;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            $cv_name = $file['name'];
            $file_path = $target;
            $stmt = $pdo->prepare("INSERT INTO cvs (user_id, cv_name, file_path) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $cv_name, $file_path]);
            echo '<div class="alert alert-success mb-2">Tải lên CV thành công!</div>';
            echo '<script>location.href=location.href;</script>'; 
            exit;
        } else {
            echo '<div class="alert alert-danger mb-2">Lỗi lưu file!</div>';
        }
    } else {
        echo '<div class="alert alert-danger mb-2">Chỉ nhận file PDF!</div>';
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Ứng viên - Phân tích CV AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; background: linear-gradient(135deg, #f7fafc 0%, #e2ecf8 100%); min-height: 100vh;}
        .navbar { box-shadow: 0 2px 8px #e3e3e3;}
        .main-content { min-height: 75vh; padding-top: 32px;}
        .card-function { border: none; border-radius: 16px; box-shadow: 0 6px 20px 0 rgba(34, 51, 84, 0.09); background: #fff; transition: transform 0.18s, box-shadow 0.18s;}
        .card-function:hover { transform: translateY(-6px) scale(1.03); box-shadow: 0 10px 32px 0 rgba(34, 51, 84, 0.13);}
        .function-icon { font-size: 2.6rem; color: #3276ef; margin-bottom: 12px;}
        .cv-manager-card { border-radius: 14px; box-shadow: 0 2px 18px #e1ecfb; background: #fff;}

        body {
    
    display: flex;
    flex-direction: column;
}

.main-content {
    flex: 1 0 auto;
    
}

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white py-3 px-4 sticky-top">
        <a class="navbar-brand d-flex align-items-center" href="../candidate/candidate_dashboard.php">
            <img src="../logo.svg" alt="Logo" width="42" height="42" class="me-2">  
            <span style="font-weight:700; font-size:1.5rem; color:#0056b3;">Phân tích CV AI</span>
        </a>
        <div class="ms-auto d-flex align-items-center">
            <span class="me-3 fw-semibold text-dark">Xin chào, <?php echo htmlspecialchars($fullname); ?></span>
            <a href="../auth/logout.php" class="btn btn-outline-danger">Đăng xuất</a>
        </div>
    </nav>
    <div class="container main-content">
        <!-- Quản lý CV -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card cv-manager-card p-4 mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Quản lý CV của bạn</h5>
                        <form method="post" enctype="multipart/form-data" style="display:inline;">
    <input type="file" id="cvUpload" name="cv_file" accept=".pdf" style="display:none;" onchange="this.form.submit()">
    <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('cvUpload').click()">
        <i class="bi bi-upload me-1"></i> Tải lên CV mới
    </button>
    <input type="hidden" name="upload_cv" value="1">
</form>

                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Tên CV</th>
                                    <th>Ngày tải lên</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($list_cv)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Bạn chưa có CV nào.</td>
                                    </tr>
                                <?php else: foreach ($list_cv as $cv): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($cv['name']); ?></td>
                                        <td><?php echo htmlspecialchars($cv['date']); ?></td>
                                        <td class="text-center">
                                            <a href="<?= htmlspecialchars($cv['file_path']) ?>" target="_blank" class="btn btn-outline-primary btn-sm" title="Xem CV">
                                            <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="view_feedback.php?id=<?= $cv['id'] ?>" target="_blank"
                                            class="btn btn-outline-success btn-sm" title="Xem đánh giá AI">
                                            <i class="bi bi-robot"></i>
                                            </a>
                                            <a href="delete_cv.php?id=<?= $cv['id'] ?>"class="btn btn-outline-danger btn-sm" title="Xóa CV"
                                            onclick="return confirm('Bạn chắc chắn muốn xóa CV này?');"> <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card chức năng như trang chủ -->
        <div class="row justify-content-center g-4">
            <div class="col-md-3 col-6">
                <div class="card card-function text-center p-4 h-100">
                    <div class="function-icon" style="color:#1dbf73;"><i class="bi bi-search"></i></div>
                    <h5 class="mb-2">Tìm kiếm việc làm</h5>
                    <p class="text-muted mb-3">Khám phá hàng ngàn cơ hội việc làm phù hợp với bạn.</p>
                    <a href="job_search.php" class="btn btn-outline-success w-100 mt-auto">Tìm việc ngay</a>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card card-function text-center p-4 h-100">
                    <div class="function-icon" style="color:#f9a826;"><i class="bi bi-file-earmark-check"></i></div>
                    <h5 class="mb-2">Đánh giá CV cá nhân</h5>
                    <p class="text-muted mb-3">Chấm điểm, nhận nhận xét, gợi ý cải thiện CV.</p>
                    <a href="upload_cv.php" class="btn btn-outline-warning w-100 mt-auto" style="color:#f9a826; border-color:#f9a826;">Đánh giá CV</a>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card card-function text-center p-4 h-100">
                    <div class="function-icon" style="color:#24a3d8;"><i class="bi bi-robot"></i></div>
                    <h5 class="mb-2">Tạo CV tự động bằng AI</h5>
                    <p class="text-muted mb-3">Để AI tạo CV chuyên nghiệp từ thông tin của bạn.</p>
                    <a href="generate_cv.php" class="btn btn-outline-info w-100 mt-auto">Tạo CV bằng AI</a>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card card-function text-center p-4 h-100">
                    <div class="function-icon" style="color:#2960ed;"><i class="bi bi-compass"></i></div>
                    <h5 class="mb-2">Tư vấn lộ trình nghề nghiệp</h5>
                    <p class="text-muted mb-3">Định hướng phát triển bản thân & nghề nghiệp phù hợp.</p>
                    <a href="career_path.php" class="btn btn-outline-primary w-100 mt-auto">Tư vấn ngay</a>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
