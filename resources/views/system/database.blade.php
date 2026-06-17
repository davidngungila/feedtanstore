@extends('layouts.app')

@section('page-title', 'Database')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold mb-6 text-primary-900">Database Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @php
                $dbTables = [];
                try {
                    $dbTables = DB::select('SHOW TABLES');
                } catch (\Exception $e) {
                    // Handle any errors
                }
            @endphp
            <div class="p-6 bg-blue-50 rounded-xl">
                <h3 class="text-sm font-medium text-blue-700 mb-2">Tables</h3>
                <p class="text-3xl font-bold text-blue-900">{{ count($dbTables) }}</p>
            </div>
            <div class="p-6 bg-green-50 rounded-xl">
                <h3 class="text-sm font-medium text-green-700 mb-2">Connection</h3>
                <p class="text-xl font-bold text-green-900">{{ config('database.default') }}</p>
            </div>
            <div class="p-6 bg-yellow-50 rounded-xl">
                <h3 class="text-sm font-medium text-yellow-700 mb-2">Host</h3>
                <p class="text-lg font-bold text-yellow-900">{{ config('database.connections.' . config('database.default') . '.host') }}</p>
            </div>
            <div class="p-6 bg-purple-50 rounded-xl">
                <h3 class="text-sm font-medium text-purple-700 mb-2">Database</h3>
                <p class="text-lg font-bold text-purple-900">{{ config('database.connections.' . config('database.default') . '.database') }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <h3 class="text-lg font-bold text-primary-900 mb-4">Tables List</h3>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">#</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Table Name</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if(count($dbTables) > 0)
                        @foreach($dbTables as $index => $table)
                            <tr>
                                <td class="px-4 py-3 text-gray-600">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ array_values((array)$table)[0] }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-gray-500">
                                No tables found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection