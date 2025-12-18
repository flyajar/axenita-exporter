<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gray-50">
<div class="max-w-xl mx-auto p-6">
    <div class="bg-white rounded-2xl shadow p-6">
        <h1 class="text-2xl font-bold">Contact us</h1>
        <p class="text-gray-600 mt-1">Fill in the form and we’ll get back to you.</p>

        @if (session('success'))
            <div class="mt-4 rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">
                <div class="font-semibold">Please fix the errors below.</div>
            </div>
        @endif

        <form class="mt-6 space-y-4" method="POST" action="{{ route("patient.get") }}">
            @csrf

            <div>
                <label class="block text-sm font-medium">
                    axenita-csrf-token
                </label>
                <input
                    name="axenita_csrf_token"
                    value="{{ old('axenita_csrf_token') }}"
                    class="mt-1 w-full rounded-lg border p-3 focus:outline-none focus:ring"
                    placeholder="Enter axenita-csrf-token"
                >
                @error('axenita_csrf_token')
                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">
                    axenita-csrf-token-cookie
                </label>
                <input
                    name="axenita_csrf_token_cookie"
                    value="{{ old('axenita_csrf_token_cookie') }}"
                    class="mt-1 w-full rounded-lg border p-3 focus:outline-none focus:ring"
                    placeholder="Enter axenita-csrf-token-cookie"
                >
                @error('axenita_csrf_token_cookie')
                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">
                    axenita-authentication (full cookie)
                </label>
                <input
                    name="axenita_auth_cookie"
                    value="{{ old('axenita_auth_cookie') }}"
                    class="mt-1 w-full rounded-lg border p-3 focus:outline-none focus:ring font-mono text-sm"
                    placeholder="axenita-authentication-<uuid>=<jwt>"
                >
                @error('axenita_auth_cookie')
                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">
                    Page size
                </label>
                <input
                    type="number"
                    name="page_size"
                    value="{{ old('page_size', 500) }}"
                    min="1"
                    max="5000"
                    class="mt-1 w-full rounded-lg border p-3 focus:outline-none focus:ring"
                    placeholder="e.g. 500"
                >
                <p class="mt-1 text-xs text-gray-500">
                    Recommended: 200–500 to avoid timeouts
                </p>

                @error('page_size')
                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">
                    Current page
                </label>
                <input
                    type="number"
                    name="current_page"
                    value="{{ old('current_page', 1) }}"
                    min="1"
                    class="mt-1 w-full rounded-lg border p-3 focus:outline-none focus:ring"
                    placeholder="e.g. 1"
                >
                <p class="mt-1 text-xs text-gray-500">
                    Start page for Axenita pagination
                </p>

                @error('current_page')
                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <button
                class="w-full rounded-lg bg-black text-white py-3 font-semibold hover:opacity-90"
            >
                Submit
            </button>
        </form>
    </div>

    <p class="text-xs text-gray-500 mt-4 text-center">
        Protected by Laravel CSRF. Public form only.
    </p>
</div>
</body>
</html>
