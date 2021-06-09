<?php
$showForm = false;
function toggleEdit() {
  $showForm = !$showForm;
}
?>
<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Produtos') }}
    </h2>
  </x-slot>
  <div class="py-12">
    @if (count($products) <= 0)
      <div class="max-w-7xl py-2 mx-auto sm:px-6 lg:px-8">
        <p class="text-xl mt-1 text-gray-400">Sem nenhum registro no momento</p>
      </div>
    @else
      @foreach ($products as $product)
        <h1>{{ $showForm }}</h1>
        <div class="max-w-7xl py-2 mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200 break-words">
              <h1 class="font-semibold text-xl text-gray-800 leading-tight">{{ $product->name }}</h1>
              <p class="text-gray-400 mb-1">#{{ $product->product_code }}</p>
              <p class="text-gray-500">Preço (unidade): <strong>R$ {{ $product->price }}</strong></p>
              <div x-data="{ open: false }">
                <button
                  @click="open = ! open"
                  class="inline-flex mt-2 rounded border-2 border-gray-700 text-gray-700 px-5 py-1 font-semibold hover:bg-gray-700 hover:text-white transition duration-150 ease-in-out">Editar</button>
                <form method="POST" action="{{ route('products.update') }}" x-show="open" class="mt-5"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95">
                  @method('PUT')
                  @csrf
                  <x-input name="id" value="{{ $product->id }}" hidden readonly />
                  <x-label for="name" :value="__('Nome')" />
                  <x-input id="email" class="block mt-1 w-full" type="text" name="name" value="{{ $product->name }}" required autocomplete="off" />
                  <x-label for="price" :value="__('Preço')" class="mt-4" />
                  <x-input id="price" class="block mt-1 w-full" type="number" name="price" value="{{ $product->price }}" required autocomplete="off" min="0.10" step="any" />
                  <x-button class="mt-4">Salvar</x-button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    @endif
  </div>
</x-app-layout>
