<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_number',
        'user_id',
        'customer_id',
        'package_id',
        'payment_type_id',
        'booking_date',
        'status',
        'payment_status',
        'total_price',
        'customer_name',
        'customer_email',
        'customer_phone',
        'notes',
        'line_items',
        'report_file',
        'coupon_id',
        'coupon_code',
        'subtotal_amount',
        'discount_amount',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'line_items' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public static function normalizeLineItems(?array $items): array
    {
        if (empty($items)) {
            return [];
        }

        return array_values(array_map(function ($item) {
            $id = (string) ($item['id'] ?? '');
            $isCustom = ! empty($item['is_custom'])
                || ! empty($item['isCustom'])
                || str_starts_with($id, 'custom-pkg-');
            $customPackageId = null;

            if ($isCustom && preg_match('/^custom-pkg-(\d+)$/', $id, $matches)) {
                $customPackageId = (int) $matches[1];
            } elseif (! empty($item['custom_package_id'])) {
                $customPackageId = (int) $item['custom_package_id'];
            }

            $type = 'service';

            if ($isCustom) {
                $type = 'custom_package';
            } elseif (str_starts_with($id, 'package-') || ($item['type'] ?? '') === 'package') {
                $type = 'package';
            }

            $normalized = [
                'id' => $id,
                'name' => $item['name'] ?? 'Unknown Item',
                'price' => (int) round($item['price'] ?? 0),
                'type' => $type,
                'category' => $item['category'] ?? null,
                'is_custom' => $isCustom,
            ];

            if ($customPackageId) {
                $normalized['custom_package_id'] = $customPackageId;
            }

            return $normalized;
        }, $items));
    }

    public static function hasCustomPackage(?array $items): bool
    {
        foreach (self::normalizeLineItems($items) as $item) {
            if (! empty($item['is_custom'])) {
                return true;
            }
        }

        return false;
    }

    public static function parseLineItemsFromNotes(?string $notes): array
    {
        if (empty($notes) || ! str_contains($notes, 'Items:')) {
            return [];
        }

        foreach (explode(' | ', $notes) as $part) {
            if (str_starts_with($part, 'Items:')) {
                $json = trim(substr($part, strlen('Items:')));
                $decoded = json_decode($json, true);

                return is_array($decoded) ? self::normalizeLineItems($decoded) : [];
            }
        }

        return [];
    }

    public function lineItemsForDisplay(): array
    {
        if (! empty($this->line_items)) {
            return $this->line_items;
        }

        return self::parseLineItemsFromNotes($this->notes);
    }

    public function lineItemsLabel(): string
    {
        $items = $this->lineItemsForDisplay();

        if (empty($items)) {
            return $this->package?->name ?? 'Lab Test Booking';
        }

        $names = array_filter(array_column($items, 'name'));

        if (count($names) === 1) {
            return $names[0];
        }

        if (count($names) === 2) {
            return implode(' & ', $names);
        }

        return $names[0] . ' +' . (count($names) - 1) . ' more';
    }

    public static function queryForCustomer(Customer $customer)
    {
        return static::query()->where(function ($q) use ($customer) {
            $q->where('customer_id', $customer->id)
                ->orWhere(function ($legacy) use ($customer) {
                    $legacy->whereNull('customer_id')
                        ->where(function ($match) use ($customer) {
                            $match->where('customer_phone', $customer->mobile);
                            if ($customer->email) {
                                $match->orWhere('customer_email', $customer->email);
                            }
                        });
                });
        });
    }
}
