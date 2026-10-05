<?php
/*
 * Created on 8 juil. 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 /*
  * Fichier en UTF-8
  */
 
/*
 R�gle Implicite :
 	- Si pas de domaine ou de range : peut s'appliquer � toutes les ressources
 	- Si pas de owl:maxCardinality : liaison n (sans limite)
 	- Si pas de owl:minCarinality : liaison 0 (il peut ne pas y en avoir)
 	- Si pas de owl:maxCardinality ni owl:minCarinality c'est une liaison 0-n
 */

$base_path =  __DIR__ . '/../..';
require_once $base_path.'/vendor/autoload.php';

$config = array(
  /* db */
  'db_name' => 'pmb_arc2',
  'db_user' => 'bibli',
  'db_pwd' => 'bibli',
  /* store */
  'store_name' => 'skos',
  /* stop after 100 errors */
  'max_errors' => 100,
  'store_strip_mb_comp_str' => 0
);
$store = ARC2::getStore($config);
if (!$store->isSetUp()) {
  	$store->setUp();
  	die();
}else{
	$store->reset();
	//Chargement de Skos en base
	$rows= $store->query('LOAD <./skos.rdf>');
	if($rows){
		echo $rows['result']['t_count']." Triplets ajoutés pour SCOS<br />";
	}else{
		echo "Erreur chargement\n";
	}
	//die();
}


$prefix="PREFIX skos: <http://www.w3.org/2004/02/skos/core#> " .
		"PREFIX dct: <http://purl.org/dc/terms/> " .
		"PREFIX owl: <http://www.w3.org/2002/07/owl#> " .
		"PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> " .
		"PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> " .
		"PREFIX pmb: <http://www.pmbservices.fr/ontology#>";


// Redéfinir l'ontologie
$q = $prefix.'
DELETE
{
    <http://www.w3.org/2004/02/skos/core> ?a ?b .
}	
WHERE {
	<http://www.w3.org/2004/02/skos/core> ?a ?b .
	FILTER(?b != owl:ontology) .
}
';
$rows = $store->query($q);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb delete ok: ".$rows["result"]["t_count"]."\n\n\n";
}

$q = $prefix.'
INSERT INTO <skos>
{
    <http://www.w3.org/2004/02/skos/core> dct:description "Ontologie PMB basée sur Skos"@fr .
    <http://www.w3.org/2004/02/skos/core> dct:title "Vocabulaire SKOS - PMB"@fr .
    <http://www.w3.org/2004/02/skos/core> dct:creator "Didier Bellamy" ; dct:creator "Matthieu Bertin"  ; dct:contributor "Florent Tétart" 
}
';
$rows = $store->query($q);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}


