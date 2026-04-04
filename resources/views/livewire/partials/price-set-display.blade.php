<div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-600 dark:text-gray-400">
    <span><span class="font-bold">Prog.</span> <span class="text-gray-800 dark:text-gray-200">{{ number_format($set['price_programming'], 2) }} {{ $unit }}</span> <span class="text-gray-400">(fixe × 1)</span></span>
    <span><span class="font-bold">Inté.</span> <span class="text-gray-800 dark:text-gray-200">{{ number_format($set['price_integration'], 2) }} {{ $unit }}</span> <span class="text-gray-400">(fixe × 1)</span></span>
    <span><span class="font-bold">Champs</span> <span class="text-gray-800 dark:text-gray-200">{{ number_format($set['price_field_creation'], 2) }} {{ $unit }}</span> <span class="text-gray-400">(× qté)</span></span>
    <span><span class="font-bold">Contenu</span> <span class="text-gray-800 dark:text-gray-200">{{ number_format($set['price_content_management'], 2) }} {{ $unit }}</span> <span class="text-gray-400">(× qté × pages)</span></span>
</div>
