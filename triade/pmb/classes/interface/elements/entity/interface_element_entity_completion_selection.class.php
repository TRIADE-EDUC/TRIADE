<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_element_entity_completion_selection.class.php,v 1.1.2.3 2025/03/14 08:57:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_element_entity_completion_selection extends interface_element_completion_selection {
	
    protected function init_openPopUpUrl() {
        $this->openPopUpUrl = './select.php?what='.$this->what.'&caller='.$this->caller;
        switch ($this->what) {
            case 'auteur':
                $this->openPopUpUrl .= '&param1=!!hidden_name!!&param2=!!name!!';
                break;
            case 'categorie':
                $this->openPopUpUrl .= '&p1=!!hidden_name!!&p2=!!name!!';
                $this->openPopUpUrl .= '&autoindex_class=autoindex_record&indexation_lang=!!indexation_lang_sel!!&dyn=1&parent=0';
                break;
            case 'collection':
                $this->openPopUpUrl .= '&p1=!!hidden_name!!&p2=!!name!!&p3=f_coll_id&p4=f_coll&p5=f_subcoll_id&p6=f_subcoll';
                break;
            case 'editeur':
                $this->openPopUpUrl .= '&p1=!!hidden_name!!&p2=!!name!!&p3=f_coll_id&p4=f_coll&p5=f_subcoll_id&p6=f_subcoll';
                break;
            case 'function':
                $this->openPopUpUrl .= '&p1=!!hidden_name!!&p2=!!name!!';
                break;
            case 'indexint':
                $this->openPopUpUrl .= '&param1=!!hidden_name!!&param2=!!name!!';
                break;
            case 'lang':
                $this->openPopUpUrl .= '&p1=!!hidden_name!!&p2=!!name!!';
                break;
            case 'music_form':
                $this->openPopUpUrl .= '&p1=!!hidden_name!!&p2=!!name!!';
                break;
            case 'music_key':
                $this->openPopUpUrl .= '&p1=!!hidden_name!!&p2=!!name!!';
                break;
            case 'oeuvre_event':
                $this->openPopUpUrl .= '&field_id=!!hidden_name!!&field_name_id=!!name!!';
                $this->openPopUpUrl .= '&dyn=3&max_field=max_oeuvre_event&add_field=add_oeuvre_event&param1=!!type!!';
                break;
            case 'serie':
                $this->openPopUpUrl .= '&param1=!!hidden_name!!&param2=!!name!!';
                break;
            case 'subcollection':
                $this->openPopUpUrl .= '&p1=!!hidden_name!!&p2=!!name!!&p3=f_coll_id&p4=f_coll&p5=f_subcoll_id&p6=f_subcoll';
                break;
            case 'titre_uniforme':
                
                break;
        }
    }
    
    public function init_default_properties($name) {
        $this->what = $name;
        switch ($this->what) {
            case 'auteur':
                $this->completion = 'authors';
                break;
            case 'categorie':
                $this->completion = 'categories_mul';
                break;
            case 'collection':
                $this->completion = 'collections';
                $this->callback = 'f_coll_id_callback';
                // linkfield='f_ed1_id'
                break;
            case 'editeur':
                $this->completion = 'publishers';
                $this->callback = 'f_ed1_id_callback';
                break;
            case 'function':
                $this->completion = 'fonction';
                break;
            case 'indexint':
                $this->completion = 'indexint';
                // typdoc=\"typdoc\"
                break;
            case 'lang':
                $this->completion = 'langue';
                $this->repeatable = true;
                $this->selector_function = 'fonction_selecteur_lang';
                break;
            case 'music_form':
                $this->completion = 'music_form';
                break;
            case 'music_key':
                $this->completion = 'music_key';
                break;
            case 'oeuvre_event':
                $this->completion = 'oeuvre_event';
//                 $this->param1 = '!!oeuvre_event_type_value!!';
                break;
            case 'serie':
                $this->completion = 'serie';
                break;
            case 'subcollection':
                $this->completion = 'subcollections';
                $this->callback = 'f_subcoll_id_callback';
                // linkfield='f_coll_id'
                break;
            case 'titre_uniforme':
                $this->completion = 'titre_uniforme';
                break;
        }
        return $this;
    }
}