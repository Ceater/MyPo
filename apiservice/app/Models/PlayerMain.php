<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerMain extends Model
{
    protected $connection   = 'mysql-member';
    protected $table        = 'player_main';
    protected $primaryKey   = 'uid';
    protected $fillable     = ['username', 'password'];
    public $incrementing    = true;
    public $timestamps      = true;

    public function player()
    {
        return $this->hasone(PlayerDet::class, 'uid');
    }

    public function playerApi()
    {
        return $this->hasMany(PlayerApi::class, 'uid');
    }

    public function playerAccounting()
    {
        return $this->hasOne(PlayerAccounting::class, 'uid');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'lvid');
    }

    public function requestDeposit()
    {
        return $this->hasMany(RequestDeposit::class, 'uid', 'id');
    }
    public function requestWithdraw()
    {
        return $this->hasMany(RequestWithdraw::class, 'uid', 'id');
    }
}
