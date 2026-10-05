<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: export_z3950.php,v 1.17 2023/10/17 14:18:55 tsamson Exp $

$base_path="../..";

$base_nocheck=1;
$base_noheader=1;
$base_nobody=1;
$base_nosession=1;

include($base_path."/includes/init.inc.php");
include($base_path."/admin/convert/export.class.php");
include($base_path."/admin/convert/xml_unimarc.class.php");
require_once($base_path."/includes/isbn.inc.php");

function make_error($nerr,$err_message) {
	echo htmlentities($nerr."@".$err_message."@");
	exit();
}

$mysql_connect = @pmb_mysql_connect(SQL_SERVER,USER_NAME,USER_PASS);
if (!$mysql_connect) {
	make_error(1,"Could'nt connect to database server");
}
if (!@pmb_mysql_select_db(DATA_BASE, $mysql_connect)) {
	make_error(2,"Database unknown");
}

//Commande envoyée
$command=$_GET["command"];
//Requete
$query=$_GET["query"];
function construct_query($query,$not,$level,$argn="") {
    global $default_tmp_storage_engine;

	//La requête commence-t-elle par and, or ou and not ?
    $query = stripslashes($query);
    
	$pos=strpos($query,"and not");
	if (($pos!==false)&&($pos==0)) {
		$ope="and not";
	} else {
		$pos=strpos($query,"or");
		if (($pos!==false)&&($pos==0)) {
			$ope="or";
		} else {
			$pos=strpos($query,"and");
			if (($pos!==false)&&($pos==0)) {
				$ope="and";
			} else $ope="";
		}
	}
	if ($ope!="") {
		//Si opérateur, recherche des arguments
		$arqs=array();
		preg_match("/^".$ope." arg".$level."!1\((.*)\) arg".$level."!2\((.*)\)$/",$query,$args);
		$return1=construct_query($args[1],0,$level+1,1);
		if ($ope=="and not")
			$return2=construct_query($args[2],1,$level+1,2);
		else
			$return2=construct_query($args[2],0,$level+1,2);
		if ($ope=="and not") $ope="and";
		$requete="create temporary table r$level ENGINE={$default_tmp_storage_engine} ";
		if ($ope=="and") {
			$requete.="select distinct $return1.notice_id from $return1, $return2 where $return1.notice_id=$return2.notice_id";
			@pmb_mysql_query($requete);
		} else {
			$requete.="select distinct notice_id from $return1";
			@pmb_mysql_query($requete);
			$requete="insert into r$level select distinct notice_id from $return2 ";
			@pmb_mysql_query($requete);
		}
		$return="r$level";
	} else {
		$use=explode("=",$query);
		$use[1]=pmb_mysql_escape_string($use[1]);
		switch ($use[0]) {
			//Titre
			case 4:
				if ($not)
					$requete="select distinct notice_id from notices where (index_wew not like '%".$use[1]."%' )";
				else
					$requete="select distinct notice_id from notices where (index_wew like '%".$use[1]."%' )";
				break;
			//ISBN
			case 7:
			    if(isISBN($use[1])) {
					// si la saisie est un ISBN
					$code = formatISBN($use[1]);
					// si échec, ISBN erroné on le prend sous cette forme
					if(!$code) $code = $use[1];
			    } else $code = $use[1];
				if ($not)
					$requete="select notice_id from notices where (code!='".$code."')";
				else
					$requete="select notice_id from notices where (code='".$code."')";
				break;
			// Auteur
			case 1003:
				if ($not) {
				    	$requete="create temporary table aut ENGINE={$default_tmp_storage_engine} select distinct responsability.responsability_notice as notice_id, index_author as auth from authors, responsability where responsability_author = author_id ";
				    	@pmb_mysql_query($requete);
				    	$requete="select distinct notice_id from aut where auth not like '%".$use[1]."%'";
				}
				else 
					$requete="select distinct notice_id from responsability, authors, notices where index_author like '%".$use[1]."%' and author_id=responsability_author and notice_id=responsability_notice ";
				break;
			default:
				make_error(3,"1=".$use[0]);
				break;
		}
		$requete="create temporary table r".$level."_".$argn." ENGINE={$default_tmp_storage_engine} ".$requete;
		@pmb_mysql_query($requete);
		$return="r".$level."_".$argn;
	}
	return $return;
}

switch ($command) {
	case "search":
		$sup_tables="";
		$sql_query=construct_query($query,0,0);
		$sql_query="select notice_id from $sql_query limit 100";
		$resultat=@pmb_mysql_query($sql_query);
		echo "0@No errors@";
		echo @pmb_mysql_num_rows($resultat);
		while (list($id)=@pmb_mysql_fetch_row($resultat)) {
			echo "@$id";
		}
		break;
	case "get_notice":
		$id=intval($query);
		$e = new export(array($id));
		$e -> get_next_notice();
		$toiso = new xml_unimarc();
		$toiso->XMLtoiso2709_notice($e->notice);
		echo "0@No errors@";
		echo $toiso->notices_[0];
		break;
}

?>
