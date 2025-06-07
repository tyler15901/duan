<?php
require_once "../includes/db.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";
redirect_if_not_logged_in();
if (!is_recruiter()) { header("Location: /candidate/dashboard.php"); exit; }
$keyword = $_GET['keyword'] ?? '';
$sql = "SELECT cvs.*, users.name AS user_name, users.email FROM cvs JOIN users ON cvs.user_id = users.id";
$params = [];
if ($keyword) {
    $sql .= " WHERE users.name LIKE ? OR cvs.ai_feedback LIKE ?";
    $params = ["%$keyword%", "%$keyword%"];
}
$sql .= " ORDER BY cvs.created_at DESC LIMIT 50";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cvs = $stmt->fetchAll();
?>
<h3>Quản lý & Tìm kiếm ứng viên</h3>
<form method="get" class="mb-3">
    <input type="text" name="keyword" placeholder="Tìm theo tên, kỹ năng..." class="form-control" value="<?= htmlspecialchars($keyword) ?>">
</form>
<?php if(!$cvs): ?>
    <div class="alert alert-warning">Không tìm thấy ứng viên nào.</div>
<?php else: ?>
    <table class="table table-bordered">
        <tr>
            <th>Tên ứng viên</th>
            <th>Email</th>
            <th>Ngày tải CV</th>
            <th>Hành động</th>
        </tr>
        <?php foreach($cvs as $cv): ?>
            <tr>
                <td><?= htmlspecialchars($cv['user_name']) ?></td>
                <td><?= htmlspecialchars($cv['email']) ?></td>
                <td><?= htmlspecialchars($cv['created_at']) ?></td>
                <td>
                    <a href="view_cv.php?id=<?= $cv['id'] ?>" class="btn btn-sm btn-info">Xem chi tiết</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<?php require_once "../includes/footer.php"; ?>