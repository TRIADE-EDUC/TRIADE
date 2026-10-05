<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: HTML.php,v 1.4 2023/11/08 11:39:44 rtigero Exp $

namespace Pmb\Common\Helper;

class HTML
{
    public const CHARSET_UTF8 = "utf-8";

    public const UNKNOWN_CHARACTER = "?";

    /**
     *
     * @param string $source
     * @return string
     */
    public static function cleanHTML(string $html, string $encoding = ""): string
    {
        global $charset;
        if (empty($encoding)) {
            $encoding = $charset;
        }
        if ($encoding == self::CHARSET_UTF8) {
            $html = preg_replace(
                '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
                '|[\x00-\x7F][\x80-\xBF]+'.
                '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
                '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
                '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/',
                self::UNKNOWN_CHARACTER,
                $html
            );

            $html = preg_replace('/<!--(.*?)-->/', '', $html);
            //$html = preg_replace('/<\?xml(.*?)>/', '', $html);

        }
        return $html ?? "";
    }

    /**
     * Format un rendu HTML donnée pour ajouter les HEADERS et faire un cleanHTML
     *
     * @param string $content
     * @return string
     */
    public static function formatRender(string $content = "", string $title = "")
    {
        global $opac_default_style;
        if(defined("GESTION")) {
            $head = \HtmlHelper::getInstance()->getStyleOpac($opac_default_style);
        } else {
            $head = \HtmlHelper::getInstance()->getStyle($opac_default_style);
        }
        if (!empty($title)) {
            $head .= "<title>{$title}</title>";
        }


        $html = '
			<!DOCTYPE html>
			<html>
				<head>
					%s
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
				</head>
				<body>
					%s
				</body>
			</html>
		';

        return sprintf(
            $html,
            $head,
            static::cleanHTML($content)
        );
    }
}
