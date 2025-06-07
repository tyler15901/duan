<?php
require_once "../includes/db.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";
redirect_if_not_logged_in();
if (!is_recruiter()) { header("Location: /candidate/dashboard.php"); exit; }
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT cvs.*, users.name AS user_name, users.email FROM cvs JOIN users ON cvs.user_id = users.id WHERE cvs.id = ?");
$stmt->execute([$id]);
$cv = $stmt->fetch();
if (!$cv) { echo "<div class='alert alert-danger'>Không tìm thấy CV!</div>"; require_once "../includes/footer.php"; exit; }

// Đánh giá recruiter
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    $stmt = $pdo->prepare("INSERT INTO recruiter_evaluations (recruiter_id, cv_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $id, $rating, $comment]);
    echo "<div class='alert alert-success'>Đã lưu đánh giá!</div>";
}
$stmt = $pdo->prepare("SELECT * FROM recruiter_evaluations WHERE cv_id = ? ORDER BY created_at DESC");
$stmt->execute([$id]);
$evaluations = $stmt->fetchAll();
?>
<h3>Chi tiết CV của <?= htmlspecialchars($cv['user_name']) ?></h3>
<a href="<?= $cv['file_path'] ?>" class="btn btn-primary btn-sm" target="_blank">Tải CV gốc</a>
<pre class="mt-3"><?= htmlspecialchars($cv['ai_feedback']) ?></pre>

<h4 class="mt-4">Đánh giá của nhà tuyển dụng</h4>
<form method="post" class="mb-3">
    <div class="mb-2">
        <label>Điểm (1-10):</label>
        <input type="number" name="rating" min="1" max="10" required>
    </div>
    <div class="mb-2">
        <textarea name="comment" class="form-control" placeholder="Nhận xét chi tiết" required></textarea>
    </div>
    <button type="submit" class="btn btn-success btn-sm">Lưu đánh giá</button>
</form>
<?php foreach($evaluations as $ev): ?>
    <div class="border p-2 mb-2">
        <b>Điểm:</b> <?= $ev['rating'] ?> <br>
        <b>Nhận xét:</b> <?= htmlspecialchars($ev['comment']) ?> <br>
        <small><?= $ev['created_at'] ?></small>
    </div>
<?php endforeach; ?>
<?php require_once "../includes/footer.php"; ?>