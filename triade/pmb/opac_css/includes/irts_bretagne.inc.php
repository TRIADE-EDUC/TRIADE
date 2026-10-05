<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: irts_bretagne.inc.php,v 1.10.8.1 2025/03/25 07:32:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function search_other_function_filters() {
	global $typ_notice, $irts_bibli;
	
	$r ="<select name='irts_bibli'>";
	$r.="<option value=''>tous les sites</option>";
	$requete="select location_libelle,idlocation from docs_location where location_visible_opac=1";
	$result = pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($result)){
		while ($loc = pmb_mysql_fetch_object($result)) {
			$selected="";
			if ($irts_bibli==$loc->idlocation) {$selected="selected";}
			$r.= "<option value='$loc->idlocation' $selected>$loc->location_libelle</option>";
		}
	}
	$r.="</select>";

	$r.="<br/>Restreindre à <input type='checkbox' name=\"typ_notice[a]\" value='1' ".($typ_notice['a']?"checked":"")."/>&nbsp;Articles&nbsp;
		<input type='checkbox' name=\"typ_notice[b]\" value='1' ".($typ_notice['b']?"checked":"")."/>&nbsp;Numéros de revue&nbsp;
		<input type='checkbox' name=\"typ_notice[s]\" value='1' ".($typ_notice['s']?"checked":"")."/>&nbsp;Revues&nbsp;
		<input type='checkbox' name=\"typ_notice[m]\" value='1' ".($typ_notice['m']?"checked":"")."/>&nbsp;Tout sauf revues";
	return $r;
}

function search_other_function_clause() {
	global $typ_notice;
	global $irts_bibli;
	
	//Filtrage niveau bilbio
	$where="";
	$t_n_tab=array();
	if(is_array($typ_notice)){
		reset($typ_notice);
		foreach ($typ_notice as $key => $val) {
			$t_n_tab[]=$key;
		}
		$t_n=implode("','",$t_n_tab);
		if ($t_n) {
			$t_n="'".$t_n."'";
			$where .= " and niveau_biblio in (".$t_n.")";
		}
	}
	
	//Requête renvoyée
	if ($irts_bibli) {
		$r = "select distinct notice_id from (
				select distinct notice_id from notices join exemplaires on expl_notice=notice_id where expl_location=$irts_bibli $where 
				UNION
				select distinct notice_id from notices join bulletins on num_notice=notice_id join exemplaires on expl_bulletin=bulletin_id where expl_location=$irts_bibli $where
			) as s1";
	} else {
		$r = "select distinct notice_id from notices where 1 $where";
	}

	return $r;
}

function search_other_function_has_values() {
	global $typ_notice;
	global $irts_bibli;
	if ((count($typ_notice))||($irts_bibli)) return true; else return false;
}

function search_other_function_get_values(){
	global $typ_notice, $irts_bibli;
	return serialize($typ_notice)."---".$irts_bibli;
}

function search_other_function_rec_history($n) {
	global $typ_notice;
	global $irts_bibli;
	$_SESSION["irts_bibli".$n]=$irts_bibli;
	$_SESSION["typ_notice".$n]=$typ_notice;
}

function search_other_function_get_history($n) {
	global $typ_notice;
	global $irts_bibli;
	$irts_bibli=$_SESSION["irts_bibli".$n];
	$typ_notice=$_SESSION["typ_notice".$n];
}

function search_other_function_human_query($n) {
	global $typ_notice;
	global $irts_bibli;
	$r="";
	$irts_bibli=intval($_SESSION["irts_bibli".$n]);
	if ($irts_bibli) {
		$r="bibliotheque : ";
		$requete="select location_libelle from docs_location where idlocation='".$irts_bibli."' limit 1";
		$res=pmb_mysql_query($requete);
		$r.=pmb_mysql_result($res,0,0);
	}
	$notices_t=array("m"=>"Monographies","s"=>"Périodiques","a"=>"Articles","b"=>"Bulletins");
	$typ_notice=$_SESSION["typ_notice".$n];
	if (count($typ_notice)) {
		$r.="pour les types de notices ";
		reset($typ_notice);
		$t_l=array();
		foreach ($typ_notice as $key => $val) {
			$t_l[]=$notices_t[$key];
		}
		$r.=implode(", ",$t_l);
	}
	
	return $r;
}

function search_other_function_post_values() {
	global $irts_bibli,$typ_notice, $charset;
	
	$ret = "";
	if (is_array($typ_notice)) {
	    if ($typ_notice['m'] != "") {
	        $ret .= "<input type=\"hidden\" name=\"typ_notice[m]\" value=\"".htmlentities($typ_notice['m'], ENT_QUOTES, $charset)."\">";
	    }
	    if ($typ_notice['s'] != "") {
	        $ret .= "<input type=\"hidden\" name=\"typ_notice[s]\" value=\"".htmlentities($typ_notice['s'], ENT_QUOTES, $charset)."\">";
	    }
	    if ($typ_notice['b'] != "") {
	        $ret .= "<input type=\"hidden\" name=\"typ_notice[b]\" value=\"".htmlentities($typ_notice['b'], ENT_QUOTES, $charset)."\">";
	    }
	    if ($typ_notice['a'] != "") {
	        $ret .= "<input type=\"hidden\" name=\"typ_notice[a]\" value=\"".htmlentities($typ_notice['a'], ENT_QUOTES, $charset)."\">";
	    }
	}
	return "<input type=\"hidden\" name=\"irts_bibli\" value=\"".htmlentities($irts_bibli, ENT_QUOTES, $charset)."\">".$ret."\n";
	
}
