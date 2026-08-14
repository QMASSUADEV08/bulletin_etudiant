<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Importer un fichier de résultats</h2>
    </x-slot>

    <div class="py-12 max-w-2xl mx-auto">
        @if (session('succes'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('succes') }}</div>
        @endif

        <form method="POST" action="{{ route('import.apercu') }}" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
            @csrf
            <label class="block mb-2">Année universitaire</label>
            <input type="text" name="annee_universitaire" placeholder="2025-2026" class="border w-full p-2 mb-4 rounded" required>

            <label class="block mb-2">Semestre</label>
            <select name="semestre" class="border w-full p-2 mb-4 rounded" required>
                <option value="S1">S1</option>
                <option value="S2">S2</option>
            </select>

            <label class="block mb-2">Fichier Excel</label>
            <input type="file" name="fichier" accept=".xlsx,.xls" class="border w-full p-2 mb-4 rounded" required>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Vérifier et prévisualiser</button>
        </form>
    </div>
</x-app-layout>