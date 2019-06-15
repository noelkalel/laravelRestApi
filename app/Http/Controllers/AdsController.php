<?php

namespace App\Http\Controllers;

use DB;
use App\Ads;
use DateTime;
use App\User;
use App\Grades;
use DateInterval;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show']);
    }


    public function index()
    {
        $ad = Ads::with(['grades'])->get();

        return response()->json($ad, 200);
    }

    public function show(Ads $ad)
    {
        return $ad;
    }

    public function store(Request $request)
    {
        $current = Carbon::now();

        $adExpires = $current->addDays(30);

        $ad = Ads::create([
            'user_id' => auth()->id(),
            'text' => request('text'),
            'type' => request('type'),
            'valid_until' => $adExpires,
        ]);

        return response()->json([
            'data' => [
                'Ad Successfully Created!'
            ]
        ], 201);
    }

    public function update(Request $request, Ads $ad)
    {
        $this->authorize('update', $ad);

        $ad->update([
            'text' => request('text'),
            'type' => request('type'),
        ]);

        if(request()->wantsJson()){
            return response([
                'data' => 'Ad Successfully Updated!'
            ], 200);
        }
    }

    public function destroy(Ads $ad)
    {
        $this->authorize('delete', $ad);

        $ad->delete();

        if(request()->wantsJson()){
            return response([
                'data' => 'Ad Successfully Deleted!'
            ], 200);
        }
    }

    public function extendAd(Ads $ad)
    {
        $this->authorize('extendAd', $ad);

        $now = Carbon::now();

        if($now >= $ad->valid_until->subDays(3) && $now < $ad->valid_until){
            $ad->valid_until = $now->addMonth(1);
            // $ad->save();

            return response()->json([
                'data' => [
                    $ad->valid_until->format('Y-m-d H:i:s'), 'Ad Successfully Extended!'
                ]
            ]); 
        }

        return response()->json([
            'error' => [
                'You can only extend ad 3 days before expiration!'
            ]
        ], 400);
    }

    // public function extendAd($id)
    // {
    //     $ad = Ads::findOrFail($id);

    //     if($ad->id !== auth()->id()){
    //         return response()->json([
    //             'error' => 'Not authorized!'
    //         ], 404);
    //     }

    //     $now = new DateTime();

    //     $valid = $ad->valid_until->getTimestamp()-$now->getTimestamp();

    //     if($valid > 0 && $valid < 3*24*60*60){
    //         $ad->valid_until = $ad->valid_until->add(new DateInterval("P3D"));

    //         return response()->json([
    //             'data' => [
    //                 $ad->valid_until->format('Y-m-d H:i:s'), 'Ad Successfully Extended!'
    //             ]
    //         ]);
    //     }

    //     return response()->json([
    //         'error' => [
    //             'You can only extend ad 3 days before expiration!'
    //         ]
    //     ], 400);
    // }

    public function grades(Request $request, Ads $ad)
    {
        $user = auth()->id();

        $current = Carbon::now();

        $anotherGrade = DB::table('grades')
                        ->where('ads_id', '=', $ad->id)
                        ->where('customer_id', '=', $user)
                        ->whereMonth('date_created', '=', date('m'))
                        ->whereYear('date_created', '=', date('Y'))
                        ->get();

        if(count($anotherGrade) >= 1){
            return response()->json([
                'error' => 'You can rate only once per month!'
            ], 409);
        }      

        $grade = Grades::create([
            'ads_id' => $ad->id,
            'customer_id' => $user,
            'rating' => request('rating'),
            'date_created' => $current
        ]);   

        if(request()->wantsJson()){
            return response([
                'data' => 'Rating Successfully Added!'
            ], 201);
        }
    }
}
