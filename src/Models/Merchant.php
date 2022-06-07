<?php

namespace Hyperpay\ConnectIn\Models;

use Jenssegers\Mongodb\Eloquent\HybridRelations;
use Illuminate\Database\Eloquent\Model;


class Merchant extends Model
{
    use HybridRelations;
    protected $connection = 'mysql';
    protected $table = 'merchants';

    protected $fillable = [
        'name',
        'email',
        'authentication_entityId',
        'access_token',
        'authentication_userId',
        'authentication_password',
        'aci_secret',
        'created_by'
    ];


    public static function boot()
    {
        parent::boot();

        static::creating(function ($merchant) {
            $merchant->created_by = auth()->user()->id ?? 1;
        });
    }

    public function mongoLog()
    {
        return $this->hasMany(MongoLog::class, 'authentication_entityId', 'authentication_entityId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'authentication_entityId', 'authentication_entityId');
    }

}
