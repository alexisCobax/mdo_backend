<?php

namespace App\Transformers\Banners;

use App\Models\Banner;
use League\Fractal\TransformerAbstract;

class FindAllTransformer extends TransformerAbstract
{
    public function transform(Banner $banner)
    {
        $banners = [
            'id' => $banner->id,
            'ubicacion' => optional($banner->Ubicaciones)->nombre,
            'tipoUbicacion' => $banner->tipoUbicacion,
            'link' => $banner->link,
            'nombre' => $banner->nombre,
        ];

        return $banners;
    }
}
