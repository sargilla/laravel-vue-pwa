<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionsResource;
use Spatie\Permission\Models\Permission as Permiso;

class PermissionsController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        return PermissionsResource::collection(Permiso::with('roles')->get());
    }


    public function store()
    {
        request()->validate(['name' => 'required|max:40|unique:permissions']);
        $permiso = Permiso::create(['guard_name' => 'web', 'name' => request()->name]);
        if (request()->roles) {
            $permiso->roles()->attach(Arr::pluck(request()->roles, 'id'));
        }
        return response(['mensaje' => "Se creó el permiso $permiso->name", 'permiso' => $permiso]);
    }

    public function show($id)
    {
        return new PermissionsResource(Permiso::findById($id));
    }

    public function update(Permiso $permission)
    {
        request()->validate(['name' => 'required|max:40']);
        $permission->update(['name' => request()->name]);
        $permission->syncRoles(Arr::pluck(request()->roles, 'id'));
        return response(["mensaje" => "Se editó el permiso $permission->name correctamente!"]);
    }

    public function destroy($permission)
    {
        $permission = Permiso::find($permission);
        if (!$permission) {
            return response(['mensaje' => 'No se encontró el permiso'], 404);
        }
        if ($permission->name == "admin-usuarios") {
            return response()->json('No puedes borrar este permiso!', 401);
        }
        $permission->delete();
        return response(["mensaje" => "Permiso $permission->name Borrado!"]);
    }

    public function getPermissions()
    {
        if (!request()->ajax()) {
            return redirect()->route('permissions.index');
        }
        return Datatables::of(Permiso::with('roles'))
            ->addColumn('roles_string', function ($query) {
                return $query->roles()->pluck('name')->implode(' / ');
            })
            ->make(true);
    }
}