//Concept
$q = $prefix.'
INSERT INTO <skos>
{
	skos:Concept owl:disjointWith  skos:Collection .
	skos:Concept owl:disjointWith  skos:ConceptScheme .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//ConceptScheme
$q = $prefix.'
INSERT INTO <skos>
{
	skos:ConceptScheme owl:disjointWith  skos:Collection .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//inScheme
/*$q = $prefix.'
INSERT INTO <skos>
{
    ?class rdfs:subClassOf  [
          rdf:type owl:Restriction ;
          owl:onProperty skos:inScheme ;
          owl:minCardinality "1"^^xsd:nonNegativeInteger
     ]   .
}
WHERE {
	?class a owl:Class .
	FILTER (isURI(?class)) 
}
';*//*
$q = $prefix.'
INSERT INTO <skos>
{
    ?class rdfs:subClassOf  _:inscheme   .
	_:inscheme rdf:type owl:Restriction ; owl:onProperty skos:inScheme   .
    _:inscheme owl:minCardinality "1"^^xsd:nonNegativeInteger   .
}
WHERE {
	?class rdf:type owl:Class .
	FILTER (isURI(?class)) 
}
';*/
$q = $prefix.'
INSERT INTO <skos>
{
	_:inscheme rdf:type owl:Restriction .
    _:inscheme owl:onProperty skos:inScheme .
    _:inscheme owl:minCardinality "1"^^xsd:nonNegativeInteger .
    skos:Concept rdfs:subClassOf _:inscheme .
    skos:ConceptScheme rdfs:subClassOf _:inscheme .
    skos:Collection rdfs:subClassOf _:inscheme .
    skos:OrderedCollection rdfs:subClassOf _:inscheme
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//prefLabel
$q = $prefix.'
INSERT INTO <skos>
{
	skos:prefLabel rdfs:range rdfs:Literal .
	skos:prefLabel owl:disjointWith  skos:altLabel .
	skos:prefLabel owl:disjointWith  skos:hiddenLabel .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}
// Attention on a pas exprimé que l'on peut en avoir plurieurs si les langues sont différentes
$q = $prefix.'
INSERT INTO <skos>
{
	_:preflabel rdf:type owl:Restriction .
    _:preflabel owl:onProperty skos:prefLabel .
    _:preflabel owl:maxCardinality "1"^^xsd:nonNegativeInteger .
    _:preflabel owl:minCardinality "1"^^xsd:nonNegativeInteger .
    skos:Concept rdfs:subClassOf _:preflabel .
    skos:ConceptScheme rdfs:subClassOf _:preflabel .
    skos:Collection rdfs:subClassOf _:preflabel .
    skos:OrderedCollection rdfs:subClassOf _:preflabel
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//altLabel
$q = $prefix.'
INSERT INTO <skos>
{
	skos:altLabel rdfs:range rdfs:Literal .
	skos:altLabel owl:disjointWith  skos:prefLabel .
	skos:altLabel owl:disjointWith  skos:hiddenLabel .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//hiddenLabel
$q = $prefix.'
INSERT INTO <skos>
{
	skos:hiddenLabel rdfs:range rdfs:Literal .
	skos:hiddenLabel owl:disjointWith  skos:prefLabel .
	skos:hiddenLabel owl:disjointWith  skos:altLabel .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//notation
//Attention : il doit avoir une valeur unique pour tous les concepts d'un même schema
$q = $prefix.'
INSERT INTO <skos>
{
	skos:notation rdfs:range rdfs:Literal
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//note
$q = $prefix.'
INSERT INTO <skos>
{
	skos:note rdfs:range rdfs:Literal
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//changeNote
$q = $prefix.'
INSERT INTO <skos>
{
	skos:changeNote rdfs:range rdfs:Literal .
	skos:changeNote rdfs:domain skos:Concept
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//definition
$q = $prefix.'
INSERT INTO <skos>
{
	skos:definition rdfs:range rdfs:Literal .
	skos:definition rdfs:domain skos:Concept
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//editorialNote
$q = $prefix.'
INSERT INTO <skos>
{
	skos:editorialNote rdfs:range rdfs:Literal .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//example
$q = $prefix.'
INSERT INTO <skos>
{
	skos:example rdfs:range rdfs:Literal .
	skos:example rdfs:domain skos:Concept
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//historyNote
$q = $prefix.'
INSERT INTO <skos>
{
	skos:historyNote rdfs:range rdfs:Literal .
	skos:historyNote rdfs:domain skos:Concept
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//scopeNote
$q = $prefix.'
INSERT INTO <skos>
{
	skos:scopeNote rdfs:range rdfs:Literal .
	skos:scopeNote rdfs:domain skos:Concept
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//broader
$q = $prefix.'
INSERT INTO <skos>
{
	skos:broader rdfs:range skos:Concept .
	skos:broader rdfs:domain skos:Concept .
	skos:broader owl:disjointWith  skos:narrower .
	skos:broader owl:disjointWith  skos:related .
	skos:broader owl:disjointWith  skos:narrowerTransitive .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//narrower
$q = $prefix.'
INSERT INTO <skos>
{
	skos:narrower rdfs:range skos:Concept .
	skos:narrower rdfs:domain skos:Concept .
	skos:narrower owl:disjointWith  skos:broader .
	skos:narrower owl:disjointWith  skos:related .
	skos:narrower owl:disjointWith  skos:broaderTransitive .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//related
$q = $prefix.'
INSERT INTO <skos>
{
	skos:related rdfs:range skos:Concept .
	skos:related rdfs:domain skos:Concept .
	skos:related owl:disjointWith  skos:broader .
	skos:related owl:disjointWith  skos:broaderTransitive .
	skos:related owl:disjointWith  skos:narrower .
	skos:related owl:disjointWith  skos:narrowerTransitive .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//broaderTransitive
$q = $prefix.'
INSERT INTO <skos>
{
	skos:broaderTransitive rdfs:range skos:Concept .
	skos:broaderTransitive rdfs:domain skos:Concept .
	skos:broaderTransitive owl:disjointWith  skos:narrower .
	skos:broaderTransitive owl:disjointWith  skos:related .
	skos:broaderTransitive owl:disjointWith  skos:narrowerTransitive .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//narrowerTransitive
$q = $prefix.'
INSERT INTO <skos>
{
	skos:narrowerTransitive rdfs:range skos:Concept .
	skos:narrowerTransitive rdfs:domain skos:Concept .
	skos:narrowerTransitive owl:disjointWith  skos:broader .
	skos:narrowerTransitive owl:disjointWith  skos:related .
	skos:narrowerTransitive owl:disjointWith  skos:broaderTransitive .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//memberList
//For any resource, every item in the list given as the value of the skos:memberList property is also a value of the skos:member property.


//mappingRelation
//Attention pour relier des concepts mais appartenant à des schémas différents
$q = $prefix.'
INSERT INTO <skos>
{
	skos:mappingRelation rdfs:range skos:Concept .
	skos:mappingRelation rdfs:domain skos:Concept
}
';

$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}
//broadMatch
//Attention pour relier des concepts mais appartenant à des schémas différents
$q = $prefix.'
INSERT INTO <skos>
{
	skos:broadMatch rdfs:range skos:Concept .
	skos:broadMatch rdfs:domain skos:Concept .
	skos:broadMatch owl:disjointWith  skos:exactMatch .
	skos:broadMatch owl:disjointWith  skos:relatedMatch .
	skos:broadMatch owl:disjointWith  skos:narrowMatch .
	skos:broadMatch owl:disjointWith  skos:closeMatch  .
}
';

$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//narrowMatch
//Attention pour relier des concepts mais appartenant à des schémas différents
$q = $prefix.'
INSERT INTO <skos>
{
	skos:narrowMatch rdfs:range skos:Concept .
	skos:narrowMatch rdfs:domain skos:Concept .
	skos:narrowMatch owl:disjointWith  skos:exactMatch .
	skos:narrowMatch owl:disjointWith  skos:relatedMatch .
	skos:narrowMatch owl:disjointWith  skos:broadMatch .
	skos:narrowMatch owl:disjointWith  skos:closeMatch  .
}
';

$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//relatedMatch
//Attention pour relier des concepts mais appartenant à des schémas différents
$q = $prefix.'
INSERT INTO <skos>
{
	skos:relatedMatch rdfs:range skos:Concept .
	skos:relatedMatch rdfs:domain skos:Concept .
	skos:relatedMatch owl:disjointWith  skos:exactMatch .
	skos:relatedMatch owl:disjointWith  skos:narrowMatch .
	skos:relatedMatch owl:disjointWith  skos:broadMatch .
	skos:relatedMatch owl:disjointWith  skos:closeMatch  .
}
';

$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}


//exactMatch
//Attention pour relier des concepts mais appartenant à des schémas différents
$q = $prefix.'
INSERT INTO <skos>
{
	skos:exactMatch rdfs:range skos:Concept .
	skos:exactMatch rdfs:domain skos:Concept .
	skos:exactMatch owl:disjointWith  skos:relatedMatch .
	skos:exactMatch owl:disjointWith  skos:narrowMatch .
	skos:exactMatch owl:disjointWith  skos:broadMatch .
	skos:exactMatch owl:disjointWith  skos:closeMatch  .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//closeMatch
//Attention pour relier des concepts mais appartenant à des schémas différents
$q = $prefix.'
INSERT INTO <skos>
{
	skos:closeMatch rdfs:range skos:Concept .
	skos:closeMatch rdfs:domain skos:Concept .
	skos:closeMatch owl:disjointWith  skos:relatedMatch .
	skos:closeMatch owl:disjointWith  skos:narrowMatch .
	skos:closeMatch owl:disjointWith  skos:broadMatch .
	skos:closeMatch owl:disjointWith  skos:exactMatch  .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
  echo "Erreurs: \n";
  print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

// semanticRelation, broaderTransitive, narrowerTransitive, mappingRelation
// par convention, ne sont pas utilisées pour réaliser des declarations
$q = $prefix.'
INSERT INTO <skos>
{
skos:semanticRelation rdf:type pmb:noAssertionProperty .
skos:broaderTransitive rdf:type pmb:noAssertionProperty .
skos:narrowerTransitive rdf:type pmb:noAssertionProperty .
skos:mappingRelation rdf:type pmb:noAssertionProperty .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
	echo "Erreurs: \n";
	print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//association entre classes, proprietes et types de données dans pmb
$q = $prefix.'
INSERT INTO <skos>
{
skos:prefLabel pmb:datatype pmb:small_text .
skos:altLabel pmb:datatype pmb:small_text .
skos:hiddenLabel pmb:datatype pmb:small_text .
skos:notation pmb:datatype pmb:small_text .
skos:note pmb:datatype pmb:text .
skos:changeNote pmb:datatype pmb:text .
skos:definition pmb:datatype pmb:text .
skos:editorialNote pmb:datatype pmb:text .
skos:example pmb:datatype pmb:text .
skos:historyNote pmb:datatype pmb:text .
skos:scopeNote pmb:datatype pmb:text .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
	echo "Erreurs: \n";
	print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}


//ajout du libellé devant être affiché (par exemple dans une liste) 
$q = $prefix.'
INSERT INTO <skos>
{
skos:ConceptScheme pmb:displayLabel skos:prefLabel .
skos:Concept pmb:displayLabel skos:prefLabel .
skos:Collection pmb:displayLabel skos:prefLabel .
skos:OrderedCollection pmb:displayLabel skos:prefLabel .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
	echo "Erreurs: \n";
	print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//ajout du libellé devant être affiché (par exemple dans une liste) 
$q = $prefix.'
INSERT INTO <skos>
{
skos:ConceptScheme pmb:searchLabel skos:prefLabel .
skos:Concept pmb:searchLabel skos:prefLabel .
skos:Collection pmb:searchLabel skos:prefLabel .
skos:OrderedCollection pmb:searchLabel skos:prefLabel .
skos:ConceptScheme pmb:searchLabel skos:altLabel .
skos:Concept pmb:searchLabel skos:altLabel .
skos:Collection pmb:searchLabel skos:altLabel .
skos:OrderedCollection pmb:searchLabel skos:altLabel .
}
';
$rows = $store->query($q, '','',1);
if ($errs = $store->getErrors()) {
	echo "Erreurs: \n";
	print_r($errs);
}else{
	echo "Nb insert ok: ".$rows["result"]["t_count"]."\n\n\n";
}

//requete de sélection pour les cardinalités des classes
$q = $prefix.'
SELECT ?class  ?y ?z
WHERE {
    ?class a owl:Class .
    ?class rdfs:subClassOf ?x .
    ?x ?y ?z .
	?x owl:onProperty skos:inScheme .
	FILTER (isURI(?class))  .
	FILTER (isLiteral(?z)) 
}		
';
$rows = $store->query($q,"rows");
if ($rows) {
	print_r($rows);
}


//Export RDF
$ns = array(
  'owl' => 'http://www.w3.org/2002/07/owl#',
  'skos' => 'http://www.w3.org/2004/02/skos/core#',
  'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
  'dct' => 'http://purl.org/dc/terms/',
  'base' => 'http://www.w3.org/2004/02/skos/core',
  'pmb' => 'http://www.pmbservices.fr/ontology#',
  'xsd' => 'http://www.w3.org/2001/XMLSchema#'
);
$conf = array('ns' => $ns);
$ser = ARC2::getRDFXMLSerializer($conf);
$all = $store->query("SELECT ?s ?p ?o WHERE { ?s ?p ?o }");
$rdfxml2 = $ser->getSerializedTriples($all["result"]['rows']);  
file_put_contents('skos_pmb.rdf', $rdfxml2);

$q = $prefix.'
SELECT DISTINCT ?scheme ?top
WHERE {
    ?scheme skos:hasTopConcept ?top .
}
';

?>
