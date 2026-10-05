<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pointage_exemplarise.php,v 1.9.6.1 2024/12/30 10:42:58 dgoron Exp $

// définition du minimum nécéssaire
$base_path=".";                            
$base_auth = "CATALOGAGE_AUTH";
$base_title = "\$msg[6]";
$base_use_dojo = 1;
$base_nochat = 1;

require_once ("$base_path/includes/init.inc.php");

global $class_path, $include_path, $msg, $act, $nonrecevable;
global $pmb_numero_exemplaire_auto_script;
global $id_bull, $bul_id, $bul_no, $bul_date, $date_date, $bul_titre;
global $f_ex_cb, $expl_cote, $expl_note, $expl_comment, $expl_prix, $expl_typdoc, $expl_location;
global $expl_section, $expl_statut, $expl_codestat, $expl_owner, $type_antivol;
global $create_notice_bul, $xmlta_doctype_bulletin, $deflt_notice_statut, $deflt_notice_is_new;
global $pmb_serialcirc_subst;
global $numero, $nume, $vol, $tom;
global $f_fichier, $f_url;
global $deflt_upload_repertoire, $pmb_indexation_docnum, $pmb_indexation_docnum_default, $f_filename, $f_explnum_statut;

require_once($class_path."/serials.class.php");
require_once($class_path."/serial_display.class.php");
require_once("$include_path/explnum.inc.php") ;
require_once ($class_path . "/parse_format.class.php");
require_once($class_path."/parametres_perso.class.php");
require_once("$class_path/abts_pointage.class.php");
require_once("$class_path/explnum.class.php");
require_once("$class_path/serialcirc_diff.class.php");
require_once($class_path."/serialcirc.class.php");
require_once($class_path."/expl.class.php");
require_once($class_path.'/audit.class.php');

$templates = "<script src=\"".$base_path."/javascript/ajax.js\" type=\"text/javascript\"></script>";
$templates.= <<<ENDOFFILE
<div id='att'></div>
			<script type='text/javascript'>
				function desactive(obj) {
					var obj_1=obj+"_1";	
					var obj_2=obj+"_2";	
					var obj_3=obj+"_3";		
					parent.document.getElementById(obj_1).disabled = true;
					parent.document.getElementById(obj_2).disabled = true;
					parent.document.getElementById(obj_3).disabled = true;
				}		
				function enregistre(obj,bul_id) {
					var obj_bul=obj+"_bul";		
					desactive(obj)
					parent.document.getElementById(obj_bul).innerHTML="<a href='./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id="+bul_id+"'>"+"!!Voir_le_bulletin!!"+"</a>!!serialcir_print!!!!print_cote!!";
					parent.kill_frame_periodique();
				}		
				function Fermer(obj) {
					desactive(obj)
				 	parent.kill_frame_periodique();
				}
			</script>
<div style='width: 98%;'>
	<div id="bouton_fermer_notice_preview" class="right"><a href='#' class='panel-close' onClick='parent.kill_frame_periodique();return false;'><i class='fa fa-times' aria-hidden='true'></i></a></div>
	!!form!!
</div>	
<script>
	ajax_parse_dom();
</script>							
ENDOFFILE;
$templates=str_replace("!!Voir_le_bulletin!!",$msg['pointage_voir_le_bulletin'],$templates);


