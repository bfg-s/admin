<?php

declare(strict_types=1);

namespace Admin;

use Admin\Components\Vue\ModalCollection;
use Admin\Models\AdminUser;
use Admin\Themes\AdminLteTheme;
use Admin\Themes\Theme;
use Auth;
use Composer\InstalledVersions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Crypt;

/**
 * The main class of the admin panel facade.
 */
class AdminEngine
{
    /**
     * Additional version if the main one is not calculated.
     *
     * @var string
     */
    public static string $version = '6.2.3';

    /**
     * Main list of admin panel themes.
     *
     * @var Theme[]
     */
    public static array $themes = [
        'admin-lte' => AdminLteTheme::class,
    ];

    /**
     * Extensions that participate in navigation.
     *
     * @var ExtendProvider[]
     */
    public static array $nav_extensions = [];

    /**
     * Installed extensions.
     *
     * @var ExtendProvider[]
     */
    public static array $installed_extensions = [];

    /**
     * Uninstalled extensions.
     *
     * @var ExtendProvider[]
     */
    public static array $not_installed_extensions = [];

    /**
     * A list of installed admin panel extensions obtained from a static file.
     *
     * @var bool[]
     */
    public static array $extensions = [];

    /**
     * To record the selected language of the admin panel.
     *
     * @var string|null
     */
    public static ?string $lang_select = null;

    /**
     * Current theme name.
     *
     * @var string
     */
    public string $theme = 'admin-lte';

    /**
     * Admin constructor.
     */
    public function __construct()
    {
        $this->theme = config('admin.theme', 'admin-lte');
    }

    /**
     * Method for adding a theme to the admin panel.
     *
     * @param  string  $class
     * @return void
     */
    public function addTheme(string $class): void
    {
        $obj = new $class;

        if ($obj instanceof Theme) {
            static::$themes[$obj->getSlug()] = $class;
        }
    }

    /**
     * Method for getting the admin panel theme.
     *
     * @return Theme|null
     */
    public function getTheme(): ?Theme
    {
        $class = static::$themes[$this->theme] ?? static::$themes['admin-lte'];
        return new $class();
    }

    /**
     * Method for getting a list of admin panel topics.
     *
     * @return Theme[]|string[]
     */
    public function getThemes(): array
    {
        return array_map(fn(string $class) => new $class, static::$themes);
    }

    /**
     * Method for getting the current user of the admin panel.
     *
     * @return AdminUser|Authenticatable|AdminEngine|null
     */
    public function user(): AdminEngine|AdminUser|Authenticatable|null
    {
        return Auth::guard('admin')->user();
    }

    /**
     * Method for checking a guest.
     *
     * @return bool
     */
    public function guest(): bool
    {
        return Auth::guard('admin')->guest();
    }

    /**
     * Method for getting the admin panel version.
     *
     * @return string
     */
    public function version(): string
    {
        if (class_exists(InstalledVersions::class)) {
            return InstalledVersions::getPrettyVersion('bfg/admin');
        } else {
            $lock_file = base_path('composer.lock');
            if (is_file($lock_file)) {
                $lock = file_get_contents($lock_file);
                $json = json_decode($lock, true);
                $admin = collect($json['packages'])->where('name', 'bfg/admin')->first();
                if ($admin && isset($admin['version'])) {
                    return ltrim($admin['version'], 'v');
                }
            }
        }
        return self::$version;
    }

    /**
     * Method for registering an admin panel extension (Used under the hood of the extension provider).
     *
     * @param  ExtendProvider  $provider
     * @return bool
     */
    public function registerExtension(ExtendProvider $provider): bool
    {
        if (!$provider::$name) {
            return false;
        }

        if (!self::$extensions) {
            self::$extensions = is_file(app()->bootstrapPath('admin_extensions.php'))
                ? include app()->bootstrapPath('admin_extensions.php')
                : [];
        }

        if (isset(self::$extensions[$provider::$name]) || $provider::$slug === 'application') {
            if (!isset(self::$installed_extensions[$provider::$name])) {
                self::$installed_extensions[$provider::$name] = $provider;

                if ($provider->included()) {
                    if (!$provider::$after) {
                        self::$nav_extensions[$provider::$slug] = $provider;
                    } else {
                        self::$nav_extensions[$provider::$after][] = $provider;
                    }
                }
            }
        } elseif (!isset(self::$not_installed_extensions[$provider::$name])) {
            self::$not_installed_extensions[$provider::$name] = $provider;
        }

        return true;
    }

    /**
     * Method for getting the installed admin panel extension.
     *
     * @param  string  $name
     * @return ExtendProvider|bool
     */
    public function extension(string $name): ExtendProvider|bool
    {
        if (isset(self::$installed_extensions[$name])) {

            return self::$installed_extensions[$name];
        }

        return false;
    }

    /**
     * Method for getting a list of installed admin panel extensions.
     *
     * @return ExtendProvider[]
     */
    public function extensions(): array
    {
        return self::$installed_extensions;
    }

    /**
     * Method for getting installed or not installed admin panel extension.
     *
     * @param  string  $name
     * @return ExtendProvider|null
     */
    public function getExtension(string $name): ExtendProvider|null
    {
        if (isset(self::$installed_extensions[$name])) {

            return self::$installed_extensions[$name];
        } elseif (self::$not_installed_extensions[$name]) {

            return self::$not_installed_extensions[$name];
        }

        return null;
    }

    /**
     * Method for obtaining a list of admin panel extension providers.
     *
     * @return string[]
     */
    public function extensionProviders(): array
    {
        return array_flip(
            array_map(
                'get_class',
                array_merge(
                    self::$installed_extensions,
                    self::$not_installed_extensions
                )
            )
        );
    }

    /**
     * Method for getting the current language of the admin panel.
     *
     * @return string|null
     */
    public function nowLang(): string|null
    {
        if (self::$lang_select) {

            return self::$lang_select;
        }

        $select = null;

        $segment = request()->segments()[0] ?? null;

        if (in_array($segment, config('admin.languages', ['en', 'uk', 'ru']))) {

            $select = $segment;
        } else {
            if (request()->cookie('lang')) {

                $lang = request()->cookie('lang');

                if (in_array($lang, config('admin.languages', ['en', 'uk', 'ru']))) {

                    $select = $lang;
                } else {
                    try {
                        $lang = explode("|", Crypt::decryptString($lang))[1] ?? null;
                    } catch (\Throwable) {
                        $lang = null;
                    }
                    if (in_array($lang, config('admin.languages'))) {

                        $select = $lang;
                    }
                }
            }
        }

        if (!$select) {

            $select = config('app.locale');
        }

        return self::$lang_select = $select;
    }
}
