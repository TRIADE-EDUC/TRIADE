<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: LayoutModel.php,v 1.11 2023/02/13 14:14:42 qvarin Exp $
namespace Pmb\CMS\Models;

class LayoutModel extends LayoutNodeModel
{

	public const CLONE_CHILDREN = true;

	public const NOT_CLONE_CHILDREN = false;

	public const CHECK_IN_TREE = true;

	public const NOT_CHECK_TREE = false;

	public const CHECK_IN_LAYOUT = true;

	public const NOT_CHECK_LAYOUT = false;

	public const TRANSFER_UNAVAILABLE = false;
	
	public const TRANSFER_AVAILABLE = true;

	public const NO_HERITAGE = false;

	public static $nbInstance = 0;

	/**
	 *
	 * @var PageLayoutModel|GabaritLayoutModel[]
	 */
	public static $instances = array();

	/**
	 * Heritage actuel (null pour aucun heritage)
	 *
	 * @var LayoutModel
	 */
	public $legacyLayout = null;

	/**
	 * Contient les modifications pour chaque mise en page
	 *
	 * en fonction de l'héritage
	 */
	public $layouts = [];

	/**
	 * Retourne la liste des heritage
	 *
	 * @return array
	 */
	public function getLayoutsList(): array
	{
		global $msg;

		$layouts = array();
		$layouts["children"] = $msg['portal_heritage_layout_default'];
		foreach (array_keys($this->layouts) as $key) {
			$explode = explode("_", $key);
			if (empty($explode[0]) || empty($explode[1])) {
				continue;
			}

			$class = $explode[0];
			$id = intval($explode[1]);
			if ($class::exist($id)) {
				$instance = $class::getInstance($id);
				if ($instance instanceof PageLayoutModel) {
					$layouts[$key] = sprintf($msg['portal_heritage_layout'], $instance->getPage()->name);
				} elseif ($instance instanceof GabaritLayoutModel) {
					$layouts[$key] = sprintf($msg['portal_heritage_layout'], $instance->name);
				}
			}
		}
		return $layouts;
	}

	/**
	 * Permet de savoir l'index de l'heritage actuel pour la propriete layouts
	 *
	 * @return boolean|string
	 */
	public function getIndexLayouts()
	{
		if (empty($this->legacyLayout)) {
			return self::NO_HERITAGE;
		}
		return get_class($this->legacyLayout) . "_" . $this->legacyLayout->getId();
	}

	/**
	 * Permet d'initialiser l'héritage si ne n'est pas deja fait
	 *
	 * @return \Pmb\CMS\Models\LayoutModel
	 */
	public function initLegacy()
	{
		if ($this->getIndexLayouts() && empty($this->layouts[$this->getIndexLayouts()])) {
			$this->layouts[$this->getIndexLayouts()] = array();
		}
		return $this;
	}

	/**
	 * Permet de remettre à zero une mise en page
	 *
	 * @param string $layout
	 * @return \Pmb\CMS\Models\LayoutModel
	 */
	public function resetLayout(string $layout)
	{
		if ("children" == $layout) {
			$this->children = array();
		} else {
			$this->removeLayout($layout);
		}
	}

	/**
	 * Permet de supprimer un heritage utile pour remettre à zero une mise en page
	 *
	 * @param string $layout Correspond au retour de la methode getIndexLayouts()
	 * @return \Pmb\CMS\Models\LayoutModel
	 */
	protected function removeLayout(string $layout)
	{
		if (isset($this->layouts[$layout])) {
			unset($this->layouts[$layout]);
		}
		return $this;
	}

	/**
	 * Permet de retourner les enfants (prend en compte l'héritage)
	 *
	 * {@inheritdoc}
	 * @see \Pmb\CMS\Models\LayoutNodeModel::getChildren()
	 */
	public function getLayout(): array
	{
		if (self::NO_HERITAGE === $this->getIndexLayouts()) {
			// Aucun hériage on retourne children
			return $this->children;
		}
		return $this->initLegacy()->layouts[$this->getIndexLayouts()];
	}

