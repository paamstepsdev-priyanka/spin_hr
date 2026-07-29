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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');

            $table->string('employment_type')->nullable();
            $table->string('designation')->nullable();
            $table->string('employee_code')->unique();
            $table->string('reporting_to')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('work_phone1')->nullable();
            $table->string('work_phone2')->nullable();
            $table->string('cell_phone')->nullable();
            $table->string('name');
            $table->string('father_name')->nullable();
            $table->string('email')->unique();
            $table->string('mobile')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('accommodation_type')->nullable();
            $table->string('rent_paid_by_company')->nullable();
            $table->string('property_owner_name')->nullable();
            $table->string('property_owner_contact')->nullable();
            $table->string('national_rent')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('aadhar_no')->nullable();
            $table->string('photo')->nullable();
            $table->string('pan_card')->nullable();
            $table->string('aadhar_card')->nullable();
            $table->string('cancelled_cheque')->nullable();
            $table->string('resume')->nullable();
            $table->string('relationship')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('primary_phone')->nullable();
            $table->string('alternative_phone')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('account_no')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch_name')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
