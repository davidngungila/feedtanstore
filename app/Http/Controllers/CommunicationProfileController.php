<?php

namespace App\Http\Controllers;

use App\Models\CommunicationProfile;
use Illuminate\Http\Request;

class CommunicationProfileController extends Controller
{
    public function index()
    {
        $profiles = CommunicationProfile::latest()->get();
        return view('system.communication-profiles', compact('profiles'));
    }

    public function create()
    {
        return view('system.communication-profiles-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:email,sms',
            'is_active' => 'boolean',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|in:tls,ssl,none',
            'email_from_address' => 'nullable|email|max:255',
            'email_from_name' => 'nullable|string|max:255',
            'sms_provider' => 'nullable|string|max:255',
            'sms_api_key' => 'nullable|string|max:255',
            'sms_api_secret' => 'nullable|string|max:255',
            'sms_from_number' => 'nullable|string|max:255',
            'messaging_sender_id' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        
        // Handle is_active - default to false if not present
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        // If this profile is being set as active, deactivate all others of the same type
        if ($data['is_active']) {
            CommunicationProfile::where('type', $data['type'])->update(['is_active' => false]);
        }

        CommunicationProfile::create($data);

        return redirect()->route('system.communication-profiles')->with('success', 'Communication Profile created successfully!');
    }

    public function show(CommunicationProfile $communicationProfile)
    {
        return view('system.communication-profiles-show', compact('communicationProfile'));
    }

    public function edit(CommunicationProfile $communicationProfile)
    {
        return view('system.communication-profiles-edit', compact('communicationProfile'));
    }

    public function update(Request $request, CommunicationProfile $communicationProfile)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:email,sms',
            'is_active' => 'boolean',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|in:tls,ssl,none',
            'email_from_address' => 'nullable|email|max:255',
            'email_from_name' => 'nullable|string|max:255',
            'sms_provider' => 'nullable|string|max:255',
            'sms_api_key' => 'nullable|string|max:255',
            'sms_api_secret' => 'nullable|string|max:255',
            'sms_from_number' => 'nullable|string|max:255',
            'messaging_sender_id' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        
        // Handle is_active
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        // If this profile is being set as active, deactivate all others of the same type
        if ($data['is_active']) {
            CommunicationProfile::where('type', $data['type'])
                ->where('id', '!=', $communicationProfile->id)
                ->update(['is_active' => false]);
        }

        $communicationProfile->update($data);

        return redirect()->route('system.communication-profiles')->with('success', 'Communication Profile updated successfully!');
    }

    public function destroy(CommunicationProfile $communicationProfile)
    {
        $communicationProfile->delete();
        return redirect()->route('system.communication-profiles')->with('success', 'Communication Profile deleted successfully!');
    }
}
