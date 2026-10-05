<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RootNode.php,v 1.18 2023/07/26 12:12:58 dbellamy Exp $
namespace Sabre\PMB;

class RootNode extends Collection {
	public $config;

	public function __construct($config){
		parent::__construct($config);
		$this->type = "rootNode";
	}

	public function getName() {
		return "";
	}

	/* (non-PHPdoc)
	 * @see Sabre\PMB.Collection::get_notice_by_meta()
	 *
	 * Intègre les informations d'une notice via les métadonnées d'un fichier déposé dans le webdav
	 *
	 * @param $name : Le nom du fichier
	 * @param $filename : chemin complet du fichier
	 * @return integer notice_id l'identifiant de la notice
	 */
	public function get_notice_by_meta($name,$filename){

		//construction de la notice standard en fonction des métadonnées
		$entry=array();
		$entry=self::buildEntry(self::getMetadata($filename, $name),$name);
		$entry['statut']=$this->config['default_statut'];

		switch($entry['niveau_biblio'].$entry['niveau_hierar']){
			//spécif de chaque type
			case 'b2':
				//bulletin
				self::buildBulletin($entry);
				break;
			case 'a2':
				//article
				self::buildAnalysis($entry);
				break;
				//erreur, périodique ou notice
			case 's1':
			case 'm0':
			default:
				self::buildNotice($entry);
				break;
		}

		return $entry['notice_id'];
	}

