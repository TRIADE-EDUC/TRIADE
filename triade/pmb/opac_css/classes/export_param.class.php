<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: export_param.class.php,v 1.6 2022/01/20 15:25:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


define("EXP_DEFAULT_OPAC",1);
define("EXP_DEFAULT_GESTION",2);
define("EXP_GLOBAL_CONTEXT",3);
define("EXP_SESSION_CONTEXT",4);

class export_param {
	
	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------
	public $context=0;
	public $tab_params=array();
	public $export_art_link=0;
	public $export_bulletinage=0;
	public $export_bull_link=0;
	public $export_perio_link=0;
	public $export_notice_perio_link=0;
	public $export_notice_art_link=0;
	public $export_mere=0;
	public $export_fille=0;
	public $export_horizontale=0;
	public $export_notice_mere_link=0;
	public $export_notice_fille_link=0;
	public $export_notice_horizontale_link=0;
	public $generer_liens=0;
	public $export_map=0;
	
	// ---------------------------------------------------------------
	//		Constructeur de la classe
	// ---------------------------------------------------------------
	public function __construct($context=2){		
		$this->context = $context;
		$this->init_var($this->context);
	}
	
	/***
	 * Affiche les paramètres d'export correspondant à la gestion
	 ***/
	public function init_var($context){
		
		global $exportparam_export_art_link, $exportparam_export_bulletinage, $exportparam_export_bull_link, $exportparam_export_perio_link;
		global $exportparam_export_notice_perio_link, $exportparam_export_notice_art_link, $exportparam_export_mere, $exportparam_export_fille, $exportparam_export_horizontale, $exportparam_generer_liens;
		global $exportparam_export_notice_mere_link, $exportparam_export_notice_fille_link, $exportparam_export_notice_horizontale_link;
		global $exportparam_export_map;
		global $opac_exp_export_art_link, $opac_exp_export_bulletinage, $opac_exp_export_bull_link, $opac_exp_export_perio_link, $opac_exp_export_notice_perio_link;
		global $opac_exp_export_notice_art_link, $opac_exp_export_mere, $opac_exp_export_fille, $opac_exp_export_horizontale, $opac_exp_generer_liens, $opac_exp_export_notice_mere_link, $opac_exp_export_notice_fille_link, $opac_exp_export_notice_horizontale_link;
		global $opac_exp_export_map;
		global $genere_lien, $mere, $fille, $horizontale, $art_link, $bull_link, $perio_link, $bulletinage, $notice_art, $notice_perio, $notice_mere, $notice_fille, $notice_horizontale;
		global $map;
		
		if($context==EXP_DEFAULT_GESTION){
			$this->export_art_link=$exportparam_export_art_link;
			$this->export_bull_link=$exportparam_export_bull_link;
			$this->export_perio_link=$exportparam_export_perio_link;
			$this->export_bulletinage=$exportparam_export_bulletinage;
			$this->export_notice_perio_link=$exportparam_export_notice_perio_link;
			$this->export_notice_art_link=$exportparam_export_notice_art_link;
			$this->export_mere=$exportparam_export_mere;
			$this->export_fille=$exportparam_export_fille;
			$this->export_horizontale=$exportparam_export_horizontale;
			$this->generer_liens=$exportparam_generer_liens;
			$this->export_notice_mere_link=$exportparam_export_notice_mere_link;
			$this->export_notice_fille_link=$exportparam_export_notice_fille_link;
			$this->export_notice_horizontale_link=$exportparam_export_notice_horizontale_link;
			$this->export_map=$exportparam_export_map;
		} elseif($context==EXP_DEFAULT_OPAC){
			$this->export_art_link=$opac_exp_export_art_link;
			$this->export_bull_link=$opac_exp_export_bull_link;
			$this->export_perio_link=$opac_exp_export_perio_link;
			$this->export_bulletinage=$opac_exp_export_bulletinage;
			$this->export_notice_perio_link=$opac_exp_export_notice_perio_link;
			$this->export_notice_art_link=$opac_exp_export_notice_art_link;
			$this->export_mere=$opac_exp_export_mere;
			$this->export_fille=$opac_exp_export_fille;
			$this->export_horizontale=$opac_exp_export_horizontale;
			$this->generer_liens=$opac_exp_generer_liens;
			$this->export_notice_mere_link=$opac_exp_export_notice_mere_link;
			$this->export_notice_fille_link=$opac_exp_export_notice_fille_link;
			$this->export_notice_horizontale_link=$opac_exp_export_notice_horizontale_link;
			$this->export_map=$opac_exp_export_map;	
		} elseif($context==EXP_SESSION_CONTEXT){
			$this->export_art_link=$_SESSION["param_export"]["art_link"];
			$this->export_bull_link=$_SESSION["param_export"]["bull_link"];
			$this->export_perio_link=$_SESSION["param_export"]["perio_link"];
			$this->export_bulletinage=$_SESSION["param_export"]["bulletinage"];
			$this->export_notice_perio_link=$_SESSION["param_export"]["notice_perio"];
			$this->export_notice_art_link=$_SESSION["param_export"]["notice_art"];
			$this->export_mere=$_SESSION["param_export"]["mere"];
			$this->export_fille=$_SESSION["param_export"]["fille"];
			$this->export_horizontale=$_SESSION["param_export"]["horizontale"];
			$this->generer_liens=$_SESSION["param_export"]["genere_lien"];
			$this->export_notice_mere_link=$_SESSION["param_export"]["notice_mere"];
			$this->export_notice_fille_link=$_SESSION["param_export"]["notice_fille"];
			$this->export_notice_horizontale_link=$_SESSION["param_export"]["notice_horizontale"];
			$this->export_map=$_SESSION["param_export"]["map"];
		} elseif($context==EXP_GLOBAL_CONTEXT) {
			$this->export_art_link=$art_link;
			$this->export_bull_link=$bull_link;
			$this->export_perio_link=$perio_link;
			$this->export_bulletinage=$bulletinage;
			$this->export_notice_perio_link=$notice_perio;
			$this->export_notice_art_link=$notice_art;
			$this->export_mere=$mere;
			$this->export_fille=$fille;
			$this->export_horizontale=$horizontale;
			$this->generer_liens=$genere_lien;
			$this->export_notice_mere_link=$notice_mere;
			$this->export_notice_fille_link=$notice_fille;
			$this->export_notice_horizontale_link=$notice_horizontale;
			$this->export_map=$map;	
		}		
	}
	
