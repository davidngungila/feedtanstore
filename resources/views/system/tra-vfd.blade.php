@extends('layouts.app')

@section('page-title', 'TRA VFD API Settings')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">TRA VFD API Settings</h2>
            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ !empty($settings->tra_api_username) ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ !empty($settings->tra_api_username) ? 'Configured' : 'Not Configured' }}
            </span>
        </div>

        <form action="{{ route('system.update') }}" method="POST">
            @csrf
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                    <div>
                        <p class="text-sm font-semibold text-blue-800">PERG_TRA_VFD_API_v1.0.1</p>
                        <p class="text-sm text-blue-700 mt-1">This integration posts receipts to the Tanzania Revenue Authority (TRA) Electronic Fiscal Device (EFD) system. Configure your TRA registration details below.</p>
                        <p class="text-sm text-blue-700 mt-1"><strong>Tax Codes:</strong> 1 = 18% Standard Rated, 3 = 0% Zero Rated, 4 = 0% Special Relief, 5 = 0% Exempted</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">API Endpoint</label>
                    <input type="url" name="tra_api_endpoint" value="{{ old('tra_api_endpoint', $settings->tra_api_endpoint ?? 'http://162.55.181.173:8080/TRA_VFD/Operations') }}" 
                           placeholder="http://162.55.181.173:8080/TRA_VFD/Operations"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">API Username</label>
                    <input type="text" name="tra_api_username" value="{{ old('tra_api_username', $settings->tra_api_username) }}" 
                           placeholder="e.g. 0756880647"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">API Password</label>
                    <input type="password" name="tra_api_password" value="{{ old('tra_api_password', $settings->tra_api_password) }}" 
                           placeholder="API password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">TIN Number</label>
                    <input type="text" name="tra_tin_number" value="{{ old('tra_tin_number', $settings->tra_tin_number) }}" 
                           placeholder="e.g. 110781512"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">VFD Serial Number</label>
                    <input type="text" name="tra_vfd_serial" value="{{ old('tra_vfd_serial', $settings->tra_vfd_serial) }}" 
                           placeholder="e.g. 03TZ843010734"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Licence Key</label>
                    <textarea name="tra_licence" rows="3" 
                              placeholder="Paste your TRA VFD licence key here"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('tra_licence', $settings->tra_licence) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-semibold transition">
                    <i class="fas fa-save mr-2"></i>Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Test Connection -->
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Test Connection</h2>
        </div>

        <div id="testResult" class="hidden mb-4">
            <div id="testResultContent" class="p-4 rounded-xl"></div>
        </div>

        <div id="xmlPreview" class="hidden mb-4">
            <div class="bg-gray-900 text-green-400 rounded-xl p-4 font-mono text-xs overflow-x-auto max-h-96 overflow-y-auto">
                <pre id="xmlContent"></pre>
            </div>
        </div>

        <div class="flex gap-3">
            <button onclick="testTraConnection()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                <i class="fas fa-plug mr-2"></i>Test Connection
            </button>
        </div>
    </div>

    <!-- Tax Code Reference -->
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-4">Product Tax Codes Reference</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Code</th>
                        <th class="text-left py-2">Description</th>
                        <th class="text-left py-2">VAT Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="py-2 font-semibold">1</td>
                        <td class="py-2">Standard Rated</td>
                        <td class="py-2">18%</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2 font-semibold">3</td>
                        <td class="py-2">Zero Rated</td>
                        <td class="py-2">0%</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2 font-semibold">4</td>
                        <td class="py-2">Special Relief</td>
                        <td class="py-2">0%</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2 font-semibold">5</td>
                        <td class="py-2">Exempted</td>
                        <td class="py-2">0%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function testTraConnection() {
    const resultDiv = document.getElementById('testResult');
    const resultContent = document.getElementById('testResultContent');
    const xmlPreview = document.getElementById('xmlPreview');
    const xmlContent = document.getElementById('xmlContent');
    
    resultDiv.classList.remove('hidden');
    xmlPreview.classList.add('hidden');
    resultContent.className = 'p-4 rounded-xl bg-blue-50 border border-blue-200';
    resultContent.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving settings and testing connection to TRA...';
    
    try {
        // First save the settings
        const form = document.querySelector('form');
        const formData = new FormData(form);
        
        await fetch('{{ route("system.update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            }
        });
        
        await new Promise(r => setTimeout(r, 500));
        
        // Get XML preview
        const xmlResponse = await fetch('/sales/receipts/1/tra-xml', {
            headers: { 'Accept': 'text/xml' }
        });
        if (xmlResponse.ok) {
            const xmlText = await xmlResponse.text();
            xmlContent.textContent = xmlText;
            xmlPreview.classList.remove('hidden');
        }
        
        // Post to TRA
        const testResponse = await fetch('/sales/receipts/post-to-tra', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ sale_id: 1 })
        });
        
        const result = await testResponse.json();
        
        if (result.success) {
            const receiptNum = result.receipt_number || 'N/A';
            const verifyLink = result.verification_link || '';
            const isDuplicate = result.duplicate || false;
            
            let html = '<div class="flex items-start gap-3">';
            html += '<i class="fas fa-check-circle text-green-600 mt-1 text-lg"></i>';
            html += '<div>';
            html += '<p class="font-semibold text-green-800">' + (isDuplicate ? 'Already Posted' : 'Posted Successfully!') + '</p>';
            html += '<p class="text-sm text-green-700 mt-1">TRA Receipt #: <strong>' + receiptNum + '</strong></p>';
            if (verifyLink) {
                html += '<p class="text-sm text-green-700 mt-1">Verification: <a href="' + verifyLink + '" target="_blank" class="underline hover:text-green-900">' + verifyLink + '</a></p>';
            }
            html += '</div></div>';
            
            resultContent.className = 'p-4 rounded-xl bg-green-50 border border-green-200';
            resultContent.innerHTML = html;
        } else {
            resultContent.className = 'p-4 rounded-xl bg-yellow-50 border border-yellow-200';
            resultContent.innerHTML = '<div class="flex items-start gap-3"><i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i><div><p class="font-semibold text-yellow-800">TRA Error</p><p class="text-sm text-yellow-700 mt-1">' + (result.error || 'Unknown response') + '</p></div></div>';
        }
    } catch (e) {
        resultContent.className = 'p-4 rounded-xl bg-red-50 border border-red-200';
        resultContent.innerHTML = '<div class="flex items-start gap-3"><i class="fas fa-times-circle text-red-600 mt-1"></i><div><p class="font-semibold text-red-800">Connection Error</p><p class="text-sm text-red-700 mt-1">' + e.message + '</p></div></div>';
    }
}
</script>
@endsection
