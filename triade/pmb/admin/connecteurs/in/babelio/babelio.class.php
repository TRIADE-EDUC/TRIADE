<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: babelio.class.php,v 1.10.2.1.2.1 2025/04/16 12:16:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/connecteurs.class.php");
require_once($class_path."/curl.class.php");

class babelio extends connector {
	//Variables internes pour la progression de la récupération des notices
	public $del_old;				//Supression ou non des notices dejà existantes

	public $profile;				//Profil Amazon
	public $match;					//Tableau des critères UNIMARC / AMAZON
	public $current_site;			//Site courant du profile (n°)
	public $searchindexes;			//Liste des indexes de recherche possibles pour le site
	public $current_searchindex;	//Numéro de l'index de recherche de la classe
	public $match_index;			//Type de recherche (power ou simple)
	public $types;					//Types de documents pour la conversino des notices

	//Résultat de la synchro
	public $error;					//Y-a-t-il eu une erreur
	public $error_message;			//Si oui, message correspondant

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
    	return "babelio";
    }

    //Formulaire des propriétés générales
	public function get_property_form() {
		global $charset;
		$this->fetch_global_properties();
		//Affichage du formulaire en fonction de $this->parameters
		if ($this->parameters) {
			$keys = unserialize($this->parameters);
			$login= $keys['login'];
			$mdp=$keys['mdp'];
		} else {
			$login="";
			$mdp="";
		}
		$r="<div class='row'>
				<div class='colonne3'><label for='login'>".$this->msg["babelio_login"]."</label></div>
				<div class='colonne-suite'><input type='text' name='login' value='".htmlentities($login,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='mdp'>".$this->msg["babelio_mdp"]."</label></div>
				<div class='colonne-suite'><input type='text' class='saisie-50em' name='mdp' value='".htmlentities($mdp,ENT_QUOTES,$charset)."'/></div>
			</div>";
		return $r;
	}

    public function make_serialized_properties() {
    	global $login, $mdp;
		//Mise en forme des paramètres à partir de variables globales (mettre le résultat dans $this->parameters)
		$keys = array();

    	$keys['login']=$login;
		$keys['mdp']=$mdp;
		$this->parameters = serialize($keys);
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::enrichment_is_allow()
	 */
	public function enrichment_is_allow()
	{
	    return connector::ENRICHMENT_YES;
	}

	public function getEnrichmentHeader(){
		$header= array();
		$header[]= "<!-- Script d'enrichissement Babélio-->";
		$header[]= "<script type='text/javascript'>
		function switchPage(notice_id,type,page,action){
			var pagin= new http_request();
			var content = document.getElementById('div_'+type+notice_id);
			var patience= document.createElement('img');
			patience.setAttribute('src','".get_url_icon('patience.gif')."');
			patience.setAttribute('align','middle');
			patience.setAttribute('id','patience'+notice_id);
			content.innerHTML = '';
			document.getElementById('onglet_'+type+notice_id).appendChild(patience);
			page = page*1;
			switch (action){
				case 'next' :
					page++;
					break;
				case 'previous' :
					page--;
					break;
			}
			pagin.request('./ajax.php?module=ajax&categ=enrichment&action=enrichment&type='+type+'&id='+notice_id+'&enrichPage='+page,false,'',true,gotEnrichment);
		}
		</script>";
		return $header;
	}

	public function getTypeOfEnrichment($source_id){
	    $type = array();
		$type['type'] = array(
			"citation",
			"critique"
		);
		$type['source_id'] = $source_id;
		return $type;
	}

	public function getEnrichment($notice_id,$source_id,$type="",$enrich_params=array(),$page=1){
		$enrichment= array();
		$this->noticeToEnrich = $notice_id;
		$this->enrichPage = $page;
		//on renvoi ce qui est demandé... si on demande rien, on renvoi tout..
		$rqt="select code from notices where notice_id = '$notice_id'";
		$res=pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			$code = pmb_mysql_result($res,0,0);
			$code = preg_replace('/-|\.| /', '', $code);
		}
		$this->typeOfEnrichment = $type;
		switch ($this->typeOfEnrichment){
			case "citation" :
				$enrichment['citation']['content'] = $this->getInfos(2,$code);
				break;
			case "critique" :
				$enrichment['critique']['content'] = $this->getInfos(1,$code);
				break;
			default :
				$enrichment['citation']['content'] = $this->getInfos(2,$code);
				$enrichment['critique']['content'] = $this->getInfos(1,$code);
				break;
		}
		$enrichment['source_label']=$this->msg['babelio_enrichment_source'];
		return $enrichment;
	}

	public function getInfos($type,$isbn){
		if(!$isbn) return "";
		$return = "";
		$t = time();
		$url = "http://www.babelio.info/sxml/$isbn&type=$type&page=".$this->enrichPage."&auth=".$this->getHash($t)."&timestamp=$t";
		$curl = new Curl();
		$xmlToParse = $curl->get($url);
		$xmlToParse = encoding_normalize::utf8_decode($xmlToParse);
		$xmlToParse=$this->cp1252Toiso88591($xmlToParse);
		$xmlToParse = str_replace("utf-8","iso-8859-1",$xmlToParse);
		$xml = _parser_text_no_function_($xmlToParse,"URLSET");
		$return = $this->formatEnrichmentResult($xml);
		$return.= $this->getEnrichmentPagin($xml['SOMMAIRE'][0]);
		return $return;
	}

	public function getHash($t){
		$keys = unserialize($this->parameters);
		return md5($keys['login'].md5($keys['mdp'])."PMB".$t);
	}

	public function getEnrichmentPagin($sommaire){
		$current = $sommaire['PAGE'][0]['value'];
		if (!empty($sommaire['RESULTATS_PAR_PAGE'][0]['value'])) {
		    $nb_page = ceil($sommaire['NB_RESULTATS'][0]['value']/$sommaire['RESULTATS_PAR_PAGE'][0]['value']);
		} else {
		    $nb_page = 0;
		}
		$ret = "";
		if($current > 1) {
		    $ret .= "<img src='".get_url_icon('prev.png')."' onclick='switchPage(\"".$this->noticeToEnrich."\",\"".$this->typeOfEnrichment."\",\"".$current."\",\"previous\");'/>";
		} else {
		    $ret .= "<img src='".get_url_icon('prev-grey.png')."'/>";
		}
		$ret .="&nbsp;".$current."/$nb_page&nbsp;";
		if($current < $nb_page) {
		    $ret .= "<img src='".get_url_icon('next.png')."' onclick='switchPage(\"".$this->noticeToEnrich."\",\"".$this->typeOfEnrichment."\",\"".$current."\",\"next\");'/>";
		} else {
		    $ret .= "<img src='".get_url_icon('next-grey.png')."'/>";
		}
		return "<div class='row'><span style='text-align:center'>".$ret."</span></div>";
	}

	public function formatEnrichmentResult($xml){
		$result = "";
		foreach($xml['URL'] as $url){
			$d = explode("T",$url['DT'][0]['value']);
			$date = formatdate($d[0]);
			$result.="
			<div class='row'>
				<div class='row'> ".
				$this->msg['babelio_enrichment_publish_date']." ".$date;
			if($this->typeOfEnrichment == 'critique') $result.= "&nbsp;".$this->stars(isset($url['NOTE'][0]['value']) ? $url['NOTE'][0]['value'] : 0) ;
			$result.="
				</div>
				<blockquote role='presentation'>";
			foreach($url['SNIPPET'] as $content){
				$result.= $content['value'];
			}
			$result.="
					<br />
					<a href='".$url['LOC'][0]['value']."' target='_blank'>".$this->msg['babelio_enrichment_see_more']."</a>
				</blockquote>
			</div>";
		}
		return $result;
	}

	// Gestion des étoiles pour les notes
	public function stars($note) {
		$etoiles_moyenne="";
		$cpt_star = 5;

		for ($i = 1; $i <= $cpt_star; $i++) {
			if($note >= $i) $etoiles_moyenne.="<img border=0 src='".get_url_icon('star.png')."' align='absmiddle'>";
			else $etoiles_moyenne.="<img border=0 src='".get_url_icon('star_unlight.png')."' align='absmiddle'>";
		}
		return $etoiles_moyenne;
	} // fin stars()

	public function cp1252Toiso88591($str){
		$cp1252_map = array(
			"\x80" => "EUR", /* EURO SIGN */
			"\x82" => "\xab", /* SINGLE LOW-9 QUOTATION MARK */
			"\x83" => "\x66",     /* LATIN SMALL LETTER F WITH HOOK */
			"\x84" => "\xab", /* DOUBLE LOW-9 QUOTATION MARK */
			"\x85" => "...", /* HORIZONTAL ELLIPSIS */
			"\x86" => "?", /* DAGGER */
			"\x87" => "?", /* DOUBLE DAGGER */
			"\x88" => "?",     /* MODIFIER LETTER CIRCUMFLEX ACCENT */
			"\x89" => "?", /* PER MILLE SIGN */
			"\x8a" => "S",   /* LATIN CAPITAL LETTER S WITH CARON */
			"\x8b" => "\x3c", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
			"\x8c" => "OE",   /* LATIN CAPITAL LIGATURE OE */
			"\x8e" => "Z",   /* LATIN CAPITAL LETTER Z WITH CARON */
			"\x91" => "\x27", /* LEFT SINGLE QUOTATION MARK */
			"\x92" => "\x27", /* RIGHT SINGLE QUOTATION MARK */
			"\x93" => "\x22", /* LEFT DOUBLE QUOTATION MARK */
			"\x94" => "\x22", /* RIGHT DOUBLE QUOTATION MARK */
			"\x95" => "\b7", /* BULLET */
			"\x96" => "\x20", /* EN DASH */
			"\x97" => "\x20\x20", /* EM DASH */
			"\x98" => "\x7e",   /* SMALL TILDE */
			"\x99" => "?", /* TRADE MARK SIGN */
			"\x9a" => "S",   /* LATIN SMALL LETTER S WITH CARON */
			"\x9b" => "\x3e;", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
			"\x9c" => "oe",   /* LATIN SMALL LIGATURE OE */
			"\x9e" => "Z",   /* LATIN SMALL LETTER Z WITH CARON */
			"\x9f" => "Y"    /* LATIN CAPITAL LETTER Y WITH DIAERESIS*/
		);
		return strtr($str, $cp1252_map);
	}
}
?>