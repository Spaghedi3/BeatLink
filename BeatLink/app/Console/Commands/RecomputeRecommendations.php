<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class RecomputeRecommendations extends Command
{
    protected $signature = 'ml:recompute';
    protected $description = 'Export dataset, train model, and seed recommendations';

    public function handle()
    {
        $this->info('1. Exporting dataset...');
        Artisan::call('ml:export-hybrid-dataset'); // You must define this next

        $this->info('2. Running Python training script...');
        $process = new Process(['python', 'train.py']);
        $process->setWorkingDirectory(base_path()); // Ensure you're at the Laravel root
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error("Python error:\n" . $process->getErrorOutput());
            return 1;
        }

        $this->info("Training completed:\n" . $process->getOutput());

        $this->info('3. Seeding new recommendations...');
        Artisan::call('db:seed', ['--class' => 'RecommendationSeeder']);

        $this->info('ML recommendations recomputed and saved!');
        return 0;
    }
}
