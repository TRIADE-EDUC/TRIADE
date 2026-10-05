<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bookreader.class.php,v 1.38.2.3 2024/12/18 08:44:58 qvarin Exp $

global $visionneuse_path;
require_once($visionneuse_path."/classes/mimetypes/affichage.class.php");
//require_once($visionneuse_path."/classes/mimetypes/converter_factory.class.php");
// require_once($visionneuse_path."/../classes/docbnf.class.php");
// require_once($visionneuse_path."/../classes/docbnf_zip.class.php");
// ini_set("display_errors",1);
// error_reporting(E_ALL & ~E_NOTICE);

require_once($visionneuse_path."/classes/mimetypes/bookreader/bookreaderPDF.class.php");
require_once($visionneuse_path."/classes/mimetypes/bookreader/bookreaderBNF.class.php");
require_once($visionneuse_path."/classes/mimetypes/bookreader/bookreaderEPUB.class.php");
require_once($visionneuse_path."/classes/mimetypes/bookreader/bookreaderZIP.class.php");

// ini_set("display_errors", 1);
// error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

class bookreader extends affichage{
	public $doc;					//le document numérique à afficher
	public $driver;				//class driver de la visionneuse
	public $params;				//paramètres éventuels
	public $toDisplay= array();	//tableau des infos à afficher
	public $tabParam = array();	//tableau décrivant les paramètres de la classe
	public $parameters = array();	//tableau des paramètres de la classe
	public $mimeTypeClass;			//instance selon le mimetype

	public function __construct($doc=0) {
    	if($doc){
    		$this->doc = $doc;
    		$this->driver = $doc->driver;
    		$this->params = $doc->params;
    		$this->getParamsPerso();
    		$this->getTabParam();
    		$this->allowedFunction = array(
    			"getPage",
    			"getWidth",
    			"getHeight",
    			"search",
    			"getBookmarks",
    			"getPDF",
    			"getCSS",
    			"getPageCount"
    		);

    		$this->driver->cleanCache();
    		if (!$this->driver->isInCache($this->doc->id)) {
    			$this->driver->copyCurrentDocInCache();
    		}
    	}
    	if(!empty($this->doc->mimetype)) {
	    	switch($this->doc->mimetype){
	    		case "application/pdf" :
	    		case "application/x-pdf" :
	    			$this->mimeTypeClass = new bookreaderPDF($this->doc, $this->parameters);
	    			break;
	    		case "application/bnf" :
	    		case "application/bnf+zip" :
	    			$this->mimeTypeClass = new bookreaderBNF($this->doc);
	    			break;
	    		case "application/epub+zip" :
	    		case "application/octet-stream" :
	    			$this->mimeTypeClass = new bookreaderEPUB($this->doc, $this->parameters);
	    			break;
	     		case "application/zip" :
	     			$this->mimeTypeClass = new bookreaderZIP($this->doc, $this->parameters);
	     			break;
	    	}
    	}
    }

    public function fetchDisplay(){
    	global $visionneuse_path, $charset;
     	//le titre
    	$this->toDisplay["titre"] = $this->doc->titre;
    	//la visionneuse pdf

    	if($this->parameters['pdf_allowed'] && ($this->doc->mimetype != "application/pdf")){
    		$this->toDisplay["doc"].="
    		<div><a href='".$this->driver->getVisionneuseUrl("lvl=ajax&explnum_id=".$this->doc->id."&method=getPDF")."' target='_blank'>G&eacute;n&eacute;rer un PDF</a></div>";
    	}
    	$this->toDisplay["doc"].="
    	<script>
    		window.onload = function(){
				checkSize();
				document.getElementById('bookreader_frame').src='".$this->driver->getVisionneuseUrl("lvl=afficheur&explnum=".$this->doc->id."&myPage=".$this->driver->params['page']."&user_query=".$this->driver->params['user_query'])."';
    		}
			function checkSize(){
				var iframe= document.getElementById('bookreader_frame');
				if (isNaN(iframe.width) || iframe.width/getFrameWidth() <= 0.9 || iframe.width/getFrameWidth() >= 1){
					iframe.style.width = '95%';
					iframe.style.height = ((getFrameHeight()-40-80)*0.95)+'px';
				}
			}

		</script>
		<iframe title='".htmlentities($this->doc->titre, ENT_QUOTES, $charset)."' id='bookreader_frame'></iframe>
		";
    	//if ($this->parameters['autoresize'] == 1)
		//la description
		$this->toDisplay["desc"] = $this->doc->desc;
		return $this->toDisplay;
    }

