<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesConvertImport.class.php,v 1.21.4.2 2025/06/04 07:18:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path, $include_path, $pmb_indexation_lang;
require_once($class_path."/external_services.class.php");
require_once($include_path."/parser.inc.php");
require_once($base_path."/admin/convert/convert.class.php");
require_once($class_path."/z3950_notice.class.php");

require_once ("$class_path/marc_table.class.php");
require_once ("$class_path/lender.class.php");
require_once ("$class_path/docs_statut.class.php");
require_once($base_path."/admin/import/import_func.inc.php");
require_once ("$include_path/isbn.inc.php");
require_once ("$include_path/marc_tables/$pmb_indexation_lang/empty_words");
require_once ("$class_path/iso2709.class.php");
require_once ("$class_path/author.class.php");
require_once ("$class_path/serie.class.php");
require_once ("$class_path/editor.class.php");
require_once ("$class_path/collection.class.php");
require_once ("$class_path/subcollection.class.php");
require_once ("$class_path/expl.class.php");
require_once ("$class_path/lender.class.php");
require_once ("$class_path/docs_type.class.php");
require_once ("$class_path/docs_section.class.php");
require_once ("$class_path/docs_location.class.php");
require_once ("$class_path/docs_codestat.class.php");
require_once ("$class_path/indexint.class.php");
require_once ("$class_path/origine_notice.class.php");
require_once ("$class_path/notice.class.php");
require_once ("$class_path/titre_uniforme.class.php");
require_once("$include_path/parser.inc.php");

class pmbesConvertImport extends external_services_api_class {
	public $catalog;
	public $converted_notice;
	public $source_id;

	public function get_catalog() {

	    if (!isset($this->catalog) || !is_array($this->catalog) || !count($this->catalog)) {
			//Lecture des différents formats de conversion possibles
			global $base_path;
			if (file_exists("$base_path/admin/convert/imports/catalog_subst.xml")) {
				$fic_catal = "$base_path/admin/convert/imports/catalog_subst.xml";
			} else {
				$fic_catal = "$base_path/admin/convert/imports/catalog.xml";
			}
			$this->catalog=_parser_text_no_function_(file_get_contents($fic_catal),"CATALOG",$fic_catal);
		}
		return $this->catalog;
	}


	/*
	 * returne la liste des conversions possibles
	 */
	public function get_convert_types() {
		$convert_types=array();
		$this->get_catalog();
		//Création et filtrage de la liste des types d'import
		for ($i=0; $i<count($this->catalog['ITEM']); $i++) {
			if ($this->catalog['ITEM'][$i]['VISIBLE']!='no') {
			   $convert_types[$i]=encoding_normalize::utf8_normalize($this->catalog['ITEM'][$i]['NAME']);
			}
		}
		return $convert_types;
	}


	/*
	 * retourne la liste des paths
	 */
	public function get_catalog_paths() {
		$catalog_paths = array();
		$this->get_catalog();
		//Création et filtrage de la liste des types d'import
		for ($i=0; $i<count($this->catalog['ITEM']); $i++) {
		    if (empty($this->catalog['ITEM'][$i]['VISIBLE']) || $this->catalog['ITEM'][$i]['VISIBLE']!='no') {
			   $catalog_paths[$this->catalog['ITEM'][$i]['PATH']] = $i;
			}
		}
		return $catalog_paths;
	}

	/*
	 * @param notice = 1 notice sans entête en utf-8
	 * @param convert_path = chemin de la conversion à réaliser
	 * @param import = true >> exécuter l'import après conversion
	 * @param source_id = Identifiant d'une source
	 * @param do_not_convert = true >> Ne pas convertir la notice (Peut-être utile si notice en format Unimarc)
	 */
	public function convert_by_path($notice, $convert_path, $import=0, $source_id=0, $do_not_convert=false) {
		$convert_type_id = 0;
		$catalog_paths = $this->get_catalog_paths();

		if (isset($catalog_paths[$convert_path])) {
			$convert_type_id = $catalog_paths[$convert_path];
		}
		return $this->convert($notice, $convert_type_id, $import, $source_id, $do_not_convert);
	}

