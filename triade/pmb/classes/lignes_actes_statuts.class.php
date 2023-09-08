<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lignes_actes_statuts.class.php,v 1.8 2019-04-20 14:45:16 ccraig Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class lgstat{
	
	 
	public $id_statut = 0;					//Identifiant de statut de ligne d'acte	
	public $libelle  = '';					//Libelle
	public $relance = 0;					//0=non, 1=oui
	 
	//Constructeur.	 
	public function __construct($id_statut=0) {
		$this->id_statut = $id_statut+0;
		if ($this->id_statut) {
			$this->load();	
		}
	}	
	
	
	// charge un statut de ligne d'acte à partir de la base.
	public function load(){
	
		global $dbh;
		
		$q = "select * from lignes_actes_statuts where id_statut = '".$this->id_statut."' ";
		$r = pmb_mysql_query($q, $dbh) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->libelle = $obj->libelle;
		$this->relance = $obj->relance;

	}

	
	// enregistre un statut de ligne d'acte en base.
	public function save(){
		
		global $dbh;

		if( $this->libelle == '' ) die("Erreur de création statut de ligne d'acte");
	
		if ($this->id_statut) {
			
			$q = "update lignes_actes_statuts set  
					libelle = '".$this->libelle."',
					relance = '".$this->relance."'
					where id_statut = '".$this->id_statut."' ";
			$r = pmb_mysql_query($q, $dbh);
			
		} else {
			
			$q = "insert into lignes_actes_statuts set 
					libelle = '".$this->libelle."',
					relance = '".$this->relance."' ";
			$r = pmb_mysql_query($q, $dbh);
			$this->id_statut = pmb_mysql_insert_id($dbh);
		
		}
	}


	//Retourne une liste des statuts de lignes d'actes (tableau)
	public static function getList($x='ARRAY_ALL') {
		
		global $dbh;
		$res = array();
		
		$q = "select * from lignes_actes_statuts order by libelle ";
		
		switch ($x) {
			case 'QUERY' :
				return $q;
			case 'ARRAY_VALUES' :
				$r = pmb_mysql_query($q, $dbh);
				$res = array();
				while ($row = pmb_mysql_fetch_object($r)){
					$res[] = $row->id_statut;
				}
				break;
			case 'ARRAY_ALL':
			default :
				$r = pmb_mysql_query($q, $dbh);
				$res = array();
				while ($row = pmb_mysql_fetch_object($r)){
					$res[$row->id_statut] = array();
					$res[$row->id_statut][0] = $row->libelle;
					$res[$row->id_statut][1] = $row->relance;
				}
				break;
		}
		return $res;
	}

	//Retourne un selecteur html avec la liste des statuts de lignes d'actes
	public static function getHtmlSelect($selected=array(), $sel_all='', $sel_attr=array()) {
		
		global $dbh,$msg,$charset;

		$sel='';
		$q = "select id_statut,libelle from lignes_actes_statuts order by libelle ";
		$r = pmb_mysql_query($q, $dbh);
		$res = array();
		if ($sel_all) {
			$res[0]=htmlentities($sel_all,ENT_QUOTES,$charset);
		}
		
		while ($row = pmb_mysql_fetch_object($r)){
			$res[$row->id_statut] = $row->libelle;
		}
		
		$size=count($res);
		if (isset($sel_attr['size']) && $sel_attr['size']>$size) $sel_attr['size']=$size;
		
		if ($size) {
			$sel="<select ";
			if (count($sel_attr)) {
				foreach($sel_attr as $attr=>$val) {
					$sel.="$attr='".$val."' ";
				}
			}
			$sel.=">";
			foreach($res as $id=>$val){
				$sel.="<option value='".$id."'";
				if(in_array($id,$selected)) $sel.=" selected='selected'";
				$sel.=" >";
				$sel.=htmlentities($val,ENT_QUOTES,$charset);
				$sel.="</option>";
			}
			$sel.='</select>';
		}
		return $sel;
	}
	
	
	
	//Vérifie si un statut de ligne d'acte existe
	public static function exists($id_statut) {
		$id_statut += 0;
		$q = "select count(1) from lignes_actes_statuts where id_statut = '".$id_statut."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
		
	}
	
		
	//Vérifie si le libelle d'un statut de ligne d'acte existe déjà en base
	public static function existsLibelle($libelle,$id_statut) {
		$id_statut += 0;
		$q = "select count(1) from lignes_actes_statuts where libelle = '".$libelle."' ";
		if ($id_statut) $q.= "and id_statut != '".$id_statut."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}
	
	public static function getLabelFromId($id) {
		return lgstat::getList()[$id][0];
	}


	//supprime un statut de ligne d'acte de la base
	public static function delete($id_statut= 0) {
		$id_statut += 0;
		if (!$id_statut) return;

		$q = "delete from lignes_actes_statuts where id_statut = '".$id_statut."' ";
		$r = pmb_mysql_query($q);
	}


	//Vérifie si un statut de ligne d'acte est utilise dans les lignes d'actes	
	public static function isUsed($id_statut){
		
		global $dbh;
		$id_statut += 0;
		if (!$id_statut) return 0;
		$total=0;
		$q = "select count(1) from lignes_actes where num_statut = '".$id_statut."' ";
		$r = pmb_mysql_query($q, $dbh); 
		$total+=pmb_mysql_result($r, 0, 0);
		$q = "select count(1) from lignes_actes_relances where num_statut = '".$id_statut."' ";
		$r = pmb_mysql_query($q, $dbh); 
		$total+=pmb_mysql_result($r, 0, 0);
		$q = "select count(1) from users where deflt3lgstatdev='".$id_statut."' or deflt3lgstatcde='".$id_statut." '";
		$r = pmb_mysql_query($q, $dbh);
		pmb_mysql_result($r, 0, 0);
		$total+=pmb_mysql_result($r, 0, 0);
		return $total;
	}


	//optimization de la table lignes_actes_statuts
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE lignes_actes_statuts');
		return $opt;
				
	}
				
}