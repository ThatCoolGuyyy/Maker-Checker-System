<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Jobs\SendEmail;
use Illuminate\Http\Request;
use App\Models\pendingRequest;
use App\Http\Traits\ResponseTraits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PendingRequestController extends Controller
{
    use ResponseTraits;

    public function send_mail(Request $request){
        // $details = [
    	// 	'subject' => 'A new action has been created'
    	// ];
    	
        $job = (new SendEmail())
            	->delay(now()->addSeconds(2)); 

        dispatch($job);
    }

    public function create(Request $request)
    {
        if($request->user_id) {
            $user = PendingRequest::where('user_id', $request->user_id)->first();
            if($user) {
                return $this->ErrorResponse('a request has already been submitted for this user');
            }
        }
        pendingRequest::Create([
            'admin_id' => auth()->user()->id,
            'user_id' => $request->user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'request_type' => 'create'
        ]);

        $this->send_mail($request);
        return $this->SuccessResponse('User create request submitted successfully. Please wait for approval');


    }
    
    public function update(Request $request, pendingRequest $pendingRequest, $id)
    {
        $user_id = $request->id;
        $user = pendingRequest::where('user_id', $user_id)->first();
        if(!$user) {
            $message = 'User not found';
            return $this->ErrorResponse($message);
        }
        $user::create([
            'admin_id' => auth()->user()->id,
            'user_id' => $user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'request_type' => 'update'
        ]);
        $this->send_mail($request);
        $message = 'User update request submitted successfully. Please wait for approval';
        return $this->SuccessResponse($message);

    }

    
    public function destroy(pendingRequest $pendingRequest, Request $request, $id)
    {
        $user_id = $request->id;
        $user = pendingRequest::where('user_id', $user_id)->first();
        if(!$user) {
            return $this->ErrorResponse('User not found');
        }
        elseif($user->status == 'pending'){
            $message = 'User action request already submitted. Please wait for approval';
            return $this->SuccessResponse($message);
        }
        $user::create([
            'admin_id' => auth()->user()->id,
            'user_id' => $user_id,
            'request_type' => 'delete'
        ]);
        $this->send_mail($request);
        $message='User delete request submitted successfully. Please wait for approval';
        return $this->SuccessResponse($message);
    }

    public function approve_request(Request $request)
    {
        $user_id = $request->id;
        $user = pendingRequest::where('user_id', $user_id)->first();
        if(!$user) {
            return $this->ErrorResponse('User not found');
        }
        switch($user->status){
            case 'approved':
                return $this->SuccessResponse('User action request already approved');
            case 'rejected':
                return $this->SuccessResponse('User action request already rejected');
        }
        $user->update([
            'status' => 'approved'
        ]);
        $request_type = $user->request_type;
        switch($request_type){
            case 'create':
                $user = User::create([
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email
                ]);
                return $this->SuccessResponse('User create request approved successfully');
            case 'update':
                $users = User::find($user_id);
                $users->update([
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email
                ]);
                return $this->SuccessResponse('User update request approved successfully');
            case 'delete':
                $user = User::where('id', $user->user_id)->first();
                $user->delete();
                return $this->SuccessResponse('User delete request approved successfully');
        }
}
    public function reject_request(Request $request, $id)
    {
        $user_id = $request->id;
        $user = pendingRequest::where('user_id', $user_id)->first();
        if(!$user) {
            return $this->ErrorResponse('User not found');
        }
        switch($user->status){
            case 'approved':
                return $this->ErrorResponse('User action request already approved');
            case 'rejected':
                return $this->ErrorResponse('User action request already rejected');
        }
        $user->status = 'rejected';
        $user->save();
        return $this->SuccessResponse('User action request rejected successfully');
    }

    public function pending_requests()
    {
        $pending_requests = pendingRequest::where('status', 'pending')->get();
        return response()->json([
            'pending_requests' => $pending_requests
        ], 200);
    }

}
