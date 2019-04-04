

## Installation 

_________________

**Pour installer l'application, suivez les instructions.**

Cloner le depot :
> git clone https://github.com/medh/crm.git && cd crm

Pour installer les dépendances :
> composer install

Installer la base de données avec vos informations :  
DB_USER : nom d'utilisateur  
DB_PASS : mot de passe  
DB_NAME : nom de la base de données
> mysql -u"DB_USER" -p"DB_PASS" << END
  CREATE DATABASE IF NOT EXISTS DB_NAME;
  use DB_NAME;
  source upgrade.sql;
  END

## Configuration
_________________


Modifiez le fichier : **app/Config.php** avec vos informations de bdd renseignées  
auparavant :
    
    ligne 29 :
    
    $this->settings = [
                "db_user" => "root",
                "db_pass" => "root",
                "db_host" => "localhost",
                "db_name" => "crm"
            ];

**lancer le serveur php :**
>php -S localhost:8000 index.php


## Test unitaire
_________________
Pour lancer les tests unitaires :
>php ./vendor/phpunit/phpunit/phpunit --configuration phpunit.xml tests --teamcity

Pour voir les resultats :
  - /tests/coverage/report 
  
