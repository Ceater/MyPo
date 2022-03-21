<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerApi extends Model
{
    protected $connection   = 'mysql-member';
    protected $table        = 'player_api';
    protected $primaryKey   = 'id';
    protected $guarded     = ['id'];
    public $incrementing    = true;
    public $timestamps      = true;

    public function player()
    {
        return $this->belongsTo(PlayerMain::class, 'uid');
    }
    /*
    public function provider()
    {
        return $this->belongsTo(ProviderMain::class, 'prid');
    }
    */
}
