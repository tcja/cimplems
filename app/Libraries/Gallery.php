<?php

namespace App\Libraries;
/*
 *
 * Gallery class.
 *
 * Author : Trim Camaj
 *
 * Description : This is the main class, it serves all functions to display the gallery datas according to the needed infos
 *
 */
class Gallery
{
	/**
	 *
	 *
	 * @var int $gallery The gallery ID
	 * */
	protected $gallery;
	/**
	 *
	 *
	 * @var array $images_array Array of fetched images
	 * */
	protected $images_array = [];
	/**
	 *
	 *
	 * @var array $galleries_array Array of fetched galleries
	 * */
	protected $galleries_array = [];

	/**
	 *
	 *
	 * @var string _IMAGES_FILE_NAME XML file name for images
	 * */
	const _IMAGES_FILE_NAME = 'images.xml';
	/**
	 *
	 * @var string _GALLERIES_FILE_NAME XML file name for galleries
	 * */
	const _GALLERIES_FILE_NAME = 'galleries.xml';
	/**
	 *
	 *  @var string $_XML_DATA_FILE_PATH XML file for the XML datas files path
	 * */
	const _XML_DATA_FILE_PATH = 'private/datas/';
	/**
	 *
	 * @var string _IMAGES_FILE_PATH XML file for images
	 * */
	const _IMAGES_FILE_PATH = self::_XML_DATA_FILE_PATH . self::_IMAGES_FILE_NAME;
	/**
	 *
	 * @var string _GALLERIES_FILE_PATH XML file for galleries
	 *  */
	const _GALLERIES_FILE_PATH = self::_XML_DATA_FILE_PATH . self::_GALLERIES_FILE_NAME;

