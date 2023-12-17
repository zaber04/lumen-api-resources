<?php

namespace Zaber04\LumenApiResources\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ErrorLog extends Model
{
    use HasUuids; // primary key uuid
    use HasFactory;

    protected $fillable = [
        'url',
        'param',
        'body',
        'controller',
        'functionName',
        'statusCode',
        'message',
        'error',
        'ip'
    ];
}
