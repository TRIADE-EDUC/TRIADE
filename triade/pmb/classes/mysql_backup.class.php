<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mysql_backup.class.php,v 1.14 2023/07/26 15:07:58 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition classe de gestion des sauvegardes

if ( ! defined( 'BACKUP_CLASS' ) ) {
  define( 'BACKUP_CLASS', 1 );

class mysql_backup {

// ---------------------------------------------------------------
//		propriétés de la classe
// ---------------------------------------------------------------

	public $host;					//---- host name e.g. localhost
	public $db;					//---- db name
	public $user;					//---- db username
	public $pass;					//---- db password
	public $fptr;					//---- file pointer
	public $filename;				//---- backup file concerned
	public $extension = '.bz2';	// extension for backup files
	public $buffer = '';			// a buffer
	public $dump = '';
// ---------------------------------------------------------------
//		mysql_backup() : constructeur
// ---------------------------------------------------------------

	public function __construct() {
		global $pmb_set_time_limit;
		@set_time_limit ($pmb_set_time_limit);
	}

//---- backup
//------------------------------------------

	public function backup() {
		if($this->open_backup_stream()) {
			$this->fetch_data();
			$this->close_backup_stream;
		} else {
			die("can't open file for backup");
		}
	}

//---- restore
//------------------------------------------

	public function restore($src) {
		$SQL = array();
		if (!empty($src)) {
			$this->filename = $src;
			if ($this->open_restore_stream() && !empty($this->buffer)) {
				// open source file
				$SQL = preg_split('/;\s*\n|;\n/m', bzdecompress($this->buffer));
				$nb_queries = count($SQL);
				for ($i = 0; $i < $nb_queries; $i++) {
				    if (!empty($SQL[$i])) {
						$result = pmb_mysql_query($SQL[$i]);
				    }
				}
			} else {
				die("can't open file to restore");
				return false;
			}
		}
		return true;
	}


//---- This will fetch data in the whole database
//------------------------------------------

	public function fetch_data() {
		//enumerate tables

		$res = pmb_mysql_list_tables(DATA_BASE);
		$i = 0;
		$ina = array();
		$fna = array();

		while($i < pmb_mysql_num_rows($res)) {
			$update_a_faire=0; /* permet de gérer les id auto_increment qui auraient pour valeur 0 */
			$table_name = pmb_mysql_tablename($res, $i);
			bzwrite ($this->fptr, "delete from $table_name;\n");
			$this->dump.="delete from $table_name;\n";
			//parse the field info first
			$res2=pmb_mysql_query("select * from {$table_name} order by 1 ");
			$nf=pmb_mysql_num_fields($res2);
			$nr=pmb_mysql_num_rows($res2);

			$fields = '';
			$values = '';
			for ($b=0;$b<$nf;$b++) {

				$fn=pmb_mysql_field_name($res2,$b);
				$ft=pmb_mysql_field_type($res2,$b);
				$fs=pmb_mysql_field_len($res2,$b);
				$ff=pmb_mysql_field_flags($res2,$b);

				$is_numeric=false;

				switch(strtolower($ft))
					{
					case "int":
						$is_numeric=true;
						break;

					case "blob":
						$is_numeric=false;
						break;

					case "real":
						$is_numeric=true;
						break;

					case "string":
						$is_numeric=false;
						break;

					case "unknown":
						switch(intval($fs))
							{
							case 4:
								// little weakness here...
								// there is no way (thru the PHP/MySQL interface)
								// to tell the difference between a tinyint and a year field type
								$is_numeric=true;
								break;

							default:
								$is_numeric=true;
								break;
							}
						break;

					case "timestamp":
						$is_numeric=true;
						break;

					case "date":
						$is_numeric=false;
						break;

					case "datetime":
						$is_numeric=false;
						break;

					case "time":
						$is_numeric=false;
						break;

					default:
						//future support for field types that are not recognized
						//(hopefully this will work without need for future modification)
						$is_numeric=true;
						//I'm assuming new field types will follow SQL numeric syntax..
						// this is where this support will breakdown
						break;
					}
				if ($fields) {
				    $fields .= ', '.$fn;
				} else {
				    $fields .= $fn;
				}
				$fna[$b] = $fn;
				$ina[$b] = $is_numeric;
			}

			//parse out the table's data and generate the SQL INSERT statements in order to replicate the data itself...

			for ($c = 0; $c < $nr; $c++) {
				$row = pmb_mysql_fetch_row($res2);
				$values = '';
				for ($d = 0; $d < $nf; $d++) {
					$data = strval($row[$d]);
					if (($d == 0) && (strval($row[$d]) == 0)) {
						/* traiter ici l'insertion avec valeur 1 pour id autoincrement et update à suivre */
					    if ($values) {
					        $values .= ', '.'1';
					    } else {
					        $values.= '1';
					    }
						$cle_update = pmb_mysql_field_name($res2, 0);
						$update_a_faire = 1;
					} else {
					    if ($ina[$d] == true) {
					        if ($values) {
					            $values .= ', '.intval($data);
					        } else {
					            $values .= intval($data);
					        }
					    } else {
					        if ($values) {
					            $values .= ", \"".pmb_mysql_escape_string($data)."\"";
					        } else {
					            $values .= "\"".pmb_mysql_escape_string($data)."\"";
					        }
					    }
					}
				}
				bzwrite ($this->fptr, "insert into $table_name ($fields) values ($values);\n");
				$this->dump.="insert into $table_name ($fields) values ($values);\n";
				if ($update_a_faire==1) {
					$update_a_faire=0;
					bzwrite ($this->fptr, "update $table_name set ".$cle_update."='0' where ".$cle_update."='1';\n");
					$this->dump.="update $table_name set ".$cle_update."='0' where ".$cle_update."='1';\n";
					}
			}

			pmb_mysql_free_result($res2);
			$i++;
		}

	}

//---- open out stream for backup
//------------------------------------------

	public function open_backup_stream() {
		global $backup_dir;
		$dir = pmb_preg_replace('/\/\s$|\/$/', '', $backup_dir);
		if(!$dir) $dir = '.';
		$this->filename = time();
		$out_file = $dir.'/'.$this->filename.$this->extension;
		$tmp = @touch($out_file);
		$this->fptr = @bzopen($out_file, 'w');
		if($this->fptr)
			return TRUE;
		else
			return FALSE;
	}

//---- open stream to restore
//------------------------------------------

	public function open_restore_stream() {
		global $backup_dir;
		$dir = pmb_preg_replace('/\/\s$|\/$/', '', $backup_dir);
		if(!$dir) $dir = '.';
		$in_file = $dir.'/'.$this->filename.$this->extension;
		$this->fptr = @fopen($in_file, 'rb');
		if($this->fptr) {
			$this->buffer = fread($this->fptr, filesize($in_file));
			fclose($this->fptr);
			return TRUE;
		} else {
			$this->buffer = '';
			return FALSE;
		}
	}


//---- closes backup stream
//------------------------------------------

	public function close_backup_stream() {
		if($this->fptr)
			fclose($this->fptr);
	}


} # fin de définition de la classe mysql_backup

} # fin de délaration

