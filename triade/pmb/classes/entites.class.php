<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entites.class.php,v 1.72.4.1 2025/03/04 10:45:25 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once ($class_path.'/budgets.class.php');
require_once ($class_path.'/coordonnees.class.php');
require_once ($class_path.'/exercices.class.php');
require_once ($include_path.'/misc.inc.php');
require_once ($include_path.'/isbn.inc.php');
global $pmb_indexation_lang;
if($pmb_indexation_lang) {
	require_once ($include_path.'/marc_tables/'.$pmb_indexation_lang.'/empty_words');
}

if(!defined('TYP_ENT_FOU')) define('TYP_ENT_FOU', 0); //Type entité 0=fournisseur
if(!defined('TYP_ENT_ETAB')) define('TYP_ENT_ETAB', 1); //Type entité 1=bibliothèque

class entites{


	public $id_entite = 0;				//Identifiant de l'entité
	public $type_entite = TYP_ENT_FOU;	//Type entité (0=fournisseur, 1=bibliothèque)
	public $num_bibli = 0;				//Identifiant de la bibliothèque si Fournisseur, 0 sinon.
	public $raison_sociale = '';
	public $commentaires = '';
	public $siret = '';				//Numéro de Siret
	public $naf = '';					//Code naf
	public $rcs = '';					//Code registre du commerce
	public $tva = '';					//Numéro de TVA intracommunautaire
	public $num_cp_client = '';		//Numéro de compte chez le fournisseur
	public $num_cp_compta = 0;			//Numéro de compte comptable (4)
	public $site_web = '';				//Url du site web de l'entité
	public $logo = '';					//Url du logo de l'entité
	public $autorisations = '';		//Autorisations d'accès à l'entité
	public $num_frais = 0;				//Identifiant des frais
	public $num_paiement = 0;			//Identifiant du mode de paiement
	public $index_entite = '';			//Champ de recherche fulltext

	protected static $etablissements = NULL;
	protected static $fournisseurs = NULL;

	//Constructeur.
	public function __construct($id_entite= 0) {
		$this->id_entite = intval($id_entite);
		if ($this->id_entite) {
			$this->load();
		}
	}

	// charge une entité à partir de la base.
	public function load(){
		$q = "select * from entites where id_entite = '".$this->id_entite."' ";
		$r = pmb_mysql_query($q);
		if(pmb_mysql_num_rows($r)){
			$obj = pmb_mysql_fetch_object($r);
			pmb_mysql_free_result($r);

			$this->type_entite = $obj->type_entite;
			$this->num_bibli = $obj->num_bibli;
			$this->raison_sociale = $obj->raison_sociale;
			$this->commentaires = $obj->commentaires;
			$this->siret = $obj->siret;
			$this->naf = $obj->naf;
			$this->rcs = $obj->rcs;
			$this->tva = $obj->tva;
			$this->num_cp_client = $obj->num_cp_client;
			$this->num_cp_compta = $obj->num_cp_compta;
			$this->site_web = $obj->site_web;
			$this->logo = $obj->logo;
			$this->autorisations = $obj->autorisations;
			$this->num_frais = $obj->num_frais;
			$this->num_paiement = $obj->num_paiement;
		}
	}