	/*
	 * @param notice = 1 notice sans entête en utf-8
	 * @param convert_type_id = identifiant de la conversion à réaliser
	 * @param import = true >> exécuter l'import après conversion
	 * @param source_id = Identifiant d'une source
	 * @param do_not_convert = true >> Ne pas convertir la notice (Peut-être utile si notice en format Unimarc)
	 */
	public function convert($notice, $convert_type_id, $import=0, $source_id=0, $do_not_convert=false) {
		global $charset;

		$retour = array();
		$this->get_catalog();
		$this->source_id=$source_id;
		$convert_type=$this->catalog['ITEM'][$convert_type_id];
		$importable=$this->catalog['ITEM'][$convert_type_id]['IMPORT'];

		if (count($convert_type)) {
			$notice_convert=$notice;
			if (($charset != "utf-8") && ($do_not_convert == false)) {
				if(function_exists("mb_convert_encoding")){
					$notice_convert = mb_convert_encoding($notice,"Windows-1252","UTF-8");
				}else{
					$notice_convert = encoding_normalize::utf8_decode($notice);
				}
			}
			$export= new convert($notice_convert,$convert_type_id);
			$this->converted_notice=$export->output_notice;

			if($import && ($importable=='yes') && $this->converted_notice) {
				$retour = $this->import();
			}
		}

		return array('notice'=>$notice, 'converted_notice'=>($charset!= "utf-8"?encoding_normalize::utf8_normalize($this->converted_notice):$this->converted_notice), 'import' => $retour);
	}


	public function import($unimarc_notice='',$source_id='') {

		global $deflt_integration_notice_statut;
		global $gestion_acces_active, $gestion_acces_user_notice, $gestion_acces_empr_notice;

		$retour = array();
		if ($unimarc_notice) {
			$this->converted_notice=$unimarc_notice;
		}
		if ($source_id) {
			$this->source_id=$source_id;
		}
		if ($this->converted_notice) {
			$z = new z3950_notice('unimarc',$this->converted_notice);
			$z->source_id = $this->source_id;
			$z->statut = $deflt_integration_notice_statut;
			$z->var_to_post();
			$retour = $z->insert_in_database();
			if($retour[0]){
				//parce que les droits sur une nouvelle ressource se calculent forcément sur le formulare que n'existe pas dans ce cas...
				if ($gestion_acces_active==1) {
					$ac= new acces();
					//traitement des droits acces user_notice
					if ($gestion_acces_user_notice==1) {
						$dom_1= $ac->setDomain(1);
						$dom_1->applyRessourceRights($retour[1]);
					}
					//traitement des droits acces empr_notice
					if ($gestion_acces_empr_notice==1) {
						$dom_2= $ac->setDomain(2);
						$dom_2->applyRessourceRights($retour[1]);
					}
				}
			}
		}
		return $retour;
	}

