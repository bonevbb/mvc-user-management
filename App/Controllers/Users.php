<?php

namespace App\Controllers;

use App\Models\Customers;
use Core\Controller;
use Core\Paginator;
use Core\View;
use App\Models\Users as UsersModel;
use Core\Session;

class Users extends Controller
{
    /**
     * Show the index page.
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index(){
        $customers = Customers::getAll();
        $users = UsersModel::getAllWithPagination();

        $pages = Paginator::getPages(UsersModel::getPageLimit());

        $message = Session::get('message');
        Session::destroy('message');

        View::renderTemplate('Users/index.html',[
            'customers' => $customers,
            'users' => $users,
            'message' => $message,
            'pages' => $pages
        ]);
    }

    /**
     * Show the add new page
     *
     * @return void
     */
    public function create(){
        UsersModel::create($_POST);
        Session::set('message','User created successfully!');

        $this->redirectBack();
    }

    /**
     * Show the edit page
     *
     *
     */
    public function edit(){
        $user =  UsersModel::getUserById($this->routeParams['id']);
        header('Content-Type: application/json');
        echo json_encode(array('user' => $user));
    }

    /**
     * Show the edit page
     *
     * @return void
     */
    public function delete(){
        UsersModel::delete($this->routeParams['id']);
        Session::set('message','Record deleted successfully!');

        $this->redirectBack();
    }

    /**
     * Update user
     *
     * @return void
     */
    public function update(){
        UsersModel::update($_POST);

        Session::set('message','User details updated successfully!');
        $this->redirectBack();
    }
}