<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Root.php,v 1.38 2023/12/08 09:27:31 jparis Exp $
namespace Pmb\DSI\Models;

use Pmb\Common\Helper\ {
	Helper,
	ParserMessage
};
use Pmb\Common\Models\Model;
use Pmb\DSI\Orm\EntitiesTagsOrm;

class Root extends Model implements CRUD, Taggable
{

	public static $instances = [];

	use ParserMessage;

	/**
	 * Liste des proprietes a ne pas cloner quand on reprend un modele
	 * Une constante EXCLUDED_PROPERTIES peut etre ajoutee dans la classe enfant
	 *
	 * @var array
	 */
	protected const GLOBAL_EXCLUDED_PROPERTIES = [
		"datafetch",
		"structure",
		"id",
		"antiLoopToArray"
	];

	/**
	 * Contient la liste des proprietes a ignorer pour le toArray
	 *
	 * @var array
	 */
	public const IGNORE_PROPS_TOARRAY = [
		"detail",
		"datafetch",
		"structure"
	];

	/**
	 * Contient la liste des proprietes a ne pas convertir en tableau
	 *
	 * @var array
	 */
	public const IGNORE_CONVERT_PROPS_TOARRAY = [
		"settings"
	];

	protected $num_tag = 0;

	public function __get(string $prop)
	{
		if (method_exists($this, Helper::camelize("get " . $prop))) {
			return $this->{Helper::camelize("get " . $prop)}();
		} elseif (property_exists($this, $prop)) {
			$ref = new \ReflectionProperty($this, $prop);
			if ($ref->isStatic()) {
				return $this::${$prop};
			}
			return $this->{$prop};
		}
		throw new \InvalidArgumentException("({$prop}) Unknown property");
	}

	public function __set(string $prop, $value)
	{
		$setterName = Helper::camelize("set " . $prop);
		if (method_exists($this, $setterName)) {
			return call_user_func(array(
				$this,
				$setterName
			), $value);
			// $this->{Helper::camelize("set " . $prop)}($value);
		} elseif (property_exists($this, $prop)) {
			$ref = new \ReflectionProperty($this, $prop);
			if ($ref->isStatic()) {
				return $this::${$prop} = $value;
			}
			return $this->{$prop} = $value;
		}
		throw new \InvalidArgumentException("Unknown property");
	}

	public static function parseCatalog()
	{
		return DSIParserDirectory::getInstance()->getManifests(__DIR__);
	}

	public static function getManifest()
	{
		static::parseCatalog();
		return DSIParserDirectory::getInstance()->getManifestByNamespace(static::class);
	}

	public static function getAvailableTypes()
	{
		return DSIParserDirectory::getInstance()->getCompatibility(static::class);
	}

	/**
	 * Cette methode doit etre remplacee dans les sous-classes
	 *
	 * @return void
	 */
	public function create()
	{
		// Do nothing
	}

	/**
	 * Cette methode doit etre remplacee dans les sous-classes
	 *
	 * @return void
	 */
	public function read()
	{
		// Do nothing
	}

	/**
	 * Cette methode doit etre remplacee dans les sous-classes
	 *
	 * @return void
	 */
	public function update()
	{
		// Do nothing
	}

	/**
	 * Cette methode doit etre remplacee dans les sous-classes
	 *
	 * @return mixed
	 */
	public function delete()
	{
		// Do nothing
	}

	/**
	 * Retourne la liste des tags de l'entite
	 *
	 * @return array
	 */
	public function getEntityTags()
	{
		$result = [];

		if (! empty($this->id)) {
			$relatedTags = EntitiesTagsOrm::finds([
				"num_entity" => $this->id,
				"type" => static::TAG_TYPE
			]);

			foreach ($relatedTags as $related) {
				$result[] = new Tag($related->num_tag);
			}
		}

		return $result;
	}

	/**
	 * Supprime un lien entre une entite et un tag
	 *
	 * @param int $numTag
	 * @param int $numEntity
	 * @return bool
	 */
	public function unlinkTag($numTag, $numEntity)
	{
		$result = EntitiesTagsOrm::finds([
			"num_tag" => $numTag,
			"num_entity" => $numEntity,
			"type" => static::TAG_TYPE
		]);
		if (count($result) == 1) {
			$link = $result[0];
			$link->delete();
			return true;
		}
		return false;
	}

	/**
	 * Ajoute un lien entre une entite et un tag
	 *
	 * @param int $numTag
	 * @param int $numEntity
	 * @return mixed
	 */
	public function linkTag($numTag, $numEntity)
	{
		$result = EntitiesTagsOrm::finds([
			"num_tag" => $numTag,
			"num_entity" => $numEntity,
			"type" => static::TAG_TYPE
		]);
		if (count($result)) {
			return [
				'error' => true,
				'errorMessage' => 'msg:tag_already_linked'
			];
		}

		$link = new EntitiesTagsOrm();
		$link->num_tag = $numTag;
		$link->num_entity = $numEntity;
		$link->type = static::TAG_TYPE;
		$link->save();
		return $link;
	}

	/**
	 * Supprime tous les liens vers les tags d'une entite
	 *
	 * @return void
	 */
	protected function removeEntityTags()
	{
		$relatedTags = EntitiesTagsOrm::finds([
			"num_entity" => $this->id,
			"type" => static::TAG_TYPE
		]);
		foreach ($relatedTags as $related) {
			$related->delete();
		}
	}

