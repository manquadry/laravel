<?php

namespace App\Http\Controllers\Api;

use App\Models\tithe;
use App\Models\member;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\juvelineharvest;
use Illuminate\Http\JsonResponse;
// use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Requests\LoginRequest;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\ResponseTrait\original;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;




class MemberController extends Controller
{
    use HttpResponses;





    // public function login(LoginRequest $request){


    //     $member  = member::where('email', '=', $request->email)->first();


    //     if(!$member ){

    //         return $this->error(''," Email address not found! Kindly registered as a member to login ",200);
    //     }else{

    //     if ($member && Hash::check($request['password'], $member->password)) {
    //         return $this->success([
    //             $member ,
    //             'Member Login Sucessfully',
    //             'token'=>$member->createToken('API Token of '.$member ->email)->plainTextToken
    //         ]);

    //     }else{

    //         return response()->json([
    //             'status' => 500,
    //             'message' => 'Something went wrong',
    //         ], 200);
    //     }
    //     }


    // }


    public function login(LoginRequest $request){


        $member  = member::where('email', '=', $request->email)->first();
        if (!$member) {
            return $this->error('',
            "Email address not found! Kindly register as a member to login",
            200);
        } else {
            if ($member && Hash::check($request['password'], $member->password)) {

                $thumbnailPath =Storage::url($member->thumbnail);
                 $thumnailPublicpath = URL::to($thumbnailPath);


                if( $member['role']==='Client'){

                    $response = [
                        'userAbilities' => [
                            [
                                'action' => 'read',
                                'subject' => 'Auth',
                            ],
                            [
                                'action' => 'read',
                                'subject' => 'AclDemo',
                            ],
                            // ... add other abilities as needed
                        ],
                        'accessToken' => $member->createToken('API Token of ' . $member->email)->plainTextToken,
                        'userData' => [
                            // $member
                            'id' => $member->id,
                            'fullName' => $member->sname, // Adjust the attribute names accordingly
                            'username' => $member->username,
                            'avatar' =>$thumnailPublicpath,
                            'email' => $member->email,
                            'role' => $member->role,
                            // ... add other user data as needed
                        ],
                    ];
                }else {
                    $response = [
                        'userAbilities' => [
                            [
                                'action' => 'manage',
                                'subject' => 'all',
                            ],
                            // ... add other abilities as needed
                        ],
                        'accessToken' => $member->createToken('API Token of ' . $member->email)->plainTextToken,
                        'userData' => [
                            // $member
                            'id' => $member->id,
                            'fullName' => $member->sname .' '. $member->fname, // Adjust the attribute names accordingly
                            'username' => $member->username,
                            'avatar' => $member->$thumnailPublicpath,
                            'email' => $member->email,
                            'role' => $member->role,
                            // ... add other user data as needed
                        ],
                    ];
                }
                return response()->json($response);
            } else {
                $response = [
                    'status' => 'false', // or 'error' based on your preference
                    'message' => 'Invalid credentials',
                ];

                return response()->json($response,
                401);
            }

        }
    }


