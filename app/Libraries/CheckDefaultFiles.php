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
	const XML_USER_FILE_PATH = 'database/users/users.xml';
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
				\Storage::put(Page::XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml', '<?xml version="1.0" encoding="UTF-8"?><data><page public="1" menuOrder="1" menuName="Home"><![CDATA[<p>                                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Cumque a nisi ab delectus amet enim in! Tenetur, sint illo aspernatur iusto harum, magni quis sunt asperiores ipsa ad quo alias!
                Autem ullam voluptatibus, accusamus error vero perspiciatis ut adipisci quidem perferendis tempora. Ratione omnis eligendi velit rerum autem fugit debitis molestiae. Deleniti totam consequuntur voluptate officiis laborum ea similique exercitationem?
                Doloremque, modi! Dolorum earum qui reiciendis quasi inventore unde libero ducimus ipsa? </p><p>Quis corrupti exercitationem dignissimos deleniti, dolor impedit enim quaerat illo perspiciatis error autem molestiae soluta culpa nostrum qui?
                At placeat nesciunt ipsam, nam earum unde exercitationem sit magni consectetur est dolorum sequi delectus quia atque accusantium quae quos asperiores debitis quidem libero excepturi illum distinctio! Incidunt, eos voluptatibus.
                Nisi quisquam quidem ab culpa cumque laboriosam at hic tenetur totam, pariatur amet excepturi voluptatum repellat sit dignissimos minima placeat voluptates expedita vero ducimus fuga itaque. </p><p>Lure nihil explicabo totam?
                Pariatur accusamus asperiores officia sint voluptate nesciunt nam impedit dolore nulla cupiditate, numquam perspiciatis rem praesentium quam tempore in optio. Cum, ex quidem! Pariatur soluta libero asperiores obcaecati maiores voluptatum.
                Eius facere velit nostrum accusantium debitis iusto quae, hic tenetur. Voluptatum beatae nihil minima ducimus sunt debitis adipisci quos vel. Magni tenetur impedit itaque officia repellendus ad perspiciatis molestiae aliquam?
                Fugit, quisquam quaerat perspiciatis earum ratione, tempore dolor nobis quia voluptatibus, veniam totam? Temporibus dolore quod laudantium animi odit porro quidem doloribus perferendis hic? Quae accusantium blanditiis maxime voluptatem eaque?
                Blanditiis, minima deserunt est ullam adipisci velit beatae quas soluta nobis! Quidem nulla libero asperiores optio, ipsam quos quis. </p><p>Cum, nam eligendi? Asperiores officiis quae enim eius dolorem veniam cupiditate!
                Quibusdam velit expedita ad blanditiis obcaecati tempora accusantium nostrum consequuntur temporibus, harum nulla necessitatibus perspiciatis. Minus eos impedit doloremque necessitatibus vero, officia doloribus nobis aliquam alias ipsum. Minima, atque dolor?
                Nobis praesentium ducimus odio excepturi ut aliquid atque veniam sunt, consequuntur voluptates, itaque animi? Possimus ducimus voluptates doloremque, voluptatem officiis non maiores repudiandae esse aliquid enim in quisquam, suscipit rerum!
                Fuga numquam quas distinctio quibusdam, nam labore mollitia omnis rem atque odio aut ea corporis doloribus maiores optio voluptates ut, fugit earum hic repellendus sed totam animi ducimus. Repellat, et?
                Repellendus, sit. Dolore voluptas quae ipsa explicabo voluptates distinctio velit eum, consequuntur consectetur odit sunt. Asperiores deserunt aut corrupti et explicabo porro quas adipisci odit cumque ipsam, nesciunt at vitae!
                Beatae a voluptatum atque! Odio itaque deserunt commodi ea perspiciatis repellendus nam perferendis fuga ipsam voluptates explicabo quas nesciunt numquam asperiores magnam molestias id quos, illo earum odit, quam maxime.
                Quis, rem! Ipsam magnam quibusdam inventore illo in et quas ab nemo, tenetur facere eaque? Tempora aperiam odit nulla dignissimos iure, labore laborum sint asperiores voluptatem neque beatae molestias repellat.
                                            </p>]]></page></data>');
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
				\Storage::put(self::XML_USER_FILE_PATH, '<?xml version="1.0" encoding="UTF-8"?><users><user type="admin" name="admin" email="admin@admin.com" password="' . bcrypt('admin123') . '"><![CDATA[Supreme admin account]]></user></users>');
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
