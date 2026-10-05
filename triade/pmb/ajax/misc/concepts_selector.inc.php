<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: concepts_selector.inc.php,v 1.4 2022/04/15 12:16:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $parent_id, $scheme_id, $page, $msg;

//En fonction de $categ, il inclut les fichiers correspondants

$query = "";

if(isset($parent_id)){
    $parent_id = intval($parent_id);
}else{
    $parent_id =0;
}
if(isset($scheme_id)){
    $scheme_id = intval($scheme_id);
}
// TODO utiliser un paramètre utilisateur existant ?
$limit=30;
if(isset($page)){
    $page = intval($page);
}else{
    $page=0;
}
if($page == 0){
    $page=1;
}
$datas = [];
if(isset($scheme_id) && $scheme_id!=0){
    if(isset($parent_id) && $parent_id!=0 && $parent_id != $scheme_id){
        $query = 'select distinct ?concept where {
            ?concept rdf:type skos:Concept .
            ?concept skos:prefLabel ?label .
            ?concept skos:inScheme <'.onto_common_uri::get_uri($scheme_id).'> .
            ?concept skos:broader <'.onto_common_uri::get_uri($parent_id).'>
        } order by ?label';
    }else{
        $query = 'select distinct ?concept where {
            ?concept rdf:type skos:Concept .
            ?concept skos:prefLabel ?label .
            ?concept skos:inScheme <'.onto_common_uri::get_uri($scheme_id).'> .
            ?concept pmb:showInTop <'.onto_common_uri::get_uri($scheme_id).'> .
        } order by ?label';
    }
}else{
    $query = 'select distinct ?scheme ?label where {
        ?scheme rdf:type skos:ConceptScheme .
            ?scheme skos:prefLabel ?label .
    } order by ?label';
}
if(!$query){
  print encoding_normalize::json_encode($datas);  
  return;
}
$store = skos_datastore::get_store();
$results = $store->query($query.' limit '.$limit.' offset '.$limit*($page-1) );
$results = $store->get_result();
// var_dump($results);
if(isset($results[0]->scheme) && $page == 1){
    $datas[] = [
        'id' => '0',
        'type' => 'root',
    ];
}
for($i=0 ; $i<count($results) ; $i++){
    if(isset($results[$i]->scheme)){
        $datas[] = [
            'id' => onto_common_uri::get_id($results[$i]->scheme),
            'type'=> 'scheme',
            'uri' => $results[$i]->scheme,
            'name' => $results[$i]->label,
            'parent' => '0'
        ];
    }else{
       $authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY,0, ['num_object'=>onto_common_uri::get_id($results[$i]->concept), 'type_object' =>AUT_TABLE_CONCEPT]);
       $datas[] = [
           'id' => onto_common_uri::get_id($results[$i]->concept),
           'type'=> 'concept',
           'uri' => $results[$i]->concept,
           'name' => $authority->get_display_statut_class_html().$authority->get_object_instance->get_display_label(),
           'isbd' => $authority->get_isbd(),
           'detail' => $authority->get_object_instance->get_details_list(),
           'scheme' => $scheme_id,
           'parent' => ($parent_id ? $parent_id : $scheme_id)
       ];
    }
}
if(count($results) == $limit){
    $page++;
    if($scheme_id){
        $datas[] = [
            'id' => ($parent_id ? $parent_id : $scheme_id).'-suite-'.$page,
            'type'=> 'pagin',
            'name' => '<button data-dojo-type="dijit/form/Button" type="button">'.$msg['pagin_navig_concept'].'</button>',
            'page' => $page,
            'scheme' => $scheme_id,
            'parent' => ($parent_id ? $parent_id : $scheme_id)
        ];
    }else{
        $datas[] = [
            'id' => 'top-suite-'.$page,
            'type'=> 'pagin',
            'name' => '<button data-dojo-type="dijit/form/Button" type="button">'.$msg['pagin_navig_concept'].'</button>',
            'page' => $page,
            'parent' => '0'
        ];
    }
}
print encoding_normalize::json_encode($datas);
return;