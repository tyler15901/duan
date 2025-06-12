<?php
require_once '../includes/db.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $error = 'Email đã được sử dụng. Vui lòng chọn email khác.';
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fullname, $email, $passwordHash, $role]);
        $_SESSION['register_success'] = true;
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - Phân tích CV AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; background: linear-gradient(135deg, #f7fafc 0%, #e2ecf8 100%); min-height: 100vh;}
        .register-container { max-width: 420px; margin: 64px auto; padding: 36px 32px 32px 32px; background: #fff; border-radius: 18px; box-shadow: 0 4px 32px 0 rgba(0, 96, 237, 0.09);}
        .form-label { font-weight: 500;}
        .register-title { font-size: 1.7rem; font-weight: 700; color: #3276ef; margin-bottom: 20px; text-align: center;}
        .logo { display: block; margin: 0 auto 16px auto;}
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
    <div class="register-container">
        <img src="../logo.svg" alt="Logo" width="46" height="46" class="logo">
        <div class="register-title">Đăng ký</div>
        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Họ và tên</label>
                <input type="text" class="form-control" name="fullname" required placeholder="Nhập họ và tên" value="<?php echo isset($fullname) ? htmlspecialchars($fullname) : '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required placeholder="Nhập email" value="<?php echo isset($email) ? htmlspecialchars($email) : '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" name="password" required placeholder="Tạo mật khẩu">
            </div>
            <div class="mb-3">
                <label class="form-label">Vai trò</label>
                <select name="role" class="form-select" required>
                    <option value="">-- Chọn vai trò --</option>
                    <option value="candidate" <?php if(isset($role) && $role=="candidate") echo "selected"; ?>>Ứng viên</option>
                    <option value="employer" <?php if(isset($role) && $role=="employer") echo "selected"; ?>>Nhà tuyển dụng</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
        </form>
        <div class="text-center mt-3">
            <span>Đã có tài khoản?</span>
            <a href="login.php" class="fw-bold text-decoration-none" style="color:#3276ef;">Đăng nhập</a>
        </div>
    </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
