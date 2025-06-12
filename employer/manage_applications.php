<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/employer_header.php';
require_once '../includes/gemini_client.php'; 
require_once '../includes/config.php';


// Kiểm tra đăng nhập & vai trò
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

$employer_id = $_SESSION['user_id'];

// Xử lý lọc trạng thái và tìm kiếm
$status_filter = $_GET['status'] ?? 'all';
$search = trim($_GET['q'] ?? '');

// Lấy danh sách JD của employer này
$stmt = $pdo->prepare("SELECT id, job_title FROM jobs WHERE user_id = ?");
$stmt->execute([$employer_id]);
$jd_list = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$job_ids = array_keys($jd_list);

// Nếu employer chưa có JD nào, show thông báo
if (!$job_ids) $apps = [];
else {
    // Xây dựng truy vấn
    $sql = "SELECT a.id AS application_id, a.applied_at, a.status, a.job_id, 
                   u.fullname, u.email, u.id AS candidate_id, c.file_path, c.cv_name
            FROM applications a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN cvs c ON c.user_id = u.id
            WHERE a.job_id IN (" . implode(',', array_fill(0, count($job_ids), '?')) . ")";

    $params = $job_ids;

    if ($status_filter !== 'all') {
        $sql .= " AND a.status = ?";
        $params[] = $status_filter;
    }
    if ($search) {
        $sql .= " AND (u.fullname LIKE ? OR u.email LIKE ? OR EXISTS (SELECT 1 FROM jobs j WHERE j.id = a.job_id AND j.job_title LIKE ?))";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    $sql .= " ORDER BY a.applied_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $apps = $stmt->fetchAll();
}

// Xử lý cập nhật trạng thái (accept/reject) - xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['application_id'])) {
    $application_id = intval($_POST['application_id']);
    $new_status = ($_POST['action'] === 'accept') ? 'accepted' : 'rejected';
    $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ? LIMIT 1");
    $stmt->execute([$new_status, $application_id]);
    header("Location: manage_applications.php?status=$status_filter&q=" . urlencode($search));
    exit();
}

// Xử lý đánh giá AI (nếu cần)
$ai_feedback = '';
if (isset($_GET['ai_eval']) && is_numeric($_GET['ai_eval'])) {
    $application_id = intval($_GET['ai_eval']);
    $stmt = $pdo->prepare("SELECT c.content FROM applications a LEFT JOIN cvs c ON c.user_id = a.user_id WHERE a.id = ?");
    $stmt->execute([$application_id]);
    $cv = $stmt->fetch();
    if ($cv && !empty($cv['content'])) {
        $prompt = "Đây là CV:\n{$cv['content']}\n\nHãy đánh giá (1) điểm mạnh, (2) điểm yếu, (3) điểm số CV (0-100), (4) gợi ý cải thiện, (5) gợi ý vị trí phù hợp, trình bày từng mục rõ ràng.";
        $gemini = new GeminiClient(GEMINI_API_KEY);
        $result = $gemini->generateContent($prompt);
        $ai_feedback = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Không nhận được phản hồi từ Gemini.';
    } else {
        $ai_feedback = 'Không tìm thấy CV ứng viên hoặc CV chưa có nội dung.';
    }
}

function getStatusColor($status) {
    switch ($status) {
        case 'accepted': return 'success'; 
        case 'rejected': return 'danger';  
        default: return 'warning';         
    }
}

