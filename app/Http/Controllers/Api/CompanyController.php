<?php

namespace App\Http\Controllers\Api;

use App\Enums\CompanyStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetVersionsCompanyRequest;
use App\Http\Requests\StoreCompanyRequest;
use App\Models\Company;
use Illuminate\Http\Response;

class CompanyController extends Controller
{
    public function getVersionsByEdrpou(GetVersionsCompanyRequest $request, string $edrpou)
    {
        $company = Company::where('edrpou', $request->route('edrpou'))
            ->with(['versions' => fn($q) => $q->latest('version_number')])
            ->firstOrFail();

        return response()->json([
            'company' => $company->name,
            'edrpou' => $company->edrpou,
            'versions' => $company->versions
        ]);
    }

    public function store(StoreCompanyRequest $request)
    {
        $data = $request->validated();

        $company = Company::firstOrNew(['edrpou' => $data['edrpou']]);
        $isNew = !$company->exists;
        $company->fill($data);

        $status = match(true) {
            $isNew => CompanyStatus::CREATED,
            $company->isDirty() => CompanyStatus::UPDATED,
            default => CompanyStatus::DUPLICATE,
        };

        if ($status !== CompanyStatus::DUPLICATE) {
            $company->save();
        }

        return response()->json([
            'status' => $status->value,
            'company_id' => $company->id,
            'version' => $company->getCurrentVersionNumber(),
        ], $status === CompanyStatus::CREATED ? Response::HTTP_CREATED : Response::HTTP_OK);
    }

}
