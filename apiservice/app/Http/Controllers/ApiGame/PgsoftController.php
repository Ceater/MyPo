<?php

namespace App\Http\Controllers\ApiGame;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\PlayerApi;
use App\Models\game\TrxListPgsoft;
use App\Models\game\TrxMutationPgsoft;

class PgsoftController extends Controller
{
    public $prid     = 1;
    public $root_url = "https://m.pg-redirect.net";
    public $op_token = "5f2d63306960f9653269282436487d10";
    public $sc_token = "8f39221526fb09d331ec5e944ad6c44b";
    public $st_token = "1078b4233542ad51cb396762cfcd3503";
    public $convertRatio = 1000;

    /**
     * 1.operator_token
     * 5f2d63306960f9653269282436487d10
     *
     * 2.secret_key
     * 8f39221526fb09d331ec5e944ad6c44b
     *
     * 3.salt
     * 1078b4233542ad51cb396762cfcd3503
     *
     * http://api-staging.octosplay.com/api/pgsoft/verifysession
     * http://api-staging.octosplay.com/api/pgsoft/cash/get
     * http://api-staging.octosplay.com/api/pgsoft/cash/transferinout
     */

    public function GetLaunchUrl(Request $req)
    {
        /**
         * Check user api exist
         * Get the player token
         */
        $username       = $req['user'];
        $gameCode       = $req['gcod'];
        $operatorToken  = $this->op_token; //Game Token

        $code = 0;

        try{
            $options    = [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'group' => env('GROUP_WEB', '1'),
                    'uid'   => $username,
                    'prid'  => 1,
                ]
            ];

            $endpoint   = env('GAMEBOY_URL', '') . "/member/get_game_token";
            $response   = makeRequest($endpoint, $options);
            $res = json_decode($response->getBody(), TRUE);

            if($response->getStatusCode() != 200 || $res['code'] != 0){
                throw new \Exception($res['data'], $res['code']);
            }

            $playerToken    = $res['data'];
            $lauch_url      = $this->root_url . "/" . $gameCode . "/index.html?bet_type=1&operator_token=$operatorToken&operator_player_session=$playerToken&language=id";

            $response = [
                "url"   => $lauch_url
            ];
        }catch(\Exception $e){
            $code = $res['code'];
            $response = [
                "error"   => $e
            ];
        }
        return CreateResponse($response, "", $code);
    }

    public function VerifyToken(Request $req)
    {
        $operatorToken  = $req['operator_token'];
        $secretToken    = $req['secret_key'];
        $op_pl_ses      = $req['operator_player_session'];

        $ec         = null;
        $datartn    = null;

        try{
            $playerApi = PlayerApi::with('player')->where("token", $op_pl_ses)->first();

            #region validation
            if($operatorToken != $this->op_token){
                $ec = $this->errorMessage('1200');
                throw new \Exception("");
            }
            if($secretToken != $this->sc_token){
                $ec = $this->errorMessage('1200');
                throw new \Exception("");
            }
            if(empty($playerApi)){
                $ec = $this->errorMessage('1034');
                throw new \Exception("");
            }
            #endregion

            $datartn = [
                'player_name'   => $playerApi->user_api,
                'nickname'      => $playerApi->player->username,
                'currency'      => env('CURRENCY_CODE', 'IDR')
            ];

        }catch(\Exception $e){

        }

        ending_phase:
        $arr_rtn = [
            'data'  => $datartn,
            'error' => $ec
        ];
        return $arr_rtn;
    }

    function getbalance(Request $req)
    {
        $username   = $req['player_name'];
        $ec         = null;
        $datartn    = null;

        try{
            $playerApi  = PlayerApi::where("user_api", $username)->first();

            if(empty($playerApi)){
                $ec = $this->errorMessage('1034');
                throw new \Exception("");
            }

            $datartn = [
                'currency_code'     => "IDR",
                'balance_amount'    => (double) $playerApi->balance / $this->convertRatio,
                'updated_time'      => time()
            ];

        }catch(\Exception $e){

        }

        ending_phase:
        $arr_rtn = [
            'data'  => $datartn,
            'error' => $ec
        ];
        return $arr_rtn;
    }

    function transferinout(Request $req)
    {
        $operatorToken  = $req['operator_token'];
        $secretToken    = $req['secret_key'];
        $currency       = $req['currency_code'];
        $op_pl_ses  = $req['operator_player_session'];
        $roundid    = $req['parent_bet_id'];
        $username   = $req['player_name'];
        $gameid     = $req['game_id'];
        $turnover   = $req['bet_amount'] * $this->convertRatio;
        $transid    = $req['transaction_id'];
        $win_amount = $req['win_amount'] * $this->convertRatio;
        $tf_amount  = $req['transfer_amount'] * $this->convertRatio; //Player Win Loss amount Note:  The amount can be positive or negative, Negative: Balance deduction, Positive: Balance addition
        $is_resent  = $req['is_validate_bet']; //To indicate if the request is a transaction that has been resent for validation True: Resent transaction False: Normal transaction
        $is_eround  = $req['is_end_round']; //To indicate if the current game hand has ended
        $is_adjst   = $req['is_adjustment']; //To indicate if the request is an adjustment or a normal transaction for a pending bet True: Adjustment False: Normal transaction
        $upd_tm     = $req['updated_time'];
        $datartn    = null;
        $ec         = null;

        $member = PlayerApi::where("token", $op_pl_ses)->first();
        $trans  = TrxListPgsoft::where("trx_id", $transid)->first();

        try{
            //region validation
            if($operatorToken != $this->op_token){
                $ec = $this->errorMessage('1200');
                throw new \Exception("");
            }
            if($secretToken != $this->sc_token){
                $ec = $this->errorMessage('1200');
                throw new \Exception("");
            }
            if($currency != env('CURRENCY_CODE', 'IDR')){
                $ec = $this->errorMessage('1034');
                throw new \Exception("");
            }
            if(empty($member)){
                $ec = $this->errorMessage('1034');
                throw new \Exception("");
            }
            if($is_adjst == TRUE){ //adj trans
                throw new \Exception("");
            }
            if($is_resent == TRUE){ //validate only
                throw new \Exception("");
            }
            //endregion

            $balance    = $member->balance;
            $vba        = ABS($turnover);
            $balance    = $balance - $vba;

            if( $balance < 0 ){
                $ec = $this->errorMessage('3202');
                throw new \Exception("");
            }

            $trx_list = [
                "oper_id"   => "",
                "ref_id"    => "",
                "trx_id"    => $transid,
                "game_name" => "",
                "username"  => $username,
                "user_api"  => $username,
                "lvid"      => "",
                "prid"      => $this->prid,
                "pcatid"    => "",
                "turnover"  => (int) $turnover,
                "vba"       => (int) ABS($tf_amount),
                "payout"    => 0,
                "wl"        => (int) $tf_amount,
                "commission"    => 0,
                "progresive"    => 0,
                "is_win"        => 0,
                "is_done"       => 1,
            ];
            TrxListPgsoft::create($trx_list);

            $trx_mutation = [
                "oper_id"   => "",
                "ref_id"    => "",
                "trx_id"    => $transid,
                "username"  => $username,
                "description"   => "Bet",
                "amount"    => (int) ABS($tf_amount),
                "mut_type"  => 1,
                "new_bal"   => $balance,
            ];
            TrxMutationPgsoft::create($trx_mutation);

            if($win_amount > 0){
                $balance    = $balance + $win_amount;
                $trx_upd    = [
                    "payout"    => (int) $tf_amount,
                    "wl"        => (int) $turnover - $tf_amount,
                    "is_win"    => 1
                ];
                TrxListPgsoft::where("trx_id", $transid)->update($trx_upd);

                $trx_mutation = [
                    "oper_id"   => "",
                    "ref_id"    => "",
                    "trx_id"    => $transid,
                    "username"  => $username,
                    "description"   => "Win",
                    "amount"    => (int) ABS($tf_amount),
                    "mut_type"  => 0,
                    "new_bal"   => $balance,
                ];
                TrxMutationPgsoft::create($trx_mutation);
            }

            PlayerApi::where('token', $op_pl_ses)->update([
                'balance' => DB::raw('balance+' . $tf_amount + $win_amount)
            ]);

            $datartn = [
                'currency_code'     => env('CURRENCY_CODE', 'IDR'),
                'balance_amount'    => (double) $balance / $this->convertRatio,
                'updated_time'      => $upd_tm
            ];
        }catch(\Exception $e){

        }

        $arr_rtn = [
            'data'  => $datartn,
            'error' => $ec
        ];
        return $arr_rtn;
    }

    function errorMessage($error)
    {
        $dataError = [
            "1034" => "Invalid request",
            "1035" => "Operation Failed",
            "1200" => "Internal server error",
            "1204" => "Invalid operator",
            "1300" => "Invalid player session",
            "1301" => "Player session token is empty",
            "1302" => "Invalid player session",
            "1303" => "Server error occurs",
            "1305" => "Invalid player",
            "1306" => "Player is blocked to access current game",
            "1307" => "Invalid player session",
            "1308" => "Player session is expired",
            "1309" => "Player is inactive",
            "1310" => "Failed to verify operator player session",
            "1315" => "Player’s operation in progress",
            "1400" => "Game is under maintenance",
            "1401" => "Game is inactive",
            "1402" => "Game does not exist",
            "3001" => "Value cannot be null",
            "3004" => "Player does not exist",
            "3005" => "Player wallet does not exist",
            "3006" => "Player wallet already exists",
            "3009" => "Free game does not exist",
            "3013" => "Out of the balance amount to transfer out",
            "3014" => "Free game cannot be cancelled",
            "3019" => "Not enough free game",
            "3021" => "No bet exists",
            "3022" => "Bet already pay-out",
            "3030" => "Free game expired",
            "3031" => "Free game already converted",
            "3032" => "Bet already existed",
            "3033" => "Bet failed",
            "3034" => "Pay-out failed",
            "3035" => "Invalid multiplier",
            "3036" => "Not enough balance to convert",
            "3040" => "Transaction does not exist",
            "3202" => "Not enough cash balance to bet",
        ];
        return [
            "code"      => $error,
            "message"   => $dataError[$error],
        ];
    }
}
