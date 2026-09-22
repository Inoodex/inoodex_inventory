<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    /**
     * Display the system settings management page.
     */
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('frontend.pages.settings.index', compact('settings'));
    }

    /**
     * Update system settings and uploaded media assets.
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name'        => 'required|string|max:255',
            'site_tagline'     => 'nullable|string|max:255',
            'footer_text'      => 'nullable|string|max:255',
            'contact_email'    => 'nullable|email|max:255',
            'contact_phone'    => 'nullable|string|max:255',
            'address'          => 'nullable|string',
            'currency_symbol'  => 'nullable|string|max:10',
            'currency_code'    => 'nullable|string|max:10',
            'date_format'      => 'nullable|string|max:20',
            'timezone'         => 'nullable|string|max:50',
            'site_logo'        => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'site_logo_white'  => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'login_logo'       => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'favicon'          => 'nullable|mimes:jpeg,png,jpg,webp,svg,ico|max:1024',
        ]);

        $uploadPath = public_path('uploads/settings');
        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0777, true, true);
        }

        // 1. Process Media Uploads
        $imageFields = [
            'site_logo'       => 'logo_main_',
            'site_logo_white' => 'logo_white_',
            'login_logo'      => 'logo_login_',
            'favicon'         => 'favicon_',
        ];

        foreach ($imageFields as $field => $prefix) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $oldValue = Setting::get($field);

                // Remove previous uploaded custom file (if stored in uploads/settings)
                if ($oldValue && str_starts_with($oldValue, 'uploads/settings/') && file_exists(public_path($oldValue))) {
                    @unlink(public_path($oldValue));
                }

                $fileName = time() . '_' . $prefix . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $fileName);

                Setting::set($field, 'uploads/settings/' . $fileName, 'media', 'image');
            }
        }

        // 2. Process General Text Settings
        $textFields = [
            'site_name'       => ['general', 'text'],
            'site_tagline'    => ['general', 'text'],
            'footer_text'     => ['general', 'text'],
            'contact_email'   => ['general', 'text'],
            'contact_phone'   => ['general', 'text'],
            'address'         => ['general', 'textarea'],
            'currency_symbol' => ['localization', 'text'],
            'currency_code'   => ['localization', 'text'],
            'date_format'     => ['localization', 'text'],
            'timezone'        => ['localization', 'text'],
        ];

        foreach ($textFields as $key => [$group, $type]) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key), $group, $type);
            }
        }

        return redirect()->route('settings.index')
            ->with('success', 'System settings and branding updated successfully.');
    }
}
