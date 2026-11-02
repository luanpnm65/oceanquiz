<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OceanQuiz - Lặn sâu khám phá tri thức đại dương</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/landing.js'])
    <style>
        /* Bọt khí nổi lên bằng CSS Animation (Mục 6.1) */
        @keyframes bubbleRise {
            0% {
                transform: translateY(100vh) scale(0.5);
                opacity: 0;
            }
            20% {
                opacity: 0.6;
            }
            80% {
                opacity: 0.5;
            }
            100% {
                transform: translateY(-20vh) scale(1.2);
                opacity: 0;
            }
        }
        .ocean-bubble {
            position: fixed;
            bottom: -50px;
            border-radius: 9999px;
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.8), rgba(56, 189, 248, 0.15));
            border: 1px solid rgba(255, 255, 255, 0.35);
            pointer-events: none;
            z-index: 10;
            animation: bubbleRise linear infinite;
        }
        /* Tắt animation khi người dùng bật prefers-reduced-motion */
        @media (prefers-reduced-motion: reduce) {
            .ocean-bubble {
                animation: none !important;
                display: none !important;
            }
            .parallax-creature {
                transform: none !important;
            }
        }
    </style>
</head>
<body class="text-white antialiased selection:bg-cyan-400 selection:text-slate-950 overflow-x-hidden">

    {{-- Nền đại dương chuyển màu động theo độ sâu từ 0m -> 4000m (cao 5 màn hình = 500vh) --}}
    <div id="ocean-canvas"
         class="relative min-h-[500vh] transition-colors duration-150"
         style="background: linear-gradient(180deg, #38bdf8 0%, #0284c7 18%, #0f172a 48%, #020617 78%, #01030a 100%);">

        {{-- Bọt khí CSS --}}
        <div id="bubbles-container" aria-hidden="true">
            <span class="ocean-bubble w-3 h-3 left-[10%]" style="animation-duration: 9s; animation-delay: 0s;"></span>
            <span class="ocean-bubble w-5 h-5 left-[22%]" style="animation-duration: 13s; animation-delay: 2s;"></span>
            <span class="ocean-bubble w-2.5 h-2.5 left-[35%]" style="animation-duration: 8s; animation-delay: 4s;"></span>
            <span class="ocean-bubble w-6 h-6 left-[50%]" style="animation-duration: 15s; animation-delay: 1s;"></span>
            <span class="ocean-bubble w-4 h-4 left-[68%]" style="animation-duration: 11s; animation-delay: 3s;"></span>
            <span class="ocean-bubble w-3.5 h-3.5 left-[82%]" style="animation-duration: 10s; animation-delay: 5s;"></span>
            <span class="ocean-bubble w-5 h-5 left-[90%]" style="animation-duration: 14s; animation-delay: 2.5s;"></span>
        </div>

        {{-- Thanh Header nổi trên cùng --}}
        <header class="fixed top-0 left-0 right-0 z-40 backdrop-blur-md bg-slate-950/35 border-b border-white/10">
            <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
                <a href="{{ route('landing') }}" class="flex items-center gap-3 font-extrabold text-xl tracking-tight">
                    <span class="w-9 h-9 rounded-xl bg-cyan-500/20 border border-cyan-300/40 flex items-center justify-center text-cyan-300">
                        🌊
                    </span>
                    <span>Ocean<span class="text-cyan-300">Quiz</span></span>
                </a>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('home') }}"
                           class="px-4 py-2 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-semibold text-sm transition">
                            Vào khoang tàu ({{ auth()->user()->name }})
                        </a>
                    @else
                        <a href="#seafloor-auth"
                           class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-sm font-medium transition">
                            Lặn xuống đáy ngay
                        </a>
                        <a href="{{ route('login') }}"
                           class="px-4 py-2 rounded-lg bg-cyan-400 hover:bg-cyan-300 text-slate-950 font-bold text-sm transition">
                            Đăng nhập
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        {{-- Thước đo độ sâu bên phải (0m -> 4.000m) --}}
        <aside class="fixed right-6 top-1/2 -translate-y-1/2 z-40 hidden md:flex flex-col items-end gap-2 select-none">
            <div class="bg-slate-950/80 backdrop-blur-md border border-cyan-500/30 rounded-2xl px-4 py-3 text-right shadow-2xl min-w-[165px]">
                <div class="text-[11px] uppercase tracking-wider text-cyan-300/80 font-semibold">Độ sâu hiện tại</div>
                <div class="text-2xl font-black font-mono text-white mt-0.5">
                    <span id="depth-value">0</span> <span class="text-sm font-normal text-cyan-300">m</span>
                </div>
                <div id="depth-zone-badge" class="mt-1 inline-block px-2 py-0.5 rounded text-xs font-semibold bg-sky-500/20 text-sky-200 border border-sky-400/30">
                    ☀️ Vùng nắng (0–200m)
                </div>
            </div>

            {{-- Thanh thước dọc --}}
            <div class="relative h-72 w-3 bg-slate-900/80 rounded-full border border-white/15 overflow-hidden mr-4">
                <div id="depth-progress-bar" class="w-full bg-gradient-to-b from-cyan-300 via-blue-500 to-indigo-900 rounded-full transition-all duration-75" style="height: 0%;"></div>
            </div>

            <div class="text-[11px] font-mono text-slate-300 space-y-1 mr-2 text-right">
                <div>0 m — Mặt biển</div>
                <div>200 m — Chạng vạng</div>
                <div>1.000 m — Nửa đêm</div>
                <div>4.000 m — Đáy sâu</div>
            </div>
        </aside>

        {{-- MỐC 1: MẶT BIỂN & VÙNG NẮNG (0 - 200 m, đàn cá nhỏ) --}}
        <section class="relative min-h-screen flex flex-col justify-center items-center px-6 pt-20 text-center">
            <div class="max-w-3xl mx-auto z-20">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/15 backdrop-blur border border-white/25 text-xs font-semibold uppercase tracking-widest text-white mb-6">
                    ☀️ Vùng Nắng (Epipelagic Zone · 0 – 200m)
                </span>
                <h1 class="text-4xl md:text-6xl font-black tracking-tight leading-tight drop-shadow-lg">
                    Lặn Sâu Vào Đại Dương <br>
                    <span class="text-cyan-200">Chinh Phục Quiz Sinh Vật Biển</span>
                </h1>
                <p class="mt-6 text-lg md:text-xl text-sky-100 max-w-2xl mx-auto leading-relaxed">
                    Đọc tài liệu khoa học từ Wikipedia tiếng Việt, tô sáng kiến thức quan trọng và thử thách tốc độ phản xạ cùng bộ câu hỏi trắc nghiệm sinh bởi AI Gemini.
                </p>

                <div class="mt-10 flex flex-wrap justify-center gap-4">
                    <a href="#seafloor-auth" class="px-7 py-3.5 rounded-xl bg-white text-slate-950 font-bold text-base shadow-xl hover:bg-cyan-100 transition">
                        Cuộn xuống để lặn sâu ↓
                    </a>
                </div>
            </div>

            {{-- SVG Đàn cá nhỏ di chuyển Parallax --}}
            <div class="parallax-creature absolute left-[8%] top-[68%] opacity-90 pointer-events-none" data-speed="0.22">
                <svg width="260" height="110" viewBox="0 0 260 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g fill="#E0F2FE" fill-opacity="0.85">
                        <path d="M45 30C60 18 82 18 98 30C82 42 60 42 45 30Z"/>
                        <polygon points="46,30 28,18 32,30 28,42"/>
                        <circle cx="84" cy="28" r="2.5" fill="#0369A1"/>
                        <path d="M125 55C145 40 172 40 192 55C172 70 145 70 125 55Z"/>
                        <polygon points="126,55 104,41 110,55 104,69"/>
                        <circle cx="176" cy="52" r="3" fill="#0369A1"/>
                        <path d="M75 82C90 72 110 72 124 82C110 92 90 92 75 82Z"/>
                        <polygon points="76,82 60,72 64,82 60,92"/>
                        <circle cx="112" cy="80" r="2" fill="#0369A1"/>
                    </g>
                </svg>
                <p class="text-xs text-sky-100/80 font-medium mt-1">🐟 Đàn cá nhỏ tầng mặt (0 – 200m)</p>
            </div>
        </section>

        {{-- MỐC 2: VÙNG CHẠNG VẠNG (200 - 1.000 m, sứa phát sáng) --}}
        <section class="relative min-h-[150vh] flex items-center justify-center px-6">
            <div class="max-w-5xl w-full grid md:grid-cols-2 gap-12 items-center z-20">
                <div class="bg-slate-900/60 backdrop-blur-md border border-cyan-400/20 rounded-3xl p-8 shadow-2xl">
                    <span class="inline-block px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 text-xs font-bold uppercase tracking-wider mb-4">
                        🪼 Vùng Chạng Vạng (Mesopelagic · 200 – 1.000m)
                    </span>
                    <h2 class="text-3xl font-extrabold text-white">Đọc Thông Minh & Highlight Trực Tiếp</h2>
                    <p class="mt-4 text-slate-300 leading-relaxed">
                        Ở độ sâu 200m, ánh sáng mặt trời bắt đầu yếu dần. Giống như những loài sứa phát quang sinh học, bạn có thể bôi đen và tô sáng 4 màu trực tiếp lên bài đọc Wikipedia để ghi nhớ số liệu trước khi đồng hồ đếm ngược kết thúc.
                    </p>
                    <ul class="mt-6 space-y-2.5 text-sm text-cyan-200">
                        <li>✓ Đồng hồ đếm ngược đồng bộ thời gian thực với Server</li>
                        <li>✓ Lưu vị trí ký tự Highlight vào Database cá nhân</li>
                        <li>✓ Tự động mở màn chơi Quiz ngay khi hết giờ đọc</li>
                    </ul>
                </div>

                {{-- SVG Sứa phát sáng (Parallax) --}}
                <div class="parallax-creature flex flex-col items-center justify-center" data-speed="0.35">
                    <svg width="240" height="280" viewBox="0 0 240 280" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-[0_0_25px_rgba(34,211,238,0.6)]">
                        <defs>
                            <radialGradient id="jellyGlow" cx="50%" cy="40%" r="55%">
                                <stop offset="0%" stop-color="#A5F3FC" stop-opacity="0.95"/>
                                <stop offset="60%" stop-color="#06B6D4" stop-opacity="0.65"/>
                                <stop offset="100%" stop-color="#0E7490" stop-opacity="0.15"/>
                            </radialGradient>
                        </defs>
                        <path d="M40 110C40 55 75 25 120 25C165 25 200 55 200 110C180 122 160 105 140 115C120 125 100 105 80 115C60 122 50 115 40 110Z" fill="url(#jellyGlow)" stroke="#67E8F9" stroke-width="2"/>
                        <path d="M75 118C65 165 85 210 70 255" stroke="#67E8F9" stroke-width="3" stroke-linecap="round"/>
                        <path d="M100 120C95 170 115 215 102 265" stroke="#A5F3FC" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M125 120C135 170 115 220 130 268" stroke="#67E8F9" stroke-width="3" stroke-linecap="round"/>
                        <path d="M150 118C165 165 145 215 162 258" stroke="#22D3EE" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M170 114C185 160 170 205 182 245" stroke="#67E8F9" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span class="text-xs text-cyan-300/90 font-medium mt-2">🪼 Sứa phát quang vùng chạng vạng (200 – 1.000m)</span>
                </div>
            </div>
        </section>

        {{-- MỐC 3: VÙNG NỬA ĐÊM (1.000 - 4.000 m, cá đèn lồng) --}}
        <section class="relative min-h-[150vh] flex items-center justify-center px-6">
            <div class="max-w-5xl w-full grid md:grid-cols-2 gap-12 items-center z-20">
                {{-- SVG Cá đèn lồng (Anglerfish) Parallax --}}
                <div class="parallax-creature flex flex-col items-center justify-center order-2 md:order-1" data-speed="0.45">
                    <svg width="290" height="220" viewBox="0 0 290 220" fill="none" xmlns="http://www.w3.org/2000/svg">
                        {{-- Cần câu phát sáng --}}
                        <path d="M135 72C115 25 65 20 48 55" stroke="#38BDF8" stroke-width="3" stroke-linecap="round"/>
                        <circle cx="45" cy="60" r="12" fill="#FDE047" class=" drop-shadow-[0_0_20px_rgba(253,224,71,0.95)]"/>
                        <circle cx="45" cy="60" r="6" fill="#FEF08A"/>
                        {{-- Thân cá đèn lồng --}}
                        <path d="M75 125C75 80 125 58 180 68C225 76 250 105 268 90L262 150C245 135 220 165 175 172C120 180 80 160 75 125Z" fill="#0F172A" stroke="#38BDF8" stroke-width="2.5"/>
                        {{-- Mắt & Răng nhọn --}}
                        <circle cx="115" cy="100" r="6" fill="#F8FAFC"/>
                        <circle cx="113" cy="100" r="2.5" fill="#020617"/>
                        <polygon points="82,125 90,115 95,127" fill="#E2E8F0"/>
                        <polygon points="96,128 104,116 110,130" fill="#E2E8F0"/>
                        <polygon points="85,140 94,152 100,138" fill="#E2E8F0"/>
                        <polygon points="102,142 110,154 116,140" fill="#E2E8F0"/>
                    </svg>
                    <span class="text-xs text-amber-300/90 font-medium mt-2">🏮 Cá đèn lồng vùng nửa đêm (1.000 – 4.000m)</span>
                </div>

                <div class="bg-slate-950/80 backdrop-blur-md border border-indigo-500/30 rounded-3xl p-8 shadow-2xl order-1 md:order-2">
                    <span class="inline-block px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold uppercase tracking-wider mb-4">
                        🌑 Vùng Nửa Đêm (Bathypelagic · 1.000 – 4.000m)
                    </span>
                    <h2 class="text-3xl font-extrabold text-white">Quiz Tốc Độ Kiểu Kahoot & Chống Gian Lận</h2>
                    <p class="mt-4 text-slate-300 leading-relaxed">
                        Trong bóng tối hoàn toàn ở độ sâu 4.000m, mỗi quyết định đều tính bằng mili-giây. Trả lời đúng càng nhanh, điểm số càng tiệm cận 1.000 điểm mỗi câu!
                    </p>
                    <div class="mt-6 p-4 rounded-2xl bg-slate-900 border border-slate-800 font-mono text-xs text-cyan-300">
                        Điểm = round(1000 × (1 - t / 2T))
                    </div>
                </div>
            </div>
        </section>

        {{-- ĐÁY TRANG (4.000m): NỀN CÁT & KHUNG ĐĂNG KÝ / ĐĂNG NHẬP (Mục 6.1) --}}
        <section id="seafloor-auth" class="relative min-h-screen flex flex-col justify-end pt-24">
            <div class="max-w-4xl mx-auto w-full px-6 pb-20 z-20">
                <div class="bg-slate-900/90 backdrop-blur-xl border border-amber-500/30 rounded-3xl p-8 md:p-12 shadow-2xl text-center">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold uppercase tracking-wider">
                        ⚓ Đã chạm đáy đại dương — 4.000m
                    </span>
                    <h2 class="text-3xl md:text-4xl font-black text-white mt-4">
                        Sẵn Sàng Bắt Đầu Chuyến Thám Hiểm?
                    </h2>
                    <p class="text-slate-300 mt-3 max-w-xl mx-auto">
                        Đăng nhập hoặc tạo tài khoản thợ lặn mới để lưu lịch sử làm bài, đánh dấu highlight và ghi danh lên bảng xếp hạng OceanQuiz.
                    </p>

                    @auth
                        <div class="mt-8 flex flex-wrap justify-center gap-4">
                            <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('home') }}"
                               class="px-8 py-4 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-slate-950 font-extrabold text-base transition shadow-lg shadow-cyan-500/25">
                                Vào Trang Chủ Ngay →
                            </a>
                        </div>
                    @else
                        <div class="mt-8 grid sm:grid-cols-2 gap-4 max-w-lg mx-auto">
                            <a href="{{ route('login') }}"
                               class="flex items-center justify-center gap-2 px-6 py-4 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-slate-950 font-extrabold text-base transition shadow-lg shadow-cyan-500/25">
                                🔑 Đăng nhập
                            </a>
                            <a href="{{ route('register') }}"
                               class="flex items-center justify-center gap-2 px-6 py-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white border border-slate-600 font-bold text-base transition">
                                🤿 Đăng ký tài khoản
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

            {{-- Lớp nền cát đáy biển --}}
            <div class="w-full relative z-10">
                <svg viewBox="0 0 1440 140" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-24 md:h-32 preserve-3d">
                    <path d="M0 60C240 15 480 95 720 60C960 25 1200 85 1440 45V140H0V60Z" fill="#78350F" fill-opacity="0.65"/>
                    <path d="M0 85C320 50 640 115 960 80C1180 55 1320 90 1440 75V140H0V85Z" fill="#92400E" fill-opacity="0.85"/>
                </svg>
                <div class="bg-[#78350F] py-4 text-center text-xs text-amber-200/80 border-t border-amber-700/40">
                    OceanQuiz · Đồ án Laravel 12 + Wikipedia API + Google Gemini AI
                </div>
            </div>
        </section>

    </div>

</body>
</html>