	/**
	 * @param mixed $metas Le tableau des métadonnées
	 * @return mixed $entry un tableau qui correspond au format d'une notice PMB, plus les tables [annexes], les informations de [bulletin] et [periodique] et les [cp] (champs perso)
	 *
	 * Cette fonction permet de transformer les métadonnées du PDF en un format $entry pour intégration via la fonction buildNotice()
	 *
	 * EXEMPLE :
	 *Array(
	 *    [niveau_biblio] => a
	 *    [niveau_hierar] => 2
	 *    [typdoc] => a
	 *    [tit1] => Article test 1
	 *    [n_contenu] => Le contenu de mon article
	 *    [n_gen] => La note générale de mon article
	 *    [n_resume] => le résumé de mon article
	 *    [create_date] => 2014-01-16 12:53:30+01:00
	 *    [index_l] => Mon premier mot clé/Mon second mot clé/Mon troisieme mot clé
	 *    [annexes] => Array(
	 *            [responsability] => Array(
	 *                    [0] => Array(
	 *                            [authors] => Array(
	 *                                    [name] => GOYA
	 *                                    [rejete] => Chantal
	 *                                    [type] => 70
	 *                                )
	 *                            [responsability_type] => 0
	 *                        )
	 *                    [1] => Array(
	 *                            [authors] => Array(
	 *                                    [name] => Centre-inffo
	 *                                    [type] => 71)
	 *                            [responsability_type] => 1
	 *                        )
	 *                    [2] => Array(
	 *                            [authors] => Array(
	 *                                    [name] => HUGO
	 *                                    [rejete] => Victor
	 *                                    [type] => 70
	 *                                )
	 *                            [responsability_type] => 2)
	 *                    [3] => Array(
	 *                            [authors] => Array(
	 *                                    [name] => DUMAS
	 *                                    [rejete] => Alexandre
	 *                                    [type] => 70
	 *                                )
	 *                            [responsability_type] => 2
	 *                        )
	 *                )
	 *            [notices_langues] => Array(
	 *                    [0] => Array(
	 *                            [code_langue] => fre
	 *                            [type_langue] => 0
	 *                        )
	 *                )
	 *            [notices_categories] => Array(
	 *                    [0] => Array(
	 *                            [categories] => Array(
	 *                                    [libelle_categorie] => Descripteur 1
	 *                                    [langue] => fr_FR
	 *                                    [num_thesaurus] => 1
	 *                                )
	 *                        )
	 *                    [1] => Array(
	 *                            [categories] => Array(
	 *                                    [libelle_categorie] => Descripteur 2
	 *                                    [langue] => fr_FR
	 *                                    [num_thesaurus] => 1
	 *                                )
	 *                        )
	 *                )
	 *        )
	 *    [cp] => Array(
	 *            [0] => Array(
	 *                    [field] => CG
	 *                    [value] => Mon text
	 *                )
	 *            [1] => Array(
	 *                    [field] => cp_test
	 *                    [value] => la première valeur de ma liste
	 *                )
	 *        )
	 *
	 *    [bulletin] => Array(
	 *            [date_date] => 2012-05-09
	 *            [mention_date] => 09/05/2012
	 *            [bulletin_numero] => Vol 1, n°3
	 *        )
	 *    [periodique] => Array(
	 *            [tit1] => Mon périodique de test
	 *            [niveau_biblio] => s
	 *            [niveau_hierar] => 1
	 *        )
	 *)
	 *
	 */
	public static function buildEntry($metas,$name){
		global $pmb_keyword_sep;

		$entry=array();
		//Construction de la notice
		//on détermine le type
		if($metas['Type']){
			foreach(explode(',', strtolower(trim($metas['Type']))) as $ligne){
				$ligne=explode('=',trim($ligne));
				if(trim($ligne[0])=='bl'){
					$entry['niveau_biblio']=trim($ligne[1]);
				}elseif(trim($ligne[0])=='hl'){
					$entry['niveau_hierar']=trim($ligne[1]);
				}elseif(trim($ligne[0])=='dt'){
					$entry['typdoc']=trim($ligne[1]);
				}
			}
		}
		//le titre
		if($metas['Title']){
			$entry['tit1']=trim($metas['Title']);
		}else{
			//si pas de titre, on prend le nom du fichier
			$entry['tit1']=trim($name);
		}

		switch($entry['niveau_biblio'].$entry['niveau_hierar']){
			//ici, vérifications des bl et hl
			case 's1':
			case 'b2':
			case 'a2':
			case 'm0':
				break;
			default:
				$entry['niveau_biblio']='m';
				$entry['niveau_hierar']='0';
				break;
		}

		//sinon, on construit et on ajoute
		//Vérifications du type de notice
		if(!$entry['typdoc']){
			$entry['typdoc']='a';
		}else{
			global $lang;
			global $include_path;

			$parser = new \XMLlist("$include_path/marc_tables/$lang/doctype.xml", 0);
			$parser->analyser();

			if(in_array($entry['typdoc'],$parser->table)){
				$tmp=array();
				$tmp=array_flip($parser->table);
				$entry['typdoc']=$tmp[$entry['typdoc']];
			}elseif(!in_array($entry['typdoc'],array_keys($parser->table))){
				//le type de doc n'existe pas dans la liste, on passe au dt standard
				$entry['typdoc']='a';
			}
		}
		//traitements génériques
		//notes
		if($metas['Caption']){
			$entry['n_contenu']=$metas['Caption'];
		}
		if($metas['Notes']){
			$entry['n_gen']=$metas['Notes'];
		}
		//pagination
		if($metas['Rights']){
			$entry['npages']=$metas['Rights'];
		}
		//Complément du titre
		if($metas['Coverage']){
			$entry['tit4']=$metas['Coverage'];
		}
		if($metas['Description']){
			$entry['n_resume']=$metas['Description'];
		}
		//date de création de la notice
		if($metas['CreateDate']){
			$entry['create_date']=self::checkDate($metas['CreateDate']);
		}
		//mots clés
		if($metas['Keywords']){
			$entry['index_l']=preg_replace('/\,\s/', $pmb_keyword_sep, $metas['Keywords']);
		}
		//tables annexes
		//auteurs
		if($metas['Author']){
			self::buildAuthors($entry,$metas['Author']);
		}
		if($metas['Contributor']){
			self::buildAuthors($entry,$metas['Contributor'],true);
		}
		//langues de publication
		if($metas['Language']){
			foreach(preg_split('/\,\s/', $metas['Language']) as $id=>$langue){
				$entry['annexes']['notices_langues'][$id]=array('code_langue'=>$langue,'type_langue'=>0);
			}
		}
		//Catégories
		if($metas['Subject']){
			self::buildCategories($entry,$metas['Subject']);
		}

		//Champs personnalisés
		if ($metas['Format']) {
			foreach (preg_split('/\s?\-{2}\s?/', $metas['Format']) as $id => $ligne) {
				$ligne = preg_split('/\=/', $ligne, 2);
				if (count($ligne) == 2) {
					$entry['cp'][$id] = array('field' => $ligne[0], 'value' => $ligne[1]);
				}
			}
		}

		switch($entry['niveau_biblio'].$entry['niveau_hierar']){
			case 's1':
				//Année d'édition
				if($metas['Date']){
					$entry['date_parution']=self::checkDate($metas['Date']);
					$entry['year']=substr($entry['date_parution'], 0,4);
				}
				//editeurs
				if($metas['Publisher']){
					self::buildPublisher($entry,$metas['Publisher']);
				}
				break;
			//spécif de chaque type
			case 'b2':
			case 'a2':
				//article ou bulletin
				if(($metas['Date'] || $metas['Identifier']) && $metas['Relation']){
					//j'ai une date et/ou un numéro de bulletin, et un titre de pério pour une notice en a2
					if ($metas['Date']) {
						$entry['bulletin']['date_date'] = self::checkDate($metas['Date']);
						$entry['date_parution'] = $entry['bulletin']['date_date'];
						$entry['year'] = substr($entry['date_parution'], 0, 4);
						$tmp = array();
						$tmp = preg_split('/\-/', $entry['bulletin']['date_date']);
						if (count($tmp) == 3) {
							$entry['bulletin']['mention_date'] = $tmp[2].'/'.$tmp[1].'/'.$tmp[0];
						} else {
							$entry['bulletin']['mention_date'] = '0000-00-00';
						}
					}
					if($metas['Identifier']){
						$entry['bulletin']['bulletin_numero']=$metas['Identifier'];
					}

					$entry['periodique']['tit1']=$metas['Relation'];
					$entry['periodique']['niveau_biblio']='s';
					$entry['periodique']['niveau_hierar']='1';

					break;
				}else{
					$entry['niveau_biblio']='m';
					$entry['niveau_hierar']='0';
				}
				//erreur, périodique ou notice
			case 'm0':
			default:
				//Année d'édition
				if($metas['Date']){
					$entry['date_parution']=self::checkDate($metas['Date']);
					$entry['year']=substr($entry['date_parution'], 0,4);
				}
				//editeurs
				if($metas['Publisher']){
					self::buildPublisher($entry,$metas['Publisher']);
				}
				//Collection
				if (!empty($metas['Relation']) && !empty($entry['ed1_id'])) {
					$entry['collections'] = array('name' => trim($metas['Relation']));
				}
				//numéro dans la collection
				if($metas['Identifier']){
					$entry['nocoll']=$metas['Identifier'];
				}
				break;
		}

		return $entry;
	}

