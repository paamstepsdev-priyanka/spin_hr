<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->boolean('is_default')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['user_id', 'company_id']);
        });

        // Seed initial user_companies mappings from existing users and employees
        $activeCompanies = DB::table('companies')->where('status', 'active')->pluck('id');
        if ($activeCompanies->isNotEmpty()) {
            $firstCompanyId = $activeCompanies->first();

            // 1. Link employees to their company
            $employees = DB::table('employees')->whereNotNull('user_id')->get();
            foreach ($employees as $emp) {
                if ($emp->user_id && $emp->company_id) {
                    DB::table('user_companies')->updateOrInsert(
                        ['user_id' => $emp->user_id, 'company_id' => $emp->company_id],
                        ['is_default' => true, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }

            // 2. Link admin users to active companies
            $adminUsers = DB::table('users')->whereIn('role', ['admin', 'super_admin'])->get();
            foreach ($adminUsers as $admin) {
                foreach ($activeCompanies as $index => $compVal) {
                    DB::table('user_companies')->updateOrInsert(
                        ['user_id' => $admin->id, 'company_id' => $compVal],
                        ['is_default' => ($index === 0), 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_companies');
    }
};
