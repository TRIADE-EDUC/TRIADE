<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serials.class.php,v 1.278.2.3.2.3 2025/06/03 12:56:37 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

// classes de gestion des périodiques
global $base_path, $class_path, $include_path;

use Pmb\Ark\Models\ArkModel;
use Pmb\Ark\Entities\ArkEntityPmb;

require_once ($class_path . "/notice.class.php");
require_once ($class_path . "/parametres_perso.class.php");
require_once ($include_path . "/notice_authors.inc.php");
require_once ($include_path . "/notice_categories.inc.php");
require_once ($class_path . "/thesaurus.class.php");
require_once ($class_path . "/editor.class.php");
require_once ($class_path . "/mono_display.class.php");
require_once ($class_path . "/acces.class.php");
require_once ("$class_path/sur_location.class.php");
require_once ($class_path . "/abts_modeles.class.php");
require_once ($class_path . "/explnum.class.php");
require_once ($class_path . "/synchro_rdf.class.php");
require_once ($class_path . "/authperso_notice.class.php");
require_once ($class_path . "/index_concept.class.php");
require_once ($class_path . "/map/map_edition_controler.class.php");
require_once ($class_path . "/map_info.class.php");
require_once ($class_path . '/vedette/vedette_composee.class.php');
require_once ($class_path . '/vedette/vedette_link.class.php');
require_once ($class_path . "/tu_notice.class.php");
require_once ($class_path . "/avis_records.class.php");
require_once ($class_path . "/notice_relations.class.php");
require_once ($class_path . "/thumbnail.class.php");
require_once ($base_path . '/admin/convert/export.class.php');
require_once ($class_path . '/audit.class.php');
require_once ($class_path . "/author.class.php");
require_once ($class_path . '/interface/entity/interface_entity_serial_form.class.php');
require_once ($class_path . '/interface/entity/interface_entity_bulletinage_form.class.php');
require_once ($class_path . '/interface/entity/interface_entity_analysis_form.class.php');

/*
 * ------------------------------------------------------------------------------------
 * classe serial : classe de gestion des notices chapeau
 * ---------------------------------------------------------------------------------------
 */
class serial extends notice
{

    // classe de la notice chapeau des périodiques
    public $serial_id = 0;

    // id de ce périodique
    public $biblio_level = 's';

    // niveau bibliographique
    public $hierar_level = '1';

    // niveau hiérarchique
    public $typdoc = '';

    // type UNIMARC du document
    public $opac_visible_bulletinage = 1;

    public $opac_serialcirc_demande = 1;

    public $target_link_on_error = "./catalog.php?categ=serials";

    public $date_parution_perio = null;

    protected static $vedette_composee_config_filename = 'serial_authors';

    // constructeur
    public function __construct($id = 0)
    {
        global $deflt_notice_is_new;
        global $deflt_opac_visible_bulletinage;

        $this->id = intval($id); // Propriété dans la classe notice
        $this->serial_id = intval($id);
        // si id, allez chercher les infos dans la base
        if ($this->id) {
            $this->fetch_serial_data();
        } else {
            $this->is_new = $deflt_notice_is_new;
            $this->opac_visible_bulletinage = $deflt_opac_visible_bulletinage;
        }
    }

    // récupération des infos en base
    public function fetch_serial_data()
    {
        $this->fetch_data();

        // type du document
        $this->typdoc = $this->type_doc;

        $this->date_parution_perio = static::get_date_parution($this->year);
    }

    // fonction de mise à jour ou de création d'un périodique
    public function update($value, $other_fields = "")
    {

        // clean des vieilles nouveautés
        static::cleaning_is_new();

        // formatage des valeurs de $value
        // $value est un tableau contenant les infos du périodique

        if (! $value['tit1'])
            return 0;

        // niveau bib et hierarchique
        $value['niveau_biblio'] = "s";
        $value['niveau_hierar'] = "1";

        // champ d'indexation libre
        if (! empty($value['index_l'])) {
            $value['index_l'] = clean_tags($value['index_l']);
        }

        $values = '';
        foreach ($value as $cle => $valeur) {
            if ($values) {
                $values .= ",$cle='$valeur'";
            } else {
                $values .= "$cle='$valeur'";
            }
        }

        if ($this->id) {
            // modif
            $q = "UPDATE notices SET $values , update_date=sysdate() $other_fields WHERE notice_id=" . $this->id;
            pmb_mysql_query($q);
            audit::insert_modif(AUDIT_NOTICE, $this->id);
        } else {
            // create
            $q = "INSERT INTO notices SET $values , create_date=sysdate(), update_date=sysdate() $other_fields";
            pmb_mysql_query($q);
            $this->id = pmb_mysql_insert_id();
            $this->serial_id = $this->id;
            audit::insert_creation(AUDIT_NOTICE, $this->id);
        }
        // Mise à jour des index de la notice
        notice::majNoticesTotal($this->id);
        return $this->id;
    }

    protected function get_tab_gestion_fields()
    {
        global $msg, $charset;
        global $opac_serialcirc_active;

        $tab_gestion_fields_form = parent::get_tab_gestion_fields();
        $tab_gestion_fields_form .= "
			<div id='el10Child_3' title='" . htmlentities($msg["opac_show_bulletinage"], ENT_QUOTES, $charset) . "' movable='yes'>
				<div id='el10Child_3a' class='row'>
					<input type='checkbox' value='1' id='opac_visible_bulletinage' name='opac_visible_bulletinage'  " . ($this->opac_visible_bulletinage & 0x01 ? "checked='checked'" : '') . " />
					<label for='opac_visible_bulletinage' class='etiquette'>" . $msg["opac_show_bulletinage"] . "</label>
				</div>
				<div id='el10Child_3b' class='row'>
					<input type='checkbox' value='1' id='a2z_opac_show' name='a2z_opac_show'  " . (! ($this->opac_visible_bulletinage & 0x10) ? "checked='checked'" : '') . " />
					<label for='a2z_opac_show' class='etiquette'>" . $msg["a2z_opac_show"] . "</label>
				</div>
			</div>
		";
        if ($opac_serialcirc_active) {
            $tab_gestion_fields_form .= "
				<div id='el10Child_8' title='" . htmlentities($msg["opac_serialcirc_demande"], ENT_QUOTES, $charset) . "' movable='yes'>
					<div id='el10Child_8a' class='row'>
						<input type='checkbox' value='1' id='opac_serialcirc_demande' name='opac_serialcirc_demande'  " . ($this->opac_serialcirc_demande ? "checked='checked'" : '') . " />
						<label for='opac_serialcirc_demande' class='etiquette'>" . $msg["opac_serialcirc_demande"] . "</label>
					</div>
				</div>
				";
        }
        return $tab_gestion_fields_form;
    }

    protected function get_content_form()
    {
        global $charset;
        global $ptab;
        global $serial_top_content_form;

        $content_form = $serial_top_content_form;

        // mise à jour de l'onglet 0
        $element = interface_entity_record_element::get_instance('el0Child_0', 'f_tit1', '237');
        $element->add_input_node('text', $this->tit1, ['data-form-name' => 'f_tit1', 'data-pmb-deb-rech' => '1']);
        $ptab[0] = str_replace('!!element_tit1!!', $element->get_display(), $ptab[0]);

        // Titre parallèle
        $element_tit3 = $this->get_input_text_content_form('el0Child_1', 'tit3', '239');
        $ptab[0] = str_replace('!!element_tit3!!', $element_tit3, $ptab[0]);

        // Complément du titre
        $element_tit4 = $this->get_input_text_content_form('el0Child_2', 'tit4', '240');
        $ptab[0] = str_replace('!!element_tit4!!', $element_tit4, $ptab[0]);

        $content_form = str_replace('!!tab0!!', $ptab[0], $content_form);

        // mise à jour de l'onglet 1
        // constitution de la mention de responsabilité
        // $this->responsabilites
        $content_form = str_replace('!!tab1!!', $this->get_tab_responsabilities_form(), $content_form);

        // mise à jour de l'onglet 2
        $ptab[2] = str_replace('!!ed1_id!!', $this->ed1_id, $ptab[2]);
        $ptab[2] = str_replace('!!ed1!!', htmlentities($this->ed1, ENT_QUOTES, $charset), $ptab[2]);
        $ptab[2] = str_replace('!!ed2_id!!', $this->ed2_id, $ptab[2]);
        $ptab[2] = str_replace('!!ed2!!', htmlentities($this->ed2, ENT_QUOTES, $charset), $ptab[2]);
        $ptab[2] = str_replace('!!force_dialog_publisher!!', $this->is_force_dialog('publisher'), $ptab[2]);
        $ptab[2] = str_replace('!!force_popup_publisher!!', $this->is_force_popup('publisher'), $ptab[2]);

        $content_form = str_replace('!!tab2!!', $ptab[2], $content_form);

        // mise à jour de l'onglet 30 (code)
        $ptab[30] = str_replace('!!cb!!', htmlentities($this->code, ENT_QUOTES, $charset), $ptab[30]);
        $ptab[30] = str_replace('!!notice_id!!', $this->id, $ptab[30]);

        $content_form = str_replace('!!tab30!!', $ptab[30], $content_form);
        $content_form = str_replace('!!year!!', $this->year ?? '', $content_form);

        // mise à jour de l'onglet 3 (notes)
        $content_form = str_replace('!!tab3!!', $this->get_tab_notes_form(), $content_form);

        // mise à jour de l'onglet 4
        $content_form = str_replace('!!tab4!!', $this->get_tab_indexation_form(), $content_form);

        // mise à jour de l'onglet 5 : langues
        $content_form = str_replace('!!tab5!!', $this->get_tab_lang_form(), $content_form);

        // mise à jour de l'onglet 6
        $content_form = str_replace('!!tab6!!', $this->get_tab_links_form(), $content_form);

        // Mise à jour de l'onglet 7
        $content_form = str_replace('!!tab7!!', $this->get_tab_customs_perso_form(), $content_form);

        // Liens vers d'autres notices
        if ($this->duplicate_from_id) {
            $notice_relations = notice_relations_collection::get_object_instance($this->duplicate_from_id);
        } else {
            $notice_relations = notice_relations_collection::get_object_instance($this->id);
        }
        $content_form = str_replace('!!tab13!!', $notice_relations->get_form($this->notice_link, 's'), $content_form);

        // champs de gestion
        $content_form = str_replace('!!tab8!!', $this->get_tab_gestion_fields(), $content_form);

        // autorité personnalisées
        if ($this->duplicate_from_id) {
            $authperso = new authperso_notice($this->duplicate_from_id);
        } else {
            $authperso = new authperso_notice($this->id);
        }
        $authperso_tpl = $authperso->get_form();
        $content_form = str_replace('!!authperso!!', $authperso_tpl, $content_form);

        // map
        global $pmb_map_activate;
        if ($pmb_map_activate) {
            $content_form = str_replace('!!tab14!!', $this->get_tab_map_form(), $content_form);
        } else {
            $content_form = str_replace('!!tab14!!', '', $content_form);
        }

        /*
         * //affichage des formulaires des droits d'acces
         * $rights_form = $this->get_rights_form();
         * $ptab[14] = str_replace('<!-- rights_form -->', $rights_form, $ptab[14]);
         * $content_form = str_replace('!!tab14!!', $ptab[14],$content_form);
         */

        return $content_form;
    }

