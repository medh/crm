<?php

namespace App\Controllers;

use InvalidArgumentException;
use App\Components\Exception\CustomException;

class AddressController extends MainController implements ControllerInterface
{
    /**
     * AddressController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->loadModel('Address');
        $this->loadModel('Contact');
    }

    /**
     * Affichage de la liste des adresses d'un contact
     *
     * @param int|null $contactId
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index(int $contactId = null)
    {
        echo $this->twig->render('address.index.html.twig', [
            'addresses' => $this->Address->getByContact($contactId),
            'contact' => $this->getParent($contactId)
        ]);
    }

    /**
     * Récuperation du contact avec son id
     *
     * @param int $parentId
     * @return bool
     * @throws CustomException
     */
    private function getParent(int $parentId): \stdClass
    {
        $contact = $this->Contact->findById($parentId);

        if (!$contact) {
            throw new CustomException('Le Contact ' . $parentId . ' n\'existe pas');
        } else {
            return $contact;
        }
    }

    /**
     * Ajout d'une adresse pour un contact
     *
     * @param int|null $contactId
     * @throws CustomException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function add(int $contactId = null)
    {
        $this->getParent($contactId);

        try {
            if (!empty($_POST) && $this->isValid($_POST)) {
                $response = $this->sanitize($_POST);
                if ($this->Address->create($response)) {
                    static::redirect('address/index/' . $contactId);
                }
            }
        } catch (InvalidArgumentException $e) {
            $error = $e->getMessage();
        }

        echo $this->twig->render('address.add.html.twig', [
            'error' => $error ?? false,
            'data' => array_merge($_POST, ['idContact' => $contactId])
        ]);
    }

    /**
     * Modification d'une adresse d'un contact
     *
     * @param int $id
     * @throws CustomException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function edit(int $id)
    {
        $addressData = $this->checkIfExist($id);

        try {
            if (!empty($_POST) && $this->isValid($_POST)) {
                $addressData = $_POST;
                $response = $this->sanitize($_POST);

                if ($this->Address->update($id, $response)) {
                    static::redirect('address/index/' . $addressData['idContact']);
                }
            }
        } catch (InvalidArgumentException $e) {
            $error = $e->getMessage();
        }

        echo $this->twig->render('address.add.html.twig', ['error' => $error ?? false, 'data' => $addressData]);
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
        $address = $this->Address->findById($id);
        if (!$address) {
            throw new CustomException('L\'addresse ' . $id . ' n\'existe pas');
        }
        return get_object_vars($address);
    }

    /**
     * Suppression d'une adresse d'un contact
     *
     * @param int $id
     * @throws CustomException
     */
    public function delete(int $id)
    {
        if ($address = $this->checkIfExist($id)) {
            $this->Address->delete($id);
            static::redirect('address/index/' . $address['idContact']);
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
            'number' => $data['number'],
            'street' => strtoupper($data['street']),
            'postalCode' => $data['postalCode'],
            'city' => strtoupper($data['city']),
            'country' => strtoupper($data['country']),
            'idContact' => $data['idContact']
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
        if (!isset($data['number']) || empty($data['number'])) {
            throw new InvalidArgumentException('Le numéro est obligatoire');
        } else if (!is_numeric($data['number'])) {
            throw new InvalidArgumentException('Le numéro est invalide');
        }

        if (!isset($data['street']) || empty($data['street'])) {
            throw new InvalidArgumentException('La rue est obligatoire');
        }

        if (!isset($data['postalCode']) || empty($data['postalCode'])) {
            throw new InvalidArgumentException('Le code postal est obligatoire');
        } else if (!is_numeric($data['postalCode'])) {
            throw new InvalidArgumentException('Le  code postal est invalide');
        }

        if (!isset($data['city']) || empty($data['city'])) {
            throw new InvalidArgumentException('La ville est obligatoire');
        }

        if (!isset($data['country']) || empty($data['country'])) {
            throw new InvalidArgumentException('Le pays est obligatoire');
        }

        return true;
    }
}