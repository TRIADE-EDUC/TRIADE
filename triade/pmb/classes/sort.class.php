<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sort.class.php,v 1.89.2.3.2.3 2025/05/15 08:16:15 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path, $class_path;
global $msg, $charset, $lang;
global $show_tris_form, $show_popup_tris_form;
global $show_sel_form, $liste_criteres_tri, $opac_nb_max_criteres_tri;

require_once $include_path.'/parser.inc.php';
require_once $include_path.'/templates/sort.tpl.php';
require_once $class_path.'/parametres_perso.class.php';
require_once "$class_path/parameters_subst.class.php";

/**
 * Classe d'abstraction d'acces aux données des tris stockés
 * l'admin utilise la base et l'opac les sessions
 */
class dataSort {

	public $typeData; //base ou session
	public $sortName;

	//pour le parcours des tris
	public $tabParcours;
	public $posParcours;
	public $nbResult;

	public function __construct($sName,$tData) {
		$this->sortName = $sName;
		$this->typeData = $tData;
	}

	protected function recupTriParDefaultId() {
	    $tab = array();
	    //Plusieurs tris par défaut définis dans les paramètres ? On va cherche le tout premier
	    $default_sort = sort::get_default_sort($this->sortName);
	    if (strstr($default_sort,'|')) {
	        $tmpArray = explode("||",$default_sort);
	        $tmpSort=explode("|",$tmpArray[0]);
	        $tab["nom_tri"] = $tmpSort[1];
	        $tab["tri_par"] = $tmpSort[0];
	    } else {
	        $tab["nom_tri"] = "";
	        $tab["tri_par"] = ( $default_sort != "" ? $default_sort : ( $this->sortName == "notices" ? "d_num_6,c_text_1" : ""));
	    }
	    return $tab;
	}

	/**
	 * Retourne un tableau avec le nom du tri et la construction du tri
	 */
	public function recupTriParId($id) {
	    // tri par défaut...
	    $tab = array();
	    if($id == "default"){
	        return $this->recupTriParDefaultId();
	    }
	    $id = intval($id);
		switch($this->typeData) {
			case 'base':
				$result = pmb_mysql_query("SELECT nom_tri, tri_par FROM tris WHERE id_tri=" . $id);
				if (pmb_mysql_num_rows($result)) {
					$tab = pmb_mysql_fetch_assoc($result);
					pmb_mysql_free_result($result);
					$tab["nom_tri"] = translation::get_translated_text($id, 'tris', 'nom_tri', $tab["nom_tri"]);
				} else {
					$tab["nom_tri"] = "";
					$tab["tri_par"] = "";
				}
				return $tab;
			case 'session':
				if(isset($_SESSION["sortname".$this->sortName.$id]) && $_SESSION["sortname".$this->sortName.$id]){
					$tab["nom_tri"] = $_SESSION["sortname".$this->sortName.$id];
					$tab["tri_par"] = $_SESSION["sort".$this->sortName.$id];
				} else {
					$tab["nom_tri"] = "";
					$tab["tri_par"] = "";
				}
				return $tab;
		}
	}

	/**
	 * Pour initialiser un parcours des tris
	 * Retourne le nombre de tris
	 */
	public function initParcoursTris($objSort) {
	    global $categ, $num_segment;

		//on initialise la position du parcours
		$this->posParcours = 0;
		$this->nbResult=0;
		$this->tabParcours=null;

		switch($this->typeData) {
			case 'base':
			    if ("search_universes" == $categ) {
			        $result = pmb_mysql_query("SELECT search_segment_sort FROM search_segments WHERE id_search_segment=" . $num_segment);
			        $result = pmb_mysql_fetch_object($result)->search_segment_sort;
			        if ($result) {
			            $different_sorts = explode('||', $result);
			            //on charge les tris dans un tableau
			            foreach ($different_sorts as $sort) {
			                $sort_to_name = explode('|', $sort);
			                $this->tabParcours[$this->nbResult]['id_tri'] = $this->nbResult;
			                $this->tabParcours[$this->nbResult]['nom_tri'] = $sort_to_name[1];
			                $this->tabParcours[$this->nbResult]['tri_par'] = $sort_to_name[0];
			                $this->nbResult++;
			            }

			        }
			        //s'il n'y a pas de tris
			        if ($this->nbResult==0) {
			            //on vide la session stockant le tri en cours
			            $_SESSION["tri"] = "";
			        }
			        return $this->nbResult;
			    }
				//echo "SELECT id_tri, nom_tri, tri_par FROM tris WHERE tri_reference='" . $this->sortName . "' ORDER BY nom_tri<br />";
				$result = pmb_mysql_query("SELECT id_tri, nom_tri, tri_par FROM tris WHERE tri_reference='" . $this->sortName . "' ORDER BY nom_tri;");
				if ($result) {
					//on charge les tris dans un tableau
					while ($this->tabParcours[$this->nbResult] = pmb_mysql_fetch_assoc($result)) {
						$this->nbResult++;
					}
					pmb_mysql_free_result($result);

					//s'il n'y a pas de tris
					if ($this->nbResult==0) {
						//on vide la session stockant le tri en cours
						$_SESSION["tri"] = "";
					}
				}
				return $this->nbResult;
			case 'session':
				$this->nbResult = (isset($_SESSION["nb_sort".$this->sortName]) ? $_SESSION["nb_sort".$this->sortName] : 0);
				//s'il n'y a pas de tris
				if ($this->nbResult==0) {
					//on vide la session stockant le tri en cours
					$_SESSION["last_sort".$this->sortName]="";
				} else {
					//on charge les tris dans un tableau
					for($i=0; $i<$this->nbResult; $i++) {
						$this->tabParcours[$i]["id_tri"] = $i;
						$this->tabParcours[$i]["nom_tri"] = $objSort->descriptionTri($_SESSION["sort".$this->sortName.$i]);
						if (!isset($_SESSION["sort".$this->sortName.$i])) {
							$_SESSION["sort".$this->sortName.$i] = "";
						}
						$this->tabParcours[$i]["tri_par"] = $_SESSION["sort".$this->sortName.$i];
					}
				}
				return $this->nbResult;
		}
	}

	/**
	 * Renvoi le tri suivant dans un parcours
	 */
	public function parcoursTriSuivant() {
		switch($this->typeData) {
			case 'base':
				$result = (isset($this->tabParcours[$this->posParcours]) ? $this->tabParcours[$this->posParcours] : '');
				$this->posParcours++;
				return $result;
			case 'session':
				$result = (isset($this->tabParcours[$this->posParcours]) ? $this->tabParcours[$this->posParcours] : '');
				$this->posParcours++;
				return $result;
		}
	}

	/**
	 * Enregistre un tri
	 */
	public function enregistreTri($id,$nomTri,$desTri) {
		global $msg;
		global $charset;

		$id = intval($id);
		switch($this->typeData) {
			case 'base':
				$txt_requete = "";

				//modif ou insertion ?
				if ($id) {
					//on modifie le tri avec les nouveaux criteres
				    $txt_requete = "UPDATE tris SET nom_tri='" . addslashes($nomTri) . "', tri_par='" . addslashes($desTri) . "' ";
					$txt_requete .= "WHERE id_tri='" . $id . "'";
				} else {
					//on vérifie que le nom de tri n'existe pas
					$txt_requete = "SELECT id_tri FROM tris WHERE nom_tri='" . addslashes($nomTri) . "'";
					$txt_requete .= " AND tri_reference='" . $this->sortName . "'";
					if (pmb_mysql_num_rows(pmb_mysql_query($txt_requete)) == 0) {
						//on genere la requete d'insertion
						$txt_requete = "INSERT INTO tris (id_tri, tri_reference, nom_tri, tri_par) ";
						$txt_requete .= "VALUES ('','" . $this->sortName . "','" . addslashes($nomTri) . "','" . addslashes($desTri) . "')";
					} else {
						//le nom existe : on le dit
						return "<script>alert ('" . $msg['tri_existant'] . "');history.back();</script>";
					}
				}
				//execution de la requete de modif ou d'insertion
				$requete = pmb_mysql_query($txt_requete);

				if (!$requete) {
					// il y a eu une erreur d'execution de la requete
					return "Erreur mysql : " . $txt_requete . "<br />" . pmb_mysql_error();
				} else {
				    if(!$id) {
				        $id = pmb_mysql_insert_id();
				    }
				    $translation = new translation($id, 'tris');
				    $translation->update('nom_tri', "nom_tri");
				}
				return "";
			case 'session':

			    if (!isset($_SESSION["nb_sort".$this->sortName])) {
			        $_SESSION["nb_sort".$this->sortName] = 0;
			    }

				//si nombre de tris enregistrés dans la session n'est pas null, parcours des variables de session pour l'existence tri et sauvegarde
				if ($_SESSION["nb_sort".$this->sortName]<=0) {
					$_SESSION["sort".$this->sortName."0"] = htmlentities($desTri,ENT_QUOTES,$charset);
					$_SESSION["nb_sort".$this->sortName] = 1;
				} else {
					$bool=false;
					if (is_array($_SESSION["nb_sort".$this->sortName])) {
						for ($i=0;$i<$_SESSION["nb_sort".$this->sortName];$i++) {
							if ($_SESSION["sort".$this->sortName.$i] == htmlentities($desTri,ENT_QUOTES,$charset)) {
								$bool=true;
							}
						}
					}
					if ($bool==true) {
						return "<script>alert ('".$msg['tri_existant']."');</script>";
					} else {
						$_SESSION["sort".$this->sortName.$_SESSION["nb_sort".$this->sortName]] = htmlentities($desTri,ENT_QUOTES,$charset);
						$_SESSION["nb_sort".$this->sortName]++;
						return "";
					}
				}
				break;

		}
	}