	public function get_form() {
	    global $msg;
	    global $charset;
	    global $coord_content_form;
	    global $ptab, $script;
	    global $PMBuserid;

	    $content_form = $coord_content_form;
	    $content_form = str_replace('!!id!!', $this->id_entite, $content_form);

	    $interface_form = new interface_admin_form('coordform');
	    if(!$this->id_entite){
	        $interface_form->set_label($msg['acquisition_ajout_biblio']);
	    }else{
	        $interface_form->set_label($msg['acquisition_modif_biblio']);
	    }

	    $ptab[1] = $ptab[1].$ptab[10].$ptab[11];
	    $ptab[1] = str_replace('!!adresse!!', htmlentities($msg['acquisition_adr_fac'],ENT_QUOTES, $charset), $ptab[1]);
	    $ptab[1] = str_replace('!!button_adr_fac!!', $ptab[12], $ptab[1]);

	    $content_form = str_replace('!!raison!!', htmlentities($this->raison_sociale,ENT_QUOTES, $charset), $content_form);
	    $content_form = str_replace('!!commentaires!!', htmlentities($this->commentaires,ENT_QUOTES, $charset), $content_form);
	    $content_form = str_replace('!!siret!!', htmlentities($this->siret,ENT_QUOTES, $charset), $content_form);
	    $content_form = str_replace('!!rcs!!', htmlentities($this->rcs,ENT_QUOTES, $charset), $content_form);
	    $content_form = str_replace('!!naf!!', htmlentities($this->naf,ENT_QUOTES, $charset), $content_form);
	    $content_form = str_replace('!!tva!!', htmlentities($this->tva,ENT_QUOTES, $charset), $content_form);
	    $content_form = str_replace('!!site_web!!', htmlentities($this->site_web,ENT_QUOTES, $charset), $content_form);
	    $content_form = str_replace('!!logo!!', htmlentities($this->logo,ENT_QUOTES, $charset), $content_form);
	    if(!$this->id_entite) {
	        $content_form = str_replace('!!contact!!', $ptab[1], $content_form);
	        $content_form = str_replace('!!max_coord!!', '2', $content_form);

	        $content_form = str_replace('!!id1!!', '0', $content_form);
	        $content_form = str_replace('!!lib_1!!', '', $content_form);
	        $content_form = str_replace('!!cta_1!!', '', $content_form);
	        $content_form = str_replace('!!ad1_1!!', '', $content_form);
	        $content_form = str_replace('!!ad2_1!!', '', $content_form);
	        $content_form = str_replace('!!cpo_1!!', '', $content_form);
	        $content_form = str_replace('!!vil_1!!', '', $content_form);
	        $content_form = str_replace('!!eta_1!!', '', $content_form);
	        $content_form = str_replace('!!pay_1!!', '', $content_form);
	        $content_form = str_replace('!!te1_1!!', '', $content_form);
	        $content_form = str_replace('!!te2_1!!', '', $content_form);
	        $content_form = str_replace('!!fax_1!!', '', $content_form);
	        $content_form = str_replace('!!ema_1!!', '', $content_form);
	        $content_form = str_replace('!!com_1!!', '', $content_form);
	        $content_form = str_replace('!!id2!!', '0', $content_form);
	        $content_form = str_replace('!!lib_2!!', '', $content_form);
	        $content_form = str_replace('!!cta_2!!', '', $content_form);
	        $content_form = str_replace('!!ad1_2!!', '', $content_form);
	        $content_form = str_replace('!!ad2_2!!', '', $content_form);
	        $content_form = str_replace('!!cpo_2!!', '', $content_form);
	        $content_form = str_replace('!!vil_2!!', '', $content_form);
	        $content_form = str_replace('!!eta_2!!', '', $content_form);
	        $content_form = str_replace('!!pay_2!!', '', $content_form);
	        $content_form = str_replace('!!te1_2!!', '', $content_form);
	        $content_form = str_replace('!!te2_2!!', '', $content_form);
	        $content_form = str_replace('!!fax_2!!', '', $content_form);
	        $content_form = str_replace('!!ema_2!!', '', $content_form);
	        $content_form = str_replace('!!com_2!!', '', $content_form);

	        $content_form = $this->autorisations($PMBuserid, $content_form);
	    } else {
	        $content_form = str_replace('!!contact!!', $ptab[1], $content_form);

	        $row = pmb_mysql_fetch_object(entites::get_coordonnees($this->id_entite,'1'));
	        $content_form = str_replace('!!id1!!', $row->id_contact, $content_form);
	        $content_form = str_replace('!!lib_1!!', htmlentities($row->libelle,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!cta_1!!', htmlentities($row->contact,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!ad1_1!!', htmlentities($row->adr1,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!ad2_1!!', htmlentities($row->adr2,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!cpo_1!!', htmlentities($row->cp,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!vil_1!!', htmlentities($row->ville,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!eta_1!!', htmlentities($row->etat,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!pay_1!!', htmlentities($row->pays,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!te1_1!!', htmlentities($row->tel1,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!te2_1!!', htmlentities($row->tel2,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!fax_1!!', htmlentities($row->fax,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!ema_1!!', htmlentities($row->email,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!com_1!!', htmlentities($row->commentaires,ENT_QUOTES,$charset), $content_form);

	        $row = pmb_mysql_fetch_object(entites::get_coordonnees($this->id_entite,'2'));
	        $content_form = str_replace('!!id2!!', $row->id_contact, $content_form);
	        $content_form = str_replace('!!lib_2!!', htmlentities($row->libelle,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!cta_2!!', htmlentities($row->contact,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!ad1_2!!', htmlentities($row->adr1,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!ad2_2!!', htmlentities($row->adr2,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!cpo_2!!', htmlentities($row->cp,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!vil_2!!', htmlentities($row->ville,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!eta_2!!', htmlentities($row->etat,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!pay_2!!', htmlentities($row->pays,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!te1_2!!', htmlentities($row->tel1,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!te2_2!!', htmlentities($row->tel2,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!fax_2!!', htmlentities($row->fax,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!ema_2!!', htmlentities($row->email,ENT_QUOTES,$charset), $content_form);
	        $content_form = str_replace('!!com_2!!', htmlentities($row->commentaires,ENT_QUOTES,$charset), $content_form);

	        $liste_coord = entites::get_coordonnees($this->id_entite,'0');
	        $content_form = str_replace('!!max_coord!!', (pmb_mysql_num_rows($liste_coord)+2), $content_form);
	        $i=3;
	        while ($row = pmb_mysql_fetch_object($liste_coord)) {
	            $content_form = str_replace('<!--coord_repetables-->', $ptab[2].'<!--coord_repetables-->', $content_form);
	            $content_form = str_replace('!!no_X!!', $i, $content_form);
	            $i++;
	            $content_form = str_replace('!!idX!!', $row->id_contact, $content_form);
	            $content_form = str_replace('!!lib_X!!', htmlentities($row->libelle,ENT_QUOTES,$charset), $content_form);
	            $content_form = str_replace('!!cta_X!!', htmlentities($row->contact,ENT_QUOTES,$charset), $content_form);
	            $content_form = str_replace('!!ad1_X!!', htmlentities($row->adr1,ENT_QUOTES,$charset), $content_form);
	            $content_form = str_replace('!!ad2_X!!', htmlentities($row->adr2,ENT_QUOTES,$charset), $content_form);
	            $content_form = str_replace('!!cpo_X!!', htmlentities($row->cp,ENT_QUOTES,$charset), $content_form);
	            $content_form = str_replace('!!vil_X!!', htmlentities($row->ville,ENT_QUOTES,$charset), $content_form);
	            $content_form = str_replace('!!eta_X!!', htmlentities($row->etat,ENT_QUOTES,$charset), $content_form);
	            $content_form = str_replace('!!pay_X!!', htmlentities($row->pays,ENT_QUOTES,$charset), $content_form);
	            $content_form = str_replace('!!te1_X!!', htmlentities($row->tel1,ENT_QUOTES,$charset), $content_form);
	            $content_form = str_replace('!!te2_X!!', htmlentities($row->tel2,ENT_QUOTES,$charset), $content_form);
	            $content_form = str_replace('!!fax_X!!', htmlentities($row->fax,ENT_QUOTES,$charset), $content_form);
	            $content_form = str_replace('!!ema_X!!', htmlentities($row->email,ENT_QUOTES,$charset), $content_form);
	            $content_form = str_replace('!!com_X!!', htmlentities($row->commentaires,ENT_QUOTES,$charset), $content_form);

	        }
	        $content_form = $this->autorisations($this->autorisations, $content_form);
	    }
	    $interface_form->set_object_id($this->id_entite)
	    ->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->raison_sociale." ?")
	    ->set_content_form($content_form)
	    ->set_table_name('coordonnees')
	    ->set_field_focus('raison');
	    $form = $script;
	    $form .= $interface_form->get_display();
	    return $form;
	}

	protected function autorisations($autorisations='', $content_form='') {
	    global $charset;
	    global $ptab;

	    $id_check_list = '';
	    $aut = explode(' ',$autorisations);

	    //Récupération de la liste des utilisateurs
	    $q = "SELECT userid, username FROM users order by username ";
	    $r = pmb_mysql_query($q);

	    while ($row = pmb_mysql_fetch_object($r)) {

	        $content_form = str_replace('<!-- autorisations -->', $ptab[4].'<!-- autorisations -->', $content_form);

	        $content_form = str_replace('!!user_name!!', htmlentities($row->username,ENT_QUOTES, $charset), $content_form);
	        $content_form = str_replace('!!user_id!!', $row->userid, $content_form);
	        if ($row->userid == 1 || in_array($row->userid, $aut)) {
	            $chk = 'checked=\'checked\'';
	            if ($row->userid == 1) $chk .= ' readonly onchange=\'this.checked = true;\'';
	        } else {
	            $chk = '';
	        }
	        $content_form = str_replace('!!checked!!', $chk, $content_form);

	        if($id_check_list)$id_check_list.='|';
	        $id_check_list.="user_aut[".$row->userid."]";
	    }
	    $content_form = str_replace('!!auto_id_list!!', $id_check_list, $content_form);
	    return $content_form;
	}

	public function set_properties_from_form() {
		global $raison, $comment, $siret, $naf, $rcs, $tva, $site_web, $logo, $user_aut;

		$this->raison_sociale = $raison;
		$this->commentaires = $comment;
		$this->siret = $siret;
		$this->naf = $naf;
		$this->rcs = $rcs;
		$this->tva = $tva;
		$this->site_web = $site_web;
		$this->logo = $logo;

		if (is_array($user_aut)) {
			$this->autorisations = ' '.implode(' ',$user_aut).' ';
		} else $this->autorisations = ' 1 ';
	}

	// enregistre une entité en base.
	public function save(){
		if( $this->raison_sociale == '' ) die ("Erreur de création entités");

		//Nettoyage des valeurs en entrée
		$this->raison_sociale = clean_string($this->raison_sociale);
		$this->siret = clean_string($this->siret);
		$this->naf = clean_string($this->naf);
		$this->rcs = clean_string($this->rcs);
		$this->tva = clean_string($this->tva);
		$this->num_cp_client = clean_string($this->num_cp_client);
		$this->num_cp_compta = clean_string($this->num_cp_compta);
		$this->site_web = clean_string($this->site_web);
		$this->logo = clean_string($this->logo);

		if($this->id_entite) {
			$q = "update entites set type_entite = '".$this->type_entite."', num_bibli = '".$this->num_bibli."', raison_sociale = '".$this->raison_sociale."', commentaires = '".$this->commentaires."', ";
			$q.= "siret = '".$this->siret."', naf = '".$this->naf."', rcs = '".$this->rcs."', tva = '".$this->tva."', num_cp_client = '".$this->num_cp_client."', ";
			$q.= "num_cp_compta = '".$this->num_cp_compta."', site_web = '".$this->site_web."', logo = '".$this->logo."', autorisations = '".$this->autorisations."', ";
			$q.= "num_frais = '".$this->num_frais."', num_paiement = '".$this->num_paiement."', ";
			$q.= "index_entite = ' ".strip_empty_words($this->raison_sociale)." '";
			$q.= "where id_entite = '".$this->id_entite."' ";
			pmb_mysql_query($q);
		} else {
			$q = "insert into entites set type_entite = '".$this->type_entite."', num_bibli = '".$this->num_bibli."', raison_sociale = '".$this->raison_sociale."', commentaires = '".$this->commentaires."', ";
			$q.= "siret = '".$this->siret."', naf = '".$this->naf."', rcs = '".$this->rcs."', tva = '".$this->tva."', num_cp_client = '".$this->num_cp_client."', ";
			$q.= "num_cp_compta = '".$this->num_cp_compta."', site_web = '".$this->site_web."', logo = '".$this->logo."' , autorisations = '".$this->autorisations."', ";
			$q.= "num_frais = '".$this->num_frais."', num_paiement = '".$this->num_paiement."', ";
			$q.= "index_entite = ' ".strip_empty_words($this->raison_sociale)." '";
			pmb_mysql_query($q);
			$this->id_entite = pmb_mysql_insert_id();
		}

	}

    /**
     * Supprime une entite de la base
     *
     * @param int $id_entite
     */
    public function delete(int $id_entite = 0)
    {
        if (! $id_entite) {
            $id_entite = intval($this->id_entite);
        }
        static::staticDelete($id_entite);
    }

    public static function staticDelete(int $id_entite = 0)
    {
        if (! $id_entite) {
            return;
        }
        $q = "delete from entites where id_entite = '" . $id_entite . "' ";
        pmb_mysql_query($q);

        $q = "delete from coordonnees where num_entite = '" . $id_entite . "' ";
        pmb_mysql_query($q);

        $q = "delete from offres_remises where num_fournisseur = '" . $id_entite . "' ";
        pmb_mysql_query($q);

        $q = "update abts_abts set fournisseur='0' where fournisseur = '" . $id_entite . "' ";
        pmb_mysql_query($q);
    }

	//vérifie l'existence d'une entité en base à partir de son identifiant
	public static function exists($id_entite= 0) {
		$q = "SELECT count(1) from entites where id_entite = '".$id_entite."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//vérifie l'existence d'un fournisseur en base a partir de son identifiant
	public static function is_a_fournisseur_id($id_entite= 0) {

	    $id_entite = intval($id_entite);
	    if (!$id_entite) {
	        return 0;
	    }
	    $q = "SELECT count(1) from entites where id_entite = '".$id_entite."' and type_entite=".TYP_ENT_FOU;
	    $r = pmb_mysql_query($q);
	    return pmb_mysql_result($r, 0, 0);

	}


	//vérifie l'existence d'un etablissement en base a partir de son identifiant
	public static function is_a_etablissement_id($id_entite= 0) {

	    $id_entite = intval($id_entite);
	    if (!$id_entite) {
	        return 0;
	    }
	    $q = "SELECT count(1) from entites where id_entite = '".$id_entite."' and type_entite=".TYP_ENT_ETAB;
	    $r = pmb_mysql_query($q);
	    return pmb_mysql_result($r, 0, 0);

	}


	//vérifie l'existence d'un fournisseur en base a partir de sa raison sociale et de l'etablissement de rattachement
	public static function is_a_fournisseur_raison_sociale($raison_sociale = '', $id_etablissement = 0 , $id_fournisseur = 0) {

	    $id_etablissement = intval($id_etablissement);
	    $id_fournisseur = intval($id_fournisseur);

	    $q = "SELECT count(1) from entites where raison_sociale = '".addslashes($raison_sociale)."' and num_bibli = '".$id_etablissement."' and type_entite=".TYP_ENT_FOU;
	    if($id_fournisseur){
	        $q.=" and id_entite != '".$id_fournisseur."'";
	    }
	    $r = pmb_mysql_query($q);
	    return pmb_mysql_result($r, 0, 0);

	}


	//vérifie l'existence d'une entité en base à partir de sa raison sociale
	public static function exists_rs($raison_sociale= 0, $numero_bibli=0, $id_entite = 0) {
		//Contrainte à appliquer :
		/*
		 * type= 1 -> etablissement
		 * type = 0 -> fournisseur
		 * Pas de fournisseur avec la même raison sociale que l'établissement
		 * Pas deux fournisseurs avec la même raison sociale dans un établissement
		 */
		$q = "select count(1) from entites where raison_sociale = '".$raison_sociale."' and num_bibli='".$numero_bibli."'";

		if($id_entite !== 0){
			$q.=" and id_entite != '".$id_entite."'";
		}
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//optimization de la table entites
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE entites');
		return $opt;
	}

	//Retourne une requete pour liste des bibliotheques
	//si user!=0 la requete est limitee aux bibliotheques accessibles par celui-ci
	public static function list_biblio($user=0) {
		$q = "select * from entites where type_entite = '1' ";
		if ($user) $q.= "and autorisations like('% ".$user." %') ";
		$q.= "order by raison_sociale ";
		return $q;
	}

	//Retourne un tableau id_entite=>['id_entite'=>id_entite , 'raison sociale' => raison_sociale]
	public static function get_etablissements_by_user($id_user = 0, $renew = FALSE) {

	    $id_user = intval($id_user);

	    static::get_all_etablissements($renew);

	    if( $id_user && !isset(static::$etablissements['by_user'][$id_user]) ) {
	        return [];
	    }
	    if( $id_user && isset(static::$etablissements['by_user'][$id_user]) ) {
	        return static::$etablissements['by_user'][$id_user];
	    }
	    return static::$etablissements['by_id'];
	}


	protected static function get_all_etablissements($renew=FALSE) {

	    if(is_null(static::$etablissements) || $renew) {
	        static::$etablissements = ['by_id'=>[], 'by_user'=>[]];
	        $q = "select id_entite, raison_sociale, autorisations from entites where type_entite = ".TYP_ENT_ETAB." order by raison_sociale";
	        $r = pmb_mysql_query($q);
	        if(pmb_mysql_num_rows($r)) {
	            while($row = pmb_mysql_fetch_assoc($r)){
	                $autorisations = explode(' ', trim($row['autorisations']));
	                static::$etablissements['by_id'][$row['id_entite']] = [
	                    'id_entite' => $row['id_entite'],
	                    'raison_sociale' => $row['raison_sociale'],
	                ];
	                if(count($autorisations)) {
	                    foreach($autorisations as $id_user) {
	                        static::$etablissements['by_user'][$id_user][$row['id_entite']] = [
	                            'id_entite' => $row['id_entite'],
	                            'raison_sociale' => $row['raison_sociale'],
	                        ];
	                    }
	                }
	            }
	        }
	    }
	    return static::$etablissements;
	}


	//Retourne un selecteur html avec la liste des etablissements
	public static function get_hmtl_select_etablissements($id_user=0, $selected=0, $sel_all=FALSE, $sel_attr=array()) {

	    global $msg,$charset;

	    $id_user = intval($id_user);
	    $selected = intval($selected);

	    $etablissements = static::get_etablissements_by_user($id_user);
	    if(!count($etablissements)) {
	        return '';
	    }
	    $sel="<select ";
	    if (is_array($sel_attr) && count($sel_attr)) {
	        foreach($sel_attr as $attr=>$val) {
	            $sel.="$attr='".$val."' ";
	        }
	    }
	    $sel.=">";
	    if($sel_all) {
	        $etablissements = [0=>['id_entite'=>0, 'raison_sociale'=>$msg['acquisition_coord_all']]] + $etablissements;
	    }
	    foreach($etablissements as $k=>$etablissement) {
	        $sel.= "<option value='".$k."' ";
	        if($k==$selected) {
	            $sel.=" selected='selected' ";
	        }
	        $sel.= ">".htmlentities($etablissement['raison_sociale'], ENT_QUOTES, $charset)."</option>";
	    }
	    $sel.= "</select>";
	    return $sel;
	}

	//Retourne une checkbox "Tous les etablissements"
	public static function get_html_checkbox_all_etablissements($checked=0, $chk_attr=array()) {

	    global $msg, $charset;

	    $checked = intval($checked);
	    $chk = "<input type='checkbox' ";
	    if (is_array($chk_attr) && count($chk_attr)) {
	        foreach($chk_attr as $attr=>$val) {
	            $chk.= "$attr='".$val."' ";
	        }
	    }
	    if($checked) {
	        $chk.= "checked='checked' ";
	    }
	    $chk.= ">";
	    $chk.= "<label ";
	    if($chk_attr['id']) {
	        $chk.= "for='".$chk_attr['id']."' ";
	    }
	    $chk.= ">".htmlentities($msg['acquisition_affect_fou_to_all_etab'], ENT_QUOTES, $charset)."</label>";
	    return $chk;
	}


	//Retourne un tableau id_entite=>['id_entite'=>id_entite , 'raison sociale' => raison_sociale]
	public static function get_fournisseurs_by_etablissement($etablissement = 0, $renew = FALSE) {

	    $etablissement = intval($etablissement);
	    static::get_all_fournisseurs($renew);
	    if( $etablissement && !isset(static::$fournisseurs['by_etablissement'][$etablissement]) ) {
	        return [];
	    }
	    if( $etablissement && isset(static::$fournisseurs['by_etablissement'][$etablissement]) ) {
	        if(isset(static::$fournisseurs['by_etablissement'][0])) {
	            return array_merge (static::$fournisseurs['by_etablissement'][0], static::$fournisseurs['by_etablissement'][$etablissement]);
	        }
	        return static::$fournisseurs['by_etablissement'][$etablissement];
	    }
	    return static::$fournisseurs['by_id'];
	}


	protected static function get_all_fournisseurs($renew=FALSE) {

	    if(is_null(static::$fournisseurs) || $renew) {
	        static::$fournisseurs = ['by_id'=>[], 'by_etablissement'=>[]];
	        $q = "select id_entite, num_bibli, raison_sociale from entites where type_entite = ".TYP_ENT_FOU;
	        $r = pmb_mysql_query($q);
	        if(pmb_mysql_num_rows($r)) {
	            while($row = pmb_mysql_fetch_assoc($r)){
	                static::$fournisseurs['by_id'][$row['id_entite']] = [
	                    'id_entite' => $row['id_entite'],
	                    'num_bibli' => $row['num_bibli'],
	                    'raison_sociale' => $row['raison_sociale'],
	                ];
	                static::$fournisseurs['by_etablissement'][$row['num_bibli']][$row['id_entite']] = [
	                    'id_entite' => $row['id_entite'],
	                    'num_bibli' => $row['num_bibli'],
	                    'raison_sociale' => $row['raison_sociale'],
	                ];
	            }
	        }
	    }
	    return static::$fournisseurs;
	}


	//Retourne la liste des fournisseurs d'un etablissement dans un ResultSet
	public static function list_fournisseurs($id_bibli=0, $debut=0, $nb_per_page=0, $aq=0) {

	    $id_bibli = intval($id_bibli);

	    $restrict = 'num_bibli=0 ';
		if ($id_bibli) {
	        $restrict = "num_bibli in (0, ".$id_bibli.") ";
		}
	    $restrict = "type_entite = ".TYP_ENT_FOU." and {$restrict} ";
		if(!$aq) {
			$q = "select * from entites where ".$restrict;
			$q.= "order by raison_sociale ";
		} else {
			$members=$aq->get_query_members("entites","raison_sociale","index_entite","id_entite",$restrict);
			$q = "select *, ".$members["select"]." as pert from entites where ".$members["where"]." ";
			if ($restrict) {
				$q.= "and ".$members["restrict"]." ";
			}
			$q.= "order by pert desc ";
		}
		if ($debut) {
			$q.="limit ".$debut ;
			if ($nb_per_page) $q.= ",".$nb_per_page;
		}
		if($nb_per_page && !$debut){
			$q.= "limit 0,".$nb_per_page;
		}
		$r = pmb_mysql_query($q);
		return $r;
	}


	//Compte le nb de fournisseurs pour un etablissement
	public static function getNbFournisseurs($id_bibli=0, $aq=0) {

	    $id_bibli = intval($id_bibli);

		$restrict = 'num_bibli=0 ';
		if ($id_bibli) {
		    $restrict = "num_bibli in (0, ".$id_bibli.") ";
		}
		$restrict = "type_entite = ".TYP_ENT_FOU." and {$restrict} ";

		if (!$aq) {
			$q = "select count(1) from entites where {$restrict} ";
		} else {
			$q = $aq->get_query_count("entites","raison_sociale","index_entite", "id_entite", $restrict);
		}
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//Retourne la liste des offres de remises par type de produit pour un fournisseur dans un ResultSet
	public static function listOffres($id_fou=0) {
		$q = "select * from offres_remises, types_produits where num_fournisseur = '".$id_fou."' and id_produit = num_produit order by libelle ";
		$r = pmb_mysql_query($q);
		return $r;
	}

	//Retourne la liste des types de produits pour lesquels il n'y a pas d'offres pour un fournisseur (dans un ResultSet)
	public static function listNoOffres($id_fou=0) {
		$q = "select num_produit from offres_remises where num_fournisseur = '".$id_fou."' ";
		$r = pmb_mysql_query($q);
		$c = pmb_mysql_num_rows($r);
		$a = array();
		while(($row = pmb_mysql_fetch_object($r))) {
			$a[] = "'".$row->num_produit."'";
		}
		$l = implode(" , ", $a );

		$q = "select id_produit, libelle from types_produits ";
		if ($c) $q.= "where id_produit not in (".$l.") order by libelle";
		$r = pmb_mysql_query($q);
		return $r;
	}


	protected static function get_array_types_produits_sans_remise($id_fournisseur = 0) {

	    $id_fournisseur = intval($id_fournisseur);
	    if (!$id_fournisseur) {
	        return [];
	    }
	    $q = "select id_produit as id, libelle from types_produits where id_produit not in (select num_produit from offres_remises where num_fournisseur={$id_fournisseur}) order by libelle";
	    $r = pmb_mysql_query($q);
	    $n = pmb_mysql_num_rows($r);
	    if(!$n) {
	        return [];
	    }
	    $t = [];
	    while($row = pmb_mysql_fetch_assoc($r)) {
	        $t[] = ['id'=>$row['id'], 'libelle'=>$row['libelle']];
	    }
	    return $t;

	}


	public static function get_html_select_types_produits_sans_remise($id_fournisseur = 0, $sel_attr = array()) {

	    global $charset;

	    $id_fournisseur = intval($id_fournisseur);
	    if(!is_array($sel_attr)) {
	        $sel_attr = [];
	    }
	    $types_produits = static::get_array_types_produits_sans_remise($id_fournisseur);

	    if(!count($types_produits)) {
	        return '';
	    }

	    $sel="<select ";
	    if (is_array($sel_attr) && count($sel_attr)) {
	        foreach($sel_attr as $attr=>$val) {
	            $sel.="$attr='".$val."' ";
	        }
	    }
	    $sel.=">";
	    foreach($types_produits as $k=>$type_produit) {
	        $sel.= "<option value='".$type_produit['id']."' ";
	        if($k==0) {
	            $sel.=" selected='selected' ";
	        }
	        $sel.= ">".htmlentities($type_produit['libelle'], ENT_QUOTES, $charset)."</option>";
	    }
	    $sel.= "</select>";
	    return $sel;


	}


	//Retourne la liste des actes d'un type pour une bibliothèque dans un ResultSet
	public static function listActes($id_bibli, $type_acte, $statut='-1', $debut=0, $nb_per_page=0, $aq=0, $user_input='', $tri='', $id_exercice=0) {
		if ($statut == '-1') {
			$filtre = '';
		} elseif ($statut == 32) {
			$filtre = "and ((actes.statut & 32) = 32) ";
		} else {
			$filtre = "and ((actes.statut & 32) = 0) and ((actes.statut & ".$statut.") = '".$statut."') ";
		}

		if ($id_exercice) {
			$filtre .="and actes.num_exercice=".$id_exercice;
		}

		$order="";
		if(trim($tri)){
			if(substr($tri,0,1)=="-"){
				$order=" ORDER BY ".substr($tri,1)." DESC ";
			}else{
				$order=" ORDER BY ".$tri." ";
			}
		}

		if(!$aq) {
			$q = "SELECT date_ech_calc, raison_sociale, actes2.numero as num_acte_parent, actes.*
					FROM (actes
					LEFT JOIN (SELECT MIN((DATE_FORMAT(date_ech, '%Y%m%d'))) AS date_ech_calc, num_acte FROM lignes_actes WHERE (('2' & statut) = '0') GROUP BY num_acte) dl ON dl.num_acte=actes.id_acte)
					LEFT JOIN entites ON entites.id_entite=actes.num_fournisseur
					LEFT JOIN liens_actes ON num_acte_lie=actes.id_acte
					LEFT JOIN actes actes2 ON actes2.id_acte=liens_actes.num_acte
					WHERE actes.num_entite = '".$id_bibli."' ";
			$q.= "AND actes.type_acte = '".$type_acte."' ".$filtre." ";
			if(trim($order)){
				$q.=$order;
			} else{
				$q.= "ORDER BY actes.numero DESC ";
			}
			$q.= "limit ".$debut ;
			if ($nb_per_page) $q.= ",".$nb_per_page;

		} else {

			$isbn = '';
			$t_codes = array();

			if ($user_input!=='') {
				if (isEAN($user_input)) {
					// la saisie est un EAN -> on tente de le formater en ISBN
					$isbn = EANtoISBN($user_input);
					// si échec, on prend l'EAN comme il vient
					if($isbn) {
						$t_codes[] = $isbn;
						$t_codes[] = formatISBN($isbn,10);
					}
				} elseif (isISBN($user_input)) {
					// si la saisie est un ISBN
					$isbn = formatISBN($user_input);
					if($isbn) {
						$t_codes[] = $isbn ;
						$t_codes[] = formatISBN($isbn,13);
					}
				} elseif (isISSN($user_input)) {
					$t_codes[] = $user_input ;
				}
			}

			if (count($t_codes)) {

				$q = "SELECT distinct(actes.id_acte), actes.*, date_ech_calc, actes2.numero as num_acte_parent, raison_sociale
					FROM (actes left join lignes_actes on num_acte=id_acte
					LEFT JOIN (SELECT MIN((DATE_FORMAT(date_ech, '%Y%m%d'))) AS date_ech_calc, num_acte FROM lignes_actes WHERE (('2' & statut) = '0') GROUP BY num_acte) dl ON dl.num_acte=actes.id_acte)
					LEFT JOIN entites ON entites.id_entite=actes.num_fournisseur
					LEFT JOIN liens_actes ON num_acte_lie=actes.id_acte
					LEFT JOIN actes actes2 ON actes2.id_acte=liens_actes.num_acte ";
				$q.= "WHERE ( actes.num_entite='".$id_bibli."' and actes.type_acte='".$type_acte."' ".$filtre." ) ";
				$q.= "and ('0' ";
				foreach ($t_codes as $v) {
					$q.= "or lignes_actes.code like '%".$v."%' ";
				}
				$q.=") ";
				if(trim($order)){
					$q.=$order;
				} else{
					$q.= "order by actes.date_ech asc, actes.numero asc";
				}
				$q.=" limit ".$debut.",".$nb_per_page." ";

			} else {

				$members_actes = $aq->get_query_members("actes","actes.numero","actes.index_acte", "actes.id_acte");
				$members_lignes = $aq->get_query_members("lignes_actes","lignes_actes.code","lignes_actes.index_ligne", "lignes_actes.id_ligne");
				$q = "select distinct(actes.id_acte), actes.*, date_ech_calc, actes2.numero as num_acte_parent, raison_sociale, max(".$members_actes["select"]."+".$members_lignes["select"].") as pert
						from (actes left join lignes_actes on num_acte=id_acte
						LEFT JOIN (SELECT MIN((DATE_FORMAT(date_ech, '%Y%m%d'))) AS date_ech_calc, num_acte FROM lignes_actes WHERE (('2' & statut) = '0') GROUP BY num_acte) dl ON dl.num_acte=actes.id_acte)
						LEFT JOIN entites ON entites.id_entite=actes.num_fournisseur
						LEFT JOIN liens_actes ON num_acte_lie=actes.id_acte
						LEFT JOIN actes actes2 ON actes2.id_acte=liens_actes.num_acte ";
				$q.= "where actes.num_entite='".$id_bibli."' and actes.type_acte='".$type_acte."' ".$filtre." ";
				$q.= "and (".$members_actes["where"]." or ".$members_lignes["where"].") ";
				$q.= "group by actes.id_acte ";
				if(trim($order)){
					$q.=$order;
				} else{
					$q.= "order by pert desc";
				}
				$q.=" limit ".$debut.",".$nb_per_page." ";
			}
		}
		$r = pmb_mysql_query($q);
		return $r;
	}

	//Compte le nb d'acte d'un type pour une bibliothèque
	public static function getNbActes($id_bibli, $type_acte, $statut='-1', $aq=0, $user_input='', $id_exercice=0) {
		if ($statut == '-1') {
			$filtre = '';
		} elseif ($statut == 32) {
			$filtre = "and ((actes.statut & 32) = 32) ";
		} else {
			$filtre = "and ((actes.statut & 32) = 0) and ((actes.statut & ".$statut.") = '".$statut."') ";
		}

		if ($id_exercice) {
			$filtre .="and num_exercice=".$id_exercice;
		}


		if (!$aq) {
			$q = "select count(1) from actes where num_entite = '".$id_bibli."' ";
			$q.= "and type_acte = '".$type_acte."' ".$filtre." ";
		} else {

			$isbn = '';
			$t_codes = array();

			if ($user_input!=='') {
				if (isEAN($user_input)) {
					// la saisie est un EAN -> on tente de le formater en ISBN
					$isbn = EANtoISBN($user_input);
					if($isbn) {
						$t_codes[] = $isbn;
						$t_codes[] = formatISBN($isbn,10);
					}
				} elseif (isISBN($user_input)) {
					// si la saisie est un ISBN
					$isbn = formatISBN($user_input);
					if($isbn) {
						$t_codes[] = $isbn ;
						$t_codes[] = formatISBN($isbn,13);
					}
				} elseif (isISSN($user_input)) {
					$t_codes[] = $user_input ;
				}
			}

			if (count($t_codes)) {

				$q = "select count(distinct(id_acte)) from actes left join lignes_actes on num_acte=id_acte ";
				$q.= "where ( num_entite='".$id_bibli."' and type_acte='".$type_acte."' ".$filtre." ) ";
				$q.= "and ('0' ";
				foreach ($t_codes as $v) {
					$q.= "or code like '%".$v."%' ";
				}
				$q.=") ";

			} else {

				$members_actes = $aq->get_query_members("actes","numero","index_acte", "id_acte");
				$members_lignes = $aq->get_query_members("lignes_actes","code","index_ligne", "id_ligne");
				$q = "select count(distinct(id_acte)) from actes left join lignes_actes on num_acte=id_acte ";
				$q.= "where ( num_entite='".$id_bibli."' and type_acte='".$type_acte."' ".$filtre." ) ";
				$q.= "and (".$members_actes["where"]." or ".$members_lignes["where"].") ";

			}
		}
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//Compte le nb de coordonnées pour une entité
	public static function count_coordonnees($id_entite=0) {
		$q = "select count(1) from coordonnees where num_entite = '".$id_entite."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}

	//Retourne un resultset contenant les coordonnées d'une entité
	//Si type_entite=1, retourne l'adresse principale (de facturation)
	//Si type_entite=2, retourne l'adresse de livraison
	//Si type_entite=0, retourne les autres coordonnées
	//Si type_entite=-1, retourne toutes les coordonnées
	public static function get_coordonnees($id_entite=0, $type_coord=0, $debut=0, $nb_per_page=0) {
		//if (!$id_entite) $id_entite = $this->id_entite;
		$q = "select * from coordonnees where num_entite = '".$id_entite."' ";
		if($type_coord != '-1') $q.= "and type_coord = '".$type_coord."' ";
		if ($debut) {
			$q.="limit ".$debut ;
			if($nb_per_page) $q.= ",".$nb_per_page;
		}
		$r = pmb_mysql_query($q);
		return $r;
	}

	//Compte le nb d'exercices pour une entité
	public static function has_exercices($id_entite=0, $statut='-1') {
		$q = "select count(1) from exercices where num_entite = '".$id_entite."' ";
		if($statut != '-1') $q.= "and statut = '".$statut."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}

	//Compte le nb de budgets pour une entité
	public static function has_budgets($id_entite=0) {
		$q = "select count(1) from budgets where num_entite = '".$id_entite."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}

	//Retourne les budgets actifs pour une entité sous forme de Resultset
	public static function listBudgetsActifs($id_entite=0) {
		$q = "select id_budget, libelle from budgets where num_entite = '".$id_entite."' and statut = '1' ";
		$r = pmb_mysql_query($q);
		return $r;
	}


	//Retourne la liste des ids des budgets actifs pour une entité, triés par libelle alpha
	public static function getIdsBudgetsActifsAsArray($id_entite=0) {

	    $id_entite = intval($id_entite);
	    $ret = array();
	    if(!$id_entite) {
	        return $ret;
	    }
	    $q = "select id_budget from budgets where num_entite = '".$id_entite."' and statut = '1' order by libelle";
	    $r = pmb_mysql_query($q);
	    if(pmb_mysql_num_rows($r)) {
	        while($row = pmb_mysql_fetch_array($r, MYSQLI_NUM)) {
	            $ret[] = $row[0];
	        }
	    }

	    return $ret;
	}


	//Retourne un Resultset contenant les rubriques finales des budgets d'une entite en fonction des droits de l'utilisateur courant si per_user=TRUE
	//modification de la recherche : on retourne les rubriques finales, mais on cherche dans toutes les rubriques
	public static function listRubriquesFinales($id_entite=0, $id_exer=0, $per_user=FALSE, $debut=0, $nb_per_page=0, $elt_query=''){
		//on cherche toutes les rubriques correspondant à la recherche
		$q = "select rubriques.id_rubrique from budgets, rubriques ";
		$q.= "where budgets.statut = '1' and budgets.num_entite = '".$id_entite."'  and budgets.num_exercice = '".$id_exer."' and rubriques.num_budget = budgets.id_budget ";
		if(trim($elt_query)){
			$q.="and rubriques.libelle like '".addslashes(str_replace('*','%',$elt_query))."' ";
		}
		$r = pmb_mysql_query($q);

		//on liste toutes les rubriques finales correspondantes
		$array_rubriques_finales = array();
		if (pmb_mysql_num_rows($r)) {
			while ($row = pmb_mysql_fetch_object($r)) {
				$rub_finales = entites::findRubriquesFinales($row->id_rubrique);
				if (count($rub_finales)) {
					foreach ($rub_finales as $id_rub_finale) {
						if ((!count($array_rubriques_finales)) || (!in_array($id_rub_finale,$array_rubriques_finales))) {
							$array_rubriques_finales[] = $id_rub_finale;
						}
					}
				}
			}
		}

		//on retourne le recordset des rubriques finales
		$q = "select budgets.libelle as lib_bud, budgets.type_budget, budgets.montant_global, budgets.seuil_alerte, rubriques.* from budgets, rubriques left join rubriques as rubriques2 on rubriques.id_rubrique=rubriques2.num_parent ";
		$q.= "where budgets.statut = '1' and budgets.num_entite = '".$id_entite."'  and budgets.num_exercice = '".$id_exer."' and rubriques.num_budget = budgets.id_budget and rubriques2.num_parent is NULL ";
		$q.= "and rubriques.id_rubrique in (".implode(",",$array_rubriques_finales).") ";
		if($per_user) {

			//Récupération de l'utilisateur
		 	$requete_user = "SELECT userid FROM users where username='".SESSlogin."' limit 1 ";
			$res_user = pmb_mysql_query($requete_user);
			$row_user=pmb_mysql_fetch_row($res_user);
			$user_userid=$row_user[0];

		$q.= "and rubriques.autorisations like('% ".$user_userid." %') ";
		}
		$q.= "order by budgets.libelle, rubriques.id_rubrique ";

		if ($debut) {
			$q.="limit ".$debut ;
			if($nb_per_page) $q.= ",".$nb_per_page;
		} else {
			if($nb_per_page) $q.= "limit ".$nb_per_page;
		}

		$r = pmb_mysql_query($q);
		return $r;

	}

	//Trouve de façon récursive toutes les rubriques finales d'une rubrique particulière
	public static function findRubriquesFinales($id_rubrique){
		$liste_rubriques = array();

		$q = "select id_rubrique from rubriques where num_parent = ".$id_rubrique;
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) {
			while ($row = pmb_mysql_fetch_object($r)) {
				$rub_enfants = entites::findRubriquesFinales($row->id_rubrique);
				if (count($rub_enfants)) {
					$liste_rubriques = array_merge($liste_rubriques,$rub_enfants);
				}
			}
		} else {
			$liste_rubriques[] = $id_rubrique;
		}

		return $liste_rubriques;
	}


	//Retourne le nombre de rubriques finales des budgets actifs d'une entite en fonction des droits de l'utilisateur courant si per_user=TRUE
	//modification de la recherche : on cherche dans toutes les rubriques
	public static function countRubriquesFinales($id_entite=0, $id_exer=0, $per_user=FALSE, $elt_query=''){
		$id_entite = intval($id_entite);

		//on cherche toutes les rubriques correspondant à la recherche
		$q = "select rubriques.id_rubrique from budgets, rubriques ";
		$q.= "where budgets.statut = '1' and budgets.num_entite = '".$id_entite."'  and budgets.num_exercice = '".$id_exer."' and rubriques.num_budget = budgets.id_budget ";
		if(trim($elt_query)){
			$q.="and rubriques.libelle like '".addslashes(str_replace('*','%',$elt_query))."' ";
		}
		$r = pmb_mysql_query($q);

		//on liste toutes les rubriques finales correspondantes
		$array_rubriques_finales = array();
		if (pmb_mysql_num_rows($r)) {
			while ($row = pmb_mysql_fetch_object($r)) {
				$rub_finales = entites::findRubriquesFinales($row->id_rubrique);
				if (count($rub_finales)) {
					foreach ($rub_finales as $id_rub_finale) {
						if ((!count($array_rubriques_finales)) || (!in_array($id_rub_finale,$array_rubriques_finales))) {
							$array_rubriques_finales[] = $id_rub_finale;
						}
					}
				}
			}
		}

		//on retourne le recordset des rubriques finales
		$q = "select count(1) from budgets, rubriques ";
		$q.= "where budgets.statut = '1' and budgets.num_entite = '".$id_entite."' and budgets.num_exercice = '".$id_exer."' and rubriques.num_budget = budgets.id_budget ";
		if($per_user) {

			//Récupération de l'utilisateur
		 	$requete_user = "SELECT userid FROM users where username='".SESSlogin."' limit 1 ";
			$res_user = pmb_mysql_query($requete_user);
			$row_user=pmb_mysql_fetch_row($res_user);
			$user_userid=$row_user[0];

		$q.= "and rubriques.autorisations like('% ".$user_userid." %') ";
		}
		if (count($array_rubriques_finales)) {
			$q.= "and rubriques.id_rubrique in (".implode(",",$array_rubriques_finales).") ";
		} else {
			$q.= "and rubriques.id_rubrique = 0 "; //pas de rubrique trouvée
		}

		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//Retourne les exercices courants d' une entité
	public static function getCurrentExercices($id_entite=0) {
		$q = "select id_exercice, libelle, statut from exercices where num_entite = '".$id_entite."' and (statut &  '".STA_EXE_ACT."') = '".STA_EXE_ACT."' ";
		$q.= "order by statut desc ";
		return $q;
	}

	//Compte le nb de suggestions pour une entité
	public static function has_suggestions($id_entite=0) {
		$q = "select count(1) from suggestions where num_entite = '".$id_entite."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//Compte le nb d'actes pour une entité
	public static function has_actes($id_entite=0,$type_entite=0) {
		if ($type_entite) {
			$q = "select count(1) from actes where num_entite = '".$id_entite."' ";
			$r = pmb_mysql_query($q);
		} else {
			$q = "select count(1) from actes where num_fournisseur = '".$id_entite."' ";
			$r = pmb_mysql_query($q);
		}
		return pmb_mysql_result($r, 0, 0);
	}

	//Màj des autorisations dans les rubriques lors de la màj des autorisations dans les entités
	public function majAutorisations() {
			$q = "select id_budget from budgets where num_entite = '".$this->id_entite."' ";
			$r = pmb_mysql_query($q);
			$nb = intval(pmb_mysql_num_rows($r));

			if ($nb) {
				$liste= '';
				for ($i=0; $i<$nb; $i++) {
					$row =pmb_mysql_fetch_row($r);
					$liste.= $row[0];
					if ($i<$nb-1) $liste.= ', ';
				}

			$q = "select id_rubrique, autorisations from rubriques where autorisations != '' and num_budget in (".$liste.") ";
			$r = pmb_mysql_query($q);
			$aut_entite = explode(' ',$this->autorisations);

			while(($row=pmb_mysql_fetch_object($r))) {

				$aut_rub = explode(' ',$row->autorisations);
				$aut = array_intersect($aut_entite, $aut_rub);

				$q1 = "update rubriques set autorisations = '".' '.implode(' ',$aut).' '."' where id_rubrique = '".$row->id_rubrique."' ";
				pmb_mysql_query($q1);
			}
		}
	}

	//Recuperation de l'etablissement session
	public static function getSessionBibliId() {
		global $deflt3bibli;
		if (!isset($_SESSION['id_bibli'])) $_SESSION['id_bibli'] = '';
		if (!$_SESSION['id_bibli'] && $deflt3bibli) {
			$_SESSION['id_bibli']=$deflt3bibli;
		}
		return $_SESSION['id_bibli'];
	}

	//Definition de l'etablissement session
	public static function setSessionBibliId($id_bibli) {
		$_SESSION['id_bibli']=$id_bibli;
		return;
	}

	//recuperation de l'etablissement par défaut
	public static function getDefaultBibliId() {
	    $id_bibli = static::getSessionBibliId();
	    if (!$id_bibli) {
	        $tab_bibli = static::get_entities();
	        if(count($tab_bibli)) {
	           $id_bibli = $tab_bibli[0]['id'];
	        }
	    }
	    return $id_bibli;
	}


	//Retourne un selecteur html avec la liste des bibliotheques
	public static function getBibliHtmlSelect($user=FALSE, $selected=0, $sel_all=FALSE, $sel_attr=array()) {
		global $msg,$charset;

		$sel='';
		$q = "select id_entite,raison_sociale from entites where type_entite = '1' ";
		if ($user) $q.= "and autorisations like('% ".$user." %') ";
		$q.= "order by raison_sociale ";
		$r = pmb_mysql_query($q);
		$res = array();
		if ($sel_all) {
			$res[0]=$msg['acquisition_coord_all'];
		}
		if($r && pmb_mysql_num_rows($r)){
			while ($row = pmb_mysql_fetch_object($r)){
				$res[$row->id_entite] = $row->raison_sociale;
			}
		}

		if (count($res)) {
			$sel="<select ";
			if (count($sel_attr)) {
				foreach($sel_attr as $attr=>$val) {
					$sel.="$attr='".$val."' ";
				}
			}
			$sel.=">";
			foreach($res as $id=>$val){
				$sel.="<option value='".$id."'";
				if($id==$selected) $sel.=" selected='selected' ";
				$sel.=" >";
				$sel.=htmlentities($val,ENT_QUOTES,$charset);
				$sel.="</option>";
			}
			$sel.='</select>';
		}
		return $sel;
	}

	//Retourne un tableau (id_entite=>raison sociale) a partir d'un tableau d'id
	//si id_bibli est précisé, limite les resultats aux fournisseurs par bibliotheque
	public static function getRaisonSociale($tab=array(),$id_bibli=0) {
		$res=array();
		if(is_array($tab) && count($tab)) {
			$q ="select id_entite, raison_sociale from entites where id_entite in ('".implode("','", $tab)."') ";
			if($id_bibli) $q.= " and num_bibli='".$id_bibli."' ";
			$r = pmb_mysql_query($q);
			while($row=pmb_mysql_fetch_object($r)) {
				$res[$row->id_entite]=$row->raison_sociale;
			}
		}
		return $res;
	}

	//Compte le nb d'abonnements pour une entité
	public static function has_abonnements($id_entite=0) {
		$q = "select count(1) from abts_abts where fournisseur = '".$id_entite."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}

	// Liste des établissements autorisés pour l'utilisateur
	static public function get_entities() {
		$entities = array();
		$query = entites::list_biblio(SESSuserid);
		$result = pmb_mysql_query($query);
		if($result) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$entities[] = array(
						'id' => $row->id_entite,
						'label' => $row->raison_sociale
				);
			}
		}
		return $entities;
	}

	// Administration - Affiche la liste des etablissements
	static public function get_display_list_entities($entities = array(), $type = 'pricing_systems') {
		$parity=1;
		$display = "<table>";
		foreach ($entities as $entity) {
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity += 1;
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./admin.php?categ=acquisition&sub=".$type."&id_entity=".$entity['id']."';\" ";
			$display .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'><td><i>".$entity['label']."</i></td></tr>";
		}
		$display .= "</table>";
		return $display;
	}

	static public function is_selected_biblio($function_name = '', $class_name = '') {
		//Affiche de la liste des etablissements auxquels a acces l'utilisateur si > 1
		$entities = static::get_entities();
		if (count($entities) == 1) {
			return true;
		}
		$def_bibli=static::getSessionBibliId();
		foreach ($entities as $entity) {
			if($def_bibli == $entity['id']) {
				return true;
			}
		}
		return false;
	}

	// Acquisition - Affiche la liste des etablissements
	static public function show_list_biblio($function_name = '', $class_name = '') {
		global $msg, $charset;

		//Affiche de la liste des etablissements auxquels a acces l'utilisateur si > 1
		$entities = static::get_entities();
		if (count($entities) == 1) {
			static::setSessionBibliId($entities[0]['id']);
			if($class_name != '') {
				$instance = new $class_name();
				return $instance->$function_name($entities[0]['id']);
			} else {
				$function_name($entities[0]['id']);
				exit;
			}
		}
		$def_bibli=static::getSessionBibliId();
		foreach ($entities as $entity) {
			if($def_bibli == $entity['id']) {
				if($class_name != '') {
					$instance = new $class_name();
					return $instance->$function_name($def_bibli);
				} else {
					$function_name($def_bibli);
					exit;
				}
			}
		}
		$display = "<h3>".htmlentities($msg['acquisition_menu_chx_ent'], ENT_QUOTES, $charset)."</h3><div class='row'></div>";
		$display .= list_accounting_entites_ui::get_instance()->get_display_list();
		return $display;
	}

	// Acquisition - Retourne un formulaire liste des fournisseurs pour un etablissement
	static public function get_form_list_fournisseurs($id_etablissement = 0, $function_name = '', $class_name = '') {
	    $id_etablissement = intval($id_etablissement);

	    if(!$class_name || !class_exists($class_name)) {
	        $class_name = '';
	    }
	    if($class_name && !method_exists($class_name, $function_name)) {
	        $class_name = '';
	        $function_name = '';
	    }
	    if(!$class_name && function_exists($function_name)) {
	        $function_name = '';
	    }

	    if(!SESSuserid) {
	        return '';
	    }

	    if(!$id_etablissement) {
	        $etablissements = static::get_etablissements_by_user(SESSuserid);
	    }

	    //pas d'etablissements
	    if(!(count($etablissements))) {
	        return '';
	    }

	    //1 seul etablissement
	    if (count($etablissements) == 1) {
	        reset ($etablissements);
	        $k = current($etablissements);
	        static::setSessionBibliId($k);
	        if($class_name) {
	            $instance = new $class_name();
	            return $instance->$function_name($k);
	        } else if($function_name) {
	            $function_name($k);
	            return;
	        }
	        return '';
	    }

	    //plusieurs etablissements et 1 etablissement en session
	    $def_etablissement = static::getSessionBibliId();
	    if($def_etablissement && isset($etablissements[$def_etablissement])) {
	        $k = $def_etablissement;
	        if($class_name) {
	            $instance = new $class_name();
	            return $instance->$function_name($k);
	        } else if($function_name) {
	            $function_name($k);
	            return;
	        }
	    }
	    //sinon on retourne une liste des etablissements
	    return static::get_form_list_etablissements(SESSuserid);
	}

	// Acquisition - Retourne un formulaire liste des etablissements
	static protected function get_form_list_etablissements($id_user = 0) {
	    global $msg, $charset;
	    global $categ, $sub;
	    global $current_module;

	    $id_user = intval($id_user);
	    if(!$id_user) {
	        return '';
	    }

	    $etablissements = static::get_etablissements_by_user($id_user);

	    if(!count($etablissements)) {
	        return '';
	    }

	    $display = "<form class='form-".$current_module."' id='list_biblio_form' name='list_biblio_form' method='post' action=\"\" >";
	    $display .= "<h3>".htmlentities($msg['acquisition_menu_chx_ent'], ENT_QUOTES, $charset)."</h3><div class='row'></div>";
	    $display .= "<table>";

	    $parity=1;
	    foreach($etablissements as $k=>$etablissement) {
	        if ($parity % 2) {
	            $pair_impair = "even";
	        } else {
	            $pair_impair = "odd";
	        }
	        $parity += 1;
	        $tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" onmousedown=\"document.forms['list_biblio_form'].setAttribute('action','./acquisition.php?categ=".$categ."&sub=".$sub."&action=list&id_bibli=".$k."');document.forms['list_biblio_form'].submit(); \" ";
	        $display .= "<tr class='".$pair_impair."' ".$tr_javascript." style='cursor: pointer'><td><i>".htmlentities($etablissement['raison_sociale'], ENT_QUOTES, $charset)."</i></td></tr>";
	    }
	    $display .=" </table></form>";
	    return $display;
	}
}
