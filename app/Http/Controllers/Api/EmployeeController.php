<?php

namespace App\Http\Controllers\Api;

use App\Models\Person;
use App\Models\JobRole;
use App\Models\Department;
use App\Models\CalculationResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EmployeeController extends BaseApiController
{
    public function index(Request $request)
    {
        // Verifica permissão no plan_config (employees -> get)
        $company = $request->get('company');
        if (!$this->checkAccess($company, 'employees', 'get')) {
            return response()->json(['error' => 'Acesso negado à API de colaboradores (GET)'], 403);
        }

        $id_company   = $company->id_company;
        $filterEmail  = $request->input('email');
        $perPage      = (int) $request->input('per_page', 15);
        $page         = (int) $request->input('page', 1);
        $orderByInput = $request->input('order_by', 'full_name');
        $orderDir     = strtolower($request->input('order_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        // Whitelist de ordenação
        $orderable = ['full_name', 'personal_email', 'corporate_email', 'created_at'];
        $orderBy   = in_array($orderByInput, $orderable, true) ? $orderByInput : 'full_name';

        // Chave de cache por empresa + parâmetros
        $cacheKey = 'api:employees:' . $id_company . ':' . md5(json_encode([
            'email'     => $filterEmail,
            'order_by'  => $orderBy,
            'order_dir' => $orderDir,
            'per_page'  => $perPage,
            'page'      => $page,
        ]));

        $payload = Cache::remember($cacheKey, 60, function () use (
            $id_company, $filterEmail, $orderBy, $orderDir, $perPage, $page
        ) {
            // Base query
            $query = Person::where('id_company', $id_company)
                ->where('person_type', 'collaborator');

            // Filtro por e-mail (pessoal ou corporativo)
            if ($filterEmail) {
                $query->where(function ($q) use ($filterEmail) {
                    $q->where('personal_email', 'like', "%{$filterEmail}%")
                      ->orWhere('corporate_email', 'like', "%{$filterEmail}%");
                });
            }

            $query->orderBy($orderBy, $orderDir);

            // Paginação
            $collaborators = $query->paginate($perPage, ['*'], 'page', $page);

            $data = [];
            foreach ($collaborators as $person) {
                // Começa com todos os campos do banco
                $item = $person->toArray();

                // --- Remoções obrigatórias ---
                unset($item['step'], $item['id_person'], $item['id_company'], $item['role'], $item['id_gender']);

                // --- Captura campos que vão para grupos (para depois remover do topo) ---
                $address = [
                    'country'            => $person->country ?? null,
                    'zip_code'           => $this->digitsOnly($person->zip_code ?? null),
                    'address_number'     => $person->address_number ?? null,
                    'address_district'   => $person->address_district ?? null,
                    'address_city'       => $person->address_city ?? null,
                    'address_state'      => $person->address_state ?? null,
                    'address_complement' => $person->address_complement ?? null,
                ];
                $contact = [
                    'cellphone'       => $person->cellphone ?? null,
                    'phone'           => $person->phone ?? null,
                    'emergency_phone' => $person->emergency_phone ?? null,
                    'personal_email'  => $person->personal_email ?? null,
                    'corporate_email' => $person->corporate_email ?? null,
                ];
                $payment = [
                    'bank'    => $person->bank ?? null,
                    'agency'  => $this->digitsOnly($person->agency ?? null),
                    'account' => $person->account ?? null,
                    'pix_key' => $person->pix_key ?? null,
                ];
                $docs = $this->sanitizeDocs([
                    'foreigner_document'   => $person->foreigner_document ?? null,
                    'military_certificate' => $person->military_certificate ?? null,
                    'cpf'                  => $person->cpf ?? null,
                    'cnpj'                 => $person->cnpj ?? null,
                    'rg'                   => $person->rg ?? null,
                    'rg_issue_date'        => $person->rg_issue_date ?? null, // mantém data
                    'rg_issuer'            => $person->rg_issuer ?? null,
                    'cnh'                  => $person->cnh ?? null,
                    'pis'                  => $person->pis ?? null,
                ]);

                // --- Remove do topo todos os campos que foram para os grupos ---
                foreach ([
                    'country','zip_code','address_number','address_district','address_city',
                    'address_state','address_complement',
                    'cellphone','phone','emergency_phone','personal_email','corporate_email',
                    'bank','agency','account','pix_key',
                    'foreigner_document','military_certificate','cpf','cnpj','rg','rg_issue_date',
                    'rg_issuer','cnh','pis','profile_pic_base64'
                ] as $k) {
                    unset($item[$k]);
                }

                // --- Enriquecimentos e mapeamentos ---
                $item['active']     = ($person->status ?? null) === 'active';
                $item['Gender']     = $this->mapGender($person->id_gender);
                $item['department'] = Department::where('id_department', $person->department)->value('description');
                $item['job_role']   = JobRole::where('id_job', $person->role)->value('description');

                // Último cálculo
                $lastCalculation = CalculationResult::where('id_company', $id_company)
                    ->where('calculation_type', 1)
                    ->where('id_entity', $person->id_person)
                    ->orderByDesc('calculed_at')
                    ->first();

                $item['calculation'] = $lastCalculation ? [
                    'profile'     => $lastCalculation->result_name,
                    'attributes'  => $lastCalculation->attributes,
                    'skills'      => $lastCalculation->skills,
                    'calculed_at' => $lastCalculation->calculed_at,
                ] : null;

                // --- Anexa os grupos ---
                $item['address']   = $address;
                $item['contact']   = $contact;
                $item['documents'] = [
                    'foreigner_document'   => $docs['foreigner_document'],
                    'military_certificate' => $docs['military_certificate'],
                    'cpf'                  => $docs['cpf'],
                    'cnpj'                 => $docs['cnpj'],
                    'rg'                   => $docs['rg'],
                    'rg_issue_date'        => $person->rg_issue_date ?? null,
                    'rg_issuer'            => $this->removeAccents($person->rg_issuer ?? '') ?: null,
                    'cnh'                  => $docs['cnh'],
                    'pis'                  => $docs['pis'],
                ];
                $item['payment']   = $payment;

                // ⚠️ OBRIGATÓRIO: profile_pic_base64 como ÚLTIMO campo
                $item['profile_pic_base64'] = $person->profile_pic_base64 ?? null;

                $data[] = $item;
            }

            return [
                'data' => $data,
                'pagination' => [
                    'current_page' => $collaborators->currentPage(),
                    'per_page'     => $collaborators->perPage(),
                    'total'        => $collaborators->total(),
                    'last_page'    => $collaborators->lastPage(),
                ],
                'order' => [
                    'by'  => $orderBy,
                    'dir' => $orderDir,
                ],
                'filters' => [
                    'email' => $filterEmail,
                ],
                'cached' => true,
            ];
        });

        return response()->json($payload);
    }

    /** 1->Feminino, 2->Masculino, 3->Outros */
    protected function mapGender($id): ?string
    {
        $map = [1 => 'Feminino', 2 => 'Masculino', 3 => 'Outros'];
        return $map[(int) $id] ?? null;
    }

    /** Remove máscara/acentos dos documentos */
    protected function sanitizeDocs(array $docs): array
    {
        $out = [];
        foreach ($docs as $k => $v) {
            if ($v === null) { $out[$k] = null; continue; }

            // Só dígitos para estes campos
            if (in_array($k, ['cpf','cnpj','cnh','pis','rg','foreigner_document'], true)) {
                $out[$k] = $this->digitsOnly($v);
                continue;
            }
            if ($k === 'rg_issue_date') { // manter data original
                $out[$k] = $v;
                continue;
            }

            // Textos: remove acentos
            $out[$k] = $this->removeAccents((string) $v);
        }
        return $out;
    }

    protected function digitsOnly($value): ?string
    {
        if ($value === null) return null;
        return preg_replace('/\D+/', '', (string) $value);
    }

    protected function removeAccents(string $str): string
    {
        if ($str === '') return '';
        if (class_exists('\Normalizer')) {
            $str = \Normalizer::normalize($str, \Normalizer::FORM_D);
            $str = preg_replace('/\pM/u', '', $str);
            return $str;
        }
        $from = 'ÀÁÂÃÄÅàáâãäåÈÉÊËèéêëÌÍÎÏìíîïÒÓÔÕÖØòóôõöøÙÚÛÜùúûüÇçÑñÝýÿ';
        $to   = 'AAAAAAaaaaaaEEEEeeeeIIIIiiiiOOOOOOooooooUUUUuuuuCcNnYyy';
        return strtr($str, $from, $to);
    }
}
