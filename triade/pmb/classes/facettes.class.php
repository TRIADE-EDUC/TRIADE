<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes.class.php,v 1.24.2.2.2.1 2025/02/11 09:20:04 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

global $class_path;
require_once ($class_path . "/acces.class.php");
require_once ($class_path . "/facettes_root.class.php");
require_once ($class_path . "/notice.class.php");

class facettes extends facettes_root
{

    /**
     * Nom de la table bdd
     *
     * @var string
     */
    public static $table_name = 'facettes';

    /**
     * Mode d'affichage (extended/external)
     *
     * @var string
     */
    public $mode = 'extended';
    
    /**
     * Methode d'affinage (filter / search)
     */
    public static $refining_method = 'filter';

    /**
     * Nom de la classe de comparaison
     */
    protected static $compare_class_name = 'facette_search_compare';

    public function __construct($objects_ids = '')
    {
        parent::__construct($objects_ids);
    }

    protected function get_query_by_facette($id_critere, $id_ss_critere)
    {
        global $lang;

        $id_critere = intval($id_critere);
        $id_ss_critere = intval($id_ss_critere);
        if (empty(static::$facet_type) || static::$facet_type == 'notices') {
            $plural_prefix = 'notices';
            $prefix = 'notice';
        } else {
            $plural_prefix = 'authorities';
            $prefix = 'authority';
        }
        $query = 'select value ,count(distinct id_'.$prefix.') as nb_result from (SELECT value,id_'.$prefix.' FROM '.$plural_prefix.'_fields_global_index'.
                    gen_where_in($plural_prefix.'_fields_global_index.id_'.$prefix, $this->objects_ids).'
					AND code_champ = '.$id_critere.'
					AND code_ss_champ = '.$id_ss_critere.'
					AND lang in ("","'.$lang.'","'.substr($lang, 0, 2).'")) as sub
					GROUP BY value
					ORDER BY ';
        return $query;
    }

    public static function make_facette_search_env()
    {
        global $search;

        //Destruction des globales avant reconstruction
        static::destroy_global_env(false); // false = sans destruction de la variable de session
        
        //creation des globales => parametres de recherche
        if(empty($search)) {
            $search = array();
        }
        $nb_search = count($search);
        if (!empty($_SESSION['facette'])) {
            for ($i=0;$i<count($_SESSION['facette']);$i++) {
                $search[] = "s_3";
                $field = "field_".($i+$nb_search)."_s_3";
                $field_=array();
                $field_ = $_SESSION['facette'][$i];
                global ${$field};
                ${$field} = $field_;
                
                $op = "op_".($i+$nb_search)."_s_3";
                $op_ = "EQ";
                global ${$op};
                ${$op}=$op_;
                
                $inter = "inter_".($i+$nb_search)."_s_3";
                $inter_ = "and";
                global ${$inter};
                ${$inter} = $inter_;
            }
        }
    }

    public static function destroy_global_env($with_session=true)
    {
        global $search;
        if(is_array($search) && count($search)){
            $nb_search = count($search);
        }else{
            $nb_search = 0;
        }
        for ($i=$nb_search; $i>=0; $i--) {
            if(!empty($search[$i]) && $search[$i] == 's_3') {
                static::destroy_global_search_element($i);
            }
        }
        if($with_session) {
            unset($_SESSION['facette']);
        }
    }
    
    protected static function get_link_delete_clicked($indice, $facettes_nb_applied)
    {
        if ($facettes_nb_applied==1) {
            $link = "facettes_reinit();";
        } else {
            $link = "facettes_delete_facette(".$indice.");";
        }
        return $link;
    }

    protected static function get_link_reinit_facettes()
    {
        $link = "facettes_reinit();";
        return $link;
    }

    protected static function get_link_back($reinit_compare = false)
    {
        if($reinit_compare) {
            $link = "facettes_reinit_compare();";
        } else {
            $link = "document.".static::$hidden_form_name.".submit();";
        }
        return $link;
    }

    public static function get_session_values()
    {
        if (!isset($_SESSION['facette'])) {
            $_SESSION['facette'] = static::get_session_default_values();
        }
        return $_SESSION['facette'];
    }

    public static function set_session_values($session_values)
    {
        $_SESSION['facette'] = $session_values;
    }

    public static function delete_session_value($param_delete_facette)
    {
        if (isset($_SESSION['facette'][$param_delete_facette])) {
            unset($_SESSION['facette'][$param_delete_facette]);
            $_SESSION['facette'] = array_values($_SESSION['facette']);
        }
    }

    public static function get_session_default_values() {
        $facet_type = static::get_facet_type();
        if(!isset($_SESSION['facette_default'][$facet_type])) {
            $_SESSION['facette_default'][$facet_type] = [];
        }
        return $_SESSION['facette_default'][$facet_type];
    }
    
    public static function set_session_default_values($session_default_values=[]) {
        if (empty($session_default_values)) {
            $session_default_values = static::get_session_values();
        }
        $_SESSION['facette_default'][static::get_facet_type()] = $session_default_values;
        
        $admin_session = new admin_session();
        $admin_session->save_property('facette_default');
    }
    
    public static function delete_session_default_values() {
        unset($_SESSION['facette_default'][static::get_facet_type()]);
        
        $admin_session = new admin_session();
        $admin_session->save_property('facette_default');
    }
    
    public static function expl_voisin($id_notice = 0)
    {
        global $charset, $msg;
        $data = array();
        $notices_list = facettes::get_expl_voisin($id_notice);
        $display = static::aff_notices_list($notices_list);
        $data['aff'] = "";
        if ($display) {
            $data['aff'] = "<h3 class='avis_detail'>" . $msg['expl_voisin_search'] . "</h3>" . $display;
        }
        if ($charset != "utf-8") {
            $data['aff'] = encoding_normalize::utf8_normalize($data['aff']);
        }
        $data['id'] = $id_notice;
        return $data;
    }

    /**
     * Utilise dans la fonction facettes::expl_voisin.
     * Permet d'aller chercher les notices du meme rayon en fonction des exemplaires d'une notice donnee.
     *
     * @param number $id_notice
     * @return number[]
     */
    public static function get_expl_voisin($id_notice = 0)
    {
        global $opac_nb_notices_similaires;

        $id_notice = intval($id_notice);
        $notice_list = array();
        $req = "select expl_cote, expl_section from exemplaires where expl_notice=$id_notice";
        $res = pmb_mysql_query($req);

        $nb_result = $opac_nb_notices_similaires;
        if ($nb_result > 6 || $nb_result < 0 || ! (isset($opac_nb_notices_similaires))) {
            $nb_result = 6;
        }
        $nb_asc = "";
        $nb_desc = "";
        if (($nb_result % 2) == 0) {
            $nb_asc = $nb_result / 2;
            $nb_desc = $nb_asc;
        } else {
            $nb_desc = $nb_result % 2;
            $nb_asc = $nb_result - $nb_desc;
        }

        if ($res && pmb_mysql_num_rows($res)) {
            $r = pmb_mysql_fetch_object($res);
            $cote = $r->expl_cote;
            $section = $r->expl_section;
            $query = "
			(select distinct expl_notice from exemplaires where expl_notice!=0 and expl_cote >= '" . $cote . "' and expl_section = '" . $section . "' and expl_notice!=$id_notice order by expl_cote asc limit " . $nb_asc . ")
				union 
			(select distinct expl_notice from exemplaires where expl_notice!=0 and expl_cote < '" . $cote . "' and expl_section = '" . $section . "' and expl_notice!=$id_notice  order by expl_cote desc limit " . $nb_desc . ")";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result) > 0) {
                while ($row = pmb_mysql_fetch_object($result)) {
                    $notice_list[] = $row->expl_notice;
                }
            }
        }
        return $notice_list;
    }

    /**
     * Permet d'affichier dans le detail d'une notice, les notices que l'emprunteur pourrais aimer
     * Conditionner sur le parametre: opac_notices_format (ça valeur doit-etre 1 ou 5)
     * Conditionner sur le parametre: opac_allow_simili_search (ça valeur doit-etre 1 ou 3)
     *
     * @param integer $id_notice
     * @return string[]
     */
    public static function similitude($id_notice = 0)
    {
        global $charset, $msg;
        $data = array();
        $notices_list = facettes::get_similitude_notice($id_notice);
        $display = static::aff_notices_list($notices_list);
        $data['aff'] = "";
        if ($display) {
            $data['aff'] = "<h3 class='avis_detail'>" . $msg['simili_search'] . "</h3>" . $display;
        }
        if ($charset != "utf-8") {
            $data['aff'] = encoding_normalize::utf8_normalize($data['aff']);
        }
        $data['id'] = $id_notice;
        return $data;
    }

    /**
     * Utilise dans la fonction facettes::similitude.
     * Permet d'aller chercher des notices que l'emprunteur pourrais aimer en fonction d'une notice donnee.
     *
     * @param number $id_notice
     * @return number[]
     */
    public static function get_similitude_notice($id_notice = 0)
    {
        global $opac_nb_notices_similaires;
        global $gestion_acces_active, $gestion_acces_empr_notice;

        $id_notice = intval($id_notice);
        $req = "select distinct code_champ, code_ss_champ, num_word from notices_mots_global_index where	(
				code_champ in(1,17,19,20,25) 
 			)and
			id_notice=$id_notice";
        /*
         * 27,28,29
         * or (code_champ=90 and code_ss_champ=2)
         * or (code_champ=90 and code_ss_champ=3)
         * or (code_champ=90 and code_ss_champ=4)
         */
        // 7337 43421

        $res = pmb_mysql_query($req);
        $where_mots = "";
        $notice_list = array();
        if ($res && pmb_mysql_num_rows($res)) {
            while ($r = pmb_mysql_fetch_object($res)) {
                if ($where_mots) {
                    $where_mots .= " or ";
                }
                $where_mots .= "(code_champ =" . $r->code_champ . " AND code_ss_champ =" . $r->code_ss_champ . " AND num_word =" . $r->num_word . " and id_notice != " . $id_notice . ")";
            }
        }
        if ($where_mots) {
            if ($gestion_acces_active == 1 && $gestion_acces_empr_notice == 1) {
                $ac = new acces();
                $dom_2 = $ac->setDomain(2);
            }
            $nb_result = $opac_nb_notices_similaires;
            if ($nb_result > 6 || $nb_result < 0 || ! (isset($opac_nb_notices_similaires))) {
                $nb_result = 6;
            }
            $req = "select id_notice, sum(pond) as s from notices_mots_global_index where $where_mots group by id_notice order by s desc limit " . $nb_result;
            $res = pmb_mysql_query($req);
            if ($res && pmb_mysql_num_rows($res)) {
                while ($r = pmb_mysql_fetch_object($res)) {
                    if ($r->s > 80) {
                        $acces_v = true;
                        if ($gestion_acces_active == 1 && $gestion_acces_empr_notice == 1) {
                            $acces_v = $dom_2->getRights($_SESSION['id_empr_session'], $r->id_notice, 4);
                        } else {
                            $requete = "SELECT notice_visible_opac, expl_visible_opac, notice_visible_opac_abon, expl_visible_opac_abon, explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='" . $r->id_notice . "' and id_notice_statut=statut ";
                            $myQuery = pmb_mysql_query($requete);
                            if ($myQuery && pmb_mysql_num_rows($myQuery)) {
                                $statut_temp = pmb_mysql_fetch_object($myQuery);
                                if (! $statut_temp->notice_visible_opac) {
                                    $acces_v = false;
                                }
                                if ($statut_temp->notice_visible_opac_abon && ! $_SESSION['id_empr_session']) {
                                    $acces_v = false;
                                }
                            } else {
                                $acces_v = false;
                            }
                        }
                        if ($acces_v) {
                            $notice_list[] = $r->id_notice;
                        }
                    }
                }
            }
        }
        return $notice_list;
    }

    protected static function aff_notices_list($notices_list)
    {
        global $charset;
        global $opac_show_book_pics, $opac_book_pics_url, $opac_book_pics_msg, $opac_url_base;
        global $opac_notice_affichage_class, $gestion_acces_active, $gestion_acces_empr_notice;
        global $opac_notice_reduit_format_similaire;

        $img_list = "";

        if ($gestion_acces_active == 1 && $gestion_acces_empr_notice == 1) {
            $ac = new acces();
            $dom_2 = $ac->setDomain(2);
        }
        $i = 0;
        $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
        foreach ($notices_list as $notice_id) {
            $acces_v = true;
            if ($gestion_acces_active == 1 && $gestion_acces_empr_notice == 1) {
                $acces_v = $dom_2->getRights($_SESSION['id_empr_session'], $notice_id, 4);
            } else {
                $requete = "SELECT notice_visible_opac, expl_visible_opac, notice_visible_opac_abon, expl_visible_opac_abon, explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='" . $notice_id . "' and id_notice_statut=statut ";
                $myQuery = pmb_mysql_query($requete);
                if ($myQuery && pmb_mysql_num_rows($myQuery)) {
                    $statut_temp = pmb_mysql_fetch_object($myQuery);
                    if (!$statut_temp->notice_visible_opac) {
                        $acces_v = false;
                    }
                    if ($statut_temp->notice_visible_opac_abon && ! $_SESSION['id_empr_session']) {
                        $acces_v = false;
                    }
                } else {
                    $acces_v = false;
                }
            }
            if (!$acces_v) {
                continue;
            }

            $req = "select * from notices where notice_id=$notice_id";
            $res = pmb_mysql_query($req);
            $image = "";
            if ($r = pmb_mysql_fetch_object($res)) {
                if (substr($opac_notice_reduit_format_similaire, 0, 1) != "H" && $opac_show_book_pics == '1') {
                    $image = "<a href='" . $opac_url_base . "index.php?lvl=notice_display&id=" . $notice_id . "'>" . "<img class='vignetteimg_simili' src='" . notice::get_picture_url_no_image($r->niveau_biblio, $r->typdoc) . "' /></a>";
                    $url_image_ok = $thumbnailSourcesHandler->generateUrl(TYPE_NOTICE, $notice_id);
                    if ($r->thumbnail_url) {
                        $title_image_ok = "";
                        $image = "<a href='" . $opac_url_base . "index.php?lvl=notice_display&id=" . $notice_id . "'>" . "<img class='vignetteimg_simili' src='" . $url_image_ok . "' title=\"" . $title_image_ok . "\" />" . "</a>";
                    } elseif ($r->code && $opac_book_pics_url) {
                        $title_image_ok = htmlentities($opac_book_pics_msg, ENT_QUOTES, $charset);
                        $image = "<a href='" . $opac_url_base . "index.php?lvl=notice_display&id=" . $notice_id . "'>" . "<img class='vignetteimg_simili' src='" . $url_image_ok . "' title=\"" . $title_image_ok . "\" />" . "</a>";
                    }
                }
                $notice = new $opac_notice_affichage_class($notice_id, "", 0, 0, 1);
                $notice->do_header_similaire();
                $notice_header = "<a href='" . $opac_url_base . "index.php?lvl=notice_display&id=" . $notice_id . "'>" . $notice->notice_header . "</a>";
                $i ++;
            }

            // affichage du titre et de l'image dans la même cellule
            if ($image != "") {
                $img_list .= "<td class='center'>" . $image . "<br />" . $notice_header . "</td>";
            } else {
                $img_list .= "<td class='center'>" . $notice_header . "</td>";
            }
        }
        if (!$i) {
            return "";
        }
        $display = "<table style='width:100%;table-layout:fixed;' role='presentation'><tr>" . $img_list . "</tr></table>";

        return $display;
    }

    /**
     * Retourne le template de facettes
     * @param string $query
     */
    public static function get_display_list_from_query($query, $type='notices')
    {
        $display = '';
        $objects = '';
        $result = pmb_mysql_query($query);
        if ($result) {
            while ($row = pmb_mysql_fetch_object($result)) {
                if ($objects) {
                    $objects .= ",";
                }
                $objects .= $row->notice_id;
            }
        }
        facettes::set_search_mode('simple_search');
        session::set_value('search', [$type => ['simple_search' => $objects]]);
        $display .= static::call_ajax_facettes();
        // Formulaire "FACTICE" pour l'application du comparateur et du filtre multiple...
        if ($display) {
            $display .= '
			<form name="form_values" style="display:none;" method="post" action="' . static::format_url('lvl=more_results&mode=extended') . '">
				<input type="hidden" name="from_see" value="1" />
				' . facette_search_compare::form_write_facette_compare() . '
			</form>';
        }
        return $display;
    }

    public static function get_formatted_value($id_critere, $id_ss_critere, $value)
    {
        // Aucun formatage nécessaire pour les facettes PMB (non externes).
        return get_msg_to_display($value);
    }
}// end class
