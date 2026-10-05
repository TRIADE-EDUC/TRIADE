<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: connecteurs_out.class.php,v 1.20.4.1 2025/03/04 10:45:25 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/*
=====================================================================================================
Comment ça marche toutes ces classes

   .--------------------.                .----------------------.
   |   connecteur_out   |                |   connecteurs_out    |
   |--------------------| [all]          |----------------------|
   | représente un      |<---------------| contient tous les    |
   | connecteur sortant |                | connecteurs sortants |
   '--------------------'                '----------------------'
              |
              |
              | contient
       [0..n] v des sources
  .-----------------------.
  | connecteur_out_source |
  |-----------------------|                            ********************************************
  | représente une        |                            * function:connector_out_check_credentials *
  | source externe        |                            ********************************************
  '-----------------------'                            * vérifie les droits d'un utilisateur      *
       [0..n] ^ une source peut être utilisée          * externe à utiliser une source            *
              | (ou non) par des groupes               *                                          *
              | d'utilisateurs                         ********************************************
              |
       [0..n] |                                        ***************************************
 ...........................                           * function:instantiate_connecteur_out *
 .       es_esgroup        .                           ***************************************
 ...........................                           * instancie la classe associée        *
 . représente un groupe d' .                           * à un connecteur                     *
 . utilisateurs externes   .                           ***************************************
 ...........................

 =====================================================================================================
 */
global $class_path, $include_path;

require_once "{$include_path}/parser.inc.php";
require_once "{$class_path}/external_services.class.php";
require_once "{$class_path}/external_services_esusers.class.php";
require_once "{$include_path}/connecteurs_out_common.inc.php";
require_once "{$class_path}/password/password.class.php";
require_once $class_path.'/encoding_normalize.class.php';

class connecteur_out {
	public $id=0;
	public $path="";
	public $name="";
	public $comment="";
	public $author="";
	public $org="";
	public $date="";
	public $url="";
	public $api_requirements=array();
	public $msg;
	public $config=array();
	public $sources=array();

	public function __construct($id, $path='') {
		global $base_path;

		if (!$path) {
			global $base_path;
			$filename = $base_path."/admin/connecteurs/out/catalog.xml";
			$xml=file_get_contents($filename);
			$param=_parser_text_no_function_($xml,"CATALOG",$filename);

			foreach ($param["ITEM"] as $anitem) {
				if ($anitem["ID"] == $id) {
					$path = $anitem["PATH"];
					break;
				}
			}
		}

		$this->id = intval($id);
		$this->path = $path;
		if (!$this->id || !$this->path)
			return false;

		if (file_exists($base_path."/admin/connecteurs/out/$path/manifest.xml"))
			$manifest=$base_path."/admin/connecteurs/out/$path/manifest.xml";
		else
			$manifest=$base_path."/admin/connecteurs/out/$path/manifest.xml";
		$this->parse_manifest($manifest);

		$this->get_messages();

		$this->get_config_from_db();

		$this->get_sources();
	}

	public function get_running_pmb_userid($source_id) {
		//Par défaut, les connecteurs executent leurs fonctions en admin
		return 1;
	}

	public function parse_manifest($filename) {
		$xml=file_get_contents($filename);
		$param=_parser_text_no_function_($xml,"MANIFEST");

		$this->name = $param["NAME"][0]["value"];
		$this->comment = $param["COMMENT"][0]["value"];
		$this->author = $param["AUTHOR"][0]["value"];
		$this->org = $param["ORG"][0]["value"];
		$this->date = $param["DATE"][0]["value"];
		$this->url = $param["URL"][0]["value"];

		if (isset($param["API_REQUIREMENTS"][0]["REQUIREMENT"])) {
			foreach ($param["API_REQUIREMENTS"][0]["REQUIREMENT"] as $arequirement) {
				$this->api_requirements[] = array(
					"group" => $arequirement["DOMAIN"],
					"name" => $arequirement["NAME"],
					"version" => $arequirement["VERSION"]
				);
			}
		}
	}

