<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestion_source.class.php,v 1.3 2017-04-26 10:20:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class suggestion_source{
	
	public $id_source=0;
	public $libelle_source='';
	
	/*
	 * Constructeur
	 */
	public function __construct($id=0){
		$this->id_source = $id+0;
		if(!$this->id_source){
			
			$this->libelle_source = '';
		} else {
			$req="select libelle_source from suggestions_source where id_source='".$this->id_source."'";
			$res = pmb_mysql_query($req);
			$src = pmb_mysql_fetch_object($res);
			$this->libelle_source = $src->libelle_source;
		}
	}
}
?>