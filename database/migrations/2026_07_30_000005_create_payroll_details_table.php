<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payroll_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            // Earnings Breakdown (Snapshot)
            $table->decimal('basic_salary')->nullable();
            $table->decimal('hra')->nullable();
            $table->decimal('conveyance_allowance')->nullable();
            $table->decimal('medical_allowance')->nullable();
            $table->decimal('special_allowance')->nullable();
            $table->decimal('other_allowance')->nullable();
            $table->decimal('variable_allowance')->nullable();
            $table->decimal('gross_salary')->nullable();

            // Attendance Figures (Snapshot)
            $table->decimal('total_days')->nullable();
            $table->decimal('leave_taken')->nullable();
            $table->decimal('net_present')->nullable();
            $table->decimal('leave_not_deducted')->nullable();
            $table->decimal('payable_days')->nullable();

            // Per Day & Earned Calculation
            $table->decimal('per_day_salary')->nullable();
            $table->decimal('earned_salary')->nullable();

            // Deductions Breakdown (Snapshot)
            $table->decimal('employee_pf')->nullable();
            $table->decimal('esi')->nullable();
            $table->decimal('professional_tax')->nullable();
            $table->decimal('tds')->nullable();
            $table->decimal('other_deduction')->nullable();
            $table->decimal('total_deduction')->nullable();

            // Final Net Salary (Snapshot)
            $table->decimal('net_salary')->nullable();

            $table->string('status')->default('Generated');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_details');
    }
};
