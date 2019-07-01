<?php

namespace App\Libraries;

use Tcja\DOMDXMLParser\DOMDXMLParser;

/*
 *
 * CheckDefaultFiles class.
 *
 * Author : Trim Camaj
 *
 * Description : Class used to add default files and folders in case they don't exist for the site to function properly
 *
 */
class CheckDefaultFiles
{
	/**
	 *
	 *  @var string $XML_PAGE_FOLDER_PATH Default pages folder location
	 * */
	const XML_USER_FILE_PATH = 'private/users/users.xml';
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
	const XML_DATA_FILE_PATH = 'private/data/';
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
	 * Checks for the site's default files existence, if they're not they will be created accordingly
	 *
	 * @return	void
	 **/
	public static function checkDefaultFiles()
	{
        /* checks for galleries's symlink  */
        if (!\File::exists(public_path('storage'))) {
            \File::link(storage_path('app/public'), public_path('storage'));
        }
		/* checks pages default files existence */
		$page = new Page('DO', 'CHECK_FILES');
		if (!$page->fetchPagesDefault('home') || !$page->fetchPagesDefault('gallery') || !$page->fetchPagesDefault('contact')) {
			$dom = new \DOMDocument('1.0', 'UTF-8');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = false;
			if (!\Storage::exists(Page::XML_PAGE_DEFAULT_FOLDER_PATH . $page->fetchPagesDefault('home') . '.xml')) {
				$file_name = 'home__home';
				\Storage::put(Page::XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml', '<?xml version="1.0" encoding="UTF-8"?><data><page public="1" menuOrder="1" menuName="Home"><![CDATA[]]></page></data>');
				$dom->load(storage_path('app/' . Page::XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml'));
				$dom->save(storage_path('app/' . Page::XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml'));
			}
			if (!\Storage::exists(Page::XML_PAGE_DEFAULT_FOLDER_PATH . $page->fetchPagesDefault('gallery') . '.xml')) {
				$file_name = 'gallery__gallery';
				\Storage::put(Page::XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml', '<?xml version="1.0" encoding="UTF-8"?><data><page public="1" menuOrder="2" menuName="Gallery"><![CDATA[]]></page></data>');
				$dom->load(storage_path('app/' . Page::XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml'));
				$dom->save(storage_path('app/' . Page::XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml'));
			}
			if (!\Storage::exists(Page::XML_PAGE_DEFAULT_FOLDER_PATH . $page->fetchPagesDefault('contact') . '.xml')) {
				$file_name = 'contact__contact';
				\Storage::put(Page::XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml', '<?xml version="1.0" encoding="UTF-8"?><data><page public="1" menuOrder="3" menuName="Contact"><![CDATA[]]></page></data>');
				$dom->load(storage_path('app/' . Page::XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml'));
				$dom->save(storage_path('app/' . Page::XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml'));
			}
		}
		/* checks user file existence */
		if (!\Storage::exists(self::XML_USER_FILE_PATH)) {
			$dom = new \DOMDocument('1.0', 'UTF-8');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = false;
			if (!\Storage::exists(self::XML_USER_FILE_PATH)) {
				\Storage::put(self::XML_USER_FILE_PATH, '<?xml version="1.0" encoding="UTF-8"?><users><user type="admin" name="admin" email="admin@admin.com" password="' . bcrypt('admin123') . '"><![CDATA[Compte administrateur suprême]]></user></users>');
				$dom->load(storage_path('app/' . self::XML_USER_FILE_PATH));
				$dom->save(storage_path('app/' . self::XML_USER_FILE_PATH));
			}
		} else {
			$user = new User;
			if (strlen($user->getAdminPassword()) !== 60) {
                $xml = new DOMDXMLParser(storage_path('app/' . self::XML_USER_FILE_PATH));

                $xml->pickNode('name', 'admin')->changeData('password', bcrypt($xml->pickNode('name', 'admin')->getAttr('password')));
			}
		}
		/* checks gallery's default files existence */
		if (!\Storage::exists(self::GALLERIES_FILE_PATH) || !\Storage::exists(self::IMAGES_FILE_PATH)) {
			$dom = new \DOMDocument('1.0', 'UTF-8');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = false;
			if (!\Storage::exists(self::GALLERIES_FILE_PATH)) {
				\Storage::put(self::GALLERIES_FILE_PATH, '<?xml version="1.0" encoding="UTF-8"?><galleries></galleries>');
				$dom->load(storage_path('app/' . self::GALLERIES_FILE_PATH));
				$dom->save(storage_path('app/' . self::GALLERIES_FILE_PATH));
			}
			if (!\Storage::exists(self::IMAGES_FILE_PATH)) {
				\Storage::put(self::IMAGES_FILE_PATH, '<?xml version="1.0" encoding="UTF-8"?><images></images>');
				$dom->load(storage_path('app/' . self::IMAGES_FILE_PATH));
				$dom->save(storage_path('app/' . self::IMAGES_FILE_PATH));
			}
		}
	}
}
