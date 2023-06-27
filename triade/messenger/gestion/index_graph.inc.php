<?php 	
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2015 THeUDS           **
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
//
?>
<script type="text/javascript" src="../common/library/jquery.min.js"></script>
<script type="text/javascript" src="../common/library/highcharts/js/highcharts.js"></script>
<?php
//<!--[if IE]>
//  <script type="text/javascript" src="../common/library/highcharts/js/excanvas.compiled.js"></script>
//<![endif]-->

  require ("graph.inc.php");
  //
  require ("../common/sql.inc.php");
  //
  //
  $requete  = " SELECT count(*) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-K1t]", $requete);
  list ($nb_users) = mysqli_fetch_row ($result);
  //
  if ($nb_users > 2)
  {
    if ($im_dashboard_show_os_graph > 0)
    {
      $data = "";
      $requete  = " SELECT distinct(USR_OS), count(*) as NB";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
      $requete .= " WHERE USR_OS <> '' ";
      $requete .= " GROUP by USR_OS ";
      $requete .= " ORDER by NB desc ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1p]", $requete);
      if ( mysqli_num_rows($result) > 1 )
      {
        while( list ($win_os, $nb) = mysqli_fetch_row ($result) )
        {
          $data .= "['" . f_os_name($win_os) . "'," . round($nb / $nb_users * 100, 1) . "], ";
        }
        if (strlen($data) > 2) 
        {
          // on enlève la dernière virgule en trop.
          $data = substr($data, 0, (strlen($data)-2) );
          $data = "[ " . $data . " ]";
          //
          graph_pie("graph_os", $data);
        }
      }
    }
    //
    //
    if ($im_dashboard_show_gender_graph > 0)
    {
      $data = "";
      $requete  = " SELECT distinct(USR_GENDER), count(*) as NB";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
      $requete .= " GROUP by USR_GENDER ";
      $requete .= " ORDER by NB desc ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1r]", $requete);
      if ( mysqli_num_rows($result) > 1 )
      {
        while( list ($tgenre, $nb) = mysqli_fetch_row ($result) )
        {
          $genre = "? ";
          if ($tgenre == "M") $genre = $l_man . " ";
          if ($tgenre == "W") $genre = $l_woman . " ";
          $data .= "['" . $genre . "'," . round($nb / $nb_users * 100, 1) . "], ";
        }
        if (strlen($data) > 2) 
        {
          // on enlève la dernière virgule en trop.
          $data = substr($data, 0, (strlen($data)-2) );
          $data = "[ " . $data . " ]";
          //
          graph_pie("graph_gender", $data);
        }
      }
    }
    //
    //
    if ($im_dashboard_show_browser_graph > 0)
    {
      $data = "";
      $requete  = " SELECT distinct(USR_BROWSER), count(*) as NB";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
      $requete .= " WHERE USR_BROWSER <> '' ";
      $requete .= " GROUP by USR_BROWSER ";
      $requete .= " ORDER by NB desc, USR_BROWSER ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1xa]", $requete);
      if ( mysqli_num_rows($result) > 1 )
      {
        while( list ($tversion, $nb) = mysqli_fetch_row ($result) )
        {
          $data .= "['" . f_reduce_browser_name($tversion) . "'," . round($nb / $nb_users * 100, 1) . "], ";
        }
        if (strlen($data) > 2) 
        {
          // on enlève la dernière virgule en trop.
          $data = substr($data, 0, (strlen($data)-2) );
          $data = "[ " . $data . " ]";
          //
          graph_pie("graph_browser", $data);
        }
      }
    }
    //
    //
    if ($im_dashboard_show_email_graph > 0)
    {
      $data = "";
      $requete  = " SELECT distinct(USR_EMAIL_CLIENT), count(*) as NB";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
      $requete .= " WHERE USR_EMAIL_CLIENT <> '' ";
      $requete .= " GROUP by USR_EMAIL_CLIENT ";
      $requete .= " ORDER by NB desc ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1xb]", $requete);
      if ( mysqli_num_rows($result) > 1 )
      {
        while( list ($tversion, $nb) = mysqli_fetch_row ($result) )
        {
          $data .= "['" . f_reduce_emailclient_name($tversion) . "'," . round($nb / $nb_users * 100, 1) . "], ";
        }
        if (strlen($data) > 2) 
        {
          // on enlève la dernière virgule en trop.
          $data = substr($data, 0, (strlen($data)-2) );
          $data = "[ " . $data . " ]";
          //
          graph_pie("graph_email", $data);
        }
      }
    }
    //
    //
    if ($im_dashboard_show_language_graph > 0)
    {
      $data = "";
      $requete  = " SELECT distinct(USR_LANGUAGE_CODE), count(*) as NB";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
      $requete .= " GROUP by USR_LANGUAGE_CODE ";
      $requete .= " ORDER by NB desc ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1m]", $requete);
      if ( mysqli_num_rows($result) > 1 )
      {
        while( list ($language_code, $nb) = mysqli_fetch_row ($result) )
        {
          $data .= "['" . $language_code . "'," . round($nb / $nb_users * 100, 1) . "], ";
        }
        if (strlen($data) > 2) 
        {
          // on enlève la dernière virgule en trop.
          $data = substr($data, 0, (strlen($data)-2) );
          $data = "[ " . $data . " ]";
          //
          graph_pie("graph_language", $data);
        }
      }
    }
    //
    //
    if ($im_dashboard_show_country_graph > 0)
    {
      $data = "";
      $requete  = " SELECT count(*) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
      $requete .= " WHERE USR_COUNTRY_CODE <> '' ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1v]", $requete);
      list ($nb_users_pays) = mysqli_fetch_row ($result);
      //
      if ($nb_users_pays > 1)
      {
        $requete  = " SELECT distinct(USR_COUNTRY_CODE), count(*) as NB";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
        $requete .= " GROUP by USR_COUNTRY_CODE ";
        $requete .= " ORDER by NB desc, USR_COUNTRY_CODE ";
        //$requete .= " LIMIT 3 ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-K1u]", $requete);
        if ( mysqli_num_rows($result) > 1 )
        {
          $tot_partiel = 0;
          while( list ($country_code, $nb) = mysqli_fetch_row ($result) )
          {
            $data .= "['" . $country_code . "'," . round($nb / $nb_users_pays * 100, 1) . "], ";
            $tot_partiel = ($tot_partiel + $nb);
          }
          if (strlen($data) > 2) 
          {
            // si limité au 3 premiers pays.
            if ($tot_partiel < $nb_users_pays)
            {
              $data .= "['" . $l_country . "...', " . ($nb_users_pays - $tot_partiel) . "]";
            }
            else
            {
              // on enlève la dernière virgule en trop.
              $data = substr($data, 0, (strlen($data)-2) );
            }
            $data = "[ " . $data . " ]";
            //
            graph_pie("graph_country", $data);
          }
        }
      }
    }
    //
    //
    if ($im_dashboard_show_timezone_graph > 0)
    {
      $data = "";
      $requete  = " SELECT distinct(USR_TIME_SHIFT), count(*) as NB";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
      //$requete .= " WHERE USR_TIME_SHIFT <> '' ";
      $requete .= " GROUP by USR_TIME_SHIFT ";
      $requete .= " ORDER by NB desc, USR_TIME_SHIFT ";
      //$requete .= " ORDER by USR_TIME_SHIFT, NB desc ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-K1s]", $requete);
      if ( mysqli_num_rows($result) > 1 )
      {
        while( list ($timeshift, $nb) = mysqli_fetch_row ($result) )
        {
          if ($timeshift < 0) 
            $t = "-"; 
          else
            $t = "+";
          $t .= intval(abs($timeshift) / 10);
          if ( (abs($timeshift / 10) - intval(abs($timeshift) / 10)) <> 0 )
            $t .= ":30";
          else
            $t .= ":00";
          //
          $data .= "['" . $t . "'," . round($nb / $nb_users * 100, 1) . "], ";
        }
        if (strlen($data) > 2) 
        {
          // on enlève la dernière virgule en trop.
          $data = substr($data, 0, (strlen($data)-2) );
          $data = "[ " . $data . " ]";
          //
          graph_pie("graph_timezone", $data);
        }
      }
    }
  }
  //
  //
?>