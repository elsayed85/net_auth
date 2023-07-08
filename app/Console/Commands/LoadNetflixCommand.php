<?php

namespace App\Console\Commands;

use App\Models\CookieRecord;
use App\Services\Netflix;
use Illuminate\Console\Command;

class LoadNetflixCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'netflix:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Netflix Accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $validAccountsCount = 0;
        $invalidAccountsCount = 0;
        $cookies = CookieRecord::freshAccounts()->get();

        // use progress bar and chunk results
        $bar = $this->output->createProgressBar(count($cookies));
        $bar->start();

        $chunks = $cookies->chunk(10);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $cookie) {
                $netflix = new Netflix();
                if ($netflix->login($cookie)) {
                    $cookie->is_active = true;
                    $cookie->save();
                    $validAccountsCount++;
                } else {
                    $cookie->delete();
                    $invalidAccountsCount++;
                }
                $bar->advance();
            }
        }

        $bar->finish();

        $this->info("\nDone");

        $this->info("Valid Accounts: {$validAccountsCount}");
        $this->info("Invalid Accounts: {$invalidAccountsCount}");
    }
}
