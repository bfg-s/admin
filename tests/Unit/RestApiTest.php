<?php

namespace Admin\Tests\Unit;

use Admin\Models\AdminPermission;
use Admin\Models\AdminRole;
use Admin\Models\AdminUser;
use Admin\Repositories\AdminRepository;
use Admin\Tests\TestCase;

class RestApiTest extends TestCase
{
    protected function getBearerToken()
    {
        return $this->post(route('admin.login'), [
            'login' => 'root',
            'password' => 'root',
        ], [
            'Content-Api' => '1',
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'status', 'message', 'bearer', 'query', 'data', 'respond', 'version'
            ])
            ->json('bearer');
    }

    protected function headers(bool $layout = true): array
    {
        AdminRepository::clearCache();

        return [
            'Content-Api' => 1,
            'Content-Layout' => $layout ? 1 : 0,
            'Authorization' => 'Bearer ' . $this->getBearerToken(),
        ];
    }

    public function test_bfg_info_request()
    {
        $this->get(route('admin.info'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'status', 'prefix', 'dark', 'langMode', 'languages'
            ]);
    }

    public function test_can_login_like_root_request()
    {
        $result = $this->post(route('admin.login'), [
            'login' => 'root',
            'password' => 'root',
        ], [
            'Content-Api' => '1',
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'status', 'message', 'bearer', 'query', 'data', 'respond', 'version'
            ])
            ->json();

        $this->assertTrue(($result['status'] ?? null) === 'success');
        $this->assertTrue(is_string($result['bearer'] ?? null));
    }

    public function test_can_login_like_admin_request()
    {
        $result = $this->post(route('admin.login'), [
            'login' => 'admin',
            'password' => 'admin',
        ], [
            'Content-Api' => '1',
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'status', 'message', 'bearer', 'query', 'data', 'respond', 'version'
            ])
            ->json();

        $this->assertTrue(($result['status'] ?? null) === 'success');
        $this->assertTrue(is_string($result['bearer'] ?? null));
    }

    public function test_can_login_like_moderator_request()
    {
        $result = $this->post(route('admin.login'), [
            'login' => 'moderator',
            'password' => 'moderator',
        ], [
            'Content-Api' => '1',
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'status', 'message', 'bearer', 'query', 'data', 'respond', 'version'
            ])
            ->json();

        $this->assertTrue(($result['status'] ?? null) === 'success');
        $this->assertTrue(is_string($result['bearer'] ?? null));
    }

