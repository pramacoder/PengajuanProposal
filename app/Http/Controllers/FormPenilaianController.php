<?php

namespace App\Http\Controllers;

use App\Models\FormPenilaian;
use App\Repositories\Firebase\FormPenilaianConfigRepository;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FormPenilaianController extends Controller
{
    public function __construct(private
        FormPenilaianConfigRepository $configRepo, private
        FirebaseService $firebaseService
        )
    {
    }

    public function index(Request $request)
    {
        $query = FormPenilaian::with('creator')->latest();

        if ($request->filled('jenis_form')) {
            $query->where('jenis_form', $request->jenis_form);
        }
        if ($request->filled('skim')) {
            $query->where('skim', $request->skim);
        }

        $formPenilaians = $query->paginate(15);
        return view('operator.form_penilaian.index', compact('formPenilaians'));
    }

    public function create()
    {
        return view('operator.form_penilaian.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_form' => 'required|string|max:255',
            'jenis_form' => 'required|string|max:50',
            'skim' => 'nullable|string|max:10',
            'tahun_ajaran' => 'nullable|string|max:20',
            'config' => 'nullable|json',
            'fields' => 'nullable|string', // JSON string from field builder
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['config'] = isset($validated['config']) && $validated['config']
            ? json_decode($validated['config'], true) : null;
        $validated['fields'] = isset($validated['fields']) && $validated['fields']
            ? json_decode($validated['fields'], true) : null;
        $validated['is_active'] = $request->boolean('is_active', true);

        $form = FormPenilaian::create($validated);

        // Dual-write to Firestore
        try {
            if ($this->firebaseService->isAvailable()) {
                $this->configRepo->createConfig($form->id, [
                    'nama_form' => $form->nama_form,
                    'jenis_form' => $form->jenis_form,
                    'skim' => $form->skim,
                    'config' => $form->config,
                    'fields' => $form->fields,
                    'tahun_ajaran' => $form->tahun_ajaran,
                ]);
            }
        }
        catch (\Throwable $e) {
            Log::warning('Firestore form penilaian config sync failed', ['error' => $e->getMessage()]);
        }

        return redirect()->route('operator.form.penilaian.index')
            ->with('success', 'Form penilaian berhasil dibuat.');
    }

    public function edit($id)
    {
        $form = FormPenilaian::findOrFail($id);
        return view('operator.form_penilaian.edit', compact('form'));
    }

    public function update(Request $request, $id)
    {
        $form = FormPenilaian::findOrFail($id);

        $validated = $request->validate([
            'nama_form' => 'required|string|max:255',
            'jenis_form' => 'required|string|max:50',
            'skim' => 'nullable|string|max:10',
            'tahun_ajaran' => 'nullable|string|max:20',
            'config' => 'nullable|json',
            'fields' => 'nullable|string', // JSON string from field builder
            'is_active' => 'boolean',
        ]);

        $validated['config'] = isset($validated['config']) && $validated['config']
            ? json_decode($validated['config'], true) : null;
        $validated['fields'] = isset($validated['fields']) && $validated['fields']
            ? json_decode($validated['fields'], true) : null;
        $validated['is_active'] = $request->boolean('is_active', true);

        $form->update($validated);

        // Sync to Firestore
        try {
            if ($this->firebaseService->isAvailable()) {
                $this->configRepo->createConfig($form->id, [
                    'nama_form' => $form->nama_form,
                    'jenis_form' => $form->jenis_form,
                    'skim' => $form->skim,
                    'config' => $form->config,
                    'fields' => $form->fields,
                    'tahun_ajaran' => $form->tahun_ajaran,
                ]);
            }
        }
        catch (\Throwable $e) {
            Log::warning('Firestore form penilaian update sync failed', ['error' => $e->getMessage()]);
        }

        return redirect()->route('operator.form.penilaian.index')
            ->with('success', 'Form penilaian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $form = FormPenilaian::findOrFail($id);
        $form->delete();

        return redirect()->route('operator.form.penilaian.index')
            ->with('success', 'Form penilaian berhasil dihapus.');
    }

    /**
     * Get the active form definition for a given placement and skim.
     * Used by reviewer views to render dynamic fields.
     */
    public static function getActiveForm(string $jenisForm, ?string $skim = null): ?FormPenilaian
    {
        return FormPenilaian::where('jenis_form', $jenisForm)
            ->where('is_active', true)
            ->where(function ($q) use ($skim) {
            $q->where('skim', $skim)->orWhereNull('skim');
        })
            ->orderByRaw("CASE WHEN skim = ? THEN 0 ELSE 1 END", [$skim ?? ''])
            ->first();
    }
}
