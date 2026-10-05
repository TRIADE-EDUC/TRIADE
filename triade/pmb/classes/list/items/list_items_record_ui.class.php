<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_items_record_ui.class.php,v 1.4.4.1 2025/03/07 15:59:18 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_items_record_ui extends list_items_ui {

	protected $expl_list_id_transfer = array();

	protected function _get_query_base() {
		global $msg;

		$query = "SELECT exemplaires.expl_id as id, exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, docs_codestat.*, lenders.*, tdoc_libelle, ";
		if(array_key_exists("surloc_libelle", $this->available_columns['main_fields']) !== false){
			$query .= "sur_location.*, ";
		}
		$query .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
		$query .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
		$query .= " IF(pret_retour>sysdate(),0,1) as retard " ;
		$query .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl ";
		$query .= " left join docs_location on exemplaires.expl_location=docs_location.idlocation ";
		if(array_key_exists("surloc_libelle", $this->available_columns['main_fields']) !== false){
			$query .= " left join sur_location on docs_location.surloc_num=sur_location.surloc_id ";
		}
		$query .= " left join docs_section on exemplaires.expl_section=docs_section.idsection ";
		$query .= " left join docs_statut on exemplaires.expl_statut=docs_statut.idstatut ";
		$query .= " left join docs_codestat on exemplaires.expl_codestat=docs_codestat.idcode ";
		$query .= " left join lenders on exemplaires.expl_owner=lenders.idlender ";
		$query .= " left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc  ";
		return $query;
	}

	protected function init_default_applied_sort() {
		global $pmb_expl_order;
		if($pmb_expl_order) {
			$cols = explode(',', $pmb_expl_order);
			foreach ($cols as $col) {
				$col_asc_desc = explode(' ', $col);
				$by = $col_asc_desc[0];
				$asc_desc = (!empty($col_asc_desc[1]) ? $col_asc_desc[1] : 'asc');
				if(array_key_exists($by, $this->available_columns['main_fields']) !== false){
					$this->add_applied_sort($by, $asc_desc);
				}
			}
		}elseif(array_key_exists("surloc_libelle", $this->available_columns['main_fields']) !== false){
			$this->add_applied_sort('surloc_libelle');
			$this->add_applied_sort('location_libelle');
			$this->add_applied_sort('section_libelle');
			$this->add_applied_sort('expl_cote');
			$this->add_applied_sort('expl_cb');
		}else{
			$this->add_applied_sort('location_libelle');
			$this->add_applied_sort('section_libelle');
			$this->add_applied_sort('expl_cote');
			$this->add_applied_sort('expl_cb');
		}
	}

	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
		global $pmb_expl_order;

		if(empty($this->applied_sort[0]['by']) && $pmb_expl_order) {
			return $this->_get_query_order_sql_build($pmb_expl_order);
		} else {
			return parent::_get_query_order();
		}
	}

	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('expl_cote', 'text', array('bold' => true));
	}

	protected function init_default_columns() {
		global $pmb_sur_location_activate, $pmb_expl_data;
		global $pmb_pret_groupement, $pmb_transferts_actif;

		if($pmb_pret_groupement || $pmb_transferts_actif) {
			$this->add_column_selection();
		}
		if($pmb_sur_location_activate) $surloc_field="surloc_libelle,";
		else $surloc_field="";
		if (!$pmb_expl_data) $pmb_expl_data="expl_cb,expl_cote,".$surloc_field."location_libelle,section_libelle,statut_libelle,tdoc_libelle";
		$colonnesarray=explode(",",$pmb_expl_data);
		if (!in_array("expl_cb", $colonnesarray)) array_unshift($colonnesarray, "expl_cb");

		//Présence de champs personnalisés
		$this->displayed_cp = array();
		if (strstr($pmb_expl_data, "#")) {
			$this->cp=new parametres_perso("expl");
		}
		for ($i=0; $i<count($colonnesarray); $i++) {
			if (substr($colonnesarray[$i],0,1)=="#") {
				//champ personnalisé
				if (!$this->cp->no_special_fields) {
					$id=substr($colonnesarray[$i],1);
					$this->add_column($this->cp->t_fields[$id]['NAME'], $this->cp->t_fields[$id]['TITRE']);
					$this->displayed_cp[$id] = $this->cp->t_fields[$id]['NAME'];
				}
			} else {
				$this->add_column($colonnesarray[$i]);
			}
		}
		if (!static::$print_mode) {
			$this->add_column('actions');
		}
	}

	protected function get_cell_content($object, $property) {
		global $msg, $charset, $base_path;
		global $pmb_droits_explr_localises, $explr_visible_mod;

		$content = '';
		switch($property) {
			case 'expl_cb':
				if (!empty(static::$link_expl)) {
					$tlink = str_replace('!!expl_id!!', $object->expl_id, static::$link_expl);
					$tlink = str_replace('!!expl_cb!!', rawurlencode($object->expl_cb), $tlink);
					$tlink = str_replace('!!notice_id!!', $object->expl_notice, $tlink);

				} else {
					$tlink = '';
				}
				//visibilité des exemplaires
				if ($pmb_droits_explr_localises) {
					$explr_tab_modif=explode(",",$explr_visible_mod);
					$as_modif = array_search($object->idlocation,$explr_tab_modif);
				} else {
					$as_modif = true;
				}
				if (($tlink) && ($as_modif!== FALSE && $as_modif!== NULL) ) {
					$content .= "<a href='$tlink'>".$object->expl_cb."</a>";
				} else {
					$content .= $object->expl_cb;
				}
				break;
// 			case 'expl_cote':
// 				if ($pmb_html_allow_expl_cote) {
// 					$content.=$object->expl_cote;
// 				} else {
// 					$content.=htmlentities($object->expl_cote,ENT_QUOTES, $charset);
// 				}
// 				break;
			case 'statut_libelle':
				if($object->pret_retour) {
					// exemplaire sorti
					$rqt_empr = "SELECT empr_nom, empr_prenom, id_empr, empr_cb FROM empr WHERE id_empr='$object->pret_idempr' ";
					$res_empr = pmb_mysql_query($rqt_empr) ;
					$res_empr_obj = pmb_mysql_fetch_object($res_empr) ;
					$situation = "<strong>{$msg[358]} ".$object->aff_pret_retour."</strong>";
					global $empr_show_caddie;
					if ($empr_show_caddie && (SESSrights & CIRCULATION_AUTH)) {
						$img_ajout_empr_caddie="<img src='".get_url_icon('basket_empr.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" onClick=\"openPopUp('".$base_path."/cart.php?object_type=EMPR&item=".$object->pret_idempr."', 'cart')\">&nbsp;";
					} else $img_ajout_empr_caddie="";
					switch (static::$print_mode) {
						case '2':
							$situation .= "<br />$res_empr_obj->empr_prenom $res_empr_obj->empr_nom";
							break;
						default :
							$situation .= "<br />$img_ajout_empr_caddie<a href='".$base_path."/circ.php?categ=pret&form_cb=".rawurlencode($res_empr_obj->empr_cb)."'>$res_empr_obj->empr_prenom $res_empr_obj->empr_nom</a>";
							break;
					}
				} else {
					// tester si réservé
					$result_resa = pmb_mysql_query("select 1 from resa where resa_cb='".addslashes($object->expl_cb)."' ");
					$reserve = pmb_mysql_num_rows($result_resa);

					// tester à ranger
					$result_aranger = pmb_mysql_query(" select 1 from resa_ranger where resa_cb='".addslashes($object->expl_cb)."' ");
					$aranger = pmb_mysql_num_rows($result_aranger);

					if ($reserve) $situation = "<strong>".$msg['expl_reserve']."</strong>"; // exemplaire réservé
					elseif($object->expl_retloc) $situation = $msg['resa_menu_a_traiter'];  // exemplaire à traiter
					elseif ($aranger) $situation = "<strong>".$msg['resa_menu_a_ranger']."</strong>"; // exemplaire à ranger
					elseif ($object->pret_flag) $situation = "<strong>{$msg[359]}</strong>"; // exemplaire disponible
					else $situation = "";
				}

				$content .= $object->statut_libelle;
				if ($situation) $content .= "<br />$situation";
				break;
			case 'actions':
				if(SESSrights & CATALOGAGE_AUTH){
					//le panier d'exemplaire
					$cart_click = "onClick=\"openPopUp('".$base_path."/cart.php?object_type=EXPL&item=".$object->expl_id."', 'cart')\"";
					$cart_over_out = "onMouseOver=\"show_div_access_carts(event,".$object->expl_id.",'EXPL',1);\" onMouseOut=\"set_flag_info_div(false);\"";
					$cart_link = "<a href='#' $cart_click $cart_over_out><img src='".get_url_icon('basket_small_20x20.gif')."' class='center' title='".htmlentities($msg['400'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['400'], ENT_QUOTES, $charset)."' /></a>";
					//l'icon pour le drag&drop de panier
					$drag_link = "<span onMouseOver='if(init_drag) init_drag();' id='EXPL_drag_" . $object->expl_id . "'  dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext=\"".htmlentities ( $object->expl_cb,ENT_QUOTES, $charset)."\" draggable=\"yes\" dragtype=\"notice\" callback_before=\"show_carts\" callback_after=\"\" style=\"padding-left:7px\"><img src=\"".get_url_icon('notice_drag.png')."\"/></span>";
				}else{
					$cart_click = "";
					$cart_link = "";
					$drag_link = "";
				}

				//l'impression de la fiche exemplaire
				$fiche_click = "onClick=\"openPopUp('".$base_path."/pdf.php?pdfdoc=fiche_catalographique&expl_id=".$object->expl_id."', 'Fiche', 500, 400, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\"";
				$fiche_link = "<a href='#' $fiche_click><img src='".get_url_icon('print.gif')."' class='center' alt='".$msg ['print_fiche_catalographique']."' title='".$msg ['print_fiche_catalographique']."'></a>";

				global $pmb_transferts_actif;

				//si les transferts sont activés
				if ($pmb_transferts_actif) {
					//si l'exemplaire n'est pas transferable on a une image vide
					$transfer_link = "<img src='".get_url_icon('spacer.gif')."' class='center' height=20 width=20>";

					$dispo_pour_transfert = transfert::est_transferable ( $object->expl_id );
					if (SESSrights & TRANSFERTS_AUTH && $dispo_pour_transfert) {
						//l'icon de demande de transfert
						$transfer_link = "<a href=\"#\" onClick=\"openPopUp('".$base_path."/catalog/transferts/transferts_popup.php?expl=".$object->expl_id."', 'cart', 600, 450, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes');\"><img src='".get_url_icon('peb_in.png')."' class='center' border=0 alt=\"".$msg ["transferts_alt_libelle_icon"]."\" title=\"".$msg ["transferts_alt_libelle_icon"]."\"></a>";
						$this->expl_list_id_transfer[] = $object->expl_id;
					}
				} else {
					$transfer_link = "";
				}

				//on met tout dans la colonne
				$content .= ((isset($fiche_link) && $fiche_link) ? $fiche_link." " : "").((isset($cart_link) && $cart_link) ? $cart_link." " : "").((isset($transfer_link) && $transfer_link) ? $transfer_link." " : "").((isset($drag_link) && $drag_link) ? $drag_link : "");
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}

	protected function get_display_content_object_list($object, $indice) {
		global $pmb_expl_list_display_comments;

		$display = parent::get_display_content_object_list($object, $indice);
		if (($object->expl_note || $object->expl_comment) && $pmb_expl_list_display_comments) {
			$notcom=array();
			$display .= "<tr><td colspan='".count($this->selected_columns)."'>";
			if ($object->expl_note && ($pmb_expl_list_display_comments & 1)) {
				$notcom[] = "<span class='erreur'>$object->expl_note</span>";
			}
			if ($object->expl_comment && ($pmb_expl_list_display_comments & 2)) {
				$notcom[] = "<span class='expl_list_comment'>".nl2br($object->expl_comment)."</span>";
			}
			$display .= implode("<br />",$notcom);
			$display .= "</tr>";
		}
		return $display;
	}

	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'actions'
		);
	}

	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}

