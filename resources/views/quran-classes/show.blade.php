@extends('layouts.table-layout2')

@section('title', __('messages.quran_class_details'))

@section('content')

<div class="">
    <div class="">
        <!-- Page Header -->
        <div class="">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">{{ __('messages.quran_class_details') }}</h3>
                </div>
            </div>
        </div>

        <!-- معلومات الحلقة القرآنية -->
        <div class="card mb-4">
            <div class="card-body">
                <table class="table table-bordered mb-0">
                    <tr>
                        <th>{{ __('messages.circle_name') }}</th>
                        <td>{{ $quranClass->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.quran_level') }}</th>
                        <td>{{ $quranClass->quranLevel->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.teacher') }}</th>
                        <td>{{ $quranClass->teacher->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.student_count') }}</th>
                        <td>{{ $quranClass->students->count() }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- جدول عرض الطلاب -->
        <div class="card card-table">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-3">{{ __('messages.students_list') }}</h4>
                    <a href="{{ route('quranClass.assign_students.form', [$quranClass->id]) }}" class="btn btn-outline-primary">
                        <i class="feather-list"></i> {{ __('messages.assign_student_to_quran_classes') }}
                    </a>
                </div>
                @if($quranClass->students->isEmpty())
                    <div class="alert alert-warning text-center">{{ __('messages.no_students_found') }}</div>
                @else
                    <table class="table table-bordered table-hover table-center mb-0" id="studentsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.student_name') }}</th>
                                <th>{{ __('messages.student_email') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th><input type="text" id="nameSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}"></th>
                                <th><input type="text" id="emailSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quranClass->students as $student)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->email ?? '-' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.users.show', $student->id) }}" class="btn btn-sm bg-info-light me-2">
                                            <i class="feather-eye"></i> {{ __('messages.view') }}
                                        </a>
                                        <a href="{{ route('admin.users.edit', $student->id) }}" class="btn btn-sm bg-warning-light me-2">
                                            <i class="feather-edit"></i> {{ __('messages.edit') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="col-auto text-end float-end ms-auto">
    <a href="{{ route('quran-levels.show', $quranClass->id) }}" class="btn btn-secondary">
        <i class="feather-arrow-left"></i> {{ __('messages.back') }}
    </a>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        let table = $('#studentsTable').DataTable({
            dom: 'Bfrtip',
            orderCellsTop: true,
            fixedHeader: true,
            order: [[0, 'asc']],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '{{ __("messages.export_excel") }}',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '{{ __("messages.export_pdf") }}',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    },
                    customize: function (doc) {
                        doc.defaultStyle.alignment = 'right';
                        doc.styles.tableHeader.alignment = 'center';
                    }
                },
                {
                    extend: 'print',
                    text: '{{ __("messages.print") }}',
                    customize: function (win) {
                        $(win.document.body)
                            .css('direction', 'rtl')
                            .css('text-align', 'right')
                            .find('table')
                            .addClass('table table-bordered');

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

        // فلترة الأعمدة
        $('#nameSearch').on('keyup', function () {
            table.column(1).search(this.value).draw();
        });

        $('#emailSearch').on('keyup', function () {
            table.column(2).search(this.value).draw();
        });
    });
</script>
@endpush
