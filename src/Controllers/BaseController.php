<?php

declare(strict_types=1);

namespace Admin\Controllers;

use Admin;
use Admin\Core\ModelSaver;
use Admin\ExtendProvider;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

/**
 * Basic admin panel controller.
 *
 * @template CurrentModel
 */
abstract class BaseController extends Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * The controller belongs to the admin panel extension provider.
     *
     * @var ExtendProvider|null
     */
    public static ExtendProvider|null $extension_affiliation = null;

    /**
     * The model the admin panel controller works with.
     *
     * @var CurrentModel
     */
    public static $model;

    /**
     * Determine whether the controller belongs to the admin panel extension provider.
     *
     * @return ExtendProvider|null
     */
    public static function extension_affiliation(): ExtendProvider|null
    {
        if (static::$extension_affiliation) {
            return static::$extension_affiliation;
        }

        $provider = 'ServiceProvider';

        $providers = Admin::extensionProviders();

        $iteration = 1;

        while (!empty($piece = body_namespace_element(static::class, $iteration))) {
            if (isset($providers["{$piece}\\{$provider}"])) {
                static::$extension_affiliation = Admin::getExtension($providers["{$piece}\\{$provider}"]);

                break;
            }

            $iteration++;
        }

        return static::$extension_affiliation;
    }

    /**
     * Save request to model.
     *
     * @param  array|null  $data
     * @return bool|void
     */
    public function requestToModel(array $data = null, array $imageModifiers = [])
    {
        $save = $data ?? request()->all();

        $alreadyCached = [];

        foreach (static::$cryptFields as $crypt_field) {
            if (array_key_exists($crypt_field, $save)) {
                if ($save[$crypt_field]) {
                    if (!isset($alreadyCached[$crypt_field])) {
                        $save[$crypt_field] = bcrypt($save[$crypt_field]);
                        $alreadyCached[$crypt_field] = $crypt_field;
                    }
                } else {
                    unset($save[$crypt_field]);
                }
            }
        }

        return $this->model()
            ? ModelSaver::do($this->model(), $save, $this, $imageModifiers)
            : false;
    }

    /**
     * Get current controller model.
     *
     * @return CurrentModel|User|Model|string|null
     */
    public function model()
    {
        return admin_repo()->modelNow;
    }

    /**
     * Get only exists model.
     *
     * @return \Illuminate\Database\Eloquent\Model|\App\Models\User|string|null
     */
    public function existsModel(): Model|User|string|null
    {
        return $this->model() && $this->model()->exists ? $this->model() : null;
    }

    /**
     * Get model primary.
     *
     * @return object|string|null
     */
    public function model_primary(): object|string|null
    {
        return admin_repo()->modelPrimary;
    }

    /**
     * Get now menu.
     *
     * @return array|null
     */
    public function now(): array|null
    {
        return admin_repo()->now;
    }

    /**
     * Check type for resource.
     *
     * @param  string  $type
     * @return bool
     */
    public function isType(string $type): bool
    {
        return $this->type() === $type;
    }

    /**
     * Get resource type.
     *
     * @return string|null
     */
    public function type(): string|null
    {
        return admin_repo()->type;
    }

    /**
     * Get navigator date.
     *
     * @param  null  $name
     * @param  null  $default
     * @return array|mixed
     */
    public function data($name = null, $default = null): mixed
    {
        if (!$name) {
            return admin_repo()->data;
        }

        return admin_repo()->data($name, $default);
    }

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceMap(): array
    {
        return [
            'index' => 'Index',
            'show' => 'Show',
            'create' => 'Create',
            'store' => 'Store',
            'edit' => 'Edit',
            'update' => 'Update',
            'destroy' => 'Destroy',
        ];
    }
}
