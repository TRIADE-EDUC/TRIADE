<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: HelperEntities.php,v 1.20.4.3 2025/05/21 08:03:12 rtigero Exp $

namespace Pmb\Common\Helper;

use notice;
use Pmb\DSI\Models\Item\Entities\Animation\AnimationListItem\AnimationListItem;
use Pmb\DSI\Models\Item\Entities\Diffusion\DiffusionListItem\DiffusionListItem;
use Pmb\DSI\Models\Item\Entities\Record\RecordListItem\RecordListItem;
use Pmb\DSI\Models\Item\Entities\Article\ArticleListItem\ArticleListItem;
use Pmb\DSI\Models\Item\Entities\ItemWatch\ItemWatchListItem\ItemWatchListItem;
use Pmb\DSI\Models\Item\Entities\Section\SectionListItem\SectionListItem;

class HelperEntities
{
	public const TYPE_EMPR = 1;

	public static function get_entities_labels()
	{
		global $msg;

		return array(
			TYPE_NOTICE => $msg['288'],
			// TYPE_BULLETIN => $msg['type_bull'],
			// TYPE_AUTHOR => $msg['isbd_author'],
			// TYPE_CATEGORY => $msg['isbd_categories'],
			// TYPE_PUBLISHER => $msg['isbd_editeur'],
			// TYPE_COLLECTION => $msg['isbd_collection'],
			// TYPE_SUBCOLLECTION => $msg['isbd_subcollection'],
			// TYPE_SERIE => $msg['isbd_serie'],
			// TYPE_TITRE_UNIFORME => $msg['isbd_titre_uniforme'],
			// TYPE_INDEXINT => $msg['isbd_indexint'],
			// TYPE_EXPL => $msg['376'],
			// TYPE_EXPLNUM => $msg['search_explnum'],
			// TYPE_AUTHPERSO => $msg['search_by_authperso_title'],
			TYPE_CMS_SECTION => $msg['cms_menu_editorial_section'],
			TYPE_CMS_ARTICLE => $msg['dsi_article_type'],
			// TYPE_CONCEPT => $msg['search_concept_title'],
			TYPE_DOCWATCH => $msg['item_docwatch'],
			TYPE_DSI_DIFFUSION => $msg['dsi_diffusions'],
			TYPE_ANIMATION => $msg['animation_base_title']
			// TYPE_EXTERNAL => $msg['facettes_external_records'],
		);
	}

	public static function get_entities_namespace()
	{
		return array(
			TYPE_NOTICE => "record",
			TYPE_AUTHOR => "author",
			TYPE_CATEGORY => "category",
			TYPE_PUBLISHER => "publisher",
			TYPE_COLLECTION => "collection",
			TYPE_SUBCOLLECTION => "subcollection",
			TYPE_SERIE => "serie",
			TYPE_TITRE_UNIFORME => "work",
			TYPE_INDEXINT => "indexint",
			TYPE_EXPL => "expl",
			TYPE_EXPLNUM => "explnum",
			TYPE_AUTHPERSO => "authperso",
			TYPE_CMS_SECTION => "section",
			TYPE_CMS_ARTICLE => "article",
			TYPE_CONCEPT => "concept",
			TYPE_DOCWATCH => "ItemWatch",
			TYPE_DSI_DIFFUSION => "diffusion",
			TYPE_CMS_EDITORIAL => "cms_editorial",
			TYPE_EXTERNAL => "external",
			TYPE_ANIMATION => "animation"
		);
	}

	public static function get_dsi_entities_namespace()
	{
		$result = array_map("ucfirst", static::get_entities_namespace());
		return array_replace($result, [
			TYPE_DOCWATCH => "ItemWatch"
		]);
	}

	public static function get_entities_namespace_plural()
	{
		return array(
			TYPE_NOTICE => "records",
			TYPE_AUTHOR => "authors",
			TYPE_CATEGORY => "categories",
			TYPE_PUBLISHER => "publishers",
			TYPE_COLLECTION => "collections",
			TYPE_SUBCOLLECTION => "subcollections",
			TYPE_SERIE => "series",
			TYPE_TITRE_UNIFORME => "works",
			TYPE_INDEXINT => "indexints",
			TYPE_EXPL => "expls",
			TYPE_EXPLNUM => "explnums",
			TYPE_AUTHPERSO => "authpersos",
			TYPE_CMS_SECTION => "sections",
			TYPE_CMS_ARTICLE => "articles",
			TYPE_CONCEPT => "concepts",
			TYPE_DOCWATCH => "items",
			TYPE_DSI_DIFFUSION => "diffusions",
			TYPE_EXTERNAL => "external",
			TYPE_ANIMATION => "animations"
		);
	}

	public static function get_entities_classnames()
	{
		return array(
			TYPE_NOTICE => "frbr_entity_records_view",
			TYPE_AUTHOR => "frbr_entity_authors_view",
			TYPE_CATEGORY => "frbr_entity_categories_view",
			TYPE_PUBLISHER => "frbr_entity_publishers_view",
			TYPE_COLLECTION => "frbr_entity_collections_view",
			TYPE_SUBCOLLECTION => "frbr_entity_subcollections_view",
			TYPE_SERIE => "frbr_entity_series_view",
			TYPE_TITRE_UNIFORME => "frbr_entity_works_view",
			TYPE_INDEXINT => "frbr_entity_indexint_view",
			TYPE_EXPL => "frbr_entity_expl_view",
			TYPE_AUTHPERSO => "frbr_entity_authperso_view",
			TYPE_CMS_SECTION => "sections",
			TYPE_CMS_ARTICLE => "articles",
			TYPE_CONCEPT => "frbr_entity_concepts_view",
			TYPE_DOCWATCH => "docwatchs",
			TYPE_DSI_DIFFUSION => "diffusions",
			TYPE_ANIMATION => "animations"
		);
	}

