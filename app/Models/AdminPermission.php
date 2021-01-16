<?php

namespace Admin\Models;

use Bfg\Dev\Traits\DumpedModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AdminPermission
 *
 * @package Admin\Models
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPermission makeDumpedModel()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPermission query()
 * @mixin \Eloquent
 * @property-read \Admin\Models\AdminRole|null $role
 * @property int $id
 * @property string $path
 * @property array $method
 * @property string $state
 * @property string|null $description
 * @property int $admin_role_id
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPermission whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPermission whereAdminRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPermission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPermission whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPermission wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPermission whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPermission whereUpdatedAt($value)
 */
class AdminPermission extends Model
{
    use DumpedModel;
    
    /**
     * @var string
     */
    protected $table = "admin_permission";

    /**
     * @var string[]
     */
    protected $fillable = [
        "path", "method", "state", "description", "admin_role_id", "active" // state: open, close
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'method' => 'array'
    ];

    /**
     * @var Collection
     */
    static $now;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function role()
    {
        return $this->hasOne(AdminRole::class, 'id', 'admin_role_id');
    }

    /**
     * @return Collection|\Illuminate\Support\Collection|AdminPermission[]
     */
    public static function now()
    {
        if (static::$now) {

            return static::$now;
        }

        $roles = admin() ? admin()->roles->pluck('id')->toArray() : [1];

        return static::$now = static::whereIn('admin_role_id', $roles)->where('active', 1)->get();
    }

    /**
     * @param  string  $url
     * @param  string  $method
     * @return bool
     */
    public static function checkUrl(string $url, string $method = 'GET')
    {
        $result = true;

        /** @var AdminPermission $close */
        foreach (static::now()->where('state', 'close') as $close) {

            $path = static::makeCheckedPath($close->path);

            if (($close->method[0] === '*' || array_search($method, $close->method) !== false) && \Str::is($path, $url)) {

                $result = false;
                break;
            }
        }

        if (!$result) {

            /** @var AdminPermission $close */
            foreach (static::now()->where('state', 'open') as $open) {

                $path = static::makeCheckedPath($open->path);

                if (($open->method[0] === '*' || array_search($method, $open->method) !== false) && \Str::is($path, $url)) {

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
    public static function checkCurrentPath()
    {
        if (!admin()) {

            return false;
        }

        return static::checkUrl(request()->decodedPath(), request()->getMethod());
    }

    /**
     * @param  string  $inner_path
     * @return string
     */
    public static function makeCheckedPath(string $inner_path)
    {
        $per_path = config('admin.route.lang_mode'. true) ? '*/' : '';

        return trim($per_path.config('admin.route.prefix'), '/').'/'.trim($inner_path, '/');
    }
}
