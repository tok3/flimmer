<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\LeadSource;
use Faker\Factory as Faker;
use App\Models\Tag;
use App\Models\PipelineStage;
use App\Models\CustomField;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@admin.com',
        ]);

        //-------------------------------------------------------------
/*
        $germanFaker = Faker::create('de_DE');
        Customer::factory()
            ->count(10)
            ->state(function (array $attributes) use ($germanFaker) {
                $fn = $germanFaker->firstName;
                $ln = $germanFaker->lastName;

                return [
                    'first_name' => $fn,
                    'last_name' => $ln,
                    'email' => strtolower($fn.'.'.$ln.'@'.$germanFaker->domainName),
                    // Add other fields as needed...
                ];
            })
            ->create();*/

        //-------------------------------------------------------------

        $leadSources = [
            'Website',
            'Online AD',
            'Twitter',
            'LinkedIn',
            'Webinar',
            'Trade Show',
            'Referral',
        ];

        foreach ($leadSources as $leadSource) {
            LeadSource::create(['name' => $leadSource]);
        }

        //-------------------------------------------------------------

        $tags = [
            'Priority',
            'VIP'
        ];

        foreach ($tags as $tag) {
            Tag::create(['name' => $tag]);
        }

        //-------------------------------------------------------------
        //$this->call(PipelineStageSeeder::class);
        $pipelineStages = [
            [
                'name' => 'Lead',
                'position' => 1,
                'is_default' => true,
            ],
            [
                'name' => 'Contact Made',
                'position' => 2,
            ],
            [
                'name' => 'Proposal Made',
                'position' => 3,
            ],
            [
                'name' => 'Proposal Rejected',
                'position' => 4,
            ],
            [
                'name' => 'Customer',
                'position' => 5,
            ]
        ];

        foreach ($pipelineStages as $stage) {
            PipelineStage::create($stage);
        }

        $defaultPipelineStage = PipelineStage::where('is_default', true)->first()->id;
        Customer::factory()->count(10)->create([
            'pipeline_stage_id' => $defaultPipelineStage,
        ]);

        //-------------------------------------------------------------
        $customFields = [
            'Birth Date',
            'Company',
            'Job Title',
            'Family Members',
        ];

        foreach ($customFields as $customField) {
            CustomField::create(['name' => $customField]);
        }

    }
}
