<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: wmo.inc.php,v 1.5.14.1 2025/03/25 07:32:25 dgoron Exp $

function search_other_function_filters() {
	global $annee_parution,$doc_num,$free, $code_langue_restrict,$lang,$nocoll,$tdnocoll;
	global $charset,$msg,$marc_liste_langues;
	global $topics, $regions, $wmo_pub;
	
	$r="";
	/*//champ theme
	$r.="<select name='topics' class='search_label'>";
	$r.="<option value=''>".$msg['all_topics']."</option>";
	$requete="select libelle_categorie from categories where num_noeud is not null and (num_noeud='10066' OR num_noeud='10013' OR num_noeud='10270' OR num_noeud='10372' OR num_noeud='10010' OR num_noeud='10096' OR num_noeud='10077' OR num_noeud='10044' OR num_noeud='10061' OR num_noeud='10036' OR num_noeud='10075' OR num_noeud='10012' OR num_noeud='10057') and langue like '%$lang%' order by 1";
	$resultat=pmb_mysql_query($requete);
	while (($res=pmb_mysql_fetch_object($resultat))) {
		$r.="<option value='".htmlentities($res->libelle_categorie,ENT_QUOTES,$charset)."' ";
		if ($res->libelle_categorie==$topics) $r.="selected='selected'";
		$r.=">".$res->libelle_categorie;
		$r.="</option>";
	}
	$r.="</select>";
	
	//champ region
	$r.="<span class='tabulation2'></span><select name='regions' class='search_label'>";
	$r.="<option value=''>".$msg['all_regions']."</option>
		 <option value=10630 ".(($regions == 10630) ? "selected='selected'" : "").">".$msg['region_0']."</option>
		 <option value=10291 ".(($regions == 10291) ? "selected='selected'" : "").">".$msg['region_1']."</option>
		 <option value=10378 ".(($regions == 10378) ? "selected='selected'" : "").">".$msg['region_2']."</option>
		 <option value=10332 ".(($regions == 10332) ? "selected='selected'" : "").">".$msg['region_3']."</option>
		 <option value=10918 ".(($regions == 10918) ? "selected='selected'" : "").">".$msg['region_4']."</option>
		 <option value=10411 ".(($regions == 10411) ? "selected='selected'" : "").">".$msg['region_5']."</option>
		 <option value=10395 ".(($regions == 10395) ? "selected='selected'" : "").">".$msg['region_6']."</option>
		 <option value=10624 ".(($regions == 10624) ? "selected='selected'" : "").">".$msg['region_antarctique']."</option>
		 <option value=10625 ".(($regions == 10625) ? "selected='selected'" : "").">".$msg['region_arctique']."</option>
		 </select>";

	//champ langue
	if (!$marc_liste_langues) $marc_liste_langues=new marc_list('lang');
	$r.="<span class='tabulation2'></span><select name='code_langue_restrict' class='search_label'>";
	$r.="<option value=''>".$msg['all_languages']."</option>";
	$requete="select distinct code_langue from notices_langues where code_langue is not null and (code_langue='eng' OR code_langue='fre' OR code_langue='spa' OR code_langue='rus' OR code_langue='chi' OR code_langue='ara') order by 1";
	$resultat=pmb_mysql_query($requete);
	// on met les balises <option> dans un tableau, indexé par le nom traduit de la langue
    $t=array();
    while ($res=pmb_mysql_fetch_object($resultat)) {
        if ($marc_liste_langues->table[$res->code_langue]) {
            $s="<option value='".htmlentities($res->code_langue,ENT_QUOTES,$charset)."' ";
            if ($res->code_langue==$code_langue_restrict) $s.="selected";
            $s.=">".$marc_liste_langues->table[$res->code_langue];
            $s.="</option>";
            $t[$marc_liste_langues->table[$res->code_langue]]=$s;
        }
    }
	// tri le tableau selon les cles (ici les noms des langues, pas les codes)
    ksort($t);

    // recopie des balises triees dans la liste <select>
    foreach($t as $k => $v) $r.=$v;

    $r.="</select>";

	//champ année : opérateur + boite texte
	$r.="<p class='br'><span class='search2_label'>$msg[sdate]&nbsp;</span><input type='text' size='5' name='annee_parution' value='".htmlentities($annee_parution,ENT_QUOTES,$charset)."'/>";
	
	//champ typdoc
	$r.="<span class='tabulation2'></span><select name='typnoti'>";
	$r.="<option value=''>".$msg['simple_search_all_doc_type']."</option>
		 <option value=1>".$msg['typnoti_1']."</option>
		 <option value=2>".$msg['typnoti_2']."</option>
		 <option value=3>".$msg['typnoti_3']."</option>
		 <option value=7>".$msg['typnoti_7']."</option>
		 <option value=4>".$msg['typnoti_4']."</option>
		 <option value=5>".$msg['typnoti_5']."</option>
		 <option value=6>".$msg['typnoti_6']."</option>
		 </select></p>";*/
	
	//case à cocher wmo publications only
	$r.="<div class='scheckbox'><input type='checkbox' name=\"wmo_pub\" value='1' ".($wmo_pub?"checked='checked'":"")."/>".$msg['swmo_pub'];
	
	//champ wmo no collection : opérateur + boite texte
	$r.="<br/><span class='tabulation2' style='margin-left:30px;'></span><span class='search2_label'>".$msg['nocoll']." &nbsp;</span><input type='text' size='5' name='nocoll' value='".htmlentities($nocoll,ENT_QUOTES,$charset)."'/>";
	
	//champ td no collection : opérateur + boite texte
	$r.="<span class='search2_label' style='margin-left:10px'>".$msg['tdnocoll']." &nbsp;</span><input type='text' size='5' name='tdnocoll' value='".htmlentities($tdnocoll,ENT_QUOTES,$charset)."'/></div>";
	
	/*//case a cocher free full text
	$r.="<br/><br/><input type='checkbox' name=\"free\" value='1' ".($free?"checked='checked'":"")."/>&nbsp;".$msg['free_full_text']."</span></div>";
	
	//case a cocher doc numeriques
	/*$r.="<br/></span><input type='checkbox' name=\"doc_num\" value='1' ".($doc_num?"checked":"")."/>&nbsp;$msg[sfull_text]</span></div><br/>";*/

	return $r;
}

