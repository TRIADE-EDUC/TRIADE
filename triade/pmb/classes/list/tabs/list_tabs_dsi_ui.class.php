<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_dsi_ui.class.php,v 1.13.2.1.2.1 2025/03/26 15:33:11 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_tabs_dsi_ui extends list_tabs_ui {

	protected function _init_tabs() {
		global $dsi_active;

		if (2 == $dsi_active) {
			$this->add_tab('dsi_menu', 'diffusions', 'dsi_diffusions');
			$this->add_tab('dsi_menu', 'products', 'dsi_products');
			$this->add_tab('dsi_menu', 'diffusions_history', 'dsi_history_dashboard');
			$this->add_tab('dsi_menu', 'diffusions_pending', 'dsi_sending_pending');
			$this->add_tab('dsi_menu', 'diffusions_private', 'dsi_private');

			$this->add_tab('dsi_models', 'items', 'dsi_items');
			$this->add_tab('dsi_models', 'views', 'dsi_views');
			$this->add_tab('dsi_models', 'triggers', 'dsi_triggers');
			$this->add_tab('dsi_models', 'subscriber_list', 'dsi_subscriber_list');
			$this->add_tab('dsi_models', 'channels', 'dsi_channels');

			$this->add_tab('dsi_galery', 'image_collection', 'dsi_image_collection');

			$this->add_tab('dsi_statutes', 'status_diffusions', 'dsi_diffusions');
			$this->add_tab('dsi_statutes', 'status_products', 'dsi_products');

			//Flux RSS
			$this->add_tab('dsi_menu_flux', 'fluxrss', 'dsi_menu_flux_definition', 'definition');

            //Veilles
            $this->add_tab('dsi_menu_docwatch', 'docwatch', 'dsi_menu_docwatch_definition');
			return;
		}

		//Diffusion
		$this->add_tab('dsi_menu_diffusion', 'diffuser', 'dsi_menu_dif_lancer', 'lancer');
		$this->add_tab('dsi_menu_diffusion', 'diffuser', 'dsi_menu_dif_auto', 'auto');
		$this->add_tab('dsi_menu_diffusion', 'diffuser', 'dsi_menu_dif_manu', 'manu');
		$this->add_tab('dsi_menu_diffusion', 'diffuser', 'dsi_menu_dif_history', 'history');

		//Bannettes
		$this->add_tab('dsi_menu_bannettes', 'bannettes', 'dsi_menu_ban_pro', 'pro');
		$this->add_tab('dsi_menu_bannettes', 'bannettes', 'dsi_menu_ban_abo', 'abo');

		//Equations
		$this->add_tab('dsi_menu_equations', 'equations', 'dsi_menu_equ_gestion', 'gestion');

		//Options
		$this->add_tab('dsi_menu_options', 'options', 'dsi_menu_cla_gestion', 'classements');

		//Flux RSS
		$this->add_tab('dsi_menu_flux', 'fluxrss', 'dsi_menu_flux_definition', 'definition');

		//Veilles
		$this->add_tab('dsi_menu_docwatch', 'docwatch', 'dsi_menu_docwatch_definition');

		//Migration vers nouvelle DSI
		$this->add_tab('dsi_menu_migration', 'migrate', 'dsi_menu_migration_definition');
	}
}