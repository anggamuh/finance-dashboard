<?php
use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;
    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fredoka:500,600,700|nunito:400,600,700,800&display=swap" rel="stylesheet" />

    <style>
        .ss-page{
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:32px 20px;
            position:relative;
            overflow:hidden;
            background:linear-gradient(160deg,#FFF6EA 0%,#FFEFE2 45%,#FFE8E4 100%);
        }
        .ss-blob{
            position:absolute;
            border-radius:50%;
            filter:blur(2px);
            opacity:.55;
            pointer-events:none;
        }
        .ss-blob-1{width:280px;height:280px;background:#FFD9A0;top:-90px;left:-90px;}
        .ss-blob-2{width:220px;height:220px;background:#B7E8D6;bottom:-70px;right:-60px;}
        .ss-blob-3{width:140px;height:140px;background:#FFC9C0;top:60%;left:-60px;opacity:.4;}

        .ss-card{
            font-family:'Nunito',system-ui,sans-serif;
            background:#FFFFFF;
            border-radius:28px;
            padding:44px 36px 36px;
            box-shadow:0 24px 60px -12px rgba(43,39,48,.18), 0 8px 20px rgba(43,39,48,.06);
            border:1px solid rgba(43,39,48,.06);
            max-width:420px;
            width:100%;
            margin:0 auto;
            position:relative;
            z-index:1;
            animation:ss-rise .5s cubic-bezier(.22,1,.36,1);
        }
        @keyframes ss-rise{
            from{opacity:0;transform:translateY(14px);}
            to{opacity:1;transform:translateY(0);}
        }

        .ss-brand{display:flex;flex-direction:column;align-items:center;text-align:center;margin-bottom:30px;}
        .ss-brand-icon{
            width:64px;height:64px;border-radius:20px;
            background:linear-gradient(145deg,#FFEDE2,#FFE0D6);
            display:flex;align-items:center;justify-content:center;
            margin-bottom:16px;
            box-shadow:inset 0 0 0 1px rgba(255,120,99,.12);
        }
        .ss-brand-icon img{width:36px;height:36px;}
        .ss-brand h1{
            font-family:'Fredoka',system-ui,sans-serif;font-weight:700;
            font-size:1.45rem;color:#2B2730;margin:0;letter-spacing:-.01em;
        }
        .ss-brand p{font-size:.88rem;color:#8a8390;margin:6px 0 0;font-weight:700;}

        .ss-label{
            display:block;font-family:'Fredoka',system-ui,sans-serif;font-weight:600;
            font-size:.85rem;color:#2B2730;margin-bottom:7px;
        }
        .ss-field{margin-bottom:20px;}
        .ss-input-wrap{position:relative;}
        .ss-input-icon{
            position:absolute;left:15px;top:50%;transform:translateY(-50%);
            width:19px;height:19px;color:#B4AEB9;pointer-events:none;
        }
        .ss-input{
            width:100%;border:2px solid rgba(43,39,48,.10);border-radius:14px;
            padding:12px 16px 12px 44px;font-family:'Nunito',sans-serif;font-size:.95rem;color:#2B2730;
            background:#FFFAF3;transition:border-color .15s ease, box-shadow .15s ease, background .15s ease;
        }
        .ss-input::placeholder{color:#C4BEC7;}
        .ss-input:focus{
            outline:none;border-color:#FF7863;box-shadow:0 0 0 4px rgba(255,120,99,.12);
            background:#fff;
        }
        .ss-toggle-pass{
            position:absolute;right:14px;top:50%;transform:translateY(-50%);
            background:none;border:none;padding:4px;cursor:pointer;color:#B4AEB9;
            display:flex;align-items:center;justify-content:center;
        }
        .ss-toggle-pass:hover{color:#6b6572;}
        .ss-toggle-pass svg{width:19px;height:19px;}

        .ss-error{
            display:flex;align-items:center;gap:5px;
            color:#E85F4B;font-size:.8rem;font-weight:700;margin-top:7px;
        }

        .ss-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:26px;flex-wrap:wrap;gap:10px;}
        .ss-remember{display:flex;align-items:center;gap:8px;font-size:.88rem;color:#6b6572;font-weight:700;cursor:pointer;}
        .ss-remember input{width:18px;height:18px;border-radius:6px;accent-color:#FF7863;cursor:pointer;}
        .ss-forgot{font-size:.85rem;font-weight:800;color:#3FA6C7;text-decoration:none;}
        .ss-forgot:hover{text-decoration:underline;}

        .ss-submit{
            width:100%;display:flex;align-items:center;justify-content:center;gap:8px;
            font-family:'Fredoka',sans-serif;font-weight:600;font-size:1rem;
            background:#FF7863;color:#fff;border:none;border-radius:999px;
            padding:14px 24px;cursor:pointer;
            box-shadow:0 5px 0 #E85F4B;transition:transform .15s ease, box-shadow .15s ease, opacity .15s ease;
        }
        .ss-submit:hover{box-shadow:0 3px 0 #E85F4B;transform:translateY(2px);}
        .ss-submit:active{transform:translateY(3px);box-shadow:0 2px 0 #E85F4B;}
        .ss-submit:disabled{opacity:.75;cursor:not-allowed;}
        .ss-spinner{
            width:16px;height:16px;border-radius:50%;
            border:2.5px solid rgba(255,255,255,.5);border-top-color:#fff;
            animation:ss-spin .7s linear infinite;
        }
        @keyframes ss-spin{to{transform:rotate(360deg);}}

        .ss-back{
            display:flex;align-items:center;justify-content:center;gap:6px;
            margin-top:24px;font-size:.85rem;
            color:#8a8390;text-decoration:none;font-weight:700;
            transition:color .15s ease, gap .15s ease;
        }
        .ss-back:hover{color:#2B2730;gap:9px;}

        .ss-status{
            display:flex;align-items:center;justify-content:center;gap:8px;
            background:#7FCFAE;color:#fff;font-weight:700;font-size:.85rem;
            border-radius:12px;padding:11px 16px;margin-bottom:22px;text-align:center;
        }
    </style>

    <div class="ss-page">
        <div class="ss-blob ss-blob-1"></div>
        <div class="ss-blob ss-blob-2"></div>
        <div class="ss-blob ss-blob-3"></div>

        <div class="ss-card" x-data="{ showPassword: false }">
            <div class="ss-brand">
                <div class="ss-brand-icon">
                    <img src="{{ asset('images/logo-icon.png') }}" alt="Second Star">
                </div>
                <h1>Selamat datang kembali</h1>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="ss-status">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit="login">
                <!-- Email Address -->
                <div class="ss-field">
                    <label for="email" class="ss-label">{{ __('Email') }}</label>
                    <div class="ss-input-wrap">
                        <svg class="ss-input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="3"/><path d="m3 6 9 6 9-6"/>
                        </svg>
                        <input wire:model="form.email" id="email" class="ss-input" type="email" name="email" required autofocus autocomplete="username" placeholder="nama@email.com">
                    </div>
                    @error('form.email')
                        <div class="ss-error">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="ss-field">
                    <label for="password" class="ss-label">{{ __('Password') }}</label>
                    <div class="ss-input-wrap">
                        <svg class="ss-input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input
                            wire:model="form.password"
                            id="password"
                            class="ss-input"
                            style="padding-right:44px"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                        >
                        <button type="button" class="ss-toggle-pass" @click="showPassword = !showPassword" tabindex="-1">
                            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/><path d="M6.61 6.61A18.5 18.5 0 0 0 1 12s4 8 11 8a10.94 10.94 0 0 0 5.39-1.39"/></svg>
                        </button>
                    </div>
                    @error('form.password')
                        <div class="ss-error">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me + Forgot Password -->
                <div class="ss-row">
                    <label class="ss-remember">
                        <input wire:model="form.remember" id="remember" type="checkbox" name="remember">
                        {{ __('Ingat saya') }}
                    </label>

                    @if (Route::has('password.request'))
                        <a class="ss-forgot" href="{{ route('password.request') }}" wire:navigate>
                            {{ __('Lupa kata sandi?') }}
                        </a>
                    @endif
                </div>

                <button type="submit" class="ss-submit" wire:loading.attr="disabled" wire:target="login">
                    <span wire:loading.remove wire:target="login">{{ __('Masuk') }}</span>
                    <span wire:loading wire:target="login" style="display:flex;align-items:center;gap:8px;">
                    </span>
                </button>
            </form>

            <a href="{{ url('/') }}" class="ss-back" wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                Kembali ke Second Star
            </a>
        </div>
    </div>
</div>