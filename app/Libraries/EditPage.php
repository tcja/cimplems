<?php

namespace App\Libraries;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Tcja\DOMDXMLParser\DOMDXMLParser;

/*
 *
 * Edit Page class.
 *
 * Author : Trim Camaj
 *
 * Description : Class used to manipulate pages files
 *
 */
class EditPage extends Page
{
	public function __construct()
	{
	}
	/**
	 * Modifies page's content
	 *
	 * Modifies page's content in the provided XML file
	 *
	 * @param 	string		$page			The name of the page to modify
	 * @param 	string		$content		The content of the page to modify
	 * @return 	string						Returns new modified content
	 **/
	public function editPage($page, $content)
	{
		parent::__construct('GET', 'ALL_PAGES_LIST');
		if (array_key_exists($page, $this->pages_default_list)) {
            $page_path = Page::XML_PAGE_DEFAULT_FOLDER_PATH . $this->pages_default_list[$page] . '.xml';
        } else {
            $page_path = Page::XML_PAGE_FOLDER_PATH . $page . '.xml';
        }

        $xml = new DOMDXMLParser(storage_path('app/' . $page_path));
        $xml->pickNode('page')->changeData('CDATA', $content);

		return $content;
	}
	/**
	 * Changes page's name
	 *
	 * Changes page's name in the provided XML file
	 *
	 * @param 	string		$new_page_name			The new name of the page
	 * @param 	string		$page_name_old			The old name of the page
	 * @return 	string								Returns the new page name and its slug
	 **/
	public function changePageName($new_page_name, $page_name_old)
	{
		parent::__construct('GET', 'ALL_PAGES_LIST');
		$page_name_slug = str_replace('-', '_', Str::slug($new_page_name));
		$page_name_old = strtolower(str_replace('-', '_', $page_name_old));
		if (array_key_exists($page_name_old, $this->pages_default_list)) {
			$page_path = Page::XML_PAGE_DEFAULT_FOLDER_PATH . $this->pages_default_list[$page_name_old] . '.xml';
			$page_path_new = Page::XML_PAGE_DEFAULT_FOLDER_PATH . $page_name_slug . '__' . $page_name_old . '.xml';
			$page_name_slug = $page_name_old;
		} else {
			$page_path = Page::XML_PAGE_FOLDER_PATH . $page_name_old . '.xml';
			$page_path_new = Page::XML_PAGE_FOLDER_PATH . $page_name_slug . '.xml';
		}

        $xml = new DOMDXMLParser(storage_path('app/' . $page_path));
        $xml->pickNode('page')->changeData('menuName', $new_page_name);

		if ($page_path != $page_path_new) {
            \Storage::move($page_path, $page_path_new);
        }

		return [
			'new_page_title' => $new_page_name,
			'new_page_slug' => str_replace('_', '-', $page_name_slug)
		];
	}
	/**
	 * Changes page's state
	 *
	 * Changes page's publish state, if false (default) the page will not be published, if true it will
	 *
	 * @param 	string		$page			The name of the page to change state, defaults to false if nothing set
	 * @return 	bool						Returns true for a published page or false for a private page
	 **/
	public function changePageState($page, $state = false)
	{
		parent::__construct('GET', 'ALL_PAGES_LIST');
		$page = strtolower(str_replace('-', '_', $page));
		if (array_key_exists($page, $this->pages_default_list)) {
            $page_path = Page::XML_PAGE_DEFAULT_FOLDER_PATH . $this->pages_default_list[$page] . '.xml';
        } else {
            $page_path = Page::XML_PAGE_FOLDER_PATH . $page . '.xml';
        }

        $xml = new DOMDXMLParser(storage_path('app/' . $page_path));
        $xml->pickNode('page')->changeData('public', ($state) ? 1 : 0);

		return (bool) $state;
	}
	/**
	 * Adds a new page in a XML file
	 *
	 * Adds a new page in the provided page XML file and updates the menu order list accordingly
	 *
	 * @param 	string		$page_name		The name of the page to create
	 * @param 	string		$order_menu		The number of the place to appear after in the menu
	 * @param 	string		$content		The content of the page to create, defaults to null if none set
	 * @return 	array						Returns an array with the following infos : the newly created page name, the page menu order number, its link and its content if any set
	 **/
	public function addPage($page_name, $order_menu, $content = null)
	{
		/* Updates menu order of the other pages first */
		parent::__construct($page_name, 'PAGES_LINKS_AND_MENU_ORDER');
		$menuList = $this->pages_links_titles;
		$pageMenu = array_slice($menuList, $order_menu);
		$pageMenuNames = Arr::flatten($pageMenu);
		$pageMenuFiles = array_map(function($val) {
			return str_replace('-', '_', Str::slug($val));
		}, $pageMenuNames);
		if (!empty($pageMenuFiles))	{
			foreach ($pageMenuFiles as $pageToEdit)	{
				$pageToEditCheck = Arr::where($this->pages_default_list, function ($val) use ($pageToEdit) {
					return preg_match('#^(' . $pageToEdit . ')(__)([a-z])+$#i', $val);
				});
				if (!empty($pageToEditCheck)) {
                    $pageToEdit = str_replace('__', '', strstr(Arr::flatten($pageToEditCheck)[0], '__'));
                }
				if (array_key_exists($pageToEdit, $this->pages_default_list)) {
                    $page_path = Page::XML_PAGE_DEFAULT_FOLDER_PATH . $this->pages_default_list[$pageToEdit].'.xml';
                } else {
                    $page_path = Page::XML_PAGE_FOLDER_PATH . $pageToEdit . '.xml';
                }

                $xml = new DOMDXMLParser(storage_path('app/' . $page_path));
                $xml->pickNode('page')->changeData('menuOrder', $xml->pickNode('page')->getAttr('menuOrder') + 1);
			}
		}
		/* Adds new page with the correct menu order number */
		$page_name_slug = str_replace('-', '_', Str::slug($page_name));
		\Storage::put(Page::XML_PAGE_FOLDER_PATH . $page_name_slug . '.xml', '<?xml version="1.0" encoding="UTF-8"?><data><page public="0"><![CDATA[]]></page></data>');
		if (array_key_exists($page_name_slug, $this->pages_default_list)) {
            $page_path = Page::XML_PAGE_DEFAULT_FOLDER_PATH . $this->pages_default_list[$page_name_slug] . '.xml';
        } else {
            $page_path = Page::XML_PAGE_FOLDER_PATH . $page_name_slug . '.xml';
        }

        $xml = new DOMDXMLParser(storage_path('app/' . $page_path));
        $xml->pickNode('page')->changeData([
            'public' => 0,
            'menuOrder' => $order_menu + 1,
            'menuName' => $page_name,
            'CDATA' => $content
        ]);

		return [
			'menuOrder' => (int) $order_menu + 1,
			'page_name' => $page_name,
			'page_link' => str_replace('_', '-', $page_name_slug),
			'content' => $content
		];
	}
	/**
	 * Changes the menu order
	 *
	 * Updates the menu order list accordingly
	 *
	 * @param 	string		$page_name			The name of the page to change in the menu
	 * @param 	string		$order_menu_new		The number of the place to appear in the menu
	 * @return 	array							Returns an array with the following infos : the newly created page name, the page menu order number, its link and its content if any set
	 **/
	public function changeMenuOrder($page_name, $order_menu_new)
	{
		$page_name = strtolower(str_replace('-', '_', $page_name));
		/* Updates menu order of the other pages first */
		parent::__construct($page_name, 'PAGES_LINKS_AND_MENU_ORDER');
		$order_menu = $this->menu_order;
		$menuList = $this->pages_links_titles;
		if ($order_menu - $order_menu_new < 0) {
			$op = false;
			$pageMenu = array_slice($menuList, $order_menu, $order_menu_new - $order_menu);
		} else {
			$op = true;
			$pageMenu = array_slice($menuList, $order_menu_new - 1, $order_menu - $order_menu_new);
		}
		$pageMenuNames = Arr::flatten($pageMenu);
		$pageMenuFiles = array_map(function($val) {
			return str_replace('-', '_', Str::slug($val));
		}, $pageMenuNames);

		if (!empty($pageMenuFiles))	{
			foreach ($pageMenuFiles as $pageToEdit)	{
				$pageToEditCheck = Arr::where($this->pages_default_list, function ($val) use ($pageToEdit) {
					return preg_match('#^(' . $pageToEdit . ')(__)([a-z])+$#i', $val);
				});
				if (!empty($pageToEditCheck)) {
                    $pageToEdit = str_replace('__', '', strstr(Arr::flatten($pageToEditCheck)[0], '__'));
                }

				if (array_key_exists($pageToEdit, $this->pages_default_list)) {
                    $page_path = Page::XML_PAGE_DEFAULT_FOLDER_PATH . $this->pages_default_list[$pageToEdit] . '.xml';
                } else {
                    $page_path = Page::XML_PAGE_FOLDER_PATH . $pageToEdit . '.xml';
                }

                $xml = new DOMDXMLParser(storage_path('app/' . $page_path));
                $xml->pickNode('page')->changeData('menuOrder', ($op) ? $xml->pickNode('page')->getAttr('menuOrder') + 1 : $xml->pickNode('page')->getAttr('menuOrder') - 1);
			}
		}
		/* Updates new page with the correct menu order number */
		if (array_key_exists($page_name, $this->pages_default_list)) {
            $page_path = Page::XML_PAGE_DEFAULT_FOLDER_PATH . $this->pages_default_list[$page_name] . '.xml';
        } else {
            $page_path = Page::XML_PAGE_FOLDER_PATH . $page_name . '.xml';
        }

        $xml = new DOMDXMLParser(storage_path('app/' . $page_path));
        $xml->pickNode('page')->changeData('menuOrder', $order_menu_new);

		return [
			'menuOrder' => (int) $order_menu_new,
			'menu_name' => $xml->pickNode('page')->getAttr('menuName'),
			'page_name' => str_replace('_', '-', $page_name),
		];
	}
	/**
	 * Deletes a page in a XML file
	 *
	 * Deletes a page in the provided page XML file
	 *
	 * @param 	string		$page_name		The name of the page to delete
	 * @return 	array						Returns an array with the following infos : home's content, the menu updated and the menu order page as well as the home page title name
	 **/
	public function deletePage($page_name)
	{
		$page_name = strtolower(str_replace('-', '_', $page_name));
		parent::__construct($page_name, 'PAGES_LINKS_AND_MENU_ORDER');
		$order_menu = $this->menu_order;
		$order_menu = $this->fetchMenuOrder($page_name);
		$menuList = $this->pages_links_titles;
		$pageMenu = array_slice($menuList, $order_menu);
		$pageMenuNames = Arr::flatten($pageMenu);
		$pageMenuFiles = array_map(function($val) {
			return str_replace('-', '_', Str::slug($val));
        }, $pageMenuNames);
        $arrayMenuOrderUpdate = [];
		if (!empty($pageMenuFiles))	{
			foreach ($pageMenuFiles as $pageToEdit)	{
				$pageToEditCheck = Arr::where($this->pages_default_list, function ($val) use ($pageToEdit) {
					return preg_match('#^(' . $pageToEdit . ')(__)([a-z])+$#i', $val);
				});
				if (!empty($pageToEditCheck)) {
                    $pageToEdit = str_replace('__', '', strstr(Arr::flatten($pageToEditCheck)[0], '__'));
                }
				if (array_key_exists($pageToEdit, $this->pages_default_list)) {
                    $page_path = Page::XML_PAGE_DEFAULT_FOLDER_PATH . $this->pages_default_list[$pageToEdit] . '.xml';
                } else {
                    $page_path = Page::XML_PAGE_FOLDER_PATH . $pageToEdit . '.xml';
                }

                $xml = new DOMDXMLParser(storage_path('app/' . $page_path));
                $arrayMenuOrderUpdate[] = $xml->pickNode('page')->getAttr('menuOrder');
                $xml->pickNode('page')->changeData('menuOrder', $xml->pickNode('page')->getAttr('menuOrder') - 1);
			}
		}
		\Storage::delete(Page::XML_PAGE_FOLDER_PATH . $page_name . '.xml');
        parent::__construct('home', 'CONTENT_AND_PAGE_NAME_AND_PAGE_STATE');

		return [
			'content' => $this->getContent(),
			'publishState' => $this->getPageState(),
			'menu_update' => $arrayMenuOrderUpdate,
			'menuOrder' => (int) $order_menu,
			'home_page_name' => $this->getPageName()
		];
	}
}
