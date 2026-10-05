<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: divers.inc.php,v 1.29 2023/02/24 14:50:33 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// formatdate() : retourne une date formatée comme il faut
function formatdate($date_a_convertir, $with_hour=0) {
	global $msg;
	
	if ($with_hour) $resultatdate=pmb_mysql_query("select date_format('".$date_a_convertir."', '".$msg["format_date_heure"]."') as date_conv ");
	else $resultatdate=pmb_mysql_query("select date_format('".$date_a_convertir."', '".$msg["format_date_sql"]."') as date_conv ");
	$date_conv=pmb_mysql_result($resultatdate,0,0);
	return $date_conv ;
	//return date($msg[1005],strtotime($date_a_convertir));
}

// formatdate_input() : retourne une date formatée comme il faut
function formatdate_input($date_a_convertir) {
	global $msg;

	$resultatdate=pmb_mysql_query("select date_format('".$date_a_convertir."', '".$msg["format_date_input_model"]."') as date_conv ");
	$date_conv=pmb_mysql_result($resultatdate,0,0);
	return $date_conv ;
}

// extraitdate() : retourne une date formatée comme il faut
function extraitdate($date_a_convertir) {
	global $msg;
	
	$date_a_convertir = str_replace ("-","/",$date_a_convertir);
	$date_a_convertir = str_replace (".","/",$date_a_convertir);
	$date_a_convertir = str_replace ("\\","/",$date_a_convertir);
	
	$format_local = str_replace ("%","",$msg["format_date_input_model"]);
	$format_local = str_replace ("-","",$format_local);
	$format_local = str_replace ("/","",$format_local);
	$format_local = str_replace ("\\","",$format_local);
	$format_local = str_replace (".","",$format_local);
	$format_local = str_replace (" ","",$format_local);
	$format_local = str_replace ($msg["format_date_input_separator"],"",$format_local);
	$date=array();
	list($date[substr($format_local,0,1)],$date[substr($format_local,1,1)],$date[substr($format_local,2,1)]) = sscanf($date_a_convertir,$msg["format_date_input"]) ;
	if ($date['Y'] && $date['m'] && $date['d']){
		 //$date_a_convertir = $date['Y']."-".$date['m']."-".$date['d'] ;
		 $date_a_convertir = sprintf("%04d-%02d-%02d",$date['Y'],$date['m'],$date['d']);
	} else {
		$date_a_convertir="";
	}
	return $date_a_convertir ;
}	