	public function fetchData()
	{
		parent::fetchData();
		//Gestion des tags
		$type = static::TAG_TYPE;
		if (isset($type)) {
			$this->tags = $this->getEntityTags();
		}

		//gestion des parametres
		if (! isset($this->settings)) {
			$this->settings = new \stdClass();
			return;
		}
		if ($this->settings != "" && is_string($this->settings)) {
			$this->settings = json_decode($this->settings);
		} else {
			$this->settings = new \stdClass();
		}
		if (empty($this->numModel)) {
			return;
		}
		//Gestion du verrou des modeles
		if (! empty($this->settings->locked) && $this->settings->locked) {
			if (method_exists(static::class, 'getInstance')) {
				$model = static::getInstance($this->numModel);
			} else {
				$model = new static($this->numModel);
			}
			$reflect = new \ReflectionClass($model);
			$props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
			$exclude = array_merge(self::GLOBAL_EXCLUDED_PROPERTIES, static::EXCLUDED_PROPERTIES);

			foreach ($props as $prop) {
				if (! in_array($prop->name, $exclude)) {
					$this->{$prop->name} = $model->{$prop->name};
				}
			}
			//Apres copie on repasse le verrou aux parametres
			$this->settings->locked = true;
		}
	}

	public function checkBeforeDelete()
	{
		// On check si on utilise le modèle dans d'autres items
		$findModels = $this->ormName::finds([
			"num_model" => $this->id
		]);

		if (empty($findModels)) {
			return true;
		}

		return false;
	}

	public static function toArray($data, $default = null)
	{
		$isA = is_a($data, self::class);

		if ($isA && ! empty(static::$antiLoopToArray[strval($data)])) {
			return strval($data);
		}

		if ($isA) {
			static::$antiLoopToArray[strval($data)] = true;
		}

		$result = [];

		if ($isA) {
			$result['__class'] = static::class;
		}

		foreach ($data as $key => $value) {

			if ($isA && in_array($key, static::getIgnorePropsToArray())) {
				continue;
			}

			if ($isA && in_array($key, static::getIgnoreConvertPropsToArray())) {
				$result[$key] = $value;
				continue;
			}

			if ($isA) {
				$value = is_null($data->__get($key)) ? $default : $data->__get($key);
			}

			if (is_object($value) && is_a($value, "\\Pmb\\Common\\Orm\\Orm")) {
				$result[$key] = $value->getInfos();
			} elseif (is_object($value) && method_exists($value, "toArray")) {
				$result[$key] = call_user_func_array([
					$value,
					"toArray"
				], [
					$value,
					$default
				]);
			} elseif (is_object($value) || is_array($value)) {
				$result[$key] = static::toArray($value, $default);
			} else {
				$result[$key] = is_null($value) ? $default : $value;
			}
		}

		if ($isA) {
			static::$antiLoopToArray[strval($data)] = false;
		}

		return $result;
	}

	/**
	 * Duplique un entite
	 *
	 * @param mixed $param
	 *        	parametre a passer dans certains cas
	 */
	public function duplicate($param = null)
	{
		if (method_exists(static::class, 'getInstance')) {
			$newEntity = static::getInstance($this->id);
		} else {
			$newEntity = new static($this->id);
		}
		$newEntity->name = $this->getDuplicateName($this->name);
		$newEntity->id = 0;
		$newEntity->create();

		if ($newEntity->id != 0) {
			return $newEntity;
		}
		return false;
	}

	/**
	 * Retourne un nom pour l'entite dupliquee
	 *
	 * @param string $name
	 *        	le nom de l'entite a dupliquer
	 * @param int $num
	 *        	numero de la duplication
	 * @return string
	 */
	protected function getDuplicateName($name = "", $num = 1)
	{
		$concatName = $name . " (" . $num . ")";
		$alreadyExists = $this->ormName::find("name", $concatName);
		if (count($alreadyExists)) {
			return $this->getDuplicateName($name, $num += 1);
		}
		return $concatName;
	}

	/**
	 * Retourne la liste des proprietes a ignorer pour le toArray
	 *
	 * @return array
	 */
	protected static function getIgnorePropsToArray()
	{
		return array_merge(parent::IGNORE_PROPS_TOARRAY, static::IGNORE_PROPS_TOARRAY);
	}

	/**
	 * Retourne une instance de la classe et la stocke dans la static $instance
	 *
	 * @param integer $id
	 * @return static
	 */
	public static function getInstance(int $id = 0)
	{
		static::$instances[static::class] = static::$instances[static::class] ?? [];

		if (isset(static::$instances[static::class][$id])) {
			$instance = static::$instances[static::class][$id];
		} else {
			$instance = new static($id);
			static::$instances[static::class][$id] = $instance;
		}

		return $instance;
	}

	public function export()
	{
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $this->name . '.dsi"');

		$this->id = 0;
		$this->numModel = 0;
		
		echo serialize(\encoding_normalize::utf8_normalize($this));
	}

	/**
	 * Réinitialise les tags puis ajoute les tags du modèle à l'entité
	 *
	 * @return void
	 */
	public function importModelTags()
	{
		//On enlève les tags existants
		$this->removeEntityTags();
		$this->tags = array();

		if ($this->numModel) {
			if (method_exists(static::class, 'getInstance')) {
				$model = static::getInstance($this->numModel);
			} else {
				$model = new static($this->numModel);
			}
			foreach ($model->tags as $tag) {
				$this->linkTag($tag->id, $this->id);
				$this->tags[] = $tag;
			}
		}
	}
}