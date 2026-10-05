<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lastfm_api.class.php,v 1.3 2019/07/10 06:44:08 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("lastfmapi/lastfmapi.php");

//classe facilitant les appel depuis le connecteurs...
class lastfm_api{
	public $auth;			// objet gérant l'authentification
	public $api_class;		// objet gérant les appels
	public $notice_infos;	// tableau regroupant les infos utiles d'une notice
	
	/*
	 * Constructeur
	 */
	public function __construct($authVars){
		$this->auth = new lastfmApiAuth('getsession', $authVars);
		$this->api_class = new lastfmApi();
	}
	
	public function set_notice_infos($notice_infos){
		$this->notice_infos = $notice_infos;
	} 

	/*
	 * Récupère les infos d'un artiste
	 */
	public function get_artist_infos(){
		global $lang;
		
		$vars = array(
			'lang' => substr($lang,0,2),
			'artist' => $this->notice_infos['author']
		);
		
		$package = $this->api_class->getPackage($this->auth, "artist");
		return $package->getInfo($vars);		
	}
	
	/*
	 * Récupère la biopgraphie d'un artiste
	 */
	public function get_artist_biography(){
		$infos = $this->get_artist_infos();
		return $infos['bio'];
	}

	/*
	 * Récupère une liste des artistes similaires d'un artiste
	 */
	public function get_similar_artists(){
		$infos = $this->get_artist_infos();
	//	highlight_string(print_r($infos,true));
		return $infos['similar'];
	}
	
	public function get_album_infos(){
		global $lang;
		
		$vars = array(
			'lang' => substr($lang,0,2),
			'album' => $this->notice_infos['title'],
			'artist' => $this->notice_infos['author']
		);
		$package = $this->api_class->getPackage($this->auth, "album");
		return $package->getInfo($vars);				
	}
	
	public function get_pictures($page=1){
		$vars = array(
			'artist' => $this->notice_infos['author'],
			'page' => $page,
			'limit' => 20
		);
		$package = $this->api_class->getPackage($this->auth, "artist");
		return $package->getImages($vars);
	}	
}