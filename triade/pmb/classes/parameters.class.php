<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: parameters.class.php,v 1.36.6.1.2.5 2025/05/22 07:57:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de gestion du paramarétrage des procédures stockées
global $include_path;
require_once("$include_path/fields.inc.php");
require_once("$include_path/parser.inc.php");

//Pour le parser XML
function _field_($param) {
	global $parameters_description;
	$parameters_description[$param['NAME']]=$param;
}

class parameters {
	public $id_query;								//N° de procédure
	public $query_parameters;				//Paramètres exprimés dans la requête
	public $parameters_description;	//Description logique des paramètres
	public $parameters_value;				//Liste des valeurs retournées par le formulaire
	public $n_parameters;						//Nombre de parmamètres
	public $final_query;							//Requête finale après transformation
	public $table;									//Table des procédures

	//Eléments issus de la table des procédures
	public $proc;

	//Créateur, renvoie 1 si l'initialisation de la classe s'est bien passée, sinon 0
	public function __construct($id_parameters,$table="caddie_procs") {

		$this->table=$table;

		//Vérification que la requête existe
		if ($table=="caddie_procs" || $table=="authorities_caddie_procs") {
			$requete="select idproc, type, name, requete, comment, autorisations, parameters from ".$this->table." where idproc=$id_parameters";
		} else {
			//Ca c'est parcequ'Eric est borné !!
			$requete="select idproc, name, requete, comment, autorisations, parameters from ".$this->table." where idproc=$id_parameters";
		}
		$resultat=pmb_mysql_query($requete);

		//Si requête échoue, c'est que le numéro passé n'est pas un nombre
		if ($resultat==false) return 0;
		//Si il y a 0 résultats, c'est que l'id passé n'existe pas
		if (pmb_mysql_num_rows($resultat)==0) return 0;

		$this->proc=pmb_mysql_fetch_object($resultat);
		$this->id_query=$id_parameters;
		//Récupération des paramètres cités dans la requête
		if (!$this->get_query_parameters()) return 0;
		//Récupération des paramètres décrits
		if (!$this->get_parameters_description()) return 0;
		//Vérification de la concordance et complément automatique s'il manque certains paramètres
		if (!$this->check_parameters()) return 0;

		$this->n_parameters=count($this->query_parameters);
		return 1;
	}

	public function get_hidden_values() {
		global $charset;
		$ret="";
		for ($i=0; $i<count($this->query_parameters); $i++) {
			$name=$this->query_parameters[$i];
			global ${$name};
			$val=${$name};
			if (isset($val)) {
				if (is_array($val)) {
					for ($j=0; $j<count($val); $j++) {
						$ret.="<input type='hidden' name='".$name."[$j]' value=\"".stripslashes(htmlentities($val[$j],ENT_QUOTES,$charset))."\" />\n";
					}
				} else {
						$ret.="<input type='hidden' name='".$name."' value=\"".stripslashes(htmlentities($val,ENT_QUOTES,$charset))."\" />\n";
				}
			}
		}
		return $ret;
	}

	//Pour ceux qui ne veulent pas gérer les appels en fonction du formulaire
	public function proceed() {
		global $form_type;
		//Si type de formulaire vide alors retourner 0
		if ($form_type=="") return 0;
		switch ($form_type) {
			//Le formulaire était le formulaire de saisie des paramètres d'une requête :
			// appel du constructeur de requête et retour de la valeur 1
			case "gen_form":
				$this->get_final_query();
				return 1;
				break;
			//Le formulaire étatit le formulaire de configuration des paramètres :
			//appel de la fonction de mise à jour des paramètres dans la table des procédures
			//et retour = 2
			case "config_form":
				$this->update_config();
				return 2;
				break;
		}
	}

	//Récupération des paramètres de la requête
	public function get_query_parameters() {
		$query_parameters=array();
		//S'il y a des termes !!*!! dans la requête alors il y a des paramètres
		if (preg_match_all("|!!(.*)!!|U",$this->proc->requete,$query_parameters)) {
			$this->query_parameters=array();
			for ($i=0; $i<count($query_parameters[1]); $i++) {
				$as=array_search($query_parameters[1][$i],$this->query_parameters);
				if (!(($as!==false)&&($as!==null)))
					$this->query_parameters[]=$query_parameters[1][$i];
			}
			return 1;
		} else {
			//Sinon retour faux
			return 0;
		}
	}