	/**
	 * Supprime un tri
	 */
	public function supprimeTri($idTri) {
	    $idTri = intval($idTri);
	    $tabParcours = array();
		switch($this->typeData) {
			case 'base':
				// on vérifie que le tri existe bien
				if (pmb_mysql_num_rows(pmb_mysql_query("SELECT * FROM tris WHERE id_tri='" . $idTri . "'")) > 0) {
				    //Les traductions d'abord
				    translation::delete($idTri, "tris");
					//c'est ok on supprime
					$result = pmb_mysql_query("DELETE FROM tris WHERE id_tri='" . $idTri . "'");
					if (!$result) {
						print "Erreur mysql : " . pmb_mysql_error();
					}
				}
				break;
			case 'session':
				//on charge les tris dans un tableau sauf celui à supprimer
				$posTab = 0;
				for($i=0; $i<$_SESSION['nb_sort'.$this->sortName]; $i++) {
					if ($i != $idTri) {
						//ce n'est pas le tri a supprimer
						//on le stocke dans le tableau
						$tabParcours[$posTab] = $_SESSION["sort".$this->sortName.$i];
						$posTab++;
					}
				}

				//on rempli les variables session sans l'element a supprimer
				for($i=0; $i<$posTab; $i++) {
					$_SESSION['sort'.$this->sortName.$i] = $tabParcours[$i];
				}
				$_SESSION['nb_sort'.$this->sortName]--;

				//si il ne subsiste plus d'historique de tris, mise à null des variables de session
				if ($_SESSION['nb_sort'.$this->sortName]==0) {
					$_SESSION['last_sort'.$this->sortName]="";
				}
				break;
		}
	}

	public function applyTri($id) {
	    global $sort;

	    $id = intval($id);
	    if($id && !isset($sort)) {
	        //Le tri est défini en gestion, on l'ajoute aux tris dispos en OPAC si nécessaire
	        $res_tri = pmb_mysql_query("SELECT * FROM tris WHERE id_tri=".$id);
	        if (pmb_mysql_num_rows($res_tri)) {
	            $last = "";
	            $row_tri = pmb_mysql_fetch_object($res_tri);
	            if ($_SESSION["nb_sort".$this->sortName]<=0) {
	                $_SESSION["sort".$this->sortName.$_SESSION["nb_sort".$this->sortName]]=$row_tri->tri_par;
	                if ($row_tri->id_tri) {
	                    $_SESSION["sortid".$this->sortName.$_SESSION["nb_sort".$this->sortName]]=$row_tri->id_tri;
	                }
	                if ($row_tri->nom_tri) {
	                    $_SESSION["sortname".$this->sortName.$_SESSION["nb_sort".$this->sortName]]=translation::get_translated_text($row_tri->id_tri, 'tris', 'nom_tri', $row_tri->nom_tri);
	                }
	                $last = 0;
	                $_SESSION["nb_sort".$this->sortName]++;
	            } else {
	                $bool=false;
	                for ($i=0;$i<$_SESSION["nb_sort".$this->sortName];$i++) {
	                    if ($_SESSION["sort".$this->sortName.$i] == $row_tri->tri_par) {
	                        $bool=true;
	                        $last = $i;
	                    }
	                }
	                if (!$bool) {
	                    $_SESSION["sort".$this->sortName.$_SESSION["nb_sort".$this->sortName]] = $row_tri->tri_par;
	                    if ($row_tri->id_tri) {
	                        $_SESSION["sortid".$this->sortName.$_SESSION["nb_sort".$this->sortName]]=$row_tri->id_tri;
	                    }
	                    if ($row_tri->nom_tri) {
	                        $_SESSION["sortname".$this->sortName.$_SESSION["nb_sort".$this->sortName]] = translation::get_translated_text($row_tri->id_tri, 'tris', 'nom_tri', $row_tri->nom_tri);
	                    }
	                    $last = $_SESSION["nb_sort".$this->sortName];
	                    $_SESSION["nb_sort".$this->sortName]++;
	                }
	            }
	            $_SESSION["last_sort".$this->sortName]="$last";
	        }
	    }elseif(isset($sort)){
	        $_SESSION["last_sort".$this->sortName]=$sort;
	    }
	}
}



/**
 * Classe de tri des résultats de recherche dans le catalogue
 * Utilise une variable de session("tri") pour stocker le tri en cours
 *
 */
class sort {
	public $params;
	public $error = false;
	public $error_message = "";
	public $table_tri_tempo = "tri_tempo"; //table temporaire à utiliser
	public $table_primary_tri_tempo; //Clé primaire de la table temporaire à créer
	public $limit; //limitation des enregistrements à utiliser dans la requête de tri pour le pager
	public $champs_select; //champs éventuels à retourner dans la requête
	public $table_select; //table éventuelle à retourner dans la requête
	public $table_primary_key_select; //clé de la table éventuelle à retourner dans la requête
	public $dSort; // objet d'acces aux informations
	private static $nb_instance = 1;

	public $current_engine = 'memory'; //memory | MyISAM | InnoDB

	public $nb_max_tri = 50000;
	/**
	 * Applique le tri donné
	 *
	 * @param string $sort_name nom du tri à appliquer
	 * @param string $accesTri
	 */
	public function __construct($sort_name, $accesTri) {
	    //On ne récupère l'id de l'authperso dans $sort_name, sinon on ne peut pas récupérer le fichier XML
	    if (strpos($sort_name, "authperso") !== false) {
	        $sort_name = "authperso";
	    }
		if ($sort_name) {
			$sname = $sort_name;
		} else {
			$sname = 'notices';
		}
		/*if(self::$nb_instance == 1) {
			mysql_set_tmp_memory_table_size();
		}*/
		$this->table_tri_tempo .= "_".self::$nb_instance;
		self::$nb_instance++;

		if ($accesTri) {
			$this->dSort = new dataSort($sname,$accesTri);
		} else {
			$this->dSort = new dataSort($sname,'base');
		}
		//on charge le fichier XML
		$this->parse();
		$this->init_nb_max_tri();
		$this->init_parameters_subst();
	}

	protected function init_nb_max_tri() {
	    global $opac_nb_max_tri;

	    if ($this->nb_max_tri < $opac_nb_max_tri) {
	        $this->nb_max_tri = intval($opac_nb_max_tri);
	    }
	}

	/**
	 * initialise les parametres de substitution de tri
	 * utilise pour les tris personnalises par defaut
	 *
	 * @return void
	 */
	protected function init_parameters_subst() {
		global $include_path;
		if (file_exists($include_path."/parameters_subst/sort_parameters_subst.xml")){
			$parameter_subst = new parameters_subst($include_path."/parameters_subst/sort_parameters_subst.xml", 1);
			$parameter_subst->extract();
		}
	}
	
	/**
	 * Affiche l'écran de choix des tris enregistrés
	 */
	public function show_tris_form() {
		global $show_tris_form;
		global $msg;

		$type = "NOTI";
		if ($this->params['REFERENCE'] == "authorities") {
    		$type = "AUT";
		}

		if ($this->dSort->initParcoursTris($this) == 0 ) {
			//il n'y a pas de tris enregistrés
			//on renvoie un message pour le dire
			$tris = $msg['aucun_tri'];
		} else {
		    // creation du tableau de la liste des tris enregistrés
		    list_sorts_ui::set_sort($this);
		    list_sorts_ui::set_sort_type($type);
		    $tris = list_sorts_ui::get_instance()->get_display_list();
		}
		$tris_form = str_replace("!!caller!!", $this->caller, $show_tris_form);
		$callback = "parent.document.getElementById('history').style.display='none';";
		switch ($this->caller){
			case "etagere" :
			case "rss_flux" :
				$callback .="parent.window.getSort(0,''); return false;";
				break;
			case "external" :
				$callback .="parent.location.href='./recall.php?current=".$_SESSION["CURRENT"]."&t=$type&tri=-1&external=1&reference=".$this->params['REFERENCE']."&type_tri=". $this->dSort->sortName ."'; return false;";
				break;
			default :
			    $callback .="parent.location.href='./recall.php?current=".$_SESSION["CURRENT"]."&t=$type&tri=-1&reference=".$this->params['REFERENCE']."&type_tri=". $this->dSort->sortName ."'; return false;";
				break;
		}
		$tris_form = str_replace("!!callback!!", $callback, $tris_form);

		//on remplace dans le template les informations issues de la base
		$tris_form = str_replace("!!sortname!!", $this->dSort->sortName, $tris_form);
		$tris_form = str_replace("!!liste_tris!!", $tris, $tris_form);
		return $tris_form;
	}

	public function show_popup_tris_form() {
		global $msg;
		global $show_popup_tris_form;

		$type = "NOTI";
		if ($this->params['REFERENCE'] == "authorities") {
    		$type = "AUT";
		}

		if ($this->dSort->initParcoursTris($this) == 0 ) {
			//il n'y a pas de tris enregistrés
			//on renvoie un message pour le dire
			$tris = $msg['aucun_tri'];
		} else {
		    list_sorts_ui::set_sort($this);
		    list_sorts_ui::set_sort_type($type);
		    list_sorts_ui::set_popup(true);
		    $tris = list_sorts_ui::get_instance()->get_display_list();
		}

		$tris_form = str_replace("!!caller!!", $this->caller, $show_popup_tris_form);
	    $callback ="./recall.php?current=". (isset($_SESSION["CURRENT"]) ? $_SESSION["CURRENT"] : '') ."&t=$type&tri=-1&reference=".$this->params['REFERENCE']."&type_tri=". $this->dSort->sortName."&ajax=1";
		switch ($this->caller){
			case "external" :
			    $callback .= "&external=1";
			    break;
			case "etagere" :
			case "rss_flux" :
			    // a revoir
			    $callback = "";
			    break;
		}
		$tris_form = str_replace("!!callback!!", $callback, $tris_form);

		//on remplace dans le template les informations issues de la base
		$tris_form = str_replace("!!sortname!!", $this->dSort->sortName, $tris_form);
		$tris_form = str_replace("!!liste_tris!!", $tris, $tris_form);
		return $tris_form;
	}

