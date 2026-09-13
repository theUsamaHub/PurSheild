<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['roles', 'vetProfile', 'shelterProfile']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->whereHas('roles', fn($q) => $q->where('slug', $role));
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $users = $query->latest()->paginate(15);

        $counts = User::selectRaw("count(*) as total")
            ->selectRaw("count(case when status = 'active' then 1 end) as active_count")
            ->selectRaw("count(case when status = 'suspended' then 1 end) as suspended_count")
            ->selectRaw("count(case when status = 'pending_verification' then 1 end) as pending_count")
            ->first();

        $rejectedCount = User::where('status', 'pending_verification')
            ->whereHas('vetProfile', fn($q) => $q->whereNotNull('rejected_at'))
            ->orWhereHas('shelterProfile', fn($q) => $q->whereNotNull('rejected_at'))
            ->count();

        $stats = [
            'total' => (int) $counts->total,
            'active' => (int) $counts->active_count,
            'suspended' => (int) $counts->suspended_count,
            'pending_verification' => (int) $counts->pending_count,
            'rejected' => $rejectedCount,
        ];

        $roles = Role::all();

        return view('admin.users.index', compact('users', 'stats', 'roles'));
    }

    public function show(User $user): View
    {
        $user->load(['roles', 'vetProfile', 'shelterProfile']);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $user->load('roles');

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            ],
            [
                'name.required' => 'The user name is required.',
                'name.max' => 'The user name must not exceed 255 characters.',
                'email.required' => 'The email address is required.',
                'email.email' => 'Please provide a valid email address.',
                'email.unique' => 'This email address is already taken.',
            ]
        );

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own status.');
        }

        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        $label = $user->status === 'active' ? 'activated' : 'deactivated';

        return back()->with('success', "User has been {$label} successfully.");
    }

    public function suspend(Request $request, User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        $validated = $request->validate(
            [
                'reason' => ['required', 'string', 'max:1000'],
            ],
            [
                'reason.required' => 'Please provide a reason for suspension.',
                'reason.max' => 'The suspension reason must not exceed 1000 characters.',
            ]
        );

        $user->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'suspension_reason' => $validated['reason'],
        ]);

        return back()->with('success', 'User has been suspended successfully.');
    }

    public function reinstate(User $user): RedirectResponse
    {
        if ($user->status !== 'suspended') {
            return back()->with('error', 'This user is not suspended.');
        }

        $user->update([
            'status' => 'active',
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        return back()->with('success', 'User has been reinstated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
