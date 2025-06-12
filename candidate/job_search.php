<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/config.php';
require_once '../includes/candidate_header.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'candidate') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Xử lý tìm kiếm
$keyword = trim($_GET['q'] ?? '');
$params = [];
$where = '';

if ($keyword) {
    $where = "WHERE job_title LIKE ?";
    $params = ["%$keyword%"];
}
$stmt = $pdo->prepare("SELECT * FROM jobs $where ORDER BY posted_at DESC");
$stmt->execute($params);
$jobs = $stmt->fetchAll();

// Lấy các JD đã ứng tuyển để ẩn nút nếu cần
$app_stmt = $pdo->prepare("SELECT job_id FROM applications WHERE user_id = ?");
$app_stmt->execute([$user_id]);
$applied_jobs = array_column($app_stmt->fetchAll(), 'job_id');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tìm kiếm việc làm - Ứng viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; background: linear-gradient(135deg, #f7fafc 0%, #e2ecf8 100%);}
        .main-content { max-width: 950px; margin: 38px auto 0 auto; background: #fff; border-radius: 18px; box-shadow: 0 4px 28px 0 rgba(0, 96, 237, 0.08); padding: 32px 36px 28px 36px;}
        h3 { color: #3276ef; font-weight: 700;}
        .action-btn { min-width: 100px; }
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
</head>
<body>
    <div class="main-content">
        <h3 class="mb-3"><i class="bi bi-search"></i> Tìm kiếm việc làm</h3>
        <form class="d-flex mb-4" method="get" style="max-width: 739px;">
            <div class="col-md-10 col-9">
                <input type="text" class="form-control" name="q" placeholder="Nhập từ khóa: tiêu đề, địa điểm..." value="<?= htmlspecialchars($keyword) ?>">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary ms-2" type="submit" ><i class="bi bi-search"></i> Tìm kiếm</button>
            </div>
        </form>

        <?php if (empty($jobs)): ?>
            <div class="alert alert-info">Không tìm thấy công việc nào phù hợp.</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
    <tr>
        <th>Tiêu đề</th>
        <th>Ngày đăng</th>
        <th class="text-center">Hành động</th>
    </tr>
</thead>
<tbody>
    <?php foreach($jobs as $job): ?>
    <tr>
        <td><?= htmlspecialchars($job['job_title']) ?></td>
        <td><?= htmlspecialchars($job['posted_at']) ?></td>
        <td class="text-center">
            <?php if (!in_array($job['id'], $applied_jobs)): ?>
                <a href="apply_job.php?job_id=<?= $job['id'] ?>" class="btn btn-success btn-sm action-btn">Ứng tuyển</a>
            <?php else: ?>
                <span class="text-success fw-bold">Đã ứng tuyển</span>
            <?php endif; ?>
            <?php if(!empty($job['file_path'])): ?>
                <a href="<?= htmlspecialchars($job['file_path']) ?>" target="_blank" class="btn btn-info btn-sm action-btn">Xem chi tiết</a>
            <?php else: ?>
                <span class="text-muted">Chưa có file</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>

            </table>
        </div>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