	/***
	 * Initialisation des paramètres d'export de la session
	 ***/
	public static function init_session(){
	    global $genere_lien, $mere, $fille, $horizontale, $art_link, $bull_link, $perio_link, $bulletinage, $notice_art, $notice_perio, $notice_mere, $notice_fille, $notice_horizontale, $map;
		
		$_SESSION["param_export"]=array();
		$_SESSION["param_export"]["genere_lien"]=$genere_lien;
		$_SESSION["param_export"]["mere"]=$mere;
		$_SESSION["param_export"]["fille"]=$fille;
		$_SESSION["param_export"]["horizontale"]=$horizontale;
		$_SESSION["param_export"]["notice_mere"]=$notice_mere;
		$_SESSION["param_export"]["notice_fille"]=$notice_fille;
		$_SESSION["param_export"]["notice_horizontale"]=$notice_horizontale;
		$_SESSION["param_export"]["bull_link"]=$bull_link;
		$_SESSION["param_export"]["art_link"]=$art_link;
		$_SESSION["param_export"]["perio_link"]=$perio_link;
		$_SESSION["param_export"]["bulletinage"]=$bulletinage;
		$_SESSION["param_export"]["notice_perio"]=$notice_perio;
		$_SESSION["param_export"]["notice_art"]=$notice_art;
		$_SESSION["param_export"]["map"]=$map;
			
	}
	
	/***
	 * Affiche les paramètres d'export correspondant à la gestion
	 ***/
	
