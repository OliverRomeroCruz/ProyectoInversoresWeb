<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function create(Project $project)
    {
        if (Auth::id() !== $project->user_id) {
            return redirect()->route('detalleProyecto', $project->id)
                ->with('error', 'No tienes permisos para crear comentarios en este proyecto.');
        }

        return view('crearEditarComentario', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        if (Auth::id() !== $project->user_id) {
            return redirect()->route('detalleProyecto', $project->id)
                ->with('error', 'No tienes permisos para crear comentarios en este proyecto.');
        }

        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'imagen' => 'nullable|url|max:500',
        ]);


        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('comentarios', 'public');
        }

        $data['user_id'] = Auth::id();
        $data['project_id'] = $project->id;

        Comment::create($data);

        return redirect()->route('detalleProyecto', $project->id)
            ->with('success', 'Comentario creado correctamente.');
    }

    public function edit(Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) {
            return redirect()->route('detalleProyecto', $comment->project_id)
                ->with('error', 'No tienes permisos para editar este comentario.');
        }

        return view('crearEditarComentario', compact('comment'));
    }

    public function update(Request $request, Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) {
            return redirect()->route('detalleProyecto', $comment->project_id)
                ->with('error', 'No tienes permisos para editar este comentario.');
        }

        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'imagen' => 'nullable|url|max:500',
        ]);


        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('comentarios', 'public');
        }

        $comment->update($data);

        return redirect()->route('detalleProyecto', $comment->project_id)
            ->with('success', 'Comentario actualizado correctamente.');
    }

    public function destroy(Comment $comment)
    {
        if (Auth::id() !== $comment->user_id && Auth::user()->rol !== 'admin') {
            return redirect()->route('detalleProyecto', $comment->project_id)
                ->with('error', 'No tienes permisos para eliminar este comentario.');
        }

        $comment->delete();

        return redirect()->route('detalleProyecto', $comment->project_id)
            ->with('success', 'Comentario eliminado correctamente.');
    }

    public function destroyByAdmin(Comment $comment)
    {
        if (Auth::user()->rol !== 'admin') {
            return redirect()->route('detalleProyecto', $comment->project_id)
                ->with('error', 'No tienes permisos para eliminar este comentario.');
        }

        $comment->delete();

        return redirect()->route('detalleProyecto', $comment->project_id)
            ->with('success', 'Comentario eliminado por admin.');
    }
}
