<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb.class.php,v 1.28.4.6 2025/05/14 07:31:46 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path;
require_once $class_path."/connecteurs.class.php";
require_once $class_path."/jsonRPCClient.php";

if (!defined("JSONRPC")) {
    define("JSONRPC",1);
}
if (!defined("SOAP")) {
    define("SOAP",2);
}

class xml_dom_pmb {

    /**
     * XML d'origine
     * @var string
     */
    public $xml;

    /**
     * Charset courant (iso-8859-1 ou utf-8)
     * @var string
     */
    public $charset;

    /**
     * Arbre des noeuds du document
     * L'arbre est compose de noeuds qui ont la structure suivante :
     *
     * $noeud = [
     *      NAME => Nom de l'element pour un noeud de type element (TYPE = 1)
     *      ATTRIBS => Tableau des attributs (nom => valeur)
     *      TYPE => 1 = Noeud element, 2 = Noeud texte
     *      CHILDS => Tableau des noeuds enfants
     * ]
     *
     * @var array
     */
    public $tree;

    /**
     * Signalement d'erreur : true : erreur lors du parse, false : pas d'erreur
     *
     * @var boolean
     */
    public $error = false;

    /**
     * Message d'erreur correspondant a l'erreur de parse
     *
     * @var string
     */
    public $error_message = "";

    /**
     *
     * @var integer
     */
    public $depth = 0;

    /**
     *
     * @var array
     */
    public $last_elt = array();

    /**
     *
     * @var array
     */
    public $n_elt = array();

    /**
     *
     * @var array
     */
    public $cur_elt = array();

    /**
     *
     * @var boolean
     */
    public $last_char = false;

    public function close_node()
    {
        $this->last_elt[$this->depth - 1]["CHILDS"][] = $this->cur_elt;
        $this->last_char = false;
        $this->cur_elt = $this->last_elt[$this->depth - 1];
        $this->depth --;
    }

    public function startElement($parser, $name, $attribs)
    {
        if ($this->last_char) {
            $this->close_node();
        }
        $this->last_elt[$this->depth] = $this->cur_elt;
        $this->cur_elt = array();
        $this->cur_elt["NAME"] = $name;
        $this->cur_elt["ATTRIBS"] = $attribs;
        $this->cur_elt["TYPE"] = 1;
        $this->last_char = false;
        $this->depth ++;
    }

    /**
     *
     * @param object $parser
     * @param string $name
     */
    public function endElement($parser, $name)
    {
        if ($this->last_char) {
            $this->close_node();
        }
        $this->close_node();
    }

    /**
     *
     * @param object $parser
     * @param string $char
     */
    public function charElement($parser, $char)
    {
        if ($this->last_char) {
            $this->close_node();
        }
        $this->last_char = true;
        $this->last_elt[$this->depth] = $this->cur_elt;
        $this->cur_elt = array();
        $this->cur_elt["DATA"] = $char;
        $this->cur_elt["TYPE"] = 2;
        $this->depth ++;
    }

    /**
     * Instanciation du parser
     *
     * Le document xml est parse selon le charset donne et une representation sous forme d'arbre est generee
     *
     * @param string $xml : XML a manipuler
     * @param string $charset : Charset du document XML
     */
    public function __construct($xml, $charset = "iso-8859-1")
    {
        $this->charset = $charset;
        $this->cur_elt = array(
            "NAME" => "document",
            "TYPE" => "0"
        );

        // Initialisation du parser
        $xml_parser = xml_parser_create($this->charset);
        xml_set_object($xml_parser, $this);
        xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, 1);
        xml_set_element_handler($xml_parser, "startElement", "endElement");
        xml_set_character_data_handler($xml_parser, "charElement");