	//Récupération de la description XML des paramètres et transformation en tableau
	public function get_parameters_description() {
		global $parameters_description;
		$parameters_description=array();
		//Appel du parser
		_parser_text_($this->proc->parameters, array("FIELD"=>"_field_"), "FIELDS");
		//Récupération du tableau
		$this->parameters_description=$parameters_description;
		return 1;
	}

	//Comparaison entre les paramètres trouvés dans la requête et ceux trouvés dans le champ XML
	//Si besoin, création des paramètres de la requête non détaillés dans le XML
	public function check_parameters() {
		//Paramètre par défaut : texte obligatoire
		$default_param=array();
		$default_param['MANDATORY']="yes";
		$default_param['ALIAS'][0]['value']="";
		$default_param['TYPE'][0]['value']="text";
		//Pour chaque paramètre trouvé dans la requête
		for ($i=0; $i<count($this->query_parameters);$i++) {
			//Si le paramètre n'est pas décrit
			if (!isset($this->parameters_description[$this->query_parameters[$i]]) ||!$this->parameters_description[$this->query_parameters[$i]]) {
				//Ajout du paramètre par défaut dans le tableau de description
				$default_param['NAME']=$this->query_parameters[$i];
				$default_param['ALIAS'][0]['value']=$this->query_parameters[$i];
				$this->parameters_description[$this->query_parameters[$i]]=$default_param;
			}
		}
		return 1;
	}

	//Renvoi du type d'un paramètre
	public function get_field_type($field) {
		return $field['TYPE'][0]['value'];
	}

	//Renvoi de l'alias d'un paramètre (texte affiché dans le formulaire)
	public function get_field_alias($field) {
		return $field['ALIAS'][0]['value'];
	}

	//Renvoi des options d'un type de paramètre
	public function get_field_options($field) {
		return $field['OPTIONS'][0];
	}

	public function get_content_form(&$check_scripts = "") {
		global $aff_list;

		$content_form = "<table class='table-no-border' style='width:100%'>\n";

		//Affichage des champs
		$champ_focus="";//nom du champ où l'on va mettre le focus
		if (!empty($this->query_parameters)) {
    		for ($i=0; $i<count($this->query_parameters); $i++) {
    			$name=$this->query_parameters[$i];
    			$champ_type=$this->get_field_type($this->parameters_description[$name]);
    			if(!$champ_focus && ($champ_type == "text")) $champ_focus=$name;//en priorité le premier champ texte
    			$content_form .= pmb_bidi("<tr><td>".$this->get_field_alias($this->parameters_description[$name])."</td>");

				$aff = '';
				$function_name = $aff_list[$champ_type];
				if (function_exists($function_name)) {
					$aff = $function_name($this->parameters_description[$name], $check_scripts);
				}
    			$content_form .= pmb_bidi("<td>".$aff."</td></tr>\n");
    		}
		}
		$content_form .= "</table>";
		return $content_form;
	}

