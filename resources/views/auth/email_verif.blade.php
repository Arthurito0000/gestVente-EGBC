<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verif email • Stock Manager</title>
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
                    <h1 class="font-heading text-2xl text-gray-900">Verification email</h1>
                    <p class="text-gray-500">Entrer votre addresse mail</p>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-md p-6 md:p-8">
                    <form action="{{ route('verification.send') }}" method="POST" class="space-y-6">
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


                        <button type="submit"
                            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg py-3 shadow-md">Verifier
                            email</button>
                    </form>
                </div>


            </div>
        </div>
    </div>
</body>

</html>
