<div class="bg-white p-6 rounded-lg shadow-md border-l-4 {{ $isGlobal ? 'border-orange-500' : 'border-purple-500' }}">
    <div class="flex justify-between items-center mb-4">
        <div class="flex flex-col w-1/2">
            <div class="flex items-center space-x-2">
                @if(!$isGlobal)
                    <div class="flex flex-col space-y-1 mr-2">
                        <button wire:click="movePage({{ $page->id }}, 'up')" class="text-gray-400 hover:text-purple-600 transition-colors" title="Monter la page">
                            <x-fas-chevron-up class="w-3 h-3" />
                        </button>
                        <button wire:click="movePage({{ $page->id }}, 'down')" class="text-gray-400 hover:text-purple-600 transition-colors" title="Descendre la page">
                            <x-fas-chevron-down class="w-3 h-3" />
                        </button>
                    </div>
                @endif
                @if($isGlobal)
                    <div class="flex items-center text-lg font-bold text-gray-800">
                        <x-fas-globe class="w-4 h-4 mr-2 text-orange-500" />
                        {{ $page->name }}
                    </div>
                @else
                    <input type="text" value="{{ $page->name }}"
                           wire:change="$refresh"
                           onchange="@this.updatePageName({{ $page->id }}, this.value)"
                           class="text-lg font-bold border-none focus:ring-0 p-0">
                @endif
            </div>

            @if(!$isGlobal)
            <div class="mt-2 flex items-center space-x-3">
                <div class="flex items-center space-x-2 bg-purple-50 px-3 py-1.5 rounded-lg border border-purple-100">
                    <label class="text-[10px] uppercase tracking-wider font-black text-purple-700">Nb de pages similaires</label>
                    <input type="number"
                           value="{{ $page->quantity ?? 1 }}"
                           onchange="@this.updatePageQuantity({{ $page->id }}, this.value)"
                           class="w-14 p-1 border-2 border-purple-200 rounded text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-200 text-center font-bold">
                </div>
            </div>
            @endif
        </div>

        @if(!$isGlobal)
        <button wire:click="deletePage({{ $page->id }})" class="text-red-500 hover:text-red-700 text-sm flex items-center self-start">
            <x-fas-trash-alt class="w-4 h-4 mr-1" />
            Supprimer la page
        </button>
        @endif
    </div>

    <div class="space-y-4">
        @foreach($page->blocks as $block)
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm mb-4">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex flex-col space-y-2 mr-4">
                        <button wire:click="moveBlock({{ $page->id }}, {{ $block->id }}, 'up')" class="text-gray-300 hover:text-purple-500 transition-colors" title="Monter le bloc">
                            <x-fas-chevron-up class="w-3 h-3" />
                        </button>
                        <button wire:click="moveBlock({{ $page->id }}, {{ $block->id }}, 'down')" class="text-gray-300 hover:text-purple-500 transition-colors" title="Descendre le bloc">
                            <x-fas-chevron-down class="w-3 h-3" />
                        </button>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800">{{ $block->block->name ?? 'Bloc inconnu' }}</h4>
                        <p class="text-xs text-gray-500 italic">{{ $block->block->description ?? '' }}</p>
                    </div>
                    <button wire:click="removeBlockFromPage({{ $page->id }}, {{ $block->id }})"
                            class="text-red-400 hover:text-red-600 transition-colors" title="Supprimer ce bloc">
                        <x-fas-times class="h-5 w-5" />
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-3 rounded border border-gray-100 items-center">
                    <div class="space-y-1">
                        <label class="block text-[10px] uppercase tracking-wider font-black text-purple-600">Quantité</label>
                        <input type="number"
                               value="{{ $block->quantity }}"
                               wire:change="updateBlockField({{ $block->id }}, 'quantity', $event.target.value)"
                               class="w-20 p-2 border-2 border-gray-200 rounded text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-200">
                    </div>
                    <div class="text-right text-xs text-gray-400 italic">
                        Les prix du gabarit n'affectent pas les calculs.
                    </div>
                </div>
            </div>
        @endforeach

        <div class="mt-4 space-y-2">
            @if(count($availableBlocks) > 10 || $blockSearch)
                <div class="relative">
                    <input type="text"
                           wire:model.live.debounce.300ms="blockSearch"
                           placeholder="Rechercher un bloc..."
                           class="text-xs border-2 border-gray-200 rounded-md w-full p-2 pl-8 focus:border-purple-500 focus:ring-1 focus:ring-purple-200">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
                        <x-fas-search class="w-3 h-3" />
                    </div>
                    @if($blockSearch)
                        <button wire:click="$set('blockSearch', '')" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-red-500">
                            <x-fas-times class="w-3 h-3" />
                        </button>
                    @endif
                </div>
            @endif

            <select wire:change="handleBlockSelection({{ $page->id }}, $event.target.value); $event.target.value = ''" class="text-sm border-2 border-gray-300 rounded-md shadow-sm w-full p-2 focus:border-purple-500 focus:ring focus:ring-purple-200">
                <option value="">+ Ajouter un bloc...</option>
                @foreach($availableBlocks as $availableBlock)
                    <option value="{{ $availableBlock->id }}">{{ $availableBlock->name }}</option>
                @endforeach
                @if(count($availableBlocks) === 0 && $blockSearch)
                    <option disabled>Aucun bloc trouvé pour "{{ $blockSearch }}"</option>
                @endif
                <hr>
                <option value="new_block" class="font-bold text-green-600">✨ Ajouter un nouveau bloc...</option>
            </select>
        </div>
    </div>
</div>
