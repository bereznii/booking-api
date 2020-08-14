<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Log
 * @package App\Models
 */
class Log extends Model
{
    /**
     * @var string
     */
    protected $table = 'logs';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    public $fillable = [
        'type',
        'context',
        'message',
        'extra'
    ];
}
