<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Notifications\PasswordChangedNotification;

class UserManagementController extends Controller
{
    /**
     * Afficher la liste des utilisateurs
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $users = User::with('roles')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(10);
        
        // Conserver les paramètres de recherche dans la pagination
        $users->appends($request->query());

        if ($request->ajax()) {
            return view('users.partials.table', compact('users'))->render();
        }

        return view('users.index', compact('users', 'search'));
    }

    /**
     * Afficher le formulaire de création d'utilisateur
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'statut' => 'sometimes|in:actif,inactif',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'statut' => $request->get('statut', 'actif'), // Par défaut actif
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès !');
    }

    /**
     * Afficher les détails d'un utilisateur
     */
    public function show(User $user)
    {
        $user->load('roles.permissions');
        return view('users.show', compact('user'));
    }

    /**
     * Afficher le formulaire d'édition d'utilisateur
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRole = $user->roles->first()?->name;
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('-', $permission->name)[0];
        });
        $userDirectPermissions = $user->permissions->pluck('name')->toArray();
        return view('users.edit', compact('user', 'roles', 'userRole', 'permissions', 'userDirectPermissions'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'statut' => 'sometimes|in:actif,inactif',
            'direct_permissions' => 'sometimes|array',
            'direct_permissions.*' => 'exists:permissions,name',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        if ($request->has('statut')) {
            $updateData['statut'] = $request->statut;
        }
        
        $user->update($updateData);

        // Synchroniser le rôle
        $user->syncRoles([$request->role]);

        // Synchroniser les permissions directes (supplémentaires par utilisateur)
        if ($request->has('direct_permissions')) {
            $user->syncPermissions($request->input('direct_permissions', []));
        } else {
            // Si aucune case n'est envoyée, retirer toutes les permissions directes
            $user->syncPermissions([]);
        }

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès !');
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte !');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès !');
    }

    /**
     * Afficher la gestion des rôles et permissions
     */
    public function roles()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('-', $permission->name)[0];
        });

        return view('users.roles', compact('roles', 'permissions'));
    }

    /**
     * Mettre à jour les permissions d'un rôle
     */
    public function updateRolePermissions(Request $request, $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        // Récupérer le rôle par nom (ou ID si nécessaire)
        $roleModel = Role::where('name', $role)->first();
        if (!$roleModel && is_numeric($role)) {
            $roleModel = Role::findOrFail((int)$role);
        }
        if (!$roleModel) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Rôle introuvable.'], 404);
            }
            return redirect()->route('users.roles')->with('error', 'Rôle introuvable.');
        }

        $roleModel->syncPermissions($request->permissions ?? []);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => "Permissions du rôle {$roleModel->name} mises à jour avec succès !"]);
        }

        return redirect()->route('users.roles')
            ->with('success', "Permissions du rôle {$roleModel->name} mises à jour avec succès !");
    }

    /**
     * Changer le statut d'un utilisateur (actif/inactif)
     */
    public function toggleStatus(User $user)
    {
        // Empêcher la modification de son propre statut
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Vous ne pouvez pas modifier votre propre statut !');
        }

        // Seuls les administrateurs peuvent modifier le statut
        if (!auth()->user()->can('manage-users')) {
            return redirect()->route('users.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation de modifier le statut des utilisateurs.');
        }

        $nouveauStatut = $user->statut === 'actif' ? 'inactif' : 'actif';
        $user->update(['statut' => $nouveauStatut]);

        $message = $nouveauStatut === 'actif' 
            ? "L'utilisateur {$user->name} a été activé avec succès !"
            : "L'utilisateur {$user->name} a été désactivé avec succès !";

        return redirect()->route('users.index')
            ->with('success', $message);
    }

    /**
     * Obtenir les permissions d'un rôle (pour AJAX)
     */
    public function getRolePermissions($role)
    {
        $roleModel = Role::where('name', $role)->first();
        if (!$roleModel && is_numeric($role)) {
            $roleModel = Role::findOrFail((int)$role);
        }
        if (!$roleModel) {
            return response()->json(['permissions' => []]);
        }

        return response()->json([
            'permissions' => $roleModel->permissions->pluck('name')->toArray()
        ]);
    }

    /**
     * Afficher l'interface de réinitialisation d'urgence
     */
    public function showEmergencyReset()
    {
        $users = User::where('statut', 'actif')->get();
        $recentLogs = PasswordResetLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        $stats = PasswordResetLog::getSecurityStats(30);

        return view('users.emergency-reset', compact('users', 'recentLogs', 'stats'));
    }

    /**
     * Réinitialiser le mot de passe d'urgence par un administrateur
     */
    public function emergencyPasswordReset(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'new_password' => 'required|min:8|confirmed',
            'reason' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($request->user_id);
        
        // Empêcher la modification de son propre mot de passe via cette interface
        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'user_id' => 'Vous ne pouvez pas utiliser cette interface pour votre propre compte.'
            ]);
        }

        // Logger l'action d'urgence
        PasswordResetLog::create([
            'email' => $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'action' => 'emergency_reset',
            'status' => 'success',
            'details' => [
                'admin_user' => auth()->user()->email,
                'reason' => $request->reason,
                'timestamp' => now()->toISOString(),
            ],
        ]);

        // Mettre à jour le mot de passe
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Envoyer la notification
        $user->notify(new PasswordChangedNotification(
            $request->ip(),
            $request->userAgent()
        ));

        return redirect()->route('users.emergency-reset')
            ->with('success', "Mot de passe réinitialisé avec succès pour {$user->name}. L'utilisateur a été notifié par email.");
    }

    /**
     * Envoyer un lien de réinitialisation d'urgence
     */
    public function sendEmergencyResetLink(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($request->user_id);

        // Logger l'action
        PasswordResetLog::create([
            'email' => $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'action' => 'emergency_link',
            'status' => 'success',
            'details' => [
                'admin_user' => auth()->user()->email,
                'reason' => $request->reason,
                'timestamp' => now()->toISOString(),
            ],
        ]);

        // Envoyer le lien de réinitialisation
        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->route('users.emergency-reset')
                ->with('success', "Lien de réinitialisation envoyé à {$user->name} ({$user->email}).");
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Obtenir les statistiques de sécurité
     */
    public function getSecurityStats()
    {
        $stats = PasswordResetLog::getSecurityStats(30);
        return response()->json($stats);
    }
}
