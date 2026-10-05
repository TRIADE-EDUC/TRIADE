<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomizableView.php,v 1.6 2023/11/10 15:30:15 jparis Exp $
namespace Pmb\DSI\Models\View;

use Pmb\DSI\Models\View\RootView;

class CustomizableView extends RootView
{

	/**
	 * Retourne un tableau de contexte à passer à H2o
	 * en fonction des champs custom
	 *
	 * @return array
	 */
	protected function getH2oAdditionnalContext()
	{
		if (empty($this->settings->customizableFields)) {
			return array();
		}

		$context = array();
		foreach ($this->settings->customizableFields as $field) {
			$method = "getData" . ucfirst($field->type);
			if (method_exists($this, $method)) {
				$this->$method($field, $context);
			}
		}
		return $context;
	}

    public function getDefaultStructureData() {
        global $msg;

        return array(
            'var' => $msg["customizable_fields_data_structure"],
            'desc' => $msg['customizable_fields_data_structure_desc'],
            'children' => []);
        
    }

	/**
	 * Ajout d'un champ de type texte au contexte
	 *
	 * @param \stdClass $field
	 * @param array $context
	 */
	protected function getDataText($field, &$context)
	{
		$value = "";

		if (! empty($field->data) && ! empty($field->data->value)) {
			$value = $field->data->value;
		}

		$context[$field->templateName] = $value;
	}

    public function getStructureDataText($field) {
        return array('var' => $field->templateName, 'desc' => '');
    }

	/**
	 * Ajout d'un champ de type color au contexte
	 *
	 * @param \stdClass $field
	 * @param array $context
	 */
	protected function getDataColor($field, &$context)
	{
		$value = "";

		if (! empty($field->data) && ! empty($field->data->value)) {
			$value = $field->data->value;
		}

		$context[$field->templateName] = $value;
	}

    public function getStructureDataColor($field) {
        return array('var' => $field->templateName, 'desc' => '');
    }

	/**
	 * Ajout d'un champ de type selecteur au contexte
	 *
	 * @param \stdClass $field
	 * @param array $context
	 */
	protected function getDataSelector($field, &$context)
	{
		$value = "";

		if (! empty($field->data) && ! empty($field->data->value)) {
			$value = $field->data->value;
		}

		$context[$field->templateName] = $value;
	}

    public function getStructureDataSelector($field) {
        $structureData = array('var' => $field->templateName, 'desc' => '');
        if (isset($field->data->multiple) && $field->data->multiple) {
            $structureData['children'] = [['var' => $field->templateName . '[i]', 'desc' => '']];
        }

        return $structureData;
    }

	/**
	 * Ajout d'un champ de type liste au contexte
	 *
	 * @param \stdClass $field
	 * @param array $context
	 */
	protected function getDataList($field, &$context)
	{
		$value = array();

		if (! empty($field->data) && ! empty($field->data->list)) {
			foreach($field->data->list as $element) {
				$value[] = $element->value;
			}
		}

		$context[$field->templateName] = $value;
	}
    public function getStructureDataList($field) {
        return array(
            'var' => $field->templateName,
            'desc' => '',
            'children' => [['var' => $field->templateName . '[i]', 'desc' => '']]
        );
    }
    
    /**
     * Ajout d'un champ de type dimension au contexte
     *
     * @param \stdClass $field
     * @param array $context
     */
    protected function getDataDimension($field, &$context)
    {
        $value = "";
        
        if (! empty($field->data) && ! empty($field->data->values)) {
            $value = $field->data->values->value . $field->data->values->dimension;
        }
        
        $context[$field->templateName] = $value;
    }

    public function getStructureDataDimension($field) {
        return array('var' => $field->templateName, 'desc' => '');
    }
}