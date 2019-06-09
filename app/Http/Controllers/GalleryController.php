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
			$upload_path_big = storage_path('app/public/images_gallery/big/');
            $upload_path_min = storage_path('app/public/images_gallery/min/');
			$widen_big_width = 1280;
			$widen_min_width = 300;

			$image_title = preg_replace('#<script[^>]*>([^<]+)</script>#', '$1', $request->image_title);
			$gallery = preg_replace('#<script[^>]*>([^<]+)</script>#', '$1', $request->gallery);

			$array = [
				'gallery' => htmlspecialchars($gallery),
				'title' => empty($image_title) ? '' : htmlspecialchars($image_title)
			];

			if ($request->total_files === 0) {
                return response()->json('erreur, veuillez rééessayer');
            }

            $file = $request->file('image_path');
			for ($i = 0; $i < $request->total_files; $i++) {
				if (in_array($file[$request->image_number_ . $i]->getMimeType(), array('image/gif', 'image/png', 'image/bmp', 'image/jpeg'))) {
					if ($file[$request->image_number_ . $i]->getMimeType() == 'image/gif') {
                        $ext = '.gif';
                    } elseif ($file[$request->image_number_ . $i]->getMimeType() == 'image/png') {
                        $ext = '.png';
                    } else {
                        $ext = '.jpg';
                    }

					$timestamp = microtime(true);
					$new_file_name = substr(sha1(md5('sàéy' . time() . rand(0, 10000) . 'YXdaewS')), 0, 16) . $ext;

					$width = getimagesize($file[$request->image_number_ . $i]->getRealPath());
					if ($width[0] <= $widen_big_width) {
						if ($upload_done = $file[$request->image_number_ . $i]->move($upload_path_big, $new_file_name)) {
                            $edit_gallery->addImage($timestamp, $new_file_name, $gallery, $image_title);
                        } else {
                            return json_encode($upload_done->getMessage());
                        }
					} else {
						\Image::make($file[$request->image_number_ . $i]->getRealPath())->widen($widen_big_width, function ($constraint) { $constraint->upsize(); })->save($upload_path_big . $new_file_name);
						$edit_gallery->addImage($timestamp, $new_file_name, $gallery, $image_title);
					}

					\Image::make($upload_path_big . $new_file_name)->widen($widen_min_width, function ($constraint) { $constraint->upsize(); })->save($upload_path_min . $new_file_name);

					$array['name'][$i][0] = $new_file_name;
					$array['name'][$i][1] = $request->image_number_ . $i;
					$array['name'][$i][2] = strval($timestamp);
				}
			}

			if ($request->file_ajax) {
                return $array;
            } else {
                return redirect('/');
            }
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
			$edit_gallery->addGallery(preg_replace('#script#', '$1', $request->gallery_title));
			$result = 'success';
		} else {
            $result = 'fail';
        }

		return response()->json(htmlspecialchars(preg_replace('#script#', '$1', $request->gallery_title)));
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

        $change_title = preg_replace('#<script[^>]*>([^<]+)</script>#', '$1', $request->change_title);
		$gallery = preg_replace('#<script[^>]*>([^<]+)</script>#', '$1', $request->gallery);

		if (!$validator->fails()) {
			$timestamp = microtime(true);
			if ($edit_gallery->modifyImage($timestamp, $request->photo_name, $gallery, $change_title, $request->modify_one_image)) {
				$array = [
					'timestamp' => $timestamp,
					'name' => $request->photo_name,
					'gallery' => htmlspecialchars($gallery),
					'title' => empty($change_title) ? '' : htmlspecialchars($change_title)
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
