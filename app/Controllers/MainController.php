<?php

namespace App\Controllers;

use App;
use App\Components\Auth\Auth;
use \Twig_Environment;

class MainController
{
    /** @var Twig_Environment */
    protected $twig;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->loader = new \Twig_Loader_Filesystem(ROOT . '/app/Views');
        $this->twig = new \Twig_Environment($this->loader);
        $this->auth = new Auth(App::getInstance()->getDatabase());
        $this->twig->addGlobal('session', $_SESSION);
        $this->twig->addGlobal('baseUrl', str_replace('index.php', '', $_SERVER['PHP_SELF']));
    }

    /**
     * Méthode de chargement de model
     *
     * @param string $model
     */
    protected function loadModel(string $model)
    {
        $this->{$model} = App::getInstance()->getModel($model);
    }

    /**
     * Méthode de redirection
     *
     * @param string $route
     */
    public static function redirect(string $route)
    {
        $baseUrl = str_replace('index.php', '', $_SERVER['PHP_SELF']);
        if (!headers_sent()) {
            header('Location: ' . $baseUrl . '?p=' . $route);
            exit();
        }
    }

    /**
     * Méthode d'appel de l'api
     *
     * @param string $methode
     * @param array $datas
     * @return mixed
     */
    public function apiClient(string $methode, array $data = [])
    {
        $api = $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '?api=' . $methode;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_FAILONERROR,1);

        $return = curl_exec($curl);
        curl_close($curl);
        return json_decode($return);
    }
}