<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PageLayoutModel.php,v 1.7 2023/02/13 14:14:42 qvarin Exp $
namespace Pmb\CMS\Models;

class PageLayoutModel extends LayoutModel
{
	
	/**
	 * Permet de genere la mise en page complete
	 *
	 * @throws \Exception si aucun heritage
	 * @return LayoutContainerModel
	 */
	public function generateTree(): LayoutContainerModel
	{
		if (empty($this->legacyLayout)) {
			throw new \Exception("Aucun héritage !");
		}
		return parent::generateTree();
	}
	
	/**
	 * Permet de savoir l'index de l'heritage actuel pour la propriete layouts
	 *
	 * @throws \Exception si aucun heritage
	 * @return boolean|string
	 */
	public function getIndexLayouts()
	{
		if (empty($this->legacyLayout)) {
			throw new \Exception("Aucun héritage !");
		}
		return get_class($this->legacyLayout) . "_" . $this->legacyLayout->getId();
	}
	
	/**
	 * Permet de remettre à zero une mise en page
	 *
	 * @return \Pmb\CMS\Models\LayoutModel
	 */
	public function resetLayout(string $layout)
	{
		$this->removeLayout($layout);
	}
	
	/**
	 * Retourne la liste des heritage
	 *
	 * @return array
	 */
	public function getLayoutsList(): array
	{
		global $msg;
		
		$layouts = array();
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
	 *
	 * @return PageModel|NULL
	 */
	public function getPage()
	{
		foreach ($this->portal->getPages() as $page) {
			if (empty($page->getPageLayout())) {
				continue;
			}

			if ($page->getPageLayout()->getId() == $this->getId()) {
				return $page;
			}
		}
		return null;
	}
}