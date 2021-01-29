<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCanStoreTransaction()
    {

        $payload = [
            'account_id' => Str::uuid()->__toString(),
            'amount' => rand(10, 1000)
        ];

        $response = $this->json('POST', url('amount'), $payload);

        $response->assertStatus(200);

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals([
            'account_id' => $responseData['data']['account_id'],
            'amount' => $responseData['data']['amount'],
        ], $payload);
    }

    public function testFailOnBadData()
    {

        $this->json('POST', url('amount'), ['amount' => rand(10, 100)])->assertStatus(422);
        $this->json('POST', url('amount'), ['account_id' => Str::uuid()])->assertStatus(422);
    }

    public function testCanFetchTransaction()
    {
        $transaction = Transaction::factory()->create();

        $response = $this->json('GET', url("/transaction/$transaction->id"));

        $response->assertStatus(200);

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($responseData['data'], [
            'account_id' => $transaction->account_id,
            'amount' => $transaction->amount
        ]);
    }

    public function testCanFetchBalance()
    {
        $accountId = Str::uuid();
        $transactions = Transaction::factory()->count(10)->create(['account_id' => $accountId]);
        $sum = $transactions->sum('amount');

        $response = $this->json('GET', url("/balance/$accountId"));

        $response->assertStatus(200);

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($responseData, [
                "balance" => $sum
        ]);
    }

    public function testCanFetchMax()
    {
        $accountId = Str::uuid();
        Transaction::factory()->count(10)->create(['account_id' => $accountId]);
        $sum = Transaction::query()->where(['account_id' => $accountId])->count('account_id');

        $response = $this->json('GET', url("/max_transaction_volume"));

        $response->assertStatus(200);
        $responseData = json_decode($response->getContent(), true);
        $response->assertJsonStructure([
            "maxVolume",
            "accounts"
        ]);
        $this->assertEquals($sum, $responseData['maxVolume']);
    }

    
}
