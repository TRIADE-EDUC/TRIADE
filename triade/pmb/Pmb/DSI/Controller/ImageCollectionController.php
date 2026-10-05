<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ImageCollectionController.php,v 1.2.2.2 2025/03/26 15:33:14 jparis Exp $

namespace Pmb\DSI\Controller;

class ImageCollectionController extends CommonController
{

	protected const VUE_NAME = "dsi/imageCollection";

	/**
	 * Taille maximale des fichiers en Ko
	 */
	protected const MAX_FILE_SIZE = 200;

	/**
	 * Mimetypes autorises
	 */
	protected const ALLOWED_MIMETYPES = ["image/png", "image/jpg", "image/jpeg", "image/gif", "image/webp", "image/bmp", "image/svg+xml"];

	/**
	 * Extensions autorisees
	 */
	protected const ALLOWED_EXTENSIONS = ["png", "jpg", "jpeg", "gif", "webp", "bmp", "svg"];

	/**
	 *
	 * {@inheritDoc}
	 * @see \Pmb\DSI\Controller\CommonController::getBreadcrumb()
	 */
	protected function getBreadcrumb()
	{
		global $msg;
		return "{$msg['dsi_galery']} {$msg['menu_separator']} {$msg['dsi_image_collection']}";
	}

	/**
	 * Affiche la page de la collection d'images.
	 *
	 * @return void
	 */
	protected function defaultAction()
	{
		$this->render();
	}

	/**
	 * Retourne en ajax les parametres de la collection d'images.
	 *
	 *  @return void
	 */

	public function getParameters()
	{
		$this->ajaxJsonResponse($this->fetchParameters());
	}

	/**
	 * Verifie la configuration du repertoire des images
	 * 
	 * @return string|true
	 */
	protected function checkConfiguration(): string|bool
	{
		global $pmb_img_folder, $pmb_img_url, $msg;

		if (empty($pmb_img_url)) {
			return $msg["dsi_image_collection_no_url"];
		}

		$path = rtrim($pmb_img_folder, DIRECTORY_SEPARATOR);
		if (empty($path)) {
			return $msg["dsi_image_collection_no_path"];
		}

		if (!is_dir($path)) {
			if (!@mkdir($path)) {
				return $msg["dsi_image_collection_error_create_folder"] . " : " . $path;
			}
		}

		if (!is_writable($path) || !is_readable($path)) {
			if (!@chmod($path, 0777)) {
				return $msg["dsi_image_collection_error_norights"] . " : " . $path;
			}
		}

		return true;
	}

	/**
	 * Recupere les parametres de la collection d'images.
	 *
	 * @return array
	 */
	private function fetchParameters(): array
	{
		$configurationError = $this->checkConfiguration();
		return [
			"configurationError" => $configurationError !== true ? $configurationError : "",
			"images" => $configurationError === true ? $this->fetchImages() : [],
			"maxFileSize" => self::MAX_FILE_SIZE,
			"allowedMimetypes" => self::ALLOWED_MIMETYPES,
			"allowedExtensions" => self::ALLOWED_EXTENSIONS
		];
	}

	/**
	 * Recupere une liste d'images du repertoire des images.
	 *
	 * @throws \Exception Si le repertoire ne peut pas etre lu.
	 * @return array
	 */
	private function fetchImages(): array
	{
		global $pmb_img_folder, $pmb_img_url;

		$path = rtrim($pmb_img_folder, DIRECTORY_SEPARATOR);
		$entries = @scandir($path);
		if ($entries === false) {
			throw new \Exception("Erreur lors de la lecture du dossier : " . $pmb_img_folder);
		}

		$images = [];
		foreach ($entries as $entry) {
			if ($entry !== '.' && $entry !== '..') {
				$filePath = $path . DIRECTORY_SEPARATOR . $entry;
				if (is_file($filePath)) {
					$extension = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
					if (in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
						$images[] = [
							"name" => $entry,
							"url" => $pmb_img_url . $entry,
							"created_at" => date("Y-m-d H:i:s", filectime($filePath))
						];
					}
				}
			}
		}

		return $images;
	}

