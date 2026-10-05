<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_ascodocpsy.inc.php,v 1.31.2.1.2.3 2025/04/28 14:27:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//Globalisation nécessaire pour différentes inclusions de ce fichier
global $class_path, $include_path, $current_module, $msg;
global $deflt_notice_is_new, $book_lender_id, $book_statut_id, $deflt_docs_location;
global $pmb_numero_exemplaire_auto_script;

// DEBUT paramétrage propre à la base de données d'importation :
require_once($class_path."/serials.class.php");
//require_once($class_path."/categories.class.php");
require_once($class_path."/noeuds.class.php");
$link_generate=0;//L'import des liens n'est pas compatible avec cette fonction
$isbn_dedoublonnage=0;//Pas de dédoublonage sur l'isbn
$_SESSION["encodage_fic_source"]="";
// templates

$tpl_beforeupload_expl = "
                <form class='form-$current_module' ENCTYPE=\"multipart/form-data\" METHOD=\"post\" ACTION=\"iimport_expl.php\">
                <h3>".$msg['import_expl_form_titre']."</h3>
                <INPUT TYPE='hidden' NAME='isbn_mandatory' id='io0' VALUE='0' />
                <INPUT TYPE='hidden' NAME='isbn_dedoublonnage' id='di0' VALUE='0' />
                <input type='hidden' name='isbn_only' id='ionly' value='0' />
                <input type='hidden' name='link_generate' id='link0' value='0' />
                <input type='hidden' name='authorities_notices' id='authorities_notices0' value='0' />
                <input type='hidden' name='authorities_origin' id='authorities_origin' value='1' />
                <input type='hidden' name='cote_mandatory' id='cm0' value='0' />
                <input type='hidden' name='tdoc_codage' id='td0' value='0' />
                <input type='hidden' name='statisdoc_codage' id='sd0' value='0' />
                <input type='hidden' name='sdoc_codage' id='sdc0' value='0' />

                <div class='form-contenu'>
                    <div class='row'>
                        <div class='colonne2'>
                    		<label class='etiquette' for='statutnot'>$msg[import_statutnot]</label>
                    		<div>
                    		".gen_liste_multiple ("select id_notice_statut, gestion_libelle from notice_statut order by 2", "id_notice_statut", "gestion_libelle", "id_notice_statut", "statutnot", "", 1, "", "","","",0)."
                    		</div>
                    	</div>
                    	<div class='colonne-suite'>
                    		&nbsp;
                    	</div>
                    </div>
                   <div class='row'>&nbsp;</div>
                   <!--    Nouveauté    -->
                   	<div class='row'>
					    <label for='notice_is_new' class='etiquette'>".$msg["notice_is_new_gestion"]."</label>
					</div>
					<div class='row'>
					    <input type='radio' name='notice_is_new' id='notice_is_not_new' ".(!isset($deflt_notice_is_new) || !$deflt_notice_is_new ? "checked='checked'" : "")." value='0'><label for='f_notice_is_not_new'>".$msg["notice_is_new_gestion_no"]."</label>
					    <input type='radio' name='notice_is_new' id='notice_is_new' ".(isset($deflt_notice_is_new) || $deflt_notice_is_new ? "checked='checked'" : "")." value='1'><label for='f_notice_is_new'>".$msg["notice_is_new_gestion_yes"]."</label>
					</div>
                    <div class='row'><hr /></div>
					<div class='row'>
                        <label class='etiquette' for='preteur statut'>$msg[560]</label>
                    </div>
                    <div class='row'>".
                        lender::gen_combo_box($book_lender_id)."&nbsp;&nbsp;".
                        docs_statut::gen_combo_box($book_statut_id)."
                    </div>
                    <div class='row'>
                        <label class='etiquette' for='localisation'>$msg[import_localisation]</label>
                    </div>
                    <div class='row'>".
                        docs_location::gen_combo_box($deflt_docs_location)."
                    </div>
                    <div class='row'><hr /></div>
                    <div class='row'>
                        <label class='etiquette' for='txt_suite'>$msg[501]</label>
                        </div>
                    <div class='row'>
                        <INPUT NAME='userfile' class='saisie-80em' TYPE='file' size='60' />
                        <INPUT NAME=\"categ\" TYPE=\"hidden\" value=\"import\" />
                        <INPUT NAME=\"sub\" TYPE=\"hidden\" value=\"import_expl\" />
                        <INPUT NAME=\"action\" TYPE=\"hidden\" value=\"afterupload\" />
                    </div>
                    </div>
                <INPUT TYPE='SUBMIT' class='bouton' NAME='upload' VALUE='".$msg[502]."' />
                </FORM>";

$tpl_beforeupload_notices = "
                    <form class='form-$current_module' ENCTYPE='multipart/form-data' METHOD='post' ACTION='iimport_expl.php' />
                    <h1>ATTENTION: Aucun exemplaire sera cr&eacute;&eacute;. Pour l'import des exemplaires merci de choisir l'autre menu</h1>
                    <h3>".$msg['import_noti_form_titre']."</h3>
                    <INPUT TYPE='hidden' NAME='isbn_mandatory' id='io0' VALUE='0' />
		    <INPUT TYPE='hidden' NAME='isbn_dedoublonnage' id='di0' VALUE='0' />
		    <input type='hidden' name='isbn_only' id='ionly' value='0' />
		    <input type='hidden' name='link_generate' id='link0' value='0' />
		    <input type='hidden' name='authorities_notices' id='authorities_notices0' value='0' />
		    <input type='hidden' name='authorities_origin' id='authorities_origin' value='1' />
                    <div class='form-contenu'>
					<div class='row'>
						<div class='colonne2'>
							<label class='etiquette' for='statutnot'>$msg[import_statutnot]</label>
							<div>
								".gen_liste_multiple ("select id_notice_statut, gestion_libelle from notice_statut order by 2", "id_notice_statut", "gestion_libelle", "id_notice_statut", "statutnot", "", 1, "", "","","",0)."
							</div>
						</div>
						<div class='colonne-suite'>
							&nbsp;
						</div>
                   	</div>
					<!--    Nouveauté    -->
                   	<div class='row'>
					    <label for='notice_is_new' class='etiquette'>".$msg["notice_is_new_gestion"]."</label>
					</div>
					<div class='row'>
					    <input type='radio' name='notice_is_new' id='notice_is_not_new' ".(!isset($deflt_notice_is_new) || !$deflt_notice_is_new ? "checked='checked'" : "")." value='0'><label for='f_notice_is_not_new'>".$msg["notice_is_new_gestion_no"]."</label>
					    <input type='radio' name='notice_is_new' id='notice_is_new' ".(isset($deflt_notice_is_new) || $deflt_notice_is_new ? "checked='checked'" : "")." value='1'><label for='f_notice_is_new'>".$msg["notice_is_new_gestion_yes"]."</label>
					</div>
	                <div class='row'>&nbsp;</div>
                        <div class='row'>
                            <label class='etiquette' for='txt_suite'>$msg[501]</label>
                            </div>
                        <div class='row'>
                            <INPUT NAME='userfile' class='saisie-80em' TYPE='file' size='60' />
                            <INPUT NAME='categ' TYPE='hidden' value='import' />
                            <INPUT NAME='sub' TYPE='hidden' value='import' />
                            <INPUT NAME='action' TYPE='hidden' value='afterupload' />
                            </div>
                        </div>
                    <INPUT TYPE='SUBMIT' class='bouton' NAME='upload' VALUE='".$msg[502]."' />
                    </FORM>";


