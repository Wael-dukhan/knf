@extends('layouts.table-layout2')

@section('title', __('messages.class_section_details'))

@section('content')
<div class="">
    <!-- Page Header -->
    <div class="row align-items-center mb-3">
        <div class="col">
            <p class="text-muted">
                {{ __('messages.class_section_name') }}: {{ $classSection->name }}
            </p>
            <h3 class="page-title">
                {{ __('messages.teacher_assignments') }}
            </h3>
        </div>
        <div class="col-auto">
            <a href="{{ route('materials.create', $classSection->id) }}" class="btn btn-primary">
                <i class="feather-edit"></i> {{ __('messages.create_material') }}
            </a>
        </div>
    </div>

    <!-- Teacher Assignments Table -->
    <div class="card card-table">
        <div class="card-body">
            <h5 class="card-title">{{ __('messages.teacher_assignments_list') }}</h5>

            @if ($materials->isEmpty())
                <div class="alert alert-info">{{ __('messages.no_materials_available') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-center mb-0" id="assignmentsTable">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.material_name') }}</th>
                                <th>{{ __('messages.teacher_name') }}</th>
                                <th>{{ __('messages.academic_year') }}</th>
                                <th>{{ __('messages.term') }}</th>
                                <th>{{ __('messages.class_section') }}</th>
                                <th class="text-end">{{ __('messages.actions') }}</th>
                            </tr>
                            <tr>
                                <th>
                                    <input type="text" id="materialNameSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}">
                                </th>
                                <th>
                                    <input type="text" id="teacherNameSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}">
                                </th>
                                <th>
                                    <input type="text" id="academicYearSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}">
                                </th>
                                <th>
                                    <input type="text" id="termSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}">
                                </th>
                                <th>
                                    <input type="text" id="classSectionSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}">
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materials as $material)
                                <tr>
                                    <td>{{ $material->name }}</td>
                                    <td>{{ $assignments[$material->id]->teacher->name ?? __('messages.not_assigned') }}</td>
                                    <td>{{ $assignments[$material->id]->academicYear->name ?? __('messages.not_assigned') }}</td>
                                    <td>{{ $assignments[$material->id]->term->name ?? __('messages.not_assigned') }}</td>
                                    <td>{{ $assignments[$material->id]->classSection->name ?? __('messages.not_assigned') }}</td>
                                    <td class="text-end">
                                        @if(isset($assignments[$material->id]))
                                            <a href="{{ route('material-assignment.edit', [ $assignments[$material->id] ]) }}" class="btn btn-primary btn-sm">
                                                <i class="feather-edit"></i> {{ __('messages.edit') }}
                                            </a>
                                            <form action="{{ route('material-assignments.destroy', [$assignments[$material->id]]) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                                                    <i class="feather-trash"></i> {{ __('messages.delete') }}
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('material-assignment.create', ['classSectionId' => $classSection->id, 'materialId' => $material->id]) }}" class="btn btn-success btn-sm">
                                                <i class="feather-plus"></i> {{ __('messages.assign') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">
                    <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if ($materials->count() > 0)
<script>
    $(document).ready(function () {
        let table = $('#assignmentsTable').DataTable({
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
                        doc.content.splice(0, 0, {
                            text: '{{ $classSection->name }}',
                            alignment: 'center',
                            margin: [0, 0, 0, 12],
                            fontSize: 14,
                            bold: true
                        });
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
    });

    // Column search
    $('#materialNameSearch').on('keyup', function () {
        table.column(0).search(this.value).draw();
    });
    $('#teacherNameSearch').on('keyup', function () {
        table.column(1).search(this.value).draw();
    });
    $('#academicYearSearch').on('keyup', function () {
        table.column(3).search(this.value).draw();
    });
    $('#termSearch').on('keyup', function () {
        table.column(4).search(this.value).draw();
    });
    $('#classSectionSearch').on('keyup', function () {
        table.column(5).search(this.value).draw();
    });
    
</script>
@endif
@endpush
