<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: acces.class.php,v 1.42.2.1.2.1 2025/03/25 15:28:42 dbellamy Exp $


if (stristr ($_SERVER['REQUEST_URI'], ".class.php")) {
    die ("no access");
}

global $base_path, $class_path;

if ( !defined ('USER_PRF_TYP')) {
    define ('USER_PRF_TYP', 0); // 0 = profil utilisateur (role)
}
if ( !defined ('RES_PRF_TYP')) {
    define ('RES_PRF_TYP', 1); // 1 = profil ressource
}

require_once ($class_path."/marc_table.class.php");


class acces {

    /* Table catalogue */
    protected static $t_cat = [];

    protected static $t_doms = [];

    public $dom;

    public function __construct()
    {
        if (!count(static::$t_cat)) {
            //Lecture catalog
            $this->parseCatalog();
        }
    }

    /**
     * Lecture fichier catalog
     */
    public function parseCatalog()
    {
        global $base_path;
        global $msg;
        $t_cat = array();
        $cp = new accesParser();
        $t_cat = $cp->run("$base_path/admin/acces/catalog.xml");

        foreach ($t_cat as $v) {
            $mt = $this->parseManifestFile($v['path']);
            $activation_param = $mt['activation_param'];
            global ${$activation_param};
            if (${$activation_param} == '1') {
                static::$t_cat[$v['id']]['id'] = $v['id'];
                static::$t_cat[$v['id']]['path'] = $v['path'];
                static::$t_cat[$v['id']]['comment'] = '';
                if (!empty($msg[$mt['comment']])) {
                    static::$t_cat[$v['id']]['comment'] = $msg[$mt['comment']];
                }
            }
        }
        unset($t_cat);
    }


    /**
     * Lecture des domaines actives
     *
     * @return array
     */
    public function getCatalog()
    {
        return static::$t_cat;
    }


    /**
     * Lecture fichier manifest
     * @param string $path
     * @return array
     */
    public function parseManifestFile($path)
    {
        global $base_path;

        $mp = new accesParser();
        return $mp->run("$base_path/admin/acces/$path/manifest.xml");
    }


    /**
     * Instanciation domaine
     *
     * @param int $dom_id
     * @return mixed
     */
    public function setDomain($dom_id)
    {
        if (empty(static::$t_doms[$dom_id])) {
            static::$t_doms[$dom_id] = new domain(static::$t_cat[$dom_id]['id'], static::$t_cat[$dom_id]['path']);
        }
        $this->dom = static::$t_doms[$dom_id];
        return $this->dom;
    }

    public static function get_instance()
    {
        global $ac;

        if (!$ac) {
            $ac = new acces();
        }
        return $ac;
    }
}


class domain
{

    public $dom = [];

    protected static $store;

    /**
     * instanciation domaine
     *
     * @param int $id
     * @param string $path
     */
    public function __construct($id, $path)
    {
        if (!$id || !$path) {
            return;
        }
        $this->dom['id'] = $id;
        $this->dom['path'] = $path;
        $this->parseDomainFile();
        $this->enableProperties();
        $this->parseMsgFile();
        $this->setDefaultRights();
        //$this->checkTables(); // Commente en OPAC
    }


    /**
     * Lecture du fichier domain
     */
    public function parseDomainFile()
    {
        global $base_path;

        $ap = new accesParser();
        $dom_file = "$base_path/admin/acces/" . $this->dom['path'] . "/domain.xml";
        $this->dom = $this->dom + $ap->run($dom_file);
    }


    /**
     * Activation des proprietes
     */
    protected function enableProperties()
    {
        if (empty($this->dom['enable'])) {
            return;
        }
        $dom_user_properties = explode(',', $this->dom['user']['properties']);
        foreach ($this->dom['enable'] as $k => $v) {

            $callback = explode('|', $v);
            $callback_method = $callback[0];
            $callback_params = explode(',', $callback[1]);
            $enable = false;
            if (method_exists($this, $callback_method)) {
                $enable = call_user_func_array([$this, $callback_method], [$callback_params]);
            }
            if (!$enable) {
                $dom_user_property_key = array_search($k, $dom_user_properties);
                if ($dom_user_property_key !== false) {
                    unset($dom_user_properties[$dom_user_property_key]);
                }
            }
        }
        $this->dom['user']['properties'] = implode(',', $dom_user_properties);
    }


