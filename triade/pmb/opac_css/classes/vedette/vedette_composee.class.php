<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_composee.class.php,v 1.24.4.1 2025/02/12 12:34:08 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/authperso.class.php");
require_once($class_path."/vedette/vedette_link.class.php");
require_once($class_path."/vedette/vedette_grammars.class.php");

class vedette_composee {

    /*** Attributes: ***/

    /**
     * Le nom du fichier de configuration xml
     * @var string
     */
    private $config_filename;

    /**
     * Tableau des éléments de la vedette : le premier niveau est la liste des
     * subdivisions, l'ordre du tableau dans chaque subdivision détermine l'ordre des
     * éléments
     * @var vedette_element
     * @access private
     */
    protected $vedette_elements = array();

    /**
     * Identifiant de la vedette composée
     * @access private
     */
    protected $id;

    /**
     * Label de la vedette composée
     * @var string
     */
    protected $label;

    /**
     * Singleton des configs lues pour ne pas reparser à chaque fois les XML
     * @var array ($config_filename => array('available_fields', 'subdivisions', 'separator'))
     */
    private static $configs = array();

    public function __construct($id = 0, $config_filename = "rameau"){
        $this->id=intval($id);
        if($this->id){
            $this->read();
        } else {
            $this->config_filename = $config_filename;
            $this->set_config();
        }
    }

    /**
     * Renvoie la liste des subdivisions
     *
     * @return Array()
     * @access public
     */
    public function get_subdivisions(){
        return self::$configs[$this->config_filename]['subdivisions'];
    }

    /**
     * Renvoie la liste des champs disponibles à l'ajout dans une vedette composée
     *
     * @return Array()
     * @access public
     */
    public function get_available_fields(){
        return self::$configs[$this->config_filename]['available_fields'];
    }

    /**
     * Renvoie les informations sur le champ visé
     * en fonction du num
     *
     * @return Array()
     * @access public
     */
    public function get_at_available_field_num($num){
        foreach(self::$configs[$this->config_filename]['available_fields'] as $field){
            if($field['num']==$num){
                return $field;
            }
        }
    }

    /**
     * Renvoie les informations sur le champ visé
     * en fonction du label
     *
     * @return Array()
     * @access public
     */
    public function get_at_available_field_class_name($class_name, $id_authperso_authority=0){
        $id_authperso=0;
        if($class_name=='vedette_authpersos' && $id_authperso_authority){
            $req="select authperso_authority_authperso_num from authperso_authorities,authperso where id_authperso=authperso_authority_authperso_num and id_authperso_authority=". $id_authperso_authority;
            $res = pmb_mysql_query($req);
            if(($r=pmb_mysql_fetch_object($res))) {
                $id_authperso=$r->authperso_authority_authperso_num;
            }
        }
        foreach(self::$configs[$this->config_filename]['available_fields'] as $field){
            if($field['class_name']==$class_name){
                if($id_authperso){
                    if($field['num']==($id_authperso+1000)){
                        return $field;
                    }
                }else {
                    return $field;
                }
            }
        }
    }

    /**
     * Renvoie les informations sur le champ visé
     * en fonction du type
     *
     * @return Array()
     * @access public
     */
    public function get_at_available_field_type($type){
        foreach(self::$configs[$this->config_filename]['available_fields'] as $field){
            if($field['type']==$type){
                return $field;
            }
        }
    }

    /**
     * Ajoute un élément dans le tableau de gestion des éléments
     *
     * @param vedette_element vedette_element élément à ajouter
     * @param string subdivision Subdivision à laquelle ajouter l'ordre
     * @param int position Position dans la subdivision
     *
     * @return void
     * @access public
     */
    public function add_element($vedette_element,$subdivision,$position){
        if(!$vedette_element || !$subdivision){
            return false;
        }

        $this->vedette_elements[$subdivision][$position]=$vedette_element;
        return true;
    }

    /**
     * Retourne les éléments présents dans une subdivision
     *
     * @param string $subdivision
     * @return array [position]vedette_element
     */
    public function get_at_elements_subdivision($subdivision){
        $at_elements_subdivision = '';
        if(isset($this->vedette_elements[$subdivision])) {
            $at_elements_subdivision = $this->vedette_elements[$subdivision];
        }
        return $at_elements_subdivision;
    }

