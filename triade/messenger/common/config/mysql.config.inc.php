<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2010 THeUDS           **
 **  Web:            http://www.theuds.com            **
 **                  http://www.intramessenger.net    **
 **  Licence :       GPL (GNU Public License)         **
 **  http://opensource.org/licenses/gpl-license.php   **
 *******************************************************/

/*******************************************************
 **       This file is part of IntraMessenger-server  **
 **                                                   **
 **  IntraMessenger is a free software.               **
 **  IntraMessenger is distributed in the hope that   **
 **  it will be useful, but WITHOUT ANY WARRANTY.     **
 *******************************************************/

if ( !defined('INTRAMESSENGER') ) die();

###########################
## FR : Paramètres Mysql ##
## EN : MySQL parameters ##
###########################

include_once("../../common/config.inc.php");

$dbhost     = HOST;
## EN : Mysql Database host (localhost if MySQL engine on same server Apache)
## FR : Nom d'hote (localhost quand la base MySQL est sur le serveur Apache)
## IT : Nome del host MySQL (localhost se MySQL engine ï¿½ nello stesso server di Apache)

$database   = DB ;
## EN : database (need to create before)
## FR : Base de donnï¿½e utilisï¿½e (ï¿½ crï¿½er avant)
## IT : Nome del DB (da creare prima)

$dbuname    = USER ;
## EN : Mysql user
## FR : utilisateur MySQL
## IT : Nome del utente Mysql

$dbpass     = PWD ;
## EN : MySQL password (for dbuname user)
## FR : Mot de passe d'accï¿½s ï¿½ MySQL (pour l'utilisateur dbuname)
## IT : Password MySQL (per l'utente MySQL sopradetto)

$PREFIX_IM_TABLE = PREFIXE."im_";

?>
