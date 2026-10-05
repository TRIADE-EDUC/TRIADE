<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: print_concepts.php,v 1.5 2023/11/30 11:10:07 dgoron Exp $

$base_path = ".";
$base_auth = "AUTORITES_AUTH";
$base_title = "\$msg[print_concepts_title]";

if ($_GET['action'] != "print") {
	$base_nobody = 0;
	$base_noheader = 0;
} else {
	$base_nobody = 1;
	$base_noheader = 1;
}
require($base_path . "/includes/init.inc.php");

global $msg, $action, $output, $typeimpression;
global $scheme_id, $parent_id;

@set_time_limit(0);

$color = array();
$color[0] = "#d7d8ff"; // violet
$color[1] = "#fcffc5"; // jaune
$color[2] = "#c9e9ff"; // bleu
$color[3] = "#c6ffc5"; // vert
$color[4] = "#ffedc5"; // saumon
$color[5] = "#F78181"; // rouge

$list_uri = array();
$data_noeud = array();
$data_noeud_to_sort = array();


if ($action != "print") {
	print "
    <h3>" . $msg['print_concepts_title'] . "</h3>
    <form name='print_options' action='print_concepts.php?action=print' method='post'>
		<b>" . $msg['print_concepts_options'] . "</b>
		<blockquote>" . $msg['print_thes_list_type'] . "
			<select name='typeimpression'>
                <option value='arbo' selected>" . $msg['print_concepts_titre_arbo'] . "</option>
				<option value='alph' >" . $msg['print_concepts_titre_alph'] . "</option>
             </select>
		</blockquote>
		<blockquote>
			<input type='checkbox' name='print_concepts_option_libelle' id='print_concepts_option_libelle' CHECKED value='1' />&nbsp;<label for='print_concepts_option_libelle'>" . $msg['print_concepts_option_libelle'] . "</label><br />
			<input type='checkbox' name='print_concepts_option_uri' id='print_concepts_option_uri' value='1' />&nbsp;<label for='print_concepts_option_uri'>" . $msg['print_concepts_option_uri'] . "</label><br />
			<input type='checkbox' name='print_concepts_option_isbd' id='print_concepts_option_isbd' value='1' />&nbsp;<label for='print_concepts_option_isbd'>" . $msg['print_concepts_option_isbd'] . "</label><br />
			<input type='checkbox' name='print_concepts_option_alter_hidden' id='print_concepts_option_alter_hidden' value='1' />&nbsp;<label for='print_concepts_option_alter_hidden'>" . $msg['print_concepts_option_alter_hidden'] . "</label><br />
			<input type='checkbox' name='print_concepts_option_detail' id='print_concepts_option_detail' value='1' />&nbsp;<label for='print_concepts_option_detail'>" . $msg['print_concepts_option_detail'] . "</label><br />
		</blockquote>
		<b>" . $msg["print_output_title"] . "</b>
		<blockquote>
			<input type='radio' name='output' id='output_printer' value='printer' checked/>&nbsp;<label for='output_printer'>" . $msg["print_output_printer"] . "</label><br />
			<input type='radio' name='output' id='output_tt' value='tt' />&nbsp;<label for='output_tt'>" . $msg["print_output_writer"] . "</label><br />
		</blockquote>
		<input type='hidden' name='aff_langue' value='fr_FR'>
		<input type='hidden' name='scheme_id' value='". $scheme_id . "'>
        <span style='text-align:center'>
            <input type='submit' value='" . $msg["print_print"] . "' class='bouton'/>&nbsp;
            <input type='button' value='" . $msg["print_cancel"] . "' class='bouton' onClick='self.close();'/>
        </span>
    </form>
    </body>
</html>";
}

if ($action == "print") {
    if (isset($scheme_id) && $scheme_id!=0) {
        if (isset($parent_id) && $parent_id!=0 && $parent_id != $scheme_id) {
            $query = 'select distinct ?concept where {
                ?concept rdf:type skos:Concept .
                ?concept skos:prefLabel ?label .
                ?concept skos:inScheme <'.onto_common_uri::get_uri($scheme_id).'> .
                ?concept skos:broader <'.onto_common_uri::get_uri($parent_id).'>
                } order by ?label';
        } else {
            $query = 'select distinct ?concept where {
                ?concept rdf:type skos:Concept .
                ?concept skos:prefLabel ?label .
                ?concept skos:inScheme <'.onto_common_uri::get_uri($scheme_id).'> .
                ?concept pmb:showInTop <'.onto_common_uri::get_uri($scheme_id).'> .
                } order by ?label';
        }
    } else {
        $query = 'select distinct ?scheme ?label where {
            ?scheme rdf:type skos:ConceptScheme .
                ?scheme skos:prefLabel ?label .
        } order by ?label';
    }
    $store = skos_datastore::get_store();
    $results = $store->get_result($store->query($query));
	if ($output == "tt") {
		header("Content-Type: application/word");
		header("Content-Disposition: attachement; filename=concepts.doc");
	}
	print "<!DOCTYPE html><html lang='" . get_iso_lang_code() . "'><head><meta charset=\"UTF-8\" /></head><body style='font-family : Arial, Helvetica, Verdana, sans-serif;'>";
	print "<h2>" . encoding_normalize::utf8_normalize($msg["print_concepts_titre_" . $typeimpression]) . "</h2>";
	$niveau = 0;
	switch ($typeimpression) {
		case "arbo":		
		    echo "<table style='border-spacing: 0px; padding: 3px; width: 100%'>";
		    $count = count($results);
		    for ($i = 0; $i < $count; $i++) {
			    enfants($results[$i]->concept, $niveau, true);
			}
			echo "</table>";
			break;
		case "alph":
		    $count = count($results);
		    for ($i = 0; $i < $count; $i++) {
		        enfants($results[$i]->concept, $niveau, false);
		    }
		    asort($data_noeud_to_sort);
		    echo "<table style='border-spacing: 0px; padding: 3px; width: 100%'>";
		    foreach ($data_noeud_to_sort as $uri => $value) {
		        echo encoding_normalize::utf8_normalize($data_noeud[$uri]['display']);
		    }
		    echo "</table>";
			break;			
	}
	print "</body></html>";
}