// 	public function get_display_search_form() {
// 		//Ne pas retourner le formulaire car déjà inclu dans un autre
// 		return '';
// 	}

	/**
	 * Affiche la recherche + la liste
	 */
	public function get_display_list() {
		global $pmb_pret_groupement, $pmb_transferts_actif;

		$display = parent::get_display_list();
		if($pmb_pret_groupement || $pmb_transferts_actif) {
			$expl_list_id = array();
			foreach ($this->objects as $object) {
				$expl_list_id[] = $object->expl_id;
			}
			$prefix = $this->get_prefix();
			$display .= "
				<input type='hidden' id='".$prefix."_expl_list_id' name='".$prefix."_expl_list_id' value='".implode(",", $expl_list_id)."' />
				<input type='hidden' id='".$prefix."_expl_list_id_transfer' name='".$prefix."_expl_list_id_transfer' value='".implode(",", $this->expl_list_id_transfer)."' />
			";
		}
		return $display;
	}

	protected function init_default_selection_actions() {
		global $msg, $pmb_pret_groupement, $pmb_transferts_actif;

		parent::init_default_selection_actions();

		//Bouton groupe d'exemplaires
		if($pmb_pret_groupement) {
			$link = array();
			$this->add_selection_action('add_groupexpl', $msg['notice_for_expl_checked_groupexpl'], '', $link);
		}

		//Bouton générer un transfert
		if($pmb_transferts_actif) {
			$link = array();
			$this->add_selection_action('gen_transfert', $msg['notice_for_expl_checked_transfert'], '', $link);
		}
	}

	protected function add_event_on_selection_action($action=array()) {
		$display = "";
		switch ($action['name']) {
			case 'add_groupexpl':
			case 'gen_transfert':
				$prefix = $this->get_prefix();

				$display = "
					on(dom.byId('".$this->objects_type."_selection_action_".$action['name']."_link'), 'click', function() {
						var selection = new Array();
						query('.".$this->objects_type."_selection:checked').forEach(function(node) {
							selection.push(node.value);
						});
						if(selection.length) {
							var confirm_msg = '".(isset($action['link']['confirm']) ? addslashes($action['link']['confirm']) : '')."';
							if(!confirm_msg || confirm(confirm_msg)) {
							";
				if($action['name'] == 'add_groupexpl') {
					$display .= "
								if(check_if_checked(document.getElementById('".$prefix."_expl_list_id').value,'groupexpl')) {
									openPopUp('./select.php?what=groupexpl&caller=form_".$prefix."_expl&expl_list_id='+get_expl_checked(document.getElementById('".$prefix."_expl_list_id').value), 'selector');
								}
								";
				} else {
					$display .= "
								if(check_if_checked(document.getElementById('".$prefix."_expl_list_id_transfer').value,'transfer')) {
									openPopUp('./catalog/transferts/transferts_popup.php?expl='+get_expl_checked(document.getElementById('".$prefix."_expl_list_id_transfer').value), 'selector');
								}
								";
				}
				$display .= "
							}
						} else {
							alert('".addslashes($this->get_error_message_empty_selection($action))."');
						}
					});
				";
				break;
			default:
				$display .= parent::add_event_on_selection_action($action);
		}
		return $display;
	}

	protected function get_selection_mode() {
		return 'button';
	}

	protected function get_name_selected_objects() {
		return "checkbox_expl";
	}

	protected function get_message_for_selection() {
		global $msg;
		return str_replace(' :', '', strip_tags($msg['notice_for_expl_checked']));
	}

	protected function get_error_message_empty_selection($action=array()) {
		global $msg;

		switch ($action['name']) {
			case 'add_groupexpl':
				return $msg['notice_expl_have_select_expl'];
			case 'gen_transfert':
				return $msg['notice_expl_have_select_transfer_expl'];
			default:
				return parent::get_error_message_empty_selection($action);
		}
	}

	protected function get_prefix() {
		if($this->filters['expl_bulletin']){
			return "bull_".$this->filters['expl_bulletin'];
		}else{
			return "noti_".$this->filters['expl_notice'];
		}
	}

	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module, $sub;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=expl&sub='.$sub;
	}
}