	/**
	 * Supprime une image du repertoire des images.
	 *
	 * @return void
	 */
	public function delete()
	{
		global $pmb_img_folder, $msg;

		$configurationError = $this->checkConfiguration();
		if ($configurationError !== true) {
			$this->ajaxError($configurationError);
		}

		if (!isset($this->data->name) || empty($this->data->name)) {
			$this->ajaxError($msg["dsi_image_collection_no_name"]);
		}

		// On s'assure que le nom du fichier ne contient pas de chemin relatif ou absolu
		$filename = basename($this->data->name);
		$path = rtrim($pmb_img_folder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

		if (!is_file($path)) {
			$this->ajaxError($msg["dsi_image_collection_file_not_found"]);
		}

		if (!unlink($path)) {
			$this->ajaxError($msg["dsi_image_collection_file_could_not_be_deleted"]);
		}

		$this->ajaxJsonResponse([
			'success' => true
		]);
	}

	/**
	 * Upload une image vers le serveur.
	 *
	 * @return void
	 */
	public function upload()
	{
		global $pmb_img_url, $msg;

		$configurationError = $this->checkConfiguration();
		if ($configurationError !== true) {
			$this->ajaxError($configurationError);
		}

		// Verifier si des fichiers ont ete envoyes
		if (!isset($_FILES) || empty($_FILES)) {
			$this->ajaxError($msg["dsi_image_collection_file_not_found"]);
			exit();
		}

		$uploadedFiles = [];
		foreach ($_FILES as $file) {
			if (!isset($file['name']) || empty($file['name'])) {
				$this->ajaxError($msg["dsi_image_collection_no_name"]);
			}

			// Creation d'un objet FileInfo pour analyser le type MIME du fichier
			$finfo = new \finfo(FILEINFO_MIME_TYPE);
			$fileMimeType = $finfo->file($file['tmp_name']);

			// Verifier si le type MIME est autorise
			if (!in_array($fileMimeType, self::ALLOWED_MIMETYPES)) {
				$this->ajaxError($msg["dsi_image_collection_invalid_file_type"]);
			}

			$fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);

			// Verifier l'extension du fichier
			if (!in_array(strtolower($fileExtension), self::ALLOWED_EXTENSIONS)) {
				$this->ajaxError($msg["dsi_image_collection_invalid_file_extension"]);
			}

			// Verifier la taille du fichier
			if ($file['size'] > (self::MAX_FILE_SIZE * 1024)) {
				$this->ajaxError($msg["dsi_image_collection_file_to_big"]);
			}

			// Creer un nom de fichier unique
			$path = $this->getUniqueFilePath($file['name']);
			if (empty($path)) {
				$this->ajaxError($msg["dsi_image_collection_file_could_not_be_created"]);
			}

			// Deplacer le fichier vers le dossier cible
			if (!move_uploaded_file($file['tmp_name'], $path)) {
				$this->ajaxError($msg["dsi_image_collection_file_could_not_be_created"]);
			}

			$name = basename($path);
			$uploadedFiles[] = [
				"name" => $name,
				"url" => $pmb_img_url . $name,
				"created_at" => date("Y-m-d H:i:s", filectime($path))
			];
		}

		if (empty($uploadedFiles)) {
			$this->ajaxError($msg["dsi_image_collection_file_could_not_be_created"]);
		}

		$this->ajaxJsonResponse($uploadedFiles);
	}


	/**
	 * Nettoyer le nom de fichier en remplaçant les caracteres problematiques.
	 *
	 * @param string $filename
	 * @return string
	 */
	private function sanitizeFilename(string $filename): string
	{
		if (empty($filename)) {
			return $filename;
		}

		// Retirer les espaces en debut et fin
		$filename = trim($filename);

		// Convertir tous les caracteres en minuscules (optionnel)
		$filename = strtolower($filename);

		// Convertir les caracteres accentues en caracteres non accentues
		$filename = transliterator_transliterate('Any-Latin; Latin-ASCII', $filename);

		// Remplacer les espaces par des undrerscores
		$filename = str_replace(' ', '_', $filename);

		// Supprimer tous les caracteres non-alphanumeriques a l'exception des underscores, des tirets et des points
		$filename = preg_replace('/[^a-z0-9\-\.\_]/', '', $filename);

		// eviter que le nom de fichier commence ou se termine par un tiret
		$filename = trim($filename, '-');

		return $filename;
	}

	/**
	 * Fonction qui nettoie et genere un path unique
	 *
	 * @param string $filename
	 * @return string|false
	 */
	private function getUniqueFilePath(string $filename): string|false
	{
		global $pmb_img_folder;

		if (empty($filename)) {
			return false;
		}

		// Nettoyer le nom de fichier en remplaçant les caracteres problematiques
		$sanitizedFilename = $this->sanitizeFilename($filename);

		// Recuperer le nom du fichier et son extension
		$pathInfo = pathinfo($sanitizedFilename);
		$baseFilename = $pathInfo['filename'];
		$extension = $pathInfo['extension'];

		// Preparer le chemin de base
		$basePath = rtrim($pmb_img_folder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $sanitizedFilename;

		// Si le fichier existe deja, on incremente le nom
		$i = 1;
		$path = $basePath;
		while (file_exists($path)) {
			// Generer un nouveau nom en ajoutant un suffixe (par exemple " (1)", " (2)", etc.)
			$baseFilename = sprintf('%s(%d)', $pathInfo['filename'], $i);
			$path = rtrim($pmb_img_folder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $baseFilename . '.' . $extension;
			$i++;
		}

		// On definit le nom final du fichier et son chemin complet
		$finalFileName = ($i === 1) ? $sanitizedFilename : $baseFilename . '.' . $extension;
		return rtrim($pmb_img_folder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $finalFileName;
	}
}

