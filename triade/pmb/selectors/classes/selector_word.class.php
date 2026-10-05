<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_word.class.php,v 1.2.6.1 2025/01/16 10:24:12 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/selectors/classes/selector.class.php");

class selector_word extends selector {

	public function __construct($user_input=''){
		parent::__construct($user_input);
	}

	public function proceed() {
		global $action;
		global $msg, $add_word_form, $f_word_add;
		global $letter;

		switch($action) {
			case 'add':
				//ajout de l'url
				$add_word_form=str_replace("!!action!!",static::get_base_url(),$add_word_form);
				print $add_word_form;
				break;
			case 'modif':
				if($f_word_add) {
					//vérification de l'existence
					$rqt="select id_mot, mot from mots left join linked_mots on (num_mot=id_mot) where mot='".$f_word_add."' and id_mot not in (select num_mot from linked_mots where linked_mots.num_linked_mot=0) group by id_mot";
					//$rqt="select id_mot from mots where mot='".$f_word_add."'";
					$execute_query=pmb_mysql_query($rqt);
					if (!$execute_query||!pmb_mysql_num_rows($execute_query)) {
						@pmb_mysql_query("INSERT INTO mots (mot) values ('".addslashes($f_word_add)."')");
						$deb_rech=$f_word_add;
						$letter=convert_diacrit(pmb_strtolower(pmb_substr($deb_rech,0,1)));
					} else {
						print "<script> alert('".$msg["word_exist"]."'); document.location='".static::get_base_url()."&action=add';</script>";
					}
				} else {
					print "<script> alert('".$msg["word_error"]."'); document.location='".static::get_base_url()."&action=add';</script>";
				}
			default :
				print $this->get_sel_header_template();
				print $this->get_js_script();
				if(!$this->user_input) {
					$this->user_input = '*';
				}
				print $this->get_display_list();
				print $this->get_sel_footer_template();
				break;
		}
	}

	protected function get_display_list() {
		global $msg, $charset;
		global $nb_per_page;
		global $page;
		global $letter;
		global $nb_per_page_select;
		global $caller;

		$display_list = '';
		$display_list .= $msg["select_word"]."<input type='button' class='bouton_small' value='$msg[word_add]' onClick=\"document.location='".static::get_base_url()."&action=add';\" style='margin-left:10px;'/>
			<hr />";
		$words_for_syn=array();
		$words_for_syn1=array();
		//recherche des mots
		$rqt="select id_mot, mot from mots left join linked_mots on (num_mot=id_mot) where id_mot not in (select num_mot from linked_mots where linked_mots.num_linked_mot=0) group by id_mot order by mot";
		$execute_query=pmb_mysql_query($rqt);
		while ($r=pmb_mysql_fetch_object($execute_query)) {
			$words_for_syn[$r->id_mot]=stripslashes($r->mot);
			$words_for_syn1[$r->id_mot]=convert_diacrit(pmb_strtolower($r->mot));
		}
		$alphabet_num = array();
		if (count($words_for_syn)) {
			//toutes les lettres de l'alphabet dans un tableau
			$alphabet=array();
			$alphabet[]='';
			for ($i=97;$i<=122;$i++) {
				$alphabet[]=chr($i);
			}
			$bool=false;
			foreach($words_for_syn as $val) {
				if ($val!="") {
					$carac=convert_diacrit(pmb_strtolower(pmb_substr($val,0,1)));
					if ($bool==false) {
						if ($this->user_input !== '*') $premier_carac=convert_diacrit(pmb_strtolower(pmb_substr($this->user_input,0,1)));
						else $premier_carac=$carac;
						$bool=true;
					}
					if (array_search($carac,$alphabet)===FALSE) $alphabet_num[]=$carac;
				}
			}
			//dédoublonnage du tableau des autres caractères
			if (count($alphabet_num)) $alphabet_num = array_unique($alphabet_num);

			if (!$letter) {
				if (count($alphabet_num)) $letter="My";
				elseif ($premier_carac) $letter=$premier_carac;
				else $letter="a";
			} elseif (!array_search($letter,$alphabet)) $letter="My";

			// affichage d'un sommaire par lettres
			$display_list.="<div class='row' style='margin-left:10px;'>";
			if (count($alphabet_num)) {
				if ($letter=='My') $display_list.="<strong><u>#</u></strong> ";
				else $display_list.="<a href='".static::get_base_url()."&letter=My'>#</a> ";
			}
			foreach($alphabet as $char) {
				$present = pmb_preg_grep("/^$char/i", $words_for_syn1);
				if (!empty($present) && strcasecmp($letter, $char)) {
					$display_list .= "<a href='".static::get_base_url()."&letter=$char'>$char</a> ";
				} elseif (!strcasecmp($letter, $char)) {
					$display_list .= "<strong><u>$char</u></strong> ";
				} else {
					$display_list .= "<span class='gris'>$char</span> ";
				}
			}
			$display_list .= "</div>";

			$display_list .= "<div class='row'>&nbsp;</div>";

			//affichage des mots
			$display_list .="<div class='row' style='margin-left:10px;'>";

			$compt=0;
			if (!$page) $page=1;
			if (!$nb_per_page) $nb_per_page=$nb_per_page_select;
			//parcours du tableau de mots, découpage en colonne et détermination des valeurs par rapport à la pagination et la lettre
			foreach ($words_for_syn as $key=>$valeur_syn) {
				if ($valeur_syn!="") {
					if ($letter!='My') {
						if (preg_match("/^$letter/i", convert_diacrit(pmb_strtolower($valeur_syn)))) {
							if (($compt>=(($page-1)*$nb_per_page))&&($compt<($page*$nb_per_page))) {
								$display_list.="<a href='#' onClick=\"set_parent('".$caller."','".$key."','".htmlentities(addslashes($valeur_syn),ENT_QUOTES,$charset)."')\">";
								$display_list.=htmlentities($valeur_syn,ENT_QUOTES,$charset)."</a><br />\n";
							}
							$compt++;
						}
					} else {
						if (pmb_substr($valeur_syn,0,1)=='0'||!array_search(convert_diacrit(pmb_strtolower(pmb_substr($valeur_syn,0,1))),$alphabet)) {
							if (($compt>=(($page-1)*$nb_per_page))&&($compt<($page*$nb_per_page))) {
								$display_list.="<a href='#' onClick=\"set_parent('".$caller."','".$key."','".htmlentities(addslashes($valeur_syn),ENT_QUOTES,$charset)."')\">";
								$display_list.=htmlentities($valeur_syn,ENT_QUOTES,$charset)."</a><br />\n";
							}
						}
						$compt++;
					}
				}
			}
			$display_list.="</div>";
			$display_list.="<div class='row'>&nbsp;</div><hr />\n";
			//affichage de la pagination
			$display_list.=aff_pagination (static::get_base_url()."&user_input=".$this->user_input."&letter=".$letter, $compt, $nb_per_page, $page) ;
			$display_list.="<div class='row'>&nbsp;</div>\n";
		}
		return $display_list;
	}
}
?>