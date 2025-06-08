<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Observers\TicketObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure default pagination style is used
        \Illuminate\Pagination\Paginator::useBootstrap();
        
        // Rejestracja observera biletów
        Ticket::observe(TicketObserver::class);
    }
}
