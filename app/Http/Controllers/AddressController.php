<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Display a listing of user addresses.
     */
    public function index()
    {
        $addresses = auth()->user()->addresses()->get();
        return view('profile.addresses', compact('addresses'));
    }

    /**
     * Store a newly created address.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:50',
            'full_address' => 'required|string|min:10',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:10',
        ], [
            'title.required' => 'Adres başlığı zorunludur.',
            'full_address.required' => 'Tam adres alanı zorunludur.',
            'full_address.min' => 'Tam adres en az 10 karakter olmalıdır.',
            'city.required' => 'Şehir zorunludur.',
        ]);

        $user = auth()->user();
        $isFirst = !$user->addresses()->exists();

        $user->addresses()->create([
            'title' => $request->title,
            'full_address' => $request->full_address,
            'city' => $request->city,
            'district' => $request->district,
            'zip' => $request->zip,
            'is_default' => $isFirst,
        ]);

        return redirect()->back()->with('success', 'Adres başarıyla eklendi.');
    }

    /**
     * Update the specified address.
     */
    public function update(Request $request, Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403, 'Yetkisiz erişim.');

        $request->validate([
            'title' => 'required|string|max:50',
            'full_address' => 'required|string|min:10',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:10',
        ], [
            'title.required' => 'Adres başlığı zorunludur.',
            'full_address.required' => 'Tam adres alanı zorunludur.',
            'full_address.min' => 'Tam adres en az 10 karakter olmalıdır.',
            'city.required' => 'Şehir zorunludur.',
        ]);

        $address->update([
            'title' => $request->title,
            'full_address' => $request->full_address,
            'city' => $request->city,
            'district' => $request->district,
            'zip' => $request->zip,
        ]);

        return redirect()->back()->with('success', 'Adres başarıyla güncellendi.');
    }

    /**
     * Delete the specified address.
     */
    public function destroy(Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403, 'Yetkisiz erişim.');

        $isDefault = $address->is_default;
        $user = auth()->user();
        
        $address->delete();

        // If the deleted address was default, set another address as default
        if ($isDefault) {
            $nextAddress = $user->addresses()->first();
            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
            }
        }

        return redirect()->back()->with('success', 'Adres başarıyla silindi.');
    }

    /**
     * Set an address as default.
     */
    public function setDefault(Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403, 'Yetkisiz erişim.');

        DB::beginTransaction();

        try {
            auth()->user()->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
