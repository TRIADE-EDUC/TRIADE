<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: listes_lecture.class.php,v 1.20 2023/12/21 11:02:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class listes_lecture {
	
	protected $type;
	
	protected $listes_lecture;
	
	/**
	 * Constructeur 
	 */
	public function __construct($type='public_reading_lists'){
		$this->type = $type;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $msg;
		
		$this->listes_lecture = array();
		$query = 'select * from opac_liste_lecture join empr on empr.id_empr = opac_liste_lecture.num_empr';
		$query .= $this->_get_query_filters();
		$query .= ' order by tag, nom_liste';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
		    $i = 0;
			while($row = pmb_mysql_fetch_object($result)) {
				if($row->tag == '') {
					$tag_label = $msg['list_lecture_tag_no_ranking'];
				} else {
					$tag_label = $row->tag;
				}
				$this->listes_lecture[$tag_label][$i] = $row;
				$notices = array();
				$notices_create_date = array();
				$query_notices = "select * from opac_liste_lecture_notices where opac_liste_lecture_num=" . $row->id_liste;
				$result_notices = pmb_mysql_query($query_notices);
				if (pmb_mysql_num_rows($result_notices)) {
				    while ($row_notices = pmb_mysql_fetch_object($result_notices)) {
				        $notices[] = $row_notices->opac_liste_lecture_notice_num;
				        $notices_create_date[$row_notices->opac_liste_lecture_notice_num] = $row_notices->opac_liste_lecture_create_date;
				    }
				}
				$this->listes_lecture[$tag_label][$i]->notices_associees = implode(',', $notices);
				$this->listes_lecture[$tag_label][$i]->notices = $notices;
				$this->listes_lecture[$tag_label][$i]->notices_create_date = $notices_create_date;
				$i++;
			}
		}
	}
		
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
	
		$filter_query = '';
		switch ($this->type) {
			case 'my_reading_lists' :
				$filter_query .= " where num_empr = '".$_SESSION['id_empr_session']."'";
				break;
			case 'shared_reading_lists' :
				$filter_query .= " join abo_liste_lecture on abo_liste_lecture.num_liste=opac_liste_lecture.id_liste and abo_liste_lecture.num_empr = '".$_SESSION['id_empr_session']."' and abo_liste_lecture.etat=2";
				break;
			case 'private_reading_lists' :
				$filter_query .= " 
					where num_empr = '".$_SESSION['id_empr_session']."'
					or id_liste in (select num_liste from abo_liste_lecture where num_empr = '".$_SESSION['id_empr_session']."' and etat=2)"; 
				break;
			case 'public_reading_lists' :
				$filter_query .= " left join abo_liste_lecture on abo_liste_lecture.num_liste=opac_liste_lecture.id_liste and abo_liste_lecture.num_empr = '".$_SESSION['id_empr_session']."'";
				$filter_query .= " where public = 1 and opac_liste_lecture.num_empr <> '".$_SESSION['id_empr_session']."'";
				break;
		}
		return $filter_query;
	}

	/**
	 * Une demande en cours
	 */
	protected function _ask_in_progress($id) {
		$query = 'select num_empr from abo_liste_lecture where etat=1 and num_liste='.$id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) return true;
		return false;
	}
	
	/**
	 * Affiche une liste de lecture à moi
	 */
	protected function get_display_my_reading_list($liste, $actions_allow = true) {
		global $msg, $charset;
		
		$display = "";
		if($liste->description) {
			$div_description = "<div id='desc".$liste->id_liste."' class='listedescription'>".$liste->description."</div>";
			$div_action = " onmouseout=\"document.getElementById('desc".$liste->id_liste."').style.visibility='hidden';\" onmouseover=\"document.getElementById('desc".$liste->id_liste."').style.visibility='visible';\"";
		} else {
			$div_description = "";
			$div_action = "";
		}
		$display .= "
			<div id='liste_".$liste->id_liste."'>";
		if($actions_allow) {
			$display .= "<input type='checkbox' class='checkbox' id='cb".$liste->id_liste."' name='list_ck[]' value='".$liste->id_liste."' title='".htmlentities($msg['list_ui_selection_checkbox'], ENT_QUOTES, $charset)."'/>";
		} else {
			$display .= "<input type='checkbox' class='checkbox' id='cb".$liste->id_liste."' name='list_ck[]' value='".$liste->id_liste."' disabled='disabled'/>";
		}
		$display .= "<span>
					<a href='./index.php?lvl=show_list&sub=view&id_liste=".$liste->id_liste."' ".$div_action.">".htmlentities($liste->nom_liste,ENT_QUOTES,$charset)."</a>
				</span>".$div_description;
		if($liste->public) {
			$display .= "
				&nbsp;<span>
					<img style='border:0px' class='align_middle' src='".get_url_icon('group.png', 1)."' title=\"".$msg['list_lecture_partagee']."\"/>
				</span>";
		}
		if($liste->read_only) {
			$display .= "
				&nbsp;<span>
					<img style='border:0px' class='align_middle' src='".get_url_icon('b_no_edit.png', 1)."' title=\"".$msg['list_lecture_readonly']."\"/>
				</span>";
		}
		if($liste->confidential) {
			$display .= "
				&nbsp;<span>
					<img style='border:0px' class='align_middle' src='".get_url_icon('lock.png', 1)."' title=\"".$msg['list_lecture_confidential']."\"/>
				</span>";
		}
		if($this->_ask_in_progress($liste->id_liste)) {
			$display .= "
				&nbsp;<span>
					<a href='./empr.php?lvl=demande_list&tab=lecture'>
						<img style='border:0px' class='align_middle' src='".get_url_icon('notification_new.png', 1)."' title=\"".$msg['list_lecture_ask_in_progess']."\"/>
					</a>			
				</span>";
		}
		$display .= "</div>";
		return $display;
	}
	
	/**
	 * Affiche la liste de mes listes de lecture
	 */
	protected function get_display_my_reading_lists() {
		global $msg;
		global $liste_lecture_prive;
		
		$my_reading_lists = "
			<form name='my_list' method='post' action='empr.php' >
				<input type='hidden' id='lvl' name='lvl' />
				<input type='hidden' id='sub' name='sub' value='my_list' />
				<input type='hidden' id='act' name='act' />";
		if(count($this->listes_lecture)) {
			$my_reading_lists.= "
					<div id='list_cadre' style='border: 1px solid rgb(204, 204, 204); overflow: auto; height: 200px;padding:2px;'>";
			foreach ($this->listes_lecture as $tag => $listes_lecture) {
				$my_reading_lists .= "<div class='row'><span class='liste_tag'>".$tag."</span></div>";
				foreach ($listes_lecture as $liste_lecture) {
					$my_reading_lists .= $this->get_display_my_reading_list($liste_lecture);
				}
			}
			$my_reading_lists .= "</div><br />";
			$my_reading_lists .= $this->get_display_buttons_reading_lists();
		} else {
			$my_reading_lists.= "
				<div class='row'>
					<label>".$msg['list_lecture_no_mylist']."</label>
				</div>";
		}
		$my_reading_lists .= "</form>";
		$display = str_replace('!!current_shared!!', '', $liste_lecture_prive);
		$display = str_replace('!!my_current!!', 'current', $display);
		$display = str_replace('!!my_list_checked!!','checked=checked', $display);
		$display = str_replace('!!listes!!', $my_reading_lists, $display);
		return $display;
	}
	
	/**
	 * Affiche une liste de lecture partagée
	 */
	protected function get_display_shared_reading_list($liste) {
		global $msg, $charset;
		
		$display = "";
		if($liste->description){
			$div_description = "<div id='desc".$liste->id_liste."' class='listedescription'>".$liste->description."</div>";
			$div_action = " onmouseout=\"document.getElementById('desc".$liste->id_liste."').style.visibility='hidden';\" onmouseover=\"document.getElementById('desc".$liste->id_liste."').style.visibility='visible';\"";
		} else {
			$div_description = "";
			$div_action = "";
		}
		$display .= "
			<div id='liste_".$liste->id_liste."'>
				<input type='checkbox' class='checkbox' id='cb".$liste->id_liste."' name='list_ck[]' value='".$liste->id_liste."' title='".htmlentities($msg['list_ui_selection_checkbox'], ENT_QUOTES, $charset)."'/>
				&nbsp;<span><a ".$div_action." href='./index.php?lvl=show_list&sub=consultation&id_liste=".$liste->id_liste."'>".htmlentities($liste->nom_liste,ENT_QUOTES,$charset)."</a><label for='cb".$liste->id_liste."' > ".htmlentities("(".$liste->empr_prenom." ".$liste->empr_nom.")",ENT_QUOTES,$charset)." </label></span>".$div_description."
			</div>
		";
		return $display;
	}
	
	/**
	 * Affiche la liste des listes de lecture partagées
	 */
	protected function get_display_shared_reading_lists() {
		global $msg;
		global $liste_lecture_prive;		
		
		$shared_reading_lists = "
			<form name='myshared_list' method='post' action='empr.php' >
				<input type='hidden' id='lvl' name='lvl' />
				<input type='hidden' id='sub' name='sub' value='shared_list' />
				<input type='hidden' id='act' name='act' />";
		if(count($this->listes_lecture)) {
			$shared_reading_lists.= "
					<div id='list_cadre' style='border: 1px solid rgb(204, 204, 204); overflow: auto; height: 200px;padding:2px;'>";
			foreach ($this->listes_lecture as $tag => $listes_lecture) {
				$shared_reading_lists .= "<span class='liste_tag'>".$tag."</span>";
				foreach ($listes_lecture as $liste_lecture) {
					$shared_reading_lists .= $this->get_display_shared_reading_list($liste_lecture);
				}
			}
			$shared_reading_lists .= "</div><br />";
			$shared_reading_lists .= $this->get_display_buttons_reading_lists();
		} else {
			$shared_reading_lists.= "
				<div class='row'>
					<label>".$msg['list_lecture_no_myshared']."</label>
				</div>";
		}		
		$shared_reading_lists.= "</form>";
		$display = str_replace('!!current_shared!!', 'current', $liste_lecture_prive);
		$display = str_replace('!!my_list_checked!!','', $display);
		$display = str_replace('!!my_current!!', '', $display);
		$display = str_replace('!!listes!!', $shared_reading_lists, $display);
		return $display;
	}
	
	/**
	 * Affiche une liste lecture publique
	 */
	protected function get_display_public_reading_list($liste) {
		global $msg, $charset;
		
		$display = "";
		
		if($liste->etat == 2) {
			$font = "<span style='color:green'>";
			$font_end = "</span>";
			$check = 'checked';
		} else {
			$font='';
			$font_end='';
			$check='';
		}
		//Ajout de script pour la gestion de la confidentialité et l'ajax
		$ajax="";
		$disable="";
		$icone="";
		$title="";
		$confidential = false;
		if($liste->confidential && !$liste->etat){
			$ajax= "onclick=\"make_mail_form('".$liste->id_liste."')\" style=\"cursor:pointer\"";
			$disable='disabled';
			$icone ="lock.png";
			$title =$msg['list_lecture_confidential'];
			$confidential = true;
		} elseif($liste->confidential && $liste->etat==1){
			$ajax= "onclick=\"demandeEnCours();\"";
			$disable='disabled';
			$icone ="hourglass.png";
			$title = $msg['list_lecture_encours_demande'];
			$confidential = true;
		} elseif($liste->confidential){
			$icone ="lock_open.png";
			$title = $msg['list_lecture_accessible'];
		}
		if($liste->description){
			$div_description = "<div id='desc".$liste->id_liste."' class='listedescription'>".$liste->description."</div>";
			$div_action = " onmouseout=\"document.getElementById('desc".$liste->id_liste."').style.visibility='hidden';\" onmouseover=\"document.getElementById('desc".$liste->id_liste."').style.visibility='visible';\"";
		} else {
			$div_description = "";
			$div_action = "";
		}
		$display .= "<div id='liste_".$liste->id_liste."' ".$ajax.">
			<input type='checkbox' class='checkbox' id='cb".$liste->id_liste."' name='list_ck[]' value='".$liste->id_liste."' ".$check." ".$disable." title='".htmlentities($msg['list_ui_selection_checkbox'], ENT_QUOTES, $charset)."' />";
		if($confidential) {
			$display .= "&nbsp;<span><a ".$div_action." href='#' onclick='return false;'>".htmlentities($liste->nom_liste,ENT_QUOTES,$charset)."</a>$font<label for='cb".$liste->id_liste."' > ".htmlentities("(".$liste->empr_prenom." ".$liste->empr_nom.")",ENT_QUOTES,$charset)." </label>".$font_end."</span>".$div_description;
		} else {
			$display .=	"&nbsp;<span><a ".$div_action." href='./index.php?lvl=show_list&sub=consultation&id_liste=".$liste->id_liste."'>".htmlentities($liste->nom_liste,ENT_QUOTES,$charset)."</a>".$font."<label for='cb".$liste->id_liste."' > ".htmlentities("(".$liste->empr_prenom." ".$liste->empr_nom.")",ENT_QUOTES,$charset)." </label>".$font_end."</span>".$div_description;
		}	
		if($icone) $display .= "<span><img style='border:0px' class='align_top' src='".get_url_icon($icone, 1)."' title=\"".$title."\" id='img_confi_".$liste->id_liste."' /></span>";
		if($liste->read_only){
			$display .= "&nbsp;<span><img style='border:0px' class='align_top' src='".get_url_icon('b_no_edit.png', 1)."' title=\"".$msg['list_lecture_readonly']."\" id='img_ro_".$liste->id_liste."' /></span>";
		}
		$display .= "</div>";
		$display .= "<div id='maillist_".$liste->id_liste."'></div>";
		return $display;
	}
	
	/**
	 * Affiche la liste des listes de lecture privées (mes listes + listes partagées)
	 */
	protected function get_display_private_reading_lists() {
		global $msg;
		global $liste_lecture_prive;
		
		$private_reading_lists = "
			<form name='myshared_list' method='post' action='empr.php' >
				<input type='hidden' id='lvl' name='lvl' />
				<input type='hidden' id='act' name='act' />";
		if(count($this->listes_lecture)) {
			$private_reading_lists.= "				
					<div id='list_cadre' style='border: 1px solid rgb(204, 204, 204); overflow: auto; height: 200px;padding:2px;'>";
			foreach ($this->listes_lecture as $tag => $listes_lecture) {
				$private_reading_lists .= "<span class='liste_tag'>".$tag."</span>";
				foreach ($listes_lecture as $liste_lecture) {
					if($liste_lecture->num_empr == $_SESSION['id_empr_session']) {
						$private_reading_lists .= $this->get_display_my_reading_list($liste_lecture);
					} else {
						$private_reading_lists .= $this->get_display_shared_reading_list($liste_lecture);
					}
				}
			}
			$private_reading_lists .= "</div><br />";
			$private_reading_lists .= $this->get_display_buttons_reading_lists();
		} else {
			$private_reading_lists.= "
				<div class='row'>
					<label>".$msg['list_lecture_no_myshared']."</label>
				</div>";
		}
		$private_reading_lists.= "</form>";
		$display = str_replace('!!current_shared!!', 'current', $liste_lecture_prive);
		$display = str_replace('!!my_list_checked!!','', $display);
		$display = str_replace('!!my_current!!', '', $display);
		$display = str_replace('!!listes!!', $private_reading_lists, $display);
		return $display;
	}
	
	/**
	 * Affiche la liste des listes de lecture publiques
	 */
	protected function get_display_public_reading_lists() {
		global $msg;
		global $liste_lecture_public;
		
		$public_reading_lists = '';
		if(count($this->listes_lecture)) {
			foreach ($this->listes_lecture as $tag=>$listes_lecture) {
				$public_reading_lists .= "<span class='liste_tag'>".$tag."</span>";
				foreach ($listes_lecture as $liste_lecture) {
					$public_reading_lists .= $this->get_display_public_reading_list($liste_lecture);
				}
			}
			$public_reading_lists .= $this->get_display_buttons_reading_lists();
		} else {
			$public_reading_lists = "
				<div class='row'>
					<label>".$msg['list_lecture_no_publiclist']."</label>
				</div>";
		}
		$display = str_replace('!!public_list!!', $public_reading_lists, $liste_lecture_public);
		return $display;
	}
	
	/**
	 * Affiche les boutons
	 */
	protected function get_display_buttons_reading_lists() {
		global $msg;
		$display = "";
		switch ($this->type) {
			case 'my_reading_lists' :
				$display .= "
					<div class='row'>
						<input type='submit' class='bouton' id='share_btn' name='share_btn' value=\"".$msg['list_lecture_share']."\" onclick='this.form.lvl.value=\"private_list\"; this.form.act.value=\"share_list\";'/>
						<input type='submit' class='bouton' id='unshare_btn' name='unshare_btn' value=\"".$msg['list_lecture_unshare']."\" onclick='this.form.lvl.value=\"private_list\"; this.form.act.value=\"unshare_list\";'/>
						<input type='submit' class='bouton' id='suppr_btn' name='suppr_btn' value=\"".$msg['list_lecture_suppr']."\" onclick='this.form.lvl.value=\"private_list\"; if(confirm_delete())this.form.act.value=\"suppr_list\";'/>
					</div>";
				break;
			case 'shared_reading_lists' :
			case 'private_reading_lists' :
				$display .= "
					<div class='row'>
						<input type='submit' class='bouton' id='unshare_btn' name='unshare_btn' value=\"".$msg['list_lecture_quit_acces']."\" onclick='this.form.lvl.value=\"private_list\"; this.form.act.value=\"suppr_acces\";'/>
                    </div>";
				break;
			case 'public_reading_lists' :
				$display .= "
					<div class='row'>
						<input type='submit' class='bouton' id='acces_btn' name='acces_btn' value=\"".$msg['list_lecture_acces']."\" onclick='this.form.lvl.value=\"public_list\"; this.form.act.value=\"get_acces\";' />
						<input type='submit' class='bouton' id='no_acces_btn' name='no_acces_btn' value=\"".$msg['list_lecture_quit_acces']."\" onclick='this.form.lvl.value=\"public_list\"; this.form.act.value=\"suppr_acces\";' />
					</div>";
				break;
		}
		return $display;
	}
	
	/**
	 * Affiche la liste des listes de lecture
	 */
	public function get_display_list() {
		$display = "";
		switch ($this->type) {
			case 'my_reading_lists' :
				$display .= $this->get_display_my_reading_lists();
				break;
			case 'shared_reading_lists' :
				$display .= $this->get_display_shared_reading_lists();
				break;
			case 'private_reading_lists' :
				$display .= $this->get_display_private_reading_lists();
				break;
			case 'public_reading_lists' :
				$display .= $this->get_display_public_reading_lists();
				break;
		}
		return $display;
	}
	
	public function get_type() {
		return $this->type;
	}
	
	public function get_listes_lecture() {
		return $this->listes_lecture;
	}
}
?>