    public function get_form()
    {
        global $msg, $action;

        // initialisation avec les paramètres du user :
        if (! $this->langues) {
            global $value_deflt_lang;
            if ($value_deflt_lang) {
                $lang_ = new marc_list('lang');
                $this->langues[] = array(
                    'lang_code' => $value_deflt_lang,
                    'langue' => $lang_->table[$value_deflt_lang]
                );
            }
        }

        if (! $this->statut) {
            global $deflt_notice_statut;
            if ($deflt_notice_statut)
                $this->statut = $deflt_notice_statut;
            else
                $this->statut = 1;
        }
        if (! $this->typdoc) {
            global $xmlta_doctype_serial;
            $this->typdoc = $xmlta_doctype_serial;
        }

        $interface_form = new interface_entity_serial_form('notice');
        $interface_form->set_enctype('multipart/form-data');
        if ($this->id) {
            $interface_form->set_label($msg['4004']);
            $interface_form->set_document_title($this->tit1 . ' - ' . $msg['4004']);
        } else {
            $interface_form->set_label($msg['4003']);
            $interface_form->set_document_title($msg['4003']);
        }
        if($action == "bul_duplicate") {
        	$interface_form->set_is_duplication(true);
        }
        $interface_form->set_object_id($this->id)
            ->set_hierar_level($this->hierar_level)
            ->set_code($this->code)
            ->set_type_doc($this->typdoc)
            ->set_duplicable(true)
            ->set_content_form($this->get_content_form())
            ->set_table_name('notices')
            ->set_field_focus('f_tit1')
            ->set_url_base(static::format_url());
        return $interface_form->get_display();
    }

    // fonction générant le form de saisie de notice chapeau
    public function do_form()
    {
        return $this->get_form();
    }

    // ---------------------------------------------------------------
    // replace_form : affichage du formulaire de remplacement
    // ---------------------------------------------------------------
    public function replace_form()
    {
        global $perio_replace;
        global $msg, $charset;
        global $include_path;
        global $deflt_notice_replace_keep_categories;
        global $perio_replace_categories, $perio_replace_category;
        global $thesaurus_mode_pmb;

        // a compléter
        if (! $this->id) {
            require_once ("$include_path/user_error.inc.php");
            error_message($msg[161], $msg[162], 1, './catalog.php');
            return false;
        }

        $perio_replace = str_replace('!!old_perio_libelle!!', $this->tit1, $perio_replace);
        $perio_replace = str_replace('!!serial_id!!', $this->id, $perio_replace);
        if (! empty($deflt_notice_replace_keep_categories) && ! empty($this->categories)) {
            // categories
            $categories_to_replace = "";
            $nb_categories = count($this->categories);
            for ($i = 0; $i < $nb_categories; $i ++) {
                $categ_id = $this->categories[$i]["categ_id"];
                $categ = new category($categ_id);
                $ptab_categ = str_replace('!!icateg!!', $i, $perio_replace_category);
                $ptab_categ = str_replace('!!categ_id!!', $categ_id, $ptab_categ);
                if ($thesaurus_mode_pmb)
                    $nom_thesaurus = '[' . $categ->thes->getLibelle() . '] ';
                else
                    $nom_thesaurus = '';
                $ptab_categ = str_replace('!!categ_libelle!!', htmlentities($nom_thesaurus . $categ->catalog_form, ENT_QUOTES, $charset), $ptab_categ);
                $categories_to_replace .= $ptab_categ;
            }
            $perio_replace_categories = str_replace('!!perio_replace_category!!', $categories_to_replace, $perio_replace_categories);
            $perio_replace_categories = str_replace('!!nb_categ!!', $nb_categories, $perio_replace_categories);

            $perio_replace = str_replace('!!perio_replace_categories!!', $perio_replace_categories, $perio_replace);
        } else {
            $perio_replace = str_replace('!!perio_replace_categories!!', "", $perio_replace);
        }
        print $perio_replace;
    }

    public function set_properties_from_form()
    {
        global $a2z_opac_show, $opac_visible_bulletinage;
        global $opac_serialcirc_active, $opac_serialcirc_demande;

        parent::set_properties_from_form();
        if ($a2z_opac_show)
            $val = 0;
        else
            $val = 0x10;
        $this->opac_visible_bulletinage = intval($opac_visible_bulletinage) | $val;

        if ($opac_serialcirc_active) {
            $this->opac_serialcirc_demande = intval($opac_serialcirc_demande);
        }

        $this->biblio_level = "s";
        $this->hierar_level = "1";
    }

    public function save()
    {
        global $gestion_acces_active, $gestion_acces_user_notice;

        $saved = parent::save();
        if ($saved) {
            $this->serial_id = $this->id;

            // traitement des droits d'acces user_notice
            if ($gestion_acces_active == 1 && $gestion_acces_user_notice == 1) {
                // on applique les memes droits d'acces user_notice aux bulletins et depouillements lies
                $q = "select num_notice from bulletins where bulletin_notice=" . $this->id . " AND num_notice!=0 ";
                $q .= "union ";
                $q .= "select analysis_notice from analysis join bulletins on analysis_bulletin=bulletin_id where bulletin_notice=" . $this->id;
                $r = pmb_mysql_query($q);
                if (pmb_mysql_num_rows($r)) {
                    while (($row = pmb_mysql_fetch_object($r))) {
                        $q = "replace into acces_res_1 select " . $row->num_notice . ", res_prf_num,usr_prf_num,res_rights,res_mask from acces_res_1 where res_num=" . $this->id;
                        pmb_mysql_query($q);
                    }
                }
            }
        }
        return $saved;
    }

    // ---------------------------------------------------------------
    // replace($by) : remplacement du périodique
    // ---------------------------------------------------------------
    public function replace($by, $supp_notice = true)
    {
        global $msg;
        global $pmb_synchro_rdf;
        global $keep_categories;
        global $notice_replace_links;
        global $pmb_ark_activate;

        if (($this->id == $by) || (! $this->id)) {
            return $msg[223];
        }

        // traitement des catégories (si conservation cochée)
        if ($keep_categories) {
            update_notice_categories_from_form($by);
        }

        // remplacement dans les bulletins
        $requete = "UPDATE bulletins SET bulletin_notice='$by' WHERE bulletin_notice='$this->id' ";
        pmb_mysql_query($requete);

        // gestion des liens
        notice_relations::replace_links($this->id, $by, $notice_replace_links);

        // remplacement des docs numériques
        $requete = "update explnum SET explnum_notice='$by' WHERE explnum_notice='$this->id' ";
        pmb_mysql_query($requete);

        // remplacement des etats de collections
        $requete = "update collections_state SET id_serial='$by' WHERE id_serial='$this->id' ";
        pmb_mysql_query($requete);

        if ($pmb_ark_activate) {
            $arkEntityReplaced = ArkEntityPmb::getEntityClassFromType(TYPE_NOTICE, $this->id);
            $arkEntityReplacing = ArkEntityPmb::getEntityClassFromType(TYPE_NOTICE, $by);
            $arkEntityReplaced->markAsReplaced($arkEntityReplacing);
        }
        if ($supp_notice) {
            $this->serial_delete();
        }

        // Mise à jour des bulletins reliés
        if ($pmb_synchro_rdf) {
            $synchro_rdf = new synchro_rdf();
            $requete = "SELECT bulletin_id FROM bulletins WHERE bulletin_notice='$by' ";
            $result = pmb_mysql_query($requete);
            while ($row = pmb_mysql_fetch_object($result)) {
                $synchro_rdf->delRdf(0, $row->bulletin_id);
                $synchro_rdf->addRdf(0, $row->bulletin_id);
            }
        }

        return FALSE;
    }

    // suppression d'une notice chapeau, uniquement notice
    public function serial_delete()
    {
        $requete = "SELECT bulletin_id,num_notice from bulletins WHERE bulletin_notice='" . $this->id . "' ";
        $myQuery1 = pmb_mysql_query($requete);
        if ($myQuery1 && pmb_mysql_num_rows($myQuery1)) {
            while (($bul = pmb_mysql_fetch_object($myQuery1))) {
                $bulletin = new bulletinage($bul->bulletin_id);
                $bulletin->delete();
            }
        }

        // suppression des modeles
        $requete = "SELECT modele_id from abts_modeles WHERE num_notice='" . $this->id . "' ";
        $result_modele = pmb_mysql_query($requete);
        while (($modele = pmb_mysql_fetch_object($result_modele))) {
            $mon_modele = new abts_modele($modele->modele_id);
            $mon_modele->delete();
        }

        // Suppression des etats de collections
        $collstate = new collstate(0, $this->id);
        $collstate->delete();

        // suppression des demandes d'abonnement aux listes de circulation
        $requete = "delete from serialcirc_ask where num_serialcirc_ask_perio=" . $this->id;
        pmb_mysql_query($requete);

        static::del_notice($this->id);

        return true;
    }

    protected function get_display_mode_selector($name)
    {
        return selector_model::get_instance($name)->get_display_mode('serial');
    }

    public static function get_pattern_link()
    {
        global $base_path;
        return $base_path . '/catalog.php?categ=serials&sub=view&serial_id=!!id!!';
    }

    public static function get_permalink($notice_id, $parent_id = 0)
    {
        global $base_path;
        return $base_path . '/catalog.php?categ=serials&sub=view&serial_id=' . intval($notice_id);
    }

    protected static function format_url($url = '')
    {
        global $base_path;

        if (isset(static::$controller) && is_object(static::$controller)) {
            return static::$controller->get_url_base() . $url;
        } else {
            return $base_path . '/catalog.php?categ=serials' . $url;
        }
    }
}

// fin définition classe

/*
 * ------------------------------------------------------------------------------------
 * classe bulletinage : classe de gestion des bulletinages
 * ---------------------------------------------------------------------------------------
 */
class bulletinage extends notice
{

    public $bulletin_id = 0;

    // id de ce bulletinage
    public $bulletin_titre = '';

    // titre propre du bulletin
    public $bulletin_numero = '';

    // mention de numéro sur la publication
    public $bulletin_notice = 0;

    // id notice parent = id du périodique relié
    public $serial_id = 0;

    // id notice parent = id du périodique relié
    public $serial;

    // instance du périodique (serial)
    public $bulletin_cb = '';

    // Code EAN13 (+ addon) du bulletin
    public $mention_date = '';

    // mention de date sur la publication au format texte libre
    public $date_date = '';

    // date de la publication au format date
    public $aff_date_date = '';

    // date de la publication au format date correct pour affichage
    public $display = '';

    // forme à afficher pour prêt, listes, etc...
    public $header = '';

    // forme du bulletin allégé pour l'affichage (résa)
    public $nb_analysis = 0;

    // nombre de notices de dépouillement
    public $bull_num_notice = 0;

    // Numéro de la notice liée

    // Notice de bulletin
    public $has_notice_bulletin = false;

    public $biblio_level = 'b';

    // niveau bibliographique
    public $hierar_level = '2';

    // niveau hiérarchique
    public $typdoc = '';

    // type UNIMARC du document
    public $code = '';

    // codebarre du périodique
    public $indexint_lib = '';

