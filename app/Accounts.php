<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Accounts extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'accounts';
    protected $primaryKey = 'id';

    protected $fillable = [
        'number',
        'balance',
        'currency',
        'user_id'
    ];

    public function getBalanceAttribute($value)
    {
        return number_format($value, 2, '.', '');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
