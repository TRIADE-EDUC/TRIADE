<?php
/* Génération automatique de la structure de la bases de données
* Le script a besoin de deux fichiers  :
* Fichier scheme.xml : contient les données exploitées. Il est obtenu en appliquant req_schema.xsl a pmb34_db445_20130711.xml ce dernier provient d'une conversion de de pmb34_db445_20130711.dez qui est le fichier généré par DeZign en xml.
* Fichier scheme.gif : export image du schéma de base de données (File->Export->Export diagram to image)
*/

error_reporting(E_ALL & ~E_NOTICE);

class db_doc {
	
	private $t_table;
	private $t_parcours;
	private $t_att;
	private $t_relation;
	private $titre;
	public $ID;

	public function __construct() {

		$this->t_table=array();
		$this->t_parcours=array();
		$this->t_att=array();
		$this->t_relation=array();
		$this->titre="<h3>Base de donn&eacute;es PMB</h3>";
		$this->ID=-1;
	}

	
	public function get_table(){
		return $this->t_table;
	}
	
	public function get_parcours(){
		return $this->t_parcours;
	}
		
	public function get_attribut(){
		return $this->t_att;
	}
	
	public function get_relation(){
		return $this->t_relation;
	}
	
	public function getTitre(){
		return $this->titre;
	}
	
	
	public function parsage(){
		$f="./scheme.xml";
		$content=file_get_contents($f);
		$rx = "/<?xml.*encoding=[\'\"](.*?)[\'\"].*?>/m";

		
		if (preg_match($rx, $content, $m)) $encoding = strtoupper($m[1]);
		else $encoding = "ISO-8859-1";	//$encoding = "UTF-8";
		
		$parser = xml_parser_create($encoding);
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, $encoding);		
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, "debutBalise", "finBalise");
		xml_set_character_data_handler($parser, "texte");
		xml_parse( $parser, $content, TRUE );
		asort($this->t_parcours);
	}
	
	public function debutBalise($parser, $tag, $att=array()) {
		

		//complète $t_table avec son nom, sa description et la table de toutes ses relations
		if ($tag=='TABLE' && count($att) && $att['NAME'] ) {
			$this->ID=$att['ID'];
			$this->t_table[$att['ID']]=array('NAME'=>$att['NAME'],'DESC'=>$att['DESC'],'LIENS'=>array(),'KEY'=>$att['PKID']);
			$this->t_parcours[$att['ID']]=$att['NAME'];
		}
		//complète $t_relation qui lie les indices de deux tables et décrit ce lien.
		if ($tag=='LIEN'&& count($att) && $att['NAME']) {
			$t_lienF=explode('-',$att['CHILD']);
			$t_lienP=explode('-',$att['PARENT']);
			$this->t_table[$t_lienF[0]]['LIENS'][]=$att['ID'];
			$this->t_table[$t_lienP[0]]['LIENS'][]=$att['ID'];	
			$this->t_relation[$att['ID']]=array('DESC'=>$att['DESC'],'T_PERE'=>$t_lienP[0],'F_PERE'=>$att['PARENT'],'T_FILS'=>$t_lienF[0],'F_FILS'=>$att['CHILD']);	
			$this->t_table[$t_lienF[0]]['ATTRS'][$att['CHILD']]['REF']=$t_lienP[0];
			if (empty($this->t_table[$t_lienF[0]]['ATTRS'][$att['CHILD']]['KEY'])){
			$this->t_table[$t_lienF[0]]['ATTRS'][$att['CHILD']]['KEY']="Cl&eacute; &eacute;trang&egrave;re";}
		}
		//complète 'Lien' de $t_table afin de regrouper le informations à afficher
		if ($tag=='FIELD'&& count($att) && $this->ID!=-1) {
			$cle="Sign&eacute";
			if($att['UNSIGNED']=="1"){$cle="Non sign&eacute;";}
			$tmp=explode('-',$att['ID']);
			$pk="";
			$rep=explode(',',$this->t_table[$this->ID]['KEY']);
			if(in_array($tmp[1],$rep)){$pk="Cl&eacute; primaire";}
			$this->t_table[$this->ID]['ATTRS'][$att['ID']]=array('NAME'=>$att['NAME'],'DESC'=>$att['DESC'],'TYPE'=>$att['TYPE']."(".$att['LENGTH'].")",'SIGNE'=>$cle,'DEFVAL'=>$att['DEFVAL'],'KEY'=>$pk);
		}
		//Construit le titre
		if ($tag=='TITRE'&& count($att) ) {
			$att['DATE']=date("d/m/Y");
			$this->titre="<h3>".$att['NAME']."</h3>"."<h5>".$att['DATE']."</h5>";
		}
		
	}
	
	public function finBalise($parser, $tag, $att=array()) {
			if ($tag=='TABLE' && count($att) && $att['NAME'] ) {
				$this->ID=-1;
			}
	}
	
	public function texte($parser, $data) {
		
	}

}







