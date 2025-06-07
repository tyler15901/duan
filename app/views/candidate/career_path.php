<?php
require_once "../includes/db.php";
require_once "../includes/functions.php";
require_once "../includes/config.php";
require_once "../includes/gemini_client.php";
require_once "../includes/header.php";
require 'vendor/autoload.php';
redirect_if_not_logged_in();
if (!is_candidate()) { header("Location: /recruiter/dashboard.php"); exit; }

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
<h3>Tư vấn lộ trình nghề nghiệp</h3>
<form method="post">
    <div class="mb-3"><textarea name="skills" class="form-control" placeholder="Kỹ năng nổi bật" required></textarea></div>
    <div class="mb-3"><textarea name="experience" class="form-control" placeholder="Kinh nghiệm làm việc" required></textarea></div>
    <button type="submit" class="btn btn-info">Tư vấn AI</button>
</form>
<?php if($advice): ?>
    <h4 class="mt-4">Lộ trình AI đề xuất:</h4>
    <pre><?= htmlspecialchars($advice) ?></pre>
<?php endif; ?>
<?php require_once "../includes/footer.php"; ?>