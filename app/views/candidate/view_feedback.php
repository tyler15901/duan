<?php
require_once "../includes/db.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";
redirect_if_not_logged_in();
if (!is_candidate()) { header("Location: /recruiter/dashboard.php"); exit; }
$stmt = $pdo->prepare("SELECT * FROM cvs WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$cvs = $stmt->fetchAll();
?>
<h3>Lịch sử đánh giá CV</h3>
<?php if(!$cvs): ?>
    <div class="alert alert-warning">Bạn chưa có CV nào được đánh giá.</div>
<?php else: ?>
    <?php foreach($cvs as $cv): ?>
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <?= htmlspecialchars($cv['created_at']) ?>
            </div>
            <div class="card-body">
                <pre><?= htmlspecialchars($cv['ai_feedback']) ?></pre>
                <a href="<?= $cv['file_path'] ?>" target="_blank" class="btn btn-sm btn-secondary">Tải CV gốc</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<?php require_once "../includes/footer.php"; ?>