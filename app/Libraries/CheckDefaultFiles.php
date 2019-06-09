<?php

namespace App\Libraries;

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
	 *  @var string $_XML_PAGE_FOLDER_PATH Default pages folder location
	 * */
	const _XML_USER_FILE_PATH = 'private/users/users.xml';
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
			$dom->preserveWhiteSpace = FALSE;
			$dom->formatOutput = TRUE;
			if (!\Storage::exists(Page::_XML_PAGE_DEFAULT_FOLDER_PATH . $page->fetchPagesDefault('home') . '.xml')) {
				$file_name = 'accueil__home';
				\Storage::put(Page::_XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml', '<?xml version="1.0" encoding="UTF-8"?><page public="1" menuOrder="1" menuName="Accueil"><![CDATA[<p><b><i><span style="font-family: Helvetica; font-size: 36px;">HOME PAGE</span><span style="font-size: 36px;">﻿ !!</span></i></b></p>]]></page>');
				$dom->load(storage_path('app/' . Page::_XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml'));
				$dom->save(storage_path('app/' . Page::_XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml'));
			}
			if (!\Storage::exists(Page::_XML_PAGE_DEFAULT_FOLDER_PATH . $page->fetchPagesDefault('gallery') . '.xml')) {
				$file_name = 'galerie__gallery';
				\Storage::put(Page::_XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml', '<?xml version="1.0" encoding="UTF-8"?><page public="1" menuOrder="2" menuName="Galerie"><![CDATA[<p>Voici notre galerie !</p>]]></page>');
				$dom->load(storage_path('app/' . Page::_XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml'));
				$dom->save(storage_path('app/' . Page::_XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml'));
			}
			if (!\Storage::exists(Page::_XML_PAGE_DEFAULT_FOLDER_PATH . $page->fetchPagesDefault('contact') . '.xml')) {
				$file_name = 'contact__contact';
				\Storage::put(Page::_XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml', '<?xml version="1.0" encoding="UTF-8"?><page public="1" menuOrder="3" menuName="Contact"><![CDATA[Contactez-nous pour en savoir plus...]]></page>');
				$dom->load(storage_path('app/' . Page::_XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml'));
				$dom->save(storage_path('app/' . Page::_XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml'));
			}
		}
		/* checks user file existence */
		if (!\Storage::exists(self::_XML_USER_FILE_PATH)) {
			$dom = new \DOMDocument('1.0', 'UTF-8');
			$dom->preserveWhiteSpace = FALSE;
			$dom->formatOutput = TRUE;
			if (!\Storage::exists(self::_XML_USER_FILE_PATH)) {
				\Storage::put(self::_XML_USER_FILE_PATH, '<?xml version="1.0" encoding="UTF-8"?><users><user type="admin" name="admin" email="admin@admin.com" password="' . bcrypt('admin123') . '"><![CDATA[Compte administrateur suprême]]></user></users>');
				$dom->load(storage_path('app/' . self::_XML_USER_FILE_PATH));
				$dom->save(storage_path('app/' . self::_XML_USER_FILE_PATH));
			}
		} else {
			$user = new User;
			if (strlen($user->getAdminPassword()) !== 60) {
				$dom = new \DOMDocument('1.0', 'UTF-8');
				$dom->preserveWhiteSpace = false;
				$dom->formatOutput = true;
				$dom->load(storage_path('app/' . self::_XML_USER_FILE_PATH));
				$xpath = new \DOMXpath($dom);
				$targets = $xpath->query('/users/user[@name="admin"]');
				if ($targets && $targets->length > 0) {
					$target = $targets->item(0);
					$target->setAttribute('password', bcrypt($target->getAttribute('password')));
				}
				$dom->save(storage_path('app/' . self::_XML_USER_FILE_PATH));
			}
		}
		/* checks gallery's default files existence */
		if (!\Storage::exists(self::_GALLERIES_FILE_PATH) || !\Storage::exists(self::_IMAGES_FILE_PATH)) {
			$dom = new \DOMDocument('1.0', 'UTF-8');
			$dom->preserveWhiteSpace = FALSE;
			$dom->formatOutput = TRUE;
			if (!\Storage::exists(self::_GALLERIES_FILE_PATH)) {
				\Storage::put(self::_GALLERIES_FILE_PATH, '<?xml version="1.0" encoding="UTF-8"?><galleries></galleries>');
				$dom->load(storage_path('app/' . self::_GALLERIES_FILE_PATH));
				$dom->save(storage_path('app/' . self::_GALLERIES_FILE_PATH));
			}
			if (!\Storage::exists(self::_IMAGES_FILE_PATH)) {
				\Storage::put(self::_IMAGES_FILE_PATH, '<?xml version="1.0" encoding="UTF-8"?><images></images>');
				$dom->load(storage_path('app/' . self::_IMAGES_FILE_PATH));
				$dom->save(storage_path('app/' . self::_IMAGES_FILE_PATH));
			}
		}
	}
}