	public function get_form($lien_base) {
		//$aff_list = liste des fonctions d'affichage en fonction du type
		global $msg;
		global $current_module;
		global $base_path;
		//$check_scripts contients les javascripts de test de validité des champs avant soumission
		$check_scripts="";

		//Titre du formulaire
		$form = "<form class='form-$current_module' id=\"formulaire\" name=\"formulaire\" action='$lien_base' method='post' enctype='multipart/form-data'>
		<h3>".$msg["proc_param_choice"]."</h3><div class='form-contenu'>";
		$form .= pmb_bidi("<h3>".$this->proc->name."</h3>");
		$form .= pmb_bidi("<i>".$this->proc->comment."</i>");
		$form .= "<br /><br />";
		$form .= $this->get_content_form($check_scripts);
		$form .= "</div>";
		$champ_focus='';
		if (!empty($this->query_parameters[0])) {
		    $this->query_parameters[0];//Si pas de champ texte par défaut on prend le premier
		}
		//Compilation des javascripts de validité renvoyés par les fonctions d'affichage
		$check_scripts="<script>function cancel_submit(message) { alert(message); return false;}\nfunction check_form() {\n".$check_scripts."\nreturn true;\n}\n</script>";
		$form .= $check_scripts;
		//Boutons d'annulation/soumission
		$form .= "<input class='bouton' type=\"button\" value=\"".$msg["76"]."\" onClick=\"history.go(-1);\">&nbsp;<input class='bouton' type=\"submit\" value=\"".$msg["proc_param_start"]."\" onClick=\"return check_form()\" />";
		$form .= "<input type=\"hidden\" name=\"id_query\" value=\"".$this->id_query."\" />\n";
		$form .= "<input type=\"hidden\" name=\"form_type\" value=\"gen_form\" />\n";
		$form .= "</form>";
		$form .= "
			<script type='text/javascript' src='".$base_path."/javascript/ajax.js'></script>
			<script type='text/javascript'>
				ajax_parse_dom();
				if (document.forms['formulaire'].elements['".$champ_focus."'] && document.forms['formulaire'].elements['".$champ_focus."'].focus) document.forms['formulaire'].elements['".$champ_focus."'].focus();
			</script>";
		return $form;
	}

	//Génération du formulaire de saisie des paramètres
	//$lien_base = adresse de postage du formulaire
	public function gen_form($lien_base) {
		echo $this->get_form($lien_base);
	}

	//Génération du formulaire de saisie des paramètres pour le planificateur
	public function gen_form_plann() {
		//$aff_list = liste des fonctions d'affichage en fonction du type
		global $aff_list;
		global $msg;
		//$check_scripts contients les javascripts de test de validité des champs avant soumission
		$check_scripts="";

		//Titre du formulaire
		$result = "<h3>".$msg["proc_param_choice"]."</h3><div class='form-contenu'>";
		$result .= pmb_bidi("<h3>".$this->proc->name."</h3>");
		$result .= pmb_bidi("<i>".$this->proc->comment."</i>");
		$result .= "<br /><br />";
		$result .= "<table class='table-no-border' style='width:100%'>\n";

		//Affichage des champs
		for ($i=0; $i<count($this->query_parameters); $i++) {
			$name=$this->query_parameters[$i];
			//appel de la globale pré-enregistré (par le planificateur) s'il y a...
			global ${$name};

			$result .= pmb_bidi("<tr><td>".$this->get_field_alias($this->parameters_description[$name])."</td>");

			$aff = '';
			$function_name = $aff_list[$this->get_field_type($this->parameters_description[$name])];
			if (function_exists($function_name)) {
				$aff = $function_name($this->parameters_description[$name], $check_scripts);
			}
			$result .= pmb_bidi("<td>".$aff."</td></tr>\n");
		}
		$result .= "</table></div>";
		//Compilation des javascripts de validité renvoyés par les fonctions d'affichage
		$check_scripts="<script>function cancel_submit(message) { alert(message); return false;}\nfunction check_form() {\n".$check_scripts."\nreturn true;\n}\n</script>";
		$result .= $check_scripts;

//		$result .= "<input type=\"hidden\" name=\"id_query\" value=\"".$this->id_query."\" />\n";
//		$result .= "<input type=\"hidden\" name=\"form_type\" value=\"gen_form\" />\n";

		return $result;
	}

	//sérialisation des paramètres de la procédure pour le planificateur
	public function make_serialized_parameters_params() {
		$t = array();
		//seulement pour les procs internes...
		if ($this->parameters_description) {
			foreach($this->parameters_description as $parameter) {
				$name = $parameter["NAME"];
				global ${$name};
				$t[$name] = ${$name};
			}
		}

		return $t;
	}

	//désérialisation des paramètres de la procédure pour le planificateur
	public function make_unserialized_parameters_params($parameters) {
	    if (is_countable($parameters)) {
	        foreach ($parameters as $aparameter=>$parameter) {
	            global ${$aparameter};
	            ${$aparameter} = $parameter;
            }
        }
	}

