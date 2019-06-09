<?php

namespace App\Libraries;
/*
 *
 * Edit Gallery class.
 *
 * Author : Trim Camaj
 *
 * Description : This class extends the main (Gallery) class and is used for all XML manipulations such as removing or modifying datas
 *
 */
class EditGallery extends Gallery
{
	public function __construct()
	{
	}

	/**
	 * Adds image infos in a XML file
	 *
	 * Adds images informations in the provided image XML file
	 *
	 * @param	string		$timestamp		Current timestamp
	 * @param 	string		$image_name		The name of the image
	 * @param 	int			$image_gallery	The image's gallery
	 * @param 	string		$image_title	The image's title, can be empty
	 * @return 	void
	 **/
	public function addImage($timestamp, $image_name, $image_gallery, $image_title)
	{
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = FALSE;
		$dom->formatOutput = TRUE;
		$dom->load(storage_path('app/' . Gallery::_IMAGES_FILE_PATH));

		$title = $dom->createElement('title');
		$cdata = $dom->createCDATASection($image_title);

		$title->setAttribute('timestamp', $timestamp);
		$title->setAttribute('fileName', $image_name);
		$title->setAttribute('gallery', $image_gallery);

		$dom->documentElement->appendChild($title);
		$title->appendChild($cdata);

		$dom->save(storage_path('app/' . Gallery::_IMAGES_FILE_PATH));
	}
	/**
	 * Modifies image infos in a XML file
	 *
	 * Modify image's informations in the provided image XML file
	 *
	 * @param 		string		$timestamp			Image's timestamp
	 * @param 		string		$image_name			The name of the image to modify
	 * @param 		int			$image_gallery		The image's gallery to modify
	 * @param 		string		$image_title		The image's title to modify, can be empty
	 * @param 		bool		$is_same_gallery	If set to true, only the image's title will be changed otherwise with the default value set to false it will modify all the informations accordignly
	 * @return		bool							Returns true if the modifications were saved in the provided XML, returns false if it failed in the process
	 **/
	public function modifyImage($timestamp, $image_name, $image_gallery, $image_title, $is_same_gallery = false)
	{
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->load(storage_path('app/' . Gallery::_IMAGES_FILE_PATH));
		$xpath = new \DOMXpath($dom);
		$targets = $xpath->query('/images/title[@fileName="' . $image_name . '"]');
		if ($targets && $targets->length > 0) {
			$target = $targets->item(0);
			if (!$is_same_gallery) {
				$target->setAttribute('timestamp', $timestamp);
				$target->setAttribute('fileName', $image_name);
				$target->setAttribute('gallery', $image_gallery);
				$cdata = $dom->createCDATASection($image_title);
				$target->replaceChild($cdata, $target->firstChild);
			} else {
				$cdata = $dom->createCDATASection($image_title);
				$target->replaceChild($cdata, $target->firstChild);
			}
			//$target->nodeValue = 'Texte modifié';
		}
		if ($dom->save(storage_path('app/' . Gallery::_IMAGES_FILE_PATH))) {
            return true;
        } else {
            return false;
        }
	}
	/**
	 * Removes image infos in a XML file
	 *
	 * Removes image's informations in the provided image XML file
	 *
	 * @param 	string	$image_name		The name of the image to remove from the provided XML file
	 * @return 	void
	 **/
	public function removeImage($image_name)
	{
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->load(storage_path('app/' . Gallery::_IMAGES_FILE_PATH));
		$images = $dom->documentElement;
		$img = $images->getElementsByTagName('title');
		foreach($img as $image)	{
			if ($image->hasAttribute('fileName') == $image_name) {
				if ($image->getAttribute('fileName') == $image_name) {
                    $images->removeChild($image);
                }
			}
		}
		$dom->save(storage_path('app/' . Gallery::_IMAGES_FILE_PATH));

		\File::delete(storage_path('app/public/images_gallery/big/' . $image_name));
		\File::delete(storage_path('app/public/images_gallery/min/' . $image_name));
	}
	/**
	 * Adds a new gallery in a XML file
	 *
	 * Adds a new gallery in the provided gallery XML file
	 *
	 * @param 	string	$gallery_name	The name of the new gallery to be added
	 * @return 	void
	 **/
	public function addGallery($gallery_name)
	{
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = FALSE;
		$dom->formatOutput = TRUE;
		$dom->load(storage_path('app/' . Gallery::_GALLERIES_FILE_PATH));

		$node = $dom->createElement('name');
		$cdata = $dom->createCDATASection($gallery_name);

		$maxID = $this->getMaxGalleryId();
		if ($maxID != 0) {
            $node->setAttribute('galleryID', $maxID + 1);
        } else {
            $node->setAttribute('galleryID', 1);
        }

		$dom->documentElement->appendChild($node);
		$node->appendChild($cdata);

		$dom->save(storage_path('app/' . Gallery::_GALLERIES_FILE_PATH));
	}
	/**
	 * Modifies a gallery's name in a XML file
	 *
	 * Modifies a gallery's name in the provided gallery XML file
	 *
	 * @param 	int		$gallery_id		The gallery's ID to be modified
	 * @param 	int		$gallery_name	The gallery's new name
	 * @return 	void
	 **/
	public function modifyGallery($gallery_id, $gallery_name)
	{
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->load(storage_path('app/' . Gallery::_GALLERIES_FILE_PATH));
		$xpath = new \DOMXpath($dom);
		$targets = $xpath->query('/galleries/name[@galleryID="' . $gallery_id . '"]');
		if ($targets && $targets->length > 0) {
			$target = $targets->item(0);
			$target->setAttribute('galleryID', $gallery_id);
			$cdata = $dom->createCDATASection($gallery_name);
			$target->replaceChild($cdata, $target->firstChild);
		}
		$dom->save(storage_path('app/' . Gallery::_GALLERIES_FILE_PATH));
	}
	/**
	 * Removes a gallery and its images (if any) in a XML file
	 *
	 * Removes a gallery and its images (if any) in the provided gallery and image XML file
	 *
	 * @param 	int		$gallery_id		The gallery's ID to be removed
	 * @return 	void
	 **/
	public function removeGallery($gallery_id)
	{
		parent::__construct([$gallery_id]);
		$gal = $this->images_array;

		if ($gal) {
			foreach ($gal as $image) {
				if (!empty($image['fileName'])) {
                    $this->removeImage($image['fileName']);
                }
			}
		}

		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->load(storage_path('app/' . Gallery::_GALLERIES_FILE_PATH));
		$images = $dom->documentElement;
		$img = $images->getElementsByTagName('name');

		foreach($img as $image)	{
			if ($image->hasAttribute('galleryID') == $gallery_id) {
				if ($image->getAttribute('galleryID') == $gallery_id) {
                    $images->removeChild($image);
                }
			}
		}
		$dom->save(storage_path('app/' . Gallery::_GALLERIES_FILE_PATH));
	}
}
