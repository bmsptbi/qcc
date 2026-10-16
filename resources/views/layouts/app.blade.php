<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'QCC Assessment') - Konvensi Mutu & Kaizen</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN fallback + modern styling) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d',
                        },
                        corporate: {
                            50: '#f0f7ff',
                            500: '#0055b8',
                            600: '#004399',
                            900: '#0b2545',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        /* Custom touch friendly slider styling */
        input[type="range"] {
            accent-color: #0055b8;
        }
        .touch-action-manipulation {
            touch-action: manipulation;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('qcc.index') }}" class="flex items-center space-x-2.5">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 via-indigo-600 to-sky-400 flex items-center justify-center text-white shadow-md shadow-blue-500/20 font-extrabold text-lg tracking-tight">
                            QC
                        </div>
                        <div>
                            <div class="font-extrabold text-slate-900 tracking-tight text-base sm:text-lg leading-tight flex items-center gap-1.5">
                                QCC Assessment
                                <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-blue-100 text-blue-700">2026</span>
                            </div>
                            <p class="text-[11px] text-slate-500 hidden sm:block">Sistem Penilaian Gugus Kendali Mutu & Kaizen</p>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('qcc.index') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('qcc.index') ? 'text-blue-700 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Dashboard Gugus
                    </a>
                    <a href="{{ route('qcc.leaderboard') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('qcc.leaderboard') ? 'text-blue-700 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Leaderboard & Hasil
                    </a>
                    <a href="{{ route('admin.judges.index') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.judges.*') ? 'text-indigo-700 bg-indigo-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Kelola Juri
                    </a>
                    <a href="{{ route('admin.circles.create') }}" class="ml-2 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 transition border border-blue-200">
                        + Circle Baru
                    </a>
                </nav>

                <!-- Active Judge Switcher / Indicator -->
                <div class="flex items-center space-x-2">
                    @php
                        $currentJudgeId = session('active_judge_id', 2);
                        $currentJudge = \App\Models\User::find($currentJudgeId) ?? \App\Models\User::where('role', 'judge')->first();
                        $allJudges = \App\Models\User::whereIn('role', ['judge', 'admin'])->get();
                    @endphp

                    <!-- Judge Switch Dropdown Form -->
                    <form action="{{ route('qcc.switch-judge') }}" method="POST" class="flex items-center">
                        @csrf
                        <div class="relative inline-flex items-center bg-slate-100/90 rounded-xl p-1 border border-slate-200/80">
                            <span class="pl-2 pr-1 text-slate-500 text-xs font-medium hidden sm:inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                Juri:
                            </span>
                            <select name="judge_id" onchange="this.form.submit()" class="bg-transparent text-xs sm:text-sm font-semibold text-slate-800 pr-7 py-1 pl-1 focus:outline-none cursor-pointer">
                                @foreach($allJudges as $j)
                                    <option value="{{ $j->id }}" {{ $currentJudge?->id == $j->id ? 'selected' : '' }}>
                                        {{ Str::limit($j->name, 22) }} ({{ ucfirst($j->role) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-lg leading-none">&times;</button>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 flex items-center space-x-2 text-sm">
                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Content Area -->
    <main class="flex-1 pb-24 md:pb-12">
        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-30 bg-white/95 backdrop-blur-md border-t border-slate-200 px-4 py-2 flex justify-around items-center shadow-lg">
        <a href="{{ route('qcc.index') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('qcc.index') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-[10px] mt-0.5">Beranda</span>
        </a>

        <a href="{{ route('admin.circles.create') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('admin.circles.*') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <div class="w-6 h-6 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                +
            </div>
            <span class="text-[10px] mt-0.5 font-bold text-blue-700">Gugus</span>
        </a>

        <a href="{{ route('admin.judges.index') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('admin.judges.*') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span class="text-[10px] mt-0.5">Juri</span>
        </a>

        <a href="{{ route('qcc.leaderboard') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('qcc.leaderboard') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span class="text-[10px] mt-0.5">Ranking</span>
        </a>
    </nav>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500 hidden md:block">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; {{ date('Y') }} PT Bekaert Indonesia - QCC Assessment & Kaizen Convention System.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
