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
                                    <a href="{{ route('system.logs.view', ['file' => $log['name']]) }}" class="text-primary-600 hover:text-primary-800 font-medium text-sm">
                                        <i class="fas fa-eye mr-1"></i>View Full
                                    </a>
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
const clearLogsModal = document.getElementById('clearLogsModal');

function openClearLogsModal() {
    clearLogsModal.classList.remove('hidden');
}
function closeClearLogsModal() {
    clearLogsModal.classList.add('hidden');
}
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !clearLogsModal.classList.contains('hidden')) closeClearLogsModal();
});
clearLogsModal.addEventListener('click', function (e) { if (e.target === clearLogsModal) closeClearLogsModal(); });
</script>
@endsection
