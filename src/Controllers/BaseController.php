<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Core\ModelSaver;
use Illuminate\Routing\Controller;
use Lar\LteAdmin\ExtendProvider;
use Lar\LteAdmin\Models\LteFunction;
use Lar\LteAdmin\Models\LteRole;

/**
 * Trait ControllerMethods
 * @package Lar\LteAdmin\Core\Traits
 */
abstract class BaseController extends Controller
{
    /**
     * @var ExtendProvider|null
     */
    static $extension_affiliation;

    /**
     * Save request to model
     *
     * @param  array|null  $data
     * @return bool|void
     */
    public function requestToModel(array $data = null)
    {
        $save = $data ?? request()->all();

        foreach (static::$crypt_fields as $crypt_field) {
            if (array_key_exists($crypt_field, $save)) {
                if ($save[$crypt_field]) {
                    $save[$crypt_field] = bcrypt($save[$crypt_field]);
                } else {
                    unset($save[$crypt_field]);
                }
            }
        }

        return $this->model() ? ModelSaver::do($this->model(), $save) : false;
    }

    /**
     * Get only exists model
     *
     * @return \Illuminate\Database\Eloquent\Model|\Lar\LteAdmin\Getters\Menu|string|null
     */
    public function existsModel()
    {
        return $this->model() && $this->model()->exists ? $this->model() : null;
    }

    /**
     * Get menu model
     *
     * @return \Illuminate\Database\Eloquent\Model|\Lar\LteAdmin\Getters\Menu|string|null
     */
    public function model()
    {
        return gets()->lte->menu->model;
    }

    /**
     * Get now menu
     *
     * @return array|\Lar\LteAdmin\Getters\Menu|null
     */
    public function now()
    {
        return gets()->lte->menu->now;
    }

    /**
     * Get resource type
     *
     * @return \Lar\LteAdmin\Getters\Menu|string|null
     */
    public function type()
    {
        return gets()->lte->menu->type;
    }

    /**
     * Check type for resource
     *
     * @param  string  $type
     * @return bool
     */
    public function isType(string $type)
    {
        return $this->type() === $type;
    }

    /**
     * @param  null  $name
     * @param  null  $default
     * @return \Lar\LteAdmin\Getters\Menu|string|null|mixed
     */
    public function data($name = null, $default = null)
    {
        if (!$name) {

            return gets()->lte->menu->data;
        }

        return gets()->lte->menu->data($name, $default);
    }

    /**
     * @param  string  $method
     * @return bool
     */
    public function can(string $method)
    {
        return lte_class_can(static::class, $method);
    }

    /**
     * @return ExtendProvider|null
     */
    public static function extension_affiliation()
    {
        if (static::$extension_affiliation) {

            return static::$extension_affiliation;
        }

        $provider = "ServiceProvider";

        $providers = \LteAdmin::extensionProviders();

        $iteration = 1;

        while (!empty($piece = body_namespace_element(static::class, $iteration))) {

            if (isset($providers["{$piece}\\{$provider}"])) {

                static::$extension_affiliation = \LteAdmin::getExtension($providers["{$piece}\\{$provider}"]);

                break;
            }

            $iteration++;
        }

        return static::$extension_affiliation;
    }

    /**
     * @param  string  $method
     * @param  array|string[]  $roles
     * @param  string|null  $description
     * @return array
     */
    public static function generatePermission(string $method, array $roles = ['*'], string $description = null)
    {
        $provider = static::extension_affiliation();

        $p_desc = "";

        if ($provider && $provider::$description) {

            $p_desc = $provider::$description;
        }

        if (!$p_desc) {

            $p_desc = static::class;
        }

        return [
            'slug' => $method,
            'class' => static::class,
            'description' => $p_desc . ($description ? " [$description]" : (\Lang::has("lte.about_method.{$method}") ? " [@lte.about_method.{$method}]":" [{$method}]")),
            'roles' => $roles === ['*'] ? LteRole::all()->pluck('id')->toArray() : collect($roles)->map(function ($item) {
                return is_numeric($item) ? $item : LteRole::where('slug', $item)->first()->id;
            })->filter()->values()->toArray()
        ];
    }
}