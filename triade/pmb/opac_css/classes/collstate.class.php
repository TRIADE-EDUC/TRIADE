<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collstate.class.php,v 1.27 2019-05-29 09:17:21 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes de gestion des états de collection de périodique

require_once($class_path."/parametres_perso.class.php");
require_once($include_path."/templates/collstate.tpl.php");

class collstate {

	// classe de la notice chapeau des périodiques
	public $id = 0;       // id de l'état de collection
	public $serial_id = 0;         // id du périodique lié
	public $bulletin_id = 0;		// id du bulletin lié
	public $location_id = 0;
	public $location_libelle = '';
	public $state_collections = '';
	public $emplacement	= 0;
	public $emplacement_libelle = '';
	public $type = 0;
	public $type_libelle = '';
	public $origine = '';
	public $note = '';
	public $cote = '';
	public $archive = '';
	public $lacune = '';
	public $statut = 0;
	public $statut_gestion_libelle = '';
	public $statut_opac_libelle = '';
	public $statut_visible_opac = '';
	public $statut_visible_opac_abon = '';
	public $statut_visible_gestion = '';
	public $statut_class_html = '';
	public $num_infopage = 0;
	public $surloc_id		= 0;
	public $surloc_libelle = '';
	public $bulletins = array();
	
	// constructeur
	public function __construct($id=0,$serial_id=0,$bulletin_id=0) {
		// si id, allez chercher les infos dans la base
		$id = $id*1;
		$serial_id = $serial_id*1;
		$bulletin_id = $bulletin_id*1;
		if($id) {
			$this->id = $id;
			$this->fetch_data();
		}
		if($serial_id) {
			$this->serial_id = $serial_id;
		}
		if($bulletin_id) {
			$this->bulletin_id = $bulletin_id;
		}
	}
    
	// récupération des infos en base
	public function fetch_data() {
		global $dbh;
		global $opac_sur_location_activate;
	
		$myQuery = pmb_mysql_query("SELECT * FROM collections_state WHERE collstate_id='".$this->id."' LIMIT 1", $dbh);
		$mycoll= pmb_mysql_fetch_object($myQuery);
	
		$this->serial_id=$mycoll->id_serial;
		$this->location_id=$mycoll->location_id;
		$this->state_collections=$mycoll->state_collections;
		$this->emplacement=$mycoll->collstate_emplacement;
		$this->type=$mycoll->collstate_type;
		$this->origine=$mycoll->collstate_origine;
		$this->note=$mycoll->collstate_note;
		$this->cote=$mycoll->collstate_cote;
		$this->archive=$mycoll->collstate_archive;
		$this->lacune=$mycoll->collstate_lacune;
		$this->statut=$mycoll->collstate_statut;
	
		$myQuery = pmb_mysql_query("SELECT * FROM arch_emplacement WHERE archempla_id='".$this->emplacement."' LIMIT 1", $dbh);
		$myempl= pmb_mysql_fetch_object($myQuery);
		$this->emplacement_libelle=$myempl->archempla_libelle;
	
		$myQuery = pmb_mysql_query("SELECT * FROM arch_type WHERE archtype_id='".$this->type."' LIMIT 1", $dbh);
		$mytype = pmb_mysql_fetch_object($myQuery);
		$this->type_libelle=$mytype->archtype_libelle;
	
		// Lecture des statuts
		$myQuery = pmb_mysql_query("SELECT * FROM arch_statut WHERE archstatut_id='".$this->statut."' LIMIT 1", $dbh);
		$mystatut = pmb_mysql_fetch_object($myQuery);
		$this->statut_gestion_libelle=$mystatut->archstatut_gestion_libelle;
		$this->statut_opac_libelle=$mystatut->archstatut_opac_libelle;
		$this->statut_visible_opac=$mystatut->archstatut_visible_opac;
		$this->statut_visible_opac_abon=$mystatut->archstatut_visible_opac_abon;
		$this->statut_visible_gestion=$mystatut->archstatut_visible_gestion;
		$this->statut_class_html=$mystatut->archstatut_class_html;
		
		$myQuery = pmb_mysql_query("select location_libelle, num_infopage, surloc_num from docs_location where idlocation='".$this->location_id."' LIMIT 1", $dbh);
		$mylocation= pmb_mysql_fetch_object($myQuery);
		$this->location_libelle=$mylocation->location_libelle;
		$this->num_infopage=$mylocation->num_infopage;
		
		if ($opac_sur_location_activate && $this->surloc_id) {
			$this->surloc_id = $mylocation->surloc_num;
			$myQuery = pmb_mysql_query("select surloc_libelle from sur_location where surloc_id='".$this->surloc_id."' LIMIT 1", $dbh);
			$mysurloc = pmb_mysql_fetch_object($myQuery);
			$this->surloc_libelle=$mysurloc->surloc_libelle;
		}
		
		$myQuery = pmb_mysql_query("SELECT collstate_bulletins_num_bulletin FROM collstate_bulletins WHERE collstate_bulletins_num_collstate='".$this->id."'", $dbh);
		while($mybulletins = pmb_mysql_fetch_object($myQuery)) {
			$this->bulletins[] = $mybulletins->collstate_bulletins_num_bulletin;
		}
	}
	
