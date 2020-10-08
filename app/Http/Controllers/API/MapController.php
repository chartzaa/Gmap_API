<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GoogleMaps;
use Cache;

class MapController extends Controller
{
public function test(){
    $response = [
        ['id'=>1,'first_name'=>'name_1'],
        ['id'=>2,'first_name'=>'name_2'],
        ['id'=>3,'first_name'=>'name_3'],
        ['id'=>4,'first_name'=>'name_4'],
        ['id'=>5,'first_name'=>'name_5'],
        ['id'=>6,'first_name'=>'name_6'],
        ['id'=>7,'first_name'=>'name_7'],
        ['id'=>8,'first_name'=>'name_8'],
        ['id'=>9,'first_name'=>'name_9'],
        ['id'=>10,'first_name'=>'name_10'],
        ['id'=>11,'first_name'=>'name_11'],
        ['id'=>12,'first_name'=>'name_12']
    ];
    return response()->json($response);
    }


   /*
   ** placeAutocomplet เป็นตัวค้นหาข้อมูลจาก สถานที่จากที่เราค้นหา string
   ** key: input , value: string
   **
   */
    public function placeAutocomplet(Request $req)
    {
        $input = $req->q;
        return $value = Cache::remember('place_'.$input, 60*30, function () use ($input){
            $response = GoogleMaps::load('placeautocomplete')
            ->setParam (['input' => $input])
            ->get();
            return response()->json(json_decode($response));
        });

    }

    /*
    ** placeSearch เป็นตัวค้นหาข้อมูลจาก สถานที่จากที่เราค้นหาค้นหาจาก string
    ** key: input , value: string
    **
    */
    public function placeSearch(Request $req)
    {

        $response = GoogleMaps::load('textsearch')
        ->setParam (['input' => $req->q])
        ->get();
        return response()->json(json_decode($response));
    }
    /*
    ** nearBySearch เป็นตัวค้นหาข้อมูลจาก สถานที่จากที่เราค้นหา จะได้ข้อมูลสถานที่ใกล้เคลียงกลับมา
    ** key-value: 
    **   location:lat,long,
    **   radius:default=1500,
    **   type:default=restaurant
    **
    */
    public function nearBySearch(Request $req)
    {

        $response = GoogleMaps::load('nearbysearch')
        ->setParam (['location' => $req->location])
        ->setParam (['radius' => 1500])
        ->setParam (['type' => 'restaurant'])
        ->get();
        return response()->json(json_decode($response));
    }

    /*
    ** placeDetail เป็นตัวค้นหาข้อมูลจาก สถานที่จากที่เราค้นหาค้นหาจาก place_id
    ** key: place_id , value: place_id ได้จาก function placeSearch หรือ placeAutocomplet
    **
    */
    public function placeDetail(Request $req)
    {

        $response = GoogleMaps::load('placedetails')
        ->setParam (['place_id' => $req->q])
        ->get();
        return response()->json(json_decode($response));
    }
    

    public function getData(Request $req){
        $input = $req->q;
        return $value = Cache::remember('get_data_'.$input, 60*30, function () use ($input){
            $response = GoogleMaps::load('placedetails')
            ->setParam (['place_id' => $input])
            ->get();
            $raw = json_decode($response);
            if($raw->status == 'OK'){
                $location = $raw->result->geometry->location->lat.','.$raw->result->geometry->location->lng;
                $near_res = GoogleMaps::load('nearbysearch')
                ->setParam (['location' => $location])
                ->setParam (['radius' => 1500])
                ->setParam (['type' => 'restaurant'])
                ->get();
                $raw_near = json_decode($near_res);
                // dd($raw_near);
                if($raw_near->status == 'OK'){
                    //dd($raw->result,$location,$near_res);
                    return response()->json(['success' => true, 'message'=>'สำเร็จ','data'=>$raw_near->results]);
                }else{
                    return response()->json(['success' => false, 'message'=>'มีบางอย่างผิดพลาดในการดึงข้อมูล Code:L-2']);
                }
            }else {
                return response()->json(['success' => false, 'message'=>'มีบางอย่างผิดพลาดในการดึงข้อมูล Code:L-1']);
            }
        });
    }
}
