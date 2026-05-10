<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Key-Value Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --maroon:       #800000;
            --maroon-dark:  #5c0000;
            --maroon-light: #9a1a1a;
            --maroon-muted: #f9f0f0;
        }

        body { font-family: 'Inter', sans-serif; background: #f4f4f5; color: #1a1a1a; }

        /* ── Navbar ── */
        .navbar { background-color: var(--maroon); }
        .navbar-brand { font-weight: 700; font-size: 1.1rem; letter-spacing: -0.01em; color: #fff !important; }

        /* ── Tabs ── */
        .nav-tabs { border-bottom: 2px solid #dee2e6; }
        .nav-tabs .nav-link {
            color: #6c757d; font-weight: 500; font-size: 0.9rem;
            border: none; border-bottom: 2px solid transparent;
            margin-bottom: -2px; padding: 0.65rem 1.25rem; border-radius: 0;
            transition: color 0.15s, border-color 0.15s;
        }
        .nav-tabs .nav-link:hover  { color: var(--maroon); border-bottom-color: #c9a0a0; }
        .nav-tabs .nav-link.active { color: var(--maroon); font-weight: 600; border-bottom-color: var(--maroon); background: transparent; }

        /* ── Card ── */
        .card { border: 1px solid #e4e4e7; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        .tab-card { border-top: none; border-radius: 0 0 10px 10px; }
        .card-header { background: #fff; border-bottom: 1px solid #e4e4e7; padding: 0.9rem 1.25rem; }
        .section-title { font-size: 0.78rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: #6c757d; }

        /* ── Method badges ── */
        .badge-post { background:#dcfce7; color:#166534; font-family:monospace; font-size:0.7rem; font-weight:700; padding:3px 8px; border-radius:4px; }
        .badge-get  { background:#dbeafe; color:#1e40af; font-family:monospace; font-size:0.7rem; font-weight:700; padding:3px 8px; border-radius:4px; }

        /* ── Inputs ── */
        .form-label { font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.35rem; }
        .form-control, .form-select {
            border-color: #d1d5db; border-radius: 7px; font-size: 0.9rem; color: #1a1a1a;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--maroon-light);
            box-shadow: 0 0 0 3px rgba(128,0,0,0.12);
        }
        .form-text { font-size: 0.78rem; color: #9ca3af; }

        /* ── Buttons ── */
        .btn-maroon {
            background-color: var(--maroon); border-color: var(--maroon); color: #fff;
            font-weight: 600; font-size: 0.9rem; border-radius: 7px; padding: 0.55rem 1.5rem;
            transition: background-color 0.15s, border-color 0.15s;
        }
        .btn-maroon:hover, .btn-maroon:focus { background-color: var(--maroon-dark); border-color: var(--maroon-dark); color: #fff; }
        .btn-maroon:active  { background-color: #440000; border-color: #440000; color: #fff; }
        .btn-maroon:disabled { background-color: #b07070; border-color: #b07070; color: #fff; }
        .btn-maroon:focus   { box-shadow: 0 0 0 3px rgba(128,0,0,0.25); }

        .btn-outline-maroon {
            border-color: var(--maroon); color: var(--maroon);
            font-weight: 500; font-size: 0.85rem; border-radius: 7px; padding: 0.4rem 0.9rem;
            transition: all 0.15s;
        }
        .btn-outline-maroon:hover { background-color: var(--maroon); color: #fff; }

        /* ── Response panel ── */
        .response-panel {
            background: #0f172a; color: #cbd5e1; border-radius: 8px;
            padding: 1rem 1.25rem;
            font-family: 'Cascadia Code', 'Fira Code', ui-monospace, monospace;
            font-size: 0.8rem; line-height: 1.7; white-space: pre-wrap; word-break: break-all; min-height: 80px;
        }

        /* ── Status badges ── */
        .status-2xx { background:#dcfce7; color:#166534; font-family:monospace; font-size:0.72rem; font-weight:700; padding:3px 8px; border-radius:4px; }
        .status-err  { background:#fee2e2; color:#991b1b; font-family:monospace; font-size:0.72rem; font-weight:700; padding:3px 8px; border-radius:4px; }

        /* ── Table ── */
        .table { font-size: 0.875rem; margin-bottom: 0; }
        .table thead th {
            font-size: 0.72rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.05em; color: #6c757d;
            background: #fafafa; border-bottom: 2px solid #e4e4e7; white-space: nowrap;
        }
        .table td { vertical-align: middle; color: #374151; }
        .table-hover tbody tr:hover { background-color: var(--maroon-muted); }

        .key-chip {
            display: inline-block; background: #f3f4f6; color: #111827;
            font-family: monospace; font-size: 0.8rem; font-weight: 600;
            padding: 2px 10px; border-radius: 5px; border: 1px solid #e5e7eb;
        }

        .value-preview {
            font-family: monospace; font-size: 0.8rem; color: #374151;
            max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
            display: inline-block; vertical-align: middle;
        }

        .btn-view {
            font-size: 0.72rem; font-weight: 600; color: var(--maroon);
            background: var(--maroon-muted); border: 1px solid #e8c8c8;
            border-radius: 4px; padding: 1px 7px; cursor: pointer; white-space: nowrap;
            transition: background 0.12s;
        }
        .btn-view:hover { background: #f0d0d0; }

        /* ── DataTables layout ── */
        div.dt-container { padding: 0; }

        div.dt-container .dt-layout-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.7rem 1.25rem; gap: 1rem;
        }
        div.dt-container .dt-layout-row:first-child { border-bottom: 1px solid #e4e4e7; background: #fafafa; }
        div.dt-container .dt-layout-row:last-child  { border-top:    1px solid #e4e4e7; background: #fafafa; border-radius: 0 0 10px 10px; }
        div.dt-container .dt-layout-row.dt-layout-table { padding: 0; }

        div.dt-container .dt-search { display: flex; align-items: center; gap: 0.5rem; }
        div.dt-container .dt-search input {
            border: 1px solid #d1d5db; border-radius: 7px; font-size: 0.875rem;
            padding: 0.4rem 0.75rem; outline: none; transition: border-color 0.15s, box-shadow 0.15s;
            min-width: 220px;
        }
        div.dt-container .dt-search input:focus {
            border-color: var(--maroon-light); box-shadow: 0 0 0 3px rgba(128,0,0,0.12);
        }
        div.dt-container .dt-info   { font-size: 0.8rem; color: #6c757d; white-space: nowrap; }
        div.dt-container .dt-length label { font-size: 0.8rem; color: #6c757d; display: flex; align-items: center; gap: 0.4rem; white-space: nowrap; }
        div.dt-container .dt-length select {
            border: 1px solid #d1d5db; border-radius: 7px; font-size: 0.8rem; padding: 0.25rem 0.5rem;
        }
        div.dt-container .dt-paging .dt-paging-button {
            border-radius: 6px !important; font-size: 0.8rem;
        }
        div.dt-container .dt-paging .dt-paging-button.current,
        div.dt-container .dt-paging .dt-paging-button.current:hover {
            background: var(--maroon) !important; border-color: var(--maroon) !important; color: #fff !important;
        }
        div.dt-container .dt-paging .dt-paging-button:hover {
            background: var(--maroon-muted) !important; border-color: #d1d5db !important; color: var(--maroon) !important;
        }
        table.dataTable thead th.dt-orderable-asc:hover,
        table.dataTable thead th.dt-orderable-desc:hover { color: var(--maroon); }

        /* ── JSON modal ── */
        .json-viewer {
            background: #0f172a; color: #e2e8f0; border-radius: 8px;
            padding: 1.1rem 1.25rem; font-family: 'Cascadia Code', 'Fira Code', ui-monospace, monospace;
            font-size: 0.8rem; line-height: 1.8; white-space: pre; overflow: auto; max-height: 420px;
        }
        .json-key    { color: #f87171; }
        .json-str    { color: #86efac; }
        .json-num    { color: #93c5fd; }
        .json-bool   { color: #c084fc; }
        .json-null   { color: #94a3b8; }
        .json-punct  { color: #94a3b8; }

        /* ── Spinner ── */
        @keyframes spin { to { transform: rotate(360deg); } }
        .btn-spinner {
            display: inline-block; width: 14px; height: 14px;
            border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff;
            border-radius: 50%; animation: spin 0.65s linear infinite;
            vertical-align: -2px; margin-right: 6px;
        }

        /* ── Empty state ── */
        .empty-state { padding: 3.5rem 1rem; text-align: center; color: #9ca3af; }
        .empty-state svg { width: 40px; height: 40px; margin: 0 auto 0.75rem; opacity: 0.4; display: block; }
        .empty-state p { margin: 0; font-size: 0.9rem; }
        .empty-state small { font-size: 0.8rem; }
    </style>
</head>
<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg px-3 px-md-4">
        <a class="navbar-brand" href="#">
            <svg class="me-2" style="width:18px;height:18px;vertical-align:-3px;fill:none;stroke:#fff;stroke-width:2;stroke-linecap:round;stroke-linejoin:round" viewBox="0 0 24 24">
                <ellipse cx="12" cy="5" rx="9" ry="3"/>
                <path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"/>
                <path d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3"/>
            </svg>
            Key-Value Store
        </a>
        <span class="ms-auto">
            <a href="/api/documentation" target="_blank"
               style="color:rgba(255,255,255,0.6);font-size:0.82rem;text-decoration:none;">
                API Docs ↗
            </a>
        </span>
    </nav>

    {{-- Main --}}
    <div class="container-lg py-5">

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-0" id="kvTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-all" type="button">All Keys and Values</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-store" type="button">Create or Modify Key-Value</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-get" type="button">Get Key Value</button>
            </li>
        </ul>

        <div class="tab-content">

            {{-- Tab: All Keys --}}
            <div class="tab-pane fade show active" id="tab-all" role="tabpanel">
                <div class="card tab-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-get">GET</span>
                            <code style="font-size:0.85rem;color:#374151;">/api/key_values</code>
                        </div>
                        <button onclick="window.location.reload()" class="btn btn-outline-maroon btn-sm d-flex align-items-center gap-1">
                            <svg style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round" viewBox="0 0 24 24">
                                <polyline points="1 4 1 10 7 10"/>
                                <path d="M3.51 15a9 9 0 1 0 .49-4.95"/>
                            </svg>
                            Refresh
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <table id="kv-table" class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="ps-4" style="width:20%">Key</th>
                                    <th style="width:40%">Value</th>
                                    <th style="width:22%">Recorded At</th>
                                    <th style="width:18%">Unix Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($keyValues as $item)
                                    @if ($item->latestValue)
                                    <tr>
                                        <td class="ps-4"><span class="key-chip">{{ $item->key }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="value-preview">{{ json_encode($item->latestValue->value) }}</span>
                                                <button class="btn-view"
                                                    onclick="viewJson({{ json_encode(json_encode($item->latestValue->value, JSON_PRETTY_PRINT)) }}, {{ json_encode($item->key) }})">
                                                    View
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-muted">{{ $item->latestValue->recorded_at->format('Y-m-d H:i:s') }}</td>
                                        <td><code style="font-size:0.8rem;color:#6c757d;">{{ $item->latestValue->recorded_at->timestamp }}</code></td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-state">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <ellipse cx="12" cy="5" rx="9" ry="3"/>
                                                    <path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"/>
                                                    <path d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3"/>
                                                </svg>
                                                <p>No keys stored yet.</p>
                                                <small>Use the <strong>Store Value</strong> tab to add your first entry.</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tab: Store Value --}}
            <div class="tab-pane fade" id="tab-store" role="tabpanel">
                <div class="card tab-card">
                    <div class="card-header d-flex align-items-center gap-2">
                        <span class="badge-post">POST</span>
                        <code style="font-size:0.85rem;color:#374151;">/api/key_values</code>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted mb-4" style="font-size:0.875rem;">
                            Store a value for a key. Creates the key if it doesn't exist, then records the value with the current timestamp.
                        </p>
                        <form id="store-form">
                            <div class="mb-3">
                                <label class="form-label">Key</label>
                                <input id="store-key" type="text" class="form-control" placeholder="e.g. temperature" autocomplete="off">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Value</label>
                                <textarea id="store-value" class="form-control font-monospace" rows="4" placeholder='{"celsius": 36.6}'></textarea>
                                <div class="form-text">Accepts any value — JSON object, array, number, boolean, or plain text.</div>
                            </div>
                            <button type="submit" id="store-btn" class="btn btn-maroon">Send Request</button>
                        </form>
                        <div id="store-result" class="mt-4 d-none">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="section-title">Response</span>
                                <span id="store-status"></span>
                            </div>
                            <div id="store-body" class="response-panel"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab: Get Value --}}
            <div class="tab-pane fade" id="tab-get" role="tabpanel">
                <div class="card tab-card">
                    <div class="card-header d-flex align-items-center gap-2">
                        <span class="badge-get">GET</span>
                        <code style="font-size:0.85rem;color:#374151;">/api/key_values/{key}</code>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted mb-4" style="font-size:0.875rem;">
                            Retrieve the latest value for a key. Provide a Unix timestamp to get the most recent value recorded at or before that point in time.
                        </p>
                        <form id="get-form">
                            <div class="mb-3">
                                <label class="form-label">Key</label>
                                <input id="get-key" type="text" class="form-control" placeholder="e.g. temperature" autocomplete="off">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Timestamp <span class="text-muted fw-normal">(optional)</span></label>
                                <input id="get-timestamp" type="number" class="form-control" placeholder="e.g. 1715000000">
                                <div class="form-text">Unix timestamp. Leave blank to get the latest value.</div>
                            </div>
                            <button type="submit" id="get-btn" class="btn btn-maroon">Send Request</button>
                        </form>
                        <div id="get-result" class="mt-4 d-none">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="section-title">Response</span>
                                <span id="get-status"></span>
                            </div>
                            <div id="get-body" class="response-panel"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /.tab-content --}}
    </div>{{-- /.container-lg --}}

    {{-- JSON viewer modal --}}
    <div class="modal fade" id="jsonModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:10px;overflow:hidden;">
                <div class="modal-header" style="background:#0f172a;border-bottom:1px solid #1e293b;padding:0.9rem 1.25rem;">
                    <h6 class="modal-title mb-0" style="color:#e2e8f0;font-size:0.875rem;font-family:monospace;" id="jsonModalLabel"></h6>
                    <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="jsonModalBody" class="json-viewer" style="border-radius:0;"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // ── DataTable ─────────────────────────────────────────────
        $('#kv-table').DataTable({
            pageLength: 25,
            language: {
                search: '',
                searchPlaceholder: 'Search keys, values…',
                lengthMenu:  'Show _MENU_ entries',
                info:        'Showing _START_–_END_ of _TOTAL_ keys',
                infoEmpty:   'No keys found',
                emptyTable:  'No keys stored yet.',
                zeroRecords: 'No keys match your search.',
            },
            columnDefs: [
                { orderable: false, targets: [1, 3] },
            ],
            order: [[0, 'asc']],
        });

        // ── JSON syntax highlighter ───────────────────────────────
        function highlight(json) {
            return json.replace(
                /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
                (match) => {
                    if (/^"/.test(match)) {
                        return /:$/.test(match)
                            ? `<span class="json-key">${match}</span>`
                            : `<span class="json-str">${match}</span>`;
                    }
                    if (/true|false/.test(match)) return `<span class="json-bool">${match}</span>`;
                    if (/null/.test(match))        return `<span class="json-null">${match}</span>`;
                    return `<span class="json-num">${match}</span>`;
                }
            );
        }

        function viewJson(prettyJson, key) {
            document.getElementById('jsonModalLabel').textContent = key;
            document.getElementById('jsonModalBody').innerHTML = highlight(prettyJson);
            new bootstrap.Modal(document.getElementById('jsonModal')).show();
        }

        // ── Form helpers ──────────────────────────────────────────
        function setLoading(btn, loading) {
            btn.disabled = loading;
            if (loading) {
                btn.dataset.label = btn.innerHTML;
                btn.innerHTML = '<span class="btn-spinner"></span>Sending…';
            } else {
                btn.innerHTML = btn.dataset.label;
            }
        }

        function showResult(resultEl, statusEl, bodyEl, status, data) {
            resultEl.classList.remove('d-none');
            const ok = status >= 200 && status < 300;
            statusEl.className = ok ? 'status-2xx' : 'status-err';
            statusEl.textContent = status + (ok ? ' OK' : ' Error');
            bodyEl.innerHTML = highlight(JSON.stringify(data, null, 2));
        }

        // ── Store ─────────────────────────────────────────────────
        document.getElementById('store-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('store-btn');
            const key = document.getElementById('store-key').value.trim();
            const raw = document.getElementById('store-value').value.trim();

            let value;
            try { value = JSON.parse(raw); } catch { value = raw; }

            setLoading(btn, true);
            try {
                const res = await fetch('/api/key_values', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ key, value }),
                });
                showResult(
                    document.getElementById('store-result'),
                    document.getElementById('store-status'),
                    document.getElementById('store-body'),
                    res.status, await res.json()
                );
            } finally {
                setLoading(btn, false);
            }
        });

        // ── Get ───────────────────────────────────────────────────
        document.getElementById('get-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('get-btn');
            const key = document.getElementById('get-key').value.trim();
            const ts  = document.getElementById('get-timestamp').value.trim();
            const url = '/api/key_values/' + encodeURIComponent(key) + (ts ? '?timestamp=' + ts : '');

            setLoading(btn, true);
            try {
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                showResult(
                    document.getElementById('get-result'),
                    document.getElementById('get-status'),
                    document.getElementById('get-body'),
                    res.status, await res.json()
                );
            } finally {
                setLoading(btn, false);
            }
        });
    </script>
</body>
</html>