if($act=="memo_doc_num"){	
	// retour après telechargement du document numérique associé au bulletin
	print "
		<script type='text/javascript'>
			function desactive(obj) {
				var obj_1=obj+'_1';	
				var obj_2=obj+'_2';	
				var obj_3=obj+'_3';		
				parent.document.getElementById(obj_1).disabled = true;
				parent.document.getElementById(obj_2).disabled = true;
				parent.document.getElementById(obj_3).disabled = true;
			}
			var obj_bul='".$id_bull."_bul';		
			desactive($id_bull);
			parent.document.getElementById(obj_bul).innerHTML=\"<a href='./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=$bul_id'>".$msg['pointage_voir_le_bulletin']."</a>\";
			parent.kill_frame_periodique();
		</script>
	";
	exit;
}	
/*
if(!$expl_id) // pas d'id, c'est une création
	echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4007], $serial_header);
else echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4008], $serial_header);
*/
function do_selector_bul_section($section_id, $location_id) {
 	global $charset;
	global $deflt_docs_section;
	global $deflt_docs_location;
	
	if (!$section_id) {
	    $section_id=$deflt_docs_section ;
	}
	if (!$location_id) {
	    $location_id=$deflt_docs_location;
	}
	$rqtloc = "SELECT idlocation FROM docs_location order by location_libelle";
	$resloc = pmb_mysql_query($rqtloc);
	$selector = '';
	while ($loc=pmb_mysql_fetch_object($resloc)) {
		$requete = "SELECT idsection, section_libelle FROM docs_section, docsloc_section where idsection=num_section and num_location='$loc->idlocation' order by section_libelle";
		$result = pmb_mysql_query($requete);
		$nbr_lignes = pmb_mysql_num_rows($result);
		if ($nbr_lignes) {			
		    if ($loc->idlocation==$location_id) {
		        $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:block\">";
		    } else {
		        $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:none\">";
		    }
			$selector .= "<select name='f_ex_section".$loc->idlocation."' id='f_ex_section".$loc->idlocation."'>";
			while($line = pmb_mysql_fetch_row($result)) {
				$selector .= "<option value='$line[0]'";
				$line[0] == $section_id ? $selector .= ' SELECTED>' : $selector .= '>';
	 			$selector .= htmlentities($line[1],ENT_QUOTES, $charset).'</option>';
			}                                         
			$selector .= '</select></div>';
		}                 
	}
	return $selector;                         
}                                                 

