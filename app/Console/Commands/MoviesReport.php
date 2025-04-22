<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Mail\MovieListMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MoviesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:movies-report {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Report of all of my Movies';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $sendToEmail = $this->option('email');
        $movies = Movie::get();
        Mail::to($sendToEmail)->send(new MovieListMail($movies));
        return Command::SUCCESS;
    }
}
