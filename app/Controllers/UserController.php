<?php

namespace App\Controllers;

use App;

class UserController extends MainController
{
    /**
     * Méthode d'authentification
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function login()
    {
        $errors = false;

        if (!empty($_POST)) {
            if ($this->auth->login($_POST['login'], $_POST['password'])) {
                static::redirect('contact/index');
            } else {
                $errors = true;
            }
        }

        echo $this->twig->render('login.html.twig', ['errors' => $errors]);
    }

    /**
     * Méthode de deconnexion
     */
    public function logout()
    {
        if (isset($_SESSION['auth'])) {
            unset($_SESSION['auth']);
            $this->auth::forbidden();
        }
    }
}