if ($pmb_numero_exemplaire_auto_script && file_exists($include_path."/$pmb_numero_exemplaire_auto_script")) {
	require_once($include_path."/".$pmb_numero_exemplaire_auto_script);
}else{
	require_once($include_path."/gen_code/gen_code_exemplaire.php");
}

function recup_noticeunimarc_suite($notice) {
	global $info_461,$info_463		;
	global $info_900,$info_901,$info_902,$info_903,$info_904,$info_905,$info_906;
	global $info_907,$info_908,$info_909,$info_910,$info_911,$info_912,$info_913,$info_914;
	global $info_606_a;
	global $info_896;
	global $bl,$hl,$serie;

	$info_461="";
	$info_463="";
	$info_900="";
	$info_901="";
	$info_902="";
	$info_903="";
	$info_904="";
	$info_905="";
	$info_906="";
	$info_907="";
	$info_908="";
	$info_909="";
	$info_910="";
	$info_911="";
	$info_912="";
	$info_913="";
	$info_914="";
	$info_896="";

	$record = new iso2709_record($notice, AUTO_UPDATE);

	$bl=$record->inner_guide['bl'];
	$hl=$record->inner_guide['hl'];
	if (($bl == "a" && $hl == "2") || ($bl == "s" && $hl == "1")){
		$serie=array();
	}

	$info_461=$record->get_subfield("461","t","v");
	$info_463=$record->get_subfield("463","t","v");

	$info_606_a=$record->get_subfield_array_array("606","a");
	$info_900=$record->get_subfield_array_array("900","a");
	$info_901=$record->get_subfield_array_array("901","a");
	$info_902=$record->get_subfield_array_array("902","a");
	$info_903=$record->get_subfield_array_array("903","a");
	$info_904=$record->get_subfield("904","a");
	$info_905=$record->get_subfield_array_array("905","a");
	$info_906=$record->get_subfield_array_array("906","a");
	$info_907=$record->get_subfield_array_array("907","a");
	$info_908=$record->get_subfield("908","a");
	$info_909=$record->get_subfield("909","a");
	$info_910=$record->get_subfield("910","a");
	$info_911=$record->get_subfield("911","a");
	$info_912=$record->get_subfield("912","a");
	$info_913=$record->get_subfield("913","a","b");
	$info_914=$record->get_subfield("914","a");
	$info_896=$record->get_subfield("896", 'a');

} // fin recup_noticeunimarc_suite

