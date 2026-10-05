<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_piwik_view_piwik.class.php,v 1.4 2023/08/17 09:47:56 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_piwik_view_piwik extends cms_module_common_view{


	public function get_form(){
		$form = parent::get_form();
		$form.="
		<div class='row'>
			<label>".$this->format_text($this->msg['cms_module_piwik_view_piwik_visualization'])."</label>
		</div>
		<div class='row'>
				<pre>ninja</pre>
			<textarea disabled='yes'>ninja</textarea>
		</div>";
		return $form;
	}


	public function get_headers($datas=array()){

		$headers = parent::get_headers($datas);
		$headers[]= "<script>".$this->generate_piwik_script($datas)."</script>";
		return $headers;
	}


	public function render($datas){
		return "";
	}


	private function generate_piwik_script($server){

		$script = "var _paq = _paq || [];";
		if(isset($server['search'])){
			$script.= "
_paq.push(['trackSiteSearch','".addslashes($server['search']['user_query'])."','".addslashes($server['search']['type'])."', '".addslashes($server['search']['count'])."']);";
		}
		if(isset($server['visitor'])){
			$i=0;
			foreach($server['visitor'] as $key => $value){
				$i++;
				$script.="
_paq.push(['setCustomVariable', ".$i.", '".$key."', '".addslashes($value)."', 'visit']);";
			}
		}
		if(isset($server['page'])){
			$i=0;
			foreach($server['page'] as $key => $value){
				$i++;
				$script.="
_paq.push(['setCustomVariable', ".$i.", '".$key."', '".addslashes($value)."', 'page']);";
			}
		}
		$script.="
_paq.push(['trackPageView']);
_paq.push(['enableLinkTracking']);
(function() {
	var u='".str_replace(array("http:","https:"),"",$server['url'])."';
	_paq.push(['setTrackerUrl', u+'piwik.php']);
	_paq.push(['setSiteId', ".$server['site_id']."]);
	var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
	g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
})();";
		return $script;
	}
}
