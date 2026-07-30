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
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            $table->decimal('basic_salary')->nullable();

            $table->decimal('variable_allowance')->nullable();
            $table->decimal('hra')->nullable();
            $table->decimal('conveyance_allowance')->nullable();
            $table->decimal('medical_allowance')->nullable();
            $table->decimal('special_allowance')->nullable();
            $table->decimal('other_allowance')->nullable();

            $table->decimal('employee_pf')->nullable();
            $table->decimal('esi')->nullable();
            $table->decimal('professional_tax')->nullable();
            $table->decimal('tds')->nullable();
            $table->decimal('other_deduction')->nullable();

            $table->decimal('gross_salary')->nullable();
            $table->decimal('total_deduction')->nullable();
            $table->decimal('net_salary')->nullable();

            $table->date('effective_from');
            $table->date('effective_to')->nullable();

            $table->string('status')->default('active');

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
        Schema::dropIfExists('employee_salaries');
    }
};
