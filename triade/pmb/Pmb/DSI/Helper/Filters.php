<?php

namespace Pmb\DSI\Helper;

use Pmb\Common\Helper\HelperEntities;
use Pmb\DSI\Models\Channel\RootChannel;
use Pmb\DSI\Models\DSIParserDirectory;
use Pmb\DSI\Models\Product;
use Pmb\DSI\Models\Tag;

class Filters
{
    public const FILTER_TAG = "tags";

    public const FILTER_ENTITIES = "entities";

    public const FILTER_CHANNEL = "channel";

    public const FILTER_PRODUCT = "products";

    public static function getProductOptions()
    {
        $product = new Product();
        $products = array();
        foreach ($product->getList() as $product) {
            $products[] = [
                "value" => $product->id,
                "label" => $product->name
            ];
        }

        return $products;
    }

    public static function getTagOptions()
    {
        $tags = [];
        $tagModel = new Tag();
        foreach ($tagModel->getTags() as $tag) {
            $tags[] = [
                "value" => $tag->id,
                "label" => $tag->name,
            ];
        }

        return $tags;
    }

    public static function getEntityOptions()
    {
        $entities = HelperEntities::get_entities_labels();
        array_walk($entities, function (&$item, $key) {
            $item = ["value" => $key, "label" => $item];
        });

        return array_values($entities);
    }

    public static function getChannelOptions()
    {
        $channels = [];
        $manifests = DSIParserDirectory::getInstance()->getManifests("Pmb/DSI/Models/Channel/");
        foreach ($manifests as $manifest) {
            $message = $manifest->namespace::getMessages();
            $channels[] = [
                "value" => RootChannel::IDS_TYPE[$manifest->namespace],
                "label" => $message['name'],
            ];
        }

        return $channels;
    }

    public static function getFilters()
    {
        return [
            [
                "label" => "msg:dsi_tag",
                "type" => static::FILTER_TAG,
                "options" => static::getTagOptions()
            ],
            [
                "label" => "msg:items_form_type",
                "type" => static::FILTER_ENTITIES,
                "options" => static::getEntityOptions()
            ],
            [
                "label" => "msg:diffusion_form_channel",
                "type" => static::FILTER_CHANNEL,
                "options" => static::getChannelOptions()
            ],
            [
                "label" => "msg:diffusion_form_products",
                "type" => static::FILTER_PRODUCT,
                "options" => static::getProductOptions()
            ]
        ];
    }
}
