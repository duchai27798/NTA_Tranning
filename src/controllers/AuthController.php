<?php


namespace app\controllers;


use app\core\Controller;

/**
 * Class AuthController
 * @package app\controllers
 */
class AuthController extends Controller
{
    public function login()
    {
        return $this->render('login');
    }

    public function register()
    {
        return $this->render('register');
    }
}