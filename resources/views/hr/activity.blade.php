@extends('layouts.app')

@section('page-title', 'Activity Logs')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h2 class="text-xl font-bold text-primary-900">Activity Logs</h2>
            <form method="GET" action="{{ route('hr.activity') }}" class="flex flex-wrap gap-2">
                <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Search action or details..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <select name="user_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ $filters['user_id'] == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="from" value="{{ $filters['from'] }}" title="From date" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <input type="date" name="to" value="{{ $filters['to'] }}" title="To date" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <button type="submit" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm"><i class="fas fa-search"></i></button>
                <a href="{{ route('hr.activity') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm"><i class="fas fa-rotate-left"></i></a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">#</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">User</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Action</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Details</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Date/Time</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if($logs->count() > 0)
                        @foreach($logs as $index => $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">{{ $logs->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $log->user ? $log->user->name : 'System' }}</td>
                                <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">{{ $log->action }}</span></td>
                                <td class="px-4 py-3 text-gray-600 max-w-[320px] truncate">{{ \Illuminate\Support\Str::limit($log->details, 80) }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                                <td class="px-4 py-3 text-right">
                                    @php($logPayload = [
                                        'user' => $log->user ? $log->user->name : 'System',
                                        'role' => $log->user ? $log->user->role : null,
                                        'action' => $log->action,
                                        'details' => $log->details,
                                        'ip' => $log->ip_address,
                                        'agent' => $log->user_agent,
                                        'time' => $log->created_at->format('D, M d, Y h:i:s A'),
                                    ])
                                    <button type="button" onclick='openLogModal(@json($logPayload))' class="text-primary-600 hover:text-primary-800 font-medium text-sm">
                                        <i class="fas fa-eye mr-1"></i>View Full
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">No activity logs found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</div>

{{-- Log full details modal --}}
<div id="logModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clipboard-list text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-primary-900">Activity Details</h3>
                </div>
                <button type="button" onclick="closeLogModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            <dl class="space-y-3 text-sm">
                <div class="grid grid-cols-3 gap-2"><dt class="text-gray-500">User</dt><dd class="col-span-2 font-semibold text-gray-900" id="logUser"></dd></div>
                <div class="grid grid-cols-3 gap-2"><dt class="text-gray-500">Role</dt><dd class="col-span-2 text-gray-800 capitalize" id="logRole"></dd></div>
                <div class="grid grid-cols-3 gap-2"><dt class="text-gray-500">Action</dt><dd class="col-span-2"><span id="logAction" class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-800"></span></dd></div>
                <div class="grid grid-cols-3 gap-2"><dt class="text-gray-500">When</dt><dd class="col-span-2 text-gray-800" id="logTime"></dd></div>
                <div class="grid grid-cols-3 gap-2"><dt class="text-gray-500">IP Address</dt><dd class="col-span-2 text-gray-800" id="logIp"></dd></div>
                <div class="grid grid-cols-3 gap-2"><dt class="text-gray-500">Device / Browser</dt><dd class="col-span-2 text-gray-800 break-words" id="logAgent"></dd></div>
                <div class="grid grid-cols-3 gap-2"><dt class="text-gray-500">Details</dt><dd class="col-span-2 text-gray-900 whitespace-pre-wrap break-words bg-gray-50 border border-gray-100 rounded-lg p-3" id="logDetails"></dd></div>
            </dl>
            <div class="flex justify-end mt-5">
                <button type="button" onclick="closeLogModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
const logModal = document.getElementById('logModal');

function openLogModal(log) {
    document.getElementById('logUser').textContent = log.user;
    document.getElementById('logRole').textContent = log.role ? log.role.replace(/_/g, ' ') : '—';
    document.getElementById('logAction').textContent = log.action;
    document.getElementById('logTime').textContent = log.time;
    document.getElementById('logIp').textContent = log.ip ?? '—';
    document.getElementById('logAgent').textContent = log.agent ?? '—';
    document.getElementById('logDetails').textContent = log.details ?? '—';
    logModal.classList.remove('hidden');
}
function closeLogModal() {
    logModal.classList.add('hidden');
}
document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !logModal.classList.contains('hidden')) closeLogModal(); });
logModal.addEventListener('click', function (e) { if (e.target === logModal) closeLogModal(); });
</script>
@endsection
