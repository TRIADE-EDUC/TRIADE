<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: exercices.class.php,v 1.31.2.1 2024/05/21 13:55:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/actes.class.php");
require_once("$class_path/budgets.class.php");
require_once("$class_path/entites.class.php");
require_once("$class_path/interface/admin/interface_admin_acquisition_form.class.php");

if(!defined('STA_EXE_CLO')) define('STA_EXE_CLO', 0);	//Statut		0 = Cloturé
if(!defined('STA_EXE_ACT')) define('STA_EXE_ACT', 1);	//Statut		1 = Actif
if(!defined('STA_EXE_DEF')) define('STA_EXE_DEF', 3);	//Statut		3 = Actif par défaut

class exercices{

	public $id_exercice = 0;					//Identifiant de l'exercice
	public $num_entite = 0;
	public $libelle = '';
	public $date_debut = '2006-01-01';
	public $date_fin = '2006-01-01';
	public $statut = STA_EXE_ACT;			//Statut de l'exercice

	//Constructeur.
	public function __construct($id_exercice= 0) {
		$this->id_exercice = intval($id_exercice);
		if ($this->id_exercice) {
			$this->load();
		}
	}

	// charge l'exercice à partir de la base.
	public function load(){
		$q = "select * from exercices where id_exercice = '".$this->id_exercice."' ";
		$r = pmb_mysql_query($q) ;
		if(pmb_mysql_num_rows($r)) {
    		$obj = pmb_mysql_fetch_object($r);
    		$this->id_exercice = $obj->id_exercice;
    		$this->num_entite = $obj->num_entite;
    		$this->libelle = $obj->libelle;
    		$this->date_debut = $obj->date_debut;
    		$this->date_fin = $obj->date_fin;
    		$this->statut = $obj->statut;
		}
	}

	public function get_content_form() {
	    global $msg;
	    global $charset;
	    global $ptab;
	    
	    $interface_content_form = new interface_content_form(static::class);
	    $interface_content_form->add_element('libelle', '103')
	    ->add_input_node('text', $this->libelle);
	    if(!$this->id_exercice) {
	        $interface_date = new interface_date('date_deb');
	        $content_date_debut = $interface_date->get_display();
	        $interface_date = new interface_date('date_fin');
	        $content_date_fin = $interface_date->get_display();
	        $content_statut = htmlentities($msg['acquisition_statut_actif'], ENT_QUOTES, $charset);
	    } else {
	        if (exercices::hasBudgets($this->id_exercice) || exercices::hasActes($this->id_exercice)) {
	            $content_date_debut = formatdate($this->date_debut);
	            $content_date_fin = formatdate($this->date_fin);
	        } else {
	            $interface_date = new interface_date('date_deb');
	            $interface_date->set_value($this->date_debut);
	            $content_date_debut = $interface_date->get_display();
	            $interface_date = new interface_date('date_fin');
	            $interface_date->set_value($this->date_fin);
	            $content_date_fin = $interface_date->get_display();
	        }
	        switch ($this->statut) {
	            case STA_EXE_CLO :
	                $ms = $msg['acquisition_statut_clot'];
	                $aff_bt_def = FALSE;
	                break;
	            case STA_EXE_DEF :
	                $ms = $msg['acquisition_statut_def'];
	                $aff_bt_def = FALSE;
	                break;
	            default :
	                $ms = $msg['acquisition_statut_actif'];
	                $aff_bt_def = TRUE;
	                break;
	        }
	        $content_statut = htmlentities($ms,ENT_QUOTES,$charset);
	        if ($aff_bt_def) {
	            $content_statut .= $ptab[2];
	        }
	    }
	    $interface_content_form->add_element('date_debut', 'calendrier_date_debut')
	    ->add_html_node($content_date_debut);
	    $interface_content_form->add_element('date_fin', 'calendrier_date_fin')
	    ->add_html_node($content_date_fin);
	    $interface_content_form->add_element('statut', 'acquisition_statut')
	    ->add_html_node($content_statut);
	    return $interface_content_form->get_display();
	}
	
	public function get_form($id_entite) {
	    global $msg;
	    global $charset;
	    
	    $interface_form = new interface_admin_acquisition_form('exerform');
	    if(!$this->id_exercice){
	    	$interface_form->set_label($msg['acquisition_ajout_exer']);
	    }else{
	    	$interface_form->set_label($msg['acquisition_modif_exer']);
	    }
	    $biblio = new entites($id_entite);
	    $form = "<div class='row'><label class='etiquette'>".htmlentities($biblio->raison_sociale,ENT_QUOTES,$charset)."</label></div>";
	    
	    $interface_form->set_object_id($this->id_exercice)
	    ->set_id_entity($id_entite)
	    ->set_statut($this->statut)
	    ->set_confirm_cloture_msg($msg['acquisition_compta_confirm_clot']." ".$this->libelle." ?")
	    ->set_confirm_delete_msg($msg['acquisition_compta_confirm_suppr']." ".$this->libelle." ?")
	    ->set_content_form($this->get_content_form())
	    ->set_table_name('exercices')
	    ->set_field_focus('libelle');
	    $form .= $interface_form->get_display();
	    return $form;
	}
	
