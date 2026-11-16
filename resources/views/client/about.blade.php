@extends('layouts.client')

@section('title', 'من نحن - منصة دارك لتنظيم الإيجار')

@section('content')
<style>
    /* ===== CUSTOM FONTS ===== */
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap');

    /* ===== CSS VARIABLES ===== */
    :root {
        --amber-primary: #F59E0B;
        --amber-dark: #D97706;
        --amber-light: #FCD34D;
        --slate-darkest: #0F172A;
        --slate-dark: #1E293B;
        --slate-mid: #334155;
        --emerald-accent: #10B981;
        --sky-accent: #0EA5E9;
        --glass-bg: rgba(255, 255, 255, 0.05);
        --glass-border: rgba(255, 255, 255, 0.1);
        --glass-shadow: rgba(0, 0, 0, 0.3);
    }

    /* ===== KEYFRAME ANIMATIONS ===== */
    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        25% { transform: translateY(-20px) rotate(5deg); }
        50% { transform: translateY(-35px) rotate(-3deg); }
        75% { transform: translateY(-15px) rotate(2deg); }
    }

    @keyframes floatReverse {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        25% { transform: translateY(20px) rotate(-5deg); }
        50% { transform: translateY(30px) rotate(3deg); }
        75% { transform: translateY(10px) rotate(-2deg); }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(60px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(80px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-80px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes pulseGlow {
        0%, 100% {
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.3);
        }
        50% {
            box-shadow: 0 0 40px rgba(245, 158, 11, 0.6), 0 0 80px rgba(245, 158, 11, 0.2);
        }
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.7);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes shimmer {
        0% { background-position: -200% center; }
        100% { background-position: 200% center; }
    }

    @keyframes rotateShape {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @keyframes borderGlow {
        0%, 100% { border-color: rgba(245, 158, 11, 0.3); }
        50% { border-color: rgba(245, 158, 11, 0.8); }
    }

    @keyframes particleFloat {
        0%, 100% {
            transform: translate(0, 0) scale(1);
            opacity: 0.6;
        }
        25% {
            transform: translate(30px, -50px) scale(1.2);
            opacity: 1;
        }
        50% {
            transform: translate(-20px, -80px) scale(0.8);
            opacity: 0.4;
        }
        75% {
            transform: translate(40px, -40px) scale(1.1);
            opacity: 0.8;
        }
    }

    @keyframes typewriter {
        from { width: 0; }
        to { width: 100%; }
    }

    @keyframes blink {
        50% { border-color: transparent; }
    }

    @keyframes ripple {
        0% {
            transform: scale(0.8);
            opacity: 1;
        }
        100% {
            transform: scale(2.4);
            opacity: 0;
        }
    }

    @keyframes morphBlob {
        0%, 100% { border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%; }
        25% { border-radius: 58% 42% 30% 70% / 55% 35% 65% 45%; }
        50% { border-radius: 30% 70% 55% 45% / 65% 55% 45% 55%; }
        75% { border-radius: 70% 30% 45% 55% / 35% 65% 55% 45%; }
    }

    /* ===== ANIMATION UTILITY CLASSES ===== */
    .animate-on-scroll {
        opacity: 0;
        transform: translateY(60px);
        transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .animate-on-scroll.animated {
        opacity: 1;
        transform: translateY(0);
    }

    .animate-slide-right {
        opacity: 0;
        transform: translateX(80px);
        transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .animate-slide-right.animated {
        opacity: 1;
        transform: translateX(0);
    }

    .animate-slide-left {
        opacity: 0;
        transform: translateX(-80px);
        transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .animate-slide-left.animated {
        opacity: 1;
        transform: translateX(0);
    }

    .animate-scale {
        opacity: 0;
        transform: scale(0.7);
        transition: all 0.7s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .animate-scale.animated {
        opacity: 1;
        transform: scale(1);
    }

    .delay-100 { transition-delay: 0.1s !important; }
    .delay-200 { transition-delay: 0.2s !important; }
    .delay-300 { transition-delay: 0.3s !important; }
    .delay-400 { transition-delay: 0.4s !important; }
    .delay-500 { transition-delay: 0.5s !important; }
    .delay-600 { transition-delay: 0.6s !important; }
    .delay-700 { transition-delay: 0.7s !important; }
    .delay-800 { transition-delay: 0.8s !important; }

    /* ===== HERO SECTION ===== */
    .hero-about {
        min-height: 100vh;
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 30%, #44337A 60%, #92400E 85%, #78350F 100%);
        background-size: 300% 300%;
        animation: gradientShift 12s ease infinite;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hero-about::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(circle at 20% 50%, rgba(245, 158, 11, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(14, 165, 233, 0.1) 0%, transparent 40%),
            radial-gradient(circle at 50% 80%, rgba(16, 185, 129, 0.08) 0%, transparent 40%);
        z-index: 1;
    }

    .hero-about::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 200px;
        background: linear-gradient(to top, #0F172A, transparent);
        z-index: 2;
    }

    .floating-shape {
        position: absolute;
        border-radius: 50%;
        opacity: 0.08;
        z-index: 1;
        pointer-events: none;
    }

    .shape-1 {
        width: 300px;
        height: 300px;
        background: linear-gradient(135deg, #F59E0B, #D97706);
        top: 10%;
        right: 10%;
        animation: float 8s ease-in-out infinite, morphBlob 12s ease-in-out infinite;
    }

    .shape-2 {
        width: 200px;
        height: 200px;
        background: linear-gradient(135deg, #0EA5E9, #0284C7);
        bottom: 20%;
        left: 5%;
        animation: floatReverse 10s ease-in-out infinite, morphBlob 15s ease-in-out infinite;
    }

    .shape-3 {
        width: 150px;
        height: 150px;
        background: linear-gradient(135deg, #10B981, #059669);
        top: 40%;
        left: 30%;
        animation: float 12s ease-in-out infinite 2s, morphBlob 10s ease-in-out infinite;
    }

    .shape-4 {
        width: 100px;
        height: 100px;
        border: 2px solid rgba(245, 158, 11, 0.3);
        border-radius: 20%;
        top: 20%;
        left: 15%;
        animation: rotateShape 20s linear infinite, float 7s ease-in-out infinite;
    }

    .shape-5 {
        width: 80px;
        height: 80px;
        border: 2px solid rgba(14, 165, 233, 0.2);
        border-radius: 30%;
        bottom: 30%;
        right: 20%;
        animation: rotateShape 25s linear infinite reverse, floatReverse 9s ease-in-out infinite;
    }

    .shape-6 {
        width: 60px;
        height: 60px;
        background: rgba(245, 158, 11, 0.15);
        border-radius: 15%;
        top: 60%;
        right: 35%;
        animation: rotateShape 15s linear infinite, float 11s ease-in-out infinite 1s;
    }

    /* Particles */
    .particle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: rgba(245, 158, 11, 0.6);
        border-radius: 50%;
        z-index: 1;
        pointer-events: none;
    }

    .particle:nth-child(1) { top: 15%; left: 25%; animation: particleFloat 6s ease-in-out infinite; }
    .particle:nth-child(2) { top: 35%; right: 15%; animation: particleFloat 8s ease-in-out infinite 1s; }
    .particle:nth-child(3) { bottom: 25%; left: 40%; animation: particleFloat 7s ease-in-out infinite 2s; }
    .particle:nth-child(4) { top: 50%; left: 60%; animation: particleFloat 9s ease-in-out infinite 0.5s; }
    .particle:nth-child(5) { top: 70%; right: 30%; animation: particleFloat 5s ease-in-out infinite 1.5s; }
    .particle:nth-child(6) { top: 20%; left: 70%; animation: particleFloat 10s ease-in-out infinite 3s; }
    .particle:nth-child(7) { bottom: 40%; left: 10%; animation: particleFloat 7.5s ease-in-out infinite 2.5s; }
    .particle:nth-child(8) { top: 80%; right: 50%; animation: particleFloat 6.5s ease-in-out infinite 4s; }

    .hero-content {
        position: relative;
        z-index: 3;
        text-align: center;
        padding: 0 1.5rem;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.5rem;
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 9999px;
        color: #FCD34D;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        backdrop-filter: blur(10px);
        animation: fadeInDown 1s ease-out, pulseGlow 3s ease-in-out infinite;
    }

    .hero-title {
        font-family: 'Cairo', sans-serif;
        font-size: clamp(2rem, 6vw, 4rem);
        font-weight: 900;
        line-height: 1.3;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #FFFFFF 0%, #FCD34D 50%, #F59E0B 100%);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: fadeInUp 1s ease-out 0.3s both, shimmer 4s linear infinite;
    }

    .hero-subtitle {
        font-family: 'Cairo', sans-serif;
        font-size: clamp(1rem, 2.5vw, 1.35rem);
        color: rgba(255, 255, 255, 0.7);
        max-width: 700px;
        margin: 0 auto 2rem;
        line-height: 1.9;
        animation: fadeInUp 1s ease-out 0.6s both;
    }

    .hero-cta-group {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: center;
        animation: fadeInUp 1s ease-out 0.9s both;
    }

    .hero-scroll-indicator {
        position: absolute;
        bottom: 50px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 3;
        animation: fadeInUp 1s ease-out 1.2s both;
    }

    .scroll-mouse {
        width: 26px;
        height: 42px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        position: relative;
        margin: 0 auto 0.5rem;
    }

    .scroll-mouse::after {
        content: '';
        width: 4px;
        height: 8px;
        background: #F59E0B;
        border-radius: 2px;
        position: absolute;
        top: 8px;
        left: 50%;
        transform: translateX(-50%);
        animation: float 2s ease-in-out infinite;
    }

    .scroll-text {
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.75rem;
        font-family: 'Cairo', sans-serif;
    }

    /* ===== GLASS CARD ===== */
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 1.25rem;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }

    .glass-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(245, 158, 11, 0.5), transparent);
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .glass-card:hover::before {
        opacity: 1;
    }

    .glass-card:hover {
        transform: translateY(-8px);
        border-color: rgba(245, 158, 11, 0.3);
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3), 0 0 30px rgba(245, 158, 11, 0.1);
    }

    /* ===== GRADIENT BORDER CARD ===== */
    .gradient-border-card {
        position: relative;
        background: var(--slate-darkest);
        border-radius: 1.25rem;
        padding: 2px;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .gradient-border-card::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 1.25rem;
        padding: 2px;
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.3), rgba(14, 165, 233, 0.3), rgba(16, 185, 129, 0.3));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        transition: all 0.4s ease;
    }

    .gradient-border-card:hover::before {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.8), rgba(14, 165, 233, 0.8), rgba(16, 185, 129, 0.8));
    }

    .gradient-border-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
    }

    .gradient-border-inner {
        background: linear-gradient(135deg, var(--slate-darkest), var(--slate-dark));
        border-radius: 1.15rem;
        padding: 2rem;
        height: 100%;
    }

    /* ===== STATS SECTION ===== */
    .stats-section {
        background: linear-gradient(180deg, #0F172A 0%, #1E293B 100%);
        position: relative;
        overflow: hidden;
    }

    .stats-section::before {
        content: '';
        position: absolute;
        top: -100px;
        left: 50%;
        transform: translateX(-50%);
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }

    .stat-card {
        text-align: center;
        padding: 2rem 1rem;
        position: relative;
    }

    .stat-icon-wrapper {
        width: 70px;
        height: 70px;
        margin: 0 auto 1rem;
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.1));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .stat-icon-wrapper::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        border: 2px solid transparent;
        border-top-color: rgba(245, 158, 11, 0.5);
        animation: rotateShape 3s linear infinite;
    }

    .stat-number {
        font-family: 'Cairo', sans-serif;
        font-size: 2.5rem;
        font-weight: 900;
        background: linear-gradient(135deg, #FCD34D, #F59E0B);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: rgba(255, 255, 255, 0.6);
        font-family: 'Cairo', sans-serif;
        font-size: 0.95rem;
        font-weight: 600;
    }

    .stat-divider {
        width: 1px;
        background: linear-gradient(to bottom, transparent, rgba(245, 158, 11, 0.3), transparent);
        align-self: stretch;
        display: none;
    }

    @media (min-width: 768px) {
        .stat-divider { display: block; }
    }

    /* ===== ABOUT PLATFORM SECTION ===== */
    .about-section {
        background: linear-gradient(180deg, #1E293B 0%, #0F172A 100%);
        position: relative;
    }

    .about-section::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.06) 0%, transparent 70%);
        pointer-events: none;
    }

    .section-label {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 1.2rem;
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.25);
        border-radius: 9999px;
        color: #FCD34D;
        font-size: 0.85rem;
        font-weight: 600;
        font-family: 'Cairo', sans-serif;
    }

    .section-title {
        font-family: 'Cairo', sans-serif;
        font-size: clamp(1.75rem, 4vw, 2.75rem);
        font-weight: 800;
        color: #FFFFFF;
        line-height: 1.4;
    }

    .section-title span {
        background: linear-gradient(135deg, #FCD34D, #F59E0B);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .about-card-icon {
        width: 60px;
        height: 60px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.25rem;
        font-size: 1.5rem;
        position: relative;
    }

    .about-card-icon.vision {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(245, 158, 11, 0.05));
        color: #FCD34D;
    }

    .about-card-icon.mission {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.2), rgba(14, 165, 233, 0.05));
        color: #7DD3FC;
    }

    .about-card-icon.values {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.05));
        color: #6EE7B7;
    }

    /* ===== WHY CHOOSE US ===== */
    .why-section {
        background: linear-gradient(180deg, #0F172A 0%, #1E293B 50%, #0F172A 100%);
        position: relative;
        overflow: hidden;
    }

    .why-section::before {
        content: '';
        position: absolute;
        top: 20%;
        right: 0;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.05) 0%, transparent 70%);
        pointer-events: none;
    }

    .feature-card {
        position: relative;
        background: var(--slate-darkest);
        border-radius: 1.25rem;
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: default;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 1.25rem;
        padding: 2px;
        background: linear-gradient(135deg, transparent, rgba(245, 158, 11, 0.0), transparent);
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        transition: all 0.5s ease;
    }

    .feature-card:hover::before {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.6), rgba(14, 165, 233, 0.4), rgba(16, 185, 129, 0.6));
    }

    .feature-card:hover {
        transform: translateY(-12px) scale(1.03);
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4), 0 0 40px rgba(245, 158, 11, 0.08);
    }

    .feature-card-inner {
        background: linear-gradient(160deg, var(--slate-darkest), var(--slate-dark));
        border-radius: 1.15rem;
        padding: 2rem 1.5rem;
        height: 100%;
        position: relative;
        z-index: 1;
    }

    .feature-icon-box {
        width: 65px;
        height: 65px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.25rem;
        font-size: 1.5rem;
        position: relative;
        transition: all 0.4s ease;
    }

    .feature-card:hover .feature-icon-box {
        transform: scale(1.1) rotate(-5deg);
    }

    .feature-icon-box.amber {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(245, 158, 11, 0.05));
        color: #FCD34D;
    }

    .feature-icon-box.sky {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.2), rgba(14, 165, 233, 0.05));
        color: #7DD3FC;
    }

    .feature-icon-box.emerald {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.05));
        color: #6EE7B7;
    }

    .feature-icon-box.purple {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(139, 92, 246, 0.05));
        color: #C4B5FD;
    }

    .feature-icon-box.rose {
        background: linear-gradient(135deg, rgba(244, 63, 94, 0.2), rgba(244, 63, 94, 0.05));
        color: #FDA4AF;
    }

    .feature-icon-box.teal {
        background: linear-gradient(135deg, rgba(20, 184, 166, 0.2), rgba(20, 184, 166, 0.05));
        color: #5EEAD4;
    }

    .feature-card-title {
        font-family: 'Cairo', sans-serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: #FFFFFF;
        margin-bottom: 0.75rem;
    }

    .feature-card-desc {
        font-family: 'Cairo', sans-serif;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.55);
        line-height: 1.8;
    }

    /* ===== TIMELINE / VALUES SECTION ===== */
    .timeline-section {
        background: linear-gradient(180deg, #0F172A 0%, #1E293B 100%);
        position: relative;
    }

    .timeline-container {
        position: relative;
        padding: 2rem 0;
    }

    .timeline-line {
        position: absolute;
        top: 0;
        bottom: 0;
        right: 50%;
        width: 3px;
        background: linear-gradient(to bottom, transparent, rgba(245, 158, 11, 0.4), rgba(14, 165, 233, 0.4), rgba(16, 185, 129, 0.4), transparent);
        transform: translateX(50%);
    }

    @media (max-width: 767px) {
        .timeline-line {
            right: 1.5rem;
            transform: none;
        }
    }

    .timeline-item {
        display: flex;
        align-items: center;
        margin-bottom: 3rem;
        position: relative;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-dot {
        position: absolute;
        right: 50%;
        transform: translateX(50%);
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: linear-gradient(135deg, #F59E0B, #D97706);
        border: 3px solid var(--slate-darkest);
        z-index: 2;
        box-shadow: 0 0 20px rgba(245, 158, 11, 0.4);
    }

    .timeline-dot::after {
        content: '';
        position: absolute;
        inset: -6px;
        border-radius: 50%;
        border: 2px solid rgba(245, 158, 11, 0.3);
        animation: ripple 2s ease-out infinite;
    }

    @media (max-width: 767px) {
        .timeline-dot {
            right: 1.5rem;
            transform: translateX(50%);
        }
    }

    .timeline-content {
        width: 45%;
        padding: 1.5rem;
    }

    .timeline-item:nth-child(odd) .timeline-content {
        margin-right: auto;
        margin-left: 55%;
        text-align: right;
    }

    .timeline-item:nth-child(even) .timeline-content {
        margin-left: auto;
        margin-right: 55%;
        text-align: right;
    }

    @media (max-width: 767px) {
        .timeline-item:nth-child(odd) .timeline-content,
        .timeline-item:nth-child(even) .timeline-content {
            width: calc(100% - 4rem);
            margin-right: 3.5rem;
            margin-left: 0;
            text-align: right;
        }
    }

    .timeline-step-number {
        font-family: 'Cairo', sans-serif;
        font-size: 3rem;
        font-weight: 900;
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(245, 158, 11, 0.05));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .timeline-step-title {
        font-family: 'Cairo', sans-serif;
        font-size: 1.2rem;
        font-weight: 700;
        color: #FFFFFF;
        margin-bottom: 0.5rem;
    }

    .timeline-step-desc {
        font-family: 'Cairo', sans-serif;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.55);
        line-height: 1.8;
    }

    /* ===== CTA SECTION ===== */
    .cta-section {
        background: linear-gradient(180deg, #1E293B 0%, #0F172A 100%);
        position: relative;
        overflow: hidden;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse at 30% 50%, rgba(245, 158, 11, 0.08) 0%, transparent 50%),
            radial-gradient(ellipse at 70% 50%, rgba(14, 165, 233, 0.06) 0%, transparent 50%);
        pointer-events: none;
    }

    .cta-box {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(14, 165, 233, 0.05));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(245, 158, 11, 0.2);
        border-radius: 2rem;
        padding: 3rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .cta-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(245, 158, 11, 0.6), transparent);
    }

    .cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 2.5rem;
        background: linear-gradient(135deg, #F59E0B, #D97706);
        color: #0F172A;
        font-family: 'Cairo', sans-serif;
        font-size: 1.1rem;
        font-weight: 700;
        border-radius: 9999px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
        animation: pulseGlow 3s ease-in-out infinite;
    }

    .cta-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transition: all 0.5s ease;
        transform: translate(-50%, -50%);
    }

    .cta-btn:hover::before {
        width: 300px;
        height: 300px;
    }

    .cta-btn:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 40px rgba(245, 158, 11, 0.4);
    }

    .cta-btn span {
        position: relative;
        z-index: 1;
    }

    .cta-btn i {
        position: relative;
        z-index: 1;
        transition: transform 0.3s ease;
    }

    .cta-btn:hover i {
        transform: translateX(-5px);
    }

    /* ===== WHATSAPP FLOATING ===== */
    .whatsapp-float {
        position: fixed;
        bottom: 90px;
        left: 20px;
        z-index: 999;
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #25D366, #128C7E);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        text-decoration: none;
        box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
        transition: all 0.3s ease;
        animation: float 3s ease-in-out infinite;
    }

    .whatsapp-float:hover {
        transform: scale(1.1) translateY(-5px);
        box-shadow: 0 8px 30px rgba(37, 211, 102, 0.6);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 640px) {
        .stat-number {
            font-size: 2rem;
        }

        .feature-card-inner {
            padding: 1.5rem 1.25rem;
        }

        .cta-box {
            padding: 2rem 1.25rem;
        }
    }

    /* ===== SECTION PADDING ===== */
    .section-pad {
        padding: 5rem 1rem;
    }

    @media (min-width: 768px) {
        .section-pad {
            padding: 6rem 2rem;
        }
    }

    /* Mobile bottom nav spacing */
    .about-page-wrapper {
        padding-bottom: 80px;
    }