    public function render(){
    	global $visionneuse_path, $charset, $lang;

    	$subst_style="";
    	if(file_exists($visionneuse_path."/classes/mimetypes/bookreader/BookReader/BookReader_subst.css")){
    		$subst_style="<link rel='stylesheet' type='text/css' href='$visionneuse_path/classes/mimetypes/bookreader/BookReader/BookReader_subst.css'/>";
    	}
    	//$doc = new docbnf_zip($visionneuse_path."/temp/".$this->doc->id);
    	print "<!DOCTYPE html>
<html>
    <head>
    	<meta charset=\"".$charset."\">
    	<link rel='stylesheet' type='text/css' href='$visionneuse_path/classes/mimetypes/bookreader/BookReader/BookReader.css'/>
    	<link rel='stylesheet' type='text/css' href='$visionneuse_path/classes/mimetypes/bookreader/BookReader/BookReaderPerso.css'/>
    	<link rel='stylesheet' type='text/css' href='".$this->driver->getVisionneuseUrl("lvl=ajax&explnum_id=".$this->doc->id."&method=getCSS'/>")."
	    $subst_style
	    <script src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/excanvas.compiled.js'></script>
    	<script src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/jquery-1.4.2.min.js'></script>
		<script src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/jquery-ui-1.8.5.custom.min.js?v=3.0.9'></script>
		<script src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/dragscrollable.js'></script>
		<script src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/jquery.colorbox-min.js'></script>
		<script src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/jquery.ui.ipad.js'></script>
		<script src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/jquery.bt.min.js'></script>";
	    if(file_exists($visionneuse_path.'/classes/mimetypes/bookreader/BookReader/BookReader_'.$lang.'.js')) {
	    	print "<script src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/BookReader_".$lang.".js?v=3.0.9'></script>";
	    } else {
	    	print "<script src='$visionneuse_path/classes/mimetypes/bookreader/BookReader/BookReader.js?v=3.0.9'></script>";
	    }
    	print "<script>
    		$(document).ready(function() {
	    		br = new BookReader();

				br.initUIStrings = function(){
					var titles = { '.logo': '', // $$$ update after getting OL record
						'.zoom_in': '".addslashes($this->message->table['zoom_in'])."',
						'.zoom_out': '".addslashes($this->message->table['zoom_out'])."',
						'.onepg': '".addslashes($this->message->table['onepg'])."',
						'.twopg': '".addslashes($this->message->table['twopg'])."',
						'.thumb': '".addslashes($this->message->table['thumb'])."',
						'.print': '".addslashes($this->message->table['print'])."',
						'.embed': '".addslashes($this->message->table['embed'])."',
						'.link': '".addslashes($this->message->table['link'])."',
						'.bookmark': '".addslashes($this->message->table['bookmark'])."',
						'.read': '".addslashes($this->message->table['read'])."',
						'.share': '".addslashes($this->message->table['share'])."',
						'.info': '".addslashes($this->message->table['info'])."',
						'.full': '".addslashes($this->message->table['full'])."',
						'.book_left': '".addslashes($this->message->table['book_left'])."',
						'.book_right': '".addslashes($this->message->table['book_right'])."',
						'.book_up': '".addslashes($this->message->table['book_up'])."',
						'.book_down': '".addslashes($this->message->table['book_down'])."',
						'.play': '".addslashes($this->message->table['play'])."',
						'.pause': '".addslashes($this->message->table['pause'])."',
						'.BRdn': '".addslashes($this->message->table['BRdn'])."',
						'.BRup': '".addslashes($this->message->table['BRup'])."',
						'.book_top': '".addslashes($this->message->table['book_top'])."',
						'.book_bottom': '".addslashes($this->message->table['book_bottom'])."',
						'.contrast': '".addslashes($this->message->table['contrast'])."'
					};
					if ('rl' == this.pageProgression) {
				        titles['.book_leftmost'] = '".addslashes($this->message->table['book_leftmost'])."';
				        titles['.book_rightmost'] = '".addslashes($this->message->table['book_rightmost'])."';
    				} else { // LTR
				        titles['.book_leftmost'] = '".addslashes($this->message->table['book_leftmost'])."';
				        titles['.book_rightmost'] = '".addslashes($this->message->table['book_rightmost'])."';
				    }

				    for (var icon in titles) {
				        if (titles.hasOwnProperty(icon)) {
				            $('#BookReader').find(icon).attr('title', titles[icon]);
				        }
				    }
				}

				br.updateNavPageNum = function(index) {
					var pageNum = this.getPageNum(index);
				    var pageStr;
				    if (pageNum[0] == 'n') { // funny index
				        pageStr = index + 1 + ' / ' + this.numLeafs; // Accessible index starts at 0 (alas) so we add 1 to make human
				    } else {
				        pageStr = 'Page ' + pageNum + '/' + this.numLeafs ;
				    }
				    $('#pagenum .currentpage').text(pageStr);
					$('#BRpager a').attr('aria-valuenow', pageNum);
				}

				//Ici on génère le bloc d'informations...
				".$this->genereInfos()."

				//mode par défaut
				br.mode = br.".$this->parameters['mode_affichage'].";

	    		br.pagesSizes= ".$this->getJSPagesSizes().";

				br.getPageWidth = function(index) {
					if(this.pagesSizes[this.getPageNum(index)]){
				   		return this.pagesSizes[this.getPageNum(index)].width;
				   	}else return 480;
				}

				br.getPageHeight = function(index) {
					if(this.pagesSizes[this.getPageNum(index)]){
				    	return this.pagesSizes[this.getPageNum(index)].height;
				   	}else return 640;
				}

				br.getPageURI = function(index, reduce, rotate) {
				    // reduce and rotate are ignored in this simple implementation, but we
				    // could e.g. look at reduce and load images from a different directory
				    // or pass the information to an image server
				    var url = '".$this->driver->getVisionneuseUrl("lvl=ajax&explnum_id=".$this->doc->id."&nodesc=1&method=getPage")."&page='+(index+1);
				    return url;
				}

				// Return which side, left or right, that a given page should be displayed on
				br.getPageSide = function(index) {
					if (0 == (index & 0x1)) {
						return 'R';
					} else {
						return 'L';
					}
				}

				br.getSpreadIndices = function(pindex) {
					var spreadIndices = [null, null];
					if ('rl' == this.pageProgression) {
						// Right to Left
						if (this.getPageSide(pindex) == 'R') {
							spreadIndices[1] = pindex;
							spreadIndices[0] = pindex + 1;
						} else {
							// Given index was LHS
							spreadIndices[0] = pindex;
							spreadIndices[1] = pindex - 1;
						}
					} else {
						// Left to right
						if (this.getPageSide(pindex) == 'L') {
							spreadIndices[0] = pindex;
							spreadIndices[1] = pindex + 1;
						} else {
							// Given index was RHS
							spreadIndices[1] = pindex;
							spreadIndices[0] = pindex - 1;
						}
					}
					return spreadIndices;
				}

				br.getPageNum = function(index) {
				    return index+1;
				}

				br.leafNumToIndex = function(leaf) {
				    return leaf-1;
				}

				br.numLeafs = ".$this->getPageCount()." ;

				// Book title and the URL used for the book title link
				br.bookTitle= '".addslashes($this->doc->titre)."';
				br.bookUrl  = '".addslashes($this->getBookURL())."';
				br.logoURL = '".addslashes($this->driver->getUrlBase())."';

				// Override the path used to find UI images
				br.imagesBaseURL = '".$visionneuse_path."/classes/mimetypes/bookreader/BookReader/images/';

				br.getEmbedCode = function(frameWidth, frameHeight, viewParams) {
				    return \"\";
				}

				br.search = function(term){
					$('#textSrch').blur();
					var url = '".$this->driver->getVisionneuseUrl("lvl=ajax&explnum_id=".$this->doc->id."&nodesc=1&method=search")."&user_query='+".pmb_escape(false)."(term);
					term = term.replace(/\//g, ' '); // strip slashes, since this goes in the url
					this.searchTerm = term;
					this.showProgressPopup('<img id=\"searchmarker\" src=\"'+this.imagesBaseURL + 'marker_srch-on.png'+'\"> Recherche en cours');
					$.ajax({url:url, dataType:'json',success : br.BRSearchCallback});
				}

				br.getBookmarksCallback = function(result){
					if(result){
						for(var i=0 ; i<result.length ; i++){
							if(result[i].deep>0)
							br.addChapter(result[i].label, result[i].analysis_page*1, result[i].page*1-1);
						}
					}
				}

				// Let's go!
				br.init();

				$('#BRreturn a').attr('target', '_blank');
				$('#BRreturn a').attr('title', '".addslashes($this->doc->titre) ." - ". addslashes($this->message->table['new_tab'])."');
				$('#BRtoolbar').find('.read').hide();
				$('#BRtoolbar .play').show();
				$('#BRtoolbar .share').hide();
				$('#BRpager a').attr('role', 'slider');
				$('#BRpager a').attr('aria-valuemax', ". $this->getPageCount() .");
				$('#BRpager a').attr('aria-valuemin', 1);
				$('#BRpager a').attr('aria-valuenow', 1);
				$('#textSrch').attr('aria-label', '".addslashes($this->message->table['BR_search'])."');
				$('#textSrch').attr('placeholder', '".addslashes($this->message->table['BR_search'])."');
				$('#btnSrch').html('".addslashes($this->message->table['BR_search_btn'])."');
				$('#BRnavCntlBtm').attr('role', 'button');

				//affichage des Bookmarks !
				$.ajax({url:'".$this->driver->getVisionneuseUrl("lvl=ajax&explnum_id=".$this->doc->id."&nodesc=1&method=getBookmarks")."', dataType:'json',success : br.getBookmarksCallback});";

    if($this->driver->params['page']){
    	print "
    			br.jumpToIndex(".($this->driver->params['page']-1).");";
    }

	if($this->parameters['allow_search']){
		print "
				//Recherche auto à l'ouverture
				var user_query = '".$this->driver->params['user_query']."';
				if ((user_query) && (user_query != '*')) {
					br.search(user_query);
				}";
	}
	print "
	    	});
    	</script>
    </head>
    <body>
    	<div id='BookReader'></div>
    </body>
</html>";

    }

    public function getBookURL(){
    	return $this->driver->getVisionneuseUrl("lvl=afficheur&explnum=".$this->doc->id);
    }

    public function getCSS(){
    	header("Content-Type: text/css");
    	$width = 0;
    	if($this->parameters['logo_url']){
    		$img = imagecreatefromstring(file_get_contents($this->parameters['logo_url']));
			if($img){
    			$width = imagesx($img);
			}
    	}
    	print "#BRtoolbar a.logo {
	display: block;
	float: left;
	width: ".$width."px;
	height: 40px;
	margin: 0 5px;
	background: transparent url(".$this->parameters['logo_url'].") no-repeat 0 0;
}";
    }

