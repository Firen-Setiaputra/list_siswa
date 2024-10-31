<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\ServiceProvider;
use App\Filament\Resources\SiswaResource;
use App\Filament\Resources\KeterlambatanResource;

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
         Filament::serving(function () {
             Filament::registerNavigationGroups([
                 NavigationGroup::make()
                     ->label('Data Keterlambatan')
                     ->items([
                         NavigationItem::make()
                             ->label('Siswa')
                             ->url(SiswaResource::getUrl())
                             ->icon('heroicon-o-user-group'),
                         NavigationItem::make()
                             ->label('Keterlambatan')
                             ->url(KeterlambatanResource::getUrl())
                             ->icon('heroicon-o-clock'),
                     ]),
             ]);
         });
     }
}
