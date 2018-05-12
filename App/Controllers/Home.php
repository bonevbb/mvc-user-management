<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;

class Home extends Controller
{
    /**
     * Show the index page.
     *
     * @return void
     */
    /**
     * Show the index page.
     *
     * @return void
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index(){
        View::renderTemplate('Home/index.html');
    }
}