<?php

namespace App\Http\Controllers;

use DataTables;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\RolesResource;

class RolesController extends Controller
{

    public function index()
    {
        return RolesResource::collection(Role::with('permissions')->orderBy('name')->get());
    }

    public function store()
    {
        request()->validate(['name' => 'required|unique:roles|max:255']);
        $rol = Role::create(['guard_name' => 'web', 'name' => request()->name]);
        if (!empty(request()->permissions)) {
            $rol->permissions()->sync(Arr::pluck(request()->permissions, 'id'));
        }
        return response(['mensaje' => "Se creó el rol $rol->name", 'role' => $rol]);
    }

    public function show(Role $role)
    {
        return new RolesResource($role);
    }

    public function update(Role $role)
    {
        request()->validate(['name' => 'required|max:255|unique:roles,name,' . $role->id]);
        $role->update(request()->except(['permisos']));
        if (request()->permissions) {
            $role->permissions()->sync(Arr::pluck(request()->permissions, 'id'));
        }
        return response(['mensaje' => "Se editó el rol $role->name correctamente!"]);
    }

    public function destroy($role)
    {
        $role = Role::find($role);
        if (!$role) {
            return response(['mensaje' => 'No se encontró el rol'], 404);
        }
        if ($role->name == "Superadmin") {
            return response(['mensaje' => 'No puedes el rol de Superadmin!'], 422);
        }
        $role->delete();
        return response(['mensaje' => "Rol $role->name Borrado!"]);
    }

    public function selectRoles()
    {
        return RolesResource::collection(Role::all()->orderBy('name')->get());
    }

    public function getRoles()
    {
        if (!request()->ajax()) {
            return redirect()->route('roles.index');
        }
        return Datatables::of(Role::with('permissions')->select('roles.*'))
            ->addColumn('permissions_string', function ($query) {
                return $query->permissions->pluck('name')->implode(' / ');
            })
            ->make(true);
    }
}
