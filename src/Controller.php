<?php

declare(strict_types=1);

namespace Rasim\PhpMvcCore;

class Controller
{
    public function model(string $model, string $modelPath = '../app/mvc/models/'): mixed
    {
        require_once $modelPath . $model . '.php';
        return new ("\\{$model}")();
    }

    public function view(string $url, array $data = [], string $viewPath = '../app/mvc/views/'): void
    {
        $fullPath = $viewPath . $url . '.php';
        if (file_exists($fullPath)) {
            require_once $fullPath;
        } else {
            die('View does not exist');
        }
    }
}
