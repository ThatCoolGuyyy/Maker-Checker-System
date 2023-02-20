<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Jobs\SendEmail;
use Illuminate\Http\Request;
use App\Enums\UserStatusEnums;
use App\Models\pendingRequest;
use App\Enums\RequestTypeEnums;
use App\Http\Requests\AdminRequest;
use App\Http\Traits\ResponseTraits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PendingRequestController extends Controller
{
    use ResponseTraits;

    public function send_mail(Request $request){
        // $details = [
    	// 	'subject' => 'A new action has been created'
    	// ];
    	
        $job = (new SendEmail()); 

        dispatch($job)->onqueue('database');
    }

    public function create(AdminRequest $adminrequest)
    {
        if($adminrequest->user_id) {
            $user = PendingRequest::where('user_id', $adminrequest->user_id)->first();
            if($user) {
                return $this->ErrorResponse('a request has already been submitted for this user');
            }
        }
        pendingRequest::Create([
            'admin_id' => auth()->user()->id,
            'user_id' => $adminrequest->user_id,
            'first_name' => $adminrequest->first_name,
            'last_name' => $adminrequest->last_name,
            'email' => $adminrequest->email,
            'request_type' => RequestTypeEnums::create,
            'approval_admin_id' => $adminrequest->approval_admin_id,
        ]);

        $this->send_mail($adminrequest);
        return $this->SuccessResponse('User create request submitted successfully. Please wait for approval');


    }
    
    public function update(AdminRequest $adminrequest)
    {
        $user_id = $adminrequest->id;
        $user = pendingRequest::where('user_id', $user_id)->first();
        if(!$user) {
            $message = 'User not found';
            return $this->ErrorResponse($message);
        }
        $user::create([
            'admin_id' => auth()->user()->id,
            'user_id' => $user_id,
            'first_name' => $adminrequest->first_name,
            'last_name' => $adminrequest->last_name,
            'email' => $adminrequest->email,
            'request_type' => RequestTypeEnums::update,
            'approval_admin_id' => $adminrequest->approval_admin_id,
        ]);
        $this->send_mail($adminrequest);
        $message = 'User update request submitted successfully. Please wait for approval';
        return $this->SuccessResponse($message);

    }

    
    public function destroy( AdminRequest $adminrequest)
    {
        $user_id = $adminrequest->id;
        $user = pendingRequest::where('user_id', $user_id)->first();
        if(!$user) {
            return $this->ErrorResponse('User not found');
        }

        $user::create([
            'admin_id' => auth()->user()->id,
            'user_id' => $user_id,
            'first_name' => $adminrequest->first_name,
            'last_name' => $adminrequest->last_name,
            'email' => $adminrequest->email,
            'request_type' => RequestTypeEnums::delete,
            'approval_admin_id' => $adminrequest->approval_admin_id,
        ]);
        $this->send_mail($adminrequest);
        $message='User delete request submitted successfully. Please wait for approval';
        return $this->SuccessResponse($message);
    }

    
    public function reject_request(Request $request)
    {
        $user_id = $request->id;
        $user = pendingRequest::where('user_id', $user_id)->first();
        if ($user->status->value == 'approved'){
                return $this->ErrorResponse('User action request already approved');
        }
        $user->delete();
        return $this->SuccessResponse('User action request rejected successfully');
    }

    public function approve_request(Request $request)
    {
        $user_id = $request->id;
        $user = pendingRequest::where('user_id', $user_id)->first();
        $status = $user->status->value;
        if($status == 'approved') 
        {
            return $this->ErrorResponse('User action request already approved');
        }
        $user->update([
            'status' => UserStatusEnums::approved
        ]);
        $request_type = $user->request_type->value;
        switch($request_type){
            case 'create':
                $user = User::create([
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email
                ]);
                return $this->SuccessResponse('User create request approved successfully');
            case 'update':
                $users = User::where('id', $user_id)->first();
                $users->update([
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email
                ]);
                return $this->SuccessResponse('User update request approved successfully');
            case 'delete':
                $user = User::find($user_id);
                $user->delete();
                return $this->SuccessResponse('User delete request approved successfully');
        }
}

    public function pending_requests( Request $request)
    {
        $pending_requests = pendingRequest::where('status', 'pending')->get();
        return response()->json([
            'pending_requests' => $pending_requests
        ], 200);

    
    }
    public function individual_pending_requests( Request $request)
    {
        $id = $request->id;
        $pending_requests = pendingRequest::where('user_id', $id)->get();
        if(!$pending_requests) {
            return $this->ErrorResponse('Request not found');
        }
        return response()->json([
            'pending_requests' => $pending_requests,
        ], 200);
    }

}