	/**
	 * @param $name : file name
	 * @param $filename : file name with path
	 * @return Array : le tableau des métadonnées du fichier
	 */
	public static function getMetadata($filename,$name){
		\create_tableau_mimetype();
		$mimetype = \trouve_mimetype($filename,extension_fichier($name));

		if($mimetype == "application/epub+zip"){
			//récupération de l'image
			$epub = new \epub_Data(realpath($filename));
			//TODO : Vérifier la récupération des métadonnées d'un epub avec \extract_metas(), sinon rétablir les commentaires ici et le else plus bas
// 			$tmp=array();
// 			$tmp=$epub->metas;
// 			foreach($tmp as $key=>$val){
// 				$metas[strtoupper(substr($key,0,1)).substr($key,1)]=$val;
// 			}
			$img = imagecreatefromstring($epub->getCoverContent());
			$file=tempnam(sys_get_temp_dir(),"vign");
			imagepng($img,$file);
			$entry['thumbnail_url']='data:image/png;base64,'.base64_encode(file_get_contents($file));
			unlink($file);
		}
// 		else{
		return \extract_metas(realpath($filename),$mimetype);
// 		}
	}

	/**
	 * @param array $entry le tableau $entry généré par la fonction buildEntry()
	 *
	 * Dédoublonne et ajoute le périodique en fonction des informations de périodique présent dans un bulletin ou un article
	 * !! Ne sert pas à l'ajout d'un $entry de type périodique !!
	 */
	public static function doPeriodique(&$entry){

		//On test si le perio existe
		$query = 'SELECT notice_id FROM notices WHERE tit1="'.addslashes($entry['periodique']['tit1']).'" AND niveau_biblio="'.addslashes($entry['periodique']['niveau_biblio']).'" AND niveau_hierar="'.addslashes($entry['periodique']['niveau_hierar']).'"';
		$result= pmb_mysql_query($query) or die('Echec d\'execution de la requete '.$query.'  : '.pmb_mysql_error());

		if(pmb_mysql_num_rows($result)){
			//si oui on passe l'id dans la zone du pério
			$entry['periodique']['notice_id']=pmb_mysql_result($result, 0,0);
		}else{
			//sinon on ajoute
			$first=true;
			$query='INSERT INTO notices SET ';
			foreach($entry['periodique'] as $fieldName=>$value){
				if(!is_array($value) && $value!='' && $fieldName!='annexes' && $fieldName!='bulletin' && $fieldName!='periodique'){
					if(!$first){
						$query.=',';
					}
					$query.=$fieldName.'="'.addslashes(trim($value)).'"';
					$first=false;
				}
			}

			pmb_mysql_query($query) or die('Echec d\'execution de la requete '.$query.'  : '.pmb_mysql_error());
			$entry['periodique']['notice_id']=pmb_mysql_insert_id();
			\notice::majNoticesTotal($entry['periodique']['notice_id']);
		}
	}

