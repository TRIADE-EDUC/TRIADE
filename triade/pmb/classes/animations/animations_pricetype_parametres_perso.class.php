<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: animations_pricetype_parametres_perso.class.php,v 1.6.4.2 2025/03/13 16:27:12 qvarin Exp $

use Pmb\Animations\Models\PriceTypeModel;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/translation.class.php");

class animations_pricetype_parametres_perso extends parametres_perso {
    public $num_type;
    public $option_visibilite;
    public $base_url;

    public function  __construct($num_price_type,$base_url="", $option_visibilite=array()) {
		global $_custom_prefixe_;

		$this->option_visibilite = $option_visibilite;

		$this->base_url = $base_url;
		$this->prefix="anim_price_type";
		$_custom_prefixe_="anim_price_type";

		$this->num_type = intval($num_price_type);

		$this->fetch_data();
	}

	protected function fetch_data(){
		global $charset;

		//Lecture des champs
		$this->no_special_fields=0;
		$this->t_fields=array();
		if(!isset(self::$st_fields[$this->prefix.'_'.$this->num_type])){
			$requete="select idchamp, name, titre, type, datatype, obligatoire, options, multiple, search, export, exclusion_obligatoire, pond, opac_sort, comment from ".$this->prefix."_custom where num_type = '".$this->num_type."' order by ordre";

			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)==0)
				self::$st_fields[$this->prefix.'_'.$this->num_type] = false;
			else {
				while ($r=pmb_mysql_fetch_object($resultat)) {
					self::$st_fields[$this->prefix.'_'.$this->num_type][$r->idchamp]["DATATYPE"]=$r->datatype;
					self::$st_fields[$this->prefix.'_'.$this->num_type][$r->idchamp]["NAME"]=$r->name;
					self::$st_fields[$this->prefix.'_'.$this->num_type][$r->idchamp]["TITRE"]=$r->titre;
					self::$st_fields[$this->prefix.'_'.$this->num_type][$r->idchamp]["TYPE"]=$r->type;
					self::$st_fields[$this->prefix.'_'.$this->num_type][$r->idchamp]["OPTIONS"][0] =_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$r->options, "OPTIONS");
					self::$st_fields[$this->prefix.'_'.$this->num_type][$r->idchamp]["MANDATORY"]=$r->obligatoire;
					self::$st_fields[$this->prefix.'_'.$this->num_type][$r->idchamp]["OPAC_SHOW"]=$r->multiple;
					self::$st_fields[$this->prefix.'_'.$this->num_type][$r->idchamp]["SEARCH"]=$r->search;
					self::$st_fields[$this->prefix.'_'.$this->num_type][$r->idchamp]["EXPORT"]=$r->export;
					self::$st_fields[$this->prefix.'_'.$this->num_type][$r->idchamp]["EXCLUSION"]=$r->exclusion_obligatoire;
					self::$st_fields[$this->prefix.'_'.$this->num_type][$r->idchamp]["POND"]=$r->pond;
					self::$st_fields[$this->prefix.'_'.$this->num_type][$r->idchamp]["OPAC_SORT"]=$r->opac_sort;
					self::$st_fields[$this->prefix.'_'.$this->num_type][$r->idchamp]["COMMENT"]=$r->comment;
				}
			}
		}
		if(self::$st_fields[$this->prefix.'_'.$this->num_type] == false){
			$this->no_special_fields=1;
		}else{
			$this->t_fields=self::$st_fields[$this->prefix.'_'.$this->num_type];
		}
	}

	//Gestion des actions en administration
	public function proceed() {
		global $action;
		global $name,$titre,$type,$datatype,$_options,$multiple,$obligatoire,$search,$export,$exclusion,$ordre,$idchamp,$id,$pond,$opac_sort,$comment,$classement;
		switch ($action) {
		    case "nouv":
		        $this->show_edit_form();
		        break;
		    case "edit":
		        $this->show_edit_form($id);
		        break;
			case "create":
				$this->check_form();
				$requete="select max(ordre) from ".$this->prefix."_custom where num_type = ".$this->num_type;
				$resultat=pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($resultat)!=0)
					$ordre=pmb_mysql_result($resultat,0,0)+1;
				else
					$ordre=1;

				$requete="insert into ".$this->prefix."_custom set num_type = '$this->num_type', name='$name', titre='$titre', type='$type', datatype='$datatype', options='$_options', multiple=$multiple, obligatoire=$obligatoire, ordre=".intval($ordre).", search=$search, export=$export, exclusion_obligatoire=$exclusion, opac_sort=$opac_sort, comment='".$comment."', custom_classement='".$classement."' ";
				pmb_mysql_query($requete);
				$idchamp = pmb_mysql_insert_id();
				$translation = new translation($idchamp, $this->prefix."_custom");
				$translation->update("titre");
				echo $this->show_field_list();
				break;
			case "update":
				$this->check_form();
				$requete="update ".$this->prefix."_custom set name='$name', titre='$titre', type='$type', datatype='$datatype', options='$_options', multiple=$multiple, obligatoire=$obligatoire, ordre=".intval($ordre).", search=$search, export=$export, exclusion_obligatoire=$exclusion, pond=$pond, opac_sort=$opac_sort, comment='".$comment."', custom_classement='".$classement."' where idchamp=$idchamp";
				pmb_mysql_query($requete);
				$translation = new translation($idchamp, $this->prefix."_custom");
				$translation->update("titre");
				echo $this->show_field_list();
				break;
			case "up":
				$requete="select ordre from ".$this->prefix."_custom where idchamp=$id";
				$resultat=pmb_mysql_query($requete);
				$ordre=pmb_mysql_result($resultat,0,0);
				$requete="select max(ordre) as ordre from ".$this->prefix."_custom where ordre<$ordre and num_type = ".$this->num_type;
				$resultat=pmb_mysql_query($requete);
				$ordre_max=@pmb_mysql_result($resultat,0,0);
				if ($ordre_max) {
					$requete="select idchamp from ".$this->prefix."_custom where ordre=$ordre_max and num_type = ".$this->num_type." limit 1";
					$resultat=pmb_mysql_query($requete);
					$idchamp_max=pmb_mysql_result($resultat,0,0);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre_max."' where idchamp=$id and num_type = ".$this->num_type;
					pmb_mysql_query($requete);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre."' where idchamp=".$idchamp_max." and num_type = ".$this->num_type;
					pmb_mysql_query($requete);
				}
				echo $this->show_field_list();
				break;
			case "down":
				$requete="select ordre from ".$this->prefix."_custom where idchamp=$id";
				$resultat=pmb_mysql_query($requete);
				$ordre=pmb_mysql_result($resultat,0,0);
				$requete="select min(ordre) as ordre from ".$this->prefix."_custom where ordre>$ordre and num_type = ".$this->num_type;
				$resultat=pmb_mysql_query($requete);
				$ordre_min=@pmb_mysql_result($resultat,0,0);
				if ($ordre_min) {
					$requete="select idchamp from ".$this->prefix."_custom where ordre=$ordre_min and num_type = ".$this->num_type." limit 1";
					$resultat=pmb_mysql_query($requete);
					$idchamp_min=pmb_mysql_result($resultat,0,0);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre_min."' where idchamp=$id and num_type = ".$this->num_type;
					pmb_mysql_query($requete);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre."' where idchamp=".$idchamp_min." and num_type = ".$this->num_type;
					pmb_mysql_query($requete);
				}
				echo $this->show_field_list();
				break;
			case "delete":
				$requete="delete from ".$this->prefix."_custom where idchamp=$idchamp";
				pmb_mysql_query($requete);
				$requete="delete from ".$this->prefix."_custom_values where ".$this->prefix."_custom_champ=$idchamp";
				pmb_mysql_query($requete);
				$requete="delete from ".$this->prefix."_custom_lists where ".$this->prefix."_custom_champ=$idchamp";
				pmb_mysql_query($requete);
				translation::delete($idchamp, $this->prefix."_custom", "titre");
				echo $this->show_field_list();
				break;
			default:
				echo $this->show_field_list();
		}
	}

	public function delete_all(){
		$query = "select idchamp from ".$this->prefix."_custom where num_type = ".$this->num_type;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while ($row = pmb_mysql_fetch_object($result)){
				$requete="delete from ".$this->prefix."_custom where idchamp=$row->idchamp";
				pmb_mysql_query($requete);
				$requete="delete from ".$this->prefix."_custom_values where ".$this->prefix."_custom_champ=$row->idchamp";
				pmb_mysql_query($requete);
				$requete="delete from ".$this->prefix."_custom_lists where ".$this->prefix."_custom_champ=$row->idchamp";
				pmb_mysql_query($requete);
			}
		}
	}

	//Suppression de la base des valeurs d'un emprunteur ou autre...
	public function delete_values($id,$type="") {
		$id = intval($id);
		if ($type && method_exists($this, "get_generic_type")) {
			//on va chercher les champs génériques
			$generic_type = $this->get_generic_type($type);
			if($generic_type){
				$generic = new cms_editorial_parametres_perso($generic_type);
				$generic->delete_values($id);
			}
		}
		//on récupère la liste des champs associés...
		$query = "select idchamp from ".$this->prefix."_custom where num_type = ".$this->num_type;
		$result = pmb_mysql_query($query);
		$idchamp = "";
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				if($idchamp) $idchamp.=",";
				$idchamp.=$row->idchamp;
			}
		}
		if(!$idchamp) $idchamp="''";

		$requete = "DELETE FROM ".$this->prefix."_custom_values where ".$this->prefix."_custom_champ in (".$idchamp.") and ".$this->prefix."_custom_origine=$id";
		$res = pmb_mysql_query($requete);
	}

	public function get_num_type(){
		return $this->num_type;
	}

	//Affichage de l'écran de gestion des paramètres perso (la liste de tous les champs définis)
	public function show_field_list() {
	    global $msg;

        $price_type = new PriceTypeModel($this->num_type);
	    $display = '';

	    $display .= "<h3>".sprintf($msg['admin_animations_priceTypes_definition'],$price_type->name)."</h3>";

	    list_custom_fields_animations_ui::set_prefix($this->prefix);
	    list_custom_fields_animations_ui::set_num_type($this->num_type);
	    list_custom_fields_animations_ui::set_option_visibilite($this->option_visibilite);

	    $list_custom_fields_animations_ui = new list_custom_fields_animations_ui();

	    $display .= $list_custom_fields_animations_ui->get_display_list();

	    $display .= "<br />";

	    $display .= "&nbsp;<input type='button' class='bouton' value=' ".$msg['admin_animations_priceTypesPerso_back']." ' onclick='document.location=\"./admin.php?categ=animations&sub=priceTypes&action=list\"'/>";
	    return $display;
	}
}