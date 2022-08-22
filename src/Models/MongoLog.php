<?php

namespace Hyperpay\ConnectIn\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\HybridRelations;



class MongoLog extends Model
{
    use HybridRelations;

    protected $connection = 'mongodb';

    protected $collection = 'mongo_logs';

    protected $guarded = [];

    protected $primaryKey = 'id';

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'authentication_entityId',  'authentication_entityId');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'id','UUID');
    }


}
