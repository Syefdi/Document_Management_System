<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\PageHelperRepositoryInterface;

class PageHelperController extends Controller
{
    private $pageHelperRepository;

    public function __construct(PageHelperRepositoryInterface $pageHelperRepository)
    {
        $this->pageHelperRepository = $pageHelperRepository;
    }

    public function getAll()
    {
        return  response($this->pageHelperRepository->all(), 200);
    }

    public function getByCode($code)
    {
        return response($this->pageHelperRepository->getByCode($code), 200);
    }

    public function update(Request $request, $id)
    {
        return  response()->json($this->pageHelperRepository->update($request->all(), $id), 200);
    }

    public function getById($id)
    {
        return response($this->pageHelperRepository->find($id), 200);
    }
}
