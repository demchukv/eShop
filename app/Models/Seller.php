<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Seller extends Model implements HasMedia
{
    use InteractsWithMedia, HasApiTokens, HasFactory, Notifiable;

    protected $table = 'seller_data'; // Виправлено на 'seller_store'

    protected $fillable = [
        'seller_id',
        'user_id',
        'store_id',
        'store_name',
        'store_url',
        'store_description',
        'commission',
        'account_number',
        'account_name',
        'bank_name',
        'bank_code',
        'status',
        'tax_name',
        'tax_number',
        'pan_number',
        'permissions',
        'slug',
        'address_proof',
        'logo',
        'store_thumbnail',
        'category_ids',
        'disk',
        'rating',
        'no_of_ratings',
    ];

    /**
     * Відношення до відгуків про продавця.
     */
    public function ratings()
    {
        return $this->hasMany(SellerRating::class, 'seller_id', 'seller_id');
    }



    public function order_items()
    {
        return $this->hasMany(OrderItems::class, 'id', 'partner_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class)->with('variants');
    }
    public function stores()
    {
        return $this->belongsToMany(Store::class)->withPivot('store_name');;
    }

    public function sellerData()
    {
        return $this->belongsTo(SellerData::class, 'seller_id', 'id');
    }

    public function registerMediaCollections(): void
    {
        $media_storage_settings = fetchDetails('storage_types', ['is_default' => 1], '*');
        $mediaStorageType = isset($media_storage_settings) && !empty($media_storage_settings) ? $media_storage_settings[0]->name : 'public';
        if ($mediaStorageType === 's3') {
            $this->addMediaCollection('sellers')->useDisk('s3');
        } else {
            $this->addMediaCollection('sellers')->useDisk('public');
        }
    }
}
