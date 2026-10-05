<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_recordslist_view_dynamic_grid.class.php,v 1.5 2023/06/07 10:27:46 tsamson Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

global $include_path;
require_once ($include_path . "/h2o/h2o.php");

class cms_module_recordslist_view_dynamic_grid extends cms_module_common_view_dynamic_grid
{

    public function get_form()
    {
        $form = "";
        $form .= parent::get_form();
        $form .= "
        <div class='row'>
            <div class='colonne3'>
                <label for='cms_module_common_view_django_template_record_content'>" . $this->format_text($this->msg['cms_module_common_view_django_template_record_content']) . "</label>
            </div>
            <div class='colonne-suite'>
                " . notice_tpl::gen_tpl_select("cms_module_common_view_django_template_record_content", $this->parameters['used_template']) . "
            </div>
            </div>
        </div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_recordslist_view_link'>" . $this->format_text($this->msg['cms_module_recordslist_view_link']) . "</label>
			</div>
			<div class='colonne-suite'>
                " . $this->get_constructor_link_form("notice") . "
			</div>
		</div>";
        return $form;
    }

    public function save_form()
    {
        global $cms_module_shelveslist_view_django_used_template;
        $this->parameters['used_template'] = $cms_module_shelveslist_view_django_used_template;
        $this->save_constructor_link_form("notice");
        return parent::save_form();
    }

    public function render($datas)
    {
        global $opac_show_book_pics;
        global $opac_book_pics_url;
        global $opac_notice_affichage_class;

        $render_datas = array();

        if (empty($opac_notice_affichage_class)) {
            $opac_notice_affichage_class = "notice_affichage";
        }

        $add_to_cart_link = '';

        $query = "SELECT notice_id, tit1, thumbnail_url, code, typdoc, niveau_biblio FROM notices WHERE notice_id IN ('" . implode("','", $datas['records'] ?? []) . "') ORDER BY field( notice_id, '" . implode("','", $datas['records'] ?? []) . "')";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
            while ($row = pmb_mysql_fetch_object($result)) {
                $url_vign = "";
                $url_vign = $thumbnailSourcesHandler->generateUrl(TYPE_NOTICE, $row->notice_id);
                if (empty($url_vign)) {
                    $url_vign = notice::get_picture_url_no_image($row->niveau_biblio, $row->typdoc);
                }

                $notice_class = new $opac_notice_affichage_class($row->notice_id);
                $notice_class->do_header();

                if ($this->parameters['used_template']) {
                    $tpl = notice_tpl_gen::get_instance($this->parameters['used_template']);
                    $content = $tpl->build_notice($row->notice_id);
                } else {
                    $notice_class->do_isbd();
                    $content = $notice_class->notice_isbd;
                }

                $render_datas[] = array(
                    'id' => $row->notice_id,
                    'title' => $row->tit1,
                    'link' => $this->get_constructed_link("notice", $row->notice_id),
                    'vign' => $url_vign,
                    'header' => $notice_class->notice_header,
                    'content' => $content
                );
            }

            $add_to_cart_link = '<span class="addCart">
							<a title="' . $this->msg['cms_module_recordslist_view_add_cart_link'] . '" target="cart_info" href="cart_info.php?notices=' . implode(",", $datas['records']) . '">' . $this->msg['cms_module_recordslist_view_add_cart_link'] . '</a>
						  </span>';
        }
        $render_datas = array(
            'title' => $datas['title'] ?? "",
            'records' => $render_datas,
            'add_to_cart_link' => $add_to_cart_link
        );
        return parent::render($render_datas);
    }

    public function get_format_data_structure()
    {
        $datas = new cms_module_carousel_datasource_notices();
        $format_datas = $datas->get_format_data_structure();
        $format_datas[0]['children'][] = array(
            'var' => "records[i].header",
            'desc' => $this->msg['cms_module_common_view_record_header_desc']
        );
        $format_datas[0]['children'][] = array(
            'var' => "records[i].content",
            'desc' => $this->msg['cms_module_common_view_slideshow_record_content_desc']
        );
        $format_datas[] = array(
            'var' => "add_to_cart_link",
            'desc' => $this->msg['cms_module_recordslist_view_add_cart_link_desc']
        );
        $format_datas = array_merge($format_datas, parent::get_format_data_structure());
        return $format_datas;
    }
}