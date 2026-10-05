<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CmsEditorialTemplate.php,v 1.1 2023/05/31 07:35:16 qvarin Exp $

namespace Pmb\DSI\Models\View\RssView\EntitiesTemplates;

use Pmb\Common\Helper\Directory;
use Pmb\Common\Helper\HelperEntities;

class CmsEditorialTemplate extends RootTemplate implements TemplateInterface
{
    public const TYPE = "";

    public const LINK_TEMPLATE = "";

    protected $id;

    protected $data;

    /**
     * @var \cms_editorial_data
     */
    protected $instance;

    /**
     * @var \cms_page
     */
    protected $cmsPage;

    public function __construct(int $id, \StdClass $data)
    {
        global $opac_url_base;
        $this->id = $id;
        $this->data = $data;

        $varName = "";
        $this->cmsPage = new \cms_page($this->data->pageId);
        foreach ($this->cmsPage->vars as $var) {
            if ($var['id'] == $this->data->varId) {
                $varName = $var['name'];
            }
        }

        $this->instance = new \cms_editorial_data($id, static::TYPE, [
            static::TYPE => "{$opac_url_base}index.php?lvl=cmspage&pageid=&{$varName}=!!id!!"
        ]);
    }

    public function getLink($tplLink)
    {
        $h2o = \H2o_collection::get_instance($this->getTemplatePath($tplLink, static::LINK_TEMPLATE));
        return $h2o->render([
            $this->getEntityNamespace() => $this->instance,
        ]);
    }

    public static function getTemplates()
    {
        global $msg;

        return [
            "tplTitle" => static::getTemplateDirectories(),
            "tplDescription" => static::getTemplateDirectories(),
            "tplLink" => static::getTemplateDirectories()
        ];
    }
}
