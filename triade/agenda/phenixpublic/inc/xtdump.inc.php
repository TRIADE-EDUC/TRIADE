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

###########################################################################
##                      -=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-               ##
##                      XT-DUMP v 0.7 :  Mysql Dump System               ##
##                      -=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-               ##
##                                                                       ##
## -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- ##
##                                                                       ##
##     Copyright (c) 2001-2003 by DreaXTeam (webmaster@dreaxteam.net)    ##
##                          http://dreaxteam.net                         ##
##                                                                       ##
## This program is free software. You can redistribute it and/or modify  ##
## it under the terms of the GNU General Public License as published by  ##
## the Free Software Foundation.                                         ##
###########################################################################

  /* Fonction retournant la date et l'heure actuelle - Actualy time function */
  function aff_date() {
    $date_now = date("d/m/Y, H:i");
    return $date_now;
  }

  function set2nul($val) {
    return (empty($val)) ? "0" : $val;
  }

  function get_length_sql($tab) {
    $taille = 0;
    for ($i=0;$i<count($tab);$i++) {
      $taille += strlen($tab[$i]);
    }
    return $taille;
  }

  /* Fonction de sauvegarde en mode Sql - Sql data dump function */
  function sqldumptable($table,$drptbl) {
    global $sv_s,$sv_d;

    $tabledump = array();
    $iCel = 0;
    $tabledump[0] = "";
    $champs = mysql_query("SHOW FIELDS FROM $table");
    if (@mysql_num_rows($champs)) {
      if ($sv_s) {
        if ($drptbl) {
          $tabledump[0] .= "DROP TABLE IF EXISTS $table;\n";
        }
        $tabledump[0] .= "CREATE TABLE $table (\n";
        $firstfield = 1;
        while ($champ = mysql_fetch_array($champs)) {
          if (!$firstfield) {
            $tabledump[0] .= ",\n";
          } else {
            $firstfield = 0;
          }
          $tabledump[0] .= "   `$champ[Field]` $champ[Type]";
          if ($champ['Null'] != "YES") {
            $tabledump[0] .= " NOT NULL";
          }
          if (!empty($champ['Default'])) {
            $tabledump[0] .= " default '$champ[Default]'";
          }
          if (!empty($champ['Extra'])) {
            $tabledump[0] .= " $champ[Extra]";
          }
        }

        $keys = mysql_query("SHOW KEYS FROM $table");
        while ($key = mysql_fetch_array($keys)) {
          $kname = $key['Key_name'];
          if ($kname != "PRIMARY" and $key['Non_unique'] == 0) {
            $kname = "UNIQUE|$kname";
          }
          if(!is_array($index[$kname])) {
            $index[$kname] = array();
          }
          $index[$kname][] = $key['Column_name'];
        }
        @mysql_free_result($keys);

        while(list($kname, $columns) = @each($index)) {
          $tabledump[0] .= ",\n";
          $colnames = implode($columns,",");
          if($kname == "PRIMARY") {
            $tabledump[0] .= "   PRIMARY KEY ($colnames)";
          } else {
            if (substr($kname,0,6) == "UNIQUE") {
              $kname = substr($kname,7);
            }
            $tabledump[0] .= "   KEY $kname ($colnames)";
          }
        }
        $tabledump[0] .= "\n);\n\n";
        $iCel++;
      }
      @mysql_free_result($champs);

      // Donnees - Data
      if ($sv_d) {
        $rows = mysql_query("SELECT * FROM $table");
        if (mysql_num_rows($rows)) {
          $numfields = mysql_num_fields($rows);
          $insertinstruction .= "INSERT INTO $table (";
          $cptchamp = -1;
          $firstfield = 1;
          while (++$cptchamp<$numfields) {
            if (!$firstfield) {
              $insertinstruction .= ",";
            } else {
              $firstfield = 0;
            }
            $insertinstruction .= mysql_field_name($rows,$cptchamp);
          }
          $insertinstruction .= ") VALUES \n";
          $insertline = $insertinstruction;
          $cpt = 1;
          while ($row = mysql_fetch_array($rows)) {
            if (strlen($insertline)>50000) {
              $tabledump[$iCel++] .= $insertline.";\n";
              $insertline = $insertinstruction;
              $cpt = 1;
            }
            if ($cpt>1)
              $insertline .= ",\n";
            $insertline .= "(";
            $cptchamp = -1;
            $firstfield = 1;
            while (++$cptchamp<$numfields) {
              if (!$firstfield) {
                $insertline .= ",";
              } else {
                $firstfield = 0;
              }
              if (!isset($row[$cptchamp])) {
                $insertline .= "NULL";
              } else {
                $insertline .= "'".mysql_escape_string($row[$cptchamp])."'";
              }
            }
            $insertline .= ")";
            $cpt++;
          }
          $tabledump[$iCel] .= $insertline.";\n\n";
        }
        @mysql_free_result($rows);
      }
    }
    return $tabledump;
  }

  /* Fonction de sauvegarde en mode CSV - CSV data dump function */
  function csvdumptable($table) {
    global $sv_s,$sv_d;

    $csvdump = array();
    $iCel = 0;
    $csvdump[0] = "";
    $champs = mysql_query("SHOW FIELDS FROM $table");
    if (@mysql_num_rows($champs)) {
      $csvdump[0] .= "\n\n## Table : ".$table."\n";
      if ($sv_s) {
        $firstfield = 1;
        while ($champ = mysql_fetch_array($champs)) {
          if (!$firstfield) {
            $csvdump[0] .= ",";
          } else {
            $firstfield = 0;
          }
          $csvdump[0] .= "'" . $champ[Field] . "'";
        }
        $csvdump[0] .= "\n";
      }
      @mysql_free_result($champs);
      $iCel++;

      // Donnees - Data
      if ($sv_d) {
        $rows = mysql_query("SELECT * FROM $table");
        $numfields = mysql_num_fields($rows);
        while ($row = mysql_fetch_array($rows)) {
          $cptchamp = -1;
          $firstfield = 1;
          while (++$cptchamp<$numfields) {
            if (!$firstfield) {
              $csvdump[$iCel] .= ",";
            } else {
              $firstfield = 0;
            }
            if (!isset($row[$cptchamp])) {
              $csvdump[$iCel] .= "NULL";
            } else {
              $csvdump[$iCel] .= "'" . addslashes($row[$cptchamp]) . "'";
            }
          }
          $csvdump[$iCel++] .= "\n";
        }
      }
      @mysql_free_result($rows);
    }
    return $csvdump;
  }

  /* Ecrire dans le fichier de sauvegarde - Write into the backup file */
  function write_file($data,$filetype) {
    global $g_fp;

    if ($filetype == "1") {
      gzwrite ($g_fp,$data);
    } else {
      fwrite ($g_fp,$data);
    }
  }

  /* Ouvrir le fichier de sauvegarde - Open the backup file */
  function open_file($filename,$filetype,$nombase) {
    global $g_fp,$f_nm;

    if ($filetype == "1") {
      $g_fp = gzopen($filename,"wb9");
    } else {
      $g_fp = fopen ($filename,"w");
    }
    $f_nm[] = $filename;
    $data = "";
    $data .= "##\n";
    $data .= "## ".trad("XTDUMP_ABOUT_1")." \n";
    $data .= "## http://dreaxteam.net \n";
    $data .= "## ------------------------- \n";
    $data .= "## ".trad("XTDUMP_ABOUT_2")." ".aff_date()."\n";
    $data .= "## ".trad("XTDUMP_ABOUT_3")." $nombase \n";
    $data .= "## -------------------------\n\n";

    write_file($data,$filetype);
    unset($data);
  }

  /* Renvoie la taille actuelle du fichier */
  function file_pos($filetype) {
    global $g_fp;

    if ($filetype == "1") {
      return gztell ($g_fp);
    } else {
      return ftell ($g_fp);
    }
  }

  /* Fermer le fichier de sauvegarde - Close the backup file */
  function close_file($filetype) {
    global $g_fp;

    if ($filetype == "1") {
      gzclose ($g_fp);
    } else {
      fclose ($g_fp);
    }
  }

  /* Fonction gerant les differents types de sauvegarde */
  function do_backup($prefixe, $droptable, $filetype, $dbname, $fzmax) {
  global $fext,$ftbl,$tbl,$fcut,$path,$dte,$fc,$gz,$nbf;
    if ($fext == ".sql") {
      // Cas un fichier par table
      if ($ftbl) {
        while (list($i) = each($tbl)) {
          // On ne traite que les tables "Phenix"
          if (ereg("^${prefixe}",$tbl[$i])) {
            $temp = sqldumptable($tbl[$i],$droptable);
            $sz_t = get_length_sql($temp);
            if ($sz_t>0) {
              // Cas taille maximale du fichier definie
              if ($fcut) {
                open_file($path."backup/dump_".$dte.$tbl[$i].$fc.".sql".$gz,$filetype,$dbname);
                $nbf = 0;
                for ($iCel=0; $iCel<count($temp); $iCel++) {
                  if ((file_pos($filetype) + 6 + strlen($temp[$iCel])) < $fzmax) {
                    write_file($temp[$iCel],$filetype);
                  } else {
                    close_file($filetype);
                    $nbf++;
                    open_file($path."backup/dump_".$dte.$tbl[$i].$fc."_".$nbf.".sql".$gz,$filetype,$dbname);
                    write_file($temp[$iCel],$filetype);
                  }
                }
                close_file($filetype);
              }
              // Cas pas de taille maximale
              else {
                open_file($path."backup/dump_".$dte.$tbl[$i].$fc.".sql".$gz,$filetype,$dbname);
                for ($iCel=0; $iCel<count($temp); $iCel++) {
                  write_file($temp[$iCel],$filetype);
                }
                close_file($filetype);
                $nbf = 1;
              }
              $tblsv = $tblsv."<B>".$tbl[$i]."</B>, ";
            }
          }
        }
      }
      // Cas un fichier pour la base
      else {
        while (list($i) = each($tbl)) {
          // On ne traite que les tables "Phenix"
          if (ereg("^${prefixe}",$tbl[$i])) {
            $temp = sqldumptable($tbl[$i],$droptable);
            $sz_t = get_length_sql($temp);
            if ($sz_t>0) {
              // Cas taille maximale du fichier definie
              if ($fcut && ((file_pos($filetype) + $sz_t) > $fzmax)) {
                for ($iCel=0; $iCel<count($temp); $iCel++) {
                  if ((file_pos($filetype) + 6 + strlen($temp[$iCel])) < $fzmax) {
                    write_file($temp[$iCel],$filetype);
                  } else {
                    close_file($filetype);
                    $nbf++;
                    open_file($path."backup/dump_".$dte.$dbname.$fc."_".$nbf.".sql".$gz,$filetype,$dbname);
                    write_file($temp[$iCel],$filetype);
                  }
                }
              }
              // Cas pas de taille maximale
              else {
                for ($iCel=0; $iCel<count($temp); $iCel++) {
                  write_file($temp[$iCel],$filetype);
                }
              }
              $tblsv = $tblsv."<B>".$tbl[$i]."</B>, ";
            }
          }
        }
      }
    }
    else if ($fext == ".csv") {
      // Cas un fichier par table
      if ($ftbl) {
        while (list($i) = each($tbl)) {
          // On ne traite que les tables "Phenix"
          if (ereg("^${prefixe}",$tbl[$i])) {
            $temp = csvdumptable($tbl[$i]);
            $sz_t = get_length_sql($temp);
            if ($sz_t>0) {
              // Cas taille maximale du fichier definie
              if ($fcut) {
                open_file($path."backup/dump_".$dte.$tbl[$i].$fc.".csv".$gz,$filetype,$dbname);
                $nbf = 0;
                for ($iCel=0; $iCel<count($temp); $iCel++) {
                  if ((file_pos($filetype) + 6 + strlen($temp[$iCel])) < $fzmax) {
                    write_file($temp[$iCel],$filetype);
                  } else {
                    close_file($filetype);
                    $nbf++;
                    open_file($path."backup/dump_".$dte.$tbl[$i].$fc."_".$nbf.".csv".$gz,$filetype,$dbname);
                    write_file($temp[$iCel],$filetype);
                  }
                }
                close_file($filetype);
              }
              // Cas pas de taille maximale
              else {
                open_file($path."backup/dump_".$dte.$tbl[$i].$fc.".csv".$gz,$filetype,$dbname);
                for ($iCel=0; $iCel<count($temp); $iCel++) {
                  write_file($temp[$iCel],$filetype);
                }
                close_file($filetype);
                $nbf = 1;
              }
              $tblsv = $tblsv."<B>".$tbl[$i]."</B>, ";
            }
          }
        }
      }
      // Cas un fichier pour la base
      else {
        while (list($i) = each($tbl)) {
          // On ne traite que les tables "Phenix"
          if (ereg("^${prefixe}",$tbl[$i])) {
            $temp = csvdumptable($tbl[$i]);
            $sz_t = get_length_sql($temp);
            if ($sz_t>0) {
              // Cas taille maximale du fichier definie
              if ($fcut && ((file_pos($filetype) + $sz_t) > $fzmax)) {
                for ($iCel=0; $iCel<count($temp); $iCel++) {
                  if ((file_pos($filetype) + 6 + strlen($temp[$iCel])) < $fzmax) {
                    write_file($temp[$iCel],$filetype);
                  } else {
                    close_file($filetype);
                    $nbf++;
                    open_file($path."backup/dump_".$dte.$dbname.$fc."_".$nbf.".csv".$gz,$filetype,$dbname);
                    write_file($temp[$iCel],$filetype);
                  }
                }
              }
              // Cas pas de taille maximale
              else {
                for ($iCel=0; $iCel<count($temp); $iCel++) {
                  write_file($temp[$iCel],$filetype);
                }
              }
              $tblsv = $tblsv."<B>".$tbl[$i]."</B>, ";
            }
          }
        }
      }
    }
    return $tblsv;
  }
?>