function import_new_notice_suite() {
	global $notice_id,$statutnot,$notice_is_new,$cree_expl_asco ;
	global $charset;

	global $info_461, $info_463,$editeur_date ;
	global $info_606_a;
	global $info_900,$info_901,$info_902,$info_903,$info_904,$info_905,$info_906;
	global $info_907,$info_908,$info_909,$info_910,$info_911,$info_912,$info_913,$info_914;
	global $info_896;

	global $bl,$hl;
	$notices_crees=array();
	$cree_expl_asco=true;
	$doublon=false;
	//cas d'un article
	if ($bl == "a" && $hl == "2"){
		$cree_expl_asco=false;
		$bulletin = array(
			//'num' => (clean_string($info_461[0]["v"]) ? 'vol '.clean_string($info_461[0]["v"]).' ' : '').'n°'.clean_string($info_463[0]["v"])
			'num' => clean_string($info_463[0]["v"]).(clean_string($info_461[0]["v"]) ? ' vol '.clean_string($info_461[0]["v"]) : ''),
			'date' => clean_string($editeur_date[0])
		);
		$perio = array(
			'titre' => $info_461[0]['t'],
			//'volume' => $info_461[0]['v']
		);
		notice_to_article($perio,$bulletin);
	} elseif($bl == "s" && $hl == "1"){
		$cree_expl_asco=false;
		update_notice("s", "1");
	}

	//Dédoublonnage
	if($bl == "a" && $hl == "2"){
		$requete="SELECT n1.notice_id, n1.tit1 from notices n1 JOIN analysis a1 ON n1.notice_id=a1.analysis_notice JOIN notices n2 ON n1.tit1=n2.tit1 JOIN analysis a2 ON n2.notice_id=a2.analysis_notice  where n1.notice_id!='".$notice_id."' and n2.notice_id='".$notice_id."' AND  a1.analysis_bulletin=a2.analysis_bulletin ORDER by n1.notice_id DESC LIMIT 1";
	}elseif($bl == "s" && $hl == "1"){
		$requete="select n1.notice_id, n1.tit1 from notices n1 JOIN notices n2 ON n1.tit1=n2.tit1 AND n1.typdoc=n2.typdoc AND n1.niveau_biblio=n2.niveau_biblio AND n1.niveau_hierar=n2.niveau_hierar  where n1.notice_id!='".$notice_id."' and n2.notice_id='".$notice_id."' ORDER by n1.notice_id DESC LIMIT 1";
	}else{
		$requete="select n1.notice_id, n1.tit1 from notices n1 JOIN notices n2 ON n1.tit1=n2.tit1 AND n1.typdoc=n2.typdoc AND n1.code=n2.code AND n1.year=n2.year AND n1.niveau_biblio=n2.niveau_biblio AND n1.niveau_hierar=n2.niveau_hierar  where n1.notice_id!='".$notice_id."' and n2.notice_id='".$notice_id."' ORDER by n1.notice_id DESC LIMIT 1";
	}
	$res=pmb_mysql_query($requete);
	if(pmb_mysql_num_rows($res)){
		$id_notice_base=pmb_mysql_result($res,0,0);
		$titre_notice_base=pmb_mysql_result($res,0,1);
		//On supprime dans le cas de la série
		$ma_notice = new serial($notice_id);
		$ma_notice -> serial_delete();
		//On s'assure d'avoir tout supprimé
		notice::del_notice($notice_id);
		$notice_id=$id_notice_base;
		$mon_msg=" Nombre de notice d&eacute;j&agrave; existantes";
		pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_".addslashes(SESSid).".inc', '".addslashes($mon_msg)."') ") ;
		$mon_msg="La notice de la base avec l'identifiant ".$notice_id." a &eacute;t&eacute; utilis&eacute;e pour celle de l'import avec le titre: ".$titre_notice_base;
		pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_".addslashes(SESSid).".inc', '".addslashes($mon_msg)."') ") ;
		$doublon=true;
	}

	$requete="update notices set statut='".$statutnot."' where notice_id='".$notice_id."' ";
	pmb_mysql_query($requete);

	$notice_is_new = intval($notice_is_new);
	$requete="update notices set notice_is_new='".$notice_is_new."' where notice_id='".$notice_id."' ";
	pmb_mysql_query($requete);

	global $ordre_categ;
	$ordre_categ=0;
	//Branche MOTCLE du Thésaurus DOC
	do_thesaurus_ascodocpsy(3, "MOTCLE", $info_606_a);

	//Branche CANDES du Thésaurus DOC
	if (is_countable($info_900) && count($info_900)) {
		do_thesaurus_ascodocpsy(3, "CANDES", $info_900);
	}

	//Branche THEME du Thésaurus DOC
	if (is_countable($info_901) && count($info_901)) {
		do_thesaurus_ascodocpsy(3, "THEME", $info_901);
	}

	//Branche NOMP du Thésaurus DOC
	if (is_countable($info_902) && count($info_902)) {
		do_thesaurus_ascodocpsy(3, "NOMP", $info_902);
	}

	//Producteur de la fiche
	$res=pmb_mysql_query("select idchamp from notices_custom where name='cp_prodfich'");
	if (!empty($info_903) && count($info_903) && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		$requete="select max(notices_custom_list_value*1) from notices_custom_lists where notices_custom_champ=".$cp_id;
		$resultat=pmb_mysql_query($requete);
		$max=pmb_mysql_result($resultat,0,0);
		$n=$max+1;
		for ($i=0; $i<count($info_903); $i++) {
			for ($j=0; $j<count($info_903[$i]); $j++) {
				$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib='".addslashes($info_903[$i][$j])."' and notices_custom_champ=".$cp_id;
				$resultat=pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($resultat)) {
					$value=pmb_mysql_result($resultat,0,0);
				} else {
					$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib REGEXP '^".addslashes($info_903[$i][$j])."[ \-]+' and notices_custom_champ=".$cp_id;
					$resultat=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($resultat)) {
						$value=pmb_mysql_result($resultat,0,0);
					} else {
						$requete="insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) values($cp_id,$n,'".addslashes($info_903[$i][$j])."')";
						pmb_mysql_query($requete);
						$value=$n;
						$n++;
					}
				}
				$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_integer) values($cp_id,$notice_id,$value)";
				pmb_mysql_query($requete);
			}
		}
	}

	//DIPSPE
	$res=pmb_mysql_query("select idchamp,datatype from notices_custom where name='cp_dipspe'");
	if (!empty($info_904[0]) && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		$datatype = pmb_mysql_result($res,0,1);
		$type_champ="";
		if($datatype == "small_text"){
			$type_champ="notices_custom_small_text";
		}elseif($datatype == "text"){
			$type_champ="notices_custom_text";
		}
		if($type_champ){
			$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,".$type_champ.") values($cp_id,$notice_id,'".addslashes($info_904[0])."')";
			pmb_mysql_query($requete);
		}
	}

	//Annexe
	$res=pmb_mysql_query("select idchamp,datatype from notices_custom where name='cp_annexe'");
	if (!empty($info_905) && is_countable($info_905) && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		$datatype = pmb_mysql_result($res,0,1);
		$type_champ="";
		if($datatype == "small_text"){
			$type_champ="notices_custom_small_text";
		}elseif($datatype == "text"){
			$type_champ="notices_custom_text";
		}
		if($type_champ){
			for ($i=0; $i<count($info_905); $i++) {
				for ($j=0; $j<count($info_905[$i]); $j++) {
					$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,".$type_champ.") values($cp_id,$notice_id,'".addslashes($info_905[$i][$j])."')";
					pmb_mysql_query($requete);
				}
			}
		}
	}

	//Lien annexe
	$res=pmb_mysql_query("select idchamp,datatype from notices_custom where name='cp_lienanne'");
	if (!empty($info_906) && is_countable($info_906) && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		$datatype = pmb_mysql_result($res,0,1);
		$type_champ="";
		if($datatype == "small_text"){
			$type_champ="notices_custom_small_text";
		}elseif($datatype == "text"){
			$type_champ="notices_custom_text";
		}
		if($type_champ){
			for ($i=0; $i<count($info_906); $i++) {
				for ($j=0; $j<count($info_906[$i]); $j++) {
					$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,".$type_champ.") values($cp_id,$notice_id,'".addslashes($info_906[$i][$j])."')";
					pmb_mysql_query($requete);
				}
			}
		}
	}

	//Localisation
	$res=pmb_mysql_query("select idchamp from notices_custom where name='cp_loc'");
	if (!empty($info_907) && is_countable($info_907) && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		$requete="select max(notices_custom_list_value*1) from notices_custom_lists where notices_custom_champ=".$cp_id;
		$resultat=pmb_mysql_query($requete);
		$max=@pmb_mysql_result($resultat,0,0);
		$n=$max+1;
		for ($i=0; $i<count($info_907); $i++) {
			for ($j=0; $j<count($info_907[$i]); $j++) {
				$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib='".addslashes($info_907[$i][$j])."' and notices_custom_champ=".$cp_id;
				$resultat=pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($resultat)) {
					$value=pmb_mysql_result($resultat,0,0);
				} else {
					$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib REGEXP '^".addslashes($info_907[$i][$j])."[ \-]+' and notices_custom_champ=".$cp_id;
					$resultat=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($resultat)) {
						$value=pmb_mysql_result($resultat,0,0);
					} else {
						$requete="insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) values($cp_id,$n,'".addslashes($info_907[$i][$j])."')";
						pmb_mysql_query($requete);
						$value=$n;
						$n++;
					}
				}
				$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_integer) values($cp_id,$notice_id,$value)";
				pmb_mysql_query($requete);
			}
		}
	}

	//Nature du texte
	$res=pmb_mysql_query("select idchamp,datatype from notices_custom where name='cp_nattext'");
	if (!empty($info_908[0]) && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		$datatype = pmb_mysql_result($res,0,1);
		if($datatype == "small_text"){
			$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_small_text) values($cp_id,$notice_id,'".addslashes($info_908[0])."')";
			pmb_mysql_query($requete);
		}elseif($datatype == "integer"){//C'est une list
			$requete="select max(notices_custom_list_value*1) from notices_custom_lists where notices_custom_champ=".$cp_id;
			$resultat=pmb_mysql_query($requete);
			$max=@pmb_mysql_result($resultat,0,0);
			$n=$max+1;

			$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib='".addslashes($info_908[0])."' and notices_custom_champ=".$cp_id;
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				$value=pmb_mysql_result($resultat,0,0);
			} else {
				$requete="insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) values($cp_id,$n,'".addslashes($info_908[0])."')";
				pmb_mysql_query($requete);
				$value=$n;
				$n++;
			}
			$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_integer) values($cp_id,$notice_id,$value)";
			pmb_mysql_query($requete);
		}
	}

	//Date du texte
	$res=pmb_mysql_query("select idchamp from notices_custom where name='cp_datetext'");
	if (!empty($info_909[0]) && $res && pmb_mysql_num_rows($res)) {
		$ma_date=trim($info_909[0]);
		if(strlen($ma_date) == 4){
			$ma_date=addslashes($ma_date."-01-01");
		}elseif(preg_match("#^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$#",$ma_date)){
			//Le format est bon RAS
		}elseif(preg_match("#^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})$#",$ma_date,$matches)){
			$ma_date=addslashes($matches[3]."-".$matches[2]."-".$matches[1]);
		}elseif(preg_match("#([0-9]{4})#",$ma_date,$matches)){
			$ma_date=addslashes($matches[1]."-01-01");
		}
		$cp_id = pmb_mysql_result($res,0,0);
		$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_date) values($cp_id,$notice_id,'".$ma_date."')";
		pmb_mysql_query($requete);
	}

	//Numéro du texte officiel
	$res=pmb_mysql_query("select idchamp from notices_custom where name='cp_numtexof'");
	if (!empty($info_910[0]) && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_small_text) values($cp_id,$notice_id,'".addslashes($info_910[0])."')";
		pmb_mysql_query($requete);
	}

	//Date de fin de validité
	$res=pmb_mysql_query("select idchamp from notices_custom where name='cp_datevali'");
	if (!empty($info_911[0]) && $res && pmb_mysql_num_rows($res)) {
		$ma_date=trim($info_911[0]);
		if(strlen($ma_date) == 4){
			$ma_date=addslashes($ma_date."-01-01");
		}elseif(preg_match("#^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$#",$ma_date)){
			//Le format est bon RAS
		}elseif(preg_match("#^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})$#",$ma_date,$matches)){
			$ma_date=addslashes($matches[3]."-".$matches[2]."-".$matches[1]);
		}elseif(preg_match("#([0-9]{4})#",$ma_date,$matches)){
			$ma_date=addslashes($matches[1]."-01-01");
		}
		$cp_id = pmb_mysql_result($res,0,0);
		$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_date) values($cp_id,$notice_id,'".$ma_date."')";
		pmb_mysql_query($requete);
	}

	//Etat des collections
	$res=pmb_mysql_query("select idchamp,datatype from notices_custom where name='cp_etatcol'");
	if (isset($info_913[0]["a"]) && $info_913[0]["a"]) {
		if($res && pmb_mysql_num_rows($res)){
			$cp_id = pmb_mysql_result($res,0,0);
			$datatype = pmb_mysql_result($res,0,1);
			$type_champ="";
			if($datatype == "small_text"){
				$type_champ="notices_custom_small_text";
			}elseif($datatype == "text"){
				$type_champ="notices_custom_text";
			}
			if($type_champ){
				$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,".$type_champ.") values($cp_id,$notice_id,'".addslashes($info_913[0]["a"])."')";
				pmb_mysql_query($requete);
			}
		}
		$matches_all=array();

		if (!empty($info_913[0]["b"])){
			if(preg_match("/papier/",$info_913[0]["b"])){
				$support_perio="Papier";
			}else{
				$support_perio=(($charset == "utf-8")?encoding_normalize::utf8_normalize("électronique"):"électronique");
			}

			$requete="SELECT archtype_id FROM arch_type WHERE archtype_libelle='".addslashes($info_913[0]["b"])."'";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)){
				$id_support=pmb_mysql_result($res,0,0);
			}else{
				$requete="INSERT INTO arch_type(archtype_libelle) VALUES('".addslashes($info_913[0]["b"])."')";
				if(pmb_mysql_query($requete)){
					$id_support=pmb_mysql_insert_id();
				}else{
					$id_support=1;//Ne doit pas passer par là
				}
			}

			$tmp1=explode("/",$info_913[0]["a"]);
			if(count($tmp1)){
				foreach($tmp1 as $decoupe1){
					$loc=$collection="";
					$tmp2=explode(":",$decoupe1);
					if(count($tmp2) == 1){
						$loc=trim($decoupe1);
					}elseif(count($tmp2)){
						$loc=trim(array_shift($tmp2));
						$collection=implode(":",$tmp2);
					}
					if($loc){
						$requete="SELECT archempla_id FROM arch_emplacement WHERE archempla_libelle LIKE '".addslashes($loc)."%'";
						$res=pmb_mysql_query($requete);
						if(pmb_mysql_num_rows($res)){
							$id_empl=pmb_mysql_result($res,0,0);
						}else{
							$requete="SELECT archempla_id FROM arch_emplacement WHERE archempla_libelle LIKE 'asco".addslashes(str_pad($loc,3,"0",STR_PAD_LEFT))."%'";
							$res=pmb_mysql_query($requete);
							if(pmb_mysql_num_rows($res)){
								$id_empl=pmb_mysql_result($res,0,0);
							}else{
								if(preg_match("/^[0-9]+$/",trim($loc))){
									$emplacement="asco".str_pad($loc,3,"0",STR_PAD_LEFT);
								}else{
									$emplacement=$loc;
								}
								$requete="INSERT INTO arch_emplacement(archempla_libelle) VALUES('".addslashes($emplacement)." - ')";
								if(pmb_mysql_query($requete)){
									$id_empl=pmb_mysql_insert_id();
								}else{
									$id_empl=2;//Ne doit pas passer par là
								}
							}
						}

						$req="SELECT collstate_id FROM collections_state WHERE id_serial='".$notice_id."' AND collstate_emplacement='".$id_empl."' AND collstate_type='".$id_support."' LIMIT 1";
						$res=pmb_mysql_query($req);
						if($res && pmb_mysql_num_rows($res)){
							$req="UPDATE collections_state SET state_collections='".addslashes($collection)."' WHERE collstate_id='".pmb_mysql_result($res,0,0)."'";
							pmb_mysql_query($req);
						}else{
							$requete="INSERT INTO collections_state(id_serial,location_id,state_collections,collstate_emplacement,collstate_type,collstate_statut) VALUES('".$notice_id."','1','".addslashes($collection)."','".$id_empl."','".$id_support."','1')";
							pmb_mysql_query($requete);
						}
					}
				}
			}
		}
		/*if(preg_match_all("#([0-9]+) : (.+?)([/]|$)#i",$info_913[0]["a"],$matches_all) && $info_913[0]["b"]){
			$requete="SELECT archtype_id FROM arch_type WHERE archtype_libelle='".addslashes($info_913[0]["b"])."'";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)){
				$id_support=pmb_mysql_result($res,0,0);
			}else{
				$requete="INSERT INTO arch_type(archtype_libelle) VALUES('".addslashes($info_913[0]["b"])."')";
				if(pmb_mysql_query($requete)){
					$id_support=pmb_mysql_insert_id();
				}else{
					$id_support=1;//Ne doit pas passer par là
				}
			}

			foreach($matches_all[1] as $key => $val){
				$loc=trim($val);
				$collection=trim($matches_all[2][$key]);
				if($loc && $collection){
					$requete="SELECT archempla_id FROM arch_emplacement WHERE archempla_libelle LIKE 'asco".addslashes(str_pad($loc,3,"0",STR_PAD_LEFT))."%'";
					$res=pmb_mysql_query($requete);
					if(pmb_mysql_num_rows($res)){
						$id_empl=pmb_mysql_result($res,0,0);
					}else{
						$requete="INSERT INTO arch_emplacement(archempla_libelle) VALUES('asco".addslashes(str_pad($loc,3,"0",STR_PAD_LEFT))."')";
						if(pmb_mysql_query($requete)){
							$id_empl=pmb_mysql_insert_id();
						}else{
							$id_empl=2;//Ne doit pas passer par là
						}
					}

					$req="SELECT collstate_id FROM collections_state WHERE id_serial='".$notice_id."' AND collstate_emplacement='".$id_empl."' AND collstate_type='".$id_support."' LIMIT 1";
					$res=pmb_mysql_query($req);
					if($res && pmb_mysql_num_rows($res)){
						$req="UPDATE collections_state SET state_collections='".addslashes($collection)."' WHERE collstate_id='".pmb_mysql_result($res,0,0)."'";
						pmb_mysql_query($req);
					}else{
						$requete="INSERT INTO collections_state(id_serial,location_id,state_collections,collstate_emplacement,collstate_type,collstate_statut) VALUES('".$notice_id."','1','".addslashes($collection)."','".$id_empl."','".$id_support."','1')";
						pmb_mysql_query($requete);
					}
				}
			}
		}*/
	}

	//Support de la fiche
	$res=pmb_mysql_query("select idchamp from notices_custom where name='cp_support'");
	if (!empty($info_914[0]) && $res && pmb_mysql_num_rows($res)) {
		$cp_id = pmb_mysql_result($res,0,0);
		$requete="select max(notices_custom_list_value*1) from notices_custom_lists where notices_custom_champ=".$cp_id;
		$resultat=pmb_mysql_query($requete);
		$max=@pmb_mysql_result($resultat,0,0);
		$n=$max+1;

		$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib='".addslashes($info_914[0])."' and notices_custom_champ=".$cp_id;
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$value=pmb_mysql_result($resultat,0,0);
		} else {
			$requete="insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) values($cp_id,$n,'".addslashes($info_914[0])."')";
			pmb_mysql_query($requete);
			$value=$n;
			$n++;
		}
		$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_integer) values($cp_id,$notice_id,$value)";
		pmb_mysql_query($requete);

	}

	if($doublon){
		//On enlève les valeurs en double dans les CP
		$requete="SELECT *, COUNT(`notices_custom_origine`) FROM `notices_custom_values` WHERE notices_custom_origine='".$notice_id."' GROUP BY `notices_custom_champ`, `notices_custom_origine`, `notices_custom_small_text`, `notices_custom_text`, `notices_custom_integer`, `notices_custom_date`, `notices_custom_float` HAVING COUNT(`notices_custom_origine`) > 1 ORDER BY COUNT(`notices_custom_origine`) DESC";
		$res=pmb_mysql_query($requete);
		while ( $ligne=pmb_mysql_fetch_object($res) ) {
			//echo "Info : ".$ligne->notices_custom_champ.", ".$ligne->notices_custom_origine.", ".$ligne->notices_custom_small_text.", ".$ligne->notices_custom_integer."\n";
			$requete="DELETE FROM notices_custom_values WHERE notices_custom_champ='".$ligne->notices_custom_champ."' AND notices_custom_origine='".$ligne->notices_custom_origine."'";
			if($ligne->notices_custom_small_text){
				$requete.=" AND notices_custom_small_text='".addslashes($ligne->notices_custom_small_text)."'";
			}
			if($ligne->notices_custom_integer){
				$requete.=" AND notices_custom_integer='".addslashes($ligne->notices_custom_integer)."'";
			}
			if($ligne->notices_custom_text){
				$requete.=" AND notices_custom_text='".addslashes($ligne->notices_custom_text)."'";
			}
			if($ligne->notices_custom_date){
				$requete.=" AND notices_custom_date='".addslashes($ligne->notices_custom_date)."'";
			}
			if($ligne->notices_custom_float){
				$requete.=" AND notices_custom_float='".addslashes($ligne->notices_custom_float)."'";
			}
			pmb_mysql_query($requete);

			$requete="INSERT INTO notices_custom_values(notices_custom_champ,notices_custom_origine,notices_custom_small_text,notices_custom_integer,notices_custom_text,notices_custom_date,notices_custom_float) VALUES ('".$ligne->notices_custom_champ."', '".$ligne->notices_custom_origine."'";
			if($ligne->notices_custom_small_text){
				$requete.=", '".addslashes($ligne->notices_custom_small_text)."'";
			}else{
				$requete.=", NULL";
			}
			if($ligne->notices_custom_integer){
				$requete.=", '".$ligne->notices_custom_integer."'";
			}else{
				$requete.=", NULL";
			}
			if($ligne->notices_custom_text){
				$requete.=", '".addslashes($ligne->notices_custom_text)."'";
			}else{
				$requete.=", NULL";
			}
			if($ligne->notices_custom_date){
				$requete.=", '".addslashes($ligne->notices_custom_date)."'";
			}else{
				$requete.=", NULL";
			}
			if($ligne->notices_custom_float){
				$requete.=", '".addslashes($ligne->notices_custom_float)."')";
			}else{
				$requete.=", NULL)";
			}
			pmb_mysql_query($requete);
		}
	}
	//Gestion thumbnail
	if(is_array($info_896) && count($info_896)) {
		$rqt_maj = "UPDATE notices SET thumbnail_url='".addslashes($info_896[0])."' WHERE notice_id='$notice_id' ";
		pmb_mysql_query($rqt_maj);
	}

} // fin import_new_notice_suite

