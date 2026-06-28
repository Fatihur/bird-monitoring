<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengusir Burung - Monitoring</title>
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
        .status-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 16px; }
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
        }
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
        #last-update { font-size: 0.72rem; color: #1E2938; opacity: 0.5; margin-left: 16px; }
        .ctrl-bar { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
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
        .btn-on { background: #006666; color: #fff; }
        .btn-on:hover:not(:disabled) { background: #007a7a; }
        .btn-off { background: #E7E5E4; color: #1E2938; }
        .btn-off:hover:not(:disabled) { background: #E0DEDD; }
        .ctrl-feedback { font-size: 0.78rem; color: #1E2938; min-width: 140px; opacity: 0.6; }
        .ctrl-feedback.ok { color: #00A63D; opacity: 1; }
        .ctrl-feedback.err { color: #FF2157; opacity: 1; }
        .spinner {
            display: inline-block; width: 14px; height: 14px;
            border: 2px solid rgba(0,0,0,.15); border-top-color: #006666;
            border-radius: 50%; animation: spin .6s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (max-width: 768px) {
            body { padding: 16px; }
            header { flex-direction: column; align-items: flex-start; gap: 12px; }
            .cards { grid-template-columns: 1fr; }
            .status-grid { grid-template-columns: 1fr; gap: 12px; }
            .ctrl-bar { flex-direction: column; align-items: stretch; }
            .btn { justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Sistem Pengusir Burung <span>- Monitoring</span></h1>
            <div style="display:flex;align-items:center;gap:12px;">
                <span id="connection-status" class="status-indicator"><span class="dot dot-green"></span> Live</span>
                <span id="last-update">—</span>
                <div id="clock"></div>
                <button onclick="fetchData()" id="btn-refresh" class="btn btn-off" style="padding:6px 14px;font-size:0.7rem;">↻ Refresh</button>
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
                            <div class="status-label">Status Buzzer</div>
                            <span id="badge-buzzer" class="badge badge-gray">—</span>
                        </div>
                    </div>
                    <div id="keterangan" class="keterangan-text" style="display:none;"></div>
                    <div id="timestamp" class="timestamp" style="display:none;"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">Deteksi Hari Ini</div>
                <div id="stat-value" class="stat-value">0</div>
                <div class="stat-label">total deteksi</div>
            </div>
        </div>

        <div class="card card-full">
            <div class="card-title">Kontrol Buzzer</div>
            <div class="ctrl-bar">
                <button id="btn-buzzer-on" class="btn btn-on" onclick="buzzerOn()">Nyalakan Buzzer</button>
                <button id="btn-buzzer-off" class="btn btn-off" onclick="buzzerOff()">Matikan Buzzer</button>
                <span id="buzzer-status" class="ctrl-feedback">—</span>
            </div>
        </div>

        <div class="table-wrapper">
            <div class="table-header">Riwayat Monitoring (50 terakhir)</div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Status Alat</th>
                            <th>Deteksi</th>
                            <th>Buzzer</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="history-body">
                        <tr><td colspan="5" class="empty-state">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function badgeHtml(value, type, id) {
            const cls = type === 'alat'
                ? (value === 'AKTIF' ? 'badge-green' : 'badge-gray')
                : type === 'deteksi'
                ? (value === 'TERDETEKSI' ? 'badge-red' : (value === 'AMAN' ? 'badge-green' : 'badge-none'))
                : (value === 'ON' ? 'badge-yellow' : 'badge-gray');
            return `<span class="badge ${cls}"${id ? ' id="' + id + '"' : ''}>${value || '—'}</span>`;
        }

        function escapeHtml(text) {
            const d = document.createElement('div');
            d.textContent = text || '—';
            return d.innerHTML;
        }

        function updateDashboard(data) {
            const l = data.latest;
            if (l) {
                document.getElementById('badge-alat').outerHTML = badgeHtml(l.status_alat, 'alat', 'badge-alat');
                document.getElementById('badge-deteksi').outerHTML = badgeHtml(l.deteksi_burung, 'deteksi', 'badge-deteksi');
                document.getElementById('badge-buzzer').outerHTML = badgeHtml(l.status_buzzer, 'buzzer', 'badge-buzzer');

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

            document.getElementById('stat-value').textContent = data.todayCount;

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
                        + '<td>' + escapeHtml(r.keterangan) + '</td>'
                        + '</tr>';
                }).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="empty-state">Belum ada data monitoring.</td></tr>';
            }

            document.getElementById('last-update').textContent = new Date().toLocaleTimeString('id-ID');
        }

        function fetchData() {
            var statusEl = document.getElementById('connection-status');
            fetch('/json')
                .then(function(r) {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(function(data) {
                    updateDashboard(data);
                    statusEl.innerHTML = '<span class="dot dot-green"></span> Live';
                })
                .catch(function() {
                    statusEl.innerHTML = '<span class="dot dot-red"></span> Putus';
                });
        }

        function buzzerOn() {
            var btn = document.getElementById('btn-buzzer-on');
            var status = document.getElementById('buzzer-status');
            btn.disabled = true;
            status.className = 'ctrl-feedback';
            status.innerHTML = '<span class="spinner"></span> Mengirim...';
            fetch('/api/buzzer/on', { method: 'POST' })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        status.className = 'ctrl-feedback ok';
                        status.textContent = '✓ Buzzer ON terkirim';
                        document.getElementById('badge-buzzer').outerHTML = badgeHtml('ON', 'buzzer', 'badge-buzzer');
                    } else {
                        status.className = 'ctrl-feedback err';
                        status.textContent = '✗ Gagal: ' + (data.message || 'unknown');
                    }
                })
                .catch(function() {
                    status.className = 'ctrl-feedback err';
                    status.textContent = '✗ Gagal — server tidak merespon';
                })
                .finally(function() { btn.disabled = false; });
        }

        function buzzerOff() {
            var btn = document.getElementById('btn-buzzer-off');
            var status = document.getElementById('buzzer-status');
            btn.disabled = true;
            status.className = 'ctrl-feedback';
            status.innerHTML = '<span class="spinner"></span> Mengirim...';
            fetch('/api/buzzer/off', { method: 'POST' })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        status.className = 'ctrl-feedback ok';
                        status.textContent = '✓ Buzzer OFF terkirim';
                        document.getElementById('badge-buzzer').outerHTML = badgeHtml('OFF', 'buzzer', 'badge-buzzer');
                    } else {
                        status.className = 'ctrl-feedback err';
                        status.textContent = '✗ Gagal: ' + (data.message || 'unknown');
                    }
                })
                .catch(function() {
                    status.className = 'ctrl-feedback err';
                    status.textContent = '✗ Gagal — server tidak merespon';
                })
                .finally(function() { btn.disabled = false; });
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
        fetchData();
        setInterval(fetchData, 3000);
    </script>
</body>
</html>
