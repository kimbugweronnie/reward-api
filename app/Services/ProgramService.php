<?php
namespace App\Services;
use App\Models\Program;
use App\Http\Requests\ProgramRequest;
use App\Http\Requests\ProgramUpdateRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProgramResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;


class ProgramService extends Controller
{
    private $program;

    public function __construct(Program $program) {
        $this->program = $program;
    }

    public function createProgram($request)
    {
        $program = $this->program->createprogram($request);
        return $this->sendResponse($program, 200);
    }

    public function getPrograms()
    {
        $programs = $this->program->all();  
        return $this->sendResponse(ProgramResource::collection($programs), 200);
    }

    public function getProgram($id)
    {
        $program=$this->program->program($id);
        return $this->sendResponse(new ProgramResource($program), 200);
    }

    public function updateProgram($request,$id)
    { 
        $program=$this->program->program($id);
        $program->update($request->validated());
        return $this->sendResponse($program, 200);
    }
    
    public function destroy($id)
    {
        $this->program::destroy($id);
        return $this->messageSubscription("Program has been deleted", 200);  
    }

}