<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerInvite extends Model
{
    /**
     * Назва таблиці, пов’язаної з моделлю.
     *
     * @var string
     */
    protected $table = 'seller_invites';

    /**
     * Поля, які можна масово заповнювати.
     *
     * @var array
     */
    protected $fillable = [
        'link',
        'user_id',
        'status',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_USED = 'used';
    const STATUS_EXPIRED = 'expired';

    const ITEM_PER_PAGE = 10;

    const EXPIRED_AFTER = 3 * 24 * 60 * 60;

    /**
     * Вимкнення автоматичного управління полями created_at і updated_at, якщо не потрібні.
     * У вашому випадку вони є, тому залишаємо true.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Зв’язок "належить до" з моделлю User.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Перевіряє, чи минув термін дії запрошення.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return now()->diffInSeconds($this->created_at) > self::EXPIRED_AFTER;
    }
}
