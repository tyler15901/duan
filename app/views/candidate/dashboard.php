<?php
require_once "../includes/db.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";
redirect_if_not_logged_in();
if (!is_candidate()) { header("Location: /recruiter/dashboard.php"); exit; }
?>
<h2>Chào mừng <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
<ul>
    <li><a href="upload_cv.php">Đánh giá CV cá nhân</a></li>
    <li><a href="view_feedback.php">Lịch sử đánh giá CV</a></li>
    <li><a href="generate_cv.php">Tạo CV tự động bằng AI</a></li>
    <li><a href="career_path.php">Tư vấn lộ trình nghề nghiệp</a></li>
</ul>
<?php require_once "../includes/footer.php"; ?>