<?php

namespace App\Models\game;

use Jenssegers\Mongodb\Eloquent\Model;

class TrxListPgsoft extends Model
{
    protected $connection   = 'mongo_trans_db';
    protected $collection   = 'trx_list_pgsoft';

    protected $fillable = [
        "oper_id",
        "ref_id",
        "trx_id",
        "game_name",
        "username",
        "user_api",
        "lvid",
        "prid",
        "pcatid",
        "turnover",
        "vba",
        "payout",
        "wl",
        "commission",
        "progresive",
        "is_win",
        "is_done",
    ];
}
