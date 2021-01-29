<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $uuids = [];
        for($i = 0; $i < 10; $i++) {
            $uuids[] = Str::uuid();
        }

        for($i = 0; $i < 120; $i++) {
            Transaction::factory()->count(5)->create(['account_id' => $uuids[rand(0, count($uuids) - 1)]]);
        }
    }
}
