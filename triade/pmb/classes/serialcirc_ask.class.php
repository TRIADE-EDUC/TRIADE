<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_ask.class.php,v 1.11 2017-08-23 07:26:36 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/serialcirc.inc.php"); // constant déclaration 
require_once($include_path."/templates/serialcirc_ask.tpl.php");
require_once($class_path."/serial_display.class.php");
require_once($class_path."/serialcirc_diff.class.php");

class serialcirc_ask {	

	public $ask_info=array();

	public function __construct($id) {
		$this->id=$id+0;		
		$this->fetch_data(); 
	}
	
	public function fetch_data() {
		$this->ask_info=array();
		$req="select * from serialcirc_ask where id_serialcirc_ask=".$this->id;
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {			
			if($r=pmb_mysql_fetch_object($resultat)){					
				$this->ask_info['id']=$r->id_serialcirc_ask;
				$this->ask_info['num_perio']=$r->num_serialcirc_ask_perio;
				$this->ask_info['num_serialcirc']=$r->num_serialcirc_ask_serialcirc;
				$this->ask_info['type']=$r->serialcirc_ask_type;
				$this->ask_info['statut']=$r->serialcirc_ask_statut;
				$this->ask_info['date']=$r->serialcirc_ask_date;
				$this->ask_info['comment']=$r->serialcirc_ask_comment;			
				$this->ask_info['num_empr']=$r->num_serialcirc_ask_empr;
				$this->ask_info['empr']=$this->empr_info($r->num_serialcirc_ask_empr);
				
				
				if(!$this->ask_info['num_perio']){					
					$this->ask_info['serialcirc_diff'] = new serialcirc_diff($r->num_serialcirc_ask_serialcirc);
					$this->ask_info['num_perio']=$this->ask_info['serialcirc_diff']->id_perio;
					
				}	
				if($this->ask_info['num_perio']){						
					$perio=new serial_display($this->ask_info['num_perio'],0,"","","","","",1,1);
					$this->ask_info['perio']['header']=$perio->header_texte;
					$this->ask_info['perio']['view_link']="./catalog.php?categ=serials&sub=view&view=abon&serial_id=".$this->ask_info['num_perio'];	
					$this->ask_info['perio']['id']=$this->ask_info['num_perio'];	
					$this->ask_info['num_abt_diff']=$this->empr_is_in_circ($this->ask_info['num_empr'],$this->ask_info['num_perio']);
					
					$this->ask_info['abts']=array();
					if(!$this->ask_info['num_abt_diff']) {				
						$req_abt="select * from abts_abts where num_notice=".$this->ask_info['num_perio'];
						$resultat_abt=pmb_mysql_query($req_abt);		
						$i=0;						
						if (pmb_mysql_num_rows($resultat_abt)) {
							while($r_abt=pmb_mysql_fetch_object($resultat_abt)){							
								$this->ask_info['abts'][$i]['id']=$r_abt->abt_id;							
								$this->ask_info['abts'][$i]['name']=$r_abt->abt_name;							
								$this->ask_info['abts'][$i]['link_diff']="./catalog.php?categ=serialcirc_diff&sub=view&num_abt=".$r_abt->abt_id.
									"&empr_id=".$this->ask_info['num_empr'];								
								
								$i++;									
							}	
						}	
					}else{
						// déjà abonné
						$req_abt="select * from abts_abts where abt_id=".$this->ask_info['num_abt_diff'];
						$resultat_abt=pmb_mysql_query($req_abt);		
						$i=0;						
						if (pmb_mysql_num_rows($resultat_abt)) {
							$r_abt=pmb_mysql_fetch_object($resultat_abt);						
							$this->ask_info['abts'][$i]['id']=$r_abt->abt_id;							
							$this->ask_info['abts'][$i]['name']=$r_abt->abt_name;							
							$this->ask_info['abts'][$i]['link_diff']="./catalog.php?categ=serialcirc_diff&sub=view&num_abt=".$r_abt->abt_id;				
									
						}
						
					}	
						
				}
				
			}
		}	
				
		// printr($this->ask_info);
	}
	
