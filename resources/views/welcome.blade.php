<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Second Star — Baju Anak Distro Original.</title>
        <meta name="description" content="Second Star Official Shop, brand lokal Bandung. Kaos, hoodie, dan bundling outfit anak laki-laki &amp; perempuan usia 1-12 tahun, bahan cotton carded.">

        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Second Star">
        <meta property="og:title" content="Second Star — Baju Anak Distro Original">
        <meta property="og:description" content="Brand lokal Bandung. Kaos, hoodie, dan bundling outfit anak laki-laki & perempuan usia 1-12 tahun, bahan cotton carded.">
        <meta property="og:image" content="{{ asset('images/logo-full.png') }}">
        <meta property="og:url" content="{{ url('/') }}">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="Second Star — Baju Anak Distro Original">
        <meta name="twitter:description" content="Brand lokal Bandung. Kaos, hoodie, dan bundling outfit anak laki-laki & perempuan usia 1-12 tahun.">
        <meta name="twitter:image" content="{{ asset('images/logo-full.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fredoka:500,600,700|nunito:400,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="icon" href="{{ asset('images/favicon.ico') }}" sizes="any">
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <style>
            :root{
                --cream:#FFF9F0;
                --paper:#FFFFFF;
                --ink:#2B2730;
                --ink-soft:#6b6572;
                --sunshine:#FFC94D;
                --sunshine-deep:#F2A900;
                --coral:#FF7863;
                --coral-deep:#E85F4B;
                --sky:#6EC3E0;
                --sky-deep:#3FA6C7;
                --mint:#7FCFAE;
                --mint-deep:#4FAF87;
                --radius:22px;
            }
            *{box-sizing:border-box;}
            body{
                margin:0;
                font-family:'Nunito',system-ui,sans-serif;
                background:var(--cream);
                color:var(--ink);
                -webkit-font-smoothing:antialiased;
            }
            h1,h2,h3,.display{
                font-family:'Fredoka',system-ui,sans-serif;
                margin:0;
                line-height:1.1;
            }
            a{color:inherit;}
            img,svg{display:block;max-width:100%;}
            .wrap{max-width:1180px;margin:0 auto;padding:0 24px;}

            /* ---------- pinking-shear zigzag divider ---------- */
            .zigzag{
                width:100%;
                height:26px;
                background-size:32px 26px;
                background-repeat:repeat-x;
            }
            .zigzag-cream-to-paper{
                background-color:var(--paper);
                background-image:linear-gradient(135deg, var(--cream) 50%, transparent 50%),
                                  linear-gradient(-135deg, var(--cream) 50%, transparent 50%);
                background-position:0 0,16px 0;
            }
            .zigzag-paper-to-cream{
                background-color:var(--cream);
                background-image:linear-gradient(135deg, var(--paper) 50%, transparent 50%),
                                  linear-gradient(-135deg, var(--paper) 50%, transparent 50%);
                background-position:0 0,16px 0;
            }
            .zigzag-ink-to-cream{
                background-color:var(--cream);
                background-image:linear-gradient(135deg, var(--ink) 50%, transparent 50%),
                                  linear-gradient(-135deg, var(--ink) 50%, transparent 50%);
                background-position:0 0,16px 0;
            }

            /* ---------- stats / social proof bar ---------- */
            .stats-bar{background:var(--ink);padding:28px 0;}
            .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;text-align:center;}
            .stats-grid .stat b{
                display:block;font-family:'Fredoka',sans-serif;font-weight:700;
                font-size:1.5rem;color:var(--sunshine);
            }
            .stats-grid .stat span{font-size:.78rem;color:rgba(255,255,255,.7);font-weight:700;}

            /* ---------- cara belanja ---------- */
            .steps{padding:64px 0;background:var(--cream);}
            .steps-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;counter-reset:step;}
            .step-card{
                background:var(--paper);border-radius:var(--radius);padding:28px 24px;
                border:1px solid rgba(43,39,48,.06);position:relative;
                transition:transform .15s ease, box-shadow .15s ease;
            }
            .step-card:hover{transform:translateY(-4px);box-shadow:0 14px 30px rgba(43,39,48,.1);}
            .step-num{
                width:38px;height:38px;border-radius:50%;background:var(--coral);color:#fff;
                display:flex;align-items:center;justify-content:center;font-family:'Fredoka',sans-serif;
                font-weight:700;margin-bottom:16px;
            }
            .step-card h3{font-size:1.05rem;margin-bottom:8px;}
            .step-card p{font-size:.9rem;color:var(--ink-soft);margin:0;line-height:1.5;}

            /* ---------- FAQ ---------- */
            .faq{padding:20px 0 70px;background:var(--cream);}
            .faq-list{max-width:720px;margin:0 auto;display:flex;flex-direction:column;gap:12px;}
            .faq-item{background:var(--paper);border-radius:16px;border:1px solid rgba(43,39,48,.08);overflow:hidden;}
            .faq-item summary{
                padding:18px 22px;cursor:pointer;font-family:'Fredoka',sans-serif;font-weight:600;
                font-size:.98rem;list-style:none;display:flex;align-items:center;justify-content:space-between;gap:12px;
            }
            .faq-item summary::-webkit-details-marker{display:none;}
            .faq-item summary .chev{transition:transform .2s ease;color:var(--coral);flex-shrink:0;}
            .faq-item[open] summary .chev{transform:rotate(180deg);}
            .faq-item .faq-body{padding:0 22px 20px;font-size:.9rem;color:var(--ink-soft);line-height:1.6;}

            /* ---------- floating contact button ---------- */
            .float-contact{
                position:fixed;right:20px;bottom:20px;z-index:50;
                width:56px;height:56px;border-radius:50%;
                background:linear-gradient(135deg,#FF7863,#F2A900);color:#fff;
                display:flex;align-items:center;justify-content:center;
                box-shadow:0 8px 20px rgba(255,120,99,.4);text-decoration:none;
                transition:transform .15s ease;
            }
            .float-contact:hover{transform:scale(1.08);}
            .float-contact svg{width:26px;height:26px;}

            /* ---------- scroll reveal (progressive enhancement) ---------- */
            .reveal-init{opacity:0;transform:translateY(16px);transition:opacity .6s ease,transform .6s ease;}
            .reveal-init.is-visible{opacity:1;transform:none;}


            header{
                position:sticky;top:0;z-index:40;
                background:rgba(255,249,240,.9);
                backdrop-filter:blur(6px);
                border-bottom:1px solid rgba(43,39,48,.08);
            }
            .nav{
                display:flex;align-items:center;justify-content:space-between;
                padding:16px 0;
            }
            .logo{
                display:flex;align-items:center;gap:10px;
                font-family:'Fredoka',sans-serif;font-weight:700;font-size:1.35rem;
                letter-spacing:.2px;
            }
            .logo-mark{
                width:36px;height:36px;flex-shrink:0;
            }
            .logo-mark img{width:100%;height:100%;object-fit:contain;}
            .nav-links{display:flex;gap:30px;font-weight:700;font-size:.95rem;}
            .nav-links a{text-decoration:none;opacity:.8;transition:opacity .2s;}
            .nav-links a:hover{opacity:1;}
            .nav-actions{display:flex;gap:12px;align-items:center;}
            .btn{
                display:inline-flex;align-items:center;justify-content:center;gap:8px;
                font-family:'Fredoka',sans-serif;font-weight:600;font-size:.95rem;
                padding:12px 24px;border-radius:999px;text-decoration:none;
                border:none;cursor:pointer;transition:transform .15s ease, box-shadow .15s ease;
            }
            .btn:active{transform:translateY(2px);}
            .btn-coral{background:var(--coral);color:#fff;box-shadow:0 5px 0 var(--coral-deep);}
            .btn-coral:hover{box-shadow:0 3px 0 var(--coral-deep);transform:translateY(2px);}
            .btn-outline{background:transparent;color:var(--ink);border:2px solid var(--ink);padding:10px 22px;}
            .btn-sunshine{background:var(--sunshine);color:var(--ink);box-shadow:0 5px 0 var(--sunshine-deep);}
            .btn-sunshine:hover{box-shadow:0 3px 0 var(--sunshine-deep);transform:translateY(2px);}
            .btn-sm{padding:9px 18px;font-size:.85rem;}

            /* ---------- hero ---------- */
            .hero{position:relative;overflow:hidden;padding:64px 0 40px;}
            .hero-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:40px;align-items:center;}
            .eyebrow{
                display:inline-flex;align-items:center;gap:8px;
                background:var(--paper);padding:7px 16px;border-radius:999px;
                font-weight:800;font-size:.8rem;letter-spacing:.03em;
                box-shadow:0 3px 0 rgba(43,39,48,.08);margin-bottom:20px;
            }
            .hero h1{font-size:clamp(2.3rem,4.4vw,3.6rem);font-weight:700;}
            .hero h1 .accent{color:var(--coral);}
            .hero p.lede{font-size:1.15rem;color:var(--ink-soft);margin:20px 0 28px;max-width:46ch;}
            .hero-ctas{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:34px;}
            .trust-row{display:flex;gap:26px;flex-wrap:wrap;}
            .trust-item{display:flex;align-items:center;gap:8px;font-weight:800;font-size:.85rem;color:var(--ink-soft);}
            .trust-item .dot{width:8px;height:8px;border-radius:50%;background:var(--mint);}

            .hero-art{position:relative;height:420px;}
            .blob{position:absolute;border-radius:50%;}
            .blob-sky{width:280px;height:280px;background:var(--sky);opacity:.35;top:0;right:20px;filter:blur(2px);}
            .blob-sunshine{width:180px;height:180px;background:var(--sunshine);opacity:.5;bottom:10px;left:0;}
            .art-card{
                position:absolute;background:var(--paper);border-radius:var(--radius);
                box-shadow:0 18px 40px rgba(43,39,48,.14);padding:18px;
            }
            .art-card svg{width:100%;height:auto;}
            .art-card.c1{width:190px;top:20px;left:40px;transform:rotate(-6deg);}
            .art-card.c2{width:170px;bottom:30px;right:10px;transform:rotate(7deg);}
            .art-card.c3{width:140px;top:150px;right:130px;transform:rotate(-3deg);background:var(--sunshine);box-shadow:0 14px 30px rgba(242,169,0,.35);}
            .float-star{position:absolute;color:var(--coral);}

            /* ---------- features ---------- */
            .features{padding:56px 0 64px;background:var(--paper);}
            .section-head{text-align:center;max-width:560px;margin:0 auto 40px;}
            .section-head .kicker{
                font-weight:800;color:var(--coral);text-transform:uppercase;
                letter-spacing:.08em;font-size:.8rem;
            }
            .section-head h2{font-size:clamp(1.6rem,2.6vw,2.2rem);margin-top:10px;}
            .section-head p{color:var(--ink-soft);margin-top:12px;}

            .feature-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:22px;}
            .feature-card{
                background:var(--cream);border-radius:var(--radius);
                padding:26px 22px;text-align:left;
                border:1px solid rgba(43,39,48,.06);
                transition:transform .15s ease, box-shadow .15s ease;
            }
            .feature-card:hover{transform:translateY(-4px);box-shadow:0 14px 30px rgba(43,39,48,.08);}
            .feature-icon{
                width:52px;height:52px;border-radius:16px;
                display:flex;align-items:center;justify-content:center;margin-bottom:16px;color:#fff;
            }
            .feature-icon svg{width:26px;height:26px;}
            .feature-card h3{font-size:1.05rem;margin-bottom:8px;}
            .feature-card p{font-size:.9rem;color:var(--ink-soft);margin:0;line-height:1.5;}

            /* ---------- categories ---------- */
            .categories{padding:64px 0;}
            .cat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;}
            .cat-card{
                position:relative;border-radius:var(--radius);padding:30px 26px;min-height:230px;
                display:flex;flex-direction:column;justify-content:flex-end;overflow:hidden;
                color:#fff;text-decoration:none;
            }
            .cat-card:before{
                content:"";position:absolute;inset:0;opacity:.14;
                background-image:radial-gradient(circle,#fff 2px,transparent 2px);
                background-size:18px 18px;
            }
            .cat-card.cat-1{background:var(--coral);}
            .cat-card.cat-2{background:var(--sky);}
            .cat-card.cat-3{background:var(--mint-deep);}
            .cat-tag{font-weight:800;font-size:.78rem;opacity:.85;margin-bottom:6px;}
            .cat-card h3{font-size:1.5rem;position:relative;z-index:1;}
            .cat-link{
                position:relative;z-index:1;margin-top:14px;font-weight:800;font-size:.9rem;
                display:inline-flex;align-items:center;gap:6px;
            }

            /* ---------- best seller carousel ---------- */
            .bestseller{padding:64px 0;background:var(--paper);}
            .carousel-wrap{position:relative;padding:0 34px;}
            .carousel-track{
                display:flex;gap:20px;overflow-x:auto;scroll-snap-type:x mandatory;
                padding:6px 4px 18px;scrollbar-width:none;
            }
            .carousel-track::-webkit-scrollbar{display:none;}
            .product-card{
                flex:0 0 auto;width:230px;scroll-snap-align:start;
                background:var(--cream);border-radius:var(--radius);overflow:hidden;
                border:1px solid rgba(43,39,48,.06);text-decoration:none;color:inherit;
                display:block;transition:transform .15s ease;
            }
            .product-card:hover{transform:translateY(-4px);box-shadow:0 14px 30px rgba(43,39,48,.12);}
            .product-thumb{
                height:170px;display:flex;align-items:center;justify-content:center;
                position:relative;
            }
            .product-thumb svg{width:64px;height:64px;}
            .product-badge{
                position:absolute;top:10px;left:10px;background:var(--coral);color:#fff;
                font-size:.68rem;font-weight:800;padding:3px 10px;border-radius:999px;
            }
            .product-info{padding:14px 16px 18px;}
            .product-info h3{font-size:.92rem;font-weight:700;margin-bottom:6px;line-height:1.3;font-family:'Nunito',sans-serif;}
            .product-info .price{font-family:'Fredoka',sans-serif;font-weight:600;color:var(--coral-deep);font-size:.9rem;margin-bottom:10px;}
            .product-info .btn{width:100%;padding:8px 14px;font-size:.8rem;}
            .carousel-nav{
                position:absolute;top:50%;transform:translateY(-50%);
                width:44px;height:44px;border-radius:50%;
                background:rgba(255,255,255,.97);
                box-shadow:0 8px 22px rgba(43,39,48,.18), 0 0 0 1px rgba(43,39,48,.05);
                display:flex;align-items:center;justify-content:center;cursor:pointer;
                border:none;z-index:10;color:var(--ink);
                transition:background .15s ease, color .15s ease, box-shadow .15s ease, transform .15s ease;
            }
            .carousel-nav svg{width:20px;height:20px;}
            .carousel-nav:hover{background:var(--coral);color:#fff;box-shadow:0 10px 26px rgba(255,120,99,.4);}
            .carousel-nav:active{transform:translateY(-50%) scale(.9);}
            .carousel-nav.prev{left:0;}
            .carousel-nav.next{right:0;}
            @media (max-width:900px){
                .carousel-wrap{padding:0;}
                .carousel-nav{display:none;}
            }

            .size-section{padding:56px 0 70px;background:var(--paper);}
            .size-strip{
                display:flex;justify-content:center;flex-wrap:wrap;
                gap:16px;padding:8px 4px 10px;
            }
            .size-tag{
                flex:0 0 auto;position:relative;
                background:var(--cream);
                border:2px dashed rgba(43,39,48,.25);
                border-radius:14px;
                padding:16px 20px 14px;
                min-width:104px;text-align:center;
            }
            .size-tag:after{
                content:"";position:absolute;top:-8px;left:50%;transform:translateX(-50%);
                width:14px;height:14px;background:var(--paper);
                border:2px solid rgba(43,39,48,.25);border-radius:50%;
            }
            .size-tag .age{font-family:'Fredoka',sans-serif;font-weight:700;font-size:1.3rem;}
            .size-tag .yr{display:block;font-size:.72rem;font-weight:800;color:var(--ink-soft);margin-top:2px;letter-spacing:.04em;}

            /* ---------- testimonial ---------- */
            .testimonial{padding:10px 0 70px;}
            .quote-card{
                max-width:760px;margin:0 auto;background:var(--ink);color:#fff;
                border-radius:28px;padding:46px 44px;text-align:center;position:relative;
            }
            .quote-mark{font-family:'Fredoka',sans-serif;font-size:4rem;color:var(--sunshine);line-height:0;position:relative;top:18px;}
            .quote-card p.quote{font-size:1.25rem;font-weight:600;line-height:1.55;margin:0 auto 22px;max-width:52ch;}
            .stars{color:var(--sunshine);letter-spacing:3px;margin-bottom:14px;font-size:1.1rem;}
            .quote-by{font-weight:800;font-size:.9rem;opacity:.75;}

            /* ---------- newsletter CTA ---------- */
            .cta-band{background:var(--sunshine);padding:54px 0;}
            .cta-band-inner{
                display:flex;align-items:center;justify-content:space-between;gap:30px;flex-wrap:wrap;
            }
            .cta-band h2{font-size:clamp(1.5rem,2.6vw,2rem);max-width:420px;}
            .cta-band p{margin-top:10px;font-weight:700;color:rgba(43,39,48,.7);}
            .cta-form{display:flex;gap:10px;flex-wrap:wrap;}
            .cta-form input{
                border:none;border-radius:999px;padding:14px 20px;font-family:'Nunito',sans-serif;
                font-weight:700;font-size:.95rem;min-width:230px;
            }

            /* ---------- footer ---------- */
            footer{background:var(--ink);color:rgba(255,255,255,.75);padding:54px 0 26px;}
            .footer-grid{display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr;gap:30px;margin-bottom:40px;}
            .footer-logo{display:flex;align-items:center;gap:10px;color:#fff;font-family:'Fredoka',sans-serif;font-weight:700;font-size:1.2rem;margin-bottom:12px;}
            .footer-logo .logo-mark{width:32px;height:32px;}
            .footer-grid p{font-size:.88rem;line-height:1.6;max-width:32ch;}
            .footer-grid h4{color:#fff;font-family:'Fredoka',sans-serif;font-size:.95rem;margin-bottom:14px;}
            .footer-grid ul{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;font-size:.88rem;}
            .footer-grid a{text-decoration:none;opacity:.85;}
            .footer-grid a:hover{opacity:1;}
            .footer-bottom{
                border-top:1px solid rgba(255,255,255,.12);padding-top:22px;
                display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;font-size:.8rem;
            }

            @media (max-width:900px){
                .hero-grid{grid-template-columns:1fr;}
                .hero-art{height:320px;margin-top:20px;}
                .feature-grid{grid-template-columns:repeat(2,1fr);}
                .cat-grid{grid-template-columns:1fr;}
                .footer-grid{grid-template-columns:1fr 1fr;}
                .nav-links{display:none;}
                .stats-grid{grid-template-columns:repeat(2,1fr);gap:24px;}
                .steps-grid{grid-template-columns:1fr;}
                .product-card{width:190px;}
                .product-thumb{height:140px;}
            }
            @media (max-width:560px){
                .feature-grid{grid-template-columns:1fr;}
                .our-products{padding:64px 0;}
                .our-products-grid{grid-template-columns:1fr 1fr;gap:11px;}
                .our-product-card{border-radius:18px;}
                .our-product-info{padding:13px 12px 14px;}
                .our-product-info h3{font-size:.79rem;min-height:3.1em;}
                .our-product-sold{display:none;}
                .our-product-price{font-size:.88rem;}
                .our-product-arrow{width:32px;height:32px;}
                .product-filter{gap:7px;overflow-x:auto;flex-wrap:nowrap;padding-bottom:4px;scrollbar-width:none;}
                .product-filter::-webkit-scrollbar{display:none;}
                .product-filter-btn{flex:0 0 auto;padding:9px 13px;font-size:.76rem;}
                .footer-grid{grid-template-columns:1fr;}
                .cta-band-inner{flex-direction:column;align-items:flex-start;}
            }
            @media (prefers-reduced-motion: reduce){
                *{transition:none !important;}
            }
            /* =========================================================
               SECOND STAR — PREMIUM PLAYFUL EXPERIENCE
            ========================================================= */
            :root{
                --ease-premium:cubic-bezier(.16,1,.3,1);
                --ease-spring:cubic-bezier(.34,1.56,.64,1);
                --shadow-soft:0 12px 30px rgba(43,39,48,.08);
                --shadow-hover:0 24px 55px rgba(43,39,48,.16);
            }

            html{scroll-behavior:smooth;}
            body{overflow-x:hidden;}

            /* ---------- page intro ---------- */
            body{
                opacity:0;
                animation:pageFadeIn .55s var(--ease-premium) forwards;
            }
            @keyframes pageFadeIn{to{opacity:1;}}

            /* ---------- scroll progress ---------- */
            #scroll-progress{
                position:fixed;top:0;left:0;width:0;height:3px;z-index:9999;
                background:linear-gradient(90deg,var(--coral),var(--sunshine),var(--sky),var(--mint));
                box-shadow:0 0 12px rgba(255,120,99,.45);
                pointer-events:none;
            }

            /* ---------- ambient cursor glow ---------- */
            #ambient-cursor-glow{
                position:fixed;left:0;top:0;width:420px;height:420px;border-radius:50%;
                background:radial-gradient(circle,rgba(255,120,99,.09) 0%,rgba(110,195,224,.05) 38%,transparent 72%);
                transform:translate(-50%,-50%);pointer-events:none;z-index:0;opacity:0;
                transition:opacity .35s ease;filter:blur(8px);
            }

            /* ---------- premium sticky header ---------- */
            header{
                transition:background .35s ease,box-shadow .35s ease,border-color .35s ease,backdrop-filter .35s ease;
            }
            header.header-scrolled{
                background:rgba(255,249,240,.88);
                backdrop-filter:blur(18px) saturate(1.25);
                -webkit-backdrop-filter:blur(18px) saturate(1.25);
                border-bottom-color:rgba(43,39,48,.05);
                box-shadow:0 10px 35px rgba(43,39,48,.07);
            }
            .nav-links a{position:relative;}
            .nav-links a::after{
                content:"";position:absolute;left:0;right:0;bottom:-8px;height:2px;border-radius:999px;
                background:var(--coral);transform:scaleX(0);transform-origin:center;
                transition:transform .3s var(--ease-premium);
            }
            .nav-links a:hover::after,.nav-links a.is-active::after{transform:scaleX(1);}

            .logo{transition:transform .25s var(--ease-premium);}
            .logo:hover{transform:translateY(-2px);}
            .logo-mark{transition:transform .5s var(--ease-spring),filter .4s ease;}
            .logo:hover .logo-mark{
                transform:rotate(-8deg) scale(1.1);
                filter:drop-shadow(0 7px 10px rgba(255,120,99,.22));
            }

            /* ---------- hero atmosphere ---------- */
            .hero{min-height:610px;isolation:isolate;}
            .hero-grid{position:relative;z-index:5;}
            .hero-grid-bg{
                position:absolute;inset:0;z-index:-3;opacity:.18;
                background-image:linear-gradient(rgba(43,39,48,.055) 1px,transparent 1px),linear-gradient(90deg,rgba(43,39,48,.055) 1px,transparent 1px);
                background-size:44px 44px;
                mask-image:linear-gradient(to bottom,black,transparent 85%);
                -webkit-mask-image:linear-gradient(to bottom,black,transparent 85%);
                pointer-events:none;
            }
            .hero-orb{position:absolute;border-radius:50%;z-index:-2;pointer-events:none;}
            .hero-orb-1{
                width:300px;height:300px;right:-70px;top:30px;
                background:radial-gradient(circle,rgba(110,195,224,.32),rgba(110,195,224,0));
                animation:orbFloatOne 11s ease-in-out infinite;
            }
            .hero-orb-2{
                width:270px;height:270px;left:-90px;bottom:-40px;
                background:radial-gradient(circle,rgba(255,201,77,.30),transparent 68%);
                animation:orbFloatTwo 13s ease-in-out infinite;
            }
            .hero-orb-3{
                width:220px;height:220px;left:45%;top:20%;
                background:radial-gradient(circle,rgba(127,207,174,.20),transparent 70%);
                animation:orbFloatThree 9s ease-in-out infinite;
            }
            @keyframes orbFloatOne{
                0%,100%{transform:translate(0,0) scale(1);}
                50%{transform:translate(-28px,30px) scale(1.09);}
            }
            @keyframes orbFloatTwo{
                0%,100%{transform:translate(0,0) scale(1);}
                50%{transform:translate(32px,-18px) scale(.94);}
            }
            @keyframes orbFloatThree{
                0%,100%{transform:translate(0,0);}
                50%{transform:translate(18px,26px);}
            }

            /* ---------- hero entrance ---------- */
            .hero .eyebrow,.hero h1,.hero .lede,.hero-ctas,.trust-row{
                opacity:0;transform:translateY(34px) scale(.985);
                animation:premiumHeroReveal .9s var(--ease-premium) forwards;
            }
            .hero .eyebrow{animation-delay:.08s;}
            .hero h1{animation-delay:.19s;}
            .hero .lede{animation-delay:.31s;}
            .hero-ctas{animation-delay:.43s;}
            .trust-row{animation-delay:.55s;}
            @keyframes premiumHeroReveal{to{opacity:1;transform:translateY(0) scale(1);}}

            .hero h1 .accent{position:relative;display:inline-block;}
            .hero h1 .accent::after{
                content:"";position:absolute;left:0;bottom:-5px;width:100%;height:8px;
                background:var(--sunshine);border-radius:50% 40% 55% 45%;z-index:-1;
                transform:scaleX(0) rotate(-1deg);transform-origin:left;
                animation:accentDraw .7s .9s var(--ease-premium) forwards;
            }
            @keyframes accentDraw{to{transform:scaleX(1) rotate(-1deg);}}

            /* ---------- hero 3D art ---------- */
            .hero-art{
                perspective:1100px;transform-style:preserve-3d;
                opacity:0;animation:heroArtReveal .9s .25s var(--ease-premium) forwards;
                transition:transform .35s var(--ease-premium);
            }
            @keyframes heroArtReveal{to{opacity:1;}}
            .art-card{
                will-change:transform;border:1px solid rgba(255,255,255,.65);
                transition:box-shadow .35s ease,filter .35s ease;
            }
            .art-card:hover{box-shadow:0 28px 60px rgba(43,39,48,.19);filter:saturate(1.05);}
            .art-card.c1{animation:premiumFloatOne 6s ease-in-out infinite;}
            .art-card.c2{animation:premiumFloatTwo 7.3s ease-in-out infinite;}
            .art-card.c3{animation:premiumFloatThree 5.4s ease-in-out infinite;}
            @keyframes premiumFloatOne{
                0%,100%{transform:translateY(0) rotate(-6deg);}
                50%{transform:translateY(-17px) rotate(-2deg);}
            }
            @keyframes premiumFloatTwo{
                0%,100%{transform:translateY(0) rotate(7deg);}
                50%{transform:translateY(-19px) rotate(3deg);}
            }
            @keyframes premiumFloatThree{
                0%,100%{transform:translateY(0) rotate(-3deg);}
                50%{transform:translateY(-12px) rotate(2deg);}
            }
            .blob-sky{animation:blobSky 8s ease-in-out infinite;}
            .blob-sunshine{animation:blobSun 7s ease-in-out infinite;}
            @keyframes blobSky{
                0%,100%{transform:translate(0,0) scale(1);}
                50%{transform:translate(-14px,12px) scale(1.06);}
            }
            @keyframes blobSun{
                0%,100%{transform:translate(0,0) scale(1);}
                50%{transform:translate(12px,-10px) scale(.94);}
            }

            /* ---------- buttons ---------- */
            .btn{
                position:relative;overflow:hidden;isolation:isolate;
                transition:transform .24s var(--ease-premium),box-shadow .24s ease,filter .24s ease;
            }
            .btn:hover{transform:translateY(-3px);}
            .btn:active{transform:translateY(1px) scale(.975);}
            .btn::before{
                content:"";position:absolute;top:-50%;left:-80%;width:60%;height:200%;
                background:linear-gradient(110deg,transparent,rgba(255,255,255,.38),transparent);
                transform:skewX(-18deg);transition:left .75s var(--ease-premium);z-index:2;pointer-events:none;
            }
            .btn:hover::before{left:135%;}

            /* ---------- trust pulse ---------- */
            .trust-item .dot{position:relative;}
            .trust-item .dot::before{
                content:"";position:absolute;inset:-5px;border-radius:50%;
                border:1px solid rgba(79,175,135,.7);animation:dotPulse 2.3s ease-out infinite;
            }
            @keyframes dotPulse{
                0%{opacity:.9;transform:scale(.5);}
                80%,100%{opacity:0;transform:scale(1.9);}
            }

            /* ---------- cinematic reveal ---------- */
            .reveal-init{
                opacity:0;transform:translateY(36px) scale(.985);
                transition:opacity .8s var(--ease-premium),transform .8s var(--ease-premium);
                will-change:transform,opacity;
            }
            .reveal-init.is-visible{opacity:1;transform:translateY(0) scale(1);}

            /* ---------- 3D tilt cards ---------- */
            .tilt-card{
                --rx:0deg;--ry:0deg;--mx:50%;--my:50%;position:relative;transform-style:preserve-3d;
                transform:perspective(900px) rotateX(var(--rx)) rotateY(var(--ry));
                transition:transform .16s ease,box-shadow .35s ease;will-change:transform;
            }
            .tilt-card:hover{
                transform:perspective(900px) rotateX(var(--rx)) rotateY(var(--ry));
                box-shadow:var(--shadow-hover);
            }
            .tilt-card::after{
                content:"";position:absolute;inset:0;border-radius:inherit;pointer-events:none;opacity:0;
                background:radial-gradient(circle at var(--mx) var(--my),rgba(255,255,255,.32),transparent 42%);
                transition:opacity .25s ease;
            }
            .tilt-card:hover::after{opacity:1;}

            /* ---------- feature polish ---------- */
            .feature-card{overflow:hidden;isolation:isolate;}
            .feature-card::before{
                content:"";position:absolute;width:120px;height:120px;top:-50px;right:-50px;border-radius:50%;
                background:rgba(255,255,255,.45);transform:scale(.7);opacity:0;
                transition:transform .6s var(--ease-premium),opacity .4s ease;
            }
            .feature-card:hover::before{opacity:1;transform:scale(1.6);}
            .feature-icon{transition:transform .4s var(--ease-spring);}
            .feature-card:hover .feature-icon{transform:translateY(-3px) rotate(-7deg) scale(1.08);}

            /* ---------- category polish ---------- */
            .cat-card{transition:box-shadow .35s ease;}
            .cat-card h3{transition:transform .35s var(--ease-premium);}
            .cat-card:hover h3{transform:translateY(-4px);}
            .cat-link{transition:transform .35s var(--ease-premium);}
            .cat-card:hover .cat-link{transform:translateX(7px);}
            .cat-card::before{transition:opacity .4s ease,transform .7s var(--ease-premium);}
            .cat-card:hover::before{opacity:.23;transform:scale(1.12) rotate(2deg);}

            /* ---------- stats count up ---------- */
            .stats-grid .stat{
                opacity:0;transform:translateY(20px);
                transition:transform .5s var(--ease-premium),opacity .5s ease;
            }
            .stats-grid .stat.is-counted{opacity:1;transform:translateY(0);}
            .stats-grid .stat b{display:inline-block;transition:transform .3s var(--ease-spring);}
            .stats-grid .stat:hover b{transform:scale(1.1);}

            /* ---------- product polish ---------- */
            .product-card{position:relative;isolation:isolate;}
            .product-thumb{overflow:hidden;}
            .product-thumb::after{
                content:"";position:absolute;inset:0;
                background:linear-gradient(130deg,transparent 30%,rgba(255,255,255,.25),transparent 70%);
                transform:translateX(-130%);transition:transform .7s var(--ease-premium);
            }
            .product-card:hover .product-thumb::after{transform:translateX(130%);}
            .carousel-nav{
                transition:transform .25s var(--ease-spring),background .25s ease,color .25s ease,box-shadow .25s ease;
            }
            .carousel-nav:hover{transform:translateY(-50%) scale(1.08);}

            /* ---------- size / faq ---------- */
            .size-tag{
                transition:transform .35s var(--ease-spring),border-color .35s ease,box-shadow .35s ease;
            }
            .size-tag:hover{
                transform:translateY(-7px) rotate(-1.5deg);border-color:var(--coral);
                box-shadow:0 12px 25px rgba(43,39,48,.08);
            }
            .faq-item{
                transition:transform .3s var(--ease-premium),box-shadow .3s ease,border-color .3s ease;
            }
            .faq-item:hover{transform:translateY(-3px);box-shadow:0 14px 30px rgba(43,39,48,.08);}
            .faq-item[open]{border-color:rgba(255,120,99,.35);}

            /* ---------- testimonial ---------- */
            .quote-card{
                overflow:hidden;transition:transform .45s var(--ease-premium),box-shadow .45s ease;
            }
            .quote-card:hover{transform:translateY(-8px);box-shadow:0 28px 60px rgba(43,39,48,.24);}
            .stars{animation:starBreath 2.6s ease-in-out infinite;}
            @keyframes starBreath{
                0%,100%{letter-spacing:3px;text-shadow:0 0 0 rgba(255,201,77,0);}
                50%{letter-spacing:5px;text-shadow:0 0 16px rgba(255,201,77,.42);}
            }

            /* ---------- floating contact ---------- */
            .float-contact{animation:contactBreath 3.2s ease-in-out infinite;}
            .float-contact::before{
                content:"";position:absolute;inset:-7px;border-radius:50%;
                border:1px solid rgba(255,120,99,.45);animation:contactRing 2.2s ease-out infinite;
            }
            .float-contact:hover{animation-play-state:paused;transform:scale(1.10) rotate(-5deg);}
            @keyframes contactBreath{
                0%,100%{transform:translateY(0);}
                50%{transform:translateY(-8px);}
            }
            @keyframes contactRing{
                0%{opacity:.7;transform:scale(.8);}
                100%{opacity:0;transform:scale(1.35);}
            }

            /* ---------- responsive motion ---------- */
            /* ---------- real product collections ---------- */
            .cat-product-card{
                min-height:310px;
                padding:0;
                background:#efe7dc;
                border:1px solid rgba(43,39,48,.07);
            }
            .cat-product-card::before{display:none;}
            .cat-product-img{
                position:absolute;inset:0;width:100%;height:100%;object-fit:cover;
                transition:transform .65s var(--ease-premium),filter .45s ease;
            }
            .cat-product-card:hover .cat-product-img{transform:scale(1.055);filter:saturate(1.04);}
            .cat-product-overlay{
                position:absolute;inset:0;z-index:1;
                background:linear-gradient(180deg,rgba(19,16,22,.02) 26%,rgba(19,16,22,.72) 100%);
            }
            .cat-product-content{
                position:absolute;left:0;right:0;bottom:0;z-index:2;padding:28px;
                color:#fff;
            }
            .cat-product-content .cat-tag{font-size:.72rem;letter-spacing:.08em;text-shadow:0 2px 12px rgba(0,0,0,.18);}
            .cat-product-content h3{font-size:1.8rem;text-shadow:0 3px 18px rgba(0,0,0,.18);}
            .cat-product-content .cat-link{margin-top:12px;display:flex;justify-content:space-between;align-items:center;}
            .cat-product-content .cat-link b{font-size:1.2rem;}

            /* ---------- Our Products ---------- */
            .our-products{padding:82px 0;background:var(--cream);position:relative;overflow:hidden;}
            .our-products::before{
                content:"";position:absolute;width:420px;height:420px;border-radius:50%;right:-160px;top:-170px;
                background:radial-gradient(circle,rgba(110,195,224,.15),transparent 70%);pointer-events:none;
            }
            .our-products-head{display:flex;align-items:flex-end;justify-content:space-between;gap:26px;margin-bottom:28px;}
            .our-products-title{margin:0;text-align:left;}
            .our-products-title p{max-width:620px;}
            .our-products-all{flex-shrink:0;margin-bottom:3px;}
            .our-products-all svg{width:18px;height:18px;}
            .product-filter{display:flex;gap:9px;flex-wrap:wrap;margin:0 0 28px;}
            .product-filter-btn{
                border:1px solid rgba(43,39,48,.10);background:rgba(255,255,255,.72);color:var(--ink-soft);
                padding:10px 16px;border-radius:999px;font-family:'Nunito',sans-serif;font-weight:800;font-size:.82rem;
                cursor:pointer;transition:all .25s var(--ease-premium);
            }
            .product-filter-btn:hover{transform:translateY(-2px);border-color:rgba(255,120,99,.28);color:var(--ink);}
            .product-filter-btn.is-active{background:var(--ink);border-color:var(--ink);color:#fff;box-shadow:0 8px 22px rgba(43,39,48,.16);}
            .our-products-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px;}
            .our-product-card{
                min-width:0;background:#fff;border:1px solid rgba(43,39,48,.07);border-radius:24px;overflow:hidden;
                box-shadow:0 9px 26px rgba(43,39,48,.055);transition:transform .35s var(--ease-premium),box-shadow .35s ease,opacity .25s ease;
            }
            .our-product-card:hover{transform:translateY(-7px);box-shadow:0 22px 46px rgba(43,39,48,.12);}
            .our-product-card.is-hidden,.our-product-card.is-collapsed{display:none;}
            .our-product-image{display:block;position:relative;aspect-ratio:1/1;background:#f6efe6;overflow:hidden;}
            .our-product-placeholder{
                position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:11px;
                padding:26px;text-align:center;background:
                    radial-gradient(circle at 82% 18%,rgba(110,195,224,.30),transparent 30%),
                    radial-gradient(circle at 18% 82%,rgba(255,201,77,.30),transparent 32%),
                    linear-gradient(145deg,#fffaf3,#f6eee5);
                color:var(--ink);
            }
            .our-product-placeholder img{width:64px;height:64px;object-fit:contain;filter:drop-shadow(0 8px 15px rgba(43,39,48,.12));}
            .our-product-placeholder b{font-family:'Fredoka',sans-serif;font-size:1rem;line-height:1.15;}
            .our-product-placeholder small{font-size:.7rem;font-weight:900;color:var(--ink-soft);letter-spacing:.06em;text-transform:uppercase;}
            .our-product-image>img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .55s var(--ease-premium);}
            .our-product-card:hover .our-product-image>img{transform:scale(1.045);}
            .our-product-badge{
                position:absolute;left:12px;top:12px;z-index:3;background:rgba(255,120,99,.94);color:#fff;
                padding:5px 9px;border-radius:999px;font-size:.66rem;font-weight:900;box-shadow:0 5px 15px rgba(43,39,48,.12);
            }
            .our-product-sku{
                position:absolute;right:12px;top:12px;z-index:3;background:rgba(255,255,255,.9);color:var(--ink);
                padding:5px 8px;border-radius:9px;font-size:.64rem;font-weight:900;backdrop-filter:blur(8px);
            }
            .our-product-info{padding:17px 17px 18px;}
            .our-product-type{font-size:.67rem;font-weight:900;color:var(--coral-deep);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;}
            .our-product-info h3{font-family:'Nunito',sans-serif;font-size:.91rem;line-height:1.38;min-height:2.75em;margin:0 0 10px;}
            .our-product-info h3 a{text-decoration:none;}
            .our-product-info .product-meta{margin-bottom:14px;}
            .our-product-bottom{display:flex;align-items:flex-end;justify-content:space-between;gap:10px;padding-top:13px;border-top:1px solid rgba(43,39,48,.065);}
            .our-product-price{font-family:'Fredoka',sans-serif;font-size:1rem;font-weight:700;color:var(--coral-deep);}
            .our-product-price small{display:block;font-family:'Nunito',sans-serif;font-size:.64rem;color:var(--ink-soft);font-weight:800;margin-bottom:2px;}
            .our-product-arrow{
                width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;
                background:var(--ink);color:#fff;transition:transform .25s var(--ease-spring),background .25s ease;
            }
            .our-product-arrow svg{width:17px;height:17px;}
            .our-product-arrow:hover{background:var(--coral);transform:translateX(3px);}
            .our-products-note{
                margin-top:26px;padding:17px 20px;border:1px dashed rgba(43,39,48,.13);border-radius:18px;
                background:rgba(255,255,255,.58);display:flex;align-items:center;gap:13px;color:var(--ink-soft);font-size:.82rem;
            }
            .our-products-note-icon{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:var(--sunshine);color:var(--ink);font-weight:900;flex-shrink:0;}
            .our-products-note strong{display:block;color:var(--ink);font-size:.84rem;margin-bottom:2px;}
            .catalog-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;margin:0 0 28px;}
            .catalog-toolbar .product-filter{margin:0;}
            .catalog-count{
                flex-shrink:0;display:inline-flex;align-items:center;gap:7px;padding:9px 13px;border-radius:999px;
                background:#fff;border:1px solid rgba(43,39,48,.08);font-size:.76rem;font-weight:900;color:var(--ink-soft);
                box-shadow:0 6px 18px rgba(43,39,48,.05);
            }
            .catalog-count b{font-family:'Fredoka',sans-serif;color:var(--coral-deep);font-size:.9rem;}
            .catalog-loadmore-wrap{display:flex;justify-content:center;margin-top:30px;}
            .catalog-loadmore{min-width:240px;}
            .catalog-loadmore .catalog-chevron{width:17px;height:17px;transition:transform .25s ease;}
            .catalog-loadmore.is-expanded .catalog-chevron{transform:rotate(180deg);}

            @media(max-width:1050px){.our-products-grid{grid-template-columns:repeat(3,minmax(0,1fr));}}

            @media(max-width:900px){
                #ambient-cursor-glow{display:none;}
                .tilt-card,.tilt-card:hover{transform:none !important;}
                .hero{min-height:auto;}
                .our-products-head{align-items:flex-start;flex-direction:column;}
                .our-products-all{margin:0;}
                .our-products-grid{grid-template-columns:repeat(2,minmax(0,1fr));}
                .catalog-toolbar{align-items:flex-start;flex-direction:column;}
                .catalog-count{align-self:flex-start;}
            }

            @media(prefers-reduced-motion:reduce){
                *,*::before,*::after{
                    animation-duration:.001ms !important;animation-iteration-count:1 !important;
                    transition-duration:.001ms !important;scroll-behavior:auto !important;
                }
                #ambient-cursor-glow{display:none !important;}
                .reveal-init{opacity:1 !important;transform:none !important;}
                .tilt-card,.tilt-card:hover{transform:none !important;}
            }


            /* =========================================================
               SECOND STAR — POLISHED ORIGINAL UI v2
               Keeps the first concept, only refines visual hierarchy.
            ========================================================= */
            :root{
                --cream:#FFF8EE;
                --paper:#FFFFFF;
                --ink:#29242E;
                --ink-soft:#6F6876;
                --line:rgba(43,39,48,.085);
                --shadow-card:0 12px 34px rgba(43,39,48,.075);
                --shadow-card-hover:0 22px 50px rgba(43,39,48,.13);
                --radius-lg:30px;
            }

            body{
                background:
                    radial-gradient(circle at 8% 4%,rgba(255,201,77,.10),transparent 24rem),
                    radial-gradient(circle at 92% 10%,rgba(110,195,224,.10),transparent 25rem),
                    var(--cream);
            }
            .wrap{max-width:1220px;padding-left:28px;padding-right:28px;}
            section[id],footer[id]{scroll-margin-top:108px;}
            #ambient-cursor-glow{display:none !important;}

            /* Floating glass header: same structure, cleaner treatment */
            header{
                padding:10px 0 0;
                background:transparent;
                border-bottom:none;
                backdrop-filter:none;
                -webkit-backdrop-filter:none;
            }
            header.header-scrolled{
                background:transparent;
                border-bottom-color:transparent;
                box-shadow:none;
                backdrop-filter:none;
                -webkit-backdrop-filter:none;
            }
            .nav{
                min-height:68px;
                padding:10px 12px 10px 16px;
                background:rgba(255,249,240,.86);
                border:1px solid rgba(43,39,48,.075);
                border-radius:22px;
                box-shadow:0 8px 26px rgba(43,39,48,.055);
                backdrop-filter:blur(16px) saturate(1.18);
                -webkit-backdrop-filter:blur(16px) saturate(1.18);
                transition:background .3s ease,box-shadow .3s ease,border-color .3s ease,transform .3s var(--ease-premium);
            }
            header.header-scrolled .nav{
                background:rgba(255,255,255,.91);
                border-color:rgba(43,39,48,.07);
                box-shadow:0 14px 40px rgba(43,39,48,.10);
            }
            .logo{font-size:1.28rem;gap:11px;white-space:nowrap;}
            .logo-mark{width:40px;height:40px;}
            .nav-links{gap:8px;}
            .nav-links a{
                opacity:.72;
                padding:10px 13px;
                border-radius:12px;
                transition:opacity .2s ease,background .2s ease,color .2s ease;
            }
            .nav-links a:hover,.nav-links a.is-active{
                opacity:1;
                background:rgba(255,255,255,.72);
            }
            .nav-links a::after{bottom:3px;left:13px;right:13px;}
            .nav-actions{gap:9px;}
            .btn{letter-spacing:.005em;}
            .btn-coral{
                box-shadow:0 5px 0 var(--coral-deep),0 10px 24px rgba(255,120,99,.16);
            }
            .btn-outline{
                border-color:rgba(43,39,48,.72);
                background:rgba(255,255,255,.28);
            }
            .btn-outline:hover{background:var(--ink);color:#fff;border-color:var(--ink);}

            /* Mobile menu */
            .mobile-menu-toggle{
                display:none;
                width:44px;height:44px;
                border:1px solid var(--line);
                border-radius:14px;
                background:rgba(255,255,255,.72);
                color:var(--ink);
                align-items:center;justify-content:center;
                cursor:pointer;
                transition:transform .2s ease,background .2s ease;
            }
            .mobile-menu-toggle:active{transform:scale(.96);}
            .mobile-menu-toggle svg{width:22px;height:22px;}
            .mobile-menu-wrap{position:relative;}
            .mobile-nav-panel{
                display:none;
                margin-top:8px;
                padding:12px;
                background:rgba(255,255,255,.96);
                border:1px solid var(--line);
                border-radius:20px;
                box-shadow:0 18px 45px rgba(43,39,48,.12);
                backdrop-filter:blur(16px);
                -webkit-backdrop-filter:blur(16px);
            }
            .mobile-nav-panel.is-open{display:grid;gap:6px;animation:mobileMenuIn .22s var(--ease-premium);}
            .mobile-nav-panel a:not(.btn){
                text-decoration:none;font-weight:800;padding:12px 14px;border-radius:12px;color:var(--ink);
            }
            .mobile-nav-panel a:not(.btn):hover{background:var(--cream);}
            .mobile-nav-panel .btn{width:100%;margin-top:4px;}
            @keyframes mobileMenuIn{
                from{opacity:0;transform:translateY(-8px) scale(.985);}
                to{opacity:1;transform:none;}
            }

            /* Hero: still playful, more composed */
            .hero{padding:82px 0 70px;min-height:640px;}
            .hero-grid{grid-template-columns:1.02fr .98fr;gap:64px;}
            .hero .eyebrow{
                margin-bottom:22px;
                border:1px solid rgba(43,39,48,.06);
                box-shadow:0 5px 18px rgba(43,39,48,.055);
            }
            .hero h1{
                font-size:clamp(2.65rem,4.8vw,4.25rem);
                letter-spacing:-.035em;
                text-wrap:balance;
                max-width:12ch;
            }
            .hero p.lede{
                font-size:1.08rem;
                line-height:1.75;
                max-width:54ch;
                margin:22px 0 30px;
            }
            .hero-ctas{gap:12px;margin-bottom:30px;}
            .hero-ctas .btn{min-height:50px;padding-left:23px;padding-right:23px;}
            .trust-row{gap:14px 24px;}
            .trust-item{
                background:rgba(255,255,255,.48);
                border:1px solid rgba(43,39,48,.055);
                padding:8px 11px;
                border-radius:12px;
            }
            .hero-art{
                height:450px;
                border-radius:40px;
                isolation:isolate;
            }
            .hero-art::before{
                content:"";
                position:absolute;
                inset:22px 10px 18px 18px;
                z-index:-1;
                background:
                    linear-gradient(145deg,rgba(255,255,255,.86),rgba(255,255,255,.52)),
                    linear-gradient(135deg,rgba(255,201,77,.12),rgba(110,195,224,.10));
                border:1px solid rgba(255,255,255,.8);
                border-radius:38px;
                box-shadow:0 28px 70px rgba(43,39,48,.09);
                transform:rotate(1.4deg);
            }
            .hero-art::after{
                content:"";
                position:absolute;
                inset:42px 34px 38px 48px;
                z-index:-1;
                border:1px dashed rgba(43,39,48,.09);
                border-radius:30px;
                transform:rotate(-1deg);
            }
            .art-card{
                border:1px solid rgba(43,39,48,.055);
                box-shadow:0 16px 38px rgba(43,39,48,.12);
            }
            .art-card.c1{width:190px;top:36px;left:42px;}
            .art-card.c2{width:182px;bottom:36px;right:22px;}
            .art-card.c3{width:126px;top:158px;right:150px;border-radius:26px;}
            .hero-float-label{
                position:absolute;z-index:8;
                display:inline-flex;align-items:center;gap:7px;
                padding:9px 13px;border-radius:999px;
                background:rgba(255,255,255,.92);
                border:1px solid rgba(43,39,48,.065);
                box-shadow:0 10px 25px rgba(43,39,48,.10);
                font-size:.75rem;font-weight:900;color:var(--ink);
                backdrop-filter:blur(10px);
            }
            .hero-float-label::before{content:"";width:7px;height:7px;border-radius:50%;background:var(--mint-deep);}
            .hero-float-label.one{left:2px;bottom:82px;}
            .hero-float-label.two{right:0;top:72px;}
            .hero-float-label.two::before{background:var(--coral);}

            /* Section rhythm */
            .features,.categories,.bestseller,.size-section,.steps{padding-top:78px;padding-bottom:82px;}
            .faq{padding-top:34px;padding-bottom:82px;}
            .section-head{max-width:640px;margin-bottom:44px;}
            .section-head .kicker{
                display:inline-flex;padding:7px 12px;border-radius:999px;
                background:rgba(255,120,99,.09);
            }
            .section-head h2{font-size:clamp(1.85rem,3vw,2.55rem);letter-spacing:-.018em;text-wrap:balance;}
            .section-head p{line-height:1.65;}

            /* Stats: keep dark brand moment, refine spacing */
            .stats-bar{padding:34px 0;position:relative;overflow:hidden;}
            .stats-bar::before{
                content:"";position:absolute;inset:0;pointer-events:none;
                background:radial-gradient(circle at 12% 20%,rgba(255,201,77,.08),transparent 24rem),radial-gradient(circle at 90% 80%,rgba(110,195,224,.07),transparent 24rem);
            }
            .stats-grid{position:relative;z-index:1;gap:10px;}
            .stats-grid .stat{padding:10px 18px;border-right:1px solid rgba(255,255,255,.10);}
            .stats-grid .stat:last-child{border-right:none;}
            .stats-grid .stat b{font-size:1.7rem;}

            /* Cards: reduce “template” feel */
            .feature-grid{gap:18px;}
            .feature-card,.step-card,.faq-item,.product-card{
                border-color:var(--line);
                box-shadow:0 1px 0 rgba(255,255,255,.7) inset;
            }
            .feature-card{
                border-radius:24px;
                padding:27px 24px 28px;
                background:linear-gradient(180deg,#fff,var(--cream));
            }
            .feature-card:hover{
                transform:translateY(-6px);
                box-shadow:var(--shadow-card-hover);
            }
            .feature-icon{border-radius:15px;box-shadow:0 8px 20px rgba(43,39,48,.09);}

            .cat-grid{gap:18px;}
            .cat-card{
                min-height:278px;
                padding:32px 28px;
                border-radius:28px;
                box-shadow:0 16px 36px rgba(43,39,48,.08);
                transform:translateZ(0);
            }
            .cat-card::after{
                content:"";position:absolute;inset:0;pointer-events:none;
                background:linear-gradient(to top,rgba(24,20,29,.18),transparent 54%);
            }
            .cat-card .cat-tag,.cat-card h3,.cat-card .cat-link{z-index:2;}
            .cat-card h3{font-size:1.72rem;}

            .bestseller{background:rgba(255,255,255,.94);}
            .carousel-track{gap:18px;padding-bottom:22px;}
            .product-card{
                width:245px;
                border-radius:24px;
                background:#fff;
                box-shadow:0 10px 28px rgba(43,39,48,.065);
            }
            .product-card:hover{transform:translateY(-6px);box-shadow:0 20px 45px rgba(43,39,48,.13);}
            .product-thumb{height:190px;}
            .product-info{padding:16px 17px 18px;}
            .product-info h3{font-size:.96rem;min-height:2.5em;}
            .product-info .btn{border-width:1px;background:rgba(255,255,255,.72);}
            .product-badge{top:12px;left:12px;padding:5px 10px;box-shadow:0 5px 14px rgba(43,39,48,.10);}
            .product-photo{
                background:linear-gradient(145deg,#fff8ef,#f7f1e9);
                overflow:hidden;
            }
            .product-img{
                position:absolute;inset:0;width:100%;height:100%;object-fit:cover;
                transition:transform .55s var(--ease-premium),filter .4s ease;
            }
            .product-card:hover .product-img{transform:scale(1.045);filter:saturate(1.04);}
            .product-img-fallback{
                position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                background:linear-gradient(145deg,#fff8ef,#f4ecdf);
            }
            .product-img-fallback img{width:72px;height:72px;object-fit:contain;opacity:.30;}
            .product-badge{z-index:3;backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);}
            .product-sku{
                position:absolute;right:12px;bottom:12px;z-index:3;
                padding:5px 9px;border-radius:999px;background:rgba(43,39,48,.78);color:#fff;
                font-family:'Fredoka',sans-serif;font-size:.68rem;font-weight:700;letter-spacing:.04em;
                box-shadow:0 5px 14px rgba(43,39,48,.14);
            }
            .product-meta{
                display:flex;align-items:center;justify-content:space-between;gap:8px;
                margin:8px 0 8px;font-size:.76rem;font-weight:800;color:var(--ink-soft);
            }
            .product-rating{color:#D48D00;white-space:nowrap;}
            .product-sold{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-align:right;}
            .product-info .price{font-size:1rem;margin:0 0 12px;}
            .product-shop-btn{display:flex!important;gap:7px;}
            .product-shop-btn svg{width:15px;height:15px;transition:transform .25s var(--ease-premium);}
            .product-card:hover .product-shop-btn svg{transform:translateX(3px);}

            .size-strip{gap:13px;}
            .size-tag{
                min-width:112px;
                padding:18px 20px 16px;
                background:rgba(255,248,238,.78);
                border:1.5px dashed rgba(43,39,48,.19);
                box-shadow:0 7px 18px rgba(43,39,48,.04);
            }
            .size-tag:after{border-width:1.5px;}

            .steps-grid{gap:18px;}
            .step-card{border-radius:24px;padding:30px 26px;box-shadow:0 10px 30px rgba(43,39,48,.05);}
            .step-card:hover{transform:translateY(-5px);box-shadow:var(--shadow-card);}
            .step-num{box-shadow:0 8px 18px rgba(255,120,99,.22);}

            .faq-list{max-width:780px;gap:10px;}
            .faq-item{border-radius:18px;background:rgba(255,255,255,.88);}
            .faq-item summary{padding:20px 22px;}
            .faq-item:hover{transform:none;box-shadow:0 10px 26px rgba(43,39,48,.06);}
            .faq-item[open]{box-shadow:0 12px 30px rgba(255,120,99,.08);}

            .testimonial{padding:8px 0 86px;}
            .quote-card{
                max-width:820px;
                border-radius:32px;
                padding:52px 52px;
                box-shadow:0 24px 60px rgba(43,39,48,.18);
            }
            .quote-card:hover{transform:translateY(-4px);}

            footer{padding-top:64px;}
            .footer-grid{gap:42px;}
            .footer-grid a{transition:opacity .2s ease,transform .2s ease;display:inline-block;}
            .footer-grid a:hover{transform:translateX(3px);}
            .float-contact{right:24px;bottom:24px;width:58px;height:58px;}

            /* Softer interactions; original JS remains intact */
            .tilt-card{transition:transform .22s ease,box-shadow .32s ease;}
            .tilt-card::after{background:radial-gradient(circle at var(--mx) var(--my),rgba(255,255,255,.22),transparent 48%);}

            @media(max-width:1050px){
                .nav-links{gap:2px;}
                .nav-links a{padding-left:9px;padding-right:9px;}
                .hero-grid{gap:38px;}
            }

            @media(max-width:900px){
                header{padding-top:8px;}
                .wrap{padding-left:20px;padding-right:20px;}
                .nav{min-height:62px;padding:8px 9px 8px 13px;border-radius:18px;}
                .nav-links{display:none;}
                .nav-actions{display:none;}
                .mobile-menu-toggle{display:flex;}
                .hero{padding:58px 0 54px;}
                .hero-grid{gap:34px;}
                .hero h1{max-width:13ch;font-size:clamp(2.5rem,8vw,3.65rem);}
                .hero-art{height:370px;max-width:580px;width:100%;margin:8px auto 0;}
                .hero-art::before{inset:18px 8px 12px 12px;}
                .art-card.c1{width:165px;left:36px;top:34px;}
                .art-card.c2{width:155px;right:28px;bottom:28px;}
                .art-card.c3{width:108px;right:42%;top:142px;}
                .hero-float-label.one{left:4px;bottom:62px;}
                .hero-float-label.two{right:2px;top:58px;}
                .stats-grid .stat{border-right:none;}
                .feature-grid{grid-template-columns:repeat(2,1fr);}
                .product-card{width:210px;}
                .product-thumb{height:160px;}
                .quote-card{padding:42px 32px;}
            }

            @media(max-width:680px){
                .nav-actions{display:none;}
                .logo{font-size:1.12rem;}
                .logo-mark{width:36px;height:36px;}
                .hero{padding-top:44px;}
                .hero h1{font-size:clamp(2.35rem,11vw,3.15rem);max-width:12ch;}
                .hero p.lede{font-size:1rem;line-height:1.68;}
                .hero-ctas{display:grid;grid-template-columns:1fr;}
                .hero-ctas .btn{width:100%;}
                .trust-row{display:grid;grid-template-columns:1fr;gap:8px;}
                .trust-item{width:fit-content;}
                .hero-art{height:330px;}
                .art-card.c1{width:142px;left:24px;top:38px;}
                .art-card.c2{width:138px;right:18px;bottom:26px;}
                .art-card.c3{width:92px;right:39%;top:130px;}
                .hero-float-label{font-size:.68rem;padding:8px 10px;}
                .hero-float-label.one{bottom:44px;}
                .hero-float-label.two{top:40px;}
                .stats-bar{padding:28px 0;}
                .stats-grid{grid-template-columns:repeat(2,1fr);gap:18px 8px;}
                .features,.categories,.bestseller,.size-section,.steps{padding-top:64px;padding-bottom:66px;}
                .section-head{margin-bottom:32px;}
                .feature-grid{grid-template-columns:1fr;}
                .cat-card{min-height:232px;}
                .size-strip{display:grid;grid-template-columns:repeat(2,1fr);}
                .size-tag{min-width:0;}
                .quote-card{padding:38px 24px;border-radius:26px;}
                .quote-card p.quote{font-size:1.08rem;}
                .float-contact{right:16px;bottom:16px;width:54px;height:54px;}
            }

            @media(max-width:420px){
                .wrap{padding-left:16px;padding-right:16px;}
                .hero-art{height:305px;}
                .art-card.c1{width:128px;left:18px;}
                .art-card.c2{width:126px;right:14px;}
                .art-card.c3{width:82px;right:38%;top:122px;}
                .hero-float-label.two{right:-2px;}
            }

            @media(prefers-reduced-motion:reduce){
                .mobile-nav-panel.is-open{animation:none !important;}
                .hero-art{transform:none !important;}
            }

            @media(max-width:560px){
                .product-card{width:78vw;max-width:270px;}
                .product-thumb{height:215px;}
                .product-info h3{min-height:auto;font-size:.95rem;}
                .product-meta{font-size:.73rem;}
            }

            /* Final responsive overrides for product catalog */
            @media(max-width:900px){
                .our-products{padding:66px 0;}
                .our-products-head{align-items:flex-start;flex-direction:column;}
                .our-products-title{margin-bottom:0;}
                .our-products-grid{grid-template-columns:repeat(2,minmax(0,1fr));}
            }
            @media(max-width:560px){
                .our-products{padding:60px 0;}
                .our-products-head{gap:18px;}
                .our-products-all{width:100%;}
                .product-filter{gap:7px;overflow-x:auto;flex-wrap:nowrap;padding-bottom:5px;scrollbar-width:none;}
                .product-filter::-webkit-scrollbar{display:none;}
                .product-filter-btn{flex:0 0 auto;padding:9px 13px;font-size:.75rem;}
                .our-products-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;}
                .our-product-card{border-radius:17px;}
                .our-product-info{padding:12px 11px 13px;}
                .our-product-type{font-size:.59rem;}
                .our-product-info h3{font-size:.77rem;min-height:3.15em;margin-bottom:8px;}
                .our-product-info .product-meta{font-size:.65rem;margin-bottom:10px;gap:6px;}
                .our-product-sold{display:none;}
                .our-product-badge{left:8px;top:8px;font-size:.57rem;padding:4px 7px;}
                .our-product-sku{right:8px;top:8px;font-size:.56rem;padding:4px 6px;}
                .our-product-bottom{padding-top:10px;}
                .our-product-price{font-size:.85rem;}
                .our-product-price small{font-size:.56rem;}
                .our-product-arrow{width:31px;height:31px;}
                .our-product-arrow svg{width:14px;height:14px;}
                .our-products-note{align-items:flex-start;padding:14px 15px;font-size:.76rem;}
                .cat-product-card{min-height:260px;}
                .cat-product-content{padding:22px;}
                .cat-product-content h3{font-size:1.55rem;}
            }

        

            /* =========================================================
               HERO PRODUCT SHOWCASE + MOBILE UX v3
               Product-first hero; compact, cleaner mobile experience.
            ========================================================= */
            .hero-products-showcase{overflow:visible;}
            .hero-products-showcase .blob{pointer-events:none;}
            .hero-product-card{
                position:absolute;z-index:6;display:block;overflow:hidden;text-decoration:none;color:inherit;
                background:#fff;border:1px solid rgba(43,39,48,.07);border-radius:28px;
                box-shadow:0 22px 54px rgba(43,39,48,.15);
                transition:transform .4s var(--ease-premium),box-shadow .4s ease,filter .35s ease;
                isolation:isolate;
            }
            .hero-product-card::after{
                content:"";position:absolute;inset:0;z-index:2;pointer-events:none;
                background:linear-gradient(180deg,transparent 48%,rgba(20,17,23,.34) 100%);
            }
            .hero-product-card img{
                width:100%;height:100%;object-fit:cover;display:block;
                transition:transform .65s var(--ease-premium),filter .4s ease;
            }
            .hero-product-card:hover{box-shadow:0 30px 68px rgba(43,39,48,.20);filter:saturate(1.03);}
            .hero-product-card:hover img{transform:scale(1.045);}
            .hero-product-main{
                width:250px;height:338px;right:26px;top:50px;
                transform:rotate(3deg);
            }
            .hero-product-main:hover{transform:translateY(-9px) rotate(1deg);}
            .hero-product-mini{
                width:158px;height:192px;border-radius:23px;
                box-shadow:0 16px 38px rgba(43,39,48,.13);
            }
            .hero-product-mini-one{left:22px;top:43px;transform:rotate(-7deg);}
            .hero-product-mini-one:hover{transform:translateY(-7px) rotate(-4deg);}
            .hero-product-mini-two{left:47px;bottom:28px;transform:rotate(5deg);}
            .hero-product-mini-two:hover{transform:translateY(-7px) rotate(2deg);}
            .hero-product-badge{
                position:absolute;top:13px;left:13px;z-index:4;
                padding:7px 10px;border-radius:999px;background:var(--coral);color:#fff;
                font-size:.61rem;font-weight:1000;letter-spacing:.07em;
                box-shadow:0 7px 18px rgba(255,120,99,.28);
            }
            .hero-product-copy{
                position:absolute;left:15px;right:15px;bottom:14px;z-index:4;color:#fff;
                display:flex;align-items:flex-end;justify-content:space-between;gap:10px;
                text-shadow:0 2px 12px rgba(0,0,0,.28);
            }
            .hero-product-copy b{font-family:'Fredoka',sans-serif;font-size:1rem;letter-spacing:.02em;}
            .hero-product-copy small{font-size:.69rem;font-weight:900;text-align:right;}
            .hero-product-copy.compact{left:11px;right:11px;bottom:10px;display:block;}
            .hero-product-copy.compact b,.hero-product-copy.compact small{display:block;text-align:left;}
            .hero-product-copy.compact b{font-size:.82rem;}
            .hero-product-copy.compact small{font-size:.59rem;margin-top:1px;opacity:.92;}
            .hero-products-showcase .hero-float-label.one{left:auto;right:5px;bottom:22px;z-index:9;}
            .hero-products-showcase .hero-float-label.two{right:0;top:22px;z-index:9;}

            /* Keep the mobile dropdown floating instead of pushing the whole page down. */
            @media(max-width:900px){
                header{padding-bottom:0;}
                .mobile-menu-wrap{height:0;z-index:80;}
                .mobile-nav-panel{
                    position:absolute;left:20px;right:20px;top:8px;margin:0;
                    box-shadow:0 22px 54px rgba(43,39,48,.18);
                }
            }

            @media(max-width:680px){
                body{background:var(--cream);}
                header{top:0;padding-top:6px;background:linear-gradient(to bottom,rgba(255,248,238,.96),rgba(255,248,238,.72),transparent);}
                .wrap{padding-left:16px;padding-right:16px;}
                .nav{min-height:58px;padding:7px 8px 7px 11px;border-radius:17px;}
                .logo{font-size:1.06rem;gap:8px;}
                .logo-mark{width:34px;height:34px;}
                .mobile-menu-toggle{width:40px;height:40px;border-radius:12px;}
                .mobile-menu-toggle svg{width:20px;height:20px;}
                .mobile-nav-panel{
                    left:16px;right:16px;padding:10px;border-radius:18px;
                    grid-template-columns:repeat(2,minmax(0,1fr));gap:6px!important;
                }
                .mobile-nav-panel a:not(.btn){padding:11px 10px;text-align:center;font-size:.82rem;background:rgba(255,248,238,.55);}
                .mobile-nav-panel .btn{grid-column:1/-1;min-height:44px;margin-top:2px;}

                .hero{padding:38px 0 44px;}
                .hero-grid{display:block;}
                .hero-grid>div:first-child{text-align:center;}
                .hero .eyebrow{margin:0 auto 17px;font-size:.69rem;padding:7px 11px;max-width:max-content;}
                .hero h1{
                    max-width:11.6ch;margin:0 auto;font-size:clamp(2.25rem,11.8vw,3rem);
                    line-height:1.02;letter-spacing:-.045em;
                }
                .hero p.lede{max-width:34em;margin:18px auto 22px;font-size:.94rem;line-height:1.62;}
                .hero-ctas{max-width:390px;margin:0 auto 18px;gap:9px;}
                .hero-ctas .btn{min-height:46px;font-size:.87rem;padding:11px 17px;}
                .trust-row{display:flex;justify-content:center;gap:7px;flex-wrap:wrap;}
                .trust-item{width:auto;font-size:.69rem;padding:7px 9px;border-radius:999px;}
                .trust-item .dot{width:6px;height:6px;}

                .hero-art.hero-products-showcase{height:355px;margin:26px auto 0;max-width:430px;}
                .hero-art::before{inset:18px 3px 14px 3px;border-radius:31px;}
                .hero-art::after{inset:35px 18px 30px 18px;border-radius:25px;}
                .hero-products-showcase .blob-sky{width:210px;height:210px;right:-20px;top:15px;}
                .hero-products-showcase .blob-sunshine{width:150px;height:150px;left:-10px;bottom:12px;}
                .hero-product-main{
                    width:61%;height:auto;aspect-ratio:4/5;right:3%;top:31px;border-radius:24px;
                    transform:rotate(2deg);
                }
                .hero-product-mini{width:34%;height:auto;aspect-ratio:1/1.14;border-radius:19px;}
                .hero-product-mini-one{left:3%;top:42px;transform:rotate(-5deg);}
                .hero-product-mini-two{left:8%;bottom:20px;transform:rotate(4deg);}
                .hero-product-badge{top:9px;left:9px;padding:5px 8px;font-size:.52rem;}
                .hero-product-copy{left:11px;right:11px;bottom:10px;}
                .hero-product-copy b{font-size:.82rem;}
                .hero-product-copy small{font-size:.56rem;}
                .hero-products-showcase .hero-float-label{font-size:.60rem;padding:7px 9px;}
                .hero-products-showcase .hero-float-label.two{right:0;top:10px;}
                .hero-products-showcase .hero-float-label.one{right:0;bottom:8px;}

                .stats-bar{padding:22px 0;}
                .stats-grid{gap:13px 4px;}
                .stats-grid .stat{padding:7px 5px;}
                .stats-grid .stat b{font-size:1.35rem;}
                .stats-grid .stat span{font-size:.66rem;}

                .features,.categories,.bestseller,.size-section,.steps{padding-top:54px;padding-bottom:56px;}
                .our-products{padding-top:56px;padding-bottom:56px;}
                .faq{padding-top:18px;padding-bottom:58px;}
                .section-head{margin-bottom:27px;}
                .section-head .kicker{font-size:.67rem;padding:6px 10px;}
                .section-head h2{font-size:1.72rem;line-height:1.08;}
                .section-head p{font-size:.88rem;line-height:1.58;}

                .cat-grid{gap:12px;}
                .cat-product-card{min-height:225px;border-radius:22px;}
                .cat-product-content{padding:19px;}
                .cat-product-content h3{font-size:1.38rem;}
                .cat-product-content .cat-link{font-size:.76rem;margin-top:9px;}

                .carousel-track{gap:12px;padding-left:1px;scroll-padding-left:1px;}
                .product-card{width:76vw;max-width:278px;border-radius:20px;}
                .product-thumb{height:auto;aspect-ratio:1/1;}
                .product-info{padding:14px 14px 15px;}

                .catalog-toolbar{gap:10px;margin-bottom:20px;}
                .product-filter{width:100%;padding-bottom:6px;}
                .catalog-count{padding:8px 11px;font-size:.69rem;}
                .our-products-grid{gap:9px;}
                .our-product-image{aspect-ratio:1/1.06;}
                .our-product-info{padding:11px 10px 12px;}
                .our-product-info h3{font-size:.75rem;line-height:1.32;min-height:3.9em;}
                .our-product-price{font-size:.82rem;}
                .our-product-arrow{width:30px;height:30px;}
                .catalog-loadmore{width:100%;max-width:360px;min-width:0;}

                .size-strip{gap:9px;}
                .size-tag{padding:15px 10px 13px;border-radius:13px;}
                .size-tag .age{font-size:1.08rem;}
                .size-tag .yr{font-size:.62rem;}
                .quote-card{padding:32px 20px;border-radius:24px;}
                .footer-grid{gap:25px;}
                footer{padding-top:46px;}
            }

            @media(max-width:390px){
                .hero{padding-top:32px;}
                .hero h1{font-size:2.18rem;}
                .hero p.lede{font-size:.89rem;}
                .hero-art.hero-products-showcase{height:325px;}
                .hero-product-main{width:62%;top:28px;}
                .hero-product-mini{width:33%;}
                .hero-product-mini-one{top:39px;}
                .hero-product-mini-two{bottom:18px;}
                .hero-products-showcase .hero-float-label.one{display:none;}
                .our-product-badge{max-width:72px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
                .our-product-info h3{font-size:.72rem;}
            }

        </style>
    </head>
    <body>

        <div id="scroll-progress" aria-hidden="true"></div>
        <div id="ambient-cursor-glow" aria-hidden="true"></div>

        <header>
            <div class="wrap nav">
                <div class="logo">
                    <span class="logo-mark"><img src="{{ asset('images/logo-icon.png') }}" alt="Second Star"></span>
                    Second Star
                </div>
                <nav class="nav-links">
                    <a href="#koleksi">Koleksi</a>
                    <a href="#produk">Produk</a>
                    <a href="#ukuran">Ukuran</a>
                    <a href="#faq">FAQ</a>
                    <a href="#kontak">Kontak</a>
                </nav>
                <button type="button" class="mobile-menu-toggle" id="mobile-menu-toggle" aria-expanded="false" aria-controls="mobile-nav-panel" aria-label="Buka menu navigasi">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </button>
                <div class="nav-actions">
                    <a href="https://shopee.co.id/secondstar.store" target="_blank" rel="noopener" class="btn btn-coral btn-sm">Belanja di Shopee</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-outline btn-sm">Dashboard</a>
                        @else
                        @endauth
                    @endif
                </div>
            </div>
            <div class="wrap mobile-menu-wrap">
                <nav class="mobile-nav-panel" id="mobile-nav-panel" aria-label="Navigasi mobile">
                    <a href="#koleksi">Koleksi</a>
                    <a href="#produk">Produk</a>
                    <a href="#ukuran">Ukuran</a>
                    <a href="#faq">FAQ</a>
                    <a href="#kontak">Kontak</a>
                    <a href="https://shopee.co.id/secondstar.store" target="_blank" rel="noopener" class="btn btn-coral">Belanja di Shopee</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-outline">Dashboard</a>
                        @endauth
                    @endif
                </nav>
            </div>
        </header>

        <section class="hero" id="home">
            <div class="hero-grid-bg" aria-hidden="true"></div>
            <div class="hero-orb hero-orb-1" aria-hidden="true"></div>
            <div class="hero-orb hero-orb-2" aria-hidden="true"></div>
            <div class="hero-orb hero-orb-3" aria-hidden="true"></div>
            <div class="wrap hero-grid">
                <div>
                    <span class="eyebrow">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;color:var(--coral);"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Brand lokal Bandung, original Second Star
                    </span>
                    <h1>Gaya keren anak masa kini, <span class="accent">nyaman</span> buat gerak seharian</h1>
                    <p class="lede">Second Star menghadirkan kaos, hoodie, dan bundling outfit distro untuk anak laki-laki &amp; perempuan usia 1-12 tahun. Bahan cotton carded, sablon adem.</p>
                    <div class="hero-ctas">
                        <a href="https://shopee.co.id/secondstar.store" target="_blank" rel="noopener" class="btn btn-coral">Belanja di ShopeeMall</a>
                        <a href="#ukuran" class="btn btn-outline">Lihat Panduan Ukuran</a>
                    </div>
                    <div class="trust-row">
                        <div class="trust-item"><span class="dot"></span>Bahan cotton carded</div>
                        <div class="trust-item"><span class="dot"></span>Proses same-day sebelum 15.00 WIB</div>
                    </div>
                </div>

                <div class="hero-art hero-products-showcase">
                    <div class="blob blob-sky"></div>
                    <div class="blob blob-sunshine"></div>

                    <span class="hero-float-label one">Original Second Star</span>
                    <span class="hero-float-label two">Ukuran 1–12 Tahun</span>

                    <a href="https://shopee.co.id/Second-Star-Atasan-Kaos-Anak-Laki-Laki-Perempuan-Atletics-Tee-1-12-Tahun-SSA154-i.352070638.43365410329"
                       target="_blank" rel="noopener noreferrer"
                       class="hero-product-card hero-product-main" aria-label="Lihat SSA154 Athletics Tee di Shopee">
                        <img src="https://down-id.img.susercontent.com/file/id-11134207-81ztk-mee2ky2kuyv8e8"
                             alt="Second Star SSA154 Athletics Tee" loading="eager" fetchpriority="high">
                        <span class="hero-product-badge">BEST SELLER</span>
                        <span class="hero-product-copy">
                            <b>SSA154</b>
                            <small>Athletics Tee</small>
                        </span>
                    </a>

                    <a href="https://shopee.co.id/-Free-Stiker-Nama-Second-Star-Atasan-Kaos-Anak-Usia-1-12-Tahun-Motif-Gen-Alpha-SSA176-i.352070638.56166337960"
                       target="_blank" rel="noopener noreferrer"
                       class="hero-product-card hero-product-mini hero-product-mini-one" aria-label="Lihat SSA176 Gen Alpha di Shopee">
                        <img src="https://down-id.img.susercontent.com/file/id-11134207-81zto-ms403orfpcsh85"
                             alt="Second Star SSA176 Gen Alpha" loading="eager">
                        <span class="hero-product-copy compact">
                            <b>SSA176</b><small>Gen Alpha</small>
                        </span>
                    </a>

                    <a href="https://shopee.co.id/Second-Star-Baju-Kaos-Distro-Anak-Laki-laki-dan-Perempuan-Umur-1-12-Tahun-Bahan-Katun-carded-30s-SSA100-i.352070638.25131279484"
                       target="_blank" rel="noopener noreferrer"
                       class="hero-product-card hero-product-mini hero-product-mini-two" aria-label="Lihat SSA100 di Shopee">
                        <img src="https://down-id.img.susercontent.com/file/id-11134207-8224s-mhrgujakirk6b1"
                             alt="Second Star SSA100 Kaos Distro Anak" loading="eager">
                        <span class="hero-product-copy compact">
                            <b>SSA100</b><small>10RB+ Terjual</small>
                        </span>
                    </a>
                </div>
            </div>
        </section>

        <section class="stats-bar">
            <div class="wrap stats-grid">
                <div class="stat"><b>26K+</b><span>Followers Instagram</span></div>
                <div class="stat"><b>800+</b><span>Konten &amp; Update Produk</span></div>
                <div class="stat"><b>1–12</b><span>Tahun, Ukuran Lengkap</span></div>
                <div class="stat"><b>100%</b><span>Original Second Star</span></div>
            </div>
        </section>

        <div class="zigzag zigzag-ink-to-cream"></div>

        <section class="features">
            <div class="wrap">
                <div class="section-head">
                    <span class="kicker">Kenapa Second Star</span>
                    <h2>Distro anak original, dipilih langsung dari Bandung</h2>
                    <p>Setiap produk melewati pengecekan bahan dan sablon supaya nyaman dipakai anak yang aktif bergerak seharian.</p>
                </div>
                <div class="feature-grid">
                    <div class="feature-card tilt-card">
                        <div class="feature-icon" style="background:var(--coral);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                        </div>
                        <h3>Cotton Carded</h3>
                        <p>Bahan adem dan menyerap keringat, sablon tidak mudah retak walau sering dicuci.</p>
                    </div>
                    <div class="feature-card tilt-card">
                        <div class="feature-icon" style="background:var(--sky-deep);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        </div>
                        <h3>Motif Distro Original</h3>
                        <p>Desain khas Second Star, dari motif kendaraan sampai gaya kasual kekinian.</p>
                    </div>
                    <div class="feature-card tilt-card">
                        <div class="feature-icon" style="background:var(--mint-deep);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                        </div>
                        <h3>Ukuran 1-12 Tahun</h3>
                        <p>Tersedia lengkap untuk anak laki-laki &amp; perempuan, dengan panduan ukuran di tiap produk.</p>
                    </div>
                    <div class="feature-card tilt-card">
                        <div class="feature-icon" style="background:var(--sunshine-deep);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16V6a1 1 0 011-1h5a1 1 0 011 1v10m-7 0h8m-8 0a2 2 0 11-4 0 2 2 0 014 0zm8 0a2 2 0 104 0m-4 0a2 2 0 014 0m0 0h1.05a2.5 2.5 0 002.45-2v-3a1 1 0 00-.293-.707l-2.5-2.5A1 1 0 0018 7.5H15"/></svg>
                        </div>
                        <h3>Ecer &amp; Grosir</h3>
                        <p>Order sebelum jam 15.00 WIB diproses hari yang sama. Terima pembelian satuan maupun partai.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="categories" id="koleksi">
            <div class="wrap">
                <div class="section-head">
                    <span class="kicker">Koleksi</span>
                    <h2>Pilih koleksi favorit si kecil</h2>
                    <p>Mulai dari kaos distro, celana nyaman, sampai paket hemat. Semua memakai produk asli dari etalase Second Star.</p>
                </div>
                <div class="cat-grid cat-product-grid">
                    <a href="https://shopee.co.id/Second-Star-Baju-Kaos-Distro-Anak-Laki-laki-dan-Perempuan-Umur-1-12-Tahun-Bahan-Katun-carded-30s-SSA100-i.352070638.25131279484" target="_blank" rel="noopener noreferrer" class="cat-card cat-product-card tilt-card">
                        <img class="cat-product-img" src="https://down-id.img.susercontent.com/file/id-11134207-8224s-mhrgujakirk6b1" alt="Kaos Distro Anak Second Star SSA100" loading="lazy" referrerpolicy="no-referrer">
                        <div class="cat-product-overlay"></div>
                        <div class="cat-product-content">
                            <div class="cat-tag">KAOS DISTRO · SSA100</div>
                            <h3>Kaos Anak</h3>
                            <span class="cat-link">Mulai Rp28.999 <b>→</b></span>
                        </div>
                    </a>
                    <a href="https://shopee.co.id/Celana-Cargo-Pendek-Anak-Laki-laki-Perempuan-1-10-tahun-Premium-Matterial-Dusky-Crinkle-SSC15-i.352070638.25734095773" target="_blank" rel="noopener noreferrer" class="cat-card cat-product-card tilt-card">
                        <img class="cat-product-img" src="https://down-id.img.susercontent.com/file/id-11134207-7rasl-m55b9ca8sweuf7" alt="Celana Cargo Pendek Anak Second Star SSC15" loading="lazy" referrerpolicy="no-referrer">
                        <div class="cat-product-overlay"></div>
                        <div class="cat-product-content">
                            <div class="cat-tag">CELANA CARGO · SSC15</div>
                            <h3>Celana Anak</h3>
                            <span class="cat-link">Mulai Rp38.542 <b>→</b></span>
                        </div>
                    </a>
                    <a href="https://shopee.co.id/PAKET-HEMAT-1-5-KAOS-ANAK-PREMIUM-KATUN-30S-SIZE-S-i.352070638.52752073542" target="_blank" rel="noopener noreferrer" class="cat-card cat-product-card tilt-card">
                        <img class="cat-product-img" src="https://down-id.img.susercontent.com/file/id-11134207-82250-mhhi3zmomccg58" alt="Paket Hemat Kaos Anak Premium Second Star" loading="lazy" referrerpolicy="no-referrer">
                        <div class="cat-product-overlay"></div>
                        <div class="cat-product-content">
                            <div class="cat-tag">PAKET 1–5 KAOS</div>
                            <h3>Paket Hemat</h3>
                            <span class="cat-link">Mulai Rp15.999 <b>→</b></span>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <div class="zigzag zigzag-cream-to-paper"></div>

        <section class="bestseller">
            <div class="wrap">
                <div class="section-head">
                    <span class="kicker">Favorit Momstar</span>
                    <h2>Produk Best Seller</h2>
                    <p>Pilihan produk favorit dengan gaya distro yang nyaman dipakai anak untuk aktivitas sehari-hari.</p>
                </div>

                <div class="carousel-wrap">
                    <button type="button" id="bs-prev" class="carousel-nav prev" aria-label="Sebelumnya">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" id="bs-next" class="carousel-nav next" aria-label="Selanjutnya">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>

                    @php
                        /*
                         * 30 produk unik yang diambil dari listing Second Star Official Shop di Shopee.
                         * Setiap kartu memakai URL produk + foto milik listing produk tersebut; tidak ada rotasi gambar representatif.
                         * Harga adalah snapshot saat data diperbarui dan dapat berubah di Shopee.
                         */
                        $catalogProducts = [
                            [
                                'sku' => 'SSA154',
                                'category' => 'kaos',
                                'name' => 'Atletics Tee Anak Laki-Laki & Perempuan 1–12 Tahun',
                                'price' => 'Rp31.120',
                                'rating' => null,
                                'sold' => 'Best Seller',
                                'badge' => 'Best Seller',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81ztk-mee2ky2kuyv8e8',
                                'url' => 'https://shopee.co.id/Second-Star-Atasan-Kaos-Anak-Laki-Laki-Perempuan-Atletics-Tee-1-12-Tahun-SSA154-i.352070638.43365410329',
                            ],
                            [
                                'sku' => 'SSA176',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Motif Gen Alpha + Free Stiker Nama',
                                'price' => 'Rp28.999',
                                'rating' => '4.8',
                                'sold' => '35 terjual',
                                'badge' => 'Top Product',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81zto-ms403orfpcsh85',
                                'url' => 'https://shopee.co.id/-Free-Stiker-Nama-Second-Star-Atasan-Kaos-Anak-Usia-1-12-Tahun-Motif-Gen-Alpha-SSA176-i.352070638.56166337960',
                            ],
                            [
                                'sku' => 'SS SMILE',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Motif SS SMILE Angka + Free Stiker Nama',
                                'price' => 'Rp28.999',
                                'rating' => null,
                                'sold' => 'Terlaris Shopee',
                                'badge' => 'Top Product',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81ztn-mr148cjqqcjlfc',
                                'url' => 'https://shopee.co.id/-Free-Stiker-Nama-Second-Star-Kaos-Anak-Motif-SS-SMILE-Angka-Usia-1-12-Tahun-Laki-laki-dan-Perempuan-i.352070638.44332946147',
                            ],
                            [
                                'sku' => 'SSA178',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Motif Motorcycle Touring + Free Stiker Nama',
                                'price' => 'Rp28.999',
                                'rating' => null,
                                'sold' => 'Terlaris Shopee',
                                'badge' => 'Top Product',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81zto-mscf8bx9n6yt42',
                                'url' => 'https://shopee.co.id/-Free-Stiker-Nama-Second-Star-Atasan-Kaos-Anak-Usia-1-12-Tahun-Motif-Motorcycle-Touring-SSA178-i.352070638.42783844769',
                            ],
                            [
                                'sku' => 'SSA175',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Motif Motorcycle + Free Stiker Nama',
                                'price' => 'Rp28.999',
                                'rating' => null,
                                'sold' => 'Terlaris Shopee',
                                'badge' => 'Top Product',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81ztf-ms3utkmoy5fr94',
                                'url' => 'https://shopee.co.id/-Free-Stiker-Nama-Second-Star-Atasan-Kaos-Anak-Usia-1-12-Tahun-Motif-Motorcycle-SSA175-i.352070638.53916331419',
                            ],
                            [
                                'sku' => 'SSA155',
                                'category' => 'kaos',
                                'name' => 'Atletics Be Star Tee Anak 1–12 Tahun',
                                'price' => 'Rp38.734',
                                'rating' => null,
                                'sold' => 'Terlaris Shopee',
                                'badge' => 'Top Product',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81ztl-mrv8xnm0eebsf1',
                                'url' => 'https://shopee.co.id/Second-Star-Atasan-Kaos-Anak-Laki-Laki-Perempuan-Atletics-Be-Star-1-12-Tahun-SSA155-i.352070638.25348408197',
                            ],
                            [
                                'sku' => 'SSA100',
                                'category' => 'kaos',
                                'name' => 'Kaos Distro Anak Cotton Carded 30s',
                                'price' => 'Rp28.999',
                                'rating' => '4.9',
                                'sold' => '10RB+ terjual',
                                'badge' => 'Terlaris',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-8224s-mhrgujakirk6b1',
                                'url' => 'https://shopee.co.id/Second-Star-Baju-Kaos-Distro-Anak-Laki-laki-dan-Perempuan-Umur-1-12-Tahun-Bahan-Katun-carded-30s-SSA100-i.352070638.25131279484',
                            ],
                            [
                                'sku' => 'SSA174',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Motif Naykila So Cute + Free Stiker Nama',
                                'price' => 'Rp28.999',
                                'rating' => null,
                                'sold' => 'Terlaris Shopee',
                                'badge' => 'Top Product',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81zth-ms3ykwr3i03m84',
                                'url' => 'https://shopee.co.id/-Free-Stiker-Nama-Second-Star-Atasan-Kaos-Anak-Usia-1-12-Tahun-Motif-Naykila-So-Cute-SSA174-i.352070638.47966353450',
                            ],
                            [
                                'sku' => 'SSA173',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Motif My Mine Gweh + Free Stiker Nama',
                                'price' => 'Rp28.999',
                                'rating' => null,
                                'sold' => 'Terlaris Shopee',
                                'badge' => 'Top Product',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81ztg-ms2443lf45j8b4',
                                'url' => 'https://shopee.co.id/-Free-Stiker-Nama-Second-Star-Atasan-Kaos-Anak-Usia-1-12-Tahun-Motif-My-Mine-Gweh-SSA173-i.352070638.57216245018',
                            ],
                            [
                                'sku' => 'DTF BEAR',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Motif DTF Bordir Beruang + Free Stiker Nama',
                                'price' => 'Rp31.120',
                                'rating' => null,
                                'sold' => 'Terlaris Shopee',
                                'badge' => 'Top Product',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81ztc-mqr5n9s3noqr23',
                                'url' => 'https://shopee.co.id/-Free-Stiker-Nama-Second-Star-Kaos-Anak-Motif-Dtf-Bordir-Beruang-Usia-1-12-Tahun-Laki-laki-dan-Perempuan-i.352070638.50414163410',
                            ],
                            [
                                'sku' => 'SSA08',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Cotton Carded Motif Damkar',
                                'price' => 'Rp30.898',
                                'rating' => '4.9',
                                'sold' => '10RB+ terjual',
                                'badge' => 'Terlaris',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81ztp-mscls51dnp54cc',
                                'url' => 'https://shopee.co.id/Second-Star-Baju-Kaos-Distro-Anak-Laki-laki-Premium-Umur-1-12-Tahun-Bahan-Cotton-Carded-Motif-Damkar-SSA08-i.352070638.22075189156',
                            ],
                            [
                                'sku' => 'SSC07',
                                'category' => 'celana',
                                'name' => 'Celana Cargo Pendek Anak Diamond Knit',
                                'price' => 'Rp25.627',
                                'rating' => '4.9',
                                'sold' => '10RB+ terjual',
                                'badge' => 'Terlaris',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-7rasf-m4tuiu1jibrq94',
                                'url' => 'https://shopee.co.id/Celana-Cargo-Pendek-Laki-laki-Perempuan-1-10-tahun-Premium-Matterial-Diamond-Knit-Viral-Lucu-SSC07-i.352070638.23659670726',
                            ],
                            [
                                'sku' => 'SSA130',
                                'category' => 'kaos',
                                'name' => 'Clearance Sale Kaos Anak Cotton Carded',
                                'price' => 'Rp23.999',
                                'rating' => '4.9',
                                'sold' => '7RB+ terjual',
                                'badge' => 'Clearance',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81ztp-ms6s4omye4g277',
                                'url' => 'https://shopee.co.id/Clearance-Sale-Second-Star-Kaos-Anak-Karakter-Bahan-Cotton-Carded-Usia-1-12-Tahun-1.0-SSA130-i.352070638.29720044927',
                            ],
                            [
                                'sku' => 'PAKET 1–5',
                                'category' => 'paket',
                                'name' => 'Paket Hemat 1–5 Kaos Anak Premium Katun 30s',
                                'price' => 'Rp15.999',
                                'rating' => '4.7',
                                'sold' => '383 terjual',
                                'badge' => 'Paket Hemat',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-82250-mhhi3zmomccg58',
                                'url' => 'https://shopee.co.id/PAKET-HEMAT-1-5-KAOS-ANAK-PREMIUM-KATUN-30S-SIZE-S-i.352070638.52752073542',
                            ],
                            [
                                'sku' => 'SSC18',
                                'category' => 'celana',
                                'name' => 'Celana Cargo Panjang Anak Dusky Crinkle',
                                'price' => 'Rp43.999',
                                'rating' => '4.9',
                                'sold' => '5RB+ terjual',
                                'badge' => 'Favorit',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-7rbkb-malgcxwphx3t79',
                                'url' => 'https://shopee.co.id/Celana-Cargo-Panjang-Anak-Laki-laki-Perempuan-1-10-tahun-Premium-Material-Dusky-Crinkle-SSC18-i.352070638.28787208808',
                            ],
                            [
                                'sku' => 'SSA52',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Motif Kucing Cute Miow Time',
                                'price' => 'Rp31.120',
                                'rating' => '4.9',
                                'sold' => '8RB+ terjual',
                                'badge' => 'Favorit',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-822wu-mpdeaceo9frgf2',
                                'url' => 'https://shopee.co.id/-Free-Stiker-Nama-Second-Star-Kaos-Anak-Motif-Kucing-Cute-Miow-Time-Umur-1-12-Tahun-Katun-SSA52-i.352070638.19570763003',
                            ],
                            [
                                'sku' => 'SSC17',
                                'category' => 'celana',
                                'name' => 'Celana Panjang Perempuan Motif Serat Kayu Polymicro',
                                'price' => 'Rp28.500',
                                'rating' => '4.7',
                                'sold' => '71 terjual',
                                'badge' => 'New',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81ztc-mrs965n8iwavb2',
                                'url' => 'https://shopee.co.id/Celana-Panjang-Perempuan-Motif-Serat-Kayu-Matterial-Polymicro-1-10-tahun-SSC17-i.352070638.46915878838',
                            ],
                            [
                                'sku' => 'SSA127',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Polos Distro Basic Tee 1–12 Tahun',
                                'price' => 'Rp23.999',
                                'rating' => null,
                                'sold' => 'Official Shop',
                                'badge' => 'Basic',
                                'image' => 'https://down-id.img.susercontent.com/file/sg-11134201-820m4-mo3fuzw6l4hu9a',
                                'url' => 'https://shopee.co.id/Second-Star-Kaos-Anak-Polos-Distro-Kaos-Basic-Baju-Anak-Laki-laki-dan-Perempuan-1-12-tahun-SSA127-i.352070638.27668390817',
                            ],
                            [
                                'sku' => 'SSA169',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Motif Gen Anti Korupsi Duo',
                                'price' => 'Rp31.120',
                                'rating' => null,
                                'sold' => 'Official Shop',
                                'badge' => 'Official',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81ztl-mrnsns61fhmoe7',
                                'url' => 'https://shopee.co.id/-Free-Stiker-Nama-Second-Star-Atasan-Kaos-Anak-Usia-1-12-Tahun-Motif-Gen-Anti-Korupsi-Duo-SSA169-i.352070638.55615691795',
                            ],
                            [
                                'sku' => 'SSA171',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Motif Elephant Be Brave',
                                'price' => 'Rp28.999',
                                'rating' => null,
                                'sold' => 'Official Shop',
                                'badge' => 'Official',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81ztg-mrr3gi7pdkw5cc',
                                'url' => 'https://shopee.co.id/-Free-Stiker-Nama-Second-Star-Atasan-Kaos-Anak-Usia-1-12-Tahun-Motif-Elephant-Be-Brave-SSA171-i.352070638.46065836872',
                            ],
                            [
                                'sku' => 'BUNDLE 6',
                                'category' => 'paket',
                                'name' => 'Paket Bundling Baju Anak 100 Ribu Dapat 6 Pcs',
                                'price' => 'Rp99.999',
                                'rating' => null,
                                'sold' => 'Official Shop',
                                'badge' => 'Bundling',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-822wm-mpuh4ls4ftosea',
                                'url' => 'https://shopee.co.id/SECOND-STAR-Paket-Bundling-Baju-Anak-100-ribu-Dapat-6-Pcs-Laki-laki-dan-Perempuan-i.352070638.56161327929',
                            ],
                            [
                                'sku' => 'SIX SEVEN',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Motif Six Seven + Free Stiker Nama',
                                'price' => 'Rp31.120',
                                'rating' => null,
                                'sold' => 'Official Shop',
                                'badge' => 'Official',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81ztq-msgk8hog969t00',
                                'url' => 'https://shopee.co.id/-Free-Stiker-Nama-Second-Star-Kaos-Anak-Motif-Six-Seven-Usia-1-12-Tahun-Laki-laki-dan-Perempuan-i.352070638.57507828335',
                            ],
                            [
                                'sku' => 'SSA138',
                                'category' => 'kaos',
                                'name' => 'Kidletics Varsity Tee Baseball Anak 1–12 Tahun',
                                'price' => 'Rp33.499',
                                'rating' => null,
                                'sold' => 'Official Shop',
                                'badge' => 'Official',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-7rbk3-m9sl2h7ufmrxbd',
                                'url' => 'https://shopee.co.id/Second-Star-Atasan-Kaos-Anak-Laki-Laki-Perempuan-Kidletics-Varsity-Tee-Baseball-1-12-Tahun-SSA138-i.352070638.27082340158',
                            ],
                            [
                                'sku' => 'SSA121',
                                'category' => 'kaos',
                                'name' => 'Kaos Distro Anak Premium Unik Bahan Cotton',
                                'price' => 'Rp31.120',
                                'rating' => null,
                                'sold' => 'Official Shop',
                                'badge' => 'Official',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-7ras9-m23ikynrlbe532',
                                'url' => 'https://shopee.co.id/Second-Star-Kaos-Distro-Anak-Laki-laki-Premium-Unik-Umur-1-12-Tahun-Bahan-Cotton-SSA121-i.352070638.28265754532',
                            ],
                            [
                                'sku' => 'SSA160',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Polos Distro CombieRib Basic',
                                'price' => 'Rp22.999',
                                'rating' => null,
                                'sold' => 'Official Shop',
                                'badge' => 'Basic',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-822wn-mpkclbdi2134a0',
                                'url' => 'https://shopee.co.id/Second-Star-Kaos-Anak-Polos-Distro-CombieRib-Kaos-Basic-Baju-Anak-Laki-laki-dan-Perempuan-1-12-tahun-SSA160-i.352070638.48511365421',
                            ],
                            [
                                'sku' => 'SSA168',
                                'category' => 'kaos',
                                'name' => 'Kaos Distro Anak Katun 30s 1–12 Tahun',
                                'price' => 'Rp28.570',
                                'rating' => null,
                                'sold' => 'Official Shop',
                                'badge' => 'Official',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-8224t-mkj0kw2ioft000',
                                'url' => 'https://shopee.co.id/Second-Star-Baju-Kaos-Distro-Anak-Laki-laki-dan-Perempuan-Umur-1-12-Tahun-Bahan-Katun-30s-SSA168-i.352070638.40528683907',
                            ],
                            [
                                'sku' => 'SSC15',
                                'category' => 'celana',
                                'name' => 'Celana Cargo Pendek Anak Dusky Crinkle',
                                'price' => 'Rp38.542',
                                'rating' => null,
                                'sold' => 'Official Shop',
                                'badge' => 'Cargo',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-7rasl-m55b9ca8sweuf7',
                                'url' => 'https://shopee.co.id/Celana-Cargo-Pendek-Anak-Laki-laki-Perempuan-1-10-tahun-Premium-Matterial-Dusky-Crinkle-SSC15-i.352070638.25734095773',
                            ],
                            [
                                'sku' => 'SSC16',
                                'category' => 'celana',
                                'name' => 'Celana Panjang Anak Hanami Pants 4–12 Tahun',
                                'price' => 'Rp60.499',
                                'rating' => null,
                                'sold' => 'Official Shop',
                                'badge' => 'Pants',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-8224s-mgvsa6tya2a159',
                                'url' => 'https://shopee.co.id/Second-Star-Celana-Panjang-Anak-Hanami-Pants-4-12-Tahun-SSC16-i.352070638.29321017079',
                            ],
                            [
                                'sku' => 'SSA163',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Polos Distro Basic Tee SSA163',
                                'price' => 'Rp23.999',
                                'rating' => null,
                                'sold' => 'Official Shop',
                                'badge' => 'Basic',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-8224v-mkdb9fotsdfnd5',
                                'url' => 'https://shopee.co.id/Second-Star-Kaos-Anak-Polos-Distro-Kaos-Basic-Baju-Anak-Laki-laki-dan-Perempuan-1-12-tahun-SSA163-i.352070638.54702816918',
                            ],
                            [
                                'sku' => 'SSA166',
                                'category' => 'kaos',
                                'name' => 'Kaos Anak Motif Diamond Gold Lion + Free Stiker Nama',
                                'price' => 'Rp31.120',
                                'rating' => null,
                                'sold' => 'Official Shop',
                                'badge' => 'Official',
                                'image' => 'https://down-id.img.susercontent.com/file/id-11134207-81ztq-mrjeuz4kv6rs0d',
                                'url' => 'https://shopee.co.id/-Free-Stiker-Nama-Second-Star-Atasan-Kaos-Anak-Usia-1-12-Tahun-Motif-Diamond-Gold-Lion-SSA166-i.352070638.47165527334',
                            ],
                        ];

                        // Enam produk teratas untuk carousel Best Seller. SSA154 tetap berada di posisi pertama.
                        $bestSellerProducts = array_slice($catalogProducts, 0, 6);
                    @endphp

                    <div class="carousel-track" id="bs-track">
                        @foreach ($bestSellerProducts as $product)
                            <a href="{{ $product['url'] }}" target="_blank" rel="noopener noreferrer" class="product-card tilt-card" aria-label="Lihat {{ $product['name'] }} di Shopee">
                                <div class="product-thumb product-photo">
                                    <div class="product-img-fallback" aria-hidden="true">
                                        <img src="{{ asset('images/logo-icon.png') }}" alt="">
                                    </div>
                                    <img
                                        class="product-img"
                                        src="{{ $product['image'] }}"
                                        alt="{{ $product['name'] }} - {{ $product['sku'] }}"
                                        loading="lazy"
                                        decoding="async"
                                        referrerpolicy="no-referrer"
                                        onerror="this.style.display='none'"
                                    >
                                    <span class="product-badge">{{ $product['badge'] }}</span>
                                    <span class="product-sku">{{ $product['sku'] }}</span>
                                </div>
                                <div class="product-info">
                                    <h3>{{ $product['name'] }}</h3>
                                    <div class="product-meta">
                                        @if (!empty($product['rating']))
                                            <span class="product-rating" aria-label="Rating {{ $product['rating'] }} dari 5">★ {{ $product['rating'] }}</span>
                                        @endif
                                        @if (!empty($product['sold']))
                                            <span class="product-sold">{{ $product['sold'] }}</span>
                                        @endif
                                    </div>
                                    <div class="price">Mulai {{ $product['price'] }}</div>
                                    <span class="btn btn-outline product-shop-btn">
                                        Lihat di Shopee
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="our-products" id="produk">
            <div class="wrap">
                <div class="our-products-head">
                    <div class="section-head our-products-title">
                        <span class="kicker">Our Products</span>
                        <h2>Koleksi Second Star</h2>
                        <p>30 produk berbeda yang diambil langsung dari listing Second Star Official Shop di Shopee. Tetap di halaman welcome, buka katalog bertahap, lalu filter sesuai kategori.</p>
                    </div>
                    <a href="https://shopee.co.id/secondstar.store" target="_blank" rel="noopener noreferrer" class="btn btn-coral our-products-all">
                        Lihat Semua di Shopee
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                </div>

                <div class="catalog-toolbar">
                    <div class="product-filter" role="group" aria-label="Filter produk">
                        <button type="button" class="product-filter-btn is-active" data-filter="all">Semua</button>
                        <button type="button" class="product-filter-btn" data-filter="kaos">Kaos Anak</button>
                        <button type="button" class="product-filter-btn" data-filter="celana">Celana Anak</button>
                        <button type="button" class="product-filter-btn" data-filter="paket">Paket Hemat</button>
                    </div>
                    <div class="catalog-count" aria-live="polite">
                        Menampilkan <b id="catalog-visible-count">8</b> dari <b id="catalog-total-count">{{ count($catalogProducts) }}</b> produk
                    </div>
                </div>

                <div class="our-products-grid" id="our-products-grid">
                    @foreach ($catalogProducts as $product)
                        <article class="our-product-card" data-category="{{ $product['category'] }}">
                            <a href="{{ $product['url'] }}" target="_blank" rel="noopener noreferrer" class="our-product-image" aria-label="Lihat {{ $product['name'] }} di Shopee">
                                @if (!empty($product['image']))
                                    <div class="product-img-fallback" aria-hidden="true">
                                        <img src="{{ asset('images/logo-icon.png') }}" alt="">
                                    </div>
                                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }} - {{ $product['sku'] }}" loading="lazy" decoding="async" referrerpolicy="no-referrer" onerror="this.style.display='none'">
                                @else
                                    <div class="our-product-placeholder">
                                        <img src="{{ asset('images/logo-icon.png') }}" alt="">
                                        <b>{{ $product['sku'] }}</b>
                                        <small>Second Star Official</small>
                                    </div>
                                @endif
                                <span class="our-product-badge">{{ $product['badge'] }}</span>
                                <span class="our-product-sku">{{ $product['sku'] }}</span>
                            </a>
                            <div class="our-product-info">
                                <div class="our-product-type">{{ $product['category'] === 'celana' ? 'Celana Anak' : ($product['category'] === 'paket' ? 'Paket Hemat' : 'Kaos Anak') }}</div>
                                <h3><a href="{{ $product['url'] }}" target="_blank" rel="noopener noreferrer">{{ $product['name'] }}</a></h3>
                                <div class="product-meta">
                                    @if (!empty($product['rating']))
                                        <span class="product-rating">★ {{ $product['rating'] }}</span>
                                    @endif
                                    @if (!empty($product['sold']))
                                        <span class="product-sold">{{ $product['sold'] }}</span>
                                    @endif
                                </div>
                                <div class="our-product-bottom">
                                    <div class="our-product-price"><small>{{ str_starts_with($product['price'], 'Rp') ? 'Mulai' : 'Harga' }}</small>{{ $product['price'] }}</div>
                                    <a href="{{ $product['url'] }}" target="_blank" rel="noopener noreferrer" class="our-product-arrow" aria-label="Buka produk di Shopee">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="catalog-loadmore-wrap">
                    <button type="button" id="catalog-loadmore" class="btn btn-outline catalog-loadmore">
                        <span id="catalog-loadmore-text">Lihat 8 Produk Lainnya</span>
                        <svg class="catalog-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/></svg>
                    </button>
                </div>

                <div class="our-products-note">
                    <span class="our-products-note-icon">★</span>
                    <div>
                        <strong>30 produk unik dari etalase resmi.</strong>
                        <span>Setiap card memakai foto dan link produk Shopee yang berbeda. Tombol “Lihat Produk Lainnya” tetap membuka katalog bertahap tanpa pindah dari welcome page.</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="size-section" id="ukuran">
            <div class="wrap">
                <div class="section-head">
                    <span class="kicker">Panduan Ukuran</span>
                    <h2>Lengkap untuk usia 1 sampai 12 tahun</h2>
                    <p>Umur yang tertera hanya perkiraan (toleransi ±2-3 cm) — disarankan cek tabel ukuran detail di tiap produk sebelum order, ya Momstar.</p>
                </div>
                <div class="size-strip">
                    <div class="size-tag"><span class="age">1–2</span><span class="yr">TAHUN</span></div>
                    <div class="size-tag"><span class="age">2–3</span><span class="yr">TAHUN</span></div>
                    <div class="size-tag"><span class="age">4–5</span><span class="yr">TAHUN</span></div>
                    <div class="size-tag"><span class="age">6–7</span><span class="yr">TAHUN</span></div>
                    <div class="size-tag"><span class="age">8–9</span><span class="yr">TAHUN</span></div>
                    <div class="size-tag"><span class="age">10–12</span><span class="yr">TAHUN</span></div>
                </div>
            </div>
        </section>

        <div class="zigzag zigzag-paper-to-cream"></div>

        <section class="steps" id="cara-belanja">
            <div class="wrap">
                <div class="section-head">
                    <span class="kicker">Cara Belanja</span>
                    <h2>Gampang, tinggal 3 langkah</h2>
                </div>
                <div class="steps-grid">
                    <div class="step-card tilt-card">
                        <div class="step-num">1</div>
                        <h3>Pilih Produk</h3>
                        <p>Klik "Belanja di ShopeeMall", cari motif dan ukuran yang pas buat si kecil.</p>
                    </div>
                    <div class="step-card tilt-card">
                        <div class="step-num">2</div>
                        <h3>Checkout &amp; Bayar</h3>
                        <p>Order langsung lewat ShopeeMall — aman, ada jaminan original &amp; garansi tepat waktu.</p>
                    </div>
                    <div class="step-card tilt-card">
                        <div class="step-num">3</div>
                        <h3>Diproses &amp; Dikirim</h3>
                        <p>Order sebelum jam 15.00 WIB diproses hari yang sama, dikemas rapi sampai ke rumah.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="faq" id="faq">
            <div class="wrap">
                <div class="section-head">
                    <span class="kicker">FAQ</span>
                    <h2>Pertanyaan yang sering ditanya Momstar</h2>
                </div>
                <div class="faq-list">
                    <details class="faq-item">
                        <summary>Apakah Second Star toko original?
                            <svg class="chev" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/></svg>
                        </summary>
                        <div class="faq-body">Ya, semua produk Second Star original dan dijual resmi lewat ShopeeMall &amp; TikTok Shop — bukan reseller atau tiruan.</div>
                    </details>
                    <details class="faq-item">
                        <summary>Ukurannya gimana kalau ragu pas mana?
                            <svg class="chev" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/></svg>
                        </summary>
                        <div class="faq-body">Cek panduan ukuran di bagian "Ukuran 1-12 Tahun" di atas sebagai patokan awal, lalu lihat tabel ukuran detail di masing-masing produk sebelum order.</div>
                    </details>
                    <details class="faq-item">
                        <summary>Bisa beli grosir/partai buat reseller?
                            <svg class="chev" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/></svg>
                        </summary>
                        <div class="faq-body">Bisa — Second Star melayani pembelian ecer maupun grosir. Chat langsung lewat Instagram atau ShopeeMall buat tanya harga partai.</div>
                    </details>
                    <details class="faq-item">
                        <summary>Kapan pesanan diproses?
                            <svg class="chev" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/></svg>
                        </summary>
                        <div class="faq-body">Order yang masuk sebelum jam 15.00 WIB diproses dan dikirim di hari yang sama.</div>
                    </details>
                </div>
            </div>
        </section>

        <section class="testimonial" id="kata-mereka">
            <div class="wrap">
                <div class="quote-card">
                    <div class="quote-mark">"</div>
                    <div class="stars">★★★★★</div>
                    <p class="quote">Bahannya adem banget dan sablonnya nggak retak walau udah dicuci berkali-kali. Anak saya betah pakai seharian, jadi langganan beli lagi kalau ada motif baru.</p>
                    <div class="quote-by">Momstar, pelanggan Second Star</div>
                </div>
            </div>
        </section>

        <div class="zigzag zigzag-ink-to-cream"></div>

        <footer id="kontak">
            <div class="wrap">
                <div class="footer-grid">
                    <div>
                        <div class="footer-logo"><span class="logo-mark"><img src="{{ asset('images/logo-icon-white.png') }}" alt="Second Star"></span>Second Star</div>
                        <p>Brand lokal Bandung untuk baju anak distro original — kaos, hoodie, dan bundling outfit usia 1-12 tahun.</p>
                    </div>
                    <div>
                        <h4>Belanja</h4>
                        <ul>
                            <li><a href="https://shopee.co.id/secondstar.store" target="_blank" rel="noopener">Anak Laki-laki</a></li>
                            <li><a href="https://shopee.co.id/secondstar.store" target="_blank" rel="noopener">Anak Perempuan</a></li>
                            <li><a href="https://shopee.co.id/secondstar.store" target="_blank" rel="noopener">Paket Bundling</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4>Bantuan</h4>
                        <ul>
                            <li><a href="#ukuran">Panduan Ukuran</a></li>
                            <li><a href="#cara-belanja">Cara Pemesanan</a></li>
                            <li><a href="#faq">Grosir &amp; Reseller</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4>Ikuti Kami</h4>
                        <ul>
                            <li><a href="https://www.instagram.com/secondstar.official/" target="_blank" rel="noopener">Instagram</a></li>
                            <li><a href="https://shopee.co.id/secondstar.store" target="_blank" rel="noopener">ShopeeMall</a></li>
                        </ul>
                    </div>
                </div>
                <div class="footer-bottom">
                    <span>© {{ date('Y') }} Second Star Official Shop. Semua hak dilindungi.</span>
                </div>
            </div>
        </footer>

        <a href="https://www.instagram.com/secondstar.official/" target="_blank" rel="noopener" class="float-contact" aria-label="Chat via Instagram">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="5"/>
                <circle cx="12" cy="12" r="4"/>
                <circle cx="17.2" cy="6.8" r="1" fill="currentColor" stroke="none"/>
            </svg>
        </a>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                /* ---------- scroll progress + header ---------- */
                const progress = document.getElementById('scroll-progress');
                const header = document.querySelector('header');
                const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
                const mobileNavPanel = document.getElementById('mobile-nav-panel');

                if (mobileMenuToggle && mobileNavPanel) {
                    function setMobileMenu(open) {
                        mobileNavPanel.classList.toggle('is-open', open);
                        mobileMenuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                        mobileMenuToggle.setAttribute('aria-label', open ? 'Tutup menu navigasi' : 'Buka menu navigasi');
                    }

                    mobileMenuToggle.addEventListener('click', function () {
                        setMobileMenu(!mobileNavPanel.classList.contains('is-open'));
                    });

                    mobileNavPanel.querySelectorAll('a').forEach(function (link) {
                        link.addEventListener('click', function () {
                            setMobileMenu(false);
                        });
                    });

                    document.addEventListener('keydown', function (event) {
                        if (event.key === 'Escape') setMobileMenu(false);
                    });

                    window.addEventListener('resize', function () {
                        if (window.innerWidth > 900) setMobileMenu(false);
                    }, { passive: true });
                }

                function updateScrollUI() {
                    const scrollTop = window.scrollY;
                    const pageHeight = document.documentElement.scrollHeight - window.innerHeight;
                    const percent = pageHeight > 0 ? (scrollTop / pageHeight) * 100 : 0;

                    if (progress) progress.style.width = percent + '%';
                    if (header) header.classList.toggle('header-scrolled', scrollTop > 18);
                }

                window.addEventListener('scroll', updateScrollUI, { passive: true });
                updateScrollUI();

                /* ---------- ambient cursor ---------- */
                const ambientGlow = document.getElementById('ambient-cursor-glow');
                if (ambientGlow && !reduceMotion && window.matchMedia('(pointer:fine)').matches) {
                    document.addEventListener('mousemove', function (event) {
                        ambientGlow.style.opacity = '1';
                        ambientGlow.style.left = event.clientX + 'px';
                        ambientGlow.style.top = event.clientY + 'px';
                    });
                    document.addEventListener('mouseleave', function () {
                        ambientGlow.style.opacity = '0';
                    });
                }

                /* ---------- hero mouse parallax ---------- */
                const hero = document.querySelector('.hero');
                const heroArt = document.querySelector('.hero-art');

                if (hero && heroArt && !reduceMotion && window.matchMedia('(pointer:fine)').matches) {
                    hero.addEventListener('mousemove', function (event) {
                        const rect = hero.getBoundingClientRect();
                        const x = ((event.clientX - rect.left) / rect.width) - .5;
                        const y = ((event.clientY - rect.top) / rect.height) - .5;

                        heroArt.style.transform =
                            'rotateY(' + (x * 2.2) + 'deg) ' +
                            'rotateX(' + (-y * 1.8) + 'deg) ' +
                            'translate3d(' + (x * 4) + 'px,' + (y * 4) + 'px,0)';
                    });

                    hero.addEventListener('mouseleave', function () {
                        heroArt.style.transform = 'rotateY(0deg) rotateX(0deg) translate3d(0,0,0)';
                    });
                }

                /* ---------- cinematic reveal ---------- */
                const revealTargets = document.querySelectorAll(
                    '.section-head, .feature-card, .cat-card, .product-card, .size-tag, .step-card, .faq-item, .quote-card'
                );

                revealTargets.forEach(function (element, index) {
                    element.classList.add('reveal-init');
                    element.style.transitionDelay = ((index % 4) * 70) + 'ms';
                });

                if ('IntersectionObserver' in window) {
                    const revealObserver = new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (!entry.isIntersecting) return;
                            entry.target.classList.add('is-visible');
                            revealObserver.unobserve(entry.target);
                        });
                    }, {
                        threshold: .12,
                        rootMargin: '0px 0px -40px 0px'
                    });

                    revealTargets.forEach(function (target) {
                        revealObserver.observe(target);
                    });
                } else {
                    revealTargets.forEach(function (target) {
                        target.classList.add('is-visible');
                    });
                }

                /* ---------- 3D tilt cards ---------- */
                if (!reduceMotion && window.matchMedia('(pointer:fine)').matches) {
                    document.querySelectorAll('.tilt-card').forEach(function (card) {
                        card.addEventListener('mousemove', function (event) {
                            const rect = card.getBoundingClientRect();
                            const x = event.clientX - rect.left;
                            const y = event.clientY - rect.top;
                            const rotateY = ((x / rect.width) - .5) * 3.5;
                            const rotateX = (.5 - (y / rect.height)) * 3.5;

                            card.style.setProperty('--ry', rotateY + 'deg');
                            card.style.setProperty('--rx', rotateX + 'deg');
                            card.style.setProperty('--mx', ((x / rect.width) * 100) + '%');
                            card.style.setProperty('--my', ((y / rect.height) * 100) + '%');
                        });

                        card.addEventListener('mouseleave', function () {
                            card.style.setProperty('--ry', '0deg');
                            card.style.setProperty('--rx', '0deg');
                        });
                    });
                }

                /* ---------- stats count-up ---------- */
                const stats = document.querySelectorAll('.stats-grid .stat');

                function animateStat(stat) {
                    const valueElement = stat.querySelector('b');
                    if (!valueElement) return;

                    const original = valueElement.textContent.trim();
                    const match = original.match(/^(\d+(?:\.\d+)?)(.*)$/);

                    stat.classList.add('is-counted');
                    if (!match || original.includes('–')) return;

                    const target = parseFloat(match[1]);
                    const suffix = match[2];
                    const duration = 1200;
                    const start = performance.now();

                    function tick(now) {
                        const progress = Math.min((now - start) / duration, 1);
                        const eased = 1 - Math.pow(1 - progress, 3);
                        const current = target * eased;
                        valueElement.textContent = (Number.isInteger(target) ? Math.round(current) : current.toFixed(1)) + suffix;

                        if (progress < 1) {
                            requestAnimationFrame(tick);
                        } else {
                            valueElement.textContent = original;
                        }
                    }

                    requestAnimationFrame(tick);
                }

                if ('IntersectionObserver' in window) {
                    const statsObserver = new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (!entry.isIntersecting) return;
                            animateStat(entry.target);
                            statsObserver.unobserve(entry.target);
                        });
                    }, { threshold: .5 });

                    stats.forEach(function (stat) {
                        statsObserver.observe(stat);
                    });
                } else {
                    stats.forEach(animateStat);
                }

                /* ---------- active nav section ---------- */
                const navLinks = document.querySelectorAll('.nav-links a[href^="#"]');
                const navSections = [];

                navLinks.forEach(function (link) {
                    const selector = link.getAttribute('href');
                    if (!selector || selector === '#') return;
                    const section = document.querySelector(selector);
                    if (section) navSections.push({ link: link, section: section });
                });

                if (navSections.length && 'IntersectionObserver' in window) {
                    const sectionObserver = new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (!entry.isIntersecting) return;

                            navLinks.forEach(function (link) {
                                link.classList.remove('is-active');
                            });

                            const found = navSections.find(function (item) {
                                return item.section === entry.target;
                            });

                            if (found) found.link.classList.add('is-active');
                        });
                    }, {
                        threshold: .22,
                        rootMargin: '-20% 0px -55% 0px'
                    });

                    navSections.forEach(function (item) {
                        sectionObserver.observe(item.section);
                    });
                }

                /* ---------- our products filter + progressive reveal ---------- */
                const productFilterButtons = document.querySelectorAll('.product-filter-btn[data-filter]');
                const ourProductCards = Array.from(document.querySelectorAll('.our-product-card[data-category]'));
                const catalogLoadMore = document.getElementById('catalog-loadmore');
                const catalogLoadMoreText = document.getElementById('catalog-loadmore-text');
                const catalogVisibleCount = document.getElementById('catalog-visible-count');
                const catalogTotalCount = document.getElementById('catalog-total-count');
                const catalogPageSize = 8;
                let catalogFilter = 'all';
                let catalogVisibleLimit = catalogPageSize;

                function getCatalogMatches() {
                    return ourProductCards.filter(function (card) {
                        return catalogFilter === 'all' || card.dataset.category === catalogFilter;
                    });
                }

                function renderCatalog() {
                    const matches = getCatalogMatches();

                    ourProductCards.forEach(function (card) {
                        const belongs = matches.includes(card);
                        card.classList.toggle('is-hidden', !belongs);
                        card.classList.add('is-collapsed');
                    });

                    matches.forEach(function (card, index) {
                        card.classList.toggle('is-collapsed', index >= catalogVisibleLimit);
                    });

                    const visible = Math.min(catalogVisibleLimit, matches.length);
                    if (catalogVisibleCount) catalogVisibleCount.textContent = visible;
                    if (catalogTotalCount) catalogTotalCount.textContent = matches.length;

                    if (!catalogLoadMore || !catalogLoadMoreText) return;

                    if (matches.length <= catalogPageSize) {
                        catalogLoadMore.style.display = 'none';
                        return;
                    }

                    catalogLoadMore.style.display = 'inline-flex';

                    if (catalogVisibleLimit >= matches.length) {
                        catalogLoadMoreText.textContent = 'Tampilkan Lebih Sedikit';
                        catalogLoadMore.classList.add('is-expanded');
                    } else {
                        const remaining = matches.length - catalogVisibleLimit;
                        const nextAmount = Math.min(catalogPageSize, remaining);
                        catalogLoadMoreText.textContent = 'Lihat ' + nextAmount + ' Produk Lainnya';
                        catalogLoadMore.classList.remove('is-expanded');
                    }
                }

                productFilterButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        catalogFilter = button.dataset.filter || 'all';
                        catalogVisibleLimit = catalogPageSize;

                        productFilterButtons.forEach(function (btn) {
                            btn.classList.toggle('is-active', btn === button);
                        });

                        renderCatalog();
                    });
                });

                if (catalogLoadMore) {
                    catalogLoadMore.addEventListener('click', function () {
                        const matches = getCatalogMatches();

                        if (catalogVisibleLimit >= matches.length) {
                            catalogVisibleLimit = catalogPageSize;
                            renderCatalog();
                            document.getElementById('produk')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            return;
                        }

                        catalogVisibleLimit = Math.min(catalogVisibleLimit + catalogPageSize, matches.length);
                        renderCatalog();
                    });
                }

                renderCatalog();

                /* ---------- looping bestseller carousel ---------- */
                const track = document.getElementById('bs-track');
                const prevButton = document.getElementById('bs-prev');
                const nextButton = document.getElementById('bs-next');

                if (track) {
                    function getCardStep() {
                        const card = track.querySelector('.product-card');
                        if (!card) return 250;

                        const style = window.getComputedStyle(track);
                        const gap = parseFloat(style.columnGap || style.gap || 20) || 20;
                        return card.offsetWidth + gap;
                    }

                    function nextSlide() {
                        const maxScroll = track.scrollWidth - track.clientWidth;
                        if (track.scrollLeft >= maxScroll - 5) {
                            track.scrollTo({ left: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
                        } else {
                            track.scrollBy({ left: getCardStep(), behavior: reduceMotion ? 'auto' : 'smooth' });
                        }
                    }

                    function prevSlide() {
                        if (track.scrollLeft <= 5) {
                            track.scrollTo({
                                left: track.scrollWidth - track.clientWidth,
                                behavior: reduceMotion ? 'auto' : 'smooth'
                            });
                        } else {
                            track.scrollBy({ left: -getCardStep(), behavior: reduceMotion ? 'auto' : 'smooth' });
                        }
                    }

                    if (nextButton) nextButton.addEventListener('click', nextSlide);
                    if (prevButton) prevButton.addEventListener('click', prevSlide);

                    let autoplay = null;

                    function stopAutoplay() {
                        if (!autoplay) return;
                        clearInterval(autoplay);
                        autoplay = null;
                    }

                    function startAutoplay() {
                        if (reduceMotion) return;
                        stopAutoplay();
                        autoplay = setInterval(nextSlide, 4800);
                    }

                    startAutoplay();
                    track.addEventListener('mouseenter', stopAutoplay);
                    track.addEventListener('mouseleave', startAutoplay);
                    track.addEventListener('touchstart', stopAutoplay, { passive: true });
                    track.addEventListener('touchend', startAutoplay, { passive: true });
                }

                /* ---------- smooth internal links ---------- */
                document.querySelectorAll('a[href^="#"]').forEach(function (link) {
                    link.addEventListener('click', function (event) {
                        const selector = this.getAttribute('href');
                        if (!selector || selector === '#') return;

                        const target = document.querySelector(selector);
                        if (!target) return;

                        event.preventDefault();
                        target.scrollIntoView({
                            behavior: reduceMotion ? 'auto' : 'smooth',
                            block: 'start'
                        });
                    });
                });
            });
        </script>

    </body>
</html>