	/**
	 * @param array $entry le tableau $entry généré par la fonction buildEntry()
	 *
	 * Fonction qui complete la fonction buildNotice() pour ajouter les informations de bulletin
 	 * Créé la notice en fonction de $entry
	 * Dédoublonne et ajoute si besoin le périodique
	 * Dédoublonne et ajoute si besoin le bulletin
	 *
	 * Si l'entry est de type bulletin, relie la notice au périodique dans notices_relation
	 * et insert dans la tables bulletins, le champ num_notice avec l'identifiant de l'entry en cours
	 */
	public static function buildBulletin(&$entry){

		if(!$entry['notice_id']){
		//on ajoute dans un premier temps la notice
			self::buildNotice($entry);
		}
		if(!$entry['periodique']['notice_id']){
			self::doPeriodique($entry);
		}

		//on test si le bulletin existe
		$query = 'SELECT bulletin_id FROM bulletins WHERE 1
		AND bulletin_numero="'.addslashes($entry['bulletin']['bulletin_numero']).'"
		AND mention_date="'.addslashes($entry['bulletin']['mention_date']).'"
		AND date_date="'.addslashes($entry['bulletin']['date_date']).'"
		AND bulletin_notice="'.addslashes($entry['periodique']['notice_id']).'" ';
		$result= pmb_mysql_query($query) or die('Echec d\'execution de la requete '.$query.'  : '.pmb_mysql_error());
		if(pmb_mysql_num_rows($result)){
			$entry['bulletin']['bulletin_id']=pmb_mysql_result($result, 0,0);
			//si oui on passe l'id dans la zone du bulletin
		}else{
			//sinon on ajoute
			$query='INSERT INTO bulletins SET bulletin_notice="'.addslashes($entry['periodique']['notice_id']).'"';
			foreach($entry['bulletin'] as $fieldName=>$value){
				$query.=','.$fieldName.'="'.addslashes(trim($value)).'"';
			}
			pmb_mysql_query($query) or die('Echec d\'execution de la requete '.$query.'  : '.pmb_mysql_error());

			$entry['bulletin']['bulletin_id']=pmb_mysql_insert_id();
		}

		if($entry['niveau_biblio'].$entry['niveau_hierar']=="b2"){
			//Si la notice récupéré est un bulletin, on fait le lien entre la notice et le périodique
			\notice_relations::replace($entry['notice_id'], $entry['periodique']['notice_id'], 'b', 1);

			//et on donne le champ num_notice au bulletin en vue de l'ajout qui suis
			$query='UPDATE bulletins SET num_notice='.$entry['notice_id'].' WHERE bulletin_id='.$entry['bulletin']['bulletin_id'];
			pmb_mysql_query($query) or die('Echec d\'execution de la requete '.$query.'  : '.pmb_mysql_error());

		}
	}

