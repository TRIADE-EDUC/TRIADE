<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_analysis_controller.class.php,v 1.8 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/entities/entities_records_controller.class.php");

class entities_analysis_controller extends entities_records_controller {
		
	protected $url_base = './catalog.php?categ=serials&sub=analysis';
	
	protected $bulletin_id = 0;
	
	protected $serial_id = 0;
	
	protected $model_class_name = 'analysis';
	
	public function get_object_instance() {
		$model_class_name = $this->get_model_class_name();
		$object_instance = new $model_class_name($this->id, $this->bulletin_id);
		if(method_exists($model_class_name, 'set_controller')) {
			$model_class_name::set_controller($this);
		}
		return $object_instance;
	}
	
	/**
	 * 8 = droits de modification
	 */
	protected function get_acces_m() {
		global $PMBuserid;
		$acces_m=1;
		$acces_j = $this->dom_1->getJoin($PMBuserid, 8, 'bulletin_notice');
		$q = "select count(1) from bulletins $acces_j where bulletin_id=".$this->bulletin_id;
		$r = pmb_mysql_query($q);
		if(pmb_mysql_result($r,0,0)==0) {
			$acces_m=0;
			if (!$this->analysis_id) {
				$this->error_message = 'mod_bull_error';
			} else {
				$this->error_message = 'mod_depo_error';
			}
		}
		return $acces_m;
	}
	
	protected function get_page_title($duplicate=false) {
		global $msg, $serial_header;
		
		if(!$this->id) {
			// pas d'id, c'est une création
			return str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4022], $serial_header);
		} else {
			if($duplicate) {
				return str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg['analysis_duplicate'], $serial_header);
			} else {
				return str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4023], $serial_header);
			}
		}
	}
	
	public function proceed_form() {
		print $this->get_page_title();
		$myAnalysis = $this->get_object_instance();
		print "<div class='row'><div class='perio-barre'>".$this->get_link_parent()."<h3>".$myAnalysis->tit1."</h3></div></div><br />";
		
		print "<div class='row'>".$myAnalysis->analysis_form()."</div>";
	}
	
	public function proceed_duplicate() {
		print $this->get_page_title(true);
		$myAnalysis = $this->get_object_instance();
		$myAnalysis->id = 0;
		$myAnalysis->duplicate_from_id = $this->id;
		print "<div class='row'><div class='perio-barre'>".$this->get_link_parent()."<h3>".$myAnalysis->tit1."</h3></div></div><br />";
		
		print "<div class='row'>".$myAnalysis->analysis_form()."</div>";
	}
	
	public function proceed_update() {

	}
	
	public function proceed_delete() {
		global $msg;
		global $pmb_archive_warehouse;
		
		$myAnalysis = $this->get_object_instance();
		if ($pmb_archive_warehouse) {
			analysis::save_to_agnostic_warehouse(array(0=>$this->analysis_id),$pmb_archive_warehouse);
		}
		$result = $myAnalysis->analysis_delete();
		if($result) {
			print "<div class=\"row\"><div class=\"msg-perio\" size=\"+2\">".$msg['catalog_notices_suppression']."</div></div>";
			print $this->get_redirection_form();
		} else {
				error_message(	$msg['catalog_serie_supp_depouill'] ,
						$msg['catalog_serie_supp_depouill_imp'],
						1,
						$this->get_permalink()."&serial_id=".$this->serial_id);
		}
	}
	
	public function proceed_move() {
		global $msg;
		global $to_bul;
		
		$myAnalysis = $this->get_object_instance();
		if(!$to_bul) {
			// affichage d'un form pour déplacer un article de périodique
			echo str_replace('!!page_title!!', $msg['4000'].$msg['1003'].$msg['analysis_move'], $serial_header);
			print "<div class='row'><div class='perio-barre'>".$this->get_link_parent()."<h3>".$myAnalysis->tit1."</h3></div></div><br />";
			print "<div class='row'>".$myAnalysis->move_form()."</div>";
		} else {
			$myAnalysis->move($to_bul);
			//Redirection
			print "<script type=\"text/javascript\">document.location='".$this->get_permalink($to_bul)."'</script>";
		}
	}
	
	protected function get_permalink($id=0) {
		if(!$id) $id = $this->bulletin_id;
		return $this->url_base."&sub=bulletinage&action=view&bul_id=".$id;
	}
	
	protected function get_link_parent() {
		global $msg;
		
		$myBul = new bulletinage($this->bulletin_id);
		// lien vers la notice chapeau
		$link_parent = "<a href=\"".$this->url_base."\">";
		$link_parent .= $msg[4010]."</a>";
		$link_parent .= "<img src='".get_url_icon('d.gif')."' class='align_middle' hspace=\"5\">";
		$link_parent .= "<a href=\"".$this->url_base."&sub=view&serial_id=";
		$link_parent .= $myBul->bulletin_notice."\">".$myBul->get_serial()->tit1.'</a>';
		$link_parent .= "<img src='".get_url_icon('d.gif')."' class='align_middle' hspace=\"5\">";
		$link_parent .= "<a href=\"".$this->get_permalink()."\">";
		if ($myBul->bulletin_numero) $link_parent .= $myBul->bulletin_numero." ";
		if ($myBul->mention_date) $link_parent .= " (".$myBul->mention_date.") ";
		$link_parent .= "[".$myBul->aff_date_date."]";
		$link_parent .= "</a> <img src='".get_url_icon('d.gif')."' class='align_middle' hspace=\"5\">";
		return $link_parent;
	}
	
	public function set_serial_id($serial_id=0) {
	    $this->serial_id = (int) $serial_id;
	}
	
	public function set_bulletin_id($bulletin_id=0) {
	    $this->bulletin_id = (int) $bulletin_id;
	}
}
