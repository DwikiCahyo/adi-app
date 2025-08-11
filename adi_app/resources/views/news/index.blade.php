{{-- resources/views/news/index.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>News List</title>
</head>
<body>
    <h1>News List</h1>
    
    @if($news->count() > 0)
        @foreach($news as $item)
            <div style="border: 1px solid #ccc; margin: 10px; padding: 10px;">
                <h3>{{ $item->title }}</h3>
                <p><strong>Content:</strong> {{ Str::limit($item->content, 100) }}</p>
                <p><strong>Created by:</strong> {{ $item->creator?->name ?? 'Unknown' }}</p>
                <p><strong>Created at:</strong> {{ $item->created_at?->format('d M Y H:i') }}</p>
                <hr>
            </div>
        @endforeach
    @else
        <p>No news found.</p>
    @endif
</body>
</html>