function search_other_function_clause() {
	global $code_langue_restrict,$annee_parution,$doc_num, $free, $topics, $wmo_pub, $regions, $typnoti, $nocoll, $tdnocoll;
	
	$custom_query = "SELECT DISTINCT notice_id FROM notices WHERE notice_id in";
	$r = "";
	
	/*if ($code_langue_restrict) {
		if ($r!="") $r.=" and notice_id in";
		$r.=" (SELECT DISTINCT notices.notice_id from notices LEFT JOIN notices_langues as a0 on (notices.notice_id = a0.num_notice) where a0.type_langue=0 and a0.code_langue LIKE '$code_langue_restrict')";
		}	

	if ($topics) {
		if ($r!="") $r.=" and notice_id in";
		$r.=" (SELECT DISTINCT notices.notice_id FROM notices LEFT JOIN notices_categories AS c1 ON (notices.notice_id = c1.notcateg_notice) LEFT JOIN categories AS c2 ON (c2.num_noeud = c1.num_noeud) where c2.libelle_categorie LIKE '$topics')" ;
	} 
	
	if ($regions) {
		if ($r!="") $r.=" and notice_id in";
		$r.=" (SELECT DISTINCT notices.notice_id FROM notices LEFT JOIN notices_categories AS c1 ON (notices.notice_id = c1.notcateg_notice) LEFT JOIN categories AS c2 ON (c2.num_noeud = c1.num_noeud) where c1.num_noeud='$regions')" ;
	} 
	
	if($typnoti) {
		if ($r!="") $r.=" and notice_id in";
		if ($typnoti==1){
			$r.= " (SELECT DISTINCT notices.notice_id FROM notices where ((notices.typdoc LIKE 'z') OR (notices.typdoc LIKE 'y')))";
		} elseif ($typnoti==2){
			$r.= " (SELECT DISTINCT notices.notice_id FROM notices where notices.typdoc LIKE 't')";
		} elseif ($typnoti==3){
			$r.= " (SELECT DISTINCT notices.notice_id FROM notices where notices.typdoc LIKE 'v')";
		} elseif ($typnoti==7){
			$r.= " (SELECT DISTINCT notices.notice_id FROM notices where notices.typdoc LIKE 'o')";
		} elseif ($typnoti==4){
			$r.= " (SELECT DISTINCT notices.notice_id FROM notices where notices.typdoc LIKE 'x' and notices.niveau_biblio LIKE 's')";
		} elseif ($typnoti==5){
			$r.= " (SELECT DISTINCT notices.notice_id FROM notices where notices.typdoc LIKE 'w' and notices.niveau_biblio LIKE 's')";
		} elseif ($typnoti==6){
			$r.= " (SELECT DISTINCT notices.notice_id FROM notices where notices.typdoc LIKE 'n')";
		}	
	}
	
	if ($free) {
		if ($r!="") $r.=" and notice_id in";
		$r.= " (SELECT distinct notices.notice_id from notices left join notices_custom_values as n1 on (notice_id=n1.notices_custom_origine and notices_custom_champ=1) left join notices_custom_values as n2 on (notice_id=n2.notices_custom_origine and n2.notices_custom_champ=2) where n1.notices_custom_integer=1 and n2.notices_custom_integer=1 and n1.notices_custom_origine is not null and n2.notices_custom_origine is not null)";
	}
	
	if ($doc_num) {
		if ($r!="") $r.=" and notice_id in";
		$r.=" (SELECT distinct explnum.explnum_notice from explnum,notices where notices.notice_id=explnum.explnum_notice) or notices.notice_id in (SELECT distinct bulletin_notice from bulletins,explnum where bulletin_id=explnum_bulletin)";
	}*/

	if ($wmo_pub){
		if ($r!="") $r.=" and notice_id in";
		if ($typnoti==3){
		$r.=" (SELECT distinct notices.notice_id from notices where ((notices.ed1_id=1) OR (notices.ed1_id=35) OR (notices.ed1_id=37) OR (notices.ed1_id=28) OR (notices.ed1_id=34) OR (notices.ed1_id=543) OR (notices.ed1_id=29) OR (notices.ed1_id=604)) OR ((notices.ed2_id=1) OR (notices.ed2_id=35) OR (notices.ed2_id=37) OR (notices.ed2_id=28) OR (notices.ed2_id=34) OR (notices.ed2_id=543) OR (notices.ed2_id=29) OR (notices.ed2_id=604)) AND (bulletins.bulletin)";	
		} else {
		$r.=" (SELECT distinct notices.notice_id from notices, bulletins where ((notices.ed1_id=1) OR (notices.ed1_id=35) OR (notices.ed1_id=37) OR (notices.ed1_id=28) OR (notices.ed1_id=34) OR (notices.ed1_id=543) OR (notices.ed1_id=29) OR (notices.ed1_id=604)) OR ((notices.ed2_id=1) OR (notices.ed2_id=35) OR (notices.ed2_id=37) OR (notices.ed2_id=28) OR (notices.ed2_id=34) OR (notices.ed2_id=543) OR (notices.ed2_id=29) OR (notices.ed2_id=604)))";	
		}
	}
	
	/*if ($annee_parution) {
		if ($r!="") $r.=" and notice_id in";
		$r.=" (SELECT distinct notices.notice_id from notices where notices.year >=".$annee_parution.")";		
	}*/
	
	if ($nocoll) {
		if ($r!="") $r.=" and notice_id in";
		$r.=" (SELECT distinct notices.notice_id from notices where notices.nocoll =".$nocoll." and ((notices.coll_id=13) or (notices.coll_id=23) or (notices.coll_id=24) or (notices.coll_id=25) or (notices.coll_id=27) or (notices.coll_id=28)))";	
	}
	
	if ($tdnocoll) {
		if ($r!="") $r.=" and notice_id in";
		$r.=" (SELECT distinct notices.notice_id from notices where notices.nocoll =".$tdnocoll." and ((notices.coll_id=14) or (notices.coll_id=48) or (notices.coll_id=44)))";	
	}
	
	if ($r!="") $r.=" and notice_id in";
	$r.= " (SELECT distinct notices.notice_id from notices LEFT JOIN notices_categories as c1 on (notices.notice_id = c1.notcateg_notice) where c1.num_noeud)";
	$r.= " and notice_id in (SELECT DISTINCT notices.notice_id from notices order by notices.year desc)";
	
	return $custom_query.$r;
}

