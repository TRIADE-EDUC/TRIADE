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
if ( !defined('INTRAMESSENGER') )
{
  exit;
}


function f_share_files_projet_folder($t_project)
{
  global $PREFIX_IM_TABLE;
  //
  $folder = "";
  if ($t_project > 0) 
  {
    $requete  = " select FPJ_FOLDER ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FPJ_FILEPROJET ";
    $requete .= " WHERE ID_PROJET = " . $t_project . " ";
    $requete .= " LIMIT 2 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-N3b]", $requete);
    if ( mysqli_num_rows($result) == 1)
    {
      list ($folder) = mysqli_fetch_row ($result);
    }
  }
	//
	return $folder;
}



function sf_remplir_medias()
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "FMD_FILEMEDIA (FMD_NAME, FMD_EXTENSIONS) VALUES ";
  $requete .= " ('Archive', '.ace.arc.arj.ark.bz.bz2.cab.dpa.gz.ice.iso.lha.lzh.nrg.rar.tar.tgz.war.xar.zip.7z.'), ";
  $requete .= " ('Audio', '.ac3.aif.aifc.aiff.bwf.cda.m4r.mod.mp3.ogg.raw.wav.wma.'), ";
  $requete .= " ('Video', '.avi.divx.flv.m3u.m4a.mkv.mov.movie.mp4.mpe.mpeg.mpg.qt.wmv.'), ";
  $requete .= " ('Graphics', '.bmp.gif.ico.jif.jpg.jpeg.jps.png.psp.rgb.svg.tif.tiff.'), ";
  $requete .= " ('Office', '.doc.docx.mdb.mpp.pdf.pps.ppt.pptx.vsd.wps.wri.xls.xlsm.xlsx.xlt.xltm.'), ";
  $requete .= " ('OpenOffice', '.odb.odc.odf.odg.odp.ods.odt.otg.oth.ots.ott.txt.'), ";
  $requete .= " ('Schema', '.art.b3d.blend.bvh.dwg.dxf.lwo.m3d.max.psd.xmind.'); ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-N3a]", $requete);
}



function sf_ftp_create_folder($folder)
{
  if ( (_SHARE_FILES != "") and (_SHARE_FILES_FTP_ADDRESS != "") and (_SHARE_FILES_FTP_LOGIN != "") and (_SHARE_FILES_FTP_PASSWORD_CRYPT != "") )
  {
    // FTP on another server :
    if ( (_SHARE_FILES_FTP_PASSWORD != "") and (_SHARE_FILES_FOLDER == "") )
    {
      if ($folder != "") $folder .= "/";
      $port_num = intval(_SHARE_FILES_FTP_PORT_NUMBER);
      if ( ($port_num <= 0) or ($port_num > 65535) ) $port_num = 21;
      $conn_id = ftp_connect(_SHARE_FILES_FTP_ADDRESS, $port_num) or die("<span class='error'>Couldn't connect to FTP server!</span>"); 
      if (@ftp_login($conn_id, _SHARE_FILES_FTP_LOGIN, _SHARE_FILES_FTP_PASSWORD)) 
      {
          if (ftp_mkdir($conn_id, $folder))
            $ret = true;
      }
      ftp_close($conn_id);
    }
    // ELSE (FTP on this webserver) :
    if ( (_SHARE_FILES_FOLDER != "") and (_SHARE_FILES_FTP_PASSWORD == "") )
    {
      if ($folder != "") $folder .= "/";
      mkdir(_SHARE_FILES_FOLDER . $folder);
    }
  }
}



