<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docbnf.class.php,v 1.6.4.2 2025/03/17 08:15:42 qvarin Exp $


if (stristr ($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/fpdf.class.php");

class docbnf {
	protected $path;
	public $ref;
	public $arkNumber;
	public $ocr;
	public $available_resolution;
	public $nb_pages;
	public $resolution;
	public $advertissement = "";
	public $tdm;
	public $pagesSizes=array();
	public $refnum;
	public $bookmarks;
	public $convert;
	public $textContent;
	public $cover;

	public function __construct($path,$resolution="D"){
		$this->path = realpath($path);
		$this->resolution = $resolution;
		$this->init();
	}

	public function init(){
		//Si on peut parser le contenu
		if($this->parse()){
			//on récupère le ref
			$this->getRef();
			//on récupère l'ArkNumber
			$this->getArkNumber();
			//on récupère le nombre de pages
			$this->getNbPages();
			//on regarde les résolutions disponibles!
			$this->getAvailableResolution();
			//on regarde si on a la couche OCR
			$this->checkOcr();
			//on regarde si on a une page d'avertissement
			$this->getAdvertissement();
			//on ajuste la résolution au besoin
			$this->adjustResolution();
			//tableau des tailles de pages
			$this->getPagesSizes();
		}else{
			die();
		}
	}

	public function get_file_path($file){
		return $this->path."/".$file;
	}

	protected function parse(){
		$dh = opendir($this->path);
		$found = false;
		$refnum = "";
		while(($file = readdir($dh))!== false){
			//on cherche le refNum
			if((substr($file,0,1) === "X" || substr($file,0,1) === "x") && (substr($file,-4) == ".XML" || substr($file,-4) == ".xml")){
				$refnum = $file;
				$found =true;
			}else if ($file == "advertissement.png"){
				$this->advertissement = $file;
			}
		}
		if($found){
			//on a trouver le refnum, on va pouvoir regarder ce qu'il contient...
 			$this->refnum = new domDocument();
 			$this->refnum->load(realpath($this->get_file_path($refnum)));
		}
		return $found;
	}

	public function getRef(){
		if(!$this->ref){
			//on va chercher la référence!
			$doc =  $this->refnum->getElementsByTagName('document')->item(0);
			$this->ref = $doc->getAttribute("identifiant");
		}
		return $this->ref;
	}

	public function getArkNumber(){
		if(!$this->arkNumber){
		//l'identifiant Ark;
 			$references = $this->refnum->getElementsByTagName('reference');
 			foreach($references as $reference){
 				if($reference->getAttribute('type') == "NOTICEBIBLIOGRAPHIQUE"){
 					$this->arkNumber = $reference->nodeValue;
 					break;
 				}
 			}
		}
		return $this->arkNumber;
	}

	public function getNbPages(){
		if(!$this->nb_pages){
			//le nombre de page
 			$this->nb_pages =  $this->refnum->getElementsByTagName('nombrePages')->item(0)->nodeValue;
		}
		return $this->nb_pages;
	}

	public function getAvailableResolution(){
		if(!$this->available_resolution){
			$this->available_resolution=array();

			if($this->file_exists($this->get_file_path("A"))){
				$this->available_resolution[] = "A";
			}
			if($this->file_exists($this->get_file_path("C"))){
				$this->available_resolution[] = "C";
			}
			if($this->file_exists($this->get_file_path("D"))){
				$this->available_resolution[] = "D";
			}
			if($this->file_exists($this->get_file_path("E"))){
				$this->available_resolution[] = "E";
			}
			if($this->file_exists($this->get_file_path("F"))){
				$this->available_resolution[] = "F";
			}
			if($this->file_exists($this->get_file_path("T"))){
				$this->available_resolution[] = "T";
			}
		}
		return $this->available_resolution;
	}

	public function adjustResolution(){
		if(count($this->available_resolution) == 1){
			$this->resolution = $this->available_resolution[0];
		}
	}

	public function checkOcr(){
		if($this->file_exists($this->get_file_path("X"))){
			$this->ocr = true;
		}
	}

	public function getAdvertissement(){
		if($this->file_exists($this->get_file_path("advertissement.png"))){
			$this->advertissement = "advertissement.png";
		}
	}

	public function get_page_content($num_page=1){
		//pour chaque page
		$pages = $this->refnum->getElementsByTagName("vueObjet");
		foreach($pages as $page){
			//on va chercher l'image
			if($page->getAttribute("ordre") == $num_page){
				$img = $page->getElementsByTagName("image")->item(0);
				$image = $img->getAttribute("nomImage");
				$image = str_replace("T",$this->resolution,$image);
				break;
			}
		}
		if($this->file_exists($this->get_file_path($this->resolution."/".$image.".PNG"))){
			return $this->get_file_content($this->get_file_path($this->resolution."/".$image.".PNG"));
		} else if($this->file_exists($this->get_file_path($this->resolution."/".$image.".JPG"))){
			return $this->get_file_content($this->get_file_path($this->resolution."/".$image.".JPG"));
		}
	}

	public function get_file_content($file_path){
		return file_get_contents($this->get_file_path($file_path));
	}

	public function getWidth($num_page){
		return $this->pagesSizes[$num_page]['width'];
	}

	public function getHeight($num_page){
		return $this->pagesSizes[$num_page]['height'];
	}

	public function file_exists($file){
		return file_exists($this->get_file_path($file));
	}

	public function search($user_query){
		$matches = array();
		//pour chaque page
		$terms = explode(" ",strtolower(convert_diacrit($user_query)));

		$pages = $this->refnum->getElementsByTagName("vueObjet");
		foreach($pages as $page){
			//on va chercher la couche OCR
			$img = $page->getElementsByTagName("image")->item(0);
			$image = $img->getAttribute("nomImage");
			$ocr = str_replace("T","X",$image);
			$num_page = str_replace("T","",$image);
			if($this->file_exists($this->get_file_path("X/".$ocr.".xml.gz"))){
				ob_start();
				readgzfile($this->get_file($this->get_file_path("X/".$ocr.".xml.gz")));
				$file = ob_get_clean();
				$xml = new domDocument("1.0","iso-8859-1");
				$xml->loadXML($file);
				//on va avoir besoin de la résolution d'origine pour calculer le ratio...
				$page = $xml->getElementsByTagName("Page")->item(0);
				$original_width = $page->getAttribute('WIDTH');
				$original_height = $page->getAttribute('HEIGHT');
				$height = $this->getHeight($num_page);
				$width = $this->getWidth($num_page);

				$h_ratio = ($height/$original_height);
				$w_ratio = ($width/$original_width);

				$strings = $xml->getElementsByTagName('String');
				foreach($strings as $string){
					foreach($terms as $term){
					    if(strtolower(convert_diacrit(encoding_normalize::utf8_decode($string->getAttribute("CONTENT")))) == $term){
							$matches[] = array(
								"text"=> $this->get_paragraphe($string),
								'par' => array(
									array(
										'page' => intval($num_page),
										'page_height' => $height,
										'b' => $height,
										't' => 0,
										'page_width' => $width,
										'r' => $width,
										'l' =>  0,
										'boxes' => array(
											array(
												'l' => $string->getAttribute("HPOS")*$w_ratio,
												'r' => ($string->getAttribute("HPOS")+$string->getAttribute("WIDTH"))*$w_ratio,
												'b' => ($string->getAttribute("VPOS")+$string->getAttribute("HEIGHT"))*$h_ratio,
												't' => $string->getAttribute("VPOS")*$h_ratio,
												'page' => intval($num_page)
											)
										)
									)
								)
							);
						}
					}
				}
			}
		}
		return array('matches' => $matches);
	}

	public function get_paragraphe($string){
		$current = $string;
		$paragraphe = "";
		while($current->nodeName != "TextBlock"){
			$current = $current->parentNode;
		}

		for($i=0; $i<$current->childNodes->length ; $i++){
			$line = $current->childNodes->item($i);
			if($line->nodeName == "TextLine"){
				for($j=0 ; $j<$line->childNodes->length ; $j++){
					$node = $line->childNodes->item($j);
					switch($node->nodeName){
						case "SP" :
							$paragraphe.= " ";
							break;
						case "String" :
							if($node->getAttribute("CONTENT") == $string->getAttribute("CONTENT")){
							    $paragraphe.= "{{{".encoding_normalize::utf8_decode($node->getAttribute("CONTENT"))."}}}";
							}else {
								$paragraphe.= encoding_normalize::utf8_decode($node->getAttribute("CONTENT"));
							}
							break;
						default :
							$paragraphe.= " ";
							break;
					}
					$paragraphe.= " ";
				}
			}
		}
		return $paragraphe;
	}

	public function getTDM(){
		if(!$this->tdm && $this->file_exists($this->get_file_path("T".$this->ref.".xml"))){

			$xml = new domDocument();
			$xml->load($this->get_file($this->get_file_path("T".$this->ref.".xml")));
			$elems = $xml->getElementsByTagName("div0");
			foreach($elems as $elem){
				$this->parseTDM($xml->encoding,$elem);
			}
		}
		return $this->tdm;
	}

	public function getBookmarks(){
		$this->getTDM();
		return $this->bookmarks;
	}

	public function parseTDM($encoding,$element,$deep=0){
		foreach($element->childNodes as $child){
			switch ($child->tagName){
				case "head" :
					//pour récupérer une table des matières textuel
					if($deep>0)$this->tdm.= encoding_normalize::utf8_decode($child->nodeValue)."\n";
					//pour les bookmarks
					$item = array();
					$item['label'] = encoding_normalize::utf8_decode($child->nodeValue);
					$item['page'] = 1;
					$item['deep'] = $deep;
					$item['head'] = true;
					$this->bookmarks[]= $item;
					break;
				case "item" :
					//pour récupérer une table des matières textuel
					for ($i = 1 ; $i<$deep ; $i++){
						if($i<$deep) $this->tdm.="\t";
					}
					$seg = $child->getElementsByTagName("seg");
					if($seg->length ==0) $this->tdm.= $child->nodeValue."\n";
					else{
						for ($i = 1 ; $i<$deep ; $i++){
							if($deep>$i) $this->tdm.="\t";
						}
						$text = $seg->item(0)->nodeValue;
						$page = $child->getElementsByTagName("xref")->item(0)->nodeValue;
						$page = encoding_normalize::utf8_decode($page);
						$this->tdm.= "$text / page $page\n";
					}
					//pour les bookmarks
					if($seg->length ==0){
						$item =array();
						$item['label'] = encoding_normalize::utf8_decode($child->nodeValue);
						$item['page'] = 1;
						$item['deep'] = $deep;
						$item['head'] = true;
						$this->bookmarks[]= $item;
					}else{
						for($i=0 ; $i < $child->getElementsByTagName("xref")->length ; $i++ ){
							$item['label'] = $child->getElementsByTagName("seg")->item(0)->nodeValue." (p. ".$child->getElementsByTagName("xref")->item($i)->nodeValue.")";
							$item['page'] = $child->getElementsByTagName("xref")->item($i)->getAttribute('from');
							$item['deep'] = $deep;
							$item['analysis'] = true;
							$item['analysis_page'] = $child->getElementsByTagName("xref")->item($i)->nodeValue;
							if($item['page']){
								if(preg_match("/.*".$this->ref."\/([^.]+)[.].*/", $item['page'],$matches)){
									$item['page'] = intval($matches[1]);
								}
							}
							$item['label'] = encoding_normalize::utf8_decode($item['label']);
							$item['page'] = encoding_normalize::utf8_decode($item['page']);
							$this->bookmarks[]= $item;
						}
					}
					break;
				case "list" :
					$deep++;

				case "div1" :
				case "div2" :
					//pour récupérer une table des matières textuel
					$this->tdm.="\n";
					$this->parseTDM($encoding,$child,$deep);
					break;
			}
		}
		return $this->tdm;
	}

	public function generatePDF($pdfParams){
		$this->convert = new fpdf_bnf($pdfParams);
		$this->convert->SetMargins(0,0);
		$this->convert->SetAutoPageBreak(true,0);
		$title = encoding_normalize::utf8_decode($this->refnum->getElementsByTagName("titre")->item(0)->nodeValue);
		$this->convert->setTitle($title);
		$this->convert->Open();

		if($this->advertissement){
			$src_img = imagecreatefromstring($this->get_file_content(($this->get_file_path($this->advertissement))));
			$img=imagecreatetruecolor(imagesx($src_img),imagesy($src_img));
			ImageSaveAlpha($img, false);
			ImageAlphaBlending($img, false);
			imagefilledrectangle($img,0,0,imagesx($src_img),imagesy($src_img),imagecolorallocatealpha($img, 0, 0, 0, 127));
			imagecopyresized($img,$src_img,0,0,0,0,imagesx($src_img),imagesy($src_img),imagesx($src_img),imagesy($src_img));
			imagepng($img,"./temp/advertissement.png");
			$this->convert->Image(realpath("./temp/advertissement.png"));
			unlink(realpath("./temp/advertissement.png"));
		}

		$i=0;
		//pour chaque page
		$pages = $this->refnum->getElementsByTagName("vueObjet");
		foreach($pages as $page){
			//on va chercher l'image
			$img = $page->getElementsByTagName("image")->item(0);
			$dimension = $img->getAttribute("dimension");
			$resolution= $img->getAttribute("resolution");
			$size= $this->convert->getSize($dimension,$resolution);
			$this->convert->AddPage("P",$size);
 			$image = $img->getAttribute("nomImage");
 			$text = str_replace("T","X",$image);
 			$image_path = $this->getImagePath($image);
 			if($image_path){
 				$this->generateOCR($text);
  				$this->convert->Image($image_path,0,0,$size[0],$size[1]);
 				unlink($image_path);
				$i++;
 			}else return false;
		}
		$this->generateBookmarks();
		$this->convert->Output($pdfParams['outname'],"I");
		return true;
	}

	public function generateOCR($pageName){
		if($this->file_exists($this->get_file_path("X/".$pageName.".xml.gz"))){
			$filepath = $this->get_file($this->get_file_path("X/".$pageName.".xml.gz"));
			ob_start();
			readgzfile($filepath);
			$file=ob_get_clean();
			$xml = new domDocument("1.0","iso-8859-1");
			$xml->loadXML($file);
			$styleNodes = $xml->getElementsByTagName("Styles")->item(0);
			$styles = array();
			if($styleNodes->childNodes->length>0)
			foreach($styleNodes->childNodes as $style){
				foreach ($style->attributes as $name => $attrNode) {
					if ($name == 'FONTSTYLE'){
						switch ($attrNode->value){
							case "bold" :
								$fontstyle = "B";
								break;
							case "italics" :
								$fontstyle = "I";
								break;
							default :
								$fontstyle = "";
								break;
						}
						$styles[$style->getAttribute("ID")][$name] =trim($fontstyle);
					}else if ($name == "FONTFAMILY"){
						switch(trim($attrNode->value)){
							case "TIMES NEW ROMAN" :
								$styles[$style->getAttribute("ID")][$name] = "times";
								break;
							case "COURIER NEW" :
								$styles[$style->getAttribute("ID")][$name] = "courier";
								break;
							default :
								$styles[$style->getAttribute("ID")][$name] = "arial";
								break;
						}
					}else if( $name != 'ID') {
						switch ($attrNode->value){
							case "bold" :
								$fontstyle = "B";
								break;
							case "italics" :
								$fontstyle = "I";
								break;
							default :
								$fontstyle = "";
								break;
						}
						$styles[$style->getAttribute("ID")][$name] =trim($attrNode->value);
					}
				}
			}
			$line = array();
			$printSpaces = $xml->getElementsByTagName("PrintSpace");
			foreach($printSpaces as $printSpace){
				//block de texte...
				$textBlocks = $printSpace->getElementsByTagName('TextBlock');
				foreach($textBlocks as $textBlock){
					$block = array();
					foreach ($textBlock->attributes as $name => $attrNode) {
						$block[$name] =$attrNode->value;
					}
					$align = substr($styles[$block['STYLEREFS']]['ALIGN'],0,1);
					//ligne d'un block
					$textLignes = $textBlock->getElementsByTagName('TextLine');
					foreach($textLignes as $textLine){
						foreach ($textLine->attributes as $name => $attrNode) {
							$line[$name] =$attrNode->value;
						}
						//style de la ligne

						$line['CONTENT'] = "";
						foreach($textLine->childNodes as $child){
							if(($child->nodeName == "String" && $child->getAttribute("STYLEREFS") != $line['STYLEREFS']) || $child->nodeName == "SP"){
								$line['WIDTH'] = $child->getAttribute("HPOS")-$line['HPOS'];
								if($child->nodeName == "SP"){
									$line['CONTENT'].= " ";
									$line['WIDTH']+= $child->getAttribute("WIDTH");
								}
								$this->convert->setY($this->convert->convertPxToMm($line['VPOS']));
								$this->convert->setX($this->convert->convertPxToMm($line['HPOS']));
								$this->convert->SetFont($styles[$line['STYLEREFS']]['FONTFAMILY'],$styles[$line['STYLEREFS']]['FONTSTYLE'],$styles[$line['STYLEREFS']]['FONTSIZE']);
								$this->convert->Cell($this->convert->convertPxToMm($line['WIDTH']),$this->convert->convertPxToMm($line['HEIGHT']), encoding_normalize::utf8_decode($line['CONTENT']),0,0,$align);
								if($child->nodeName != "SP")
									$line['STYLEREFS'] = $child->getAttribute("STYLEREFS");
								$line['CONTENT'] = "";
								$line['HPOS'] = $line['HPOS']+$line['WIDTH'];

							}
							switch($child->nodeName){
								case "String" :
									$line['CONTENT'].=$child->getAttribute("CONTENT");
									$width= $child->getAttribute("WIDTH");
									break;
							}

						}
						if($line['CONTENT']){
							$line['WIDTH'] = $width;
							$this->convert->setY($this->convert->convertPxToMm($line['VPOS']));
							$this->convert->setX($this->convert->convertPxToMm($line['HPOS']));
							$this->convert->SetFont($styles[$line['STYLEREFS']]['FONTFAMILY'],$styles[$line['STYLEREFS']]['FONTSTYLE'],$styles[$line['STYLEREFS']]['FONTSIZE']);
							$this->convert->Cell($this->convert->convertPxToMm($line['WIDTH']),$this->convert->convertPxToMm($line['HEIGHT']), encoding_normalize::utf8_decode($line['CONTENT'])." ",0,0,$align);
							$this->textContent.=" ".encoding_normalize::utf8_decode($line['CONTENT']);
						}
					}
				}
			}
		}
	}

	public function generateBookmarks(){
		$this->getTDM();
		for($i=0 ; $i<count($this->bookmarks) ; $i++){
			$item = $this->bookmarks[$i];
			if($item['deep']>0 && $item['page'] == 1 && $this->bookmarks[$i+1] && $this->bookmarks[$i+1]['deep'] >$item['deep']){
				$item['page'] =$this->bookmarks[$i+1]['page'];
			}
			if($item['page']){
				if($this->advertissement){
					$item['page']++;
				}
				$this->convert->Bookmark($item['label'],$item['page'],$item['deep']);
			}
		}
	}

	public function getImagePath($image){
		$image = str_replace("T",$this->resolution,$image);
		$img_path = $this->resolution."/".$image.".PNG";
		if(!$this->file_exists($this->get_file_path($img_path))){
			$img_path = $this->resolution."/".$image.".JPG";
		}

		$number = intval(str_replace($this->resolution,"",$image));
 		$src_img = imagecreatefromstring($this->get_file_content(($this->get_file_path($img_path))));
 		$img=imagecreatetruecolor($this->getWidth($number),$this->getHeight($number));
		ImageSaveAlpha($img, false);
 		ImageAlphaBlending($img, false);
 		imagefilledrectangle($img,0,0,$this->getWidth($number),$this->getHeight($number),imagecolorallocatealpha($img, 0, 0, 0, 127));
 		imagecopyresized($img,$src_img,0,0,0,0,$this->getWidth($number),$this->getHeight($number),imagesx($src_img),imagesy($src_img));
 		imagejpeg($img,"./temp/".$image.".jpg");
 		return realpath("./temp/".$image.".jpg");
	}

	public function get_file($file_path){
		return $this->get_file_path($file_path);
	}

	public function getPagesSizes(){
		//pour chaque page
		if(!$this->pagesSizes){
			$pages = $this->refnum->getElementsByTagName("vueObjet");
			foreach($pages as $page){
				//on va chercher l'image
				$img = $page->getElementsByTagName("image")->item(0);
				$image = $img->getAttribute("nomImage");
				$image = str_replace("T",$this->resolution,$image);
				$dimensions = $img->getAttribute("dimension");
				$infos = explode(",",$dimensions);
				$this->pagesSizes[intval($page->getAttribute("ordre"))] =array(
					'width' => $infos[0],
					'height'=>  $infos[1]
				);
			}
		}
		return $this->pagesSizes;
	}

	public function getCover(){
		if(!$this->cover){
			$this->cover = "";
			$page = $this->refnum->getElementsByTagName("vueObjet")->item(0);
			//on va chercher l'image
			$img = $page->getElementsByTagName("image")->item(0);
			$image = $img->getAttribute("nomImage");
			$this->cover = $this->getImagePath($image);
		}
		return $this->cover;
	}

}

/*
 * Extention FPDF pour les documents BnF
 */

class fpdf_bnf extends FPDF{
	public $logoUrl;	//url du logo déposé sur chaque page...
	public $header;	//header de page...
	public $footers;	//pied de page du document...
	public $resolution;

	public $outlines=array();
	public $OutlineRoot;


	public function __construct($params=array()){
		parent::__construct();
		$this->footers = $params['footers'];
		$this->setCreator(encoding_normalize::utf8_decode($params['creator']));
		$this->SetTextColor(0);
		$this->cMargin = 0;
	}

	public function getSize($dimension,$resolution){
		$this->resolution = $resolution;
		$dimension = explode(",",$dimension);
		$resolution = explode(",",$resolution);
		$this->resolution = $resolution[0];
		$size = array(
				$this->convertPxToMm($dimension[0],$resolution[0]),
				$this->convertPxToMm($dimension[1],$resolution[1])
		);
		return $size;
	}

	public function convertPxToMm($px,$dpi=0){
		return ($px*25.4)/($dpi ? $dpi : $this->resolution);
	}

	public function Footer(){
		if ($this->logoUrl !="") $this->Image($this->logoUrl,10,8,20);
		if ($this->header) {
			$this->SetFont('Arial',"",14);
			$this->Cell(80); //Décalage à droite
			$this->Cell(30,10,$this->header,0,'C');
		}

		//si on a un footer spécificique pour la page courante...
		$footer = array();
		if(isset($this->footers['page'.$this->PageNo()])){
			$footer = $this->footers['page'.$this->PageNo()];
		}else if (isset($this->footers['all'])){
			$footer = $this->footers['all'];
		}

		//on applique le footer
		if($footer['name']){
			$this->SetY((-15*$this->h/297));
			$this->SetX((5*$this->w/210));
			//Police Arial italique 8
			$this->SetFont('Arial','I',(8*$this->w/210));
			if($footer['link']){
			    $this->Cell(0,10,encoding_normalize::utf8_decode($footer['name']),0,0,'',false,encoding_normalize::utf8_decode($footer['link']));
			}else{
			    $this->Cell(0,10,encoding_normalize::utf8_decode($footer['name']),0,0,'',false,'');
			}
		}
	}

	public function Error($msg){
		//erreur sur la classe FDPF, on la log avant d'arreter la génération...
// 		logMsg($msg);
		//Fatal error
		parent::Error($msg);
	}

	/*************************************************************************
	 *  Fonctions pour les signets (provient du site FPDF / Auteur : Olivier  *
	 		*  http://www.fpdf.org/fr/script/script1.php                            *
	 		*  Modifié par Arnaud RENOU (prise en compte d'un numéro de page        *
	 				*************************************************************************/

	public function Bookmark($txt, $page=-1, $level=0, $y=0)	{
		if($y==-1)
			$y=$this->GetY();
		if($page == -1){
			$page = $this->PageNo();
		}
		$this->outlines[]=array('t'=>$txt, 'l'=>$level, 'y'=>($this->h-$y)*$this->k, 'p'=>$page);
	}
	public function BookmarkUTF8($txt,$page=-1, $level=0, $y=0){
		$this->Bookmark($this->_UTF8toUTF16($txt),$page, $level,$y);
	}

	public function _putbookmarks(){
		$nb=count($this->outlines);
		if($nb==0)
			return;
		$lru=array();
		$level=0;
		foreach($this->outlines as $i=>$o)
		{
			if($o['l']>0)
			{
				$parent=$lru[$o['l']-1];
				//Set parent and last pointers
				$this->outlines[$i]['parent']=$parent;
				$this->outlines[$parent]['last']=$i;
				if($o['l']>$level)
				{
					//Level increasing: set first pointer
					$this->outlines[$parent]['first']=$i;
				}
			}
			else
				$this->outlines[$i]['parent']=$nb;
			if($o['l']<=$level and $i>0)
			{
				//Set prev and next pointers
				$prev=$lru[$o['l']];
				$this->outlines[$prev]['next']=$i;
				$this->outlines[$i]['prev']=$prev;
			}
			$lru[$o['l']]=$i;
			$level=$o['l'];
		}
		//Outline items
		$n=$this->n+1;
		foreach($this->outlines as $i=>$o)
		{
			$this->_newobj();
			$this->_out('<</Title '.$this->_textstring($o['t']));
			$this->_out('/Parent '.($n+$o['parent']).' 0 R');
			if(isset($o['prev']))
				$this->_out('/Prev '.($n+$o['prev']).' 0 R');
			if(isset($o['next']))
				$this->_out('/Next '.($n+$o['next']).' 0 R');
			if(isset($o['first']))
				$this->_out('/First '.($n+$o['first']).' 0 R');
			if(isset($o['last']))
				$this->_out('/Last '.($n+$o['last']).' 0 R');
			$this->_out(sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]',1+2*$o['p'],$o['y']));
			$this->_out('/Count 0>>');
			$this->_out('endobj');
		}
		//Outline root
		$this->_newobj();
		$this->OutlineRoot=$this->n;
		$this->_out('<</Type /Outlines /First '.$n.' 0 R');
		$this->_out('/Last '.($n+$lru[0]).' 0 R>>');
		$this->_out('endobj');
	}

	protected function _putresources(){
		parent::_putresources();
		$this->_putbookmarks();
	}

	protected function _putcatalog(){
		parent::_putcatalog();
		if(count($this->outlines)>0)
		{
			$this->_out('/Outlines '.$this->OutlineRoot.' 0 R');
			$this->_out('/PageMode /UseOutlines');
		}
	}

}
?>