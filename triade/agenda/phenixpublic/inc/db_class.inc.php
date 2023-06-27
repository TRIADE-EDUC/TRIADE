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
      $this->ConnexionID = @mysql_connect($host, $user, $passwd);

      if (!$this->ConnexionID)
        return( false );

      if ($database)
        return( $this->DbSelectDatabase($database) );

      return( true );
    }

    function DbSelectDatabase($database) {
      $this->DatabaseName = $database;

      if ($this->ConnexionID) {
        return @mysql_select_db($database, $this->ConnexionID);
      }
      else {
        return false;
      }
    }

    function DbQuery($query, $start = '', $limit = '') {
      if ($start != '' || $limit != '') {
        $query .= ' LIMIT '.$start.','.$limit;
      }
      $this->Result = @mysql_query($query, $this->ConnexionID);
      $this->cpt = $this->cpt+1;
      return( $this->Result );
    }

    function DbNextID($table,$champ) {
      $this->DbQuery("SELECT MAX(".$champ.") FROM ".$table);
      return( $this->DbResult(0,0)+1 );
    }

    function DbNumRows() {
      return( @mysql_num_rows($this->Result) );
    }

    function DbAffectedRows() {
      return( @mysql_affected_rows($this->ConnexionID) );
    }

    function DbInsertID() {
      return( @mysql_insert_id($this->ConnexionID) );
    }

    function DbDataSeek($line = 0) {
      return( @mysql_data_seek($this->Result,$line) );
    }

    function DbNextRow() {
      $this->Row = @mysql_fetch_array($this->Result);
      return( $this->Row );
    }

    function DbResult($row,$field) {
      return( @mysql_result($this->Result,$row,$field) );
    }

    function DbNumFields() {
      return( @mysql_num_fields($this->Result) );
    }

    function DbFieldName($field) {
      return( @mysql_field_name($this->Result,$field) );
    }

    function DbListTable($base) {
      $this->Result = @mysql_list_tables($base,$this->ConnexionID);
      return( $this->Result );
    }

    function DbTableName($table) {
      return( @mysql_tablename($this->Result,$table) );
    }

    function DbError() {
      return( @mysql_error($this->ConnexionID) );
    }

    function DbErrorNo() {
      return( @mysql_errno($this->ConnexionID) );
    }

    function DbNbReq() {
      return( $this->cpt );
    }

    function DbDeconnect() {
      return( @mysql_close($this->ConnexionID) );
    }
  }
?>