	public function import_basic($notices,$params=array(),$with_expl=false){
		global $base_path,$class_path,$include_path,$msg,$charset;
		global $deflt_integration_notice_statut,$deflt_lenders,$deflt_docs_statut,$deflt_docs_location;
		global $deflt_notice_is_new;

		if (!defined('SESSid')) {
		    define('SESSid', '');
		}
		$log=array();
		//On contrôle tous les paramètres obligatoires
		if(!$params["func_import"]){
			$params["func_import"]="func_bdp.inc.php";//Function d'import à utiliser
		}
		if(file_exists($base_path."/admin/import/".$params["func_import"])){
			require_once($base_path."/admin/import/".$params["func_import"]);
		}else{
			require_once($base_path."/admin/import/func_bdp.inc.php");
		}

		//Notices
		if(!isset($params["isbn_mandatory"])) $params["isbn_mandatory"]="0";//ISBN obligatoire ?
		if(!isset($params["isbn_dedoublonnage"])) $params["isbn_dedoublonnage"]="1";//Dédoublonnage sur ISBN ?
		if(!isset($params["isbn_only"])) $params["isbn_only"]="0";//Que les ISBN
		if(!isset($params["statutnot"])) $params["statutnot"]=$deflt_integration_notice_statut;//Statut des notices importées  -> On met la valeur du paramètre utilisateur "Statut de notice par défaut en intégration de notice"
		if(!isset($params["link_generate"])) $params["link_generate"]="0";//Générer les liens entre notices ?
		if(!isset($params["authorities_notices"])) $params["authorities_notices"]="0";//Tenir compte des notices d'autorités
		if(!isset($params["authorities_default_origin"])) $params["authorities_default_origin"]="";//Origine par défaut des autorités si non précisé dans les notices
		if(!isset($params["notice_is_new"])) $params["notice_is_new"]=$deflt_notice_is_new;//Est-ce une nouveauté ?

		//Exemplaires
		if($with_expl){
			if(!isset($params["book_lender_id"])) $params["book_lender_id"]=$deflt_lenders;//Propriétaire  -> On met la valeur du paramètre utilisateur "Propriétaire par défaut en création d'exemplaire"
			if(!isset($params["book_statut_id"])) $params["book_statut_id"]=$deflt_docs_statut;//Statut  -> On met la valeur du paramètre utilisateur "Statut de document par défaut en création d'exemplaire"
			if(!isset($params["book_location_id"])) $params["book_location_id"]=$deflt_docs_location;//Localisation  -> On met la valeur du paramètre utilisateur "Localisation du document par défaut en création d'exemplaire"
			if(!isset($params["cote_mandatory"])) $params["cote_mandatory"]="0";//Cote obligatoire ?
			if(!isset($params["tdoc_codage"])) $params["tdoc_codage"]="0";//Types de document Codage du propriétaire ?
			if(!isset($params["statisdoc_codage"])) $params["statisdoc_codage"]="0";//Codes statistiques Codage du propriétaire ?
			if(!isset($params["sdoc_codage"])) $params["sdoc_codage"]="0";//Sections Codage du propriétaire ?
		}
		//Find de contrôle des paramètres obligatoires
		//On rend global tous les paramètres passés (et pas forcément que les obligatoires) pour la suite
		foreach ( $params as $key => $value ) {
       		global ${$key};
       		${$key}=$value;
		}

		if(count($notices)){
			ob_start();//On temporise toutes les sorties (dans le cas ou dans la fonction d'import on fait des sorties écrans directement)
			$nbtot_notice=count($notices);
			$notice_deja_presente=0;
			$notice_rejetee=0;
			global $notices_crees, $notices_a_creer,$bulletins_crees,$bulletins_a_creer;
			$notices_crees=$notices_a_creer=$bulletins_crees=$bulletins_a_creer=array();
			if($with_expl){
				global $section_995, $typdoc_995, $codstatdoc_995, $nb_expl_ignores;
				$section_995_=new marc_list("section_995");
				$section_995=$section_995_->table;
				$typdoc_995_=new marc_list("typdoc_995");
				$typdoc_995=$typdoc_995_->table;
				$codstatdoc_995_=new marc_list("codstatdoc_995");
				$codstatdoc_995=$codstatdoc_995_->table;
				$nb_expl_ignores=0;
			}
			foreach ( $notices as $notice ) {
				$notice=encoding_normalize::utf8_normalize($notice);
       			$res_lecture = recup_noticeunimarc($notice) ;
       			if($params["link_generate"]) recup_noticeunimarc_link($notice);
       			global $tit_200a;
       			if (!$res_lecture || !$tit_200a[0]) {
	                $res_lecture = 0;
	                $fp = fopen ($base_path."/temp/err_import.unimarc","a+");
	                fwrite ($fp, $notice);
	                fclose ($fp);
	                $notice_rejetee++;
				}else{
					recup_noticeunimarc_suite($notice) ;
					global $isbn,$EAN,$issn_011,$collection_225,$collection_410,$code,$code10,$isbn_OK,$notice_id;
					if($isbn[0]=="NULL") $isbn[0]="";
	                // si isbn vide, on va tenter de prendre l'EAN stocké en 345$b
	                if ($isbn[0]=="") $isbn[0]=$EAN[0] ;
	                // si isbn vide, on va tenter de prendre le serial en 011
	                if ($isbn[0]=="") $isbn[0]=$issn_011[0];
	                // si ISBN obligatoire et isbn toujours vide :
	                if ($params["isbn_mandatory"] == 1 && $isbn[0]=="") {
	                    // on va tenter de prendre l'ISSN stocké en 225$x
	                    $isbn[0]=$collection_225[0]['x'] ;
	                    // si isbn toujours vide, on va tenter de prendre l'ISSN stocké en 410$x
	                    if ($isbn[0]=="") $isbn[0]=$collection_410[0]['x'] ;
	                }

					// on commence par voir ce que le code est (basé sur la recherche par code du module catalogage
					$ex_query = clean_string($isbn[0]);

					$EAN = '';
					$isbn = '';
					$code = '';
					$code10 = '' ;

					if(isEAN($ex_query)) {
						// la saisie est un EAN -> on tente de le formater en ISBN
						$EAN=$ex_query;
						$isbn = EANtoISBN($ex_query);
						// si échec, on prend l'EAN comme il vient
						if(!$isbn)
							$code = str_replace("*","%",$ex_query);
						else {
							$code=$isbn;
							$code10=formatISBN($code,10);
						}
					} else {
						if(isISBN($ex_query)) {
							// si la saisie est un ISBN
							$isbn = formatISBN($ex_query);
							// si échec, ISBN erroné on le prend sous cette forme
							if(!$isbn)
								$code = str_replace("*","%",$ex_query);
							else {
								$code10=$isbn ;
								$code=formatISBN($code10,13);
							}
						} else {
							// ce n'est rien de tout ça, on prend la saisie telle quelle
							$code = str_replace("*","%",$ex_query);
						}
					}
					$isbn_OK=$code;
	                $new_notice = 0;
	                $notice_id = 0 ;
					// le paramétrage est-il : dédoublonnage sur code ? / Ne dédoublonner que sur code ISBN (ignorer les ISSN) ?
	                if ((($params["isbn_dedoublonnage"])&&(!$params["isbn_only"]))||(($params["isbn_dedoublonnage"])&&($params["isbn_only"])&&(isISBN($isbn)))) {

						$trouvees=0;
						if ($EAN && $isbn) {
							// cas des EAN purs : constitution de la requête
							$requete = "SELECT distinct notice_id FROM notices ";
							$requete.= " WHERE notices.code in ('$code','$EAN'".($code10?",'$code10'":"").") limit 1";
							$myQuery = pmb_mysql_query($requete);
							$trouvees=pmb_mysql_num_rows($myQuery);
						} elseif ($isbn) {
							// recherche d'un isbn
							$requete = "SELECT distinct notice_id FROM notices ";
							$requete.= " WHERE notices.code in ('$code'".($code10?",'$code10'":"").") limit 1";
							$myQuery = pmb_mysql_query($requete);
							$trouvees=pmb_mysql_num_rows($myQuery);
						} elseif ($code) {
							// note : le code est recherché dans le champ code des notices
							// (cas des code-barres disques qui échappent à l'EAN)
							//
							$requete = "SELECT notice_id FROM notices ";
							$requete.= " WHERE notices.code like '$code' limit 10";
							$myQuery = pmb_mysql_query($requete);
							$trouvees=pmb_mysql_num_rows($myQuery);
						}

	                    // dédoublonnage sur isbn
	                    if ($EAN  || $isbn || $code) {
	                        if ($trouvees==0) {
	                            $new_notice=1;
	                        } else {
	                            $new_notice=0;
	                            $notice_id = pmb_mysql_result($myQuery,0,"notice_id");
	                            pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_expl_".addslashes(SESSid).".inc', '".$msg[542]." $EAN  || $isbn || $code ".addslashes($tit_200a[0])."') ") ;
	                        }
	                    } else {
	                        if ($params["isbn_mandatory"] == 1) {
	                            pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_".addslashes(SESSid).".inc', '".$msg[543]."') ") ;
	                        } else {
	                            $new_notice = 1;
	                            pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_".addslashes(SESSid).".inc', '".$msg[565]."') ") ;
	                        }
	                    }
	                } else {
	                    // pas de dédoublonnage
	                    if ($params["isbn_mandatory"] == 1 && $isbn_OK=="") {
	                       pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_".addslashes(SESSid).".inc', '".$msg[543]."') ") ;
	                    }elseif($isbn_OK){
	                        $new_notice = 1;
	                    }else{
	                    	 $new_notice = 1;
	                         pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_".addslashes(SESSid).".inc', '".$msg[565]."') ") ;
	                    }
	                }

					 /* the notice is new, we are going to import it... */
	                if ($new_notice==1) {
	                    import_new_notice() ;
	                    if($params["link_generate"]) import_notice_link();
	    				import_new_notice_suite() ;
	    				// Mise à jour de la table "notices_global_index"
	    				notice::majNoticesGlobalIndex($notice_id);
	    				// Mise à jour de la table "notices_mots_global_index"
	    				notice::majNoticesMotsGlobalIndex($notice_id);
	                } else {
	                	$notice_deja_presente++;

						//TRAITEMENT DES DOCS NUMERIQUES SUR NOTICE EXISTANTE
						global $add_explnum;//Fonction d'import func_ensai_ensae.inc.php
						if (($add_explnum===TRUE) && function_exists("ajoute_explnum")) ajoute_explnum();
					}

	                // TRAITEMENT DES EXEMPLAIRES ICI
	                if ($with_expl) {
	                    traite_exemplaires () ;
	                }
				}
			}//Fin du traitement des notices

			//Gestion des logs
			$formulaire="";
            $script="";
            $log["notice_deja_presente"]=$notice_deja_presente;
            $log["notice_rejetee"]=$notice_rejetee;
            $log["nbtot_notice"]=$nbtot_notice;
            $log["stdout"] = ob_get_contents();
            if($charset != "utf-8") $log["stdout"]= encoding_normalize::utf8_normalize($log["stdout"]);
  			ob_end_clean();
			$gen_liste_log="";

            $resultat_liste=pmb_mysql_query("SELECT error_origin, error_text, count(*) as nb_error FROM error_log where error_origin in ('expl_".addslashes(SESSid).".class','import_expl_".addslashes(SESSid).".inc','iimport_expl_".addslashes(SESSid).".inc','import_".addslashes(SESSid).".inc.php', 'import_".addslashes(SESSid).".inc','import_func_".addslashes(SESSid).".inc.php') group by error_origin, error_text");
            $nb_liste=pmb_mysql_num_rows($resultat_liste);
            if ($nb_liste>0) {
	            $i_log=0;
	            while ($i_log<$nb_liste) {
	            	$tmp=array();
	            	$tmp["error_origin"]=pmb_mysql_result($resultat_liste,$i_log,"error_origin");
	            	if($charset != "utf-8") $tmp["error_origin"]= encoding_normalize::utf8_normalize($tmp["error_origin"]);
	            	$tmp["error_text"]=pmb_mysql_result($resultat_liste,$i_log,"error_text");
	            	if($charset != "utf-8") $tmp["error_text"]= encoding_normalize::utf8_normalize($tmp["error_text"]);
	            	$tmp["nb_error"]=pmb_mysql_result($resultat_liste,$i_log,"nb_error");
	            	$log["error_log"][]=$tmp;
	                $i_log++;
				}
				pmb_mysql_query("DELETE FROM error_log WHERE error_origin  in ('expl_".addslashes(SESSid).".class','import_expl_".addslashes(SESSid).".inc','iimport_expl_".addslashes(SESSid).".inc','import_".addslashes(SESSid).".inc.php', 'import_".addslashes(SESSid).".inc','import_func_".addslashes(SESSid).".inc.php')");
            }else{
            	$log["result"]=$this->msg["import_basic_msg_ok"];
            	if($charset != "utf-8") $log["result"]= encoding_normalize::utf8_normalize($log["result"]);
            }
		}else{
			$log["result"]=$this->msg["import_basic_msg_ko"];
			if($charset != "utf-8") $log["result"]= encoding_normalize::utf8_normalize($log["result"]);
		}
		return $log;
	}
}
