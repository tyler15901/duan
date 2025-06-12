<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/config.php';
require_once '../includes/employer_header.php';

// Chỉ cho nhà tuyển dụng đăng nhập mới vào
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

// Xử lý xóa JD (nếu có)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $jd_id = intval($_GET['delete']);
    // Xóa file trên ổ cứng
    $stmt = $pdo->prepare("SELECT file_path FROM jobs WHERE id = ? AND user_id = ?");
    $stmt->execute([$jd_id, $_SESSION['user_id']]);
    $jd = $stmt->fetch();
    if ($jd && file_exists($jd['file_path'])) {
        unlink($jd['file_path']);
    }
    // Xóa JD trong database
    $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ? AND user_id = ?");
    $stmt->execute([$jd_id, $_SESSION['user_id']]);
    header('Location: manage_jd.php?msg=deleted');
    exit();
}

// Lấy danh sách JD của nhà tuyển dụng hiện tại
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE user_id = ? ORDER BY posted_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$jobs = $stmt->fetchAll();

$msg = '';
if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') {
    $msg = 'Đã xóa JD thành công!';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý JD - Nhà tuyển dụng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; background: linear-gradient(135deg, #f7fafc 0%, #e2ecf8 100%);}
        .main-content { max-width: 900px; margin: 38px auto 0 auto; background: #fff; border-radius: 18px; box-shadow: 0 4px 28px 0 rgba(0, 96, 237, 0.08); padding: 32px 36px 28px 36px;}
        h3 { color: #3276ef; font-weight: 700;}
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
        <h3 class="mb-3"><i class="bi bi-archive"></i> Quản lý JD đã đăng</h3>
        <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

        <?php if (empty($jobs)): ?>
            <div class="alert alert-info">Bạn chưa đăng tin JD nào.</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Tiêu đề</th>
                        <th>File JD (PDF)</th>
                        <th>Ngày đăng</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($jobs as $job): ?>
                    <tr>
                        <td><?= htmlspecialchars($job['job_title']) ?></td>
                        <td>
                            <?php if(!empty($job['file_path'])): ?>
                                <a href="<?= htmlspecialchars($job['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                    Xem / Tải PDF
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Chưa có file</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($job['posted_at']) ?></td>
                        <td class="text-center">
                            <a href="manage_jd.php?delete=<?= $job['id'] ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Bạn chắc chắn muốn xóa JD này?');">
                                Xóa
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <div class="text-end">
            <a href="add_jd.php" class="btn btn-success mt-3"><i class="bi bi-plus"></i> Đăng tin JD mới</a>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
