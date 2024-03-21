<?php


function generateRandomString($length, $firstChar = '') {
    // Определение строки, используемой для генерации случайной строки
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    // Вычисление длины строки $characters
    $charactersLength = strlen($characters);
    // Инициализация конечной строки. Пока пустой.
    $randomString = '';
    // Добавление первого символа в начало
    $randomString .= $firstChar; 

    // Цикл, который повторяется (length - 1) раз
    for ($i = 1; $i < $length; $i++) {
        // Добавление случайного символа
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}