	public function get_messages() {
		global $lang;
		global $base_path;
		$path = $this->path;

		if (file_exists($base_path."/admin/connecteurs/out/$path/messages/".$lang.".xml")) {
			$file_name=$base_path."/admin/connecteurs/out/$path/messages/".$lang.".xml";
		} else if (file_exists($base_path."/admin/connecteurs/out/$path/messages/fr_FR.xml")) {
			$file_name=$base_path."/admin/connecteurs/out/$path/messages/fr_FR.xml";
		}
		if ($file_name) {
			$xmllist=new XMLlist($file_name);
			$xmllist->analyser();
			$this->msg=$xmllist->table;
		}
	}

	public function ckeck_api_requirements() {
		$api_catalog = es_catalog::get_instance();

		foreach ($this->api_requirements as $arequirement) {
			//Pas le groupe? NON!
			if (!isset($api_catalog->groups[$arequirement["group"]]))
				return false;
			//Pas la méthode? NON!
			if (!isset($api_catalog->groups[$arequirement["group"]]->methods[$arequirement["name"]]))
				return false;
			//Pas une version suffisante? NON!
			if ($api_catalog->groups[$arequirement["group"]]->methods[$arequirement["name"]]->version < $arequirement["version"])
				return false;

			//Sinon? OUI!
			return true;
		}
	}

	public function commit_to_db() {
		$sql = "REPLACE INTO connectors_out SET connectors_out_config = '".addslashes(serialize($this->config))."', connectors_out_id = ".$this->id;
		pmb_mysql_query($sql);
	}

	public function get_config_from_db() {
		$sql = "SELECT connectors_out_config FROM connectors_out WHERE connectors_out_id = ".$this->id;
		$res = pmb_mysql_query($sql);
		if(pmb_mysql_num_rows($res)) {
			$row = pmb_mysql_fetch_assoc($res);
			$this->config = unserialize($row["connectors_out_config"]);
		}
	}

	//Abstraite
	public function get_config_form() {
		//Rien
		return "";
	}

	//Abstraite
	public function update_config_from_form() {
		//Rien
		return;
	}

	public function instantiate_source_class($source_id) {
		return new connecteur_out_source($this, $source_id, $this->msg);
	}

	public function get_sources() {
		$sql = "SELECT connectors_out_source_id FROM connectors_out_sources WHERE connectors_out_sources_connectornum = ".$this->id;
		$res = pmb_mysql_query($sql);
		while ($row=pmb_mysql_fetch_assoc($res)) {
			$this->sources[] = $this->instantiate_source_class($row["connectors_out_source_id"]);
		}
	}

	//Cette fonction défini si le connecteur a besoin des messages de /includes/messages/*.xml
	public function need_global_messages() {
		return true;
	}

	//Abstraite
	public function process($source_id, $pmb_user_id) {
		//Cette fonction correspond au traitement d'une requète sur une source dans le cadre de l'utilisation du connecteur

		//Rien
		return;
	}
}

function instantiate_connecteur_out($connector_id) {
	global $msg, $current_module; //utilisées dans les require
	global $base_path;
	$filename = $base_path."/admin/connecteurs/out/catalog.xml";
	$xml=file_get_contents($filename);
	$param=_parser_text_no_function_($xml,"CATALOG",$filename);

	foreach ($param["ITEM"] as $anitem) {
		if ($anitem["ID"] == $connector_id) {
			$before_eval_vars = get_defined_vars();
			require_once $base_path."/admin/connecteurs/out/".$anitem["PATH"]."/".$anitem["PATH"].".class.php";

			//Procédure d'extraction de variable: voir http://fr2.php.net/manual/en/language.variables.scope.php#91982
			$function_variable_names = array("function_variable_names" => 0, "before_eval_vars" => 0, "created" => 0);
		    $created = array_diff_key(get_defined_vars(), $GLOBALS, $function_variable_names, $before_eval_vars);
		    foreach ($created as $created_name => $on_sen_fiche)
        		global ${$created_name};
		    extract($created);

			$conn = new $anitem["PATH"]($connector_id, $anitem["PATH"]);
			return $conn;
		}
	}

	return NULL;
}

class connecteurs_out {
	public $connectors=array();

	public function __construct() {
		global $base_path;
		$filename = $base_path."/admin/connecteurs/out/catalog.xml";
		$xml=file_get_contents($filename);
		$param=_parser_text_no_function_($xml,"CATALOG",$filename);

		foreach ($param["ITEM"] as $anitem) {
			$this->connectors[] = new connecteur_out($anitem["ID"], $anitem["PATH"]);
		}

	}
}

