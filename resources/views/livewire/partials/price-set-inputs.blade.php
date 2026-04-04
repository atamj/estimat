<div class="grid grid-cols-2 gap-3">
    <div>
        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">Programmation <span class="font-normal text-gray-400">(fixe × 1)</span></label>
        <div class="relative">
            <input type="number" step="0.01" wire:model="{{ $model }}.price_programming" placeholder="0"
                   class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2 pr-8 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500">
            <span class="absolute inset-y-0 right-0 pr-2 flex items-center text-gray-400 text-xs font-bold pointer-events-none">{{ $unit }}</span>
        </div>
        @error($model . '.price_programming') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    <div>
        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">Intégration <span class="font-normal text-gray-400">(fixe × 1)</span></label>
        <div class="relative">
            <input type="number" step="0.01" wire:model="{{ $model }}.price_integration" placeholder="0"
                   class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2 pr-8 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500">
            <span class="absolute inset-y-0 right-0 pr-2 flex items-center text-gray-400 text-xs font-bold pointer-events-none">{{ $unit }}</span>
        </div>
        @error($model . '.price_integration') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    <div>
        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">Création de champs <span class="font-normal text-gray-400">(× quantité)</span></label>
        <div class="relative">
            <input type="number" step="0.01" wire:model="{{ $model }}.price_field_creation" placeholder="0"
                   class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2 pr-8 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500">
            <span class="absolute inset-y-0 right-0 pr-2 flex items-center text-gray-400 text-xs font-bold pointer-events-none">{{ $unit }}</span>
        </div>
        @error($model . '.price_field_creation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    <div>
        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">Gestion de contenu <span class="font-normal text-gray-400">(× qté × pages)</span></label>
        <div class="relative">
            <input type="number" step="0.01" wire:model="{{ $model }}.price_content_management" placeholder="0"
                   class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2 pr-8 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500">
            <span class="absolute inset-y-0 right-0 pr-2 flex items-center text-gray-400 text-xs font-bold pointer-events-none">{{ $unit }}</span>
        </div>
        @error($model . '.price_content_management') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
</div>
