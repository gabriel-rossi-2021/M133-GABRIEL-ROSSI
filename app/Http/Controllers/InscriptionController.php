<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;


class InscriptionController extends Controller
{
    public function index(){
        // NOMBRE RANDOM POUR CAPTCHA
        $number1 = rand(1, 10);
        $number2 = rand(1, 10);
        // RESULTAT DU CAPTCHA
        $result_inscription = $number1 + $number2;
        session(['captcha_result_inscription' => $result_inscription]);

        return view('inscription', compact('number1', 'number2'));
    }

    public function store(Request $request){
        // VALIDE LES ENTREES
        $validateData = $request->validate([
            'username' => 'required|regex:/^[^A-Z@]*$/|max:20',
            'pwd' => 'required|regex:/^(?=.*[A-Z]).{8,}$/',
            'email' => 'required|regex:/^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/',
            'civilite' => 'required',
            'rue' => 'required|regex:/^[a-zA-Z0-9\s\']{1,255}$/',
            'npa' => ['required','regex:/^(100[0-9]|10[1-9][0-9]|9[0-5][0-9]{2}|965[0-8])$/'],
            'ville' => 'required|regex:/^[a-zA-Z\s\']{1,50}$/',
        ], [
            // MESSAGE D'ERREUR EN CAS DE NON RESPECT DE LA REGEX
            'username_register.regex' => "Le nom d'utilisateur ne doit pas contenir de majuscules et de '@'.",
            'pwd_register.regex' => "Le mot de passe doit contenir au moins 8 caractères et une majuscule.",
            'email_register.regex' => "L'email doit contenir un '@' et un '.'",
            'rue_register.regex' => "La rue ne doit contenir que des lettres, des espaces et des apostrophes.",
            'npa_register.regex' => "Le NPA doit être un nombre entre 1000 et 9658.",
            'ville_register.regex' => "La ville ne doit contenir que des lettres, des espaces et des apostrophes."
        ]);

        // RECUPERER LES DONNEES
        $login = $validateData['username'];
        $pwd = $validateData['pwd'];
        $email = $validateData['email'];
        $civilite = $validateData['civilite'];
        $rue = $validateData['rue'];
        $npa = $validateData['npa'];
        $ville = $validateData['ville'];
        $captcha = $request->input('captcha_inscription');

        // HASHAGE MOT DE PASSE
        $pwdHash = Hash::make($pwd);

        // RECUPERER LE RESULTAT DU CAPTCHA
        $result_inscription = session('captcha_result_inscription');

        // VERIFICATION CAPTCHA
        if ($captcha == $result_inscription){
            // CHEMIN VERS LES FICHIERS
            $authFilePath = base_path('resources/data/connexion.txt');
            $dataFilePath = base_path('resources/data/data.txt');

            // AJOUTER DONNEE DANS CONNEXION.TXT
            $connexionData = $login . ':' . $pwdHash;
            File::append($authFilePath, $connexionData.PHP_EOL);

            // AJOUTER DONNEE DANS DATA.TXT
            $data = $email . ':' . $civilite . ':' . $rue . ':' . $npa . ':' . $ville;
            File::append($dataFilePath, $data.PHP_EOL);

            // REDIRIGER VERS LA PAGE INDEX
            return redirect()->intended('/connexion')->with('success', 'Le compte à été créer, merci de vous connecter');;

        }else{
            return redirect()->back()->withErrors([
                'error' => "Le captcha n'est pas valide"
            ]);
        }
    }
}
