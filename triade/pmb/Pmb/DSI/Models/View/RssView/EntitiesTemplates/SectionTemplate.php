<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SectionTemplate.php,v 1.1 2023/05/31 07:35:16 qvarin Exp $

namespace Pmb\DSI\Models\View\RssView\EntitiesTemplates;

use Pmb\Common\Helper\Directory;
use Pmb\Common\Helper\HelperEntities;

class SectionTemplate extends CmsEditorialTemplate
{
    public const TYPE = "section";

    public const CONST_TYPE = TYPE_CMS_SECTION;

    public const TITLE_TEMPLATE = "section_for_rss_title.tpl.html";

    public const LINK_TEMPLATE = "section_for_rss_link.tpl.html";

    public const DESCRIPTION_TEMPLATE = "section_for_rss_description.tpl.html";
}
