<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_agroparistech.inc.php,v 1.13 2023/08/29 15:31:35 dbellamy Exp $

/*
 *  ATTENTION CE FICHIER EST EN UTF-8
 */

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once($class_path."/notice_relations.class.php");

function renseigne_cp_agro($val,$notice_id,$type="notices"){
	$nom=$val["n"];
	$valeur=$val["a"];
	if(!trim($nom) || !trim($valeur) || !$notice_id){
		return false;
	}

	//on va chercher les informations sur le champs
	$rqt = "SELECT idchamp, type, datatype FROM ".$type."_custom WHERE name='" . addslashes(trim($nom)) . "'";
	$res = pmb_mysql_query($rqt);
	if (!pmb_mysql_num_rows($res))
		return false;

	$cp=pmb_mysql_fetch_object($res);
	
	if($cp->type != $val["t"]){
		return false;
	}
	
	//On enregistre la valeur au bon endroit
	switch ($cp->type) {
		case "list":
			//On est sur une liste
			switch ($cp->datatype) {
				case "integer":
					$requete="select ".$type."_custom_list_value from ".$type."_custom_lists where ".$type."_custom_list_lib='".addslashes(trim($valeur))."' and ".$type."_custom_champ='".$cp->idchamp."' ";
					$resultat=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($resultat)) {
						$value2=pmb_mysql_result($resultat,0,0);
					} else {
						$requete="select max(".$type."_custom_list_value*1) from ".$type."_custom_lists where ".$type."_custom_champ='".$cp->idchamp."' ";
						$resultat=pmb_mysql_query($requete);
						$max=@pmb_mysql_result($resultat,0,0);
						$n=$max+1;
						$requete="insert into ".$type."_custom_lists (".$type."_custom_champ,".$type."_custom_list_value,".$type."_custom_list_lib) values('".$cp->idchamp."',$n,'".addslashes(trim($valeur))."')";
						if(!pmb_mysql_query($requete)) return false;
						$value2=$n;
					}
					$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_integer) values('".$cp->idchamp."','".$notice_id."','".$value2."')";
					if(!pmb_mysql_query($requete)) return false;
					break;
				default:
					$requete="select ".$type."_custom_list_value from ".$type."_custom_lists where ".$type."_custom_list_lib='".addslashes(trim($valeur))."' and ".$type."_custom_champ='".$cp->idchamp."' ";
					$resultat=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($resultat)) {
						$value2=pmb_mysql_result($resultat,0,0);
					} else {
						$requete="select ".$type."_custom_list_value from ".$type."_custom_lists where ".$type."_custom_list_value='".addslashes(trim($valeur))."' and ".$type."_custom_champ='".$cp->idchamp."' ";
						$resultat=pmb_mysql_query($requete);
						if (pmb_mysql_num_rows($resultat)) {
							$value2=pmb_mysql_result($resultat,0,0);
						} else {
							$requete="insert into ".$type."_custom_lists (".$type."_custom_champ,".$type."_custom_list_value,".$type."_custom_list_lib) values('".$cp->idchamp."','".addslashes(trim($valeur))."','".addslashes($valeur)."')";
							if(!pmb_mysql_query($requete)) return false;
							$value2=trim($valeur);
						}
					}
					$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_".$cp->datatype.") values('".$cp->idchamp."','".$notice_id."','".$value2."')";
					if(!pmb_mysql_query($requete)) return false;
					break;
			}
			break;
		case "url":
			$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_".$cp->datatype.") values('".$cp->idchamp."','".$notice_id."','".addslashes(trim($val["c"]))."')";
			if(!pmb_mysql_query($requete)) return false;
			break;
		case "resolve":
			$mes_pp= new parametres_perso($type);
			if($mes_pp->get_formatted_output(array($val["c"]),$cp->idchamp) == $val["b"]){
				$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_".$cp->datatype.") values('".$cp->idchamp."','".$notice_id."','".addslashes($val["c"])."')";
				if(!pmb_mysql_query($requete)) return false;
			}else{
				return false;
			}
			break;
		case "query_list":
		case "query_auth":
			$mes_pp= new parametres_perso($type);
			if($mes_pp->get_formatted_output(array($val["c"]),$cp->idchamp) == $valeur){
				$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_".$cp->datatype.") values('".$cp->idchamp."','".$notice_id."','".addslashes($val["c"])."')";
				if(!pmb_mysql_query($requete)) return false;
			}else{
				return false;
			}
			break;
		default:
			switch ($cp->datatype) {
				case "small_text":
					$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_small_text) values('".$cp->idchamp."','".$notice_id."','".addslashes(trim($valeur))."')";
					if(!pmb_mysql_query($requete)) return false;
					break;
				case "int":
				case "integer":
					$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_integer) values('".$cp->idchamp."','".$notice_id."','".addslashes(trim($valeur))."')";
					if(!pmb_mysql_query($requete)) return false;
					break;
				case "text":
					$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_text) values('".$cp->idchamp."','".$notice_id."','".addslashes(trim($valeur))."')";
					if(!pmb_mysql_query($requete)) return false;
					break;
				case "date":
					$requete="insert into ".$type."_custom_values (".$type."_custom_champ,".$type."_custom_origine,".$type."_custom_date) values('".$cp->idchamp."','".$notice_id."','".addslashes(decoupe_date(trim($valeur)))."')";
					if(!pmb_mysql_query($requete)) return false;
					break;
			}
			break;
	}
	return true;
}