	/**
	 * Permet de remplacer un enfants en fonction de son index
	 *
	 * @param int $index
	 * @param LayoutContainerModel|LayoutElementModel $child
	 */
	public function replaceChild(int $index, $child)
	{
		if (self::NO_HERITAGE === $this->getIndexLayouts()) {
			$this->children[$index] = $child;
		} else {
			$this->layouts[$this->getIndexLayouts()][$index] = $child;
		}
	}

	/**
	 * Permet de déplacer un enfant avant un autre enfants
	 *
	 * @param int $currentIndex Index de l'enfant a deplacer
	 * @param int $beforeIndex Index de l'enfant de l'autre enfants
	 */
	public function moveChildBefore(int $currentIndex, int $beforeIndex)
	{
		$children = $this->getLayout();
		$count = count($children) ?? 0;
		if ($count == 0 || $currentIndex < 0 || $currentIndex >= $count) {
			return false;
		}

		if ($beforeIndex <= 0) {
			$beforeIndex = 0;
		}
		if ($beforeIndex >= $count) {
			$beforeIndex = $count;
		}

		if ($currentIndex == $beforeIndex) {
			return false;
		}

		// On duplique l'enfant à son nouvel emplacement
		$this->splice($beforeIndex, 0, [
			$children[$currentIndex]
		]);

		// On recalcul l'ancien emplacement
		$oldIndex = ($currentIndex > $beforeIndex) ? $currentIndex + 1 : $currentIndex;

		// On le supprime de son ancien emplacement
		$this->splice($oldIndex, 1);
		return true;
	}

	/**
	 * Permet d'ajouter une zone/cadre dans la mise en page
	 *
	 * @param LayoutContainerModel|LayoutElementModel $child
	 * @return LayoutContainerModel|LayoutElementModel
	 */
	public function appendChild($child)
	{
		$child = $this->initLegacy()->mergeChild($child, self::CLONE_CHILDREN);
		if (self::NO_HERITAGE === $this->getIndexLayouts()) {
			$this->children[] = $child;
		} else {
			$this->layouts[$this->getIndexLayouts()][] = $child;
		}
		return $child;
	}

	/**
	 * Permet d'inserer un enfant en fonction d'un index
	 *
	 * @param int $index
	 * @param LayoutContainerModel|LayoutElementModel $child
	 */
	public function insert(int $index, $child)
	{
		$child = $this->initLegacy()->mergeChild($child, self::CLONE_CHILDREN);
		$this->splice($index, 0, [
			$child
		]);
		return $child;
	}

	/**
	 * Efface/remplace une portion du tableau des enfants
	 *
	 * @param int $offset
	 * @param int $length
	 * @param mixed $replace
	 */
	protected function splice(int $offset, $length = null, $replace = null)
	{
		if (self::NO_HERITAGE === $this->getIndexLayouts()) {
			array_splice($this->children, $offset, $length, $replace);
		} else {
			array_splice($this->layouts[$this->getIndexLayouts()], $offset, $length, $replace);
		}
	}

	/**
	 * Permet de cloner l'enfant ajoute et recupere les enfants modifies pour une zone
	 *
	 * @param LayoutContainerModel|LayoutElementModel $child
	 * @param boolean $clone
	 * @return LayoutContainerModel|LayoutElementModel
	 */
	private function mergeChild($child, bool $clone = self::CLONE_CHILDREN)
	{
		$idTag = $child->getSemantic()->getIdTag();
		if ($clone === self::CLONE_CHILDREN && empty($this->getElementByIdTag($idTag))) {
			$child = clone $child;
		}
		if ($child instanceof LayoutContainerModel) {
			$child = $this->mergeChildrenInContainer($child);
		}
		return $child;
	}

