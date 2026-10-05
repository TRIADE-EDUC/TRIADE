<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_filter_animations_by_start_date.class.php,v 1.1 2023/09/20 14:52:16 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_common_filter_animations_by_start_date extends cms_module_common_filter
{

    public function get_filter_from_selectors()
    {
        return array(
            "cms_module_common_selector_animation_by_start_date"
        );
    }

    public function get_filter_by_selectors()
    {
        return array();
    }

    public function filter($datas)
    {
        $query = "SELECT id_animation FROM anim_animations
                    JOIN anim_events ON id_event = num_event
                    WHERE to_days(start_date)>=to_days(now())
                    AND to_days(end_date)>=to_days(now())
                    AND id_animation in (" . implode(',', $datas) . ")";

        $result = pmb_mysql_query($query);

        $filtered_datas = [];
        while ($row = pmb_mysql_fetch_row($result)) {
            $filtered_datas[] = $row[0];
        }

        return $filtered_datas;
    }
}