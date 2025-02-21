@extends('backend.app')

@section('title', 'Todos')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            {{-- Page Title --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('todos.index') }}">Todos</a></li>
                                <li class="breadcrumb-item active">List</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            {{-- End Page Title --}}

            {{-- DataTable --}}
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">All Todos</h5>
                            <a href="{{ route('todos.create') }}" class="btn btn-primary btn-sm" id="addNewPage">Add
                                Todo</a>
                        </div>
                        <div class="card-body">
                            <table id="datatable"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="column-id">#</th>
                                        <th class="column-title">Title</th>
                                        <th class="column-email">Email</th>
                                        <th class="column-due-date">Due Date</th>
                                        <th class="column-status">Reminder Sent</th>
                                        <th class="column-status">Status</th>
                                        <th class="column-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal for viewing todo details --}}
    <div class="modal fade" id="viewTodoModal" tabindex="-1" aria-labelledby="TodoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="TodoModalLabel" class="modal-title">Todo Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            if (!$.fn.DataTable.isDataTable('#datatable')) {
                var table = $('#datatable').DataTable({
                    responsive: true,
                    order: [],
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"],
                    ],
                    processing: true,
                    serverSide: true,
                    pagingType: "full_numbers",
                    ajax: {
                        url: "{{ route('todos.index') }}",
                        type: "GET",
                    },
                    dom: "<'row table-topbar'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>>" +
                        "<'row'<'col-12'tr>>" +
                        "<'row table-bottom'<'col-md-5 dataTables_left'i><'col-md-7'p>>",
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search records...",
                        lengthMenu: "Show _MENU_ entries",
                        processing: `
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>`,
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'title',
                            name: 'title',
                            orderable: true,
                            searchable: true
                        },
                        {
                            data: 'email',
                            name: 'email',
                            orderable: true,
                            searchable: true
                        },
                        {
                            data: 'due_date',
                            name: 'due_date',
                            orderable: true,
                            searchable: true
                        },
                        {
                            data: 'reminder_email_sent',
                            name: 'reminder_email_sent',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                    ],
                });

                $('#datatable').on('draw.dt', function() {
                    $('td.column-action').each(function() {
                        let buttonCount = $(this).find('button').length;
                        let width = 5 + (buttonCount - 1) * 5;
                        $(this).css('width', width + '%');
                    });
                });

                dTable.buttons().container().appendTo('#file_exports');
                new DataTable('#example', {
                    responsive: true
                });
            }
        });

        // Fetch and display todo details
        function showTodoDetails(id) {
            let url = '{{ route('todos.show', ':id') }}';
            url = url.replace(':id', id);

            axios.get(url)
                .then(function(response) {
                    if (response.data.status) {
                        let data = response.data.data;
                        let modalBody = document.querySelector('#viewTodoModal .modal-body');

                        modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Title</label>
                                <p>${data.title}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <p>${data.description}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <p>${data.email}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Due Date</label>
                                <p>${data.due_date}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Reminder Email Sent</label>
                                <p>${data.reminder_email_sent ? 'Yes' : 'No'}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <p>${data.status}</p>
                            </div>
                        </div>
                    </div>
                `;
                    } else {
                        toastr.error('Failed to load todo details.');
                    }
                })
                .catch(function(error) {
                    console.error(error);
                    toastr.error('Could not fetch todo details.');
                });
        }

        // Status Change Confirm Alert
        function showStatusChangeAlert(id) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to update the status?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    statusChange(id);
                }
            });
        }

        // Status Change using Axios
        function statusChange(id) {
            let url = '{{ route('todos.status', ':id') }}'.replace(':id', id);

            axios.get(url)
                .then(function(response) {
                    let resp = response.data;
                    console.log('Response:', resp);

                    $('#datatable').DataTable().ajax.reload();

                    if (resp.data && resp.data.action === 'published') {
                        toastr.success(resp.message);
                    } else if (resp.data && resp.data.action === 'unpublished') {
                        toastr.error(resp.message);
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error.response);
                    let errMsg = error.response && error.response.data && error.response.data.errors ?
                        error.response.data.errors :
                        'An error occurred. Please try again.';
                    toastr.error(errMsg);
                });
        }

        // Delete Confirm Alert
        function showDeleteConfirm(id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure you want to delete this record?',
                text: 'If you delete this, it will be gone forever.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteItem(id);
                }
            });
        }

        // Delete function using Axios
        function deleteItem(id) {
            let url = '{{ route('todos.destroy', ':id') }}'.replace(':id', id);
            let csrfToken = '{{ csrf_token() }}';

            axios.delete(url, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(function(response) {
                    let resp = response.data;
                    $('#datatable').DataTable().ajax.reload();
                    if (resp.status) {
                        toastr.success(resp.message);
                    } else {
                        toastr.error(resp.message);
                    }
                })
                .catch(function(error) {
                    console.error('Error deleting todo:', error.response);
                    let errMsg = 'An error occurred. Please try again.';
                    if (error.response && error.response.data && error.response.data.errors) {
                        errMsg = error.response.data.errors;
                    }
                    toastr.error(errMsg);
                });
        }
    </script>
@endpush