    // libelle indexation interne
    public $notice_show_expl = 1;

    // affichage des exemplaires dans la notice de bulletin

    // données de(s) exemplaire(s) : un tableau d'objets
    public $expl;

    // données des exemplaires numériques
    public $explnum;

    public $nbexplnum;

    protected static $vedette_composee_config_filename = 'bulletin_authors';

    // constructeur
    public function __construct($bulletin_id, $serial_id = 0, $link_explnum = '', $localisation = 0, $make_display = true)
    {
        global $pmb_droits_explr_localises, $explr_invisible;
        global $pmb_sur_location_activate;
        global $xmlta_doctype_bulletin;
        global $deflt_notice_is_new;

        $this->bulletin_id = intval($bulletin_id);
        if ($this->bulletin_id) {
            $this->fetch_bulletin_data();
            $this->id = $this->bull_num_notice;
        } else {
            $this->is_new = $deflt_notice_is_new;
            $this->id = 0;
        }
        if ($serial_id) {
            $this->bulletin_notice = $serial_id;
            $this->serial_id = $serial_id;
        }

        $tmp_link = $this->notice_link;

        // On vide les liens entre notices car ils sont appliqués pour le serial dans le $this
        $this->serial = new serial($this->bulletin_notice);
        if ($this->serial->serial_id) {
            $this->notice_link = array();
            $this->notice_link = $tmp_link;
        }
        unset($tmp_link);

        // si le bulletin n'a pas de notice associée, son typedoc par défaut sera celui de la notice chapeau
        if ($xmlta_doctype_bulletin) {
            if (! $this->typdoc)
                $this->typdoc = $xmlta_doctype_bulletin;
        } else {
            if (! $this->typdoc)
                $this->typdoc = $this->serial->typdoc;
        }

        if ($make_display) { // Je ne crée la partie affichage que quand j'en ai besoin
            $this->make_display();
            $this->make_short_display();
        }

        // on récupère les données d'exemplaires liés
        $this->expl = array();
        if ($this->bulletin_id) {
            $requete = "SELECT count(1) from analysis where analysis_bulletin='" . $this->bulletin_id . "'";
            $query_nb_analysis = pmb_mysql_query($requete);
            $this->nb_analysis = pmb_mysql_result($query_nb_analysis, 0, 0);

            // visibilité des exemplaires:
            if ($pmb_droits_explr_localises && $explr_invisible)
                $where_expl_localises = " and expl_location not in ($explr_invisible)";
            else
                $where_expl_localises = "";
            if ($localisation > 0)
                $where_localisation = " and expl_location=$localisation ";
            else
                $where_localisation = "";

            $requete = "SELECT exemplaires.*, tdoc_libelle, section_libelle";
            $requete .= ", statut_libelle, location_libelle";
            $requete .= ", codestat_libelle, lender_libelle, pret_flag ";
            $requete .= " FROM exemplaires, docs_type, docs_section, docs_statut, docs_location, docs_codestat, lenders ";
            $requete .= "  WHERE exemplaires.expl_bulletin=" . $this->bulletin_id . "$where_expl_localises $where_localisation";
            $requete .= " AND docs_type.idtyp_doc=exemplaires.expl_typdoc";
            $requete .= " AND docs_section.idsection=exemplaires.expl_section";
            $requete .= " AND docs_statut.idstatut=exemplaires.expl_statut";
            $requete .= " AND docs_location.idlocation=exemplaires.expl_location";
            $requete .= " AND docs_codestat.idcode=exemplaires.expl_codestat";
            $requete .= " AND lenders.idlender=exemplaires.expl_owner";
            $myQuery = pmb_mysql_query($requete);
            if (pmb_mysql_num_rows($myQuery)) {
                while (($expl = pmb_mysql_fetch_object($myQuery))) {
                    if ($pmb_sur_location_activate) {
                        $sur_loc = sur_location::get_info_surloc_from_location($expl->expl_location);
                        $expl->sur_loc_libelle = $sur_loc->libelle;
                        $expl->sur_loc_id = $sur_loc->id;
                    }
                    $this->expl[] = $expl;
                }
                /*
                 * note : le tableau est constitué d'objet dont les propriétés sont :
                 * id exemplaire expl_id;
                 * code-barre expl_cb;
                 * notice expl_notice;
                 * bulletinage expl_bulletin;
                 * type doc expl_typdoc;
                 * libelle type doc tdoc_libelle;
                 * cote expl_cote;
                 * section expl_section;
                 * libelle section section_libelle;
                 * statut expl_statut;
                 * libelle statut statut_libelle;
                 * localisation expl_location;
                 * libelle localisation location_libelle;
                 * code statistique expl_codestat;
                 * libelle code_stat codestat_libelle;
                 * libelle proprietaire lender_libelle;
                 * date de dépot BDP par exemple expl_date_depot;
                 * date de retour expl_date_retour;
                 * note expl_note;
                 * prix expl_prix;
                 * owner $expl->expl_owner;
                 */
            }
            $requete = "SELECT explnum.* FROM explnum WHERE explnum_bulletin='" . $this->bulletin_id . "' ";
            $myQuery = pmb_mysql_query($requete);
            $this->nbexplnum = pmb_mysql_num_rows($myQuery);
            if ($make_display && $this->nbexplnum) { // Je ne crée la partie affichage que quand j'en ai besoin
                $this->explnum = show_explnum_per_notice(0, $this->bulletin_id, $link_explnum);
            }
        }
        return $this->bulletin_id;
    }

    // fabrication de la version affichable
    public function make_display()
    {
        global $charset;
        $this->display = htmlentities($this->get_serial()->tit1, ENT_QUOTES, $charset);
        if ($this->bulletin_numero)
            $this->display .= '. ' . $this->bulletin_numero;
        // affichage de la mention de date utile : mention_date si existe, sinon date_date
        if ($this->mention_date) {
            $date_affichee = " (" . $this->mention_date . ")";
        } else if ($this->date_date) {
            $date_affichee = " [" . $this->aff_date_date . "]";
        } else {
            $date_affichee = "";
        }
        $this->display .= $date_affichee;

        if ($this->bulletin_titre)
            $this->display .= " : " . htmlentities($this->bulletin_titre, ENT_QUOTES, $charset);
        if ($this->bulletin_cb)
            $this->display .= ". " . $this->bulletin_cb;
        if ($this->bull_num_notice) {
			$record = new elements_records_list_ui([$this->bull_num_notice], 1, false);
			$record->set_level(5);
			if(empty($this->notice_show_expl)) {
			    $record->set_show_explnum(0);
			    $record->set_show_expl(0);
			    $record->set_show_statut(1);
            }
			$record = new elements_records_list_ui([$this->bull_num_notice], 1, false);
			$this->display.="<blockquote>".$record->get_elements_list()."</blockquote>";
        }
    }

    // fabrication de la version allégée pour l'affichage
    public function make_short_display()
    {
        $this->header = $this->get_serial()->tit1;
        if ($this->bulletin_numero)
            $this->header .= '. ' . $this->bulletin_numero;
        // affichage de la mention de date utile : mention_date si existe, sinon date_date
        if ($this->mention_date) {
            $date_affichee = " (" . $this->mention_date . ")";
        } else if ($this->date_date) {
            $date_affichee = " [" . $this->aff_date_date . "]";
        } else {
            $date_affichee = "";
        }
        $this->header .= $date_affichee;
    }

    // récupération des infos sur le bulletinage
    public function fetch_bulletin_data()
    {
        global $msg;

        $myQuery = pmb_mysql_query("SELECT *, date_format(date_date, '" . $msg["format_date"] . "') as aff_date_date FROM bulletins WHERE bulletin_id='" . $this->bulletin_id . "' ");

        if (pmb_mysql_num_rows($myQuery)) {
            $bulletin = pmb_mysql_fetch_object($myQuery);
            $this->bulletin_titre = $bulletin->bulletin_titre;
            $this->bulletin_notice = $bulletin->bulletin_notice;
            $this->bulletin_numero = $bulletin->bulletin_numero;
            $this->bulletin_cb = $bulletin->bulletin_cb;
            $this->mention_date = $bulletin->mention_date;
            $this->date_date = $bulletin->date_date;
            $this->aff_date_date = $bulletin->aff_date_date;
            $this->bull_num_notice = $bulletin->num_notice;
            $this->id = $bulletin->num_notice;

            if ($this->id) {
                $this->fetch_data();
                // type du document
                $this->typdoc = $this->type_doc;
            }
        }

        if ($this->date_date == "0000-00-00") {
            $this->date_date = "";
            $this->aff_date_date = "";
        }

        return pmb_mysql_num_rows($myQuery);
    }

