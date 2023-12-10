<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Traits\HttpResponses;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class ClientController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        $params = $request->query();

        $clients = Client::query();

        if ($request->has('name') && !empty($params['name'])) {
            $clients->where('name', 'ilike', "%" . $params['name'] . "%");
        }

        if ($request->has('cpf') && !empty($params['cpf'])) {
            $clients->where('cpf', $params['cpf']);
        }

        if ($request->has('date_birth') && !empty($params['date_birth'])) {
            $clients->where('date_birth', $params['date_birth']);
        }

        return $clients->get();
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();

            $request->validate([
                'name' => 'string|required',
                'email' => 'email|required|unique:clients',
                'date_birth' => 'date_format:Y-m-d|required',
                'cpf' => 'string|required|unique:clients',
                'address' => 'string|required'
            ]);

            $client = Client::create($data);

            return $client;
        } catch (\Exception $exception) { // Em caso de erro
            return $this->error($exception->getMessage(), HttpFoundationResponse::HTTP_BAD_REQUEST);
        }
    }
    public function update($id, Request $request)
    {
        try {
            $data = $request->only('name', 'email', 'date_birth', 'address');

            $request->validate([
                'name' => 'string',
                'email' => 'email|unique:clients',
                'date_birth' => 'date_format:Y-m-d',
                'address' => 'string'
            ]);

            $client = Client::find($id);
            if (!$client) return $this->error('Cliente não encontrado', HttpFoundationResponse::HTTP_NOT_FOUND);
            $client->update($data);

            return $client;
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), HttpFoundationResponse::HTTP_BAD_REQUEST);
        }
    }

    public function destroy($id)
    {
        $client = Client::find($id);
        if (!$client) return $this->error('Cliente não encontrado', HttpFoundationResponse::HTTP_NOT_FOUND);
        $client->delete();
        return $this->error('', HttpFoundationResponse::HTTP_NO_CONTENT);
    }
}
