<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: LookupHelper.php,v 1.5.2.1 2024/10/01 08:24:20 jparis Exp $

namespace Pmb\DSI\Helper;

use Pmb\DSI\Models\Channel\Portal\PortalChannel;
use Pmb\DSI\Models\Diffusion;

class LookupHelper
{
    public const PREFIX_PATTERN = "";

    public const PREFIX_H2O = "";

    public const PATTERN = [
        "!!equation!!",
        "!!date!!",
        "!!public!!",
        "!!nb_notices!!",
        "!!nb_notice!!",
        "!!hearmeroar!!"
    ];

    public const DYNAMIC_PATTERNS = [
        "!!portal_diffusion_link_{dynamic}!!" => "getPortalDiffusionLink"
    ];

    public static function format(string $template, $diffusion, bool $stripTags = false)
    {
        // $template = static::parseDom($template, $stripTags);
        $template = static::replacePattern($template, $diffusion);
        $template = static::replaceDynamicPatterns($template, $diffusion);
        return $stripTags ? strip_tags($template) : $template;
    }

    public static function h2oLookup($name, $h2oContext)
    {
        $prefixName = ":" . static::PREFIX_H2O;
        if (strpos($name, $prefixName) === 0) {
            $pattern = str_replace($prefixName, "", $name);
            $pattern = "!!" . static::PREFIX_PATTERN . $pattern . "!!";

            if (in_array($pattern, static::PATTERN)) {
                return $pattern;
            }
            return "";
        }
        return null;
    }

    public static function replacePattern($template, $diffusion)
    {

        $replace = [
            "equation" => "",
            "date" => "",
            "public" => "",
            "nb_notices" => "",
            "nb_notice" => "",
            "hearmeroar" => ""
        ];

        $replace['date'] = formatdate(today());
        $replace['equation'] = ($diffusion->item) ? $diffusion->item->getSearchInput() : "";
        $replace['public'] = $diffusion->settings->opacName ?? "";
        $replace['nb_notices'] = ($diffusion->item) ? $diffusion->item->getNbResults() : 0;
        $replace['nb_notice'] = ($diffusion->item) ? $diffusion->item->getNbResults() : 0;

        //A Lannister always pays his debts
        $replace['hearmeroar'] = "&#129409;";

        return str_replace(static::PATTERN, $replace, $template);
    }

    public static function getPatternList()
    {
        global $msg;

        $patternList = [];
        foreach (static::PATTERN as $pattern) {
            if (strpos($pattern, 'hearmeroar') || strpos($pattern, 'nb_notice')) {
                // don't show the secret egg
                continue;
            }
            $label = trim($pattern, "!");
            $label = $msg["selvars_{$label}"] ?? $label;
            $patternList[$pattern] = $label;
        }
        
        return $patternList;
    }

    public static function replaceDynamicPatterns($template, $diffusion)
    {
        foreach (static::DYNAMIC_PATTERNS as $pattern => $callback) {
            $pattern = str_replace("{dynamic}", "(.{1,}?)", $pattern);
            $pattern = "/$pattern/";
            $matches = array();

            preg_match_all($pattern, $template, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                if (count($match) == 2) {
                    $value = $match[1];
                    if (method_exists(static::class, $callback)) {
                        $template = str_replace($match[0], static::$callback($value), $template);
                    }
                }
            }
        }
        return $template;
    }

    protected static function getPortalDiffusionLink(int $idDiffusion)
    {
        $diffusion = new Diffusion($idDiffusion);
        $diffusion->fetchChannel();

        if ($diffusion->channel instanceof PortalChannel) {
            return $diffusion->channel->getPortalDiffusionLink($idDiffusion);
        }

        return "";
    }
}