	/**
	 * Permet de fusionner les enfants de la zone s'il y en a qui on ete modifie
	 *
	 * @param LayoutContainerModel $zone
	 * @return LayoutElementModel|LayoutContainerModel
	 */
	private function mergeChildrenInContainer(LayoutContainerModel $zone)
	{
		$layout = $this->getLayout();
		$index = count($layout);
		for ($i = 0; $i < $index; $i ++) {
			/**
			 *
			 * @var LayoutElementModel|LayoutContainerModel $element
			 */
			$element = $layout[$i];
			if ($element->getSemantic()->getIdTag() == $zone->getSemantic()->getIdTag()) {
				$this->splice($i, 1);
				return $element;
			}
		}

		$children = $zone->getChildren();
		$index = count($children);
		for ($i = 0; $i < $index; $i ++) {
			if ($children[$i] instanceof LayoutContainerModel) {
				$childUpdate = $this->mergeChildrenInContainer($children[$i]);
				$zone->replaceChild($i, $childUpdate);
			}
		}
		return $zone;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Pmb\CMS\Models\LayoutNodeModel::removeFrame()
	 */
	public function removeFrame(int $id): bool
	{
		$instanceFrame = LayoutElementModel::getInstance($id);
		$idTag = $instanceFrame->getSemantic()->getIdTag();

		if (empty($this->getParentByChildrenIdTag($idTag))) {
			// On n'a pas le parent dans la layout
			// donc on vas l'ajouter pour sauvegarder la suppression
			$parent = $this->legacyLayout->getParentByChildrenIdTag($idTag);
			$parent = $this->appendChild($parent);
			// Le append fait un clone donc il faut aller chercher son nouvel identifiant
			$id = $parent->getElementByIdTag($idTag)->getId();
		}

		foreach ($this->getLayout() as $key => $child) {
			if ($child instanceof LayoutElementModel && $child->id == $id) {
				$this->splice($key, 1);
				return true;
			}

			if ($child instanceof LayoutContainerModel && $child->removeFrame($id)) {
				return true;
			}
		}
		return false;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Pmb\CMS\Models\LayoutNodeModel::removeZone()
	 */
	public function removeZone($id)
	{
		$instanceZone = LayoutContainerModel::getInstance($id);
		$idTag = $instanceZone->getSemantic()->getIdTag();

		if (empty($this->getParentByChildrenIdTag($idTag))) {
			// On n'a pas le parent dans la layout
			// donc on vas l'ajouter pour sauvegarder la suppression
			$parent = $this->legacyLayout->getParentByChildrenIdTag($idTag);
			$parent = $this->appendChild($parent);
			// Le append fait un clone donc il faut aller chercher son nouvel identifiant
			$id = $parent->getElementByIdTag($idTag)->getId();
		}

		foreach ($this->getLayout() as $key => $child) {
			if ($child instanceof LayoutContainerModel) {
				if ($child->id == $id) {
					$this->splice($key, 1);
					return true;
				}
				if ($child->removeZone($id)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Pmb\CMS\Models\LayoutNodeModel::getParentByChildrenIdTag()
	 */
	public function getParentByChildrenIdTag(string $childrenIdTag)
	{
		foreach ($this->getLayout() as $element) {
			if ($element->getSemantic()->getIdTag() == $childrenIdTag) {
				if ($this instanceof LayoutContainerModel) {
					return $this;
				}
				return null;
			}
			if ($element instanceof LayoutContainerModel) {
				$parent = $element->getParentByChildrenIdTag($childrenIdTag);
				if (! empty($parent)) {
					return $parent;
				}
			}
		}
		return null;
	}

	/**
	 *
	 *
	 * @param string $id_tag
	 * @param boolean $checkInPageLayout
	 * @return NULL|LayoutContainerModel
	 */
	public function getParentByIdTag(string $id_tag, bool $checkInLayout = self::CHECK_IN_LAYOUT)
	{
		$parent = null;
		if ($checkInLayout && ! empty($this->getLayout())) {
			$parent = $this->getElementByIdTag($id_tag);
		}
		if (empty($parent)) {
			$tree = $this->generateTree();
			$parent = ($id_tag == LayoutNodeModel::ROOT_CONTAINER_ID) ? $tree : $tree->getElementByIdTag($id_tag);
		}
		return $parent;
	}

	/**
	 * Permet de genere la mise en page complete
	 *
	 * @throws \Exception
	 * @return LayoutContainerModel
	 */
	public function generateTree(): LayoutContainerModel
	{
		$tree = null;
		
		if ($this->getIndexLayouts() !== LayoutModel::NO_HERITAGE) {
		    // On construit l'arbre du parent
			$tree = $this->legacyLayout->generateTree();
		} else {
			$tree = $this->getElementByIdTag("container");
			if (empty($tree)) {
				throw new \Exception("Root container not found");
			}
		}

		if (empty($tree) || $tree->getSemantic()->getIdTag() != LayoutNodeModel::ROOT_CONTAINER_ID) {
			throw new \Exception("Root container not found");
		}

	    if ($this->getIndexLayouts() !== LayoutModel::NO_HERITAGE && ! empty($this->getLayout())) {
		    // On fait un duplication de $tree pour éviter de le répercuter sur toutes pages/modèles
			$tree = $this->updateTree(clone $tree, $this->getLayout());
		}
		return $tree;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Pmb\CMS\Models\LayoutNodeModel::getAllFrames()
	 */
	public function getAllFrames(): array
	{
		return $this->generateTree()->getAllFrames();
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Pmb\CMS\Models\LayoutNodeModel::getAllZones()
	 */
	public function getAllZones(): array
	{
		$container = $this->generateTree();
		$zones = $container->getAllZones();
		$zones[] = $container;
		return $zones;
	}

	/**
	 *
	 * @param LayoutContainerModel $zone
	 * @param array $childrenLayout
	 * @return \Pmb\CMS\Models\LayoutElementModel|\Pmb\CMS\Models\LayoutContainerModel
	 */
	private function updateTree(LayoutContainerModel $zone, array $childrenLayout)
	{
		$index = count($childrenLayout);
		for ($i = 0; $i < $index; $i ++) {
			if ($childrenLayout[$i]->getSemantic()->getIdTag() == $zone->getSemantic()->getIdTag()) {
			    $childrenLayout[$i]->isEdited();
				return $childrenLayout[$i];
			}
		}

		$children = $zone->getChildren();
		$index = count($children);
		for ($i = 0; $i < $index; $i ++) {
			if ($children[$i] instanceof LayoutContainerModel) {
				$childUpdate = $this->updateTree($children[$i], $childrenLayout);
				$zone->replaceChild($i, $childUpdate);
			}
		}
		return $zone;
	}
	
	/**
	 * Permet d'aller chercher un noeud avec un identifiant
	 *
	 * @param string $idTag Identifiant du noeud
	 * @param string $checkTree Permet de construire completement la mise en page pour chercher le noeud
	 * @return LayoutElementModel|LayoutContainerModel|NULL
	 */
	public function getElementByIdTag(string $idTag, $checkTree = self::NOT_CHECK_TREE)
	{
		$element = null;
		if ($checkTree === self::CHECK_IN_TREE) {
			$tree = $this->generateTree();
			$element = $idTag == LayoutNodeModel::ROOT_CONTAINER_ID ? $tree : $tree->getElementByIdTag($idTag);
		}
		if (empty($element)) {
			$children = $this->getLayout();
			$index = count($children);
			for ($i = 0; $i < $index; $i ++) {
				if ($idTag == $children[$i]->getSemantic()->getIdTag()) {
					$element = $children[$i];
					break;
				}
				if ($children[$i] instanceof LayoutContainerModel) {
					$find = $children[$i]->getElementByIdTag($idTag);
					if (! empty($find)) {
						$element = $find;
						break;
					}
				}
			}
		}
		return $element;
	}
	
	/**
	 * Permet de supprimer un noeud en fonction de son identifiant
	 *
	 * @param string $idTag
	 * @return boolean
	 */
	public function removeElementByIdTag(string $idTag)
	{
		foreach ($this->getLayout() as $key => $child) {
			if ($child->getSemantic()->getIdTag() == $idTag) {
				$this->splice($key, 1);
				return true;
			}
			
			if ($child instanceof LayoutContainerModel && $child->removeElementByIdTag($idTag)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Permet de partager une modification sur tout les héritage définie dans $layouts
	 *
	 * @param string $idTag
	 */
	public function shareLayout(string $idTag)
	{
		global $msg;
		
		$layoutContainer = $this->getElementByIdTag($idTag);
		if (!($layoutContainer instanceof LayoutContainerModel)) {
			return ["error" => true, "errorMessage" => $msg['portal_zone_required']];
		}
		
		if ($layoutContainer instanceof ZoneCMSModel) {
			// Zone CMS on regarde le parent
			$currentParent = $this->getParentByChildrenIdTag($idTag);
			if (!($currentParent instanceof ZoneOpacModel)) {
				// Zone parente n'est pas une zone OPAC on ne vas pas plus loin
				return ["error" => true, "errorMessage" => $msg['portal_zone_parent_not_zone_opac']];
			}
		}
		
		$details = [];
		foreach ($this->layouts as $key => $layout) {
		    $explode = explode("_", $key);
		    $class = $explode[0];
		    $id = intval($explode[1]);
		    
		    if (!$class::exist($id)) {
		        // Gabarit/PageLayout existe plus ou pas
		        continue;
		    }
		    
			if ($this->getIndexLayouts() == $key) {
				// On ne reporte pas dans le même layout
				continue;
			}
			
			$instance = $class::getInstance($id);
			
			$success = false;
			if ($layoutContainer instanceof ZoneCMSModel) {
				$parentLayout = $this->getElementFromLayout($currentParent->getSemantic()->getIdTag(), $key);
				if (empty($parentLayout)) {
					continue;
				}
				if (self::TRANSFER_AVAILABLE == $this->repercuteLayout($layout, $currentParent)) {
					// Le parent n'a jamais été déviré donc on le dérive
					$parentLayout = clone $parentLayout;
					$this->layouts[$key][] = $parentLayout;
				}
				$parentLayout->appendChild(clone $layoutContainer);
				$success = true;
			} elseif (self::TRANSFER_AVAILABLE == $this->repercuteLayout($layout, $layoutContainer)) {
				$this->layouts[$key][] = clone $layoutContainer;
				$success = true;
			}
			
			if ($instance instanceof PageLayoutModel) {
			    $details[sprintf($msg['portal_heritage_layout'], $instance->getPage()->name)] = $success;
			} elseif ($instance instanceof GabaritLayoutModel) {
			    $details[sprintf($msg['portal_heritage_layout'], $instance->name)] = $success;
			}
		}
		if ($this->getIndexLayouts() !== false) {
			$success = false;
			if ($layoutContainer instanceof ZoneCMSModel) {
				$parentLayout = $this->getElementFromLayout($currentParent->getSemantic()->getIdTag());
				if (!empty($parentLayout)) {
					$parentLayout->appendChild(clone $layoutContainer);
					$success = true;
				}
			} else if (self::TRANSFER_AVAILABLE == $this->repercuteLayout($this->children, $layoutContainer)) {
				$this->children[] = clone $layoutContainer;
				$success = true;
			}
			$details[$msg['portal_heritage_layout_default']] = $success;
		}
		
		return [
			"details" => $details,
			"error" => false,
			"errorMessage" => ""
		];
	}
	
	/**
	 * Permet de repercuter une modification dans la mise en page (appeler par shareLayout)
	 *
	 * @param array $layout
	 * @param LayoutContainerModel $layoutContainer
	 * @return bool
	 */
	protected function repercuteLayout(array $layout, LayoutContainerModel $layoutContainer): bool
	{
		/**
		 * @var LayoutContainerModel $zone
		 */
		foreach ($layout as $zone) {
			if ($zone->getSemantic()->getIdTag() == $layoutContainer->getSemantic()->getIdTag()) {
				return self::TRANSFER_UNAVAILABLE;
			}
			
			if ($zone->getElementByIdTag($layoutContainer->getSemantic()->getIdTag())) {
				return self::TRANSFER_UNAVAILABLE;
			}
			
			foreach ($layoutContainer->getChildren() as $child) {
				if ($child instanceof LayoutElementModel) {
					if ($zone->getElementByIdTag($child->getSemantic()->getIdTag())) {
						return self::TRANSFER_UNAVAILABLE;
					}
				} elseif (self::TRANSFER_UNAVAILABLE == $this->repercuteLayout($layout, $child)) {
					return self::TRANSFER_UNAVAILABLE;
				}
			}
		}
		return self::TRANSFER_AVAILABLE;
	}
	
	/**
	 * Retourne un element en fonction de sont idtag pour une mise en page donné
	 *
	 * @param string $idTag
	 * @param string $layoutIndex
	 * @return LayoutContainerModel|LayoutElementModel|NULL
	 */
	protected function getElementFromLayout(string $idTag, string $layoutIndex = "")
	{
		if (empty($layoutIndex)) {
			$layout = $this->children;
			$legacyLayout = null;
		} else {
			$layout = $this->layouts[$layoutIndex] ?? [];
			
			$explode = explode("_", $layoutIndex);
			$classname = $explode[0];
			$id = intval($explode[1]);
			
			$legacyLayout = $classname::getInstance($id);
		}
		
		foreach ($layout as $element) {
			
			if ($element->getSemantic()->getIdTag() == $idTag) {
				return $element;
			}
			
			if ($element instanceof LayoutContainerModel) {
				$elementFound = $element->getElementByIdTag($idTag);
				if (! empty($elementFound)) {
					return $elementFound;
				}
			}
		}
		
		$elementFound = null;
		if ($legacyLayout) {
			$elementFound = $legacyLayout->getElementFromLayout($idTag, $legacyLayout->getIndexLayouts());
		}
		return $elementFound;
	}
	
	public static function destroyFrame(string $idTag)
	{
		foreach (static::$instances as $instance) {
			
			$backupHeritage = $instance->legacyLayout;
			
			if ($instance instanceof GabaritLayoutModel) {
				// Cas sans héritage
				$instance->legacyLayout = null;
				$instance->removeCMSFrame($idTag);
			}
			
			// Cas avec héritage
			$heritage = !empty($instance->layouts) ? array_keys($instance->layouts) : [];
			foreach ($heritage as $key) {
				$explode = explode("_", $key);
				if (empty($explode[0]) || empty($explode[1])) {
					continue;
				}
				
				$class = $explode[0];
				$id = intval($explode[1]);
				if ($class::exist($id)) {
					$heritageInstance = $class::getInstance($id);
					$heritageInstance->removeCMSFrame($idTag);
					
					$instance->legacyLayout = $heritageInstance;
					$instance->removeCMSFrame($idTag);
				}
			}
			
			$instance->legacyLayout = $backupHeritage;
		}
	}
	
	/**
	 *
	 * @param int $id
	 */
	public function removeCMSFrame(string $idTag)
	{
		foreach ($this->getLayout() as $child) {
			if ($child instanceof LayoutContainerModel) {
				$child->removeCMSFrame($idTag);
			} elseif ($child->getSemantic()->getIdTag() == $idTag) {
				$this->removeFrame($child->getId());
			}
		}
	}
}
