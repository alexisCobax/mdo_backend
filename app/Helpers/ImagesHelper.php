<?php

namespace App\Helpers;

class ImagesHelper
{
    /**
     * uploadMultipleImages
     *
     * @param  mixed $request
     * @param  mixed $folder
     * @return void
     */
    public function uploadMultipleImages($request, $folder)
    {
        $uploadedImages = [];

        if ($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $image) {
                if ($image->isValid()) {
                    $uploadedImages[] = $image->store($folder, 'public');
                }
            }
        }

        return $uploadedImages;
    }
}
