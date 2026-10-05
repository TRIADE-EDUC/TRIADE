<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.6 2024/03/22 15:31:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $id;
global $liens_gestion, $sub, $class_path;

require_once($class_path."/notice.class.php");

/**
 * Page de consultation d'une autorité
 */
//TODO - Redéfinir ce tableau selon les droits de l'utilisateur
$allowed_authorities = array(
	"author",
	"category",
	"publisher",
	"collection",
	"subcollection",
	"serie",
	"titre_uniforme",
	"indexint",
	"concept",
	"authperso"
);

$id = intval($id);
$authority_page = null;

notice::init_globals_patterns_links();

$liens_gestion = array(
		'lien_auteur' => "./autorites.php?categ=see&sub=author&id=!!id!!",
		'lien_categ' => "./autorites.php?categ=see&sub=category&id=!!id!!",
		'lien_editeur' => "./autorites.php?categ=see&sub=publisher&id=!!id!!",
		'lien_collection' => "./autorites.php?categ=see&sub=collection&id=!!id!!",
		'lien_subcollection' => "./autorites.php?categ=see&sub=subcollection&id=!!id!!",
		'lien_serie' => "./autorites.php?categ=see&sub=serie&id=!!id!!",
		'lien_titre_uniforme' => "./autorites.php?categ=see&sub=titre_uniforme&id=!!id!!",
		'lien_indexint' => "./autorites.php?categ=see&sub=indexint&id=!!id!!",
		'lien_authperso' => "./autorites.php?categ=see&sub=authperso&id=!!id!!"
 );

//On s'assure que l'on peut afficher l'autorité demandée
if(!empty($sub) && in_array($sub, $allowed_authorities) && $id>0){
	switch ($sub){
		case "author":
			require_once($class_path."/authorities/page/authority_page_author.class.php");
			$authority_page = new authority_page_author($id);
			break;
		case "category" :
			require_once($class_path."/authorities/page/authority_page_category.class.php");
			$authority_page = new authority_page_category($id);
			break;
		case "publisher":
			require_once($class_path."/authorities/page/authority_page_publisher.class.php");
			$authority_page = new authority_page_publisher($id);
			break;
		case "collection" :
			require_once($class_path."/authorities/page/authority_page_collection.class.php");
			$authority_page = new authority_page_collection($id);
			break;
		case "subcollection" :
			require_once($class_path."/authorities/page/authority_page_subcollection.class.php");
			$authority_page = new authority_page_subcollection($id);
			break;
		case "serie" :
			require_once($class_path."/authorities/page/authority_page_serie.class.php");
			$authority_page = new authority_page_serie($id);
			break;
		case "titre_uniforme" :
			require_once($class_path."/authorities/page/authority_page_titre_uniforme.class.php");
			$authority_page = new authority_page_titre_uniforme($id);
			break;
		case "indexint" :
			require_once($class_path."/authorities/page/authority_page_indexint.class.php");
			$authority_page = new authority_page_indexint($id);
			break;
		case "concept" :
			$authority_page = new skos_page_concept($id);
			break;
		case "authperso" :
			/**
			 * A voir avec AR too
			 */
			require_once($class_path."/authorities/page/authority_page_authperso.class.php");
			$authority_page = new authority_page_authperso($id);		
			break;
	}
}
if(is_object($authority_page)){
	$authority_page->proceed();
}else{
	//Autorité non existante ou pas autorisée pour l'usager
	include('./autorites/authors/authors.inc.php');
}