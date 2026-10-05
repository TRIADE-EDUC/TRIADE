<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArticleTemplate.php,v 1.1 2023/05/31 07:35:16 qvarin Exp $

namespace Pmb\DSI\Models\View\RssView\EntitiesTemplates;

use Pmb\Common\Helper\Directory;
use Pmb\Common\Helper\HelperEntities;

class ArticleTemplate extends CmsEditorialTemplate
{
    public const TYPE = "article";

    public const CONST_TYPE = TYPE_CMS_ARTICLE;

    public const TITLE_TEMPLATE = "article_for_rss_title.tpl.html";

    public const LINK_TEMPLATE = "article_for_rss_link.tpl.html";

    public const DESCRIPTION_TEMPLATE = "article_for_rss_description.tpl.html";
}