	public function get_collstate_datas($filtre='') {
		global $dbh;
		global $opac_view_filter_class;
		global $opac_collstate_data, $opac_collstate_order;
	
		$location=$filtre->location;
		$query = "SELECT collstate_id FROM arch_statut
			JOIN collections_state ON archstatut_id=collstate_statut and ((archstatut_visible_opac=1 and archstatut_visible_opac_abon=0)".( $_SESSION["user_code"]? " or (archstatut_visible_opac_abon=1 and archstatut_visible_opac=1)" : "").") ";
		if($this->bulletin_id) {
			$query .= "JOIN collstate_bulletins ON collstate_bulletins_num_collstate = collstate_id ";
		}
		if($opac_view_filter_class){
			if(!$opac_view_filter_class->params["nav_collections"]){
				$opac_view_filter_class->params["nav_collections"][0]="0";
			}
			$query .= "JOIN docs_location ON location_id=idlocation and idlocation in (". implode(",",$opac_view_filter_class->params["nav_collections"]).") ";
		} else {
			$query .= "LEFT JOIN docs_location ON location_id = idlocation ";
		}
		$query .="LEFT JOIN sur_location on docs_location.surloc_num=surloc_id
			LEFT JOIN arch_emplacement ON collstate_emplacement=archempla_id
			WHERE ".($location?"(location_id='$location') and ":"");
		if ($this->bulletin_id) {
			$query .= "collstate_bulletins_num_bulletin='".$this->bulletin_id."' ";
		} else {
			$query .= "id_serial='".$this->serial_id."' ";
		}
		if ($opac_collstate_order) $query .= " ORDER BY ".$opac_collstate_order;
		else $query .= " ORDER BY ".($type?"location_libelle, ":"")."archempla_libelle, collstate_cote";
		
		$myQuery = pmb_mysql_query($query, $dbh);
		$datas = array();		
		if(pmb_mysql_num_rows($myQuery)) {			
			while($coll = pmb_mysql_fetch_object($myQuery)) {
				$datas[] = new collstate($coll->collstate_id);
			}
		}
		return $datas;
	}
		
