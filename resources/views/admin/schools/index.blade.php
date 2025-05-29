@extends('layouts.table-layout2')

@section('title', __('messages.schools_list'))

@section('content')

    <div class="">
        <div class="">
            <!-- Page Header -->
            <div class="">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ __('messages.schools_list') }}</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto">
                        <a href="{{ route('admin.schools.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('messages.create_school') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Schools Table -->
            <div class="card card-table">
                <div class="card-body">
                    <table class="table table-bordered table-hover table-center mb-0" id="schoolsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.name') }}
                                <th>{{ __('messages.location') }} 
                                <th class="">{{ __('messages.actions') }}</th>
                            </tr>
                            <tr>
                                <th>
                                    <input type="text" id="nameSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}"></th>
                                </th>
                                <th>
                                    <input type="text" id="locationSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}"></th>
                                </th>
                                <th>
                                    <input type="text" id="contactNumberSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}"></th>
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schools as $school)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $school->name }}</td>
                                    <td>{{ $school->location }}</td>
                                    <td class="text-end">
                                        <div class="actions">
                                            <a href="{{ route('grade_levels.index', $school->id) }}" class="btn btn-sm bg-info-light me-2">
                                                <i class="feather-eye"></i> {{ __('messages.view_grade_levels') }}
                                            </a>
                                            <a href="{{ route('teacher-attendance.index', $school->id) }}" class="btn btn-sm bg-primary me-2">
                                                <i class="feather-eye"></i> {{ __('messages.teacher_attendance_log') }}
                                            </a>
                                            <a href="{{ route('admin.schools.edit', $school->id) }}" class="btn btn-sm bg-warning-light me-2">
                                                <i class="feather-edit"></i> {{ __('messages.edit') }}
                                            </a>
                                            <form action="{{ route('admin.schools.destroy', $school->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm bg-danger-light" onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                                                    <i class="feather-trash-2"></i> {{ __('messages.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            let table = $('#schoolsTable').DataTable({
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

            $('#locationSearch').on('keyup', function () {
                table.column(2).search(this.value).draw();
            });

            $('#contactNumberSearch').on('keyup', function () {
                table.column(3).search(this.value).draw();
            });
        });
    </script>
@endpush
