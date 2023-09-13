<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchTenderRequest;
use App\Http\Requests\StoreTenderRequest;
use App\Http\Requests\UpdateTenderRequest;
use App\Models\Tender;
use Illuminate\Support\Facades\Validator;

/**
 * 
 * @group Trender
 */
class TenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchTenderRequest $request)
    {
        $tenders = Tender::whereRaw('LOWER(title) LIKE ? ', ['%' . strtolower($request->q) . '%'])
            ->orWhereRaw('LOWER(description) LIKE ? ', ['%' . strtolower($request->q) . '%'])
            ->get();

        return $tenders;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTenderRequest $request)
    {
        $data = $request->validated();
        $tender = auth()->user()->institution->tenders()->create($data);

        foreach ($data['details'] as $tenderDetails) {

            if (is_array($tenderDetails['data'])) {
                $tenderDetails['data'] = json_encode($tenderDetails['data']);
            }

            $vaildator = Validator::make($tenderDetails, [
                'type' => ['required', 'string', 'in:text,textarea,select,radio,checkbox,uploader'],
                'question' => ['required', 'string'],
                'description' => ['required', 'string'],
                'data' => ['present'],
            ]);

            $tender->tenderDetails()->create($vaildator->validated());
        }

        return $tender;
    }

    /**
     * Display the specified resource.
     */
    public function show(Tender $tender)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTenderRequest $request, Tender $tender)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tender $tender)
    {
        $tender->delete();

        return response()->noContent();
    }
}
