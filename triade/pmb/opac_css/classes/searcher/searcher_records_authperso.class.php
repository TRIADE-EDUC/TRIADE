<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records_authperso.class.php,v 1.2 2020/10/29 10:43:43 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

class searcher_records_authperso extends searcher_records
{

    protected function _filter_results()
    {
        if (! empty($this->var_table['authperso_num'])) {
            $query = "
                SELECT DISTINCT notice_authperso_notice_num as notice_id FROM notices_authperso 
				JOIN authperso_authorities ON id_authperso_authority = notice_authperso_authority_num 
                AND authperso_authority_authperso_num = " . $this->var_table['authperso_num'] . "
				WHERE notice_authperso_notice_num IN (" . $this->objects_ids . ")";
            $results = pmb_mysql_query($query);
            $this->objects_ids = "";
            if (pmb_mysql_num_rows($results)) {
                while ($rows = pmb_mysql_fetch_assoc($results)) {
                    if ($this->objects_ids) {
                        $this->objects_ids .= ',';
                    }
                    $this->objects_ids .= $rows["notice_id"];
                }
            }
        }
    }
}