	/**
	 * Retourne la liste des tris disponibles
	 * @return string formulaire
	 */
	public function show_popup_tris_form_segment() {
	    global $msg;
	    global $show_popup_tris_form_segment;
	    global $num_segment;

	    if ($this->dSort->initParcoursTris($this) == 0 ) {
	        //il n'y a pas de tris enregistrés
	        //on renvoie un message pour le dire
	        $tris = $msg['aucun_tri'];
	    } else {
	        list_sorts_ui::set_sort($this);
	        list_sorts_ui::set_popup(true);
	        $tris = list_sorts_ui::get_instance()->get_display_list();
	    }
	    $tris_form = str_replace("!!caller!!", $this->caller, $show_popup_tris_form_segment);

	    //on remplace dans le template les informations issues de la base
	    $tris_form = str_replace("!!sortname!!", $this->dSort->sortName, $tris_form);
	    $tris_form = str_replace("!!liste_tris!!", $tris, $tris_form);
	    $tris_form = str_replace("!!id_segment!!", $num_segment, $tris_form);
	    return $tris_form;
	}

	/**
	 * Affiche l'écran de sélection des criteres de tri
	 */
	public function show_sel_form($id_tri=0) {
		switch($this->dSort->typeData) {
			case 'base':
				return $this->show_sel_formAdmin($id_tri);
			case 'session':
				return $this->show_sel_formOPAC($id_tri);
		}
	}

	/**
	 * Fonction de calcul de la visibilité d'un critère de tri
	 */
	public function visibility($field) {
		$visibility=true;
		if (isset($field["VAR"]) && $field["VAR"]) {
			for ($i=0; $i<count($field["VAR"]); $i++) {
				$name=$field["VAR"][$i]["NAME"] ;
				global ${$name};
				if ($field["VAR"][$i]["VISIBILITY"]=="yes") {
					$visibility=true;
				} else {
					$visibility=false;
				}
				if (isset(${$name})) {
					for ($j=0; $j<count($field["VAR"][$i]["VALUE"]); $j++) {
						if (${$name} == $field["VAR"][$i]["VALUE"][$j]["value"]) {
							if ($field["VAR"][$i]["VALUE"][$j]["VISIBILITY"]=="yes") {
								$visibility=true;
							} else {
								$visibility=false;
							}
						}
					}
				}
			}
		}
		return $visibility;
	}

	protected function _compare_labels($a, $b) {
	    global $msg;

	    if(!empty($a["LABEL"])) $cmp_a = $a["LABEL"];
	    else $cmp_a = $msg[$a["NAME"]];
	    if(!empty($b["LABEL"])) $cmp_b = $b["LABEL"];
	    else $cmp_b = $msg[$b["NAME"]];
	    return strcmp(strtolower(convert_diacrit($cmp_a)), strtolower(convert_diacrit($cmp_b)));
	}

	protected function _sort_fields($fields) {
	    usort($fields, array($this, '_compare_labels'));
	    return $fields;
	}

	/**
	 * Affiche l'écran de sélection des criteres de tri
	 */
	public function show_sel_formAdmin($id_tri, $index_tri=null, $sort_string=null) {
	    global $show_sel_form, $show_sel_form_segments, $sel_form_actions;
	    global $num_segment, $origin;
		global $charset;
		global $msg;

		if (is_null($sort_string)) {
		    $sort_string = "";
		}
		//les champs de tris possible
		$fields = $this->params["FIELD"];
        $fields = $this->_sort_fields($fields);

		//initialisation des variables
		$liste_selectionnes = "";
		$nom_du_tri = "";

		//génération de la liste des criteres
		$liste_criteres = "";

		//si id_tri est renseigné, c'est alors une modification du tri sélectionné
		if ($id_tri!==0 || null!==$index_tri) {
		    if($origin == "search_segment"){
		        $result = [];
		        if ("" != $sort_string) {
		        }
		        $result['nom_tri'] = trim(explode('|', $sort_string)[1]);
		        $result['tri_par'] = trim(explode('|', $sort_string)[0]);
	            //on charge les tris dans un tableau
		    }else {
			     $result = $this->dSort->recupTriParId($id_tri);
		    }
			//$requete = pmb_mysql_query("SELECT nom_tri, tri_par FROM tris WHERE id_tri='" . $id_tri . "'");
			if ($result) {
				//$result = pmb_mysql_fetch_array($requete);
				$nom_du_tri = $result['nom_tri'];

				//recherche et décomposition du tri
				$tri_par = explode(",", $result['tri_par']);

				for ($i = 0; $i < count($tri_par); $i++) {
				    $tri_par[$i] = trim($tri_par[$i]);
					//on decompose la description du critere de tri (c_num_2)
					$tri_par1 = explode("_", $tri_par[$i]);

					for ($j = 0; $j < count($fields); $j++) {
						//on parcours tous les champs (pour récuperer le nom)
					    if (isset($tri_par1[2]) && $fields[$j]["ID"] == trim($tri_par1[2])) {
							//on est dans le bon champs

							//on determine le type et le sens du tri pour l'affichage
							switch ($tri_par1[1]) {
								case 'num' :
									if ($tri_par1[0] == "c")
										$debut = "0-9 ";
									else
										$debut = "9-0 ";
									break;
								case 'text' :
									if ($tri_par1[0] == "c")
										$debut = "A-Z ";
									else
										$debut = "Z-A ";
									break;
							}

							//la liste des champs sélectionnés
							$liste_selectionnes .= "<option value='" . $tri_par1[0] . "_" . $tri_par1[1] . "_" . $tri_par1[2] . "'>";
							//si champ perso, on a déjà le libellé
							if(isset($fields[$j]['SOURCE']) && ($fields[$j]['SOURCE'] == "cp" || $fields[$j]['SOURCE'] == "onto")) $name = $fields[$j]['LABEL'];
							else $name = $msg[$fields[$j]['NAME']];
							$liste_selectionnes .= $debut . "" . htmlentities($name, ENT_QUOTES, $charset);
							$liste_selectionnes .= "</option>\n";

							//ce champ est utilise donc on ne l'affichera pas
							$fields[$j]["UTILISE"] = true;

						}
					}
				}

				//on créé la liste des criteres restants
				for ($j = 0; $j < count($fields); $j++) {
					// sans les champs déja utilisés

				    if ((isset($fields[$j]["UTILISE"]) && $fields[$j]["UTILISE"]!=true) || !isset($fields[$j]["UTILISE"])){
				        if ($this->visibility($fields[$j])) {
							//si champ perso, on a déjà le libellé
				            if(isset($fields[$j]['SOURCE']) && ($fields[$j]['SOURCE'] == "cp" || $fields[$j]['SOURCE'] == "onto")) $name = $fields[$j]['LABEL'];
				            else $name = $msg[$fields[$j]['NAME']];
							$liste_criteres .= "<option value='c_" . $fields[$j]["TYPE"] . "_" . $fields[$j]["ID"] . "'>" . htmlentities($name, ENT_QUOTES, $charset) . "</option>\n";
						}
					}

				}

			}
		} else {
			//on créé la liste des criteres
			for ($j = 0; $j < count($fields); $j++) {
				if ($this->visibility($fields[$j])) {
					//si champ perso, on a déjà le libellé
				    if(isset($fields[$j]['SOURCE']) && ($fields[$j]['SOURCE'] == "cp" || $fields[$j]['SOURCE'] == "onto")) $name = $fields[$j]['LABEL'];
				    else $name = $msg[$fields[$j]['NAME']];
					$liste_criteres .= "<option value='c_" . $fields[$j]["TYPE"] . "_" . $fields[$j]["ID"] . "'>" . htmlentities($name, ENT_QUOTES, $charset) . "</option>\n";
				}
			}
		}

		//on remplace toutes les variables dans le template
		if (isset($origin) && 'search_segment' == $origin) {
		    $sel_form = str_replace("!!id_tri!!", $id_tri, $show_sel_form_segments);
		} else {
    		$sel_form = str_replace("!!id_tri!!", $id_tri, $show_sel_form);
		}
		if (isset($num_segment)) {
		    $sel_form = str_replace("!!id_segment!!", $num_segment, $sel_form);
		}
		$sel_form = str_replace("!!sel_form_action!!", $sel_form_actions, $sel_form);
		$sel_form = str_replace("!!caller!!", $this->caller, $sel_form);
		$sel_form = str_replace("!!sortname!!", $this->dSort->sortName, $sel_form);
		$sel_form = str_replace("!!nom_tri!!", htmlentities($nom_du_tri, ENT_QUOTES, $charset), $sel_form);
		$sel_form = str_replace("!!liste_criteres!!", $liste_criteres, $sel_form);
		$sel_form = str_replace("!!liste_selectionnes!!", $liste_selectionnes, $sel_form);

		return $sel_form;
	}

    public function show_sel_formOPAC() {
    	global $show_sel_form;
		global $liste_criteres_tri;
		global $charset;
		global $msg;
		global $opac_nb_max_criteres_tri;

		$fields = $this->params["FIELD"];
		$fields = $this->_sort_fields($fields);

		$liste_criteres = '';
    	for ($i=0;$i<count($fields);$i++) {
    		if ($this->visibility($fields[$i])) {
    			$liste_criteres.="<option value='".$fields[$i]["ID"]."'>".htmlentities($msg[$fields[$i]["NAME"]],ENT_QUOTES,$charset)."</option>\n";
    		}
    	}

    	$listes_tri = "";
    	for ($i=1;$i<$opac_nb_max_criteres_tri;$i++) {
    		$listes_tri .= str_replace("!!idLigne!!",$i,$liste_criteres_tri);
    	}

    	$sel_form = str_replace("!!liste_criteres_tri!!", $listes_tri, $show_sel_form);
    	$sel_form = str_replace("!!liste_criteres!!", $liste_criteres, $sel_form);

    	return $sel_form;
    }

