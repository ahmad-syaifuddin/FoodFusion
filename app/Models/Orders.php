<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'order_number',
        'total_amount',
        'voucher_discount',
        'voucher_id',
        'qty',
        'payment_method',
        'payment_status',
        'status',
        'notes',
        'payment_proof'
    ];

    // Tambahkan accessor untuk format status yang lebih mudah dibaca
    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Menunggu Konfirmasi Penjual',
            'awaiting payment' => 'Menunggu Pembayaran Dikonfirmasi',
            // 'waiting_payment' => 'Menunggu Pembayaran Dikonfirmasi',
            'payment_rejected' => 'Pembayaran Ditolak',
            'confirmed' => 'Pesanan Dikonfirmasi',
            'processing' => 'Diproses',
            'delivered' => 'Dalam Pengantaran',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ][$this->status] ?? ucfirst($this->status);
    }

    protected $casts = [
        'payment_method' => 'string',
        'payment_status' => 'string',
        'status' => 'string',
        'sub_total' => 'float',
        'coupon' => 'float',
        'total_amount' => 'float',
        'quantity' => 'integer',
    ];


    // Accessors & Mutators untuk menghitung total harga awal dan total diskon
    public function getTotalOriginalPriceAttribute()
    {
        return $this->orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    public function getTotalDiscountAttribute()
    {
        return $this->total_original_price - $this->total_amount;
    }

    // Define status constants for better code readability
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_AWAITING_PAYMENT = 'awaiting payment';
    const STATUS_PAYMENT_REJECTED = 'payment rejected';
    const STATUS_PROCESSING = 'processing';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_PAID = 'paid';
    const PAYMENT_UNPAID = 'unpaid';

    const PAYMENT_METHOD_COD = 'Cash On Delivery';
    const PAYMENT_METHOD_TRANSFER = 'Transfer';
    const PAYMENT_METHOD_MIDTRANS = 'Midtrans';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'order_id');
    }

    // public function shipping()
    // {
    //     return $this->belongsTo(Shipping::class);
    // }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItems::class, 'order_id');
    }

    public function items()
    {
        return $this->orderItems();
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    // Accessors & Mutators
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedSubTotalAttribute()
    {
        return 'Rp ' . number_format($this->sub_total, 0, ',', '.');
    }

    public function getFormattedCouponAttribute()
    {
        return $this->coupon ? 'Rp ' . number_format($this->coupon, 0, ',', '.') : '-';
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_CONFIRMED => 'bg-info',
            self::STATUS_AWAITING_PAYMENT => 'bg-info',
            self::STATUS_PAYMENT_REJECTED => 'bg-danger',
            self::STATUS_PROCESSING => 'bg-info',
            self::STATUS_DELIVERED => 'bg-primary',
            self::STATUS_COMPLETED => 'bg-success',
            self::STATUS_CANCELLED => 'bg-danger',
            default => 'bg-secondary'
        };
    }

     // Tambahkan accessor untuk warna status
         public function getStatusColorAttribute()
    {
        return [
            'pending' => 'secondary',
            'awaiting payment' => 'secondary',
            'payment_rejected' => 'danger',
            'confirmed' => 'info',
            'processing' => 'info',
            'delivered' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger'
        ][$this->status] ?? 'secondary';
    }

    public function getPaymentStatusBadgeAttribute()
    {
        return $this->payment_status === self::PAYMENT_PAID ? 'bg-success' : 'bg-danger';
    }


    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeAwaitingPayment($query)
    {
        return $query->where('status', self::STATUS_AWAITING_PAYMENT);
    }

    public function scopePaymentRejected($query)
    {
        return $query->where('status', self::STATUS_PAYMENT_REJECTED);
    }

    public function scopeProcess($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_UNPAID);
    }

    // Helper Methods
    public function isPaid()
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function isUnpaid()
    {
        return $this->payment_status === self::PAYMENT_UNPAID;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isAwaitingPayment()
    {
        return $this->status === self::STATUS_AWAITING_PAYMENT;
    }

    public function isPaymentRejected()
    {
        return $this->status === self::STATUS_PAYMENT_REJECTED;
    }

    public function isProcessing()
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isDelivered()
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function canBeCancelled()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeProcessed()
    {
        return $this->status === self::STATUS_PENDING && $this->isPaid();
    }

    // Method to update order status
    public function updateStatus($status)
    {
        if (in_array($status, [self::STATUS_PENDING, self::STATUS_PAYMENT_REJECTED, self::STATUS_AWAITING_PAYMENT, self::STATUS_CONFIRMED, self::STATUS_PROCESSING, self::STATUS_DELIVERED, self::STATUS_COMPLETED, self::STATUS_CANCELLED])) {
            $this->update(['status' => $status]);
            return true;
        }
        return false;
    }

    // Method to update payment status
    public function updatePaymentStatus($status)
    {
        if (in_array($status, [self::PAYMENT_PAID, self::PAYMENT_UNPAID])) {
            $this->update(['payment_status' => $status]);
            return true;
        }
        return false;
    }

    // Boot method for any model events
    protected static function boot()
    {
        parent::boot();

        // Generate order number before creating
        static::creating(function ($order) {
            $order->order_number = $order->order_number ?? 'ORD-' . uniqid();
        });
    }
}
