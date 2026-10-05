<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: aut_link.class.php,v 1.26 2023/07/26 15:07:58 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
// gestion des liens entre autorités

global $class_path;
require_once("$class_path/marc_table.class.php");
require_once("$class_path/author.class.php");
require_once("$class_path/publisher.class.php");
require_once("$class_path/collection.class.php");
require_once("$class_path/subcollection.class.php");
require_once("$class_path/indexint.class.php");
require_once("$class_path/serie.class.php");
require_once("$class_path/category.class.php");
require_once("$class_path/titre_uniforme.class.php");
require_once("$class_path/authperso.class.php");
require_once("$class_path/authorities_collection.class.php");
//require_once("$class_path/concept.class.php");


//require_once($include_path."/templates/aut_link.tpl.php");

define('AUT_TABLE_AUTHORS',1);
define('AUT_TABLE_CATEG',2);
define('AUT_TABLE_PUBLISHERS',3);
define('AUT_TABLE_COLLECTIONS',4);
define('AUT_TABLE_SUB_COLLECTIONS',5);
define('AUT_TABLE_SERIES',6);
define('AUT_TABLE_TITRES_UNIFORMES',7);
define('AUT_TABLE_INDEXINT',8);
define('AUT_TABLE_AUTHPERSO',9);
define('AUT_TABLE_CONCEPT',10);
define('AUT_TABLE_INDEX_CONCEPT',11);
// Pour la classe authorities_collection
define('AUT_TABLE_CATEGORIES',12);
define('AUT_TABLE_AUTHORITY',13);
define('AUT_TABLE_ANIMATION',14);

// définition de la classe de gestion des liens entre autorités
class aut_link {
    
    protected $aut_link_xml;
    public $aut_table;
    public $id;
    protected $js_aut_link_table_list = ''; // nécesaire pour les aut perso..
    protected $aut_list;
    
    public function __construct($aut_table,$id) {
        $this->aut_table = $aut_table;
        $this->id = intval($id);
        $this->getdata();
    }
    
