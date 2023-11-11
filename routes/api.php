<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\adminController;
use App\Http\Controllers\Api\childrenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function () {

// });



Route::post('/login',[MemberController::class,'login']);
Route::post('/logout',[MemberController::class,'logout']);



//member API
Route::get('/Allmember',[MemberController::class,'FetchAllMember']);
Route::post('/Addmember',[MemberController::class,'Addmember']);
// Route::get('/member/{userid}',[MemberController::class,'GetMember']);
// Route::put('/member/{userid}/update',[MemberController::class,'updateMember']);
// Route::delete('/member/{userid}/delete',[MemberController::class,'deleteMember']);

// //children API
// Route::get('/Allchildren',[childrenController::class,'FetchAllChildren']);
// Route::post('/Addchildren',[childrenController::class,'AddChild']);
// Route::delete('/children/{parentid}/{id}/delete',[childrenController::class,'deleteChildren']);
// Route::put('/children/{id}/update',[childrenController::class,'updateChild']);
// Route::get('/children/{parentid}/viewallchildren',[childrenController::class,'viewchildren']);
// Route::get('/children/{parentid}/{id}/viewchild',[childrenController::class,'viewchild']);

// //admin API
// // Route::get('Alltitle',[adminController::class,'FetchAlltitle']);

// Route::get('/Alltitle',[adminController::class,'FetchAlltitle']);
// Route::post('/Addtitle',[adminController::class,'Addnewtitle']);
// Route::delete('/title/{id}/delete',[adminController::class,'deleteTitle']);
// Route::put('/title/{id}/update',[adminController::class,'updateTitle']);


// //National
// Route::get('/Allnational',[adminController::class,'FetchAllNatinal']);
// Route::post('/Addnational',[adminController::class,'AddNewNatinal']);
// Route::put('/updatenational/{code}/update',[adminController::class,'UpdateNational']);
// Route::delete('/deletenational/{code}/delete',[adminController::class,'deleteNational']);

// //State
// Route::get('/Allstate',[adminController::class,'FetchAllState']);
// Route::post('/Addstate',[adminController::class,'AddNewState']);
// Route::get('/State/{scode}',[adminController::class,'GetAState']);


//protected route
Route::group(['middleware' => ['auth:sanctum']],function(){

    Route::get('/member/{userid}',[MemberController::class,'GetMember']);
    Route::post('/member/{userid}/update',[MemberController::class,'updateMember']);
    Route::delete('/member/{userid}/delete',[MemberController::class,'deleteMember']);
    Route::get('Allmember',[MemberController::class,'FetchAllMember']);
    });


    Route::post('Addmember',[MemberController::class,'Addmember']);
    Route::post('AddNewTithe',[MemberController::class,'AddNewTithe']);
// Route::put('member/{userid}/update',[MemberController::class,'updateMember']);
// Route::delete('member/{userid}/delete',[MemberController::class,'deleteMember']);

//children API
    Route::get('Allchildren',[childrenController::class,'FetchAllChildren']);
    Route::post('Addchildren',[childrenController::class,'AddChild']);
    Route::delete('children/{parentid}/{id}/delete',[childrenController::class,'deleteChildren']);
    Route::put('children/{id}/update',[childrenController::class,'updateChild']);
    Route::get('children/{parentid}/viewallchildren',[childrenController::class,'viewchildren']);
    Route::get('children/{parentid}/viewchild',[childrenController::class,'viewchild']);

//admin API
// Route::get('Alltitle',[adminController::class,'FetchAlltitle']);

    Route::get('Alltitle',[adminController::class,'FetchAlltitle']);
    Route::post('Addtitle',[adminController::class,'Addnewtitle']);
    Route::delete('title/{id}/delete',[adminController::class,'deleteTitle']);
    Route::put('title/{id}/update',[adminController::class,'updateTitle']);


    //National
    Route::get('Allnational',[adminController::class,'FetchAllNatinal']);
    Route::get('getAnational/{code}/get',[adminController::class,'getNational']);
    Route::post('Addnational',[adminController::class,'AddNewNatinal']);
    Route::put('updatenational/{code}/update',[adminController::class,'UpdateNational']);
    Route::delete('deletenational/{code}/delete',[adminController::class,'deleteNational']);

    //State
    Route::get('Allstate',[adminController::class,'FetchAllState']);
    Route::post('Addstate',[adminController::class,'AddNewState']);
    Route::get('State/{scode}',[adminController::class,'GetAState']);
    Route::put('updatestate/{scode}/update',[adminController::class,'UpdateState']);
    Route::delete('deletestate/{scode}/delete',[adminController::class,'deleteState']);

    //area
    Route::post('Addarea',[adminController::class,'AddNewArea']);
    Route::get('Allarea',[adminController::class,'FetchAllarea']);
    Route::get('Area/{acode}',[adminController::class,'GetAnArea']);
    Route::put('updatearea/{acode}/update',[adminController::class,'UpdateArea']);
    Route::delete('deletearea/{acode}/delete',[adminController::class,'deleteArea']);

    //province
    Route::post('Addprovince',[adminController::class,'AddNewProvince']);
    Route::get('Allprovince',[adminController::class,'FetchAllProvince']);
    Route::get('Province/{pcode}',[adminController::class,'GetAProvince']);
    Route::put('Update/{pcode}/update',[adminController::class,'UpdateProvince']);
    Route::delete('Deleteprovince/{pcode}/delete',[adminController::class,'DeleteProvince']);

    //circuit
    Route::post('Addcircuit',[adminController::class,'AddNewcircuit']);
    Route::get('Allcircuit',[adminController::class,'FetchAllCircuit']);
    Route::get('Circuit/{cicode}',[adminController::class,'GetACircuit']);
    Route::put('UpdateCicuit/{cicode}/update',[adminController::class,'UpdateCircuit']);
    Route::delete('DeleteCircuit/{cicode}/delete',[adminController::class,'DeleteCircuit']);


    //District
    Route::post('AddDistrict',[adminController::class,'AddNewDistrict']);
    Route::get('AllDistrict',[adminController::class,'FetchAllDistrict']);
    Route::get('District/{dcode}',[adminController::class,'GetADistrict']);
    Route::put('UpdateDistrict/{dcode}/update',[adminController::class,'UpdateDistrict']);
    Route::delete('DeleteDistrict/{dcode}/delete',[adminController::class,'DeleteDistrict']);

    //Parish
    Route::post('AddParish',[adminController::class,'AddNewParish']);
    Route::get('AllParish',[adminController::class,'FetchAllParish']);
    Route::get('Parish/{picode}',[adminController::class,'GetAParish']);
    Route::put('UpdateParish/{picode}/update',[adminController::class,'UpdateParish']);
    Route::delete('DeleteParish/{picode}/delete',[adminController::class,'DeleteParish']);


    //get all parishes or one parish
    Route::get('getAllParishes',[adminController::class,'FetchAllParishes']);
    Route::get('getAParish/{parishcode}',[adminController::class,'FetchAllParishes']);

    // EVENT END POINT
    Route::post('AddEvent',[adminController::class,'AddNewEvent']);
    Route::get('AllEvent',[adminController::class,'FetchAllEvent']);
    Route::get('Event/{EventId}',[adminController::class,'GetAnEvent']);
    Route::post('updateEvent/{EventId}/update',[adminController::class,'updateEvent']);
    Route::delete('DeleteEvent/{EventId}/delete',[adminController::class,'DeleteEvent']);





