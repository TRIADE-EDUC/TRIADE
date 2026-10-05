<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_equations.class.php,v 1.2 2022/02/10 10:46:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class bannette_equations{
	protected $num_bannette;
	protected $equations;
	
	public function __construct($num_bannette) {
		$this->num_bannette = intval($num_bannette);		
		$this->fetch_data();
	}
	
	public function fetch_data() {		
		$this->equations = array();
		$requete = "select num_equation from bannette_equation, equations WHERE num_bannette='".$this->num_bannette."' and id_equation=num_equation ";
		$res = pmb_mysql_query($requete);
		while($equ=pmb_mysql_fetch_object($res)) {
			$this->equations[] = $equ->num_equation ;
		}
	}
	
	public function get_text(){
		$text = '';
		if (static::is_private($this->num_bannette)) {
			if (isset($this->equations[0]) && $this->equations[0]) {
				$equation = new equation($this->equations[0]);
				$text .= $equation->nom_equation;
			}
		} else {
			if($countEquations = count($this->equations)){
				foreach($this->equations as $key=>$id){
					$equation = new equation($id);
					$text .= $equation->human_query;
					if($key != ($countEquations-1)){
						$text .= "<br>";
					}
				}
			}
		}
		return $text;
	}
	
	public static function delete($num_bannette=0) {
		$num_bannette = intval($num_bannette);
		if (static::is_private($num_bannette)) {
			$requete = "select num_equation from bannette_equation WHERE num_bannette='".$num_bannette."'";
			$res = pmb_mysql_query($requete);
			$temp = pmb_mysql_fetch_object($res);
			$requete = "delete from equations WHERE id_equation='$temp->num_equation'";
			$res = pmb_mysql_query($requete);
		}
		$requete = "delete from bannette_equation WHERE num_bannette='".$num_bannette."'";
		$res = pmb_mysql_query($requete);
	}
	
	protected static function is_private($num_bannette=0) {
		$num_bannette = intval($num_bannette);
		$query = "select proprio_bannette from bannettes where id_bannette = ".$num_bannette;
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, 0, 0);
	}
	
	public function get_equations() {
		return $this->equations;
	}
		
}// end class
