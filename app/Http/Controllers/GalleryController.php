<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Gallery;
use App\Libraries\EditGallery;
use Mobile_Detect;

class GalleryController extends Controller
{
    public function doUpload(Request $request, EditGallery $edit_gallery)
	{
        $validator = \Validator::make($request->all(), [
            'image_title' => 'nullable|max:50',
            'gallery' => 'required|numeric',
            'total_files' => 'required|numeric',
        ]);

		if (!$validator->fails()) {
            return $edit_gallery->uploadImage($request, $request->image_title, $request->gallery);
		} else {
            return redirect('/');
        }
    }

    public function createGallery(Request $request, EditGallery $edit_gallery)
	{
        $validator = \Validator::make($request->all(), [
            'gallery_title' => 'required|min:3|max:50'
        ]);

		if (!$validator->fails()) {
			$edit_gallery->addGallery($request->gallery_title);
			$result = 'success';
		} else {
            $result = 'fail';
        }

		return response()->json($request->gallery_title);
    }

    public function editGalleries(Request $request, EditGallery $edit_gallery)
	{
		foreach ($request->all() as $gallery_id => $galleryName) {
			if (is_int($gallery_id)) {
                $validator = \Validator::make($request->all(), [
                    'gallery_title' => 'required|min:3|max:50'
                ]);
				$edit_gallery->modifyGallery($gallery_id, $request->$gallery_id);
			} else {
                break;
            }
		}
		return response()->json('done');
    }

    public function editImageShowForm(Request $request, Gallery $gallery, Mobile_Detect $mobile_detect)
	{
        $validator = \Validator::make($request->all(), [
            'image_name' => 'required'
        ]);
        if (!$validator->fails()) {
            $array_image = $gallery->getImageInfos($request->image_name);
            $galleries_name = $gallery->getGalleriesArray();

            if ($mobile_detect->isMobile() || $mobile_detect->isTablet()) {
                $result = view(config('site.theme_dir') . config('site.theme') . '/' . 'edit_image_mobile', ['array_image' => $array_image, 'galleries_name' => $galleries_name])->render();
            } else {
                $result = view(config('site.theme_dir') . config('site.theme') . '/' . 'edit_image', ['array_image' => $array_image, 'galleries_name' => $galleries_name])->render();
            }

            return response()->json($result);
        } else {
            return response()->json('error_file');
        }
    }

    public function editImage(Request $request, EditGallery $edit_gallery)
	{
		$validator = \Validator::make($request->all(), [
            'photo_name' => 'required',
            'gallery' => 'required|numeric',
            'change_title' => 'nullable|max:50',
		]);

		if (!$validator->fails()) {
			$timestamp = microtime(true);
			if ($edit_gallery->modifyImage($timestamp, $request->photo_name, $request->gallery, $request->change_title, $request->modify_one_image)) {
				$array = [
					'timestamp' => $timestamp,
					'name' => $request->photo_name,
					'gallery' => $request->gallery,
					'title' => empty($request->change_title) ? '' : $request->change_title
				];
				return $array;
			} else {
                return response()->json('fail');
            }
		} else {
            return response()->json('fail');
        }
	}

    public function deleteImage(Request $request, EditGallery $edit_gallery)
	{
		$validator = \Validator::make($request->all(), [
            'file' => 'required|size:20'
		]);
		if (!$validator->fails()) {
            $edit_gallery->removeImage($request->file);
        } else {
            return response()->json('fail');
        }
    }

    public function deleteGallery(Request $request, EditGallery $edit_gallery)
	{
		$validator = \Validator::make($request->all(), [
            'gallery_id' => 'required|numeric'
		]);
		if (!$validator->fails()) {
            $edit_gallery->removeGallery($request->gallery_id);
        } else {
            return response()->json('fail');
        }
	}
}
