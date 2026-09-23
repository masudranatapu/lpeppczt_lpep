<?php

namespace App\Http\Controllers\App;

use App\Models\RenewableEnergy;
use App\Models\User;
use App\Models\Union;
use Devfaysal\BangladeshGeocode\Models\District;
use Devfaysal\BangladeshGeocode\Models\Division;
use Devfaysal\BangladeshGeocode\Models\Upazila;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RenewableEnergyController extends Controller
{
    public function index()
    {
        $data = [
            'staffs' => User::active()->whereRelation('userPermission', 'role_id', ROLE_STAFF)->get(),
            'agents' => User::active()->whereRelation('userPermission', 'role_id', ROLE_AGENT)->get(),
        ];
        return view('renewable_energy.index', $data);
    }

    public function create()
    {
        $data = [
            'staffs' => User::active()->whereRelation('userPermission', 'role_id', ROLE_STAFF)->get(),
            'agents' => User::active()->whereRelation('userPermission', 'role_id', ROLE_AGENT)->get(),
            'divisions' => Division::query()->select('id', 'name', 'name')->orderBy('name')->pluck('name', 'id')
        ];
        return view('renewable_energy.create', $data);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:biogas,solar',
            'area_id' => 'required|exists:users,id',
            'client_name' => 'required|string|max:255',
            'document' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
        ]);

        try {
            DB::beginTransaction();
            $data = $request->except(['_token', '_method', 'visit_date', 'document']);
            // Visit date is authoritative server data and cannot be manually changed.
            $data['visit_date'] = now()->format('Y-m-d H:i:s');
            $data['plant_start_date'] = $data['plant_start_date'] ?: null;
            $data['plant_end_date'] = $data['plant_end_date'] ?: null;

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/renewable_energy'), $fileName);
                $data['document'] = 'uploads/renewable_energy/' . $fileName;
            }

            RenewableEnergy::create($data + [
                'created_by' => auth()->id()
            ]);
            DB::commit();
            return response()->json('Renewable Energy entry saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $entry = RenewableEnergy::with(['area', 'agent', 'district', 'upazila', 'union', 'creator'])->findOrFail($id);
        return view('renewable_energy.show', compact('entry'));
    }

    public function edit($id)
    {
        $entry = RenewableEnergy::findOrFail($id);
        $data = [
            'entry' => $entry,
            'staffs' => User::active()->whereRelation('userPermission', 'role_id', ROLE_STAFF)->get(),
            'agents' => User::active()->whereRelation('userPermission', 'role_id', ROLE_AGENT)->get(),
            'divisions' => Division::query()->select('id', 'name', 'name')->orderBy('name')->pluck('name', 'id'),
            'districts' => District::query()->where('division_id', $entry->division_id)->pluck('name', 'id'),
            'upazilas' => Upazila::query()->where('district_id', $entry->district_id)->pluck('name', 'id'),
            'unions' => Union::query()->where('upazila_id', $entry->upazila_id)->pluck('name', 'id'),
        ];
        return view('renewable_energy.edit', $data);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $entry = RenewableEnergy::findOrFail($id);
        try {
            DB::beginTransaction();
            // Keep the automatically generated entry date immutable during edits.
            $data = $request->except(['_token', '_method', 'visit_date', 'document']);
            $data['plant_start_date'] = $data['plant_start_date'] ?: null;
            $data['plant_end_date'] = $data['plant_end_date'] ?: null;

            if ($request->hasFile('document')) {
                if ($entry->document && file_exists(public_path($entry->document))) {
                    @unlink(public_path($entry->document));
                }
                $file = $request->file('document');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/renewable_energy'), $fileName);
                $data['document'] = 'uploads/renewable_energy/' . $fileName;
            }

            $entry->update($data + [
                'updated_by' => auth()->id()
            ]);
            DB::commit();
            return response()->json('Renewable Energy entry updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        RenewableEnergy::destroy($id);
        return response()->json('Renewable Energy entry deleted successfully!');
    }

    public function allAsJson(Request $request)
    {
        return datatables()->of(
            RenewableEnergy::query()
                ->when($request->area_id, fn($q) => $q->where('area_id', $request->area_id))
                ->when($request->agent_id, fn($q) => $q->where('agent_id', $request->agent_id))
                ->when($request->start_date, fn($q) => $q->whereDate('visit_date', '>=', $request->start_date))
                ->when($request->end_date, fn($q) => $q->whereDate('visit_date', '<=', $request->end_date))
                ->with(['area', 'agent', 'division', 'district', 'upazila', 'union'])
        )
            ->addColumn('action', function ($data) {
                $btn = '<div class="btn-group">';
                $btn .= '<a href="javascript:void(0)" data-id="' . $data->id . '" class="btn btn-info btn-sm" id="viewData">View</a>';
                $btn .= '<a data-id="' . $data->id . '" class="btn btn-primary btn-sm" id="tableEdit">Edit</a>';
                $btn .= ' <a href="javascript:void(0)" data-id="' . $data->id . '" id="deleteData" class="btn btn-danger btn-sm">Delete</a>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function downloadPdf($id)
    {
        $entry = RenewableEnergy::with(['creator', 'updater', 'division', 'district', 'upazila', 'union'])->findOrFail($id);

        $html = view('renewable_energy.pdf', compact('entry'))->render();

        $dompdf = new \Dompdf\Dompdf();

        // Enable remote images for logos if needed
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('Renewable_Energy_' . $entry->id . '.pdf', ["Attachment" => false]);
    }
}
