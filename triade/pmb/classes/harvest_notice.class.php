<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: harvest_notice.class.php,v 1.5 2017-06-30 14:32:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/harvest_notice.tpl.php");
//pour récup les infos de notice
require_once($base_path."/admin/convert/export.class.php");
require_once($class_path."/export_param.class.php");

require_once($class_path."/harvest.class.php");
require_once($class_path."/harvest_profil_import.class.php");
require_once($base_path."/admin/convert/xml_unimarc.class.php");
require_once($class_path."/z3950_notice.class.php");
require_once ("$include_path/isbn.inc.php");

//require_once($base_path."/admin/import/func_customfields.inc.php");

class harvest_notice {
	public $id=0; 
	public $id_profil=0; 
	public $info=array();
	
	public function __construct($notice_id=0, $harvest_id=0, $profil_id=0) {
		$this->id=$notice_id+0;		
		$this->id_harvest=$harvest_id+0;
		$this->id_profil=$profil_id+0;
		$this->fetch_data();
	}
	
	public function fetch_data() {
		$this->info=array();
		$this->info['notice_base']=array();
		$this->info['harvest']=array();
		$this->info['profil']=array();
		
		if($this->id){
			$this->info['notice_base']=$this->get_notice_unimarc($this->id);
		}		
		if($this->id_harvest){
			$h=new  harvest($this->id_harvest);
			$this->info['harvest']=$h;
		}	
		if($this->id_profil){
			$h=new  harvest_profil_import($this->id_profil);
			$this->info['profil']=$h;
		}
	//printr($this->info['notice_base']);		
	
	}    
 	
