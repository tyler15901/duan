<?php
session_start();
require_once __DIR__ . '/../../../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>CV AI Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="/">CV AI Platform</a>
    <div>
      <?php if(is_logged_in()): ?>
        <span class="text-white me-2">Xin chào, <?= htmlspecialchars($_SESSION['name']) ?></span>
        <a href="/?url=auth/logout" class="btn btn-danger btn-sm">Đăng xuất</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<div class="container">