<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;

class SettingController extends Controller
{
    public function get(string $key)
    {
        $setting = Setting::where('key', $key)->first();
        if (!$setting) {
            return response()->json(['found' => false]);
        }
        return response()->json([
            'found' => true,
            'setting' => new SettingResource($setting),
        ]);
    }

    public function set(SetSettingRequest $request)
    {
        Setting::updateOrCreate(
            ['key' => $request->key],
            ['value' => $request->value]
        );

        return response()->noContent();
    }
}
