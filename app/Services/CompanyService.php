<?php
namespace App\Services;
use App\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;


class CompanyService extends Controller
{
    private $company;

    public function __construct(Company $company) {
        $this->company = $company;
    }

    public function createCompany($request)
    {
        $company = $this->company->createCompany($request);
        return $this->sendResponse($company, 201);
    }

}