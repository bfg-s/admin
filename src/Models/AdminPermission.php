<?php

declare(strict_types=1);

namespace Admin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Admin;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $path
 * @property array $method
 * @property string $state
 * @property string|null $description
 * @property int $admin_role_id
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Admin\Models\AdminRole|null $role
 * @method static Builder|AdminPermission makeDumpedModel()
 * @method static Builder|AdminPermission newModelQuery()
 * @method static Builder|AdminPermission newQuery()
 * @method static Builder|AdminPermission query()
 * @method static Builder|AdminPermission whereActive($value)
 * @method static Builder|AdminPermission whereCreatedAt($value)
 * @method static Builder|AdminPermission whereDescription($value)
 * @method static Builder|AdminPermission whereId($value)
 * @method static Builder|AdminPermission whereAdminRoleId($value)
 * @method static Builder|AdminPermission whereMethod($value)
 * @method static Builder|AdminPermission wherePath($value)
 * @method static Builder|AdminPermission whereState($value)
 * @method static Builder|AdminPermission whereUpdatedAt($value)
 * @mixin Eloquent
 */
class AdminPermission extends Model
{
    /**
     * @var Collection
     */
    public static $now;
    /**
     * @var string
     */
    protected $table = 'admin_permission';
    /**
     * @var string[]
     */
    protected $fillable = [
        'path', 'method', 'state', 'description', 'admin_role_id', 'active', // state: open, close
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

        /** @var AdminPermission $close */
        foreach (static::now()->where('state', 'close') as $close) {
            $path = ltrim(static::makeCheckedPath($close->path), '/');

            if (($close->method[0] === '*' || in_array($method, $close->method)) && Str::is(
                    url($path),
                    $url
                )) {
                $result = false;
                break;
            }
        }

        if (!$result) {

            /** @var AdminPermission $close */
            foreach (static::now()->where('state', 'open') as $open) {
                $path = ltrim(static::makeCheckedPath($open->path), '/');

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
     * @return mixed
     */
    public static function now(): mixed
    {
        if (static::$now) {
            return static::$now;
        }

        $roles = Admin::user()?->roles->pluck('id') ?? [];

        return static::$now = static::whereIn('admin_role_id', $roles)
            ->where('active', 1)
            ->get();
    }

    /**
     * @param  string  $inner_path
     * @return string
     */
    public static function makeCheckedPath(string $inner_path): string
    {
        $per_path = config('layout.lang_mode') ? '*/' : '';

        return ltrim(trim($per_path.config('admin.route.prefix'), '/').'/'.trim($inner_path, '/'), '/');
    }

    /**
     * @return bool
     */
    public static function check(): bool
    {
        if (!admin_user()) {
            return true;
        }

        if (request()->is(static::makeCheckedPath('profile*'))) {
            return true;
        }

        $result = !static::now()->where('path', '==', '*')->where('state', 'close')->first();

        $method = request()->ajax() && !request()->pjax() && request()->has('_exec') ? 'POST' : request()->getMethod();

        /** @var AdminPermission $close */
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
            /** @var AdminPermission $close */
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
    public function role(): HasOne
    {
        return $this->hasOne(AdminRole::class, 'id', 'admin_role_id');
    }
}