	//Récupération de la requête interprétée en fonction de ce qui a été saisi
	//dans le formulaire de saisie des paramètres
	public function get_final_query($return_error=false) {
		global $chk_list;
		global $val_list;

		//Vérification du formulaire côté serveur
		if(!empty($this->query_parameters)) {
			for ($i=0; $i<count($this->query_parameters); $i++) {
				$name=$this->query_parameters[$i];
				$chk = false;
				$check_message = '';
				$function_name = $chk_list[$this->get_field_type($this->parameters_description[$name])];
				if (function_exists($function_name)) {
					$chk = $function_name($this->parameters_description[$name], $check_message);
				}

				if (!$chk) {
				    if ($return_error) {
				        return $check_message;
				    } else {
    					echo "<script>alert(\"".$check_message."\"); history.go(-1);</script>";
    					exit();
				    }
				}
			}
		}

		//Récupération des valeurs finales & remplacement dans la requête
		$query=$this->proc->requete;
		if(!empty($this->query_parameters)) {
			for ($i=0; $i<count($this->query_parameters); $i++) {
				$name=$this->query_parameters[$i];
				eval("\$val=".$val_list[$this->get_field_type($this->parameters_description[$name])]."(\$this->parameters_description[\$name]);");
				if($this->get_field_type($this->parameters_description[$name]) == 'selector'){
					$field_options = $this->get_field_options($this->parameters_description[$name]);
					if(!is_numeric($val) && ($field_options['DATA_TYPE'][0]['value'] == 9)){
						$val = onto_common_uri::get_id($val);
					}
				}
				$query=str_replace("!!".$name."!!",$val,$query);
			}
		}
		//Stockage du résultats
		$this->final_query=$query;
		return '';
	}

	//Conversion en XML du tableau des options
	public function options_to_xml($field) {
	    if (isset($field['OPTIONS'][0])) {
	        return array_to_xml($field['OPTIONS'][0],"OPTIONS");
	    }
	    return "";
	}

	//Affichage de la liste des types de champs
	public function show_list_type($field) {
		global $type_list;
		$res="<select name=\"".$field['NAME']."_type\">";
		reset($type_list);
		foreach ($type_list as $key => $val) {
			$res.="<option value=\"".$key."\" ";
			if ($key==$field['TYPE'][0]['value']) $res.="selected";
			$res.=">".$val."</option>";
		}
		$res.="</select>";
		return $res;
	}

	//Fonction de mise à jour de la description des paramètres d'une procédure
	//l'appel doit être fait après le soumission du formulaire de configuration
	public function update_config($lien_base = '') {
		global $charset;
		global $msg;

		$ret="<?xml version=\"1.0\" encoding=\"$charset\"?>\n";
		$ret.="<FIELDS>\n";

		//Pour chaque paramètre
		for ($i=0; $i<count($this->query_parameters); $i++) {
			$name=$this->query_parameters[$i];
			//Récupération des valeurs du formulaire de configuration
			$alias=$name."_alias";
			$mandatory=$name."_mandatory";
			$for=$name."_for";
			$type=$name."_type";
			$options=$name."_options";
			global ${$alias},${$mandatory},${$for},${$type},${$options};

			//Transformation de mandatory en "yes" ou "no"
			if (${$mandatory}==1) ${$mandatory}="yes"; else ${$mandatory}="no";

			//Si un type choisi dans le formulaire de configuration ne correspond pas au type des options
			//alors erreur !
			if (${$type}!=${$for}) {
				echo "<script>alert(\"".sprintf($msg["proc_param_bad_type"],$name,${$alias})."\"); history.go(-1);</script>";
				exit();
			}

			//Ajout de la description XML du paramètre
			$ret.=" <FIELD NAME=\"".$name."\" MANDATORY=\"".${$mandatory}."\">\n";
			$ret.="  <ALIAS><![CDATA[".stripslashes(${$alias})."]]></ALIAS>\n";
			$ret.="  <TYPE>".${$type}."</TYPE>\n";
			$ret.=stripslashes(${$options})."\n";
			$ret.=" </FIELD>\n";
		}
		$ret.="</FIELDS>";

		//Mise à jour de la procédure
		$requete="update ".$this->table." set parameters='".addslashes($ret)."' where idproc=".$this->id_query;
		pmb_mysql_query($requete);

		//Retour au lien
		echo "<script>document.location='$lien_base';</script>";
	}

