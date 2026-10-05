<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart.class.php,v 1.14 2020/11/04 10:40:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

if ( ! defined( 'CART_CLASS' ) ) {
  define( 'CART_CLASS', 1 );

// fonction callback de filtrage des valeurs nulles

function array_clean($var) {
	return ($var != 0);
}

class cart {
	// propriétés
	public $path = './'				;	// répertoire de stockage des paniers
	public $file = ''					;	// nom du fichier XML
	public $name = ''					;	// nom de référence
	public $description = ''			;	// description du contenu du panier
	public $entry						;	// tableau accueillant les items du panier	
	public $parser						;	// réf. sur le parser
	public $nb_items = 0				;	// nombre d'enregistrements dans le panier
	public $dtd_path = 'cart.dtd'		;	// path et nom de la DTD

	// méthodes

	// le constructeur

	public function __construct($xml_file='', $path='') {
		// le fichier a un nom, on récupère les données dedans
		$this->path = $path;
		$this->clean_path();
		$this->entry = array();
		if($xml_file) {
			$this->file = $this->path.$xml_file;
			$this->get_cart();
			$this->nb_items = count($this->entry);
		} else {
			$this->create_cart();
		}
		return;
	}


	// mise à jour de la description

	public function set_description($desc='') {
		$this->description = $desc;
	}

	// mise en forme du path
	
	public function clean_path() {
		if(!$this->path) {
			$this->path = './';
		} else {
			$this->path = preg_replace('/\/$|\/\s$|\s$/', '', $this->path);
			$this->path .= '/';
		}
	}


	// liste des paniers disponibles

	public static function get_cart_list($path='') {

		// nettoyage du path

		if(!$path) {
			$path = '.';
		} else {
			$path = preg_replace('/\/$|\/\s$|\s$/', '', $path);
		}

		$cart_list=array();
		if ($dir = @opendir($path)) {
  			while($file = readdir($dir)) {
				if(preg_match('/\.xml$/i', $file)) { 
					$myCart = new cart($file, $path);
					if($myCart->name) {
						$cart_list[] = array( 
							'name' => $myCart->name,
							'file' => $myCart->file,
							'items'=> $myCart->nb_items,
							'description' => $myCart->description);
					}
				} 
 			}
  			closedir($dir);
		}
		return $cart_list;

	}

	// création d'un panier vide
	
	public function create_cart() {
		$this->name = 'CART'.time();
		$this->file = $this->path.$this->name.".xml";
		if( $fp = fopen($this->file, "w"))
			fclose($fp);
		else
			die( "<strong>PMB cart parser error</strong>&nbsp;: can't create cart ".$this->file);
		$this->nb_items = 0;
	}


	// ajout d'un item

	public function add_item($item=0) {
		if(!(int)$item || in_array($item, $this->entry))
			return;
		$this->entry[] = $item;
		$this->nb_items = count($this->entry);
	}


	// suppression d'un item

	public function del_item($item = 0) {
	    if (!(int)$item) {
			return;
	    }
		$nb_entries = count($this->entry);
		for ($i = 0; $i < $nb_entries; $i++) {
			if ((int) $this->entry[$i] == $item) {
				$this->entry[$i] = 0;
				$this->nb_items--;
			}
		}
		$this->entry = array_filter($this->entry, 'array_clean');
	}


	// suppression d'un fichier de panier

	public function delete() {
		if(@unlink($this->file)) {
			$this->entry=array();
			$this->name='';
			$this->nb_items=0;
			$this->description='';
			$this->file='';
		}
	}

	// sauvegarde du panier

	public function save_cart() {
		if ($fp = @fopen($this->file, 'w')) {
			$header = "";
			$header .= "\n<!DOCTYPE cart SYSTEM \"$this->dtd_path\">";
			$header .= "\n<cart name=\"$this->name";
			$header .= "\" description=\"$this->description\">";
			fwrite($fp, $header);
			// élimination des valeurs nulles
			$this->entry = array_filter($this->entry, 'array_clean');
			$nb_entries = count($this->entry);
			for ($i = 0; $i < $nb_entries; $i++) {
			    if ((int) $this->entry[$i]) {
					fwrite($fp, "\n\t<item>$this->entry[$i]</item>");
			    }
			}
			$footer = "\n</cart>\n";
			fwrite($fp, $footer);
			fflush($fp);
			fclose($fp);
		} else {
			die("<strong>PMB cart parser error</strong>&nbsp;: can't store datas in $this->file");
		}
		
	}

	// fonctions du gestionnaire d'éléments
	
	public function debutBalise($parser, $nom, $attributs) {
		switch($nom) {
			case 'CART':
				$this->name = $attributs['NAME'];
				$this->description =  $attributs['DESCRIPTION'];
				break;
			case 'ITEM':
				break;
			default:
				break;
		}
		return;
	}

	public function finBalise($parser, $nom) {
		return;
	}

	// content() -> gestionnaire de données

	public function content($parser, $data) {
		if((int)$data) {
			$this->entry[] = $data;
		}
		return;
	}



	// get_cart() : ouvre un fichier et récupère le panier
	public function get_cart() {
		global $charset;
		if(! $fp = @fopen($this->file, 'r')) {
			die( "<strong>PMB cart parser error</strong>&nbsp;: can't access ".$this->file);
		} else {
			$file_size=filesize ($this->file);
			$data = fread ($fp, $file_size);
			
			$rx = "/<?xml.*encoding=[\'\"](.*?)[\'\"].*?>/m";
			if (preg_match($rx, $data, $m)) $encoding = strtoupper($m[1]);
				else $encoding = "ISO-8859-1";
			$this->parser = xml_parser_create($encoding);
			xml_parser_set_option($p, XML_OPTION_TARGET_ENCODING, $charset);		
			xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, true);
			xml_set_object($this->parser, $this);
			xml_set_element_handler($this->parser, "debutBalise", "finBalise");
			xml_set_character_data_handler($this->parser, "content");
			while($data = fread($fp, 4096)) {
				if( !xml_parse($this->parser, $data, feof($fp))) {
					die( sprintf("XML error : %s <br />at line %d\n\n'",
						xml_error_string(xml_get_error_code($this->parser)),
						xml_get_current_line_number($this->parser))); 
				}
			}
			fclose($fp);
			xml_parser_free($this->parser);
		}
	}

} // fin de déclaration de la classe cart
  
} # fin de déclaration du fichier cart.class