<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_display.class.php,v 1.45.8.1 2025/02/07 13:49:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/notice.class.php");
require_once($class_path."/record_display.class.php");
require_once($class_path."/acces.class.php");
require_once ($class_path."/frbr/frbr_build.class.php");

/**
 * class notice_display
 * Controler Générique d'une page de notice
 */
class notice_display {

	protected $id;

	protected $acces_v;

	protected $dom;

	protected $hash;

	protected $datetime;

	/**
	 * Constructeur
	 * @param int $id Identifiant de la notice
	 */
	public function __construct($id) {
		$this->id = intval($id);
		$datetime = new DateTime();
		$this->datetime = $datetime->getTimestamp();
		$this->hash = $this->generate_hash();
	}

	public function proceed($entity_type,$context=array()){
		global $charset;

		$frbr_build = frbr_build::get_instance($this->id, $entity_type);
		$this->calculate_restrict_access_rights();
		if($frbr_build->has_page() && $frbr_build->has_cadres() && $this->acces_v) {
		    $display_graph = false;
		    //récupération des données des jeux de données
		    $datanodes_data = $frbr_build->get_datanodes_data();
		    $this->dom = new DOMDocument();
		    $this->dom->encoding = $charset;
		    $old_errors_value = false;
		    if(libxml_use_internal_errors(true)){
		        $old_errors_value = true;
		    }
		    $html = $this->get_details_display();
		    if($charset == "utf-8"){
		        $this->dom->loadHTML("<?xml version='1.0' encoding='$charset'>".$html);
		    }else{
		        $this->dom->loadHTML($html);
		    }
		    if (!$this->dom->getElementById('noticeNot')) {
		        $this->dom = $this->setAllId($this->dom);
		    }
		    if ($this->dom->getElementById("noticeNot") == null) {
		        print $html;
		        $this->add_tab_result_read();
		        return;
		    }
		    $ref_node = $this->dom->getElementById("noticeNot")->parentNode;
		    foreach ($frbr_build->get_cadres() as $cadre) {
		        if ($cadre['place_visibility']) {
		            if($cadre['cadre_type']) {
		                switch ($cadre['cadre_type']) {
		                    case 'isbd':
		                        $ref_node->appendChild($this->dom->importNode($this->dom->getElementById("noticeNot"),true));
		                        break;
		                    case 'frbr_graph' :
		                        $graph_node = $this->dom->createElement("div");
		                        $graph_node->setAttribute('id', 'frbr_entity_graph');
		                        $ref_node->appendChild($graph_node);
		                        break;
		                }
		            } else {
		                $view_instance = new $cadre['cadre_object']($cadre['id']);
		                $html = $view_instance->show_cadre($datanodes_data);
		                $tmp_dom = new domDocument();
		                if($charset == "utf-8"){
		                    @$tmp_dom->loadHTML("<?xml version='1.0' encoding='$charset'>".$html);
		                }else{
		                    @$tmp_dom->loadHTML($html);
		                }
		                if (!$tmp_dom->getElementById($view_instance->get_dom_id())) {
		                    $tmp_dom = $this->setAllId($tmp_dom);
		                }
		                $ref_node->appendChild($this->dom->importNode($tmp_dom->getElementById($view_instance->get_dom_id()),true));
		            }
		        }
		        if ($cadre['cadre_visible_in_graph']) {
		            $display_graph = true;
		        }
		    }

		    if(!$frbr_build->get_page()->get_parameter_value('isbd')) {
		        $ref_node->removeChild($this->dom->getElementById('noticeNot'));
		    }
		    //frbr_graph
		    if ($this->dom->getElementById("frbr_entity_graph")) {
		        if ($display_graph) {
		            $this->build_graph();
		        } else {
		            $this->dom->getElementById("frbr_entity_graph")->parentNode->removeChild($this->dom->getElementById("frbr_entity_graph"));
		        }
		    }

		    print $this->dom->saveHTML();
		    libxml_use_internal_errors($old_errors_value);
		} else {
			print $this->get_details_display();
		}
		$this->add_tab_result_read();
	}

	protected function is_authorized_in_view() {
		if(!empty($_SESSION["opac_view"]) && !empty($_SESSION["opac_view_query"])){
			$query = "select opac_view_num_notice from opac_view_notices_".$_SESSION["opac_view"]." where opac_view_num_notice =".$this->id;
			$result = pmb_mysql_query($query);
			if(!pmb_mysql_num_rows($result)) {
				$this->acces_v = FALSE;
			}
		}
	}

