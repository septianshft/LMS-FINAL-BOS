<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="{{asset('css//output.css')}}" rel="stylesheet">
</head>
<body class="text-black font-poppins pt-10 pb-[50px]">
    <div class="max-w-[1200px] mx-auto w-full flex flex-col gap-10">
        @include('front.partials.nav')
        <h2 class="font-bold text-2xl">Submit Task</h2>
        <div class="bg-white p-6 rounded-2xl shadow w-full max-w-lg">
            <p class="font-semibold mb-4">{{ $task->name }}</p>
            <p class="mb-4">{{ $task->description }}</p>
            <form method="POST" action="{{ route('task.submit.store', $task) }}" enctype="multipart/form-data" class="flex flex-col gap-4">
                @csrf
                <div>
                    <label class="block mb-1">File (optional)</label>
                    <input type="file" name="file">
                </div>
                <div>
                    <label class="block mb-1">Answer (optional)</label>
                    <textarea name="answer" class="w-full border rounded p-2"></textarea>
                </div>
                <button type="submit" class="p-2 bg-blue-600 text-white rounded">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>
