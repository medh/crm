<?php

namespace App\Models;

use App\Database;

abstract class AbstractModel
{
    /** @var Database  */
    protected $database;

    /**
     * Model constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Méthode pour résupérer la liste de toutes les lignes dans une table
     *
     * @return array|bool|false|mixed|\PDOStatement
     */
    public function getAll()
    {
        return $this->query('SELECT * FROM ' . $this->table);
    }

    /**
     * Méthode de récupération d'une entité à partir de don Id
     *
     * @param $id
     * @return array|bool|false|mixed|\PDOStatement
     */
    public function findById($id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE id = ?", [$id], true);
    }

    /**
     * Méthode de préparation et d'exécution de requete
     *
     * @param $statement
     * @param null $attributes
     * @param bool $one
     * @return array|bool|false|mixed|\PDOStatement
     */
    public function query($statement, $attributes = null, $one = false)
    {
        if ($attributes) {
            return $this->database->prepare(
                $statement,
                $attributes,
                null,
                $one
            );
        } else {
            return $this->database->query(
                $statement,
                null,
                $one
            );
        }
    }

    /**
     * Méthode de création d'une ligne dans la table cible
     *
     * @param $fields
     * @return array|bool|mixed|\PDOStatement
     */
    public function create($fields)
    {
        $fields = $this->cleanInputs($fields);
        $sqlParts = [];
        $attributes = [];
        foreach ($fields as $k => $v) {
            $sqlParts[] = "$k = ?";
            $attributes[] = $v;
        }
        $sqlPart = implode(', ', $sqlParts);
        return $this->query("INSERT INTO {$this->table} SET $sqlPart",
            $attributes, true);
    }

    /**
     * Méthode de mise à jour d'une ligne dans la table cible
     *
     * @param $id
     * @param $fields
     */
    public function update($id, $fields)
    {
        $fields = $this->cleanInputs($fields);
        $sqlParts = [];
        $attributes = [];
        foreach ($fields as $k => $v) {
            $sqlParts[] = "$k = ?";
            $attributes[] = $v;
        }
        $attributes[] = $id;
        $sqlPart = implode(', ', $sqlParts);

        return $this->query("UPDATE {$this->table} SET $sqlPart WHERE id = ?",
            $attributes, true);
    }

    /**
     * Supprime un enregistrement
     *
     * @param $id
     * @return array|bool|false|mixed|\PDOStatement
     */
    public function delete($id)
    {
        return $this->query("DELETE FROM {$this->table} WHERE id = ?", [$id], true);
    }

    /**
     * Méthode de normalisation des champs
     *
     * @param $data
     * @return array|string
     */
    private function cleanInputs($data)
    {
        $cleanInputs = [];
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $cleanInputs[$k] = $this->cleanInputs($v);
            }
        } else {
            if (get_magic_quotes_gpc()) {
                $data = trim(stripslashes($data));
            }
            $data = strip_tags($data);
            $cleanInputs = trim($data);
        }

        return $cleanInputs;
    }
}