<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: category_auto.class.php,v 1.9 2019/12/30 15:40:43 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$include_path/parser.inc.php");
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/categories.class.php");
require_once($class_path."/noeuds.class.php");


class category_auto {

    public function _construct() {
    	
    }
    
    public static function get_info_categ($record){
    	global $include_path,$tabl_categ_has_recovered,$tabl_categ_recovered,$tabl_categ_recovery;
		$tabl_categ_recovered=array();
		if(!isset($tabl_categ_has_recovered)){
			$tabl_categ_has_recovered=array();
			$tabl_categ_recovery=array();
	
			if(file_exists($include_path."/category_auto/import_category.xml")){
				$tabl_categ_recovery=_parser_text_no_function_(file_get_contents($include_path."/category_auto/import_category.xml"),"AUTHORITY");
			}
			
			foreach ( $tabl_categ_recovery as $root ) {
				foreach ( $root as $thes ) {
				    foreach ( $thes["CATEGORY"] as $root_field ) {
					    foreach ( $root_field["FIELD"] as $field_val ) {
							$name_field=$field_val["CODE"];
							if(!empty($field_val["AUTHORITY_NUMBER"])){
								$tmp=array();
								$tmp["field"]=$name_field;
								$tmp["subfield"]=$field_val["AUTHORITY_NUMBER"];
								$tabl_categ_has_recovered[]=$tmp;
							}
							if(!empty($field_val["ORDER"]) && $field_val["ORDER"] == "import"){
								$tmp=array();
								$tmp["field"]=$name_field;
								$tmp["subfield"]="";
								$tabl_categ_has_recovered[]=$tmp;
							}else{
								category_auto::browse_subfields($field_val["SUBFIELD"],$name_field,$tabl_categ_has_recovered);
							}
						}
					}
				}
			}
		}
		
		if(count($tabl_categ_has_recovered)){
			foreach ( $tabl_categ_has_recovered as $value ) {
	       		$tabl_categ_recovered[$value["field"].$value["subfield"]]=$record->get_subfield_array_array($value["field"],$value["subfield"]);
			}
		}
    }
    
