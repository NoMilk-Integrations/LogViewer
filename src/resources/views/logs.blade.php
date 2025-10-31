<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} | Logs</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 h-screen flex flex-col">
    <div class="p-8 flex-shrink-0">
        <h1 class="text-3xl font-bold text-gray-900">Logs fra {{ config('app.name') }}</h1>
        <p class="text-gray-600 mt-2">Logfilen bliver automatisk ryddet, så kun de seneste {{ config('log-viewer.retention_weeks') }} ugers data bevares.</p>
    </div>

    <div class="flex-1 px-8 pb-8 overflow-hidden flex flex-col">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
            <div class="bg-gray-900 text-gray-100 p-4 font-mono text-sm overflow-y-auto flex-1">
                <div class="space-y-1">
                    @forelse($lines as $line)
                        @php
                            preg_match('/\[(.*?)\]\s+local\.(\w+):\s*(.*)/', $line, $matches);
                            $timestamp = $matches[1] ?? '';
                            $level = $matches[2] ?? 'INFO';
                            $message = $matches[3] ?? '';

                            $levelColor = match($level) {
                                'ERROR','ALERT' => 'text-red-400',
                                'WARNING' => 'text-yellow-400',
                                'DEBUG' => 'text-purple-400',
                                default => 'text-green-400'
                            };
                        @endphp

                        <div>
                            <span class="text-blue-400">[{{ $timestamp }}]</span>
                            <span class="text-gray-500">local.</span><span class="{{ $levelColor }}">{{ $level }}</span><span class="text-gray-500">:</span>
                            <span class="text-gray-300">{{ $message }}</span>
                        </div>
                    @empty
                        <div class="text-gray-500">Ingen data fundet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</body>
</html>