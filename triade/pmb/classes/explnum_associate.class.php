<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_associate.class.php,v 1.6 2023/10/24 09:57:08 gneveu Exp $


if (stristr ($_SERVER['REQUEST_URI'], ".class.php"))
	die ("no access");

class explnum_associate {
	/**
	 * @var explnum
	 */
	private $explnum;
	
	public function __construct($explnum) {
		$this->explnum = $explnum;
	}
	
	public function getPlayer(&$tpl) {
		if (in_array($this->explnum->explnum_mimetype, array("audio/mpeg", "audio/mp3"))) {
			$player = $this->getPlayerAudio();
		} else {
			$player = $this->getPlayerVideo();
		}
		$tpl = str_replace("!!player!!", $player, $tpl);
	}
	
	private function getPlayerAudio() {
		$visionneuse_path = "./opac_css/visionneuse";
		$player = "
		<script type='text/javascript'>
			var css = document.createElement('link');
			css.href = '$visionneuse_path/classes/mimetypes/videojs/videoJS/video-js.css';
			css.rel = 'stylesheet';
			document.getElementsByTagName('head')[0].appendChild(css);
		
			var script = document.createElement('script');
			script.src = '$visionneuse_path/classes/mimetypes/videojs/videoJS/video.js';
			document.getElementsByTagName('head')[0].appendChild(script);
			
			var script = document.createElement('script');
			script.innerHTML = 'videojs.options.flash.swf = \'$visionneuse_path/classes/mimetypes/videojs/videoJS/video-js.swf\';';
			document.getElementsByTagName('head')[0].appendChild(script);
		</script>
		<audio id='videojs' height='90' width='350' class='video-js vjs-default-skin vjs-big-play-centered' controls preload data-setup='{\"techOrder\": [\"html5\", \"flash\"]}'>
			<source src='./doc_num_data.php?explnum_id=".$this->explnum->explnum_id."' type='".$this->explnum->explnum_mimetype."'/>
		</audio>
		";
		return $player;
	}
	
	private function getPlayerVideo() {
		$visionneuse_path = "./opac_css/visionneuse";
		
		$player = "
		<script type='text/javascript'>
			var css = document.createElement('link');
			css.href = '$visionneuse_path/classes/mimetypes/videojs/videoJS/video-js.css';
			css.rel = 'stylesheet';
			document.getElementsByTagName('head')[0].appendChild(css);
		
			var script = document.createElement('script');
			script.src = '$visionneuse_path/classes/mimetypes/videojs/videoJS/video.js';
			document.getElementsByTagName('head')[0].appendChild(script);
			
			var script = document.createElement('script');
			script.innerHTML = 'videojs.options.flash.swf = \'$visionneuse_path/classes/mimetypes/videojs/videoJS/video-js.swf\';';
			document.getElementsByTagName('head')[0].appendChild(script);
		</script>
		<video id='videojs' height='270' width='480' class='video-js vjs-default-skin vjs-big-play-centered' controls preload data-setup='{\"techOrder\": [\"html5\", \"flash\"]}'>
			<source src='./doc_num_data.php?explnum_id=".$this->explnum->explnum_id."' type='".$this->explnum->explnum_mimetype."'/>
		</video>
		";
		return $player;
	}
	
	public function getAjaxCall(&$tpl) {
		global $base_path;
		$ajaxCall = "
		<script>
			function get_explnum_associate_svg(response) {
				document.getElementById('speech_timeline').innerHTML = response;
				var req = new http_request();		
				req.request('$base_path/ajax.php?module=catalog&categ=explnum&quoifaire=get_associate_js&explnum_id=".$this->explnum->explnum_id."',0,'',1,get_explnum_associate_js,'');
			}
			
			function get_explnum_associate_js(response) {
			
				var script = document.createElement('script');
				script.innerHTML = response;
				
				document.getElementById('speech_timeline_js').appendChild(script);
			}
		
			function get_explnum_associate_ajax() {
				if (document.getElementById('explnum_associate_add_speaker')) {
					document.getElementById('explnum_associate_add_speaker').removeEventListener('click', add_speaker, false);
				}
				if (document.getElementById('speech_timeline')) {
					document.getElementById('speech_timeline').removeEventListener('contextmenu', display_edit_menu, true);
				}
			
				var req = new http_request();		
				req.request('$base_path/ajax.php?module=catalog&categ=explnum&quoifaire=get_associate_svg&explnum_id=".$this->explnum->explnum_id."',0,'',1,get_explnum_associate_svg,'');
			}
			
			get_explnum_associate_ajax();
		</script>
		";
		$tpl = str_replace("!!ajaxCall!!", $ajaxCall, $tpl);
	}
	
	public function getReturnLink(&$tpl) {
		global $base_path;
		
		$returnLink = "";
		if ($this->explnum->explnum_bulletin) { // Cas d'un bulletin
			$returnLink = $base_path."/catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=" . intval($this->explnum->explnum_bulletin);
		} else {
			$query = "select notice_id, niveau_biblio from notices where notice_id = " . intval($this->explnum->explnum_notice);
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				if ($notice = pmb_mysql_fetch_object($result)) {
					if ($notice->niveau_biblio == 's') { // Cas d'une série
					    $returnLink = $base_path."/catalog.php?categ=serials&sub=view&serial_id=" . intval($notice->notice_id);
					} else if ($notice->niveau_biblio == 'm') { // Cas d'une monographie
					    $returnLink = $base_path."/catalog.php?categ=isbd&id=" . intval($notice->notice_id);
					} else if ($notice->niveau_biblio == 'a') { // Cas d'un article
					    $query = "select analysis_bulletin from analysis where analysis_notice = " . intval($notice->notice_id);
						$result = pmb_mysql_query($query);
						if ($result && pmb_mysql_num_rows($result)) {
							if ($analysis = pmb_mysql_fetch_object($result)) {
							    $returnLink = $base_path."/catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=" . intval($analysis->analysis_bulletin) . "&art_to_show=" . intval($notice->notice_id) . "#anchor_" . intval($notice->notice_id);
							}
						}
					}
				}
			}
		}
		$tpl = str_replace("!!return_link!!", $returnLink, $tpl);
	}
}
?>