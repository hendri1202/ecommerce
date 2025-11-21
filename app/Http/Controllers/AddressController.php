<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:100'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated) {
            if (!empty($validated['is_default'])) {
                Address::where('user_id', auth()->id())->update(['is_default' => false]);
            }

            Address::create(array_merge($validated, [
                'user_id' => auth()->id(),
                'is_default' => !empty($validated['is_default']),
            ]));
        });

        return back()->with('success', 'Alamat disimpan.');
    }

    public function destroy(Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);

        DB::transaction(function () use ($address) {
            $wasDefault = $address->is_default;
            $address->delete();

            if ($wasDefault) {
                $newDefault = Address::where('user_id', auth()->id())->first();
                if ($newDefault) {
                    $newDefault->update(['is_default' => true]);
                }
            }
        });

        return back()->with('success', 'Alamat dihapus.');
    }

    public function setDefault(Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);

        DB::transaction(function () use ($address) {
            Address::where('user_id', auth()->id())->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return back()->with('success', 'Alamat default diperbarui.');
    }
}