	/**
	 * @param array $entry le tableau $entry généré par la fonction buildEntry()
	 *
	 * Fonction qui complete la fonction buildNotice() pour ajouter les informations d'articles
	 * Créé la notice en fonction de $entry
	 * Dédoublonne et ajoute si besoin le périodique
	 * Dédoublonne et ajoute si besoin le bulletin
	 * Insert le lien dans la table [analysis]
	 */
	public static function buildAnalysis(&$entry){
		if(!$entry['notice_id']){
			//on ajoute dans un premier temps la notice
			self::buildNotice($entry);
		}

		if(!$entry['periodique']['notice_id']){
			self::doPeriodique($entry);
		}

		if(!$entry['bulletin']['bulletin_id']){
			self::buildBulletin($entry);
		}

		//on ajoute le lien entre le bulletin et la notice d'article
		$query='INSERT IGNORE INTO analysis (analysis_bulletin, analysis_notice) VALUES ('.$entry['bulletin']['bulletin_id'].','.$entry['notice_id'].')';
		pmb_mysql_query($query) or die('Echec d\'execution de la requete '.$query.'  : '.pmb_mysql_error());
	}

	/**
	 * @param array $entry le tableau $entry généré par la fonction buildEntry()
	 *
	 * Fonction d'import d'une notice formaté par la fonction buildEntry()
	 * Ajoute les informations d'audit
	 * Ajoute les champs personnalisés
	 * Ajoute les tables annexes [responsability], [notices_categories] et [notices_langues]
	 * Met à jours l'indexation de la notice
	 */
	public static function buildNotice(&$entry){
		global $pmb_type_audit;
		global $webdav_current_user_name,$webdav_current_user_id;

		//la notice existe déjà ? si oui, on renvoi l'id trouvé
		if($entry['niveau_biblio'].$entry['niveau_hierar']=='a2'){
			$query = '
			SELECT n1.* FROM notices AS n1
			JOIN analysis ON n1.notice_id=analysis_notice
			JOIN bulletins ON bulletin_id=analysis_bulletin
			JOIN notices AS n2 ON n2.notice_id=bulletin_notice
			WHERE n1.tit1="'.addslashes($entry['tit1']).'"
			AND n1.niveau_biblio="'.addslashes($entry['niveau_biblio']).'"
			AND n1.niveau_hierar="'.addslashes($entry['niveau_hierar']).'"
			AND bulletin_numero="'.addslashes($entry['bulletin']['bulletin_numero']).'"
			AND mention_date="'.addslashes($entry['bulletin']['mention_date']).'"
			AND date_date="'.addslashes($entry['bulletin']['date_date']).'"
			AND n2.tit1="'.addslashes($entry['periodique']['tit1']).'"
			AND n2.niveau_biblio="'.addslashes($entry['periodique']['niveau_biblio']).'"
			AND n2.niveau_hierar="'.addslashes($entry['periodique']['niveau_hierar']).'"
			';
			$result= pmb_mysql_query($query);
		}else{
			$query = 'SELECT * FROM notices WHERE tit1="'.addslashes($entry['tit1']).'" AND niveau_biblio="'.addslashes($entry['niveau_biblio']).'" AND niveau_hierar="'.addslashes($entry['niveau_hierar']).'"';
			$result= pmb_mysql_query($query);
		}

		if(pmb_mysql_num_rows($result)){
			// La notice existe
			$entry=array_merge(pmb_mysql_fetch_array($result,PMB_MYSQL_ASSOC),$entry);
			//TODO : A vérifier
			$first=true;
			$query='UPDATE notices SET ';
			foreach(array_keys($entry) as $fieldName){
				if(!is_array($entry[$fieldName]) && $entry[$fieldName]!='' && $fieldName!='ancien_num_name'){
					if(!$first){
						$query.=',';
					}
					$query.=$fieldName.'="'.addslashes($entry[$fieldName]).'"';
					$first=false;
				}
			}
			$query.=' WHERE notice_id="'.addslashes($entry['notice_id']).'"';
			pmb_mysql_query($query) or die('echec de la requete : '.$query.'<br/>'.pmb_mysql_error()."\n");
		} else {
			//les éditeurs
			if (!empty($entry['publishers'])) {
				foreach ($entry['publishers'] as $id => $publisher) {
					if ($id < 2) {
						if ($id === 0) {
							$entry['ed1_id'] = \editeur::import($publisher);
						} elseif ($id === 1) {
							$entry['ed2_id'] = \editeur::import($publisher);
						}
					}
				}
			}

			//la collection
			if (!empty($entry['collections']) && !empty($entry['ed1_id'])) {
				$entry['collections']['parent'] = $entry['ed1_id'];
				$entry['coll_id'] = \collection::import($entry['collections']);
			}

			$first=true;
			$query='INSERT INTO notices SET ';
			foreach($entry as $fieldName=>$value){
				if(!is_array($value) && $value!=''){
					if(!$first){
						$query.=',';
					}
					$query.=$fieldName.'="'.addslashes(trim($value)).'"';
					$first=false;
				}
			}
			pmb_mysql_query($query) or die('Echec d\'execution de la requete '.$query.'  : '.pmb_mysql_error());
			$entry['notice_id']=pmb_mysql_insert_id();

			if($pmb_type_audit && ($webdav_current_user_id || $webdav_current_user_name) && $entry['create_date']){
				//ajout des informations d'audit
				$query='INSERT INTO audit (type_obj,object_id,user_id,user_name,type_modif,quand) VALUES (1,'.$entry['notice_id'].','.$webdav_current_user_id.',"'.addslashes($webdav_current_user_name).'",1,"'.$entry['create_date'].'")';
				pmb_mysql_query($query) or die('Echec d\'execution de la requete '.$query.'  : '.pmb_mysql_error());
			}
		}

		//les champs persos
		if (!empty($entry['cp'])) {
			foreach ($entry['cp'] as $cp) {
				\parametres_perso::import($entry['notice_id'], $cp['field'], $cp['value'], 'notices');
			}
		}

		//ajout dans les tables annexes a la notice
		if (!empty($entry['annexes'])) {
			foreach ($entry['annexes'] as $typeAnnexe => $annexes) {
				foreach ($annexes as $id => $annexe) {
					switch ($typeAnnexe) {
						case 'responsability':
							//Import et récupération des identifiants auteurs
							$annexe['responsability_author']=\auteur::import($annexe['authors']);

							$annexe['responsability_notice']=$entry['notice_id'];

							break;
						case 'notices_categories':
							//Import et récupération des identifiants catégories
							$query='SELECT num_noeud FROM categories WHERE libelle_categorie="'.addslashes(trim($annexe['categories']['libelle_categorie'])).'" AND num_thesaurus='.$annexe['categories']['num_thesaurus'].' AND langue="'.$annexe['categories']['langue'].'"';
							$result=pmb_mysql_query($query) or die('Echec d\'execution de la requete '.$query.'  : '.pmb_mysql_error());
							if(pmb_mysql_num_rows($result)){
								//le noeud existe déjà
								$annexe['num_noeud']=pmb_mysql_result($result, 0,0);
							}else{
								//le noeud n'existe pas, on cherche le parent non classé
								$query='SELECT id_noeud FROM noeuds WHERE autorite="NONCLASSES" AND num_thesaurus='.$annexe['categories']['num_thesaurus'];
								$result=pmb_mysql_query($query) or die('Echec d\'execution de la requete '.$query.'  : '.pmb_mysql_error());
								if(pmb_mysql_num_rows($result)){
									//on ajoute le noeud
									$query='INSERT INTO noeuds SET num_parent='.pmb_mysql_result($result,0,0).', visible=1, num_thesaurus='.$annexe['categories']['num_thesaurus'];
									pmb_mysql_query($query) or die('Echec d\'execution de la requete '.$query.'  : '.pmb_mysql_error());
									$entry['annexes']['notices_categories'][$id]['num_noeud']=pmb_mysql_insert_id();
									//on ajoute la catégorie
									$categorie=new \categories($annexe['num_noeud'],$annexe['categories']['langue']);
									$categorie->libelle_categorie=trim($annexe['categories']['libelle_categorie']);
									$categorie->save();
								}
							}

							$annexe['notcateg_notice']=$entry['notice_id'];

							break;
						case 'notices_langues':
							$annexe['num_notice']=$entry['notice_id'];
							break;
						case 'notices_authorities_sources':
							$annexe['num_notice']=$entry['notice_id'];
							break;
						case 'notices_relations':
							$annexe['num_notice']=$entry['notice_id'];
							break;
						case 'notices_titres_uniformes':
							$annexe['ntu_num_notice']=$entry['notice_id'];
							break;
					}

					$first=true;
					$query='INSERT IGNORE INTO '.$typeAnnexe.' SET ';
					foreach($annexe as $fieldName=>$value){
						if(!is_array($value) && $value!=''){
							if(!$first){
								$query.=',';
							}
							$query.=$fieldName.'="'.addslashes(trim($value)).'"';
							$first=false;
						}
					}

					pmb_mysql_query($query) or die('Echec d\'execution de la requete '.$query.'  : '.pmb_mysql_error());
				}
			}
		}

		\notice::majNoticesTotal($entry['notice_id']);
	}

