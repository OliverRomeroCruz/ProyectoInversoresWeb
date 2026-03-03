<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Inversion;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{

    public function misProyectos()
    {
        $user = Auth::user();

        $proyectos = Project::with('user')
            ->where('user_id', $user->id)
            ->get();

        return view('proyectosPersonales', compact('proyectos'));
    }


    public function mostrarProyecto($id)
    {
        $proyecto = Project::with('user', 'inversiones.user')->find($id);

        if (!$proyecto) {
            return redirect()->back()->with('error', 'Proyecto no encontrado');
        }

        return view('detalleProyecto', compact('proyecto'));
    }


    public function formCrearProyecto()
    {
        $user = Auth::user();

        $proyectosActivos = $user->projects()
            ->whereIn('estado', ['pendiente', 'activo'])
            ->count();

        return view('creacionProyecto', compact('proyectosActivos'));
    }

    public function crearProyecto(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'imagen_url' => 'required|url|max:500',
            'video_url' => 'nullable|url|max:500',
            'min_inversion' => 'required|numeric|min:0',
            'max_inversion' => 'required|numeric|gte:min_inversion',
            'fecha_fin' => 'required|date|after:today',
        ]);

        $user = Auth::user();

        Project::create([
            'user_id' => $user->id,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'imagen_url' => $request->imagen_url,
            'video_url' => $request->video_url,
            'min_inversion' => $request->min_inversion,
            'max_inversion' => $request->max_inversion,
            'inversion_actual' => 0,
            'fecha_fin' => $request->fecha_fin,
            'estado' => 'pendiente',
        ]);

        return redirect()->route('mis-proyectos')->with('success', 'Proyecto creado correctamente');
    }

    public function cancelarProyecto($id)
    {
        $proyecto = Project::with('inversiones.user')->find($id);

        if (!$proyecto) {
            return redirect()->back()->with('error', 'Proyecto no encontrado');
        }

        $userId = Auth::id();
        if ($proyecto->user_id != $userId) {
            return redirect()->back()->with('error', 'No tienes permiso para cancelar este proyecto');
        }

        if ($proyecto->estado !== 'activo') {
            return redirect()->back()->with('error', 'El proyecto debe estar activo para cancelarlo');
        }

        DB::transaction(function () use ($proyecto) {
            foreach ($proyecto->inversiones as $inversion) {
                $usuario = $inversion->user;
                $usuario->dinero += $inversion->monto;
                $usuario->save();
            }

            $proyecto->inversiones()->delete();
            $proyecto->estado = "cancelado";
            $proyecto->save();
        });

        return redirect()->route('detalleProyecto', ['id' => $proyecto->id])->with('success', 'Proyecto cancelado correctamente');
    }

    public function completarProyecto($id)
    {
        $proyecto = Project::with('inversiones.user')->find($id);

        if (!$proyecto) {
            return redirect()->back()->with('error', 'Proyecto no encontrado');
        }

        $userId = Auth::id();
        if ($proyecto->user_id != $userId) {
            return redirect()->back()->with('error', 'No tienes permiso para completar este proyecto');
        }

        if ($proyecto->estado !== 'activo') {
            return redirect()->back()->with('error', 'El proyecto debe estar activo para completarlo');
        }

        if ($proyecto->inversion_actual < $proyecto->min_inversion) {
            return redirect()->back()->with('error', 'El proyecto no ha alcanzado la inversión mínima');
        }

        DB::transaction(function () use ($proyecto) {
            $usuario = $proyecto->user;
            $usuario->dinero += $proyecto->inversion_actual;
            $usuario->save();

            $proyecto->inversiones()->delete();
            $proyecto->estado = "completado";
            $proyecto->save();
        });

        return redirect()->route('detalleProyecto', ['id' => $proyecto->id])->with('success', 'Proyecto completado correctamente');
    }

    public function invertir(Request $request, $id)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01'
        ]);

        $proyecto = Project::find($id);

        if (!$proyecto) {
            return redirect()->back()->with('error', 'Proyecto no encontrado');
        }

        $user = Auth::user();

        if ($request->monto > $user->dinero) {
            return redirect()->back()->with('error', 'No tienes suficiente saldo para esta inversión.');
        }

        DB::transaction(function () use ($proyecto, $user, $request) {
            $proyecto->inversiones()->create([
                'user_id' => $user->id,
                'monto' => $request->monto,
            ]);

            $user->dinero -= $request->monto;
            $user->save();

            $proyecto->inversion_actual += $request->monto;
            $proyecto->save();
        });

        return redirect()->back()->with('success', 'Inversión realizada correctamente');
    }

    public function gestionProyectosAdmin()
    {
        $proyectosPendientes = Project::with('user')
            ->where('estado', 'pendiente')
            ->simplePaginate(10, ['*'], 'pendientes');

        $proyectosActivos = Project::with('user')
            ->where('estado', 'activo')
            ->simplePaginate(10, ['*'], 'activos');

        $proyectosCancelados = Project::with('user')
            ->where('estado', 'cancelado')
            ->simplePaginate(10, ['*'], 'cancelados');

        $proyectosCompletados = Project::with('user')
            ->where('estado', 'completado')
            ->simplePaginate(10, ['*'], 'completados');

        $usuarios = \App\Models\User::where('id', '!=', auth()->id())
            ->simplePaginate(10, ['*'], 'usuarios');

        return view('panel-admin', compact(
            'proyectosPendientes',
            'proyectosActivos',
            'proyectosCancelados',
            'proyectosCompletados',
            'usuarios'
        ));
    }


    public function confirmarProyecto($id)
    {
        $proyecto = Project::find($id);

        if (!$proyecto) {
            return redirect()->back()->with('error', 'Proyecto no encontrado.');
        }

        $proyecto->estado = 'activo';
        $proyecto->save();

        return redirect()->back()->with('success', 'Proyecto aprobado correctamente.');
    }

    public function denegarProyecto($id)
    {
        $proyecto = Project::find($id);

        if (!$proyecto) {
            return redirect()->back()->with('error', 'Proyecto no encontrado.');
        }

        $proyecto->estado = 'rechazado';
        $proyecto->save();

        return redirect()->back()->with('success', 'Proyecto rechazado correctamente.');
    }

    public function cancelarProyectoAdmin($id)
    {
        $proyecto = Project::with('inversiones.user')->find($id);

        if (!$proyecto) {
            return back()->with('error', 'Proyecto no encontrado');
        }
        DB::transaction(function () use ($proyecto) {
            foreach ($proyecto->inversiones as $inversion) {
                $usuario = $inversion->user;
                $usuario->dinero += $inversion->monto;
                $usuario->save();
            }

            $proyecto->inversiones()->delete();

            $proyecto->estado = "cancelado";
            $proyecto->save();
        });

        return back()->with('success', 'Proyecto cancelado correctamente y las inversiones han sido devueltas');
    }

    public function listarProyectos()
    {
        $proyectos = Project::with('user')
            ->where('estado', 'activo')
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        $ultimosProyectos = Project::with('user')
            ->where('estado', 'activo')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('proyectos.index', compact('proyectos', 'ultimosProyectos'));
    }

    public function misInversiones()
    {
        $inversiones = Inversion::with('project')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(9);

        return view('inversionesPersonales', compact('inversiones'));
    }


    public function edit($id)
    {
        $proyecto = Project::findOrFail($id);

        return view('edit', compact('proyecto'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
        ]);

        $proyecto = Project::findOrFail($id);

        $proyecto->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()
            ->route('detalleProyecto', ['id' => $proyecto->id])
            ->with('success', 'Proyecto actualizado correctamente');
    }

    public function retirar($id)
    {
        $inversion = Inversion::with('project', 'user')->findOrFail($id);

        if ($inversion->user_id !== auth()->id()) {
            abort(403);
        }

        if ($inversion->created_at->addHours(24)->isPast()) {
            return back()->with('error', 'Ya no puedes retirar esta inversión.');
        }

        $proyecto = $inversion->project;

        if (!$proyecto) {
            return back()->with('error', 'La inversión no tiene proyecto asociado.');
        }

        DB::transaction(function () use ($inversion, $proyecto) {
            $user = $inversion->user;
            $user->dinero += $inversion->monto;
            $user->save();

            $proyecto->inversion_actual -= $inversion->monto;
            $proyecto->save();

            $inversion->delete();
        });

        return back()->with('success', 'Inversión retirada correctamente.');
    }

    public function indexJson(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $proyectos = Project::with('user')->paginate($perPage);
        return response()->json([
            'data' => $proyectos->items(),
            'pagination' => [
                'current_page' => $proyectos->currentPage(),
                'last_page' => $proyectos->lastPage(),
                'per_page' => $proyectos->perPage(),
                'total' => $proyectos->total(),
                'has_next_page' => $proyectos->hasMorePages(),
            ]
        ]);
    }

    public function showJson($id)
    {
        $proyecto = Project::with('user', 'inversiones', 'comments')->find($id);
        if (!$proyecto) {
            return response()->json(['error' => 'Proyecto no encontrado'], 404);
        }
        return response()->json($proyecto);
    }

    public function storeJson(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen_url' => 'nullable|url',
            'video_url' => 'nullable|url',
            'min_inversion' => 'required|numeric|min:0',
            'max_inversion' => 'required|numeric|min:0',
            'inversion_actual' => 'nullable|numeric|min:0',
            'fecha_fin' => 'required|date',
            'estado' => 'required|string|in:pendiente,activo,completado,cancelado',
        ]);

        $proyecto = Project::create($request->all());

        return response()->json($proyecto, 201);
    }

    public function updateJson(Request $request, $id)
    {
        $proyecto = Project::find($id);
        if (!$proyecto) {
            return response()->json(['error' => 'Proyecto no encontrado'], 404);
        }

        $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'imagen_url' => 'sometimes|url',
            'video_url' => 'sometimes|url',
            'min_inversion' => 'sometimes|numeric|min:0',
            'max_inversion' => 'sometimes|numeric|min:0',
            'inversion_actual' => 'sometimes|numeric|min:0',
            'fecha_fin' => 'sometimes|date',
            'estado' => 'sometimes|string|in:pendiente,activo,completado,cancelado',
        ]);

        $proyecto->update($request->all());

        $proyecto->update($request->all());

        return response()->json($proyecto);
    }

    public function home(Request $request)
    {
        $ultimosProyectos = Project::with('user')
            ->where('estado', 'activo')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $query = Project::with('user')->whereNotIn('estado', ['pendiente', 'rechazado']);

        if ($request->has('estado') && $request->estado != '') {
            if ($request->estado == 'activo') {
                $query->where('estado', 'activo');
            } elseif ($request->estado == 'inactivo') {
                $query->whereIn('estado', ['completado', 'cancelado']);
            }
        }

        if ($request->has('min_inversion') && $request->min_inversion != '') {
            $query->where('min_inversion', '>=', $request->min_inversion);
        }
        if ($request->has('max_inversion') && $request->max_inversion != '') {
            $query->where('max_inversion', '<=', $request->max_inversion);
        }

        if ($request->has('busqueda') && $request->busqueda != '') {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'like', '%' . $busqueda . '%')
                  ->orWhere('descripcion', 'like', '%' . $busqueda . '%');
            });
        }

        $proyectos = $query->paginate(6);

        return view('home', compact('proyectos', 'ultimosProyectos'));
    }
}
