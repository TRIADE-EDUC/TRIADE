<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: groupexpl.class.php,v 1.17 2024/01/15 15:07:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once("$include_path/templates/groupexpl.tpl.php");
require_once("$include_path/expl_info.inc.php") ;
require_once("$include_path/bull_info.inc.php") ;
require_once($class_path."/session.class.php");

class groupexpl {
	public $id=0;
	public $info=array();
	public $error_message="";
	public $error_html_message="";
	public $info_message="";
	
	public function __construct($id=0) {
		$this->id=intval($id);
		$this->error_message="";
		$this->info_message="";
		$this->fetch_data();
	}
	
	public function fetch_data() {
		$this->info=array();
		$this->info['id']= 0;
		$this->info['name']= '';
		$this->info['resp_expl_num']= 0;
		$this->info['location']= 0;
		$this->info['statut_principal']	= 0;
		$this->info['statut_others']= '';
		$this->info['comment']= '';
		$this->info['expl']=array();
		if(!$this->id) return;
		$req="select * from groupexpl where id_groupexpl=". $this->id;		
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);		
			$this->info['id']= $r->id_groupexpl;	
			$this->info['name']= $r->groupexpl_name;
			$this->info['resp_expl_num']= $r->groupexpl_resp_expl_num;
			$this->info['location']= $r->groupexpl_location;
			$this->info['statut_principal']	= $r->groupexpl_statut_resp;
			$this->info['statut_others']= $r->groupexpl_statut_others;
			$this->info['comment']= $r->groupexpl_comment;	
		}
		
		$req="select * from groupexpl_expl, exemplaires where expl_id=groupexpl_expl_num and groupexpl_num=". $this->id;		
		$resultat=pmb_mysql_query($req);	
		$i=0;
		$this->info['checked']=0;
		if(!isset($this->info['not_checked'])) $this->info['not_checked']=0;
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){	
				$this->info['expl'][$i]['id']= $r->groupexpl_expl_num;	
				$this->info['expl'][$i]['checked']= $r->groupexpl_checked;
				if(!$r->groupexpl_checked){
					$this->info['not_checked']++;
				}
				// récup des infos de l'expl					
				$this->info['expl'][$i]['cb']= $r->expl_cb;
				$this->info['expl_list'][$r->expl_cb]=1;
				// est-il en prêt
				$this->info['expl'][$i]['pret']=array();
				$req_pret="select * from pret, empr where id_empr=pret_idempr and pret_idexpl=". $r->groupexpl_expl_num;
				$res_pret=pmb_mysql_query($req_pret);
				if (pmb_mysql_num_rows($res_pret)) {
					$r_pret=pmb_mysql_fetch_object($res_pret);	
					
					$this->info['expl'][$i]['pret']['date']=$r_pret->pret_date;
					$this->info['expl'][$i]['pret']['retour']=$r_pret->pret_retour;					
					$this->info['expl'][$i]['pret']['id_empr']=$r_pret->pret_idempr;
					$this->info['expl'][$i]['pret']['empr_cb']=$r_pret->empr_cb;
					$this->info['expl'][$i]['pret']['empr_nom']=$r_pret->empr_nom;
					$this->info['expl'][$i]['pret']['empr_prenom']=$r_pret->empr_prenom;
					$this->info['expl'][$i]['pret']['emprunteur']="<a href='./circ.php?categ=pret&form_cb=".$r_pret->empr_cb."'>".$r_pret->empr_nom." ".$r_pret->empr_prenom."</a>";				
				
					$this->info['pret']['i']=$i;
					$this->info['pret']['date']=$r_pret->pret_date;
					$this->info['pret']['retour']=$r_pret->pret_retour;					
					$this->info['pret']['id_empr']=$r_pret->pret_idempr;
					$this->info['pret']['empr_cb']=$r_pret->empr_cb;
					$this->info['pret']['empr_nom']=$r_pret->empr_nom;
					$this->info['pret']['empr_prenom']=$r_pret->empr_prenom;
					$this->info['pret']['emprunteur']="<a href='./circ.php?categ=pret&form_cb=".$this->info['pret']['empr_cb']."'>".$r_pret->empr_nom." ".$r_pret->empr_prenom."</a>";				
				}
				$i++;
			}
		}					 				
	// printr($this->info);
	}

	public function is_doc_header($cb) {
		if(!($expl_id=$this->get_expl_id($cb))) return 0;
		if($this->info['resp_expl_num']==$expl_id) return 1; 
		return 0;
	}
	
	public function group_is_check_out() {
		if(count($this->info['pret']) )return 1;
		return 0;
	}	
	   
	public function group_have_error() {		
		return $this->info['checked'];		
	}
	
	public function get_expl_id($cb) {
		$req="select expl_id from exemplaires where expl_cb='$cb' ";		
		$resultat=pmb_mysql_query($req);	
		if (!pmb_mysql_num_rows($resultat)) {			
			return 0;
		}	
		$r=pmb_mysql_fetch_object($resultat);
		return $r->expl_id;
	}
	
	public function raz_check($cb='') {
		if($cb)	{
			if(!($expl_id=$this->get_expl_id($cb))) return 0;
			$req="update groupexpl_expl SET groupexpl_checked=0 where groupexpl_expl_num=".$expl_id;			
			pmb_mysql_query($req);		
		}else{
			$req="update groupexpl_expl SET groupexpl_checked=0 where groupexpl_num=".$this->id;			
			pmb_mysql_query($req);					
		}	
				
		$this->fetch_data();	
		
	}
	
	public function do_check($cb='') {
		if($cb)	{
			if(!($expl_id=$this->get_expl_id($cb))) return 0;
			$req="update groupexpl_expl SET groupexpl_checked=1 where groupexpl_expl_num=".$expl_id;			
			pmb_mysql_query($req);		
		}else{
			$req="update groupexpl_expl SET groupexpl_checked=1 where groupexpl_num=".$this->id;			
			pmb_mysql_query($req);					
		}					
		$this->fetch_data();			
	}	
	
	public function get_name_group_from_id($id) {
		$req="select groupexpl_name from groupexpl where id_groupexpl='$id' ";
		$resultat=pmb_mysql_query($req);
		if (!pmb_mysql_num_rows($resultat)) return 0;
		$r=pmb_mysql_fetch_object($resultat);
		return $r->groupexpl_name;
	}
	
	public function get_id_group_from_cb($cb) {
		$req="select id_groupexpl from groupexpl, groupexpl_expl, exemplaires where expl_id=groupexpl_expl_num and groupexpl_num=id_groupexpl and expl_cb='$cb' ";		
		$resultat=pmb_mysql_query($req);	
		if (!pmb_mysql_num_rows($resultat)) return 0;
		$r=pmb_mysql_fetch_object($resultat);
		return $r->id_groupexpl;
	}	
	
	public function add_expl($cb) {
		global $msg;
		
		$this->error_message="";
		$this->info_message="";
		if(!($expl_id=$this->get_expl_id($cb))){
				$this->error_message=$msg['groupexpl_form_error_not_exist']." $cb";
			return 0;
		} 
		if($id_group=$this->get_id_group_from_cb($cb)){
			// l'exemplaire appartient déja à un group
			if($id_group!=$this->id){
				// group autre
				$this->error_message=$msg['groupexpl_form_error_already_in_group'];
				$this->error_html_message="<a href='./circ.php?categ=groupexpl&action=form&id=$id_group'>".$this->get_name_group_from_id($id_group)."</a>";
				return 0;
			}else{
				// celui-ci
				$this->error_message=$msg['groupexpl_form_error_already_in_this_group'];
				return 1;
			}
		}		
		$req="INSERT INTO groupexpl_expl SET groupexpl_num=".$this->id.", groupexpl_expl_num=".$expl_id;		
		pmb_mysql_query($req);

		$req="update exemplaires set expl_statut=".$this->info['statut_others']." where expl_id=".$expl_id;				
		pmb_mysql_query($req);		
		
		$this->fetch_data();
		$this->info_message=$msg['groupexpl_form_info_insert'];
		return 1;
	}
	
    public function del_expl($cb) {
		$this->error_message="";		
		if(!($expl_id=$this->get_expl_id($cb))) return 0;
		$req="DELETE from groupexpl_expl WHERE groupexpl_expl_num=".$expl_id;			
		pmb_mysql_query($req);		
		$this->fetch_data();
		return 1;    	
    } 
    
    public function get_expl_display($tpl,$id){    
		$expl = get_expl_info($id,1);
		$tpl=str_replace('!!cb!!',$expl->expl_cb ,$tpl);
		$tpl=str_replace('!!notice!!',$expl->aff_reduit ,$tpl);
		$tpl=str_replace('!!sur_loc_libelle!!',$expl->sur_loc_libelle,$tpl);
		$tpl=str_replace('!!location_libelle!!',$expl->location_libelle,$tpl);
		$tpl=str_replace('!!section_libelle!!',$expl->section_libelle,$tpl);
		$tpl=str_replace('!!expl_cote!!',$expl->expl_cote,$tpl);
		$tpl=str_replace('!!statut_libelle!!',$expl->statut_libelle,$tpl);
		return $tpl;
    }
 
    public function get_content_form() {
        global $msg;
        global $pmb_lecteurs_localises,$deflt_docs_location;
        
        $interface_content_form = new interface_content_form(static::class);
        $interface_content_form->add_element('name', 'groupexpl_form_name')
        ->add_input_node('text', $this->info['name']);
        if($pmb_lecteurs_localises){
            if(!$this->info['location']) {
                $f_loc=$deflt_docs_location;
            } else {
                $f_loc=$this->info['location'];
            }
            $interface_content_form->add_element('f_loc', 'groupexpl_form_location')
            ->add_query_node('select', 'SELECT idlocation, location_libelle FROM docs_location order by location_libelle', $f_loc)
            ->set_first_option(0, $msg['all_location']);
        }
        $interface_content_form->add_element('statut_principal', 'groupexpl_form_statut_principal')
        ->add_html_node(do_selector('docs_statut', 'statut_principal', $this->info['statut_principal']));
        $interface_content_form->add_element('statut_others', 'groupexpl_form_statut_others')
        ->add_html_node(do_selector('docs_statut', 'statut_others', $this->info['statut_others']));
        $interface_content_form->add_element('comment', 'groupexpl_form_comment')
        ->add_textarea_node($this->info['comment'], 50, 2);
        if ($this->id) {
            $interface_content_form->add_element('expl_list')
            ->add_html_node(list_items_group_edit_ui::get_instance(array('expl_group' => $this->id))->get_display_list());
        }
        return $interface_content_form->get_display();
    }
    
	public function get_form() {
		global $groupexpl_form_add_expl_tpl,$msg,$charset;		
		
		$interface_form = new interface_form('groupexpl_form');
		if(!$this->id){
			$interface_form->set_label($msg['groupexpl_form_add']);
		}else{
			$interface_form->set_label($msg['groupexpl_form_edit']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->info['name']." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('groupexpl')
		->set_field_focus('name');
		if($this->id) {
			$interface_form->add_action_extension('see_button', $msg['groupexpl_list_see'], './circ.php?categ=groupexpl&action=see_form&id='.$this->id);
		}
		$display = $interface_form->get_display();
		if ($this->id) {
			$content_form_add_expl = $groupexpl_form_add_expl_tpl;
			$content_form_add_expl=str_replace('!!error_message!!',htmlentities($this->error_message,ENT_QUOTES,$charset), $content_form_add_expl);
			$content_form_add_expl=str_replace('!!error_html_message!!',$this->error_html_message, $content_form_add_expl);
			$content_form_add_expl=str_replace('!!info_message!!',htmlentities($this->info_message,ENT_QUOTES,$charset), $content_form_add_expl);
			$content_form_add_expl = str_replace('!!id!!', $this->id, $content_form_add_expl);
			$display .= $content_form_add_expl;
		}
		return $display;
	}

	public function set_properties_from_form() {
		global $name, $comment, $f_loc, $statut_principal, $statut_others, $resp_expl_num;
		
		$this->info['name'] = stripslashes($name);
		$this->info['resp_expl_num'] = intval($resp_expl_num);
		$this->info['location'] = intval($f_loc);
		$this->info['statut_principal']	= intval($statut_principal);
		$this->info['statut_others'] = intval($statut_others);
		$this->info['comment'] = stripslashes($comment);
	}
	
	public function save() {
		$fields="
			groupexpl_name='".addslashes($this->info['name'])."',
			groupexpl_resp_expl_num='".$this->info['resp_expl_num']."',
			groupexpl_comment='".addslashes($this->info['comment'])."',
			groupexpl_location='".$this->info['location']."',
			groupexpl_statut_resp='".$this->info['statut_principal']."',
			groupexpl_statut_others='".$this->info['statut_others']."'			
		";		
		if(!$this->id){ // Ajout
			$req="INSERT INTO groupexpl SET $fields ";	
			pmb_mysql_query($req);
			$this->id = pmb_mysql_insert_id();
		} else {
			$req="UPDATE groupexpl SET $fields where id_groupexpl=".$this->id;	
			pmb_mysql_query($req);				
		}
		session::set_last_used("groupexpl", $this->id);
		$req="update exemplaires set expl_statut=".$this->info['statut_principal']." where expl_id=".$this->info['resp_expl_num'];				
		pmb_mysql_query($req);	
		$this->fetch_data();
	}	
	
	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$req="DELETE from groupexpl WHERE id_groupexpl=".$id;
			pmb_mysql_query($req);
			$req_pret="delete from groupexpl_expl where groupexpl_num=".$id;
			pmb_mysql_query($req_pret);
		}
		return true;
	}	
   
	public function get_see_form() {
		global $groupexpl_see_form_tpl,$msg,$charset;		
		global $pmb_lecteurs_localises;
		global $groupexpl_see_form_principale_tpl;
		
		$tpl=$groupexpl_see_form_tpl;				
		$location_libelle = '';
		if($pmb_lecteurs_localises){			
			$res = pmb_mysql_query("SELECT location_libelle FROM docs_location where idlocation=".$this->info['location']);			
			if ($r = pmb_mysql_fetch_object($res)) {
				$location_libelle="<label class='etiquette'>".$msg["groupexpl_see_form_location"]."</label>".$r->location_libelle;
			}
		}		
		foreach($this->info['expl'] as $expl){
			if($expl['checked']){	
				$tpl=str_replace('!!responsable!!',$this->get_expl_display($groupexpl_see_form_principale_tpl,$expl['id']),$tpl);
			}
		}
		$tpl=str_replace('!!responsable!!',"",$tpl);
		$tpl=str_replace('!!error_message!!',htmlentities($this->error_message,ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace('!!info_message!!',htmlentities($this->info_message,ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace('!!location!!',$location_libelle, $tpl);
		if($this->id) {
		    $tpl=str_replace('!!expl_list!!',list_items_group_see_ui::get_instance(array('expl_group' => $this->id))->get_display_list(),$tpl);
		}
		$tpl=str_replace('!!name!!',htmlentities($this->info['name'],ENT_QUOTES,$charset),$tpl);
		$tpl=str_replace('!!comment!!',htmlentities($this->info['comment'],ENT_QUOTES,$charset),$tpl);
		$tpl=str_replace('!!id!!',$this->id,$tpl);
		 
		return $tpl;
	}	
	
	public function get_confirm_form($cb) {
		global $groupexpl_confirm_form_tpl,$msg,$charset;		
		global $pmb_lecteurs_localises;
		
		$is_doc_header=$this->is_doc_header($cb);	
		$tpl=$groupexpl_confirm_form_tpl;
		if(!$is_doc_header)	$message="<span class='erreur'>".$msg["groupexpl_see_form_warrning"]."</span><br >";
		if($pmb_lecteurs_localises){			
			$res = pmb_mysql_query("SELECT location_libelle FROM docs_location where idlocation=".$this->info['location']);			
			if ($r = pmb_mysql_fetch_object($res)) {
				$location_libelle="<label class='etiquette'>".$msg["groupexpl_see_form_location"]."</label>".$r->location_libelle;
			}
		}		
		$tpl=str_replace('!!message!!',$message, $tpl);
		$tpl=str_replace('!!location!!',$location_libelle, $tpl);
		$tpl=str_replace('!!expl_list!!',list_items_group_confirm_ui::get_instance(array('expl_group' => $this->id))->get_display_list(),$tpl);
		$tpl=str_replace('!!name!!',htmlentities($this->info['name'],ENT_QUOTES,$charset),$tpl);
		$tpl=str_replace('!!comment!!',htmlentities($this->info['comment'],ENT_QUOTES,$charset),$tpl);
		$tpl=str_replace('!!id!!',$this->id,$tpl);
		 
		return $tpl;		
	}
} //groupexpl class end





class groupexpls {	
	public $info=array();
	public $error_message="";
	public $info_message="";
	
	public function __construct() {
		$this->error_message="";
		$this->info_message="";
		$this->fetch_data();
	}
	
	public function fetch_data() {
		global $f_loc,$montrerquoi;
		$f_loc = intval($f_loc);
		$this->info=array();
		$this->error_message="";	
		$this->info_message="";	

		$req="select * from groupexpl   ";		
		if($f_loc){
			$req.=" where groupexpl_location =$f_loc ";			
		}		
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){	
				$groupexpl= new groupexpl($r->id_groupexpl);
				if($montrerquoi=="pret" && !$groupexpl->group_is_check_out()) continue;
				if($montrerquoi=="error" && !$groupexpl->group_have_error()) continue;
				$this->info[]=$groupexpl->info;
			}
		}
	}
	
	public function set_error_message($error_message) {
		$this->error_message=$error_message;	
	}	
	
	public function get_list() {
		global $charset;
		global $pmb_lecteurs_localises,$groupexpl_list_tpl,$groupexpl_list_line_tpl,$msg;
		global $f_loc,$montrerquoi;
		
		//Sélection de la localisation		
		$loc_select="";		
		if($pmb_lecteurs_localises){
			$loc_select .= "<br />".$msg["groupexpl_location"];
			$loc_select .= "<select name='f_loc' onchange='document.check_resa.submit();'>";
			$res = pmb_mysql_query("SELECT idlocation, location_libelle FROM docs_location order by location_libelle");
			$loc_select .= "<option value='0'>".$msg["all_location"]."</option>";
			while ($value = pmb_mysql_fetch_array($res)) {
				$loc_select .= "<option value='".$value[0]."'";
				if ($value[0]==$f_loc)	$loc_select .= " selected ";		
				$loc_select .= ">".htmlentities($value[1],ENT_QUOTES,$charset)."</option>";
			}
			$loc_select .= "</select>";
		}
		
		$tpl=$groupexpl_list_tpl;	
		$pret_checked=$error_checked=$all_checked="";
		switch($montrerquoi){
			case "pret" :
				$pret_checked="checked='checked'";				
			break;
			case 'checked':
				$error_checked="checked='checked'";	
			break;					
			case "list":
			default:						
				$all_checked="checked='checked'";
			break;
		}		
		$tpl=str_replace('!!pret_checked!!',$pret_checked, $tpl);
		$tpl=str_replace('!!error_checked!!',$error_checked, $tpl);	
		$tpl=str_replace('!!all_checked!!',$all_checked, $tpl);	
		
		$tpl_list="";
		$odd_even="odd";
		foreach($this->info as $elt){
			//printr($elt);
			$tpl_elt=$groupexpl_list_line_tpl;
			if($odd_even=='odd')$odd_even="even";
			else $odd_even="odd";
			$tpl_elt=str_replace('!!odd_even!!',$odd_even, $tpl_elt);	
			$tpl_elt=str_replace('!!name!!',htmlentities($elt['name'],ENT_QUOTES,$charset), $tpl_elt);	
			
			if(isset($elt['pret'])) {
				$tpl_elt=str_replace('!!emprunteur!!',$elt['pret']['emprunteur'], $tpl_elt);
			} else {
				$tpl_elt=str_replace('!!emprunteur!!','', $tpl_elt);
			}
			$error="";
			if($elt['not_checked'])	$error="X"; 
			$tpl_elt=str_replace('!!error!!',$error, $tpl_elt);	
			$tpl_elt=str_replace('!!id!!',$elt['id'], $tpl_elt);	
			$tpl_list.=$tpl_elt;	
		}
		$tpl=str_replace('!!location_filter!!',$loc_select, $tpl);
		$tpl=str_replace('!!error_message!!',htmlentities($this->error_message,ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace('!!info_message!!',htmlentities($this->info_message,ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace('!!list!!',$tpl_list, $tpl);
		return $tpl;
	}	
	
	public static function get_group_expl($cb){
		$req="select id_groupexpl from groupexpl, groupexpl_expl, exemplaires where expl_id=groupexpl_expl_num and groupexpl_num=id_groupexpl and expl_cb='$cb' ";		
		$resultat=pmb_mysql_query($req);	
		if (!pmb_mysql_num_rows($resultat)){
			return 0;
		} 
		$r=pmb_mysql_fetch_object($resultat);
		return $r->id_groupexpl;	
	}
	
	public static function get_group_name_expl($cb){
		$req="select groupexpl_name from groupexpl, groupexpl_expl, exemplaires where expl_id=groupexpl_expl_num and groupexpl_num=id_groupexpl and expl_cb='$cb' ";
		$resultat=pmb_mysql_query($req);
		if (!pmb_mysql_num_rows($resultat)){
			return "";
		}
		$r=pmb_mysql_fetch_object($resultat);
		return $r->groupexpl_name;
	}
		
    	
} // groupexpls class end
	
