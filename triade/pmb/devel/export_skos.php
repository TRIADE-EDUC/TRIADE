<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: export_skos.php,v 1.6 2023/10/17 14:18:55 tsamson Exp $

$base_path="..";
$base_noheader = 1;
$base_nocheck = 1;
$base_nobody = 1;
$base_nosession =1;

require_once($base_path."/includes/init.inc.php");

$numt = intval($_GET["thesaurus"]);
$prefix=$_GET["prefix"]; //http://http://www.ressources-de-la-formation.fr
$tname=$_GET["tname"]; //thesaurus_formation

header("Content-Type: application/xml");
header('Content-Disposition: attachment; filename="'.$tname.'.xml"');

//ARC2 attend des infos en UTF-8
pmb_mysql_query("SET NAMES 'UTF8'");

$res=pmb_mysql_query("select * from thesaurus where id_thesaurus=".$numt);
$rt=pmb_mysql_fetch_object($res);

$thesaurus=array(
			'rdf:type'=>array('http://www.w3.org/2004/02/skos/core#ConceptScheme'),
			'http://www.w3.org/2004/02/skos/core#prefLabel'=>$rt->libelle_thesaurus,
		);

$th=array();

$requete="select id_noeud from noeuds where autorite='ORPHELINS' and num_thesaurus=".$numt;
$orph=pmb_mysql_result(pmb_mysql_query($requete),0,0);

$requete="select id_noeud,autorite, num_parent, num_renvoi_voir from noeuds where num_thesaurus=".$numt;
$res=pmb_mysql_query($requete);

while ($rc=pmb_mysql_fetch_object($res)) {
	$categ=array();
	if (($rc->num_parent!=$orph)&&($rc->id_noeud!=$rt->num_noeud_racine)&&($rc->id_noeud!=$orph)) {
		$categ['rdf:type']=array('http://www.w3.org/2004/02/skos/core#Concept');
		$categ['http://www.w3.org/2004/02/skos/core#inScheme']=array($prefix.'/skos/'.$tname);
		if ($rc->num_parent==$rt->num_noeud_racine) {
			$thesaurus['http://www.w3.org/2004/02/skos/core#hasTopConcept'][]=$prefix.'/skos/concept#'.$rc->id_noeud;
			$categ['http://www.w3.org/2004/02/skos/core#topConceptOf']=array($prefix.'/skos/'.$tname);
		}
		//Insertion du prefLabel
		$requete="select * from categories where num_noeud=".$rc->id_noeud;
		$res_categ=pmb_mysql_query($requete);
		while ($rcl=pmb_mysql_fetch_object($res_categ)) {
			$categ['http://www.w3.org/2004/02/skos/core#prefLabel'][]=array("value"=>$rcl->libelle_categorie,"type"=>"literal","lang"=>substr($rcl->langue,0,2));
			if ($rcl->note_application) {
				$categ['http://www.w3.org/2004/02/skos/core#definition'][]=array("value"=>$rcl->note_application,"type"=>"literal","lang"=>substr($rcl->langue,0,2));
			}
			if ($rcl->comment_public) {
				$categ['http://www.w3.org/2004/02/skos/core#editorialNote'][]=array("value"=>$rcl->comment_public,"type"=>"literal","lang"=>substr($rcl->langue,0,2));
			}
			
		}
		//Insertion du parent
		if ($rc->num_parent!=$rt->num_noeud_racine) {
			$categ['http://www.w3.org/2004/02/skos/core#broader']=array($prefix.'/skos/concept#'.$rc->num_parent);
			$th[$prefix.'/skos/concept#'.$rc->num_parent]['http://www.w3.org/2004/02/skos/core#narrower'][]=$prefix.'/skos/concept#'.$rc->id_noeud;
		}
		//Insertion des voir aussi...
		$requete="select num_noeud_dest from voir_aussi where num_noeud_orig=".$rc->id_noeud;
		$res_voir_aussi=pmb_mysql_query($requete);
		while ($rcv=pmb_mysql_fetch_object($res_voir_aussi)) {
			$categ['http://www.w3.org/2004/02/skos/core#related'][]=$prefix.'/skos/concept#'.$rcv->num_noeud_dest;
		}
		//Insert des altLabel
		$requete="select id_noeud from noeuds where num_parent=".$orph." and num_renvoi_voir=".$rc->id_noeud;
		$res_alt=pmb_mysql_query($requete);
		while ($rcalt=pmb_mysql_fetch_object($res_alt)) {
			$res_altlabels=pmb_mysql_query("select * from categories where num_noeud=".$rcalt->id_noeud);
			while ($rcaltl=pmb_mysql_fetch_object($res_altlabels)) {
				$categ['http://www.w3.org/2004/02/skos/core#altLabel'][]=array("value"=>$rcaltl->libelle_categorie,"type"=>"literal","lang"=>substr($rcaltl->langue,0,2));
			}
		}
		foreach($categ as $key=>$val) {
			$th[$prefix.'/skos/concept#'.$rc->id_noeud][$key]=$val;
		}
	} else {
		
	}
}

$th[$prefix.'/skos/'.$tname]=$thesaurus;

$parser=ARC2::getRDFParser();
print $parser->toRDFXML($th);

