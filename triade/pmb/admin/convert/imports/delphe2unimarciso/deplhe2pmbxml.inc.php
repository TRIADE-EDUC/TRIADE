<?php
/*
 * Created on 11 juil. 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function init_term_convert(){
	global $base_path;
	
	global $convert;
	$convert = array();
	$fp = fopen("$base_path/admin/convert/imports/delphe2unimarciso/TabCorrespDelphes.txt","r");
	while (!feof($fp)) {
		$data = fgetcsv($fp,4096,"\t");
		$convert[$data[0]] = array('aciege' => $data[2], 'delphes' => $data[3]);
	}
	fclose($fp);
}
init_term_convert();
function convert_delphe($notice, $s, $islast, $isfirst, $param_path) {
	global $charset;
	global $convert;
	
	$fields=explode("\t",$notice);
	$id=trim($fields[0]);
	$lang=trim($fields[1]);
	$titre=trim($fields[2]);
	$auteur_physiques=trim($fields[3]);
	$auteur_moraux=trim($fields[4]);
	$perio_name=trim($fields[5]);
	$date=trim($fields[6]);
	$resume=trim($fields[7]);
	$num_bulletin=trim($fields[8]);
	$pagin=trim($fields[9]);
	$desc_fre=trim($fields[10]);
	$desc_geo_fre=trim($fields[11]);
	$mots=trim($fields[12]);
	$societe=trim($fields[13]);
	$url=trim($fields[14]);
	
	//Construction du fichier
	$error="";
	if($fields)
		$data="
	<notice>";
	
	$data.= "
		<rs>n</rs>
		<dt>a</dt>
		<bl>a</bl>
		<hl>2</hl>
		<el>1</el>
		<ru>i</ru>";	
	//id
	$data.="
		<f c='001'>".htmlspecialchars($id,ENT_NOQUOTES,$charset)."</f>";
	
	//langues
	//peut etre multiple, 1er = langues de publication, 2nd et al.= langues orginales...
	$langues = explode("|",$lang);
	if(count($langues)){
		if(count($langues)>1) $ind = "1 ";
		else $ind = "0 ";
		$data.="
		<f c='101' ind='$ind'>";	
		for($i=0 ; $i<count($langues) ; $i++){
			$lang = strtolower(substr($langues[$i],0,3));
			if($lang == "fra") $lang = "fre";
			else $lang = "eng";
			if($i==0){
				$data.="
			<s c='a'>".$lang."</s>";
			}else{
				$data.="
			<s c='c'>".htmlspecialchars($lang,ENT_NOQUOTES,$charset)."</s>";
			}
		}
			$data.="
		</f>";	
	}	
	
	//titre
	$data.="
		<f c='200' ind=' 1'>";
	$data.="
			<s c='a'>".htmlspecialchars($titre,ENT_NOQUOTES,$charset)."</s>";
	$data.="
		</f>";	
	
	//pagination
	if($pagin){
		$data .= "
		<f c='215' ind='  '>
			<s c='a'>".htmlspecialchars($pagin,ENT_NOQUOTES,$charset)."</s>
		</f>";			
	}
	
	if($resume){
		$data.="
		<f c='330' ind='  '>
			<s c='a'>".htmlspecialchars($resume,ENT_NOQUOTES,$charset)."</s>
		</f>";
	}

	//titre pério
	if($perio_name){
		$data .= "
		<f c='461' ind='  '>
			<s c='t'>".htmlspecialchars($perio_name,ENT_NOQUOTES,$charset)."</s>
		</f>";
	}

	//infos bulletin
	if($date || $num_bulletin){
		$data .= "
		<f c='463' ind='  '>";
		if($date) $data.="
			<s c='d'>$date</s>";
		if($num_bulletin) $data.="
			<s c='v'>".htmlspecialchars($num_bulletin,ENT_NOQUOTES,$charset)."</s>";
		$data.="
		</f>";
	}

	//descripteurs
	if($desc_fre){
		$desc= explode("|",$desc_fre);
		if(count($desc)){
			foreach($desc as $term){
				$data.="
		<f c='606' ind='  '>
			<s c='a'>".htmlspecialchars($term,ENT_NOQUOTES,$charset)."</s>
		</f>";
//				if($convert[$term]){
//					$data.="
//		<f c='606' ind='# '>";
//					if($convert[$term]['aciege']!= ""){
//						$data.="
//			<s c='a'>".htmlspecialchars($convert[$term]['aciege'],ENT_NOQUOTES,$charset)."</s>";
//					}else {
//						$data.="
//			<s c='2'>delphes</s>
//			<s c='a'>".htmlspecialchars($convert[$term]['delphes'],ENT_NOQUOTES,$charset)."</s>";
//					}
//					$data.="
//		</f>";	
//				}else {
//					$data.="
//		<f c='610' ind='  '>
//			<s c='a'>".htmlspecialchars($term,ENT_NOQUOTES,$charset)."</s>
//		</f>";						
//				}	
			}
		}		
	}
	
	//descripteurs géo
	if($desc_geo_fre){
		$desc= explode("|",$desc_geo_fre);
		if(count($desc)){
			foreach($desc as $term){
				$data.="
		<f c='606' ind='  '>
			<s c='a'>".htmlspecialchars($term,ENT_NOQUOTES,$charset)."</s>
		</f>";	
//				if($convert[$term]){
//					$data.="
//		<f c='606' ind='# '>";
//					if($convert[$term]['aciege']!= ""){
//						$data.="
//			<s c='a'>".htmlspecialchars($convert[$term]['aciege'],ENT_NOQUOTES,$charset)."</s>";
//					}else {
//						$data.="
//			<s c='2'>delphes</s>
//			<s c='a'>".htmlspecialchars($convert[$term]['delphes'],ENT_NOQUOTES,$charset)."</s>";
//					}
//					$data.="
//		</f>";	
//				}else {
//					$data.="
//		<f c='610' ind='  '>
//			<s c='a'>".htmlspecialchars($term,ENT_NOQUOTES,$charset)."</s>
//		</f>";						
//				}		
			}
		}		
	}	
	//mots-clés
	if($mots){
		$keywords= explode("|",$mots);
		if(count($keywords)){
			foreach($keywords as $keyword){
				$data.="
		<f c='606' ind='  '>
			<s c='a'>".htmlspecialchars($keyword,ENT_NOQUOTES,$charset)."</s>
		</f>";				
			}
		}
	}
	
	//auteurs 
	$auteurs =array();
	if($auteur_physiques) $auteurs = explode("|",$auteur_physiques);
	if(count($auteurs)){
		for($i=0 ; $i<count($auteurs) ; $i++){
			if($i == 0 ) $field = "700";
			else $field = "701";
			$data .= "
		<f c='$field' ind='  '>";
			if(preg_match("/([^(]*)\(([^)]*)/",$auteurs[$i],$matches)){
				$data.="
			<s c='a'>".trim(htmlspecialchars($matches[1],ENT_NOQUOTES,$charset))."</s>
			<s c='b'>".trim(htmlspecialchars($matches[2],ENT_NOQUOTES,$charset))."</s>";
			}else{
				$data.="
			<s c='a'>".htmlspecialchars($auteurs[$i],ENT_NOQUOTES,$charset)."</s>";
			}
			$data .= "
		</f>";
		}
	}
	
	//collectivités
	$aut_coll = array();
	if($auteur_moraux) $aut_coll = explode("|",$auteur_moraux);
	if(count($aut_coll)){
		for($i=0 ; $i<count($aut_coll) ; $i++){
			if($i == 0 && count($auteurs) == 0) $field = "710";
			else $field = "711";
			$data .= "
		<f c='$field' ind='  '>
			<s c='a'>".htmlspecialchars($aut_coll[$i],ENT_NOQUOTES,$charset)."</s>
		</f>";
		}
	}
	
	//URL
	if($url){
		$data.="
		<f c='856' ind='  '>
			<s c='u'>".htmlspecialchars($url,ENT_NOQUOTES,$charset)."</s>
		</f>";		
	}
	
	//societe
	if($societe){
		$keywords= explode("|",$societe);
		if(count($keywords)){
			foreach($keywords as $keyword){
				$data.="
		<f c='606' ind='  '>
			<s c='2'>local</s>
			<s c='a'>".htmlspecialchars($keyword,ENT_NOQUOTES,$charset)."</s>
		</f>";				
			}
		}
	}	
	
	$data .= "
	</notice>";
	if (!$error) $r['VALID'] = true; else $r['VALID']=false;
	$r['ERROR'] = $error;
	$r['DATA'] = $data;
	return $r;
}
?>