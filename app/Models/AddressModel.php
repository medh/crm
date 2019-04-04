<?php

namespace App\Models;

class AddressModel extends AbstractModel
{
    /** @var string  */
    protected $table = "addresses";

    /**
     * Méthode de récupération des adresses d'un contact
     *
     * @param int $contactId
     * @return array|bool|mixed|\PDOStatement
     */
    public function getByContact(int $contactId)
    {
        return $this->query("SELECT * FROM $this->table WHERE idContact = ?", [$contactId]);
    }
}