<?php
session_start();
$fullname = $_SESSION['fullname'] ?? 'Nhà tuyển dụng';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhà tuyển dụng - Phân tích CV AI</title>
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
        <a class="navbar-brand d-flex align-items-center" href="../employer_dashboard.php">
            <img src="../logo.svg" alt="Logo" width="42" height="42" class="me-2">
            <span style="font-weight:700; font-size:1.5rem; color:#0056b3;">Phân tích CV AI</span>
        </a>
        <div class="ms-auto d-flex align-items-center">
            <span class="me-3 fw-semibold text-dark">Xin chào, <?php echo htmlspecialchars($fullname); ?></span>
            <a href="../auth/logout.php" class="btn btn-outline-danger">Đăng xuất</a>
        </div>
    </nav>
    <div class="container main-content">
        <div class="row justify-content-center g-4">
            <div class="col-md-3 col-6">
                <div class="card card-function text-center p-4 h-100">
                    <div class="function-icon"><i class="bi bi-archive"></i></div>
                    <h5 class="mb-2">Quản lý JD</h5>
                    <p class="text-muted mb-3">Xem và chỉnh sửa các tin tuyển dụng đã đăng.</p>
                    <a href="manage_jd.php" class="btn btn-outline-success w-100 mt-auto">Quản lý JD</a>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card card-function text-center p-4 h-100">
                    <div class="function-icon"><i class="bi bi-plus-square"></i></div>
                    <h5 class="mb-2">Đăng tin ngay</h5>
                    <p class="text-muted mb-3">Đăng tin tuyển dụng mới, thu hút ứng viên phù hợp.</p>
                    <a href="add_jd.php" class="btn btn-outline-warning w-100 mt-auto" style="color:#f9a826; border-color:#f9a826;">Đăng tin ngay</a>
                </div>
            </div>
            
            <div class="col-md-3 col-6">
                <div class="card card-function text-center p-4 h-100">
                    <div class="function-icon"><i class="bi bi-people"></i></div>
                    <h5 class="mb-2">Quản lý ứng viên</h5>
                    <p class="text-muted mb-3">Quản lý toàn bộ ứng viên đã đăng ký/nộp CV.</p>
                    <a href="manage_applications.php" class="btn btn-outline-primary w-100 mt-auto">Quản lý ứng viên</a>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