function stats_sharefile_inc($id_user, $share_or_exchange)
{
  global $PREFIX_IM_TABLE, $id_connect, $have_quota_download;
  //
  if ($have_quota_download != "")
  {
    $requete  = " SELECT FST_NB_SHARE, FST_NB_EXCHANGE, FST_NB_LAST_DATE, FST_LAST_DATE, FST_NB_LAST_WEEK, FST_LAST_WEEK, WEEK(CURDATE()) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "FST_FILESTATS ";
    $requete .= " WHERE ID_USER_AUT = " . $id_user;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-N3c]", $requete);
    if ( mysqli_num_rows($result) == 1 ) 
    {
      list($nb_share, $nb_exchange, $nb_last_date, $last_date, $nb_last_week, $last_week, $this_week) = mysqli_fetch_row ($result);
      //
      if ($share_or_exchange == "S")
        $nb_share++;
      else
        $nb_exchange++;
      //
      $last_date = date("Ymd", strtotime($last_date));
      if ($last_date != date("Ymd"))
      {
        $nb_last_date = 1;
        //shoutbox_remove_old_msg();
      }
      else
        $nb_last_date++;
      //
      if ($last_week <> $this_week)
        $nb_last_week = 1;
      else
        $nb_last_week++;
      //
      //
      $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FST_FILESTATS ";
      if ($share_or_exchange == "S")
        $requete .= " SET FST_NB_SHARE = " . $nb_share . ", ";
      else
        $requete .= " SET FST_NB_EXCHANGE = " . $nb_exchange . ", ";
      //
      $requete .= "     FST_NB_LAST_DATE = " . $nb_last_date . ", FST_LAST_DATE = CURDATE(), ";
      $requete .= "     FST_NB_LAST_WEEK = " . $nb_last_week . ", FST_LAST_WEEK = WEEK(CURDATE()) ";
      $requete .= " WHERE ID_USER_AUT = " . $id_user;
      $requete .= " limit 1 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-N3d]", $requete);
    }
    else
    {
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "FST_FILESTATS (ID_USER_AUT, FST_NB_SHARE, FST_NB_EXCHANGE, FST_NB_LAST_DATE, FST_LAST_DATE, FST_NB_LAST_WEEK, FST_LAST_WEEK ) ";
      $requete .= " VALUES (" . $id_user . ", ";
      if ($share_or_exchange == "S")
        $requete .= " 1, 0, ";
      else
        $requete .= " 0, 1, ";
      //
      $requete .= " 1, CURDATE(), 1, WEEK(CURDATE()) ) " ;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-N3e]", $requete);
      //
      //shoutbox_remove_old_msg();
    }
  }
  //
  //
  update_last_activity_user($id_user);
  //
  //
  if (_STATISTICS != "")
  {
    $requete  = " UPDATE " . $PREFIX_IM_TABLE . "STA_STATS ";
    if ($share_or_exchange == "S")
      $requete .= " SET STA_SF_NB_SHARE = STA_SF_NB_SHARE + 1 ";
    else
      $requete .= " SET STA_SF_NB_EXCHANGE = STA_SF_NB_EXCHANGE + 1 ";
    //
    $requete .= " WHERE STA_DATE = CURDATE() ";
    $requete .= " limit 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-N3f]", $requete);
  }
}





function stats_sharefile_download_inc($id_user, $file_id, $fil_size)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $requete  = " SELECT ID_FILE_LAST_DL, FSD_NB_DOWNLOAD, FSD_LAST_DATE, FSD_NB_LAST_DATE, FSD_TRAFFIC_LAST_DATE, ";
  $requete .= " WEEK(CURDATE()), FSD_LAST_WEEK, FSD_NB_LAST_WEEK, FSD_TRAFFIC_LAST_WEEK, ";
  $requete .= " MONTH(CURDATE()), FSD_LAST_MONTH, FSD_NB_LAST_MONTH, FSD_TRAFFIC_LAST_MONTH ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FSD_FILESTATSDOWNLOAD ";
  $requete .= " WHERE ID_USER_DL = " . $id_user;
  $requete .= " LIMIT 2 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-N3s]", $requete);
  if ( mysqli_num_rows($result) == 1 ) 
  {
    list($last_file_id, $nb_download, $last_date, $nb_last_date, $traffic_last_date,
      $this_week, $last_week, $nb_last_week, $traffic_last_week, 
      $this_month, $last_month, $nb_last_month, $traffic_last_month) = mysqli_fetch_row ($result);
    //
    if ($last_file_id <> $file_id)
    {
      $nb_download++;
      //
      $last_date = date("Ymd", strtotime($last_date));
      if ($last_date != date("Ymd"))
      {
        $nb_last_date = 1;
        $traffic_last_date = $fil_size;
        //shoutbox_remove_old_msg();
      }
      else
      {
        $nb_last_date++;
        $traffic_last_date = ($traffic_last_date + $fil_size);
      }
      //
      if ($last_week <> $this_week)
      {
        $nb_last_week = 1;
        $traffic_last_week = $fil_size;
      }
      else
      {
        $nb_last_week++;
        $traffic_last_week = ($traffic_last_week + $fil_size);
      }
      //
      if ($last_month <> $this_month)
      {
        $nb_last_month = 1;
        $traffic_last_month = $fil_size;
      }
      else
      {
        $nb_last_month++;
        $traffic_last_month = ($traffic_last_month + $fil_size);
      }
      //
      //
      $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FSD_FILESTATSDOWNLOAD ";
      $requete .= " SET ID_FILE_LAST_DL = " . $file_id . ", FSD_NB_DOWNLOAD = " . $nb_download . ", ";
      $requete .= "     FSD_LAST_DATE = CURDATE(), FSD_NB_LAST_DATE = " . $nb_last_date . ", FSD_TRAFFIC_LAST_DATE = " . $traffic_last_date . ", ";
      $requete .= "     FSD_LAST_WEEK = WEEK(CURDATE()), FSD_NB_LAST_WEEK = " . $nb_last_week . ", FSD_TRAFFIC_LAST_WEEK = " . $traffic_last_week . ", ";
      $requete .= "     FSD_LAST_MONTH = MONTH(CURDATE()), FSD_NB_LAST_MONTH = " . $nb_last_month . ", FSD_TRAFFIC_LAST_MONTH = " . $traffic_last_month . " ";
      $requete .= " WHERE ID_USER_DL = " . $id_user;
      $requete .= " and ID_FILE_LAST_DL <> " . $file_id; // pour limiter les doubles
      $requete .= " limit 1 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-N3t]", $requete);
    }
  }
  else
  {
    $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "FSD_FILESTATSDOWNLOAD ";
    $requete .= " (ID_USER_DL, ID_FILE_LAST_DL, FSD_NB_DOWNLOAD, ";
    $requete .= " FSD_LAST_DATE, FSD_NB_LAST_DATE, FSD_TRAFFIC_LAST_DATE, ";
    $requete .= " FSD_LAST_WEEK, FSD_NB_LAST_WEEK, FSD_TRAFFIC_LAST_WEEK, ";
    $requete .= " FSD_LAST_MONTH, FSD_NB_LAST_MONTH, FSD_TRAFFIC_LAST_MONTH ) ";
    $requete .= " VALUES (" . $id_user . ", " . $file_id . ", 1, ";
    $requete .= " CURDATE(), 1, " . $fil_size . ", ";
    $requete .= " WEEK(CURDATE()), 1, " . $fil_size . ", ";
    $requete .= " MONTH(CURDATE()), 1, " . $fil_size . ") ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-N3u]", $requete);
    //
    //shoutbox_remove_old_msg();
  }
  //
  //
  update_last_activity_user($id_user);
  //
  //
  if (_STATISTICS != "")
  {
    $requete  = " UPDATE " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " SET STA_SF_NB_DOWNLOAD = STA_SF_NB_DOWNLOAD + 1 ";
    $requete .= " WHERE STA_DATE = CURDATE() ";
    $requete .= " limit 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-N3f2]", $requete);
  }
}




