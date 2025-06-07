<?php
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller {

    public function login() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['name'];
                // Điều hướng theo vai trò
                if ($user['role'] === 'candidate') {
                    header("Location: /?url=candidate/dashboard");
                } else {
                    header("Location: /?url=recruiter/dashboard");
                }
                exit;
            } else {
                $error = "Sai email hoặc mật khẩu!";
            }
        }
        // Đăng ký thành công chuyển tới login với ?reg=1
        $registered = isset($_GET['reg']);
        $this->render('auth/login', compact('error', 'registered'));
    }

    public function register() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $name = trim($_POST['name']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = $_POST['role'];
            $userModel = new User();

            if ($userModel->findByEmail($email)) {
                $error = "Email đã tồn tại!";
            } else {
                $userModel->create($email, $password, $name, $role);
                header("Location: /?url=auth/login&reg=1");
                exit;
            }
        }
        $this->render('auth/register', compact('error'));
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: /?url=auth/login");
        exit;
    }
}