    // fonction de mise à jour d'une entrée MySQL de bulletinage
    // DG - Remplacée par set_properties_from_form et save le 05/08/2019
    public function update($value, $dont_update_bul = false, $other_fields = "")
    {

        // clean des vieilles nouveautés
        static::cleaning_is_new();

        if (is_array($value)) {
            $this->bulletin_titre = $value['bul_titre'];
            $this->bulletin_numero = $value['bul_no'] ?? "";
            $this->bulletin_cb = $value['bul_cb'] ?? "";
            $this->mention_date = $value['bul_date'];

            // Note YPR : à revoir
            if ($value['date_date'])
                $this->date_date = $value['date_date'];
            else
                $this->date_date = today();

            // construction de la requete :
            $data = "bulletin_titre='" . $this->bulletin_titre . "'";
            $data .= ",bulletin_numero='" . $this->bulletin_numero . "'";
            $data .= ",bulletin_cb='" . $this->bulletin_cb . "'";
            $data .= ",mention_date='" . $this->mention_date . "'";
            $data .= ",date_date='" . $this->date_date . "'";
            $data .= ",index_titre=' " . strip_empty_words($this->bulletin_titre) . " '";

            if (! $this->bulletin_id) {
                // si c'est une creation, on ajoute l'id du parent la date et on cree la notice !
                $data .= ",bulletin_notice='" . $this->bulletin_notice . "'";
                // fabrication de la requete finale
                $requete = "INSERT INTO bulletins SET $data";
                pmb_mysql_query($requete);
                $insert_last_id = pmb_mysql_insert_id();
                audit::insert_creation(AUDIT_BULLETIN, $insert_last_id);
                $this->bulletin_id = $insert_last_id;
            } else {
                $requete = "UPDATE bulletins SET $data WHERE bulletin_id='" . $this->bulletin_id . "' LIMIT 1";
                pmb_mysql_query($requete);
                audit::insert_modif(AUDIT_BULLETIN, $this->bulletin_id);
                $requete = "UPDATE notices SET date_parution='" . $value['date_parution'] . "', year='" . $value['year'] . "' WHERE notice_id in (SELECT analysis_notice FROM analysis WHERE analysis_bulletin=$this->bulletin_id)";
                pmb_mysql_query($requete);
            }
        } else
            return;

        global $include_path;

        if (! $dont_update_bul) {
            // formatage des valeurs de $value
            // $value est un tableau contenant les infos du périodique
            if (empty($value['tit1'])) {
                $this->bull_num_notice = 0;
                // return;
            }

            // Nettoyage des infos bulletin
            unset($value['bul_titre']);
            unset($value['bul_no']);
            unset($value['bul_cb']);
            unset($value['bul_date']);
            unset($value['date_date']);

            if (! empty($value['index_l']))
                $value['index_l'] = clean_tags($value['index_l']);

            if (! empty($value['aut']) && is_array($value['aut']) && $value['aut'][0]['id'])
                $value['aut'] = 'aut_exist';
            else
                $value['aut'] = '';

            if (! empty($value['categ']) && is_array($value['categ']) && $value['categ'][0]['id'])
                $value['categ'] = 'categ_exist';
            else
                $value['categ'] = '';

            if (! empty($value["concept"]))
                $value["concept"] = 'concept_exist';
            else
                $value["concept"] = '';

            // type de document
            // $value['typdoc']=$value['typdoc'];
            $empty = "";
            if (! empty($value['force_empty']))
                $empty = "perso";
            unset($value['force_empty']);

            if (isset($value['create_notice_bul']) && $value['create_notice_bul']) {
                $empty .= "create_notice_bul";
                unset($value['create_notice_bul']);
            }

            $values = '';
            foreach ($value as $cle => $valeur) {
                if (($cle != "statut") && ($cle != "tit1") && ($cle != "niveau_hierar") && ($cle != "niveau_biblio") && ($cle != "index_sew") && ($cle != "index_wew") && ($cle != "typdoc") && ($cle != "date_parution") && ($cle != "year") && ($cle != "indexation_lang")) {
                    if ((($cle == "indexint" || $cle == "ed1_id" || $cle == "ed2_id") && ($valeur)) || ($cle != "indexint" && $cle != "ed1_id" && $cle != "ed2_id")) {
                        $empty .= $valeur;
                    }
                }
                if ($cle == 'aut' || $cle == 'categ' || $cle == 'concept') {
                    $values .= '';
                } else {
                    if ($values) {
                        $values .= ",$cle='$valeur'";
                    } else {
                        $values .= "$cle='$valeur'";
                    }
                }
            }
            if ($this->bull_num_notice) {
                if ($empty) {
                    // modif
                    pmb_mysql_query("UPDATE notices SET $values , update_date=sysdate() $other_fields WHERE notice_id=" . $this->bull_num_notice);
                    // Mise à jour des index de la notice
                    notice::majNoticesTotal($this->bull_num_notice);
                    audit::insert_modif(AUDIT_NOTICE, $this->bull_num_notice);
                } else {
                    static::del_notice($this->bull_num_notice);
                    $this->bull_num_notice = "";
                    pmb_mysql_query("update bulletins set num_notice=0 where bulletin_id=" . $this->bulletin_id);
                }
                return $this->bulletin_id;
            } else {

                // create
                if ($empty) {
                    pmb_mysql_query("INSERT INTO notices SET $values , create_date=sysdate(), update_date=sysdate() $other_fields ");
                    $this->bull_num_notice = pmb_mysql_insert_id();
                    // Mise à jour des index de la notice
                    notice::majNoticesTotal($this->bull_num_notice);
                    audit::insert_creation(AUDIT_NOTICE, $this->bull_num_notice);

                    // Mise à jour du bulletin
                    $requete = "update bulletins set num_notice=" . $this->bull_num_notice . " where bulletin_id=" . $this->bulletin_id;
                    pmb_mysql_query($requete);

                    // Calcul des droits d'accès
                    notice::calc_access_rights($this->bull_num_notice);

                    // Mise à jour des liens bulletin -> notice mère
                    notice_relations::insert($this->bull_num_notice, $this->get_serial()->id, 'b', 1, 'up', false);
                }
                return $this->bulletin_id;
            }
        } else {
            /*
             * Quand passe-t'on ici ?
             */
            if ($this->bull_num_notice) {
                // Mise à jour du bulletin
                $requete = "update bulletins,notices set num_notice=" . $this->bull_num_notice . ",bulletin_titre=tit1 where bulletin_id=" . $this->bulletin_id . " and notice_id=" . $this->bull_num_notice;
                pmb_mysql_query($requete);

                // Mise à jour des liens bulletin -> notice mere
                notice_relations::insert($this->bull_num_notice, $this->get_serial()->id, 'b', 1, 'up', false);
                // Recherche des articles
                $requete = "select analysis_notice from analysis where analysis_bulletin=" . $this->bulletin_id;
                $resultat_analysis = pmb_mysql_query($requete);
                $n = 1;
                while (($r_a = pmb_mysql_fetch_object($resultat_analysis))) {
                    notice_relations::insert($r_a->analysis_notice, $this->bull_num_notice, 'a', $n);
                    $n ++;
                }
            }
            return $this->bulletin_id;
        }
    }

