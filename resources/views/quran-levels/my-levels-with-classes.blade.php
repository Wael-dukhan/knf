@extends('layouts.table-layout2')

@section('title', __('quran_levels.my_levels'))

@section('content')
    <div class="">
        <div class="">
            <!-- Page Header -->
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">
                        {{ __('quran_levels.my_levels') }}
                    </h3>
                </div>
            </div>

            @if($levels->isEmpty())
                <div class="alert alert-warning text-center mt-3">
                    {{ __('messages.no_levels_found_for_teacher') }}
                </div>
            @else
                @foreach($levels as $item)
                    <div class="card card-table mt-4">
                        <div class="card-header">
                            <h4>{{ $item['level']->name }} - 
                                <small>{{ $item['level']->academicYear->name ?? '-' }}</small>
                            </h4>
                            <p>{{ $item['level']->description ?? __('messages.no_description') }}</p>
                        </div>
                        <div class="card-body">
                            @if($item['classes']->isEmpty())
                                <div class="alert alert-info text-center">
                                    {{ __('quran_levels.no_classes_for_level') }}
                                </div>
                            @else
                                <table class="table table-bordered table-hover mb-0" id="classesTable_{{ $item['level']->id }}">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('messages.class_name') }}</th>
                                            <th>{{ __('messages.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($item['classes'] as $class)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $class->name }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('quran-classes.show', $class->id) }}" class="btn btn-sm btn-info">
                                                        <i class="feather-eye"></i> {{ __('messages.view') }}
                                                    </a>
                                                    <a href="{{ route('quran_student_attendance.index', $class->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="feather-check-circle"></i> {{ __('messages.student_attendance_log') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif

            <div class="mt-4">
                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        @foreach($levels as $item)
            $('#classesTable_{{ $item['level']->id }}').DataTable({
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
        @endforeach
    });
</script>
@endpush
