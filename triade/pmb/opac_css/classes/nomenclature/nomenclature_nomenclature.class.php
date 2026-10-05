<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_nomenclature.class.php,v 1.5 2024/04/25 12:47:10 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//require_once($class_path."/nomenclature/nomenclature_family.class.php");


/**
 * class nomenclature_nomenclature
 * Représente une nomenclature
 */
class nomenclature_nomenclature {

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
	protected $voice_definition_in_progress;
	protected $voices;
	protected $current_voice;
	protected $current_voice_effective;
	protected $current_voice_code;
	
	protected static $families_list;
	protected static $workshops_list;
	/**
	 * Constructeur
	 *
	 * @param boolean reset
	 * @return void
	 * @access public
	 */
	public function __construct($reset = false) {
		if($reset){
			static::$families_list = array();
			static::$workshops_list = array();
		}
		$this->init_default_families_definition();
		$this->init_default_workshops_definition();
	} // end of member function __construct

	
	protected function init_default_families_definition(){
	    if (!empty(static::$families_list)) {
	        $this->families = static::$families_list;
	    } else {
    		$query = "select id_family from nomenclature_families order by family_order asc";
    		$result = pmb_mysql_query($query);
    		$this->families = array();
    		if(pmb_mysql_num_rows($result)){
    			while($row = pmb_mysql_fetch_object($result)){
    				$this->families[] = nomenclature_family::get_instance($row->id_family, true);
    			}
    		}
    		static::$families_list = $this->families;
	    }
	}
	
	protected function init_default_workshops_definition(){
	    if (!empty(static::$workshops_list)) {
	        $this->workshops = static::$workshops_list;
	    } else {
    		$query = "select id_workshop from nomenclature_workshops order by workshop_order asc";
    		$result = pmb_mysql_query($query);
    		$this->workshops = array();
    		if($result){
    			if(pmb_mysql_num_rows($result)){
    				while($row = pmb_mysql_fetch_object($result)){
    					$this->workshops[] = nomenclature_workshop::get_instance($row->id_workshop);
    				}
    			}	
    		}
    		static::$workshops_list = $this->workshops;
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
		    if($this->other_instrument->get_id() === 0){
		        $query ="select id_instrument, instrument_name from nomenclature_instruments where instrument_code = '".$this->other_instrument->get_code()."'";
		        $result = pmb_mysql_query($query);
		        if(pmb_mysql_num_rows($result)){
		            $row = pmb_mysql_fetch_object($result);
		            $this->other_instrument->set_name($row->instrument_name);
		            $this->other_instrument->set_id($row->id_instrument);
		        }
		    }
			$this->instrument->add_other_instrument($this->other_instrument);
			$this->other_instrument = null;
			$this->other_instrument_definition_in_progress = false;
		}
	}

	protected function finalize_current_no_standard_instrument(){
		if($this->no_standard_instrument_definition_in_progress){
		    if($this->instrument->get_id() === 0){
		        $query ="select id_instrument, instrument_name from nomenclature_instruments where instrument_code = '".$this->instrument->get_code()."'";
		        $result = pmb_mysql_query($query);
		        if(pmb_mysql_num_rows($result)){
		            $row = pmb_mysql_fetch_object($result);
		            $this->instrument->set_name($row->instrument_name);
		            $this->instrument->set_id($row->id_instrument);
		        }
		    }
		    $this->no_standard_instrument_definition_in_progress = false;
		}
	}
	
