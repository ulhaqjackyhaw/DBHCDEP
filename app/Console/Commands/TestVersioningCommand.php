<?php

namespace App\Console\Commands;

use App\Models\DataKaryawan;
use App\Models\Version;
use Illuminate\Console\Command;

class TestVersioningCommand extends Command
{
    protected $signature = 'test:versioning';
    protected $description = 'Test the versioning system';

    public function handle()
    {
        // Check data count
        $count = DataKaryawan::count();
        $this->info("Total DataKaryawan records: {$count}");
        
        // Show sample data
        $samples = DataKaryawan::take(3)->get(['nik', 'nama', 'gender']);
        $this->info("Sample records:");
        foreach ($samples as $sample) {
            $this->line("  {$sample->nik} - {$sample->nama} - {$sample->gender}");
        }
        
        // Check versions
        $versionCount = Version::count();
        $this->info("Total Version records: {$versionCount}");
        
        if ($versionCount > 0) {
            $versions = Version::withCount('history')->latest()->take(3)->get(['id', 'description', 'created_at']);
            $this->info("Recent versions:");
            foreach ($versions as $version) {
                $this->line("  {$version->id} - {$version->description} - {$version->history_count} karyawan - {$version->created_at}");
            }
        }
        
        // Test creating a new version (this will actually save all employee data to history)
        if ($this->option('no-interaction') || $this->confirm('Create a test version? This will backup all employee data.')) {
            $startTime = microtime(true);
            
            $version = Version::create([
                'description' => 'Test Version ' . now()->format('Y-m-d H:i:s')
            ]);
            
            // Count records that will be backed up
            $totalRecords = DataKaryawan::count();
            $this->info("Backing up {$totalRecords} employee records...");
            
            // Use the same chunking method as the controller
            $processedRecords = 0;
            DataKaryawan::query()->chunkById(500, function ($employees) use ($version, &$processedRecords) {
                $historyData = $employees->map(function ($employee) use ($version) {
                    $attributes = $employee->getAttributes();
                    $attributes['version_id'] = $version->id;
                    unset($attributes['id']);
                    $attributes['created_at'] = now();
                    $attributes['updated_at'] = now();
                    return $attributes;
                });
                
                \App\Models\EmployeeHistory::insert($historyData->toArray());
                $processedRecords += $historyData->count();
                $this->line("  Processed {$processedRecords} records...");
            });
            
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);
            
            // Reload to get the count
            $version = $version->fresh();
            $actualCount = $version->history()->count();
            
            $this->info("✅ Created version: {$version->description}");
            $this->info("📊 Backed up {$actualCount} employee records");
            $this->info("⏱️  Time taken: {$duration} seconds");
        }
    }
}
