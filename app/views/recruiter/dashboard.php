<?php
require_once "../includes/db.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";
redirect_if_not_logged_in();
if (!is_recruiter()) { header("Location: /candidate/dashboard.php"); exit; }
?>
<h2>Chào mừng <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
<ul>
    <li><a href="search_candidates.php">Tìm kiếm & Quản lý ứng viên</a></li>
    <li><a href="statistics.php">Thống kê tổng quan</a></li>
</ul>
<?php require_once "../includes/footer.php"; ?>