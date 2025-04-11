<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR-Code-Generator</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Stelle sicher, dass du das Package simplesoftwareio/simple-qrcode installiert hast -->
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">QR-Code-Generator</h1>
        <form action="{{ route('admin.qrcodes') }}" method="post" class="mb-6">
            @csrf
            <label for="number" class="block mb-2">Anzahl der QR-Code-Sets (jeweils A, B, C, D):</label>
            <input type="number" name="number" id="number" min="1" class="border p-2 mr-2" required>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Erstellen</button>
        </form>

        @if($n > 0)
            <h2 class="text-xl font-semibold mb-4">Erzeugte QR-Code-Sets ({{ $n }} Set{{ $n > 1 ? 's' : '' }}):</h2>
            @foreach($qrSets as $index => $set)
                <div class="mb-6 border p-4 rounded">
                    <h3 class="font-bold mb-2">Set {{ $index + 1 }}</h3>
                    <div class="flex flex-wrap space-x-4">
                        @foreach(['A', 'B', 'C', 'D'] as $letter)
                            <div class="mb-4">
                                <p class="font-semibold">Option {{ $letter }}</p>
                                {{-- Generiere den QR-Code mithilfe der QrCode-Facade --}}
                                {!! QrCode::size(150)->generate($set[$letter]) !!}
                                <p class="mt-2 text-xs break-all">{{ $set[$letter] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</body>
</html>
