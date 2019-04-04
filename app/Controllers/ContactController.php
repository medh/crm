<?php

namespace App\Controllers;

use Exception;
use App\Components\Exception\CustomException;
use InvalidArgumentException;

class ContactController extends MainController implements ControllerInterface
{
    /** @var int $userId */
    protected $userId;

    /**
     * ContactController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->loadModel('Contact');

        $this->userId = $_SESSION['auth']['id'];
    }

    /**
     * Affichage de la liste des contacts de l'utilisateur connecté
     *
     * @param int|null $id
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index(int $id = null)
    {
        echo $this->twig->render('contact.index.html.twig', [
            'contacts' => $this->Contact->getByUser($this->userId)
        ]);
    }

    /**
     * Ajout d'un contact
     *
     * @param int|null $id
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function add(int $id = null)
    {
        try {
            if (!empty($_POST) && $this->isValid($_POST)) {
                $response = $this->sanitize($_POST);
                if ($this->Contact->create($response)) {
                    static::redirect('contact/index');
                }
            }
        } catch (InvalidArgumentException $e) {
            $error = $e->getMessage();
        }
        echo $this->twig->render('contact.add.html.twig', ['error' => $error ?? false, 'data' => $_POST]);
    }

    /**
     * Modification d'un contact
     *
     * @param int $id
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function edit(int $id)
    {
        $contactData = $this->checkIfExist($id);

        try {
            if (!empty($_POST) && $this->isValid($_POST)) {
                $contactData = $_POST;
                $response = $this->sanitize($_POST);
                if ($this->Contact->update($id, $response)) {
                    static::redirect('contact/index');
                }
            }
        } catch (InvalidArgumentException $e) {
            $error = $e->getMessage();
        }

        echo $this->twig->render('contact.add.html.twig', ['error' => $error ?? false, 'data' => $contactData]);
    }

    /**
     * Vérification de l'existance d'une addresse
     *
     * @param $id
     * @return array
     * @throws CustomException
     */
    private function checkIfExist($id): array
    {
        $contact = $this->Contact->findById($id);
        if (!$contact) {
            throw new CustomException('Le contact ' . $id . ' n\'existe pas');
        }
        return get_object_vars($contact);
    }

    /**
     * Suppression d'un contact
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        if ($this->checkIfExist($id)) {
            $this->Contact->delete($id);
            static::redirect('contact/index');
        }

    }

    /**
     * Normalisation des données à enregistrer
     *
     * @param array $data
     * @return array
     */
    public function sanitize(array $data = []): array
    {
        return [
            'email' => strtolower($data['email']),
            'firstname' => ucfirst(strtolower($data['firstname'])),
            'lastname' => ucfirst(strtolower(($data['lastname']))),
            'userId' => $this->userId
        ];
    }

    /**
     * Vérification des contraintes d'enregistrement
     *
     * @param array $data
     * @return bool
     * @throws Exception
     */
    private function isValid(array $data): bool
    {
        if (!isset($data['lastname']) || empty($data['lastname'])) {
            throw new InvalidArgumentException('Le lastname est obligatoire');
        } else {
            $isPalindrome = $this->apiClient('palindrome', ['name' => $data['lastname']]);
            if ($isPalindrome->response) {
                throw new InvalidArgumentException($isPalindrome->message);
            }
        }

        if (!isset($data['firstname']) || empty($data['firstname'])) {
            throw new InvalidArgumentException('Le firstname est obligatoire');
        }

        if (!isset($data['email']) || empty($data['email'])) {
            throw new InvalidArgumentException('L\'email est obligatoire');
        } else {
            $isEmail = $this->apiClient('email', ['email' => $data['email']]);
            if (!$isEmail->response) {
                throw new InvalidArgumentException($isEmail->message);
            }
        }

        return true;
    }
}