class connecteur_out_source {
	public $id;
	public $connector_id;
	public $connector;
	public $name="";
	public $comment="";

	//modif compatibilite E_STRICT php5.4
	//public $config="";
	public $config=array();

	public $msg=array();

	public function __construct($connector, $id, $msg) {
		$this->id = intval($id);
		$this->connector = $connector;
		$this->connector_id = $connector->id;
		$this->msg = $msg;

		if ($this->id) {
			$sql = "SELECT * FROM connectors_out_sources WHERE connectors_out_source_id = ".$this->id;
			$res = pmb_mysql_query($sql);
			$row = pmb_mysql_fetch_assoc($res);
			$this->name = $row["connectors_out_source_name"];
			$this->comment = $row["connectors_out_source_comment"];
			$this->config = unserialize($row["connectors_out_source_config"]);
			$this->config = stripslashes_array($this->config);
		}
	}

	public function commit_to_db() {
		if (!$this->id)
			return;
		$this->config = addslashes_array($this->config);
		$serialized = serialize($this->config);
		$sql = "REPLACE INTO connectors_out_sources SET connectors_out_source_id = ".$this->id.", connectors_out_sources_connectornum = ".$this->connector_id.", connectors_out_source_name='".addslashes($this->name)."', connectors_out_source_comment = '".addslashes($this->comment)."', connectors_out_source_config = '".addslashes($serialized)."'";
		pmb_mysql_query($sql);
	}

	public static function add_new($connector_id) {
		$sql = "INSERT INTO connectors_out_sources (connectors_out_sources_connectornum) VALUES (".$connector_id.")";
		pmb_mysql_query($sql);
		$new_source_id = pmb_mysql_insert_id();
		$conn = new connecteur_out($connector_id);
		return new connecteur_out_source($conn, $new_source_id, array());
	}

	public static function name_exists($name_to_test) {
		$sql = "SELECT COUNT(1) FROM connectors_out_sources WHERE connectors_out_source_name = '".addslashes($name_to_test)."'";
		$count = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
		return $count > 0;
	}

	public static function get_connector_id($source_id) {
		$source_id = intval($source_id);
		$sql = "SELECT connectors_out_sources_connectornum FROM connectors_out_sources WHERE connectors_out_source_id = ".$source_id;
		$res = pmb_mysql_query($sql);
		$row = pmb_mysql_fetch_array($res);
		return $row["connectors_out_sources_connectornum"];
	}

	public function get_config_form() {
		global $msg, $charset;

		/* Nom de la source */
		$result  = 	'<div class="row"><label class="etiquette" for="source_name">'.$msg["connector_out_sourcename"].'</label><br />';
		$result .=	'<input id="source_name" name="source_name" type="text" value="'.htmlentities($this->name,ENT_QUOTES, $charset).'" class="saisie-80em" required></div><br />';

		/* Commentaire */
		$result  .= 	'<div class="row"><label class="etiquette" for="source_comment">'.$msg["connector_out_sourcecomment"].'</label><br />';
		$result .=	'<input id="source_comment" name="source_comment" type="text" value="'.htmlentities($this->comment, ENT_QUOTES, $charset).'" class="saisie-80em"></div><br />';

		return $result;
	}

	/**
	 * Supprime une source
	 *
	 * @param int $source_id
	 */
    public function delete(int $source_id = 0)
    {
        $source_id = intval($source_id);
        static::staticDelete($source_id);

    }

    public static function staticDelete(int $source_id = 0)
    {
        if (! $source_id) {
            return;
        }
        $sql = "DELETE FROM connectors_out_sources WHERE connectors_out_source_id = " . $source_id;
        pmb_mysql_query($sql);
    }

	public function update_config_from_form() {
		global $source_name, $source_comment;
		$this->name = stripslashes($source_name);
		$this->comment = stripslashes($source_comment);
		//Rien
		return;
	}
}

