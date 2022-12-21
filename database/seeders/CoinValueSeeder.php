<?php

namespace Database\Seeders;

use App\Models\CoinValue;
use App\Models\Company;
use http\Client\Curl\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoinValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Company::query()->find(1);
        CoinValue::query()->updateOrCreate([
           'company_id' => $company->id,
        ],[
            'coin_value' => 1500
        ]);
        $company->coin_value_history()->create([
            'user_id' => 1,
            'coin_value' => 1500
        ]);
    }
}
