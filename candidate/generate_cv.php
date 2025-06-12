<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/config.php';
require_once '../includes/candidate_header.php'; // dùng chung header ứng viên
require __DIR__ . '/../vendor/autoload.php';
use Dompdf\Dompdf;
use GuzzleHttp\Client;

// Kiểm tra đăng nhập đúng vai trò ứng viên
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'candidate') {
    header("Location: ../auth/login.php");
    exit();
}

$cv_content = '';
$pdf_path = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin form
    $info = "Họ tên: {$_POST['name']}\nEmail: {$_POST['email']}\nHọc vấn: {$_POST['education']}\nKỹ năng: {$_POST['skills']}\nKinh nghiệm: {$_POST['experience']}";
    $prompt = "Dựa trên thông tin sau, hãy tạo một bản CV chuyên nghiệp, trình bày rõ ràng:\n$info";
    $client = new Client();
    try {
        $response = $client->post(
            'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=' . GEMINI_API_KEY,
            [
                'headers' => [ 'Content-Type'  => 'application/json' ],
                'json' => [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ]
                ]
            ]
        );
        $result = json_decode($response->getBody(), true);
        if (
            isset($result['candidates'][0]['content']['parts'][0]['text'])
            && !empty($result['candidates'][0]['content']['parts'][0]['text'])
        ) {
            $cv_content = $result['candidates'][0]['content']['parts'][0]['text'];
        } else {
            $error = 'Không nhận được phản hồi hợp lệ từ AI.';
        }
    } catch (Exception $e) {
        $error = 'Lỗi khi gọi API Gemini: ' . $e->getMessage();
    }

    // Nếu có nội dung CV thì sinh PDF
    if (!empty($cv_content) && empty($error)) {
        $dompdf = new Dompdf();
        // Bạn có thể custom lại giao diện PDF ở đây nếu muốn
        $html_cv = '<pre style="font-family:Arial, Helvetica, sans-serif;font-size:1rem">' . htmlspecialchars($cv_content) . '</pre>';
        $dompdf->loadHtml($html_cv);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $pdfOutput = $dompdf->output();
        // Đảm bảo thư mục uploads/CV/ tồn tại
        $upload_dir = "../uploads/CV/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $pdf_path = $upload_dir . 'generated_cv_' . $_SESSION['user_id'] . '_' . time() . '.pdf';
        file_put_contents($pdf_path, $pdfOutput);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo CV tự động bằng AI - Phân tích CV AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; background: linear-gradient(135deg, #f7fafc 0%, #e2ecf8 100%); min-height: 100vh;}
        .main-content { max-width: 580px; margin: 38px auto 0 auto; background: #fff; border-radius: 18px; box-shadow: 0 4px 28px 0 rgba(0, 96, 237, 0.08); padding: 32px 36px 28px 36px;}
        h3 { color: #3276ef; font-weight: 700;}
        pre { white-space: pre-wrap; }
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
        <h3 class="mb-3"><i class="bi bi-robot"></i> Tạo CV tự động bằng AI</h3>
        <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

        <form method="post" class="mb-4">
            <div class="mb-3"><input type="text" name="name" class="form-control" placeholder="Họ tên" required></div>
            <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
            <div class="mb-3"><textarea name="education" class="form-control" placeholder="Học vấn" required></textarea></div>
            <div class="mb-3"><textarea name="skills" class="form-control" placeholder="Kỹ năng" required></textarea></div>
            <div class="mb-3"><textarea name="experience" class="form-control" placeholder="Kinh nghiệm" required></textarea></div>
            <button type="submit" class="btn btn-success">Tạo CV</button>
        </form>

        <?php if($cv_content): ?>
            <h5 class="mt-4">Bản CV AI tạo ra:</h5>
            <pre><?= htmlspecialchars($cv_content) ?></pre>
            <?php if($pdf_path): ?>
                <a href="<?= htmlspecialchars($pdf_path) ?>" class="btn btn-primary mt-2" target="_blank">Tải PDF</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
