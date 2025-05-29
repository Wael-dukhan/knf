@extends('layouts.table-layout2')

@section('title', __('messages.grade_level_details'))

@section('content')
<div class="">
    <!-- Page Header -->
    <div class="row align-items-center mb-3">
        <div class="col">
            <p class="text-muted">
                {{ __('messages.school') }}: {{ $school->name ?? '-' }}
            </p>
            <h3 class="page-title">
                {{ __('messages.grade_level_details') }}: {{ $gradeLevel->name }}
            </h3>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.grades.create', [ 'grade_level' => $gradeLevel->id ]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.create_grade') }}
            </a>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="card card-table">
        <div class="card-body">
            <h5 class="card-title">{{ __('messages.grades_list') }}</h5>

            @if ($grades->isEmpty())
                <div class="alert alert-info">{{ __('messages.no_grades_available') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-center mb-0" id="gradesTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.grade_name') }}</th>
                                <th>{{ __('messages.school_name') }}</th>
                                <th>{{ __('messages.academic_year') }}</th>
                                <th class="text-end">{{ __('messages.actions') }}</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th>
                                    <input type="text" id="nameSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}">
                                </th>
                                <th>
                                    <input type="text" id="schoolSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}">
                                </th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grades as $index => $grade)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $grade->name }}</td>
                                    <td>{{ $grade->school->name ?? '-' }}</td>
                                    <td>{{ $grade->academicYear->name }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.grades.show', $grade->id) }}" class="btn btn-info btn-sm me-2">
                                            <i class="feather-eye"></i> {{ __('messages.view_class_sections') }}
                                        </a>
                                        <a href="{{ route('admin.grades.edit', $grade->id) }}" class="btn btn-warning btn-sm">
                                            <i class="feather-edit"></i> {{ __('messages.edit') }}
                                        </a>
                                        <form action="{{ route('admin.grades.destroy', $grade->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                                                <i class="feather-trash-2"></i> {{ __('messages.delete') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <a href="{{ route('grade_levels.index', [$grade->grade_level ]) }}" class="btn btn-secondary mt-3">
                        <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if ($grades->count() > 0)
<script>
    $(document).ready(function () {
        let table = $('#gradesTable').DataTable({
            dom: 'Bfrtip',
            order: [[0, 'asc']],
            orderCellsTop: true,
            fixedHeader: true,
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

        // Column search
        $('#nameSearch').on('keyup', function () {
            table.column(1).search(this.value).draw();
        });

        $('#schoolSearch').on('keyup', function () {
            table.column(2).search(this.value).draw();
        });
    });
</script>
@endif
@endpush
