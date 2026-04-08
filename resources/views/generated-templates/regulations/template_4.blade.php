@extends('themes.default.layouts.app')

@section('content')
<div style="direction: rtl; text-align: right; max-width: 1100px; margin: 40px auto; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; box-sizing: border-box;">
    
    <div style="border-bottom: 2px solid {{ $siteSettings->primary_color ?? '#2ea36b' }}; padding-bottom: 15px; margin-bottom: 35px;">
        <h1 style="color: #1a4a38; font-size: 28px; font-weight: bold; margin: 0;">السياسات</h1>
    </div>

    @if(isset($items) && (is_array($items) ? count($items) > 0 : $items->count() > 0))
        <div style="display: flex; flex-direction: column; gap: 15px;">
            @foreach($items as $item)
                <div style="display: flex; align-items: center; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.03); flex-wrap: wrap; gap: 15px;">
                    
                    <div style="flex: 1 1 250px;">
                        <h2 style="font-size: 18px; font-weight: 600; color: #2d3748; margin: 0 0 8px 0;">
                            {{ $item->title ?? 'بدون عنوان' }}
                        </h2>
                        
                        @if(!empty($item->description))
                            <p style="font-size: 14px; color: #718096; margin: 0 0 12px 0; line-height: 1.6;">
                                {{ $item->description }}
                            </p>
                        @endif

                        @if(!empty($item->fileMedia))
                            <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-top: 10px;">
                                @if(!empty($item->fileMedia->extension))
                                    <span style="font-size: 12px; color: #4a5568; background-color: #edf2f7; padding: 4px 10px; border-radius: 4px; border: 1px solid #e2e8f0;">
                                        نوع الملف: <strong style="text-transform: uppercase;">{{ $item->fileMedia->extension }}</strong>
                                    </span>
                                @endif
                                
                                @if(!empty($item->fileMedia->size))
                                    <span style="font-size: 12px; color: #4a5568; background-color: #edf2f7; padding: 4px 10px; border-radius: 4px; border: 1px solid #e2e8f0;">
                                        الحجم: <span dir="ltr"><strong>{{ number_format($item->fileMedia->size / 1024 / 1024, 2) }} MB</strong></span>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div style="flex: 1 1 150px; display: flex; justify-content: flex-end; margin-right: auto;">
                        @if(!empty($item->fileMedia) && !empty($item->fileMedia->url))
                            <a href="{{ \App\Support\Media\MediaUrl::forDiskPath('public', ltrim($item->fileMedia->url, '/')) }}" target="_blank" style="display: inline-block; padding: 10px 24px; background-color: {{ $siteSettings->primary_color ?? '#2ea36b' }}; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: bold; border: 1px solid {{ $siteSettings->primary_color ?? '#2ea36b' }}; white-space: nowrap;">
                                عرض الملف
                            </a>
                        @else
                            <span style="display: inline-block; padding: 10px 24px; background-color: #f7fafc; color: #a0aec0; border-radius: 6px; font-size: 14px; border: 1px solid #e2e8f0; cursor: not-allowed; white-space: nowrap;">
                                لا يوجد ملف
                            </span>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px; background-color: #f9fafb; border: 2px dashed #cbd5e0; border-radius: 8px;">
            <p style="font-size: 18px; color: #718096; margin: 0; font-weight: 500;">
                لا توجد سياسات
            </p>
        </div>
    @endif

</div>
@endsection
