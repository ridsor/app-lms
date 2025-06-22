<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait ProductTrait
{
    public static function generateSku($product): string
    {
        $categoryPrefix = $product->category
            ? strtoupper(substr($product->category->name, 0, 3))
            : 'GEN';

        $productNamePrefix = strtoupper(substr(Str::slug($product->name), 0, 3));
        $randomComponent = Str::upper(Str::random(3));
        $id = (optional(self::latest()->first())->id ?? 0) + 1;
        $idComponent = Str::padLeft($id, 4, '0');

        return "{$categoryPrefix}-{$productNamePrefix}-{$idComponent}-{$randomComponent}";
        // Contoh: ELC-KOM-0042-XYZ
    }
}
