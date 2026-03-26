<x-filament::page>
    <table style="width:100%; text-align:right;">
        <thead>
            <tr>
                <th>الجمعية</th>
                <th>التاريخ</th>
                <th>الحجم</th>
            </tr>
        </thead>
        <tbody>
            @foreach($this->backups as $backup)
                <tr>
                    <td>{{ $backup['name'] }}</td>
                    <td>{{ $backup['date'] }}</td>
                    <td>{{ $backup['size'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-filament::page>
