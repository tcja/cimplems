<?php

namespace App\Libraries;

use Illuminate\Support\Arr;
use Mobile_Detect;
use Tcja\DOMDXMLParser\DOMDXMLParser;

/*
 *
 * Page class.
 *
 * Author : Trim Camaj
 *
 * Description : Class used to retrieve informations about pages
 *
 */
class Page
{
	/**
	 *
	 *
	 * @var string $content Page's content
	 * */
	protected $content;
	/**
	 *
	 *
	 * @var array $pages_links_titles List of the pages links
	 * */
	protected $pages_links_titles;
	/**
	 *
	 *
	 * @var array $menu_order Current menu order number
	 * */
	protected $menu_order;
	/**
	 *
	 *
	 * @var array $pages_default Default pages array
	 * */
	protected $pages_default_list;
	/**
	 *
	 *
	 * @var array $pages_list User pages array
	 * */
	protected $pages_list;
	/**
	 *
	 *
	 * @var array $all_pages_list All pages array
	 * */
	protected $all_pages_list;
	/**
	 *
	 *
	 * @var string $real_slug page's real slug
	 * */
	protected $real_slug;
	/**
	 *
	 *
	 * @var string $page_name page's title name
	 * */
	protected $page_name;
	/**
	 *
	 *
	 * @var bool $page_state page's state
	 * */
    protected $page_state;
    /**
	 *
	 *
	 * @var bool $private_pages private pages list
	 * */
	protected $private_pages;

	/**
	 *
	 *  @var string $XML_PAGE_FOLDER_PATH Default pages folder location
	 * */
	const XML_PAGE_FOLDER_PATH = 'private/pages/';
	/**
	 *
	 *  @var string $XML_PAGE_FOLDER_PATH Default pages folder location
	 * */
	const XML_PAGE_DEFAULT_FOLDER_PATH = 'private/pages_default/';

