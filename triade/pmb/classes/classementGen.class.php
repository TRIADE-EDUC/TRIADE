<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: classementGen.class.php,v 1.6 2017-05-06 12:03:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/parser.inc.php");
require_once($include_path."/templates/classementGen.tpl.php");

class classementGen {

	// propriétés
	public $object_type = '';			//type de l'objet à classer (etagere, caddie, ...)
	public $object_id = 0;				//identifiant de l'objet à classer

	public $libelle = '';		//libellé du classement
	
	public static $classementGenListe = array(); //Liste des classements existants, passé en static pour éviter de recalculer

	// constructeur
	public function __construct($object_type, $object_id = 0) {
		
		if (!isset($_SESSION["classementGen_types"][$object_type])){
			classementGen::parseXml();
		}		
		$this->object_type = $object_type;
		$this->object_id = $object_id+0;
		if($this->object_id){
			$this->getLibelle();
		}
	}
	
	//parsage du xml des classements
	public static function parseXml(){
		global $include_path;
		
		$xmlFile=$include_path."/classementGen/classementGen.xml";
		
		// Gestion de fichier subst
		$xmlFile_subst=substr($xmlFile,0,-4)."_subst.xml";
		if (file_exists($xmlFile_subst)){
			$xmlFile=$xmlFile_subst;
		}
		
		//Parse le fichier dans un tableau
		$fp=fopen($xmlFile,"r") or die("Can't find XML file $xmlFile");
		$xml=fread($fp,filesize($xmlFile));
		fclose($fp);
		$param=_parser_text_no_function_($xml, "PMBCLASSEMENT");
		
		//Récupération des éléments
		for ($i=0; $i<count($param["TYPES"][0]["TYPE"]); $i++) {
			$p_typ=$param["TYPES"][0]["TYPE"][$i];
			$typ=array();
			$typ["TABLE"]=$p_typ["TABLE"][0]["value"];
			$typ["TABLE_ID"]=$p_typ["TABLE_ID"][0]["value"];
			$typ["TABLE_CLASSEMENT_FIELD"]=$p_typ["TABLE_CLASSEMENT_FIELD"][0]["value"];
			$typ["AUTORISATION_WHERE"]=$p_typ["AUTORISATION_WHERE"][0]["value"];
				
			$_SESSION["classementGen_types"][$p_typ["NAME"]]=$typ;
		}
	}
	
