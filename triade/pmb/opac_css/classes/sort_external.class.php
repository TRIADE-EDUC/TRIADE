<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sort_external.class.php,v 1.3 2022/05/13 09:56:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/sort.class.php");

/**
 * Classe de tri des résultats de recherche externe dans le catalogue
 * Utilise une variable de session("tri") pour stocker le tri en cours
 *
 */
class sort_external extends sort {
	
	/**
	 * Genere la requete select d'un element table
	 */
	public function genereRequeteUpdate($desTable, $nomTable, $nomChp, $nomColonneTempo) {
	    
		$query = "SELECT rid, source_id FROM external_count JOIN $nomTable ON rid = notice_id";
		$result = pmb_mysql_query($query);
		$infos = [];
		
		if (pmb_mysql_num_rows($result)) {
		    while($row = pmb_mysql_fetch_assoc($result)) {
		        $table_name = "entrepot_source_".$row["source_id"];
		        if (!isset($infos[$table_name])) {
		            $infos[$table_name] = [];
		        }
		        $infos[$table_name][] = $row["rid"];		        
		    }
		}
		
		$query = "";
		foreach ($infos as $name => $ids) {
		    if ($query) {
		        $query .= " UNION ";
		    }
		    $query .= "SELECT recid AS notice_id, SUBSTRING(".$this->ajoutIfNull($desTable["TABLEFIELD"][0]).", 1, 255) AS $nomChp FROM $name ";
		    //
		    //On ajout les éventuelles liaisons
		    //
		    if(isset($desTable["LINK"])) {
		        for ($x = 0; $x < count($desTable["LINK"]); $x++) {
		            $query .= static::genereRequeteLinks($desTable, $nomTable, $desTable["LINK"][$x], $name, "notice_id");
		        }
		    }
		    $query .= " WHERE ";
		    //si on a un filtre supplementaire
		    if (isset($desTable["FILTER"])) {
		        if (isset($desTable["FILTER"][0]["GLOBAL"])) {
		            global ${$desTable["FILTER"][0]["GLOBAL"]};
		            $desTable["FILTER"][0]['value'] = str_replace('!!' . $desTable["FILTER"][0]["GLOBAL"] . '!!', ${$desTable["FILTER"][0]["GLOBAL"]}, $desTable["FILTER"][0]['value']);
		        }
		        $query .= " " . $desTable["FILTER"][0]['value'];
		    }
		    
		    $query .= " AND recid IN (".implode(',', $ids).")";
		    
		    //On applique la restriction ORDER BY
		    //Utilisé pour les types de langues ou d'auteurs, ...
		    if (isset($desTable["ORDERBY"])) {
		        $query .= " ORDER BY ".$this->ajoutIfNull($desTable["ORDERBY"][0]);
		    }
		    //Si l'on a un group by on passe par une sous-requete pour que le groupement soit fait après le tri (Cas des Auteurs : C'est l'auteur principal qui doit être utilisé pour le tri)
		    if (isset($desTable["GROUPBY"])) {
		        if (isset($desTable["ORDERBY"])) {
		            // Si ORDER BY, on passe par une table temporaire car sinon il n'est pas pris en compte par le group by
		        	$this->gen_temporary_table($nomTable."_groupby", 'notice_id', $nomChp, $query);
		            
		            $query = "SELECT * FROM ".$nomTable."_groupby";
		            $query .= " GROUP BY ".$desTable["GROUPBY"][0]["value"];
		        } else {
		            $query = "SELECT * FROM (".$query.") AS asubquery";
		            $query .= " GROUP BY ".$desTable["GROUPBY"][0]["value"];
		        }
		    }
		}
		//
		//On met le tout dans une table temporaire
		//
		$this->gen_temporary_table($nomTable."_update", 'notice_id', $nomChp, "SELECT * FROM (".$query.") AS temp");
		
		//
		//Et on rempli la table tri_tempo avec les éléments de la table temporaire
		//
		$requete = "UPDATE $nomTable, ".$nomTable."_update 
                    SET $nomTable.$nomChp  = ".$nomTable."_update.$nomChp 
                    WHERE $nomTable.notice_id =  ".$nomTable."_update.notice_id 
                    AND ".$nomTable."_update.$nomChp IS NOT NULL 
                    AND ".$nomTable."_update.$nomChp != ''";
		return $requete;
	}
}
?>