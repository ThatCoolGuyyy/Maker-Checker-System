<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\pendingRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PendingRequestController extends Controller
{
   
    public function create(Request $request)
    {
        // $credentials = $request->only('user_id', 'first_name', 'last_name', 'email');
        // $new_request = new pendingRequest();
        // $new_request->admin_id = auth()->user()->id;
        // $new_request->request_type = 'create';

        // $new_request->save($credentials);

        pendingRequest::create([
            'admin_id' => auth()->user()->id,
            'user_id' => $request->user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'request_type' => 'create'
        ]);

        return response()->json([
            'message' => 'Request submitted successfully. Please wait for approval'
        ], 200);




    }
    
    public function update(Request $request, pendingRequest $pendingRequest)
    {
        $user_id = $request->route('id');
        $user = pendingRequest::where('user_id', $user_id)->first();
        if(!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        // elseif($user->status == 'pending'){
        //     return response()->json([
        //         'message' => 'User update request already submitted. Please wait for approval'
        //     ], 200);
        // }
        $user::create([
            'admin_id' => auth()->user()->id,
            'user_id' => $user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'request_type' => 'update'
        ]);

        return response()->json([
            'message' => 'User update request submitted successfully. Please wait for approval'
        ], 200);

    }

    
    public function destroy(pendingRequest $pendingRequest, Request $request)
    {
        $user_id = $request->route('id');
        $user = pendingRequest::where('user_id', $user_id)->first();
        if(!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        elseif($user->status == 'pending'){
            return response()->json([
                'message' => 'User action request already submitted. Please wait for approval'
            ], 200);
        }
        $user::create([
            'admin_id' => auth()->user()->id,
            'user_id' => $request->user_id,
            'request_type' => 'delete'
        ]);
        return response()->json([
            'message' => 'User delete request submitted successfully. Please wait for approval'
        ], 200);
    }

    public function approve_request(Request $request)
    {
        $user_id = $request->route('id');
        $user = pendingRequest::where('user_id', $user_id)->first();
        if(!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        elseif($user->status == 'approved'){
            return response()->json([
                'message' => 'User action request already approved'
            ], 200);
        }
        elseif($user->status == 'rejected'){
            return response()->json([
                'message' => 'User action request already rejected'
            ], 200);
        }
        $user->update([
            'status' => 'approved'
        ]);
        $request_type = $user->request_type;
        if($request_type == 'create'){
            $user = User::create([
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email
            ]);
            return response()->json([
                'message' => 'User create request approved successfully'
            ], 200);
        }
        elseif($request_type == 'update'){
            $users = User::where('id', $user->user_id)->first();
            $users->update([
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email
            ]);
            return response()->json([
                'message' => 'User update request approved successfully'
            ], 200);
            // $user->first_name = $user->first_name;
            // $user->last_name = $user->last_name;
            // $user->email = $user->email;
            // $user->save();

        }
        else{
            if($request_type == 'delete'){
            $user = User::where('id', $user->user_id)->first();
            $user->delete();
            return response()->json([
                'message' => 'User delete request approved successfully'
            ], 200);
        }
        
    }
}
    public function reject_request(Request $request)
    {
        $user_id = $request->route('id');
        $user = pendingRequest::where('user_id', $user_id)->first();
        if(!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        elseif($user->status == 'approved'){
            return response()->json([
                'message' => 'User action request already approved'
            ], 200);
        }
        elseif($user->status == 'rejected'){
            return response()->json([
                'message' => 'User action request already rejected'
            ], 200);
        }
        $user->status = 'rejected';
        $user->save();
        return response()->json([
            'message' => 'User action request rejected successfully'
        ], 200);
    }

    public function pending_requests()
    {
        $pending_requests = pendingRequest::where('status', 'pending')->get();
        return response()->json([
            'pending_requests' => $pending_requests
        ], 200);
    }

}
