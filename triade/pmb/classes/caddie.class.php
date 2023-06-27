<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie.class.php,v 1.105 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des paniers

require_once ($class_path."/caddie_root.class.php");
require_once ($class_path."/expl.class.php");
require_once ($class_path."/audit.class.php");
require_once ($class_path."/classementGen.class.php");
require_once ($include_path."/templates/cart.tpl.php");

require_once ($class_path."/mono_display.class.php");
require_once ($class_path."/serial_display.class.php");
require_once ($class_path."/sort.class.php");
require_once ($class_path."/notice.class.php");
require_once ($class_path."/progress_bar.class.php");
require_once ($class_path."/export_param.class.php");
require_once($class_path."/notice_relations_collection.class.php");
require_once($class_path.'/event/events/event_users_group.class.php');
require_once ($class_path."/list/caddie/list_caddie_ui.class.php");
require_once ($class_path."/elements_list/elements_records_caddie_list_ui.class.php");
require_once ($class_path."/event/events/event_display_overload.class.php");

class caddie extends caddie_root {
	// propriétés
	public $idcaddie ;
	public $type = ''			;	// Type de panier (EXPL = exemplaire, BULL = bulletin, NOTI = notice)
	public $item_base = 0		;	// nombre d'enregistrements issus/connus dans la base PMB dans le panier
	public $nb_item_base_pointe = 0	;	// nombre d'enregistrements pointés issus/connus dans la base PMB dans le panier
	public $nb_item_blob = 0 		;	// nombre d'enregistrements inconnus dans la base PMB dans le panier
	public $nb_item_blob_pointe = 0 	;	// nombre d'enregistrements pointés inconnus dans la base PMB dans le panier
	public static $table_name = 'caddie';
	public static $field_name = 'idcaddie';
	public static $table_content_name = 'caddie_content';
	public static $field_content_name = 'caddie_id';
	
