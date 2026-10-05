<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationsController.php,v 1.19.4.1 2025/04/07 15:18:01 tsamson Exp $
namespace Pmb\Animations\Opac\Controller;

use Pmb\Common\Opac\Controller\Controller;
use Pmb\Animations\Opac\Models\AnimationModel;
use Pmb\Animations\Opac\Views\AnimationsView;
use Pmb\Animations\Opac\Models\RegistrationModel;
use Pmb\Animations\Orm\AnimationOrm;

class AnimationsController extends Controller
{

    public function proceed($categ = '')
    {
        switch ($categ) {
            case 'see':
                return $this->AnimationSeeAction($this->data->id, $this->data->empr_id);
            case 'list':
                return $this->AnimationSeeAllAction();
            default:
                return '';
        }
    }

    public function AnimationSeeAction(int $id, int $emprId)
    {
        global $base_path;

        if (!AnimationOrm::exist($id)) {
            return $this->AnimationSeeAllAction();
        }
        
        $template_path = "$base_path/includes/templates/animations/common/animation_display.tpl.html";
        if (file_exists("$base_path/includes/templates/animations/common/animation_display_subst.tpl.html")) {
            $template_path = "$base_path/includes/templates/animations/common/animation_display_subst.tpl.html";
        }
        
        $H2o = \H2o_collection::get_instance($template_path);
        $context = $this->getAnimationRenderContext($id, $emprId);

        $animationTemplate = $H2o->render($context);

        $view = new AnimationsView('animations/animations', [
            'animations' => [
                'render' => $animationTemplate
            ]
        ]);
        print $view->render();
    }

    public function AnimationSeeAllAction()
    {
        global $base_path;

        $template_path = "$base_path/includes/templates/animations/common/animations_list.tpl.html";
        if (file_exists("$base_path/includes/templates/animations/common/animations_list_subst.tpl.html")) {
            $template_path = "$base_path/includes/templates/animations/common/animations_list_subst.tpl.html";
        }

        $H2o = \H2o_collection::get_instance($template_path);
        $animationTemplate = $H2o->render([
            'animations' => AnimationModel::getAnimationsList(),
            'formData' => [
                'registrationAllowed' => RegistrationModel::registrationAllowed()
            ]
        ]);

        $view = new AnimationsView('animations/animations', [
            'animations' => [
                'render' => $animationTemplate
            ],
            'action' => "list"
        ]);
        print $view->render();
    }
    
    public function getAnimationRenderContext(int $id, int $emprId)
    {
        global $pmb_gestion_devise;
        
        $animation = new AnimationModel($id);
        $registration = new RegistrationModel(RegistrationModel::getIdRegistrationFromEmprAndAnimation($emprId, $id));
        
        $animation->getViewData();
        if ($animation->hasChildrens) {
            foreach ($animation->childrens as $children) {
                $idRegistration = RegistrationModel::getIdRegistrationFromEmprAndAnimation($emprId, $children->id);
                $children->alreadyRegistred = false;
                if ($idRegistration != 0) {
                    $children->alreadyRegistred = true;
                }
            }
        }
        if (isset($animation->childrens)) {
            usort($animation->childrens, function ($a, $b) {
                $startDateA = strtotime($a->event->rawStartDate);
                $startDateB = strtotime($b->event->rawStartDate);
                
                return $startDateA - $startDateB;
            });
        }
        $context = [
            'animation' => $animation,
            'registration' => $registration->getViewData($emprId),
            'formData' => [
                'registrationAllowed' => RegistrationModel::registrationAllowed(),
                'globals' => [
                    'pmbDevise' => html_entity_decode($pmb_gestion_devise)
                ]
            ]
        ];
        return $context;
    }
}