	/**
	 * Enregistre les criteres de tri dans la table tris
	 */
	public function sauvegarder($id_tri, $nom_tri, $tris_par) {

		$criteres = implode(",",$tris_par);

		return $this->dSort->enregistreTri($id_tri,$nom_tri,$criteres);

	}

	/**
	 * Supprime un tri sauvegarder
	 */
	public function supprimer($id_tri) {
		return $this->dSort->supprimeTri($id_tri);
	}

	/**
	 * Retourne le texte de description du tri à partir de sa description
	 */
	public function descriptionTri($desTri) {
		global $msg;

		//récuperations des champs
		$fields = $this->params["FIELD"];

		$tris_par = explode(",",$desTri);

		$trier_par_texte = "";
		foreach ($tris_par as $selectValue) {
			//découpage du champ (ex : c_num_2 (croissance ou décroissance (c ou d),
			//type de champ (num,text,...) et id du champ)
			$temp = explode("_", $selectValue);

			//on genere le texte descriptif à afficher
			if (!empty($fields) && is_array($fields)) {
    			for ($i = 0; $i < count($fields); $i++) {
    				if (isset($temp[2]) && $fields[$i]["ID"] == $temp[2]) {
    					$trier_par_texte .= $msg[$fields[$i]["NAME"]] . " ";
    					if ($temp[0] == "c") {
    						$trier_par_texte .= $msg["tri_texte_croissant"];
    					} else {
    						$trier_par_texte .= $msg["tri_texte_decroissant"];
    					}
    					$trier_par_texte .= ",";
    				}
    			}
			}
		}
		//on enleve la derniere virgule et on ajoute la )
		$trier_par_texte = substr($trier_par_texte, 0, strlen($trier_par_texte) - 1);

		return $trier_par_texte;
	}


	/**
	 * Retourne le texte de description du tri a partir d'un id
	 */
	public function descriptionTriParId($id_tri) {
		global $charset;

		if ($id_tri!="") {

			//récupération de la description du tri
			$result = $this->dSort->recupTriParId($id_tri);

			$nom_tri = $result['nom_tri'];

			$trier_par_texte = "(" . htmlentities($this->descriptionTri($result['tri_par']),ENT_QUOTES,$charset) . ")";

			//on concatene le message complet
			$trier_par_texte = $nom_tri." ".$trier_par_texte;

			return $trier_par_texte;
		} else {
			return "";
		}
	}

	/**
	 * Ajoute un tri (croissant) sur le titre des notices s'il n'y en a pas
	 *
	 * @param array $trier_par
	 *
	 * @return array
	 */
	public function ajoutTriForUniqueRender($trier_par) {
	    switch ($this->dSort->sortName) {
	        case 'notices':
	        	if( !in_array('c_text_1', $trier_par) && !in_array('d_text_1', $trier_par) ) {
	                $trier_par[] = 'c_text_1';
	            }
	            break;
	    }
	    return $trier_par;
	}

	protected function get_query_create_temporary_table($nomTable, $referenceKey, $nomChp, $selectTempo='', $current_engine='') {
	    if (empty($current_engine)) {
	        $current_engine = $this->current_engine;
	    }
	    $cmd_table = "CREATE TEMPORARY TABLE ".$nomTable ."(".$referenceKey." INT NOT NULL, ".$nomChp." varchar(255) not null default '') ENGINE=".$current_engine;
	    if($selectTempo) {
	        $cmd_table .= " (".$selectTempo.")";
	    }
	    return $cmd_table;
	}

	protected function gen_temporary_table($nomTable, $referenceKey, $nomChp, $selectTempo='') {
	    global $default_storage_engine;

		$cmd_table = "DROP TEMPORARY TABLE IF EXISTS ".$nomTable;
		pmb_mysql_query($cmd_table);
		$cmd_table = $this->get_query_create_temporary_table($nomTable, $referenceKey, $nomChp, $selectTempo);
		$result = pmb_mysql_query($cmd_table);
		if (!$result){
		    $cmd_table = "DROP TABLE IF EXISTS " . $nomTable;
		    pmb_mysql_query($cmd_table);
		    $cmd_table = $this->get_query_create_temporary_table($nomTable, $referenceKey, $nomChp, $selectTempo, $default_storage_engine);
		    pmb_mysql_query($cmd_table);
		}
		$cmd_table = "ALTER TABLE " . $nomTable . " ADD INDEX (" .$referenceKey.")";
		pmb_mysql_query($cmd_table);
	}

	/**
	 * Applique le tri sélectionné
	 * Renvoie la requete finale utilisant les criteres de tri
	 */
	public function appliquer_tri($idTri_orTri, $selectTempo, $nomColonneIndex,$debLimit=0,$nbLimit=0) {
	    global $default_storage_engine;

		//récuperations des champs
		$fields = $this->params["FIELD"];
		$tableEnCours = $this->table_tri_tempo;

		//creation de la table de tri
		//$cmd_table = "DROP TABLE " . $tableEnCours;
		//pmb_mysql_query($cmd_table);
		//$cmd_table = "CREATE TABLE " . $tableEnCours . " ENGINE=".$this->current_engine." (".$selectTempo.")";
		$cmd_table = "CREATE TEMPORARY TABLE IF NOT EXISTS " . $tableEnCours . " ENGINE=".$this->current_engine." (".$selectTempo.")";
		$result = pmb_mysql_query($cmd_table);
		if (!$result){
		    $cmd_table = "DROP TABLE IF EXISTS " . $tableEnCours;
		    pmb_mysql_query($cmd_table);
		    $cmd_table = "CREATE TEMPORARY TABLE " . $tableEnCours . " ENGINE=".$default_storage_engine." (".$selectTempo.")";
		    pmb_mysql_query($cmd_table);
		}
		if (!pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM ". $tableEnCours ." LIKE '". $nomColonneIndex ."'"))){
			$cmd_table = "ALTER TABLE " . $tableEnCours . " ADD PRIMARY KEY (" . $nomColonneIndex.")";
			pmb_mysql_query($cmd_table);
		}

		//récupération de la description du tri
		$result = [];
		if (is_array($idTri_orTri)) {
			$result = $idTri_orTri;
		} else {
			$result = $this->dSort->recupTriParId($idTri_orTri);
		}
		$trier_par = array();
		if(!empty($result['tri_par'])) {
		    $trier_par = explode(",",$result['tri_par']);
		}
		$trier_par = $this->ajoutTriForUniqueRender($trier_par);
		//suppression des espaces avant après les chaines de caractères
		$trier_par = clean_string($trier_par);

		if (pmb_mysql_num_rows(pmb_mysql_query($selectTempo)) > $this->nb_max_tri) {
		    //Gardons uniquement le premier tri
		    if (count($trier_par) > 1) {
		        $trier_par = array_slice($trier_par, 0, 1);
		    }
		}
		//parcours des champs sur lesquels trier
		$orderby = '';
		for ($j = 0; $j < count($trier_par); $j++) {
			//découpage du champ (ex : c_num_2 (croissance ou décroissance (c ou d),
			//type de champ (num,text,...) et id du champ)
			$temp = explode("_", $trier_par[$j]);

			//on parcours tous les champs de tri possible
			for ($i = 0; $i < count($fields); $i++) {

				//afin de trouver ceux sur lesquels le tri s'applique
				if (isset($temp[2]) && $fields[$i]["ID"] == $temp[2]) {
					//on est sur un champ de tri

					//suivant le type de champ
					switch ($fields[$i]["TYPEFIELD"]) {
						case "internal":
							//c'est un champ de la requete de base

							//on verifie que le champ est dans la table temporaire
							$requete_fields = pmb_mysql_query("SELECT * FROM " . $tableEnCours . " LIMIT 1");
							$x = 0;
							while ($x < pmb_mysql_num_fields($requete_fields)) {
								$ligne = pmb_mysql_fetch_field($requete_fields, $x);
								if ($ligne->name == $fields[$i]["TABLEFIELD"][0]['value']) {
									//le champ est la donc on ajoute le champ au order
									$orderby .= $this->ajoutOrder($fields[$i]["TABLEFIELD"][0]['value'],$temp[0]) . ",";
									$x = pmb_mysql_num_fields($requete_fields);
								}
								$x++;
							}
							pmb_mysql_free_result($requete_fields);
							break;

						case "select":
							//une requete union est nécéssaire

							//le nom du champ on ajoute tb pour corriger le probleme des noms numeriques
							$nomChamp = "tb".$fields[$i]["NAME"];

							//on ajoute la colonne au orderby
							$orderby .= $this->ajoutOrder($nomChamp,$temp[0]) . ",";

							//on ajoute la colonne à la table temporaire
							$this->ajoutColonneTableTempo($tableEnCours, $nomChamp, $temp[1]);


							//on parcours la ou les tables pour generer les updates
							if (!empty($fields[$i]["TABLE"])) {
    							for ($x = 0; $x < count($fields[$i]["TABLE"]); $x++) {

    								$requete = $this->genereRequeteUpdate($fields[$i]["TABLE"][$x], $tableEnCours, $nomChamp, $nomColonneIndex);

    								//echo("updateSort:".$requete."<br />");
    								pmb_mysql_query($requete);

    								//pour les tris sur les libellés de marclist
    								if(isset($fields[$i]["TABLE"][$x]["TABLEFIELD"][0]["MARCTYPE"])) {
    									$requete = $this->genereRequeteUpdateFromMarcType($fields[$i]["TABLE"][$x]["TABLEFIELD"][0]["MARCTYPE"], $tableEnCours, $nomChamp);
    									pmb_mysql_query($requete);
    								}
    							}
							}

							//on a aussi des champs persos maitenant...
							if(isset($fields[$i]['SOURCE']) && $fields[$i]['SOURCE'] == "cp"){
								$requete = $this->generateRequeteCPUpdate($fields[$i], $tableEnCours, $nomChamp);
								pmb_mysql_query($requete);
							}

							break;
						case "authority":
						    //le nom du champ on ajoute tb pour corriger le probleme des noms numeriques
						    $nomChamp = "tb".$fields[$i]["NAME"];

						    //on ajoute la colonne au orderby
						    $orderby .= $this->ajoutOrder($nomChamp,$temp[0]) . ",";

						    //on ajoute la colonne à la table temporaire
						    $this->ajoutColonneTableTempo($tableEnCours, $nomChamp, $temp[1]);

						    //on a aussi des champs persos maitenant...
						    if(isset($fields[$i]['SOURCE']) && $fields[$i]['SOURCE'] == "cp"){
						        $requete = $this->generateRequeteCPAuthorityUpdate($fields[$i], $tableEnCours, $nomChamp);
						        pmb_mysql_query($requete);
						    }
						    break;
						case "callable":
						    if (!empty($fields[$i]["CALLABLE"])) {
						        foreach ($fields[$i]["CALLABLE"] AS $callable){
    						        if (!file_exists($callable['CLASS_PATH'])) {
    						            continue;
    						        }
    						        require_once($callable['CLASS_PATH']);
    						        $callback_parameters = array($tableEnCours);
    						        if (!empty($callable['PARAMETERS'])) {
    						            $callback_parameters = array_merge($callback_parameters, explode(',', $callable['PARAMETERS']));
    						        }
    						        $tableEnCours = call_user_func_array(array($callable['CLASS_NAME'], $callable['METHOD']), $callback_parameters);
    						        $orderby .= $this->ajoutOrder($callback_parameters[1],$temp[0]) . ",";
						        }
						    }
						    break;
					} //switch
				} //if ($fields[$i]["ID"] == $temp[2]) {
			} //for ($i = 0; $i < count($fields); $i++) {
		} //for ($j = 0; $j < count($trier_par); $j++) {

		if ($orderby!="") {
			//on enleve la derniere virgule
			$orderby = substr($orderby, 0, strlen($orderby) - 1);

			//on va classer la table tempo suivant les criteres donnés
			$requete = "ALTER TABLE " . $tableEnCours ." ORDER BY ". $orderby;
			pmb_mysql_query($requete);
		}

		//on retourne la requete sur la table de tri
    	if ($this->table_select!="") {
    		//c'est une requete avec des informations extérieures
    		$requete = "SELECT " . $nomColonneIndex . "," . $this->champs_select;
    		$requete .= " FROM " . $this->table_tri_tempo . "," . $this->table_select;
    		$requete .= " WHERE " . $this->table_select . "." . $this->table_primary_key_select;
    		$requete .= "=" . $this->table_tri_tempo . "." . $nomColonneIndex;
    		$requete .= " GROUP BY " . $nomColonneIndex;
    		if ($orderby!="") $requete .= " ORDER BY " . $orderby;
    		if ($nbLimit>0) $requete .= " LIMIT " . $debLimit . "," . $nbLimit;
    	} else {
			if ($nbLimit>0) {
	    		//requete de base sur la table triée avec limit
				$requete = "SELECT * FROM " . $tableEnCours;
				if ($orderby!="") $requete .= " ORDER BY " . $orderby;
				$requete .= " LIMIT " . $debLimit . "," . $nbLimit;
			} else {
	    		//requete de base sur la table triée
				$requete = "SELECT " . $nomColonneIndex . " FROM " . $tableEnCours;
				if ($orderby!="") $requete .= " ORDER BY " . $orderby;
			}
    	}
 		return $requete;
	}

