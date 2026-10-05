<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CategoryModel.php,v 1.5 2023/03/14 15:16:12 gneveu Exp $

namespace Pmb\Autorities\Models;

use Pmb\Common\Models\Model;

class CategoryModel extends Model
{
    protected $ormName = "\Pmb\Autorities\Orm\CategoryOrm";
    
    public static function getCategory($categoryId)
    {
        $category = new \category($categoryId);
        $conceptInfos = [];
        $conceptInfos['id'] = $category->id;
        $conceptInfos['displayLabel'] = $category->get_isbd();
        return $conceptInfos;
    }
    
    public static function updateAnimationCategories($categories, $animationId)
    {
        $ordre = 0;
        $rqtDel = "DELETE FROM anim_animation_categories WHERE num_animation='$animationId'";
        pmb_mysql_query($rqtDel);
        $rqtIns = "INSERT INTO anim_animation_categories (num_animation, num_noeud, ordre_categorie) VALUES";
        foreach ($categories as $categ) {
            if (!empty($categ->id)) {
                $rqt = "$rqtIns ('$animationId', '$categ->id', '$ordre')";
                pmb_mysql_query($rqt);
                $ordre++;
            }
        }
    }
}