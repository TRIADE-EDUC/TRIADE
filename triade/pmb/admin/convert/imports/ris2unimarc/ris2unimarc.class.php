<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ris2unimarc.class.php,v 1.4.2.1 2024/08/01 09:41:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path, $include_path;
require_once("$class_path/marc_table.class.php");
require_once("$include_path/isbn.inc.php");
require_once($base_path."/admin/convert/convert.class.php");

class ris2unimarc extends convert {

	protected static function organize_line($tab_line){
		$res = array();
		
		for($i=0;$i<count($tab_line);$i++){
			$matches = array();
			if(preg_match("/([A-Z0-9]{1,4}) *- (.*)/",$tab_line[$i],$matches)){
				$champ = $matches[1];
				if(isset($res[$champ]) && $res[$champ]) {
					$res[$champ] = $res[$champ]."###".trim($matches[2]);		
				} else {
				    $res[$champ] = trim($matches[2]);
				}
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
		
		if(mb_detect_encoding($notice) =='UTF-8' && $charset == "iso-8859-1")
			$notice = encoding_normalize::utf8_decode($notice);
			
		if (!$tab_functions) $tab_functions=new marc_list('function');
		$fields=explode("\n",$notice);
		$error=$warning="";
		if($fields)
			$data="<notice>\n";
		$lignes = static::organize_line($fields);
		//initialisation des champs
		$titre = $titre_other = $editeur_nom = $editeur_ville = $auteur_principal = $infos_isbn = $infos_issn = $url = $mention_date = $date_sql = '';
		$subtype = $notes = $start_page = $end_page = $keywords = $collectivite = $bull_num = $bull_vol = $resume = $perio_title = '';
		$auteur_secondaire = $autres_auteurs = $doi = '';
		//Parcours
		foreach($lignes as $champ=>$value) {		
			switch($champ){						
				case 'CT':
				case 'TI':
				case 'T1':
					//Titre principal
					$titre = $value;				
				break;
				case 'T2':
					//Autre info sur titre
					$titre_other = $value;
				break;
				case 'PB':
					//Editeur
					$editeur_nom = $value;
				break;
				case 'CY':
					//Editeur
					$editeur_ville = $value;
				break;
				case 'AU':
				case 'A3':
				case 'A4':
					//Autres auteurs
					$autres_auteurs = $value;
				break;
				case 'A1':
					//Auteur principal
					$auteur_principal = $value;
				break;
				case 'A2':
					//Auteur secondaire
					$auteur_secondaire = $value;
				break;			
				case 'SN':
					//ISBN/ISSN
					$code = trim($value);
					$pos = strpos($code,"(");
					if ($pos !== false) $code = substr($code,0,$pos);
					if(isISBN($code)){
						$infos_isbn=$code;
					} elseif(isISSN($code)){
						$infos_issn=$code;
					} else {
						$infos_isbn=$infos_issn=$code;
						$warning = "wrong ISBN/ISSN \n";
					}
				break;
				case 'UR':
					//URL
					$url = $value;
				break;
				case 'PY':
					//Date de publication (YYYY/MM/DD)
					$dates = explode("/",$value);
					if (!empty($dates[0])) {
					    $year = $dates[0];
					} else {
					    $year = '';
					}
					if (!empty($dates[1])) {
					    $month = $dates[1];
					} else {
					    $month = '';
					}
					if (!empty($dates[2])) {
					    $day = $dates[2];
					} else {
					    $day = '';
					}
					$publication_date = $year;
					if(!isset($lignes['Y1']) && $year && $month && $day){
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
				case 'Y1' :
					$dates = explode("/",$value);
					if (!empty($dates[0])) {
					    $year = $dates[0];
					} else {
					    $year = '';
					}
					if (!empty($dates[1])) {
					    $month = $dates[1];
					} else {
					    $month = '';
					}
					if (!empty($dates[2])) {
					    $day = $dates[2];
					} else {
					    $day = '';
					}
					if(!isset($lignes['PY'])){
						$publication_date = $year;
					}
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
				case 'TY':
					//Document type
					switch($value){
						case 'ABST':
							$subtype='Abstract';
						break;
						case 'BOOK':
							$subtype='Book';
						break;
						case 'CHAP':
							$subtype='Chapter';
						break;
						case 'COMP':
							$subtype='Computing Program';
						break;
						case 'CONF':
							$subtype='Conference Proceedings';
						break;
						case 'INPR':
							$subtype='Preprint';
						break;
						case 'NEWS':
						case 'JFULL':
							$subtype='Journal';
						break;
						case 'MGZN':
						case 'JOUR':
							$subtype='Article';
						break;
						case 'MAP':
							$subtype='Map';
						break;
						case 'UNPB':
						case 'RPRT':
							$subtype='Report';
						break;
						case 'SLIDE':
							$subtype='Presentation';
						break;
						case 'THES':
							$subtype='Thesis';
						break;
						default :
							$subtype='Article';
						break;
					}				
				break;
				case 'N1':
				case 'N2':
					//Notes
					if($notes){
						$notes.='###';
					}
					$notes.= $value;
				break;	
				case 'SP':
					//Start page
					$start_page = $value;
				break;
				case 'EP':
					//End page
					$end_page = $value;
				break;
				case 'KW':
					//Mots cles
					$keywords = $value;	
				break;
				case 'AD':
					//Collectivite
					$collectivite = $value;
				break;
				case 'IS':
				case 'IP':
					//Numéro de bulletin
					$bull_num = $value;
					break;
				case 'VL':
				case 'VI':
					//Volume
					$bull_vol = $value;
					break;
				case 'AB':
					//Résumé
					$resume = $value;
					break;
				case 'JF':
					//Titre complet du pério
					$perio_title = $value;
					break;
				case 'JO' :
					//Titre standard du pério
					if(!isset($lignes['JF'])){
					$perio_title = $value;
					}
					break;
				case 'DO' :
					//Numéro de DOI
					$doi = $value;
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
		
		//Soyons sûr que le microtime ne sera plus le même..
		usleep(1);
		
		$data.="<f c='001' ind='  '>\n";
		$data.=htmlspecialchars(microtime(),ENT_QUOTES,$charset);
		$data.="</f>\n";

		$data.=static::get_converted_field_uni('010', 'a', $infos_isbn);
	
		if($titre){
			$data.="<f c='200' ind='  '>\n";								
			$data.="	<s c='a'>".htmlspecialchars($titre,ENT_QUOTES,$charset)."</s>";
			if($titre_other) $data.="	<s c='e'>".htmlspecialchars($titre_other,ENT_QUOTES,$charset)."</s>";
			$data.="</f>\n";
		}
		if($editeur_nom || !empty($publication_date) || $editeur_ville){
			$data.="<f c='210' ind='  '>\n";				
			if($editeur_ville) $data.="	<s c='a'>".htmlspecialchars($editeur_ville,ENT_QUOTES,$charset)."</s>\n";		
			if($editeur_nom) $data.="	<s c='c'>".htmlspecialchars($editeur_nom,ENT_QUOTES,$charset)."</s>\n";
			if(!empty($publication_date)) $data.="	<s c='d'>".htmlspecialchars($publication_date,ENT_QUOTES,$charset)."</s>";	
			$data.="</f>\n";
		}	
		if($start_page || $end_page){
			$data.="<f c='215' ind='  '>\n";				
			if($start_page && $end_page) $data.="	<s c='a'>".htmlspecialchars($start_page."-".$end_page,ENT_QUOTES,$charset)."</s>\n";
			if(!$start_page && $end_page) $data.="	<s c='a'>".htmlspecialchars($end_page,ENT_QUOTES,$charset)."</s>\n";
			if($start_page && !$end_page) $data.="	<s c='a'>".htmlspecialchars($start_page,ENT_QUOTES,$charset)."</s>\n";			
			$data.="</f>\n";
		}	
		if($notes){
			$note = explode('###',$notes);
			$doi ="";
			$pubmedid = "";
			for($i=0;$i<count($note);$i++){
				if(strpos($note[$i],"doi:")!== false) {
					$doi = $note[$i];
				} else if (strpos($note[$i],"PubMed ID:")!== false){
					$pubmedid =  $note[$i];
				} else {				
					if(strlen($note[$i]) > 9000){
						$word =wordwrap($note[$i],9000,"####");
						$words = explode("####",$word);
						for($j=0;$j<count($words);$j++){
							$data.=static::get_converted_field_uni('300', 'a', $words[$j]);
						}
					} else {
						$data.=static::get_converted_field_uni('300', 'a', $note[$i]);
					}
				}
			}	
		}
		$data.=static::get_converted_field_uni('330', 'a', $resume);
		if($perio_title){
			$data.="<f c='461' ind='  '>\n";				
			$data.="	<s c='t'>".htmlspecialchars($perio_title,ENT_QUOTES,$charset)."</s>\n";	
			if($infos_issn) $data.="	<s c='x'>".htmlspecialchars($infos_issn,ENT_QUOTES,$charset)."</s>\n";	
			$data.="	<s c='9'>lnk:perio</s>\n";		
			$data.="</f>\n";
		}	
		if($bull_num || $bull_vol){
			$data.="<f c='463' ind='  '>\n";								
			if($bull_num && $bull_vol) 
				$data.="	<s c='v'>"."vol. ".htmlspecialchars($bull_vol,ENT_QUOTES,$charset).", no. ".htmlspecialchars($bull_num,ENT_QUOTES,$charset)."</s>\n";
			else if($bull_num && !$bull_vol)
				$data.="	<s c='v'>no. ".htmlspecialchars($bull_num,ENT_QUOTES,$charset)."</s>\n";
			else if(!$bull_num && $bull_vol)
				$data.="	<s c='v'>vol. ".htmlspecialchars($bull_vol,ENT_QUOTES,$charset)."</s>\n";
			if($date_sql)
				$data.="	<s c='d'>".htmlspecialchars($date_sql,ENT_QUOTES,$charset)."</s>\n";
			if($mention_date)
				$data.="	<s c='e'>".htmlspecialchars($mention_date,ENT_QUOTES,$charset)."</s>\n";
			$data.="	<s c='9'>lnk:bull</s>\n";		
			$data.="</f>\n";
		}
		if($keywords){
			$mots = explode('###',$keywords);
			for($i=0;$i<count($mots);$i++){
				$data.="<f c='610' ind='0 '>\n";
				$data.="	<s c='a'>".htmlspecialchars($mots[$i],ENT_QUOTES,$charset)."</s>\n";
				$data.="</f>\n";
			}
		}
		
		if($auteur_principal){
			$first_auts = array();
			$first_auts = explode("###",$auteur_principal);
			$aut = explode(", ",array_shift($first_auts));
			$data.="<f c='700' ind='  '>\n";								
			$data.="	<s c='a'>".htmlspecialchars($aut[0],ENT_QUOTES,$charset)."</s>\n";
			if (!empty($aut[1])) {
                $data.="	<s c='b'>".htmlspecialchars($aut[1],ENT_QUOTES,$charset)."</s>\n";
			}
			if (!empty($aut[2])) {
			    $data.="	<s c='c'>".htmlspecialchars($aut[2],ENT_QUOTES,$charset)."</s>\n";
			}
			$data.="</f>\n";
			if(is_array($first_auts) && count($first_auts)){
				$autres_auteurs = implode('###',$first_auts).($autres_auteurs? '###' : '').$autres_auteurs;
		}
		}
		
		if($collectivite){
			$collectivites = explode("###",$collectivite);
			if((count($collectivites) == 1) && !$auteur_principal) {
				$coll_elt = explode(", ",$collectivites[0],2);
				$coll_nom = $coll_elt[0];
				$coll_pays = trim(substr($coll_elt[1],(strrpos($coll_elt[1],", ")+1)));
				$coll_lieu = trim(substr($coll_elt[1],0,-(strlen($coll_pays)+2)));
				$data.="<f c='710' ind='0 '>\n";								
				$data.="	<s c='a'>".htmlspecialchars($coll_nom,ENT_QUOTES,$charset)."</s>\n";
				$data.="	<s c='e'>".htmlspecialchars($coll_lieu,ENT_QUOTES,$charset)."</s>\n";
				$data.="	<s c='m'>".htmlspecialchars($coll_pays,ENT_QUOTES,$charset)."</s>\n";
				$data.="</f>\n";
			} else {
				for($i=0;$i<count($collectivites);$i++){
					$coll_elt = explode(", ",$collectivites[$i],2);
					$coll_nom = $coll_elt[0];
					$coll_pays = trim(substr($coll_elt[1],(strrpos($coll_elt[1],", ")+1)));
					$coll_lieu = trim(substr($coll_elt[1],0,-(strlen($coll_pays)+2)));
					
					$data.="<f c='711' ind='0 '>\n";								
					$data.="	<s c='a'>".htmlspecialchars($coll_nom,ENT_QUOTES,$charset)."</s>\n";
					$data.="	<s c='e'>".htmlspecialchars($coll_lieu,ENT_QUOTES,$charset)."</s>\n";
					$data.="	<s c='m'>".htmlspecialchars($coll_pays,ENT_QUOTES,$charset)."</s>\n";
					$data.="</f>\n";
				}
			} 
		}
		if($autres_auteurs){
			$others = explode("###",$autres_auteurs);
			for($i=0;$i<count($others);$i++){
				$aut = explode(", ",$others[$i]);
				$data.="<f c='701' ind='  '>\n";								
				$data.="	<s c='a'>".htmlspecialchars($aut[0],ENT_QUOTES,$charset)."</s>\n";
				if (!empty($aut[1])) {
				    $data.="	<s c='b'>".htmlspecialchars($aut[1],ENT_QUOTES,$charset)."</s>\n";
				}
				if (!empty($aut[2])) {
				    $data.="	<s c='c'>".htmlspecialchars($aut[2],ENT_QUOTES,$charset)."</s>\n";
				}
				$data.="</f>\n";
			}
		}
		if($auteur_secondaire){
			$secs = explode("###",$auteur_secondaire);
			for($i=0;$i<count($secs);$i++){
				$aut = explode(", ",$secs[$i]);
				$data.="<f c='702' ind='  '>\n";								
				$data.="	<s c='a'>".htmlspecialchars($aut[0],ENT_QUOTES,$charset)."</s>\n";
				if (!empty($aut[1])) {
				    $data.="	<s c='b'>".htmlspecialchars($aut[1],ENT_QUOTES,$charset)."</s>\n";
				}
				if (!empty($aut[2])) {
				    $data.="	<s c='c'>".htmlspecialchars($aut[2],ENT_QUOTES,$charset)."</s>\n";
				}
				$data.="</f>\n";
			}
		}
		
		$data.=static::get_converted_field_uni('856', 'u', $url);
		$data.=static::get_converted_field_uni('900', 'a', $subtype, array('l' => 'Sub-Type', 'n' => 'subtype'));
		if($doi){
			$doi = trim(str_replace("doi:","",$doi));
			$data.=static::get_converted_field_uni('900', 'a', $doi, array('l' => 'DOI', 'n' => 'cp_doi_identifier'));
		}
		if (!empty($pubmedid)){	
			$pubmedid = trim(str_replace("PubMed ID:","",$pubmedid));
			$data.=static::get_converted_field_uni('900', 'a', $pubmedid, array('l' => 'PUBMED', 'n' => 'cp_pubmed_identifier'));
		}
		$data .= "</notice>\n";
	
		$r = array();
		if (!$error) $r['VALID'] = true; else $r['VALID']=false;
		$r['ERROR'] = $error;
		$r['WARNING'] = $warning;
		$r['DATA'] = $data;
		return $r;
	}
}
