<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ProductsController.php,v 1.17.2.1.2.1 2025/05/21 12:31:40 rtigero Exp $
namespace Pmb\DSI\Controller;

use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Models\Product;
use Pmb\DSI\Models\DiffusionProduct;
use Pmb\DSI\Models\ProductStatus;
use Pmb\DSI\Models\EventProduct;
use Pmb\DSI\Orm\ProductOrm;

class ProductsController extends CommonController
{

	protected const VUE_NAME = "dsi/products";

	/**
	 *
	 * {@inheritdoc}
	 * @see \Pmb\DSI\Controller\CommonController::getBreadcrumb()
	 */
	protected function getBreadcrumb()
	{
		global $msg, $action, $id;
		$breadcrum = "{$msg['dsi_menu']} {$msg['menu_separator']} {$msg['dsi_products']}";

		if (isset($action) && $action == "edit" && isset($id)) {
			if (ProductOrm::exist($id)) {
				$product = ProductOrm::findById($id);
				$breadcrum .= " {$msg['menu_separator']} {$product->name}";
			}
		}
		return $breadcrum;
	}

	protected function defaultAction()
	{
		$product = new Product();
		$productStatus = new ProductStatus();

		$this->render([
			"list" => $product->getList(),
			"productStatus" => $productStatus->getList()
		]);
	}

	protected function addAction()
	{
		$this->render($this->getFormData());
	}

	protected function editAction()
	{
		global $id;

		$id = intval($id);
		if (ProductOrm::exist($id)) {
			$this->render($this->getFormData($id));
		} else {
			global $msg;
			$this->notFound(
				sprintf($msg['product_not_found'], strval($id)),
				"./dsi.php?categ=products"
			);
		}
	}

	/**
	 * Recuperation donnees formulaire ajout/edition
	 *
	 * @param int $id
	 * @return array[]
	 */
	protected function getFormData($id = 0)
	{
		$data = array();

		$data['product'] = new Product($id);
		$data['productDiffusion'] = new DiffusionProduct();

		$diffusion = new Diffusion();

		$data["diffusions"] = $diffusion->getFilteredList();

		$productStatus = new ProductStatus();
		$data["productStatus"] = $productStatus->getList();

		return $data;
	}
	public function save()
	{
		$this->data->id = intval($this->data->id);
		$product = new Product($this->data->id);
		$result = $product->check($this->data);
		if ($result['error']) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}

		$product->setFromForm($this->data);
		if (0 == $this->data->id) {
			$product->create();
		} else {
			$product->update();
		}
		//Save du product diffusion
		foreach ($this->data->productDiffusions as $diffusionProduct) {
			$diffusionProductModel = new DiffusionProduct($diffusionProduct->num_diffusion, $product->id);
			$result = $diffusionProductModel->check($diffusionProduct);
			if (!$result) {
				$this->ajaxError($result['errorMessage']);
				exit();
			}

			$diffusionProductModel->setFromForm($diffusionProduct);
			if ((isset($diffusionProduct->num_product) && 0 == $diffusionProduct->num_product) || (isset($diffusionProduct->num_diffusion) && 0 == $diffusionProduct->num_diffusion)) {
				$diffusionProductModel->create();
			} else {
				$diffusionProductModel->update();
			}
		}

		//Save des events du produit
		foreach ($this->data->events as $eventProduct) {
			$eventProductModel = new EventProduct($eventProduct->id, $product->id);
			$result = $eventProductModel->check($eventProduct);
			if (!$result) {
				$this->ajaxError($result['errorMessage']);
				exit();
			}

			if ((isset($eventProduct->id) && 0 == $eventProduct->id)) {
				$eventProductModel->create();
			} else {
				$eventProductModel->update();
			}
		}

		$this->ajaxJsonResponse($product);
		exit();
	}

	public function deleteProductDiffusion()
	{
		$productDiffusion = new DiffusionProduct($this->data->num_diffusion, $this->data->num_product);
		$result = $productDiffusion->delete();

		if ($result['error']) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}
		$this->ajaxJsonResponse([
			'success' => true
		]);
		exit();
	}

	public function delete()
	{
		if (!ProductOrm::exist($this->data->id)) {
			$this->ajaxError("Ce produit n'existe pas");
			exit();
		}
		$product = new Product($this->data->id);
		//On supprime les liens
		foreach ($product->productDiffusions as $productDiffusion) {
			$productDiffusion = new DiffusionProduct($productDiffusion->num_diffusion, $product->id);
			$result = $productDiffusion->delete();

			if ($result['error']) {
				$this->ajaxError($result['errorMessage']);
				exit();
			}
		}


		$result = $product->delete();

		if ($result['error']) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}
		$this->ajaxJsonResponse([
			'success' => true
		]);
		exit();
	}

	/**
	 * relie un tag a l'entite
	 */
	public function unlinkTag()
	{
		$product = new Product();
		$delete = $product->unlinkTag($this->data->numTag, $this->data->numEntity);
		$this->ajaxJsonResponse($delete);
	}

	/**
	 * Supprime le lien entre un tag et une entite
	 */
	public function linkTag()
	{
		$product = new Product();
		$link = $product->linkTag($this->data->numTag, $this->data->numEntity);
		$this->ajaxJsonResponse($link);
	}

	public function deleteAll()
	{
		foreach ($this->data->ids as $id) {
			$product = new Product($id);
			$result = $product->delete();
			if ($result["error"]) {
				$this->ajaxError($result['errorMessage']);
			}
		}
		$this->ajaxJsonResponse(['success' => true]);
	}

	public function importModelTags()
	{
		$product = new Product($this->data->numEntity);
		$product->importModelTags();
		$this->ajaxJsonResponse($product->tags);
	}
}
