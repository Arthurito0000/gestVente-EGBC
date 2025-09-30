<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
        return view('users.edit', compact('user', 'roles', 'userRole'));
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
    public function updateRolePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('users.roles')
            ->with('success', "Permissions du rôle {$role->name} mises à jour avec succès !");
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
    public function getRolePermissions(Role $role)
    {
        return response()->json([
            'permissions' => $role->permissions->pluck('name')->toArray()
        ]);
    }
}