    public function getdata() {
        global $msg;
        global $pmb_opac_url;
        $this->parse_file();
        
        $rqt="select * from aut_link where (aut_link_from='".$this->aut_table."' and aut_link_from_num='".$this->id."')
		order by aut_link_type, aut_link_string_start_date, aut_link_string_end_date, aut_link_rank";
        $aut_res=pmb_mysql_query($rqt);
        $i=0;
        while ($row = pmb_mysql_fetch_object($aut_res)) {
            $i++;
            $this->aut_list[$i]['to'] = $row->aut_link_to;
            $this->aut_list[$i]['to_num'] = $row->aut_link_to_num;
            $this->aut_list[$i]['type'] = $row->aut_link_type;
            $this->aut_list[$i]['comment'] = $row->aut_link_comment;
            $this->aut_list[$i]['string_start_date'] = $row->aut_link_string_start_date;
            $this->aut_list[$i]['string_end_date'] = $row->aut_link_string_end_date;
            $this->aut_list[$i]['start_date'] = $row->aut_link_start_date;
            $this->aut_list[$i]['end_date'] = $row->aut_link_end_date;
            $this->aut_list[$i]['rank'] = $row->aut_link_rank;
            $this->aut_list[$i]['direction'] = $row->aut_link_direction;
            $this->aut_list[$i]['reverse_link_num'] = $row->aut_link_reverse_link_num;
            
            if($this->aut_list[$i]['reverse_link_num']) {
                $this->aut_list[$i]['flag_reciproc'] = 1;
            } else {
                $this->aut_list[$i]['flag_reciproc'] = 0;
            }
            
            switch($this->aut_list[$i]['to']){
                case AUT_TABLE_AUTHORS :
                    $auteur = authorities_collection::get_authority("author", $this->aut_list[$i]['to_num']);
                    $this->aut_list[$i]['isbd_entry'] = $auteur->get_isbd();
                    $this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_author'] ,$auteur->get_isbd());
                    break;
                case AUT_TABLE_CATEG :
                    $categ = authorities_collection::get_authority("category", $this->aut_list[$i]['to_num']);
                    $this->aut_list[$i]['isbd_entry'] = $categ->libelle;
                    $this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_categ'], $categ->libelle);
                    break;
                case AUT_TABLE_PUBLISHERS :
                    $ed = authorities_collection::get_authority("publisher", $this->aut_list[$i]['to_num']);
                    $this->aut_list[$i]['isbd_entry'] = $ed->get_isbd();
                    $this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_publisher'] ,$ed->get_isbd());
                    break;
                case AUT_TABLE_COLLECTIONS :
                    $collection = authorities_collection::get_authority("collection", $this->aut_list[$i]['to_num']);
                    $this->aut_list[$i]['isbd_entry'] = $collection->get_isbd();
                    $this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_coll'], $collection->get_isbd());
                    break;
                case AUT_TABLE_SUB_COLLECTIONS :
                    $subcollection = authorities_collection::get_authority("subcollection", $this->aut_list[$i]['to_num']);
                    $this->aut_list[$i]['isbd_entry'] = $subcollection->get_isbd();
                    $this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_subcoll'], $subcollection->get_isbd());
                    break;
                case AUT_TABLE_SERIES :
                    $serie = authorities_collection::get_authority("serie", $this->aut_list[$i]['to_num']);
                    $this->aut_list[$i]['isbd_entry'] = $serie->get_isbd();
                    $this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_serie'], $serie->get_isbd());
                    break;
                case AUT_TABLE_TITRES_UNIFORMES :
                    $tu = authorities_collection::get_authority("titre_uniforme", $this->aut_list[$i]['to_num']);
                    $this->aut_list[$i]['isbd_entry'] = $tu->get_isbd();
                    $this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_tu'], $tu->get_isbd());
                    break;
                case AUT_TABLE_INDEXINT :
                    $indexint = authorities_collection::get_authority("indexint", $this->aut_list[$i]['to_num']);
                    $this->aut_list[$i]['isbd_entry'] = $indexint->get_isbd();
                    $this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_indexint'], $indexint->get_isbd());
                    break;
                case AUT_TABLE_CONCEPT :
                    $concept= authorities_collection::get_authority("concept", $this->aut_list[$i]['to_num']);
                    $this->aut_list[$i]['isbd_entry'] = $concept->get_display_label();
                    $this->aut_list[$i]['libelle'] =  $concept->get_display_label();
                    break;
                default:
                    if($this->aut_list[$i]['to']>1000){
                        // authperso
                        $authperso = new authperso($this->aut_list[$i]['to']-1000);
                        $isbd=authperso::get_isbd($this->aut_list[$i]['to_num']);
                        $this->aut_list[$i]['isbd_entry']=$isbd;
                        $this->aut_list[$i]['libelle']="[".$authperso->info['name']."] ".$isbd;
                        $this->aut_list[$i]['url_to_opac']=$pmb_opac_url."index.php?lvl=authperso_see&id=".$this->aut_list[$i]['to_num'];
                    }
                    break;
            }
            $relation = new marc_select("aut_link","f_aut_link_type$i", $this->aut_list[$i]['type']);
            $this->aut_list[$i]['relation_libelle'] = $relation->libelle;
        }
    }
    
    public function get_completion_table_name($table) {
        
        switch ($table) {
            case '1' :
                $table_name = 'authors';
                break;
            case '2' :
                $table_name =  'categories_mul';
                break;
            case '3' :
                $table_name = 'publishers';
                break;
            case '4' :
                $table_name = 'collections';
                break;
            case '5' :
                $table_name = 'subcollections';
                break;
            case '6' :
                $table_name = 'serie';
                break;
            case '7' :
                $table_name = 'titre_uniforme';
                break;
            case '8' :
                $table_name = 'indexint';
                break;
            case '10' :
                $table_name = 'onto';
                break;
            default :
                if ($table > 1000) {
                    $table_name = 'authperso_' . (intval($table) - 1000);
                }
                break;
        }
        return $table_name;
    }
    
