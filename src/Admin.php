<?php

namespace Admin;

use Admin\Themes\AdminLteTheme;
use Admin\Themes\PublishedAdminLteTheme;
use Admin\Themes\Theme;
use Auth;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Admin\Components\Vue\ModalCollection;
use Admin\Models\AdminUser;
use Illuminate\Support\Facades\Crypt;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Admin
{
    /**
     * @var string
     */
    public static string $version = '6.0.0';

    /**
     * @var Theme[]
     */
    public static array $themes = [
        'admin-lte' => AdminLteTheme::class,
        'published-admin-lte' => PublishedAdminLteTheme::class,
    ];

    /**
     * @var ExtendProvider[]
     */
    public static array $nav_extensions = [];

    /**
     * @var ExtendProvider[]
     */
    public static array $installed_extensions = [];

    /**
     * @var ExtendProvider[]
     */
    public static array $not_installed_extensions = [];

    /**
     * @var bool[]
     */
    public static array $extensions = [];

    /**
     * @var bool
     */
    public static bool $echo = false;

    /**
     * @var string|null
     */
    public static ?string $lang_select = null;

    /**
     * @var string
     */
    public string $theme = 'admin-lte';

    /**
     * Configure instance in constructor
     */
    public function __construct()
    {
        $this->theme = config('admin.theme', 'admin-lte');
    }

    /**
     * Add theme using the "register" method in your service provider.
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
     * @return Theme|null
     */
    public function getTheme(): ?Theme
    {
        $class = static::$themes[$this->theme] ?? static::$themes['admin-lte'];
        return new $class();
    }

    /**
     * @return Theme[]|string[]
     */
    public function getThemes(): array
    {
        return array_map(fn (string $class) => new $class, static::$themes);
    }

    /**
     * @return AdminUser|Authenticatable|Admin|null
     */
    public function user(): Admin|AdminUser|Authenticatable|null
    {
        return Auth::guard('admin')->user();
    }

    /**
     * @return bool
     */
    public function guest(): bool
    {
        return Auth::guard('admin')->guest();
    }

    /**
     * @return string
     */
    public function version(): string
    {
        if (class_exists(\Composer\InstalledVersions::class)) {
            return \Composer\InstalledVersions::getPrettyVersion('bfg/admin');
        } else {
            $lock_file = base_path('composer.lock');
            if (is_file($lock_file)) {
                $lock = file_get_contents($lock_file);
                $json = json_decode($lock, 1);
                $admin = collect($json['packages'])->where('name', 'bfg/admin')->first();
                if ($admin && isset($admin['version'])) {
                    return ltrim($admin['version'], 'v');
                }
            }
        }
        return self::$version;
    }

    /**
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
     * @param  string  $name
     * @return bool|ExtendProvider
     */
    public function extension(string $name): bool|ExtendProvider
    {
        if (isset(self::$installed_extensions[$name])) {
            return self::$installed_extensions[$name];
        }

        return false;
    }

    /**
     * @return ExtendProvider[]
     */
    public function extensions(): array
    {
        return self::$installed_extensions;
    }

    /**
     * @param  string  $name
     * @return ExtendProvider|null
     */
    public function getExtension(string $name): ?ExtendProvider
    {
        if (isset(self::$installed_extensions[$name])) {
            return self::$installed_extensions[$name];
        } elseif (self::$not_installed_extensions[$name]) {
            return self::$not_installed_extensions[$name];
        }

        return null;
    }

    /**
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
     * Get request lang.
     *
     * @return array|string|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function nowLang(): array|string|null
    {
        if (self::$lang_select) {
            return self::$lang_select;
        }

        $select = null;

        $segment = request()->segments()[0] ?? null;

        if (in_array($segment, config('admin.languages', ['en', 'uk', 'ru']))) {
            $select = $segment;
        } else if (request()->cookie('lang')) {
            $lang = request()->cookie('lang');

            if (in_array($lang, config('admin.languages', ['en', 'uk', 'ru']))) {
                $select = $lang;
            } else {
                $lang = explode("|", Crypt::decryptString($lang))[1] ?? null;

                if (in_array($lang, config('admin.languages'))) {
                    $select = $lang;
                }
            }
        }

        if (!$select) {
            $select = config('app.locale');
        }

        return self::$lang_select = $select;
    }
}