	protected function calculate_restrict_access_rights() {
		global $gestion_acces_active, $gestion_acces_empr_notice;
		global $opac_opac_view_activate;

		$this->acces_v=TRUE;
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			$ac= new acces();
			$dom_2= $ac->setDomain(2);
			$this->acces_v = $dom_2->getRights($_SESSION['id_empr_session'],$this->id,4);
		} else {
			$query = "SELECT notice_visible_opac, expl_visible_opac, notice_visible_opac_abon, expl_visible_opac_abon, explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='".$this->id."' and id_notice_statut=statut ";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$statut_temp = pmb_mysql_fetch_object($result);
				if(!$statut_temp->notice_visible_opac)	$this->acces_v=FALSE;
				if($statut_temp->notice_visible_opac_abon && !$_SESSION['id_empr_session'])	$this->acces_v=FALSE;
			} else 	$this->acces_v=FALSE;
		}
		//Visible - Mais est-elle visible dans la vue OPAC ?
		if($this->acces_v && $opac_opac_view_activate) {
			$this->is_authorized_in_view();
		}
	}

	protected function get_details_explnum_display() {
		global $msg, $charset;
		global $mode_phototeque, $opac_photo_mean_size_x, $opac_photo_show_form;
		global $opac_visionneuse_allow, $sendToVisionneuseNoticeDisplay;

		$display = '';
		if (isset($mode_phototeque) && $mode_phototeque) {
			// Traitement exemplaire numerique
			$query = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_data, explnum_vignette, explnum_nomfichier, explnum_extfichier FROM explnum WHERE ";
			$query .= "explnum_notice='".$this->id."' ";
			$query .= " order by explnum_id LIMIT 1";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$explnumobj=pmb_mysql_fetch_object($result);
				if ($explnumobj->explnum_url) {
					$display .= "<img width='$opac_photo_mean_size_x' src=\"".$explnumobj->explnum_url."\"/><br />";
				} else{
					//répertoire d'upload ou stockage en base, le traitement reste identique...
					$display .= "<img src=\"vign_middle.php?explnum_id=".$explnumobj->explnum_id."\"/><br />";
				}
				if ($opac_photo_show_form) {
					$display .= "<a href='index.php?lvl=doc_command&id=".$this->id."&mode_phototeque=1'>".htmlentities($msg["command_phototeque_command_command"],ENT_QUOTES,$charset)."</a>";
				}
			}
			$hide_explnum=1;
		} else {
			$query = "select explnum_id from explnum join bulletins on explnum_bulletin=bulletin_id where num_notice=".$this->id." union select explnum_id from explnum where explnum_notice=".$this->id;
			$result = pmb_mysql_query($query);
			if($opac_visionneuse_allow && pmb_mysql_num_rows($result)){
				//print "&nbsp;&nbsp;&nbsp;".$link_to_visionneuse;
				$display .= $sendToVisionneuseNoticeDisplay;
			}
		}
		return $display;
	}

	protected function get_details_display() {
		global $msg, $charset;
		global $pmb_logs_activate;
		global $opac_notices_depliable;

		$display = '';
		if(!isset($this->acces_v)) {
			$this->calculate_restrict_access_rights();
		}

		if($this->id && $this->acces_v) {
			if($pmb_logs_activate) notice::recup_notice_infos($this->id);
			$query = "SELECT notice_id, niveau_biblio,typdoc,(opac_visible_bulletinage&0x1) as opac_visible_bulletinage FROM notices WHERE notice_id='".$this->id."' LIMIT 1";
			$result = pmb_mysql_query($query);
			while ($obj=pmb_mysql_fetch_object($result)) {
				$display .= $this->get_details_explnum_display();
				$opac_notices_depliable = 0;
				switch($obj->niveau_biblio) {
					case "s":
						$display .= aff_notice($this->id);
						if(!$obj->opac_visible_bulletinage) {
							break;
						}
						$display .= record_display::get_display_bulletins_list($this->id);
						break;
					case "a":
						$display .= aff_notice($this->id);
						break;
					case "m":
					default :
						$display .= "<br />".aff_notice($this->id);
						break;
				}
			}
		} else {
			$display .= "<h3>".htmlentities($msg['record_display_forbidden'],ENT_QUOTES,$charset).'</h3>';
		}
		return $display;
	}

	public function get_facetteslist() {
		global $nbr_lignes;

		if(!isset($this->acces_j)) {
			$this->calculate_restrict_access_rights();
		}
		$facettes_tpl = '';
		//comparateur de facettes : on ré-initialise
		$_SESSION['facette']=array();
		if($nbr_lignes){
			$query = "SELECT distinct notices.notice_id FROM notices ".$this->get_join_recordslist()." ".$this->acces_j." ".$this->statut_j;
			$query .= " WHERE ".$this->get_clause_authority_id_recordslist()." $this->statut_r ";
			$facettes_tpl .= facettes::get_display_list_from_query($query);
		}
		return $facettes_tpl;
	}

	protected function build_graph(){
	    global $include_path, $charset;
		$html = '';

		$record = new record_datas($this->id);
		$frbr_entity_graph = frbr_entity_graph::get_entity_graph($record, 'record');
		$frbr_entity_graph->get_entities_graphed(true);
		$content = $frbr_entity_graph->get_json_entities_graphed();

		$entities_graphed = $frbr_entity_graph->get_entities_graphed();

		if (count($entities_graphed['links'])) {
			$template_path = $include_path.'/templates/frbr_entities_graph.tpl.html';
			if(file_exists($include_path.'/templates/frbr_entities_graph_subst.tpl.html')){
				$template_path = $include_path.'/templates/frbr_entities_graph_subst.tpl.html';
			}
			if(file_exists($template_path)){
				$h2o = H2o_collection::get_instance($template_path);

				$graph = array('nodes'=> $content['nodes'], 'links' => $content['links']);
				$html = $h2o->render(array('graph' => $graph));

				$tmp_dom = new domDocument();
				if($charset == "utf-8"){
					@$tmp_dom->loadHTML("<?xml version='1.0' encoding='$charset'>".$html);
				}else{
					@$tmp_dom->loadHTML($html);
				}
				if (!$tmp_dom->getElementById('entity_graph')) {
					$tmp_dom = $this->setAllId($tmp_dom);
				}
				$this->dom->getElementById("frbr_entity_graph")->appendChild($this->dom->importNode($tmp_dom->getElementById('entity_graph'),true));
			}
		}

	}

	public function setAllId($DOMNode){
		if($DOMNode->hasChildNodes()){
			for ($i=0; $i<$DOMNode->childNodes->length;$i++) {
				$this->setAllId($DOMNode->childNodes->item($i));
			}
		}
		if($DOMNode->hasAttributes()){
			$id=$DOMNode->getAttribute("id");
			if($id){
				$DOMNode->setIdAttribute("id",true);
			}
		}
		return $DOMNode;
	}

	private function add_tab_result_read() {
	    global $opac_notice_affichage_class, $pmb_logs_activate;
	    if ($this->id && $this->datetime && $this->hash) {
	        if ($opac_notice_affichage_class::check_token($this->id, $this->datetime, $this->hash)) {
	            add_value_session('tab_result_read', $this->id);
	            if($pmb_logs_activate) {
	                global $infos_notice, $infos_expl;
	                $infos_notice = $opac_notice_affichage_class::get_infos_notice($this->id);
	                $infos_expl = $opac_notice_affichage_class::get_infos_expl($this->id);
	                generate_log('notice_display', [], true);
	            }
	        }
	    }
	}

	public function generate_hash() {
	    $hash = "";
	    $query = "select notice_id, create_date from notices where notice_id=".$this->id;
	    $result = pmb_mysql_query($query);
	    if ($result) {
	        if (pmb_mysql_num_rows($result) == 1) {
	            $row = pmb_mysql_fetch_object($result);
	            if(isset($_SERVER["HTTP_REFERER"])) {
	               $short_request_uri = substr($_SERVER["HTTP_REFERER"], strrpos($_SERVER["HTTP_REFERER"], "/")+1);
	            } else {
	                $short_request_uri = '';
	            }
	            $hash = md5($row->notice_id."_".$this->datetime."_".$row->create_date."_".$short_request_uri);
	        }
	    }
	    return $hash;
	}
}