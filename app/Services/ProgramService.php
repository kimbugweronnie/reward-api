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

    public function __construct(Program $program)
    {
        $this->program = $program;
    }

    /**
     * Create a new program
     */
    public function createProgram($request):object
    {
        try {
            $program = $this->createProgramRecord($request);
            return $this->sendResponse($program, 201);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Error creating program');
        }
    }

    /**
     * Get all programs
     */
    public function getPrograms():object
    {
        $programs = $this->getAllPrograms();
        return $this->sendGetResponse(ProgramResource::collection($programs), 200);
    }

    /**
     * Get a single program by id
     */
    public function getProgram($id):object
    {
        $program = $this->findProgramById($id);
        return $this->sendGetResponse(new ProgramResource($program), 200);
    }

    /**
     * Update a program
     */
    public function updateProgram($request, $id):object
    {
        try {
            $program = $this->findProgramById($id);
            $this->updateProgramRecord($program, $request);
            return $this->sendResponse($program, 201);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Error updating program');
        }
    }

    /**
     * Delete a program by id
     */
    public function destroy($id):object
    {
        $this->deleteProgram($id);
        return $this->messageSubscription('Program has been deleted', 200);
    }

   

    /**
     * Create program record
     */
    private function createProgramRecord($request):object
    {
        return $this->program->createprogram($request);
    }

    /**
     * Fetch all programs
     */
    private function getAllPrograms():object
    {
        return $this->program->all();
    }

    /**
     * Find program by ID
     */
    private function findProgramById($id):object
    {
        return $this->program->program($id);
    }

    /**
     * Update program record
     */
    private function updateProgramRecord($program, $request):void
    {
        $program->update($request->validated());
    }

    /**
     * Delete program record
     */
    private function deleteProgram($id):void
    {
        $this->program::destroy($id);
    }

    /**
     * Handle any errors
     */
    private function handleError(\Exception $e, $message):object
    {
        return $this->messageSubscription($message, 500);
    }
}