function bul_do_form($obj) {
	// $obj = objet contenant les propriétés de l'exemplaire associé
	global $bul_expl_form1,$expl_bulletinage_tpl;
	global $msg; // pour texte du bouton supprimer
	global $charset;
	global $pmb_antivol ;
	global $id_bull,$bul_id,$serial_id,$numero,$pmb_rfid_activate,$pmb_rfid_serveur_url;
	global $deflt_explnum_statut;
	
	if(!$obj->abt_numeric) {
	    $bul_expl_form1 = str_replace('!!expl_bulletinage_tpl!!', $expl_bulletinage_tpl, $bul_expl_form1);	
	} else {
	    $bul_expl_form1 = str_replace('!!expl_bulletinage_tpl!!', "", $bul_expl_form1);	
	}
	$action = "./pointage_exemplarise.php?act=update&id_bull=$id_bull&bul_id=$bul_id";
	
	// statut
	$select_statut = gen_liste_multiple ("select id_explnum_statut, gestion_libelle from explnum_statut order by 2", "id_explnum_statut", "gestion_libelle", "id_explnum_statut", "f_explnum_statut", "", $deflt_explnum_statut, "", "","","",0) ;
	$bul_expl_form1 = str_replace('!!statut_list!!', $select_statut, $bul_expl_form1);

	if(!isset($obj->expl_bulletin)) $obj->expl_bulletin = 0;
	if(!isset($obj->expl_cb)) $obj->expl_cb = '';
	if(!isset($obj->expl_id)) $obj->expl_id = 0;
	if(!isset($obj->expl_notice)) $obj->expl_notice = 0;
	if(!isset($obj->expl_note)) $obj->expl_note = '';
	if(!isset($obj->expl_comment)) $obj->expl_comment = '';
	if(!isset($obj->bul_titre)) $obj->bul_titre = '';
	
	// mise à jour des champs de gestion
	$bul_expl_form1 = str_replace('!!bul_id!!', $obj->expl_bulletin, $bul_expl_form1);
	$bul_expl_form1 = str_replace('!!id_form!!', md5(microtime()), $bul_expl_form1);
	$bul_expl_form1 = str_replace('!!org_cb!!', $obj->expl_cb, $bul_expl_form1);	
	$bul_expl_form1 = str_replace('!!expl_id!!', $obj->expl_id, $bul_expl_form1);
	
	$bul_expl_form1 = str_replace('!!action!!', $action, $bul_expl_form1);
	$bul_expl_form1 = str_replace('!!id!!', $obj->expl_notice, $bul_expl_form1);
	$bul_expl_form1 = str_replace('!!cb!!', $obj->expl_cb, $bul_expl_form1);
	$bul_expl_form1 = str_replace('!!note!!', $obj->expl_note, $bul_expl_form1);
	$bul_expl_form1 = str_replace('!!comment!!', $obj->expl_comment, $bul_expl_form1);
	$bul_expl_form1 = str_replace('!!cote!!', htmlentities($obj->expl_cote,ENT_QUOTES, $charset), $bul_expl_form1);
	$bul_expl_form1 = str_replace('!!prix!!', $obj->expl_prix, $bul_expl_form1);
	if(!$obj->abt_numeric) {
	    $bul_expl_form1 = str_replace('!!focus!!',$obj->focus, $bul_expl_form1);
	} else {
	    $bul_expl_form1 = str_replace('!!focus!!',"", $bul_expl_form1);
	}
	// select "type document"
	$bul_expl_form1 = str_replace('!!type_doc!!', do_selector('docs_type', 'expl_typdoc', $obj->expl_typdoc), $bul_expl_form1);		
	// select "section"
	$bul_expl_form1 = str_replace('!!section!!', do_selector_bul_section($obj->expl_section, $obj->expl_location), $bul_expl_form1);
	// select "statut"
	$bul_expl_form1 = str_replace('!!statut!!', do_selector('docs_statut', 'expl_statut', $obj->expl_statut), $bul_expl_form1);
	// select "localisation"
	$bul_expl_form1 = str_replace('!!localisation!!',
				gen_liste ("select distinct idlocation, location_libelle from docs_location, docsloc_section where num_location=idlocation order by 2", "idlocation", "location_libelle", 'expl_location', "calcule_section(this);", $obj->expl_location, "", "","","",0),
				$bul_expl_form1);
	// select "code statistique"
	$bul_expl_form1 = str_replace('!!codestat!!', do_selector('docs_codestat', 'expl_codestat', $obj->expl_codestat), $bul_expl_form1);
	// select "owner"
	$bul_expl_form1 = str_replace('!!owner!!', do_selector('lenders', 'expl_owner', $obj->expl_owner), $bul_expl_form1);
	$selector="";
	if($pmb_antivol>0) {
		// select "type_antivol"
		$selector = "
		<div class='colonne3'>
			<label class='etiquette' for='type_antivol'>".$msg['type_antivol']."</label>
			<div class='row'>";
				$selector .= exemplaire::gen_antivol_selector($obj->type_antivol);
		$selector .= '
			</div>
		</div>';   
	}        
	$bul_expl_form1 = str_replace('!!type_antivol!!', $selector, $bul_expl_form1);
	$bul_expl_form1 = str_replace('!!bul_id!!', $bul_id, $bul_expl_form1);
	$bul_expl_form1 = str_replace('!!expl_id!!', $obj->expl_id, $bul_expl_form1);	
	$bul_expl_form1 = str_replace('!!bul_no!!', htmlentities($obj->bul_no,ENT_QUOTES, $charset)	, $bul_expl_form1);
	$date_date = "<input type='date' id='date_date_parution' name='date_date' value='".$obj->date_date."' />";		
	$bul_expl_form1 = str_replace('!!date_date!!', $date_date, $bul_expl_form1);
	$bul_expl_form1 = str_replace('!!bul_date!!', htmlentities($obj->bul_date,ENT_QUOTES, $charset), $bul_expl_form1);
	$bul_expl_form1 = str_replace('!!bul_titre!!', htmlentities($obj->bul_titre,ENT_QUOTES, $charset), $bul_expl_form1);
	$bul_expl_form1 = str_replace('!!serial_id!!', $serial_id, $bul_expl_form1);
	$bul_expl_form1 = str_replace('!!numero!!', $obj->bul_titre, $bul_expl_form1);	
	$bul_expl_form1 = str_replace('!!destinataire!!', $obj->destinataire, $bul_expl_form1);

	$p_perso=new parametres_perso("expl");
	if (!$p_perso->no_special_fields) {
		$c=0;
		$perso="";
		$perso_=$p_perso->show_editable_fields($obj->expl_id);
		for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
			$p=$perso_["FIELDS"][$i];
			if ($c==0) $perso.="<div class='row'>\n";
			$perso.="<div class='colonne3'><label for='".$p["NAME"]."' class='etiquette'>".$p["TITRE"]." </label>".$p["COMMENT_DISPLAY"]."<div class='row'>".$p["AFF"]."</div></div>\n";
			$c++;
			if ($c==3) {
				$perso.="</div>\n";
				$c=0;
			}
		}	
		if ($c==1) {
		    $perso.="<div class='colonne2'>&nbsp;</div>\n</div>\n";
		}
		$perso=$perso_["CHECK_SCRIPTS"]."\n".$perso;
	} else {
		$perso="\n<script>function check_form() { return true; }</script>\n";
	}
	$bul_expl_form1 = str_replace("!!champs_perso!!",$perso,$bul_expl_form1);
	
	if ($pmb_rfid_activate==1 && $pmb_rfid_serveur_url && !$obj->abt_numeric) {
		$script_rfid_encode="if(script_rfid_encode()==false) return false;";	
		$bul_expl_form1 = str_replace('!!questionrfid!!', $script_rfid_encode, $bul_expl_form1);
	} else {
	    $bul_expl_form1 = str_replace('!!questionrfid!!', '', $bul_expl_form1);
	}
	
	$bul_expl_form1 = str_replace('!!create_notice_bul!!', '<input type="checkbox" value="1" id="create_notice_bul" name="create_notice_bul">&nbsp;'.$msg['bulletinage_create_notice'], $bul_expl_form1);
	
	return $bul_expl_form1 ;
}

