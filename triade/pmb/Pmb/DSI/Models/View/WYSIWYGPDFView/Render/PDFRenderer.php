<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PDFRenderer.php,v 1.2 2023/07/27 12:23:12 gneveu Exp $
namespace Pmb\DSI\Models\View\WYSIWYGPDFView\Render;

use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\Item\Item;
use Pmb\DSI\Models\View\RootView;
use Pmb\DSI\Models\View\WYSIWYGView\WYSIWYGView;
use Pmb\DSI\Models\View\WYSIWYGView\Render\HTML5Renderer;

class PDFRenderer extends HTML5Renderer
{

    public const CONTAINER_ELEMENT_TEMPLATE = '!!content!!';

    public const BLOC_ELEMENT_TEMPLATE = '<div style="!!style!!">!!content!!</div>';

    public const ROOT_BLOC_ELEMENT_TEMPLATE = '
        <style>
            * {
                box-sizing: border-box;
                position: relative;
            }

            .pdf-container {
                position:relative;
                width: 100%;
                max-width: 100%;
            }

            table {
                table-layout: fixed;
            }

            td {
                padding: 0;
            }
        </style>
        <page style="width: 8.21in;padding: 0in">
            <table class="pdf-container" style="!!style!!" cellpadding="0" cellspacing="0">
                <tr>
                    <td>!!content!!</td>
                </tr>
            </table>
        </page>';

    public function render($currentElement, $root = false): string
    {
        $currentElement->root = $root ?? false;
        return parent::render($currentElement);
    }

    protected function renderBlockElement($currentElement)
    {
        if (! isset($currentElement->style->width)) {
            $currentElement->style->width = "100%";
        }

        if ($currentElement->root || empty($currentElement->blocks) || count($currentElement->blocks) == 1) {
            return $this->renderBlockWithoutChild($currentElement);
        } else {
            return $this->renderBlockWithChild($currentElement);
        }
    }

    protected function renderBlockWithoutChild($currentElement)
    {
        $content = "";
        foreach ($currentElement->blocks as $block) {
            $content .= $this->render($block);
        }

        if ($currentElement->root) {
            unset($currentElement->style->width);
        } else {
            $currentElement->style->maxWidth = $currentElement->style->width;
            $currentElement->style->position = "relative";
        }

        return str_replace([
            '!!style!!',
            '!!content!!'
        ], [
            $this->getStyleString($currentElement->style),
            $content
        ], $currentElement->root ? static::ROOT_BLOC_ELEMENT_TEMPLATE : static::BLOC_ELEMENT_TEMPLATE);
    }

    protected function renderBlockWithChild($currentElement)
    {
        if ($currentElement->style->flexDirection == "column") {
            $html = "<table style='!!style!!' class='pdf-grid' cellpadding='0' cellspacing='0'>!!content!!</table>";
        } else {
            $html = "<table style='!!style!!' class='pdf-grid' cellpadding='0' cellspacing='0'><tr>!!content!!</tr></table>";
        }

        $width = 100;
        if (! empty($currentElement->blocks) && $currentElement->style->flexDirection != "column") {
            $width = 100 / count($currentElement->blocks);
            $width = round($width, 2, PHP_ROUND_HALF_ODD);
        }

        $content = '';
        foreach ($currentElement->blocks as $block) {
            if ($currentElement->style->flexDirection == "column") {
                $content .= "<tr><td style='width:{$width}%;max-width:{$width}%;'>!!content!!</td></tr>";
            } else {
                $content .= "<td style='width:{$width}%;max-width:{$width}%;'>!!content!!</td>";
            }
            $content = str_replace('!!content!!', $this->render($block), $content);
        }
        return str_replace([
            '!!style!!',
            '!!content!!'
        ], [
            $this->getStyleString($currentElement->style),
            $content
        ], $html);
    }

    protected function renderVideoElement($currentElement)
    {
        return "<!-- videos not supported -->";
    }

    protected function getStyleString($style): string
    {
        if (!is_object($style)) {
            return "";
        }

        if (isset($style->block)) {
            $style = $style->block;
        }

        $style = get_object_vars($style);
        $style = $this->convertToXHTML($style);

        array_walk($style, function (&$value, $attribute) {
            $value = "{$attribute}:{$value}";
        });

        return implode(';', $style);
    }

    protected function convertToXHTML($style)
    {
        $convertedStyle = [];

        foreach ($style as $attribute => $value) {
            $attribute = Helper::camelize_to_kebab($attribute);

            switch ($attribute) {
                case 'display':
                    if ($value !== 'flex') {
                        $convertedStyle['display'] = $value;
                    }
                    break;

                case 'justify-content':
                    $convertedStyle['position'] = 'relative';
                    switch ($value) {
                        default:
                        case 'start':
                            $convertedStyle['text-align'] = 'left';
                            break;
                        case 'center':
                            $convertedStyle['text-align'] = 'center';
                            break;
                        case 'end':
                            $convertedStyle['text-align'] = 'right';
                            break;
                    }
                    break;

                case 'align-items':
                    $convertedStyle['position'] = 'relative';
                    switch ($value) {
                        default:
                        case 'start':
                            $convertedStyle['vertical-align'] = 'top';
                            break;
                        case 'center':
                            $convertedStyle['vertical-align'] = 'middle';
                            break;
                        case 'end':
                            $convertedStyle['vertical-align'] = 'bottom';
                            break;
                    }
                    break;

                case 'min-height':
                case 'flex-direction':
                case 'flex-grow':
                case 'flex':
                    // not compatible
                    break;

                default:
                    $convertedStyle[$attribute] = $value;
                    break;
            }
        }

        return $convertedStyle;
    }

    protected function renderImageElement($currentElement)
    {
        $currentElement->style->block->maxWidth = "100%";
        $currentElement->style->image->maxWidth = "100%";

        return str_replace([
            '!!style!!',
            '!!content!!'
        ], [
            $this->getStyleString($currentElement->style->block),
            parent::renderImageElement($currentElement)
        ], static::BLOC_ELEMENT_TEMPLATE);
    }

    protected function renderTextElement($currentElement)
    {
        return str_replace([
            '!!style!!',
            '!!content!!'
        ], [
            "width:100%;",
            parent::renderTextElement($currentElement)
        ], static::BLOC_ELEMENT_TEMPLATE);
    }

    protected function renderRichTextElement($currentElement)
    {
        return str_replace([
            '!!style!!',
            '!!content!!'
        ], [
            "width:100%;",
            parent::renderRichTextElement($currentElement)
        ], static::BLOC_ELEMENT_TEMPLATE);
    }

    protected function renderViewElement($currentElement)
    {
        return str_replace([
            '!!style!!',
            '!!content!!'
        ], [
            "width:100%;position:relative;",
            parent::renderViewElement($currentElement)
        ], static::BLOC_ELEMENT_TEMPLATE);
    }
}
