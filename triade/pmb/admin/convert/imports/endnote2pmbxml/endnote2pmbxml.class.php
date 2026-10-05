<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: endnote2pmbxml.class.php,v 1.2 2022/04/21 07:34:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path, $include_path;
require_once("$class_path/marc_table.class.php");
require_once("$include_path/isbn.inc.php");
require_once($base_path."/admin/convert/convert.class.php");

class endnote2pmbxml extends convert {

	protected static function organize_line($tab_line){
		$res = array();
		
		for($i=0;$i<count($tab_line);$i++){
			$matches = array();
			if(preg_match("/(%[A-Z0-9@+]{1}) (.*)/",$tab_line[$i],$matches)){
				$champ = $matches[1];
				if($res[$champ]) {
					$res[$champ] = $res[$champ]."###".trim($matches[2]);		
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
				case '%T':
					//Titre principal
					$titre = $value;				
				break;
				case '%B':
					//Autre info sur titre
					$titre_other = $value;
				break;
				case '%Q':
					//Autre info sur titre
					$titre_parallel = $value;
				break;
				case '%C':
					//Editeur
					$editeur_ville = $value;
				break;
				case '%Y':
				case '%A':
					//Autres auteurs
					$autres_auteurs = $value;
				break;
				case '%E':
					//Auteur secondaire
					$auteur_secondaire = $value;
				break;			
				case '%@':
					//ISBN/ISSN
					$code = $value;
					if(isISBN($code)){
						$infos_isbn=$code;
					} elseif(isISSN($code)){
						$infos_issn=$code;
					} else $error = "wrong ISBN/ISSN \n";
				break;
				case '%U':
					//URL
					$url = $value;
				break;
				case '%D':
					//Date de publication (YYYY/MM/DD)
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
				case '%0':
					
					/*switch($value){
						case 'Book':
						case 'Edited Book':
						case 'Electronic Book':
							$subtype='Book';
						break;
						case 'Computer Program':
							$subtype='Computing Program';
						break;
						case 'Conference Proceedings':
						case 'Conference Paper':	
							$subtype='Conference Proceedings';
						break;
						case 'Unpublished Work':
							$subtype='Preprint';
						break;
						case 'Online Multimedia':
							$subtype='Interactive Multimedia';
						break;
						case 'Journal Article':
						case 'Magazine Article':	
						case 'Electronic Article':
						case 'Newspaper Article':
							$subtype='Article';
						break;
						case 'Map':
							$subtype='Map';
						break;
						case 'Report':
							$subtype='Report';
						break;
						case 'Online Database':
							$subtype='Database';
						break;
						case 'Thesis':
							$subtype='Thesis';
						break;
						default :
							$subtype='Article';
						break;
					}	*/	
					
					//Document type
					$subtype=$value;		
				break;
				case '%Z':
					//Notes
					$notes = $value;
				break;	
				case '%P':
					//End page
					$page = $value;
				break;
				case '%K':
					//Mots cles
					$keywords = $value;	
				break;
				case '%+':
					//Collectivite
					$collectivite = $value;
				break;
				case '%N':
					//Numéro de bulletin
					$bull_num = $value;
					break;
				case '%V':
					//Volume
					$bull_vol = $value;
					break;
				case '%X':
					//Résumé
					$resume = $value;
					break;
				case '%J':
					//Titre du pério
					$perio_title = $value;
					break;
				case '%G':
					$langue = $value;
					break;
				case '%M':
					$id_endnote = $value;
					break;
				case '%I':
					$publisher = $value;
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
		
		$data.="<f c='001' ind='  '>\n";
		$data.=htmlspecialchars($id_endnote,ENT_QUOTES,$charset);
		$data.="</f>\n";
		
		$data.=static::get_converted_field_uni('010', 'a', $infos_isbn);
		$data.=static::get_converted_field_uni('011', 'a', $infos_issn);
		$data.=static::get_converted_field_uni('101', 'a', $langue);
		
		if($titre){
			$data.="<f c='200' ind='  '>\n";								
			$data.="	<s c='a'>".htmlspecialchars($titre,ENT_QUOTES,$charset)."</s>";
			if($titre_other) $data.="	<s c='e'>".htmlspecialchars($titre_other,ENT_QUOTES,$charset)."</s>";
			if($titre_parallel) $data.="	<s c='d'>".htmlspecialchars($titre_parallel,ENT_QUOTES,$charset)."</s>";
			$data.="</f>\n";
		}
		$data.=static::get_converted_field_uni('210', 'c', $publisher);
		$data.=static::get_converted_field_uni('215', 'a', $page);
			
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
		
		if($collectivite){
			$collectivites = explode("###",$collectivite);
			if((count($collectivites) == 1)) {
				$coll_elt = explode(", ",$collectivites[0],3);
				$coll_infos = explode(", ",$coll_elt[2],2);
				$coll_nom = $coll_infos[0];
				$coll_pays = trim(substr($coll_infos[1],(strrpos($coll_infos[1],", ")+1)));
				$coll_lieu = trim(substr($coll_infos[1],0,-(strlen($coll_pays)+2)));
				$data.="<f c='710' ind='0 '>\n";								
				$data.="	<s c='a'>".htmlspecialchars($coll_nom,ENT_QUOTES,$charset)."</s>\n";
				$data.="	<s c='e'>".htmlspecialchars($coll_lieu,ENT_QUOTES,$charset)."</s>\n";
				$data.="	<s c='m'>".htmlspecialchars($coll_pays,ENT_QUOTES,$charset)."</s>\n";
				$data.="</f>\n";
			} else {
				for($i=0;$i<count($collectivites);$i++){
					$coll_elt = explode(", ",$collectivites[$i],3);
					$coll_infos = explode(", ",$coll_elt[2],2);
					$coll_nom = $coll_infos[0];
					$coll_pays = trim(substr($coll_infos[1],(strrpos($coll_infos[1],", ")+1)));
					$coll_lieu = trim(substr($coll_infos[1],0,-(strlen($coll_pays)+2)));
					$data.="<f c='710' ind='0 '>\n";								
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
				$data.="	<s c='b'>".htmlspecialchars($aut[1],ENT_QUOTES,$charset)."</s>\n";
				if($aut[2]) $data.="	<s c='c'>".htmlspecialchars($aut[2],ENT_QUOTES,$charset)."</s>\n";
				$data.="</f>\n";
			}
		}
		if($auteur_secondaire){
			$secs = explode("###",$auteur_secondaire);
			for($i=0;$i<count($secs);$i++){
				$aut = explode(", ",$secs);
				$data.="<f c='702' ind='  '>\n";								
				$data.="	<s c='a'>".htmlspecialchars($aut[0],ENT_QUOTES,$charset)."</s>\n";
				$data.="	<s c='b'>".htmlspecialchars($aut[1],ENT_QUOTES,$charset)."</s>\n";
				if($aut[2]) $data.="	<s c='c'>".htmlspecialchars($aut[2],ENT_QUOTES,$charset)."</s>\n";
				$data.="</f>\n";
			}
		}
		
		$data.=static::get_converted_field_uni('856', 'u', $url);
		$data.=static::get_converted_field_uni('900', 'a', $subtype, array('l' => 'Sub-Type', 'n' => 'subtype'));
		$data .= "</notice>\n";
	
		$r = array();
		if (!$error) $r['VALID'] = true; else $r['VALID']=false;
		$r['ERROR'] = $error;
		$r['DATA'] = $data;
		return $r;
	}
}
