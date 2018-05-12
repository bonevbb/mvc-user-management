<?php

namespace Core;

use Twig_SimpleFilter;


/**
 * View
 */
class View
{
    /**
     * Render a view file
     *
     * @param string $view The view file
     *
     * @throws \Exception
     * @return void
     */
    public static function render($view, $args = [])
    {
        extract($args,EXTR_SKIP);

        $file = "../App/Views/$view"; //relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    /**
     *  Render a view template using Twig
     *
     * @param string $template The template file
     * @param array $args Associative array of data to display in the view (optional)
     *
     * @return void
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function renderTemplate($template, $args = []){
        static $twig = null;

        if($twig === null){
            $loader = new \Twig_Loader_Filesystem('../App/Views');
            $twig = new \Twig_Environment($loader);
        }

        echo $twig->render($template, $args);
    }
}