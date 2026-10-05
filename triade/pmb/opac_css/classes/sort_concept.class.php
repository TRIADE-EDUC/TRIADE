<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sort_concept.class.php,v 1.2 2020/08/05 12:37:36 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class sort_concept{
    
    public static function update_table_tempo_with_property($table_tempo, $property){
        $query = "SELECT authorities.id_authority, num_object FROM authorities JOIN ".$table_tempo . " ON authorities.id_authority = " . $table_tempo .".id_authority";
        $result= pmb_mysql_query($query);
        $items = [];
        if (pmb_mysql_num_rows($result)){
            while ($row = pmb_mysql_fetch_assoc($result)){
                $items[$row['id_authority']] = static::get_property_from_id_item($row['num_object'], $property);            
            }
            pmb_mysql_query("ALTER TABLE " . $table_tempo . " ADD " . $property . " text");
            pmb_mysql_query("DROP TEMPORARY TABLE IF EXISTS ".$table_tempo."_update");
            
            $query = "CREATE TEMPORARY TABLE ".$table_tempo."_update (id_authority INT, $property text)";
            pmb_mysql_query($query);
            
            $query = "INSERT INTO ".$table_tempo."_update (id_authority, $property) VALUES ";
            $comma = false;
            foreach ($items as $id=>$value){
                if ($comma){
                    $query .= ', ';
                }
                $query .= "($id,'$value')";
                $comma = true;
            }
            pmb_mysql_query($query);
            $query = "UPDATE ".$table_tempo.", ".$table_tempo."_update SET "
                     .$table_tempo.".".$property." = ".$table_tempo."_update.".$property.
                     " WHERE ".$table_tempo.".id_authority = ".$table_tempo."_update.id_authority 
                        AND ".$table_tempo."_update.".$property." IS NOT NULL";
            pmb_mysql_query($query);
        }
        return $table_tempo;
    }
    
    private static function get_property_from_id_item($id_item, $property){
        $concept_uri = onto_common_uri::get_uri($id_item);
        $query = 'select ?property where {
                    ?property pmb:name "'.$property.'"
				}';
        skos_onto::query($query);
        if (skos_onto::num_rows()) {
            foreach (skos_onto::get_result() as $result) {
                $query = 'select ?value where {
                       <'.$concept_uri.'> <'.$result->property.'> ?value
                }';
                skos_datastore::query($query);
                if (skos_datastore::num_rows()) {
                    foreach (skos_datastore::get_result() as $concept) {
                        return $concept->value;
                    }
                }
            }
        }
        return "";
    }
}