function search_other_function_has_values() {
	global $code_langue_restrict,$annee_parution, $doc_num, $free, $topics, $wmo_pub, $regions, $typnoti, $nocoll, $tdnocoll;
	if ($code_langue_restrict||$annee_parution||$doc_num||$free||$annee_parution||$topics||$wmo_pub||$regions||$typnoti) 
	return true; 
	else return false;
}

function search_other_function_get_values() {
	global $code_langue_restrict, $annee_parution, $doc_num, $free, $topics, $wmo_pub, $regions, $typnoti, $nocoll, $tdnocoll;
	
	return serialize(array($code_langue_restrict, $annee_parution, $doc_num, $free, $topics, $wmo_pub, $regions, $typnoti, $nocoll, $tdnocoll));
}

function search_other_function_rec_history($n) {
    global $msg;
	global $code_langue_restrict,$annee_parution,$doc_num,$free,$topics, $wmo_pub, $regions, $typnoti, $nocoll, $tdnocoll;
	$_SESSION["code_langue_restrict".$n]=$code_langue_restrict;
	$_SESSION["annee_parution".$n]=$annee_parution;
	$_SESSION["doc_num".$n]=$doc_num;
	$_SESSION["free".$n]=$free;
	$_SESSION["topics".$n]=$topics;
	$_SESSION["wmo_pub".$n]=$wmo_pub;
	$_SESSION["regions".$n]=$regions;
	$_SESSION["nocoll".$n]=$nocoll;
	$_SESSION["tdnocoll".$n]=$tdnocoll;
	
    $_SESSION["typnoti".$n]=$typnoti;
	
	if ($_SESSION["typnoti".$n]) {
		$r2=sprintf($msg["simple_search_history_doc_type"],$doctype->table[$_SESSION["typdoc".$n]]);
	} else $r2=$msg["simple_search_history_all_doc_types"];
}

