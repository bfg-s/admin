<?php

namespace Admin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Admin;
use Admin\Traits\DumpedModel;
use Str;

/**
 * Admin\Models\LtePermission.
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
 * @method static Builder|AdminPermission makeDumpedModel()
 * @method static Builder|AdminPermission newModelQuery()
 * @method static Builder|AdminPermission newQuery()
 * @method static Builder|AdminPermission query()
 * @method static Builder|AdminPermission whereActive($value)
 * @method static Builder|AdminPermission whereCreatedAt($value)
 * @method static Builder|AdminPermission whereDescription($value)
 * @method static Builder|AdminPermission whereId($value)
 * @method static Builder|AdminPermission whereLteRoleId($value)
 * @method static Builder|AdminPermission whereMethod($value)
 * @method static Builder|AdminPermission wherePath($value)
 * @method static Builder|AdminPermission whereState($value)
 * @method static Builder|AdminPermission whereUpdatedAt($value)
 * @mixin Eloquent
 */
class AdminPermission extends Model
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

        /** @var AdminPermission $close */
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
            /** @var AdminPermission $close */
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
     * @return Collection|\Illuminate\Support\Collection|AdminPermission[]
     */
    public static function now()
    {
        if (static::$now) {
            return static::$now;
        }

        $roles = Admin::user()?->roles->pluck('id') ?? [];

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

        return trim($per_path.config('admin.route.prefix'), '/').'/'.trim($inner_path, '/');
    }

    /**
     * @return bool
     */
    public static function check()
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
    public function role()
    {
        return $this->hasOne(AdminRole::class, 'id', 'lte_role_id');
    }
}
