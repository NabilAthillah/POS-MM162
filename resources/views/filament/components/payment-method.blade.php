<div class="grid grid-cols-2 gap-4">
    <button wire:click="paymentProcess('cash')"
        class="flex flex-col items-center p-2 rounded bg-white dark:bg-gray-700 hover:bg-gray-200">
        <img src="{{ asset('assets/img/money.webp') }}" class="h-16" alt="Cash">
        <span>Cash</span>
    </button>

    <button wire:click="paymentProcess('Other QRIS')"
        class="flex flex-col items-center p-2 rounded bg-white dark:bg-gray-700 hover:bg-gray-200">
        <img src="{{ asset('assets/img/qris.png') }}" class="h-16" alt="QRIS">
        <span>QRIS</span>
    </button>
</div>