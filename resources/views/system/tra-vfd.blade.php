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
                        <p class="text-sm text-blue-700 mt-1"><strong>Tax Codes:</strong> 1 = 18% Standard Rated, 3 = 0% Zero Rated, 4 = 0% Special Relief, 5 = 0% Exempted (used for non-VAT registered sellers)</p>
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

                <div class="md:col-span-2">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="vat_registered" value="1" {{ $settings->vat_registered ? 'checked' : '' }} 
                               class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <span class="text-sm font-medium text-gray-700">VAT Registered</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1 ml-8">Enable if your business is registered for VAT. If disabled, all sales will use tax code 5 (Exempted - 0% VAT).</p>
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

    <!-- Fiscal Counters -->
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Fiscal Counters</h2>
        </div>
        <div class="bg-gray-50 rounded-xl p-4 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Global Counter (GC)</p>
                    <p class="text-3xl font-bold text-primary-900">{{ $settings->tra_gc ?? 1 }}</p>
                    <p class="text-xs text-gray-400 mt-1">Increments with each receipt</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Daily Counter (DC)</p>
                    <p class="text-3xl font-bold text-primary-900">{{ $settings->tra_dc ?? 1 }}</p>
                    <p class="text-xs text-gray-400 mt-1">Resets each day</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Z Report (ZNUM)</p>
                    <p class="text-3xl font-bold text-primary-900">{{ $settings->tra_znum ?? date('Ymd') }}</p>
                    <p class="text-xs text-gray-400 mt-1">YYYYMMDD format</p>
                </div>
            </div>
        </div>
        <p class="text-sm text-gray-500">Counters auto-increment after each successful TRA posting. Do not modify unless you know what you're doing.</p>
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
                        <td class="py-2">Exempted (also used for non-VAT registered sellers)</td>
                        <td class="py-2">0%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function readJsonResponse(response) {
    const text = await response.text();
    try {
        return { data: JSON.parse(text) };
    } catch (e) {
        return { parseError: 'HTTP ' + response.status + ' returned a non-JSON response: ' + text.slice(0, 300) };
    }
}

function showTraTestMessage(kind, title, message) {
    const resultContent = document.getElementById('testResultContent');
    const styles = {
        info: ['bg-blue-50 border-blue-200', 'text-blue-800', 'text-blue-700', 'fa-spinner fa-spin'],
        warning: ['bg-yellow-50 border-yellow-200', 'text-yellow-800', 'text-yellow-700', 'fa-exclamation-triangle'],
        error: ['bg-red-50 border-red-200', 'text-red-800', 'text-red-700', 'fa-times-circle']
    }[kind] || ['bg-blue-50 border-blue-200', 'text-blue-800', 'text-blue-700', 'fa-info-circle'];
    resultContent.className = 'p-4 rounded-xl ' + styles[0] + ' border';
    resultContent.innerHTML = '<div class="flex items-start gap-3"><i class="fas ' + styles[3] + ' mt-1"></i><div><p class="font-semibold ' + styles[1] + '">' + title + '</p><p class="text-sm mt-1 ' + styles[2] + '">' + message + '</p></div></div>';
}

async function testTraConnection() {
    const resultDiv = document.getElementById('testResult');
    const resultContent = document.getElementById('testResultContent');
    const xmlPreview = document.getElementById('xmlPreview');
    const xmlContent = document.getElementById('xmlContent');

    resultDiv.classList.remove('hidden');
    xmlPreview.classList.add('hidden');
    showTraTestMessage('info', 'Saving settings and testing connection to TRA...', 'Please wait.');
    
    try {
        // First save the settings
        const form = document.querySelector('form');
        const formData = new FormData(form);
        
        const saveResponse = await fetch('{{ route("system.update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            }
        });
        if (!saveResponse.ok) {
            showTraTestMessage('error', 'Settings save failed', 'HTTP ' + saveResponse.status + ' while saving TRA settings. Fix the settings form and try again.');
            return;
        }
        
        await new Promise(r => setTimeout(r, 500));
        
        // Get XML preview
        const xmlResponse = await fetch('{{ route("sales.receipts.tra-xml", 1) }}', {
            headers: { 'Accept': 'text/xml' }
        });
        if (xmlResponse.ok) {
            const xmlText = await xmlResponse.text();
            xmlContent.textContent = xmlText;
            xmlPreview.classList.remove('hidden');
        } else {
            showTraTestMessage('error', 'XML preview failed', 'HTTP ' + xmlResponse.status + ' while generating the TRA XML preview.');
            return;
        }
        
        // Confirm before posting to TRA
        const firstConfirmed = confirm('Are you sure you want to post this test sale to TRA?');
        if (!firstConfirmed) {
            resultContent.className = 'p-4 rounded-xl bg-yellow-50 border border-yellow-200';
            resultContent.innerHTML = '<div class="flex items-start gap-3"><i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i><div><p class="font-semibold text-yellow-800">TRA posting cancelled</p><p class="text-sm text-yellow-700 mt-1">Test posting was cancelled by user.</p></div></div>';
            return;
        }

        const secondConfirmed = confirm('Are you REALLY sure you want to post this test sale to TRA? This action cannot be undone.');
        if (!secondConfirmed) {
            resultContent.className = 'p-4 rounded-xl bg-yellow-50 border border-yellow-200';
            resultContent.innerHTML = '<div class="flex items-start gap-3"><i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i><div><p class="font-semibold text-yellow-800">TRA posting cancelled</p><p class="text-sm text-yellow-700 mt-1">Test posting was cancelled by user.</p></div></div>';
            return;
        }
        
        // Post to TRA
        const testResponse = await fetch('{{ route("sales.receipts.post-to-tra") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ sale_id: 1 })
        });
        
        const { data: result, parseError } = await readJsonResponse(testResponse);
        if (parseError) {
            showTraTestMessage('error', 'Connection Error', parseError);
            return;
        }

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
            showTraTestMessage('warning', 'TRA Error', result.error || 'Unknown response');
        }
    } catch (e) {
        showTraTestMessage('error', 'Connection Error', e.message);
    }
}
</script>
@endsection
