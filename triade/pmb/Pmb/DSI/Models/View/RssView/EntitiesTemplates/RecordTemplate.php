<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordTemplate.php,v 1.2 2023/05/31 07:35:16 qvarin Exp $

namespace Pmb\DSI\Models\View\RssView\EntitiesTemplates;

class RecordTemplate extends RootTemplate implements TemplateInterface
{
    protected $id;

    protected $notice;

    public function __construct(int $id)
    {
        global $opac_notice_affichage_class, $liens_opac;

        $this->id = $id;

        if ($opac_notice_affichage_class != "") {
            $classname = "\\{$opac_notice_affichage_class}";
            $this->notice = new $classname($id, $liens_opac, "", 1, 0, 0, 1, true);
        } else {
            $this->notice = new \notice_affichage($id, $liens_opac, "", 1, 0, 0, 1, true);
        }
    }

    public function getTitle($tplTitle)
    {
        global $deflt2docs_location;

        $title = '';
        if ($tplTitle) {
            if (intval($tplTitle)) {
                $tplTitle = intval($tplTitle);
                $noticeTemplateGen = \notice_tpl_gen::get_instance($tplTitle);
                $title .= $noticeTemplateGen->build_notice($this->id, $deflt2docs_location);
            } else {
                $title .= \record_display::get_display_for_rss_title($this->id, $tplTitle);
            }
        } else {
            $this->notice->do_header_without_html();
            $title .= $this->notice->notice_header_without_html;
        }

        return $title;
    }

    public function getLink($tplLink)
    {
        global $liens_opac;
        return str_replace('!!id!!', $this->id, $liens_opac['lien_rech_notice']);
    }

    public function getDescription($tplDescription)
    {
        global $deflt2docs_location;

        $desc = "";
        if ($tplDescription) {
            if (intval($tplDescription)) {
                $tplDescription = intval($tplDescription);
                $noticeTemplateGen = \notice_tpl_gen::get_instance($tplDescription);
                $desc = $noticeTemplateGen->build_notice($this->id, $deflt2docs_location);
            } else {
                $desc = \record_display::get_display_for_rss_description($this->id, $tplDescription);
            }
        } else {
            switch ($this->format_flux) {
                case 'TITLE':
                    $desc='';
                    break;
                case 'ABSTRACT':
                    $desc = $this->notice->notice->n_resume.'<br />';
                    break;
                case 'ISBD':
                default:
                    $this->notice->do_isbd(0, 0);
                    $desc = $this->notice->notice_isbd;
                    break;
            }
        }

        return $desc;
    }

    public static function getTemplates()
    {
        global $msg;

        $templates = array_merge([
            0 => $msg['notice_tpl_list_default'],
        ], \notice_tpl::get_list());

        $directories = \record_display::get_directories();
        foreach ($directories as $value => $label) {
            $templates[$value] = $label;
        }

        return [
            "tplTitle" => $templates,
            "tplDescription" => $templates
        ];
    }
}