    public function getTabParam(){
    	if(!isset($this->parameters['pdf_creator'])) $this->parameters['pdf_creator'] = '';
    	if(!isset($this->parameters['pdf_footer_name'])) $this->parameters['pdf_footer_name'] = '';
    	if(!isset($this->parameters['pdf_footer_link'])) $this->parameters['pdf_footer_link'] = '';
    	if(!isset($this->parameters['logo_url'])) $this->parameters['logo_url'] = '';
    	if(!isset($this->parameters['resolution_image'])) $this->parameters['resolution_image'] = '';
    	$this->tabParam = array(
    		"pdf_allowed"=>array("type"=>"checkbox","name"=>"pdf_allowed","value"=>1,"desc"=>"Autoriser l'export au format PDF"),
    		"pdf_creator"=>array("type"=>"text","name"=>"pdf_creator","value"=>$this->parameters['pdf_creator'],"desc"=>"Auteur du PDF"),
     		"pdf_footer_name"=>array("type"=>"text","name"=>"pdf_footer_name","value"=>$this->parameters['pdf_footer_name'],"desc"=>"Libell&eacute; du pied de page"),
     		"pdf_footer_link"=>array("type"=>"text","name"=>"pdf_footer_link","value"=>$this->parameters['pdf_footer_link'],"desc"=>"Lien du pied de page"),
     		"logo_url"=>array("type"=>"text","name"=>"logo_url","value"=>$this->parameters['logo_url'],"desc"=>"Lien vers l'image du logo (en haut &agrave; gauche) hauteur maximum : 40px"),
    		"resolution_image"=>array("type"=>"text","name"=>"resolution_image","value"=>$this->parameters['resolution_image'],"desc"=>"R&eacute;solution des images g&eacute;n&eacute;r&eacute;es par pdftoppm"),
    		"format_image"=>array("type"=>"radio","name"=>"format_image","value"=>array("jpeg" => "jpeg", "png" => "png", "imagick" => "imagick"),"desc"=>"Format des images g&eacute;n&eacute;r&eacute;es par pdftoppm. Choisir imagick si les attributs -png et -jpeg ne sont pas support&eacute;s par pdftoppm."),
    		"mode_affichage"=>array("type"=>"radio","name"=>"mode_affichage","value"=>array("constMode1up" => "Mode 1 page", "constMode2up" => "Mode 2 pages", "constModeThumb" => "Mode vignettes"),"desc"=>"Mode d'affichage par d&eacute;faut"),
    		"allow_search"=>array("type"=>"radio","name"=>"allow_search","value"=>array("0" => "Non", "1" => "Oui"),"desc"=>"Lancer automatiquement une recherche à l'ouverture"),
    	);
       	return $this->tabParam;
    }