//descripteurs
function do_thesaurus_ascodocpsy($id_thesaurus, $nom_categ, $branch_values=array(), $lang='fr_FR') {
	global $notice_id,$ordre_categ;

	$num_parent=0;
	$limit_search=0;
	//Recherche du thésaurus ASCODOC
	switch ($nom_categ){
		case "MOTCLE":
			$req="SELECT id_thesaurus,num_noeud_racine FROM thesaurus WHERE libelle_thesaurus LIKE '%SANTEPSY%'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 1)){
				$id_thesaurus=pmb_mysql_result($res, 0,0);
				$num_parent=pmb_mysql_result($res, 0,1);
			}
			break;

		case "CANDES":
			$req="SELECT id_thesaurus,num_noeud FROM thesaurus JOIN categories ON id_thesaurus=num_thesaurus WHERE libelle_thesaurus LIKE '%SANTEPSY%' AND libelle_categorie LIKE 'CANDES'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 1)){
				$id_thesaurus=pmb_mysql_result($res, 0,0);
				$num_parent=pmb_mysql_result($res, 0,1);
				$limit_search=$num_parent;
			}
			break;

		case "THEME":
			$req="SELECT id_thesaurus,num_noeud_racine FROM thesaurus WHERE libelle_thesaurus LIKE '%THEMES%'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 1)){
				$id_thesaurus=pmb_mysql_result($res, 0,0);
				$num_parent=pmb_mysql_result($res, 0,1);
			}
			break;

		case "NOMP":
			$req="SELECT num_thesaurus,id_noeud FROM noeuds WHERE autorite LIKE 'NOMSPROPRES'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 1)){
				$id_thesaurus=pmb_mysql_result($res, 0,0);
				$num_parent=pmb_mysql_result($res, 0,1);
				$limit_search=$num_parent;
			}
			break;

	}

	if(!$num_parent){//ancien mode
		$res=pmb_mysql_query("select id_noeud from noeuds where autorite='TOP' and num_thesaurus='".$id_thesaurus."'");
		if($res && pmb_mysql_num_rows($res)){
			$parent_thes = pmb_mysql_result($res,0,0);
		}else{
			return;
		}
		$rqt = "select id_noeud from noeuds join categories on id_noeud=num_noeud and libelle_categorie='".$nom_categ."' and num_parent='".$parent_thes."'";
		$res = pmb_mysql_query($rqt);
		if ($res && pmb_mysql_num_rows($res)) {
			$num_parent = pmb_mysql_result($res,0,0);
			$limit_search=$num_parent;
		}
	}

	if($num_parent){
		foreach ($branch_values as $terms){
			foreach($terms as $term){
			    $categ_to_index = 0;
				$categ_id = categories::searchLibelle(addslashes($term),$id_thesaurus,$lang,$limit_search);
				if($categ_id){
					//le terme existe
					$noeud = new noeuds($categ_id);
					if($noeud->num_renvoi_voir){
						$categ_to_index = $noeud->num_renvoi_voir;
					}else{
						$categ_to_index = $categ_id;
					}
				}elseif($nom_categ != 'THEME'){
					//le terme est à créer
					$n = new noeuds();
					$n->num_thesaurus = $id_thesaurus;
					$n->num_parent = $num_parent;
					$n->save();
					$c = new categories($n->id_noeud, $lang);
					$c->libelle_categorie = $term;
					$c->save();

					$categ_to_index = $n->id_noeud;
				}
			    if ($categ_to_index){
                    $requete = "INSERT IGNORE INTO notices_categories (notcateg_notice,num_noeud,ordre_categorie) VALUES($notice_id,$categ_to_index,$ordre_categ)";
                    pmb_mysql_query($requete);
				}
				$ordre_categ++;
			}
		}
	}
}

