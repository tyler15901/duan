<?php
class Controller {
    protected function render($view, $data = []) {
        extract($data);
        require __DIR__ . '/../app/views/layouts/header.php';
        require __DIR__ . '/../app/views/' . $view . '.php';
        require __DIR__ . '/../app/views/layouts/footer.php';
    }
}