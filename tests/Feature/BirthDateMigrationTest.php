<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BirthDateMigrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_birth_date_column_is_available_after_migration(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'birth_date'));
        $this->assertSame('date', Schema::getColumnType('users', 'birth_date'));
    }

    public function test_birth_date_column_accepts_a_date(): void
    {
        $user = User::factory()->create();

        DB::table((new User)->getTable())
            ->where('id', $user->getKey())
            ->update(['birth_date' => '2000-01-15']);

        $this->assertSame('2000-01-15', DB::table((new User)->getTable())->where('id', $user->getKey())->value('birth_date'));
    }

    public function test_birth_date_column_accepts_null(): void
    {
        $user = User::factory()->create();

        DB::table((new User)->getTable())
            ->where('id', $user->getKey())
            ->update(['birth_date' => null]);

        $this->assertNull(DB::table((new User)->getTable())->where('id', $user->getKey())->value('birth_date'));
    }
}