function update_notice($bl,$hl){
	global $notice_id;
	$update =" update notices set niveau_biblio = '$bl', niveau_hierar ='$hl', tnvol='', tparent_id=0 where notice_id = $notice_id";
	pmb_mysql_query($update);
}

function notice_to_article($perio_info,$bull_info){
	global $notice_id;
	$bull_id = genere_bulletin($perio_info,$bull_info);
	update_notice("a","2");
	$insert = "insert into analysis set analysis_bulletin = $bull_id, analysis_notice = $notice_id";
	pmb_mysql_query($insert);

}

function genere_perio($perio_info){
	$search = "select notice_id from notices where tit1 LIKE '".addslashes($perio_info['titre'])."' and niveau_biblio = 's' and niveau_hierar = '1'";
	$res = pmb_mysql_query($search);
	if(pmb_mysql_num_rows($res) == 0){
		//il existe pas, faut le créer
		$chapeau=new serial();
		$info=array();
		$info['tit1']=addslashes($perio_info['titre']);
		$info['niveau_biblio']='s';
		$info['niveau_hierar']='1';
		$info['typdoc']='p';

		$chapeau->update($info);
		$perio_id=$chapeau->id;
	}else $perio_id = pmb_mysql_result($res,0,0);
	return $perio_id;
}

