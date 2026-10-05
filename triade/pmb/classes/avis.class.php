<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis.class.php,v 1.9.2.1.2.1 2025/03/13 16:27:12 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/mono_display.class.php");
require_once($class_path."/serial_display.class.php");
require_once($include_path."/templates/avis.tpl.php");
require_once ($include_path."/interpreter/bbcode.inc.php");

define('AVIS_RECORDS',1);
define('AVIS_ARTICLES',2);
define('AVIS_SECTIONS',3);

class avis {

	/**
	 * Identifiant de l'objet
	 */
	protected $object_id;

	/**
	 * Type de l'object
	 */
	protected $object_type;

	/**
	 * Chaîne de caractère du type de l'objet
	 * @var string
	 */
	protected $object_string_type;

	protected $nbr_lignes;

	public function __construct($object_id = 0) {
		$this->object_id = intval($object_id);
		if(!isset($this->object_type)) $this->object_type = 0;
		$this->set_object_string_type($this->object_type);
	}

	public function get_object_string_type() {
		return $this->object_string_type;
	}

	public function set_object_string_type($type) {
		$this->object_string_type = '';
		switch ($type) {
			case AVIS_RECORDS :
				$this->object_string_type = 'notice';
				break;
			case AVIS_ARTICLES :
				$this->object_string_type = 'article';
				break;
			case AVIS_SECTIONS :
				$this->object_string_type = 'section';
				break;
			default:
				$this->object_string_type = '';
				break;
		}
	}

	protected function get_filters_display() {
		global $msg;
		global $montrerquoi;

		$display = "<div class='row'><span class='usercheckbox'><input type='radio' name='montrerquoi' value='novalid' id='novalid' onclick='this.form.submit();' ";
		if ($montrerquoi=='novalid') $display .= "checked" ;
		$display .= " /><label for='novalid'>".$msg['avis_show_novalid']."</label></span>&nbsp;<span class='usercheckbox'><input type='radio' name='montrerquoi' value='valid' id='valid' onclick='this.form.submit();' ";
		if ($montrerquoi=='valid') $display .= "checked" ;
		$display .= " /><label for='valid'>".$msg['avis_show_valid']."</label></span>&nbsp;<span class='usercheckbox'><input type='radio' name='montrerquoi' value='all' id='all' onclick='this.form.submit();' ";
		if ($montrerquoi=='all') $display .= "checked" ;
		$display .= " /><label for='all'>".$msg['avis_show_all']."</label></span></div>";
		return $display;
	}

	protected function _get_select_query() {
	}

	protected function _get_join_query() {
	}

	protected function _get_restrict_query() {
		global $montrerquoi;
		$restrict = "";
		switch ($montrerquoi) {
			case 'all':
				$restrict = " 1 " ;
				break;
			case 'valid' :
				$restrict = " valide='1' " ;
				break;
			default:
			case 'novalid' :
				$restrict = " valide='0' " ;
				break;
		}
		if($this->object_id) {
			$restrict .= " and num_notice = ".$this->object_id." " ;
		}
		return $restrict;
	}

	protected function _get_sort_query() {
	}

	public function get_query() {
		global $msg;
		global $debut;
		global $nb_per_page;

		$query = "select avis.note, avis.sujet, avis.commentaire, avis.id_avis, DATE_FORMAT(avis.dateAjout,'".$msg['format_date']."') as ladate, ";
		$query .= "empr_login, empr_nom, empr_prenom, valide ";
		$query .= $this->_get_select_query();
		$query .= "from avis ";
		$query .= "left join empr on empr.id_empr=avis.num_empr ";
		$query .= $this->_get_join_query();
		$query .= "where ".$this->_get_restrict_query()." ";
		$query .= "and avis.type_object = ".$this->object_type." and avis_private = 0 ";
		if(!$this->nbr_lignes) {
			$result = pmb_mysql_query($query);
			$this->nbr_lignes = pmb_mysql_num_rows($result);
		}
		$query .= $this->_get_sort_query();
		$query .= "limit $debut, $nb_per_page";
		return $query;
	}

	public static function get_display_note($avis) {
	    global $msg;
	    global $pmb_avis_note_display_mode;

	    if($pmb_avis_note_display_mode){
	        if($pmb_avis_note_display_mode!=1){
	            $categ_avis=$msg['avis_detail_note_'.$avis->note];
	        } else {
	            $categ_avis='';
	        }
	        if($pmb_avis_note_display_mode!=2){
	            $etoiles="";$cpt_star = 4;
	            for ($i = 1; $i <= $avis->note; $i++) {
	                $etoiles.="<img border=0 src='".get_url_icon('star.png')."' align='absmiddle' />";
	            }
	            for ( $j = round($avis->note);$j <= $cpt_star ; $j++) {
	                $etoiles .= "<img border=0 src='".get_url_icon('star_unlight.png')."' align='absmiddle' />";
	            }
	        }
	        if($pmb_avis_note_display_mode==3 || $pmb_avis_note_display_mode==5) {
	            return $etoiles."<br />".$categ_avis;
	        } else if($pmb_avis_note_display_mode==4) {
	            return $etoiles;
	        } else {
	            return $etoiles.$categ_avis;
	        }
	    }
	    return '';
	}

