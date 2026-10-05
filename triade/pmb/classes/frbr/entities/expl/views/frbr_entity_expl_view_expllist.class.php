<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_expl_view_expllist.class.php,v 1.2 2021/08/09 13:48:24 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/author.class.php");
require_once($class_path."/expl_data.class.php");

class frbr_entity_expl_view_expllist extends frbr_entity_common_view_django {	
	
	public function __construct($id=0){
	    parent::__construct($id);
	    $this->default_template = "<h3>{{title}}</h3>
{% for elt in expl %}
{% if elt.id_notice %} 
<a href='./index.php?lvl=notice_display&id={{elt.id_notice}}'>{{elt.notice_title}} - {{elt.cb}}</a>
{% endif %}
{% if elt.id_bulletin %} 
<a href='./index.php?lvl=bulletin_display&id={{elt.id_bulletin}}'>{{elt.notice_title}} - {{elt.cb}}</a>
{% endif %}
<br>
{% endfor %}";
    }
    
    public function render($datas, $grouped_datas = []){
        global $base_path;
        global $id;
      
        $render_datas = array();
        $template_path = $base_path . '/temp/'.LOCATION.'_frbr_entity_expl_view_expllist_' . $this->id;
        if (!file_exists($template_path) || (md5($this->parameters->active_template) != md5_file($template_path))) {
            file_put_contents($template_path, $this->parameters->active_template);
        }        
        $title = '';
        if (is_array($datas)) {            
            $query = "SELECT titre FROM expl_custom
                LEFT JOIN expl_custom_values ON expl_custom_champ = idchamp
                WHERE expl_custom_origine=" . $datas[0] . " AND expl_custom_integer = $id";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $row = pmb_mysql_fetch_object($result);
                $title = $row->titre;
            }            
            foreach ($datas as $expl_id) {
                $render_datas[] = expl_data::get_record_data($expl_id); 
            }
        }
        $h2o = H2o_collection::get_instance($template_path);
        return $h2o->render(array(
            'expl' => $render_datas,
            'title' => $title
        ));
    }    
      
    public function get_format_data_structure(){
        $format = array();
        $format[] = array(
            'var' => "title",
            'desc' => $this->msg['frbr_entity_expl_view_title']
        );
        $expl = array(
            'var' => "expl",
            'desc' => $this->msg['frbr_entity_expl_view_expl_desc'],
            'children' => expl_data::get_properties('expl[i]')
        );
        $format[] = $expl;
        $format = array_merge($format,parent::get_format_data_structure());
        return $format;
    }
    
    public function save_form(){
        global $frbr_entity_records_view_django_directory;
        
        if (isset($frbr_entity_records_view_django_directory)) {
            $this->parameters->django_directory = stripslashes($frbr_entity_records_view_django_directory);
        }
        return parent::save_form();
    }
    
}