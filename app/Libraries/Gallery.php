<?php

namespace App\Libraries;

use Tcja\DOMDXMLParser\DOMDXMLParser;

/*
 *
 * Gallery class.
 *
 * Author : Trim Camaj
 *
 * Description : This is the main class, it serves all functions to display the gallery data according to the needed infos
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
	 * @var string IMAGES_FILE_NAME XML file name for images
	 * */
	const IMAGES_FILE_NAME = 'images.xml';
	/**
	 *
	 * @var string GALLERIES_FILE_NAME XML file name for galleries
	 * */
	const GALLERIES_FILE_NAME = 'galleries.xml';
	/**
	 *
	 *  @var string $XML_DATA_FILE_PATH XML file for the XML data files path
	 * */
	const XML_DATA_FILE_PATH = 'database/galleries/';
	/**
	 *
	 * @var string IMAGES_FILE_PATH XML file for images
	 * */
	const IMAGES_FILE_PATH = self::XML_DATA_FILE_PATH . self::IMAGES_FILE_NAME;
	/**
	 *
	 * @var string GALLERIES_FILE_PATH XML file for galleries
	 *  */
	const GALLERIES_FILE_PATH = self::XML_DATA_FILE_PATH . self::GALLERIES_FILE_NAME;

	/**
	 * Constructor used to retrieve data from any gallery, it also checks whether the XML files exist or not and if not creates them accordingly
	 *
	 * Retrieves data from a specific gallery by its ID or from all galleries by default and stores them in an array
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
	 * @param 	string		$image_name		The name of the image to get the data from
	 * @return	mixed						Returns an array with the data found (stores the following data : the name of the image, its gallery and its title if any), returns false if no data found
	 **/
	public function getImageInfos($image_name)
	{
        $xml = new DOMDXMLParser(storage_path('app/' . self::IMAGES_FILE_PATH));

        if ($xml->checkNode('fileName', $image_name)) {
            return [
                'name' => $xml->pickNode('fileName', $image_name)->getAttr('fileName'),
                'gallery' => $xml->pickNode('fileName', $image_name)->getAttr('galleryID'),
                'title' => $xml->pickNode('fileName', $image_name)->getValue()
            ];
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
        $xml = new DOMDXMLParser(storage_path('app/' . self::IMAGES_FILE_PATH));

        return $xml->getTotalItems();
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
        $xml = new DOMDXMLParser(storage_path('app/' . self::GALLERIES_FILE_PATH));

        return $xml->pickNode('gallery')->getHighestValue('galleryID');
	}
	/**
	 * Gets image data according from its timestamp
	 *
	 * Gets image informations according to the timestamp provided and returns it in form of an array
	 *
	 * @param 	string		$timestamp		The timestamp of the image to get the data from
	 * @return	mixed						Returns an array with the following data : the timesamp, the image name and its title (if any) or returns false if no data found
	 **/
	public function getImageFromTimestamp($timestamp)
	{
        $xml = new DOMDXMLParser(storage_path('app/' . self::IMAGES_FILE_PATH));

        if ($xml->checkNode('timestamp', $timestamp)) {
            return [
                'timestamp' => $xml->pickNode('timestamp', $timestamp)->getAttr('timestamp'),
                'fileName' => $xml->pickNode('timestamp', $timestamp)->getAttr('fileName'),
                'title' => $xml->pickNode('timestamp', $timestamp)->getValue()
            ];
        } else {
            return false;
        }
	}
	/**
	 * Fetches the galleries in an array
	 *
	 * Fetches all the galleries and stores them in an array
	 *
	 * @return	array	Returns an array with the following data : the gallery's ID and the gallery's name
	 **/
	protected function fetchGalleries()
	{
        $xml = new DOMDXMLParser(storage_path('app/' . self::GALLERIES_FILE_PATH));
        $id = $xml->pickNode('gallery')->fetchData('galleryID')->toArray();
        $values = $xml->pickNode('gallery')->fetchData('nodeValue')->toArray();

        return ($id) ? array_combine($id, $values) : [];
	}
	/**
	 * Fetches the images in an array
	 *
	 * Fetches all the images no matter the galley or fetches them from a specific gallery and stores them in an array
	 *
	 * @param	bool		$JSON		If set to true it will return the fetched data in a JSON encoded array, defaults to false will return a plain array
	 * @return	mixed					Returns false if there is no images in the image XML file or returns an array of the images with the following data (the gallery ID and name, the timestamp,
     *                                  the image name, its gallery id and the title if any)
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
	 * @param 	int		$galleryID		The gallery ID to get the data from, defaults to the ID 1
	 * @return	array						Returns an array with the following data : the image name
	 **/
	private function fetchImagesNamesFromGallery($galleryID = 1)
	{
        $xml = new DOMDXMLParser(storage_path('app/' . self::IMAGES_FILE_PATH));
        if ($xml->checkNode('galleryID', $galleryID)) {
            return $xml->pickNode('galleryID', $galleryID)->fetchData('fileName')->toArray();
        }

        return [];
	}
	/**
	 * Fetches the images titles from a specific gallery
	 *
	 * Fetches the images titles from a specific gallery and stores them in an array
	 *
	 * @param 	int		$galleryID		The gallery ID to get the data from, defaults to the ID 1
	 * @return	array						Returns an array with the following data : the image title
	 **/
	private function fetchTitlesFromGallery($galleryID = 1)
	{
        $xml = new DOMDXMLParser(storage_path('app/' . self::IMAGES_FILE_PATH));
        if ($xml->checkNode('galleryID', $galleryID)) {
            return $xml->pickNode('galleryID', $galleryID)->fetchData('nodeValue')->toArray();
        }

        return [];
	}
	/**
	 * Fetches the images timestamps from a specific gallery
	 *
	 * Fetches the images timestamps from a specific gallery and stores them in an array
	 *
	 * @param 	int		$galleryID		    The gallery ID to get the data from, defaults to the ID 1
	 * @return	array						Returns an array (sorted by timestamp) with the following data : the image timestamp
	 **/
	private function fetchTimestampsFromGallery($galleryID = 1)
	{
        $xml = new DOMDXMLParser(storage_path('app/' . self::IMAGES_FILE_PATH));
        if ($xml->checkNode('galleryID', $galleryID)) {
            return $xml->pickNode('galleryID', $galleryID)->fetchData('timestamp')->sortBy('timestamp')->toArray();
        }

        return [];
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
