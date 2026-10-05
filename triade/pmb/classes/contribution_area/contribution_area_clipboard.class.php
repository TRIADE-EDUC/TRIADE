<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_clipboard.class.php,v 1.1 2020/07/29 13:16:57 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

class contribution_area_clipboard
{
    public static function get_clipboard($id)
    {
        $clipboard = [];
        if (empty($id)) {
            return $clipboard;
        }
        $query = "SELECT datas FROM contribution_area_clipboard where id_clipboard = $id";
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            $result = pmb_mysql_fetch_object($result);
            $clipboard = unserialize($result->datas);
        }
        return $clipboard;
    }

    public static function push_clipboard()
    {
        global $scenario_id;
        global $area_id;
        global $source_area_id;
        global $duplicate_forms;
        global $data;
        
        // Rajouter l'id de l'utilisateur
        
        $datas = [
            "scenario_id" => $scenario_id,
            "area_id" => $area_id,
            "source_area_id" => $source_area_id,
            "duplicate_forms" => $duplicate_forms,
            "data" => json_decode(preg_replace('/:\s*(-?\d+(.\d+)?([e|E][-|+]\d+)?)/', ': "$1"', stripslashes($data)))
        ];
        
        $query = 'INSERT INTO contribution_area_clipboard (datas, created_at) VALUES ("' . addslashes(serialize($datas)) . '", now() )';
        pmb_mysql_query($query);
        $id = pmb_mysql_insert_id();
        
        return array(
            "id" => $id
        );
    }
    
    public static function is_valid($id)
    {
        if (empty($id)) {
            return false;
        }
        
        $query = "SELECT 1 FROM contribution_area_clipboard where id_clipboard = $id";
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            return true;
        }
        return false;
    }
    
    public static function delete_clipboard($id)
    {
        if (empty($id)) {
            return false;
        }
        
        $query = "DELETE FROM contribution_area_clipboard where id_clipboard = $id";
        pmb_mysql_query($query);
        
        $now = new Datetime();
        $now->modify('-1 hours');
        
        $query = 'DELETE FROM contribution_area_clipboard where created_at <= "' . $now->format('Y-m-d H:i:s') .'"';
        pmb_mysql_query($query);
        
        return true;
    }
}
