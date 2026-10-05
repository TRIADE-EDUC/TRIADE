<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc.class.php,v 1.60 2023/12/27 15:42:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Spipu\Html2Pdf\Html2Pdf;

global $class_path, $include_path;
require_once($include_path."/serialcirc.inc.php");
require_once($include_path."/templates/serialcirc.tpl.php");
require_once($class_path."/serial_display.class.php");
require_once($class_path."/serialcirc_diff.class.php");
require_once($class_path."/resa.class.php");
require_once($class_path."/serialcirc_print_fields.class.php");
require_once($include_path."/h2o/h2o.php");
require_once("$class_path/aut_link.class.php");

class serialcirc {
	public $info_expl=array();
	public $info_circ=array();
	public $id_location=0;
	
	protected static $emprs_infos = array();
	
	public function __construct($id_location) {
		global $pmb_lecteurs_localises,$deflt_docs_location;
		$this->id_location=0;
		if($pmb_lecteurs_localises){
			$id_location = intval($id_location);
			if(!$id_location)$id_location=$deflt_docs_location;
			$this->id_location=$id_location;
		}
		$this->fetch_data();
	}
	
	public function fetch_data() {
		$this->info_expl=array();
		$this->info_circ=array();
		$restrict = '';
		if($this->id_location) $restrict=" and expl_location=".$this->id_location;
		$req="select * from serialcirc_expl
				join exemplaires on serialcirc_expl.num_serialcirc_expl_id = exemplaires.expl_id
				join bulletins on exemplaires.expl_bulletin = bulletins.bulletin_id
				join serialcirc on serialcirc_expl.num_serialcirc_expl_serialcirc = serialcirc.id_serialcirc
				left join abts_abts on exemplaires.expl_abt_num = abts_abts.abt_id
				where 1 ".$restrict." order by date_date DESC";
		$resultat=pmb_mysql_query($req);
		if ($resultat && pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){
				$this->info_expl[$r->expl_id]['expl_cb']= $r->expl_cb;
				$this->info_expl[$r->expl_id]['expl_id']= intval($r->expl_id);
				$this->info_expl[$r->expl_id]['expl_statut']= $r->expl_statut;
				$this->info_expl[$r->expl_id]['expl_location']= $r->expl_location;
				
				$rqtSite = "SELECT location_libelle FROM docs_location WHERE idlocation=" . intval($r->expl_location);
				$resSite = pmb_mysql_result(pmb_mysql_query($rqtSite),0);
				$this->info_expl[$r->expl_id]['expl_location_name']= $resSite;
				
				$rqtSite = "SELECT lender_libelle FROM lenders WHERE idlender=".$r->expl_owner;
				$resSite = pmb_mysql_result(pmb_mysql_query($rqtSite),0);
				$this->info_expl[$r->expl_id]['expl_owner']= $resSite;
				
				$this->info_expl[$r->expl_id]['expl_cote']= $r->expl_cote;
				
				$this->info_expl[$r->expl_id]['bulletine_date']= $r->serialcirc_expl_bulletine_date;
				$this->info_expl[$r->expl_id]['num_diff']= $r->num_serialcirc_expl_serialcirc_diff;
				$this->info_expl[$r->expl_id]['expl_abt_num']= $r->expl_abt_num;
				$this->info_expl[$r->expl_id]['expl_abt_name']= $r->abt_name;
				
				$this->info_expl[$r->expl_id]['numero']= $r->bulletin_numero;
				$this->info_expl[$r->expl_id]['mention_date']= $r->mention_date;
				$this->info_expl[$r->expl_id]['bulletin_notice']= $r->bulletin_notice;
				$this->info_expl[$r->expl_id]['bulletin_id']= $r->bulletin_id;
				$this->info_expl[$r->expl_id]['num_notice']= $r->num_notice;
				$this->info_expl[$r->expl_id]['expl_link']="./catalog.php?categ=serials&sub=bulletinage&action=expl_form&bul_id=" . intval($r->bulletin_id) . "&expl_id=" . intval($r->expl_id);
				$this->info_expl[$r->expl_id]['bull_link']="./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=" . intval($r->bulletin_id);
				$this->info_expl[$r->expl_id]['serial_link']="./catalog.php?categ=serials&sub=view&serial_id=" . intval($r->bulletin_notice);
				$this->info_expl[$r->expl_id]['abt_link']="./catalog.php?categ=serialcirc_diff&sub=view&num_abt=".$r->expl_abt_num;
				$this->info_expl[$r->expl_id]['cirdiff_link']="./catalog.php?categ=serialcirc_diff&sub=view&num_abt=".$r->expl_abt_num;
				$this->info_expl[$r->expl_id]['view_link']='./circ.php?categ=visu_ex&form_cb_expl='.$r->expl_cb;
				
				$req_serial="select * from notices  where notice_id=" . intval($r->bulletin_notice);
				$res_serial=pmb_mysql_query($req_serial);
				if ($r_serial=pmb_mysql_fetch_object($res_serial)){
					$this->info_expl[$r->expl_id]['serial_title']=$r_serial->tit1;
				}
				
				$this->info_expl[$r->expl_id]['num_serialcirc']= $r->id_serialcirc;
				$this->info_expl[$r->expl_id]['serialcirc_type']= $r->serialcirc_type;
				$this->info_expl[$r->expl_id]['serialcirc_checked']= $r->serialcirc_checked;
				$this->info_expl[$r->expl_id]['serialcirc_expl_statut_circ_after']= $r->serialcirc_expl_statut_circ_after;
				$this->info_expl[$r->expl_id]['serialcirc_diff'] = new serialcirc_diff($r->num_serialcirc_expl_serialcirc);
				$this->info_expl[$r->expl_id]['state_circ']= $r->serialcirc_expl_state_circ;
				//	$this->info_expl[$r->expl_id]['diff']= $r->num_serialcirc_expl_serialcirc_diff;
				$this->info_expl[$r->expl_id]['current_empr']= $r->num_serialcirc_expl_current_empr;
				$this->info_expl[$r->expl_id]['start_date']= $r->serialcirc_expl_start_date;
				
				$this->fetch_info_circ($r->expl_id);
				
				$this->info_expl[$r->expl_id]['circ']=array();
				$req_circ="select * from serialcirc_circ where num_serialcirc_circ_expl =".$r->expl_id;
				$resultat_circ=pmb_mysql_query($req_circ);
				if (pmb_mysql_num_rows($resultat_circ)) {
					while($r_circ=pmb_mysql_fetch_object($resultat_circ)){
						$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['subscription']=$r_circ->serialcirc_circ_subscription;
						$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['ret_asked ']=$r_circ->serialcirc_circ_ret_asked ;
						$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['trans_asked ']=$r_circ->serialcirc_circ_trans_asked ;
						$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['trans_doc_asked']=$r_circ->serialcirc_circ_trans_doc_asked;
						$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['expected_date']=$r_circ->serialcirc_circ_expected_date;
						$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['pointed_date']=$r_circ->serialcirc_circ_pointed_date;
						if($this->info_expl[$r->expl_id]['serialcirc_diff']->virtual_circ && !$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['subscription']==0){
							$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['no_subscription']=1;
						}
						else {
							$this->info_expl[$r->expl_id]['circ'][$r_circ->num_serialcirc_circ_empr ]['no_subscription']=0;
						}
					}
				}
			}
		}
	}
	
	public function fetch_info_circ($id_expl){
		$this->info_circ[$id_expl]=array();
		$req="select *,DATEDIFF(serialcirc_circ_expected_date,CURDATE())as late_diff from serialcirc_circ where num_serialcirc_circ_expl=$id_expl order by serialcirc_circ_order ";
		$resultat=pmb_mysql_query($req);
		$last_owner = 0;
		if ($resultat && pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['id']=$r->id_serialcirc_circ;
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['num_diff']=$r->num_serialcirc_circ_diff;
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['num_expl']=$r->num_serialcirc_circ_expl;
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['num_empr']=$r->num_serialcirc_circ_empr;
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['num_serialcirc']=$r->num_serialcirc_circ_serialcirc;
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['order']=$r->serialcirc_circ_order;
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['subscription']=$r->serialcirc_circ_subscription;
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['hold_asked']=$r->serialcirc_circ_hold_asked;
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['ret_asked']=$r->serialcirc_circ_ret_asked;
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['trans_asked']=$r->serialcirc_circ_trans_asked;
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['doc_asked']=$r->serialcirc_circ_trans_doc_asked;
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['expected_date']=$r->serialcirc_circ_expected_date;
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['pointed_date']=$r->serialcirc_circ_pointed_date;
				if ($r->serialcirc_circ_pointed_date) {
					$last_owner = $r->num_serialcirc_circ_empr;
				}
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['group_name']=$r->serialcirc_circ_group_name;
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['current_owner']=0;
				$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['late_diff']=$r->late_diff;
				if($this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['late_diff'] <0 && !$this->info_circ[$id_expl][$r->num_serialcirc_circ_empr]['pointed_date']){
					$this->info_expl[$id_expl]['is_late']=1;
				}else{
					$this->info_expl[$id_expl]['is_late']=0;
				}
			}
			if ($last_owner) {
				$this->info_circ[$id_expl][$last_owner]['current_owner']=1;
			}
		}
		return $this->info_circ[$id_expl];
	}
	
	public static function empr_info($id){
		if (isset(self::$emprs_infos[$id])) {
			return self::$emprs_infos[$id];
		}
		$info = array();
		$req="select empr_cb, empr_nom ,  empr_prenom, empr_mail, empr_login from empr where id_empr=".$id;
		$res_empr=pmb_mysql_query($req);
		if ($empr=pmb_mysql_fetch_object($res_empr)) {
			$info['cb'] = $empr->empr_cb;
			$info['nom'] = $empr->empr_nom;
			$info['prenom'] = $empr->empr_prenom;
			$info['mail'] = $empr->empr_mail;
			$info['empr_login'] = $empr->empr_login;
			$info['id_empr']=$id;
			$info['view_link']='./circ.php?categ=pret&form_cb='.$empr->empr_cb;
			$info['empr_libelle']=$info['nom']." ".$info['prenom']." ( ".$info['cb'] ." ) ";
		}
		self::$emprs_infos[$id] = $info;
		return self::$emprs_infos[$id];
	}
	
	public function expl_info($id){
		$info=array();
		$req="select * from exemplaires, bulletins, notices where expl_id=$id and expl_bulletin=bulletin_id and bulletin_notice=notice_id ";
		$resultat=pmb_mysql_query($req);
		
		if($r=pmb_mysql_fetch_object($resultat)){
			$info['id']=$id;
			$info['cb']=$r->expl_cb;
			$info['id_bulletin']=$r->expl_bulletin;
			$info['perio']=$r->tit1;
			$info['numero']= $r->bulletin_numero;
			$info['mention_date']= $r->mention_date;
			$info['view_link']='./circ.php?categ=visu_ex&form_cb_expl='.$r->expl_cb;
		}
		return $info;
	}
	
	public function empr_is_subscribe($empr_id, $expl_id){
		if( !$this->info_expl[$expl_id]['circ'][$empr_group['num_empr'] ]){
			return true;
		} elseif( !$this->info_expl[$expl_id]['circ'][$empr_group['num_empr'] ]['no_subscription']){
			return true;
		}
		return false;
	}
	
	public function delete_diffusion($expl_id){
		$status=1;
		if (!isset($this->info_expl[$expl_id]) || !$this->info_expl[$expl_id]) return 0;
		// Traitement des résa
		$req="select num_serialcirc_circ_empr from serialcirc_circ where serialcirc_circ_hold_asked=2 and num_serialcirc_circ_expl=$expl_id
		order by serialcirc_circ_order";
		$res=pmb_mysql_query($req);
		if(pmb_mysql_num_rows($res)){
			while ($r=pmb_mysql_fetch_object($res)) {
				$resa=new reservation($r->num_serialcirc_circ_empr,0,$this->info_expl[$expl_id]['bulletin_id']);
				$resa->add();
			}
		}
		
		$req="delete from serialcirc_expl where num_serialcirc_expl_id =$expl_id";
		pmb_mysql_query($req);
		$req="delete from serialcirc_circ where num_serialcirc_circ_expl =$expl_id";
		pmb_mysql_query($req);
		
		// On nettoie la table serialcirc, on regarde les listes qui ne sont plus attachées à des abonnements et plus utilisées dans des circultation en cours
		$req = 'delete from serialcirc where num_serialcirc_abt = 0 and id_serialcirc not in (select distinct num_serialcirc_expl_serialcirc from serialcirc_expl)';
		pmb_mysql_query($req);
		
		// on change le statut si demandé
		if($this->info_expl[$expl_id]['serialcirc_expl_statut_circ_after']){
			$req="update exemplaires set expl_statut=".$this->info_expl[$expl_id]['serialcirc_expl_statut_circ_after']." where expl_id=".$expl_id;
			pmb_mysql_query($req);
		}
		
		// traitement résa
		$query = "select count(1) from resa where resa_idbulletin=".$this->info_expl[$expl_id]['bulletin_id'];
		$result = @pmb_mysql_query($query);
		if(@pmb_mysql_result($result, 0, 0)) {
			$status=2;// mail de résa sera envoyé à l'affectation dans résa à traiter
		}
		return $status;
	}
	
	static public function delete_expl($expl_id){
		$req="delete from serialcirc_expl where num_serialcirc_expl_id =$expl_id";
		pmb_mysql_query($req);
		$req="delete from serialcirc_circ where num_serialcirc_circ_expl =$expl_id";
		pmb_mysql_query($req);
	}
	
	public function ask_send_mail($expl_id,$empr_id,$objet,$texte_mail){
		global $biblio_name,$biblio_email,$PMBuseremailbcc;
		
		$expl_info=$this->expl_info($expl_id);
		$empr_info=static::empr_info($empr_id);
		$texte_mail=str_replace("!!issue!!", $expl_info['perio']."-".$expl_info['numero'], $texte_mail);
		$texte_mail = str_replace("!!biblio_name!!", $biblio_name, $texte_mail);
		mailpmb($empr_info["prenom"]." ".$empr_info["nom"], $empr_info["mail"], $objet,	$texte_mail, $biblio_name, $biblio_email,"", "", $PMBuseremailbcc,1);
		return true;
	}
	
	public function resa_accept($expl_id,$empr_id){
		
		$req="select * from bulletins, exemplaires where bulletin_id=expl_bulletin and expl_id=$expl_id";
		$res=pmb_mysql_query($req);
		if ($r=pmb_mysql_fetch_object($res)) {
			//			$resa=new reservation($empr_id,0,$r->bulletin_id);
			//			$resa->add();
			$req="update serialcirc_circ set serialcirc_circ_hold_asked=2 where
			num_serialcirc_circ_expl=$expl_id and num_serialcirc_circ_empr=$empr_id";
			$res=pmb_mysql_query($req);
		}
		// mail de résa sera envoyé à l'affectation dans résa à traité
		return true;
	}
	
	public function resa_none($expl_id,$empr_id){
		global $msg;
		$req="update serialcirc_circ set serialcirc_circ_hold_asked=0 where
		num_serialcirc_circ_expl=$expl_id and num_serialcirc_circ_empr=$empr_id";
		pmb_mysql_query($req);
		
		// mail
		$this->ask_send_mail($expl_id,$empr_id,$msg["serialcirc_circ_title"],$msg['serialcirc_resa_no_mail_text']);
		return true;
	}
	
	public function get_next_diff_id($expl_id){
		$found=0;
		foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $id_diff => $diffusion){
			// pas en circ on retourne le premier
			if(!$this->info_expl[$expl_id]['num_diff']) return $id_diff;
			if($id_diff==$this->info_expl[$expl_id]['num_diff'])$found=1;
			elseif( $found ){
				return $id_diff;
			}
		}
		// le dernier l'a consulté; pas de suivant;
		return 0;
	}
	
	public function get_next_empr_id($expl_id) {
		$found = 0;
		$current_group = '';
		foreach ($this->info_circ[$expl_id] as $empr_id => $info_circ) {
			if ($found && (!$current_group || ($current_group != $info_circ['group_name']))) {
				// On a trouvé au tour d'avant et cet utilisateur n'est pas du même groupe
				return $empr_id;
			}
			if (!$this->info_expl[$expl_id]['current_empr']) {
				// Premier retour, on passe au deuxième emprunteur
				$found = 1;
				continue;
			}
			if ($this->info_expl[$expl_id]['current_empr'] == $empr_id) {
				$current_group = $info_circ['group_name'];
				$found = 1;
			}
		}
		return 0;
	}
	
	// l'exemplaire revient à la bib
	public function return_expl($expl_id){
		global $msg;
		
		if($this->info_expl[$expl_id]['serialcirc_type'] == SERIALCIRC_TYPE_rotative){
			// delete et changement de statut éventuel
			$status=$this->delete_diffusion($expl_id);
		}else{// SERIALCIRC_TYPE_star
			// envoi au empr suivant
			$next_diff_id = $this->get_next_diff_id($expl_id);
			$next_empr_id = $this->get_next_empr_id($expl_id);
			if($next_empr_id){
				$req="UPDATE serialcirc_expl SET num_serialcirc_expl_serialcirc_diff=".$next_diff_id.",
				serialcirc_expl_state_circ=1,
				serialcirc_expl_ret_asked=0,
				serialcirc_expl_trans_asked=0,
				serialcirc_expl_trans_doc_asked=0,
				num_serialcirc_expl_current_empr=".$next_empr_id."
				where num_serialcirc_expl_id= $expl_id";
				
				pmb_mysql_query($req);
				$status=2;
			}else{
				// C'est terminé!
				$status=$this->delete_diffusion($expl_id);
			}
		}
		switch($status){
			case "2":
				$info=$msg["circ_retour_ranger_resa"];
				break;
			default://On ne mets pas de message différent si l'exemplaire a déjà été retourné
				$info=$msg["serialcirc_info_retour"];
				break;
		}
		return $info;
	}
	
	public function print_diffusion($expl_id,$start_diff_id){
		$tpl=$this->build_print_diffusion($expl_id,$start_diff_id);
		global $class_path;
		$html2pdf = new Html2Pdf('P','A4','fr');
		$html2pdf->writeHTML($tpl);
		$html2pdf->output('diffusion.pdf');
	}
	
	public function print_sel_diffusion($list){
		$tpl='';
		foreach($list as $circ){
			$expl_id=$circ['expl_id'];
			$start_diff_id=$circ['start_diff_id'];
			
			$tpl.=$this->build_print_diffusion($expl_id,$start_diff_id);
		}
		global $class_path;
		$html2pdf = new Html2Pdf('P','A4','fr');
		$html2pdf->writeHTML($tpl);
		$html2pdf->output('diffusion.pdf');
	}
	
	protected function get_empr_list($expl_id,$start_diff_id) {
		$empr_list = array();
		if($start_diff_id) {
			$found = 0;
		} else {
			$found = 1;
		}
		reset($this->info_expl[$expl_id]['serialcirc_diff']->diffusion);
		foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $diff_id => $diffusion){
			if($start_diff_id && !$found){
				if($start_diff_id==$diff_id)$found=1;
			}
			if($found){
				if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_group ){
					foreach($diffusion['group'] as $empr_group){
						$empr_list[$empr_group["num_empr"]]=$diff_id;
					}
				}else  {
					$empr_list[$diffusion["num_empr"]]=$diff_id;
				}
			}
		}
		return $empr_list;
	}
	
	protected function get_empr_list_to_print($expl_id,$start_diff_id) {
		$empr_list_to_print = array();
		$empr_list = array();
		if($start_diff_id) {
			$found = 0;
		} else {
			$found = 1;
		}
		reset($this->info_expl[$expl_id]['serialcirc_diff']->diffusion);
		foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $diff_id => $diffusion){
			if($start_diff_id && !$found){
				if($start_diff_id==$diff_id)$found=1;
			}
			if($found){
				if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_group ){
					foreach($diffusion['group'] as $empr_group){
						$empr_list[$empr_group["num_empr"]]=$diff_id;
					}
				}else  {
					$empr_list[$diffusion["num_empr"]]=$diff_id;
				}
				if(($this->info_expl[$expl_id]['serialcirc_diff']->circ_type == SERIALCIRC_TYPE_star) && !count($empr_list_to_print)){
					// on n'imprime que le suivant dans la liste
					$empr_list_to_print = $empr_list;
				}
			}
		}
		return $empr_list_to_print;
	}
	
	protected function get_empr_days($expl_id,$start_diff_id) {
		$empr_days = array();
		if($start_diff_id) {
			$found = 0;
		} else {
			$found = 1;
		}
		reset($this->info_expl[$expl_id]['serialcirc_diff']->diffusion);
		foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $diff_id => $diffusion){
			if($start_diff_id && !$found){
				if($start_diff_id==$diff_id)$found=1;
			}
			if($found){
				if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_group ){
					foreach($diffusion['group'] as $empr_group){
						if($empr_group["duration"])
							$empr_days[$empr_group["num_empr"]]=$empr_group["duration"];
						else
							$empr_days[$empr_group["num_empr"]]=$this->info_expl[$expl_id]['serialcirc_diff']->duration;
					}
				}else  {
					if($diffusion["duration"])	{
						$empr_days[$diffusion["num_empr"]]=$diffusion["duration"]; // durée de consultation particulière
					} else {
						$empr_days[$diffusion["num_empr"]]=$this->info_expl[$expl_id]['serialcirc_diff']->duration;
					}
				}
			}
		}
		return $empr_days;
	}
	
	public function build_print_diffusion($expl_id,$start_diff_id){
		global $serialcirc_circ_pdf_diffusion,$charset;
		global $msg;
		
		// AP : start_diff_id est en fait maintenant l'id de l'emprunteur
		// Si la circulation est déjà lancée, on passe par la table serialcirc_circ
		if (!empty($this->info_circ[$expl_id]) && count($this->info_circ[$expl_id])) {
			return $this->build_print_diffusion_from_current_circ($expl_id,$start_diff_id);
		}
		$end = false;
		// On remet l'identifiant de diffusion pour garder le fonctionnement précédent
		if(!empty($this->info_expl[$expl_id]) && is_array($this->info_expl[$expl_id]['serialcirc_diff']->diffusion)) {
			foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $diff_id => $diffusion){
				// Si l'identifiant n'est pas transmis on prend le premier
				// Si l'identifiant correspond à l'identifiant courant, on récupère l'identifiant de la diffusion
				if(!$start_diff_id || (($diffusion["empr_type"] == SERIALCIRC_EMPR_TYPE_empr) && ($start_diff_id == $diffusion['num_empr']))){
					$start_diff_id = $diff_id;
					break;
				}
				// Si c'est un groupe, on le parcourt
				if(($diffusion["empr_type"] == SERIALCIRC_EMPR_TYPE_group)) {
					foreach ($diffusion['group'] as $empr) {
						if ($start_diff_id == $empr['num_empr']) {
							$start_diff_id = $diff_id;
							//break 2;
							$end = true;
							break;
						}
					}
					if($end) break;
				}
			}
		}
		if (empty($this->info_expl[$expl_id])) return '';
		$req="UPDATE serialcirc_expl SET num_serialcirc_expl_serialcirc_diff=".$start_diff_id.",
		serialcirc_expl_state_circ=1,
		serialcirc_expl_start_date=CURDATE()
		where num_serialcirc_expl_id= $expl_id";
		pmb_mysql_query($req);
		
		$req="select date_format(CURDATE(), '".$msg["format_date"]."') as print_date";
		$result = pmb_mysql_query($req);
		$obj = pmb_mysql_fetch_object($result);
		$print_date = $obj->print_date;
		
		$tpl = $serialcirc_circ_pdf_diffusion;
		$tpl=str_replace("!!expl_cb!!", htmlentities($this->info_expl[$expl_id]['expl_cb'],ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace("!!date!!", htmlentities($this->info_expl[$expl_id]['mention_date'],ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace("!!periodique!!", htmlentities($this->info_expl[$expl_id]['serial_title'],ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace("!!numero!!", htmlentities($this->info_expl[$expl_id]['numero'],ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace("!!print_date!!", htmlentities($print_date,ENT_QUOTES,$charset), $tpl);
		//	$tpl=str_replace("!!abonnement!!", htmlentities($this->info_expl[$expl_id]['serialcirc_diff']->abt_name,ENT_QUOTES,$charset), $tpl);
		
		if($start_diff_id) {
			$found = 0;
		} else {
			$found = 1;
		}
		$empr_list = array();
		$empr_days = array();
		$empr_list_to_print = array();
		foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $diff_id => $diffusion){
			
			if($start_diff_id && !$found){
				if($start_diff_id==$diff_id)$found=1;
			}
			if($found){
				if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_group ){
					foreach($diffusion['group'] as $empr_group){
						$empr_list[$empr_group["num_empr"]]=$diff_id;
						if($empr_group["duration"])
							$empr_days[$empr_group["num_empr"]]=$empr_group["duration"];
							else
								$empr_days[$empr_group["num_empr"]]=$this->info_expl[$expl_id]['serialcirc_diff']->duration;
								
								if($diffusion['type_diff']==1 && !$empr_group["responsable"]){
									// groupe marguerite: on n'imprimera pas ce lecteur sauf le responsable
									//$empr_no_display[$empr_group["num_empr"]]=1;
								}
					}
				}else  {
					$empr_list[$diffusion["num_empr"]]=$diff_id;
					if($diffusion["duration"])	$empr_days[$diffusion["num_empr"]]=$diffusion["duration"]; // durée de consultation particulière
					else $empr_days[$diffusion["num_empr"]]=$this->info_expl[$expl_id]['serialcirc_diff']->duration;
				}
				if(($this->info_expl[$expl_id]['serialcirc_diff']->circ_type == SERIALCIRC_TYPE_star) && !count($empr_list_to_print)){
					// on n'imprime que le suivant dans la liste
					$empr_list_to_print = $empr_list;
				}
			}
			$last_empr=$this->info_expl[$expl_id]['serialcirc_diff']->empr_info[$diffusion["num_empr"]];
		}
		if (!count($empr_list_to_print)) {
			$empr_list_to_print = $empr_list;
		}
		$this->gen_circ($empr_list,$empr_days, $expl_id);
		
		$gen_tpl= new serialcirc_print_fields($this->info_expl[$expl_id]['num_serialcirc']);
		if (!$gen_tpl->circ_tpl) {
			$gen_tpl->circ_tpl = serialcirc_print_fields::get_default_tpl();
		}
		$header_list=$gen_tpl->get_header_list();
		$nb_col=count($header_list);
		if (!$nb_col) return '';
		$width_col=(int) (100/$nb_col);
		
		$th = "";
		foreach($header_list as $titre){
			$th.="<th style='width: $width_col%; text-align: left'>".htmlentities($titre,ENT_QUOTES,$charset)."</th>";
		}
		$tpl=str_replace("!!th!!", $th, $tpl);
		$tr_list="";
		foreach($empr_list_to_print as $empr_id=>$diff_id){
			if(isset($empr_no_display[$empr_id]) && $empr_no_display[$empr_id]) continue;
			$data=array();
			$data['empr_id']=$empr_id;
			$data_fields=$gen_tpl->get_line($data);
			$td_list="";
			foreach($data_fields as $field){
				$td_list.="<td style='width: $width_col%; text-align: left'>".htmlentities($field,ENT_QUOTES,$charset)."</td>";
			}
			$tr_list.="<tr>".$td_list."</tr>";
		}
		$tpl=str_replace("!!table_contens!!", $tr_list, $tpl);
		
		
		
		if($gen_tpl->piedpage){
			$data=array();
			$data['expl']=$this->info_expl[$expl_id];
			$data['last_empr']=$last_empr;
			//		 	printr($data['expl']);
			$tpl.=H2o::parseString($gen_tpl->piedpage)->render($data);
		}
		
		if($charset!="utf-8"){
			$tpl=encoding_normalize::utf8_normalize($tpl);
		}
		return $tpl;
	}
	
	public function build_print_diffusion_from_current_circ($expl_id,$start_empr_id){
		global $serialcirc_circ_pdf_diffusion,$charset;
		global $msg;
		
		if (!$this->info_expl[$expl_id]) return '';
		$req="UPDATE serialcirc_expl SET
		num_serialcirc_expl_current_empr = ".$start_empr_id.",
		serialcirc_expl_state_circ=1,
		serialcirc_expl_start_date=CURDATE()
		where num_serialcirc_expl_id= $expl_id";
		pmb_mysql_query($req);
		
		$req="select date_format(CURDATE(), '".$msg["format_date"]."') as print_date";
		$result = pmb_mysql_query($req);
		$obj = pmb_mysql_fetch_object($result);
		$print_date = $obj->print_date;
		
		$tpl = $serialcirc_circ_pdf_diffusion;
		$tpl=str_replace("!!expl_cb!!", htmlentities($this->info_expl[$expl_id]['expl_cb'],ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace("!!date!!", htmlentities($this->info_expl[$expl_id]['mention_date'],ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace("!!periodique!!", htmlentities($this->info_expl[$expl_id]['serial_title'],ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace("!!numero!!", htmlentities($this->info_expl[$expl_id]['numero'],ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace("!!print_date!!", htmlentities($print_date,ENT_QUOTES,$charset), $tpl);
		
		$found = 0;
		if(!$start_empr_id) {
			$found = 1;
		}
		
		$gen_tpl = new serialcirc_print_fields($this->info_expl[$expl_id]['num_serialcirc']);
		if (!$gen_tpl->circ_tpl) {
			$gen_tpl->circ_tpl = serialcirc_print_fields::get_default_tpl();
		}
		$header_list = $gen_tpl->get_header_list();
		$nb_col = count($header_list);
		if (!$nb_col) return '';
		$width_col = (int) (100/$nb_col);
		$th = '';
		
		foreach($header_list as $titre){
			$th.= "<th style='width: $width_col%; text-align: left'>".htmlentities($titre,ENT_QUOTES,$charset)."</th>";
		}
		
		$tpl = str_replace("!!th!!", $th, $tpl);
		$tr_list = "";
		$current_group = '';
		
		foreach ($this->info_circ[$expl_id] as $empr_id => $info_circ) {
			if($found && ($this->info_expl[$expl_id]['serialcirc_type'] == SERIALCIRC_TYPE_star) && $current_group && ($current_group != $info_circ['group_name'])){
				// On a changé de groupe et on est en circulation en étoile, on s'arrête là
				break;
			}
			$current_group = $info_circ['group_name'];
			if ($start_empr_id && !$found && ($start_empr_id == $empr_id)) {
				$found = 1;
			}
			if (!$found) {
				continue;
			}
			$data = array();
			$data['empr_id'] = $empr_id;
			$data_fields = $gen_tpl->get_line($data);
			$td_list="";
			foreach($data_fields as $field){
				$td_list.="<td style='width: ".$width_col."%; text-align: left;'>".htmlentities($field,ENT_QUOTES,$charset)."</td>";
			}
			$tr_list.="<tr>".$td_list."</tr>";
			if(($this->info_expl[$expl_id]['serialcirc_type'] == SERIALCIRC_TYPE_star) && !$current_group){
				// On n'est pas dans un groupe et on est en circulation en étoile, on s'arrête là
				break;
			}
		}
		$tpl=str_replace("!!table_contens!!", $tr_list, $tpl);
		
		if ($gen_tpl->piedpage) {
			$data = array();
			$data['expl'] = $this->info_expl[$expl_id];
			$data['last_empr'] = $last_empr;
			$tpl.= H2o::parseString($gen_tpl->piedpage)->render($data);
		}
		if($charset != "utf-8"){
			$tpl = encoding_normalize::utf8_normalize($tpl);
		}
		return $tpl;
	}
	
	public function gen_circ($empr_list, $empr_days,$expl_id){
		$order=0;
		$nb_days=0;
		if($this->info_expl[$expl_id]['serialcirc_diff']->virtual_circ){
			foreach($empr_list as $empr_id=>$diff_id){
				
				$req=" update serialcirc_circ SET
				
				serialcirc_circ_expected_date=DATE_ADD(CURDATE(),INTERVAL $nb_days DAY)
				where
				num_serialcirc_circ_diff=".$diff_id ." and
				num_serialcirc_circ_expl=".$expl_id ." and
				num_serialcirc_circ_empr=". $empr_id." and
				serialcirc_circ_subscription=1 ";
				pmb_mysql_query($req);
				$order++;
				$nb_days+=$empr_days[$empr_id];
			}
		}else{
			$req=" delete from serialcirc_circ where num_serialcirc_circ_expl=".$expl_id  ;
			pmb_mysql_query($req);
			
			foreach($empr_list as $empr_id=>$diff_id){
				
				$req=" insert into serialcirc_circ SET
				num_serialcirc_circ_diff=".$diff_id .",
				num_serialcirc_circ_expl=".$expl_id .",
				num_serialcirc_circ_empr=". $empr_id.",
				serialcirc_circ_subscription=1,
				serialcirc_circ_order=". $order.",
				serialcirc_circ_expected_date=DATE_ADD(CURDATE(),INTERVAL $nb_days DAY),
				num_serialcirc_circ_serialcirc=".$this->info_expl[$expl_id]['num_serialcirc'].",
				serialcirc_circ_group_name='".$this->info_expl[$expl_id]['serialcirc_diff']->diffusion[$diff_id]['empr_name']."'";
				pmb_mysql_query($req);
				$order++;
				$nb_days+=$empr_days[$empr_id];
			}
		}
		// on change le statut si demandé
		if($this->info_expl[$expl_id]['serialcirc_diff']->expl_statut_circ){
			$req="update exemplaires set expl_statut=".$this->info_expl[$expl_id]['serialcirc_diff']->expl_statut_circ." where expl_id=".$expl_id;
			pmb_mysql_query($req);
		}
	}
	
	public function repair_diffusion($expl_id) {
		// Si la circulation est déjà lancée, on ne doit pas avoir besoin de réparation
		if (!empty($this->info_circ[$expl_id]) && count($this->info_circ[$expl_id])) {
			return false;
		}
		if(!empty($this->info_expl[$expl_id])) {
			$num_serialcirc = intval($this->info_expl[$expl_id]['num_serialcirc']);
			$expl_abt_num = intval($this->info_expl[$expl_id]['expl_abt_num']);
			if($num_serialcirc && $expl_abt_num) {
				$query = "SELECT num_serialcirc_abt FROM serialcirc WHERE id_serialcirc = ".$num_serialcirc." AND num_serialcirc_abt = 0";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)) {
					//A quelle circulation est maintenant rattachée l'abonnement ?
					$query = "SELECT id_serialcirc FROM serialcirc WHERE num_serialcirc_abt = ".$expl_abt_num;
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)) {
						$new_id_serialcirc = pmb_mysql_result($result, 0, 'id_serialcirc');
						$query = "UPDATE serialcirc_expl SET num_serialcirc_expl_serialcirc = '".$new_id_serialcirc."' WHERE num_serialcirc_expl_serialcirc = ".$num_serialcirc." AND num_serialcirc_expl_id = ".$expl_id;
						pmb_mysql_query($query);
						return true;
					}
				}
			}
		}
		return false;
	}
	
	public function send_mail($expl_id,$objet,$texte_mail){
		global $biblio_name,$biblio_email,$PMBuseremailbcc;
		if (!$this->info_expl[$expl_id]) return false;
		// Si pas encore recu par l'emprunteur on ne fait rien...
		if(!$empr_id = $this->info_expl[$expl_id]['current_empr']) return false;
		$empr_info = static::empr_info($empr_id);
		$texte_mail=str_replace("!!issue!!", $this->info_expl[$expl_id]["serial_title"]." - ".$this->info_expl[$expl_id]['numero'], $texte_mail);
		return mailpmb($empr_info["prenom"]." ".$empr_info["nom"], $empr_info["mail"], $objet,	$texte_mail, $biblio_name, $biblio_email,"", "", $PMBuseremailbcc,1);
	}
	
	public function send_alert($expl_id){
		global $biblio_name, $biblio_email, $PMBuseremailbcc, $msg, $opac_url_base, $opac_connexion_phrase;
		
		$req=" delete from serialcirc_circ where num_serialcirc_circ_expl=".$expl_id;
		pmb_mysql_query($req);
		
		$empr_list = array();
		foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $diff_id => $diffusion){
			if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_group ){
				foreach($diffusion['group'] as $empr_group){
					$empr_list[$empr_group["num_empr"]]=$diff_id;
				}
			}else  {
				$empr_list[$diffusion["num_empr"]]=$diff_id;
			}
		}
		
		$req="UPDATE serialcirc_expl SET
		serialcirc_expl_state_circ=0,
		serialcirc_expl_start_date=CURDATE()
		where num_serialcirc_expl_id= $expl_id";
		pmb_mysql_query($req);
		
		$order=0;
		foreach($empr_list as $empr_id=>$diff_id){
			
			$req=" insert into serialcirc_circ SET
			num_serialcirc_circ_diff=".$diff_id .",
			num_serialcirc_circ_expl=".$expl_id .",
			num_serialcirc_circ_empr=". $empr_id.",
			serialcirc_circ_subscription=0,
			serialcirc_circ_order=". $order.",
			num_serialcirc_circ_serialcirc=".$this->info_expl[$expl_id]['num_serialcirc'].",
			serialcirc_circ_group_name='".$this->info_expl[$expl_id]['serialcirc_diff']->diffusion[$diff_id]['empr_name']."'";
			pmb_mysql_query($req);
			$order++;
			
			// envoi email alerte
			$expl_info=$this->expl_info($expl_id);
			$empr_info=static::empr_info($empr_id);
			$dates = time();
			$login = $empr_info['empr_login'];
			$code=md5($opac_connexion_phrase.$login.$dates);
			
			$issue = "<a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$expl_info['id_bulletin']."&code=".$code."&emprlogin=".$login."&date_conex=".$dates."'>".$expl_info['perio']."-".$expl_info['numero']."</a>";
			
			$objet = $msg['serialcirc_send_alert_mail_object'];
			
			$texte_mail = $msg['serialcirc_send_alert_mail_text'];
			$texte_mail = str_replace("!!issue!!", $issue, $texte_mail);
			$texte_mail = str_replace("!!biblio_name!!", $biblio_name, $texte_mail);
			
			mailpmb($empr_info["prenom"]." ".$empr_info["nom"], $empr_info["mail"], $objet,	$texte_mail, $biblio_name, $biblio_email,"", "", $PMBuseremailbcc,1);
		}
	}
	
	public function call_expl($expl_id){
		global $biblio_name, $msg;
		
		$req="UPDATE serialcirc_expl SET
		serialcirc_expl_ret_asked=1
		where num_serialcirc_expl_id= $expl_id";
		pmb_mysql_query($req);
		
		if(!$empr_id=$this->info_expl[$expl_id]['current_empr']) return false;
		
		$req="UPDATE serialcirc_circ SET
		serialcirc_circ_ret_asked = serialcirc_circ_ret_asked+1
		where num_serialcirc_circ_expl= $expl_id and num_serialcirc_circ_empr=$empr_id";
		pmb_mysql_query($req);
		
		$expl_info=$this->expl_info($expl_id);
		$objet=$msg["serialcirc_circ_title"];
		$texte_mail = $msg["serialcirc_call_mail_text"];
		$texte_mail = str_replace("!!issue!!", $expl_info['perio']."-".$expl_info['numero'], $texte_mail);
		$texte_mail = str_replace("!!biblio_name!!", $biblio_name, $texte_mail);
		
		$status=$this->send_mail($expl_id,$objet,$texte_mail);
		return $status;
	}
	
	public function call_insist($expl_id){
		global $msg, $biblio_name;
		
		$req="UPDATE serialcirc_expl SET
		serialcirc_expl_trans_doc_asked=1
		where num_serialcirc_expl_id= $expl_id";
		pmb_mysql_query($req);
		
		if(!$empr_id=$this->info_expl[$expl_id]['current_empr']) return false;
		
		$req="UPDATE serialcirc_circ SET
		serialcirc_circ_trans_doc_asked = serialcirc_circ_trans_doc_asked+1
		where num_serialcirc_circ_expl= $expl_id and num_serialcirc_circ_empr=$empr_id";
		pmb_mysql_query($req);
		
		$expl_info=$this->expl_info($expl_id);
		$objet=$msg["serialcirc_circ_title"];
		$texte_mail = $msg["serialcirc_transmission_mail_text"];
		$texte_mail = str_replace("!!issue!!", $expl_info['perio']."-".$expl_info['numero'], $texte_mail);
		$texte_mail = str_replace("!!biblio_name!!", $biblio_name, $texte_mail);
		
		$status=$this->send_mail($expl_id,$objet,$texte_mail);
		return $status;
	}
	
	public function do_trans($expl_id){
		global $msg, $biblio_name;
		$req="UPDATE serialcirc_expl SET
		serialcirc_expl_trans_doc_asked=2
		where num_serialcirc_expl_id= $expl_id";
		pmb_mysql_query($req);
		
		if(!$empr_id=$this->info_expl[$expl_id]['current_empr']) return false;
		
		$req="UPDATE serialcirc_circ SET
		serialcirc_circ_trans_doc_asked = serialcirc_circ_trans_doc_asked+1
		where num_serialcirc_circ_expl= $expl_id and num_serialcirc_circ_empr=$empr_id";
		pmb_mysql_query($req);
		
		$expl_info=$this->expl_info($expl_id);
		$objet=$msg["serialcirc_circ_title"];
		$texte_mail = $msg["serialcirc_transmission_mail_text"];
		$texte_mail = str_replace("!!issue!!", $expl_info['perio']."-".$expl_info['numero'], $texte_mail);
		$texte_mail = str_replace("!!biblio_name!!", $biblio_name, $texte_mail);
		
		$status=$this->send_mail($expl_id,$objet,$texte_mail);
		return $status;
	}
	
	public function build_diff_sel($expl_id){
		global $charset;
		$tpl="
			<select name='!!zone!!_group_circ_select_$expl_id' id='!!zone!!_group_circ_select_$expl_id' style='width: 15em;'>
				!!diff_select!!
			</select>";
		$list="";
		if (count($this->info_circ[$expl_id])) {
			$current_group = '';
			$current_group_first_empr = 0;
			$checked = '';
			foreach ($this->info_circ[$expl_id] as $empr_id => $info_circ) {
				if ($current_group && ($info_circ['group_name'] != $current_group)) {
					// On a fini le parcourt d'un groupe, on l'affiche, on réinitialise
					$list.="<option value='".$current_group_first_empr."' $checked >".htmlentities($current_group, ENT_QUOTES, $charset)."</option>";
					$current_group = '';
					$current_group_first_empr = 0;
					$checked = '';
				}
				if ($info_circ['current_owner'] || ($this->info_expl[$expl_id]['current_empr'] == $empr_id)) {
					$checked = " selected='selected' ";
				}
				if ($info_circ['group_name'] && ($info_circ['group_name'] == $current_group)) {
					// On est toujours dans le groupe, on ne fait rien
					continue;
				}
				if ($info_circ['group_name']) {
					// On rentre dans un groupe, on le stocke, on n'affiche rien pour l'instant
					$current_group = $info_circ['group_name'];
					$current_group_first_empr = $empr_id;
					continue;
				}
				// Si on arrive ici, on est dans le cas d'un emprunteur sans groupe
				$empr_infos = static::empr_info($empr_id);
				$list.="<option value='".$empr_id."' $checked >".htmlentities($empr_infos['empr_libelle'], ENT_QUOTES, $charset)."</option>";
				$checked = '';
			}
			$tpl=str_replace("!!diff_select!!", $list, $tpl);
			return $tpl;
		}
		foreach($this->info_expl[$expl_id]['serialcirc_diff']->diffusion as $diffusion){
			if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_empr && $this->info_expl[$expl_id]['serialcirc_diff']->virtual_circ ){
				if(empty($this->info_circ[$expl_id][$diffusion["num_empr"]]['subscription']))	continue;
			}
			if($diffusion["empr_type"]== SERIALCIRC_EMPR_TYPE_group ) {
				$name=$diffusion["empr_name"];
				// On récupère le premier emprunteur du groupe
				$num_empr = $diffusion['group'][0]['num_empr'];
			} else {
				$name=$this->info_expl[$expl_id]['serialcirc_diff']->empr_info[$diffusion["num_empr"]]["empr_libelle"];
				$num_empr = $diffusion['num_empr'];
			}
			if($this->info_expl[$expl_id]['num_diff'] == $diffusion['id']) {
				$checked=" selected='selected' ";
			} else {
				$checked="";
			}
			$list.="<option value='".$num_empr."' $checked >".htmlentities($name, ENT_QUOTES, $charset)."</option>";
		}
		$tpl=str_replace("!!diff_select!!", $list, $tpl);
		return $tpl;
	}
	
	public function build_empr_list($expl_id){
		global $charset;
		// on liste les empr réel et ceux du group
		$name_list="";
		
		foreach ($this->info_circ[$expl_id] as $empr_id => $info_circ) {
			$empr_infos = static::empr_info($empr_id);
			$name = "<a href='".$empr_infos['view_link']."'>".htmlentities(($info_circ['group_name'] ? '('.$info_circ['group_name'].') ' : '').$empr_infos["empr_libelle"],ENT_QUOTES,$charset)."</a><br />";
			if ($info_circ['current_owner'] || ($this->info_expl[$expl_id]['current_empr'] == $empr_id))	 {
				$name = "<span class='erreur'>".$name."</span><br />";
			}
			$name_list.= $name;
		}
		return $name_list;
	}
	
	public function build_expl_form($expl_id,$tpl,$zone=''){
		global $charset;
		$tpl=str_replace("!!expl_id!!", $expl_id, $tpl);
		$tpl=str_replace("!!bull_id!!", $this->info_expl[$expl_id]['bulletin_id'], $tpl);
		$tpl=str_replace("!!expl_cb!!", "<a href='".$this->info_expl[$expl_id]['expl_link']."'>".htmlentities($this->info_expl[$expl_id]['expl_cb'],ENT_QUOTES,$charset)."</a>", $tpl);
		$tpl=str_replace("!!date!!", htmlentities($this->info_expl[$expl_id]['mention_date'],ENT_QUOTES,$charset)."</a>", $tpl);
		$tpl=str_replace("!!periodique!!","<a href='".$this->info_expl[$expl_id]['serial_link']."'>". htmlentities( $this->info_expl[$expl_id]['serial_title'],ENT_QUOTES,$charset), $tpl);
		$tpl=str_replace("!!numero!!","<a href='".$this->info_expl[$expl_id]['bull_link']."'>". htmlentities($this->info_expl[$expl_id]['numero'],ENT_QUOTES,$charset)."</a>", $tpl);
		$tpl=str_replace("!!abonnement!!",  "<a href='".$this->info_expl[$expl_id]['cirdiff_link']."'>".htmlentities( $this->info_expl[$expl_id]['expl_abt_name'],ENT_QUOTES,$charset)."</a>", $tpl);
		$tpl=str_replace("!!destinataire!!",$this->build_diff_sel($expl_id), $tpl);
		$tpl=str_replace("!!empr_list!!", $this->build_empr_list($expl_id), $tpl);
		$tpl=str_replace("!!zone!!", $zone, $tpl);
		return $tpl;
	}
	
	public function gen_circ_cb($cb) {
		global $serialcirc_circ_cb_notfound;
		$info="";
		$req="select * from serialcirc_expl,exemplaires where expl_cb='$cb' and expl_id=num_serialcirc_expl_id ";
		$resultat=pmb_mysql_query($req);
		if ($resultat) {
			if (!pmb_mysql_num_rows($resultat)) {
				$this->info_cb['cb']='';
				return str_replace("!!cb!!", $cb, $serialcirc_circ_cb_notfound);
			}
			$r=pmb_mysql_fetch_object($resultat);
			$info.=list_serialcirc_expl_pointage_ui::get_instance(array('point_expl_id' => $r->expl_id))->get_display_list();
		}
		return $info;
	}
	
	
} //serialcirc class end
