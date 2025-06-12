<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/config.php';
require_once '../includes/gemini_client.php';

// Kiểm tra đăng nhập và đúng vai trò ứng viên
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'candidate') {
    header('Location: ../auth/login.php');
    exit();
}

$fullname = $_SESSION['fullname'] ?? 'Ứng viên';

$advice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $skills = trim($_POST['skills']);
    $exp = trim($_POST['experience']);
    $prompt = "Dựa trên kỹ năng: $skills và kinh nghiệm sau: $exp, hãy tư vấn lộ trình nghề nghiệp phù hợp, gợi ý kỹ năng cần học thêm để phát triển sự nghiệp.";
    $gemini = new GeminiClient(GEMINI_API_KEY);
    $result = $gemini->generateContent($prompt);
    $advice = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Không nhận được phản hồi từ Gemini.';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tư vấn lộ trình nghề nghiệp - Phân tích CV AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; background: linear-gradient(135deg, #f7fafc 0%, #e2ecf8 100%); min-height: 100vh;}
        .navbar { box-shadow: 0 2px 8px #e3e3e3;}
        .main-content { max-width: 1100px; margin: 38px auto 0 auto; background: #fff; border-radius: 18px; box-shadow: 0 4px 28px 0 rgba(0, 96, 237, 0.08); padding: 32px 36px 28px 36px;}
        h3 { color: #3276ef; font-weight: 700;}
        textarea.form-control { min-height: 70px;}
        .ai-output { background: #f7faff; border-radius: 12px; padding: 18px 22px; font-size: 1.08rem; white-space: pre-wrap;}
        footer {
    flex-shrink: 0;
}
body {
    
    display: flex;
    flex-direction: column;
}

.main-content {
    flex: 1 0 auto;
    
}
    </style>
</head>
<body>
    <?php include '../includes/candidate_header.php'; ?>

    <div class="main-content">
        <h3 class="mb-3"><i class="bi bi-compass"></i> Tư vấn lộ trình nghề nghiệp</h3>
        <form method="post" class="mb-4">
            <div class="mb-3">
                <label class="form-label">Kỹ năng nổi bật của bạn</label>
                <textarea name="skills" class="form-control" placeholder="VD: PHP, SQL, HTML, Giao tiếp, Làm việc nhóm..." required><?php if(isset($skills)) echo htmlspecialchars($skills); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Kinh nghiệm làm việc</label>
                <textarea name="experience" class="form-control" placeholder="VD: Thực tập sinh tại ABC, 1 năm lập trình viên web..." required><?php if(isset($exp)) echo htmlspecialchars($exp); ?></textarea>
            </div>
            <button type="submit" class="btn btn-info w-100">Tư vấn AI</button>
        </form>
        <?php if($advice): ?>
            <h5 class="mb-2">Lộ trình AI đề xuất:</h5>
            <div class="ai-output"><?= nl2br(htmlspecialchars($advice)) ?></div>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
