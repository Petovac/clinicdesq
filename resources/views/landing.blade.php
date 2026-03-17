<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClinicDesq — Veterinary Practice Management Software</title>
    <meta name="description" content="Modern cloud-based veterinary clinic management. Appointments, billing, prescriptions, inventory, and more. Start your free trial today.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet">
    <style>
        /* ═══════════════════════════════════════════
           RESET & BASE
           ═══════════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; color: #1e293b; line-height: 1.6; overflow-x: hidden; background: #fff; }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; }

        /* ═══════════════════════════════════════════
           UTILITIES
           ═══════════════════════════════════════════ */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        .section { padding: 100px 0; }
        .section-label { display: inline-block; font-size: 13px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #0d9488; margin-bottom: 12px; }
        .section-title { font-size: 38px; font-weight: 800; line-height: 1.2; margin-bottom: 16px; color: #0f172a; }
        .section-subtitle { font-size: 18px; color: #64748b; max-width: 600px; line-height: 1.7; }
        .text-center { text-align: center; }
        .mx-auto { margin-left: auto; margin-right: auto; }

        /* ═══════════════════════════════════════════
           SCROLL REVEAL ANIMATIONS
           ═══════════════════════════════════════════ */
        .reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.7s cubic-bezier(.22,1,.36,1), transform 0.7s cubic-bezier(.22,1,.36,1); }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-left { opacity: 0; transform: translateX(-40px); transition: opacity 0.7s cubic-bezier(.22,1,.36,1), transform 0.7s cubic-bezier(.22,1,.36,1); }
        .reveal-left.visible { opacity: 1; transform: translateX(0); }
        .reveal-right { opacity: 0; transform: translateX(40px); transition: opacity 0.7s cubic-bezier(.22,1,.36,1), transform 0.7s cubic-bezier(.22,1,.36,1); }
        .reveal-right.visible { opacity: 1; transform: translateX(0); }
        .stagger > * { opacity: 0; transform: translateY(24px); transition: opacity 0.6s ease, transform 0.6s ease; }
        .stagger.visible > *:nth-child(1) { transition-delay: .05s; }
        .stagger.visible > *:nth-child(2) { transition-delay: .12s; }
        .stagger.visible > *:nth-child(3) { transition-delay: .19s; }
        .stagger.visible > *:nth-child(4) { transition-delay: .26s; }
        .stagger.visible > *:nth-child(5) { transition-delay: .33s; }
        .stagger.visible > *:nth-child(6) { transition-delay: .40s; }
        .stagger.visible > * { opacity: 1; transform: translateY(0); }

        /* ═══════════════════════════════════════════
           NAVBAR
           ═══════════════════════════════════════════ */
        .navbar { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; padding: 0 24px; transition: all .3s; }
        .navbar.scrolled { background: rgba(255,255,255,.92); backdrop-filter: blur(16px); box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .navbar-inner { max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; height: 72px; }
        .nav-logo { font-size: 22px; font-weight: 800; color: #0d9488; letter-spacing: -.5px; }
        .nav-logo span { color: #0f172a; }
        .nav-links { display: flex; align-items: center; gap: 32px; }
        .nav-links a { font-size: 14px; font-weight: 500; color: #475569; transition: color .2s; }
        .nav-links a:hover { color: #0d9488; }
        .nav-right { display: flex; align-items: center; gap: 16px; }
        .nav-login-group { position: relative; }
        .nav-login-btn { font-size: 13px; font-weight: 600; color: #475569; padding: 8px 16px; border-radius: 8px; cursor: pointer; border: 1px solid #e2e8f0; background: #fff; transition: all .2s; }
        .nav-login-btn:hover { background: #f8fafc; border-color: #cbd5e1; }
        .nav-login-dropdown { display: none; position: absolute; top: 100%; right: 0; background: #fff; border-radius: 12px; box-shadow: 0 12px 40px rgba(0,0,0,.12); border: 1px solid #e2e8f0; padding: 8px; min-width: 200px; z-index: 100; padding-top: 16px; }
        .nav-login-dropdown::before { content: ''; position: absolute; top: -10px; left: 0; right: 0; height: 10px; }
        .nav-login-group:hover .nav-login-dropdown,
        .nav-login-group:focus-within .nav-login-dropdown { display: block; }
        .nav-login-dropdown a { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 8px; font-size: 14px; font-weight: 500; color: #334155; transition: background .15s; }
        .nav-login-dropdown a:hover { background: #f0fdfa; color: #0d9488; }
        .nav-login-dropdown a svg { width: 18px; height: 18px; color: #94a3b8; }
        .btn-cta { display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; border-radius: 10px; font-size: 14px; font-weight: 700; border: none; cursor: pointer; transition: all .25s; }
        .btn-primary { background: #0d9488; color: #fff; box-shadow: 0 4px 14px rgba(13,148,136,.3); }
        .btn-primary:hover { background: #0f766e; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(13,148,136,.4); }
        .btn-secondary { background: #fff; color: #0d9488; border: 2px solid #0d9488; }
        .btn-secondary:hover { background: #f0fdfa; }
        .btn-lg { padding: 14px 32px; font-size: 16px; border-radius: 12px; }
        .hamburger { display: none; background: none; border: none; cursor: pointer; padding: 8px; }
        .hamburger span { display: block; width: 24px; height: 2px; background: #334155; margin: 5px 0; transition: all .3s; border-radius: 2px; }

        /* ═══════════════════════════════════════════
           HERO
           ═══════════════════════════════════════════ */
        @keyframes gradient-shift { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        @keyframes pulse-badge { 0%,100%{box-shadow:0 0 0 0 rgba(13,148,136,.4)} 50%{box-shadow:0 0 0 12px rgba(13,148,136,0)} }

        .hero { min-height: 100vh; display: flex; align-items: center; padding: 100px 0 80px; position: relative; overflow: hidden; background: linear-gradient(-45deg, #042f2e, #0d3d56, #134e4a, #064e3b); background-size: 400% 400%; animation: gradient-shift 18s ease infinite; }
        .hero::before { content:''; position:absolute; top:0; left:0; right:0; bottom:0; background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); }
        .hero-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; position: relative; z-index: 1; }
        .hero-text { color: #fff; }
        .hero-text h1 { font-size: 52px; font-weight: 900; line-height: 1.1; margin-bottom: 20px; letter-spacing: -1px; }
        .hero-text h1 .highlight { background: linear-gradient(135deg, #5eead4, #2dd4bf); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .hero-text p { font-size: 18px; color: rgba(255,255,255,.8); margin-bottom: 32px; line-height: 1.7; max-width: 480px; }
        .hero-btns { display: flex; gap: 16px; flex-wrap: wrap; }
        .hero-badge { display: inline-flex; align-items: center; gap: 8px; padding: 8px 18px; background: rgba(255,255,255,.12); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,.2); border-radius: 999px; color: #5eead4; font-size: 13px; font-weight: 700; margin-bottom: 24px; animation: pulse-badge 3s infinite; }

        /* Hero Mockup */
        .hero-mockup { perspective: 1000px; }
        .mockup-window { background: #fff; border-radius: 16px; box-shadow: 0 40px 80px rgba(0,0,0,.3); overflow: hidden; animation: float 6s ease-in-out infinite; transform: rotateY(-5deg) rotateX(2deg); }
        .mockup-titlebar { height: 40px; background: #f1f5f9; display: flex; align-items: center; padding: 0 16px; gap: 8px; }
        .mockup-dot { width: 10px; height: 10px; border-radius: 50%; }
        .mockup-dot.red { background: #ef4444; }
        .mockup-dot.yellow { background: #f59e0b; }
        .mockup-dot.green { background: #22c55e; }
        .mockup-body { padding: 24px; }

        /* Animated dashboard inside mockup */
        @keyframes bar-fill { from{width:0} to{width:var(--w)} }
        @keyframes fade-in-row { from{opacity:0;transform:translateX(-10px)} to{opacity:1;transform:translateX(0)} }
        @keyframes type-text { from{width:0} to{width:100%} }

        .mock-stat-row { display: flex; gap: 12px; margin-bottom: 16px; }
        .mock-stat { flex: 1; background: #f0fdfa; border-radius: 10px; padding: 14px; }
        .mock-stat .num { font-size: 24px; font-weight: 800; color: #0d9488; }
        .mock-stat .label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
        .mock-table { width: 100%; }
        .mock-table-row { display: flex; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; gap: 12px; animation: fade-in-row .5s ease both; }
        .mock-table-row:nth-child(1) { animation-delay: .3s; }
        .mock-table-row:nth-child(2) { animation-delay: .5s; }
        .mock-table-row:nth-child(3) { animation-delay: .7s; }
        .mock-table-row:nth-child(4) { animation-delay: .9s; }
        .mock-avatar { width: 32px; height: 32px; border-radius: 50%; background: #e0f2fe; display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .mock-name { flex: 1; font-size: 13px; font-weight: 600; color: #334155; }
        .mock-badge-sm { padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .mock-badge-green { background: #dcfce7; color: #166534; }
        .mock-badge-blue { background: #dbeafe; color: #1e40af; }
        .mock-badge-amber { background: #fef3c7; color: #92400e; }
        .mock-bar-track { height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; width: 80px; }
        .mock-bar-fill { height: 100%; border-radius: 3px; animation: bar-fill 1.5s ease both; }
        .mock-bar-fill.teal { background: #14b8a6; }
        .mock-bar-fill.blue { background: #3b82f6; }
        .mock-bar-fill.amber { background: #f59e0b; }

        /* ═══════════════════════════════════════════
           FEATURES
           ═══════════════════════════════════════════ */
        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-top: 48px; }
        .feature-card { background: #fff; border-radius: 16px; padding: 32px; border: 1px solid #e2e8f0; transition: all .3s cubic-bezier(.22,1,.36,1); position: relative; overflow: hidden; }
        .feature-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--accent); transform: scaleX(0); transition: transform .3s; transform-origin: left; }
        .feature-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,0,0,.08); border-color: transparent; }
        .feature-card:hover::before { transform: scaleX(1); }
        .feature-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
        .feature-card h3 { font-size: 18px; font-weight: 700; margin-bottom: 10px; color: #0f172a; }
        .feature-card p { font-size: 14px; color: #64748b; line-height: 1.7; }

        /* ═══════════════════════════════════════════
           HOW IT WORKS
           ═══════════════════════════════════════════ */
        .steps-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 32px; margin-top: 56px; position: relative; }
        .steps-grid::before { content: ''; position: absolute; top: 48px; left: 12%; right: 12%; height: 3px; background: linear-gradient(90deg, #0d9488, #14b8a6, #5eead4, #99f6e4); border-radius: 2px; z-index: 0; }
        .step-card { position: relative; z-index: 1; text-align: center; }
        .step-num { width: 40px; height: 40px; border-radius: 50%; background: #0d9488; color: #fff; font-size: 16px; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 4px 14px rgba(13,148,136,.3); position: relative; z-index: 2; }
        .step-mockup { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.08); border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 20px; }
        .step-mockup-bar { height: 28px; background: #f8fafc; display: flex; align-items: center; padding: 0 10px; gap: 5px; border-bottom: 1px solid #e2e8f0; }
        .step-mockup-bar span { width: 7px; height: 7px; border-radius: 50%; }
        .step-mockup-body { padding: 16px; min-height: 140px; }
        .step-card h3 { font-size: 15px; font-weight: 700; margin-bottom: 6px; }
        .step-card p { font-size: 13px; color: #64748b; }

        /* Step mockup animations */
        @keyframes typing { from{width:0;border-right-color:#0d9488} to{width:100%;border-right-color:transparent} }
        @keyframes line-appear { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
        @keyframes bar-grow { from{width:0} }
        @keyframes check-pop { from{transform:scale(0)} to{transform:scale(1)} }

        .type-line { overflow: hidden; white-space: nowrap; border-right: 2px solid #0d9488; font-size: 12px; color: #334155; font-weight: 500; padding: 4px 0; }
        .animate .type-line { animation: typing 2s steps(30) forwards; }
        .animate .type-line:nth-child(2) { animation-delay: 2s; }
        .animate .type-line:nth-child(3) { animation-delay: 4s; }

        .rx-line { display: flex; align-items: center; gap: 8px; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 12px; opacity: 0; }
        .animate .rx-line { animation: line-appear .4s ease forwards; }
        .animate .rx-line:nth-child(1) { animation-delay: .3s; }
        .animate .rx-line:nth-child(2) { animation-delay: .6s; }
        .animate .rx-line:nth-child(3) { animation-delay: .9s; }
        .animate .rx-line:nth-child(4) { animation-delay: 1.2s; }
        .rx-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }

        .inv-bar-row { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 11px; color: #475569; }
        .inv-bar { height: 8px; border-radius: 4px; flex: 1; background: #e2e8f0; overflow: hidden; }
        .inv-bar-fill { height: 100%; border-radius: 4px; width: 0; }
        .animate .inv-bar-fill { animation: bar-grow 1.2s ease forwards; }
        .animate .inv-bar-fill:nth-child(1) { animation-delay: .2s; }

        /* ═══════════════════════════════════════════
           PRICING
           ═══════════════════════════════════════════ */
        .pricing-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-top: 48px; align-items: start; }
        .pricing-card { background: #fff; border-radius: 20px; padding: 36px; border: 2px solid #e2e8f0; position: relative; transition: all .3s; }
        .pricing-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,.08); }
        .pricing-card.featured { border-color: #0d9488; box-shadow: 0 8px 30px rgba(13,148,136,.15); transform: scale(1.04); }
        .pricing-card.featured:hover { transform: scale(1.04) translateY(-4px); }
        .pricing-popular { position: absolute; top: -14px; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg, #0d9488, #14b8a6); color: #fff; padding: 6px 20px; border-radius: 999px; font-size: 12px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; }
        .pricing-name { font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        .pricing-desc { font-size: 13px; color: #64748b; margin-bottom: 24px; }
        .pricing-price { margin-bottom: 8px; }
        .pricing-price .original { font-size: 18px; color: #94a3b8; text-decoration: line-through; margin-right: 8px; }
        .pricing-price .amount { font-size: 42px; font-weight: 900; color: #0f172a; line-height: 1; }
        .pricing-price .currency { font-size: 20px; font-weight: 700; color: #64748b; vertical-align: super; }
        .pricing-price .period { font-size: 14px; color: #94a3b8; font-weight: 500; }
        .pricing-trial { display: inline-block; padding: 6px 14px; background: #ecfdf5; color: #059669; border-radius: 8px; font-size: 12px; font-weight: 700; margin-bottom: 24px; }
        .pricing-features { list-style: none; margin-bottom: 28px; }
        .pricing-features li { display: flex; align-items: center; gap: 10px; padding: 8px 0; font-size: 14px; color: #475569; }
        .pricing-features li.disabled { color: #cbd5e1; }
        .pricing-check { width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .pricing-check.yes { background: #d1fae5; color: #059669; }
        .pricing-check.no { background: #f1f5f9; color: #cbd5e1; }
        .btn-full { width: 100%; justify-content: center; }

        /* ═══════════════════════════════════════════
           STATS
           ═══════════════════════════════════════════ */
        .stats-section { background: #f8fafc; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 32px; }
        .stat-item { text-align: center; padding: 20px; }
        .stat-num { font-size: 42px; font-weight: 900; color: #0d9488; line-height: 1; margin-bottom: 4px; }
        .stat-label { font-size: 14px; color: #64748b; font-weight: 500; }

        /* ═══════════════════════════════════════════
           CTA BANNER
           ═══════════════════════════════════════════ */
        .cta-banner { background: linear-gradient(135deg, #0d9488, #0f766e, #115e59); padding: 80px 0; text-align: center; color: #fff; position: relative; overflow: hidden; }
        .cta-banner::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,.04) 0%, transparent 60%); }
        .cta-banner h2 { font-size: 36px; font-weight: 800; margin-bottom: 16px; position: relative; }
        .cta-banner p { font-size: 18px; color: rgba(255,255,255,.8); margin-bottom: 32px; position: relative; }
        .btn-white { background: #fff; color: #0d9488; font-weight: 700; box-shadow: 0 4px 14px rgba(0,0,0,.1); position: relative; }
        .btn-white:hover { background: #f0fdfa; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.15); }

        /* ═══════════════════════════════════════════
           FOOTER
           ═══════════════════════════════════════════ */
        .footer { background: #0f172a; color: #94a3b8; padding: 64px 0 32px; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
        .footer-brand { }
        .footer-brand .nav-logo { font-size: 20px; margin-bottom: 12px; display: inline-block; }
        .footer-brand p { font-size: 14px; line-height: 1.7; max-width: 300px; }
        .footer h4 { font-size: 13px; font-weight: 700; color: #e2e8f0; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; }
        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 10px; }
        .footer-links a { font-size: 14px; color: #94a3b8; transition: color .2s; }
        .footer-links a:hover { color: #5eead4; }
        .footer-bottom { border-top: 1px solid #1e293b; padding-top: 24px; display: flex; justify-content: space-between; align-items: center; font-size: 13px; }

        /* ═══════════════════════════════════════════
           RESPONSIVE
           ═══════════════════════════════════════════ */
        @media (max-width: 1024px) {
            .hero-text h1 { font-size: 40px; }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .steps-grid { grid-template-columns: repeat(2, 1fr); }
            .steps-grid::before { display: none; }
            .pricing-grid { grid-template-columns: repeat(2, 1fr); }
            .pricing-card.featured { transform: scale(1); }
            .pricing-card.featured:hover { transform: translateY(-4px); }
        }
        @media (max-width: 768px) {
            .section { padding: 64px 0; }
            .section-title { font-size: 28px; }
            .nav-links { display: none; }
            .nav-right .nav-login-group { display: none; }
            .hamburger { display: block; }
            .hero-grid { grid-template-columns: 1fr; gap: 40px; }
            .hero-mockup { display: none; }
            .hero-text h1 { font-size: 34px; }
            .features-grid { grid-template-columns: 1fr; }
            .steps-grid { grid-template-columns: 1fr; gap: 24px; }
            .pricing-grid { grid-template-columns: 1fr; max-width: 400px; margin-left: auto; margin-right: auto; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 480px) {
            .hero-text h1 { font-size: 28px; }
            .hero-btns { flex-direction: column; }
            .stats-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; gap: 32px; }
        }

        /* Mobile menu overlay */
        .mobile-menu { display: none; position: fixed; top: 72px; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,.98); backdrop-filter: blur(16px); z-index: 999; padding: 24px; flex-direction: column; gap: 8px; }
        .mobile-menu.open { display: flex; }
        .mobile-menu a { display: block; padding: 14px 20px; font-size: 16px; font-weight: 600; color: #334155; border-radius: 12px; transition: background .2s; }
        .mobile-menu a:hover { background: #f0fdfa; color: #0d9488; }
        .mobile-menu .sep { height: 1px; background: #e2e8f0; margin: 8px 0; }
    </style>
</head>
<body>

<!-- ════════════════════════════════════════════
     NAVBAR
     ════════════════════════════════════════════ -->
<nav class="navbar" id="navbar">
    <div class="navbar-inner">
        <a href="#" class="nav-logo">Clinic<span>Desq</span></a>
        <div class="nav-links">
            <a href="#features">Features</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#pricing">Pricing</a>
        </div>
        <div class="nav-right">
            <div class="nav-login-group">
                <button class="nav-login-btn">Login</button>
                <div class="nav-login-dropdown">
                    <a href="/vet/login">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Veterinarian Login
                    </a>
                    <a href="/staff/login">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        Staff / Admin Login
                    </a>
                    <a href="/parent/login">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Pet Parent Portal
                    </a>
                </div>
            </div>
            <div class="signup-dropdown" style="position:relative;display:inline-block;">
                <a href="#" class="btn-cta btn-primary" onclick="event.preventDefault();this.parentElement.classList.toggle('open')">Get Started Free</a>
                <div class="signup-menu" style="display:none;position:absolute;top:100%;right:0;margin-top:8px;background:#fff;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,.15);padding:8px;min-width:220px;z-index:1000;">
                    <a href="/vet/register" style="display:block;padding:10px 14px;border-radius:8px;color:#1e293b;text-decoration:none;font-size:14px;font-weight:500;transition:background .15s;">I'm a Veterinarian</a>
                    <a href="/register/organisation" style="display:block;padding:10px 14px;border-radius:8px;color:#1e293b;text-decoration:none;font-size:14px;font-weight:500;transition:background .15s;">Register My Clinic</a>
                </div>
            </div>
            <button class="hamburger" onclick="document.getElementById('mobileMenu').classList.toggle('open')">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <a href="#features" onclick="document.getElementById('mobileMenu').classList.remove('open')">Features</a>
    <a href="#how-it-works" onclick="document.getElementById('mobileMenu').classList.remove('open')">How It Works</a>
    <a href="#pricing" onclick="document.getElementById('mobileMenu').classList.remove('open')">Pricing</a>
    <div class="sep"></div>
    <a href="/vet/login">Veterinarian Login</a>
    <a href="/staff/login">Staff / Admin Login</a>
    <a href="/parent/login">Pet Parent Portal</a>
    <div class="sep"></div>
    <a href="/register/organisation" style="background:#2563eb;color:#fff;text-align:center;border-radius:12px;">Register My Clinic</a>
    <a href="/vet/register" style="background:#0d9488;color:#fff;text-align:center;border-radius:12px;">Register as Vet</a>
</div>

<!-- ════════════════════════════════════════════
     HERO
     ════════════════════════════════════════════ -->
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-text">
                <div class="hero-badge">First Month Free</div>
                <h1>The Complete <span class="highlight">Veterinary Practice</span> Management Platform</h1>
                <p>Streamline appointments, billing, prescriptions, inventory, and patient records. Everything your clinic needs, beautifully designed and incredibly simple.</p>
                <div class="hero-btns">
                    <a href="/register/organisation" class="btn-cta btn-primary btn-lg">Register My Clinic</a>
                    <a href="/vet/register" class="btn-cta btn-secondary btn-lg" style="color:#fff;border-color:rgba(255,255,255,.3);">I'm a Veterinarian</a>
                </div>
            </div>
            <div class="hero-mockup">
                <div class="mockup-window">
                    <div class="mockup-titlebar">
                        <div class="mockup-dot red"></div>
                        <div class="mockup-dot yellow"></div>
                        <div class="mockup-dot green"></div>
                    </div>
                    <div class="mockup-body">
                        <div class="mock-stat-row">
                            <div class="mock-stat">
                                <div class="num">24</div>
                                <div class="label">Today's Appts</div>
                            </div>
                            <div class="mock-stat">
                                <div class="num" style="color:#3b82f6;">156</div>
                                <div class="label">Active Patients</div>
                            </div>
                            <div class="mock-stat">
                                <div class="num" style="color:#f59e0b;">12</div>
                                <div class="label">Low Stock</div>
                            </div>
                        </div>
                        <div class="mock-table">
                            <div class="mock-table-row">
                                <div class="mock-avatar">🐕</div>
                                <div class="mock-name">Bruno — Vaccination</div>
                                <span class="mock-badge-sm mock-badge-green">Completed</span>
                            </div>
                            <div class="mock-table-row">
                                <div class="mock-avatar">🐈</div>
                                <div class="mock-name">Whiskers — Checkup</div>
                                <span class="mock-badge-sm mock-badge-blue">In Progress</span>
                            </div>
                            <div class="mock-table-row">
                                <div class="mock-avatar">🐕</div>
                                <div class="mock-name">Max — Surgery Follow-up</div>
                                <span class="mock-badge-sm mock-badge-amber">Waiting</span>
                            </div>
                            <div class="mock-table-row">
                                <div class="mock-avatar">🐇</div>
                                <div class="mock-name">Bunny — Dental Check</div>
                                <span class="mock-badge-sm mock-badge-green">Completed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════════
     FEATURES
     ════════════════════════════════════════════ -->
<section class="section" id="features" style="background:#f8fafc;">
    <div class="container text-center">
        <div class="reveal">
            <div class="section-label">Features</div>
            <h2 class="section-title">Everything Your Clinic Needs</h2>
            <p class="section-subtitle mx-auto">Powerful tools designed specifically for veterinary practices, from small clinics to multi-branch hospitals.</p>
        </div>
        <div class="features-grid stagger">
            <div class="feature-card" style="--accent:#0d9488;">
                <div class="feature-icon" style="background:#f0fdfa;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/></svg>
                </div>
                <h3>Smart Appointments</h3>
                <p>Schedule, reschedule, and manage appointments with a clean calendar view. Automatic reminders and follow-up tracking built in.</p>
            </div>
            <div class="feature-card" style="--accent:#3b82f6;">
                <div class="feature-icon" style="background:#eff6ff;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                </div>
                <h3>Billing & Invoicing</h3>
                <p>Generate professional invoices with GST support. Treatment-to-bill pipeline automatically calculates charges from prescriptions.</p>
            </div>
            <div class="feature-card" style="--accent:#8b5cf6;">
                <div class="feature-icon" style="background:#f5f3ff;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
                <h3>Digital Prescriptions</h3>
                <p>Create and print prescriptions with drug database integration. Vet stamp and signature automatically applied. Beautiful PDF templates.</p>
            </div>
            <div class="feature-card" style="--accent:#f59e0b;">
                <div class="feature-icon" style="background:#fffbeb;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                </div>
                <h3>Inventory Management</h3>
                <p>Track stock with FEFO deduction, batch management, central warehouse, clinic transfers, and real-time low-stock alerts.</p>
            </div>
            <div class="feature-card" style="--accent:#ec4899;">
                <div class="feature-icon" style="background:#fdf2f8;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <h3>Pet Parent Portal</h3>
                <p>Give pet parents their own login to view appointments, prescriptions, bills, and complete health history of their pets.</p>
            </div>
            <div class="feature-card" style="--accent:#06b6d4;">
                <div class="feature-icon" style="background:#ecfeff;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <h3>IPD & Diagnostics</h3>
                <p>Admit patients, track vitals, manage treatment plans with daily progress notes. Full in-patient department management.</p>
            </div>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════════
     HOW IT WORKS
     ════════════════════════════════════════════ -->
<section class="section" id="how-it-works">
    <div class="container text-center">
        <div class="reveal">
            <div class="section-label">How It Works</div>
            <h2 class="section-title">Simple. Intuitive. Powerful.</h2>
            <p class="section-subtitle mx-auto">See how ClinicDesq streamlines your entire workflow from appointment to checkout.</p>
        </div>
        <div class="steps-grid stagger">
            <!-- Step 1: Appointment -->
            <div class="step-card">
                <div class="step-num">1</div>
                <div class="step-mockup">
                    <div class="step-mockup-bar">
                        <span style="background:#ef4444;"></span>
                        <span style="background:#f59e0b;"></span>
                        <span style="background:#22c55e;"></span>
                    </div>
                    <div class="step-mockup-body">
                        <div style="font-size:11px;color:#94a3b8;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">New Appointment</div>
                        <div style="background:#f0fdfa;border-radius:6px;padding:8px;margin-bottom:6px;font-size:12px;color:#0d9488;font-weight:600;">Pet: Bruno (Labrador)</div>
                        <div style="background:#f8fafc;border-radius:6px;padding:8px;margin-bottom:6px;font-size:12px;color:#475569;">Dr. Sharma</div>
                        <div style="background:#f8fafc;border-radius:6px;padding:8px;font-size:12px;color:#475569;">Today, 10:30 AM</div>
                    </div>
                </div>
                <h3>Book Appointment</h3>
                <p>Quick scheduling with pet & vet selection</p>
            </div>
            <!-- Step 2: Examine -->
            <div class="step-card">
                <div class="step-num">2</div>
                <div class="step-mockup">
                    <div class="step-mockup-bar">
                        <span style="background:#ef4444;"></span>
                        <span style="background:#f59e0b;"></span>
                        <span style="background:#22c55e;"></span>
                    </div>
                    <div class="step-mockup-body">
                        <div style="font-size:11px;color:#94a3b8;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">Case Sheet</div>
                        <div class="type-line">Weight: 28.5 kg | Temp: 101.2°F</div>
                        <div class="type-line">Symptoms: Lethargy, loss of appetite</div>
                        <div class="type-line">Diagnosis: Bacterial infection</div>
                    </div>
                </div>
                <h3>Examine & Diagnose</h3>
                <p>Record vitals, symptoms, and diagnosis</p>
            </div>
            <!-- Step 3: Prescribe -->
            <div class="step-card">
                <div class="step-num">3</div>
                <div class="step-mockup">
                    <div class="step-mockup-bar">
                        <span style="background:#ef4444;"></span>
                        <span style="background:#f59e0b;"></span>
                        <span style="background:#22c55e;"></span>
                    </div>
                    <div class="step-mockup-body">
                        <div style="font-size:11px;color:#94a3b8;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">Prescription</div>
                        <div class="rx-line"><span class="rx-dot" style="background:#0d9488;"></span>Amoxicillin 250mg — BID x 7d</div>
                        <div class="rx-line"><span class="rx-dot" style="background:#3b82f6;"></span>Metronidazole 200mg — BID x 5d</div>
                        <div class="rx-line"><span class="rx-dot" style="background:#f59e0b;"></span>Pantoprazole 40mg — OD x 7d</div>
                        <div class="rx-line"><span class="rx-dot" style="background:#8b5cf6;"></span>Probiotic Sachet — OD x 10d</div>
                    </div>
                </div>
                <h3>Prescribe & Bill</h3>
                <p>Auto-generate bills from treatments</p>
            </div>
            <!-- Step 4: Inventory -->
            <div class="step-card">
                <div class="step-num">4</div>
                <div class="step-mockup">
                    <div class="step-mockup-bar">
                        <span style="background:#ef4444;"></span>
                        <span style="background:#f59e0b;"></span>
                        <span style="background:#22c55e;"></span>
                    </div>
                    <div class="step-mockup-body">
                        <div style="font-size:11px;color:#94a3b8;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">Inventory</div>
                        <div class="inv-bar-row">
                            <span style="width:70px;">Amoxicillin</span>
                            <div class="inv-bar"><div class="inv-bar-fill" style="width:75%;background:#14b8a6;"></div></div>
                            <span style="font-weight:600;">75%</span>
                        </div>
                        <div class="inv-bar-row">
                            <span style="width:70px;">Metronid.</span>
                            <div class="inv-bar"><div class="inv-bar-fill" style="width:45%;background:#3b82f6;"></div></div>
                            <span style="font-weight:600;">45%</span>
                        </div>
                        <div class="inv-bar-row">
                            <span style="width:70px;">Pantopra.</span>
                            <div class="inv-bar"><div class="inv-bar-fill" style="width:20%;background:#ef4444;"></div></div>
                            <span style="font-weight:600;color:#ef4444;">20%</span>
                        </div>
                        <div class="inv-bar-row">
                            <span style="width:70px;">Probiotic</span>
                            <div class="inv-bar"><div class="inv-bar-fill" style="width:90%;background:#22c55e;"></div></div>
                            <span style="font-weight:600;">90%</span>
                        </div>
                    </div>
                </div>
                <h3>Track Everything</h3>
                <p>Auto-deduct stock, get low-stock alerts</p>
            </div>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════════
     STATS
     ════════════════════════════════════════════ -->
<section class="stats-section section" style="padding:60px 0;">
    <div class="container">
        <div class="stats-grid stagger">
            <div class="stat-item">
                <div class="stat-num" data-count="500">0</div>
                <div class="stat-label">Clinics Onboarded</div>
            </div>
            <div class="stat-item">
                <div class="stat-num" data-count="1200">0</div>
                <div class="stat-label">Veterinarians</div>
            </div>
            <div class="stat-item">
                <div class="stat-num" data-count="50000">0</div>
                <div class="stat-label">Appointments Managed</div>
            </div>
            <div class="stat-item">
                <div class="stat-num" data-count="25000">0</div>
                <div class="stat-label">Prescriptions Generated</div>
            </div>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════════
     PRICING
     ════════════════════════════════════════════ -->
<section class="section" id="pricing">
    <div class="container text-center">
        <div class="reveal">
            <div class="section-label">Pricing</div>
            <h2 class="section-title">Simple, Transparent Pricing</h2>
            <p class="section-subtitle mx-auto">No hidden fees. No surprises. Choose the plan that fits your practice.</p>
        </div>
        <div class="pricing-grid stagger">
            <!-- Starter -->
            <div class="pricing-card">
                <div class="pricing-name">Starter</div>
                <div class="pricing-desc">For solo practitioners and small clinics</div>
                <div class="pricing-price">
                    <span class="original">&#8377;1,499</span><br>
                    <span class="currency">&#8377;</span><span class="amount">999</span>
                    <span class="period">/doctor/month</span>
                </div>
                <div class="pricing-trial">1st Month Free</div>
                <ul class="pricing-features">
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>Appointments</li>
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>Billing & Invoicing</li>
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>Prescriptions & Case Sheets</li>
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>Pet Parent Portal</li>
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>Follow-ups</li>
                    <li class="disabled"><span class="pricing-check no"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span>Inventory Management</li>
                    <li class="disabled"><span class="pricing-check no"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span>Reports & Analytics</li>
                    <li class="disabled"><span class="pricing-check no"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span>IPD Management</li>
                </ul>
                <a href="/register/organisation" class="btn-cta btn-secondary btn-full">Start Free Trial</a>
            </div>
            <!-- Professional -->
            <div class="pricing-card featured">
                <div class="pricing-popular">Most Popular</div>
                <div class="pricing-name">Professional</div>
                <div class="pricing-desc">For growing clinics that need more power</div>
                <div class="pricing-price">
                    <span class="original">&#8377;1,999</span><br>
                    <span class="currency">&#8377;</span><span class="amount">1,499</span>
                    <span class="period">/doctor/month</span>
                </div>
                <div class="pricing-trial">1st Month Free</div>
                <ul class="pricing-features">
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>Everything in Starter</li>
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>Inventory Management</li>
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>Reports & Analytics</li>
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>Diagnostics & Lab</li>
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>AI-Powered Insights</li>
                    <li class="disabled"><span class="pricing-check no"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span>Multi-Clinic Management</li>
                    <li class="disabled"><span class="pricing-check no"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span>Custom Branding</li>
                    <li class="disabled"><span class="pricing-check no"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span>API Access</li>
                </ul>
                <a href="/register/organisation" class="btn-cta btn-primary btn-full">Start Free Trial</a>
            </div>
            <!-- Enterprise -->
            <div class="pricing-card">
                <div class="pricing-name">Enterprise</div>
                <div class="pricing-desc">For multi-branch hospitals and chains</div>
                <div class="pricing-price">
                    <span class="amount" style="font-size:32px;">Custom</span>
                    <span class="period">/tailored to your needs</span>
                </div>
                <div class="pricing-trial" style="background:#f1f5f9;color:#475569;">Book a Demo</div>
                <ul class="pricing-features">
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>Everything in Professional</li>
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>IPD Management</li>
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>Multi-Clinic Management</li>
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>Custom Branding & Templates</li>
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>API Access</li>
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>Priority Support</li>
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>Unlimited Clinics</li>
                    <li><span class="pricing-check yes"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>Dedicated Account Manager</li>
                </ul>
                <a href="/register/organisation" class="btn-cta btn-primary btn-full" style="background:#0f172a;box-shadow:0 4px 14px rgba(15,23,42,.3);">Contact Sales</a>
            </div>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════════
     CTA BANNER
     ════════════════════════════════════════════ -->
<section class="cta-banner">
    <div class="container reveal">
        <h2>Ready to Modernize Your Practice?</h2>
        <p>Join hundreds of veterinary clinics already using ClinicDesq. Start your free trial today.</p>
        <a href="/register/organisation" class="btn-cta btn-white btn-lg">Start Your Free Month</a>
    </div>
</section>

<!-- ════════════════════════════════════════════
     FOOTER
     ════════════════════════════════════════════ -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="nav-logo">Clinic<span style="color:#e2e8f0;">Desq</span></div>
                <p>Modern veterinary practice management software. Built for clinics that care about efficiency and patient experience.</p>
            </div>
            <div>
                <h4>Product</h4>
                <ul class="footer-links">
                    <li><a href="#features">Features</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                </ul>
            </div>
            <div>
                <h4>Login</h4>
                <ul class="footer-links">
                    <li><a href="/vet/login">Veterinarian</a></li>
                    <li><a href="/staff/login">Staff / Admin</a></li>
                    <li><a href="/parent/login">Pet Parent</a></li>
                    <li><a href="/vet/register">Register as Vet</a></li>
                    <li><a href="/register/organisation">Register Clinic</a></li>
                </ul>
            </div>
            <div>
                <h4>Company</h4>
                <ul class="footer-links">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} ClinicDesq. All rights reserved.</span>
            <span>Made with care for veterinary professionals</span>
        </div>
    </div>
</footer>

<!-- ════════════════════════════════════════════
     SCRIPTS
     ════════════════════════════════════════════ -->
<script>
// Navbar scroll effect
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 20);
});

// Scroll reveal
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            if (entry.target.classList.contains('animate-trigger')) {
                entry.target.classList.add('animate');
            }
        }
    });
}, { threshold: 0.15 });

document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .stagger, .step-mockup-body').forEach(el => {
    revealObserver.observe(el);
});

// Step mockup animations
document.querySelectorAll('.step-mockup-body').forEach(el => {
    const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.closest('.step-card').classList.add('animate');
            }
        });
    }, { threshold: 0.3 });
    obs.observe(el);
});

// Counter animation
const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const el = entry.target;
            const target = parseInt(el.dataset.count);
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                el.textContent = Math.floor(current).toLocaleString('en-IN') + '+';
            }, 16);
            counterObserver.unobserve(el);
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('.stat-num[data-count]').forEach(el => counterObserver.observe(el));

// Signup dropdown toggle
document.addEventListener('click', function(e) {
    document.querySelectorAll('.signup-dropdown').forEach(d => {
        if (!d.contains(e.target)) {
            d.classList.remove('open');
            d.querySelector('.signup-menu').style.display = 'none';
        }
    });
    const dd = e.target.closest('.signup-dropdown');
    if (dd) {
        const menu = dd.querySelector('.signup-menu');
        menu.style.display = dd.classList.contains('open') ? 'block' : 'none';
    }
});
</script>

</body>
</html>
