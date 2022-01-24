<?php

namespace Lar\LteAdmin\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lar\LteAdmin\Core\Traits\DumpedModel;

/**
 * Lar\LteAdmin\Models\LtePermission.
 *
 * @property int $id
 * @property string $path
 * @property array $method
 * @property string $state
 * @property string|null $description
 * @property int $lte_role_id
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LteRole|null $role
 * @method static \Illuminate\Database\Eloquent\Builder|LtePermission makeDumpedModel()
 * @method static \Illuminate\Database\Eloquent\Builder|LtePermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtePermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtePermission query()
 * @method static \Illuminate\Database\Eloquent\Builder|LtePermission whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtePermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtePermission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtePermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtePermission whereLteRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtePermission whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtePermission wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtePermission whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtePermission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LtePermission extends Model
{
    use DumpedModel;

    /**
     * @var string
     */
    protected $table = 'lte_permission';

    /**
     * @var string[]
     */
    protected $fillable = [
        'path', 'method', 'state', 'description', 'lte_role_id', 'active', // state: open, close
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'method' => 'array',
    ];

    /**
     * @var Collection
     */
    public static $now;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function role()
    {
        return $this->hasOne(LteRole::class, 'id', 'lte_role_id');
    }

    /**
     * @return Collection|\Illuminate\Support\Collection|LtePermission[]
     */
    public static function now()
    {
        if (static::$now) {
            return static::$now;
        }

        $roles = lte_user() ? lte_user()->roles->pluck('id')->toArray() : [1];

        return static::$now = static::whereIn('lte_role_id', $roles)->where('active', 1)->get();
    }

    /**
     * @param  string  $url
     * @param  string  $method
     * @return bool
     */
    public static function checkUrl(string $url, string $method = 'GET'): bool
    {
        $result = ! static::now()->where('path', '==', '*')->where('state', 'close')->first();

        /** @var LtePermission $close */
        foreach (static::now()->where('state', 'close') as $close) {
            $path = static::makeCheckedPath($close->path);

            if (($close->method[0] === '*' || array_search($method, $close->method) !== false) && \Str::is(url($path), $url)) {
                $result = false;
                break;
            }
        }

        if (! $result) {

            /** @var LtePermission $close */
            foreach (static::now()->where('state', 'open') as $open) {
                $path = static::makeCheckedPath($open->path);

                if (($open->method[0] === '*' || array_search($method, $open->method) !== false) && \Str::is(url($path), $url)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    public static function check()
    {
        if (! lte_user()) {
            return true;
        }

        if (request()->is(static::makeCheckedPath('profile*'))) {
            return true;
        }

        $result = ! static::now()->where('path', '==', '*')->where('state', 'close')->first();

        $method = request()->ajax() && ! request()->pjax() && request()->has('_exec') ? 'POST' : request()->getMethod();

        /** @var LtePermission $close */
        foreach (static::now()->where('state', 'close') as $close) {
            $path = static::makeCheckedPath($close->path);

            if (($close->method[0] === '*' || array_search($method, $close->method) !== false) && request()->is($path)) {
                $result = false;
                break;
            }
        }

        if (! $result) {

            /** @var LtePermission $close */
            foreach (static::now()->where('state', 'open') as $open) {
                $path = static::makeCheckedPath($open->path);

                if (($open->method[0] === '*' || array_search($method, $open->method) !== false) && request()->is($path)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @param  string  $inner_path
     * @return string
     */
    public static function makeCheckedPath(string $inner_path)
    {
        $per_path = config('layout.lang_mode') ? '*/' : '';

        return trim($per_path.config('lte.route.prefix'), '/').'/'.trim($inner_path, '/');
    }
}