	public function check_default_param(){
		
		global $form_param;

		if($this->generer_liens){
			$form_param = str_replace('!!checked_0!!','checked',$form_param);
			$form_param = str_replace('!!display_list!!','',$form_param);
		}
		else {
			$form_param = str_replace('!!checked_0!!','',$form_param);
			$form_param = str_replace('!!display_list!!','display:none',$form_param);
		}
		
		if($this->export_mere){
			$form_param = str_replace('!!checked_1!!','checked',$form_param);
			$form_param = str_replace('!!disabled_3!!','',$form_param);
		} else {
			$form_param = str_replace('!!checked_1!!','',$form_param);
			$form_param = str_replace('!!disabled_3!!','disabled',$form_param);
		}
		
		if($this->export_fille){
			$form_param = str_replace('!!checked_2!!','checked',$form_param);
			$form_param = str_replace('!!disabled_4!!','',$form_param);
		} else {
			$form_param = str_replace('!!checked_2!!','',$form_param);
			$form_param = str_replace('!!disabled_4!!','disabled',$form_param);
		}
		
		if($this->export_horizontale){
			$form_param = str_replace('!!checked_11!!','checked',$form_param);
			$form_param = str_replace('!!disabled_5!!','',$form_param);
		} else {
			$form_param = str_replace('!!checked_11!!','',$form_param);
			$form_param = str_replace('!!disabled_5!!','disabled',$form_param);
		}
		
		if($this->export_bull_link){
			$form_param = str_replace('!!checked_3!!','checked',$form_param);
		} else {
			$form_param = str_replace('!!checked_3!!','',$form_param);
		}
		
		if($this->export_perio_link){
			$form_param = str_replace('!!checked_4!!','checked',$form_param);
			$form_param = str_replace('!!disabled_1!!','',$form_param);
		} else {
			$form_param = str_replace('!!checked_4!!','',$form_param);
			$form_param = str_replace('!!disabled_1!!','disabled',$form_param);
		}
		
		if($this->export_art_link){
			$form_param = str_replace('!!checked_5!!','checked',$form_param);
			$form_param = str_replace('!!disabled_2!!','',$form_param);
		} else {
			$form_param = str_replace('!!checked_5!!','',$form_param);
			$form_param = str_replace('!!disabled_2!!','disabled',$form_param);
		}
		
		if($this->export_bulletinage)
			$form_param = str_replace('!!checked_6!!','checked',$form_param);
		else 
			$form_param = str_replace('!!checked_6!!','',$form_param);		
		
		if($this->export_notice_perio_link)
			$form_param = str_replace('!!checked_7!!','checked',$form_param);
		else 
			$form_param = str_replace('!!checked_7!!','',$form_param);	
		
		if($this->export_notice_art_link)
			$form_param = str_replace('!!checked_8!!','checked',$form_param);
		else
			$form_param = str_replace('!!checked_8!!','',$form_param);
			
		if($this->export_notice_mere_link)
			$form_param = str_replace('!!checked_9!!','checked',$form_param);
	    else 
			$form_param = str_replace('!!checked_9!!','',$form_param);
			
		if($this->export_notice_fille_link)
			$form_param = str_replace('!!checked_10!!','checked',$form_param);
		 else 
			$form_param = str_replace('!!checked_10!!','',$form_param);

		if($this->export_notice_horizontale_link)
			$form_param = str_replace('!!checked_12!!','checked',$form_param);
		 else 
			$form_param = str_replace('!!checked_12!!','',$form_param);
			
		if($this->export_map)
		    $form_param = str_replace('!!checked_13!!','checked',$form_param);
		else
		    $form_param = str_replace('!!checked_13!!','',$form_param);
			        
		return $form_param;
	}
	
