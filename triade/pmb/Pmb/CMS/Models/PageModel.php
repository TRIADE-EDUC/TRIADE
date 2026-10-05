<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PageModel.php,v 1.20 2023/09/12 10:04:16 jparis Exp $
namespace Pmb\CMS\Models;

use Pmb\Common\Helper\Portal;

class PageModel extends PortalRootModel
{


    public static $nbInstance = 0;

    /**
     * 
     * @var PageModel[]
     */
    public static $instances = array();

    protected $name = "";

    protected $type = "";

    protected $subType = "";
    
    protected $contexts = [];

    protected $bookmarkContext = null;

    /**
     *
     * @var null|PagePortalModel|PageFRBRModel
     */
    protected $parentPage = null;

    /**
     *
     * @var null|GabaritLayoutModel
     */
    protected $gabaritLayout = null;

    /**
     *
     * @var PageLayoutModel
     */
    protected $pageLayout = null;

    /**
     *
     * @var ConditionEnvModel|ConditionFRBRModel[]
     */
    protected $conditions = [];

    /**
     *
     * @return NULL|PagePortalModel|PageFRBRModel
     */
    public function getPageParent()
    {
        return $this->parentPage;
    }

    /**
     *
     * @return NULL|GabaritLayoutModel
     */
    public function getGabaritLayout()
    {
        if (empty($this->getPageParent()) && empty($this->gabaritLayout)) {
            $this->gabaritLayout = $this->portal->getDefaultGabarit();
        }
        return $this->gabaritLayout;
    }

    /**
     *
     * @return PageLayoutModel
     */
    public function getPageLayout()
    {
    	if (empty($this->pageLayout)) {
    		$this->init();
    	}
        return $this->pageLayout;
    }

    /**
     *
     * @return ConditionEnvModel|ConditionFRBRModel[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    public function generateTree(): LayoutContainerModel
    {
    	$pageLayout = $this->getPageLayout();
    	if (empty($pageLayout->legacyLayout)) {
    		$pageLayout->legacyLayout = $this->getGabaritLayout();
    		if (empty($this->getGabaritLayout())) {
    			$pageLayout->legacyLayout = $this->parentPage->getPageLayout();
    		}
    	}
        return $this->getPageLayout()->generateTree();
    }

    public function setPageParent(PageModel $parentPage)
    {
        $this->unsetGabarit();
        $this->parentPage = $parentPage;
    }

    public function unsetPageParent()
    {
        $this->parentPage = null;
    }
    
    public function unsetGabarit() 
    {
        $this->gabaritLayout = null;
    }

    public function setGabarit(GabaritLayoutModel $gabaritLayout) 
    {
        $this->unsetPageParent();
        $this->gabaritLayout = $gabaritLayout;
    }
    
    public function setPageLayout($pageLayout)
    {
        $this->pageLayout = $pageLayout;
    }
    
    public function setDataFromForm($data)
    {
        if (! empty($data['name'])) {
            $this->name = trim($data['name']);
        } else {
            throw new \Exception("name not found!");
        }

        if (! empty($data['type'])) {
            if (in_array($data['type'], Portal::PAGES)) {
                $this->type = intval($data['type']);
            } else {
                throw new \Exception("{$data['type']} is not a page type!");
            }
        }

        if (! empty($data['sub_type'])) {
            if (in_array($data['sub_type'], Portal::getAllSubTypes())) {
                $this->subType = intval($data['sub_type']);
            } else {
                throw new \Exception("{$data['sub_type']} is not a page subtype!");
            }
        }
        
        if (is_array($data['parent_page']) && ! empty($data['parent_page'])) {
            $className = $data['parent_page']['class'];
            $this->setPageParent($className::getInstance($data['parent_page']['id']));
            $this->getPageLayout()->legacyLayout = $this->parentPage->getPageLayout();
            
        } else if (is_array($data['gabarit_layout']) && ! empty($data['gabarit_layout'])) {
            $className = $data['gabarit_layout']['class'];
            $this->setGabarit($className::getInstance($data['gabarit_layout']['id']));
            $this->getPageLayout()->legacyLayout = $this->gabaritLayout;
            
        } else {
            throw new \Exception("gabarit or parent page not found!");
        }
        
        $this->conditions = [];
        if (! empty($data['conditions'])) {
            $index = count($data['conditions']);
            for ($i = 0; $i < $index; $i++) {
                $condition = $data['conditions'][$i];
                if (empty($condition['class']) || !class_exists($condition['class'])) {
                    continue;
                }
                $this->conditions[] = new $condition['class']($condition, $this->portal);
            }
        }
    }
    
    /**
     * 
     * @param string $id_tag
     * @param boolean $checkInPageLayout
     * @return NULL|LayoutContainerModel
     */
    public function getParentByIdTag($id_tag, $checkInPageLayout = true) {
        $parent = null;
        if ($checkInPageLayout && !empty($this->pageLayout)) {
            $parent = $this->pageLayout->getElementByIdTag($id_tag);
        }
        if(empty($parent)) {
            $tree = $this->generateTree();
            $parent = $id_tag == LayoutNodeModel::ROOT_CONTAINER_ID ? $tree : $tree->getElementByIdTag($id_tag);
        }
        return $parent;
    }
    
    public function getContexts()
    {
        return $this->contexts;
    }

    public function addContext($context)
    {
        $this->contexts[] = $context;
    }
    
    public function editContext($context, $indexContext)
    {
        $this->contexts[$indexContext] = $context;
    }

    public function removeContext($indexContext)
    {
        if ($this->getBookmarkContext() !== null && $this->getBookmarkContext() == $indexContext) {
            $this->unsetBookmarkContext();
        }
        array_splice($this->contexts, $indexContext, 1);
    }

    public function setBookmarkContext($indexBookmark)
    {
        $this->bookmarkContext = $indexBookmark;
    }

    public function getBookmarkContext()
    {
        return $this->bookmarkContext;
    }

    public function unsetBookmarkContext()
    {
        unset($this->bookmarkContext);
    }
    
    public function init()
    {
    	$this->setPageLayout(new PageLayoutModel($this->portal, []));
    }
}