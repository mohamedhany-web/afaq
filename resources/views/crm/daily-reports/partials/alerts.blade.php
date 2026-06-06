@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-xl font-tajawal">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl font-tajawal">{{ session('error') }}</div>
@endif
