<?php

namespace App\Http\Resources;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $companyId = $this->properties->get('company_id');
        $company = Company::find($companyId);

        $dateString = $this->properties->get('date');
        $date = Carbon::createFromFormat('Y-m-d', $dateString)->subMonth();
        $formattedDate = $date->format('M Y');

        return [
            'id' => $this->id,
            'description' => $this->description,
            'company' => $company,
            'date' => $formattedDate,
        ];
    }
}
