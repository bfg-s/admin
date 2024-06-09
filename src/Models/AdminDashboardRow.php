<?php

declare(strict_types=1);

namespace Admin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * A model that is responsible for user dashboard rows in the admin panel.
 *
 * @property-read AdminUser|null $admin_user
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDashboardRow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDashboardRow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDashboardRow query()
 * @property int $id
 * @property int $admin_user_id
 * @property int $order
 * @property array|null $widgets
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDashboardRow whereAdminUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDashboardRow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDashboardRow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDashboardRow whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDashboardRow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDashboardRow whereWidgets($value)
 * @mixin Eloquent
 */
class AdminDashboardRow extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_dashboard_rows';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_user_id', 'order', 'widgets'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'admin_user_id' => 'integer',
        'order' => 'integer',
        'widgets' => 'array',
    ];

    /**
     * The relationships with the admin user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function admin_user(): HasOne
    {
        return $this->hasOne(AdminUser::class, 'id', 'admin_user_id');
    }

    /**
     * Get the number of columns for the widgets.
     *
     * @return int
     */
    public function getColsAttribute(): int
    {
        $count = count($this->widgets);

        if ($count === 1) {
            return 12;
        } elseif ($count === 2) {
            return 6;
        } elseif ($count === 3) {
            return 4;
        } elseif ($count === 4) {
            return 3;
        } elseif ($count === 6) {
            return 2;
        } elseif ($count === 12) {
            return 1;
        } else {
            return 12;
        }
    }
}
