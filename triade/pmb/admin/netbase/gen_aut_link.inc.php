<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: gen_aut_link.inc.php,v 1.7 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["gen_aut_link_title"]);

$error = '';

if (pmb_mysql_num_rows(pmb_mysql_query("show columns from aut_link like 'id_aut_link'")) == 0) {
    
    $relations = new marc_select("aut_link");
    $links = $relations->table['descendant'];
    
    $query = "SELECT * FROM aut_link";
    $result = pmb_mysql_query($query);
    
    $error_codes = array();
    while ($row = pmb_mysql_fetch_object($result)) {
        if (!array_key_exists($row->aut_link_type, $links)) {
            $error_codes[] = $row->aut_link_type;
        }
    }

    if (count($error_codes)) {
        $error_codes = array_unique($error_codes);
        asort($error_codes);
        $error = "<div class='erreur'>" . htmlentities($msg['gen_net_base_aut_link_error'] . implode(', ', $error_codes), ENT_QUOTES, $charset) . "</div>";
    }
    
    if (!$error) {
        $query = "RENAME TABLE aut_link TO aut_link_old";    
        pmb_mysql_query($query);
        
        $query = "CREATE TABLE aut_link(
            id_aut_link INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    		aut_link_from INT(2) NOT NULL DEFAULT 0 ,
    		aut_link_from_num INT(11) NOT NULL DEFAULT 0 ,
    		aut_link_to INT(2) NOT NULL DEFAULT 0 ,
    		aut_link_to_num INT(11) NOT NULL DEFAULT 0 ,
    		aut_link_type VARCHAR(10) NOT NULL DEFAULT '',
    		aut_link_comment VARCHAR(255) NOT NULL DEFAULT '',
            aut_link_string_start_date VARCHAR(255) NOT NULL DEFAULT '',
            aut_link_string_end_date VARCHAR(255) NOT NULL DEFAULT '',
            aut_link_start_date DATE NOT NULL DEFAULT '0000-00-00',
            aut_link_end_date DATE NOT NULL DEFAULT '0000-00-00',
    		aut_link_rank INT(11) NOT NULL DEFAULT 0 ,
    		aut_link_direction VARCHAR(4) NOT NULL DEFAULT '',
    		aut_link_reverse_link_num INT(11) NOT NULL DEFAULT 0 ,
    
            INDEX i_from(aut_link_from,aut_link_from_num),
            INDEX i_to (aut_link_to,aut_link_to_num),
    		KEY(aut_link_from, aut_link_from_num, aut_link_to, aut_link_to_num, aut_link_type))
        ";
        
        $result = pmb_mysql_query($query);
        
        $query = "SELECT * FROM aut_link_old";
        $result = pmb_mysql_query($query);    
        while ($row = pmb_mysql_fetch_object($result)) {
            $query = "INSERT INTO aut_link SET 
                aut_link_from=" . $row->aut_link_from . ",
                aut_link_from_num=" . $row->aut_link_from_num . ",
                aut_link_to=" . $row->aut_link_to . ",
                aut_link_to_num=" . $row->aut_link_to_num . ",
                aut_link_type='" . addslashes($row->aut_link_type) . "',
                aut_link_comment='" . addslashes($row->aut_link_comment) . "',
                aut_link_string_start_date='" . addslashes($row->aut_link_string_start_date) . "',
                aut_link_string_end_date='" . addslashes($row->aut_link_string_end_date) . "',
                aut_link_start_date='" . $row->aut_link_start_date . "',
                aut_link_end_date='" . $row->aut_link_end_date . "',
                aut_link_direction='down'
            ";    
            pmb_mysql_query($query);
            $id_aut_link = pmb_mysql_insert_id();
            if ($row->aut_link_reciproc) {
                $query = "INSERT INTO aut_link SET
                    aut_link_from=" . $row->aut_link_to . ",
                    aut_link_from_num=" . $row->aut_link_to_num . ",
                    aut_link_to=" . $row->aut_link_from . ",
                    aut_link_to_num=" . $row->aut_link_from_num . ",
                    aut_link_type='" . addslashes('i' . $row->aut_link_type) . "',
                    aut_link_comment='" . addslashes($row->aut_link_comment) . "',
                    aut_link_string_start_date='" . addslashes($row->aut_link_string_start_date) . "',
                    aut_link_string_end_date='" . addslashes($row->aut_link_string_end_date) . "',
                    aut_link_start_date='" . $row->aut_link_start_date . "',
                    aut_link_end_date='" . $row->aut_link_end_date . "',
                    aut_link_direction='up',
                    aut_link_reverse_link_num = " . $id_aut_link . "
                ";        
                pmb_mysql_query($query);
                $id_aut_link_other = pmb_mysql_insert_id();
                
                $query = "UPDATE aut_link SET
                    aut_link_reverse_link_num = " . $id_aut_link_other . " 
                    WHERE id_aut_link=" . $id_aut_link;
                pmb_mysql_query($query);            
            }
        }    
        $query = "DROP TABLE aut_link_old";
        $result = pmb_mysql_query($query);
    }
}
$spec = $spec - GEN_AUT_LINK;
$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["gen_aut_link_title"], ENT_QUOTES, $charset) .  $error;
// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec);
		
