<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private const ROLES = ['admin', 'user'];   // same list as the Blade $roles

    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $kw = $request->search;
                $q->where(fn ($w) => $w->where('name', 'like', "%{$kw}%")
                                       ->orWhere('email', 'like', "%{$kw}%"));
            })
            ->when(in_array($request->role, self::ROLES, true), fn ($q) => $q->where('role', $request->role))
            ->when($request->status === 'active',    fn ($q) => $q->where('status', true))
            ->when($request->status === 'suspended', fn ($q) => $q->where('status', false))
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $row = User::selectRaw(
            "COUNT(*) as total,
             SUM(role = 'admin') as admin,
             SUM(status = 1) as active,
             SUM(status = 0) as suspended"
        )->first();

        $counts = [
            'total'     => (int) ($row->total ?? 0),
            'admin'     => (int) ($row->admin ?? 0),
            'active'    => (int) ($row->active ?? 0),
            'suspended' => (int) ($row->suspended ?? 0),
        ];

        return view('settings.index', compact('users', 'counts'));
    }

    public function updateUser(Request $request)
    {
        $data = $request->validate([
            'user_id'   => ['required', 'integer', 'exists:users,id'],
            'role'      => ['required', Rule::in(self::ROLES)],
            'status' => ['required', 'boolean'],
        ]);


        $me       = $request->user();
        $newRole  = $data['role'];
        $newState = (bool) $data['status'];

        // 1. nobody can change their own access (the UI locks it, the server enforces it)
        if ((int) $data['user_id'] === $me->id) {
            return back()->with('error', 'You cannot change your own role or status.');
        }

        $error = DB::transaction(function () use ($data, $newRole, $newState, $me) {
            // lock the row so two admins cannot race each other
            $target = User::lockForUpdate()->findOrFail($data['user_id']);

            // 2. never remove the last active admin
            $losesAdmin = $target->role === 'admin' && $target->status
                          && ($newRole !== 'admin' || !$newState);

            if ($losesAdmin) {
                $otherAdmins = User::where('role', 'admin')
                    ->where('status', true)
                    ->where('id', '!=', $target->id)
                    ->lockForUpdate()
                    ->count();

                if ($otherAdmins === 0) {
                    return 'There must be at least one active admin.';
                }
            }

            $before = ['role' => $target->role, 'status' => (bool) $target->status];

            if ($before['role'] === $newRole && $before['status'] === $newState) {
                return 'nochange';
            }

            $target->role      = $newRole;
            $target->status = $newState;
            $target->save();

            // 3. suspended or demoted users are signed out everywhere
            if (!$newState || $before['role'] !== $newRole) {
                $this->endSessions($target);
            }

            Log::info('User access changed', [
                'by'     => $me->id,
                'target' => $target->id,
                'from'   => $before,
                'to'     => ['role' => $newRole, 'status' => $newState],
            ]);

            return null;
        });

        if ($error === 'nochange') {
            return back()->with('success', 'No changes were made.');
        }
        if ($error) {
            return back()->with('error', $error);
        }

        return back()->with('success', 'User updated.');
    }

    /** works when SESSION_DRIVER=database; other drivers rely on the middleware below */
    private function endSessions(User $user): void
    {
        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
        }

        $user->forceFill(['remember_token' => null])->save();   // kills "remember me" cookies
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
