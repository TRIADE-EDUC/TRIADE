<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RSS.php,v 1.1 2024/02/20 08:52:57 jparis Exp $
namespace Pmb\Common\Helper;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class RSS {
    protected $link = "";
    protected $timeout = 2;
    protected $nbElements = 0;

    public function __construct($link = "", $nbElements = 0) {
        $link = filter_var($link, FILTER_VALIDATE_URL);
        
        if($link) {
            $this->link = $link;
        }

        $this->nbElements = intval($nbElements);
    }

    public function setTimeout($timeout = 2) {
        $this->timeout = intval($timeout);
    }

    protected function getContent() {
        if(empty($this->link)) {
            return "";
        }

        $curl = new \Curl();
        $curl->timeout = $this->timeout;

        $content = $curl->get($this->link);
        if(!$content || ($content->headers['Status-Code'] != 200) ) {
			return "";
		}

        $body = trim($content->body);
        if(!$body) {
			return "";
		}

        return $body;
    }

    public function parseContent() {
        global $lang;

        $content = $this->getContent();
        $parsedContent = [];
        
        $domDocument = new \domDocument();
        $loaded = $domDocument->loadXML($content);
        
        if(!$loaded) {
            return $parsedContent;
        }

        //Flux RSS
        $channel = $domDocument->getElementsByTagName("channel");
		if ($channel->length > 0) {
            $channel = $channel->item(0);

            $parsedContent["title"] = $channel->getElementsByTagName("title")->item(0)->nodeValue;
            $parsedContent["items"] = [];

            foreach($channel->getElementsByTagName("item") as $item) {
                if($this->nbElements != 0 && count($parsedContent["items"]) >= $this->nbElements) {
                    break;
                }
                
                $date = new \DateTime($item->getElementsByTagName("pubDate")->item(0)->nodeValue);
                $parsedContent["items"][] = [
                    "title" => $item->getElementsByTagName("title")->item(0)->nodeValue,
                    "link" => $item->getElementsByTagName("link")->item(0)->nodeValue,
                    "description" => $item->getElementsByTagName("description")->item(0)->nodeValue,
                    "pubDate" => DateHelper::formatDateByUserLang($date)
                ];
            }

            return $parsedContent;
        }

        //Flux Atom
        $feed = $domDocument->getElementsByTagName("feed");
        if($feed->length > 0) {
            $feed = $feed->item(0);

            $parsedContent["title"] = $feed->getElementsByTagName("title")->item(0)->nodeValue;
            $parsedContent["items"] = [];

            foreach($feed->getElementsByTagName("entry") as $entry) {
                if($this->nbElements != 0 && count($parsedContent["items"]) >= $this->nbElements) {
                    break;
                }

                $date = new \DateTime($entry->getElementsByTagName("published")->item(0)->nodeValue);
                $parsedContent["items"][] = [
                    "title" => $entry->getElementsByTagName("title")->item(0)->nodeValue,
                    "link" => $entry->getElementsByTagName("link")->item(0)->getAttribute("href"),
                    "description" => $entry->getElementsByTagName("summary")->item(0)->nodeValue,
                    "pubDate" => DateHelper::formatDateByUserLang($date)
                ];
            }

            return $parsedContent;
        }

        return $parsedContent;
    }
}