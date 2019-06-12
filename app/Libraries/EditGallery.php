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
	 * Uploads a file image into the storage
	 *
	 * Uploads a file image into the storage and adds its corresponding infos (title if any set, gallery ID) into the image XML file
	 *
	 * @param	object		$request		    Illuminate\Http\Request instance
	 * @param 	string		$image_title		The image's title (optional)
	 * @param 	int		    $gallery	        The image gallery's ID
	 * @return 	mixed                           Returns an error message if the request wasn't triggered by an ajax request, returns an array of the image's infos if its upload succeeded
	 **/
	public function uploadImage($request, $image_title, $gallery)
	{
		$upload_path_big = storage_path('app/public/images_gallery/big/');
        $upload_path_min = storage_path('app/public/images_gallery/min/');
        $widen_big_width = 1280;
        $widen_min_width = 300;

        $array = [
            'gallery' => $gallery,
            'title' => empty($image_title) ? '' : $image_title
        ];

        if ($request->total_files === 0) {
            return 0;
        }

        $file = $request->file('image_path');
        for ($i = 0; $i < $request->total_files; $i++) {
            if (in_array($file[$request->image_number_ . $i]->getMimeType(), array('image/gif', 'image/png', 'image/bmp', 'image/jpeg'))) {
                if ($file[$request->image_number_ . $i]->getMimeType() == 'image/gif') {
                    $ext = '.gif';
                } elseif ($file[$request->image_number_ . $i]->getMimeType() == 'image/png') {
                    $ext = '.png';
                } else {
                    $ext = '.jpg';
                }

                $timestamp = microtime(true);
                $new_file_name = substr(sha1(md5('sàéy' . time() . rand(0, 10000) . 'YXdaewS')), 0, 16) . $ext;

                $width = getimagesize($file[$request->image_number_ . $i]->getRealPath());
                if ($width[0] <= $widen_big_width) {
                    if ($upload_done = $file[$request->image_number_ . $i]->move($upload_path_big, $new_file_name)) {
                        $this->addImage($timestamp, $new_file_name, $gallery, $image_title);
                    } else {
                        return $upload_done->getMessage();
                    }
                } else {
                    \Image::make($file[$request->image_number_ . $i]->getRealPath())->widen($widen_big_width, function ($constraint) { $constraint->upsize(); })->save($upload_path_big . $new_file_name);
                    $this->addImage($timestamp, $new_file_name, $gallery, $image_title);
                }

                \Image::make($upload_path_big . $new_file_name)->widen($widen_min_width, function ($constraint) { $constraint->upsize(); })->save($upload_path_min . $new_file_name);

                $array['name'][$i][0] = $new_file_name;
                $array['name'][$i][1] = $request->image_number_ . $i;
                $array['name'][$i][2] = strval($timestamp);
            }
        }

        if ($request->file_ajax) {
            return $array;
        } else {
            return 0;
        }
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
	 * @return 	bool                        Returns 1 if the new value was saved into the file or 0 if failed
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

		if ($dom->save(storage_path('app/' . Gallery::_IMAGES_FILE_PATH))) {
            return 1;
        } else {
            return 0;
        }
	}
	/**
	 * Modifies image infos in a XML file
	 *
	 * Modify image's informations in the provided image XML file
	 *
	 * @param 		string		$image_name			The name of the image to modify
	 * @param 		int			$image_gallery		The image's gallery to modify
	 * @param 		string		$image_title		The image's title to modify, can be empty
	 * @param 		bool		$is_same_gallery	If set to true, only the image's title will be changed otherwise with the default value set to false it will modify all the informations accordignly
	 * @return		mixed							Returns array of image's new infos if the modifications were saved in the provided XML, returns 0 if it failed in the process
	 **/
	public function modifyImage($image_name, $image_gallery, $image_title, $is_same_gallery = false)
	{
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->load(storage_path('app/' . Gallery::_IMAGES_FILE_PATH));
		$xpath = new \DOMXpath($dom);
        $targets = $xpath->query('/images/title[@fileName="' . $image_name . '"]');
        $timestamp = microtime(true);

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
		} else {
            return 0;
        }

		if ($dom->save(storage_path('app/' . Gallery::_IMAGES_FILE_PATH))) {
            return [
                'timestamp' => $timestamp,
                'name' => $image_name,
                'gallery' => $image_gallery,
                'title' => empty($image_title) ? '' : $image_title
            ];
        } else {
            return 0;
        }
	}
	/**
	 * Removes image infos in a XML file
	 *
	 * Removes image's informations in the provided image XML file
	 *
	 * @param 	string	$image_name		The name of the image to remove from the provided XML file
	 * @return 	bool                    Returns 1 if the image was correctly removed from the file and the storage or 0 if failed
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

		if ($dom->save(storage_path('app/' . Gallery::_IMAGES_FILE_PATH))) {
            \File::delete(storage_path('app/public/images_gallery/big/' . $image_name));
            \File::delete(storage_path('app/public/images_gallery/min/' . $image_name));
            return 1;
        } else {
            return 0;
        }

	}
	/**
	 * Adds a new gallery in a XML file
	 *
	 * Adds a new gallery in the provided gallery XML file
	 *
	 * @param 	string	$gallery_name	The name of the new gallery to be added
	 * @return 	mixed                   Returns the new gallery's name if the new value was saved into the file or false if failed
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

        if ($dom->save(storage_path('app/' . Gallery::_GALLERIES_FILE_PATH))) {
            return $gallery_name;
        } else {
            return 0;
        }
	}
	/**
	 * Modifies a gallery's name in a XML file
	 *
	 * Modifies a gallery's name in the provided gallery XML file
	 *
	 * @param 	int		$gallery_id		The gallery's ID to be modified
	 * @param 	int		$gallery_name	The gallery's new name
	 * @return 	bool                    Returns 1 if the new value was saved into the file or 0 if failed
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
		} else {
            return 0;
        }

        if ($dom->save(storage_path('app/' . Gallery::_GALLERIES_FILE_PATH))) {
            return 1;
        } else {
            return 0;
        }
	}
	/**
	 * Removes a gallery and its images (if any) in a XML file
	 *
	 * Removes a gallery and its images (if any) in the provided gallery and image XML file
	 *
	 * @param 	int		$gallery_id		The gallery's ID to be removed
	 * @return 	bool                    Returns 1 if the gallery was correctly removed from the file or 0 if failed
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

		if ($dom->save(storage_path('app/' . Gallery::_GALLERIES_FILE_PATH))) {
            return 1;
        } else {
            return 0;
        }
	}
}
