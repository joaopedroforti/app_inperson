<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Occurrence; // Certifique-se de ter um modelo Occurrence
use Illuminate\Support\Facades\Auth; // Para acessar o usuário autenticado
use Illuminate\Support\Facades\Session; // Para acessar dados da sessão
use Illuminate\Support\Facades\Log; // Adicionado para logging

class OccurrenceController extends Controller
{
    /**
     * Armazena uma nova ocorrência no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Loga todos os dados recebidos na requisição para depuração
        Log::info('Incoming Occurrence Request:', $request->all());

        // Validação dos dados da requisição
        $request->validate([
            'id_person' => 'required|integer',
            'writer' => 'required|string|max:255',
            'rule_writer' => 'required|string|max:255',
            'text' => 'required|string',
        ]);

        try {
            // Obter id_company da sessão
            $idCompany = Session::get('company_id');

            // Verificar se id_company está disponível
            if (is_null($idCompany)) {
                // Retorna um erro 400 se o ID da empresa não for encontrado na sessão
                return response()->json([
                    'success' => false,
                    'message' => 'ID da empresa não encontrado na sessão. Certifique-se de que a sessão está configurada corretamente.'
                ], 400);
            }

            // Criar uma nova ocorrência
            $occurrence = Occurrence::create([
                'id_company' => $idCompany,
                'id_person' => $request->id_person,
                'writer' => $request->writer,
                'rule_writer' => $request->rule_writer,
                'text' => $request->text,
                'date' => now(), // Define a data e hora atuais
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ocorrência adicionada com sucesso!',
                'occurrence' => $occurrence // Retorna a ocorrência criada
            ], 201);

        } catch (\Exception $e) {
            // Em caso de erro, retorna uma resposta de erro
            Log::error('Erro ao adicionar ocorrência: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar ocorrência. Por favor, tente novamente mais tarde.'
            ], 500);
        }
    }
    public function update(Request $request, $id_occourrence)
    {
        // Validação dos dados da requisição
        $request->validate([
            'text' => 'required|string',
        ]);

        try {
            $occurrence = Occurrence::find($id_occourrence);

            if (!$occurrence) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocorrência não encontrada.'
                ], 404);
            }

            // Opcional: Verificar se o usuário logado é o autor da anotação
            // Isso adiciona uma camada de segurança no backend
            // if ($occurrence->writer !== Auth::user()->name) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Você não tem permissão para editar esta anotação.'
            //     ], 403); // Forbidden
            // }

            $occurrence->text = $request->text;
            $occurrence->save();

            return response()->json([
                'success' => true,
                'message' => 'Ocorrência atualizada com sucesso!',
                'occurrence' => $occurrence
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar ocorrência: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar ocorrência. Por favor, tente novamente mais tarde.'
            ], 500);
        }
    }
    // Você pode adicionar outros métodos aqui, como index, show, update, destroy, etc.
}