	//Formulaire de configuration des paramètres
	//$lien_base = adresse de postage du formulaire
	//$lien_cancel = adresse de retour en cas d'annulation
	public function show_config_screen($lien_base,$lien_cancel) {
		global $type_list;
		global $options_list;
		global $charset;
		global $include_path;
		global $msg;
		global $current_module;
		global $charset;
		echo "<form class='form-$current_module' name=\"formulaire\" method=\"post\" action=\"$lien_base\">\n";
		//Titre du formulaire
		echo "<h3>".$msg["proc_param_define"]."</h3><div class='form-contenu'><h3>".$this->proc->name."</h3>";
		echo pmb_bidi("<i>".$this->proc->comment."</i><br />");
		$html_requete=$this->proc->requete;

		//Surlignage des paramètres dans la requête
		for ($i=0; $i<count($this->query_parameters); $i++) {
			$name=$this->query_parameters[$i];
			$html_requete=str_replace("!!".$name."!!","<span style='color:#AA0000'><b><i>".$name."</i></b></span>",$html_requete);
		}
		echo "<br />".$html_requete."<br />";
		echo "<br />";
		echo "<table class='table-no-border' style='width:100%'>\n";
		echo "<tr><th></th><th>".$msg["proc_param_title"]."</th><th>".$msg["proc_param_choice_mod"]."</th><th>".$msg["proc_param_mandatory"]."</th><th></th></tr>\n";

		//Affichage du tableau de configuration des paramètres
		for ($i=0; $i<count($this->query_parameters); $i++) {
			$name=$this->query_parameters[$i];
			echo pmb_bidi("<tr><td><b>".$name."</b></td><td><input type=\"text\" value=\"".htmlentities($this->get_field_alias($this->parameters_description[$name]),ENT_QUOTES,$charset)."\" name=\"".$name."_alias\"></td>");
			echo pmb_bidi("<td>".$this->show_list_type($this->parameters_description[$name])."</td>");
			echo "<td><input type=\"checkbox\" name=\"".$name."_mandatory\" value=\"1\" ";
			if ($this->parameters_description[$name]['MANDATORY']=="yes") echo "checked";
			echo "></td><td><input class=\"bouton\" type=\"button\" value=\"".$msg["proc_param_options"]."\"  onClick=\"openPopUp('".$include_path."/options/options.php?name=".$name."&type='+this.form.".$name."_type.options[this.form.".$name."_type.selectedIndex].value,'options');\"><input type=\"hidden\" name=\"".$name."_options\" value=\"".htmlentities($this->options_to_xml($this->parameters_description[$name]),ENT_QUOTES, $charset)."\" /><input type=\"hidden\" name=\"".$name."_for\" value=\"".$this->get_field_type($this->parameters_description[$name])."\" /></td>";
			echo "</tr>\n";
		}
		echo "</table></div>";

		//Boutons de soumission/annulation
		echo "<input type=\"hidden\" name=\"id_query\" value=\"".$this->id_query."\" />\n";
		echo "<input type=\"hidden\" name=\"form_type\" value=\"config_form\" />\n";
		echo "<input type=\"button\" value=\"".$msg["76"]."\" class=\"bouton\" onClick=\"document.location='$lien_cancel';\">&nbsp;<input type=\"submit\" value=\"".$msg["77"]."\" class=\"bouton\" />";
		echo "</form>";
	}

	//Verification de la presence et de la syntaxe des parametres de la requete
	//retourne true si OK, le nom du parametre entre parentheses sinon
	public static function check_param($requete) {
		$query_parameters=array();
		//S'il y a des termes !!*!! dans la requête alors il y a des paramètres
		if (preg_match_all("|!!(.*)!!|U",$requete,$query_parameters)) {
			for ($i=0; $i<count($query_parameters[1]); $i++) {
				if (!preg_match("/^[A-Za-z][A-Za-z0-9_]*$/",$query_parameters[1][$i])) {
					return "(".$query_parameters[1][$i].")";
				}
			}
		}
		return true;
	}
}
?>