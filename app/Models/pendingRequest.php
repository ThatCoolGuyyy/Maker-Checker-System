<?php

namespace App\Models;

use App\Enums\RequestTypeEnums;
use App\Enums\UserStatusEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class pendingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'approval_admin_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'status',
        'request_type'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function approval_admins()
    // {
    //     return $this->belongsTo(approval_admins_table::class);
    // }
    protected static function pendingRequestFactory(){
        return \database\Factories\pendingRequestFactory::new();
    }

    protected $casts = [
        'status' => UserStatusEnums::class,
        'request_type' => RequestTypeEnums::class,
    ];
}
