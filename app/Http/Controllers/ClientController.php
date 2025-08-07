<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    private $clientRepositoryInterface;

    public function __construct(ClientRepositoryInterface $clientRepositoryInterface)
    {
        $this->clientRepositoryInterface = $clientRepositoryInterface;
    }

    public function index()
    {
        return response($this->clientRepositoryInterface->orderBy('createdDate')->all(), 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'companyName' => "required:clients,companyName,$id,id,deleted_at,NULL",
            'email' => "required|unique:clients,email,$id,id,deleted_at,NULL"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 409);
        }
        return  response($this->clientRepositoryInterface->update($request->all(), $id), 201);
    }

    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'companyName' => "required:clients,companyName,NULL,id,deleted_at,NULL",
            'email' => "required|unique:clients,email,NULL,id,deleted_at,NULL"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 409);
        }
        return  response($this->clientRepositoryInterface->create($request->all()), 201);
    }

    public function get($id)
    {
        $fileRequest = $this->clientRepositoryInterface->find($id);
        return response($fileRequest, 201);
    }

    public function delete($id)
    {
        return  response($this->clientRepositoryInterface->delete($id), 204);
    }
}
