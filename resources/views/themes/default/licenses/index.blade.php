<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title ?? 'تراخيص الجمعية' }}</title>
</head>
<body>
    <h1>{{ $page->title ?? 'تراخيص الجمعية' }}</h1>
    <div>{!! $page->content ?? '' !!}</div>
</body>
</html>
