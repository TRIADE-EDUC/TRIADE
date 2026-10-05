<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_diffusionslistabon_view_diffusionslistabon.class.php,v 1.1.4.2 2025/04/11 10:10:09 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_diffusionslistabon_view_diffusionslistabon extends cms_module_common_view_django
{

    public function __construct($id = 0)
    {
        parent::__construct($id);
        $this->default_template = "{% include './includes/templates/cms/modules/diffusionslistabon/cms_module_diffusionslistabon_view.tpl.html' %}";
    }

    public function get_form()
    {
        $content = parent::get_form();
        
        return <<<HTML
            <div class='row'>
                <div class='colonne3'>
                    <label for='cms_module_common_diffusionslistabon_view_link'>
                        {$this->format_text($this->msg['cms_module_common_view_diffusionslistabon_build_diffusion_link'])}
                    </label>
                </div>
                <div class='colonne_suite'>
                    {$this->get_constructor_link_form("diffusion")}
                </div>
            </div>
        
            {$content}
        
            <div class='row'>
                <div class='colonne3'>
                    <label for='cms_module_diffusionslistabon_view_diffusionslistabon_css'>
                        {$this->format_text($this->msg['cms_module_diffusionslistabon_view_diffusionslistabon_css'])}
                    </label>
                </div>
                <div class='colonne-suite'>
                    <textarea name='cms_module_diffusionslistabon_view_diffusionslistabon_css'>
                        {$this->format_text($this->parameters['css'] ?? '')}
                    </textarea>
                </div>
            </div>
        HTML;
    }

    public function save_form()
    {
        global $cms_module_diffusionslistabon_view_diffusionslistabon_css;

        $this->save_constructor_link_form("diffusion");
        $this->parameters['css'] = stripslashes($cms_module_diffusionslistabon_view_diffusionslistabon_css);

        return parent::save_form();
    }


    public function render($data)
    {
        $data['empr']['captcha'] = emprunteur_display::get_captcha();
        $data['empr']['password_rules'] = emprunteur::get_json_enabled_password_rules(0);
        $data['module']['msg'] = $this->msg;
        
        $rendered = parent::render($data);

        return $rendered;
    }


    /**
     * Permet d'ajouter des meta dans la page en OPAC
     *
     * @param array $data
     * @return array
     */
    public function get_headers($data = [])
    {
        global $opac_url_base;

        return [
            "add" => [
                '<script src="' . $opac_url_base . 'includes/javascript/misc.js"></script>',
                '<script src="' . $opac_url_base . 'includes/javascript/ajax.js"></script>',
            ],
        ];
    }

    protected function get_format_data_diffusion_structure($prefix)
    {
        return array(
            array(
                'var' => $prefix . ".id",
                'desc' => $this->msg['cms_module_diffusionslistabon_view_diffusions_id_desc']
            ),
            array(
                'var' => $prefix . ".name",
                'desc' => $this->msg['cms_module_diffusionslistabon_view_diffusions_name_desc']
            ),
            array(
                'var' => $prefix . ".lastDiffusion",
                'desc' => $this->msg['cms_module_diffusionslistabon_view_diffusions_last_desc']
            ),
            array(
                'var' => $prefix . ".nbResults",
                'desc' => $this->msg['cms_module_diffusionslistabon_view_diffusions_results_desc']
            ),
            array(
                'var' => $prefix . ".isSubscribed",
                'desc' => $this->msg['cms_module_diffusionslistabon_view_diffusions_subscribed_desc']
            )
        );
    }

    public function get_format_data_structure()
    {
        return array_merge(array(
            array(
                'var' => "diffusions",
                'desc' => $this->msg['cms_module_diffusionslistabon_view_diffusions_desc'],
                'children' => $this->get_format_data_diffusion_structure("diffusions[i]")
            ),
            array(
                'var' => "tags",
                'desc' => $this->msg['cms_module_diffusionslistabon_view_categories_desc'],
                'children' => array(
                    array(
                        'var' => "tags[h].id",
                        'desc' => $this->msg['cms_module_diffusionslistabon_view_diffusions_id_tag_desc']
                    ),
                    array(
                        'var' => "tags[h].name",
                        'desc' => $this->msg['cms_module_diffusionslistabon_view_diffusions_name_desc']
                    ),
                    array(
                        'var' => "tags[h].diffusions",
                        'desc' => $this->msg['cms_module_diffusionslistabon_view_diffusions_desc'],
                        'children' => $this->get_format_data_diffusion_structure("tags[h].diffusions[i]")
                    )
                )
            )
        ), parent::get_format_data_structure());
    }
}
