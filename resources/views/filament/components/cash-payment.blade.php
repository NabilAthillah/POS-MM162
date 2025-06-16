<x-modal wire:model.defer="showCheckModal">
    <h2>Apakah Anda ingin mengecek keaslian uang cash tersebut?</h2>
    <button wire:click="openCameraModal">Ya</button>
    <button wire:click="payCashDirectly">Tidak</button>
</x-modal>

<x-modal wire:model.defer="showCameraModal">
    <h2>Upload gambar uang cash atau ambil foto</h2>

    <input type="file" wire:model="image" accept="image/*" capture="environment" />

    @error('image') <span class="error">{{ $message }}</span> @enderror

    <button wire:click="checkCashAuthenticity">Submit</button>
    <button wire:click="$set('showCameraModal', false)">Batal</button>
</x-modal>