function sql_value($rqt) {
    $result=pmb_mysql_query($rqt);
    if($result) {
        $row = pmb_mysql_fetch_row($result);
        if(isset($row[0]))	{
            return $row[0];
        }
    }
	return '';
}

$value = array();

$id_bull = intval($id_bull);
$requete = "SELECT * FROM abts_grille_abt WHERE id_bull='$id_bull'";
$abtsQuery = pmb_mysql_query($requete);
if(pmb_mysql_num_rows($abtsQuery)) {
	$abts = pmb_mysql_fetch_object($abtsQuery);
	$modele_id = $abts->modele_id;
	$abt_id = $abts->num_abt;
	$value['date_date']=$abts->date_parution;
}
$abt_id = intval($abt_id);
$requete = "SELECT * FROM abts_abts WHERE abt_id='$abt_id'";
$abtsQuery = pmb_mysql_query($requete);
if(pmb_mysql_num_rows($abtsQuery)) {
	$abts = pmb_mysql_fetch_object($abtsQuery);
	$abt_numeric = $abts->abt_numeric;
	$exemp_auto = $abts->exemp_auto;
	$type_antivol = $abts->type_antivol;
	$date_debut = $abts->date_debut;
	$date_fin = $abts->date_fin;
	
}
$modele_id = intval($modele_id);
$requete = "SELECT num_notice,format_periode FROM abts_modeles WHERE modele_id='$modele_id'";
$abtsQuery = pmb_mysql_query($requete);
if(pmb_mysql_num_rows($abtsQuery)) {
	$abts = pmb_mysql_fetch_object($abtsQuery);
	$format_periode = $abts->format_periode;
	$serial_id = $abts->num_notice;
}

