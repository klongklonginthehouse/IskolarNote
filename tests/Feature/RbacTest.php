<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_admin_can_open_the_admin_dashboard(): void
    {
        $this->actingAs($this->user('admin'))
            ->get('/admin')
            ->assertOk()
            ->assertSee('Administration');
    }

    public function test_student_is_denied_the_admin_dashboard_by_direct_url(): void
    {
        $this->actingAs($this->user('student'))
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_from_a_protected_url(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_student_cannot_change_another_users_role(): void
    {
        $victim = $this->user('student');

        $this->actingAs($this->user('student'))
            ->post("/admin/users/{$victim->id}/role", ['role' => 'admin'])
            ->assertForbidden();

        $this->assertSame('student', $victim->fresh()->role);
    }

    public function test_admin_can_change_another_users_role(): void
    {
        $target = $this->user('student');

        $this->actingAs($this->user('admin'))
            ->post("/admin/users/{$target->id}/role", ['role' => 'moderator'])
            ->assertRedirect();

        $this->assertSame('moderator', $target->fresh()->role);
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $admin = $this->user('admin');

        $this->actingAs($admin)
            ->post("/admin/users/{$admin->id}/role", ['role' => 'student'])
            ->assertRedirect();

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_review_queue_is_allowed_for_moderators_and_denied_for_students(): void
    {
        $this->actingAs($this->user('moderator'))->get('/review')->assertOk();
        $this->actingAs($this->user('student'))->get('/review')->assertForbidden();
        $this->actingAs($this->user('admin'))->get('/review')->assertOk();
    }

    public function test_student_cannot_submit_a_review_action(): void
    {
        $mod = $this->user('moderator');
        $dept = \App\Models\Department::create(['name' => 'Computer Science']);
        $resource = \App\Models\Resource::create([
            'user_id' => $mod->id,
            'department_id' => $dept->id,
            'title' => 'Pending reviewer notes',
            'course_code' => 'CS 12',
            'instructor' => 'Prof. Dela Cruz',
            'type' => 'reviewer',
            'status' => 'pending',
        ]);

        $this->actingAs($this->user('student'))
            ->post("/review/{$resource->id}/approve")
            ->assertForbidden();

        $this->assertSame('pending', $resource->fresh()->status);
    }

    public function test_public_registration_cannot_assign_an_admin_role(): void
    {
        $this->post('/register', [
            'name' => 'Mallory',
            'email' => 'mallory@iskolar.test',
            'password' => 'password123',
            'role' => 'admin',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'mallory@iskolar.test']);
    }
}