	public function appliquer_tri_from_tmp_table($idTri_orTri=0, $tableEnCours='', $nomColonneIndex='',$debLimit=0,$nbLimit=0){
		//récuperations des champs
		$fields = $this->params["FIELD"];

		//récupération de la description du tri
		if (is_array($idTri_orTri)) {
			$result = $idTri_orTri;
		} else {
			$result = $this->dSort->recupTriParId($idTri_orTri);
		}
		$trier_par = explode(",",$result['tri_par']);
		$trier_par = $this->ajoutTriForUniqueRender($trier_par);
		//suppression des espaces avant après les chaines de caractères
		$trier_par = clean_string($trier_par);

		if (pmb_mysql_num_rows(pmb_mysql_query("select * from ".$tableEnCours)) > $this->nb_max_tri) {
		    //Gardons uniquement le premier tri
		    if (count($trier_par) > 1) {
		        $trier_par = array_slice($trier_par, 0, 1);
		    }
	    }
		//parcours des champs sur lesquels trier
		$orderby = '';
		for ($j = 0; $j < count($trier_par); $j++) {
			//découpage du champ (ex : c_num_2 (croissance ou décroissance (c ou d),
			//type de champ (num,text,...) et id du champ)
			$temp = explode("_", $trier_par[$j]);
			//on parcours tous les champs de tri possible
			for ($i = 0; $i < count($fields); $i++) {
				//afin de trouver ceux sur lesquels le tri s'applique
				if (isset($temp[2]) && $fields[$i]["ID"] == $temp[2]) {
					//on est sur un champ de tri
					//suivant le type de champ
					switch ($fields[$i]["TYPEFIELD"]) {
						case "internal":
							//c'est un champ de la requete de base
							$nomChamp = $fields[$i]["TABLEFIELD"][0]['value'];
							//on verifie que le champ est dans la table temporaire
							$requete_fields = pmb_mysql_query("SELECT * FROM " . $tableEnCours . " LIMIT 1");
							$x = 0;
							if ($requete_fields) {
								while ($x < pmb_mysql_num_fields($requete_fields)) {
									$ligne = pmb_mysql_fetch_field($requete_fields, $x);
									if ($ligne->name == $nomChamp) {
										//le champ est la donc on ajoute le champ au order
										if($orderby!="") $orderby.=",";
										$orderby .= $this->ajoutOrder($nomChamp,$temp[0]);
										$x = pmb_mysql_num_fields($requete_fields);
									}
									$x++;
								}
								pmb_mysql_free_result($requete_fields);
							}
							break;
						case "select":
							//une requete union est nécéssaire
							//le nom du champ on ajoute tb pour corriger le probleme des noms numeriques
							$nomChamp = "tb".$fields[$i]["NAME"];
							//on ajoute la colonne au orderby
							if($orderby!="") $orderby.=",";
							$orderby .= $this->ajoutOrder($nomChamp,$temp[0]);
							//on ajoute la colonne à la table temporaire
							$this->ajoutColonneTableTempo($tableEnCours, $nomChamp, $temp[1]);

							//on parcours la ou les tables pour generer les updates
							if (!empty($fields[$i]["TABLE"])) {
    							for ($x = 0; $x < count($fields[$i]["TABLE"]); $x++) {
    								$requete = $this->genereRequeteUpdate($fields[$i]["TABLE"][$x], $tableEnCours, $nomChamp, $nomColonneIndex);
    								pmb_mysql_query($requete);

    								//pour les tris sur les libellés de marclist
    								if(isset($fields[$i]["TABLE"][$x]["TABLEFIELD"][0]["MARCTYPE"])) {
    									$requete = $this->genereRequeteUpdateFromMarcType($fields[$i]["TABLE"][$x]["TABLEFIELD"][0]["MARCTYPE"], $this->table_tri_tempo, $nomChamp);
    									pmb_mysql_query($requete);
    								}
    							}
							}

							//on a aussi des champs persos maitenant...
							if(isset($fields[$i]['SOURCE']) && $fields[$i]['SOURCE'] == "cp"){
								$requete = $this->generateRequeteCPUpdate($fields[$i], $tableEnCours, $nomChamp);
								pmb_mysql_query($requete);
							}
							break;
						case "authority":
						    //le nom du champ on ajoute tb pour corriger le probleme des noms numeriques
						    $nomChamp = "tb".$fields[$i]["NAME"];
						    //on ajoute la colonne au orderby
						    if($orderby!="") $orderby.=",";
						    //on ajoute la colonne au orderby
						    $orderby .= $this->ajoutOrder($nomChamp,$temp[0]);

						    //on ajoute la colonne à la table temporaire
						    $this->ajoutColonneTableTempo($tableEnCours, $nomChamp, $temp[1]);

						    //on a aussi des champs persos maitenant...
						    if(isset($fields[$i]['SOURCE']) && $fields[$i]['SOURCE'] == "cp"){
						        $requete = $this->generateRequeteCPAuthorityUpdate($fields[$i], $tableEnCours, $nomChamp);
						        pmb_mysql_query($requete);
						    }
						    break;
					} //switch
				} //if ($fields[$i]["ID"] == $temp[2]) {
			} //for ($i = 0; $i < count($fields); $i++) {
		} //for ($j = 0; $j < count($trier_par); $j++) {

		if ($orderby!="") {
			//on va classer la table tempo suivant les criteres donnés
			$requete = "ALTER TABLE " . $tableEnCours ." ORDER BY ". $orderby;
			pmb_mysql_query($requete);
		}

		//on retourne la requete sur la table de tri
		if ($this->table_select!="") {
			//c'est une requete avec des informations extérieures
			$requete = "SELECT " . $nomColonneIndex . "," . $this->champs_select;
			$requete .= " FROM " . $this->table_tri_tempo . "," . $this->table_select;
			$requete .= " WHERE " . $this->table_select . "." . $this->table_primary_key_select;
			$requete .= "=" . $this->table_tri_tempo . "." . $nomColonneIndex;
			$requete .= " GROUP BY " . $nomColonneIndex;
			if ($orderby!="") $requete .= " ORDER BY " . $orderby;
			if ($nbLimit>0) $requete .= " LIMIT " . $debLimit . "," . $nbLimit;
		} else {
			if ($nbLimit>0) {
				//requete de base sur la table triée avec limit
				$requete = "SELECT * FROM " . $tableEnCours;
				if ($orderby!="") $requete .= " ORDER BY " . $orderby;
				$requete .= " LIMIT " . $debLimit . "," . $nbLimit;
			} else {
				//requete de base sur la table triée
				$requete = "SELECT " . $nomColonneIndex . " FROM " . $tableEnCours;
				if ($orderby!="") $requete .= " ORDER BY " . $orderby;
			}
		}
		return $requete;
	}