	/**
	 * @param string $date une date formaté 0000:00:00 hh:ii:ss
	 * @return string une date formaté 0000-00-00
	 *
	 * Fonction de nettoyage d'une date
	 */
	public static function checkDate($date) {
		$date = preg_split('/\s/', $date);
		$date[0] = preg_replace('/\:/', '-', $date[0]);
		if (count($date) > 1) {
			return implode(' ', $date);
		} else {
			return $date[0];
		}
	}

	/**
	 * @param array $entry le tableau $entry généré par la fonction buildEntry()
	 * @param string $stringPublishers une chaine de caractère qui contient les éditeurs
	 *
	 * Découpe la chaine de caractère et importe l'éditeur
	 * Ajoute au tableau $entry l'information ed1_id et ed2_id
	 */
	public static function buildPublisher(&$entry,$stringPublishers){
		foreach(preg_split('/\,\s/', $stringPublishers) as $id=>$ligne){
			$entry['publishers'][$id]=array('name'=>trim($ligne));
		}
	}

	/**
	 * @param array $entry le tableau $entry généré par la fonction buildEntry()
	 * @param string $stringCategories une chaine de caractère qui contient les catégories
	 *
	 * Découpe les catégories et formate le tableau de responsabilité
	 * Ajoute au tableau [annexes] les informations de catégories dans [notices_categories]
	 */
	public static function buildCategories(&$entry, $stringCategories) {
		global $thesaurus_defaut;

		foreach (preg_split('/\s?\-{2}\s?/', $stringCategories) as $ligne) {
		    if (is_array($entry['annexes']['notices_categories']) && !empty($entry['annexes']['notices_categories'])) {
				$id = max(array_keys($entry['annexes']['notices_categories'])) + 1;
			} else {
				$id = 0;
			}
			$entry['annexes']['notices_categories'][$id]['categories']['libelle_categorie'] = trim($ligne);
			$entry['annexes']['notices_categories'][$id]['categories']['langue'] = 'fr_FR';
			$entry['annexes']['notices_categories'][$id]['categories']['num_thesaurus'] = $thesaurus_defaut;
		}
	}

