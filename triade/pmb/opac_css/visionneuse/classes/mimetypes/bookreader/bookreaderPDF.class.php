<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bookreaderPDF.class.php,v 1.31.6.1.2.2 2025/02/10 15:03:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $visionneuse_path;
require_once($visionneuse_path."/classes/mimetypes/bookreader/PDFMetadata.class.php");

class bookreaderPDF {
	public $doc;			//le document PDF à traiter
	public $parameters;	//tableau décrivant les paramètres de la classe
	public $PDFMetadata;
	public $pagesSizes;

	public function __construct($doc,$parameters){
		$this->doc = $doc;
		$this->parameters = $parameters;
		$this->PDFMetadata = new PDFMetadata($this->doc->driver->get_cached_filename($this->doc->id));
		$this->getPagesSizes();
	}

	public function getPage($page){

		$format = $this->parameters['format_image'];

		switch ($format) {
			case "imagick":
			case "png":
				$extension = "png";
				$content_type = "image/x-png";
				break;
			case "jpeg":
				$extension = "jpg";
				$content_type = "image/jpeg";
				break;
		}

		$len = strlen($this->getPageCount());

		$file_name=$this->doc->driver->get_cached_filename("page_".$this->doc->id)."-".str_pad($page, $len,"0",STR_PAD_LEFT).".".$extension;
		if (!file_exists($file_name)) {
			if (stripos($_SERVER['SERVER_SOFTWARE'], "win")!==false || stripos(PHP_OS, "win")!==false ) {
				$extension = "png";
				$content_type = "image/x-png";
				$file_name=$this->doc->driver->get_cached_filename("page_".$this->doc->id)."-".str_pad($page, 6, "0", STR_PAD_LEFT).".".$extension;
			}
		}

		if (!file_exists($file_name)) {
			$resolution = $this->parameters['resolution_image'];
			if ($format == "imagick") {
				exec("pdftoppm -f $page -l $page -r ".$resolution." -cropbox ".$this->doc->driver->get_cached_filename($this->doc->id)." ".$this->doc->driver->get_cached_filename("page_".$this->doc->id));
				$imagick = new Imagick();
				$imagick->setResolution($resolution,$resolution);
				$source_file=$this->doc->driver->get_cached_filename("page_".$this->doc->id)."-".str_pad($page, $len, "0", STR_PAD_LEFT).".ppm";
				//selon la version, pdftoppm ne renvoie pas les mêmes noms de fichier...
				if (!file_exists($source_file)) {
					if (stripos($_SERVER['SERVER_SOFTWARE'], "win")!==false || stripos(PHP_OS, "win")!==false ) {
						$source_file=$this->doc->driver->get_cached_filename("page_".$this->doc->id)."-".str_pad($page, 6, "0", STR_PAD_LEFT).".ppm";
					}
				}
				if (!empty($source_file) && file_exists($source_file)) {
				    $imagick->readImage($source_file);
				    $imagick->writeImage($file_name);
				    unlink($source_file);
				}
			} else {
				if (stripos($_SERVER['SERVER_SOFTWARE'], "win")!==false || stripos(PHP_OS, "win")!==false ) {
					exec("pdftopng -f $page -l $page -r ".$resolution." ".$this->doc->driver->get_cached_filename($this->doc->id)." ".$this->doc->driver->get_cached_filename("page_".$this->doc->id));
				}else{
					exec("pdftoppm -f $page -l $page -r ".$resolution." -".$format." -cropbox ".$this->doc->driver->get_cached_filename($this->doc->id)." ".$this->doc->driver->get_cached_filename("page_".$this->doc->id));
				}
			}
		}
		if (file_exists($file_name)) {
			header("Content-Type: ".$content_type);
			if(!empty($this->doc->path)) {
				header('Content-disposition: inline; filename="'.basename($this->doc->path).'"');
			}
			print file_get_contents($file_name);
		}
	}

	public function getWidth($page){
		return $this->PDFMetadata->pagesSizes[$page]['width']*72/$this->parameters['resolution_image'];
	}

	public function getHeight($page){
		return $this->PDFMetadata->pagesSizes[$page]['height']*72/$this->parameters['resolution_image'];
	}

	public function getPagesSizes(){
		$this->pagesSizes= array();
		foreach($this->PDFMetadata->pagesSizes as $page => $size){
			$this->pagesSizes[$page] = array(
				'width' => $size['width']*72/$this->parameters['resolution_image'],
				'height' => $size['height']*72/$this->parameters['resolution_image']
			);
		}
	}