	public function set_properties_from_form() {
		global $libelle, $ent, $date_deb, $date_fin;
		
		$this->libelle = stripslashes($libelle);
		$this->num_entite = $ent;
		if ($date_deb && $date_fin) {
			$this->date_debut = $date_deb;
			$this->date_fin = $date_fin;
		}
	}
	
	// enregistre l'exercice en base.
	public function save(){
		if( (!$this->num_entite) || ($this->libelle == '') ) die("Erreur de création exercice");
		if($this->id_exercice) {
			$q = "update exercices set num_entite = '".$this->num_entite."', libelle ='".addslashes($this->libelle)."', ";
			$q.= "date_debut = '".$this->date_debut."', date_fin = '".$this->date_fin."', statut = '".$this->statut."' ";
			$q.= "where id_exercice = '".$this->id_exercice."' ";
			pmb_mysql_query($q);
		} else {
			$q = "insert into exercices set num_entite = '".$this->num_entite."', libelle = '".addslashes($this->libelle)."', ";
			$q.= "date_debut =  '".$this->date_debut."', date_fin = '".$this->date_fin."', statut = '".$this->statut."' ";
			pmb_mysql_query($q);
			$this->id_exercice = pmb_mysql_insert_id();
			$this->load();
		}
	}

	//supprime un exercice de la base
	public static function delete($id_exercice= 0) {
		$id_exercice = intval($id_exercice);
		if(!$id_exercice) return;

		//Suppression des actes
//TODO Voir suppression du lien entre actes et exercices

 		$res_actes = actes::listByExercice($id_exercice);
		while (($row = pmb_mysql_fetch_object($res_actes))) {
			actes::delete($row->id_acte);
		}

		//Suppression des budgets
		$res_budgets = budgets::listByExercice($id_exercice);
		while (($row = pmb_mysql_fetch_object($res_budgets))) {
			budgets::delete($row->id_budget);
		}
		//Suppression de l'exercice
		$q = "delete from exercices where id_exercice = '".$id_exercice."' ";
		pmb_mysql_query($q);

	}

	//retourne une requete pour la liste des exercices de l'entité
	public static function listByEntite($id_entite, $mask='-1', $order='date_debut desc') {
		$q = "select * from exercices where num_entite = '".$id_entite."' ";
		if ($mask != '-1') $q.= "and (statut & '".$mask."') = '".$mask."' ";
		$q.= "order by ".$order." ";
		return $q;
	}

