<?php
abstract class Controller {
    protected function render(string $view, array $data = []): void {
        extract($data, EXTR_SKIP);
        require dirname(__DIR__, 2) . '/views/layout/header.php';
        require dirname(__DIR__, 2) . '/views/' . $view . '.php';
        require dirname(__DIR__, 2) . '/views/layout/footer.php';
    }
    protected function redirect(string $url): never { header('Location: ' . $url); exit; }
    protected function flash(string $type, string $message): void { $_SESSION['flash'] = compact('type','message'); }
}