pmb_mysql_close();

function enfants($uri, $niveau, $print_arbo = true) {
    global $list_uri, $data_noeud, $data_noeud_to_sort;
    
    if (in_array($uri, $list_uri)) return;
    $list_uri[] = $uri;
    
    $authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, ['num_object' => onto_common_uri::get_id($uri), 'type_object' => AUT_TABLE_CONCEPT]); 
    if ($print_arbo) {
        echo encoding_normalize::utf8_normalize(infos_noeud($uri, $authority, $niveau));
        flush();
    } else {
        $data_noeud[$uri] = get_data_noeud($uri, $authority, $niveau);
        $data_noeud_to_sort[$uri] = strtoupper($data_noeud[$uri]['name']);
    }
     
    // chercher les enfants
    $narrowers = $authority->get_object_instance->get_narrowers()->get_concepts();
    if (count($narrowers)) {
        if ($print_arbo) $niveau++;
        foreach ($narrowers as $narrower) {
            enfants($narrower->get_uri(), $niveau, $print_arbo);
        }
    }
}

function get_data_noeud($uri, $authority, $niveau) {
    global $color;
    global $print_concepts_option_libelle, $print_concepts_option_uri, $print_concepts_option_isbd, $print_concepts_option_detail, $print_concepts_option_alter_hidden;
    
    $data = [
        'id' => onto_common_uri::get_id($uri),
        'uri' => $uri,
        'name' => $authority->get_object_instance->get_display_label(),
        'isbd' => $authority->get_isbd(),
        'alter_hidden' => $authority->get_object_instance->get_alter_hidden_list(),
        'detail' => $authority->get_object_instance->get_details_list(),
    ];
    $data['display'] = "
        <tr>
            <td bgcolor='" . $color[$niveau] . "'>
                <div style='margin-left: " . (20 * $niveau) . "px;'>" .
                ($print_concepts_option_libelle ? $data['name'] . '<br/>' : '') .
                ($print_concepts_option_isbd ? $data['isbd'] . '<br/>' : '') .
                ($print_concepts_option_uri ? $data['uri'] . '<br/>' : '') .
                ($print_concepts_option_alter_hidden ? $data['alter_hidden'] . '<br/>' : '') .
                ($print_concepts_option_detail ? $data['detail'] . '<br/>' : '') . "
                </div>
            </td>
        </tr>
    ";
    return $data;
}

function infos_noeud($uri, $authority, $niveau) {	
    global $color;
    global $print_concepts_option_libelle, $print_concepts_option_uri, $print_concepts_option_isbd, $print_concepts_option_detail, $print_concepts_option_alter_hidden;
		
	$data = [
	    'id' => onto_common_uri::get_id($uri),
	    'uri' => $uri,
	    'name' => $authority->get_object_instance->get_display_label(),
	    'isbd' => $authority->get_isbd(),
	    'alter_hidden' => $authority->get_object_instance->get_alter_hidden_list(),
	    'detail' => $authority->get_object_instance->get_details_list(),
	];
	return "
        <tr>
            <td bgcolor='" . $color[$niveau] . "'>
                <div style='margin-left: " . (20 * $niveau) . "px;'>" .
                    ($print_concepts_option_libelle ? $data['name'] . '<br/>' : '') .
                    ($print_concepts_option_isbd ? $data['isbd'] . '<br/>' : '') .
                    ($print_concepts_option_uri ? $data['uri'] . '<br/>' : '') .
                    ($print_concepts_option_alter_hidden ? $data['alter_hidden'] . '<br/>' : '') . 
                    ($print_concepts_option_detail ? $data['detail'] . '<br/>' : '') . "
                </div>
            </td>
        </tr>
    ";	
}

