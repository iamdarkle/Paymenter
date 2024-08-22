<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Qirolab\Theme\Theme;

class SettingsProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {}

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->getSettings();
    }

    public static function getSettings()
    {
        if (config('settings') && !empty(config('settings'))) {
            return;
        }
        try {
            // Load settings from cache
            $settings = Cache::get('settings', []);
            if (empty($settings)) {
                $settings = Setting::where('settingable_type', null)->get()->pluck('value', 'key');
                Cache::put('settings', $settings);
            }
            // Is the current command a config:cache command?
            if (isset($_SERVER['argv']) && (in_array('config:cache', $_SERVER['argv']) || in_array('optimize', $_SERVER['argv']))) {
                dd();

                return;
            }
            config(['settings' => $settings]);
            foreach (\App\Classes\Settings::settings() as $settings) {
                foreach ($settings as $setting) {
                    if (isset($setting['override']) && config("settings.$setting[name]") !== null) {
                        config([$setting['override'] => config("settings.$setting[name]")]);
                    }
                }
            }

            include_once app_path('Classes/helpers.php');

            date_default_timezone_set(config('settings.timezone', 'UTC'));

            Theme::set(config('settings.theme', 'default'), 'default');
        } catch (\Exception $e) {
            // Do nothing
        }
    }

    public static function flushCache()
    {
        Cache::forget('settings');
        self::getSettings();
    }
}
