<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Paciente::where('ativo', true);

        if ($request->has('busca')) {
            $busca = $request->input('busca');
            $query->where(function($q) use ($busca) {
                $q->where('nome', 'like', "%$busca%")
                ->orWhere('email', 'like', "%$busca%");
            });
        }

        return $query->orderBy('nome')->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:pacientes,email',
        ]);

        $paciente = Paciente::create($request->all());

        return response()->json($paciente, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => "required|email|unique:pacientes,email,$id",
        ]);

        $paciente = Paciente::findOrFail($id);
        $paciente->update($request->all());

        return response()->json(['message' => 'Paciente atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->ativo = false;
        $paciente->save();

        return response()->json(['message' => 'Paciente desativado com sucesso']);
    }

}

