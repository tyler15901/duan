<?php
require_once "../includes/db.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";
redirect_if_not_logged_in();
if (!is_recruiter()) { header("Location: /candidate/dashboard.php"); exit; }
$total_cv = $pdo->query("SELECT COUNT(*) FROM cvs")->fetchColumn();
$total_candidate = $pdo->query("SELECT COUNT(*) FROM users WHERE role='candidate'")->fetchColumn();
$total_feedback = $pdo->query("SELECT COUNT(*) FROM recruiter_evaluations")->fetchColumn();
?>
<h3>Thống kê tổng quan</h3>
<ul>
    <li>Tổng số CV được tải lên: <b><?= $total_cv ?></b></li>
    <li>Tổng số ứng viên: <b><?= $total_candidate ?></b></li>
    <li>Tổng số đánh giá của nhà tuyển dụng: <b><?= $total_feedback ?></b></li>
</ul>
<?php require_once "../includes/footer.php"; ?>