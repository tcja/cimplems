<?php

namespace App\Libraries;

use Tcja\DOMDXMLParser;

/*
 *
 * CheckDefaultFiles class.
 *
 * Author: tcja
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
				\Storage::put(Page::XML_PAGE_DEFAULT_FOLDER_PATH . $file_name . '.xml', '<?xml version="1.0" encoding="UTF-8"?><data><page public="1" menuOrder="1" menuName="Home"><![CDATA[<p style="text-align: center; "><span style="font-size: 24px; font-family: &quot;Arial Black&quot;;">Welcome to cimplems</span></p><p style="text-align: left;"><span style="font-family: Arial;">﻿This is the home page of your website, feel free to change anything as you please, enjoy!</span></p><p style="text-align: left;">Default login&nbsp;<span style="font-weight: bolder;">e-mail is : admin@admin-cimplems.com</span>&nbsp;and&nbsp;<span style="font-weight: bolder;">password is : admin</span>, please change them upon your first login for obvious security reasons and also because this e-mail will be used to send you the informations when someone fills the contact form to get in touch with you.</p><p style="">Please do not forget to change the following options from&nbsp;<b><i>APP_ENV=local</i></b> to&nbsp;<i><b>APP_ENV=<font color="#0000ff">production</font></b></i><font color="#0000ff"> </font>and&nbsp;<b><i>APP_DEBUG=true</i> </b>to&nbsp;<b><i>APP_DEBUG=<font color="#0000ff">false</font></i></b><font color="#0000ff"> </font>in the <b>".env"</b> file whenever you are ready to rollout your website.<br><br></p><p style="text-align: left;"><span style="font-family: Arial; font-size: 18px;"><b>What is cimplems?</b></span></p><p style="text-align: left;">Cimplems stands for a contraction of simple and <a href="https://en.wikipedia.org/wiki/Content_management_system" target="_blank">CMS</a> (Content Management System) as for "Simple CMS" because that is what cimplems is trying to achieve : making a CMS simple to install (XML <a href="https://en.wikipedia.org/wiki/Flat-file_database" target="_blank">flat file database</a>) and use (<a href="https://en.wikipedia.org/wiki/WYSIWYG" target="_blank">WYSIWYG</a>&nbsp;administration), with cimplems, everything you do is on the same page, no admin panel with thousands of options, just simple buttons on modals that do the essential customisation of your website.<br></p><p><span style="font-family: Arial; font-size: 18px;"><span style="font-weight: bolder;">How does it work?</span></span></p><p style="text-align: left;">Hover through each buttons to see what they do and play around to familiarize yourself with all the functions available, currently, you can create standard pages, manage multiple galleries and interact with your visitors through the contact page, all the pages can be turned ON or OFF at will, please note that if you put the home page offline your website will switch to "under construction" mode meaning that the visitors will see a "under construction" page preventing them from navigating your website.<br></p><p><span style="font-family: Arial; font-size: 18px;"><span style="font-weight: bolder;">What\'s the tech stack under the hood?</span></span></p><p style="text-align: left;">This CMS is built with <a href="https://laravel.com/" target="_blank">Laravel 8</a> for the back-end and <a href="https://jquery.com/" target="_blank">jQuery</a>&nbsp;as well as <a href="https://getbootstrap.com/" target="_blank">bootstrap</a> for the front-end, it is reactive by default but can not be if you want, all CMS options can be customised within the CMS\'s config file (path: "<b><font color="#ff0000" style="--darkreader-inline-color:#ff1a1a;" data-darkreader-inline-color="">root_website_dir/</font>config/site.php"</b>).<br></p><p style="text-align: left;"><span style="font-family: Arial; font-size: 18px; font-weight: 700;">How about languages?</span><br></p><p style="text-align: left;">Localization is currently available with french and english languages, the latter being the default one (configurable from config file), if you wish to make your own you can do so by creating a new file within "<span style="font-size: 1rem;"><font color="#ff0000" data-darkreader-inline-color="" style="--darkreader-inline-color:#ff1a1a;"><b>root_website_dir/</b></font></span><span style="font-size: 1rem;"><b>resources/lang/es/site.php</b>" directory (this would be an exemple for a spanish locale), if you wish to share it with the project, feel free to make a pull request.</span></p><p style="text-align: left;"><span style="font-family: Arial; font-size: 18px; font-weight: 700;">And themes?</span></p><p style="text-align: left;">3 themes (all using bootstrap) are available but of course you can make your own (dig down the theme path and see how they work before making your own =&gt; "<span style="font-weight: bold; color: rgb(255, 0, 0); font-size: 1rem; --darkreader-inline-color:#ff1a1a;" data-darkreader-inline-color="">root_website_dir/</span><span style="font-size: 1rem;"><b>resources/views/themes/name-of-the-theme/"&nbsp;</b>and "</span><span style="color: rgb(255, 0, 0); font-weight: 700; font-size: 1rem; --darkreader-inline-color:#ff1a1a;" data-darkreader-inline-color="">root_website_dir/</span><b style="font-size: 1rem;">public/themes/</b><span style="font-weight: bolder;">name-of-the-theme/"</span>)<span style="font-size: 1rem;">, the default theme is <i>"bootstrap"</i>, the other two themes are <i>"default"</i> which is a very simple theme and <i>"blog"</i> which is a theme that catters more to blogging style, you can see how those themes look by changing the name of the current used theme from the config file&nbsp;</span><span style="font-size: 1rem;">(path:</span><span style="font-size: 1rem;">&nbsp;"</span><span style="font-size: 1rem; font-weight: bolder;"><font color="#ff0000" data-darkreader-inline-color="" style="--darkreader-inline-color:#ff1a1a;">root_website_dir/</font>config/site.php"</span><span style="font-size: 1rem;">)</span><span style="font-size: 1rem;">.</span></p><p><span style="font-family: Arial; font-size: 18px; font-weight: 700;">Where are the files stored?</span></p><p>Cimplems works with XML files, the 3 default pages files are stored into : "<span style="color: rgb(255, 0, 0); font-size: 1rem; --darkreader-inline-color:#ff1a1a;" data-darkreader-inline-color=""><b>root_website_dir/</b></span><span style="font-size: 1rem;"><b>storage/app/database/pages/default/</b>" whereas the custom pages remain in : "</span><span style="font-size: 1rem; color: rgb(255, 0, 0); --darkreader-inline-color:#ff1a1a;" data-darkreader-inline-color=""><b>root_website_dir/</b></span><span style="font-size: 1rem;"><b>storage/app/database/pages/custom/</b>".<br>The user admin file is located at : "</span><span style="color: rgb(255, 0, 0); font-size: 1rem; --darkreader-inline-color:#ff1a1a;" data-darkreader-inline-color=""><b>root_website_dir/</b></span><span style="font-size: 1rem;"><b>storage/app/database/users/users.xml</b>", this file serves for the authentification as an administrator.<br>The files related to galleries are in this folder : "</span><span style="color: rgb(255, 0, 0); font-size: 1rem; --darkreader-inline-color:#ff1a1a;" data-darkreader-inline-color=""><b>root_website_dir/</b></span><span style="font-size: 1rem;"><b>storage/app/database/galleries/</b>", the images are uploaded to : "</span><b><span style="color: rgb(255, 0, 0); font-size: 1rem; --darkreader-inline-color:#ff1a1a;" data-darkreader-inline-color="">root_website_dir/</span><span style="font-size: 1rem;">storage/app/public/images_gallery/</span></b><span style="font-size: 1rem;">".</span></p><p><span style="font-size: 1rem;"><br></span></p>]]></page></data>');
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
				\Storage::put(self::XML_USER_FILE_PATH, '<?xml version="1.0" encoding="UTF-8"?><users><user type="admin" name="admin" email="admin@admin-cimplems.com" password="' . bcrypt('admin') . '"><![CDATA[Supreme admin account]]></user></users>');
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