</style>

<div class="about-page-wrapper" dir="rtl" style="font-family: 'Cairo', sans-serif;">

    {{-- ==================== HERO SECTION ==================== --}}
    <section class="hero-about" id="hero">
        {{-- Floating Shapes --}}
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
        <div class="floating-shape shape-4"></div>
        <div class="floating-shape shape-5"></div>
        <div class="floating-shape shape-6"></div>

        {{-- Particles --}}
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>

        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-building"></i>
                منصة دارك لتنظيم الإيجار
            </div>

            <h1 class="hero-title">
                وجهتك الأولى لإيجار<br>الوحدات السكنية المميزة
            </h1>

            <p class="hero-subtitle">
                نسعى في منصة دارك لتقديم تجربة إيجار سكنية استثنائية تجمع بين الجودة والراحة والثقة،
                مع مجموعة واسعة من الخيارات العقارية المختارة بعناية لتناسب احتياجاتك.
            </p>

            <div class="hero-cta-group">
                <a href="{{ route('client.apartments.index') ?? '#' }}"
                   class="cta-btn">
                    <span>تصفح الوحدات السكنية</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>

        <div class="hero-scroll-indicator">
            <div class="scroll-mouse"></div>
            <span class="scroll-text">اسحب للأسفل</span>
        </div>
    </section>

    {{-- ==================== STATS SECTION ==================== --}}
    <section class="stats-section section-pad" id="stats">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-0 items-center">

                {{-- Stat 1 --}}
                <div class="stat-card animate-on-scroll" data-delay="0">
                    <div class="stat-icon-wrapper">
                        <i class="fas fa-smile text-amber-400 text-2xl"></i>
                    </div>
                    <div class="stat-number" data-target="500" data-suffix="+">0</div>
                    <div class="stat-label">عميل سعيد</div>
                </div>

                {{-- Stat 2 --}}
                <div class="stat-card animate-on-scroll delay-100" data-delay="100">
                    <div class="stat-icon-wrapper">
                        <i class="fas fa-home text-sky-400 text-2xl"></i>
                    </div>
                    <div class="stat-number" data-target="100" data-suffix="+">0</div>
                    <div class="stat-label">وحدة سكنية</div>
                </div>

                {{-- Stat 3 --}}
                <div class="stat-card animate-on-scroll delay-200" data-delay="200">
                    <div class="stat-icon-wrapper">
                        <i class="fas fa-handshake text-emerald-400 text-2xl"></i>
                    </div>
                    <div class="stat-number" data-target="50" data-suffix="+">0</div>
                    <div class="stat-label">مستثمر عقاري</div>
                </div>

                {{-- Stat 4 --}}
                <div class="stat-card animate-on-scroll delay-300" data-delay="300">
                    <div class="stat-icon-wrapper">
                        <i class="fas fa-headset text-purple-400 text-2xl"></i>
                    </div>
                    <div class="stat-number" data-target="24" data-suffix="/7">0</div>
                    <div class="stat-label">دعم متواصل</div>
                </div>

            </div>
        </div>
    </section>

    {{-- ==================== ABOUT PLATFORM SECTION ==================== --}}
    <section class="about-section section-pad" id="about">
        <div class="max-w-6xl mx-auto">
            {{-- Section Header --}}
            <div class="text-center mb-16">
                <div class="animate-on-scroll mb-4">
                    <span class="section-label">
                        <i class="fas fa-info-circle"></i>
                        تعرّف علينا
                    </span>
                </div>
                <h2 class="section-title animate-on-scroll delay-100">
                    ما هي منصة <span>دارك</span>؟
                </h2>
                <p class="text-gray-400 mt-4 max-w-2xl mx-auto leading-relaxed animate-on-scroll delay-200" style="font-family: 'Cairo', sans-serif;">
                    منصة رقمية متخصصة في تنظيم وإدارة الإيجار العقاري، نقدم حلولاً ذكية تربط المستأجرين بأفضل الوحدات السكنية المتاحة.
                </p>
            </div>

            {{-- Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">

                {{-- Vision Card --}}
                <div class="glass-card p-6 lg:p-8 animate-on-scroll delay-100">
                    <div class="about-card-icon vision">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3" style="font-family: 'Cairo', sans-serif;">رؤيتنا</h3>
                    <p class="text-gray-400 leading-relaxed text-sm" style="font-family: 'Cairo', sans-serif;">
                        أن نكون المنصة الأولى والأكثر ثقة في سوق الإيجار العقاري، من خلال تقديم تجربة رقمية متكاملة تسهّل على العملاء العثور على سكنهم المثالي بسهولة وشفافية.
                    </p>
                </div>

                {{-- Mission Card --}}
                <div class="glass-card p-6 lg:p-8 animate-on-scroll delay-200">
                    <div class="about-card-icon mission">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3" style="font-family: 'Cairo', sans-serif;">مهمتنا</h3>
                    <p class="text-gray-400 leading-relaxed text-sm" style="font-family: 'Cairo', sans-serif;">
                        تبسيط عملية البحث والحجز العقاري من خلال منصة ذكية تقدم خيارات متنوعة ومعلومات دقيقة وخدمة عملاء استثنائية، لضمان رضا جميع الأطراف.
                    </p>
                </div>

                {{-- Values Card --}}
                <div class="glass-card p-6 lg:p-8 animate-on-scroll delay-300">
                    <div class="about-card-icon values">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3" style="font-family: 'Cairo', sans-serif;">قيمنا</h3>
                    <p class="text-gray-400 leading-relaxed text-sm" style="font-family: 'Cairo', sans-serif;">
                        الشفافية في التعامل، الجودة في الاختيار، الالتزام بالمواعيد، واحترام خصوصية عملائنا. نؤمن بأن الثقة هي أساس كل علاقة ناجحة.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- ==================== WHY CHOOSE US SECTION ==================== --}}
    <section class="why-section section-pad" id="why-us">
        <div class="max-w-6xl mx-auto">
            {{-- Section Header --}}
            <div class="text-center mb-16">
                <div class="animate-on-scroll mb-4">
                    <span class="section-label">
                        <i class="fas fa-star"></i>
                        لماذا تجعلنا خيارك الأفضل؟
                    </span>
                </div>
                <h2 class="section-title animate-on-scroll delay-100">
                    لماذا يفضل العملاء والمُلاّك <span>منصة دارك</span>؟
                </h2>
                <p class="text-gray-300 mt-4 max-w-2xl mx-auto leading-relaxed animate-on-scroll delay-200 text-sm sm:text-base font-medium" style="font-family: 'Cairo', sans-serif;">
                    نحن نضع مصلحة وراحة العميل في المقام الأول، ونوفر تجربة عقارية متكاملة تمتاز بالأمان والاحترافية.
                </p>
            </div>

            {{-- Features Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Feature 1: Quality --}}
                <div class="feature-card animate-on-scroll delay-100">
                    <div class="feature-card-inner flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    جودة موثوقة
                                </span>
                                <div class="feature-icon-box amber !mb-0 !w-11 !h-11">
                                    <i class="fas fa-shield-alt text-base"></i>
                                </div>
                            </div>
                            <h3 class="feature-card-title">وحدات سكنية مختارة بعناية</h3>
                            <p class="feature-card-desc">
                                نهتم باختيار وتدقيق العين العقارية لضمان أعلى معايير الجودة، النظافة، والتأثيث الفاخر.
                            </p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-white/10 flex items-center gap-1.5 text-xs font-bold text-emerald-400">
                            <i class="fas fa-check-circle text-emerald-400"></i>
                            <span>معيار أساسي لدينا</span>
                        </div>
                    </div>
                </div>

                {{-- Feature 2: Variety --}}
                <div class="feature-card animate-on-scroll delay-200">
                    <div class="feature-card-inner flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                    مواقع مميزة
                                </span>
                                <div class="feature-icon-box sky !mb-0 !w-11 !h-11">
                                    <i class="fas fa-map-marker-alt text-base"></i>
                                </div>
                            </div>
                            <h3 class="feature-card-title">خيارات متنوعة في المواقع والأسعار</h3>
                            <p class="feature-card-desc">
                                توفير شقق وأجنحة بمختلف الفئات والمناطق الحيوية لتلائم ميزانيتك واحتياجك السكني.
                            </p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-white/10 flex items-center gap-1.5 text-xs font-bold text-emerald-400">
                            <i class="fas fa-check-circle text-emerald-400"></i>
                            <span>معيار أساسي لدينا</span>
                        </div>
                    </div>
                </div>

                {{-- Feature 3: Direct Booking --}}
                <div class="feature-card animate-on-scroll delay-300">
                    <div class="feature-card-inner flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    سرعة في الأداء
                                </span>
                                <div class="feature-icon-box emerald !mb-0 !w-11 !h-11">
                                    <i class="fas fa-comments text-base"></i>
                                </div>
                            </div>
                            <h3 class="feature-card-title">خدمة حجز وتواصل مباشرة</h3>
                            <p class="feature-card-desc">
                                تجربة حجز وتنسيق سلسة ومباشرة بدون تعقيدات وبأعلى قدر من الشفافية.
                            </p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-white/10 flex items-center gap-1.5 text-xs font-bold text-emerald-400">
                            <i class="fas fa-check-circle text-emerald-400"></i>
                            <span>معيار أساسي لدينا</span>
                        </div>
                    </div>
                </div>

                {{-- Feature 4: Follow-up --}}
                <div class="feature-card animate-on-scroll delay-400">
                    <div class="feature-card-inner flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                    دعم دائم
                                </span>
                                <div class="feature-icon-box purple !mb-0 !w-11 !h-11">
                                    <i class="fas fa-headset text-base"></i>
                                </div>
                            </div>
                            <h3 class="feature-card-title">متابعة مستمرة للعميل</h3>
                            <p class="feature-card-desc">
                                فريقنا متواجد دائماً لمتابعة احتياجاتك والإجابة على كافة الاستفسارات طوال فترة التعامل.
                            </p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-white/10 flex items-center gap-1.5 text-xs font-bold text-emerald-400">
                            <i class="fas fa-check-circle text-emerald-400"></i>
                            <span>معيار أساسي لدينا</span>
                        </div>
                    </div>
                </div>

                {{-- Feature 5: Marketing --}}
                <div class="feature-card animate-on-scroll delay-500">
                    <div class="feature-card-inner flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                    تسويق فعال
                                </span>
                                <div class="feature-icon-box rose !mb-0 !w-11 !h-11">
                                    <i class="fas fa-bullhorn text-base"></i>
                                </div>
                            </div>
                            <h3 class="feature-card-title">تسويق احترافي للعقارات</h3>
                            <p class="feature-card-desc">
                                استخدام أدوات تسويق عصرية وتصوير احترافي لعرض عقارات المالكين للبيع والإيجار.
                            </p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-white/10 flex items-center gap-1.5 text-xs font-bold text-emerald-400">
                            <i class="fas fa-check-circle text-emerald-400"></i>
                            <span>معيار أساسي لدينا</span>
                        </div>
                    </div>
                </div>

                {{-- Feature 6: Platform Chat --}}
                <div class="feature-card animate-on-scroll delay-600">
                    <div class="feature-card-inner flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    تواصل مباشر
                                </span>
                                <div class="feature-icon-box amber !mb-0 !w-11 !h-11">
                                    <i class="fas fa-comments text-base"></i>
                                </div>
                            </div>
                            <h3 class="feature-card-title">سهولة التواصل والمحادثة المباشرة</h3>
                            <p class="feature-card-desc">
                                إمكانية الاستفسار عن الشقق والأسعار ومحادثة المالك مباشرة عبر المنصة مع تنبيهات وإشعارات فورية.
                            </p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-white/10 flex items-center gap-1.5 text-xs font-bold text-emerald-400">
                            <i class="fas fa-check-circle text-emerald-400"></i>
                            <span>معيار أساسي لدينا</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ==================== TIMELINE / VALUES SECTION ==================== --}}
    <section class="timeline-section section-pad" id="journey">
        <div class="max-w-5xl mx-auto">
            {{-- Section Header --}}
            <div class="text-center mb-16">
                <div class="animate-on-scroll mb-4">
                    <span class="section-label">
                        <i class="fas fa-route"></i>
                        رحلتك معنا
                    </span>
                </div>
                <h2 class="section-title animate-on-scroll delay-100">
                    كيف تبدأ <span>رحلتك</span> مع منصة دارك؟
                </h2>
                <p class="text-gray-400 mt-4 max-w-2xl mx-auto leading-relaxed animate-on-scroll delay-200" style="font-family: 'Cairo', sans-serif;">
                    خطوات بسيطة تفصلك عن الحصول على وحدتك السكنية المثالية
                </p>
            </div>

            {{-- Timeline --}}
            <div class="timeline-container">
                <div class="timeline-line"></div>

                {{-- Step 1 --}}
                <div class="timeline-item animate-on-scroll">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content glass-card p-5">
                        <div class="timeline-step-number">01</div>
                        <h4 class="timeline-step-title">
                            <i class="fas fa-search text-amber-400 ml-2"></i>
                            تصفح الوحدات
                        </h4>
                        <p class="timeline-step-desc">
                            استعرض مجموعتنا الواسعة من الوحدات السكنية المتاحة، وتعرف على تفاصيل كل وحدة من صور وموقع ومميزات وأسعار.
                        </p>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="timeline-item animate-on-scroll delay-200">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content glass-card p-5">
                        <div class="timeline-step-number">02</div>
                        <h4 class="timeline-step-title">
                            <i class="fas fa-filter text-sky-400 ml-2"></i>
                            اختر ما يناسبك
                        </h4>
                        <p class="timeline-step-desc">
                            استخدم أدوات البحث والتصفية لتحديد الوحدة المناسبة لك من حيث الموقع والسعر والمساحة والمميزات المطلوبة.
                        </p>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="timeline-item animate-on-scroll delay-400">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content glass-card p-5">
                        <div class="timeline-step-number">03</div>
                        <h4 class="timeline-step-title">
                            <i class="fas fa-comments text-amber-400 ml-2"></i>
                            محادثة المالك مباشرة
                        </h4>
                        <p class="timeline-step-desc">
                            تواصل مباشرة مع المالك عبر منصة المحادثات الفورية للاستفسار عن أي تفاصيل وتأكيد رغبتك بالحجز واستلام الإشعارات.
                        </p>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="timeline-item animate-on-scroll delay-600">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content glass-card p-5">
                        <div class="timeline-step-number">04</div>
                        <h4 class="timeline-step-title">
                            <i class="fas fa-key text-purple-400 ml-2"></i>
                            احجز واستلم
                        </h4>
                        <p class="timeline-step-desc">
                            أكمل عملية الحجز بسهولة واستلم وحدتك السكنية. سنتابع معك خلال فترة إقامتك لضمان تجربة مميزة وسلسة.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== CTA SECTION ==================== --}}
    <section class="cta-section section-pad" id="cta">
        <div class="max-w-4xl mx-auto">
            <div class="cta-box animate-on-scroll">
                {{-- Decorative Elements --}}
                <div class="absolute top-4 right-4 w-20 h-20 rounded-full opacity-10"
                     style="background: linear-gradient(135deg, #F59E0B, #D97706); animation: float 6s ease-in-out infinite;"></div>
                <div class="absolute bottom-4 left-4 w-16 h-16 rounded-full opacity-10"
                     style="background: linear-gradient(135deg, #0EA5E9, #0284C7); animation: floatReverse 8s ease-in-out infinite;"></div>

                <div class="relative z-10">
                    <div class="mb-4">
                        <i class="fas fa-building text-4xl text-amber-400 mb-4 block" style="animation: float 3s ease-in-out infinite;"></i>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-white mb-4" style="font-family: 'Cairo', sans-serif;">
                        مستعد لتجد <span class="text-amber-400">سكنك المثالي</span>؟
                    </h2>
                    <p class="text-gray-400 mb-8 max-w-xl mx-auto leading-relaxed" style="font-family: 'Cairo', sans-serif;">
                        تصفح وحداتنا السكنية المتاحة الآن واختر ما يناسبك من بين خيارات متنوعة ومميزة بأسعار تنافسية.
                    </p>
                    <a href="{{ route('client.apartments.index') ?? '#' }}"
                       class="cta-btn">
                        <span>تصفح الشقق المتاحة</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ===== INTERSECTION OBSERVER FOR SCROLL ANIMATIONS =====
    const observerOptions = {
        root: null,
        rootMargin: '0px 0px -80px 0px',
        threshold: 0.1
    };

    const scrollObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                // Don't unobserve to allow re-animation if needed
            }
        });
    }, observerOptions);

    // Observe all animated elements
    document.querySelectorAll('.animate-on-scroll, .animate-slide-right, .animate-slide-left, .animate-scale').forEach(el => {
        scrollObserver.observe(el);
    });

    // ===== ANIMATED COUNTER =====
    const counterElements = document.querySelectorAll('.stat-number[data-target]');
    let countersAnimated = false;

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !countersAnimated) {
                countersAnimated = true;
                animateCounters();
            }
        });
    }, { threshold: 0.3 });

    const statsSection = document.getElementById('stats');
    if (statsSection) {
        counterObserver.observe(statsSection);
    }

    function animateCounters() {
        counterElements.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const suffix = counter.getAttribute('data-suffix') || '';
            const duration = 2000;
            const startTime = performance.now();

            function updateCounter(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);

                // Easing function (ease-out cubic)
                const easeOut = 1 - Math.pow(1 - progress, 3);
                const current = Math.round(easeOut * target);

                counter.textContent = current + suffix;

                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target + suffix;
                }
            }

            requestAnimationFrame(updateCounter);
        });
    }

    // ===== PARALLAX EFFECT ON HERO =====
    const hero = document.getElementById('hero');
    const shapes = document.querySelectorAll('.floating-shape');

    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const heroHeight = hero ? hero.offsetHeight : 0;

        if (scrolled < heroHeight) {
            const parallaxSpeed = 0.3;

            shapes.forEach((shape, index) => {
                const speed = parallaxSpeed * (index % 3 + 1) * 0.3;
                shape.style.transform = `translateY(${scrolled * speed}px)`;
            });

            // Parallax on hero content
            const heroContent = document.querySelector('.hero-content');
            if (heroContent) {
                heroContent.style.transform = `translateY(${scrolled * 0.15}px)`;
                heroContent.style.opacity = 1 - (scrolled / heroHeight) * 0.8;
            }
        }
    }, { passive: true });

    // ===== SMOOTH SCROLL FOR INTERNAL LINKS =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ===== DYNAMIC PARTICLE GENERATION =====
    function createParticles() {
        const heroSection = document.getElementById('hero');
        if (!heroSection) return;

        for (let i = 0; i < 15; i++) {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: absolute;
                width: ${Math.random() * 4 + 2}px;
                height: ${Math.random() * 4 + 2}px;
                background: rgba(245, 158, 11, ${Math.random() * 0.5 + 0.2});
                border-radius: 50%;
                top: ${Math.random() * 100}%;
                left: ${Math.random() * 100}%;
                z-index: 1;
                pointer-events: none;
                animation: particleFloat ${Math.random() * 8 + 5}s ease-in-out infinite ${Math.random() * 5}s;
            `;
            heroSection.appendChild(particle);
        }
    }

    createParticles();

    // ===== TILT EFFECT ON FEATURE CARDS (DESKTOP) =====
    if (window.matchMedia('(min-width: 768px)').matches) {
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                const rotateX = (y - centerY) / 20;
                const rotateY = (centerX - x) / 20;

                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-12px) scale(1.03)`;
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0) scale(1)';
            });
        });
    }

    // ===== GLASSMORPHISM GLOW FOLLOW (DESKTOP) =====
    if (window.matchMedia('(min-width: 768px)').matches) {
        document.querySelectorAll('.glass-card').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                card.style.background = `radial-gradient(circle at ${x}px ${y}px, rgba(245, 158, 11, 0.08), rgba(255, 255, 255, 0.05))`;
            });

            card.addEventListener('mouseleave', () => {
                card.style.background = 'rgba(255, 255, 255, 0.05)';
            });
        });
    }

});
</script>
@endsection