    public function get_data() {
        return $this->aut_list;
    }
    
    public function get_display($caller="categ_form") {
        if (empty($this->aut_list)) return "";
        
        $aut_link_table_select = array();
        $aut_link_table_select[AUT_TABLE_AUTHORS]='./index.php?lvl=author_see&id=!!to_num!!';
        $aut_link_table_select[AUT_TABLE_CATEG]='./index.php?lvl=categ_see&id=!!to_num!!';
        $aut_link_table_select[AUT_TABLE_PUBLISHERS]='./index.php?lvl=publisher_see&id=!!to_num!!';
        $aut_link_table_select[AUT_TABLE_COLLECTIONS]='./index.php?lvl=coll_see&id=!!to_num!!';
        $aut_link_table_select[AUT_TABLE_SUB_COLLECTIONS]='./index.php?lvl=subcoll_see&id=!!to_num!!';
        $aut_link_table_select[AUT_TABLE_SERIES]='./index.php?lvl=serie_see&id=!!to_num!!';
        $aut_link_table_select[AUT_TABLE_TITRES_UNIFORMES]='./index.php?lvl=titre_uniforme_see&id=!!to_num!!';
        $aut_link_table_select[AUT_TABLE_INDEXINT]='./index.php?lvl=indexint_see&id=!!to_num!!';
        $aut_link_table_select[AUT_TABLE_CONCEPT]='./index.php?lvl=concept_see&id=!!to_num!!';
        $aut_link_table_select[AUT_TABLE_AUTHPERSO]='./index.php?lvl=authperso_see&id=!!to_num!!';
        
        $marc = marc_list_collection::get_instance("aut_link");
        $liste_type_relation = $marc->table;
        
        $aff="<ul>";
        foreach ($this->aut_list as $aut) {
            $aff.="<li>";
            if($aut['direction'] == 'up') {
                $aff.= $liste_type_relation['ascendant'][$aut['type']]." : ";
            } else	{
                $aff.= $liste_type_relation['descendant'][$aut['type']]." : ";
            }
            if($aut['to'] > 1000) {
                $link=str_replace("!!to_num!!",$aut['to_num'],$aut_link_table_select[AUT_TABLE_AUTHPERSO]);
            } else {
                $link=str_replace("!!to_num!!",$aut['to_num'],$aut_link_table_select[$aut['to']]);
            }
            $aff.=" <a href='".$link."'>".$aut['libelle']."</a>";
            $aff_dates = '';
            if ($aut['string_start_date']) {
                $aff_dates.= $aut['string_start_date'];
            }
            if ($aff_dates && $aut['string_end_date']) {
                $aff_dates.= ' - ';
            }
            if ($aut['string_end_date']) {
                $aff_dates.= $aut['string_end_date'];
            }
            if ($aff_dates && !$aut['comment']) {
                $aff.= " (" . $aff_dates . ")";
            }
            if($aut['comment']) {
                $aff.= " (" . $aff_dates . ' ' . $aut['comment'] . ")";
            }
            $aff.="</li>";
        }
        $aff.="</ul>";
        return $aff;
    }
    
    public function get_aut_list() {
        if (!isset($this->aut_list)) {
            return $this->getdata();
        }
        return $this->aut_list;
    }
    