	public static function get_entity_tree($type = 0)
	{
		if (!$type) {
			return [];
		}
		$className = self::get_entities_classnames()[$type];
		if (! class_exists($className)) {
			return array();
		}
		$view = new $className;
		return $view->get_format_data_structure();
	}

	public static function get_entities_default_templates()
	{
		return array(
			TYPE_NOTICE => "<div>
{% for record in records %}
{{record.content}}
{% endfor %}
</div>",
			TYPE_AUTHOR => "<div>
{% for author in authors %}
<h3>{{author.name}}</h3>
<blockquote>{{author.comment}}</blockquote>
{% endfor %}
</div>",
			TYPE_CATEGORY => "<div>
{% for category in categories %}
<h3>{{category.libelle}}</h3>
<blockquote>{{category.comment}}</blockquote>
{% endfor %}
</div>",
			TYPE_PUBLISHER => "<div>
{% for publisher in publishers %}
<h3>{{publisher.name}}</h3>
<blockquote>{{publisher.comment}}</blockquote>
{% endfor %}
</div>",
			TYPE_COLLECTION => "<div>
{% for collection in collections %}
<h3>{{collection.name}}</h3>
<blockquote>{{collection.comment}}</blockquote>
{% endfor %}
</div>",
			TYPE_SUBCOLLECTION => "<div>
{% for subcollection in subcollections %}
<h3>{{subcollection.name}}</h3>
<blockquote>{{subcollection.comment}}</blockquote>
{% endfor %}
</div>",
			TYPE_SERIE => "<div>
{% for serie in series %}
<h3>{{serie.name}}</h3>
{% endfor %}
</div>",
			TYPE_TITRE_UNIFORME => "<div>
{% for work in works %}
<h3>{{work.name}}</h3>
<blockquote>{{work.comment}}</blockquote>
{% endfor %}
</div>",
			TYPE_INDEXINT => "<div>
{% for ind in indexint %}
<h3>{{ind.name}}</h3>
<blockquote>{{ind.comment}}</blockquote>
{% endfor %}
</div>",
			TYPE_EXPL => "<h3>{{title}}</h3>
{% for elt in expl %}
{% if elt.id_notice %}
<a href='./index.php?lvl=notice_display&id={{elt.id_notice}}'>{{elt.notice_title}} - {{elt.cb}}</a>
{% endif %}
{% if elt.id_bulletin %}
<a href='./index.php?lvl=bulletin_display&id={{elt.id_bulletin}}'>{{elt.notice_title}} - {{elt.cb}}</a>
{% endif %}
<br>
{% endfor %}",
			TYPE_EXPLNUM => "explnum",
			TYPE_AUTHPERSO => "<div>
{% for auth in authperso %}
	{% if auth.info.view %}
		{{ auth.info.view }}
	{% else %}
		{{ auth.name }} : {{ auth.info.isbd }}
	{% endif%}
{% endfor %}
</div>",
			TYPE_CMS_SECTION => "<div>
{% for section in sections %}
<h3>{{section.title}}</h3>
<blockquote>{{section.resume}}</blockquote>
<blockquote>{{section.contenu}}</blockquote>
{% endfor %}
</div>",
			TYPE_CMS_ARTICLE => "<div>
{% for article in articles %}
<h3>{{article.title}}</h3>
<blockquote>{{article.resume}}</blockquote>
<blockquote>{{article.content}}</blockquote>
{% endfor %}
</div>",
			TYPE_CONCEPT => "<div>
{% for concept in concepts %}
<h3>{{concept.uri}}</h3>
<blockquote>{{concept.broaders_list}}</blockquote>
<blockquote>{{concept.narrowers_list}}</blockquote>
{% endfor %}
</div>",
			TYPE_DOCWATCH => "<div>
{% for item in items %}
{% if item.interesting %}
{% if item.status!=2 %}
<div>
    <a href='{{item.url}}' title='Source' target='_blank'>
        <h3>{{item.title}}</h3>
    </a>
    <blockquote>{{item.publication_date}} / {{item.source.title}}</blockquote>
    <blockquote>{{item.summary}}</blockquote>
</div>
{% endif %}
{% endif %}
{% endfor %}
</div>",
			TYPE_DSI_DIFFUSION => "<div>
{% for diffusion in diffusions %}
	{{diffusion.name}}
{% endfor %}
</div>",
			TYPE_ANIMATION => "<div>
{% for animation in animations %}
	<h3>{{animation.name}}</h3>
	<blockquote>{{animation.description}}</blockquote>
{% endfor %}
</div>"
		);
	}

	public static function get_entities_search_fields()
	{
		return array(
			"record" => "search_fields",
			"author" => "search_fields_authorities",
			"empr" => "search_fields_empr"
		);
	}

	public static function get_subscriber_entities()
	{
		return array(
			self::TYPE_EMPR => "Empr"
		);
	}

	public static function get_item_from_type($type)
	{
		switch ($type) {
			case TYPE_NOTICE:
				return RecordListItem::class;
			case TYPE_CMS_ARTICLE:
				return ArticleListItem::class;
			case TYPE_CMS_SECTION:
				return SectionListItem::class;
			case TYPE_DOCWATCH:
				return ItemWatchListItem::class;
			case TYPE_DSI_DIFFUSION:
				return DiffusionListItem::class;
			case TYPE_ANIMATION:
				return AnimationListItem::class;
			default:
				return "";
		}
	}
}