	// ---------------------------------------------------------------
	//		caddie($id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($caddie_id=0) {
		$this->idcaddie = $caddie_id+0;
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	//		getData() : récupération infos caddie
	// ---------------------------------------------------------------
	protected function getData() {
		global $dbh;
		parent::getData();
		$this->type = '';
		if($this->idcaddie) {
			$requete = "SELECT * FROM caddie WHERE idcaddie='$this->idcaddie' ";
			$result = @pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);
				$this->idcaddie = $temp->idcaddie;
				$this->type = $temp->type;
				$this->name = $temp->name;
				$this->comment = $temp->comment;
				$this->autorisations = $temp->autorisations;
				$this->autorisations_all = $temp->autorisations_all;
				$this->classementGen = $temp->caddie_classement;
				$this->acces_rapide = $temp->acces_rapide;
				$this->favorite_color = $temp->favorite_color;
				$this->creation_user_name = $temp->creation_user_name;
				$this->creation_date = $temp->creation_date;
			
				//liaisons
				$req="SELECT idetagere, name FROM etagere_caddie JOIN etagere ON etagere_id=idetagere WHERE caddie_id='".$this->idcaddie."' GROUP BY idetagere";
				$res=pmb_mysql_query($req,$dbh);
				if($res && pmb_mysql_num_rows($res)){
					while ($ligne=pmb_mysql_fetch_object($res)){
						$this->liaisons["etageres"][]=array("id"=>$ligne->idetagere,"lib"=>$ligne->name);
					}
				}
				$req="SELECT id_bannette, nom_bannette FROM bannettes WHERE num_panier='".$this->idcaddie."' GROUP BY id_bannette";
				$res=pmb_mysql_query($req,$dbh);
				if($res && pmb_mysql_num_rows($res)){
					while ($ligne=pmb_mysql_fetch_object($res)){
						$this->liaisons["bannettes"][]=array("id"=>$ligne->id_bannette,"lib"=>$ligne->nom_bannette);
					}
				}
				$req="SELECT id_rss_flux, nom_rss_flux FROM rss_flux_content JOIN rss_flux ON num_rss_flux=id_rss_flux WHERE num_contenant='".$this->idcaddie."' AND type_contenant='CAD' GROUP BY id_rss_flux";
				$res=pmb_mysql_query($req,$dbh);
				if($res && pmb_mysql_num_rows($res)){
					while ($ligne=pmb_mysql_fetch_object($res)){
						$this->liaisons["rss_flux"][]=array("id"=>$ligne->id_rss_flux,"lib"=>$ligne->nom_rss_flux);
					}
				}
				$req="SELECT connector_out_set_id, connector_out_set_caption FROM connectors_out_sets WHERE connector_out_set_config REGEXP '\{s:16:\"included_caddies\";a:[0-9]+:\{i:0;[i:0-9;]*i:".$this->idcaddie.";[i:0-9;]*\}'";
				$res=pmb_mysql_query($req,$dbh);
				if($res && pmb_mysql_num_rows($res)){
					while ($ligne=pmb_mysql_fetch_object($res)){
						$this->liaisons["connectors"][]=array("id"=>$ligne->connector_out_set_id,"lib"=>$ligne->connector_out_set_caption);
					}
				}
			}
			$this->compte_items();
		}
	}
	
	protected function get_template_form() {
		global $cart_form;
		return $cart_form;
	}
	
	protected function get_warning_delete() {
		global $msg;
		
		$message_delete_warning = $msg["caddie_used_in_warning"];
		foreach ($this->liaisons as $type => $values){
			if(count($values)){
				switch ($type){
					case "etageres":
						$message_delete_warning .= "\\n- ".$msg["etagere_menu"];
						break;
					case "bannettes":
						$message_delete_warning .= "\\n- ".$msg["dsi_menu_bannettes"];
						break;
					case "rss_flux":
						$message_delete_warning .= "\\n- ".$msg["dsi_menu_flux"];
						break;
					case "connectors":
						$message_delete_warning .= "\\n- ".$msg["admin_connecteurs_sets"];
						break;
					default://On ne doit pas passer par là
						break;//On sort aussi du foreach
				}
			}
		}
		$message_delete_warning .= "\\n";
		return $message_delete_warning;
	}
	
	public static function get_types() {
		return array('NOTI', 'EXPL', 'BULL');
	}
	
	// formulaire
	public function get_form($form_action="", $form_cancel="") {
		global $msg, $charset;
		global $liaison_tpl;
		global $current_print;
		
		$form = parent::get_form($form_action, $form_cancel);
		$form=str_replace('!!cart_type!!', $this->get_type_form(), $form);
		if ($this->get_idcaddie()) {
			$info_liaisons = $this->get_links_form();
			$message_delete_warning = "";
			if($info_liaisons){
				$liaison_tpl=str_replace("<!-- info_liaisons -->",$info_liaisons,$liaison_tpl);
				$form = str_replace('<!-- liaisons -->', $liaison_tpl, $form);
				$message_delete_warning = $this->get_warning_delete();
			}
			$button_delete = "<input type='button' class='bouton' value=' ".$msg['supprimer']." ' onClick=\"javascript:confirmation_delete(".$this->get_idcaddie().",'".htmlentities(addslashes($this->name),ENT_QUOTES, $charset)."')\" />";
			$form = str_replace('!!button_delete!!', $button_delete, $form);
			$form .= confirmation_delete("./catalog.php?categ=caddie&action=del_cart&idcaddie=",$message_delete_warning);
		} else {
			$form = str_replace('!!button_delete!!', '', $form);
		}
		return $form;
	}
	
	// Liaisons pour le panier
	protected function get_links_form() {
		global $msg, $charset;
		global $dsi_active;
			
		$links_form = "";
		$end = false;
		foreach ( $this->liaisons as $type => $values ) {
			if (count ( $values )) {
				$links_form .= "<br>";
				switch ($type) {
					case "etageres" :
						$links_form .= "<div class='row'>
                                            <label for='' class='etiquette'>" . $msg ["etagere_menu"] . "</label>
                                        </div>
                                        <div class='row'>";
						$link = "<a href='./catalog.php?categ=etagere&sub=constitution&action=edit_etagere&idetagere=!!id!!'>!!name!!</a>";
						break;
					case "bannettes" :
						$links_form .= "<div class='row'>
                                            <label for='' class='etiquette'>" . $msg ["dsi_menu_bannettes"] . "</label>
                                        </div>
                                        <div class='row'>";
						if ($dsi_active && (SESSrights & DSI_AUTH)) {
							$link = "<a href='./dsi.php?categ=bannettes&sub=pro&id_bannette=!!id!!&suite=acces'>!!name!!</a>";
						} else {
							$link = "!!name!!";
						}
						break;
					case "rss_flux" :
						$links_form .= "<div class='row'>
                                            <label for='' class='etiquette'>" . $msg ["dsi_menu_flux"] . "</label>
                                        </div>
                                        <div class='row'>";
						if ($dsi_active && (SESSrights & DSI_AUTH)) {
							$link = "<a href='./dsi.php?categ=fluxrss&id_rss_flux=!!id!!&suite=acces'>!!name!!</a>";
						} else {
							$link = "!!name!!";
						}
						break;
					case "connectors" :
						$links_form .= "<div class='row'>
                                           <label for='' class='etiquette'>" . $msg ["admin_connecteurs_sets"] . "</label>
                                       </div>
                                       <div class='row'>";
						if (SESSrights & ADMINISTRATION_AUTH) {
							$link = "<a href='./admin.php?categ=connecteurs&sub=out_sets&action=edit&id=!!id!!'>!!name!!</a>";
						} else {
							$link = "!!name!!";
						}
						break;
					default : // On ne doit pas passer par là
						$links_form = "";
						//break 2; // On sort aussi du foreach
						$end = true;
						break;
				}
				if($end) break;
				foreach ( $values as $infos ) {
				    $links_form .= "<div class='row caddie_links'>";
					$links_form .= str_replace ( array (
							"!!id!!",
							"!!name!!"
					), array (
							$infos ["id"],
							htmlentities ( $infos ["lib"], ENT_QUOTES, $charset )
					), $link );
					$links_form .= "</div>";
				}
				$links_form .= "</div>";
			}
		}
		return $links_form;
	}
	
	public function set_properties_from_form() {
		global $cart_type;
		global $classementGen_caddie;

		parent::set_properties_from_form();
		if(!$this->idcaddie || ($this->idcaddie && !$this->nb_item && $cart_type)) {
			$this->type = $cart_type;
		}
		$this->classementGen = stripslashes($classementGen_caddie);
	}
	
	protected static function get_order_cart_list() {
		return " order by type, name, comment ";
	}
	
	static public function get_cart_data($temp) {
		global $dbh;
		
		$nb_item = 0 ;
		$nb_item_pointe = 0 ;
		$nb_item_base = 0 ;
		$nb_item_base_pointe = 0 ;
		$nb_item_blob = 0 ;
		$nb_item_blob_pointe = 0 ;
		$rqt_nb_item="select count(1) from caddie_content where caddie_id='".$temp->idcaddie."' ";
		$nb_item = pmb_mysql_result(pmb_mysql_query($rqt_nb_item, $dbh), 0, 0);
		$rqt_nb_item_pointe = "select count(1) from caddie_content where caddie_id='".$temp->idcaddie."' and (flag is not null and flag!='') ";
		$nb_item_pointe = pmb_mysql_result(pmb_mysql_query($rqt_nb_item_pointe, $dbh), 0, 0);
		$rqt_nb_item_base="select count(1) from caddie_content where caddie_id='".$temp->idcaddie."' and (content is null or content='') ";
		$nb_item_base = pmb_mysql_result(pmb_mysql_query($rqt_nb_item_base, $dbh), 0, 0);
		$rqt_nb_item_base_pointe="select count(1) from caddie_content where caddie_id='".$temp->idcaddie."' and (content is null or content='') and (flag is not null and flag!='') ";
		$nb_item_base_pointe = pmb_mysql_result(pmb_mysql_query($rqt_nb_item_base_pointe, $dbh), 0, 0);
		$nb_item_blob = $nb_item - $nb_item_base ;
		$nb_item_blob_pointe = $nb_item_pointe - $nb_item_base_pointe ;
		
		return array(
				'idcaddie' => $temp->idcaddie,
				'name' => $temp->name,
				'type' => $temp->type,
				'comment' => $temp->comment,
				'autorisations' => $temp->autorisations,
				'autorisations_all' => $temp->autorisations_all,
				'caddie_classement' => $temp->caddie_classement,
				'acces_rapide' => $temp->acces_rapide,
				'favorite_color' => $temp->favorite_color,
				'nb_item' => $nb_item,
				'nb_item_pointe' => $nb_item_pointe,
				'nb_item_base' => $nb_item_base,
				'nb_item_base_pointe' => $nb_item_base_pointe,
				'nb_item_blob' => $nb_item_blob,
				'nb_item_blob_pointe' => $nb_item_blob_pointe
		
		);
	}
	
	// liste des paniers disponibles
	static public function get_cart_list($restriction_panier="",$acces_rapide = 0) {
		return parent::get_cart_list($restriction_panier, $acces_rapide);
	}
	
	// création d'un panier vide
	public function create_cart() {
		$requete = "insert into caddie set name='".addslashes($this->name)."', type='".$this->type."', comment='".addslashes($this->comment)."', autorisations='".$this->autorisations."', autorisations_all='".$this->autorisations_all."', caddie_classement='".addslashes($this->classementGen)."', acces_rapide='".$this->acces_rapide."', favorite_color='".addslashes($this->favorite_color)."' ";
		$user = $this->get_info_user();
		if(is_object($user) && count($user)) {
			$requete .= ", creation_user_name='".addslashes($user->name)."', creation_date='".date("Y-m-d H:i:s")."'";
		}
		pmb_mysql_query($requete);
		$this->idcaddie = pmb_mysql_insert_id();
		$this->compte_items();
		return $this->idcaddie;
	}
	
	// sauvegarde du panier
	public function save_cart() {
		$query = "update caddie set name='".addslashes($this->name)."', type='".$this->type."', comment='".addslashes($this->comment)."', autorisations='".$this->autorisations."', autorisations_all='".$this->autorisations_all."', caddie_classement='".addslashes($this->classementGen)."', acces_rapide='".$this->acces_rapide."', favorite_color='".addslashes($this->favorite_color)."' where ".static::get_field_name()."='".$this->get_idcaddie()."'";
		$result = pmb_mysql_query($query);
		return true;
	}
	
	// ajout d'un item
	public function add_item($item=0, $object_type="NOTI", $bul_or_dep="") {
		// $bul_or_dep permet de choisir entre notice de dépouillement (DEP) 
		//   ou notice de bulletin (par défaut) lors de l'ajout d'un bulletin à un panier de notices
		
		global $dbh;
		
		if (!$item) return CADDIE_ITEM_NULL ;
		
		// les objets sont identiques
		if ($object_type==$this->type) {
			// rêgle : les caddies sont homogènes, on y stocke des objets de même type en fonction du type du caddie
			$requete_compte = "select count(1) from caddie_content where caddie_id='".$this->idcaddie."' AND object_id='".$item."' ";
			$result_compte = @pmb_mysql_query($requete_compte, $dbh);
			$deja_item=pmb_mysql_result($result_compte, 0, 0);
			if (!$deja_item) {
				$requete= "insert into caddie_content set caddie_id='".$this->idcaddie."', object_id='".$item."', content='' ";
				$result = @pmb_mysql_query($requete, $dbh);
			}
		} else {
			// Traitement des cas particuliers
			// panier d'exemplaires : 
			//		Notice reçue : 
			//			on stocke tous les exemplaires associés à la notice
			//				voir le pb de notice de dépouillement
			if ($this->type=="EXPL" && $object_type=="NOTI") { 
				$rqt_mono_serial_bull_analysis = "select niveau_biblio, niveau_hierar from notices where notice_id = '$item' ";
				$res_mono_serial_bull_analysis = pmb_mysql_query($rqt_mono_serial_bull_analysis, $dbh);
				$row_mono_serial_bull_analysis = pmb_mysql_fetch_object($res_mono_serial_bull_analysis);
				// monographie
				if ($row_mono_serial_bull_analysis->niveau_biblio=="m" && $row_mono_serial_bull_analysis->niveau_hierar=="0")
					$rqt_expl = "select expl_id from exemplaires where expl_notice='$item' ";
				// périodique : notice mère
				if ($row_mono_serial_bull_analysis->niveau_biblio=="s" && $row_mono_serial_bull_analysis->niveau_hierar=="1")
					$rqt_expl = "select expl_id from exemplaires, bulletins where bulletin_notice='$item' and expl_bulletin=bulletin_id ";
				// périodique : notice de dépouillement (analytique)
				if ($row_mono_serial_bull_analysis->niveau_biblio=="a" && $row_mono_serial_bull_analysis->niveau_hierar=="2")
					$rqt_expl = "select expl_id from exemplaires, analysis where analysis_notice='$item' and analysis_bulletin=expl_bulletin ";
				// bulletin : notice de bulletin
				if ($row_mono_serial_bull_analysis->niveau_biblio=="b" && $row_mono_serial_bull_analysis->niveau_hierar=="2")
					$rqt_expl = "select expl_id from exemplaires, bulletins where num_notice='$item' and bulletin_id=expl_bulletin ";
			}
			//		Bulletin reçu : 
			//			on stocke tous les exemplaires associés au bulletin
			if ($this->type=="EXPL" && $object_type=="BULL") {
				$rqt_expl = "select expl_id from exemplaires where expl_bulletin='$item' ";
			}
			
			// panier de notices :
			//		EXPL reçu : 
			//			on stocke la notice de l'exemplaire 
			//				voir le pb d'expl de bulletin
			if ($this->type=="NOTI" && $object_type=="EXPL") {
				$rqt_mono_bull = "select expl_notice, expl_bulletin from exemplaires where expl_id='$item' ";
				$res_mono_bull = pmb_mysql_query($rqt_mono_bull, $dbh);
				$row_mono_bull = pmb_mysql_fetch_object($res_mono_bull);
				// expl de monographie
				if ($row_mono_bull->expl_notice && !$row_mono_bull->expl_bulletin)
					$rqt_expl = "select expl_notice from exemplaires where expl_id='$item' ";
				// expl de bulletin
				if (!$row_mono_bull->expl_notice && $row_mono_bull->expl_bulletin)
					$rqt_expl = "select bulletin_notice from exemplaires, bulletins where expl_id='$item' and expl_bulletin=bulletin_id ";
			} 
			//		BULL reçu : 
			//			on stocke la notice du bulletin si existante
			//    ATTENTION: modif version 3.1.12: ajout de la notice de bulletin et non plus les notices de dépouillement
			if ($this->type=="NOTI" && $object_type=="BULL") {
				if ($bul_or_dep=="DEP") $rqt_expl = "select analysis_notice from analysis where analysis_bulletin='$item' ";
				else $rqt_expl = "select num_notice from bulletins where bulletin_id='$item' and num_notice!=0";
			} // fin if NOTI / BULL
			
			// panier de bulletins :
			//		EXPL reçu : 
			//			on stocke le bulletin de l'exemplaire 
			if ($this->type=="BULL" && $object_type=="EXPL") {
				$rqt_mono_bull = "select expl_notice, expl_bulletin from exemplaires where expl_id='$item' ";
				$res_mono_bull = pmb_mysql_query($rqt_mono_bull, $dbh);
				$row_mono_bull = pmb_mysql_fetch_object($res_mono_bull);
				// expl de monographie
				if ($row_mono_bull->expl_notice && !$row_mono_bull->expl_bulletin)
					return CADDIE_ITEM_IMPOSSIBLE_BULLETIN;
				// expl de bulletin
				if (!$row_mono_bull->expl_notice && $row_mono_bull->expl_bulletin)
					$rqt_expl = "select expl_bulletin from exemplaires where expl_id='$item' ";
			}
			//		NOTI reçue : 
			//			on stocke le bulletin associé à la notice chapeau reçue
			//			ou bien le bulletin contenant la notice de dépouillement reçue
			if ($this->type=="BULL" && $object_type=="NOTI") {
				$rqt_mono_serial_bull_analysis = "select niveau_biblio, niveau_hierar from notices where notice_id = '$item' ";
				$res_mono_serial_bull_analysis = pmb_mysql_query($rqt_mono_serial_bull_analysis, $dbh);
				$row_mono_serial_bull_analysis = pmb_mysql_fetch_object($res_mono_serial_bull_analysis);
				// monographie
				if ($row_mono_serial_bull_analysis->niveau_biblio=="m" && $row_mono_serial_bull_analysis->niveau_hierar=="0")
					return CADDIE_ITEM_IMPOSSIBLE_BULLETIN;
				// périodique : notice mère
				if ($row_mono_serial_bull_analysis->niveau_biblio=="s" && $row_mono_serial_bull_analysis->niveau_hierar=="1")
					$rqt_expl = "select bulletin_id from bulletins where bulletin_notice='$item' ";
				// périodique : notice de dépouillement (analytique)
				if ($row_mono_serial_bull_analysis->niveau_biblio=="a" && $row_mono_serial_bull_analysis->niveau_hierar=="2")
					$rqt_expl = "select analysis_bulletin from analysis where analysis_notice='$item' ";
				// bulletin : notice de bulletin
				if ($row_mono_serial_bull_analysis->niveau_biblio=="b" && $row_mono_serial_bull_analysis->niveau_hierar=="2")
					$rqt_expl = "select bulletin_id from bulletins where num_notice='$item' ";
			}
			if ($this->type=="EXPL" && $object_type=="EXPL") {
				$rqt_expl = "select expl_id from exemplaires where expl_id='$item' ";
			} // fin if NOTI / BULL
			
			if ($rqt_expl) {
				$res_expl = pmb_mysql_query($rqt_expl, $dbh);
				for($i=0;$i<pmb_mysql_num_rows($res_expl);$i++) {
					$row=pmb_mysql_fetch_row($res_expl);
					$requete_compte = "select count(1) from caddie_content where caddie_id='".$this->idcaddie."' AND object_id='".$row[0]."' ";
					$result_compte = @pmb_mysql_query($requete_compte, $dbh);
					$deja_item=pmb_mysql_result($result_compte, 0, 0);
					if (!$deja_item) {
						$requete= "insert into caddie_content set caddie_id='".$this->idcaddie."', object_id='".$row[0]."', content='' ";
						$result = @pmb_mysql_query($requete, $dbh);
					}
				} // fin for
			}
		} // fin else types différents
		return CADDIE_ITEM_OK ;
	}

	// ajout d'un item blob
	public function add_item_blob($blobobject=0, $blob_type="EXPL_CB") {
		global $dbh;
		
		if (!$blobobject) return CADDIE_ITEM_NULL ;
		
		$requete_compte = "select count(1) from caddie_content where caddie_id='".$this->idcaddie."' and content='".$blobobject."' and blob_type='".$blob_type."' ";
		$result_compte = @pmb_mysql_query($requete_compte, $dbh);
		$deja_item=pmb_mysql_result($result_compte, 0, 0);
		
		if (!$deja_item) {
			$requete= "insert into caddie_content set caddie_id='".$this->idcaddie."', object_id=0, content='".$blobobject."', blob_type='".$blob_type."' ";
			$result = pmb_mysql_query($requete, $dbh);
		}	
	}

	// suppression d'un item EXPL_CB
	public function del_item_blob($expl_cb="") {
		global $dbh;
		$requete = "delete FROM caddie_content where caddie_id='".$this->idcaddie."' and blob_type='EXPL_CB' and content='".$expl_cb."' ";
		$result = @pmb_mysql_query($requete, $dbh);
		$this->compte_items();
	}

	public function del_item_base($item=0,$forcage=array()) {
		global $dbh;
		
		if (!$item) return CADDIE_ITEM_NULL ;
		
		switch ($this->type) {
			case "EXPL" :
				if (!$this->verif_expl_item($item)) {
					if ($forcage['source_id']) {
						exemplaire::save_to_agnostic_warehouse(array(0=>$item),$forcage['source_id']);
					}
					if (exemplaire::del_expl($item)) {
						return CADDIE_ITEM_SUPPR_BASE_OK ;
					} else {
						return 0 ;
					}
				} else return CADDIE_ITEM_EXPL_PRET ;
				break ;
			case "BULL" :
				if (!$this->verif_bull_item($item,$forcage)) {
					// aucun prêt d'exemplaire de ce bulletin en cours, on supprime :
					$myBulletinage = new bulletinage($item);
					$myBulletinage->delete();	
					
					return CADDIE_ITEM_SUPPR_BASE_OK ;
				} else return CADDIE_ITEM_BULL_USED ;
				break ;
			case "NOTI" :
				if (!$this->verif_noti_item($item,$forcage)) {
					if ($forcage['source_id']) {
						notice::save_to_agnostic_warehouse(array(0=>$item),$forcage['source_id']);
					}
					$requete="SELECT niveau_biblio, niveau_hierar FROM notices WHERE notice_id='".$item."'";
					$res=pmb_mysql_query($requete, $dbh);
					if(pmb_mysql_num_rows($res) && (pmb_mysql_result($res,0,0) == "s") && (pmb_mysql_result($res,0,1) == "1")){
						$myBulletinage = new serial($item);
						$myBulletinage->serial_delete();
					}else{
						notice::del_notice($item);
					}
					return CADDIE_ITEM_SUPPR_BASE_OK ;
				} else return CADDIE_ITEM_NOTI_USED ;
				break ;
			}
						
		return CADDIE_ITEM_OK ;
	}

	// suppression d'un item de tous les caddies du même type le contenant
	public function del_item_all_caddies($item, $type) {
		global $dbh;
		$requete = "select idcaddie FROM caddie where type='".$type."' ";
		$result = pmb_mysql_query($requete, $dbh);
		for($i=0;$i<pmb_mysql_num_rows($result);$i++) {
			$temp=pmb_mysql_fetch_object($result);
			$requete_suppr = "delete from caddie_content where caddie_id='".$temp->idcaddie."' and object_id='".$item."' ";
			$result_suppr = pmb_mysql_query($requete_suppr, $dbh);
		}
	}

	public function del_item_flag($inconnu_aussi=1) {
		global $dbh;
		$requete = "delete FROM caddie_content where caddie_id='".$this->idcaddie."' and (flag is not null and flag!='') ";
		if (!$inconnu_aussi) $requete .= " and (content is null or content='') ";
		$result = @pmb_mysql_query($requete, $dbh);
		$this->compte_items();
	}

	public function del_item_no_flag($inconnu_aussi=1) {
		global $dbh;
		$requete = "delete FROM caddie_content where caddie_id='".$this->idcaddie."' and (flag is null or flag='') ";
		if (!$inconnu_aussi) $requete .= " and (content is null or content='') "; 
		$result = @pmb_mysql_query($requete, $dbh);
		$this->compte_items();
	}

	// Export des documents numérique d'un item 
	public function export_doc_num($item=0,$chemin) {
		global $dbh, $charset, $msg;
		
		$pattern_nom_fichier_doc_num="!!explnumid!!_!!idnotice!!_!!idbulletin!!_!!indicedocnum!!_!!nomdoc!!";
		
		if ($this->type=="NOTI") {
			$requete = "select explnum_id, explnum_notice as numnotice, explnum_bulletin, explnum_data, explnum_extfichier, explnum_nomfichier, length(explnum_data) as taille ";
			$requete .= " FROM explnum WHERE ";
			$requete .= " explnum_notice=$item ";
		} elseif ($this->type=="BULL") {
			$requete = "select explnum_id, bulletin_notice as numnotice, explnum_bulletin, explnum_data, explnum_extfichier, explnum_nomfichier, length(explnum_data) as taille ";
			$requete .= " FROM explnum JOIN bulletins on bulletin_id=explnum_bulletin WHERE ";
			$requete .= " explnum_bulletin=$item ";
		} else return; // pas encore de document numérique attaché à un exemplaire
		$requete .= " and ((explnum_data is not null and explnum_data!='') OR (explnum_nomfichier is not null and explnum_nomfichier!=''))";
	
		$result = pmb_mysql_query($requete, $dbh) or die(pmb_mysql_error()."<br />$requete");
		for($i=0;$i<pmb_mysql_num_rows($result);$i++) {
			$t=pmb_mysql_fetch_object($result);
			$t->explnum_id = str_pad ($t->explnum_id, 6, "0", STR_PAD_LEFT) ;
			$t->numnotice = str_pad ($t->numnotice, 6, "0", STR_PAD_LEFT) ;
			$t->explnum_bulletin = str_pad ($t->explnum_bulletin, 6, "0", STR_PAD_LEFT) ;
			$nomfic= $pattern_nom_fichier_doc_num;
			$nomfic = str_replace("!!explnumid!!",    str_pad ($t->explnum_id, 6, "0", STR_PAD_LEFT), $nomfic) ;
			$nomfic = str_replace("!!idnotice!!",     str_pad ($t->numnotice, 6, "0", STR_PAD_LEFT), $nomfic) ;
			$nomfic = str_replace("!!idbulletin!!",   str_pad ($t->explnum_bulletin, 6, "0", STR_PAD_LEFT), $nomfic) ;
			$nomfic = str_replace("!!indicedocnum!!", str_pad ($i, 3, "0", STR_PAD_LEFT), $nomfic) ;
			$nomfic = str_replace("!!nomdoc!!",       $t->explnum_nomfichier, $nomfic) ;
			$hf = fopen($chemin.$nomfic, "w");
			if ($hf) {
				$explnum = new explnum($t->explnum_id);
				fwrite($hf, $explnum->get_file_content());
				fclose($hf);
				$ret .= "<li>".$msg['caddie_expdocnum_wtrue']." <a href=\"".$chemin.$nomfic."\">".htmlentities($nomfic, ENT_QUOTES, $charset)."</a></li>";
			} else {
				$ret .= "<li><i>".$msg['caddie_expdocnum_wfalse']." ".htmlentities($nomfic, ENT_QUOTES, $charset)."</i></li>";
			}
		}
		if (!empty($ret)) return "<blockquote>".$msg['caddie_expdocnum_dir']." ".htmlentities($chemin, ENT_QUOTES, $charset)."<br /><ul>".$ret."</ul></blockquote>";
		else return;
	}

	public function pointe_item($item=0, $object_type="NOTI", $blob="", $blob_type="EXPL_CB") {
		global $dbh;
	
		if (!$item) {
			$requete_compte = "select count(1) from caddie_content where caddie_id='".$this->idcaddie."' and content='".$blob."' and blob_type='".$blob_type."' ";
			$result_compte = @pmb_mysql_query($requete_compte, $dbh);
			$deja_item=pmb_mysql_result($result_compte, 0, 0);
				
			if ($deja_item) {
				$requete = "update caddie_content set flag='1' where caddie_id='".$this->idcaddie."' and content='".$blob."' ";
				$result = @pmb_mysql_query($requete, $dbh);
				$this->compte_items();
			} else return CADDIE_ITEM_INEXISTANT;
				
			return CADDIE_ITEM_NULL ;
		}
	
		// les objets sont identiques
		if ($object_type==$this->type) {
			// rêgle : les caddies sont homogènes, on y stocke des objets de même type en fonction du type du caddie
			$requete_compte = "select count(1) from caddie_content where caddie_id='".$this->idcaddie."' and object_id='".$item."' ";
			$result_compte = @pmb_mysql_query($requete_compte, $dbh);
			$deja_item=pmb_mysql_result($result_compte, 0, 0);
				
			if ($deja_item) {
				$requete = "update caddie_content set flag='1' where caddie_id='".$this->idcaddie."' and object_id='".$item."' ";
				$result = @pmb_mysql_query($requete, $dbh);
				$this->compte_items();
			} else return CADDIE_ITEM_INEXISTANT;
		} else {
			// Traitement des cas particuliers
			// panier d'exemplaires :
			//		Notice reçue :
			//			on stocke tous les exemplaires associés à la notice
			//				voir le pb de notice de dépouillement
			if ($this->type=="EXPL" && $object_type=="NOTI") {
				$rqt_mono_serial_bull_analysis = "select niveau_biblio, niveau_hierar from notices where notice_id = '$item' ";
				$res_mono_serial_bull_analysis = pmb_mysql_query($rqt_mono_serial_bull_analysis, $dbh);
				$row_mono_serial_bull_analysis = pmb_mysql_fetch_object($res_mono_serial_bull_analysis);
				// monographie
				if ($row_mono_serial_bull_analysis->niveau_biblio=="m" && $row_mono_serial_bull_analysis->niveau_hierar=="0")
					$rqt_expl = "select expl_id from exemplaires where expl_notice='$item' ";
				// périodique : notice mère
				if ($row_mono_serial_bull_analysis->niveau_biblio=="s" && $row_mono_serial_bull_analysis->niveau_hierar=="1")
					$rqt_expl = "select expl_id from exemplaires, bulletins where bulletin_notice='$item' and expl_bulletin=bulletin_id ";
				// périodique : notice de dépouillement (analytique)
				if ($row_mono_serial_bull_analysis->niveau_biblio=="a" && $row_mono_serial_bull_analysis->niveau_hierar=="2")
					$rqt_expl = "select expl_id from exemplaires, analysis where analysis_notice='$item' and analysis_bulletin=expl_bulletin ";
				// bulletin : notice de bulletin
				if ($row_mono_serial_bull_analysis->niveau_biblio=="b" && $row_mono_serial_bull_analysis->niveau_hierar=="2")
					$rqt_expl = "select expl_id from exemplaires, bulletins where num_notice='$item' and bulletin_id=expl_bulletin ";
			}
			//		Bulletin reçu :
			//			on stocke tous les exemplaires associés au bulletin
			if ($this->type=="EXPL" && $object_type=="BULL") {
				$rqt_expl = "select expl_id from exemplaires where expl_bulletin='$item' ";
			}
				
			// panier de notices :
			//		EXPL reçu :
			//			on stocke la notice de l'exemplaire
			//				voir le pb d'expl de bulletin
			if ($this->type=="NOTI" && $object_type=="EXPL") {
				$rqt_mono_bull = "select expl_notice, expl_bulletin from exemplaires where expl_id='$item' ";
				$res_mono_bull = pmb_mysql_query($rqt_mono_bull, $dbh);
				$row_mono_bull = pmb_mysql_fetch_object($res_mono_bull);
				// expl de monographie
				if ($row_mono_bull->expl_notice && !$row_mono_bull->expl_bulletin)
					$rqt_expl = "select expl_notice from exemplaires where expl_id='$item' ";
				// expl de bulletin
				if (!$row_mono_bull->expl_notice && $row_mono_bull->expl_bulletin)
					$rqt_expl = "select bulletin_notice from exemplaires, bulletins where expl_id='$item' and expl_bulletin=bulletin_id ";
			}
			//		BULL reçu :
			//			on stocke les notices de dépouillement du bulletin
			if ($this->type=="NOTI" && $object_type=="BULL") {
				$rqt_expl = "select analysis_notice from analysis where analysis_bulletin='$item' ";
			} // fin if NOTI / EXPL
				
			// panier de bulletins :
			//		EXPL reçu :
			//			on stocke le bulletin de l'exemplaire
			if ($this->type=="BULL" && $object_type=="EXPL") {
				$rqt_mono_bull = "select expl_notice, expl_bulletin from exemplaires where expl_id='$item' ";
				$res_mono_bull = pmb_mysql_query($rqt_mono_bull, $dbh);
				$row_mono_bull = pmb_mysql_fetch_object($res_mono_bull);
				// expl de monographie
				if ($row_mono_bull->expl_notice && !$row_mono_bull->expl_bulletin)
					return CADDIE_ITEM_IMPOSSIBLE_BULLETIN;
				// expl de bulletin
				if (!$row_mono_bull->expl_notice && $row_mono_bull->expl_bulletin)
					$rqt_expl = "select expl_bulletin from exemplaires where expl_id='$item' ";
			}
			//		NOTI reçue :
			//			on stocke le bulletin associé à la notice chapeau reçue
			//			ou bien le bulletin contenant la notice de dépouillement reçue
			if ($this->type=="BULL" && $object_type=="NOTI") {
				$rqt_mono_serial_bull_analysis = "select niveau_biblio, niveau_hierar from notices where notice_id = '$item' ";
				$res_mono_serial_bull_analysis = pmb_mysql_query($rqt_mono_serial_bull_analysis, $dbh);
				$row_mono_serial_bull_analysis = pmb_mysql_fetch_object($res_mono_serial_bull_analysis);
				// monographie
				if ($row_mono_serial_bull_analysis->niveau_biblio=="m" && $row_mono_serial_bull_analysis->niveau_hierar=="0")
					return CADDIE_ITEM_IMPOSSIBLE_BULLETIN;
				// périodique : notice mère
				if ($row_mono_serial_bull_analysis->niveau_biblio=="s" && $row_mono_serial_bull_analysis->niveau_hierar=="1")
					$rqt_expl = "select bulletin_id from bulletins where bulletin_notice='$item' ";
				// périodique : notice de dépouillement (analytique)
				if ($row_mono_serial_bull_analysis->niveau_biblio=="a" && $row_mono_serial_bull_analysis->niveau_hierar=="2")
					$rqt_expl = "select analysis_bulletin from analysis where analysis_notice='$item' ";
				// bulletin : notice de bulletin
				if ($row_mono_serial_bull_analysis->niveau_biblio=="b" && $row_mono_serial_bull_analysis->niveau_hierar=="2")
					$rqt_expl = "select bulletin_id from bulletins where num_notice='$item' ";
			}
				
			if ($rqt_expl) {
				$res_expl = pmb_mysql_query($rqt_expl, $dbh);
				for($i=0;$i<pmb_mysql_num_rows($res_expl);$i++) {
					$row=pmb_mysql_fetch_row($res_expl);
					$requete_compte = "select count(1) from caddie_content where caddie_id='".$this->idcaddie."' and object_id='".$row[0]."' ";
					$result_compte = @pmb_mysql_query($requete_compte, $dbh);
					$deja_item=pmb_mysql_result($result_compte, 0, 0);
					if ($deja_item) {
						$requete = "update caddie_content set flag='1' where caddie_id='".$this->idcaddie."' and object_id='".$row[0]."' ";
						$result = @pmb_mysql_query($requete, $dbh);
					}
				} // fin for
				$this->compte_items();
			}
		} // fin else types différents
		return CADDIE_ITEM_OK ;
	}
	
	// suppression d'un panier
	public function delete() {
		global $dbh;
		
	    //On supprime le panier des étagères
	    $requete = "DELETE FROM etagere_caddie where caddie_id='".$this->idcaddie."' ";
	    $result = @pmb_mysql_query($requete, $dbh);
	    //On supprime le panier des bannettes
	    $requete = "UPDATE bannettes SET num_panier=0 where num_panier='".$this->idcaddie."' ";
	    $result = @pmb_mysql_query($requete, $dbh);
	    //On supprime le panier des flux RSS
	    $requete = "DELETE FROM rss_flux_content where num_contenant='".$this->idcaddie."' AND type_contenant='CAD' ";
	    $result = @pmb_mysql_query($requete, $dbh);
	    //On supprime dans les sets pour les connecteurs sortants
	    $requete = "SELECT * FROM connectors_out_sets WHERE connector_out_set_config REGEXP '\{s:16:\"included_caddies\";a:[0-9]+:\{i:0;[i:0-9;]*i:".$this->idcaddie.";[i:0-9;]*\}'";
	    $result = pmb_mysql_query($requete, $dbh);
	    if ($result && pmb_mysql_num_rows($result)) {
	    	while ($row = pmb_mysql_fetch_object($result)) {
				$array_connector_out_set_config = unserialize($row->connector_out_set_config);
				foreach ($array_connector_out_set_config["included_caddies"] as $k => $v) {
					if ($v==$this->idcaddie) {
						array_splice($array_connector_out_set_config["included_caddies"],$k,1);
						break;
					}
				}
	    		@pmb_mysql_query("UPDATE connectors_out_sets SET connector_out_set_config = '".addslashes(serialize($array_connector_out_set_config))."' WHERE connector_out_set_id = ".$row->connector_out_set_id);
	    	}
	    }
	    
	    //suppression panier
		parent::delete();
		
	}

	// get_cart() : ouvre un panier et récupère le contenu
	public function get_cart($flag="", $inconnu_aussi=1) {
		global $dbh;
		$cart_list=array();
		switch ($flag) {
			case "FLAG" :
				$requete = "SELECT * FROM caddie_content where caddie_id='".$this->idcaddie."' and (flag is not null and flag!='') ";
				if (!$inconnu_aussi) $requete .= " and (content is null or content='') "; 
				break ;
			case "NOFLAG" :
				$requete = "SELECT * FROM caddie_content where caddie_id='".$this->idcaddie."' and (flag is null or flag='') ";
				if (!$inconnu_aussi) $requete .= " and (content is null or content='') ";
				break ;
			case "ALL" :
			default :
				$requete = "SELECT * FROM caddie_content where caddie_id='".$this->idcaddie."' ";
				if (!$inconnu_aussi) $requete .= " and (content is null or content='') ";
				break ;
		}
		$result = @pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($result)) {
			while ($temp = pmb_mysql_fetch_object($result)) {
				$cart_list[] = $temp->object_id;
			}
		} 
		return $cart_list;
	}

	// compte_items 
	public function compte_items() {
		parent::compte_items();
		$this->nb_item_base = 0 ;
		$this->nb_item_base_pointe = 0 ;
		$this->nb_item_blob = 0 ;
		$this->nb_item_blob_pointe = 0 ;
		$rqt_nb_item_base="select count(1) from caddie_content where caddie_id='".$this->idcaddie."' and (content is null or content='')";
		$this->nb_item_base = pmb_mysql_result(pmb_mysql_query($rqt_nb_item_base), 0, 0);
		$rqt_nb_item_base_pointe="select count(1) from caddie_content where caddie_id='".$this->idcaddie."' and (content is null or content='') and (flag is not null and flag!='') ";
		$this->nb_item_base_pointe = pmb_mysql_result(pmb_mysql_query($rqt_nb_item_base_pointe), 0, 0);
		$this->nb_item_blob = $this->nb_item - $this->nb_item_base ;
		$this->nb_item_blob_pointe = $this->nb_item_pointe - $this->nb_item_base_pointe ;
	}

	public function verif_expl_item($expl) {
		if ($expl) {
			$query = "select count(1) from pret where pret_idexpl=".$expl." limit 1 ";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_result($result, 0, 0)) return 1 ;
		}
		return 0 ;
	}
	
	public function verif_bull_item($bull,$forcage=array()) {
		
		global $dbh;
		// plus aucune vérification, on supprime en cascade :
		//		bulletin
		//		notice
		//		exemplaire
		//		exemplaires numériques
		/*$query = "select count(1) from exemplaires, pret where expl_bulletin=".$bull." and pret_idexpl=expl_id limit 1 ";
		$result = pmb_mysql_query($query, $dbh);
		if (pmb_mysql_result($result, 0, 0)) return 1 ;
			else return 0 ;*/
		if($bull){
			$query = "select count(1) from analysis where analysis_bulletin=".$bull." limit 1 ";
			$result = pmb_mysql_query($query, $dbh);
			if(pmb_mysql_result($result, 0, 0)){
				return 1 ;
			}
			$query = "select count(1) from exemplaires where expl_bulletin=".$bull." limit 1 ";
			$result = pmb_mysql_query($query, $dbh);
			if(pmb_mysql_result($result, 0, 0)){
				return 1 ;
			}
			$query = "select count(1) from bulletins where bulletin_id=".$bull." AND num_notice!='0' limit 1 ";
			$result = pmb_mysql_query($query, $dbh);
			if(pmb_mysql_result($result, 0, 0)){
				return 1 ;
			}
			$query = "select count(1) from explnum where explnum_bulletin=".$bull." limit 1 ";
			$result = pmb_mysql_query($query, $dbh);
			if (pmb_mysql_result($result, 0, 0)&& !$forcage['bulletin_linked_expl_num']){
				return 1 ;
			}
		}
		return 0;
	}
	
	public function verif_noti_item($noti,$forcage=array()) {
	
		global $dbh;
		if ($noti) {
			if ($this->type=="BULL") {
				$query = "select count(1) from analysis where analysis_notice=".$noti." limit 1 ";
				$result = pmb_mysql_query($query, $dbh);
				if (pmb_mysql_result($result, 0, 0)) return 1 ;
			}
			
			$query = "select count(1) from bulletins where bulletin_notice=".$noti." limit 1 ";
			$result = pmb_mysql_query($query, $dbh);
			if (pmb_mysql_result($result, 0, 0)) return 1 ;
			
			$notice_relations = notice_relations_collection::get_object_instance($noti);
			if ($notice_relations->get_nb_links() && !$forcage['notice_linked']) return 1 ;
			
			$query = "select count(1) from exemplaires where expl_notice=".$noti." limit 1 ";
			$result = pmb_mysql_query($query, $dbh);
			if (pmb_mysql_result($result, 0, 0)) return 1 ;
			
			$query = "select count(1) from resa where resa_idnotice=".$noti." limit 1 ";
			$result = pmb_mysql_query($query, $dbh);
			if (pmb_mysql_result($result, 0, 0)) return 1 ;
			
			$query = "select count(1) from explnum where explnum_notice=".$noti." limit 1 ";
			$result = pmb_mysql_query($query, $dbh);
			if (pmb_mysql_result($result, 0, 0)&& !$forcage['notice_linked_expl_num']) return 1 ;
			
			//Pour les périodiques
			$requete="SELECT niveau_biblio, niveau_hierar FROM notices WHERE notice_id='".$noti."'";
			$res=pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res) && (pmb_mysql_result($res,0,0) == "s") && (pmb_mysql_result($res,0,1) == "1")){
				
				$query = "select count(1) from collections_state where id_serial=".$noti." limit 1 ";
				$result = pmb_mysql_query($query, $dbh);
				if (pmb_mysql_result($result, 0, 0) && !$forcage['notice_perio_collstat']) return 1 ;
				
				$query = "select count(1) from abts_abts where num_notice=".$noti." limit 1 ";
				$result = pmb_mysql_query($query, $dbh);
				if (pmb_mysql_result($result, 0, 0) && !$forcage['notice_perio_abo']) return 1 ;
				
				$query = "select count(1) from abts_modeles where num_notice=".$noti." limit 1 ";
				$result = pmb_mysql_query($query, $dbh);
				if (pmb_mysql_result($result, 0, 0) && !$forcage['notice_perio_modele']) return 1 ;
			}
		}
		return 0 ;
	}
	
	static public function show_actions($id_caddie = 0, $type_caddie = 'NOTI') {
		global $cart_action_selector,$cart_action_selector_line;

		$array_actions = self::get_array_actions($id_caddie, $type_caddie);
		//On crée les lignes du menu
		$lines = '';
		if(is_array($array_actions) && count($array_actions)){
			foreach($array_actions as $item_action){
				$tmp_line = str_replace('!!cart_action_selector_line_location!!',$item_action['location'],$cart_action_selector_line);
				$tmp_line = str_replace('!!cart_action_selector_line_msg!!',$item_action['msg'],$tmp_line);
				$lines.= $tmp_line;
			}
		}
		
		//On récupère le template
		$to_show = str_replace('!!cart_action_selector_lines!!',$lines,$cart_action_selector);
		
		return $to_show;
	}
	
	public static function get_array_actions($id_caddie = 0, $type_caddie = 'NOTI', $actions_to_remove = array()) {
		global $msg;
		global $pmb_scan_request_activate, $gestion_acces_active, $pmb_transferts_actif;
		
		$array_actions = array();
		if (empty($actions_to_remove['edit_cart'])) { 
			$array_actions[] = array('msg' => $msg["caddie_menu_action_edit_panier"], 'location' => './catalog.php?categ=caddie&sub=gestion&quoi=panier&action=edit_cart&idcaddie='.$id_caddie.'&item=0');
		}
		if (empty($actions_to_remove['supprpanier'])) {
			$array_actions[] = array('msg' => $msg["caddie_menu_action_suppr_panier"], 'location' => './catalog.php?categ=caddie&sub=action&quelle=supprpanier&action=choix_quoi&object_type=NOTI&idcaddie='.$id_caddie.'&item=0');
		}
		if (empty($actions_to_remove['transfert'])) {
			$array_actions[] = array('msg' => $msg["caddie_menu_action_transfert"], 'location' => './catalog.php?categ=caddie&sub=action&quelle=transfert&action=transfert&object_type=NOTI&idcaddie='.$id_caddie.'&item=');
		}
		if (empty($actions_to_remove['edition'])) {
			$array_actions[] = array('msg' => $msg["caddie_menu_action_edition"], 'location' => './catalog.php?categ=caddie&sub=action&quelle=edition&action=choix_quoi&object_type=NOTI&idcaddie='.$id_caddie.'&item=0');
		}
		if ($type_caddie == "EXPL" && empty($actions_to_remove['impr_cote'])) {
			$array_actions[] = array('msg' => $msg["caddie_menu_action_impr_cote"], 'location' => './catalog.php?categ=caddie&sub=action&quelle=impr_cote&action=choix_quoi&object_type=EXPL&idcaddie='.$id_caddie.'&item=0');
		}
		if (empty($actions_to_remove['export'])) {
			$array_actions[] = array('msg' => $msg["caddie_menu_action_export"], 'location' => './catalog.php?categ=caddie&sub=action&quelle=export&action=choix_quoi&object_type=NOTI&idcaddie='.$id_caddie.'&item=0');
		}
		if (empty($actions_to_remove['expdocnum'])) {
			$array_actions[] = array('msg' => $msg["caddie_menu_action_exp_docnum"], 'location' => './catalog.php?categ=caddie&sub=action&quelle=expdocnum&action=choix_quoi&object_type=NOTI&idcaddie='.$id_caddie.'&item=0');
		}
		if (empty($actions_to_remove['selection'])) {
			$array_actions[] = array('msg' => $msg["caddie_menu_action_selection"], 'location' => './catalog.php?categ=caddie&sub=action&quelle=selection&action=&object_type=NOTI&idcaddie='.$id_caddie.'&item=0');
		}
		$evt_handler = events_handler::get_instance();
		$event = new event_users_group("users_group", "get_autorisation_del_base");
		$evt_handler->send($event);
		if(!$event->get_error_message() && empty($actions_to_remove['supprbase'])){
			$array_actions[] = array('msg' => $msg["caddie_menu_action_suppr_base"], 'location' => './catalog.php?categ=caddie&sub=action&quelle=supprbase&action=choix_quoi&object_type=NOTI&idcaddie='.$id_caddie.'&item=0');
		}
		if (empty($actions_to_remove['reindex'])) {
			$array_actions[] = array('msg' => $msg["caddie_menu_action_reindex"], 'location' => './catalog.php?categ=caddie&sub=action&quelle=reindex&action=choix_quoi&object_type=NOTI&idcaddie='.$id_caddie.'&item=0');
		}
		if($gestion_acces_active && empty($actions_to_remove['access_rights'])){
			$array_actions[] = array('msg' => $msg["caddie_menu_action_access_rights"], 'location' => './catalog.php?categ=caddie&sub=action&quelle=access_rights&action=choix_quoi&object_type=NOTI&idcaddie='.$id_caddie.'&item=0');
		}
		if((SESSrights & CIRCULATION_AUTH) && $pmb_scan_request_activate && empty($actions_to_remove['scan_request'])){
			$array_actions[] = array('msg' => $msg["scan_request_record_button"], 'location' => './catalog.php?categ=caddie&sub=action&quelle=scan_request&action=choix_quoi&object_type=NOTI&idcaddie='.$id_caddie);
		}
		if ($pmb_transferts_actif && empty($actions_to_remove['transfert_to_locations'])) {
			$array_actions[] = array('msg' => $msg["caddie_menu_action_transfert_to_location"], 'location' => './catalog.php?categ=caddie&sub=action&quelle=transfert_to_location&action=choix_quoi&object_type=EXPL&idcaddie='.$id_caddie);
		}
		$event = new event_display_overload("caddie_action", "add_array_caddie_action");
		$event->set_entity_id($id_caddie);
		$event->set_overload_type($type_caddie);
		$evt_handler->send($event);
		$action_overloads_tmp=$event->get_array_action_overloads();
		if(is_array($action_overloads_tmp) && count($action_overloads_tmp)){
			foreach($action_overloads_tmp as $element){
				$array_actions[] = $element;
			}
		}
		return $array_actions;
	}
		
	public static function is_reachable($caddie_id=0) {
		global $PMBuserid;
		
		$query = 'select idcaddie from caddie where idcaddie="'.$caddie_id.'" and (autorisations="'.$PMBuserid.'" or autorisations like "'.$PMBuserid.' %" or autorisations like "% '.$PMBuserid.' %" or autorisations like "% '.$PMBuserid.'" or autorisations_all=1)';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			return true;
		}
		return false;
	}
	
	public static function get_data_from_id($caddie_id=0) {
		$data = array();
		$query = "SELECT name, comment, caddie_classement FROM caddie WHERE idcaddie='".$caddie_id."'";
		$result = @pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$data = array(
				'name' => $row->name,
				'comment' => $row->comment,
				'classement' => $row->caddie_classement
			);
		}
		return $data;
	}
	
	protected function replace_in_action_query($query, $by) {
		$final_query=str_replace("CADDIE(NOTI)",$by,$query);
		$final_query=str_replace("CADDIE(EXPL)",$by,$final_query);
		$final_query=str_replace("CADDIE(BULL)",$by,$final_query);
		return $final_query;
	}
	
	protected function get_edition_template_form() {
		global $cart_choix_quoi_edition;
		return $cart_choix_quoi_edition;
	}
	
	public function get_list_caddie_ui() {
		global $show_list;
		
		list_caddie_ui::set_id_caddie($this->idcaddie);
		list_caddie_ui::set_object_type($this->type);
		if($show_list) {
			list_caddie_ui::set_show_list(true);		
		}
		switch ($this->type) {
			case 'BULL':
				return new list_caddie_ui(array(), array(), array('by' => 'bulletin_titre', 'asc_desc' => 'asc'));
				break;
			case 'NOTI':
			case 'EXPL':
			default:
				return new list_caddie_ui();
				break;
		}
	}
	
	public function get_edition_form($action="", $action_cancel="") {
		global $msg;
		
		if(!$action) $action = "./catalog/caddie/action/edit.php?idcaddie=".$this->get_idcaddie();
		if(!$action_cancel) $action_cancel = "./catalog.php?categ=caddie&sub=action&quelle=edition&action=&idcaddie=0" ;
		$form = parent::get_edition_form($action, $action_cancel);
		$sel_notice_tpl=notice_tpl_gen::gen_tpl_select("notice_tpl",0,'',1,1);
		$suppl = "";
		if($sel_notice_tpl) {
			$sel_notice_tpl=$msg['caddie_select_notice_tpl']."&nbsp;".$sel_notice_tpl;
			$suppl.= "&nbsp;<input type='button' class='bouton' value='".$msg['etatperso_export_notice']."' onclick=\"this.form.dest.value='EXPORT_NOTI'; this.form.submit();\" />";
		}
		$form = str_replace('<!-- !!boutons_supp!! -->', $suppl, $form);
		$form = str_replace('<!-- notice_template -->', $sel_notice_tpl, $form);
		return $form;
	}
	
	public function get_export_form($action="", $action_cancel="") {
		global $msg;
		global $base_path;
		global $cart_choix_quoi_exporter;
		global $catalog;
	
		$form = $cart_choix_quoi_exporter;
	
		$form = str_replace('!!action!!', $action, $form);
		$form = str_replace('!!action_cancel!!', $action_cancel, $form);
		$form = str_replace('!!titre_form!!', $msg["caddie_choix_export"], $form);
		$form = str_replace('!!bouton_valider!!', $msg["caddie_bouton_exporter"], $form);
		
		//Lecture des différents exports possibles
		$catalog=array();
		$n_typ_total=0;
		if (file_exists("$base_path/admin/convert/imports/catalog_subst.xml"))
			$fic_catal = "$base_path/admin/convert/imports/catalog_subst.xml";
		else
			$fic_catal = "$base_path/admin/convert/imports/catalog.xml";
		
		_parser_($fic_catal, array("ITEM"=>"_item_catalog_"), "CATALOG");
		
		//Création de la liste des types d'import
		$export_type="<select name=\"export_type\" id=\"export_type\">\n";
		for ($i=0; $i<count($catalog); $i++) {
			$export_type.="<option value=\"".$catalog[$i]['INDEX']."\">".$catalog[$i]['NAME']."</option>\n";
		}
		$export_type.="</select>";
		
		$form=str_replace("!!export_type!!",$export_type,$form);
		
		$param = new export_param(EXP_DEFAULT_GESTION);
		$form=str_replace("!!form_param!!",$param->check_default_param(),$form);
		return $form;
	}
	
	public function get_item_info_from_expl_cb($expl_cb, $ajax_mode = 0) {
		global $msg;
		global $alert_sound_list;
		
		$item_info = new stdClass();
		$item_info->message_ajout_expl = '';
		$item_info->expl_ajout_ok = 0;
		$item_info->expl_id = 0;
		$item_info->stuff = '';
		if($expl_cb) {
			$item_info->expl_ajout_ok = 1;
			$query = "select expl_id from exemplaires where expl_cb='".$expl_cb."'";
			$result = pmb_mysql_query($query);
			if(!pmb_mysql_num_rows($result)) {
				// exemplaire inconnu
				$item_info->message_ajout_expl = "<strong>$expl_cb&nbsp;: $msg[367]</strong>";
				$item_info->expl_ajout_ok = 0;
				$alert_sound_list[]="critique";
			} else {
				$expl_trouve = pmb_mysql_fetch_object($result);
				$item_info->expl_id = $expl_trouve->expl_id;
				if($stuff = get_expl_info($item_info->expl_id)) {
					//on renvoi moins d'infos via le mode AJAX
					if($ajax_mode) {
						$item_info->expl_notice=$stuff->expl_notice;
						$item_info->titre=$stuff->titre;
					} else {
						$item_info->stuff = check_pret($stuff);
					}
				} else {
					$item_info->message_ajout_expl = "<strong>$expl_cb&nbsp;: $msg[395]</strong>";
					$item_info->expl_ajout_ok = 0;
					$alert_sound_list[]="critique";
				}
			}
		}
		return $item_info;
	}
	
	// affichage du contenu complet d'un caddie
	public function aff_cart_objects ($url_base="./catalog.php?categ=caddie&sub=gestion&quoi=panier&idcaddie=0", $no_del=false,$rec_history=0, $no_point=false ) {
		global $msg;
		global $dbh;
		global $begin_result_liste, $end_result_liste;
		global $affich_tris_result_liste;
		global $pmb_nb_max_tri;
		global $nbr_lignes, $page, $nb_per_page_search ;
		global $url_base_suppr_cart ;
	
		$url_base_suppr_cart = $url_base ;
	
		$cb_display = "
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
	    		<span class=\"notice-heada\">!!heada!!</span>
	    		<br />
			</div>
			";
	
		// nombre de références par pages
		if ($nb_per_page_search != "") $nb_per_page = $nb_per_page_search ;
		else $nb_per_page = 10;
	
		// on récupére le nombre de lignes
		if(!$nbr_lignes) {
			$requete = "SELECT count(1) FROM caddie_content where caddie_id='".$this->get_idcaddie()."' ".static::get_query_filters();
			$res = pmb_mysql_query($requete, $dbh);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
		}
	
		if(!$page) $page=1;
		$debut =($page-1)*$nb_per_page;
	
		//Calcul des variables pour la suppression d'items
		$modulo = $nbr_lignes%$nb_per_page;
		if($modulo == 1){
			$page_suppr = (!$page ? 1 : $page-1);
		} else {
			$page_suppr = $page;
		}
		$nb_after_suppr = ($nbr_lignes ? $nbr_lignes-1 : 0);
	
		if($nbr_lignes) {
			// on lance la vraie requête
			switch ($this->type) {
				case "NOTI":
					$from = " caddie_content left join notices on notice_id = object_id ";
					$order_by = " index_sew " ;
					break ;
				case "EXPL":
					$from = " caddie_content left join exemplaires on expl_id=object_id left join notices on notice_id = expl_notice ";
					$order_by = " index_sew " ;
					break ;
				case "BULL":
					$from = " caddie_content left join bulletins on bulletin_id = object_id ";
					$order_by = " date_date " ;
					break ;
			}
	
			$requete = "SELECT * FROM $from where caddie_id='".$this->get_idcaddie()."' ".static::get_query_filters();
			$requete .= " order by ".$order_by;
			$requete .= " LIMIT $debut,$nb_per_page ";
			//gestion du tri
			if ($this->type=="NOTI") {
				if ($nbr_lignes<=$pmb_nb_max_tri) {
					if ($_SESSION["tri"]) {
						$requete = "SELECT notice_id,caddie_content.* FROM $from where caddie_id='".$this->get_idcaddie()."'";
						$sort=new sort('notices','base');
						$requete = $sort->appliquer_tri($_SESSION["tri"], $requete, "notice_id", $debut, $nb_per_page);
					}
				}
			}
			// fin gestion tri
				
			$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
			// l'affichage du résultat est fait après le else
		} else {
			print $msg[399];
			return;
		}
	
		$liste=array();
		$result = @pmb_mysql_query($requete, $dbh) ;
		if ($result) {
			if(pmb_mysql_num_rows($result)) {
				while ($temp = pmb_mysql_fetch_object($result)) {
					$liste[] = array('object_id' => $temp->object_id, 'content' => $temp->content, 'blob_type' => $temp->blob_type, 'flag' => $temp->flag ) ;
				}
			}
		}
		if(!sizeof($liste) || !is_array($liste)) {
			print $msg[399];
			return;
		} else {
			print $this->get_js_script_cart_objects('catalog');
	
			// en fonction du type de caddie on affiche ce qu'il faut
			if ($this->type=="NOTI") {
				// boucle de parcours des notices trouvées
				// inclusion du javascript de gestion des listes dépliables
				// début de liste
				print $begin_result_liste;
				//Affichage du lien impression et panier
				if (($rec_history)&&($_SESSION["CURRENT"]!==false)) {
					$current=$_SESSION["CURRENT"];
					print "&nbsp;<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=$current&action=print_prepare','print'); return false;\"><img src='".get_url_icon('basket_small_20x20.gif')."' border='0' class='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>&nbsp;<a href='#' onClick=\"openPopUp('./print.php?current_print=$current&action_print=print_prepare','print',500,600,-2,-2,'scrollbars=yes,menubar=0'); return false;\"><img src='".get_url_icon('print.gif')."' border='0' class='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
					print "&nbsp;<a href='#' onClick=\"openPopUp('./download.php?current_download=$current&action_download=download_prepare".$tri_id_info."','download'); return false;\"><img src='".get_url_icon('upload_docnum.gif')."' border='0' class='center' alt=\"".$msg["docnum_download"]."\" title=\"".$msg["docnum_download"]."\"/></a>";
					if ($nbr_lignes<=$pmb_nb_max_tri) {
						print "&nbsp;".$affich_tris_result_liste;
					}
				}
				print caddie::show_actions($this->get_idcaddie(),$this->type);
					
				$elements_records_caddie_list_ui = new elements_records_caddie_list_ui($liste, count($liste), false);
				$elements_records_caddie_list_ui->set_show_resa(0);
				$elements_records_caddie_list_ui->set_show_resa_planning(0);
				$elements_records_caddie_list_ui->set_draggable(0);
				elements_records_caddie_list_ui::set_url_base($url_base);
				elements_records_caddie_list_ui::set_idcaddie($this->get_idcaddie());
				elements_records_caddie_list_ui::set_no_del($no_del);
				elements_records_caddie_list_ui::set_no_point($no_point);
				print $elements_records_caddie_list_ui->get_elements_list();
				
				print $end_result_liste;
			} // fin si NOTI
			// si EXPL
			if ($this->type=="EXPL") {
				// boucle de parcours des exemplaires trouvés
				// inclusion du javascript de gestion des listes dépliables
				// début de liste
				print $begin_result_liste;
				print caddie::show_actions($this->get_idcaddie(),$this->type);
				foreach ($liste as $cle => $expl) {
					if (!$expl['content']) {
						if($stuff = get_expl_info($expl['object_id'])) {
							if (!$no_point) {
								if ($expl['flag']) $marque_flag ="<img src='".get_url_icon('depointer.png')."' id='caddie_".$this->get_idcaddie()."_item_".$stuff->expl_id."' title=\"".$msg['caddie_item_depointer']."\" onClick='del_pointage_item(".$this->get_idcaddie().",".$stuff->expl_id.");' style='cursor: pointer'/>" ;
								else $marque_flag ="<img src='".get_url_icon('pointer.png')."' id='caddie_".$this->get_idcaddie()."_item_".$stuff->expl_id."' title=\"".$msg['caddie_item_pointer']."\" onClick='add_pointage_item(".$this->get_idcaddie().",".$stuff->expl_id.");' style='cursor: pointer'/>" ;
							} else {
								if ($expl['flag']) $marque_flag ="<img src='".get_url_icon('tick.gif')."'/>" ;
								else $marque_flag ="" ;
							}
							if (!$no_del) $stuff->lien_suppr_cart = "<a href='$url_base&action=del_item&object_type=EXPL&item=$stuff->expl_id&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" /></a> $marque_flag";
							else $stuff->lien_suppr_cart = $marque_flag ;
							$stuff = check_pret($stuff);
							print pmb_bidi(print_info($stuff,0,1));
						} else {
							print "<strong>ID : ".$expl['object_id']."&nbsp;: ${msg[395]}</strong>";
						}
					} else {
						if (!$stuff = get_expl_info($expl['object_id'])) {
							$expl_id = 0;
						} else {
							$expl_id = $stuff->expl_id;
						}						
						if (!$no_point) {
							if ($expl['flag']) $marque_flag ="<img src='".get_url_icon('depointer.png')."' id='caddie_".$this->get_idcaddie()."_item_". $expl['content']."' title=\"".$msg['caddie_item_depointer']."\" onClick='del_pointage_item(".$this->get_idcaddie().",".$expl_id.");' style='cursor: pointer'/>" ;
							else $marque_flag ="<img src='".get_url_icon('pointer.png')."' id='caddie_".$this->get_idcaddie()."_item_". $expl['content']."' title=\"".$msg['caddie_item_pointer']."\" onClick='add_pointage_item(".$this->get_idcaddie().",".$expl_id.");' style='cursor: pointer'/>" ;
						} else {
							if ($expl['flag']) $marque_flag ="<img src='".get_url_icon('tick.gif')."'/>" ;
							else $marque_flag ="" ;
						}
						if (!$no_del) $lien_suppr_cart = "<a href='$url_base&action=del_item&object_type=EXPL_CB&item=".urlencode($expl['content'])."&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" /></a> $marque_flag";
						else $lien_suppr_cart = $marque_flag ;
						$cb_display = "
						<div id=\"el" . $expl['content'] ."Parent\" class=\"notice-parent\">
						<span class=\"notice-heada\"><strong>$lien_suppr_cart Code-barre : $expl[content]&nbsp;: ${msg[395]}</strong></span>
						<br />
						</div>
						";
						print $cb_display;
					}
				} // fin de liste
				print $end_result_liste;
			} // fin si EXPL
			if ($this->type=="BULL") {
				// boucle de parcours des bulletins trouvés
				// inclusion du javascript de gestion des listes dépliables
				// début de liste
				print $begin_result_liste;
				print caddie::show_actions($this->get_idcaddie(),$this->type);
				foreach ($liste as $cle => $expl) {
					if (!$no_del) $show_del=1; else $show_del=0;
					if($bull_aff = show_bulletinage_info($expl['object_id'], 0 , $show_del, $expl['flag'],1)) {
						print pmb_bidi($bull_aff);
					} else {
						if (!$no_point) {
							if ($expl['flag']) $marque_flag ="<img src='".get_url_icon('depointer.png')."' id='caddie_".$this->get_idcaddie()."_item_".$expl['object_id']."' title=\"".$msg['caddie_item_depointer']."\" onClick='del_pointage_item(".$this->get_idcaddie().",".$expl['object_id'].");' style='cursor: pointer'/>" ;
							else $marque_flag ="<img src='".get_url_icon('pointer.png')."' id='caddie_".$this->get_idcaddie()."_item_".$expl['object_id']."' title=\"".$msg['caddie_item_pointer']."\" onClick='add_pointage_item(".$this->get_idcaddie().",".$expl['object_id'].");' style='cursor: pointer'/>" ;
						} else {
							if ($expl['flag']) $marque_flag ="<img src='".get_url_icon('tick.gif')."'/>" ;
							else $marque_flag ="" ;
						}
						if (!$no_del) $lien_suppr_cart = "<a href='$url_base&action=del_item&object_type=EXPL_CB&item=".$expl['content']."&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" /></a> $marque_flag";
						else $lien_suppr_cart = $marque_flag ;
						$cb_display = "
						<div id=\"el!!id!!Parent\" class=\"notice-parent\">
						<span class=\"notice-heada\"><strong>$lien_suppr_cart Code-barre : $expl[content]&nbsp;: ${msg[395]}</strong></span>
						<br />
						</div>
						";
						print $cb_display;
					}
				} // fin de liste
				print $end_result_liste;
			} // fin si BULL
		}
		print "<br />".$nav_bar ;
		return;
	}
	
	public function aff_cart_titre() {
		global $msg;
		
		$link = "./catalog.php?categ=search&mode=3&object_type=".$this->type."&idcaddie=".$this->get_idcaddie()."&item=";
		return "
			<div class='titre-panier'>
				<h3>
					<a href='".$link."'>".$this->name.($this->comment ? " - ".$this->comment : "")."</a> <i><small>(".$msg["caddie_de_".$this->type].")</small></i>
				</h3>
			</div>";
	}
	
	public function aff_cart_nb_items() {
		global $msg;
	
		return "
		<div id='cart_".$this->get_idcaddie()."_nb_items' name='cart_".$this->get_idcaddie()."_nb_items'>
			<div class='row'>
				<div class='colonne3'>".$msg['caddie_contient']."</div>
				<div class='colonne3 center'>".$msg['caddie_contient_total']."</div>
				<div class='colonne_suite center'>".$msg['caddie_contient_nb_pointe']."</div>
			</div>
			<div class='row'>
				<div class='colonne3 align_left'>".$msg['caddie_contient_total']."</div>
				<div class='colonne3 center'><b><span id='nb_item'>".$this->nb_item."</span></b></div>
				<div class='colonne_suite center'><b><span id='nb_item_pointe'>".$this->nb_item_pointe."</span></b></div>
			</div>
			<div class='row'>
				<div class='colonne3 align_left'>".$msg['caddie_contient_dont_fonds']."</div>
				<div class='colonne3 center'><label class='etiquette' id='nb_item_base'>".$this->nb_item_base."</label></div>
				<div class='colonne_suite center'><label id='nb_item_base_pointe'>".$this->nb_item_base_pointe."</label></div>
			</div>
			<div class='row'>
				<div class='colonne3 align_left'>".$msg['caddie_contient_dont_inconnus']."</div>
				<div class='colonne3 center'><label class='etiquette' id='nb_item_blob'>".$this->nb_item_blob."</label></div>
				<div class='colonne_suite center'><label id='nb_item_blob_pointe'>".$this->nb_item_blob_pointe."</label></div>
			</div>
			<div class='row'></div>
		</div>";
	}
	
	protected function get_choix_quoi_template_form() {
		global $cart_choix_quoi;
		return $cart_choix_quoi;
	}
	
	public function get_choix_quoi_form($action="", $action_cancel="", $titre_form="", $bouton_valider="",$onclick="", $aff_choix_dep = false) {
		global $msg, $charset, $base_path;
		global $quelle;
		global $cart_choix_quoi_not_ou_dep,$notice_linked_suppr_form,$bull_liked_suppr_form;
		global $deflt_agnostic_warehouse;
		
		$form = parent::get_choix_quoi_form($action, $action_cancel, $titre_form, $bouton_valider, $onclick, $aff_choix_dep);
		
		$sources_form ='';
		if ($quelle=='supprbase') {
			$n_sources=0;
			require_once($base_path."/admin/connecteurs/in/agnostic/agnostic.class.php");
			$conn=new agnostic($base_path.'/admin/connecteurs/in/agnostic');
			$conn->get_sources();
			$n_sources=count($conn->sources);
			if ($n_sources) {
				$sources_form = "<div class='row'>&nbsp;</div><div class='row'>".$msg['caddie_save_to_warehouse']."<select name='source_id' id='source_id' >";
				$sources_form.= "<option value='0' ".(!$deflt_agnostic_warehouse ? "selected='selected'" : "").">".$msg['caddie_save_to_warehouse_none']."</option>";
				foreach($conn->sources as $k=>$v) {
					$sources_form.= "<option value='".$k."' ".($deflt_agnostic_warehouse == $k ? "selected='selected'" : "").">".htmlentities($v['NAME'],ENT_QUOTES,$charset)."</option>";
				}
				$sources_form.= "</select></div>";
			}
			if($this->type == 'NOTI') {
				$form = str_replace('<!--suppr_link-->', $notice_linked_suppr_form.$sources_form, $form);
			}elseif($this->type == 'EXPL') {
				$form = str_replace('<!--suppr_link-->', $sources_form, $form);
			}elseif($this->type == 'BULL'){
				$form = str_replace('<!--suppr_link-->', $bull_liked_suppr_form, $form);
			}
		}
		if ($aff_choix_dep) $form = str_replace('!!bull_not_ou_dep!!',$cart_choix_quoi_not_ou_dep,$form);
		else $form = str_replace('!!bull_not_ou_dep!!',"<div class='row'>&nbsp;</div>",$form);
		return $form;
	}
	
	public function reindex_object($object) {
		if ($this->type=='NOTI'){
			// Mise à jour de tous les index de la notice
			notice::majNoticesTotal($object);
		}elseif($this->type=='BULL'){
			$requete="SELECT bulletin_titre, num_notice FROM bulletins WHERE bulletin_id='".$object."'";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)){
				$element=pmb_mysql_fetch_object($res);
				if(trim($element->bulletin_titre)){
					$requete="UPDATE bulletins SET index_titre=' ".addslashes(strip_empty_words($element->bulletin_titre))." ' WHERE bulletin_id='".$object."'";
					pmb_mysql_query($requete);
				}
				if($element->num_notice){
					notice::majNoticesTotal($element->num_notice);
				}
			
			}
		}elseif($this->type=='EXPL'){
			$requete="SELECT expl_notice, expl_bulletin FROM exemplaires WHERE expl_id='".$object."' ";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)){
				$row=pmb_mysql_fetch_object($res);
				if($row->expl_notice){
					notice::majNoticesTotal($row->expl_notice);
				}else{
					$requete="SELECT bulletin_titre, num_notice FROM bulletins WHERE bulletin_id='".$row->expl_bulletin."'";
					$res2=pmb_mysql_query($requete);
					if(pmb_mysql_num_rows($res2)){
						$element=pmb_mysql_fetch_object($res2);
						if(trim($element->bulletin_titre)){
							$requete="UPDATE bulletins SET index_titre=' ".addslashes(strip_empty_words($element->bulletin_titre))." ' WHERE bulletin_id='".$row->expl_bulletin."'";
							pmb_mysql_query($requete);
						}
						if($element->num_notice){
							notice::majNoticesTotal($element->num_notice);
						}
					}
				}
			}
		}
	}
	
	public function del_items_base_from_list($liste=array()) {
		global $supp_notice_linked;
		global $supp_notice_linked_expl_num;
		global $source_id;
		global $supp_notice_perio_abo;
		global $supp_notice_perio_collstat;
		global $supp_notice_perio_modele;
		global $supp_bulletin_linked_expl_num;
		global $supp_notice_linked_cascade;
		
		global $url_base;
		
		// le formulaire demande de supprimer les notices meme avec liens
		$forcage = array();
		if($supp_notice_linked) $forcage['notice_linked']=1; else $forcage['notice_linked']=0;
		if($supp_notice_linked_expl_num) $forcage['notice_linked_expl_num']=1; else $forcage['notice_linked_expl_num']=0;
		if($source_id) $forcage['source_id']=$source_id; else $forcage['source_id']=0;
		if($supp_notice_perio_abo) $forcage['notice_perio_abo']=1; else $forcage['notice_perio_abo']=0;
		if($supp_notice_perio_collstat) $forcage['notice_perio_collstat']=1; else $forcage['notice_perio_collstat']=0;
		if($supp_notice_perio_modele) $forcage['notice_perio_modele']=1; else $forcage['notice_perio_modele']=0;
		if($supp_bulletin_linked_expl_num) $forcage['bulletin_linked_expl_num']=1; else $forcage['bulletin_linked_expl_num']=0;
		
		$res_aff_suppr_base = '';
		foreach ($liste as $cle => $object) {
			// le formulaire demande de suprimmer toutes les notices liées à celle-ci
			if($supp_notice_linked_cascade) {
				$forcage['notice_linked']=1;
				$liste_linked=notice::get_list_child($object);
				foreach($liste_linked as $object) {
					if ($this->del_item_base($object,$forcage)==CADDIE_ITEM_SUPPR_BASE_OK)
						$this->del_item_all_caddies ($object, $this->type) ;
					else {
						$res_aff_suppr_base .= aff_cart_unique_object ($object, $this->type, $url_base="./catalog.php?categ=caddie&sub=gestion&quoi=panier&idcaddie=".$this->idcaddie) ;
					}
				}
			} else {
				if ($this->del_item_base($object,$forcage)==CADDIE_ITEM_SUPPR_BASE_OK) $this->del_item_all_caddies ($object, $this->type) ;
				else {
					$res_aff_suppr_base .= aff_cart_unique_object ($object, $this->type, $url_base="./catalog.php?categ=caddie&sub=gestion&quoi=panier&idcaddie=".$this->idcaddie) ;
				}
			}
		}
		return $res_aff_suppr_base;
	}
	
	protected function write_content_tableau($worksheet) {
		global $elt_flag, $elt_no_flag, $notice_tpl;
		
		afftab_cart_objects ($this->idcaddie, $elt_flag , $elt_no_flag, $notice_tpl ) ;
	}
	
	protected function get_display_content_tableauhtml() {
		global $elt_flag, $elt_no_flag, $notice_tpl;
		
		afftab_cart_objects ($this->idcaddie, $elt_flag , $elt_no_flag, $notice_tpl ) ;
	}
	
	public function get_export_iframe($param_exp='') {
		export_param::init_session();
		$param_exp=new export_param(EXP_SESSION_CONTEXT);
		return parent::get_export_iframe($param_exp);
	}
	
	public function get_idcaddie() {
		return $this->idcaddie;
	}
	
} // fin de déclaration de la classe caddie