	//Récupérer de l'affichage complet
	public function get_display_list($base_url,$filtre,$debut=0,$page=0, $type=0) {
		global $dbh, $msg,$nb_per_page_a_search,$tpl_collstate_liste,$tpl_collstate_liste_line, $tpl_collstate_surloc_liste, $tpl_collstate_surloc_liste_line;
		global $opac_sur_location_activate, $opac_view_filter_class;
		global $collstate_list_header, $collstate_list_footer;
		global $opac_collstate_data, $opac_collstate_order, $opac_url_base;
		global $charset,$class_path;
		global $pmb_collstate_advanced, $tpl_collstate_bulletins_list_th, $tpl_collstate_bulletins_list_td;
		
		if(is_object($filtre)) {
			$location=$filtre->location;
		} else {
			$location="";
		}
		$query = "SELECT  collstate_id , location_id, num_infopage, surloc_id FROM arch_statut
			JOIN collections_state ON archstatut_id=collstate_statut and ((archstatut_visible_opac=1 and archstatut_visible_opac_abon=0)".( $_SESSION["user_code"]? " or (archstatut_visible_opac_abon=1 and archstatut_visible_opac=1)" : "").") ";
		if($this->bulletin_id) {
			$query .= "JOIN collstate_bulletins ON collstate_bulletins_num_collstate = collstate_id ";
		}
		if($opac_view_filter_class){
			if(!$opac_view_filter_class->params["nav_collections"]){
				$opac_view_filter_class->params["nav_collections"][0]="0";
			}
			$query .= "JOIN docs_location ON location_id=idlocation and idlocation in (". implode(",",$opac_view_filter_class->params["nav_collections"]).") "; 
		} else {
			$query .= "LEFT JOIN docs_location ON location_id = idlocation ";
		}	
		$query .="LEFT JOIN sur_location on docs_location.surloc_num=surloc_id
			LEFT JOIN arch_emplacement ON collstate_emplacement=archempla_id	
			WHERE ".($location?"(location_id='$location') and ":"");
		if ($this->bulletin_id) {
			$query .= "collstate_bulletins_num_bulletin='".$this->bulletin_id."' ";
		} else {
			$query .= "id_serial='".$this->serial_id."' ";
		}
		if ($opac_collstate_order) $query .= " ORDER BY ".$opac_collstate_order;
		else $query .= " ORDER BY ".($type?"location_libelle, ":"")."archempla_libelle, collstate_cote";
		$myQuery = pmb_mysql_query($query, $dbh);
		
		if((!pmb_mysql_error() && ($this->nbr = pmb_mysql_num_rows($myQuery)))) {
			
			if ($opac_sur_location_activate) {
				$tpl_collstate_liste[$type] = str_replace('<!-- surloc -->',$tpl_collstate_surloc_liste,$tpl_collstate_liste[$type]);
				$tpl_collstate_liste_line[$type] = str_replace('<!-- surloc -->',$tpl_collstate_surloc_liste_line,$tpl_collstate_liste_line[$type]);
			}
			
			if ($opac_collstate_data) {
				if (strstr($opac_collstate_data, "#")) {
					require_once($class_path."/parametres_perso.class.php");
					$cp=new parametres_perso("collstate");
				}
				$colonnesarray=explode(",",$opac_collstate_data);
				$collstate_list_header_deb="<tr>";
				
				for ($i=0; $i<count($colonnesarray); $i++) {
					if (substr($colonnesarray[$i],0,1)=="#") {
						//champs personnalisés
						$id=substr($colonnesarray[$i],1);
						if (!$cp->no_special_fields) {
							$collstate_list_header_deb.="<th class='collstate_header_cp_".str_replace('#','',$colonnesarray[$i])."'>".htmlentities($cp->t_fields[$id]["TITRE"],ENT_QUOTES, $charset)."</th>";
						}
					}else{
						eval ("\$colencours=\$msg['collstate_header_".$colonnesarray[$i]."'];");
						$collstate_list_header_deb.="<th class='collstate_header_".$colonnesarray[$i]."'>".htmlentities($colencours,ENT_QUOTES, $charset)."</th>";
					}
				}
				$collstate_list_header_deb.= "!!collstate_bulletins_list_th!!";
				$collstate_list_header_deb.="</tr>";
			}
			$parity=1;
			$liste="";
			while(($coll = pmb_mysql_fetch_object($myQuery))) {
				$my_collstate=new collstate($coll->collstate_id);
				if ($parity++ % 2) $pair_impair = "even"; else $pair_impair = "odd";
 				$tr_surbrillance = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
 				if($opac_collstate_data) {	
 					$liste.="<tr class='".$pair_impair."' ".$tr_surbrillance." >";
					$colencours="";
					for ($i=0; $i<count($colonnesarray); $i++) {
						if (substr($colonnesarray[$i],0,1)=="#") {
							//champs personnalisés
							$id=substr($colonnesarray[$i],1);
							$cp->get_values($coll->collstate_id);
							if (!$cp->no_special_fields) {
								$temp=$cp->get_formatted_output($cp->values[$id], $id);
								if (!$temp) $temp=" ";
								$liste.="<td class='".$colonnesarray[$i]."' >".htmlentities($temp,ENT_QUOTES, $charset)."</td>";
							}
						}else{
							eval ("\$colencours=\$my_collstate->".$colonnesarray[$i].";");
							if ($colonnesarray[$i]=="location_libelle" && $my_collstate->num_infopage) {
								if ($my_collstate->surloc_id != "0") $param_surloc="&surloc=".$my_collstate->surloc_id;
								else $param_surloc="";
								$collstate_location = "<a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$my_collstate->num_infopage."&location=".$my_collstate->location_id.$param_surloc."\" title=\"".$msg['location_more_info']."\">".$my_collstate->location_libelle."</a>";
								$liste.="<td class='".$colonnesarray[$i]."'>".$collstate_location."</td>";
							} else {
								$liste.="<td class='".$colonnesarray[$i]."'>".htmlentities($colencours,ENT_QUOTES, $charset)."</td>";
							}
						}
					}
					if ($pmb_collstate_advanced) {
						$bulletins_list_onclick = str_replace('!!collstate_bulletins_list_onclick!!', 'document.location="'.$opac_url_base.'index.php?lvl=collstate_bulletins_display'.($my_collstate->id ? '&id='.$my_collstate->id : '').($this->serial_id ? '&serial_id='.$this->serial_id : '').($this->bulletin_id ? '&bulletin_id='.$this->bulletin_id : '').'";', $tpl_collstate_bulletins_list_td);
						$liste.= $bulletins_list_onclick;
					}
					$liste.= "</tr>";
				} else {
					$line = str_replace('!!tr_javascript!!','' , $tpl_collstate_liste_line[$type]);
					$line = str_replace('!!tr_surbrillance!!',$tr_surbrillance , $line);
					$line = str_replace('!!pair_impair!!',$pair_impair , $line);
					if ($opac_sur_location_activate) {
						$line = str_replace('!!surloc!!', $my_collstate->surloc_libelle, $line);
					}
					if ($my_collstate->num_infopage) {
						if ($my_collstate->surloc_id != "0") $param_surloc="&surloc=".$my_collstate->surloc_id;
						else $param_surloc="";
						$collstate_location = "<a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$my_collstate->num_infopage."&location=".$my_collstate->location_id.$param_surloc."\" title=\"".$msg['location_more_info']."\">".$my_collstate->location_libelle."</a>";
					} else
						$collstate_location = $my_collstate->location_libelle;
					$line = str_replace('!!localisation!!', $collstate_location, $line);
					$line = str_replace('!!cote!!', $my_collstate->cote, $line);
					$line = str_replace('!!type_libelle!!', $my_collstate->type_libelle, $line);
					$line = str_replace('!!emplacement_libelle!!', $my_collstate->emplacement_libelle, $line);
					$line = str_replace('!!statut_libelle!!', $my_collstate->statut_opac_libelle, $line);
					$line = str_replace('!!origine!!', $my_collstate->origine, $line);
					$line = str_replace('!!state_collections!!',str_replace("\n","<br />",$my_collstate->state_collections), $line);
					$line = str_replace('!!archive!!',$my_collstate->archive, $line);
					$line = str_replace('!!lacune!!', str_replace("\n","<br />",$my_collstate->lacune), $line);
					if ($pmb_collstate_advanced) {
						$bulletins_list_onclick = str_replace('!!collstate_bulletins_list_onclick!!', 'document.location="'.$opac_url_base.'index.php?lvl=collstate_bulletins_display'.($my_collstate->id ? '&id='.$my_collstate->id : '').($this->serial_id ? '&serial_id='.$this->serial_id : '').($this->bulletin_id ? '&bulletin_id='.$this->bulletin_id : '').'";', $tpl_collstate_bulletins_list_td);
						$line = str_replace('!!collstate_bulletins_list_td!!', $bulletins_list_onclick, $line);
					} else {
						$line = str_replace('!!collstate_bulletins_list_td!!', '', $line);
					}
					$liste.= $line;
				}
			}
			if($opac_collstate_data) {
				$liste = $collstate_list_header.$collstate_list_header_deb.$liste.$collstate_list_footer;
			} else {
				$liste = str_replace('!!collstate_liste!!',$liste , $tpl_collstate_liste[$type]);
				$liste = str_replace('!!base_url!!', $base_url, $liste);
				$liste = str_replace('!!location!!', $location, $liste);
			}
			if ($pmb_collstate_advanced) {
				$liste = str_replace('!!collstate_bulletins_list_th!!', $tpl_collstate_bulletins_list_th, $liste);
			} else {
				$liste = str_replace('!!collstate_bulletins_list_th!!', '', $liste);
			}
		} else {
			$liste = $msg["collstate_no_collstate"];
		}
		$this->liste = $liste;
	}