    /**
     * Renvoie les informations d'une subdivision en fonction de son code
     * @param string $code Code de la subdivision
     * @return array Informations de la subdivision
     */
    public function get_at_subdivision_code($code) {
        foreach (self::$configs[$this->config_filename]['subdivisions'] as $subdivision) {
            if ($subdivision["code"] == $code) {
                return $subdivision;
            }
        }
        return array();
    }

    /**
     * Retourne les éléments
     *
     * @return array [subdivision][position]vedette_element
     */
    public function get_elements(){
        return $this->vedette_elements;
    }

    /**
     * Renvoie le nombre d'éléments dans la subdivision
     *
     * @return int
     * @access public
     */
    public function get_nb_elements_subdivision($subdivision){
        return count($this->vedette_elements[$subdivision]);
    }

    /**
     * remonte les données de la base de données
     *
     * @return void
     * @access public
     */
    public function read(){
        global $base_path;
        global $class_path;
        global $include_path;
        global $javascript_path;
        global $styles_path;
        if(!$this->id){
            return false;
        }

        $query = "select label, grammar from vedette where id_vedette = ".$this->id;
        $result = pmb_mysql_query($query);
        if ($result && pmb_mysql_num_rows($result)) {
            if ($vedette = pmb_mysql_fetch_object($result)) {
                $this->label = $vedette->label;
                $this->config_filename = $vedette->grammar;
            }
        }

        $this->set_config();

        $query='select object_type, object_id, subdivision, position, num_available_field from vedette_object where num_vedette = '.$this->id.' order by position';
        $result=pmb_mysql_query($query);
        if(!pmb_mysql_error() && pmb_mysql_num_rows($result)){
            while($element_from_database=pmb_mysql_fetch_object($result)){
                if ($element_from_database->object_id) {
                    $field=$this->get_at_available_field_num($element_from_database->num_available_field);
                    $vedette_element_class_name=$field['class_name'];
                    if(!$vedette_element_class_name || !file_exists($class_path."/vedette/".$vedette_element_class_name.".class.php")) continue;
                    require_once($class_path."/vedette/".$vedette_element_class_name.".class.php");
                    if (empty($field['params'])) {
                        $field['params'] = array();
                    }
                    $element = new $vedette_element_class_name($field["num"], $element_from_database->object_id, '', $field['params']);
                    $this->add_element($element, $element_from_database->subdivision, $element_from_database->position);
                }
            }
        }
    }

    /**
     * Enregistre une vedette composée
     *
     * @return void
     * @access public
     */
    public function save(){
        if ($this->check_value()) {
            if($this->id){
                $query='DELETE FROM vedette_object WHERE num_vedette='.$this->id;
                pmb_mysql_query($query);

                $query='UPDATE vedette set label = "'.addslashes($this->label).'" where id_vedette = '.$this->id;
                pmb_mysql_query($query);
            }else{
                $query='INSERT INTO vedette (label, grammar) values ("'.addslashes($this->label).'", "'.$this->config_filename.'")';
                pmb_mysql_query($query);
                $this->id=pmb_mysql_insert_id();
            }
            foreach ($this->vedette_elements as $subdivision=>$elements){
                foreach ($elements as $position=>$element){
                    $query = 'INSERT INTO vedette_object (object_type, object_id, num_vedette, subdivision, position, num_available_field) values ('.$element->get_type().', '.$element->get_db_id().', '.$this->id.', "'.$subdivision.'", '.$position.', '.$element->get_num_available_field().')';
                    pmb_mysql_query($query);
                }
            }
            return $this->id;
        }
        return 0;
    }

    public static function replace($object_type, $id, $by){
        $responsabilities = array();
        $query = 'select distinct link.num_object as reponsability_num, type_object as reponsability_type from vedette_object as obj, vedette_link as link WHERE obj.num_vedette=link.num_vedette and obj.object_type="'.$object_type.'" and obj.object_id='.$id;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            while($r = pmb_mysql_fetch_object($result)){
                $responsabilities[]=array(
                    'type' => $r->reponsability_type,
                    'id' => $r->reponsability_num
                );
            }
        }

        $query = 'UPDATE vedette_object SET object_id="'.$by.'" WHERE object_type="'.$object_type.'" and object_id='.$id;
        pmb_mysql_query($query);

