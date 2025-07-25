@extends('layouts.table-layout2')

@section('title', __('messages.children_list'))

@section('content')
    <div class="mb-2">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">{{ __('messages.children_list') }}</h3>
            </div>
        </div>
    </div>

    <!-- Children Table -->
    <div class="card">
        <div class="card-header">
            <h5>{{ __('messages.children_list') }}</h5>
        </div>
        <div class="card-body">
            @if ($children->count() > 0)
                <table class="table table-bordered table-striped" id="childrenTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.email') }}</th>
                            <th>{{ __('messages.class_section') }}</th>
                            <th>{{ __('messages.grade') }}</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}" id="nameSearch"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}" id="emailSearch"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}" id="sectionSearch"></th>
                            <th><input type="text" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}" id="gradeSearch"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($children as $child)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $child->name }}</td>
                                <td>{{ $child->email }}</td>
                                <td>{{ optional($child->currentStudentClassSection->classSection)->name ?? '' }}</td>
                                <td>{{ optional($child->currentStudentClassSection->classSection->grade)->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center">{{ __('messages.no_children') }}</p>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        let table = $('#childrenTable').DataTable({
            dom: 'Bfrtip',
            order: [[0, 'asc']],
            orderCellsTop: true,
            fixedHeader: true,
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '{{ __("messages.export_excel") }}',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '{{ __("messages.export_pdf") }}',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible'
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

        $('#nameSearch').on('keyup', function () {
            table.column(1).search(this.value).draw();
        });
        $('#emailSearch').on('keyup', function () {
            table.column(2).search(this.value).draw();
        });
        $('#sectionSearch').on('keyup', function () {
            table.column(3).search(this.value).draw();
        });
        $('#gradeSearch').on('keyup', function () {
            table.column(4).search(this.value).draw();
        });
    });
</script>
@endpush
