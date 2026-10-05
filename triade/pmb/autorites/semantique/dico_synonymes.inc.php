<?php
// +-------------------------------------------------+
// ï¿? 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dico_synonymes.inc.php,v 1.16.2.1 2024/06/11 08:23:55 qvarin Exp $action $mot

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $word_search, $word_selected, $clause, $limit, $include_path, $class_path, $baseurl, $page, $aff_liste_mots, $action, $mot_js, $aff_modif_mot;
global $mot, $aff_mot_lie, $mots_lies, $msg, $id_mot, $word_id_selected, $max_f_word, $nb_per_page, $nb_per_page_gestion, $tri, $letter, $charset;

if (!isset($word_search)) $word_search = "";
if (!isset($word_selected)) $word_selected = "";
if (!isset($clause)) $clause = "";
if (!isset($limit)) $limit = "";

require_once("$include_path/templates/dico_synonymes.tpl.php");
require_once("$class_path/semantique.class.php");

$baseurl="./autorites.php?categ=semantique&sub=synonyms";
if (!isset($page) || !$page) $page=1;

//si on recherche une clé spécifique, on remplace !!cle!! par la clé sinon par rien
if ($word_search) {
    $aff_liste_mots=str_replace("!!cle!!", "'".htmlentities(stripslashes($word_search), ENT_QUOTES, $charset)."'", $aff_liste_mots);
} else {
    $aff_liste_mots=str_replace("!!cle!!", "", $aff_liste_mots);
}

