<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function createAppSetting(Request $request)
    {
        // Validate input
        // $request->validate([
        //     'is_maintenance_on' => 'required|boolean',
        // ]);

        // Ensure only one record exists
        $existingSetting = AppSetting::first();

        if ($existingSetting) {
            return response()->json([
                'status' => false,
                'message' => 'App setting record already exists.',
            ], 400);
        }

        // Create new record
        $setting = AppSetting::create([
            'is_maintenance_on' => false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'App setting record created successfully.',
            // 'data' => $setting,
        ], 201);
    }

    /**
     * Update an existing App Setting record.
     */
    public function updateAppSetting(Request $request)
    {
        // Validate input
        $request->validate([
            'is_maintenance_on' => 'required|boolean',
        ]);

        // Find the first AppSetting record
        $setting = AppSetting::first();

        if (!$setting) {
            return response()->json([
                'status' => false,
                'message' => 'No app setting record found. Please create one first.',
            ]);
        }

        // Update the setting
        $setting->is_maintenance_on = $request->is_maintenance_on;
        $setting->save();

        return response()->json([
            'status' => true,
            'message' => 'App setting updated successfully.',
            // 'data' => $setting,
        ]);
    }
}
