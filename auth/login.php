<?php
require_once '../includes/db.php';
session_start();

$register_success = false;
if (!empty($_SESSION['register_success'])) {
    $register_success = true;
    unset($_SESSION['register_success']);
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT id, fullname, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];
        if ($user['role'] == 'candidate') {
            header('Location: ../candidate/candidate_dashboard.php');
        } else {
            header('Location: ../employer/employer_dashboard.php');
        }
        exit();
    } else {
        $error = 'Email hoặc mật khẩu không đúng!';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - Phân tích CV AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <style>
        
        body { font-family: 'Montserrat', Arial, sans-serif; background: linear-gradient(135deg, #f7fafc 0%, #e2ecf8 100%); min-height: 100vh;}
        .login-container { max-width: 400px; margin: 64px auto; padding: 36px 32px 32px 32px; background: #fff; border-radius: 18px; box-shadow: 0 4px 32px 0 rgba(0, 96, 237, 0.09);}
        .form-label { font-weight: 500;}
        .login-title { font-size: 1.7rem; font-weight: 700; color: #3276ef; margin-bottom: 20px; text-align: center;}
        .logo { display: block; margin: 0 auto 10px auto;}
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
    <nav class="navbar navbar-light bg-white shadow-sm px-4">
        <a class="navbar-brand d-flex align-items-center" href="../index.php">
            <img src="../logo.svg" alt="Logo" width="38" height="38" class="me-2">
            <span style="font-weight:700; color:#0056b3;">Phân tích CV AI</span>
        </a>
    </nav>
    <div class="main-content">
    <div class="login-container">
        <img src="../logo.svg" alt="Logo" width="46" height="46" class="logo">
        <div class="login-title">Đăng nhập</div>
        <?php if ($register_success): ?>
            <div class="alert alert-success text-center">
                Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required placeholder="Nhập email">
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" name="password" required placeholder="Nhập mật khẩu">
            </div>
            <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
        </form>
        <div class="text-center mt-3">
            <span>Bạn chưa có tài khoản?</span>
            <a href="register.php" class="fw-bold text-decoration-none" style="color:#3276ef;">Đăng ký ngay</a>
        </div>
    </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
