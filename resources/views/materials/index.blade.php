@extends('layouts.table-layout2')

@section('title', __('messages.materials_list'))

@section('content')
    <div class="mb-2">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">{{ __('messages.materials_list') }}</h3>
            </div>
            <div class="col-auto text-end float-end ms-auto">
                <a href="{{ route('materials.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> {{ __('messages.create_material') }}</a>
            </div>
        </div>
    </div>
    <!-- Material Table -->
    <div class="card">
        <div class="card-header">
            <h5>{{ __('messages.materials_list') }}</h5>
        </div>
        <div class="card-body">
            @if ($materials->count() > 0)
                <table class="table table-bordered table-striped" id="materialsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.school') }}</th>
                            <th>{{ __('messages.grade') }} </th>
                            <th>{{ __('messages.academic_year') }}</th>
                            <th>{{ __('messages.main_book') }}</th>
                            <th>{{ __('messages.activity_book') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>
                                <input type="text" id="nameSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}">
                            </th>
                            <th>
                                <input type="text" id="schoolSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}">
                            </th>
                            <th>
                                <input type="text" id="gradeSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}">
                            </th>
                            <th>
                                <input type="text" id="academicYearSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}">
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materials as $material)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $material->name }}</td>
                                <td>{{ $material->grade->school->name }}</td>
                                <td>{{ $material->grade->name }}</td>
                                <td>{{ $material->grade->academicYear->name }}</td>
                                <td>
                                    @if ($material->main_book_path)
                                        <div class="mb-3">
                                            <a href="{{ asset('storage/' . $material->main_book_path) }}" target="_blank" class="btn btn-outline-primary">{{ __('messages.view')}}</a>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($material->activity_book_path)
                                        <div class="">
                                            <a href="{{ asset('storage/' . $material->activity_book_path) }}" target="_blank" class="btn btn-outline-primary">{{ __('messages.view')}}</a>
                                        </div>
                                    @endif
                                </td>
                                {{-- <td>{{ $material->materialUserTermSections->user->name }}</td> --}}
                                <td>
                                    @if ($material->book_path)
                                        <a href="{{ asset('storage/' . $material->book_path) }}" target="_blank" class="btn btn-sm btn-success">
                                            تحميل الكتاب
                                        </a>
                                    @endif

                                    <a href="{{ route('materials.edit', $material->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> {{ __('messages.edit') }}</a>
                                    <form action="{{ route('materials.destroy', $material->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.confirm_delete') }}')"><i class="fa fa-trash"></i> {{ __('messages.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center">{{ __('messages.no_materials') }}</p>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            let table = $('#materialsTable').DataTable({
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

            // فلترة الأعمدة
            $('#nameSearch').on('keyup', function () {
                table.column(1).search(this.value).draw();
            });
            $('#schoolSearch').on('keyup', function () {
                table.column(2).search(this.value).draw();
            });
            $('#gradeSearch').on('keyup', function () {
                table.column(3).search(this.value).draw();
            });
            $('#academicYearSearch').on('keyup', function () {
                table.column(4).search(this.value).draw();
            });

            // $('#teacherSearch').on('keyup', function () {
            //     table.column(3).search(this.value).draw();
            // });
        });
    </script>
@endpush