//Préparation nouveau bulletin
$myBulletinage = new bulletinage(0, $serial_id);

//Genération du libellé de période
$print_format=new parse_format();
$print_format->var_format['DATE'] = $value['date_date'];
$print_format->var_format['NUM'] = $nume;
$print_format->var_format['VOL'] = $vol;
$print_format->var_format['TOM'] = $tom;
$print_format->var_format['START_DATE'] = $date_debut;
$print_format->var_format['END_DATE'] = $date_fin;

$requete = "SELECT * FROM abts_abts_modeles WHERE modele_id='$modele_id' and abt_id='$abt_id' ";
$abtsabtsQuery = pmb_mysql_query($requete);
if(pmb_mysql_num_rows($abtsabtsQuery)) {
	$abtsabts = pmb_mysql_fetch_object($abtsabtsQuery);
	$print_format->var_format['START_NUM'] = $abtsabts->num;
	$print_format->var_format['START_VOL'] = $abtsabts->vol;
	$print_format->var_format['START_TOM'] = $abtsabts->tome;	
	$num_statut=$abtsabts->num_statut_general;
}


$print_format->cmd = $format_periode;
$libelle_periode=$print_format->exec_cmd();

$flag_exemp_auto=0;
if ($exemp_auto==1){
	if (file_exists($include_path."/$pmb_numero_exemplaire_auto_script")) {
		require_once($include_path."/$pmb_numero_exemplaire_auto_script");
		$flag_exemp_auto=1;
	}	
}

