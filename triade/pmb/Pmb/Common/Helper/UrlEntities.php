<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UrlEntities.php,v 1.2 2023/06/30 14:18:50 rtigero Exp $

namespace Pmb\Common\Helper;

class UrlEntities
{
    public const LINK = [
        TYPE_NOTICE => "catalog.php?categ=isbd&id=!!id!!",
        TYPE_BULLETIN => "catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!id!!"
    ];

    public const OPAC_LINK = [
        TYPE_NOTICE => "index.php?lvl=notice_display&id=!!id!!",
        TYPE_BULLETIN => "index.php?lvl=bulletin_display&id=!!id!!"
    ];

    public const OPAC_SEARCH_LINK = [
        "lien_rech_notice" => "index.php?lvl=notice_display&id=!!id!!",
        "lien_rech_auteur" => "index.php?lvl=author_see&id=!!id!!",
        "lien_rech_editeur" => "index.php?lvl=publisher_see&id=!!id!!",
        "lien_rech_titre_uniforme" => "index.php?lvl=titre_uniforme_see&id=!!id!!",
        "lien_rech_serie" => "index.php?lvl=serie_see&id=!!id!!",
        "lien_rech_collection" => "index.php?lvl=coll_see&id=!!id!!",
        "lien_rech_subcollection" => "index.php?lvl=subcoll_see&id=!!id!!",
        "lien_rech_indexint" => "index.php?lvl=indexint_see&id=!!id!!",
        // "lien_rech_motcle" => "index.php?lvl=search_result&mode=keyword&auto_submit=1&user_query=!!mot!!",
        "lien_rech_motcle" => "index.php?lvl=more_results&mode=keyword&user_query=!!mot!!&tags=ok",
        "lien_rech_categ" => "index.php?lvl=categ_see&id=!!id!!",
        "lien_rech_perio" => "index.php?lvl=notice_display&id=!!id!!",
        "lien_rech_bulletin" => "index.php?lvl=bulletin_display&id=!!id!!",
        "lien_rech_concept" => "index.php?lvl=concept_see&id=!!id!!",
        "lien_rech_authperso" => "index.php?lvl=authperso_see&id=!!id!!",
    ];

    public const OPAC_SEARCH_LINK_CONST = [
        TYPE_NOTICE => "index.php?lvl=notice_display&id=!!id!!",
        TYPE_AUTHOR => "index.php?lvl=author_see&id=!!id!!",
        TYPE_PUBLISHER => "index.php?lvl=publisher_see&id=!!id!!",
        TYPE_TITRE_UNIFORME => "index.php?lvl=titre_uniforme_see&id=!!id!!",
        TYPE_SERIE => "index.php?lvl=serie_see&id=!!id!!",
        TYPE_COLLECTION => "index.php?lvl=coll_see&id=!!id!!",
        TYPE_SUBCOLLECTION => "index.php?lvl=subcoll_see&id=!!id!!",
        TYPE_INDEXINT => "index.php?lvl=indexint_see&id=!!id!!",
        TYPE_CATEGORY => "index.php?lvl=categ_see&id=!!id!!",
        TYPE_BULLETIN => "index.php?lvl=bulletin_display&id=!!id!!",
        TYPE_CONCEPT => "index.php?lvl=concept_see&id=!!id!!",
        TYPE_AUTHPERSO => "index.php?lvl=authperso_see&id=!!id!!",
    ];

    public static function getOPACLink()
    {
        return array_map(function (string $link) {
            global $base_path;
            global $use_opac_url_base, $opac_url_base;

            if ($use_opac_url_base) {
                return $opac_url_base . $link;
            } else {
                return defined('GESTION') ? "{$base_path}/opac_css/{$link}" : "{$base_path}/{$link}";
            }
        }, static::OPAC_SEARCH_LINK);
    }

    public static function getPermalink(int $type)
    {
        global $use_opac_url_base;

        if (!$use_opac_url_base && defined('GESTION')) {
            return static::LINK[$type] ?? "";
        } else {
            return static::OPAC_LINK[$type] ?? "";
        }
    }

    public static function getOpacRealPermalink(int $type, int $id)
    {
        if(empty(static::OPAC_SEARCH_LINK_CONST[$type])){
            return "";
        }
        return str_replace("!!id!!", $id, static::OPAC_SEARCH_LINK_CONST[$type]);
    }
}
