<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: videojs.class.php,v 1.13 2023/12/07 15:18:26 dgoron Exp $

global $visionneuse_path;
require_once($visionneuse_path."/classes/mimetypes/affichage.class.php");

class videojs extends affichage {
	public $doc;
	public $driver;
	public $toDisplay = array();
	public $isDiarized = false;
	
	public function __construct($doc=0){
		if ($doc) {
			$this->doc = $doc;
			$this->driver = $doc->driver;
	    	$this->driver->cleanCache();
	    	$this->getParamsPerso();
	    	$this->allowedFunction = array(
	    			"getEmbedVideo",
	    	);
	    	if (!$this->driver->isInCache($this->doc->id)) {
	    		$this->driver->setInCache($this->doc->id,$this->driver->openCurrentDoc());
	   		}
	   		$this->checkIfDiarized();
		}
	}
	
	public function fetchDisplay(){
	    global $visionneuse_path, $charset;
		//le titre
		$this->toDisplay["titre"] = $this->doc->titre;
		
		$this->toDisplay["doc"].="
			<script src='./includes/javascript/ajax.js'></script>
			<link href='$visionneuse_path/classes/mimetypes/videojs/videoJS/video-js.css' rel='stylesheet'>
			<script src='$visionneuse_path/classes/mimetypes/videojs/videoJS/video.js'></script>
			<script>
				var fullScreen = function(){
					var video = this;
					
					open_fullscreen();
				};
				
				window.onload = function(){
					_V_('videojs', {
  						techOrder: ['html5', 'flash']
					});
					checkSize();
					var video = _V_('videojs');
 					video.on('fullscreenchange', fullScreen);
				}";
		
		// Fonction de redimensionnement
		$this->toDisplay["doc"] .= $this->getFunctionCheckSize();
		
		$this->toDisplay["doc"] .="
				function showCode(){
					var video_code = document.getElementById('video_code');
					
					if (video_code.style.display=='none'){
						video_code.style.display='inline-block';
					} else {
						video_code.style.display='none';
					} 
				}
				
			</script>
			<div>
				<video id='videojs' class='video-js vjs-default-skin vjs-big-play-centered' controls preload='auto' data-setup='{\"techOrder\": [\"html5\", \"flash\"]}' style='margin:auto;'>
				  <source src='".$this->driver->getVisionneuseUrl("lvl=afficheur&explnum=".$this->doc->id)."' type='".$this->doc->mimetype."'>";
		$nom_fichier=basename($this->doc->path,".".$this->doc->extension).".vtt";
		$requete="select e1.explnum_id from explnum as e1, explnum as e2 where e1.explnum_notice=e2.explnum_notice and e1.explnum_nomfichier='".addslashes($nom_fichier)."' and e2.explnum_id=".$this->doc->id;
		
		$res=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($res)) {
			$this->toDisplay["doc"].="			<track kind='subtitles' src='".$this->driver->getUrlBase()."doc_num.php?explnum_id=".pmb_mysql_result($res,0,0)."' srclang='fr' label='Français' default>\n";
		}
		$this->toDisplay["doc"].="
				</video>
			</div>
			<a onClick='showCode();' style='display:block;margin:5px 0'>Int&eacute;grer cette vid&eacute;o&nbsp;</a>
			<textarea id='video_code' style='display:none;' readonly='readonly' cols='60'>
                <iframe
                    title='".htmlentities($this->doc->titre, ENT_QUOTES, $charset)."'
                    id='iframe_video_".$this->doc->id."' 
                    width=\"560\" 
                    height=\"315\" 
                    src=\"".$this->driver->getVisionneuseUrl("lvl=ajax&explnum_id=".$this->doc->id."&method=getEmbedVideo")."\" 
                    frameborder=\"0\" 
                    allowFullScreen='true' 
                    mozAllowFullScreen='true' 
                    webkitAllowFullScreen='true'>
                </iframe>
            </textarea>";
		
		if ($this->isDiarized) $this->toDisplay["doc"] .= "
			<div>
				<span id='speech_timeline'></span>
			</div>
			<div id='speech_timeline_js'></div>
		".$this->getAssociateSpeakers();
		
