<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phân tích CV AI</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; background: linear-gradient(135deg, #f7fafc 0%, #e2ecf8 100%); min-height: 100vh;}
        .main-content { min-height: 75vh; display: flex; flex-direction: column; justify-content: center; padding-top: 60px;}
        .card-function { border: none; border-radius: 16px; box-shadow: 0 6px 20px 0 rgba(34, 51, 84, 0.08); transition: transform 0.2s, box-shadow 0.2s; background: #fff;}
        .card-function:hover { transform: translateY(-6px) scale(1.03); box-shadow: 0 10px 32px 0 rgba(34, 51, 84, 0.13);}
        .function-icon { font-size: 2.6rem; color: #3276ef; margin-bottom: 12px;}
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light px-4">
        <a class="navbar-brand fw-bold" href="#">
            <img src="logo.svg" width="40" height="40" class="d-inline-block align-middle" alt="Logo">
            <span style="font-weight:700; font-size:1.5rem; color:#0056b3;">Phân tích CV AI</span>
        </a>
        <div class="ms-auto">
            <a href="auth/login.php" class="btn btn-outline-primary me-2">Đăng nhập</a>
            <a href="auth/register.php" class="btn btn-primary">Đăng ký</a>
        </div>
    </nav>
    <div class="container main-content py-5">
        <div class="row justify-content-center g-4">
            <div class="col-md-3 col-6">
                <div class="card card-function text-center p-4 h-100">
                    <div class="function-icon"><i class="bi bi-search"></i></div>
                    <h5 class="mb-2">Tìm kiếm việc làm</h5>
                    <p class="text-muted mb-3">Khám phá hàng ngàn cơ hội việc làm phù hợp với bạn.</p>
                    <a href="candidate/job_search.php" class="btn btn-outline-success w-100 mt-auto">Tìm việc ngay</a>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card card-function text-center p-4 h-100">
                    <div class="function-icon"><i class="bi bi-file-earmark-check"></i></div>
                    <h5 class="mb-2">Đánh giá CV cá nhân</h5>
                    <p class="text-muted mb-3">Chấm điểm, nhận nhận xét, gợi ý cải thiện CV.</p>
                    <a href="candidate/upload_cv.php" class="btn btn-outline-warning w-100 mt-auto" style="color:#f9a826; border-color:#f9a826; ">Đánh giá CV</a>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card card-function text-center p-4 h-100">
                    <div class="function-icon"><i class="bi bi-robot"></i></div>
                    <h5 class="mb-2">Tạo CV tự động bằng AI</h5>
                    <p class="text-muted mb-3">Để AI tạo CV chuyên nghiệp từ thông tin của bạn.</p>
                    <a href="candidate/generate_cv.php" class="btn btn-outline-info w-100 mt-auto">Tạo CV bằng AI</a>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card card-function text-center p-4 h-100">
                    <div class="function-icon"><i class="bi bi-compass"></i></div>
                    <h5 class="mb-2">Tư vấn lộ trình nghề nghiệp</h5>
                    <p class="text-muted mb-3">Định hướng phát triển bản thân & nghề nghiệp phù hợp.</p>
                    <a href="candidate/career_path.php" class="btn btn-outline-primary w-100 mt-auto">Tư vấn ngay</a>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 text-center">
                <span class="fw-bold">Bạn là nhà tuyển dụng?</span>
                <a href="auth/login.php?role=employer" class="btn btn-outline-danger ms-3">Đăng nhập nhà tuyển dụng</a>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
