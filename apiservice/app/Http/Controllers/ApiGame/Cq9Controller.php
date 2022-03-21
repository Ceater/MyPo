<?php

namespace App\Http\Controllers\ApiGame;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Cq9Controller extends Controller
{
    public $currency = "IDR";
    public $dummyplayer = ['a', 'b', 'c'];
    /*
    status code
        ※amount must not be negative, if so, please return error code “1003”
        ※mtcode is unique, if duplicate please return error code “2009”
        ※if eventTime format error occur, please return error code “1004”
    */
    public function bet(Request $req){
        $account    = $req['account'];
        $date_i     = $req['eventTime'];
        $g_provider = $req['gamehall'];
        $g_code     = $req['gamecode'];
        $round_id   = $req['roundid'];
        $amount     = $req['amount'];
        $mtcode     = $req['mtcode'];
        $session    = $req['session'];
        $platform   = $req['platform'];

        if($amount < 0){
            $code   = 1003;
        }

        return $this->FinalizeRequest($code);
    }

    public function checkPlayer(Request $req, $account){
        $playerExist = false; // true / false
        if(in_array($account, $this->dummyplayer)){
            $playerExist = true;
        }
        $response_format = [
            "data"  => $playerExist,
            "status"    => [
                "code"      => '0',
                "message"   => 'Success',
                "datetime"  => date("c"),
            ]
        ];
        return response()->json($response_format);
    }

    public function checkBalance(Request $req, $account){
        return $this->FinalizeRequest('0', $account);
    }

    public function endround(Request $req){
        $account    = $req['account'];
        $date_i     = $req['eventTime'];
        $g_provider = $req['gamehall'];
        $g_code     = $req['gamecode'];
        $round_id   = $req['roundid'];
        $amount     = $req['amount'];
        $mtcode     = $req['mtcode'];
        $data       = $req['data'];
        $validbet   = $req['validbet'];
        $createtime = $req['createTime'];
        $freegame   = $req['freegame'];
        $jackpot    = $req['jackpot'];
        $jackpotcon = $req['jackpotcontribution'];

        return $this->FinalizeRequest();
    }

    public function rollOut(Request $req){
        //Roll out player’s bet amount to game server in Table or Fish game
        $account    = $req['account'];
        $date_i     = $req['eventTime'];
        $g_provider = $req['gamehall'];
        $g_code     = $req['gamecode'];
        $round_id   = $req['roundid'];
        $amount     = $req['amount'];
        $mtcode     = $req['mtcode'];
        $session    = $req['session'];

        return $this->FinalizeRequest();
    }

    public function takeAll(Request $req){
        //Take all the money out from player’s Seamless Wallet and transfer to Fishing game server
        $account    = $req['account'];
        $date_i     = $req['eventTime'];
        $g_provider = $req['gamehall'];
        $g_code     = $req['gamecode'];
        $round_id   = $req['roundid'];
        $amount     = $req['amount'];
        $mtcode     = $req['mtcode'];
        $session    = $req['session'];

        return $this->FinalizeRequest();
    }

    public function rollIn(Request $req){
        //End game round and return remaining amount from game server to player’s Seamless Wallet (for Fish game and table game only)
        $account    = $req['account'];
        $date_i     = $req['eventTime'];
        $g_provider = $req['gamehall'];
        $g_code     = $req['gamecode'];
        $round_id   = $req['roundid'];
        $round_id   = $req['validbet'];
        $round_id   = $req['bet'];
        $round_id   = $req['win'];
        $round_id   = $req['roomfee'];
        $amount     = $req['amount'];
        $mtcode     = $req['mtcode'];
        $createTime = $req['createTime'];
        $rake       = $req['rake'];
        $gametype   = $req['gametype'];

        return $this->FinalizeRequest();
    }

    public function debitBalance(Request $req){
        //Substract amount from settled order
        $account    = $req['account'];
        $date_i     = $req['eventTime'];
        $g_provider = $req['gamehall'];
        $g_code     = $req['gamecode'];
        $round_id   = $req['roundid'];
        $amount     = $req['amount'];
        $mtcode     = $req['mtcode'];

        return $this->FinalizeRequest();
    }

    public function creditBalance(Request $req){
        //Add amount from settled order
        $account    = $req['account'];
        $date_i     = $req['eventTime'];
        $g_provider = $req['gamehall'];
        $g_code     = $req['gamecode'];
        $round_id   = $req['roundid'];
        $amount     = $req['amount'];
        $mtcode     = $req['mtcode'];

        return $this->FinalizeRequest();
    }

    public function bonus(Request $req){
        //Game Bonus
        $account    = $req['account'];
        $date_i     = $req['eventTime'];
        $g_provider = $req['gamehall'];
        $g_code     = $req['gamecode'];
        $round_id   = $req['roundid'];
        $amount     = $req['amount'];
        $mtcode     = $req['mtcode'];

        return $this->FinalizeRequest();
    }

    public function payOff(Request $req){
        //Deliver payout amount to player from promotion
        $account    = $req['account'];
        $date_i     = $req['eventTime'];
        $amount     = $req['amount'];
        $mtcode     = $req['mtcode'];
        $mtcode     = $req['remark'];

        return $this->FinalizeRequest();
    }

    public function refund(Request $req){
        //Refund bet/rollout/takeall amount
        $account    = $req['account'];
        $date_i     = $req['eventTime'];
        $amount     = $req['amount'];
        $mtcode     = $req['mtcode'];
        $mtcode     = $req['remark'];

        return $this->FinalizeRequest();
    }

    // Finallizer
    public function FinalizeRequest($code = 0, $account = ""){
        $balance = 0;
        if(in_array($account, $this->dummyplayer)){
            $balance = 1000000;
        }

        $response_format = [
            "data" => [
                "balance"   => $balance,
                "currency"  => $this->currency,
            ],
            "status" => [
                "code"      => "0",
                "message"   => "Success",
                "datetime"  => date("c"),
            ]
        ];
        return response()->json($response_format);
    }
}
