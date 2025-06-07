<?php
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_candidate() {
    return is_logged_in() && $_SESSION['role'] === 'candidate';
}

function is_recruiter() {
    return is_logged_in() && $_SESSION['role'] === 'recruiter';
}

function redirect_if_not_logged_in() {
    if (!is_logged_in()) {
        header("Location: /auth/login.php");
        exit;
    }
}