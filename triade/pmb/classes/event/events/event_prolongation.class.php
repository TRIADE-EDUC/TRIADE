<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_prolongation.class.php,v 1.1.2.2 2024/12/17 15:16:36 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
	die("no access");

require_once($class_path . '/event/event.class.php');

class event_prolongation extends event
{

	protected $id_prolongation;
	protected $id_empr;

	/**
	 * Récupère l'identifiant de la prolongation.
	 *
	 * @return int
	 */
	public function get_id_prolongation()
	{
		return $this->id_prolongation;
	}

	
	/**
	 * Récupère l'identifiant de l'emprunteur.
	 *
	 * @return int
	 */
	public function get_id_empr()
	{
		return $this->id_empr;
	}

	/**
	 * Affecte l'identifiant de la prolongation.
	 *
	 * @param int $id_prolongation
	 * @return self
	 */
	public function set_id_prolongation($id_prolongation)
	{
		$this->id_prolongation = $id_prolongation;
		return $this;
	}

	/**
	 * Affecte l'identifiant de l'emprunteur.
	 *
	 * @param int $id_empr
	 * @return self
	 */
	public function set_id_empr($id_empr)
	{
		$this->id_empr = $id_empr;
		return $this;
	}
}