//Renvoi le pmbuser_id correspondant aux credentials externes passés en paramètre
function connector_out_check_credentials($username, $password, $source_id) {
	$source_id = intval($source_id);

	if (!$username) {
		//--Utilisateur anonyme

		//Verifions si le groupe anonyme a le droit d'utiliser la source
		$sql = "SELECT COUNT(1) FROM connectors_out_sources_esgroups WHERE connectors_out_source_esgroup_sourcenum = ".$source_id.' AND connectors_out_source_esgroup_esgroupnum = -1';
		$count = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
		$allowed = $count > 0;

		if ($allowed) {
			$sql = 'SELECT esgroup_pmbusernum FROM es_esgroups WHERE esgroup_id = -1';
			$res = pmb_mysql_query($sql);
			if (!pmb_mysql_num_rows($res))
				return 1;
			else
				return pmb_mysql_result($res, 0, 0);
		}

		return false;
	} else if (strpos($username, "@") !== false) {
		//--Lecteur

		$login_info = explode("@", $username);
		if (count($login_info) != 2)
			return false;
		$empr_name = $login_info[0];
		$es_group = $login_info[1];
		if (!$empr_name || !$es_group)
			return false;

		//Cherchons le lecteur
		$empr_id=0;
		$encrypted_password = '';
		$password_match = false;
		$sql = "SELECT id_empr, empr_password FROM empr WHERE empr_login = '".addslashes($empr_name)."' limit 1";
		$res = pmb_mysql_query($sql);
		if (pmb_mysql_num_rows($res)) {
			$row = pmb_mysql_fetch_assoc($res);
			$empr_id = $row['id_empr'];
			$encrypted_password = $row['empr_password'];
		}
		//Pas trouvé? Plouf!
		if (!$empr_id) {
			return false;
		}
		$hash_format = password::get_hash_format($encrypted_password);
		if( 'bcrypt' == $hash_format ) {
			$password_match = password::verify_hash($password, $encrypted_password);
		} elseif( $encrypted_password == password::gen_previous_hash($password, $empr_id) ) {
			$password_match = true;
		}
		if (!$password_match) {
			return false;
		}
		//Cherchons le groupe
		$sql = "SELECT esgroup_id FROM es_esgroups WHERE esgroup_name = '".addslashes($es_group)."'";
		$res = pmb_mysql_query($sql);
		//Pas trouvé? Plouf!
		if (!pmb_mysql_num_rows($res))
			return false;
		$esgroup_id = pmb_mysql_result($res, 0, 0);
		$es_group = new es_esgroup($esgroup_id);

		//Vérifions que le lecteur est dans le groupe
		$sql = "SELECT SUM(EXISTS(SELECT 1 FROM empr_groupe WHERE empr_id = ".$empr_id." AND groupe_id = esgroupuser_usernum)) > 0 AS in_group FROM es_esgroup_esusers WHERE esgroupuser_usertype = 2 AND esgroupuser_groupnum = ".$esgroup_id;
		$res = pmb_mysql_query($sql);
		$empr_in_group = pmb_mysql_result($res, 0, 0);
		if (!$empr_in_group)//Vil faquin, tu as cru pouvoir rentré en mentant sur ton groupe d'origine? Ca marche pas ici; plouf!
			return false;

		//Verifions si le groupe a le droit d'utiliser la source
		$sql = "SELECT COUNT(1) FROM connectors_out_sources_esgroups WHERE connectors_out_source_esgroup_sourcenum = ".$source_id.' AND connectors_out_source_esgroup_esgroupnum = '.$esgroup_id;
		$count = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
		$allowed = $count > 0;

		//Pas le droit? Plouf!
		if (!$allowed)
			return false;

		//Et voilà, tout est bon, ça passe
		return $es_group->esgroup_pmbuserid;
	}
	else {
		//--Utilisateur classique

		//Cherchons si cet utilisateur existe, et si oui, récupérons son groupe
		$esuser = es_esuser::create_from_credentials($username, $password);
		if (!$esuser)
			return false;
		$esgroup_id = $esuser->esuser_group;

		//Si l'utilisateur n'est pas dans un groupe, il ne peut pas avoir de droits, donc plouf
		if (!$esgroup_id)
			return false;

		//Verifions si le groupe a le droit d'utiliser la source
		$sql = "SELECT COUNT(1) FROM connectors_out_sources_esgroups WHERE connectors_out_source_esgroup_sourcenum = ".$source_id.' AND connectors_out_source_esgroup_esgroupnum = '.$esgroup_id;
		$count = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
		$allowed = $count > 0;

		//Pas le droit? Plouf!
		if (!$allowed)
			return false;

		//Sinon on renvoi le pmbuserid associé au groupe
		$esgroup = new es_esgroup($esgroup_id);
		return $esgroup->esgroup_pmbuserid;
	}

	return false;
}

?>