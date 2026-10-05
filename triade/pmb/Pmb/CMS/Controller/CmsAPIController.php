<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CmsAPIController.php,v 1.47.2.1 2024/09/26 10:29:15 tsamson Exp $
namespace Pmb\CMS\Controller;

use Pmb\CMS\Models\PortalModel;
use Pmb\CMS\Models\PagePortalModel;
use Pmb\CMS\Models\GabaritLayoutModel;
use Pmb\CMS\Models\PageModel;
use Pmb\CMS\Models\LayoutContainerModel;
use Pmb\CMS\Models\FrameCMSModel;
use Pmb\CMS\Models\LayoutElementModel;
use Pmb\CMS\Models\PageLayoutModel;
use Pmb\CMS\Models\ZoneCMSModel;
use Pmb\CMS\Models\LayoutModel;
use Pmb\CMS\Orm\PortalOrm;
use Pmb\CMS\Orm\VersionOrm;
use cms_cache;

class CmsAPIController
{
    /**
     *
     * @var array
     */
    protected $data;

    /**
     *
     * @var integer
     */
    protected $version_num;

    /**
     *
     * @var PortalModel
     */
    protected $portal;

    public function __construct(int $version_num = 0, array $data = array())
    {
        $this->version_num = $version_num;
        $this->data = $data;
    }
    
    
    private function getPortal(): PortalModel
    {
        if (! isset($this->portal)) {
            if ($this->version_num == 0) {
                $this->version_num = PortalModel::getIdVersion(PortalModel::getCurrentPortal());
            }
            $this->portal = PortalModel::getPortal($this->version_num);
            
        }
        return $this->portal;
    }
    
    /**
     *
     * @param array $list
     * @return string[]
     */
    private function formatList(array $list): array
    {
        $parsedList = array();
        foreach ($list as $element) {
        	$serialize = $element->serialize();
            if ($element instanceof LayoutModel) {
            	$serialize['layouts_list'] = $element->getLayoutsList();
            }
            if ($element instanceof PageModel) {
            	$serialize['page_layout']['layouts_list'] = $element->getPageLayout()->getLayoutsList();
            }
            if (method_exists($element, "generateTree")) {
                // Le LayoutContainer racine ne doit pas être présent
                $serialize['tree'] = [];
            }
            $parsedList[] = $serialize;
        }
        return $parsedList;
    }
    
    /**
     *
     * @param mixed $result
     */
    private function sendResult($result)
    {
        try {
            ajax_http_send_response(\encoding_normalize::utf8_normalize($result));
        } catch (\Exception $e) {
            $this->sendError($e->getMessage());
        }
    }
    
    /**
     *
     * @param string $error_message
     */
    private function sendError(string $error_message)
    {
        ajax_http_send_response(\encoding_normalize::utf8_normalize([
            'error' => true,
            'errorMessage' => $error_message
        ]));
    }

    public function pageList()
    {
        $this->sendResult($this->formatList($this->getPortal()->getPages()));
    }

    public function gabaritList()
    {
        $this->sendResult($this->formatList($this->getPortal()->getGabaritLayouts()));
    }

    public function frameList()
    {
        $this->sendResult($this->getPortal()->getFrameList());
    }

    public function pageRemove($id)
    {
        if ($this->getPortal()->removePage($id)) {
            $this->sendResult($this->getPortal()->save());
        } else {
            $this->sendError("page not deleted!");
        }
    }

    public function gabaritRemove($id)
    {
        $portal = $this->getPortal();
        if (GabaritLayoutModel::exist($id) && $portal->removeGabarit($id)) {
            
            $pages = $portal->getPages();
            $index = count($pages);
            for ($i = 0; $i < $index; $i++) {
                if ($pages[$i]->getGabaritLayout()->id == $id) {
                    $pages[$i]->setGabarit($portal->getDefaultGabarit());
                }
            }
            
            $this->sendResult($portal->save());
        } else {
            $this->sendError("gabarit not deleted!");
        }
    }

    public function zoneRemove($id)
    {
        if (empty($this->data) || empty($this->data['gabarit'])) {
            return $this->sendError("no data found!");
        }
        $portal = $this->getPortal();
        try {
            $gabarit = GabaritLayoutModel::getInstance($this->data['gabarit']);
            if ($gabarit->removeZone($id)) {
                $this->sendResult($portal->save());
            } else {
                $this->sendError("zone not deleted!");
            }
        } catch (\Exception $e) {
            $this->sendError($e->getMessage());
        }
    }
    
    public function frameRemove(string $idTag)
    {
    	if (empty($idTag)) {
    		return $this->sendError("no data found!");
    	}
    	$portal = $this->getPortal();
    	try {
    		LayoutModel::destroyFrame($idTag);
    		$this->sendResult($portal->save());
    	} catch (\Exception $e) {
    		$this->sendError($e->getMessage());
    	}
    }
    