function recup_noticeunimarc_suite($notice) {
	global $infos_4XX;
	global $bl,$hl,$isbn_only,$isbn_dedoublonnage;
	global $tit_200a,$serie_200;
	global $info_003;
	global $info_900;
	global $info_336, $info_337;
	global $issn_011;
	if(!$isbn_dedoublonnage || ($isbn_only == 1)){
		$issn_011[0].="_pasToucheACa";
	}
	$zones = array(
		412,
		413,
		421,
		422,
		423,
		430,
		431,
		432,
		433,
		434,
		435,
		436,
		437,
		440,
		441,
		442,
		443,
		444,
		445,
		446,
		447,
		451,
		451,
		452,
		452,
		453,
		454,
		455,
		456,
		520
	);
	
	$infos_4XX = array();
	$info_003 = array();
	
	$record = new iso2709_record($notice, AUTO_UPDATE);
	$bl=$record->inner_guide['bl'];
	$hl=$record->inner_guide['hl'];	
	$info_003=$record->get_subfield("003");
	$info_336=$record->get_subfield("336","a");
	$info_337=$record->get_subfield("337","a");
	foreach($zones as $zone){
		$infos_4XX[$zone] = $record->get_subfield($zone,"0","t","x");
	}
	
	$info_105=$record->get_subfield("105","a");
	if(trim($info_105[0])){
		//Illustration
		if(($tmp=substr($info_105[0],0,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_105_illustration";
			$info_900[]=$val;
		}
		if(($tmp=substr($info_105[0],1,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_105_illustration";
			$info_900[]=$val;
		}
		if(($tmp=substr($info_105[0],2,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_105_illustration";
			$info_900[]=$val;
		}
		if(($tmp=substr($info_105[0],3,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_105_illustration";
			$info_900[]=$val;
		}
		
		//Nature
		if(($tmp=substr($info_105[0],4,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_105_nature";
			$info_900[]=$val;
		}
		if(($tmp=substr($info_105[0],5,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_105_nature";
			$info_900[]=$val;
		}
		if(($tmp=substr($info_105[0],6,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_105_nature";
			$info_900[]=$val;
		}
		if(($tmp=substr($info_105[0],7,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_105_nature";
			$info_900[]=$val;
		}
		
		//Info
		if(substr($info_105[0],8,1) == "1"){
			$val=array();
			$val["a"]="Congrès";
			$val["t"]="list";
			$val["n"]="cp_105_info";
			$info_900[]=$val;
		}
		if(substr($info_105[0],9,1) == "1"){
			$val=array();
			$val["a"]="Constitué de mélange";
			$val["t"]="list";
			$val["n"]="cp_105_info";
			$info_900[]=$val;
		}
		if(substr($info_105[0],10,1) == "1"){
			$val=array();
			$val["a"]="Contient un index";
			$val["t"]="list";
			$val["n"]="cp_105_info";
			$info_900[]=$val;
		}
		
		//Genre
		if(($tmp=substr($info_105[0],11,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_105_genre";
			$info_900[]=$val;
		}
		
		//biblio
		if(($tmp=substr($info_105[0],12,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_105_biblio";
			$info_900[]=$val;
		}
	}
	
	$info_110=$record->get_subfield("110","a");
	if(trim($info_110[0])){
		$val=array();
		$val["a"]=$info_110[0];
		$val["t"]="text";
		$val["n"]="cp_110";
		$info_900[]=$val;
		//Type de ressource continue
		if(($tmp=substr($info_110[0],0,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_110_typePer";
			$info_900[]=$val;
		}
		//Périodicité
		if(($tmp=substr($info_110[0],1,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_110_periodicite";
			$info_900[]=$val;
		}
		//Régularité
		if(($tmp=substr($info_110[0],2,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_110_regularite";
			$info_900[]=$val;
		}
		//Type de publication de référence
		if(($tmp=substr($info_110[0],3,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_110_typepubli";
			$info_900[]=$val;
		}
		//Nature du contenu
		if(($tmp=substr($info_110[0],4,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_110_contenu";
			$info_900[]=$val;
		}
		if(($tmp=substr($info_110[0],5,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_110_contenu";
			$info_900[]=$val;
		}
		if(($tmp=substr($info_110[0],6,1)) && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_110_contenu";
			$info_900[]=$val;
		}
		//Congres
		$tmp=substr($info_110[0],7,1);
		if(($tmp !== "") && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_110_congres";
			$info_900[]=$val;
		}
	}
	
	$info_135=$record->get_subfield("135","a");
	if(trim($info_135[0])){
		//Type de contenu electronique
		$tmp=substr($info_135[0],0,1);
		if(($tmp !== "") && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_135_type";
			$info_900[]=$val;
		}
		
		//Support
		$tmp=substr($info_135[0],1,1);
		if(($tmp !== "") && $tmp != "|"){
			$val=array();
			$val["a"]=$tmp;
			$val["t"]="list";
			$val["n"]="cp_135_support";
			$info_900[]=$val;
		}
	
	}
	
	//pour les monographies, le 200$a et 200$i s'inverse...
	if($bl == "m"){
		if(clean_string($serie_200[0]['i']) != ""){
			$tmp_buffer = $serie_200[0]['i'];
			$serie_200[0]['i'] = $tit_200a[0];
			$tit_200a[0] = $tmp_buffer;
		}
	}
} 
	
function import_new_notice_suite() {
	global $dbh ;
	global $notice_id ;
	global $bl,$hl;
	global $infos_4XX;
	global $hierarchic_level;
	global $bibliographic_level	;
	global $doc_type;
	global $origine_notice;
	global $notices_crees;
	global $issn_011,$info_900;
	global $tit_200a;
	global $isbn;
	global $statutnot;
	global $info_003;
	global $info_336, $info_337;
	global $isbn_only,$isbn_dedoublonnage;
	
	if(isset($bibliographic_level) && isset($hierarchic_level)){
		$niveau_biblio = $bibliographic_level.$hierarchic_level;
	}else{
		$niveau_biblio =$bl.$hl;
	}
	//num_notice = fille
	//linked_notice = mere
	
	$sens = array(
		'mother' => array(
			"linked_notice",
			"num_notice"
		),
		'child' => array(
			"num_notice",
			"linked_notice"
		)
	);
	
	
	$link_type = array(
		'412' => array(
			'code' => "v",
			'sens_link' => "child"
		),
		'413' => array(
			'code' => "v",
			'sens_link' => "mother"
		),
		'421' => array(
			'code' => "e",
			'sens_link' => "mother"
		),
		'422' => array(
			'code' => "e",
			'sens_link' => "child"
		),
		'423' => array(
			'code' => "k",
			'sens_link' => "child"
		),
		'430' => array(
			'code' => "l",
			'sens_link' => "child"
		),
		'431' => array(
			'code' => "o",
			'sens_link' => "mother"
		),
		'432' => array(
			'code' => "t",
			'sens_link' => "child"
		),
		'433' => array(
			'code' => "o",
			'sens_link' => "mother"
		),
		'434' => array(
			'code' => "m",
			'sens_link' => "child"
		),
		'435' => array(
			'code' => "s",
			'sens_link' => "child"
		),
		'436' => array(
			'code' => "n",
			'sens_link' => "mother"
		),
		'437' => array(
			'code' => "o",
			'sens_link' => "mother"
		),
		'440' => array(
			'code' => "l",
			'sens_link' => "mother"
		),
		'441' => array(
			'code' => "o",
			'sens_link' => "child"
		),
		'442' => array(
			'code' => "t",
			'sens_link' => "mother"
		),
		'443' => array(
			'code' => "o",
			'sens_link' => "child"
		),
		'444' => array(
			'code' => "m",
			'sens_link' => "mother"
		),
		'445' => array(
			'code' => "s",
			'sens_link' => "mother"
		),
		'446' => array(
			'code' => "o",
			'sens_link' => "child"
		),
		'447' => array(
			'code' => "n",
			'sens_link' => "child"
		),
		'451' => array(
			'code' => "u",
			'sens_link' => "child"
		),
		'452' => array(
			'code' => "p",
			'sens_link' => "child"
		),
		'453' => array(
			'code' => "h",
			'sens_link' => "mother"
		),
		'454' => array(
			'code' => "h",
			'sens_link' => "child"
		),
		'455' => array(
			'code' => "q",
			'sens_link' => "mother"
		),
		'456' => array(
			'code' => "q",
			'sens_link' => "child"
		),
		'520' => array(
			'code' => "f",
			'sens_link' => "child"
		)
	);

	//dédoublonnage !
	if($isbn && $isbn_dedoublonnage){
		$query = "select notice_id from notices where code like '".$isbn."' and notice_id != ".$notice_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			notice::del_notice($notice_id);
			$notice_id = $row->notice_id;
			pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_expl_".addslashes(SESSid).".inc', '".addslashes("La notice (".$tit_200a[0].", ".$isbn.") n'a pas été reprise car elle existe déjà en base (notice id: ".$notice_id.")")."') ") ;
		}
	}
	
	if(!$isbn_dedoublonnage || ($isbn_only == 1)){
		$rq="UPDATE notices SET code=REPLACE(code, '_pasToucheACa', '') WHERE notice_id = ".$notice_id;
		pmb_mysql_query($rq);
	
	}elseif($issn_011[0]){
		$query = "select notice_id from notices where code like '".$issn_011[0]."' and notice_id != ".$notice_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				if(in_array($row->notice_id,$notices_crees)){
					$old = new notice($row->notice_id);
					$old->replace($notice_id);
					$tab = array_flip($notices_crees);
					unset($tab[$row->notice_id]);
					$notices_crees = array_flip($tab);
				}else{
					notice::del_notice($notice_id);
					$notice_id = $row->notice_id;
					pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_expl_".addslashes(SESSid).".inc', '".addslashes("La notice (".$tit_200a[0].", ".$isbn.") n'a pas été reprise car elle existe déjà en base (notice id: ".$notice_id.")")."') ") ;
				}
			}
		}		
	}

	$n_gen_plus="";
	if(count($info_336)){
		foreach ( $info_336 as $value ) {
       		if($tmp=trim($value)){
       			if($n_gen_plus)$n_gen_plus.="\n";
       			$n_gen_plus.=$tmp;
       		}
		}
	}
	if(count($info_337)){
		foreach ( $info_337 as $value ) {
       		if($tmp=trim($value)){
       			if($n_gen_plus)$n_gen_plus.="\n";
       			$n_gen_plus.=$tmp;
       		}
		}
	}
	
	if($n_gen_plus){
		$requ="UPDATE notices SET n_gen=IF(n_gen != '',CONCAT(n_gen,'\n".addslashes($n_gen_plus)."'),'".addslashes($n_gen_plus)."') WHERE notice_id = ".$notice_id;
		if(!pmb_mysql_query($requ)){
			echo "Requête echoué: ".$requ."<br/>";
		}
	}

	switch($niveau_biblio){
		case "s1" :
		case "s0" :
			foreach($infos_4XX as $key => $children){
				foreach($children as $child){
					$issn = "";
					//on commence par chercher si la notice existe
					$issn = traite_code_ISSN($child['x']);
					if($issn){
						$query = "select notice_id from notices where code ='".$issn."' and niveau_biblio = 's' and niveau_hierar = '1'";
						$result = pmb_mysql_query($query);
						if(!pmb_mysql_num_rows($result)){
							//la notice n'existe pas, il faut la creer...

							/* Origine de la notice */
							$origine_not['nom']=clean_string($origine_notice[0]['b']);
							$origine_not['pays']=clean_string($origine_notice[0]['a']);
							$orinot_id = origine_notice::import($origine_not);
							if ($orinot_id==0) $orinot_id=1 ;
							
							$query = "insert into notices set 
								typdoc = '".$doc_type."',
								tit1 = '".addslashes(clean_string($child['t']))."',
								code = '".$issn."',
								niveau_biblio = 's',
								niveau_hierar = '1',
								statut = ".$statutnot.",
								origine_catalogage = '".$orinot_id."',
								create_date = sysdate(),
								update_date = sysdate()
							";
							pmb_mysql_query($query);
							$child_id = pmb_mysql_insert_id();
							$notices_crees[$child[0]]=$child_id;
							notice::majNotices($child_id);
							notice::majNoticesGlobalIndex($child_id);
							notice::majNoticesMotsGlobalIndex($child_id);
						}else{
							$child_id = pmb_mysql_result($result,0,0);
						}
						if($child_id){
							// on regarde si une relation similaire existe deja...
							$query = "select relation_type from notices_relations where relation_type = '".$link_type[$key]['code']."' and ((num_notice = ".$notice_id." and linked_notice = ".$child_id.") or (num_notice = ".$child_id." and linked_notice = ".$notice_id."))";
							$result = pmb_mysql_query($query);
							
							if(!pmb_mysql_num_rows($result)){
								$ranking = 0;
								$query = "select count(ranking) from notices_relations where relation_type = '".$link_type[$key]['code']."' and ";
								if($link_type[$key]['sens_link'] == "mother"){
									$query.= "num_notice = ".$child_id;
								}else{
									$query.= "num_notice = ".$notice_id;
								}
								$result = pmb_mysql_query($query);
								if(pmb_mysql_num_rows($result)) $ranking = pmb_mysql_result($result,0,0);
								if($sens[$link_type[$key]['sens_link']][0] == 'num_notice') {
									notice_relations::insert_from_import($notice_id, $child_id, $link_type[$key]['code'], ($ranking+1));
								} else {
									notice_relations::insert_from_import($child_id, $notice_id, $link_type[$key]['code'], ($ranking+1));
								}
							}
						}
					}
				}
			}
			break;
	}
	
	if(count($info_900)){
		for($i=0;$i<count($info_900);$i++){
			if(trim($info_900[$i]["a"])){
				if(!renseigne_cp_agro($info_900[$i],$notice_id)){
					pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_expl_".addslashes(SESSid).".inc', '".addslashes("La valeur  : ".$info_900[$i]["a"]." n'a pas été reprise dans le champ personnalisé : ".$info_900[$i]["n"]." car le champ n'existe pas ou n'est pas défini de la même façon")."') ") ;
				}
			}
		}
	}
	
	if($tmp=trim($info_003[0])){
		$requete="SELECT notices_custom_origine FROM notices_custom_values WHERE notices_custom_champ=22 AND notices_custom_origine='".$notice_id."' AND notices_custom_small_text='".addslashes($tmp)."' ";
		$res=pmb_mysql_query($requete);
		if($res && pmb_mysql_num_rows($res)){
			
		}else{
			$requete="INSERT INTO notices_custom_values(notices_custom_champ, notices_custom_origine, notices_custom_small_text) VALUES('22','".$notice_id."','".addslashes($tmp)."')";
			pmb_mysql_query($requete);
		}
	}
}	

function traite_exemplaires () {

}

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	return import_expl::export_traite_exemplaires($ex);
}