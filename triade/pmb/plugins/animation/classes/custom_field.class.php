<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_field.class.php,v 1.3 2022/12/14 15:06:04 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], '.class.php')) {
	die('no access');
}

require_once "$base_path/plugins/animation/classes/animation_conf.class.php";

class custom_field
{
	/**
	 * Identifient du champ perso
	 *
	 * @var int|string
	 */
	private $id_champ = 0;
	
	public function __construct()
	{
		$this->add_parametre_perso();
	}
	
	/**
	 * Vérifie si le champ perso est présent pour le type d'article
	 *
	 * @return boolean
	 */
	private function check_parametre_perso()
	{
		if (empty($this->id_champ)) {
			$query = 'SELECT idchamp FROM cms_editorial_custom WHERE name = "cp_animation_id" and num_type = (SELECT id_editorial_type FROM cms_editorial_types WHERE editorial_type_element = "article_generic")';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$this->id_champ = pmb_mysql_result($result, 0, 0);
			} else {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Ajoute le champ perso pour le type d'article
	 */
	private function add_parametre_perso()
	{
		if (! $this->check_parametre_perso()) {
			
			$order = 0;
			$query = 'SELECT MAX(ordre)+1 FROM cms_editorial_custom WHERE num_type = (SELECT id_editorial_type FROM cms_editorial_types WHERE editorial_type_element = "article_generic")';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$order = intval(pmb_mysql_result($result, 0, 0));
			}
			
			$query = 'INSERT INTO cms_editorial_custom set
                        num_type = (SELECT id_editorial_type FROM cms_editorial_types WHERE editorial_type_element = "article_generic"), name="cp_animation_id",
                        titre="' . plugins::get_message('animation', "animation_cp_title") . '", type="text", datatype="integer",
                        options="<OPTIONS FOR=\"text\">' . PHP_EOL . ' <SIZE>50</SIZE>' . PHP_EOL . ' <MAXSIZE>255</MAXSIZE>' . PHP_EOL . ' <REPEATABLE>0</REPEATABLE>' . PHP_EOL . ' <ISHTML>0</ISHTML>' . PHP_EOL . '</OPTIONS>",
                        multiple="0", obligatoire="0", ordre="' . $order . '", search="0", export="0", exclusion_obligatoire="0", opac_sort="100", comment="", custom_classement=""';
			pmb_mysql_query($query);
			$this->id_champ = pmb_mysql_insert_id();
		}
		$this->migrate_old_parametre_perso();
	}
	
	/**
	 * Ancien paramètre perso, on récupère les valeurs et on le supprime
	 */
	private function migrate_old_parametre_perso()
	{
		$query = 'SELECT idchamp FROM cms_editorial_custom WHERE name="cp_animation_id" AND
                    num_type IN (SELECT id_editorial_type FROM cms_editorial_types WHERE editorial_type_element = "article")';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_assoc($result)) {
				
				$query2 = 'SELECT 1 FROM cms_editorial_custom_values WHERE
                    cms_editorial_custom_champ = "' . intval($row['idchamp']) . '"';
				$result2 = pmb_mysql_query($query2);
				if (pmb_mysql_num_rows($result2)) {
					// On récupère les anciennes données
					pmb_mysql_query("UPDATE cms_editorial_custom_values SET cms_editorial_custom_champ='" . intval($this->id_champ) . "' WHERE cms_editorial_custom_champ = '" . intval($row['idchamp']) . "'");
				}
				
				// On supprime le champ perso
				pmb_mysql_query("DELETE FROM cms_editorial_custom WHERE idchamp = '" . intval($row['idchamp']) . "'");
			}
		}
	}
	
	public function get_id_champ()
	{
		return $this->id_champ ?? 0;
	}
}