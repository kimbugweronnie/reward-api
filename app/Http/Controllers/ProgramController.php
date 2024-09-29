<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ProgramRequest;
use App\Http\Requests\ProgramUpdateRequest;
use App\Services\ProgramService;

class ProgramController extends Controller
{
    public $programservice;
    public function __construct(ProgramService $programservice) {
        $this->programservice = $programservice; 
    }
   
    public function index()
    {
        return $this->programservice->getPrograms();
    }

    public function store(ProgramRequest $request)
    {
        return $this->programservice->createProgram($request->validated());
    }

    public function show($id)
    {
        return $this->programservice->getProgram($id);
    }

    public function update(ProgramUpdateRequest $request, $id)
    {
        return $this->programservice->updateProgram($request,$id);
    }

    public function destroy($id)
    {
        return $this->programservice->destroy($id);
    }
}
