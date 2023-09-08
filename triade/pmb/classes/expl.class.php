<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl.class.php,v 1.108 2019-02-21 14:58:15 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classe de gestion des exemplaires
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/audit.class.php");
require_once($class_path."/sur_location.class.php");
require_once($class_path."/serialcirc.class.php");
require_once($class_path."/index_concept.class.php");
require_once($base_path."/catalog/expl/prix_expl.inc.php");
require_once($base_path.'/admin/convert/export.class.php');
require_once($class_path."/import/import_expl.class.php");

if(!isset($pmb_numero_exemplaire_auto)) $pmb_numero_exemplaire_auto = '';
if ($pmb_numero_exemplaire_auto) {
	if (file_exists($include_path."/$pmb_numero_exemplaire_auto_script")) {
		require_once($include_path."/$pmb_numero_exemplaire_auto_script");
	}else {
		require_once($include_path."/gen_code/gen_code_exemplaire.php");
	}
}else {
	// Utile pour les expl numériques de PNB
	require_once($include_path."/gen_code/gen_code_exemplaire.php");
}

//require_once($include_path."/gen_code/gen_code_exemplaire.php");
if ( ! defined( 'EXEMPLAIRE_CLASS' ) ) {
  define( 'EXEMPLAIRE_CLASS', 1 );

class exemplaire {
	
	public $expl_id = 0;
	public $cb = '';
	public $id_notice = 0;
	public $id_bulletin = 0;
	public $id_bulletin_notice = 0;
	public $id_num_notice = 0;
	public $typdoc_id = 0;
	public $typdoc = '';
	public $duree_pret = 0;
	public $cote = '';
	public $section_id = 0;
	public $section = '';
	public $statut_id = 0;
	public $statut = '';
	public $pret = 0;
	public $location_id = 0;
	public $location = '';
	public $codestat_id = 0;
	public $codestat = '';
	public $date_depot = '0000-00-00';
	public $date_retour = '0000-00-00';
	public $note = '';
	public $prix = '';
	public $owner_id = 0;
	public $lastempr = 0;
	public $last_loan_date = '0000-00-00';
	public $create_date = '0000-00-00';
	public $update_date = '0000-00-00';
	public $type_antivol="";
	public $tranfert_location_origine = 0;
	public $tranfert_statut_origine = 0;
	public $tranfert_section_origine = 0;
	public $expl_comment='';
	public $nbparts = 1;
	public $expl_retloc = 0;
	
	public $ajax_cote_fields = '';
	public $explr_acces_autorise="MODIF" ; // sera égal à INVIS, MODIF ou UNMOD en fonction des droits de l'utilisateur sur la localisation
	public $error = false;
	public static $digital_ids = array();
	/**
	 * @var id de l'exemplaire dont le fantome est issus
	 */
	public $ref_num = 0;
	
	protected static $long_maxi_cb;
	protected static $long_maxi_cote;
	protected static $long_maxi_prix;
	
	// constructeur
	public function __construct($cb='', $id=0, $id_notice=0, $id_bulletin=0) {
	
		global $dbh;
		global $class_path;
		global $pmb_sur_location_activate;
		
		// on checke si l'exemplaire est connu
		if ($cb && !$id) $clause_where = " WHERE expl_cb like '$cb' ";
		
		if ( (!$cb && $id) || ($cb && $id) ) $clause_where = " WHERE expl_id = '$id' ";
		
		if ($cb || $id) {
			$requete = "SELECT *, section_libelle, location_libelle";
			$requete .= " FROM exemplaires LEFT JOIN docs_section ON (idsection = expl_section) 
					LEFT JOIN docs_location ON (idlocation = expl_location)
					LEFT JOIN docs_type ON (idtyp_doc = expl_typdoc)";
			$requete .= $clause_where ;
			$result = @pmb_mysql_query($requete, $dbh);
	
			if(pmb_mysql_num_rows($result)) {
				$item = pmb_mysql_fetch_object($result);
				$this->expl_id		= $item->expl_id;
				$this->cb			= $item->expl_cb;
				$this->id_notice	= $item->expl_notice;
				$this->id_bulletin	= $item->expl_bulletin;
				$this->typdoc_id	= $item->expl_typdoc;
				$this->typdoc		= $item->tdoc_libelle;
				$this->duree_pret	= $item->duree_pret;
				$this->cote			= $item->expl_cote;
				$this->section_id	= $item->expl_section;
				$this->section		= $item->section_libelle;
				$this->statut_id	= $item->expl_statut;
				//$this->statut		= $item->statut_libelle;		
				//$this->pret		= $item->pret_flag;				
				$this->location_id	= $item->expl_location;
				$this->location		= $item->location_libelle;
				$this->codestat_id	= $item->expl_codestat;
				//$this->codestat	= $item->codestat_libelle;
				$this->date_depot 	= $item->expl_date_depot ;
				$this->date_retour 	= $item->expl_date_retour ;
				$this->note			= $item->expl_note;
				$this->prix			= $item->expl_prix;
				$this->owner_id		= $item->expl_owner;
				$this->lastempr		= $item->expl_lastempr;
				$this->last_loan_date =  $item->last_loan_date;
				$this->create_date 	= $item->create_date;
				$this->update_date 	= $item->update_date;
				$this->type_antivol = $item->type_antivol ;
				$this->transfert_location_origine = $item->transfert_location_origine;
				$this->transfert_statut_origine = $item->transfert_statut_origine;
				$this->transfert_section_origine = $item->transfert_section_origine;
				$this->expl_comment	= $item->expl_comment;
				$this->nbparts		= $item->expl_nbparts;
				$this->expl_retloc	= $item->expl_retloc;
				$this->ref_num = $item->expl_ref_num;
				
				if($pmb_sur_location_activate){
					$sur_loc= sur_location::get_info_surloc_from_location($item->expl_location);
					$this->sur_loc_libelle=$sur_loc->libelle;
					$this->sur_loc_id=$sur_loc->id;
				}
				// visibilite des exemplaires
				global $explr_invisible, $explr_visible_unmod, $explr_visible_mod, $pmb_droits_explr_localises ;
				if ($pmb_droits_explr_localises) {
					$tab_invis=explode(",",$explr_invisible);
					$tab_unmod=explode(",",$explr_visible_unmod);
	
					$as_invis = array_search($this->location_id,$tab_invis);
					$as_unmod = array_search($this->location_id,$tab_unmod);
					if ($as_invis!== FALSE && $as_invis!== NULL) {
						$this->explr_acces_autorise="INVIS" ;	
					} elseif ($as_unmod!== FALSE && $as_unmod!== NULL) {
						$this->explr_acces_autorise="UNMOD" ;
					} else {
						$this->explr_acces_autorise="MODIF" ;	
					}
				} else {
					$this->explr_acces_autorise="MODIF" ;	
				}
				
			} else { // rien trouvé en base
				$this->cb = $cb;
				$this->id_notice = $id_notice;
				$this->id_bulletin = $id_bulletin;
				$this->set_deflt_typdoc_id();
				global $explr_invisible, $explr_visible_unmod, $explr_visible_mod, $pmb_droits_explr_localises ;
				if ($pmb_droits_explr_localises) {
					if ($explr_visible_mod) {
						$this->explr_acces_autorise="MODIF" ;
					} else {
						$this->explr_acces_autorise="UNMOD" ;
					}
				} else {
					$this->explr_acces_autorise="MODIF" ;
				}
			}
		} else { // rien de fourni apparemment
			$this->cb = $cb;
			$this->id_notice = $id_notice;
			$this->id_bulletin = $id_bulletin;
			$this->set_deflt_typdoc_id();
			global $explr_invisible, $explr_visible_unmod, $explr_visible_mod, $pmb_droits_explr_localises ;
			if ($pmb_droits_explr_localises) {
				if ($explr_visible_mod) {
					$this->explr_acces_autorise="MODIF" ;
				} else {
					$this->explr_acces_autorise="UNMOD" ;
				}
			} else {
				$this->explr_acces_autorise="MODIF" ;
			}
		}
		if ($this->id_bulletin) {
  			$qb="select bulletin_notice, num_notice from bulletins where bulletin_id='".$this->id_bulletin."' ";
   			$rb=@pmb_mysql_query($qb, $dbh);
   			if (pmb_mysql_num_rows($rb)) {
   				$this->id_bulletin_notice=pmb_mysql_result($rb,0,0);
   				$this->id_num_notice=pmb_mysql_result($rb,0,1);
   			}
		}
	}	
	
	public function set_properties_from_form() {
		global $f_ex_cb, $f_ex_typdoc, $f_ex_cote;
		global $f_ex_section, $f_ex_statut, $f_ex_location;
		global $f_ex_cstat, $f_ex_note, $f_ex_comment;
		global $f_ex_prix, $f_ex_owner, $type_antivol, $f_ex_nbparts;
		
		$this->cb = $f_ex_cb;
				
		$this->typdoc_id = $f_ex_typdoc+0;
		$this->cote = stripslashes($f_ex_cote);
		$this->section_id = $f_ex_section+0;
		//Exception pour certains formulaires
		if(!$this->section_id) {
			global ${'f_ex_section'.$f_ex_location};
			$this->section_id = ${'f_ex_section'.$f_ex_location};
			$this->section_id += 0;
		}
		$this->statut_id = $f_ex_statut+0;
		$this->location_id = $f_ex_location+0;
		$this->codestat_id = $f_ex_cstat+0;
		$this->note = stripslashes($f_ex_note);
		$this->expl_comment = stripslashes($f_ex_comment);
		$this->prix = stripslashes($f_ex_prix);
		$this->owner_id = $f_ex_owner+0;		
		$this->type_antivol = $type_antivol+0;
		$this->nbparts = $f_ex_nbparts+0;
	}
	
	//sauvegarde en base
	public function save() {
		global $dbh;
		global $thesaurus_concepts_active;
		
		$this->error=false;

		if ((trim($this->cb)!=='')
				&& ($this->id_notice || $this->id_bulletin) 
				&& ($this->typdoc_id)
				&& (trim($this->cote)!=='')
				&& ($this->location_id)
				&& ($this->section_id)
				&& ($this->codestat_id)	
				&& ($this->owner_id)	
				&& ($this->statut_id)	) {
						
			if ($this->expl_id) {
				$q = "update exemplaires set ";
			} else {
				$q = "insert into exemplaires set ";
			}
			$q.= "expl_cb = '".$this->cb."', ";
			$q.= "expl_notice = '".$this->id_notice."', ";
			if ($this->id_notice) {
				$q.= "expl_bulletin = '0', ";
			} else {
				$q.= "expl_bulletin = '".$this->id_bulletin ."', ";	
			}
			
			$transfert_origine="";
			if($this->expl_id){
				$rqt = "SELECT id_transfert FROM transferts, transferts_demande WHERE num_transfert=id_transfert and etat_transfert=0 AND num_expl='".$this->expl_id."' " ;
				$res = pmb_mysql_query( $rqt );
				if (!pmb_mysql_num_rows($res)){
					// pas de transfert en cours, on met à jour transfert_location_origine
					$transfert_origine= ", transfert_location_origine='".$this->location_id."', transfert_statut_origine='".$this->statut_id."', transfert_section_origine='".$this->section_id."' ";
				}
			}else{
				// en création
				$transfert_origine= ", transfert_location_origine='".$this->location_id."', transfert_statut_origine='".$this->statut_id."', transfert_section_origine='".$this->section_id."' ";
			}
			if($this->expl_id){
				$audit=new audit();
				$audit->get_old_infos("SELECT expl_statut, expl_location, transfert_location_origine, transfert_statut_origine, transfert_section_origine, expl_owner FROM exemplaires WHERE expl_cb='".$this->cb."' ");
			} else {
			    $q.= "create_date = '" . date("Y-m-d H:i:s") . "', ";
			}
			
			$q.= "expl_typdoc = '".$this->typdoc_id."', ";
			$q.= "expl_cote = '".addslashes(trim($this->cote))."', ";
			$q.= "expl_section = '".$this->section_id."', ";
			$q.= "expl_statut =  '".$this->statut_id."', ";
			$q.= "expl_location = '".$this->location_id."' $transfert_origine , ";
			$q.= "expl_codestat = '".$this->codestat_id."', ";
			$q.= "expl_date_depot = '".$this->date_depot."', ";
			$q.= "expl_date_retour = '".$this->date_retour."', ";
			$q.= "expl_note = '".addslashes($this->note)."', ";
			$q.= "expl_prix = '".addslashes($this->prix)."', ";
			$q.= "expl_owner = '".$this->owner_id."', ";
			$q.= "expl_lastempr = '".$this->lastempr."', ";
			$q.= "last_loan_date = '".$this->last_loan_date."', ";
			$q.= "type_antivol = '".$this->type_antivol."', ";
			$q.= "expl_comment = '".addslashes($this->expl_comment)."', ";
			$q.= "expl_nbparts = '".$this->nbparts."', ";
			$q.= "expl_retloc = '".$this->expl_retloc."', ";
			$q.= "expl_ref_num= '".$this->ref_num."' ";
			
			if ($this->expl_id) {
				$q.= "where expl_id='".$this->expl_id."' ";
			}
			$r = pmb_mysql_query($q); 
			if ($r) {
				if(!$this->expl_id) {
					$this->expl_id = pmb_mysql_insert_id();
					audit::insert_creation (AUDIT_EXPL, $this->expl_id) ;
				} else{					
					$audit->get_new_infos("SELECT expl_statut, expl_location, transfert_location_origine, transfert_statut_origine, transfert_section_origine, expl_owner FROM exemplaires WHERE expl_cb='".$this->cb."' ");
					$audit->save_info_modif(AUDIT_EXPL, $this->expl_id ,"expl.class.php");
				}
				
				// traitement des concepts
				if($thesaurus_concepts_active == 1){
					$index_concept = new index_concept($this->expl_id, TYPE_EXPL);
					$index_concept->save();
				}
				
				//Insertion des champs personalisés
				$p_perso=new parametres_perso("expl");
				$p_perso->rec_fields_perso($this->expl_id);
				// Mise a jour de la table notices_mots_global_index
				
				//On teste ici les différentes notices associées à l'exemplaire
				//Notice de périodique
				if($this->id_bulletin_notice){
					notice::majNoticesMotsGlobalIndex($this->id_bulletin_notice,'expl');
				}
				//Notice du bulletin
				if($this->id_num_notice){
					notice::majNoticesMotsGlobalIndex($this->id_num_notice,'expl');
				}
				//Notice de monographie
				if($this->id_notice){
					notice::majNoticesMotsGlobalIndex($this->id_notice,'expl');
				}

			} else {
				$this->error=true;
			}
		} else {
			$this->error=true; 				
		}
		return !$this->error;
	}
	
	public static function gen_antivol_selector($type_antivol='') {
		global $msg;
		global $value_deflt_antivol;
		
		if ($type_antivol=="") $type_antivol=$value_deflt_antivol;
		$selector = "<select name='type_antivol' id='type_antivol'>
				<option value='0' ".($type_antivol == 0 ? "selected='selected'" : "").">".$msg["type_antivol_aucun"]."</option>
				<option value='1' ".($type_antivol == 1 ? "selected='selected'" : "").">".$msg["type_antivol_magnetique"]."</option>		
				<option value='2' ".($type_antivol == 2 ? "selected='selected'" : "").">".$msg["type_antivol_autre"]."</option>
			</select>";
		return $selector;
	}
	
	public function gen_cb() {
		$requete="DELETE from exemplaires_temp where sess not in (select SESSID from sessions)";
		pmb_mysql_query($requete);
		
		//Appel à la fonction de génération automatique de cb
		$code_exemplaire =init_gen_code_exemplaire($this->id_notice,$this->id_bulletin);
		do {
			$code_exemplaire = gen_code_exemplaire($this->id_notice,$this->id_bulletin,$code_exemplaire);
			$requete="select expl_cb from exemplaires WHERE expl_cb='$code_exemplaire'";
			$res0 = pmb_mysql_query($requete);
			$requete="select cb from exemplaires_temp WHERE cb='$code_exemplaire' AND sess <>'".SESSid."'";
			$res1 = pmb_mysql_query($requete);
		} while((pmb_mysql_num_rows($res0)||pmb_mysql_num_rows($res1)));
		 
		//Memorise dans temps le cb et la session pour le cas de multi utilisateur session
		$this->cb = $code_exemplaire;
		$requete="INSERT IGNORE INTO exemplaires_temp (cb ,sess) VALUES ('$this->cb','".SESSid."')";
		pmb_mysql_query($requete);
		return $code_exemplaire;
	}
	
	public function fill_form (&$form, $action) {
		global $dbh, $charset, $msg;
		global $pmb_antivol;
		global $antivol_form;
		global $option_num_auto;
		global $pmb_rfid_activate,$pmb_rfid_serveur_url;
		global $pmb_expl_show_dates, $pmb_expl_show_lastempr;
		global $thesaurus_concepts_active;
		global $expl_create_update_date_form, $expl_filing_return_date_form;
		
		if (isset($option_num_auto)) {
	  		$this->gen_cb();
		}
		
		//on compte le nombre de prets pour cet exemplaire
		if($this->expl_id) {
			$query = "select count(arc_expl_id) as nb_prets from pret_archive where arc_expl_id = ".$this->expl_id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$nb_prets = $row->nb_prets ;
			}else $nb_prets = 0;
			if($nb_prets){
				//dernière date de pret pour cet exemplaire
				$query = "select date_format(last_loan_date, '".$msg["format_date"]."') as date_last from exemplaires where expl_id = ".$this->expl_id;
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$expl_pret = pmb_mysql_fetch_object($result);
					$date_last = $expl_pret->date_last ;
					$info_nb_prets=str_replace("!!nb_prets!!",$nb_prets,$msg['expl_nbprets']);
					$query = "select count(pret_idexpl) ";
					$query .= "from pret, empr where pret_idexpl='".$this->expl_id."' and pret_idempr=id_empr ";
					$result = pmb_mysql_query($query);
					if ($result && pmb_mysql_result($result,0,0)) {
						$info_date_last = str_replace("!!date_last!!",$date_last,$msg['expl_lastpret_encours']);
					} else {
						$info_date_last = str_replace("!!date_last!!",$date_last,$msg['expl_lastpret_retour']);
					}
					print $info_nb_prets." ".$info_date_last;
				}
			}
		}
	
		$form = str_replace('!!action!!', $action, $form);
		if ($this->id_notice) {
			$form = str_replace('!!id!!', $this->id_notice, $form);
		} else {
			$form = str_replace('!!id!!', $this->id_bulletin, $form);
		}
		$form = str_replace('!!cb!!',   htmlentities($this->cb  , ENT_QUOTES, $charset), $form);
		$form = str_replace('!!nbparts!!',   htmlentities($this->nbparts  , ENT_QUOTES, $charset), $form);
		$form = str_replace('!!note!!', htmlentities($this->note, ENT_QUOTES, $charset), $form);
		$form = str_replace('!!comment!!', htmlentities($this->expl_comment, ENT_QUOTES, $charset), $form);
		if ($this->id_notice) {
			$form = str_replace('!!cote!!', htmlentities(prefill_cote($this->id_notice,$this->cote), ENT_QUOTES, $charset), $form);
		} else {
			$form = str_replace('!!cote!!', htmlentities(prefill_cote($this->id_bulletin_notice,$this->cote), ENT_QUOTES, $charset), $form);
		}
		if ($this->id_notice) {
			$form = str_replace('!!prix!!', htmlentities(prefill_prix($this->id_notice,$this->prix), ENT_QUOTES, $charset), $form);
		} else {
			$form = str_replace('!!prix!!', htmlentities($this->prix, ENT_QUOTES, $charset), $form);
		}
	
		// select "type document"
		$form = str_replace('!!type_doc!!', do_selector('docs_type', 'f_ex_typdoc', $this->typdoc_id), $form);		
	
		// select "section"
		$form = str_replace('!!section!!', $this->do_selector(), $form);
	
		// select "statut"
		$form = str_replace('!!statut!!', do_selector('docs_statut', 'f_ex_statut', $this->statut_id), $form);
	
		// select "localisation"
		//visibilité des exemplaires
		global $explr_visible_mod, $pmb_droits_explr_localises ;
		if ($pmb_droits_explr_localises) $where_clause_explr = "idlocation in (".$explr_visible_mod.") and";
		else $where_clause_explr = "";
		$form = str_replace('!!localisation!!', gen_liste ("select distinct idlocation, location_libelle from docs_location, docsloc_section where $where_clause_explr num_location=idlocation order by 2 ", "idlocation", "location_libelle", 'f_ex_location', "calcule_section(this);", $this->location_id, "", "","","",0), $form);
		
		// select "code statistique"
		$form = str_replace('!!codestat!!', do_selector('docs_codestat', 'f_ex_cstat', $this->codestat_id), $form);
	
		if ($pmb_antivol) {
			$antivol_form = str_replace('!!type_antivol!!', exemplaire::gen_antivol_selector($this->type_antivol), $antivol_form);
			$form = str_replace('!!antivol_form!!', $antivol_form, $form);
		} else {
			$form = str_replace('!!antivol_form!!', '', $form);
		}
		
		// select "owner"
		$form = str_replace('!!owner!!', do_selector('lenders', 'f_ex_owner', $this->owner_id), $form);
		
		//dates creation / modification
		if ($this->expl_id && ($pmb_expl_show_dates=='1' || $pmb_expl_show_dates=='3')) {
			$create_update_date_form = $expl_create_update_date_form;
			$create_update_date_form = str_replace('!!create_date!!',format_date($this->create_date),$create_update_date_form);
			$create_update_date_form = str_replace('!!update_date!!',format_date($this->update_date),$create_update_date_form);
			$form = str_replace('!!create_update_date_form!!',$create_update_date_form,$form);
		} else {
			$form = str_replace('!!create_update_date_form!!','',$form);
		}
		
		//dates dépôt / retour
		if ($this->expl_id && ($pmb_expl_show_dates=='2' || $pmb_expl_show_dates=='3')) {
			$filing_return_date_form = $expl_filing_return_date_form;
			$filing_return_date_form = str_replace('!!filing_date!!',format_date($this->date_depot),$filing_return_date_form);
			$filing_return_date_form = str_replace('!!return_date!!',format_date($this->date_retour),$filing_return_date_form);
			$form = str_replace('!!filing_return_date_form!!',$filing_return_date_form,$form);
		} else {
			$form = str_replace('!!filing_return_date_form!!','',$form);
		}
		
		// Indexation concept
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($this->expl_id, TYPE_EXPL);
			$form = str_replace('<!-- index_concept_form -->', $index_concept->get_form("expl"), $form);
		}
		
		$perso = '';
		$p_perso=new parametres_perso("expl");
		if (!$p_perso->no_special_fields) {
			$perso_=$p_perso->show_editable_fields($this->expl_id);
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				if(($i == count($perso_["FIELDS"])-1) && ($i%2 == 0)) $element_class = 'row';
				else $element_class = 'colonne2';
				$p=$perso_["FIELDS"][$i];
				$perso.="<div id='el9Child_".$p["ID"]."' class='".$element_class."' movable='yes' title=\"".htmlentities($p["TITRE"], ENT_QUOTES, $charset)."\">
							<label for='".$p["NAME"]."' class='etiquette'>".$p["TITRE"]." </label>".$p["COMMENT_DISPLAY"]."
							<div class='row'>".$p["AFF"]."</div>
						</div>\n";
			}	
			$perso=$perso_["CHECK_SCRIPTS"]."\n".$perso;
		} else {
			$perso="\n<script>function check_form() { return true; }</script>\n";
		}
		$form = str_replace("!!champs_perso!!",$perso,$form);
		
		if($this->id_bulletin) {
			// circulation des périodique
			$perio_circ_tpl="";
			$in_circ=0;
			if($this->expl_id){
				$req = "select * from serialcirc_expl where num_serialcirc_expl_id=".$this->expl_id;
				$res_in_circ = pmb_mysql_query($req);
				if(pmb_mysql_num_rows($res_in_circ)){
					$in_circ=1;
					$perio_circ_tpl="<label class='etiquette'>".$msg['serialcirc_expl_in_circ']."</label>";
				}
			}
			if(!$in_circ){
				$req = "select * from abts_abts, bulletins, serialcirc where abts_abts.num_notice =bulletin_notice and  bulletin_id=".$this->id_bulletin." and num_serialcirc_abt=abt_id order by abt_name";
				$res_circ = pmb_mysql_query($req);
				if($nb=pmb_mysql_num_rows($res_circ)){
					$perio_circ_tpl="<input type='checkbox' name='serial_circ_add' value='1'> ".$msg['serialcirc_add_expl'];
					if($nb>1){
						$perio_circ_tpl.="<select name='abt_id'>";
					}
					while($circ = pmb_mysql_fetch_object($res_circ)){
						if($nb==1){
							$perio_circ_tpl.="<input type='hidden' name='abt_id' value='".$circ->abt_id."' >";
							break;
						}
						$perio_circ_tpl.="<option value='".$circ->abt_id."'> ".htmlentities($circ->abt_name,ENT_QUOTES,$charset)."</option>";
					}
					if($nb>1){
						$perio_circ_tpl.="</select>";
					}
				}
			}
			$form = str_replace("!!perio_circ_tpl!!",$perio_circ_tpl,$form);
		} else {
			$form = str_replace("!!perio_circ_tpl!!",'',$form);
		}
		
		if ($pmb_rfid_activate==1 && $pmb_rfid_serveur_url) {
			$form = str_replace('!!questionrfid!!', "if(script_rfid_encode()==false) return false;", $form);
		} else {
			$form = str_replace('!!questionrfid!!', '', $form);
		}
		
		// boutons
		$modifier = "";
		$dupliquer = "";
		$supprimer = "";
		if ($this->explr_acces_autorise=="MODIF") {
			if($this->expl_id) {
				if($this->id_notice) {
					$delete_msg = $msg[314]." ?"; 
					$delete_action = "./catalog.php?categ=del_expl&id=".$this->id_notice."&cb=".urlencode($this->cb)."&expl_id=".$this->expl_id;
					$duplicate_action = "./catalog.php?categ=dupl_expl&id=".$this->id_notice."&cb=".urlencode($this->cb)."&expl_id=".$this->expl_id;
				} else {
					$delete_msg = $msg['confirm_suppr_serial_expl'];
					$delete_action = "./catalog.php?categ=serials&sub=bulletinage&action=expl_delete&bul_id=".$this->id_bulletin."&expl_id=".$this->expl_id;
					$duplicate_action = "./catalog.php?categ=serials&sub=bulletinage&action=dupl_expl&bul_id=".$this->id_bulletin."&expl_id=".$this->expl_id;
				}
				$supprimer = "
					<script type=\"text/javascript\">
						function confirm_delete() {
							result = confirm(\"".$delete_msg."\");
							if(result) document.location = \"".$delete_action."\";
							else unload_on();
						}
					</script>
					<input type='button' class='bouton' value=\"${msg['63']}\" name='del_ex' id='del_ex' onClick=\"unload_off();confirm_delete();\" />";
				$dupliquer = "&nbsp;<input type='button' class='bouton' value=\"".$msg['dupl_expl_bt']."\" name='dupl_ex' id='dupl_ex' onClick=\"unload_off();document.location='".$duplicate_action."' ; \" />";
			}
			$modifier = "<input type='submit' class='bouton' value=' $msg[77] ' onClick=\"unload_off();return test_form(this.form);\" />";
		}
		
		$form = str_replace('!!supprimer!!', $supprimer, $form);
		$form = str_replace('!!dupliquer!!', $dupliquer, $form);
		$form = str_replace('!!modifier!!', $modifier, $form);
				
		if($this->id_notice) {
			//Remplissage ajax de la cote
			global $pmb_prefill_cote_ajax;
			if($pmb_prefill_cote_ajax)
				$expl_ajax_cote=" completion='expl_cote' listfield='".$this->ajax_cote_fields.",f_ex_cb,f_ex_typdoc,f_ex_location,f_ex_owner,f_ex_statut,f_ex_cstat".($pmb_antivol>0 ? ",type_antivol":"")."' ";
			else $expl_ajax_cote="";
			$form = str_replace("!!expl_ajax_cote!!",$expl_ajax_cote,$form);
		}
		
		// zone du dernier emrunteur
		$last_pret = "";
		if ($pmb_expl_show_lastempr && $this->lastempr) {
			$lastempr = new emprunteur($this->lastempr, '', FALSE, 0) ;
			$last_pret = "<hr /><div class='row'><b>$msg[expl_lastempr] </b>";
			$link = "<a href='./circ.php?categ=pret&form_cb=".rawurlencode($lastempr->cb)."'>";
			$last_pret .= $link.$lastempr->prenom.' '.$lastempr->nom.' ('.$lastempr->cb.')</a>';
			$last_pret .= "</div>";
		}
		
		// zone de l'emprunteur
		$query = "select empr_cb, empr_nom, empr_prenom, ";
		$query .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
		$query .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
		$query .= " IF(pret_retour>sysdate(),0,1) as retard " ;
		$query .= " from pret, empr where pret_idexpl='".$this->expl_id."' and pret_idempr=id_empr ";
		$result = pmb_mysql_query($query, $dbh);
		if (pmb_mysql_num_rows($result)) {
			$pret = pmb_mysql_fetch_object($result);
			$last_pret .= "<hr /><div class='row'><b>$msg[380]</b> ";
			$link = "<a href='./circ.php?categ=pret&form_cb=".rawurlencode($pret->empr_cb)."'>";
			$last_pret .= $link.$pret->empr_prenom.' '.$pret->empr_nom.' ('.$pret->empr_cb.')</a>';
			$last_pret .= "&nbsp;${msg[381]}&nbsp;".$pret->aff_pret_date;
			$last_pret .= ".&nbsp;${msg[358]}&nbsp;".$pret->aff_pret_retour.".";
			$last_pret .= "</div>";
		}
		$form = $form.$last_pret;
	}
	
	public function expl_form ($action='', $annuler='') {
		global $expl_form;
		global $msg, $pmb_type_audit;
		
		if ($action && $this->id_notice) {
			$action .= '&id='.$this->id_notice.'&org_cb='.urlencode($this->cb);
		}
		if ($action && $this->id_bulletin) {
			$action .= '&expl_bulletin='.$this->id_bulletin.'&org_cb='.urlencode($this->cb);
		}
		if($this->id_bulletin) {
			$expl_form = str_replace('!!grid_type!!', 'expl_bulletin', $expl_form);
		} else {
			$expl_form = str_replace('!!grid_type!!', 'expl', $expl_form);
		}
		$this->fill_form ($expl_form, $action);
		
		if ($pmb_type_audit && $this->expl_id) $link_audit =  audit::get_dialog_button($this->expl_id, 2);
				else $link_audit = "" ;
	
		$expl_form = str_replace('!!link_audit!!', $link_audit, $expl_form);
		$expl_form = str_replace('!!id_form!!', md5(microtime()), $expl_form);
		
		// affichage
		return $expl_form;
	}
	
	public function zexpl_form($action) {	
		global $expl_form;
		
		$this->fill_form ($expl_form, $action);
	
		$expl_form = str_replace('!!supprimer!!', "", $expl_form);
		$expl_form = str_replace('!!link_audit!!', "", $expl_form);
	
		// affichage
		print "<span class='zexpl_form'>".pmb_bidi($expl_form)."</span>";
	}
	
	// ----------------------------------------------------------------------------
	//	fonction do_selector qui génère des combo_box avec tout ce qu'il faut
	// ----------------------------------------------------------------------------
	public function do_selector($reset_add_idem = false, $selected='') {	
		global $dbh;
	 	global $charset;
	 	global $msg;
		global $deflt_docs_section;
		global $deflt_docs_location;
		
		if (!$this->section_id) $this->section_id=$deflt_docs_section ;
		if (!$this->location_id) $this->location_id=$deflt_docs_location;
		
		$selector = '';
		$rqtloc = "SELECT idlocation FROM docs_location order by location_libelle";
		$resloc = pmb_mysql_query($rqtloc, $dbh);
		while ($loc=pmb_mysql_fetch_object($resloc)) {
			$requete = "SELECT idsection, section_libelle FROM docs_section, docsloc_section where idsection=num_section and num_location='$loc->idlocation' order by section_libelle";
			$result = pmb_mysql_query($requete, $dbh);
			$nbr_lignes = pmb_mysql_num_rows($result);
			if ($nbr_lignes) {			
				if ($loc->idlocation==$this->location_id) $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:block\">\r\n";
				else $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:none\">\r\n";
				$selector .= "<select name='f_ex_section".$loc->idlocation."' id='f_ex_section".$loc->idlocation."'>";
				if ($reset_add_idem) {
					if(!$selected) {
						$selector.= "<option value='0' SELECTED>".$msg["reset_same_section"]."</option>";
					}else { 
						$selector.= "<option value='0' >".$msg["reset_same_section"]."</option>";
					}	
				}
				while ($line = pmb_mysql_fetch_row($result)) {
					$selector .= "<option value='$line[0]'";
					if (!$reset_add_idem) {
						$selector.= ($line[0] == $this->section_id ? ' SELECTED' : '');
					}else{
						$selector.= ($line[0] == $selected ? ' SELECTED' : '');
					}
		 			$selector.= '>'.htmlentities($line[1],ENT_QUOTES, $charset).'</option>';
				}                                         
				$selector.= '</select></div>';
				$this->ajax_cote_fields .= ($this->ajax_cote_fields != '' ? ",f_ex_section".$loc->idlocation : "f_ex_section".$loc->idlocation);
			}
		}
		return $selector;                         
	}                                                 
	 
	
	// ---------------------------------------------------------------
	//		import() : import d'un exemplaire 
	// ---------------------------------------------------------------
	// fonction d'import d'exemplaire (membre de la classe 'exemplaire');
	public function import($data) {                          
		global $msg;                              
	                                                  
		// cette méthode prend en entrée un tableau constitué des informations exemplaires suivantes :
		//	$data['cb'] 	                  
		//	$data['notice']
		//  $data['bulletin']                   
		//	$data['typdoc']
		//	$data['cote']                     
		//	$data['section']                  
		//	$data['statut']                   
		//	$data['location']                 
		//	$data['codestat']                 
		//	$data['creation']                 
		//	$data['modif']                    
		//	$data['note']                     
		//	$data['prix']                     
		//	$data['expl_owner']               
		//	$data['cote_mandatory'] cote obligatoire = 1, non obligatoire = 0
		//	$data['quoi_faire'] que faire de cet exemplaire :
		//		0 : supprimer, 1 ou vide : Mettre à jour ou ajouter, 2 : ajouter si possible, sinon rien.
	                                                  
		global $dbh;                              
	                                                  
		// check sur le type de  la variable passée en paramètre
		if(!sizeof($data) || !is_array($data)) {  
			// si ce n'est pas un tableau ou un tableau vide, on retourne 0
			$sql_log = pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".$msg[544]."') ") ;
			return 0;                         
			}                                 
	                                                  
		if ($data['quoi_faire']=="") $data['quoi_faire']="2" ;
		if ((string)$data['quoi_faire']=="0") {
			$sql_del = "delete from exemplaires where expl_cb='".addslashes($data['cb'])."' " ;
			pmb_mysql_query($sql_del) ;
			return -1 ;
			}
			                                  		                                  
		// check sur les éléments du tableau (cb, cote, notice, typdoc, section, statut, location, codestat, owner sont requis).
		if(!isset(static::$long_maxi_cb)) {
			static::$long_maxi_cb = pmb_mysql_field_len(pmb_mysql_query("SELECT expl_cb FROM exemplaires limit 1"),0);
		}
		$data['cb'] = rtrim(substr(trim($data['cb']),0,static::$long_maxi_cb));
		if(!isset(static::$long_maxi_cote)) {
			static::$long_maxi_cote = pmb_mysql_field_len(pmb_mysql_query("SELECT expl_cote FROM exemplaires limit 1"),0); 
		}
		$data['cote'] = rtrim(substr(trim($data['cote']),0,static::$long_maxi_cote));
		if(!isset(static::$long_maxi_prix)) {
			static::$long_maxi_prix = pmb_mysql_field_len(pmb_mysql_query("SELECT expl_prix FROM exemplaires limit 1"),0);
		}
		$data['prix'] = rtrim(substr(trim($data['prix']),0,static::$long_maxi_prix));
	                                                  
		if ($data['expl_owner']=="") {
			$sql_log = pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', 'No lender given') ") ;
			return 0;                         
			}                                 
		
		if($data['cb']=="") {                     
			$sql_log = pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".$msg[545]."') ") ;
			return 0;                         
			}                                 
		
		if ($data['cote']=="") {                  
			if ($data['cote_mandatory']==1) { 
				$sql_log = pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".$msg[546]."') ") ;
				return 0;                 
				} else {                  
					$sql_log = pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".$msg[567]."') ") ;
					}                 
			}                                 
		
		if($data['notice']==0) {
			if ($data['bulletin']==0) {                  
				$sql_log = pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".$msg[547]."') ") ;
				return 0;                         
			}                                 
		}
		
		if($data['typdoc']==0) {                  
			$sql_log = pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".$msg[548]."') ") ;
			return 0;                         
			}                                 
		if($data['section']==0) {                 
			$sql_log = pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".$msg[549]."') ") ;
			return 0;             
			}                                 
		if($data['statut']==0) {                  
			$sql_log = pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".$msg[550]."') ") ;
			return 0;                         
			}                                 
		if($data['location']==0) {                
			$sql_log = pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".$msg[551]."') ") ;
			return 0;                         
			}                                 
		if($data['codestat']==0) {                
			$sql_log = pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".$msg[552]."') ") ;
			return 0;                         
			}                                 
		if($data['type_antivol']=="") {                
			$data['type_antivol']="1";
			} 
		// préparation de la requête              
		$key0 = addslashes($data['cb']);          
		$key1 = addslashes($data['cote']);        
		                                          
		/* vérification que l'exemplaire existe ou pas */
		$exe = new stdClass();
		$query = "SELECT expl_id FROM exemplaires WHERE expl_cb='${key0}' LIMIT 1 ";
		$result = @pmb_mysql_query($query, $dbh);     
		if(!$result) die("can't SELECT exemplaires ".$query);
		if(pmb_mysql_num_rows($result)) $exe  = pmb_mysql_fetch_object($result);
	                                                  
	    if (!$data['date_depot']) $data['date_depot']="sysdate()" ; else $data['date_depot']="'".$data['date_depot']."'" ;                   
		if (!$data['date_retour']) $data['date_retour']="sysdate()" ; else $data['date_retour']="'".$data['date_retour']."'" ;                   
	                                                  
		// l'exemplaire existe et on ne pouvait que l'ajouter, on retourne l'ID 
		if ($exe->expl_id!="" && $data['quoi_faire']=="2") {
			$sql_log = pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".$msg[553].$data['cb']."') ") ;
			return $exe->expl_id;
			}                                 
		
		// l'exemplaire existe et on doit le mettre à jour
		if ($exe->expl_id!="" && $data['quoi_faire']=="1") {
			$sql_a_faire = "update exemplaires SET " ;
			$sql_a_faire_suite = " where expl_cb='".addslashes($data['cb'])."' " ;
			}
		
		// l'exemplaire n'existe pas : on doit le créer
		if ($exe->expl_id=="") {
			$sql_a_faire = "insert into exemplaires SET " ;
			$sql_a_faire_suite = "" ;
			}
		
		$query  = $sql_a_faire ;
		$query .= "expl_cb='".$key0."', ";        
		$query .= "expl_notice='".$data['notice']."', ";
		$query .= "expl_bulletin='".$data['bulletin']."', ";
		$query .= "expl_typdoc='".$data['typdoc']."', ";
		$query .= "expl_cote=trim('".$key1."'), ";      
		$query .= "expl_section='".$data['section']."', ";
		$query .= "expl_statut='".$data['statut']."', ";
		$query .= "expl_location='".$data['location']."', ";
		$query .= "expl_codestat='".$data['codestat']."', ";
		$query .= "expl_note='".addslashes($data['note'])."', ";
		$query .= "expl_comment='".addslashes($data['comment'])."', ";
		$query .= "expl_prix='".addslashes($data['prix'])."', ";
		$query .= "expl_owner='".$data['expl_owner']."', ";      
		$query .= "expl_date_depot=".$data['date_depot'].", ";
		$query .= "expl_date_retour=".$data['date_retour'].", ";
		
		$query .= "transfert_location_origine = ".$data['location'].", ";
		$query .= "transfert_statut_origine=".$data['statut'].", ";
		$query .= "transfert_section_origine=".$data['section'].", ";
		//$query .= "type_antivol=".$data['type_antivol'].", ";
		if($data['creation']){
			$query .= "create_date='".$data['creation']."'"; 
		}else{
			$query .= "create_date=sysdate() ";
		} 
	  
		$query .= $sql_a_faire_suite ;    
		$result = @pmb_mysql_query($query, $dbh);     
		if(!$result) die("can't INSERT into exemplaires ".$query);
	     
		if ($exe->expl_id="") {
			audit::insert_creation(AUDIT_EXPL,pmb_mysql_insert_id($dbh));
			$exe->expl_id = pmb_mysql_insert_id($dbh);
		} else {
			$sql_id = pmb_mysql_query("select expl_id from exemplaires where expl_cb='".addslashes($data['cb'])."' ") ;
			$exe  = pmb_mysql_fetch_object($sql_id);  
			audit::insert_modif(AUDIT_EXPL,$exe->expl_id); 
		}     
		
		// Imports > Exemplaires UNIMARC
		global $import_explajtEXPL, $import_expl_caddie_EXPL;
		if(!empty($import_explajtEXPL) && !empty($import_expl_caddie_EXPL)) {
			import_expl::add_object_caddie($exe->expl_id, 'EXPL', $import_expl_caddie_EXPL);
		}
		
		return $exe->expl_id;
	} /* fin méthode import */                
	
	// Suppression
	public static function del_expl($id=0) {
		global $dbh;
		global $explr_invisible, $explr_visible_unmod, $explr_visible_mod, $pmb_droits_explr_localises;
		global $pmb_archive_warehouse;
		
		$id += 0;
		$sql_pret = pmb_mysql_query("select 1 from pret where pret_idexpl ='$id' ") ;
		if (pmb_mysql_num_rows($sql_pret)) return 0 ;
		
		// visibilite de l'exemplaire
		if ($pmb_droits_explr_localises) {
			$query = "select expl_location from exemplaires where expl_id='".$id."'";
			$result = pmb_mysql_query($query,$dbh);
			$location_id = pmb_mysql_result($result,0,0);
			$tab_mod=explode(",",$explr_visible_mod);
			$as_modif = array_search($location_id,$tab_mod);
			if ($as_modif===false) return 0 ;
		}
		
		$requete = "select idcaddie FROM caddie where type='EXPL' ";
		$result = pmb_mysql_query($requete, $dbh);
		for($i=0;$i<pmb_mysql_num_rows($result);$i++) {
			$temp=pmb_mysql_fetch_object($result);
			$requete_suppr = "delete from caddie_content where caddie_id='".$temp->idcaddie."' and object_id='".$id."' ";
			$result_suppr = pmb_mysql_query($requete_suppr, $dbh);
			}
		audit::delete_audit (AUDIT_EXPL, $id) ;
		$p_perso=new parametres_perso("expl");
		$p_perso->delete_values($id);
		
		// nettoyage transfert
		$requete_suppr = "delete from transferts_demande where num_expl='$id'";
		$result_suppr = pmb_mysql_query($requete_suppr);
		
		// nettoyage circulation des périodiques
		serialcirc::delete_expl($id);
		
		//archivage
		if ($pmb_archive_warehouse) {
			static::save_to_agnostic_warehouse(array(0=>$id),$pmb_archive_warehouse);
		}
		
		// nettoyage doc. à ranger
		$requete_suppr = "delete from resa_ranger where resa_cb in (select expl_cb from exemplaires where expl_id='".$id."') ";
		$result_suppr = pmb_mysql_query($requete_suppr, $dbh);
		
		// nettoyage indexation concepts
		$index_concept = new index_concept($id, TYPE_EXPL);
		$index_concept->delete();
		
		$sql_del = pmb_mysql_query("delete from exemplaires where expl_id='$id' ") ;
		
		return 1 ;	
	}

	//sauvegarde un ensemble de notices dans un entrepot agnostique a partir d'un tableau d'ids d'exemplaires
	public static function save_to_agnostic_warehouse($expl_ids=array(),$source_id=0,$keep_expl=1) {
		global $base_path,$class_path,$include_path,$dbh;
		if (is_array($expl_ids) && count($expl_ids) && $source_id*1) {
			$export_params=array(
				'genere_lien'=>1,
				'notice_mere'=>1,
				'notice_fille'=>1,
				'mere'=>0,
				'fille'=>0,
				'bull_link'=>1,
				'perio_link'=>1,
				'art_link'=>0,
				'bulletinage'=>0,
				'notice_perio'=>0,
				'notice_art'=>0,
				'export_only_expl_ids'=> $expl_ids
			);
			$notice_ids=array();
			$bulletin_ids=array();
			$perio_ids=array();
			$q='select expl_notice,expl_bulletin,bulletin_notice from exemplaires left join bulletins on expl_bulletin=bulletin_id and expl_bulletin!=0 where expl_id in ('.implode(',',$expl_ids).')';
			$r=pmb_mysql_query($q,$dbh);
			if (pmb_mysql_num_rows($r)) {
				while($row=pmb_mysql_fetch_object($r)){
					if($row->expl_notice) $notice_ids[]=$row->expl_notice;
					if($row->expl_bulletin) $bulletin_ids[]=$row->expl_bulletin;
					if($row->bulletin_notice) $perio_ids[]=$row->bulletin_notice;
				}
			}
			if (count($notice_ids) || count($bulletin_ids)) {
				require_once($base_path."/admin/connecteurs/in/agnostic/agnostic.class.php");
				$conn=new agnostic($base_path.'/admin/connecteurs/in/agnostic');
				$source_params = $conn->get_source_params($source_id);
				$export_params['docnum']=1;
				$export_params['docnum_rep']=$source_params['REP_UPLOAD'];
			}
			if (count($notice_ids)) {
				$notice_ids=array_unique($notice_ids);
				$e=new export($notice_ids);
				$records=array();
				do{
					$nn = $e->get_next_notice('',array(),array(),$keep_expl,$export_params);
					if ($e->notice) $records[] = $e->xml_array;
				} while($nn);
				
				$conn->rec_records_from_xml_array($records,$source_id);
			}
			if (count($bulletin_ids)) {
				$bulletin_ids=array_unique($bulletin_ids);
				$perio_ids=array_unique($perio_ids);
				$e=new export($perio_ids);
				$e->expl_bulletin_a_exporter=$bulletin_ids;
				$records=array();
				do{
					$nn = $e->get_next_bulletin('',array(),array(),$keep_expl,$export_params);
					if ($e->notice) $records[] = $e->xml_array;
				} while($nn);
				$conn->rec_records_from_xml_array($records,$source_id);
			}
		}
	}

	/**
	 * Fonction de purge des exemplaires fantomes (appelée en fin de transfert)
	 * @param int $expl_id Id de l'exemplaire à tester
	 * @return boolean
	 */
	public static function purge_ghost($expl_id){
		global $dbh;
		$rqt = "select expl_ref_num from exemplaires where expl_id = ".$expl_id." ;";
		$result = pmb_mysql_query($rqt, $dbh);
		if($result){
			$parent_id = pmb_mysql_fetch_object($result);
			//Il s'agit d'un exemplaire fantome
			if($parent_id->expl_ref_num != 0){
				self::del_expl($expl_id);
				return true;
			}
			return false;
		}
		return false;
	}
		
	/**
	 * Fonction de mise à jour du code-barres de l'exemplaire (avec tests)
	 * @param string $old_cb Ancien code-barres 
	 * @param string $new_cb Nouveau code-barres 
	 * @return int Retourne un entier correspondant aux différents cas d'erreurs/réussite
	 */
	public static function update_cb($old_cb, $new_cb){
		global $dbh;
		if(SESSrights & (CATALOGAGE_AUTH + CATAL_MODIF_CB_EXPL_AUTH)){
			$requete="select expl_cb from exemplaires WHERE expl_cb='".$new_cb."'";
			$result = pmb_mysql_query($requete,$dbh);
			
			$requete="select cb from exemplaires_temp WHERE cb='".$new_cb."' AND sess <>'".SESSid."'";
			$result_tempo = pmb_mysql_query($requete,$dbh);
			
			if(!pmb_mysql_num_rows($result) && !pmb_mysql_num_rows($result_tempo)){//Code-barres non existant en base
				$requete = "update exemplaires set expl_cb = '".$new_cb."' where expl_cb='".$old_cb."'";
				$result = pmb_mysql_query($requete, $dbh);
				if($result){ //La requete de mise à jour a réussi
					return 1;
				}
				return 2;
			}
			//Le code-barres existe déjà en base
			return 0;
		}
		return 3;
	}
	
	public function get_notice_title() {
		$title = '';
		if($this->id_bulletin) {
			if($this->id_bulletin_notice) {
				$title .= notice::get_notice_title($this->id_bulletin_notice);
			} else {
				$title .= notice::get_notice_title($this->id_num_notice);
			}
		} else {
			$title .= notice::get_notice_title($this->id_notice);
		}
		return $title;
	}
	
	//renvoi le no d'exemplaire pour le tableau avec ou sans lien
	public static function get_cb_link($cb_expl) {
		global $base_path;
		
		$des_expl = "";
		if (SESSrights & CIRCULATION_AUTH) {
			$des_expl .= "<a href='".$base_path."/circ.php?categ=visu_ex&form_cb_expl=" . $cb_expl . "'>";
			$des_expl .= $cb_expl;
			$des_expl .= "</a>";
		} else {
			$des_expl .= $cb_expl;
		}
		return $des_expl;
	}
	
	public static function is_digital($id){
	    if(!isset(static::$digital_ids[$id])){
	        $id=intval($id);
	        $query = "select pnb_order_expl_num from pnb_orders_expl where pnb_order_expl_num =".$id;
	        $result = pmb_mysql_query($query);
	        if(pmb_mysql_num_rows($result)){
	            static::$digital_ids[$id] = true;
	        }else{
	            static::$digital_ids[$id] = false;
	        }
	    }
	    return static::$digital_ids[$id];
	}
	
	// récupère l'id d'un exemplaire d'après son code barre
	public static function get_expl_id_from_cb($cb) {
		if (!$cb) return FALSE;
		$query = "select expl_id as id from exemplaires where expl_cb='".$cb."' limit 1";
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, '0', 'id');
	}
	
	/**
	 * Définition du type de support par défaut
	 */
	protected function set_deflt_typdoc_id() {
		if ($this->typdoc_id) {
			return;
		}
		if ($this->id_bulletin) {
			global $deflt_serials_docs_type;
			$this->typdoc_id = $deflt_serials_docs_type;
			return;
		}
		if ($this->id_notice) {
			$notice = notice::get_notice($this->id_notice);
			switch ($notice->biblio_level) {
				case 's' :
				case 'b' :
					global $deflt_serials_docs_type;
					$this->typdoc_id = $deflt_serials_docs_type;
					break;
				default :
					global $deflt_docs_type;
					$this->typdoc_id = $deflt_docs_type;
					break;
			}
		}
		return;
	}
	
	// Donne l'id de la notice par son identifiant d'expl
	public static function get_expl_notice_from_id($expl_id=0) {
		$expl_id += 0;
		$query = "select expl_notice, expl_bulletin from exemplaires where expl_id = ".$expl_id;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		if($row->expl_notice) {
			return $row->expl_notice;
		} else {
			$query = "select num_notice from bulletins where bulletin_id = ".$row->expl_bulletin;
			$result = pmb_mysql_query($query);
			return pmb_mysql_result($result, 0, 'num_notice');				
		}
	}
	
	// Donne l'id du bulletin par son identifiant d'expl
	public static function get_expl_bulletin_from_id($expl_id=0) {
		$expl_id += 0;
		$query = "select expl_bulletin from exemplaires where expl_id = ".$expl_id;
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, 0, 'expl_bulletin');
	}
	
	public static function get_nb_prets_from_id($expl_id=0) {
		$nb_prets = 0;
		$expl_id += 0;
		$query = "select count(arc_expl_id) as nb_prets from pret_archive where arc_expl_id = ".$expl_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$nb_prets = $row->nb_prets ;
		}
		return $nb_prets;
	}
} # fin de la classe exemplaire                   
                                                  
} # fin de définition                             
