<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GiftCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Company::query()->findOrFail(1);
        $company->giftCards()->create([
            'title' => 'gift 1',
            'coin' => 50,
            'expiration_date' => '2023-12-29'
        ]);
    }
}
