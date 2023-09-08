<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

include_once 'DB.php';
include_once 'lib_param.php';
include_once 'timezone.php';
include_once 'conf_error.php';
include_once 'lib_prefixe.php';


global $prefixe;
global $cnx;
global $dsn;
global $ERROR;

function cnx_ajax() {
	global $dsn;
	global $prefixe;
	$cnx = DB::connect($dsn);
	if(DB::isError($cnx))
	{
		//exit($cnx->getMessage());
	}
	else
	{
		return $cnx;
	}
}

function execSql_ajax($sql) {
	global $cnx;
	global $ERROR;
	global $prefixe;
	$res = $cnx->query($sql);
	if(DB::isError($res))
	{
		 if (($res->getMessage() != "DB Error: already exists") && ($res->getMessage() != "DB Error: unknown error")) {
			$fichier="./data/erreurs.log";
       	        	$texte  = dateDMY()." à ".dateHIS();
			$texte .= "<br>Base de type : " . DBTYPE ;
       	        	$texte .= "<br>Fichier : <b>".$_SERVER["PHP_SELF"]."</b><br />\n";
       	        	$texte .= "Notice sur la ligne :<br>";
       	        	$texte .= "<i>$sql</i><br>";
			$texte .= $res->getMessage()."<br>";
			$texte .= "<hr><br>";
       	        	$fichier=fopen($fichier,"a");
       	        	fwrite($fichier,$texte);
       	        	fclose($fichier);
		 }

		if ($ERROR == "true")  {
               		print("<font color=\"red\"><b>$sql</b></font><br><br>");
	       		print $res->getMessage();
	        }
		Pgclose_ajax();
	}
	else {
		// print $sql."<br>";
		return $res;
	}
}

function Pgclose_ajax(){
	global $cnx;
	global $prefixe;
	$close=$cnx->disconnect();
	if(DB::isError($close))
	{
//		exit($close->getMessage());
	}
	else
	{
		return(true);
	}
}


/**
* Libérer un résultat sql
*
* @param object Objet de type ResultSet
* @return bool
*/
function freeResult_ajax($resultSet) {
	return($resultSet->free());
}
#----------------------------------------------------------------
// chargement du résultat d une requête SQL dans une matrice (tableau)
function chargeMat2_ajax($res) {
	$c = $res->numCols();
	$l = $res->numRows();
	for($i=0;$i<$l;$i++)
	{
		$ligne = & $res->fetchRow();
		$mat[$i] = $ligne[0];
	}
	freeResult_ajax($res);
	return $mat;
}

function recherche_ajax_eleve($motif,$prefixe) {
	global $cnx;
	$sql="SELECT nom FROM ${prefixe}eleves WHERE  nom LIKE '$motif%' ORDER BY nom ";
	$res=execSql_ajax($sql);
  	$data=chargeMat2_ajax($res);
   	return $data;
}


function recherche_ajax_entreprise($motif,$prefixe) {
	global $cnx;
	$sql="SELECT nom FROM ${prefixe}stage_entreprise  WHERE  nom LIKE '$motif%' ORDER BY nom ";
	$res=execSql_ajax($sql);
  	$data=chargeMat2_ajax($res);
   	return $data;
}


function recherche_ajax_matiere($motif,$prefixe) {
        global $cnx;
        $sql="SELECT CONCAT(libelle,' ',sous_matiere) FROM ${prefixe}matieres  WHERE  lower(CONCAT(libelle,' ',sous_matiere)) LIKE '$motif%' AND offline='0'";
        $res=execSql_ajax($sql);
        $data=chargeMat2_ajax($res);
        return $data;
}



function recherche_ajax_sanction($motif,$prefixe) {
	global $cnx;
	$sql="SELECT libelle FROM  ${prefixe}type_sanction  WHERE  id_category='$motif' ORDER BY 1 ";
	$res=execSql_ajax($sql);
  	$data=chargeMat2_ajax($res);
   	if (count($data) > 0) {
  		return $data;
  	}else{
  		return array();
  	}
}

function sansaccent($chaine) {
   return strtr($chaine,'àâäåãáÂÄÀÅÃÁæÆçÇéèêëÉÊËÈïîìíÏÎÌÍñÑöôóòõÓÔÖÒÕùûüúÜÛÙÚÿ','aaaaaaaaaaaaaacceeeeeeeeiiiiiiiinnoooooooooouuuuuuuuy');
}


function sansaccentmajuscule($chaine) {
   return strtr($chaine,'ÂÄÀÅÃÁÇÉÊËÈÏÎÌÍÑÓÔÖÒÕÜÛÙÚ','AAAAAACEEEEIIIINOOOOOUUUU');
}

