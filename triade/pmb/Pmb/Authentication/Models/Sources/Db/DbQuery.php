<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DbQuery.php,v 1.3 2023/07/13 13:14:58 dbellamy Exp $

namespace Pmb\Authentication\Models\Sources\Db;

use Exception;
use PDO;
use Pmb\Authentication\Common\AbstractQuery;
use Pmb\Authentication\Interfaces\AuthenticationQueryInterface;
use Pmb\Authentication\Models\AuthenticationHandler;
use password;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class DbQuery extends AbstractQuery implements AuthenticationQueryInterface
{

    // Parametres
    protected $login_modes = [
        'submit'
    ];

    protected $charset = 'utf-8';

    /**
     * AuthenticationQuery implementation *
     */
    protected $dsn = '';

    protected $user = null;

    protected $pwd = null;

    protected $table = null;

    // Nom du champ que l'on veut
    protected $login_attr = null;

    protected $password_attr = null;

    protected $password_function = null;

    protected $attrs = [];

    protected $result = [];

    protected $query = '';

    protected $connect = false;

    protected $external_user = null;

    protected $external_attributes = [];

    /**
     * Lancement authentification (mode submit)
     *
     * @param AuthenticationHandler $caller
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    public function runExternalLoginSubmit(AuthenticationHandler $caller, string $username, string $password)
    {
        static::$logger->debug(__METHOD__ . " >> {$this->login_attr}={$username} / ***");

        $this->caller = $caller;

        if (empty($username) || empty($password)) {
            static::$logger->debug(__METHOD__ . " >> KO");
            return false;
        }

        try {
            // Connexion a la base de donnee
            $pdo = new PDO($this->dsn, $this->user, $this->pwd);

            // Requete
            $query = "SELECT $this->password_attr FROM $this->table WHERE $this->login_attr = '$username'";
            $result = $pdo->query($query);

            // On traite le resultat de la requete
            // Pour l'instant je suppose que je n'ai qu'un seul resultat
            if (! \password::verify_hash($password, $result->fetch(\PDO::FETCH_ASSOC)["password"])) {
                static::$logger->debug("Invalid password");
                return false;
            }

            // Si c'est bon on va chercher les informations
            // Ici on va recuperer les attributs que l'on veut : attrs
            // Pour l'instant on recupere tout
            $query = "SELECT * FROM $this->table WHERE $this->login_attr = '$username'";
            $result = $pdo->query($query);

            $this->external_user = $username;
            $this->external_attributes = $result->fetch(\PDO::FETCH_ASSOC);

            // On retourne les informations
            return true;

        } catch (Exception $e) {
            static::$logger->debug("Erreur de connexion : " . $e->getMessage());
            echo "Erreur de connexion : " . $e->getMessage();
            die();
        }
    }
}
