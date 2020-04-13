<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class Product extends Model
{
    use Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    protected $fillable = ['title', 'slug', 'description', 'img', 'new', 'status', 'price', 'category_id'];

    public function category()
    {
        return $this->hasOne(Category::class, 'id','category_id');
    }

    public static function updateImg($request, $product = null)
    {
        $item = new self();
        if ($product == null) {
            $name = time() . '.' . explode('/', explode(':', substr($request->img, 0, strpos($request->img, ';')))[1])[1];
            Image::make($request->img)->save(public_path('/img/products/') . $name);
            $request->merge(['img' => $name]);
            $item->create([
                'title' => $request['title'],
                'category_id' => $request['category_id'],
                'description' => $request['description'],
                'price' => $request['price'],
                'img' => $request['img'],
                'new' => $request['new'] == true ? 1 : 0,
                'status' => $request['status'] == true ? 1 : 0,
            ]);
            return ['message' => 'created'];
        } else {
            $currentPhoto = $product->img;
            if($request->img != $currentPhoto) {
                $name = time() . '.' . explode('/', explode(':', substr($request->img, 0, strpos($request->img, ';')))[1])[1];

                Image::make($request->img)->save(public_path('/img/products/') . $name);
                $request->merge(['img' => $name]);

                $userPhoto = public_path('img/products/') . $currentPhoto;
                if (file_exists($userPhoto)) {
                    @unlink($userPhoto);
                }
            }
            $product->title = $request['title'];
            $product->category_id = $request['category_id'];
            $product->description = $request['description'];
            $product->price = $request['price'];
            $product->img = $request['img'];
            $product->new = $request['new'] == true ? 1 : 0;
            $product->status = $request['status'] == true ? 1 : 0;
            $product->save();
            return ['message' => 'updated'];
        }

    }
}