	public function get_order_by($idTri){
	    $orderby="";
	    $fields = $this->params['FIELD'];
	    $result = $this->dSort->recupTriParId($idTri);
	    $trier_par = explode(",",$result['tri_par']);
	    for ($j = 0; $j < count($trier_par); $j++) {
	        $temp = explode("_", $trier_par[$j]);
	        //on parcours tous les champs de tri possible
	        for ($i = 0; $i < count($fields); $i++) {
	            if ($fields[$i]["ID"] == $temp[2]) {
	                switch ($fields[$i]["TYPEFIELD"]) {
	                    case "internal":
	                        $nomChamp = $fields[$i]["TABLEFIELD"][0]['value'];
	                        if($orderby!="")$orderby .=",";
	                        $orderby .= $this->ajoutOrder($nomChamp,$temp[0]);
	                        break;
	                    case "select":
	                        $nomChamp = "tb".$fields[$i]["NAME"];
	                        if($orderby!="")$orderby .=",";
	                        $orderby .= $this->ajoutOrder($nomChamp,$temp[0]);
	                        break;
	                    case "authority":
	                        $nomChamp = "tb".$fields[$i]["NAME"];
	                        if($orderby!="")$orderby .=",";
	                        $orderby .= $this->ajoutOrder($nomChamp,$temp[0]);
	                        break;
	                } //switch
	            }
	        }
	    }
	    return $orderby;
	}

	public function delete_useless($nomCol,$orderby,$need){
	    $query = "select ".$nomCol." as crit,count(*) as nb_elem from ".$this->table_tri_tempo." group by ".preg_replace("/ |desc|asc/i",'',$orderby)." order by $orderby";
	    $res = pmb_mysql_query($query);
	    $keep = array();
	    $nb_elem= 0;
	    if(pmb_mysql_num_rows($res)){
	        while($row = pmb_mysql_fetch_object($res)){
	            $nb_elem += intval($row->nb_elem);
	            $keep[] = addslashes($row->crit);
	            if($nb_elem>$need){
	                $clean = "DELETE FROM ".$this->table_tri_tempo." where $nomCol not in ('".implode("','",$keep)."')";
	                pmb_mysql_query($clean);
	                break;
	            }
	        }
	    }
	}

	/**
	 * Ajoute une colonne à la table temporaire du nom et du type précisé
	 */
	public function ajoutColonneTableTempo($nomTable, $nomCol,$type) {

	    if (!pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM ". $nomTable ." LIKE '". $nomCol ."'"))){
			//d'abord on ajoute la colonne
			$cmd_table = "ALTER TABLE " . $nomTable . " ADD " . $nomCol . " ";

			//en fonction du type on met le type mysql
			switch($type) {
				case "num":
					$cmd_table .= "integer";
					break;
				case "text":
				default:
					$cmd_table .= "varchar(255)";
					break;
			}

			//execution de l'ajout de la colonne
			pmb_mysql_query($cmd_table);
		}
	}

	/**
	 * Renvoi le nom du champ et l'ordre de tri SQL
	 */
	public function ajoutOrder($nomChp,$typeorder) {

		$tmpTxt = $nomChp;

		//suivant le type de tri
		switch ($typeorder) {
			case "c":
				$tmpTxt .= " ASC";
				break;
			case "d":
			default:
				$tmpTxt .= " DESC";
				break;
		}


		return $tmpTxt;
	}

