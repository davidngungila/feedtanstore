@extends('layouts.app')

@section('page-title', 'System Logs')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-primary-900">System Logs</h2>
                <p class="text-sm text-gray-500">Full application log files from storage/logs — click "View Full" to read a file.</p>
            </div>
            <form action="{{ route('system.logs.clear') }}" method="POST" id="clearLogsForm">
                @csrf
                <button type="button" onclick="openClearLogsModal()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-trash mr-2"></i>Clear Logs
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">File Name</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Size</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Last Modified</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if(count($logFiles) > 0)
                        @foreach($logFiles as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $log['name'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ number_format($log['size'] / 1024, 2) }} KB</td>
                                <td class="px-4 py-3 text-gray-600">{{ date('Y-m-d H:i:s', $log['modified']) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button" data-name="{{ $log['name'] }}" onclick="viewLogFile(this)" class="text-primary-600 hover:text-primary-800 font-medium text-sm">
                                        <i class="fas fa-eye mr-1"></i>View Full
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No log files found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Log file viewer modal --}}
<div id="logFileModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl h-[85vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 flex-shrink-0">
            <h3 class="font-bold text-primary-900 truncate"><i class="fas fa-file-lines mr-2 text-primary-500"></i><span id="logFileName"></span></h3>
            <div class="flex items-center gap-2 ml-4">
                <input type="text" id="logFilterInput" placeholder="Filter lines..." class="hidden md:block px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <button type="button" onclick="closeLogFileModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <pre id="logFileContent" class="flex-1 overflow-auto m-0 p-4 bg-gray-950 text-gray-100 text-xs leading-relaxed whitespace-pre-wrap break-all">Loading…</pre>
        <div class="p-3 border-t border-gray-200 flex justify-end flex-shrink-0">
            <button type="button" onclick="closeLogFileModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm">Close</button>
        </div>
    </div>
</div>

{{-- Clear logs confirmation modal --}}
<div id="clearLogsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-trash text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold text-primary-900">Clear All Logs</h3>
            </div>
            <p class="text-gray-700 mb-2">Are you sure you want to clear all system logs?</p>
            <p class="text-xs text-gray-500 mb-5">Every log file in storage/logs will be emptied. This cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeClearLogsModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="button" onclick="document.getElementById('clearLogsForm').submit()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">Yes, Clear Logs</button>
            </div>
        </div>
    </div>
</div>

<script>
const logFileModal = document.getElementById('logFileModal');
const clearLogsModal = document.getElementById('clearLogsModal');
const rawLogContent = { lines: [] };

function openClearLogsModal() {
    clearLogsModal.classList.remove('hidden');
}
function closeClearLogsModal() {
    clearLogsModal.classList.add('hidden');
}

async function viewLogFile(btn) {
    document.getElementById('logFileName').textContent = btn.dataset.name;
    document.getElementById('logFileContent').textContent = 'Loading…';
    document.getElementById('logFilterInput').value = '';
    logFileModal.classList.remove('hidden');

    try {
        const resp = await fetch('{{ route('system.logs.view') }}?file=' + encodeURIComponent(btn.dataset.name), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const data = await resp.json();
        rawLogContent.lines = data.content.split('\n');
        renderLogLines('');
    } catch (err) {
        document.getElementById('logFileContent').textContent = 'Failed to load log file: ' + err.message;
    }
}

function renderLogLines(filter) {
    const q = filter.trim().toLowerCase();
    const el = document.getElementById('logFileContent');
    if (!q) {
        el.textContent = rawLogContent.lines.join('\n');
        return;
    }
    const matches = rawLogContent.lines.filter(function (line) { return line.toLowerCase().includes(q); });
    el.textContent = matches.length ? matches.join('\n') : 'No lines match "' + filter + '".';
}
document.getElementById('logFilterInput').addEventListener('input', function () { renderLogLines(this.value); });

function closeLogFileModal() {
    logFileModal.classList.add('hidden');
    rawLogContent.lines = [];
}
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        if (!logFileModal.classList.contains('hidden')) closeLogFileModal();
        if (!clearLogsModal.classList.contains('hidden')) closeClearLogsModal();
    }
});
[logFileModal, clearLogsModal].forEach(function (m) {
    m.addEventListener('click', function (e) { if (e.target === m) m.classList.add('hidden'); });
});
</script>
@endsection
