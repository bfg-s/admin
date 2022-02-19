<?php

namespace LteAdmin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use LteAdmin;
use LteAdmin\Traits\DumpedModel;
use Str;

/**
 * LteAdmin\Models\LtePermission.
 *
 * @property int $id
 * @property string $path
 * @property array $method
 * @property string $state
 * @property string|null $description
 * @property int $lte_role_id
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\LteRole|null $role
 * @method static Builder|LtePermission makeDumpedModel()
 * @method static Builder|LtePermission newModelQuery()
 * @method static Builder|LtePermission newQuery()
 * @method static Builder|LtePermission query()
 * @method static Builder|LtePermission whereActive($value)
 * @method static Builder|LtePermission whereCreatedAt($value)
 * @method static Builder|LtePermission whereDescription($value)
 * @method static Builder|LtePermission whereId($value)
 * @method static Builder|LtePermission whereLteRoleId($value)
 * @method static Builder|LtePermission whereMethod($value)
 * @method static Builder|LtePermission wherePath($value)
 * @method static Builder|LtePermission whereState($value)
 * @method static Builder|LtePermission whereUpdatedAt($value)
 * @mixin Eloquent
 */
class LtePermission extends Model
{
    use DumpedModel;

    /**
     * @var Collection
     */
    public static $now;
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
     * @param  string  $url
     * @param  string  $method
     * @return bool
     */
    public static function checkUrl(string $url, string $method = 'GET'): bool
    {
        $result = !static::now()->where('path', '==', '*')->where('state', 'close')->first();

        /** @var LtePermission $close */
        foreach (static::now()->where('state', 'close') as $close) {
            $path = static::makeCheckedPath($close->path);

            if (($close->method[0] === '*' || in_array($method, $close->method)) && Str::is(
                    url($path),
                    $url
                )) {
                $result = false;
                break;
            }
        }

        if (!$result) {
            /** @var LtePermission $close */
            foreach (static::now()->where('state', 'open') as $open) {
                $path = static::makeCheckedPath($open->path);

                if (($open->method[0] === '*' || in_array($method, $open->method)) && Str::is(
                        url($path),
                        $url
                    )) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @return Collection|\Illuminate\Support\Collection|LtePermission[]
     */
    public static function now()
    {
        if (static::$now) {
            return static::$now;
        }

        $roles = LteAdmin::user()?->roles->pluck('id') ?? [];

        return static::$now = static::whereIn('lte_role_id', $roles)
            ->where('active', 1)
            ->get();
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

    /**
     * @return bool
     */
    public static function check()
    {
        if (!lte_user()) {
            return true;
        }

        if (request()->is(static::makeCheckedPath('profile*'))) {
            return true;
        }

        $result = !static::now()->where('path', '==', '*')->where('state', 'close')->first();

        $method = request()->ajax() && !request()->pjax() && request()->has('_exec') ? 'POST' : request()->getMethod();

        /** @var LtePermission $close */
        foreach (static::now()->where('state', 'close') as $close) {
            $path = static::makeCheckedPath($close->path);

            if (($close->method[0] === '*' || array_search(
                        $method,
                        $close->method
                    ) !== false) && request()->is($path)) {
                $result = false;
                break;
            }
        }

        if (!$result) {
            /** @var LtePermission $close */
            foreach (static::now()->where('state', 'open') as $open) {
                $path = static::makeCheckedPath($open->path);

                if (($open->method[0] === '*' || array_search(
                            $method,
                            $open->method
                        ) !== false) && request()->is($path)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @return HasOne
     */
    public function role()
    {
        return $this->hasOne(LteRole::class, 'id', 'lte_role_id');
    }
}
