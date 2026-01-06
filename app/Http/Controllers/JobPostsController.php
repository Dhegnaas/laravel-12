<?php

namespace App\Http\Controllers;

use App\Models\JobPosts;
use Doctrine\Inflector\Rules\English\Rules;
use Illuminate\Http\Request;
use App\Core\Traits\AuditTrailTraits;
use App\Core\Traits\GlobalTraits;
use Illuminate\Support\Facades\DB;

class JobPostsController extends Controller
{
    use AuditTrailTraits, GlobalTraits;
    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        return JobPosts::with(['auditTrails'])->get();

    }
    public function pagination(Request $request)
    {
        $query = JobPosts::with('auditTrails')->orderBy('id', 'desc');
        return $this->paginate($query, $request);
    }
    public function filtration(Request $request)
    {
        return $this->filter(JobPosts::with('auditTrails'), $request->condition, $request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $validData = $this->validateData($request);
            $validData['status'] = 'draft';
            $validData['created_by'] = auth()->id();
            $job = JobPosts::create($validData);
            $this->auditTrail('save', $job->id, now(), 'job', 'Created');
            return response()->json([
                JobPosts::with('auditTrails')->find($job->id)
            ]);

        });
    }

    /**
     * Display the specified resource.
     */
    public function show(JobPosts $JobPosts)
    {
        // Kani waa qaabka saxda ah:
        return response()->json(
            $JobPosts->load(['auditTrails']) // Si toos ah u soo celi Object-ka
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobPosts $JobPosts)
    {
        return DB::transaction(function () use ($request, $JobPosts) {
            $validateData = $this->validateData($request, $JobPosts->id);
            $JobPosts->update($validateData);
            $this->auditTrail('update', $JobPosts->id, now(), 'job', 'Updated');
            return response()->json([
                JobPosts::with(['auditTrails'])->where('id', $JobPosts->id)->first()
            ]);
        });
    }
    public function submit(JobPosts $JobPosts)
    {
        return DB::transaction(function () use ($JobPosts) {
            $JobPosts->update(['status' => 'submitted']);
            $this->auditTrail('submit', $JobPosts->id, now(), 'job', 'Submitted job');
            return response()->json([
                JobPosts::with('auditTrails')->where('id', $JobPosts->id)->first()
            ]);
        });
    }
    public function cancel(JobPosts $JobPosts)
    {
        return DB::transaction(function () use ($JobPosts) {
            $JobPosts->update(['status' => 'canceled']);
            $this->auditTrail('cancel', $JobPosts->id, now(), 'job', 'Canceled job');
            return response()->json([
                JobPosts::with('auditTrails')->where('id', $JobPosts->id)->first()
            ]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(JobPosts $JobPosts)
    {
        $this->auditTrail('delete', $JobPosts->id, now(), 'job', 'Deleted');
        return $JobPosts->delete();
    }
    protected function validateData(Request $request, $jobId = null)
    {
        $id = $jobId ?? $request->id ?? null;
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department' => 'nullable|string|max:255',
        ];
        return $request->validate($rules);
    }
}
