<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: record_log.class.php,v 1.27.4.1 2025/02/07 13:49:04 qvarin Exp $

use Pmb\Common\Helper\JsonWebToken;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path;
require_once $class_path . '/cookies_consent.class.php';

class record_log
{
    public const ALLOWED_METHOD = ['GET', 'POST'];
    public const ALLOW_EMPTY_USER_AGENT = false;

    // public const DISALLOWED_WORDS_IN_USER_AGENT = ['BOT', 'SPIDER', 'CRAWL', 'QWANTIFY', 'SLURP'];
    public const DISALLOWED_WORDS_IN_USER_AGENT = [
        'BOT', 'SPIDER', 'CRAWL', 'QWANTIFY', 'SLURP',
        'OWASP', 'SQLMAP', 'DIRBUSTER', 'GOBUSTER', 'NMAP',
        'PYTHON', 'WGET', 'CURL', 'CHATGPT', 'SCRAP',
    	'PUPPETEER', 'METASPLOIT', 'BURP SUITE', 'BURPSUITE',
    	'FUZZ'
    ];

    /**
     * Identifiant du log
     *
     * @var integer
     */
    public $id_log = 0;

    /**
     * URL demandee
     *
     * @var string
     */
    public $url_asked = '';

    /**
     * URL de reference
     *
     * @var string
     */
    public $url_ref = '';

    /**
     * Tableau des variables GET
     *
     * @var array
     */
    public $get_log = [];

    /**
     * Tableau des variables POST
     *
     * @var array
     */
    public $post_log = [];

    /**
     * Tableau des variables SERVER
     *
     * @var array
     */
    public $serveur = [];

    /**
     * Numero de session
     *
     * @var integer
     */
    public $num_session = 0;

    /**
     * Donees de l'emprunteur
     *
     * @var array
     */
    public $empr = [];

    /**
     * Donnees de la notice
     *
     * @var array
     */
    public $doc = [];

    /**
     * Donnees de l'exemplaire
     *
     * @var array
     */
    public $expl = [];

    /**
     * Nombre de resultats
     *
     * @var array
     */
    public $nb_results = [];