    protected static function check_param($args)
    {
        if (!is_array($args) || (count($args) != 3)) {
            return false;
        }
        $type_param = $args[0];
        $sstype_param = $args[1];
        $valeur_param = $args[2];
        $q = "select id_param from parametres where type_param='" . addslashes($type_param) . "' and sstype_param='" . addslashes($sstype_param) . "' ";
        $q .= "and valeur_param='" . addslashes($valeur_param) . "' limit 1";
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            return true;
        }
        return false;
    }


    /**
     * lecture messages
     */
    public function parseMsgFile()
    {
        global $base_path, $lang;

        $msg_file = $base_path . "/admin/acces/" . $this->dom['path'] . "/messages/" . $lang . ".xml";
        if (!is_readable($msg_file)) {
            $msg_file = $base_path . "/admin/acces/" . $this->dom['path'] . "/messages/fr_FR.xml";
        }
        if (is_readable($msg_file)) {
            $parser = new XMLlist($msg_file);
            $parser->analyser();
            $this->dom['msg'] = $parser->table;
        }
    }


    /**
     * definition des droits par defaut
     */
    public function setDefaultRights()
    {
        $this->dom['default_rights'] = 0;
    }


    /**
     * creation des tables necessaires
     */
    public function checkTables()
    {
        //creation de la table de stockage des droits par profils utilisateurs et par ressources si inexistante
        $q = "CREATE TABLE IF NOT EXISTS acces_res_".$this->dom['id']." (
            res_num int(8) unsigned NOT NULL default 0,
            res_prf_num int(2) unsigned NOT NULL default 0,
            usr_prf_num int(2) unsigned NOT NULL default 0,
            res_rights int(2) NOT NULL default 0,
            res_mask int(2) NOT NULL default 0,
            PRIMARY KEY (res_num, usr_prf_num)
        )";
        pmb_mysql_query($q);

        //creation de la table de surcharge des droits par utilisateur si inexistante
        $q = "CREATE TABLE IF NOT EXISTS acces_usr_".$this->dom['id']." (
            usr_num int(8) unsigned NOT NULL default 0,
            res_prf_num int(2) unsigned NOT NULL default 0,
            usr_rights int(2) NOT NULL default 0,
            updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (usr_num, res_prf_num)
         )";
        pmb_mysql_query($q);
    }


    /**
     * Lecture message general/specifique domaine
     *
     * @param string $msg_code
     */
    public function getComment($msg_code)
    {
        global $msg;
        if (substr($msg_code, 0, 4) == 'msg:') {
            return $msg[substr($msg_code, 4)];
        } else {
            return $this->dom['msg'][$msg_code];
        }
    }


    /**
     * Retourne la liste des proprietes utilisateur accessibles
     * et eventuellement celles utilisees
     *
     * @return array
     */
    public function getUserProperties()
    {
        $t_acc = explode(',', $this->dom['user']['properties']);
        $t_r = array();
        foreach ($t_acc as $v) {
            $t_r[$v]['id'] = $v;
            $p_lib = $this->dom['properties'][$v]['lib'];
            $t_r[$v]['lib'] = $this->getComment($p_lib);
        }
        unset($v);
        return $t_r;
    }


    public function getDisplayUserProperties()
    {
        global $charset;

        $display = '';
        $t = $this->getUserProperties();
        if (count($t)) {
            foreach ($t as $k => $v) {
                $display .= "<div class='row'>";
                $display .= "<input type='checkbox' id='chk_prop[$k]' name='chk_prop[]' value='" . $k . "' />";
                $display .= "&nbsp;<label class='etiquette' for='chk_prop[$k]'>" . htmlentities($v['lib'], ENT_QUOTES, $charset) . "</label>";
                $display .= "</div>";
            }
        }
        return $display;
    }


    /**
     * enregistrement d'un profil utilisateur
     *
     * @param int $prf_id
     * @param string $prf_lib
     * @param string $prf_rule
     * @param string $prf_hrule
     * @param int $prf_used
     * @return int
     */
    public function saveUserProfile($prf_id, $prf_lib, $prf_rule, $prf_hrule, $prf_used)
    {
        if ($prf_id) { // profils deja enregistres, on modifie juste nom et profil utilise
            $q = "update acces_profiles set prf_name='" . addslashes($prf_lib) . "', prf_used='" . $prf_used . "' where prf_id ='" . $prf_id . "' ";
            pmb_mysql_query($q);
            return $prf_id;
        } else { //on regarde si un profil similaire n'existe pas deja
            $q = "select prf_id from acces_profiles where dom_num='" . $this->dom['id'] . "' and prf_type='" . USER_PRF_TYP . "' and prf_rule='" . addslashes($prf_rule) . "' limit 1 ";
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r)) {
                $row = pmb_mysql_fetch_object($r);
                return $row->prf_id;
            } else {
                $q = "insert into acces_profiles set dom_num='".$this->dom['id']."', prf_type='".USER_PRF_TYP."',prf_name='".addslashes($prf_lib)."', prf_rule='".addslashes($prf_rule)."', prf_hrule='".addslashes($prf_hrule)."' ";
                pmb_mysql_query($q);
                $il = pmb_mysql_insert_id();
                $q = "update acces_profiles set prf_used=prf_id where prf_id='" . $il . "' ";
                pmb_mysql_query($q);
                return $il;
            }
        }
    }


    /**
     * enregistrement des profils utilisateurs
     * @param int $prf_id
     * @param string $prf_lib
     * @param string $prf_rule
     * @param string $prf_hrule
     * @param array $prf_used
     * @param array $unused_prf_id
     */
    public function saveUserProfiles($prf_id, $prf_lib, $prf_rule, $prf_hrule, $prf_used = array(), $unused_prf_id = array())
    {
        $t_id = array();
        if (count($prf_id)) {
            foreach ($prf_id as $k => $v) {
                $t_id[] = $this->saveUserProfile($v, $prf_lib[$k], $prf_rule[$k], $prf_hrule[$k], $prf_used[$k]);
            }
            $l_id = implode("','", $t_id);
            $q = "delete from acces_profiles where dom_num='" . $this->dom['id'] . "' and prf_type='" . USER_PRF_TYP . "' and prf_id not in ('" . $l_id . "') ";
            pmb_mysql_query($q);
        }

        //que faire des anciens profils inutilises
        if (count($unused_prf_id)) {
            foreach ($unused_prf_id as $v) {

                //modification dans la table des droits par ressource
                if (empty($prf_used[$v])) {
                    $prf_used[$v] = 0;
                }
                $q = "update acces_res_" . $this->dom['id'] . " set usr_prf_num='" . $prf_used[$v] . "' where usr_prf_num='" . $v . "' ";
                pmb_mysql_query($q);

                //suppression dans la table de droits
                $q = "delete from acces_rights where dom_num='" . $this->dom['id'] . "' and usr_prf_num='" . $v . "' ";
                pmb_mysql_query($q);
            }
        }
    }


    /**
     * lecture des profils utilisateurs
     * @return string
     */
    public function loadUserProfiles()
    {
        $q = "select * from acces_profiles where ";
        $q .= "dom_num = '" . $this->dom['id'] . "' ";
        $q .= "and prf_type='" . USER_PRF_TYP . "' order by prf_name ";
        return $q;
    }


    /**
     * Lecture des profils utilisateurs utiles pour le calcul des droits
     *
     * @param array $except
     * @return string
     */
    public function loadUsedUserProfiles($except = array())
    {
        $q = "select * from acces_profiles where ";
        $q .= "dom_num = '" . $this->dom['id'] . "' ";
        $q .= "and prf_type='" . USER_PRF_TYP . "' ";
        if (count($except)) {
            $q .= "and prf_id not in ('" . implode("','", $except) . "') ";
        }
        $q .= "and prf_id = prf_used ";
        $q .= "order by prf_name ";
        return $q;
    }


    /**
     * suppression des profils utilisateurs
     */
    public function deleteUserProfiles()
    {
        $q = "delete from acces_profiles where prf_type='" . USER_PRF_TYP . "' and dom_num='" . $this->dom['id'] . "' ";
        pmb_mysql_query($q);
    }


    /**
     * Calcul des profils utilisateurs en mode automatique (produit scalaire)
     *
     * @param array $chk_prop
     * @return array|number|string|array|mixed
     */
    public function calcUserProfiles($chk_prop = array())
    {
        global $msg;
        if (!count($chk_prop)) {
            return array();
        }

        //Recuperation proprietes utilisateurs
        $t_p = array();
        $t_pid = explode(',', $this->dom['user']['properties']);

        foreach ($t_pid as $v) {
            if (in_array($v, $chk_prop)) {
                $t_p[$v]['type'] = $this->dom['properties'][$v]['ref']['type'];
                $t_p[$v]['name'] = $this->dom['properties'][$v]['ref']['name'];
                $t_p[$v]['key'] = $this->dom['properties'][$v]['ref']['key'];
                $t_p[$v]['value'] = $this->dom['properties'][$v]['ref']['value'];
                $t_p[$v]['clause'] = $this->dom['properties'][$v]['ref']['clause'];
                $t_p[$v]['class'] = $this->dom['properties'][$v]['ref']['class'];
                $t_p[$v]['method'] = $this->dom['properties'][$v]['ref']['method'];
            }
        }
        unset($v);

        //Recuperation des valeurs possibles pour chaque propriete
        $t_kv = array();
        foreach ($t_p as $k => $v) {
            switch ($v['type']) {
                case 'table':
                    $q = "select " . $v['key'] . ", " . $v['value'] . " from " . $v['name'] . " " . ($v['clause'] ? "where " . $v['clause'] . " " : "") . "order by 2 ";
                    $r = pmb_mysql_query($q);
                    while (($row = pmb_mysql_fetch_row($r))) {
                        $t_kv[$k][$row[0]] = $row[1];
                    }
                    break;
                case 'marc_table':
                    $t_m = marc_list_collection::get_instance('doctype');
                    $t_kv[$k] = $t_m->table;
                    break;
                case 'callable':
                    $t_kv[$k] = call_user_func_array(array($v["class"], $v["method"]), []);
                    break;
                default:
                    break;
            }
        }
        unset($v);
        $t_k = array_keys($t_kv);
        $t_kv = array_reverse($t_kv, true);

        $nb_tab = count($t_kv);

        $t_r = $this->tab_scal($t_kv);

        $p = current($t_k);
        foreach ($t_r as $k => $v) {
            reset($t_k);
            $p = current($t_k);
            $prev_p = 0;
            $j = 0;
            for ($i = 0; $i < $nb_tab; $i++) {
                $t_r[$k]['rule']['search'][] = 'f_' . $p;
                if ($prev_p == 0) {
                    $t_r[$k]['rule']['inter_' . $prev_p . '_f_' . $p] = '';
                } else {
                    $t_r[$k]['rule']['inter_' . $prev_p . '_f_' . $p] = 'and';
                }
                $t_r[$k]['rule']['op_' . $prev_p . '_f_' . $p] = 'EQ';
                $t_r[$k]['rule']['field_' . $prev_p . '_f_' . $p][] = $v[$j];
                $prev_p = $p;

                if (empty($t_r[$k]['hrule'])) {
                    $t_r[$k]['hrule'] = '';
                }
                if ($t_r[$k]['hrule']) {
                    $t_r[$k]['hrule'] .= "\n" . $msg['search_and'] . " ";
                }
                $t_r[$k]['hrule'] .= $this->getComment($this->dom['properties'][$p]['lib']) . " " . $v[$j + 1];

                if (empty($t_r[$k]['name'])) {
                    $t_r[$k]['name'] = $this->dom['msg']['user_prf_calc_lib'];
                }
                $t_r[$k]['name'] .= " / " . $v[$j + 1];

                $p = next($t_k);
                $j += 2;
            }
        }


        /**
         * comparaison et reprise des profils deja existants
         */
        foreach ($t_r as $k => $v) {
            $t_r[$k]['rule'] = serialize($v['rule']);
            $q = "select prf_id, prf_name, prf_used from acces_profiles where dom_num='" . $this->dom['id'] . "' and prf_type='" . USER_PRF_TYP . "' and prf_rule='" . $t_r[$k]['rule'] . "' limit 1 ";
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r)) {
                $row = pmb_mysql_fetch_object($r);
                $t_r[$k]['name'] = $row->prf_name;
                $t_r[$k]['old'] = $row->prf_id;
            } else {
                $t_r[$k]['old'] = 0;
            }
        }
        //print '<pre>';print_r($t_r);print '</pre>';

        //faut trier
        uasort($t_r, "cmpRules");

        return $t_r;
    }


    public function tab_scal($t_kv)
    {
        $nb_val = 1;
        foreach ($t_kv as $v) {
            $nb_val = $nb_val * count($v);
        }
        unset($v);

        $tr = array();
        $i = 1;
        $t1 = array_pop($t_kv);
        $n1 = $nb_val / count($t1);
        foreach ($t1 as $k1 => $v1) {
            for ($j = 0; $j < $n1; $j++) {
                $tr[$i][] = $k1;
                $tr[$i][] = $v1;
                $i++;
            }
        }
        while (count($t_kv)) {
            $t2 = array_pop($t_kv);
            $tr = $this->tab_scal_2($tr, $t2);
        }
        return $tr;
    }


    public function tab_scal_2($tr, $t2)
    {
        foreach ($tr as $kr => $vr) {
            $tr[$kr][] = key($t2);
            $tr[$kr][] = current($t2);
            if (!next($t2)) {
                reset($t2);
            }
        }
        return $tr;
    }


    /**
     * Retourne la liste des proprietes ressource accessibles
     * et eventuellement celles utilisees
     *
     * @return array
     */
    public function getResourceProperties()
    {
        $t_acc = explode(',', $this->dom['resource']['properties']);
        $t_r = array();
        foreach ($t_acc as $v) {
            $t_r[$v]['id'] = $v;
            $p_lib = $this->dom['properties'][$v]['lib'];
            $t_r[$v]['lib'] = $this->getComment($p_lib);
        }
        unset($v);
        return $t_r;
    }


    public function getDisplayResourceProperties()
    {
        global $charset;

        $display = '';
        $t = $this->getResourceProperties();
        if (count($t)) {
            foreach ($t as $k => $v) {
                $display .= "<div class='row'>";
                $display .= "<input type='checkbox' id='chk_prop[$k]' name='chk_prop[]' value='" . $k . "' />";
                $display .= "&nbsp;<label class='etiquette' for='chk_prop[$k]'>" . htmlentities($v['lib'], ENT_QUOTES, $charset) . "</label>";
                $display .= "</div>";
            }
        }
        return $display;
    }


    /**
     * enregistrement d'un profil ressource
     *
     * @param int $prf_id
     * @param string $prf_lib
     * @param string $prf_rule
     * @param string $prf_hrule
     * @param int $prf_used
     * @return int
     */
    public function saveResourceProfile($prf_id, $prf_lib, $prf_rule, $prf_hrule, $prf_used)
    {
        if ($prf_id) { // profils deja enregistres, on modifie juste nom et profil utilise
            $q = "update acces_profiles set prf_name='" . addslashes($prf_lib) . "', prf_used='" . $prf_used . "' where prf_id ='" . $prf_id . "' ";
            pmb_mysql_query($q);
            return $prf_id;
        } else {
            $q = "insert into acces_profiles set dom_num='".$this->dom['id']."', prf_type='".RES_PRF_TYP."', prf_name='".addslashes($prf_lib)."', prf_rule='".addslashes($prf_rule)."', prf_hrule='".addslashes($prf_hrule)."' ";
            pmb_mysql_query($q);
            $il = pmb_mysql_insert_id();
            $q = "update acces_profiles set prf_used=prf_id where prf_id='" . $il . "' ";
            pmb_mysql_query($q);
            return $il;
        }
    }


    /**
     * enregistrement des profils ressources
     *
     * @param int $prf_id
     * @param string $prf_lib
     * @param string $prf_rule
     * @param string $prf_hrule
     * @param array $prf_used
     * @param array $unused_prf_id
     */
    public function saveResourceProfiles($prf_id, $prf_lib, $prf_rule, $prf_hrule, $prf_used = array(), $unused_prf_id = array())
    {
        $t_id=array();
        if (count($prf_id)) {
            foreach($prf_id as $k=>$v) {
                $t_id[] = $this->saveResourceProfile($v, $prf_lib[$k], $prf_rule[$k], $prf_hrule[$k], $prf_used[$k]);
            }
        }
        $l_id=implode("','", $t_id);
        $q = "delete from acces_profiles where dom_num='".$this->dom['id']."' and prf_type='".RES_PRF_TYP."' and prf_id not in ('".$l_id."') ";
        pmb_mysql_query($q);

        //que faire des anciens profils inutilises
        if (count($unused_prf_id)) {
            foreach ($unused_prf_id as $v) {
                //modification dans la table des droits par ressource
                if (!$prf_used[$v]) {
                    $prf_used[$v] = 0;
                }
                $q = "update acces_res_".$this->dom['id']." set res_prf_num='".$prf_used[$v]."' where res_prf_num='".$v."' ";
                pmb_mysql_query($q);

                //suppression dans la table de droits
                $q = "delete from acces_rights where dom_num='".$this->dom['id']."' and res_prf_num='".$v."' ";
                pmb_mysql_query($q);
            }
        }

    }


    /**
     * Lecture des profils ressources
     *
     * @return string
     */
    public function loadResourceProfiles()
    {
        $q = "select * from acces_profiles where ";
        $q .= "dom_num = '" . $this->dom['id'] . "' ";
        $q .= "and prf_type='" . RES_PRF_TYP . "' order by prf_name ";
        return $q;
    }


    /**
     * Lecture des profils ressources utiles pour le calcul des droits
     *
     * @param array $except
     * @return string
     */
    public function loadUsedResourceProfiles($except = array())
    {
        $q = "select * from acces_profiles where ";
        $q .= "dom_num = '" . $this->dom['id'] . "' ";
        $q .= "and prf_type='" . RES_PRF_TYP . "' ";
        if (count($except)) {
            $q .= "and prf_id not in ('" . implode("','", $except) . "') ";
        }
        $q .= "and prf_id = prf_used ";
        $q .= "order by prf_name ";
        return $q;
    }


    /**
     * suppression des profils ressources
     */
    public function deleteResourceProfiles()
    {
        $q = "delete from acces_profiles where prf_type='" . RES_PRF_TYP . "' and dom_num='" . $this->dom['id'] . "' ";
        pmb_mysql_query($q);
    }


    /**
     * Calcul des profils ressources en mode automatique (produit scalaire)
     *
     * @param array $chk_prop
     * @return array|number|string|array|mixed
     */
    public function calcResourceProfiles($chk_prop = array())
    {
        global $msg;
        if (!count($chk_prop)) {
            return array();
        }

        //Recuperation proprietes utilisateurs
        $t_p = array();
        $t_pid = explode(',', $this->dom['resource']['properties']);

        foreach ($t_pid as $v) {
            if (in_array($v, $chk_prop)) {
                $t_p[$v]['type'] = $this->dom['properties'][$v]['ref']['type'];
                $t_p[$v]['name'] = $this->dom['properties'][$v]['ref']['name'];
                $t_p[$v]['key'] = $this->dom['properties'][$v]['ref']['key'];
                $t_p[$v]['value'] = $this->dom['properties'][$v]['ref']['value'];
                $t_p[$v]['clause'] = $this->dom['properties'][$v]['ref']['clause'];
                $t_p[$v]['class'] = $this->dom['properties'][$v]['ref']['class'];
                $t_p[$v]['method'] = $this->dom['properties'][$v]['ref']['method'];
            }
        }
        unset($v);

        //Recuperation des valeurs possibles pour chaque propriete
        $t_kv = array();
        foreach ($t_p as $k => $v) {
            switch ($v['type']) {
                case 'table':
                    $q = "select " . $v['key'] . ", " . $v['value'] . " from " . $v['name'] . " " . ($v['clause'] ? "where " . $v['clause'] . " " : "") . "order by 2 ";
                    $r = pmb_mysql_query($q);
                    while (($row = pmb_mysql_fetch_row($r))) {
                        $t_kv[$k][$row[0]] = $row[1];
                    }
                    break;
                case 'marc_table':
                    $xf = substr($v['name'], 0, strrpos($v['name'], '.'));
                    $t_m = marc_list_collection::get_instance($xf);
                    $t_kv[$k] = $t_m->table;
                    break;
                case 'callable':
                    $t_kv[$k] = call_user_func_array(array($v["class"], $v["method"]), []);
                    break;
                default:
                    break;
            }
        }
        unset($v);
        $t_k = array_keys($t_kv);
        $t_kv = array_reverse($t_kv, true);

        $nb_tab = count($t_kv);

        $t_r = $this->tab_scal($t_kv);

        $p = current($t_k);
        foreach ($t_r as $k => $v) {
            reset($t_k);
            $p = current($t_k);
            $prev_p = 0;
            $j = 0;
            for ($i = 0; $i < $nb_tab; $i++) {
                $t_r[$k]['rule']['search'][] = 'f_' . $p;
                if ($prev_p == 0) {
                    $t_r[$k]['rule']['inter_' . $prev_p . '_f_' . $p] = '';
                } else {
                    $t_r[$k]['rule']['inter_' . $prev_p . '_f_' . $p] = 'and';
                }
                $t_r[$k]['rule']['op_' . $prev_p . '_f_' . $p] = 'EQ';
                $t_r[$k]['rule']['field_' . $prev_p . '_f_' . $p][] = $v[$j];
                $prev_p = $p;

                if (empty($t_r[$k]['hrule'])) {
                    $t_r[$k]['hrule'] = '';
                }
                if ($t_r[$k]['hrule']) {
                    $t_r[$k]['hrule'] .= "\n" . $msg['search_and'] . " ";
                }
                $t_r[$k]['hrule'] .= $this->getComment($this->dom['properties'][$p]['lib']) . " " . $v[$j + 1];

                if (empty($t_r[$k]['name'])) {
                    $t_r[$k]['name'] = $this->dom['msg']['res_prf_calc_lib'];
                }
                $t_r[$k]['name'] .= " / " . $v[$j + 1];

                $p = next($t_k);
                $j += 2;
            }
        }

        //comparaison et reprise des profils deja existants
        foreach ($t_r as $k => $v) {
            $t_r[$k]['rule'] = serialize($v['rule']);
            $q = "select prf_id, prf_name from acces_profiles where dom_num='" . $this->dom['id'] . "' and prf_type='" . RES_PRF_TYP . "' and prf_rule='" . $t_r[$k]['rule'] . "' limit 1 ";
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r)) {
                $row = pmb_mysql_fetch_object($r);
                $t_r[$k]['name'] = $row->prf_name;
                $t_r[$k]['old'] = $row->prf_id;
            } else {
                $t_r[$k]['old'] = 0;
            }
        }
        //print '<pre>';print_r($t_r);print '</pre>';

        //faut trier
        uasort($t_r, "cmpRules");

        return $t_r;
    }


    /**
     * lecture des elements faisant l'objet d'un controle d'acces sur les ressources
     * si all=0, retourne les controles dependants de l'utilisateur
     * si all=1, retourne les controles independants de l'utilisateur
     * si all=2, retourne tous les controles
     *
     * @param number $all
     * @return NULL[]
     */
    public function getControls($all = 2)
    {
        $t_r = array();
        foreach ($this->dom['controls'] as $k => $v) {

            switch ($all) {
                case 0:
                    if ($v['global'] != 'yes') {
                        $t_r[$k] = $this->getComment($v['lib']);
                    }
                    break;
                case 1:
                    if ($v['global'] == 'yes') {
                        $t_r[$k] = $this->getComment($v['lib']);
                    }
                    break;
                case 2:
                default:
                    $t_r[$k] = $this->getComment($v['lib']);
                    break;
            }
        }
        unset($v);
        return $t_r;
    }


    /**
     * enregistrement des droits du domaine
     *
     * @param array $t_rights
     */
    public function saveDomainRights($t_rights = array())
    {
        //recuperation de la liste des controles
        $t_ctl = $this->getControls(2);
        //Suppression des anciens droits
        $q = "delete from acces_rights where dom_num='" . $this->dom['id'] . "' ";
        pmb_mysql_query($q);

        //recuperation des roles utilisateurs
        $q_usr = $this->loadUsedUserProfiles();
        $r_usr = pmb_mysql_query($q_usr);
        $t_usr = array();
        $t_usr[0] = 0;
        if (pmb_mysql_num_rows($r_usr)) {
            while (($row = pmb_mysql_fetch_object($r_usr))) {
                $t_usr[] = $row->prf_id;
            }
        }

        $q_res = $this->loadUsedResourceProfiles();
        $r_res = pmb_mysql_query($q_res);
        $t_res = array();
        $t_res[0] = 0;
        if (pmb_mysql_num_rows($r_res)) {
            while (($row = pmb_mysql_fetch_object($r_res))) {
                $t_res[] = $row->prf_id;
            }
        }

        //pour chaque profil utilisateur
        foreach ($t_usr as $k_usr => $v_usr) {

            //pour chaque profil ressource
            foreach ($t_res as $k_res => $v_res) {

                //representation decimale des droits utilisateur/ressource
                $b_r = 0;
                if (count($t_ctl)) {
                    foreach ($t_ctl as $k_ctl => $v_ctl) {
                        if (!empty($this->dom['controls'][$k_ctl]['global']) && ($this->dom['controls'][$k_ctl]['global'] == 'yes') && !empty($t_rights[0][0][$k_ctl]) && ($t_rights[0][0][$k_ctl] == 1)) {
                            $b_r = $b_r + pow(2, ($k_ctl - 1));
                        } elseif (!empty($t_rights[$v_usr][$v_res][$k_ctl]) && ($t_rights[$v_usr][$v_res][$k_ctl] == 1)) {
                            $b_r = $b_r + pow(2, ($k_ctl - 1));
                        }
                    }
                }
                $q = "replace into acces_rights set dom_num=" . $this->dom['id'] . ",  usr_prf_num=" . $v_usr . ", res_prf_num=" . $v_res . ", dom_rights=" . $b_r . " ";
                pmb_mysql_query($q);
            }
        }
    }


    /**
     * lecture nombre de ressources a mettre a jour
     *
     * @return number|string
     */
    public function getNbResourcesToUpdate()
    {
        $ret = 0;
        switch ($this->dom['resource']['ref']['type']) {
            case 'table':
                $q = "select count(*) from " . $this->dom['resource']['ref']['name'] . " ";
                $r = pmb_mysql_query($q);
                if (pmb_mysql_num_rows($r)) {
                    $ret = pmb_mysql_result($r, 0, 0);
                }
                break;
            case 'rdf':
                $this->get_store($this->dom['resource']['ref']['name']);
                $query = self::$store->query('
                SELECT ?uri WHERE {
                    ?uri <' . $this->dom['resource']['ref']['key'] . '> <' . $this->dom['resource']['ref']['value'] . '>
                }');
                if ($query) {
                    $ret = self::$store->num_rows();
                }
                break;
            default:
                break;
        }
        return $ret;
    }


    /**
     * generation des droits par profil utilisateur pour chacune des ressources
     *
     * @param number $nb_done
     * @param number $chk_sav_spe_rights
     * @return number|boolean
     */
    public function applyDomainRights($nb_done = 0, $chk_sav_spe_rights = 0)
    {
        $nb_per_pass = 500;

        switch ($this->dom['resource']['ref']['type']) {
            case 'table':
                //lecture de nb_per_pass ressources
                $q0 = "select " . $this->dom['resource']['ref']['key'] . " from " . $this->dom['resource']['ref']['name'] . " limit " . $nb_done . "," . $nb_per_pass;
                $r0 = pmb_mysql_query($q0);

                //pour chaque ressource, definition du profil
                $nb_done += pmb_mysql_num_rows($r0);
                if ($r0) {
                    while (($row0 = pmb_mysql_fetch_row($r0))) {
                        $this->applyRessourceRights($row0[0], $chk_sav_spe_rights);
                    }
                }
                break;
            case 'rdf':
                $this->get_store($this->dom['resource']['ref']['name']);
                $query = self::$store->query('
                SELECT ?uri WHERE {
                    ?uri <' . $this->dom['resource']['ref']['key'] . '> <' . $this->dom['resource']['ref']['value'] . '>
                }');
                if ($query) {
                    if (self::$store->num_rows()) {
                        $result = self::$store->get_result();
                        foreach ($result as $elem) {
                            $this->applyRessourceRights(onto_common_uri::get_id($elem->uri), $chk_sav_spe_rights);
                            $nb_done++;
                        }
                    }
                }
                break;
            default:
                break;
        }
        return $nb_done;
    }


    /**
     * Suppression des droits pour un domaine
     *
     */
    public function deleteDomainRights()
    {
        $this->dom['id'] = intval($this->dom['id']);
        if (!$this->dom['id']) {
            return;
        }
        $table_r = "acces_res_". $this->dom['id'];
        $q = "show tables like '$table_r'";
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            $qr = "delete from acces_res_" . $this->dom['id'] . " ";
            pmb_mysql_query($qr);
        }
        $table_u = "acces_usr_". $this->dom['id'];
        $q = "show tables like '$table_u'";
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            $qu = "delete from acces_usr_" . $this->dom['id'] . " ";
            pmb_mysql_query($qu);
        }
    }


    /**
     * nettoyage
     *
     */
    public function cleanResources()
    {
        $this->dom_['id'] = intval($this->dom['id']);
        if (!$this->dom['id']) {
            return;
        }
        $table = "acces_res_". $this->dom['id'];
        $q = "show tables like '$table'";
        $r = pmb_mysql_query($q);
        if (!pmb_mysql_num_rows($r)) {
            return;
        }
        $q4 = "delete from $table where res_prf_num !=0 and res_prf_num not in (select distinct prf_used from acces_profiles)";
        pmb_mysql_query($q4);
        $q5 = "delete from $table where usr_prf_num != 0 and usr_prf_num not in (select distinct prf_used from acces_profiles)";
        pmb_mysql_query($q5);
        $q6 = "delete from $table where res_num not in (select " . $this->dom['resource']['ref']['key'] . " from " . $this->dom['resource']['ref']['name'] . ") ";
        pmb_mysql_query($q6);
    }


    /**
     * lecture des droits pour la ressource
     *
     * @param number $res_id
     * @return array
     */
    public function getResourceRights($res_id = 0)
    {
        if ($res_id != 0) {
            $res_prf = $this->getResourceProfile($res_id);
            $q = $this->loadUsedUserProfiles();
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r)) {
                $t_usr = array();
                $t_usr[0] = $res_prf;
                while(($row = pmb_mysql_fetch_object($r))) {
                    $t_usr[$row->prf_id] = $res_prf;
                }
                $q = "select usr_prf_num, (res_rights ^ res_mask) from acces_res_" . $this->dom['id'] . " where res_num=" . $res_id;
                $r = pmb_mysql_query($q);
                $t_r = array();
                if (pmb_mysql_num_rows($r)) {
                    while (($row = pmb_mysql_fetch_row($r))){
                        $t_r[$row[0]][$res_prf] = $row[1];
                    }
                    return $t_r;
                }
            }
        }
        return $this->loadDomainRights();
    }


    /**
     * lecture de l'ensemble des droits du domaine
     *
     * @return array [usr_prf_num][res_prf_num]=dom_rights
     */
    public function loadDomainRights()
    {
        $q = "select usr_prf_num, res_prf_num, dom_rights from acces_rights where dom_num=" . $this->dom['id'] . " ";
        $r = pmb_mysql_query($q);
        $t_r = array();
        if (pmb_mysql_num_rows($r)) {
            while (($row = pmb_mysql_fetch_object($r))) {
                $t_r[$row->usr_prf_num][$row->res_prf_num] = $row->dom_rights;
            }
        }
        return $t_r;
    }


    /**
     * stocke les droits des utilisateurs a la creation/modification de la ressource
     *
     * @param number $old_res_id
     * @param number $res_id
     * @param array $res_prf
     * @param array $chk_rights
     * @param array $prf_rad
     * @param array $r_rad
     */
    public function storeUserRights($old_res_id = 0, $res_id = 0, $res_prf = array(), $chk_rights = array(), $prf_rad = array(), $r_rad = array())
    {
        if (!$res_id) {
            die('Error: ressource id not defined');
        }

        if (!$old_res_id) { //creation ressource

            $res_prf = $this->defineResourceProfile($res_id, true);

            //recuperation de la liste des droits du domaine
            $t_r = $this->loadDomainRights();

            //recuperation de la liste des controles
            $t_ctl = $this->getControls(2);

            //recuperation des profils utilisateurs utiles
            $t_usr = array_keys($t_r);

            //pour chaque profil utilisateur
            foreach ($t_usr as $k_usr => $v_usr) {
                $res_rights = $t_r[$v_usr][$res_prf];
                $q = "replace into acces_res_" . $this->dom['id'] . " set res_num=" . $res_id . ", res_prf_num=" . $res_prf . ", ";
                $q .= "usr_prf_num=" . $v_usr . ", res_rights=" . $res_rights . ", res_mask=0 ";
                pmb_mysql_query($q);
            }
        } else { //modification ressource
            if (!is_array($res_prf)) {
                $res_prf = array();
            }
            if (!is_array($res_prf[$this->dom['id']])) {
                $res_prf[$this->dom['id']] = array();
            }
            if (!is_array($chk_rights)) {
                $chk_rights = array();
            }
            if (!is_array($chk_rights[$this->dom['id']])) {
                $chk_rights[$this->dom['id']] = array();
            }
            if ((count($res_prf[$this->dom['id']]) == 0) && (count($chk_rights[$this->dom['id']]) == 0)) {
                $res_prf = $this->defineResourceProfile($res_id, true);
            } elseif (count($res_prf[$this->dom['id']]) != 0) { //Profil ressource fourni
                switch ($prf_rad[$this->dom['id']]) {
                    case 'R': //Profil recalcule
                        $res_prf = $this->defineResourceProfile($res_id, true);
                        break;
                    case 'C': //Profil choisi
                        $res_prf = $res_prf[$this->dom['id']];
                        break;
                    default:
                        break;
                }
            } else if (count($res_prf[$this->dom['id']]) == 0) {
                $res_prf = $this->defineResourceProfile($res_id, true);
            }

            //recuperation de la liste des droits du domaine
            $t_r = $this->loadDomainRights();

            //recuperation de la liste des controles
            $t_ctl = $this->getControls(2);

            //recuperation des profils utilisateurs utiles
            $t_usr = array_keys($t_r);

            //pour chaque profil utilisateur
            foreach ($t_usr as $k_usr => $v_usr) {

                //representation decimale du masque utilisateur/ressource
                $mask = 0;
                if ($r_rad[$this->dom['id']] == 'C' && count($t_ctl)) {
                    foreach ($t_ctl as $k_ctl => $v_ctl) {
                        if (isset($chk_rights[$this->dom['id']][$v_usr]) && isset($chk_rights[$this->dom['id']][$v_usr][$k_ctl]) && $chk_rights[$this->dom['id']][$v_usr][$k_ctl] == 1) {
                            $mask = $mask + pow(2, ($k_ctl - 1));
                        }
                    }
                } else {
                    $mask = $t_r[$v_usr][$res_prf];
                }
                $res_rights = $t_r[$v_usr][$res_prf];
                $q = "replace into acces_res_" . $this->dom['id'] . " set res_num=" . $res_id . ", res_prf_num=" . $res_prf;
                $q .= ",  usr_prf_num=" . $v_usr . ", res_rights=" . $res_rights . ", res_mask=($res_rights ^ $mask) ";
                pmb_mysql_query($q);
            }
        }
    }


    /**
     * retourne le(s) droit(s) precise(s) de l'utilisateur sur la ressource
     * si $rights=0, retourne tous les droits
     *
     * @param int $usr_id
     * @param int $res_id
     * @param number $rights
     * @return string|number
     */
    public function getRights($usr_id, $res_id, $rights = 0)
    {
        $usr_id = intval($usr_id);
        $res_id = intval($res_id);
        $rights = intval($rights);

        $qr = '';
        if ($rights) {
            $qr = "& $rights";
        }

        //recherche de surcharge sur les droits d'acces
        $q = "select (usr_rights " . $qr . ")
            from acces_usr_" . $this->dom['id'] . "
            where usr_num =" . $usr_id . " and res_prf_num = (select distinct res_prf_num from acces_res_" . $this->dom['id'] . " where res_num =" . $res_id . ")";
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            return pmb_mysql_result($r, 0, 0);
        }

        $usr_prf = $this->getUserProfile($usr_id);
        $q = "select ((res_rights ^ res_mask) " . $qr . ") from acces_res_" . $this->dom['id'] . " where res_num=" . $res_id . " and usr_prf_num IN (" . implode(",", $usr_prf) . ")";
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            while ($row = pmb_mysql_fetch_array($r)) {
                if ($row[0]) {
                    return $row[0];
                }
            }
        }
        return 0;
    }


    /**
     * retourne le(s) droit(s) d'un profil utilisateur sur un profil ressource
     *
     * @param number $usr_prf_num
     * @param number $res_prf_num
     * @return string|number
     */
    public function getDomainRights($usr_prf_num = 0, $res_prf_num = 0)
    {
        $q = "select dom_rights from acces_rights where res_prf_num=" . $res_prf_num . " and usr_prf_num=" . $usr_prf_num . " and dom_num=" . $this->dom['id'];
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            return pmb_mysql_result($r, 0, 0);
        }
        return 0;
    }


    /**
     * retourne le(s) droit(s) d'un utilisateur
     *
     * @param int $usr_id
     * @param int $usr_prf
     *
     * @return array [usr_prf_num][res_prf_num]=rights
     */
    public function get_user_rights($usr_id, $usr_prf = [])
    {
        if (empty($usr_prf)) {
            $usr_prf = $this->getUserProfile($usr_id);
        }
        $t_r = array();

        $q = "select res_prf_num, usr_rights from acces_usr_" . $this->dom['id'] . " where usr_num=" . $usr_id . " ";
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            while ($row = pmb_mysql_fetch_object($r)) {
                foreach ($usr_prf as $prf) {
                    $t_r[$prf][$row->res_prf_num] = $row->usr_rights;
                }
            }
        } else {
            $t_d = $this->loadDomainRights();
            foreach ($usr_prf as $prf) {
                if ($t_d[$prf]) {
                    $t_r[$prf] = $t_d[$prf];
                } else {
                    $t_r = $t_d[0];
                }
            }
        }
        return $t_r;
    }


    /**
     * retourne une requete donnant la liste des ressources repondant aux criteres passes
     *
     * @param int $usr_id
     * @param string $rights
     * @return string
     */
    public function getResourceList($usr_id = 0, $rights = '')
    {
        $usr_prf = $this->getUserProfile($usr_id);
        $q = "select res_num from acces_res_" . $this->dom['id'] . " where usr_prf_num IN (" . implode(",", $usr_prf) . ") and ((res_rights ^ res_mask) & " . $rights . ") ";
        return $q;
    }


    /**
     * retourne la jointure a utiliser pour recuperer les ressources correspondant aux criteres passes
     *
     * @param int $usr_id
     * @param int $rights
     * @param string $join_field
     * @return string
     */
    public function getJoin($usr_id, $rights, $join_field)
    {
        $usr_id = intval($usr_id);
        //recherche de surcharge sur les droits d'acces
        $q = "select count(*) from acces_usr_" . $this->dom['id'] . " where usr_num=" . $usr_id . " ";
        $r = pmb_mysql_query($q);

        if (pmb_mysql_result($r, 0, 0)) {
            $j = " join acces_res_" . $this->dom['id'] . " on " . $join_field . "=acces_res_" . $this->dom['id'] . ".res_num and acces_res_" . $this->dom['id'] . ".usr_prf_num=0 ";
            $j .= "join acces_usr_" . $this->dom['id'] . " on acces_res_" . $this->dom['id'] . ".res_prf_num=acces_usr_" . $this->dom['id'] . ".res_prf_num and acces_usr_" . $this->dom['id'] . ".usr_num=" . $usr_id . " ";
            $j .= "and (acces_usr_" . $this->dom['id'] . ".usr_rights & " . $rights . ") ";
        } else {
            $usr_prf = $this->getUserProfile($usr_id);
            $j = " join acces_res_" . $this->dom['id'] . " on " . $join_field . "=acces_res_" . $this->dom['id'] . ".res_num and acces_res_" . $this->dom['id'] . ".usr_prf_num IN (" . implode(",", $usr_prf) . ") and ((acces_res_" . $this->dom['id'] . ".res_rights ^ acces_res_" . $this->dom['id'] . ".res_mask) & " . $rights . ") ";
        }
        return $j;
    }


    public function getFilterQuery($usr_id = 0, $rights = '', $field = '', $ids = '')
    {
        $usr_id = intval($usr_id);
        //recherche de surcharge sur les droits d'acces
        $q = "select count(*) from acces_usr_" . $this->dom['id'] . " where usr_num=" . $usr_id . " ";
        $r = pmb_mysql_query($q);

        if (pmb_mysql_result($r, 0, 0)) {
            $j = "select res_num as " . $field . " from acces_res_" . $this->dom['id'] . " ar ";
            $j .= "join acces_usr_" . $this->dom['id'] . " au on ar.res_prf_num=au.res_prf_num and usr_num=" . $usr_id . " ";
            $j .= "where usr_prf_num=0 and (usr_rights & " . $rights . ") and res_num in (" . $ids . ") ";
        } else {
            $usr_prf = $this->getUserProfile($usr_id);
            $j = $this->getFilterQueryByProfile($usr_prf, $rights, $field, $ids);
        }
        return $j;
    }


    /**
     * Retourne la requete de calcul de droits en fonction d'un profile et des droits a tester
     */
    public function getFilterQueryByProfile($usr_prf = "", $rights = "", $field = "", $ids = "")
    {
        return "select res_num as ".$field." from acces_res_".$this->dom['id']." where usr_prf_num IN (".implode(",", $usr_prf).") and ((res_rights ^ res_mask) & ".$rights.") and res_num in (".$ids.")";
    }


    /**
     * calcul du role utilisateur
     *
     * @param number $usr_id
     * @return number[]|NULL[]
     */
    public function getUserProfile($usr_id = 0)
    {
        $usr_id = intval($usr_id);
        //Recuperation des regles
        $q = $this->loadUserProfiles();
        $r = pmb_mysql_query($q);
        $prf_id = [];

        if (pmb_mysql_num_rows($r)) {
            $t_rules = array();
            $prf_used = array();
            $t_var = array();
            while (($row = pmb_mysql_fetch_object($r))) {
                $t_rules[$row->prf_id] = unserialize($row->prf_rule);
                $prf_used[$row->prf_id] = $row->prf_used;
            }

            //recuperation des variables necessaires au calcul
            foreach ($this->dom['user']['property_link']['r_query'] as $k_var => $v_var) {
                switch ($v_var['type']) {
                    case 'var':
                        $x = $v_var['value'];
                        global ${$x};
                        $t_var[$k_var] = ${$x};
                        break;
                    case 'session':
                        $x = $v_var['value'];
                        $t_var[$k_var] = (isset($_SESSION[$x]) ? $_SESSION[$x] : '');
                        break;
                    case 'field':
                        $t_var[$k_var] = 0;
                        if ($usr_id !== 0) {
                            $q = "select ".$v_var['value']." from ".$this->dom['user']['ref']['name']." where ".$this->dom['user']['ref']['key']."=".$usr_id.($this->dom['user']['ref']['clause'] ? " and ".$this->dom['user']['ref']['clause'] : "" );
                            $r = pmb_mysql_query($q);
                            if (pmb_mysql_num_rows($r)) {
                                $t_var[$k_var] = pmb_mysql_result($r, 0, 0);
                            }
                        }
                        break;
                    case 'sql':
                        $t_var[$k_var] = 0;
                        if ($usr_id !== 0) {
                            $q = str_replace('!!usr_id!!', $usr_id, $v_var['value']);
                            $r = pmb_mysql_query($q);
                            if (pmb_mysql_num_rows($r)) {
                                $t_var[$k_var] = [];
                                while ($row = pmb_mysql_fetch_array($r)) {
                                    $t_var[$k_var][] = $row[0];
                                }
                                //$t_var[$k_var]= pmb_mysql_result($r,0,0);
                            }
                        }
                        break;
                    case 'callable':
                        $t_var[$k_var] = call_user_func_array(array($v_var["class"], $v_var["method"]), [$usr_id]);

                        break;
                    default:
                        break;
                }
            }
            unset($v_var);

            //Quelle est la regle qui s'applique ?
            foreach ($t_rules as $k_rule => $v_rule) {
                $result = true;
                //construction de la regle
                $prev_p_id = 0;
                foreach ($v_rule['search'] as $k_s => $v_s) {
                    $p_id = substr($v_s, 2);
                    $var_value = 0;
                    if (isset($t_var[$p_id])) {
                        $var_value = $t_var[$p_id];
                    }
                    $t_values = $t_rules[$k_rule]['field_' . $prev_p_id . '_f_' . $p_id];
                    if (is_array($var_value)) {
                        $found = false;
                        for ($i = 0; $i < count($var_value); $i++) {
                            if (in_array($var_value[$i], $t_values)) {
                                $found = true;
                                break;
                            }
                        }
                        if ($found == false) {
                            $result = false;
                            break;
                        }
                    } else {
                        if (!in_array($var_value, $t_values)) {
                            $result = false;
                            break;
                        }
                    }
                    $prev_p_id = $p_id;
                }
                if ($result) { //Toutes les conditions sont reunies, on se sauve
                    $prf_id[] = $prf_used[$k_rule];
                }
            }
        }
        if (empty($prf_id)) {
            $prf_id = [0];
        }
        return $prf_id;
    }


    /**
     * Lecture du profil courant ressource
     *
     * Lit le profil courant de la ressource dans la table acces_res_? si existant
     * sinon retourne une valeur calculee (creation)
     *
     */
    public function getResourceProfile($res_id = 0)
    {
        if ($res_id) {
            $q = "select res_prf_num from acces_res_" . $this->dom['id'] . " join acces_profiles on dom_num='" . $this->dom['id'] . "' and prf_type='1' and prf_id=res_prf_num ";
            $q .= "where res_num = " . $res_id . " limit 1";

            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r)) {
                return pmb_mysql_result($r, 0, 0);
            }
        }
        return $this->defineResourceProfile($res_id, false);
    }


    /**
     * lecture du nom du profil ressource
     *
     * @param int $res_prf
     * @return string
     */
    public function getResourceProfileName($res_prf)
    {
        $q = "select prf_name from acces_profiles where prf_id='$res_prf' ";
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            return pmb_mysql_result($r, 0, 0);
        }
        return $this->getComment('res_prf_def_lib');
    }


    /**
     * definition du profil pour la ressource en creation
     *
     * @param number $res_id
     * @param bool $force_c_query : force l'utilisation des criteres de creation
     * @return number|number|NULL
     */
    public function defineResourceProfile($res_id = 0, bool $force_c_query = false)
    {
        //Recuperation des regles
        $q = $this->loadResourceProfiles();
        $r = pmb_mysql_query($q);
        $prf_id = 0;
        if (pmb_mysql_num_rows($r)) {
            $t_rules = array();
            $prf_used = array();
            $t_var = array();
            while (($row = pmb_mysql_fetch_object($r))) {
                $t_rules[$row->prf_id] = unserialize($row->prf_rule);
                $prf_used[$row->prf_id] = $row->prf_used;
            }

            //recuperation des variables necessaires au calcul
            $_query = ($force_c_query) ? 'c_query' : 'i_query';

            foreach ($this->dom['resource']['property_link'][$_query] as $k_var => $v_var) {
                switch ($v_var['type']) {
                    case 'var':
                        $x = $v_var['value'];
                        global ${$x};
                        $t_var[$k_var] = ${$x};
                        break;
                    case 'session':
                        $x = $v_var['value'];
                        $t_var[$k_var] = constant($x);
                        break;
                    case 'field':
                        if ($res_id == 0) {
                            return $prf_id;
                        }
                        $q = "select ".$v_var['value']." from ".$this->dom['resource']['ref']['name']." where ".$this->dom['resource']['ref']['key']."=".$res_id.($this->dom['resource']['ref']['clause'] ? " and ".$this->dom['resource']['ref']['clause'] : "");
                        $r = pmb_mysql_query($q);
                        if (pmb_mysql_num_rows($r)) {
                            $t_var[$k_var] = pmb_mysql_result($r, 0, 0);
                        } else {
                            $t_var[$k_var] = 0;
                        }
                        break;
                    case 'sql':
                        if ($res_id == 0) {
                            return $prf_id;
                        }
                        $q = str_replace('!!res_id!!', $res_id, $v_var['value']);
                        $r = pmb_mysql_query($q);
                        if (pmb_mysql_num_rows($r)) {
                            $t_var[$k_var] = pmb_mysql_result($r, 0, 0);
                        } else {
                            $t_var[$k_var] = 0;
                        }
                        break;
                    case 'callable':
                        if ($res_id == 0) {
                            return $prf_id;
                        }
                        $t_var[$k_var] = call_user_func_array(array($v_var["class"], $v_var["method"]), [$res_id]);
                        break;
                    default:
                        break;
                }
            }
            unset($v_var);

            //Quelle est la regle qui s'applique ?
            $prf_id = 0;
            foreach ($t_rules as $k_rule => $v_rule) {
                $result = TRUE;
                //construction de la regle
                $prev_p_id = 0;
                foreach ($v_rule['search'] as $v_s) {
                    $p_id = substr($v_s, 2);

                    $var_value = $t_var[$p_id];
                    $t_values = $t_rules[$k_rule]['field_' . $prev_p_id . '_f_' . $p_id];

                    if (!in_array($var_value, $t_values)) {
                        $result = FALSE;
                        break;
                    }
                    $prev_p_id = $p_id;
                }
                if ($result == TRUE) { //Toutes les conditions sont reunies, on se sauve
                    $prf_id = $prf_used[$k_rule];
                    break;
                }
            }
        }
        return $prf_id;
    }


    public function delRessource($res_id = 0)
    {
        if (!$res_id) {
            return;
        }
        $q = "delete from acces_res_" . $this->dom['id'] . " where res_num=" . $res_id;
        pmb_mysql_query($q);
    }


    /**
     * generation des droits par profil utilisateur pour une ressource
     *
     * @param int $res_id
     * @param number $chk_sav_spe_rights
     */
    public function applyRessourceRights($res_id, $chk_sav_spe_rights = 0)
    {
        $res_prf = $this->getResourceProfile($res_id);

        //recuperation des droits/profils utilisateurs pour le profil ressource en cours de traitement
        $q1 = "select usr_prf_num, dom_rights from acces_rights where dom_num=" . $this->dom['id'] . " and res_prf_num=" . $res_prf . " ";
        $r1 = pmb_mysql_query($q1);
        $t_d = array();
        if (pmb_mysql_num_rows($r1)) {
            while (($row = pmb_mysql_fetch_object($r1))) {
                $t_d[$row->usr_prf_num] = $row->dom_rights;
            }
        }

        //pour chaque profil utilisateur
        foreach ($t_d as $k_d => $v_d) {
            $q2 = "select res_mask from acces_res_" . $this->dom['id'] . " where res_num=" . $res_id . " and usr_prf_num=" . $k_d;
            $r2 = pmb_mysql_query($q2);
            if (pmb_mysql_num_rows($r2)) {
                $q3 = "update acces_res_" . $this->dom['id'] . " set res_prf_num=" . $res_prf . ", res_rights=" . $v_d;
                if (!$chk_sav_spe_rights) $q3 .= ", res_mask=0";
                $q3 .= " where res_num=" . $res_id . " and usr_prf_num=" . $k_d;
            } else {
                $q3 = "insert into acces_res_" . $this->dom['id'] . " set res_num=" . $res_id . ", res_prf_num=" . $res_prf . ", usr_prf_num=" . $k_d . ", res_rights=" . $v_d . ", res_mask=0";
            }
            pmb_mysql_query($q3);
        }
    }


    /**
     * surcharge des droits utilisateurs
     *
     * @param number $usr_id
     * @param number $override_rights
     * @param array $chk_rights
     */
    public function override_user_rights($usr_id = 0, $override_rights = 0, $chk_rights = array())
    {
        $usr_id = intval($usr_id);
        $override_rights = intval($override_rights);
        $chk_rights = is_array($chk_rights) ? $chk_rights : [];

        if ($usr_id && $override_rights && count($chk_rights)) {
            switch ($override_rights) {

                case '1': //creation surcharge

                    //TODO Il faut pouvoir surcharger
                    if (!empty($chk_rights[$this->dom['id']])) {

                        //recuperation des profils ressources
                        $q_res = $this->loadUsedResourceProfiles();
                        $r_res = pmb_mysql_query($q_res);
                        $t_res = array();
                        $t_res[0] = 0;
                        if (pmb_mysql_num_rows($r_res)) {
                            while (($row = pmb_mysql_fetch_object($r_res))) {
                                $t_res[] = $row->prf_id;
                            }
                        }

                        //recuperation des droits generiques  du domaine pour avoir les droits globaux
                        $t_r = $this->getDomainRights();

                        //recuperation de la liste de tous les controles
                        $t_ctl = $this->getControls(2);

                        //pour chaque profil ressource
                        foreach ($t_res as $k_res => $v_res) {

                            //representation decimale des droits utilisateur/ressource
                            $b_r = 0;
                            if (count($t_ctl)) {
                                foreach ($t_ctl as $k_ctl => $v_ctl) {

                                    if (($this->dom['controls'][$k_ctl]['global'] == 'yes') && ($t_r & $this->dom['controls'][$k_ctl]['id'])) {
                                        $b_r = $b_r + pow(2, ($k_ctl - 1));
                                    } elseif(!empty($chk_rights[$this->dom['id']][$v_res]) && !empty($chk_rights[$this->dom['id']][$v_res][$k_ctl]) && ($chk_rights[$this->dom['id']][$v_res][$k_ctl] == 1)) {
                                        $b_r = $b_r + pow(2, ($k_ctl - 1));
                                    }
                                }
                            }

                            $q = "replace into acces_usr_" . $this->dom['id'] . " set usr_num=" . $usr_id . ",  res_prf_num=" . $v_res . ", usr_rights=" . $b_r . " ";
                            pmb_mysql_query($q);
                        }
                    }

                    break;

                case '2': //suppression surcharge

                    $q = "delete from acces_usr_" . $this->dom['id'] . " where usr_num=" . $usr_id . " ";
                    pmb_mysql_query($q);
                    break;
            }
        }
    }


    protected function get_store($store_name)
    {
        if (empty(self::$store)) {
            $store_config = array(
                /* db */
                'db_name' => DATA_BASE,
                'db_user' => USER_NAME,
                'db_pwd' => USER_PASS,
                'db_host' => SQL_SERVER,
                /* store */
                'store_name' => $this->dom['resource']['ref']['name'],
                /* stop after 100 errors */
                'max_errors' => 100,
                'store_strip_mb_comp_str' => 0
            );
            self::$store = new onto_store_arc2($store_config);
        }
        return self::$store;
    }
}