    protected function get_content_form()
    {
        global $msg;
        global $charset;
        global $serial_bul_content_form;
        // Notice
        global $ptab, $ptab_bul;
        global $include_path, $class_path;
        global $pmb_bulletin_date_parution;

        $content_form = $serial_bul_content_form;

        // mise à jour de l'onglet 0
        // $ptab[0] = str_replace('!!tit1!!', htmlentities($this->tit1,ENT_QUOTES, $charset) , $ptab[0]);

        // Titre parallèle
        $element_tit3 = $this->get_input_text_content_form('el0Child_0', 'tit3', '239');
        $ptab_bul[0] = str_replace('!!element_tit3!!', $element_tit3, $ptab_bul[0]);

        // Complément du titre
        $element_tit4 = $this->get_input_text_content_form('el0Child_1', 'tit4', '240');
        $ptab_bul[0] = str_replace('!!element_tit4!!', $element_tit4, $ptab_bul[0]);

        $content_form = str_replace('!!tab0!!', $ptab_bul[0], $content_form);

        // mise à jour de l'onglet 1
        // constitution de la mention de responsabilité
        // $this->responsabilites
        $content_form = str_replace('!!tab1!!', $this->get_tab_responsabilities_form(), $content_form);

        // mise à jour de l'onglet 2
        /*
         * $ptab[2] = str_replace('!!ed1_id!!', $this->ed1_id , $ptab[2]);
         * $ptab[2] = str_replace('!!ed1!!', htmlentities($this->ed1,ENT_QUOTES, $charset) , $ptab[2]);
         * $ptab[2] = str_replace('!!ed2_id!!', $this->ed2_id , $ptab[2]);
         * $ptab[2] = str_replace('!!ed2!!', htmlentities($this->ed2,ENT_QUOTES, $charset) , $ptab[2]);
         *
         * $content_form = str_replace('!!tab2!!', $ptab[2], $content_form);
         */

        // mise à jour de l'onglet 30 (code)
        $content_form = str_replace('!!tab30!!', $this->get_tab_isbn_form(), $content_form);

        // mise à jour de l'onglet 3 (notes)
        $content_form = str_replace('!!tab3!!', $this->get_tab_notes_form(), $content_form);

        // mise à jour de l'onglet 4
        $content_form = str_replace('!!tab4!!', $this->get_tab_indexation_form(), $content_form);

        // Collation
        $ptab[41] = str_replace("!!npages!!", htmlentities($this->npages, ENT_QUOTES, $charset), $ptab[41]);
        $ptab[41] = str_replace("!!ill!!", htmlentities($this->ill, ENT_QUOTES, $charset), $ptab[41]);
        $ptab[41] = str_replace("!!size!!", htmlentities($this->size, ENT_QUOTES, $charset), $ptab[41]);
        $ptab[41] = str_replace("!!accomp!!", htmlentities($this->accomp, ENT_QUOTES, $charset), $ptab[41]);
        $ptab[41] = str_replace("!!prix!!", htmlentities($this->prix, ENT_QUOTES, $charset), $ptab[41]);
        $content_form = str_replace('!!tab41!!', $ptab[41], $content_form);

        // mise à jour de l'onglet 5 : langues
        $content_form = str_replace('!!tab5!!', $this->get_tab_lang_form(), $content_form);

        // mise à jour de l'onglet 6
        $content_form = str_replace('!!tab6!!', $this->get_tab_links_form(), $content_form);

        // Mise à jour de l'onglet 7
        $content_form = str_replace('!!tab7!!', $this->get_tab_customs_perso_form(), $content_form);

        // Liens vers d'autres notices
        if ($this->duplicate_from_id) {
            $notice_relations = notice_relations_collection::get_object_instance($this->duplicate_from_id);
        } else {
            $notice_relations = notice_relations_collection::get_object_instance($this->bull_num_notice);
        }
        $content_form = str_replace('!!tab13!!', $notice_relations->get_form($this->notice_link, 'b'), $content_form);

        // champs de gestion
        $content_form = str_replace('!!tab8!!', $this->get_tab_gestion_fields(), $content_form);

        global $pmb_map_activate;
        if ($pmb_map_activate) {
            $content_form = str_replace('!!tab14!!', $this->get_tab_map_form(), $content_form);
        } else {
            $content_form = str_replace('!!tab14!!', '', $content_form);
        }

        // autorité personnalisées
        if ($this->duplicate_from_id) {
            $authperso = new authperso_notice($this->duplicate_from_id);
        } else {
            $authperso = new authperso_notice($this->bull_num_notice);
        }
        $authperso_tpl = $authperso->get_form();
        $content_form = str_replace('!!authperso!!', $authperso_tpl, $content_form);

        $content_form = str_replace('!!serial_id!!', $this->get_serial()->id, $content_form);
        $content_form = str_replace('!!bul_id!!', $this->bulletin_id, $content_form);
        $content_form = str_replace('!!bul_titre!!', htmlentities($this->bulletin_titre, ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!bul_no!!', htmlentities($this->bulletin_numero, ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!bul_date!!', htmlentities($this->mention_date, ENT_QUOTES, $charset), $content_form);
        $content_form = str_replace('!!bul_cb!!', $this->bulletin_cb, $content_form);

        if (! $this->bulletin_id && ($this->date_date == '0000-00-00' || empty($this->date_date)) && $pmb_bulletin_date_parution == "1") {
            $this->date_date = today();
        }
        $date_date = "<input type='date' name='date_date' value='" . $this->date_date . "' />";
        $content_form = str_replace('!!date_date!!', $date_date, $content_form);

        // Case à cocher pour créer la notice de bulletin
        $create_notice_bul = '<input type="checkbox" value="1" id="create_notice_bul" name="create_notice_bul">&nbsp;' . $msg['bulletinage_create_notice'];
        if ($this->bulletin_id) {
            if ($this->bull_num_notice) {
                $del_bulletin_notice_js = "onClick='if(confirm(\"" . $msg["del_bulletin_notice_confirm"] . "\")){location.href=\"./catalog.php?categ=serials&sub=bulletinage&action=bul_del_notice&bul_id=" . $this->bulletin_id . "\";}'";
                $create_notice_bul = "<input type='checkbox' id='create_notice_bul' checked='checked' disabled='true'><input type='hidden' name='create_notice_bul' value='1'>&nbsp;" . $msg['bulletinage_created_notice'] . "&nbsp;<input class='bouton' type='button' name='del_bulletin_notice' value='" . $msg["del_bulletin_notice"] . "' " . $del_bulletin_notice_js . "/>";
            }
        }
        $content_form = str_replace('!!create_notice_bul!!', $create_notice_bul, $content_form);

        return $content_form;
    }

    public function get_form()
    {
        global $msg;

        // initialisation avec les paramètres du user :
        if (! $this->langues) {
            global $value_deflt_lang;
            if ($value_deflt_lang) {
                $lang = new marc_list('lang');
                $this->langues[] = array(
                    'lang_code' => $value_deflt_lang,
                    'langue' => $lang->table[$value_deflt_lang]
                );
            }
        }

        if (! $this->statut) {
            global $deflt_notice_statut;
            $this->statut = $deflt_notice_statut;
        }
        if (! $this->typdoc) {
            global $xmlta_doctype_bulletin;
            if ($xmlta_doctype_bulletin) {
                $this->typdoc = $xmlta_doctype_bulletin;
            } else {
                global $xmlta_doctype_serial;
                $this->typdoc = $xmlta_doctype_serial;
            }
        }

        $interface_form = new interface_entity_bulletinage_form('notice');
        $interface_form->set_enctype('multipart/form-data');
        if ($this->bulletin_id) {
            $interface_form->set_label($msg['4006']);
            $interface_form->set_document_title($this->header . ' - ' . $msg['4006']);
        } else {
            $interface_form->set_label($msg['4005']);
            $interface_form->set_document_title($msg['4005']);
        }

        $interface_form->set_object_id($this->bulletin_id)
            ->set_hierar_level($this->hierar_level)
            ->set_code($this->code)
            ->set_type_doc($this->typdoc)
            ->set_duplicable(true)
            ->set_serial_id($this->get_serial()->id)
            ->set_content_form($this->get_content_form())
            ->set_table_name('notices')
            ->set_field_focus('bul_no')
            ->set_url_base(static::format_url());
        return $interface_form->get_display();
    }

    // fonction d'affichage du formulaire de mise à jour
    public function do_form()
    {
        return $this->get_form();
    }

    public function set_properties_from_form()
    {
        global $bul_no, $bul_date, $date_date, $bul_cb, $bul_titre, $f_tit1;

        parent::set_properties_from_form();
        $this->bulletin_numero = clean_string(stripslashes($bul_no));
        $this->mention_date = clean_string(stripslashes($bul_date));
        $this->date_date = stripslashes($date_date);
        $this->bulletin_cb = clean_string(stripslashes($bul_cb));
        $this->bulletin_titre = stripslashes($bul_titre);

        $this->tit1 = $this->bulletin_numero . ($this->mention_date ? " - " . $this->mention_date : "") . ($this->bulletin_titre ? " - " . $this->bulletin_titre : "");
        // Set de la globale f_tit1 pour pouvoir ajouter une signature sur la notice du bulletin
        $f_tit1 = $this->tit1;
        if ($this->date_date == '0000-00-00' || empty($date_date)) {
            $this->year = "";
        } else {
            $this->year = substr($this->date_date, 0, 4);
        }
        $this->date_parution = $this->date_date;
    }

    public function has_notice_bulletin($force_empty)
    {
        global $create_notice_bul;

        if (isset($create_notice_bul) && $create_notice_bul) {
            $this->has_notice_bulletin = true;
        }
        if ($force_empty) {
            $this->has_notice_bulletin = true;
        }
        if (! $this->has_notice_bulletin) {
            if ($this->commentaire_gestion || $this->thumbnail_url || $this->code || $this->tit3 || $this->tit4 || $this->num_notice_usage || ! empty($this->responsabilites['auteurs']) || ! empty($this->categories) || ! empty($this->concepts_ids) || $this->ed1_id || $this->ed2_id || $this->n_gen || $this->n_contenu || $this->n_resume || $this->indexint || $this->index_l || $this->lien || $this->eformat || $this->ill || $this->size || $this->prix || $this->accomp || $this->npages) {
                $this->has_notice_bulletin = true;
            }
        }
        return $this->has_notice_bulletin;
    }

    public function save()
    {
        global $msg;
        global $pmb_notice_img_folder_id;
        global $pmb_synchro_rdf;
        global $pmb_ark_activate;
        // Pour la synchro rdf
        if ($pmb_synchro_rdf) {
            $synchro_rdf = new synchro_rdf();
            if ($this->bulletin_id) {
                $synchro_rdf->delRdf(0, $this->bulletin_id);
            }
        }
        $p_perso = new parametres_perso("notices");
        $nberrors = $p_perso->check_submited_fields();
        $force_empty = $p_perso->presence_exclusion_fields();
        if ($_FILES['f_img_load']['name'] && $pmb_notice_img_folder_id) {
            $force_empty = "f_img_load";
        }
        $this->has_notice_bulletin($force_empty);
        if (($nberrors && ! $this->has_notice_bulletin) || ! $nberrors) {
            // construction de la requete :
            $data = "bulletin_titre='" . addslashes($this->bulletin_titre) . "'";
            $data .= ",bulletin_numero='" . addslashes($this->bulletin_numero) . "'";
            $data .= ",bulletin_cb='" . addslashes($this->bulletin_cb) . "'";
            $data .= ",mention_date='" . addslashes($this->mention_date) . "'";
            $data .= ",date_date='" . addslashes($this->date_date) . "'";
            $data .= ",index_titre=' " . addslashes(strip_empty_words($this->bulletin_titre)) . " '";

            if (! $this->bulletin_id) {
                // si c'est une creation, on ajoute l'id du parent la date et on cree la notice !
                $data .= ",bulletin_notice='" . $this->bulletin_notice . "'";
                // fabrication de la requete finale
                $requete = "INSERT INTO bulletins SET $data";
                pmb_mysql_query($requete);
                $insert_last_id = pmb_mysql_insert_id();
                audit::insert_creation(AUDIT_BULLETIN, $insert_last_id);
                $this->bulletin_id = $insert_last_id;
            } else {
                $requete = "UPDATE bulletins SET $data WHERE bulletin_id='" . $this->bulletin_id . "' LIMIT 1";
                pmb_mysql_query($requete);
                audit::insert_modif(AUDIT_BULLETIN, $this->bulletin_id);
                $requete = "UPDATE notices SET date_parution='" . addslashes($this->date_parution) . "', year='" . addslashes($this->year) . "' WHERE notice_id in (SELECT analysis_notice FROM analysis WHERE analysis_bulletin=$this->bulletin_id)";
                pmb_mysql_query($requete);
            }
            if ($this->has_notice_bulletin) {
                $saved = parent::save();
                if (! $saved) {
                    return false;
                }
            }
            if ($this->bull_num_notice) {
                if (! ($force_empty || $this->has_notice_bulletin)) {
                    static::del_notice($this->bull_num_notice);
                    $this->bull_num_notice = "";
                    pmb_mysql_query("update bulletins set num_notice=0 where bulletin_id=" . $this->bulletin_id);
                }
            } else {
                if ($force_empty || $this->has_notice_bulletin) {
                    // Mise à jour du bulletin
                    $this->bull_num_notice = $this->id;
                    $requete = "update bulletins set num_notice=" . $this->bull_num_notice . " where bulletin_id=" . $this->bulletin_id;
                    pmb_mysql_query($requete);
                    // Mise à jour des liens bulletin -> notice mère
                    notice_relations::insert($this->bull_num_notice, $this->get_serial()->id, 'b', 1, 'up', false);
                }
            }
            // Pour la synchro rdf
            if ($pmb_synchro_rdf) {
                $synchro_rdf->addRdf(0, $this->bulletin_id);
            }
        } else {
            error_message_history($msg["notice_champs_perso"], $p_perso->error_message, 1);
            exit();
        }
        if ($pmb_ark_activate) {
            ArkModel::saveArkFromEntity($this);
        }
        return $this->bulletin_id;
    }

    public function delete_analysis()
    {
        global $pmb_archive_warehouse;

        if ($this->bulletin_id) {
            $requete = "SELECT analysis_notice FROM analysis WHERE analysis_bulletin=" . $this->bulletin_id;
            $myQuery2 = pmb_mysql_query($requete);
            while (($dep = pmb_mysql_fetch_object($myQuery2))) {
                $ana = new analysis($dep->analysis_notice);
                if ($pmb_archive_warehouse) {
                    static::save_to_agnostic_warehouse(array(
                        0 => $dep->analysis_notice
                    ), $pmb_archive_warehouse);
                }
                // Clean des vedettes
                $id_vedettes_links_deleted = static::delete_vedette_links($dep->analysis_notice);
                foreach ($id_vedettes_links_deleted as $id_vedette) {
                    $vedette_composee = new vedette_composee($id_vedette);
                    $vedette_composee->delete();
                }

                $ana->analysis_delete();
            }
        }
    }

    // ---------------------------------------------------------------
    // replace_form : affichage du formulaire de remplacement
    // ---------------------------------------------------------------
    public function replace_form()
    {
        global $bulletin_replace;
        global $msg, $charset;
        global $include_path;
        global $deflt_notice_replace_keep_categories;
        global $bulletin_replace_categories, $bulletin_replace_category;
        global $thesaurus_mode_pmb;

        if (! $this->bulletin_id) {
            require_once ("$include_path/user_error.inc.php");
            error_message($msg[161], $msg[162], 1, './catalog.php');
            return false;
        }
        $requete = "SELECT analysis_notice FROM analysis WHERE analysis_bulletin=" . $this->bulletin_id;
        $myQuery2 = pmb_mysql_query($requete);
        if (pmb_mysql_num_rows($myQuery2)) {
            $del_depouillement = "<label class='etiquette' for='del'>" . $msg['replace_bulletin_checkbox'] . "</label><input value='1' yes='' name='del' id='del' type='checkbox' checked>";
        }
        $bulletin_replace = str_replace('!!old_bulletin_libelle!!', $this->bulletin_numero . " [" . formatdate($this->date_date) . "] " . htmlentities($this->mention_date, ENT_QUOTES, $charset) . " " . htmlentities($this->bulletin_titre, ENT_QUOTES, $charset), $bulletin_replace);
        $bulletin_replace = str_replace('!!bul_id!!', $this->bulletin_id, $bulletin_replace);
        $bulletin_replace = str_replace('!!serial_id!!', $this->get_serial()->id, $bulletin_replace);
        $bulletin_replace = str_replace('!!del_depouillement!!', $del_depouillement, $bulletin_replace);
        if (! empty($deflt_notice_replace_keep_categories) && ! empty($this->categories)) {
            // categories
            $categories_to_replace = "";
            $nb_categories = count($this->categories);
            for ($i = 0; $i < $nb_categories; $i ++) {
                if (isset($this->categories[$i]["categ_id"]) && $this->categories[$i]["categ_id"]) {
                    $categ_id = $this->categories[$i]["categ_id"];
                } else {
                    $categ_id = 0;
                }
                $categ = new category($categ_id);
                $ptab_categ = str_replace('!!icateg!!', $i, $bulletin_replace_category);
                $ptab_categ = str_replace('!!categ_id!!', $categ_id, $ptab_categ);
                if ($thesaurus_mode_pmb)
                    $nom_thesaurus = '[' . $categ->thes->getLibelle() . '] ';
                else
                    $nom_thesaurus = '';
                $ptab_categ = str_replace('!!categ_libelle!!', htmlentities($nom_thesaurus . $categ->catalog_form, ENT_QUOTES, $charset), $ptab_categ);
                $categories_to_replace .= $ptab_categ;
            }
            $bulletin_replace_categories = str_replace('!!bulletin_replace_category!!', $categories_to_replace, $bulletin_replace_categories);
            $bulletin_replace_categories = str_replace('!!nb_categ!!', $nb_categories, $bulletin_replace_categories);

            $bulletin_replace = str_replace('!!bulletin_replace_categories!!', $bulletin_replace_categories, $bulletin_replace);
        } else {
            $bulletin_replace = str_replace('!!bulletin_replace_categories!!', "", $bulletin_replace);
        }
        print $bulletin_replace;
    }

    // ---------------------------------------------------------------
    // replace($by) : remplacement du périodique
    // ---------------------------------------------------------------
    public function replace($by, $del_article = 0)
    {
        global $pmb_synchro_rdf;
        global $keep_categories;
        global $notice_replace_links;
        global $pmb_ark_activate;

        // traitement des dépouillements du bulletin
        if ($del_article) {
            // suppression des notices de dépouillement
            $this->delete_analysis();
        } else {
            // sinon on ratache les dépouillements existants
            $requete = "UPDATE analysis SET analysis_bulletin=$by where analysis_bulletin=" . $this->bulletin_id;
            pmb_mysql_query($requete);
        }

        // gestion des liens
        $requete = "select num_notice from bulletins where bulletin_id=" . $this->bulletin_id;
        $result = pmb_mysql_query($requete);
        if ($result && pmb_mysql_num_rows($result)) {
            $num_notice = pmb_mysql_result($result, 0, 0);
            $requete = "select num_notice from bulletins where bulletin_id=" . $by;
            $result = pmb_mysql_query($requete);
            if ($result && pmb_mysql_num_rows($result)) {
                $num_notice_by = pmb_mysql_result($result, 0, 0);
                if ($num_notice && $num_notice_by) { // les deux bulletins ont bien une notice
                    notice_relations::replace_links($num_notice, $num_notice_by, $notice_replace_links);
                }
            }
        }

        // traitement des catégories (si conservation cochée)
        if ($keep_categories) {
            update_notice_categories_from_form(0, $by);
        }

        // ratachement des exemplaires
        $requete = "UPDATE exemplaires SET expl_bulletin=$by WHERE expl_bulletin=" . $this->bulletin_id;
        pmb_mysql_query($requete);

        // élimination des docs numériques
        $requete = "UPDATE explnum SET explnum_bulletin=$by WHERE explnum_bulletin=" . $this->bulletin_id;
        pmb_mysql_query($requete);

        // Mise à jour des articles reliés
        if ($pmb_synchro_rdf) {
            $synchro_rdf = new synchro_rdf();
            $requete = "SELECT analysis_notice FROM analysis WHERE analysis_bulletin='$by' ";
            $result = pmb_mysql_query($requete);
            while ($row = pmb_mysql_fetch_object($result)) {
                $synchro_rdf->delRdf($row->analysis_notice, 0);
                $synchro_rdf->addRdf($row->analysis_notice, 0);
            }
        }

        if ($pmb_ark_activate) {
            $arkEntityReplaced = ArkEntityPmb::getEntityClassFromType(TYPE_BULLETIN, $this->bulletin_id);
            $arkEntityReplacing = ArkEntityPmb::getEntityClassFromType(TYPE_BULLETIN, $by);
            $arkEntityReplaced->markAsReplaced($arkEntityReplacing);
        }
        $this->delete();
        return false;
    }

    // Suppression de bulletin
    public function delete()
    {
        global $pmb_synchro_rdf;
        global $pmb_ark_activate;

        // suppression des notices de dépouillement
        $this->delete_analysis();

        // synchro rdf
        if ($pmb_synchro_rdf) {
            $synchro_rdf = new synchro_rdf();
            $synchro_rdf->delRdf(0, $this->bulletin_id);
        }

        // suppression des exemplaires
        $req_expl = "select expl_id from exemplaires where expl_bulletin ='" . $this->bulletin_id . "' ";

        $result_expl = pmb_mysql_query($req_expl);
        while (($expl = pmb_mysql_fetch_object($result_expl))) {
            exemplaire::del_expl($expl->expl_id);
        }

        // expl numériques
        $req_explNum = "select explnum_id from explnum where explnum_bulletin=" . $this->bulletin_id . " ";
        $result_explNum = pmb_mysql_query($req_explNum);
        while (($explNum = pmb_mysql_fetch_object($result_explNum))) {
            $myExplNum = new explnum($explNum->explnum_id);
            $myExplNum->delete();
        }

        $requete = "delete from caddie_content using caddie, caddie_content where caddie_id=idcaddie and type='BULL' and object_id='" . $this->bulletin_id . "' ";
        pmb_mysql_query($requete);

        // Suppression des résas du bulletin
        $requete = "DELETE FROM resa WHERE resa_idbulletin=" . $this->bulletin_id;
        pmb_mysql_query($requete);

        // Suppression des résas du bulletin planifiées
        $requete = "DELETE FROM resa_planning WHERE resa_idbulletin=" . $this->bulletin_id;
        pmb_mysql_query($requete);

        // Suppression des transferts_demande
        $requete = "DELETE FROM transferts_demande using transferts_demande, transferts WHERE num_transfert=id_transfert and num_bulletin=" . $this->bulletin_id;
        pmb_mysql_query($requete);
        // Suppression des transferts
        $requete = "DELETE FROM transferts WHERE num_bulletin=" . $this->bulletin_id;
        pmb_mysql_query($requete);

        // suppression de la notice du bulletin
        $requete = "select num_notice from bulletins where bulletin_id=" . $this->bulletin_id;
        $res_nbul = pmb_mysql_query($requete);
        if (pmb_mysql_num_rows($res_nbul)) {
            $num_notice = pmb_mysql_result($res_nbul, 0, 0);
            if ($num_notice) {

                // suppression des vedettes
                $id_vedettes_links_deleted = static::delete_vedette_links($this->bulletin_id);
                foreach ($id_vedettes_links_deleted as $id_vedette) {
                    $vedette_composee = new vedette_composee($id_vedette);
                    $vedette_composee->delete();
                }

                static::del_notice($num_notice);
            }
        }

        scan_requests::clean_scan_requests_on_delete_record(0, $this->bulletin_id);

        // Suppression de ce bulletin
        $requete = "DELETE FROM bulletins WHERE bulletin_id=" . $this->bulletin_id;
        pmb_mysql_query($requete);
        audit::delete_audit(AUDIT_BULLETIN, $this->bulletin_id);

        if ($pmb_ark_activate) {
            $arkEntity = ArkEntityPmb::getEntityClassFromType(TYPE_BULLETIN, $this->bulletin_id);
            $arkEntity->markAsDeleted();
        }
    }

    public function get_serial()
    {
        return $this->serial;
    }

    public function get_record_header()
    {
        $serial_display = new serial_display($this->bulletin_notice, 1);
        return $serial_display->header;
    }

    public function get_record_isbd()
    {
        $serial_display = new serial_display($this->bulletin_notice, 1);
        return $serial_display->isbd;
    }

    // Donne les id des notices d'articles associées au bulletin
    public static function get_list_analysis($id)
    {
        $id = intval($id);
        $tab = array();
        $query = "SELECT analysis_notice FROM analysis WHERE analysis_bulletin = " . $id;
        $result = pmb_mysql_query($query);
        if ($result && pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $tab[] = $row->analysis_notice;
            }
        }
        return $tab;
    }

