<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sort.class.php,v 1.88 2019-05-29 11:43:04 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/misc.inc.php');
require_once ($include_path.'/parser.inc.php');
require_once ($include_path.'/templates/sort.tpl.php');
require_once ($class_path.'/parametres_perso.class.php');
require_once($class_path."/translation.class.php");

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
	
	public function __construct($sName,$tData) {
		$this->sortName = $sName;
		$this->typeData = $tData;
	}
	
	/**
	 * Retourne un tableau avec le nom du tri et la construction du tri
	 */
	public function recupTriParId($id) {
	    // tri par défaut...
	    $tab = array();
		if($id == "default"){
			//Plusieurs tris par défaut définis dans les paramètres ? On va cherche le tout premier
			$default_sort = sort::get_default_sort($this->sortName);
			if (strstr($default_sort,'|')) {
				$tmpArray = explode("||",$default_sort);
				$tmpSort=explode("|",$tmpArray[0]);
				$tab["nom_tri"] = $tmpSort[1];
				$tab["tri_par"] = $tmpSort[0];
			} else {
				$tab["nom_tri"] = "";
				$tab["tri_par"] = $default_sort!= "" ? $default_sort : "d_num_6,c_text_1";
			}
			return $tab;
		}
		switch($this->typeData) {
			case 'base':
				$result = pmb_mysql_query("SELECT nom_tri, tri_par FROM tris WHERE id_tri=" . $id);
				if ($result) {
					$tab = pmb_mysql_fetch_assoc($result);
					pmb_mysql_free_result($result);
					return $tab;
				}
				return null;
			case 'session':
				$tab["nom_tri"] = "";
				if (!empty($_SESSION["sortname".$this->sortName.$id])) {
					$tab["nom_tri"] = $_SESSION["sortname".$this->sortName.$id];
				}
				if (!empty($_SESSION["sort".$this->sortName.$id])) {
				    $tab["tri_par"] = $_SESSION["sort".$this->sortName.$id];
				}
				return $tab;
		}
	}
	
	
	/**
	 * Pour initialiser un parcours des tris
	 * Retourne le nombre de tris
	 */
	public function initParcoursTris($objSort) {
		//on initialise la position du parcours
		$this->posParcours = 0;
		$this->nbResult=0;
		$this->tabParcours=null;
		
		switch($this->typeData) {
			
			case 'base':
				$result = pmb_mysql_query("SELECT id_tri, nom_tri, tri_par FROM tris WHERE tri_reference='" . $this->sortName . "' ORDER BY nom_tri;");
				//echo "SELECT id_tri, nom_tri, tri_par FROM tris WHERE tri_reference='" . $this->sortName . "' ORDER BY nom_tri<br />";
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
					
					return $this->nbResult;
				}
				$_SESSION["tri"] = "";
				return 0;
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
						if (!isset($_SESSION["sort".$this->sortName.$i])) {
							$_SESSION["sort".$this->sortName.$i] = "";
						}
						$this->tabParcours[$i]["nom_tri"] = $objSort->descriptionTri($_SESSION["sort".$this->sortName.$i]);
						if(isset($_SESSION["sortname".$this->sortName.$i]) && $_SESSION["sortname".$this->sortName.$i]){
							$this->tabParcours[$i]["nom_tri"] = $_SESSION["sortname".$this->sortName.$i];
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
		
		switch($this->typeData) {
			case 'base':
				$txt_requete = "";
				
				//modif ou insertion ?
				if ($id != "") {
					//on modifie le tri avec les nouveaux criteres
					$txt_requete = "UPDATE tris SET nom_tri='" . addslashes($nomTri) . "', tri_par='" . $desTri . "' ";
					$txt_requete .= "WHERE id_tri='" . $id . "'";
				} else {
					//on vérifie que le nom de tri n'existe pas
					$txt_requete = "SELECT id_tri FROM tris WHERE nom_tri='" . addslashes($nomTri) . "'";
					$txt_requete .= " AND tri_reference='" . $this->sortName . "'";
					if (pmb_mysql_num_rows(pmb_mysql_query($txt_requete)) == 0) {
						//on genere la requete d'insertion
						$txt_requete = "INSERT INTO tris (id_tri, tri_reference, nom_tri, tri_par) ";
						$txt_requete .= "VALUES ('','" . $this->sortName . "','" . addslashes($nomTri) . "','" . $desTri . "')";
					} else {
						//le nom existe : on le dit
						return "<script>alert ('" . $msg['tri_existant'] . "');history.back();</script>";
					}
				}
				
				if ($txt_requete!="") {
					
					//execution de la requete de modif ou d'insertion
					$requete = pmb_mysql_query($txt_requete);
					
					if (!$requete) {
						// il y a eu une erreur d'execution de la requete
						return "Erreur mysql : " . $txt_requete . "<br />" . pmb_mysql_error();
					}
				}
				return "";
			case 'session':
				//si nombre de tris enregistrés dans la session n'est pas null, parcours des variables de session pour l'existence tri et sauvegarde
				if (!isset($_SESSION["nb_sort".$this->sortName])) {
					$_SESSION["nb_sort".$this->sortName] = 0;
				}
				if ($_SESSION["nb_sort".$this->sortName]<=0) {
					$_SESSION["sort".$this->sortName.$_SESSION["nb_sort".$this->sortName]]=htmlentities($desTri,ENT_QUOTES,$charset);
					if ($nomTri) {
						$_SESSION["sortname".$this->sortName.$_SESSION["nb_sort".$this->sortName]]=htmlentities($nomTri,ENT_QUOTES,$charset);
					}
					$_SESSION["nb_sort".$this->sortName]++;
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
						if ($nomTri) {
							$_SESSION["sortname".$this->sortName.$_SESSION["nb_sort".$this->sortName]] = htmlentities($nomTri,ENT_QUOTES,$charset);
						}
						$_SESSION["nb_sort".$this->sortName]++;
					}
				}
				break;
				
		}
	}
	
	/**
	 * Supprime un tri
	 */
	public function supprimeTri($sort_ids=array()) {
		switch($this->typeData) {
			case 'base':
				$q = 'delete from tris where id_tri in('.implode(',',$sort_ids).') ';
				@pmb_mysql_query($q);
				break;
			case 'session':
				$nb_sort = $_SESSION['nb_sort'.$this->sortName];
				$last_sort = $_SESSION['sort'.$this->sortName.$_SESSION['last_sort'.$this->sortName]];
				
				//stockage des tris a conserver dans un tableau et suppression des variables session
				$tab_sort = array();
				$j=0;
				for($i=0; $i<$nb_sort; $i++) {
					if (!in_array($i,$sort_ids)) {
						//ce n'est pas un tri a supprimer
						//on le stocke dans le tableau
						$tab_sort[$j]['descTri'] = $_SESSION["sort".$this->sortName.$i];
						$tab_sort[$j]['nomTri'] = $_SESSION["sortname".$this->sortName.$i];
						$j++;
					}
					unset($_SESSION['sort'.$this->sortName.$i]);
					unset($_SESSION['sortname'.$this->sortName.$i]);
				}
				
				//reaffectation des variables session
				$_SESSION['last_sort'.$this->sortName]="";
				foreach($tab_sort as $k=>$v) {
					$_SESSION['sort'.$this->sortName.$k]=$v['descTri'];
					$_SESSION['sortname'.$this->sortName.$k]=$v['nomTri'];
					if ($last_sort==$v) {
						$_SESSION['last_sort'.$this->sortName]=$k;
					}
				}
				$_SESSION['nb_sort'.$this->sortName]=count($tab_sort);
				break;
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
	
	
	/**
	 * Applique le tri donné
	 * @$sort_name nom du tri à appliquer
	 */
	public function __construct($sort_name, $accesTri) {
		if ($sort_name) {
			$sname = $sort_name;
		} else {
			$sname = 'notices';
		}
		$this->table_tri_tempo .= "_".self::$nb_instance;
		self::$nb_instance++;
		
		if ($accesTri) {
			$this->dSort = new dataSort($sname,$accesTri);
		} else {
			$this->dSort = new dataSort($sname,'base');
		}
		//on charge le fichier XML
		$this->parse();
		//on ajoute les tris par défaut ajoutés en paramètres
		$this->add_default_sort();
	}
	
	/**
	 * Ajoute les tris par défaut éventuellement saisis en paramètre
	 */
	public function add_default_sort(){
		$sortArray = explode(" ", self::get_default_sort_list($this->dSort->sortName),2);
		if ($sortArray[1] != "") {
			//on vérifie l'existence d'un flag : que la recherche par défaut ne revienne pas si l'utilisateur l'a supprimée par le formulaire
			if(!isset($_SESSION['sort'.$this->dSort->sortName.'flag'])){
				$tmpArray = explode("||",$sortArray[1]);
				foreach($tmpArray as $tmpElement){
					if(trim($tmpElement)){
						if (strstr($tmpElement,'|')) {
							$tmpSort=explode("|",$tmpElement);
							$this->dSort->enregistreTri('',$tmpSort[1],$tmpSort[0]);
						} else {
							$this->dSort->enregistreTri('','',$tmpElement);
						}
					}
				}
				$_SESSION['sort'.$this->dSort->sortName.'flag']=1;
			}
		}
	}
	
	
	/**
	 * Affiche l'écran de choix des tris enregistrés
	 */
	public function show_tris_form() {
		global $show_tris_form;
		global $ligne_tableau_tris;
		global $msg;
		
		if ($this->dSort->initParcoursTris($this) == 0 ) {
			//il n'y a pas de tris enregistrés
			
			//on renvoie un message pour le dire
			$tris = $msg['aucun_tri'];
			
		} else {
			// creation du tableau de la liste des tris enregistrés
			$parity = 1;
			$tris = "";
			
			//affichage des enregistrements de tris possibles
			while ($result = $this->dSort->parcoursTriSuivant()) {
				//gestion du surlignage une ligne sur 2
				if ($parity % 2)
					$pair_impair = "even";
					else
						$pair_impair = "odd";
						
						//html d'une ligne
						$tristemp = str_replace("!!id_tri!!", $result['id_tri'], $ligne_tableau_tris);
						$tristemp = str_replace("!!nom_tri!!", $result['nom_tri'], $tristemp);
						$tristemp = str_replace("!!pair_impair!!", $pair_impair, $tristemp);
						$tris .= $tristemp;
						
						$parity += 1;
			}
		}
		
		//on remplace dans le template les informations issues de la base
		$tris_form = str_replace("!!sortname!!", $this->dSort->sortName, $show_tris_form);
		$tris_form = str_replace("!!liste_tris!!", $tris, $tris_form);
		return $tris_form;
	}
	
	/**
	 * affiche un selecteur des tris disponibles
	 */
	static function show_tris_selector($sort_name = "notices") {
		global $msg, $lvl;
		
		$sortArray = explode(" ", self::get_default_sort_list($sort_name));
		//Mode Ajax
		if ($sortArray[0] == 0) {
			$tris_selector = "<span class=\"espaceResultSearch\">&nbsp;</span><script type='text/javascript' src='./includes/javascript/select.js'></script>
						<script>
							var ajax_get_sort=new http_request();
					
							function get_sort_content(del_sort, ids) {
								var url = './ajax.php?module=ajax&categ=sort&sub=get_sort&raz_sort='+(typeof(del_sort) != 'undefined' ? del_sort : '')+'&suppr_ids='+(typeof(ids) != 'undefined' ? ids : '')+'&page_en_cours=!!page_en_cours!!';
								ajax_get_sort.request(url,0,'',1,show_sort_content,0,0);
							}
					
							function show_sort_content(response) {
								document.getElementById('frame_notice_preview').innerHTML=ajax_get_sort.get_text();
								var tags = document.getElementById('frame_notice_preview').getElementsByTagName('script');
					       		for(var i=0;i<tags.length;i++){
									window.eval(tags[i].text);
					        	}
							}
							function kill_sort_frame() {
								var sort_view=document.getElementById('frame_notice_preview');
								if (sort_view)
									sort_view.parentNode.removeChild(sort_view);
							}
						</script>
						<span class=\"triSelector\"><a onClick='show_layer(); get_sort_content();' title=\"".$msg['tris_dispos']."\" style='cursor : pointer;'><img src='".get_url_icon('orderby_az.gif')."' alt=\"".$msg['tris_dispos']."\" class='align_bottom' hspace='3' style='border:0px' id='sort_icon'></a></span>";
		} elseif ($sortArray[0] == 1) {
			global $sort;
			
			if(!isset($sort)){
				$sort = (isset($_SESSION["last_sort$sort_name"]) ? $_SESSION["last_sort$sort_name"] : "");
			}
			if ($sort !== "") $sel_sort = $sort;
			else $sel_sort = -1;
			// creation du tableau de la liste des tris enregistrés
			$tris_selector = "<span class=\"espaceResultSearch\">&nbsp;</span><span class=\"triSelector\"><select name='tri_selector' id='tri_selector' onChange='applySort(this.options[this.selectedIndex].value)'>";
			//affichage des enregistrements de tris possibles
			$sort = new sort($sort_name, 'session');
			$sort->dSort->initParcoursTris($sort);
			switch ($lvl) {
				case 'author_see' :
				case 'authperso_see' :
				case 'categ_see' :
				case 'coll_see' :
				case 'congres_see' :
				case 'indexint_see' :
				case 'publisher_see' :
				case 'serie_see' :
				case 'subcoll_see' :
				case 'titre_uniforme_see' :
					$tris_selector .= "<option value=''>".$msg['show_tris_selector_no_sort']."</option>";
					break;
			}
			$tris_selector .= "<option value='default'".(($sel_sort == "default") ? " selected" : "").">".$sort->descriptionTriParId("default",false, false)."</option>";
			while ($result = $sort->dSort->parcoursTriSuivant()) {
				$tris_selector .= "<option value='".$result['id_tri']."' ".(($sel_sort == (string)$result['id_tri']) ? " selected" : "").">";
				$tris_selector .= $result['nom_tri'];
				$tris_selector .= "</option>";
			}
			$tris_selector .= "<option value='custom'>".$msg['tris_dispos']."</option>";
			$tris_selector .= "</select></span>
			<script>
			function applySort(value){
				if (value=='custom') {
					maPage='index.php?lvl=sort&page_en_cours=!!page_en_cours!!';
					
				} else {
					maPage='index.php?!!page_en_cours1!!&get_last_query=1&sort='+value;
				}
				document.location = maPage;
			}
			</script><span class=\"espaceResultSearch\">&nbsp;</span>";
		} else {
			$tris_selector = "<span class=\"espaceResultSearch\">&nbsp;</span><span class=\"triSelector\"><a href='index.php?lvl=sort&page_en_cours=!!page_en_cours!!' title=\"".$msg['tris_dispos']."\"><img src='".get_url_icon('orderby_az.gif')."' alt=\"".$msg['tris_dispos']."\" class='align_bottom' hspace='3' style='border:0px' id='sort_icon'></a></span>";
		}
		
		return $tris_selector;
	}
	
	/**
	 * retour la requête avec le tri à appliquer
	 */
	public static function get_sort_query($query='', $nbr_lignes=0, $debut=0, $sort_name="notices", $nom_colonne_index="notice_id", $nb_limit=20) {
		global $opac_nb_max_tri;
		if (isset($_GET["sort"])) {
			$_SESSION["last_sort$sort_name"] = $_GET["sort"];
		}
		if ($nbr_lignes>$opac_nb_max_tri) {
			$_SESSION["last_sort$sort_name"] = "";
		}
		$sort = new sort($sort_name, 'session');
		if (isset($_SESSION["last_sort$sort_name"])) {
			$query = $sort->appliquer_tri($_SESSION["last_sort$sort_name"], $query, $nom_colonne_index, $debut, $nb_limit);
		} else {
			$query = $sort->appliquer_tri("default", $query, $nom_colonne_index, $debut, $nb_limit);
		}
		return $query;
	}
	
	public static function get_sort_etagere_query($query='', $nbr_lignes=0, $debut=0, $sort_name="notices") {
		global $opac_etagere_notices_order;
		global $opac_nb_aut_rec_per_page;
		
		if (isset($_SESSION["last_sort$sort_name"])) {
			$sort = new sort($sort_name, 'session');
			$query = $sort->appliquer_tri($_SESSION["last_sort$sort_name"], $query, "notice_id", $debut, $opac_nb_aut_rec_per_page);
		} else {
			$query .= "order by ".$opac_etagere_notices_order." LIMIT $debut,$opac_nb_aut_rec_per_page ";
		}
		return $query;
	}
	
	/**
	 * affiche le tri dans la liste de résultats
	 */
	public static function show_tris_in_result_list($nbr_lignes, $sort_name="notices") {
		global $msg;
		global $opac_nb_max_tri;
		global $opac_default_sort_display;
		
		//fin gestion du tri
		$result_list = '';
		$affich_tris_result_liste = self::show_tris_selector($sort_name);
		if ($nbr_lignes<=$opac_nb_max_tri) {
			$pos=strpos($_SERVER['REQUEST_URI'],"?");
			$pos1=strpos($_SERVER['REQUEST_URI'],"get");
			if ($pos1==0) $pos1=strlen($_SERVER['REQUEST_URI']);
			else $pos1=$pos1-3;
			$para=urlencode(substr($_SERVER['REQUEST_URI'],$pos+1,$pos1-$pos+1));
			$para1=substr($_SERVER['REQUEST_URI'],$pos+1,$pos1-$pos+1);
			$affich_tris_result_liste=str_replace("!!page_en_cours!!",$para,$affich_tris_result_liste);
			$affich_tris_result_liste=str_replace("!!page_en_cours1!!",$para1,$affich_tris_result_liste);
			$end_html = '';
			if(isset($_SESSION["last_sort$sort_name"]) || $opac_default_sort_display) { //Encapsulation des éléments de tri dans un container pour faciliter le style
				$result_list.= '<span class="triContainer">';
				$end_html = '</span>';
			}
			$result_list.=  $affich_tris_result_liste;
			$sort = new sort($sort_name, 'session');
			if (isset($_SESSION["last_sort$sort_name"])) {
				$result_list.=  "<span class='sort'>".$msg['tri_par']." ".$sort->descriptionTriParId($_SESSION["last_sort$sort_name"])."<span class=\"espaceResultSearch\">&nbsp;</span></span>";
			} elseif ($opac_default_sort_display) {
				$result_list.= "<span class='sort'>".$msg['tri_par']." ".$sort->descriptionTriParId("default")."<span class=\"espaceResultSearch\">&nbsp;</span></span>";
			}
			$result_list.= $end_html;
		} else {
			$result_list.=  "<span class=\"espaceResultSearch\">&nbsp;</span>";
		}
		return $result_list;
	}
	
	/**
	 *
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
	
	/**
	 * Affiche l'écran de sélection des criteres de tri
	 */
	public function show_sel_formAdmin($id_tri) {
		global $show_sel_form;
		global $charset;
		global $msg;
		
		//les champs de tris possible
		$fields = $this->params["FIELD"];
		
		//initialisation des variables
		$liste_selectionnes = "";
		$nom_du_tri = "";
		
		//génération de la liste des criteres
		$liste_criteres = "";
		
		//si id_tri est renseigné, c'est alors une modification du tri sélectionné
		if ($id_tri!=0) {
			$result = $this->dSort->recupTriParId($id_tri);
			//$requete = pmb_mysql_query("SELECT nom_tri, tri_par FROM tris WHERE id_tri='" . $id_tri . "'");
			if ($result) {
				//$result = pmb_mysql_fetch_array($requete);
				$nom_du_tri = $result['nom_tri'];
				
				//recherche et décomposition du tri
				$tri_par = explode(",", $result['tri_par']);
				
				for ($i = 0; $i < count($tri_par); $i++) {
					
					//on decompose la description du critere de tri (c_num_2)
					$tri_par1 = explode("_", $tri_par[$i]);
					
					for ($j = 0; $j < count($fields); $j++) {
						//on parcours tous les champs (pour récuperer le nom)
						if ($fields[$j]["ID"] == $tri_par1[2]) {
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
							if(isset($fields[$j]['SOURCE']) && $fields[$j]['SOURCE'] == "cp") $name = $fields[$j]['LABEL'];
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
					if ($fields[$j]["UTILISE"]!=true){
						if ($this->visibility($fields[$j])) {
							//si champ perso, on a déjà le libellé
							if(isset($fields[$j]['SOURCE']) && $fields[$j]['SOURCE'] == "cp") $name = $fields[$j]['LABEL'];
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
					if(isset($fields[$j]['SOURCE']) && $fields[$j]['SOURCE'] == "cp") $name = $fields[$j]['LABEL'];
					else $name = $msg[$fields[$j]['NAME']];
					$liste_criteres .= "<option value='c_" . $fields[$j]["TYPE"] . "_" . $fields[$j]["ID"] . "'>" . htmlentities($name, ENT_QUOTES, $charset) . "</option>\n";
				}
			}
		}
		
		//on remplace toutes les variables dans le template
		$sel_form = str_replace("!!id_tri!!", $id_tri, $show_sel_form);
		$sel_form = str_replace("!!sortname!!", $this->dSort->sortName, $sel_form);
		$sel_form = str_replace("!!nom_tri!!", $nom_du_tri, $sel_form);
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
		
		$liste_criteres = '';
		for ($i=0;$i<count($fields);$i++) {
			if ($this->visibility($fields[$i])) {
				//si champ perso, on a déjà le libellé
				if(isset($fields[$i]['SOURCE']) && $fields[$i]['SOURCE'] == "cp") $name = $fields[$i]['LABEL'];
				else $name = $msg[$fields[$i]['NAME']];
				$liste_criteres.="<option value='".$fields[$i]["ID"]."' data-type='".$fields[$i]["TYPE"]."'>".htmlentities($name,ENT_QUOTES,$charset)."</option>\n";
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
		global $msg;
		global $charset;
		
		$criteres = implode(",",$tris_par);
		
		return $this->dSort->enregistreTri($id_tri,$nom_tri,$criteres);
		
	}
	
	/**
	 * Supprime un tri sauvegarder
	 */
	public function supprimer($sort_ids=array()) {
		$this->dSort->supprimeTri($sort_ids);
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
			for ($i = 0; $i < count($fields); $i++) {
				if (isset($temp[2]) && $fields[$i]["ID"] == $temp[2]) {
					if(isset($fields[$i]['SOURCE']) && $fields[$i]['SOURCE'] == "cp"){
						$trier_par_texte .= $fields[$i]['LABEL'] . " ";
					}else{
						$trier_par_texte .= $msg[$fields[$i]["NAME"]] . " ";
					}
					if ($temp[0] == "c") {
						$trier_par_texte .= $msg["tri_texte_croissant"];
					} else {
						$trier_par_texte .= $msg["tri_texte_decroissant"];
					}
					$trier_par_texte .= ", ";
				}
			}
		}
		//on enleve la derniere virgule et on ajoute la )
		$trier_par_texte = substr($trier_par_texte, 0, strlen($trier_par_texte) - 2);
		
		return $trier_par_texte;
	}
	
	
	/**
	 * Retourne le texte de description du tri a partir d'un id
	 */
	public function descriptionTriParId($id_tri,$affiche_description = true, $whith_html = true) {
		if ($id_tri!="") {
			//récupération de la description du tri
			$result = $this->dSort->recupTriParId($id_tri);
			$nom_tri = $result['nom_tri'];
			if ($affiche_description || !$nom_tri) {
				//on concatene le message complet
				$trier_par_texte = "(" . $this->descriptionTri($result['tri_par']) . ")";
				if(!$whith_html) {
					return $nom_tri.' '.$trier_par_texte;
				} else {
					return "<span class=\"triDescription\"><span class=\"triLabel\">".$nom_tri."</span> <span class=\"triDetail\">".$trier_par_texte."</span></span>";
				}
			} else {
				if(!$whith_html) {
					return $nom_tri;
				} else {
					return "<span class=\"triDescription\"><span class=\"triLabel\">".$nom_tri."</span> <span class=\"triDetail\"></span></span>";
				}
			}
		} else {
		    return "";
		}
	}
	
	/**
	 * Applique le tri sélectionner
	 * Renvoi la requete finale utilisant les criteres de tri
	 */
	public function appliquer_tri($idTri, $selectTempo, $nomColonneIndex,$debLimit,$nbLimit) {
		global $msg;
		
		//récuperations des champs
		$fields = $this->params["FIELD"];
		$tableEnCours = $this->table_tri_tempo;
		
		//creation de la table de tri
		//$cmd_table = "DROP TABLE " . $tableEnCours;
		//pmb_mysql_query($cmd_table);
		//$cmd_table = "CREATE TABLE " . $tableEnCours . " ENGINE=MyISAM (".$selectTempo.")";
		$cmd_table = "CREATE TEMPORARY TABLE " . $tableEnCours . " ENGINE=MyISAM (".$selectTempo.")";
		pmb_mysql_query($cmd_table);
		$cmd_table = "ALTER TABLE " . $tableEnCours . " ADD PRIMARY KEY (" . $nomColonneIndex.")";
		pmb_mysql_query($cmd_table);
		
		//récupération de la description du tri
		$result = $this->dSort->recupTriParId($idTri);		
		$trier_par = array();
		if(!empty($result['tri_par'])) {
		  $trier_par = explode(",",$result['tri_par']);
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
							for ($x = 0; $x < count($fields[$i]["TABLE"]); $x++) {
								
								$requete = $this->genereRequeteUpdate($fields[$i]["TABLE"][$x], $tableEnCours, $nomChamp, $nomColonneIndex);
								
								//echo("updateSort:".$requete."<br />");
								pmb_mysql_query($requete);
							}
							
							//on a aussi des champs persos maitenant...
							if(isset($fields[$i]['SOURCE']) && $fields[$i]['SOURCE'] == "cp"){
								$requete = $this->generateRequeteCPUpdate($fields[$i], $tableEnCours, $nomChamp);
								pmb_mysql_query($requete);
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
	
	public function appliquer_tri_from_tmp_table($idTri=0, $table, $nomColonneIndex,$start=0,$numbers=0){
		//récuperations des champs
		$fields = $this->params["FIELD"];
		$this->table_tri_tempo = $table;
		
		//récupération de la description du tri
		$result = $this->dSort->recupTriParId($idTri);
		
		$trier_par = explode(",",$result['tri_par']);
		$do=false;
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
							$requete_fields = pmb_mysql_query("SELECT * FROM " . $this->table_tri_tempo . " LIMIT 1");
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
							$this->ajoutColonneTableTempo($this->table_tri_tempo, $nomChamp, $temp[1]);
							
							//on parcours la ou les tables pour generer les updates
							for ($x = 0; $x < count($fields[$i]["TABLE"]); $x++) {
								$requete = $this->genereRequeteUpdate($fields[$i]["TABLE"][$x], $this->table_tri_tempo, $nomChamp, $nomColonneIndex);
								pmb_mysql_query($requete);
							}
							
							//on a aussi des champs persos maitenant...
							if(isset($fields[$i]['SOURCE']) && $fields[$i]['SOURCE'] == "cp"){
								$requete = $this->generateRequeteCPUpdate($fields[$i], $this->table_tri_tempo, $nomChamp);
								pmb_mysql_query($requete);
							}
							break;
							
					} //switch
					if($numbers >0){
						$this->delete_useless($nomChamp, $orderby,($start+$numbers));
					}
					
				} //if ($fields[$i]["ID"] == $temp[2]) {
			} //for ($i = 0; $i < count($fields); $i++) {
		} //for ($j = 0; $j < count($trier_par); $j++) {
		
		//on retourne la requete sur la table de tri
		if ($this->table_select!="") {
			//c'est une requete avec des informations extérieures
			$requete = "SELECT " . $nomColonneIndex . "," . $this->champs_select;
			$requete .= " FROM " . $this->table_tri_tempo . "," . $this->table_select;
			$requete .= " WHERE " . $this->table_select . "." . $this->table_primary_key_select;
			$requete .= "=" . $this->table_tri_tempo . "." . $nomColonneIndex;
			$requete .= " GROUP BY " . $nomColonneIndex;
		} else {
			//requete de base sur la table triée
			$requete = "SELECT " . $nomColonneIndex . " FROM " . $this->table_tri_tempo;
		}
		if ($orderby!="") $requete .= " ORDER BY " . $orderby;
		if($numbers>0){
			$requete.=" limit $start,".$numbers;
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
				$nb_elem+=($row->nb_elem);
				$keep[]=$row->crit;
				if($nb_elem>$need){
					$clean = "delete from ".$this->table_tri_tempo." where $nomCol not in ('".implode("','",$keep)."')";
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
		
		//d'abord on ajoute la colonne
		$cmd_table = "ALTER TABLE " . $nomTable . " ADD " . $nomCol . " ";
		
		//en fonction du type on met le type mysql
		switch($type) {
			case "num":
				$cmd_table .= "integer";
				break;
			case "text":
			default:
				$cmd_table .= "text";
				break;
		}
		
		//execution de l'ajout de la colonne
		pmb_mysql_query($cmd_table);
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
						$extractinfo_sql .= " ON " . $desTable["NAME"] . "." . $desLink["EXTERNALFIELD"][0]['value'];
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
	
	/**
	 * Genere la requete select d'un element table
	 */
	public function genereRequeteUpdate($desTable, $nomTable, $nomChp, $nomColonneTempo) {
		
		$tables = $nomTable . "," .$this->params["REFERENCE"];
		$groupby = "";
		
		//
		//SELECT de base pour la récupération des informations
		//
		$extractinfo_sql = "SELECT ".$this->params["REFERENCE"].'.'.$this->params["REFERENCEKEY"].", ".$this->ajoutIfNull($desTable["TABLEFIELD"][0])." AS ".$nomChp." FROM ".$nomTable.' LEFT JOIN '.$this->params["REFERENCE"].' ON ('.$this->params["REFERENCE"].'.'.$this->params["REFERENCEKEY"].' = '.$nomTable.'.'.$this->params["REFERENCEKEY"].')';
		
		//
		//On ajout les éventuelles liaisons
		//
		if(isset($desTable["LINK"])) {
			for ($x = 0; $x < count($desTable["LINK"]); $x++) {
				$extractinfo_sql .= static::genereRequeteLinks($desTable, $nomTable, $desTable["LINK"][$x], $this->params["REFERENCE"], $this->params["REFERENCEKEY"]);
			}
		}
		
		//si on a un filtre supplementaire
		if (isset($desTable["FILTER"])) {
		    if (isset($desTable["FILTER"][0]["GLOBAL"])) {
		        global ${$desTable["FILTER"][0]["GLOBAL"]};
		        $desTable["FILTER"][0]['value'] = str_replace('!!' . $desTable["FILTER"][0]["GLOBAL"] . '!!', ${$desTable["FILTER"][0]["GLOBAL"]}, $desTable["FILTER"][0]['value']);
		    }
			$extractinfo_sql .= " WHERE " . $desTable["FILTER"][0]['value'];	
		}
		
		//On applique la restriction ORDER BY
		//Utilisé pour les types de langues ou d'auteurs, ...
		if (isset($desTable["ORDERBY"])) {
			$extractinfo_sql .= " ORDER BY ".$this->ajoutIfNull($desTable["ORDERBY"][0]);
		}
		
		//Si l'on a un group by on passe par une sous-requete pour que le groupement soit fait après le tri (Cas des Auteurs : C'est l'auteur principal qui doit être utilisé pour le tri)
		if (isset($desTable["GROUPBY"])) {
			if (isset($desTable["ORDERBY"])) {
				// Si ORDER BY, on passe par une table temporaire car sinon il n'est pas pris en compte par le group by
				$sql = "DROP TEMPORARY TABLE IF EXISTS ".$nomTable."_groupby";
				pmb_mysql_query($sql);
				$temporary2_sql = "CREATE TEMPORARY TABLE ".$nomTable."_groupby ENGINE=MyISAM (".$extractinfo_sql.")";
				
				pmb_mysql_query($temporary2_sql);
				pmb_mysql_query("alter table ".$nomTable."_groupby add index(".$this->params["REFERENCEKEY"].")");
				
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
		$sql = "DROP TEMPORARY TABLE IF EXISTS ".$nomTable."_update";
		pmb_mysql_query($sql);
		$temporary2_sql = "CREATE TEMPORARY TABLE ".$nomTable."_update ENGINE=MyISAM (".$extractinfo_sql.")";
		
		pmb_mysql_query($temporary2_sql);
		pmb_mysql_query("alter table ".$nomTable."_update add index(".$this->params["REFERENCEKEY"].")");
		
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
	public function ajoutIfNull($tableau) {
		if (isset($tableau["NULLVALUE"])) {
			$tmpTxt = "IFNULL(" . $tableau['value'] . ",'" . $tableau["NULLVALUE"] . "')";
		} else {
			$tmpTxt = $tableau['value'];
		}
		
		return $tmpTxt;
	}
	
	
	/**
	 * Parse les fichiers XML de parametres
	 * il y a un fichier par type de tris
	 */
	public function parse() {
		global $include_path;
		global $charset;
		global $dbh;
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
		
		//tri perso
		$p_perso = new parametres_perso($this->params['PPERSOPREFIX']);
		
		foreach($p_perso->t_fields as $key => $t_field){
			if($t_field['OPAC_SHOW'] && $t_field['OPAC_SORT']){
				$param=$t_field['OPTIONS'][0];
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
								'LABEL' => translation::get_text($t_field['idchamp'], $p_perso->prefix."_custom", 'titre',  $t_field['TITRE']),
								'TABLEFIELD' => array('value'=>$tablefield),
								'REQ_SUITE' => "left join ".$p_perso->prefix."_custom_values on ".$this->params["REFERENCE"].".".$this->params["REFERENCEKEY"]." = ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_origine where ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_champ = '".$key."' ".$groupby
						);
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
								'LABEL' => translation::get_text($t_field['idchamp'], $p_perso->prefix."_custom", 'titre',  $t_field['TITRE']),
								'TABLEFIELD' => array('value'=>$tablefield),
								'REQ_SUITE' => "left join ".$p_perso->prefix."_custom_values on ".$this->params["REFERENCE"].".".$this->params["REFERENCEKEY"]." = ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_origine
left join ".$p_perso->prefix."_custom_lists on ".$p_perso->prefix."_custom_".$t_field['DATATYPE']." = ".$p_perso->prefix."_custom_list_value
where ".$p_perso->prefix."_custom_lists.".$p_perso->prefix."_custom_champ ='".$key."' and ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_champ ='".$key."'  ".$groupby
						);
						break;
					case "date_box" :
						$p_tri = array(
						'SOURCE' => "cp",
						'TYPEFIELD' => "select",
						'ID' => "cp".$key,
						'TYPE' => "text",
						'NAME' => $t_field['NAME'],
						'LABEL' => translation::get_text($t_field['idchamp'], $p_perso->prefix."_custom", 'titre',  $t_field['TITRE']),
						'TABLEFIELD' => array('value'=>$p_perso->prefix."_custom_".$t_field['DATATYPE']),
						'REQ_SUITE' => "left join ".$p_perso->prefix."_custom_values on ".$this->params["REFERENCE"].".".$this->params["REFERENCEKEY"]." = ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_origine where ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_champ = '".$key."'"
								);
						break;
					case "query_list" :
						$tableid = "";
						$tablefield = "";
						$tablename = "";
						if($param['MULTIPLE'][0]['value']){
							if($param['QUERY'][0]['value']){
								$res = pmb_mysql_query($param['QUERY'][0]['value'],$dbh);
								if ($res) {
									$tableid = pmb_mysql_field_name($res,0);
									$tablefield = "group_concat(".pmb_mysql_field_name($res,1)." separator ' ')";
									$tablename = pmb_mysql_field_table($res,0);
								}
							}
							$groupby = "group by ".$this->params["REFERENCEKEY"];
						} else {
							if($param['QUERY'][0]['value']){
								$res = pmb_mysql_query($param['QUERY'][0]['value'],$dbh);
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
								'LABEL' => translation::get_text($t_field['idchamp'], $p_perso->prefix."_custom", 'titre',  $t_field['TITRE']),
								'TABLEFIELD' => array('value'=>$tablefield),
								'REQ_SUITE' => "left join ".$p_perso->prefix."_custom_values on ".$this->params["REFERENCE"].".".$this->params["REFERENCEKEY"]." = ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_origine
left join ".$tablename." on ".$p_perso->prefix."_custom_".$t_field['DATATYPE']." = ".$tableid."
where ".$p_perso->prefix."_custom_values.".$p_perso->prefix."_custom_champ ='".$key."' ".$groupby
						);
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
				".$this->ajoutIfNull($field['TABLEFIELD'])." AS ".$nomChp."
			FROM ".$nomTable." LEFT JOIN ".$this->params['REFERENCE']." ON (".$this->params['REFERENCE'].".".$this->params['REFERENCEKEY']." = ".$nomTable.".".$this->params['REFERENCEKEY'].")
				".$field['REQ_SUITE'];
		
		//On met le tout dans une table temporaire
		$sql = "DROP TEMPORARY TABLE IF EXISTS ".$nomTable."_update";
		pmb_mysql_query($sql);
		$temporary2_sql = "CREATE TEMPORARY TABLE ".$nomTable."_update ENGINE=MyISAM (".$requete.")";
		pmb_mysql_query($temporary2_sql);
		pmb_mysql_query("alter table ".$nomTable."_update add index(".$this->params["REFERENCEKEY"].")");
		
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
	
	public static function get_default_sort_list($sort_name) {
		switch ($sort_name) {
			case "reading_list" :
				global $opac_default_sort_reading_list;
				return $opac_default_sort_reading_list;
			case "notices" :
			default :
				global $opac_default_sort_list;
				return $opac_default_sort_list;
		}
	}
	
	public static function get_default_sort($sort_name) {
		switch ($sort_name) {
			case "reading_list" :
				global $opac_default_sort_reading;
				return $opac_default_sort_reading;
			case "notices" :
			default :
				global $opac_default_sort;
				return $opac_default_sort;
		}
	}
}
?>