	/**
	 * @param array $entry le tableau $entry généré par la fonction buildEntry()
	 * @param string $stringAuthors une chaine de caractère qui contient les auteurs
	 * @param boolean $secondary true=auteurs secondaires
	 *
	 * Découpe la chaine de caractère et formate le tableau de responsabilité
	 * Ajoute au tableau [annexes] les informations de responsabilité dans [responsability]
	 */
	public static function buildAuthors(&$entry, $stringAuthors, $secondary = false) {
		$tmp = array();
		if (!empty($secondary)) {
			$tmp = preg_split('/\,\s/', $stringAuthors);
		} else {
			$tmp = preg_split('/\s?\-{2}\s?/', $stringAuthors);
		}
		foreach ($tmp as $ligne) {
			$author = array();
			if (is_array($entry['annexes']['responsability']) && !empty($entry['annexes']['responsability'])) {
				$id = max(array_keys($entry['annexes']['responsability'])) + 1;
			} else {
				$id = 0;
			}
			$ligne = preg_split('/\s?\|\s?/', $ligne);
			$author['name'] = $ligne[0];
			if (!empty($ligne[1])) {
				$author['rejete'] = $ligne[1];
				$author['type'] = '70';
			} else {
				//$author['type']='71';
				$author['type'] = '70'; //Modif CG 06
			}
			$entry['annexes']['responsability'][$id]['authors'] = $author;
			if (!empty($secondary)) {
				$entry['annexes']['responsability'][$id]['responsability_type'] = '2';
			} else {
				if ($id === 0) {
					$entry['annexes']['responsability'][$id]['responsability_type'] = '0';
				} else {
					$entry['annexes']['responsability'][$id]['responsability_type'] = '1';
				}
			}
		}
	}
}