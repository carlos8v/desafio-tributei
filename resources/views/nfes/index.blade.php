<?php
function mask($str, $mask) {
  $maskedStr = array();
  $i = 0;
  for ($index = 0; $index < strlen($mask); $index++) {
    array_push($maskedStr, $mask[$index] == '#' ? $str[$i++]: $mask[$index]);
  }
  return implode('', $maskedStr);
}
?>

<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('NFes') }}
    </h2>
  </x-slot>
  <div class="py-12">
    <div class="max-w-7xl py-2 mx-auto sm:px-6 lg:px-8 mb-2">
      <a href="{{ route('nfes.new') }}" class="ml-2 font-semibold text-xl text-gray-600 leading-tight mb-2 hover:text-gray-700 hover:underline">Adicionar</a>
    </div>
    @if (count($nfes) <= 0)
      <div class="max-w-7xl py-2 mx-auto sm:px-6 lg:px-8">
        <p class="text-xl mt-1 text-gray-400">Sem nenhum registro no momento</p>
      </div>
    @else
      @foreach ($nfes as $nfe)
        <div class="max-w-7xl py-2 mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200 break-words">
              <h1 class="font-semibold text-xl text-gray-800 leading-tight mb-2">{{ $nfe->nfe_code }}</h1>
              <p class="text-gray-500">Empresa: {{ $nfe->name }}</p>
              <p class="text-gray-500">CNPJ: {{ mask($nfe->CNPJ, "##.###.###/####-##") }}</p>
              <p class="text-gray-500">Data de emissão: {{ $nfe->generated_date }}</p>
              <p class="text-gray-500" p>Valor da nota: <strong>R$ {{ number_format($nfe->total_price, 2) }}</strong></p>
              <a href="{{ route('nfes.show', $slug = $nfe->id) }}" class="inline-flex mt-2 rounded border-2 border-gray-700 text-gray-700 px-5 py-1 font-semibold hover:bg-gray-700 hover:text-white transition duration-150 ease-in-out">Ver mais</a>
            </div>
          </div>
        </div>
      @endforeach
    @endif
  </div>
</x-app-layout>
