<?php

namespace App\Models;

class UserModel extends AbstractModel
{
    /** @var string */
    protected $table = "users";

    /**
     * Récupération de l'utilisateur par son login
     *
     * @param $login
     * @return array|bool|false|mixed|\PDOStatement
     */
    public function getByLogin($login)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE login = ?", [$login], true);

    }
}