function detectFormatDate($date_a_convertir, $compl="01", $date_flot = false){
	global $msg;
	$date_array = array();
	$matches = [];
	if(preg_match("#(\d{4})[-/\.](\d{4})#",$date_a_convertir, $matches)){
	    //cas particulier des intervalles
	    //on ne tien compte de que la 1ère date
	    $date = detectFormatDate($matches[1]);
	}else if(preg_match("#(\d{4})[-/\.](\d{2})[-/\.](\d{2})#",$date_a_convertir, $matches)){
	    try{
	        $date = $matches[1]."-".$matches[2]."-".$matches[3];
	    }catch(Exception $e){
	        $date = "0000-00-00";
	    }
	}else if(preg_match("#(\d{1,2})[-/\.](\d{2})[-/\.](\d{4})#",$date_a_convertir, $matches)){
	    $tmp_date = new DateTime($matches[1]."-".$matches[2]."-".$matches[3]);
	    $date = date_format($tmp_date, 'Y-m-d');
	}else if(preg_match(getDatePattern(),$date_a_convertir)){
		$date = extraitdate($date_a_convertir);
	}elseif(preg_match(getDatePattern("short"),$date_a_convertir)){
		$format = str_replace ("%","",$msg["format_date_short"]);
		$format = str_replace ("-","",$format);
		$format = str_replace ("/","",$format);
		$format = str_replace ("\\","",$format);
		$format = str_replace (".","",$format);
		$format = str_replace (" ","",$format);
		$format = str_replace ($msg["format_date_input_separator"],"",$format);
		list($date_array[substr($format,0,1)],$date_array[substr($format,1,1)],$date_array[substr($format,2,1)]) = sscanf($date_a_convertir,$msg["format_date_short_input"]);
		if ($date_array['Y'] && $date_array['m']){
			if ($compl == "min") {
			    $date = sprintf("%04d-%02d-%02s",$date_array['Y'],$date_array['m'],"01");
			} elseif ($compl == "max") {
			    $date = sprintf("%04d-%02d-%02s",$date_array['Y'],$date_array['m'],date("t",mktime( 0, 0, 0, $date_array['m'], 1, $date_array['Y'] )));
			} else{
			    $date = sprintf("%04d-%02d-%02s",$date_array['Y'],$date_array['m'],$compl);
			}
		}elseif ($date_array['Y']){
			$date = sprintf("%04d-%02d-%02s",$date_array['Y'],"01","01");
		}else{
			$date = "0000-00-00";
		}
	}elseif(preg_match(getDatePattern("year"),$date_a_convertir,$matches)){
		if ($compl == "min") {
			$date = $matches[0]."-01-01";
		} elseif ($compl == "max") {
			$date = $matches[0]."-12-31";
		} else{
			$date = $matches[0]."-".$compl."-".$compl;
		}
		if ($date_flot === true) {
     	    if ($date_a_convertir[0] == '-'){
     	        $date = '-'.$matches[0];
     	    } else {
         	    $date = $matches[0];
     	    }
		}
	}else{
		$format = str_replace ("%",".",$msg["format_date"]);
		$format = str_replace ("-","",$format);
		$format = str_replace ("/","",$format);
		$format = str_replace ("\\","",$format);
		$format = str_replace (".","",$format);
		$format = str_replace (" ","",$format);
		$pattern= array();
		for($i=0 ; $i< strlen($format) ; $i++){
			switch($format[$i]){
				case "m" :
				case "d" :
					$pattern[$i] =  '\d{1,2}';
					break;
				case "Y" :
					$pattern[$i] =  '(\d{2})';
					break;
			}
		}
		if(preg_match("#".implode(".", $pattern)."#", $date_a_convertir,$matches)){
			if(substr(date("Y"),2,2) < $matches['1']){
				$correct_year = ((substr(date("Y"),0,2)*1)-1).$matches[1];
			}else{
				$correct_year = substr(date("Y"),0,2).$matches[1];
			}
			if(substr($format,-1) == "Y"){
				$date = detectFormatDate(substr($date_a_convertir,0,-2).$correct_year,$compl);
			}
		}else{
		    if (($date_flot === true) && preg_match("/^\-?\d+$/", $date_a_convertir)) {
		        $date = $date_a_convertir;
		    } else {
		        $date = "0000-00-00";
		    }
		}
	}

	return $date;
}

function getDatePattern($format="long"){
	global $msg;
	switch($format){
		case "long" :
			$format_date = str_replace ("%","",$msg["format_date"]);
			break;
		case "short" :
			$format_date = str_replace ("%","",$msg["format_date_short"]);
			break;
		case "year":
			$format_date = "Y"; 
			break;
	}
	$format_date = str_replace("-", " ", $format_date);
	$format_date = str_replace("/", " ", $format_date);
	$format_date = str_replace("\\", " ", $format_date);
	$format_date = str_replace(".", " ", $format_date);	
	$format_date_array = explode(" ", $format_date);
	$pattern = array();
	for ($i = 0; $i < count($format_date_array); $i++) {
	    switch ($format_date_array[$i]) {
			case "m" :
			case "d" :
				$pattern[$i] = '\d{1,2}';
			break;
			case "Y" :
				$pattern[$i] = '\d{4,}';
			break;
		}
	}
	return "#".implode("[-/\.]", $pattern)."#";
}

function getDojoPattern($date) {
	$formatted_date = str_replace (array("%d", "%m", "%y", "%D", "%M", "%Y"),array("dd","MM","yy","DD","MMMM","yyyy"),$date);
	if(strpos($formatted_date, '%') !== false) {
		return '';
	} else {
		return $formatted_date;
	}
}

// verif_date permet de vérifier si une date saisie est valide
function verif_date($date) {
	$mysql_date= extraitdate($date);
	$rqt= "SELECT DATE_ADD('" .$mysql_date. "', INTERVAL 0 DAY)";
	if($result=pmb_mysql_query($rqt))
		if($row = pmb_mysql_fetch_row($result))	
			if($row[0]){
				return $row[0];
			}
	return false;
}

function compose_date($date) {
	// on recherche à recomposer une date entière valide.
	if(is_numeric($date)) {
		//c'est un année, on rajoute 01/01/ devant
		$date_gen="01/01/".sprintf("%04d",$date);
	} else {
		$param=explode("/",$date);
		if(count($param)== 2) {
			$date_gen="01/".$date;
		} else if(count($param)== 3) {
			$date_gen=$date;
		}
		
	}
	return verif_date($date_gen);	
}
			