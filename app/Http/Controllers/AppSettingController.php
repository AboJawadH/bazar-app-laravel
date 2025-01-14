<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function createAppSetting(Request $request)
    {
        $validatedData =  $request->validate([
            'is_maintenance_on' => 'required|boolean',
            'maintenance_message' => 'nullable|string',
            'build_number' => 'nullable|string',
            'update_type' => 'nullable|string',
            'font_type' => 'nullable|string',
            'day_color' => 'nullable|string',
            'night_color' => 'nullable|string',
        ]);

        // Ensure only one record exists
        $setting = AppSetting::first();

        if ($setting) {
            // Update the existing record
            $setting->update($validatedData);
            $message = 'App setting record updated successfully.';
        } else {
            // Create a new record
            $setting = AppSetting::create($validatedData);
            $message = 'App setting record created successfully.';
        }

        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $setting,
        ], 200);
    }

    /**
     * Update an existing App Setting record.
     */
    public function checkForUpdate(Request $request)
    {
        // Validate that the version number is provided
        $validatedData = $request->validate([
            'version_number' => 'required|string',
        ]);

        $currentVersion = $validatedData['version_number'];
        $appSettings = AppSetting::first();

        // If no settings exist, return 'no_update'
        if (!$appSettings || !$appSettings->build_number) {
            return response()->json([
                'status' => true,
                'update_type' => 'no_update',
            ], 200);
        }

        $latestVersion = $appSettings->build_number;
        $updateType = $appSettings->update_type; // 'mandatory' or 'optional'

        // Compare versions
        if (version_compare($currentVersion, $latestVersion, '>=')) {
            // The app is up-to-date
            return response()->json([
                'status' => true,
                'update_type' => 'no_update',
                'latest_version' => $latestVersion,
            ], 200);
        }

        // If the version is outdated, return the update type
        return response()->json([
            'status' => true,
            'update_type' => $updateType,
            'latest_version' => $latestVersion,
        ], 200);
    }
}