function stats_sharefile_add_alert_reject($id_user, $alert_or_reject)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $requete  = " SELECT FST_NB_ALERT_SEND, FST_NB_REJECT ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FST_FILESTATS ";
  $requete .= " WHERE ID_USER_AUT = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-N3g]", $requete);
  if ( mysqli_num_rows($result) == 1 ) 
  {
    list($nb_alert, $nb_reject) = mysqli_fetch_row ($result);
    //
    if ($alert_or_reject == "A")
      $nb_alert++;
    else
      $nb_reject++;
    //
    $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FST_FILESTATS ";
    if ($alert_or_reject == "A")
      $requete .= " SET FST_NB_ALERT_SEND = " . $nb_alert . " ";
    else
      $requete .= " SET FST_NB_REJECT = " . $nb_reject . " ";
    //
    $requete .= " WHERE ID_USER_AUT = " . $id_user;
    $requete .= " limit 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-N3h]", $requete);
  }
  else
  {
    $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "FST_FILESTATS (ID_USER_AUT, FST_NB_ALERT_SEND, FST_NB_REJECT) ";
    $requete .= " VALUES (" . $id_user . ", ";
    if ($alert_or_reject == "A")
      $requete .= " 1, 0) ";
    else
      $requete .= " 0, 1) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-N3i]", $requete);
  }
}



function stats_sharefile_add_note_user($id_user_aut, $note)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FST_FILESTATS ";
  if (intval($note) > 0)
    $requete .= " SET FST_NB_VOTE_M = FST_NB_VOTE_M +  1 ";
  else
    $requete .= " SET FST_NB_VOTE_L = FST_NB_VOTE_L +  1 ";
  //
  $requete .= " WHERE ID_USER_AUT = " . $id_user_aut;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-N3k]", $requete);
}



function stats_sharefile_update_scores($id_file, $id_user_aut)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $score_more = 0;
  $requete  = " select count(FLV_VOTE_M)";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FLV_FILEVOTE ";
  $requete .= " WHERE ID_FILE = " . $id_file;
  $requete .= " AND FLV_VOTE_M > 0 ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-N3n]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($score_more) = mysqli_fetch_row ($result);
  }
  //
  $score_less = 0;
  $requete  = " select count(FLV_VOTE_L)";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FLV_FILEVOTE ";
  $requete .= " WHERE ID_FILE = " . $id_file;
  $requete .= " AND FLV_VOTE_L < 0 ";
  //$requete .= " AND FLV_VOTE_L > 0 ";  NON !
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-N3p]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  {
    list ($score_less) = mysqli_fetch_row ($result);
  }
  //
  //
  if ($score_more > 0)
  {
    $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FST_FILESTATS ";
    $requete .= " SET FST_MAX_VOTE_M = " . $score_more;
    $requete .= " WHERE ID_USER_AUT = " . $id_user_aut;
    $requete .= " AND FST_MAX_VOTE_M < " . $score_more;
    $requete .= " limit 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-N3q]", $requete);
  }
  if ($score_less > 0)
  {
    $requete  = " UPDATE " . $PREFIX_IM_TABLE . "FST_FILESTATS ";
    $requete .= " SET FST_MAX_VOTE_L = " . $score_less;
    $requete .= " WHERE ID_USER_AUT = " . $id_user_aut;
    $requete .= " AND FST_MAX_VOTE_L < " . $score_less;
    $requete .= " limit 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-N3r]", $requete);
  }
}

?>