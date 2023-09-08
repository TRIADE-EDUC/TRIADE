<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: electre.class.php,v 1.8 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");

require_once('electre_apiv3.class.php');

class electre extends connector {
    private $apiv3;
    private $aut_function=null;
    
	
    public function __construct($connector_path="") {
    	parent::__construct($connector_path);
    }
    
    public function get_id() {
    	return "electre";
    }
    
    //Est-ce un entrepot ?
	public function is_repository() {
		return 2;
	}
    
    public function source_get_property_form($source_id) {
    	global $charset;
    	$params=$this->get_source_params($source_id);
    	if ($params["PARAMETERS"]) {
    	    //Affichage du formulaire avec $params["PARAMETERS"]
    	    $vars=unserialize($params["PARAMETERS"]);
    	    foreach ($vars as $key=>$val) {
    	        global ${$key};
    	        ${$key}=$val;
    	    }
    	}
    	if(!$electre_maxresults){
    	    $electre_maxresults = 250;
    	}
    	
    	$form="
        <div class='row'>
            <div class='colonne3'>
                <label for='url'>".$this->msg["electre_maxresults"]."</label>
    		</div>
            <div class='colonne_suite'>
    		  <input name=\"electre_maxresults\" type=\"text\" value=\"".htmlentities($electre_maxresults,ENT_QUOTES,$charset)."\">
            </div>
        </div>";
    	return $form;
    }
    
	 //Formulaire des propriétés générales
	public function get_property_form() {
		global $charset;
		$this->fetch_global_properties();
		//Affichage du formulaire en fonction de $this->parameters
		if ($this->parameters) {
			$keys = unserialize($this->parameters);
			$clientID= $keys['clientID'];
			$clientSecret=$keys['clientSecret'];
		} else {
		    $clientID="";
		    $clientSecret="";
		}	
				
		$r="<div class='row'>
				<div class='colonne3'><label for='clientID'>".$this->msg["electre_clientID"]."</label></div>
				<div class='colonne-suite'><input type='text' id='clientID' name='clientID' value='".htmlentities($clientID,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='clientSecret'>".$this->msg["electre_clientSecret"]."</label></div>
				<div class='colonne-suite'><input type='text' class='saisie-50em' id='clientSecret' name='clientSecret' value='".htmlentities($clientSecret,ENT_QUOTES,$charset)."'/></div>
			</div>";
		return $r;
	}
    
    public function make_serialized_properties() {
        global $clientID, $clientSecret,$electre_maxresults;
		//Mise en forme des paramètres à partir de variables globales (mettre le résultat dans $this->parameters)
		$keys = array();
		$keys['clientID']=$clientID;
		$keys['clientSecret']=$clientSecret;
		$keys['electre_maxresults']=$electre_maxresults;
		$this->parameters = serialize($keys);
	}
	

	public function make_serialized_source_properties($source_id) {
	    global $electre_maxresults;
	    $t["electre_maxresults"] = (int) $electre_maxresults;
	    $this->sources[$source_id]["PARAMETERS"] = serialize($t);
	}
	
