<?php

namespace Rafael\RespiraBem\services;

class ViewRender
{
    public static function render($view)
    {
        $viewPath = __DIR__ . '/../../views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            throw new \Exception("View não encontrada: $view");
        }

        ob_start();
        include $viewPath;
        return ob_get_clean();
    }

    public static function renderComponent($component,$data = [])
    {
        $componentPath = __DIR__ . '/../../views/components/' . $component . '.php';

        if (!file_exists($componentPath)) {
            throw new \Exception("Componente não encontrado: $component");
        }

        ob_start();
        extract($data);
        
        include $componentPath;
        return ob_get_clean();
    }
}
