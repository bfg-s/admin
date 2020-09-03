<?php

namespace Lar\LteAdmin;

use Lar\LteAdmin\Components\Vue\ModalCollection;

/**
 * Class LteAdmin
 *
 * @package Lar\CryptoApi
 */
class LteAdmin
{
    /**
     * @var string
     */
    static $vesion = "3.1.12";

    /**
     * @var ExtendProvider[]
     */
    static $nav_extensions = [];

    /**
     * @var ExtendProvider[]
     */
    static $installed_extensions = [];

    /**
     * @var ExtendProvider[]
     */
    static $not_installed_extensions = [];

    /**
     * @var bool[]
     */
    static $extensions;

    /**
     * @var bool
     */
    static $echo = false;

    /**
     * @var array
     */
    protected $content_segments = [
        'app_end_wrapper' => [],
        'prep_end_wrapper' => [
            ['component' => ModalCollection::class, 'params' => []]
        ],
        'app_end_content' => [],
        'prep_end_content' => [],
    ];

    /**
     * @return \Lar\LteAdmin\Models\LteUser|\Illuminate\Contracts\Auth\Authenticatable|\App\Models\Admin
     */
    public function user()
    {
        return \Auth::guard('lte')->user();
    }

    /**
     * @return mixed
     */
    public function guest()
    {
        return \Auth::guard('lte')->guest();
    }

    /**
     * @return string
     */
    public function version()
    {
        return LteAdmin::$vesion;
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
    public function toWrapper(string $component, array $params = [], bool $prepend = false)
    {
        $segment = $prepend ? "prep_end_wrapper" : "app_end_wrapper";

        return $this->toSegment($segment, $component, $params);
    }

    /**
     * @param  string  $component
     * @param  array  $params
     * @param  bool  $prepend
     * @return $this
     */
    public function toContent(string $component, array $params = [], bool $prepend = false)
    {
        $segment = $prepend ? "prep_end_content" : "app_end_content";

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
     * @throws \Exception
     */
    public function registerExtension(ExtendProvider $provider)
    {
        if (!$provider::$name) {

            return false;
        }

        if (!LteAdmin::$extensions) {

            LteAdmin::$extensions = include storage_path('lte_extensions.php');
        }

        if (isset(LteAdmin::$extensions[$provider::$name]) || $provider::$slug === 'application') {

            if (!isset(LteAdmin::$installed_extensions[$provider::$name])) {

                LteAdmin::$installed_extensions[$provider::$name] = $provider;

                if ($provider->included()) {

                    if (!$provider::$after) {
                        LteAdmin::$nav_extensions[$provider::$slug] = $provider;
                    } else {
                        LteAdmin::$nav_extensions[$provider::$after][] = $provider;
                    }
                }
            }
        }

        else if (!isset(LteAdmin::$not_installed_extensions[$provider::$name])) {

            LteAdmin::$not_installed_extensions[$provider::$name] = $provider;
        }

        return true;
    }

    /**
     * @param  string  $name
     * @return bool|ExtendProvider
     */
    public function extension(string $name)
    {
        if (isset(LteAdmin::$installed_extensions[$name])) {

            return LteAdmin::$installed_extensions[$name];
        }

        return false;
    }

    /**
     * @return ExtendProvider[]
     */
    public function extensions()
    {
        return LteAdmin::$installed_extensions;
    }

    /**
     * @param  string  $name
     * @return ExtendProvider|null
     */
    public function getExtension(string $name)
    {
        if (isset(LteAdmin::$installed_extensions[$name])) {
            return LteAdmin::$installed_extensions[$name];
        } else if (LteAdmin::$not_installed_extensions[$name]) {
            return LteAdmin::$not_installed_extensions[$name];
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
                    LteAdmin::$installed_extensions,
                    LteAdmin::$not_installed_extensions
                )
            )
        );
    }
}