        if (! xml_parse($xml_parser, $xml)) {
            $this->error_message = sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser));
            $this->error = true;
        }
        $this->tree = $this->last_elt[0];
        xml_parser_free($xml_parser);
        unset($xml_parser);
    }

    /**
     * Recuperation d'un noeud par son chemin
     *
     * Recherche un noeud selon le chemin donne en parametre. Un noeud de depart peut etre precise
     * Les chemins ont la syntaxe suivante :
     *  <a>
     *      <b>
     *          <c id="0">Texte</c>
     *          <c id="1">
     *              <d>Sous texte</d>
     *          </c>
     *          <c id="2">Texte 2</c>
     *      </b>
     *  </a>
     *
     * a/b/c        Le premier noeud element c (<c id="0">Texte</c>)
     * a/b/c[2]/d   Le premier noeud element du deuxieme noeud c (<d>Sous texte</d>)
     * a/b/c[3]     Le troisieme noeud element c (<c id="2">Texte 2</c>)
     * a/b/id@c     Le premier noeud element c (<c id="0">Texte</c>). L'attribut est ignore
     * a/b/id@c[3]  Le troisieme noeud element c (<c id="2">Texte 2</c>). L'attribut est ignore
     *
     * Les attributs ne peuvent être cites que sur le noeud final.
     *
     * @param string $path Chemin du noeud recherche
     * @param noeud [$node] Noeud de depart de la recherche (le noeud doit etre de type 1)
     * @return array : Noeud correspondant au chemin ou false si non trouve
     */
    public function get_node($path, $node = array())
    {
        if (empty($node)) {
            $node = &$this->tree;
        }
        $paths = explode("/", $path);
        for ($i = 0; $i < count($paths); $i ++) {
            if ($i == count($paths) - 1) {
                $pelt = explode("@", $paths[$i]);
                if (count($pelt) == 1) {
                    $p = $pelt[0];
                } else {
                    $p = $pelt[1];
                }
            } else {
                $p = $paths[$i];
            }
            $matches = [];
            if (preg_match("/\[([0-9]*)\]$/", $p, $matches)) {
                $name = substr($p, 0, strlen($p) - strlen($matches[0]));
                $n = $matches[1];
            } else {
                $name = $p;
                $n = 0;
            }
            $nc = 0;
            $found = false;
            for ($j = 0; $j < count($node["CHILDS"]); $j ++) {
                if (($node["CHILDS"][$j]["TYPE"] == 1) && ($node["CHILDS"][$j]["NAME"] == $name)) {
                    // C'est celui la !!
                    if ($nc == $n) {
                        $node = &$node["CHILDS"][$j];
                        $found = true;
                        break;
                    } else {
                        $nc ++;
                    }
                }
            }
            if (! $found) {
                return false;
            }
        }
        return $node;
    }

    /**
     * Recuperation d'un ensemble de noeuds par leur chemin
     *
     * Recherche d'un ensemble de noeuds selon le chemin donne en parametre. Un noeud de depart peut etre precise
     * Les chemins ont la syntaxe suivante :
     *  <a>
     *      <b>
     *          <c id="0">Texte</c>
     *          <c id="1">
     *              <d>Sous texte</d>
     *          </c>
     *          <c id="2">Texte 2</c>
     *      </b>
     *  </a>
     *
     * a/b/c        Tous les elements c fils de a/b
     * a/b/c[2]/d   Tous les elements d fils de a/b et du deuxieme element c
     * a/b/id@c     Tous les noeuds elements c fils de a/b. L'attribut est ignore
     *
     * @param string $path : Chemin des noeuds recherches
     * @param noeud [$node] : Noeud de depart de la recherche (le noeud doit être de type 1)
     * @return array : Tableau des noeuds correspondants au chemin ou false si non trouve
     */
    public function get_nodes($path, $node = "")
    {
        $n = 0;
        $nodes = array();
        while ($nod = $this->get_node($path . "[$n]", $node)) {
            $nodes[] = $nod;
            $n ++;
        }
        return $nodes;
    }

    /**
     * Recuperation des donnees serialisees d'un noeud element
     *
     * Recupere sous forme texte les donnees d'un noeud element :
     * - Si c'est un element qui n'a qu'un noeud texte comme fils, renvoie le texte
     * - Si c'est un element qui a d'autres elements comme fils, la version serialisee des enfants est renvoyee
     *
     * @param array $node : Noeud duquel recuperer les donnees
     * @param bool $force_entities : true : les donnees sont renvoyees avec les entites xml, false : les donnees sont renvoyees sans entites
     * @return string
     */
    public function get_datas($node, $force_entities = false)
    {
        $char = "";
        if (! isset($node['TYPE']) || ($node["TYPE"] != 1)) {
            return false;
        }
        // Recherche des fils et verification qu'il n'y a que du texte !
        if (! isset($node["CHILDS"]) || ! is_array($node["CHILDS"])) {
            return $char;
        }
        $flag_text = true;
        for ($i = 0; $i < count($node["CHILDS"]); $i ++) {
            if ($node["CHILDS"][$i]["TYPE"] != 2)
                $flag_text = false;
        }
        if ((! $flag_text) && (! $force_entities)) {
            $force_entities = true;
        }
        for ($i = 0; $i < count($node["CHILDS"]); $i ++) {
            if ($node["CHILDS"][$i]["TYPE"] == 2) {
                if ($force_entities) {
                    $char .= htmlspecialchars($node["CHILDS"][$i]["DATA"], ENT_NOQUOTES, $this->charset);
                } else {
                    $char .= $node["CHILDS"][$i]["DATA"];
                }
            } else {
                $char .= "<" . $node["CHILDS"][$i]["NAME"];
                if (count($node["CHILDS"][$i]["ATTRIBS"])) {
                    foreach ($node["CHILDS"][$i]["ATTRIBS"] as $key => $val) {
                        $char .= " " . $key . "=\"" . htmlspecialchars($val, ENT_NOQUOTES, $this->charset) . "\"";
                    }
                }
                $char .= ">";
                $char .= $this->get_datas($node["CHILDS"][$i], $force_entities);
                $char .= "</" . $node["CHILDS"][$i]["NAME"] . ">";
            }
        }
        return $char;
    }

    /**
     * Recuperation des attributs d'un noeud
     *
     * Renvoie le tableau des attributs d'un noeud element (Type 1)
     *
     * @param array $node : Noeud element duquel on veut les attributs
     * @return mixed : Tableau des attributs Nom => Valeur ou false si ce n'est pas un noeud de type 1
     */
    public function get_attributes($node)
    {
        if ($node["TYPE"] != 1) {
            return false;
        }
        return $node["ATTRIBUTES"];
    }

    /**
     * Recupere les donnees serialisees d'un noeud ou la valeur d'un attribut selon le chemin
     *
     * Exemples de valeurs renvoyees selon le chemin :
     *  <a>
     *      <b>
     *          <c id="0">Texte</c>
     *          <c id="1">
     *              <d>Sous texte</d>
     *          </c>
     *          <c id="2">Texte 2</c>
     *      </b>
     *  </a>
     *
     * a/b/c        Renvoie : "Texte"
     * a/b/c[2]/d   Renvoie : "Sous texte"
     * a/b/c[2]     Renvoie : "<d>Sous texte</d>"
     * a/b/c[3]     Renvoie : "Texte 2"
     * a/b/id@c     Renvoie : "0"
     * a/b/id@c[3]  Renvoie : "2"
     *
     * @param string $path : chemin du noeud recherche
     * @param array $node : Noeud de depart de la recherche
     * @return string : donnee serialisee ou valeur de l'attribut, false si le chemin n'existe pas
     */
    public function get_value($path, $node = "")
    {
        $elt = $this->get_node($path, $node);
        if ($elt) {
            $paths = explode("/", $path);
            $pelt = explode("@", $paths[count($paths) - 1]);
            if (count($pelt) > 1) {
                $a = $pelt[0];
                // Recherche de l'attribut
                $matches = [];
                if (preg_match("/\[([0-9]*)\]$/", $a, $matches)) {
                    $attr = substr($a, 0, strlen($a) - strlen($matches[0]));
                    $n = $matches[1];
                } else {
                    $attr = $a;
                    $n = 0;
                }
                $nc = 0;
                $found = false;
                foreach ($elt["ATTRIBS"] as $key => $val) {
                    if ($key == $attr) {
                        // C'est celui la !!
                        if ($nc == $n) {
                            $value = $val;
                            $found = true;
                            break;
                        } else {
                            $nc ++;
                        }
                    }
                }
                if (! $found) {
                    $value = "";
                }
            } else {
                $value = $this->get_datas($elt);
            }
        }
        return $value;
    }

    /**
     * Recupere les donnees serialisees ou la valeur d'un attribut d'un ensemble de noeuds selon le chemin
     *
     * Exemples de valeurs renvoyees selon le chemin :
     *  <a>
     *      <b>
     *          <c id="0">Texte</c>
     *          <c id="1">
     *              <d>Sous texte</d>
     *          </c>
     *          <c id="2">Texte 2</c>
     *      </b>
     *  </a>
     *
     * a/b/c        Renvoie : [0]=>"Texte",[1]=>"<d>Sous texte</d>",[2]=>"Texte 2"
     * a/b/c[2]/d   Renvoie : [0]=>"Sous texte"
     * a/b/id@c     Renvoie : [0]=>"0",[1]=>"1",[2]=>"2"
     *
     * @param string $path : chemin des noeuds recherches
     * @param array $node : Noeud de depart de la recherche
     * @return array : Tableau des donnees serialisees ou des valeurs de l'attribut, false si le chemin n'existe pas
     */
	public function get_values($path, $node = "")
    {
        $elts = array();
        $values = array();
        $n = 0;
        while ($elt = $this->get_node($path . "[$n]", $node)) {
            $elts[$n] = $elt;
            $n ++;
        }
        if (count($elts)) {
            for ($i = 0; $i < count($elts); $i ++) {
                $elt = $elts[$i];
                $paths = explode("/", $path);
                $pelt = explode("@", $paths[count($paths) - 1]);
                if (count($pelt) > 1) {
                    $a = $pelt[0];
                    // Recherche de l'attribut
                    $matches = [];
                    if (preg_match("/\[([0-9]*)\]$/", $a, $matches)) {
                        $attr = substr($a, 0, strlen($a) - strlen($matches[0]));
                        $n = $matches[1];
                    } else {
                        $attr = $a;
                        $n = 0;
                    }
                    $nc = 0;
                    $found = false;
                    foreach ($elt["ATTRIBS"] as $key => $val) {
                        if ($key == $attr) {
                            // C'est celui la !!
                            if ($nc == $n) {
                                $values[] = $val;
                                $found = true;
                                break;
                            } else {
                                $nc ++;
                            }
                        }
                    }
                    if (! $found) {
                        $values[] = "";
                    }
                } else {
                    $values[] = $this->get_datas($elt);
                }
            }
        }
        return $values;
    }
}

