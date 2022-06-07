<?php

namespace Hyperpay\ConnectIn\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class Transaction extends Model
{
    use HybridRelations;
    protected $connection = 'mysql';

    protected $fillable = [
        'authentication_entityId',
        'amount',
        'currency',
        'brand_uuid',
        'UUID',
        'merchantTransactionId',
        'notificationUrl',
        'shopperResultUrl',
        'status',
        'dueDate',
    ];

    protected $casts = [
        'dueDate' => 'datetime',
    ];

    protected $with = ['mongoLog' , 'merchant' ];


    protected $dates = ['dueDate'];

    private  $statuses = [
        1 => 'Pending',
        2 => 'Paid',
        3 => 'Inactive',
        4 => 'Failed',
        5 => 'Cancelled'
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'authentication_entityId', 'authentication_entityId');
    }

    public function mongoLog()
    {
        return $this->hasOne(MongoLog::class, 'invoice_id', 'invoice_id');
    }

    public function requestTransaction()
    {
        return $this->hasMany(RequestTransaction::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function getStatusAttribute($value)
    {
        return $this->statuses[$value];
    }

}
