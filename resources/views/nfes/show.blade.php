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
    <h2 class="font-semibold text-xl text-gray-800 leading-tight break-words">
      {{ $nfe->nfe_code }}
    </h2>
  </x-slot>
  @if (count($products) <= 0)
    <div class="max-w-7xl py-2 mx-auto sm:px-6 lg:px-8">
      <p class="text-xl mt-1 text-gray-400">Sem nenhum registro no momento</p>
    </div>
  @else
    <div class="max-w-7xl py-2 mx-auto sm:px-6 lg:px-8 mt-4">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200 break-words">
          <h1 class="font-semibold text-xl text-gray-800 leading-tight mb-2">Informações</h1>
          <p class="text-gray-500">Empresa: {{ $nfe->name }}</p>
          <p class="text-gray-500">CNPJ: {{ mask($nfe->CNPJ, '##.###.###/####-##') }}</p>
          <p class="text-gray-500">Data: {{ $nfe->generated_date }}</p>
          <p class="text-gray-500">Frete: R$ {{ number_format($nfe->delivery_price, 2) }}</p>
          <h1 class="font-semibold text-base text-gray-800 leading-tight mt-2">Cliente</h1>
          <p class="text-gray-500">Nome: {{ $customer->name }}</p>
          @if ($customer->CPF)
            <p class="text-gray-500">CPF: {{ mask($customer->CPF, '###.###.###-##') }}</p>
          @endif
          @if ($customer->CNPJ)
            <p class="text-gray-500">CNPJ: {{ mask($customer->CNPJ, '##.###.###/####-##') }}</p>
          @endif
          <p class="text-gray-500">Email: {{ $customer->email }}</p>
        </div>
      </div>
    </div>
    <div class="max-w-7xl py-2 mx-auto sm:px-6 lg:px-8 mb-2 mt-4">
      <h1 class="ml-2 font-semibold text-xl text-gray-800 leading-tight">Produtos</h1>
    </div>
    @foreach ($products as $product)
    <div class="max-w-7xl py-2 mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200 break-words">
          <h1 class="font-semibold text-xl text-gray-800 leading-tight mb-2">{{ $product->name }}</h1>
          <p class="text-gray-500">Preço: R$ {{ $product->price }}</p>
          <p class="text-gray-500">Quantidade: {{ $product->quantity }}x</p>
          <p class="text-gray-500">Preço total: <strong>R$ {{ number_format($product->total, 2) }}</strong></p>
        </div>
      </div>
    </div>
    @endforeach
    <div class="max-w-7xl py-2 mx-auto sm:px-6 lg:px-8 mb-2 mt-4">
      <h1 class="ml-2 font-semibold text-xl text-gray-800 leading-tight mb-4">Valor da nota: <strong>R$ {{ number_format($total_price, 2) }}</strong></h1>
    </div>
  @endif
</x-app-layout>
