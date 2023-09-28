<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchTenderRequest;
use App\Http\Requests\StoreTenderRequest;
use App\Http\Requests\UpdateTenderRequest;
use App\Http\Resources\TrenderHomeResource;
use App\Models\Category;
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
     * 
     * @queryParam q string
     * @queryParam category_ids object
     * @queryParam cities object
     */
    public function index(SearchTenderRequest $request)
    {
        $tenders = Tender::where(fn ($query) => $query->whereRaw('LOWER(title) LIKE ? ', ['%' . strtolower($request->q) . '%'])
            ->orWhereRaw('LOWER(description) LIKE ? ', ['%' . strtolower($request->q) . '%']));

        if ($request->category_ids) {
            $tenders = $tenders->whereHas('institution', fn ($query) => $query->whereHas('categories', fn ($query) => $query->whereIn('category_id', $request->category_ids)));
        }

        if ($request->cities) {
            $tenders = $tenders->whereHas('institution', fn ($query) => $query->whereIn('city', $request->cities));
        }

        $tenders = $tenders->get();

        return TrenderHomeResource::collection($tenders);
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
     * @response {
    "data": {
        "id": 25,
        "title": "Eum officia eum.",
        "description": "Porro doloremque molestiae culpa temporibus perferendis minima. Qui non nulla dolorem laudantium.",
        "created_at": "2023-09-17T08:12:58.000000Z",
        "ended_at": "2023-09-17 09:25:57",
        "offers_count": 3,
        "best_offer_from": "Barry Barton",
        "offers": [
            {
                "id": 11,
                "trender_name": "Eum officia eum.",
                "for": "Dr. Delfina Lesch",
                "work_on_it": "2023-09-17T09:17:03.000000Z",
                "work_ended_it": "2023-09-17 09:25:57"
            },
            {
                "id": 12,
                "trender_name": "Eum officia eum.",
                "for": "Dr. Delfina Lesch",
                "work_on_it": "2023-09-17T09:17:03.000000Z",
                "work_ended_it": "2023-09-17 09:25:57"
            },
            {
                "id": 13,
                "trender_name": "Eum officia eum.",
                "for": "Dr. Delfina Lesch",
                "work_on_it": "2023-09-17T09:17:03.000000Z",
                "work_ended_it": "2023-09-17 09:25:57"
            }
        ],
        "trender_details": [
            {
                "id": 10,
                "type": "quis",
                "question": "est",
                "description": "Natus sunt enim beatae quo. Et nisi est dolorum nihil et similique facilis nihil. Explicabo incidunt ut excepturi.",
                "data": "molestias",
                "tender_id": 25,
                "created_at": "2023-09-17T08:12:58.000000Z",
                "updated_at": "2023-09-17T08:12:58.000000Z"
            }
        ]
    }
}
     */
    public function show(Tender $trender)
    {
        $trender->load('tenderDetails', 'offers');

        return new TrenderResource($trender);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTenderRequest $request, Tender $tender)
    {
        //
    }

    /**
     * @response {
    "data": [
        {
            "id": 25,
            "title": "Eum officia eum.",
            "description": "Porro doloremque molestiae culpa temporibus perferendis minima. Qui non nulla dolorem laudantium.",
            "created_at": "2023-09-17T08:12:58.000000Z",
            "ended_at": null,
            "offers_count": 3,
            "best_offer_from": {
                "id": 11,
                "tender_id": 25,
                "company_id": 20,
                "is_draft": 0,
                "created_at": "2023-09-17T09:17:03.000000Z",
                "updated_at": "2023-09-17T09:17:03.000000Z"
            },
            "offers": [
                {
                    "id": 11,
                    "trender_name": "Eum officia eum.",
                    "for": "Dr. Delfina Lesch",
                    "work_on_it": "2023-09-17T09:17:03.000000Z",
                    "work_ended_it": null
                },
                {
                    "id": 12,
                    "trender_name": "Eum officia eum.",
                    "for": "Dr. Delfina Lesch",
                    "work_on_it": "2023-09-17T09:17:03.000000Z",
                    "work_ended_it": null
                },
                {
                    "id": 13,
                    "trender_name": "Eum officia eum.",
                    "for": "Dr. Delfina Lesch",
                    "work_on_it": "2023-09-17T09:17:03.000000Z",
                    "work_ended_it": null
                }
            ]
        }
    ]
}
     */

    public function myTrenders()
    {
        $trenders = auth()->user()->institution->tenders()->with('offers')->get();

        return TrenderResource::collection($trenders);
    }

    public function close(Tender $tender)
    {
        $tender->ended_at = now();
        $tender->save();

        return response()->noContent();
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
