<x-filament-widgets::widget>
    <div class="dashboard-cards-grid-pro">
        @foreach($stats as $stat)
            <a href="{{ $stat['url'] ?? '#' }}" class="dashboard-card-pro {{ $stat['class'] }}">
                <div class="dashboard-card-bg-icon">{{ $stat['icon_bg'] }}</div>

                <div class="dashboard-card-head-pro">
                    <div>
                        <div class="dashboard-card-label-pro">{{ $stat['label'] }}</div>
                        <div class="dashboard-card-desc-pro">{{ $stat['desc'] }}</div>
                    </div>
                    <div class="dashboard-card-icon-pro">{{ $stat['icon'] }}</div>
                </div>

                <div class="dashboard-card-number-wrap">
                    <div class="dashboard-card-value-pro js-count-up" data-target="{{ $stat['value'] }}">0</div>
                </div>

                <div class="dashboard-card-footer-pro">
                    <span>اليوم: {{ $stat['today'] }}</span>
                    <span>عرض القسم</span>
                </div>
            </a>
        @endforeach
    </div>

    <style>
        .dashboard-cards-grid-pro{
            display:grid;
            grid-template-columns:repeat(3,minmax(0,1fr));
            gap:16px;
        }

        .dashboard-card-pro{
            position:relative;
            border-radius:24px;
            padding:24px;
            color:#fff !important;
            text-decoration:none !important;
            box-shadow:0 14px 35px rgba(0,0,0,.12);
            min-height:175px;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            overflow:hidden;
            transition:all .22s ease;
        }

        .dashboard-card-pro:hover{
            transform:translateY(-6px);
            box-shadow:0 24px 50px rgba(0,0,0,.18);
            color:#fff !important;
        }

        .dashboard-card-bg-icon{
            position:absolute;
            inset-inline-start:18px;
            bottom:8px;
            font-size:5.2rem;
            opacity:.10;
            line-height:1;
            pointer-events:none;
            user-select:none;
        }

        .dashboard-card-head-pro{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:12px;
            position:relative;
            z-index:2;
        }

        .dashboard-card-label-pro{
            font-size:1.08rem;
            font-weight:800;
            line-height:1.6;
        }

        .dashboard-card-desc-pro{
            font-size:.88rem;
            opacity:.92;
            margin-top:4px;
        }

        .dashboard-card-icon-pro{
            font-size:1.9rem;
            line-height:1;
            opacity:.96;
        }

        .dashboard-card-number-wrap{
            position:relative;
            z-index:2;
        }

        .dashboard-card-value-pro{
            font-size:2.4rem;
            font-weight:900;
            line-height:1.1;
            letter-spacing:.5px;
        }

        .dashboard-card-footer-pro{
            position:relative;
            z-index:2;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            font-size:.9rem;
            opacity:.95;
        }

        .dashboard-card-green{ background:linear-gradient(135deg,#16a34a,#22c55e); }
        .dashboard-card-blue{ background:linear-gradient(135deg,#2563eb,#3b82f6); }
        .dashboard-card-amber{ background:linear-gradient(135deg,#d97706,#f59e0b); }
        .dashboard-card-red{ background:linear-gradient(135deg,#dc2626,#ef4444); }
        .dashboard-card-indigo{ background:linear-gradient(135deg,#4f46e5,#6366f1); }
        .dashboard-card-slate{ background:linear-gradient(135deg,#334155,#475569); }

        @media (max-width: 1024px){
            .dashboard-cards-grid-pro{
                grid-template-columns:repeat(2,minmax(0,1fr));
            }
        }

        @media (max-width: 640px){
            .dashboard-cards-grid-pro{
                grid-template-columns:1fr;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.js-count-up').forEach((el) => {
                const target = parseInt(el.dataset.target || '0', 10);
                const duration = 900;
                const startTime = performance.now();

                function update(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const value = Math.floor(progress * target);
                    el.textContent = value.toLocaleString('ar-SA');

                    if (progress < 1) {
                        requestAnimationFrame(update);
                    } else {
                        el.textContent = target.toLocaleString('ar-SA');
                    }
                }

                requestAnimationFrame(update);
            });
        });
    </script>
</x-filament-widgets::widget>
