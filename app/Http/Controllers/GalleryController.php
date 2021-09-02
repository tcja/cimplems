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

		if ($validator->fails()) {
            return redirect('/');
        }

        return response()->json($edit_gallery->uploadImage($request, $request->image_title, $request->gallery));
    }

    public function createGallery(Request $request, EditGallery $edit_gallery)
	{
        $validator = \Validator::make($request->all(), [
            'gallery_title' => 'required|min:3|max:60'
        ]);

		if ($validator->fails()) {
            return redirect('/');
        }

        return response()->json($edit_gallery->addGallery($request->gallery_title));
    }

    public function editGalleries(Request $request, EditGallery $edit_gallery)
	{
		foreach ($request->all() as $gallery_id => $galleryName) {
			if (is_int($gallery_id)) {
                $validator = \Validator::make($request->all(), [
                    $gallery_id => 'required|min:3|max:60'
                ]);
                $edit_gallery->modifyGallery($gallery_id, $galleryName);
			}
        }

        return response()->json(1);
    }

    public function editImageShowForm(Request $request, Gallery $gallery, Mobile_Detect $mobile_detect)
	{
        $validator = \Validator::make($request->all(), [
            'image_name' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/');
        }

        $array_image = $gallery->getImageInfos($request->image_name);
        $galleries_name = $gallery->getGalleriesArray();

        if ($mobile_detect->isMobile() || $mobile_detect->isTablet()) {
            return response()->json(view(config('site.theme_dir') . config('site.theme') . '/' . 'edit_image_mobile', ['array_image' => $array_image, 'galleries_name' => $galleries_name])->render());
        } else {
            return response()->json(view(config('site.theme_dir') . config('site.theme') . '/' . 'edit_image', ['array_image' => $array_image, 'galleries_name' => $galleries_name])->render());
        }
    }

    public function editImage(Request $request, EditGallery $edit_gallery)
	{
		$validator = \Validator::make($request->all(), [
            'photo_name' => 'required',
            'gallery' => 'required|numeric',
            'change_title' => 'nullable|max:50',
            'page' => 'nullable|numeric',
		]);

		if ($validator->fails()) {
            return redirect('/');
        }

        return response()->json($edit_gallery->modifyImage($request->photo_name, $request->gallery, $request->change_title, $request->modify_one_image, $request->page));
	}

    public function deleteImage(Request $request, EditGallery $edit_gallery)
	{
		$validator = \Validator::make($request->all(), [
            'file' => 'required|size:20',
            'galleryID' => 'required|numeric',
            'page' => 'required|numeric'
        ]);

		if ($validator->fails()) {
            return redirect('/');
        }

        $remove_image = $edit_gallery->removeImage($request->file);

        $gallery = new Gallery($request->galleryID);

        if ($array_images = $gallery->getImagesArray()) {
            $array_images_filtered = $gallery->paginateGalleries($request->page);
        } else {
            $array_images_filtered[0] = 0;
        }

        return [$array_images_filtered[0], $remove_image];
    }

    public function deleteGallery(Request $request, EditGallery $edit_gallery)
	{
		$validator = \Validator::make($request->all(), [
            'gallery_id' => 'required|numeric'
        ]);

		if ($validator->fails()) {
            return redirect('/');
        }

        return response()->json($edit_gallery->removeGallery($request->gallery_id));
    }

    public function changeGalleryPage(Request $request, Gallery $gallery)
	{
		$validator = \Validator::make($request->all(), [
            'galleryID' => 'required|numeric',
            'page' => 'required|numeric'
        ]);

		if ($validator->fails()) {
            return response()->json('fail');
        }

        $gallery = new Gallery($request->galleryID);
        $array_images = $gallery->getImagesArray();

        if ($array_images) {
            $array_images_filtered = $gallery->paginateGalleries($request->page);

            return $array_images_filtered[0];
        }

        return response()->json('fail');
    }
}