function genere_bulletin($perio_info,$bull_info,$isbull=true){
	global $bl,$hl,$notice_id,$doc_type;
	//on récup et/ou génère le pério
	$perio_id = genere_perio($perio_info);

	if(!$bull_info['num']){
		$bull_info['num']="[s.n.]";
	}

	$info=array();
	$info['bul_titre']='';
	$info['bul_no']=addslashes($bull_info['num']);
	$info['bul_date']=addslashes($bull_info['date']);
	$info['date_date']="";
	$date_complete=false;
	if(strlen($bull_info['date']) == 4){
		$info['date_date']=addslashes($bull_info['date']."-01-01");
	}elseif(preg_match("#^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$#",trim($bull_info['date']))){
		$info['date_date']=addslashes($bull_info['date']);
		$date_complete=true;
	}elseif(preg_match("#^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})$#",trim($bull_info['date']),$matches)){
		$info['date_date']=addslashes($matches[3]."-".$matches[2]."-".$matches[1]);
		$date_complete=true;
	}elseif(preg_match("#([0-9]{4})#",trim($bull_info['date']),$matches)){
		$info['date_date']=addslashes($matches[1]."-01-01");
	}

	$search = "select bulletin_id from bulletins where bulletin_numero LIKE '".addslashes($info['bul_no'])."' AND bulletin_notice = $perio_id";
	if($info['date_date']){
		if($date_complete){
			$search.=" AND date_date = '".$info['date_date']."'";
		}else{
			$search.=" AND date_date LIKE '".substr($info['date_date'],0,4)."%'";
		}
	}elseif($info['bul_date']){
		$search.=" AND mention_date LIKE '%".$info['bul_date']."%'";
	}
	$res = pmb_mysql_query($search);
	if(pmb_mysql_num_rows($res) == 0){
		$bulletin=new bulletinage("",$perio_id);
		$bull_id=$bulletin->update($info);
	}else {
		$bull_id = pmb_mysql_result($res,0,0);
		if(pmb_mysql_num_rows($res) > 1){//On tente d'affiner
			$search = "select bulletin_id from bulletins where bulletin_numero LIKE '".addslashes($info['bul_no'])."' AND bulletin_notice = $perio_id";
			if($info['date_date']){
				if($date_complete){
					$search.=" AND date_date = '".$info['date_date']."'";
				}else{
					$search.=" AND date_date LIKE '".substr($info['date_date'],0,4)."%'";
				}
			}else{
				$search.=" AND mention_date = '".$info['bul_date']."'";
			}
			$res = pmb_mysql_query($search);
			if(pmb_mysql_num_rows($res) == 1){
				$bull_id = pmb_mysql_result($res,0,0);
			}else{
			//}elseif($doc_type == "t"){//"Texte officiel"
				$bulletin=new bulletinage("",$perio_id);
				$bull_id=$bulletin->update($info);
			}
		}
		//on regarde si une notice n'existe pas déjà pour ce bulletin
		/*$req = "select num_notice from bulletins where bulletin_id = $bull_id and num_notice != 0";
		$res = pmb_mysql_query($req);
		//si oui on retire l'enregistrement en cours, et on continue sur la notice existante...
		if(pmb_mysql_num_rows($res)>0) {
			notice::del_notice($notice_id);
			$notice_id = pmb_mysql_result($res,0,0);
		}*/
	}
	return $bull_id;
}

// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	global $nb_expl_ignores,$bulletin_ex,$charset ;
	global $prix, $notice_id, $info_995, $tdoc_codage, $book_lender_id,
	$sdoc_codage, $book_statut_id, $statisdoc_codage, $locdoc_codage,
	$cote_mandatory, $book_location_id,$cree_expl_asco ;

	if(!$cree_expl_asco){
		return;
	}
	// lu en 010$d de la notice
	$price = $prix[0];

	$nb_infos_995 = count($info_995);
	// la zone 995 est répétable
	for ($nb_expl = 0; $nb_expl < $nb_infos_995; $nb_expl++) {
		/* RAZ expl */
		$expl = array();

		/* préparation du tableau à passer à la méthode */
		$expl['cb'] 	    = $info_995[$nb_expl]['f'];

		if ($bulletin_ex) {
			$expl['bulletin']=$bulletin_ex;
			$expl['notice']=0;
		} else {
			$expl['notice']     = $notice_id ;
			$expl['bulletin']=0;
		}


		$expl['location'] = $book_location_id;

		if ($info_995[$nb_expl]['a']) {
			//On regarde sur le codage d'import
			$req="SELECT idlocation FROM docs_location WHERE locdoc_codage_import='".addslashes($info_995[$nb_expl]['a'])."' ";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 1)){
				$expl['location'] = pmb_mysql_result($res, 0,0);
			}else{
				//On regarde sur le début du libellé
				$req="SELECT idlocation FROM docs_location WHERE location_libelle REGEXP '^".addslashes($info_995[$nb_expl]['a'])."[ \-]+' ";
				$res=pmb_mysql_query($req);
				if($res && (pmb_mysql_num_rows($res) == 1)){
					$expl['location'] = pmb_mysql_result($res, 0,0);
				}else{
					$data_doc=array();
					$data_doc['location_libelle'] = $info_995[$nb_expl]['a'];
					$data_doc['locdoc_codage_import'] = $info_995[$nb_expl]['a'];
					if ($locdoc_codage) $data_doc['locdoc_owner'] = $book_lender_id ;
					else $data_doc['locdoc_owner'] = 0 ;
					$expl['location'] = docs_location::import($data_doc);
				}
			}
		}

		//On regarde si on a déjà un exemplaire pour cette localisation
		$req="SELECT expl_id FROM exemplaires WHERE expl_location='".$expl['location']."' AND expl_notice='".$expl['notice']."' AND expl_bulletin='".$expl['bulletin']."'";
		$res = pmb_mysql_query($req);
		if(pmb_mysql_num_rows($res)){
			//Si oui on ne créer pas l'exemplaire
			continue;
		}

		//Génération du code barres
		if (!$expl['cb']) {
			$requete="DELETE from exemplaires_temp where sess not in (select SESSID from sessions)";
			$res = pmb_mysql_query($requete);
			//Appel à la fonction de génération automatique de cb
			$code_exemplaire =init_gen_code_exemplaire($expl['notice'],$expl['bulletin']);
			do {
				$code_exemplaire = gen_code_exemplaire($expl['notice'],$expl['bulletin'],$code_exemplaire);
				$requete="select expl_cb from exemplaires WHERE expl_cb='$code_exemplaire'";
				$res0 = pmb_mysql_query($requete);
				$requete="select cb from exemplaires_temp WHERE cb='$code_exemplaire' AND sess <>'".SESSid."'";
				$res1 = pmb_mysql_query($requete);
			} while((pmb_mysql_num_rows($res0)||pmb_mysql_num_rows($res1)));

			//Memorise dans temps le cb et la session pour le cas de multi utilisateur session
			$expl['cb'] = $code_exemplaire;
			$requete="INSERT INTO exemplaires_temp (cb ,sess) VALUES ('".$expl['cb']."','".SESSid."')";
			$res = pmb_mysql_query($requete);
		}

		$data_doc=array();
		$data_doc['tdoc_libelle'] = $info_995[$nb_expl]['r'];
		if (!$data_doc['tdoc_libelle']) $data_doc['tdoc_libelle'] = (($charset == "utf-8")?encoding_normalize::utf8_normalize("Indéterminé"):"Indéterminé");

		$requete="SELECT idtyp_doc FROM docs_type WHERE tdoc_libelle LIKE '".addslashes($data_doc['tdoc_libelle'])."'";
		$res=pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res) && $id=pmb_mysql_result($res,0,0)){
			$expl['typdoc'] = $id;
		}else{
			$data_doc['duree_pret'] = 0 ; /* valeur par défaut */
			$data_doc['tdoc_codage_import'] = $data_doc['tdoc_libelle'] ;
			if ($tdoc_codage) $data_doc['tdoc_owner'] = $local_book_lender_id ;
				else $data_doc['tdoc_owner'] = 0 ;
			$expl['typdoc'] = docs_type::import($data_doc);
		}

		$expl['cote'] = $info_995[$nb_expl]['k'];

		if(!trim($info_995[$nb_expl]['q'])) $info_995[$nb_expl]['q']=(($charset == "utf-8")?encoding_normalize::utf8_normalize("Indéterminé"):"Indéterminé");

		// $expl['section']    = $info_995[$nb_expl]['q']; à chercher dans docs_section
		$data_doc=array();
		$info_995[$nb_expl]['q']=trim($info_995[$nb_expl]['q']);
		if (!$info_995[$nb_expl]['q'])
			$info_995[$nb_expl]['q'] = "u";
		$data_doc['section_libelle'] = $info_995[$nb_expl]['q'];
		$data_doc['sdoc_codage_import'] = $info_995[$nb_expl]['q'] ;
		if ($sdoc_codage) $data_doc['sdoc_owner'] = $book_lender_id ;
		else $data_doc['sdoc_owner'] = 0 ;
		$expl['section'] = docs_section::import($data_doc);

		/* $expl['statut']     à chercher dans docs_statut */
		/* TOUT EST COMMENTE ICI, le statut est maintenant choisi lors de l'import
		 if ($info_995[$nb_expl]['o']=="") $info_995[$nb_expl]['o'] = "e";
		$data_doc=array();
		$data_doc['statut_libelle'] = $info_995[$nb_expl]['o']." -Statut importé (".$book_lender_id.")";
		$data_doc['pret_flag'] = 1 ;
		$data_doc['statusdoc_codage_import'] = $info_995[$nb_expl]['o'] ;
		$data_doc['statusdoc_owner'] = $book_lender_id ;
		$expl['statut'] = docs_statut::import($data_doc);
		FIN TOUT COMMENTE */

		$expl['statut'] = $book_statut_id;


		if(!trim($info_995[$nb_expl]['p'])) $info_995[$nb_expl]['p']="In";

		// $expl['codestat']   = $info_995[$nb_expl]['q']; 'q' utilisé, éventuellement à fixer par combo_box
		$data_doc=array();
		//$data_doc['codestat_libelle'] = $info_995[$nb_expl]['q']." -Pub visé importé (".$book_lender_id.")";
		$data_doc['codestat_libelle'] = $info_995[$nb_expl]['p'];
		$data_doc['statisdoc_codage_import'] = $info_995[$nb_expl]['p'] ;
		if ($statisdoc_codage) $data_doc['statisdoc_owner'] = $book_lender_id ;
		else $data_doc['statisdoc_owner'] = 0 ;
		$expl['codestat'] = docs_codestat::import($data_doc);


		// $expl['creation']   = $info_995[$nb_expl]['']; à préciser
		// $expl['modif']      = $info_995[$nb_expl]['']; à préciser

		$expl['note']       = $info_995[$nb_expl]['u'];
		$expl['prix']       = $price;
		$expl['expl_owner'] = $book_lender_id ;
		$expl['cote_mandatory'] = $cote_mandatory ;

		if (!empty($info_995[$nb_expl]['m'])) {
			$expl['date_depot'] = substr($info_995[$nb_expl]['m'],0,4)."-".substr($info_995[$nb_expl]['m'],4,2)."-".substr($info_995[$nb_expl]['m'],6,2) ;
		}
		if (!empty($info_995[$nb_expl]['n'])) {
			$expl['date_retour'] = substr($info_995[$nb_expl]['n'],0,4)."-".substr($info_995[$nb_expl]['n'],4,2)."-".substr($info_995[$nb_expl]['n'],6,2) ;
		}

		// quoi_faire
		if ($info_995[$nb_expl]['0']) $expl['quoi_faire'] = $info_995[$nb_expl]['0']  ;
		else $expl['quoi_faire'] = 2 ;

		/*echo "<pre>";
		print_r($expl);
		echo "</pre>";*/
		$expl_id = exemplaire::import($expl);
		if ($expl_id == 0) {
			$nb_expl_ignores++;
		}

		//debug : affichage zone 995
		/*
		echo "995\$a =".$info_995[$nb_expl]['a']."<br />";
		echo "995\$b =".$info_995[$nb_expl]['b']."<br />";
		echo "995\$c =".$info_995[$nb_expl]['c']."<br />";
		echo "995\$d =".$info_995[$nb_expl]['d']."<br />";
		echo "995\$f =".$info_995[$nb_expl]['f']."<br />";
		echo "995\$k =".$info_995[$nb_expl]['k']."<br />";
		echo "995\$m =".$info_995[$nb_expl]['m']."<br />";
		echo "995\$n =".$info_995[$nb_expl]['n']."<br />";
		echo "995\$o =".$info_995[$nb_expl]['o']."<br />";
		echo "995\$q =".$info_995[$nb_expl]['q']."<br />";
		echo "995\$r =".$info_995[$nb_expl]['r']."<br />";
		echo "995\$u =".$info_995[$nb_expl]['u']."<br /><br />";
		*/
	} // fin for
} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI