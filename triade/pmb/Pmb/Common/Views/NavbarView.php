<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: NavbarView.php,v 1.10.2.1.2.2 2025/05/13 06:20:33 dgoron Exp $
namespace Pmb\Common\Views;

use H2o_collection;

class NavbarView
{

    const TEMPLATE_PATH = "./includes/templates/navbar/";

    const CLASSIC = "navbar";

    const PAGINATOR = "paginator";

    const PAGINATOR_PERIO = "paginatorPerio";

    const NB_PER_PAGE_SELECTOR = "selector";

    const DISTANCE = 5;

    const DISTANCE_RGAA = 1;

    protected $pages = [];

    protected $page;

    protected $distance = self::DISTANCE;

    protected $total;

    protected $nbPerPage;

    protected $nbPages;

    protected $url;

    protected $nbPerPageCustomUrl;

    protected $action;

    protected $script;

    protected $infos = [];

    protected $customs = [];

    protected $initialized = false;

    protected $onsubmit = "return test_form(form)";

    protected  $from_cms = false;
    
    protected $onclick = '';

    public function __construct($page, $total, $nb_per_page, $url, $nb_per_page_custom_url = '', $action = '', $from_cms = false)
    {
        $this->page = intval($page);
        $this->total = intval($total);
        $this->nbPerPage = intval($nb_per_page);
        $this->url = preg_replace("/(page=)\d+/", "$1!!page!!", $url);
        $this->nbPerPageCustomUrl = $nb_per_page_custom_url;
        $this->action = $action;
        $this->from_cms = $from_cms;
    }

    public function setCustoms(string $customs)
    {
        $this->customs = explode(',', trim($customs));
    }

    public function setDistance(int $d)
    {
        $this->distance = $d;
    }

    protected function initPages()
    {
        global $opac_rgaa_active;

        $intervalPage = $this->getIntervalPage();

        if($opac_rgaa_active){
            if($this->nbPages > 4){
                if($this->page < 4 ){
                    $this->addPages(2, 4, false, true);
                }elseif($this->page > $this->nbPages - 3){
                    $this->addPages($this->nbPages - 3, $this->nbPages - 1, true, false);
                }else{
                    $this->addPages($intervalPage[0], $intervalPage[1], true, true);
                }
            }else{
                $this->addPages($intervalPage[0], $intervalPage[1], false, false);
            }
        }else{
            $this->addPages($intervalPage[0], $intervalPage[1], false, false);
        }
    }

    protected function getIntervalPage(){
        global $opac_rgaa_active;

        if($opac_rgaa_active){
            if(($this->nbPages <= 4) && ($this->page <= 4 )){
                $this->distance = 2;
            }
            $start = $this->page - $this->distance;
            if ($start <= 2) {
                $start = 2;
            }

            $end = $this->page + $this->distance;
            if ($end >= $this->nbPages) {
                $end = $this->nbPages - 1;
            }
        }else{
            $start = $this->page - $this->distance;
            if ($start < 1) {
                $start = 1;
            }
            $end = $this->page + $this->distance;
            if ($end > $this->nbPages) {
                $end = $this->nbPages;
            }
        }
        return array($start, $end);
    }

    protected function addPage($page){
        $this->pages[] = $page;
    }

    protected function addPages(int $start, int $end, bool $ellipsisStart, bool $ellipsisEnd)
    {
        global $opac_rgaa_active;

        if($opac_rgaa_active){
            $this->addPage(1);
            if($ellipsisStart){$this->addPage('...');}
        }
        for ($page = $start; $page <= $end; $page ++) {
            $this->addPage($page);
        }
        if($opac_rgaa_active){
            if($this->nbPages > 1){
                if($ellipsisEnd){$this->addPage('...');}
                $this->addPage($this->nbPages);
            }
        }
    }

    protected function init()
    {
        if ($this->initialized) {
            return;
        }

        global $script_test_form, $msg, $opac_rgaa_active;

        if ($this->nbPerPage) {
            $this->nbPages = ceil($this->total / $this->nbPerPage);
        } else {
            $this->nbPages = 1;
        }

        $this->initPages();

        $customUrl = str_replace("!!page!!", "1", $this->url);
        if (strpos($customUrl, 'javascript:') !== false) {
            $customUrl = $this->nbPerPageCustomUrl . ";" . $customUrl;
        } else {
            $customUrl = 'javascript:document.location="' . $customUrl . $this->nbPerPageCustomUrl . '"';
        }
        if (function_exists('test_field_value_comp')) {
            $this->script = str_replace("!!tests!!", test_field_value_comp('form', 'page', GREATER, $this->nbPages, $msg["page_too_high"]) . "\n" . test_field_value_comp('form', 'page', LESSER, 1, $msg["page_too_low"]), $script_test_form);
        }

        if ($this->action == "") {
            $this->action = $this->url;
            $this->action = str_replace("&page=!!page!!", "", $this->action);
            $this->action = str_replace("page=!!page!!&", "", $this->action);
            $this->action = str_replace("page=!!page!!", "", $this->action);
        }
        $last = ((($this->page - 1) * $this->nbPerPage) + $this->nbPerPage);
        if ($last > $this->total) {
            $last = $this->total;
        }
        $isLink = strpos($this->url, "javascript:") !== 0;
        $this->infos = [
            'current' => [
                'page' => $this->page,
                'nbPerPage' => $this->nbPerPage,
                'previous' => $this->page - 1,
                'next' => $this->page + 1,
                'elems' => [
                    'first' => ((($this->page - 1) * $this->nbPerPage) + 1),
                    'last' => $last
                ],
            ],
            'total' => $this->total,
            'nbPages' => $this->nbPages,
            'url' => $this->url,
            'onclick' => $this->onclick,
            'action' => $this->action,
            'script' => $this->script,
            'pages' => $this->pages,
            'onsubmit' => $this->onsubmit,
            'custom' => [
                'url' => $customUrl,
                'customs' => $this->customs
            ],
            'rgaa' => [
                'active' => $opac_rgaa_active,
                'isLink' => $isLink,
                'onclick' => $isLink ? "" : str_replace("javascript:", "", $this->url),
            ],
            'from_cms' => $this->from_cms
        ];
        $this->initialized = true;
    }

    public function render($what = self::CLASSIC)
    {
        global $opac_rgaa_active;

        $this->init();
        if ($opac_rgaa_active && file_exists(self::TEMPLATE_PATH . "rgaa_{$what}.html")) {
            $filename = self::TEMPLATE_PATH . "rgaa_{$what}.html";
        }

        if (file_exists(self::TEMPLATE_PATH . $what . "_subst.html")) {
            $filename = self::TEMPLATE_PATH . $what . "_subst.html";
        }

        if ($opac_rgaa_active && file_exists(self::TEMPLATE_PATH . "rgaa_{$what}_subst.html")) {
            $filename = self::TEMPLATE_PATH . "/rgaa_{$what}_subst.html";
        }

        if (empty($filename) || ! is_file($filename)) {
            $filename = self::TEMPLATE_PATH . $what . ".html";
        }
        $tpl = H2o_collection::get_instance($filename);
        return $tpl->render($this->infos);
    }

    public function getNavigator()
    {
        return $this->render();
    }

    public function getPaginator()
    {
        return $this->render(self::PAGINATOR);
    }

    public function getPaginatorPerio()
    {
        return $this->render(self::PAGINATOR_PERIO);
    }

    public function getNbPerPageSelector()
    {
        return $this->render(self::NB_PER_PAGE_SELECTOR);
    }

    public function setOnsubmit($onsubmit)
    {
        $this->onsubmit = $onsubmit;
    }
    
    public function setOnclick($onclick)
    {
        $this->onclick = $onclick;
    }
}

