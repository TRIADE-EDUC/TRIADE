<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_sets.class.php,v 1.4.2.1.2.1 2025/02/11 09:20:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class facettes_sets {

    protected $type;

    protected $data;

    public function __construct($type='') {
        $this->type = $type;
        $this->fetch_data();
    }

    protected function fetch_data() {
        global $PMBuserid;

        $query = "SELECT id_set FROM facettes_sets
                LEFT JOIN facettes_sets_users ON facettes_sets_users.num_set = facettes_sets.id_set AND facettes_sets_users.num_user = ".$PMBuserid;
        if(!empty($this->type)) {
            if($this->type == 'authorities') {
                $authorities_types = static::get_authorities_types();
                $query .= " WHERE (type IN ('".implode("','", $authorities_types)."') OR type LIKE 'authperso%')";
            } else {
                $query .= " WHERE type = '".$this->type."'";
            }
        }
        $query .= " ORDER BY type, ranking, name";
        $result = pmb_mysql_query($query);
        if ($result) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $this->data[$row->id_set] = new facettes_set($row->id_set);
            }
        }
    }

    public static function get_authorities_types()
    {
        return [
            'authors', 'categories', 'publishers', 'collections', 'subcollections',
            'series', 'titres_uniformes', 'indexint'
        ];
    }

    public function get_filtered_list($num_user)
    {
        $filtered_list = [];
        $num_user = intval($num_user);
        $grp_num = user::get_param($num_user, 'grp_num');
        if (is_countable($this->data)) {
            foreach ($this->data as $facettes_set) {
                $users_groups = $facettes_set->get_users_groups();
                if($facettes_set->get_num_user() == $num_user || (is_array($users_groups) && in_array($grp_num, $users_groups))) {
                    $filtered_list[] = $facettes_set;
                }
            }
        }
        return $filtered_list;
    }

    public function get_display_selector($selected) {
        global $msg, $charset, $PMBuserid;

        $facettes_sets = $this->get_filtered_list($PMBuserid);
        if (!empty($facettes_sets) && count($facettes_sets) > 1) {
            $selector = "
            <div>
                <label for='facettes_sets_selector'>".htmlentities($msg['facettes_set'], ENT_QUOTES, $charset)."</label>
            </div>
            <div>
                <select id='facettes_sets_selector' name='facettes_sets_selector' onchange='facettes_set_selection(this.value)'>";
                foreach ($facettes_sets as $facettes_set) {
                    if($this->type == 'authorities') {
                        $selector .= "<option value='".$facettes_set->get_id()."' ".($facettes_set->get_id() == $selected ? "selected='selected'" : "").">".htmlentities($facettes_set->get_name(), ENT_QUOTES, $charset)."</option>";
                    } else {
                        $selector .= "<option value='".$facettes_set->get_id()."' ".($facettes_set->get_id() == $selected ? "selected='selected'" : "").">".htmlentities($facettes_set->get_name(), ENT_QUOTES, $charset)."</option>";
                    }
                }
                $selector .= "
                </select>
            </div>";
            return $selector;
        }
        return '';
    }
}

