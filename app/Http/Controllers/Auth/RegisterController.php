<?php

namespace Registro\Http\Controllers\Auth;

use Registro\User;
use Registro\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */

     //Ruta a la que redirecciona despues de registrar
    protected $redirectTo = '/respuesta';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

     //funcion de codigo aleatorio
        function generarCodigo($longitud) {
          $key = '';
          $pattern = '1234567890abcdefghijklmnopqrstuwxyz';
          $max = strlen($pattern)-1;
          for ($i=0; $i < $longitud; $i++) $key .= $pattern{mt_rand(0,$max)};
          return $key;
        }


    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            //La siguiente linea pide password para validar
            //'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
      $code = $this->generarCodigo(12); //variable en donde se define el tamaño del array que crea la funcion generarcodigo
      $email = $data['email'];
      $dates = array('name'=> $data['name'], 'code' => $code);
      $this->Email($dates, $email);
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'code' => $code, //codigo generado aleatoriamente en la funcion de arriba

            //La siguiente linea pide password para crear un usuario
            //'password' => bcrypt($data['password']),
        ]);
    }

    function Email($dates, $email){
      Mail::send('emails.plantilla', $dates, function($message) use ($email){
        $message->subject('Registro Exitoso');
        $message->to($email);
        $message->from('no-repply@test.com', 'Test de Confirmacion');
      });
    }
}