    public function test_dashboard_request()
    {
        $this->get(route('admin.dashboard'), $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'menu', 'buttonGroups', 'contents', 'query', 'data', 'respond', 'version'
            ]);
    }

    public function test_dashboard_without_layout_request()
    {
        $this->get(route('admin.dashboard'), $this->headers(false))
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'query', 'data', 'respond', 'version'
            ]);
    }

    public function test_administrator_list_request()
    {
        $users = $this->get(route('admin.administration.admin_user.index'), $this->headers(false))
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'query', 'data', 'respond', 'version'
            ])->json('data.adminuser10.data');

        foreach ($users as $user) {
            $this->assertTrue(AdminUser::find($user['id']) !== null);
        }

        $contents = $this->get(route('admin.administration.admin_user.index'), $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'menu', 'buttonGroups', 'contents', 'query', 'data', 'respond', 'version'
            ])->json('contents');

        $this->assertTrue($contents[0]['component'] === 'Admin\Components\CardComponent');
    }

    public function test_administrator_show_request()
    {
        $user = AdminUser::first();

        $data = $this->get(route('admin.administration.admin_user.show', $user->id), $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'menu', 'buttonGroups', 'contents', 'query', 'data', 'respond', 'version'
            ])->json('data.adminuser3');

        $this->assertTrue($data['id'] === $user->id);
        $this->assertTrue($data['login'] === $user->login);
        $this->assertTrue($data['email'] === $user->email);
        $this->assertTrue($data['name'] === $user->name);
        $this->assertTrue($data['avatar'] === $user->avatar);
    }

    public function test_administrator_form_create_request()
    {
        $data = $this->get(route('admin.administration.admin_user.create'), $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'menu', 'buttonGroups', 'contents', 'query', 'data', 'respond', 'version'
            ])->json('contents.0.component');

        $this->assertTrue($data === 'Admin\Components\CardComponent');
    }

    public function test_administrator_form_edit_request()
    {
        $user = AdminUser::first();

        $data = $this->get(route('admin.administration.admin_user.edit', $user->id), $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'menu', 'buttonGroups', 'contents', 'query', 'data', 'respond', 'version'
            ])->json();

        $this->assertTrue($data['contents'][0]['component'] === 'Admin\Components\CardComponent');

        $this->assertTrue($data['data']['model']['id'] === $user->id);
        $this->assertTrue($data['data']['model']['login'] === $user->login);
        $this->assertTrue($data['data']['model']['email'] === $user->email);
        $this->assertTrue($data['data']['model']['name'] === $user->name);
        $this->assertTrue($data['data']['model']['avatar'] === $user->avatar);
    }

    public function test_administrator_update_request()
    {
        $user = AdminUser::first();
        $userOrigin = clone $user;

        $result = $this->put(route('admin.administration.admin_user.update', $user->id), [
            'login' => fake()->slug,
            'name' => fake()->name,
            'email' => fake()->email,
        ], $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'status', 'message', 'query', 'data', 'respond', 'version'
            ])->json();

        $this->assertTrue($result['status'] === "success");

        $user->refresh();

        $this->assertTrue($result['data']['model']['id'] === $user->id);
        $this->assertTrue($result['data']['model']['login'] === $user->login);
        $this->assertTrue($result['data']['model']['name'] === $user->name);
        $this->assertTrue($result['data']['model']['email'] === $user->email);

        $user->update(
            $userOrigin->only('login', 'name', 'email')
        );
    }

    public function test_administrator_create_request()
    {
        $result = $this->post(route('admin.administration.admin_user.store'), [
            'login' => fake()->slug,
            'name' => fake()->name,
            'email' => fake()->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ], $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'status', 'message', 'query', 'data', 'respond', 'version'
            ])->json();

        $this->assertTrue($result['status'] === "success");

        $user = AdminUser::where('login', $result['data']['model']['login'])->first();

        $this->assertTrue($user !== null);

        $user->delete();
    }

    public function test_administrator_delete_request()
    {
        $user = AdminUser::create([
            'login' => fake()->slug,
            'name' => fake()->name,
            'email' => fake()->email,
            'password' => 'password',
        ]);

        $result = $this->delete(route('admin.administration.admin_user.destroy', $user->id), [], $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'status', 'message', 'query', 'data', 'respond', 'version'
            ])->json();

        $this->assertTrue($result['status'] === "success");

        $this->assertTrue(AdminUser::find($user->id) === null);
    }

    public function test_role_list_request()
    {
        $roles = $this->get(route('admin.administration.admin_role.index'), $this->headers(false))
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'query', 'data', 'respond', 'version'
            ])->json('data.adminrole9.data');

        foreach ($roles as $role) {
            $this->assertTrue(AdminRole::find($role['id']) !== null);
        }

        $contents = $this->get(route('admin.administration.admin_role.index'), $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'menu', 'buttonGroups', 'contents', 'query', 'data', 'respond', 'version'
            ])->json('contents');

        $this->assertTrue($contents[0]['component'] === 'Admin\\Components\\CardComponent');
    }

    public function test_role_show_request()
    {
        $role = AdminRole::first();

        $data = $this->get(route('admin.administration.admin_role.show', $role->id), $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'menu', 'buttonGroups', 'contents', 'query', 'data', 'respond', 'version'
            ])->json('data.adminrole3');

        $this->assertTrue($data['id'] === $role->id);
        $this->assertTrue($data['name'] === $role->name);
        $this->assertTrue($data['slug'] === $role->slug);
    }

    public function test_role_form_create_request()
    {
        $data = $this->get(route('admin.administration.admin_role.create'), $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'menu', 'buttonGroups', 'contents', 'query', 'data', 'respond', 'version'
            ])->json('contents.0.component');

        $this->assertTrue($data === 'Admin\Components\CardComponent');
    }

    public function test_role_form_update_request()
    {
        $role = AdminRole::first();

        $data = $this->get(route('admin.administration.admin_role.edit', $role->id), $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'menu', 'buttonGroups', 'contents', 'query', 'data', 'respond', 'version'
            ])->json();

        $this->assertTrue($data['contents'][0]['component'] === 'Admin\Components\CardComponent');

        $this->assertTrue($data['data']['model']['id'] === $role->id);
        $this->assertTrue($data['data']['model']['name'] === $role->name);
        $this->assertTrue($data['data']['model']['slug'] === $role->slug);
    }

    public function test_role_update_request()
    {
        $role = AdminRole::first();
        $roleOrigin = clone $role;

        $result = $this->put(route('admin.administration.admin_role.update', $role->id), [
            'name' => fake()->name,
            'slug' => fake()->slug,
        ], $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'status', 'message', 'query', 'data', 'respond', 'version'
            ])->json();

        $this->assertTrue($result['status'] === "success");

        $role->refresh();

        $this->assertTrue($result['data']['model']['id'] === $role->id);
        $this->assertTrue($result['data']['model']['name'] === $role->name);
        $this->assertTrue($result['data']['model']['slug'] === $role->slug);

        $role->update(
            $roleOrigin->only('name', 'slug')
        );
    }

    public function test_role_create_request()
    {
        $result = $this->post(route('admin.administration.admin_role.store'), [
            'name' => fake()->name,
            'slug' => fake()->slug,
        ], $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'status', 'message', 'query', 'data', 'respond', 'version'
            ])->json();

        $this->assertTrue($result['status'] === "success");

        $role = AdminRole::where('slug', $result['data']['model']['slug'])->first();

        $this->assertTrue($role !== null);

        $role->delete();
    }

    public function test_role_delete_request()
    {
        $role = AdminRole::create([
            'name' => fake()->name,
            'slug' => fake()->slug,
        ]);

        $result = $this->delete(route('admin.administration.admin_role.destroy', $role->id), [], $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'status', 'message', 'query', 'data', 'respond', 'version'
            ])->json();

        $this->assertTrue($result['status'] === "success");

        $this->assertTrue(AdminRole::find($role->id) === null);
    }

    public function test_permission_list_request()
    {
        $contents = $this->get(route('admin.administration.admin_permission.index'), $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'menu', 'buttonGroups', 'contents', 'query', 'data', 'respond', 'version'
            ])->json('contents');

        $this->assertTrue($contents[0]['component'] === 'Admin\\Components\\CardComponent');
    }

    public function test_permission_show_request()
    {
        $data = $this->get(route('admin.administration.admin_permission.show', 1), $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'menu', 'buttonGroups', 'contents', 'query', 'data', 'respond', 'version'
            ])->json('data.adminpermission3');

        $this->assertTrue($data['id'] === 1);
        $this->assertTrue($data['path'] === 'admin*');
    }

    public function test_permission_form_create_request()
    {
        $data = $this->get(route('admin.administration.admin_permission.create'), $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'menu', 'buttonGroups', 'contents', 'query', 'data', 'respond', 'version'
            ])->json('contents.0.component');

        $this->assertTrue($data === 'Admin\\Components\\CardComponent');
    }

    public function test_permission_form_update_request()
    {
        $data = $this->get(route('admin.administration.admin_permission.edit', 1), $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'menu', 'buttonGroups', 'contents', 'query', 'data', 'respond', 'version'
            ])->json();

        $this->assertTrue($data['contents'][0]['component'] === 'Admin\\Components\\CardComponent');

        $this->assertTrue($data['data']['model']['id'] === 1);
        $this->assertTrue($data['data']['model']['path'] === 'admin*');
    }

    public function test_permission_update_request()
    {
        $permission = AdminPermission::first();
        $permissionOrigin = clone $permission;

        $result = $this->put(route('admin.administration.admin_permission.update', $permission->id), [
            'path' => fake()->slug,
            'method' => ['GET'],
            'state' => 'open',
            'admin_role_id' => 1,
        ], $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'status', 'message', 'query', 'data', 'respond', 'version'
            ])->json();

        $this->assertTrue($result['status'] === "success");
        $this->assertTrue($result['data']['model']['id'] === $permission->id);

        $permission->refresh();

        $this->assertTrue($result['data']['model']['path'] === $permission->path);
        $this->assertTrue($result['data']['model']['method'] === $permission->method);
        $this->assertTrue($result['data']['model']['state'] === $permission->state);
        $this->assertTrue($result['data']['model']['admin_role_id'] === $permission->admin_role_id);

        $permission->update(
            $permissionOrigin->only('path', 'method', 'state', 'admin_role_id')
        );
    }

    public function test_permission_create_request()
    {
        $result = $this->post(route('admin.administration.admin_permission.store'), [
            'path' => fake()->slug,
            'method' => ['GET'],
            'state' => 'open',
            'admin_role_id' => 1,
        ], $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'status', 'message', 'query', 'data', 'respond', 'version'
            ])->json();

        $this->assertTrue($result['status'] === "success");

        $permission = AdminPermission::where('path', $result['data']['model']['path'])->first();

        $this->assertTrue($permission !== null);

        $permission->delete();
    }

    public function test_permission_delete_request()
    {
        $permission = AdminPermission::create([
            'path' => fake()->slug,
            'method' => ['GET'],
            'state' => 'open',
            'admin_role_id' => 1,
        ]);

        $result = $this->delete(route('admin.administration.admin_permission.destroy', $permission->id), [], $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'status', 'message', 'query', 'data', 'respond', 'version'
            ])->json();

        $this->assertTrue($result['status'] === "success");

        $this->assertTrue(AdminPermission::find($permission->id) === null);
    }

    public function test_user_profile_request()
    {
        $user = AdminUser::first();

        $data = $this->get(route('admin.profile'), $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'meta', 'menu', 'buttonGroups', 'contents', 'query', 'data', 'respond', 'version'
            ])->json('data.model');

        $this->assertTrue($data['id'] === $user->id);
        $this->assertTrue($data['login'] === $user->login);
        $this->assertTrue($data['email'] === $user->email);
        $this->assertTrue($data['name'] === $user->name);
        $this->assertTrue($data['avatar'] === $user->avatar);
    }
}
