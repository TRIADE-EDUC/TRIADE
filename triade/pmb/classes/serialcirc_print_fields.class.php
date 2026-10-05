<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_print_fields.class.php,v 1.21 2021/12/21 15:01:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/parametres_perso.class.php");

class serialcirc_print_fields {
	public $id=0;
	public $circ_tpl=array();
	public $piedpage="";
	
	public function __construct($id_serialcirc=0) {
		$this->id = intval($id_serialcirc);
		$this->fetch_data();
	}
	
	public function fetch_data() {
		$this->p_perso = new parametres_perso("empr");
		$this->circ_tpl=array();
		$this->piedpage="";
		$requete="select * from serialcirc where id_serialcirc=".$this->id ;
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);
			if($r->serialcirc_tpl) {
				//On va récupérer le template prédéfini
				if (intval($r->serialcirc_tpl) > 0) {
					$query = "select * from serialcirc_tpl where serialcirctpl_id=".$r->serialcirc_tpl;
					$res = pmb_mysql_query($query);
					if ($res) {
						$row=pmb_mysql_fetch_object($res);
						if($row->serialcirctpl_tpl) {
							$this->circ_tpl=stripslashes_array(unserialize($row->serialcirctpl_tpl));
							$this->piedpage=$row->serialcirctpl_piedpage;
						}
					}
				} else {
					$this->circ_tpl=stripslashes_array(unserialize($r->serialcirc_tpl));
					$this->piedpage=$r->serialcirc_piedpage;
				}
			}
			
		}	
	}
	
	static public function get_default_tpl() {
		$tpl = '';
		$requete="select serialcirctpl_tpl FROM serialcirc_tpl WHERE serialcirctpl_id=1";
		$res=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($res)) {
			$row=pmb_mysql_fetch_object($res);
			$tpl=stripslashes_array(unserialize($row->serialcirctpl_tpl));
		}
		return $tpl;
	}
	
	public function get_header_list(){
		$header_list=array();
		foreach($this->circ_tpl as $line){
			if($line['type']=="libre"){
				$header_list[]=$line['label'];
			}else {
				$header_list[]=$this->get_field_label($line['type'],$line['id']);
			}
		}
		return($header_list);
	}
	
	public function get_line($data){
		global $charset;
		
		$elt=array();
		if($data['empr_id']){
			$req="SELECT empr.*, empr_statut.statut_libelle, empr_categ.libelle as libelle_categ, empr_codestat.libelle as libelle_codestat, docs_location.location_libelle, GROUP_CONCAT(distinct libelle_groupe) as group_name 
					FROM empr_statut, empr_categ, empr_codestat, docs_location, empr 
					LEFT JOIN empr_groupe ON (empr.id_empr=empr_groupe.empr_id) 
					LEFT JOIN groupe on (groupe.id_groupe=empr_groupe.groupe_id) 
					WHERE empr.empr_categ=empr_categ.id_categ_empr 
					AND empr.empr_statut=empr_statut.idstatut 
					AND empr.empr_codestat=empr_codestat.idcode 
					AND empr.empr_location=docs_location.idlocation  
					AND empr.id_empr=".$data['empr_id'];
			$res_empr=pmb_mysql_query($req);
			$empr=pmb_mysql_fetch_object($res_empr);
			$pp=$this->p_perso->show_fields($data['empr_id']);
		}
		
		foreach($this->circ_tpl as $line){
			switch($line['type']){
				case 'pp':
					$found=0;
					foreach($pp['FIELDS'] as $pp_data){
						if($pp_data['ID']==$line['id']){
							//on décode les entités html puis toutes les valeur sont réencodées à l'affichage
							$elt[]=html_entity_decode($pp_data['AFF'],ENT_QUOTES,$charset);
							$found=1;
						}
					}
					if(!$found)$elt[]=" ";
				break;	
				case "name":$elt[]= $empr->empr_nom;
				break;
				case "emprlibelle":$elt[]= $empr->empr_nom." ".$empr->empr_prenom;
				break;
				case "cb":$elt[]=$empr->empr_cb;
				break;
				case "mail":$elt[]=$empr->empr_mail;
				break;
				case "adr1":$elt[]=$empr->empr_adr1;
				break;
				case "adr2":$elt[]=$empr->empr_adr2;
				break;
				case "tel1":$elt[]=$empr->empr_tel1;
				break;
				case "tel2":$elt[]=$empr->empr_tel2;
				break;
				case "ville":$elt[]=$empr->empr_ville;
				break;			
				case "libre":$elt[]=" ";
				break;
				case "statut":$elt[]=$empr->statut_libelle;
				break;	
				case "categ":$elt[]=$empr->libelle_categ;
				break;	
				case "codestat":$elt[]=$empr->libelle_codestat;
				break;	
				case "groupe":$elt[]=$empr->group_name;
				break;	
				case "loc":$elt[]=$empr->location_libelle;
				break;	
				default :$elt[]=" ";
				break;					
			}		
		}	
		return $elt;
	}	
	
	public function get_select_form($name="select_field",$selected=0,$onchange="serialcirc_print_add_button();") {
		global $charset,$msg;
		
		$sel=htmlentities($msg["serialcirc_diff_option_form_fiche_format_add_field"], ENT_QUOTES, $charset)."
		<div class='row'>
		 <select name='$name' id='$name' onchange='$onchange'>
		 	<option value='name'>".htmlentities($msg['serialcirc_print_add_fields'],ENT_QUOTES,$charset)."</option>
		 	<optgroup class='erreur' label='".htmlentities($msg["serialcirc_print_group_empr_fields"],ENT_QUOTES,$charset)."' ></optgroup>		
			<option value='emprlibelle'>".htmlentities($this->get_field_label('emprlibelle'),ENT_QUOTES,$charset)."</option>		
			<option value='name'>".htmlentities($this->get_field_label('name'),ENT_QUOTES,$charset)."</option>
			<option value='cb'>".htmlentities($this->get_field_label('cb'),ENT_QUOTES,$charset)."</option>
			<option value='adr1'>".htmlentities($this->get_field_label('adr1'),ENT_QUOTES,$charset)."</option>
			<option value='adr2'>".htmlentities($this->get_field_label('adr2'),ENT_QUOTES,$charset)."</option>
			<option value='tel1'>".htmlentities($this->get_field_label('tel1'),ENT_QUOTES,$charset)."</option>
			<option value='tel2'>".htmlentities($this->get_field_label('tel2'),ENT_QUOTES,$charset)."</option>	
			<option value='ville'>".htmlentities($this->get_field_label('ville'),ENT_QUOTES,$charset)."</option>			
			<option value='categ'>".htmlentities($this->get_field_label('categ'),ENT_QUOTES,$charset)."</option>			
			<option value='statut'>".htmlentities($this->get_field_label('statut'),ENT_QUOTES,$charset)."</option>			
			<option value='codestat'>".htmlentities($this->get_field_label('codestat'),ENT_QUOTES,$charset)."</option>			
			<option value='loc'>".htmlentities($this->get_field_label('loc'),ENT_QUOTES,$charset)."</option>			
			<option value='groupe'>".htmlentities($this->get_field_label('groupe'),ENT_QUOTES,$charset)."</option>			
			!!empr_param_perso!!	
			<optgroup class='erreur' label='".htmlentities($msg["serialcirc_print_group_other"],ENT_QUOTES,$charset)."' ></optgroup>	
			<option value='libre'>".htmlentities($this->get_field_label('libre'),ENT_QUOTES,$charset)."</option>		
		</select>
		</div>	
		<div class='row'>	
			!!fiche_fields!!
		</div>
		<div class='row'>
		</div>
		";
		$perso_fields="";
		if(count($this->p_perso->t_fields ))$perso_fields="<optgroup class='erreur' label='".htmlentities($msg["serialcirc_print_group_empr_p_perso"],ENT_QUOTES,$charset)."' ></optgroup>";
		foreach($this->p_perso->t_fields as $id =>$p){
			$perso_fields.="<option value='pp_$id'>".htmlentities($this->get_field_label('pp',$id),ENT_QUOTES,$charset)."</option>";
		}
		$sel=str_replace('!!empr_param_perso!!', $perso_fields, $sel);	
		
		$line_tpl="		
			<div id='drag_!!index!!'  handler=\"handleprint_!!index!!\" dragtype='circdiffprint' draggable='yes' recepttype='circdiffprint' id_circdiff='!!index!!'		
				recept='yes' dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext='!!titre_drag!!' downlight=\"circdiff_downlight\" highlight=\"circdiff_highlight\"			
				order='!!index!!' style='' id_serialcirc='".$this->id."'>
				<span id=\"handleprint_!!index!!\" style=\"float:left; padding-right : 7px\"><img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' /></span>
				
				<input type='button' class='bouton' name='delete_line'  value='".htmlentities($msg["serialcirc_print_delete_line"],ENT_QUOTES,$charset)."'
				onclick=\"serialcirc_print_del_button('!!index!!'); \" >
				!!titre_field!!								
			</div>		
		";

		$tpl_list="";	
		$index=0;
		foreach($this->circ_tpl as $cpt => $line){
			$tpl=$line_tpl;
			$titre_field=$this->get_field_label($line['type'],$line['id']);
			$tpl=str_replace('!!titre_drag!!',$titre_field , $tpl);
			if(!$line['id'])$line['id']=0;
			$name=$line['type']."_".$cpt."_".$line['id'];
			if($line['type']=="libre") $titre_field.="<input type='text' name='".$name."_label'  value='".htmlentities($line['label'], ENT_QUOTES, $charset)."' >";			
			$titre_field.="<input type='hidden' name='field_list[]'  value='$name' >";
				
			$tpl=str_replace('!!titre_field!!',$titre_field , $tpl);
			$tpl=str_replace('!!index!!',$index , $tpl);
			$tpl_list.=$tpl;
				
			$index++;				
		}		
		$sel=str_replace('!!fiche_fields!!', $tpl_list, $sel);	
		return $sel;
	}
	
	public function get_field_label($field,$id=0){
		global $msg;
		switch($field){
			case "name":
				return $msg["serialcirc_print_empr_name"];
				break;
			case "emprlibelle":
				return $msg["serialcirc_print_empr_libelle"];
				break;
			case "cb":
				return $msg["serialcirc_print_empr_cb"];
				break;
			case "adr1":
				return $msg["serialcirc_print_empr_adr1"];
				break;
			case "adr2":
				return $msg["serialcirc_print_empr_adr2"];
				break;
			case "tel1":
				return $msg["serialcirc_print_empr_tel1"];
				break;
			case "tel2":
				return $msg["serialcirc_print_empr_tel2"];
				break;
			case "ville":
				return $msg["serialcirc_print_empr_ville"];
				break;			
			case "libre":
				return $msg["serialcirc_print_libre_fields"];
				break;							
			case "pp":
				if(count($this->p_perso->t_fields )){
					if($this->p_perso->t_fields[$id]){
						return $this->p_perso->t_fields[$id]["TITRE"];
					}			
				}	
				break;
			case "categ" :
				return $msg["serialcirc_print_empr_categ"];
				break;
			case "statut" :
				return $msg["serialcirc_print_empr_statut"];
				break;
			case "codestat" :
				return $msg["serialcirc_print_empr_codestat"];
				break;
			case "groupe" :
				return $msg["serialcirc_print_empr_groupe"];
				break;
			case "loc" :
				return $msg["serialcirc_print_empr_loc"];
				break;
		}
	}
	
	public function save_form(){
		global $field_list,$piedpage,$fiche_tpl_id_sel;
		
		if ($fiche_tpl_id_sel) {
			$req="update serialcirc set serialcirc_tpl='".$fiche_tpl_id_sel."', serialcirc_piedpage='$piedpage' where id_serialcirc=".$this->id ;
			pmb_mysql_query($req);
		} else {
			$this->circ_tpl=array();
			$cpt=0;
			if(!$field_list)$field_list=array();
			foreach($field_list as $field){
				$data=explode('_',$field);
				$this->circ_tpl[$cpt]['type']=$data[0];
				$this->circ_tpl[$cpt]['id']=$data[2];
				$val_label=$field."_label";
				global ${$val_label};
				$this->circ_tpl[$cpt]['label']=  ${$val_label};
				$cpt++;
			}
			$req="update serialcirc set serialcirc_tpl='".addslashes(serialize($this->circ_tpl))."', serialcirc_piedpage='$piedpage' where id_serialcirc=".$this->id ;
			pmb_mysql_query($req);
		}
		$this->fetch_data();
	}	
	
	public function up_order($tablo){	
		$liste = explode(",",$tablo);
		$new_circ_tpl=array();
		for($i=0;$i<count($liste);$i++){			
			$new_circ_tpl[]=$this->circ_tpl[$liste[$i]];
		}	
		$req="update serialcirc set serialcirc_tpl='".addslashes(serialize($new_circ_tpl))."' where id_serialcirc=".$this->id ;
		pmb_mysql_query($req);
		$this->fetch_data();
	}
	
	public function add_field(){
		global $select_field;
		$cpt=count($this->circ_tpl);
		$data=explode('_',$select_field);
		$this->circ_tpl[$cpt]['type']=$data[0];
		$this->circ_tpl[$cpt]['id']=$data[1];		
		$req="update serialcirc set serialcirc_tpl='".addslashes(serialize($this->circ_tpl))."' where id_serialcirc=".$this->id ;
		pmb_mysql_query($req);
		$this->fetch_data();
	}	
	
	public function del_field(){
		global $index;
		array_splice($this->circ_tpl,$index,1);
		$req="update serialcirc set serialcirc_tpl='".addslashes(serialize($this->circ_tpl))."' where id_serialcirc=".$this->id ;
		pmb_mysql_query($req);
		$this->fetch_data();	
	}
	
	public function change_fields(){
		global $form_serialcirc_tpl;
		if ($form_serialcirc_tpl) {
			$requete = "select serialcirctpl_tpl from serialcirc_tpl where serialcirctpl_id=".$form_serialcirc_tpl;
			$result = pmb_mysql_query($requete);
			if ($result) {
				if (pmb_mysql_num_rows($result) == 1) {
					$req="update serialcirc set serialcirc_tpl='".$form_serialcirc_tpl."' where id_serialcirc=".$this->id;
					pmb_mysql_query($req);
				}
			}
		} else {
			$req="update serialcirc set serialcirc_tpl='' where id_serialcirc=".$this->id;
			pmb_mysql_query($req);
		}
 		$this->fetch_data();
	}
	
	public function get_sort_form($name="sort_field",$selected=0,$onchange="serialcirc_diff_sort_button();") {
		global $charset,$msg;
	
		$sel="
		<div class='row'>
		".$msg["sort_label"]." : <select name='$name' id='$name' onchange='$onchange'>
		<option value=''>".htmlentities($msg['serialcirc_print_add_fields'],ENT_QUOTES,$charset)."</option>
		 	<optgroup class='erreur' label='".htmlentities($msg["serialcirc_print_group_empr_fields"],ENT_QUOTES,$charset)."' ></optgroup>
			<option value='emprlibelle' ".($selected=="emprlibelle" ? "selected='selected'" : "").">".htmlentities($this->get_field_label('emprlibelle'),ENT_QUOTES,$charset)."</option>
			<option value='name' ".($selected=="name" ? "selected='selected'" : "").">".htmlentities($this->get_field_label('name'),ENT_QUOTES,$charset)."</option>
			<option value='cb' ".($selected=="cb" ? "selected='selected'" : "").">".htmlentities($this->get_field_label('cb'),ENT_QUOTES,$charset)."</option>
			<option value='adr1' ".($selected=="adr1" ? "selected='selected'" : "").">".htmlentities($this->get_field_label('adr1'),ENT_QUOTES,$charset)."</option>
			<option value='adr2' ".($selected=="adr2" ? "selected='selected'" : "").">".htmlentities($this->get_field_label('adr2'),ENT_QUOTES,$charset)."</option>
			<option value='tel1' ".($selected=="tel1" ? "selected='selected'" : "").">".htmlentities($this->get_field_label('tel1'),ENT_QUOTES,$charset)."</option>
			<option value='tel2' ".($selected=="tel2" ? "selected='selected'" : "").">".htmlentities($this->get_field_label('tel2'),ENT_QUOTES,$charset)."</option>
			<option value='ville' ".($selected=="ville" ? "selected='selected'" : "").">".htmlentities($this->get_field_label('ville'),ENT_QUOTES,$charset)."</option>
			!!empr_param_perso!!
		</select>
		</div>
		<div class='row'>
		</div>
		";
		$perso_fields="";
		if(count($this->p_perso->t_fields ))$perso_fields="<optgroup class='erreur' label='".htmlentities($msg["serialcirc_print_group_empr_p_perso"],ENT_QUOTES,$charset)."' ></optgroup>";
		foreach($this->p_perso->t_fields as $id =>$p){
			$perso_fields.="<option value='pp_$id' ".($selected=="pp_".$id ? "selected='selected'" : "").">".htmlentities($this->get_field_label('pp',$id),ENT_QUOTES,$charset)."</option>";
		}
		$sel=str_replace('!!empr_param_perso!!', $perso_fields, $sel);
	
		return $sel;
	}
	
} //serialcirc class end