class accesParser
{
    public $parser;

    public $t = array();

    public $prev_tag = '';

    public $prev_id = '';

    public $path_tag = array();

    public $text = '';


    public function run($file)
    {
        global $charset;

        //Recherche du fichier XML de description
        $subst_file = str_replace('.xml', '_subst.xml', $file);
        if (is_readable($subst_file)) {
            $file = $subst_file;
        }
        $xml = file_get_contents($file, "r") or die(htmlentities("Can't find XML file $file", ENT_QUOTES, $charset));

        unset($this->t);
        $m = [];
        $rx = "/<?xml.*encoding=[\'\"](.*?)[\'\"].*?>/m";
        if (preg_match($rx, $xml, $m)) {
            $encoding = strtoupper($m[1]);
        } else {
            $encoding = "ISO-8859-1";
        }

        $this->parser = xml_parser_create($encoding);
        xml_set_object($this->parser, $this);
        xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, $charset);
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, FALSE);
        xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, TRUE);
        xml_set_element_handler($this->parser, "tagStart", "tagEnd");
        xml_set_character_data_handler($this->parser, "texte");

        if (!xml_parse($this->parser, $xml, TRUE)) {
            die(sprintf("XML error on line: %d", xml_error_string(xml_get_error_code($this->parser)), xml_get_current_line_number($this->parser)));
        }
        xml_parser_free($this->parser);
        unset($this->parser);
        return ($this->t);
    }


    public function tagStart($parser, $tag, $att)
    {
        $this->prev_tag = end($this->path_tag);
        $this->path_tag[] = $tag;
        switch ($tag) {

            //lecture catalog.xml
            case 'catalog':
                break;
            case 'item':
                $this->t[$att['id']]['id'] = $att['id'];
                $this->t[$att['id']]['path'] = $att['path'];
                break;

                //lecture domain.xml
            case 'user':
                $this->t['user']['lib'] = $this->getMsg($att['lib']);
                $this->t['user']['properties'] = $att['properties'];
                break;
            case 'resource':
                $this->t['resource']['lib'] = $this->getMsg($att['lib']);
                $this->t['resource']['properties'] = $att['properties'];
                break;
            case 'ref':
                if ($this->prev_tag == 'resource') {
                    $this->t['resource']['ref']['type'] = $att['type'];
                    $this->t['resource']['ref']['name'] = $att['name'];
                    $this->t['resource']['ref']['key'] = $att['key'];
                    $this->t['resource']['ref']['clause'] = (isset($att['clause']) ? $att['clause'] : '');
                    $this->t['resource']['ref']['value'] = (isset($att['value']) ? $att['value'] : '');
                }
                if ($this->prev_tag == 'user') {
                    $this->t['user']['ref']['type'] = $att['type'];
                    $this->t['user']['ref']['name'] = $att['name'];
                    $this->t['user']['ref']['key'] = $att['key'];
                    $this->t['user']['ref']['clause'] = (isset($att['clause']) ? $att['clause'] : '');
                }
                if ($this->prev_tag == 'property') {
                    $this->t['properties'][$this->prev_id]['ref']['type'] = $att['type'];
                    $this->t['properties'][$this->prev_id]['ref']['name'] = $att['name'];
                    $this->t['properties'][$this->prev_id]['ref']['key'] = (isset($att['key']) ? $att['key'] : '');
                    $this->t['properties'][$this->prev_id]['ref']['value'] = (isset($att['value']) ? $att['value'] : '');
                    $this->t['properties'][$this->prev_id]['ref']['clause'] = (isset($att['clause']) ? $att['clause'] : '');
                    $this->t['properties'][$this->prev_id]['ref']['class'] = (isset($att['class']) ? $att['class'] : '');
                    $this->t['properties'][$this->prev_id]['ref']['method'] = (isset($att['method']) ? $att['method'] : '');
                }
                break;
            case 'property_link':
                $this->prev_id = $att['with'];
                if (!empty($att['enable'])) {
                    $this->t['enable'][$att['with']] = $att['enable'];
                }
                break;
            case 'c_query':
            case 'r_query':
            case 'i_query':
                $tag_2 = $this->path_tag[count($this->path_tag) - 3];
                $this->t[$tag_2][$this->prev_tag][$tag][$this->prev_id]['type'] = $att['type'];
                $this->t[$tag_2][$this->prev_tag][$tag][$this->prev_id]['value'] = (isset($att['value']) ? $att['value'] : '');
                $this->t[$tag_2][$this->prev_tag][$tag][$this->prev_id]['class'] = (isset($att['class']) ? $att['class'] : '');
                $this->t[$tag_2][$this->prev_tag][$tag][$this->prev_id]['method'] = (isset($att['method']) ? $att['method'] : '');
                break;
            case 'properties':
                break;
            case 'property':
                $this->t['properties'][$att['id']]['id'] = $att['id'];
                $this->t['properties'][$att['id']]['lib'] = $att['lib'];
                $this->prev_id = $att['id'];
                break;
            case 'controls':
                break;
            case 'control':
                $this->t['controls'][$att['id']]['id'] = $att['id'];
                $this->t['controls'][$att['id']]['lib'] = $att['lib'];
                $this->t['controls'][$att['id']]['global'] = (isset($att['global']) ? $att['global'] : '');
                $this->prev_id = $att['id'];
                break;
            default:
                break;
        }
        $this->text = '';
    }


    public function tagEnd($parser, $tag)
    {
        if (!count($this->path_tag)) {
            return;
        }
        if (trim($this->text) !== '') {
            $this->t[$tag] = $this->text;
        }
        array_pop($this->path_tag);
    }


    public function texte($parser, $data)
    {
        if (!count($this->path_tag)) {
            return;
        }
        $this->text .= $data;
    }


    public function getMsg($code)
    {
        global $msg;

        if (substr($code, 0, 4) == 'msg:') {
            if (empty($msg[substr($code, 4)])) {
                return '';
            }
            return $msg[substr($code, 4)];
        }
        return $this->t_dom[$code];
    }
}


/**
 * fonction de comparaison des regles pour tri de la liste
 */
function cmpRules($a, $b)
{
    return (strcmp($a['hrule'], $b['hrule']));
}

