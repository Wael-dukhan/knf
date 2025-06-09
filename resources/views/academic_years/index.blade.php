@extends('layouts.table-layout2')

@section('title', __('messages.academic_years_list'))

@section('content')
<div class="">
    <div class="">
        <style>
            table.table-bordered.dataTable th,
            table.table-bordered.dataTable td {
                text-align: center;
                vertical-align: middle;
            }
        </style>

        <div class="card shadow-sm p-4 mb-4" style="background-color: #fff;">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h2 class="mb-0">
                    {{ __('messages.academic_years_list') }}
                </h2>
                <a href="{{ route('admin.academic_years.create') }}" class="btn btn-outline-primary">
                    <i class="feather-plus"></i> {{ __('messages.create_new_academic_year') }}
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if($academicYears->isEmpty())
                <div class="alert alert-info text-center">
                    {{ __('messages.no_academic_years_found') }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="academicYearsTable" style="min-width: 900px;">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>
                                    {{ __('messages.school') }} <br>
                                </th>
                                <th>
                                    {{ __('messages.academic_year') }} <br>
                                </th>
                                <th>
                                    {{ __('messages.start_date') }} <br>
                                </th>
                                <th>
                                    {{ __('messages.end_date') }} <br>
                                </th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                            <tr>
                                <th>
                                    <input type="text" class="column_search form-control form-control-sm mt-1" data-column="1" placeholder="{{ __('messages.search') }}">
                                </th>
                                <th>
                                    <input type="text" class="column_search form-control form-control-sm mt-1" data-column="2" placeholder="{{ __('messages.search') }}">
                                </th>
                                <th>
                                    <input type="text" class="column_search form-control form-control-sm mt-1" data-column="3" placeholder="{{ __('messages.search') }}">
                                </th>
                                <th>
                                    <input type="text" class="column_search form-control form-control-sm mt-1" data-column="4" placeholder="{{ __('messages.search') }}">
                                </th>
                                <th>
                                    <input type="text" class="column_search form-control form-control-sm mt-1" data-column="5" placeholder="{{ __('messages.search') }}">
                                </th>
                                <th></th>
                        </thead>
                        <tbody>
                            @foreach($academicYears as $index => $year)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $year->school->name ?? '-' }}</td>
                                    <td>{{ $year->name }}</td>
                                    <td>{{ $year->start_date ? \Carbon\Carbon::parse($year->start_date)->format('Y-m-d') : '-' }}</td>
                                    <td>{{ $year->end_date ? \Carbon\Carbon::parse($year->end_date)->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.academic_years.edit', $year->id) }}" class="btn btn-warning btn-sm me-1">
                                            <i class="feather-edit"></i> {{ __('messages.edit') }}
                                        </a>
                                        <form action="{{ route('admin.academic_years.destroy', $year->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.are_you_sure') }}')">
                                                <i class="feather-trash-2"></i> {{ __('messages.delete') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            var table = $('#academicYearsTable').DataTable({
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
            $('input.column_search').on('keyup change clear', function () {
                var columnIndex = $(this).data('column');
                table.column(columnIndex).search(this.value).draw();
            });
        });
    </script>
@endpush
