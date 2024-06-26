<?php

namespace App\Models;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

/**
 * @property string hash
 * @property string createdAt
 * @property string secretText
 * @property string expireAfter
 * @property int expireAfterViews
 */
class Secret extends Model
{
    use HasFactory;
    protected $fillable = ['hash', 'secretText', 'createdAt', 'expireAfter', 'expireAfterViews'];
    protected $primaryKey = 'hash';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
}
