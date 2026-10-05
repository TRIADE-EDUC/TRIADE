<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Taggable.php,v 1.6 2023/02/02 09:52:22 rtigero Exp $
namespace Pmb\DSI\Models;

interface Taggable
{

	/**
	 * Retourne les tags lies a une entite
	 */
	public function getEntityTags();

	/**
	 * Relie un tag a une entite
	 *
	 * @param int $numTag
	 * @param int $numEntity
	 */
	public function linkTag(int $numTag, int $numEntity);

	/**
	 * Supprime le lien entre un tag et une entite
	 *
	 * @param int $numTag
	 * @param int $numEntity
	 */
	public function unlinkTag(int $numTag, int $numEntity);
}