    public function getParamsPerso(){
		$params = $this->driver->getClassParam('bookreader');

		$this->unserializeParams($params);

		$this->parameters['resolution_image'] = intval($this->parameters['resolution_image']);
        if(!$this->parameters['resolution_image'] ) {
		    $this->parameters['resolution_image'] = 100;
		}
		if($this->parameters['format_image'] == "") {
		    $this->parameters['format_image'] = "imagick";
		}
		if(!$this->parameters['mode_affichage']) {
		    $this->parameters['mode_affichage'] = "constMode1up";
		}
	}

	public function unserializeParams($paramsToUnserialized){
		$this->parameters = unserialize($paramsToUnserialized);
		if(!$this->parameters['print_allowed']) {
		    $this->parameters['print_allowed'] = 0;
		}
		return $this->parameters;
	}

	public function serializeParams($paramsToSerialized){

	    if(empty($paramsToSerialized['print_allowed'])) {
	        $paramsToSerialized['print_allowed'] = 0;
	    }

		$this->parameters = $paramsToSerialized;
		return serialize($paramsToSerialized);
	}

	/**
	 * Retourne la page demande
	 *
	 * @return int
	 */
	protected function getRequestedPage() {
		$page = isset($_GET['page']) ? $_GET['page'] : 1;
		$page = intval($page);

		if ($page < 1) {
			$page = 1;
		}

		if ($page > $this->getPageCount()) {
			$page = $this->getPageCount();
		}

		return $page;
	}

