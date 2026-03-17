<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InternshipRequestStatusChanged;
use App\Models\Intern;
use App\Models\InternshipRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class InternshipRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = InternshipRequest::with('user')->latest();

        if ($status) {
            $query->where('status', $status);
            if ($status === 'accepted') {
                $query->where(function ($q) {
                    $q->whereNull('period_end')
                      ->orWhere('period_end', '>=', now()->toDateString());
                });
            }
        }

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(10)->withQueryString();

        return view('admin.requests.index', compact('requests', 'status', 'search'));
    }

    public function show(InternshipRequest $request)
    {
        $request->load('user');

        return view('admin.requests.show', compact('request'));
    }

    public function accept(Request $httpRequest, InternshipRequest $request)
    {
        $request->update([
            'status'      => 'accepted',
            'reviewed_by' => $httpRequest->user()->id,
            'reviewed_at' => now(),
        ]);

        Intern::firstOrCreate(
            ['user_id' => $request->user_id],
            [
                'internship_request_id' => $request->id,
                'start_date'            => $request->period_start,
                'end_date'              => $request->period_end,
                'duration_days'         => $request->period_start && $request->period_end
                    ? $request->period_start->diffInDays($request->period_end) + 1
                    : null,
            ]
        );

        $this->notifyApplicant($request);

        return redirect()->route('admin.requests.index')->with('status', __('Request accepted.'));
    }

    public function reject(Request $httpRequest, InternshipRequest $request)
    {
        $data = $httpRequest->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $request->update([
            'status'      => 'rejected',
            'admin_notes' => $data['admin_notes'] ?? null,
            'reviewed_by' => $httpRequest->user()->id,
            'reviewed_at' => now(),
        ]);

        $this->notifyApplicant($request);

        return redirect()->route('admin.requests.index')->with('status', __('Request rejected.'));
    }

    /**
     * Notify applicant but never block admin flow if mail is misconfigured.
     */
    protected function notifyApplicant(InternshipRequest $request): void
    {
        try {
            Mail::to($request->user->email)->queue(
                new InternshipRequestStatusChanged($request)
            );
        } catch (Throwable $e) {
            Log::warning('Unable to send internship status email', [
                'request_id' => $request->id,
                'email'      => $request->user->email,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
