<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: epub.class.php,v 1.9 2023/12/07 15:18:26 dgoron Exp $

global $visionneuse_path;
require_once($visionneuse_path."/classes/mimetypes/affichage.class.php");
require_once($visionneuse_path."/../classes/epubData.class.php");
require_once($visionneuse_path."/classes/mimetypes/converter_factory.class.php");

class epub extends affichage{
	public $doc;					//le document numérique à afficher
	public $driver;				//class driver de la visionneuse
	public $params;				//paramètres éventuels
	public $toDisplay= array();	//tableau des infos à afficher
	public $tabParam = array();	//tableau décrivant les paramètres de la classe
	public $parameters = array();	//tableau des paramètres de la classe
	public $ebook; 				//l'objet ebook

    public function __construct($doc=0) {
    	if($doc){
    		$this->doc = $doc;
    		$this->driver = $doc->driver;
    		$this->params = $doc->params;
    		$this->getParamsPerso();
    	}
    }

    public function fetchDisplay(){
        global $visionneuse_path, $charset;

    	//le document
    	$this->driver->cleanCache();
  		if (!$this->driver->isInCache($this->doc->id)) {
    		$this->driver->copyCurrentDocInCache();
    	}
    	$ebook = new epubData($this->driver->get_cached_filename($this->doc->id));
     	//le titre
    	$this->toDisplay["titre"] = $this->doc->titre;
    	//la visionneuse
    	$this->toDisplay["doc"]="
    	<div id='divEpub' style='display:block;margin:auto'>
    		<div id='ePubFrameTocViewer' style='height:100%;width:25%;border:1px solid #000000;float:left;text-align:left;overflow:auto'>
    		";
    	$identPrecedent=-1;
    	foreach ($ebook->toc as $tocItem) {
    		if ($tocItem["level"]==0 && $identPrecedent>=0) {
    			$this->toDisplay["doc"] .= "<br>";
    		}
    		$this->toDisplay["doc"] .= "<div style='text-indent:".($tocItem["level"]*10)."px'>
    										<a onclick='goPageToc(\"".addslashes($tocItem["content"])."\");return false;' href='#'>
    											".$tocItem["text"]."
    										</a>
    									</div>";
    		$identPrecedent=$tocItem["level"];
    	}
    	$this->toDisplay["doc"] .= "
    		</div>
    		<iframe 
                title='".htmlentities($this->doc->titre, ENT_QUOTES, $charset)."' 
                id='ePubFrameViewer' 
                name='ePubFrameViewer' 
                style='height:100%;width:70%'>
            </iframe>
    		<div style='text-align:center'>
    			<img src='".$visionneuse_path."/images/zoom_plus.gif' alt='zoom_plus' onClick='zoomIn();return false;' style='display:inline;'/>
    			&nbsp;
				<img src='".$visionneuse_path."/images/zoom_moins.gif' alt='zoom_moins' onClick='zoomOut();return false;' style='display:inline;'/>
    			&nbsp;
    			<img src='".$visionneuse_path."/images/first.gif' alt='first' onClick=\"pageCours=0;goPage(pages[pageCours]['href']);return false;\" style='display:inline;'/>
    			&nbsp;
	        	<img src='".$visionneuse_path."/images/prev.gif' alt='previous' onClick=\"changePage('-');\" style='display:inline;'/>
	        	&nbsp;
	        	page <input type='text' id='pageNum' name='pageNum' value='0' size='1'/ onChange='appelPage();'> / ".count($ebook->pages)."
				&nbsp;
    			<img src='".$visionneuse_path."/images/next.gif' alt='next' onClick=\"changePage('+');\" style='display:inline;'/>
    			&nbsp;
    			<img src='".$visionneuse_path."/images/last.gif' alt='last' onClick=\"pageCours=".(count($ebook->pages)-1).";goPage(pages[pageCours]['href']);return false;\" style='display:inline;'/>
    		</div>
    	</div>
    	<div class='row'></div>
    	<script>
    		var pages = ".json_encode($ebook->pages).";
    		var pageCours = 0;
    		var idiv= document.getElementById('divEpub');
    		var iframe= document.getElementById('ePubFrameViewer');
    		var pageNumText = document.getElementById('pageNum');
    		var fontSize = 1;
    		var fichierActuel = '';

    		function changePage(sens) {
				var ok = true;
				if (sens == '+') {
					if (pages[pageCours+1]) {
						pageCours++;
					} else {
						ok = false;
					}
				}
				if (sens == '-') {
					if (pages[pageCours-1]) {
						pageCours--;
					} else {
						ok = false;
					}
				}
				if (ok) {
					goPage(pages[pageCours]['href']);
				}
			}

			function goPage(page) {
				//On charge la page
				iframe.src='visionneuse.php/".$this->driver->driver_name."/".$this->doc->id."/'+page;
			}

			function appelPage() {
				var maPage = pageNumText.value;
				maPage = parseInt(maPage);
				if (isNaN(maPage)) {
					alert('Numéro de page non valide');
				} else {
					maPage--; //Le tableau commence à l'indice 0
					if (pages[maPage]) {
						pageCours = maPage;
						goPage(pages[pageCours]['href']);
					} else {
						alert('Numéro de page non valide');
					}
				}
			}

			function goPageToc(page) {
				for (var i= 0; i < pages.length; i++) {
				    if (page == pages[i]['href']) {
				    	pageCours = i;
				    	break;
				    }
				}
				goPage(page);
			}

			function resizeDivConteneur () {
				idiv.style.width = '".$this->parameters["size_x"]."%';
				idiv.style.height = ((getFrameHeight()-40-80)*".($this->parameters["size_y"]/100).")+'px';
			}

			function zoomIn() {
				fontSize += 0.1;
				iframe.contentDocument.body.style.fontSize = fontSize + 'em';
			}

			function zoomOut() {
				if (fontSize > 0.11) {
					fontSize -= 0.1;
					iframe.contentDocument.body.style.fontSize = fontSize + 'em';
				}
			}

			function trouvePageNum() {
				var ancre = new Array();
				nb_key = 0;
				for (var i in pages) {
					pg = pages[i]['href'].split('#');
					if (pg[0] == fichierActuel) {
						var ancreTmp = new Array();
						ancreTmp['numPage'] = i;
						ancreTmp['ancre'] = pg[1];
						ancre.push(ancreTmp);
						nb_key++;
					}
				}
				if (nb_key == 1) {
					pageCours = parseInt(ancre[0]['numPage']);
				} else {
					var doc = iframe.contentDocument;
					var scroll = doc.documentElement.scrollTop;
					if (typeof(iframe.innerHeight) == 'number') {
						var hauteurFrame = iframe.innerHeight;
					} else if(doc.documentElement && doc.documentElement.clientHeight) {
						var hauteurFrame = doc.documentElement.clientHeight;
					}
					for (var i=0; i<ancre.length; i++) {
						if (doc.getElementById(ancre[i]['ancre'])) {
							var y = doc.getElementById(ancre[i]['ancre']).offsetTop - scroll;
							if (y >= 0 && y < (hauteurFrame/10)) {
								pageCours = parseInt(ancre[i]['numPage']);
								break;
							} else if (y >= 0 && y > (hauteurFrame/10)) {
								pageCours = parseInt(ancre[i]['numPage'])-1;
								break;
							} else if(i==(ancre.length-1)) {
								//on est sur la dernière ancre, rien de détecté : elle est au dessus
								pageCours = parseInt(ancre[i]['numPage']);
							}
						}
					}
				}
				pageNumText.value = pageCours+1;
			}

			function monInit() {
				//div conteneur
				resizeDivConteneur();
				//docnum
				goPage(pages[pageCours]['href']);
			}

			function pageChargee() {
				//Fichier actuel
				var tmpFrameSrc = frames['ePubFrameViewer'].location.href.split('/');
				var frameSrc = tmpFrameSrc[(tmpFrameSrc.length-1)];
				tmpFrameSrc = frameSrc.split('#');
				fichierActuel = tmpFrameSrc[0];

				//Numéro de page en cours
				trouvePageNum();

				//On reprend le dernier niveau de zoom
				try {
					iframe.contentWindow.body.style.fontSize = fontSize + 'em';
				}catch(err){
					iframe.contentDocument.body.style.fontSize = fontSize + 'em';
				}

				//on met à jour sur scroll
	    		try {
					iframe.contentWindow.onscroll=function(){
		    			trouvePageNum();
		    		}
				}catch(err){
					iframe.contentDocument.onscroll=function(){
		    			trouvePageNum();
		    		}
				}
			}

    		if (window.attachEvent) {
    			window.attachEvent('onload', monInit);
    		} else {
    			window.addEventListener('load', monInit, false);
    		}

    		if (iframe.attachEvent) {
    			iframe.attachEvent('onload', pageChargee);
    		} else {
    			iframe.addEventListener('load', pageChargee, false);
    		}

    	</script>
    	";
    	//la description
		$this->toDisplay["desc"] = $this->doc->desc;
    	return $this->toDisplay;
    }