	public function getPage() {
		session_write_close();
		$this->mimeTypeClass->getPage($this->getRequestedPage());
	}

	public function getWidth() {
		$this->mimeTypeClass->getWidth($this->getRequestedPage());
	}

	public function getHeight(){
		$this->mimeTypeClass->getHeight($this->getRequestedPage());
	}

	public function search(){
		global $visionneuse_path;
		$user_query = 1;
		if(isset($_GET['user_query'])){
			$user_query = $_GET['user_query'];
		}
		$result = $this->mimeTypeClass->search($user_query);

		print json_encode(encoding_normalize::utf8_normalize($result));
	}

	public function getBookmarks(){
	    if (method_exists($this->mimeTypeClass, 'getBookmarks')) {
	        print json_encode(encoding_normalize::utf8_normalize($this->mimeTypeClass->getBookmarks()));
	    }
	    print json_encode(array());
	}

	public function getPDF(){
		global $visionneuse_path;
		if($this->parameters['pdf_allowed']){
			$pdfParams = array();

			//Auteur du PDF
			if(!$this->parameters['pdf_creator']){
				$this->parameters['pdf_creator'] = "PMB";
			}
			$pdfParams['creator'] = encoding_normalize::utf8_normalize($this->parameters['pdf_creator']);

			//Définition du footer
			if($this->parameters['pdf_footer_name'] || $this->parameters['pdf_footer_link']){
				$pdfParams['footers']=array(
					'all' => array(
					    'name' => encoding_normalize::utf8_normalize($this->parameters['pdf_footer_name']),
					    'link' => encoding_normalize::utf8_normalize($this->parameters['pdf_footer_link'])
					)
				);
			}

			//Nommage du fichier de sortie
			$pdfParams['outname'] = encoding_normalize::utf8_normalize($this->doc->titre.".pdf");

			$this->mimeTypeClass->getPDF($pdfParams);
		}
	}

