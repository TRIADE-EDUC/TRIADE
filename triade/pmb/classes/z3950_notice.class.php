<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Emmanuel PACAUD < emmanuel.pacaud@univ-poitiers.fr>            |
// +-------------------------------------------------+
// $Id: z3950_notice.class.php,v 1.268.2.2.2.9 2025/05/13 08:47:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $base_path, $class_path, $include_path, $msg;

require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/notice.class.php");
require_once($base_path."/catalog/z3950/manual_categorisation.inc.php");
global $pmb_indexation_lang;
if ($pmb_indexation_lang) {
    require_once("$include_path/marc_tables/$pmb_indexation_lang/empty_words");
}
require_once("$include_path/isbn.inc.php");
require_once("$class_path/iso2709.class.php");
require_once("$class_path/author.class.php");
require_once("$class_path/serie.class.php");
require_once("$class_path/editor.class.php");
require_once("$class_path/collection.class.php");
require_once("$class_path/subcollection.class.php");
require_once("$class_path/origine_notice.class.php");
require_once("$class_path/audit.class.php");
require_once("$class_path/expl.class.php");
require_once("$include_path/templates/expl.tpl.php");
require_once("$class_path/acces.class.php");
require_once("$class_path/upload_folder.class.php");
require_once($class_path."/category_auto.class.php");
require_once("$include_path/explnum.inc.php");
require_once("$class_path/serials.class.php");
require_once($class_path."/notice_doublon.class.php");
require_once("$class_path/origin_authorities.class.php");
require_once($class_path."/synchro_rdf.class.php");
require_once("$class_path/docs_section.class.php");
require_once("$class_path/docs_codestat.class.php");
require_once("$class_path/docs_type.class.php");
require_once($class_path."/notice_relations.class.php");
require_once($class_path."/thumbnail.class.php");
require_once($class_path."/indexation_stack.class.php");

global $categ, $action, $notice_org;
if ($categ == 'z3950' && in_array($action, array('import', 'integrer', 'integrerexpl')) && $notice_org) {
    $requete="select z_marc,fichier_func from z_notices, z_bib where znotices_id='".$notice_org."' and znotices_bib_id=bib_id";
    $resultat=pmb_mysql_query($requete);
    $notice_org=pmb_mysql_result($resultat, 0, 0);
    $z_bib_fichier_func=pmb_mysql_result($resultat, 0, 1);
}

//Recherche de la fonction auxiliaire d'intégration
if (!isset($z_bib_fichier_func) || !$z_bib_fichier_func) {
    global $z3950_import_modele;
    if ($z3950_import_modele) {
        if (file_exists($base_path."/catalog/z3950/".$z3950_import_modele)) {
            require_once($base_path."/catalog/z3950/".$z3950_import_modele);
        } elseif ($categ != 'param') { // On n'affiche pas l'erreur sur la page de paramètres sinon on tourne en rond !
            error_message("", sprintf($msg["admin_error_file_import_modele_z3950"], $z3950_import_modele), 1, "./admin.php?categ=param&form_type_param=z3950&form_sstype_param=import_modele#justmodified");
            exit;
        }
    } else {
        require_once($base_path."/catalog/z3950/func_other.inc.php");
    }
} else {
    require_once($base_path."/catalog/z3950/".$z_bib_fichier_func);
}

class z3950_notice
{
    public $id;
    public $type_import;
    public $bibliographic_level;
    public $hierarchic_level;
    public $document_type;
    public $year;
    public $statut;


    public $source_id;
    public $titles;
    public $serie_id;
    public $serie;
    public $serie_200;
    public $nbr_in_serie;

    public $authors;
    public $author_functions;

    public $editors;
    public $collection;
    public $subcollection;
    public $nbr_in_collection;
    public $mention_edition;	// mention d'édition (1ère, deuxième...)
    public $isbn;

    public $page_nbr;
    public $illustration;
    public $size;
    public $prix;
    public $accompagnement;

    public $general_note;
    public $content_note;
    public $abstract_note;

    public $internal_index;

    public $dewey ;
    public $free_index;
    public $matieres_index;

    public $link_url;
    public $link_format;

    public $language_code;
    public $original_language_code;

    public $action;

	public $origine_notice = array();
    public $orinot_id = 1 ;

	public $aut_array = array(); // tableau des auteurs
	public $categories = array();// les categories
    public $categorisation_type = "categorisation_auto";
	public $titres_uniformes = array();

	public $info_600_3 = array();
	public $info_600_a = array();
	public $info_600_b = array();
	public $info_600_c = array();
	public $info_600_d = array();
	public $info_600_f = array();
	public $info_600_g = array();
	public $info_600_j = array();
	public $info_600_p = array();
	public $info_600_t = array();
	public $info_600_x = array();
	public $info_600_y = array();
	public $info_600_z = array();

	public $info_601_3 = array();
	public $info_601_a = array();
	public $info_601_b = array();
	public $info_601_c = array();
	public $info_601_d = array();
	public $info_601_e = array();
	public $info_601_f = array();
	public $info_601_g = array();
	public $info_601_h = array();
	public $info_601_j = array();
	public $info_601_t = array();
	public $info_601_x = array();
	public $info_601_y = array();
	public $info_601_z = array();

	public $info_602_3 = array();
	public $info_602_a = array();
	public $info_602_f = array();
	public $info_602_j = array();
	public $info_602_t = array();
	public $info_602_x = array();
	public $info_602_y = array();
	public $info_602_z = array();

	public $info_604_3 = array();
	public $info_604_a = array();
	public $info_604_h = array();
	public $info_604_i = array();
	public $info_604_j = array();
	public $info_604_k = array();
	public $info_604_l = array();
	public $info_604_x = array();
	public $info_604_y = array();
	public $info_604_z = array();

	public $info_605_3 = array();
	public $info_605_a = array();
	public $info_605_h = array();
	public $info_605_i = array();
	public $info_605_k = array();
	public $info_605_l = array();
	public $info_605_m = array();
	public $info_605_n = array();
	public $info_605_q = array();
	public $info_605_r = array();
	public $info_605_s = array();
	public $info_605_u = array();
	public $info_605_w = array();
	public $info_605_j = array();
	public $info_605_x = array();
	public $info_605_y = array();
	public $info_605_z = array();

	public $info_606_3 = array();
	public $info_606_a = array();
	public $info_606_j = array();
	public $info_606_x = array();
	public $info_606_y = array();
	public $info_606_z = array();

	public $info_607_3 = array();
	public $info_607_a = array();
	public $info_607_j = array();
	public $info_607_x = array();
	public $info_607_y = array();
	public $info_607_z = array();

	public $info_608_3 = array();
	public $info_608_a = array();
	public $info_608_j = array();
	public $info_608_x = array();
	public $info_608_y = array();
	public $info_608_z = array();

	public $tu_500 = array();
	public $tu_500_i = array();
	public $tu_500_j = array();
	public $tu_500_l = array();
	public $tu_500_n = array();
	public $tu_500_r = array();
	public $tu_500_s = array();

	public $info_503 = array();
	public $info_503_d = array();
	public $info_503_j = array();

	public $info_900 = array();

    //bulletin
    public $bull_id;
    public $bull_num;
    public $bull_date;
    public $bull_mention;
    public $bull_titre;
    public $bull_notice;
    //perio
    public $perio_titre;
    public $perio_issn;
    public $perio_id;

    public $thumbnail_url = "";
	public $doc_nums = array();
	public $exemplaires = array();

    public $message_retour="";
    public $notice="";
    public $notice_type;

    public $bt_integr_value = '';
    public $bt_undo_value = '';
    public $bt_undo_action='';

    //upload_vignette
    public $flag_upload_vignette="";

    public $notice_is_new=0;
    public $notice_date_is_new="0000-00-00 00:00:00"; // date nouveauté
    public $commentaire_gestion="";
    public $indexation_lang="";
    public $libelle_form="";
    public $signature="";
    public $notice_id = 0;
    public $unimarc_id = 0;
    public $notices_crees = [];
    public $bulletins_crees = [];
    public $notices_a_creer = [];
    public $bulletins_a_creer = [];

    protected static $long_maxi_code;

    /**
     * Key = Identifiant unimarc (champ 001)
     * Value = Identifiant dans le PMB
     * @var array
     */
	public static $record_tmp = array();

    /**
     * Key = Identifiant unimarc du periodique (champ 001)
     * Second Key = HASH (md5) num + date + mention du bulletin
     * Value = Identifiant Bulletin dans le PMB
     * @var array[]
     */
	public static $issue_tmp = array();

    /**
     * Nombre incrémenté pour les bulletins génériques
     * @var integer
     */
    protected static $issue_tmp_num = 0;

    /**
     *
     * @var string
     */
    public const LINK = "lnk";

    /**
     *
     * @var string
     */
    public const TYPE_LINK = "type_lnk";

    /**
     *
     * @var string
     */
    public $from_result = false;

    public function __construct($type, $marc = null, $source_id=0, $from_result = false)
    {
        $this->type_import = strtolower($type);
        $this->from_result = $from_result;
        switch ($this->type_import) {
            case 'form':
                $this->from_form();
                break;
            case 'from_scratch':
                break;
            default:
                if ($marc != null) {
                    if ($this->type_import == 'sutrs') {
                        $this->type_import= 'unimarc';
                    }
                    $this->notice_type=$this->type_import;
                    $this->notice=$marc;
                    $this->source_id = intval($source_id);
                    $record = new iso2709_record($marc, AUTO_UPDATE, $this->type_import);

                    if ($this->type_import == 'unimarc') {
                        $this->from_unimarc($record);
                    } else {
                        $this->from_usmarc($record);
                    }

                    if (function_exists("traite_info_subst")) {
                        traite_info_subst($this);
                    }
                }
                break;
        }
    }

    public static function substitute($tag, $value, &$string)
    {
        global $charset;
        $string = str_replace("!!$tag!!", htmlentities(trim($value ?? ''), ENT_QUOTES, $charset), $string);
    }

    public function insert_in_database()
    {
        global $class_path;
        global $gestion_acces_active, $gestion_acces_user_notice, $gestion_acces_empr_notice;
        global $res_prf, $chk_rights;
        global $pmb_synchro_rdf;
        global $opac_url_base,$pmb_notice_img_folder_id;
        global $xmlta_doctype_serial;
        global $thesaurus_concepts_active, $msg;

        if (empty($this->titles[0])) {
            // La notice n'a pas de titre
            pmb_mysql_query("INSERT INTO error_log (error_origin, error_text) VALUES ('import_" . addslashes(SESSid) . ".inc', '" . addslashes($msg['277']) . "') ");
            return;
        }

        if ($this->bibliographic_level=="s" && $this->hierarchic_level=="2") {
            $this->bibliographic_level = "b";
        }

        $new_notice = 0;
        $notice_retour = 0;
        if (!isset(static::$long_maxi_code)) {
            static::$long_maxi_code = pmb_mysql_field_len(pmb_mysql_query("SELECT code FROM notices limit 1"), 0);
        }
        $isbn = rtrim(substr($this->isbn, 0, static::$long_maxi_code));
        if ($isbn != "") {
            if (isISBN($isbn)) {
                if (strlen($isbn)==13) {
                    $isbn1=formatISBN($isbn, 13);
                } else {
                    $isbn1=formatISBN($isbn, 10);
                }
            }
            $sql_rech="select notice_id from notices where code = '".$isbn."' ";
            if ($isbn1) {
                $sql_rech.=" or code='".$isbn1."' ";
            }
            $sql_result_rech = pmb_mysql_query($sql_rech) or die("Couldn't select notice ! = ".$sql_rech);
            if (pmb_mysql_num_rows($sql_result_rech) == 0) {
                $new_notice=1;
            } else {
                $new_notice=0;
                $lu = pmb_mysql_fetch_array($sql_result_rech);
                $notice_retour = $lu['notice_id'];
            }
        } else {
            $new_notice = 1;
        }

        if ($new_notice == 0) {
			return array($new_notice, $notice_retour);
        }

        // GESTION DES EDITEURS
		$editor_ids = array();
        for ($i = 0; $i < 2; $i++) {
            if (isset($this->editors[$i]['id']) && $this->editors[$i]['id']) {
                $editor_ids[$i] = $this->editors[$i]['id'];
            } else {
                $editor_ids[$i] = editeur::import($this->editors[$i]);
            }
        }

        // GESTION DES COLLECTIONS
        $this->collection['parent'] = $editor_ids[0];
        if (isset($this->collection["id"]) && $this->collection["id"]) {
            $collection_id = $this->collection["id"];
        } else {
            $collection_id = collection::import($this->collection);
        }

        // GESTION DES SOUS-COLLECTIONS
        $this->subcollection['coll_parent'] = $collection_id;
        if (isset($this->subcollection["id"]) && $this->subcollection["id"]) {
            $subcollection_id = $this->subcollection["id"];
        } else {
            $subcollection_id = subcollection::import($this->subcollection);
        }

        // GESTION DES TITRES DE SERIE
        if (!isset($this->serie_id) || !$this->serie_id) {
            $this->serie_id = serie::import(stripslashes($this->serie));
        }

        /* traitement de Dewey */
        if (!isset($this->internal_index) || !$this->internal_index) {
            if (!isset($this->dewey["new_comment"]) || !$this->dewey["new_comment"]) {
                $this->dewey["new_comment"] = "";
            } else {
                if (is_array($this->dewey["new_comment"]) && !empty($this->dewey["new_comment"][0])) {
                    $this->dewey["new_comment"] = $this->dewey["new_comment"][0];
                }
            }
            if (!isset($this->dewey["new_pclass"]) || !$this->dewey["new_pclass"]) {
                $this->dewey["new_pclass"] = "";
            }
            $this->internal_index = indexint::import((isset($this->dewey[0]) ? clean_string($this->dewey[0]) : ''), clean_string($this->dewey["new_comment"]), clean_string($this->dewey["new_pclass"]));
        }

        $date_parution_z3950 = notice::get_date_parution($this->year);

        /* Origine de la notice */
        $this->orinot_id = origine_notice::import($this->origine_notice);
        if ($this->orinot_id == 0) {
            $this->orinot_id = 1;
        }

        if ($this->notice_is_new) { // flag affecté comme new en création
            $this->notice_date_is_new = date('Y-m-d H:i:s');
        }

        $sql_ins = "insert into notices (
			typdoc          ,
			code        	,
			tit1            ,
			tit2            ,
			tit3            ,
			tit4            ,
			tparent_id      ,
			tnvol           ,
			ed1_id          ,
			ed2_id          ,
			year            ,
			npages          ,
			ill             ,
			size            ,
			accomp          ,
			coll_id         ,
			subcoll_id      ,
			nocoll          ,
			mention_edition ,
			n_gen           ,
			n_contenu       ,
			n_resume        ,
			indexint,
			statut,
			commentaire_gestion,
			signature,
			thumbnail_url,
			index_l,
			niveau_biblio,
			niveau_hierar,
			lien,
			eformat,
			origine_catalogage,
			prix,
			create_date,
			date_parution,
			indexation_lang,
			notice_is_new,
			notice_date_is_new
			) values (
			'" . addslashes($this->document_type) . "',
			'" . addslashes($this->isbn) . "',
			'" . addslashes($this->titles[0]) . "',
			'" . addslashes($this->titles[1]) . "',
			'" . addslashes($this->titles[2]) . "',
			'" . addslashes($this->titles[3]) . "',
			'" . intval($this->serie_id) . "',
			'" . addslashes($this->nbr_in_serie) . "',
			" . intval($editor_ids[0]) . " ,
			" . intval($editor_ids[1]) . " ,
			'" . addslashes($this->year) . "',
			'" . addslashes($this->page_nbr) . "',
			'" . addslashes($this->illustration) . "',
			'" . addslashes($this->size) . "',
			'" . addslashes($this->accompagnement) . "',
			" . intval($collection_id) . " ,
			" . intval($subcollection_id) . " ,
			'" . addslashes($this->nbr_in_collection) . "',
			'" . addslashes($this->mention_edition) . "',
			'" . addslashes($this->general_note) . "',
			'" . addslashes($this->content_note) . "',
			'" . addslashes($this->abstract_note) . "',
			'" . addslashes($this->internal_index) . "',
			'" . addslashes($this->statut) . "',
			'" . addslashes($this->commentaire_gestion) . "',
			'" . addslashes($this->signature) . "',
			'" . addslashes($this->thumbnail_url) . "',
			'" . addslashes(clean_tags($this->free_index)) . "',
			'" . addslashes($this->bibliographic_level) . "',
			'" . addslashes($this->hierarchic_level) . "',
			'" . addslashes($this->link_url) . "',
			'" . addslashes($this->link_format) . "',
			'" . intval($this->orinot_id) . "',
			'" . addslashes($this->prix) . "',
			sysdate(),
			'" . addslashes($date_parution_z3950) . "',
			'" . addslashes($this->indexation_lang) . "',
			'" . addslashes($this->notice_is_new) . "',
			'" . addslashes($this->notice_date_is_new) . "'
		 )";

        pmb_mysql_query($sql_ins) or die("Couldn't insert into table notices : ".$sql_ins);
        $notice_retour = pmb_mysql_insert_id();
        $this->notice_id = intval($notice_retour);
        audit::insert_creation(AUDIT_NOTICE, $notice_retour);

        // GESTION DU BULLETIN
        if (!empty($this->bull_id) && $this->is_article()) {
            $success = $this->create_link_article_to_issue($this->notice_id, $this->bull_id);
            // si $success == false le bulletin est cree plus loin (import_notice_links)
            if ($success) {
                // on peut arriver ici avec une chaine de caractere ou un entier
                $id_unimarc_article = is_numeric($this->unimarc_id[0]) ? intval($this->unimarc_id[0]) : trim($this->unimarc_id[0]);
                static::$issue_tmp[$id_unimarc_article] = $this->bull_id;
            }
        }

        if (!empty($this->unimarc_id) && !empty($this->unimarc_id[0])) {
            // on peut arriver ici avec une chaine de caractere ou un entier
            $old_id = is_numeric($this->unimarc_id[0]) ? intval($this->unimarc_id[0]) : trim($this->unimarc_id[0]);
            // La notice est déjà créer temporairement
            if (!empty(static::$record_tmp[$old_id])) {
                $tmp_record_id = static::$record_tmp[$old_id];

                // On remplace la notice temporaire par la notice avec toutes les infos
                $old_notice = new notice($tmp_record_id);
                $old_notice->replace($this->notice_id);

                // Si c'est un périodique, on conserve les bulletins pour éviter de les recréer
                if ($this->is_serial() && isset(static::$issue_tmp[$tmp_record_id])) {
                    static::$issue_tmp[$this->notice_id] = static::$issue_tmp[$tmp_record_id];
                    unset(static::$issue_tmp[$tmp_record_id]);
                }
            } else {
                static::$record_tmp[$old_id] = $this->notice_id;
            }
        }

        // On gère les liens entre notices
        $this->import_notice_links();

        if ($gestion_acces_active==1) {
            $ac= new acces();
            //traitement des droits acces user_notice
            if ($gestion_acces_user_notice==1) {
                $dom_1= $ac->setDomain(1);
                $dom_1->storeUserRights(0, $notice_retour);
            }
            //traitement des droits acces empr_notice
            if ($gestion_acces_empr_notice==1) {
                $dom_2= $ac->setDomain(2);
                $dom_2->storeUserRights(0, $notice_retour);
            }
        }

        // purge de la base des responsabilités de la notice intégrée...
        $this->insert_responsabilities($notice_retour);

        // traitement des titres uniformes
        global $pmb_use_uniform_title;
        if ($pmb_use_uniform_title && isset($this->titres_uniformes) && count($this->titres_uniformes)) {
            $ntu=new tu_notice($notice_retour);
            $ntu->update($this->titres_uniformes);
        }

        // traitement des langues
        $this->insert_languages($notice_retour);

        // traitement des categories
        if ($this->categorisation_type == "categorisation_auto") {
            if (function_exists('traite_categories_enreg')) {
                traite_categories_enreg($notice_retour, $this->categories);
            }
        } else {
            $rqt_del = "delete from notices_categories where notcateg_notice='" . intval($notice_retour) . "' ";
            pmb_mysql_query($rqt_del);

            if (!empty($this->categories)) {
                $rqt_ins = "insert into notices_categories (notcateg_notice, num_noeud, ordre_categorie) VALUES ";

				$rqt_ins_values = array();
                foreach ($this->categories as $i=>$category) {
                    $id_categ=$category['categ_id'];
                    if ($id_categ) {
                        $rqt_ins_values[] = " ('" . intval($notice_retour) . "', '" . intval($id_categ) . "', " . intval($i) . ") " ;
                    }
                }
                $rqt_ins .= implode(",", $rqt_ins_values);
                pmb_mysql_query($rqt_ins);
            }
        }

        // traitement des concepts
        if ($thesaurus_concepts_active == 1) {
            $index_concept = new index_concept($notice_retour, TYPE_NOTICE);
            $index_concept->save();
        }

        //Traitement des champs personnalisés (du formulaire !!!)
        $p_perso=new parametres_perso("notices");
        $nberrors=$p_perso->check_submited_fields();
        $p_perso->rec_fields_perso($notice_retour);

        //Traitement import perso
        global $notice_id,$notice_org,$notice_type_org;
        if (function_exists('z_recup_noticeunimarc_suite') && function_exists('z_import_new_notice_suite')) {
            $notice_id=$notice_retour;
            if (!$notice_org) {
                $notice_tmp = $this->notice;
            }
            z_recup_noticeunimarc_suite($notice_tmp ? $notice_tmp : $notice_org);
            z_import_new_notice_suite();
            $notice_tmp="";
        }

        //Recherche du titre uniforme automatique
        //global $opac_enrichment_bnf_sparql;
        //$opac_enrichment_bnf_sparql=1;

        $titre_uniforme=notice::getAutomaticTu($notice_retour);

        //Traitement upload vignette
        if (trim($this->flag_upload_vignette)) {
            $req = "select repertoire_path from upload_repertoire where repertoire_id ='".$pmb_notice_img_folder_id."'";
            $res = pmb_mysql_query($req);
            if (pmb_mysql_num_rows($res)) {
                $rep=pmb_mysql_fetch_object($res);
            }
            //le fichier
            if (file_exists($rep->repertoire_path.$this->flag_upload_vignette)) {
                if (copy($rep->repertoire_path.$this->flag_upload_vignette, $rep->repertoire_path."img_".$notice_retour)) {
                    unlink($rep->repertoire_path.$this->flag_upload_vignette);
                }
                //On détruit l'image si elle est en cache
                global $pmb_img_cache_folder;
                $manag_cache = getimage_cache($notice_retour);
                if ($manag_cache["location"] && preg_match("#^".$pmb_img_cache_folder."(.+)$#", $manag_cache["location"])) {
                    unlink($manag_cache["location"]);
                    global $opac_img_cache_folder;
                    if ($opac_img_cache_folder && file_exists(str_replace($pmb_img_cache_folder, $opac_img_cache_folder, $manag_cache["location"]))) {
                        unlink(str_replace($pmb_img_cache_folder, $opac_img_cache_folder, $manag_cache["location"]));
                    }
                }
            }
            //le champ
            $rqt_upd = "UPDATE notices SET thumbnail_url='".addslashes($opac_url_base."getimage.php?noticecode=&vigurl=&notice_id=".$notice_retour)."' WHERE notice_id=".$notice_retour;
            pmb_mysql_query($rqt_upd);
        }


        indexation_stack::push($notice_retour, TYPE_NOTICE);

        //On ne sait pas trop d'où on vient (depuis PMB ou depuis un script externe), on lance directement l'indexation pour éviter que l'élément reste dans la pile
        indexation_stack::live_indexation();

        //Calcul de la signature
        $sign = new notice_doublon();
        pmb_mysql_query("update notices set signature='{$sign->gen_signature($notice_retour)}' where notice_id=" . intval($notice_retour));

        //synchro_rdf
        if ($pmb_synchro_rdf) {
            $synchro_rdf = new synchro_rdf();
        }

        //Exemplaires
        if (
        	!empty($this->exemplaires) &&
        	is_countable($this->exemplaires) &&
        	count($this->exemplaires) &&
        	!$this->is_article()
        ) {
            //			global $section_995, $typdoc_995, $codstatdoc_995;
            // 			, $nb_expl_ignores;
            $section_995_ = new marc_list("section_995");
            $section_995 = $section_995_->table;
            $typdoc_995_ = new marc_list("typdoc_995");
            $typdoc_995 = $typdoc_995_->table;
            $codstatdoc_995_ = new marc_list("codstatdoc_995");
            $codstatdoc_995 = $codstatdoc_995_->table;
            // 			$nb_expl_ignores=0;

            global $deflt_docs_statut, $deflt_docs_location, $deflt_lenders;

            foreach ($this->exemplaires as $info_expl) {
                /* RAZ expl */
				$expl = array();

                if ($notice_retour) {
                    /* préparation du tableau à passer à la méthode */
                    $expl['cb'] = $info_expl['f'];

                    // On a une notice de bulletin
                    if ($this->is_issue()) {
                        if ($this->bull_id) {
                            $expl['notice'] = 0;
                            $expl['bulletin'] = intval($this->bull_id);
                        } elseif (!$this->bull_id && $notice_retour) {
                            $query = "SELECT bulletin_id FROM bulletins WHERE num_notice = '" . intval($notice_retour) . "'";
                            $result = pmb_mysql_query($query);
                            if (pmb_mysql_num_rows($result)) {
                                $row = pmb_mysql_fetch_assoc($result);
                                $expl['notice'] = 0;
                                $expl['bulletin'] = intval($row['bulletin_id']);
                            } else {
                                $expl['notice'] = $notice_retour;
                                $expl['bulletin'] = 0;
                            }
                        }
                    } else {
                        $expl['notice'] = intval($notice_retour);
                        $expl['bulletin']	= 0;
                    }

                    //$expl['bulletin']	= 0;

                    // $expl['typdoc'] = $info_995['r']; à chercher dans docs_typdoc
                    //$data_doc['tdoc_libelle'] = $info_995['r']." -Type doc importé (".$book_lender_id.")";
					$data_doc = array();
                    $data_doc['tdoc_libelle'] = $typdoc_995[$info_expl['r']];
                    if (!$data_doc['tdoc_libelle']) {
                        $data_doc['tdoc_libelle'] = "\$r non conforme -".$info_expl['r']."-" ;
                    }
                    $data_doc['duree_pret'] = 0 ; /* valeur par défaut */
                    $data_doc['tdoc_codage_import'] = $info_expl['r'] ;

                    if ($deflt_lenders) {
                        $data_doc['tdoc_owner'] = $deflt_lenders;
                    } else {
                        $data_doc['tdoc_owner'] = 0;
                    }

                    $expl['typdoc'] = docs_type::import($data_doc);
                    $expl['cote'] = $info_expl['k'];

                    // $expl['section']    = $info_995['q']; à chercher dans docs_section
                    $data_doc = array();
                    $info_expl['q'] = isset($info_expl['q']) ? trim($info_expl['q']) : "";
                    if (!$info_expl['q']) {
                        $info_expl['q'] = "u";
                    }

                    $data_doc['section_libelle'] = $section_995[$info_expl['q']];
                    $data_doc['sdoc_codage_import'] = $info_expl['q'] ;

                    if ($deflt_lenders) {
                        $data_doc['sdoc_owner'] = $deflt_lenders;
                    } else {
                        $data_doc['sdoc_owner'] = 0;
                    }

                    $expl['section'] = docs_section::import($data_doc);
                    $expl['statut'] = $deflt_docs_statut;

                    if (is_numeric($info_expl['a'])) {
                        $expl['location'] = $info_expl['a'];
                    } else {
                        $expl['location'] = $deflt_docs_location;
                    }

                    // $expl['codestat']   = $info_995['q']; 'q' utilisé, éventuellement à fixer par combo_box
					$data_doc = array();
                    //$data_doc['codestat_libelle'] = $info_995['q']." -Pub visé importé (".$book_lender_id.")";
                    $data_doc['codestat_libelle'] = $codstatdoc_995[$info_expl['q']];
                    $data_doc['statisdoc_codage_import'] = $info_expl['q'];

					if ($statisdoc_codage) {
                        $data_doc['statisdoc_owner'] = $deflt_lenders;
                    } else {
                        $data_doc['statisdoc_owner'] = 0 ;
                    }

                    $expl['codestat'] = docs_codestat::import($data_doc);
                    $expl['note'] = $info_expl['u'];
                    $expl['expl_owner'] = $deflt_lenders ;

                    if ($info_expl['m']) {
                        $expl['date_depot'] = substr($info_expl['m'], 0, 4)."-".substr($info_expl['m'], 4, 2)."-".substr($info_expl['m'], 6, 2) ;
                    }
                    if ($info_expl['n']) {
                        $expl['date_retour'] = substr($info_expl['n'], 0, 4)."-".substr($info_expl['n'], 4, 2)."-".substr($info_expl['n'], 6, 2) ;
                    }

                    exemplaire::import($expl);
                }
            }
        }

        //Documents numériques
        global $deflt_explnum_statut;
        if (!empty($this->doc_nums)) {
            foreach ($this->doc_nums as $doc_num) {
                if (empty($doc_num['a'])) {
                    continue;
                }
                if (empty($doc_num['s'])) {
                    $doc_num['s'] = $deflt_explnum_statut;
                }
                $bull_id = $this->is_issue() ? $this->bull_id : 0;
                $explnum = new explnum(0, $notice_retour, $bull_id);
                if (!empty($doc_num['__nodownload__'])) {
                    $explnum->add_url($doc_num['b'], $doc_num['a'], true, $doc_num['s']);
                } else {
                    $explnum->add_from_url($doc_num['b'], $doc_num['a'], true, $this->source_id, $doc_num['f'], $doc_num['p'], $doc_num['s']);
                }
            }
        }

        //synchro_rdf
        if ($pmb_synchro_rdf) {
            $synchro_rdf->addRdf($notice_retour, 0);
        }

        //Traitement import perso
        global $notice_id;
        if (function_exists('z_import_new_notice_fin')) {
            $notice_id=$notice_retour;
            z_import_new_notice_fin();
        }

        // On est passé par le bouton "remplacer par source externe" ?
        global $notice_to_replace;
        if (!empty($notice_to_replace)) {
            $old_notice = new notice($notice_to_replace);
            $old_notice->replace($this->notice_id);
        }