	//Vérifie si un exercice existe
	public static function exists($id_exercice){
		$id_exercice = intval($id_exercice);
		$q = "select count(1) from exercices where id_exercice = '".$id_exercice."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si le libellé d'un exercice existe déjà pour une entité
	public static function existsLibelle($id_entite, $libelle, $id_exercice=0){
		$id_entite = intval($id_entite);
		$id_exercice = intval($id_exercice);
		$q = "select count(1) from exercices where libelle = '".addslashes($libelle)."' and num_entite = '".$id_entite."' ";
		if ($id_exercice) $q.= "and id_exercice != '".$id_exercice."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}

	//Compte le nb de budgets affectés à un exercice
	public static function hasBudgets($id_exercice=0){
		$id_exercice = intval($id_exercice);
		if (!$id_exercice) return 0;
		$q = "select count(1) from budgets where num_exercice = '".$id_exercice."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}

	//Compte le nb de budgets actifs affectés à un exercice
	public static function hasBudgetsActifs($id_exercice=0){
		$id_exercice = intval($id_exercice);
		if (!$id_exercice) return 0;
		$q = "select count(1) from budgets where num_exercice = '".$id_exercice."' and statut != '2' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}

	//Compte le nb d'actes affectés à un exercice
	public static function hasActes($id_exercice=0){
		$id_exercice = intval($id_exercice);
		if (!$id_exercice) return 0;
		$q = "select count(1) from actes where num_exercice = '".$id_exercice."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}

	//Compte le nb d'actes actifs affectés à un exercice
	//Actes actifs == commandes non soldées et non payées
	public static function hasActesActifs($id_exercice=0){
		$id_exercice = intval($id_exercice);
		if (!$id_exercice) return 0;
		$q = "select count(1) from actes where num_exercice = '".$id_exercice."' ";
		$q.= "and (type_acte = 0 and (statut & 32) != 32) ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}

	//choix exercice par défaut pour une entité
	public function setDefault($id_exercice=0) {
		if (!$id_exercice) $id_exercice = $this->id_exercice;
		$q = "update exercices set statut = '".STA_EXE_ACT."' where statut = '".STA_EXE_DEF."' and num_entite = '".$this->num_entite."' limit 1 ";
		pmb_mysql_query($q);
		$q = "update exercices set statut = '".STA_EXE_DEF."' where id_exercice = '".$this->id_exercice."' limit 1 ";
		pmb_mysql_query($q);

	}

	//Recuperation de l'exercice session
	public static function getSessionExerciceId($id_bibli,$id_exer) {
		global $deflt3exercice;

		$id_bibli = intval($id_bibli);
		$id_exer = intval($id_exer);
		$q = "select id_exercice from exercices where num_entite = '".$id_bibli."' and (statut &  '".STA_EXE_ACT."') = '".STA_EXE_ACT."' ";
		$q.= "order by statut desc ";
		$r = pmb_mysql_query($q);
		$res=array();
		while($row=pmb_mysql_fetch_object($r)) {
			$res[]=$row->id_exercice;
		}
		if (!$id_exer && isset($_SESSION['id_exercice']) && $_SESSION['id_exercice']) {
			$id_exer=$_SESSION['id_exercice'];
		}
		if (in_array($id_exer, $res)) {
			$_SESSION['id_exercice'] = $id_exer;
		} elseif (in_array($deflt3exercice, $res)) {
			$_SESSION['id_exercice'] = $deflt3exercice;
		} elseif (isset($res[0])) {
			$_SESSION['id_exercice'] = $res[0];
		} else {
		    $_SESSION['id_exercice'] = 0;
		}
		return $_SESSION['id_exercice'];
	}

	//Definition de l'exercice session
	public function setSessionExerciceId($deflt3exercice) {
		$_SESSION['id_exercice']=$deflt3exercice;
		return;
	}

	//optimization de la table exercices
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE exercices');
		return $opt;
	}

	public static function getExercicesByEntite($id_bibli, $actif_only = true) {
	    $id_bibli = intval($id_bibli);
	    if(!$id_bibli) {
	        return [];
	    }
	    $q = "select id_exercice, libelle, statut from exercices where num_entite = '".$id_bibli."' ";
	    if($actif_only) {
	        $q .= "and (statut &  '".STA_EXE_ACT."') = '".STA_EXE_ACT."' ";
	    }
	    $q.= "order by statut desc, libelle asc ";
	    $r = pmb_mysql_query($q);
	    $exercices = array();
	    while ($row = pmb_mysql_fetch_object($r)){
	        $exercices[$row->id_exercice] = array(
	            'label' => $row->libelle,
	            'actif' => ($row->statut & STA_EXE_ACT ? 1 : 0)
	        );
	    }
	    return $exercices;
	}
	
	//Retourne un selecteur html avec la liste des exercices actifs pour une ou plusieurs bibliotheque
	public static function getHtmlSelect($id_bibli=0, $selected=0, $sel_all=FALSE, $sel_attr=array(), $actif_only = true) {
		global $msg,$charset;

		$sel='';
		if ($id_bibli) {
		    $res = array();
		    if ($sel_all) {
		        $res[0]=array(
		            'label' => $msg['acquisition_exer_all'],
		            'actif' => '1'
		        );
		    }
		    $exercices = static::getExercicesByEntite($id_bibli, $actif_only);
		    foreach ($exercices as $id_exercice=>$exercice) {
		        $res[$id_exercice] = $exercice;
		    }
			if (count($res)) {
				$sel="<select ";
				if (count($sel_attr)) {
					foreach($sel_attr as $attr=>$val) {
						$sel.="$attr='".$val."' ";
					}
				}
				$sel.=">";
				foreach($res as $id=>$val){
					$sel.="<option value='".$id."'";
					if($id==$selected) $sel.=' selected=selected';
					if(!$val['actif']) {
					    $sel.=" style='color:#ccc;' ";
					}
					$sel.=" >";
					$sel.=htmlentities($val['label'],ENT_QUOTES,$charset);
					$sel.="</option>";
				}
				$sel.='</select>';
			}
		}
		return $sel;
	}

	public static function getActiveExercicesByEntite($id_bibli) {
		$id_bibli = intval($id_bibli);
		if(!$id_bibli) {
			 return [];
		}
		$q = "select id_exercice, libelle from exercices where num_entite = '".$id_bibli."' and (statut &  '".STA_EXE_ACT."') = '".STA_EXE_ACT."' ";
		$q.= "order by statut desc, libelle asc ";
		$r = pmb_mysql_query($q);
		if(!$r) {
			return [];
		}
		$ret=array();
		while($row = pmb_mysql_fetch_assoc($r)) {
			$ret[$row['id_exercice']] = $row['libelle'];
		}
		return $ret;
	}
}






