<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Pboivin\FilamentPeek\FilamentPeekPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        $this->renderAdminLoginLinksInLocalEnv();
        $this->addDebugBarScript();
    }

    public function panel(Panel $panel): Panel
    {
        FilamentView::registerRenderHook(
            'panels::head.start',
            fn (): string => '<meta name="robots" content="noindex,nofollow">'
        );

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->spa()
            ->favicon(asset('/favicon.ico'))
            ->brandLogo(fn () => view('admin.logo'))
            ->colors([
                'primary' => Color::Blue,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([

            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(\locales()->keys()->toArray()),
                FilamentExceptionsPlugin::make(),
                FilamentPeekPlugin::make(),
            ]);
    }

    private function renderAdminLoginLinksInLocalEnv(): void
    {
        Filament::registerRenderHook(
            'panels::auth.login.form.before',
            fn (): string => Blade::render(<<<'HTML'
             @env('local')
             <div class="flex space-x-2 leading-none">
                <strong>Login As: </strong>
                <div><x-login-link email="admin@example.com" label="Admin" /></div>
            </div>
             @endenv
            HTML
            )
        );
    }

    private function addDebugBarScript(): void
    {
        if (config('app.debug')) {
            FilamentAsset::register([
                Js::make('clockwork-toolbar', 'https://cdn.jsdelivr.net/gh/underground-works/clockwork-browser@1/dist/toolbar.js'),
            ]);
        }
    }
}
