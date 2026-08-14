<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Aperçu avant validation</h2>
    </x-slot>

    <div class="py-12 max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <p><strong>Fichier :</strong> {{ $nom_original }}</p>
        <p><strong>Semestre :</strong> {{ $semestre }} — {{ $annee_universitaire }}</p>

        @if (count($verification['erreurs']) > 0)
            <div class="bg-red-100 text-red-800 p-4 rounded my-4">
                <p class="font-bold">Erreurs détectées — import bloqué :</p>
                <ul class="list-disc pl-5">
                    @foreach ($verification['erreurs'] as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="bg-green-100 text-green-800 p-4 rounded my-4">
                Aucune erreur détectée. Aperçu des premiers matricules :
                <ul class="list-disc pl-5">
                    @foreach ($verification['apercu'] as $m)
                        <li>{{ $m }}</li>
                    @endforeach
                </ul>
            </div>

            <form method="POST" action="{{ route('import.confirmer') }}">
                @csrf
                <input type="hidden" name="chemin_temp" value="{{ $chemin_temp }}">
                <input type="hidden" name="nom_original" value="{{ $nom_original }}">
                <input type="hidden" name="annee_universitaire" value="{{ $annee_universitaire }}">
                <input type="hidden" name="semestre" value="{{ $semestre }}">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Confirmer et publier</button>
            </form>
        @endif

        <a href="{{ route('import.formulaire') }}" class="block mt-4 text-blue-600">← Réessayer avec un autre fichier</a>
    </div>
</x-app-layout>