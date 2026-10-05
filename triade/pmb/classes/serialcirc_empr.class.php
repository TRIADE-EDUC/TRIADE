<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_empr.class.php,v 1.12 2022/02/10 08:18:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/serialcirc_empr.tpl.php");
require_once($class_path."/serialcirc_diff.class.php");

class serialcirc_empr{
	public $empr_id;	// identifiant de l'emprunteur
	public $serialcirc_list;	// tableau des abonnement de l'emprunteur
	public $info;	// info des listes de circulation de l'emprunteur
	public $serialcirc_circ_list =array(); //tableau des circulations en cours..

	public function __construct($empr_id){
		$this->empr_id = intval($empr_id);
		$this->fetch_data();
	}

	protected function fetch_data(){
		$this->serialcirc_list = array();
	
		$alone = "select distinct id_serialcirc from serialcirc_diff join serialcirc on num_serialcirc_diff_serialcirc = id_serialcirc where num_serialcirc_diff_empr = ".$this->empr_id;
		$group = "select distinct id_serialcirc from serialcirc_diff join serialcirc on num_serialcirc_diff_serialcirc = id_serialcirc join serialcirc_group on num_serialcirc_group_diff = id_serialcirc_diff where num_serialcirc_group_empr = ".$this->empr_id;
 		$query = $alone." union ".$group;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$this->serialcirc_list[] = $row->id_serialcirc;
				$diff = new serialcirc_diff($row->id_serialcirc);
				$this->info[$row->id_serialcirc] = $diff->serial_info;
			}
		}
		$already_start = "select distinct num_serialcirc_circ_serialcirc as id_serialcirc, num_serialcirc_circ_expl as expl_id from serialcirc_circ where num_serialcirc_circ_empr = ".$this->empr_id;
		$result = pmb_mysql_query($already_start);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				if(!in_array($row->id_serialcirc,$this->serialcirc_circ_list)){
					$this->serialcirc_circ_list[] = $row->id_serialcirc;
				}
				if(!isset($this->info[$row->id_serialcirc]) || !$this->info[$row->id_serialcirc]){
					$diff = new serialcirc_diff($row->id_serialcirc);
					$this->info[$row->id_serialcirc] = $diff->serial_info;
				}
				$this->info[$row->id_serialcirc]['expls'][] = $row->expl_id;
			}
		}
	}

	public function get_list(){
		global $msg,$charset;
		global $empr_serialcirc_tmpl,$empr_serialcirc_tmpl_item;	
		global $empr_serialcirc_circ_tmpl_item;
		
		$tpl = "";
		if(count($this->serialcirc_list) || count($this->serialcirc_circ_list)){
			$tpl=$empr_serialcirc_tmpl;
			$items="";
			
			$query = "select empr_cb from empr where id_empr = ".$this->empr_id;
			$result = pmb_mysql_query($query);
			$cb = pmb_mysql_result($result,0,0);
			
			$circ_list = $this->serialcirc_circ_list;
			
			$seriallist_ids="";
			for($i=0; $i<count($this->serialcirc_list) ; $i++){
				$diff_id=$this->serialcirc_list[$i];
				if($seriallist_ids) $seriallist_ids.="|";
				$expls = "";
				$seriallist_ids.=$diff_id;
				foreach($circ_list as $j => $id){
					if ($id == $diff_id){
						$expls = "";
						foreach($this->info[$diff_id]['expls'] as $expl){
							$query = "select expl_cb,expl_notice, expl_bulletin from exemplaires where expl_id = ".$expl;
							$result =pmb_mysql_query($query);
							if(pmb_mysql_num_rows($result)){
								$row  = pmb_mysql_fetch_object($result);
								if($row->expl_notice){
									$notice = new mono_display($row->expl_notice);
									$libelle = $notice->header;
								}else{
									$bulletin = new bulletinage_display($row->expl_bulletin);
									$libelle = $bulletin->display;
								}
								if($expls)$expls.="<br />";
								$expls.= "<a href='./circ.php?categ=serialcirc&cb=".$row->expl_cb."'>".$libelle."</a>";
							}
						}
						unset($circ_list[$j]);
					}
				}
								
				$item=$empr_serialcirc_tmpl_item;
				$item = str_replace("!!periodique!!","<a href='".$this->info[$diff_id]['serial_link']."'>".htmlentities($this->info[$diff_id]['serial_name'],ENT_QUOTES,$charset)."</a>",$item);
				$item=str_replace('!!abt!!',   "<a href='".$this->info[$diff_id]['serialcirc_link']."'>".htmlentities($this->info[$diff_id]['abt_name'],ENT_QUOTES,$charset)."</a>" , $item);	
				$item=str_replace('!!bulletinage_see!!',   "<a href='".$this->info[$diff_id]['bulletinage_link']."'>".htmlentities($msg['link_notice_to_bulletinage'],ENT_QUOTES,$charset)."</a>" , $item);	
				$item=str_replace('!!exemplaire_see!!',   $expls , $item);
				$item=str_replace('!!id!!',$diff_id,$item);
				$items.=$item;
			}
			
			if(count($circ_list)){
				for($i=0; $i<count($circ_list) ; $i++){
					$expls = "";
					$diff_id=$circ_list[$i];
					foreach($this->info[$diff_id]['expls'] as $expl){
						$query = "select expl_cb,expl_notice, expl_bulletin from exemplaires where expl_id = ".$expl;
							$result =pmb_mysql_query($query);
							if(pmb_mysql_num_rows($result)){
								$row  = pmb_mysql_fetch_object($result);
								if($row->expl_notice){
									$notice = new mono_display($row->expl_notice);
									$libelle = $notice->header;
								}else{
									$bulletin = new bulletinage_display($row->expl_bulletin);
									$libelle = $bulletin->display;
								}
								if($expls)$expls.="<br />";
								$expls.= "<a href='./circ.php?categ=serialcirc&cb=".$row->expl_cb."'>".$libelle."</a>";
							}
					}
					$item=$empr_serialcirc_circ_tmpl_item;
					if(!isset($this->info[$diff_id]['serial_link'])) $this->info[$diff_id]['serial_link'] = '';
					if(!isset($this->info[$diff_id]['serial_name'])) $this->info[$diff_id]['serial_name'] = '';
					if(!isset($this->info[$diff_id]['abt_name'])) $this->info[$diff_id]['abt_name'] = '';
					if(!isset($this->info[$diff_id]['bulletinage_link'])) $this->info[$diff_id]['bulletinage_link'] = '';
					if(!isset($this->info[$diff_id]['serialcirc_link'])) $this->info[$diff_id]['serialcirc_link'] = '';
					
					$item = str_replace("!!periodique!!","<a href='".$this->info[$diff_id]['serial_link']."'>".htmlentities($this->info[$diff_id]['serial_name'],ENT_QUOTES,$charset)."</a>",$item);
					$item=str_replace('!!abt!!',   "<a href='".$this->info[$diff_id]['serialcirc_link']."'>".htmlentities($this->info[$diff_id]['abt_name'],ENT_QUOTES,$charset)."</a>" , $item);
					$item=str_replace('!!bulletinage_see!!',   "<a href='".$this->info[$diff_id]['bulletinage_link']."'>".htmlentities($msg['link_notice_to_bulletinage'],ENT_QUOTES,$charset)."</a>" , $item);
					$item=str_replace('!!exemplaire_see!!',   $expls , $item);
					$item=str_replace('!!id!!',$diff_id,$item);
					$items.=$item;
				}
			}
			
			
			$tpl = str_replace("!!empr_cb!!",$cb,$tpl);
			$tpl = str_replace("!!serialcirc_empr_ids_list!!",$seriallist_ids,$tpl);
			$tpl = str_replace("!!serialcirc_empr_list!!",$items,$tpl);
		}	
		return $tpl;
	}
	
	public function unsbuscribe($serialscirc=array()){
		global $msg;
		$error_message = array();
		for($i=0 ; $i<count($serialscirc) ; $i++){
			$serialscirc[$i] = intval($serialscirc[$i]);
			//suppression simple...
			$query = "delete from serialcirc_diff where num_serialcirc_diff_serialcirc = ".$serialscirc[$i]." and serialcirc_diff_empr_type=0 and num_serialcirc_diff_empr = ".$this->empr_id;
			pmb_mysql_query($query);
			//dans le cas d'un groupe, on évite de supprimer le responsable
			$query = "select serialcirc_group.serialcirc_group_responsable,serialcirc_diff_group_name,abt_name from serialcirc_diff join serialcirc_group on num_serialcirc_group_diff = id_serialcirc_diff join serialcirc on id_serialcirc = num_serialcirc_diff_serialcirc join abts_abts on abt_id = num_serialcirc_abt  where num_serialcirc_diff_serialcirc = ".$serialscirc[$i]." and serialcirc_diff_empr_type=1  and num_serialcirc_group_empr = ".$this->empr_id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				if(!$row->serialcirc_group_responsable){
					$query = "delete serialcirc_group.* from serialcirc_diff join serialcirc_group on num_serialcirc_group_diff = id_serialcirc_diff where num_serialcirc_diff_serialcirc = ".$serialscirc[$i]." and serialcirc_diff_empr_type=1  and num_serialcirc_group_empr = ".$this->empr_id;
					pmb_mysql_query($query);
				}else{
					$error_message[] = sprintf($msg['serialcirc_unsubscribe_group_resp'],$row->abt_name,$row->serialcirc_diff_group_name);
				}
			}
		}
		return $error_message;
	}
	
	public function forward($serialscirc = array(), $new_empr_id = 0, $duplicate = false){
		global $msg, $charset;
		$error_message = array();
		$message = array();
		$forwarded =false;
		$new_empr_id = intval($new_empr_id);
		if($new_empr_id){
			for($i=0 ; $i<count($serialscirc) ; $i++){
				$serialscirc[$i] = intval($serialscirc[$i]);
				//on commence par regarder si le nouveau lecteur n'est pas déjà abonné à la liste...
				$query = "select num_serialcirc_diff_empr, abt_name,concat(empr_nom,' ',empr_prenom) as name from serialcirc_diff join serialcirc on num_serialcirc_diff_serialcirc = id_serialcirc join abts_abts on num_serialcirc_abt = abt_id join empr on id_empr=num_serialcirc_diff_empr where num_serialcirc_diff_serialcirc = ".$serialscirc[$i]." and serialcirc_diff_empr_type=0 and num_serialcirc_diff_empr = ".$new_empr_id;
				$result = pmb_mysql_query($query);
				if(!pmb_mysql_num_rows($result)){
					$query = "select serialcirc_group.num_serialcirc_group_empr,abt_name, concat(empr_nom,' ',empr_prenom) as name from serialcirc_diff join serialcirc_group on num_serialcirc_group_diff = id_serialcirc_diff join serialcirc on id_serialcirc = num_serialcirc_diff_serialcirc join abts_abts on abt_id = num_serialcirc_abt join empr on id_empr=num_serialcirc_group_empr where num_serialcirc_diff_serialcirc = ".$serialscirc[$i]." and serialcirc_diff_empr_type=1  and num_serialcirc_group_empr = ".$new_empr_id;
					$result = pmb_mysql_query($query);
					if(!pmb_mysql_num_rows($result)){
						if ($duplicate) {
							$this->duplicate($serialscirc[$i], $new_empr_id);
						} else {
							$query = "update serialcirc_diff set num_serialcirc_diff_empr = ".$new_empr_id." where num_serialcirc_diff_serialcirc = ".$serialscirc[$i]." and num_serialcirc_diff_empr = ".$this->empr_id;
							pmb_mysql_query($query);
							$query = "update serialcirc_group join serialcirc_diff on num_serialcirc_group_diff = id_serialcirc_diff join serialcirc on id_serialcirc = num_serialcirc_diff_serialcirc join abts_abts on abt_id = num_serialcirc_abt set num_serialcirc_group_empr = ".$new_empr_id." where num_serialcirc_diff_serialcirc = ".$serialscirc[$i]." and serialcirc_diff_empr_type=1  and num_serialcirc_group_empr = ".$this->empr_id;
							pmb_mysql_query($query);
						}
						$forwarded = true;
					}else{
						$row = pmb_mysql_fetch_object($result);
						$error_message[] = sprintf(($duplicate ? $msg['serialcirc_duplicate_already_subcribed'] : $msg['serialcirc_forward_already_subcribed']), $row->abt_name, $row->name);
					}
				}else{
					$row = pmb_mysql_fetch_object($result);
					$error_message[] = sprintf(($duplicate ? $msg['serialcirc_duplicate_already_subcribed'] : $msg['serialcirc_forward_already_subcribed']), $row->abt_name, $row->name);
				}
			}
		}
		if($forwarded){
			$query = "select empr_cb, concat(empr_nom,' ',empr_prenom) as name from empr where id_empr = ".$new_empr_id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$message[] = sprintf(($duplicate ? $msg['serialcirc_empr_duplicated'] : $msg['serialcirc_empr_forwarded']),"<a href='./circ.php?categ=pret&form_cb=".$row->empr_cb."'>".htmlentities($row->name, ENT_QUOTES, $charset)."</a>");
			}
		}
		return array('messages'=>$message,'errors'=>$error_message);
	}
	
	protected function duplicate($serialcirc, $new_empr_id) {
		$serialcirc = intval($serialcirc);
		$new_empr_id = intval($new_empr_id);
		$query = 'select * from serialcirc_diff where num_serialcirc_diff_serialcirc = '.$serialcirc.' and num_serialcirc_diff_empr = '.$this->empr_id;
		$result = pmb_mysql_query($query);
		
		while ($row = pmb_mysql_fetch_assoc($result)) {
			$query = 'select max(serialcirc_diff_order) from serialcirc_diff where num_serialcirc_diff_serialcirc = '.$serialcirc;
			$order = pmb_mysql_result(pmb_mysql_query($query), 0, 0)+1;
			$query = 'insert into serialcirc_diff set num_serialcirc_diff_empr = '.$new_empr_id.', serialcirc_diff_order = '.$order;
			foreach ($row as $column => $value) {
				if (($column == 'id_serialcirc_diff') || ($column == 'num_serialcirc_diff_empr') || ($column == 'serialcirc_diff_order')) {
					continue;
				}
				$query.= ', '.$column.' = "'.$value.'"';
			}
			pmb_mysql_query($query);
		}
		
		$query = 'select serialcirc_group.* from serialcirc_group join serialcirc_diff on num_serialcirc_group_diff = id_serialcirc_diff where num_serialcirc_diff_serialcirc = '.$serialcirc.' and num_serialcirc_group_empr = '.$this->empr_id;
		$result = pmb_mysql_query($query);
		
		while ($row = pmb_mysql_fetch_assoc($result)) {
			$query = 'select max(serialcirc_group_order) from serialcirc_group join serialcirc_diff on num_serialcirc_group_diff = id_serialcirc_diff where num_serialcirc_diff_serialcirc = '.$serialcirc;
			$order = pmb_mysql_result(pmb_mysql_query($query), 0, 0)+1;
			$query = 'insert into serialcirc_group set num_serialcirc_group_empr = '.$new_empr_id.', serialcirc_group_order = '.$order;
			foreach ($row as $column => $value) {
				if (($column == 'id_serialcirc_group') || ($column == 'num_serialcirc_group_empr') || ($column == 'serialcirc_group_order') || ($column == 'serialcirc_group_responsable')) {
					continue;
				}
				$query.= ', '.$column.' = "'.$value.'"';
			}
			pmb_mysql_query($query);
		}
	}
} // class end
