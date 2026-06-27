@extends('layouts.app')

@section('page-title', 'VFD Customer Display Settings')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">VFD Customer Display Settings</h2>
        </div>

        <form action="{{ route('system.update') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="vfd_enabled" id="vfd_enabled" 
                           {{ old('vfd_enabled', $settings->vfd_enabled) ? 'checked' : '' }} 
                           class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                    <label for="vfd_enabled" class="text-sm font-medium text-gray-700">Enable VFD Customer Display</label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Serial Port</label>
                    <input type="text" name="vfd_port" value="{{ old('vfd_port', $settings->vfd_port ?? 'COM3') }}" 
                           placeholder="COM3, /dev/ttyUSB0, etc."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Baud Rate</label>
                    <select name="vfd_baud" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="9600" {{ old('vfd_baud', $settings->vfd_baud ?? 9600) == 9600 ? 'selected' : '' }}>9600</option>
                        <option value="1200" {{ old('vfd_baud', $settings->vfd_baud ?? 9600) == 1200 ? 'selected' : '' }}>1200</option>
                        <option value="2400" {{ old('vfd_baud', $settings->vfd_baud ?? 9600) == 2400 ? 'selected' : '' }}>2400</option>
                        <option value="4800" {{ old('vfd_baud', $settings->vfd_baud ?? 9600) == 4800 ? 'selected' : '' }}>4800</option>
                        <option value="19200" {{ old('vfd_baud', $settings->vfd_baud ?? 9600) == 19200 ? 'selected' : '' }}>19200</option>
                        <option value="38400" {{ old('vfd_baud', $settings->vfd_baud ?? 9600) == 38400 ? 'selected' : '' }}>38400</option>
                        <option value="57600" {{ old('vfd_baud', $settings->vfd_baud ?? 9600) == 57600 ? 'selected' : '' }}>57600</option>
                        <option value="115200" {{ old('vfd_baud', $settings->vfd_baud ?? 9600) == 115200 ? 'selected' : '' }}>115200</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Bits</label>
                    <select name="vfd_data_bits" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="8" {{ old('vfd_data_bits', $settings->vfd_data_bits ?? 8) == 8 ? 'selected' : '' }}>8</option>
                        <option value="7" {{ old('vfd_data_bits', $settings->vfd_data_bits ?? 8) == 7 ? 'selected' : '' }}>7</option>
                        <option value="6" {{ old('vfd_data_bits', $settings->vfd_data_bits ?? 8) == 6 ? 'selected' : '' }}>6</option>
                        <option value="5" {{ old('vfd_data_bits', $settings->vfd_data_bits ?? 8) == 5 ? 'selected' : '' }}>5</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stop Bits</label>
                    <select name="vfd_stop_bits" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="1" {{ old('vfd_stop_bits', $settings->vfd_stop_bits ?? 1) == 1 ? 'selected' : '' }}>1</option>
                        <option value="2" {{ old('vfd_stop_bits', $settings->vfd_stop_bits ?? 1) == 2 ? 'selected' : '' }}>2</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Parity</label>
                    <select name="vfd_parity" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="none" {{ old('vfd_parity', $settings->vfd_parity ?? 'none') == 'none' ? 'selected' : '' }}>None</option>
                        <option value="odd" {{ old('vfd_parity', $settings->vfd_parity ?? 'none') == 'odd' ? 'selected' : '' }}>Odd</option>
                        <option value="even" {{ old('vfd_parity', $settings->vfd_parity ?? 'none') == 'even' ? 'selected' : '' }}>Even</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
