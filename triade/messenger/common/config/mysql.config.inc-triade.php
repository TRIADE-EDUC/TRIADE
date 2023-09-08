<?php
/*******************************************************
 **                  IntraMessenger 				          **
 **                                                   **
 **  Copyright:         (C) 2007 - 2008 THeUDS        **
 **  Web:               http://www.theuds.com         **
 *******************************************************/

/*******************************************************
 **       This file is part of IntraMessenger.        **
 **                                                   **
 **  IntraMessenger is free software.  			          **
 **  IntraMessenger is distributed in the hope that   **
 **  it will be useful, but WITHOUT ANY WARRANTY.     **
 *******************************************************/

###########################
## FR : Param�tres Mysql ##
## EN : MySQL parameters ##
###########################

include_once("../../common/config.inc.php");

$dbhost     = HOST;
## EN : Mysql Database host (localhost if MySQL engine on same server Apache)
## FR : Nom d'hote (localhost quand la base MySQL est sur le serveur Apache)
## IT : Nome del host MySQL (localhost se MySQL engine � nello stesso server di Apache)

$database   = DB ;
## EN : database (need to create before)
## FR : Base de donn�e utilis�e (� cr�er avant)
## IT : Nome del DB (da creare prima)

$dbuname    = USER ;
## EN : Mysql user
## FR : utilisateur MySQL
## IT : Nome del utente Mysql

$dbpass     = PWD ;
## EN : MySQL password (for dbuname user)
## FR : Mot de passe d'acc�s � MySQL (pour l'utilisateur dbuname)
## IT : Password MySQL (per l'utente MySQL sopradetto)

$PREFIX_IM_TABLE = PREFIXE."im_";
//$PREFIX_IM_TABLE = "IM_";

?>
