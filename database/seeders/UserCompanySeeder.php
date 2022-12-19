<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Company::query()->findOrFail(1);
        $user = User::query()->findOrFail(1);
        $company->users()->updateExistingPivot($user, array(['first_name' => 'yasin', 'last_name' => 'baghban', 'coin_amount' => 50, 'currency_amount' => 1500]));
    }
}