    public function move_form()
    {
        global $include_path, $bulletin_move, $msg;

        if (! $this->bulletin_id) {
            require_once ($include_path . '/user_error.inc.php');
            error_message($msg['bulletin_move'], $msg['4024'], 1, './catalog.php');
            return false;
        }
        $bulletin_move = str_replace('!!bul_id!!', $this->bulletin_id, $bulletin_move);

        print $bulletin_move;
    }

    // ---------------------------------------------------------------
    // move($to_serial) : déplacement du bulletin
    // ---------------------------------------------------------------
    public function move($to_serial)
    {
        // rattachement du bulletin au périodique
        $requete = 'UPDATE bulletins SET bulletin_notice = ' . $to_serial . ' WHERE bulletin_id=' . $this->bulletin_id;
        pmb_mysql_query($requete);

        return false;
    }

    protected function get_display_mode_selector($name)
    {
        return selector_model::get_instance($name)->get_display_mode('bulletin');
    }

    public static function get_notice_id_from_id($bulletin_id)
    {
        $bulletin_id = intval($bulletin_id);
        $query = "SELECT num_notice, bulletin_notice FROM bulletins WHERE bulletin_id = " . $bulletin_id;
        $result = pmb_mysql_query($query);
        $row = pmb_mysql_fetch_object($result);
        if ($row->num_notice) {
            return $row->num_notice; // Notice de bulletin
        } else {
            return $row->bulletin_notice; // Notice de périodique
        }
    }

    public static function get_date_date_from_id($bulletin_id)
    {
        $bulletin_id = intval($bulletin_id);
        $query = "SELECT date_date FROM bulletins WHERE bulletin_id = " . $bulletin_id;
        $result = pmb_mysql_query($query);
        $row = pmb_mysql_fetch_object($result);
        return $row->date_date;
    }

    public static function get_pattern_link()
    {
        global $base_path;
        return $base_path . '/catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!id!!';
    }

    public static function get_permalink($bulletin_id, $parent_id = 0)
    {
        global $base_path;
        return $base_path . '/catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=' . intval($bulletin_id);
    }

    protected static function format_url($url = '')
    {
        global $base_path;

        if (isset(static::$controller) && is_object(static::$controller)) {
            return static::$controller->get_url_base() . $url;
        } else {
            return $base_path . '/catalog.php?categ=serials&sub=bulletinage' . $url;
        }
    }
}

// fin définition classe

// mark dep

/*
 * ------------------------------------------------------------------------------------
 * classe analysis : classe de gestion des dépouillements
 * ---------------------------------------------------------------------------------------
 */
class analysis extends notice
{

    public $id_bulletinage = 0;

    // id du bulletinage contenant ce dépouillement
    public $bulletinage;

    // instance du bulletin (bulletinage)
    public $biblio_level = 'a';

    // niveau bibliographique
    public $hierar_level = '2';

    // niveau hiérarchique
    public $typdoc = '';

    // type de document (imprimé par défaut)
    public $indexint_lib = '';

    // libelle indexint
    public $action = '';

    // cible du formulaire généré par la méthode do_form
    public $pages = '';

    // mention de pagination
    public $responsabilites_dep = array(
        "responsabilites" => array(),
        "auteurs" => array()
    );

    // les auteurs
    protected static $vedette_composee_config_filename = 'analysis_authors';

