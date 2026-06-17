@extends('layouts.app')

@section('page-title', 'Backup & Restore')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Backup & Restore</h2>
            <form action="{{ route('system.backup.create') }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create New Backup
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">File Name</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Size</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @php
                        $backups = [];
                        $backupPath = storage_path('app/backups');
                        if (File::exists($backupPath)) {
                            $files = File::files($backupPath);
                            foreach ($files as $file) {
                                $backups[] = [
                                    'name' => $file->getFilename(),
                                    'size' => $file->getSize(),
                                    'date' => $file->getMTime()
                                ];
                            }
                        }
                    @endphp
                    @if(count($backups) > 0)
                        @foreach($backups as $backup)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $backup['name'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ number_format($backup['size'] / 1024, 2) }} KB</td>
                                <td class="px-4 py-3 text-gray-600">{{ date('Y-m-d H:i:s', $backup['date']) }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('system.backup.download', $backup['name']) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                No backups found. Create your first backup!
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection