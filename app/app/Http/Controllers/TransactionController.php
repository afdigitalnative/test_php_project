<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{


    /**
     * Store a new transaction
     * GET /amount
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function amount(Request $request)
    {
        $validate = validator($request->all(), [
            'account_id' => 'required|uuid',
            'amount' => 'required|numeric'
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $transaction = Transaction::create([
            'account_id' => $request->account_id,
            'amount' => $request->amount
        ]);

        return response()->json(['message' => "Transaction created.", "data" => $transaction]);
    }

    /**
     * Get a transaction detail.
     * GET /transaction/{transaction_id}
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show($transaction_id)
    {
        $transaction = Transaction::query()->where('id', $transaction_id)->first(['account_id', 'amount']);

        return response()->json(['message' => "Transaction details.", "data" => $transaction]);
    }

     /**
     * Display the specified resource.
     * GET /balance/{account_id}
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function balance($account_id)
    {
        $balance = Transaction::query()->where('account_id', $account_id)->sum('amount');

        return response()->json(['balance' => $balance]);
    }

     /**
     * Get balance of an account
     * GET /max_transaction_volume
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function maxVolume()
    {
        $maxVols = \DB::select("SELECT account_id, COUNT(id) as txct FROM transactions GROUP BY account_id ORDER BY txct DESC");
        $max = $maxVols[0];

        $topAccounts = array_filter($maxVols, function($a) use($max) {
            return $a->txct == $max->txct;
        });

        return response()->json([
            "maxVolume" => $max->txct,
            "accounts" => array_column($topAccounts, 'account_id')
        ]);
    }


}
