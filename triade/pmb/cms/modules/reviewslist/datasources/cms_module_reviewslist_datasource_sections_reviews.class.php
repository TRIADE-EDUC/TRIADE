<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_reviewslist_datasource_sections_reviews.class.php,v 1.2 2022/08/04 14:13:00 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_reviewslist_datasource_sections_reviews extends cms_module_common_datasource_reviews{
    
    protected const OBJECT_TYPE = AVIS_SECTIONS;
}