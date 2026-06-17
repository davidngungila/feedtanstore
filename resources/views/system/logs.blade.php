@extends('layouts.app')

@section('page-title', 'System Logs')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">System Logs</h2>
            <form action="{{ route('system.logs.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all logs?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
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
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if(count($logFiles) > 0)
                        @foreach($logFiles as $log)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $log['name'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ number_format($log['size'] / 1024, 2) }} KB</td>
                                <td class="px-4 py-3 text-gray-600">{{ date('Y-m-d H:i:s', $log['modified']) }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                No log files found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection