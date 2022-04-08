<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;


class UserController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function update(Request $request)
    {
        //PUT  api/user (name, email, birthdate, city, work, password, password_confirm)
        $array = ['error'=>''];

        $name             = $request->input('name');
        $email            = $request->input('email');
        $birthdate        = $request->input('birthdate');
        $city             = $request->input('city');
        $work             = $request->input('work');
        $password         = $request->input('password');
        $password_confirm = $request->input('password_confirm');

        $user = User::find($this->loggedUser['id']);

        //NAME
        if ($name){
            $user->name = $name;
        }

        //EMAIL
        if ($email){
            if ($email != $user->email){
                $emailExists = User::where('email', $email)->count();
                if ($emailExists === 0){
                    $user->email = $email;
                }else{
                    $array['error'] = 'E-mail já existe!';
                    return $array;
                }
            }
        }

        //BIRTHDATE
        if ($birthdate){
            if (strtotime($birthdate) === false){
                $array['error'] = 'Data de nascimento inválida!';
                return $array;
            }
        }

        //CITY
        if ($city){
            $user->city = $city;
        }

        //WORK
        if ($work){
            $user->work = $work;
        }

        //PASSWORD
        if ($password && $password_confirm){
            if ($password === $password_confirm){
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $user->password = $hash;
            }else{
                $array['error'] = 'As senhas não conferem!';
                return $array;
            }
        }

        $user->save();

        return $array;
    }
}