	public static function get_display_text($avis) {
	    global $charset;

	    $display = '';
	    $note = static::get_display_note($avis);
	    if (!$avis->valide) {
	        $display .=  "<span style='color:#CC0000'>$note<b>".htmlentities($avis->sujet,ENT_QUOTES,$charset)."</b></span>";
	    } else {
            $display .=  "<span style='color:#00BB00'>$note<b>".htmlentities($avis->sujet,ENT_QUOTES,$charset)."</b></span>";
	    }
	    if($charset != "utf-8") {
	        $avis->commentaire=cp1252Toiso88591($avis->commentaire);
	    }
        $display .=  ", ".htmlentities($avis->ladate,ENT_QUOTES,$charset)." ".htmlentities($avis->empr_prenom." ".$avis->empr_nom,ENT_QUOTES,$charset);
        return $display;
	}

	public static function get_display_review($avis) {
		$display =  "
		<div class='left'>
		<input type='checkbox' name='valid_id_avis[]' id='valid_id_avis[]' value='$avis->id_avis' onClick=\"stop_evenement(event);\"/>" ;

		$display .= static::get_display_text($avis);
		$display .=  "
		</div>
		<div class='row'>
		".do_bbcode($avis->commentaire)."
		</div>";
		return $display;
	}

	protected function pagination() {
		global $montrerquoi, $nb_per_page, $page;

		$pagination = '';
		if($this->nbr_lignes) {
			$url_base = "./catalog.php?categ=avis&montrerquoi=$montrerquoi";
			$pagination = aff_pagination ($url_base, $this->nbr_lignes, $nb_per_page, $page, 10, false, true);
		}
		return $pagination;
	}

	public function get_display_list_form() {
		global $msg;
		global $current_module;

		$form = "
            <script type='text/javascript'>
                pmb_include('./javascript/bbcode.js');
            </script>
			<form class='form-".$current_module."' method='post' id='validation_avis' name='validation_avis' >
					<h3>".$msg['avis_titre_form']."</h3>
					<div class='form-contenu'>";
		$form .= $this->get_filters_display();
		if (method_exists($this, 'get_display_list')) {
			$form .= $this->get_display_list();
		}
		$form .= $this->pagination();
		$form .= "
		</div>
		<div class='row'>
			<div class='left'>
				<input type='hidden' name='quoifaire' value='' />
				<input type='button' class='bouton' name='selectionner' value='".$msg['avis_bt_selectionner']."' onClick=\"setCheckboxes('validation_avis', 'valid_id_avis', true); return false;\" />&nbsp;
				<input type='button' class='bouton' name='deselectionner' value='".$msg['avis_bt_deselectionner']."' onClick=\"setCheckboxes('validation_avis', 'valid_id_avis', false); return false;\" />&nbsp;
				<input type='button' class='bouton' name='valider' value='".$msg['avis_bt_valider']."' onclick='this.form.quoifaire.value=\"valider\"; this.form.submit()' />&nbsp;
				<input type='button' class='bouton' name='invalider' value='".$msg['avis_bt_invalider']."' onclick='this.form.quoifaire.value=\"invalider\"; this.form.submit()' />&nbsp;
			</div>
			<div class='right'>
				<input type='button' class='bouton' name='supprimer' value='".$msg['avis_bt_supprimer']."' onclick='this.form.quoifaire.value=\"supprimer\"; this.form.submit()' />&nbsp;
			</div>
		</div>
		<div class='row'></div>
		</form>";
		return $form;
	}

	public static function validate($id) {
		$id = intval($id);
		$query = "update avis set valide=1 where id_avis='".$id."' ";
		pmb_mysql_query($query);
	}

	public static function unvalidate($id) {
		$id = intval($id);
		$query = "update avis set valide=0 where id_avis='".$id."' ";
		pmb_mysql_query($query);
	}

	public static function delete($id) {
		$id = intval($id);
		$query = "delete from avis where id_avis='".$id."' ";
		pmb_mysql_query($query);
	}

	public static function delete_from_object($id) {
	}

	public function get_data() {
		global $msg, $charset;

		$memo_avis = array();
		$query = "SELECT id_avis,note,sujet,commentaire,DATE_FORMAT(dateajout,'".$msg['format_date']."') as ladate,empr_login,empr_nom, empr_prenom, valide
			from avis left join empr on id_empr=num_empr
			where num_notice='".$this->object_id."' and type_object='".$this->object_type."' and valide=1 order by avis_rank, dateajout desc";
		$result = pmb_mysql_query($query);
		if ($result) {
			while ($avis = pmb_mysql_fetch_object($result)) {
				$avis->note_textuelle = $msg['avis_detail_note_'.$avis->note];
				if($charset != "utf-8") $avis->commentaire=cp1252Toiso88591($avis->commentaire);
 				$avis->commentaire = do_bbcode($avis->commentaire);
				$memo_avis[]=$avis;
			}
		}
		return $memo_avis;
	}

	public function get_notes_avg() {
		$query = "SELECT avg(note) as moyenne, count(*) as combien FROM avis WHERE avis_private = 0 and num_notice = '".$this->object_id."' and type_object = '".$this->object_type."'";
		$result = pmb_mysql_query($query);
		$row=pmb_mysql_fetch_object($result);
		return $row->combien."|".$row->moyenne;
	}
}