    // constructeur
    public function __construct($analysis_id, $bul_id = 0)
    {
        global $deflt_notice_is_new;
        global $deflt_notice_statut_analysis;
        // param : l'article hérite-t-il de l'URL de la notice chapeau
        global $pmb_serial_link_article;
        // param : l'article hérite-t-il de l'URL de la vignette de la notice chapeau
        global $pmb_serial_thumbnail_url_article;
        // param : l'article hérite-t-il de l'URL de la vignette de la notice bulletin
        global $pmb_bulletin_thumbnail_url_article;
        global $opac_url_base;
        $this->id = intval($analysis_id);
        if ($bul_id)
            $this->id_bulletinage = $bul_id;

        if ($this->id) {
            $this->fetch_analysis_data();
        } else {
            $this->is_new = $deflt_notice_is_new;
        }
        $tmp_link = $this->notice_link;

        // On vide les liens entre notices car ils sont appliqués pour le serial dans le $this
        $this->bulletinage = new bulletinage($this->id_bulletinage);
        if ($this->bulletinage->bulletin_id) {
            $this->notice_link = array();
            $this->notice_link = $tmp_link;
        }
        unset($tmp_link);

        // si c'est une création, on renseigne les valeurs héritées de la notice chapeau
        if (! $this->id) {
            $this->langues = $this->get_bulletinage()->get_serial()->langues;
            $this->languesorg = $this->get_bulletinage()->get_serial()->languesorg;
            if ($deflt_notice_statut_analysis) {
                $this->statut = $deflt_notice_statut_analysis;
            } else {
                if ($this->get_bulletinage()->statut) {
                    $this->statut = $this->get_bulletinage()->statut;
                } else {
                    $this->statut = $this->get_bulletinage()->get_serial()->statut;
                }
            }
            // Héritage du lien de la notice chapeau
            if ($pmb_serial_link_article) {
                $this->lien = $this->get_bulletinage()->get_serial()->lien;
                $this->eformat = $this->get_bulletinage()->get_serial()->eformat;
            }
            // Héritage du lien de la vignette de la notice chapeau
            if ($pmb_serial_thumbnail_url_article) {
			    $this->thumbnail_url = $opac_url_base."thumbnail.php?type=1&id=".$this->get_bulletinage()->get_serial()->id;
            }
            // Héritage du lien de la vignette de la notice bulletin
			if ($pmb_bulletin_thumbnail_url_article && !empty($this->get_bulletinage()->bull_num_notice)) {
			    $this->thumbnail_url = $opac_url_base."thumbnail.php?type=1&id=".$this->get_bulletinage()->bull_num_notice;
            }
        }
        // afin d'avoir forcément un typdoc
        if (! $this->typdoc) {
            global $xmlta_doctype_analysis;
            if ($xmlta_doctype_analysis) {
                $this->typdoc = $xmlta_doctype_analysis;
            } else {
                if ($this->get_bulletinage()->typdoc) {
                    $this->typdoc = $this->get_bulletinage()->typdoc;
                } else
                    $this->typdoc = $this->get_bulletinage()->get_serial()->typdoc;
            }
        }
        return $this->id;
    }

    // récupération des infos en base
    public function fetch_analysis_data()
    {
        $this->fetch_data();

        // type du document
        $this->typdoc = $this->type_doc;

        // libelle des auteurs
        $this->responsabilites_dep = $this->responsabilites;

        // Mention de pagination
        $this->pages = $this->npages;
    }

    protected function get_analysis_content_form($notice_type = false)
    {
        global $msg, $charset;
        global $analysis_top_content_form, $pdeptab, $xmlta_indexation_lang;

        $content_form = $analysis_top_content_form;

        $content_form = str_replace('!!id!!', $this->get_bulletinage()->get_serial()->id, $content_form);

        // mise à jour de l'onglet 0
        //Titre propre
        $element = interface_entity_record_element::get_instance('el0Child_0', 'f_tit1', '237');
        $element->add_input_node('text', $this->tit1, ['data-form-name' => 'f_tit1', 'data-pmb-deb-rech' => '1']);
        $pdeptab[0] = str_replace('!!element_tit1!!', $element->get_display(), $pdeptab[0]);

//         $pdeptab[0] = str_replace('!!tit2!!', htmlentities($this->tit2, ENT_QUOTES, $charset), $pdeptab[0]);

        // Titre parallèle
        $element_tit3 = $this->get_input_text_content_form('el0Child_1', 'tit3', '239');
        $pdeptab[0] = str_replace('!!element_tit3!!', $element_tit3, $pdeptab[0]);

        // Complément du titre
        $element_tit4 = $this->get_input_text_content_form('el0Child_2', 'tit4', '240');
        $pdeptab[0] = str_replace('!!element_tit4!!', $element_tit4, $pdeptab[0]);

        $content_form = str_replace('!!tab0!!', $pdeptab[0], $content_form);

        // mise à jour de l'onglet 1
        // constitution de la mention de responsabilité
        // $this->responsabilites
        $content_form = str_replace('!!tab1!!', $this->get_tab_responsabilities_form(), $content_form);

        // mise à jour de l'onglet 2
        $pdeptab[2] = str_replace('!!pages!!', htmlentities($this->pages, ENT_QUOTES, $charset), $pdeptab[2]);

        $content_form = str_replace('!!tab2!!', $pdeptab[2], $content_form);

        // mise à jour de l'onglet 3 (notes)
        $content_form = str_replace('!!tab3!!', $this->get_tab_notes_form(), $content_form);

        // mise à jour de l'onglet 4
        $content_form = str_replace('!!tab4!!', $this->get_tab_indexation_form(), $content_form);

        // mise à jour de l'onglet 5 : Langues
        // langues répétables
        $content_form = str_replace('!!tab5!!', $this->get_tab_lang_form(), $content_form);

        // mise à jour de l'onglet 6
        $content_form = str_replace('!!tab6!!', $this->get_tab_links_form(), $content_form);

        // Gestion des titres uniformes, onglet 230
        global $pmb_use_uniform_title;
        if ($pmb_use_uniform_title) {
            $content_form = str_replace('!!tab230!!', $this->get_tab_uniform_title_form(), $content_form);
        }

        // Mise à jour de l'onglet 7
        $content_form = str_replace('!!tab7!!', $this->get_tab_customs_perso_form(), $content_form);

        // Liens vers d'autres notices
        if ($this->duplicate_from_id) {
            $notice_relations = notice_relations_collection::get_object_instance($this->duplicate_from_id);
        } else {
            $notice_relations = notice_relations_collection::get_object_instance($this->id);
        }
        $content_form = str_replace('!!tab13!!', $notice_relations->get_form($this->notice_link, 'a', ($this->duplicate_from_id ? true : false)), $content_form);

        // champs de gestion
        $content_form = str_replace('!!tab8!!', $this->get_tab_gestion_fields(), $content_form);
        $content_form = str_replace('!!indexation_lang_sel!!', ($this->indexation_lang ? $this->indexation_lang : $xmlta_indexation_lang), $content_form);

        // autorité personnalisées
        if ($this->duplicate_from_id) {
            $authperso = new authperso_notice($this->duplicate_from_id);
        } else {
            $authperso = new authperso_notice($this->id);
        }
        $authperso_tpl = $authperso->get_form();
        $content_form = str_replace('!!authperso!!', $authperso_tpl, $content_form);

        // map
        global $pmb_map_activate;
        if ($pmb_map_activate) {
            $content_form = str_replace('!!tab14!!', $this->get_tab_map_form(), $content_form);
        } else {
            $content_form = str_replace('!!tab14!!', "", $content_form);
        }
        if ($notice_type) {
            global $analysis_type_form;

            $date_clic = "onClick=\"openPopUp('./select.php?what=calendrier&caller=notice&date_caller=&param1=f_bull_new_date&param2=date_date_lib&auto_submit=NO&date_anterieure=YES', 'calendar')\"  ";
            $date_date = "<input type='hidden' id='f_bull_new_date' name='f_bull_new_date' value='' />
				<input class='saisie-10em' type='text' name='date_date_lib' value='' />
				<input class='bouton' type='button' name='date_date_lib_bouton' value='" . $msg["bouton_calendrier"] . "' " . $date_clic . " />";

            $analysis_type_form = str_replace("!!date_date!!", $date_date, $analysis_type_form);
            $analysis_type_form = str_replace("!!perio_type_new!!", "checked", $analysis_type_form);
            $analysis_type_form = str_replace("!!bull_type_new!!", "checked", $analysis_type_form);
            $analysis_type_form = str_replace("!!perio_type_use_existing!!", "", $analysis_type_form);
            $analysis_type_form = str_replace("!!bull_type_use_existing!!", "", $analysis_type_form);

            $content_form = str_replace("!!type_catal!!", $analysis_type_form, $content_form);
        } else {
            $content_form = str_replace("!!type_catal!!", "", $content_form);
        }
        return $content_form;
    }

    public function get_analysis_form($notice_type = false)
    {
        global $msg;

        // initialisation avec les paramètres du user :
        if (! $this->langues) {
            global $value_deflt_lang;
            if ($value_deflt_lang) {
                $lang = new marc_list('lang');
                $this->langues[] = array(
                    'lang_code' => $value_deflt_lang,
                    'langue' => $lang->table[$value_deflt_lang]
                );
            }
        }

        $interface_form = new interface_entity_analysis_form('notice');
        $interface_form->set_enctype('multipart/form-data');
        if ($this->id) {
            $interface_form->set_label($msg['4023']);
            $interface_form->set_document_title(($this->tit1 ? $this->tit1 . ' - ' : '') . $msg['4023']);
        } else {
            $interface_form->set_label($msg['4022']);
            $interface_form->set_document_title($msg['4022']);
        }

        $interface_form->set_object_id($this->id)
            ->set_hierar_level($this->hierar_level)
            ->set_code($this->code)
            ->set_type_doc($this->typdoc)
            ->set_duplicable(true)
            ->set_serial_id($this->get_bulletinage()
            ->get_serial()->id)
            ->set_bulletin_id($this->id_bulletinage)
            ->set_content_form($this->get_analysis_content_form($notice_type))
            ->set_table_name('notices')
            ->set_field_focus('f_tit1')
            ->set_url_base(static::format_url());
        return $interface_form->get_display();
    }

    // génération du form de saisie
    public function analysis_form($notice_type = false)
    {
        global $style;
        global $include_path, $class_path;

        // inclusion de la feuille de style des expandables
        print $style;

        return $this->get_analysis_form($notice_type);
    }

    public function set_properties_from_form()
    {
        global $pages;

        parent::set_properties_from_form();
        $this->npages = clean_string($pages);
        // insert de year à partir de la date de parution du bulletin
        if ($this->get_bulletinage()->date_date) {
            $this->year = substr($this->get_bulletinage()->date_date, 0, 4);
        }
        $this->date_parution = $this->get_bulletinage()->date_date;
    }

    public function save()
    {
        global $id_sug;
        if (! $this->id) {
            $is_creation = true;
        } else {
            $is_creation = false;
        }
        $saved = parent::save();
        if ($saved && $is_creation) {
            $requete = 'INSERT INTO analysis SET';
            $requete .= ' analysis_bulletin=' . $this->id_bulletinage;
            $requete .= ', analysis_notice=' . $this->id;
            pmb_mysql_query($requete);

            if ($id_sug && $this->id) {
                $req_sug = "update suggestions set num_notice='" . $this->id . "' where id_suggestion='" . $id_sug . "'";
                pmb_mysql_query($req_sug);
            }
        }
        return $saved;
    }

