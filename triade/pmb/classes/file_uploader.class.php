<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: file_uploader.class.php,v 1.4.2.1 2024/09/06 07:21:37 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class file_uploader {
	
	public function __construct() {
	}
	
	public static function getBytes($val) {
		$last = strtolower($val[strlen($val) - 1]);
		$Bytes = intval(trim($val));
		switch ($last) {
			case 'g':
				$Bytes *= 1024;
			case 'm':
				$Bytes *= 1024;
			case 'k':
				$Bytes *= 1024;
		}
		return $Bytes;
	}
	
	/*
	 * Fonction qui dézippe dans le bon répertoire
	 */
	public static function unzip($filename, $up_place, $path, $id_rep) {
		global $charset, $base_path;
		
		$unzipped_files = array();
		
		$zip = new zip($filename);
		$zip->readZip();
		$cpt = 0;
		if ($up_place && $path != '') {
			$up = new upload_folder($id_rep);
		}
		
		if (is_array($zip->entries) && count($zip->entries)) {
			foreach ( $zip->entries as $file ) {
				$file_name_for_get_file_content = $file['fileName'];
				
				$encod = mb_detect_encoding($file['fileName'], "UTF-8,ISO-8859-1");
				if ($encod && ($encod == 'UTF-8') && ($charset == "iso-8859-1")) {
					$file['fileName'] = encoding_normalize::utf8_decode($file['fileName']);
				} elseif ($encod && ($encod == 'ISO-8859-1') && ($charset == "utf-8")) {
					$file['fileName'] = encoding_normalize::utf8_normalize($file['fileName']);
				}
				
				$file['fileName'] = static::clean_file_name($file['fileName']);
				
				if ($up_place && $path != '') {
					$chemin = $path;
					if ($up->isHashing()) {
						$hashname = $up->hachage($file['fileName']);
						@mkdir($hashname);
						$filepath = $up->encoder_chaine($hashname . $file['fileName']);
					} else
						$filepath = $up->encoder_chaine($up->formate_nom_to_path($chemin) . $file['fileName']);
						// On regarde si le fichier existe avant de le créer
						$continue = true;
						$compte = 0;
						$filepath_tmp = $filepath;
						do {
							if (! file_exists($filepath_tmp)) {
								$continue = false;
							} else {
								$compte++;
								if (preg_match("/^(.+)(\..+)$/i", $filepath, $matches)) {
									$filepath_tmp = $matches[1] . "_" . $compte . $matches[2];
								} else {
									$filepath_tmp = $filepath . "_" . $compte;
								}
							}
						} while ( $continue );
						if ($compte) {
							$filepath = $filepath_tmp;
						}
						$fh = fopen($filepath, 'w+');
						fwrite($fh, $zip->getFileContent($file_name_for_get_file_content));
						fclose($fh);
						if ($compte) {
							if (preg_match("/^(.+)(\..+)$/i", $file['fileName'], $matches)) {
								$file['fileName'] = $matches[1] . "_" . $compte . $matches[2];
							} else {
								$file['fileName'] = $file['fileName'] . "_" . $compte;
							}
						}
				} else {
					$chemin = $base_path . '/temp/' . $file['fileName'];
					$fh = fopen($chemin, 'w');
					fwrite($fh, $zip->getFileContent($file['fileName']));
					fclose($fh);
					$base = true;
				}
				
				$unzipped_files[$cpt]['chemin'] = $chemin;
				$unzipped_files[$cpt]['nom'] = $file['fileName'];
				$unzipped_files[$cpt]['base'] = $base;
				$cpt++;
			}
		}
		return $unzipped_files;
	}
	
	public static function clean_file_name($filename){
		
		$filename = convert_diacrit($filename);
		$filename = preg_replace('/[^\x20-\x7E]/','_', $filename);
		$filename = str_replace(',', '_', $filename);
		return $filename;
	}
	
	public static function get_limit() {
		$maxUpload = static::getBytes(ini_get('upload_max_filesize')); // can only be set in php.ini and not by ini_set()
		$maxPost = static::getBytes(ini_get('post_max_size'));         // can only be set in php.ini and not by ini_set()
		$memoryLimit = static::getBytes(ini_get('memory_limit'));
		if($memoryLimit > -1){
			return min($maxUpload, $maxPost, $memoryLimit);
		}else{
			return min($maxUpload, $maxPost);
		}
	}
	
	public static function get_file(){
		global $charset;
		
		$headers = getallheaders();
		//Uniformisons les retours en minuscules pour la compatibilité sur tous les environnements
		$headers = array_change_key_case($headers, CASE_LOWER);
		if($charset == 'utf-8') {
		    $headers['x-file-name'] = encoding_normalize::utf8_encode($headers['x-file-name']);
		}
		$protocol = $_SERVER["SERVER_PROTOCOL"];
		
		if (!isset($headers['content_length'])) {
			if (!isset($headers['x-file-size'])) {
				header($protocol.' 411 Length Required');
				exit('Header \'Content-Length\' not set.');
			}else{
				$headers['content-length']=preg_replace('/\D*/', '', $headers['x-file-size']);
			}
		}
		
		if (isset($headers['x-file-size'], $headers['x-file-name'])) {
			
			$file = new stdClass();
			$file->name = basename($headers['x-file-name']);
			$file->filename = preg_replace('/[^ \.\w_\-\(\)]*/', '', basename(reg_diacrit($headers['x-file-name'])));
			$file->size = preg_replace('/\D*/', '', $headers['x-file-size']);
			
			$limit = static::get_limit();
			if ($headers['content-length'] > $limit) {
				header($protocol.' 403 Forbidden');
				exit('File size to big. Limit is '.$limit. ' bytes.');
			}
			
			$i=1;
			while(file_exists("./temp/".$file->filename)){
				if($i==1){
					$file->filename = substr($file->filename,0,strrpos($file->filename,"."))."_".$i.substr($file->filename,strrpos($file->filename,"."));
				}else{
					$file->filename = substr($file->filename,0,strrpos($file->filename,($i-1).".")).$i.substr($file->filename,strrpos($file->filename,"."));
				}
				$i++;
			}
			return $file;
		}else {
			header($protocol.' 500 Internal Server Error');
			static::debug($headers);
			exit('Correct headers are not set.');
		}
	}
	
	public static function debug($tab){
		//FROM STORAGE
		highlight_string(print_r($tab,true));
	}
	
} /* fin de définition de la classe */