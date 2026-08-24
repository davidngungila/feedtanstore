@extends('layouts.app')

@section('page-title', 'Audit Logs')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Audit Logs</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Time</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">User</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Action</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Details</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">IP Address</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if($logs->count() > 0)
                        @foreach($logs as $log)
                            <tr>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $log->user ? $log->user->name : 'System' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($log->action === 'Login') bg-blue-100 text-blue-800
                                        @elseif($log->action === 'Create') bg-green-100 text-green-800
                                        @elseif($log->action === 'Update') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 max-w-[320px] truncate">{{ \Illuminate\Support\Str::limit($log->details, 80) }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $log->ip_address }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    @php($auditPayload = [
                                        'user' => $log->user ? $log->user->name : 'System',
                                        'role' => $log->user ? $log->user->role : null,
                                        'action' => $log->action,
                                        'details' => $log->details,
                                        'ip' => $log->ip_address,
                                        'agent' => $log->user_agent,
                                        'time' => $log->created_at->format('D, M d, Y h:i:s A'),
                                    ])
                                    <button type="button" onclick='openAuditModal(@json($auditPayload))' class="text-primary-600 hover:text-primary-800 font-medium text-sm">
                                        <i class="fas fa-eye mr-1"></i>View Full
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">No audit logs found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</div>

{{-- Audit log full details modal --}}
<div id="auditModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-shield-halved text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-primary-900">Audit Log Details</h3>
                </div>
                <button type="button" onclick="closeAuditModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            <dl class="space-y-3 text-sm">
                <div class="grid grid-cols-3 gap-2"><dt class="text-gray-500">User</dt><dd class="col-span-2 font-semibold text-gray-900" id="auditUser"></dd></div>
                <div class="grid grid-cols-3 gap-2"><dt class="text-gray-500">Role</dt><dd class="col-span-2 text-gray-800 capitalize" id="auditRole"></dd></div>
                <div class="grid grid-cols-3 gap-2"><dt class="text-gray-500">Action</dt><dd class="col-span-2"><span id="auditAction" class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-800"></span></dd></div>
                <div class="grid grid-cols-3 gap-2"><dt class="text-gray-500">When</dt><dd class="col-span-2 text-gray-800" id="auditTime"></dd></div>
                <div class="grid grid-cols-3 gap-2"><dt class="text-gray-500">IP Address</dt><dd class="col-span-2 text-gray-800" id="auditIp"></dd></div>
                <div class="grid grid-cols-3 gap-2"><dt class="text-gray-500">Device / Browser</dt><dd class="col-span-2 text-gray-800 break-words" id="auditAgent"></dd></div>
                <div class="grid grid-cols-3 gap-2"><dt class="text-gray-500">Details</dt><dd class="col-span-2 text-gray-900 whitespace-pre-wrap break-words bg-gray-50 border border-gray-100 rounded-lg p-3" id="auditDetails"></dd></div>
            </dl>
            <div class="flex justify-end mt-5">
                <button type="button" onclick="closeAuditModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
const auditModal = document.getElementById('auditModal');

function openAuditModal(log) {
    document.getElementById('auditUser').textContent = log.user;
    document.getElementById('auditRole').textContent = log.role ? log.role.replace(/_/g, ' ') : '—';
    document.getElementById('auditAction').textContent = log.action;
    document.getElementById('auditTime').textContent = log.time;
    document.getElementById('auditIp').textContent = log.ip ?? '—';
    document.getElementById('auditAgent').textContent = log.agent ?? '—';
    document.getElementById('auditDetails').textContent = log.details ?? '—';
    auditModal.classList.remove('hidden');
}
function closeAuditModal() {
    auditModal.classList.add('hidden');
}
document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !auditModal.classList.contains('hidden')) closeAuditModal(); });
auditModal.addEventListener('click', function (e) { if (e.target === auditModal) closeAuditModal(); });
</script>
@endsection
