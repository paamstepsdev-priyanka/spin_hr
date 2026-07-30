<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'TechCorp Solutions',
                'email' => 'info@techcorp.com',
                'contact_no' => '9876543211',
                'address_line1' => '101 Tech Park, BKC',
                'address_line2' => 'Bandra East',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'zip_code' => '400051',
                'pf_applicable' => 'YES',
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'Acme Global Enterprises',
                'email' => 'contact@acmeglobal.com',
                'contact_no' => '9876543212',
                'address_line1' => '45 Business Tower, MG Road',
                'address_line2' => 'Central District',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'zip_code' => '560001',
                'pf_applicable' => 'YES',
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'Apex HR Systems',
                'email' => 'support@apexsystems.com',
                'contact_no' => '9876543213',
                'address_line1' => '12 Connaught Place',
                'address_line2' => 'Inner Circle',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'zip_code' => '110001',
                'pf_applicable' => 'NO',
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'Quantum Innovations',
                'email' => 'hello@quantuminnovations.com',
                'contact_no' => '9876543214',
                'address_line1' => '88 HITEC City Phase 2',
                'address_line2' => 'Cyberabad',
                'city' => 'Hyderabad',
                'state' => 'Telangana',
                'zip_code' => '500081',
                'pf_applicable' => 'YES',
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'Synergy Infotech',
                'email' => 'admin@synergyinfo.com',
                'contact_no' => '9876543215',
                'address_line1' => '502 Hinjewadi IT Park',
                'address_line2' => 'Phase 1',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'zip_code' => '411057',
                'pf_applicable' => 'YES',
                'status' => 'ACTIVE',
            ],
        ];

        foreach ($companies as $companyData) {
            Company::updateOrCreate(
                ['email' => $companyData['email']],
                $companyData
            );
        }
    }
}
