@extends('layouts.app')

@section('page-title', 'View Log — ' . $name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease] h-full flex flex-col">
    <div class="card rounded-2xl p-4 mb-4 flex flex-wrap items-center justify-between gap-3">
        <div class="min-w-0">
            <a href="{{ route('system.logs') }}" class="text-primary-600 hover:text-primary-800 font-medium text-sm">
                <i class="fas fa-arrow-left mr-1"></i>Back to System Logs
            </a>
            <h2 class="text-lg font-bold text-primary-900 truncate mt-1"><i class="fas fa-file-lines mr-2 text-primary-500"></i>{{ $name }}</h2>
            <p class="text-xs text-gray-500">{{ number_format($size / 1024, 2) }} KB · full content below (last 200 KB for very large files)</p>
        </div>
        <input type="text" id="logFilterInput" autocomplete="off" placeholder="Filter lines containing..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full md:w-72">
    </div>

    <div class="flex-1 min-h-0">
        <pre id="logFileContent" class="h-full max-h-[70vh] overflow-auto m-0 p-4 bg-gray-950 text-gray-100 text-xs leading-relaxed rounded-2xl whitespace-pre-wrap break-all">{{ $content }}</pre>
    </div>
</div>

<script>
const rawLines = document.getElementById('logFileContent').textContent.split('\n');
document.getElementById('logFilterInput').addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    const el = document.getElementById('logFileContent');
    if (!q) {
        el.textContent = rawLines.join('\n');
        return;
    }
    const matches = rawLines.filter(function (line) { return line.toLowerCase().includes(q); });
    el.textContent = matches.length ? matches.join('\n') : 'No lines match "' + this.value + '".';
});
</script>
@endsection