    /*Si $tabl_categ_lib est un tableau alors les catégories ne seront pas crées mais elles seront enregistrées dans ce tableau
    Retourne un tableau de la forme suivante quelque soit les paramètres à l'import :
    Array(
    [0] => Array(
            [wording] => libellé de la catégorie // Vide dans le cas de la reprise hiérarchisée sinon toujours renseigné
            [id_authority] => numéro d'autorité // Présent si dans le fichier import_category.xml on a définit le paramère authority_number pour le tag field et que l'on a autant de authority_number dans le champ unimarc que de catégorie reliée à la notice dans PMB
            				  Dans le cas d'une reprise d'un champ de façon hiérarchique authority_number sera associé au terme de plus bas niveau
            [id_pmb] => id_noeud de PMB // Présent si le noeud a été créé dans la base (si fonction appelée sans paramètre)
        )
    )
    Dans le cas d'une reprise hiérarchique sans créatation direct (Z-3950) la gestion des autorités n'est pas encore réalisée
    */
    /*
     * DG (05/12/2019)
     * Non conforme Exakat mais le passage en tableau change le passage des conditions
     * On est d'accord que le code est mauvais à la base mais je ne vois pas comment le corriger
     */
    public static function save_info_categ(&$tabl_categ_lib=""){
    	global $tabl_categ_recovery,$tabl_categ_recovered;
    	$tabl_link_authority=array();
    	/*echo "<pre>";
    	print_r($tabl_categ_recovered);
    	echo "</pre>";*/
		if(count($tabl_categ_recovered)){
			global $incr_categ;
			$incr_categ=-1;
			foreach ( $tabl_categ_recovery as $root ) {
				foreach ( $root as $thes ) {
					$obj_thes= new thesaurus($thes["ID"]);
					if($obj_thes ->num_noeud_racine){//Test pour savoir si le thésaurus existe
					    foreach ( $thes["CATEGORY"] as $field_root ) {
					    	$root_node_object="";
					    	switch ($field_root["ID"]) {
								case "TOP":
									$root_node_object=new noeuds($obj_thes ->num_noeud_racine);
									break;
								case "ORPHELINS":
									$root_node_object=new noeuds($obj_thes->num_noeud_orphelins);
									break;
								case "NONCLASSES":
									$root_node_object=new noeuds($obj_thes->num_noeud_nonclasses);
									break;
								default:
									$root_node_object=new noeuds($field_root["ID"]);
									if($root_node_object->num_thesaurus != $obj_thes ->id_thesaurus){
										$root_node_object="";
									}
									break;
							}
					    	if($root_node_object){
					    		$root_node_number=$root_node_object->id_noeud;
					    	}else{
					    		$root_node_number=0;
					    	}
						    foreach ( $field_root["FIELD"] as $field_val ) {
								$name_field=$field_val["CODE"];
								if($field_val["ORDER"] == "import"){
									//on reprends la liste des sous champs dans l'ordre du fichier de notice
									$subfield=array();
									foreach ( $field_val["SUBFIELD"] as $subfield_root ) {
										$subfield[$subfield_root["CODE"]]=$subfield_root;
									}
									for ($_1=0; $_1<count($tabl_categ_recovered[$name_field]); $_1++) {
										$wording="";
										for ($_2=0; $_2<count($tabl_categ_recovered[$name_field][$_1]); $_2++) {
											if(($info_subfield=$subfield[$tabl_categ_recovered[$name_field][$_1][$_2]["label"]]) && ($tmp=$tabl_categ_recovered[$name_field][$_1][$_2]["content"])){
												if($wording)$wording.=$info_subfield["PREFIX"];
												$wording.=$tmp;
												if($info_subfield["SUFFIX"])$wording.=$info_subfield["SUFFIX"];
											}
										}
										
										$tabl_autho_temp=array();
										$tabl_autho_temp["wording"]=$wording;
										if($field_val["AUTHORITY_NUMBER"] && (count($tabl_categ_recovered[$name_field.$field_val["AUTHORITY_NUMBER"]][$_1]) == 1)){
											$tabl_autho_temp["id_authority"]=$tabl_categ_recovered[$name_field.$field_val["AUTHORITY_NUMBER"]][$_1][0];
										}
										if(!is_array($tabl_categ_lib)){
											$tabl_autho_temp["id_pmb"]=category_auto::build_categ($wording,$obj_thes ->id_thesaurus,$root_node_number);
										}else{
											$tmp_build=array();
											$tmp_build["wording"]=$wording;
											$tmp_build["id_thes"]=$obj_thes ->id_thesaurus;
											$tmp_build["id_parent"]=$root_node_number;
											$tabl_categ_lib[]=$tmp_build;
										}
										$tabl_link_authority[]=$tabl_autho_temp;
									}
								}else{
									//on reprend la liste des sous champs
									$subfield=array();
									$subfield=$field_val["SUBFIELD"];
									//On parcours les champs de la notice pour créer les catégories
									for ($_1=0; $_1<count($tabl_categ_recovered[$name_field.$subfield[0]["CODE"]]); $_1++) {
										//$tabl_libelle=array();
										if($subfield[0]["REPEAT"] == "1"){
											//Le premier sous champ est aussi répétable et on procède à une association 1-1 avec les sous champs suivant si il y en a
											for ($_2=0; $_2<count($tabl_categ_recovered[$name_field.$subfield[0]["CODE"]][$_1]); $_2++) {
												if($wording=trim($tabl_categ_recovered[$name_field.$subfield[0]["CODE"]][$_1][$_2])){
													for($_pos_subfiel=1;$_pos_subfiel<count($subfield);$_pos_subfiel++){//Parcour des autres sous champs
														if($tmp=trim($tabl_categ_recovered[$name_field.$subfield[$_pos_subfiel]["CODE"]][$_1][$_2])){
															if($wording)$wording.=$subfield[$_pos_subfiel]["PREFIX"];
															$wording.=$tmp;
															if($subfield[$_pos_subfiel]["SUFFIX"])$wording.=$subfield[$_pos_subfiel]["SUFFIX"];
														}
													}
													//Construction des catégories terminée
													$tabl_autho_temp=array();
													$tabl_autho_temp["wording"]=$wording;
													if($field_val["AUTHORITY_NUMBER"] && (count($tabl_categ_recovered[$name_field.$subfield[0]["CODE"]][$_1]) == count($tabl_categ_recovered[$name_field.$field_val["AUTHORITY_NUMBER"]][$_1]))){
														$tabl_autho_temp["id_authority"]=$tabl_categ_recovered[$name_field.$field_val["AUTHORITY_NUMBER"]][$_1][$_2];
													}
													if(!is_array($tabl_categ_lib)){
														$tabl_autho_temp["id_pmb"]=category_auto::build_categ($wording,$obj_thes ->id_thesaurus,$root_node_number);
													}else{
														$tmp_build=array();
														$tmp_build["wording"]=$wording;
														$tmp_build["id_thes"]=$obj_thes ->id_thesaurus;
														$tmp_build["id_parent"]=$root_node_number;
														$tabl_categ_lib[]=$tmp_build;
													}
													$tabl_link_authority[]=$tabl_autho_temp;
												}
											}
										}else{
											$wording="";
											if(!$subfield[0]["REPEAT"]){
												//Pas de répétion
												$wording=trim($tabl_categ_recovered[$name_field.$subfield[0]["CODE"]][$_1][0]);
											}elseif($subfield[0]["REPEAT"] == "2"){
												//On répette le premier sous champs dans le libellé
												for ($_2=0; $_2<count($tabl_categ_recovered[$name_field.$subfield[0]["CODE"]][$_1]); $_2++) {
													if($tmp=trim($tabl_categ_recovered[$name_field.$subfield[0]["CODE"]][$_1][$_2])){
														if($wording)$wording.=$subfield[0]["PREFIX"];
														$wording.=$tmp;
														if($subfield[0]["SUFFIX"])$wording.=$subfield[0]["SUFFIX"];
													}
												}
											}
											
											if(!$subfield[0]["SUBFIELD"]){//Si pas fils
												for($_pos_subfiel=1;$_pos_subfiel<count($subfield);$_pos_subfiel++){
													for ($_2=0; $_2<count($tabl_categ_recovered[$name_field.$subfield[$_pos_subfiel]["CODE"]][$_1]); $_2++) {
														if($tmp=trim($tabl_categ_recovered[$name_field.$subfield[$_pos_subfiel]["CODE"]][$_1][$_2])){
															if($wording)$wording.=$subfield[$_pos_subfiel]["PREFIX"];
															$wording.=$tmp;
															if($subfield[$_pos_subfiel]["SUFFIX"])$wording.=$subfield[$_pos_subfiel]["SUFFIX"];
														}
													}
												}
												//Construction des catégories terminée
												$tabl_autho_temp=array();
												$tabl_autho_temp["wording"]=$wording;
												if($field_val["AUTHORITY_NUMBER"] && (count($tabl_categ_recovered[$name_field.$field_val["AUTHORITY_NUMBER"]][$_1]) == 1)){
													$tabl_autho_temp["id_authority"]=$tabl_categ_recovered[$name_field.$field_val["AUTHORITY_NUMBER"]][$_1][0];
												}
												if(!is_array($tabl_categ_lib)){
													$tabl_autho_temp["id_pmb"]=category_auto::build_categ($wording,$obj_thes ->id_thesaurus,$root_node_number);
												}else{
													$tmp_build=array();
													$tmp_build["wording"]=$wording;
													$tmp_build["id_thes"]=$obj_thes ->id_thesaurus;
													$tmp_build["id_parent"]=$root_node_number;
													$tabl_categ_lib[]=$tmp_build;
												}
												$tabl_link_authority[]=$tabl_autho_temp;
											}else{
												//Si fils
												$create_node=true;
												if(!$root_node_number){
													$create_node=false;
												}
												if(!is_array($tabl_categ_lib)){
													global $tabl_id_categ_link;
													$tabl_id_categ_link=array();
													$id_field_parent=category_auto::build_categ($wording,$obj_thes ->id_thesaurus,$root_node_number,false,$create_node);
													category_auto::browse_category($subfield[0]["SUBFIELD"],$tabl_categ_recovered,$name_field,$_1,$obj_thes ->id_thesaurus,$id_field_parent,$tabl_categ_lib,$create_node);
													if($field_val["AUTHORITY_NUMBER"] && (count($tabl_categ_recovered[$name_field.$field_val["AUTHORITY_NUMBER"]][$_1]) == count($tabl_id_categ_link))){
														foreach ( $tabl_categ_recovered[$name_field.$field_val["AUTHORITY_NUMBER"]][$_1] as $key => $value ) {
       														$tabl_autho_temp=array();
       														$tabl_autho_temp["id_authority"]=$value;
       														$tabl_autho_temp["id_pmb"]=$tabl_id_categ_link[$key];
       														$tabl_link_authority[]=$tabl_autho_temp;
														}
													}
												}else{
													$tmp_build=array();
													$tmp_build["wording"]=$wording;
													$tmp_build["id_thes"]=$obj_thes ->id_thesaurus;
													$tmp_build["id_parent"]=$root_node_number;
													$tmp_build["link"]=0;
													$tabl_categ_lib[]=$tmp_build;
													category_auto::browse_category($subfield[0]["SUBFIELD"],$tabl_categ_recovered,$name_field,$_1,$obj_thes ->id_thesaurus,$wording,$tabl_categ_lib,$create_node);
												}
											}		
										}
									}
								}
							}
						}
					}
				}
			}
		}
		/*echo "<pre>";
    	print_r($tabl_link_authority);
    	echo "</pre>";*/
		return $tabl_link_authority;
    }
    
