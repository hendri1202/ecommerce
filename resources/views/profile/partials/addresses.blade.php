<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Alamat Saya') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Kelola daftar alamat dan pilih mana yang ingin dijadikan default.
        </p>
    </header>

    <form method="post" action="{{ route('addresses.store') }}" class="mt-4 space-y-4">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="label" value="Label (Opsional)" />
                <x-text-input id="label" name="label" type="text" class="mt-1 block w-full" :value="old('label')" />
                <x-input-error class="mt-2" :messages="$errors->get('label')" />
            </div>
            <div>
                <x-input-label for="recipient_name" value="Nama Penerima" />
                <x-text-input id="recipient_name" name="recipient_name" type="text" class="mt-1 block w-full" :value="old('recipient_name', auth()->user()->name)" required />
                <x-input-error class="mt-2" :messages="$errors->get('recipient_name')" />
            </div>
            <div>
                <x-input-label for="phone" value="No HP" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', auth()->user()->phone)" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>
            <div>
                <x-input-label for="postal_code" value="Kode Pos" />
                <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" :value="old('postal_code', auth()->user()->postal_code)" />
                <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
            </div>
            <div>
                <x-input-label for="province" value="Provinsi" />
                <x-text-input id="province" name="province" type="text" class="mt-1 block w-full" :value="old('province', auth()->user()->province)" />
                <x-input-error class="mt-2" :messages="$errors->get('province')" />
            </div>
            <div>
                <x-input-label for="city" value="Kota/Kabupaten" />
                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', auth()->user()->city)" />
                <x-input-error class="mt-2" :messages="$errors->get('city')" />
            </div>
            <div class="sm:col-span-2">
                <x-input-label for="address" value="Alamat Lengkap" />
                <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', auth()->user()->address) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('address')" />
            </div>
            <div class="sm:col-span-2 flex items-center gap-2">
                <input id="is_default" name="is_default" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <label for="is_default" class="text-sm text-gray-700">Jadikan alamat default</label>
            </div>
        </div>

        <x-primary-button>{{ __('Tambah Alamat') }}</x-primary-button>
    </form>

    <div class="mt-6 space-y-3">
        @forelse(auth()->user()->addresses()->orderByDesc('is_default')->get() as $address)
            <div class="border rounded-md p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="font-semibold">{{ $address->label ?? 'Alamat' }} @if($address->is_default)<span class="text-xs text-emerald-600">(Default)</span>@endif</div>
                        <div class="text-sm text-gray-700">{{ $address->recipient_name }} - {{ $address->phone }}</div>
                        <div class="text-sm text-gray-600">{{ $address->address }}, {{ $address->city }}, {{ $address->province }} ({{ $address->postal_code }})</div>
                    </div>
                    <div class="flex flex-col gap-2">
                        @unless($address->is_default)
                            <form method="POST" action="{{ route('addresses.default', $address) }}">
                                @csrf
                                @method('PATCH')
                                <x-primary-button type="submit" class="py-2 px-3 text-xs">{{ __('Jadikan Default') }}</x-primary-button>
                            </form>
                        @endunless
                        <form method="POST" action="{{ route('addresses.destroy', $address) }}">
                            @csrf
                            @method('DELETE')
                            <x-danger-button class="py-2 px-3 text-xs" onclick="return confirm('Hapus alamat ini?')">{{ __('Hapus') }}</x-danger-button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-600">Belum ada alamat tersimpan.</p>
        @endforelse
    </div>
</section>
