<?php

namespace App\Models\game;

use Jenssegers\Mongodb\Eloquent\Model;

class TrxMutationPgsoft extends Model
{
    protected $connection   = 'mongo_trans_db';
    protected $collection   = 'trx_mutation_pgsoft';

    protected $fillable = [
        "oper_id",
        "ref_id",
        "trx_id",
        "username",
        "description",
        "amount",
        "mut_type",
        "new_bal",
    ];
}
