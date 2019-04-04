<?php

namespace App\Components\Api;

class Palindrome
{
    private $name;

    /**
     * Palindrome constructor.
     */
    public function __construct($name = null)
    {
        if (!is_null($name)) {
            $this->setName($name);
        }
    }

    /**
     * Setter de la chaine en entrée
     *
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Vérification du plindrome
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return strrev($this->name) == $this->name;
    }


}