	//récupération classement de l'objet
	public function getLibelle() {
		global $dbh;

		$res = pmb_mysql_query("SELECT ".$_SESSION["classementGen_types"][$this->object_type]["TABLE_CLASSEMENT_FIELD"]." 
				FROM ".$_SESSION["classementGen_types"][$this->object_type]["TABLE"]." 
				WHERE ".$_SESSION["classementGen_types"][$this->object_type]["TABLE_ID"]."=".$this->object_id,$dbh);
		if(pmb_mysql_num_rows($res)){
			$this->libelle = pmb_mysql_result($res, 0, 0);
		}
	}
	
	//enregistrement classement de l'objet
	public function saveLibelle($value) {
		global $dbh;

		$value=trim($value);
		$this->libelle = $value;
		$res = pmb_mysql_query("UPDATE ".$_SESSION["classementGen_types"][$this->object_type]["TABLE"]." 
				SET ".$_SESSION["classementGen_types"][$this->object_type]["TABLE_CLASSEMENT_FIELD"]."='".addslashes($value)."' 
				WHERE ".$_SESSION["classementGen_types"][$this->object_type]["TABLE_ID"]."=".$this->object_id,$dbh);
	}

	//affichage sélecteur de classement
	public function show_selector($url_callback,$user_id) {
		global $msg,$classementGen_selector;
		
		$to_show = $classementGen_selector;
		$to_show = str_replace("!!object_id!!",$this->object_id,$to_show);
		$to_show = str_replace("!!object_type!!",$this->object_type,$to_show);
		$to_show = str_replace("!!classements_liste!!",$this->getClassementsSelectorContent($user_id,$this->libelle),$to_show);
		$to_show = str_replace("!!msg_object_classement!!",$msg[$this->object_type.'_classement_list'],$to_show);
		$to_show = str_replace("!!msg_object_classement_save!!",$msg[$this->object_type."_classement_save"],$to_show);
		$to_show = str_replace("!!url_callback!!",$url_callback,$to_show);
		
		return $to_show;
	}
	
	public function getClassementsSelectorContent($user_id,$classement_selected='') {
		global $charset;
		
		$listeClassements = "";
		if(!$classement_selected){
			$listeClassements .= "<option value='' selected='selected'></option>";
		}
		$arrayClassements = $this->getClassementsList($user_id);
		if(count($arrayClassements)){
			foreach($arrayClassements as $value){
				if($classement_selected==$value){
					$selected=" selected='selected' ";
				}else{
					$selected="";
				}
				$listeClassements .= "<option value='".htmlentities($value ,ENT_QUOTES, $charset)."' $selected>".htmlentities(stripslashes($value) ,ENT_QUOTES, $charset)."</option>";
			}
		}
		return $listeClassements;
	}
	
	//Liste des classements disponibles pour le type
	public function getClassementsList($user_id) {
		global $dbh;
		
		if(!isset(static::$classementGenListe[0])){

			$requete = "SELECT DISTINCT ".$_SESSION["classementGen_types"][$this->object_type]["TABLE_CLASSEMENT_FIELD"]."
				FROM ".$_SESSION["classementGen_types"][$this->object_type]["TABLE"]."
				WHERE ".$_SESSION["classementGen_types"][$this->object_type]["TABLE_CLASSEMENT_FIELD"]."<>'' ";
			if($autorisation_where=trim($_SESSION["classementGen_types"][$this->object_type]["AUTORISATION_WHERE"])){
				$requete.="AND ".str_replace("!!id!!",$user_id,$autorisation_where)." ";
			}
			$requete.= "ORDER BY 1";
			
			$res = pmb_mysql_query($requete,$dbh);
			if(pmb_mysql_num_rows($res)){
				while ($row = pmb_mysql_fetch_array($res)){
					static::$classementGenListe[]=$row[0];
				}
			}else{
				static::$classementGenListe=array();
			}
		}

		return static::$classementGenListe;
	}
	
	//Libellé "Aucun classement" par défaut
	public static function getDefaultLibelle(){
		global $msg;
		
		return $msg["classementGen_default_libelle"];
	}
	
	//Affiche la liste des classements selon les droits de l'utilisateur
	public function show_list_classements($user_id,$baseLink){
		global $msg,$charset;
		global $classementGen_list_table_header, $classementGen_list_table_row, $classementGen_list_table_footer;
		
		$arrayClassements = $this->getClassementsList($user_id);

		if(count($arrayClassements)){
			$to_show=str_replace("!!title!!",$msg["classementGen_list_title"],$classementGen_list_table_header);
			$parity=1;
			foreach($arrayClassements as $value){
				$value=stripslashes($value);
				if($parity % 2){
					$pair_impair = "even";
				}else{
					$pair_impair = "odd";
				}
				$parity += 1;
				$tr_js=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" onmousedown=\"document.location='".$baseLink."&action=edit&classement=".urlencode($value)."';\" ";
				$trow = $classementGen_list_table_row;
				$trow = str_replace("!!tr_class!!",$pair_impair,$trow);
				$trow = str_replace("!!tr_js!!",$tr_js,$trow);
				$trow = str_replace("!!td_lib!!",htmlentities($value,ENT_QUOTES,$charset),$trow);
				$to_show .= $trow;
			}
			$to_show .= $classementGen_list_table_footer;
		}else{
			$to_show = $msg["classementGen_list_no_entry"];
		}

		return $to_show;
	}
	
	//Formulaire d'édition du classement
	public function show_form_edit_classement($value,$baseLink){
		global $classementGen_form_edit,$msg,$charset;
		
		$value=stripslashes($value);
		
		$to_show = $classementGen_form_edit;		
		$to_show = str_replace("!!newValue!!",stripslashes(htmlentities($value,ENT_QUOTES,$charset)),$to_show);
		$to_show = str_replace("!!oldValue!!",htmlentities($value,ENT_QUOTES,$charset),$to_show);
		$to_show = str_replace("!!form_title!!",$msg["classementGen_list_form_title"],$to_show);
		$to_show = str_replace("!!form_libelle!!",$msg["classementGen_list_form_libelle"],$to_show);
		$to_show = str_replace("!!back_link!!",$baseLink,$to_show);
		$to_show = str_replace("!!action_link!!",$baseLink."&action=update",$to_show);
		$to_show = str_replace("!!suppr_link!!",$baseLink."&action=delete",$to_show);
		
		return $to_show;
	}
	
	//Mise à jour du libellé du classement
	public function update_classement($oldClassement,$newClassement,$user_id){
		global $dbh;
		
		if($oldClassement!==$newClassement){
			$requete = "UPDATE ".$_SESSION["classementGen_types"][$this->object_type]["TABLE"]." 
					SET ".$_SESSION["classementGen_types"][$this->object_type]["TABLE_CLASSEMENT_FIELD"]."='".addslashes($newClassement)."' 
					WHERE ".$_SESSION["classementGen_types"][$this->object_type]["TABLE_CLASSEMENT_FIELD"]."='".addslashes($oldClassement)."' ";
			if($autorisation_where=trim($_SESSION["classementGen_types"][$this->object_type]["AUTORISATION_WHERE"])){
				$requete.="AND ".str_replace("!!id!!",$user_id,$autorisation_where)." ";
			}
	
			pmb_mysql_query($requete,$dbh);
		}
		
		return;
	}
	
	//Suppression du libellé du classement
	public function delete_classement($oldClassement,$user_id){
		global $dbh;
	
		$requete = "UPDATE ".$_SESSION["classementGen_types"][$this->object_type]["TABLE"]."
				SET ".$_SESSION["classementGen_types"][$this->object_type]["TABLE_CLASSEMENT_FIELD"]."=''
				WHERE ".$_SESSION["classementGen_types"][$this->object_type]["TABLE_CLASSEMENT_FIELD"]."='".addslashes($oldClassement)."' ";
		if($autorisation_where=trim($_SESSION["classementGen_types"][$this->object_type]["AUTORISATION_WHERE"])){
			$requete.="AND ".str_replace("!!id!!",$user_id,$autorisation_where)." ";
		}

		pmb_mysql_query($requete,$dbh);
	
		return;
	}
	
	public function proceed($action) {
		global $PMBuserid;
		global $baseLink;
		global $classement, $oldClassement, $newClassement;
		
		switch($action){
			case "edit" :
				print $this->show_form_edit_classement($classement,$baseLink);
				break;
			case "update" :
				$this->update_classement($oldClassement,$newClassement,$PMBuserid);
				print $this->show_list_classements($PMBuserid,$baseLink);
				break;
			case "delete" :
				$this->delete_classement($oldClassement,$PMBuserid);
				print $this->show_list_classements($PMBuserid,$baseLink);
				break;
			default :
				print $this->show_list_classements($PMBuserid,$baseLink);
				break;
		}
	}
 
} //fin de déclaration du fichier classement.class.php