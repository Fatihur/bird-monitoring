<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="api-token" content="{{ config('app.api_token') }}">
    <title>Dashboard Pengusir Burung -IoT</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=JetBrains+Mono:wght@400;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Space Mono', 'JetBrains Mono', monospace;
            background: #E7E5E4; color: #1E2938;
            min-height: 100vh; padding: 24px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 18px 28px; background: #E7E5E4;
            border-radius: 8px; margin-bottom: 28px;
            box-shadow: -4px -4px 10px rgba(255,255,255,0.7), 4px 4px 12px rgba(0,0,0,0.1);
        }
        header h1 { font-size: 1.3rem; font-weight: 700; color: #1E2938; letter-spacing: 0.5px; }
        header h1 span { color: #006666; }
        #clock {
            font-size: 0.85rem; font-variant-numeric: tabular-nums; color: #1E2938;
            background: #E7E5E4; padding: 7px 18px; border-radius: 4px;
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.08), inset -2px -2px 5px rgba(255,255,255,0.6);
        }
        .cards { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px; }
        .card {
            background: #E7E5E4; border-radius: 8px; padding: 22px 26px;
            box-shadow: -4px -4px 10px rgba(255,255,255,0.7), 4px 4px 12px rgba(0,0,0,0.1);
        }
        .card-full { grid-column: 1 / -1; }
        .card-title {
            font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.2px; color: #006666; margin-bottom: 18px;
        }
        .status-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 16px; }
        .status-item {
            text-align: center; padding: 14px 10px;
            background: #E7E5E4; border-radius: 8px;
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.08), inset -2px -2px 5px rgba(255,255,255,0.6);
        }
        .status-label {
            font-size: 0.65rem; color: #1E2938; text-transform: uppercase;
            letter-spacing: 0.8px; margin-bottom: 8px; font-weight: 400; opacity: 0.6;
        }
        .timestamp {
            font-size: 0.8rem; color: #1E2938; text-align: right; opacity: 0.6;
            padding-top: 14px; margin-top: 14px; border-top: 1px solid rgba(0,0,0,0.1);
        }
        .timestamp strong { color: #1E2938; opacity: 0.8; }
        .keterangan-text {
            font-size: 0.85rem; color: #1E2938; margin-top: 14px;
            padding: 12px 16px; background: #E7E5E4;
            border-radius: 4px;
            box-shadow: inset 2px 2px 4px rgba(0,0,0,0.08), inset -2px -2px 4px rgba(255,255,255,0.6);
        }
        .stat-value { font-size: 2.5rem; font-weight: 700; color: #006666; text-align: center; line-height: 1.2; }
        .stat-label { text-align: center; font-size: 0.65rem; color: #1E2938; text-transform: uppercase; letter-spacing: 0.8px; margin-top: 6px; opacity: 0.6; }
        .badge {
            display: inline-block; padding: 5px 16px; border-radius: 4px;
            font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;
            box-shadow: -2px -2px 5px rgba(255,255,255,0.6), 2px 2px 6px rgba(0,0,0,0.08);
        }
        .badge-red { background: #E7E5E4; color: #FF2157; }
        .badge-green { background: #E7E5E4; color: #00A63D; }
        .badge-yellow { background: #E7E5E4; color: #FE9900; }
        .badge-gray { background: #E7E5E4; color: #1E2938; opacity: 0.5; }
        .badge-none { background: #E7E5E4; color: #1E2938; opacity: 0.35; }
        .table-wrapper {
            background: #E7E5E4; border-radius: 8px; overflow: hidden;
            box-shadow: -4px -4px 10px rgba(255,255,255,0.7), 4px 4px 12px rgba(0,0,0,0.1);
        }
        .table-header {
            padding: 14px 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.2px; color: #006666;
            box-shadow: 0 1px 0 rgba(255,255,255,0.5), inset 0 -1px 0 rgba(0,0,0,0.06);
            display: flex; align-items: center; justify-content: space-between;
        }
        .table-header .btn { padding: 4px 12px; font-size: 0.65rem; }
        .pagination {
            display: flex; align-items: center; justify-content: center;
            gap: 6px; padding: 12px 20px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.5), 0 -1px 0 rgba(0,0,0,0.06);
        }
        .page-btn {
            padding: 4px 10px; border-radius: 4px; border: none;
            font-family: 'Space Mono', monospace; font-size: 0.72rem; cursor: pointer;
            background: #E7E5E4; color: #1E2938;
            box-shadow: -2px -2px 4px rgba(255,255,255,0.6), 2px 2px 6px rgba(0,0,0,0.08);
            transition: box-shadow .15s, transform .15s;
        }
        .page-btn:active { box-shadow: inset 2px 2px 4px rgba(0,0,0,0.08), inset -2px -2px 4px rgba(255,255,255,0.6); transform: scale(0.95); }
        .page-btn:disabled { opacity: .3; cursor: not-allowed; }
        .page-btn.active { background: #006666; color: #fff; }
        .page-info { font-size: 0.7rem; color: #1E2938; opacity: 0.6; }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            text-align: left; padding: 10px 14px; font-size: 0.65rem; font-weight: 400;
            text-transform: uppercase; letter-spacing: 0.6px; color: #1E2938; opacity: 0.6;
            background: #E7E5E4; white-space: nowrap;
            box-shadow: inset 0 -1px 0 rgba(0,0,0,0.06);
        }
        tbody td {
            padding: 10px 14px; font-size: 0.82rem; white-space: nowrap; color: #1E2938;
            box-shadow: inset 0 -1px 0 rgba(255,255,255,0.4);
        }
        tbody tr:last-child td { box-shadow: none; }
        tbody tr:hover td { background: rgba(0,102,102,0.04); }
        .text-muted { color: #1E2938; opacity: 0.5; }
        .empty-state { text-align: center; padding: 48px 24px; color: #1E2938; opacity: 0.5; font-size: 0.9rem; }
        .no-wrap { white-space: nowrap; }
        .status-indicator { display: inline-flex; align-items: center; gap: 6px; font-size: 0.75rem; }
        .dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; }
        .dot-green { background: #00A63D; box-shadow: -1px -1px 3px rgba(255,255,255,0.5), 2px 2px 5px rgba(0,0,0,0.1); }
        .dot-red { background: #FF2157; box-shadow: -1px -1px 3px rgba(255,255,255,0.5), 2px 2px 5px rgba(0,0,0,0.1); }
        .dot-gray { background: #1E2938; opacity: 0.4; box-shadow: -1px -1px 3px rgba(255,255,255,0.5), 2px 2px 5px rgba(0,0,0,0.1); }
        .dot-yellow { background: #FE9900; box-shadow: -1px -1px 3px rgba(255,255,255,0.5), 2px 2px 5px rgba(0,0,0,0.1); }
        #last-update { font-size: 0.72rem; color: #1E2938; opacity: 0.5; margin-left: 16px; }

        /* ── Toggle Switch ── */
        .ctrl-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .ctrl-card {
            background: #E7E5E4; border-radius: 8px; padding: 20px 24px;
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.08), inset -2px -2px 5px rgba(255,255,255,0.6);
            display: flex; align-items: center; justify-content: space-between;
            transition: background .3s;
        }
        .ctrl-card.active { background: rgba(0,166,61,0.06); }
        .ctrl-info { display: flex; flex-direction: column; gap: 4px; }
        .ctrl-name { font-size: 0.9rem; font-weight: 700; color: #1E2938; }
        .ctrl-desc { font-size: 0.68rem; color: #1E2938; opacity: 0.5; }
        .toggle-wrapper { display: flex; align-items: center; gap: 10px; }
        .toggle-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; min-width: 32px; text-align: center; }
        .toggle-label.on { color: #00A63D; }
        .toggle-label.off { color: #1E2938; opacity: 0.4; }
        .toggle-switch {
            position: relative; width: 56px; height: 30px; flex-shrink: 0;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
            background: #E7E5E4; border-radius: 30px;
            box-shadow: inset 2px 2px 4px rgba(0,0,0,0.12), inset -2px -2px 4px rgba(255,255,255,0.6);
            transition: background .3s, box-shadow .3s;
        }
        .toggle-slider::before {
            content: ""; position: absolute; height: 22px; width: 22px;
            left: 4px; bottom: 4px;
            background: #E7E5E4; border-radius: 50%;
            box-shadow: -2px -2px 4px rgba(255,255,255,0.7), 2px 2px 5px rgba(0,0,0,0.15);
            transition: transform .25s;
        }
        .toggle-switch input:checked + .toggle-slider { background: #006666; }
        .toggle-switch input:checked + .toggle-slider::before { transform: translateX(26px); }
        .toggle-switch input:disabled + .toggle-slider { cursor: not-allowed; opacity: 0.5; }
        .toggle-spinner {
            display: none; width: 14px; height: 14px; flex-shrink: 0;
            border: 2px solid rgba(0,0,0,.15); border-top-color: #006666;
            border-radius: 50%; animation: spin .6s linear infinite;
        }
        .toggle-spinner.show { display: inline-block; }

        .btn {
            padding: 12px 28px; border-radius: 4px; border: none;
            font-family: 'Space Mono', monospace;
            font-size: 0.78rem; font-weight: 700; cursor: pointer;
            letter-spacing: 0.5px; text-transform: uppercase;
            display: inline-flex; align-items: center; gap: 8px;
            box-shadow: -3px -3px 7px rgba(255,255,255,0.7), 3px 3px 8px rgba(0,0,0,0.1);
            transition: box-shadow .15s, transform .15s;
        }
        .btn:active:not(:disabled) {
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.08), inset -2px -2px 5px rgba(255,255,255,0.6);
            transform: scale(0.97);
        }
        .btn:disabled { opacity: .4; cursor: not-allowed; }
        .btn-danger { background: #E7E5E4; color: #FF2157; }
        .btn-danger:hover:not(:disabled) { background: #E0DEDD; }
        .ml-auto { margin-left: auto; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Toast Notification ── */
        .toast-container {
            position: fixed; top: 20px; right: 20px; z-index: 9999;
            display: flex; flex-direction: column; gap: 8px;
            max-width: 360px; pointer-events: none;
        }
        .toast {
            padding: 12px 18px; border-radius: 6px;
            font-family: 'Space Mono', monospace; font-size: 0.78rem;
            background: #E7E5E4; color: #1E2938; cursor: pointer;
            box-shadow: -3px -3px 7px rgba(255,255,255,0.7), 3px 3px 8px rgba(0,0,0,0.12);
            opacity: 0; transform: translateX(100%);
            transition: opacity .25s, transform .25s;
            display: flex; align-items: center; gap: 8px; pointer-events: auto;
        }
        .toast.show { opacity: 1; transform: translateX(0); }
        .toast.success { border-left: 3px solid #00A63D; }
        .toast.error { border-left: 3px solid #FF2157; }
        .toast.info { border-left: 3px solid #006666; }
        @media (max-width: 768px) {
            body { padding: 16px; }
            header { flex-direction: column; align-items: flex-start; gap: 12px; }
            .cards { grid-template-columns: 1fr; }
            .status-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
            .ctrl-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Dashboard Pengusir Burung <span>- IoT</span></h1>
            <div style="display:flex;align-items:center;gap:12px;">
                <span id="connection-status" class="status-indicator"><span class="dot dot-green"></span> Live</span>
                <span id="last-update">—</span>
                <div id="clock"></div>
            </div>
        </header>

        <div class="cards">
            <div class="card">
                <div class="card-title">Status Terkini</div>
                <div id="status-content">
                    <div class="status-grid">
                        <div class="status-item">
                            <div class="status-label">Status Alat</div>
                            <span id="badge-alat" class="badge badge-gray">—</span>
                        </div>
                        <div class="status-item">
                            <div class="status-label">Deteksi Burung</div>
                            <span id="badge-deteksi" class="badge badge-gray">—</span>
                        </div>
                        <div class="status-item">
                            <div class="status-label">Buzzer</div>
                            <span id="badge-buzzer" class="badge badge-gray">—</span>
                        </div>
                        <div class="status-item">
                            <div class="status-label">Sensor PIR</div>
                            <span id="badge-pir" class="badge badge-gray">—</span>
                        </div>
                    </div>
                    <div id="keterangan" class="keterangan-text" style="display:none;"></div>
                    <div id="timestamp" class="timestamp" style="display:none;"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">Deteksi Hari Ini</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                    <div class="status-item">
                        <div class="stat-value" id="stat-aman" style="font-size:1.8rem;color:#00A63D;">0</div>
                        <div class="stat-label">AMAN</div>
                    </div>
                    <div class="status-item">
                        <div class="stat-value" id="stat-terdeteksi" style="font-size:1.8rem;color:#FF2157;">0</div>
                        <div class="stat-label">TERDETEKSI</div>
                    </div>
                </div>
                <div style="text-align:center;padding-top:10px;border-top:1px solid rgba(0,0,0,0.08);">
                    <span class="stat-label">Total: <strong id="stat-total" style="color:#006666;">0</strong></span>
                </div>
            </div>
        </div>

        <div class="card card-full">
            <div class="card-title">Grafik Deteksi Per Jam (Hari Ini)</div>
            <canvas id="chart-deteksi" height="100"></canvas>
        </div>

        <div class="card card-full">
            <div class="card-title">Kontrol Perangkat</div>
            <div class="ctrl-grid">
                {{-- Buzzer Trigger --}}
                <div class="ctrl-card" id="ctrl-buzzer-card">
                    <div class="ctrl-info">
                        <div class="ctrl-name">🔊 Buzzer</div>
                        <div class="ctrl-desc">Picu buzzer 5 detik (manual)</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span class="toggle-spinner" id="spinner-buzzer"></span>
                        <button class="btn" id="btn-trigger-buzzer" onclick="triggerBuzzer()" style="padding:10px 20px;color:#006666;font-size:0.78rem;">
                            🔔 Picu Buzzer
                        </button>
                    </div>
                </div>

                {{-- PIR Toggle --}}
                <div class="ctrl-card" id="ctrl-pir-card">
                    <div class="ctrl-info">
                        <div class="ctrl-name">📡 Sensor PIR</div>
                        <div class="ctrl-desc">Aktifkan/nonaktifkan deteksi gerak</div>
                    </div>
                    <div class="toggle-wrapper">
                        <span class="toggle-spinner" id="spinner-pir"></span>
                        <span class="toggle-label off" id="label-pir-off">OFF</span>
                        <label class="toggle-switch">
                            <input type="checkbox" id="toggle-pir" onchange="togglePir(this)" disabled>
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="toggle-label on" id="label-pir-on">ON</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="table-wrapper">
            <div class="table-header">
                <span id="riwayat-title">Riwayat Aktifitas</span>
                <button id="btn-clear" onclick="clearHistory()" class="btn btn-danger">✕ Hapus Riwayat</button>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Status Alat</th>
                            <th>Deteksi</th>
                            <th>Buzzer</th>
                            <th>PIR</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="history-body">
                        <tr><td colspan="6" class="empty-state">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
            <div id="pagination" class="pagination"></div>
        </div>
    </div>

    <script>
        const API_TOKEN = document.querySelector('meta[name="api-token"]').getAttribute('content');

        function apiHeaders(extra) {
            var h = { 'X-API-Token': API_TOKEN };
            if (extra) Object.assign(h, extra);
            return h;
        }

        function badgeHtml(value, type, id) {
            const cls = type === 'alat'
                ? (value === 'AKTIF' ? 'badge-green' : 'badge-gray')
                : type === 'deteksi'
                ? (value === 'TERDETEKSI' ? 'badge-red' : (value === 'AMAN' ? 'badge-green' : 'badge-none'))
                : type === 'pir'
                ? (value === 'AKTIF' ? 'badge-green' : 'badge-red')
                : (value === 'ON' ? 'badge-yellow' : 'badge-gray');
            return '<span class="badge ' + cls + '"' + (id ? ' id="' + id + '"' : '') + '>' + (value || '—') + '</span>';
        }

        function escapeHtml(text) {
            const d = document.createElement('div');
            d.textContent = text || '—';
            return d.innerHTML;
        }

        var currentPage = 1;
        var isLoading = false;

        function renderPagination(data) {
            var el = document.getElementById('pagination');
            if (data.last_page <= 1) { el.innerHTML = ''; return; }
            var html = '';
            html += '<button class="page-btn" onclick="goPage(1)"' + (data.current_page <= 1 ? ' disabled' : '') + '>«</button>';
            html += '<button class="page-btn" onclick="goPage(' + (data.current_page - 1) + ')"' + (data.current_page <= 1 ? ' disabled' : '') + '>‹</button>';
            html += '<span class="page-info"> Halaman ' + data.current_page + ' / ' + data.last_page + ' </span>';
            html += '<button class="page-btn" onclick="goPage(' + (data.current_page + 1) + ')"' + (data.current_page >= data.last_page ? ' disabled' : '') + '>›</button>';
            html += '<button class="page-btn" onclick="goPage(' + data.last_page + ')"' + (data.current_page >= data.last_page ? ' disabled' : '') + '>»</button>';
            el.innerHTML = html;
            document.getElementById('riwayat-title').textContent = 'Riwayat Monitoring (' + data.total + ' total)';
        }

        function goPage(page) {
            currentPage = page;
            fetchData();
        }

        function syncToggles(latest) {
            var pirState = latest ? (latest.status_pir || 'AKTIF') : 'AKTIF';
            var pirToggle = document.getElementById('toggle-pir');

            if (!pirToggle.dataset.pending) {
                pirToggle.checked = pirState === 'AKTIF';
                pirToggle.disabled = false;
                updateToggleLabels('pir', pirState);
            }
        }

        function updateToggleLabels(device, state) {
            var onLabel = document.getElementById('label-' + device + '-on');
            var offLabel = document.getElementById('label-' + device + '-off');
            if (state === 'ON') {
                onLabel.className = 'toggle-label on';
                offLabel.className = 'toggle-label off';
            } else {
                onLabel.className = 'toggle-label off';
                offLabel.className = 'toggle-label on';
            }
        }

        function updateDashboard(data) {
            const l = data.latest;
            if (l) {
                document.getElementById('badge-alat').outerHTML = badgeHtml(l.status_alat, 'alat', 'badge-alat');
                document.getElementById('badge-deteksi').outerHTML = badgeHtml(l.deteksi_burung, 'deteksi', 'badge-deteksi');
                document.getElementById('badge-buzzer').outerHTML = badgeHtml(l.status_buzzer, 'buzzer', 'badge-buzzer');
                document.getElementById('badge-pir').outerHTML = badgeHtml(l.status_pir || 'AKTIF', 'pir', 'badge-pir');

                syncToggles(l);

                const ket = document.getElementById('keterangan');
                if (l.keterangan) {
                    ket.textContent = l.keterangan;
                    ket.style.display = 'block';
                } else {
                    ket.style.display = 'none';
                }

                const ts = document.getElementById('timestamp');
                const d = new Date(l.created_at);
                const pad = n => String(n).padStart(2, '0');
                ts.innerHTML = 'Terakhir diperbarui: <strong>' + pad(d.getDate()) + '/' + pad(d.getMonth()+1) + '/' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds()) + '</strong>';
                ts.style.display = 'block';
            }

            document.getElementById('stat-total').textContent = data.todayCount;
            document.getElementById('stat-aman').textContent = data.amanCount || 0;
            document.getElementById('stat-terdeteksi').textContent = data.terdeteksiCount || 0;

            const tbody = document.getElementById('history-body');
            if (data.histories && data.histories.length > 0) {
                tbody.innerHTML = data.histories.map(function(r) {
                    const d = new Date(r.created_at);
                    const pad = n => String(n).padStart(2, '0');
                    const waktu = pad(d.getDate()) + '/' + pad(d.getMonth()+1) + '/' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
                    return '<tr>'
                        + '<td class="no-wrap">' + waktu + '</td>'
                        + '<td>' + badgeHtml(r.status_alat, 'alat') + '</td>'
                        + '<td>' + badgeHtml(r.deteksi_burung, 'deteksi') + '</td>'
                        + '<td>' + badgeHtml(r.status_buzzer, 'buzzer') + '</td>'
                        + '<td>' + badgeHtml(r.status_pir || 'AKTIF', 'pir') + '</td>'
                        + '<td>' + escapeHtml(r.keterangan) + '</td>'
                        + '</tr>';
                }).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="empty-state">Belum ada data monitoring.</td></tr>';
            }

            document.getElementById('last-update').textContent = new Date().toLocaleTimeString('id-ID');

            if (window.deteksiChart && data.chart) {
                deteksiChart.data.datasets[0].data = data.chart;
                deteksiChart.update();
            }
        }

        function fetchData() {
            if (isLoading) return;
            isLoading = true;

            var statusEl = document.getElementById('connection-status');
            statusEl.innerHTML = '<span class="dot dot-yellow"></span> Memuat...';

            fetch('/json?page=' + currentPage)
                .then(function(r) {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(function(data) {
                    updateDashboard(data);
                    renderPagination(data);
                    statusEl.innerHTML = data.is_online
                        ? '<span class="dot dot-green"></span> Live'
                        : '<span class="dot dot-red"></span> Putus';
                })
                .catch(function() {
                    statusEl.innerHTML = '<span class="dot dot-red"></span> Putus';
                })
                .finally(function() {
                    isLoading = false;
                });
        }

        function togglePir(checkbox) {
            var spinner = document.getElementById('spinner-pir');

            checkbox.dataset.pending = '1';
            checkbox.disabled = true;
            spinner.classList.add('show');

            fetch('/api/pir/toggle', {
                method: 'POST',
                headers: apiHeaders({ 'Content-Type': 'application/json' }),
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    var newState = data.pir;
                    checkbox.checked = newState === 'AKTIF';
                    updateToggleLabels('pir', newState);
                    document.getElementById('badge-pir').outerHTML = badgeHtml(newState, 'pir', 'badge-pir');
                    showToast('✓ Sensor PIR ' + newState, 'success');
                } else {
                    showToast('✗ ' + (data.message || 'Gagal'), 'error');
                }
            })
            .catch(function() {
                showToast('✗ Server tidak merespon', 'error');
            })
            .finally(function() {
                delete checkbox.dataset.pending;
                checkbox.disabled = false;
                spinner.classList.remove('show');
            });
        }

        function triggerBuzzer() {
            var spinner = document.getElementById('spinner-buzzer');
            var btn = document.getElementById('btn-trigger-buzzer');

            btn.disabled = true;
            spinner.classList.add('show');

            fetch('/api/buzzer/trigger', {
                method: 'POST',
                headers: apiHeaders({ 'Content-Type': 'application/json' }),
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    document.getElementById('badge-buzzer').outerHTML = badgeHtml('ON', 'buzzer', 'badge-buzzer');
                    showToast('✓ Buzzer dipicu (5 detik)', 'success');
                } else {
                    showToast('✗ ' + (data.message || 'Gagal'), 'error');
                }
            })
            .catch(function() {
                showToast('✗ Server tidak merespon', 'error');
            })
            .finally(function() {
                btn.disabled = false;
                spinner.classList.remove('show');
            });
        }

        function clearHistory() {
            if (!confirm('Hapus semua riwayat monitoring?')) return;

            var btn = document.getElementById('btn-clear');
            btn.disabled = true;
            btn.textContent = '⏳ Menghapus...';

            fetch('/api/history', {
                method: 'DELETE',
                headers: apiHeaders(),
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    updateDashboard(data);
                    renderPagination(data);
                    currentPage = 1;
                    showToast('✓ Riwayat dihapus', 'success');
                } else {
                    showToast('✗ ' + (data.message || 'Gagal'), 'error');
                }
            })
            .catch(function() {
                showToast('✗ Server tidak merespon', 'error');
            })
            .finally(function() {
                btn.disabled = false;
                btn.innerHTML = '✕ Hapus Riwayat';
            });
        }

        function updateClock() {
            const now = new Date();
            const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            const pad = n => String(n).padStart(2, '0');
            document.getElementById('clock').textContent =
                days[now.getDay()] + ', ' + pad(now.getDate()) + '/' + pad(now.getMonth()+1) + '/' + now.getFullYear()
                + ' ' + pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
        }

        updateClock();
        setInterval(updateClock, 1000);

        window.deteksiChart = new Chart(document.getElementById('chart-deteksi'), {
            type: 'bar',
            data: {
                labels: ['00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'],
                datasets: [{
                    label: 'TERDETEKSI',
                    data: [],
                    backgroundColor: '#FF2157',
                    borderRadius: 3,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        ticks: { font: { family: 'Space Mono' }, color: '#1E2938' },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { family: 'Space Mono' }, color: '#1E2938' },
                        grid: { color: 'rgba(0,0,0,0.06)' }
                    }
                }
            }
        });

        fetchData();
        setInterval(fetchData, 5000);

        function showToast(message, type) {
            type = type || 'info';
            var container = document.getElementById('toast-container');
            var toast = document.createElement('div');
            toast.className = 'toast ' + type;
            toast.innerHTML = message;
            container.appendChild(toast);

            toast.offsetHeight;
            toast.classList.add('show');

            var timer = setTimeout(function() {
                dismissToast(toast);
            }, 3000);

            toast.addEventListener('click', function() {
                clearTimeout(timer);
                dismissToast(toast);
            });

            while (container.children.length > 3) {
                container.removeChild(container.firstChild);
            }
        }

        function dismissToast(toast) {
            toast.classList.remove('show');
            setTimeout(function() {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 250);
        }
    </script>
    <div id="toast-container" class="toast-container"></div>
</body>
</html>
