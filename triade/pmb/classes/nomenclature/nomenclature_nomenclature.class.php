<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_nomenclature.class.php,v 1.18 2019-02-18 13:45:53 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//require_once($class_path."/nomenclature/nomenclature_family.class.php");


/**
 * class nomenclature_nomenclature
 * Représente une nomenclature
 */
class nomenclature_nomenclature {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Tableau des familles de la nomenclature
	 * @access protected
	 */
	protected $families;

	/**
	 * Nomenclature abrégée
	 * @access protected
	 */
	protected $abbreviation;
	
	/**
	 * Tableau des ateliers de la nomenclature
	 * @access protected
	 */
	protected $workshops;
		
	protected $family_definition_in_progress = false;
	protected $musicstand_definition_in_progress = false;
	protected $instrument_definition_in_progress = false;
	protected $other_instrument_definition_in_progress = false;
	protected $current_family= -1;
	protected $current_musicstand=0;
	protected $musicstand_effective = 1;
	protected $instrument;
	protected $other_instrument;
	protected $musicstand_part=0;
	protected $indefinite_character = "~";
	/**
	 * Constructeur
	 *
	 * @return void
	 * @access public
	 */
	public function __construct( ) {
		$this->init_default_families_definition();
		$this->init_default_workshops_definition();
	} // end of member function __construct

	
	protected function init_default_families_definition(){
		global $dbh;
		$query = "select id_family from nomenclature_families order by family_order asc";
		$result = pmb_mysql_query($query,$dbh);
		$this->families = array();
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$this->families[] = new nomenclature_family($row->id_family);
			}
		}
	}
	
	protected function init_default_workshops_definition(){
		global $dbh;
		$query = "select id_workshop from nomenclature_workshops order by workshop_order asc";
		$result = pmb_mysql_query($query,$dbh);
		$this->workshops = array();
		if($result){
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$this->workshops[] = new nomenclature_workshop($row->id_workshop);
				}
			}	
		}
		
	}
	/**
	 * Setter
	 *
	 * @param string abbreviation Nomenclature abrégée

	 * @return void
	 * @access public
	 */
	public function set_abbreviation( $abbreviation ) {
		$this->abbreviation = pmb_preg_replace('/\s+/', '', $abbreviation);
	} // end of member function set_abbreviation

	/**
	 * Getter
	 *
	 * @return string
	 * @access public
	 */
	public function get_abbreviation( ) {
		return  pmb_preg_replace('/\s+/', '', $this->abbreviation);
	} // end of member function get_abbreviation

	/**
	 * Getter
	 *
	 * @return nomenclature_family
	 * @access public
	 */
	public function get_families( ) {
		return $this->families;
	} // end of member function get_families

	/**
	 * Setter
	 *
	 * @param nomenclature_family families Tableau des familles

	 * @return void
	 * @access public
	 */
	public function set_families( $families ) {
		$this->families = $families;
	} // end of member function set_families

	/**
	 * Analyse la nomenclature abrégée pour setter la property families
	 * 
	 * Appel à  la machine d'états
	 *
	 * @param bool partial Booléen qui m'indique que la nomenclature n'est pas complète

	 * @return void
	 * @access public
	 */
	
	public function get_next_family(){
		if(count($this->families) > $this->current_family){
			$this->family_definition_in_progress = true;
			$this->current_family++;
			$this->current_musicstand = -1;
			return true;
		}else{
			$this->family_definition_in_progress = false;
		}
		return false;
	}
	
	public function get_next_musicstand(){
		$this->musicstand_part=0;
		if(count($this->families[$this->current_family]->get_musicstands()) > $this->current_musicstand) {
			$this->musicstand_definition_in_progress = true;
			$this->musicstand_effective=1;
			$this->current_musicstand++;
			return true;
		}else {
			$this->musicstand_definition_in_progress = false;
			return false;
		}
	}
	
	public function get_standard_instrument(){
		if(!$this->instrument_definition_in_progress){
			$this->instrument_definition_in_progress = true;
			return clone $this->families[$this->current_family]->get_musicstand($this->current_musicstand)->get_standard_instrument();
		}
	}
	
	public function get_no_standard_instrument(){
		if(!$this->instrument_definition_in_progress){
			$this->instrument_definition_in_progress = true;
			$no_std_inst = new nomenclature_instrument(0,"", "");
			$no_std_inst->set_standard(false);
			return $no_std_inst; 
		}
	}
	
	public function get_other_instrument(){
		if(!$this->other_instrument_definition_in_progress){
			$this->other_instrument_definition_in_progress = true;
			$no_std_inst = new nomenclature_instrument(0,"", "");
			$no_std_inst->set_standard(false);
			return $no_std_inst;
		}
	}

	protected function finalize_current_other_instrument(){
		if($this->other_instrument_definition_in_progress){
			$this->instrument->add_other_instrument($this->other_instrument);
			$this->other_instrument = null;
			$this->other_instrument_definition_in_progress = false;
		}
	}
	
	protected function finalize_current_instrument(){
		if($this->instrument_definition_in_progress){
			$this->finalize_current_other_instrument();
			$this->families[$this->current_family]->get_musicstand($this->current_musicstand)->add_instrument($this->instrument,true);
// 			var_dump("Famille ".$this->current_family." (". $this->families[$this->current_family]->get_name().
// 			") => Pupitre ".$this->current_musicstand." (".
// 			$this->families[$this->current_family]->get_musicstand($this->current_musicstand)->get_name().
// 			") => ".
// 			count($this->families[$this->current_family]->get_musicstand($this->current_musicstand)->get_instruments()));
// 			var_dump($this->families[$this->current_family]->get_musicstand($this->current_musicstand));
			$this->instrument = null;
			$this->instrument_definition_in_progress = false;
		}else if($this->musicstand_effective > 0){
			//cas ou seul l'effectif est défini, on prend alors l'instrument standard avec l'effectif correspondant
			$this->instrument = clone $this->families[$this->current_family]->get_musicstand($this->current_musicstand)->get_standard_instrument();
			$this->instrument->set_effective($this->musicstand_effective);
			$this->instrument_definition_in_progress = true;
			$this->finalize_current_instrument();
		}
	}
	
	protected function finalize_current_musicstand(){
		if($this->musicstand_definition_in_progress){
			$this->finalize_current_instrument();
			$this->families[$this->current_family]->get_musicstand($this->current_musicstand)->set_effective($this->musicstand_effective);
			//réinitialisation
			$this->musicstand_effective=1;
			$this->musicstand_definition_in_progress = false;
		}
	}
	
	protected function finalize_current_family(){
		$this->finalize_current_musicstand();
		//réinitialisation
		$this->family_definition_in_progress = false;
	}
		
	public function analyze(){
		global $msg;
		$this->family_definition_in_progress = $this->musicstand_definition_in_progress = $this->instrument_definition_in_progress = $this->other_instrument_definition_in_progress = false;
		$state = "START";
		for($i=0 ; $i<strlen($this->abbreviation) ;$i++){
			$c = $this->abbreviation[$i];
// 			var_dump($state."	=> ".$c);
			switch($state){
				case "START" :
				case "NEW_FAMILY" :
					//on veut un chiffre au départ...
					if($c === "0"  || $c*1 > 0 ){
						//si une famille est encore en cours de définition, il y a un problème
						if($this->family_definition_in_progress){
							$state = "ERROR";
							$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_already_family_def"]);
						}else{
							//on récupère la prochaine famille
							if(!$this->get_next_family()){
								//si plus de familles à définir, il y a un problème
								$state = "ERROR";
								$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_end_family_def"]);
							}else{
								//on créé de le premier pupitre de la famille...
								if($this->musicstand_definition_in_progress){
									$state = "ERROR";
									$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_already_musicstand_def"]);
								}else{
									if(!$this->get_next_musicstand()){
										$state = "ERROR";
										$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_end_musicstand_def"]);
									}else{
										$this->musicstand_effective = $c;
										$state = "MUSICSTAND";
									}
								}
							}
						}
					}else{
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_no_numeric"]);
					}
					break;
				case "MUSICSTAND" :
					//pas de pupitre en cours de définition, on a un problème
					if(!$this->musicstand_definition_in_progress){
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_no_musicstand_def"]);
					}else{
						//ca peut être un chiffre encore (concaténation de l'effectif)
						if($c === "0"  || $c*1 > 0 ){
							$this->musicstand_effective.= $c;
						}else{
							switch($c){
								//fin de la famille
								case "-" :
									$this->finalize_current_family();
									$state="NEW_FAMILY";
									break;
								//fin de pupitre
								case "." :
									$this->finalize_current_musicstand();
									$state = "NEW_MUSICSTAND";
									break;
								case "[" :
									$state = "NEW_INSTRUMENT";
									break;
								case "]" :
									$state = "ERROR";
									$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_no_detail_musicstand_def"]);
									break;
							}
						}
					}
					break;
				case "NEW_MUSICSTAND" :
					// pupitre en cours de définition, on a un problème
					if($this->musicstand_definition_in_progress){
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_already_musicstand_def"]);
					}else{
						if($c === "0"  || $c*1 > 0 ){
							if(!$this->get_next_musicstand()){
								$state = "ERROR";
								$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_end_musicstand_def"]);
							}else{
								$this->musicstand_effective = $c;
								$state = "MUSICSTAND";
								$this->musicstand_part=0;
							}
						}else{
							$state = "ERROR";
							$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_no_numeric"]);
						}
					}
					break;
				case "NEW_INSTRUMENT" :
					// un instrument est déjà en cours de définition
					if($this->instrument_definition_in_progress){
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_already_instrument_def"]);
					}else{
						// un chiffre ? alors c'est un instrument standard
						if($c === "0"  || $c*1 > 0 ){							
							if($this->families[$this->current_family]->get_musicstand($this->current_musicstand)->get_divisable()){
								$this->instrument->set_effective($c);
								$this->musicstand_part++;
								$this->instrument->set_part($this->musicstand_part);								
							}else{
								//le chiffre est le numéro d'ordre sur le pupitre...
							}	
							$this->instrument = $this->get_standard_instrument();
							$state = "INSTRUMENT_STANDARD";
						}else{
							switch($c){
								case "]" :
								case "." :
								case "-" :
									$state = "ERROR";
									$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze"]);
									break;
								default :
									$this->instrument = $this->get_no_standard_instrument();
									$this->instrument->set_code($c);
									$state = "INSTRUMENT_NO_STANDARD"; 
							}
						}
					}
					break;
				case "INSTRUMENT_STANDARD" :
					if(!$this->instrument_definition_in_progress){
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_no_instrument_def"]);
					}else { 
						if($c === "0"  || $c*1 > 0 ){
							if($this->families[$this->current_family]->get_musicstand($this->current_musicstand)->get_divisable()){
								$this->instrument->set_effective($this->instrument->get_effective().$c);
							}else{
								//rien à faire, c'est la suite du numéro d'ordre
							}
						}else{
							switch($c){
								case "-" :
									$state = "ERROR";
									$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_close_musicstand_detail"]);
								break;
								case "." :
									$this->finalize_current_instrument();
									$state = "NEW_INSTRUMENT";
									break;
								case "]" :
									$state = "MUSICSTAND";
									break;
								case "/" :
									$state = "NEW_OTHER_INSTRUMENT";
									break;
							}
						}
					}
					break;
				case "INSTRUMENT_NO_STANDARD":
					if(!$this->instrument_definition_in_progress){
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_no_instrument_def"]);
					}else {
						switch($c){
							case "-" :
								$state = "ERROR";
								$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_close_musicstand_detail"]);
								break;
							case "." :
								$this->finalize_current_instrument();
								$state = "NEW_INSTRUMENT";
								break;
							case "]" :
								$state = "MUSICSTAND";
								break;
							case "/" :
								$state = "NEW_OTHER_INSTRUMENT";
								break;
							default : 
								$this->instrument->set_code($this->instrument->get_code().$c);
								break;
						}
					}
					break;
				case "NEW_OTHER_INSTRUMENT" :
					if($this->other_instrument_definition_in_progress){
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_already_other_instrument_def"]);
					}else{
						if($c === "0"  || $c*1 > 0 ){
							$state = "ERROR";
							$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_other_instrument_no_first_numeric"]);
						}else{
							switch($c){
								case "/" :
								case "]" :
								case "." :
								case "-" :
									$state = "ERROR";
									$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze"]);
									break;
								default :
									$this->other_instrument = $this->get_other_instrument();
									$this->other_instrument->set_code($this->other_instrument->get_code().$c);
									$state = "OTHER_INSTRUMENT";
									break;
							}
						}
					}
					break;
				case "OTHER_INSTRUMENT" :
					if(!$this->other_instrument_definition_in_progress){
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_no_other_instrument_def"]);
					}else {
						switch($c){
							case "-" :
								$state = "ERROR";
								$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_close_musicstand_detail"]);
								break;
							case "." :
								$this->finalize_current_instrument();
								$state = "NEW_INSTRUMENT";
								break;
							case "]" :
								$state = "MUSICSTAND";
								break;
							case "/" :
								$this->finalize_current_other_instrument();
								$state = "NEW_OTHER_INSTRUMENT";
								break;
							default :
								$this->other_instrument->set_code($this->other_instrument->get_code().$c);
								break;
						}
					}	
					break;
 				case "ERROR" :
				default:
					break;
			}
		}
		if($state == "ERROR"){
			for($i=0 ; $i<strlen($this->abbreviation) ;$i++){
				if($error['position'] == $i){
					print "<b>";
				}
				print $this->abbreviation[$i];
				if($error['position'] == $i){
					print "</b>";
				}
			}
			var_dump($error['msg']);
		}else{
			$this->finalize_current_family();
		}
	}
	
	/**
	 * Méthode qui vérifie la structure de l'arbre des familles
	 *
	 * @return bool
	 * @access public
	 */
	public function check( ) {
	} // end of member function check

	/**
	 * Calcule et affecte la nomenclature abrégée à  partir de l'arbre
	 *
	 * @return void
	 * @access public
	 */
	public function calc_abbreviation( ) {
		$tfamilies = array();
		foreach ($this->families as $family) {
			$nomenclature_family = new nomenclature_family($family->get_id());
			$nomenclature_family->calc_abbreviation();
			$tfamilies[] = $nomenclature_family->get_abbreviation();
		}
		$this->set_abbreviation(implode("-", $tfamilies));
	} // end of member function calc_abbreviation
	
	public function get_families_tree(){
		$tree = array();
		foreach($this->families as $family){
			$tree[] = array(
				'id' => $family->get_id(),
				'name' => $family->get_name(),
				'musicstands' => $this->get_musiscstands_tree($family)			
			);
		}
		return $tree;
	}
	protected function get_musiscstands_tree($family){
		$tree = array();
		foreach($family->get_musicstands() as $musicstand){
			$tree[] = $musicstand->get_tree_informations();
		}
		return $tree;
	}
	
	public function get_indefinite_character(){
		return $this->indefinite_character;
	}
	
	public function set_indefinite_character($indefinite_charracter){
		$this->indefinite_character = $indefinite_charracter;
	}
	
	/**
	 * Getter
	 *
	 * @return nomenclature_workshop
	 * @access public
	 */
	public function get_workshops( ) {
		return $this->workshops;
	} // end of member function get_workshops
	
	/**
	 * Setter
	 *
	 * @param nomenclature_workshop families Tableau des ateliers
	
	 * @return void
	 * @access public
	 */
	public function set_workshops( $workshops ) {
		$this->workshops = $workshops;
	} // end of member function set_workshops
	
	public function get_workshops_tree(){
		$tree = array();
		foreach($this->workshops as $workshop){
			$tree[] = array(
					'id' => $workshop->get_id(),
					'label' => $workshop->get_label(),
					'instruments' => $this->get_instruments_tree($workshop)
			);
		}
		return $tree;
	}
	
	protected function get_instruments_tree($workshop){
		$tree = array();
		foreach($workshop->get_instruments() as $instrument){
			$tree[] = $instrument->get_tree_informations();
		}
		return $tree;
	}
	
} // end of nomenclature_nomenclature