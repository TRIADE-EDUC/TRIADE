<?php 	
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2016 THeUDS           **
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


/*
<script language='Javascript' type='text/javascript'>
  document.getElementById("graph_session").className="section_to_hide";
  document.getElementById('graph_user').className="section_to_hide";
  document.getElementById('graph_create').className="section_to_hide";
  document.getElementById('graph_message').className="section_to_hide";
  document.getElementById('graph_week').className="section_to_hide";
  document.getElementById('attente').className="section_to_show";
</script>
*/
  require ("graph.inc.php");
  //
  require ("../common/sql.inc.php");
  //
  //
  $have_stats = "";
  $requete  = " select count(*) ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-G1c]", $requete);
  list ($nb_row_stats) = mysqli_fetch_row ($result);
  if ($nb_row_stats > 1)
  {
    $have_stats = "X";
    //
    $title_suffix = "";
    if ($only > 367) $only = 0;
    if ($only > 0) $title_suffix = " - " . $only . " " . $l_days;
    //
    //
    // les stats du jour pour le nb de user ne se fait que le lendemain, au 1er insert, donc la, on calcule en direct.
    $nb_user_last_day = 0;
    if ($group_by == '')
    {
      $requete  = " select max(STA_NB_MSG), max(STA_NB_CREAT), max(STA_NB_SESSION), max(STA_NB_USR), max(STA_DATE) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-G1b]", $requete);
      list ($max_nb_msg, $max_nb_creat, $max_nb_session, $max_nb_user, $max_dat) = mysqli_fetch_row ($result);
      if ($max_dat != "")
      {
        $requete  = " SELECT count(*) ";
        $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
        $requete .= " where USR_DATE_LAST = " . date("Ymd", strtotime($max_dat));
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-G1a]", $requete);
        list($nb_user_last_day) = mysqli_fetch_row ($result);
      }
    }
    //
    //
    $title_2 = "";
    if ($group_by == 'week') $title_2 = $l_admin_stats_by_week;
    if ($group_by == 'month') $title_2 = $l_admin_stats_by_month;
    if ($group_by == 'year') $title_2 = $l_admin_stats_by_year;
    //
    $data = "";
    $periodes = "";
    if ($group_by == '')
    {
      //$requete  = " select DATE_FORMAT(STA_DATE, '%Y,%c-1,%e'), STA_NB_MSG, STA_NB_CREAT, STA_NB_SESSION, STA_NB_USR, STA_SBX_NB_MSG, STA_SF_NB_SHARE, STA_SF_NB_EXCHANGE, STA_SF_NB_DOWNLOAD ";
      $requete  = " select DATE_FORMAT(STA_DATE, '%Y,%c,%e'), STA_NB_MSG, STA_NB_CREAT, STA_NB_SESSION, STA_NB_USR, STA_SBX_NB_MSG, STA_SF_NB_SHARE, STA_SF_NB_EXCHANGE, STA_SF_NB_DOWNLOAD ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
      if ( ($only > 0) and ($only < 367) ) $requete .= " WHERE DATEDIFF(CURDATE() , STA_DATE) <= " . $only ;
      $requete .= " ORDER BY STA_DATE ";
    }
    else
    {
      if ($group_by == 'week')  $requete  = " select DATE_FORMAT(STA_DATE, '%v-%x'), sum(STA_NB_MSG), sum(STA_NB_CREAT), ROUND(avg(STA_NB_SESSION)), ROUND(avg(STA_NB_USR)), sum(STA_SBX_NB_MSG), sum(STA_SF_NB_SHARE), sum(STA_SF_NB_EXCHANGE), sum(STA_SF_NB_DOWNLOAD) ";
      if ($group_by == 'month') $requete  = " select DATE_FORMAT(STA_DATE, '%m-%Y'), sum(STA_NB_MSG), sum(STA_NB_CREAT), ROUND(avg(STA_NB_SESSION)), ROUND(avg(STA_NB_USR)), sum(STA_SBX_NB_MSG), sum(STA_SF_NB_SHARE), sum(STA_SF_NB_EXCHANGE), sum(STA_SF_NB_DOWNLOAD) ";
      if ($group_by == 'year')  $requete  = " select year(STA_DATE), sum(STA_NB_MSG), sum(STA_NB_CREAT), ROUND(avg(STA_NB_SESSION)), ROUND(avg(STA_NB_USR)), sum(STA_SBX_NB_MSG), sum(STA_SF_NB_SHARE), sum(STA_SF_NB_EXCHANGE), sum(STA_SF_NB_DOWNLOAD) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
      if ($only > 0) $requete .= " WHERE DATEDIFF(CURDATE() , STA_DATE) <= " . $only ;
      //if ($group_by == 'week')  $requete .= " group by year(STA_DATE), week(STA_DATE) ";
      if ($group_by == 'week')  $requete .= " group by DATE_FORMAT(STA_DATE, '%v-%x') ";
      //if ($group_by == 'month') $requete .= " group by year(STA_DATE), month(STA_DATE) ";
      if ($group_by == 'month') $requete .= " group by DATE_FORMAT(STA_DATE, '%m-%Y') ";
      if ($group_by == 'year')  $requete .= " group by year(STA_DATE) ";
      $requete .= " ORDER BY STA_DATE ";
    }
    //
    $periodes = "";
    //
    $data_session = "";
    $data_user = "";
    $data_creat = "";
    $data_msg = "";
    $data_sbx_msg = "";
    $data_sharefile = "";
    $data_sharefile_exchange = "";
    $data_sharefile_download = "";
    $last_ligne = "";
    $last_date = "";
    //
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-G1d]", $requete);
    $nb_row = mysqli_num_rows($result);
    if ( $nb_row > 0 )
    {
      while( list ($date, $nb_msg, $nb_creat, $nb_session, $nb_user, $nb_sbx_msg, $nb_sharefile, $nb_sharefile_exchange, $nb_sharefile_download) = mysqli_fetch_row ($result) )
      {
        if ($group_by == '')
        {
          // car mois de date UTC commence à 0 !!!
          $p = explode(",", $date);
          $y = $p[0];
          $m = $p[1];
          $d = $p[2];
          $date = $y . "," . ($m-1) . "," . $d;
          //
          // Session :
          $data_session .= "[Date.UTC(" . $date . "), " . $nb_session . "], ";
          //
          // User :
          $last_ligne = "[Date.UTC(" . $date . "), " . $nb_user . "], ";
          $data_user .= $last_ligne;
          $last_date = $date;
          //
          // Create :
          $data_creat .= "[Date.UTC(" . $date . "), " . $nb_creat . "], ";
          //
          // Message :
          $data_msg .= "[Date.UTC(" . $date . "), " . $nb_msg . "], ";
          //
          // Shoutbox :
          $data_sbx_msg .= "[Date.UTC(" . $date . "), " . $nb_sbx_msg . "], ";
          //
          // ShareFiles - send :
          $data_sharefile .= "[Date.UTC(" . $date . "), " . $nb_sharefile . "], ";
          //
          // ShareFiles - exchange :
          $data_sharefile_exchange .= "[Date.UTC(" . $date . "), " . $nb_sharefile_exchange . "], ";
          //
          // ShareFiles - download :
          $data_sharefile_download .= "[Date.UTC(" . $date . "), " . $nb_sharefile_download . "], ";
        }
        else
        {
          $periodes .= "'" . $date . "', ";
          //
          // Session :
          $data_session .= $nb_session . ", ";
          //
          // User :
          $data_user .= $nb_user . ", ";
          //
          // Create :
          $data_creat .= $nb_creat . ", ";
          //
          // Message :
          $data_msg .= $nb_msg . ", ";
          //
          // Shoutbox :
          $data_sbx_msg .= $nb_sbx_msg . ", ";
          //
          // ShareFiles - send :
          $data_sharefile .= $nb_sharefile . ", ";
          //
          // ShareFiles - exchange :
          $data_sharefile_exchange .= $nb_sharefile_exchange . ", ";
          //
          // ShareFiles - download :
          $data_sharefile_download .= $nb_sharefile_download . ", ";
        }
      }
      //
      if (strlen($periodes) > 2)
      {
        $periodes = substr($periodes, 0, (strlen($periodes)-2) );
        $periodes = "[ " . $periodes . " ]";
      }
      //
      // Session :
      if ( (strlen($data_session) > 2) or (strlen($periodes_session) > 2) )
      {
        // on enlève la dernière virgule en trop.
        $data_session = substr($data_session, 0, (strlen($data_session)-2) );
        $data_session = "[ " . $data_session . " ]";
        //
        if ($group_by == '')
          graph_area("graph_session", $data_session, $l_admin_stats_col_nb_session . $title_suffix, $l_admin_stats_click_drag_to_zoom);
        else
          graph_column("graph_session", $periodes, $data_session, $l_admin_stats_col_nb_session, $title_2 . " (" . $l_admin_stats_average . ")" . $title_suffix, $l_admin_stats_click_drag_to_zoom);
      }
      //
      // User :
      if (strlen($data_user) > 2)
      {
        // pour la liste des users, les valeurs du jours ne sont pas encore dans la table de stats...
        if ( ($group_by == '') and ($last_ligne != '') )
        {
          // donc on enlève la dernière ligne (la valeur était à zéro de toute facon :
          $data_user = substr($data_user, 0, (strlen($data_user)-strlen($last_ligne)));
          // 
          // qu'on remplace avec la bonne valeur (celle en temps, donc non définitive) :
          $data_user .= "[Date.UTC(" . $last_date . "), " . $nb_user_last_day . "], ";
        }
        //
        // on enlève la dernière virgule en trop.
        $data_user = substr($data_user, 0, (strlen($data_user)-2) );
        $data_user = "[ " . $data_user . " ]";
        //
        if ($group_by == '')
          graph_area("graph_user", $data_user, $l_admin_stats_col_nb_users . $title_suffix, $l_admin_stats_click_drag_to_zoom);
        else
          graph_column("graph_user", $periodes, $data_user, $l_admin_stats_col_nb_users, $title_2 . " (" . $l_admin_stats_average . ")" . $title_suffix, $l_admin_stats_click_drag_to_zoom);
      }
      //
      // Create :
      if (strlen($data_creat) > 2)
      {
        // on enlève la dernière virgule en trop.
        $data_creat = substr($data_creat, 0, (strlen($data_creat)-2) );
        $data_creat = "[ " . $data_creat . " ]";
        //
        if ($group_by == '')
          graph_area("graph_create", $data_creat, $l_admin_stats_col_nb_creat . $title_suffix, $l_admin_stats_click_drag_to_zoom);
        else
          graph_column("graph_create", $periodes, $data_creat, $l_admin_stats_col_nb_creat, $title_2 . $title_suffix, $l_admin_stats_click_drag_to_zoom);
      }
      //
      // Message :
      if (strlen($data_msg) > 2)
      {
        // on enlève la dernière virgule en trop.
        $data_msg = substr($data_msg, 0, (strlen($data_msg)-2) );
        $data_msg = "[ " . $data_msg . " ]";
        //
        if ($group_by == '')
          graph_area("graph_message", $data_msg, $l_admin_stats_col_nb_msg . $title_suffix, $l_admin_stats_click_drag_to_zoom);
        else
          graph_column("graph_message", $periodes, $data_msg, $l_admin_stats_col_nb_msg, $title_2 . $title_suffix, $l_admin_stats_click_drag_to_zoom);
      }
      //
      // Shoutbox :
      if (_SHOUTBOX != "")
      {
        if (strlen($data_sbx_msg) > 2)
        {
          // on enlève la dernière virgule en trop.
          $data_sbx_msg = substr($data_sbx_msg, 0, (strlen($data_sbx_msg)-2) );
          $data_sbx_msg = "[ " . $data_sbx_msg . " ]";
          //
          if ($group_by == '')
            graph_area("graph_shoutbox", $data_sbx_msg, $l_admin_stats_col_nb_msg_sbx . $title_suffix, $l_admin_stats_click_drag_to_zoom);
          else
            graph_column("graph_shoutbox", $periodes, $data_sbx_msg, $l_admin_stats_col_nb_msg_sbx, $title_2 . $title_suffix, $l_admin_stats_click_drag_to_zoom);
        }
      }
      //
      // ShareFiles - send :
      if (_SHARE_FILES != "")
      {
        if (strlen($data_sharefile) > 2)
        {
          // on enlève la dernière virgule en trop.
          $data_sharefile = substr($data_sharefile, 0, (strlen($data_sharefile)-2) );
          $data_sharefile = "[ " . $data_sharefile . " ]";
          //
          if ($group_by == '')
            graph_area("graph_share_files_nb_share", $data_sharefile, $l_admin_share_files_title . $title_suffix, $l_admin_stats_click_drag_to_zoom);
          else
            graph_column("graph_share_files_nb_share", $periodes, $data_sharefile, $l_admin_share_files_title, $title_2 . $title_suffix, $l_admin_stats_click_drag_to_zoom);
        }
        //
        // ShareFiles - exchange :
        if (_SHARE_FILES_EXCHANGE != "")
        {
          if (strlen($data_sharefile_exchange) > 2)
          {
            // on enlève la dernière virgule en trop.
            $data_sharefile_exchange = substr($data_sharefile_exchange, 0, (strlen($data_sharefile_exchange)-2) );
            $data_sharefile_exchange = "[ " . $data_sharefile_exchange . " ]";
            //
            if ($group_by == '')
              graph_area("graph_share_files_nb_exchange", $data_sharefile_exchange, $l_admin_share_files_exchange . $title_suffix, $l_admin_stats_click_drag_to_zoom);
            else
              graph_column("graph_share_files_nb_exchange", $periodes, $data_sharefile_exchange, $l_admin_share_files_exchange, $title_2 . $title_suffix, $l_admin_stats_click_drag_to_zoom);
          }
        }
        //
        // ShareFiles - download :
        if (strlen($data_sharefile_download) > 2)
        {
          // on enlève la dernière virgule en trop.
          $data_sharefile_download = substr($data_sharefile_download, 0, (strlen($data_sharefile_download)-2) );
          $data_sharefile_download = "[ " . $data_sharefile_download . " ]";
          //
          if ($group_by == '')
            graph_area("graph_share_files_nb_download", $data_sharefile_download, $l_index_share_file_download . $title_suffix, $l_admin_stats_click_drag_to_zoom);
          else
            graph_column("graph_share_files_nb_download", $periodes, $data_sharefile_download, $l_index_share_file_download, $title_2 . $title_suffix, $l_admin_stats_click_drag_to_zoom);
        }
      }
    }
    //
    //
    //
    //
    //
    //
    $graph_mix = "";
    if ($group_by != '')
    {
      $data = ""; // ?????
      $data = $data_msg; // ?????
      
      $data_1 = "";
      $data_2 = "";
      $data_3 = "";
      $periodes = "";
      $legende = "";
      /*
      if ($group_by == 'week')  $requete  = " select DATE_FORMAT(STA_DATE, '%v-%x'), ROUND(avg(STA_NB_MSG)), ROUND(avg(STA_NB_CREAT)), ROUND(avg(STA_NB_SESSION)), ROUND(avg(STA_NB_USR)) ";
      if ($group_by == 'month') $requete  = " select DATE_FORMAT(STA_DATE, '%m-%Y'), ROUND(avg(STA_NB_MSG)), ROUND(avg(STA_NB_CREAT)), ROUND(avg(STA_NB_SESSION)), ROUND(avg(STA_NB_USR)) ";
      if ($group_by == 'year')  $requete  = " select year(STA_DATE), ROUND(avg(STA_NB_MSG)), ROUND(avg(STA_NB_CREAT)), ROUND(avg(STA_NB_SESSION)), ROUND(avg(STA_NB_USR)) ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
      if ($only > 0)  $requete .= " WHERE DATEDIFF(CURDATE() , STA_DATE) <= " . $only ;
      if ($group_by == 'week')  $requete .= " group by DATE_FORMAT(STA_DATE, '%v-%x') ";
      if ($group_by == 'month') $requete .= " group by DATE_FORMAT(STA_DATE, '%m-%Y') ";
      if ($group_by == 'year')  $requete .= " group by year(STA_DATE) ";
      $requete .= " ORDER BY STA_DATE ";
      */
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-G1h]", $requete);
      $nb_row = mysqli_num_rows($result);
      if ( ($nb_row > 1) and ($nb_row < 40) )
      {  
        while( list ($date, $nb_msg, $nb_creat, $nb_session, $nb_user, $nb_sbx_msg, $nb_sharefile, $nb_sharefile_exchange, $nb_sharefile_download) = mysqli_fetch_row ($result) )
        {
          $legende .= "' " . $date . "', ";
          $data_1 .= $nb_session . ", ";
          $data_2 .= $nb_user . ", ";
          $data_3 .= $nb_creat . ", ";
          
          $last_ligne = "[Date.UTC(" . $date . "), " . $nb_user . "], ";
          $data .= $last_ligne;
          $last_date = $date;

        }
        if (strlen($data_1) > 2)
        {
          // on enlève la dernière virgule en trop.
          $legende = substr($legende, 0, (strlen($legende)-2) );
          $data_1 = substr($data_1, 0, (strlen($data_1)-2) );
          $data_2 = substr($data_2, 0, (strlen($data_2)-2) );
          $data_3 = substr($data_3, 0, (strlen($data_3)-2) );
          $data_1 = "{ name: '" . $l_admin_stats_col_nb_session . "', data: [ " . $data_1 . " ] }, ";       // 2 axes, donc :   yAxis: 1, type: 'column',
          $data_2 = "{ name: '" . $l_admin_stats_col_nb_users . "', data: [ " . $data_2 . " ] }, ";
          $data_3 = "{ name: '" . $l_admin_stats_col_nb_creat . "', yAxis: 1, type: 'spline', data: [ " . $data_3 . " ] } ";  // 2 axes, donc :  type: 'spline',
          $data = $data_1 . $data_2 . $data_3;
          //
          //graph_column_basic("graph_mix", $data, $l_admin_stats_day_of_week . " (" . $l_admin_stats_average . ")", $legende );
          //graph_column_basic("graph_mix", $data, $title_2 . " (" . $l_admin_stats_average . ")", $legende);
          graph_column_basic_2_axes("graph_mix", $data, $title_2 . " (" . $l_admin_stats_average . ")" . $title_suffix, $legende, $l_admin_stats_col_nb_session ." <BR> & ". $l_admin_stats_col_nb_users , $l_admin_stats_col_nb_creat );
          $graph_mix = "OK";
        }
      }
    }
    //
    //
    //
    $data = "";
    $data_1 = "";
    $data_2 = "";
    $data_3 = "";
    $data_4 = "";
    $data_5 = "";
    $data_6 = "";
    $data_7 = "";
    $data_8 = "";
    $days = "#";
    $requete  = " select WEEKDAY(STA_DATE), ROUND(avg(STA_NB_MSG)), ROUND(avg(STA_NB_CREAT)), ROUND(avg(STA_NB_SESSION)), ROUND(avg(STA_NB_USR)), ROUND(avg(STA_SBX_NB_MSG)), ROUND(avg(STA_SF_NB_SHARE)), ROUND(avg(STA_SF_NB_EXCHANGE)), ROUND(avg(STA_SF_NB_DOWNLOAD))  ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    if ($only > 0)  $requete .= " WHERE DATEDIFF(CURDATE() , STA_DATE) <= " . $only ;
    $requete .= " GROUP BY WEEKDAY(STA_DATE) ";
    $requete .= " ORDER BY WEEKDAY(STA_DATE) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-G1j]", $requete);
    $nb_row = mysqli_num_rows($result);
    if ( $nb_row > 0 )
    {  
      while( list ($n_days, $nb_msg, $nb_creat, $nb_session, $nb_user, $nb_sbx_msg, $nb_sharefile, $nb_sharefile_exchange, $nb_sharefile_download) = mysqli_fetch_row ($result) )
      {
        $days  .= $n_days . "#";
        $data_1 .= $nb_msg . ", ";
        $data_2 .= $nb_session . ", ";
        $data_3 .= $nb_user . ", ";
        $data_4 .= $nb_creat . ", ";
        $data_5 .= $nb_sbx_msg . ", ";
        $data_6 .= $nb_sharefile . ", ";
        $data_7 .= $nb_sharefile_exchange . ", ";
        $data_8 .= $nb_sharefile_download . ", ";
      }
      if (strlen($data_1) > 2)
      {
        // on enlève la dernière virgule en trop.
        if (strlen($data_1) > 2) $data_1 = substr($data_1, 0, (strlen($data_1)-2) );
        if (strlen($data_2) > 2) $data_2 = substr($data_2, 0, (strlen($data_2)-2) );
        if (strlen($data_3) > 2) $data_3 = substr($data_3, 0, (strlen($data_3)-2) );
        if (strlen($data_4) > 2) $data_4 = substr($data_4, 0, (strlen($data_4)-2) );
        if (strlen($data_5) > 2) $data_5 = substr($data_5, 0, (strlen($data_5)-2) );
        if (strlen($data_6) > 2) $data_6 = substr($data_6, 0, (strlen($data_6)-2) );
        if (strlen($data_7) > 2) $data_7 = substr($data_7, 0, (strlen($data_7)-2) );
        if (strlen($data_8) > 2) $data_8 = substr($data_8, 0, (strlen($data_8)-2) );
        $data_1 = "{ name: '" . $l_admin_stats_col_nb_msg . "', data: [ " . $data_1 . " ] } ";
        $data_2 = "{ name: '" . $l_admin_stats_col_nb_session . "', data: [ " . $data_2 . " ] } ";
        $data_3 = "{ name: '" . $l_admin_stats_col_nb_users . "', data: [ " . $data_3 . " ] } ";
        $data_4 = "{ name: '" . $l_admin_stats_col_nb_creat . "', data: [ " . $data_4 . " ] } ";
        $data_5 = "{ name: '" . $l_admin_stats_col_nb_msg_sbx . "', data: [ " . $data_5 . " ] } ";
        $data_6 = "{ name: '" . $l_admin_share_files_title . "', data: [ " . $data_6 . " ] } ";
        $data_7 = "{ name: '" . $l_admin_share_files_exchange . "', data: [ " . $data_7 . " ] } ";
        $data_8 = "{ name: '" . $l_index_share_file_download . "', data: [ " . $data_8 . " ] } ";
        $data = $data_1 . ", " . $data_2 . ", " . $data_3 . ", " . $data_4 ;
        if (_SHOUTBOX != "") $data .= ", " . $data_5;
        if (_SHARE_FILES != "")
        {
          $data .= ", " . $data_6;
          if (_SHARE_FILES_EXCHANGE != "") $data .= ", " . $data_7;
          #$data .= ", " . $data_8;
        }
        //
        $legende = " ";
        if (strpos($days, "0#") > 0) $legende .= " '" . $l_day_0 . "', ";
        if (strpos($days, "1#") > 0) $legende .= " '" . $l_day_1 . "', ";
        if (strpos($days, "2#") > 0) $legende .= " '" . $l_day_2 . "', ";
        if (strpos($days, "3#") > 0) $legende .= " '" . $l_day_3 . "', ";
        if (strpos($days, "4#") > 0) $legende .= " '" . $l_day_4 . "', ";
        if (strpos($days, "5#") > 0) $legende .= " '" . $l_day_5 . "', ";
        if (strpos($days, "6#") > 0) $legende .= " '" . $l_day_6 . "', ";
        // on enlève la dernière virgule en trop.
        if (strlen($legende) > 2) $legende = substr($legende, 0, (strlen($legende)-2) );
        //
        graph_areaspline("graph_week", $data, $l_admin_stats_day_of_week . " (" . $l_admin_stats_average . ")" . $title_suffix, $l_admin_stats_click_to_show_hide, $legende );
      }
    }
  }
  //
?>