    public function pageUpdate($id = 0)
    {
        if (empty($this->data)) {
            return $this->sendError("data not found!");
        }

        $portal = $this->getPortal();
        try {
            $create = true;
            if ($id) {
                $create = false;
                $page = PageModel::getInstance($id);
            } else {
                $page = new PagePortalModel($portal, []);
            }
            $page->setDataFromForm($this->data);
            if ($create) {
                $portal->addPage($page);
            }
            $this->sendResult($this->getPortal()->save());
        } catch (\Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function gabaritUpdate($id = 0)
    {
        if (empty($this->data)) {
            return $this->sendError("data not found!");
        }

        // init
        $this->getPortal();

        try {
        	$legacyLayout = null;
        	if (!empty($this->data['gabarit']['legacy_layout']['id']) && !empty($this->data['gabarit']['legacy_layout']['class'])) {
        		$class = $this->data['gabarit']['legacy_layout']['class'];
        		$legacyLayout = $class::getInstance($this->data['gabarit']['legacy_layout']['id']);
        	}
        	
            $create = true;
            if (GabaritLayoutModel::exist($id)) {
                $create = false;
                $gabaritLayout = GabaritLayoutModel::getInstance($id);
            } else {
            	$gabaritLayout = new GabaritLayoutModel($this->getPortal(), []);
            }
            
            $gabaritLayout->setDataFromForm($this->data['gabarit'], $legacyLayout);
            if ($create) {
                $this->getPortal()->addGabarit($gabaritLayout);
            }
            

            $gabaritLayout->disassociatedPages();
            if (! empty($this->data['pages'])) {
                $gabaritLayout->associatedPages($this->data['pages']);
            }
            
            $this->sendResult($this->getPortal()->save());
        } catch (\Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function zoneUpdate($id)
    {
        try {
            $zone = LayoutContainerModel::getInstance($id);
            foreach ($this->data as $prop => $value) {
                $zone->{$prop} = $value;
            }
            $this->sendResult($this->getPortal()->save());
        } catch (\Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function gabaritFrameList($id)
    {
        $this->getPortal();
        if (! GabaritLayoutModel::exist($id)) {
            return $this->sendResult([]);
        }
        return $this->sendResult($this->formatList(GabaritLayoutModel::getInstance($id)->getAllFrames()));
    }

    public function pageFrameList($id)
    {
        $this->getPortal();

        if (! PageModel::exist($id)) {
            return $this->sendResult([]);
        }

        try {
            $page = PageModel::getInstance($id);
            $frames = $page->generateTree()->getAllFrames();
        } catch (\Exception $e) {
            $frames = [];
        }

        if (empty($frames)) {
            return $this->sendResult([]);
        }
        return $this->sendResult($this->formatList($frames));
    }

    public function pageZoneList($id)
    {
        $this->getPortal();
        if (! PageModel::exist($id)) {
            return $this->sendResult([]);
        }
        try {
            $tree = PageModel::getInstance($id)->generateTree();
            $zones = $tree->getAllZones();
            $zones[] = $tree;
        } catch (\Exception $e) {
            $zones = [];
        }

        if (empty($zones)) {
            return $this->sendResult([]);
        }
        return $this->sendResult($this->formatList($zones));
    }

    public function gabaritZoneList($id)
    {
        $this->getPortal();
        if (! GabaritLayoutModel::exist($id)) {
            return $this->sendResult([]);
        }
        try {
            $tree = GabaritLayoutModel::getInstance($id)->generateTree();
            $zones = $tree->getAllZones();
            $zones[] = $tree;
        } catch (\Exception $e) {
            $zones = [];
        }

        if (empty($zones)) {
            return $this->sendResult([]);
        }
        return $this->sendResult($this->formatList($zones));
    }

    public function pageClearCache($id)
    {
        global $msg;

        $this->getPortal();
        $page = PageModel::getInstance($id);
        $frames = $page->generateTree()->getAllFrames();
        $index = count($frames);
        for ($i = 0; $i < $index; $i ++) {
            if ($frames[$i] instanceof FrameCMSModel) {
                $frames[$i]->clearCache();
            }
        }
        return $this->sendResult([
            "msg" => $msg['page_clean_cache_done']
        ]);
    }

    public function frameClearCache($id)
    {
        global $msg;
        $hash = FrameCMSModel::getHashCadre($id);
        if (! empty($hash)) {
            pmb_mysql_query("DELETE FROM cms_cache_cadres WHERE cache_cadre_hash ='$hash'");
        }
        return $this->sendResult([
            "msg" => $msg['frame_clean_cache_done']
        ]);
    }

    public function portalClearCache()
    {
        global $msg;
        
        cms_cache::clean_cache();
        return $this->sendResult([
            "msg" => $msg['frame_clean_cache_done']
        ]);
    }

    public function frameClassement()
    {
        if (empty($this->data)) {
            return $this->sendError("data not found!");
        }

        try {
            // init
            $this->getPortal();
            LayoutElementModel::addFrameClassement($this->data['classement'], $this->data['data']['item']['semantic']['id_tag']);
            $this->sendResult($this->getPortal()->save());
        } catch (\Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function gabaritClassement()
    {
        if (empty($this->data)) {
            return $this->sendError("data not found!");
        }

        try {
            $this->getPortal();
            $gabaritId = $this->data['data']['item']['id'];
            if (GabaritLayoutModel::exist($gabaritId)) {
                $gabarit = GabaritLayoutModel::getInstance($gabaritId);
                $gabarit->setClassement($this->data['classement']);
                $this->sendResult($this->getPortal()->save());
            } else {
                return $this->sendError("gabarit not found!");
            }
        } catch (\Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function updateTreePage($id)
    {
        if (empty($this->data)) {
            return $this->sendError("data not found!");
        }

        $portal = $this->getPortal();
        $page = PageModel::getInstance($id);
        $pageLayout = $page->getPageLayout();
        if (empty($pageLayout)) {
            $pageLayout = new PageLayoutModel($portal, []);
            $page->setPageLayout($pageLayout);
        }

        $currentParentIdTag = $this->data['parent']['semantic']['id_tag'];
        $currentParent = $pageLayout->getElementByIdTag($currentParentIdTag, LayoutModel::CHECK_IN_TREE);
        if (empty($currentParent)) {
            return $this->sendError("Parent not found!");
        }

        $currentParent = $pageLayout->appendChild($currentParent);
        $children = $currentParent->getChildren();
        if (empty($children) || empty($children[$this->data['index_element']])) {
            return $this->sendError("Children not found!");
        }

        if ($currentParentIdTag != $this->data['new_parent']) {
            $childMoved = $children[$this->data['index_element']];
            // On supprime l'enfant de l'ancienne zone
            if ($childMoved instanceof LayoutContainerModel) {
                $currentParent->removeZone($childMoved->getId());
            } else {
                $currentParent->removeFrame($childMoved->getId());
            }

            // On vas chercher le nouveau parent
            $newParent = $pageLayout->getElementByIdTag($this->data['new_parent']);
            if (empty($newParent)) {
                // Le parent n'a jamais ete derive, on l'ajoute
                $newParent = $pageLayout->getElementByIdTag($this->data['new_parent'], LayoutModel::CHECK_IN_TREE);
                $newParent = $pageLayout->appendChild($newParent);
            }

            if (empty($newParent)) {
                return $this->sendError("New parent not found!");
            }
            $newParent->appendChild($childMoved);
        } else {
            $newParent = $currentParent;
        }

        if (isset($this->data['new_index_element']) && is_numeric($this->data['new_index_element'])) {
            $currentIndex = intval($this->data['index_element']);
            $beforeIndex = intval($this->data['new_index_element']);
            $newParent->moveChildBefore($currentIndex, $beforeIndex);
        }

        $this->sendResult($portal->save());
    }

    public function updateTreeGabarit($id)
    {
        if (empty($this->data)) {
            return $this->sendError("data not found!");
        }

        $portal = $this->getPortal();
        $gabarit = GabaritLayoutModel::getInstance($id);

        $checkTree = LayoutModel::CHECK_IN_TREE;
        if (LayoutModel::NO_HERITAGE === $gabarit->getIndexLayouts()) {
        	$checkTree = LayoutModel::NOT_CHECK_TREE;
        }
        
        $currentParentIdTag = $this->data['parent']['semantic']['id_tag'];
        $currentParent = $gabarit->getElementByIdTag($currentParentIdTag, $checkTree);
        if (empty($currentParent)) {
        	return $this->sendError("Parent not found!");
        }
        
        if (LayoutModel::NO_HERITAGE !== $gabarit->getIndexLayouts()) {
	        $currentParent = $gabarit->appendChild($currentParent);
        }
        
        $children = $currentParent->getChildren();
        if (empty($children) || empty($children[$this->data['index_element']])) {
        	return $this->sendError("Children not found!");
        }

        if ($currentParentIdTag != $this->data['new_parent']) {
        	$childMoved = $children[$this->data['index_element']];
        	// On supprime l'enfant de l'ancienne zone
            if ($childMoved instanceof LayoutContainerModel) {
                $currentParent->removeZone($childMoved->getId());
            } else {
                $currentParent->removeFrame($childMoved->getId());
            }

            // On vas chercher le nouveau parent
            $newParent = $gabarit->getElementByIdTag($this->data['new_parent']);
            if (empty($newParent) && LayoutModel::NO_HERITAGE !== $gabarit->getIndexLayouts()) {
                // Le parent n'a jamais ete derive, on l'ajoute
                $newParent = $gabarit->getElementByIdTag($this->data['new_parent'], LayoutModel::CHECK_IN_TREE);
                $newParent = $gabarit->appendChild($newParent);
            }

            if (empty($newParent)) {
                return $this->sendError("New parent not found!");
            }
            $newParent->appendChild($childMoved);
        } else {
            $newParent = $currentParent;
        }

        if (isset($this->data['new_index_element']) && is_numeric($this->data['new_index_element'])) {
            $currentIndex = intval($this->data['index_element']);
            $beforeIndex = intval($this->data['new_index_element']);
            $newParent->moveChildBefore($currentIndex, $beforeIndex);
        }
        
        $this->sendResult($portal->save());
    }

    public function updateTagElementPageLayout($id)
    {
        if (empty($this->data)) {
            return $this->sendError("data not found!");
        }
        $portal = $this->getPortal();
        $page = PageModel::getInstance($id);
        $pageLayout = $page->getPageLayout();
        if (empty($pageLayout)) {
            $pageLayout = new PageLayoutModel($portal, []);
            $page->setPageLayout($pageLayout);
        }
        $currentParentIdTag = $this->data['parent']['semantic']['id_tag'];
        $currentParent = $page->getParentByIdTag($currentParentIdTag);
        $currentParent = $pageLayout->appendChild($currentParent);

        $child = $currentParent->getChildren()[$this->data['index_element']];
        $child->getSemantic()->setTag($this->data['tag_element']);

        $this->sendResult($portal->save());
    }

    public function updateTagElementGabaritLayout($id)
    {
        if (empty($this->data)) {
            return $this->sendError("data not found!");
        }
        
        $portal = $this->getPortal();
        $gabarit = GabaritLayoutModel::getInstance($id);
        
        
        $currentParent = $gabarit->getElementByIdTag($this->data['parent']['semantic']['id_tag']);
        if (empty($currentParent)) {
        	$currentParent = $gabarit->generateTree()->getElementByIdTag($this->data['parent']['semantic']['id_tag']);
        	$currentParent = $gabarit->appendChild($currentParent);
        }

        $child = $currentParent->getChildren()[$this->data['index_element']];
        $child->getSemantic()->setTag($this->data['tag_element']);

        $this->sendResult($portal->save());
    }

    public function hidePageLayoutElement($id)
    {
        if (empty($this->data)) {
            return $this->sendError("data not found!");
        }

        $portal = $this->getPortal();
        $page = PageModel::getInstance($id);
        $pageLayout = $page->getPageLayout();
        if (empty($pageLayout)) {
            $pageLayout = new PageLayoutModel($portal, []);
            $page->setPageLayout($pageLayout);
        }

        $currentParent = $page->getParentByIdTag($this->data['parent']);
        $currentParent = $pageLayout->appendChild($currentParent);

        $elementRemove = $currentParent->getElementByIdTag($this->data['item']['semantic']['id_tag']);
        $elementRemove->isHidden = ! $elementRemove->isHidden;

        $this->sendResult($portal->save());
    }

    public function hideGabaritLayoutElement($id)
    {
        if (empty($this->data)) {
            return $this->sendError("data not found!");
        }

        $portal = $this->getPortal();
        $gabarit = GabaritLayoutModel::getInstance($id);

        $elementRemove = $gabarit->getElementByIdTag($this->data['item']['semantic']['id_tag']);
        if (empty($elementRemove)) {
        	
        	if (LayoutModel::NO_HERITAGE === $gabarit->getIndexLayouts()) {
	        	$currentParent = $gabarit->getParentByChildrenIdTag($this->data['item']['semantic']['id_tag']);
        	} else {
	        	$currentParent = $gabarit->generateTree()->getParentByChildrenIdTag($this->data['item']['semantic']['id_tag']);
        	}
        	if (empty($currentParent)) {
        		return $this->sendError("element not found!");
        	}
        	
        	if (LayoutModel::NO_HERITAGE !== $gabarit->getIndexLayouts()) {
	        	$currentParent = $gabarit->appendChild($currentParent);
        	}
        	$elementRemove = $currentParent->getElementByIdTag($this->data['item']['semantic']['id_tag']);
        }
        $elementRemove->isHidden = ! $elementRemove->isHidden;
        
        $this->sendResult($portal->save());
    }

    public function removePageLayoutElement($id)
    {
        if (empty($this->data) || empty($this->data['item'])) {
            return $this->sendError("data not found!");
        }

        $portal = $this->getPortal();
        $page = PageModel::getInstance($id);
        $pageLayout = $page->getPageLayout();
        if (empty($pageLayout)) {
            $pageLayout = new PageLayoutModel($portal, []);
            $page->setPageLayout($pageLayout);
        }

        if (empty($this->data['parent'])) {
            // generateTree retourne le container clonner
            $currentParent = $page->generateTree()->getParentByChildrenIdTag($this->data['item']['semantic']['id_tag']);
        } else {
            $currentParent = $page->getParentByIdTag($this->data['parent']);
        }
        
        /**
         * appendChild de pageLayout vérifie s'il le contient déjà
         * et réalise un merge des enfants
         */
        $currentParent = $pageLayout->appendChild($currentParent);

        $elementRemove = $currentParent->getElementByIdTag($this->data['item']['semantic']['id_tag']);
        $elementRemove instanceof LayoutContainerModel ? $currentParent->removeZone($elementRemove->getId()) : $currentParent->removeFrame($elementRemove->getId());

        $this->sendResult($portal->save());
    }

    public function zoneClasses($idTag)
    {
        $portal = $this->getPortal();
        $class = $this->data['item']['class'];
        
        /**
         * 
         * @var PageModel|GabaritLayoutModel $instance
         */
        $instance = $class::getInstance($this->data['item']['id']);
        if ($instance instanceof PageModel) {
        	$pageLayout = $instance->getPageLayout();
        	if (empty($pageLayout)) {
        		$pageLayout = new PageLayoutModel($portal, []);
        		$instance->setPageLayout($pageLayout);
        	}
        	
        	$currentParent = $instance->generateTree()->getParentByChildrenIdTag($idTag);
        	$currentParent = $pageLayout->appendChild($currentParent);
        } else {
        	if (LayoutModel::NO_HERITAGE === $instance->getIndexLayouts()) {        		
        		$currentParent = $instance->getParentByChildrenIdTag($idTag);
        	} else {        		
	        	$currentParent = $instance->generateTree()->getParentByChildrenIdTag($idTag);
	        	$currentParent = $instance->appendChild($currentParent);
        	}
        }
        
        $layoutContainerModel = $currentParent->getElementByIdTag($idTag);
        $layoutContainerModel->getSemantic()->removeAllClass();
        
        if (! empty($this->data['classes'])) {
        	$index = count($this->data['classes']);
            for ($i = 0; $i < $index; $i ++) {
            	$layoutContainerModel->getSemantic()->addClass(trim($this->data['classes'][$i]));
            }
        }

        $this->sendResult($portal->save());
    }

    public function frameClasses($idTag)
    {
    	$portal = $this->getPortal();
    	$class = $this->data['item']['class'];

    	/**
    	 *
    	 * @var PageModel|GabaritLayoutModel $instance
    	 */
    	$instance = $class::getInstance($this->data['item']['id']);
    	if ($instance instanceof PageModel) {
    		$pageLayout = $instance->getPageLayout();
    		if (empty($pageLayout)) {
    			$pageLayout = new PageLayoutModel($portal, []);
    			$instance->setPageLayout($pageLayout);
    		}
    		
    		$currentParent = $instance->generateTree()->getParentByChildrenIdTag($idTag);
    		$currentParent = $pageLayout->appendChild($currentParent);
    	} else {
    		if (LayoutModel::NO_HERITAGE === $instance->getIndexLayouts()) {
    			$currentParent = $instance->getParentByChildrenIdTag($idTag);
    		} else {
    			$currentParent = $instance->generateTree()->getParentByChildrenIdTag($idTag);
    			$currentParent = $instance->appendChild($currentParent);
    		}
    	}
    	
    	$layoutElementModel = $currentParent->getElementByIdTag($idTag);
        $layoutElementModel->getSemantic()->removeAllClass();
        if (! empty($this->data['classes'])) {
        	$index = count($this->data['classes']);
            for ($i = 0; $i < $index; $i ++) {
            	$layoutElementModel->getSemantic()->addClass(trim($this->data['classes'][$i]));
            }
        }

        $this->sendResult($portal->save());
    }
    
    public function frameAttributes($idTag)
    {
    	$portal = $this->getPortal();
    	$class = $this->data['item']['class'];
    	
    	/**
    	 *
    	 * @var PageModel|GabaritLayoutModel $instance
    	 */
    	$instance = $class::getInstance($this->data['item']['id']);
    	if ($instance instanceof PageModel) {
    		$pageLayout = $instance->getPageLayout();
    		if (empty($pageLayout)) {
    			$pageLayout = new PageLayoutModel($portal, []);
    			$instance->setPageLayout($pageLayout);
    		}
    		
    		$currentParent = $instance->generateTree()->getParentByChildrenIdTag($idTag);
    		$currentParent = $pageLayout->appendChild($currentParent);
    	} else {
    		if (LayoutModel::NO_HERITAGE === $instance->getIndexLayouts()) {
    			$currentParent = $instance->getParentByChildrenIdTag($idTag);
    		} else {
    			$currentParent = $instance->generateTree()->getParentByChildrenIdTag($idTag);
    			$currentParent = $instance->appendChild($currentParent);
    		}
    	}
    	
    	$layoutElementModel = $currentParent->getElementByIdTag($idTag);
        $layoutElementModel->getSemantic()->removeAllAttributes();
        if (! empty($this->data['attributes'])) {
        	$index = count($this->data['attributes']);
            for ($i = 0; $i < $index; $i ++) {
            	$layoutElementModel->getSemantic()->addAttribute($this->data['attributes'][$i]['name'], $this->data['attributes'][$i]['value']);
            }
        }

        $this->sendResult($portal->save());
    }
    
    public function zoneAttributes($idTag)
    {
    	$portal = $this->getPortal();
    	$class = $this->data['item']['class'];
        
    	/**
    	 *
    	 * @var PageModel|GabaritLayoutModel $instance
    	 */
    	$instance = $class::getInstance($this->data['item']['id']);
    	if ($instance instanceof PageModel) {
    		$pageLayout = $instance->getPageLayout();
    		if (empty($pageLayout)) {
    			$pageLayout = new PageLayoutModel($portal, []);
    			$instance->setPageLayout($pageLayout);
    		}
    		
    		$currentParent = $instance->generateTree()->getParentByChildrenIdTag($idTag);
    		$currentParent = $pageLayout->appendChild($currentParent);
    	} else {
    		if (LayoutModel::NO_HERITAGE === $instance->getIndexLayouts()) {
    			$currentParent = $instance->getParentByChildrenIdTag($idTag);
    		} else {
    			$currentParent = $instance->generateTree()->getParentByChildrenIdTag($idTag);
    			$currentParent = $instance->appendChild($currentParent);
    		}
    	}
    	
    	$layoutContainerModel = $currentParent->getElementByIdTag($idTag);
        $layoutContainerModel->getSemantic()->removeAllAttributes();
        if (! empty($this->data['attributes'])) {
        	$index = count($this->data['attributes']);
            for ($i = 0; $i < $index; $i ++) {
            	$layoutContainerModel->getSemantic()->addAttribute($this->data['attributes'][$i]['name'], $this->data['attributes'][$i]['value']);
            }
        }
        
        $this->sendResult($portal->save());
    }

    public function addElementPageLayout($id)
    {
        global $msg;
        
        if (empty($this->data)) {
            return $this->sendError("data not found!");
        }

        $portal = $this->getPortal();
        $page = PageModel::getInstance($id);
        $pageLayout = $page->getPageLayout();
        if (empty($pageLayout)) {
            $pageLayout = new PageLayoutModel($portal, []);
            $page->setPageLayout($pageLayout);
        }
        
        $cadreId = $this->data['cadre_id'] ?? null;
        if (! empty($cadreId)) {
            if ($page->getParentByIdTag($cadreId)) {
                return $this->sendError($msg['frame_already_exist']);
            }
            
            $child = new FrameCMSModel($portal, [
                "name" => $this->data["name"],
                "semantic" => $this->data["semantic"],
            ]);
            $child->getSemantic()->setIdTag($cadreId);
        } else {
            $child = new ZoneCMSModel($portal, $this->data);
            $child->getSemantic()->setIdTag("cms_zone_" . $child->getId());
        }

        $parent = $page->getParentByIdTag($this->data['parent']);
        if (!empty($child) && !empty($parent)) {            
            $parent = $pageLayout->appendChild($parent);
            $parent->appendChild($child);
            $this->sendResult($portal->save());
        } else {
        	return $this->sendError("Empty Child");
        }
    }

    public function addElementGabaritLayout($id)
    {
        global $msg;
        
        if (empty($this->data)) {
            return $this->sendError("data not found!");
        }

        $portal = $this->getPortal();
        $gabarit = GabaritLayoutModel::getInstance($id);
        
        $cadreId = $this->data['cadre_id'] ?? null;
        if (! empty($cadreId)) {
            if ($gabarit->getElementByIdTag($cadreId)) {
                return $this->sendError($msg['frame_already_exist']);
            }
            $child = new FrameCMSModel($portal, [
                "name" => $this->data["name"],
                "semantic" => $this->data["semantic"],
            ]);
            $child->getSemantic()->setIdTag($cadreId);
        } else {
            $child = new ZoneCMSModel($portal, $this->data);
            $child->getSemantic()->setIdTag("cms_zone_" . $child->getId());
        }
        
        $parent = $gabarit->getParentByIdTag($this->data['parent']);
        if (!empty($child) && !empty($parent)) {
        	if (LayoutModel::NO_HERITAGE !== $gabarit->getIndexLayouts()) {
	        	$parent = $gabarit->appendChild($parent);
        	}
            $parent->appendChild($child);
            $this->sendResult($portal->save());
        } else {
        	return $this->sendError("Empty Child");
        }
    }
    
    public function framePageList()
    {
        if (empty($this->data)) {
            return $this->sendResult([]);
        }
        
        try {
            $pages = [];
            foreach ($this->getPortal()->getPages() as $page) {
                $pageLayout = $page->getPageLayout();
                if (!empty($pageLayout)) {
                    foreach ($pageLayout->getAllFrames() as $frame) {
                        if ($frame->getSemantic()->getIdTag() == $this->data['idTag']) {
                            $pages[] = $page->serialize();
                            break;
                        }
                    }
                }
            }
            return $this->sendResult($pages);
        } catch(\Exception $e) {
            return $this->sendResult([]);
        }
    }
    
    public function frameGabaritList()
    {
        if (empty($this->data)) {
            return $this->sendResult([]);
        }
        
        try {       
            $idTag = $this->data['idTag'];
            $gabarits = [];
            foreach ($this->getPortal()->getGabaritLayouts() as $gabarit) {
                foreach ($gabarit->getAllFrames() as $frame) {
                    if ($frame->getSemantic()->getIdTag() == $idTag) {
                        $gabarits[] = $gabarit->serialize();
                        break;
                    }
                }
            }
            return $this->sendResult($gabarits);
        } catch(\Exception $e) {
            return $this->sendResult([]);
        }
    }
    
    public function pageRemoveFrame($id)
    {
        $portal = $this->getPortal();
        if (empty($this->data) || empty($this->data['idTag']) || !PageModel::exist($id)) {
            return $this->sendError("data or page not found");
        }
        
        try {  
            
            if (!preg_match('/cms_module_[^_]+_.+/', $this->data['idTag'])) {
                return $this->sendError("frame opac can't be deleted!");
            }
            
            $page = PageModel::getInstance($id);
            $pageLayout = $page->getPageLayout();
            if (!empty($pageLayout)) {                
                $pageLayout->removeElementByIdTag($this->data['idTag']);
                $this->sendResult($portal->save());
            }
        } catch(\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function gabaritRemoveFrame($id)
    {
        $portal = $this->getPortal();
        if (empty($this->data) || empty($this->data['idTag']) || !GabaritLayoutModel::exist($id)) {
            return $this->sendError("data or page not found");
        }
        
        try {
            
            if (!preg_match('/cms_module_[^_]+_.+/', $this->data['idTag'])) {
                return $this->sendError("frame opac can't be deleted!");
            }
            
            $gabarit = GabaritLayoutModel::getInstance($id);
            if (LayoutModel::NO_HERITAGE === $gabarit->getIndexLayouts()) {
            	$parent = $gabarit->getParentByChildrenIdTag($this->data['idTag']);
            } else {
            	$parent = $gabarit->generateTree()->getParentByChildrenIdTag($this->data['idTag']);
	            $parent = $gabarit->appendChild($parent);            	
            }
            $success = $gabarit->removeElementByIdTag($this->data['idTag']);
            if ($success) {
                $this->sendResult($portal->save());
            }
        } catch(\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function gabaritRemoveZone($id)
    {
        $portal = $this->getPortal();
        if (empty($this->data) || empty($this->data['idTag']) || !GabaritLayoutModel::exist($id)) {
            return $this->sendError("data or page not found");
        }
        
        try {
            if (!preg_match('/cms_zone_[0-9]+/', $this->data['idTag'])) {
                return $this->sendError("zone opac can't be deleted!");
            }
            
            $gabarit = GabaritLayoutModel::getInstance($id);
            if (LayoutModel::NO_HERITAGE === $gabarit->getIndexLayouts()) {
            	$parent = $gabarit->getParentByChildrenIdTag($this->data['idTag']);
            } else {
	            
	            $parent = $gabarit->generateTree()->getParentByChildrenIdTag($this->data['idTag']);
	            $parent = $gabarit->appendChild($parent);
            }
            $success = $gabarit->removeElementByIdTag($this->data['idTag']);
            
            if ($success) {
                $this->sendResult($portal->save());
            }
        } catch(\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    
    public function pageSaveContext($id) 
    {
        $portal = $this->getPortal();
        if (empty($this->data) || empty($this->data['name']) || empty($this->data['value']) || !PageModel::exist($id)) {
            return $this->sendError("data or page not found");
        }
        
        try {
            $page = PageModel::getInstance($id);
            $page->addContext([
                "name" => trim($this->data['name']),
                "value" => trim($this->data['value']),
                "url" => trim($this->data['url']),
            ]);
            $this->sendResult($portal->save());
        } catch(\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    
    public function pageEditContext($id) 
    {
        $portal = $this->getPortal();
        if (empty($this->data) || empty($this->data['context']) || empty($this->data['context']['name']) || empty($this->data['context']['value']) || !PageModel::exist($id)) {
            return $this->sendError("data or page not found");
        }
        
        try {
            $page = PageModel::getInstance($id);
            $page->editContext([
                "name" => trim($this->data['context']['name']),
                "value" => trim($this->data['context']['value']),
                "url" => trim($this->data['context']['url']),
            ], $this->data['index_context']);
            $this->sendResult($portal->save());
        } catch(\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    
    public function pageRemoveContext($id) 
    {
        $portal = $this->getPortal();
        if (empty($this->data) || !isset($this->data['index_context']) || !PageModel::exist($id)) {
            return $this->sendError("data or page not found");
        }
        
        try {
            $page = PageModel::getInstance($id);
            $page->removeContext($this->data['index_context']);
            $this->sendResult($portal->save());
        } catch(\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function opacViews() 
    {
        try {
            $opacViews = [];
            $query = "SELECT opac_view_id, opac_view_name FROM opac_views ORDER BY opac_view_name ASC";
            $result = pmb_mysql_query($query);
            if(pmb_mysql_num_rows($result)){
                while($row = pmb_mysql_fetch_assoc($result)) {
                    $opacViews[] = [
                        "value" => $row['opac_view_id'],
                        "label" => $row['opac_view_name']
                    ];
                }
            }
            $this->sendResult($opacViews);
        } catch(\Exception $e) {
            $this->sendResult([]); 
        }
    }

    public function pageBookmarkContext($id) 
    {
        $portal = $this->getPortal();
        if (empty($this->data) || !isset($this->data['index_context']) || !PageModel::exist($id)) {
            return $this->sendError("data or page not found");
        }
        
        try {
            $page = PageModel::getInstance($id);
            if ($page->getBookmarkContext() !== null && $page->getBookmarkContext() == $this->data['index_context']) {
                $page->unsetBookmarkContext();                
            } else {                
                $page->setBookmarkContext(intval($this->data['index_context']));
            }
            $this->sendResult($portal->save());
        } catch(\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    
    public function gabaritDuplicate($id)
    {
        global $msg;
        
        $portal = $this->getPortal();
        $id = intval($id);
        
        if (!GabaritLayoutModel::exist($id)) {
            return $this->sendError("Gabarit not exist!");
        }
        
        try {
            $gabaritLayout = GabaritLayoutModel::getInstance($id);
            
            $legacyLayout = $gabaritLayout->legacyLayout;
            $gabaritLayout->legacyLayout = null;
            
            $gabaritLayoutDuplicated = clone $gabaritLayout;
            
            $gabaritLayout->legacyLayout = $legacyLayout;
            $gabaritLayoutDuplicated->legacyLayout = $legacyLayout;
            
            $gabaritLayoutDuplicated->name = sprintf($msg['duplicated_of'], $gabaritLayoutDuplicated->name);
            if ($gabaritLayoutDuplicated->isDefault()) {
                $gabaritLayoutDuplicated->default = 0;
            }
            $portal->addGabarit($gabaritLayoutDuplicated);
            $this->sendResult($portal->save());
        } catch(\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    
    
    public function gabaritRemoveLayout($id)
    {
    	// init	
    	$portal = $this->getPortal();
    	
    	if (!GabaritLayoutModel::exist($id)) {
    		return $this->sendError("Gabarit not exist!");
    	}
    	
    	try {
    		$gabaritLayout = GabaritLayoutModel::getInstance($id);
    		$gabaritLayout->resetLayout($this->data['layout']);
    		$this->sendResult($portal->save());
    	} catch(\Exception $e) {
    		return $this->sendError($e->getMessage());
    	}
    }

    public function pageRemoveLayout($id)
    {
    	// init
    	$portal = $this->getPortal();
    	
    	if (!PageModel::exist($id)) {
    		return $this->sendError("Page not exist!");
    	}
    	
    	try {
    		$page = PageModel::getInstance($id);
    		$pageLayout = $page->getPageLayout();
    		if (empty($pageLayout)) {    			
	    		return $this->sendError("PageLayout not exist!");
    		}
    		$pageLayout->resetLayout($this->data['layout']);
    		$this->sendResult($portal->save());
    	} catch(\Exception $e) {
    		return $this->sendError($e->getMessage());
    	}
    }
    
    public function fecthLayout() 
    {
    	$class = $this->data['class'];
    	$id = intval($this->data['id']);
    	
    	if (!class_exists($class)) {
    		return $this->sendError("Class not exist");
    	}
    	
    	// init
    	$this->getPortal();
    	
    	if (!$class::exist($id)) {
    		return $this->sendError("Instance not exist");
    	}
    	
    	try {
    		$layout = array();
    		$intances = $class::getInstance($id);
    		if (method_exists($intances, "generateTree")) {
    			$tree = $intances->generateTree();
    			$layout = $tree->serialize();
    		}
    		$this->sendResult($layout);
    	} catch(\Exception $e) {
    		return $this->sendError($e->getMessage());
    	}
    }

    public function shareLayout() 
    {
    	$class = $this->data['item']['class'];
    	$id = intval($this->data['item']['id']);
    	
    	if (!class_exists($class)) {
    		return $this->sendError("Class not exist");
    	}
    	
    	// init
    	$this->getPortal();
    	
    	if (!$class::exist($id)) {
    		return $this->sendError("Instance not exist");
    	}
    	
    	try {
    		$intances = $class::getInstance($id);
    		/**
    		 * @var PageLayoutModel|GabaritLayoutModel $intances
    		 */
    		$layout = $intances;
    		if ($intances instanceof PageModel) {
    			$layout = $intances->getPageLayout();
    		}
    		
    		$result = $layout->shareLayout($this->data['zone']['semantic']['id_tag']);
    		$result['version_num'] = $this->getPortal()->save();
    		$this->sendResult($result);
    	} catch(\Exception $e) {
    		return $this->sendError($e->getMessage());
    	}
    }

    public function switchVersion(int $idPortal, int $idVersion)
    {
        if (empty($idPortal) || empty($idVersion)) {
            return $this->sendError("no data found!");
    	}

    	try {
            $portalOrm = new PortalOrm($idPortal);
            $portalOrm->version_num = $idVersion;
            $portalOrm->save();
    
            $this->sendResult(true);
    	} catch (\Exception $e) {
    		$this->sendError($e->getMessage());
    	}
    }

    public function renameVersion()
    {
        if (empty($this->data["id_version"]) || empty($this->data["name"])) {
            return $this->sendError("no data found!");
    	}

    	try {
            $versionOrm = new VersionOrm($this->data["id_version"]);
            $versionOrm->name = $this->data["name"];
            $versionOrm->save();
    
            $this->sendResult(true);
    	} catch (\Exception $e) {
    		$this->sendError($e->getMessage());
    	}
    }

    public function fetchVersions()
    {
    	try {
            $this->sendResult(CmsController::formatPortalList());
    	} catch (\Exception $e) {
    		$this->sendError($e->getMessage());
    	}
    }

    public function cleanVersions()
    {
    	try {
            $this->sendResult(CmsController::cleanVersions());
    	} catch (\Exception $e) {
    		$this->sendError($e->getMessage());
    	}
    }
}