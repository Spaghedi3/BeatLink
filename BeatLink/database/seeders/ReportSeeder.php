<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;
use App\Models\User;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $reporter = User::first(); // pick the first user

        Report::create([
            'user_id'         => $reporter->id,
            'reportable_type' => \App\Models\Track::class,
            'reportable_id'   => 25,
            'type'            => 'track',
            'reason'          => 'This track contains copyrighted material.',
            'status'          => 'in_review',
        ]);
    }
}
