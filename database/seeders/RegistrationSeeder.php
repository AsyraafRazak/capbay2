<?php

namespace Database\Seeders;

use App\Models\Registration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable model event listeners for raw speed
        Registration::flushEventListeners();

        $totalRecords = 50000;
        $batchSize = 1000;
        $batches = $totalRecords / $batchSize;

        $this->command->info("Seeding {$totalRecords} registrations in {$batches} batches of {$batchSize}...");

        DB::beginTransaction();
        try {
            for ($i = 0; $i < $batches; $i++) {
                // Generate a batch of fake records as array data
                $records = Registration::factory()
                    ->count($batchSize)
                    ->make()
                    ->map(function ($registration) {
                        // Make sure timestamps are present for raw insert
                        $now = now();
                        $registration->created_at = $registration->created_at ?? $now;
                        $registration->updated_at = $registration->updated_at ?? $now;
                        return $registration->getAttributes();
                    })
                    ->toArray();

                Registration::insert($records);
            }
            DB::commit();
            $this->command->info("Successfully seeded {$totalRecords} registrations.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Seeding failed: " . $e->getMessage());
            throw $e;
        }
    }
}
