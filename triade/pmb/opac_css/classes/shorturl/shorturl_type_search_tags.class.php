<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturl_type_search_tags.class.php,v 1.2 2023/08/17 09:47:56 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/shorturl/shorturl_type.class.php");

class shorturl_type_search_tags extends shorturl_type{
	
	protected function rss() {
	}
	
	protected function permalink(){
		global $base_path;

		$context = unserialize($this->context);	
		$suite = '';
		if(isset($context['opac_view'])){
			$suite .= '&opac_view='.$context['opac_view'];
		}
		$html = '
			<html><head></head><body><img src="'.get_url_icon('patience.gif').'"/>';

		//on ajoute un flag "from_permalink" pour forcer l'enregistrement en session de la recherche dans navigator.inc.php, afin de pouvoir appliquer des facettes
		$html.= '
			<script>
				document.location = "'.$base_path.'/index.php?lvl=more_results&mode=keyword&user_query='.rawurlencode($context['user_query']).'&tags='.$context['tags'].'&from_permalink=1'.$suite.'";
			</script>
			<body></html>';
		print $html;
	}

	public function generate_hash($action,$context=array()) {
		global $charset, $user_query, $tags;
		$hash = '';

		$context =array();
		$context["user_query"] = rawurldecode($user_query);
		$context["tags"] = $tags;
		//on essaye de conserver la vue!
		if(isset($_SESSION['opac_view']) && $_SESSION['opac_view']){
			$context['opac_view'] = $_SESSION['opac_view'];
		}
		if(method_exists($this, $action)){
			$hash = self::create_hash('search_tags',$action,$context);
		}
		return $hash;
	}
}