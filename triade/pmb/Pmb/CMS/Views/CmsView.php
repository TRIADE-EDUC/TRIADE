<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CmsView.php,v 1.3 2022/09/23 13:04:48 qvarin Exp $

namespace Pmb\CMS\Views;

use Pmb\Common\Views\VueJsView;

class CmsView extends VueJsView
{
    public function render()
    {
        global $javascript_path;
        
        /*
         * Fichier externe pour Dojo
         */
        $content = "<script type='text/javascript' src='$javascript_path/portal/cms_build.js'></script>";
        $content.= "<script type='text/javascript'>var \$cmsData = ".\encoding_normalize::json_encode($this->getCmsData()).";</script>";
        $content .= parent::render();
        return $content;
    }
    
    protected function getCmsData() 
    {
    	global $msg, $pmb_url_base, $cms_url_base_cms_build;
    	
    	return [
    		"url_base" => $pmb_url_base . "rest.php/cms/",
    		"url_base_opac" => $cms_url_base_cms_build,
    		"clean_cache" => [
    			"name" => $msg['cms_clean_cache'],
    			"confirm" => $msg['cms_clean_cache_confirm'],
    			"title" => \cms_cache::get_cache_formatted_last_date()
    		],
    		"clean_cache_img" => [
    			"name" => $msg['cms_clean_cache_img'],
    			"confirm" => $msg['cms_clean_cache_confirm_img']
    		],
    		'img' => [
    			'tick' => get_url_icon('tick.gif'),
    			'error' => get_url_icon('error.png')
    		]
    	];
    }
}

