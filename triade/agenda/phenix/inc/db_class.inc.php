<?php
  /**************************************************************************\
  * Phenix Agenda                                                            *
  * http://phenix.gapi.fr                                                    *
  * Written by    Stephane TEIL            <phenix-agenda@laposte.net>       *
  * Contributors  Christian AUDEON (Omega) <christian.audeon@gmail.com>      *
  *               Maxime CORMAU (MaxWho17) <maxwho17@free.fr>                *
  *               Mathieu RUE (Frognico)   <matt_rue@yahoo.fr>               *
  *               Bernard CHAIX (Berni69)  <ber123456@free.fr>               *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

// ----------------------------------------------------------------------------
// Connexion a la BDD
// ----------------------------------------------------------------------------


  $DB_CX = new Db();
  if (!$DB_CX->DbConnect($cfgHote, $cfgUser, $cfgPass, $cfgBase)) {
    serveurDown();
  }

// ----------------------------------------------------------------------------
// Classe gerant la connexion a la BDD
// ----------------------------------------------------------------------------
  class Db {
    var $ConnexionID;
    var $DatabaseName;
    var $Result;
    var $Row;
    var $cpt;

// CONSTRUCTEUR
    function Db($cxID = 0) {
      $this->ConnexionID = $cxID;
      return ( $this->ConnexionID );
    }

// METHODES PUBLIQUES
    function DbConnect($host, $user, $passwd, $database) {
      $this->ConnexionID = @mysqli_connect($host, $user, $passwd, $database);
 
    if(mysqli_connect_error())
        return( false );
    else
      	if ($database)
        	return( $this->DbSelectDatabase($database) );
    }

function DbSelectDatabase($database) {
      $this->DatabaseName = $database;
      if ($this->ConnexionID) {
        return @mysqli_select_db($this->ConnexionID,$database);
      }
      else {
        return false;
      }
}

    function DbQuery($query, $start = '', $limit = '') {
      if ($start != '' || $limit != '') {
        $query .= ' LIMIT '.$start.','.$limit;
      }
      $this->Result = @mysqli_query($this->ConnexionID,$query);
      $this->cpt = $this->cpt+1;
      return( $this->Result );
    }

    function DbNextID($table,$champ) {
      $this->DbQuery("SELECT MAX(".$champ.") FROM ".$table);
      return( $this->DbResult(0,0)+1 );
    }

    function DbNumRows() {
      return( @mysqli_num_rows($this->Result) );
    }

    function DbAffectedRows() {
      return( @mysqli_affected_rows($this->ConnexionID) );
    }

    function DbInsertID() {
      return( @mysqli_insert_id($this->ConnexionID) );
    }

    function DbDataSeek($line = 0) {
      return( @mysqli_data_seek($this->Result,$line) );
    }

    function DbNextRow() {
      $this->Row = @mysqli_fetch_array($this->Result);
      return( $this->Row );
    }

    function DbResult($row,$field) {
	$i=0; 
	mysqli_data_seek($this->Result,0);
	while($results=mysqli_fetch_array($this->Result)){
		if ($i==$row){$result=$results[$field];}
		$i++;
	}
	return $result;
     // return( @mysqli_result($this->Result,$row,$field) );
    }

    function DbNumFields() {
      return( @mysqli_num_fields($this->Result) );
    }

    function DbFieldName($field) {
      return( @mysqli_fetch_field_direct($this->Result,$field) );
    }

    function DbListTable($base) {
      $this->Result = @mysqli_list_tables($base,$this->ConnexionID);
      return( $this->Result ); 
    }

    function DbTableName($table) {
      return( @mysqli_tablename($this->Result,$table) ); 
    }

    function DbError() {
      return( @mysqli_error($this->ConnexionID) );
    }

    function DbErrorNo() {
      return( @mysqli_errno($this->ConnexionID) );
    }

    function DbNbReq() {
      return( $this->cpt );
    }

    function DbDeconnect() {
      return( @mysqli_close($this->ConnexionID) );
    }
}


?>
