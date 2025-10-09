<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password reset • Stock Manager</title>
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
                    <h1 class="font-heading text-2xl text-gray-900">Réinisialisation du mot de passe</h1>
                    <p class="text-gray-500">Entrer votre nouveau mot de passe</p>
                    
                    <!-- Timer d'expiration -->
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center justify-center text-yellow-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm">Ce lien expire dans : <strong id="countdown">60:00</strong></span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-md p-6 md:p-8">
                    <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token ?? '' }}" />
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
                        @if ($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded mb-4 text-sm">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                        <div class="text-sm text-gray-600 mb-2">
                            <span>Compte:</span>
                            <span class="font-medium text-gray-800">{{ $email ?? old('email') }}</span>
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
                                <input type="password" name="password" id="passwordField" required
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
                                    <!-- Icône œil barré (visible quand mot de passe affiché) -->
                                    <svg id="eyeClosed" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 11-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Indicateur de force du mot de passe -->
                            <div class="mt-2">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="text-gray-600">Force du mot de passe</span>
                                    <span id="strengthText" class="font-medium text-gray-400">Aucun</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div id="strengthBar" class="h-2 rounded-full transition-all duration-300 bg-gray-300" style="width: 0%"></div>
                                </div>
                                <div id="strengthTips" class="mt-2 text-xs text-gray-500 space-y-1 hidden">
                                    <div class="flex items-center gap-2">
                                        <span id="lengthCheck" class="text-red-500">✗</span>
                                        <span>Au moins 8 caractères</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span id="uppercaseCheck" class="text-red-500">✗</span>
                                        <span>Une lettre majuscule</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span id="lowercaseCheck" class="text-red-500">✗</span>
                                        <span>Une lettre minuscule</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span id="numberCheck" class="text-red-500">✗</span>
                                        <span>Un chiffre</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span id="specialCheck" class="text-red-500">✗</span>
                                        <span>Un caractère spécial (!@#$%^&*)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirmation du mot de
                                passe</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                <input type="password" name="password_confirmation" required
                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition-colors pl-10 pr-10 py-3 text-sm"
                                    placeholder="••••••••" />
                                <button type="button"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                    aria-label="Afficher le mot de passe">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg py-3 shadow-md">Réinitialiser
                            le mot de passe</button>
                    </form>
                </div>


            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('passwordField');
            const togglePassword = document.getElementById('togglePassword');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            const strengthTips = document.getElementById('strengthTips');

            // Éléments de vérification
            const lengthCheck = document.getElementById('lengthCheck');
            const uppercaseCheck = document.getElementById('uppercaseCheck');
            const lowercaseCheck = document.getElementById('lowercaseCheck');
            const numberCheck = document.getElementById('numberCheck');
            const specialCheck = document.getElementById('specialCheck');

            // Toggle password visibility
            togglePassword.addEventListener('click', function() {
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

            // Password strength checker
            passwordField.addEventListener('input', function() {
                const password = this.value;
                let score = 0;
                let checks = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /[0-9]/.test(password),
                    special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
                };

                // Afficher les conseils si l'utilisateur commence à taper
                if (password.length > 0) {
                    strengthTips.classList.remove('hidden');
                } else {
                    strengthTips.classList.add('hidden');
                    strengthBar.style.width = '0%';
                    strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-gray-300';
                    strengthText.textContent = 'Aucun';
                    strengthText.className = 'font-medium text-gray-400';
                    return;
                }

                // Mise à jour des checks visuels
                updateCheck(lengthCheck, checks.length);
                updateCheck(uppercaseCheck, checks.uppercase);
                updateCheck(lowercaseCheck, checks.lowercase);
                updateCheck(numberCheck, checks.number);
                updateCheck(specialCheck, checks.special);

                // Calcul du score
                Object.values(checks).forEach(check => {
                    if (check) score++;
                });

                // Bonus pour longueur
                if (password.length >= 12) score += 0.5;
                if (password.length >= 16) score += 0.5;

                // Mise à jour de la barre et du texte
                updateStrengthDisplay(score);
            });

            function updateCheck(element, isValid) {
                if (isValid) {
                    element.textContent = '✓';
                    element.className = 'text-green-500';
                } else {
                    element.textContent = '✗';
                    element.className = 'text-red-500';
                }
            }

            function updateStrengthDisplay(score) {
                let width, bgClass, textClass, text;

                if (score < 2) {
                    width = '20%';
                    bgClass = 'bg-red-500';
                    textClass = 'text-red-600';
                    text = 'Très faible';
                } else if (score < 3) {
                    width = '40%';
                    bgClass = 'bg-orange-500';
                    textClass = 'text-orange-600';
                    text = 'Faible';
                } else if (score < 4) {
                    width = '60%';
                    bgClass = 'bg-yellow-500';
                    textClass = 'text-yellow-600';
                    text = 'Moyen';
                } else if (score < 5) {
                    width = '80%';
                    bgClass = 'bg-blue-500';
                    textClass = 'text-blue-600';
                    text = 'Fort';
                } else {
                    width = '100%';
                    bgClass = 'bg-green-500';
                    textClass = 'text-green-600';
                    text = 'Très fort';
                }

                strengthBar.style.width = width;
                strengthBar.className = `h-2 rounded-full transition-all duration-300 ${bgClass}`;
                strengthText.textContent = text;
                strengthText.className = `font-medium ${textClass}`;
            }

            // Timer de compte à rebours (60 minutes = 3600 secondes)
            let timeLeft = 60 * 60; // 60 minutes en secondes
            const countdownElement = document.getElementById('countdown');

            function updateCountdown() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                
                countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                // Changer la couleur selon le temps restant
                const timerContainer = countdownElement.closest('.bg-yellow-50');
                if (timeLeft <= 300) { // 5 minutes
                    timerContainer.className = timerContainer.className.replace('bg-yellow-50 border-yellow-200', 'bg-red-50 border-red-200');
                    countdownElement.className = 'text-red-800 font-bold animate-pulse';
                } else if (timeLeft <= 900) { // 15 minutes
                    timerContainer.className = timerContainer.className.replace('bg-yellow-50 border-yellow-200', 'bg-orange-50 border-orange-200');
                    countdownElement.className = 'text-orange-800 font-bold';
                }
                
                if (timeLeft <= 0) {
                    // Lien expiré
                    countdownElement.textContent = 'EXPIRÉ';
                    countdownElement.className = 'text-red-800 font-bold animate-pulse';
                    
                    // Désactiver le formulaire
                    const form = document.querySelector('form');
                    const inputs = form.querySelectorAll('input, button');
                    inputs.forEach(input => {
                        input.disabled = true;
                        input.classList.add('opacity-50', 'cursor-not-allowed');
                    });
                    
                    // Afficher un message d'expiration
                    const expiredMessage = document.createElement('div');
                    expiredMessage.className = 'mt-4 p-4 bg-red-100 border border-red-300 rounded-lg text-red-800 text-center';
                    expiredMessage.innerHTML = `
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <strong>Lien expiré</strong>
                        </div>
                        <p>Ce lien de réinitialisation a expiré. Veuillez demander un nouveau lien.</p>
                        <a href="{{ route('verification.notice') }}" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Demander un nouveau lien
                        </a>
                    `;
                    form.parentNode.insertBefore(expiredMessage, form.nextSibling);
                    
                    clearInterval(countdownInterval);
                    return;
                }
                
                timeLeft--;
            }

            // Démarrer le timer
            const countdownInterval = setInterval(updateCountdown, 1000);
            updateCountdown(); // Affichage initial

            // Conseils de sécurité
            const securityTips = [
                "💡 Utilisez une combinaison de lettres, chiffres et symboles",
                "🔒 Évitez les informations personnelles (nom, date de naissance)",
                "📱 Considérez l'utilisation d'un gestionnaire de mots de passe",
                "🔄 Ne réutilisez pas ce mot de passe sur d'autres sites",
                "⚡ Plus long = plus sécurisé (minimum 12 caractères recommandé)"
            ];

            let currentTipIndex = 0;
            
            // Afficher les conseils de sécurité de manière rotative
            function showSecurityTip() {
                // Créer ou mettre à jour le conteneur de conseils
                let tipContainer = document.getElementById('security-tip');
                if (!tipContainer) {
                    tipContainer = document.createElement('div');
                    tipContainer.id = 'security-tip';
                    tipContainer.className = 'mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 text-sm transition-all duration-500';
                    
                    const form = document.querySelector('form');
                    form.parentNode.insertBefore(tipContainer, form);
                }
                
                tipContainer.innerHTML = `
                    <div class="flex items-start">
                        <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>${securityTips[currentTipIndex]}</span>
                    </div>
                `;
                
                currentTipIndex = (currentTipIndex + 1) % securityTips.length;
            }

            // Afficher le premier conseil immédiatement
            showSecurityTip();
            
            // Changer de conseil toutes les 8 secondes
            setInterval(showSecurityTip, 8000);
        });
    </script>
</body>

</html>