        $query = 'select num_vedette from vedette_object WHERE object_type="'.$object_type.'" and object_id='.$by;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            while($r = pmb_mysql_fetch_object($result)){
                $vedette_id = $r->num_vedette;
                $vedette = new vedette_composee($vedette_id);

                if ($vedette->update_label()) {
                    $query = "update vedette set label = '".addslashes($vedette->get_label())."' where id_vedette = ".$vedette->get_id();
                    pmb_mysql_query($query);

                    vedette_link::update_objects_linked_with_vedette($vedette);
                }
            }
        }

        foreach ($responsabilities as $responsability ){
            switch($responsability['type']){
                case TYPE_NOTICE_RESPONSABILITY_PRINCIPAL:
                case TYPE_NOTICE_RESPONSABILITY_AUTRE:
                case TYPE_NOTICE_RESPONSABILITY_SECONDAIRE:
                    $query='select responsability_notice from responsability WHERE id_responsability='.$responsability['id'];
                    $result=pmb_mysql_query($query);
                    if(pmb_mysql_num_rows($result)){
                        $r=pmb_mysql_fetch_object($result);
                        notice::majNoticesTotal($r->responsability_notice);
                    }
                    break;
                case TYPE_TU_RESPONSABILITY:
                case TYPE_TU_RESPONSABILITY_INTERPRETER:
                    $query='select responsability_tu_num from responsability_tu WHERE id_responsability_tu='.$responsability['id'];
                    $result=pmb_mysql_query($query);
                    if(pmb_mysql_num_rows($result)){
                        $r=pmb_mysql_fetch_object($result);
                        titre_uniforme::update_index($r->responsability_tu_num);
                    }
                    break;
                case TYPE_CONCEPT_PREFLABEL:
                    //réindexation des notices indexés avec le concepts
                    $query = "select num_object from index_concept where type_object = 1 and num_concept = ".$responsability['id'];
                    $result = pmb_mysql_query($query);
                    if($result && pmb_mysql_num_rows($result)){
                        while($row = pmb_mysql_fetch_object($result)){
                            indexation_stack::push($row->num_object, TYPE_NOTICE, "concept");
                        }
                    }
                    break;
            }
        }
    }

    /**
     * Supprime les données de la base de données
     *
     * @return void
     * @access public
     */
    public function delete(){
        if(!$this->id){
            return false;
        }

        $query='DELETE FROM vedette_object WHERE num_vedette='.$this->id;
        pmb_mysql_query($query);

        $query='DELETE FROM vedette WHERE id_vedette='.$this->id;
        pmb_mysql_query($query);

        $query='DELETE FROM vedette_link WHERE num_vedette='.$this->id;
        pmb_mysql_query($query);

        return true;
    }

    /**
     * Lecture de la configuration xml
     */
    private function set_config(){
        global $include_path,$base_path,$charset;

        if (!isset(self::$configs[$this->config_filename])) {
            self::$configs[$this->config_filename] = array();

            if(file_exists($include_path.'/vedette/'.$this->config_filename.'_subst.xml')){
                $xmlFile = $include_path.'/vedette/'.$this->config_filename.'_subst.xml';
            }elseif(file_exists($include_path.'/vedette/'.$this->config_filename.'.xml')){
                $xmlFile =$include_path.'/vedette/'.$this->config_filename.'.xml';
            }else{
                //pas de fichier à analyser
                return false;
            }

            $fileInfo = pathinfo($xmlFile);
            $tempFile = $base_path."/temp/XML".preg_replace("/[^a-z0-9]/i","",$fileInfo['dirname'].$fileInfo['filename'].$charset).".tmp";

            if (!file_exists($tempFile) || filemtime($xmlFile) > filemtime($tempFile)) {
                //Le fichier XML original a-t-il été modifié ultérieurement ?
                //on va re-générer le pseudo-cache
                if(file_exists($tempFile)){
                    unlink($tempFile);
                }
                //Parse le fichier dans un tableau
                $fp=fopen($xmlFile,"r") or die("Can't find XML file $xmlFile");
                $xml=fread($fp,filesize($xmlFile));
                fclose($fp);
                $xml_2_analyze=_parser_text_no_function_($xml, 'COMPOSED_HEADINGS');

                self::$configs[$this->config_filename]['available_fields'] = array_map("self::clean_read_xml", $xml_2_analyze['AVAILABLE_FIELDS'][0]['FIELD']);
                self::$configs[$this->config_filename]['subdivisions'] = array_map("self::clean_read_xml", $xml_2_analyze['HEADING'][0]['SUBDIVISION']);
                self::$configs[$this->config_filename]['separator'] = $xml_2_analyze['SEPARATOR'][0]['value'];

                // On interprète les paramètres éventuels
                foreach(self::$configs[$this->config_filename]['available_fields'] as &$available_field) {
                    if (!empty($available_field['params'])) {
                        $available_field['params'] = encoding_normalize::json_decode($available_field['params'], true);
                    }
                }

                $tmp = fopen($tempFile, "wb");
                fwrite($tmp,serialize(self::$configs[$this->config_filename]));
                fclose($tmp);
            } else if (file_exists($tempFile)){
                $tmp = fopen($tempFile, "r");
                self::$configs[$this->config_filename] = unserialize(fread($tmp,filesize($tempFile)));
                fclose($tmp);
            }
            $this->build_authpersos();
            $this->build_ontologies();
            $this->set_default_fields();
        }
        return true;
    }

    private static function clean_read_xml($array){
        $return=array();
        foreach ($array as $key=>$val){
            if($val){
                $return[strtolower($key)]=$val;
            }
        }
        return $return;
    }

    /**
     * Retourne le label de la vedette composée
     */
    public function get_label() {
        return $this->label;
    }

    /**
     * Retourne l'identifiant de la vedette composée
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * renseigne l'identifiant de la vedette composée
     */
    public function set_id($id) {
        $this->id = intval($id);
        return;
    }

    /**
     * Retourne le separateur du label de la vedette composée
     */
    public function get_separator() {
        return self::$configs[$this->config_filename]['separator'];
    }

    /**
     * Vérifie la validité de la vedette composée
     */
    protected function check_value() {
        foreach (self::$configs[$this->config_filename]['subdivisions'] as $subdivision) {
            $nb_elements = 0;
            if (isset($this->vedette_elements[$subdivision["code"]]) && count($this->vedette_elements[$subdivision["code"]])) {
                foreach ($this->vedette_elements[$subdivision["code"]] as $element) {
                    if ($element->get_id() && $element->get_isbd()) {
                        $nb_elements++;
                    }
                }
            }
            if (isset($subdivision["min"]) && $subdivision["min"] && ($subdivision["min"] > $nb_elements)) {
                return false;
            }
            if (isset($subdivision["max"]) && $subdivision["max"] && ($subdivision["max"] < $nb_elements)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Setter du label
     * @param string $label
     */
    public function set_label($label) {
        $this->label = $label;
    }

    /**
     * Vide le tableau des éléments
     */
    public function reset_elements() {
        $this->vedette_elements = array();
    }

    /**
     * Met à jour les vedettes contenant l'élément
     * @param int $element_id Identifiant en base de l'élément
     * @param string $element_type Type de l'élément
     */
    public static function update_vedettes_built_with_element($element_id, $element_type) {
        $vedettes_id = self::get_vedettes_built_with_element($element_id, $element_type);
        foreach ($vedettes_id as $vedette_id) {
            $vedette = new vedette_composee($vedette_id);
            if ($vedette->update_label()) {
                $query = "update vedette set label = '".addslashes($vedette->get_label())."' where id_vedette = ".$vedette->get_id();
                pmb_mysql_query($query);

                vedette_link::update_objects_linked_with_vedette($vedette);
            }
        }
    }

    public static function get_vedettes_display($vedettes_id) {
        $diplay = '';
        foreach ($vedettes_id as $vedette_id) {
            $responsabilities = array();
            $query = 'select distinct link.num_object as reponsability_num, type_object as reponsability_type from vedette_link as link
					WHERE link.num_vedette='.$vedette_id;

            $result = pmb_mysql_query($query);
            if(pmb_mysql_num_rows($result)){
                while($r = pmb_mysql_fetch_object($result)){
                    $responsabilities[] = array(
                        'type' => $r->reponsability_type,
                        'id' => $r->reponsability_num
                    );
                }
            }
            $vedette = new vedette_composee($vedette_id);
            $diplay.= $vedette->get_label() .'<br>';
            foreach ($responsabilities as $responsability ){
                switch($responsability['type']){
                    case TYPE_NOTICE_RESPONSABILITY_PRINCIPAL:
                    case TYPE_NOTICE_RESPONSABILITY_AUTRE:
                    case TYPE_NOTICE_RESPONSABILITY_SECONDAIRE:
                        $query = 'select responsability_notice from responsability WHERE id_responsability='.$responsability['id'];
                        $result = pmb_mysql_query($query);
                        if(pmb_mysql_num_rows($result)){
                            $r = pmb_mysql_fetch_object($result);
                            $diplay.= notice::get_notice_view_link($r->responsability_notice)."<br>";
                        }
                        break;
                    case TYPE_TU_RESPONSABILITY:
                    case TYPE_TU_RESPONSABILITY_INTERPRETER:
                        $query = 'select responsability_tu_num from responsability_tu WHERE id_responsability_tu='.$responsability['id'];
                        $result = pmb_mysql_query($query);
                        if(pmb_mysql_num_rows($result)){
                            $r = pmb_mysql_fetch_object($result);
                            $tu = new titre_uniforme($r->responsability_tu_num);
                            $diplay.= "<a href='./autorites.php?categ=see&sub=titre_uniforme&id=".$r->responsability_tu_num."' class='lien_gestion'>".$tu->get_isbd()."</a><br>";
                        }
                        break;
                    case TYPE_CONCEPT_PREFLABEL:
                        //notices indexés avec le concepts
                        $query = "select num_object from index_concept where type_object = 1 and num_concept = ".$responsability['id'];
                        $result = pmb_mysql_query($query);
                        if($result && pmb_mysql_num_rows($result)){
                            while($r = pmb_mysql_fetch_object($result)){
                                $diplay.= notice::get_notice_view_link($r->num_object)."<br>";
                            }
                        }
                        break;
                }
            }
        }
        return $diplay;
    }

    /**
     * Supprime un élement dans les vedettes et met à jour le label des vedettes concernées
     * @param int $element_id Identifiant en base de l'élément
     * @param int $element_type Type de l'élément
     */
    public static function delete_element_and_update_vedettes_built_with_element($element_id, $element_type) {
        $element_id = intval($element_id);
        $element_type = intval($element_type);

        $query = "SELECT num_vedette FROM vedette_object WHERE object_type = $element_type and object_id = $element_id";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $query = "DELETE FROM vedette_object WHERE object_type = $element_type and object_id = $element_id";
            pmb_mysql_query($query);
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $vedette = new vedette_composee($row["num_vedette"]);
                $vedette->update_label();

                $query = "update vedette set label = '".$vedette->get_label()."' where id_vedette = ".$vedette->get_id();
                pmb_mysql_query($query);

                vedette_link::update_objects_linked_with_vedette($vedette);
            }
        }
    }

    public static function get_element_type_num_from_type_str($vedette,$element_type) {
        // On récupère l'identifiant lié au type d'élément
        $element_type_num=0;
        if($element_type>1000){	// authperso
            $element_type_num = $element_type;
        }else{
            foreach($vedette->get_available_fields() as $field){
                if($field["type"] == $element_type){
                    $element_type_num = $field["num"];
                    break;
                }
            }
        }
        return $element_type_num;
    }

    /**
     * Retourne un tableau des identifiants des vedettes contenant l'élément
     * @param int $element_id Identifiant en base de l'élément
     * @param string $element_type Type de l'élément
     *
     * @return array Tableau des identifiants des vedettes
     */
    public static function get_vedettes_built_with_element($element_id, $element_type) {
        $vedettes_id = array();

        $grammars = vedette_grammars::get_grammars();
        foreach ($grammars as $grammar) {
            // On va chercher en base les vedettes contenant cet élément
            $query = "select distinct num_vedette from vedette_object inner join vedette on num_vedette = id_vedette where object_id = ".$element_id." and object_type = ".$element_type." and grammar = '".$grammar."'";
            $result = pmb_mysql_query($query);
            if ($result && pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_object($result)) {
                    $vedettes_id[] = $row->num_vedette;
                }
            }
        }
        return $vedettes_id;
    }

    /**
     * Retourne un tableau des identifiants des vedettes contenant les éléments passés en paramètres
     * @param int $elements Tableau des identifiants en base des éléments
     * @param string $grammar Grammaire de l'élément à récupérer
     * @param boolean $exact_match Spécifie si on veut retourner les vedettes composées uniquement des éléments transmis
     * @return array Tableau des identifiants des vedettes retrouvées
     */
    public static function get_vedettes_built_with_elements($elements, $grammar = "", $exact_match = false) {
        $vedettes_id = array();
        $grammars = array();
        if($grammar){
            $grammars[] = $grammar;
        }else{
            $grammars = vedette_grammars::get_grammars();
        }
        foreach($grammars as $grammar){
            $subquery = '';
            foreach($elements as $element){
                // On va chercher en base les vedettes contenant cet élément
                $query = "select distinct num_vedette from vedette_object inner join vedette on num_vedette = id_vedette
						where object_id = ".$element['id']." and object_type = ".$element['type']." and grammar = '".$grammar."'";
                if ($subquery) {
                    $query.= " and num_vedette in (".$subquery.")";
                }
                $subquery = $query;
            }

            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_assoc($result)) {
                    $vedettes_id[] = $row['num_vedette'];
                }
            }
        }
        if (count($vedettes_id) && $exact_match) {
            $query = 'select num_vedette from vedette_object where num_vedette in ('.implode(',', $vedettes_id).') group by num_vedette having count(*) = '.count($elements);
            $vedettes_id = array();

            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_assoc($result)) {
                    $vedettes_id[] = $row['num_vedette'];
                }
            }
        }
        return $vedettes_id;
    }

    /**
     * Recalcule le label de la vedette
     * @return boolean True si le libellé est modifié, false sinon
     */
    public function update_label() {
        // On trie le tableau des éléments
        $this->sort_vedette_elements();

        $label = "";

        foreach ($this->vedette_elements as $elements){
            /* @var $element vedette_element */
            foreach ($elements as $element){
                if ($label) $label .= self::$configs[$this->config_filename]['separator'];
                $label .= $element->get_isbd();
            }
        }
        if ($this->label != $label) {
            $this->label = $label;
            return true;
        }
        return false;
    }

    /**
     * Trie le tableau des éléments selon l'ordre des subdivisions
     */
    private function sort_vedette_elements() {
        $sort_array = array();

        // On crée un tableau contenant les ordres
        foreach ($this->vedette_elements as $subdivision_code => $elements) {
            $subdivision = $this->get_at_subdivision_code($subdivision_code);
            $sort_array[] = $subdivision["order"];
        }

        // On trie le tableau des vedettes par rapport au tableau des ordres
        array_multisort($sort_array, $this->vedette_elements, SORT_NUMERIC);
    }

    protected function build_authpersos(){
        $authorities = array();
        for($i=0 ; $i<count(self::$configs[$this->config_filename]['available_fields']) ; $i++){
            if(self::$configs[$this->config_filename]['available_fields'][$i]['type'] == 'authperso'){
                $infos = self::$configs[$this->config_filename]['available_fields'][$i];
                unset(self::$configs[$this->config_filename]['available_fields'][$i]);
                $authpersos=authpersos::get_instance();
                foreach($authpersos->info as $authority){
                    $authorities[] = array(
                        'num' => (string)($infos['num']+$authority['id']),
                        'name' => $authority['name'],
                        'class_name' => "vedette_authpersos",
                        'type' => "authperso".$authority['id'],
                        'params' => array(
                            'id_authority'=> $authority['id'],
                            'label' => $authority['name']
                        )
                    );
                }
                break;
            }
        }
        self::$configs[$this->config_filename]['available_fields']=array_merge(self::$configs[$this->config_filename]['available_fields'],$authorities);
    }

    protected function build_ontologies(){
        $ontologies = array();
        for($i=0 ; $i<count(self::$configs[$this->config_filename]['available_fields']) ; $i++){
            if(self::$configs[$this->config_filename]['available_fields'][$i]['type'] == 'ontologie'){
                $infos = self::$configs[$this->config_filename]['available_fields'][$i];
                unset(self::$configs[$this->config_filename]['available_fields'][$i]);
                $tmp = new ontologies();
                $ontos = $tmp->looking_for_use_in_concepts();
                foreach($ontos as $onto){
                    $ontologies[] = array(
                        'num' => (string)($infos['num']+$onto['id']),
                        'name' => $onto['name'],
                        'class_name' => "vedette_ontologies",
                        'type' => "ontology".$onto['id'],
                        'params' => array(
                            'num' => (string)($infos['num']+$onto['id']),
                            'id_ontology'=> $onto['ontology_id'],
                            'label' => $onto['name'],
                            'pmbname' => $onto['pmbname']
                        )
                    );
                }
                break;
            }
        }
        self::$configs[$this->config_filename]['available_fields']=array_merge(self::$configs[$this->config_filename]['available_fields'],$ontologies);
    }

    /**
     * Retourne l'identifiant de la vedette liée à un objet
     * @param int $object_id Identifiant de l'objet
     * @param int $object_type Type de l'objet
     * @return int Identifiant de la vedette liée
     */
    public static function get_vedette_id_from_object($object_id, $object_type) {
    	$object_id = intval($object_id);
        if ($object_id) {
            $query = "select num_vedette from vedette_link where num_object = ".$object_id." and type_object = ".$object_type;
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                return pmb_mysql_result($result, 0, 0);
            }
        }
        return 0;
    }


    /**
     * Retourne la liste des identifiants de vedette liée à un tableau
     * @param array $ids Identifiant de l'objet
     * @param int $object_type Type de l'objet
     * @return array Tableau d'indentifiant de la vedette liée
     */
    public static function get_vedette_id_from_array ($ids, $object_type) {
    	if(!empty($ids) && is_array($ids)) {
	        $query = "select num_vedette, num_object from vedette_link where num_object IN (" . implode(",", $ids) . ") and type_object = " . $object_type;
	        $result = pmb_mysql_query($query);
	        if (pmb_mysql_num_rows($result)) {
	            $return = array();
	            while ($row = pmb_mysql_fetch_object($result)) {
	                $return[$row->num_object] = $row->num_vedette;
	            }
	            return $return;
	        }
    	}
        return [];
    }

    /**
     * Retourne l'identifiant de l'objet lié à la vedette
     * @param int $object_id Identifiant de la vedette
     * @param int $object_type Type de l'objet
     * @return int Identifiant de l'objet lié
     */
    public static function get_object_id_from_vedette_id($vedette_id, $object_type) {
        $query = "select num_object from vedette_link where num_vedette = ".$vedette_id." and type_object = ".$object_type;
        $result = pmb_mysql_query($query);
        if ($result && pmb_mysql_num_rows($result)) {
            if ($row = pmb_mysql_fetch_object($result)) {
                return $row->num_object;
            }
        }
        return 0;
    }

    public function get_subdivision_name_by_code($code) {
        global $msg;

        foreach (self::$configs[$this->config_filename]['subdivisions'] as $subdivision) {
            if ($subdivision["code"] == $code) {
                if (substr($subdivision['name'], 0, 4) == "msg:") {
                    if ($msg[substr($subdivision['name'], 4)]) {
                        return $msg[substr($subdivision['name'], 4)];
                    } else {
                        return substr($subdivision['name'], 4);
                    }
                } else if ($subdivision['name']) {
                    return $subdivision['name'];
                } else {
                    return $subdivision['code'];
                }
            }
        }
        return "";
    }

    public function get_config_filename() {
        return $this->config_filename;
    }

    protected function set_default_fields() {
        global $class_path;
        foreach (self::$configs[$this->config_filename]['subdivisions'] as $subdivision) {
            if (empty($subdivision['default_fields'])) {
                continue;
            }
            $default_fields_num = explode(',', $subdivision['default_fields']);
            foreach ($default_fields_num as $i => $default_field_num) {
                $default_field = $this->get_at_available_field_num($default_field_num);
                require_once($class_path."/vedette/".$default_field['class_name'].".class.php");
                if (empty($default_field['params'])) {
                    $default_field['params'] = array();
                }
                $element = new $default_field['class_name']($default_field["num"], 0, '', $default_field['params']);
                $this->add_element($element, $subdivision['code'], $i);
            }
        }
    }

    /**
     * Méthode remplissant la vedette via les données enregistrées
     *
     * @param object|string $formated_value
     */
    public function feed($qualification) {
        global $base_path, $class_path;


        $this->label = '';
        if (!empty($qualification->apercu_vedette)) {
            $this->label = $qualification->apercu_vedette;
        }

        if (!empty($qualification->elements)) {
            foreach ($qualification->elements as $role => $elements) {
                foreach ($elements as $position => $element) {
                    $field = $this->get_at_available_field_num($element->available_field_num);

                    $vedette_element_class_name = $field['class_name'];
                    if (empty($vedette_element_class_name) || !file_exists("$class_path/vedette/$vedette_element_class_name.class.php")) {
                        continue;
                    }

                    require_once "$class_path/vedette/$vedette_element_class_name.class.php";
                    if (empty($field['params'])) {
                        $field['params'] = [];
                    }

                    $element = new $vedette_element_class_name($field['num'], $element->id, '', $field['params']);
                    $this->add_element($element, $role, $position);
                }
            }
        }
    }
    public function get_vedette_elements() {
    	return $this->vedette_elements;
    }
}