function search_other_function_get_history($n) {
	global $code_langue_restrict,$annee_parution,$doc_num,$free,$topics, $wmo_pub,$regions, $typnoti, $nocoll, $tdnocoll;
	$code_langue_restrict=$_SESSION["code_langue_restrict".$n];
	$annee_parution=$_SESSION["annee_parution".$n];
	$doc_num=$_SESSION["doc_num".$n];
	$free=$_SESSION["free".$n];
	$topics=$_SESSION["topics".$n];
	$wmo_pub=$_SESSION["wmo_pub".$n];
	$regions=$_SESSION["regions".$n];
	$typnoti=$_SESSION["typnoti".$n];
	$nocoll=$_SESSION["nocoll".$n];
	$tdnocoll=$_SESSION["tdnocoll".$n];
}

function search_other_function_human_query($n) {
	global $msg,$marc_liste_langues, $code_langue_restrict,$annee_parution,$doc_num,$free,$topics,$regions,$wmo_pub,$typnoti,$nocoll,$tdnocoll;
	if (!$marc_liste_langues) $marc_liste_langues=new marc_list('lang');{
	$r="";
	$code_langue_restrict=$_SESSION["code_langue_restrict".$n];
	}
	
	//message historique recherche langue
	if ($code_langue_restrict) {
		$r.=", ".$msg['langue_publication_query']." : ";
		$r.=$marc_liste_langues->table[$code_langue_restrict];	
	}
	
	//message annee parution
	if (!$annee_parution) {
		$annee_parution=$_SESSION["annee_parution".$n];
	}
	
	if($annee_parution) {
		$r.=", ".$msg['year_start'].">=";
		$r.=$annee_parution;
	}
	
	//message nocoll
	if (!$nocoll) {
		$nocoll=$_SESSION["nocoll".$n];
	}
	
	if($nocoll) {
		$r.=", ".$msg['nocoll']."=";
		$r.=$nocoll;
	}
	
	//message tdnocoll
	if (!$tdnocoll) {
		$tdnocoll=$_SESSION["tdnocoll".$n];
	}
	
	if($tdnocoll) {
		$r.=", ".$msg['tdnocoll']."=";
		$r.=$tdnocoll;
	}
	
	//message wmo publications
	if (!$wmo_pub) {
		$wmo_pub=$_SESSION["wmo_pub".$n];
	}
	
	if ($wmo_pub){
		$r.=", ".$msg['author_search']."/".$msg['publisher_search']."= WMO/OMM/BMO...";
	}
	
	//message topics
	if (!$topics) {
		$topics=$_SESSION["topics".$n];
	}
	
	if ($topics){
		$r.=", ".$msg['categories_search']."=";
		$r.=$topics;
	}
	
	//message  régions
	if (!$regions) {
	$regions=$_SESSION["regions".$n];
	}
	
	if($regions) {
		$r.=", ".$msg['regions_search']."=";
			if($regions==10630) {$r.=$msg['region_0'];}
			if($regions==10291) {$r.=$msg['region_1'];}
			if($regions==10378) {$r.=$msg['region_2'];}
			if($regions==10332) {$r.=$msg['region_3'];}
			if($regions==10918) {$r.=$msg['region_4'];}
			if($regions==10411) {$r.=$msg['region_5'];}
			if($regions==10395) {$r.=$msg['region_6'];}
			if($regions==10624) {$r.=$msg['region_antarctique'];}
			if($regions==10625) {$r.=$msg['region_arctique'];}
		}
	
	//message typdoc
	if (!$typnoti) {
		$typnoti=$_SESSION["typnoti".$n];
	}
	
	if($typnoti){
		$r.=", ".$msg['typdocdisplay_start']."= ";
		if($typnoti==1) {$r.=$msg['typnoti_1'];}
		elseif($typnoti==2) {$r.=$msg['typnoti_2'];}
		elseif($typnoti==3) {$r.=$msg['typnoti_3'];}
		elseif($typnoti==4) {$r.=$msg['typnoti_4'];}
		elseif($typnoti==5) {$r.=$msg['typnoti_5'];}
		elseif($typnoti==6) {$r.=$msg['typnoti_6'];}
		elseif($typnoti==7) {$r.=$msg['typnoti_7'];}
	}
	
	//message free full text
	if (!$free) {
		$free=$_SESSION["free".$n];
	}
	
	if ($free){
		$r.=", ".$msg['access_custom_free'];
	}
	
	//message doc_num
	if (!$doc_num) {
		$doc_num=$_SESSION["doc_num".$n];
	}
	
	if ($doc_num){
		$r.=", ".$msg['sfull_text'];
	}
	
        
	return $r;
}
