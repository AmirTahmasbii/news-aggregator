<?php

namespace App\Console\Commands\Article;

use App\Services\Sources\Guardian\client as GuardianClient;
use App\Services\Sources\NewsApi\client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article:update-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fetch and update articles from live api into local database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $new = (new client)->update();
        
        if ($new['status'] != 'success')
            Log::alert('news_api: ' . $new['message']);

        $guard = (new GuardianClient)->update();

        if ($guard['status'] != 'success')
            Log::alert('guard: ' . $guard['message']);
    }
}
