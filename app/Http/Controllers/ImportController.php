<?php

namespace App\Http\Controllers;

use App\Models\FichierResultat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
    public function formulaire()
    {
        return view('admin.import.formulaire');
    }

    public function apercu(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:xlsx,xls',
            'annee_universitaire' => 'required|string',
            'semestre' => 'required|in:S1,S2',
        ]);

        $user = auth()->user();
        $fichier = $request->file('fichier');

        // Stockage temporaire pendant la vérification
        $cheminTemp = $fichier->store('temp');

        $verification = $this->verifierFichier(storage_path('app/' . $cheminTemp));

        return view('admin.import.apercu', [
            'chemin_temp' => $cheminTemp,
            'nom_original' => $fichier->getClientOriginalName(),
            'annee_universitaire' => $request->annee_universitaire,
            'semestre' => $request->semestre,
            'verification' => $verification,
        ]);
    }

    public function confirmer(Request $request)
    {
        $request->validate([
            'chemin_temp' => 'required|string',
            'nom_original' => 'required|string',
            'annee_universitaire' => 'required|string',
            'semestre' => 'required|in:S1,S2',
        ]);

        $user = auth()->user();
        $filiereSlug = Str::slug($user->filiere->libelle);
        $dossierDestination = "{$request->annee_universitaire}/{$filiereSlug}/{$request->semestre}";

        // Déplace le fichier temporaire vers le stockage définitif
        Storage::disk('resultats')->makeDirectory($dossierDestination);
        $cheminFinal = "{$dossierDestination}/resultats.xlsx";
        Storage::disk('resultats')->put(
            $cheminFinal,
            Storage::get($request->chemin_temp)
        );
        Storage::delete($request->chemin_temp); // nettoie le temporaire

        FichierResultat::updateOrCreate(
            [
                'filiere_id' => $user->filiere_id,
                'annee_universitaire' => $request->annee_universitaire,
                'semestre' => $request->semestre,
            ],
            [
                'nom_fichier' => $request->nom_original,
                'chemin_fichier' => $cheminFinal,
                'statut' => 'actif',
                'importe_par' => $user->id,
                'date_import' => now(),
            ]
        );

        return redirect()->route('import.formulaire')->with('succes', 'Fichier importé et publié avec succès.');
    }

    private function verifierFichier(string $chemin): array
    {
        $erreurs = [];
        $apercuLignes = [];

        try {
            $spreadsheet = IOFactory::load($chemin);
            $sheet = $spreadsheet->getActiveSheet();

            // Cherche la colonne "Matricule(id permanant de l'etudiant)" sur la première ligne d'en-tête
            $colonneMatricule = null;
            foreach ($sheet->getRowIterator(1, 1) as $row) {
                foreach ($row->getCellIterator() as $cell) {
                    if (Str::contains(Str::lower((string) $cell->getValue()), 'matricule')) {
                        $colonneMatricule = $cell->getColumn();
                    }
                }
            }

            if (!$colonneMatricule) {
                $erreurs[] = "Aucune colonne 'Matricule' détectée dans la première ligne.";
                return ['erreurs' => $erreurs, 'apercu' => []];
            }

            $matriculesVus = [];
            $ligne = 2;
            $maxApercu = 5;

            while ($sheet->getCell($colonneMatricule . $ligne)->getValue() !== null) {
                $matricule = trim((string) $sheet->getCell($colonneMatricule . $ligne)->getValue());

                if ($matricule === '') {
                    $erreurs[] = "Matricule vide à la ligne {$ligne}.";
                } elseif (in_array($matricule, $matriculesVus)) {
                    $erreurs[] = "Matricule en double : {$matricule} (ligne {$ligne}).";
                } else {
                    $matriculesVus[] = $matricule;
                }

                if (count($apercuLignes) < $maxApercu) {
                    $apercuLignes[] = $matricule;
                }

                $ligne++;
            }

        } catch (\Exception $e) {
            $erreurs[] = "Fichier illisible ou corrompu : " . $e->getMessage();
        }

        return ['erreurs' => $erreurs, 'apercu' => $apercuLignes];
    }
}

