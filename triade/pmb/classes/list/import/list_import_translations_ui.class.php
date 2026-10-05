<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_import_translations_ui.class.php,v 1.1.2.3 2024/12/19 14:48:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_import_translations_ui extends list_import_ui {
	
    protected $primary_keys = [];
    
    protected function has_filter_states($row) {
        if (empty($this->filters['states'])) {
            return true;
        }
        $deduplication_state = $this->_get_object_property_deduplication_state($row);
        if (in_array($deduplication_state, $this->filters['states'])) {
            return true;
        }
        return false;
    }
    
    protected function add_object($row) {
        $row->id = $row->trans_table."_".$row->trans_field."_".$row->trans_lang."_".$row->trans_num;
        if (empty($this->primary_keys[$row->trans_table])) {
            $query = "SHOW KEYS FROM ".addslashes($row->trans_table)." WHERE Key_name = 'PRIMARY'";
            $result = pmb_mysql_query($query);
            $this->primary_keys[$row->trans_table] = pmb_mysql_result($result, 0, 'Column_name');
        }
        $row->trans_primary_key = $this->primary_keys[$row->trans_table];
        
        $row->translation_from = list_translations_ui::get_translation_from($row);
        if (empty($row->translation_to)) {
            $row->translation_to = ($row->trans_small_text ? $row->trans_small_text : $row->trans_text);
        }
        if ($this->has_filter_states($row)) {
            $this->objects[] = $row;
        }
    }
	
    protected function init_available_columns() {
        parent::init_available_columns();
        if (!empty($this->available_columns['main_fields'])) {
            $position   = array_search('trans_lang', array_keys($this->available_columns['main_fields']));
            $this->available_columns['main_fields'] = array_merge(
                array_slice($this->available_columns['main_fields'], 0, $position),
                ['translation_from' => 'translation_from'],
                array_slice($this->available_columns['main_fields'], $position)
            );
        }
    }
    
    protected function init_default_columns() {
        parent::init_default_columns();
    }
    
    protected function _get_object_property_deduplication_state($object) {
        if (empty($object->deduplication_state)) {
            if ($object->translation_from) {
                $translated_text = translation::get_translated_text($object->trans_num, $object->trans_table, $object->trans_field, '', $object->trans_lang);
                if(empty($translated_text)) {
                    $object->deduplication_state = 'new';
                } else {
                    if(strcmp($translated_text, $object->translation_to) == 0) {
                        $object->deduplication_state = 'same';
                    } else {
                        $object->deduplication_state = 'replace';
                    }
                }
            } else {
                $object->deduplication_state = 'unknown';
            }
        }
        return $object->deduplication_state;
    }
	
    protected function import_object($object) {
        if ($object->translation_from) {
            $translation = new translation($object->trans_num, $object->trans_table);
            $data = $translation->get_data();
            if (isset($data[$object->trans_field][$object->trans_lang])) {
                $query = "delete from translation 
                    WHERE trans_num='".$object->trans_num."' 
                    AND trans_table='".$object->trans_table."'
                    AND trans_field='".$object->trans_field."'
                    AND trans_lang='".$object->trans_lang."'";
                pmb_mysql_query($query);
            }
            if ($object->trans_small_text != '') {
                $translation->save($object->trans_field, $object->trans_lang, 'small_text', $object->trans_small_text);
            } else {
                $translation->save($object->trans_field, $object->trans_lang, 'text', $object->trans_text);
            }
        }
    }
    
	public function get_primary_keys() {
	    return $this->primary_keys;
	}
}