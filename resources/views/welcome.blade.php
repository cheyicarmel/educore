<!DOCTYPE html>

<html lang="fr">
    <head>
        <meta charset="utf-8"/>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <title>EduCore - Système de Gestion Scolaire Intelligent | Connexion</title>
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
        <script id="tailwind-config">
            tailwind.config = {
                darkMode: "class",
                theme: {
                    extend: {
                        colors: {
                            "primary": "#2b6cee",
                            "background-light": "#f6f6f8",
                            "background-dark": "#101622",
                        },
                        fontFamily: {
                            "display": ["Lexend", "sans-serif"]
                        },
                        borderRadius: {
                            "DEFAULT": "0.25rem",
                            "lg": "0.5rem",
                            "xl": "0.75rem",
                            "full": "9999px"
                        },
                    },
                },
            }
        </script>
        <style>
            body {
                font-family: 'Lexend', sans-serif;
            }
        </style>
    </head>


    <body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 antialiased h-screen overflow-hidden">
        <div class="flex h-full w-full">
            <!-- Partie de gauche -->
            <div class="hidden lg:flex lg:w-3/5 xl:w-2/3 relative items-center justify-center overflow-hidden">

                <div class="absolute inset-0 z-0">
                    <div class="w-full h-full bg-cover bg-center" data-alt="Modern library interior with high ceilings and bookshelves" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAKOPS5W8a-wLGW7yktjjwI3vTNwfuwjcHyNdp1eXp_ZjmSIUZayWkOZjBD1ARC1t9I1S7mvpNcifsFJPIXVJ4exzbUbG2FLZkir7L_m_cPCl-38HbJQO2eWmpkD7ncTCDiORScMLvVPQVfALFa8q_35MIn3h-vIbHUtWzaNuLaqN25aWle4JX3gW7G3pS23UNuLdyMcUn7h8Vvfw8Ko6ddPIbjttyJzt0InkA6HvxiAeUyQEF0pIsDLyxA8UonsSgJdW0WBc5QqUdM');">
                    
                    </div>

                    <div class="absolute inset-0 bg-primary/40 mix-blend-multiply">

                    </div>

                    <div class="absolute inset-0 bg-gradient-to-t from-background-dark/80 via-transparent to-transparent">
                    
                    </div>
                </div>

               <div class="relative z-10 px-12 xl:px-24 text-white">

                    <div class="flex items-center gap-3 mb-8">
                        <div class="bg-white p-2 rounded-xl text-primary">
                            <span class="material-symbols-outlined text-primary text-2xl">school</span>
                        </div>
                        <span class="text-3xl font-bold tracking-tight">EduCore</span>
                    </div>

                    <h1 class="text-5xl xl:text-6xl font-black mb-6 leading-tight">
                        La gestion scolaire,<br/>simplifiée et centralisée.
                    </h1>

                    <p class="text-xl text-slate-100/90 max-w-lg font-light leading-relaxed">
                        EduCore centralise notes, moyennes, paiements et bulletins en un seul endroit — pour les élèves, les enseignants et l'administration.
                    </p>

                    <div class="mt-12 flex flex-col gap-4">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-white/80">calculate</span>
                            <span class="text-sm font-medium text-white/90">Calcul automatique des moyennes</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-white/80">payments</span>
                            <span class="text-sm font-medium text-white/90">Suivi financier en temps réel</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-white/80">description</span>
                            <span class="text-sm font-medium text-white/90">Génération de bulletins PDF</span>
                        </div>
                    </div>

                </div>
            </div>


            <!-- Partie de droite - Avec formulaire de connexion -->
            <div class="w-full lg:w-2/5 xl:w-1/3 flex flex-col justify-center bg-white dark:bg-background-dark px-6 sm:px-10 md:px-16 xl:px-20 py-8 sm:py-12 shadow-2xl relative z-20 overflow-y-auto">
                <div class="max-w-md w-full mx-auto">

                    <header class="mb-8 text-center lg:text-left">
                        <div class="lg:hidden flex justify-center mb-6">
                            <div class="flex items-center gap-2 text-primary">
                                <span class="material-symbols-outlined text-primary text-2xl">school</span>
                                <span class="text-2xl font-bold tracking-tight">EduCore</span>
                            </div>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 mb-2">Bon retour parmi nous</h2>
                        <p class="text-slate-500 dark:text-slate-400 font-normal text-sm sm:text-base">Accédez à votre espace sécurisé EduCore</p>
                    </header>

                    <form class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2" for="email">Adresse e-mail</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-slate-400 text-xl">mail</span>
                                </div>
                                <input class="block w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-sm" id="email" name="email" placeholder="exemple@ecole.com" type="email"/>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2" for="password">Mot de passe</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-slate-400 text-xl">lock</span>
                                </div>
                                <input class="block w-full pl-11 pr-12 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-sm" id="password" name="password" placeholder="••••••••" type="password"/>
                                <button class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-primary" type="button" id="toggle-password">
                                    <span class="material-symbols-outlined text-xl" id="toggle-icon">visibility</span>
                                </button>
                            </div>
                        </div>

                        <button class="w-full flex justify-center py-3.5 px-6 border border-transparent rounded-lg shadow-sm text-sm sm:text-base font-bold text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200" type="submit">
                            Se connecter
                        </button>
                    </form>

                    <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <p class="text-center text-xs text-slate-400 dark:text-slate-500 uppercase tracking-widest font-bold mb-4">Accès Centralisé</p>
                        <div class="grid grid-cols-4 gap-2 sm:gap-4 text-center">
                            <div class="flex flex-col items-center gap-1 group cursor-default">
                                <div class="size-9 sm:size-10 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-xl">admin_panel_settings</span>
                                </div>
                                <span class="text-[9px] sm:text-[10px] font-semibold text-slate-500">Admin</span>
                            </div>
                            <div class="flex flex-col items-center gap-1 group cursor-default">
                                <div class="size-9 sm:size-10 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-xl">school</span>
                                </div>
                                <span class="text-[9px] sm:text-[10px] font-semibold text-slate-500">Enseignant</span>
                            </div>
                            <div class="flex flex-col items-center gap-1 group cursor-default">
                                <div class="size-9 sm:size-10 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-xl">person</span>
                                </div>
                                <span class="text-[9px] sm:text-[10px] font-semibold text-slate-500">Élève</span>
                            </div>
                            <div class="flex flex-col items-center gap-1 group cursor-default">
                                <div class="size-9 sm:size-10 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-xl">payments</span>
                                </div>
                                <span class="text-[9px] sm:text-[10px] font-semibold text-slate-500">Comptable</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const togglePassword = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggle-icon');

            togglePassword.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                toggleIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
            });
        </script>
    </body>
</html>