function getStatusText($status) {
    switch ($status) {
        case 'accepted': return 'Chấp nhận';
        case 'rejected': return 'Từ chối';
        default: return 'Chưa duyệt';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý ứng viên ứng tuyển</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; background: linear-gradient(135deg, #f7fafc 0%, #e2ecf8 100%); min-height: 100vh;}
        .navbar { box-shadow: 0 2px 8px #e3e3e3;}
        .status-dot { width:16px; height:16px; border-radius:50%; display:inline-block; margin-right:6px; vertical-align:middle; }
        .status-success { background: #21c274; }
        .status-danger  { background: #f6504d; }
        .status-warning { background: #ffc107; }
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
<div class="main-content" style="max-width:1100px; margin:38px auto 0 auto; background:#fff; border-radius:18px; box-shadow:0 4px 28px 0 rgba(0,96,237,0.08); padding:32px 36px 28px 36px;">
    <h3 class="mb-4"><i class="bi bi-people"></i> Quản lý ứng viên ứng tuyển</h3>

    <!-- Thanh tìm kiếm & bộ lọc -->
    <form class="row g-2 align-items-center mb-4" method="get">
        <div class="col-auto">
            <select name="status" class="form-select">
                <option value="all" <?= $status_filter=='all'?'selected':'' ?>>Tất cả trạng thái</option>
                <option value="accepted" <?= $status_filter=='accepted'?'selected':'' ?>>Chấp nhận</option>
                <option value="rejected" <?= $status_filter=='rejected'?'selected':'' ?>>Từ chối</option>
                <option value="pending" <?= $status_filter=='pending'?'selected':'' ?>>Chưa duyệt</option>
            </select>
        </div>
        <div class="col">
            <input type="text" name="q" class="form-control" placeholder="Tìm kiếm tên/email/JD..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Tìm kiếm</button>
        </div>
    </form>

    <!-- Hiển thị kết quả AI nếu có -->
    <?php if($ai_feedback): ?>
        <div class="alert alert-info"><b>Kết quả AI:</b><br><pre><?= htmlspecialchars($ai_feedback) ?></pre></div>
    <?php endif; ?>

    <?php if (empty($apps)): ?>
        <div class="alert alert-info">Chưa có ứng viên nào ứng tuyển vào các JD của bạn.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                <tr>
                    <th class="text-center">Trạng thái</th>
                    <th>Tên ứng viên</th>
                    <th>Email</th>
                    <th>Tên JD</th>
                    <th>Ngày ứng tuyển</th>
                    <th class="text-center">Hành động</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($apps as $app): ?>
                    <tr>
                        <td class="text-center">
                            <span class="status-dot status-<?= getStatusColor($app['status']) ?>"></span>
                            <?= getStatusText($app['status']) ?>
                        </td>
                        <td><?= htmlspecialchars($app['fullname']) ?></td>
                        <td><?= htmlspecialchars($app['email']) ?></td>
                        <td><?= htmlspecialchars($jd_list[$app['job_id']] ?? '') ?></td>
                        <td><?= htmlspecialchars($app['applied_at']) ?></td>
                        <td class="text-center">
                            <!-- Xem chi tiết CV -->
                            <?php if (!empty($app['file_path'])): ?>
                                <a href="<?= htmlspecialchars($app['file_path']) ?>" target="_blank" class="btn btn-outline-primary btn-sm" title="Xem CV"><i class="bi bi-eye"></i></a>
                            <?php else: ?>
                                <span class="text-muted">Không có CV</span>
                            <?php endif; ?>

                            <!-- Đánh giá AI -->
                            <a href="?ai_eval=<?= $app['application_id'] ?>&status=<?= htmlspecialchars($status_filter) ?>&q=<?= urlencode($search) ?>" class="btn btn-outline-info btn-sm" title="Đánh giá AI CV"><i class="bi bi-robot"></i></a>

                            <!-- Chấp nhận -->
                            <?php if ($app['status'] !== 'accepted'): ?>
                                <form method="post" style="display:inline;" onsubmit="return confirm('Bạn chắc chắn muốn CHẤP NHẬN ứng viên này?');">
                            <input type="hidden" name="application_id" value="<?= $app['application_id'] ?>">
                            <input type="hidden" name="action" value="accept">
                            <button type="submit" class="btn btn-outline-success btn-sm" title="Chấp nhận"><i class="bi bi-check2-circle"></i></button>
                            </form>
                            <?php endif; ?>

                            <!-- Từ chối -->
                            <?php if ($app['status'] !== 'rejected'): ?>
                                <form method="post" style="display:inline;" onsubmit="return confirm('Bạn chắc chắn muốn TỪ CHỐI ứng viên này?');">
                                <input type="hidden" name="application_id" value="<?= $app['application_id'] ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Từ chối"><i class="bi bi-x-circle"></i></button>
                                </form>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