	public function rec_record($record,$source_id,$search_id) {
	    global $msg;
        
        //on regarde si la notice n'a pas déjà été trouvée...
        $query = 'select * from external_count';
        if($this->has_ref($source_id, $record['id'])){
            return;
        }
        $nb_categ = 0;
        foreach($record as $key => $infos){
            switch($key){
                case 'id' :
                    $unimarc["001"][0] = $infos;
                    break;
                case 'source' :
                    $unimarc["801"][0]["a"][0] = "FR";
                    $unimarc["801"][0]["b"][0] = $infos;
                    break;
                case 'titre' :
                    $unimarc["200"][0]["a"][0] = $infos;
                    break;
                case 'resume' :  
                    $unimarc["330"][0]["a"][0] = $infos;
                    break;
                case 'quatriemedecouverture' :
                    $unimarc["930"][0]["a"][0] = $infos;
                    break;
                case 'editeurs' :
                    for($i=0 ; $i< count($infos) ; $i++){
                        $unimarc["210"][$i]["c"][0] = $infos[$i]['libelle'];
                        $unimarc["210"][$i]["3"][0] = $infos[$i]['id'];
                    }
                    break;
                case 'auteurs' :
                    for($i=0 ; $i< count($infos) ; $i++){
                        $zone='700';
                        if($i!=0){
                            $zone='701';
                        }
                        $aut = explode(',',$infos[$i]['formeBib']);
                        
                        $bloc = array(
                            '3' => array($infos[$i]['id']),
                            'a' => array(trim($aut[0])),
                            '4' => array($this->getFunctionMapping($infos[$i]['contribution']))
                        );
                        if(trim($aut[1]) != ""){
                            $bloc['b'] = array(trim($aut[1]));
                        }
                        $unimarc[$zone][]= $bloc;
                    }
                    break;
                case 'contributeurs' :
                    for($i=0 ; $i< count($infos) ; $i++){
                        $bloc = array(
                            '3' => array($infos[$i]['id']),
                            'a' => array($infos[$i]['formeBib']),
                            '4' => array($this->getFunctionMapping($infos[$i]['contribution']))
                        );
                        $unimarc[701][]= $bloc;
                    }
                    break;
                case 'dateParution' :
                    $unimarc["210"][0]['d'][0] = date($msg['1005'],strtotime($infos));
                    break;
                case 'collections' :
                    for($i=0 ; $i< count($infos) ; $i++){
                        $unimarc['225'][$i]['a'][0] = $infos[$i]['libelle'];
                        $unimarc['225'][$i]['3'][0] = $infos[$i]['id']; 
                    }
                    break;
                case 'prix' :
                    $unimarc['010'][0]['d'][0] = $infos['ttc'].' '.$infos['devise'];
                    break;
                case 'description' :
                    if($infos['nbPages']){
                        $unimarc['215'][0]['a'][0] = $infos['nbPages'].' p.';
                    }
                    $size = "";
                    if($infos['largeur']){
                        $size = $infos['largeur'];
                        if($infos['hauteur']){
                            $size.='x'.$infos['hauteur'];
                        }
//                         if($infos['epaisseur']){
//                             $size.='x'.$infos['epaisseur'];
//                         }
                    }
                    if($size!= ''){
                        $unimarc['215'][0]['d'][0] = $size.' cm';
                    }
                    if($infos['mentionillustrations']){
                        $unimarc['215'][0]['e'][0] = $infos['mentionillustrations'];
                    }
                    break;
                case 'clils' :
                case 'publics' :
                    for($i=0 ; $i<count($infos) ; $i++){
                        $unimarc['606'][$nb_categ]=array(
                            '3' => array($infos[$i]['code']),
                            'a' => array($infos[$i]['libelle'])
                        );
                        $nb_categ++;
                    }
                   
                    break;
                case 'isbns':
                    if(!isset($unimarc['010'][0]['a'][0])){
                        $unimarc["010"][0]["a"][0] = formatISBN(str_replace('-','',$infos[0]),13);
                    }
                    break;
                case 'disponibilite' :
                case 'eansCouverture' :
                case 'editions' :
                case '_links' :
                case 'flagScolaire' :
                case 'disponibilite' :
                case 'catalog' :
                    break;
                default : 
//                     var_dump($key,$infos,'-----------------------------------------------------------------------');
            }
        }
        
        // Absence de source ?
        if(!isset($record['source'])){
            $unimarc["801"][0]["a"][0] = "FR";
            $unimarc["801"][0]["b"][0] = "Electre";
        }
        
        // Si résumé, on laisse tombé la 4ème de couv!
        if(!isset($unimarc['330']) && $unimarc['930']){
            $unimarc['330'] = $unimarc['930'];
        }
        unset($unimarc['930']);
        
        //Pas d'ISBN, on prend l'EAN
        if(!isset($unimarc['010'][0]['a'][0]) && isset($record['eans'])){
            $unimarc["010"][0]["a"][0] = $record['eans'];
        }
        $recid = $this->insert_into_external_count($source_id, $record['id']);
        $values = array(
            '("'.$this->get_id().'", "'.$source_id.'", "'.$record['id'].'", "'.date("Y-m-d H:i:s",time()).'", "rs", "", "", "", "*", "", "'.$recid.'", "'.$search_id.'")',
            '("'.$this->get_id().'", "'.$source_id.'", "'.$record['id'].'", "'.date("Y-m-d H:i:s",time()).'", "ru", "", "", "", "*", "", "'.$recid.'", "'.$search_id.'")',
            '("'.$this->get_id().'", "'.$source_id.'", "'.$record['id'].'", "'.date("Y-m-d H:i:s",time()).'", "el", "", "", "", "1", "", "'.$recid.'", "'.$search_id.'")',
            '("'.$this->get_id().'", "'.$source_id.'", "'.$record['id'].'", "'.date("Y-m-d H:i:s",time()).'", "bl", "", "", "", "m", "", "'.$recid.'", "'.$search_id.'")',
            '("'.$this->get_id().'", "'.$source_id.'", "'.$record['id'].'", "'.date("Y-m-d H:i:s",time()).'", "hl", "", "", "", "0", "", "'.$recid.'", "'.$search_id.'")',
            '("'.$this->get_id().'", "'.$source_id.'", "'.$record['id'].'", "'.date("Y-m-d H:i:s",time()).'", "dt", "", "", "", "a", "", "'.$recid.'", "'.$search_id.'")'
        );
        $field_order=0;
        foreach($unimarc as $ufield => $field){
            $subfield_order=0;
            for($i=0 ; $i<count($field) ; $i++){
                if(is_array($field[$i])){
                    foreach($field[$i] as $usubfield => $subfield){
                        for($j=0 ; $j<count($subfield) ; $j++){
                            $values[]= '("'.$this->get_id().'", "'.$source_id.'", "'.$record['id'].'", "'.date("Y-m-d H:i:s",time()).'", "'.$ufield.'", "'.$usubfield.'", "'.$field_order.'", "'.$subfield_order.'", "'.addslashes($subfield[$j]).'", " '.addslashes(strip_empty_words($subfield[$j])).' ", "'.$recid.'", "'.$search_id.'")';
                        }
                    }
                }else{
                    $values[]= '("'.$this->get_id().'", "'.$source_id.'", "'.$record['id'].'", "'.date("Y-m-d H:i:s",time()).'", "'.$ufield.'", "", "'.$field_order.'", "", "'.addslashes($field[$i]).'", " '.addslashes(strip_empty_words($field[$i])).' ", "'.$recid.'", "'.$search_id.'")';
                }
                $subfield_order++;
            }
            $field_order++;
        }
        $requete="insert into entrepot_source_".$source_id." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid, search_id) values ";
        $requete.= implode(",", $values);
        pmb_mysql_query($requete);
	}
		