	/**
	 * Genere les liaisons (jointures)
	 */
	protected static function genereRequeteLinks($desTable, $nomTable, $desLink, $params_reference, $params_referencekey) {
		$extractinfo_sql = "";
		if(isset($desLink["TABLE"][0]['ALIAS']) && $desLink["TABLE"][0]['ALIAS']){
			$alias = $desLink["TABLE"][0]['ALIAS'];
		}else{
			$alias = (isset($desLink["TABLE"][0]['value']) ? $desLink["TABLE"][0]['value'] : "");
		}
		if(isset($desLink["TYPE"])) {
			switch ($desLink["TYPE"]) {
				case "n1" :
					if (isset($desLink["TABLEKEY"][0]['value']) && $desLink["TABLEKEY"][0]['value']) {
						$extractinfo_sql .= " LEFT JOIN " . $desLink["TABLE"][0]['value'].($desLink["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
    					if (isset($desLink["EXTERNALTABLE"][0]['value'])) {
    					    $extractinfo_sql .= " ON " . $desLink["EXTERNALTABLE"][0]['value'] . "." . $desLink["EXTERNALFIELD"][0]['value'];
    					} else {
							$extractinfo_sql .= " ON " . $desTable["NAME"] . "." . $desLink["EXTERNALFIELD"][0]['value'];
    					}
						$extractinfo_sql .= "=" . $alias . "." . $desLink["TABLEKEY"][0]['value'];
					} else {
						$extractinfo_sql .= " LEFT JOIN " . $desTable["NAME"];
						$extractinfo_sql .= " ON " . $params_reference . "." . $params_referencekey;
						$extractinfo_sql .= "=" . $desTable["NAME"] . "." . $desLink["EXTERNALFIELD"][0]['value'];
					}
					break;
				case "1n" :
					$extractinfo_sql .= " LEFT JOIN " . $desTable["NAME"];
					$extractinfo_sql .= " ON (" . $desTable["NAME"] . "." . $desTable["TABLEKEY"][0]['value'];
					$extractinfo_sql .= "=" . $params_reference . "." . $desLink["REFERENCEFIELD"][0]['value'] . ") ";
					break;
				case "nn" :
					$extractinfo_sql .= " LEFT JOIN " . $desLink["TABLE"][0]['value'].($desLink["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
					$extractinfo_sql .= " ON (" . $nomTable . "." . $params_referencekey;
					$extractinfo_sql .= "=" . $alias . "." . $desLink["REFERENCEFIELD"][0]['value'] . ") ";

					if (!isset($desLink["LINKRESTRICT"][0]['value'])) {
					    $desLink["LINKRESTRICT"][0]['value'] = "";
					}

					//Autres jointures
					if(isset($desLink["LINK"])) {
						for ($x = 0; $x <= count($desLink["LINK"]); $x++) {
							$extractinfo_sql .= static::genereRequeteLinks($desTable, $desLink["TABLE"][0]['value'], $desLink["LINK"][$x], $desLink["TABLE"][0]['value'], $desLink["EXTERNALFIELD"][0]['value']);
						}
					} else {
						if (isset($desLink["TABLEKEY"][0]['value']) && $desLink["TABLEKEY"][0]['value']) {
							$extractinfo_sql .= " LEFT JOIN " . $desTable["NAME"];
							$extractinfo_sql .= " ON (" . $alias . "." . $desLink["TABLEKEY"][0]['value'];
							$extractinfo_sql .= "=" . $desTable["NAME"] . "." . $desLink["EXTERNALFIELD"][0]['value'] ." ".$desLink["LINKRESTRICT"][0]['value']. ") ";
						} else {
							$extractinfo_sql .= " LEFT JOIN " . $desTable["NAME"];
							$extractinfo_sql .= " ON (" . $alias . "." . $desLink["EXTERNALFIELD"][0]['value'];
							$extractinfo_sql .= "=" . $desTable["NAME"] . "." . $desTable["TABLEKEY"][0]['value'] . " ".$desLink["LINKRESTRICT"][0]['value'].") ";

						}
					}
					break;
			}
		}
		return $extractinfo_sql;
	}

	protected function genereRequeteUpdateFromMarcType($type, $nomTable, $nomChp) {
		$requete = "";
		$marc_list_instance = marc_list_collection::get_instance($type);
		if(is_object($marc_list_instance) && count($marc_list_instance->table)) {
			$sql = "DROP TEMPORARY TABLE IF EXISTS ".$nomTable."_marctype";
			pmb_mysql_query($sql);

			$temporary_sql = "CREATE TEMPORARY TABLE ".$nomTable."_marctype (
				".$nomChp."_code varchar(255) not null default '',
				".$nomChp."_label varchar(255) not null default ''
			) ENGINE=".$this->current_engine;
			pmb_mysql_query($temporary_sql);

			$temporary_insert_sql = "INSERT INTO ".$nomTable."_marctype VALUES ";
			foreach ($marc_list_instance->table as $code=>$value) {
				$temporary_insert_sql .= "('".addslashes($code)."', '".addslashes($value)."'),";
			}
			//Elimination de la dernière virgule
			$temporary_insert_sql = substr($temporary_insert_sql, 0, strlen($temporary_insert_sql)-1);
			pmb_mysql_query($temporary_insert_sql);
			pmb_mysql_query("alter table ".$nomTable."_marctype add index(".$nomChp."_code)");

			//
			//Et on rempli la table tri_tempo avec les éléments de la table temporaire
			//
			$requete = "UPDATE ".$nomTable.", ".$nomTable."_marctype
				SET " . $nomTable.".".$nomChp . " = " . $nomTable."_marctype.".$nomChp."_label
				WHERE " . $nomTable.".".$nomChp."=" . $nomTable."_marctype.".$nomChp."_code";
		}
		return $requete;
	}

	/**
	 * Genere la requete select d'un element table
	 */
	public function genereRequeteUpdate($desTable, $nomTable, $nomChp, $nomColonneTempo) {
		global $lang;

		//SELECT de base pour la récupération des informations
		//
		$extractinfo_sql = "SELECT ".$this->params["REFERENCE"].'.'.$this->params["REFERENCEKEY"].", SUBSTRING(".$this->ajoutIfNull($desTable["TABLEFIELD"][0]).", 1, 255) AS ".$nomChp." FROM ".$nomTable.' LEFT JOIN '.$this->params["REFERENCE"].' ON ('.$this->params["REFERENCE"].'.'.$this->params["REFERENCEKEY"].' = '.$nomTable.'.'.$this->params["REFERENCEKEY"].')';

		//
		//On ajout les éventuelles liaisons
		//
		if(isset($desTable["LINK"])) {
		    $nb_link = count($desTable["LINK"]);
		    for ($x = 0; $x < $nb_link; $x++) {
				$extractinfo_sql .= static::genereRequeteLinks($desTable, $nomTable, $desTable["LINK"][$x], $this->params["REFERENCE"], $this->params["REFERENCEKEY"]);
			}
		}

		//si on a un filtre supplementaire
		if (isset($desTable["FILTER"])) {
			$extractinfo_sql .= " WHERE " . $desTable["FILTER"][0]['value'];
		}

		//On applique la restriction ORDER BY
		//Utilisé pour les types de langues ou d'auteurs, ...
		if (isset($desTable["ORDERBY"])) {
			$extractinfo_sql .= " ORDER BY ".$this->ajoutIfNull($desTable["ORDERBY"][0]);
		}

		//remplacement de motif pour les catégories
		$extractinfo_sql = str_replace('!!lang!!', $lang, $extractinfo_sql);

		//Si l'on a un group by on passe par une sous-requete pour que le groupement soit fait après le tri (Cas des Auteurs : C'est l'auteur principal qui doit être utilisé pour le tri)
		if (isset($desTable["GROUPBY"])) {
			if (isset($desTable["ORDERBY"])) {
				// Si ORDER BY, on passe par une table temporaire car sinon il n'est pas pris en compte par le group by
				$this->gen_temporary_table($nomTable."_groupby", $this->params["REFERENCEKEY"], $nomChp, $extractinfo_sql);

				$extractinfo_sql = "SELECT * FROM ".$nomTable."_groupby";
				$extractinfo_sql .= " GROUP BY ".$desTable["GROUPBY"][0]["value"];
			} else {
				$extractinfo_sql = "SELECT * FROM (".$extractinfo_sql.") AS asubquery";
				$extractinfo_sql .= " GROUP BY ".$desTable["GROUPBY"][0]["value"];
			}
		}
		//
		//On met le tout dans une table temporaire
		//
		$this->gen_temporary_table($nomTable."_update", $this->params["REFERENCEKEY"], $nomChp, $extractinfo_sql);

		//
		//Et on rempli la table tri_tempo avec les éléments de la table temporaire
		//
		$requete = "UPDATE " . $this->params["REFERENCE"].", ".$nomTable.", ".$nomTable."_update";
		$requete .= " SET " . $nomTable.".".$nomChp . " = " . $nomTable."_update.".$nomChp;

		//le lien vers la table de tri temporaire
		$requete .= " WHERE " . $nomTable.".".$this->params["REFERENCEKEY"];
		$requete .= "=" . $nomTable."_update.".$this->params["REFERENCEKEY"];
		$requete .= " AND ".$this->params["REFERENCE"].".".$this->params["REFERENCEKEY"]."=".$nomTable.".".$this->params["REFERENCEKEY"];
		$requete .= " AND ".$nomTable."_update.".$nomChp." IS NOT NULL";
		$requete .= " AND ".$nomTable."_update.".$nomChp." != ''";

		return $requete;
	}


	/**
	 * Ajoute le ifnull si précisé
	 */
	public function ajoutIfNull($tableau, $nomTable = "") {

	    $champ = $tableau['value'];
	    if (!empty($nomTable)) {
	        $champ = $nomTable .".". $tableau['value'];
	    }

		if (isset($tableau["NULLVALUE"])) {
		    $tmpTxt = "IFNULL(". $champ . ",'" . $tableau["NULLVALUE"] . "')";
		} else {
		    $tmpTxt = $champ;
		}

		return $tmpTxt;
	}


	/**
	 * Parse les fichiers XML de parametres
	 * il y a un fichier par type de tris
	 */
	public function parse() {
		global $include_path;
		global $id_authperso;

		if(strpos($this->dSort->sortName,'ontologies')!== false) {
		    return $this->parse_ontology();
		}

		$params_name = $this->dSort->sortName . "_params";
		global ${$params_name};
		$params = ${$params_name};

		if ($params) {
			$this->params = $params;
		} else {
			$nomfichier = $include_path . "/sort/" . $this->dSort->sortName . "/sort.xml";

			if (file_exists($include_path . "/sort/" . $this->dSort->sortName . "/sort_subst.xml")) {
				$nomfichier=$include_path . "/sort/" . $this->dSort->sortName . "/sort_subst.xml";
				$fp = fopen($nomfichier, "r");
			} else if (file_exists($nomfichier)) {
				$fp = fopen($nomfichier, "r");
			}
			if ($fp) {
				//un fichier est ouvert donc on le lit
				$xml = fread($fp, filesize($nomfichier));
				//on le ferme
				fclose($fp);
				//on le parse pour le transformer en tableau
				$params = _parser_text_no_function_($xml, "SORT", $nomfichier);
				//on le stocke dans la classe
				$this->params = $params;
			} else {
				$this->error = true;
				$this->error_message = "Can't open definition file";
			}
		}

		if (empty($this->params['PPERSOPREFIX'])) {
		    return;
		}

		//tri perso
		$p_perso = new parametres_perso($this->params['PPERSOPREFIX']);

		foreach($p_perso->t_fields as $key => $t_field){
			$param=$t_field['OPTIONS'][0];


			$flag = true;
			//cas particulier des authperso, on filtre sur le bon type
			if (!empty($id_authperso)) {
			    $flag = false;
			    if ($id_authperso == $t_field['NUM_TYPE']) {
			        $flag = true;
			    }
			}
			if ($flag) {
    			switch($t_field['TYPE']){
    				case "comment" :
    				case "text":
    					if(isset($param['REPETABLE']) && $param['REPETABLE'][0]['value']){
    						$tablefield = "group_concat(".$p_perso->prefix."_custom_".$t_field['DATATYPE']." separator ' ')";
    						$groupby = "group by ".$this->params["REFERENCEKEY"];
    					}else{
    						$tablefield = $p_perso->prefix."_custom_".$t_field['DATATYPE'];
    						$groupby = "";
    					}
    					$p_tri = array(
    						'SOURCE' => "cp",
    						'TYPEFIELD' => "select",
    						'ID' => "cp".$key,
    						'TYPE' => "text",
    						'NAME' => $t_field['NAME'],
    						'LABEL' => $t_field['TITRE'],
    						'TABLEFIELD' => array('value'=>$tablefield)
    					);
    					if ($this->params['REFERENCE'] == 'authorities') {
    					    $p_tri['REQ_SUITE'] = "left join " . $p_perso->prefix . "_custom_values on " . $this->params["REFERENCE"] . ".num_object = " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_origine and type_object = " . $this->params["TYPEOBJECT"] . " where " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_champ = '" . $key . "' " . $groupby ;
    					} else {
    					    $p_tri['REQ_SUITE'] = "left join " . $p_perso->prefix . "_custom_values on " . $this->params["REFERENCE"] . "." . $this->params["REFERENCEKEY"] . " = " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_origine where " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_champ = '" . $key . "' " . $groupby ;
    					}
    					break;
    				case "list":
    					if($param['MULTIPLE'][0]['value']){
    						$tablefield = "group_concat(".$p_perso->prefix."_custom_list_lib separator ' ')";
    						$groupby = "group by ".$this->params["REFERENCEKEY"];
    					}else{
    						$tablefield = $p_perso->prefix."_custom_list_lib";
    						$groupby = "";
    					}
    					$p_tri = array(
    						'SOURCE' => "cp",
    						'TYPEFIELD' => "select",
    						'ID' => "cp".$key,
    						'TYPE' => "text",
    						'NAME' => $t_field['NAME'],
    						'LABEL' => $t_field['TITRE'],
    						'TABLEFIELD' => array('value'=>$tablefield)
    					);

    					if ($this->params['REFERENCE'] == 'authorities') {
    						$p_tri['REQ_SUITE'] = "left join " . $p_perso->prefix . "_custom_values on " . $this->params["REFERENCE"] . ".num_object = " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_origine and type_object = " . $this->params["TYPEOBJECT"] . "
                                                       left join " . $p_perso->prefix . "_custom_lists on " . $p_perso->prefix . "_custom_" . $t_field['DATATYPE'] . " = " . $p_perso->prefix . "_custom_list_value
                                                       where " . $p_perso->prefix . "_custom_lists." . $p_perso->prefix . "_custom_champ ='$key' and " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_champ ='$key' $groupby";
    					} else {
    						$p_tri['REQ_SUITE'] = "left join " . $p_perso->prefix . "_custom_values on " . $this->params["REFERENCE"] . "." . $this->params["REFERENCEKEY"] . " = " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_origine
    												left join ".$p_perso->prefix."_custom_lists on ".$p_perso->prefix."_custom_".$t_field['DATATYPE']." = ".$p_perso->prefix."_custom_list_value
    												where " . $p_perso->prefix . "_custom_lists." . $p_perso->prefix . "_custom_champ ='$key' and " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_champ ='$key' $groupby";
    					}
    					break;
    				case "date_box" :
    					$p_tri = array(
    						'SOURCE' => "cp",
    						'TYPEFIELD' => "select",
    						'ID' => "cp".$key,
    						'TYPE' => "text",
    						'NAME' => $t_field['NAME'],
    						'LABEL' => $t_field['TITRE'],
    						'TABLEFIELD' => array('value'=>$p_perso->prefix."_custom_".$t_field['DATATYPE'])
    					);

    					if ($this->params['REFERENCE'] == 'authorities') {
    						$p_tri['REQ_SUITE'] = "left join " . $p_perso->prefix . "_custom_values on " . $this->params["REFERENCE"] . ".num_object = " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_origine where " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_champ = '$key' and type_object = " . $this->params["TYPEOBJECT"];
    					} else {
    						$p_tri['REQ_SUITE'] = "left join " . $p_perso->prefix . "_custom_values on " . $this->params["REFERENCE"] . "." . $this->params["REFERENCEKEY"] . " = " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_origine where " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_champ = '$key'";
    					}
    					break;
    				case "query_list" :
    					$tableid = "";
    					$tablefield = "";
    					$tablename = "";
    					if($param['MULTIPLE'][0]['value']){
    						if($param['QUERY'][0]['value']){
    							$res = pmb_mysql_query($param['QUERY'][0]['value']);
    							if ($res) {
    								$tableid = pmb_mysql_field_name($res,0);
    								$tablefield = "group_concat(".pmb_mysql_field_name($res,1)." separator ' ')";
    								$tablename = pmb_mysql_field_table($res,0);
    							}
    						}
    						$groupby = "group by ".$this->params["REFERENCEKEY"];
    					} else {
    						if($param['QUERY'][0]['value']){
    							$res = pmb_mysql_query($param['QUERY'][0]['value']);
    							if ($res) {
    								$tableid = pmb_mysql_field_name($res,0);
    								$tablefield = pmb_mysql_field_name($res,1);
    								$tablename = pmb_mysql_field_table($res,0);
    							}
    						}
    						$groupby = "";
    					}

    					$p_tri = array(
    						'SOURCE' => "cp",
    						'TYPEFIELD' => "select",
    						'ID' => "cp".$key,
    						'TYPE' => "text",
    						'NAME' => $t_field['NAME'],
    						'LABEL' => $t_field['TITRE'],
    						'TABLEFIELD' => array('value'=>$tablefield)
    					);
    					if ($this->params['REFERENCE'] == 'authorities') {
    						$p_tri['REQ_SUITE'] = "left join " . $p_perso->prefix . "_custom_values on " . $this->params["REFERENCE"] . ".num_object = " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_origine and type_object = " . $this->params["TYPEOBJECT"] . "
    												left join $tablename on " . $p_perso->prefix . "_custom_" . $t_field['DATATYPE'] . " = $tableid
    												where " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_champ ='$key' $groupby";
    					} else {
    						$p_tri['REQ_SUITE'] = "left join " . $p_perso->prefix . "_custom_values on " . $this->params["REFERENCE"] . "." . $this->params["REFERENCEKEY"] . " = " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_origine
    												left join $tablename on " . $p_perso->prefix . "_custom_" . $t_field['DATATYPE'] . " = $tableid
    												where " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_champ ='$key' $groupby";
    					}

    					break;
    				case "query_auth" :
    				    $p_tri = array(
    				    	'SOURCE' => "cp",
    				    	'TYPEFIELD' => "authority",
    				    	'ID' => "cp".$key,
    				    	'TYPE' => "text",
    				    	'NAME' => $t_field['NAME'],
    				    	'LABEL' => $t_field['TITRE'],
    						'PREFIX' => $p_perso->prefix,
    						'T_FIELD' => $t_field
    				    );
    					if ($this->params['REFERENCE'] == 'authorities') {
    						$p_tri['REQ_SUITE'] = "left join " . $p_perso->prefix . "_custom_values on authorities.num_object = " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_origine
    												where " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_champ ='" . $key . "' ";
    					} else {
    						$p_tri['REQ_SUITE'] = "left join " . $p_perso->prefix . "_custom_values on notices.notice_id = " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_origine
    												where " . $p_perso->prefix . "_custom_values." . $p_perso->prefix . "_custom_champ ='$key' ";
    					}
    				    break;
    				default :
    					$p_tri =array();
    					break;
    			}
                if($p_tri)$this->params['FIELD'][]=$p_tri;
			}
		}
	}

	public function generateRequeteCPUpdate($field, $nomTable, $nomChp){
		$requete = "
			SELECT
				".$this->params['REFERENCE'].'.'.$this->params['REFERENCEKEY'].",
				SUBSTRING(".$this->ajoutIfNull($field['TABLEFIELD']).", 1, 255) AS ".$nomChp."
			FROM ".$nomTable." LEFT JOIN ".$this->params['REFERENCE']." ON (".$this->params['REFERENCE'].".".$this->params['REFERENCEKEY']." = ".$nomTable.".".$this->params['REFERENCEKEY'].")
				".$field['REQ_SUITE'];

		//On met le tout dans une table temporaire
		$this->gen_temporary_table($nomTable."_update", $this->params["REFERENCEKEY"], $nomChp, $requete);

		//
		//Et on rempli la table tri_tempo avec les éléments de la table temporaire
		//
		$requete = "UPDATE ".$nomTable.", ".$nomTable."_update";
		$requete .= " SET " . $nomTable.".".$nomChp . " = " . $nomTable."_update.".$nomChp;

		//le lien vers la table de tri temporaire
		$requete .= " WHERE " . $nomTable.".".$this->params["REFERENCEKEY"];
		$requete .= "=" . $nomTable."_update.".$this->params["REFERENCEKEY"];
		$requete .= " AND ".$nomTable."_update.".$nomChp." IS NOT NULL";
		$requete .= " AND ".$nomTable."_update.".$nomChp." != ''";

		return $requete;
	}

	public function generateRequeteCPAuthorityUpdate($field, $nomTable, $nomChp){
	    $datatype = !empty($field['T_FIELD']['DATATYPE']) ? $field['T_FIELD']['DATATYPE'] : $field['T_FIELD']['datatype'];
	    $requete = "
			SELECT
				".$this->params['REFERENCE'].'.'.$this->params['REFERENCEKEY']." AS tbCPId,
				".$field['PREFIX']."_custom_".$datatype." AS tbCPAuthority
			FROM ".$nomTable." LEFT JOIN ".$this->params['REFERENCE']." ON (".$this->params['REFERENCE'].".".$this->params['REFERENCEKEY']." = ".$nomTable.".".$this->params['REFERENCEKEY'].")
				".$field['REQ_SUITE'];
	    $result = pmb_mysql_query($requete);
	    $objects_ids = array();
	    if(pmb_mysql_num_rows($result)) {
	        while ($row = pmb_mysql_fetch_object($result)) {
	            $objects_ids[$row->tbCPId] = get_authority_isbd_from_field($field['T_FIELD'], $row->tbCPAuthority);
	        }
	    }

	    //On met le tout dans une table temporaire
	    $this->gen_temporary_table($nomTable."_update", $this->params['REFERENCEKEY'], $nomChp);

	    foreach ($objects_ids as $object_id=>$authority_value) {
	        $query = "INSERT INTO ".$nomTable."_update
                SET ".$this->params['REFERENCEKEY']." = ".$object_id.",
                ".$nomChp . " = '" .addslashes(pmb_substr($authority_value, 0, 255))."'";
	        pmb_mysql_query($query);
	    }

	    //
	    //Et on rempli la table tri_tempo avec les éléments de la table temporaire
	    //
	    $requete = "UPDATE ".$nomTable.", ".$nomTable."_update";
	    $requete .= " SET " . $nomTable.".".$nomChp . " = " . $nomTable."_update.".$nomChp;

	    //le lien vers la table de tri temporaire
	    $requete .= " WHERE " . $nomTable.".".$this->params["REFERENCEKEY"];
	    $requete .= "=" . $nomTable."_update.".$this->params["REFERENCEKEY"];
	    $requete .= " AND ".$nomTable."_update.".$nomChp." IS NOT NULL";
	    $requete .= " AND ".$nomTable."_update.".$nomChp." != ''";

	    return $requete;
	}

	public static function get_default_sort($sort_name) {
	    switch ($sort_name) {
	        case "external" :
	            return "d_num_11,d_text_2,c_text_1";
	        case "notices" :
				global $pmb_default_sort;
				if (!empty($pmb_default_sort)) {
					return $pmb_default_sort;
				}
	            return "d_num_6,d_text_19,c_text_1";
	        default :
				//parametres globaux a surcharger dans ./includes/parameters_subst/sort_parameters_subst.xml
				return self::get_global_default_sort($sort_name);
	    }
	}

	private static function get_global_default_sort($sort_name) {
		global ${"pmb_default_sort_" . $sort_name};
		if (!empty(${"pmb_default_sort_" . $sort_name})) {
			return ${"pmb_default_sort_" . $sort_name};
	    }
		return "";
	}

	private function parse_ontology() {

	    $params_name = $this->dSort->sortName . "_params";
	    global ${$params_name};
	    $params = ${$params_name};
	    if ($params) {
	        $this->params = $params;
	    }
	    $this->params['REFERENCEKEY'] = 'id_item';
	    $class_uri = onto_common_uri::get_uri(str_replace("ontologies","",$this->dSort->sortName));
	    $ontology = new ontology(ontologies::get_ontology_id_from_class_uri($class_uri));

	    $onto = $ontology->get_onto();
	    foreach ($onto->get_classes() as $c) {
	        if($class_uri != $c->uri){
	            continue;
	        }
	        $class = $onto->get_class($c->uri);
	        foreach ($class->get_properties() as $uri_property) {
	            $property = $class->get_property($uri_property);
	            switch ($property->pmb_datatype) {
	                case "http://www.pmbservices.fr/ontology#integer";
	                    $type = "num";
    	                break;
	                default :
                        $type = "text";
	                    break;
	            }
	            $p_tri = array(
	                'SOURCE' => 'onto',
	                'TYPEFIELD' => $property->pmb_datatype,
	                'ID' => onto_common_uri::get_id($property->uri),
	                'TYPE' => $type,
	                'NAME' => $property->pmb_name,
	                'LABEL' => $property->get_label()
	            );
	            if (!empty($p_tri)) {
	                $this->params['FIELD'][] = $p_tri;
	            }
	        }
	    }
	}

	public static function get_display_tris_selector($nbr_lignes, $sort_name="notices") {
		// Report OPAC -> Gestion
		return '';
	}
}
