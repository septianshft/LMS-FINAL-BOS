<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="{{asset('css//output.css')}}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <!-- CSS -->
    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
</head>
<body class="font-poppins p-10">
    <h1 class="text-2xl font-bold mb-4">Your Cart</h1>
    <ul class="space-y-4">
        @forelse($items as $item)
            <li class="flex justify-between items-center border-b pb-2">
                <span>{{ $item->course->name }}</span>
                <div class="flex gap-2 items-center">
                    <form action="{{ route('courses.join', $item->course->slug) }}" method="POST">
                        @csrf
                        <button class="px-2 py-1 rounded bg-blue-600 text-white text-sm">Join Now</button>
                    </form>
                    <form action="{{ route('cart.destroy', $item) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600 text-sm">Remove</button>
                    </form>
                </div>
            </li>
        @empty
            <li>No items in cart.</li>
        @endforelse
    </ul>
</body>
</html>
