<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Navbar.php,v 1.1.2.2 2025/01/24 08:23:15 dgoron Exp $
namespace Pmb\Common\Library\Navbar;

use Pmb\Common\Views\NavbarView;
use Pmb\Common\Helper\GlobalContext;

class Navbar
{

    protected $page;
    
    protected $nbrRows;
    
    protected $nbPerPage;
    
    protected $customs;
    
    protected $url = '';
    
    protected $onclick = '';
    
    protected $hiddenFormName = '';
    
    protected $distance = NavBarView::DISTANCE;
    
    public function __construct($page, $nbrRows, $nbPerPage)
    {
        $this->page = intval($page);
        $this->nbrRows = intval($nbrRows);
        $this->nbPerPage = intval($nbPerPage);
        if (defined('GESTION')) {
            $this->distance = 10;
        }
    }
    
    public function render()
    {
        global $opac_rgaa_active;
        
        $navbar = new NavbarView($this->page, $this->nbrRows, $this->nbPerPage, $this->url);
        $navbar->setOnclick($this->onclick);
//         $url, $nb_per_page_custom_url, $action
        
        if (!empty($this->customs)) {
            $navbar->setCustoms($this->customs);
        } else if (GlobalContext::get('items_pagination_custom')) {
            $navbar->setCustoms(GlobalContext::get('items_pagination_custom'));
        }
        if($opac_rgaa_active){
            $navbar->setDistance(NavBarView::DISTANCE_RGAA);
        } else {
            $navbar->setDistance($this->distance);
        }
        return $navbar->render();
    }
    
    public function setCustoms($customs)
    {
        $this->customs = $customs;
    }
    
    public function setUrl($url)
    {
        $this->url = $url;
    }
    
    public function setOnclick($onclick)
    {
        $this->onclick = $onclick;
    }
    
    public function setHiddenFormName($hiddenFormName, $launchSearch=false)
    {
        $this->hiddenFormName = $hiddenFormName;
        if(empty($this->url)) {
            $this->url = '#';
        }
        if(empty($this->onclick)) {
            $this->onclick = "document.".$this->hiddenFormName.".page.value=!!page!!;";
            if ($launchSearch) {
                $this->onclick .= " document.".$this->hiddenFormName.".launch_search.value=1; ";
            }
            $this->onclick .= " document.".$this->hiddenFormName.".submit(); return false;";
        }
    }
    
    public function setDistance($distance)
    {
        $this->distance = $distance;
    }
}