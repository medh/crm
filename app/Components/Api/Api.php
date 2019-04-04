<?php

namespace App\Components\Api;

class Api extends ApiService
{
    /**
     * Méthode à appeler par l'api pour vérifier
     * si la chaine en entrée est un palindrome
     */
    public function palindrome()
    {
        if ($this->getRequestMethod() != "POST") {
            $this->response('', 406);
        }

        $name = $this->request['name'];

        $palindrome = new Palindrome();
        $palindrome->setName($name);

        if ($palindrome->isValid()) {
            $result = [
                "response" => true,
                "message" => "Le nom du contact ne peut pas être un palindrome"
            ];
        } else {
            $result = [
                "response" => false,
                "message" => " Le nom est valide"
            ];
        }

        $this->response($this->json($result), 200);
    }

    /**
     * Vérification du format de l'email
     */
    public function email()
    {
        if ($this->getRequestMethod() != "POST") {
            $this->response('', 406);
        }

        $email = $this->request['email'];

        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result = [
                "response" => true,
                "message" => "L'email est au bon format"
            ];
        } else {
            $result = [
                "response" => false,
                "message" => "Le format de l'email n'est pas correct"
            ];
        }

        $this->response($this->json($result), 200);
    }

    /**
     * Encodage des données en json
     *
     * @param $data
     * @return string
     */
    private function json($data): string
    {
        return is_array($data) ? json_encode($data) : $data;
    }
}
