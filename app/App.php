<?php

use App\Config;
use App\Database;

class App
{
    /** @var  $dbInstance */
    private $dbInstance;
    /** @var  $instance */
    private static $instance;

    /**
     * Auto chargement des classes
     */
    public static function load()
    {
        ini_set('session.save_path',realpath(ROOT . '/session'));
        session_start();
        require ROOT . '/app/Autoloader.php';
        App\Autoloader::register();
        require ROOT . '/vendor/autoload.php';
    }

    /**
     * Singleton de la class
     *
     * @return App
     */
    public static function getInstance() : self
    {
        if (is_null(self::$instance)) {
            self::$instance = new App();
        }
        return self::$instance;
    }

    /**
     * Récupération du model avec son nom
     *
     * @param string $name
     * @return mixed
     */
    public function getModel(string $name)
    {
        $name = '\\App\\Models\\' . ucfirst($name) . 'Model';
        return new $name($this->getDatabase());
    }

    /**
     * Setter de la Bdd
     *
     * @param $config
     */
    private function setDatabase(Config $config)
    {
        $this->dbInstance = new Database($config->get('db_name'),
            $config->get('db_user'), $config->get('db_pass'),
            $config->get('db_host'));
    }

    /**
     * Récupération de la base de données
     *
     * @return Database
     */
    public function getDatabase() : Database
    {
        if (is_null($this->dbInstance)) {
            $this->setDatabase(Config::getInstance());
        }

        return $this->dbInstance;
    }
}