    /**
     * Donnees generiques
     *
     * @var array
     */
    public $generique = [];

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->init_environnement();
    }

    /**
     * Initialisation de l'environnement
     *
     * @return void
     */
    protected function init_environnement(): void
    {
        $this->get_log = $_GET ?? [];
        $this->post_log = $_POST ?? [];
        $this->serveur = $_SERVER ?? [];

        if (isset($_SERVER['REQUEST_URI'])) {
            $this->url_asked = $_SERVER['REQUEST_URI'];
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->url_ref = $_SERVER['HTTP_REFERER'];
        }
    }

    /**
     * Ajout un element au log
     *
     * @param string $nom
     * @param mixed $value
     * @return void
     */
    public function add_log(string $nom, $value = 0): void
    {
        if (!$value) {
            return;
        }

        switch($nom) {
            case 'num_session':
                $this->num_session = $value;
                break;

            case 'empr':
                $this->empr = $value;
                break;

            case 'docs':
                $this->doc = $value;
                break;

            case 'expl':
                $this->expl = $value;
                break;

            case 'nb_results':
                $this->nb_results = $value;
                break;

            default:
                $this->generique[$nom] = $value;
                break;

        }
    }

    /**
     * Indique si la methode de la requete est valid
     *
     * @return boolean
     */
    private static function is_valid_request_method(): bool
    {
        return in_array($_SERVER['REQUEST_METHOD'], static::ALLOWED_METHOD);
    }

    /**
     * Indique si la requete est rejete
     *
     * @return boolean
     */
    private static function is_rejected_request(): bool
    {
        global $pmb_logs_exclude_robots;

        if (empty($pmb_logs_exclude_robots)) {
            // Le parametre est vide. Pourquoi ?
            return false;
        }

        $exclude_robots = explode(",", $pmb_logs_exclude_robots);
        $active = array_shift($exclude_robots);
        if (!$active) {
            // Le parametre n'est pas active
            return false;
        }

        if (!static::ALLOW_EMPTY_USER_AGENT && empty($_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }

        // Exclusion des robots en fonction de l'user agent
        foreach (static::DISALLOWED_WORDS_IN_USER_AGENT as $word) {
            if (preg_match('/' . $word . '/i', $_SERVER['HTTP_USER_AGENT'] ?? '')) {
                return true;
            }
        }

        // Exclusion d'adresses IP
        if (!empty($exclude_robots)) {
            return (
                in_array($_SERVER['REMOTE_ADDR'], $exclude_robots) ||
                (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && in_array($_SERVER['HTTP_X_FORWARDED_FOR'], $exclude_robots))
            );
        }

        return false;
    }

    /**
     * Indique si la requete est valide
     *
     * @return boolean
     */
    public static function is_valid_request(): bool
    {
        if (!static::is_valid_request_method() || static::is_rejected_request()) {
            return false;
        }

        // La personne a fait opposition a l'utilisation des donnees
        return false === cookies_consent::is_opposed_pmb_logs_service();
    }

    /**
     * Transfert de logopac vers statopac
     *
     * @return boolean
     */
    public function transfert_log_to_stats(): bool
    {
        global $pmb_perio_vidage_log;

        $first_day = $this->sql_value('SELECT date_log FROM logopac WHERE validated = 1 ORDER BY date_log LIMIT 1');
        $periodicite = $this->sql_value('SELECT DATEDIFF(CURRENT_DATE(), "' . addslashes($first_day) . '")');

        if ($periodicite >= $pmb_perio_vidage_log) {
            pmb_mysql_query('INSERT INTO statopac (date_log, url_demandee, url_referente, get_log, post_log,num_session, server_log, empr_carac, empr_doc, empr_expl,nb_result, gen_stat)
                    SELECT date_log, url_demandee, url_referente, get_log, post_log, num_session, server_log, empr_carac, empr_doc, empr_expl, nb_result, gen_stat FROM logopac WHERE validated = 1');

            pmb_mysql_query("TRUNCATE TABLE logopac");
            return true;
        }

        return false;
    }

    /**
     * Nettoyage
     *
     * @return void
     */
    private function clean(): void
    {
        global $pmb_perio_vidage_stat;

        $uncached_internal_emptylogstatopac = 0;
        $res_uncache = pmb_mysql_query("SELECT valeur_param FROM parametres_uncached WHERE type_param='internal' AND sstype_param='emptylogstatopac'");
        if (pmb_mysql_num_rows($res_uncache)) {
            $uncached_internal_emptylogstatopac = pmb_mysql_result($res_uncache, 0, 0);
        }

        if ($uncached_internal_emptylogstatopac) {
            $date_internal = explode(" ", $uncached_internal_emptylogstatopac);
            if ((time() - $date_internal[1]) > 86400) {
                pmb_mysql_query("UPDATE parametres_uncached SET valeur_param=0 WHERE type_param='internal' AND sstype_param='emptylogstatopac'");
                $uncached_internal_emptylogstatopac = 0;
            }
        }

        if (!$uncached_internal_emptylogstatopac) {
            [$mode, $nb_jours] = explode(",", $pmb_perio_vidage_stat);
            $first_day_stat = $this->sql_value("SELECT date_log FROM statopac ORDER BY date_log LIMIT 1");

            switch ($mode) {
                case '1':
                    // On vide tous les x jours
                    $periodicite = $this->sql_value("SELECT DATEDIFF(CURRENT_DATE(),'".addslashes($first_day_stat)."')");
                    if ($periodicite >= $nb_jours) {
                        pmb_mysql_query("UPDATE parametres_uncached SET valeur_param='1 ".(time())."' WHERE type_param='internal' AND sstype_param='emptylogstatopac'");
                        pmb_mysql_query("TRUNCATE TABLE statopac");
                        pmb_mysql_query("UPDATE parametres_uncached SET valeur_param=0 WHERE type_param='internal' AND sstype_param='emptylogstatopac'");
                    }
                    break;

                case '2':
                    // On vide tout ce qui a plus de x jours
                    $periodicite = $this->sql_value("SELECT DATEDIFF(CURRENT_DATE(),'".addslashes($first_day_stat)."')");
                    if ($periodicite >= $nb_jours) {
                        pmb_mysql_query("UPDATE parametres_uncached set valeur_param='1 ".(time())."' WHERE type_param='internal' AND sstype_param='emptylogstatopac'");
                        pmb_mysql_query("DELETE from statopac where date_log< DATE_SUB(CURRENT_DATE() , INTERVAL ".addslashes($nb_jours)." DAY)");
                        pmb_mysql_query("UPDATE parametres_uncached SET valeur_param=0 WHERE type_param='internal' AND sstype_param='emptylogstatopac'");
                    }
                    break;
            }
        }
    }

    /**
     * Valide un log
     *
     * @param string $token JWT contenant l'id du log
     * @return boolean
     */
    public function valid(string $token): bool
    {
        $data = JsonWebToken::decode($token);
        if (false === $data) {
            return false;
        }

        $id_log = intval($data['id_log'] ?? 0);
        if (empty($id_log) || $id_log < 1) {
            return false;
        }

        $result = pmb_mysql_query('SELECT 1 FROM logopac WHERE id_log = ' . $id_log . ' AND validated = 0');
        if (pmb_mysql_num_rows($result)) {
            pmb_mysql_query('UPDATE logopac SET validated = 1 WHERE id_log = ' . $id_log);

            return true;
        }
        return false;
    }

    /**
     * Retourne le script de validation
     *
     * @return string Retourne le script ou une chaine vide si le log est deja valide
     */
    public function validation_script(): string
    {
        $result = pmb_mysql_query('SELECT 1 FROM logopac WHERE id_log = ' . $this->id_log . ' AND validated = 0');
        if (pmb_mysql_num_rows($result)) {
            $body = http_build_query([
                'log_token' => JsonWebToken::encode(['id_log' => $this->id_log])
            ]);

            return <<<HTML
            <script>
                fetch('./ajax.php?module=ajax&categ=log&action=valid', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'no-cache': true
                    },
                    body: '{$body}'
                });
            </script>
            HTML;
        }

        return '';
    }

    /**
     * Enregistrement
     *
     * @return false|int false pour indiquer qu'il n'y a pas d'enregistrement, sinon id du log
     */
    public function save(bool $validate = false)
    {
        // Verification
        if (!static::is_valid_request()) {
            return false;
        }

        // Transfert de la table logopac > statopac
        $this->transfert_log_to_stats();

        if (0 < $this->id_log) {
            // Mise a jour du log
            // On ne remet pas a jour les champs (url_demandee, url_referente, get_log, post_log, num_session, server_log)
            // car elles sont calcules dans le constructeur

            $rqt = "UPDATE logopac SET ";
            $rqt .= "empr_carac = '".addslashes(serialize($this->empr))."', ";
            $rqt .= "empr_doc = '".addslashes(serialize($this->doc))."', ";
            $rqt .= "empr_expl = '".addslashes(serialize($this->expl))."', ";
            $rqt .= "nb_result = '".addslashes(serialize($this->nb_results))."', ";
            $rqt .= "gen_stat = '".addslashes(serialize($this->generique))."' ";
            $rqt .= "WHERE id_log = " . $this->id_log;

            pmb_mysql_query($rqt);
        } else {
            // Insertion du log
            $rqt = "INSERT INTO logopac (url_demandee, url_referente, get_log, post_log, num_session, server_log, empr_carac, empr_doc, empr_expl, nb_result, gen_stat) VALUES ('";
            $rqt .= addslashes($this->url_asked)."','".addslashes($this->url_ref)."','".addslashes(serialize($this->get_log))."','".addslashes(serialize($this->post_log))."','".addslashes($this->num_session)."','".addslashes(serialize($this->serveur))."','".addslashes(serialize($this->empr))."','".addslashes(serialize($this->doc))."','".addslashes(serialize($this->expl))."','".addslashes(serialize($this->nb_results))."','".addslashes(serialize($this->generique))."')";
            pmb_mysql_query($rqt);

            $this->id_log = intval(pmb_mysql_insert_id());
        }

        if ($validate) {
            pmb_mysql_query('UPDATE logopac SET validated = 1 WHERE id_log = ' . $this->id_log);
        }

        // Gestion du verrou
        $this->clean();

        return $this->id_log;
    }

    /**
     * Joue une requete SQL et retourne le premier resultat
     *
     * @param string $rqt
     * @return string|int
     */
    protected function sql_value(string $rqt)
    {
        $result = pmb_mysql_query($rqt);
        if ($result) {
            $row = pmb_mysql_fetch_row($result);
        }

        return !empty($row[0]) ? $row[0] : '';
    }

    /**
     * Enregistrement des donnees de l'emprunteur
     *
     * @return void
     */
    public function log_empr_data(): void
    {
        if ($_SESSION['user_code']) {
            $res = pmb_mysql_query($this->get_empr_query());
            if (pmb_mysql_num_rows($res)) {
                $empr_carac = pmb_mysql_fetch_array($res);
                pmb_mysql_free_result($res);

                $this->add_log('empr', $empr_carac);
            }
        }
    }

    /**
     * Enregistrement de la vue OPAC utilisee
     *
     * @return void
     */
    public function log_opac_view(): void
    {
        global $opac_opac_view_activate;
        if ($opac_opac_view_activate){
            $this->add_log('opac_view', $_SESSION["opac_view"]);
        }
    }

    /**
     * Enregistrement de la vue OPAC utilisee
     *
     * @return void
     */
    public function log_search(): void
    {
        global $search, $nb_results_tab;
        global $search_type, $tab;

        $this->add_log('nb_results', $nb_results_tab);

        if ($search) {
            if ($search_type=="external_search") {
                switch ($_SESSION["ext_type"]) {
                    case "multi":
                        $search_file="search_fields_unimarc";
                        break;
                    default:
                        $search_file="search_simple_fields_unimarc";
                        break;
                }
            } else if (isset($tab) && $tab == "affiliate") {
                switch ($search_type) {
                    case "simple_search":
                        $search_file="search_fields_unimarc";
                        break;
                    default:
                        $search_file="search_simple_fields_unimarc";
                        break;
                }
            } else {
                $search_file = "";
            }

            $search_stat = new search($search_file);
            $this->add_log('multi_search', $search_stat->serialize_search());
            $this->add_log('multi_human_query', $search_stat->make_human_query());
        }
    }

    /**
     * Retourne la requete SQL pour recuperer les donnees de l'emprunteur
     *
     * @return string
     */
    protected function get_empr_query(): string
    {
        $query = " select empr_prof,empr_cp, empr_ville as ville, empr_year, empr_sexe, empr_pays, empr_login, empr_date_adhesion, empr_date_expiration, count(pret_idexpl) as nbprets, count(resa.id_resa) as nbresa, code.libelle as codestat, es.statut_libelle as statut, categ.libelle as categ, gr.libelle_groupe as groupe,dl.location_libelle as location
            from empr e
            left join empr_codestat code on code.idcode=e.empr_codestat
            left join empr_statut es on e.empr_statut=es.idstatut
            left join empr_categ categ on categ.id_categ_empr=e.empr_categ
            left join empr_groupe eg on eg.empr_id=e.id_empr
            left join groupe gr on eg.groupe_id=gr.id_groupe
            left join docs_location dl on e.empr_location=dl.idlocation
            left join resa on e.id_empr=resa_idempr
            left join pret on e.id_empr=pret_idempr
            where e.empr_login='".addslashes($_SESSION['user_code'])."'
            group by resa_idempr, pret_idempr";
        return $query;
    }
}
