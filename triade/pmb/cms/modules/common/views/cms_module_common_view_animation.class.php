<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_animation.class.php,v 1.3 2021/04/01 12:00:32 qvarin Exp $

use Pmb\Animations\Models\AnimationModel;

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

class cms_module_common_view_animation extends cms_module_common_view_django
{

    public function __construct($id = 0)
    {
        parent::__construct($id);
        $this->default_template = "<h3>{{ animation.name }}</h3>
<p>{{ animation.description }}</p>";
    }

    public function get_format_data_structure()
    {
        $animation = new AnimationModel();
        return array_merge($animation->getCmsStructure('animation'), parent::get_format_data_structure());
    }
}