	//Fonction de recherche
	public function search($source_id,$query,$search_id) {
	    $apiV3 = $this->getAPI($source_id);
		for ($i=0 ; $i<count($query) ; $i++){
		    $electreField = $this->getSearchMapping($query[$i]->ufield);
		    if ($electreField != ''){
		        $value = $query[$i]->values[0];
		        if($electreField == "isbnEan"){
		            $value = formatISBN(str_replace('-','',$value), 13);
		        }
		        $results = $apiV3->search($electreField,$value);
		        if(isset($results['results']['totalcount']) && $results['results']['totalcount']>0 ){
		            for($j=0 ; $j<count($results['results']['list']) ; $j++){
		                $this->rec_record($results['results']['list'][$j], $source_id, $search_id);
		            }
		        }
		    }
		}
	}
	
	private function getAPI($source_id)
	{
	    if(!is_object($this->apiv3)){ 
	        if ($this->parameters) {
	           $keys = unserialize($this->parameters);
	           $clientID= $keys['clientID'];
	           $clientSecret=$keys['clientSecret'];
	           $electre_maxresults=$keys['electre_maxresults'];
            }
            $params=$this->get_source_params($source_id);
            $params = unserialize($params['PARAMETERS']);
	        $this->apiv3 = new electre_apiv3($clientID, $clientSecret);
	        $this->apiv3->set_maxresults($params['electre_maxresults']);
	    }
	    return $this->apiv3;
	}
	
	public function enrichment_is_allow(){
		return false;
	}
	
	private function getSearchMapping($field){
	    $electreField = '';
	    switch ($field){
	        case 'XXX' :
	            $electreField = ['titre','auteur','collection','editeur','isbnEan'];
	            break;
	        case '200$a' :
	            $electreField = 'titre';
	            break;
	        case '7XX' :
	            $electreField = 'auteur';
	            break;
	        case '225$a410$t' :
	            $electreField = 'collection';
	            break;
	        case '210$c' :
	            $electreField = 'editeur';
	            break;
	        case '010$a' :
	            $electreField = 'isbnEan';
	            break;
	    }
	    return $electreField;
	}
	
	private function getFunctionMapping($function)
	{
        global $include_path;
	    if (!is_array($this->aut_function)){
	        // C'est en français chez Electre !
	        global $lang;
	        $old = $lang;
	        $lang='fr_FR';
            $tmp = new marc_list('function');
            $lang=$old;
            $this->aut_function = array_flip($tmp->table);
        }
        if(isset($this->aut_function[$function])){
            return $this->aut_function[$function];
        }
        return $function;
	}
}
?>