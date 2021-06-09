<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Novo NFe') }}
    </h2>
  </x-slot>
  <div class="max-w-7xl py-2 mx-auto sm:px-6 lg:px-8 mt-4">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200 break-words">
          <form method="POST" action="{{ route('nfes.store') }}" enctype="multipart/form-data">
            @csrf
            @if (count($errors) > 0)
              <div class="mb-3">
                <strong>Algo deu errado:</strong>
                <ul>
                  @foreach ($errors->all() as $error)
                    <li class="text-red-500">{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            <x-label for="files" :value="__('Arquivos')"/>
            <input id="files" class="block mt-1 w-full" type="file" name="files[]" multiple accept=".xml"/>
            <p class="text-gray-400 mt-1">* Os arquivos devem estar no formato <strong>.xml</strong></p>
            <p class="text-gray-400 mb-1">* O envio pode demorar</strong></p>
            <x-button class="block">Enviar</x-button>
          </form>
        </div>
      </div>
    </div>
</x-app-layout>
