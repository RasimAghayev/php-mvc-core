<?php

declare(strict_types=1);

namespace Rasim\PhpMvcCore;

class Core
{
    protected string $currentController = 'PhoneBooks';
    protected string $currentMethod = 'index';
    protected array $params = [];
    protected string $controllerPath;

    public function __construct(string $controllerPath = '../app/mvc/controllers/')
    {
        $this->controllerPath = $controllerPath;
        $url = $this->getUrl();
        if (is_null($url)) {
            return;
        }
        if (file_exists($this->controllerPath . ucwords($url[0]) . '.php')) {
            $this->currentController = ucwords($url[0]);
            unset($url[0]);
        }
        require_once $this->controllerPath . $this->currentController . '.php';
        $this->currentController = new $this->currentController;
        if (isset($url[1])) {
            if (method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                unset($url[1]);
            }
        }
        $this->params = $url ? array_values($url) : [];
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl(): ?array
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return null;
    }
}
