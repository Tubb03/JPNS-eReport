<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportImage;
use App\Models\User;
use Illuminate\Http\Request;

class MigrationController extends Controller
{
    public function importFirebase(Request $request)
    {
        $data = $request->validate([
            'reports' => 'required|array',
        ]);

        $count = 0;

        foreach ($data['reports'] as $fb) {
            // Find or dynamically create a user mapped to their old name
            $name = $fb['name'] ?? 'Unknown Staff';
            $email = strtolower(str_replace(' ', '.', $name)) . '@example.com';
            
            $user = User::firstOrCreate(
                ['name' => $name],
                [
                    'email' => $email,
                    'password' => bcrypt('password123'), // Temporary default password for generic users
                    'role' => 'staff'
                ]
            );

            // Create the record
            $timestampMs = $fb['createdAt'] ?? round(microtime(true) * 1000);
            $createdAt = date('Y-m-d H:i:s', $timestampMs / 1000);

            $report = Report::create([
                'user_id' => $user->id,
                'unit' => $fb['unit'] ?? 'Unknown Unit',
                'program_name' => $fb['program'] ?? 'Unknown Program',
                'program_date' => $fb['date'] ?? date('Y-m-d'),
                'description' => $fb['description'] ?? '',
                'objective' => $fb['objective'] ?? '',
                'full_report' => $fb['fullReport'] ?? '',
                'created_at' => $createdAt,
                'updated_at' => $createdAt
            ]);

            // Map and store physical hotlinks
            if (!empty($fb['imageUrls'])) {
                foreach ((array) $fb['imageUrls'] as $url) {
                    ReportImage::create([
                        'report_id' => $report->id,
                        'image_path' => $url
                    ]);
                }
            }
            $count++;
        }

        return response()->json(['success' => true, 'migrated_records' => $count]);
    }
}