    public function Addmember(StoreUserRequest $request)
    {



        $fetchparish=adminController::FetchAllParishes($request->parishcode)->original['Allparish'];

        if( !$fetchparish){

            return response()->json([
                'status' => 500,
                'message' => 'Parish does not exist',
            ], 200);


        }

        $parishNames =implode(', ', array_column($fetchparish, 'parishname'));
        $member = validator($request->all());
        $ParismemberCount = member::where('parishcode',$request->parishcode)->count();

        // $userAgent = $request->header('User-Agent');




        if ($ParismemberCount == 0) {
            $ParismemberCount = 1;
            $num_padded = sprintf("%02d", $ParismemberCount);
        } elseif ($ParismemberCount < 10) {
            $ParismemberCount = $ParismemberCount + 1;
            $num_padded = sprintf("%02d", $ParismemberCount);
        } else {
            $num_padded = $ParismemberCount + 1;
        }


        if ($request->hasFile('thumbnail')) {

            $fileUploaded = $request->file('thumbnail');
            $memberNewPic = $request->parishcode.$num_padded.'.'. $fileUploaded->getClientOriginalExtension();


            $thumbnailPath = $fileUploaded->storeAs('thumbnails', $memberNewPic, 'public');

               // Store new profile image


            // $baseUrl = config('app.url') . '/laravel/storage/app/public/'. $thumbnailPath;
        } else {
            $thumbnailPath = ""; // Or provide a default image path
        }

        $member = member::create([
            'UserId' => $request->parishcode. $num_padded,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'sname' => $request->sname,
            'fname' => $request->fname,
            'mname' => $request->mname,
            'Gender' => $request->Gender,
            'dob' => $request->dob,
            'mobile' => $request->mobile,
            'Altmobile' => $request->Altmobile,
            'Residence' => $request->Residence,
            'Country' => $request->Country,
            'State' => $request->State,
            'City' => $request->City,
            'Title' => $request->Title,
            'dot' => $request->dot,
            'MStatus' => $request->MStatus,
            'ministry' => $request->ministry,
            'Status' => $request->Status,
            'thumbnail' => $thumbnailPath,
            'parishcode' => $request->parishcode,
            'parishname'=> $parishNames,
            'role'=>'Client',
        ]);


        if ($member) {
            return $this->success([
                $member,
                'Member created sucessfully',
                // 'token'=>$member->createToken('API Token of '.$member->email)->plainTextToken
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
            ], 200);
        }



    }

    public function FetchAllMember()
    {

        $members = member::with('children')->get();




        if ($members->count() > 0) {
            return response()->json([
                'status' => 200,
                'message' => 'Record fetched successfully',
                'members ' => $members,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message ' => 'No records found!',
            ], 200);
        }

    }


    public function GetMember($UserId)
    {
        $member = member::with('children')->where('UserId','=', $UserId)->get();


        if ($member) {
            return response()->json([
                'status' => 200,
                'message' => 'Record fetched successfully',
                'member ' => $member,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'User not found',
            ], 404);
        }

    }

