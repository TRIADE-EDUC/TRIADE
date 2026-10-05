<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis.class.php,v 1.36.4.1 2025/03/26 13:29:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/avis.tpl.php");
require_once($class_path."/liste_lecture.class.php");

define('AVIS_RECORDS',1);
define('AVIS_ARTICLES',2);
define('AVIS_SECTIONS',3);

class avis {

	/**
	 * Identifiant de l'objet
	 * @var integer
	 */
	protected $object_id;

	/**
	 * Type de l'objet
	 */
	protected $object_type;

	/**
	 * Moyenne des notes publiques et privées
	 * @var float
	 */
	protected $average = 0.00;

	/**
	 * Moyenne des notes publiques
	 * @var float
	 */
	protected $public_average = 0.00;

	/**
	 * Nombre d'avis
	 * @var integer
	 */
	protected $number = 0;

	/**
	 * Liste des avis
	 * @var array
	 */
	protected $avis;

	protected $nb_by_note = array();

	/**
	 * Chaîne de caractère du type de l'objet
	 * @var string
	 */
	protected $object_string_type;

	public function __construct($object_id, $object_type=AVIS_RECORDS) {
		$this->object_id = intval($object_id);
		$this->object_type = $object_type;
		$this->set_object_string_type($object_type);
		$this->fetch_data();
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
		}
	}

	protected function fetch_data() {
		global $msg;

		$this->avis = array();
		$query = "select avg(note) as average, count(id_avis) as number from avis where valide = 1";
		if(isset($_SESSION['id_empr_session']) && $_SESSION['id_empr_session']) {
			$query .= "
				and (
					avis_private = 0
					or (avis_private = 1 and num_empr='".$_SESSION['id_empr_session']."')
					or (avis_private = 1 and avis_num_liste_lecture <> 0
							and (
								avis_num_liste_lecture in (
									select num_liste from abo_liste_lecture
									where abo_liste_lecture.num_empr='".$_SESSION['id_empr_session']."' and abo_liste_lecture.etat=2
								) or
								avis_num_liste_lecture in (select id_liste from opac_liste_lecture where opac_liste_lecture.num_empr = '".$_SESSION['id_empr_session']."')
							)
						)
					)";
		} else {
			$query .= " and avis_private = 0";
		}
		$query .= " and num_notice = ".$this->object_id." and type_object = ".$this->get_object_type()."
			group by num_notice, type_object";
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
 			$this->average = number_format($row->average, 1, ',', '');
			$this->number = $row->number;
			if ($this->number) {
				$this->avis = array();
				$query = "select id_avis, note, commentaire, sujet, DATE_FORMAT(dateajout,'".$msg['format_date']."') as create_date, avis_private, avis_num_liste_lecture, num_empr, empr_login, empr_nom, empr_prenom
					from avis
					left join empr on id_empr=num_empr
					where num_notice='$this->object_id' and type_object = ".$this->get_object_type()." and valide=1";
				if($_SESSION['id_empr_session']) {
					$query .= "
						and (
							avis_private = 0
							or (avis_private = 1 and num_empr='".$_SESSION['id_empr_session']."')
							or (avis_private = 1 and avis_num_liste_lecture <> 0
									and (
										avis_num_liste_lecture in (
											select num_liste from abo_liste_lecture
											where abo_liste_lecture.num_empr='".$_SESSION['id_empr_session']."' and abo_liste_lecture.etat=2
										) or
										avis_num_liste_lecture in (select id_liste from opac_liste_lecture where opac_liste_lecture.num_empr = '".$_SESSION['id_empr_session']."')
									)
								)
							)";
				} else {
					$query .= " and avis_private = 0";
				}
				$query .= " order by avis_private desc, avis_num_liste_lecture, dateajout desc";
				$result = pmb_mysql_query($query);
				if ($result && pmb_mysql_num_rows($result)) {
					$notes_sum = 0;
					$this->avis['public'] = array();
					$this->avis['private'] = array();
					while ($avis = pmb_mysql_fetch_object($result)) {
						if($avis->avis_private) {
							$this->avis['private'][$avis->avis_num_liste_lecture][] = $avis;
						} else {
							$this->avis['public'][] = $avis;
							$notes_sum = $notes_sum + $avis->note;
						}
						if (!isset($this->nb_by_note[$avis->note])) {
							$this->nb_by_note[$avis->note] = 0;
						}
						$this->nb_by_note[$avis->note]++;
					}
					if(count($this->avis['public'])){
						$this->public_average = number_format($notes_sum / count($this->avis['public']), 1, ',', '');
					}
				}
			}
		}
	}

	/**
	 * Sélecteur des listes de lecture privées
	 */
	public function gen_selector_private_reading_lists($id_avis = 0, $selected = 0) {
		global $msg;

		$display = '';
		$query = "select id_liste from opac_liste_lecture
				join empr on empr.id_empr = opac_liste_lecture.num_empr
				where opac_liste_lecture.num_empr = '".$_SESSION['id_empr_session']."'
				or id_liste in (select num_liste from abo_liste_lecture where num_empr = '".$_SESSION['id_empr_session']."' and etat=2)
				";
		$listes = array();
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
			    $liste = new liste_lecture($row->id_liste);
			    $notices = $liste->notices;
				// Pour ne sélectionner que les listes de lecture qui intégrent cette notice
				if (in_array($this->object_id, $notices)) {
					$listes[] = $row->id_liste;
				}
			}
		}
		if (count($listes)) {
			$filter = implode(',', $listes);
		} else {
			$filter = 0;
		}
		$query = "select id_liste, nom_liste from opac_liste_lecture where id_liste in ( ".$filter." ) order by nom_liste ";
		$display = gen_liste($query,'id_liste','nom_liste', 'avis_'.$id_avis.'_listes_lecture_notice_'.$this->object_id, '', $selected, 0, $msg['avis_liste_lecture_default_value'], 0, $msg['avis_liste_lecture_default_value']);

		return $display;
	}

	/**
	 * Retourne l'affichage des étoiles
	 */
	protected function get_stars() {

		$stars="";
		if (!$this->public_average) {
			for ($i = 0; $i < 5; $i++) {
				$stars .= "<img class='img_star_avis' style='border:0px' src='".get_url_icon('star_unlight.png')."'  />";
			}
		} else {
			$cpt_star = 0;
			for ($i = 1; $i <= $this->public_average; $i++) {
				$stars.="<img class='img_star_avis' style='border:0px' src='".get_url_icon('star.png')."'  />";
				$cpt_star++;
			}
			if (substr($this->public_average,2,2) > 75) {
				$stars.="<img class='img_star_avis' style='border:0px' src='".get_url_icon('star.png')."'  />";
				$cpt_star++;
			} elseif (substr($this->public_average,2,2) > 25) {
				$stars .= "<img class='img_star_avis' style='border:0px' src='".get_url_icon('star-semibright.png')."'  />";
				$cpt_star++;
			}
			for ($cpt_star;$cpt_star < 5 ; $cpt_star++) {
				$stars .= "<img class='img_star_avis' style='border:0px' src='".get_url_icon('star_unlight.png')."'  />";
			}
		}
		return $stars;
	}

	/**
	 * Affichage des avis
	 */
	public function get_display() {
		global $msg, $charset;

		$display = '';

		//Affichage des Etoiles et nombre d'avis
		if ($this->number > 0) {
			$display = "<a href='#' class='donner_avis' title=\"".htmlentities($msg[$this->get_object_string_type().'_title_avis'],ENT_QUOTES,$charset)."\" onclick=\"openPopUp('avis.php?todo=liste&".$this->get_object_string_type()."id=".$this->object_id."','avis'); return false;\">".$this->number."&nbsp;".$msg[$this->get_object_string_type().'_bt_avis']."</a>";
			$stars = $this->get_stars();
			$display .= "<a href='#' class='consult_avis' title=\"".htmlentities($msg[$this->get_object_string_type().'_title_avis'],ENT_QUOTES,$charset)."\" onclick=\"openPopUp('avis.php?todo=liste&".$this->get_object_string_type()."id=".$this->object_id."','avis'); return false;\">".$stars."</a>";
		} else {
			$display = "<a href='#' class='donner_avis' title=\"".htmlentities($msg[$this->get_object_string_type().'_title_avis'],ENT_QUOTES,$charset)."\" onclick=\"openPopUp('avis.php?todo=liste&".$this->get_object_string_type()."id=".$this->object_id."','avis'); return false;\">".$msg['avis_aucun']."</a>";
		}
		return $display;
	}

	protected function get_note_display_stars($note) {
		global $msg;

		$stars = "";
		$cpt_star = 4;
		for ($i = 1; $i <= $note; $i++) {
			$stars .= "<img style='border:0px' src='".get_url_icon('star.png')."'  alt=\"".$msg['avis_detail_note_'.$i]."\" />";
		}
		for ($i = round($note); $i <= $cpt_star; $i++) {
			$stars .= "<img style='border:0px' src='".get_url_icon('star_unlight.png')."'  alt=\"".$msg['avis_detail_note_'.$i]."\" />";
		}
		return $stars;
	}

	/**
	 * Template des notes
	 * @param number $id_avis
	 */
	protected function get_note_form($id_avis = 0, $note_avis = 3) {
		global $msg;
		global $opac_avis_note_display_mode, $charset;

		$note_form = "";
		if($opac_avis_note_display_mode) {
			$note_form .= "<div class='row'><label>".$msg["avis_appreciation"]."</label>";
			switch($opac_avis_note_display_mode) {
				case 2 :
					$note_form .= "<select id='avis_".$id_avis."_note_".$this->get_object_string_type()."_".$this->object_id."' name='avis_".$id_avis."_note_".$this->get_object_string_type()."_".$this->object_id."'>";
					for($note = 0; $note <= 5; $note++) {
						$note_form .= "<option value='".$note."' ".($note == $note_avis ? "selected='selected'" : "").">".$msg["avis_detail_note_".$note]."</option>";
					}
					$note_form .= "</select>";
					break;
				case 4 :
					$note_form .= "
					<span class='echelle_avis'>
						<span class='echelle_avis_text'>".$msg["avis_note_1"]."</span>
						<span class='echelle_avis_stars'>
							<span class='echelle_avis_star'>";
					for($note = 1; $note <= 5; $note++) {
						$note_form .= "<input type='radio' name='avis_".$id_avis."_note_".$this->get_object_string_type()."_".$this->object_id."' id='avis_".$id_avis."_note_".$note."_".$this->get_object_string_type()."_".$this->object_id."' value='".$note."' ".($note == $note_avis ? "checked" : "")." /><label for='avis_".$id_avis."_note_".$note."_".$this->get_object_string_type()."_".$this->object_id."'></label>";
					}
					$note_form .= "
							</span>
						</span>
						<span class='echelle_avis_text'>".$msg["avis_note_5"]."</span>
					</span>";
					break;
				case 5 :
					$note_form .= "
					<span class='echelle_avis'>
						<span class='echelle_avis_stars'>
							<span class='echelle_avis_star'>
					";
					for($note = 1; $note <= 5; $note++) {
						$note_form .= "<input type='radio' name='avis_".$id_avis."_note_".$this->get_object_string_type()."_".$this->object_id."' id='avis_".$id_avis."_note_".$note."_".$this->get_object_string_type()."_".$this->object_id."' value='".$note."' title='".htmlentities($msg["avis_detail_note_".$note],ENT_QUOTES,$charset)."' ".($note == $note_avis ? "checked" : "")." onClick=\"avis_checked(".$id_avis.", ".$this->object_id.", '".$this->get_object_string_type()."');\" /><label for='avis_".$id_avis."_note_".$note."_".$this->get_object_string_type()."_".$this->object_id."'></label>";
					}
					$note_form .= "
							</span>
						</span>
						&nbsp;&nbsp;<span id='avis_".$id_avis."_detail_note_".$this->get_object_string_type()."_".$this->object_id."'>".$msg["avis_detail_note_3"]."</span>
					</span>";
					break;
				case 1 :
				case 3 :
					$note_form .= "
					<span class='echelle_avis'>
						".$msg['avis_note_1'];
					for($note = 1; $note <= 5; $note++) {
						$note_form .= "<input type='radio' name='avis_".$id_avis."_note_".$this->get_object_string_type()."_".$this->object_id."' id='avis_".$id_avis."_note_".$note."_".$this->get_object_string_type()."_".$this->object_id."' value='".$note."' ".($note == $note_avis ? "checked" : "")." />";
					}
					$note_form .= $msg['avis_note_5']."
						</span>";
					break;
			}
			$note_form .= "</div>";
		} else {
			$note_form .= "<input type='hidden' name='avis_".$id_avis."_note_".$this->get_object_string_type()."_".$this->object_id."' value='3'>";
		}
		return $note_form;
	}

	/**
	 * Formulaire d'édition d'un avis
	 */
	public function get_form($id_avis = 0) {
		global $msg;
		global $avis_tpl_form;
		global $id_liste;
		global $opac_avis_default_private;

		$form = $avis_tpl_form;
		$id_avis = intval($id_avis);
		if($id_avis) {
			$query = "select num_empr, note, sujet, commentaire, avis_private, avis_num_liste_lecture from avis where id_avis = ".$id_avis;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			$form = str_replace("!!note!!", $this->get_note_form($id_avis, $row->note), $form);
			$form = str_replace("!!sujet!!", $row->sujet, $form);
			$form = str_replace("!!commentaire!!", $row->commentaire, $form);
			$form = str_replace("!!private!!", ($row->avis_private ? "checked='checked'" : ''), $form);
			$form = str_replace("!!listes_lecture!!", $this->gen_selector_private_reading_lists($id_avis, $row->avis_num_liste_lecture), $form);
			$form = str_replace("!!button_send!!", "", $form);
			if(static::is_editable($row->avis_private, $row->num_empr)) {
				$form = str_replace("!!button_save!!", "<input type='button' class='bouton' onclick=\" save_avis(".$id_avis.", ".$this->object_id.", '".$this->get_object_string_type()."');  return false; \" value='".$msg["avis_bt_save"]."'>", $form);
				$form = str_replace("!!button_delete!!", "<input type='button' class='bouton' onclick=\"if(confirm('".addslashes($msg['avis_bt_delete_confirm'])."')) {delete_avis(".$id_avis.", ".$this->object_id.", '".$this->get_object_string_type()."');}  return false; \" value='".$msg["avis_bt_delete"]."'>", $form);
			} else {
				$form = str_replace("!!button_save!!", "", $form);
				$form = str_replace("!!button_delete!!", "", $form);
			}
		} else {
			$form = str_replace("!!note!!", $this->get_note_form(), $form);
			$form = str_replace("!!sujet!!", "", $form);
			$form = str_replace("!!commentaire!!", "", $form);
			$form = str_replace("!!private!!", ($opac_avis_default_private ? "checked='checked'" : ''), $form);
			$selected_reading_list = ($id_liste ? $id_liste : 0);
			$form = str_replace("!!listes_lecture!!", $this->gen_selector_private_reading_lists(0, $selected_reading_list), $form);
			$form = str_replace("!!button_send!!", "<input type='button' class='bouton' onclick=\" save_avis(0, !!object_id!!, '".$this->get_object_string_type()."');  return false; \" value='".$msg["avis_bt_envoyer"]."'>", $form);
			$form = str_replace("!!button_save!!", "", $form);
			$form = str_replace("!!button_delete!!", "", $form);
		}
		$form = str_replace("!!id!!", $id_avis, $form);
		$form = str_replace("!!object_id!!", $this->object_id, $form);
		$form = str_replace("!!object_type!!", $this->get_object_string_type(), $form);
		return $form;
	}

	/**
	 * Affichage de l'entête de liste
	 */
	protected function get_display_header_detail($node_id, $label) {
		$display = "
		<div id='".$node_id."_parent' class='avis-parent'>
            <br/>
			<span class='avis_header_liste' onClick=\"avis_expand_list('".$node_id."'); return false;\" style='cursor : pointer;'>
				<h3>".$label."</h3>
			</span>
		</div>";
		return $display;
	}

	/**
	 * Affichage du détail d'un avis
	 */
	protected function get_display_line_detail($data, $node_id, $order) {
		global $msg, $charset;
		global $opac_avis_note_display_mode;
		global $opac_avis_show_writer;
		global $opac_rgaa_active, $charset;

		if ($order % 2 == 1) $pair_impair="even"; else 	$pair_impair="odd";
		$display = "
		<div  id='".$node_id."_child_avis_".$data->id_avis."' class='$pair_impair avis_display' >
			<div class='avis_display_header'>";
		if($opac_avis_note_display_mode){
			$display .= "<span class='avis_detail_note_".$data->note." avis_note' >";
			switch ($opac_avis_note_display_mode) {
				case 1 :
					$display .= "<span title=\"".htmlentities($msg["avis_detail_note_".$data->note],ENT_QUOTES,$charset)."\" >".$this->get_note_display_stars($data->note)."</span>";
					break;
				case 2 :
					$display .= $msg['avis_detail_note_'.$data->note];
					break;
				case 3 :
					$display .= "<span title=\"".htmlentities($msg["avis_detail_note_".$data->note],ENT_QUOTES,$charset)."\" >".$this->get_note_display_stars($data->note)." ".$msg['avis_detail_note_'.$data->note]."</span><br />";
					break;
				case 4 :
					$display .= "<span title=\"".htmlentities($msg["avis_detail_note_".$data->note],ENT_QUOTES,$charset)."\" >".$this->get_note_display_stars($data->note)."</span>";
					break;
				case 5 :
					$display .= "<span title=\"".htmlentities($msg["avis_detail_note_".$data->note],ENT_QUOTES,$charset)."\" >".$this->get_note_display_stars($data->note)." ".$msg['avis_detail_note_'.$data->note]."</span><br />";
					break;
			}
			$display .= "</span>";
		}
		$display .= "
				<span class='avis_entete'>
					<b>".htmlentities($data->sujet,ENT_QUOTES,$charset)."</b>
				</span>
			</div>
			<div class='avis_display_info'>
				<span class='avis_creator'>";
		switch ($opac_avis_show_writer) {
			case 1 :
				if($data->empr_nom) {
					$display .= " ".$msg['avis_de']." ".$data->empr_prenom." ".$data->empr_nom." ".$msg['avis_le']." ".$data->create_date;
				}
				break;
			case 2 :
				if($data->empr_login) {
					$display .= " ".$msg['avis_de']." ".$data->empr_login." ".$msg['avis_le']." ".$data->create_date;
				}
				break;
			case 3 :
				if($data->empr_prenom) {
					$display .= " ".$msg['avis_de']." ".$data->empr_prenom." ".$msg['avis_le']." ".$data->create_date;
				}
				break;
			default :
				$display .= " ".ucfirst($msg['avis_le'])." ".$data->create_date;
				break;
		}
		if(static::is_editable($data->avis_private, $data->num_empr)) {
			if($opac_rgaa_active) {
				$display .= "
				<button class='button-unstylized' onclick=\"show_avis(".$data->id_avis.", ".$this->object_id.", '".$this->get_object_string_type()."'); return false;\" style='cursor : pointer'>
					<img src='".get_url_icon('tag.png')."' alt='".htmlentities($msg['avis_bt_edit'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['avis_bt_edit'],ENT_QUOTES,$charset)."' />
				</button>
				<button class='button-unstylized' onclick=\"if(confirm('".addslashes($msg['avis_bt_delete_confirm'])."')) {delete_avis(".$data->id_avis.", ".$this->object_id.", '".$this->get_object_string_type()."');} return false;\" style='cursor : pointer'>
					<img src='".get_url_icon('cross.png')."' alt='".htmlentities($msg['avis_bt_delete'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['avis_bt_delete'],ENT_QUOTES,$charset)."' />
				</button>
				";
			} else {
				$display .= "
				<a onclick=\"show_avis(".$data->id_avis.", ".$this->object_id.", '".$this->get_object_string_type()."'); return false;\" style='cursor : pointer'>
					<img src='".get_url_icon('tag.png')."' alt='".htmlentities($msg['avis_bt_edit'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['avis_bt_edit'],ENT_QUOTES,$charset)."' />
				</a>
				<a onclick=\"if(confirm('".addslashes($msg['avis_bt_delete_confirm'])."')) {delete_avis(".$data->id_avis.", ".$this->object_id.", '".$this->get_object_string_type()."');} return false;\" style='cursor : pointer'>
					<img src='".get_url_icon('cross.png')."' alt='".htmlentities($msg['avis_bt_delete'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['avis_bt_delete'],ENT_QUOTES,$charset)."' />
				</a>
				";
			}
		}
		$display .= "
				</span>
			</div>
			<div class='avis_display_content'>
				<span class='avis_commentaire'>".do_bbcode($data->commentaire)."</span>
			</div>
		</div>";
		if(static::is_editable($data->avis_private, $data->num_empr)) {
			$display .= $this->get_form($data->id_avis);
		}
		return $display;
	}

	/**
	 * Affichage du détail des avis
	 */
	public function get_display_detail() {
		global $msg;
		global $action; // pour gérer l'affichage des avis en impression de panier
		global $opac_avis_allow;
		global $opac_avis_note_display_mode;
		global $opac_rgaa_active;

		$display = '';
		if($this->number) {
			if ($action=="print" || ($opac_avis_allow==1 && !$_SESSION["user_code"] )) {
				$display .= "<h3 class='avis_detail'>".$msg['avis_detail']." :
						".str_replace("!!nb_avis!!",$this->number,$msg['avis_detail_nb_auth_ajt'])."
						</h3>";
			} else {
				$display .= "<h3 class='avis_detail'>".$msg['avis_detail'];
				if($opac_avis_note_display_mode && $opac_avis_note_display_mode!=2) {
					$display .= " ".$this->get_stars();
				}
				if($opac_rgaa_active) {
					$display .= "<span class='lien_ajout_avis'> :
					<button class='button-unstylized' onclick=\"show_avis(0, ".$this->object_id.", '".$this->get_object_string_type()."'); return false;\">".str_replace("!!nb_avis!!",$this->number,$msg['avis_detail_nb_ajt'])."</button>
					</span></h3>";
				} else {
					$display .= "<span class='lien_ajout_avis'> :
					<a href='#' onclick=\"show_avis(0, ".$this->object_id.", '".$this->get_object_string_type()."'); return false;\">".str_replace("!!nb_avis!!",$this->number,$msg['avis_detail_nb_ajt'])."</a>
					</span></h3>";
				}
				
				$display .= $this->get_form();
			}
		} else {
		    if ($action=="print" || ($opac_avis_allow==1 && (!isset($_SESSION["user_code"]) || !$_SESSION["user_code"]) )) {
				$display .= "<h3 class='avis_detail'>".$msg['avis_detail_aucun_auth_ajt']."</h3>";
			} else {
				if($opac_rgaa_active) {
					$display .= "<h3 class='avis_detail'>".$msg['avis_detail']."
						<span class='lien_ajout_avis'>
							<button class='button-unstylized' onclick=\"show_avis(0, ".$this->object_id.", '".$this->get_object_string_type()."'); return false;\">".$msg['avis_detail_aucun_ajt']."</button>
						</span>
					</h3>";
				} else {
					$display .= "<h3 class='avis_detail'>".$msg['avis_detail']."
						<span class='lien_ajout_avis'>
							<a href='#' onclick=\"show_avis(0, ".$this->object_id.", '".$this->get_object_string_type()."'); return false;\">".$msg['avis_detail_aucun_ajt']."</a>
						</span>
					</h3>";
				}
				$display .= $this->get_form();
			}
		}
		if(isset($this->avis['private']) && is_array($this->avis['private']) && count($this->avis['private'])) {
			foreach ($this->avis['private'] as $id_liste_lecture=>$group) {
				if($id_liste_lecture) {
					$liste_lecture = new liste_lecture($id_liste_lecture);
					$label_liste = $liste_lecture->nom_liste;
				} else {
					$label_liste = $msg['avis_private_list'];
				}
				$node_id = 'avis_private_'.$id_liste_lecture.'_'.$this->get_object_string_type().'_'.$this->object_id;
				$display .= $this->get_display_header_detail($node_id, $label_liste);
				$display .= "<div id='".$node_id."_child' class='avis-child'>";
				foreach ($group as $order => $data) {
					$display .= $this->get_display_line_detail($data, $node_id, $order);
				}
				$display .= "</div>";
			}
		}
		if(isset($this->avis['public']) && is_array($this->avis['public']) && count($this->avis['public'])) {
			$node_id = 'avis_public_'.$this->get_object_string_type().'_'.$this->object_id;
			$display .= $this->get_display_header_detail($node_id, $msg['avis_public']);
			$display .= "<div id='".$node_id."_child' class='avis-child'>";
			foreach ($this->avis['public'] as $order => $data) {
				$display .= $this->get_display_line_detail($data, $node_id, $order);
			}
			$display .= "</div>";
		}
		$display = "
			<script src='./includes/javascript/avis.js'></script>
			<div id='avis_".$this->object_id."' class='avis_".$this->get_object_string_type()."'>".$display."</div>";
		return $display;
	}

	/**
	 * Affichage des étoiles uniquement
	 * @return string
	 */
	public function get_display_only_stars() {
		global $msg, $charset;
		return "<a href='#' title=\"".htmlentities($msg[$this->get_object_string_type().'_title_avis'],ENT_QUOTES,$charset)."\" onclick=\"openPopUp('avis.php?todo=liste&".$this->get_object_string_type()."id=".$this->object_id."','avis'); return false;\">".$this->get_stars()."</a>";
	}

	public static function save_avis($id_avis, $object_id, $object_type) {
		global $charset;
		global $allow_avis, $opac_avis_allow;
		global $id_empr, $note, $sujet, $commentaire, $private, $num_liste_lecture;

		if(($opac_avis_allow==3) || ($_SESSION["user_code"] && ($opac_avis_allow ==1 || $opac_avis_allow ==2) && $allow_avis)) {
			if (!$note) $note="NULL";
			$masque="@<[\/\!]*?[^<>]*?>@si";
			$commentaire = preg_replace($masque,'',$commentaire);
			if($charset != "utf-8") $commentaire=cp1252Toiso88591($commentaire);
			if($private) {
				$valide = 1;
			} else {
				$valide = 0;
				$num_liste_lecture = 0; // un avis public ne sera pas associé à une liste de lecture
			}
			$id_avis = intval($id_avis);
			if($id_avis) {
				$query = "select avis_private, num_empr from avis where id_avis = '".$id_avis."'";
				$result = pmb_mysql_query($query);
				if($result) {
					$row = pmb_mysql_fetch_object($result);
					if(static::is_editable($row->avis_private, $row->num_empr)) {
						$query = "update avis set
							note = '".$note."', sujet = '".$sujet."', commentaire = '".$commentaire."', valide = '".$valide."',
							avis_private = '".$private."', avis_num_liste_lecture = '".$num_liste_lecture."'
							where id_avis = '".$id_avis."'";
						pmb_mysql_query($query);
						return true;
					}
				}
				return false;
			} else {
				$query = "insert into avis (num_empr,num_notice,type_object,note,sujet,commentaire,valide,avis_private,avis_num_liste_lecture) values ('".$id_empr."','".$object_id."','".$object_type."','".$note."','".$sujet."','".$commentaire."','".$valide."','".$private."','".$num_liste_lecture."')";
				pmb_mysql_query($query);
				return true;
			}
		} else {
			return false;
		}
	}

	public static function delete_avis($id_avis) {
		$id_avis = intval($id_avis);
		if($id_avis) {
			$query = "select avis_private, num_empr from avis where id_avis = '".$id_avis."'";
			$result = pmb_mysql_query($query);
			if($result) {
				$row = pmb_mysql_fetch_object($result);
				if(static::is_editable($row->avis_private, $row->num_empr)) {
					$query = "delete from avis where id_avis = '".$id_avis."'";
					pmb_mysql_query($query);
					return true;
				}
			}
		}
		return false;
	}

	public static function is_editable($avis_private = 0, $num_empr = 0) {
		if($avis_private && ($num_empr == $_SESSION['id_empr_session'])) {
			return true;
		} else {
			return false;
		}
	}

	public function get_object_type() {
		return $this->object_type;
	}

	public function get_average() {
		return $this->average;
	}

	public function get_public_average() {
		return $this->public_average;
	}

	public function get_number() {
		return $this->number;
	}

	public function get_avis() {
		return $this->avis;
	}

	public function get_nb_by_note() {
		return $this->nb_by_note;
	}

	public function set_average($average) {
		$this->average = $average;
	}

	public function set_public_average($public_average) {
		$this->public_average = $public_average;
	}

	public function set_number($number) {
		$this->number = $number;
	}

	public function set_avis($avis) {
		$this->avis = $avis;
	}

	public function set_nb_by_note($nb_by_note) {
		$this->nb_by_note = $nb_by_note;
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