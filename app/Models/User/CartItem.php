<?php

namespace App\Models\User;

use App\Models\Catalog\Product;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'options',
        'option_labels',
        'quantity',
        'price',
        'note',
        'session_key',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'option_labels' => 'array',
            'quantity' => 'integer',
            'price' => 'integer',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Tạo unique key cho cart item
    public static function makeKey(int $productId, array $options = []): string
    {
        return $productId . '_' . md5(json_encode($options));
    }
}
