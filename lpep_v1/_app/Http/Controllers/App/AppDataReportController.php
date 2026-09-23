<?php

namespace App\Http\Controllers\App;

use App\Models\Farm;
use App\Models\CtlWeightInfo;
use App\Models\CtlDiseaseInfo;
use App\Models\CtlVaccineInfo;
use App\Models\CtlAbortionInfo;
use App\Models\CtlImpregnation;
use App\Models\CtlPregnancyExam;
use App\Models\CtlMilkProduction;
use App\Models\CtlFoodConsumption;
use App\Http\Controllers\Controller;

class AppDataReportController extends Controller
{
    public function milkProduction()
    {
        $data['ctlMilkProductions'] = CtlMilkProduction::query()
            //->with('cattle', 'farm')
            ->with('cattle', 'farm', 'customer')
            ->orderByDesc('date')
            ->get();

        return view('app-reports.milk-production', $data);
    }

    public function vaccine()
    {
        $data['ctlVaccineInfo'] = CtlVaccineInfo::query()
            ->with('cattle', 'farm', 'customer', 'cattleDisease', 'cattleVaccine')
            ->orderByDesc('date')
            ->get();

        return view('app-reports.vaccine', $data);
    }

    public function disease()
    {
        $data['ctlDiseaseInfo'] = CtlDiseaseInfo::query()
            ->with('cattle', 'farm', 'customer', 'cattleDisease')
            ->orderByDesc('date')
            ->get();

        return view('app-reports.disease', $data);
    }

    public function impregnation()
    {
        $data['impregnations'] = CtlImpregnation::query()
            ->with('farm', 'customer', 'manualHit', 'seedCompany')
            ->orderByDesc('pal_date')
            ->get();

        return view('app-reports.impregnation', $data);
    }

    public function pregnancy()
    {
        $data['ctlPregnancyExams'] = CtlPregnancyExam::query()
            ->with('farm', 'customer', 'cattle')
            ->orderByDesc('expected_delivery_date')
            ->get();

        return view('app-reports.pregnancy', $data);
    }

    public function abortion()
    {
        $data['ctlAbortionInfos'] = CtlAbortionInfo::query()
            ->with('farm', 'customer', 'cattle')
            ->orderByDesc('date')
            ->get();

        return view('app-reports.abortion', $data);
    }

    public function foodConsumption()
    {
        $data['ctlFoodConsumptions'] = CtlFoodConsumption::query()
            ->with('farm', 'customer', 'cattle', 'cattleFood')
            ->orderByDesc('date')
            ->get();

        return view('app-reports.food-consumption', $data);
    }

    public function weightInfo()
    {
        $data['ctlWeightInfos'] = CtlWeightInfo::query()
            ->with('farm', 'customer', 'cattle')
            ->orderByDesc('date')
            ->get();

        return view('app-reports.weight-info', $data);
    }

    public function farm()
    {
        $data['farms'] = Farm::query()
            ->with('customer')
            ->withCount('cattle', 'calves')
            ->get();

        return view('app-reports.farm', $data);
    }
}
