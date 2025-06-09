@extends('layouts.table-layout2')

@section('title', __('quran_levels.levels_by_school'))

@section('content')
    <div class="">
        <div class="">
            <!-- Page Header -->
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">
                        {{ __('quran_levels.levels_by_school') }}
                        <span class="text-muted fs-6">({{ __('messages.school') }}: {{ $schoolName }})</span>
                    </h3>
                </div>
                <div class="col-auto text-end float-end ms-auto">
                    <a href="{{ route('quran-levels.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('messages.create_quran_level') }}
                    </a>
                </div>
            </div>

            <!-- Alert when no levels -->
            @if($quranLevels->isEmpty())
                <div class="alert alert-warning text-center mt-3">
                    {{ __('quran_levels.no_levels_found') }}
                </div>
            @else
                <!-- Table -->
                <div class="card card-table mt-4">
                    <div class="card-body">
                        <table class="table table-bordered table-hover table-center mb-0" id="quranLevelsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('quran_levels.name') }}</th>
                                    <th>{{ __('quran_levels.academic_year') }}</th>
                                    <th>{{ __('quran_levels.level_order') }}</th>
                                    <th>{{ __('quran_levels.description') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}" id="nameSearch"></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}" id="yearSearch"></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}" id="orderSearch"></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}" id="descSearch"></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quranLevels as $level)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $level->name }}</td>
                                        <td>{{ $level->academicYear->name ?? '-' }}</td>
                                        <td>{{ $level->level_order }}</td>
                                        <td>{{ $level->description ?? __('messages.no_description') }}</td>
                                        <td class="text-end">
                                            <div class="actions">
                                                <a href="{{ route('quran-levels.show', $level->id) }}" class="btn btn-sm bg-info-light me-2">
                                                    <i class="feather-eye"></i> {{ __('messages.view_quran_classes') }}
                                                </a>
                                                <a href="{{ route('quran-levels.edit', $level->id) }}" class="btn btn-sm bg-warning-light me-2">
                                                    <i class="feather-edit"></i> {{ __('messages.edit') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Back Button -->
            <div class="mt-4">
                <a href="{{ route('admin.schools.index') }}" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            let table = $('#quranLevelsTable').DataTable({
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

            // فلترة الأعمدة
            $('#nameSearch').on('keyup', function () {
                table.column(1).search(this.value).draw();
            });
            $('#yearSearch').on('keyup', function () {
                table.column(2).search(this.value).draw();
            });
            $('#orderSearch').on('keyup', function () {
                table.column(3).search(this.value).draw();
            });
            $('#descSearch').on('keyup', function () {
                table.column(4).search(this.value).draw();
            });
        });
    </script>
@endpush