if(($act=='update') ) {
	$value['niveau_biblio']='b'; 
	$value['niveau_hierar']='2'; 
	$value['bul_no']=$bul_no;
	$value['bul_date']=$bul_date;
	$value['date_date']=$date_date;
	$value['bul_titre']=$bul_titre;
	// on verifie l'existance du bulletin avec le numéro et la date_date du formulaire
	$bul_id=sql_value("SELECT bulletin_id FROM bulletins where bulletin_numero='$bul_no' and date_date='".$value['date_date']."' and bulletin_notice='".$serial_id."'");
	if(!$bul_id){
		//création de notice de bulletin si case à cocher
		if (isset($create_notice_bul) && $create_notice_bul) {
			$value['create_notice_bul']=true;
			$value['tit1'] = $value["bul_no"].($value["bul_date"]?" - ".$value["bul_date"]:"").($bul_titre?" - ".$bul_titre:"");
			if($xmlta_doctype_bulletin) {
				$value['typdoc']=$xmlta_doctype_bulletin;
			} else {
				$value['typdoc']=$myBulletinage->get_serial()->typdoc;
			}
			$value['statut']=$deflt_notice_statut;
			$value['notice_is_new']=$deflt_notice_is_new;
			
			if($value['date_date'] == '0000-00-00' || !isset($value['bul_date'])) $value['year'] = "";
			else $value['year'] = substr($value['date_date'],0,4);
		
			$value['date_parution'] = $value['date_date'];
		}
		//Création du bulletin si pas déjà présent
		$bul_id = $myBulletinage->update($value);
	}
	if(!$abt_numeric){
		// c'est un abonnement qui n'est pas exclusivement numérique. On crée l'exemplaire de bulletin
		$expl_cote = clean_string($expl_cote);
		$expl_note = clean_string($expl_note);
		$expl_comment = clean_string($expl_comment);
		$expl_prix = clean_string($expl_prix);	
		
		$formlocid="f_ex_section".$expl_location ;
		$expl_section=${$formlocid} ;
	
		// si le code-barre saisi est vide, on affiche une erreur
		if (trim($f_ex_cb) == "") {
			print "<script>alert('".addslashes($msg['pointage_message_code_vide'])."'); history.go(-1);</script>";
			exit();
		}
		// si le code-barre saisi est déjà utilisé, on affiche une erreur
		$requete = "SELECT COUNT(1) FROM exemplaires WHERE expl_cb='$f_ex_cb'";
		$myQuery = pmb_mysql_query($requete);
		if(pmb_mysql_result($myQuery, 0, 0))  { 
			print "<script>alert('".addslashes($msg['pointage_message_code_utilise'])."'); history.go(-1);</script>";
			exit();
		}
		$p_perso=new parametres_perso("expl");
		$nberrors=$p_perso->check_submited_fields();
		if($nberrors) {
		    print "<script>alert('".addslashes(strip_tags($p_perso->error_message))."'); history.go(-1);</script>";
		    exit();
		}
		
		// Dépiéger l'exemplaire (lié à l'abonnement) du dernier bulletin		
		if($num_statut) {
			//A ne faire que si l'abonnement n'a pas de liste de circulation associée...
			$query = "select id_serialcirc from serialcirc where num_serialcirc_abt = ".$abt_id;
			$result = pmb_mysql_query($query);
			if(!pmb_mysql_num_rows($result)){
				$requete="SELECT bulletin_id  FROM bulletins where date_date<'$date_date' and bulletin_notice='$serial_id' ORDER BY date_date DESC LIMIT 1";
				$result_dernier = pmb_mysql_query($requete);
				if ($r_dernier = pmb_mysql_fetch_object($result_dernier)) {
					$dernier_bul_id	=$r_dernier->bulletin_id;
					$requete = "update exemplaires set expl_statut=$num_statut where expl_bulletin=$dernier_bul_id and expl_abt_num='$abt_id' ";
					pmb_mysql_query($requete);
				}
			}
		}
		
		
		$transfert_origine=", transfert_location_origine='$expl_location', transfert_statut_origine='$expl_statut', transfert_section_origine='$expl_section' ";
		
		$values = "expl_cb='$f_ex_cb'";
		$values .= ", expl_notice='0'";
		$values .= ", expl_bulletin='$bul_id'";
		$values .= ", expl_typdoc='$expl_typdoc'";
		$values .= ", expl_cote='$expl_cote'";
		$values .= ", expl_section='$expl_section'";
		$values .= ", expl_statut='$expl_statut'";
		$values .= ", expl_location='$expl_location' $transfert_origine ";
		$values .= ", expl_codestat='$expl_codestat'";
		$values .= ", expl_note='$expl_note'";
		$values .= ", expl_comment='$expl_comment'";
		$values .= ", expl_prix='$expl_prix'";
		$values .= ", expl_owner='$expl_owner'";
		$values .= ", type_antivol='$type_antivol'";
		$values .= ", expl_abt_num='$abt_id'";
		$requete = "INSERT INTO exemplaires set $values , create_date=sysdate() ";
	
		$myQuery = pmb_mysql_query($requete);
		$expl_id=pmb_mysql_insert_id();	
		audit::insert_creation (AUDIT_EXPL, $expl_id) ;

		//parametres_perso de l'exemplaire
		if(!$nberrors) {
		    $p_perso->rec_fields_perso($expl_id);
		}

		$serialcirc_diff=new serialcirc_diff(0,$abt_id);
		if(count($serialcirc_diff->diffusion)){ //est-ce qu'il y a des destinataires ?
			// Si c'est à faire circuler
			if($serialcirc_diff->id){ 
				$serialcirc_diff->add_circ_expl($expl_id);
				$start_diff_id = $serialcirc_diff->get_start_diff_id();
				$serialcir_print="<br/><input class='bouton' type='button' onclick='serialcirc_print_list_circ($expl_id,".$start_diff_id.");return false;' value='".$msg['serialcirc_circ_list_bull_circulation_imprimer_bt']."'>";
			}elseif ($pmb_serialcirc_subst){			
				$print_cote="<img src='".get_url_icon('print.gif')."' alt='Imprimer...' title='Imprimer...' class='align_middle' style='border:0px; padding-left:7px' onclick='imprime_cote($expl_id);return false;'	>";
			}
		}
	
	}
	//Mis à jour du bulletin avec les valeurs du formulaire	-> Si il existe on ne modifie pas les info, Si il n'existait pas il a été créé précédemment.
	/*$requete = "UPDATE bulletins set bulletin_numero='".$bul_no."',date_date='".$date_date."', mention_date='".$bul_date."', bulletin_titre='".$bul_titre."' WHERE bulletin_id='$bul_id' ";
	$myQuery = pmb_mysql_query($requete);*/
	
	// Mise a jour de la table notices_mots_global_index pour toutes les notices en relation avec l'exemplaire
	exemplaire::majNoticesMotsGlobalIndex($expl_id);
	
	// Déclaration du bulletin comme reçu
	$requete="update abts_grille_abt set state='2' where id_bull= '$id_bull' ";	
	pmb_mysql_query($requete);
	
	if(($f_fichier["name"]!="") || trim($f_url)){	
		// Il y a un document numérique rattaché au bulletin
		$up_place=0;
		$id_rep=0;
		$path = '';
		$ck_index=0;
		if ($deflt_upload_repertoire) {
			$id_rep = $deflt_upload_repertoire;
			if($id_rep) {
				$r = new upload_folder($id_rep);
				$path = $r->repertoire_nom;
				$up_place = 1;
			}
		}	
		if ($pmb_indexation_docnum && $pmb_indexation_docnum_default) {
		    $ck_index=1;
		}
		$explnum = new explnum();	
		// Url de retour après téléchargement du document.	
		$retour ="./pointage_exemplarise.php?act=memo_doc_num&id_bull=$id_bull&bul_id=$bul_id";		
		$explnum->mise_a_jour(0, $bul_id, $f_filename, $f_url, $retour,0,0, $f_explnum_statut);	
		exit();
	}else{	
		// Pas de doc numérique, on ferme l'iframe 
		$id_form = md5(microtime());
		$templates=str_replace("!!form!!","<script type='text/javascript'>enregistre('$id_bull','$bul_id');</script>",$templates);	
	}
} else {
	// Formulaire 
	include("$include_path/templates/serials.tpl.php");
	
	abts_pointage::delete_retard($abt_id,  $value['date_date'],$numero);	
	
	if($nonrecevable) {
		$value['bul_titre'] = $msg['abonnements_bulletin_non_recevable'] ;
		$requete="update abts_grille_abt set state='3' where id_bull= '$id_bull' ";	
		pmb_mysql_query($requete);		
		abts_pointage::delete_retard($abt_id);
		$templates=str_replace("!!form!!","<script type='text/javascript'>parent.kill_frame_periodique();</script>",$templates);//Il ne faut pas utiliser la fonction Fermer() pour pouvoir recevoir un bulletin que l'on aurai coché "Non recevable" par erreur
		print $templates;
		exit();
	}
	$expl = new stdClass();
	$expl->date_date =$value['date_date'];
	$expl->bul_date = $libelle_periode; 
	$expl->bul_no = stripslashes($numero);
		
	//Récupération des infos du bulletin pour les proposer sur la frame
	$bul_id = 0;
	$requete = "SELECT * FROM bulletins where bulletin_numero='$numero' and bulletin_notice='$serial_id' and date_date='".$value['date_date']."'";
	$bull_Query = pmb_mysql_query($requete);
	if(pmb_mysql_num_rows($bull_Query)) {	
		$bull = pmb_mysql_fetch_object($bull_Query);
		$bul_id= $bull->bulletin_id;
		$expl->date_date = $bull->date_date;
		$expl->bul_date = $bull->mention_date;
		$expl->bul_titre = $bull->bulletin_titre;
	}	
	if($flag_exemp_auto==1)	{
		//Génération automatique de code barre, activé pour cet abonnement
  		$requete="DELETE from exemplaires_temp where sess not in (select SESSID from sessions)";
   		pmb_mysql_query($requete); 	
    	//Appel à la fonction de génération automatique de cb
    	$code_exemplaire =init_gen_code_exemplaire(0,$bul_id);
    	do {
    		$code_exemplaire = gen_code_exemplaire(0,$bul_id,$code_exemplaire);
    		$requete="select expl_cb from exemplaires WHERE expl_cb='$code_exemplaire'";
    		$res0 = pmb_mysql_query($requete);
    		$requete="select cb from exemplaires_temp WHERE cb='$code_exemplaire' AND sess <>'".SESSid."'";
    		$res1 = pmb_mysql_query($requete);
    	} while((pmb_mysql_num_rows($res0)||pmb_mysql_num_rows($res1)));
    		
   		//Memorise dans temps le cb et la session pour le cas de multi utilisateur session
   		$requete="INSERT INTO exemplaires_temp (cb ,sess) VALUES ('$code_exemplaire','".SESSid."')";
   		pmb_mysql_query($requete);
		$expl->expl_cb=$code_exemplaire;	
		//Focus sur le bouton 'Enregistre'
		$expl->focus="<script type='text/javascript' >document.forms[\"expl\"].bouton_enregistre.focus();</script>";
	} else {
		//Focus sur le l'input de saisie de code barre 
		$expl->focus="<script type='text/javascript' >document.forms[\"expl\"].f_ex_cb.focus();</script>";
	}
	$bull_form="";				
	$perio = new serial_display($myBulletinage->get_serial()->id, 1);
	$perio_header =  $perio->header;
	
	print pmb_bidi("<div class='row'><h2>".$perio_header.'</h2></div>');
	if($abt_numeric){
		$expl->abt_numeric=1;
	}else {
		// c'est un abonnement qui n'est pas exclusivement numérique. On crée le formulaire de l'exemplaire de bulletin
		$expl->abt_numeric=0;

		$requete = "SELECT * FROM abts_abts WHERE abt_id='$abt_id'";
		$abtsQuery = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($abtsQuery)) {
			$abts = pmb_mysql_fetch_object($abtsQuery);
			$expl->expl_cote = $abts->cote;
			$expl->expl_location = $abts->location_id;
			$expl->expl_section = $abts->section_id;
			$expl->expl_codestat = $abts->codestat_id;
			$expl->expl_typdoc = $abts->typdoc_id;
			$expl->expl_statut = $abts->statut_id;
			$expl->expl_owner = $abts->lender_id;
			$expl->expl_prix = $abts->prix;
			$expl->type_antivol = $abts->type_antivol;
			$expl->destinataire = $abts->destinataire;
		}				
		// sélection de la cote dewey de la notice chapeau pour pré-renseignement de la cote en création expl
		$query_cote = "select indexint_name from indexint, notices, bulletins where bulletin_id='$bul_id' and bulletin_notice=notice_id and notices.indexint=indexint.indexint_id ";
		$myQuery_cote = pmb_mysql_query($query_cote );
		if(pmb_mysql_num_rows($myQuery_cote)) {
			$pre_cote = pmb_mysql_fetch_object($myQuery_cote);
			$expl->expl_cote = $pre_cote->indexint_name ;
		}	
	}	
	$bull_form.= bul_do_form($expl);
	$templates=str_replace("!!form!!",$bull_form,$templates);	
}
if(!isset($serialcir_print)) $serialcir_print = '';
if(!isset($print_cote)) $print_cote = '';
$templates=str_replace("!!serialcir_print!!",$serialcir_print,$templates);
$templates=str_replace("!!print_cote!!",$print_cote,$templates);

print $templates;
?>