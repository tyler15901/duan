<?php
if (session_status() == PHP_SESSION_NONE) session_start();
$fullname = $_SESSION['fullname'] ?? 'Nhà tuyển dụng';
?>
<nav class="navbar navbar-expand-lg bg-white py-3 px-4 sticky-top">
    <a class="navbar-brand d-flex align-items-center" href="../employer/employer_dashboard.php">
        <img src="../logo.svg" alt="Logo" width="42" height="42" class="me-2">
        <span style="font-weight:700; font-size:1.5rem; color:#0056b3;">Phân tích CV AI</span>
    </a>
    <div class="ms-auto d-flex align-items-center">
        <span class="me-3 fw-semibold text-dark">Xin chào, <?php echo htmlspecialchars($fullname); ?></span>
        <a href="../auth/logout.php" class="btn btn-outline-danger">Đăng xuất</a>
    </div>
</nav>
