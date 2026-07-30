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
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('hra', 10, 2)->default(0);
            $table->decimal('conveyance_allowance', 10, 2)->default(0);
            $table->decimal('medical_allowance', 10, 2)->default(0);
            $table->decimal('special_allowance', 10, 2)->default(0);
            $table->decimal('other_allowance', 10, 2)->default(0);
            $table->decimal('variable_allowance', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2)->default(0);

            // Attendance Figures (Snapshot)
            $table->decimal('total_days', 5, 2)->default(0);
            $table->decimal('leave_taken', 5, 2)->default(0);
            $table->decimal('net_present', 5, 2)->default(0);
            $table->decimal('leave_not_deducted', 5, 2)->default(0);
            $table->decimal('payable_days', 5, 2)->default(0);

            // Per Day & Earned Calculation
            $table->decimal('per_day_salary', 10, 2)->default(0);
            $table->decimal('earned_salary', 10, 2)->default(0);

            // Deductions Breakdown (Snapshot)
            $table->decimal('employee_pf', 10, 2)->default(0);
            $table->decimal('esi', 10, 2)->default(0);
            $table->decimal('professional_tax', 10, 2)->default(0);
            $table->decimal('tds', 10, 2)->default(0);
            $table->decimal('other_deduction', 10, 2)->default(0);
            $table->decimal('total_deduction', 10, 2)->default(0);

            // Final Net Salary (Snapshot)
            $table->decimal('net_salary', 10, 2)->default(0);

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