    public function get_notice_unimarc($notice_id){
    	//récupère les param d'exports
		$export_param = new export_param();
		$param = $export_param->get_parametres($export_param->context);
		//petit nettoyage pour un bon fonctionnement...
		foreach($param as $key => $value){
			$param[str_replace("export_","",$key)] = $param[$key];
		}
		//maintenant que c'est en ordre, on peut y aller!
		$export = new export(array($notice_id),array(),array());
		$export->get_next_notice("",array(),array(),false,$param);
		return $export->xml_array;	
    }

    
    public function get_notice_externe($notice_id) {
    	global $charset,$msg;
    	
		$memo=array();
		$notice_extern_to_memo=array();
		$notice_uni=$this->info['notice_base'];
		
		$req="select * from notices where notice_id=".$notice_id." ";
		$resultat=pmb_mysql_query($req);	
		if ($r=pmb_mysql_fetch_object($resultat)) {
			$code=$r->code;
			$notice_extern= $this->info['harvest']->havest_notice($code,$notice_id);
//			printr($notice_extern);
//			printr($notice_uni);
			$cpt=0;
			foreach($notice_extern as $contens){
				$cpt++;
				$profil=$this->info['profil']->info['fields'][$contens['xml_id']];	
				
				// $pmb_fields=$this->info['harvest']->fields_id[$contens['xml_id']];
				//printr($pmb_fields);
				$harvest=$this->info['harvest']->info['fields'][$contens['xml_id']];			
				if($profil){					
					if($profil['flagtodo']==1){
						// on remplace les champs par les nouvelles valeurs
						foreach($notice_uni['f'] as $index=>$uni_field){		
//							printr($contens);
//							printr($uni_field);
							if($contens['pmb_unimacfield'] && $uni_field['c']==$contens['pmb_unimacfield'] && $contens['pmb_unimacsubfield']){	
								// si champ et sous champ, on delete les anciens champs/sous-champ		
								foreach($uni_field['s'] as $cpt=> $ss_field){
									if($ss_field['c'] == $contens['pmb_unimacsubfield']){
										array_splice($notice_uni['f'][$index]['s'],$cpt,1);
									}	
								}	
								if(!count($uni_field['s']))array_splice($notice_uni['f'],$index,1);
							}elseif($contens['pmb_unimacfield']&& $uni_field['c']==$contens['pmb_unimacfield']) {
								// si pas de sous champ on efface tout 
								array_splice($notice_uni['f'],$index,1);
							}		
							
						}
					}
					if($profil['flagtodo']== 1 || $profil['flagtodo']== 2){					
						$notice_extern_to_memo[]=$contens;
					}						
				}				
			}
			
			//printr($notice_uni);	
			//printr($notice_extern_to_memo);	
			
			// Pour tout les champs nouveau à insérer
			$memo_prev=array();
			foreach($notice_extern_to_memo as $contens){
				$nb=count($notice_uni['f']);
				$flag_create_unimacfield=0;
				if($contens['num_source']!=$memo_prev['num_source']){
					$flag_create_unimacfield=1;
				}								
				if($contens['pmb_unimacfield']!=$memo_prev['pmb_unimacfield']){
					$flag_create_unimacfield=1;
				}				
				if($contens['field_order']!=$memo_prev['field_order']){
					$flag_create_unimacfield=1;
				}		

				if($flag_create_unimacfield){
					$index=$nb;
				} else{
					$index=$memo_prev['i'];
				}
				$notice_uni['f'][$index]['c']=$contens['pmb_unimacfield'];
				$notice_uni['f'][$index]['ind']=$contens['field_ind'];
				if($contens['pmb_unimacsubfield']) $sschamp=$contens['pmb_unimacsubfield'];
				else $sschamp=$contens['usubfield'];
				
				$nb_ss=count($notice_uni['f'][$index]['s']);
				
				$notice_uni['f'][$index]['s'][$nb_ss]['c']=$sschamp;
				$notice_uni['f'][$index]['s'][$nb_ss]['value'] =$contens['value'];
				
				$memo_prev=$contens;
				$memo_prev['i']=$index; // $memo de l'enregistrement en cours
	//			printr($memo_prev);			
			}	
		} else{ //notice inexistante
			return "";
		}
		// printr($notice_uni);	
		// conversion du tableau en xml
		$export= new export($notice_id);
		$export->xml_array=$notice_uni;
		$export->toxml();
		$notice_xml=$export->notice;
		
		// conversion du xml en unimarc
		$xml_unimarc=new xml_unimarc();
		$xml_unimarc->XMLtoiso2709_notice($notice_xml,$charset);
		$notice=$xml_unimarc->notices_[0];
		
		$z=new z3950_notice("unimarc",$notice);
		$z->libelle_form =  $msg["notice_connecteur_remplace_catal"] ;
		if($z->bibliographic_level == "a" && $z->hierarchic_level=="2"){ // article
			//$form=$z->get_form("catalog.php?categ=update&id=".$notice_id,$notice_id,'button',true);
		} else{
			$form=$z->get_form("catalog.php?categ=harvest&action=record&notice_id=".$notice_id,$notice_id,'button');
		}
		
		$form=str_replace("<!--!!form_title!!-->","<h3>".sprintf($msg["harvest_notice_build_title"],$notice_id, $item)."</h3>",$form);
		
		print $form;
	}
	
    public function record_notice($notice_id){
    	$z=new z3950_notice("form");
    	$ret=$z->update_in_database($notice_id);
    	print "
    		<div class='row'><div class='msg-perio'>".$msg["maj_encours"]."</div></div>
			<script type=\"text/javascript\">document.location='./catalog.php?categ=isbd&id=$notice_id'</script>
		";
    	printr($ret);
    }
    
	public function get_form_sel(){
		global $harvest_notice_tpl,$harvest_notice_tpl_error;
		
		//Je regarde si la notice à un isbn
		$req="SELECT code FROM notices WHERE notice_id='".$this->id."'";
		$res=pmb_mysql_query($req);
		if(pmb_mysql_num_rows($res) && (isISBN(pmb_mysql_result($res,0,0)) || isEAN(pmb_mysql_result($res,0,0)))){
			$tpl=$harvest_notice_tpl;
			
			$harvests=new harvests();
			$tpl=str_replace('!!sel_harvest!!',$harvests->get_sel('harvest_id',0),$tpl);
			
			$h=new  harvest_profil_imports();
			$tpl=str_replace('!!sel_profil!!',$h->get_sel('profil_id',0), $tpl);
			
			$tpl=str_replace('!!notice_id!!',$this->id, $tpl);
		}else{
			$tpl=$harvest_notice_tpl_error;
		}
		return $tpl;
	}
} //harvests class end
	
