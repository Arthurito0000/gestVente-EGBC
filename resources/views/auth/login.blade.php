<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login • Stock Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            500: '#3B82F6',
                            600: '#2563EB',
                            700: '#1D4ED8',
                            800: '#1E40AF',
                            900: '#1E3A8A',
                            950: '#0B1220'
                        }
                    }
                }
            }
        };
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@600;700&family=Nunito:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        .font-heading {
            font-family: Inter, system-ui, sans-serif
        }

        .font-body {
            font-family: Nunito, system-ui, sans-serif
        }
    </style>
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>

<body class="font-body min-h-screen bg-white">
    <div class="grid grid-cols-1 md:grid-cols-5 w-full h-screen">
        <!-- Left: Brand/Message -->
        <div
            class="relative bg-gradient-to-br from-primary-800 via-primary-800 to-primary-900 text-white p-8 md:p-16 flex flex-col justify-between md:col-span-3">
            <div>
                <div class="flex items-center gap-3 mb-8 ">
                    <div
                        class="w-12 h-12 bg-white/10 border border-white/20 rounded-xl flex items-center justify-center">
                        <span class="font-heading text-xl"> <img src="{{ asset('images/logo.png') }}" alt="logo"
                                class="h-[30px] items-center" /></span>
                    </div>
                    <div class="font-heading text-lg">Sales Manager EGBC</div>
                </div>

                <h2 class="font-heading text-3xl md:text-4xl leading-tight">Gérez vos stocks avec clarté et précision
                </h2>
                <p class="mt-4 text-white/80 text-base md:text-lg">Suivez vos produits, mouvements et factures en temps
                    réel. Une interface moderne, rapide et efficace.</p>
            </div>

            <div class="mt-12 hidden md:block">
                <ul class="space-y-4 text-white/95">
                    <li
                        class="flex items-start gap-3 bg-white/10 backdrop-blur-sm rounded-lg p-3 border border-white/10">
                        <div class="w-6 h-6 rounded-md bg-white/20 flex items-center justify-center">✓</div>
                        <div>
                            <div class="font-medium">Tableau de bord intuitif</div>
                            <div class="text-sm text-white/80">KPIs clés et mouvements récents en un coup d'œil.</div>
                        </div>
                    </li>
                    <li
                        class="flex items-start gap-3 bg-white/10 backdrop-blur-sm rounded-lg p-3 border border-white/10">
                        <div class="w-6 h-6 rounded-md bg-white/20 flex items-center justify-center">⇅</div>
                        <div>
                            <div class="font-medium">Mouvements ENTREE/SORTIE</div>
                            <div class="text-sm text-white/80">Suivi précis des entrées et sorties de stock.</div>
                        </div>
                    </li>
                    <li
                        class="flex items-start gap-3 bg-white/10 backdrop-blur-sm rounded-lg p-3 border border-white/10">
                        <div class="w-6 h-6 rounded-md bg-white/20 flex items-center justify-center">€</div>
                        <div>
                            <div class="font-medium">Facturation simple</div>
                            <div class="text-sm text-white/80">Générez et consultez vos factures rapidement.</div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right: Form -->
        <div class="p-8 md:p-14 flex items-center justify-center bg-white h-screen md:col-span-2">
            <div class="w-full max-w-md">
                <div class="mb-8">
                    <div class="flex justify-center items-center">
                        <img src="{{ asset('images/logo.png') }}" alt="logo" class="h-[120px] items-center" />
                    </div>
                    <h1 class="font-heading text-2xl text-gray-900">Se connecter</h1>
                    <p class="text-gray-500">Accédez à votre tableau de bord</p>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-md p-6 md:p-8">
                    <form action="{{ route('login') }}" method="POST" class="space-y-6">
                        @csrf
                        @if (session('error'))
                            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16 14a4 4 0 10-8 0m8 0v1a4 4 0 11-8 0v-1m12 6v-1a6 6 0 00-12 0v1m-2 0h16" />
                                    </svg>
                                </span>
                                <input type="email" name="email"
                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition-colors pl-10 pr-3 py-3 text-sm"
                                    placeholder="address mail" />
                            </div>

                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                <input type="password" name="password" id="passwordField"
                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition-colors pl-10 pr-10 py-3 text-sm"
                                    placeholder="••••••••" />
                                <button type="button" id="togglePassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                                    aria-label="Afficher le mot de passe">
                                    <!-- Icône œil ouvert (masqué par défaut) -->
                                    <svg id="eyeOpen" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <!-- Icône œil fermé (visible par défaut) -->
                                    <svg id="eyeClosed" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 11-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox"
                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-600" />
                                Se souvenir de moi
                            </label>
                            <a class="text-primary-700 hover:text-primary-600"
                                href="{{ route('verification.notice') }}">Mot de passe oublié ?</a>
                        </div>
                        <button type="submit"
                            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg py-3 shadow-md">Connexion</button>
                    </form>
                </div>

                <div class="mt-6 text-xs text-gray-500 text-center">
                    En continuant, vous acceptez nos <a href="#"
                        class="underline hover:text-gray-700">conditions</a> et notre <a href="#"
                        class="underline hover:text-gray-700">politique de confidentialité</a>.
                </div>
            </div>
        </div>
    </div>
    <script>
        // Toastr configuration
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 5000
        };
        @if (session('success'))
            toastr.success('{{ session('success') }}');
        @endif
        @if (session('error'))
            toastr.error('{{ session('error') }}');
        @endif
        @if ($errors->any())
            toastr.error('{{ $errors->first() }}');
        @endif

        // Fonctionnalité masquer/démasquer mot de passe
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordField = document.getElementById('passwordField');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');

            togglePassword.addEventListener('click', function() {
                // Basculer le type d'input
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    eyeOpen.classList.add('hidden');
                    eyeClosed.classList.remove('hidden');
                    togglePassword.setAttribute('aria-label', 'Masquer le mot de passe');
                } else {
                    passwordField.type = 'password';
                    eyeOpen.classList.remove('hidden');
                    eyeClosed.classList.add('hidden');
                    togglePassword.setAttribute('aria-label', 'Afficher le mot de passe');
                }
            });
        });
    </script>
</body>

</html>
