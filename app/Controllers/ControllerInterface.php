<?php

namespace App\Controllers;

interface ControllerInterface
{
    /**
     * Methode pour page d'accueil
     */
    public function index(int $id = null);

    /**
     * Methode pour page de creation
     */
    public function add(int $id = null);

    /**
     * Methode pour page de modification
     */
    public function edit(int $id);

    /**
     * Methode pour page de suppression
     */
    public function delete(int $id);

    /**
     * @param array $data
     *
     * @return array
     */
    public function sanitize(array $data = []): array;

}