	protected function finalize_current_instrument(){
		if($this->instrument_definition_in_progress){
			$this->finalize_current_other_instrument();
			$this->finalize_current_no_standard_instrument();
			$this->families[$this->current_family]->get_musicstand($this->current_musicstand)->add_instrument($this->instrument,true);
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
	
	protected function finalize_current_voice() {
	    if ($this->current_voice['effective_indefinite']) {
	        $this->current_voice['effective'] = 'nd';
	    }
	    $this->voices[] = $this->current_voice;
	    $this->current_voice = array(
	        'effective' => '',
	        'effective_indefinite' => '',
	        'code' => ''
	    );
        $this->voice_definition_in_progress = false;
	}
		
	public function analyze(){
		global $msg;

		$this->family_definition_in_progress = $this->musicstand_definition_in_progress = $this->instrument_definition_in_progress = $this->other_instrument_definition_in_progress = $this->no_standard_instrument_definition_in_progress = false;
		$state = "START";
		
		for ($i = 0; $i < strlen($this->abbreviation); $i++) {
		    
			// (i) Les espaces sont supprimés dans le set_abbreaviation()
			$c = $this->abbreviation[$i];
			
			switch ($state) {
				case "START" :
				case "NEW_FAMILY" :
				    
				    //on veut un chiffre au départ ou le caractère indéfini
				    if (!is_numeric($c) && $c != $this->indefinite_character) {
				        $state = "ERROR";
				        $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_no_numeric"]);
				        break;
				    }

				    if ($this->family_definition_in_progress) {
				        $state = "ERROR";
				        $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_already_family_def"]);
				        break;
				    }
				    
				    //on récupère la prochaine famille
				    if (!$this->get_next_family()) {
				        //si plus de familles à définir, il y a un problème
				        $state = "ERROR";
				        $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_end_family_def"]);
				        break;
				    }
				    
			        //on créé de le premier pupitre de la famille...
			        if ($this->musicstand_definition_in_progress){
			            $state = "ERROR";
			            $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_already_musicstand_def"]);
			            break;
			        }
			        
		            if (!$this->get_next_musicstand()) {
		                $state = "ERROR";
		                $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_end_musicstand_def"]);
		            } else {
		                $this->musicstand_effective = $c;
		                $state = "MUSICSTAND";
		            }
		            
					break;
					
				case "MUSICSTAND" :
					//pas de pupitre en cours de définition, on a un problème
					if(!$this->musicstand_definition_in_progress){
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_no_musicstand_def"]);
						break;
					}
					
					//ca peut être un chiffre encore (concaténation de l'effectif)
					if ($c === "0"  || intval($c) > 0) {
						$this->musicstand_effective.= $c;
					} else {
						switch($c) {
						    case $this->indefinite_character:
						        $state = "ERROR";
						        $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_already_musicstand_undetermined"]);
								break;
								
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
								
							default:
								$state = "ERROR";
								$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_illegal_character"]);
								break;
						}
					}
					break;
				case "NEW_MUSICSTAND" :
					// pupitre en cours de définition, on a un problème
					if($this->musicstand_definition_in_progress){
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_already_musicstand_def"]);
						break;
					}
					
					if($c === "0"  || intval($c) > 0 ){
						if(!$this->get_next_musicstand()){
							$state = "ERROR";
							$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_end_musicstand_def"]);
						}else{
							$this->musicstand_effective = $c;
							$state = "MUSICSTAND";
							$this->musicstand_part=0;
						}
					} else if ($c == $this->indefinite_character) {
					    $this->musicstand_effective = 'nd';
					    $this->get_next_musicstand();
					    $state = "MUSICSTAND";
					} else {
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_no_numeric"]);
					}
					break;
				case "NEW_INSTRUMENT" :
					// un instrument est déjà en cours de définition
					if($this->instrument_definition_in_progress){
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_already_instrument_def"]);
						break;
					}
					
					switch ($c) {
						case "]" :
						case "." :
						case "-" :
							$state = "ERROR";
							$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze"]);
							break;

						case "[" :
							$state = "ERROR";
							$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_already_musicstand_detail_def"]);
							break;
						
						default:
							// un chiffre ? alors c'est un instrument standard
							if ($c === "0" || intval($c) > 0) {
							    if (preg_match('/^[A-Za-z]+$/', $this->abbreviation[$i+1])) {
							        // HACK: Si le caractère suivant est une lettre, alors on a un instrument non standard dans les cordes
							        $this->no_standard_instrument_definition_in_progress = true;
							        $this->instrument = $this->get_no_standard_instrument();
							        $state = "INSTRUMENT_NO_STANDARD"; 
							        break;
							    }
							    	
						    	if (isset($this->instrument) && $this->families[$this->current_family]->get_musicstand($this->current_musicstand)->get_divisable()) {
									$this->instrument->set_effective($c);
									$this->musicstand_part++;
									$this->instrument->set_part($this->musicstand_part);								
								}
								
								$this->instrument = $this->get_standard_instrument();
								$state = "INSTRUMENT_STANDARD";
							} else {
								switch($c){
									case $this->indefinite_character:
										$this->instrument = $this->get_standard_instrument();
										$this->instrument->set_effective('nd');
										$state = "INSTRUMENT_STANDARD";
									    break;
									default :
									    $this->no_standard_instrument_definition_in_progress = true;
										$this->instrument = $this->get_no_standard_instrument();
										$this->instrument->set_code($c);
										$state = "INSTRUMENT_NO_STANDARD";
										break;
								}
							}
							break;
					}
					break;
				case "INSTRUMENT_STANDARD" :
					if(!$this->instrument_definition_in_progress){
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_no_instrument_def"]);
						break;
					}

					if ( $c === "0"  || intval($c) > 0){
						if ($this->families[$this->current_family]->get_musicstand($this->current_musicstand)->get_divisable()) {
							$this->instrument->set_effective($this->instrument->get_effective().$c);
						}
					}else{
						switch($c){
							case "-" :
								$state = "ERROR";
								$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_close_musicstand_detail"]);
								break;
							case "[" :
								$state = "ERROR";
								$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_already_musicstand_detail_def"]);
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
					break;
				case "INSTRUMENT_NO_STANDARD":
					if(!$this->instrument_definition_in_progress){
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_no_instrument_def"]);
						break;
					}
					
					switch($c) {
						case "-" :
							$state = "ERROR";
							$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_close_musicstand_detail"]);
							break;
						case "[" :
							$state = "ERROR";
							$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_already_musicstand_detail_def"]);
							break;
						case "." :
							$this->finalize_current_instrument();
							$state = "NEW_INSTRUMENT";
							break;
						case "]" :
						    $this->finalize_current_no_standard_instrument();
							$state = "MUSICSTAND";
							break;
						case "/" :
						    $this->finalize_current_no_standard_instrument();
							$state = "NEW_OTHER_INSTRUMENT";
							break;
						default : 
							$this->instrument->set_code($this->instrument->get_code().$c);
							break;
					}
					break;
				case "NEW_OTHER_INSTRUMENT" :
					if($this->other_instrument_definition_in_progress){
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_already_other_instrument_def"]);
						break;
					}
					
					if($c === "0"  || intval($c) > 0 ){
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_other_instrument_no_first_numeric"]);
						break;
					}
					
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
					break;
				case "OTHER_INSTRUMENT" :
					if(!$this->other_instrument_definition_in_progress){
						$state = "ERROR";
						$error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_error_analyze_no_other_instrument_def"]);
						break;
					}
					switch($c) {
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
    				break;
 				case "ERROR" :
				default:
					break;
			}
		}
		if($state == "ERROR"){
			for($i=0 ; $i<strlen($this->abbreviation) ;$i++){
				if($error['position'] == $i){
					print "<b style='background-color: yellow;'>";
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
			$nomenclature_family = nomenclature_family::get_instance($family->get_id());
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
	
	public function is_letter($char){
	    if(preg_match('/[a-z\s]/i', $char)){
	        return true;
	    }
	    return false;
	}
	
	public function analyze_voices() {
	    global $msg;
	    $state = "START";
	    $error = array();
	    $this->current_voice = array(
	        'effective' => '',
	        'effective_indefinite' => '',
	        'code' => ''
	    );
	    $part_in_def = false;

	    for ($i = 0; $i < strlen($this->abbreviation); $i++) {
	    	
	    	// (i) Les espaces sont supprimés dans le set_abbreaviation()
	        $c = $this->abbreviation[$i];
	        
	        switch ($state) {
	            case "START":
	                $this->current_voice['effective_indefinite'] = true;
	                if($c === "0"  || intval($c) > 0 ){
	                    $this->current_voice['effective'] = $c;
	                    $this->current_voice['effective_indefinite'] = false;
	                    $state = "VOICE_EFFECTIVE";
	                }else if($this->is_letter($c)){
	                    $this->current_voice['code']=$c;
	                    $this->voice_definition_in_progress = true;
	                    $state = "VOICE";
	                }else if($c == $this->indefinite_character){
	                    $this->voice_definition_in_progress = true;
	                    $state = "VOICE";
	                }else{
	                    $state = "ERROR";
	                    $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_voices_error_analyze_invalid_char"]);
	                }
	                break;
	            case "NEW_VOICE":
	                $this->current_voice['effective_indefinite'] = true;
	                if ($c === "0"  || intval($c) > 0 ){
	                    $this->current_voice['effective'].=$c;
	                    $this->current_voice['effective_indefinite'] = false;
	                    $state = "VOICE_EFFECTIVE";
	                } elseif ($this->is_letter($c)){
	                    $this->current_voice['code']=$c;
	                    $this->voice_definition_in_progress = true;
	                    $state = "VOICE";
	                } elseif ($c == $this->indefinite_character){
	                    $this->voice_definition_in_progress = true;
	                    $state = "VOICE";
	                } else {
	                    $state = "ERROR";
	                    $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_voices_error_analyze_invalid_char"]);
	                }
	                break;
	            case "VOICE":
	                if($this->voice_definition_in_progress){
	                    if($this->is_letter($c)){
	                        $this->current_voice['code'].=$c;
	                    }else{
	                        switch($c){
	                            case ".":
	                                $this->finalize_current_voice();
	                                if(!$this->voice_definition_in_progress){
	                                    $state = "NEW_VOICE";
	                                }else{
	                                    $state = "ERROR";
	                                    $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_voices_error_analyze_incorrect_effective"]);
	                                }
	                                break;
	                            case "[":
	                                $part_in_def = true;
	                                $state = "NEW_PART";
	                                break;
	                            default:
	                                $state = "ERROR";
	                                $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_voices_error_analyze_invalid_char"]);
	                                break;
	                        }
	                    }
	                }else{
	                    $state = "ERROR";
	                    $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_voices_error_analyze_no_voice_in_def"]);
	                }
	                break;
	            case "VOICE_EFFECTIVE":
	                if($c === "0"  || intval($c) > 0 ){
	                    $this->current_voice['effective'].=$c;
	                }else if($this->is_letter($c)){
	                    $this->current_voice['code'].=$c;
	                    $this->voice_definition_in_progress = true;
	                    $state = "VOICE";
	                }else{
	                    $state = "ERROR";
	                    $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_voices_error_analyze_letter_needed"]);
	                }
	                break;
	                
	            case "NEW_PART":
	                if ($part_in_def === false) {
	                    $state = "ERROR";
	                    $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_voices_error_analyze_no_part_in_def"]);
    	                break;
	                }
	                
	                if($c === "0"  || intval($c) > 0 ) {
	                    $this->current_voice['effective'].=$c;
	                    $state = "PART_EFFECTIVE";
    	                break;
	                }
	                
	                switch ($c) {
	                    case "~":
	                        $this->current_voice['effective_indefinite'] = true;
	                        $state = "PART";
	                        break;
	                    
	                    default:
                            $state = "ERROR";
                            $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_voices_error_analyze_invalid_char"]);
                            break;
	                }
	                break;

	            case "PART":
	                switch ($c) {
	                    case ".":
	                        $state = "NEW_PART";
	                        break;
	                    
	                    case "]":
	                        $part_in_def = false;
	                        $state = "VOICE";
	                        break;
	                    
	                    default:
                            $state = "ERROR";
                            $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_voices_error_analyze_specifics_chars_only"]);
                            break;
	                }
	                break;

	            case "PART_EFFECTIVE":
	                
	                if ($c === "0"  || intval($c) > 0 ) {
	                    $this->current_voice['effective'].=$c;
	                    break;
	                }
	                
	                switch ($c) {
	                    case ".":
	                        $this->finalize_current_voice();
	                        $state = "NEW_PART";
	                        break;
	                    
	                    case "]":
	                        $part_in_def = false;
	                        $state = "VOICE";
	                        break;
	                    
	                    default:
                            $state = "ERROR";
                            $error = array('position'=> $i,'msg' => $msg["nomenclature_js_nomenclature_voices_error_analyze_invalid_char"]);
                            break;
	                }
	                break;

	            case "ERROR":
	            default:
	                break;
	        }
	    }
	    
	    if($state == "ERROR"){
	        for($i=0 ; $i<strlen($this->abbreviation) ;$i++){
	            if($error['position'] == $i){
	                print "<b style='background-color: yellow;'>";
	            }
	            print $this->abbreviation[$i];
	            if($error['position'] == $i){
	                print "</b>";
	            }
	        }
	        var_dump($error['msg']);
	    }else{
	        $this->finalize_current_voice();
	    }
	}
	
	public function get_voices() {
	    return $this->voices;
	}
	
} // end of nomenclature_nomenclature