<?php

namespace App\Components\Auth;

use App;
use App\Database;

class Auth extends AbstractAuth
{
    /** @var Database */
    private $database;

    /**
     * Auth constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Mathode de connexion de l'utilisateur à partir de son login et du mot de passe encoder en md5
     *
     * @param $login
     * @param $password
     *
     * @return boolean
     */
    public function login($login, $password) : bool
    {
        $userModel = new App\Models\UserModel($this->database);
        $user = $userModel->getByLogin($login);

        if ($user && $user->password === md5($password)) {
            $_SESSION['auth'] = [
                "id" => $user->id,
                "login" => $user->login,
                "email" => $user->email
            ];
            return true;
        } else {
            return false;
        }
    }

    /**
     * Méthode de redirection d'un utilisateur vers la page de login
     */
    public static function forbidden()
    {
        header('HTTP/1.0 403 Forbidden');
        header('Location: ?p=user/login');
    }
}