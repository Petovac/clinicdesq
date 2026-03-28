<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\VetCertificate;
use App\Models\CertificateTemplate;
use App\Models\Pet;
use App\Models\Clinic;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    /**
     * List certificates for a pet
     */
    public function index(Pet $pet)
    {
        $certificates = $pet->certificates()->with('vet', 'clinic')->get();
        return view('vet.certificates.index', compact('pet', 'certificates'));
    }

    /**
     * Show create form — select type and fill template fields
     */
    public function create(Request $request, Pet $pet)
    {
        $vet = Auth::guard('vet')->user();
        $clinicId = session('active_clinic_id');
        $clinic = Clinic::with('organisation')->findOrFail($clinicId);

        $type = $request->get('type', 'health');

        // Get template: org-specific first, else system default
        $template = CertificateTemplate::where('type', $type)
            ->where(fn($q) => $q->where('organisation_id', $clinic->organisation_id)->orWhereNull('organisation_id'))
            ->orderByRaw('organisation_id IS NULL ASC')
            ->first();

        if (!$template) {
            return back()->with('error', 'No template found for this certificate type.');
        }

        // Pre-fill fields from template defaults
        $fields = $template->getFields();

        // If vaccination certificate, auto-populate vaccination history
        if ($type === 'vaccination') {
            $pet->load('vaccinations');
        }

        // Available template types
        $types = CertificateTemplate::forOrg($clinic->organisation_id);

        $appointmentId = $request->get('appointment_id');

        return view('vet.certificates.create', compact('pet', 'vet', 'clinic', 'template', 'fields', 'types', 'type', 'appointmentId'));
    }

    /**
     * Store certificate (as draft or issued)
     */
    public function store(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'certificate_type' => 'required|string',
            'title' => 'required|string|max:255',
            'content' => 'nullable|array',
            'valid_until' => 'nullable|date',
        ]);

        $vet = Auth::guard('vet')->user();
        $clinicId = session('active_clinic_id');
        $clinic = Clinic::findOrFail($clinicId);

        $status = $request->get('action') === 'issue' ? 'issued' : 'draft';

        $certificate = VetCertificate::create([
            'pet_id' => $request->pet_id,
            'appointment_id' => $request->appointment_id,
            'clinic_id' => $clinicId,
            'vet_id' => $vet->id,
            'organisation_id' => $clinic->organisation_id,
            'certificate_template_id' => $request->template_id,
            'certificate_type' => $request->certificate_type,
            'certificate_number' => VetCertificate::generateNumber(),
            'title' => $request->title,
            'content' => $request->content,
            'issued_date' => now()->toDateString(),
            'valid_until' => $request->valid_until,
            'status' => $status,
        ]);

        // If issued, generate PDF
        if ($status === 'issued') {
            $this->generatePdf($certificate);
        }

        return redirect()->route('vet.certificates.index', $certificate->pet_id)
            ->with('success', $status === 'issued' ? 'Certificate issued successfully.' : 'Certificate saved as draft.');
    }

    /**
     * Edit a draft certificate
     */
    public function edit(VetCertificate $certificate)
    {
        if ($certificate->isIssued()) {
            return back()->with('error', 'Cannot edit an issued certificate.');
        }

        $pet = $certificate->pet;
        $vet = $certificate->vet;
        $clinic = $certificate->clinic->load('organisation');
        $template = $certificate->template;
        $fields = $template ? $template->getFields() : [];

        // Merge saved content values into field defaults
        $savedContent = $certificate->content ?? [];
        foreach ($fields as &$field) {
            if (isset($savedContent[$field['key']])) {
                $field['default'] = $savedContent[$field['key']];
            }
        }

        return view('vet.certificates.edit', compact('certificate', 'pet', 'vet', 'clinic', 'template', 'fields'));
    }

    /**
     * Update a draft certificate
     */
    public function update(Request $request, VetCertificate $certificate)
    {
        if ($certificate->isIssued()) {
            return back()->with('error', 'Cannot edit an issued certificate.');
        }

        $certificate->update([
            'title' => $request->title,
            'content' => $request->content,
            'valid_until' => $request->valid_until,
        ]);

        $status = $request->get('action') === 'issue' ? 'issued' : 'draft';

        if ($status === 'issued') {
            $certificate->update(['status' => 'issued', 'issued_date' => now()->toDateString()]);
            $this->generatePdf($certificate);
        }

        return redirect()->route('vet.certificates.index', $certificate->pet_id)
            ->with('success', $status === 'issued' ? 'Certificate issued.' : 'Draft updated.');
    }

    /**
     * Preview certificate as HTML
     */
    public function preview(VetCertificate $certificate)
    {
        $certificate->load(['pet.petParent', 'vet', 'clinic.organisation']);

        // If vaccination type, load vaccination records
        $vaccinations = collect();
        if ($certificate->certificate_type === 'vaccination') {
            $vaccinations = $certificate->pet->vaccinations;
        }

        return view('documents.certificate.classic', [
            'certificate' => $certificate,
            'pet' => $certificate->pet,
            'parent' => $certificate->pet->petParent,
            'vet' => $certificate->vet,
            'clinic' => $certificate->clinic,
            'org' => $certificate->clinic->organisation,
            'vaccinations' => $vaccinations,
            'isPreview' => true,
        ]);
    }

    /**
     * Download certificate PDF
     */
    public function download(VetCertificate $certificate)
    {
        if ($certificate->pdf_path && file_exists(storage_path('app/public/' . $certificate->pdf_path))) {
            return response()->download(
                storage_path('app/public/' . $certificate->pdf_path),
                $certificate->certificate_number . '.pdf'
            );
        }

        // Generate on-the-fly if PDF doesn't exist
        $this->generatePdf($certificate);
        $certificate->refresh();

        if ($certificate->pdf_path) {
            return response()->download(
                storage_path('app/public/' . $certificate->pdf_path),
                $certificate->certificate_number . '.pdf'
            );
        }

        return back()->with('error', 'Failed to generate PDF.');
    }

    /**
     * Generate and store PDF
     */
    private function generatePdf(VetCertificate $certificate): void
    {
        $certificate->load(['pet.petParent', 'pet.vaccinations', 'vet', 'clinic.organisation']);

        $vaccinations = collect();
        if ($certificate->certificate_type === 'vaccination') {
            $vaccinations = $certificate->pet->vaccinations;
        }

        $html = view('documents.certificate.classic', [
            'certificate' => $certificate,
            'pet' => $certificate->pet,
            'parent' => $certificate->pet->petParent,
            'vet' => $certificate->vet,
            'clinic' => $certificate->clinic,
            'org' => $certificate->clinic->organisation,
            'vaccinations' => $vaccinations,
            'isPreview' => false,
        ])->render();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'portrait');

        $dir = 'certificates/' . $certificate->organisation_id;
        $path = $dir . '/' . $certificate->certificate_number . '.pdf';

        if (!is_dir(storage_path('app/public/' . $dir))) {
            mkdir(storage_path('app/public/' . $dir), 0755, true);
        }

        $pdf->save(storage_path('app/public/' . $path));
        $certificate->update(['pdf_path' => $path]);
    }
}
