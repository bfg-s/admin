<?php

namespace Admin\Tests\Unit;

use Admin\Models\AdminLog;
use Admin\Tests\TestCase;

class HelpersTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_log_create(): void
    {
        admin_log('test log create', 'test details', 'fas fa-users');

        $model = AdminLog::latest()->first();

        $this->assertTrue($model->title == 'test log create');
        $this->assertTrue($model->detail == 'test details');
        $this->assertTrue($model->ip == '127.0.0.1');
        $this->assertTrue($model->url == config('app.url'));
        $this->assertTrue($model->method == 'GET');
        $this->assertTrue($model->user_agent == 'Symfony');
        $this->assertTrue($model->icon == 'fas fa-users');
    }

    public function test_admin_relative_path()
    {
        $this->assertTrue(admin_relative_path() == '/app/Admin');
        $this->assertTrue(admin_relative_path("with/my/path") == '/app/Admin/with/my/path');
    }

    public function test_admin_app_namespace()
    {
        $this->assertTrue(admin_app_namespace() == 'App\Admin');
        $this->assertTrue(admin_app_namespace("WithMyNamespace") == 'App\Admin\WithMyNamespace');
    }

    public function test_admin_related_methods()
    {
        $this->assertTrue(admin_related_methods('store') === ['store', 'create', 'access']);
        $this->assertTrue(admin_related_methods("update") === ['update', 'edit', 'access']);
        $this->assertTrue(admin_related_methods("create") === ['create', 'store', 'access']);
        $this->assertTrue(admin_related_methods("edit") === ['edit', 'update', 'access']);
        $this->assertTrue(admin_related_methods("destroy") === ['destroy', 'delete', 'access']);
        $this->assertTrue(admin_related_methods("delete") === ['delete', 'destroy', 'access']);
    }

    public function test_admin_app_path()
    {
        $this->assertTrue(str_ends_with(admin_app_path(), admin_relative_path()));
        $this->assertTrue(str_ends_with(admin_app_path('test'), admin_relative_path('test')));
    }

    public function test_admin_uri()
    {
        $this->assertTrue(admin_uri() == '/en/bfg');
        $this->assertTrue(admin_uri('test') == '/en/bfg/test');
    }

    public function test_admin_asset()
    {
        $this->assertTrue(admin_asset() == config('app.url') . '/admin');
        $this->assertTrue(admin_asset('test') == config('app.url') . '/admin/test');
    }

    public function test_version_string()
    {
        $this->assertTrue(admin_version_string('v1.2.3') == 'v1.2.<small>3</small>');
    }

    public function test_admin_make_url_with_params()
    {
        $this->assertTrue(admin_make_url_with_params('test', ['arr' => 1]) == 'test?arr=1');
    }

    public function test_admin_page()
    {
        $this->assertTrue(admin_page() instanceof \Admin\Page);
    }
}