    public static function browse_subfields($tab_ss_champ,$name_field,&$tabl_categ_has_recovered){
		foreach ( $tab_ss_champ as $key => $subfield_root ) {
			$tmp=array();
			$tmp["field"]=$name_field;
			$tmp["subfield"]=$subfield_root["CODE"];
			$tabl_categ_has_recovered[]=$tmp;
	       
			if(!empty($subfield_root["SUBFIELD"])) {
				category_auto::browse_subfields($subfield_root["SUBFIELD"],$name_field,$tabl_categ_has_recovered);
			}
		}
	}
	
	public static function browse_category($subfield,$tabl_categ_recovered,$name_field,$counter_field,$id_thes,$root_node_number,&$tabl_categ_lib,$create_node){
		global $incr_categ,$notice_id,$tabl_id_categ_link;
		$creation=0;
		$id_noeud=$root_node_number;
		foreach ( $subfield as $key => $subfield_root ) {
			//Je parcours les sous-champs	
			for ($_2=0; $_2<count($tabl_categ_recovered[$name_field.$subfield_root["CODE"]][$counter_field]); $_2++) {
				if($tmp=trim($tabl_categ_recovered[$name_field.$subfield_root["CODE"]][$counter_field][$_2])){
					if($creation){
						//Si j'ai dans un même champ plusieurs fois le même sous champ je lie la notice à tous sauf le dernier si il a des enfants
						if(!is_array($tabl_categ_lib)){
							if($notice_id && $id_noeud){
								$incr_categ++;
								$rqt_add = "insert IGNORE into notices_categories set notcateg_notice='".$notice_id."', num_noeud='".$id_noeud."', ordre_categorie='".$incr_categ."' " ;
								pmb_mysql_query($rqt_add);
								if(!in_array($id_noeud,$tabl_id_categ_link)){
									$tabl_id_categ_link[]=$id_noeud;
								}
							}
						}else{
							$tmp_build=array();
							$tmp_build["wording"]=$id_noeud;
							$tmp_build["id_thes"]=$id_thes;
							$tmp_build["create_node"]=$create_node;
							$tmp_build["link"]=1;
							$tabl_categ_lib[]=$tmp_build;
						}
					}
					if(!is_array($tabl_categ_lib)){
						$id_noeud=category_auto::build_categ($tmp,$id_thes,$root_node_number,false,$create_node);
					}else{
						$id_noeud=$tmp;
						$tmp_build=array();
						$tmp_build["wording"]=$tmp;
						$tmp_build["id_thes"]=$id_thes;
						$tmp_build["create_node"]=$create_node;
						$tmp_build["word_parent"]=$root_node_number;
						$tmp_build["link"]=0;
						$tabl_categ_lib[]=$tmp_build;
					}
					$creation++;
				}
			}
	       
			if($subfield_root["SUBFIELD"]){
				 $nb_creation=category_auto::browse_category($subfield_root["SUBFIELD"],$tabl_categ_recovered,$name_field,$counter_field,$id_thes,$id_noeud,$tabl_categ_lib,$create_node);
				 if(!$nb_creation){
				 	//Si je n'ai trouvé aucun fils je fait le lien avec le père
				 	if(!is_array($tabl_categ_lib)){
				 		if($notice_id && $id_noeud){
							$incr_categ++;
							$rqt_add = "insert IGNORE into notices_categories set notcateg_notice='".$notice_id."', num_noeud='".$id_noeud."', ordre_categorie='".$incr_categ."' " ;
							pmb_mysql_query($rqt_add);
							if(!in_array($id_noeud,$tabl_id_categ_link)){
								$tabl_id_categ_link[]=$id_noeud;
							}
						}
				 	}else{
				 		$tmp_build=array();
						$tmp_build["wording"]=$id_noeud;
						$tmp_build["id_thes"]=$id_thes;
						$tmp_build["create_node"]=$create_node;
						$tmp_build["link"]=1;
						$tabl_categ_lib[]=$tmp_build;
				 	}
				 }else{
				 	$creation+=$nb_creation;
				 }
			}elseif($id_noeud){
				//Si je n'ai pas de sous champs à reprendre je lie la notice
				if(!is_array($tabl_categ_lib)){
					if($notice_id && $id_noeud){
						$incr_categ++;
						$rqt_add = "insert IGNORE into notices_categories set notcateg_notice='".$notice_id."', num_noeud='".$id_noeud."', ordre_categorie='".$incr_categ."' " ;
						pmb_mysql_query($rqt_add);
						if(!in_array($id_noeud,$tabl_id_categ_link)){
							$tabl_id_categ_link[]=$id_noeud;
						}
					}
				}else{
					$tmp_build=array();
					$tmp_build["wording"]=$id_noeud;
					$tmp_build["id_thes"]=$id_thes;
					$tmp_build["create_node"]=$create_node;
					$tmp_build["link"]=1;
					$tabl_categ_lib[]=$tmp_build;
				}
			}
	       
		}
		return $creation;
	}
	
	public static function build_categ($tab_categ,$id_thes,$id_parent,$do_lien=true,$do_create=true){
		global $incr_categ,$notice_id,$lang;
		if(trim($tab_categ)){
			$resultat = categories::searchLibelle(addslashes($tab_categ), $id_thes, $lang,$id_parent);				
			if (!$resultat && $id_parent && $do_create){
				// création de la catégorie
				$n=new noeuds();
				$n->num_parent=$id_parent;
				$n->num_thesaurus=$id_thes;
				$n->save();
				$resultat=$id_n=$n->id_noeud;
				$c=new categories($id_n, $lang);
				$c->libelle_categorie=$tab_categ;
				$c->save();
			}
			// ajout de l'indexation à la notice dans la table notices_categories
			if($do_lien && $resultat && $notice_id){
				$incr_categ++;
				$rqt_ajout = "insert IGNORE into notices_categories set notcateg_notice='".$notice_id."', num_noeud='".$resultat."', ordre_categorie='".$incr_categ."' " ;
				pmb_mysql_query($rqt_ajout);
			}
			return $resultat;
		}
		return 0;
	}
}
?>