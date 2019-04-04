<?php

namespace App\Models;

class ContactModel extends AbstractModel
{
    /** @var string  */
    protected $table = "contacts";

    /**
     * Méthode de récupération des contacts d'un utilisateur
     *
     * @param $userId
     * @return array|bool|mixed|\PDOStatement
     */
    public function getByUser($userId)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE userId = ?", [$userId]);
    }
}