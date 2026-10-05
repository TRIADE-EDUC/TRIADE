<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: medline2unimarc.class.php,v 1.3 2022/07/22 15:32:29 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path, $include_path;
require_once("$class_path/marc_table.class.php");
require_once("$include_path/isbn.inc.php");
require_once($base_path."/admin/convert/convert.class.php");

class medline2unimarc extends convert {

	protected static function organize_line($tab_line){
		$res = array();
		
		for($i=0;$i<count($tab_line);$i++){
			$matches = array();
			if(preg_match("/([A-Z]{1,4}) *- (.*)/",$tab_line[$i],$matches)){
				$champ = $matches[1];
				if($res[$champ]) {
					$res[$champ] = $res[$champ].", ".trim($matches[2]);		
				} else $res[$champ] = trim($matches[2]);
			} else {
				$res[$champ] = $res[$champ]." ".trim($tab_line[$i]);
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
		
		if (!$tab_functions) $tab_functions=new marc_list('function');
		$fields=explode("\n",$notice);
		$error="";
		if($fields)
			$data="<notice>\n";
		$lignes = static::organize_line($fields);
		foreach($lignes as $champ=>$value) {		
			switch($champ){					
				case 'TI':
					//Titre principal
					$titre = $value;					
					break;
				case 'PL':
					//Editeur
					$editeur = $value;
					break;
				case 'AU':
					//Auteur principal
					$auteur = explode(", ",$value);
					break;
				case 'IS':
					//ISBN/ISSN
					$code = $value;
					$pos = strpos($code,"(");
					$endcode = substr($code,$pos);
					$code = trim(substr($code,0,$pos));				
					if(isISBN($code)){
						$infos_isbn=$code." ".$endcode;
					} elseif(isISSN($code)){
						$infos_issn=$code." ".$endcode;
					} else $error = "wrong ISBN/ISSN \n";
					break;
				case 'DP':
					//Date de publication
					$date = $value;
					if($date){	
						$date_elt = explode(' ',$date);	
						if($date_elt[0]) $year = $date_elt[0];
						if($date_elt[2]) $day = $date_elt[2];
						if($date_elt[1]) {
							$mois = $date_elt[1];
							switch($mois){
								case 'Jan':
									$month = "01";
								break;
								case 'Feb':
									$month = "02";
								break;
								case 'Mar':
									$month = "03";
								break;
								case 'Apr':
									$month = "04";
								break;
								case 'May':
									$month = "05";
								break;
								case 'Jun':
									$month = "06";
								break;
								case 'Jul':
									$month = "07";
								break;
								case 'Aug':
									$month = "08";
								break;
								case 'Sep':
									$month = "09";
								break;
								case 'Oct':
									$month = "10";
								break;
								case 'Nov':
									$month = "11";
								break;
								case 'Dec':
									$month = "12";
								break;
							}		
						}
						if($year && $month && $day) $date_sql = $year."-".$month."-".$day;
						else if($year && $month) $date_sql = $year."-".$month."-01";
						else if($year) $date_sql = $year."-01-01";
					}
					break;
				case 'IP':
					//Numéro de bulletin
					$bull_num = $value;
					break;
				case 'VI':
					//Volume
					$bull_vol = $value;
					break;
				case 'AB':
					//Résumé
					$resume = $value;
					break;
				case 'JT':
					//Titre du pério
					$perio_title = $value;
					break;
				case 'LA':
					//Langue
					$langue = $value;
					break;
				case 'TT':
					//Titre parallele
					$titre_parallele = $value;
					break;	
				case 'PG':
					//Pagination
					$pagination = $value;
					break;
				case 'PMID':
					//Pubmed ID
					$pubmed_id = $value;
					break;
				case 'PT':
					//Document Type
					$doc_type = $value;
					break;	
				case 'AID':
					//DOI
					$ids = explode(",",$value);
					if(is_array($ids)){
						for($i=0;$i<count($ids);$i++){
							if(strpos($ids[$i],"[doi]") !== false){
								$doi = trim(substr($ids[$i],0,strpos($ids[$i],"[doi]")));
							}
						}
					}
					break;
				case 'AD':
					$collectivite = $value;
					break;
				default:
					$data .= '';
				break;
			}
		}
		
		//Construction du fichier
		
		$data.= "<rs>n</rs>
			  <dt>a</dt>
			  <bl>a</bl>
			  <hl>2</hl>
			  <el>1</el>
			  <ru>i</ru>\n";	
		
		if($pubmed_id){
			$data.="<f c='001' ind='  '>\n";
			$data.=htmlspecialchars($pubmed_id,ENT_QUOTES,$charset);
			$data.="</f>\n";
		}
		$data.=static::get_converted_field_uni('010', 'a', $infos_isbn);
		$data.=static::get_converted_field_uni('101', 'a', $langue);
		if($titre){
			$data.="<f c='200' ind='  '>\n";								
			$data.="	<s c='a'>".htmlspecialchars($titre,ENT_QUOTES,$charset)."</s>\n";
			if($titre_parallele) $data.="	<s c='d'>".htmlspecialchars($titre_parallele,ENT_QUOTES,$charset)."</s>\n";
			$data.="</f>\n";
		}
		$data.=static::get_converted_field_uni('210', 'c', $editeur);
		$data.=static::get_converted_field_uni('215', 'a', $pagination);
		$data.=static::get_converted_field_uni('330', 'a', $resume);
		if($perio_title){
			$data.="<f c='461' ind='  '>\n";				
			$data.="	<s c='t'>".htmlspecialchars($perio_title,ENT_QUOTES,$charset)."</s>\n";	
			if($infos_issn) $data.="	<s c='x'>".htmlspecialchars($infos_issn,ENT_QUOTES,$charset)."</s>\n";	
			$data.="	<s c='9'>lnk:perio</s>\n";		
			$data.="</f>\n";
		}
		if($bull_num || $bull_vol || $date || $date_sql){
			$data.="<f c='463' ind='  '>\n";								
			if($bull_num && $bull_vol) 
				$data.="	<s c='v'>"."vol. ".htmlspecialchars($bull_vol,ENT_QUOTES,$charset).", no. ".htmlspecialchars($bull_num,ENT_QUOTES,$charset)."</s>\n";
			else if($bull_num && !$bull_vol)
				$data.="	<s c='v'>no. ".htmlspecialchars($bull_num,ENT_QUOTES,$charset)."</s>\n";
			else if(!$bull_num && $bull_vol)
				$data.="	<s c='v'>vol. ".htmlspecialchars($bull_vol,ENT_QUOTES,$charset)."</s>\n";
			if($date){
				$data.="	<s c='e'>".htmlspecialchars($date,ENT_QUOTES,$charset)."</s>\n";
			}
			if($date_sql){
				$data.="	<s c='d'>".htmlspecialchars($date_sql,ENT_QUOTES,$charset)."</s>\n";
			}
			$data.="	<s c='9'>lnk:bull</s>\n";		
			$data.="</f>\n";
		}
		
	    $coll_code = "710";
		if($auteur){
		    $data.=static::get_converted_field_uni('700', 'a', $auteur[0]);
		    if($auteur){
		        for($i=1;$i<count($auteur);$i++){
		            $data.=static::get_converted_field_uni('701', 'a', $auteur[$i]);
		        }
		    }
		    $coll_code = "711";
		}
		if($collectivite){
			$coll = explode(",",$collectivite,2);
			$data.="<f c='$coll_code' ind='0 '>\n";								
			$data.="	<s c='a'>".htmlspecialchars($coll[0],ENT_QUOTES,$charset)."</s>\n";
			$data.="	<s c='e'>".htmlspecialchars($coll[1],ENT_QUOTES,$charset)."</s>\n";
			$data.="</f>\n";
		}
		
		if($doc_type){
			switch($doc_type){
				case 'Abstracts':
				case 'Meeting Abstracts':
					$doctype = "Abstract";
					break;
				case 'Academic Dissertations':
					$doctype = "Thesis";
					break;
				case 'Annual Reports':
				case 'Technical Report':
					$doctype = "Report";
					break;
				case 'Book Reviews':
				case 'Review':
					$doctype = "Review";
					break;	
				case 'Classical Article':
				case 'Corrected and Republished Article':
				case 'Journal Article':
				case 'Newspaper Article':
					$doctype = "Article";
					break;
				case 'Comment':		
				case 'Published Erratum':	
					$doctype = "Erratum";
					break;
				case 'Congresses':
					$doctype = "Conference Proceedings";
					break;
				case 'Database':
					$doctype = "Database";
					break;
				case 'Dictionary':
					$doctype = "Dictionary";
					break;
				case 'Directory':
					$doctype = "Directory";
					break;
				case 'Editorial':
					$doctype = "Editorial";
					break;
				case 'Encyclopedias':
					$doctype = "Encyclopedia";
					break;
				case 'Letter':
					$doctype = "Letter";
					break;
				case 'Unpublished Works':
					$doctype = "Preprint";
					break;
				default:
					$doctype = "Article";
					break;
			}
			$data.=static::get_converted_field_uni('900', 'a', $doctype, array('l' => 'Sub-Type', 'n' => 'subtype'));
		}
		$data.=static::get_converted_field_uni('900', 'a', $doi, array('l' => 'DOI Id', 'n' => 'cp_doi_identifier'));
		if($pubmed_id){		
			$data.=static::get_converted_field_uni('856', 'u', 'http://www.ncbi.nlm.nih.gov/pubmed/'.$pubmed_id);
			$data.=static::get_converted_field_uni('900', 'a', $pubmed_id, array('l' => 'PUBMED Id', 'n' => 'cp_pubmed_identifier'));
		}
		$data .= "</notice>\n";
			
		$r = array();
		if (!$error) {
			$r['VALID'] = true; 
		}else {
			$r['VALID']=false;
		}
		$r['ERROR'] = $error;
		$r['DATA'] = $data;
		return $r;
	}
}