switch ($action) {
	case 'view':
		$aff_mots=str_replace("!!mots_js!!",$mot_js,$aff_modif_mot);
		if ($mot) {

			$mot=stripslashes($mot);

			$t=semantique::list_synonyms(rawurldecode($mot));
			$compt=count($t);
			if ($compt) {
				//parcours des mots liés trouvés
				for ($j=0;$j<$compt;$j++) {
					$mots_lies.=$aff_mot_lie;
					$mots_lies=str_replace("!!iword!!",$j,$mots_lies);
					$mots_lies=str_replace("!!word!!",stripslashes($t[$j]["mot"]),$mots_lies);
					$mots_lies=str_replace("!!id_word!!",$t[$j]["code"],$mots_lies);
					$mots_lies=str_replace("!!word_mutual_checked!!",(!empty($t[$j]["has_word_mutual"]) ? "checked='checked'" : ""),$mots_lies);
				}
				$aff_mots=str_replace("!!mots_lie!!",$mots_lies,$aff_mots);
				$aff_mots=str_replace("!!max_f_word!!",$compt,$aff_mots);
			} else {
				//pas de résultat on affiche une seule case de saisie
				$aff_mot_lie=str_replace("!!iword!!","0",$aff_mot_lie);
				$aff_mot_lie=str_replace("!!word!!","",$aff_mot_lie);
				$aff_mot_lie=str_replace("!!id_word!!","",$aff_mot_lie);
				$aff_mot_lie=str_replace("!!word_mutual_checked!!","checked='checked'",$aff_mot_lie);
				$aff_mots=str_replace("!!mots_lie!!",$aff_mot_lie,$aff_mots);

				$aff_mots=str_replace("!!max_f_word!!","1",$aff_mots);
			}
		//	$baseurl.="&word_selected=".$mot;
			$aff_mots=str_replace("!!id_mot!!",$id_mot,$aff_mots);
			$aff_mots=str_replace("!!mot_original!!",$mot,$aff_mots);
			$aff_mots=str_replace("!!supprimer!!","<div class='right'><input type='button' class='bouton' value='".$msg["63"]."' onClick=\"var response; response=confirm('".$msg["word_del_confirm"]."'); if (response) document.location='./autorites.php?categ=semantique&sub=synonyms&action=del&id_mot=".$id_mot."&mot=".rawurlencode($mot)."'; return false;\"></div>\n",$aff_mots);
		} else {
			//si le mot est vide, on affiche le formulaire vierge
			$aff_mot_lie=str_replace("!!iword!!","0",$aff_mot_lie);
			$aff_mot_lie=str_replace("!!word!!","",$aff_mot_lie);
			$aff_mot_lie=str_replace("!!id_word!!","",$aff_mot_lie);
			$aff_mot_lie=str_replace("!!word_mutual_checked!!","checked='checked'",$aff_mot_lie);
			$aff_mots=str_replace("!!mots_lie!!",$aff_mot_lie,$aff_mots);
			$aff_mots=str_replace("!!mot_original!!","",$aff_mots);
			//on ne peut supprimer un mot inexistant
			$aff_mots=str_replace("!!supprimer!!","",$aff_mots);
			$aff_mots=str_replace("!!max_f_word!!","1",$aff_mots);
			$aff_mots=str_replace("!!id_mot!!","",$aff_mots);
		}
		if ($word_search) $baseurl.="&action=search&word_search=".rawurlencode($word_search);
		$aff_mots=str_replace("!!action!!",$baseurl,$aff_mots);
		print $aff_mots;
		break;
	case 'modif':
		$bool_erreur=false;
		if ($word_selected) {
			//insertion d'un nouveau mot
		    if ($word_id_selected){
		        $rqt_ins = "update mots set mot='".$word_selected."' where id_mot='$word_id_selected' ";
		    }else {
		        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT id_mot FROM mots WHERE mot = '$word_selected'"))) {
		            $bool_erreur=true;
		            print "<script> alert('{$msg['word_exists']}'); </script>";
		        } else {
    		        $rqt_ins ="insert into mots set mot='".$word_selected."' ";
        			@pmb_mysql_query($rqt_ins);
        			$word_id_selected= pmb_mysql_insert_id();
		        }
		    }
		} else {
				$bool_erreur=true;
				print "<script> alert('".$msg["word_error"]."'); </script>";
		}
		if ($bool_erreur==false) {
		    //nettoyage des liens qui ne devraient pas exister
		    pmb_mysql_query("DELETE FROM linked_mots WHERE num_linked_mot = 0 AND type_lien = 1;");
		    pmb_mysql_query("DELETE FROM linked_mots WHERE num_mot = 0 AND type_lien = 1;");
			$f_words=array();
			$f_words_mutal=array();
			//récupération des synonymes affectés au mot
			for ($i=$max_f_word-1;$i>=0 ; $i--) {
				$var_word = "f_word$i" ;
				global ${$var_word};
				if (${$var_word} && (${$var_word}!=$word_selected)) {
					$var_word_id="f_word_id$i";
					global ${$var_word_id};
					$var_word_mutual = "f_word_mutual$i" ;
					global ${$var_word_mutual};
					if (${$var_word_id}) {
						$f_words[]=${$var_word_id};
						$f_words_mutal[]=array(
								'code' =>${$var_word_id},
								'is_mutual' => (${$var_word_mutual} ? 1 : 0)
						);
					} else {
						//vérification de l'existence du mot
						$rqt_exist="select id_mot, mot from mots where mot='".${$var_word}."' and id_mot not in (select num_mot from linked_mots where linked_mots.num_linked_mot=0) group by id_mot";
						$query_exist=pmb_mysql_query($rqt_exist);
						if (!pmb_mysql_num_rows($query_exist)) {
							//insertion d'un nouveau mot
							$rqt_ins="insert into mots (mot) values ('".${$var_word}."')";

							@pmb_mysql_query($rqt_ins);
							//recherche de l'id du mot inséré
							$f_words[]=pmb_mysql_insert_id();
							$f_words_mutal[]=array(
									'code' => pmb_mysql_insert_id(),
									'is_mutual' => (${$var_word_mutual} ? 1 : 0)
							);
						} else {
						    $row = pmb_mysql_fetch_assoc($query_exist);
						    $f_words[]=$row["id_mot"];
						    $f_words_mutal[]=array(
						        'code' => $row["id_mot"],
						        'is_mutual' => (${$var_word_mutual} ? 1 : 0)
						    );
						}
					}
				}
			}
			//dédoublonne le tableau
			$f_words=array_unique($f_words);

			//suppression des enregistrements existants
			$rqt_del = "delete from linked_mots where num_mot='".$word_id_selected."' ";
			pmb_mysql_query($rqt_del);
			//insertion du mot et de ses synonymes
			$rqt_ins = "insert into linked_mots (num_mot, num_linked_mot, type_lien, ponderation) VALUES ";

			//récupération des synonymes affectés au mot
			for ($i=0;$i<count($f_words) ; $i++) {
				$valeurs="('".$word_id_selected."','".$f_words[$i]."','1','0.5')";
				pmb_mysql_query($rqt_ins.$valeurs);
			}
			//enregistrement des synonymes réciproques
			for ($i=0;$i<count($f_words_mutal) ; $i++) {
				if($f_words_mutal[$i]['is_mutual']) {
					$rqt_ins = "insert ignore into linked_mots (num_mot, num_linked_mot, type_lien, ponderation) VALUES ";
					$valeurs="('".$f_words_mutal[$i]['code']."','".$word_id_selected."','1','0.5')";
					pmb_mysql_query($rqt_ins.$valeurs);
				} else {
					pmb_mysql_query("delete from linked_mots where num_mot='".$f_words_mutal[$i]['code']."' and num_linked_mot='".$word_id_selected."' and type_lien='1'");
				}
			}
			$letter=convert_diacrit(pmb_strtolower(pmb_substr($word_selected,0,1)));
		}
		break;
	case 'search':
		if ($word_search) {
			$baseurl.="&action=search&word_search=".rawurlencode($word_search);
			$word_search=str_replace("*","%",rawurldecode($word_search));
			$clause=" and mot like '".$word_search."'";
		}
		if (!$nb_per_page) $nb_per_page=$nb_per_page_gestion;
		$limit=" limit ".(($page-1)*$nb_per_page).",".$nb_per_page;
		break;
	case 'last_words':
		$tri="order by id_mot desc";
		if (!$nb_per_page) $nb_per_page=$nb_per_page_search;
		$limit=" limit ".(($page-1)*$nb_per_page).",".$nb_per_page;
		break;
	case 'del':
		if ($id_mot) {
			//recherche si le mot est synonyme d'un autre mot
			$rqt="select num_mot from linked_mots where num_linked_mot=".$id_mot;
			$execute_query=pmb_mysql_query($rqt);
			if (!pmb_mysql_num_rows($execute_query)) {
				$rqt_del = "delete from mots where id_mot='".$id_mot."' ";
				@pmb_mysql_query($rqt_del);
				$rqt_del = "delete from linked_mots where num_mot='".$id_mot."' ";
				@pmb_mysql_query($rqt_del);
				//$letter=convert_diacrit(pmb_strtolower(pmb_substr($mot,0,1)));
			} else print "<script> alert('".addslashes($msg["other_word_syn_error"])."'); document.location='./autorites.php?categ=semantique&sub=synonyms&id_mot=$id_mot&mot=$mot&action=view';</script>";
		} else print "<script> alert('".$msg["word_error"]."'); </script>";
		break;
	default:

		break;
}
if ($action!='view') {
	if (!$nb_per_page) $nb_per_page=$nb_per_page_gestion;
	if ($action!='last_words') $tri="order by mot";
	//comptage des mots
	$rqt1="SELECT id_mot, mot FROM mots WHERE id_mot NOT IN (SELECT num_mot FROM linked_mots WHERE linked_mots.num_linked_mot=0 AND type_lien > 1)$clause GROUP BY id_mot";
	$execute_query1=pmb_mysql_query($rqt1);
	$nb_result=pmb_mysql_num_rows($execute_query1);
	pmb_mysql_free_result($execute_query1);
	//recherche des mots
	$affichage_mots="";
	$affichage_lettres="";
	$rqt="SELECT id_mot, mot FROM mots WHERE id_mot NOT IN (SELECT num_mot FROM linked_mots WHERE linked_mots.num_linked_mot=0 AND type_lien > 1)$clause GROUP BY id_mot $tri $limit";
	$execute_query=pmb_mysql_query($rqt);
	if ($execute_query&&$nb_result) {
		$affichage_mots="<div class='row'>";
		if ($action=='last_words'||$word_search) {
			$parity=1;
			$affichage_mots.="<table>";
			$affichage_mots.="<th>".$msg["word_selected"]."</th>";
		} else {
			$words_for_syn=array();
			$words_for_syn1=array();
		}
		while ($r=pmb_mysql_fetch_object($execute_query)) {
			if (!$word_search&&$action!='last_words') {
				$words_for_syn[$r->id_mot]=stripslashes($r->mot);
				$words_for_syn1[$r->id_mot]=convert_diacrit(pmb_strtolower(stripslashes($r->mot)));
			} else {
					if ($parity % 2) {
					$pair_impair = "even";
					} else {
						$pair_impair = "odd";
						}
					$parity += 1;
					$affichage_mots.="<tr class='$pair_impair'><td><a href='".$baseurl."&id_mot=".$r->id_mot."&mot=".rawurlencode(stripslashes($r->mot))."&action=view'>".stripslashes($r->mot)."</a></td></tr>";
				}
		}

		if ($action=='last_words'||$word_search) {
			$aff_liste_mots=str_replace("!!lettres!!","",$aff_liste_mots);
			$affichage_mots.="</table>";
			$compt=$nb_result;
		} else {
			if (count($words_for_syn)) {
				//toutes les lettres de l'alphabet dans un tableau
				$alphabet=array();
				$alphabet[]='';
				for ($i=97;$i<=122;$i++) {
					$alphabet[]=chr($i);
				}

				$bool=false;
				$alphabet_num = array();
				foreach($words_for_syn as $val) {
					if ($val!="") {
						$carac=convert_diacrit(pmb_strtolower(pmb_substr($val,0,1)));
						if ($bool==false) {
							if ($word_selected) $premier_carac=convert_diacrit(pmb_strtolower(pmb_substr($word_selected,0,1)));
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
				$affichage_lettres = "<div class='row'>";

				if (!empty($alphabet_num)) {
				    if ($letter == 'My') {
				        $affichage_lettres .= "<strong><u>#</u></strong> ";
				    } else {
				        $affichage_lettres .= "<a href='$baseurl&letter=My'>#</a> ";
				    }
				}
				foreach ($alphabet as $char) {
					$present = pmb_preg_grep("/^$char/i", $words_for_syn1);
					if (!empty($present) && strcasecmp($letter, $char)) {
						$affichage_lettres .= "<a href='$baseurl&letter=$char'>$char</a> ";
					} elseif (!strcasecmp($letter, $char)) {
						$affichage_lettres .= "<strong><u>$char</u></strong> ";
					} else {
					    $affichage_lettres .= "<span class='gris'>$char</span> ";
					}
				}
				$affichage_lettres .= "</div>";

				//affichage des mots

				$compt=0;
				$bool=false;
				if (!$page) $page=1;

				//parcours du tableau de mots, découpage en colonne et détermination des valeurs par rapport à la pagination et la lettre
				foreach ($words_for_syn as $key=>$valeur_syn) {
					if ($valeur_syn!="") {
						if ($compt>=(($page-1)*$nb_per_page)&&($compt<($page*$nb_per_page))) {
							if ($bool==false&&(($compt % 30)==0)) {
								$affichage_mots.="<div class='row'>";
							}
						}
						if ($letter!='My') {
							if (preg_match("/^$letter/i", convert_diacrit(pmb_strtolower($valeur_syn)))) {
								if (($compt>=(($page-1)*$nb_per_page))&&($compt<($page*$nb_per_page))) {
									$affichage_mots.="<a href='$baseurl&id_mot=".$key."&mot=".rawurlencode($valeur_syn)."&action=view'>".htmlentities($valeur_syn,ENT_QUOTES,$charset)."</a><br />\n";
								}
								$compt++;
							}
						} else {
							if (pmb_substr($valeur_syn,0,1)=='0'||!array_search(convert_diacrit(pmb_strtolower(pmb_substr($valeur_syn,0,1))),$alphabet)) {
								if (($compt>=(($page-1)*$nb_per_page))&&($compt<($page*$nb_per_page))) {
									$affichage_mots.="<a href='$baseurl&id_mot=".$key."&mot=".rawurlencode($valeur_syn)."&action=view'>".htmlentities($valeur_syn,ENT_QUOTES,$charset)."</a><br />\n";
								}
								$compt++;
							}
						}
						if ($compt>=(($page-1)*$nb_per_page)&&($compt<($page*$nb_per_page))) {
						    if ($compt == $nb_per_page) {
								$affichage_mots.="</div>";
							}
						}
						if ($compt==0) $bool=true;
					}
				}
				$aff_liste_mots=str_replace("!!lettres!!",$affichage_lettres,$aff_liste_mots);
			}
		}
		$affichage_mots.="</div>";
		$affichage_mots.="<div class='row'>&nbsp;</div>\n";
		//affichage de la pagination
		$affichage_mots.=aff_pagination ($baseurl.($letter ? "&letter=".$letter : ''), $compt, $nb_per_page, $page) ;
		$affichage_mots.="<div class='row'>&nbsp;</div>\n";
		$aff_liste_mots=str_replace("!!see_last_words!!","<div class='right'><a href='./autorites.php?categ=semantique&sub=synonyms&action=last_words'>".$msg["see_last_words_created"]."</a></div>",$aff_liste_mots);
	} else $aff_liste_mots=str_replace("!!see_last_words!!","",$aff_liste_mots);
	$aff_liste_mots=str_replace("!!lettres!!",$affichage_lettres,$aff_liste_mots);
	$aff_liste_mots=str_replace("!!liste_mots!!",$affichage_mots,$aff_liste_mots);
	$aff_liste_mots=str_replace("!!action!!",$baseurl,$aff_liste_mots);
	print $aff_liste_mots;
}
?>
