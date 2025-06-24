<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function download(Certificate $certificate)
    {
        abort_unless(auth()->id() === $certificate->user_id, 403);

        return Storage::disk('public')->download($certificate->path);
    }
}
