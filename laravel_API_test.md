# Creación de API en Laravel

Para la validación se necesita sanctum, que probablemente ya está instalado:


   composer require laravel/sanctum

En su forma más basica, basta con crear rutas en `routes/api.php` y los controladores
correspondientes en un nuevo controlador en un directorio `Controllers/API/`

La API puede tener un método de registro, pero no es necesario. El el proceso de login
se crea un token que luego se usa para llamar a la API. Luego el logout cierra la
sessión y borra los token del usuario.


# app/Http/Controllers/API/AuthController.php

<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use Validator;
use App\Models\User;

class AuthController extends Controller
{
   

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password')))
        {
            return response()
                ->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['message' => 'Hi '.$user->name.', welcome to home','access_token' => $token, 'token_type' => 'Bearer', ]);
    }

    // method for user logout and delete token
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }
}




# routes/api.php

//API route for login user
Route::post('/login', [AuthController::class, 'login']);

# Grupo protegido por autenticacion
Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/testapi', [AstroController::class, 'testapi']);

    // API route for logout user
    Route::post('/logout', [AuthController::class, 'logout']);
});


## Ejemplo de uso con aplicacion externa


#!/usr/bin/env python3

import requests
from requests.structures import CaseInsensitiveDict
import json

login_url = "http://127.0.0.1:8000/api/login"
logout_url = "http://127.0.0.1:8000/api/logout"
proposal_url = "http://127.0.0.1:8000/api/proposals/2/overview"
test_url = "http://127.0.0.1:8000/api/testapi"
#token = "dd9b7ec5b5b1acd18fbb8a6136516842c6988d95dad18144b55725ceda57a541"
#resp = requests.post(login_url) # , headers=headers
#resp = requests.get(proposal_url) # , headers=headers

data = {
  'email': 'japp@iac.es',
  'password': 'caca'
}

login_response = requests.post(login_url, data=data, verify=True)
login_json = login_response.json()

headers = CaseInsensitiveDict()
headers["Accept"] = "application/json"
login_token = login_json['access_token']
headers["Authorization"] = f"Bearer {login_token}"


response = requests.get(proposal_url, headers=headers, verify=True)

test_url = "http://127.0.0.1:8000/api/testapi"
response = requests.get(test_url, headers=headers, verify=True)


logout_response = requests.post(logout_url, headers=headers, verify=True)



# Convert json string to dict
response_json = json.loads(response.content)




