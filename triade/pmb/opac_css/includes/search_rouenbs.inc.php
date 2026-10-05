<?php

function search_other_function_filters() {
	global $typdoc_multi, $rbs_bibli;
	global $charset;
	global $msg;

	global $gestion_acces_active,$gestion_acces_empr_notice;
	global $class_path;
	//droits d'acces emprunteur/notice
	$acces_j='';
	if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
		require_once("$class_path/acces.class.php");
		$ac= new acces();
		$dom_2= $ac->setDomain(2);
		$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
	}
	
	if($acces_j) {
		$statut_j='';
		$statut_r='';
	} else {
		$statut_j=',notice_statut';
		$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
	}
	
	$requete = "SELECT typdoc FROM notices $acces_j $statut_j where typdoc!='' $statut_r GROUP BY typdoc";
	$result = pmb_mysql_query($requete);
	$r .= " <span><table style='width:30%'><tr><td>";
	$r .= " <select name='typdoc_multi[]' multiple size='3'>";
	$r .= "  <option ";
	$r .=" value=''";
	if (is_array($typdoc_multi)) {
		if (in_array("", $typdoc_multi)) $r .=" selected";
	}
	$r .=">".$msg["simple_search_all_doc_type"]."</option>\n";
	$doctype = new marc_list('doctype');
	$obj=array();
	while (($rt = pmb_mysql_fetch_row($result))) {
		$obj[$rt[0]]=1;
	}	
	foreach ($doctype->table as $key=>$libelle){
		if ($obj[$key]==1){
			$r .= "  <option ";
			$r .= " value='$key'";
			if (is_array($typdoc_multi)) {
				if (in_array($key, $typdoc_multi)) $r .=" selected";
			}
			$r .= ">".htmlentities($libelle,ENT_QUOTES, $charset)."</option>\n";
		}
	}
	$r .= "</select></td><td>";
	$r.="<select name='rbs_bibli'>";
	$r.="<option value=''>".htmlentities($msg["search_loc_all_site"],ENT_QUOTES,$charset)."</option>";
	$requete="select location_libelle,idlocation from docs_location where location_visible_opac=1";
	$result = pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($result)){
		while ($loc = pmb_mysql_fetch_object($result)) {
			$selected="";
			if ($rbs_bibli==$loc->idlocation) {$selected="selected";}
			$r.= "<option value='$loc->idlocation' $selected>$loc->location_libelle</option>";
		}
	}
	$r.="</select>";
	$r.="</td></tr></table></span>";
	return $r;
}

function search_other_function_get_values(){
	global $typdoc_multi, $rbs_bibli;
	return serialize(array($typdoc_multi))."---".$rbs_bibli;
}

function search_other_function_clause() {
	global $typdoc_multi;
	global $rbs_bibli;

	$r = "";
	$where = "";
	$t_m_tab = array();
	$t_m = "";
	if (!empty($typdoc_multi) && is_array($typdoc_multi) && count($typdoc_multi)) {
		reset($typdoc_multi);
		// on ne remplit pas le tableau si la valeur 'tout type de document' est sélectionnée
		if (!in_array('', $typdoc_multi)) {
			$typdoc_multi = array_flip($typdoc_multi);
			foreach ($typdoc_multi as $key => $val) {
			    $t_m_tab[]=$key;
			}
			$typdoc_multi = array_flip($typdoc_multi);
		}
		$t_m=implode("','",$t_m_tab);
		if ($t_m) {
			$t_m="'".$t_m."'";
			$where .= " and typdoc in (".$t_m.")";
		}
	}
	if ($rbs_bibli) {
		$where .= " and notice_id in (select expl_notice from exemplaires where expl_location='$rbs_bibli' UNION select  bulletin_notice from bulletins join exemplaires on expl_bulletin=bulletin_id  where expl_location='$rbs_bibli' )";
	}
	if ($t_m || $rbs_bibli) {
		$r="select distinct notice_id from notices where 1 ".$where;
	}
	return $r;
}

function search_other_function_has_values() {
	global $typdoc_multi, $rbs_bibli;
	if ((!empty($typdoc_multi) && count($typdoc_multi))||($rbs_bibli)) {
	    return true; 
	} else {
	    return false;
	}
}

function search_other_function_rec_history($n) {
	global $typdoc_multi;
	global $rbs_bibli;
	$_SESSION["typdoc_multi".$n]=$typdoc_multi;
	$_SESSION["rbs_bibli".$n]=$rbs_bibli;
}

function search_other_function_get_history($n) {
	global $typdoc_multi;
	global $rbs_bibli;
	$typdoc_multi=$_SESSION["typdoc_multi".$n];
	$rbs_bibli=$_SESSION["rbs_bibli".$n];	
}

function search_other_function_human_query($n) {
	global $typdoc_multi;
	global $rbs_bibli;
	
	$r="";
	$typdoc_multi=$_SESSION["typdoc_multi".$n];
	if (!empty($typdoc_multi) && is_array($typdoc_multi) && count($typdoc_multi)) {
		$r.="pour les types de documents ";
		$doctype = new marc_list('doctype');
		reset($typdoc_multi);
		$t_d=array();
		foreach ($typdoc_multi as $val) {
			$t_d[]=$doctype->table[$val];
		}
		$r.=implode(", ",$t_d);
	}
	$rbs_bibli = intval($_SESSION["rbs_bibli".$n]);
	if ($rbs_bibli) {
		$r.="bibliotheque : ";
		$requete="select location_libelle from docs_location where idlocation='".$rbs_bibli."' limit 1";
		$res=pmb_mysql_query($requete);
		$r.=@pmb_mysql_result($res,0,0);
	}
	return $r;
}

function search_other_function_post_values() {
	global $typdoc_multi, $charset;
	global $rbs_bibli;
	$retour = "";
	if (is_array($typdoc_multi) && count($typdoc_multi)) {
		foreach($typdoc_multi as $v) {
		    $retour.= "<input type='hidden' name='typdoc_multi[]' value='".htmlentities($v, ENT_QUOTES, $charset)."' />\n";
		}
	}
	$retour .= "<input type=\"hidden\" name=\"rbs_bibli\" value=\"".htmlentities($rbs_bibli, ENT_QUOTES, $charset)."\">\n";
	
	return $retour;
}