	public function get_collstate_bulletins_display() {
		global $tpl_collstate_bulletins_list_page, $tpl_collstate_bulletins_list_page_collstate_line;
		global $msg;
		
		$html = $tpl_collstate_bulletins_list_page;
		$line = '';
		if ($this->location_libelle) {
			$line = str_replace('!!label!!', $msg["collstate_form_localisation"], $tpl_collstate_bulletins_list_page_collstate_line);
			$line = str_replace('!!value!!', $this->location_libelle, $line);
		}
		$html = str_replace('!!localisation!!', $line, $html);

		$line = '';
		if ($this->emplacement_libelle) {
			$line = str_replace('!!label!!', $msg["collstate_form_emplacement"], $tpl_collstate_bulletins_list_page_collstate_line);
			$line = str_replace('!!value!!', $this->emplacement_libelle, $line);
		}
		$html = str_replace('!!emplacement_libelle!!', $line, $html);

		$line = '';
		if ($this->cote) {
			$line = str_replace('!!label!!', $msg["collstate_form_cote"], $tpl_collstate_bulletins_list_page_collstate_line);
			$line = str_replace('!!value!!', $this->cote, $line);
		}
		$html = str_replace('!!cote!!', $line, $html);

		$line = '';
		if ($this->type_libelle) {
			$line = str_replace('!!label!!', $msg["collstate_form_support"], $tpl_collstate_bulletins_list_page_collstate_line);
			$line = str_replace('!!value!!', $this->type_libelle, $line);
		}
		$html = str_replace('!!type_libelle!!', $line, $html);

		$line = '';
		if ($this->statut_opac_libelle) {
			$line = str_replace('!!label!!', $msg["collstate_form_statut"], $tpl_collstate_bulletins_list_page_collstate_line);
			$line = str_replace('!!value!!', $this->statut_opac_libelle, $line);
		}
		$html = str_replace('!!statut_libelle!!', $line, $html);

		$line = '';
		if ($this->origine) {
			$line = str_replace('!!label!!', $msg["collstate_form_origine"], $tpl_collstate_bulletins_list_page_collstate_line);
			$line = str_replace('!!value!!', $this->origine, $line);
		}
		$html = str_replace('!!origine!!', $line, $html);

		$line = '';
		if ($this->state_collections) {
			$line = str_replace('!!label!!', $msg["collstate_form_collections"], $tpl_collstate_bulletins_list_page_collstate_line);
			$line = str_replace('!!value!!', $this->state_collections, $line);
		}
		$html = str_replace('!!state_collections!!', $line, $html);

		$line = '';
		if ($this->archive) {
			$line = str_replace('!!label!!', $msg["collstate_form_archive"], $tpl_collstate_bulletins_list_page_collstate_line);
			$line = str_replace('!!value!!', $this->archive, $line);
		}
		$html = str_replace('!!archive!!', $line, $html);

		$line = '';
		if ($this->lacune) {
			$line = str_replace('!!label!!', $msg["collstate_form_lacune"], $tpl_collstate_bulletins_list_page_collstate_line);
			$line = str_replace('!!value!!', $this->lacune, $line);
		}
		$html = str_replace('!!lacune!!', $line, $html);
		
		$bulletins_list = '';
		if (count($this->bulletins)) {
			$bulletins_list = record_display::get_display_bulletins_list(0, $this->bulletins);
		}
		$html = str_replace('!!bulletins_list!!', $bulletins_list, $html);
		
		return $html;
	}
} // fin définition classe
