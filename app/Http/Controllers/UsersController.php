<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Arr;
use Yajra\Datatables\Datatables;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserCollection;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::with('roles')->get());
    }

    public function show(User $user)
    {
        //$usuario->load('roles');
        // return view('usuarios.show', compact('usuario'));
        // return Cache::remember('roles', 64500, function () {
        return new UserResource(User::with('roles')->get()->find($user));
        // })

    }

    public function store()
    {
        $this->validate(request(), [
            'name' => 'required|max:120',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        request()->merge(['email' => trim(request()->email), 'password' => bcrypt(request()->password)]);

        $user = User::create(request()->except(['password_confirmation', 'roles']));

        if (request()->roles) {
            $user->assignRole(Arr::pluck(request()->roles, 'id'));
        }
        return response(['mensaje' => "Se creó el usuario", 'user' => $user]);
    }

    public function update(User $user)
    {
        $this->validate(request(), [
            'name' => 'max:120',
            'email' => 'email',
        ]);
        if (request('password')) {
            $this->validate(request(), [
                'password' => 'required|min:6|confirmed'
            ]);
            $user->fill([
                'password' => Hash::make(request()->password)
            ]);
        }
        if (request()->name) {
            $user->fill([
                'name' => request()->name,
            ]);
        }
        if (request()->email) {
            $user->fill([
                'email' => trim(request()->email)
            ]);
        }
        if (isset(request()->activated)) {
            $user->fill([
                'activated' => request()->activated ? 1 : 0
            ]);
        }
        $user->save();
        if (request()->roles) {
            $user->roles()->sync(Arr::pluck(request()->roles, 'id'));
        }
        return response(['mensaje' => "Se actualizó el usuario {$user->name}", 'user' => new UserResource($user)]);
    }

    public function destroy($user)
    {
        $user =  User::find($user);
        if (!$user) {
            return response(['mensaje' => 'No se encontró el usuario'], 404);
        }
        $user->delete();
        return response(['mensaje' => "Se borró el usuario", 'user' => $user]);
    }

    public function updateProfile()
    {
        $user = auth()->user();

        request()->validate([
            'name' => 'required|min:4|max:120',
            'email' => 'required|email',
        ]);

        if (request('password')) {
            request()->validate([
                'password' => 'required|min:6|confirmed'
            ]);

            request()->merge(['password' => Hash::make(request()->password)]);
        };
        $user->update(collect(request()->except(['password_confirmation']))->filter()->toArray());
        return response(['user' => new UserResource($user), 'message' => 'Se actualizó su perfil correctamente']);
    }

    public function getUsers()
    {
        if (!request()->ajax()) {
            return redirect()->route('users.index');
        }
        // return 'hola';
        return Datatables::of(User::with('roles')->select('users.*'))
            ->addColumn('roles_string', function ($query) {
                return $query->roles->pluck('name')->implode(' / ');
            })
            ->addColumn('notificaciones_string', function ($query) {
                return $query->notificaciones;
            })
            ->make(true);
    }

    public function avatar()
    {
        $nombre = auth()->user()->avatar;
        $this->validate(request(), [
            'archivo' => ['required', 'image']
        ]);
        if ($nombre) {
            $ruta = "public/avatars";
            $archivo = "$ruta/$nombre";
            $this->deleteFile($archivo);
        }
        $file = basename(request()->file('archivo')->store('avatars', 'public'));
        auth()->user()->update(['avatar' =>  $file]);
        return $file;
    }

    protected function removeAvatar()
    {
        $nombre = auth()->user()->avatar;
        $ruta = "public/avatars";
        $archivo = "$ruta/$nombre";
        $deleted = $this->deleteFile($archivo);

        if ($deleted) {
            auth()->user()->update(['avatar' =>  null]);
            return response()->json(['mensaje' => 'Archivo Borrado'], 200);
        } else {
            return response()->json(['mensaje' => 'El archivo no pudo borrarse'], 404);
        }
    }

    public function deleteFile($filePath)
    {
        if (Storage::exists($filePath)) {
            return Storage::delete($filePath);
        } else return false;
    }
}
