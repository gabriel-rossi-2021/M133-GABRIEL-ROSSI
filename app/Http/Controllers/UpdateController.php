<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class UpdateController extends Controller
{
    public function index()
    {
        // RÉCUPÉRER LE NOM D'UTILISATEUR DE LA SESSION OUVERTE
        $sessionUsername = session('user')->username;

        // CHEMIN VERS LES FICHIERS
        $authFilePath = base_path('resources/data/connexion.txt');
        $dataFilePath = base_path('resources/data/data.txt');

        // LECTURE DU FICHIER connexion.txt
        $users = file($authFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // RECHERCHER LA LIGNE CORRESPONDANTE AU NOM D'UTILISATEUR
        $lineNumber = false;
        foreach ($users as $key => $user) {
            $credentials = explode(':', $user);
            if ($sessionUsername == $credentials[0]) {
                $lineNumber = $key;
                session(['lineNumber' => $lineNumber]); // PERMET DE LE RECUPERER DANS D'AUTRE FONCTION
                break;
            }
        }

        // LECTURE DU FICHIER data.txt
        $dataLines = file($dataFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // RÉCUPÉRER LES DONNÉES DE L'UTILISATEUR
        $dataLine = $dataLines[$lineNumber];
        $data = explode(':', $dataLine);

        $email = $data[0];
        $civilite = $data[1];
        $rue = $data[2];
        $npa = $data[3];
        $ville = $data[4];

        // Passez les données à la vue
        return view('updateCompte', compact('sessionUsername','email', 'civilite', 'rue', 'npa', 'ville'));
    }

    public function update(Request $request)
    {
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
            'username.regex' => "Le nom d'utilisateur ne doit pas contenir de majuscules et de '@'.",
            'pwd.regex' => "Le mot de passe doit contenir au moins 8 caractères et une majuscule.",
            'email.regex' => "L'email doit contenir un '@' et un '.'",
            'rue.regex' => "La rue ne doit contenir que des lettres, des espaces et des apostrophes.",
            'npa.regex' => "Le NPA doit être un nombre entre 1000 et 9658.",
            'ville.regex' => "La ville ne doit contenir que des lettres, des espaces et des apostrophes."
        ]);


        // RÉCUPÉRER LE NOM D'UTILISATEUR DE LA SESSION OUVERTE
        $sessionUsername = session('user')->username;

        // CHEMIN VERS LES FICHIERS
        $authFilePath = base_path('resources/data/connexion.txt');
        $dataFilePath = base_path('resources/data/data.txt');

        // LECTURE DU FICHIER connexion.txt
        $users = file($authFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // RECHERCHER LA LIGNE CORRESPONDANTE AU NOM D'UTILISATEUR
        $lineNumber = false;
        foreach ($users as $key => $user) {
            $credentials = explode(':', $user);
            if ($sessionUsername == $credentials[0]) {
                $lineNumber = $key;
                break;
            }
        }

        // LECTURE DES FICHIERS
        $authLines = file($authFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $dataLines = file($dataFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // RÉCUPÉRER LES DONNÉES DE L'UTILISATEUR
        $dataLine = $dataLines[$lineNumber];
        $data = explode(':', $dataLine);

        $login = $validateData['username'];
        $pwd = $validateData['pwd'];
        $email = $validateData['email'];
        $civilite = $validateData['civilite'];
        $rue = $validateData['rue'];
        $npa = $validateData['npa'];
        $ville = $validateData['ville'];
        // HASHAGE MOT DE PASSE
        $pwdHash = Hash::make($pwd);

        // MODIFIER LE FICHIER connexion.txt
        $authLines[$lineNumber] = $login . ":" . $pwdHash;
        $updatedAuth = implode("\n", $authLines);
        file_put_contents($authFilePath, $updatedAuth, LOCK_EX);
        // Mettre à jour le nom d'utilisateur dans la session
        session(['user' => (object) ['username' => $login]]);

        // MODIFIER LE FICHIER data.txt
        $dataLines[$lineNumber] = $email . ":" . $civilite. ":" . $rue . ":" . $npa . ":" . $ville;
        $updatedData = implode("\n", $dataLines);
        file_put_contents($dataFilePath, $updatedData, LOCK_EX);

        // MESSAGE DE SUCCÈS
        return redirect()->route('vue_index')->with('success', 'Les informations ont été mises à jour');
    }
}
