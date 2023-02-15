<?php
 namespace  App\Http\Traits;

trait ResponseTraits
{
    public function SuccessResponse( $message, $status = 200)
    {
        return response ([
            'success' => true,
            'message' => $message,
        ], $status);
    }

    public function ErrorResponse($message, $status = 400)
    {
       return response  ([
            'success' => false,
            'message' => $message,
       ],$status);
    }

}