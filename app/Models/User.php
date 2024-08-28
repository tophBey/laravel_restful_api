<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


// implement supaya bisa login
class User extends Model implements Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $keyType = 'int'; 
    public $timeStamps = true;
    public $incrementing = true;

    protected $fillable = [
        'username',
        'password',
        'name'
    ];


    // warm1
    public function contacts(): HasMany{
        return $this->hasMany(Contact::class,'user_id','id');
    }


    // implement method dari autenticable
    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(){
        return 'username';

    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier(){
        return $this->username;

    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(){
        return $this->password;

    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken(){
        return $this->token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value){

        $this->token = $value;

    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName(){
        return 'token';

    }
}



