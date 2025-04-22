<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Paciente;
use Illuminate\Http\Request;

class AgendamentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Agendamento::with('paciente')
            ->orderBy('data', 'desc');

        if ($request->filled('nome')) {
            $query->whereHas('paciente', function ($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->nome . '%');
            });
        }

        if ($request->filled('data')) {
            $query->where('data', $request->data);
        }

        return $query->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'data' => 'required|date',
            'hora' => 'required',
            'observacao' => 'nullable|string',
        ]);

        $agendamento = Agendamento::create($request->all());

        return response()->json($agendamento, 201);
    }

    public function update(Request $request, $id)
    {
        $agendamento = Agendamento::findOrFail($id);

        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'data' => 'required|date',
            'hora' => 'required',
            'observacao' => 'nullable|string',
        ]);

        $agendamento->update($request->all());

        return response()->json(['message' => 'Agendamento atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $agendamento->delete();

        return response()->json(['message' => 'Agendamento excluído com sucesso']);
    }
}

