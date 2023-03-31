<?php

namespace Admin;

use Auth;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Admin\Components\Vue\ModalCollection;
use Admin\Models\AdminUser;

class Admin
{
    /**
     * @var string
     */
    public static $version = '5.0.0';

    /**
     * @var ExtendProvider[]
     */
    public static $nav_extensions = [];

    /**
     * @var ExtendProvider[]
     */
    public static $installed_extensions = [];

    /**
     * @var ExtendProvider[]
     */
    public static $not_installed_extensions = [];

    /**
     * @var bool[]
     */
    public static $extensions;

    /**
     * @var bool
     */
    public static $echo = false;

    /**
     * @var array
     */
    protected $content_segments = [
        'app_end_wrapper' => [],
        'prep_end_wrapper' => [
            //['component' => Navigator::class, 'params' => []],
            ['component' => ModalCollection::class, 'params' => []],
        ],
        'app_end_content' => [],
        'prep_end_content' => [],
    ];

    /**
     * @return AdminUser|Authenticatable|Admin
     */
    public function user()
    {
        return Auth::guard('admin')->user();
    }

    /**
     * @return mixed
     */
    public function guest()
    {
        return Auth::guard('admin')->guest();
    }

    /**
     * @return string
     */
    public function version()
    {
        return self::$version;
    }

    /**
     * @param  string  $component
     * @param  array  $params
     * @param  bool  $prepend
     * @return $this
     */
    public function toWrapper(string $component, array $params = [], bool $prepend = false)
    {
        $segment = $prepend ? 'prep_end_wrapper' : 'app_end_wrapper';

        return $this->toSegment($segment, $component, $params);
    }

    /**
     * @param  string  $segment
     * @param  string  $component
     * @param  array  $params
     * @return $this
     */
    public function toSegment(string $segment, string $component, array $params = [])
    {
        if (isset($this->content_segments[$segment])) {
            $this->content_segments[$segment][] = ['component' => $component, 'params' => $params];
        }

        return $this;
    }

    /**
     * @param  string  $component
     * @param  array  $params
     * @param  bool  $prepend
     * @return $this
     */
    public function toContent(string $component, array $params = [], bool $prepend = false)
    {
        $segment = $prepend ? 'prep_end_content' : 'app_end_content';

        return $this->toSegment($segment, $component, $params);
    }

    /**
     * @param  string  $segment
     * @return array
     */
    public function getSegments(string $segment)
    {
        if (isset($this->content_segments[$segment])) {
            return $this->content_segments[$segment];
        }

        return [];
    }

    /**
     * @param  ExtendProvider  $provider
     * @throws Exception
     */
    public function registerExtension(ExtendProvider $provider)
    {
        if (!$provider::$name) {
            return false;
        }

        if (!self::$extensions) {
            self::$extensions = is_file(storage_path('admin_extensions.php'))
                ? include storage_path('admin_extensions.php')
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
    public function extension(string $name)
    {
        if (isset(self::$installed_extensions[$name])) {
            return self::$installed_extensions[$name];
        }

        return false;
    }

    /**
     * @return ExtendProvider[]
     */
    public function extensions()
    {
        return self::$installed_extensions;
    }

    /**
     * @param  string  $name
     * @return ExtendProvider|null
     */
    public function getExtension(string $name)
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
    public function extensionProviders()
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
}
