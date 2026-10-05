<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexint.class.php,v 1.42.2.1 2024/06/04 13:59:06 jparis Exp $

// définition de la classe de gestion des 'indexations internes'
if ( ! defined( 'INDEXINT_CLASS' ) ) {
  define( 'INDEXINT_CLASS', 1 );

class indexint {

	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------
	public $indexint_id=0;	// MySQL indexint_id in table 'indexint'
	public	$name='';		// nom de l'indexation
	public	$comment='';	// commentaire
	public	$pclass='';		// id plan de classement
	public	$childs = array();
	public	$has_child = 0 ;
	public $num_statut = 1;
	protected $p_perso;
	public $name_pclass = '';
	public $display = '';

	// ---------------------------------------------------------------
	//		indexint($id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0, $rech_cote="") {
		$this->indexint_id = intval($id);
		if (!$this->indexint_id && $rech_cote) {
	        $this->name = $rech_cote;
		}
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	//		getData() : récupération infos 
	// ---------------------------------------------------------------
	public function getData() {
	    global $pclass;
	    
		$this->name			='';
		$this->comment		='';
		$this->pclass		= (!empty($pclass) ? intval($pclass) : 0);
		$this->display="";
		$this->num_statut = 1;
		if(!$this->indexint_id) {
			if ($this->name) { // rech par cote et non par $id
				$requete = "SELECT indexint_id,indexint_name,indexint_comment,num_pclass FROM indexint WHERE indexint_name='".$this->name."' " ;
				$result = pmb_mysql_query($requete);
				if(pmb_mysql_num_rows($result)) {
					$temp = pmb_mysql_fetch_object($result);
					$this->indexint_id	= $temp->indexint_id;
					$this->name		= $temp->indexint_name;
					$this->comment		= $temp->indexint_comment;
					$this->pclass		= $temp->num_pclass;
					if ($this->comment) $this->display = $this->name." ($this->comment)" ;
					else $this->display = $this->name ;
					$this->num_statut = $this->get_authority()->get_num_statut();
				}
			}
		} else {
			$requete = "SELECT indexint_id,indexint_name,indexint_comment,num_pclass FROM indexint WHERE indexint_id='".$this->indexint_id."' " ;
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				$this->indexint_id	= $temp->indexint_id;
				$this->name		= $temp->indexint_name;
				$this->comment		= $temp->indexint_comment;
				$this->pclass		= $temp->num_pclass;
				if ($this->comment) $this->display = $this->name." ($this->comment)" ;
				else $this->display = $this->name;
				$this->num_statut = $this->get_authority()->get_num_statut();
			}
		}
		$this->cherche_child();
	}

	public function has_notices() {
		$query = "select count(1) from notices where indexint=".$this->indexint_id;
		$result = pmb_mysql_query($query);
		return (@pmb_mysql_result($result, 0, 0));
	}

	public function cherche_direct_child() {
	// fonction réduite à un seul niveau de récursivité par rapport à cherche_child. gm
		global $pmb_indexint_decimal ;
		
		$this->childs = array();
		
		if (!$pmb_indexint_decimal) {
			$this->has_child = 0 ;
			return ;
			}
		
		/* calcul de l'arbo :
		si 3ème carac != 0
			niveau 3
			sinon si 2eme carac != 0
				niveau 2
				sinon prendre le premier carac
		rechercher quand même avec les trois carac entiers
		*/
	
		if (pmb_strlen($this->name)>3)
		{
		$clause = " indexint_name regexp '^".$this->name.".$'";
		}
		else
		{
		$carac1 = substr($this->name, 0 , 1);
		$carac2 = substr($this->name, 1 , 1);
		$carac3 = substr($this->name, 2 , 1);
		$entier = substr($this->name, 0 , 3);
			if ($carac3 != "0") {
				$clause = " indexint_name regexp '^".$entier."..$' " ;
				} elseif ($carac2 != "0") {
					$clause = " indexint_name regexp '^".$carac1.$carac2.".$' " ;
					} else
						{ 
						if ($carac1 != "1") { $clause = " indexint_name regexp '^".$carac1.".$' " ; } 
						else $clause = " indexint_name regexp '^.00$' "; }  
		}
		
		$query = "select indexint_id,indexint_name,indexint_comment from indexint where ".$clause." order by indexint_name ";
		$res = pmb_mysql_query($query);
		$this->has_child=pmb_mysql_num_rows($res) ;
		if ($this->has_child) {
			while ($obj=pmb_mysql_fetch_object($res)) {
				$this->childs[]=array(
						'idchild' => $obj->indexint_id,
						'namechild' => $obj->indexint_name,
						'commentchild' => $obj->indexint_comment) ;
			}
		} 
		return ;
	}


	public function cherche_child() {
		global $pmb_indexint_decimal ;
		
		$this->childs = array();
		
		if (!$pmb_indexint_decimal || !$this->indexint_id) {
			$this->has_child = 0 ;
			return ;
		}
		
		/* calcul de l'arbo :
		si 3ème carac != 0
			niveau 3
			sinon si 2eme carac != 0
				niveau 2
				sinon prendre le premier carac
		rechercher quand même avec les trois carac entiers
		*/
		$entier = substr($this->name, 0 , 3);
		if (pmb_strlen($this->name)>3){
			$clause = " indexint_name like '".$entier."%'";
		}else {
			$carac1 = substr($this->name, 0 , 1);
			$carac2 = substr($this->name, 1 , 1);
			$carac3 = substr($this->name, 2 , 1);
			if ($carac3 != "0"){
				$clause = " indexint_name like '".$entier."%' " ;
			}elseif ($carac2 != "0"){
				$clause = " indexint_name like '".$carac1.$carac2."%' " ;
			}else{
				$clause = " indexint_name like '".$carac1."%' " ;
			}
		}
		if($this->pclass){
			$clause.= " AND num_pclass='".$this->pclass."' " ;
		}
		// avec affichage de l'indexation parente
		// $query = "select indexint_id,indexint_name,indexint_comment from indexint where ".$clause." order by indexint_name ";
		// sans affichage de l'indexation parente
		$query = "select indexint_id,indexint_name,indexint_comment from indexint where ".$clause." and indexint_name <> '".addslashes($this->name)."' order by indexint_name ";
		$res = pmb_mysql_query($query);
		$this->has_child=pmb_mysql_num_rows($res) ;
		if ($this->has_child) 
			while ($obj=pmb_mysql_fetch_object($res)) {
				$this->childs[]=array(
						'idchild' => $obj->indexint_id,
						'namechild' => $obj->indexint_name,
						'commentchild' => $obj->indexint_comment) ;
			}
		return ;
	}
	
	public function child_list($image='./images/folder.gif',$css='', $dest=0) {
	
		global $css;
		global $nb_col_scat;
		global $main;

		$l = "";
		foreach($this->childs as $valeur) {
			$libelle = $valeur['namechild']." ".$valeur['commentchild'];
			$id = $valeur['idchild'];
			$l .=  "<a href=./index.php?lvl=indexint_see&id=$id&main=$main ><img src='".get_url_icon('folder.gif')."' style='border:0px'> ".$libelle."</a>";
			$l .= "<br />";
		}
		$l = "<br /><div style='margin-left:48px'>$l</div>";
		return $l;
	}

	public function get_db_id() {
		return $this->indexint_id;
	}
	
	public function get_isbd() {
		if ($this->comment) $isbd = $this->name." - ".str_replace("\r"," ",str_replace("\n"," ",$this->comment));
		else $isbd = $this->name ;
		if ($this->name_pclass) {
			$isbd = "[".$this->name_pclass."] ".$isbd;
		}
		return $isbd;
	}
	
	public function get_permalink() {
		global $liens_opac;
		return str_replace('!!id!!', $this->indexint_id, $liens_opac['lien_rech_indexint']);
	}
	
	public function get_comment() {
		return $this->comment;
	}
	
	public function get_header() {
		return $this->display;
	}
	
	public function format_datas($antiloop = false){
		$formatted_data = array(
				'name' => $this->name,
				'comment' => $this->comment
		);
		$formatted_data = array_merge($this->get_authority()->format_datas(), $formatted_data);
		return $formatted_data;
	}
	
	public function get_p_perso() {
		if(!isset($this->p_perso)) {
			$this->p_perso = $this->get_authority()->get_p_perso();
		}
		return $this->p_perso;
	}
	
	public function get_authority() {
		return authorities_collection::get_authority('authority', 0, ['num_object' => $this->indexint_id, 'type_object' => AUT_TABLE_INDEXINT]);
	}
} # fin de définition de la classe indexint

} # fin de délaration

