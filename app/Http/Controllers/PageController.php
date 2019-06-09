<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Libraries\Gallery;
use Mobile_Detect;
use App\Libraries\Page;
use App\Libraries\CheckDefaultFiles;
//use App\Libraries\User;
//use App\Libraries\EditUser;
use App\Libraries\EditPage;
//use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\Mail;
//use Illuminate\Support\Arr;
//use Illuminate\Pagination\LengthAwarePaginator;

class PageController extends Controller
{
    public function index($slug = 'home', Mobile_Detect $mobile_detect/*  , Request $request */)
    {
        /* $lolz = ['asd', 'asdy', 'asd', 'asdy', 'asdd', 'asfdy', 'ased', 'asdsy', 'asxd', 'asfdy', 'asgd', 'agsdy', 'ahsd', 'asdy', 'agsd', 'asdjy', 'arsd', 'asd3y', 'awsd', 'assdy', 'asd', 'asxdy'];
        $bise = new LengthAwarePaginator($lolz, count($lolz), 6, 1);
        echo ($bise->render()); */
        //echo __('passwords.token');
        //$test->fetchPublicPages();
        //dd($test->fetchPrivatePages());

        $this->listPrivatePages();
        CheckDefaultFiles::checkDefaultFiles();

        if ($mobile_detect->isMobile() || $mobile_detect->isTablet()) {
            $isMobile = true;
        } else {
            $isMobile = false;
        }

        $slug = strtolower(str_replace('-', '_', $slug));
        $pages_list = new Page($slug, 'ALL_PAGES_LIST');//dd($pages_list->getAllPagesList());
        if (array_key_exists($slug, $pages_list->getAllPagesList())) {
            $checkHome = new Page('home');
            if ($checkHome->getRealSlug() == 'home' && !$checkHome->getPageState() && empty(session('admin'))) {
                return view('under_construction', ['rootUrl' => url('/')]);
            } else {
                $page = new Page($slug);
                if ($isMobile) {
                    return view(config('site.theme_dir') . config('site.theme') . '/' . 'site_mobile', [
                        'rootUrl' => url('/'),
                        'isMobile' => $isMobile,
                        'pageLinks' => $page->getPagesLinksTitles(),
                        'page' => 'page',
                        'publishState' => $page->getPageState(),
                        'currentPageTitle' => $page->getPageName(),
                        'currentSlug' => $page->getRealSlug(),
                        'currentMenuOrder' => $page->getMenuOrder(),
                        'content' => $page->getContent()
                    ]);
                } else {
                    return view(config('site.theme_dir') . config('site.theme') . '/' . 'site', [
                        'rootUrl' => url('/'),
                        'isMobile' => $isMobile,
                        'pageLinks' => $page->getPagesLinksTitles(),
                        'page' => 'page',
                        'publishState' => $page->getPageState(),
                        'currentPageTitle' => $page->getPageName(),
                        'currentSlug' => $page->getRealSlug(),
                        'currentMenuOrder' => $page->getMenuOrder(),
                        'content' => $page->getContent()
                    ]);
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
        if (!$validator->fails()) {
            return response()->json($edit_page->editPage($request->slug, $request->content));
        } else {
            return response()->json('fail');
        }
    }

    public function addPage(Request $request, EditPage $edit_page)
    {
        $validator = \Validator::make($request->all(), [
            'page_name' => 'required',
            'afterMenu' => 'required|numeric'
        ]);
        if (!$validator->fails()) {
            return $edit_page->addPage($request->page_name, $request->afterMenu);
        } else {
            return response()->json('fail');
        }
    }

    public function deletePage(Request $request, EditPage $edit_page)
    {
        $validator = \Validator::make($request->all(), [
            'page_name_delete' => 'required'
        ]);
        if (!$validator->fails()) {
            if (!empty($request->array_images)) {
                foreach ($request->array_images as $image_name) {
                    if (!\Storage::exists(storage_path('app/public/images_site/' . $image_name))) {
                        \File::delete(storage_path('app/public/images_site/' . $image_name));
                    }
                }
            }
            return $edit_page->deletePage($request->page_name_delete);
        } else {
            return response()->json('fail');
        }
    }

    public function showPageAjax(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'page_name_show' => 'required'
        ]);
        if (!$validator->fails()) {
            $page = new Page($request->page_name_show, 'CONTENT_AND_MENU_ORDER_NUMBER_AND_SLUG_AND_PAGE_STATE_AND_PAGE_TITLE');
            return [
                'content' => $page->getContent(),
                'currentMenuOrder' => $page->getMenuOrder(),
                'currentPageTitle' => $page->getPageName(),
                'currentSlug' => str_replace('_', '-', $page->getRealSlug()),
                'publishState' => $page->getPageState()
            ];
        } else {
            return response()->json('fail');
        }
    }

    public function changeMenuOrder(Request $request, EditPage $edit_page)
    {
        $validator = \Validator::make($request->all(), [
            'page_name_menu' => 'required',
            'order_menu_new' => 'required|numeric'
        ]);
        if (!$validator->fails()) {
            return $edit_page->changeMenuOrder($request->page_name_menu, $request->order_menu_new);
        } else {
            return response()->json('fail');
        }
    }

    public function changePageName(Request $request, EditPage $edit_page)
    {
        $validator = \Validator::make($request->all(), [
            'page_name_menu_change' => 'required'
        ]);
        if (!$validator->fails()) {
            return $edit_page->changePageName($request->page_name_menu_change, $request->page_name_old);
        } else {
            return response()->json('fail');
        }
    }

    public function changePageState(Request $request, EditPage $edit_page)
    {
        $validator = \Validator::make($request->all(), [
            'page_name' => 'required',
            'page_state' => 'required|numeric'
        ]);
        if (!$validator->fails()) {
            return response()->json($edit_page->changePageState($request->page_name, $request->page_state));
        } else {
            return response()->json('fail');
        }
    }

    public function uploadImage(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'image' => 'required',
            'image_name' => 'required'
        ]);
        if (!$validator->fails()) {
            $upload_path = storage_path('app/public/images_site/');
            $widen_width = 1280;

            if ($request->total_files === 0) {
                return response()->json('erreur, veuillez rééessayer');
            }

            $file = $request->file('image');

            if (in_array($file->getMimeType(), array('image/gif', 'image/png', 'image/bmp', 'image/jpeg'))) {
                if ($file->getMimeType() == 'image/gif') {
                    $ext = '.gif';
                } elseif ($file->getMimeType() == 'image/png') {
                    $ext = '.png';
                } else {
                    $ext = '.jpg';
                }

                $new_file_name = \Illuminate\Support\Str::slug($request->image_name).$ext;

                $width = getimagesize($file->getRealPath());
                if ($width[0] <= $widen_width) {
                    if ($upload_done = $file->move($upload_path, $new_file_name)) {
                    } else {
                        return json_encode($upload_done->getMessage());
                    }
                } else {
                    \Image::make($file->getRealPath())->widen($widen_width, function ($constraint) { $constraint->upsize(); })->save($upload_path.$new_file_name);
                }
            } else {
                return response()->json('fail');
            }

            return response()->json($new_file_name);
        } else {
            return response()->json('fail');
        }
    }

    public function deleteSiteImage(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'image_name' => 'required'
        ]);
        if (!$validator->fails()) {
            if (!\Storage::exists(storage_path('app/public/images_site/' . $request->image_name))) {
                \File::delete(storage_path('app/public/images_site/' . $request->image_name));
            } else {
                return response()->json('file_not_found');
            }

            return response()->json('file_removed');
        } else {
            return response()->json('fail');
        }
    }

    public function listPrivatePages()
    {
        $page = new Page('GET', 'PRIVATE_PAGES');
        return $page->getPrivatePages();
    }

    public function showHomePage()
    {
        $page = new Page('home', 'CONTENT_AND_PAGE_NAME_AND_PAGE_STATE');
        return ['publishState' => $page->getPageState(), 'content' => $page->getContent()];
    }
}