	/**
	 * Constructor used to retrieve datas from any gallery, it also checks whether the XML files exist or not and if not creates them accordingly
	 *
	 * Retrieves datas from a specific gallery by its ID or from all galleries by default and stores them in an array
	 *
	 * @param 	int		$gallery		Gallery's ID to be shown, if not set, defaults to false will show all galleries
	 * @return	void
	 **/
 	public function __construct($gallery = false)
	{
		$this->galleries_array = $this->fetchGalleries();
		if ($gallery) {
			if (array_key_exists($gallery[0], $this->galleries_array)) {
                $this->gallery = $gallery[0];
            } else {
                $this->gallery = 1;
            }
		}
		$this->images_array = $this->fetchImages();
	}
	/**
	 * Gets image infos from a XML file
	 *
	 * Gets image informations provided from an image XML file and stores them in an array
	 *
	 * @param 	string		$image_name		The name of the image to get the datas from
	 * @return	mixed						Returns an array with the datas found (stores the following datas : the name of the image, its gallery and its title if any), returns false if no data found
	 **/
	public function getImageInfos($image_name)
	{
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->load(storage_path('app/' . self::_IMAGES_FILE_PATH));

		$xpath = new \DOMXpath($dom);
		$targets = $xpath->query('/images/title[@fileName="' . $image_name . '"]');
		if ($targets && $targets->length > 0) {
			$target = $targets->item(0);
			return array('name' => $target->getAttribute('fileName'), 'gallery' => $target->getAttribute('gallery'), 'title' => $target->nodeValue);
		} else {
            return false;
        }
	}
	/**
	 * Gets the total number of the images from XML file
	 *
	 * Gets the total number of the images from XML file no matter the gallery
	 *
	 * @return	int		Returns the number of images found
	 **/
	public function getTotalImages()
	{
		$doc = new \DOMDocument('1.0', 'UTF-8');
		$doc->load(storage_path('app/' . self::_IMAGES_FILE_PATH));
		$datas = $doc->getElementsByTagName('title');

		return $datas->length;
	}
	/**
	 * Gets highest gallery id
	 *
	 * Gets highest gallery id and stores it in a string
	 *
	 * @return	mixed	Returns a string with the highest gallery id, if no gallery returns 0
	 **/
	public function getMaxGalleryId()
	{
		$doc = new \DOMDocument('1.0', 'UTF-8');
		$doc->load(storage_path('app/' . self::_GALLERIES_FILE_PATH));
		$datas = $doc->getElementsByTagName('name');
		if ($datas->length == 0) {
            return 0;
        }
		$array = [];
		foreach ($datas as $data) {
            array_push($array, $data->getAttribute('galleryID'));
        }

		return max($array);
	}
	/**
	 * Gets image data according from its timestamp
	 *
	 * Gets image informations according to the timestamp provided and returns it in form of an array
	 *
	 * @param 	string		$timestamp		The timestamp of the image to get the data from
	 * @return	mixed						Returns an array with the following datas : the timesamp, the image name and its title (if any) or returns false if no data found
	 **/
	public function getImageFromTimestamp($timestamp)
	{
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = FALSE;
		$dom->formatOutput = TRUE;
		$dom->load(storage_path('app/' . self::_IMAGES_FILE_PATH));

		$xpath = new \DOMXpath($dom);
		$targets = $xpath->query('/images/title[@timestamp="' . $timestamp . '"]');
		if ($targets && $targets->length > 0) {
			$target = $targets->item(0);
			return array('timestamp' => $target->getAttribute('timestamp'), 'fileName' => $target->getAttribute('fileName'), 'title' => $target->nodeValue);
		} else {
            return false;
        }
	}
	/**
	 * Fetches the galleries in an array
	 *
	 * Fetches all the galleries no matter the gallery and stores them in an array
	 *
	 * @return	array	Returns an array with the following datas : the gallery's ID and the gallery's name
	 **/
	protected function fetchGalleries()
	{
		$doc = new \DOMDocument('1.0', 'UTF-8');
		$doc->load(storage_path('app/' . self::_GALLERIES_FILE_PATH));
		$datas = $doc->getElementsByTagName('name');
		$array = [];
		foreach ($datas as $data) {
            $array[$data->getAttribute('galleryID')] = $data->nodeValue;
        }

		return $array;
	}
	/**
	 * Fetches the images in an array
	 *
	 * Fetches all the images no matter the galley or fetches them from a specific gallery and stores them in an array
	 *
	 * @param	bool		$JSON		If set to true it will return the fetched datas in a JSON encoded array, defaults to false will return a plain array
	 * @return	mixed					Returns false if there is no images in the image XML file or returns an array of the images with the following datas (the gallery ID and name, the timestamp, the image name, its gallery id and the title if any)
	 **/
	protected function fetchImages($JSON = false)
	{
		$galleryNames = $this->galleries_array;
		$array = [];

		$totalimages = $this->getTotalImages();
		if (!$totalimages) {
            return false;
        }
		elseif ($this->gallery)	{
			$array_sub = array('galleryID' => (int) $this->gallery, 'gallery' => $galleryNames[$this->gallery]);
			$array[$this->gallery]['galleryInfos'] = $array_sub;
			$timestamps = $this->fetchTimestampsFromGallery($this->gallery);
			foreach ($timestamps as $ktimestamp => $timestamp) {
				$imageInfos = $this->getImageFromTimestamp($timestamp);
				$array[$this->gallery][$ktimestamp] = $imageInfos;
			}
		} else {
			foreach ($galleryNames as $kgallery => $galleryName) {
				$array_sub = array('galleryID' => (int) $kgallery, 'gallery' => $galleryNames[$kgallery]);
				$array[$kgallery]['galleryInfos'] = $array_sub;
				$timestamps = $this->fetchTimestampsFromGallery($kgallery);
				foreach ($timestamps as $ktimestamp => $timestamp) {
					$imageInfos = $this->getImageFromTimestamp($timestamp);
					$array[$kgallery][$ktimestamp] = $imageInfos;
				}
			}
		}
		if ($this->gallery != false) {
			if ($JSON) {
                return json_encode($array[$this->gallery]);
            } else {
                return $array[$this->gallery];
            }
		} else {
			if (!empty($array)) {
				if ($JSON) {
                    return json_encode($array);
                } else {
                    return $array;
                }
			} else {
                return false;
            }
		}
	}
	/**
	 * Fetches the images names from a specific gallery
	 *
	 * Fetches the images names from a specific gallery and stores them in an array
	 *
	 * @param 	int		$galleryID		The gallery ID to get the datas from, defaults to the ID 1
	 * @return	array						Returns an array with the following datas : the image name
	 **/
	private function fetchImagesNamesFromGallery($galleryID = 1)
	{
		$doc = new \DOMDocument('1.0', 'UTF-8');
		$doc->load(storage_path('app/' . self::_IMAGES_FILE_PATH));
		$datas = $doc->getElementsByTagName('title');
		$i = 0;
		$array = [];
		foreach ($datas as $data) {
			if ($data->getAttribute('gallery') == $galleryID) {
                $array[$i] = $data->getAttribute('fileName');
            }
			$i++;
		}
		return $array;
	}
	/**
	 * Fetches the images titles from a specific gallery
	 *
	 * Fetches the images titles from a specific gallery and stores them in an array
	 *
	 * @param 	int		$galleryID		The gallery ID to get the datas from, defaults to the ID 1
	 * @return	array						Returns an array with the following datas : the image title
	 **/
	private function fetchTitlesFromGallery($galleryID = 1)
	{
		$doc = new \DOMDocument('1.0', 'UTF-8');
		$doc->load(storage_path('app/' . self::_IMAGES_FILE_PATH));
		$datas = $doc->getElementsByTagName('title');
		$i = 0;
		$array = [];
		foreach ($datas as $data) {
			if ($data->getAttribute('gallery') == $galleryID) {
                $array[$i] = $data->nodeValue;
            }
			$i++;
		}
		return $array;
	}
	/**
	 * Fetches the images timestamps from a specific gallery
	 *
	 * Fetches the images timestamps from a specific gallery and stores them in an array
	 *
	 * @param 	int		$galleryID		The gallery ID to get the datas from, defaults to the ID 1
	 * @return	array						Returns an array (sorted by timestamp) with the following datas : the image timestamp
	 **/
	private function fetchTimestampsFromGallery($galleryID = 1)
	{
		$doc = new \DOMDocument('1.0', 'UTF-8');
		$doc->load(storage_path('app/' . self::_IMAGES_FILE_PATH));
		$datas = $doc->getElementsByTagName('title');
		$i = 0;
		$array = [];
		foreach ($datas as $data) {
			if ($data->getAttribute('gallery') == $galleryID) {
                $array[$i] = $data->getAttribute('timestamp');
            }
			$i++;
		}
		sort($array);
		return $array;
	}
	/**
	 *
	 * @return 	string
	 **/
	public function getGalleriesArray()
	{
		return $this->galleries_array;
	}
	/**
	 *
	 * @return 	string
	 **/
	public function getImagesArray()
	{
		return $this->images_array;
	}
}