class pmb extends connector {

    /**
     *
     * @var string
     */
    public $search_id;

    /**
     * url distante
     *
     * @var string
     */
    public $url;

    /**
     * identifiant pour la recherche distante
     *
     * @var string
     */
    public $username;

    /**
     * mot de passe pour la recherche distante
     *
     * @var string
     */
    public $password;

    /**
     * protocole utilise en cours
     *
     * @var int
     */
    public $current_protocole;

    /**
     * Liste des indexes de recherche possibles pour le site
     *
     * @var unknown
     */
    public $searchindexes;

    /**
     * Numero de l'index de recherche de la classe
     *
     * @var unknown
     */
    public $current_searchindex;

    /**
     * Type de recherche (power ou simple)
     *
     * @var unknown
     */
    public $match_index;

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
        return "pmb";
    }

    public function source_get_property_form($source_id)
    {
        global $charset;

        $max_return = 100;
        $url = '';
        $protocole = JSONRPC;
        $display_items = 0;
        $authentification = 0;
        $auth_login = '';
        $auth_password = '';
        $auth_connexion_phrase = '';

        $params=$this->get_source_params($source_id);
        if ($params["PARAMETERS"]) {
            // Affichage du formulaire avec $params["PARAMETERS"]
            $vars = unserialize($params["PARAMETERS"]);
            foreach ($vars as $key => $val) {
                global ${$key};
                ${$key} = $val;
            }
        }
        if (empty($max_return)) {
            $max_return = 100;
        }

        $checked1 = '';
        $checked2 = '';
        $checked3 = '';

        if ($protocole == SOAP) {
            $checked1 = "checked";
        }
        if ($protocole == JSONRPC) {
            $checked2 = "checked";
        }

        if ($authentification == 1) {
            $checked3 = " checked ";
        }

        $form = "
        <script>var old_search_index='search_index_" . $url . "'</script>
        <div class='row'>
            <div class='colonne3'>
                <label for='url'>" . $this->msg["pmb_site"] . "</label>
            </div>
            <div class='colonne_suite'>
                <input name='url' id='url' class='saisie-50em' type='text' value='" . htmlentities($url, ENT_QUOTES, $charset) . "'/>
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for='protocole'>" . $this->msg["pmb_protocole"] . "</label>
            </div>
            <div class='colonne_suite'>
                <input name='protocole' id='protocole' class='' type='radio' value='" . SOAP . "' " .
        // coche l'option deja enregistree. Par defaut l'option Json est cochee
        $checked1 . "'/>" . $this->msg["pmb_prtcl_soap"] . "
                <input name='protocole' id='protocole' class='' type='radio' value='" . JSONRPC . "'" .
                    $checked2 . "/>" . $this->msg["pmb_prtcl_json"] . "
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for='max_return'>" . $this->msg["pmb_max_return"] . "</label>
            </div>
            <div class='colonne_suite'>
                <input type='text' name='max_return' id='max_return' class='saisie-5em' value='" . htmlentities($max_return, ENT_QUOTES, $charset) . "' size='10'/>
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for='display_items'>" . $this->msg["pmb_display_items"] . "</label>
            </div>
            <div class='colonne_suite'>
                <input type='checkbox' name='display_items' id='display_items' value='1' " . ($display_items == "1" ? " checked='checked' " : "") . " />
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for='authentification'>" . $this->msg["pmb_authentification"] . "</label>
            </div>
            <div class='colonne_suite'>
                <input type='checkbox' name='authentification' id='authentification' value='1'" . $checked3 . " onchange='checkAuth();' />
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for=''>" . $this->msg["pmb_username"] . "</label>
            </div>
            <div class='colonne_suite'>
                <input type='text' name='auth_login' id='auth_login' class='saisie-5em' value='" . htmlentities($auth_login, ENT_QUOTES, $charset) . "'" . ($authentification == "0" ? " disabled " : "") . "'/>
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for=''>" . $this->msg["pmb_password"] . "</label>
            </div>
            <div class='colonne_suite'>
                <input type='text' name='auth_password' id='auth_password' class='saisie-5em' value='" . htmlentities($auth_password, ENT_QUOTES, $charset) . "'" . ($authentification == "0" ? " disabled " : "") . "'/>
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for=''>" . $this->msg["pmb_connexion_phrase"] . "</label>
            </div>
            <div class='colonne_suite'>
                <input type='text' name='auth_connexion_phrase' id='auth_connexion_phrase' class='saisie-50em' value='" . htmlentities($auth_connexion_phrase, ENT_QUOTES, $charset) . "'" . ($authentification == "0" ? " disabled " : "") . "'/>
            </div>
        </div>

        <div class='row'></div>
        <script>

        function checkAuth(){
            if(document.getElementById('authentification').checked){
                document.getElementById('auth_login').disabled = false;
                document.getElementById('auth_password').disabled = false;
                document.getElementById('auth_connexion_phrase').disabled = false;
            } else {
                document.getElementById('auth_login').disabled = true;
                document.getElementById('auth_password').disabled = true;
                document.getElementById('auth_connexion_phrase').disabled = true;
            }
        }
        </script>";
        return $form;
    }

    /**
     * Fonction de recherche
     */
    public function search($source_id, $query, $search_id)
    {
        global $charset;
        global $pmb_curl_proxy;
        global $base_path;

        $this->error = false;
        $this->error_message = "";

        $max_return = 100;
        $display_items = 0;
        $protocole = JSONRPC;
        $url = '';

        $params = $this->get_source_params($source_id);
        $this->fetch_global_properties();
        if ($params["PARAMETERS"]) {
            // Affichage du formulaire avec $params["PARAMETERS"]
            $vars = unserialize($params["PARAMETERS"]);
            foreach ($vars as $key => $val) {
                global ${$key};
                ${$key} = $val;
            }
        }

        // on affecte la valeur 100 au nombre de notices retournees si aucune valeur n'a ete affectee
        if (empty($max_return)) {
            $max_return = 100;
        }

        // construit le tableau pour la recherche multi-criteres
        $tab_query = array();
        foreach ($query as $i => $q) {
            $t = array();
            $t["inter"] = $q->inter;
            $uid = explode("_", $q->uid);
            if ($uid[0] == "f") {
                $t["field"] = $uid[1];
            } else {
                $t["field"] = $q->uid;
            }
            $t["operator"] = $q->op;
            if (count($q->values) > 1) {
                $vi = 0;
                foreach ($q->values as $value) {
                    $t["value"][$vi] = $value;
                    $vi ++;
                }
            } else {
                $t["value"] = $q->values[0];
            }
            $tab_query[$i] = $t;
        }

        // on verifie d'abord les parametres du formulaire
        if ($display_items == "1") {
            $items = true;
        }else {
            $items = false;
        }

        $this->current_protocole = $protocole;

        try {

            switch ($this->current_protocole) {

                case JSONRPC:
                    $ws = new jsonRPCClient($url);
                    $ws->setUser($vars['auth_login'] ?? '');
                    $ws->setPwd($vars['auth_password'] ?? '');
                    $ws->setSalt($vars['auth_connexion_phrase'] ?? '');
                    $tab_query = encoding_normalize::utf8_normalize($tab_query);
                    $res = $ws->pmbesSearch_advancedSearch($query[0]->sc_type, $tab_query);

                    if (! empty($res["nbResults"])) {
                        // Si le nombre de notices est > a la valeur max alors on attribue a nb la valeur max
                        $nb = $res["nbResults"] > $max_return ? $max_return : $res["nbResults"];
                        // On va chercher le contenu des notices au format pmb_xml_unimarc
                        $notices = $ws->pmbesSearch_fetchSearchRecords($res["searchId"], 0, $nb, "pmb_xml_unimarc", "utf-8", true, $items);

                        for ($i = 0; $i < $nb; $i ++) {
                            $this->rec_records($notices[$i], $source_id, $search_id);
                        }
                    }
                    break;

                case SOAP:

                    $ws = new SoapClient($url . "&wsdl");
                    $res = $ws->pmbesSearch_advancedSearch($query[0]->sc_type, $tab_query);

                    if (! empty($res->nbResults)) {
                        // Si le nombre de notices est > a la valeur max alors on attribue a nb la valeur max
                        $nb = $res->nbResults > $max_return ? $max_return : $res->nbResults;
                        // On va chercher le contenu des notices au format pmb_xml_unimarc
                        $notices = $ws->pmbesSearch_fetchSearchRecords($res->searchId, 0, $nb, "pmb_xml_unimarc", "utf-8", true, $items);

                        for ($i = 0; $i < $nb; $i ++) {
                            $this->rec_records($notices[$i], $source_id, $search_id);
                        }
                    }
                    break;
            }

        } catch (Exception $e) {}
    }

    public function rec_records($notice_xml_uni, $source_id, $search_id)
    {
        global $charset, $base_path;

        if ($notice_xml_uni && $this->current_protocole) {
            // l'instanciation est differente selon les protocoles
            if ($this->current_protocole == JSONRPC) {
                $rec_uni_dom = new xml_dom_pmb($notice_xml_uni["noticeContent"], "utf-8");
            } else {
                $rec_uni_dom = new xml_dom_pmb($notice_xml_uni->noticeContent, "iso-8859-1");
            }
            $the_notice = $rec_uni_dom->get_nodes("notice");
            $add_explnum = true; // Si on a deja les documents numeriques via l'export pas la peine de les ajouter...
            if (is_countable($the_notice) && count($the_notice) && is_array($the_notice[0]["CHILDS"])) {
                foreach ($the_notice[0]["CHILDS"] as $value) {
                    if ((isset($value["NAME"]) && $value["NAME"] == "f") && is_array($value["ATTRIBS"]) && ($value["ATTRIBS"]["c"] == "897")) {
                        $add_explnum = false;
                    }
                }
            }
            if (($add_explnum) && ! empty($notice_xml_uni["noticeExplNums"]) && is_array($notice_xml_uni["noticeExplNums"]) && count($notice_xml_uni["noticeExplNums"])) {

                $i = is_countable($the_notice[0]['CHILDS']) ? count($the_notice[0]['CHILDS']) : 0;

                foreach ($notice_xml_uni["noticeExplNums"] as $k => $docnum) {

                    $the_notice[0]['CHILDS'][$i] = array(
                        'NAME' => 'f',
                        'ATTRIBS' => array(
                            'c' => '897'
                        ),
                        'TYPE' => '1',
                        'CHILDS' => array(
                            0 => array(
                                'NAME' => 's',
                                'ATTRIBS' => array(
                                    'c' => 'a'
                                ),
                                'TYPE' => '1',
                                'CHILDS' => array(
                                    0 => array(
                                        'DATA' => $docnum['url'],
                                        'TYPE' => '2'
                                    )
                                )
                            ),
                            1 => array(
                                'NAME' => 's',
                                'ATTRIBS' => array(
                                    'c' => 'b'
                                ),
                                'TYPE' => '1',
                                'CHILDS' => array(
                                    0 => array(
                                        'DATA' => $docnum['name'],
                                        'TYPE' => '2'
                                    )
                                )
                            ),
                            2 => array(
                                'NAME' => 's',
                                'ATTRIBS' => array(
                                    'c' => 'f'
                                ),
                                'TYPE' => '1',
                                'CHILDS' => array(
                                    0 => array(
                                        'DATA' => $docnum['filename'],
                                        'TYPE' => '2'
                                    )
                                )
                            )
                        )
                    );
                    $i ++;
                }
            }

            if ($the_notice) {
                foreach ($the_notice as $anotice) {
                    $this->rec_record($rec_uni_dom, $anotice, $source_id, $search_id);
                }
            }
        }
    }

    public function rec_record($rec_uni_dom, $notice, $source_id, $search_id)
    {
        global $charset, $base_path;

        $date_import = date("Y-m-d H:i:s", time());

        // Initialisation
        $ref = "";
        $ufield = "";
        $usubfield = "";
        $field_order = 0;
        $subfield_order = 0;
        $value = "";

        $fs = $rec_uni_dom->get_nodes("f", $notice);
        // Recherche du 001
        for ($i = 0; $i < count($fs); $i ++) {
            if ($fs[$i]["ATTRIBS"]["c"] == "001") {
                $ref = $rec_uni_dom->get_datas($fs[$i]);
                break;
            }
        }

        // Mise a jour
        if ($ref) {
            // Si conservation des anciennes notices, on regarde si elle existe
            if (! $this->del_old) {
                $ref_exists = $this->has_ref($source_id, $ref, $search_id);
            }
            // Si pas de conservation des anciennes notices, on supprime
            if ($this->del_old) {
                $this->delete_from_entrepot($source_id, $ref, $search_id);
                $this->delete_from_external_count($source_id, $ref);
            }
            // Si pas de conservation ou reference inexistante
            if (($this->del_old) || ((! $this->del_old) && (! $ref_exists))) {
                // Insertion de l'entête
                $n_header = [];
                $n_header["rs"] = $rec_uni_dom->get_value("notice/rs");
                $n_header["ru"] = $rec_uni_dom->get_value("notice/ru");
                $n_header["el"] = $rec_uni_dom->get_value("notice/el");
                $n_header["bl"] = $rec_uni_dom->get_value("notice/bl");
                $n_header["hl"] = $rec_uni_dom->get_value("notice/hl");
                $n_header["dt"] = $rec_uni_dom->get_value("notice/dt");

                // Recuperation d'un ID
                $recid = $this->is_into_external_count($source_id, $ref);
                if (! $recid) {
                    $recid = $this->insert_into_external_count($source_id, $ref);
                }

                foreach ($n_header as $hc => $code) {
                    $this->insert_header_into_entrepot($source_id, $ref, $date_import, $hc, $code, $recid, $search_id);
                }
                for ($i = 0; $i < count($fs); $i ++) {
                    $ufield = $fs[$i]["ATTRIBS"]["c"];
                    $field_order = $i;
                    $ss = $rec_uni_dom->get_nodes("s", $fs[$i]);

                    if (is_array($ss)) {
                        for ($j = 0; $j < count($ss); $j ++) {
                            $usubfield = $ss[$j]["ATTRIBS"]["c"];
                            $value = $rec_uni_dom->get_datas($ss[$j]);
                            if ($charset != "utf-8") {
                                if (function_exists("mb_convert_encoding")) {
                                    $value = mb_convert_encoding($value, "Windows-1252", "UTF-8");
                                } else {
                                    $value = encoding_normalize::utf8_decode($value);
                                }
                            }
                            $subfield_order = $j;
                            $this->insert_content_into_entrepot($source_id, $ref, $date_import, $ufield, $usubfield, $field_order, $subfield_order, $value, $recid, $search_id);
                        }
                    } else {
                        $value = $rec_uni_dom->get_datas($fs[$i]);
                        if ($charset != "utf-8") {
                            if (function_exists("mb_convert_encoding")) {
                                $value = mb_convert_encoding($value, "Windows-1252", "UTF-8");
                            } else {
                                $value = encoding_normalize::utf8_decode($value);
                            }
                        }
                        $this->insert_content_into_entrepot($source_id, $ref, $date_import, $ufield, $usubfield, $field_order, $subfield_order, $value, $recid, $search_id);
                    }
                }
                $this->insert_origine_into_entrepot($source_id, $ref, $date_import, $recid, $search_id);
                $this->rec_isbd_record($source_id, $ref, $recid);
            }
        }
    }

    public function make_serialized_source_properties($source_id)
    {
        global $url, $response_group, $search_index, $max_return, $protocole, $authentification, $display_items;
        global $auth_login, $auth_password, $auth_connexion_phrase;

        $t = array();
        $t["url"] = stripslashes($url);
        $t["protocole"] = $protocole;
        $t["response_group"] = $response_group;
        $t["search_index"] = $search_index;
        $t["max_return"] = $max_return;
        $t["authentification"] = $authentification;
        $t["auth_login"] = $auth_login;
        $t["auth_password"] = $auth_password;
        $t["auth_connexion_phrase"] = stripslashes($auth_connexion_phrase);
        $t["display_items"] = $display_items;
        $this->sources[$source_id]["PARAMETERS"] = serialize($t);
    }

    /**
     * Recupere la liste des periodiques sous forme de tableau
     *
     * @param int $source_id : id Source
     * @param int $opac_user_id : id lecteur
     */
    public function fetch_serial_list($source_id, $opac_user_id = - 1)
    {
        global $charset;
        global $pmb_curl_proxy;
        global $base_path;

        $url = '';
        $protocole = JSONRPC;

        $params = $this->get_source_params($source_id);
        $this->fetch_global_properties();
        if ($params["PARAMETERS"]) {
            $vars = unserialize($params["PARAMETERS"]);
            foreach ($vars as $key => $val) {
                global ${$key};
                ${$key} = $val;
            }
        }

        $this->current_protocole = $protocole;

        $ret = array();
        $ws = null;

        try {

            switch ($this->current_protocole) {

                case JSONRPC:
                    $ws = new jsonRPCClient($url);
                    $ws->setUser($vars['auth_login'] ?? '');
                    $ws->setPwd($vars['auth_password'] ?? '');
                    $ws->setSalt($vars['auth_connexion_phrase'] ?? '');
                    break;

                case SOAP:
                    $ws = new SoapClient($url . "&wsdl");
                    break;
            }
            if ($ws) {
                $res = $ws->pmbesNotices_fetchSerialList($opac_user_id);
            }
            $ret = $this->object_to_array($res);

        } catch (Exception $e) {}

        return $ret;
    }

    /**
     * Recupere une liste de notices
     *
     * @param int $source_id
     * @param array $ids
     * @param string $rec_format
     * @param string $rec_charset
     * @param boolean $w_links
     * @param boolean $record
     * @return array
     */
    public function fetch_notice_list_full($source_id, $ids = array(), $rec_format = 'raw_array_assoc', $rec_charset = 'utf-8', $w_links = false, $record = false)
    {
        global $charset;
        global $pmb_curl_proxy;
        global $base_path;

        $protocole = JSONRPC;
        $url = '';

        $params = $this->get_source_params($source_id);
        $this->fetch_global_properties();
        if ($params["PARAMETERS"]) {
            $vars = unserialize($params["PARAMETERS"]);
            foreach ($vars as $key => $val) {
                global ${$key};
                ${$key} = $val;
            }
        }

        $this->current_protocole = $protocole;

        $ret = [];
        $res = [];

        try {

            switch ($this->current_protocole) {

                case JSONRPC:
                    $ws = new jsonRPCClient($url);
                    $ws->setUser($vars['auth_login'] ?? '');
                    $ws->setPwd($vars['auth_password']) ?? '';
                    $ws->setSalt($vars['auth_connexion_phrase'] ?? '');
                    break;

                case SOAP:
                    $ws = new SoapClient($url . "&wsdl");
                    break;
            }
            if ($ws) {
                $res = $ws->pmbesNotices_fetchNoticeListFull($ids, $rec_format, $rec_charset, $w_links);
            }
            $ret = $this->object_to_array($res);

        } catch (Exception $e) {
            return $ret;
        }
        if ($record && is_array($ret)) {
            foreach ($ret as $notice) {
                $this->rec_records($notice, $source_id, $search_id);
            }
        }
        return $ret;
    }

    /**
     * Recupere une liste de bulletins
     *
     * @param int $source_id
     * @param array $ids
     * @param string $rec_format
     * @param string $rec_charset
     * @return array
     */
    public function fetch_bulletin_list_full($source_id, $ids = array(), $rec_format = 'raw_array_assoc', $rec_charset = 'utf-8')
    {
        global $charset;
        global $pmb_curl_proxy;
        global $base_path;

        $protocole = JSONRPC;
        $url = '';

        $params = $this->get_source_params($source_id);
        $this->fetch_global_properties();
        if ($params["PARAMETERS"]) {
            $vars = unserialize($params["PARAMETERS"]);
            foreach ($vars as $key => $val) {
                global ${$key};
                ${$key} = $val;
            }
        }

        $this->current_protocole = $protocole;

        $ret = [];
        $res = [];
        try {

            switch ($this->current_protocole) {

                case JSONRPC:
                    $ws = new jsonRPCClient($url);
                    $ws->setUser($vars['auth_login'] ?? '');
                    $ws->setPwd($vars['auth_password'] ?? '');
                    $ws->setSalt($vars['auth_connexion_phrase'] ?? '');
                    break;

                case SOAP:
                    $ws = new SoapClient($url . "&wsdl");
                    break;
            }
            if ($ws) {
                $res = $ws->pmbesNotices_fetchBulletinListFull($ids, $rec_format, $rec_charset);
            }
            $ret = $this->object_to_array($res);

        } catch (Exception $e) {
            return $ret;
        }
        return $ret;
    }

    public function object_to_array($obj)
    {
        if (is_object($obj)) {
            $obj = (array) $obj;
        }
        if (is_array($obj) && count($obj)) {
            foreach ($obj as $k => $v) {
                $obj[$k] = $this->object_to_array($v);
            }
        }
        return $obj;
    }

}