		return array($new_notice, $notice_retour);
    }

    public function update_in_database($id_notice = 0)
    {
        global $pmb_synchro_rdf;
        global $thesaurus_concepts_active;

        $new_notice = 2;
        $notice_retour = intval($id_notice);

        if (!$id_notice) {
			return array(2, 0);
        }

        //synchro_rdf
        if ($pmb_synchro_rdf) {
            $synchro_rdf = new synchro_rdf();
            $synchro_rdf->delRdf($notice_retour, 0);
        }
        // traitement des titres uniformes
        global $pmb_use_uniform_title;
        if ($pmb_use_uniform_title) {
        	if (
        		!empty($this->titres_uniformes) &&
        		is_countable($this->titres_uniformes) &&
        		count($this->titres_uniformes)
        	) {
                $ntu=new tu_notice($id_notice);
                $ntu->update($this->titres_uniformes);
            }
        }

		$editor_ids = array();
        for ($i = 0; $i < 2; $i++) {
            if ($this->editors[$i]['id']) {
                $editor_ids[$i] = $this->editors[$i]['id'];
            } else {
                $editor_ids[$i] = editeur::import($this->editors[$i]);
            }
        }

        if ($this->collection["id"]) {
            $collection_id = $this->collection["id"];
        } else {
            $this->collection['parent'] = $editor_ids[0];
            $collection_id = collection::import($this->collection);
        }

        if ($this->subcollection["id"]) {
            $subcollection_id = $this->subcollection["id"];
        } else {
            $this->subcollection['coll_parent'] = $collection_id;
            $subcollection_id = subcollection::import($this->subcollection);
            if (!isset($this->serie_id) || !$this->serie_id) {
                $this->serie_id = serie::import(stripslashes($this->serie));
            }
        }

        /* traitement de Dewey */
        if (!$this->internal_index) {
            if (!isset($this->dewey["new_comment"]) || !$this->dewey["new_comment"]) {
                $this->dewey["new_comment"] = "";
            }
            if (!isset($this->dewey["new_pclass"]) || !$this->dewey["new_pclass"]) {
                $this->dewey["new_pclass"] = "";
            }
            $this->internal_index = indexint::import(clean_string($this->dewey[0]), clean_string($this->dewey["new_comment"]), clean_string($this->dewey["new_pclass"]));
        }

        $date_parution_z3950 = notice::get_date_parution($this->year);
        /* Origine de la notice */
        $this->orinot_id = origine_notice::import($this->origine_notice);
        if ($this->orinot_id == 0) {
            $this->orinot_id = 1;
        }

        $req_new="select notice_is_new, notice_date_is_new from notices where notice_id=".$id_notice;
        $res_new=pmb_mysql_query($req_new);
        if (pmb_mysql_num_rows($res_new)) {
            if ($r=pmb_mysql_fetch_object($res_new)) {
                if ($r->notice_is_new==$this->notice_is_new) { // pas de changement du flag
                    $this->notice_date_is_new = $r->notice_date_is_new;
                } elseif ($this->notice_is_new) { // Changement du flag et affecté comme new
                    $this->notice_date_is_new = date('Y-m-d H:i:s');
                } else {// raz date
                    $this->notice_date_is_new = '0000-00-00 00:00:00';
                }
            }
        }

        $sql_ins = "update notices set
			typdoc           		='" . addslashes($this->document_type) . "',
			code        	        ='" . addslashes($this->isbn) . "',
			tit1                    ='" . addslashes($this->titles[0]) . "',
			tit2                    ='" . addslashes($this->titles[1]) . "',
			tit3                    ='" . addslashes($this->titles[2]) . "',
			tit4                    ='" . addslashes($this->titles[3]) . "',
			tparent_id              ='" . intval($this->serie_id) . "',
			tnvol                   ='" . addslashes($this->nbr_in_serie) . "',
			ed1_id                  =" . intval($editor_ids[0]) . " ,
			ed2_id                  =" . intval($editor_ids[1]) . " ,
			year                    ='" . addslashes($this->year) . "',
			npages                  ='" . addslashes($this->page_nbr) . "',
			ill                     ='" . addslashes($this->illustration) . "',
			size                    ='" . addslashes($this->size) . "',
			accomp                  ='" . addslashes($this->accompagnement) . "',
			coll_id                 =" . intval($collection_id) . " ,
			subcoll_id              =" . intval($subcollection_id) . " ,
			nocoll                  ='" . addslashes($this->nbr_in_collection) . "',
			mention_edition         ='" . addslashes($this->mention_edition) . "',
			n_gen                   ='" . addslashes($this->general_note) . "',
			n_contenu               ='" . addslashes($this->content_note) . "',
			n_resume                ='" . addslashes($this->abstract_note) . "',
			indexint                ='" . addslashes($this->internal_index) . "',
			statut					='" . addslashes($this->statut) . "',
			commentaire_gestion		='" . addslashes($this->commentaire_gestion) . "',
			indexation_lang			='" . addslashes($this->indexation_lang) . "',
			thumbnail_url			='" . addslashes($this->thumbnail_url) . "',
			index_l                 ='" . addslashes(clean_tags($this->free_index)) . "',
			niveau_biblio           ='" . addslashes($this->bibliographic_level) . "',
			niveau_hierar           ='" . addslashes($this->hierarchic_level) . "',
			lien                    ='" . addslashes($this->link_url) . "',
			eformat                 ='" . addslashes($this->link_format) . "',
			origine_catalogage      ='" . intval($this->orinot_id) . "',
			prix                    ='" . addslashes($this->prix) . "',
			date_parution 			='" . addslashes($date_parution_z3950) . "',
			notice_is_new			='" . addslashes($this->notice_is_new) . "',
			notice_date_is_new		='" . addslashes($this->notice_date_is_new) . "'
			where notice_id='" . intval($id_notice) . "' ";

        pmb_mysql_query($sql_ins) or die("Couldn't update notices : ".$sql_ins);
        $notice_retour = intval($id_notice);
        audit::insert_modif(AUDIT_NOTICE, $id_notice);

        // purge de la base des responsabilités de la notice intégrée...
        $this->insert_responsabilities($notice_retour);

        // traitement des categories
        if ($this->categorisation_type == "categorisation_auto") {
            if (function_exists('traite_categories_enreg')) {
                traite_categories_enreg($notice_retour, $this->categories);
            }
        } else {
            $rqt_del = "delete from notices_categories where notcateg_notice='$notice_retour' ";
            pmb_mysql_query($rqt_del);

            $rqt_ins = "insert into notices_categories (notcateg_notice, num_noeud, ordre_categorie) VALUES ";

			$rqt_ins_values = array();
            foreach ($this->categories as $i=>$category) {
                $id_categ=$category['categ_id'];
                if ($id_categ) {
                    $rqt_ins_values[] = " ('" . intval($notice_retour) . "', '" . intval($id_categ) . "', " . intval($i) . ") " ;
                }
            }
            $rqt_ins .= implode(",", $rqt_ins_values);
            pmb_mysql_query($rqt_ins);
        }

        // traitement des concepts
        if ($thesaurus_concepts_active == 1) {
            $index_concept = new index_concept($notice_retour, TYPE_NOTICE);
            $index_concept->save();
        }

        // traitement des langues
        $this->insert_languages($notice_retour);

        //Traitement des champs personnalisés (du formulaire !!!)
        $p_perso=new parametres_perso("notices");
        $nberrors=$p_perso->check_submited_fields();
        $p_perso->rec_fields_perso($notice_retour);

        //Traitement import perso
        global $notice_id,$notice_org,$notice_type_org;
        if (function_exists('z_recup_noticeunimarc_suite') && function_exists('z_import_new_notice_suite')) {
            //Suppression des champs persos
            $requete="delete from notices_custom_values where notices_custom_origine=".$notice_retour;
            pmb_mysql_query($requete);
            $notice_id=$notice_retour;
            z_recup_noticeunimarc_suite($notice_org);
            z_import_new_notice_suite();
        }

        // Mise à jour de tous les index de la notice
        notice::majNoticesTotal($notice_retour);

        //Documents numériques
        foreach ($this->doc_nums as $doc_num) {
            if (!$doc_num["a"]) {
                continue;
            }
            $explnum = new explnum(0, $notice_retour, $this->bull_id);
            $explnum->add_from_url($doc_num["b"], $doc_num["a"], false, $this->source_id, $doc_num["f"], '', $doc_num["s"]);
        }

        //synchro_rdf
        if ($pmb_synchro_rdf) {
            $synchro_rdf->addRdf($notice_retour, 0);
        }

		return array($new_notice, $notice_retour);
    }

    public function get_form($action, $id_notice=0, $retour='link', $article=false)
    {
        // construit le formulaire de catalogage pré-rempli
        global $msg, $charset, $current_module ;
        global $include_path;
        global $base_path;
        global $znotices_id;
        global $item;
        global $thesaurus_concepts_active;

        $fonction = new marc_list('function');

        $this->action = $action;

        include("$include_path/templates/z3950_form.tpl.php");
        global $bt_undo;
        global $form_notice;
        global $ptab;
        global $zone_categ_form, $zone_article_form;

        // mise à jour de l'entête du formulaire
        $form_notice = str_replace('!!libelle_form!!', $this->libelle_form, $form_notice);

        // mise à jour des flags de niveau hiérarchique
        $form_notice = str_replace('!!b_level!!', $this->bibliographic_level, $form_notice);
        $form_notice = str_replace('!!h_level!!', $this->hierarchic_level, $form_notice);
        for ($i = 0; $i < 4; $i++) {
            z3950_notice::substitute("title_$i", ($this->titles[$i] ?? ''), $ptab[0]);
        }
        //Titre de série
        $index_int_sql = "SELECT serie_id, serie_name FROM series WHERE serie_name = '".addslashes($this->serie)."'";
        $res = pmb_mysql_query($index_int_sql);
        $num_rows = pmb_mysql_num_rows($res);
        if ($num_rows == 1) {
            $the_row = pmb_mysql_fetch_assoc($res);
            z3950_notice::substitute("serie", $the_row["serie_name"], $ptab[0]);
            z3950_notice::substitute("serie_id", $the_row["serie_id"], $ptab[0]);
            z3950_notice::substitute("serie_type_use_existing", 'checked', $ptab[0]);
            z3950_notice::substitute("serie_type_insert_new", '', $ptab[0]);
        } else {
            z3950_notice::substitute("serie", "", $ptab[0]);
            z3950_notice::substitute("serie_id", "", $ptab[0]);
            z3950_notice::substitute("serie_type_use_existing", '', $ptab[0]);
            z3950_notice::substitute("serie_type_insert_new", 'checked', $ptab[0]);
        }
        z3950_notice::substitute("nbr_in_serie", $this->nbr_in_serie, $ptab[0]);
        z3950_notice::substitute("serie_new_name", $this->serie, $ptab[0]);
        $form_notice = str_replace('!!tab0!!', $ptab[0], $form_notice);

        // mise à jour de l'onglet 1
        // constitution de la mention de responsabilité
        $nb_autres_auteurs = 0 ;
        $nb_auteurs_secondaires = 0 ;//print "<pre>";print_r($this->aut_array);print "</pre>";
        $auteurs_secondaires = '';
        $autres_auteurs = '';

        $nb_auts = 0;
        if (!empty($this->aut_array) && is_countable($this->aut_array)) {
        $nb_auts = count($this->aut_array);
        }

        for ($as = 0; $as < $nb_auts; $as++) {
            if (isset($this->aut_array[$as]["responsabilite"]) && $this->aut_array[$as]["responsabilite"]===0) {
                $numrows = 0;
                if ($this->aut_array[$as]["date"]) {
                    $sql_author_find = "SELECT author_id, author_name, author_rejete, author_date FROM authors WHERE author_name = '".addslashes($this->aut_array[$as]["entree"])."' AND author_rejete = '".addslashes($this->aut_array[$as]["rejete"])."' AND author_type = '".$this->aut_array[$as]["type_auteur"]."' AND author_date ='".addslashes($this->aut_array[$as]["date"])."'";
                    $res = pmb_mysql_query($sql_author_find);
                    $numrows = pmb_mysql_num_rows($res);
                }
                if (!$numrows) {
                    $sql_author_find = "SELECT author_id, author_name, author_rejete, author_date FROM authors WHERE author_name = '".addslashes($this->aut_array[$as]["entree"])."' AND author_rejete = '".addslashes($this->aut_array[$as]["rejete"])."' AND author_type = '".$this->aut_array[$as]["type_auteur"]."'";
                    $res = pmb_mysql_query($sql_author_find);
                    $numrows = pmb_mysql_num_rows($res);
                }
                if ($numrows == 1) {
                    $existing_author = pmb_mysql_fetch_array($res);
                    $existing_author_id = $existing_author["author_id"];
                } else {
                    $existing_author_id = 0;
                }
                z3950_notice::substitute("author0_type_use_existing", $existing_author_id ? "checked" : "", $ptab[1]);
                z3950_notice::substitute("author0_type_insert_new", $existing_author_id ? "" : "checked", $ptab[1]);
                if ($existing_author_id) {
                    z3950_notice::substitute("f_author_name_0_existing", $existing_author["author_name"].", ".$existing_author["author_rejete"].($existing_author["author_date"] ? " (".$existing_author["author_date"].")" : ""), $ptab[1]);
                    z3950_notice::substitute("f_aut0_existing_id", $existing_author_id, $ptab[1]);
                } else {
                    z3950_notice::substitute("f_author_name_0_existing", '', $ptab[1]);
                    z3950_notice::substitute("f_aut0_existing_id", 0, $ptab[1]);
                }

                z3950_notice::substitute("author_name_0", (empty($this->aut_array[$as]["entree"]) ? '' : $this->aut_array[$as]["entree"]), $ptab[1]);
                z3950_notice::substitute("author_rejete_0", (empty($this->aut_array[$as]["rejete"]) ? '' : $this->aut_array[$as]["rejete"]), $ptab[1]);
                z3950_notice::substitute("author_date_0", (empty($this->aut_array[$as]["date"]) ? '' : $this->aut_array[$as]["date"]), $ptab[1]);
                z3950_notice::substitute("author_function_0", (empty($this->aut_array[$as]["fonction"]) ? '' : $this->aut_array[$as]["fonction"]), $ptab[1]);
                z3950_notice::substitute("author_function_label_0", (empty($this->aut_array[$as]["fonction"]) ? '' : $fonction->table[$this->aut_array[$as]["fonction"]]), $ptab[1]);
                z3950_notice::substitute("author_lieu_0", (empty($this->aut_array[$as]["lieu"]) ? '' : $this->aut_array[$as]["lieu"]), $ptab[1]);
                z3950_notice::substitute("author_pays_0", (empty($this->aut_array[$as]["pays"]) ? '' : $this->aut_array[$as]["pays"]), $ptab[1]);
                z3950_notice::substitute("author_comment_0", (empty($this->aut_array[$as]["author_comment"]) ? '' : $this->aut_array[$as]["author_comment"]), $ptab[1]);
                z3950_notice::substitute("author_ville_0", (empty($this->aut_array[$as]["ville"]) ? '' : $this->aut_array[$as]["ville"]), $ptab[1]);
                z3950_notice::substitute("author_subdivision_0", (empty($this->aut_array[$as]["subdivision"]) ? '' : $this->aut_array[$as]["subdivision"]), $ptab[1]);
                z3950_notice::substitute("author_numero_0", (empty($this->aut_array[$as]["numero"]) ? '' : $this->aut_array[$as]["numero"]), $ptab[1]);
                z3950_notice::substitute("author_web_0", (empty($this->aut_array[$as]["web"]) ? '' : $this->aut_array[$as]["web"]), $ptab[1]);
                z3950_notice::substitute("authority_number_0", (empty($this->aut_array[$as]["authority_number"]) ? '' : $this->aut_array[$as]["authority_number"]), $ptab[1]);

                for ($type = 70; $type <= 72; $type++) {
                    if ($this->aut_array[$as]["type_auteur"] == $type) {
                        $sel = " selected";
                    } else {
                        $sel = "";
                    }
                    z3950_notice::substitute("author_type_".$type."_0", $sel, $ptab[1]);
                    if ($this->aut_array[$as]["type_auteur"] == '70') {
                        z3950_notice::substitute('display_0', 'none', $ptab[1]);
                    } else {
                        z3950_notice::substitute('display_0', '', $ptab[1]);
                    }
                }
            }
            if (isset($this->aut_array[$as]["responsabilite"]) && $this->aut_array[$as]["responsabilite"]==1) {
                if ($this->aut_array[$as]["entree"] == "") {
                    continue;
                }
                $ptab_aut_autres = str_replace('!!iaut!!', $nb_autres_auteurs, $ptab[11]) ;

                $numrows = 0;
                if ($this->aut_array[$as]["date"]) {
                    $sql_author_find = "SELECT author_id, author_name, author_rejete, author_date FROM authors WHERE author_name = '".addslashes($this->aut_array[$as]["entree"])."' AND author_rejete = '".addslashes($this->aut_array[$as]["rejete"])."' AND author_type = '".$this->aut_array[$as]["type_auteur"]."' AND author_date ='".addslashes($this->aut_array[$as]["date"])."'";
                    $res = pmb_mysql_query($sql_author_find);
                    $numrows = pmb_mysql_num_rows($res);
                }
                if (!$numrows) {
                    $sql_author_find = "SELECT author_id, author_name, author_rejete, author_date FROM authors WHERE author_name = '".addslashes($this->aut_array[$as]["entree"])."' AND author_rejete = '".addslashes($this->aut_array[$as]["rejete"])."' AND author_type = '".$this->aut_array[$as]["type_auteur"]."'";
                    $res = pmb_mysql_query($sql_author_find);
                    $numrows = pmb_mysql_num_rows($res);
                }
                if ($numrows == 1) {
                    $existing_author = pmb_mysql_fetch_array($res);
                    $existing_author_id = $existing_author["author_id"];
                } else {
                    $existing_author_id = 0;
                }
                z3950_notice::substitute("author1_type_use_existing_", $existing_author_id ? "checked" : "", $ptab_aut_autres);
                z3950_notice::substitute("author1_type_insert_new_", $existing_author_id ? "" : "checked", $ptab_aut_autres);
                if ($existing_author_id) {
                    z3950_notice::substitute("f_aut1", $existing_author["author_name"].", ".$existing_author["author_rejete"].($existing_author["author_date"] ? " (".$existing_author["author_date"].")" : ""), $ptab_aut_autres);
                    z3950_notice::substitute("f_aut1_id", $existing_author_id, $ptab_aut_autres);
                } else {
                    z3950_notice::substitute("f_aut1", '', $ptab_aut_autres);
                    z3950_notice::substitute("f_aut1_id", '', $ptab_aut_autres);
                }

                z3950_notice::substitute("author_name_1", (empty($this->aut_array[$as]["entree"]) ? '' : $this->aut_array[$as]["entree"]), $ptab_aut_autres);
                z3950_notice::substitute("author_rejete_1", (empty($this->aut_array[$as]["rejete"]) ? '' : $this->aut_array[$as]["rejete"]), $ptab_aut_autres);
                z3950_notice::substitute("author_date_1", (empty($this->aut_array[$as]["date"]) ? '' : $this->aut_array[$as]["date"]), $ptab_aut_autres);
                z3950_notice::substitute("author_function_1", (empty($this->aut_array[$as]["fonction"]) ? '' : $this->aut_array[$as]["fonction"]), $ptab_aut_autres);
                z3950_notice::substitute("author_function_label_1", (empty($this->aut_array[$as]["fonction"]) ? '' : $fonction->table[$this->aut_array[$as]["fonction"]]), $ptab_aut_autres);
                z3950_notice::substitute("author_lieu_1", (empty($this->aut_array[$as]["lieu"]) ? '' : $this->aut_array[$as]["lieu"]), $ptab_aut_autres);
                z3950_notice::substitute("author_pays_1", (empty($this->aut_array[$as]["pays"]) ? '' : $this->aut_array[$as]["pays"]), $ptab_aut_autres);
                z3950_notice::substitute("author_comment_1", (empty($this->aut_array[$as]["author_comment"]) ? '' : $this->aut_array[$as]["author_comment"]), $ptab_aut_autres);
                z3950_notice::substitute("author_ville_1", (empty($this->aut_array[$as]["ville"]) ? '' : $this->aut_array[$as]["ville"]), $ptab_aut_autres);
                z3950_notice::substitute("author_subdivision_1", (empty($this->aut_array[$as]["subdivision"]) ? '' : $this->aut_array[$as]["subdivision"]), $ptab_aut_autres);
                z3950_notice::substitute("author_numero_1", (empty($this->aut_array[$as]["numero"]) ? '' : $this->aut_array[$as]["numero"]), $ptab_aut_autres);
                z3950_notice::substitute("author_web_1", (empty($this->aut_array[$as]["web"]) ? '' : $this->aut_array[$as]["web"]), $ptab_aut_autres);
                z3950_notice::substitute("authority_number_1", (empty($this->aut_array[$as]["authority_number"]) ? '' : $this->aut_array[$as]["authority_number"]), $ptab_aut_autres);
                for ($type = 70; $type <= 72; $type++) {
                    if ($this->aut_array[$as]["type_auteur"] == $type) {
                        $sel = " selected";
                    } else {
                        $sel = "";
                    }
                    z3950_notice::substitute("author_type_".$type."_1", $sel, $ptab_aut_autres);
                    if ($this->aut_array[$as]["type_auteur"] == '70') {
                        z3950_notice::substitute('display_1'.$nb_autres_auteurs, 'none', $ptab_aut_autres);
                    } else {
                        z3950_notice::substitute('display_1'.$nb_autres_auteurs, '', $ptab_aut_autres);
                    }
                }

                $autres_auteurs .= $ptab_aut_autres ;
                $nb_autres_auteurs++ ;
            }
            if (isset($this->aut_array[$as]["responsabilite"]) && $this->aut_array[$as]["responsabilite"]==2) {
                if ($this->aut_array[$as]["entree"] == "") {
                    continue;
                }
                $ptab_aut_autres = str_replace('!!iaut!!', $nb_auteurs_secondaires, $ptab[12]) ;

                $numrows = 0;
                if ($this->aut_array[$as]["date"]) {
                    $sql_author_find = "SELECT author_id, author_name, author_rejete, author_date FROM authors WHERE author_name = '".addslashes($this->aut_array[$as]["entree"])."' AND author_rejete = '".addslashes($this->aut_array[$as]["rejete"])."' AND author_type = '".$this->aut_array[$as]["type_auteur"]."' AND author_date ='".addslashes($this->aut_array[$as]["date"])."'";
                    $res = pmb_mysql_query($sql_author_find);
                    $numrows = pmb_mysql_num_rows($res);
                }
                if (!$numrows) {
                    $sql_author_find = "SELECT author_id, author_name, author_rejete, author_date FROM authors WHERE author_name = '".addslashes($this->aut_array[$as]["entree"])."' AND author_rejete = '".addslashes($this->aut_array[$as]["rejete"])."' AND author_type = '".$this->aut_array[$as]["type_auteur"]."'";
                    $res = pmb_mysql_query($sql_author_find);
                    $numrows = pmb_mysql_num_rows($res);
                }
                if ($numrows == 1) {
                    $existing_author = pmb_mysql_fetch_array($res);
                    $existing_author_id = $existing_author["author_id"];
                } else {
                    $existing_author_id = 0;
                }
                z3950_notice::substitute("author2_type_use_existing_", $existing_author_id ? "checked" : "", $ptab_aut_autres);
                z3950_notice::substitute("author2_type_insert_new_", $existing_author_id ? "" : "checked", $ptab_aut_autres);
                if ($existing_author_id) {
                    z3950_notice::substitute("f_aut2", $existing_author["author_name"].", ".$existing_author["author_rejete"].($existing_author["author_date"] ? " (".$existing_author["author_date"].")" : ""), $ptab_aut_autres);
                    z3950_notice::substitute("f_aut2_id", $existing_author_id, $ptab_aut_autres);
                } else {
                    z3950_notice::substitute("f_aut2", '', $ptab_aut_autres);
                    z3950_notice::substitute("f_aut2_id", 0, $ptab_aut_autres);
                }

                z3950_notice::substitute("author_name_2", (empty($this->aut_array[$as]["entree"]) ? '' : $this->aut_array[$as]["entree"]), $ptab_aut_autres);
                z3950_notice::substitute("author_rejete_2", (empty($this->aut_array[$as]["rejete"]) ? '' : $this->aut_array[$as]["rejete"]), $ptab_aut_autres);
                z3950_notice::substitute("author_date_2", (empty($this->aut_array[$as]["date"]) ? '' : $this->aut_array[$as]["date"]), $ptab_aut_autres);
                z3950_notice::substitute("author_function_2", (empty($this->aut_array[$as]["fonction"]) ? '' : $this->aut_array[$as]["fonction"]), $ptab_aut_autres);
                z3950_notice::substitute("author_function_label_2", (empty($this->aut_array[$as]["fonction"]) ? '' : $fonction->table[$this->aut_array[$as]["fonction"]]), $ptab_aut_autres);
                z3950_notice::substitute("author_lieu_2", (empty($this->aut_array[$as]["lieu"]) ? '' : $this->aut_array[$as]["lieu"]), $ptab_aut_autres);
                z3950_notice::substitute("author_pays_2", (empty($this->aut_array[$as]["pays"]) ? '' : $this->aut_array[$as]["pays"]), $ptab_aut_autres);
                z3950_notice::substitute("author_comment_2", (empty($this->aut_array[$as]["author_comment"]) ? '' : $this->aut_array[$as]["author_comment"]), $ptab_aut_autres);
                z3950_notice::substitute("author_ville_2", (empty($this->aut_array[$as]["ville"]) ? '' : $this->aut_array[$as]["ville"]), $ptab_aut_autres);
                z3950_notice::substitute("author_subdivision_2", (empty($this->aut_array[$as]["subdivision"]) ? '' : $this->aut_array[$as]["subdivision"]), $ptab_aut_autres);
                z3950_notice::substitute("author_numero_2", (empty($this->aut_array[$as]["numero"]) ? '' : $this->aut_array[$as]["numero"]), $ptab_aut_autres);
                z3950_notice::substitute("author_web_2", (empty($this->aut_array[$as]["web"]) ? '' : $this->aut_array[$as]["web"]), $ptab_aut_autres);
                z3950_notice::substitute("authority_number_2", (empty($this->aut_array[$as]["authority_number"]) ? '' : $this->aut_array[$as]["authority_number"]), $ptab_aut_autres);
                for ($type = 70; $type <= 72; $type++) {
                    if ($this->aut_array[$as]["type_auteur"] == $type) {
                        $sel = " selected";
                    } else {
                        $sel = "";
                    }
                    z3950_notice::substitute("author_type_".$type."_2", $sel, $ptab_aut_autres);
                    if ($this->aut_array[$as]["type_auteur"] == '70') {
                        z3950_notice::substitute('display_2'.$nb_auteurs_secondaires, 'none', $ptab_aut_autres);
                    } else {
                        z3950_notice::substitute('display_2'.$nb_auteurs_secondaires, '', $ptab_aut_autres);
                    }
                }
                $auteurs_secondaires .= $ptab_aut_autres ;
                $nb_auteurs_secondaires++ ;
            }
        }
        // au cas ou pas d'auteur principal : on fait le ménage dans le formulaire
        z3950_notice::substitute("author_name_0", "", $ptab[1]);
        z3950_notice::substitute("author_rejete_0", "", $ptab[1]);
        z3950_notice::substitute("author_date_0", "", $ptab[1]);
        z3950_notice::substitute("author_function_0", "", $ptab[1]);
        z3950_notice::substitute("author_function_label_0", "", $ptab[1]);
        z3950_notice::substitute("f_author_name_0_existing", "", $ptab[1]);
        z3950_notice::substitute("f_aut0_existing_id", "", $ptab[1]);
        z3950_notice::substitute("author0_type_use_existing", "", $ptab[1]);
        z3950_notice::substitute("author0_type_insert_new", "checked", $ptab[1]);
        z3950_notice::substitute("author_lieu_0", "", $ptab[1]);
        z3950_notice::substitute("author_pays_0", "", $ptab[1]);
        z3950_notice::substitute("author_comment_0", "", $ptab[1]);
        z3950_notice::substitute("author_ville_0", "", $ptab[1]);
        z3950_notice::substitute("author_subdivision_0", "", $ptab[1]);
        z3950_notice::substitute("author_numero_0", "", $ptab[1]);
        z3950_notice::substitute("author_web_0", "", $ptab[1]);
        z3950_notice::substitute("authority_number_0", "", $ptab[1]);
        z3950_notice::substitute('display_0', 'none', $ptab[1]);


        $ptab[1] = str_replace('!!max_aut1!!', $nb_autres_auteurs+1, $ptab[1]);
        $ptab[1] = str_replace('!!iaut_added1!!', $nb_autres_auteurs, $ptab[1]);
        $ptab[1] = str_replace('!!max_aut2!!', $nb_auteurs_secondaires+1, $ptab[1]);
        $ptab[1] = str_replace('!!iaut_added2!!', $nb_auteurs_secondaires, $ptab[1]);

        $ptab[1] = str_replace('!!autres_auteurs!!', $autres_auteurs, $ptab[1]);
        $ptab[1] = str_replace('!!auteurs_secondaires!!', $auteurs_secondaires, $ptab[1]);
        $form_notice = str_replace('!!tab1!!', $ptab[1], $form_notice);

        //Editeur 1
        $existing_publisher_id = 0;
        //On tente avec toutes les infos
        if ($this->editors[0]['name'] && $this->editors[0]['ville']) {
            $sql_find_publisher = "SELECT ed_id,ed_ville,ed_name FROM publishers WHERE ed_name = '".addslashes($this->editors[0]['name'])."' AND ed_ville = '".addslashes($this->editors[0]['ville'])."'";
            $res = pmb_mysql_query($sql_find_publisher);
            if (pmb_mysql_num_rows($res) == 1) {
                $existing_publisher = pmb_mysql_fetch_array($res);
                $existing_publisher_id = $existing_publisher["ed_id"];
            }
        }
        //Non? Le nom sans ville peut être alors?
        if (!$existing_publisher_id && $this->editors[0]['name']) {
            $sql_find_publisher = "SELECT ed_id,ed_ville,ed_name FROM publishers WHERE ed_name = '".addslashes($this->editors[0]['name'])."' AND ed_ville = ''";
            $res = pmb_mysql_query($sql_find_publisher);
            if (pmb_mysql_num_rows($res) == 1) {
                $existing_publisher = pmb_mysql_fetch_array($res);
                $existing_publisher_id = $existing_publisher["ed_id"];
            }
        }
        //Juste le nom alors?
        if (!$existing_publisher_id && $this->editors[0]['name'] && !$this->editors[0]['ville']) {
            $sql_find_publisher = "SELECT ed_id,ed_ville,ed_name FROM publishers WHERE ed_name = '".addslashes($this->editors[0]['name'])."'";
            $res = pmb_mysql_query($sql_find_publisher);
            if (pmb_mysql_num_rows($res) == 1) {
                $existing_publisher = pmb_mysql_fetch_array($res);
                $existing_publisher_id = $existing_publisher["ed_id"];
            }
        }
        if (!$existing_publisher_id) {
            $existing_publisher_id = 0;
        }
        z3950_notice::substitute("editor_type_use_existing", $existing_publisher_id ? 'checked' : '', $ptab[2]);
        z3950_notice::substitute("editor_type_insert_new", $existing_publisher_id ? '' : 'checked', $ptab[2]);
        if ($existing_publisher_id) {
            $editor = new editeur($existing_publisher_id);
            $editor_display = $editor->display;
            if (!$editor_display) {
                $info_ville = $existing_publisher["ed_ville"] ? ' ('.$existing_publisher["ed_ville"].')' : "";
                $editor_display = $existing_publisher["ed_name"].$info_ville;
            }
            z3950_notice::substitute("f_ed1", $editor_display, $ptab[2]);
            z3950_notice::substitute("f_ed1_id", $existing_publisher_id, $ptab[2]);
        } else {
            z3950_notice::substitute("f_ed1", '', $ptab[2]);
            z3950_notice::substitute("f_ed1_id", '', $ptab[2]);
        }

        z3950_notice::substitute("editor_name_0", $this->editors[0]['name'], $ptab[2]);
        z3950_notice::substitute("editor_ville_0", $this->editors[0]['ville'], $ptab[2]);

        //Editeur 2
        $existing_publisher_id2 = 0;
        //On tente avec toutes les infos
        if ($this->editors[1]['name'] && $this->editors[1]['ville']) {
            $sql_find_publisher2 = "SELECT ed_id,ed_ville,ed_name FROM publishers WHERE ed_name = '".addslashes($this->editors[1]['name'])."' AND ed_ville = '".addslashes($this->editors[1]['ville'])."'";
            $res = pmb_mysql_query($sql_find_publisher2);
            if (pmb_mysql_num_rows($res) == 1) {
                $existing_publisher2 = pmb_mysql_fetch_array($res);
                $existing_publisher_id2 = $existing_publisher2["ed_id"];
            }
        }
        //Non? Le nom sans ville peut être alors?
        if (!$existing_publisher_id2 && $this->editors[1]['name']) {
            $sql_find_publisher2 = "SELECT ed_id,ed_ville,ed_name FROM publishers WHERE ed_name = '".addslashes($this->editors[1]['name'])."' AND ed_ville = ''";
            $res = pmb_mysql_query($sql_find_publisher2);
            if (pmb_mysql_num_rows($res) == 1) {
                $existing_publisher2 = pmb_mysql_fetch_array($res);
                $existing_publisher_id2 = $existing_publisher2["ed_id"];
            }
        }
        //Juste le nom alors?
        if (!$existing_publisher_id2 && $this->editors[1]['name'] && !$this->editors[1]['ville']) {
            $sql_find_publisher2 = "SELECT ed_id,ed_ville,ed_name FROM publishers WHERE ed_name = '".addslashes($this->editors[1]['name'])."'";
            $res = pmb_mysql_query($sql_find_publisher2);
            if (pmb_mysql_num_rows($res) == 1) {
                $existing_publisher2 = pmb_mysql_fetch_array($res);
                $existing_publisher_id2 = $existing_publisher2["ed_id"];
            }
        }
        if (!$existing_publisher_id2) {
            $existing_publisher_id2 = 0;
        }

        z3950_notice::substitute("editor1_type_use_existing", $existing_publisher_id2 ? 'checked' : '', $ptab[2]);
        z3950_notice::substitute("editor1_type_insert_new", $existing_publisher_id2 ? '' : 'checked', $ptab[2]);
        if ($existing_publisher_id2) {
            $editor = new editeur($existing_publisher_id2);
            $editor_display = $editor->display;
            if (!$editor_display) {
                $info_ville = $existing_publisher2["ed_ville"] ? ' ('.$existing_publisher2["ed_ville"].')' : "";
                $editor_display = $existing_publisher2["ed_name"].$info_ville;
            }
            z3950_notice::substitute("f_ed11", $editor_display, $ptab[2]);
            z3950_notice::substitute("f_ed11_id", $existing_publisher_id2, $ptab[2]);
        } else {
            z3950_notice::substitute("f_ed11", '', $ptab[2]);
            z3950_notice::substitute("f_ed11_id", '', $ptab[2]);
        }

        z3950_notice::substitute("editor_name_1", $this->editors[1]['name'], $ptab[2]);
        z3950_notice::substitute("editor_ville_1", $this->editors[1]['ville'], $ptab[2]);

        //Collection
        if ($existing_publisher_id && $this->collection['name']) {
            $sql_collection_find = "SELECT collection_id, collection_name FROM collections WHERE collection_name = '".addslashes($this->collection['name'])."' AND collection_parent = '".$existing_publisher_id."'";
            $res = pmb_mysql_query($sql_collection_find);
            if (pmb_mysql_num_rows($res) == 1) {
                $existing_collection = pmb_mysql_fetch_array($res);
                $existing_collection_id = $existing_collection["collection_id"];
            } else {
                $existing_collection_id = 0;
            }
        } else {
            $existing_collection_id = 0;
        }

        z3950_notice::substitute("collection_type_use_existing", $existing_collection_id ? 'checked' : '', $ptab[2]);
        z3950_notice::substitute("collection_type_insert_new", $existing_collection_id ? '' : 'checked', $ptab[2]);

        if ($existing_collection_id) {
            z3950_notice::substitute("f_coll_existing", $existing_collection["collection_name"], $ptab[2]);
            z3950_notice::substitute("f_coll_existing_id", $existing_collection_id, $ptab[2]);
        } else {
            z3950_notice::substitute("f_coll_existing", '', $ptab[2]);
            z3950_notice::substitute("f_coll_existing_id", '0', $ptab[2]);
        }
        z3950_notice::substitute("collection_name", $this->collection['name'], $ptab[2]);
        z3950_notice::substitute("collection_issn", $this->collection['issn'], $ptab[2]);

        //Sous Collection
        if ($existing_collection_id && $this->subcollection['name']) {
            $sql_subcollection_find = "SELECT sub_coll_id, sub_coll_name FROM sub_collections WHERE sub_coll_name = '".addslashes($this->subcollection['name'])."' AND sub_coll_parent = '".$existing_collection_id."'";
            $res = pmb_mysql_query($sql_subcollection_find) or die(pmb_mysql_error()."<br />$sql_subcollection_find");
            if (pmb_mysql_num_rows($res) == 1) {
                $existing_subcollection = pmb_mysql_fetch_array($res);
                $existing_subcollection_id = $existing_subcollection["sub_coll_id"];
            } else {
                $existing_subcollection_id = 0;
            }
        } else {
            $existing_subcollection_id = 0;
        }

        z3950_notice::substitute("subcollection_type_use_existing", $existing_subcollection_id ? 'checked' : '', $ptab[2]);
        z3950_notice::substitute("subcollection_type_insert_new", $existing_subcollection_id ? '' : 'checked', $ptab[2]);

        if ($existing_subcollection_id) {
            z3950_notice::substitute("f_subcoll_existing", $existing_subcollection["sub_coll_name"], $ptab[2]);
            z3950_notice::substitute("f_subcoll_existing_id", $existing_subcollection_id, $ptab[2]);
        } else {
            z3950_notice::substitute("f_subcoll_existing", '', $ptab[2]);
            z3950_notice::substitute("f_subcoll_existing_id", '0', $ptab[2]);
        }
        z3950_notice::substitute("subcollection_name", $this->subcollection['name'], $ptab[2]);
        z3950_notice::substitute("subcollection_issn", $this->subcollection['issn'], $ptab[2]);

        z3950_notice::substitute("nbr_in_collection", $this->nbr_in_collection, $ptab[2]);

        z3950_notice::substitute("year", $this->year, $ptab[2]);

        z3950_notice::substitute("mention_edition", $this->mention_edition, $ptab[2]);

        $form_notice = str_replace('!!tab2!!', $ptab[2], $form_notice);

        z3950_notice::substitute("isbn", $this->isbn, $ptab[3]);

        $form_notice = str_replace('!!tab3!!', $ptab[3], $form_notice);

        z3950_notice::substitute("page_nbr", $this->page_nbr, $ptab[4]);
        z3950_notice::substitute("illustration", $this->illustration, $ptab[4]);
        z3950_notice::substitute("prix", $this->prix, $ptab[4]);
        z3950_notice::substitute("accompagnement", $this->accompagnement, $ptab[4]);
        z3950_notice::substitute("size", $this->size, $ptab[4]);
        $form_notice = str_replace('!!tab4!!', $ptab[4], $form_notice);

        z3950_notice::substitute("general_note", $this->general_note, $ptab[5]);
        z3950_notice::substitute("content_note", $this->content_note, $ptab[5]);
        z3950_notice::substitute("abstract_note", $this->abstract_note, $ptab[5]);
        $form_notice = str_replace('!!tab5!!', $ptab[5], $form_notice);

        // indexation interne
        $pclassement_sql = "SELECT * FROM pclassement";
        $res = pmb_mysql_query($pclassement_sql);
        $pclassement_count = pmb_mysql_num_rows($res);
        if (!$pclassement_count) {
            $pclassement_count = 1;
        }

        if ($pclassement_count > 1) {
			$pclassements = array();
            while ($row = pmb_mysql_fetch_assoc($res)) {
				$pclassements[] = array("id" => $row["id_pclass"], "name" => $row["name_pclass"]);
            }

            $pclassement_combobox = '<select name="f_indexint_new_pclass">';
            foreach ($pclassements as $pclassement) {
                $pclassement_combobox .= '<option value="'.$pclassement["id"].'">'.$pclassement["name"].'</option>';
            }
            $pclassement_combobox .= '</select>';
        } else {
            $pclassement_combobox = "";
        }

        $ptab[6] = str_replace("!!multiple_pclass_combo_box!!", $pclassement_combobox, $ptab[6]);

        if (!isset($this->dewey[0])) {
            $this->dewey[0]='';
        }
        $index_int_sql = "SELECT indexint_name, indexint_comment, indexint_id, name_pclass FROM indexint LEFT JOIN pclassement ON (pclassement.id_pclass = indexint.num_pclass) WHERE indexint_name = '".addslashes($this->dewey[0])."'";
        $res = pmb_mysql_query($index_int_sql);
        $num_rows = pmb_mysql_num_rows($res);
        if ($num_rows == 1) {
            $the_row = pmb_mysql_fetch_assoc($res);
            z3950_notice::substitute("indexint", $the_row["indexint_name"].': '.$the_row["indexint_comment"], $ptab[6]);
            z3950_notice::substitute("indexint_id", $the_row["indexint_id"], $ptab[6]);

            z3950_notice::substitute("indexint_type_use_existing", 'checked', $ptab[6]);
            z3950_notice::substitute("indexint_type_insert_new", '', $ptab[6]);
            z3950_notice::substitute("multiple_index_int_propositions", '', $ptab[6]);
        } elseif ($num_rows > 1) {
			$index_ints = array();
            while ($row = pmb_mysql_fetch_assoc($res)) {
				$index_ints[] = array("id" => $row["indexint_id"], "name" => $row["indexint_name"], "comment" => $row["indexint_comment"], "pclass" => $row["name_pclass"]);
            }

            $form_indexint_proposition = "<ul>";
            foreach ($index_ints as $index_int) {
                $form_indexint_proposition .= "<li><b>[".$index_int["pclass"]."]</b> ".$index_int["name"].": ".$index_int["comment"].'&nbsp;';
                $jsaction = "document.getElementById('indexint_type_use_existing').checked=1; document.getElementById('f_indexint').value='".addslashes($index_int["name"].' - '.$index_int["comment"])."'; document.getElementById('f_indexint_id').value='".addslashes($index_int["id"])."'";
                $form_indexint_proposition .= '<input type="button" class="bouton" value="'.$msg["notice_integre_indexint_use"].'" onclick="'.$jsaction.'">';
                $form_indexint_proposition .= '</li>';
            }
            $form_indexint_proposition .= "</ul>";
            $ptab[6] = str_replace("!!multiple_index_int_propositions!!", $form_indexint_proposition, $ptab[6]);

            z3950_notice::substitute("indexint", "", $ptab[6]);
            z3950_notice::substitute("indexint_id", "", $ptab[6]);
            z3950_notice::substitute("indexint_type_use_existing", 'checked', $ptab[6]);
            z3950_notice::substitute("indexint_type_insert_new", '', $ptab[6]);
        } else {
            z3950_notice::substitute("indexint", "", $ptab[6]);
            z3950_notice::substitute("indexint_id", "", $ptab[6]);
            z3950_notice::substitute("indexint_type_use_existing", '', $ptab[6]);
            z3950_notice::substitute("indexint_type_insert_new", 'checked', $ptab[6]);
            z3950_notice::substitute("multiple_index_int_propositions", '', $ptab[6]);
        }
        z3950_notice::substitute("indexint_new_name", $this->dewey[0], $ptab[6]);
        z3950_notice::substitute("indexint_new_comment", "", $ptab[6]);

        // indexation libre
        z3950_notice::substitute("f_free_index", $this->free_index, $ptab[6]);
        global $pmb_keyword_sep ;
        $sep="'$pmb_keyword_sep'";
        if (!$pmb_keyword_sep) {
            $sep="' '";
        }
        if (ord($pmb_keyword_sep)==0xa || ord($pmb_keyword_sep)==0xd) {
            $sep=$msg['catalogue_saut_de_ligne'];
        }
        $ptab[6]= str_replace("!!sep!!", htmlentities($sep, ENT_QUOTES, $charset), $ptab[6]);
        $form_notice = str_replace('!!tab6!!', $ptab[6], $form_notice);

        // Gestion des titres uniformes
		$value_tu = array();
		$ntu_data = array();

        $nb_tu = 0;
        if (!empty($this->tu_500) && is_countable($this->tu_500)) {
            $nb_tu = count($this->tu_500);
        }

        for ($i=0 ; $i<$nb_tu ; $i++) {
            $value_tu[$i]['name'] = $this->tu_500[$i]['a'];
            $ntu_data[$i] = new stdClass();
            $ntu_data[$i]->tu = new stdClass();

            $ntu_data[$i]->tu->name = $this->tu_500[$i]['a'];
            $value_tu[$i]['tonalite'] = $this->tu_500[$i]['u'];

            if (!empty($this->tu_500_r[$i]) && is_countable($this->tu_500_r[$i])) {
                for ($j=0;$j<count($this->tu_500_r[$i]);$j++) {
                    $value_tu[$i]['distrib'][$j]= $this->tu_500_r[$i][$j];
                }
            }
            if (!empty($this->tu_500_s[$i]) && is_countable($this->tu_500_s[$i])) {
                for ($j=0;$j<count($this->tu_500_s[$i]);$j++) {
                    $value_tu[$i]['ref'][$j]= $this->tu_500_s[$i][$j];
                }
            }

            if (($tu_id=titre_uniforme::import_tu_exist($value_tu, 1))) {
                // 	le titre uniforme est déjà existant
                $ntu_data[$i]->num_tu= $tu_id;
            } else {
                // le titre uniforme n'est pas existant
                if (!empty($this->tu_500_n[$i]) && is_countable($this->tu_500_n[$i])) {
                    for ($j=0;$j<count($this->tu_500_n[$i]);$j++) {
                        $value_tu[$i]['comment'].= $this->tu_500_r[$i][$j];
                        if (($j+1)<count($this->tu_500_n[$i])) {
                            $value_tu[$i]['comment'].="\n";
                        }
                    }
                }

                if (!empty($this->tu_500_j[$i]) && is_countable($this->tu_500_j[$i])) {
                    for ($j=0;$j<count($this->tu_500_j[$i]);$j++) {
                        $value_tu[$i]['subdiv'][$j]= $this->tu_500_j[$i][$j];
                    }
                }
            }
            // memorisation du niveau biblio de ce titre uniforme
            if (!empty($this->tu_500_i[$i]) && is_countable($this->tu_500_i[$i])) {
                for ($j=0;$j<count($this->tu_500_i[$i]);$j++) {
                    $ntu_data[$i]->titre.= $this->tu_500_i[$i][$j];
                    if (($j+1)<count($this->tu_500_i[$i])) {
                        $ntu_data[$i]->titre.="; ";
                    }
                }
            }
            $ntu_data[$i]->date=$this->tu_500[$i]['k'];
            if (!empty($this->tu_500_l[$i]) && is_countable($this->tu_500_l[$i])) {
                for ($j=0;$j<count($this->tu_500_l[$i]);$j++) {
                    $ntu_data[$i]->sous_vedette.= $this->tu_500_l[$i][$j];
                    if (($j+1)<count($this->tu_500_l[$i])) {
                        $ntu_data[$i]->sous_vedette.="; ";
                    }
                }
            }
            $ntu_data[$i]->langue=$this->tu_500[$i]['m'];
            $ntu_data[$i]->version=$this->tu_500[$i]['q'];
            $ntu_data[$i]->mention=$this->tu_500[$i]['w'];
        }

        // serialisation des champs de l'autorité titre uniforme
        global $pmb_use_uniform_title;
        if ($pmb_use_uniform_title) {
            $memo_value_tu="<input type='hidden' name='memo_value_tu' value=\"". rawurlencode(serialize($value_tu))."\">";
            $ptab[230] = str_replace("!!titres_uniformes!!", $memo_value_tu.tu_notice::get_form_import("notice", $ntu_data), $ptab[230]);
            $form_notice = str_replace('!!tab230!!', $ptab[230], $form_notice);
        }

        // mise à jour de l'onglet 7 : langues
        // langues répétables
        $lang_repetables = '';
        $lang = new marc_list('lang');
        if (empty($this->language_code) || !is_countable($this->language_code)) {
            $max_lang = 1;
        } else {
            $max_lang = count($this->language_code);
        }
        for ($i = 0 ; $i < $max_lang ; $i++) {
            if ($i) {
                $ptab_lang = str_replace('!!ilang!!', $i, $ptab[701]) ;
            } else {
                $ptab_lang = str_replace('!!ilang!!', $i, $ptab[70]) ;
            }
            if (empty($this->language_code)) {
                $ptab_lang = str_replace('!!lang_code!!', '', $ptab_lang);
                $ptab_lang = str_replace('!!lang!!', '', $ptab_lang);
            } else {
                $ptab_lang = str_replace('!!lang_code!!', $this->language_code[$i], $ptab_lang);
                $ptab_lang = str_replace('!!lang!!', htmlentities($lang->table[$this->language_code[$i]], ENT_QUOTES, $charset), $ptab_lang);
            }
            $lang_repetables .= $ptab_lang ;
        }
        $ptab[7] = str_replace('!!max_lang!!', $max_lang, $ptab[7]);
        $ptab[7] = str_replace('!!langues_repetables!!', $lang_repetables, $ptab[7]);

        // langues originales répétables
        $langorg_repetables = '';
        if (empty($this->original_language_code) || !is_countable($this->original_language_code)) {
            $max_langorg = 1;
        } else {
            $max_langorg = count($this->original_language_code);
        }
        for ($i = 0 ; $i < $max_langorg ; $i++) {
            if ($i) {
                $ptab_lang = str_replace('!!ilangorg!!', $i, $ptab[711]) ;
            } else {
                $ptab_lang = str_replace('!!ilangorg!!', $i, $ptab[71]) ;
            }
            if (empty($this->original_language_code)) {
                $ptab_lang = str_replace('!!langorg_code!!', '', $ptab_lang);
                $ptab_lang = str_replace('!!langorg!!', '', $ptab_lang);
            } else {
                $ptab_lang = str_replace('!!langorg_code!!', $this->original_language_code[$i], $ptab_lang);
                $ptab_lang = str_replace('!!langorg!!', htmlentities($lang->table[$this->original_language_code[$i]], ENT_QUOTES, $charset), $ptab_lang);
            }
            $langorg_repetables .= $ptab_lang ;
        }
        $ptab[7] = str_replace('!!max_langorg!!', $max_langorg, $ptab[7]);
        $ptab[7] = str_replace('!!languesorg_repetables!!', $langorg_repetables, $ptab[7]);

        $form_notice = str_replace('!!tab7!!', $ptab[7], $form_notice);

        z3950_notice::substitute("link_url", $this->link_url, $ptab[8]);
        z3950_notice::substitute("link_format", $this->link_format, $ptab[8]);

        $form_notice = str_replace('!!tab8!!', $ptab[8], $form_notice);

        // définition de la page cible du form
        $form_notice = str_replace('!!action!!', $this->action, $form_notice);

        // ajout des selecteurs
        $select_doc = new marc_select('doctype', 'typdoc', $this->document_type);
        $form_notice = str_replace('!!document_type!!', $select_doc->display, $form_notice);
        if ($article) {
            $form_notice = str_replace('!!checked_mono!!', "", $form_notice);
            $form_notice = str_replace('!!checked_perio!!', "", $form_notice);
            $form_notice = str_replace('!!checked_art!!', "selected=\"selected\"", $form_notice);
        } else {
            if ($this->bibliographic_level == 's') {
                $form_notice = str_replace('!!checked_mono!!', "", $form_notice);
                $form_notice = str_replace('!!checked_perio!!', "selected=\"selected\"", $form_notice);
                $form_notice = str_replace('!!checked_art!!', "", $form_notice);
            } else {
                $form_notice = str_replace('!!checked_mono!!', "selected=\"selected\"", $form_notice);
                $form_notice = str_replace('!!checked_perio!!', "", $form_notice);
                $form_notice = str_replace('!!checked_art!!', "", $form_notice);
            }
        }

        //Zone des perios et des bulletins pour les articles
        $zone_article_form  = str_replace("!!perio_titre!!", ($this->perio_titre[0] ?? ''), $zone_article_form);
        $zone_article_form  = str_replace("!!perio_issn!!", ($this->perio_issn[0] ?? ''), $zone_article_form);
        $zone_article_form  = str_replace("!!bull_date!!", ($this->bull_mention[0] ?? ''), $zone_article_form);
        $zone_article_form  = str_replace("!!bull_titre!!", ($this->bull_titre[0] ?? ''), $zone_article_form);
        $zone_article_form  = str_replace("!!bull_num!!", ($this->bull_num[0] ?? ''), $zone_article_form);

        if (!empty($this->bull_date[0])) {
            $date_date_formatee = formatdate_input($this->bull_date[0]);
            $date_date_hid = $this->bull_date[0];
        } else {
            $date_date_formatee = '';
            $date_date_hid = '';
        }
        $date_clic = "onClick=\"openPopUp('./select.php?what=calendrier&caller=notice&date_caller=&param1=f_bull_new_date&param2=date_date_lib&auto_submit=NO&date_anterieure=YES', 'calendar')\"  ";
        $date_date = "<input type='hidden' id='f_bull_new_date' name='f_bull_new_date' value='$date_date_hid' />
			<input class='saisie-10em' type='text' name='date_date_lib' value='".$date_date_formatee."' placeholder='".$msg["format_date_input_placeholder"]."' />
			<input class='bouton' type='button' name='date_date_lib_bouton' value='".$msg["bouton_calendrier"]."' ".$date_clic." />";
        $zone_article_form = str_replace("!!date_date!!", $date_date, $zone_article_form);

        //On cherche si le perio existe
        $num_rows_perio = 0;
        if (!empty($this->perio_titre[0]) && !empty($this->perio_issn[0])) {
            $req="select notice_id, tit1 from notices where niveau_biblio='s' and niveau_hierar='1'
					and tit1='".addslashes($this->perio_titre[0])."'
					and code='".addslashes($this->perio_issn[0])."' limit 1";
            $res_perio = pmb_mysql_query($req);
            $num_rows_perio = pmb_mysql_num_rows($res_perio);
        }
        if (!$num_rows_perio) {
            if (!empty($this->perio_titre[0])) {
                $req="select notice_id, tit1 from notices where niveau_biblio='s' and niveau_hierar='1'
					and tit1='".addslashes($this->perio_titre[0])."'
					limit 1";
                $res_perio = pmb_mysql_query($req);
                $num_rows_perio = pmb_mysql_num_rows($res_perio);
            }
        }
        if (!$num_rows_perio) {
            if (!empty($this->perio_issn[0])) {
                $req="select notice_id, tit1 from notices where niveau_biblio='s' and niveau_hierar='1'
						and code='".addslashes($this->perio_issn[0])."' limit 1";
                $res_perio = pmb_mysql_query($req);
                $num_rows_perio = pmb_mysql_num_rows($res_perio);
            }
        }
        if ($num_rows_perio == 1) {
            $perio_found = pmb_mysql_fetch_object($res_perio);
            $idperio = $perio_found->notice_id;
            $zone_article_form  = str_replace("!!f_perio_existing!!", htmlentities($perio_found->tit1, ENT_QUOTES, $charset), $zone_article_form);
            $zone_article_form  = str_replace("!!f_perio_existing_id!!", $perio_found->notice_id, $zone_article_form);
            $zone_article_form  = str_replace("!!perio_type_new!!", "", $zone_article_form);
            $zone_article_form  = str_replace("!!perio_type_use_existing!!", "checked", $zone_article_form);
        } else {
            $idperio = 0;
            $zone_article_form  = str_replace("!!f_perio_existing!!", "", $zone_article_form);
            $zone_article_form  = str_replace("!!f_perio_existing_id!!", "", $zone_article_form);
            $zone_article_form  = str_replace("!!perio_type_new!!", "checked", $zone_article_form);
            $zone_article_form  = str_replace("!!perio_type_use_existing!!", "", $zone_article_form);
        }

        //On cherche si le bulletin existe
        $num_rows_bull=0;
        if (!empty($this->bull_num[0]) && !empty($idperio)) {
            $req="select bulletin_id, bulletin_numero,date_date,mention_date from bulletins where bulletin_notice='".$idperio."' and  bulletin_numero like '%".addslashes($this->bull_num[0])."%' ";
            $res_bull = pmb_mysql_query($req);
            $num_rows_bull = pmb_mysql_num_rows($res_bull);
        }
        if (empty($num_rows_bull) && !empty($this->bull_date[0]) && !empty($idperio)) {
            $req="select bulletin_id, bulletin_numero,date_date,mention_date from bulletins where bulletin_notice='".$idperio."' and date_date='".addslashes($this->bull_date[0])."' ";
            $res_bull = pmb_mysql_query($req);
            $num_rows_bull = pmb_mysql_num_rows($res_bull);
        } elseif (($num_rows_bull > 1) && !empty($this->bull_date[0]) && !empty($idperio)) {
            $req="select bulletin_id, bulletin_numero,date_date,mention_date from bulletins where bulletin_notice='".$idperio."' and date_date='".addslashes($this->bull_date[0])."' and  bulletin_numero like '%".addslashes($this->bull_num[0])."%' ";
            $res_bull = pmb_mysql_query($req);
            $num_rows_bull = pmb_mysql_num_rows($res_bull);
        }
        if (empty($num_rows_bull) && !empty($this->bull_mention[0]) && !empty($idperio)) {
            $req="select bulletin_id, bulletin_numero,date_date,mention_date from bulletins where bulletin_notice='".$idperio."' and mention_date='".addslashes($this->bull_mention[0])."' ";
            $res_bull = pmb_mysql_query($req);
            $num_rows_bull = pmb_mysql_num_rows($res_bull);
        } elseif (($num_rows_bull > 1) && !empty($this->bull_mention[0]) && !empty($idperio)) {
            if (!empty($this->bull_date[0])) {
                $req = "select bulletin_id, bulletin_numero,date_date,mention_date from bulletins where bulletin_notice='".$idperio."' and date_date='".addslashes($this->bull_date[0])."' and mention_date='".addslashes($this->bull_mention[0])."' ";
            } else {
                $req = "select bulletin_id, bulletin_numero,date_date,mention_date from bulletins where bulletin_notice='".$idperio."' and mention_date='".addslashes($this->bull_mention[0])."' and  bulletin_numero like '%".addslashes($this->bull_num[0])."%' ";
            }
            $res_bull = pmb_mysql_query($req);
            $num_rows_bull = pmb_mysql_num_rows($res_bull);
        }

        if ($num_rows_bull) {
            $bull_found = pmb_mysql_fetch_object($res_bull);
            $f_bull_existing=trim($bull_found->bulletin_numero);
            if (!$f_bull_existing && trim($bull_found->date_date)) {
                $f_bull_existing="[".trim($bull_found->date_date)."]";
            } elseif (!$f_bull_existing && trim($bull_found->mention_date)) {
                $f_bull_existing="(".trim($bull_found->mention_date).")";
            }
            $zone_article_form  = str_replace("!!f_bull_existing!!", htmlentities($f_bull_existing, ENT_QUOTES, $charset), $zone_article_form);
            $zone_article_form  = str_replace("!!f_bull_existing_id!!", $bull_found->bulletin_id, $zone_article_form);
            $zone_article_form  = str_replace("!!bull_type_new!!", (($num_rows_bull) ? '' : "checked='checked'"), $zone_article_form);
            $zone_article_form  = str_replace("!!bull_type_use_existing!!", (($num_rows_bull) ? "checked='checked'" : ''), $zone_article_form);
        } else {
            $zone_article_form  = str_replace("!!f_bull_existing!!", "", $zone_article_form);
            $zone_article_form  = str_replace("!!f_bull_existing_id!!", "", $zone_article_form);
            $zone_article_form  = str_replace("!!bull_type_new!!", "checked='checked'", $zone_article_form);
            $zone_article_form  = str_replace("!!bull_type_use_existing!!", "", $zone_article_form);
        }
        $form_notice = str_replace("!!zone_article!!", $zone_article_form, $form_notice);
        if ($article) {
            $form_notice = str_replace("!!display_zone_article!!", "", $form_notice);
        } else {
            $form_notice = str_replace("!!display_zone_article!!", "none", $form_notice);
        }

        if ($item) {
            $form_notice = str_replace('!!notice_entrepot!!', "<input type='hidden' name='item' value='$item' />", $form_notice);
        } else {
            $form_notice = str_replace('!!notice_entrepot!!', "", $form_notice);
        }

        if (!empty($this->origine_notice)) {
            $form_notice = str_replace('!!orinot_nom!!', $this->origine_notice['nom'], $form_notice);
            $form_notice = str_replace('!!orinot_pays!!', $this->origine_notice['pays'], $form_notice);
        }
        //Traitement du 503 "titre de forme" pour le Musée des beaux arts de Nantes
        global $tableau_503;
		$tableau_503 = array(
			"info_503" => $this->info_503,
            "info_503_d" => $this->info_503_d,
            "info_503_j" => $this->info_503_j,
		);

        // traitement des catégories : affichage dans le formulaire


        $tableau_600 = array(
            "info_600_3" => $this->info_600_3,
            "info_600_a" => $this->info_600_a,
            "info_600_b" => $this->info_600_b,
            "info_600_c" => $this->info_600_c,
            "info_600_d" => $this->info_600_d,
            "info_600_f" => $this->info_600_f,
            "info_600_g" => $this->info_600_g,
            "info_600_j" => $this->info_600_j,
            "info_600_p" => $this->info_600_p,
            "info_600_t" => $this->info_600_t,
            "info_600_x" => $this->info_600_x,
            "info_600_y" => $this->info_600_y,
            "info_600_z" => $this->info_600_z,
        );
        $tableau_601 = array(
            "info_601_3" => $this->info_601_3,
            "info_601_a" => $this->info_601_a,
            "info_601_b" => $this->info_601_b,
            "info_601_c" => $this->info_601_c,
            "info_601_d" => $this->info_601_d,
            "info_601_e" => $this->info_601_e,
            "info_601_f" => $this->info_601_f,
            "info_601_g" => $this->info_601_g,
            "info_601_h" => $this->info_601_h,
            "info_601_j" => $this->info_601_j,
            "info_601_t" => $this->info_601_t,
            "info_601_x" => $this->info_601_x,
            "info_601_y" => $this->info_601_y,
        	"info_601_z" => $this->info_601_z,
        );
        $tableau_602 = array(
            "info_602_3" => $this->info_602_3,
            "info_602_a" => $this->info_602_a,
            "info_602_f" => $this->info_602_f,
            "info_602_j" => $this->info_602_j,
            "info_602_t" => $this->info_602_t,
            "info_602_x" => $this->info_602_x,
            "info_602_y" => $this->info_602_y,
        	"info_602_z" => $this->info_602_z,
        );
        $tableau_604 = array(
            "info_604_3" => $this->info_604_3,
            "info_604_a" => $this->info_604_a,
            "info_604_h" => $this->info_604_h,
            "info_604_i" => $this->info_604_i,
            "info_604_j" => $this->info_604_j,
            "info_604_k" => $this->info_604_k,
            "info_604_l" => $this->info_604_l,
            "info_604_x" => $this->info_604_x,
            "info_604_y" => $this->info_604_y,
        	"info_604_z" => $this->info_604_z,
        );
        $tableau_605 = array(
            "info_605_3" => $this->info_605_3,
            "info_605_a" => $this->info_605_a,
            "info_605_h" => $this->info_605_h,
            "info_605_i" => $this->info_605_i,
            "info_605_k" => $this->info_605_k,
            "info_605_l" => $this->info_605_l,
            "info_605_m" => $this->info_605_m,
            "info_605_n" => $this->info_605_n,
            "info_605_q" => $this->info_605_q,
            "info_605_r" => $this->info_605_r,
            "info_605_s" => $this->info_605_s,
            "info_605_u" => $this->info_605_u,
            "info_605_w" => $this->info_605_w,
            "info_605_j" => $this->info_605_j,
            "info_605_x" => $this->info_605_x,
            "info_605_y" => $this->info_605_y,
        	"info_605_z" => $this->info_605_z,
        );
        $tableau_606 = array(
            "info_606_3" => $this->info_606_3,
            "info_606_a" => $this->info_606_a,
            "info_606_j" => $this->info_606_j,
            "info_606_x" => $this->info_606_x,
            "info_606_y" => $this->info_606_y,
        	"info_606_z" => $this->info_606_z,
        );
        $tableau_607 = array(
            "info_607_3" => $this->info_607_3,
            "info_607_a" => $this->info_607_a,
            "info_607_j" => $this->info_607_j,
            "info_607_x" => $this->info_607_x,
            "info_607_y" => $this->info_607_y,
        	"info_607_z" => $this->info_607_z,
        );
        $tableau_608 = array(
            "info_608_3" => $this->info_608_3,
            "info_608_a" => $this->info_608_a,
            "info_608_j" => $this->info_608_j,
            "info_608_x" => $this->info_608_x,
            "info_608_y" => $this->info_608_y,
        	"info_608_z" => $this->info_608_z,
        );


        // catégories
        $form_notice = str_replace('!!zone_categ_form!!', $zone_categ_form, $form_notice);
        $max_categ = 1;
        $ptab_categ = str_replace('!!icateg!!', 0, $ptab[60]) ;
        $ptab_categ = str_replace('!!categ_id!!', 0, $ptab_categ);
        $ptab_categ = str_replace('!!categ_libelle!!', '', $ptab_categ);
        $ptab[6] = str_replace("!!categories_repetables!!", $ptab_categ, $ptab[6]);
        $ptab[6] = str_replace('!!tab_categ_order!!', "", $ptab[6]);

        if (function_exists('traite_categories_for_form')) {
            if ($thesaurus_concepts_active == 1 && function_exists('traite_concepts_for_form')) {
                //tableau_606 traduit en concepts
                $traitement_rameau=traite_categories_for_form($tableau_600, $tableau_601, $tableau_602, $tableau_605, [], $tableau_607, $tableau_608);
            } else {
                $traitement_rameau=traite_categories_for_form($tableau_600, $tableau_601, $tableau_602, $tableau_605, $tableau_606, $tableau_607, $tableau_608);
            }
        } else {
            $traitement_rameau='';
        }
        if (!is_array($traitement_rameau)) {
            $traitement_rameau = array("form" => $traitement_rameau, "message" => "");
        }
        $form_notice = str_replace('!!message_rameau!!', $traitement_rameau["message"], $form_notice);
        $form_notice = str_replace('!!traitement_rameau!!', $traitement_rameau["form"], $form_notice);

        if ($thesaurus_concepts_active == 1 && function_exists('traite_concepts_for_form')) {
            //tableau_606 traduit en concepts
            $manual_categorisation_form = get_manual_categorisation_form($tableau_600, $tableau_601, $tableau_602, $tableau_604, $tableau_605, [], $tableau_607, $tableau_608);
        } else {
            $manual_categorisation_form = get_manual_categorisation_form($tableau_600, $tableau_601, $tableau_602, $tableau_604, $tableau_605, $tableau_606, $tableau_607, $tableau_608);
        }
        $form_notice = str_replace('!!manual_categorisation!!', $manual_categorisation_form, $form_notice);

        // Indexation concept
        if ($thesaurus_concepts_active == 1) {
            $index_concept = new index_concept(0, TYPE_NOTICE);

            $concepts_not_found = "";
            if (function_exists('traite_concepts_for_form')) {
                $concept_labels = traite_concepts_for_form($tableau_606);

                if (!empty($concept_labels["message"])) {
                    foreach (explode(" @@@ ", $concept_labels["message"]) as $concept_label) {
                        if ($concept_label) {
                            $id = concept::get_concept_id_from_label(html_entity_decode($concept_label, ENT_QUOTES, $charset));
                            if ($id) {
                                $index_concept->add_concept(new concept($id));
                            } else {
                                if ($concepts_not_found) {
                                    $concepts_not_found .= "<br/>";
                                }
                                $concepts_not_found .= "<em>".$concept_label."</em>";
                            }
                        }
                    }
                }
            }
            $index_concepts_form = $index_concept->get_form("notice");

            if ($concepts_not_found) {
                $index_concepts_form .= "<div>".$msg["notice_integre_concepts_not_found"]." :<br/>".$concepts_not_found."</div>";
            }

            $form_notice = str_replace('!!index_concept_form!!', $index_concepts_form, $form_notice);
        } else {
            $form_notice = str_replace('!!index_concept_form!!', "", $form_notice);
        }

        //Mise à jour de l'onglet 9
        $p_perso=new parametres_perso("notices");

        if (function_exists("param_perso_form")) {
            param_perso_form($p_perso);
        }

        //pour Pubmed et DOI, on regarde si on peut remplir un champ résolveur...
        if (!empty($this->others_ids)) {
            foreach ($p_perso->t_fields as $key => $t_field) {
                if ($t_field['TYPE']  =="resolve") {
                    $field_options = $t_field['OPTIONS'][0];
                    foreach ($field_options['RESOLVE'] as $resolve) {
                        //pubmed = 1 | DOI = 2
                        foreach ($this->others_ids as $other_id) {
                            if ($other_id['b'] == "PMID" && $resolve['ID']=="1") {
                                //on a le champ perso résolveur PubMed
                                $p_perso->values[$key][]=$other_id['a']."|1";
                            } elseif ($other_id['b'] == "DOI" && $resolve['ID']=="2") {
                                //on a le champ perso résolveur DOI
                                $p_perso->values[$key][]=$other_id['a']."|2";
                            }
                        }
                    }
                }
            }
        }

        if (!$p_perso->no_special_fields) {
            $perso_=$p_perso->show_editable_fields($id_notice, true);
            $perso="";
            if (!empty($perso_["FIELDS"]) && is_countable($perso_["FIELDS"])) {
                for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
                    $p=$perso_["FIELDS"][$i];
                    $perso.="<div class='row'>
                        <label for='".$p["NAME"]."' class='etiquette'>".$p["TITRE"]." </label>".$p["COMMENT_DISPLAY"]."
                        </div>
                        <div class='row'>
                        ".$p["AFF"]."
                        </div>
                        ";
                }
            }
            $perso.=$perso_["CHECK_SCRIPTS"];
            $ptab[9]=str_replace("!!champs_perso!!", $perso, $ptab[9]);
        } else {
            $ptab[9]="\n<script type='text/javascript' >function check_form() { return true; }</script>\n";
        }
        $form_notice = str_replace('!!tab9!!', $ptab[9], $form_notice);

        // champs de gestion
        $ptab[10] = str_replace('!!message_folder!!', thumbnail::get_message_folder(), $ptab[10]);

        // langue de la notice
        global $lang,$xmlta_indexation_lang;
        $user_lang = (!empty($this->indexation_lang) ? $this->indexation_lang : $xmlta_indexation_lang);
        $langues = new XMLlist("$include_path/messages/languages.xml");
        $langues->analyser();
        $clang = $langues->table;

        $combo = "<select name='indexation_lang' id='indexation_lang' class='saisie-20em' >";
        if (!$user_lang) {
            $combo .= "<option value='' selected>--</option>";
        } else {
            $combo .= "<option value='' >--</option>";
        }
        foreach ($clang as $cle => $value) {
            // arabe seulement si on est en utf-8
            if (($charset != 'utf-8' && $user_lang != 'ar') || ($charset == 'utf-8')) {
                if (strcmp($cle, $user_lang) != 0) {
                    $combo .= "<option value='$cle'>$value ($cle)</option>";
                } else {
                    $combo .= "<option value='$cle' selected>$value ($cle)</option>";
                }
            }
        }
        $combo .= "</select>";
        $ptab[10] = str_replace('!!indexation_lang!!', $combo, $ptab[10]);
        $form_notice = str_replace('!!indexation_lang_sel!!', $user_lang, $form_notice);

        global $deflt_integration_notice_statut;
        if ($id_notice) {
            $rqt_statut="select statut from notices where notice_id='$id_notice' ";
            $res_statut=pmb_mysql_query($rqt_statut);
            $stat = pmb_mysql_fetch_object($res_statut) ;
            $select_statut = gen_liste_multiple("select id_notice_statut, gestion_libelle from notice_statut order by 2", "id_notice_statut", "gestion_libelle", "id_notice_statut", "form_notice_statut", "", $stat->statut, "", "", "", "", 0) ;
        } else {
            $select_statut = gen_liste_multiple("select id_notice_statut, gestion_libelle from notice_statut order by 2", "id_notice_statut", "gestion_libelle", "id_notice_statut", "form_notice_statut", "", $deflt_integration_notice_statut, "", "", "", "", 0) ;
        }
        $ptab[10] = str_replace('!!notice_statut!!', $select_statut, $ptab[10]);
        $ptab[10] = str_replace('!!commentaire_gestion!!', (!empty($this->commentaire_gestion) ? htmlentities($this->commentaire_gestion, ENT_QUOTES, $charset) : ''), $ptab[10]);
        $ptab[10] = str_replace('!!thumbnail_url!!', htmlentities($this->thumbnail_url, ENT_QUOTES, $charset), $ptab[10]);

        global $deflt_notice_is_new;
        if ($deflt_notice_is_new) {
            $notice_is_new_checked_no = '';
            $notice_is_new_checked_yes = 'checked = "checked"';
        } else {
            $notice_is_new_checked_no = 'checked = "checked"';
            $notice_is_new_checked_yes = '';
        }
        $ptab[10] = str_replace('!!notice_is_new_checked_no!!', $notice_is_new_checked_no, $ptab[10]);
        $ptab[10] = str_replace('!!notice_is_new_checked_yes!!', $notice_is_new_checked_yes, $ptab[10]);

        $form_notice = str_replace('!!tab10!!', $ptab[10], $form_notice);

        // Documents Numériques
        $docnum_infos = "";
        $count = 0;
        $upload_doc_num="";
        if ($this->source_id) {
            $requete="select * from connectors_sources where source_id=".$this->source_id."";
            $resultat=pmb_mysql_query($requete);
            if (pmb_mysql_num_rows($resultat)) {
                $r=pmb_mysql_fetch_object($resultat);
                if (!$r->upload_doc_num) {
                    $upload_doc_num = "checked";
                }
            }
        }
        if (!empty($this->doc_nums) && count($this->doc_nums)) {
            global $deflt_explnum_statut;
            $statutlist = gen_liste_multiple("select id_explnum_statut, gestion_libelle from explnum_statut order by 2", "id_explnum_statut", "gestion_libelle", "id_explnum_statut", "doc_num_statut!!docnumid!!", "", $deflt_explnum_statut, "", "", "", "", 0);
            foreach ($this->doc_nums as $doc_num) {
                $docnum_info = $ptab[1111];
                // 				$alink = '<a target="_blank" href="'.htmlspecialchars($doc_num["a"]).'">'.htmlspecialchars($doc_num["a"]).'</a>';
                $docnum_info = str_replace('!!docnum_url!!', htmlspecialchars($doc_num["a"], ENT_QUOTES, $charset), $docnum_info);
                $docnum_info = str_replace('!!docnum_caption!!', htmlspecialchars($doc_num["b"], ENT_QUOTES, $charset), $docnum_info);
                $docnum_info = str_replace('!!docnum_filename!!', htmlspecialchars($doc_num["f"], ENT_QUOTES, $charset), $docnum_info);
                $docnum_info = str_replace('!!docnum_statutlist!!', $statutlist, $docnum_info);
                $docnum_info = str_replace('!!docnumid!!', $count, $docnum_info);
                $docnum_info = str_replace('!!upload_doc_num!!', $upload_doc_num, $docnum_info);
                $docnum_infos .= $docnum_info;
                $count++;
            }
        }
        if (!$docnum_infos) {
            $docnum_infos = $msg["noticeintegre_nodocnum"];
        }
        $ptab[1110] = str_replace('!!docnum_count!!', $count, $ptab[1110]);
        $ptab[1110] = str_replace('!!docnums!!', $docnum_infos, $ptab[1110]);
        $form_notice = str_replace('!!tab11!!', $ptab[1110], $form_notice);


        $aac=explode('&', $action);
        $retact = (!empty($aac[2]) ? '&'.$aac[2] : '').(!empty($aac[5]) ? '&'.$aac[5] : '').(!empty($aac[6]) ? '&'.$aac[6] : '');
        global $force;
        $retares='';
        switch ($retour) {
            case 'button':
                if ($this->message_retour) {
                    $retares="<input type='button' class='bouton' onclick='history.go(-1);' value='".$this->message_retour."' />";
                } else {
                    if ($force == 1) {
                        $retares="<a href='javascript:history.go(-2);'>".$msg['z3950_retour_a_resultats']."</a>";
                    } else {
                        $retares="<a href='javascript:history.go(-1);'>".$msg['z3950_retour_a_resultats']."</a>";
                    }
                }
                break;
            case 'link':
                $retares="<a href='./catalog.php?categ=z3950&action=display".$retact."'>".$msg['z3950_retour_a_resultats']."</a>";
                break;
            default:
                break;
        }
        $form_notice = str_replace('!!retour_a_resultats!!', $retares, $form_notice);

        if (!$this->bt_integr_value) {
            if (!$id_notice) {
                $form_notice = str_replace('!!bouton_integration!!', $msg['z3950_integr_not_seule'], $form_notice);
            } else {
                $form_notice = str_replace('!!bouton_integration!!', $msg['notice_z3950_remplace_catal'], $form_notice);
            }
        } else {
            $form_notice = str_replace('!!bouton_integration!!', $this->bt_integr_value, $form_notice);
        }
        if ($this->bt_undo_value && $this->bt_undo_action) {
            $bt_undo = str_replace('!!value!!', $this->bt_undo_value, $bt_undo);
            $bt_undo = str_replace('!!action!!', $this->bt_undo_action, $bt_undo);
            $form_notice = str_replace('<!-- bt_undo -->', $bt_undo, $form_notice);
        }
        $form_notice = str_replace('!!id_notice!!', $id_notice, $form_notice);
        $form_notice = str_replace('!!notice!!', $znotices_id, $form_notice);
        $form_notice = str_replace('!!notice_type!!', $this->notice_type, $form_notice);

        return $form_notice;
    }

    // Traitement retour du formulaire
    public function from_form()
    {
        global $typdoc, $b_level, $h_level, $f_title_0, $f_title_1, $f_title_2, $f_title_3, $f_serie, $f_serie_id, $f_nbr_in_serie ;
        global $f_editor_name_0, $f_editor_ville_0, $f_editor_name_1, $f_editor_ville_1, $f_collection_name, $f_collection_issn, $f_subcollection_name, $f_subcollection_issn,
        $f_nbr_in_collection, $f_year, $f_mention_edition, $f_cb, $f_page_nbr, $f_illustration, $f_size, $f_prix, $f_accompagnement,
        $f_general_note, $f_content_note, $f_abstract_note,
        $f_indexint, $f_indexint_id, $f_free_index,
        $f_language_code, $f_original_language_code,
        $f_link_url, $f_link_format,
        $f_orinot_nom, $f_orinot_pays,
        $form_notice_statut, $f_commentaire_gestion, $f_thumbnail_url,
        $f_notice_is_new ;
        global $categ_pas_trouvee, $pmb_keyword_sep, $categorisation_type,$indexation_lang;
        global $opac_url_base,$item;

        global $pmb_use_uniform_title;
        if ($pmb_use_uniform_title) {
            global $max_titre_uniforme;
            if ($max_titre_uniforme) {
                global $memo_value_tu;
                $value_tu=unserialize(rawurldecode($memo_value_tu));
                // Titres uniformes
                for ($i=0; $i < $max_titre_uniforme; $i++) {
                    $var_tu_id = "f_titre_uniforme_code$i" ;
                    $var_tu_titre = "f_titre_uniforme$i" ;

                    $var_ntu_titre = "ntu_titre$i" ;
                    $var_ntu_date = "ntu_date$i" ;
                    $var_ntu_sous_vedette = "ntu_sous_vedette$i" ;
                    $var_ntu_langue = "ntu_langue$i" ;
                    $var_ntu_version = "ntu_version$i" ;
                    $var_ntu_mention = "ntu_mention$i" ;
                    global ${$var_tu_id},${$var_tu_titre},${$var_ntu_titre},${$var_ntu_date},${$var_ntu_sous_vedette},${$var_ntu_langue},${$var_ntu_version},${$var_ntu_mention};

                    if (${$var_tu_titre}) {
                        // on crée un nouveau titre uniforme si un id et un titre différent, ou si pas d'id
                        if (${$var_tu_id} && (${$var_tu_titre} != $value_tu[$i]["name"]) || (!${$var_tu_id})) {
                            $value_tu[$i]["name"]=${$var_tu_titre};
                            $tu_id=titre_uniforme::import($value_tu[$i], 1);
                        } else {
                            $tu_id=${$var_tu_id};
                        }
                        // il est soit existant, soit créé
                        if ($tu_id) {
                            $this->titres_uniformes[] = array(
                                'tu_titre' => ${$var_tu_titre},
                                'num_tu' => $tu_id,
                                'ntu_titre' => ${$var_ntu_titre},
                                'ntu_date' => ${$var_ntu_date},
                                'ntu_sous_vedette' => ${$var_ntu_sous_vedette},
                                'ntu_langue' => ${$var_ntu_langue},
                                'ntu_version' => ${$var_ntu_version},
                                'ntu_mention' => ${$var_ntu_mention},
                            );
                        }
                    }
                }
            }
        }

        $this->document_type = clean_string(stripslashes($typdoc));
        $this->bibliographic_level = clean_string(stripslashes($b_level));
        $this->hierarchic_level = clean_string(stripslashes($h_level));
        $this->titles[0] = clean_string(stripslashes($f_title_0));
        $this->titles[1] = clean_string(stripslashes($f_title_1));
        $this->titles[2] = clean_string(stripslashes($f_title_2));
        $this->titles[3] = clean_string(stripslashes($f_title_3));

        global $serie_type;
        if ($serie_type == "use_existing") {
            $this->serie = clean_string(stripslashes($f_serie));
            $this->serie_id = intval($f_serie_id);
        } else {
            global $f_serie_new;
            $this->serie = clean_string(stripslashes($f_serie_new));
        }

        $this->nbr_in_serie = clean_string(stripslashes($f_nbr_in_serie));

        $this->aut_array = array();
        global $max_aut1, $max_aut2;

        // auteur principal
        global $author0_type;
        if ($author0_type == "use_existing") {
            global $f_existing_f0_code, $f_aut0_existing_id, $f_auth_number_to_compare_0;
            if ($f_aut0_existing_id) {
                $this->aut_array[] = array(
                    'fonction' => $f_existing_f0_code,
                    'id' => $f_aut0_existing_id,
                    'responsabilite' => 0,
                    // si auteur existe, on récupère quand même le numéro d'autorité de l'auteur en cours d'import
                    // il sera intégré si la source de l'autorité n'existe pas encore en base
                    'authority_number' => $f_auth_number_to_compare_0,
                );
            }
        } else {
            global $f_author_name_0, $f_author_rejete_0, $f_author_date_0, $f_author_type_0, $f_author_function_0, $f_author_lieu_0, $f_author_pays_0, $f_author_comment_0, $f_author_ville_0, $f_author_subdivision_0, $f_author_numero_0, $f_author_web_0, $f_authority_number_0  ;
            $this->aut_array[] = array(
                'entree' => stripslashes($f_author_name_0),
                'rejete' => stripslashes($f_author_rejete_0),
                'date' => stripslashes($f_author_date_0),
                'type_auteur' => $f_author_type_0,
                'fonction' => $f_author_function_0,
                'id' => 0,
                'responsabilite' => 0,
                'lieu' =>  stripslashes($f_author_lieu_0),
                'pays' => stripslashes($f_author_pays_0),
                'author_comment' => stripslashes($f_author_comment_0),
                'ville' => stripslashes($f_author_ville_0),
                'subdivision' => stripslashes($f_author_subdivision_0),
                'numero' => $f_author_numero_0,
                'web' => $f_author_web_0,
                'authority_number' => $f_authority_number_0,
            );
        }

        // autres auteurs
        for ($i=0; $i< $max_aut1 ; $i++) {
            $var_aut_name = "f_author_name_1$i" ;
            $var_aut_rejete = "f_author_rejete_1$i" ;
            $var_aut_date = "f_author_date_1$i" ;
            $var_aut_type_auteur = "f_author_type_1$i" ;
            $var_aut_function = "f_author_function_1$i" ;
            $var_auth_type_use = "author1_type_$i";
            $var_aut_pays = "f_author_lieu_1$i" ;
            $var_aut_lieu = "f_author_pays_1$i" ;
            $var_aut_comment = "f_author_comment_1$i" ;
            $var_aut_ville = "f_author_ville_1$i" ;
            $var_aut_subdivision = "f_author_subdivision_1$i" ;
            $var_aut_numero = "f_author_numero_1$i" ;
            $var_aut_web = "f_author_web_1$i" ;
            $var_aut_number = "f_authority_number_1$i" ;

            global ${$var_aut_name}, ${$var_aut_rejete}, ${$var_aut_date}, ${$var_aut_type_auteur}, ${$var_aut_function}, ${$var_auth_type_use}, ${$var_aut_lieu}, ${$var_aut_pays}, ${$var_aut_comment}, ${$var_aut_ville}, ${$var_aut_subdivision}, ${$var_aut_numero}, ${$var_aut_web}, ${$var_aut_number};
            if (${$var_auth_type_use} == "use_existing") {
                $a_id = "f_aut1_id$i";
                $a_code = "f_f1_code$i";
                // si auteur existe, on récupère quand même le numéro d'autorité de l'auteur en cours d'import
                // il sera intégré si la source de l'autorité n'existe pas encore en base
                $a_aut_to_compare = "f_auth_number_to_compare_1$i";
                global ${$a_code}, ${$a_id}, ${$a_aut_to_compare};
                if (${$a_id}) {
                    $this->aut_array[] = array(
                        'fonction' => ${$a_code},
                        'id' => ${$a_id},
                        'responsabilite' => 1,
                        'authority_number' => ${$a_aut_to_compare},
                    );
                }
            } elseif (${$var_aut_name}) {
            	$this->aut_array[] = array(
                    'entree' => stripslashes(${$var_aut_name}),
                    'rejete' => stripslashes(${$var_aut_rejete}),
                    'date' => stripslashes(${$var_aut_date}),
                    'type_auteur' => ${$var_aut_type_auteur},
                    'fonction' => ${$var_aut_function},
                    'id' => 0,
                    'responsabilite' => 1,
                    'lieu' => stripslashes(${$var_aut_lieu}),
                    'pays' => stripslashes(${$var_aut_pays}),
                    'author_comment' => stripslashes(${$var_aut_comment}),
                    'ville' => stripslashes(${$var_aut_ville}),
                    'subdivision' => stripslashes(${$var_aut_subdivision}),
                    'numero' => ${$var_aut_numero},
                    'web' => ${$var_aut_web},
                    'authority_number' => ${$var_aut_number},
                );
            }
        }
        // auteurs secondaires
        for ($i=0; $i< $max_aut2 ; $i++) {
            $var_aut_name = "f_author_name_2$i" ;
            $var_aut_rejete = "f_author_rejete_2$i" ;
            $var_aut_date = "f_author_date_2$i" ;
            $var_aut_type_auteur = "f_author_type_2$i" ;
            $var_aut_function = "f_author_function_2$i" ;
            $var_auth_type_use = "author2_type_$i";
            $var_aut_pays = "f_author_lieu_2$i" ;
            $var_aut_lieu = "f_author_pays_2$i" ;
            $var_aut_comment = "f_author_comment_2$i" ;
            $var_aut_ville = "f_author_ville_2$i" ;
            $var_aut_subdivision = "f_author_subdivision_2$i" ;
            $var_aut_numero = "f_author_numero_2$i" ;
            $var_aut_web = "f_author_web_2$i" ;
            $var_aut_number = "f_authority_number_2$i" ;
            global ${$var_aut_name}, ${$var_aut_rejete}, ${$var_aut_date}, ${$var_aut_type_auteur}, ${$var_aut_function}, ${$var_auth_type_use}, ${$var_aut_lieu}, ${$var_aut_pays}, ${$var_aut_comment}, ${$var_aut_ville}, ${$var_aut_subdivision}, ${$var_aut_numero}, ${$var_aut_web}, ${$var_aut_number};

            if (${$var_auth_type_use} == "use_existing") {
                $a_id = "f_aut2_id$i";
                $a_code = "f_f2_code$i";
                // si auteur existe, on récupère quand même le numéro d'autorité de l'auteur en cours d'import
                // il sera intégré si la source de l'autorité n'existe pas encore en base
                $a_aut_to_compare = "f_auth_number_to_compare_2$i";
                global ${$a_code}, ${$a_id}, ${$a_aut_to_compare};
                if (${$a_id}) {
                	$this->aut_array[] = array(
                        'fonction' => ${$a_code},
                        'id' => ${$a_id},
                        'responsabilite' => 2,
                        'authority_number' => ${$a_aut_to_compare},
                    );
                }
            } elseif (${$var_aut_name}) {
            	$this->aut_array[] = array(
                    'entree' => stripslashes(${$var_aut_name}),
                    'rejete' => stripslashes(${$var_aut_rejete}),
                    'date' => stripslashes(${$var_aut_date}),
                    'type_auteur' => ${$var_aut_type_auteur},
                    'fonction' => ${$var_aut_function},
                    'id' => 0,
                    'responsabilite' => 2,
                    'lieu' => stripslashes(${$var_aut_lieu}),
                    'pays' => stripslashes(${$var_aut_pays}),
                    'author_comment' => stripslashes(${$var_aut_comment}),
                    'ville' => stripslashes(${$var_aut_ville}),
                    'subdivision' => stripslashes(${$var_aut_subdivision}),
                    'numero' => ${$var_aut_numero},
                    'web' => ${$var_aut_web},
                    'authority_number' => ${$var_aut_number},
                );
            }
        }

        global $editor_type, $f_ed1_id;
        global $collection_type,$f_coll_existing_id;
        global $subcollection_type,$f_subcoll_existing_id;
        if ($editor_type == "use_existing") {
            if ($f_ed1_id) {
                $this->editors[0]['id'] = intval($f_ed1_id);

                if ($collection_type == "use_existing") {
                    if ($f_coll_existing_id) {
                        $this->collection['id'] = intval($f_coll_existing_id);

                        if ($subcollection_type == "use_existing") {
                            if ($f_subcoll_existing_id) {
                                $this->subcollection['id'] = intval($f_subcoll_existing_id);
                            }
                        } else {
                            $this->subcollection['name'] = clean_string(stripslashes($f_subcollection_name));
                            $this->subcollection['issn'] = clean_string(stripslashes($f_subcollection_issn));
                            $this->subcollection['id'] = 0;
                        }
                    }//Si on dit utiliser une collection existante mais qu'il n'y en a pas on ne reprend pas de sous collection
                } else {
                    $this->collection['name'] = clean_string(stripslashes($f_collection_name));
                    $this->collection['issn'] = clean_string(stripslashes($f_collection_issn));
                    $this->collection['id'] = 0;

                    //Si on insert une nouvelle collection on insert aussi une nouvelle sous-collection
                    $this->subcollection['name'] = clean_string(stripslashes($f_subcollection_name));
                    $this->subcollection['issn'] = clean_string(stripslashes($f_subcollection_issn));
                    $this->subcollection['id'] = 0;
                }
            }//Si on dit utiliser un editeur existant mais qu'il n'y en a pas on ne reprend pas de collection ni sous collection
        } else {
            $this->editors[0]['name'] = clean_string(stripslashes($f_editor_name_0));
            $this->editors[0]['ville'] = clean_string(stripslashes($f_editor_ville_0));
            $this->editors[0]['id'] = 0;

            //Si on insert un nouvel editeur on insert aussi une nouvelle sous-collection
            $this->collection['name'] = clean_string(stripslashes($f_collection_name));
            $this->collection['issn'] = clean_string(stripslashes($f_collection_issn));
            $this->collection['id'] = 0;

            //Et une sous collection
            $this->subcollection['name'] = clean_string(stripslashes($f_subcollection_name));
            $this->subcollection['issn'] = clean_string(stripslashes($f_subcollection_issn));
            $this->subcollection['id'] = 0;
        }
        global $editor1_type, $f_ed11_id;
        if ($editor1_type == "use_existing") {
            if ($f_ed11_id) {
                $this->editors[1]['id'] = intval($f_ed11_id);
            }
        } else {
            $this->editors[1]['name'] = clean_string(stripslashes($f_editor_name_1));
            $this->editors[1]['ville'] = clean_string(stripslashes($f_editor_ville_1));
            $this->editors[1]['id'] = 0;
        }

        $this->nbr_in_collection = clean_string($f_nbr_in_collection);

        $this->year = clean_string($f_year);
        $this->mention_edition = clean_string($f_mention_edition);

        $this->isbn = clean_string(stripslashes($f_cb));

        $this->page_nbr = clean_string(stripslashes($f_page_nbr));
        $this->illustration = clean_string(stripslashes($f_illustration));
        $this->size = clean_string(stripslashes($f_size));
        $this->prix = clean_string(stripslashes($f_prix));
        $this->accompagnement = clean_string(stripslashes($f_accompagnement));

        $this->general_note = stripslashes($f_general_note);
        $this->content_note = stripslashes($f_content_note);
        $this->abstract_note = stripslashes($f_abstract_note);

        // catégories
        if ($categorisation_type == "categorisation_auto") {
            if (function_exists('traite_categories_from_form')) {
                $this->categories = traite_categories_from_form();
            } else {
            	$this->categories = array();
            }
            $this->categorisation_type = "categorisation_auto";
        } else {
            global $max_categ;
            $categories = array();
            for ($i=0, $count=$max_categ; $i < $count; $i++) {
                $a_categ_id_name = "f_categ_id".$i;

                global ${$a_categ_id_name};
                if (!${$a_categ_id_name}) {
                    continue;
                }

                $acategory = array("categ_id" => ${$a_categ_id_name});
                $categories[] = $acategory;
            }
            $this->categories = $categories;
            $this->categorisation_type = "categorisation_manual";
        }

        global $indexint_type;
        if ($indexint_type == "use_existing") {
            $this->dewey[0] = clean_string(stripslashes($f_indexint));
            $this->internal_index = $f_indexint_id ;
        } else {
            global $f_indexint_new, $f_indexint_new_comment, $f_indexint_new_pclass;
            $this->dewey[0] = clean_string(stripslashes($f_indexint_new));
            $this->dewey["new_comment"] = clean_string(stripslashes($f_indexint_new_comment));
            $this->dewey["new_pclass"] = clean_string(stripslashes($f_indexint_new_pclass));
            $this->internal_index = 0;
        }

        // $categ_pas_trouvee ; est un tableau de catégories pas trouvées,
        //		mots clés pas présents dans $this->categories mais à conserver en zone libre

        if (!isset($categ_pas_trouvee)) {
        	$categ_pas_trouvee = array();
        }
        $categ_pas_trouvee_pasvide = array();
        if (!empty($categ_pas_trouvee) && is_countable($categ_pas_trouvee)) {
            for ($i=0;$i<count($categ_pas_trouvee);$i++) {
                if (trim($categ_pas_trouvee[$i])) {
                    $categ_pas_trouvee_pasvide[] = addslashes(trim($categ_pas_trouvee[$i]));
                }
            }
        }

        if ($f_free_index) {
            $categ_pas_trouvee_pasvide[] = $f_free_index;
        }

        if (is_array($categ_pas_trouvee_pasvide) && count($categ_pas_trouvee_pasvide)) {
            $f_free_index=implode($pmb_keyword_sep, $categ_pas_trouvee_pasvide);
        }
        $this->free_index = clean_string($f_free_index);


        // traitement des langues
        global $max_lang, $max_langorg;
        $f_lang_form = array();
        $f_langorg_form = array();
        // langues de la publication
        $j=0;
        $this->language_code = array();
        for ($i=0; $i< $max_lang ; $i++) {
            $var_langcode = "f_lang_code$i" ;
            global ${$var_langcode} ;
            if (${$var_langcode}) {
                $this->language_code[$j] =  ${$var_langcode};
                $j++;
            }
        }
        // langues originales
        $j=0;
        $this->original_language_code = array();
        for ($i=0; $i< $max_langorg ; $i++) {
            $var_langorgcode = "f_langorg_code$i" ;
            global ${$var_langorgcode};
            if (${$var_langorgcode}) {
                $this->original_language_code[$j] =  ${$var_langorgcode};
                $j++;
            }
        }

        $this->link_url = clean_string($f_link_url);
        $this->link_format = clean_string($f_link_format);

        $this->origine_notice['nom'] = clean_string($f_orinot_nom);
        $this->origine_notice['pays'] = clean_string($f_orinot_pays);

        $this->statut = $form_notice_statut ;
        $this->commentaire_gestion  = $f_commentaire_gestion ;
        $this->indexation_lang = $indexation_lang ;
        $this->thumbnail_url		= $f_thumbnail_url;

        // vignette de la notice uploadé dans un répertoire
        $uploaded_thumbnail_url = thumbnail::create("es_".$item);
        if ($uploaded_thumbnail_url) {
            $this->thumbnail_url = $uploaded_thumbnail_url;
            $this->flag_upload_vignette = "img_es_".$item;
        }

        //Document Numérique
        global $doc_num_count;
        for ($i=0; $i<$doc_num_count; $i++) {
            $include_cb_name = "include_doc_num".$i;
            global ${$include_cb_name};
            if (${$include_cb_name}) {
                $docnum_filename_name = "doc_num_filename".$i;
                global ${$docnum_filename_name};
                $docnum_caption_name = "doc_num_caption".$i;
                global ${$docnum_caption_name};
                $docnum_url_name = "doc_num_url".$i;
                global ${$docnum_url_name};
                $docnum_statut_name = "doc_num_statut".$i;
                global ${$docnum_statut_name};
                $docnum_nodownload_name = "doc_num_nodownload".$i;
                global ${$docnum_nodownload_name};
                if (${$docnum_nodownload_name}) {
                    $nodownload = 1;
                } else {
                    $nodownload = 0;
                }

                $this->doc_nums[] = array(
                    "a" => ${$docnum_url_name},
                    "b" => ${$docnum_caption_name},
                    "f" => ${$docnum_filename_name},
                    "__nodownload__" => $nodownload,
                    "s" => ${$docnum_statut_name},
                );
            }
        }

        //Périos
        global $perio_type, $bull_type;
        if ($perio_type == 'use_existing') {
            global $f_perio_existing_id;
            $this->perio_id = intval($f_perio_existing_id);
        } else {
            global $f_perio_new, $f_perio_new_issn;
            $this->perio_titre = clean_string(stripslashes($f_perio_new));
            $this->perio_issn = clean_string(stripslashes($f_perio_new_issn));
        }

        //Bulletins
        if ($bull_type == 'use_existing') {
            global $f_bull_existing_id;
            $this->bull_id = intval($f_bull_existing_id);
        } else {
            global $f_bull_new_num, $f_bull_new_titre, $f_bull_new_mention,$date_date_lib;
            $this->bull_num = clean_string(stripslashes($f_bull_new_num));
            $this->bull_titre = clean_string(stripslashes($f_bull_new_titre));
            $this->bull_date = extraitdate(clean_string(stripslashes($date_date_lib)));
            $this->bull_mention = clean_string(stripslashes($f_bull_new_mention));
        }

        $this->notice_is_new = $f_notice_is_new;
    }

    public function process_isbn($code)
    {
        /* We've got everything, let's have a look if ISBN already exists in notices table */
        $code_nettoye = preg_replace("/([^0-9|X])/i", '', $code);

        $isbn_10 = '';
        $isbn_13 = '';

        $code_length = strlen($code_nettoye);

        switch ($code_length) {
            case 13:
                if (isEAN($code_nettoye)) {
                    $isbn_13 = formatISBN($code_nettoye, 13);
                    $isbn_10 = formatISBN($code_nettoye, 10);
                }
                break;
            case 10:
                if (isISBN($code_nettoye)) {
                    $isbn_10 = formatISBN($code_nettoye, 10);
                }
                break;
            default:
                break;
        }

        if ($isbn_13) {
            return $isbn_13;
        }
        if ($isbn_10) {
            return $isbn_10;
        }
        $ret_code = clean_string($code);
        return $ret_code;
    }

    public function process_author($name, $rejete, $type, $date)
    {
        if (!$rejete) {
            $field = explode(',', $name, 2);
            $name = $field[0];
            $rejete = $field[1];
        }

        $name = $this->clean_field($name);
        $rejete = $this->clean_field($rejete);

        $author = array();
        $author['name'] = $name;
        $author['rejete'] = $rejete;
        $author['type'] = $type;
        $author['date'] = $date;

        return $author;
    }

    public function clean_field($field)
    {
        return preg_replace('/-$| $|\($|\)$|\[$|\]$|\:$|\;$|\/$\|\$|([^ ].)\.$|,$/', '$1', trim($field));
    }

    // fonction d'intégration de l'origine de l'autorité (pratiquement identique à keep_authority_infos dans pmb/admin/import/import.finc.php)
    public function insert_authority_infos($authority_number, $type, $id_origin_authority, $authority_infos=[])
    {
        global $opac_enrichment_bnf_sparql;

        //on a un numéro d'autorité, on regarde si on l'a déjà rencontré
        $num_authority=$authority_infos['id'];
        $query = "select id_authority_source,num_authority from authorities_sources where authority_number = '".$authority_number."' and num_origin_authority='".$id_origin_authority."' and authority_type = '".$type."'";
        $result = pmb_mysql_query($query) or die("can't select authorities_sources :".$query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_object($result);
            $num_authority = $row->num_authority;
            $num_authority_source= $row->id_authority_source;
            // on cherche la préférence... dès fois que...
            $query = "select id_authority_source, num_authority from authorities_sources where authority_number = '".$authority_number."' and authority_type = '".$type."' and authority_favorite = 1";
            $result = pmb_mysql_query($query) or die("can't select authorities_sources :".$query);
            if (pmb_mysql_num_rows($result)) {
                $row = pmb_mysql_fetch_object($result);
                $num_authority = $row->num_authority;
                $num_authority_source= $row->id_authority_source;
            }
        } else {
            // on importe l'autorité dans la base si elle n'a pas d'id
            if ($num_authority==0) {
                switch($type) {
                    case "author":
                        $num_authority = auteur::import($authority_infos);
                        break;
                    case "uniform_title":
                        $num_authority = titre_uniforme::import($authority_infos);
                        break;
                        /*
                        // DB : Non utilise, commente car la methode correspondante ne comporte pas les memes parametres
                    case "category":
                        $num_authority = category::import($authority_infos);
                        break;
                        */
                    case "collection":
                        $num_authority = collection::import($authority_infos);
                        break;
                    case "subcollection":
                        $num_authority = subcollection::import($authority_infos);
                        break;
                    case "serie":
                        break;
                }
            }
            // on intègre la source de l'autorité
            $query = "insert into authorities_sources set
			num_authority = '$num_authority',
			authority_number = '".$authority_number."',
			authority_type = '$type',
			num_origin_authority = ".$id_origin_authority.",
			import_date = now()";
            pmb_mysql_query($query) or die("can't insert authorities_sources :".$query);
            $num_authority_source = pmb_mysql_insert_id();

            if (($opac_enrichment_bnf_sparql) && ($type=='author')) {
                auteur::author_enrichment($num_authority);
            }
        }
        return $num_authority;
    }

    public function from_usmarc($record)
    {
        $this->document_type = $record->inner_guide['dt'];
        $this->bibliographic_level = $record->inner_guide['bl'];
        $this->hierarchic_level = $record->inner_guide['hl'];

        if ($this->hierarchic_level=="") {
            if ($this->bibliographic_level=="s") {
                $this->hierarchic_level="1";
            }
            if ($this->bibliographic_level=="m") {
                $this->hierarchic_level="0";
            }
        }

        $inner_directory_len = 0;
        if (!empty($record->inner_directory) && is_countable($record->inner_directory)) {
            $inner_directory_len = count($record->inner_directory);
        }

        for ($i = 0; $i < $inner_directory_len; $i++) {
            $cle=$record->inner_directory[$i]['label'];
            switch($cle) {
                case "020": /* isbn */
                    $isbn = $record->get_subfield($cle, 'a');
                    break;
                case "041": /* language */
                    $this->language_code = $record->get_subfield_array($cle, "a");
                    break;
                case "245": /* titles */
                    $tit = $record->get_subfield($cle, "a", "b", "n", "p");
                    break;
                case "246": /* titles */
                    $tit_sup=$record->get_subfield($cle, "a");
                    break;
                case "247": /* former title */
                    $tit_for=$record->get_subfield($cle, "a");
                    break;
                case "250": /* mention_edition */
                    $subfield = $record->get_subfield($cle, "a");
                    $this->mention_edition = $subfield[0];
                    break;
                case "260": /* publisher */
                    $editor = $record->get_subfield($cle, "a", "b", "c");
                    break;
                case "300": /* description */
                    $subfield = $record->get_subfield($cle, "a");
                    $this->page_nbr = $this->clean_field($subfield[0]);
                    $subfield = $record->get_subfield($cle, "b");
                    $this->illustration = $this->clean_field($subfield[0]);
                    $subfield = $record->get_subfield($cle, "c");
                    $this->size = $this->clean_field($subfield[0]);
                    $subfield = $record->get_subfield($cle, "e");
                    $this->accompagnement = $this->clean_field($subfield[0]);
                    break;
                case "022": /* collection */
                    $collection_022=$record->get_subfield($cle, "a");
                    break;
                case "222": /* collection */
                    $collection_222=$record->get_subfield($cle, "a", "v", "x");
                    break;
                case "440": /* collection */
                    $collection_440=$record->get_subfield($cle, "a", "v", "x");
                    break;
                case "500": /* inside */
                    $general_note = $record->get_subfield($cle, "a");
                    break;
                case "502": /* abstract */
                    $content_note = $record->get_subfield($cle, "a");
                    break;
                case "520": /* abstract */
                    $abstract_note = $record->get_subfield($cle, "a");
                    break;
                case "082": /* Dewey */
                    $this->dewey=$record->get_subfield($cle, "a");
                    break;
                case "100":
                    $aut_100=$record->get_subfield($cle, "a", "d", "e", "4", "3");
                    break;
                case "110":
                    $aut_110=$record->get_subfield($cle, "a", "d", "e", "4", "3");
                    break;
                case "111":
                    $aut_111=$record->get_subfield($cle, "a", "d", "e", "4", "3");
                    break;
                case "700":
                    $aut_700=$record->get_subfield($cle, "a", "d", "e", "4", "3");
                    break;
                case "710":
                    $aut_710=$record->get_subfield($cle, "a", "d", "e", "4", "3");
                    break;
                case "711":
                    $aut_711=$record->get_subfield($cle, "a", "d", "e", "4", "3");
                    break;
                case "856":
                    $subfield = $record->get_subfield($cle, "u", "q");
                    $this->ressource = $subfield[0];
                    break;
                case "650":
                    $this->info_606_a=$record->get_subfield_array_array($cle, "a");
                    $this->info_606_3=$record->get_subfield_array_array($cle, "3");
                    $this->info_606_j=$record->get_subfield_array_array($cle, "j");
                    $this->info_606_x=$record->get_subfield_array_array($cle, "x");
                    $this->info_606_y=$record->get_subfield_array_array($cle, "y");
                    $this->info_606_z=$record->get_subfield_array_array($cle, "z");
                    break;
                case "653":
                    $index_sujets=$record->get_subfield($cle, "a");
                    break;
                default:
                    break;
            } /* end of switch */
        } /* end of for */

        $this->isbn = $this->process_isbn($isbn[0]);

        /* INSERT de la notice OK, on va traiter les auteurs
         10# : personnal : type auteur 70                71# : collectivités : type auteur 71
         1 seul en 700                                   idem pour les déclinaisons
         n en 701 n en 702
         les 7#0 tombent en auteur principal : responsability_type = 0
         les 7#1 tombent en autre auteur : responsability_type = 1
         les 7#2 tombent en auteur secondaire : responsability_type = 2
         */
        $this->aut_array = array();
        /* on compte tout de suite le nbre d'enreg dans les répétables */
        $nb_repet_700 = (!empty($aut_700) && is_countable($aut_700)) ? count($aut_700) : 0;
        $nb_repet_710 = (!empty($aut_710) && is_countable($aut_710)) ? count($aut_710) : 0;
        $nb_repet_711 = (!empty($aut_711) && is_countable($aut_711)) ? count($aut_711) : 0;

        /* renseignement de aut0 */
        if ($aut_100[0]['a']!="") { /* auteur principal en 100 ? */
            $author=$this->process_author($aut_100[0]['a'], $aut_100[0]['b'], '', '');

            $this->aut_array[] = array(
                "entree" => $author['name'], //$aut_100[0]['a'],
                "rejete" => $author['rejete'], //$aut_100[0]['b'],
                "type_auteur" => "70",
                "fonction" => convert_usmarc_unimarc_functions($aut_100[0][4]),
                "id" => 0,
                "responsabilite" => 0,
                "authority_number" => $aut_100[0][3]
            );
        } elseif ($aut_110[0]['a']!="") { /* auteur principal en 110 ? */
            $author=$this->process_author($aut_110[0]['a'], $aut_110[0]['b'], '', '');
            $this->aut_array[] = array(
                "entree" => $author['name'], //$aut_110[0]['a'],
                "rejete" => $author['rejete'], //$aut_110[0]['b'],
                "type_auteur" => "71",
                "fonction" => convert_usmarc_unimarc_functions($aut_110[0][4]),
                "id" => 0,
                "responsabilite" => 0,
                "authority_number" => $aut_110[0][3]
            );
        } elseif ($aut_111[0]['a']!="") { /* auteur principal en 111 ? */
            $author=$this->process_author($aut_111[0]['a'], $aut_111[0]['b'], '', '');
            $this->aut_array[] = array(
                "entree" => $author['name'], //$aut_111[0]['a'],
                "rejete" => $author['rejete'], //$aut_111[0]['b'],
                "type_auteur" => "71",
                "fonction" => convert_usmarc_unimarc_functions($aut_111[0][4]),
                "id" => 0,
                "responsabilite" => 0,
                "authority_number" => $aut_111[0][3]
            );
        }

        /* renseignement de aut1 */
        for ($i=0 ; $i < $nb_repet_700 ; $i++) {
            $author=$this->process_author($aut_700[$i]['a'], $aut_700[$i]['b'], '', '');
            $this->aut_array[] = array(
                "entree" => $author['name'], //$aut_700[$i]['a'],
                "rejete" => $author['rejete'], //$aut_700[$i]['b'],
                "type_auteur" => "70",
                "fonction" => convert_usmarc_unimarc_functions($aut_700[$i][4]),
                "id" => 0,
                "responsabilite" => 1,
                "authority_number" => $aut_700[$i][3]
            );
        }
        for ($i=0 ; $i < $nb_repet_710 ; $i++) {
            $author=$this->process_author($aut_710[$i]['a'], $aut_710[$i]['b'], '', '');
            $this->aut_array[] = array(
                "entree" => $author['name'], //$aut_710[$i]['a'],
                "rejete" => $author['rejete'], //$aut_710[$i]['b'],
                "type_auteur" => "71",
                "fonction" => convert_usmarc_unimarc_functions($aut_710[$i][4]),
                "id" => 0,
                "responsabilite" => 1,
                "authority_number" => $aut_710[$i][3]
            );
        }
        /* renseignement de aut2 */
        for ($i=0 ; $i < $nb_repet_711 ; $i++) {
            $author=$this->process_author($aut_711[$i]['a'], $aut_711[$i]['b'], '', '');
            $this->aut_array[] = array(
                "entree" => $author['name'], //$aut_711[$i]['a'],
                "rejete" => $author['rejete'], //$aut_711[$i]['b'],
                "type_auteur" => "71",
                "fonction" => convert_usmarc_unimarc_functions($aut_711[$i][4]),
                "id" => 0,
                "responsabilite" => 2,
                "authority_number" => $aut_711[$i][3]
            );
        }

        /* Editors */
        $this->year = preg_replace("/[^0-9\[\]()]/", "", $editor[0]['c']);

        $this->editors[0]['name'] = $this->clean_field($editor[0]['b']);
        $this->editors[0]['ville'] = $this->clean_field($editor[0]['a']);

        $this->editors[1]['name'] = $this->clean_field($editor[1]['b']);
        $this->editors[1]['ville'] = $this->clean_field($editor[1]['a']);

        /* ici traitement des collections */
        $coll_name = "";
        $subcoll_name = "";
        $coll_issn = "";
        $subcoll_issn = "";
        $nocoll = "";

        /* traitement de 222$a, si rien alors 440$a pour la collection */
        if ($collection_222[0]['a']!="") {
            $coll_name = $this->clean_field($collection_222[0]['a']);
            $coll_issn = $collection_022[0]['a'];
        } elseif ($collection_440[0]['a']!="") {
            $coll_name = $this->clean_field($collection_440[0]['a']);
            $coll_issn = $collection_440[0]['x'];
        }

        /* traitement de 222 1, si rien alors 440 1 pour la sous-collection */
        if ($collection_222[1]['a']!="") {
            $subcoll_name = $this->clean_field($collection_222[1]['a']);
            $subcoll_issn = $collection_022[1]['a'];
        } elseif ($collection_440[1]['a']!="") {
            $subcoll_name = $this->clean_field($collection_440[1]['a']);
            $subsubcoll_issn = $collection_440[1]['x'];
        }

        /* gaffe au nocoll, en principe en 440$v */
        if ($collection_440[0]['v']!="") {
            $this->nbr_in_collection = $collection_440[0]['v'];
        } else {
            $this->nbr_in_collection = "";
        }

        $this->collection['name']=clean_string($coll_name);
        $this->collection['issn']=clean_string($coll_issn);

        $this->subcollection['name']=clean_string($subcoll_name);
        $this->subcollection['issn']=clean_string($subcoll_issn);

        /* Series */
        $this->serie = clean_string($tit[0]['p']);
        $this->nbr_in_serie = clean_string($tit[0]['n']);

        /* traitement ressources */
        $this->link_url = $ressource[0]["u"];
        $this->link_format = $ressource[0]["2"];

        /* traitement des titres */
        $this->titles[0] = (isset($tit[0]['a'])) ? preg_replace('/-$| $|\||\($|\)$|\[$|\]$|\:$|\;$|\/$|\\$|\.+$/', '', trim($tit[0]['a']))  : '';
        $this->titles[1] = (isset($tit_sup[0]['a'])) ?  preg_replace('/-$| $|\||\($|\)$|\[$|\]$|\:$|\;$|\/$\|\$|\.+$/', '', trim($tit_sup[0]['a'])) : '';
        $this->titles[2] = (isset($tit_for[0]['a'])) ?  preg_replace('/-$| $|\||\($|\)$|\[$|\]$|\:$|\;$|\/$\|\$|\.+$/', '', trim($tit_for[0]['a'])) : '';
        $this->titles[3] = (isset($tit[0]['b'])) ?  preg_replace('/-$| $|\||\($|\)$|\[$|\]$|\:$|\;$|\/$\|\$|\.+$/', '', trim($tit[0]['b'])) : '';

        $this->general_note = "";
        $this->content_note = "";
        $this->abstract_note = "";

        /* prepare index_titre field for searching */
        if (!empty($abstract_note) && is_countable($abstract_note)) {
            for ($i = 0; $i < count($abstract_note); $i++) {
                $this->abstract_note .= $abstract_note[$i]." ";
            }
        }
        if (!empty($general_note) && is_countable($general_note)) {
            for ($i = 0; $i < count($general_note); $i++) {
                $this->general_note .= $general_note[$i]." ";
            }
        }
        if (!empty($content_note) && is_countable($content_note)) {
            for ($i = 0; $i < count($content_note); $i++) {
                $this->content_note .= $content_note[$i]." ";
            }
        }
        if (trim($this->abstract_note) == "") {
            $this->abstract_note = $this->general_note." ".$this->content_note;
        }

        global $pmb_keyword_sep ;
        if (!$pmb_keyword_sep) {
            $pmb_keyword_sep=" ";
        }
        if (is_array($index_sujets)) {
            $this->free_index = implode($pmb_keyword_sep, $index_sujets);
        } else {
            $this->free_index = $index_sujets;
        }
    }

    public function from_unimarc($record)
    {
        $this->document_type = $record->inner_guide['dt'];
        $this->bibliographic_level = $record->inner_guide['bl'];
        $this->hierarchic_level = $record->inner_guide['hl'];

        if ($this->hierarchic_level=="") {
            if ($this->bibliographic_level=="s") {
                $this->hierarchic_level="1";
            }
            if ($this->bibliographic_level=="m") {
                $this->hierarchic_level="0";
            }
        }
        if (function_exists("param_perso_prepare")) {
            param_perso_prepare($record);
        }
        $indicateur = array();
        $isbn = array();
        $cb = '';
        $tit = array();
        $editeur_lieu = array();
        $editeur_nom = '';
        $editeur_date = array();
        $editeur_date_machine = array();
        $collection_225 = array();
        $general_note = '';
        $content_note = '';
        $abstract_note = '';
        $EAN = '';
        $collection_411 = array();
        $bulletin_463 = '';
        $perio530a = '';
        $index_sujets = '';
        $aut_700 = array();
        $aut_701 = array();
        $aut_702 = array();
        $aut_710 = array();
        $aut_711 = array();
        $aut_712 = array();
        $origine_notice = array();
        $ressource = array();
        $info_995 = '';

        $inner_directory_len = 0;
        if (!empty($record->inner_directory) && is_countable($record->inner_directory)) {
            $inner_directory_len = count($record->inner_directory);
        }

        for ($i = 0; $i < $inner_directory_len; $i++) {
            $cle=$record->inner_directory[$i]['label'];
            $indicateur[$cle][]=substr($record->inner_data[$i]['content'], 0, 2);
            switch($cle) {
                case "001": /* id unimarc */
                    $this->unimarc_id = $record->get_subfield($cle);
                    break;
                case "010": /* isbn */
                    $isbn = $record->get_subfield($cle, 'a');
                    $subfield = $record->get_subfield($cle, "d");
                    if (!isset($this->prix)) {
                        $this->prix = '';
                    }
                    if (empty($this->prix) && !empty($subfield)) {
                        $this->prix = $subfield[0];
                    }
                case "011": /* isbn */
                    $isbn = $record->get_subfield($cle, 'a');
                    break;
                case "014": /* isbn */
                    $this->others_ids = $record->get_subfield($cle, 'a', 'b');
                    break;
                case "071": /* barcode */
                    $cb = $record->get_subfield($cle, "a");
                    $prix = $record->get_subfield($cle, "d");
                    if (!isset($this->prix)) {
                        $this->prix = '';
                    }
                    if (empty($this->prix) && !empty($prix)) {
                        $this->prix = $prix[0];
                    }
                    break;
                case "101": /* language */
                    //martizva NOTE: some server send language in UPCASE!!!
                    // $subfield = $record->get_subfield($cle,"a");
                    // $this->language_code = strtolower($subfield[0]);
                    // $subfield = $record->get_subfield($cle,"c");
                    // $this->original_language_code = strtolower($subfield[0]);
                    $this->language_code = $record->get_subfield_array($cle, "a");
                    $this->original_language_code = $record->get_subfield_array($cle, "c");
                    break;
                case "200": /* titles */
                    $tit = $record->get_subfield($cle, 'a', 'c', 'd', 'e', 'v');
                    $tit_200a=$record->get_subfield_array($cle, 'a');
                    $tit_200c=$record->get_subfield_array($cle, 'c');
                    $tit_200d=$record->get_subfield_array($cle, 'd');
                    $tit_200e=$record->get_subfield_array($cle, 'e');
                    $tit_200v=$record->get_subfield_array($cle, 'v');
                    $this->serie_200=$record->get_subfield($cle, 'h', 'i');
                    break;
                case "205": /* mention_edition */
                    $subfield = $record->get_subfield($cle, "a");
                    $this->mention_edition = $subfield[0];
                    break;
                case "210": /* publisher */
                case "214":
                case "219":
                    $editeur_lieu=$record->get_subfield_array_array($cle, "a");
                    $editeur_nom=$record->get_subfield_array_array($cle, "c");
                    $editeur_date=$record->get_subfield_array($cle, "d");
                    $editeur_date_machine=$record->get_subfield_array($cle, "h");
                    break;
                case "215": /* description */
                    $subfield = $record->get_subfield($cle, "a");
                    $this->page_nbr = (!empty($subfield) ? $subfield[0] : '');
                    $subfield = $record->get_subfield($cle, "c");
                    $this->illustration = (!empty($subfield) ? $subfield[0] : '');
                    $subfield = $record->get_subfield($cle, "d");
                    $this->size = (!empty($subfield) ? $subfield[0] : '');
                    $subfield = $record->get_subfield($cle, "e");
                    $this->accompagnement = (!empty($subfield) ? $subfield[0] : '');
                    break;
                case "225": /* collection */
                    $collection_225 = $record->get_subfield($cle, "a", "i", "v", "x");
                    break;
                case "300": /* inside */
                    $general_note = $record->get_subfield_array($cle, "a");
                    break;
                case "327": /* inside */
                    $content_note=$record->get_subfield_array($cle, "a");
                    break;
                case "330": /* abstract */
                    $abstract_note = $record->get_subfield_array($cle, "a");
                    break;
                case "345": /* EAN */
                    $EAN=$record->get_subfield($cle, "b");
                    break;
                case "410": /* collection */
                    $collection_410 = $record->get_subfield($cle, "t", "v", "x");
                    break;
                case "411": /* sub-collection */
                    $collection_411 = $record->get_subfield($cle, "t", "v", "x");
                    break;
                case "461": /* series ou perios*/
                    if (($this->bibliographic_level == 'a' || $this->bibliographic_level == 'b' || $this->bibliographic_level == 's')  && $this->hierarchic_level == '2') {
                        $perio = $record->get_subfield($cle, "t", "x", "v", "d", "e");
                        $this->perio_titre = $record->get_subfield_array($cle, "t");
                        $this->perio_issn = $record->get_subfield_array($cle, "x");
                        if ($record->get_subfield_array($cle, "v")) {
                            $this->bull_num = $record->get_subfield_array($cle, "v");
                        }
                        if ($record->get_subfield_array($cle, "d")) {
                            $this->bull_date = $record->get_subfield_array($cle, "d");
                            if (!empty($this->bull_date[0]) && pmb_strlen($this->bull_date[0]) < 8) {
                                //Transformation au format MySQL
                                $this->bull_date[0] = detectFormatDate($this->bull_date[0]);
                            }
                        }
                        if ($record->get_subfield_array($cle, "e")) {
                            $this->bull_mention = $record->get_subfield_array($cle, "e");
                        }
                    } else {
                        $serie = $record->get_subfield($cle, "t", "v");
                    }
                    break;
                case "463":/* Bulletins */
                    $bulletin_463 = $record->get_subfield($cle, "t", "v", "d", "e");
                    $this->bull_num = $record->get_subfield_array($cle, "v");
                    $this->bull_date = $record->get_subfield_array($cle, "d");
                    if (!empty($this->bull_date[0]) && pmb_strlen($this->bull_date[0]) < 8) {
                        //Transformation au format MySQL
                        $this->bull_date[0] = detectFormatDate($this->bull_date[0]);
                    }
                    $this->bull_mention = $record->get_subfield_array($cle, "e");
                    $this->bull_titre = $record->get_subfield_array($cle, "t");
                    break;
                case "500": /* Titres uniformes */
                    $this->tu_500 = $record->get_subfield($cle, "a", "k", "m", "q", "u", "v", "w");
                    $this->tu_500_i = $record->get_subfield_array_array($cle, "i");
                    $this->tu_500_j = $record->get_subfield_array_array($cle, "j");
                    $this->tu_500_l = $record->get_subfield_array_array($cle, "l");
                    $this->tu_500_n = $record->get_subfield_array_array($cle, "n");
                    $this->tu_500_r = $record->get_subfield_array_array($cle, "r");
                    $this->tu_500_s = $record->get_subfield_array_array($cle, "s");
                    break;
                case "503": /* Titres de forme */
                    $this->info_503 = $record->get_subfield($cle, "a", "e", "f", "h", "m", "n");
                    $this->info_503_d = $record->get_subfield_array_array($cle, "d");
                    $this->info_503_j = $record->get_subfield_array_array($cle, "j");
                    break;
                case "530":
                    $perio530a=$record->get_subfield_array($cle, 'a');
                    break;
                    //TODO AR
                    //recup 530 (voir import_unimarc_lien)
                case "600": // 600 PERSONAL NAME USED AS SUBJECT
                    $this->info_600_3=$record->get_subfield_array_array($cle, "3");
                    $this->info_600_a=$record->get_subfield_array_array($cle, "a");
                    $this->info_600_b=$record->get_subfield_array_array($cle, "b");
                    $this->info_600_c=$record->get_subfield_array_array($cle, "c");
                    $this->info_600_d=$record->get_subfield_array_array($cle, "d");
                    $this->info_600_f=$record->get_subfield_array_array($cle, "f");
                    $this->info_600_g=$record->get_subfield_array_array($cle, "g");
                    $this->info_600_j=$record->get_subfield_array_array($cle, "j");
                    $this->info_600_p=$record->get_subfield_array_array($cle, "p");
                    $this->info_600_t=$record->get_subfield_array_array($cle, "t");
                    $this->info_600_x=$record->get_subfield_array_array($cle, "x");
                    $this->info_600_y=$record->get_subfield_array_array($cle, "y");
                    $this->info_600_z=$record->get_subfield_array_array($cle, "z");
                    break;
                case "601": // 601 CORPORATE BODY NAME USED AS SUBJECT
                    $this->info_601_3=$record->get_subfield_array_array($cle, "3");
                    $this->info_601_a=$record->get_subfield_array_array($cle, "a");
                    $this->info_601_b=$record->get_subfield_array_array($cle, "b");
                    $this->info_601_c=$record->get_subfield_array_array($cle, "c");
                    $this->info_601_d=$record->get_subfield_array_array($cle, "d");
                    $this->info_601_e=$record->get_subfield_array_array($cle, "e");
                    $this->info_601_f=$record->get_subfield_array_array($cle, "f");
                    $this->info_601_g=$record->get_subfield_array_array($cle, "g");
                    $this->info_601_h=$record->get_subfield_array_array($cle, "h");
                    $this->info_601_t=$record->get_subfield_array_array($cle, "t");
                    $this->info_601_j=$record->get_subfield_array_array($cle, "j");
                    $this->info_601_x=$record->get_subfield_array_array($cle, "x");
                    $this->info_601_y=$record->get_subfield_array_array($cle, "y");
                    $this->info_601_z=$record->get_subfield_array_array($cle, "z");
                    break;
                case "602": // 602 FAMILY NAME USED AS SUBJECT
                    $this->info_602_3=$record->get_subfield_array_array($cle, "3");
                    $this->info_602_a=$record->get_subfield_array_array($cle, "a");
                    $this->info_602_f=$record->get_subfield_array_array($cle, "f");
                    $this->info_602_t=$record->get_subfield_array_array($cle, "t");
                    $this->info_602_j=$record->get_subfield_array_array($cle, "j");
                    $this->info_602_x=$record->get_subfield_array_array($cle, "x");
                    $this->info_602_y=$record->get_subfield_array_array($cle, "y");
                    $this->info_602_z=$record->get_subfield_array_array($cle, "z");
                    break;
                case "604": // 604 AUTEUR-TITRE USED AS SUBJECT
                    $this->info_604_a=$record->get_subfield_array_array($cle, "a");
                    $this->info_604_h=$record->get_subfield_array_array($cle, "h");
                    $this->info_604_i=$record->get_subfield_array_array($cle, "i");
                    $this->info_604_j=$record->get_subfield_array_array($cle, "j");
                    $this->info_604_k=$record->get_subfield_array_array($cle, "k");
                    $this->info_604_l=$record->get_subfield_array_array($cle, "l");
                    $this->info_604_x=$record->get_subfield_array_array($cle, "x");
                    $this->info_604_y=$record->get_subfield_array_array($cle, "y");
                    $this->info_604_z=$record->get_subfield_array_array($cle, "z");
                    break;
                case "605": // 605 TITLE USED AS SUBJECT
                    $this->info_605_3=$record->get_subfield_array_array($cle, "3");
                    $this->info_605_a=$record->get_subfield_array_array($cle, "a");
                    $this->info_605_h=$record->get_subfield_array_array($cle, "h");
                    $this->info_605_i=$record->get_subfield_array_array($cle, "i");
                    $this->info_605_j=$record->get_subfield_array_array($cle, "j");
                    $this->info_605_k=$record->get_subfield_array_array($cle, "k");
                    $this->info_605_l=$record->get_subfield_array_array($cle, "l");
                    $this->info_605_m=$record->get_subfield_array_array($cle, "m");
                    $this->info_605_n=$record->get_subfield_array_array($cle, "n");
                    $this->info_605_q=$record->get_subfield_array_array($cle, "q");
                    $this->info_605_r=$record->get_subfield_array_array($cle, "r");
                    $this->info_605_s=$record->get_subfield_array_array($cle, "s");
                    $this->info_605_u=$record->get_subfield_array_array($cle, "u");
                    $this->info_605_w=$record->get_subfield_array_array($cle, "w");
                    $this->info_605_j=$record->get_subfield_array_array($cle, "j");
                    $this->info_605_x=$record->get_subfield_array_array($cle, "x");
                    $this->info_605_y=$record->get_subfield_array_array($cle, "y");
                    $this->info_605_z=$record->get_subfield_array_array($cle, "z");
                    break;
                case "606": // RAMEAU / TOPICAL NAME USED AS SUBJECT
                    $this->info_606_3=$record->get_subfield_array_array($cle, "3");
                    $this->info_606_a=$record->get_subfield_array_array($cle, "a");
                    $this->info_606_j=$record->get_subfield_array_array($cle, "j");
                    $this->info_606_x=$record->get_subfield_array_array($cle, "x");
                    $this->info_606_y=$record->get_subfield_array_array($cle, "y");
                    $this->info_606_z=$record->get_subfield_array_array($cle, "z");
                    break;
                case "607": // 607 GEOGRAPHICAL NAME USED AS SUBJECT
                    $this->info_607_3=$record->get_subfield_array_array($cle, "3");
                    $this->info_607_a=$record->get_subfield_array_array($cle, "a");
                    $this->info_607_j=$record->get_subfield_array_array($cle, "j");
                    $this->info_607_x=$record->get_subfield_array_array($cle, "x");
                    $this->info_607_y=$record->get_subfield_array_array($cle, "y");
                    $this->info_607_z=$record->get_subfield_array_array($cle, "z");
                    break;
                case "608": // 608 Vedette matière de forme, de genre ou des caractéristiques physiques
                    $this->info_608_3=$record->get_subfield_array_array($cle, "3");
                    $this->info_608_a=$record->get_subfield_array_array($cle, "a");
                    $this->info_608_j=$record->get_subfield_array_array($cle, "j");
                    $this->info_608_x=$record->get_subfield_array_array($cle, "x");
                    $this->info_608_y=$record->get_subfield_array_array($cle, "y");
                    $this->info_608_z=$record->get_subfield_array_array($cle, "z");
                    break;
                case "610": /* mots clé */
                    $index_sujets=$record->get_subfield($cle, "a");
                    break;
                case "676": /* Dewey */
                    $this->dewey = $record->get_subfield($cle, "a");
                    $new_comment = $record->get_subfield($cle, "l");
                    if (!empty($new_comment[0])) {
                        $this->dewey["new_comment"] = $new_comment[0];
                    }
                    break;
                case "686": /* PCDM */
                    if (!$this->dewey) {
                        $this->dewey=$record->get_subfield($cle, "a");
                    }
                    break;
                case "700":
                    $aut_700=$record->get_subfield($cle, "a", "b", "c", "d", "4", "f", "N", "3");
                    break;
                case "701":
                    $aut_701=$record->get_subfield($cle, "a", "b", "c", "d", "4", "f", "N", "3");
                    break;
                case "702":
                    $aut_702=$record->get_subfield($cle, "a", "b", "c", "d", "4", "f", "N", "3");
                    break;
                case "710":
                    $aut_710=$record->get_subfield($cle, "a", "b", "c", "g", "d", "4", "f", "e", "k", "l", "m", "n", "3");
                    break;
                case "711":
                    $aut_711=$record->get_subfield($cle, "a", "b", "c", "g", "d", "4", "f", "e", "k", "l", "m", "n", "3");
                    break;
                case "712":
                    $aut_712=$record->get_subfield($cle, "a", "b", "c", "g", "d", "4", "f", "e", "k", "l", "m", "n", "3");
                    break;
                case "801": /* origine du catalogage */
                    $origine_notice=$record->get_subfield($cle, "a", "b");
                    break;
                case "856":
                    $ressource = $record->get_subfield($cle, "u", "q");
                    break;
                case "995": /* infos de la BDP */
                    $info_995 = $record->get_subfield($cle, "a", "b", "c", "d", "f", "k", "m", "n", "o", "r", "u");
                    $this->exemplaires = $info_995;
                    break;
                case "896": /* Thumbnail */
                    $thumbnail_url = $record->get_subfield($cle, "a");
                    $this->thumbnail_url = $thumbnail_url[0] ?? "";
                    break;
                    //Documents numériques
                case "897":
                    $this->doc_nums = $record->get_subfield($cle, "a", "b", "f", "p");
                    break;
                case "900":
                	$this->info_900 = $record->get_subfield($cle, "a");
                	break;
                default:
                    break;
            } /* end of switch */
        } /* end of for */

        //Traitement import perso
        if (function_exists('param_perso_prepare_fin')) {
            param_perso_prepare_fin($this->notice, $this->exemplaires);
        }

        //Récupération des catégories en lien avec le fichier xml
        category_auto::get_info_categ($record);
        $this->isbn = (!empty($isbn[0]) ? $this->process_isbn($isbn[0]) : '');
        if (function_exists("traite_categories_from_unimarc") && !$this->from_result) {
            $this->categories = traite_categories_from_unimarc($this);
        }
        /* INSERT de la notice OK, on va traiter les auteurs
         70# : personnal : type auteur 70                71# : collectivités : type auteur 71
         1 seul en 700                                   idem pour les déclinaisons
         n en 701 n en 702
         les 7#0 tombent en auteur principal : responsability_type = 0
         les 7#1 tombent en autre auteur : responsability_type = 1
         les 7#2 tombent en auteur secondaire : responsability_type = 2
         */
        $this->aut_array = array();
        /* on compte tout de suite le nbre d'enreg dans les répétables */
        $nb_repet_701 = (!empty($aut_701) && is_countable($aut_701)) ? count($aut_701) : 0;
        $nb_repet_711 = (!empty($aut_711) && is_countable($aut_711)) ? count($aut_711) : 0;
        $nb_repet_702 = (!empty($aut_702) && is_countable($aut_702)) ? count($aut_702) : 0;
        $nb_repet_712 = (!empty($aut_712) && is_countable($aut_712)) ? count($aut_712) : 0;

        /* renseignement de aut0 */
        if (isset($aut_700[0]['a']) && $aut_700[0]['a']!="") { /* auteur principal en 700 ? */
        	$this->aut_array[] = array(
                "entree" => $aut_700[0]['a'],
                "rejete" => $aut_700[0]['b'],
                "author_comment" => $aut_700[0]['c']." ".$aut_700[0]['d'],
                "date" => $aut_700[0]['f'],
                "type_auteur" => "70",
                "fonction" => $aut_700[0][4],
                "id" => 0,
                "responsabilite" => 0,
                "ordre" => 0,
                "authority_number" => $aut_700[0][3]
        	);
        } elseif (isset($aut_710[0]['a']) && $aut_710[0]['a']!="") { /* auteur principal en 710 ? */
            if (substr($indicateur["710"][0], 0, 1)=="1") {
                $type_auteur="72";
            } else {
                $type_auteur="71";
            }

            $lieu=$aut_710[0]['e'];
            if (!$lieu) {
                $lieu=$aut_710[0]['k'];
            }
            $this->aut_array[] = array(
                "entree" => $aut_710[0]['a'],
                "rejete" => $aut_710[0]['g'],
                "subdivision" => $aut_710[0]['b'],
                "author_comment" => $aut_710[0]['c'],
                "numero" => $aut_710[0]['d'],
                "ville" => $aut_710[0]['l'],
                "web" => $aut_710[0]['n'],
                "date" => $aut_710[0]['f'],
                "type_auteur" => intval($type_auteur),
                "fonction" => $aut_710[0][4],
                "id" => 0,
                "responsabilite" => 0,
                "ordre" => 0,
                "lieu" => $lieu,
                "pays" => $aut_710[0]['m'],
                "authority_number" => $aut_710[0][3]
            );
        }

        /* renseignement de aut1 */
        for ($i=0 ; $i < $nb_repet_701 ; $i++) {
        	$this->aut_array[] = array(
                "entree" => $aut_701[$i]['a'],
                "rejete" => $aut_701[$i]['b'],
                "author_comment" => $aut_701[$i]['c']." ".$aut_701[$i]['d'],
                "date" => $aut_701[$i]['f'],
                "type_auteur" => "70",
                "fonction" => $aut_701[$i][4],
                "id" => 0,
                "responsabilite" => 1,
                "ordre" => ($i+1),
                "authority_number" => $aut_701[$i][3]
        	);
        }
        for ($i=0 ; $i < $nb_repet_711 ; $i++) {
            if (substr($indicateur["711"][$i], 0, 1)=="1") {
                $type_auteur="72";
            } else {
                $type_auteur="71";
            }

            $lieu=$aut_711[$i]['e'];
            if (!$lieu) {
                $lieu=$aut_711[$i]['k'];
            }
            $this->aut_array[] = array(
                "entree" => $aut_711[$i]['a'],
                "rejete" => $aut_711[$i]['g'],
                "subdivision" => $aut_711[$i]['b'],
                "author_comment" => $aut_711[$i]['c'],
                "numero" => $aut_711[$i]['d'],
                "ville" => $aut_711[$i]['l'],
                "web" => $aut_711[$i]['n'],
                "date" => $aut_711[$i]['f'],
                "type_auteur" => intval($type_auteur),
                "fonction" => $aut_711[$i][4],
                "id" => 0,
                "responsabilite" => 1,
                "lieu" => $lieu,
                "pays" => $aut_711[$i]['m'],
                "ordre" => ($i+1),
                "authority_number" => $aut_711[$i][3]
            );
        }
        /* renseignement de aut2 */
        for ($i=0 ; $i < $nb_repet_702 ; $i++) {
        	$this->aut_array[] = array(
                "entree" => $aut_702[$i]['a'],
                "rejete" => $aut_702[$i]['b'],
                "author_comment" => $aut_702[$i]['c']." ".$aut_702[$i]['d'],
                "date" => $aut_702[$i]['f'],
                "type_auteur" => "70",
                "fonction" => $aut_702[$i][4],
                "id" => 0,
                "responsabilite" => 2,
                "ordre" => ($i+1),
                "authority_number" => $aut_702[$i][3]
        	);
        }
        for ($i=0 ; $i < $nb_repet_712 ; $i++) {
            if (substr($indicateur["712"][$i], 0, 1)=="1") {
                $type_auteur="72";
            } else {
                $type_auteur="71";
            }
            $lieu=$aut_712[$i]['e'];
            if (!$lieu) {
                $lieu=$aut_712[$i]['k'];
            }

            $this->aut_array[] = array(
                "entree" => $aut_712[$i]['a'],
                "rejete" => $aut_712[$i]['g'],
                "subdivision" => $aut_712[$i]['b'],
                "author_comment" => $aut_712[$i]['c'],
                "numero" => $aut_712[$i]['d'],
                "ville" => $aut_712[$i]['l'],
                "web" => $aut_712[$i]['n'],
                "date" => $aut_712[$i]['f'],
                "type_auteur" => intval($type_auteur),
                "fonction" => $aut_712[$i][4],
                "id" => 0,
                "responsabilite" => 2,
                "lieu" => $lieu,
                "pays" => $aut_712[$i]['m'],
                "ordre" => ($i+1),
            	"authority_number" => $aut_712[$i][3],
            );
        }
        /*  Added for some italian z39.50 server
         Some adjustment to clean the values from symbol like << and others */
        for ($i=0 ; $i < $nb_repet_701+$nb_repet_711+$nb_repet_702 +$nb_repet_712+1 ; $i++) {
            $this->aut_array[$i]['entree']=(!empty($this->aut_array[$i]['entree']) ? del_more_garbage($this->aut_array[$i]['entree']) : '');
            $this->aut_array[$i]['rejete']=(!empty($this->aut_array[$i]['rejete']) ? del_more_garbage($this->aut_array[$i]['rejete']) : '');
        }

        /* traitement des éditeurs */
        $editor=array();
        if (is_array($editeur_nom)) {
            foreach ($editeur_nom as $key_nom1 => $nom1) {
                foreach ($nom1 as $key_nom2 => $nom2) {
                	$mon_ed=array();
                    $mon_ed["c"]=$nom2;

                    if (!empty($editeur_lieu) && !empty($editeur_lieu[$key_nom1]) && !empty($editeur_lieu[$key_nom1][$key_nom2])) {
                        $mon_ed["a"]=$editeur_lieu[$key_nom1][$key_nom2];
                    }

                    $editor[]=$mon_ed;
                }
            }
        }
        $this->year=(!empty($editeur_date[0]) ? clean_string($editeur_date[0]) : '');

        $this->editors[0]['name'] = ((!empty($editor[0]) && !empty($editor[0]['c'])) ? clean_string($editor[0]['c']) : '');
        $this->editors[0]['ville'] = ((!empty($editor[0]) && !empty($editor[0]['a'])) ? clean_string($editor[0]['a']) : '');

        $this->editors[1]['name'] = ((!empty($editor[1]) && !empty($editor[1]['c'])) ? clean_string($editor[1]['c']) : '');
        $this->editors[1]['ville'] = ((!empty($editor[1]) && !empty($editor[1]['a'])) ? clean_string($editor[1]['a']) : '');

        /*  Added for some italian z39.50 server
         Some adjustment to clean the values from symbol like << and others */
        $this->editors[0]['name']=del_more_garbage($this->editors[0]['name']);
        $this->editors[1]['name']=del_more_garbage($this->editors[1]['name']);

        /* traitement des collections */
        $coll_name="";
        $subcoll_name="";
        $coll_issn="";
        $subcoll_issn="";
        $nocoll_ins="";

        // Collection : traitement de 225 et 410, préférence donnée au 410
        if (isset($collection_410[0]['t']) && $collection_410[0]['t']) {
            $coll_name=$collection_410[0]['t'];
        } elseif (isset($collection_225[0]['a']) && $collection_225[0]['a']!="") {
            $coll_name=$collection_225[0]['a'];
        }

        if (isset($collection_410[0]['x']) && $collection_410[0]['x']) {
            $coll_issn=$collection_410[0]['x'];
        } elseif (isset($collection_225[0]['x']) && $collection_225[0]['x']!="") {
            $coll_issn=$collection_225[0]['x'];
        }


        // Sous-collection : traitement de 225$i et 411, préférence donnée au 411
        if (isset($collection_411[0]['t']) && $collection_411[0]['t']) {
            $subcoll_name=$collection_411[0]['t'];
        } elseif (isset($collection_225[0]['i']) && $collection_225[0]['i']!="") {
            $subcoll_name=$collection_225[0]['i'];
        }

        if (isset($collection_411[0]['x']) && $collection_411[0]['x']) {
            $subcoll_issn=$collection_411[0]['x'];
        }

        // Numéro dans la collection, présent en 411$v, sinon en 410$v et enfin en 225$v
        if (isset($collection_411[0]['v']) && $collection_411[0]['v']!="") {
            $this->nbr_in_collection = $collection_411[0]['v'];
        } elseif (isset($collection_410[0]['v']) && $collection_410[0]['v']!="") {
            $this->nbr_in_collection = $collection_410[0]['v'];
        } elseif (isset($collection_225[0]['v']) && $collection_225[0]['v']!="") {
            $this->nbr_in_collection = $collection_225[0]['v'];
        } else {
            $this->nbr_in_collection = "";
        }

        $this->collection['name']=clean_string($coll_name);
        $this->collection['issn']=clean_string($coll_issn);

        $this->subcollection['name']=clean_string($subcoll_name);
        $this->subcollection['issn']=clean_string($subcoll_issn);

        /*  Added for some italian z39.50 server
         Some adjustment to clean the values from symbol like << and others */
        $this->collection['name']=del_more_garbage($this->collection['name']);
        $this->subcollection['name']=del_more_garbage($this->subcollection['name']);

        //TODO AR
        //nettoyage si notice de bulletin (voir import_unimarc_lien)
        //TEST si perio_titre vide -> recup 530
        if ($this->bibliographic_level=="s" && $this->hierarchic_level=="2") {
            if (!$this->perio_titre && $perio530a) {
                $this->perio_titre = $perio530a;
            }

            if (!$this->bull_titre && $this->serie_200) {
            	$this->bull_titre = array(clean_string($this->serie_200[0]['i']));
            } elseif (!$this->bull_titre && $tit_200a) {
            	$this->bull_titre = array(clean_string($tit_200a[0]));
            }

            if (!$this->bull_num && $this->serie_200) {
            	$this->bull_num = array(clean_string($this->serie_200[0]['h']));
            }

            if (!$this->bull_date && $editeur_date_machine) {
            	$this->bull_date =  array($editeur_date_machine[0]);
                if (!empty($this->bull_date[0]) && pmb_strlen($this->bull_date[0]) < 8) {
                    //Transformation au format MySQL
                    $this->bull_date[0] = detectFormatDate($this->bull_date[0]);
                }
            }
            if (!$this->bull_mention && $editeur_date) {
            	$this->bull_mention =  array(clean_string($editeur_date[0]));
            }
            $this->serie_200 = array();
        }


        /* Series  TODO: Check if it's Ok */
        $this->serie = ((!empty($serie) && !empty($serie[0]) && !empty($serie[0]['t'])) ? clean_string($serie[0]['t']) : '');
        if ($this->serie) {
            $this->nbr_in_serie = $serie[0]['v'];
        } else {
        	$this->serie = ((!empty($this->serie_200[0]) && !empty($this->serie_200[0]['i'])) ? clean_string($this->serie_200[0]['i']) : '');
            if ($this->serie) {
                $this->nbr_in_serie = $this->serie_200[0]['h'];
            } else {
                $this->nbr_in_serie = "";
            }
        }

        /* Traitement des notes */
        $this->general_note = "";
        $this->content_note = "";
        $this->abstract_note = "";
        if (!$abstract_note) {
        	$abstract_note=array();
        }
        $this->abstract_note= implode("\n", $abstract_note);

        if (!$general_note) {
        	$general_note=array();
        }
        $this->general_note= implode("\n", $general_note);

        if (!$content_note) {
        	$content_note=array();
        }
        $this->content_note= implode("\n", $content_note);

        /* traitement ressources */
        $this->link_url = ($ressource[0]["u"] ?? "");
        $this->link_format = ($ressource[0]["q"] ?? "");

        /* Titles processing */
        if (empty($tit_200a)) {
        	$tit_200a=array();
        }
        if (empty($tit_200c)) {
        	$tit_200c=array();
        }
        if (empty($tit_200d)) {
        	$tit_200d=array();
        }
        if (empty($tit_200e)) {
        	$tit_200e=array();
        }
        $tit[0]['a'] = implode(" ; ", $tit_200a);
        $tit[0]['c'] = implode(" ; ", $tit_200c);
        $tit[0]['d'] = implode(" ; ", $tit_200d);
        $tit[0]['e'] = implode(" ; ", $tit_200e);

        $this->titles[0] = clean_string($tit[0]['a']);
        $this->titles[1] = clean_string($tit[0]['c']);
        $this->titles[2] = clean_string($tit[0]['d']);
        $this->titles[3] = clean_string($tit[0]['e']);

        /*  Added for some italian z39.50 server
         Some adjustment to clean the values from symbol like << and others */
        $this->titles[0]=del_more_garbage($this->titles[0]);
        $this->titles[1]=del_more_garbage($this->titles[1]);
        $this->titles[2]=del_more_garbage($this->titles[2]);
        $this->titles[3]=del_more_garbage($this->titles[3]);

        global $pmb_keyword_sep ;
        if (!$pmb_keyword_sep) {
            $pmb_keyword_sep=" ";
        }
        if (is_array($index_sujets)) {
            $this->free_index = implode($pmb_keyword_sep, $index_sujets);
        } else {
            $this->free_index = $index_sujets;
        }

        if (isset($origine_notice[0]['b'])) {
            $this->origine_notice['nom']=clean_string($origine_notice[0]['b']);
        } else {
            $this->origine_notice['nom']="";
        }
        if (isset($origine_notice[0]['a'])) {
            $this->origine_notice['pays']=clean_string($origine_notice[0]['a']);
        } else {
            $this->origine_notice['pays']="";
        }
    }

    public function get_isbd_display()
    {
        $tdoc = new marc_list('doctype');

        if ($this->aut_array[0]['rejete']) {
            $author = $this->aut_array[0]['rejete']." ".$this->aut_array[0]['entree'];
        } else {
            $author = $this->aut_array[0]['entree'];
        }

        $display =
        $this->titles[0]." [".$tdoc->table[$this->document_type]."] / ".
        $author.".&nbsp;- ".
        $this->editors[0]['name'].", ".
        $this->year.".&nbsp;- ".
        $this->page_nbr." : ".
        $this->illustration." ; ".
        $this->size.".&nbsp;- (".
        $this->collection['name'].". ".
        $this->subcollection['name']." ; ".
        $this->nbr_in_collection.").<br />".
        $this->isbn.
        "<br />".$this->abstract_note;

        return array($this->isbn, $this->titles[0], $author, $display);
    }

    /*
     * Transforme les variables de classe comme si elles étaient postées
     */
    public function var_to_post($no_download='1')
    {
        global $deflt_integration_notice_statut;
        global $deflt_notice_is_new;
        global $biblio_notice;

        $this->statut = $deflt_integration_notice_statut;
        $this->notice_is_new = intval($deflt_notice_is_new);

        $biblio_notice = "";

        if ($this->bibliographic_level == 'a' && $this->hierarchic_level=='2') {
            //Pério et bulletin
            $this->perio_titre = $this->perio_titre[0];
            if (!empty($this->perio_issn) && !empty($this->perio_issn[0])) {
                $this->perio_issn = $this->perio_issn[0];
            } else {
                $this->perio_issn = "";
            }
            $this->bull_titre = $this->bull_titre[0] ?? '';
            $this->bull_date = $this->bull_date[0] ?? '';
            if (!empty($this->bull_mention) && isset($this->bull_mention[0])) {
                $this->bull_mention = $this->bull_mention[0];
            } else {
                $this->bull_mention = "";
            }
            $this->bull_num = $this->bull_num[0] ?? '';

            //On cherche si le perio existe
            $num_rows_perio = 0;
            if ($this->perio_titre && $this->perio_issn) {
                $req = "select notice_id, tit1, code from notices where niveau_biblio='s' and niveau_hierar='1'
						and tit1='" . addslashes($this->perio_titre) . "'
						and code='" . addslashes($this->perio_issn) . "' limit 1";
                $res_perio = pmb_mysql_query($req);
                $num_rows_perio = pmb_mysql_num_rows($res_perio);
            }
            if (!$num_rows_perio) {
                if ($this->perio_titre) {
                    $req="select notice_id, tit1, code from notices where niveau_biblio='s' and niveau_hierar='1'
						and tit1='" . addslashes($this->perio_titre) . "' limit 1";
                    $res_perio = pmb_mysql_query($req);
                    $num_rows_perio = pmb_mysql_num_rows($res_perio);
                }
            }
            if (!$num_rows_perio) {
                if ($this->perio_issn) {
                    $req="select notice_id, tit1, code from notices where niveau_biblio='s' and niveau_hierar='1'
							and code='" . addslashes($this->perio_issn) . "' limit 1";
                    $res_perio = pmb_mysql_query($req);
                    $num_rows_perio = pmb_mysql_num_rows($res_perio);
                }
            }
            if ($num_rows_perio == 1) {
                $perio_found = pmb_mysql_fetch_object($res_perio);
                $this->perio_titre = $perio_found->tit1;
                $this->perio_issn = $perio_found->code;
                $this->perio_id = intval($perio_found->notice_id);
            }
            //On cherche si le bulletin existe
            $num_rows_bull=0;
            if ($this->bull_num && $this->perio_id) {
                $req="select bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre from bulletins where bulletin_notice='".$this->perio_id."' and  bulletin_numero like '%".$this->bull_num."%' order by date_date desc, bulletin_id desc ";
                $res_bull = pmb_mysql_query($req);
                $num_rows_bull = pmb_mysql_num_rows($res_bull);
            }
            if (!$num_rows_bull && $this->bull_date && $this->perio_id) {
                $req="select bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre from bulletins where bulletin_notice='".$this->perio_id."' and date_date='".$this->bull_date."' order by date_date desc, bulletin_id desc ";
                $res_bull = pmb_mysql_query($req);
                $num_rows_bull = pmb_mysql_num_rows($res_bull);
            } elseif (($num_rows_bull > 1) && $this->bull_date && $this->perio_id) {
                $req="select bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre from bulletins where bulletin_notice='".$this->perio_id."' and date_date='".$this->bull_date."' and  bulletin_numero like '%".$this->bull_num."%' order by date_date desc, bulletin_id desc ";
                $res_bull = pmb_mysql_query($req);
                $num_rows_bull = pmb_mysql_num_rows($res_bull);
            }
            if (!$num_rows_bull && $this->bull_mention && $this->bull_num && $this->perio_id) {
                $req="select bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre from bulletins where bulletin_notice='".$this->perio_id."' and mention_date='".$this->bull_mention."' and  bulletin_numero like '%".$this->bull_num."%' order by date_date desc, bulletin_id desc ";
                $res_bull = pmb_mysql_query($req);
                $num_rows_bull = pmb_mysql_num_rows($res_bull);
            } elseif (($num_rows_bull > 1) && $this->bull_mention && $this->perio_id) {
                if ($this->bull_date[0]) {
                    $req="select bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre from bulletins where bulletin_notice='".$this->perio_id."' and date_date='".$this->bull_date."' and mention_date='".$this->bull_mention."' order by date_date desc, bulletin_id desc ";
                } else {
                    $req="select bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre from bulletins where bulletin_notice='".$this->perio_id."' and mention_date='".$this->bull_mention."' and  bulletin_numero like '%".$this->bull_num."%' order by date_date desc, bulletin_id desc ";
                }
                $res_bull = pmb_mysql_query($req);
                $num_rows_bull = pmb_mysql_num_rows($res_bull);
            }

            if ($num_rows_bull) { //Il peut y en avoir +sieurs mais on prend le plus récent
                $bull_found = pmb_mysql_fetch_object($res_bull);
                $this->bull_titre = $bull_found->bulletin_titre;
                $this->bull_date = $bull_found->date_date;
                $this->bull_mention = $bull_found->mention_date;
                $this->bull_num = $bull_found->bulletin_numero;
                $this->bull_id = intval($bull_found->bulletin_id);
            }

            $biblio_notice = "art";
        }

        //TODO AR
        //si bulletin, inspiré de juste au dessus, retrouver le pério, checker si le bulletin existe déjà, mettre la global biblio_notice à bull
        if ($this->bibliographic_level == 's' && $this->hierarchic_level=='2') {
            //Pério et bulletin
            $this->perio_titre = $this->perio_titre[0] ?? "";
            $this->perio_issn = $this->perio_issn[0] ?? "";
            $this->bull_titre = $this->bull_titre[0] ?? "";
            $this->bull_date = $this->bull_date[0] ?? "";
            $this->bull_mention = $this->bull_mention[0] ?? "";
            $this->bull_num = $this->bull_num[0] ?? "";

            //On cherche si le perio existe
            $num_rows_perio = 0;
            if ($this->perio_titre && $this->perio_issn) {
                $req="select notice_id, tit1, code from notices where niveau_biblio='s' and niveau_hierar='1'
						and tit1='" . addslashes($this->perio_titre) . "'
						and code='" . addslashes($this->perio_issn) . "' limit 1";
                $res_perio = pmb_mysql_query($req);
                $num_rows_perio = pmb_mysql_num_rows($res_perio);
            }
            if (!$num_rows_perio && $this->perio_titre) {
            	$req="select notice_id, tit1, code from notices where niveau_biblio='s' and niveau_hierar='1'
            		and tit1='" . addslashes($this->perio_titre) . "'
            		limit 1";
            	$res_perio = pmb_mysql_query($req);
            	$num_rows_perio = pmb_mysql_num_rows($res_perio);
            }
            if (!$num_rows_perio && $this->perio_issn) {
                $req="select notice_id, tit1, code from notices where niveau_biblio='s' and niveau_hierar='1'
                        and code='" . addslashes($this->perio_issn) . "' limit 1";
                $res_perio = pmb_mysql_query($req);
                $num_rows_perio = pmb_mysql_num_rows($res_perio);
            }
            if ($num_rows_perio == 1) {
                $perio_found = pmb_mysql_fetch_object($res_perio);
                $this->perio_titre = $perio_found->tit1;
                $this->perio_issn = $perio_found->code;
                $this->perio_id = intval($perio_found->notice_id);
            }
            //On cherche si le bulletin existe
            $num_rows_bull=0;
            if ($this->bull_num && $this->perio_id) {
                $req="select bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre, num_notice from bulletins where bulletin_notice='".$this->perio_id."' and  bulletin_numero like '%".$this->bull_num."%' order by date_date desc, bulletin_id desc ";
                $res_bull = pmb_mysql_query($req);
                $num_rows_bull = pmb_mysql_num_rows($res_bull);
            }
            if (!$num_rows_bull && $this->bull_date && $this->perio_id) {
                $req="select bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre, num_notice from bulletins where bulletin_notice='".$this->perio_id."' and date_date='".$this->bull_date."' order by date_date desc, bulletin_id desc ";
                $res_bull = pmb_mysql_query($req);
                $num_rows_bull = pmb_mysql_num_rows($res_bull);
            } elseif (($num_rows_bull > 1) && $this->bull_date && $this->perio_id) {
                $req="select bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre, num_notice from bulletins where bulletin_notice='".$this->perio_id."' and date_date='".$this->bull_date."' and  bulletin_numero like '%".$this->bull_num."%' order by date_date desc, bulletin_id desc ";
                $res_bull = pmb_mysql_query($req);
                $num_rows_bull = pmb_mysql_num_rows($res_bull);
            }
            if (!$num_rows_bull && $this->bull_mention && $this->bull_num && $this->perio_id) {
                $req="select bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre, num_notice from bulletins where bulletin_notice='".$this->perio_id."' and mention_date='".$this->bull_mention."' and  bulletin_numero like '%".$this->bull_num."%' order by date_date desc, bulletin_id desc ";
                $res_bull = pmb_mysql_query($req);
                $num_rows_bull = pmb_mysql_num_rows($res_bull);
            } elseif (($num_rows_bull > 1) && $this->bull_mention && $this->perio_id) {
                if ($this->bull_date[0]) {
                    $req="select bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre, num_notice from bulletins where bulletin_notice='".$this->perio_id."' and date_date='".$this->bull_date."' and mention_date='".$this->bull_mention."' order by date_date desc, bulletin_id desc ";
                } else {
                    $req="select bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre, num_notice from bulletins where bulletin_notice='".$this->perio_id."' and mention_date='".$this->bull_mention."' and  bulletin_numero like '%".$this->bull_num."%' order by date_date desc, bulletin_id desc ";
                }
                $res_bull = pmb_mysql_query($req);
                $num_rows_bull = pmb_mysql_num_rows($res_bull);
            }

            if ($num_rows_bull) { //Il peut y en avoir +sieurs mais on prend le plus récent
                $bull_found = pmb_mysql_fetch_object($res_bull);
                $this->bull_titre = $bull_found->bulletin_titre;
                $this->bull_date = $bull_found->date_date;
                $this->bull_mention = $bull_found->mention_date;
                $this->bull_num = $bull_found->bulletin_numero;
                $this->bull_id = intval($bull_found->bulletin_id);
                $this->bull_notice = intval($bull_found->num_notice);
                //Recupérer id de notice, et avant d'appeler insert_in_database -> vérifier que la valeur n'est pas remplie
            }

            $biblio_notice = "bull";
        }


        if ($this->source_id) {
            $requete="select upload_doc_num from connectors_sources where source_id=".$this->source_id."";
            $resultat=pmb_mysql_query($requete);
            if (pmb_mysql_num_rows($resultat)) {
                $r=pmb_mysql_fetch_object($resultat);
                if ($r->upload_doc_num) {
                    $no_download=0;
                } else {
                    $no_download=1;
                }
            }
        }
        //documents numeriques
        if (!empty($this->doc_nums)) {
            foreach ($this->doc_nums as $k=>$v) {
                $this->doc_nums[$k]['__nodownload__'] = $no_download;
            }
        }
    }

    /**
     * Importer les liens entre les notices
     */
    public function import_notice_links()
    {
        $notice = new iso2709_record($this->notice);
        $this->notices_crees = $this->notices_a_creer = $this->bulletins_a_creer = $this->bulletins_crees = [];

        $id_unimarc = 0;
        $fiel001 = $notice->get_subfield('001');
        if (!empty($fiel001)) {
            $id_unimarc = intval($fiel001[0] ?? 0);
        }

        $tit_200d = "";
        $fiel200d = $notice->get_subfield_array("200", 'd');
        if (!empty($fiel200d)) {
            $tit_200d = $fiel200d[0] ?? "";
        }

        if ($this->is_article() && $tit_200d == 'Article_expl_bulletin') {
            // Quand on est sur le cas de la reconstruction d'un exemplaire de bulletin
            $issues = $this->get_linked_relations($notice, '463', 'bull_expl');
            $data = [];
            foreach ($issues as $relation) {
                $data = $this->get_data_from_fields_relation($relation);
            }
            if (!empty($data)) {
                $id_serial = $this->create_record_serial($data['field0'], $data['titles'][1], $data['code_x']);
                $issue = [
                    'titre' => $data['titles'][0],
                    'date' => $data['date'],
                    'mention' => $data['mention'],
                    'num' => $data['num'],
                ];
                $this->create_issue($id_serial, $issue);
            }
            return;
        }

        $niveau_biblio = $this->bibliographic_level . $this->hierarchic_level;
        switch ($niveau_biblio) {
            case 's1':
                // Lien vers un périodique
                $articles = $this->get_linked_relations($notice, '464', 'art');
                $issues = $this->get_linked_relations($notice, '462', 'bull');
                // La creation du bulletin doit tenir compte des valeurs en propriétés
                // Elles peuvent être réécrite dans une fonction personnalisée nommée traite_info_subst
                if (!empty($issues) && is_array($issues)) {
                    $issues[0] = $this->set_data_from_fields_relation($issues[0], 'v', $this->bull_num);
                    $issues[0] = $this->set_data_from_fields_relation($issues[0], 'e', $this->bull_mention);
                    $issues[0] = $this->set_data_from_fields_relation($issues[0], 'd', $this->bull_date);
                    $issues[0] = $this->set_data_from_fields_relation($issues[0], 't', $this->bull_titre);
                }
                $this->create_issues_and_articles($issues, $articles, $notice);
                break;
            case 'b2':
                // Lien vers une notice de bulletin
                $serials = $this->get_linked_relations($notice, '461', 'parent', 'b');
                // La creation du périodique doit tenir compte des valeurs en propriétés
                // Elles peuvent être réécrite dans une fonction personnalisée nommée traite_info_subst
                if (!empty($serials) && is_array($serials)) {
                    $serials[0] = $this->set_data_from_fields_relation($serials[0], 't', $this->perio_titre);
                    $serials[0] = $this->set_data_from_fields_relation($serials[0], 'x', $this->perio_issn);
                }
                $this->create_links_issues_notice($serials, $notice);
                break;
            case 'a2':
                // Lien vers un article
                $serials = $this->get_linked_relations($notice, '461', 'perio');
                // La creation du périodique doit tenir compte des valeurs en propriétés
                // Elles peuvent être réécrite dans une fonction personnalisée nommée traite_info_subst
                if (!empty($serials) && is_array($serials)) {
                    $serials[0] = $this->set_data_from_fields_relation($serials[0], 't', $this->perio_titre);
                    $serials[0] = $this->set_data_from_fields_relation($serials[0], 'x', $this->perio_issn);
                }
                $issues = $this->get_linked_relations($notice, '463', 'bull');
                // La creation du bulletin doit tenir compte des valeurs en propriétés
                // Elles peuvent être réécrite dans une fonction personnalisée nommée traite_info_subst
                if (!empty($issues) && is_array($issues)) {
                    $issues[0] = $this->set_data_from_fields_relation($issues[0], 'v', $this->bull_num);
                    $issues[0] = $this->set_data_from_fields_relation($issues[0], 'e', $this->bull_mention);
                    $issues[0] = $this->set_data_from_fields_relation($issues[0], 'd', $this->bull_date);
                    $issues[0] = $this->set_data_from_fields_relation($issues[0], 't', $this->bull_titre);
                }
                $field = [
                    'id_base' => $id_unimarc,
                    'titre' => $this->titles[0],
                ];
                $this->create_links_articles($issues, $serials, $field, $notice);
                break;
        }

        // Traitement des relations mères/filles
        $this->create_linked_relations($notice, 'parent');
        $this->create_linked_relations($notice, 'child');

        // Traitement des relations horizontales
        $this->create_linked_relations($notice, 'pair');

        // On traite les notices qui ont été mises en attente de création
        if (isset($this->notices_a_creer[$id_unimarc])) {
            $nb_records = count($this->notices_a_creer[$id_unimarc]);
            for ($i = 0; $i < $nb_records; $i++) {
                switch ($this->notices_a_creer[$id_unimarc][$i]['lnk']) {
                    case 'parent':
                        // On a une relation vers un parent
                        if (! notice_relations::relation_exists($this->notices_a_creer[$id_unimarc][$i]['id_asso'], $this->notice_id, $this->notices_a_creer[$id_unimarc][$i]['type_lnk'])) {
                            notice_relations::insert_from_import($this->notices_a_creer[$id_unimarc][$i]['id_asso'], $this->notice_id, $this->notices_a_creer[$id_unimarc][$i]['type_lnk'], $this->notices_a_creer[$id_unimarc][$i]['rank']);
                        }
                        break;
                    case 'child':
                    case 'pair':
                        // On a une relation vers un enfant ou une relation vers une notice horizontale
                        if (! notice_relations::relation_exists($this->notice_id, $this->notices_a_creer[$id_unimarc][$i]['id_asso'], $this->notices_a_creer[$id_unimarc][$i]['type_lnk'])) {
                            notice_relations::insert_from_import($this->notice_id, $this->notices_a_creer[$id_unimarc][$i]['id_asso'], $this->notices_a_creer[$id_unimarc][$i]['type_lnk'], $this->notices_a_creer[$id_unimarc][$i]['rank']);
                        }
                        break;
                    case 'art':
                        // On a un lien d'un article vers un periodique
                        if (empty($this->notices_crees[$id_unimarc])) {
                            // On ne peut pas passer par la
                            $query = "INSERT INTO notices(tit1, niveau_biblio, niveau_hierar, npages) VALUES('" . addslashes($this->notices_a_creer[$id_unimarc][$i]['titre_art']) . "', 'a', '2', '" . addslashes($this->notices_a_creer[$id_unimarc][$i]['page'])."')";
                            pmb_mysql_query($query);
                            $id_article = pmb_mysql_insert_id();
                            audit::insert_creation(AUDIT_NOTICE, $id_article);

                            // Calcul des droits d'accès s'ils sont activés
                            notice::calc_access_rights($id_article);
                            $this->notices_crees[$id_unimarc] = $id_article;
                        }

                        $id_serial = $this->notices_a_creer[$id_unimarc][$i]['id_asso'];
                        $issue = $this->notices_a_creer[$id_unimarc][$i];
                        $this->create_record_article('', '', '', $id_article ? $id_article : $this->notice_id, $issue, '', '', $id_serial);
                        break;
                    case 'perio':
                        // On a un lien d'un periodique vers un article
                        if (empty($this->notices_crees[$id_unimarc])) {
                            // On ne peut pas passer par la
                            $query = "INSERT INTO notices(tit1, code, niveau_biblio, niveau_hierar) VALUES('" . addslashes($this->notices_a_creer[$id_unimarc][$i]['titre_perio']) . "', '" . addslashes($this->notices_a_creer[$id_unimarc][$i]['code']) . ", 's', '1')";
                            pmb_mysql_query($query);
                            $id_serial = pmb_mysql_insert_id();
                            audit::insert_creation(AUDIT_NOTICE, $id_serial);

                            // Calcul des droits d'accès s'ils sont activés
                            notice::calc_access_rights($id_serial);
                            $this->notices_crees[$id_unimarc] = $id_serial;
                        } else {
                            $id_serial = $this->notices_crees[$id_unimarc];
                        }

                        $issue = [
                            'titre' => $this->notices_a_creer[$id_unimarc][$i]['titre_bull'],
                            'date' => $this->notices_a_creer[$id_unimarc][$i]['date'],
                            'mention' => $this->notices_a_creer[$id_unimarc][$i]['mention'],
                            'num' => $this->notices_a_creer[$id_unimarc][$i]['num'],
                        ];
                        $this->create_record_article('', '', '', $this->notices_a_creer[$id_unimarc][$i]['id_asso'], $issue, '', '', $id_serial);
                        break;
                }
            }
            unset($this->notices_a_creer[$id_unimarc]);
        }

        // On rattache le perio a ses notices de bulletin
        if (!empty($this->bulletins_a_creer[$id_unimarc])) {
            $id_serial = $this->notice_id;
            $nb_issues = count($this->bulletins_a_creer[$id_unimarc]);
            for ($i = 0; $i < $nb_issues; $i++) {
                $issue = $this->bulletins_a_creer[$id_unimarc][$i];
                $id_issue = $this->create_issue($id_serial, $issue);
                $this->create_link_record_issue('', $id_serial, $id_issue, $this->bulletins_a_creer[$id_unimarc][$i]['bull_notice'], '', $issue);
            }
            unset($this->bulletins_a_creer[$id_unimarc]);
        }
    }

    protected function get_relations($relations, $direction, $lien = self::LINK)
    {
    	$relations_lnk = array();
        foreach ($relations as $relation) {
            foreach ($relation as $field) {
                $relation_type = '';
                if ($field['label'] == 9) {
                    $exploded_content = explode(':', $field['content']);
                    if ($exploded_content[0] == $lien) {
                        $relation_type = $exploded_content[1];
                    }
                }
                if (!empty($relation_type) && $relation_type == $direction) {
                    $relations_lnk[] = $relation;
                    break;
                }
            }
        }
        return $relations_lnk;
    }

    /**
     * Nettoyage des relations
     * @param iso2709_record $notice
     */
    private function get_cleaned_linked_relations($notice, $direction)
    {
        $linked_relations = $this->get_linked_relations($notice, '', $direction);
        if (!empty($linked_relations)) {
            foreach ($linked_relations as $key => $relation) {
                $title = "";
                foreach ($relation as $field) {
                    if ($field['label'] == 't') {
                        $title = $field['content'];
                    }
                }
                if (empty($title)) {
                    // Si pas de titre, on évite de créer une notice sans titre
                    unset($linked_relations[$key]);
                }
            }
        }
        return $linked_relations;
    }

    /**
     * Création des relations
     * @param iso2709_record $notice
     */
    private function create_linked_relations($notice, $direction)
    {
        $linked_relations = $this->get_cleaned_linked_relations($notice, $direction);
        // Reste-t-il des relations à créer après le nettoyage ci-dessus ?
        if (!empty($linked_relations)) {
            foreach ($linked_relations as $linked_relation) {
                $this->create_linked_relation($linked_relation);
            }
        }
    }

    /**
     * Récupération des informations des liens d'une notice
     *
     * @param array|iso2709_record $notice
     * @param string $cle_unimarc
     * @param string $lien
     * @param string $type_lien
     * @return array
     */
    public function get_linked_relations($notice = [], $cle_unimarc = '', $lien = '', $type_lien = '')
    {
        $result_tab = [];
        if (!empty($cle_unimarc)) {
            $result_tab = $notice->get_subfield_array_array($cle_unimarc);
            if (!empty($lien) && !empty($result_tab)) {
                $result_tab = $this->get_relations($result_tab, $lien);
                if (!empty($type_lien)) {
                    $result_tab = $this->get_relations($result_tab, $type_lien, self::TYPE_LINK);
                }
            }
        } elseif (!empty($lien)) {
        	$link_tab = array();
            foreach ($notice->inner_data as $fields) {
                $result_tab = $notice->get_subfield_array_array($fields['label']);
                $relations_lnk = $this->get_relations($result_tab, $lien);
                if (!empty($relations_lnk)) {
                    foreach ($relations_lnk as $relation_lnk) {
                        $link_tab[] = $relation_lnk;
                    }
                }
            }

            $result_tab = $link_tab;
            if (!empty($type_lien) && !empty($result_tab)) {
                $result_tab = $this->get_relations($result_tab, $type_lien, self::TYPE_LINK);
            }
        } elseif (!empty($type_lien)) {
        	$type_link_tab = array();
            foreach ($notice->inner_data as $fields) {
                $result_tab = $notice->get_subfield_array_array($fields['label']);
                $relations_lnk = $this->get_relations($result_tab, $type_lien, self::TYPE_LINK);
                if (!empty($relations_lnk)) {
                    foreach ($relations_lnk as $relation_lnk) {
                        $type_link_tab[] = $relation_lnk;
                    }
                }
            }
            $result_tab = $type_link_tab;
        }

        return $result_tab;
    }

    /**
     * Création de bulletins et d'articles pour une notice de periodique
     * (creer_bulletinage_et_articles)
     *
     * @param array $tab_issues
     * @param array $tab_art
     * @param iso2709_record $notice
     */
    public function create_issues_and_articles($tab_issues, $tab_art, $notice)
    {
        global $msg;

        $code = !empty($this->isbn) ? $this->isbn : 0;
        $id_serial_to_keep = $this->serial_already_exist($this->titles[0], $code, $this->notice_id);
        if (!empty($id_serial_to_keep) && $id_serial_to_keep !== false) {
            pmb_mysql_query("INSERT INTO error_log (error_origin, error_text) VALUES ('import_" . addslashes(SESSid) . ".inc', '" . $msg[542] . " $this->notice_id " . " $this->isbn " . addslashes(clean_string($this->titles[0])) . "') ");

            // Si j'ai déja une notice dans la base avec ce titre et ce code je supprime celle que je suis en train d'importer
            $perio_traite = new serial($this->notice_id);
            $perio_traite->replace($id_serial_to_keep);
            $perio_traite->serial_delete();

            // Je travaille avec le periodique qui était dans la base
            $this->notice_id = $id_serial_to_keep;
            $id_unimarc = $notice->get_subfield("001")[0];
            $this->notices_crees[$id_unimarc] = $id_serial_to_keep;

            if (!empty(static::$record_tmp[intval($id_unimarc)])) {
                // On remplace l'id supprimer par celui du perio présent en base
                static::$record_tmp[intval($id_unimarc)] = $id_serial_to_keep;
            }
        }

        if (!empty($tab_issues)) {
            foreach ($tab_issues as $relation) {
                $data = $this->get_data_from_fields_relation($relation);
                $this->create_issue($this->notice_id, $data, '', '');
            }
        }

        if (!empty($tab_art)) {
            foreach ($tab_art as $relation) {
                $data = $this->get_data_from_fields_relation($relation);

                $id = $data['id'];
                $titles = $data['titles'];

                if (!isset($this->notices_crees[$id]) && empty($data['field0'])) {
                    $issue = [
                        'titre' => $titles[1],
                        'date' => $data['date'],
                        'mention' => $data['mention'],
                        'num' => $data['num'],
                    ];

                    $id_unimarc_art = intval($id);
                    if (!empty(static::$record_tmp) && !empty(static::$record_tmp[$id_unimarc_art])) {
                        // Article déjà créer avec un bulletin ( on se retrouve avec 2 bulletin)

                        if (!empty($this->bulletins_crees) && !empty($this->bulletins_crees[$this->notice_id][$data['num']]) && !empty($this->bulletins_crees[$this->notice_id][$data['num']][$data['date'].$data['mention']])) {
                            // On supprimer le bulletin temp
                            if (!empty(static::$issue_tmp) && !empty(static::$issue_tmp[$id_unimarc_art])) {
                                import_records::delete_bulletin(static::$issue_tmp[$id_unimarc_art]);
                            }
                            $id_issue = $this->bulletins_crees[$this->notice_id][$data['num']][$data['date'].$data['mention']];
                            $this->create_link_article_to_issue(static::$record_tmp[$id_unimarc_art], $id_issue);
                        }
                    } else {
                        // creation de l'article
                        $this->create_record_article($id_unimarc_art, $titles[0], $data['page'], 0, $issue, "", "", $this->notice_id);
                    }
                } elseif (!empty($data['field0'])) {
                    // On enregistre les informations pour créer l'article plus tard
                    $this->notices_a_creer[$id][] = [
                        'type_lnk' => $data['type_lnk'],
                        'lnk' => $data['lnk'],
                        'rank' => $data['rank'],
                        'titre_art' => $titles[0],
                        'titre' => $titles[1],
                        'num' => $data['num'],
                        'mention' => $data['mention'],
                        'date' => $data['date'],
                        'id_asso' => $this->notice_id,
                        'page' => $data['page'],
                    ];
                }
            }
        }
    }

    protected function get_data_issue_from_unimarc($notice = null)
    {
        return [
        	'titre' => trim($notice->get_subfield_array('200', 'i')[0] ?? ""),
        	'date' => trim($notice->get_subfield_array('210', 'h')[0] ?? ""),
        	'mention' => trim($notice->get_subfield_array('210', 'd')[0] ?? ""),
        	'num' => trim($notice->get_subfield_array('200', 'h')[0] ?? ""),
        ];
    }

    /**
     * Création des liens pour les notices de bulletin (creer_liens_pour_bull_notice)
     *
     * @param array $serials
     * @param iso2709_record $notice
     */
    public function create_links_issues_notice($serials = [], $notice = null)
    {
        global $pmb_synchro_rdf;

        $id_unimarc = $notice->get_subfield('001');
        $id_unimarc = (!empty($id_unimarc[0]) ? intval($id_unimarc[0]) : 0);

        $titre530 = $notice->get_subfield_array('530', 'a')[0] ?? "";
        $titre530 = trim($titre530);
        if (empty($serials)) {
            if (!isset($this->notices_crees[$id_unimarc])) {
                if ($pmb_synchro_rdf) {
                    $synchro_rdf = new synchro_rdf();
                }
                $converto_to_mono = false;
                if (empty($this->perio_id)) {
                    if (empty($titre530) && !empty($this->perio_titre)) {
                        $titre530 = $this->perio_titre;
                    }
                    if (empty($titre530)) {
                        // On a aucune infos sur le périodique, on vas transformer le l'article en monographie
                        $converto_to_mono = true;
                    } else {
                        if (!isset($this->perio_issn)) {
                            $this->perio_issn = '';
                        }
                        $this->perio_id = $this->create_record_serial(0, $titre530, $this->perio_issn);
                        if ($pmb_synchro_rdf) {
                            $synchro_rdf->addRdf($this->perio_id, 0);
                        }
                    }
                }

                if ($converto_to_mono) {
                    if ($this->notice_id) {
                        // la notice de bulletin est cree mais on sait pas sur quoi la lie, on le passe en monographie
                        $query = "UPDATE notices SET niveau_biblio = 'm', niveau_hierar = '0' WHERE notice_id = '$this->notice_id'";
                        pmb_mysql_query($query);
                        return;
                    } else {
                        pmb_mysql_query("INSERT INTO error_log (error_origin, error_text) VALUES ('import_" . addslashes(SESSid) . ".inc', 'this->notice_id empty') ");
                        return;
                    }
                }

                if (empty($this->bull_id)) {
                    $bull_title = $this->bull_titre;
                    if (empty($bull_title) && !empty($notice->get_subfield_array('200', 'i'))) {
                        $bull_title = $notice->get_subfield_array('200', 'i')[0];
                    }

                    $bull_date = $this->bull_date;
                    if (empty($bull_date) && !empty($notice->get_subfield_array('210', 'h'))) {
                        $bull_date = $notice->get_subfield_array('210', 'h')[0];
                    }

                    $bull_mention = $this->bull_mention;
                    if (empty($bull_mention) && !empty($notice->get_subfield_array('210', 'd'))) {
                        $bull_mention = $notice->get_subfield_array('210', 'd')[0];
                    }

                    $bull_num = $this->bull_num;
                    if (empty($bull_num) && !empty($notice->get_subfield_array('200', 'h'))) {
                        $bull_num = $notice->get_subfield_array('200', 'h')[0];
                    }

                    $this->bull_id = $this->create_issue($this->perio_id, [
                        'titre' => trim($bull_title),
                        'date' => trim($bull_date),
                        'mention' => trim($bull_mention),
                        'num' => trim($bull_num),
                    ]);
                    if ($pmb_synchro_rdf) {
                        $synchro_rdf->addRdf(0, $this->bull_id);
                    }
                }
                $this->create_link_record_issue(0, $this->perio_id, $this->bull_id, $this->notice_id);

                //Insertion dans la table notices_relation, ajout de la relatioin entre la notice de bulletin et la notice de perio
                notice_relations::insert($this->notice_id, $this->perio_id, 'b', 1, 'up', false);
            }
        } else {
            $id = 0;
            foreach ($serials as $relation) {
                $data = $this->get_data_from_fields_relation($relation);
                $id = intval($data['id']);

                if (empty($id) || $id === 0) {
                    // Si tu tombe ici, il y a un problème...
                    // l'id dans le lien vide ou $serials est vide, mais c'est pas normal
                    return;
                }

                if (empty($this->notices_crees[$id]) && empty($data['field0'])) {
                    // On créé le periodique car il n'est pas créé
                    if (empty($titre530)) {
                        $titre530 = 'Sans titre';
                    }
                    $id_serial = $this->create_record_serial($id, $titre530, $data['code_x'] ?? 0);
                    $issue = $this->get_data_issue_from_unimarc($notice);
                    $id_issue = $this->create_issue($id_serial, $issue);
                    $this->bull_id = $id_issue;
                    $this->create_link_record_issue($id_unimarc, $id_serial, $id_issue, $this->notice_id, $this->titles[0], $issue);
                } elseif (!empty($this->notices_crees[$id])) {
                    // La notice de perio est déja créée
                    $id_serial = $this->notices_crees[$id];
                    $issue = $this->get_data_issue_from_unimarc($notice);
                    $id_issue = $this->create_issue($id_serial, $issue);
                    $this->bull_id = $id_issue;
                    $this->create_link_record_issue($id_unimarc, $id_serial, $id_issue, $this->notice_id, $this->titles[0], $issue);
                } elseif (!empty($data['field0'])) {
                    // Le lien sera à refaire plus tard
                    $this->bulletins_a_creer[$data['field0']] = $this->get_data_issue_from_unimarc($notice);
                    $this->bulletins_a_creer[$data['field0']]['bull_notice'] = $this->notices_crees[$id_unimarc];
                } else {
                    // Si j'ai un bulletin avec un 461 qui ne rentre pas dans les autres cas, je passe le bulletin en monographie (cas très peu probable)
                    $query = "SELECT bulletin_id FROM bulletins WHERE num_notice = '$this->notice_id'";
                    $res = pmb_mysql_query($query);
                    if (!pmb_mysql_num_rows($res)) {
                        // Si il n'est pas déja relié, je le passe en monographie sinon je n'y touche pas
                        $query = "UPDATE notices SET niveau_biblio = 'm', niveau_hierar = '0' WHERE notice_id='$this->notice_id'";
                        $res = pmb_mysql_query($query);
                    }
                }
            }
        }
    }

    /**
     * Création de notice de périodique
     *
     * @param number $old_id
     * @param string $tit1
     * @param number $code
     * @return string|mixed
     */
    public function create_record_serial($old_id = 0, $tit1 = "", $code = 0)
    {
        if (!is_int($old_id)) {
            $old_id = intval($old_id);
        }

        if ($old_id != 0 && !empty(static::$record_tmp[$old_id])) {
            // La notice a deja ete creer
            return static::$record_tmp[$old_id];
        }

        // Le bulletin a déjà été créé ou choisi via l'interface
        if (!empty($this->perio_id)) {
            return $this->perio_id;
        }

        if (!empty($old_id) && isset($this->notices_crees[$old_id])) {
            $id_serial = $this->notices_crees[$old_id];
        } else {
            $id_serial = $this->serial_already_exist($tit1, $code);
            if (empty($id_serial) || $id_serial === false) {
                global $xmlta_doctype_serial;

                $new_perio = new serial();
                $id_serial = $new_perio->update([
                    'tit1' => addslashes(clean_string($tit1)),
                    'code' => addslashes($code),
                    'niveau_biblio' => 's',
                    'niveau_hierar' => '1',
                    'statut' => $this->statut,
                    'typdoc' => $xmlta_doctype_serial,
                ]);

                if (!empty($old_id)) {
                    static::$record_tmp[$old_id] = $id_serial;
                }
            }
            if (!empty($old_id)) {
                $this->notices_crees[$old_id] = $id_serial;
            }
        }

        //valorisation de la propriété de classe
        $this->perio_id = $id_serial;

        return $id_serial;
    }

    /**
     * Création d'un bulletin
     *
     * @param number $id_serial Id periodique
     * @param array $issue_data Donnee du bulletin
     * @param string $title_serial Titre du periodique (si pas d'identifiant)
     * @param number $code_serial Code du periodique (si pas d'identifiant)
     * @return number Id du bulletin
     */
    public function create_issue($id_serial = 0, $issue_data = [], $title_serial = "", $code_serial = 0)
    {
        global $msg;

        if (!is_int($id_serial)) {
            $id_serial = intval($id_serial);
        }

        if (empty($id_serial) || $id_serial == 0) {
            // Si je n'ai pas d'identifiant de périodique, je vais en chercher un ou le créer
            $id_serial = $this->create_record_serial(0, $title_serial, $code_serial);
        }

        //En provenance du formulaire d'intégration avec la création d'un nouveau bulletin
        if (empty($this->bull_id) && $this->type_import == 'form') {
        	$issue_data['titre'] = !empty($this->bull_titre) ? trim($this->bull_titre) : "";
        	$issue_data['date'] = !empty($this->bull_date) ? trim($this->bull_date) : "";
        	$issue_data['mention'] = !empty($this->bull_mention) ? trim($this->bull_mention) : "";
        	$issue_data['num'] = !empty($this->bull_num) ? trim($this->bull_num) : "";
        } elseif (empty($issue_data)) {
            // Donnee pour un bulletin temporaire
            $issue_data = [
                'titre' => $msg['tmp_title_bulletin'],
                'date' => '0000-00-00',
                'mention' => '00/00/0000',
                'num' => static::$issue_tmp_num,
            ];
            static::$issue_tmp_num++;
        } else {
        	$issue_data['titre'] = $issue_data['titre'] ?? "";
        	$issue_data['date'] = $issue_data['date'] ?? "";
        	$issue_data['mention'] = $issue_data['mention'] ?? "";
        	$issue_data['num'] = $issue_data['num'] ?? "";
        }

        $hash = md5($issue_data['num'].$issue_data['date'].$issue_data['mention']);
        if (!empty(static::$issue_tmp[$id_serial]) && !empty(static::$issue_tmp[$id_serial][$hash])) {
            // Bulletin deja cree
            return static::$issue_tmp[$id_serial][$hash];
        }

        // Le bulletin a déjà été créé ou choisi via l'interface
        if (!empty($this->bull_id)) {
            return $this->bull_id;
        }

        if (!empty($this->bulletins_crees[$id_serial]) && !empty($this->bulletins_crees[$id_serial][$issue_data['num']][$issue_data['date'].$issue_data['mention']])) {
            // Bulletin deja cree
            return $this->bulletins_crees[$id_serial][$issue_data['num']][$issue_data['date'].$issue_data['mention']];
        }

        // On regarde si le bulletin n'est pas déjà présent en base
        $date = !empty($issue_data['date']) ? $issue_data['date'] : "";
        $titre = !empty($issue_data['titre']) && $issue_data['titre'] != $msg['tmp_title_bulletin'] ? $issue_data['titre'] : "";
        $id_issue = $this->issue_already_exist($id_serial, $issue_data['num'], $issue_data['mention'], $titre, $date);
        if (!empty($id_issue) && $id_issue !== false) {
            return $id_issue;
        }

        $new_bull = new bulletinage(0, $id_serial);
        $new_bull->update([
            'bul_no' => addslashes($issue_data['num']),
            'bul_date' => addslashes($issue_data['mention']),
            'date_date' => addslashes($issue_data['date']),
            'bul_titre' => addslashes($issue_data['titre']),
            'bul_cb' => '',
        ]);
        $id_issue = $new_bull->bulletin_id;
        static::$issue_tmp[$id_serial][$hash] = $id_issue;
        $this->bulletins_crees[$id_serial][$issue_data['num']][$issue_data['date'].$issue_data['mention']] = $id_issue;

        //valorisation de la propriété de classe
        if (empty($this->bull_id)) {
            $this->bull_id = $id_issue;
        }

        return $id_issue;
    }

    /**
     * Création des liens de notice de bulletin
     *
     * @param number $old_id id unimarc
     * @param number $id_serial id périodique
     * @param number $id_issue id du bulletin
     * @param number $id_rec_issue id notice de bulletin
     * @param string $title_rec_issue titre de la notice de bulletin
     * @param array $issue Donnée du Bulletin
     * @return number|string
     */
    public function create_link_record_issue($old_id = 0, $id_serial = 0, $id_issue = 0, $id_rec_issue = 0, $title_rec_issue = "", $issue = [])
    {
        global $msg;

        if (empty($title_rec_issue)) {
            $title_rec_issue = $this->titles[0];
        }

        $data = [
            'titles' => $this->titles,
            'tit1' => $title_rec_issue,
            'statut' => $this->statut,
        ];
        $id_record_issue = import_records::insert_relation_bulletin_num_notice($id_issue, $id_rec_issue, $data);
        $this->notices_crees[$old_id] = $id_record_issue;

        //Lien entre la notice de bulletin et la notice de periodique
        if (!empty($id_serial)) {
            notice_relations::insert($id_record_issue, $id_serial, 'b', 1, 'up', false);
        }
        if (!empty($id_record_issue) && !empty($issue['date'])) {
            $query = "UPDATE notices SET year = '" . addslashes(substr($issue['date'], 0, 4)) . "', date_parution = '" . addslashes($issue['date']) . "' WHERE notice_id = '$id_record_issue'";
            pmb_mysql_query($query);
        }
        return $id_record_issue;
    }

    /**
     * Création des liens pour les articles
     *
     * @param array $issues infos du bulletin
     * @param array $serials infos du periodique
     * @param array $field contient l'id unimarc et le titre de l'article
     * @param iso2709_record $notice class iso2709_record de l'article
     */
    public function create_links_articles($issues, $serials, $field, $notice)
    {
        global $msg, $pmb_synchro_rdf;

        // On n'a pas d'info pour le bull et perio
        if (empty($issues) && empty($serials)) {
            if ($pmb_synchro_rdf) {
                $synchro_rdf = new synchro_rdf();
            }

            $converto_to_mono = false;
            if (empty($this->perio_id)) {
                $serie_title = $this->perio_titre;
                $serie_code = $this->perio_issn ?? 0;

                // On regarde si on a les informations de bulletinage dans le 461
                $subfield = $notice->get_subfield_array('461', 't');
                if (empty($serie_title) && !empty($subfield[0])) {
                    $serie_title = $subfield[0];
                }

                if (empty($serie_title) && empty($notice->get_subfield_array('461', 'v'))) {
                    // On a aucune infos sur le périodique, on vas transformer le l'article en monographie
                    $converto_to_mono = true;
                } else {
                    $this->perio_id = $this->create_record_serial(0, $serie_title, $serie_code);
                    if ($pmb_synchro_rdf) {
                        $synchro_rdf->addRdf($this->perio_id, 0);
                    }
                }
            }

            if ($converto_to_mono) {
                if ($this->notice_id) {
                    // L'article est cree mais on sait pas sur quoi le lie, on le passe en monographie
                    $query = "UPDATE notices SET niveau_biblio = 'm', niveau_hierar = '0' WHERE notice_id = '" . intval($this->notice_id) . "'";
                    pmb_mysql_query($query);
                    return;
                } else {
                    pmb_mysql_query("INSERT INTO error_log (error_origin, error_text) VALUES ('import_" . addslashes(SESSid) . ".inc', 'this->notice_id empty') ");
                    return;
                }
            }

            if (empty($this->bull_id)) {
                $bull_titre = $this->bull_titre;
                $subfield = $notice->get_subfield_array('461', 'v');
                if (empty($bull_titre) && !empty($subfield)) {
                    $bull_titre = 'Bulletin N°' . $subfield[0] ?? "";
                }
                $bull_num = $this->bull_num ?? "";
                if (empty($bull_num) && !empty($subfield)) {
                    $bull_num = $subfield[0] ?? "";
                }

                $bull_date = $this->bull_date ?? "";
                $mention = $this->bull_mention ?? "";
                if (empty($mention) && !empty($notice->get_subfield_array('210', 'd'))) {
                    $mention  = $notice->get_subfield_array('210', 'd')[0];
                }

                $this->bull_id = $this->create_issue($this->perio_id, [
                    'titre' => $bull_titre,
                    'date' => $bull_date,
                    'mention' => trim($mention),
                    'num' => $bull_num,
                ]);
                if ($pmb_synchro_rdf) {
                    $synchro_rdf->addRdf(0, $this->bull_id);
                }
            }

            // Création du lien Bull -> Article
            $this->create_link_article_to_issue($this->notice_id, $this->bull_id);

            $bulletin = new bulletinage($this->bull_id);
            $date_parution = $bulletin->date_date;
            if (!empty($date_parution) && $date_parution != '0000-00-00') {
                $query = "update notices set date_parution='" . addslashes($date_parution) . "' where notice_id='" . intval($this->notice_id) . "'";
                pmb_mysql_query($query);
            }
        } elseif (!empty($issues) && empty($serials)) {
            // On n'a pas d'info pour le perio mais on en a pour le bull

            // On créé un périodique sans titre (On regarde avant si on en a pas déja créé une)
            foreach ($issues as $relation) {
                $data = $this->get_data_from_fields_relation($relation);
                if (empty($data['titre'])) {
                    $data['titre'] = $data['num'];
                }
                // Creation d'un périodique sans titre
                $id_serial = $this->create_record_serial(0, 'Sans titre', '');

                $id_issue = $this->create_issue($id_serial, $data);

                $this->create_link_article_to_issue($this->notice_id, $id_issue);
            }
        } elseif (empty($issues) && !empty($serials)) {
            // On n'a pas d'info pour le bull mais on en a pour le perio

            // On créé un bulletin générique pour rattacher les articles au pério
            foreach ($serials as $relation) {
                $data = $this->get_data_from_fields_relation($relation);

                $id = $data['id'];
                $titles = $data['titles'];

                if (empty($this->notices_crees[$id]) && empty($data['field0'])) {
                    // Periodique n'est pas cree et ne sera pas a creer plutard
                    $id_serial = $this->create_record_serial($id, $titles[0], $data['code_x']);
                    $id_issue = $this->create_issue($id_serial);
                    $this->create_link_article_to_issue($this->notice_id, $id_issue);
                } elseif (!empty($this->notices_crees[$id])) {
                    // Periodique deja cree
                    $id_issue = $this->create_issue($this->notices_crees[$id]);
                    $this->create_link_article_to_issue($this->notice_id, $id_issue);
                } else {
                    // Les liens seront a creer plus tard pour cet article
                    $this->notices_a_creer[$id][] = [
                        'type_lnk' => $data['type_lnk'],
                        'lnk' => $data['lnk'],
                        'rank' => $data['rank'],
                        'titre_perio' => $titles[0],
                        'code' => $data['code_x'],
                        'id_asso' => $this->notice_id,
                        'num' => 0,
                        'mention' => '00/00/0000',
                        'date' => '0000-00-00',
                        'titre_bull' => $msg['tmp_title_bulletin'],
                    ];
                }
            }
        } else {
            // On a les infos pour le bull et le perio
            $nb_serials = (!empty($serials) && is_countable($serials)) ? count($serials) : 0;
            for ($i = 0; $i < $nb_serials; $i++) {
                $data_serial = $this->get_data_from_fields_relation($serials[$i]);
                $data_issue = $this->get_data_from_fields_relation($issues[$i]);

                $id = $data_serial['id'];

                if (empty($this->notices_crees[$id]) && empty($data_serial['field0'])) {
                    // On a les deux liens, on regarde si le perio existe déjà dans la base
                    $id_serial = $this->create_record_serial($id, $data_serial['titles'][0], $data_serial['code_x'] ?? 0);
                    $id_issue = $this->create_issue($id_serial, $data_issue);
                    $this->create_link_article_to_issue($this->notice_id, $id_issue);
                } elseif (!empty($this->notices_crees[$id])) {
                    // Si il est créé on récupère son identifiant
                    $id_issue = $this->create_issue($this->notices_crees[$id], $data_issue);
                    $this->create_link_article_to_issue($this->notice_id, $id_issue);
                } else {
                    $this->notices_a_creer[$id][] = [
                        'type_lnk' => $data_serial['type_lnk'],
                        'lnk' => $data_serial['lnk'],
                        'rank' => $data_serial['rank'],
                        'titre_perio' => $data_serial['titles'][0],
                        'code' => $data_serial['code_x'],
                        'id_asso' => $this->notice_id,
                        'num' => $data_issue['num'],
                        'mention' => $data_issue['mention'],
                        'date' => $data_issue['date'],
                        'titre_bull' => $data_issue['titre'],
                    ];
                }
            }
        }
    }

    /**
     * Création des notices d'article
     *
     * @param number $old_id
     * @param string $title_article
     * @param string $page
     * @param number $id_article
     * @param array $issue
     * @param string $title_serial
     * @param string $code_serial
     * @param number $id_serial
     * @return number|mixed
     */
    public function create_record_article($old_id = 0, $title_article = "", $page = "", $id_article = 0, $issue = [], $title_serial = "", $code_serial = "", $id_serial = 0)
    {
        global $msg;

        if (!is_int($old_id)) {
            // On evite les espaces pour la cle $notice_tmp
            $old_id = intval($old_id);
        }

        if ($old_id != 0 && !empty(static::$record_tmp[$old_id])) {
            // La notice a deja ete creer
            return static::$record_tmp[$old_id];
        }

        if (!empty($old_id) && isset($this->notices_crees[$old_id])) {
            $id_article = $this->notices_crees[$old_id];
        } else {
            // On va chercher le bulletin
            if (!empty(static::$issue_tmp[$old_id])) {
                $id_issue = static::$issue_tmp[$old_id];
            } else {
                $id_issue = $this->create_issue($id_serial, $issue, $title_serial, $code_serial);
                static::$issue_tmp[$old_id] = $id_issue;
            }

            $id_article = $this->article_already_exist($id_article, $id_issue, $title_article);
            if (empty($id_article) || $id_article === false) {
                $query = "INSERT INTO notices(tit1, npages, niveau_biblio, niveau_hierar, statut) VALUES('" . addslashes(clean_string($title_article)) . "','" . addslashes($page) . "', 'a', '2', '$this->statut')";
                pmb_mysql_query($query);
                $id_article = pmb_mysql_insert_id();
                audit::insert_creation(AUDIT_NOTICE, $id_article);
                // Calcul des droits d'accès s'ils sont activés
                notice::calc_access_rights($id_article);
                // Mise à jour de tous les index de la notice
                notice::majNoticesTotal($id_article);

                $this->create_link_article_to_issue($id_article, $id_issue);
                static::$record_tmp[$old_id] = $id_article;
            } else {
                if (!empty(static::$record_tmp[$old_id])) {
                    static::$record_tmp[$old_id] = $id_article;
                }
            }

            if (!empty($old_id)) {
                $this->notices_crees[$old_id] = $id_article;
            }
        }

        if (!empty($id_article) && !empty($issue['date'])) {
            $query = "UPDATE notices SET year = '" . addslashes(substr($issue['date'], 0, 4)) . "', date_parution = '" . addslashes($issue['date']) . "' WHERE notice_id = '$id_article'";
            pmb_mysql_query($query);
        }

        return $id_article;
    }

    protected function get_data_from_fields_relation($fields)
    {
    	$data = array();
        foreach ($fields as $field) {
            switch ($field['label']) {
                case '9':
                    $splitted_string = explode(':', $field['content']);
                    switch ($splitted_string[0]) {
                        case 'id':
                        case 'type_lnk':
                        case 'lnk':
                        case 'rank':
                        case 'bl':
                        case 'page':
                            $data[$splitted_string[0]] = $splitted_string[1];
                            break;
                    }
                    break;
                case 't':
                    if (empty($data['titles'])) {
                    	$data['titles'] = array();
                    }
                    $data['titles'][] = $field['content'];
                    //Assurons la rétro-compatibilité : on garde le dernier titre ici
                    $data['titre'] = $field['content'];
                    break;
                case 'd':
                    $data['date'] = $field['content'];
                    break;
                case 'e':
                    $data['mention'] = $field['content'];
                    break;
                case 'v':
                    $data['num'] = $field['content'];
                    break;
                case '0':
                    $data['field0'] = $field['content'];
                    break;
                case 'y':
                    $data['code_y'] = $field['content'];
                    break;
                case 'x':
                    $data['code_x'] = $field['content'];
                    break;
            }
        }
        return $data;
    }

    protected function set_data_from_fields_relation($fields, $label, $content='')
    {
        if (!empty($content) && !is_array($content)) {
            foreach ($fields as $key=>$field) {
                if (isset($field['label']) && $field['label'] == $label) {
                    $fields[$key]['content'] = $content;
                }
            }
        }
        return $fields;
    }

    /**
     * Création d'une relation
     *
     * @param array $records
     */
    public function create_linked_relation($fields)
    {
        $data = $this->get_data_from_fields_relation($fields);

        $id = $data['id'];
        $titles = $data['titles'];

        $id_record_linked = 0;
        if (!empty($this->notices_crees[$id])) {
            // Si la notice liée est créée
            $id_record_linked = $this->notices_crees[$id];
        } elseif (!empty($data['field0'])) {
            // Le lien sera a créer plus tard
            $this->notices_a_creer[$id][] = [
                'type_lnk' => $data['type_lnk'],
                'lnk' => $data['lnk'],
                'rank' => $data['rank'],
                'id_asso' => $this->id,
            ];
        } else {
            switch ($data['bl']) {
                case 'm0':
                    $id_record_linked = $this->create_record_monograph($data);
                    break;
                case 's1':
                    $id_record_linked = $this->create_record_serial($id, $titles[0], $data['code_x']);
                    break;
                case 'a2':
                    $issue = [
						'titre' => $titles[1],
						'date' => $data['date'],
						'mention' => $data['mention'],
						'num' => $data['num'],
                    ];
                    $id_record_linked = $this->create_record_article($id, $titles[0], '', '', $issue, $titles[2], $data['code_x'], '');
                    break;
                case 'b2':
                    $issue = [
						'titre' => $titles[1],
						'date' => $data['date'],
						'mention' => $data['mention'],
						'num' => $data['num'],
                    ];
                    $id_record_linked = $this->create_record_issue($id, $titles[0], $issue, $titles[2], $data['code_x']);
                    break;
            }
        }
        if (!empty($this->notice_id) && !empty($id_record_linked) && ($this->notice_id != $id_record_linked)) {
            $direction = '';
            switch ($data['lnk']) {
                case 'child':
                    $direction = 'down';
                    break;
                case 'parent':
                    $direction = 'up';
                    break;
                case 'pair':
                    $direction = 'both';
                    break;
            }
            if (! notice_relations::relation_exists($this->notice_id, $id_record_linked, $data['type_lnk'], $direction)) {
                notice_relations::insert_from_import($this->notice_id, $id_record_linked, $data['type_lnk'], $data['rank'], $direction);
            }
        }
    }

    /**
     * Création des notices de monographie
     *
     * @param number $old_id
     * @param string $title
     * @param string $code
     * @return string|number
     */
    public function create_record_monograph($data)
    {
        $old_id = intval($data['id']);
        $title = $data['titre'];
        $code = $data['code_y'];

        $id = notice::get_notice_id_from_cb($code);
        if ($id) {
            if (!empty($old_id)) {
                $this->notices_crees[$old_id] = $id;
            }
            return $id;
        }

        if (!empty(static::$record_tmp[$old_id])) {
            // La notice a deja ete creer
            return static::$record_tmp[$old_id];
        }

        $id = $this->record_already_exist($title, $this->statut, $code);
        if (empty($id) || $id === false) {
            $query = "INSERT INTO notices(tit1, code, niveau_biblio, niveau_hierar, statut) VALUES('" . addslashes($title) . "', '" . addslashes($code) . "', 'm', '0', '$this->statut')";
            pmb_mysql_query($query);
            $id = pmb_mysql_insert_id();

            audit::insert_creation(AUDIT_NOTICE, $id);

            // Mise à jour de tous les index de la notice
            notice::majNoticesTotal($id);

            if (!empty($old_id)) {
                static::$record_tmp[$old_id] = $id;
                $this->notices_crees[$old_id] = $id;
            }
        }

        return $id;
    }

    /**
     * Création des notices de bulletin
     *
     * @param number $old_id
     * @param string $title_rec_issue
     * @param array $issue
     * @param string $title_serial
     * @param string $code_serial
     * @return number|string|mixed
     */
    public function create_record_issue($old_id = 0, $title_rec_issue = "", $issue = [], $title_serial = "", $code_serial = "")
    {
        if (!empty($old_id) && isset($this->notices_crees[$old_id])) {
            $id_record_issue = $this->notices_crees[$old_id];
        } else {
            //On va chercher le bulletin
            $id_serial = $this->create_record_serial(0, $title_serial, $code_serial);
            $id_issue = $this->create_issue($id_serial, $issue, $title_serial, $code_serial);
            $id_record_issue = $this->create_link_record_issue($old_id, $id_serial, $id_issue, 0, $title_rec_issue, $issue);
        }

        return $id_record_issue;
    }

    /**
     * Creation du lien Bulletin -> Article
     * @param int $id_article Identifiant de l'article
     * @param int $id_issue Identifiant du bulletin
     * @return boolean
     */
    public function create_link_article_to_issue($id_article, $id_issue)
    {
        $id_article = intval($id_article);
        $id_issue = intval($id_issue);
        if (!empty($this->bull_id) && $id_issue != $this->bull_id) {
            // On supprime le lien qui a été créer
            import_records::delete_relation_analysis_bulletin($id_article, $this->bull_id);
        }
        return import_records::insert_relation_analysis_bulletin($id_article, $id_issue);
    }

    /**
     * Est un article
     * @return boolean
     */
    public function is_article()
    {
        return $this->bibliographic_level . $this->hierarchic_level == "a2";
    }

    /**
     * Est un bulletin
     * @return boolean
     */
    public function is_issue()
    {
        return $this->bibliographic_level . $this->hierarchic_level == "b2";
    }

    /**
     * Est un périodique
     * @return boolean
     */
    public function is_serial()
    {
        return $this->bibliographic_level . $this->hierarchic_level == "s1";
    }

    /**
     * Recherche d'un doublon de périodique
     * Si le périodique est déjà présent on retourne son identifiant
     * Sinon false
     *
     * @param string $tit1 Titre du périodique
     * @param number $code Code || ISBN du périodique
     * @param string $clause_ids Identifiant de notice à exclure
     * @return number|boolean
     */
    private function serial_already_exist($tit1, $code = 0, $clause_ids = "")
    {
        if (function_exists("z_import_serial_already_exist")) {
            return z_import_serial_already_exist($tit1, $code, $clause_ids);
        }

        $query = "SELECT notice_id FROM notices WHERE tit1 = '" . addslashes(clean_string($tit1)) . "' AND niveau_biblio = 's' AND niveau_hierar = '1'";
        //         if (!empty($code) && isISBN($isbn)) {
        //             $query .= " AND code = '" . addslashes($code) . "'";
        //         }
        if (!empty($clause_ids)) {
            $query .= " AND notice_id != '" . addslashes($clause_ids) . "'";
        }
        $res = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($res)) {
            return intval(pmb_mysql_result($res, 0, 0));
        }
        return false;
    }

    /**
     * Recherche d'un doublon de bulletin
     * Si le bulletin est déjà présent on retourne son identifiant
     * Sinon false
     *
     * @param number $id_serial
     * @param string|number $bulletin_numero
     * @param string|number $mention_date
     * @param string $titre
     * @param string $date
     * @return number|boolean
     */
    private function issue_already_exist($id_serial, $bulletin_numero, $mention_date, $titre = "", $date = "")
    {
        if (function_exists("z_import_issue_already_exist")) {
            return z_import_issue_already_exist($id_serial, $bulletin_numero, $mention_date, $titre, $date);
        }

        $query = "SELECT bulletin_id FROM bulletins WHERE
                    bulletin_notice = '" . intval($id_serial) . "' AND
                    bulletin_numero = '" . addslashes($bulletin_numero) . "' AND
                    mention_date = '" . addslashes($mention_date) . "'";
        //         if (!empty($titre)) {
        //             $query .= " AND bulletin_titre = '" . addslashes(clean_string($titre)) . "'";
        //         }
        if (!empty($date)) {
            $query .= " AND date_date = '" . addslashes($date) . "'";
        }
        $res = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($res)) {
            return intval(pmb_mysql_result($res, 0, 0));
        }
        return false;
    }

    /**
     * Recherche d'un doublon de notice
     * Si la notice est déjà présent on retourne son identifiant
     * Sinon false
     *
     * @param string $titre
     * @param string|number $statut
     * @param string|number $code
     * @return number|boolean
     */
    private function record_already_exist($titre, $statut, $code = "")
    {
        if (function_exists("z_import_record_already_exist")) {
            return z_import_record_already_exist($titre, $statut, $code);
        }

        $query = "SELECT notice_id FROM notices WHERE
                    tit1 = '" . addslashes(clean_string($titre)) . "' AND
                    statut = '" . addslashes($statut) . "'";
        if (!empty($code) && isISBN($code)) {
            $query .= " AND code = '" . addslashes($code) . "'";
        }
        $res = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($res)) {
            return intval(pmb_mysql_result($res, 0, 0));
        }
        return false;
    }

    /**
     * Recherche d'un doublon d'article
     * Si l'article est déjà présent on retourne son identifiant
     * Sinon false
     *
     * @param string|number $clause_ids
     * @param string|number $id_issue
     * @param string $title_article
     * @return number|boolean
     */
    private function article_already_exist($clause_ids, $id_issue, $title_article)
    {
        if (function_exists("z_import_article_already_exist")) {
            return z_import_article_already_exist($clause_ids, $id_issue, $title_article);
        }

        $query = "SELECT notice_id FROM notices JOIN analysis ON analysis_notice=notice_id WHERE
                    notice_id != '" . addslashes($clause_ids) . "' AND
                    analysis_bulletin='" . addslashes($id_issue) . "' AND
                    tit1 = '" . addslashes(clean_string($title_article)) . "'";
        $res = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($res)) {
            return intval(pmb_mysql_result($res, 0, 0));
        }
        return false;
    }

    /**
     * Traitement des langues
     *
     * @param int $notice_retour
     */
    protected function insert_languages($notice_retour)
    {
        $notice_retour = intval($notice_retour);

        // langues de la publication
        $rqt_del = "delete from notices_langues where num_notice='$notice_retour' ";
        pmb_mysql_query($rqt_del);

        if (is_array($this->language_code) && count($this->language_code)) {
            $rqt_ins = "insert into notices_langues (num_notice, type_langue, code_langue, ordre_langue) VALUES ";
            foreach ($this->language_code as $ordre_lang=>$code_lang) {
                if ($code_lang) {
                    $rqt = $rqt_ins . " ('" . $notice_retour . "', 0, '" . addslashes($code_lang) . "', ". intval($ordre_lang) .") " ;
                    pmb_mysql_query($rqt);
                }
            }
        }

        // langues originales
        if (is_array($this->original_language_code) && count($this->original_language_code)) {
            $rqt_ins = "insert into notices_langues (num_notice, type_langue, code_langue, ordre_langue) VALUES ";
            foreach ($this->original_language_code as $ordre_lang=>$code_lang) {
                if ($code_lang) {
                    $rqt = $rqt_ins . " ('" . $notice_retour . "', 1, '" . addslashes($code_lang) . "', ". intval($ordre_lang) .") " ;
                    pmb_mysql_query($rqt);
                }
            }
        }
    }

    /**
     * Traitement des responsabilites
     *
     * @param int $notice_retour
     */
    protected function insert_responsabilities($notice_retour)
    {
        $notice_retour = intval($notice_retour);

        if ($notice_retour) {
            $rqt_del = "delete from responsability where responsability_notice='$notice_retour'" ;
            pmb_mysql_query($rqt_del) or die("Couldn't purge table responsability : ".$rqt_del);
        }

        $rqt_ins = "insert into responsability (responsability_author, responsability_notice, responsability_fonction, responsability_type, responsability_ordre ) VALUES ";
        $nb_auts = (!empty($this->aut_array) && is_countable($this->aut_array)) ? count($this->aut_array) : 0;

        for ($i = 0; $i < $nb_auts; $i++) {
        	$aut = array();
            $aut['id']=(isset($this->aut_array[$i]['id']) ? clean_string($this->aut_array[$i]['id']) : 0);
            $aut['name']=(isset($this->aut_array[$i]['entree']) ? clean_string($this->aut_array[$i]['entree']) : '');
            $aut['rejete']=(isset($this->aut_array[$i]['rejete']) ? clean_string($this->aut_array[$i]['rejete']) : '');
            $aut['date']=(isset($this->aut_array[$i]['date']) ? clean_string($this->aut_array[$i]['date']) : '');
			$aut['type']=(isset($this->aut_array[$i]['type_auteur']) ? $this->aut_array[$i]['type_auteur'] : '');
            $aut['subdivision']=(isset($this->aut_array[$i]['subdivision']) ? clean_string($this->aut_array[$i]['subdivision']) : '');
            $aut['numero']=(isset($this->aut_array[$i]['numero']) ? clean_string($this->aut_array[$i]['numero']) : '');
            $aut['lieu']=(isset($this->aut_array[$i]['lieu']) ? clean_string($this->aut_array[$i]['lieu']) : '');
            $aut['ville']=(isset($this->aut_array[$i]['ville']) ? clean_string($this->aut_array[$i]['ville']) : '');
            $aut['pays']=(isset($this->aut_array[$i]['pays']) ? clean_string($this->aut_array[$i]['pays']) : '');
            $aut['web']=(isset($this->aut_array[$i]['web']) ? clean_string($this->aut_array[$i]['web']) : '');
            $aut['author_comment']=(isset($this->aut_array[$i]['author_comment']) ? clean_string($this->aut_array[$i]['author_comment']) : '');
            $aut['authority_number']=(isset($this->aut_array[$i]['authority_number']) ? clean_string($this->aut_array[$i]['authority_number']) : '');

            /* Origine de l'autorité : on reprend les infos d'origine de la notice pour les attribuées aux origines des autorités */
            $id_origine_auth = 0;
            $id_origine_auth = origin_authorities::import($this->origine_notice);

            if ($id_origine_auth == 0) {
                $id_origine_auth = 1;
            }

            // import de l'autorité auteur si elle existe et conservation des infos sur l'origine de l'autorité
            if ($aut['authority_number'] != '' && $id_origine_auth) {
                $this->aut_array[$i]["id"] = $this->insert_authority_infos($aut['authority_number'], "author", $id_origine_auth, $aut);
            }

            if (empty($this->aut_array[$i]["id"])) {
                $this->aut_array[$i]["id"] = auteur::import($aut);
            }

            if ($this->aut_array[$i]["id"]) {
                $rqt = $rqt_ins . " (" . intval($this->aut_array[$i]["id"]) . "," . $notice_retour . ", '" . $this->aut_array[$i]['fonction'] . "'," . $this->aut_array[$i]['responsabilite'] . "," . $i . ") " ;
                pmb_mysql_query($rqt);
            }
        }
    }

    /**
     * enregistrement de la notices dans les catégories
     * @param integer $notice_retour
     * @param array $categories
     * @param number $thesaurus_traite
     */
    public static function traite_categories_enreg($notice_retour, $categories, $thesaurus_traite=0)
    {
        // si $thesaurus_traite fourni, on ne delete que les catégories de ce thesaurus, sinon on efface toutes
        //  les indexations de la notice sans distinction de thesaurus
        if (empty($thesaurus_traite)) {
            $rqt_del = "DELETE FROM notices_categories WHERE notcateg_notice='$notice_retour' ";
        } else {
            $rqt_del = "DELETE FROM notices_categories WHERE notcateg_notice='$notice_retour' AND num_noeud IN (SELECT id_noeud FROM noeuds WHERE num_thesaurus='$thesaurus_traite' and id_noeud=notices_categories.num_noeud) ";
        }
        pmb_mysql_query($rqt_del);
        $rqt_ins = "INSERT INTO notices_categories (notcateg_notice, num_noeud, ordre_categorie) VALUES ";
        $nb_categories =  (!empty($categories) && is_countable($categories)) ? count($categories) : 0;
        for ($i = 0; $i < $nb_categories; $i++) {
            $id_categ = $categories[$i]['categ_id'];
            if (!empty($id_categ)) {
                $rqt = $rqt_ins . " ('$notice_retour','$id_categ', $i) ";
                pmb_mysql_query($rqt);
            }
        }
    }

    public static function traite_categories_for_form($unimarc_code, $tableau = [])
    {
        global $msg, $rameau;
        $rameau = "";

        $champ_rameau = "";
        switch ($unimarc_code) {
            case '606':
                $info_606_a = ($tableau["info_606_a"] ?? '');
                $info_606_j = ($tableau["info_606_j"] ?? '');
                $info_606_x = ($tableau["info_606_x"] ?? '');
                $info_606_y = ($tableau["info_606_y"] ?? '');
                $info_606_z = ($tableau["info_606_z"] ?? '');

                $nb_infos_606_a = (is_array($info_606_a) ? count($info_606_a) : 0);
                for ($a = 0; $a < $nb_infos_606_a; $a++) {
                    $libelle_final = "";
                    $libelle_j = "";

                    $nb_infos_606_j = (!empty($info_606_j[$a]) && is_countable($info_606_j[$a])) ? count($info_606_j[$a]) : 0;
                    for ($j = 0; $j < $nb_infos_606_j; $j++) {
                        if (empty($libelle_j)) {
                            $libelle_j .= trim($info_606_j[$a][$j]);
                        } else {
                            $libelle_j .= " ** ".trim($info_606_j[$a][$j]);
                        }
                    }

                    if (empty($libelle_j)) {
                        $libelle_final = trim($info_606_a[$a][0]);
                    } else {
                        $libelle_final = trim($info_606_a[$a][0])." ** $libelle_j";
                    }
                    if (empty($libelle_final)) {
                        break;
                    }

                    $nb_infos_606_x = (!empty($info_606_x[$a]) && is_countable($info_606_x[$a])) ? count($info_606_x[$a]) : 0;
                    for ($j = 0; $j < $nb_infos_606_x; $j++) {
                        $libelle_final .= " : ".trim($info_606_x[$a][$j]);
                    }

                    $nb_infos_606_y = (!empty($info_606_y[$a]) && is_countable($info_606_y[$a])) ? count($info_606_y[$a]) : 0;
                    for ($j = 0; $j < $nb_infos_606_y; $j++) {
                        $libelle_final .= " : ".trim($info_606_y[$a][$j]);
                    }

                    $nb_infos_606_z = (!empty($info_606_z[$a]) && is_countable($info_606_z[$a])) ? count($info_606_z[$a]) : 0;
                    for ($j = 0; $j < $nb_infos_606_z; $j++) {
                        $libelle_final .= " : ".trim($info_606_z[$a][$j]);
                    }

                    if (!empty($champ_rameau)) {
                        $champ_rameau .= " @@@ ";
                    }
                    $champ_rameau .= $libelle_final;
                }
                break;
        }
        return $champ_rameau;
    }

    public static function traite_categories_from_form()
    {
        global $max_categ ;
		$categories = array();
        for ($i=0; $i< $max_categ ; $i++) {
            $var_categ = "f_categ_id$i" ;
            global ${$var_categ} ;
            if (${$var_categ}) {
                $categories[] = array('categ_id' => ${$var_categ});
            }
        }
        return $categories ;
    }

    public static function create_categ_z3950($num_parent, $libelle, $index)
    {
        global $thes;
        $n = new noeuds();
        $n->num_thesaurus = $thes->id_thesaurus;
        $n->num_parent = $num_parent;
        $n->save();

        $c = new categories($n->id_noeud, 'fr_FR');
        $c->libelle_categorie = $libelle;
        $c->index_categorie = $index;
        $c->save();

        return $n->id_noeud;
    }
    
    public static function traite_concepts_for_form($unimarc_code, $tableau = []) {
        global $msg, $rameau;
        
        $rameau = "" ;
        $champ_rameau="";
        switch ($unimarc_code) {
            case '606':
                $info_606_a = (isset($tableau["info_606_a"]) ? $tableau["info_606_a"] : '');
                $info_606_j = (isset($tableau["info_606_j"]) ? $tableau["info_606_j"] : '');
                $info_606_x = (isset($tableau["info_606_x"]) ? $tableau["info_606_x"] : '');
                $info_606_y = (isset($tableau["info_606_y"]) ? $tableau["info_606_y"] : '');
                $info_606_z = (isset($tableau["info_606_z"]) ? $tableau["info_606_z"] : '');
                
                
                for ($a=0; $a<sizeof($info_606_a); $a++) {
                    $libelle_final="";
                    $libelle_j="";
                    for ($j=0; $j<sizeof($info_606_j[$a]); $j++) {
                        if (!$libelle_j) $libelle_j .= trim($info_606_j[$a][$j]) ;
                        else $libelle_j .= " -- ".trim($info_606_j[$a][$j]) ;
                    }
                    if (!$libelle_j) $libelle_final = trim($info_606_a[$a][0]) ; else $libelle_final = trim($info_606_a[$a][0])." -- ".$libelle_j ;
                    if (!$libelle_final) break ;
                    for ($j=0; $j<sizeof($info_606_x[$a]); $j++) {
                        $libelle_final .= " -- ".trim($info_606_x[$a][$j]) ;
                    }
                    for ($j=0; $j<sizeof($info_606_y[$a]); $j++) {
                        $libelle_final .= " -- ".trim($info_606_y[$a][$j]) ;
                    }
                    for ($j=0; $j<sizeof($info_606_z[$a]); $j++) {
                        $libelle_final .= " -- ".trim($info_606_z[$a][$j]) ;
                    }
                    if ($champ_rameau) $champ_rameau.=" @@@ ";
                    $champ_rameau.=$libelle_final;
                }
                break;
        }
        return $champ_rameau;
    }
    
    public static function mettre_notice_panier($id_notice,$libelle_panier,$type="NOTI")
    {
        $inserted = import_records::insert_caddie($id_notice, $libelle_panier, $type);
        if ($inserted['error'] && function_exists('affiche_mes_erreurs')) {
            affiche_mes_erreurs($inserted['error_message']);
        }
    }
}