		//la description
		$this->toDisplay["desc"] = $this->doc->desc;
		return $this->toDisplay;
	}
	
	public function getFunctionCheckSize() {
		if ($this->isDiarized) {
			$return = "
				function checkSize(){
					var video= _V_('videojs');
					video.width(".$this->parameters["size_x"].");
					video.height(".$this->parameters["size_y"].");
				}";
		} else {
			$return = "
				function checkSize(){
					var video= _V_('videojs');
					if (isNaN(video.width) || video.width/getFrameWidth() <= 0.9 || video.width/getFrameWidth() >= 1){
						video.width(getFrameWidth()*0.95);
						video.height((getFrameHeight()-40-80)*0.95);
					}
				}";
		}
		
		return $return;
	}
	
	public function getAssociateSpeakers() {
		global $base_path;
		$ajaxCall = "
		<script>
			function get_explnum_associate_svg(response) {
				document.getElementById('speech_timeline').innerHTML = response;
				var req = new http_request();		
				req.request('$base_path/ajax.php?module=ajax&categ=explnum_associate&sub=get_associate_js&explnum_id=".$this->doc->id."',0,'',1,get_explnum_associate_js,'');
			}
			
			function get_explnum_associate_js(response) {
			
				var script = document.createElement('script');
				script.innerHTML = response;
				
				document.getElementById('speech_timeline_js').appendChild(script);
			}
		
			var req = new http_request();		
			req.request('$base_path/ajax.php?module=ajax&categ=explnum_associate&sub=get_associate_svg&explnum_id=".$this->doc->id."',0,'',1,get_explnum_associate_svg,'');
		</script>
		";
		
		return $ajaxCall;
	}
	
	public function render(){
		global $visionneuse_path;
		header("Content-Type: ".$this->doc->mimetype);
		session_write_close();
		print $this->driver->openCurrentDoc();
	}
	
	public function getEmbedVideo(){
		global $visionneuse_path;
		
		print "<!DOCTYPE html>
	<html>
		<head>
			<link href='$visionneuse_path/classes/mimetypes/videojs/videoJS/video-js.css' rel='stylesheet'>
			<script src='$visionneuse_path/classes/mimetypes/videojs/videoJS/video.js'></script>
			<title>".$this->doc->titre."</title>
			<script>
				var isFullScreen = false;
				var iframe = window.parent.document.getElementById('iframe_video_".$this->doc->id."');
				var iframe_height = iframe.height;
				var iframe_width = iframe.width;
				
				var fullScreen = function(){
					var video = this;
					
					if (isFullScreen) {
						iframe.height=iframe_height;
						iframe.width=iframe_width;
						iframe.style.position='static';
						iframe.style.top=iframe.offsetParent.offsetTop;
						iframe.style.left=iframe.offsetParent.offsetLeft;
						
						isFullScreen = false;
					} else {
						iframe.height=window.parent.document.documentElement.clientHeight;
						iframe.width=window.parent.document.documentElement.clientWidth;
						iframe.style.position='absolute';
						iframe.style.top=-iframe.offsetParent.offsetTop;
						iframe.style.left=-iframe.offsetParent.offsetLeft;
						iframe.style.zIndex='100';
					
						isFullScreen = true;
					}
				};
				
				window.onload = function(){
					_V_('videojs', {
  						techOrder: ['html5', 'flash']
					});
					var video = _V_('videojs');
					video.width(iframe_width);
					video.height(iframe_height);
					if (navigator.userAgent.indexOf('MSIE')>-1) {
						video.addEvent('fullscreenchange', fullScreen);
					}
				}
				
			</script>
		</head>
		<body style='margin:0'>
			<video id='videojs' class='video-js vjs-default-skin' controls preload='auto' data-setup='{\"techOrder\": [\"html5\", \"flash\"]}'>
			  <source src='".$this->driver->getUrlBase()."/visionneuse.php?lvl=afficheur&explnum=".$this->doc->id."' type='video/mp4'>
			</video>
		</body>
	</html>";
	}
	
	private function checkIfDiarized() {
		$query = "select count(*) as nb from explnum_segments where explnum_segment_explnum_num = ".$this->doc->id;
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_num_rows($result)) {
			$nb = pmb_mysql_fetch_object($result)->nb;
			if ($nb > 0) $this->isDiarized = true;
		}
	}
	
	public function getTabParam(){
		$this->tabParam = array(
				"size_x"=>array("type"=>"text","name"=>"size_x","value"=>$this->parameters['size_x'],"desc"=>"Largeur du lecteur"),
				"size_y"=>array("type"=>"text","name"=>"size_y","value"=>$this->parameters['size_y'],"desc"=>"Hauteur du lecteur")
		);
		return $this->tabParam;
	}
		
	public function getParamsPerso(){
		$params = $this->driver->getClassParam('videojs');
		$this->unserializeParams($params);
	}
	
	public function unserializeParams($paramsToUnserialized){
		$this->parameters = unserialize($paramsToUnserialized);
		if(!$this->parameters['size_x']) $this->parameters['size_x'] = "480";
		if(!$this->parameters['size_y']) $this->parameters['size_y'] = "270";
		return $this->parameters;
	}
	
	public function serializeParams($paramsToSerialized){
		$this->parameters =$paramsToSerialized;
		if(!$this->parameters['size_x']) $this->parameters['size_x'] = "480";
		if(!$this->parameters['size_y']) $this->parameters['size_y'] = "270";
		return serialize($paramsToSerialized);
	}
}
?>