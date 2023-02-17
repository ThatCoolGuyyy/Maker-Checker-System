<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\pendingRequest;
use App\Http\Traits\ResponseTraits;
use App\Models\approval_admins_table;

class ApprovalMiddleware
{
    use ResponseTraits;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // $admins = str_split($request->approval_admins);
        // $user = approval_admins_table::where('id', $request->id)->first();
        // if($admins)
        // return $next($request);
        $user_id = $request->id;
        $user = pendingRequest::where('user_id', $user_id)->first();

        if(!$user){
            return $this->ErrorResponse('User not found');
        }
        
        if($request->approval_admin_id == $user->approval_admin_id)
        {
            return $next($request);
        }
        return $this->ErrorResponse('You are not authorized');
        
    
    }
}
