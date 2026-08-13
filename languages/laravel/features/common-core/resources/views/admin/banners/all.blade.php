@extends('admin.layouts.app')
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Banner </h3>
                    <div class="nk-block-des">
                        <p>List of all banners</p>
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
                                    <a href="{{ url('admin/banners/add') }}" class="btn btn-primary">
                                        <em class="icon ni ni-plus"></em>
                                        <span>Add Banner</span>
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
                                <th>Link URL</th>
                                <th>Status</th>
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
                ajax: "{{ route('admin.banners') }}",
                "order": [[0, "desc"]],
                responsive: !0,
                autoFill: !0,
                keys: !0,
                lengthMenu: [
                    [10, 100, 500, -1],
                    [10, 100, 500, "All"]
                ],
                "columns": [
                    {
                        "mData": "image",
                        "mRender": function(data, type, row) {
                             if (row.image) {
                                var imageUrl = '{{ asset('storage') }}' + '/' + row.image;
                                return '<img src="' + imageUrl + '" style="width: 110px; height: 60px; border-radius: 4px;">';
                            } else {
                                var imageUrl = '{{ asset('assets/admin/images/default-image.png') }}';
                                return '<img src="' + imageUrl + '" style="width: 60px; height: 60px;">';
                            }
                        }
                    },
                    {
                        "mData": "link_url",
                        "mRender": function(data) {
                            return data ? '<a href="'+data+'" target="_blank">'+data+'</a>' : '<span>NA</span>';
                        }
                    },
                    {
                        "mData": "is_active"
                    },
                    {
                        "mData": "action"
                    }
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
                            url: "{{ url('admin/banners/delete') }}/" + id,
                            type: "DELETE",
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

        function changeStatus(id, status) {
            if ($.trim(id)) {
                let statusText = status == 1 ? "activate" : "deactivate";
                Swal.fire({
                    title: 'Update Status?',
                    text: "Do you want to " + statusText + " this?",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, change it!'
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                            url: "{{ url('admin/banners/change-status') }}",
                            type: "POST",
                            data: { 'id': id, 'status': status },
                            dataType: "JSON",
                            success: function(response) {
                                if (response.status == 'success') {
                                    Swal.fire("Updated", response.message, "success");
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
    </script>
@endsection
