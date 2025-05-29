@extends('layouts.table-layout2')

@section('title', __('messages.grade_details'))

@section('content')
    <div class="">
        <div class="">
            <!-- School Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">{{ __('messages.basic_information') }}</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <h2 class="mb-3">{{ __('messages.details') }}: {{ $grade->school->name }} -
                                    {{ $grade->name }}</h2>
                                <div>
                                    <a href="{{ route('admin.class_sections.create', ['grade_id' => $grade->id]) }}"
                                        class="btn btn-primary">
                                        <i class="fas fa-plus"></i> {{ __('messages.create_class_section') }}
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __('messages.total_class_sections') }}:</strong> {{ $grade->classSections->count() }}
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __('messages.total_students') }}:</strong>
                            {{ $grade->classSections->flatMap(function ($section) {
        return $section->users()->whereHas('roles', fn($q) => $q->where('name', 'student'))->get();
    })->count() }}
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __('messages.academic_year') }}:</strong> {{ $grade->academicYear->name ?? '-' }}
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm p-4 mb-4">
                <h4 class="mb-3">{{ __('messages.class_sections') }}</h4>

                @if($grade->classSections->isEmpty())
                    <div class="alert alert-info">{{ __('messages.no_class_sections_available') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle" id="classSectionsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.class_section_name') }}</th>
                                    <th>{{ __('messages.number_of_students') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grade->classSections as $section)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $section->name }}</td>
                                        <td>
                                            {{ $section->users()->whereHas('roles', fn($q) => $q->where('name', 'student'))->count() }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('attendance.index', $section->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="feather-edit"></i> {{ __('messages.attendance_log') }}
                                            </a>
                                            <a href="{{ route('admin.class_sections.show', $section->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="feather-eye"></i> {{ __('messages.view_students') }}
                                            </a>
                                            <a href="{{ route('admin.class_sections.edit', $section->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="feather-edit"></i> {{ __('messages.edit') }}
                                            </a>
                                            <form action="{{ route('admin.class_sections.destroy', $section->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                                                    <i class="feather-trash-2"></i> {{ __('messages.delete') }}
                                                </button>
                                            </form>
                                            <a href="{{ route('material-assignments.show', $section->id) }}"
                                                class="btn btn-sm btn-secondary">
                                                <i class="feather-edit"></i> {{ __('messages.view_materials') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                <div class="mt-4">
                    <a href="{{ route('grade_levels.show', [$grade->school, $grade->grade_level]) }}"
                        class="btn btn-secondary mt-3">
                        <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#classSectionsTable').DataTable({
                dom: 'Bfrtip',
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
        });
    </script>
@endpush