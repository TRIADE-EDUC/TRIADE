<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cababstract2unimarc.class.php,v 1.3 2023/08/28 14:04:13 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path, $include_path;
require_once("$class_path/marc_table.class.php");
require_once("$include_path/isbn.inc.php");
require_once($base_path."/admin/convert/convert.class.php");

class cababstract2unimarc extends convert {

	protected static function organize_line($tab_line){
		$res = array();
		for($i=0;$i<count($tab_line);$i++){
			$matches=array();
			if(preg_match("/^([A-Z0-9]{1,4}) +(.*)$/",$tab_line[$i],$matches)){
				$champ = $matches[1];
				if($res[$champ]) {
					$res[$champ] = $res[$champ]."###".trim($matches[2]);		
				} else $res[$champ] = trim($matches[2]);
			} else if(strpos(trim($tab_line[$i]),'ER') === 0){
				//FIN DE NOTICE
				break;
			}else{
				$res[$champ] = $res[$champ].'###'.trim($tab_line[$i]);
			}
		}
		return $res;
	}
	
	public static function convert_data($notice, $s, $islast, $isfirst, $param_path) {
		global $cols;
		global $ty;
		global $intitules;
		global $base_path,$origine;
		global $tab_functions;
		global $lot;
		global $charset;	
		
		if(mb_detect_encoding($notice) =='UTF-8' && $charset == "iso-8859-1")
			$notice = encoding_normalize::utf8_decode($notice);
	
		if (!$tab_functions) $tab_functions=new marc_list('function');
		$fields=explode("\n",$notice);
		$error="";
		if($fields)
			$data="<notice>\n";
		$lignes = static::organize_line($fields);
		//initialisation des champs
		$typdoc = $authors = $others_authors = $title = $serial_title = $issue_volume = $issue_number = $pagination = $year = $abstract = $lang = $doi = '';
		$publication_date = '';
		//Parcours
		foreach($lignes as $champ=>$value) {		
			switch($champ){		
				case 'PT':
					$typdoc = $value;
					break;
				case 'AU':
					$authors = $value;
					break;
				case 'TI' : 
					$title = $value;
					break;
				case 'SO':
					$serial_title = $value;
					break;
				case 'VL' : 
					$issue_volume = $value;
					break;
				case 'IS':
					$issue_number = $value;
					break;
				case 'PS':
					$pagination = $value;
					break;
				case 'PY':
					$dates = explode("/",$value);
					if($dates[0]) $year = $dates[0];
					if($dates[1]) $month = $dates[1];
					if($dates[2]) $day = $dates[2];
					$publication_date = $year;
					
					if($year && $month && $day){
						$date_sql = str_replace("/","-",$value);
						$mention_date = $value;
					} else if($year && $month && !$day){
						$date_sql = $year."-".$month."-01";
						$mention_date = $year."/".$month;
					} else if($year && !$month && !$day){
						$date_sql = $year."-01-01";
						$mention_date = $year;
					}	
					break;
				case 'AB':
					$abstract = $value;
					break;
				case 'LA':
					$lang = $value;
					break;
			}		
		}
		
		
		//SPECIFIQUE LIMAGRAIN...
		// On force le type de document
		$typdoc = "a";
		//Construction du fichier
		$data.= "<rs>n</rs>
			  <dt>".$typdoc."</dt>
			  <bl>a</bl>
			  <hl>2</hl>
			  <el>1</el>
			  <ru>i</ru>\n";	
		
		//Soyons sûr que le microtime ne sera plus le même..
		usleep(1);
		
		$data.="<f c='001' ind='  '>\n";
		$data.=htmlspecialchars(microtime(),ENT_QUOTES,$charset);
		$data.="</f>\n";
	
		if($langue){
			$data.="<f c='101' ind='1 '>\n";
			$data.="	<s c='a'>".htmlspecialchars(strtolower(substr($lang,0,3)),ENT_QUOTES,$charset)."</s>";
			$data.="</f>\n";		
		}
		$data.=static::get_converted_field_uni('200', 'a', implode(',',explode('###',$title)));
		$data.=static::get_converted_field_uni('215', 'a', $pagination);
		$data.=static::get_converted_field_uni('330', 'a', implode(',',explode('###',$abstract)));
		if($serial_title){
			$data.="<f c='461' ind='  '>\n";				
			$data.="	<s c='t'>".htmlspecialchars(implode(',',explode('###',$serial_title)),ENT_QUOTES,$charset)."</s>\n";	
			if($serial_issn) $data.="	<s c='x'>".htmlspecialchars($serial_issn,ENT_QUOTES,$charset)."</s>\n";	
			$data.="	<s c='9'>lnk:perio</s>\n";		
			$data.="</f>\n";
		}	
		if($issue_number || $issue_volume){
			$data.="<f c='463' ind='  '>\n";								
			if($issue_number && $issue_number) 
				$data.="	<s c='v'>".htmlspecialchars($issue_volume,ENT_QUOTES,$charset)."(".htmlspecialchars($issue_number,ENT_QUOTES,$charset).")</s>\n";
			else if($issue_number && !$issue_volume)
				$data.="	<s c='v'>".htmlspecialchars($issue_number,ENT_QUOTES,$charset)."</s>\n";
			else if(!$issue_number && $issue_volume)
				$data.="	<s c='v'>".htmlspecialchars($issue_volume,ENT_QUOTES,$charset)."</s>\n";
			if($date_sql)
				$data.="	<s c='d'>".htmlspecialchars($date_sql,ENT_QUOTES,$charset)."</s>\n";
			if($mention_date)
				$data.="	<s c='e'>".htmlspecialchars($mention_date,ENT_QUOTES,$charset)."</s>\n";
			$data.="	<s c='9'>lnk:bull</s>\n";		
			$data.="</f>\n";
		}
			
		if($authors){
			$first_auts = array();
			$first_auts = explode("###",$authors);
			$aut = explode(", ",array_shift($first_auts));
			$data.="<f c='700' ind='  '>\n";								
			$data.="	<s c='a'>".htmlspecialchars($aut[0],ENT_QUOTES,$charset)."</s>\n";
			$data.="	<s c='b'>".htmlspecialchars($aut[1],ENT_QUOTES,$charset)."</s>\n";
			if($aut[2]) $data.="	<s c='c'>".htmlspecialchars($aut[2],ENT_QUOTES,$charset)."</s>\n";
			$data.="</f>\n";
			if(is_array($first_auts) && count($first_auts)){
				$others_authors = implode('###',$first_auts);
			}
		}
		if($others_authors){
			$others = explode("###",$others_authors);
			for($i=0;$i<count($others);$i++){
				$aut = explode(", ",$others[$i]);
				$data.="<f c='701' ind='  '>\n";								
				$data.="	<s c='a'>".htmlspecialchars($aut[0],ENT_QUOTES,$charset)."</s>\n";
				$data.="	<s c='b'>".htmlspecialchars($aut[1],ENT_QUOTES,$charset)."</s>\n";
				if($aut[2]) $data.="	<s c='c'>".htmlspecialchars($aut[2],ENT_QUOTES,$charset)."</s>\n";
				$data.="</f>\n";
			}
		}
		if($doi){
			$doi = trim($doi);
			$data.=static::get_converted_field_uni('900', 'a', $doi, array('l' => 'DOI', 'n' => 'cp_doi_identifier'));
		}
		$data .= "</notice>\n";
		$r = array();
		if (!$error) $r['VALID'] = true; else $r['VALID']=false;
		$r['ERROR'] = $error;
		$r['DATA'] = $data;
		return $r;
	}
}
