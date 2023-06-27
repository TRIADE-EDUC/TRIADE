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
//
// fonctions métiers de Triade
//
// HOST, USER, DB, PWD définies dans ../common/config.inc.php
//


	include_once("../magpierss/rss_fetch.inc");



//-------------------------------------------------------------------------//
//-------------------------------------------------------------------------//
// configuration du nombre de sanction pour la mise en place d'une retenue
function ajoutRss($idpers,$url,$membre) {
        global $cnx;
	global $prefixe;
	include_once("./magpierss/rss_fetch.inc");

	$sql="SELECT idpers,membre,url  FROM ${prefixe}rssgen WHERE idpers='$idpers' AND membre='$membre' AND url='$url'";
	$res=execSql($sql);
        $data=chargeMat($res);
	if (count($data) > 0) {
		return ;
	}
	$sql="INSERT INTO ${prefixe}rssgen (idpers,membre,url) VALUES ('$idpers','$membre','$url')";
	execSql($sql);
}
/*
	$rss=fetch_rss($url);
	$datechannel=$rss->channel['lastbuilddate'];
	foreach ($rss->items as $item) {
		$href 	= $item['link'];
		$title 	= $item['title'];
		$date 	= $item['pubdate'];
		if (trim($date) == "") {
			$date=$datechannel;
		}
		if (trim($date) == "") {
			$date=date("r");
		}
		$title=addslashes($title);
	        $sql="INSERT INTO ${prefixe}rss (idgen,conx,datemodif,title) VALUES ('$id','non','$date','$title')";
       		execSql($sql);
	}
}
 */
//-------------------------------------------------------------------------//
function consultRss($idpers,$membre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id,idpers,membre,url  FROM ${prefixe}rssgen WHERE idpers='$idpers' AND membre='$membre'";
	$res=execSql($sql);
	$data=chargeMat($res);
        unset($sql);
        return $data;
}
//-------------------------------------------------------------------------//
function miseAjour($title,$idrss) {
	global $cnx;
	global $prefixe;
	$title=addslashes($title);
	$sql="SELECT idgen  FROM ${prefixe}rss WHERE title='$title' AND idgen='$idrss' ";
	$res=execSql($sql);
        $data=chargeMat($res);
	if (count($data) > 0) {
		$sql="UPDATE ${prefixe}rss SET conx='oui' WHERE title='$title' AND idgen='$idrss' ";
		execSql($sql);
		return $data;
	}else{
		$sql="INSERT INTO ${prefixe}rss (idgen,conx,title) VALUES ('$idrss','oui','$title')";
       		execSql($sql);
	}
        unset($sql);
       
}

function recupUrl($idRss) {
	global $cnx;
	global $prefixe;
	$sql="SELECT url  FROM ${prefixe}rssgen WHERE id='$idRss' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data[0][0]);
}

function rechercheId($url,$idpers,$membre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id  FROM ${prefixe}rssgen WHERE idpers='$idpers' AND membre='$membre' AND url='$url'";
	$res=execSql($sql);
        $data=chargeMat($res);
	return $data[0][0];

}

//-------------------------------------------------------------------------//
function suppRss($url,$idpers,$membre) {
        global $cnx;
        global $prefixe;
        $idRss=rechercheId($url,$idpers,$membre);
        if ($idRss > 0) {
                $sql="DELETE FROM ${prefixe}rss WHERE idgen='$idRss' ";
                execSql($sql);
        }
        $sql="DELETE FROM ${prefixe}rssgen WHERE url='$url' AND idpers='$idpers' AND  membre='$membre' ";
        execSql($sql);
}

//-------------------------------------------------------------------------//
//
//
function idRss($url,$idpers,$membre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id  FROM ${prefixe}rssgen WHERE idpers='$idpers' AND membre='$membre' AND url='$url'";
	$res=execSql($sql);
	$data=chargeMat($res);
	return($data[0][0]);

}	
//
function RssDejaLu($title,$url,$idpers,$membre) {
	global $cnx;
	global $prefixe;
	$sql="SELECT id  FROM ${prefixe}rssgen WHERE idpers='$idpers' AND membre='$membre' AND url='$url'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$id=$data[0][0];

	$title=addslashes($title);
	$sql="SELECT idgen,conx FROM ${prefixe}rss WHERE title='$title' AND idgen='$id'";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) > 0) {
		return $data[0][1];
	}else{
		return "non";
	}

}




?>
