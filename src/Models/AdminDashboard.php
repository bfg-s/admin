<?php

declare(strict_types=1);

namespace Admin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * A model that is responsible for user dashboards in the admin panel.
 *
 * @mixin Eloquent
 */
class AdminDashboard extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_dashboards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_user_id', 'name'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'admin_user_id' => 'integer',
        'name' => 'string',
    ];

    /**
     * The relationships with the admin user.
     *
     * @return HasOne
     */
    public function admin_user(): HasOne
    {
        return $this->hasOne(AdminUser::class, 'id', 'admin_user_id');
    }

    /**
     * The relationships with the rows of the dashboard.
     *
     * @return HasMany
     */
    public function rows(): HasMany
    {
        return $this->hasMany(AdminDashboardRow::class, 'admin_dashboard_id', 'id');
    }
}
