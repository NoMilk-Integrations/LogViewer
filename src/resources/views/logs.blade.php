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
    <div class="p-8 flex-shrink-0 border-b border-gray-200">
        <div class="flex flex-col md:flex-row justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Logs fra {{ config('app.name') }}</h1>
                <p class="text-gray-600 mt-2">Logfilen bliver automatisk ryddet, så kun de seneste {{ config('log-viewer.retention_weeks') }} ugers data bevares.</p>
            </div>

            @if(count($files) > 1)
                <div class="mt-4 md:mt-0">
                    <label for="log-file-select" class="block text-sm font-medium text-gray-700 mb-2">Vælg logfil</label>

                    <select id="log-file-select" class="block w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg shadow-sm">
                        @foreach($files as $key => $file)
                            <option value="{{ $key }}" @if($key === $selectedFile) selected @endif>
                                {{ ucfirst($key) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    <div class="flex-1 px-8 pb-8 overflow-hidden flex flex-col">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
            <div class="bg-gray-900 text-gray-100 p-4 font-mono text-sm overflow-y-auto flex-1">
                <div class="space-y-1">
                    @forelse($lines as $line)
                        @php
                            $env = config('app.env', 'local');

                            preg_match('/\[(.*?)\]\s+' . preg_quote($env, '/') . '\.(\w+):\s*(.*)/', $line, $matches);

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

    <script>
        document.getElementById('log-file-select')?.addEventListener('change', function(e) {
            window.location.href = `{{ route('logs.show') }}?file=${e.target.value}`;
        });
    </script>
</body>
</html>