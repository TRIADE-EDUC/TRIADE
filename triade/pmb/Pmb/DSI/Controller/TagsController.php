<?php
namespace Pmb\DSI\Controller;

use Pmb\DSI\Models\Tag;

class TagsController extends CommonController
{

	public function save()
	{
		$tag = new Tag();

		$result = $tag->check($this->data);
		if (isset($result['error']) && $result['error'] == true) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}

		$tag->setFromForm($this->data);
		if (empty($this->data->id)) {
			$tag->create();
		} else {
			$tag->update();
		}
		$this->ajaxJsonResponse($tag);
		exit();
	}

	public function delete()
	{
		$tag = new Tag($this->data->id);
		$result = $tag->delete();

		if (isset($result['error']) && $result['error'] == true) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}
		$this->ajaxJsonResponse([
			'success' => true
		]);
		exit();
	}

	public function getRelatedEntities(int $numTag)
	{
		$tagModel = new Tag();
		$entities = $tagModel->getRelatedEntities($numTag);
		$this->ajaxJsonResponse($entities);
	}
	
	public function getTags()
	{
		$this->ajaxJsonResponse((new Tag())->getTags());
	}
}

