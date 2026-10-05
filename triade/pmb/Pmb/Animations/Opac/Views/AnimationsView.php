<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationsView.php,v 1.2 2021/03/11 13:41:40 qvarin Exp $
namespace Pmb\Animations\Opac\Views;

use Pmb\Animations\Views\AnimationsView as View;

global $base_path;
require_once($base_path."/includes/securimage/securimage.php");

class AnimationsView extends View
{

    private $activeCaptcha = false;

    protected $distPath = "./includes/javascript/vuejs/";

    protected $captchaPath = "./includes/securimage/securimage.js";

    public function use_captcha(string $input_name = 'captcha_code')
    {
        global $base_path, $lang;
        
        // On active le captcha pour que le script soit ajouté.
        $this->activeCaptcha = true;
        
        // On envoi la lang de l'opac à securimage
        $_SESSION['captcha_lang'] = $lang;
        
        // On définit les paramètres
        $options = array();
        $options['input_name'] = $input_name;
        $options['securimage_path'] = $base_path . "/includes/securimage";
        $options['disable_flash_fallback'] = false;
        $options['input_text'] = '';
        $options['show_text_input'] = 0;
        
        // On ajoute le template dans les données
        $this->data['captchaTemplate'] = \Securimage::getCaptchaHtml($options);
    }

    public function render()
    {
        $content = parent::render();
        if ($this->activeCaptcha) {
            $content .= "<script type='text/javascript' src='" . $this->captchaPath . "'></script>";
        }
        return $content;
    }
}