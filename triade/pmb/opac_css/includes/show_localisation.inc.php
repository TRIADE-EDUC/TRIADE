<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: show_localisation.inc.php,v 1.106.4.2 2025/01/30 11:42:35 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $msg, $charset;
global $opac_nb_sections_per_line, $opac_categories_nb_col_subcat;
global $location, $id;
global $back_surloc, $back_loc, $back_section_see, $opac_sur_location_activate;
global $opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet, $opac_rgaa_active;
global $default_tmp_storage_engine;
global $nc, $lcote, $ssub, $plettreaut, $dcote;

require_once($class_path."/translation.class.php");
require_once($class_path."/show_localisation.class.php");

if (!$opac_nb_sections_per_line) $opac_nb_sections_per_line=6;

/* Paramètres optionnels dans l'url
 * back_surloc => Lien de retour sur la sur-localisation
 * back_loc => Lien de retour sur la localisation
 * back_section_see => Lien de retour sur image (home)
 * */

//Attaques XSS et injection SQL
if(isset($nc)){
	$nc = intval($nc);
}
if(isset($lcote)){
    $lcote = intval($lcote);
}
if(isset($ssub)){
    $ssub = intval($ssub);
}
if(isset($plettreaut)){
	$plettreaut=pmb_alphabetic("^a-z0-9A-Z\-\s","",$plettreaut);
}
if(isset($dcote)){
	$req="SELECT count(expl_id) FROM exemplaires WHERE expl_cote LIKE '".addslashes($dcote)."%' ";
	$res=pmb_mysql_query($req);
	if(!$res || !pmb_mysql_result($res,0,0)){
		$dcote="";
	}
}
$location = intval($location);
show_localisation::set_num_location($location);
if (!$location) {
	//Il n'y a pas de localisation selectionnée, afficher les localisations
	print "<div id='aut_details'>\n";
	print common::format_title($msg["l_browse_bibliotheques"]);

	print "<div id='aut_details_container'>\n";

	print show_localisation::get_display_list();
} else {
	// id localisation fournie
	$requete="select idlocation, location_libelle,surloc_num, location_pic, name, adr1, adr2, cp, town, state, country, phone, email, website, commentaire, show_a2z from docs_location where idlocation='$location' and location_visible_opac=1";
	$resultat=pmb_mysql_query($requete);
	$objloc=pmb_mysql_fetch_object($resultat);

	if (isset($back_surloc) && $back_surloc) $param_surloc = "&back_surloc=".rawurlencode($back_surloc);
	else $param_surloc="";
	$url_loc ="index.php?lvl=section_see&location=".$location;
	if (isset($back_section_see) && $back_section_see) $param_section_see = "&back_section_see=".$back_section_see;
	else $param_section_see="";

	if (isset($back_loc) && $back_loc) $location_link="<span class=\"espaceResultSearch\">&nbsp;</span><a href=\"".$url_loc.$param_surloc.$param_section_see."\">". htmlentities(translation::get_translated_text($objloc->idlocation, "docs_location", "location_libelle",$objloc->location_libelle),ENT_QUOTES,$charset)."</a>";
	else $location_link="<span class=\"espaceResultSearch\">&nbsp;</span>".htmlentities(translation::get_translated_text($objloc->idlocation, "docs_location", "location_libelle",$objloc->location_libelle),ENT_QUOTES,$charset);

	$sur_location_link="";
	if ($opac_sur_location_activate==1){
		$requete="select surloc_id, surloc_libelle, surloc_pic, surloc_css_style from sur_location where surloc_id='$objloc->surloc_num'";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			if ($r=pmb_mysql_fetch_object($resultat)) {
				if (!empty($back_surloc)) $url_surloc = $back_surloc;
				else $url_surloc = "index.php?lvl=section_see&surloc=".$r->surloc_id;
				$sur_location_link="<span class=\"espaceResultSearch\">&nbsp;</span><a href=\"".$url_surloc."\">". htmlentities( $r->surloc_libelle,ENT_QUOTES,$charset)."</a>";
			}
		}
	}
	print "<div id='aut_details'>\n";

	if (isset($back_section_see) && $back_section_see) {
	    $url_section_see = $back_section_see;
	} else {
	    $url_section_see = "index.php?lvl=section_see";
	}

	if ($opac_rgaa_active) {
    	print "<h1 class='loc_title'><span><a href=\"".$url_section_see."\"><img src='".get_url_icon("home.gif")."' alt='home' style='border:0px' class='align_bottom'/></a>".$sur_location_link.$location_link."</span></h1>";
	} else {
    print "<h3 class='loc_title'><span><a href=\"".$url_section_see."\"><img src='".get_url_icon("home.gif")."' alt='home' style='border:0px' class='align_bottom'/></a>".$sur_location_link.$location_link."</span></h3>";
	}
	if ($objloc->commentaire || $objloc->location_pic) {
		print "<table class='loc_comment' role='presentation'><tr><td class='location_pic'>";
		if ($objloc->location_pic)
			print "<span class=\"espaceResultSearch\">&nbsp;</span><img src='".$objloc->location_pic."' alt='location' style='border:0px' class='center' />";
		else
			print "<span class=\"espaceResultSearch\">&nbsp;</span>";
		print "</td><td>";
		if ($objloc->commentaire)
			print $objloc->commentaire;
		else
			print "<span class=\"espaceResultSearch\">&nbsp;</span>";
		print "</td></tr></table>";
	}
	$Fnm = "includes/mw_liste_type.inc.php";
	if (file_exists($Fnm)) { include($Fnm);}

	print "<div id='aut_details_container'>\n";

	//Il n'y a pas de section sélectionnée
	$id = intval($id);
	show_localisation::set_num_section($id);
	if (!$id) {
	    print show_localisation::get_display_sections_list();

		if ($objloc->show_a2z) {
			require_once($base_path."/classes/perio_a2z.class.php");
			$a2z=new perio_a2z(0,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);
			print $perio_a2z=$a2z->get_form();
		}

	} else {
		//enregistrement de l'endroit actuel dans la session
		rec_last_authorities();

		$location = intval($location);
		if (!empty($back_surloc)) {
			$ajout_back = "&back_surloc=".rawurlencode($back_surloc)."&back_loc=".rawurlencode($url_loc).$param_section_see;
		} else {
			$ajout_back = "";
		}
		$requete="select idsection, section_libelle, section_libelle_opac, section_pic from docs_section where idsection=$id";
		$result = pmb_mysql_query($requete);
		$row = pmb_mysql_fetch_object($result);
		if ($row->section_libelle_opac) {
			$section_libelle = translation::get_translated_text($row->idsection, 'docs_section', 'section_libelle_opac', $row->section_libelle_opac);
		} else {
			$section_libelle = translation::get_translated_text($row->idsection, 'docs_section', 'section_libelle', $row->section_libelle);
		}
		if ($row->section_pic) $image_src = $row->section_pic ;
		else  $image_src = get_url_icon("rayonnage-small.png") ;
		print "<div id='aut_see'><h3>";
		if (!file_exists($Fnm))	{
			print "<a href='index.php?lvl=section_see&location=".$location.$ajout_back."'><img src='".$image_src."' style='border:0px' class='center' alt='".$msg["l_rayons"]."' title='".$msg["l_rayons"]."'/></a><span class=\"espaceResultSearch\">&nbsp;</span>";
		}

		$requete="SELECT num_pclass FROM docsloc_section WHERE num_location='".$location."' AND num_section='".$id."' ";
		$res=pmb_mysql_query($requete);
		$type_aff_navigopac=0;
		if(pmb_mysql_num_rows($res)){
			$type_aff_navigopac=pmb_mysql_result($res,0,0);
		}

		//droits d'acces emprunteur/notice
		show_localisation::init_query_restricts();

		if($type_aff_navigopac == 0){//Pas de navigation
			print pmb_bidi($section_libelle);
			print "</h3>\n";
			print "</div>";
			//On récupère les notices de monographie avec au moins un exemplaire dans la localisation et la section
			$requete="create temporary table temp_n_id ENGINE={$default_tmp_storage_engine} (
                ".show_localisation::get_query_records_items('notice_id', '', 'notice_id')."
            )";
			pmb_mysql_query($requete);
			//On récupère les notices de périodique avec au moins un exemplaire d'un bulletin dans la localisation et la section
			$requete="INSERT INTO temp_n_id (
                ".show_localisation::get_query_serials_items('notice_id', '', 'notice_id')."
            )";
			pmb_mysql_query($requete);
			@pmb_mysql_query("alter table temp_n_id add index(notice_id)");
			$requete = "SELECT notices.notice_id FROM temp_n_id JOIN notices ON notices.notice_id=temp_n_id.notice_id GROUP BY notices.notice_id";
			$nbr_lignes=pmb_mysql_num_rows(pmb_mysql_query($requete));
			show_localisation::affiche_notice_navigopac($requete);
		}elseif($type_aff_navigopac == -1){//Navigation par auteurs
			//On récupère les notices de monographie avec au moins un exemplaire dans la localisation et la section
			$requete="create temporary table temp_n_id ENGINE={$default_tmp_storage_engine} (
                ".show_localisation::get_query_records_items('notice_id', '', 'notice_id')."
            )";
			pmb_mysql_query($requete);
			//On récupère les notices de périodique avec au moins un exemplaire d'un bulletin dans la localisation et la section
			$requete="INSERT INTO temp_n_id (
                ".show_localisation::get_query_serials_items('notice_id', '', 'notice_id')."
            )";
			pmb_mysql_query($requete);
			@pmb_mysql_query("alter table temp_n_id add index(notice_id)");
			if(!$plettreaut){
				$nb_auteur_max=18;
				//On a pas encore choisi de première lettre d'auteur
				print pmb_bidi($section_libelle);
				print " > ".$msg["navigopac_aut"];
				print "</h3>\n";

				//On va chercher tous les auteurs des notices
				$requete = "SELECT IF(SUBSTRING(TRIM(index_author),1,1) != '' ,SUBSTRING(TRIM(index_author),1,1),'vide') as plettre, COUNT(1) as nb FROM temp_n_id LEFT JOIN responsability ON responsability_notice=notice_id LEFT JOIN authors ON author_id=responsability_author GROUP BY IF(index_author IS NOT NULL and TRIM(index_author) !='',SUBSTRING(TRIM(index_author),1,1),index_author) ORDER BY 1";
				$res=pmb_mysql_query($requete);
				$tab_aut=array();
				while ($ligne = pmb_mysql_fetch_object($res)) {
					//echo " Lettre : ".$ligne->plettre." Nombre : ".$ligne->nb."<br />";
					if($ligne->plettre == "vide"){
						if($tab_aut[$ligne->plettre]){
							$nb=$tab_aut[$ligne->plettre][0]+$ligne->nb;
							$tab_aut[$ligne->plettre]=array($nb,$msg["navigopac_ss_aut"]);
						}else{
							$tab_aut[$ligne->plettre]=array($ligne->nb,$msg["navigopac_ss_aut"]);
						}
					}elseif(preg_match("#[0-9]#",$ligne->plettre)){
						if($tab_aut["num"]){
							$nb=$tab_aut["num"][0]+$ligne->nb;
							$tab_aut["num"]=array($nb,"0-9");
						}else{
							$tab_aut["num"]=array($ligne->nb,"0-9");
						}
					}else{
						$tab_aut[mb_strtoupper($ligne->plettre)]=array($ligne->nb,mb_strtoupper($ligne->plettre));
					}
				}
				while(count($tab_aut) > $nb_auteur_max){//Pour minimiser le nombre d'étagère à afficher
					//Je vais chercher deux valeurs qui peuvent être regroupées
					$coupl_plus_petit=10000000;
					$ancienne_valeur=0;
					$ancienne_lettre="";
					$lettre_a_regoupe=array();
					foreach ($tab_aut as $key => $value ) {
       					if($key != "num" && $key != "vide"){
       						if($ancienne_valeur && ($ancienne_valeur + $value[0] < $coupl_plus_petit)){
								$coupl_plus_petit=$ancienne_valeur + $value[0];
								$lettre_a_regoupe=array($ancienne_lettre,$key);
							}
							$ancienne_valeur=$value[0];
							$ancienne_lettre=$key;
       					}
					}
					//J'en regroupe deux
					$new_key=substr($lettre_a_regoupe[0],0,1)."-".substr($lettre_a_regoupe[1],-1);
					$tab_aut[$new_key]=array(($tab_aut[$lettre_a_regoupe[0]][0]*1+$tab_aut[$lettre_a_regoupe[1]][0]*1),$new_key);
					unset($tab_aut[$lettre_a_regoupe[0]]);
					unset($tab_aut[$lettre_a_regoupe[1]]);
					ksort($tab_aut);
				}
				print "<table class='center' style='width:100%' role='presentation'>";
				$n=0;
				foreach ( $tab_aut as $key => $value ) {
					if ($n==0) print "<tr>";
					print "<td style='width:120px'><a href='./index.php?lvl=section_see&location=".$location."&id=".$id."&plettreaut=".$key.$ajout_back."'><img src='".get_url_icon('folder.gif')."' alt='folder' class='center' style='border:0px'/>".htmlentities($value[1],ENT_QUOTES,$charset)."</a></td>";
					$n++;
					if ($n==$opac_nb_sections_per_line) { print "</tr>"; $n=0; }
				}
				if ($n!=0) {
					while ($n<$opac_nb_sections_per_line) {
						print "<td></td>";
						$n++;
					}
					print "</tr>";
				}
				print "</table>";
				print "</div>";
				$requete = "SELECT notices.notice_id FROM temp_n_id JOIN notices ON notices.notice_id=temp_n_id.notice_id GROUP BY notices.notice_id";
				$nbr_lignes=pmb_mysql_num_rows(pmb_mysql_query($requete));
				show_localisation::affiche_notice_navigopac($requete);
			}else{
				//On sait par quoi doit commencer le nom de l'auteur
				print "<a href='index.php?lvl=section_see&location=".$location."&id=".$id.$ajout_back."'>";
				print pmb_bidi($section_libelle);
				print "</a>";

				if($plettreaut == "num"){
					$requete = "SELECT notices.notice_id FROM temp_n_id JOIN responsability ON responsability_notice=temp_n_id.notice_id JOIN authors ON author_id=responsability_author and trim(index_author) REGEXP '^[0-9]' JOIN notices ON notices.notice_id=temp_n_id.notice_id GROUP BY notices.notice_id";
					print " > ".$msg["navigopac_aut_com_par_chiffre"];
				}elseif($plettreaut == "vide"){
					$requete = "SELECT notices.notice_id FROM temp_n_id LEFT JOIN responsability ON responsability_notice=temp_n_id.notice_id LEFT JOIN notices ON notices.notice_id=temp_n_id.notice_id WHERE responsability_author IS NULL GROUP BY notices.notice_id";
					print " > ".$msg["navigopac_ss_aut"];
				}else{
					$requete = "SELECT notices.notice_id FROM temp_n_id JOIN responsability ON responsability_notice=temp_n_id.notice_id JOIN authors ON author_id=responsability_author and trim(index_author) REGEXP '^[".$plettreaut."]' JOIN notices ON notices.notice_id=temp_n_id.notice_id GROUP BY notices.notice_id";
					print " > ".$msg["navigopac_aut_com_par"]." ".$plettreaut;
				}
				$nbr_lignes=pmb_mysql_num_rows(pmb_mysql_query($requete));
				print "</h3>\n";
				print "</div>";
				show_localisation::affiche_notice_navigopac($requete);
			}
		}else{//Navigation par un plan de classement

			if(!isset($dcote) || !$dcote) {
			    $query = show_localisation::get_query_records_items('distinct SUBSTR(expl_cote,1,1) as dcote');
				$query .= " UNION ";
				$query .= show_localisation::get_query_serials_items('distinct SUBSTR(expl_cote,1,1) as dcote');
				$result = pmb_mysql_query($query);
				if($result && pmb_mysql_num_rows($result) == 1) {
					//Afin d'afficher les sous-niveaux du premier niveau
					$dcote = pmb_mysql_result($result, 0, 0);
				}
			}
			if (strlen($dcote)||($nc==1)) print "<a href='index.php?lvl=section_see&location=".$location."&id=".$id.$ajout_back."'>";
			print pmb_bidi($section_libelle);

			if (strlen($dcote)||($nc==1)) print "</a>";
			//Calcul du chemin
			if (strlen($dcote)) {
				if (!$ssub) {
					for ($i=0; $i<strlen($dcote); $i++) {
						$chemin="";
						$ccote=substr($dcote,0,$i+1);
						$ccote_times = $lcote-$i-1;
						if($ccote_times >= 0) {
							$ccote=$ccote.str_repeat("0",$ccote_times);
						}
						if ($i>0) {
							$cote_n_1=substr($dcote,0,$i);
							$compl_n_1="";
							$compl_n_1_times = $lcote-$i;
							if($compl_n_1_times >= 0) {
								$compl_n_1=str_repeat("0",$compl_n_1_times);
							}
							if (($ccote)==($cote_n_1.$compl_n_1)) $chemin=$msg["l_general"];
						}
						if (!$chemin) {
							$requete="select indexint_name,indexint_comment from indexint where indexint_name='".$ccote."' and num_pclass='".$type_aff_navigopac."'";
							$res_ch=pmb_mysql_query($requete);
							if (pmb_mysql_num_rows($res_ch))
								$chemin=pmb_mysql_result(pmb_mysql_query($requete),0,1);
							else
								$chemin=$msg["l_unclassified"];
						}
						print " > ";
						if ((($i+1)<strlen($dcote))||($nc==1)) print "<a href='index.php?lvl=section_see&location=".$location."&id=".$id."&dcote=".substr($dcote,0,$i+1)."&lcote=".$lcote.$ajout_back."'>";
						print pmb_bidi($chemin);
						if ((($i+1)<strlen($dcote))||($nc==1)) print "</a>"; else $theme=$chemin;
					}
				} else {
					$t_dcote=explode(",",$dcote);
					$requete="select indexint_comment from indexint where indexint_name='".stripslashes($t_dcote[0])."' and num_pclass='".$type_aff_navigopac."'";
					$res_ch=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($res_ch))
						$chemin=pmb_mysql_result(pmb_mysql_query($requete),0,0);
					else
						$chemin=$msg["l_unclassified"];
					print pmb_bidi(" > ".$chemin);
				}
			}
			if ($nc==1) { print " > ".$msg["l_unclassified"]; $theme=$msg["l_unclassified"]; }
			print "</h3>\n";
			if ($ssub) {
				$t_expl_cote_cond=array();
				if (is_countable($t_dcote)) {
					for ($i=0; $i<count($t_dcote); $i++) {
						$t_expl_cote_cond[]="expl_cote regexp '(^".$t_dcote[$i]." )|(^".$t_dcote[$i]."[0-9])|(^".$t_dcote[$i]."$)|(^".$t_dcote[$i].".)'";
					}
				}
				$expl_cote_cond="(".implode(" or ",$t_expl_cote_cond).")";
			}

			if(!$nbr_lignes) {

				if (!$ssub) {
				    $clause = '';
				    if (strlen($dcote)) {
						$dcote_times = $lcote-strlen($dcote);
						if($dcote_times >= 0) {
				        	$clause .= "expl_cote regexp '".$dcote.str_repeat("[0-9]",$dcote_times)."' and expl_cote not regexp '(\\\\.[0-9]*".$dcote.str_repeat("[0-9]",$dcote_times).")|([^0-9]*[0-9]+\\\\.?[0-9]*.+".$dcote.str_repeat("[0-9]",$dcote_times).")' ";
						}
				    }
				    $requete = show_localisation::get_query_records_items('COUNT(distinct notice_id)', $clause);
					$res = pmb_mysql_query($requete);
					$nbr_lignes = @pmb_mysql_result($res, 0, 0);

					$requete2 = show_localisation::get_query_serials_items('COUNT(distinct notice_id)', $clause);
					$res = pmb_mysql_query($requete2);
					$nbr_lignes += @pmb_mysql_result($res, 0, 0);

				} else {
				    $clause = '';
				    if (strlen($dcote)) {
				        $clause.= $expl_cote_cond;
				    }
				    $requete = show_localisation::get_query_records_items('COUNT(distinct notice_id)', $clause);
					$res = pmb_mysql_query($requete);
					$nbr_lignes = @pmb_mysql_result($res, 0, 0);

					$requete2 = show_localisation::get_query_serials_items('COUNT(distinct notice_id)', $clause);
					$res = pmb_mysql_query($requete2);
					$nbr_lignes += @pmb_mysql_result($res, 0, 0);
				}
			}

			if($nbr_lignes) {
			    $clause = '';
			    if (strlen($dcote)) {
			        if (!$ssub) {
						$dcote_times = $lcote-strlen($dcote);
						if($dcote_times >= 0) {
			            	$clause .= "expl_cote regexp '".$dcote.str_repeat("[0-9]",$dcote_times)."' and expl_cote not regexp '(\\\\.[0-9]*".$dcote.str_repeat("[0-9]",$dcote_times).")|([^0-9]*[0-9]+\\\\.?[0-9]*.+".$dcote.str_repeat("[0-9]",$dcote_times).")' ";
						}
			        } else {
			            $clause.= $expl_cote_cond;
			        }
			    }
				//Table temporaire de tous les id
				$requete = "create temporary table temp_n_id ENGINE={$default_tmp_storage_engine} (
                    ".show_localisation::get_query_records_items('notice_id, expl_id', $clause, 'notice_id, expl_id')."
                )";
				pmb_mysql_query($requete);

				$requete2 = "insert into temp_n_id (
                    ".show_localisation::get_query_serials_items('notice_id, expl_id', $clause, 'notice_id, expl_id')."
                )";
				@pmb_mysql_query($requete2);
				@pmb_mysql_query("alter table temp_n_id add index(notice_id, expl_id)");
				//Calcul du classement
				$index=array();
				if (!$ssub) {
					$rq1_index="create temporary table union1 ENGINE={$default_tmp_storage_engine} (select distinct expl_cote from exemplaires, temp_n_id where expl_location='".$location."' and expl_section='".$id."' and expl_notice=temp_n_id.notice_id) ";
					pmb_mysql_query($rq1_index);
					$rq2_index="create temporary table union2 ENGINE={$default_tmp_storage_engine} (select distinct expl_cote from exemplaires join (select distinct bulletin_id from bulletins join temp_n_id where bulletin_notice=notice_id) as sub on (bulletin_id=expl_bulletin) where expl_location='".$location."' and expl_section='".$id."') ";
					pmb_mysql_query($rq2_index);
					$req_index="select distinct expl_cote from union1 union select distinct expl_cote from union2";
					$res_index=pmb_mysql_query($req_index);

					if ($level_ref==0) $level_ref=1;

					// Prepare indexint pre selection - Zend
					$zendIndexInt = array();
					//$zendIndexIntCache = array();
					$zendQ1 = "SELECT indexint_name, indexint_comment FROM indexint WHERE indexint_name NOT REGEXP '^[0-9][0-9][0-9]' AND indexint_comment != '' AND num_pclass='".$type_aff_navigopac."'";
					$zendRes = pmb_mysql_query($zendQ1);
					while ($zendRow = pmb_mysql_fetch_assoc($zendRes)) {
						$zendIndexInt[$zendRow['indexint_name']] = $zendRow['indexint_comment'];
					}
					// Zend
					while ($ct=pmb_mysql_fetch_object($res_index)) {
						//Je regarde si le début existe dans indexint
						$lf=5;
						$t=array();
						while ($lf>0) {
							$zendKey = substr($ct->expl_cote, 0, $lf);
							if ($zendIndexInt[$zendKey]) {
								if (!$nc) {
									$t["comment"]=$zendIndexInt[$zendKey];
									$t["dcote"]=$zendKey;
									$t["ssub"]=1;
									$index[$t["dcote"]]=$t;
									break;
								} else {
									$rq_del="select distinct notice_id, expl_id from notices, exemplaires where expl_cote='".$ct->expl_cote."' and expl_notice=notice_id ";
									$rq_del.=" union select distinct notice_id, expl_id from notices, exemplaires, bulletins where expl_cote='".$ct->expl_cote."' and expl_bulletin=bulletin_id and bulletin_notice=notice_id ";
									$res_del=pmb_mysql_query($rq_del) ;
									if (pmb_mysql_num_rows($res_del)) {
										while ($n_id=pmb_mysql_fetch_object($res_del)) {
											pmb_mysql_query("delete from temp_n_id where notice_id=".$n_id->notice_id." and expl_id=".$n_id->expl_id);
										}
									}
								}
							}
							$lf--;
						}
						if ($lf==0) {
							if (preg_match("/[0-9][0-9][0-9]/",$ct->expl_cote,$c)) {
								$found=false;
								$lcote=3;
								$level= intval($level_ref);
								while ((!$found)&&($level<=$lcote)) {
									$cote=substr($c[0],0,$level);
									$compl = "";
									$compl_times = $lcote-$level;
									if($compl_times >= 0) {
										$compl=str_repeat("0",$compl_times);
									}
									$rq_index="select indexint_name,indexint_comment from indexint where indexint_name='".$cote.$compl."' and length(indexint_name)>=$lcote and indexint_comment!='' and num_pclass='".$type_aff_navigopac."' order by indexint_name limit 1 ";
									$res_index_1=pmb_mysql_query($rq_index);
									if (pmb_mysql_num_rows($res_index_1)) {
										$name=pmb_mysql_result($res_index_1,0,0);
										if (!$nc) {
											if (substr($name,0,$level-1)==$dcote) {
												$t["comment"]=pmb_mysql_result($res_index_1,0,1);
												if ($level>1) {
													$cote_n_1=substr($c[0],0,$level-1);
													$compl_n_1="";
													$compl_n_1_times = $lcote-$level+1;
													if($compl_n_1_times >= 0) {
														$compl_n_1=str_repeat("0",$compl_n_1_times);
													}
													if (($cote.$compl)==($cote_n_1.$compl_n_1))
														$t["comment"]="Généralités";
												}
												$t["lcote"]=$lcote;
												$t["dcote"]=$cote;
												$index[$name]=$t;
												$found=true;
											} else $level++;
										} else {
											if (substr($name,0,$level-1)==$dcote) {
												$rq_del="select distinct notice_id, expl_id from notices, exemplaires where expl_cote='".$ct->expl_cote."' and expl_notice=notice_id ";
												$rq_del.=" union select distinct notice_id, expl_id from notices, exemplaires, bulletins where expl_cote='".$ct->expl_cote."' and expl_bulletin=bulletin_id and bulletin_notice=notice_id ";
												$res_del=pmb_mysql_query($rq_del);
												if (pmb_mysql_num_rows($res_del)) {
													while ($n_id=pmb_mysql_fetch_object($res_del)) {
														pmb_mysql_query("delete from temp_n_id where notice_id=".$n_id->notice_id." and expl_id=".$n_id->expl_id);
													}
												}
												$found=true;
											} else $level++;
										}
									} else $level++;
								}
								if (($level>$lcote)&&($lf==0)) {
									$t["comment"]=$msg["l_unclassified"];
									$t["lcote"]=$lcote;
									$t["dcote"]=$dcote;
									$index["NC"]=$t;
								}
							} else {
								$t["comment"]=$msg["l_unclassified"];
								$t["lcote"]=$lcote;
								$t["dcote"]=$dcote;
								$index["NC"]=$t;
							}
						}
					}
				}
				if ($nc) {
					$nbr_lignes=pmb_mysql_result(pmb_mysql_query("select count(1) from temp_n_id"),0,0);
				}
				if ($nbr_lignes) {
					//Affichage des sous catégories
					if (count($index)>1) {
						if (!strlen($dcote))
							print pmb_bidi(sprintf($msg["l_etageres"],htmlentities($section_libelle,ENT_QUOTES,$charset)));
						else if (strlen($dcote)==1)
							print pmb_bidi(sprintf($msg["l_themes"],htmlentities($theme,ENT_QUOTES,$charset)));
						else
							pmb_bidi(print sprintf($msg["l_sub_themes"],htmlentities($theme,ENT_QUOTES,$charset)));
						reset($index);
						$ssub_val=array();
						//Regroupement des libellés identiques hors dewey
						foreach ($index as $key => $val) {
							if ($val["ssub"]) {
								if ($ssub_val[$val["comment"]]) {
									$ssub_val[$val["comment"]]["dcote"].=",".$val["dcote"];
								} else {
									$ssub_val[$val["comment"]]=$val;
								}
							} else {
								$ssub_val[$val["comment"]."@ssub"]=$val;
							}
						}
						//Affichage du classement si il reste suffisamment de catégories
						if (count($ssub_val)>1) {
							$opac_categories_nb_col_subcat;
							$cur_col=0;
							reset($ssub_val);
							asort($ssub_val);
							print "<table>";
							foreach ($ssub_val as $key => $val) {
								if ($cur_col==0) print "<tr>";
								if (($key=="NC")||($key==$msg["l_unclassified"]."@ssub")) $nc1=1; else $nc1=0;
								print "<td style='width:33%'><a href='./index.php?lvl=section_see&id=".$id."&location=".$location."&dcote=".$val["dcote"]."&lcote=".$val["lcote"]."&nc=".$nc1."&ssub=".$val["ssub"].$ajout_back."'><img src='".get_url_icon('folder.gif')."' alt='folder' class='center' style='border:0px'/>".htmlentities($val["comment"],ENT_QUOTES,$charset)."</a></td>";
								$cur_col++;
								if ($cur_col==$opac_categories_nb_col_subcat) {
									print "</tr>";
									$cur_col=0;
								}
							}
							if ($cur_col<$opac_categories_nb_col_subcat) {
								for ($i=$cur_col; $i<$opac_categories_nb_col_subcat; $i++) {
									print "<td><span class=\"espaceResultSearch\">&nbsp;</span></td>";
								}
								print "</tr>";
							}
							print "</table><br />";
						}
					}
					print "</div>";
					$requete = "SELECT DISTINCT notices.notice_id FROM temp_n_id JOIN notices ON notices.notice_id=temp_n_id.notice_id GROUP BY notices.notice_id";
					show_localisation::affiche_notice_navigopac($requete);
				} else {
					print "</div><br /><blockquote role='presentation'>$msg[categ_empty]</blockquote><br />";
				}
			} else {
				print "</div><br /><blockquote role='presentation'>$msg[categ_empty]</blockquote><br />";
			}
		}
	}
}
print "</div><!-- / #aut_details_container -->\n";
print "</div><!-- / #aut_details -->\n";
