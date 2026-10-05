<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interpreter_notice_tpl.class.php,v 1.1.4.2 2025/05/21 06:52:24 dgoron Exp $

global $include_path;

require_once ($include_path . '/misc.inc.php');

class interpreter_notice_tpl {
    
    public $param;
    
    public $id_notice = 0;
    
    public $notice = null;
    
    public function __construct($param) {
        global $parser_environnement;
        
        $this->param = $param;
        if(!empty($parser_environnement['id_notice'])) {
            $this->id_notice = intval($parser_environnement['id_notice']);
            $this->notice=gere_global();
        }
    }
    
    
    public function get_authors_number($responsability_type, $author_type=[], $responsability_fonction='') {
        $responsability_type = intval($responsability_type);
        $author_type = implode("','",$author_type);
        $query="select count(*) as nb from responsability, authors
			where responsability_notice='".$this->id_notice."'
				and responsability_author=author_id ";
        if ($author_type) {
            $query .= " and author_type in('".$author_type."') ";
        }
        if ($responsability_type <= 2) {
            $query.= " and responsability_type<='".addslashes($responsability_type)."'";
        } else {
            if ($responsability_type == 3) {
                $query.= " and responsability_type='1'";
            } else if ($responsability_type == 4) {
                $query.= " and responsability_type='2'";
            } else if ($responsability_type == 5) {
                $query.= " and responsability_type in('1','2')";
            }
        }
        if (!empty($responsability_fonction)) {
            $query .= " and responsability_fonction='".addslashes($responsability_fonction)."'";
        }
        $result = pmb_mysql_query($query);
        $result_count = pmb_mysql_fetch_object($result);
        return $result_count->nb;
    }
    
    public function get_authors($responsability_type, $author_type=[], $responsability_fonction='', $limit=0) {
        $authors = array();
        $responsability_type = intval($responsability_type);
        $author_type = implode("','",$author_type);
        $query = "select author_id, responsability_fonction, responsability_type
			from responsability, authors
			where responsability_notice='".$this->id_notice."'
				and responsability_author=author_id ";
        if ($author_type) {
            $query .= " and author_type in('".$author_type."') ";
        }
        if ($responsability_type <= 2) {
            $query.= " and responsability_type<='".addslashes($responsability_type)."' ";
        } else {
            if ($responsability_type == 3) {
                $query.= " and responsability_type='1' ";
            } else if ($responsability_type == 4) {
                $query.= " and responsability_type='2' ";
            } else if ($responsability_type == 5) {
                $query.= " and responsability_type in('1','2') ";
            }
        }
        if (!empty($responsability_fonction)) {
            $query .= " and responsability_fonction='".addslashes($responsability_fonction)."'";
        }
        $query.= " order by responsability_type, responsability_ordre " ;
        if ($limit>0) $query .= " limit 0,".$limit;
        $res_sql = pmb_mysql_query($query);
        while ($row = pmb_mysql_fetch_object($res_sql)) {
            $authors[] = $row;
        }
        return $authors;
    }
    