	public function empr_is_in_circ($id_empr,$id_perio){
		$req="select abt_id,id_serialcirc_diff from serialcirc_diff,serialcirc, abts_abts 
		where num_serialcirc_diff_serialcirc=id_serialcirc and num_serialcirc_abt=abt_id and  num_notice=$id_perio 
		and num_serialcirc_diff_empr=$id_empr";
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			if($r=pmb_mysql_fetch_object($resultat)){			
				return $r->abt_id;	
			}		
		}	
		return 0;	
	}
	public function ask_send_mail($empr_id,$objet,$texte_mail){
		global $biblio_name,$biblio_email,$PMBuseremailbcc;
		
		$empr_info=$this->empr_info($empr_id);
		$texte_mail=str_replace("!!issue!!", $this->ask_info['perio']['header'], $texte_mail);			
		return mailpmb($empr_info["prenom"]." ".$empr_info["nom"], $empr_info["mail"], $objet,	$texte_mail, $biblio_name, $biblio_email,"", "", $PMBuseremailbcc,1);
	}
	
	public function accept(){
		global $serialcirc_inscription_accepted_mail,$serialcirc_inscription_end_mail,$msg,$charset;
		
		if ($charset=="utf-8") {
			$serialcirc_inscription_accepted_mail = utf8_encode($serialcirc_inscription_accepted_mail);
			$serialcirc_inscription_end_mail = utf8_encode($serialcirc_inscription_end_mail);
		}
		
		$req="update serialcirc_ask set serialcirc_ask_statut=1 where id_serialcirc_ask=".$this->id;
		$resultat=pmb_mysql_query($req);	
		// send mail
		if($this->ask_info['type']) $this->ask_send_mail($this->ask_info['num_empr'],$msg["serialcirc_circ_title"],$serialcirc_inscription_end_mail);
		else $this->ask_send_mail($this->ask_info['num_empr'],$msg["serialcirc_circ_title"],$serialcirc_inscription_accepted_mail);
	}
	
	public function refus(){
		global $serialcirc_inscription_no_mail,$msg,$charset;
		
		if ($charset=="utf-8") {
			$serialcirc_inscription_no_mail = utf8_encode($serialcirc_inscription_no_mail);
		}
		
		$req="update serialcirc_ask set serialcirc_ask_statut=2 where id_serialcirc_ask=".$this->id;
		$resultat=pmb_mysql_query($req);	
		// send mail
		$this->ask_send_mail($this->ask_info['num_empr'],$msg["serialcirc_circ_title"],$serialcirc_inscription_no_mail);		
	}
	
	public function set_inscription($id_perio,$id_empr,$id_serialcirc=0){
		if($id_serialcirc)$circ= ", num_serialcirc_ask_serialcirc= $id_serialcirc ";
		$req="update serialcirc_ask set serialcirc_ask_statut=3 $circ where num_serialcirc_ask_perio=$id_perio and num_serialcirc_ask_empr=$id_empr";
		$resultat=pmb_mysql_query($req);	
		// send mail
		
	}
	
	public function delete(){
		if($this->ask_info['statut']==0) return; //pas accepté ou refusée
		$req="delete from serialcirc_ask where id_serialcirc_ask=".$this->id;
		pmb_mysql_query($req);	
			
		// le supprimer de la list de diff si demande de désabonnement
		if($this->ask_info['type']==1){
			$req=" DELETE from serialcirc_diff WHERE num_serialcirc_diff_serialcirc=".$this->ask_info['num_serialcirc']." and num_serialcirc_diff_empr=".$this->ask_info['num_empr'];
			pmb_mysql_query($req);	
			
			// et dans le groupe
			$req=" select id_serialcirc_group from serialcirc_group, serialcirc_diff WHERE 
			serialcirc_diff_empr_type=1
			and num_serialcirc_group_diff= id_serialcirc_diff
			and num_serialcirc_diff_serialcirc=".$this->ask_info['num_serialcirc']." 			
			and num_serialcirc_group_empr=".$this->ask_info['num_empr'];
			
			$resultat=pmb_mysql_query($req);	
			while($r=pmb_mysql_fetch_object($resultat)){					
				$req=" DELETE from serialcirc_group	where id_serialcirc_group=$r->id_serialcirc_group";
				pmb_mysql_query($req);					
			}
		}
		
	}
	
	public function empr_info($id){
		global $dbh;
		$info=array();
		$req="select empr_cb, empr_nom ,  empr_prenom, empr_mail from empr where id_empr=".$id;
		$res_empr=pmb_mysql_query($req);
		if ($empr=pmb_mysql_fetch_object($res_empr)) {			
			$info['cb'] = $empr->empr_cb;
			$info['nom'] = $empr->empr_nom; 
			$info['prenom'] = $empr->empr_prenom;  
			$info['mail'] = $empr->empr_mail;  		
			$info['id_empr']=$id;
			$info['empr_libelle']=$info['nom']." ".$info['prenom']." ( ".$info['cb'] ." ) ";
			$info['view_link']='./circ.php?categ=pret&form_cb='.$empr->empr_cb;
		}
		$this->empr_info[$id]=$info;
		return $info;
	}	
	
} //serialcirc class end

class serialcirc_asklist {
	
	public $type_filter=0;
	public $location_filter=0;
	public $statut_filter=0;	
	public $asklist=array();
	
	public function __construct($location_filter=0,$type_filter=0,$statut_filter=0) {	
		$this->type_filter=$type_filter+0;	
		$this->location_filter=$location_filter+0;	
		$this->statut_filter=$statut_filter+0;	
		$this->fetch_data(); 
	}
	
