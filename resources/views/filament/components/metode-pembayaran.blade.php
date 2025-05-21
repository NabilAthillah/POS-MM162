        <div class="grid grid-cols-2 gap-4">
            <button wire:click="prosesPembayaran('cash')"
                class="flex flex-col items-center p-2 rounded bg-white dark:bg-gray-700 hover:bg-gray-200">
                <img src="{{ asset('assets/img/money.webp') }}" class="h-16" alt="Cash">
                <span>Cash</span>
            </button>

            <button wire:click="prosesPembayaran('qris')"
                class="flex flex-col items-center p-2 rounded bg-white dark:bg-gray-700 hover:bg-gray-200">
                <img src="{{ asset('assets/img/qris.png') }}" class="h-16" alt="QRIS">
                <span>QRIS</span>
            </button>

            <button wire:click="prosesPembayaran('va_bca')"
                class="flex flex-col items-center p-2 rounded bg-white dark:bg-gray-700 hover:bg-gray-200">
                <img src="{{ asset('assets/img/bca.png') }}" class="h-16" alt="VA BCA">
                <span>VA BCA</span>
            </button>

            <button wire:click="prosesPembayaran('va_bni')"
                class="flex flex-col items-center p-2 rounded bg-white dark:bg-gray-700 hover:bg-gray-200">
                <img src="{{ asset('assets/img/bni.png') }}" class="h-16" alt="VA BNI">
                <span>VA BNI</span>
            </button>

            <button wire:click="prosesPembayaran('va_mandiri')"
                class="flex flex-col items-center p-2 rounded bg-white dark:bg-gray-700 hover:bg-gray-200">
                <img src="{{ asset('assets/img/mandiri.webp') }}" class="h-16" alt="VA Mandiri">
                <span>VA Mandiri</span>
            </button>

            <button wire:click="prosesPembayaran('va_bri')"
                class="flex flex-col items-center p-2 rounded bg-white dark:bg-gray-700 hover:bg-gray-200">
                <img src="{{ asset('assets/img/bri.png') }}" class="h-16" alt="VA BRI">
                <span>VA BRI</span>
            </button>
        </div>