    public function get_formatted_authors($function_name, $authors) {
        global $fonction_auteur;
        
        $formatted_authors = array();
        foreach ($authors as $author) {
            $aut_detail = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $author->author_id);
            $formatted_author = '';
            switch ($function_name) {
                case 'aff_authors':
                    if(empty($this->param[6])) {
                        $formatted_author .= $aut_detail->isbd_entry;
                        if ($author->responsability_fonction && !empty($this->param[4]) && $this->param[4]==1) {
                            if (!empty($fonction_auteur[$author->responsability_fonction])) {
                                $formatted_author .= ", ".$fonction_auteur[$author->responsability_fonction];
                            }
                        }
                    } elseif($this->param[6]==1){
                        if ($author->responsability_fonction && !empty($fonction_auteur[$author->responsability_fonction])) {
                            $function=$fonction_auteur[$author->responsability_fonction];
                        } else {
                            $function="";
                        }
                        $format="%s %s (%s)";
//                         $args[]=$aut_detail->name;
//                         $args[]=$aut_detail->rejete;
//                         $args[]=$function;
                        $formatted_author .= sprintf($format,$aut_detail->name,substr($aut_detail->rejete,0,1),$function);
                    }
                    break;
                case 'aff_authors_by_type':
                    $formatted_author = $aut_detail->isbd_entry;
                    if ($author->responsability_fonction && !empty($this->param[4]) && $this->param[4]==1) {
                        if (!empty($fonction_auteur[$author->responsability_fonction])) {
                            $formatted_author .= ", ".$fonction_auteur[$author->responsability_fonction];
                        }
                    }
                    break;
                case 'aff_authors_by_type_dir':
                    $formatted_author = $aut_detail->isbd_entry;
                    if ($author->responsability_fonction && !empty($this->param[4]) && $this->param[4]==1 && $author->responsability_fonction == "651") {
                        $formatted_author .= " (dir.)";
                    }
                    break;
                case 'aff_authors_by_type_with_tpl':
                    $aut_pp= new parametres_perso("author");
                    $aut_pp->get_values($author->author_id);
                    $values_pp = $aut_pp->values;
                    $aut_detail->parametres_perso=array();
                    foreach ( $values_pp as $field_id => $vals ) {
                        $aut_detail->parametres_perso[$aut_pp->t_fields[$field_id]["NAME"]]["TITRE"]=$aut_pp->t_fields[$field_id]["TITRE"];
                        foreach ( $vals as $value ) {
                            $aut_detail->parametres_perso[$aut_pp->t_fields[$field_id]["NAME"]]["VALUE"][]=$aut_pp->get_formatted_output(array($value),$field_id);
                        }
                    }
                    if ($author->responsability_fonction && !empty($this->param[4]) && $this->param[4]==1) {
                        if (!empty($fonction_auteur[$author->responsability_fonction])) {
                            $aut_detail->function = $fonction_auteur[$author->responsability_fonction];
                        } else {
                            $aut_detail->function = '';
                        }
                    }
                    $parser=parse_format::get_instance('notice_tpl.inc.php');
                    $parser->cmd =_get_aut_infos($aut_detail,$this->param[7]);
                    $formatted_author = $parser->exec_cmd();
                    break;
            }
            if (!isset($formatted_authors[$author->responsability_type])) {
                $formatted_authors[$author->responsability_type] = [];
            }
            $formatted_authors[$author->responsability_type][] = $formatted_author;
        }
        return $formatted_authors;
    }
    
    public function get_display_from_formatted_authors($formatted_authors, $separator_in_type, $separator_between_type, $exceeded=false, $separator_last_author='') {
        $display = '';
        if ($separator_last_author) {
            $authors_number = 0;
            if (!empty($formatted_authors[0]) && count($formatted_authors[0])) {
                $authors_number += count($formatted_authors[0]);
            }
            if (!empty($formatted_authors[1]) && count($formatted_authors[1])) {
                $authors_number += count($formatted_authors[1]);
            }
            if (!empty($formatted_authors[2]) && count($formatted_authors[2])) {
                $authors_number += count($formatted_authors[2]);
            }
            $indice_authors = 0;
            foreach ($formatted_authors as $authors) {
                if (!empty($display)) {
                    if (($indice_authors+1) == $authors_number) {
                        $display .= $separator_last_author;
                    } else {
                        $display .= $separator_between_type;
                    }
                }
                foreach ($authors as $indice_author=>$author) {
                    if ($indice_author) {
                        if (($indice_authors+1) == $authors_number) {
                            $display .= $separator_last_author;
                        } else {
                            $display .= $separator_in_type;
                        }
                    }
                    $display .= $author;
                    $indice_authors++;
                }
            }
        } else {
            $aut = (!empty($formatted_authors[0]) ? $formatted_authors[0] : []);
            if (!empty($formatted_authors[1]) && count($formatted_authors[1])) {
                $aut[]=implode($separator_in_type,$formatted_authors[1]);
            }
            if (!empty($formatted_authors[2]) && count($formatted_authors[2])) {
                $aut[]=implode($separator_in_type,$formatted_authors[2]);
            }
            if ($exceeded) {
                $aut[]="et al.";
            }
            if (count($aut)) {
                $display .= implode($separator_between_type,$aut);
            }
        }
        return $display;
    }
    
    public function aff_authors() {
        $this->param[0] = (isset($this->param[0]) ? $this->param[0] : '');
        $this->param[1] = (isset($this->param[1]) ? intval($this->param[1]) : 0);
        $author_types = array();
        if (isset($this->param[7]) && $this->param[7]) {
            $responsability_fonction = $this->param[7];
        } else {
            $responsability_fonction = '';
        }
        $this->param[8] = (isset($this->param[8]) ? $this->param[8] : '');
        $authors_number = $this->get_authors_number($this->param[0], $author_types, $responsability_fonction);
        $authors = $this->get_authors($this->param[0], $author_types, $responsability_fonction, $this->param[1]);
        if (!empty($authors)) {
            $formatted_authors = $this->get_formatted_authors('aff_authors', $authors);
            $exceeded = false;
            if ($this->param[1]!= 0 && isset($this->param[5]) && $this->param[5] && $authors_number>$this->param[1]) {
                $exceeded = true;
            }
            return $this->get_display_from_formatted_authors($formatted_authors, $this->param[2], $this->param[3], $exceeded, $this->param[8]);
        }
        return '';
    }
    
    public function aff_authors_by_type(){
        $this->param[1] = (isset($this->param[1]) ? intval($this->param[1]) : 0);
        $this->param[6] = (isset($this->param[6]) ? explode(",",$this->param[6]) : []);
        $this->param[7] = (isset($this->param[7]) ? $this->param[7] : '');
        $authors_number = $this->get_authors_number($this->param[0], $this->param[6]);
        $authors = $this->get_authors($this->param[0], $this->param[6], '', $this->param[1]);
        if (!empty($authors)) {
            $formatted_authors = $this->get_formatted_authors('aff_authors_by_type', $authors);
            $exceeded = false;
            if ($this->param[1]!= 0 && isset($this->param[5]) && $this->param[5] && $authors_number>$this->param[1]) {
                $exceeded = true;
            }
            return $this->get_display_from_formatted_authors($formatted_authors, $this->param[2], $this->param[3], $exceeded, $this->param[7]);
        }
        return '';
    }
    
    public function aff_authors_by_type_dir(){
        $this->param[1] = (isset($this->param[1]) ? intval($this->param[1]) : 0);
        $this->param[6] = (isset($this->param[6]) ? explode(",",$this->param[6]) : []);
        $this->param[7] = (isset($this->param[7]) ? $this->param[7] : '');
        $authors_number = $this->get_authors_number($this->param[0], $this->param[6]);
        $authors = $this->get_authors($this->param[0], $this->param[6], '', $this->param[1]);
        if (!empty($authors)) {
            $formatted_authors = $this->get_formatted_authors('aff_authors_by_type_dir', $authors);
            $exceeded = false;
            if ($this->param[1]!= 0 && isset($this->param[5]) && $this->param[5] && $authors_number>$this->param[1]) {
                $exceeded = true;
            }
            return $this->get_display_from_formatted_authors($formatted_authors, $this->param[2], $this->param[3], $exceeded, $this->param[7]);
        }
        return '';
    }
    
    public function aff_authors_by_type_with_tpl(){
        $this->param[1] = (isset($this->param[1]) ? intval($this->param[1]) : 0);
        $this->param[6] = (isset($this->param[6]) ? explode(",",$this->param[6]) : []);
        $this->param[9] = (isset($this->param[9]) ? $this->param[9] : '');
        if (isset($this->param[8]) && $this->param[8]) {
            $responsability_fonction = $this->param[8];
        } else {
            $responsability_fonction = '';
        }
        $authors_number = $this->get_authors_number($this->param[0], $this->param[6], $responsability_fonction);
        $authors = $this->get_authors($this->param[0], $this->param[6], $responsability_fonction, $this->param[1]);
        if (!empty($authors)) {
            $formatted_authors = $this->get_formatted_authors('aff_authors_by_type_with_tpl', $authors);
            $exceeded = false;
            if ($this->param[1]!= 0 && isset($this->param[5]) && $this->param[5] && $authors_number>$this->param[1]) {
                $exceeded = true;
            }
            return $this->get_display_from_formatted_authors($formatted_authors, $this->param[2], $this->param[3], $exceeded, $this->param[9]);
        }
        return '';
    }
    
    public static function get_instance($param) {
        global $parser_environnement;
        if(!$parser_environnement['id_notice']) {
            return '';
        }
        return new interpreter_notice_tpl($param);
    }
    
    public static function exec($param, $method_name) {
        $interpreter_notice_tpl = static::get_instance($param);
        if (is_object($interpreter_notice_tpl)) {
            return $interpreter_notice_tpl->{$method_name}();
        }
        return '';
    }
}

