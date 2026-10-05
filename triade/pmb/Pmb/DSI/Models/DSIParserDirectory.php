<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DSIParserDirectory.php,v 1.2.4.1 2025/03/19 15:53:33 jparis Exp $

namespace Pmb\DSI\Models;

use cache_factory;
use Pmb\Common\Library\Parser\ParserDirectory;

class DSIParserDirectory extends ParserDirectory
{
	protected $baseDir = __DIR__;
	/**
	 *
	 * @var array
	 */
	protected $catalog = [];

	protected $parserManifest = "\Pmb\DSI\Models\DSIParserManifest";

	/**
	 * 
	 * @param string $namespace
	 * @return string[]
	 */
	public function getCompatibility(string $namespace)
	{
		$manifest = $this->getManifestByNamespace($namespace);
		return $manifest ? $manifest->compatibility : [];
	}
	/**
	 *
	 * @param string $namespace
	 * @return DSIParserManifest|null
	 */
	public function getManifestByNamespace(string $namespace)
	{
		return !empty($this->manifest[$namespace]) ? $this->manifest[$namespace] : null;
	}

	protected function parse()
	{
		$path = $this->baseDir;
		$cache = cache_factory::getCache();
		$cacheKey = 'dsi_manifests_' . md5($path);
		$manifests = [];

		// Tentative de récupération des données depuis le cache
		if ($cache) {
			$cacheData = $cache->getFromCache($cacheKey);

			if ($cacheData && is_array($cacheData)) {
				foreach ($cacheData as $key => $manifest) {
					if ($manifest instanceof DSIParserManifest && is_string($manifest->simplexml)) {
						$manifest->simplexml = simplexml_load_string($manifest->simplexml);
					}
					$manifests[$key] = $manifest;
				}

				// Si les données sont présentes dans le cache, on les charge
				$this->pathManifest = $manifests;
				$manifests = array_values($manifests);
			}
		}

		// Si le cache n'a pas retourné les données, on charge les manifests
		if (empty($manifests)) {
			// Récupération des manifests depuis le système de fichiers
			$manifests = $this->loadManifests($path);

			// Mise en cache des données pour la prochaine fois
			if ($cache) {
				$manifestsClone = [];
				foreach ($this->pathManifest as $key => $manifest) {
					if ($manifest instanceof DSIParserManifest) {
						
						// on passe le simplexml en chaine de caractères car il n'est pas serialisable et provoque une erreur
						$clonedManifest = clone $manifest;
						$clonedManifest->simplexml = $clonedManifest->simplexml->asXML();
					}
					$manifestsClone[$key] = $clonedManifest;
				}

				$cache->setInCache($cacheKey, $manifestsClone);
			}
		}

		foreach ($manifests as $manifest) {
			if(!($manifest instanceof DSIParserManifest)) {
				continue;
			}

			$this->manifest[$manifest->namespace] = $manifest;

			if (!isset($this->catalog[$manifest->type])) {
				$this->catalog[$manifest->type] = [];
			}
			$this->catalog[$manifest->type][] = $manifest->namespace;
		}
		$this->parsed = true;
	}

	/**
	 *
	 * @param string $type
	 * @return string
	 */
	public function getClass(string $type)
	{
		return !empty($this->catalog[$type]) ? $this->catalog[$type] : "";
	}

	/**
	 * Retourne le catalogue
	 * @return array
	 */
	public function getCatalog()
	{
		return $this->catalog;
	}
}