    public static function getBulletinIdFromAnalysisId($analysis_id = 0)
    {
        if (! $analysis_id)
            return 0;
        $q = "select analysis_bulletin from analysis where analysis_notice='" . $analysis_id . "' ";
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r))
            return pmb_mysql_result($r, 0, 0);
        return 0;
    }

    // fonction de mise à jour d'une entrée MySQL de bulletinage
    public function analysis_update($values, $other_fields = "")
    {
        global $pmb_map_activate;

        // clean des vieilles nouveautés
        static::cleaning_is_new();

        if (is_array($values)) {
            $this->biblio_level = 'a';
            $this->hierar_level = '2';
            $this->typdoc = $values['typdoc'];
            $this->statut = $values['statut'];
            $this->commentaire_gestion = $values['f_commentaire_gestion'];
            $this->thumbnail_url = $values['f_thumbnail_url'];
            $this->tit1 = $values['f_tit1'];
            $this->tit2 = $values['f_tit2'];
            $this->tit3 = $values['f_tit3'];
            $this->tit4 = $values['f_tit4'];
            $this->n_gen = $values['f_n_gen'];
            $this->n_contenu = $values['f_n_contenu'];
            $this->n_resume = $values['f_n_resume'];
            $this->indexint = $values['f_indexint_id'];
            $this->index_l = $values['f_indexation'];
            $this->lien = $values['f_lien'];
            $this->eformat = $values['f_eformat'];
            $this->pages = $values['pages'];
            $this->signature = $values['signature'];
            $this->indexation_lang = $values['indexation_lang'];
            $this->notice_is_new = $values['notice_is_new'];
            $this->num_notice_usage = $values['num_notice_usage'];

            // insert de year à partir de la date de parution du bulletin
            if ($this->date_date) {
                $this->year = substr($this->date_date, 0, 4);
            }
            $this->date_parution_perio = $this->date_date;

            // construction de la requête :
            $data = "typdoc='" . $this->typdoc . "'";
            $data .= ", statut='" . $this->statut . "'";
            $data .= ", tit1='" . $this->tit1 . "'";
            $data .= ", tit3='" . $this->tit3 . "'";
            $data .= ", tit4='" . $this->tit4 . "'";
            $data .= ", year='" . $this->year . "'";
            $data .= ", npages='" . $this->pages . "'";
            $data .= ", n_contenu='" . $this->n_contenu . "'";
            $data .= ", n_gen='" . $this->n_gen . "'";
            $data .= ", n_resume='$this->n_resume'";
            $data .= ", lien='" . $this->lien . "'";
            $data .= ", eformat='" . $this->eformat . "'";
            $data .= ", indexint='" . $this->indexint . "'";
            $data .= ", index_l='" . clean_tags($this->index_l) . "'";
            $data .= ", niveau_biblio='" . $this->biblio_level . "'";
            $data .= ", niveau_hierar='" . $this->hierar_level . "'";
            $data .= ", commentaire_gestion='" . $this->commentaire_gestion . "'";
            $data .= ", thumbnail_url='" . $this->thumbnail_url . "'";
            $data .= ", signature='" . $this->signature . "'";
            $data .= ", date_parution='" . $this->date_parution_perio . "'";
            $data .= ", indexation_lang='" . $this->indexation_lang . "'";
            $data .= ", notice_is_new='" . $this->notice_is_new . "'";
            $data .= ", num_notice_usage='" . $this->num_notice_usage . "'
			$other_fields";
            $result = 0;
            if (! $this->id) {

                // si c'est une création
                // fabrication de la requête finale
                $requete = "INSERT INTO notices SET $data , create_date=sysdate(), update_date=sysdate() ";
                $myQuery = pmb_mysql_query($requete);
                $this->id = pmb_mysql_insert_id();
                if ($myQuery)
                    $result = $this->id;
                // si l'insertion est OK, il faut créer l'entrée dans la table 'analysis'
                if ($this->id) {

                    // autorité personnalisées
                    $authperso = new authperso_notice($this->id);
                    $authperso->save_form();

                    // map
                    if ($pmb_map_activate) {
                        $map = new map_edition_controler(TYPE_RECORD, $this->id);
                        $map->save_form();
                        $map_info = new map_info($this->id);
                        $map_info->save_form();
                    }
                    // Mise à jour des index de la notice
                    notice::majNoticesTotal($this->id);
                    audit::insert_creation(AUDIT_NOTICE, $this->id);
                    $requete = 'INSERT INTO analysis SET';
                    $requete .= ' analysis_bulletin=' . $this->id_bulletinage;
                    $requete .= ', analysis_notice=' . $this->id;
                    $myQuery = pmb_mysql_query($requete);
                }
            } else {

                $requete = "UPDATE notices SET $data , update_date=sysdate() WHERE notice_id='" . $this->id . "' LIMIT 1";
                $myQuery = pmb_mysql_query($requete);

                // autorité personnalisées
                $authperso = new authperso_notice($this->id);
                $authperso->save_form();

                // map
                if ($pmb_map_activate) {
                    $map = new map_edition_controler(TYPE_RECORD, $this->id);
                    $map->save_form();
                    $map_info = new map_info($this->id);
                    $map_info->save_form();
                }
                // Mise à jour des index de la notice
                notice::majNoticesTotal($this->id);
                audit::insert_modif(AUDIT_NOTICE, $this->id);
                if ($myQuery)
                    $result = $this->id;
            }

            // vignette de la notice uploadé dans un répertoire
            $id = $this->id;
            $uploaded_thumbnail_url = thumbnail::create($id);
            if ($uploaded_thumbnail_url) {
                $req = "update notices set thumbnail_url='" . $uploaded_thumbnail_url . "' where notice_id ='" . $id . "'";
                pmb_mysql_query($req);
            }
            return $result;
        } // if(is_array($values))
    }

    // suppression d'un dépouillement
    public function analysis_delete()
    {
        static::del_notice($this->id);

        return true;
    }

    public function move_form()
    {
        global $include_path, $analysis_move, $msg;

        if (! $this->id) {
            require_once ($include_path . '/user_error.inc.php');
            error_message($msg['161'], $msg['162'], 1, './catalog.php');
            return false;
        }
        $analysis_move = str_replace('!!analysis_id!!', $this->id, $analysis_move);
        $analysis_move = str_replace('!!bul_id!!', $this->id_bulletinage, $analysis_move);

        print $analysis_move;
    }

    // ---------------------------------------------------------------
    // move($to_bul) : déplacement du dépouillement
    // ---------------------------------------------------------------
    public function move($to_bul)
    {
        global $pmb_synchro_rdf;

        // rattachement du dépouillement
        $requete = 'UPDATE analysis SET analysis_bulletin=' . $to_bul . ' WHERE analysis_notice=' . $this->id;
        pmb_mysql_query($requete);

        // dates
        $myBul = new bulletinage($to_bul);
        $year = substr($myBul->date_date, 0, 4);
        $date_parution = $myBul->date_date;

        $requete = 'UPDATE notices SET year="' . $year . '", date_parution="' . $date_parution . '", update_date=sysdate() WHERE notice_id=' . $this->id . ' LIMIT 1';
        pmb_mysql_query($requete);

        // Indexation du dépouillement
        notice::majNoticesTotal($this->id);
        audit::insert_modif(AUDIT_NOTICE, $this->id);
        if ($pmb_synchro_rdf) {
            $synchro_rdf = new synchro_rdf();
            $synchro_rdf->delRdf($this->id, 0);
            $synchro_rdf->addRdf($this->id, 0);
        }

        return false;
    }

    public function get_bulletinage()
    {
        return $this->bulletinage;
    }

    protected function get_display_mode_selector($name)
    {
        return selector_model::get_instance($name)->get_display_mode('analysis');
    }

    public static function get_pattern_link()
    {
        global $base_path;
        return $base_path . '/catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!bul_id!!&art_to_show=!!id!!';
    }

    public static function get_permalink($notice_id, $parent_id = 0)
    {
        global $base_path;
        return $base_path . '/catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=' . $parent_id . '&art_to_show=' . $notice_id;
    }

    protected static function format_url($url = '')
    {
        global $base_path;

        if (isset(static::$controller) && is_object(static::$controller)) {
            return static::$controller->get_url_base() . $url;
        } else {
            return $base_path . '/catalog.php?categ=serials&sub=analysis' . $url;
        }
    }
} // fin définition classe

/*
  aide-mémoire
  à l'issue de l'héritage mutiple, on a les propriétés :

  class serial

    $serial_id            id de ce périodique
    $biblio_level         niveau bibliographique
    $hierar_level         niveau hiérarchique
    $typdoc               type UNIMARC du document (imprimé par défaut)
    $tit1                 titre propre
    $tit3                 titre parallèle
    $tit4                 complément du titre propre
    $ed1_id               id de l'éditeur 1
    $ed1                  forme affichable de l'éditeur 1
    $ed2_id               id de l'éditeur 2
    $ed2                  forme affichable de l'éditeur 2
    $n_gen                note générale
    $n_resume             note de résumé
    $index_l              indexation libre
    $lien                 URL associée
    $eformat              type de la ressource électronique
    $action               cible du formulaire généré par la méthode do_form

  class bulletinage

    $bulletin_id         id de ce bulletinage
    $bulletin_titre      titre propre
    $bulletin_numero     mention de numéro sur la publication
    $bulletin_notice     id notice parent = id du périodique relié
    $bulletin_cb         code barre EAN13 (+addon)
    $mention_date        mention de date sur la publication
    $date_date           date de création de l'entrée de bulletinage
    $display             forme à afficher pour prêt, listes, etc...

  class analysis

	$analysis_id            id de ce dépouillement
	$id_bulletinage         id du bulletinage contenant ce dépouillement
	$analysis_biblio_level  niveau bibliographique
	$analysis_hierar_level  niveau hiérarchique
	$analysis_typdoc        type de document (imprimé par défaut)
	$analysis_tit1          titre propre
	$analysis_tit3          titre parallèle
	$analysis_tit4          complément du titre propre
	$analysis_aut1_id       id de l'auteur 1
	$analysis_aut1          ** forme affichable de l'auteur 1
	$analysis_f1_code       code de fonction auteur 1
	$analysis_f1            ** fonction auteur 1
	$analysis_aut2_id       id de l'auteur 2
	$analysis_aut2          ** forme affichable de l'auteur 2
	$analysis_f2_code       code de fonction auteur 2
	$analysis_f2            ** fonction auteur 1
	$analysis_aut3_id       id de l'auteur 3
	$analysis_aut3          ** forme affichable de l'auteur 3
	$analysis_f3_code       code de fonction auteur 3
	$analysis_f3            ** fonction auteur 3
	$analysis_aut4_id       id de l'auteur 4
	$analysis_aut4          ** forme affichable de l'auteur 4
	$analysis_f4_code       code de fonction auteur 4
	$analysis_f4            ** fonction auteur 4
	$analysis_ed1_id        id de l'éditeur 1
	$analysis_ed1           forme affichable de l'éditeur 1
	$analysis_ed2_id        id de l'éditeur 2
	$analysis_ed2           forme affichable de l'éditeur 2
	$analysis_n_gen         note générale
	$analysis_n_resume      note de résumé
	$analysis_index_l       indexation libre
	$analysis_eformat  	 format de la ressource
	$analysis_lien          lien vers une ressource électronique
	$action          	 cible du formulaire généré par la méthode do_form
	$analysis_pages         mention de pagination


*/