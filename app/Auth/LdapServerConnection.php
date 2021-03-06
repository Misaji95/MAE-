<?php
/**
 * Created by PhpStorm.
 * User: Full Stack JavaScrip
 * Date: 13/07/2016
 * Time: 14:45
 */

namespace App\Auth;

use App\Models\User;


class LdapServerConnection
{
    private $usuario;

    public function __construct()
    {
        $this->rdn = env('LDAP_RDN');
        $this->hostname = env('LDAP_HOSTNAME');

    }

    public function verificarUsuario($username, $password)
    {

        if (!$username or !$password ) {
            dd('Datos de acceso faltantes.', 401);
            return false;
        }
        if (!extension_loaded('ldap')) {
            dd('PHP LDAP extension not loaded.', 418);
            return false;
        }
        $conn = ldap_connect($this->hostname);
        if (!$conn) {
            dd("Could not connect to LDAP host $this->hostname: " . ldap_error($conn), 401);
            return false;
        }
        if (!$con = @ldap_bind($conn, "uid=" . $username . ',' . $this->rdn, $password)) {

                //dd('Could not bind to AD: ' . ldap_error($conn), 401);
                return false;
            }
        
            $result = ldap_search($conn, $this->rdn, 'uid=' . $username, array('uid', 'cn', 'mail'));
            $datos = ldap_get_entries($conn, $result);

            for ($i=0; $i<$datos["count"]; $i++) {
                $nombre =$datos[$i]["cn"][0] ;
                $codigo =  $datos[$i]["uid"][0];
                $correo = $datos[$i]["mail"][0];
            }
            $this->usuario = new user();
            $this->usuario->code = strtoupper($codigo);
            $this->usuario->password = ($password);
            $this->usuario->fullName = $nombre;
            $this->usuario->email= $correo;
            $this->usuario->idRol= 2;

            $usuario=User::where("code",$this->usuario->code)->first();
            
             if(is_null($usuario)){
                $this->usuario->save();  
            }else{

                $this->usuario->update();
                $this->usuario=$usuario;

            }




            return true;

    }

    public function verificarUsuarioById($codigoUtbId)
    {

        if (!extension_loaded('ldap')) {
            dd('PHP LDAP extension not loaded.', 418);
            return false;
        }
        $conn = ldap_connect("$this->hostname");
        if (!$conn) {
            dd("Could not connect to LDAP host $this->hostname: " . ldap_error($conn), 401);
            return false;
        }
        if (!$con = ldap_bind($conn, "uid=readonly"  . ',' . $this->rdn,'read_only_utbvirtual')) {
            dd('Could not bind to AD: ' . ldap_error($conn), 401);
            return false;
        } else {
            $result = ldap_search($conn, $this->rdn, 'uid=' . $codigoUtbId, array('uid', 'cn', 'mail'));
            $datos = ldap_get_entries($conn, $result);

            for ($i = 0; $i < $datos["count"]; $i++) {
                $nombre = $datos[$i]["cn"][0];
                $codigo = $datos[$i]["uid"][0];
                $correo = $datos[$i]["mail"][0];
            }

            $this->usuario = new User();
            $this->usuario->fullName = $nombre;
            $this->usuario->code = $codigo;
            $this->usuario->email = $correo;
            $this->usuario->id=User::where("code",$codigo)->first()->id;
        }

        return $this->usuario;
    }

    public function getUsuario()
    {   

        return $this->usuario;
    }


}