	/**
	 * Constructor used to retrieve content from an html page
	 *
	 * Retrieves content from an html page and stores it in $content property
	 *
	 * @param 	int			$page		Page name to be retrieved, if not set defaults to home if no page provided
	 * @param 	string		$action		The action te be performed
	 * @return	void
	 **/
 	public function __construct($page = 'home', $action = null)
	{
        if ($action === 'CHECK_FILES') {
            return;
        }

		$page = strtolower(str_replace('-', '_', $page));
		$this->pages_default_list = $this->fetchPagesDefault();
		$this->pages_list = $this->fetchPages();
		if (session('admin') === true) {
            $this->all_pages_list = $this->fetchAllPages();
        } else {
            $this->all_pages_list = $this->fetchAllPages(true);
        }
		if ($action === 'ALL_PAGES_LIST') {
            return;
        } elseif ($action === 'PAGE_NAME') {
            $this->page_name = $this->fetchPageName($page);
        } elseif ($action === 'PRIVATE_PAGES') {
            $this->private_pages = $this->fetchPrivatePages();
        } elseif ($action === 'PAGES_TITLES_LINKS') {
            $this->pages_links_titles = $this->fetchPagesLinksTitles();
        } elseif ($action === 'PAGES_TITLES_LINKS_AND_MENU_ORDER_NUMBER') {
			$this->pages_links_titles = $this->fetchPagesLinksTitles();
			$this->menu_order = $this->fetchMenuOrder($page);
		} elseif ($action === 'CURRENT_PAGE_MENU_ORDER_NUMBER') {
            $this->menu_order = $this->fetchMenuOrder($page);
        } elseif ($action === 'PAGES_LINKS_AND_MENU_ORDER') {
			$this->pages_links_titles = $this->fetchPagesLinksTitles();
			$this->menu_order = $this->fetchMenuOrder($page);
		} elseif ($action === 'CONTENT') {
            $this->content = $this->fetchContent($page);
        } elseif ($action === 'CONTENT_AND_PAGE_NAME') {
			$this->content = $this->fetchContent($page);
			$this->page_name = $this->fetchPageName($page);
		} elseif ($action === 'CONTENT_AND_PAGE_NAME_AND_PAGE_STATE') {
			$this->content = $this->fetchContent($page);
			$this->page_state = $this->fetchPageState($page);
			$this->page_name = $this->fetchPageName($page);
		} elseif ($action === 'CONTENT_AND_MENU_ORDER_NUMBER') {
			$this->content = $this->fetchContent($page);
			$this->menu_order = $this->fetchMenuOrder($page);
		} elseif ($action === 'CONTENT_AND_MENU_ORDER_NUMBER_AND_SLUG') {
			$this->content = $this->fetchContent($page);
			$this->menu_order = $this->fetchMenuOrder($page);
			$this->real_slug = $this->fetchRealSlug($page);
		} elseif ($action === 'CONTENT_AND_MENU_ORDER_NUMBER_AND_SLUG_AND_PAGE_STATE_AND_PAGE_TITLE') {
			$this->content = $this->fetchContent($page);
			$this->page_name = $this->fetchPageName($page);
			$this->page_state = $this->fetchPageState($page);
			$this->menu_order = $this->fetchMenuOrder($page);
			$this->real_slug = $this->fetchRealSlug($page);
		} elseif ($action === 'CONTENT_AND_MENU_ORDER_NUMBER_AND_SLUG_AND_PAGE_STATE') {
			$this->content = $this->fetchContent($page);
			$this->page_state = $this->fetchPageState($page);
			$this->menu_order = $this->fetchMenuOrder($page);
			$this->real_slug = $this->fetchRealSlug($page);
		} else {
			$this->content = $this->fetchContent($page);
			$this->page_name = $this->fetchPageName($page);
			$this->page_state = $this->fetchPageState($page);
			$this->menu_order = $this->fetchMenuOrder($page);
			$this->pages_links_titles = $this->fetchPagesLinksTitles();
			$this->real_slug = $this->fetchRealSlug($page);
		}
	}
	/**
	 * Fetches the content of the requested page
	 *
	 * Fetches the content of the requested page and returns it in form of a string
	 *
	 * @param 	string		$page		Page's name to fetch the content from
	 * @return	mixed					Returns a string of the content or an array for the "contact" page with the content and the "contact" form
	 **/
	protected function fetchContent($page)
	{
		$contactForm = false;
		$galleries = false;
		if ($page == 'home' || $page == 'gallery' || $page == 'contact') {
			$default_page = $this->fetchPagesDefault($page);
			$page_path = self::XML_PAGE_DEFAULT_FOLDER_PATH . $default_page . '.xml';
			if ($page == 'contact') {
                $contactForm = view(config('site.theme_dir') . config('site.theme') . '/' . 'contact')->render();
            } elseif ($page == 'gallery') {
				$gallery = new Gallery;
				$array_images = $gallery->getImagesArray();
				$galleries_names = $gallery->getGalleriesArray();
				$mobile_detect = new Mobile_Detect;
				if ($mobile_detect->isMobile() || $mobile_detect->isTablet()) {
					$galleries = view(config('site.theme_dir') . config('site.theme') . '/' . 'gallery_mobile', [
						'rootUrl' => url('/'),
						'isMobile' => true,
						'pageLinks' => $this->pages_links_titles,
						'currentSlug' => 'gallery',
						'currentMenuOrder' => $this->menu_order,
						'array_images' => $array_images,
						'galleries_names' => $galleries_names
					])->render();
				} else {
					$galleries = view(config('site.theme_dir') . config('site.theme') . '/' . 'gallery', [
						'rootUrl' => url('/'),
						'isMobile' => false,
						'pageLinks' => $this->pages_links_titles,
						'currentSlug' => 'gallery',
						'currentMenuOrder' => $this->menu_order,
						'array_images' => $array_images,
						'galleries_names' => $galleries_names
					])->render();
				}
			}
		} else {
            $page_path = self::XML_PAGE_FOLDER_PATH . $page . '.xml';
        }

        $xml = new DOMDXMLParser(storage_path('app/' . $page_path));
        $content = $xml->pickNode('page')->getValue();


		if ($contactForm) {
            return ['content' => $content, 'contactForm' => $contactForm];
        } elseif ($galleries) {
            return ['content' => $content, 'galleries' => $galleries];
        } else {
            return $content;
        }
	}
	/**
	 * Fetches the menu order number
	 *
	 * Fetches the current's page menu order number
	 *
	 * @param 	string		$page		Page's name to fetch the data from
	 * @return	int						Returns the page's menu order number
	 **/
	protected function fetchMenuOrder($page)
	{
		if ($page == 'home' || $page == 'gallery' || $page == 'contact') {
			$default_page = $this->fetchPagesDefault($page);
			$page_path = self::XML_PAGE_DEFAULT_FOLDER_PATH . $default_page . '.xml';
		} else {
			if (array_key_exists($page, $this->pages_default_list)) {
                $page_path = Page::XML_PAGE_DEFAULT_FOLDER_PATH . $this->pages_default_list[$page] . '.xml';
            } elseif (!in_array($page, $this->pages_list)) {
                return;
            } else {
                $page_path = self::XML_PAGE_FOLDER_PATH . $page . '.xml';
            }
		}

        $xml = new DOMDXMLParser(storage_path('app/' . $page_path));

		return (int) $xml->pickNode('page')->getAttr('menuOrder');
	}
	/**
	 * Fetches the default pages
	 *
	 * Fetches the default pages and returns them in an array
	 *
	 * @param 	bool		$real_name		If set to true, will return the default pages real names, defaults to false returns the pages name without _home/_gallery/_contact
	 * @return	mixed						Return the list of the default pages by default (if set to false) or returns the full default page name if specified from $real_name param or returns
     *                                      false if the specific default page was not found
	 **/
	public function fetchPagesDefault($real_name = false)
	{
		if ($real_name) {
			$pages_list = scandir(storage_path('app/private/pages_default'));
			Arr::pull($pages_list, 0);
			Arr::pull($pages_list, 1);
			$pages_list = array_values($pages_list);
			$pages_list = array_map(function($page) {
				return strstr($page, '.', true);
			}, $pages_list);

			if ($real_name == 'home' || $real_name == 'gallery' || $real_name == 'contact') {
				$page = Arr::where($pages_list, function ($val) use ($real_name) {
					return strpos($val, '__'.$real_name);
				});
				return !empty(Arr::flatten($page)[0]) ? Arr::flatten($page)[0] : false;
			} else {
                return false;
            }
		} else {
			$pages_list = scandir(storage_path('app/private/pages_default'));
			Arr::pull($pages_list, 0);
			Arr::pull($pages_list, 1);
			$pages_list = array_values($pages_list);
			$pages_list = array_map(function($page) {
				return [str_replace('__', '', strstr(strstr($page, '.', true), '__')) => strstr($page, '.', true)];
			}, $pages_list);
			$pages_list = Arr::collapse($pages_list);
		}
		return $pages_list;
	}
	/**
	 * Fetches the user pages
	 *
	 * Fetches the user created pages and returns them in an array
	 *
	 * @return	array		Return the list of the pages
	 **/
	protected function fetchPages()
	{
		$pages_list = scandir(storage_path('app/private/pages'));
        Arr::pull($pages_list, 0);
        Arr::pull($pages_list, 1);

		$pages_list = array_values($pages_list);
        $pages_list = array_map(function($page) {
            return [strstr($page, '.', true) => strstr($page, '.', true)];
		}, $pages_list);

		return Arr::collapse($pages_list);
	}
	/**
	 * Fetches all pages
	 *
	 * Fetches all pages (both user and default ones as well as public and private) and returns them in an array
	 *
	 * @param 	bool		$public			If set to true, will only return pages set to public state
	 * @return	array						Return the list of the pages
	 **/
	protected function fetchAllPages($public = false)
	{
		if ($public) {
			$pages_list = $this->fetchAllPages();
			$array = [];
			foreach ($pages_list as $slug => $page) {
				if ($this->fetchPageState($slug) === 1 || $slug === 'home') {
                    $array[$slug] = $page;
                }
			}
			return $array;
		} else {
            return array_merge($this->pages_default_list, $this->pages_list);
        }
    }
    /**
	 * Fetches private pages
	 *
	 * Fetches all private pages and returns them in an array
	 *
	 * @return	array   	Return the list of the private pages
	 **/
	protected function fetchPrivatePages()
	{
        $pages_list = $this->fetchAllPages();
        $array = [];
        foreach ($pages_list as $slug => $page) {
            if ($this->fetchPageState($slug) === 0) {
                $array[$slug] = $this->fetchPageName($page);
            }
        }
        return array_values($array);
    }
    /**
	 * Fetches public pages
	 *
	 * Fetches all public pages and returns them in an array
	 *
     * @return	array		Return the list of the public pages
	 **/
	protected function fetchPublicPages()
	{
        $pages_list = $this->fetchAllPages();
        $array = [];
        foreach ($pages_list as $slug => $page) {
            if ($this->fetchPageState($slug) === 1) {
                $array[$slug] = $this->fetchPageName($page);
            }
        }
        return $array;
	}
	/**
	 * Fetches the menu titles and links
	 *
	 * Fetches the menu titles and links with the right order from their respective page name and returns them in an array
	 *
	 * @return	array	Returns an array of the menu titles
	 **/
	protected function fetchPagesLinksTitles()
	{
		$pages_list = $this->all_pages_list;
		$array = [];
		foreach ($pages_list as $slug => $page) {
			if (in_array($page, $this->pages_default_list)) {
                $page_path = self::XML_PAGE_DEFAULT_FOLDER_PATH . $page . '.xml';
            } else {
				$page_path = self::XML_PAGE_FOLDER_PATH . $page . '.xml';
				$slug = $page;
            }

            $xml = new DOMDXMLParser(storage_path('app/' . $page_path));
            $array[$xml->pickNode('page')->getAttr('menuOrder')] = [$slug => $xml->pickNode('page')->getAttr('menuName')];
		}
        ksort($array);

		return $array;
	}
	/**
	 * Fetches the real slug
	 *
	 * Fetches the real slug of a given page
	 *
	 * @param 	string		$page		Page's name to fetch the data from
	 * @return	string					Returns the real slug
	 **/
	protected function fetchRealSlug($page)
	{
		if ($page == 'home' || $page == 'gallery' || $page == 'contact') {
            $real_slug = $page;
        } else {
			if (array_key_exists($page, $this->pages_default_list)) {
                $real_slug = $page;
            } elseif (in_array($page, $this->pages_list)) {
                $real_slug = $page;
            }
		}
		return $real_slug;
	}
	/**
	 * Fetches the page's title name
	 *
	 * Fetches the page's title name of a given page
	 *
	 * @param 	string		$page		Page's name to fetch the data from
	 * @return	string					Returns the page's title name
	 **/
	protected function fetchPageName($page)
	{
		if ($page == 'home' || $page == 'gallery' || $page == 'contact') {
			$default_page = $this->fetchPagesDefault($page);
			$page_path = self::XML_PAGE_DEFAULT_FOLDER_PATH . $default_page . '.xml';
		} else {
			if (array_key_exists($page, $this->pages_default_list)) {
                $page_path = self::XML_PAGE_DEFAULT_FOLDER_PATH . $this->pages_default_list[$page] . '.xml';
            } elseif (in_array($page, $this->pages_default_list)) {
                $page_path = self::XML_PAGE_DEFAULT_FOLDER_PATH . $page . '.xml';
            } elseif (!in_array($page, $this->pages_list)) {
                return;
            } else {
                $page_path = self::XML_PAGE_FOLDER_PATH . $page . '.xml';
            }
		}
        $xml = new DOMDXMLParser(storage_path('app/' . $page_path));

		return $xml->pickNode('page')->getAttr('menuName');
	}
	/**
	 * Fetches the page's publish state
	 *
	 * Fetches the page's publish state
	 *
	 * @param 	string		$page		Page's name to fetch the data from
	 * @return	bool					Return true (1) if the page is published and false (0) if not
	 **/
	protected function fetchPageState($page)
	{
		if ($page == 'home' || $page == 'gallery' || $page == 'contact') {
			$default_page = $this->fetchPagesDefault($page);
			$page_path = self::XML_PAGE_DEFAULT_FOLDER_PATH . $default_page . '.xml';
		} else {
			if (array_key_exists($page, $this->pages_default_list)) {
                $page_path = self::XML_PAGE_DEFAULT_FOLDER_PATH . $this->pages_default_list[$page] . '.xml';
            } elseif (!in_array($page, $this->pages_list)) {
                return;
            } else {
                $page_path = self::XML_PAGE_FOLDER_PATH . $page . '.xml';
            }
		}
        $xml = new DOMDXMLParser(storage_path('app/' . $page_path));

		return (int) $xml->pickNode('page')->getAttr('public');
	}
	/**
	 * Gets page's content from its property
	 *
	 *
	 * @return 	string	Returns the page's content
	 **/
	public function getContent()
	{
		return $this->content;
	}
	/**
	 * Gets pages's titles and links
	 *
	 *
	 * @return 	array	Returns the page's titles and links
	 **/
	public function getPagesLinksTitles()
	{
		return $this->pages_links_titles;
	}
	/**
	 * Gets default pages
	 *
	 *
	 * @return 	array	Returns the page's titles and links
	 **/
	public function getPagesDefaultList()
	{
		return $this->pages_default_list;
	}
	/**
	 * Gets pages
	 *
	 *
	 * @return 	array	Returns the user pages
	 **/
	public function getPagesList()
	{
		return $this->pages_list;
	}
	/**
	 * Gets all pages
	 *
	 *
	 * @return 	array	Returns all pages
	 **/
	public function getAllPagesList()
	{
		return $this->all_pages_list;
	}
	/**
	 * Gets pages's current menu order number
	 *
	 *
	 * @return 	int	Returns the pages's current menu order number
	 **/
	public function getMenuOrder()
	{
		return $this->menu_order;
	}
	/**
	 * Gets pages's real slug
	 *
	 *
	 * @return 	string	Returns the pages's real slug
	 **/
	public function getRealSlug()
	{
		return $this->real_slug;
	}
	/**
	 * Gets pages's name title
	 *
	 *
	 * @return 	string	Returns the pages's name title
	 **/
	public function getPageName()
	{
		return $this->page_name;
	}
	/**
	 * Gets pages's publish state
	 *
	 *
	 * @return 	string	Returns the pages's publish state
	 **/
	public function getPageState()
	{
		return $this->page_state;
    }
    /**
	 * Gets private pages list
	 *
	 *
	 * @return 	string	Returns the private pages list
	 **/
	public function getPrivatePages()
	{
		return $this->private_pages;
	}
	/**
	 * Finds the highest number order for the menu titles
	 *
	 *
	 * @return 	int	Returns the highest match found
	 **/
	public function getMaxLinkOrder()
	{
		return max(array_keys($this->pages_links_titles));
	}
}
