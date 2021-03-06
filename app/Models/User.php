<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use JWTAuth;

class User extends Authenticatable implements JWTSubject
{

    protected $table = 'users';
    public $timestamps = true;
    protected $fillable = array('fullName','idRol', 'code', 'password', 'email');
    
    protected $hidden = array('remember_token','password');

    public function rol(){

        return $this->hasOne(Rol::Class);

    }

    public function scopeFilters($query,$datos){
    	if(isset($datos["fullname"])){
    		$query->where("fullname",'like',"%".$datos["fullname"]."%");
    	}
        if(isset($datos["idRol"])){
            if(is_array($datos["idRol"])){
                $query->whereIn("idRol",$datos["idRol"]);
            }else{
                $query->where("idRol",$datos["idRol"]);
            }
        }
    	if(isset($datos["code"])){
    		$query->where("code",'like',"%".$datos["code"]."%");
    	}
    	
    	if(isset($datos["email"])){
    		$query->where("email",'like',"%".$datos["email"]."%");
    	}
    	
    	return $query;
    		
    }
    public function scopeIndex($query,$datos){
        
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->code;
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function getCode(){

        /**
         * Retorna el código de la persona que está en sesión
         *
         * Codigo de ejemplo profesor Jairo
         * se puede usar para probar el método ScheduleController@now
         *
         *
         * @return string
         * TODO: cambiar codigo ejemplo por lo que esta aqui abajo - retorna el usuario logueado
         *return \Auth::user()->code;
         */

        return 'T00010915';


    }


}