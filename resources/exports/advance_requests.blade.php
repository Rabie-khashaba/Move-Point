<table>
    <thead>
        <tr>
            <th>م</th>
            <th>مقدم الطلب</th>
            <th>نوع الموظف</th>
            <th>المبلغ</th>
            <th>التقسيط</th>
            <th>عدد الأشهر</th>
            <th>القسط الشهري</th>
            <th>المحافظة</th>
            <th>المنطقة</th>
            <th>الحالة</th>
            <th>تاريخ الطلب</th>
        </tr>
    </thead>
    <tbody>
        @foreach($advances as $index => $advance)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $advance->requester_name }}</td>
                <td>
                    @if($advance->representative)
                        مندوب
                    @elseif($advance->employee)
                        موظف
                    @elseif($advance->supervisor)
                        مشرف
                    @else
                        غير محدد
                    @endif
                </td>
                <td>{{ number_format($advance->amount, 2) }}</td>
                <td>{{ $advance->is_installment ? 'نعم' : 'لا' }}</td>
                <td>{{ $advance->is_installment ? $advance->installment_months : '-' }}</td>
                <td>{{ $advance->is_installment ? number_format($advance->monthly_installment, 2) : '-' }}</td>
                <td>{{ $advance->representative?->governorate?->name ?? 'غير محدد' }}</td>
                <td>{{ $advance->representative?->location?->name ?? 'غير محدد' }}</td>
                <td>
                    @if($advance->status === 'pending')
                        في الانتظار
                    @elseif($advance->status === 'approved')
                        تمت الموافقة
                    @else
                        مرفوض
                    @endif
                </td>
                <td>{{ $advance->created_at->format('Y-m-d') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