	public function getJSPagesSizes(){
		$this->mimeTypeClass->getPagesSizes();
		$js = json_encode($this->mimeTypeClass->pagesSizes);
		return $js;
	}

	public function getPageCount(){
		global $visionneuse_path;

		return $this->mimeTypeClass->getPageCount();
	}

	public function genereInfos(){
		$infos = $this->doc->driver->getCurrentBiblioInfos();
		$bloc_infos = "br.buildInfoDiv = function(jInfoDiv) {
			//Le titre du document
			jInfoDiv.find('.BRfloatTitle a').attr({
				'href': this.bookUrl, 'alt': this.bookTitle}).text(this.bookTitle);
				//La première page en couverture...
				jQuery('<img>', {
					src : br.getPageURI(0),
					height : '200px',
				}).appendTo(jInfoDiv.find('.BRfloatCover'));
				jQuery('<br>', {}).appendTo(jInfoDiv.find('.BRfloatTitle'));
				jQuery('<br>', {}).appendTo(jInfoDiv.find('.BRfloatTitle'));";
		if ($infos['explnum_licence']) {
			$bloc_infos.= "
				jQuery('<p>".addslashes($infos['explnum_licence'])."</p>').appendTo(jInfoDiv.find('.BRfloatTitle'));";
		}
		$bloc_infos.= "
				jQuery('<br>', {}).appendTo(jInfoDiv.find('.BRfloatTitle'));
 				jQuery('<p>', {
 					text : 'Titre : ".addslashes($infos['title']['value'])."',
 				}).appendTo(jInfoDiv.find('.BRfloatTitle'));";
		if($infos['author']['value']){
			$bloc_infos.= "
				jQuery('<p>".addslashes(($infos['author']['label']))." : ".addslashes(($infos['author']['value']))."</p>').appendTo(jInfoDiv.find('.BRfloatTitle'));";
		}
		if($infos['date']['value']){
			$bloc_infos.= "
 				jQuery('<p>', {
 					text : '".addslashes(($infos['date']['label']))." : ".addslashes($infos['date']['value'])."',
 				}).appendTo(jInfoDiv.find('.BRfloatTitle'));";
		}
 		$bloc_infos.= "
 				jQuery(\"<p><a href='".$infos['permalink']['value']."' target='_blank'>".addslashes(($infos['permalink']['label']))."</a></p>\").appendTo(jInfoDiv.find('.BRfloatTitle'));";

//  		if($this->parameters['pdf_allowed']){
//  			$bloc_infos.= "
//  				jQuery(\"<p><a href='./visionneuse.php?lvl=ajax&explnum_id=".$this->doc->id."&method=getPDF' target='_blank'>Télécharger le PDF</a></p>\").appendTo(jInfoDiv.find('.BRfloatFoot'));";
//  		}
 		$bloc_infos.= "
			}";
		return $bloc_infos;
	}
}
?>
