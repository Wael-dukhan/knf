@extends('layouts.table-layout2')

@section('title', __('messages.quran_classes_list'))

@section('content')
    <div class="">
        <div class="">
            <!-- Page Header -->
            <div class="">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ __('messages.quran_classes_list') }}</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto">
                        <a href="{{ route('quran-classes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('messages.quran_classes_list') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Quran Classes Table -->
            <div class="card card-table">
                <div class="card-body">
                    <table class="table table-bordered table-hover table-center mb-0" id="quranClassesTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.circle_name') }}</th>
                                <th>{{ __('messages.quran_level') }}</th>
                                <th>{{ __('messages.school') }}</th>
                                <th>{{ __('messages.teacher') }}</th>
                                <th>{{ __('messages.student_count') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th><input type="text" id="classNameSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}"></th>
                                <th><input type="text" id="quranLevelSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}"></th>
                                <th><input type="text" id="schoolSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}"></th>
                                <th><input type="text" id="teacherSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}"></th>
                                <th><input type="text" id="studentCountSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quranClasses as $class)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $class->name }}</td>
                                    <td>{{ $class->quranLevel->name ?? '-' }}</td>
                                    <td>{{ $class->quranLevel->school->name ?? '-' }}</td>
                                    <td>{{ $class->teacher->name ?? '-' }}</td>
                                    <td>{{ $class->students_count ?? $class->students()->count() }}</td>
                                    <td class="text-end">
                                        <div class="actions">
                                            <a href="{{ route('quran-classes.show', $class->id) }}" class="btn btn-sm bg-info-light me-2">
                                                <i class="feather-eye"></i> {{ __('messages.view') }}
                                            </a>
                                            <a href="{{ route('quran-classes.edit', $class->id) }}" class="btn btn-sm bg-warning-light me-2">
                                                <i class="feather-edit"></i> {{ __('messages.edit') }}
                                            </a>
                                            <form action="{{ route('quran-classes.destroy', $class->id) }}" method="POST" style="display:inline;">
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
            let table = $('#quranClassesTable').DataTable({
                dom: 'Bfrtip',
                orderCellsTop: true,
                fixedHeader: true,
                order: [[0, 'asc']],
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '{{ __("messages.export_excel") }}',
                        exportOptions: { columns: ':visible:not(:last-child)' }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '{{ __("messages.export_pdf") }}',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: { columns: ':visible:not(:last-child)' },
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

            // فلترة الأعمدة بنصوص البحث
            $('#classNameSearch').on('keyup', function () {
                table.column(1).search(this.value).draw();
            });
            $('#quranLevelSearch').on('keyup', function () {
                table.column(2).search(this.value).draw();
            });
            $('#schoolSearch').on('keyup', function () {
                table.column(3).search(this.value).draw();
            });
            $('#teacherSearch').on('keyup', function () {
                table.column(4).search(this.value).draw();
            });
            $('#studentCountSearch').on('keyup', function () {
                table.column(5).search(this.value).draw();
            });
        });
    </script>
@endpush