    public function updateMember(Request $request, String $UserId)
    {



        $validator = Validator::make($request->all(), [
            'email' => 'required|email |max:191',
            'sname' => 'required|string|max:191',
            'fname' => 'required|string|max:191',
            'mname' => 'required|string|max:191',
            'Gender' => 'required|string|max:191',
            'dob' => 'required|string|max:191',
            'mobile' => 'required|string|max:191',
            'Altmobile' => 'required|string|max:191',
            'Residence' => 'required|string|max:191',
            'Country' => 'required|string|max:191',
            'State' => 'required|string|max:191',
            'City' => 'required|string|max:191',
            'Title' => 'required|string|max:191',
            'dot' => 'required|string|max:191',
            'MStatus' => 'required|string|max:191',
            'ministry' => 'required|string|max:191',
            'Status' => 'required|string|max:191',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);



        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'error' => $validator->messages(),
            ], 422);

        } else {

            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $Thumbnail =  $UserId.'.'. $file->getClientOriginalExtension();
                $thumbnailPath = $file->storeAs('thumbnails', $Thumbnail, 'public');
            } else {
                $thumbnailPath = null; // Or provide a default image path
            }

            $fetchparish=adminController::FetchAllParishes($request->parishcode)->original['Allparish'];
            $parishNames =implode(', ', array_column($fetchparish, 'parishname'));

            $member = validator($request->all());

            $member = member::where('UserId', '=', $UserId)->first();

            if ($member) {
                    $member->update([
                        // 'UserId' => $request->UserId,
                        'email' => $request->email,
                        // 'password' => $request->password,
                        'sname' => $request->sname,
                        'fname' => $request->fname,
                        'mname' => $request->mname,
                        'Gender' => $request->Gender,
                        'dob' => $request->dob,
                        'mobile' => $request->mobile,
                        'Altmobile' => $request->Altmobile,
                        'Residence' => $request->Residence,
                        'Country' => $request->Country,
                        'State' => $request->State,
                        'City' => $request->City,
                        'Title' => $request->Title,
                        'dot' => $request->dot,
                        'MStatus' => $request->MStatus,
                        'ministry' => $request->ministry,
                        'Status' => $request->Status,
                        'thumbnail' => $thumbnailPath,
                        'parishcode' => $request->parishcode,
                        'parishname'=> $parishNames,
                    ]);
                    return response()->json([
                        'status' => 200,
                        'message' => 'Member information updated Sucessfully !',
                        'member'=> $member,
                    ],200);

            } else {

                    return response()->json([
                        'status' => 500,
                        'message' => 'Update failed as user is not found',
                    ], 200);

            }
        }

    }
    public function deleteMember($UserId){

        $member = member::where('UserId', '=', $UserId)->first();
        if ($member) {

            $member->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Member deleted  successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'User/Member not found',
            ], 404);
        }


    }

    public function AddNewTithe(Request $request)
    {

     $validator = Validator::make($request->all(), [
      //validator used in input data(Add New Parish)-copy and paste
      'UserId'       => 'required|string|max:191',
      'pymtdate' => 'required|string|max:191',
      'Amount'   => 'required|string|max:191',
      'pymtImg'    => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
      // we dont have to add picode bc we will generate it ourselve
     ]);

       if ($validator->fails()) {
      return response()->json([
       'status' => 422,
       'error'  => $validator->messages(),
      ], 422);

     } else {

         $member = member::where('UserId',$request->UserId)->get();

      if ($request->hasFile('pymtImg')) {

       $fileUploaded = $request->file('pymtImg');
       $paymentImg  = $request->pymtdate.''. $request->UserId . '.' . $fileUploaded->getClientOriginalExtension();
       $pymtImgPath = $fileUploaded->storeAs('pymtImgs', $paymentImg, 'public');
      } else {
       $pymtImgPath = ""; // Or provide a default image path
      }
        if(!$member){
            return response()->json([
                'status' => 500,
                'message' => 'Member does not exist',
            ], 200);
        }else{

        $Surname=$member[0]['sname'];
        $FirstName=$member[0]['fname'];
        $MiddleName=$member[0]['mname'];
        $pariscode=$member[0]['parishcode'];
        $parisname=$member[0]['parishname'];


            $tithe = tithe::create([
            'UserId'       => $request->UserId,
            'FullName'     =>$Surname.' '.$FirstName.' '.$MiddleName,
            'pymtdate' => $request->pymtdate,
            'Amount'   => $request->Amount,
            'parishcode'     =>$pariscode,
            'parishname'        =>$parisname,
            'pymtImg'   => $pymtImgPath,
            ]);



        }

        if ($tithe) {

        return response()->json([
            'status'  => 200,
            'message' => ' Tithe paid sucessfully',
            'tithe'=> $tithe,
        ], 200);
        } else {

        return response()->json([
            'status'  => 500,
            'message' => 'Something went wrong '  . ' tithe not created',
        ], 200);
        }

    }
    }

    public function GetATithe($UserId)
    {
     $tithe = tithe::where('UserId', '=', $UserId)->first();
     if ($tithe) {
      return response()->json([
       'status'  => 200,
       'message' => $UserId . ' Record fetched successfully',
       'tithe '  => $tithe,
      ], 200);
     } else {
      return response()->json([
       'status'  => 404,
       'message' => 'User not found',
      ], 404);
     }

    }

    public function GetAllParishTithe($parishcode)
    {
     $tithe = tithe::where('parishcode', '=', $parishcode)->get();
     if ($tithe) {
      return response()->json([
       'status'  => 200,
       'message' => $parishcode . ' Record fetched successfully',
       'tithe '  => $tithe,
      ], 200);
     } else {
      return response()->json([
       'status'  => 404,
       'message' => 'User not found',
      ], 404);
     }

    }


    public function UpdateTithe(Request $request, String $UserId)
    {



        $validator = Validator::make($request->all(), [
            //validator used in input data(tithe)-copy and paste
      //'UserId'       => 'required|string|max:191',
      'pymtdate' => 'required|string|max:191',
      'Amount'   => 'required|string|max:191',
      'pymtImg'  => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
      //'sname' => 'required|string|max:191',
      //'fname' => 'required|string|max:191',
      //'mname' => 'required|string|max:191',
      //'parishcode'  =>'required|string|max:191',
      //'parishname' =>'required|string|max:191',

      // we dont have to add userId bc we will generate it ourselve
        ]);



        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'error' => $validator->messages(),
            ], 422);

        } else {

            if ($request->hasFile('pymtImg')) {
                $file = $request->file('pymtImg');
                $pymtImg =  $request->pymtdate.''. $UserId .'.'. $file->getClientOriginalExtension();
                $pymtImgPath = $file->storeAs('pymtImgs', $pymtImg, 'public');
            } else {
                $pymtImgPath = null; // Or provide a default image path
            }

            $fetchparish=adminController::FetchAllParishes($request->parishcode)->original['Allparish'];
            $parishNames =implode(', ', array_column($fetchparish, 'parishname'));

            $tithe = validator($request->all());

            $tithe = tithe::where('UserId', '=', $UserId)->first();

            if ($tithe) {
                    $tithe->update([
                        //'UserId'       => $request->UserId,
                        //'sname'  =>$request->sname,
                        //'fname'  =>$request->fname,
                        //'mname'  =>$request->mname,
                        'pymtdate' => $request->pymtdate,
                        'Amount'   => $request->Amount,
                        //'parishcode'     =>$request->pariscode,
                        //'parishname'        =>$request->parisname,
                        'pymtImg'   => $pymtImgPath,
                    ]);
                    return response()->json([
                        'status' => 200,
                        'message' => 'Tithe information updated Sucessfully !',
                        'tithe'=> $tithe,
                    ],200);

            } else {

                    return response()->json([
                        'status' => 500,
                        'message' => 'Update failed as user is not found',
                    ], 200);

            }
        }

    }


    public function DeleteTithe($UserId){

        $tithe = tithe::where('UserId', '=', $UserId)->first();
        if ($tithe) {

            $tithe->delete();
            return response()->json([
                'status' => 200,
                'message' => 'tithe deleted  successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'User/tithe not found',
            ], 404);
        }


    }


    public function AddNewJuvelineHarvest(Request $request)
    {

     $validator = Validator::make($request->all(), [
      //validator used in input data(Add New Parish)-copy and paste
      'UserId'       => 'required|string|max:191',
      'pymtdate' => 'required|string|max:191',
      'Amount'   => 'required|string|max:191',
      'pymtImg'    => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
      // we dont have to add picode bc we will generate it ourselve
     ]);

       if ($validator->fails()) {
      return response()->json([
       'status' => 422,
       'error'  => $validator->messages(),
      ], 422);

     } else {

         $member = member::where('UserId',$request->UserId)->get();

      if ($request->hasFile('pymtImg')) {

       $fileUploaded = $request->file('pymtImg');
       $jhpaymentImg  = $request->pymtdate.''. $request->UserId . '.' . $fileUploaded->getClientOriginalExtension();
       $pymtImgPath = $fileUploaded->storeAs('jharvestpymtImgs', $jhpaymentImg, 'public');
      } else {
       $pymtImgPath = ""; // Or provide a default image path
      }
        if(!$member){
            return response()->json([
                'status' => 500,
                'message' => 'Member does not exist',
            ], 200);
        }else{

        $Surname=$member[0]['sname'];
        $FirstName=$member[0]['fname'];
        $MiddleName=$member[0]['mname'];
        $pariscode=$member[0]['parishcode'];
        $parisname=$member[0]['parishname'];


            $juvelineharvest = juvelineharvest::create([
            'UserId'       => $request->UserId,
            'FullName'     =>$Surname.' '.$FirstName.' '.$MiddleName,
            'pymtdate' => $request->pymtdate,
            'Amount'   => $request->Amount,
            'parishcode'     =>$pariscode,
            'parishname'        =>$parisname,
            'pymtImg'   => $pymtImgPath,
            ]);
        }

        if ($juvelineharvest) {

        return response()->json([
            'status'  => 200,
            'message' => ' juvelineharvest paid sucessfully',
            'juvelineharvest'=> $juvelineharvest,
        ], 200);
        } else {

        return response()->json([
            'status'  => 500,
            'message' => 'Something went wrong '  . ' juvelineharvest not created',
        ], 200);
        }

    }
    }

    public function GetAllParishJuvelineDue($parishcode)
    {
     $juvelineharvest = juvelineharvest::where('parishcode', '=', $parishcode)->get();
     if ($juvelineharvest) {
      return response()->json([
       'status'  => 200,
       'message' => $parishcode . ' Record fetched successfully',
       'juvelineharvest '  => $juvelineharvest,
      ], 200);
     } else {
      return response()->json([
       'status'  => 404,
       'message' => 'User not found',
      ], 404);
     }

    }

    public function GetAJuvelineDue($UserId)
    {
     $juvelineharvest = juvelineharvest::where('UserId', '=', $UserId)->first();
     if ($juvelineharvest) {
      return response()->json([
       'status'  => 200,
       'message' => $UserId . ' Record fetched successfully',
       'juvelineharvest '  => $juvelineharvest,
      ], 200);
     } else {
      return response()->json([
       'status'  => 404,
       'message' => 'User not found',
      ], 404);
     }

    }


    public function UpdateJuvelineDue(Request $request, String $UserId)
    {

        $validator = Validator::make($request->all(), [
            //validator used in input data(tithe)-copy and paste
      //'UserId'       => 'required|string|max:191',
      'pymtdate' => 'required|string|max:191',
      'Amount'   => 'required|string|max:191',
      'pymtImg'  => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
      //'sname' => 'required|string|max:191',
      //'fname' => 'required|string|max:191',
      //'mname' => 'required|string|max:191',
      //'parishcode'  =>'required|string|max:191',
      //'parishname' =>'required|string|max:191',

      // we dont have to add userId bc we will generate it ourselve
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'error' => $validator->messages(),
            ], 422);

        } else {

            if ($request->hasFile('pymtImg')) {
                $file = $request->file('pymtImg');
                $jhpaymentImg =  $request->pymtdate.''. $UserId .'.'. $file->getClientOriginalExtension();
                $jhpaymentImgPath = $file->storeAs('jhpaymentImgs', $jhpaymentImg, 'public');
            } else {
                $jhpaymentImgPath = null; // Or provide a default image path
            }

            $fetchparish=adminController::FetchAllParishes($request->parishcode)->original['Allparish'];
            $parishNames =implode(', ', array_column($fetchparish, 'parishname'));

            $juvelineharvest = validator($request->all());

            $juvelineharvest = juvelineharvest::where('UserId', '=', $UserId)->first();

            if ($juvelineharvest) {
                    $juvelineharvest->update([
                        //'UserId'       => $request->UserId,
                        //'sname'  =>$request->sname,
                        //'fname'  =>$request->fname,
                        //'mname'  =>$request->mname,
                        'pymtdate' => $request->pymtdate,
                        'Amount'   => $request->Amount,
                        //'parishcode'     =>$request->pariscode,
                        //'parishname'        =>$request->parisname,
                        'pymtImg'   => $jhpaymentImgPath,
                    ]);
                    return response()->json([
                        'status' => 200,
                        'message' => 'juvelineharvest information updated Sucessfully !',
                        'juvelineharvest'=> $juvelineharvest,
                    ],200);

            } else {

                    return response()->json([
                        'status' => 500,
                        'message' => 'Update failed as user is not found',
                    ], 200);

            }
        }

    }

    public function DeleteJuvelineDue($UserId){

        $juvelineharvest = juvelineharvest::where('UserId', '=', $UserId)->first();
        if ($juvelineharvest) {

            $juvelineharvest->delete();
            return response()->json([
                'status' => 200,
                'message' => 'juvelineharvest deleted  successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'User/juvelineharvest not found',
            ], 404);
        }


    }
    // public function logout()
    // {
    //     Auth::user()->currentAccessToken()->delete();

    //     return $this->success([
    //         'message' => 'You have succesfully been logged out and your token has been removed'
    //     ]);
    // }

}
