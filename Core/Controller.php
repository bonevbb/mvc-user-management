<?php

namespace Core;

//Base Controller
abstract class Controller
{
    /**
     * Parameters from the matched route
     * @var array
     */
    protected $routeParams = [];

    public function __construct($routeParams)
    {
        $this->routeParams = $routeParams;
    }

    /**
     * Magic method _call
     * @param $name
     * @param $arguments
     *
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        $method = $name;

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $arguments);
                $this->after();
            } else {
                throw new \Exception("Method $method not found in controller " . get_class($this));
            }
        }
    }

    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {

    }

    /**
     * After filter - called after an action method.
     *
     * @return void
     */
    protected function after()
    {

    }

    protected function redirectBack(){
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    protected function redirectTd(){
        echo '<pre>';
        print_r();
        die;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }


}