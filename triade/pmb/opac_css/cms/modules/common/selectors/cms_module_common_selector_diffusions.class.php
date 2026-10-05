<?php

// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_diffusions.class.php,v 1.1.4.2 2025/04/11 10:10:10 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\DSI\Orm\DiffusionOrm;

class cms_module_common_selector_diffusions extends cms_module_common_selector
{

	public function __construct($id = 0)
	{
		parent::__construct($id);
		if (!is_array($this->parameters)) {
			$this->parameters = [];
		}
	}

	public function save_form()
	{
		$this->parameters = $this->get_value_from_form("id_diffusion");
		return parent::save_form();
	}

	public function get_form()
	{
		$form = <<<HTML
			<div class="row">
				<div class="colonne3">
					<label for="cms_module_common_selector_diffusion_id_diffusion">
						{$this->format_text($this->msg['cms_module_common_selector_diffusions_ids'])}
					</label>
				</div>
				<div class="colonne-suite">
					{$this->gen_select()}
				</div>
			</div>
		HTML;

		$form .= parent::get_form();

		return $form;
	}

	private function gen_select(): string
	{
		return <<<HTML
			<select name="{$this->get_form_value_name('id_diffusion')}[] multiple='multiple'">
				{$this->gen_select_options()}
			</select>
		HTML;
	}

	private function gen_select_options(): string
	{
		$diffusions = $this->getOpacDiffusions();
		$options = "";

		if (!empty($diffusions)) {
			foreach ($diffusions as $diffusion) {
				// Déterminer si l'option doit être sélectionnée
				$selected = in_array($diffusion['id'], $this->parameters) ? "selected='selected'" : "";

				$options .= <<<HTML
					<option value="{$diffusion['id']}" {$selected}>
						{$this->format_text($diffusion['name'])}
					</option>
				HTML;
			}
		} else {
			$options .= <<<HTML
				<option value="0">
					{$this->format_text($this->msg['cms_module_common_selector_bannettes_no_bannette'])}
				</option>
			HTML;
		}

		return $options;
	}

	private function getOpacDiffusions(): array
	{
		$result = [];
		$diffusions = DiffusionOrm::finds([
			"settings" => [
				"value" => "%\"opacVisibility\":true%",
				"operator" => "LIKE",
				"inter" => "AND"
			]
		]);
		foreach ($diffusions as $diffusion) {
			if (!$this->checkDiffusionOpacFilters($diffusion)) {
				continue;
			}

			$result[] = [
				"id" => $diffusion->id_diffusion,
				"name" => $diffusion->name
			];
		}
		return $result;
	}

	private function checkDiffusionOpacFilters($diffusion): bool
	{
		$settings = $diffusion->settings;
		if (is_string($settings)) {
			$settings = json_decode($diffusion->settings);
		}

		if (!empty($settings->opacVisibilityCateg)) {
			return false;
		}

		if (!empty($settings->opacVisibilityGroups)) {
			return false;
		}

		return true;
	}

	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value()
	{
		if (!$this->value) {
			$this->value = $this->parameters;
		}
		return $this->value;
	}
}