	public function fetch_data() {
		$this->asklist=array();
		$filter=" where 1 ";
		$filter_table="";
		if($this->type_filter){
			$filter.=" and serialcirc_ask_type=".($this->type_filter-1);
		}
		if($this->location_filter){
			$filter_table.=" ,empr ";
			$filter.=" and empr_location=".$this->location_filter." and num_serialcirc_ask_empr=id_empr";
		}
		if($this->statut_filter){
			$filter.=" and serialcirc_ask_statut = ".($this->statut_filter-1);
		}		
		$req="select * from serialcirc_ask $filter_table $filter ";
		//print $req;
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			$i=0;
			while($r=pmb_mysql_fetch_object($resultat)){	
				$ask =new serialcirc_ask($r->id_serialcirc_ask);				
				$this->asklist[$i]=$ask->ask_info;				
				
				$i++;
			}
		}			
		//print"<pre>";print_r($this->diffusion);print"</pre>";exit;
	}

	public function get_form_list(){
		global $msg,$charset,$serialcirc_asklist_filter_tpl,$serialcirc_asklist_tpl;
		global $serialcirc_asklist_tr;
		$tpl=$serialcirc_asklist_filter_tpl;
		$tpl=str_replace('!!localisation_filter!!',	gen_liste ("select distinct idlocation, location_libelle from docs_location, docsloc_section where num_location=idlocation order by 2 ", "idlocation", "location_libelle", 'location_id', "calcule_section(this);", $this->location_filter, "", "",0,$msg["serialcirc_asklist_location_all"],0),$tpl);
		$tpl=str_replace('!!type_filter!!',	$this->gen_selector('type_filter',
			array( 
				0=>$msg['serialcirc_asklist_type_all'], 
				1=>$msg['serialcirc_asklist_type_0'], 
				2=>$msg['serialcirc_asklist_type_1']
			),	$this->type_filter	), $tpl);
			
		$tpl = str_replace('!!statut_filter!!',	$this->gen_selector('statut_filter',
			array( 
				0=>$msg['serialcirc_asklist_statut_all'], 
				1=>$msg['serialcirc_asklist_statut_0'], 
				2=>$msg['serialcirc_asklist_statut_1'], 
				3=>$msg['serialcirc_asklist_statut_2'],
				4=>$msg['serialcirc_asklist_statut_3']
			),$this->statut_filter ), $tpl);
		
		if(!count($this->asklist))	return $tpl.$msg["serialcirc_asklist_no"];
		
		$tpl.=$serialcirc_asklist_tpl;
		$tpl=str_replace('!!location_filter!!',	 $this->location_filter,$tpl);
		$tpl=str_replace('!!type_filter!!',	 $this->type_filter,$tpl);
		$tpl=str_replace('!!statut_filter!!',	 $this->statut_filter,$tpl);
		$list_tr = '';
		foreach($this->asklist as $ask){
			$tr=$serialcirc_asklist_tr;
			
			
			$tr=str_replace("!!date!!",$ask['date'], $tr);
			$tr=str_replace("!!type!!",$msg['serialcirc_asklist_type_'.$ask['type']], $tr);
			$name= "<a href='".$ask['empr']['view_link']."'>".htmlentities($ask['empr']["empr_libelle"],ENT_QUOTES,$charset)."</a><br />";
			$tr=str_replace("!!destinataire!!",$name, $tr);
			$abt_list="";
			if($ask['type']==0){
				foreach($ask['abts'] as $abt){					
					$abt_list.="<br /><a href='". $abt['link_diff'] ."' >".$abt['name']." </a>";
				}
			}
			$tr=str_replace("!!perio!!","<a href='".$ask['perio']['view_link']."'>".$ask['perio']['header'].$abt_list, $tr);
			$tr=str_replace("!!statut!!",$msg['serialcirc_asklist_statut_'.$ask['statut']], $tr);
			$tr=str_replace("!!comment!!",$ask['comment'], $tr);
			
			$tr=str_replace("!!id_ask!!",$ask['id'], $tr);		
			$tr=str_replace("!!num_empr!!",$ask['num_empr'], $tr);
			
			$list_tr.=$tr;
		}
		
		$tpl=str_replace("!!asklist!!", $list_tr, $tpl);
		return $tpl;
	}
	
	public function gen_selector($name,$field_list,$value=0){
		global $charset;
		$selector="<select name='$name' id='$name'>";		
		foreach($field_list as $val =>$field) {
			$selector.= "<option value='".$val."'";
			$val == $value ? $selector .= ' selected=\'selected\'>' : $selector .= '>';
	 		$selector.= htmlentities($field,ENT_QUOTES, $charset).'</option>';
		}                                         
		return $selector.'</select>'; 
	}
} //serialcirc class end