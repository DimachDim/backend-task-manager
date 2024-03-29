<?php
// Проверяем URI, если он не соответствует нашему пути frontend/web
if ($_SERVER['REQUEST_URI'] != '/frontend/api') {
    // Перенаправляем запрос на путь frontend/web
    header('Location: /frontend/api' . $_SERVER['REQUEST_URI']);
    exit;
}