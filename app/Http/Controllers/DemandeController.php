<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DemandeController extends Controller
{
    public function index()
    {
        try {
            $demandes = Demande::with(['client', 'chauffeur'])->latest()->get();
            return view('demandes.index', compact('demandes'));
        } catch (\Exception $e) {
            Log::error('Erreur récupération demandes:', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la récupération des demandes');
        }
    }

    public function show(Demande $demande)
    {
        $demande->load(['client', 'chauffeur']);
        return view('demandes.show', compact('demande'));
    }

    public function edit(Demande $demande)
    {
        return view('demandes.edit', compact('demande'));
    }

    public function update(Request $request, Demande $demande)
    {
        $validated = $request->validate([
            'status' => 'required|in:en_attente,acceptee,refusee'
        ]);

        $demande->update($validated);

        return redirect()
            ->route('demandes.index')
            ->with('success', 'La demande a été mise à jour avec succès.');
    }

    public function destroy(Demande $demande)
    {
        $demande->delete();
        return redirect()
            ->route('demandes.index')
            ->with('success', 'La demande a été supprimée avec succès.');
    }
}
