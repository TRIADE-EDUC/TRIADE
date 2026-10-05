<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionHistoryParser.php,v 1.9 2024/03/15 14:10:23 rtigero Exp $
namespace Pmb\DSI\Models;

use Pmb\DSI\Models\Item\RootItem;
use Pmb\DSI\Models\View\RootView;
use Pmb\DSI\Models\Channel\RootChannel;
use Pmb\DSI\Models\SubscriberList\RootSubscriberList;

class DiffusionHistoryParser
{

    /**
     *
     * @param array $buffer
     * @return RootItem
     */
    public function parseItem(array $buffer)
    {
        $rootItem = null;
        $bufferFormated = [];
        foreach ($buffer as $key => $contentBuffer) {
            $item = $this->formatToInstance($contentBuffer->content);
            if ($item->numParent == 0) {
                $rootItem = $item;
            } else {
                $bufferFormated[$key] = $item;
            }
        }

        $rootItem->childs = $this->fetchChildren($rootItem, $bufferFormated);
        return $rootItem;
    }

    /**
     *
     * @param array $buffer
     * @return RootView
     */
    public function parseView(array $buffer)
    {
        $rootView = null;
        $bufferFormated = [];

        foreach ($buffer as $key => $contentBuffer) {
            $view = $this->formatToInstance($contentBuffer->content);
            if ($view->numParent == 0) {
                $rootView = $view;
            } else {
                $bufferFormated[$key] = $view;
            }
        }

        $rootView->childs = $this->fetchChildren($rootView, $bufferFormated);
        return $rootView;
    }

    /**
     *
     * @param array $buffer
     * @return RootSubscriberList[]
     */
    public function parseSubscribers(array $buffer)
    {
        $subscribers = [];
        foreach ($buffer[0]->content as $subscriber) {
            // On regarde si le subscriber est supprimé dans l'envoi en attente
            if (isset($subscriber->updateType) && $subscriber->updateType == 0) {
                $subscribers[] = $this->formatToInstance($subscriber);
            }
        }

        return $subscribers;
    }

    /**
     *
     * @param array $buffer
     * @return RootChannel
     */
    public function parseChannel(array $buffer)
    {
        $channel = null;
        foreach ($buffer as $contentBuffer) {
            $channel = $this->formatToInstance($contentBuffer->content);
        }
        return $channel;
    }

    /**
     * Permet d'aller chercher les enfants d'un instance donnee
     *
     * @param RootItem|RootView $parent
     * @param RootItem[]|RootView[] $buffer
     * @return RootItem[]|RootView[]
     */
    protected function fetchChildren($parent, $buffer)
    {
        $children = [];

        foreach ($buffer as $child) {
            if (isset($parent->settings->locked) && $parent->settings->locked) {
                if ($child->numParent == $parent->numModel) {
                    $child->childs = $this->fetchChildren($child, $buffer);
                    $children[] = $child;
                }
            } else {
                if (($child->numParent == $parent->id)) {
                    $child->childs = $this->fetchChildren($child, $buffer);
                    $children[] = $child;
                }
            }
        }

        return $children;
    }

    /**
     * Formate une stdClass en instance (Item/View/Channel/Subscriber)
     *
     * @param object $data
     * @return mixed
     */
    private function formatToInstance($data)
    {
        $ignoredProps = [
            "__class"
        ];

        if (is_object($data) && property_exists($data, '__class')) {
            $object = new $data->__class();
            foreach ($data as $key => $value) {
                if (!in_array($key, $ignoredProps) && property_exists($object, $key)) {
                    if (is_object($value)) {
                        $object->{$key} = $this->formatToInstance($value);
                    } else {
                        $object->{$key} = $value;
                    }
                }
            }
            return $object;
        }

        return $data;
    }
}
