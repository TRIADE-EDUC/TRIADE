<?php
namespace Pmb\Common\Helper;
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Annotations.php,v 1.1 2021/05/04 13:13:36 arenou Exp $

class Annotations
{
    public const REGEXP = '/@(Description|Example|Since|Format|Collection|Type|var)\s(.+)/';
    // TODO - Insert your code here

    /**
     */
    public function __construct()
    {

        // TODO - Insert your code here
    }
    
    public static function get(\Reflector $reflectedObject,string $annotation="")
    {
        $docComment = $reflectedObject->getDocComment();
        if ($docComment === false) {
            return [];
        }
        $response = []; 
        if (preg_match_all(self::REGEXP, $docComment, $annotations)) {
            // Remplissage de la feuille
            for ($i = 0; $i < count($annotations[0]); $i ++) {
                $response[$annotations[1][$i]] = trim(\encoding_normalize::charset_normalize($annotations[2][$i], "iso-8859-1"));
            }
        }
        return $response;
    }
}