    /**
     * Parse le fichier xml
     */
    private function parse_file() {
        global $base_path, $include_path, $charset;
        global $KEY_CACHE_FILE_XML;
        
        $filepath = $include_path."/authorities/aut_links_subst.xml";
        if (!file_exists($filepath)) {
            $filepath = $include_path."/authorities/aut_links.xml";
        }
        
        $fileInfo = pathinfo($filepath);
        $fileName = preg_replace("/[^a-z0-9]/i","",$fileInfo['dirname'].$fileInfo['filename'].$charset);
        $tempFile = $base_path."/temp/XML".$fileName.".tmp";
        $dejaParse = false;
        
        $cache_php=cache_factory::getCache();
        $key_file="";
        if ($cache_php) {
            $key_file=getcwd().$fileName.filemtime($filepath);
            $key_file=$KEY_CACHE_FILE_XML.md5($key_file);
            if($tmp_key = $cache_php->getFromCache($key_file)){
                if($cache = $cache_php->getFromCache($tmp_key)){
                    if(count($cache) == 1){
                        $this->aut_link_xml = $cache[0];
                        $dejaParse = true;
                    }
                }
            }
            
        }else{
            if (file_exists($tempFile) ) {
                //Le fichier XML original a-t-il été modifié ultérieurement ?
                if (filemtime($filepath) > filemtime($tempFile)) {
                    //on va re-générer le pseudo-cache
                    if($tempFile && file_exists($tempFile)){
                        unlink($tempFile);
                    }
                } else {
                    $dejaParse = true;
                }
            }
            if ($dejaParse) {
                $tmp = fopen($tempFile, "r");
                $cache = unserialize(fread($tmp,filesize($tempFile)));
                fclose($tmp);
                if(count($cache) == 1){
                    $this->aut_link_xml = $cache[0];
                }else{
                    //SOUCIS de cache...
                    if($tempFile && file_exists($tempFile)){
                        unlink($tempFile);
                    }
                    $dejaParse = false;
                }
            }
        }
        
        if(!$dejaParse){
            $fp=fopen($filepath,"r") or die("Can't find XML file");
            $size=filesize($filepath);
            
            $xml=fread($fp,$size);
            fclose($fp);
            $aut_links = _parser_text_no_function_($xml, "AUT_LINKS", $filepath);
            
            $this->aut_link_xml = array();
            $aut_def = array();
            foreach($aut_links['DEFINITION'][0]['ENTRY'] as $xml_aut_definition){
                $aut_def[$xml_aut_definition['CODE']] = $xml_aut_definition['value'];
            }
            
            /**
             * Le résultat du parse du fichier xml est stocké en temps que tableau sérialisé dans le fichier tempo
             */
            //Lecture des liens
            foreach ($aut_links['LINKS'][0]['AUTHORITY'] as $main_authority) {
                $aut_allowed = array();
                if($main_authority['AUTHORITY_ALLOWED']){
                    foreach($main_authority['AUTHORITY_ALLOWED'] as $sub_aut_allowed){
                        if(isset($aut_def[$sub_aut_allowed['value']])){
                            $aut_allowed[] = $aut_def[$sub_aut_allowed['value']];
                        }
                        
                    }
                }
                if(isset($aut_def[$main_authority['CODE']])){
                    $this->aut_link_xml[$aut_def[$main_authority['CODE']]]['aut_to_display'] = $aut_allowed;
                }
            }
            
            if ($key_file) {
                $key_file_content=$KEY_CACHE_FILE_XML.md5(serialize(array($this->aut_link_xml)));
                $cache_php->setInCache($key_file_content, array($this->aut_link_xml));
                $cache_php->setInCache($key_file,$key_file_content);
            }else{
                $tmp = fopen($tempFile, "wb");
                fwrite($tmp,serialize(array($this->aut_link_xml)));
                fclose($tmp);
            }
        }
    }
    
    public static function get_type_from_const($const){
        switch($const){
            case "1" :
                return "author";
            case "2" :
                return "category";
            case "3" :
                return "publisher";
            case "4" :
                return "collection";
            case "5" :
                return "subcollection";
            case "6" :
                return "serie";
            case "7" :
                return "titre_uniforme";
            case "8" :
                return "indexint";
            case "9" :
                return "authperso";
            case "10" :
                return "concept";
        }
    }
    
