<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CompanyRequest;
use App\Http\Requests\CompanyUpdateRequest;
use App\Services\CompanyService;

class CompanyController extends Controller
{
    private $companyService;
    public function __construct(CompanyService $companyService) {
        $this->companyService = $companyService; 
    }
   
    public function index()
    {
        //
    }

   
    public function create()
    {
        //
    }

   
    public function store(CompanyRequest $request):object
    {
        return $this->companyService->createCompany($request->validated());
    }

    
    public function show($id)
    {
        //
    }

   
    public function edit($id)
    {
        //
    }

    
    public function update(Request $request, $id)
    {
        //
    }

   
    public function destroy($id)
    {
        //
    }
}
