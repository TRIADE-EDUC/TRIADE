<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: LayoutNodeModel.php,v 1.14 2023/02/13 14:14:42 qvarin Exp $
namespace Pmb\CMS\Models;

class LayoutNodeModel extends PortalRootModel implements TreeInterfaceModel
{

	public const ROOT_CONTAINER_ID = "container";

	protected $name = null;

	/**
	 * Utilise pour l'interface pour savoir si la zone est modifie
	 *
	 * @var bool
	 */
	protected $isEdited = false;

	/**
	 *
	 * @var LayoutContainerModel|LayoutElementModel[]
	 */
	protected $children = array();

	/**
	 * Permet de retourner les enfants
	 *
	 * {@inheritdoc}
	 * @see \Pmb\CMS\Models\TreeInterfaceModel::getChildren()
	 */
	public function getChildren(): array
	{
		return $this->children;
	}

	/**
	 * Permet de passer isEdited a vrai
	 */
	public function isEdited()
	{
	    $this->isEdited = true;
	}

	/**
	 * Ne fait rien, utilise la fonction isEdited()
	 * Methode cree pour pas que le serialize modifie la valeur
	 *
	 * @param bool $value
	 */
	public function setEdited($value)
	{}

	/**
	 *
	 * @param int $index
	 * @param LayoutContainerModel|LayoutElementModel $child
	 */
	public function replaceChild(int $index, $child)
	{
		$this->children[$index] = $child;
	}

	/**
	 *
	 * @param LayoutContainerModel|LayoutElementModel $child
	 */
	public function appendChild($child)
	{
		$this->children[] = $child;
	}

	/**
	 *
	 * @param int $currentIndex
	 * @param int $beforeIndex
	 */
	public function moveChildBefore(int $currentIndex, int $beforeIndex)
	{
		$count = count($this->getChildren()) ?? 0;
		if ($count == 0 || $currentIndex < 0 || $currentIndex >= $count) {
			return false;
		}

		if ($beforeIndex <= 0)
			$beforeIndex = 0;
		if ($beforeIndex >= $count)
			$beforeIndex = $count;

		if ($currentIndex == $beforeIndex) {
			return false;
		}

		// On duplique l'enfant à son nouvel emplacement
		array_splice($this->children, $beforeIndex, 0, [
			$this->children[$currentIndex]
		]);
		// On recalcul l'ancien emplacement
		$oldIndex = ($currentIndex > $beforeIndex) ? $currentIndex + 1 : $currentIndex;
		// On le supprime de son ancien emplacement
		array_splice($this->children, $oldIndex, 1);
		return true;
	}

	/**
	 *
	 * @param int $index
	 * @param LayoutContainerModel|LayoutElementModel $child
	 */
	public function insert(int $index, $child)
	{
		array_splice($this->children, $index, 0, [
			$child
		]);
	}

	/**
	 *
	 * @return LayoutElementModel[]
	 */
	public function getAllFrames(): array
	{
		$frames = array();
		foreach ($this->getChildren() as $child) {
			if ($child instanceof LayoutContainerModel) {
				$frames = array_merge($frames, $child->getAllFrames());
			} else {
				array_push($frames, $child);
			}
		}
		return $frames;
	}

	/**
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function removeZone(int $id)
	{
		foreach ($this->getChildren() as $key => $child) {
			if ($child instanceof LayoutContainerModel) {
				if ($child->id == $id) {
					array_splice($this->children, $key, 1);
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
	 * @param int $id
	 * @return boolean
	 */
	public function removeFrame(int $id): bool
	{
		foreach ($this->getChildren() as $key => $child) {
			if ($child instanceof LayoutElementModel && $child->id == $id) {
				array_splice($this->children, $key, 1);
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
	 * @param int $id
	 */
	public function removeCMSFrame(string $idTag)
	{
		$frames = array_filter($this->getAllFrames(), function (LayoutElementModel $frame) use ($idTag) {
			return $frame->getSemantic()->getIdTag() == $idTag;
		});
		
			
		foreach ($frames as $frame) {
			$this->removeFrame($frame->getId());
		}
	}

	/**
	 *
	 * @param string $idTag
	 * @return boolean
	 */
	public function removeElementByIdTag(string $idTag)
	{
		foreach ($this->getChildren() as $key => $child) {
			if ($child->getSemantic()->getIdTag() == $idTag) {
				array_splice($this->children, $key, 1);
				return true;
			}

			if ($child instanceof LayoutContainerModel && $child->removeElementByIdTag($idTag)) {
				return true;
			}
		}
		return false;
	}

	/**
	 *
	 * @param string $idTag
	 * @return LayoutElementModel|LayoutContainerModel|NULL
	 */
	public function getElementByIdTag(string $idTag)
	{
		$children = $this->getChildren();
		$index = count($children);
		for ($i = 0; $i < $index; $i ++) {
			if ($idTag == $children[$i]->getSemantic()->getIdTag()) {
				return $children[$i];
			}
			if ($children[$i] instanceof LayoutContainerModel) {
				$find = $children[$i]->getElementByIdTag($idTag);
				if (! empty($find)) {
					return $find;
				}
			}
		}
		return null;
	}

	/**
	 *
	 * @return LayoutContainerModel[]
	 */
	public function getAllZones(): array
	{
		$zones = array();
		$children = $this->getChildren();
		$index = count($children);
		for ($i = 0; $i < $index; $i ++) {
			if ($children[$i] instanceof LayoutContainerModel) {
				if (! $children[$i]->isDeleted) {
					$zones = array_merge($zones, $children[$i]->getAllZones());
					$zones[] = $children[$i];
				}
			}
		}
		return $zones;
	}

	/**
	 *
	 * @return \Pmb\CMS\Models\FrameOpacModel[]|\Pmb\CMS\Models\ZoneOpacModel[]
	 */
	public function getChildrenWithOpacElements()
	{
		$elements = array();
		foreach ($this->getChildren() as $child) {
			if ($child instanceof LayoutElementModel) {
				if ($child instanceof FrameOpacModel) {
					$elements[] = $child;
				}
			} elseif (! empty($child->getChildrenWithOpacElements()) || $child instanceof ZoneOpacModel) {
				$elements[] = $child;
			}
		}
		return $elements;
	}

	/**
	 *
	 * @param string $childrenIdTag
	 * @return LayoutContainerModel|null
	 */
	public function getParentByChildrenIdTag(string $childrenIdTag)
	{
		foreach ($this->getChildren() as $child) {

			if ($child->getSemantic()->getIdTag() == $childrenIdTag) {
				return $this;
			}

			if ($child instanceof LayoutContainerModel) {
				$parent = $child->getParentByChildrenIdTag($childrenIdTag);
				if (! empty($parent)) {
					return $parent;
				}
			}
		}
		return null;
	}
}