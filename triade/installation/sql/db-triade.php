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

// int(10) unsigned

include_once("../common/config.inc.php");
include_once("DB.php");


function cnx() {
        global $dsn;
        $cnx =& DB::connect($dsn);
        if(DB::isError($cnx)) {
		print "<br>&nbsp;&nbsp;&nbsp;Erreur d'accès à la base :".$cnx->getMessage();
                print "<br>&nbsp;&nbsp;&nbsp;Code erreur : ".$cnx->getCode();
                print "<br>&nbsp;&nbsp;&nbsp;Info erreur : ".$cnx->getDebugInfo();
                print "<br>&nbsp;&nbsp;&nbsp;Verififier le login et le mot de passe";
                print "<br>&nbsp;&nbsp;&nbsp;Verififier si la base est bien créée";		
		exit;
        }
        else
        {
                return $cnx;
        }
}


function execSql($sql) {
        global $cnx;

        // $sql=MyAddSlashes($sql);

        $res =& $cnx->query($sql);
        if(DB::isError($res))
        {
		
               	if (ereg("^DROP",$sql)) {
			//rien
		}else {	
               		print "<br>$sql</br>";
	               	Pgclose();
			print "<b>Warning SQL</b><br>";
		        print "Problème d'accès à votre serveur SQL, pour corriger cliquez sur le bouton suivant: ";
		        print "<br><br>";
		        print "<input type=button value='Réparer le requête SQL' onclick=\"history.go(0);\" >";
		        print "<br><br>";

                exit($res->getMessage()." ".$res->getCode()." ".$res->getDebugInfo());
        	}
        }
        else {
                return $res;
        }
}





function Pgclose(){
        global $cnx;
        $close=$cnx->disconnect();
        if(DB::isError($close)) {
                exit($close->getMessage());
        }
        else
        {
                return(true);
        }
}

function chargeMat($res) {
        $c = $res->numCols();
        $l = $res->numRows();
        for($i=0;$i<$l;$i++)
        {
                $ligne = & $res->fetchRow();
                for($j=0;$j<$c;$j++)
                {
                        $mat[$i][$j] = $ligne[$j];
                }
        }
        return $mat;
}


function MyAddSlashes($chaine ) {
  return( get_magic_quotes_gpc() == 1 ? $chaine : addslashes($chaine) );
}


function MyStripSlashes($chaine) {
  return( get_magic_quotes_gpc() == 1 ? stripslashes($chaine) : $chaine );
}

function miseAjourBase() {
	global $cnx;
	global $prefixe;
	// ----------------------------------------------------------------------------------------------------------------------------------
	$sql="TRUNCATE TABLE ${prefixe}news_admin";
	execSql($sql);
	$sql="INSERT INTO ${prefixe}news_admin (nom,prenom,date,heure,titre,texte,type,config_video) VALUES ('Triade','Support','$date',$heure','Essai Vidéo','<br><br><table align=\'center\' style=\'box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75);\' ><tr><td><object width=\'420\' height=\'280\'><param name=\'movie\' value=\'http://www.youtube.com/watch?v=_R5IQoIYvTM\'></param><param name=\'allowFullScreen\' value=\'false\'></param><param name=\'allowscriptaccess\' value=\'always\'></param><embed src=\'http://www.youtube.com/watch?v=_R5IQoIYvTM\'  type=\'application/x-shockwave-flash\' allowscriptaccess=\'always\' allowfullscreen=\'false\' width=\'420\' height=\'280\'></embed></object></td></tr></table><br /><br />','video','')";
	execSql($sql);
	// ------------------------------------------------------------------------------------------------------------------------------------
}