    public function render(){
    	$ebook = new epubData($this->driver->get_cached_filename($this->doc->id));
		if (substr($this->driver->getParam("page"),-3)=="css"){
    		header("Content-Type: text/css");
    	}else{
    		header("Content-Type: text/html;charset='".$ebook->charset."'");
    	}
    	print $ebook->getPageContent($this->driver->getParam("page"));
    }

    public function getTabParam(){
    	if(!isset($this->parameters['size_x'])) $this->parameters['size_x'] = '';
    	if(!isset($this->parameters['size_y'])) $this->parameters['size_y'] = '';
    	$this->tabParam = array(
			"size_x"=>array("type"=>"text","name"=>"size_x","value"=>$this->parameters['size_x'],"desc"=>"Largeur du document en % de l'espace visible"),
			"size_y"=>array("type"=>"text","name"=>"size_y","value"=>$this->parameters['size_y'],"desc"=>"Hauteur du document en % de l'espace visible")
		);
       	return $this->tabParam;
    }

	public function getParamsPerso(){
		$params = $this->driver->getClassParam('epub');
		$this->unserializeParams($params);
		if($this->parameters['size_x'] == 0) $this->parameters['size_x'] = $this->driver->getParam("maxX");
		if($this->parameters['size_y'] == 0) $this->parameters['size_y'] = $this->driver->getParam("maxY");
	}

	public function unserializeParams($paramsToUnserialized){
		$this->parameters = unserialize($paramsToUnserialized);
		return $this->parameters;
	}

	public function serializeParams($paramsToSerialized){
		$this->parameters =$paramsToSerialized;
		return serialize($paramsToSerialized);
	}
}
?>