    public function generate_aut_type_selector($caller="categ_form", $aut_sel=0, $index=0){
        global $msg;
        global $thesaurus_concepts_active;
        global $form_aut_link_buttons, $pmb_aut_link_autocompletion;
        
        if ($pmb_aut_link_autocompletion) {
            $aut_table_list="<select class='aut_link_authorities_selector' id='f_aut_link_table_list_" . $index . "' name='f_aut_link_table_list_" . $index . "' onchange = 'onchange_aut_link_selector($index)'>";
        } else {
            $aut_table_list="<select class='aut_link_authorities_selector' id='f_aut_link_table_list' name='f_aut_link_table_list'>";
        }
        $options = '';
        //Cas à gérer pour les autorités persos
        $auth_type = ($this->aut_table <= 1000 ? $this->aut_table : 9);
        $first = 0;
        foreach($this->aut_link_xml[$auth_type]['aut_to_display'] as $aut_to_display){
            $selected = '';
            if ((!$aut_sel && !$first) || ($aut_to_display == $aut_sel)) {
                $selected = ' selected="selected" ';
            }
            $first = 1;
            switch($aut_to_display){
                case '1':
                    $options.= '<option value="'.AUT_TABLE_AUTHORS.'" ' . $selected. '>'.$msg["133"].'</option>';
                    break;
                case '2':
                    $options.= '<option value="'.AUT_TABLE_CATEG.'" ' . $selected. '>'.$msg['134'].'</option>';
                    break;
                case '3':
                    $options.= '<option value="'.AUT_TABLE_PUBLISHERS.'" ' . $selected. '>'.$msg['publisher'].'</option>';
                    break;
                case '4':
                    $options.= '<option value="'.AUT_TABLE_COLLECTIONS.'">'.$msg['136'].'</option>';
                    break;
                case '5':
                    $options.= '<option value="'.AUT_TABLE_SUB_COLLECTIONS.'" ' . $selected. '>'.$msg['137'].'</option>';
                    break;
                case '6':
                    $options.= '<option value="'.AUT_TABLE_SERIES.'" ' . $selected. '>'.$msg['333'].'</option>';
                    break;
                case '7':
                    $options.= '<option value="'.AUT_TABLE_TITRES_UNIFORMES.'" ' . $selected. '>'.$msg['aut_menu_titre_uniforme'].'</option>';
                    break;
                case '8':
                    $options.= '<option value="'.AUT_TABLE_INDEXINT.'" ' . $selected. '>'.$msg['indexation_decimale'].'</option>';
                    break;
                case '9':
                    $authpersos = authpersos::get_instance();
                    $info=$authpersos->get_data();
                    foreach($info as $elt){
                        $selected = '';
                        if ($pmb_aut_link_autocompletion) {
                            if(($elt['id'] + 1000) == $aut_sel) {
                                $selected = ' selected="selected" ';
                            }
                        }
                        $tpl_elt="<option value='!!id_authperso!!' " . $selected. ">!!name!!</option>";
                        $tpl_elt=str_replace('!!name!!',$elt['name'], $tpl_elt);
                        $tpl_elt=str_replace('!!id_authperso!!',$elt['id'] + 1000, $tpl_elt);
                        $this->js_aut_link_table_list.="aut_link_table_select[".($elt['id'] + 1000)."]='./select.php?what=authperso&authperso_id=".$elt['id']."&caller=$caller&dyn=2&param1=';";
                        $options.= $tpl_elt;
                    }
                    break;
                case '10':
                    if($thesaurus_concepts_active){
                        $options.= '<option value="'.AUT_TABLE_CONCEPT.'" ' . $selected. '>'.$msg['onto_common_concept'].'</option>';
                    }
                    break;
            }
        }
        if($options){
            $add_button = $form_aut_link_buttons;
            $add_button = str_replace("!!index!!", $index, $add_button);
            return $aut_table_list.$options.'</select>' . $add_button;
        }
        return '';
    }
    
    // fin class
}