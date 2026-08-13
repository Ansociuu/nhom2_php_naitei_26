<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Database Viewer — {{ config('app.name', 'Laravel') }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f4f5f7;
            color: #1a1a2e;
            padding: 2rem;
            line-height: 1.5;
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: .5rem;
            letter-spacing: -0.02em;
        }

        .subtitle {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        .table-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            background: #fafafa;
        }

        .table-name {
            font-size: 1rem;
            font-weight: 600;
            font-family: 'SF Mono', SFMono-Regular, Consolas, monospace;
            color: #1e40af;
        }

        .table-meta {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .table-meta span {
            display: inline-block;
            background: #f3f4f6;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            margin-left: 0.5rem;
        }

        .table-scroll {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        thead th {
            text-align: left;
            padding: 0.6rem 1rem;
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
            font-weight: 600;
            color: #374151;
            white-space: nowrap;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        tbody td {
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            white-space: nowrap;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #374151;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .null-value {
            color: #d1d5db;
            font-style: italic;
        }

        .empty-state {
            padding: 2rem;
            text-align: center;
            color: #9ca3af;
            font-size: 0.9rem;
        }

        .badge-count {
            background: #dbeafe;
            color: #1e40af;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-weight: 600;
        }

        .badge-cols {
            background: #f3e8ff;
            color: #7c3aed;
        }
    </style>
</head>
<body>
    <h1>📦 Database Viewer</h1>
    <p class="subtitle">Showing all {{ count($tables) }} tables from the database. Rows limited to 100 per table.</p>

    @forelse ($tables as $table)
        <div class="table-card">
            <div class="table-header">
                <span class="table-name">{{ $table['name'] }}</span>
                <div class="table-meta">
                    <span class="badge-cols">{{ count($table['columns']) }} columns</span>
                    <span class="badge-count">{{ $table['count'] }} rows</span>
                </div>
            </div>

            @if ($table['count'] > 0)
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                @foreach ($table['columns'] as $col)
                                    <th>{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($table['rows'] as $row)
                                <tr>
                                    @foreach ($table['columns'] as $col)
                                        <td>
                                            @if (is_null($row->$col))
                                                <span class="null-value">NULL</span>
                                            @else
                                                {{ $row->$col }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">No data in this table</div>
            @endif
        </div>
    @empty
        <div class="empty-state">No tables found in the database.</div>
    @endforelse
</body>
</html>
