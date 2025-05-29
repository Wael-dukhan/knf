@extends('layouts.table-layout')

@section('title', __('messages.users_list'))

@section('content')
    <style>
        #usersTable_wrapper {
            background: #fff;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }

        #usersTable {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        #usersTable thead {
            background-color: #f1f3f5;
            color: #212529;
        }

        #usersTable tbody td {
            padding: 0.85rem;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        #usersTable tbody tr:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s ease-in-out;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 0.45rem 0.75rem;
            font-size: 0.875rem;
        }

        .dt-buttons .btn {
            margin: 0 0.3rem 1rem 0;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .btn-primary,
        .btn-danger,
        .btn-warning {
            font-size: 0.875rem;
            padding: 0.4rem 0.75rem;
            border-radius: 0.375rem;
        }
    </style>

    <h2>{{ __('messages.users_list') }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3">{{ __('messages.create_new_user') }}</a>
    <button id="downloadPdf" class="btn btn-danger mb-3 float-end">{{ __('messages.export_pdf') }}</button>

    <div id="pdfContent">
        <table id="usersTable" class="table  table-striped table-bordered">
            <thead>
                <tr>
                    <th>{{ __('messages.number') }}</th> <!-- عمود الترقيم -->
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.email') }}</th>
                    <th>{{ __('messages.role') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                    <tr>
                        <td class="user-number">{{ $index + 1 }}</td> <!-- الترقيم الثابت قابل للترتيب -->
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if ($user->roles->isNotEmpty())
                                {{ $user->roles->pluck('name')->join(', ') }}
                            @else
                                {{ __('messages.no_roles_found') }}
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                class="btn btn-warning btn-sm">{{ __('messages.edit') }}</a>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">{{ __('messages.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#usersTable').DataTable({
                dom: 'Bfrtip',
                order: [[0, 'asc']], // ترتيب تصاعدي في البداية حسب العمود الأول
                columnDefs: [
                    { orderable: true, targets: 0 } // تمكين الترتيب على العمود الأول
                ],
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '{{ __("messages.export_excel") }}',
                        title: '{{ __("messages.users_list") }}',
                        exportOptions: {
                            columns: ':visible:not(:last-child)' // إخفاء العمود الأخير (الإجراءات) عند التصدير إلى Excel
                        }
                    },
                    {
                        extend: 'print',
                        text: '{{ __("messages.print") }}',
                        title: '{{ __("messages.users_list") }}',
                        customize: function (win) {
                            $(win.document.body)
                                .css('direction', 'rtl')
                                .css('text-align', 'right')
                                .find('table')
                                .addClass('table table-bordered')
                                .css('direction', 'rtl');

                            // إخفاء العمود الأخير (عمود الإجراءات) عند الطباعة
                            $(win.document.body).find('table th:last-child, table td:last-child').css('display', 'none');
                        }
                    }
                ],
                language: {
                    search: "{{ __('messages.search') }}",
                    lengthMenu: "{{ __('messages.show') }} _MENU_",
                    info: "{{ __('messages.showing') }} _START_ {{ __('messages.to') }} _END_ {{ __('messages.of') }} _TOTAL_",
                    paginate: {
                        previous: "{{ __('messages.previous') }}",
                        next: "{{ __('messages.next') }}"
                    }
                }
            });
        });

        document.getElementById('downloadPdf').addEventListener('click', function () {
            let table = $('#usersTable').DataTable();

            // عرض كل الصفوف مؤقتاً
            table.page.len(-1).draw();

            // إخفاء العمود الأخير (عمود الإجراءات) قبل التصدير
            $('#usersTable th:last-child, #usersTable td:last-child').css('display', 'none');

            setTimeout(() => {
                // استهداف الجدول فقط للطباعة
                const element = document.getElementById('usersTable');

                const opt = {
                    margin: 0.5,  // تقليل الهوامش
                    filename: 'users_list.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: {
                        scale: 2,  // زيادة دقة الصورة لتقليل التأثير على وضوح الخطوط
                        letterRendering: true // يساعد في تحسين وضوح النصوص
                    },
                    jsPDF: {
                        unit: 'in',
                        format: 'a4',
                        orientation: '{{ app()->getLocale() == "ar" ? "portrait" : "portrait" }}',
                        autoSize: true, // تحسين الطباعة لتفادي القص
                        compressPdf: true  // تقليل الحجم للحد من القطع
                    }
                };

                html2pdf().set(opt).from(element).save().then(() => {
                    // إعادة pagination للوضع الطبيعي
                    table.page.len(10).draw();

                    // إعادة عرض العمود الأخير (عمود الإجراءات) بعد تنزيل PDF
                    $('#usersTable th:last-child, #usersTable td:last-child').css('display', '');
                });
            }, 500); // تأخير بسيط لضمان تحديث الجدول قبل التصدير
        });

    </script>
@endpush