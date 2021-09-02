<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Page;
use App\Libraries\EditPage;
use App\Libraries\EditGallery;
use App\Libraries\CheckDefaultFiles;
use Mobile_Detect;

class PageController extends Controller
{
    public function index(Mobile_Detect $mobile_detect, $slug = 'home')
    {
        $this->listPrivatePages();
        CheckDefaultFiles::checkDefaultFiles();

        if ($mobile_detect->isMobile() || $mobile_detect->isTablet()) {
            $isMobile = true;
        } else {
            $isMobile = false;
        }

        $pages_list = new Page($slug, 'ALL_PAGES_LIST');
        if (array_key_exists(strtolower(str_replace('-', '_', $slug)), $pages_list->getAllPagesList())) {
            $checkHome = new Page('home');
            if ($checkHome->getPageSlug() == 'home' && !$checkHome->getPageState() && empty(session('admin'))) {
                return view('under_construction', ['rootUrl' => url('/')]);
            } else {
                $page = new Page($slug);
                $data = [
                    'rootUrl' => url('/'),
                    'isMobile' => $isMobile,
                    'page' => 'page',
                    'pageLinks' => $page->getPagesLinksTitles(),
                    'publishState' => $page->getPageState(),
                    'pageTitle' => $page->getPageTitle(),
                    'pageSlug' => $page->getPageSlug(),
                    'pageName' => $page->getPageName(),
                    'menuOrder' => $page->getMenuOrder(),
                    'content' => $page->getContent()
                ];

                if ($isMobile) {
                    return view('site_mobile', $data);
                } else {
                    return view('site', $data);
                }
            }
        } else {
            abort(404);
        }
    }

    public function editPage(Request $request, EditPage $edit_page)
    {
        $validator = \Validator::make($request->all(), [
            'slug' => 'required',
            'content' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json('fail');
        }

        return response()->json($edit_page->editPage(str_replace('-', '_', $request->slug), $request->content));
    }

    public function addPage(Request $request, EditPage $edit_page)
    {
        $validator = \Validator::make($request->all(), [
            'page_name' => 'required',
            'afterMenu' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json('fail');
        }

        return $edit_page->addPage($request->page_name, $request->afterMenu);
    }

    public function deletePage(Request $request, EditPage $edit_page)
    {
        $validator = \Validator::make($request->all(), [
            'page_name_delete' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json('fail');
        }

        if (!empty($request->array_images)) {
            foreach ($request->array_images as $image_name) {
                if (!\Storage::exists(storage_path(config('site.page_images_path') . $image_name))) {
                    \File::delete(storage_path(config('site.page_images_path') . $image_name));
                }
            }
        }

        return $edit_page->deletePage($request->page_name_delete);
    }

    public function showPageAjax(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'page_name_show' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json('fail');
        }

        $page = new Page($request->page_name_show, 'CONTENT_AND_MENU_ORDER_NUMBER_AND_SLUG_AND_PAGE_STATE_AND_PAGE_TITLE');

        return [
            'content' => $page->getContent(),
            'menuOrder' => $page->getMenuOrder(),
            'pageTitle' => $page->getPageTitle(),
            'pageName' => $page->getPageName(),
            'slug' => $page->getPageSlug(),
            'publishState' => $page->getPageState()
        ];
    }

    public function changeMenuOrder(Request $request, EditPage $edit_page)
    {
        $validator = \Validator::make($request->all(), [
            'page_name_menu' => 'required',
            'order_menu_new' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json('fail');
        }

        return $edit_page->changeMenuOrder($request->page_name_menu, $request->order_menu_new);
    }

    public function changePageName(Request $request, EditPage $edit_page)
    {
        $validator = \Validator::make($request->all(), [
            'page_name_menu_change' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json('fail');
        }

        return $edit_page->changePageName($request->page_name_menu_change, $request->page_name_old);
    }

    public function changePageState(Request $request, EditPage $edit_page)
    {
        $validator = \Validator::make($request->all(), [
            'page_name' => 'required',
            'page_state' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json('fail');
        }

        return response()->json($edit_page->changePageState($request->page_name, $request->page_state));
    }

    public function uploadImage(Request $request, EditGallery $edit_gallery)
    {
        $validator = \Validator::make($request->all(), [
            'image' => 'required',
            'image_name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json('fail');
        }

        return response()->json($edit_gallery->uploadImageForPage($request));
    }

    public function deleteSiteImage(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'image_name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json('fail');
        }

        if (!\Storage::exists(storage_path(config('site.page_images_path') . $request->image_name))) {
            \File::delete(storage_path(config('site.page_images_path') . $request->image_name));
        } else {
            return response()->json('file_not_found');
        }

        return response()->json('file_removed');
    }

    public function listPrivatePages()
    {
        $page = new Page('GET', 'PRIVATE_PAGES');

        return $page->getPrivatePages();
    }

    public function showHomePage()
    {
        $page = new Page('home', 'CONTENT_AND_PAGE_TITLE_AND_PAGE_STATE');

        return [
            'publishState' => $page->getPageState(),
            'content' => $page->getContent()
        ];
    }
}
