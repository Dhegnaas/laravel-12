<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Core\Traits\AuditTrailTraits;
use App\Core\Traits\GlobalTraits;

class AccountsController extends Controller
{
    use AuditTrailTraits, GlobalTraits;

    public function list()
    {
        return Account::with('auditTrails')->get();
    }

    public function pagination(Request $request)
    {
        $query = Account::with('auditTrails')->orderByDesc('id');
        return $this->paginate($query, $request);
    }

    public function filtration(Request $request)
    {
        return $this->filter(
            Account::with('auditTrails'),
            $request->condition,
            $request
        );
    }

    public function save(Request $request)
    {
        return DB::transaction(function () use ($request) {

            $validData = $this->validateData($request);
            $validData['status'] = 'draft';
            // default created_by = logged in user
            $validData['created_by'] = auth()->id();
            $account = Account::create($validData);
            $this->auditTrail('save', $account->id, now(), 'account', 'Created');
            return response()->json(
                Account::with('auditTrails')->find($account->id)
            );
        });
    }

    public function show(Account $account)
    {
        return response()->json(
            Account::with('auditTrails')->find($account->id)
        );
    }

    public function update(Request $request, Account $account)
    {
        return DB::transaction(function () use ($request, $account) {

            $validatedData = $this->validateData($request, $account->id);
            $account->update($validatedData);
            $this->auditTrail('update', $account->id, now(), 'account', 'Updated');
            return response()->json(
                Account::with('auditTrails')->find($account->id)
            );
        });
    }

    public function submit(Account $account)
    {
        return DB::transaction(function () use ($account) {
            $account->update(['status' => 'submitted']);
            $this->auditTrail('submit', $account->id, now(), 'account', 'Submitted account');
            return response()->json(
                Account::with('auditTrails')->find($account->id)
            );
        });
    }

    public function cancel(Account $account)
    {
        return DB::transaction(function () use ($account) {
            $account->update(['status' => 'draft']);
            $this->auditTrail('cancel', $account->id, now(), 'account', 'Cancelled account');
            return response()->json(
                Account::with('auditTrails')->find($account->id)
            );
        });
    }

    public function delete(Account $account)
    {
        $this->auditTrail('delete', $account->id, now(), 'account', 'Account deleted');
        $account->delete();
        return response()->json(['message' => 'Account deleted']);
    }

    protected function validateData(Request $request, $accountId = null)
    {
        return $request->validate([
            'acc_name' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'balance' => 'required|numeric|min:0',
        ]);
    }
}