	public function search($user_query){
		global $charset;

		$matches = array();

		if (!file_exists($this->doc->driver->get_cached_filename($this->doc->id).".bbox")){
			exec("pdftotext -bbox -enc UTF-8 ".$this->doc->driver->get_cached_filename($this->doc->id)." ".$this->doc->driver->get_cached_filename($this->doc->id).".bbox");
		}

		$dom = new DOMDocument('1.0', 'UTF-8');
		// Hexadecimal In String
        //file_put_contents($this->doc->driver->get_cached_filename($this->doc->id).".bbox", str_replace(array(chr("0x01"),chr("0x02"),chr("0x1f"),chr("0x1e")),"",file_get_contents($this->doc->driver->get_cached_filename($this->doc->id).".bbox")));
		$contents = str_replace(array(chr(1),chr(2),chr(31),chr(30)),"",file_get_contents($this->doc->driver->get_cached_filename($this->doc->id).".bbox"));
		// suppression des caracteres non-imprimables
		$contents = pmb_preg_replace("/\\x0|[\x01-\x1f]/U","",$contents);
		file_put_contents($this->doc->driver->get_cached_filename($this->doc->id).".bbox", $contents);
		$dom->load($this->doc->driver->get_cached_filename($this->doc->id).".bbox");
		// On nettoie la recherche
		$user_query = strip_empty_words(strtolower(convert_diacrit($user_query)));

		$terms = explode(" ", $user_query);

		$pages = $dom->getElementsByTagName("page");
		$height = 0;
		$width = 0;

		//on parcourt les pages
		for($i=0 ; $i<$pages->length ; $i++){
			$current_page = $pages->item($i);
			$height = $current_page->getAttribute("height");
			$width = $current_page->getAttribute("width");

			$h_ratio = $this->getHeight($i+1)/$height;
			$w_ratio = $this->getWidth($i+1)/$width;

			$words = $current_page->getElementsByTagName("word");
			//on parcourt les mots du fichier
			for($j=0 ; $j<$words->length ; $j++){
				//on parcourt les termes de la recherche
				$current_word = $words->item($j);
				if ($charset == "iso-8859-1") $current_word_value = iconv("UTF-8", "ISO-8859-1//TRANSLIT",$current_word->nodeValue);
				else $current_word_value = $current_word->nodeValue;
				foreach($terms as $term){
					if(strpos(strtolower(convert_diacrit($current_word_value)),$term) !== false){
						//trouvé
						//texte à afficher en aperçu
						$text = "...";
						for ($k=$j-3 ; $k<=$j+3 ; $k++){
							if ($j == $k) $text .= "<span style='background-color:#CCCCFF;font-size:100%;font-style:normal;color:#000000;'>";
							if ($charset == "iso-8859-1") {
								$text .= htmlentities(iconv("UTF-8", "ISO-8859-1//TRANSLIT",$words->item($k)->nodeValue),ENT_QUOTES,$charset)." ";
							} else {
								$text .= htmlentities($words->item($k)->nodeValue,ENT_QUOTES,$charset);
							}
							if ($j == $k) $text .= "</span>";
							$text .= " ";
						}
						$text .= "... ";

						$matches[] = array(
							"text"=> $text,
							'par' => array(
								array(
									'page' => ($i+1),
									'page_height' => $height,
									'b' => $height,
									't' => 0,
									'page_width' => $width,
									'r' => $width,
									'l' =>  0,
									'boxes' => array(
										array(
											'l' => $current_word->getAttribute("xMin")*$w_ratio,
											'r' => $current_word->getAttribute("xMax")*$w_ratio,
											'b' => $current_word->getAttribute("yMax")*$h_ratio,
											't' => $current_word->getAttribute("yMin")*$h_ratio,
											'page' => ($i+1)
										)
									)
								)
							)
						);
					} else if (strpos($term, strtolower(convert_diacrit($current_word_value))) === 0) {
						// On regarde si le terme n'est pas découpé dans le document
						// Le mot correspond au début du terme, on va regarder les mots suivants
						$offset = 0;
						$word_index = $j;
						$word_index_value = $current_word_value;

						do {
							$offset += strlen(strtolower(convert_diacrit($word_index_value)));
							$word_index++;
							if ($charset == "iso-8859-1") $word_index_value = iconv("UTF-8", "ISO-8859-1//TRANSLIT",$words->item($word_index)->nodeValue);
							else $word_index_value = $words->item($word_index)->nodeValue;
						} while (strpos($term, strtolower(convert_diacrit($word_index_value)), $offset) === $offset);

						if ($offset >= strlen($term)) {
							// le terme à été trouvé
							//texte à afficher en aperçu
							$word_index--;
							$text = "...";
							for ($k=$j-3 ; $k<=$word_index+3 ; $k++){
								if ($j == $k) $text .= "<span style='background-color:#CCCCFF;font-size:100%;font-style:normal;color:#000000;'>";
								if ($charset == "iso-8859-1") {
									$text .= htmlentities(iconv("UTF-8", "ISO-8859-1//TRANSLIT",$words->item($k)->nodeValue),ENT_QUOTES,$charset);
								} else {
									$text .= htmlentities($words->item($k)->nodeValue,ENT_QUOTES,$charset);
								}
								if ($k == $word_index) $text .= "</span>";
								$text .= " ";
							}
							$text .= "... ";

							$matches[] = array(
								"text"=> $text,
								'par' => array(
									array(
										'page' => ($i+1),
										'page_height' => $height,
										'b' => $height,
										't' => 0,
										'page_width' => $width,
										'r' => $width,
										'l' =>  0,
										'boxes' => array(
											array(
												'l' => $current_word->getAttribute("xMin")*$w_ratio,
												'r' => $words->item($word_index)->getAttribute("xMax")*$w_ratio,
												'b' => $words->item($word_index)->getAttribute("yMax")*$h_ratio,
												't' => $current_word->getAttribute("yMin")*$h_ratio,
												'page' => ($i+1)
											)
										)
									)
								)
							);
						}
					} else {
						//perdu
						continue;
					}
				}
			}
		}
		return array('matches' => $matches);
	}

	public function getBookmarks(){
		return $this->PDFMetadata->getBookmarks();
	}

	public function getPDF(){

	}

	public function getPageCount(){
		return $this->PDFMetadata->nb_pages;
	}
}

?>