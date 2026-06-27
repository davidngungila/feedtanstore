@extends('layouts.app')

@section('page-title', 'Customer Display (VFD) Settings')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Customer Display (VFD) Settings</h2>
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

            <div class="flex items-center justify-between">
                <button type="button" id="testVfdBtn" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-play mr-2"></i>Test VFD Display
                </button>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Test Logs Section -->
    <div class="card rounded-2xl p-6 mt-4" id="vfdTestLogs" style="display: none;">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-700">Test Logs</h3>
            <button id="closeLogsBtn" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="vfdLogContent" class="bg-gray-900 text-gray-100 p-4 rounded-lg font-mono text-sm overflow-x-auto max-h-80 overflow-y-auto">
        </div>
    </div>
</div>

<script>
document.getElementById('testVfdBtn').addEventListener('click', async function() {
    const btn = this;
    const originalText = btn.innerHTML;
    const logsSection = document.getElementById('vfdTestLogs');
    const logContent = document.getElementById('vfdLogContent');
    
    // Collect form values
    const vfdEnabled = document.getElementById('vfd_enabled').checked;
    const vfdPort = document.querySelector('input[name="vfd_port"]').value;
    const vfdBaud = parseInt(document.querySelector('select[name="vfd_baud"]').value);
    const vfdDataBits = parseInt(document.querySelector('select[name="vfd_data_bits"]').value);
    const vfdStopBits = parseInt(document.querySelector('select[name="vfd_stop_bits"]').value);
    const vfdParity = document.querySelector('select[name="vfd_parity"]').value;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';
    btn.disabled = true;
    
    try {
        const response = await fetch('{{ route('system.vfd.test') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                vfd_enabled: vfdEnabled,
                vfd_port: vfdPort,
                vfd_baud: vfdBaud,
                vfd_data_bits: vfdDataBits,
                vfd_stop_bits: vfdStopBits,
                vfd_parity: vfdParity
            })
        });
        
        const result = await response.json();
        
        // Show logs
        logsSection.style.display = 'block';
        if (result.logs) {
            logContent.innerHTML = result.logs.map(log => 
                log.includes('ERROR') || log.includes('FAILED') 
                    ? `<div class="text-red-400">${log}</div>` 
                    : log.includes('SUCCESS') || log.includes('Wrote')
                        ? `<div class="text-green-400">${log}</div>`
                        : `<div>${log}</div>`
            ).join('');
        } else {
            logContent.innerHTML = '<div>No logs available</div>';
        }
        
        if (result.success) {
            btn.innerHTML = '<i class="fas fa-check mr-2"></i>Test Sent!';
            btn.classList.remove('bg-green-600', 'hover:bg-green-700');
            btn.classList.add('bg-green-500');
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('bg-green-500');
                btn.classList.add('bg-green-600', 'hover:bg-green-700');
            }, 3000);
        } else {
            btn.innerHTML = '<i class="fas fa-times mr-2"></i>Failed';
            btn.classList.remove('bg-green-600', 'hover:bg-green-700');
            btn.classList.add('bg-red-500');
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('bg-red-500');
                btn.classList.add('bg-green-600', 'hover:bg-green-700');
            }, 3000);
        }
    } catch (error) {
        logsSection.style.display = 'block';
        logContent.innerHTML = `<div class="text-red-400">Error: ${error.message}</div>`;
        
        btn.innerHTML = '<i class="fas fa-times mr-2"></i>Failed';
        btn.classList.remove('bg-green-600', 'hover:bg-green-700');
        btn.classList.add('bg-red-500');
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('bg-red-500');
            btn.classList.add('bg-green-600', 'hover:bg-green-700');
        }, 3000);
    }
    
    btn.disabled = false;
});

document.getElementById('closeLogsBtn').addEventListener('click', function() {
    document.getElementById('vfdTestLogs').style.display = 'none';
});
</script>
@endsection
