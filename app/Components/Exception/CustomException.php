<?php

namespace App\Components\Exception;

class CustomException extends \Exception
{
    /**
     * Affichage de l'exception dans une page 404
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function show()
    {
        $loader = new \Twig_Loader_Filesystem(ROOT . '/app/Views');
        $twig = new \Twig_Environment($loader);
        echo $twig->render('404.html.twig', [
            'message' => $this->getMessage(),
            'session' => $_SESSION,
            'baseUrl' => str_replace('index.php', '', $_SERVER['SCRIPT_NAME'])
        ]);
    }
}