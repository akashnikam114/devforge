@extends('admin.layouts.app')
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Push Notification</h3>
                    <div class="nk-block-des">
                        <p>List of all notifications</p>
                    </div>
                </div>
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                            <em class="icon ni ni-menu-alt-r"></em>
                        </a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    <a href="{{ url('admin/push_notifications/add') }}" class="btn btn-primary">
                                        <em class="icon ni ni-plus"></em>
                                        <span>Add Notification</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="nk-block nk-block-lg mt-3">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            datatable1();
        });

        function datatable1() {
            NioApp.DataTable('#myTable', {
                "processing": true,
                "serverSide": true,
                "searching": true,
                "bLengthChange": true,
                "drawCallback": function(settings) {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                },
                ajax: "{{ route('admin.notification') }}",
                "order": [[1, "asc"]],
                responsive: !0,
                autoFill: !0,
                keys: !0,
                lengthMenu: [[10, 100, 500, -1], [10, 100, 500, "All"]],
                "columns": [
                    {
                        "mData": "image",
                        "mRender": function(data, type, row) {
                             if (row.image) {
                                var imageUrl = '{{ asset('storage') }}' + '/' + row.image;
                                return '<img src="' + imageUrl + '" style="width:60px; height: 40px; border-radius: 4px;">';
                            } else {
                                var imageUrl = '{{ asset('assets/admin/img/default-image.jpeg') }}';
                                return '<img src="' + imageUrl + '" style="width: 60px; height: 60px;">';
                            }
                        }
                    },
                    { "mData": "title" },
                    {
                        "mData": "description",
                        "mRender": function(data) {
                            if (!data) return '<span>NA</span>';
                            let truncated = data.length > 30 ? data.substr(0, 30) + '...' : data;
                            let escaped = data.replace(/"/g, '&quot;');
                            return `<span data-bs-toggle="tooltip" title="${escaped}" style="cursor:help;">${truncated}</span>`;
                        }
                    },
                    { "mData": "action" }
                ]
            });
        }

        function deleteRecord(id) {
            if ($.trim(id)) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                            url: "{{ url('admin/push_notifications/delete') }}/" + id,
                            type: "GET",
                            dataType: "JSON",
                            success: function(response) {
                                if (response.status == 'success') {
                                    Swal.fire("Deleted", response.message, "success");
                                    $("#myTable").DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire("Error!", response.message, "error");
                                }
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                Swal.fire("Error!", "Something went wrong", "error");
                            }
                        });
                    }
                });
            }
        }

        function sendNotification(id) {
            if ($.trim(id)) {
                Swal.fire({
                    title: 'Send Notification?',
                    text: "This will send a push notification to all users.",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, send it!',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                            url: "{{ url('admin/push_notifications/send') }}/" + id,
                            type: "POST",
                            dataType: "JSON",
                        }).done(function(response) {
                            return response;
                        }).fail(function(xhr, ajaxOptions, thrownError) {
                            Swal.showValidationMessage(`Request failed: Something went wrong`);
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (result.value.status == 'success') {
                            Swal.fire("Sent!", result.value.message, "success");
                        } else {
                            Swal.fire("Error!", result.value.message, "error");
                        }
                    }
                });
            }
        }
    </script>
@endsection
