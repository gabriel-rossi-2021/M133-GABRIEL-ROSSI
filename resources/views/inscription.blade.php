@extends('layouts.mainLayout')

@section('content')
    <section id="section-inscriptinon">
        <div class="container-fluid" id="container-flui-inscription">
            <div class="div-connexion-form">
                <h1>Inscription</h1>
                <br>
                <br>
                <form action="{{ route('store_inscription') }}" method="POST">
                    @csrf
                    <!-- Name -->
                    <div class="form-group label-floating">
                      <label class="control-label form-label" for="username">Nom d'utilisateur :</label>
                      <input class="form-control" id="username" type="text" name="username" placeholder="admin" required>
                      @error('username_register')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="pwd" class="form-label">Mot de passe :</label>
                        <input type="password" class="form-control @error('pwd') is-invalid @enderror" id="pwd" name="pwd">
                        @error('pwd_register')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="email" class="form-label">Adresse email :</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email">
                        @error('email_register')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="civilite" class="form-label">Civilité :</label>
                        <select class="form-select" name="civilite">
                            <option value="Madame">Madame</option>
                            <option value="Monsieur">Monsieur</option>
                            <option value="Autres">Autres</option>
                        </select>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="rue" class="form-label">Rue :</label>
                        <input type="text" class="form-control @error('rue') is-invalid @enderror" id="rue" name="rue" placeholder="">
                        @error('rue_register')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="npa" class="form-label">NPA :</label>
                        <input type="number" min="1000" max="9658" class="form-control @error('npa') is-invalid @enderror" id="npa" name="npa" placeholder="">
                        @error('npa_register')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="ville" class="form-label">Ville :</label>
                        <input type="text" class="form-control @error('ville') is-invalid @enderror" id="ville" name="ville" placeholder="">
                        @error('ville_register')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- captcha -->
                    <div style="margin-top: 2%;" class="form-group label-floating">
                        <label for="captcha" class="control-label form-label">Résoudre l'addition : </label>
                        <span id="captcha" style="font-size: 20px"><strong>{{ $number1 }} + {{ $number2}} = </strong></span>
                        <input type="number" class="form-control" id="captcha" name="captcha_inscription" min="1" max="20" required>
                    </div>

                    <div style="margin-top: 5%;" class="form-group">
                        @error('error')
                            <span class="alert alert-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Form Submit -->
                    <div class="form-submit mt-5">
                        <button style="background:#2c6db8;color:white;width:100%;"class="btn btn-common" type="submit">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
