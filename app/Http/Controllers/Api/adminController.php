<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\area;
use App\Models\circuit;
use App\Models\district;
use App\Models\event;
use App\Models\national;
use App\Models\parish;
use App\Models\province;
use App\Models\state;
use App\Models\title;
use App\Models\vineyard;
use App\Models\ministry;
use Illuminate\Http\Request;
use Illuminate\Http\ResponseTrait\original;
use Illuminate\Support\Facades\Validator;

class adminController extends Controller
{
 public function FetchAlltitle()
 {
  $alltitle = title::all();
  if ($alltitle->count() > 0) {
   return response()->json([
    'status'  => 200,
    'message' => 'Record fetched successfully',
    'titles ' => $alltitle,
   ], 200);
  } else {
   return response()->json([
    'status'   => 404,
    'message ' => 'No title records found!',
   ], 200);
  }

 }

 public function Addnewtitle(Request $request)
 {
  $validator = Validator::make($request->all(), [
   'gender' => 'required|string|max:191',
   'title'  => 'required|string|max:191',
   'status' => 'required|string|max:191',
   'level'  => 'required|string|max:191',
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {

   $title = title::create([
    'gender' => $request->gender,
    'title'  => $request->title,
    'status' => $request->status,
    'level'  => $request->level,
    'p1'     => $request->p1,
    'p2'     => $request->p2,
    'p3'     => $request->p3,
    'p4'     => $request->p4,
    'p5'     => $request->p5,
    'p6'     => $request->p6,
    'p7'     => $request->p7,
    'p8'     => $request->p8,
    'p9'     => $request->p9,
   ]);

   if ($title) {
    return response()->json([
     'status'  => 200,
     'message' => $request->title . ' added sucessfully',
    ], 200);
   } else {
    return response()->json([
     'status'  => 500,
     'message' => 'Something went wrong ' . $request->title . ' title not added',
    ], 200);
   }

  }

 }

 public function deleteTitle($id)
 {

  $title = title::where('id', '=', $id)->first();
  if ($title) {

   $title->delete();
   return response()->json([
    'status'  => 200,
    'message' => 'title deleted  successfully',
   ], 200);
  } else {
   return response()->json([
    'status'  => 404,
    'message' => 'title not found',
   ], 404);
  }

 }

 public function updateTitle(Request $request, Int $id)
 {
  $validator = Validator::make($request->all(), [
   'gender' => 'required|string|max:191',
   'title'  => 'required|string|max:191',
   'status' => 'required|string|max:191',
   'level'  => 'required|string|max:191',
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {

   $title = title::where('id', '=', $id)->first();

   if ($title) {
    $title->update([
     'gender' => $request->gender,
     'title'  => $request->title,
     'status' => $request->status,
     'level'  => $request->level,
     'p1'     => $request->p1,
     'p2'     => $request->p2,
     'p3'     => $request->p3,
     'p4'     => $request->p4,
     'p5'     => $request->p5,
     'p6'     => $request->p6,
     'p7'     => $request->p7,
     'p8'     => $request->p8,
     'p9'     => $request->p9,

    ]);
    return response()->json([
     'status'  => 200,
     'message' => 'Title information updated Sucessfully !',
    ], 200);

   } else {

    return response()->json([
     'status'  => 500,
     'message' => 'Update failed as titlt is not found',
    ], 200);

   }
  }

 }

 public function FetchAllNatinal()
 {
  // Fetch all national records and eager load their associated state records
  $nationals = National::with('state.area.province.circuit.district.parish', 'area.province.circuit.district.parish', 'province.circuit.district.parish', 'circuit.district.parish', 'district.parish', 'parish')->get();

  if ($nationals->count() > 0) {
   return response()->json([
    // 'status' => 200,
    // 'message' => 'Record fetched successfully',
    'nationalsParish ' => $nationals,
   ], 200);
  } else {
   return response()->json([
    'status'   => 404,
    'message ' => 'No title records found!',
   ], 200);
  }

 }

 public function getNational($nationalcode)
 {
// Fetch one national records and eager load their associated state records
  $nationals = National::with('state.area.province.circuit.district.parish', 'area.province.circuit.district.parish', 'province.circuit.district.parish', 'circuit.district.parish', 'district.parish', 'parish')->where('code', $nationalcode)->get();

  if ($nationals->count() > 0) {
   return response()->json([
    'status'          => 200,
    'message'         => 'Record fetched successfully',
    'nationalParish ' => $nationals,
   ], 200);
  } else {
   return response()->json([
    'status'   => 404,
    'message ' => 'No title records found!',
   ], 200);
  }

 }

 public function AddNewNatinal(Request $request)
 {
  $validator = Validator::make($request->all(), [
   'email'        => 'required|string|max:191',
   'phone1'       => 'required|string|max:191',
   'country'      => 'required|string|max:191',
   'state'        => 'required|string|max:191',
   'address'      => 'required|string|max:191',
   'nationalname' => 'required|string|max:191',
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {

   $code = strtoupper(substr($request->country, 0, 3));

   $countnational = national::where('code', 'LIKE', '%' . $code . '%')->count();

   if ($countnational == 0) {
    $countnational = 1;
    $num_padded    = sprintf("%02d", $countnational);
   } elseif ($countnational < 10) {
    $countnational = $countnational + 1;
    $num_padded    = sprintf("%02d", $countnational);
   } else {
    $num_padded = $countnational + 1;
   }

   $national = national::create([
    'email'        => $request->email,
    'phone1'       => $request->phone1,
    'phone2'       => $request->phone2,
    'country'      => $request->country,
    'states'       => $request->state,
    'city'         => $request->city,
    'address'      => $request->address,
    'nationalname' => $request->nationalname,
    'code'         => $code . $num_padded,
   ]);

   if ($national) {
    return response()->json([
     'status'  => 200,
     'message' => 'National parish added sucessfully',
    ], 200);
   } else {
    return response()->json([
     'status'  => 500,
     'message' => 'Something went wrong title not added',
    ], 200);
   }

  }

 }

 public function UpdateNational(Request $request, string $code)
 {
  $validator = Validator::make($request->all(), [
   'email'        => 'required|string|max:191',
   'phone1'       => 'required|string|max:191',
   'country'      => 'required|string|max:191',
   'state'        => 'required|string|max:191',
   'address'      => 'required|string|max:191',
   'nationalname' => 'required|string|max:191',
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {

   $nationalParish = national::where('code', '=', $code)->first();

   if ($nationalParish) {
    $nationalParish->update([
     'email'        => $request->email,
     'phone1'       => $request->phone1,
     'phone2'       => $request->phone2,
     'country'      => $request->country,
     'states'       => $request->state,
     'city'         => $request->city,
     'address'      => $request->address,
     'nationalname' => $request->nationalname,

    ]);
    return response()->json([
     'status'  => 200,
     'message' => 'National Parish information updated Sucessfully !',
    ], 200);

   } else {

    return response()->json([
     'status'  => 500,
     'message' => 'Update failed as national title is not found',
    ], 200);

   }
  }

 }

 public function deleteNational($code)
 {

  $national = national::where('code', '=', $code)->first();
  if ($national) {

   $national->delete();
   return response()->json([
    'status'  => 200,
    'message' => 'national deleted  successfully',
   ], 200);
  } else {
   return response()->json([
    'status'  => 404,
    'message' => 'national not found',
   ], 404);
  }

 }

 public function FetchAllState()
 {
  $state = state::with('area.province.circuit.district.parish', 'province.circuit.district.parish', 'circuit.district', 'district.parish', 'parish')->get();

  if ($state->count() > 0) {
   return response()->json([
    'status'       => 200,
    'message'      => 'State Record fetched successfully',
    'StateParish ' => $state,
   ], 200);
  } else {
   return response()->json([
    'status'   => 404,
    'message ' => 'No State records found!',
   ], 200);
  }

 }

 public function AddNewState(Request $request)
 {
  $validator = Validator::make($request->all(), [
   'email'     => 'required|string|max:191',
   'phone1'    => 'required|string|max:191',
   'country'   => 'required|string|max:191',
   'state'     => 'required|string|max:191',
   'address'   => 'required|string|max:191',
   'statename' => 'required|string|max:191',
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {

   $scode = strtoupper(substr($request->state, 0, 2));

   $countstate = state::where('scode', 'LIKE', '%' . $scode . '%')->count();

   if ($countstate == 0) {
    $countstate = 1;
    $num_padded = sprintf("%02d", $countstate);
   } elseif ($countstate < 10) {
    $countstate = $countstate + 1;
    $num_padded = sprintf("%02d", $countstate);
   } else {
    $num_padded = $countstate + 1;
   }

   $state = state::create([
    'email'        => $request->email,
    'phone1'       => $request->phone1,
    'phone2'       => $request->phone2,
    'country'      => $request->country,
    'state'        => $request->state,
    'city'         => $request->city,
    'address'      => $request->address,
    'statename'    => $request->statename,
    'nationalcode' => $request->nationalcode,
    'scode'        => $scode . $num_padded,
   ]);

   if ($state) {
    return response()->json([
     'status'  => 200,
     'message' => 'State parish added sucessfully',
    ], 200);
   } else {
    return response()->json([
     'status'  => 500,
     'message' => 'Something went wrong State parish not added',
    ], 200);
   }

  }

 }

 public function GetAState($scode)
 {
  $state = state::with('area.province.circuit.district.parish', 'province.circuit.district.parish', 'circuit.district', 'district.parish', 'parish')->where('scode', $scode)->get();
  if ($state) {
   return response()->json([
    'status'  => 200,
    'message' => 'Record fetched successfully',
    'state '  => $state,
   ], 200);
  } else {
   return response()->json([
    'status'  => 404,
    'message' => 'User not found',
   ], 404);
  }

 }

 public function UpdateState(Request $request, string $scode)
 {
  $validator = Validator::make($request->all(), [
   //validator used in input data(Add New State)-copy and paste
   'email'     => 'required|string|max:191',
   'phone1'    => 'required|string|max:191',
   'country'   => 'required|string|max:191',
   'state'     => 'required|string|max:191',
   'address'   => 'required|string|max:191',
   'statename' => 'required|string|max:191',
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {

   $StateParish = state::where('scode', '=', $scode)->first();

   if ($StateParish) {
    $StateParish->update([
     //Copy payload from Adding New State Minus scode
     'email'        => $request->email,
     'phone1'       => $request->phone1,
     'phone2'       => $request->phone2,
     'country'      => $request->country,
     'state'        => $request->state,
     'city'         => $request->city,
     'address'      => $request->address,
     'statename'    => $request->statename,
     'nationalcode' => $request->nationalcode,

    ]);
    return response()->json([
     'status'  => 200,
     'message' => $request->statename . 'information updated Sucessfully !',
    ], 200);

   } else {

    return response()->json([
     'status'  => 500,
     'message' => 'Update failed ' . $request->statename . ' Parish is not found',
    ], 200);

   }
  }

 }

 public function deleteState($scode)
 {

  $state = state::where('scode', '=', $scode)->first();
  if ($state) {

   $state->delete();
   return response()->json([
    'status'  => 200,
    'message' => $state->statename . ' deleted  successfully',
   ], 200);
  } else {
   return response()->json([
    'status'  => 404,
    'message' => $state->statename . ' not found',
   ], 404);
  }

 }

 public function AddNewArea(Request $request)
 {
  $validator = Validator::make($request->all(), [
   'email'         => 'required|string|max:191',
   'phone1'        => 'required|string|max:191',
   'country'       => 'required|string|max:191',
   'state'         => 'required|string|max:191',
   'city'          => 'required|string|max:191',
   'address'       => 'required|string|max:191',
   'areaname'      => 'required|string|max:191',
   'reportingcode' => 'required|string|max:191',
   // we dont have to add acode bc we will generate it ourselve
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {
   $acode = strtoupper(substr($request->city, 0, 2)); //SH,IK,AP

   $countarea = area::where('acode', 'LIKE', '%' . $acode . '%')->count();

   if ($countarea == 0) {
    $countarea  = 1;
    $num_padded = sprintf("%02d", $countarea);
   } elseif ($countarea < 10) {
    $countarea  = $countarea + 1;
    $num_padded = sprintf("%02d", $countarea);
   } else {
    $num_padded = $countarea + 1;
   }

   $area = area::create([
    'email'         => $request->email,
    'phone1'        => $request->phone1,
    'phone2'        => $request->phone2,
    'country'       => $request->country,
    'state'         => $request->state,
    'city'          => $request->city,
    'address'       => $request->address,
    'areaname'      => $request->areaname,
    'reportingcode' => $request->reportingcode,
    'acode'         => $acode . $num_padded,
   ]);

   if ($area) {
    return response()->json([
     'status'  => 200,
     'message' => $request->areaname . ' Area added sucessfully',
    ], 200);
   } else {
    return response()->json([
     'status'  => 500,
     'message' => 'Something went wrong ' . $request->areaname . ' Area not added',
    ], 200);
   }

  }

 }

 public function FetchAllarea()
 {

//   $area = area::all();
  $area = area::with('province.circuit.district.parish', 'circuit.district.parish', 'district.parish', 'parish')->get();
  if ($area->count() > 0) {
   return response()->json([
    'status'      => 200,
    'message'     => 'Record fetched successfully',
    'areaParish ' => $area,
   ], 200);
  } else {
   return response()->json([
    'status'   => 404,
    'message ' => 'No title records found!',
   ], 200);
  }

 }

 public function GetAnArea($acode)
 {
  //    $state = state::where('scode', '=', $scode)->first();
  // $state = state::find($scode)->with('national')->get();
  $area = area::with('province.circuit.district.parish', 'circuit.district.parish', 'district.parish', 'parish')->where('acode', $acode)->get();

  if ($area) {
   return response()->json([
    'status'  => 200,
    'message' => 'Record fetched successfully',
    'area '   => $area,
   ], 200);

  } else {
   return response()->json([
    'status'  => 404,
    'message' => 'User not found',
   ], 404);
  }

 }

 public function UpdateArea(Request $request, string $acode)
 {
  $validator = Validator::make($request->all(), [
   //validator used in input data(Add New Area)-copy and paste
   'email'         => 'required|string|max:191',
   'phone1'        => 'required|string|max:191',
   'country'       => 'required|string|max:191',
   'state'         => 'required|string|max:191',
   'city'          => 'required|string|max:191',
   'address'       => 'required|string|max:191',
   'areaname'      => 'required|string|max:191',
   'reportingcode' => 'required|string|max:191',
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {

   $AreaParish = area::where('acode', '=', $acode)->first();

   if ($AreaParish) {
    $AreaParish->update([
     //Copy payload from Adding New State Minus acode
     'email'         => $request->email,
     'phone1'        => $request->phone1,
     'phone2'        => $request->phone2,
     'country'       => $request->country,
     'state'         => $request->state,
     'city'          => $request->city,
     'address'       => $request->address,
     'areaname'      => $request->areaname,
     'reportingcode' => $request->reportingcode,

    ]);
    return response()->json([
     'status'  => 200,
     'message' => $request->areaname . 'information updated Sucessfully !',
    ], 200);

   } else {

    return response()->json([
     'status'  => 500,
     'message' => 'Update failed ' . $request->areaname . ' Parish is not found',
    ], 200);

   }
  }

 }

 public function deleteArea($acode)
 {

  $area = area::where('acode', '=', $acode)->first();
  if ($area) {

   $area->delete();
   return response()->json([
    'status'  => 200,
    'message' => $area->areaname . ' deleted  successfully',
   ], 200);
  } else {
   return response()->json([
    'status'  => 404,
    'message' => $area->areaname . ' not found',
   ], 404);
  }

 }

 public function AddNewProvince(Request $request)
 {
  $validator = Validator::make($request->all(), [
   'email'         => 'required|string|max:191',
   'phone1'        => 'required|string|max:191',
   'country'       => 'required|string|max:191',
   'state'         => 'required|string|max:191',
   'city'          => 'required|string|max:191',
   'address'       => 'required|string|max:191',
   'provincename'  => 'required|string|max:191',
   'reportingcode' => 'required|string|max:191',
   // we dont have to add pcode bc we will generate it ourselve
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {
   $pcode = strtoupper(substr($request->city, 0, 2)); //SH,IK,AP

   $countprovince = province::where('pcode', 'LIKE', '%' . $pcode . '%')->count();

   if ($countprovince == 0) {
    $countprovince = 1;
    $num_padded    = sprintf("%02d", $countprovince);
   } elseif ($countprovince < 10) {
    $countprovince = $countprovince + 1;
    $num_padded    = sprintf("%02d", $countprovince);

   } else {
    $num_padded = $countprovince + 1;
   }

   $area = province::create([
    'email'         => $request->email,
    'phone1'        => $request->phone1,
    'phone2'        => $request->phone2,
    'country'       => $request->country,
    'state'         => $request->state,
    'city'          => $request->city,
    'address'       => $request->address,
    'provincename'  => $request->provincename,
    'reportingcode' => $request->reportingcode,
    'pcode'         => 'P' . $pcode . $num_padded,
   ]);

   if ($area) {
    return response()->json([
     'status'  => 200,
     'message' => $request->provincename . ' Province added sucessfully',
    ], 200);
   } else {
    return response()->json([
     'status'  => 500,
     'message' => 'Something went wrong ' . $request->provincename . ' Area not added',
    ], 200);
   }

  }

 }

 public function FetchAllProvince()
 {

  $Province = province::with('circuit.district.parish', 'district.parish', 'parish')->get();
//   $Province = Province::all();
  if ($Province->count() > 0) {
   return response()->json([
    'status'          => 200,
    'message'         => 'Record fetched successfully',
    'ProvinceParish ' => $Province,
   ], 200);
  } else {
   return response()->json([
    'status'   => 404,
    'message ' => 'No title records found!',
   ], 200);
  }

 }
 public function GetAProvince($pcode)
 {
  // $province = province::where('pcode', $pcode)
  //     ->leftJoin('area', 'province.reportingcode', '=', 'area.acode')
  //     ->leftJoin('state', function ($joinState) {
  //         $joinState->on('area.reportingcode', '=', 'state.scode')
  //             ->orOn('province.reportingcode', '=', 'state.scode');
  //     })
  //     ->leftJoin('national', function ($join) {
  //         $join->on('state.nationalcode', '=', 'national.code')
  //             ->orOn('area.reportingcode', '=', 'national.code')
  //             ->orOn('province.reportingcode', '=', 'national.code');
  //     })
  //     ->select(
  //         'province.*',
  //         'state.statename',
  //         'state.scode as statecode',
  //         'area.areaname',
  //         'area.acode as areacode',
  //         'national.nationalname as nationalname',
  //         'national.code as nationalcode'
  //     )
  //     ->first();

  $province = province::with('circuit.district.parish', 'district.parish', 'parish')->where('pcode', $pcode)->get();
  if ($province) {
   return response()->json([
    'status'   => 200,
    'message'  => 'Record fetched successfully',
    'province' => $province,
   ], 200);
  } else {
   return response()->json([
    'status'  => 404,
    'message' => 'Province not found',
   ], 404);
  }

 }

 public function UpdateProvince(Request $request, string $pcode)
 {
  $validator = Validator::make($request->all(), [
   //validator used in input data(Add New Province)-copy and paste
   'email'         => 'required|string|max:191',
   'phone1'        => 'required|string|max:191',
   'country'       => 'required|string|max:191',
   'state'         => 'required|string|max:191',
   'city'          => 'required|string|max:191',
   'address'       => 'required|string|max:191',
   'provincename'  => 'required|string|max:191',
   'reportingcode' => 'required|string|max:191',
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {

   $provinceParish = province::where('pcode', '=', $pcode)->first();

   if ($provinceParish) {
    $provinceParish->update([
     //Copy payload from Adding New Province Minus pcode
     'email'         => $request->email,
     'phone1'        => $request->phone1,
     'phone2'        => $request->phone2,
     'country'       => $request->country,
     'state'         => $request->state,
     'city'          => $request->city,
     'address'       => $request->address,
     'provincename'  => $request->provincename,
     'reportingcode' => $request->reportingcode,

    ]);
    return response()->json([
     'status'  => 200,
     'message' => $request->provincename . 'information updated Sucessfully !',
    ], 200);

   } else {

    return response()->json([
     'status'  => 500,
     'message' => 'Update failed ' . $request->provincename . ' Parish is not found',
    ], 200);

   }
  }

 }

 public function DeleteProvince($pcode)
 {

  $province = province::where('pcode', '=', $pcode)->first();
  if ($province) {

   $province->delete();
   return response()->json([
    'status'  => 200,
    'message' => $province->provincename . ' deleted  successfully',
   ], 200);
  } else {
   return response()->json([
    'status'  => 404,
    'message' => $province->provincename . ' not found',
   ], 404);
  }

 }

 public function AddNewcircuit(Request $request)
 {
  $validator = Validator::make($request->all(), [
   'email'         => 'required|string|max:191',
   'phone1'        => 'required|string|max:191',
   'country'       => 'required|string|max:191',
   'state'         => 'required|string|max:191',
   'city'          => 'required|string|max:191',
   'address'       => 'required|string|max:191',
   'circuitname'   => 'required|string|max:191',
   'reportingcode' => 'required|string|max:191',
   // we dont have to add cicode bc we will generate it ourselve
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {
   $cicode = strtoupper(substr($request->city, 0, 2)); //SH,IK,AP

   $countcircuit = circuit::where('cicode', 'LIKE', '%' . $cicode . '%')->count();

   if ($countcircuit == 0) {
    $countcircuit = 1;
    $num_padded   = sprintf("%02d", $countcircuit);
   } elseif ($countcircuit < 10) {
    $countcircuit = $countcircuit + 1;
    $num_padded   = sprintf("%02d", $countcircuit);

   } else {
    $num_padded = $countcircuit + 1;
   }

   $circuit = circuit::create([
    'email'         => $request->email,
    'phone1'        => $request->phone1,
    'phone2'        => $request->phone2,
    'country'       => $request->country,
    'state'         => $request->state,
    'city'          => $request->city,
    'address'       => $request->address,
    'circuitname'   => $request->circuitname,
    'reportingcode' => $request->reportingcode,
    'cicode'        => 'C' . $cicode . $num_padded,
   ]);

   if ($circuit) {
    return response()->json([
     'status'  => 200,
     'message' => $request->circuitname . ' Circuit added sucessfully with circuit code ' . 'C' . $cicode . $num_padded,
    ], 200);
   } else {
    return response()->json([
     'status'  => 500,
     'message' => 'Something went wrong ' . $request->circuitname . ' Circuit not added',
    ], 200);
   }

  }

 }

 public function FetchAllCircuit()
 {

  // $circuit = circuit::all();
  $circuit = circuit::with('district.parish', 'parish')->get();

  if ($circuit->count() > 0) {
   return response()->json([
    'status'         => 200,
    'message'        => 'Record fetched successfully',
    'circuitParish ' => $circuit,
   ], 200);
  } else {
   return response()->json([
    'status'   => 404,
    'message ' => 'No Circuit records found!',
   ], 200);
  }

 }

 public function GetACircuit($cicode)
 {
  // $circuit = circuit::where('cicode', $cicode)
  //     ->leftJoin('province', 'circuit.reportingcode', '=', 'province.pcode')

  //     ->leftJoin('area', function ($joinCircuit) {
  //         $joinCircuit->on('province.reportingcode', '=', 'area.acode')
  //             ->orOn('circuit.reportingcode', '=', 'area.acode');
  //     })
  //     ->leftJoin('state', function ($joinState) {
  //         $joinState->on('area.reportingcode', '=', 'state.scode')
  //             ->orOn('province.reportingcode', '=', 'state.scode')
  //             ->orOn('circuit.reportingcode', '=', 'state.scode');
  //     })
  //     ->leftJoin('national', function ($join) {
  //         $join->on('state.nationalcode', '=', 'national.code')
  //             ->orOn('area.reportingcode', '=', 'national.code')
  //             ->orOn('province.reportingcode', '=', 'national.code')
  //             ->orOn('circuit.reportingcode', '=', 'national.code');

  //     })
  //     ->select(
  //         'circuit.*',
  //         'province.provincename',
  //         'province.pcode as provincecode',
  //         'area.areaname',
  //         'area.acode as areacode',
  //         'state.statename',
  //         'state.scode as statecode',
  //         'national.nationalname as nationalname',
  //         'national.code as nationalcode'
  //     )
  //     ->first();
  $circuit = circuit::with('district.parish', 'parish')->where('cicode', $cicode)->get();

  if ($circuit) {
   return response()->json([
    'status'  => 200,
    'message' => 'Record fetched successfully',
    'circuit' => $circuit,
   ], 200);
  } else {
   return response()->json([
    'status'  => 404,
    'message' => 'circuit not found',
   ], 404);
  }

 }

 public function UpdateCircuit(Request $request, string $cicode)
 {
  $validator = Validator::make($request->all(), [
   //validator used in input data(Add New Circuit)-copy and paste
   'email'         => 'required|string|max:191',
   'phone1'        => 'required|string|max:191',
   'country'       => 'required|string|max:191',
   'state'         => 'required|string|max:191',
   'city'          => 'required|string|max:191',
   'address'       => 'required|string|max:191',
   'circuitname'   => 'required|string|max:191',
   'reportingcode' => 'required|string|max:191',
   // we dont have to add cicode bc we will generate it ourselve
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {

   $circuitParish = circuit::where('cicode', '=', $cicode)->first();

   if ($circuitParish) {
    $circuitParish->update([
     //Copy payload from Adding New Circuit Minus pcode
     'email'         => $request->email,
     'phone1'        => $request->phone1,
     'phone2'        => $request->phone2,
     'country'       => $request->country,
     'state'         => $request->state,
     'city'          => $request->city,
     'address'       => $request->address,
     'circuitname'   => $request->circuitname,
     'reportingcode' => $request->reportingcode,
    ]);
    return response()->json([
     'status'  => 200,
     'message' => $request->circuitname . ' Circuit updated Sucessfully !',
    ], 200);

   } else {

    return response()->json([
     'status'  => 500,
     'message' => 'Update failed ' . $request->circuitname . ' Circuit is not found',
    ], 200);

   }
  }

 }

 public function DeleteCircuit($cicode)
 {

  $circuit = circuit::where('cicode', '=', $cicode)->first();
  if ($circuit) {

   $circuit->delete();
   return response()->json([
    'status'  => 200,
    'message' => $circuit->circuitname . ' deleted  successfully',
   ], 200);
  } else {
   return response()->json([
    'status'  => 404,
    'message' => $circuit->circuitname . ' not found',
   ], 404);
  }

 }

 public function AddNewDistrict(Request $request)
 {
  $validator = Validator::make($request->all(), [
   'email'         => 'required|string|max:191',
   'phone1'        => 'required|string|max:191',
   'country'       => 'required|string|max:191',
   'state'         => 'required|string|max:191',
   'city'          => 'required|string|max:191',
   'address'       => 'required|string|max:191',
   'districtname'  => 'required|string|max:191',
   'reportingcode' => 'required|string|max:191',
   // we dont have to add dcode bc we will generate it ourselve
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {
   $dcode = strtoupper(substr($request->city, 0, 2)); //SH,IK,AP

   $district = district::where('dcode', 'LIKE', '%' . $dcode . '%')->count();

   if ($district == 0) {
    $district   = 1;
    $num_padded = sprintf("%02d", $district);
   } elseif ($district < 10) {
    $district   = $district + 1;
    $num_padded = sprintf("%02d", $district);

   } else {
    $num_padded = $district + 1;
   }

   $district = district::create([
    'email'         => $request->email,
    'phone1'        => $request->phone1,
    'phone2'        => $request->phone2,
    'country'       => $request->country,
    'state'         => $request->state,
    'city'          => $request->city,
    'address'       => $request->address,
    'districtname'  => $request->districtname,
    'reportingcode' => $request->reportingcode,
    'dcode'         => 'D' . $dcode . $num_padded,
   ]);

   if ($district) {
    return response()->json([
     'status'  => 200,
     'message' => $request->districtname . ' district added sucessfully with district code ' . 'D' . $dcode . $num_padded,
    ], 200);
   } else {
    return response()->json([
     'status'  => 500,
     'message' => 'Something went wrong ' . $request->districtname . ' district not added',
    ], 200);
   }

  }

 }

 public function FetchAllDistrict()
 {
  $district = district::with('parish')->get();
  if ($district->count() > 0) {
   return response()->json([
    'status'          => 200,
    'message'         => 'Record fetched successfully',
    'districtParish ' => $district,
   ], 200);
  } else {
   return response()->json([
    'status'   => 404,
    'message ' => 'No district records found!',
   ], 200);
  }

 }
 public function GetADistrict($dcode)
 {
  // $district = district::where('dcode', $dcode)
  //     ->leftJoin('circuit', 'district.reportingcode', '=', 'circuit.cicode')

  //     ->leftJoin('province', function ($joinProvince) {
  //         $joinProvince->on('circuit.reportingcode', '=', 'province.pcode')
  //             ->orOn('district.reportingcode', '=', 'province.pcode');
  //     })

  //     ->leftJoin('area', function ($joinArea) {
  //         $joinArea->on('province.reportingcode', '=', 'area.acode')
  //             ->orOn('circuit.reportingcode', '=', 'area.acode')
  //             ->orOn('district.reportingcode', '=', 'area.acode');
  //     })
  //     ->leftJoin('state', function ($joinState) {
  //         $joinState->on('area.reportingcode', '=', 'state.scode')
  //             ->orOn('province.reportingcode', '=', 'state.scode')
  //             ->orOn('circuit.reportingcode', '=', 'state.scode')
  //             ->orOn('district.reportingcode', '=', 'state.scode')
  //         ;
  //     })
  //     ->leftJoin('national', function ($join) {
  //         $join->on('state.nationalcode', '=', 'national.code')
  //             ->orOn('area.reportingcode', '=', 'national.code')
  //             ->orOn('province.reportingcode', '=', 'national.code')
  //             ->orOn('circuit.reportingcode', '=', 'national.code')
  //             ->orOn('district.reportingcode', '=', 'national.code');

  //     })
  //     ->select(
  //         'district.*',
  //         'circuit.circuitname',
  //         'circuit.cicode as circuitcode',
  //         'province.provincename',
  //         'province.pcode as provincecode',
  //         'area.areaname',
  //         'area.acode as areacode',
  //         'state.statename',
  //         'state.scode as statecode',
  //         'national.nationalname as nationalname',
  //         'national.code as nationalcode'
  //     )
  //     ->first();

  $district = district::with('parish')->where('dcode', $dcode)->get();

  if ($district) {
   return response()->json([
    'status'   => 200,
    'message'  => 'Record fetched successfully',
    'district' => $district,
   ], 200);
  } else {
   return response()->json([
    'status'  => 404,
    'message' => 'circuit not found',
   ], 404);
  }

 }

 public function UpdateDistrict(Request $request, string $dcode)
 {
  $validator = Validator::make($request->all(), [
   //validator used in input data(Add New disrtict)-copy and paste
   'email'         => 'required|string|max:191',
   'phone1'        => 'required|string|max:191',
   'country'       => 'required|string|max:191',
   'state'         => 'required|string|max:191',
   'city'          => 'required|string|max:191',
   'address'       => 'required|string|max:191',
   'districtname'  => 'required|string|max:191',
   'reportingcode' => 'required|string|max:191',
   // we dont have to add dcode bc we will generate it ourselve
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {

   $districtParish = district::where('dcode', '=', $dcode)->first();

   if ($districtParish) {
    $districtParish->update([
     //Copy payload from Adding New Diostrict Minus pcode
     'email'         => $request->email,
     'phone1'        => $request->phone1,
     'phone2'        => $request->phone2,
     'country'       => $request->country,
     'state'         => $request->state,
     'city'          => $request->city,
     'address'       => $request->address,
     'districtname'  => $request->districtname,
     'reportingcode' => $request->reportingcode,
     //dcode will not be included because its the one we are using
    ]);
    return response()->json([
     'status'  => 200,
     'message' => $request->districtname . ' District updated Sucessfully !',
    ], 200);

   } else {

    return response()->json([
     'status'  => 500,
     'message' => 'Update failed ' . $request->districtname . ' District is not found',
    ], 200);

   }
  }

 }

 public function DeleteDistrict($dcode)
 {

  $district = district::where('dcode', '=', $dcode)->first();
  if ($district) {

   $district->delete();
   return response()->json([
    'status'  => 200,
    'message' => $district->districtname . ' deleted  successfully',
   ], 200);
  } else {
   return response()->json([
    'status'  => 404,
    'message' => $district->districtname . ' not found',
   ], 404);
  }

 }

 public function AddNewParish(Request $request)
 {
  $validator = Validator::make($request->all(), [
   'email'         => 'required|string|max:191',
   'phone1'        => 'required|string|max:191',
   'country'       => 'required|string|max:191',
   'state'         => 'required|string|max:191',
   'city'          => 'required|string|max:191',
   'address'       => 'required|string|max:191',
   'parishname'    => 'required|string|max:191',
   'reportingcode' => 'required|string|max:191',
   // we dont have to add picode bc we will generate it ourselve
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {
   $picode = strtoupper(substr($request->city, 0, 2)); //SH,IK,AP

   $parish = parish::where('picode', 'LIKE', '%' . $picode . '%')->count();

   if ($parish == 0) {
    $parish     = 1;
    $num_padded = sprintf("%02d", $parish);
   } elseif ($parish < 10) {
    $parish     = $parish + 1;
    $num_padded = sprintf("%02d", $parish);

   } else {
    $num_padded = $parish + 1;
   }

   $parish = parish::create([
    'email'         => $request->email,
    'phone1'        => $request->phone1,
    'phone2'        => $request->phone2,
    'country'       => $request->country,
    'state'         => $request->state,
    'city'          => $request->city,
    'address'       => $request->address,
    'parishname'    => $request->parishname,
    'reportingcode' => $request->reportingcode,
    'picode'        => 'P' . $picode . $num_padded,
   ]);

   if ($parish) {
    return response()->json([
     'status'  => 200,
     'message' => $request->parishname . ' parish added sucessfully with parish code ' . 'P' . $picode . $num_padded,
    ], 200);
   } else {
    return response()->json([
     'status'  => 500,
     'message' => 'Something went wrong ' . $request->parishname . ' district not added',
    ], 200);
   }

  }

 }

 public function FetchAllParish()
 {

  // $parish = parish::all();
  // $parish = parish::with('district.circuit.province.area.state.national')->get();
  // $parish = parish::with('district.circuit.province.area.state.national','circuit.province.area.state.national','province.area.state.national','area.state.national','state.national','national')->get();

  $parish = parish::with('district', 'circuit', 'province', 'area', 'state', 'national')->get();
//remove null from the array of event
  $filteredParish = $parish->filter(function ($item) {
   return !is_null($item);
  });

  if ($parish->count() > 0) {
   return response()->json([
    'status'        => 200,
    'message'       => 'Record fetched successfully',
    'parishParish ' => $parish,
   ], 200);
  } else {
   return response()->json([
    'status'   => 404,
    'message ' => 'No parish records found!',
   ], 200);
  }

 }

 public function GetAParish($picode)
 {
  $parish = parish::with('district.circuit.province.area.state.national', 'circuit.province.area.state.national', 'province.area.state.national', 'area.state.national', 'state.national', 'national')->where('picode', '=', $picode)->get();

  if ($parish) {
   return response()->json([
    'status'  => 200,
    'message' => 'Record fetched successfully',
    'parish'  => $parish,
   ], 200);
  } else {
   return response()->json([
    'status'  => 404,
    'message' => 'circuit not found',
   ], 404);
  }

 }

 public function UpdateParish(Request $request, string $picode)
 {
  $validator = Validator::make($request->all(), [
   //validator used in input data(Add New Parish)-copy and paste
   'email'         => 'required|string|max:191',
   'phone1'        => 'required|string|max:191',
   'country'       => 'required|string|max:191',
   'state'         => 'required|string|max:191',
   'city'          => 'required|string|max:191',
   'address'       => 'required|string|max:191',
   'parishname'    => 'required|string|max:191',
   'reportingcode' => 'required|string|max:191',
   // we dont have to add picode bc we will generate it ourselve
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {

   $parishParish = parish::where('picode', '=', $picode)->first();

   if ($parishParish) {
    $parishParish->update([
     //Copy payload from Adding New Diostrict Minus pcode
     'email'         => $request->email,
     'phone1'        => $request->phone1,
     'phone2'        => $request->phone2,
     'country'       => $request->country,
     'state'         => $request->state,
     'city'          => $request->city,
     'address'       => $request->address,
     'parishname'    => $request->parishname,
     'reportingcode' => $request->reportingcode,
     //dcode will not be included because its the one we are using
    ]);
    return response()->json([
     'status'  => 200,
     'message' => $request->parishname . ' parish updated Sucessfully !',
    ], 200);

   } else {

    return response()->json([
     'status'  => 500,
     'message' => 'Update failed ' . $request->parishname . ' parish is not found',
    ], 200);

   }
  }

 }

 public function DeleteParish($picode)
 {

  $parish = parish::where('picode', '=', $picode)->first();
  if ($parish) {

   $parish->delete();
   return response()->json([
    'status'  => 200,
    'message' => $parish->parishname . ' Parish deleted  successfully',
   ], 200);
  } else {
   return response()->json([
    'status'  => 404,
    'message' => $parish->parishname . ' not found',
   ], 404);
  }

 }

 public static function FetchAllParishes($parishcode = null)
 {

  //Get All parishes
  $national = national::select('email', 'phone1', 'phone2', 'country', 'states', 'city', 'address', 'nationalname as parishname', 'code as parishcode');
  $state    = state::select('email', 'phone1', 'phone2', 'country', 'state', 'city', 'address', 'statename as parishname', 'scode as parishcode');
  $area     = area::select('email', 'phone1', 'phone2', 'country', 'state', 'city', 'address', 'areaname as parishname', 'acode as parishcode');
  $province = province::select('email', 'phone1', 'phone2', 'country', 'state', 'city', 'address', 'provincename as parishname', 'pcode as parishcode');
  $circuit  = circuit::select('email', 'phone1', 'phone2', 'country', 'state', 'city', 'address', 'circuitname as parishname', 'cicode as parishcode');
  $district = district::select('email', 'phone1', 'phone2', 'country', 'state', 'city', 'address', 'districtname as parishname', 'dcode as parishcode');
  $parish   = parish::select('email', 'phone1', 'phone2', 'country', 'state', 'city', 'address', 'parishname as parishname', 'picode as parishcode');

  ///To get a single parish
  if ($parishcode !== null) {
   $national = national::select('email', 'phone1', 'phone2', 'country', 'states', 'city', 'address', 'nationalname as parishname', 'code as parishcode')->where('code', $parishcode);
   $state    = state::select('email', 'phone1', 'phone2', 'country', 'state', 'city', 'address', 'statename as parishname', 'scode as parishcode')->orWhere('scode', $parishcode);
   $area     = area::select('email', 'phone1', 'phone2', 'country', 'state', 'city', 'address', 'areaname as parishname', 'acode as parishcode')->orWhere('acode', $parishcode);
   $province = province::select('email', 'phone1', 'phone2', 'country', 'state', 'city', 'address', 'provincename as parishname', 'pcode as parishcode')->orWhere('pcode', $parishcode);
   $circuit  = circuit::select('email', 'phone1', 'phone2', 'country', 'state', 'city', 'address', 'circuitname as parishname', 'cicode as parishcode')->orWhere('cicode', $parishcode);
   $district = district::select('email', 'phone1', 'phone2', 'country', 'state', 'city', 'address', 'districtname as parishname', 'dcode as parishcode')->orWhere('dcode', $parishcode);
   $parish   = parish::select('email', 'phone1', 'phone2', 'country', 'state', 'city', 'address', 'parishname as parishname', 'picode as parishcode')->orWhere('picode', $parishcode);
  }

  $result = $national
   ->union($state)
   ->union($area)
   ->union($province)
   ->union($circuit)
   ->union($district)
   ->union($parish)
   ->get();

  return response()->json([
   'status'    => 200,
   'message'   => 'Record fetched successfully',
   'Allparish' => $result->toArray(),
  ], 200);
 }

 public function AddNewEvent(Request $request)
 {

  $validator = Validator::make($request->all(), [
   //validator used in input data(Add New Parish)-copy and paste
   'Title'       => 'required|string|max:191',
   'Description' => 'required|string|max:191',
   'startdate'   => 'required|string|max:191',
   'enddate'     => 'required|string|max:191',
   'Time'        => 'required|string|max:191',
   'Moderator'   => 'required|string|max:191',
   'Minister'    => 'required|string|max:191',
   'Type'        => 'required|string|max:191',
   'parishcode'  => 'required|string|max:191',
   'eventImg'    => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
   // we dont have to add picode bc we will generate it ourselve
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {

   $date      = strtoupper(substr($request->startdate, 0, -3));
   $dateParts = explode('-', $request->startdate); // Use '-' for date format "YYYY-MM-DD"
   $yr        = $dateParts[0];
   $event     = event::where('parishcode', 'LIKE', '%' . $request->eventcode . '%')
    ->where('startdate', 'LIKE', '%' . $date . '%')
    ->count();

   if ($event == 0) {
    $event      = 1;
    $num_padded = sprintf("%02d", $event);
   } elseif ($event < 10) {
    $event      = $event + 1;
    $num_padded = sprintf("%02d", $event);

   } else {
    $num_padded = $event + 1;
   }

   $fetchparish = adminController::FetchAllParishes($request->parishcode)->original['Allparish'];
   if (!$fetchparish) {
    return response()->json([
     'status'  => 500,
     'message' => 'Parish does not exist',
    ], 200);

   }

   $parishNames = implode(', ', array_column($fetchparish, 'parishname'));

   if ($request->hasFile('eventImg')) {

    $fileUploaded = $request->file('eventImg');
    $eventNewPic  = $request->parishcode . $yr . $num_padded . '.' . $fileUploaded->getClientOriginalExtension();
    $eventImgPath = $fileUploaded->storeAs('eventImgs', $eventNewPic, 'public');
   } else {
    $eventImgPath = ""; // Or provide a default image path
   }

   $event = event::create([
    'EventId'     => $request->parishcode . $num_padded,
    'Title'       => $request->Title,
    'Description' => $request->Description,
    'startdate'   => $request->startdate,
    'enddate'     => $request->enddate,
    'Time'        => $request->Time,
    'Moderator'   => $request->Moderator,
    'Minister'    => $request->Minister,
    'Type'        => $request->Type,
    'parishcode'  => $request->parishcode,
    'parishname'  => $parishNames,
    'eventImg'    => $eventNewPic,

   ]);

   if ($event) {
    return response()->json([
     'status'  => 200,
     'message' => $request->parishcode . $yr . '-' . $num_padded . ' event created sucessfully',
    ], 200);
   } else {
    return response()->json([
     'status'  => 500,
     'message' => 'Something went wrong ' . $request->parishcode . $yr . '-' . $num_padded . ' event not created',
    ], 200);
   }

  }
 }

 public function FetchAllEvent()
 {
  $allevent = event::all();
  if ($allevent->count() > 0) {
   return response()->json([
    'status'  => 200,
    'message' => 'Record fetched successfully',
    'events ' => $allevent,
   ], 200);
  } else {
   return response()->json([
    'status'   => 404,
    'message ' => 'No event records found!',
   ], 200);
  }

 }

 public function GetAnEvent($EventId)
 {
  $event = event::where('EventId', '=', $EventId)->first();
  if ($event) {
   return response()->json([
    'status'  => 200,
    'message' => $EventId . ' Record fetched successfully',
    'event '  => $event,
   ], 200);
  } else {
   return response()->json([
    'status'  => 404,
    'message' => 'User not found',
   ], 404);
  }

 }

 public function updateEvent(Request $request, String $EventId)
 {

  $validator = Validator::make($request->all(), [
   //validator used in input data(Add New Event)-copy and paste
   'Title'       => 'required|string|max:191',
   'Description' => 'required|string|max:191',
   'startdate'   => 'required|string|max:191',
   'enddate'     => 'required|string|max:191',
   'Time'        => 'required|string|max:191',
   'Moderator'   => 'required|string|max:191',
   'Minister'    => 'required|string|max:191',
   'Type'        => 'required|string|max:191',
   'parishcode'  => 'required|string|max:191',
   'eventImg'    => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
   // we dont have to add EventId bc we will generate it ourselve
  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {

   $fetchparish = adminController::FetchAllParishes($request->parishcode)->original['Allparish'];
   if (!$fetchparish) {

    return response()->json([
     'status'  => 500,
     'message' => 'Parish does not exist',
    ], 200);

   }
   $parishNames = implode(', ', array_column($fetchparish, 'parishname'));

   if ($request->hasFile('eventImg')) {
    $file         = $request->file('eventImg');
    $eventImg     = $request->EventId . '.' . $file->getClientOriginalExtension();
    $eventImgPath = $file->storeAs('eventImgs', $eventImg, 'public');
   } else {
    $eventImgPath = null; // Or provide a default image path
   }


   $event = event::where('EventId', '=', $EventId)->first();

   if ($event) {
    $event->update([
     'event'       => $request->event,
     'Description' => $request->Description,
     'startdate'   => $request->startdate,
     'enddate'     => $request->enddate,
     'Time'        => $request->Time,
     'Moderator'   => $request->Moderator,
     'Minister'    => $request->Minister,
     'Type'        => $request->Type,
     'parishcode'  => $request->parishcode,
     'parishname'  => $parishNames,
     'eventImg'   => $eventImgPath,

    ]);
    return response()->json([
     'status'  => 200,
     'message' =>' Event information updated Sucessfully !',
     'event'=> $event,
    ], 200);

   } else {

    return response()->json([
     'status'  => 500,
     'message' => 'Update failed as titlt is not found',
    ], 200);

   }
  }

 }

 public function DeleteEvent($EventId)
 {

  $event = event::where('EventId', '=', $EventId)->first();
  if ($event) {

   $event->delete();
   return response()->json([
    'status'  => 200,
    'message' => ' event deleted  successfully',
   ], 200);
  } else {
   return response()->json([
    'status'  => 404,
    'message' => 'User/event not found',
   ], 404);
  }

 }

 public function AddNewVineyard(Request $request)
 {

  $validator = Validator::make($request->all(), [
   //validator used in input data(Add New Parish)-copy and paste
    'vineyard' => 'required|string|max:191',

  ]);

    if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {

      $vineyard = vineyard::where('vineyard',$request->vineyard)->get();


     if(!$vineyard){
         return response()->json([
             'status' => 500,
             'message' => 'vineyard does not exist',
         ], 200);
     }else{

         $vineyard = vineyard::create([
         'vineyard'       => $request->vineyard,

         ]);
     }

     if ($vineyard) {

     return response()->json([
         'status'  => 200,
         'message' => $request->vineyard .' post sucessfully created',
         'vineyard'=> $vineyard,
     ], 200);
     } else {

     return response()->json([
         'status'  => 500,
         'message' => 'Something went wrong '  .$request->vineyard .'  not created',
     ], 200);
     }

 }
 }

 public function FetchAllVineyard()
 {
  $allvineyard = vineyard::all();
  if ($allvineyard->count() > 0) {
   return response()->json([
    'status'  => 200,
    'message' => 'Record fetched successfully',
    'vineyard ' => $allvineyard,
   ], 200);
  } else {
   return response()->json([
    'status'   => 404,
    'message ' => 'No Vineyard records found!',
   ], 200);
  }

 }

 public function updateVineyard(Request $request, String $vineyardid)
 {
  $validator = Validator::make($request->all(), [
   //validator used in input data(Add New Event)-copy and paste
   'vineyard'       => 'required|string|max:191',

  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  }
    $vineyard = vineyard::where('Id', '=', $vineyardid)->first();

   if ($vineyard) {
    $vineyard->update([
     'vineyard' => $request->vineyard,

    ]);
    return response()->json([
     'status'  => 200,
     'message' =>' Vineyard information updated Sucessfully !',
     'vineyard'=> $vineyard,
    ], 200);

   } else {

    return response()->json([
     'status'  => 500,
     'message' => 'Update failed as Vineyard is not found',
    ], 200);

   }
  }

  public function DeleteVineyard($VineyardId)
  {

   $vineyard = vineyard::where('Id', '=', $VineyardId)->first();
   if ($vineyard) {

    $vineyard->delete();
    return response()->json([
     'status'  => 200,
     'message' => 'deleted  successfully',
    ], 200);
   } else {
    return response()->json([
     'status'  => 404,
     'message' => $vineyard.  'not found',
    ], 404);
   }

  }

  public function AddNewMinistry(Request $request)
 {

  $validator = Validator::make($request->all(), [
   //validator used in input data(Add New Parish)-copy and paste
    'ministry' => 'required|string|max:191',

  ]);

    if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  } else {

      $ministry = ministry::where('ministry',$request->ministry)->get();


     if(!$ministry){
         return response()->json([
             'status' => 500,
             'message' => 'vineyard does not exist',
         ], 200);
     }else{

         $ministry = ministry::create([
         'ministry' => $request->ministry,

         ]);
     }

     if ($ministry) {

     return response()->json([
         'status'  => 200,
         'message' => $request->ministry .' post sucessfully created',
         'vineyard'=> $ministry,
     ], 200);
     } else {

     return response()->json([
         'status'  => 500,
         'message' => 'Something went wrong '  .$request->ministry .'  not created',
     ], 200);
     }

 }
 }

 public function FetchAllMinistry()
 {
  $allministry = ministry::all();
  if ($allministry->count() > 0) {
   return response()->json([
    'status'  => 200,
    'message' => 'Record fetched successfully',
    'ministry ' => $allministry,
   ], 200);
  } else {
   return response()->json([
    'status'   => 404,
    'message ' => 'No ministry records found!',
   ], 200);
  }

 }

 public function updateMinistry(Request $request, String $ministryId)
 {
  $validator = Validator::make($request->all(), [
   //validator used in input data(Add New Event)-copy and paste
   'ministry'       => 'required|string|max:191',

  ]);

  if ($validator->fails()) {
   return response()->json([
    'status' => 422,
    'error'  => $validator->messages(),
   ], 422);

  }
    $ministry = ministry::where('Id', '=', $ministryId)->first();

   if ($ministry) {
    $ministry->update([
     'ministry' => $request->ministry,

    ]);
    return response()->json([
     'status'  => 200,
     'message' => ' ministry information updated Sucessfully !',
     'ministry'=> $ministry,
    ], 200);

   } else {

    return response()->json([
     'status'  => 500,
     'message' => 'Update failed as ministry is not found',
    ], 200);

   }
  }

  public function DeleteMinistry($ministryId)
  {

   $ministry = ministry::where('Id', '=', $ministryId)->first();
   if ($ministry) {

    $ministry->delete();
    return response()->json([
     'status'  => 200,
     'message' => 'Ministry deleted  successfully',
    ], 200);
   } else {
    return response()->json([
     'status'  => 404,
     'message' => $ministry.  'not found',
    ], 404);
   }

  }

}
