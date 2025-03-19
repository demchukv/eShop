<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $type
 * @property string|null $mobile
 * @property string|null $alternate_mobile
 * @property string|null $address
 * @property string|null $landmark
 * @property int|null $area_id
 * @property int|null $city_id
 * @property string $city
 * @property string $area
 * @property string|null $pincode
 * @property int $system_pincode
 * @property int|null $country_code
 * @property string|null $state
 * @property string|null $country
 * @property string|null $latitude
 * @property string|null $longitude
 * @property int $is_default
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereAlternateMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereLandmark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address wherePincode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereSystemPincode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUserId($value)
 */
	class Address extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $city_id
 * @property int $zipcode_id
 * @property float $minimum_free_delivery_order_amount
 * @property float|null $delivery_charges
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\City|null $city
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\Zipcode|null $zipcode
 * @method static \Illuminate\Database\Eloquent\Builder|Area newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Area newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Area query()
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereDeliveryCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereMinimumFreeDeliveryOrderAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereZipcodeId($value)
 */
	class Area extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property int|null $category_id
 * @property string $name
 * @property string|null $type
 * @property \Illuminate\Support\Carbon $created_at
 * @property int $status
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attribute_values> $attribute_values
 * @property-read int|null $attribute_values_count
 * @property-read \App\Models\Category|null $category
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereUpdatedAt($value)
 */
	class Attribute extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $attribute_id
 * @property int|null $filterable
 * @property string $value
 * @property int|null $swatche_type
 * @property string|null $swatche_value
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Attribute|null $attribute
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute_values newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute_values newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute_values query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute_values whereAttributeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute_values whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute_values whereFilterable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute_values whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute_values whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute_values whereSwatcheType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute_values whereSwatcheValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute_values whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute_values whereValue($value)
 */
	class Attribute_values extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property int|null $category_id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $image
 * @property string|null $slug
 * @property int|null $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Blog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog query()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereUpdatedAt($value)
 */
	class Blog extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property string|null $name
 * @property string|null $slug
 * @property string $image
 * @property string|null $banner
 * @property int|null $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereUpdatedAt($value)
 */
	class BlogCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property string|null $name
 * @property string|null $slug
 * @property string $image
 * @property int|null $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Brand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Brand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Brand query()
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereUpdatedAt($value)
 */
	class Brand extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $store_id
 * @property int $product_variant_id
 * @property int $qty
 * @property int $is_saved_for_later
 * @property string $product_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Product_variants|null $productVariant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereIsSavedForLater($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereProductVariantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUserId($value)
 */
	class Cart extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property string $name
 * @property int|null $parent_id
 * @property string $slug
 * @property string $image
 * @property string $banner
 * @property string|null $style
 * @property int|null $row_order
 * @property int|null $status
 * @property int $clicks
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereRowOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $category_ids
 * @property int|null $store_id
 * @property string|null $style
 * @property int|null $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $banner_image
 * @property string|null $background_color
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|CategorySliders newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategorySliders newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategorySliders query()
 * @method static \Illuminate\Database\Eloquent\Builder|CategorySliders whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategorySliders whereBannerImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategorySliders whereCategoryIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategorySliders whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategorySliders whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategorySliders whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategorySliders whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategorySliders whereStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategorySliders whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategorySliders whereUpdatedAt($value)
 */
	class CategorySliders extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property int $user_id
 * @property int $favorite_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ChFavorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChFavorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChFavorite query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChFavorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChFavorite whereFavoriteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChFavorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChFavorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChFavorite whereUserId($value)
 */
	class ChFavorite extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property int $from_id
 * @property int $to_id
 * @property string|null $body
 * @property string|null $attachment
 * @property int $seen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ChMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChMessage whereAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChMessage whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChMessage whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChMessage whereSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChMessage whereToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChMessage whereUpdatedAt($value)
 */
	class ChMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $status
 * @property string|null $passport
 * @property string|null $tax_id
 * @property array|null $photos
 * @property string|null $message
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeUserStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeUserStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeUserStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeUserStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeUserStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeUserStatus whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeUserStatus whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeUserStatus wherePassport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeUserStatus wherePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeUserStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeUserStatus whereTaxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeUserStatus whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeUserStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeUserStatus whereUserId($value)
 */
	class ChangeUserStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property float $minimum_free_delivery_order_amount
 * @property float $delivery_charges
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereDeliveryCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereMinimumFreeDeliveryOrderAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereUpdatedAt($value)
 */
	class City extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property string|null $title
 * @property string|null $slug
 * @property string|null $short_description
 * @property string|null $description
 * @property string|null $image
 * @property int|null $seller_id
 * @property string|null $product_type
 * @property string|null $product_ids
 * @property string|null $product_variant_ids
 * @property float|null $price
 * @property float|null $special_price
 * @property string|null $attribute
 * @property string|null $attribute_value_ids
 * @property int|null $deliverable_type  (0:none, 1:all, 2:include, 3:exclude)
 * @property string|null $deliverable_zipcodes
 * @property int $city_deliverable_type  (0:none, 1:all, 2:include, 3:exclude)
 * @property string $deliverable_cities
 * @property string|null $deliverable_zones
 * @property string|null $pickup_location
 * @property string|null $other_images
 * @property string|null $tax
 * @property string|null $tags
 * @property int|null $selected_products
 * @property string|null $sku
 * @property string|null $stock
 * @property int|null $availability
 * @property int|null $cod_allowed
 * @property int $download_allowed
 * @property string|null $download_type
 * @property string|null $download_link
 * @property int|null $is_prices_inclusive_tax
 * @property int|null $is_returnable
 * @property int|null $is_cancelable
 * @property string|null $cancelable_till
 * @property int $is_attachment_required
 * @property string|null $weight
 * @property string|null $height
 * @property string|null $length
 * @property string|null $breadth
 * @property int|null $total_allowed_quantity
 * @property int|null $minimum_order_quantity
 * @property int|null $quantity_step_size
 * @property int|null $has_similar_product
 * @property string|null $similar_product_ids
 * @property int $status
 * @property int $minimum_free_delivery_order_qty
 * @property string|null $delivery_charges
 * @property float $rating
 * @property int $no_of_ratings
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereAttribute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereAttributeValueIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereAvailability($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereBreadth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereCancelableTill($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereCityDeliverableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereCodAllowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereDeliverableCities($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereDeliverableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereDeliverableZipcodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereDeliverableZones($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereDeliveryCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereDownloadAllowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereDownloadLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereDownloadType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereHasSimilarProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereIsAttachmentRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereIsCancelable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereIsPricesInclusiveTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereIsReturnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereMinimumFreeDeliveryOrderQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereMinimumOrderQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereNoOfRatings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereOtherImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct wherePickupLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereProductIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereProductVariantIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereQuantityStepSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereSelectedProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereSimilarProductIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereSpecialPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereTotalAllowedQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProduct whereWeight($value)
 */
	class ComboProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property string|null $name
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ComboProductAttributeValue> $attribute_values
 * @property-read int|null $attribute_values_count
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttribute query()
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttribute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttribute whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttribute whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttribute whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttribute whereUpdatedAt($value)
 */
	class ComboProductAttribute extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property int|null $combo_product_attribute_id
 * @property string|null $value
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\ComboProductAttribute|null $attribute
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttributeValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttributeValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttributeValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttributeValue whereComboProductAttributeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttributeValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttributeValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttributeValue whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttributeValue whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttributeValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductAttributeValue whereValue($value)
 */
	class ComboProductAttributeValue extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $seller_id
 * @property int|null $product_id
 * @property int $votes
 * @property string|null $question
 * @property string|null $answer
 * @property int $answered_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductFaq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductFaq newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductFaq query()
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductFaq whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductFaq whereAnsweredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductFaq whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductFaq whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductFaq whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductFaq whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductFaq whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductFaq whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductFaq whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductFaq whereVotes($value)
 */
	class ComboProductFaq extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $product_id
 * @property int $rating
 * @property string|null $images
 * @property string|null $title
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\ComboProduct|null $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductRating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductRating query()
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductRating whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductRating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductRating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductRating whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductRating whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductRating whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductRating whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductRating whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComboProductRating whereUserId($value)
 */
	class ComboProductRating extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $code
 * @property string|null $symbol
 * @property string|null $exchange_rate
 * @property int $is_default
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Currency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency query()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereExchangeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereUpdatedAt($value)
 */
	class Currency extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $message
 * @property string|null $type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|CustomMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomMessage whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomMessage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomMessage whereUpdatedAt($value)
 */
	class CustomMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $role_id
 * @property string|null $ip_address
 * @property string $username
 * @property string $password
 * @property string|null $email
 * @property string|null $mobile
 * @property string|null $image
 * @property string $disk
 * @property float|null $balance
 * @property string|null $activation_selector
 * @property string|null $activation_code
 * @property string|null $forgotten_password_selector
 * @property string|null $forgotten_password_code
 * @property int|null $forgotten_password_time
 * @property string|null $remember_selector
 * @property string|null $remember_token
 * @property int|null $created_on
 * @property int|null $last_login
 * @property int|null $active
 * @property string|null $company
 * @property string|null $address
 * @property string|null $bonus_type
 * @property int|null $bonus
 * @property float $cash_received
 * @property string|null $dob
 * @property int|null $country_code
 * @property string|null $city
 * @property string|null $area
 * @property string|null $street
 * @property string|null $pincode
 * @property string|null $serviceable_zipcodes
 * @property string $serviceable_cities
 * @property string|null $serviceable_zones
 * @property string|null $apikey
 * @property string|null $referral_code
 * @property string|null $friends_code
 * @property string|null $fcm_id
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string $type
 * @property string|null $front_licence_image
 * @property string|null $back_licence_image
 * @property int $status
 * @property int $is_notification_on
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $active_status
 * @property string $avatar
 * @property int $dark_mode
 * @property string|null $messenger_color
 * @property string $first_name
 * @property string $last_name
 * @property string $telegram_id
 * @property string $telegram_username
 * @property string|null $birthdate
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy query()
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereActivationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereActivationSelector($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereActiveStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereApikey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereBackLicenceImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereBonusType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereCashReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereCreatedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereDarkMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereFcmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereForgottenPasswordCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereForgottenPasswordSelector($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereForgottenPasswordTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereFriendsCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereFrontLicenceImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereIsNotificationOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereMessengerColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy wherePincode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereReferralCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereRememberSelector($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereServiceableCities($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereServiceableZipcodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereServiceableZones($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereTelegramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereTelegramUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deliveryboy whereUsername($value)
 */
	class Deliveryboy extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $order_id
 * @property int|null $order_item_id
 * @property string|null $subject
 * @property string|null $message
 * @property string|null $file_url
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalOrdersMail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalOrdersMail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalOrdersMail query()
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalOrdersMail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalOrdersMail whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalOrdersMail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalOrdersMail whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalOrdersMail whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalOrdersMail whereOrderItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalOrdersMail whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DigitalOrdersMail whereUpdatedAt($value)
 */
	class DigitalOrdersMail extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $question
 * @property string|null $answer
 * @property string|null $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Faq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq query()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereUpdatedAt($value)
 */
	class Faq extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $product_id
 * @property int|null $seller_id
 * @property string|null $product_type
 * @property int $is_seller
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite query()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereIsSeller($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereUserId($value)
 */
	class Favorite extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $delivery_boy_id
 * @property float $opening_balance
 * @property float $closing_balance
 * @property float $amount
 * @property string|null $status
 * @property string|null $message
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FundTransfer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FundTransfer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FundTransfer query()
 * @method static \Illuminate\Database\Eloquent\Builder|FundTransfer whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundTransfer whereClosingBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundTransfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundTransfer whereDeliveryBoyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundTransfer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundTransfer whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundTransfer whereOpeningBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundTransfer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundTransfer whereUpdatedAt($value)
 */
	class FundTransfer extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $uuid
 * @property int $seller_id
 * @property string|null $name
 * @property string|null $file_name
 * @property string|null $disk
 * @property string|null $disk_name
 * @property string|null $conversions_disk
 * @property string|null $collection_name
 * @property string|null $extension
 * @property string|null $mime_type
 * @property string|null $custom_properties
 * @property string|null $size
 * @property string|null $generated_conversions
 * @property string|null $responsive_images
 * @property string|null $manipulations
 * @property int|null $order_column
 * @property string|null $model_type
 * @property int|null $model_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $_token
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCollectionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereConversionsDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCustomProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereDiskName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereGeneratedConversions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereManipulations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereOrderColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereResponsiveImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereUuid($value)
 */
	class Image extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $language
 * @property string|null $code
 * @property int $is_rtl
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereIsRtl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereUpdatedAt($value)
 */
	class Language extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property int $seller_id
 * @property string $name
 * @property string $extension
 * @property string $type
 * @property string $sub_directory
 * @property string $size
 * @property int|null $order_column
 * @property string $model_type
 * @property int|null $model_id
 * @property string $file_name
 * @property string $disk
 * @property string $conversions_disk
 * @property string $collection_name
 * @property string $mime_type
 * @property string $custom_properties
 * @property string $generated_conversions
 * @property string $responsive_images
 * @property string $manipulations
 * @property int|null $uuid
 * @property string|null $object_url
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Media query()
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereCollectionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereConversionsDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereCustomProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereGeneratedConversions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereManipulations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereObjectUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereOrderColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereResponsiveImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereSubDirectory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUuid($value)
 */
	class Media extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property string $title
 * @property string $message
 * @property string $type
 * @property string $type_id
 * @property string|null $send_to
 * @property string|null $users_id
 * @property string|null $image
 * @property string|null $link
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereSendTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUsersId($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property string|null $title
 * @property string|null $type
 * @property int|null $type_id
 * @property string $link
 * @property string $image
 * @property string|null $banner_image
 * @property int|null $min_discount
 * @property int|null $max_discount
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Offer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Offer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Offer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereBannerImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereMaxDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereMinDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereUpdatedAt($value)
 */
	class Offer extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property string|null $title
 * @property string|null $banner_image
 * @property string|null $offer_ids
 * @property int|null $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|OfferSliders newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfferSliders newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfferSliders query()
 * @method static \Illuminate\Database\Eloquent\Builder|OfferSliders whereBannerImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferSliders whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferSliders whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferSliders whereOfferIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferSliders whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferSliders whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferSliders whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferSliders whereUpdatedAt($value)
 */
	class OfferSliders extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $store_id
 * @property int|null $address_id
 * @property string $mobile
 * @property float $total
 * @property float|null $delivery_charge
 * @property int $is_delivery_charge_returnable
 * @property float|null $wallet_balance
 * @property string|null $promo_code_id
 * @property float|null $promo_discount
 * @property float|null $discount
 * @property float|null $total_payable
 * @property float|null $final_total
 * @property string $payment_method
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $address
 * @property string|null $delivery_time
 * @property string|null $delivery_date
 * @property int|null $otp
 * @property string|null $email
 * @property string|null $notes
 * @property int $is_pos_order
 * @property int $is_shiprocket_order
 * @property int $is_cod_collected
 * @property string|null $type
 * @property int $order_payment_currency_id
 * @property string $order_payment_currency_code
 * @property string $base_currency_code The base currency used in the system when placing orders.
 * @property float $order_payment_currency_conversion_rate
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderBankTransfers> $orderBankTransfers
 * @property-read int|null $order_bank_transfers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderCharges> $orderCharges
 * @property-read int|null $order_charges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItems> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBaseCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeliveryCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeliveryTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFinalTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIsCodCollected($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIsDeliveryChargeReturnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIsPosOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereIsShiprocketOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderPaymentCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderPaymentCurrencyConversionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderPaymentCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePromoCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePromoDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTotalPayable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereWalletBalance($value)
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $order_id
 * @property string|null $attachments
 * @property string $disk
 * @property int|null $status (0:pending|1:rejected|2:accepted)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Order|null $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|OrderBankTransfers newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderBankTransfers newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderBankTransfers query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderBankTransfers whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderBankTransfers whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderBankTransfers whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderBankTransfers whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderBankTransfers whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderBankTransfers whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderBankTransfers whereUpdatedAt($value)
 */
	class OrderBankTransfers extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $seller_id
 * @property string $product_variant_ids
 * @property int $order_id
 * @property string $order_item_ids
 * @property float|null $delivery_charge
 * @property string|null $promo_code_id
 * @property float|null $promo_discount
 * @property float|null $sub_total
 * @property float|null $total
 * @property int $otp
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Order|null $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges whereDeliveryCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges whereOrderItemIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges whereOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges whereProductVariantIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges wherePromoCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges wherePromoDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderCharges whereUpdatedAt($value)
 */
	class OrderCharges extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $store_id
 * @property int $order_id
 * @property int|null $delivery_boy_id
 * @property int $seller_id
 * @property int $is_credited
 * @property int $otp
 * @property string|null $product_name
 * @property string|null $variant_name
 * @property int $product_variant_id
 * @property int $quantity
 * @property int $delivered_quantity
 * @property float $price
 * @property float|null $discounted_price
 * @property string|null $tax_ids
 * @property float|null $tax_percent
 * @property float|null $tax_amount
 * @property float|null $discount
 * @property float $sub_total
 * @property string|null $deliver_by
 * @property int|null $updated_by
 * @property string $status
 * @property float $admin_commission_amount
 * @property float $seller_commission_amount
 * @property string|null $active_status
 * @property string|null $hash_link
 * @property int|null $is_sent
 * @property string $order_type
 * @property string|null $attachment
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Order|null $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereActiveStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereAdminCommissionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereDeliverBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereDeliveredQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereDeliveryBoyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereDiscountedPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereHashLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereIsCredited($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereIsSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereOrderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereProductVariantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereSellerCommissionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereTaxIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereTaxPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItems whereVariantName($value)
 */
	class OrderItems extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $order_id
 * @property int $shiprocket_order_id
 * @property int $shipment_id
 * @property int $courier_company_id
 * @property string $awb_code
 * @property int $pickup_status
 * @property string $pickup_scheduled_date
 * @property string $pickup_token_number
 * @property int $status
 * @property string $others
 * @property string $pickup_generated_date
 * @property string $data
 * @property string $date
 * @property int $is_canceled
 * @property string $manifest_url
 * @property string $label_url
 * @property string $invoice_url
 * @property string|null $order_item_id
 * @property string|null $courier_agency
 * @property string $tracking_id
 * @property int|null $parcel_id
 * @property string $url
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereAwbCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereCourierAgency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereCourierCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereInvoiceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereIsCanceled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereLabelUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereManifestUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereOrderItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereOthers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereParcelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking wherePickupGeneratedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking wherePickupScheduledDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking wherePickupStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking wherePickupTokenNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereShipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereShiprocketOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereTrackingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTracking whereUrl($value)
 */
	class OrderTracking extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $mobile
 * @property string $otp
 * @property int $varified 1 : verify | 0: not verify
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|Otps newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Otps newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Otps query()
 * @method static \Illuminate\Database\Eloquent\Builder|Otps whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otps whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otps whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otps whereOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otps whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otps whereVarified($value)
 */
	class Otps extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property int $order_id
 * @property int|null $delivery_boy_id
 * @property string $name
 * @property string|null $type
 * @property string $status
 * @property string $active_status
 * @property int $otp
 * @property float $delivery_charge
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Parcel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Parcel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Parcel query()
 * @method static \Illuminate\Database\Eloquent\Builder|Parcel whereActiveStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcel whereDeliveryBoyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcel whereDeliveryCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcel whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcel whereOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcel whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcel whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcel whereUpdatedAt($value)
 */
	class Parcel extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property int $parcel_id
 * @property int $order_item_id
 * @property int $product_variant_id
 * @property float $unit_price
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Parcelitem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Parcelitem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Parcelitem query()
 * @method static \Illuminate\Database\Eloquent\Builder|Parcelitem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcelitem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcelitem whereOrderItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcelitem whereParcelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcelitem whereProductVariantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcelitem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcelitem whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcelitem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parcelitem whereUpdatedAt($value)
 */
	class Parcelitem extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $payment_type
 * @property string $payment_address
 * @property string|null $amount_requested
 * @property string|null $remarks
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentRequest whereAmountRequested($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentRequest wherePaymentAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentRequest wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentRequest whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentRequest whereUserId($value)
 */
	class PaymentRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $seller_id
 * @property string $pickup_location
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string|null $address2
 * @property string $city
 * @property string $state
 * @property string $country
 * @property string $pincode
 * @property string|null $latitude
 * @property string|null $longitude
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation query()
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation wherePickupLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation wherePincode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickupLocation whereUpdatedAt($value)
 */
	class PickupLocation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property string|null $product_identity
 * @property int $category_id
 * @property int|null $seller_id
 * @property \App\Models\Tax|null $tax
 * @property int|null $row_order
 * @property string|null $type
 * @property string|null $stock_type 0 =>'Simple_Product_Stock_Active' 1 => "Product_Level" 2 => "Variable_Level"
 * @property string $name
 * @property string|null $short_description
 * @property string $slug
 * @property int|null $indicator 0 - none | 1 - veg | 2 - non-veg
 * @property int $cod_allowed
 * @property int $download_allowed
 * @property string|null $download_type
 * @property string|null $download_link
 * @property int $minimum_order_quantity
 * @property int $quantity_step_size
 * @property int|null $total_allowed_quantity
 * @property int $is_prices_inclusive_tax
 * @property int|null $is_returnable
 * @property int|null $is_cancelable
 * @property string|null $cancelable_till
 * @property int $is_attachment_required
 * @property string $image
 * @property string|null $other_images
 * @property string|null $video_type
 * @property string|null $video
 * @property string|null $tags
 * @property string|null $warranty_period
 * @property string|null $guarantee_period
 * @property string|null $made_in
 * @property string|null $hsn_code
 * @property string|null $brand
 * @property string|null $sku
 * @property int|null $stock
 * @property int|null $availability
 * @property float|null $rating
 * @property int|null $no_of_ratings
 * @property string|null $description
 * @property string $extra_description
 * @property int $deliverable_type (0:none, 1:all, 2:include, 3:exclude)
 * @property string|null $deliverable_zipcodes
 * @property int $city_deliverable_type 	(0:none, 1:all, 2:include, 3:exclude)
 * @property string $deliverable_cities
 * @property string|null $deliverable_zones
 * @property string|null $pickup_location
 * @property int|null $status
 * @property int $minimum_free_delivery_order_qty
 * @property float $delivery_charges
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductApprovalComment> $approvalComments
 * @property-read int|null $approval_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductApproval> $approvals
 * @property-read int|null $approvals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product_variants> $category
 * @property-read int|null $category_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product_attributes> $productAttributes
 * @property-read int|null $product_attributes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product_variants> $productVariants
 * @property-read int|null $product_variants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductRating> $ratings
 * @property-read int|null $ratings_count
 * @property-read \App\Models\Seller|null $seller
 * @property-read \App\Models\Seller|null $sellerData
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAvailability($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCancelableTill($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCityDeliverableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCodAllowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeliverableCities($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeliverableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeliverableZipcodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeliverableZones($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeliveryCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDownloadAllowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDownloadLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDownloadType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereExtraDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereGuaranteePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereHsnCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIndicator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsAttachmentRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsCancelable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsPricesInclusiveTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsReturnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMadeIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMinimumFreeDeliveryOrderQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMinimumOrderQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereNoOfRatings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereOtherImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePickupLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductIdentity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereQuantityStepSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereRowOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereStockType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTotalAllowedQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereVideoType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereWarrantyPeriod($value)
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property int $manager_id
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $status
 * @property-read \App\Models\User|null $manager
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApproval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApproval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApproval query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApproval whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApproval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApproval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApproval whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApproval whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApproval whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApproval whereUpdatedAt($value)
 */
	class ProductApproval extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property int $manager_id
 * @property string $comment
 * @property array|null $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $manager
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApprovalComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApprovalComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApprovalComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApprovalComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApprovalComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApprovalComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApprovalComment whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApprovalComment whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApprovalComment whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductApprovalComment whereUpdatedAt($value)
 */
	class ProductApprovalComment extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $seller_id
 * @property int|null $product_id
 * @property int $votes
 * @property string|null $question
 * @property string|null $answer
 * @property int $answered_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|ProductFaq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductFaq newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductFaq query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductFaq whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductFaq whereAnsweredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductFaq whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductFaq whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductFaq whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductFaq whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductFaq whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductFaq whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductFaq whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductFaq whereVotes($value)
 */
	class ProductFaq extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property float $rating
 * @property string|null $images
 * @property string|null $title
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Product_variants|null $productVariant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereUserId($value)
 */
	class ProductRating extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property string $attribute_value_ids
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Product|null $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product_attributes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product_attributes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product_attributes query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product_attributes whereAttributeValueIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_attributes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_attributes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_attributes whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_attributes whereUpdatedAt($value)
 */
	class Product_attributes extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property string|null $attribute_value_ids
 * @property string|null $attribute_set
 * @property float $price
 * @property float|null $special_price
 * @property float $dealerprice
 * @property string|null $sku
 * @property int|null $stock
 * @property float $weight
 * @property float $height
 * @property float $breadth
 * @property float $length
 * @property string|null $images
 * @property int|null $availability
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Attribute|null $attribute
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attribute> $attributes
 * @property-read int|null $attributes_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Product|null $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductRating> $ratings
 * @property-read int|null $ratings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereAttributeSet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereAttributeValueIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereAvailability($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereBreadth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereDealerprice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereSpecialPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product_variants whereWeight($value)
 */
	class Product_variants extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property string|null $title
 * @property string $promo_code
 * @property string|null $message
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $no_of_users
 * @property float|null $minimum_order_amount
 * @property float|null $discount
 * @property string|null $discount_type
 * @property float|null $max_discount_amount
 * @property int $repeat_usage
 * @property int|null $no_of_repeat_usage
 * @property string|null $image
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int|null $is_cashback
 * @property int|null $list_promocode
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereIsCashback($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereListPromocode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereMaxDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereMinimumOrderAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereNoOfRepeatUsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereNoOfUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode wherePromoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereRepeatUsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereUpdatedAt($value)
 */
	class PromoCode extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $code
 * @property int $product_id
 * @property int $dealer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $dealer
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCode whereDealerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCode whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCode whereUpdatedAt($value)
 */
	class ReferralCode extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property int $product_variant_id
 * @property int $order_id
 * @property int $order_item_id
 * @property int $status
 * @property string|null $remarks
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|ReturnRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReturnRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReturnRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReturnRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReturnRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReturnRequest whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReturnRequest whereOrderItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReturnRequest whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReturnRequest whereProductVariantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReturnRequest whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReturnRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReturnRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReturnRequest whereUserId($value)
 */
	class ReturnRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property string $title
 * @property string|null $short_description
 * @property string $style
 * @property string|null $header_style
 * @property string|null $product_ids
 * @property int $row_order
 * @property string|null $categories
 * @property string|null $product_type
 * @property string|null $banner_image
 * @property string|null $background_color
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereBannerImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCategories($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereHeaderStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereProductIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereRowOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereUpdatedAt($value)
 */
	class Section extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $national_identity_card
 * @property string|null $authorized_signature
 * @property string $disk
 * @property string|null $pan_number
 * @property int $status approved: 1 | not-approved: 2 | deactive:0 | removed :7
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItems> $order_items
 * @property-read int|null $order_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Store> $stores
 * @property-read int|null $stores_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Seller newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Seller newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Seller query()
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereAuthorizedSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereNationalIdentityCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller wherePanNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereUserId($value)
 */
	class Seller extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $seller_id
 * @property int $store_id
 * @property int $category_id
 * @property float $commission
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|SellerCommission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SellerCommission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SellerCommission query()
 * @method static \Illuminate\Database\Eloquent\Builder|SellerCommission whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerCommission whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerCommission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerCommission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerCommission whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerCommission whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerCommission whereUpdatedAt($value)
 */
	class SellerCommission extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $link
 * @property int $user_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|SellerInvite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SellerInvite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SellerInvite query()
 * @method static \Illuminate\Database\Eloquent\Builder|SellerInvite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerInvite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerInvite whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerInvite whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerInvite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerInvite whereUserId($value)
 */
	class SellerInvite extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $variable
 * @property string|null $value
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereVariable($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $store_id
 * @property string $type
 * @property int|null $type_id
 * @property string|null $link
 * @property string $image
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Slider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider query()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereUpdatedAt($value)
 */
	class Slider extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $name
 * @property int $is_default
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|StorageType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StorageType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StorageType query()
 * @method static \Illuminate\Database\Eloquent\Builder|StorageType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageType whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageType whereUpdatedAt($value)
 */
	class StorageType extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $image
 * @property string|null $banner_image
 * @property string|null $banner_image_for_most_selling_product
 * @property string|null $stack_image
 * @property string|null $login_image
 * @property string|null $half_store_logo
 * @property string $disk
 * @property int $is_single_seller_order_system
 * @property int|null $is_default_store
 * @property string|null $note_for_necessary_documents
 * @property string|null $primary_color
 * @property string|null $secondary_color
 * @property string|null $store_settings
 * @property string|null $hover_color
 * @property string|null $active_color
 * @property string $background_color
 * @property int|null $status
 * @property float $rating
 * @property int $no_of_ratings
 * @property string $delivery_charge_type
 * @property int $delivery_charge_amount
 * @property int $minimum_free_delivery_amount
 * @property string $product_deliverability_type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Seller> $sellers
 * @property-read int|null $sellers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Store newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Store newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Store query()
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereActiveColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereBannerImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereBannerImageForMostSellingProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereDeliveryChargeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereDeliveryChargeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereHalfStoreLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereHoverColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereIsDefaultStore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereIsSingleSellerOrderSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereLoginImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereMinimumFreeDeliveryAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereNoOfRatings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereNoteForNecessaryDocuments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereProductDeliverabilityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereStackImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereStoreSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Store whereUpdatedAt($value)
 */
	class Store extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $title
 * @property string $percentage
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Tax newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tax newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tax query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereUpdatedAt($value)
 */
	class Tax extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $ticket_type_id
 * @property int|null $user_id
 * @property string|null $subject
 * @property string|null $email
 * @property string|null $description
 * @property int|null $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereTicketTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereUserId($value)
 */
	class Ticket extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $user_type
 * @property int|null $user_id
 * @property int|null $ticket_id
 * @property string|null $message
 * @property string|null $attachments
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|TicketMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketMessage whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketMessage whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketMessage whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketMessage whereUserType($value)
 */
	class TicketMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $title
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|TicketType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketType query()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketType whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketType whereUpdatedAt($value)
 */
	class TicketType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $from_time
 * @property string $to_time
 * @property string $last_order_time
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|TimeSlot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeSlot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeSlot query()
 * @method static \Illuminate\Database\Eloquent\Builder|TimeSlot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeSlot whereFromTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeSlot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeSlot whereLastOrderTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeSlot whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeSlot whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeSlot whereToTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimeSlot whereUpdatedAt($value)
 */
	class TimeSlot extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $transaction_type
 * @property int $user_id
 * @property string|null $order_id
 * @property int|null $order_item_id
 * @property string|null $type
 * @property string|null $txn_id
 * @property string|null $payu_txn_id
 * @property float $amount
 * @property string|null $status
 * @property string|null $currency_code
 * @property string|null $payer_email
 * @property string $message
 * @property string|null $transaction_date
 * @property int|null $is_refund
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereIsRefund($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereOrderItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePayerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePayuTxnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereTxnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUserId($value)
 */
	class Transaction extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $version
 * @property string $created_at
 * @property string $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Updates latestById()
 * @method static \Illuminate\Database\Eloquent\Builder|Updates newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Updates newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Updates query()
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Updates whereVersion($value)
 */
	class Updates extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $role_id
 * @property string|null $ip_address
 * @property string $username
 * @property mixed $password
 * @property string|null $email
 * @property string|null $mobile
 * @property string|null $image
 * @property string $disk
 * @property float|null $balance
 * @property string|null $activation_selector
 * @property string|null $activation_code
 * @property string|null $forgotten_password_selector
 * @property string|null $forgotten_password_code
 * @property int|null $forgotten_password_time
 * @property string|null $remember_selector
 * @property string|null $remember_token
 * @property int|null $created_on
 * @property int|null $last_login
 * @property int|null $active
 * @property string|null $company
 * @property string|null $address
 * @property string|null $bonus_type
 * @property int|null $bonus
 * @property float $cash_received
 * @property string|null $dob
 * @property int|null $country_code
 * @property string|null $city
 * @property string|null $area
 * @property string|null $street
 * @property string|null $pincode
 * @property string|null $serviceable_zipcodes
 * @property string $serviceable_cities
 * @property string|null $serviceable_zones
 * @property string|null $apikey
 * @property string|null $referral_code
 * @property string|null $friends_code
 * @property string|null $fcm_id
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string $type
 * @property string|null $front_licence_image
 * @property string|null $back_licence_image
 * @property int $status
 * @property int $is_notification_on
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $active_status
 * @property string $avatar
 * @property int $dark_mode
 * @property string|null $messenger_color
 * @property string $first_name
 * @property string $last_name
 * @property string $telegram_id
 * @property string $telegram_username
 * @property string|null $birthdate
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $referrals
 * @property-read int|null $referrals_count
 * @property-read \App\Models\Role|null $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Seller|null $seller_data
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActivationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActivationSelector($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActiveStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereApikey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBackLicenceImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBonusType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCashReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDarkMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFcmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereForgottenPasswordCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereForgottenPasswordSelector($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereForgottenPasswordTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFriendsCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFrontLicenceImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsNotificationOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMessengerColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePincode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereReferralCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberSelector($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereServiceableCities($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereServiceableZipcodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereServiceableZones($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTelegramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTelegramUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 */
	class User extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $fcm_id
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|UserFcm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFcm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFcm query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFcm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFcm whereFcmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFcm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFcm whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFcm whereUserId($value)
 */
	class UserFcm extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $status
 * @property string|null $passport
 * @property string|null $tax_id
 * @property string|null $photos
 * @property string|null $message
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus wherePassport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus wherePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus whereTaxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatus whereUserId($value)
 */
	class UserStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $zipcode
 * @property int $city_id
 * @property float $minimum_free_delivery_order_amount
 * @property float|null $delivery_charges
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Zipcode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Zipcode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Zipcode query()
 * @method static \Illuminate\Database\Eloquent\Builder|Zipcode whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zipcode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zipcode whereDeliveryCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zipcode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zipcode whereMinimumFreeDeliveryOrderAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zipcode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zipcode whereZipcode($value)
 */
	class Zipcode extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $serviceable_city_ids
 * @property string|null $serviceable_zipcode_ids
 * @property int|null $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Zone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Zone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Zone query()
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereServiceableCityIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereServiceableZipcodeIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereUpdatedAt($value)
 */
	class Zone extends \Eloquent {}
}

