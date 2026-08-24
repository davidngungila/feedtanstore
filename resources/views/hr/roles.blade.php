@extends('layouts.app')

@section('page-title', 'Roles & Permissions')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="card rounded-2xl p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
            <div>
                <h2 class="text-xl font-bold text-primary-900">Roles &amp; Permissions</h2>
                <p class="text-sm text-gray-500">Control what each role can do in every module. These permissions guard the Employees &amp; HR section and the sidebar menu.</p>
            </div>
            @if($canManage)
            <button type="button" onclick="openSavePermissionsModal()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
            @else
            <span class="text-xs text-gray-400 italic">Read-only — ask an administrator for changes.</span>
            @endif
        </div>

        <form id="permissionsForm" action="{{ route('hr.roles.save') }}" method="POST">
            @csrf
            <div class="overflow-x-auto mt-4">
                <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Module</th>
                            @foreach($roles as $role)
                                <th class="px-3 py-3 text-center text-gray-700 font-medium">{{ ucfirst(str_replace('_', ' ', $role)) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($modules as $module)
                        <tr class="hover:bg-gray-50/60">
                            <td class="px-4 py-3 font-semibold text-gray-800 capitalize">{{ $module }}</td>
                            @foreach($roles as $role)
                            <td class="px-3 py-3">
                                <div class="flex justify-center gap-1">
                                    @foreach($actions as $action)
                                        @php($checked = $matrix[$module][$role][$action] ?? true)
                                        <label class="cursor-pointer {{ $canManage ? '' : 'pointer-events-none' }}" title="{{ ucfirst($action) }}">
                                            <input type="checkbox"
                                                name="perm[{{ $module }}][{{ $role }}][{{ $action }}]"
                                                value="1"
                                                {{ $checked ? 'checked' : '' }}
                                                {{ $canManage ? '' : 'disabled' }}
                                                class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                                            <span class="sr-only">{{ ucfirst($action) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-gray-400 mt-2">Checkbox order per cell: <strong>C</strong>reate · <strong>R</strong>ead · <strong>U</strong>pdate · <strong>D</strong>elete (hover to see each).</p>
        </form>
    </div>
</div>

{{-- Save permissions confirmation modal --}}
<div id="savePermissionsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-shield-halved text-primary-600"></i>
                </div>
                <h3 class="text-lg font-bold text-primary-900">Apply Permission Changes</h3>
            </div>
            <p class="text-gray-700 mb-2">Are you sure you want to update the roles &amp; permissions matrix?</p>
            <p class="text-xs text-gray-500 mb-5">Changes take effect immediately — menus and guarded actions adjust for all users.</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeSavePermissionsModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="button" onclick="document.getElementById('permissionsForm').submit()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">Yes, Apply Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
const savePermissionsModal = document.getElementById('savePermissionsModal');

function openSavePermissionsModal() {
    savePermissionsModal.classList.remove('hidden');
}
function closeSavePermissionsModal() {
    savePermissionsModal.classList.add('hidden');
}
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !savePermissionsModal.classList.contains('hidden')) closeSavePermissionsModal();
});
savePermissionsModal.addEventListener('click', function (e) { if (e.target === savePermissionsModal) closeSavePermissionsModal(); });
</script>
@endsection
