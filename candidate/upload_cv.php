<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/config.php';
require_once '../includes/gemini_client.php';
require_once '../includes/candidate_header.php';
require __DIR__ . '/../vendor/autoload.php';
use Smalot\PdfParser\Parser;

// Chỉ cho ứng viên đăng nhập mới sử dụng
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'candidate') {
    header("Location: ../auth/login.php");
    exit();
}

$error = $success = '';
$ai_feedback = '';
$gemini_raw = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv_file'])) {
    $file = $_FILES['cv_file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($file['error'] == 0 && $ext === 'pdf') {
        // Đảm bảo thư mục uploads/CV tồn tại
        $uploadDir = "../uploads/CV/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $target = $uploadDir . uniqid() . "_" . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $target)) {
            // Đọc text từ PDF
            try {
                $parser = new Parser();
                $pdf = $parser->parseFile($target);
                $cv_text = $pdf->getText();
            } catch (Exception $e) {
                $error = "Không đọc được nội dung file PDF.";
                $cv_text = "";
            }

            // Gọi Gemini API nếu đọc được nội dung
            if ($cv_text) {
                $prompt = "Đây là CV:\n$cv_text\n\nHãy đánh giá (1) điểm mạnh, (2) điểm yếu, (3) điểm số CV (0-100), (4) gợi ý cải thiện, (5) gợi ý vị trí phù hợp, trình bày từng mục rõ ràng.";
                $gemini = new GeminiClient(GEMINI_API_KEY);
                $result = $gemini->generateContent($prompt);
                $gemini_raw = $result; // Lưu lại để debug nếu cần

                // Xử lý kết quả trả về từ Gemini
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $ai_feedback = $result['candidates'][0]['content']['parts'][0]['text'];
                } elseif (isset($result['error']['message'])) {
                    $ai_feedback = 'Lỗi từ Gemini: ' . htmlspecialchars($result['error']['message']);
                } else {
                    $ai_feedback = 'Không nhận được phản hồi từ Gemini.';
                }
            }

            // Lưu xuống DB nếu có nội dung và phản hồi AI
            if ($cv_text && $ai_feedback) {
                $stmt = $pdo->prepare("INSERT INTO cvs (user_id, cv_name, file_path, content, ai_feedback, uploaded_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$_SESSION['user_id'], $file['name'], $target, $cv_text, $ai_feedback]);
                $success = "Đã phân tích CV và lưu kết quả.";
            } elseif (!$error) {
                $error = "Không phân tích được nội dung CV.";
            }
        } else {
            $error = "Lỗi lưu file!";
        }
    } else {
        $error = "Chỉ nhận file PDF.";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đánh giá CV cá nhân - Phân tích CV AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; background: linear-gradient(135deg, #f7fafc 0%, #e2ecf8 100%); min-height: 100vh;}
        .main-content { max-width: 1100px; margin: 38px auto 0 auto; background: #fff; border-radius: 18px; box-shadow: 0 4px 28px 0 rgba(0, 96, 237, 0.08); padding: 32px 36px 28px 36px;}
        h3 { color: #3276ef; font-weight: 700;}
        .ai-output { background: #f7faff; border-radius: 12px; padding: 18px 22px; font-size: 1.08rem; white-space: pre-wrap;}
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
    <div class="main-content">
        <h3 class="mb-3"><i class="bi bi-file-earmark-check"></i> Đánh giá CV cá nhân</h3>
        <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

        <?php if ($success && !empty($ai_feedback)): ?>
            <div class="alert alert-info">
                <h5>Kết quả phân tích AI:</h5>
                <div class="ai-output"><?= nl2br(htmlspecialchars($ai_feedback)) ?></div>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="mt-3">
            <div class="mb-3">
                <label class="form-label">Tải lên CV (chỉ nhận PDF)</label>
                <input type="file" name="cv_file" accept=".pdf" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Phân tích CV</button>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