	/***
 	 * Mise à jour des paramètres dans la base
 	 ***/	
	public function update(){
		if(!$this->tab_params)
			return;
		
		//construction de la requete			
		foreach($this->tab_params as $cle=>$valeur){
			$requete="UPDATE parametres SET ";		
			$affectation='';
			$affectation .= " valeur_param='".$valeur."' WHERE sstype_param='$cle'";		
			$requete .= $affectation;
			pmb_mysql_query($requete);
		}	
		return;
	}
	
	/***
 	 * Récupération des paramètres dans un tableau selon le contexte
 	 ***/
	public function get_parametres($context){
		$parametres=array();
		if($context == EXP_DEFAULT_GESTION){
			$parametres["generer_liens"] = (int) $this->generer_liens;
			$parametres["export_mere"] = (int) $this->export_mere;
			$parametres["export_fille"] = (int) $this->export_fille;
			$parametres["export_horizontale"] = (int) $this->export_horizontale;
			$parametres["export_notice_art_link"] = (int) $this->export_notice_art_link;
			$parametres["export_notice_perio_link"] = (int) $this->export_notice_perio_link;
			$parametres["export_bulletinage"] = (int) $this->export_bulletinage;
			$parametres["export_bull_link"] = (int) $this->export_bull_link;
			$parametres["export_perio_link"] = (int) $this->export_perio_link;
			$parametres["export_art_link"] = (int) $this->export_art_link;
			$parametres["export_notice_mere_link"] = (int) $this->export_notice_mere_link;
			$parametres["export_notice_fille_link"] = (int) $this->export_notice_fille_link;
			$parametres["export_notice_horizontale_link"] = (int) $this->export_notice_horizontale_link;
			$parametres["export_map"] = (int) $this->export_map;			
		} elseif ($context == EXP_DEFAULT_OPAC){
			$parametres["exp_generer_liens"] = (int) $this->generer_liens;
			$parametres["exp_export_mere"] = (int) $this->export_mere;
			$parametres["exp_export_fille"] = (int) $this->export_fille;
			$parametres["exp_export_horizontale"] = (int) $this->export_horizontale;
			$parametres["exp_export_notice_art_link"] = (int) $this->export_notice_art_link;
			$parametres["exp_export_notice_perio_link"] = (int) $this->export_notice_perio_link;
			$parametres["exp_export_bulletinage"] = (int) $this->export_bulletinage;
			$parametres["exp_export_bull_link"] = (int) $this->export_bull_link;
			$parametres["exp_export_perio_link"] = (int) $this->export_perio_link;
			$parametres["exp_export_art_link"] = (int) $this->export_art_link;
			$parametres["exp_export_notice_mere_link"] = (int) $this->export_notice_mere_link;
			$parametres["exp_export_notice_fille_link"] = (int) $this->export_notice_fille_link;
			$parametres["exp_export_notice_horizontale_link"] = (int) $this->export_notice_horizontale_link;
			$parametres["exp_export_map"] = (int) $this->export_map;
			
		} elseif ($context == EXP_GLOBAL_CONTEXT || $context == EXP_SESSION_CONTEXT){
			$parametres["genere_lien"] = (int) $this->generer_liens;
			$parametres["mere"] = (int) $this->export_mere;
			$parametres["fille"] = (int) $this->export_fille;
			$parametres["horizontale"] = (int) $this->export_horizontale;
			$parametres["notice_art"] = (int) $this->export_notice_art_link;
			$parametres["notice_perio"] = (int) $this->export_notice_perio_link;
			$parametres["bulletinage"] = (int) $this->export_bulletinage;
			$parametres["bull_link"] = (int) $this->export_bull_link;
			$parametres["perio_link"] = (int) $this->export_perio_link;
			$parametres["art_link"] = (int) $this->export_art_link;
			$parametres["notice_mere"] = (int) $this->export_notice_mere_link;
			$parametres["notice_fille"] = (int) $this->export_notice_fille_link;
			$parametres["notice_horizontale"] = (int) $this->export_notice_horizontale_link;
			$parametres["map"] = (int) $this->export_map;			
		}
		
		if($parametres)
			$this->tab_params = $parametres;
		return $parametres;
	}
	
}
?>