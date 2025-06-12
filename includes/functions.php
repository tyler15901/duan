<?php
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_candidate() {
    return is_logged_in() && $_SESSION['role'] === 'candidate';
}

function is_employer() {
    return is_logged_in() && $_SESSION['role'] === 'employer';
}

function redirect_if_not_logged_in() {
    if (!is_logged_in()) {
        header("Location: /auth/login.php");
        exit;
    }
}