<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all()->keyBy('name');

        $branches = [
            [
                'company_id' => $companies['TechCorp Solutions']->id ?? 1,
                'name' => 'Mumbai Head Office',
                'email' => 'mumbai.branch@techcorp.com',
                'contact_no' => '0229876541',
                'address_line1' => '101 Tech Park, BKC',
                'address_line2' => 'Bandra East',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'zip_code' => '400051',
                'status' => 'active',
            ],
            [
                'company_id' => $companies['Acme Global Enterprises']->id ?? 2,
                'name' => 'Bengaluru Tech Hub',
                'email' => 'blr.branch@acmeglobal.com',
                'contact_no' => '0809876542',
                'address_line1' => '45 Business Tower, MG Road',
                'address_line2' => 'Central District',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'zip_code' => '560001',
                'status' => 'active',
            ],
            [
                'company_id' => $companies['Apex HR Systems']->id ?? 3,
                'name' => 'Delhi Regional Office',
                'email' => 'delhi.branch@apexsystems.com',
                'contact_no' => '0119876543',
                'address_line1' => '12 Connaught Place',
                'address_line2' => 'Inner Circle',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'zip_code' => '110001',
                'status' => 'active',
            ],
            [
                'company_id' => $companies['Quantum Innovations']->id ?? 4,
                'name' => 'Hyderabad R&D Center',
                'email' => 'hyd.branch@quantuminnovations.com',
                'contact_no' => '0409876544',
                'address_line1' => '88 HITEC City Phase 2',
                'address_line2' => 'Cyberabad',
                'city' => 'Hyderabad',
                'state' => 'Telangana',
                'zip_code' => '500081',
                'status' => 'active',
            ],
            [
                'company_id' => $companies['Synergy Infotech']->id ?? 5,
                'name' => 'Pune Development Center',
                'email' => 'pune.branch@synergyinfo.com',
                'contact_no' => '0209876545',
                'address_line1' => '502 Hinjewadi IT Park',
                'address_line2' => 'Phase 1',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'zip_code' => '411057',
                'status' => 'active',
            ],
        ];

        foreach ($branches as $branchData) {
            Branch::updateOrCreate(
                ['email' => $branchData['email']],
                $branchData
            );
        }
    }
}
